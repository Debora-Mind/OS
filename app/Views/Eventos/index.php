<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<link rel="stylesheet" href="<?= site_url('recursos/vendor/fullcalendar/fullcalendar.min.css') ?>">
<link rel="stylesheet" href="<?= site_url('recursos/vendor/fullcalendar/toastr.min.css') ?>">

<!-- TODO Estilizar calendário para mobile-->
<style>
    .fc-toolbar.fc-header-toolbar {
        margin-bottom: 0.5rem;
        color: #e3e3e3;
    }
    .fc-event, .fc-event-dot {
        background-color: #d96473;
        border-color: darkred;
        color: #ffffff !important;
    }
    .fc-day {
        background-color: #343a3f;
    }
    .fc-past {
        opacity: 0.45;
    }
    .fc-today {
        background-color: #c4c4c6 !important;
    }
    .fc-day-top.fc-today {
        background-color: #c4c4c6 !important;
        border: 1px solid #dbdbdb !important;
        border-bottom: none !important;
    }
    .fc-day-number {
        color: #f1f1f1;
    }
    .fc-today .fc-day-number {
        color: #d96473;
        font-weight: bold;
    }
    .fc-day-header {
        background-color: #d96473;
        color: #e3e3e3;
        font-weight: bold;
    }
</style>

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
                        },
                        error: function () {
                            alert('Não foi possível processar a solicitação. Por favor entre em contato com o suporte técnico.')
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
                        error: function () {
                            alert('Não foi possível processar a solicitação. Por favor entre em contato com o suporte técnico.')
                        },
                    })
                }
            },
            eventClick: function (event) {
                if (event.conta_id || event.ordem_id){
                        alert(event.title)
                }
                else {
                    var exibeEvento = confirm(event.title + '\r\n\r' + 'Gostaria de excluir esse evento?')
                    if (exibeEvento){
                        var confirmaExclusao = confirm('Tem certeza?');

                        if (confirmaExclusao){
                            $.ajax({
                                url: '<?= site_url('eventos/excluir') ?>',
                                type: 'GET',
                                data: {
                                    id: event.id,
                                },
                                success: function (response){
                                    calendario.fullCalendar('removeEvents', event.id)
                                    exibeMensagem('Evento removido com sucesso!')
                                },
                                error: function () {
                                    alert('Não foi possível processar a solicitação. Por favor entre em contato com o suporte técnico.')
                                }
                            })
                        }
                    }
                }
            }
        })
    })

    function exibeMensagem(mensagem){
        toastr.success(mensagem, 'Evento');
    }

</script>


<?= $this->endSection() ?>
