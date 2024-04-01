<div class="form-group">
    <label class="form-control-label" for="nome">Nome</label>
    <input type="text" name="nome" placeholder="Insira o nome do grupo de acesso" class="form-control" value="<?= esc($grupo->nome) ?>">
</div>
<div class="form-group">
    <label class="form-control-label" for="descricao">Descrição</label>
    <textarea type="text" name="descricao" placeholder="Insira a descrição do grupo de acesso" class="form-control"><?= esc($grupo->descricao) ?></textarea>
</div>


<div class="custom-control custom-checkbox">
    <input type="hidden" name="tecnico" value="0">
    <input type="checkbox" class="custom-control-input" value="1" name="tecnico" id="tecnico" <?= $grupo->tecnico == true ? 'checked' : '' ?>>
    <label for="tecnico" class="custom-control-label">Grupo de acesso técnico</label>
    <a tabindex="0" role="button" data-toggle="popover"
       data-trigger="focus" title="Importante" style="text-decoration: none"
       data-content="Se esse grupo for definido como <b class='text-danger'>técnico</b>,
       o usuário poderá ser selecionado como <b>Responsável técnico</b> pelas ordens de serviço">
        &nbsp;&nbsp;<i class="fa fa-question-circle text-danger fa-lg"></i>
    </a>
</div>
