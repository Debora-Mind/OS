<?= $this->extend('Layout/Autenticacao/principal_autenticacao') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>
<div class="row">
    <!-- Logo & Information Panel-->
    <div class="col-lg-6">
        <div class="info d-flex align-items-center">
            <div class="content">
                <div class="logo">
                    <h1><?= $titulo ?></h1>
                </div>
                <p>Crie a sua senha.</p>
            </div>
        </div>
    </div>
    <!-- Form Panel    -->
    <div class="col-lg-6 bg-white">
        <div class="form d-flex align-items-center">
            <div class="content">

                <?= form_open('/', ['id' => 'form', 'class' => 'form-validate'], ['token' => $token]) ?>

                <div id="response">

                </div>


                <div class="form-group">
                    <input id="login-password" type="password" name="password" required
                           data-msg="Por favor informe sua nova senha" class="input-material">
                    <label for="login-password" class="label-material">Sua nova senha</label>
                </div>
                <div class="form-group">
                    <input id="login-password" type="password" name="password-confirmation" required
                           data-msg="Por favor confirme a sua nova senha" class="input-material">
                    <label for="login-password" class="label-material">Confirme a sua nova senha</label>
                </div>

                <input type="submit" id="btn-reset" href="index.html" class="btn btn-primary" value="Salvar">

                <?= form_close() ?>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->
<script>
    $(document).ready(function () {
        $("#form").on('submit', function (e) {

            e.preventDefault()

            $.ajax({
                type: 'POST',
                url: '<?= site_url('password/processareset') ?>',
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    $('#response').html('')
                    $('#btn-reset').val('Por favor aguarde...')
                },
                success: function (response) {
                    $('#btn-reset').val('Salvar').removeAttr('disabled')

                    $('[name=csrf_ordem]').val(response.token);

                    if (!response.erro) {
                        window.location.href = "<?= site_url('login'); ?>"
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
                    $('#btn-reset').val('Salvar').removeAttr('disabled')
                }
            })

        });

        $("#form").submit(function () {
            $(this).find(":submit").attr('disabled', 'disabled')
        })
    })
</script>
<?= $this->endSection() ?>
