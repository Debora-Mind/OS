<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-4">
        <div class="user-block block">

            <h5 class="card-title mt-2"><?= esc($grupo->nome) ?></h5>
            <p class="card-text"><?= esc($grupo->descricao) ?></p>
            <p class="card-text">Criado <?= $grupo->created_at->humanize() ?></p>
            <p class="card-text">Atualizado <?= $grupo->updated_at->humanize() ?></p>
            <?php if ($grupo->deleted_at): ?>
                <p class="card-text">Excluído <?= $grupo->deleted_at->humanize() ?></p>
            <?php endif; ?>
            <p class="contributions mt-0"><?= $grupo->exibeSituacao() ?>
                <a tabindex="0" role="button" data-toggle="popover"
                   data-trigger="focus" title="Importante" style="text-decoration: none"
                   data-content="Esse grupo <?= $grupo->tecnico ? 'será' : 'não será' ?> exibido como opção na hora de definir <b>Responsável técnico</b> pela ordem de serviço">
                    &nbsp;&nbsp;<i class="fa fa-question-circle text-danger fa-lg"></i>
                </a>
            </p>
            <br>

            <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a href="<?= site_url("grupos/editar/$grupo->id") ?>" class="dropdown-item">Editar grupo de acesso</a>
                    <div class="dropdown-divider"></div>
                    <?php if ($grupo->deleted_at == null): ?>
                        <a href="<?= site_url("grupos/excluir/$grupo->id") ?>" class="dropdown-item">Excluir grupo de acesso</a>
                    <?php else: ?>
                        <a href="<?= site_url("grupos/restaurar/$grupo->id") ?>" class="dropdown-item">Restaurar grupo de acesso</a>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= site_url("grupos") ?>" class="btn btn-secondary ml-2">Voltar</a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->
<?= $this->endSection() ?>
