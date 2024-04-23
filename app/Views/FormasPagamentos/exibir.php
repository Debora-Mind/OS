<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <?php if ($forma->id < 3): ?>
        <div class="col-md-12">
            <div class="alert alert-info pb-0" role="alert">
                <h4 class="alert-heading">Importante</h4>
                <p>A forma de pagamento <b><?= $forma->nome ?></b> não pode ser editada ou excluída, pois
                    pertence ao sistema.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-lg-4">


        <div class="user-block block">

            <h5 class="card-title mt-2"><?= esc($forma->nome) ?></h5>
            <p class="card-text"><?= esc($forma->descricao) ?></p>
            <p class="card-text">Criado <?= $forma->created_at->humanize() ?></p>
            <p class="card-text">Atualizado <?= $forma->updated_at->humanize() ?></p>
            <p class="contributions mt-0"><?= $forma->exibeSituacao() ?></p>
            <br>

            <?php if ($forma->id >= 3): ?>
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                        Ações
                    </button>
                    <div class="dropdown-menu">
                        <a href="<?= site_url("formaspagamentos/editar/$forma->id") ?>" class="dropdown-item">Editar forma de pagamento</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?= site_url("formaspagamentos/excluir/$forma->id") ?>" class="dropdown-item">Excluir forma de pagamento</a>
                    </div>
                </div>
            <?php endif; ?>
            <a href="<?= site_url("formaspagamentos") ?>" class="btn btn-secondary mr-2">Voltar</a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->
<?= $this->endSection() ?>
