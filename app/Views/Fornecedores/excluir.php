<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-6">
        <div class="block">
            <div class="block-body">

                <?= form_open("fornecedores/excluir/$fornecedor->id") ?>

                <div class="alert alert-warning" role="alert">
                    Tem certeza da exclusão do registro?
                </div>

                <div class="form-group mt-5 mb-0">
                    <input id="btn-salvar" type="submit" class="btn btn-danger btn-sm mr-2" value="Sim, pode excluir">
                    <a href="<?= site_url("fornecedores/exibir/$fornecedor->id") ?>"
                       class="btn btn-secondary btn-sm ml-2">Cancelar</a>
                </div>

                <?= form_close() ?>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<?= $this->endSection() ?>
