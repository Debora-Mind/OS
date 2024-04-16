<div class="row">

    <?php if ($item->id === null): ?>
    <div class="col md-4 mb-4">
        <label for="">Este é um item do tipo:</label>
        <div class="custom-control custom-radio mb-2">
            <input type="radio" class="custom-control-input" id="produto" name="tipo" value="produto" checked>
            <label for="produto" class="custom-control-label">
                <i class="fa fa-archive text-success" aria-hidden="true"></i>&nbsp;Novo produto</label>
        </div>
        <div class="custom-control custom-radio">
            <input type="radio" class="custom-control-input" id="servico" name="tipo" value="serviço">
            <label for="servico" class="custom-control-label">
                <i class="fa fa-wrench text-white" aria-hidden="true"></i>&nbsp;Novo serviço</label>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-group col-md-12">
        <label class="form-control-label">Nome</label>
        <input type="text" name="nome" placeholder="Insira o nome do item" class="form-control"
               value="<?= esc($item->nome) ?>">
    </div>
    <div class="produto form-group col-md-4">
        <label class="form-control-label">Marca</label>
        <input type="text" name="marca" placeholder="Insira a marca do item" class="form-control"
               value="<?= esc($item->marca) ?>">
    </div>
    <div class="produto form-group col-md-4">
        <label class="form-control-label">Modelo</label>
        <input type="text" name="modelo" placeholder="Insira o Modelo do item" class="form-control"
               value="<?= esc($item->modelo) ?>">
    </div>
    <div class="produto form-group col-md-4">
        <label class="form-control-label">Estoque</label>
        <input type="number" name="estoque" placeholder="Insira a quantidade em estoque" class="form-control"
               value="<?= esc($item->estoque) ?>">
    </div>
</div>
<div class="row">
    <div class="produto form-group col-md-4">
        <label class="form-control-label">Preço de custo</label>
        <input type="text" name="preco_custo" placeholder="Valor de custo" class="form-control money"
               value="<?= esc($item->preco_custo) ?>">
    </div>
    <div class="form-group col-md-4">
        <label class="form-control-label">Preço de venda</label>
        <input type="text" name="preco_venda" placeholder="Valor de venda" class="form-control money"
               value="<?= esc($item->preco_venda) ?>">
    </div>
    <div class="form-group col-md-12">
        <label class="form-control-label">Descrição</label>
        <textarea name="descricao" placeholder="Insira a descrição" class="form-control" rows="5"
        ><?= esc($item->descricao) ?></textarea>
    </div>

</div>
<div class="produto custom-control custom-checkbox">
    <input type="hidden" name="controla_estoque" value="0">
    <input type="checkbox" class="custom-control-input" value="1" name="controla_estoque"
           id="controla_estoque" <?= $item->controla_estoque == true ? 'checked' : '' ?>>
    <label for="controla_estoque" class="custom-control-label">Controla estoque</label>
</div>

<div class="custom-control custom-checkbox">
    <input type="hidden" name="ativo" value="0">
    <input type="checkbox" class="custom-control-input" value="1" name="ativo"
           id="ativo" <?= $item->ativo == true ? 'checked' : '' ?>>
    <label for="ativo" class="custom-control-label">Fornecedor ativo</label>
</div>
