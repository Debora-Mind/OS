<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Cliente extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];

    public function exibeSituacao()
    {
        if ($this->deleted_at != null) {
            $icone = '<span class="text-white">Excluído</span>&nbsp;<i class="fa fa-undo"></i>&nbsp;Desfazer';

            $situacao = anchor("clientes/restaurar/$this->id", $icone, ['class' => 'btn btn-sm']);

            return $situacao;
        }

        return '<span class="text-success"><i class="fa fa-thumbs-up"></i>&nbsp;Disponível</span>';
    }

    public function formatarCPF()
    {
        $cpf = $this->cpf;
        // Remove qualquer caractere não numérico
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Adiciona os pontos e o traço
        $cpf_formatado = substr($cpf, 0, 3) . '.';
        $cpf_formatado .= substr($cpf, 3, 3) . '.';
        $cpf_formatado .= substr($cpf, 6, 3) . '-';
        $cpf_formatado .= substr($cpf, 9, 2);

        return $cpf_formatado;
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

    public function formatarCep()
    {
        $cep = $this->cep;
        // Remove qualquer caractere não numérico
        $cep = preg_replace('/[^0-9]/', '', $cep);

        // Adiciona os pontos e o traço
        $cep_formatado = substr($cep, 0, 5) . '-';
        $cep_formatado .= substr($cep, 5, 8);

        return $cep_formatado;
    }

    public function removeFormatacao()
    {
        $this->cpf = str_replace(['.', '-'], '', $this->cpf);
        $this->telefone = str_replace(['-', '(', ')', ' '], '', $this->telefone);
        $this->cep = str_replace('-', '', $this->cep);
    }
}
