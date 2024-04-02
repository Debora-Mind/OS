<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<link rel="stylesheet" href="<?= site_url('recursos/vendor/selectize/selectize.bootstrap4.css') ?>">
<style>
    /* Estilizando o select para acompanhar a formatação do template */

    .selectize-input,
    .selectize-control.single .selectize-input.input-active {
        background: #2d3035 !important;
    }

    .selectize-dropdown,
    .selectize-input,
    .selectize-input input {
        color: #777;
    }

    .selectize-input {
        /*        height: calc(2.4rem + 2px);*/
        border: 1px solid #444951;
        border-radius: 0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-8">
        <div class="user-block block">

            <?php if (empty($permissoesDisponiveis)): ?>

                <p class="contributions text-info my-0">Esse grupo já possui todas as permissões disponíveis!</p>

            <?php else: ?>

                <div id="response">
                    <!-- Retornos -->
                </div>

                <?= form_open('/', ['id' => 'form'], ['id' => "$grupo->id"]) ?>

                <div class="form-group">
                    <label for="" class="form-control-label">Escolha uma ou mais permissões</label>
                    <select name="permissao_id[]" multiple class="selectize" id="">
                        <option value="">Escolha...</option>
                        <?php foreach ($permissoesDisponiveis as $permissao): ?>
                            <option value="<?= $permissao->id ?>"><?= esc($permissao->nome) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mt-5 mb-0">

                    <input id="btn-salvar" type="submit" class="btn btn-danger btn-sm mr-2" value="Salvar">

                    <a href="<?= site_url("grupos/exibir/$grupo->id") ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>
                </div>

                <?= form_close() ?>

            <?php endif; ?>

        </div>

    </div>

    <div class="col-lg-4">
        <div class="user-block block">

            <?php if (empty($grupo->permissoes)): ?>
                <p class="contributions text-warning my-0">Esse grupo ainda não possui permissões de acesso!</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Permissão</th>
                                <th>Excluir</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($grupo->permissoes as $permissao): ?>
                            <tr>
                                <td><?= esc($permissao->nome) ?></td>
                                <td><a href="#" class="btn btn-sm brn-danger"></a>Excluir</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="mt-3 ml-1"><?= $grupo->pager->links ?></div>
                </div>
            <?php endif; ?>

        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="text/javascript" src="<?= site_url('recursos/vendor/selectize/selectize.min.js') ?>"></script>
<script>
    $(document).ready(function () {
        $(".selectize").selectize({
            create: true,
            sortField: "text",
        })
    })
</script>
<?= $this->endSection() ?>
