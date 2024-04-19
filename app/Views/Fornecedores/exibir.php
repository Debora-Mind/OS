<?= $this->extend('Layout/principal') ?>

<?= $this->section('titulo') ?> <?= $titulo ?> <?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<!--estilos-->
<?= $this->endSection() ?>

<?= $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-4">
        <div class="user-block block">

            <h5 class="card-title mt-2"><?= esc($fornecedor->razao) ?></h5>
            <p class="card-text"><b>Telefone: </b><?= esc($fornecedor->telefone) ?></p>
            <p class="card-text"><b>CNPJ: </b><?= esc($fornecedor->cnpj) ?></p>
            <p class="card-text"><b>IE: </b><?= esc($fornecedor->ie) ?></p>
            <p class="card-text"><b>CEP: </b><?= esc($fornecedor->cep) ?></p>
            <p class="card-text"><b>Endereço: </b><?= esc($fornecedor->endereco) ?></p>
            <p class="card-text"><b>Número: </b><?= esc($fornecedor->numero) ?></p>
            <p class="card-text"><b>Bairro: </b><?= esc($fornecedor->bairro) ?></p>
            <p class="card-text"><b>Cidade: </b><?= esc($fornecedor->cidade) ?></p>
            <p class="card-text"><b>Estado: </b><?= esc($fornecedor->estado) ?></p>
            <p class="card-text">Criado <?= $fornecedor->created_at->humanize() ?></p>
            <p class="card-text">Atualizado <?= $fornecedor->updated_at->humanize() ?></p>
            <?php if ($fornecedor->deleted_at): ?>
                <p class="card-text">Excluído <?= $fornecedor->deleted_at->humanize() ?></p>
            <?php endif; ?>
            <p class="contributions mt-0"><?= $fornecedor->exibeSituacao() ?></p>
            <br>

            <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    Ações
                </button>
                <div class="dropdown-menu">
                    <a href="<?= site_url("fornecedores/editar/$fornecedor->id") ?>" class="dropdown-item">Editar
                        fornecedor</a>
                    <a href="<?= site_url("fornecedores/notas/$fornecedor->id") ?>" class="dropdown-item">Gegenciar
                        as notas fiscais</a>
                    <div class="dropdown-divider"></div>
                    <?php if ($fornecedor->deleted_at == null): ?>
                        <a href="<?= site_url("fornecedores/excluir/$fornecedor->id") ?>" class="dropdown-item">Excluir
                            fornecedor</a>
                    <?php else: ?>
                        <a href="<?= site_url("fornecedores/restaurar/$fornecedor->id") ?>" class="dropdown-item">Restaurar
                            fornecedor</a>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= site_url("fornecedores") ?>" class="btn btn-secondary ml-2">Voltar</a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!--scripts-->
<?= $this->endSection() ?>
