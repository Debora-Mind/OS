<div class="form-group">
    <label class="form-control-label">Nome completo</label>
    <input type="text" name="nome" placeholder="Insira o nome completo" class="form-control" value="<?= esc($usuario->nome) ?>">
</div>
<div class="form-group">
    <label class="form-control-label">Email</label>
    <input type="email" name="email" placeholder="Insira o e-mail de acesso" class="form-control" value="<?= esc($usuario->email) ?>">
</div>
<div class="form-group">
    <label class="form-control-label">Senha</label>
    <input type="password" name="password" placeholder="Senha de acesso" class="form-control">
</div>
<div class="form-group">
    <label class="form-control-label">Confirmação de senha</label>
    <input type="password" name="password_confirmation" placeholder="Confirme a senha de acesso" class="form-control">
</div>

<div class="custom-control custom-checkbox">
    <input type="hidden" name="ativo" value="0">
    <input type="checkbox" class="custom-control-input" value="1" name="ativo" id="ativo" <?= $usuario->ativo == true ? 'checked' : '' ?>>
    <label for="ativo" class="custom-control-label">Usuário ativo</label>
</div>
