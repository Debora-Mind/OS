<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-4">
        <div class="user-block block">

            <?php if ($item->tipo === 'produto'): ?>
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
            <?php endif; ?>

            <h5 class="card-title mt-2"><?= esc($item->nome) ?></h5>
            <div class="row justify-content-sm-start">
                <div class="d-flex align-items-center">
                    <p class="contributions mt-0 ml-3 px-4"><?= $item->exibeTipo() ?></p>
                </div>
                <div class="d-flex align-items-center">
                    <p class="contributions mt-0 ml-1 px-4">Estoque: <?= $item->exibeEstoque() ?></p>
                </div>
                <div class="d-flex align-items-center">
                    <p class="contributions mt-0 ml-1 px-4"><?= $item->exibeSituacao() ?></p>
                </div>
            </div>

            <p class="contributions mt-0">
                <a target="_blank" class="btn btn-sm" href="<?= site_url("itens/codigobarras/$item->id") ?>">
                    Ver código de barras do item</a>
            </p>

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
    <div class="col-lg-8">
        <div class="user-block block">

            <div>
                <h5 class="card-title mt-2">Histórico de alterações do item</h5>

                <?php if (isset($item->historico) === false): ?>

                <p class="contributions text-warning">Item não possui histórico de alterações</p>

                <?php else: ?>

                    <?php foreach ($item->historico as $key => $historico): ?>

                    <div id="accordion">
                        <div class="card">
                            <div class="card-header" id="heading-<?= $key ?>">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-<?= $key ?>"
                                    aria-expanded="false" aria-controls="collapseOne">
                                        Em <?= date('d/m/Y H:i', strtotime($historico['created_at'])) ?>
                                    </button>
                                </h5>
                            </div>

                            <div class="collapse <?= $key === 0 ? 'show' : '' ?>" id="collapse-<?= $key ?>"
                                 aria-labelledby="heading-<?= $key ?>" data-parent="#accordion">
                                <div class="card-body">
                                    <?php foreach ($historico['atributos_alterados'] as $evento): ?>
                                        <p class="my-0"><?= $evento ?> </p>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->
<?= $this->endSection() ?>
