<div class="form-group">
    <label class="form-control-label" for="nome">Nome</label>
    <input type="text" name="nome" placeholder="Insira o nome da forma de pagamento" class="form-control"
           value="<?= esc($forma->nome) ?>">
</div>
<div class="form-group">
    <label class="form-control-label" for="descricao">Descrição</label>
    <textarea type="text" name="descricao" placeholder="Insira a descrição da forma de pagamento"
              class="form-control"><?= esc($forma->descricao) ?></textarea>
</div>
<div class="custom-control custom-checkbox">
    <input type="hidden" name="ativo" value="0">
    <input type="checkbox" class="custom-control-input" value="1" name="ativo"
           id="ativo" <?= $forma->ativo == true ? 'checked' : '' ?>>
    <label for="ativo" class="custom-control-label">Ativa </label>

</div>
