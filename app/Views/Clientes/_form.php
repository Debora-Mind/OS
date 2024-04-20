<div class="row">
    <div class="form-group col-md-12">
        <label class="form-control-label">Nome completo</label>
        <input type="text" name="nome" placeholder="Insira o Nome Completo" class="form-control"
               value="<?= esc($cliente->nome) ?>">
    </div>
    <div class="form-group col-md-3">
        <label class="form-control-label">CPF</label>
        <input type="text" name="cpf" placeholder="Insira o CPF" class="form-control cpf"
               value="<?= esc($cliente->cpf) ?>">
    </div>
    <div class="form-group col-md-3">
        <label class="form-control-label">Telefone</label>
        <input type="text" name="telefone" placeholder="Insira o número de telefone" class="form-control sp_celphones"
               value="<?= esc($cliente->telefone) ?>">
    </div>
    <div class="form-group col-md-6">
        <label class="form-control-label">E-mail(para acesso ao sistema)</label>
        <input type="text" name="email" placeholder="Insira o e-mail" class="form-control"
               value="<?= esc($cliente->email) ?>">
        <div id="email"></div>
    </div>
    <div class="form-group col-md-3">
        <label class="form-control-label">CEP</label>
        <input type="text" name="cep" placeholder="Insira o CEP" class="form-control cep"
               value="<?= esc($cliente->cep) ?>">
        <div id="cep"></div>
    </div>
    <div class="form-group col-md-7">
        <label class="form-control-label">Endereço</label>
        <input type="text" name="endereco" placeholder="Insira o endereço" class="form-control"
               value="<?= esc($cliente->endereco) ?>" readonly>
    </div>
    <div class="form-group col-md-2">
        <label class="form-control-label">Número</label>
        <input type="text" name="numero" placeholder="Número" class="form-control"
               value="<?= esc($cliente->numero) ?>">
    </div>
    <div class="form-group col-md-5">
        <label class="form-control-label">Bairro</label>
        <input type="text" name="bairro" placeholder="Insira o bairro" class="form-control"
               value="<?= esc($cliente->bairro) ?>" readonly>
    </div>
    <div class="form-group col-md-5">
        <label class="form-control-label">Cidade</label>
        <input type="text" name="cidade" placeholder="Insira a Cidade" class="form-control"
               value="<?= esc($cliente->cidade) ?>" readonly>
    </div>
    <div class="form-group col-md-2">
        <label class="form-control-label">Estado</label>
        <input type="text" name="estado" placeholder="Insira o UF" class="form-control"
               value="<?= esc($cliente->estado) ?>" readonly>
    </div>
</div>
