<?php 
require('../kernel/core.php');
$region = lol::parseserver($_GET['region']);
/* Check profile at first */
if($db->query('SELECT id FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->num_rows == 0 OR $db->query('SELECT revision_date FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->fetch_row()[0] == 0)
{
	reload::basicdata($_GET['summoner'],$region);
	$s_data = $db->query('SELECT summoner_id,revision_date FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->fetch_row();
	$summoner_id = $s_data[0];
	reload::leagues($summoner_id,$region);
	reload::recent_matches($summoner_id,$region);
	reload::champ_mastery($summoner_id,$region);
	$profile_status_reloaded = 'reloaded';
}
else
{
	$summoner_id = $db->query('SELECT summoner_id FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->fetch_row()[0];
	if(!empty($_GET['reload']) AND $db->query('SELECT last_update FROM lol_summoners WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->fetch_row()[0] < (time()-onlol::timing('profile_reload_interval')) OR $db->query('SELECT last_update FROM lol_summoners WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->fetch_row()[0] < (time()-onlol::timing('profile_reload_interval')))
	{
		$summoner_revision_date_before = (int) $db->query('SELECT revision_date FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->fetch_row()[0];
		reload::basicdata($_GET['summoner'],$region);
		$summoner_revision_date_after = (int) $db->query('SELECT revision_date FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->fetch_row()[0];
		if(!empty($summoner_revision_date_before))
		{
			if($summoner_revision_date_before != $summoner_revision_date_after)
			{
				reload::recent_matches($summoner_id,$region);
				$profile_status_reloaded = 'reloaded';
			}
			else
			{
				$db->query('UPDATE lol_summoners SET last_update='.time().' WHERE region="'.$region.'" AND summoner_id='.$summoner_id);
				$profile_status_reloaded = 'reloaded';
			}
		}
		else
		{
			reload::recent_matches($summoner_id,$region);
			$profile_status_reloaded = 'reloaded';
		}
	}
	else
	{
		$profile_status_reloaded = 'not_reloaded';
	}
}
/* Summoner data variables */
$summoner_info = $db->query('SELECT summoner_id,name,icon,level,revision_date,last_update FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->fetch_row();
$summoner_id = $summoner_info[0];
$summoner_name = $summoner_info[1];
$summoner_icon = $summoner_info[2];
$summoner_level = $summoner_info[3];
$summoner_revision = $summoner_info[4];
$summoner_lastupdate = $summoner_info[5];
if($db->query('SELECT id FROM lol_summoners_leagues WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'"')->num_rows > 0)
{
	$summoner_league = $db->query('SELECT tier,division,lp,wins,losses,miniseries,miniseries_progress,mmr FROM lol_summoners_leagues WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'"')->fetch_row();
	$summoner_tier = $summoner_league[0];
	$summoner_division = $summoner_league[1];
	$summoner_lp = $summoner_league[2];
	$summoner_wins = $summoner_league[3];
	$summoner_losses = $summoner_league[4];
	$summoner_miniseries = $summoner_league[5];
	$summoner_miniseries_progress = $summoner_league[6];
	$summoner_mmr = $summoner_league[7];
}
else //Is unranked
{
	$summoner_tier = 'U';
	$summoner_division = 'I';
	$summoner_lp = 0;
	$summoner_wins = 0;
	$summoner_losses = 0;
	$summoner_miniseries = 'false';
	$summoner_miniseries_progress = 'N/A';
	$summoner_mmr = 0;
}
check::ingame($summoner_id,$region);
/* Log of searchs */
if(@$summoner_id != 0 or !empty($summoner_id))
{
	onlol::addsearchlog($summoner_name,$region,$summoner_icon);
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- START @HEAD -->
    <head>
        <?php echo template::meta('profile_title'); ?>
		<title><?php echo lang::trans('profile_title').' '.$summoner_name ?></title>
        <!-- START @FAVICONS -->
        <link href="<?php echo URL ?>/style/favicons/144x144.png" rel="apple-touch-icon-precomposed" sizes="144x144">
        <link href="<?php echo URL ?>/style/favicons/114x114.png" rel="apple-touch-icon-precomposed" sizes="114x114">
        <link href="<?php echo URL ?>/style/favicons/72x72.png" rel="apple-touch-icon-precomposed" sizes="72x72">
        <link href="<?php echo URL ?>/style/favicons/57x57.png" rel="apple-touch-icon-precomposed">
        <link href="<?php echo URL ?>/style/favicons/favicon.png" rel="shortcut icon">
        <!--/ END FAVICONS -->

        <!-- START @FONT STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700" rel="stylesheet">
        <link href="http://fonts.googleapis.com/css?family=Oswald:700,400" rel="stylesheet">
        <!--/ END FONT STYLES -->

        <!-- START @GLOBAL MANDATORY STYLES -->
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <!--/ END GLOBAL MANDATORY STYLES -->
		<script>
		var lang_exit_txt_title = "<?php echo lang::trans('web_exit_title') ?>";
		var lang_exit_txt = "<?php echo lang::trans('web_exit_txt') ?>";
		var lang_exit_y = "<?php echo lang::trans('web_exit_y') ?>";
		var lang_exit_n = "<?php echo lang::trans('web_exit_n') ?>";
		</script>
        <!-- START @PAGE LEVEL STYLES -->
        
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/fontawesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/animate.css/animate.min.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/jquery.gritter/css/jquery.gritter.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/fuelux/dist/css/fuelux.min.css" rel="stylesheet">
		<link href="<?php echo URL ?>/style/global/css/cursor.css" rel="stylesheet">
		
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/datatables/css/dataTables.bootstrap.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/datatables/css/datatables.responsive.css" rel="stylesheet">
        <!--/ END PAGE LEVEL STYLES -->
		<?php if($summoner_tier != 'U')
		{
			echo '<style>
			.ranking_border{
				-moz-border-image: url('.URL.'/game/borders/'.$summoner_tier.'.png) 2 2 2 2 stretch stretch;
				-webkit-border-image: url('.URL.'/game/borders/'.$summoner_tier.'.png) 2 2 2 2 stretch stretch;
				border-image-source: url('.URL.'/game/borders/'.$summoner_tier.'.png) 2 2 2 2 stretch stretch;
				width: 151px;
				height: 150px;
			}
			</style>';
		}
		?>
        <!-- START @THEME STYLES -->
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery/dist/jquery.min.js"></script>
        <link href="<?php echo URL ?>/style/home/css/reset.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/home/css/layout.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/home/css/components.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/home/css/plugins.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/home/css/themes/default.theme.css" rel="stylesheet" id="theme">
        <link href="<?php echo URL ?>/style/home/css/pages/dashboard-retail.css" rel="stylesheet">
        <!--/ END THEME STYLES -->

        <!-- START @IE SUPPORT -->
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/html5shiv/dist/html5shiv.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/respond-minmax/dest/respond.min.js"></script>
        <![endif]-->
        <!--/ END IE SUPPORT -->
    </head>
    <!--/ END HEAD -->

    <!--

    |=========================================================================================================================|
    |  TABLE OF CONTENTS (Use search to find needed section)                                                                  |
    |=========================================================================================================================|
    |  01. @HEAD                        |  Container for all the head elements                                                |
    |  02. @META SECTION                |  The meta tag provides metadata about the HTML document                             |
    |  03. @FAVICONS                    |  Short for favorite icon, shortcut icon, website icon, tab icon or bookmark icon    |
    |  04. @FONT STYLES                 |  Font from google fonts                                                             |
    |  05. @GLOBAL MANDATORY STYLES     |  The main 3rd party plugins css file                                                |
    |  06. @PAGE LEVEL STYLES           |  Specific 3rd party plugins css file                                                |
    |  07. @THEME STYLES                |  The main theme css file                                                            |
    |  08. @IE SUPPORT                  |  IE support of HTML5 elements and media queries                                     |
    |=========================================================================================================================|
    |  09. @BODY                        |  Contains all the contents of an HTML document                                      |
    |  10. @WRAPPER                     |  Wrapping page section                                                              |
    |  11. @HEADER                      |  Header page section contains about logo, top navigation, notification menu         |
    |  12. @SIDEBAR LEFT                |  Sidebar page section contains all sidebar menu left                                |
    |  13. @PAGE CONTENT                |  Contents page section contains breadcrumb, content page, footer page               |
    |  14. @SIDEBAR RIGHT               |  Sidebar page section contains all sidebar menu right                               |
    |  15. @BACK TOP                    |  Element back to top and action                                                     |
    |=========================================================================================================================|
    |  16. @CORE PLUGINS                |  The main 3rd party plugins script file                                             |
    |  17. @PAGE LEVEL PLUGINS          |  Specific 3rd party plugins script file                                             |
    |  18. @PAGE LEVEL SCRIPTS          |  The main theme script file                                                         |
    |=========================================================================================================================|

    START @BODY
    |=========================================================================================================================|
	|  TABLE OF CONTENTS (Apply to body class)                                                                                |
	|=========================================================================================================================|
    |  01. page-boxed                   |  Page into the box is not full width screen                                         |
	|  02. page-header-fixed            |  Header element become fixed position                                               |
	|  03. page-sidebar-fixed           |  Sidebar element become fixed position with scroll support                          |
	|  04. page-sidebar-minimize        |  Sidebar element become minimize style width sidebar                                |
	|  05. page-footer-fixed            |  Footer element become fixed position with scroll support on page content           |
	|  06. page-sound                   |  For playing sounds on user actions and page events                                 |
	|=========================================================================================================================|

	-->
    <body class="page-session page-sound page-header-fixed page-sidebar-fixed">

        <!--[if lt IE 9]>
        <p class="upgrade-browser">Upps!! You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/" target="_blank">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- START @WRAPPER -->
        <section id="wrapper">

            <?php echo template::nav_top() ?>
            <?php echo template::left_menu('profile') ?>

            <!-- START @PAGE CONTENT -->
            <section id="page-content" >
                <!-- Start body content -->
				<?php
				$main_champ_id = $db->query('SELECT main_champ_id FROM lol_summoners_champmastery WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->fetch_row()[0];
				if($main_champ_id != 0)
				{
					$main_champ = lol::champ_id2key($main_champ_id);
				}
				else
				{
					$main_champ = 0;
				}
				?>
                <div class="body-content animated fadeIn" style="background-image: url(<?php echo URL ?>/game/champions/<?php echo $main_champ ?>/splash/0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">

                    <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="profile-cover" style="background-color:rgba(255,255,255,0.2);">
                        <div class="cover rounded shadow no-overflow">
                            <div class="inner-cover"  style="height:200px">
								<div class="col-lg-1 col-md-2 col-sm-1">
									<div class="ranking_border"><img draggable="false" src="<?php echo URL ?>/game/icons/<?php echo $summoner_icon ?>.png" style="width:128px; height:128px; margin-top:10.5px; margin-left:10px;"></div>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2" style="margin-left: 50px; margin-top:40px; text-align:center;">
									<div class="alert alert-default" style="color:black;">
									<?php 
									$size = 2;
									if(strlen($summoner_name) >= 14)
									{
										$size = 3;
									}
									if(strlen($summoner_name) >= 17)
									{
										$size = 4;
									}
									?>
                                        <h<?php echo $size ?> style="margin-top:4px"><?php echo $summoner_name ?></h<?php echo $size ?>>
                                    </div>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2" style="margin-top:40px; text-align:center;">
								<?php
								if($summoner_mmr > 0)
								{
									if($summoner_mmr >= lol::division_mmr($summoner_tier.'_'.$summoner_division) && $summoner_mmr >= lol::division_mmr(lol::tier_oneless($summoner_tier,$summoner_division)))
									{
										$mmr_status = 'success';
										$mmr_msg = lang::trans('profile_mmr_normal');
									}
									if($summoner_mmr < lol::division_mmr($summoner_tier.'_'.$summoner_division) && $summoner_mmr > lol::division_mmr(lol::tier_oneless($summoner_tier,$summoner_division)))
									{
										$mmr_status = 'warning';
										$mmr_msg = lang::trans('profile_mmr_low');
									}
									if($summoner_mmr < lol::division_mmr($summoner_tier.'_'.$summoner_division) && $summoner_mmr < lol::division_mmr(lol::tier_oneless($summoner_tier,$summoner_division)))
									{
										$mmr_status = 'danger';
										$mmr_msg = lang::trans('profile_mmr_elohell');
									}
									if($summoner_mmr > lol::division_mmr($summoner_tier.'_'.$summoner_division) && $summoner_mmr > lol::division_mmr(lol::tier_onemore($summoner_tier,$summoner_division)))
									{
										$mmr_status = 'teals';
										$mmr_msg = lang::trans('profile_mmr_high');
									}
									if(empty($mmr_status))
									{
										$mmr_status = 'success';
										$mmr_msg = lang::trans('profile_mmr_normal');
									}
								}
								else
								{
									$mmr_status = 'lilac';
									$mmr_msg = lang::trans('profile_mmr_nommr');
								}
								?>
									<div class="alert alert-<?php echo $mmr_status ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $mmr_msg ?>">
                                        <h2 style="margin-top:4px"><?php if($summoner_mmr > 0){ echo lang::trans('profile_mmr').' : '.$summoner_mmr; } else { echo lang::trans('profile_mmr_nommr_title').': '.$summoner_level; }?></h2>
                                    </div>
								</div>
                                <!-- Start offcanvas btn group menu: This menu will take position at the top of profile cover (mobile only). -->
                                <div class="btn-group cover-menu-mobile hidden-lg hidden-md">
                                    <button type="button" class="btn btn-theme btn-sm dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right no-border" role="menu">
                                        <li class="active"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>"><i class="fa fa-fw fa-clock-o"></i> <span><?php echo lang::trans('profile_menu_history') ?></span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/leagues"><i class="fa fa-fw fa-users"></i> <span><?php echo lang::trans('profile_menu_teamsnleagues') ?></span></a></li>
										<li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/stats"><i class="fa fa-fw fa-chart"></i> <span><?php echo lang::trans('profile_menu_stats') ?></span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/matchlist"><i class="fa fa-fw fa-folder"></i> <span><?php echo lang::trans('profile_menu_matchlist') ?></span></a></li>
										<li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/runes"><i class="fa fa-fw fa-photo"></i> <span><?php echo lang::trans('profile_menu_runes') ?></span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/masteries"><i class="fa fa-fw fa-users"></i><span> <?php echo lang::trans('profile_menu_masteries') ?> </span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/champmastery"><i class="fa fa-fw fa-envelope"></i> <span><?php echo lang::trans('profile_menu_champmastery') ?></span></a></li>
										<li class="pull-right"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/reload"><i class="fa fa-fw fa-refresh"></i> <span><?php echo lang::trans('profile_menu_reload') ?></span></a></li>
                                    </ul>
                                </div>
                            </div>
                            <ul class="list-unstyled no-padding hidden-sm hidden-xs cover-menu">
                                <li class="active"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>"><i class="fa fa-fw fa-clock-o"></i> <span><?php echo lang::trans('profile_menu_history') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/leagues"><i class="fa fa-fw fa-users"></i> <span><?php echo lang::trans('profile_menu_teamsnleagues') ?></span></a></li>
								<li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/stats"><i class="fa fa-fw fa-line-chart"></i> <span><?php echo lang::trans('profile_menu_stats') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/matchlist"><i class="fa fa-fw fa-fw fa-folder"></i> <span><?php echo lang::trans('profile_menu_matchlist') ?></span></a></li>
								<li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/runes"><i class="fa fa-fw fa-cubes"></i> <span><?php echo lang::trans('profile_menu_runes') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/masteries"><i class="fa fa-fw fa-bookmark"></i> <span><?php echo lang::trans('profile_menu_masteries') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/champmastery"><i class="fa fa-fw fa-fire"></i> <span><?php echo lang::trans('profile_menu_champmastery') ?></span></a></li>
								<li class="pull-right"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/reload"><i class="fa fa-fw fa-refresh"></i> <span><?php echo lang::trans('profile_menu_reload') ?></span></a></li>
                            </ul>
                        </div><!-- /.cover -->
                    </div><!-- /.profile-cover -->
                    </div>
					
					<div class="col-lg-3 col-md-3 col-sm-4">
                    <div class="divider"></div>
						 <div class="panel panel-lilac rounded shadow">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h3 class="panel-title"><?php echo lang::trans('profile_box_recentplayedwith_title') ?></h3>
                                </div>
                                <div class="clearfix"></div>
                            </div><!-- /.panel-heading -->
							
                            <div class="panel-body no-padding rounded">
                               <div class="inner-all">
							   <?php
								if($db->query('SELECT id FROM lol_summoners_recentlyplayed WHERE summoner_id='.$summoner_id)->num_rows > 0)
								{
									$recentlyplayed = json_decode($db->query('SELECT data FROM lol_summoners_recentlyplayed WHERE summoner_id='.$summoner_id)->fetch_row()[0],true);
									if(count($recentlyplayed) > 0)
									{
								?>
                                    <div class="table-responsive" style="text-align: center;">
                                                        <table class="table">
                                                            <thead>
                                                            <tr>
                                                                <th><?php echo lang::trans('profile_box_recentplayedwith_table_head_summoner') ?></th>
                                                                <th><?php echo lang::trans('profile_box_recentplayedwith_table_head_games') ?></th>
                                                                <th><?php echo lang::trans('profile_box_recentplayedwith_table_head_winrate') ?></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
															<?php
															foreach($recentlyplayed as $recent_summoner_id => $recent_data)
															{
																echo '<tr>
																<td style="text-decoration:none;"><a href="'.URL.'/summoner/'.$region.'/'.lol::summoner_id2name($recent_summoner_id).'">'.lol::summoner_id2name($recent_summoner_id).'</a></td>
																<td>'.$recent_data['games'].'</td>
																<td>'.$recent_data['winrate'].' %</td>
																</tr>';
															}
															?>
                                                            </tbody>
                                                        </table>
                                                    </div>
								<?php
									}
									else
									{
										echo lang::trans('profile_box_recentplayedwith_table_head_norecent');
									}
								}
								else
								{
									echo lang::trans('profile_box_recentplayedwith_table_head_norecent');
								}
								?>
                                </div>
                            </div><!-- /.panel-body -->
                        </div><!-- /.panel -->
					</div>
					
					
					<div class="col-lg-9 col-md-9 col-sm-8">
                    <div class="divider"></div>
                    <div class="panel rounded shadow">
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <h3 class="panel-title">Employee List <span class="label label-danger">AJAX Support</span></h3>
                                    </div>
                                    <div class="pull-right">
                                        <button class="btn btn-sm" data-action="refresh" data-container="body" data-toggle="tooltip" data-placement="top" data-title="Refresh" data-original-title="" title=""><i class="fa fa-refresh"></i></button>
                                        <button class="btn btn-sm" data-action="collapse" data-container="body" data-toggle="tooltip" data-placement="top" data-title="Collapse" data-original-title="" title=""><i class="fa fa-angle-up"></i></button>
                                        <button class="btn btn-sm" data-action="remove" data-container="body" data-toggle="tooltip" data-placement="top" data-title="Remove" data-original-title="" title=""><i class="fa fa-times"></i></button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div><!-- /.panel-heading -->
                                <div class="panel-body">
                                    <!-- Start datatable -->
                                    <div id="datatable-ajax_wrapper" class="dataTables_wrapper form-inline"><table id="datatable-ajax" class="table table-striped table-primary dataTable" role="grid" aria-describedby="datatable-ajax_info">
                                        <thead>
                                            <tr role="row">
											<th data-class="expand" width="5%" class="expand sorting_asc" tabindex="0" aria-controls="datatable-ajax" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Name</th>
											<th class="sorting" width="35%" tabindex="0" aria-controls="datatable-ajax" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending">Position</th>
											<th class="sorting" width="20%" tabindex="0" aria-controls="datatable-ajax" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending">Office</th>
											<th class="sorting" width="10%" tabindex="0" aria-controls="datatable-ajax" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending">Age</th>
											<th class="sorting" width="10%" tabindex="0" aria-controls="datatable-ajax" rowspan="1" colspan="1" aria-label="Start date: activate to sort column ascending">Start date</th>
											<th class="sorting" width="20%" tabindex="0" aria-controls="datatable-ajax" rowspan="1" colspan="1" aria-label="Salary: activate to sort column ascending">Salary</th></tr>
                                        </thead>
                                        <!--tbody section is required-->
                                        <tbody>
										<?php
										$games = $db->query('SELECT id,creation_timestamp,participants,queue,match_id FROM lol_matches WHERE summoner_ids LIKE "%'.$summoner_id.'%" ORDER BY creation_timestamp DESC');
										$games_count = 0;
										while($full_game_info = $games ->fetch_row())
										{
											$player_data = json_decode($full_game_info[2],true)[$summoner_id];
											echo '<tr role="row">
													<td class="expand sorting_1">
														<span class="responsiveExpander"></span> <div style="text-align:center;">'.lol::champ_id2name($player_data['champ_id']).'</div> <br> <img style="min-width: 30%;margin: 0 auto;height: auto;" src="'.URL.'/game/champions/'.lol::champ_id2key($player_data['champ_id']).'/base/0.png">
													</td>
													<td>1</td>
													<td>mg</td>
													<td>5407</td>
													<td>2008/11/28</td>
													<td>$162,700</td>
												</tr>';
										}
										?>
										</tbody>
                                        <!--tfoot section is optional-->
                                        <tfoot>
                                            <tr><th class="expand" rowspan="1" colspan="1">Name</th><th rowspan="1" colspan="1">Position</th><th rowspan="1" colspan="1">Office</th><th rowspan="1" colspan="1">Age</th><th rowspan="1" colspan="1">Start date</th><th rowspan="1" colspan="1">Salary</th></tr>
                                        </tfoot>
                                    </table><!--/ End datatable -->
                                </div><!-- /.panel-body -->
                            </div>
                    </div>
                    </div>
                    </div><!-- /.row -->

                </div><!-- /.body-content -->
                <!--/ End body content -->

            </section><!-- /#page-content -->
            <!--/ END PAGE CONTENT -->
        </section><!-- /#wrapper -->
        <!--/ END WRAPPER -->

        <!-- START @BACK TOP -->
        <div id="back-top" class="animated pulse circle">
            <i class="fa fa-angle-up"></i>
        </div><!-- /#back-top -->
        <!--/ END BACK TOP -->

        <!-- START JAVASCRIPT SECTION (Load javascripts at bottom to reduce load time) -->
        <!-- START @CORE PLUGINS -->
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery-cookie/jquery.cookie.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery.gritter/js/jquery.gritter.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/typehead.js/dist/handlebars.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/typehead.js/dist/typeahead.bundle.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery-nicescroll/jquery.nicescroll.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery.sparkline.min/index.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery-easing-original/jquery.easing.1.3.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/ionsound/js/ion.sound.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/bootbox/bootbox.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/retina.js/dist/retina.min.js"></script>
        <!--/ END CORE PLUGINS -->

        <!-- START @PAGE LEVEL PLUGINS -->
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/datatables/js/jquery.dataTables.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/datatables/js/dataTables.bootstrap.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/datatables/js/datatables.responsive.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/fuelux/dist/js/fuelux.min.js"></script>
        <!--/ END PAGE LEVEL PLUGINS -->

        <!-- START @PAGE LEVEL SCRIPTS -->
        <script src="<?php echo URL ?>/style/home/js/apps.js"></script>
        <script>$(document).ready(function() {
			$('#datatable-ajax').DataTable();
		} );</script>
        <!--/ END PAGE LEVEL SCRIPTS -->
        <!--/ END JAVASCRIPT SECTION -->

        <!-- START GOOGLE ANALYTICS -->
        <?php echo template::analytics(); ?>
        <!--/ END GOOGLE ANALYTICS -->
		<?php
		if(!empty($load_notification_error_reload_apibusy))
		{
			echo '<script>$.gritter.add({
					title: \''.lang::trans('server_api_too_busy').'\',
					text: \''.lang::trans('server_api_too_busy_sub').'\',
					image: \'/style/home/images/lee_notification.png\',
					sticky: false,
					time: \'4000\'
			});</script>';
		}
		if(!empty($profile_status_reloaded) && @$profile_status_reloaded == 'not_reloaded')
		{
			$time_elapsed_to_reload = ((time()-onlol::timing('profile_reload_interval'))+(time()-$db->query('SELECT last_update FROM lol_summoners WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->fetch_row()[0]));
			if((time()-$time_elapsed_to_reload) <= 0)
			{
				echo '<script>window.location="'.URL.'/summoner/'.$region.'/'.$summoner_name.'/reload";</script>';
			}
			echo '<script>$.gritter.add({
					title: \''.lang::trans('summoner_not_reloaded').'\',
					text: \''.str_replace('{{time}}',onlol::time_elapsed_string($time_elapsed_to_reload),str_replace('{{summoner}}',$summoner_name,lang::trans('summoner_not_reloaded_sub'))).'\',
					image: \'/style/home/images/poro_notification.png\',
					sticky: false,
					time: \'4000\'
			});</script>';
		}
		if(!empty($profile_status_reloaded) && @$profile_status_reloaded == 'reloaded')
		{
			echo '<script>$.gritter.add({
					title: \''.lang::trans('summoner_reloaded').'\',
					text: \''.str_replace('{{summoner}}',$summoner_name,lang::trans('summoner_reloaded_sub')).'\',
					image: \'/style/home/images/poro_notification.png\',
					sticky: false,
					time: \'4000\'
			});</script>';
		}
		?>
    </body>
    <!--/ END BODY -->

</html>
