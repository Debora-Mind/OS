<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Ordem extends Entity
{
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];

    public function exibeSituacao()
    {
        if ($this->deleted_at != null) {

            if (url_is('relatorios*')){
                return '<span>Excluída</span>';
            }

            $icone = '<span class="text-white">Excluída</span>&nbsp;<i class="fa fa-undo"></i>&nbsp;Desfazer';

            $situacao = anchor("ordens/restaurar/$this->codigo", $icone, ['class' => 'btn btn-sm']);

            return $situacao;
        } else {
            if($this->situacao == 'aberta') {
                return '<span class="text-warning"><i class="fa fa-unlock"></i>&nbsp;' . ucfirst($this->situacao) . '</span>';
            }
            elseif ($this->situacao == 'encerrada') {
                return '<span class="text-success"><i class="fa fa-lock"></i>&nbsp;' . ucfirst($this->situacao) . '</span>';
            }
            elseif ($this->situacao == 'aguardando') {
                return '<span class="text-info"><i class="fa fa-clock-o"></i>&nbsp;' . ucfirst($this->situacao) . '</span>';
            }
            elseif ($this->situacao == 'cancelada') {
                return '<span class="text-danger"><i class="fa fa-ban"></i>&nbsp;' . ucfirst($this->situacao) . '</span>';
            }
            elseif ($this->situacao == 'nao_pago') {
                return '<span class="text-danger"><i class="fa fa-clock-o"></i>&nbsp;Não pago</span>';
            }
        }

        return '<span class="text-danger"><i class="fa fa-sign-out"></i>&nbsp;Erro</span>';
    }
}
