<?php
include('kernel/core.php');
?>
<!DOCTYPE html>
<html lang="<?php echo $lang['core.page.lang'] ?>">
<head>
<meta charset="<?php echo $lang['core.page.encode'] ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo $lang['core.page.description'] ?>">
<meta name="author" content="<?php echo $lang['core.page.author'] ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo URL ?>/favicon.ico">
<title><?php echo $lang['title.error.404'] ?></title>
<link href="<?php echo URL ?>/style/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo URL ?>/style/css/sidebar-nav.min.css" rel="stylesheet">
<link href="<?php echo URL ?>/style/css/animate.css" rel="stylesheet">
<link href="<?php echo URL ?>/style/css/toastr.css" rel="stylesheet">
<link href="<?php echo URL ?>/style/css/main.css" rel="stylesheet">
<link href="<?php echo URL ?>/style/css/custom.css" rel="stylesheet">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<!-- Preloader -->
<div class="preloader">
  <div class="cssload-speeding-wheel"></div>
</div>
<section id="wrapper" class="error-page">
  <div class="error-box">
    <div class="error-body text-center">
      <h1>404</h1>
      <h3 class="text-uppercase"><?php echo $lang['page.error.404.title'] ?></h3>
      <p class="text-muted m-t-30 m-b-30"><?php echo $lang['page.error.404.desc'] ?></p>
      <a href="<?php echo URL ?>" class="btn btn-info btn-rounded waves-effect waves-light m-b-40"><?php echo $lang['page.error.back'] ?></a> </div>
    <footer class="footer text-center"><?php echo date('Y'); ?>  <?php echo $lang['footer.copy'] ?></footer>
  </div>
</section>
<script src="<?php echo URL ?>/style/js/jquery.min.js"></script>
<script src="<?php echo URL ?>/style/js/bootstrap.min.js"></script>
<script src="<?php echo URL ?>/style/js/scripts.min.js"></script>

</body>
</html>
