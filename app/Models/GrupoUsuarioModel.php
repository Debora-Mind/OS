<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoUsuarioModel extends Model
{
    protected $table = 'grupos_usuarios';
    protected $returnType = 'object';
    protected $allowedFields = ['grupo_id', 'usuario_id'];

    public function recuperaGruposDoUsuario(int $usuario_id, int $quantidadePaginacao)
    {
        $atributos = [
            'grupos_usuarios.id AS principal_id',
            'grupos.id AS grupo_id',
            'grupos.nome',
            'grupos.descricao'
        ];

        return $this->select($atributos)
            ->join('grupos', 'grupos.id = grupos_usuarios.grupo_id')
            ->join('usuarios', 'usuarios.id = grupos_usuarios.usuario_id')
            ->where('grupos_usuarios.usuario_id', $usuario_id)
            ->groupBy('grupos.nome')
            ->paginate($quantidadePaginacao);
    }

    public function usuarioEstaNoGrupo(int $grupoId, int $usuarioId)
    {
        return $this->where('grupo_id', $grupoId)
            ->where('usuario_id', $usuarioId)
            ->first();
    }

    public function recuperaGrupos()
    {
        $atributos = [
            'grupos_usuarios.usuario_id',
            'grupos.id AS grupo_id',
            'grupos.nome',
        ];

        return $this->select($atributos)
            ->asArray()
            ->join('grupos', 'grupos.id = grupos_usuarios.grupo_id')
            ->join('usuarios', 'usuarios.id = grupos_usuarios.usuario_id')
            ->where('grupos.deleted_at', null)
            ->findAll();
    }
}
