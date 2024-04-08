<?php

namespace App\Models;

use App\Libraries\Token;
use CodeIgniter\Model;
use PhpParser\Node\Stmt\Unset_;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $returnType       = 'App\Entities\Usuario';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'nome',
        'email',
        'password',
        'reset_hash',
        'reset_expira_em',
        'imagem',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules    = [
        'id'           => 'permit_empty|is_natural_no_zero',
        'nome'         => 'required|min_length[3]|max_length[125]',
        'email'        => 'required|valid_email|max_length[230]|is_unique[usuarios.email,id,{id}]',
        'password'     => 'required|min_length[6]',
        'password_confirmation' => 'required_with[password]|matches[password]'
    ];
    protected $validationMessages   = [
        'nome'         => [
            'required'   => 'O campo Nome é obrigatório.',
            'min_length' => 'O campo Nome precisa ter pelo menos 3 caractéres.',
            'max_length' => 'O campo Nome não pode ser maior que 125 caractéres.',
        ],
        'email' => [
            'required'      => 'O campo E-mail é obrigatório.',
            'max_length'    => 'O campo E-mail não pode ser maior que 230 caractéres.',
            'valid_email'   => 'O campo E-mail precisa conter um e-mail valido.',
            'is_unique'     => 'O e-mail informado já está sendo utilizado.',
        ],
        'password' => [
            'required'      => 'O campo Senha é obrigatório.',
            'min_length'    => 'O campo Senha precisa ter pelo menos 6 caractéres.',
        ],
        'password_confirmation' => [
            'required_with' => 'Por favor confirme a sua senha.',
            'matches'       => 'As senhas não são iguais',
        ],
    ];

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])){
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
        };

        return $data;
    }

    public function buscaUsuarioPorEmail(string $email)
    {
        return $this->where('email', $email)->where('deleted_at', null)->first();
    }

    public function recuperaPermissoesDoUsuarioLogado(int $usuario_id)
    {
        $atributos = [
            'usuarios.id',
            'usuarios.nome AS usuario',
            'grupos_usuarios.*',
            'permissoes.nome AS permissao'
        ];

        return $this->select($atributos)
                ->asArray()
                ->join('grupos_usuarios', 'grupos_usuarios.usuario_id = usuarios.id')
                ->join('grupos_permissoes', 'grupos_permissoes.grupo_id = grupos_usuarios.grupo_id')
                ->join('permissoes', 'permissoes.id = grupos_permissoes.permissao_id')
                ->where('usuarios.id', $usuario_id)
                ->groupBy('permissoes.nome')
                ->findAll();
    }

    public function buscaUsuarioPorToken(string $token)
    {
        $token = new Token($token);
        $tokenHash = $token->getHash();

        $usuario = $this->where('reset_hash', $tokenHash)
            ->where('deleted_at', null)
            ->first();

        if ($usuario === null) {
            return null;
        }

        if ($usuario->reset_expira_em < date('Y-m-d H:i:s')) {
            return null;
        }

        return $usuario;
    }

}
