<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-4">
        <div class="user-block block">

            <h5 class="card-title mt-2"><?= esc($cliente->nome) ?></h5>
            <p class="card-text"><b>CPF: </b><?= esc($cliente->cpf) ?></p>
            <p class="card-text"><b>Telefone: </b><?= esc($cliente->telefone) ?></p>
            <p class="card-text"><b>E-mail: </b><?= esc($cliente->email) ?></p>
            <p class="card-text"><b>CEP: </b><?= esc($cliente->cep) ?></p>
            <p class="card-text"><b>Endereço: </b><?= esc($cliente->endereco) ?></p>
            <p class="card-text"><b>Número: </b><?= esc($cliente->numero) ?></p>
            <p class="card-text"><b>Bairro: </b><?= esc($cliente->bairro) ?></p>
            <p class="card-text"><b>Cidade: </b><?= esc($cliente->cidade) ?></p>
            <p class="card-text"><b>Estado: </b><?= esc($cliente->estado) ?></p>
            <p class="card-text">Criado <?= $cliente->created_at->humanize() ?></p>
            <p class="card-text">Atualizado <?= $cliente->updated_at->humanize() ?></p>
            <?php if ($cliente->deleted_at): ?>
                <p class="card-text">Excluído <?= $cliente->deleted_at->humanize() ?></p>
            <?php endif; ?>
            <p class="contributions mt-0"><?= $cliente->exibeSituacao() ?></p>
            <br>

            <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a href="<?= site_url("clientes/editar/$cliente->id") ?>" class="dropdown-item">Editar
                        cliente</a>
                    <a href="<?= site_url("clientes/historicodeatendimentos/$cliente->id") ?>" class="dropdown-item">Histórico
                    de atendimentos</a>
                    <div class="dropdown-divider"></div>
                    <?php if ($cliente->deleted_at == null): ?>
                        <a href="<?= site_url("clientes/excluir/$cliente->id") ?>" class="dropdown-item">Excluir
                            cliente</a>
                    <?php else: ?>
                        <a href="<?= site_url("clientes/restaurar/$cliente->id") ?>" class="dropdown-item">Restaurar
                            cliente</a>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= site_url("clientes") ?>" class="btn btn-secondary ml-2">Voltar</a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->
<?= $this->endSection() ?>
