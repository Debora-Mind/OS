<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Fornecedor extends Entity
{
    protected $datamap = [];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [];

    public function formatarCNPJ()
    {
        $cnpj = $this->cnpj;
        // Remove qualquer caractere não numérico
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Adiciona os pontos, barras e traços
        $cnpj_formatado = substr($cnpj, 0, 2) . '.';
        $cnpj_formatado .= substr($cnpj, 2, 3) . '.';
        $cnpj_formatado .= substr($cnpj, 5, 3) . '/';
        $cnpj_formatado .= substr($cnpj, 8, 4) . '-';
        $cnpj_formatado .= substr($cnpj, 12, 2);

        return $cnpj_formatado;
    }

    function formatarTelefone()
    {

        $telefone = $this->telefone;
        // Remove qualquer caractere não numérico
        $telefone = preg_replace('/[^0-9]/', '', $telefone);

        // Adiciona os parênteses, espaços e traços
        $telefone_formatado = '(' . substr($telefone, 0, 2) . ') ';
        $telefone_formatado .= substr($telefone, 2, 4) . '-';
        $telefone_formatado .= substr($telefone, 6);

        return $telefone_formatado;
    }

    public function removeFormatacao()
    {
        $this->cnpj = str_replace(['.', '-', '/'], '', $this->cnpj);
        $this->telefone = str_replace(['-', '(', ')'], '', $this->telefone);
        $this->cep = str_replace('-', '', $this->cep);
    }

    public function exibeSituacao()
    {
        if ($this->deleted_at != null) {
            $icone = '<span class="text-white">Excluído</span>&nbsp;<i class="fa fa-undo"></i>&nbsp;Desfazer';

            $situacao = anchor("fornecedores/restaurar/$this->id", $icone, ['class' => 'btn btn-sm']);

            return $situacao;
        } elseif ($this->ativo) {
            return '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo';
        } else {
            return '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo';
        }
    }

}
