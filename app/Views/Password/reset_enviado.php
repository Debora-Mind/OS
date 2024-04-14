<?= $this->extend('Layout/Autenticacao/principal_autenticacao') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>
<div class="row">
    <!-- Logo & Information Panel-->
    <div class="col-lg-8 mx-auto">
        <div class="info d-flex align-items-center">
            <div class="content">
                <div class="logo">
                    <h1><?= $titulo ?></h1>
                </div>
                <p>Não deixe de conferir a caixa de span.</p>
            </div>
        </div>
    </div>
    <!-- Form Panel    -->
    <div class="col-lg-6 bg-white d-none">
        <div class="form d-flex align-items-center d-none">
            <div class="content">


            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->

<?= $this->endSection() ?>
