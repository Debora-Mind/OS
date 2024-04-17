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

    public function formataValorParaDB()
    {
        $this->preco_venda = esc(str_replace('.', '', $this->preco_venda));
        $this->preco_venda = esc(str_replace(',', '.', $this->preco_venda));

        $this->preco_custo = esc(str_replace('.', '', $this->preco_custo));
        $this->preco_custo = esc(str_replace(',', '.', $this->preco_custo));
    }

    public function removeCamposServico()
    {
        unset($this->marca);
        unset($this->modelo);
        unset($this->preco_custo);
        unset($this->estoque);
        unset($this->controla_estoque);
    }

    public function recuperaAtributosAlterados(): string
    {
        $atributosAlterados = [];

        if ($this->hasChanged('nome')) {
            $atributosAlterados['nome'] = "O nome foi alterado para $this->nome";
        }
        if ($this->hasChanged('marca')) {
            $atributosAlterados['marca'] = "A marca foi alterada para $this->marca";
        }
        if ($this->hasChanged('modelo')) {
            $atributosAlterados['modelo'] = "O modelo foi alterado para $this->modelo";
        }
        if ($this->hasChanged('preco_custo')) {
            $atributosAlterados['preco_custo'] = "O preço de custo foi alterado para " . $this->precoCustoFormatado();
        }
        if ($this->hasChanged('preco_venda')) {
            $atributosAlterados['preco_venda'] = "O preço de venda foi alterado para " . $this->precoVendaFormatado();
        }
        if ($this->hasChanged('estoque')) {
            $atributosAlterados['estoque'] = "O Estoque foi alterado para $this->estoque";
        }
        if ($this->hasChanged('descricao')) {
            $atributosAlterados['descricao'] = "A descrição foi alterada para $this->descricao";
        }
        if ($this->hasChanged('controla_estoque')) {
            if ($this->controle_estoque == 1) {
                $atributosAlterados['controla_estoque'] = "O controle de estoque foi ativado";
            }
            else {
                $atributosAlterados['controla_estoque'] = "O controle de estoque foi inativado";
            }
        }
        if ($this->hasChanged('ativo')) {
            if ($this->ativo == 1) {
                $atributosAlterados['ativo'] = "O item foi ativado";
            }
            else {
                $atributosAlterados['ativo'] = "O item foi inativado";
            }
        }

        return serialize($atributosAlterados);
    }
}
