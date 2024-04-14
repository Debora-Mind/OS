<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-4">
        <div class="user-block block">
            <div class="text-center">

                <?php if ($usuario->imagem == null): ?>

                    <img src="<?= site_url('recursos/img/usuario_sem_imagem.png') ?>" alt="Usuário sem imagem"
                         class="card-img-top" style="width: 90%">

                <?php else: ?>

                    <img src="<?= site_url("usuarios/imagem/$usuario->imagem") ?>" alt="<?= $usuario->nome ?>"
                         class="card-img-top" style="width: 90%">

                <?php endif; ?>

                <a href="<?= site_url("usuarios/editarimagem/$usuario->id") ?>"
                   class="btn btn-outline-primary btn-sm mt-3">Alterar imagem</a>
            </div>

            <hr class="border-secondary">

            <h5 class="card-title mt-2"><?= esc($usuario->nome) ?></h5>
            <p class="card-text"><?= esc($usuario->email) ?></p>
            <p class="card-text">Criado <?= $usuario->created_at->humanize() ?></p>
            <p class="card-text">Atualizado <?= $usuario->updated_at->humanize() ?></p>
            <?php if ($usuario->deleted_at): ?>
                <p class="card-text">Excluído <?= $usuario->deleted_at->humanize() ?></p>
            <?php endif; ?>
            <p class="contributions mt-0"><?= $usuario->exibeSituacao() ?></p>
            <br>

            <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a href="<?= site_url("usuarios/editar/$usuario->id") ?>" class="dropdown-item">Editar usuário</a>
                    <a href="<?= site_url("usuarios/grupos/$usuario->id") ?>" class="dropdown-item">Gerenciar os grupos
                        de acesso</a>
                    <div class="dropdown-divider"></div>
                    <?php if ($usuario->deleted_at == null): ?>
                        <a href="<?= site_url("usuarios/excluir/$usuario->id") ?>" class="dropdown-item">Excluir
                            usuário</a>
                    <?php else: ?>
                        <a href="<?= site_url("usuarios/restaurar/$usuario->id") ?>" class="dropdown-item">Restaurar
                            usuário</a>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= site_url("usuarios") ?>" class="btn btn-secondary ml-2">Voltar</a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->
<?= $this->endSection() ?>
