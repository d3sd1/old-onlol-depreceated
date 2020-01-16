<?php
$dont_check_session = true;
require('core.php');
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ONLoL ~ Panel</title>
    <!-- PACE-->
    <link rel="stylesheet" type="text/css" href="plugins/PACE/themes/blue/pace-theme-flash.css">
    <script type="text/javascript" src="plugins/PACE/pace.min.js"></script>
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="plugins/bootstrap/dist/css/bootstrap.min.css">
    <!-- Fonts-->
    <link rel="stylesheet" type="text/css" href="plugins/themify-icons/themify-icons.css">
    <!-- Primary Style-->
    <link rel="stylesheet" type="text/css" href="build/css/umega.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries-->
    <!-- WARNING: Respond.js doesn't work if you view the page via file://--> 
    <!--[if lt IE 9]>
    <script type="text/javascript" src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script type="text/javascript" src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="user-page">
    <h1 class="fw-600 mt-0 mb-20">Panel</h1>
	<?php
	if(!empty($_POST['username']) AND !empty($_POST['password']))
	{
		if($db->query('SELECT id FROM admin_users WHERE username="'.$_POST['username'].'" AND password="'.md5($_POST['password']).'"')->num_rows > 0)
		{
			$_SESSION['onlol_adminpanel_logged'] = true;
			$_SESSION['onlol_adminpanel_logged_user'] = $_POST['username'];
			admin::redirect('index.php');
		}
		else
		{
			echo '<h2 style="color:red;">Error de conexión</h2>';
		}
	}
  ?>
    <form method="post" action="login.php" class="form-horizontal">
      <div class="form-group has-feedback">
        <div class="col-xs-12">
          <input type="text" aria-describedby="exampleInputEmail" name="username" required placeholder="Usuario" class="form-control rounded"><span aria-hidden="true" class="ti-user form-control-feedback"></span><span id="exampleInputEmail" class="sr-only">(default)</span>
        </div>
      </div>
      <div class="form-group has-feedback">
        <div class="col-xs-12">
          <input type="password" aria-describedby="exampleInputPassword" name="password" required placeholder="Contraseña" class="form-control rounded"><span aria-hidden="true" class="ti-key form-control-feedback"></span><span id="exampleInputPassword" class="sr-only">(default)</span>
        </div>
      </div>
      <button type="submit" class="btn btn-lg btn-success btn-raised btn-block">Conectar</button>
    </form>
    <!-- jQuery-->
    <script type="text/javascript" src="plugins/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap JavaScript-->
    <script type="text/javascript" src="plugins/bootstrap/dist/js/bootstrap.min.js"></script>
  </body>
</html>