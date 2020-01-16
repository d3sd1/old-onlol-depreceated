<?php
require('kernel/core.php');
if(empty($_GET['id']))
{
	die(header('Location: '.$config['web.url'].'/forums'));
}
else
{
	if($db->query('SELECT id FROM forums_forums WHERE id='.$_GET['id'])->num_rows == 0)
	{
		die(header('Location: '.$config['web.url'].'/forums'));
	}
}
$forum_details = $db->query('SELECT id,title FROM forums_forums WHERE id='.$_GET['id'])->fetch_row();
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
<main id="main-container" style="background-image: url(<?php echo $config['web.url'] ?>/assets/game/champions/splash/Draven_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;"><div class="content content-narrow">
	<ol class="breadcrumb push-15">
		<li><a class="text-muted" href="<?php echo $config['web.url'] ?>/forums"><?php echo $lang['forumsMenuTitle']; ?></a></li>
		<li><a class="link-effect" href="<?php echo $config['web.url'].'/forums/'.$forum_details[0] ?>"><?php echo $lang[$forum_details[1]] ?></a></li>
	</ol>
	<div class="block">
	<div class="block-header bg-gray-lighter">
		<ul class="block-options">
			<li>
				<button type="button"><a href="<?php echo $config['web.url'].'/forums/'.$forum_details[0] ?>')"><i class="si si-refresh"></i></a></button>
			</li>
		</ul>
		<ul class="block-options block-options-left">
			<li>
				<a href="<?php echo $config['web.url'] ?>/forums/new?ref=<?php echo $forum_details[0] ?>"><i class="fa fa-plus"></i> <?php echo $lang['forumsNewTopic']; ?></a>
			</li>
		</ul>
	</div>
	<div class="block-content">
	<table class="table table-striped table-borderless table-vcenter">
	<thead>
		<tr>
			<th colspan="2"><?php echo $lang[$forum_details[1]] ?></th>
			<th class="text-center hidden-xs hidden-sm" style="width: 100px;"><?php echo $lang['forumsDetailsViews'] ?></th>
			<th class="text-center hidden-xs hidden-sm" style="width: 100px;"><?php echo $lang['forumsDetailsComments'] ?></th>
			<th class="hidden-xs hidden-sm" style="width: 200px;"><?php echo $lang['forumsDetailsLastcomment'] ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
			$forums_posts = $db->query('SELECT id,title,fixed,author_name,time,views,comments,last_comment_by,last_comment_time FROM forums_posts WHERE forum_id='.$forum_details[0].' ORDER BY time DESC');
			while($forum_postinfo = $forums_posts->fetch_row())
			{
				echo '<tr>';
				if($forum_postinfo[2] == true)
				{	
					echo '<td class="text-center" style="width: 75px;">
							<i class="si si-pin fa-2x"></i>
						</td>
						<td>
							<h4 class="h5 font-w600 push-5">
								<a href="'.$config['web.url'].'/forums/topic/'.$forum_postinfo[0].'">'.$forum_postinfo[1].'</a>
							</h4>
							<div class="font-s13 text-muted">
								'.$forum_postinfo[3].' - <em>'.$lang['coreTimeAgo'].' '.core::time_elapsed($forum_postinfo[4]).'</em>
							</div>
						</td>';
				}
				else{
					echo '<td colspan="2">
						<h4 class="h5 font-w600 push-5">
							<a href="'.$config['web.url'].'/forums/topic/'.$forum_postinfo[0].'">'.$forum_postinfo[1].'</a>
						</h4>
						<div class="font-s13 text-muted">
							'.$forum_postinfo[3].' - <em>'.$lang['coreTimeAgo'].' '.core::time_elapsed($forum_postinfo[4]).'</em>
						</div>
					</td>';
				}
		
		echo '
		<td class="text-center hidden-xs hidden-sm">
			<a class="font-w600" href="javascript:void(0)">'.$forum_postinfo[5].'</a>
		</td>
		<td class="text-center hidden-xs hidden-sm">
			<a class="font-w600" href="javascript:void(0)">'.$forum_postinfo[6].'</a>
		</td>';
		if($forum_postinfo[7] != NULL)
		{
			echo '<td class="hidden-xs hidden-sm">
				<span class="font-s13">'.$lang['forumsDetailsBy'].' '.$forum_postinfo[7].' '.$lang['coreTimeAgo'].' '.core::time_elapsed($forum_postinfo[8]).'</span>
			</td>';
		}
		else
		{
			echo '<td class="hidden-xs hidden-sm">
				<span class="font-s13">'.$lang['forumsDetailsNoComments'].'</span>
			</td>';
		}
	echo '</tr>';
			}
			?>
	
	</tbody>
	</table>
	<nav class="text-right">
	<ul class="pagination">
	<li class="active">
	<a href="javascript:void(0)">1</a>
	</li>
	<li>
	<a href="javascript:void(0)">2</a>
	</li>
	<li class="disabled">
	<span>...</span>
	</li>
	<li>
	<a href="javascript:void(0)">11</a>
	</li>
	<li>
	<a href="javascript:void(0)">12</a>
	</li>
	<li>
	<a href="javascript:void(0)"><i class="fa fa-angle-right"></i></a>
	</li>
	<li>
	<a href="javascript:void(0)"><i class="fa fa-angle-double-right"></i></a>
	</li>
	</ul>
	</nav>
	</div>
	</div>
	</div>
</main><!-- End Forums -->
	<?php echo template::footer(); ?>
</div>
<?php echo template::scripts(); ?>
</body>
</html>