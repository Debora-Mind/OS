<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-4">
        <div class="user-block block">

            <h5 class="card-title mt-2"><?= esc($conta->razao) ?></h5>
            <p class="card-text"><b>CNPJ: </b><?= esc($conta->cnpj) ?></p>
            <p class="card-text"><b>Valor da cobnta: </b>R$&nbsp;
                <?= esc(number_format($conta->valor_conta, '2', ',', '.')) ?>
            </p>
            <p class="card-text">Criado <?= $conta->created_at->humanize() ?></p>
            <p class="card-text">Atualizado <?= $conta->updated_at->humanize() ?></p>
            <p class="contributions mt-0"><?= $conta->exibeSituacao() ?></p>
            <br>

            <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a href="<?= site_url("contas/editar/$conta->id") ?>" class="dropdown-item">Editar
                        conta</a>
                    <?php if ($conta->deleted_at == null): ?>
                        <a href="<?= site_url("contas/excluir/$conta->id") ?>" class="dropdown-item">Excluir
                            conta</a>
                    <?php else: ?>
                        <a href="<?= site_url("contas/restaurar/$conta->id") ?>" class="dropdown-item">Restaurar
                            conta</a>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= site_url("contas") ?>" class="btn btn-secondary ml-2">Voltar</a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->
<?= $this->endSection() ?>
