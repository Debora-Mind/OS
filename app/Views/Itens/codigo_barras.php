<?= $this->extend('Layout/Autenticacao/principal_autenticacao') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>
<div class="row">
    <!-- Logo & Information Panel-->
    <div class="col-lg-6 mx-auto">
        <div class="form d-flex align-items-center bg-info">
            <div class="content">
                <div class="mt-5 text-center text-white">
                    <h2 class="text-black"><?= $item->nome ?></h2>
                    <p><?= $item->codigo_barras ?></p>
                    <p><?= $item->codigo_interno ?></p>
                    <p><?= $item->nome ?></p>
                    <p><button class="btn btn-sm btn-primary bg-light text-black border-dark" onclick="window.print()"><i class="fa fa-print"></i> Imprimir</button></p>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->

<?= $this->endSection() ?>
