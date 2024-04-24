<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<link rel="stylesheet" href="<?= site_url('recursos/vendor/selectize/selectize.bootstrap4.css') ?>">
<style>
    /* Estilizando o select para acompanhar a formatação do template */

    .selectize-input,
    .selectize-control.single .selectize-input.input-active {
        background: #2d3035 !important;
    }

    .selectize-dropdown,
    .selectize-input,
    .selectize-input input {
        color: #777;
    }

    .selectize-input {
        /*        height: calc(2.4rem + 2px);*/
        border: 1px solid #444951;
        border-radius: 0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-12">
        <div class="block">
            <div class="block-body">

                <div id="response">
                    <!-- Retornos -->
                </div>

                <?= form_open('/', ['id' => 'form'], ['codigo' => "$ordem->codigo"]) ?>

                <?= $this->include('Ordens/_form') ?>

                <div class="form-group mt-5 mb-0">

                    <input id="btn-salvar" type="submit" class="btn btn-danger btn-sm mr-2" value="Salvar">

                    <a href="<?= site_url("ordens") ?>"
                       class="btn btn-secondary btn-sm ml-2">Voltar</a>
                </div>

                <?= form_close() ?>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script type="text/javascript" src="<?= site_url('recursos/vendor/selectize/selectize.min.js') ?>"></script>

<script>
    $(document).ready(function () {
        var $select = $(".selectize").selectize({
            create: false,
            sortField: "text",
            valueField: 'id',
            maxItem: 1,
            labelField: 'nome',
            searchField: ['nome', 'cpf'],
            load: function (query, callback) {
                if (query.length < 3) {
                    return callback()
                }

                $.ajax({
                    url: '<?= site_url('ordens/buscaClientes/') ?>',
                    data: {
                        termo: encodeURIComponent(query)
                    },
                    success: function (response){
                        $select.options = response

                        callback(response)
                    },
                    error: function () {
                        alert('Não foi possível processar a solicitação. Por favor entre em contato com o suporte técnico.')
                    }
                })
            }
        })})

    $(document).ready(function () {

        $("#form").on('submit', function (e) {

            e.preventDefault()

            $.ajax({
                type: 'POST',
                url: '<?= site_url('ordens/cadastrar') ?>',
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
                        window.location.href = "<?= site_url("ordens/detalhes/$ordem->codigo"); ?>"
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
