<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>

<link rel="stylesheet" href="<?= site_url('recursos/vendor/auto-complete/jquery-ui.css') ?>">

<style>
    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 9999 !important;
    }

    .ui-menu-item .ui-menu-item-wrapper.ui-state-active {
        background: #fff !important;
        color: #007bff !important;
        border: none;
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-12">
        <div class="block">
            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#adicionarItens">
                Adicionar itens
            </button>

            <?php if ($ordem->itens === null): ?>
                <div class="contributions py-3 text-center">
                    <p class="my-0">Nenhum item foi adicionado à ordem</p>
                </div>
            <?php else: ?>
                <div class="table-responsive my-3">
                    <table class="table table-borderless table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Preço</th>
                                <th scope="col">Quantidade</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col" class="text-center">Remover</th>
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

                                    $hiddenAcoes = [
                                            'id_principal' => $item->id_principal,
                                            'item_id' => $item->id,
                                    ]
                                ?>

                                <tr>
                                    <th scope="row"><?= ellipsize($item->nome, 32, .5) ?></th>
                                    <td><?= esc(ucfirst($item->tipo)) ?></td>
                                    <td>R$ <?= esc(number_format($item->preco_venda, 2, ',', '.')) ?></td>
                                    <td>
                                        <?= form_open("ordensitens/atualizarquantidade/$ordem->codigo", ['class' => 'form-inline'], $hiddenAcoes) ?>
                                            <input style="max-width: 80px !important;" type="number" name="quantidade" class="form-control form-control-sm" value="<?= $item->item_quantidade ?>" required>
                                            <button type="submit" class="btn btn-outline-success btn-sm ml-2">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                                        <?= form_close() ?>
                                    </td>
                                    <td>R$ <?= esc(number_format($item->item_quantidade * $item->preco_venda, 2, ',', '.')) ?></td>
                                    <td class="pt-2 text-center">
                                        <?php
                                            $atributosRemover = [
                                                'class' => 'form-inline',
                                                'onClick' => 'return confirm("Tem certeza da exclusão?")',
                                            ];
                                        ?>
                                        <?= form_open("ordensitens/removeritem/$ordem->codigo", $atributosRemover, $hiddenAcoes) ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm ml-2 mx-auto">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        <?= form_close() ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-right font-weight-bold" colspan="4">
                                    <label for="">Valor produtos:</label>
                                </td>
                                <td class="font-weight-bold">R$ <?= esc(number_format($valorProdutos, 2, ',', '.')) ?></td>
                            </tr>
                            <tr>
                                <td class="text-right font-weight-bold" colspan="4">
                                    <label for="">Valor serviços:</label>
                                </td>
                                <td class="font-weight-bold">R$ <?= esc(number_format($valorServicos, 2, ',', '.')) ?></td>
                            </tr>
                            <tr>
                                <td class="text-right font-weight-bold" colspan="4">
                                    <label for="">Valor total:</label>
                                </td>
                                <td class="font-weight-bold">R$ <?= esc(number_format($valorServicos + $valorProdutos, 2, ',', '.')) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>

            <div class="btn-group">
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
            <a href="<?= site_url("ordens") ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>
        </div>
    </div>
</div>

<div class="modal fade" id="adicionarItens" tabindex="-1" role="dialog" aria-labelledby="adicionarItensLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-tittle" id="adicionarItensLabel">Adicionar itens na ordem <?= $ordem->codigo ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div id="response"></div>

                <div class="ui-widget">
                    <input type="text" name="query" id="query" class="form-control form-control-lg mb-5"
                    placeholder="Pesquise pelo nome ou código do item">
                </div>
                <div class="block-body">

                    <?php
                        $hiddens = [
                            'codigo' => $ordem->codigo,
                            'item_id' => '', //preenchido no autocomplete
                        ];
                    ?>

                    <?= form_open('/', ['id' => 'form'], $hiddens) ?>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label for="" class="form-control-label">Item</label>
                            <input type="text" name="item_nome" class="form-control" readonly required>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="" class="form-control-label">Valor</label>
                            <input type="text" name="item_preco" class="form-control money" readonly required>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="" class="form-control-label">Quantidade</label>
                            <input type="number" name="quantidade" class="form-control" value="1" min="1" step="1" required>
                        </div>
                    </div>


                    <div class="form-group">
                        <input type="submit" id="btn-salvar" class="btn btn-danger btn-sm mr-2" value="Salvar">
                        <button type="button" class="btn btn-secondery btn-sm" data-dismiss="modal">Cancelar</button>
                    </div>

                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= site_url('recursos/vendor/auto-complete/jquery-ui.js') ?>"></script>
<script src="<?= site_url('recursos/vendor/mask/jquery.mask.min.js') ?>"></script>
<script src="<?= site_url('recursos/vendor/mask/app.js') ?>"></script>

<script>
    $(document).ready(function (){
        $("#query").autocomplete({
            minLength: 4,
            source: function (request, response) {
                $.ajax({
                    url: "<?= site_url('ordensitens/pesquisaitens') ?>",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    beforeSend: function () {
                        $("#response").html('');
                        $("#form")[0].reset();
                    },
                    success: function (data) {
                        if (data.length < 1) {
                            data = [{
                                label: 'Item não encontrado',
                                value: -1
                            }];
                        }
                        response(data);
                    },
                });
            },
            select: function (event, ui) {
                $(this).val("");
                event.preventDefault();

                if (ui.item.value === -1) {
                    $(this).val("");
                    return false;
                }
                else {
                    var item_id = ui.item.id;
                    var item_nome = ui.item.value;
                    var item_preco = ui.item.item_preco;

                    $("[name=item_id]").val(item_id);
                    $("[name=item_nome]").val(item_nome);
                    $("[name=item_preco]").val(item_preco);
                }
            },
        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li class='ui-autocomplete-row'></li>")
                .data("item.autocomplete", item)
                .append(item.label)
                .appendTo(ul);
        }
    })

    $("#form").on('submit', function (e) {

        e.preventDefault()

        $.ajax({
            type: 'POST',
            url: '<?= site_url('ordensitens/adicionaritem') ?>',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $('#response').html('')
                $('#btn-salvar').val('Por favor aguarde...')
            },
            success: function (response) {
                $('#btn-salvar').val('Salvar').removeAttr('disabled')

                $('[name=csrf_ordem]').val(response.token);

                if (!response.erro) {

                    if (response.info) {
                        $('#response').html('<div class="alert alert-info">' + response.info + '</div>');
                    } else {
                        window.location.href = "<?= site_url("ordensitens/itens/$ordem->codigo"); ?>"
                    }
                } else {
                    $('#response').html('<div class="alert alert-danger">' + response.erro + '</div>');

                    if (response.erros_model) {
                        $.each(response.erros_model, function (key, value) {
                            $("#response").append('<ul class="list-unstyled"><li class="text-danger">' + value + '</li></ul>')
                        })
                    }
                }
            },
            error: function () {
                alert('Não foi possível processar a solicitação. Por favor entre em contato com o suporte técnico.')
                $('#btn-salvar').val('Salvar').removeAttr('disabled')
            }
        })

    });

    $("#form").submit(function () {
        $(this).find(":submit").attr('disabled', 'disabled')
    })
</script>

<?= $this->endSection() ?>
