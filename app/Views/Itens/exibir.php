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

                <?php if ($item->imagem == null): ?>

                    <img src="<?= site_url('recursos/img/item_sem_imagem.png') ?>" alt="Item sem imagem"
                         class="card-img-top" style="width: 90%">

                <?php else: ?>

                    <img src="<?= site_url("itens/imagem/$item->imagem") ?>" alt="<?= $item->nome ?>"
                         class="card-img-top" style="width: 90%">

                <?php endif; ?>

                <a href="<?= site_url("itens/editarimagem/$item->id") ?>"
                   class="btn btn-outline-primary btn-sm mt-3">Alterar imagem</a>
            </div>

            <hr class="border-secondary">

            <h5 class="card-title mt-2"><?= esc($item->nome) ?></h5>
            <p class="contributions mt-0"><?= $item->exibeTipo() ?></p>
            <p class="contributions mt-0">Estoque: <?= $item->exibeEstoque() ?></p>
            <p class="contributions mt-0"><?= $item->exibeSituacao() ?></p>

            <?php if ($item->marca): ?>
                <p class="card-text"><b>Marca </b><?= esc($item->marca) ?></p>
            <?php endif; ?>

            <?php if ($item->modelo): ?>
                <p class="card-text"><b>Modelo </b><?= esc($item->modelo) ?></p>
            <?php endif; ?>

            <?php if ($item->preco_custo): ?>
                <p class="card-text"><b>Preço de custo </b><?= $item->precoCustoFormatado() ?></p>
            <?php endif; ?>

            <p class="card-text">
                <b><?= $item->tipo == 'produto' ? 'Preço de venda ' : 'Valor do serviço ' ?></b>
                <?= $item->precoVendaFormatado() ?></p>

            <p class="card-text">Criado <?= $item->created_at->humanize() ?></p>
            <p class="card-text">Atualizado <?= $item->updated_at->humanize() ?></p>
            <?php if ($item->deleted_at): ?>
                <p class="card-text">Excluído <?= $item->deleted_at->humanize() ?></p>
            <?php endif; ?>
            <br>

            <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a href="<?= site_url("itens/editar/$item->id") ?>" class="dropdown-item">Editar item</a>
                    <div class="dropdown-divider"></div>
                    <?php if ($item->deleted_at == null): ?>
                        <a href="<?= site_url("itens/excluir/$item->id") ?>" class="dropdown-item">Excluir
                            item</a>
                    <?php else: ?>
                        <a href="<?= site_url("itens/restaurar/$item->id") ?>" class="dropdown-item">Restaurar
                            item</a>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= site_url("itens") ?>" class="btn btn-secondary ml-2">Voltar</a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->
<?= $this->endSection() ?>
