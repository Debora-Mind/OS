<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoPermissaoModel extends Model
{
    protected $table            = 'grupos_permissoes';
    protected $returnType       = 'array';
    protected $allowedFields    = ['grupo_id', 'permissao_id'];

    public function recuperaPermissoesDoGrupo(int $grupoId, int $quantidadePaginacao)
    {
        $atributos = [
            'grupos_permissoes.id',
            'grupos.id AS grupo_id',
            'permissoes.id AS permissao_id',
            'permissoes.nome',
        ];

        return $this->select($atributos)
            ->join('grupos', 'grupos.id = grupos_permissoes.grupo_id')
            ->join('permissoes', 'permissoes.id = grupos_permissoes.permissao_id')
            ->where('grupos_permissoes.grupo_id', $grupoId)
            ->groupBy('permissoes.nome')
            ->paginate($quantidadePaginacao);
    }
}
