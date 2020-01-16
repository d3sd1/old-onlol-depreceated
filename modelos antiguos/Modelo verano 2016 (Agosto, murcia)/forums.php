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
</head>
<body>

<div id="page-container" class="sidebar-l sidebar-o side-scroll header-navbar-fixed <?php if(!empty($_COOKIE['onlol_sidebar']) && @$_COOKIE['onlol_sidebar'] == 'min') {echo 'sidebar-mini';} ?>">
	
	
	<?php echo template::sideBarRight(); ?>
	<?php echo template::headerNavBar($user_region); ?>
	<?php echo template::sideBar('forums'); ?>
	<!-- Start Forums -->
<main id="main-container" style="background-image: url(<?php echo $config['web.url'] ?>/assets/game/champions/splash/blitzcrank_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">

<div class="content content-narrow">
	<div class="block">
		<div class="block-header bg-gray-lighter">
			<ul class="block-options">
			<li>
				<button type="button"><a href="<?php echo $config['web.url'] ?>/forums.php"><i class="si si-refresh"></i></a></button>
			</li>
			</ul>
			<h3 class="block-title"><?php echo $lang['forumsTitle']; ?></h3>
		</div>
		<div class="block-content block-content-full">
			<?php
			$forums_cats = $db->query('SELECT id,title FROM forums_categories ORDER BY order_num ASC');
			while($forum_categories = $forums_cats->fetch_row())
			{
				echo '<table class="table table-striped table-borderless table-vcenter">
				<thead>
					<tr>
						<th colspan="2">'.$lang[$forum_categories[1]].'</th>
						<th class="text-center hidden-xs hidden-sm" style="width: 100px;">'.$lang['forumsDetailsTopics'].'</th>
						<th class="text-center hidden-xs hidden-sm" style="width: 100px;">'.$lang['forumsDetailsPosts'].'</th>
						<th class="hidden-xs hidden-sm" style="width: 200px;">'.$lang['forumsDetailsLastpost'].'</th>
					</tr>
				</thead>
				<tbody>
				';
				$forums_forums = $db->query('SELECT id,title,description,icon,topics,posts,last_post FROM forums_forums WHERE cat_id='.$forum_categories[0].' ORDER BY order_num ASC');
				while($forum_details = $forums_forums->fetch_row())
				{
					echo '
					<tr>
						<td class="text-center" style="width: 75px;">
							<i class="'.$forum_details[3].' fa-2x"></i>
						</td>
						<td>
							<h4 class="h5 font-w600 push-5">
								<a href="'.$config['web.url'].'/forums/'.$forum_details[0].'">'.$lang[$forum_details[1]].'</a>
							</h4>
							<div class="font-s13 text-muted">'.$lang[$forum_details[2]].'</div>
						</td>
						<td class="text-center hidden-xs hidden-sm">
							<a class="font-w600" href="javascript:void(0)">'.$forum_details[4].'</a>
						</td>
						<td class="text-center hidden-xs hidden-sm">
							<a class="font-w600" href="javascript:void(0)">'.$forum_details[5].'</a>
						</td>';
						if($forum_details[6] != NULL)
						{
							echo '<td class="hidden-xs hidden-sm">
								<span class="font-s13">'.$forum_details[6].'</span>
							</td>';
						}
						else
						{
							echo '<td class="hidden-xs hidden-sm">
								<span class="font-s13">'.$lang['forumsDetailsNoPosts'].'</span>
							</td>';
						}
					echo '</tr>';
				}
				echo '
				</tbody>
			</table>
			';
			}
			?>
		</div>
	</div>
</div>
</main>
	<!-- End Forums -->
	<?php echo template::footer(); ?>
</div>
<?php echo template::scripts(); ?>
</body>
</html>