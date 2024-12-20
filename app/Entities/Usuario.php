<?php

namespace App\Entities;

use App\Libraries\Token;
use CodeIgniter\Entity\Entity;

class Usuario extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function exibeSituacao()
    {
        if ($this->deleted_at != null) {
            $icone = '<span class="text-white">Excluído</span>&nbsp;<i class="fa fa-undo"></i>&nbsp;Desfazer';

            $situacao = anchor("usuarios/restaurar/$this->id", $icone, ['class' => 'btn btn-sm']);

            return $situacao;
        } elseif ($this->ativo) {
            return '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo';
        } else {
            return '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo';
        }
    }

    public function verificaPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    public function temPermissaoPara(string $permissao): bool
    {
        if ($this->is_admin == true) {
            return true;
        }

        if (empty($this->permissoes)) {
            return false;
        }

        if (in_array($permissao, $this->permissoes) == false) {
            return false;
        }

        return true;
    }

    public function iniciaPasswordReset(): void
    {
        $token = new Token();

        $this->reset_token = $token->getValue();
        $this->reset_hash = $token->getHash();
        //Expira em 2h
        $this->reset_expira_em = date('Y-m-d H:i:s', time() + 7200);
    }

    public function finalizaPasswordReset(): void
    {
        $this->reset_hash = null;
        $this->reset_expira_em = null;
    }
}
