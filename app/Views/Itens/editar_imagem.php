<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-5">
        <div class="block">
            <div class="block-body">

                <?php if (count($item->imagens) >= 10): ?>

                    <p class="contributions text-warning my-0">Esse item já possui as 10 imagens permitidas. <br>
                        Para adicionar mais, precisa remover as imagens atuais.
                    </p>

                <?php else: ?>
                <div id="response">
                    <!-- Retornos -->
                </div>

                <?= form_open_multipart('/', ['id' => 'form'], ['id' => "$item->id"]) ?>

                <div class="form-group col-md-12">
                    <label class="form-control-label">Escolha uma ou mais imagens</label>
                    <input type="file" name="imagens[]" class="form-control-file" multiple
                           accept="">
                </div>

                <div class="form-group mt-5 mb-0">
                    <input id="btn-salvar" type="submit" class="btn btn-danger btn-sm mr-2" value="Salvar">
                    <a href="<?= site_url("itens/exibir/$item->id") ?>"
                       class="btn btn-secondary btn-sm ml-2">Voltar</a>
                </div>

                <?= form_close() ?>

                <?php endif; ?>

            </div>
        </div>

    </div>

    <div class="col-lg-7">
        <div class="user-block block">

            <?php if (empty($item->imagens)): ?>
                <p class="contributions text-warning my-0">Esse item ainda não possui nenhuma imagem</p>
            <?php else: ?>

                <ul class="listi-inline text-center p-0 m-0">

                    <?php foreach ($item->imagens as $imagem): ?>
                        <li class="list-inline-item">
                            <div class="card" style="width: 10rem">
                                <img class="card-img-top" src="<?= site_url("itens/imagem/$imagem->imagem") ?>"
                                     alt="Imagem do produto <?= esc($item->nome) ?>">
                                <div class="card-body text-center py-2">

                                    <?php
                                        $atributos = [
                                                'onSubmit' => "return confirm('Tem certeza da exclusão da imagem?')",
                                        ]
                                    ?>

                                    <?= form_open("itens/removeimagem/$imagem->imagem", $atributos) ?>

                                        <button class="btn btn-sm btn-danger">Excluir</button>

                                    <?= form_close() ?>

                                </div>
                            </div>
                        </li>

                    <?php endforeach; ?>

                </ul>


            <?php endif; ?>

        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= site_url('recursos/vendor/mask/app.js') ?>"></script>
<script src="<?= site_url('recursos/vendor/mask/jquery.mask.min.js') ?>"></script>

<script>
    $(document).ready(function () {

        $("#form").on('submit', function (e) {

            e.preventDefault()

            $.ajax({
                type: 'POST',
                url: '<?= site_url('itens/upload') ?>',
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
                        window.location.href = "<?= site_url("itens/editarimagem/$item->id"); ?>"
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

<!--Limita o tamanho do arquivo para 5MB-->
<script>
    $(document).ready(function () {
        // Função para validar o tamanho do arquivo
        function validarTamanhoArquivo(input) {
            // Verificar se o navegador suporta a propriedade 'files'
            if (input.files && input.files[0]) {
                var tamanhoMaximo = 5 * 1024 * 1024; // 5 megabytes em bytes
                var arquivo = input.files[0];

                // Verificar o tamanho do arquivo
                if (arquivo.size > tamanhoMaximo) {
                    $('#response').html(
                        '<div class="alert alert-danger alert-dimissible fade show">' +
                        '    Verifique os erros abaixo e tente novamente' +
                        '    <button class="close" type="button" data-dismiss="alert" aria-label="Close">' +
                        '        <span aria-hidden="true">&times;</span>' +
                        '    </button>' +
                        '</div>' +
                        '<ul class="pl-0">' +
                        '    <span class="text-danger">Por favor selecione uma nota fiscal de no máximo 5MB</span>' +
                        '</ul>'
                    );
                    $(input).val('');
                }
            }
        }

        // Associar evento onchange ao campo de entrada de arquivo
        $('input[type="file"]').change(function () {
            validarTamanhoArquivo(this);
        });
    });
</script>

<?= $this->endSection() ?>
