<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<link href="https://cdn.datatables.net/v/bs4/dt-2.0.3/r-3.0.1/datatables.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">
    <div class="col-lg-12">
        <div class="block">

            <a href="<?= site_url('ordens/criar') ?>" class="btn btn-danger mb-4">Cadastrar ordem de serviço</a>

            <div class="table-responsive">
                <table id="ajaxTable" class="table table-striped table-sm" style="width: 100%">
                    <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Data de abertura</th>
                        <th>Situação</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.datatables.net/v/bs4/dt-2.0.3/r-3.0.1/datatables.min.js"></script>
<script>
    $(document).ready(function () {

        const DATATABLE_PTBR = {
            "sEmptyTable": "Nenhum registro encontrado",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(Filtrados de _MAX_ registros)",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "_MENU_ resultados por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sZeroRecords": "Nenhum registro encontrado",
            "sSearch": "Pesquisar",
            "oPaginate": {
                "sNext": "Próximo",
                "sPrevious": "Anterior",
                "sFirst": "Primeiro",
                "sLast": "Último"
            },
            "oAria": {
                "sSortAscending": ": Ordenar colunas de forma ascendente",
                "sSortDescending": ": Ordenar colunas de forma descendente"
            },
            "select": {
                "rows": {
                    "_": "Selecionado %d linhas",
                    "0": "Nenhuma linha selecionada",
                    "1": "Selecionado 1 linha"
                }
            }
        }

        $('#ajaxTable').DataTable({
            "oLanguage": DATATABLE_PTBR,
            "ajax": "<?= site_url('ordens/recuperaordens') ?>",
            "columns": [
                {"data": "codigo"},
                {"data": "cliente"},
                {"data": "cpf"},
                {"data": "created_at"},
                {"data": "situacao"},
            ],
            "order": [],
            "deferRender": true,
            "processing": true,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw">'
            },
            "responsive": true,
            "pagingType": $(window).width() < 768 ? "simple" : "simple_numbers"
        })
    })
</script>
<?= $this->endSection() ?>
