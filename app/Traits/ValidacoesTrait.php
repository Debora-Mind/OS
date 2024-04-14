<?php

namespace App\Traits;

trait ValidacoesTrait
{
    public function consultaViaCep(string $cep): array
    {
        $cep = str_replace('-', '', $cep);

        $url = "https://viacep.com.br/ws/$cep/json/";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Adicione estas opções para ignorar erros de certificado SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $resposta = curl_exec($ch);

        $erro = curl_error($ch);

        $retorno = [];
        if ($erro) {
            $retorno['erro'] = $erro;

            return $retorno;
        }

        $consulta = json_decode($resposta);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $retorno['erro'] = '<span class="text-danger">Erro ao processar a resposta do ViaCEP.</span>';
            return $retorno;
        }

        if (isset($consulta->erro) && !isset($consulta->cep)) {
            session()->set('blockCep', true);

            $retorno['erro'] = '<span class="text-danger">Informe um CEP válido</span>';

            return $retorno;
        }

        session()->set('blockCep', false);

        $retorno['endereco'] = esc($consulta->logradouro ?? '');
        $retorno['bairro'] = esc($consulta->bairro ?? '');
        $retorno['cidade'] = esc($consulta->localidade ?? '');
        $retorno['estado'] = esc($consulta->uf ?? '');

        return $retorno;
    }
}