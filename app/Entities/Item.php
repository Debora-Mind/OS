<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Item extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function exibeSituacao()
    {
        if ($this->deleted_at !== null) {
            $icone = '<span class="text-white">Excluído</span>&nbsp;<i class="fa fa-undo"></i>';

            $situacao = anchor("itens/restaurar/$this->id", $icone, ['class' => 'btn btn-sm p-0']);

            return $situacao;
        } elseif ($this->ativo) {
            return '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo';
        } else {
            return '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo';
        }
    }

    public function exibeTipo()
    {
        if ($this->tipo === 'produto') {
            $tipoItem = '<i class="fa fa-archive text-success"></i>&nbsp;Produto';
        } else {
            $tipoItem = '<i class="fa fa-wrench text-white"></i>&nbsp;Serviço';
        }

        return $tipoItem;
    }

    public function exibeEstoque()
    {
        return $this->tipo === 'produto' ? $this->estoque : 'Não se aplica';
    }

    public function precoVendaFormatado()
    {
        return 'R$&nbsp;' . esc(str_replace('.', ',', $this->preco_venda));
    }

    public function precoCustoFormatado()
    {
        return 'R$&nbsp;' . esc(str_replace('.', ',', $this->preco_custo));
    }
}
