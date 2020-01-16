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
	<?php echo template::sideBar('start'); ?>
	<!-- Start index -->
	<main id="main-container">
		<div class="content bg-image overflow-hidden" style="background-image: url('assets/img/backgrounds/index.jpg');">
			<div class="push-50-t push-15">
				<h1 class="h2 text-white animated zoomIn" style="visibility:hidden">ONLoL</h1>
				<h2 class="h5 text-white-op animated zoomIn" style="visibility:hidden">ONLoL</h2>
			</div>
		</div>
		<div class="content bg-white border-b">
			<div class="row items-push text-uppercase">
				<div class="col-xs-6 col-sm-3">
					<div class="font-w700 text-gray-darker animated fadeIn"><?php echo $lang['indexHeadSummoners']; ?></div>
					<div class="text-muted animated fadeIn"><small><i class="fa fa-user"></i> <?php echo $lang['indexHeadSummonersAnalized']; ?></small></div>
					<a class="h2 font-w300 text-primary animated flipInX"><?php echo $db->query('SELECT id FROM lol_summoners')->num_rows ?></a>
				</div>
				<div class="col-xs-6 col-sm-3">
					<div class="font-w700 text-gray-darker animated fadeIn"><?php echo $lang['indexHeadMatches']; ?></div>
					<div class="text-muted animated fadeIn"><small><i class="fa fa-gamepad"></i> <?php echo $lang['indexHeadMatchesAnalized']; ?></small></div>
					<a class="h2 font-w300 text-primary animated flipInX"><?php echo $db->query('SELECT id FROM lol_matches')->num_rows ?></a>
				</div>
				<div class="col-xs-6 col-sm-3">
					<div class="font-w700 text-gray-darker animated fadeIn"><?php echo $lang['indexHeadForum']; ?></div>
					<div class="text-muted animated fadeIn"><small><i class="fa fa-book"></i> <?php echo $lang['indexHeadForumPosts']; ?></small></div>
					<a class="h2 font-w300 text-primary animated flipInX"><?php echo $db->query('SELECT id FROM forums_posts')->num_rows ?></a>
				</div>
				<div class="col-xs-6 col-sm-3">
					<div class="font-w700 text-gray-darker animated fadeIn"><?php echo $lang['webLoLVersion']; ?></div>
					<div class="text-muted animated fadeIn"><small><i class="si si-calendar"></i> <?php echo $lang['webLoLVersionDsc']; ?></small></div>
					<a class="h2 font-w300 text-primary animated flipInX" href="base_comp_charts.php"><?php echo $db->query('SELECT MAX(value) FROM web_versions')->fetch_row()[0] ?></a>
				</div>
			</div>
		</div>

		<div class="content">
			<div class="row">
				<div class="col-lg-8">
					<div class="block block-themed">
						<div class="block-header bg-primary">
							<ul class="block-options">
								<li>
									<button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo" onclick="loadurl('index.php')"><i class="si si-refresh"></i></button>
								</li>
							</ul>
							<h3 class="block-title"><?php echo $lang['homeRecentActivity'] ?></h3>
						</div>
						<div class="block-content">
							<ul class="list list-timeline pull-t">
							<?php
							if($db->query('SELECT id FROM web_activity')->num_rows > 0)
							{
								$webVersionActivity = $db->query('SELECT type,web_version,time FROM web_activity ORDER BY time DESC');
								while($row = $webVersionActivity->fetch_row())
								{
									switch($row[0])
									{
										case 'systemUpdated':
										$activityIcon = 'fa fa-cog';
										$activityBg = 'bg-primary-dark';
										break;
										case 'chartUpdated':
										$activityIcon = 'fa fa-line-chart';
										$activityBg = 'bg-smooth';
										break;
										case 'newChamp':
										$activityIcon = 'fa fa-user-plus';
										$activityBg = 'bg-info';
										break;
										case 'rankingsUpdated':
										$activityIcon = 'glyphicon glyphicon-list-alt';
										$activityBg = 'bg-flat';
										break;
										default:
										$activityIcon = 'glyphicon glyphicon-list-alt';
										$activityBg = 'bg-flat';
										break;
									}
									echo '<li>
									<div class="list-timeline-time">'.str_replace('{{time}}',core::time_elapsed($row[2]/ 1000),$lang['startActivityTimeElapsed']).'</div>
									<i class="'.$activityIcon.' list-timeline-icon '.$activityBg.'"></i>
									<div class="list-timeline-content">
									<p class="font-w600">'.$lang[$row[0]].'</p>
									<p class="font-s13">'.str_replace('{{ver}}','<b>'.$row[1].'</b>',$lang[$row[0].'Dsc']).'</p>
									</div>
									</li>';
								}
							}
							else
							{
								echo '<li>
									<div class="list-timeline-time">'.$lang['coreTimeNow'].'</div>
									<i class="fa fa-cog fa-spin list-timeline-icon bg-primary-dark"></i>
									<div class="list-timeline-content">
									<p class="font-w600">'.$lang['webActivityUpdating'].'</p>
									<p class="font-s13">'.$lang['webActivityUpdatingDsc'].'</p>
									</div>
									</li>';
							}
							?>
							</ul>
						</div>
					</div>
				</div>

			<div class="col-lg-4">
				<div class="block">
				<div class="block-header">
				<ul class="block-options">
				<li>
				<button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
				</li>
				</ul>
				<h3 class="block-title"><?php echo $lang['chartTeamsWinRate'] ?></h3>
				</div>
				<div class="block-content block-content-full">
				<div class="teamsWinRate" style="height: 330px;"></div>
				</div>
				</div>
			</div>
		</div>
	</main>
	<!-- End index -->
	<?php echo template::footer(); ?>
