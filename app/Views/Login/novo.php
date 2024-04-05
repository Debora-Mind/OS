<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title> Ordem de Serviço | <?= $titulo ?> </title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="all,follow">
        <!-- Bootstrap CSS-->
        <link rel="stylesheet" href="<?= site_url('recursos/') ?>vendor/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome CSS-->
        <link rel="stylesheet" href="<?= site_url('recursos/') ?>vendor/font-awesome/css/font-awesome.min.css">
        <!-- Custom Font Icons CSS-->
        <link rel="stylesheet" href="<?= site_url('recursos/') ?>css/font.css">
        <!-- Google fonts - Muli-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,700">
        <!-- theme stylesheet-->
        <link rel="stylesheet" href="<?= site_url('recursos/') ?>css/style.default.css" id="theme-stylesheet">
        <!-- Custom stylesheet - for your changes-->
        <link rel="stylesheet" href="<?= site_url('recursos/') ?>css/custom.css">
        <!-- Favicon-->
        <link rel="shortcut icon" href="<?= site_url('recursos/') ?>img/favicon.ico">
        <!-- Tweaks for older IEs--><!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
        <!-- Estilos específicos de views -->

    </head>
  <body>
    <div class="login-page">
      <div class="container d-flex align-items-center">
        <div class="form-holder has-shadow">
          <div class="row">
            <!-- Logo & Information Panel-->
            <div class="col-lg-6">
              <div class="info d-flex align-items-center">
                <div class="content">
                  <div class="logo">
                    <h1><?= $titulo ?></h1>
                  </div>
                  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                </div>
              </div>
            </div>
            <!-- Form Panel    -->
            <div class="col-lg-6 bg-white">
              <div class="form d-flex align-items-center">
                <div class="content">

                <?= form_open('/', ['id' => 'form', 'class' => 'form-validate']) ?>
                    <div class="form-group">
                      <input id="login-username" type="text" name="email" required data-msg="Por favor informe seu e-mail" class="input-material">
                      <label for="login-username" class="label-material">Seu e-mail de acesso</label>
                    </div>
                    <div class="form-group">
                      <input id="login-password" type="password" name="password" required data-msg="Por favor informe sua senha" class="input-material">
                      <label for="login-password" class="label-material">Sua senha</label>
                    </div>
                    <input type="submit" id="btn-login" href="index.html" class="btn btn-primary" value="Entrar">
                    <!-- This should be submit button but I replaced it with <a> for demo purposes-->
                <?= form_close() ?>
                <a href="#" class="forgot-pass mt-2">Esqueceu sua senha?</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="copyrights text-center">
         <p>2018 &copy; Your company. Download From <a target="_blank" href="https://templateshub.net">Templates Hub</a>.</p>
      </div>
    </div>
    <!-- JavaScript files-->
    <script src="<?= site_url('recursos/') ?>vendor/jquery/jquery.min.js"></script>
    <script src="<?= site_url('recursos/') ?>vendor/popper.js/umd/popper.min.js"> </script>
    <script src="<?= site_url('recursos/') ?>vendor/bootstrap/js/bootstrap.min.js"> </script>
    <script src="<?= site_url('recursos/') ?>vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="<?= site_url('recursos/') ?>js/front.js"></script>

  </body>
</html>