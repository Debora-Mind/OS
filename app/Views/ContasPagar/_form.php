<?php if ($conta->id === null): ?>

    <div class="form-group">
        <label for="fornecedor_id" class="form-control-label">Escolha o fornecedor</label>
        <a tabindex="0" role="button" data-toggle="popover"
           data-trigger="focus" style="text-decoration: none"
           data-content="Busque pela razão social ou pelo CNPJ. É preciso digitar pelo menos 3 caracteres para buscar">
            &nbsp;&nbsp;<i class="fa fa-question-circle text-info fa-lg"></i>
        </a>
        <select name="fornecedor_id[]" class="selectize" id="fornecedor_id" required>
            <option value="">Escolha...</option>
        </select>
    </div>

<?php else: ?>

    <div class="form-group">
        <label class="form-control-label">Fornecedor</label>
        <a tabindex="0" role="button" data-toggle="popover"
           data-trigger="focus" style="text-decoration: none"
           data-content="Não é permitido editar o fornecedor da conta">
            &nbsp;&nbsp;<i class="fa fa-question-circle text-info fa-lg"></i>
        </a>
        <input type="text" disabled readonly class="form-control"
               value="<?= esc($conta->razao) ?>">
    </div>

<?php endif; ?>


<div class="form-group">
    <label class="form-control-label">Valor da conta</label>
    <input type="text" name="valor_conta" placeholder="Insira o valor da conta" class="form-control money"
           value="<?= esc($conta->valor_conta) ?>">
</div>
<div class="form-group">
    <label class="form-control-label">Data de vencimento</label>
    <input type="date" name="data_vencimento" placeholder="Insira a data de vencimento" class="form-control date"
           value="<?= esc($conta->data_vencimento) ?>">
</div>
<div class="form-group">
    <label class="form-control-label">Descrição da conta</label>
    <textarea name="descricao_conta" placeholder="Insira a descrição da conta" class="form-control date"
    ><?= esc($conta->descricao_conta) ?></textarea>
</div>


<div class="custom-control custom-radio mb-2">
    <input type="radio" class="custom-control-input" value="0" id="aberta" name="situacao"
           id="ativo" <?= $conta->situacao == false ? 'checked' : '' ?>>
    <label for="aberta" class="custom-control-label pt-1">Está conta está em aberto</label>
</div>
<div class="custom-control custom-radio mb-2">
    <input type="radio" class="custom-control-input" value="1" id="paga" name="situacao"
           id="ativo" <?= $conta->situacao == true ? 'checked' : '' ?>>
    <label for="paga" class="custom-control-label pt-1">Está conta está paga</label>

</div>
