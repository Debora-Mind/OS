<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-8">
        <div class="block">
            <div class="block-body">

                <div id="response">
                    <!-- Retornos -->
                </div>

                <?= form_open_multipart('/', ['id' => 'form'], ['id' => "$fornecedor->id"]) ?>

                <div class="form-group col-md-12">
                    <label class="form-control-label">Valos da nota fiscal</label>
                    <input type="text" name="valor_nota" placeholder="Insira o valor" class="form-control"
                           value="<?= esc('') ?>">
                </div>
                <div class="form-group col-md-12">
                    <label class="form-control-label">Data emissão da nota</label>
                    <input type="date" name="data_emissao" placeholder="Data emissão" class="form-control"
                           value="<?= esc('') ?>">
                </div>
                <div class="form-group col-md-12">
                    <label class="form-control-label">Arquivo em PDF da nota fiscal</label>
                    <input type="file" name="nota_fiscal" class="form-control-file"
                           value="<?= esc('') ?>">
                </div>
                <div class="form-group col-md-12">
                    <label class="form-control-label">Breve descrição dos itens da nota fiscal</label>
                    <textarea name="descricao_itens" class="form-control" placeholder="Insira a descrição dos itens..."
                    ><?= esc('') ?></textarea>
                </div>

                <div class="form-group mt-5 mb-0">
                    <input id="btn-salvar" type="submit" class="btn btn-danger btn-sm mr-2" value="Salvar">
                    <a href="<?= site_url("fornecedores/exibir/$fornecedor->id") ?>"
                       class="btn btn-secondary btn-sm ml-2">Voltar</a>
                </div>

                <?= form_close() ?>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= site_url('recursos/vendor/loadingoverlay/loadingoverlay.min.js') ?>"></script>
<script src="<?= site_url('recursos/vendor/mask/app.js') ?>"></script>
<script src="<?= site_url('recursos/vendor/mask/jquery.mask.min.js') ?>"></script>

<script>
    $(document).ready(function () {

        <?= $this->include('Fornecedores/_viacep') ?>

        $("#form").on('submit', function (e) {

            e.preventDefault()

            $.ajax({
                type: 'POST',
                url: '<?= site_url('fornecedores/atualizar') ?>',
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
                            window.location.href = "<?= site_url("fornecedores/exibir/$fornecedor->id"); ?>"
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
    })
</script>

<?= $this->endSection() ?>
