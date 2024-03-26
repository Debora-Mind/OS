<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<link href="https://cdn.datatables.net/v/bs4/dt-2.0.3/r-3.0.1/datatables.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="block">
                <div class="title"><strong>Compact Table</strong></div>
                <div class="table-responsive">
                    <table id="ajaxTable" class="table table-striped table-sm" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Imagem</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Situação</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/v/bs4/dt-2.0.3/r-3.0.1/datatables.min.js"></script>
<?= $this->endSection() ?>
