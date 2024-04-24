<div class="user-block">
    <div class="form-row">
        <div class="col-md-12">
            <?php if ($ordem->id === null): ?>
                <div class="contributions">
                    Ordem aberta por: <?= usuario_logado()->nome ?>
                </div>
            <?php else: ?>
                <div class="contributions">
                    Ordem aberta por: <?= esc($ordem->usuario_abertura) ?>
                </div>
                <?php if ($ordem->usuario_responsavel !== null): ?>
                    <p class="contributions px-4">Técnico responsável: <?= esc($ordem->usuario_responsavel) ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($ordem->id === null): ?>

        <div class="form-group">
            <label for="cliente" class="form-control-label">Escolha o cliente</label>
            <a tabindex="0" role="button" data-toggle="popover"
               data-trigger="focus" style="text-decoration: none"
               data-content="Busque pelo nome ou CPF do cliente. É preciso digitar pelo menos 3 caracteres para buscar">
                &nbsp;&nbsp;<i class="fa fa-question-circle text-info fa-lg"></i>
            </a>
            <select name="cliente" class="selectize" id="cliente" required>
                <option value="">Escolha...</option>
            </select>
        </div>

    <?php else: ?>

        <div class="form-group">
            <label class="form-control-label">Cliente</label>
            <a tabindex="0" role="button" data-toggle="popover"
               data-trigger="focus" style="text-decoration: none"
               data-content="Não é permitido editar cliente da ordem de serviço">
                &nbsp;&nbsp;<i class="fa fa-question-circle text-info fa-lg"></i>
            </a>
            <input type="text" disabled readonly class="form-control"
                   value="<?= esc($ordem->nome) ?>">
        </div>

    <?php endif; ?>

    <div class="form-group">
        <label class="form-control-label">Equipamento</label>
        <input type="text" name="equipamento" placeholder="Descreva o equipamento" class="form-control"
               value="<?= esc($ordem->equipamento) ?>">
    </div>
    <div class="form-group">
        <label class="form-control-label">Defeitos</label>
        <textarea name="defeito" placeholder="Descreva os defeitos" class="form-control date"
        ><?= esc($ordem->defeito) ?></textarea>
    </div>
    <div class="form-group">
        <label class="form-control-label">Observações</label>
        <textarea name="observacoes" placeholder="Observações" class="form-control date"
        ><?= esc($ordem->observacoes) ?></textarea>
    </div>

    <?php if ($ordem->id): ?>
        <div class="form-group">
            <label class="form-control-label">Parecer técnico</label>
            <textarea name="parecer_tecnico" placeholder="Descreva o parecer técnico" class="form-control date"
            ><?= esc($ordem->parecer_tecnico) ?></textarea>
        </div>
    <?php endif; ?>

</div>

