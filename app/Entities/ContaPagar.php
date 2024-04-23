<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use PhpParser\Node\Stmt\Return_;

class ContaPagar extends Entity
{
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];

    public function exibeSituacao(): string
    {
        if ($this->situacao == 1) {
            return '<i class="fa fa-check-circle text-success">
                    </i>&nbsp;Conta foi paga em ' .
                    date('d/m/Y', strtotime($this->updated_at));
        }

        if ($this->data_vencimento == date('Y-m-d')) {
            return '<i class="fa fa-info-circle text-warning">
                    </i>&nbsp;Conta vencerá hoje ' .
                    date('d/m/Y', strtotime($this->data_vencimento));
        }

        if ($this->data_vencimento > date('Y-m-d')) {
            return '<i class="fa fa-info-circle text-info">
                    </i>&nbsp;Conta vencerá em ' .
                date('d/m/Y', strtotime($this->data_vencimento));
        }

        if ($this->data_vencimento < date('Y-m-d') && $this->situacao == 0) {
            return '<i class="fa fa-exclamation-circle text-danger">
                    </i>&nbsp;Conta venceu em ' .
                date('d/m/Y', strtotime($this->data_vencimento));
        }

        return '<i class="fa fa-exclamation-circle text-danger">
                    </i>&nbsp;Não encontramos os dados da sua conta';
    }

    public function defineDataVencimentoEvento(): int
    {
        $dataAtualConvertida = $this->mutateDate(date('Y-m-d'));

        return $dataAtualConvertida->difference($this->data_vencimento)->getDays();
    }
}
