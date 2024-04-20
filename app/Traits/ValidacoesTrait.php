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

    public function checkEmail(string $email, bool $bypass = false): array
    {
        session()->remove('blockEmail');
        $retorno = [];
        if ($bypass == true) {
            return $retorno;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://mailcheck.p.rapidapi.com/?domain={$email}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: mailcheck.p.rapidapi.com",
                "x-rapidapi-key: " . getenv('CHAVE_CHECK_MAIL_API'),
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $resposta = curl_exec($curl);
        $erro = curl_error($curl);

        curl_close($curl);

        if ($erro) {
            $resposta['erro'] = "cURL Error #:" . $erro;
            return $retorno;
        }

        $consulta = json_decode($resposta);

        if ($consulta->block == true) {
            session()->set('blockEmail', esc($consulta->block));
            $retorno['erro'] = '<span class="text-danger">O e-mail é inválido</span>';
            return $retorno;
        }

        return $retorno;
    }

}