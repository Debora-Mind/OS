<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class FormaPagamento extends Entity
{
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];

    public function exibeSituacao()
    {
        if ($this->ativo) {
            return '<i class="fa fa-unlock text-success"></i>&nbsp;Ativa';
        } else {
            return '<i class="fa fa-lock text-warning"></i>&nbsp;Inativa';
        }
    }
}
