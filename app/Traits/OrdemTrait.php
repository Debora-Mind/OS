<?php

namespace App\Traits;

use App\Models\OrdemItemModel;

trait OrdemTrait
{
    public function preparaItensDaOrdem(object $ordem): object
    {
        $ordemItemModel = new OrdemItemModel();

        if ($ordem->situacao === 'aberta'){
            $ordemItens = $ordemItemModel->recuperaItensDaOrdem($ordem->id);

            $ordem->itens = ($ordemItens !== null ? $ordemItens : null);

            return $ordem;
        }

        if ($ordem->itens !== null) {
            $ordem->itens = unserialize($ordem->itens);
        }

        return $ordem;
    }
}