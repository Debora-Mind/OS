<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-4">
        <div class="block">
            <div class="text-center">
                
                <?php if ($usuario->imagem == null): ?>
                
                    <img src="<?= site_url('recursos/img/usuario_sem_imagem.png') ?>" alt="Usuário sem imagem"
                         class="card-img-top" style="width: 90%">
                
                <?php else: ?>
                
                    <img src="<?= site_url("usuarios/imagem/$usuario->imagem") ?>" alt="<?= $usuario->nome ?>"
                         class="card-img-top" style="width: 90%">
                
                <?php endif; ?>

                <a href="<?= site_url("usuario/editarimagem/$usuario->id") ?>"
                   class="btn btn-outline-primary btn-sm mt-3">Alterar imagem</a>
            </div>

            <hr class="border-secondary">
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->
<?= $this->endSection() ?>
