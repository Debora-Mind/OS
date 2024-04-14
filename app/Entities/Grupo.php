<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Grupo extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function exibeSituacao()
    {
        if ($this->deleted_at != null) {
            $icone = '<span class="text-white">Excluído</span>&nbsp;<i class="fa fa-undo"></i>&nbsp;Desfazer';

            $situacao = anchor("grupos/restaurar/$this->id", $icone, ['class' => 'btn btn-sm']);

            return $situacao;
        } elseif ($this->tecnico) {
            return '<i class="fa fa-eye text-secondary"></i>&nbsp;Técnico';
        } else {
            return '<i class="fa fa-eye-slash text-danger"></i>&nbsp;Não técnico';
        }
    }
}
