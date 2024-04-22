<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<link rel="stylesheet" href="<?= site_url('recursos/vendor/fullcalendar/fullcalendar.min.css') ?>">
<link rel="stylesheet" href="<?= site_url('recursos/vendor/fullcalendar/toastr.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div id="calendario" class="container-fluid"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= site_url('recursos/vendor/fullcalendar/fullcalendar.min.js') ?>"></script>
<script src="<?= site_url('recursos/vendor/fullcalendar/toastr.min.js') ?>"></script>
<script src="<?= site_url('recursos/vendor/fullcalendar/moment.min.js') ?>"></script>

<script>
    $(document).ready(function () {
        var calendario = $('#calendario').fullCalendar({
            header: {
                left: 'prev, next, today',
                center: 'title',
                right: 'month',
            },
            height: 580,
            editable: true,
            events: '<?= site_url('eventos/eventos') ?>',
            displayEventTime: false,
            selectable: true,
            selectHelper: true,
            select: function (start, end, allDay){
                var title = prompt('Informe o título do evento')
                if (title){
                    var start = $.fullCalendar.formatDate(start, 'Y-MM-DD');
                    var end = $.fullCalendar.formatDate(end, 'Y-MM-DD');

                    $.ajax({
                        url: '<?= site_url('eventos/cadastrar') ?>',
                        type: 'GET',
                        data: {
                            title: title,
                            start: start,
                            end: end,
                        },
                        success: function (response){
                            exibeMensagem('Evento criado com sucesso!')
                            calendario.fullCalendar('renderEvent', {
                                id: response.id,
                                title: title,
                                start: start,
                                end: end,
                                allDay: allDay,
                            }, true)
                            calendario.fullCalendar('unselect')
                        }
                    })
                }
            },
            eventDrop: function (event, denta, revertFunc){
                if (event.conta_id || event.ordem_id){
                    alert('Não é possível alterar o evento, pois o mesmo está atrelado a uma conta ou ordem de serviço.')
                    revertFunc();
                }
                else {
                    var start = $.fullCalendar.formatDate(event.start, 'Y-MM-DD');
                    var end = $.fullCalendar.formatDate(event.end, 'Y-MM-DD');

                    $.ajax({
                        url: '<?= site_url('eventos/atualizar/') ?>' + event.id,
                        type: 'GET',
                        data: {
                            start: start,
                            end: end,
                        },
                        success: function (response){
                            exibeMensagem('Evento atualizado com sucesso!')
                        },
                    })
                }
            }



        })
    })

    function exibeMensagem(mensagem){
        toastr.success(mensagem, 'Evento');
    }

</script>


<?= $this->endSection() ?>
