<?php 
require('../kernel/core.php');
$region = lol::parseserver($_GET['region']);
/* Check profile at first */
if($db->query('SELECT id FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->num_rows == 0)
{
	onlol::redirect(URL.'/summoner/'.$region.'/'.$_GET['summoner'].'/reload');
}
else
{
	$summoner_id = $db->query('SELECT summoner_id FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->fetch_row()[0];
	if($db->query('SELECT timestamp FROM lol_summoners_champmastery WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->fetch_row()[0] < (time()-onlol::timing('profile_champmastery_interval')) OR $db->query('SELECT id FROM lol_summoners_champmastery WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->num_rows == 0)
	{
		reload::champ_mastery($summoner_id,$region);
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
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>"><i class="fa fa-fw fa-clock-o"></i> <span><?php echo lang::trans('profile_menu_history') ?></span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/leagues"><i class="fa fa-fw fa-users"></i> <span><?php echo lang::trans('profile_menu_teamsnleagues') ?></span></a></li>
										<li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/stats"><i class="fa fa-fw fa-chart"></i> <span><?php echo lang::trans('profile_menu_stats') ?></span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/matchlist"><i class="fa fa-fw fa-folder"></i> <span><?php echo lang::trans('profile_menu_matchlist') ?></span></a></li>
										<li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/runes"><i class="fa fa-fw fa-photo"></i> <span><?php echo lang::trans('profile_menu_runes') ?></span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/masteries"><i class="fa fa-fw fa-users"></i><span> <?php echo lang::trans('profile_menu_masteries') ?> </span></a></li>
                                        <li class="active"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/champmastery"><i class="fa fa-fw fa-envelope"></i> <span><?php echo lang::trans('profile_menu_champmastery') ?></span></a></li>
										<li class="pull-right"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/reload"><i class="fa fa-fw fa-refresh"></i> <span><?php echo lang::trans('profile_menu_reload') ?></span></a></li>
                                    </ul>
                                </div>
                            </div>
                            <ul class="list-unstyled no-padding hidden-sm hidden-xs cover-menu">
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>"><i class="fa fa-fw fa-clock-o"></i> <span><?php echo lang::trans('profile_menu_history') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/leagues"><i class="fa fa-fw fa-users"></i> <span><?php echo lang::trans('profile_menu_teamsnleagues') ?></span></a></li>
										<li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/stats"><i class="fa fa-fw fa-line-chart"></i> <span><?php echo lang::trans('profile_menu_stats') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/matchlist"><i class="fa fa-fw fa-folder"></i> <span><?php echo lang::trans('profile_menu_matchlist') ?></span></a></li>
								<li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/runes"><i class="fa fa-fw fa-cubes"></i> <span><?php echo lang::trans('profile_menu_runes') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/masteries"><i class="fa fa-fw fa-bookmark"></i> <span><?php echo lang::trans('profile_menu_masteries') ?></span></a></li>
                                <li class="active"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/champmastery"><i class="fa fa-fw fa-fire"></i> <span><?php echo lang::trans('profile_menu_champmastery') ?></span></a></li>
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
                                    <h3 class="panel-title"><?php echo lang::trans('profile_box_champmastery_title') ?></h3>
                                </div>
                                <div class="clearfix"></div>
                            </div><!-- /.panel-heading -->
							<?php $champmasterytotal_data = $db->query('SELECT total_levels,total_points,data FROM lol_summoners_champmastery WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->fetch_row(); $champmasterytotal_data_array = json_decode($champmasterytotal_data[2],true);
							$champmasterytotal_data_array_count_1 = 0;
							$champmasterytotal_data_array_count_2 = 0;
							$champmasterytotal_data_array_count_3 = 0;
							$champmasterytotal_data_array_count_4 = 0;
							$champmasterytotal_data_array_count_5 = 0;
							foreach($champmasterytotal_data_array as $champmasterytotal_data_array_data)
							{
								if($champmasterytotal_data_array_data['level'] == 1)
								{
									$champmasterytotal_data_array_count_1 = $champmasterytotal_data_array_count_1+1;
								}
								if($champmasterytotal_data_array_data['level'] == 2)
								{
									$champmasterytotal_data_array_count_2 = $champmasterytotal_data_array_count_2+1;
								}
								if($champmasterytotal_data_array_data['level'] == 3)
								{
									$champmasterytotal_data_array_count_3 = $champmasterytotal_data_array_count_3+1;
								}
								if($champmasterytotal_data_array_data['level'] == 4)
								{
									$champmasterytotal_data_array_count_4 = $champmasterytotal_data_array_count_4+1;
								}
								if($champmasterytotal_data_array_data['level'] == 5)
								{
									$champmasterytotal_data_array_count_5 = $champmasterytotal_data_array_count_5+1;
								}
							}	?>
                            <div class="panel-body no-padding">
							<div class="table-responsive">
                                                        <table class="table">
                                                            <tbody>
                                                            <tr>
                                                                <td>
                                                                    <span class="pull-left text-capitalize"><?php echo lang::trans('profile_box_champmastery_levels') ?></span>
                                                                    <span class="pull-right text-strong fg-teals"><?php echo $champmasterytotal_data[0] ?></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="pull-left text-capitalize"><?php echo lang::trans('profile_box_champmastery_points') ?></span>
                                                                    <span class="pull-right text-strong fg-teals"><?php echo number_format($champmasterytotal_data[1],0,',',',') ?></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="pull-left text-capitalize"><?php echo lang::trans('profile_box_champmastery_champsused') ?></span>
                                                                    <span class="pull-right text-strong fg-teals"><?php echo count($champmasterytotal_data_array) ?></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="pull-left text-capitalize"><?php echo lang::trans('profile_box_champmastery_champsnotused') ?></span>
                                                                    <span class="pull-right text-strong fg-teals"><?php echo $db->query('SELECT id FROM lol_champs WHERE lang="en"')->num_rows-count($champmasterytotal_data_array) ?></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="pull-left text-capitalize"><?php echo lang::trans('profile_box_champmastery_champswithmastery') ?> 5</span>
                                                                    <span class="pull-right text-strong fg-teals"><?php echo $champmasterytotal_data_array_count_5 ?></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="pull-left text-capitalize"><?php echo lang::trans('profile_box_champmastery_champswithmastery') ?> 4</span>
                                                                    <span class="pull-right text-strong fg-teals"><?php echo $champmasterytotal_data_array_count_4 ?></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="pull-left text-capitalize"><?php echo lang::trans('profile_box_champmastery_champswithmastery') ?> 3</span>
                                                                    <span class="pull-right text-strong fg-teals"><?php echo $champmasterytotal_data_array_count_3 ?></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="pull-left text-capitalize"><?php echo lang::trans('profile_box_champmastery_champswithmastery') ?> 2</span>
                                                                    <span class="pull-right text-strong fg-teals"><?php echo $champmasterytotal_data_array_count_2 ?></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <span class="pull-left text-capitalize"><?php echo lang::trans('profile_box_champmastery_champswithmastery') ?> 1</span>
                                                                    <span class="pull-right text-strong fg-teals"><?php echo $champmasterytotal_data_array_count_1 ?></span>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                            </div><!-- /.panel-body -->
                        </div><!-- /.panel -->
					</div>
					
					
					<div class="col-lg-9 col-md-9 col-sm-8">
                    <div class="divider"></div>
                    <div class="row">
                        <div class="col-md-12">
                            
                              <!-- Start repeater -->
                                    <div class="fuelux">
                                        <div class="repeater" data-staticheight="800" id="myRepeater">
                                            <div class="repeater-header">
                                                <div class="repeater-header-left">
                                                    <div class="repeater-search">
                                                        <div class="search input-group">
                                                            <input type="search" class="form-control" placeholder="<?php echo lang::trans('champmastery_table_search_placeholder') ?>"/>
                                                          <span class="input-group-btn">
                                                            <button class="btn btn-default" type="button">
                                                                <span class="glyphicon glyphicon-search"></span>
                                                                <span class="sr-only"><?php echo lang::trans('games_table_search') ?></span>
                                                            </button>
                                                          </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="repeater-header-right">
                                                    <div class="btn-group repeater-views" data-toggle="buttons">
                                                        <label class="btn btn-success active">
                                                            <input name="repeaterViews" type="radio" value="list"><span class="glyphicon glyphicon-list"></span>
                                                        </label>
                                                        <label class="btn btn-success">
                                                            <input name="repeaterViews" type="radio" value="thumbnail"><span class="glyphicon glyphicon-th"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="repeater-viewport">
                                                <div class="repeater-canvas"></div>
                                                <div class="loader repeater-loader"></div>
                                            </div>
                                            <div class="repeater-footer">
                                                <div class="repeater-footer-left">
                                                    <div class="repeater-itemization">
                                                        <span><span class="repeater-start"></span> - <span class="repeater-end"></span> <?php echo lang::trans('games_table_matches_per_game_of') ?> <span class="repeater-count"></span> <?php echo lang::trans('champmastery_table_matches_per_mastery_page') ?></span>
                                                        <div class="btn-group selectlist dropup" data-resize="auto">
                                                            <button type="button" class="btn btn-default dropdown-toggle dropup" data-toggle="dropdown">
                                                                <span class="selected-label">&nbsp;</span>
                                                                <span class="caret"></span>
                                                                <span class="sr-only"><?php echo lang::trans('games_table_matches_per_game') ?></span>
                                                            </button>
                                                            <ul class="dropdown-menu" role="menu">
                                                                <li data-value="5"><a href="#">5</a></li>
                                                                <li data-value="10" data-selected="true"><a href="#">10</a></li>
                                                                <li data-value="20"><a href="#">20</a></li>
                                                                <li data-value="50" data-foo="bar" data-fizz="buzz"><a href="#">50</a></li>
                                                                <li data-value="100"><a href="#">100</a></li>
                                                            </ul>
                                                            <input class="hidden hidden-field" name="itemsPerPage" readonly="readonly" aria-hidden="true" type="text"/>
                                                        </div>
                                                        <span><?php echo lang::trans('games_table_matches_per_game_page') ?></span>
                                                    </div>
                                                </div>
                                                <div class="repeater-footer-right">
                                                    <div class="repeater-pagination">
                                                        <button type="button" class="btn btn-default btn-sm repeater-prev">
                                                            <span class="glyphicon glyphicon-chevron-left"></span>
                                                            <span class="sr-only"><?php echo lang::trans('games_table_matches_per_game_prev') ?></span>
                                                        </button>
                                                        <label class="page-label" id="myPageLabel"><?php echo lang::trans('games_table_matches_per_game_page_single') ?></label>
                                                        <div class="repeater-primaryPaging active">
                                                            <div class="input-group input-append dropdown combobox dropup">
                                                                <input type="text" class="form-control" aria-labelledby="myPageLabel">
                                                                <div class="input-group-btn">
                                                                    <button type="button" class="btn btn-default dropdown-toggle dropup" data-toggle="dropdown">
                                                                        <span class="caret"></span>
                                                                        <span class="sr-only"><?php echo lang::trans('games_table_matches_per_game_pagefilter') ?></span>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-right"></ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="text" class="form-control repeater-secondaryPaging" aria-labelledby="myPageLabel">
                                                        <span><?php echo lang::trans('games_table_matches_per_game_of') ?> <span class="repeater-pages"></span></span>
                                                        <button type="button" class="btn btn-default btn-sm repeater-next">
                                                            <span class="glyphicon glyphicon-chevron-right"></span>
                                                            <span class="sr-only"><?php echo lang::trans('games_table_matches_per_game_next') ?></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/ End repeater -->
									<br>
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
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/fuelux/dist/js/fuelux.min.js"></script>
        <!--/ END PAGE LEVEL PLUGINS -->

        <!-- START @PAGE LEVEL SCRIPTS -->
        <script src="<?php echo URL ?>/style/home/js/apps.js"></script>
        <script>'use strict';
var BlankonTable = function () {

    // =========================================================================
    // SETTINGS APP
    // =========================================================================
    var globalPluginsPath = BlankonApp.handleBaseURL()+'/assets/global/plugins/bower_components';

    return {

        // =========================================================================
        // CONSTRUCTOR APP
        // =========================================================================
        init: function () {
            BlankonTable.datatable();
        },

        // =========================================================================
        // DATATABLE
        // =========================================================================
        datatable: function () {
            var responsiveHelperAjax = undefined;
            var responsiveHelperDom = undefined;
            var breakpointDefinition = {
                tablet: 1024,
                phone : 480
            };

            var tableDom = $('#datatable-dom');

            // Using DOM
            // Remove arrow datatable
            $.extend( true, $.fn.dataTable.defaults, {
                "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0, 1, 2, 5 ] } ]
            } );
            tableDom.dataTable({
                autoWidth        : false,
                preDrawCallback: function () {
                    // Initialize the responsive datatables helper once.
                    if (!responsiveHelperDom) {
                        responsiveHelperDom = new ResponsiveDatatablesHelper(tableDom, breakpointDefinition);
                    }
                },
                rowCallback    : function (nRow) {
                    responsiveHelperDom.createExpandIcon(nRow);
                },
                drawCallback   : function (oSettings) {
                    responsiveHelperDom.respond();
                }
            });

            // Repeater
            var columns = [
                {
                    label: '<?php echo lang::trans('champmastery_table_head_champion') ?>',
                    property: 'row_1',
                    sortable: true,
					width:'15%'
                },
                {
                    label: '<?php echo lang::trans('champmastery_table_head_level') ?>',
                    property: 'row_3',
                    sortable: true,
					width:'20%'
                },
                {
                    label: '<?php echo lang::trans('champmastery_table_head_points') ?>',
                    property: 'row_4',
                    sortable: true,
					width:'30%'
                },
                {
                    label: '<?php echo lang::trans('champmastery_table_head_pointslvl') ?>',
                    property: 'row_5',
                    sortable: true
                }
            ];
            var delays = ['300', '600', '900', '1200'];
            var products = [
			<?php
			$mastery_champ_summoner = json_decode($db->query('SELECT data FROM lol_summoners_champmastery WHERE summoner_id='.$summoner_id.' AND region="'.$region.'"')->fetch_row()[0],true);
			$mastery_champ_summoner_colors = array(1 => 'danger',2 => 'warning',3 => 'success',4 => 'info',5 => 'teals');
			foreach($mastery_champ_summoner as $mastery_champ_id => $mastery_champ_data)
			{
				if($mastery_champ_data['points_need_nextlevel'] == 0)
				{
					$mastery_champ_progress = '<div class=\"progress-striped\"><div class=\"progress-bar progress-bar-'.$mastery_champ_summoner_colors[5].'\" role=\"progressbar\" aria-valuenow=\"100\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 100%;\">'.lang::trans('champmastery_table_head_pointslvl_full').'</div></div>';
				}
				else
				{
					switch($mastery_champ_data['level'])
					{
						case 1:
						$mastery_champ_progress_color = $mastery_champ_summoner_colors[1];
						break;
						case 2:
						$mastery_champ_progress_color = $mastery_champ_summoner_colors[2];
						break;
						case 3:
						$mastery_champ_progress_color = $mastery_champ_summoner_colors[3];
						break;
						case 4:
						$mastery_champ_progress_color = $mastery_champ_summoner_colors[4];
						break;
						case 5:
						$mastery_champ_progress_color = $mastery_champ_summoner_colors[5];
						break;
						default: $mastery_champ_progress_color = 'inverse';
					}
					$mastery_champ_progress = '<div class=\"progress-striped\"><div class=\"progress-bar progress-bar-'.$mastery_champ_progress_color.'\" role=\"progressbar\" aria-valuenow=\"100\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 100%;\">'.$mastery_champ_data['points'].'/'.($mastery_champ_data['points_need_nextlevel']+$mastery_champ_data['points']).'</div></div>';
				}
				echo '{
                    "row_1": "<div class=\"row\" style=\"text-align:center;\"><a href=\"'.URL.'/game/champions/'.lol::champ_id2name($mastery_champ_id).'\">'.lol::champ_id2name($mastery_champ_id).'</a></div><br><div class=\"col-md-12\" style=\"margin-right:0px;\"><a href=\"'.URL.'/game/champions/'.lol::champ_id2name($mastery_champ_id).'\"><img style=\"max-width:100%;width:70px;\" height=\"70px\" src=\"'.URL.'/game/champions/'.lol::champ_id2key($mastery_champ_id).'/base/0.png\"></div>",
                    "row_3": "<div class=\"col-md-12\" style=\"margin-right:0px;\"><img draggable=\"false\" style=\"display: block;margin-left: auto; margin-right: auto\" src=\"'.URL.'/game/champions_mastery/tier_'.$mastery_champ_data['level'].'.png\"></div>",
					"row_4": "'.number_format($mastery_champ_data['points'],0,',',',').'",
                    "row_5": "'.$mastery_champ_progress.'",
					"thumb_name": "'.lol::champ_id2name($mastery_champ_id).'",
                    "ThumbnailImage": "'.URL.'/game/champions/'.lol::champ_id2key($mastery_champ_id).'/base/0.png",
                },';
			}
			?>
            ];
            var dataSource, filtering;

            dataSource = function(options, callback){
                var items = filtering(options);
                var resp = {
                    count: items.length,
                    items: [],
                    page: options.pageIndex,
                    pages: Math.ceil(items.length/(options.pageSize || 50))
                };
                var i, items, l;

                i = options.pageIndex * (options.pageSize || 50);
                l = i + (options.pageSize || 50);
                l = (l <= resp.count) ? l : resp.count;
                resp.start = i + 1;
                resp.end = l;

                if(options.view==='list' || options.view==='thumbnail'){
                    if(options.view==='list'){
                        resp.columns = columns;
                        for(i; i<l; i++){
                            resp.items.push(items[i]);
							console.log(items[i].color + ' COLOR');
                        }
                    }else{
                        for(i; i<l; i++){
                            resp.items.push({
                                name: items[i].thumb_name,
                                src: items[i].ThumbnailImage,
                            });
                        }
                    }

                    setTimeout(function(){
                        callback(resp);
                    }, delays[Math.floor(Math.random() * 4)]);
                }
            };

            filtering = function(options){
                var items = $.extend([], products);
                var search;
                if(options.filter.value!=='all'){
                    items = $.grep(items, function(item){
                        return (item.type.search(options.filter.value)>=0);
                    });
                }
                if(options.search){
                    search = options.search.toLowerCase();
                    items = $.grep(items, function(item){
                        return (
                        (item.row_1.toLowerCase().search(options.search)>=0)
                        );
                    });
                }
                if(options.sortProperty){
                    items = $.grep(items, function(item){
                        if(options.sortProperty==='id' || options.sortProperty==='height' || options.sortProperty==='weight'){
                            return parseFloat(item[options.sortProperty]);
                        }else{
                            return item[options.sortProperty];
                        }
                    });
                    if(options.sortDirection==='desc'){
                        items.reverse();
                    }
                }

                return items;
            };

            // REPEATER
            $('#repeaterIllustration').repeater({
                dataSource: dataSource
            });

            $('#myRepeater').repeater({
                dataSource: dataSource
            });

            $('#myRepeaterList').repeater({
                dataSource: dataSource
            });

            $('#myRepeaterThumbnail').repeater({
                dataSource: dataSource,
                thumbnail_template: '<div class="thumbnail repeater-thumbnail" style="background: {{color}};"><img height="75" src="{{src}}" width="65"><span>{{name}}</span></div>'
            });

        }

    };

}();

// Call main app init
BlankonTable.init();</script>
        <!--/ END PAGE LEVEL SCRIPTS -->
        <!--/ END JAVASCRIPT SECTION -->

        <!-- START GOOGLE ANALYTICS -->
        <?php echo template::analytics(); ?>
        <!--/ END GOOGLE ANALYTICS -->
    </body>
    <!--/ END BODY -->

</html>
