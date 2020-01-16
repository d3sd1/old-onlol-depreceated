<?php
require_once('../kernel/core.php');
if(empty($_GET['id']))
{
	echo '<script>
	loadurl(\'forums.php\');
	</script>';
	die();
}
else
{
	if($db->query('SELECT id FROM forums_posts WHERE id='.$_GET['id'])->num_rows == 0)
	{
		echo '<script>
		loadurl(\'forums.php\');
		</script>';
		die();
	}
}
$post_details = $db->query('SELECT id,title,time,forum_id,author_name,content FROM forums_posts WHERE id='.$_GET['id'])->fetch_row();
$forum_details = $db->query('SELECT id,title FROM forums_forums WHERE id='.$post_details[3])->fetch_row();
?>
<script>
document.title = '<?php echo $lang['pageMetaTitleForumTopic']; ?> <?php echo $post_details[1] ?>';
jQuery(function(){App.initHelpers('notify');});
jQuery(function(){App.initHelpers('slick');});
</script>

<main id="main-container"><div class="content content-narrow">
<ol class="breadcrumb push-15">
	<li><a class="text-muted" href="javascript:loadurl('forums.php')"><?php echo $lang['forumsMenuTitle']; ?></a></li>
	<li><a class="link-effect" href="javascript:loadurl('forums_cat.php?id=<?php echo $forum_details[0] ?>')"><?php echo $lang[$forum_details[1]] ?></a></li>
	<li><a class="link-effect" href="javascript:loadurl('forums_post.php?id=<?php echo $post_details[0] ?>')"><?php echo $post_details[1] ?></a></li>
</ol>
<div class="block">
<div class="block-header bg-gray-lighter">
<ul class="block-options">
<li>
<button data-toggle="scroll-to" data-target="#forum-reply-form" type="button"><i class="fa fa-reply"></i> <?php echo $lang['forumTopicAnswer'] ?></button>
</li>
<li>
<button type="button" data-toggle="block-option" data-action="fullscreen_toggle"></button>
</li>
</ul>
<h3 class="block-title"><?php echo $post_details[1] ?></h3>
</div>
<div class="block-content">
<table class="table table-striped table-borderless">
<tbody>
<tr>
<td class="hidden-xs"></td>
<td class="font-s13 text-muted">
<a href="base_pages_profile.php"><?php echo $post_details[4] ?></a> <?php echo $lang['coreTimeAgo'].' - '.core::time_elapsed($post_details[2]) ?>
</td>
</tr>
<tr>
<td class="text-center hidden-xs" style="width: 140px;">
<div class="push-10">
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar5.jpg" alt="">
</a>
</div>
<small>2 Posts</small>
</td>
<td>
<p><?php echo $post_details[5] ?></p>
<hr>
<p class="font-s13 text-muted">There is only one way to avoid criticism: do nothing, say nothing, and be nothing.</p>
</td>
</tr>
<tr>
<td class="hidden-xs"></td>
<td class="font-s13 text-muted">
<a href="base_pages_profile.php">Adam Hall</a> on May 10, 2015 - 10:09
</td>
</tr>
<tr>
<td class="text-center hidden-xs" style="width: 140px;">
<div class="push-10">
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar13.jpg" alt="">
</a>
</div>
<small>15 Posts</small>
</td>
<td>
<p>Felis ullamcorper curae erat nulla luctus sociosqu phasellus posuere habitasse sollicitudin, libero sit potenti leo ultricies etiam blandit id platea augue, erat habitant fermentum lorem commodo taciti tristique etiam curabitur suscipit lacinia habitasse amet mauris eu eget ipsum nec magna in, adipiscing risus aenean turpis proin duis fringilla praesent ornare lorem eros malesuada vitae nullam diam velit potenti consectetur, vehicula accumsan risus lectus tortor etiam facilisis tempus sapien tortor, mi vestibulum taciti dapibus viverra ac justo vivamus erat phasellus turpis nisi class praesent duis ligula, vel ornare faucibus potenti nibh turpis, at id semper nunc dui blandit. Enim et nec habitasse ultricies id tortor curabitur, consectetur eu inceptos ante conubia tempor platea odio, sed sem integer lacinia cras non risus euismod turpis platea erat ultrices iaculis rutrum taciti, fusce lobortis adipiscing dapibus habitant sodales gravida pulvinar, elementum mi tempus ut commodo congue ipsum justo nec dui cursus scelerisque elementum volutpat tellus nulla laoreet taciti, nibh suspendisse primis arcu integer vulputate etiam ligula lobortis nunc, interdum commodo libero aliquam suscipit phasellus sollicitudin arcu varius venenatis erat ornare tempor nullam donec vitae etiam tellus.</p>
<hr>
<p class="font-s13 text-muted">Be yourself; everyone else is already taken.</p>
</td>
</tr>

<tr id="forum-reply-form">
<td class="hidden-xs"></td>
<td class="font-s13 text-muted">
<a href="base_pages_profile.php">Eric Lawson</a> Just now
</td>
</tr>
<tr>
<td class="text-center hidden-xs">
<div class="push-10">
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar9.jpg" alt="">
</a>
</div>
<small>850 Posts</small>
</td>
<td>
<form class="form-horizontal" action="base_pages_forum_discussion.php" method="post" onsubmit="return false;">
<div class="form-group">
<div class="col-xs-12">
<textarea id="js-ckeditor" name="ckeditor"></textarea>
</div>
</div>
<div class="form-group">
<div class="col-xs-12">
<button class="btn btn-sm btn-primary" type="submit"><i class="fa fa-reply"></i> <?php echo $lang['forumTopicAnswer'] ?></button>
</div>
</div>
</form>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</main>
<script src="<?php echo $config['web.url'] ?>/assets/js/oneui.min-2.1.js"></script>