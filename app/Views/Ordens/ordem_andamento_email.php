<h3>Olá, <?= esc($ordem->cliente->nome) ?></h3>

<p>Até o momento a sua ordem de serviço está com o status de <strong><?= ucfirst(esc($ordem->situacao)) ?></strong></p>

<p>
    <strong>Equipamento: </strong><?= esc($ordem->equipamento) ?>
<p>
    <strong>Defeito: </strong><?= esc($ordem->defeito ?? 'Não informado') ?>
</p>
<p>
    <strong>Observações: </strong><?= esc($ordem->observacoes ?? 'Não informado') ?>
</p>
<p>
    <strong>Data de abertura: </strong><?= date('d/m/Y H:i', strtotime($ordem->created_at)) ?>
</p>

<?php if($ordem->itens === null): ?>
    <p>Nenhum item foi adicionado à ordem até o momento</p>
<?php else: ?>
    <?php
        $valorProduto = 0;
        $valorServicos = 0;

        foreach ($ordem->itens as $item){
            if ($item->tipo === 'produto') {
                $valorProduto += $item->preco_venda * $item->quantidade;
            }
            else {
                $valorServicos += $item->preco_venda * $item->quantidade;
            }
        }
    ?>
    <p>
        <strong>Valores de produtos: </strong>R$&nbsp;<?= number_format($valorProduto, 2, ',', '.') ?>
    </p>
    <p>
        <strong>Valores de serviços: </strong>R$&nbsp;<?= number_format($valorServicos, 2, ',', '.') ?>
    </p>
    <p>
        <strong>Valor total: </strong>R$&nbsp;<?= number_format($valorServicos + $valorProduto, 2, ',', '.') ?>
    </p>
<?php endif; ?>

<hr>

<p>Não deixe de conferir as suas <strong><a href="<?= site_url('ordens/minhas') ?>">Ordens de serviços</a></strong></p>

<small>Não é necessário responder esse e-mail</small>
