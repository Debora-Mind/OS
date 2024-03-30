<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Usuario extends Entity
{
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];

    public function exibeSituacao()
    {
        if ($this->deleted_at != null) {
            $icone = '<span class="text-white">Excluído</span>&nbsp;<i class="fa fa-undo"></i>&nbsp;Desfazer';

            $situacao = anchor("usuarios/restaurarusuario/$this->id", $icone, ['class' => 'btn btn-sm']);

            return $situacao;
        }
        elseif ($this->ativo == true) {
            return '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo';
        }
        else {
            return '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo' ;
        }
    }
}
