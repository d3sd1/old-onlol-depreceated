<?php 
require('../kernel/core.php');
$region = lol::parseserver($_GET['region']);
/* Check profile at first */
if($db->query('SELECT id FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->num_rows == 0)
{
	onlol::redirect(URL.'/summoner/'.$region.'/'.$_GET['summoner'].'&reload=true');
}
elseif(empty($_GET['notify']))
{
	$summoner_id = $db->query('SELECT summoner_id FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->fetch_row()[0];
	if($db->query('SELECT timestamp FROM lol_summoner_runes WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->fetch_row()[0] < (time()-onlol::timing('profile_runes_interval')) OR $db->query('SELECT id FROM lol_summoner_runes WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->num_rows == 0)
	{
		reload::runes($summoner_id,$region);
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
		<link href="<?php echo URL ?>/style/home/css/runes.css" rel="stylesheet">
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
                                        <li class="active"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/runes"><i class="fa fa-fw fa-photo"></i> <span><?php echo lang::trans('profile_menu_runes') ?></span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/masteries"><i class="fa fa-fw fa-users"></i><span> <?php echo lang::trans('profile_menu_masteries') ?> </span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/champmastery"><i class="fa fa-fw fa-envelope"></i> <span><?php echo lang::trans('profile_menu_champmastery') ?></span></a></li>
										<li class="pull-right"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/reload"><i class="fa fa-fw fa-refresh"></i> <span><?php echo lang::trans('profile_menu_reload') ?></span></a></li>
                                    </ul>
                                </div>
                            </div>
                            <ul class="list-unstyled no-padding hidden-sm hidden-xs cover-menu">
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>"><i class="fa fa-fw fa-clock-o"></i> <span><?php echo lang::trans('profile_menu_history') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/leagues"><i class="fa fa-fw fa-users"></i> <span><?php echo lang::trans('profile_menu_teamsnleagues') ?></span></a></li>
								<li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/stats"><i class="fa fa-fw fa-line-chart"></i> <span><?php echo lang::trans('profile_menu_stats') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/matchlist"><i class="fa fa-fw fa-folder"></i> <span><?php echo lang::trans('profile_menu_matchlist') ?></span></a></li>
                                <li class="active"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/runes"><i class="fa fa-fw fa-cubes"></i> <span><?php echo lang::trans('profile_menu_runes') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/masteries"><i class="fa fa-bookmark"></i> <span><?php echo lang::trans('profile_menu_masteries') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/champmastery"><i class="fa fa-fw fa-fire"></i> <span><?php echo lang::trans('profile_menu_champmastery') ?></span></a></li>
								<li class="pull-right"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/reload"><i class="fa fa-fw fa-refresh"></i> <span><?php echo lang::trans('profile_menu_reload') ?></span></a></li>
                            </ul>
                        </div><!-- /.cover -->
                    </div><!-- /.profile-cover -->
                    </div>
					<div class="col-md-12">
						<div class="divider"></div>
                            <!-- Start color vertical tabs -->
                            <div class="panel panel-tab panel-tab-double panel-tab-vertical row no-margin mb-15 shadow">
                                <!-- Start tabs heading -->
                                <div class="panel-heading no-padding col-lg-2 col-md-2 col-sm-2">
                                    <ul class="nav nav-tabs">
									<?php
									$summoner_runes = json_decode($db->query('SELECT data FROM lol_summoner_runes WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->fetch_row()[0],true);
									foreach($summoner_runes['pages'] as $runepage_count => $runepage_data)
									{
										$runepage_status = null;
										if($summoner_runes['current'] == $runepage_count)
										{
											$runepage_status = ' active';
										}
										echo '<li class="nav-border nav-border-left-success'.$runepage_status.'">
                                            <a href="#runepage_'.$runepage_count.'" data-toggle="tab" aria-expanded="false">
                                                '.($runepage_count+1).' - '.$runepage_data['name'].'
                                            </a>
                                        </li>';
									}
									?>
                                    </ul>
                                </div><!-- /.panel-heading -->
                                <!--/ End tabs heading -->

                                <!-- Start tabs content -->
                                <div class="panel-body col-lg-10 col-md-10 col-sm-10">
                                    <div class="tab-content">
											<?php
											// acabar el mapa de imagenes + las estadisticas de la runa
											foreach($summoner_runes['pages'] as $runepage_count => $runepage_data)
											{
												$runepage_status = null;
												if($summoner_runes['current'] == $runepage_count)
												{
													$runepage_status = ' active in';
												}
												echo '<div class="tab-pane fade'.$runepage_status.'" id="runepage_'.$runepage_count.'">
												<div class="col-md-8">';
											
												
													
												echo '<div style="position:relative">
														<img src="'.URL.'/game/runes/bg/rune-bg.png" width="656" hspace="16" height="436" vspace="16">';
														
												$runes_stats = array();
												foreach($runepage_data['slots'] as $runepage_slot_count => $runepage_slot_runeid)
												{
													$rune_width = 48;
													$rune_height = 54;
													if($runepage_slot_count > 27)
													{
														$rune_width = 96;
														$rune_height = 96;
													}
													echo '<a href="'.URL.'/runes/'.$runepage_slot_runeid.'"><img width="'.$rune_width.'px" height="'.$rune_height.'px" data-toggle="tooltip" title="<b>'.$db->query('SELECT name FROM lol_runes WHERE rune_id='.$runepage_slot_runeid.' AND lang="'.lang::parselang($_SESSION['onlol_lang']).'"')->fetch_row()[0].'</b><p> '.$db->query('SELECT description FROM lol_runes WHERE rune_id='.$runepage_slot_runeid.' AND lang="'.lang::parselang($_SESSION['onlol_lang']).'"')->fetch_row()[0].'</p>"  data-html="true" draggable="false" class="rune_'.$runepage_slot_count.'" border="0" src="'.URL.'/game/runes/'.$runepage_slot_runeid.'.png"></a> <!-- '.$runepage_slot_count.' -->';
													$this_rune_stats = json_decode($db->query('SELECT stats FROM lol_runes WHERE rune_id='.$runepage_slot_runeid.' AND lang="'.lang::parselang($_SESSION['onlol_lang']).'"')->fetch_row()[0],true);
													if(!array_key_exists(key($this_rune_stats),$runes_stats))
													{
														$runes_stats[key($this_rune_stats)] = array();
														$runes_stats[key($this_rune_stats)]['plus'] = 0;
													}
													$runes_stats[key($this_rune_stats)]['plus'] = ($runes_stats[key($this_rune_stats)]['plus']+$this_rune_stats[key($this_rune_stats)]);
												}					
													echo '</div>
													
											</div>
											<div class="col-md-4">

                                            <!-- Start list bank table -->
                                            <div class="panel">

                                                <div class="panel-heading">
                                                    <h3 class="panel-title text-center">'.lang::trans('runepage_stats_title').'</h3>
                                                </div><!-- /.panel-heading -->
                                                <div class="panel-body no-padding">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <tbody>
                                                            ';
															foreach($runes_stats as $rune_key => $rune_stat_merged)
															{
																if(stristr($rune_key,'Percent') == true)
																{
																	$rune_stat_depured = ($rune_stat_merged['plus']*100);
																	$rune_stat_depured_extra = ' %';
																}
																else
																{
																	$rune_stat_depured = $rune_stat_merged['plus'];
																	$rune_stat_depured_extra = null;
																}
																if(stristr($rune_key,'Cooldown') == false)
																{
																	$rune_stat_action = ' + ';
																}
																else
																{
																	$rune_stat_action = null; //Negative values already have - char
																}
																echo '<tr>
                                                                <td>
                                                                    <span class="pull-left text-capitalize">'.$db->query('SELECT trans_value FROM lol_trans WHERE trans_key="'.$rune_key.'" AND lang="'.lang::parselang($_SESSION['onlol_lang']).'"')->fetch_row()[0].'</span>
                                                                    <span class="pull-right text-strong fg-teals"><strong style="color:black;">'.$rune_stat_action.number_format($rune_stat_depured,2).$rune_stat_depured_extra.'</strong></span>
                                                                </td>
                                                            </tr>';
															}
															
                                                            echo '
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div><!-- /.panel-body -->
                                            </div><!--/ End list bank table -->

                                        </div>
                                        </div>';
											}
											?>
                                        
                                    </div>
                                </div><!-- /.panel-body -->
                                <!--/ End tabs content -->
                            </div><!-- /.panel -->
                            <!--/ End color vertical tabs -->

                        </div>
                    </div>
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
        

        <!--/ END PAGE LEVEL SCRIPTS -->
        <!--/ END JAVASCRIPT SECTION -->

        <!-- START GOOGLE ANALYTICS -->
        <?php echo template::analytics(); ?>
        <!--/ END GOOGLE ANALYTICS -->
    </body>
    <!--/ END BODY -->

</html>
