<?php
require('kernel/core.php');
?><!DOCTYPE html>
<!--[if IE 9]>         <html class="ie9 no-focus"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-focus"> <!--<![endif]-->
<head>
<meta charset="utf-8">
	<title><?php echo $lang['pageMetaTitleJSoff'] ?></title>
	<meta charset="utf-8">
	<title><?php echo $lang['pageMetaTitleIndex']; ?></title>
	<meta name="description" content="<?php echo $lang['pageMetaDescription']; ?>">
	<meta name="author" content="<?php echo $lang['pageMetaAuthor']; ?>">
	<meta name="robots" content="noindex, nofollow">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
	<link rel="shortcut icon" href="<?php echo $config['web.url'] ?>/favicon.ico">
	<link rel="icon" type="image/png" href="<?php echo $config['web.url'] ?>/favicon.ico" sizes="16x16">
	<link rel="icon" type="image/png" href="<?php echo $config['web.url'] ?>/favicon.ico" sizes="32x32">
	<link rel="icon" type="image/png" href="<?php echo $config['web.url'] ?>/favicon.ico" sizes="96x96">
	<link rel="icon" type="image/png" href="<?php echo $config['web.url'] ?>/favicon.ico" sizes="160x160">
	<link rel="icon" type="image/png" href="<?php echo $config['web.url'] ?>/favicon.ico" sizes="192x192">
	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $config['web.url'] ?>/favicon.ico">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $config['web.url'] ?>/favicon.ico">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $config['web.url'] ?>/favicon.ico">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $config['web.url'] ?>/favicon.ico">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $config['web.url'] ?>/favicon.ico">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $config['web.url'] ?>/favicon.ico">
	<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $config['web.url'] ?>/favicon.ico">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $config['web.url'] ?>/favicon.ico">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $config['web.url'] ?>/favicon.ico">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">
	<link rel="stylesheet" href="<?php echo $config['web.url'] ?>/assets/js/plugins/slick/slick.min.css">
	<link rel="stylesheet" href="<?php echo $config['web.url'] ?>/assets/js/plugins/slick/slick-theme.min.css">
	<link rel="stylesheet" href="<?php echo $config['web.url'] ?>/assets/css/bootstrap.min-1.4.css">
	<link rel="stylesheet" id="css-main" href="<?php echo $config['web.url'] ?>/assets/css/oneui.min-2.1.css">
</head>
<body style="background-image: url(<?php echo $config['web.url'] ?>/assets/game/champions/splash/thresh_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
	<div class="content bg-white text-center pulldown overflow-hidden" style="opacity:0.95">
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">
			<div class="font-s64 text-gray push-30-t push-50">
			<i class="fa fa-cog fa-spin"></i>
			</div>
			<h1 class="h2 font-w400 push-15 animated fadeInLeftBig"><?php echo $lang['JSoffPageTitle'] ?></h1>
			<h2 class="h3 font-w300 text-dark-op push-50 animated fadeInRightBig"><?php echo $lang['JSoffPageDesc'] ?> <br><br> <a href="<?php echo $config['web.url'] ?>"><button class="btn btn-minw btn-square btn-primary" type="button"><?php echo $lang['JSoffPageButtonSolved']; ?></button></a></h2>
			
			</div>
		</div>
	</div>
	<div class="pulldown push-10-t text-center animated fadeInUp">
		<small class="text-muted font-w600"><span class="js-year-copy"></span> <?php echo $lang['footerCopy']; ?> &copy; 2015 - <?php echo date('Y'); ?> ~ <?php echo $lang['footerCopyOwnerCreate']; ?> <i class="fa fa-heart text-city"></i> <?php echo $lang['footerCopyOwner']; ?></small>
	</div>
	<script src="assets/js/oneui.min-2.1.js"></script>
</body>
</html>