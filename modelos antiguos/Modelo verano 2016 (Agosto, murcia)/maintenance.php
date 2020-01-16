<?php
require('kernel/core.php');
?>
<!DOCTYPE html>
<!--[if IE 9]>
	<html class="ie9 no-focus">
<![endif]-->
<!--[if gt IE 9]><!-->
	<html class="no-focus"> 
<!--<![endif]-->
<head>
	<?php echo template::basehead($lang['pageMetaTitleMaintenance']); ?>
	<meta http-equiv="refresh" content="30; url=<?php echo $_GET['callback'] ?>" />
</head>
<body>

<div id="page-container" class="sidebar-l sidebar-o side-scroll header-navbar-fixed <?php if(!empty($_COOKIE['onlol_sidebar']) && @$_COOKIE['onlol_sidebar'] == 'min') {echo 'sidebar-mini';} ?>">
	
	
	<?php echo template::sideBarRight(); ?>
	<?php echo template::headerNavBar($user_region); ?>
	<?php echo template::sideBar(null); ?>
	<!-- Start Maintenance -->

<main id="main-container" style="background-image: url(<?php echo $config['web.url'] ?>/assets/game/champions/splash/thresh_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
	<div class="content bg-white text-center pulldown overflow-hidden" style="opacity:0.95;height:100%;">
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">
				<div class="font-s64 text-gray push-30-t push-50">
					<i class="fa fa-cog fa-spin"></i>
				</div>
				<h1 class="h2 font-w400 push-15 animated fadeInLeftBig"><?php echo $lang['maintenanceHeadTitle'] ?></h1>
				<h2 class="h3 font-w300 text-dark-op push-50 animated fadeInRightBig"><?php if(empty($_GET['riotapi'])) { echo $lang['maintenanceHeadDsc']; } else { echo $lang['maintenanceHeadDscApiError']; } ?></h2>
			</div>
		</div>
	</div>
</main>
	<!-- End Maintenance -->
	<?php echo template::footer(); ?>
</div>
<?php echo template::scripts(); ?>
<script src="<?php echo $config['web.url'] ?>/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>

</body>
</html>