</div>
<?php echo template::scripts(); 
/* Notifications */
if(!empty(@$_GET['notify']))
{
	if($_GET['notify'] == 'error_regionnotset')
	{
		echo '<script>$.notify("'.$lang['RegionNotFound'].'", "error");</script>';
	}
	if($_GET['notify'] == 'error_summonernotset')
	{
		echo '<script>$.notify("'.$lang['SummonerNotSet'].'", "error");</script>';
	}
	if($_GET['notify'] == 'error_summonernotfoundbyregion')
	{
		echo '<script>$.notify("'.str_replace(array('{{name}}','{{region}}'),array(@$_GET['summoner'],strtoupper(@$_GET['region'])),$lang['summonerNotFound']).'", "error");</script>';
	}
}
?>
<script src="assets/js/plugins/sparkline/jquery.sparkline.min.js"></script>
<script src="assets/js/plugins/easy-pie-chart/jquery.easypiechart.min.js"></script>
<script src="assets/js/plugins/chartjs/Chart.min.js"></script>
<script src="assets/js/plugins/flot/jquery.flot.min.js"></script>
<script src="assets/js/plugins/flot/jquery.flot.pie.min.js"></script>
<script src="assets/js/plugins/flot/jquery.flot.stack.min.js"></script>
<script src="assets/js/plugins/flot/jquery.flot.resize.min.js"></script>
<script>
var BaseCompCharts=function() {
    
    i=function() {
        var a=jQuery(".teamsWinRate");
        jQuery.plot(a, [ {
            label: "<?php echo $lang['chartTeamsWinRateBlue'] ?>", data: <?php echo $db->query('SELECT value FROM web_charts_base WHERE name="winRateBySideBlue"')->fetch_row()[0]; ?>
        }
        , {
            label: "<?php echo $lang['chartTeamsWinRateRed'] ?>", data: <?php echo $db->query('SELECT value FROM web_charts_base WHERE name="winRateBySideRed"')->fetch_row()[0]; ?>
        }
        ], {
            colors:["#0085CA","#860038"], legend: {
                show: !1
            }
            , series: {
                pie: {
                    show:!0, radius:1, label: {
                        show:!0, radius:2/3, formatter:function(o, t) {
                            return'<div class="flot-pie-label">'+o+"<br>"+Math.round(t.percent)+"%</div>"
                        }
                        , background: {
                            opacity: .75, color: "#000000"
                        }
                    }
                }
            }
        }
        )
    };
    return {
        init:function() {
            i()
        }
    }
}();
jQuery(function() {
    BaseCompCharts.init()
}
);</script>

</body>
</html>