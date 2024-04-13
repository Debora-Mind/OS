<div class="row">
    <div class="form-group col-md-12">
        <label class="form-control-label">Razão social</label>
        <input type="text" name="razao" placeholder="Insira o razão social" class="form-control" value="<?= esc($fornecedor->razao) ?>">
    </div>
    <div class="form-group col-md-4">
        <label class="form-control-label">CNPJ</label>
        <input type="text" name="cnpj" placeholder="Insira o CNPJ" class="form-control cnpj" value="<?= esc($fornecedor->cnpj) ?>">
    </div>
    <div class="form-group col-md-4">
        <label class="form-control-label">Inscrição Estadual</label>
        <input type="text" name="ie" placeholder="Insira a Inscrição Estadual" class="form-control" value="<?= esc($fornecedor->ie) ?>">
    </div>
    <div class="form-group col-md-4">
        <label class="form-control-label">Telefone</label>
        <input type="text" name="telefone" placeholder="Insira o número de telefone" class="form-control sp_celphones" value="<?= esc($fornecedor->telefone) ?>">
    </div>
    <div class="form-group col-md-4">
        <label class="form-control-label">CEP</label>
        <input type="text" name="cep" placeholder="Insira o CEP" class="form-control cep" value="<?= esc($fornecedor->cep) ?>">
        <div id="cep"></div>
    </div>
    <div class="form-group col-md-6">
        <label class="form-control-label">Endereço</label>
        <input type="text" name="endereco" placeholder="Insira o endereço" class="form-control" value="<?= esc($fornecedor->endereco) ?>" readonly>
    </div>
    <div class="form-group col-md-2">
        <label class="form-control-label">Número</label>
        <input type="text" name="numero" placeholder="Número" class="form-control" value="<?= esc($fornecedor->numero) ?>">
    </div>
    <div class="form-group col-md-5">
        <label class="form-control-label">Bairro</label>
        <input type="text" name="bairro" placeholder="Insira o bairro" class="form-control" value="<?= esc($fornecedor->bairro) ?>" readonly>
    </div>
    <div class="form-group col-md-5">
        <label class="form-control-label">Cidade</label>
        <input type="text" name="cidade" placeholder="Insira a Cidade" class="form-control" value="<?= esc($fornecedor->cidade) ?>" readonly>
    </div>
    <div class="form-group col-md-2">
        <label class="form-control-label">Estado</label>
        <input type="text" name="estado" placeholder="Insira o UF" class="form-control" value="<?= esc($fornecedor->estado) ?>" readonly>
    </div>
</div>
<div class="custom-control custom-checkbox">
    <input type="hidden" name="ativo" value="0">
    <input type="checkbox" class="custom-control-input" value="1" name="ativo" id="ativo" <?= $fornecedor->ativo == true ? 'checked' : '' ?>>
    <label for="ativo" class="custom-control-label">Fornecedor ativo</label>
</div>
