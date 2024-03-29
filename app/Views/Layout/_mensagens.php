<?php if (session()->has('sucesso')): ?>

<div class="alert alert-success alert-dimissible fade show">
    <strong>Tudo certo!</strong> <?= session('sucesso')?>
    <button class="close" type="button" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<?php endif; ?>

<?php if (session()->has('info')): ?>

    <div class="alert alert-info alert-dimissible fade show">
        <strong>Informação!</strong> <?= session('info')?>
        <button class="close" type="button" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

<?php endif; ?>

<?php if (session()->has('atencao')): ?>

    <div class="alert alert-warning alert-dimissible fade show">
        <strong>Atenção!</strong> <?= session('atencao')?>
        <button class="close" type="button" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

<?php endif; ?>

<?php if (session()->has('erros_model')): ?>

    <ul>
        <?php foreach ($erros_model as $erro): ?>

            <li class="text-danger"><?= $erro ?></li>

        <?php endforeach; ?>
    </ul>

<?php endif; ?>

<?php if (session()->has('error')): ?>

    <div class="alert alert-danger alert-dimissible fade show">
        <strong>Error!</strong> <?= session('error')?>
        <button class="close" type="button" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

<?php endif; ?>
