<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-12">
        <div class="block">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a href="#pills-detalhes" id="pills-detalhes-tab" class="nav-link active" data-toggle="pill" role="tab"
                    aria-controls="pills-detalhes" aria-selected="true">Detalhes da ordem</a>
                </li>
                <?php if (isset($ordem->trasacao)): ?>
                    <li class="nav-item">
                        <a href="#pills-transacoes" id="pills-transacoes-tab" class="nav-link" data-toggle="pill" role="tab"
                        aria-controls="pills-transacoes" aria-selected="false">Transações da ordem</a>
                    </li>
                    <?php endif; ?>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-detalhes" role="tabpanel" aria-labelledby="pills-detalhes-tab">
                    <div class="user-block text-center">
                        <div class="user-tittle">
                            <h5 class="card-title mt-2"><?= esc($ordem->nome) ?></h5>
                            <span>Ordem: <?= esc($ordem->codigo) ?></span>
                        </div>
                        <p class="contributions px-4"><?= $ordem->exibeSituacao() ?></p>
                        <p class="contributions px-4">Aberta por: <?= esc($ordem->usuario_abertura) ?></p>
                        <p class="contributions px-4">Responsável técnico: <?= esc($ordem->usuario_responsavel) ?? 'Não definido' ?></p>
                        <?php if ($ordem->situacao === 'encerrada'): ?>
                            <p class="contributions px-4">Encerrada por <?= esc($ordem->usuario_encerramento) ?></p>
                        <?php endif; ?>
                        <p class="card-text">Criado <?= $ordem->created_at->humanize() ?></p>
                        <p class="card-text">Atualizado <?= $ordem->updated_at->humanize() ?></p>

                        <hr class="border-secondary">

                        <?php if ($ordem->itens === null): ?>
                            <div class="contributions py-3">
                                <p class="my-0">Nenhum item foi adicionado à ordem</p>

                                <?php if ($ordem->situacao === 'aberta'): ?>
                                    <a class="btn btn-outline-info btn-sm" href='<?= site_url("ordensitens/itens/$ordem->codigo")?>' >
                                        Adicionar itens</a>
                                <?php endif; ?>

                            </div>
                        <?php else: ?>
                            <div class="table-responsive my-3">
                                <table class="table table-borderless table-striped text-left">
                                    <thead>
                                    <tr>
                                        <th scope="col">Item</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Preço</th>
                                        <th scope="col">Quantidade</th>
                                        <th scope="col">Subtotal</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $valorProdutos = 0;
                                            $valorServicos = 0;
                                        ?>

                                        <?php foreach ($ordem->itens as $item): ?>
                                            <?php
                                                if ($item->tipo === 'produto') {
                                                    $valorProdutos += $item->preco_venda * $item->item_quantidade;
                                                }
                                                else {
                                                    $valorServicos += $item->preco_venda * $item->item_quantidade;
                                                }
                                            ?>
                                            <tr>
                                                <th scope="row"><?= ellipsize($item->nome, 32, .5) ?></th>
                                                <td><?= esc(ucfirst($item->tipo)) ?></td>
                                                <td>R$ <?= esc(number_format($item->preco_venda, 2, ',', '.')) ?></td>
                                                <td><?= $item->item_quantidade ?></td>
                                                <td>R$ <?= esc(number_format($item->item_quantidade * $item->preco_venda, 2, ',', '.')) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td class="text-right font-weight-bold" colspan="4">
                                            <label for="">Valor produtos:</label>
                                        </td>
                                        <td class="font-weight-bold">
                                            R$ <?= esc(number_format($valorProdutos, 2, ',', '.')) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right font-weight-bold" colspan="4">
                                            <label for="">Valor serviços:</label>
                                        </td>
                                        <td class="font-weight-bold">R$ <?= esc(number_format($valorServicos, 2, ',', '.')) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right font-weight-bold" colspan="4">
                                            <label for="">Valor desconto:</label>
                                        </td>
                                        <td class="font-weight-bold">
                                            R$ <?= esc(number_format($ordem->valor_desconto, 2, ',', '.')) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right font-weight-bold" colspan="4">
                                            <label for="">Valor total com desconto:</label>
                                        </td>
                                        <td class="font-weight-bold">R$
                                            <?php
                                            $valorItens = $valorServicos + $valorProdutos;

                                                echo (esc(number_format($valorItens - $ordem->valor_desconto, 2, ',', '.')))
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right font-weight-bold" colspan="4">
                                            <label for="">Valor total da ordem:</label>
                                        </td>
                                        <td class="font-weight-bold">
                                            R$ <?= esc(number_format($valorServicos + $valorProdutos, 2, ',', '.')) ?>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (isset($ordem->trasacao)): ?>
                    <div class="tab-pane fade" id="pills-transacoes" role="tabpanel" aria-labelledby="pills-detalhes-tab">
                     ///
                </div>
                <?php endif; ?>
            </div>

            <div class="btn-group mt-3">
                <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">

                    <?php if ($ordem->situacao === 'aberta'): ?>
                        <a href="<?= site_url("ordens/editar/$ordem->codigo") ?>" class="dropdown-item">Editar ordem</a>
                        <a href="<?= site_url("ordens/encerrar/$ordem->codigo") ?>" class="dropdown-item">Encerrar ordem</a>
                        <a href="<?= site_url("ordensitens/itens/$ordem->codigo") ?>" class="dropdown-item">Gerenciar itens da ordem</a>
                        <a href="<?= site_url("ordens/responsavel/$ordem->codigo") ?>" class="dropdown-item">Definir técnico responsável</a>
                    <?php endif; ?>

                    <a href="<?= site_url("ordensevidenciar/evidencias/$ordem->codigo") ?>" class="dropdown-item">Evidências da ordem</a>
                    <a href="<?= site_url("ordens/email/$ordem->codigo") ?>" class="dropdown-item">Enviar por e-mail</a>
                    <a href="<?= site_url("ordens/gerarpdf/$ordem->codigo") ?>" class="dropdown-item">Gerar PDF</a>


                    <div class="dropdown-divider"></div>
                    <?php if ($ordem->deleted_at == null): ?>
                        <a href="<?= site_url("ordens/excluir/$ordem->codigo") ?>" class="dropdown-item">Excluir
                            ordem</a>
                    <?php else: ?>
                        <a href="<?= site_url("ordens/restaurar/$ordem->codigo") ?>" class="dropdown-item">Restaurar
                            ordem</a>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= site_url("ordens") ?>" class="btn btn-secondary btn-sm ml-2 mt-3">Voltar</a>
        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->
<?= $this->endSection() ?>
