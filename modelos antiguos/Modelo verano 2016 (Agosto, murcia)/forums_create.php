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
	<?php echo template::basehead($lang['pageMetaTitleIndex']); ?>
	<link rel="stylesheet" href="<?php echo $config['web.url'] ?>/assets/js/plugins/select2/select2.min.css">
	<link rel="stylesheet" href="<?php echo $config['web.url'] ?>/assets/js/plugins/select2/select2-bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo $config['web.url'] ?>/assets/js/plugins/jquery-tags-input/jquery.tagsinput.min.css">

</head>
<body>

<div id="page-container" class="sidebar-l sidebar-o side-scroll header-navbar-fixed <?php if(!empty($_COOKIE['onlol_sidebar']) && @$_COOKIE['onlol_sidebar'] == 'min') {echo 'sidebar-mini';} ?>">
	
	
	<?php echo template::sideBarRight(); ?>
	<?php echo template::headerNavBar($user_region); ?>
	<?php echo template::sideBar('forums'); ?>
	<!-- Start Forums -->
<main id="main-container" style="background-image: url(<?php echo $config['web.url'] ?>/assets/game/champions/splash/FiddleSticks_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
	<div class="content content-narrow">
		<ol class="breadcrumb push-15">
			<li><a class="text-muted" href="<?php echo $config['web.url'] ?>/forums"><?php echo $lang['forumsMenuTitle']; ?></a></li>
			<li><a class="link-effect" href="<?php echo $config['web.url'] ?>/forums/new"> <i class="fa fa-plus"></i> <?php echo $lang['forumsMenuTitleNewTopic']; ?></a></li>
		</ol>
		<div class="block">
			<div class="block-header bg-gray-lighter">
			<ul class="block-options">
			<li>
			<button type="button" data-toggle="block-option" data-action="fullscreen_toggle"></button>
			</li>
			<li>
			<button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
			</li>
			</ul>
			<h3 class="block-title"><i class="fa fa-plus"></i> <?php echo $lang['forumsMenuTitleNewTopic']; ?></h3>
			</div>
			<div class="block-content block-content-full block-content-narrow">
				<form class="form-horizontal push-10-t" action="base_pages_forum_new_topic.php" method="post" onsubmit="return false;">
					<div class="form-group">
						<div class="col-md-7">
							<div class="form-material form-material-primary">
								<select class="js-select2 form-control" id="topic-forum" name="topic-forum" style="width: 100%;" data-placeholder="<?php echo $lang['forumsCreateHolderCat']; ?>">
									<option></option>
									<?php
									$forumsCategories = $db->query('SELECT id,title FROM forums_categories ORDER BY order_num ASC');
									if(!empty($_GET['ref']))
									{
										if($db->query('SELECT id FROM forums_forums WHERE id='.@$_GET['ref'])->num_rows > 0)
										{
											$forumDetected = $_GET['ref'];
										}
									}
									while($cat = $forumsCategories->fetch_row())
									{
										echo '<option value="introduction" disabled>'.$lang[$cat[1]].'</option>';
										$forumsForums = $db->query('SELECT id,title FROM forums_forums WHERE cat_id='.$cat[0]);
										while($forum = $forumsForums->fetch_row())
										{
											(@$forumDetected == $forum[0]) ? $forumSelected=' selected':$forumSelected=null;
											echo '<option value="'.$forum[0].'"'.$forumSelected.'>- '.$lang[$forum[1]].'</option>';
										}
									}
									?>
								</select>
								<label for="topic-forum"><?php echo $lang['forumsCreateTitleCat']; ?> <span class="text-danger">*</span></label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-7">
							<div class="form-material form-material-primary">
								<input class="form-control" type="text" id="topic-title" name="topic-title" placeholder="<?php echo $lang['forumsCreateTitleTopicHold']; ?>">
								<label for="topic-title"><?php echo $lang['forumsCreateTitleTopic']; ?> <span class="text-danger">*</span></label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-7">
							<div class="form-material form-material-primary">
								<label class="css-input switch switch-success">
									<input class="form-control" type="checkbox" checked><span></span> <?php echo $lang['forumsCreateCommentsStatus']; ?>
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12">
							<textarea id="js-ckeditor" name="ckeditor"></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12">
							<button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-plus"></i> <?php echo $lang['forumsCreateSend']; ?></button>
							<button class="btn btn-sm btn-default" type="button"><i class="fa fa-eye"></i> <?php echo $lang['forumsCreatePrev']; ?></button>
							<button class="btn btn-sm btn-default" type="button"><i class="fa fa-floppy-o"></i> <?php echo $lang['forumsCreateSave']; ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</main>
<!-- End Forums -->
	<?php echo template::footer(); ?>
</div>
<?php echo template::scripts(); ?>
<script src="<?php echo $config['web.url'] ?>/assets/js/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo $config['web.url'] ?>/assets/js/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo $config['web.url'] ?>/assets/js/plugins/jquery-tags-input/jquery.tagsinput.min.js"></script>
<script src="<?php echo $config['web.url'] ?>/assets/js/plugins/ckeditor/ckeditor.js"></script>

<script>jQuery(function(){App.initHelpers(['ckeditor', 'select2', 'tags-inputs']);});</script>

</body>
</html>
