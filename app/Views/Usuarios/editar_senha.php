<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

    <div class="row">

        <div class="col-lg-6">
            <div class="block">
                <div class="block-body">

                    <div id="response">
                        <!-- Retornos -->
                    </div>

                    <?= form_open_multipart('/', ['id' => 'form']) ?>

                    <div class="form-group">
                        <label class="form-control-label">Informe a senha atual</label>
                        <input type="password" name="current_password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Informe a nova senha</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Confirme a nova senha</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>

                    <div class="form-group mt-4 mb-0">

                        <input id="btn-salvar" type="submit" class="btn btn-danger btn-sm mr-2" value="Salvar">

                        <a href="<?= site_url("") ?>" class="btn btn-secondary btn-sm ml-2">Voltar</a>
                    </div>

                    <?= form_close() ?>

                </div>
            </div>

        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

    <script>
        $(document).ready(function () {
            $("#form").on('submit', function (e){

                e.preventDefault()

                $.ajax({
                    type: 'POST',
                    url: '<?= site_url('usuarios/atualizarsenha') ?>',
                    data: new FormData(this),
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function (){
                        $('#response').html('')
                        $('#btn-salvar').val('Por favor aguarde...')
                    },
                    success: function (response){
                        $('#btn-salvar').val('Salvar').removeAttr('disabled')

                        $('[name=csrf_ordem]').val(response.token);

                        if (!response.erro) {
                            $('#form')[0].reset()

                            if (response.info) {
                                $('#response').html('<div class="alert alert-info">' + response.info + '</div>')

                            } else {
                                $('#response').html('<div class="alert alert-success">' + response.sucesso + '</div>')
                            }
                        }
                        else {
                            $('#response').html('<div class="alert alert-danger">' + response.erro + '</div>')

                            if (response.erros_model) {
                                $.each(response.erros_model, function (key, value) {
                                    $("#response").append('<ul class="list-unstyled"><li class="text-danger">' + value + '</li></ul>')
                                })
                            }
                        }
                    },
                    error: function (){
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