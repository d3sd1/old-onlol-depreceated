<?php 
require('../kernel/core.php'); 
if(!empty($_GET['skip_inactivity']))
{
	$_SESSION['onlol_inactivity_disabled'] = true;
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- START @HEAD -->
    <head>
        <?php echo template::meta(); ?>
		<title><?php echo lang::trans('home_title') ?></title>
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
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/dropzone/downloads/css/dropzone.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/jquery.gritter/css/jquery.gritter.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/jvectormap/jquery-jvectormap.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/horizontal-chart/build/css/horizBarChart.css" rel="stylesheet">
		<link href="<?php echo URL ?>/style/global/plugins/bower_components/bootstrap-tour/build/css/bootstrap-tour.min.css" rel="stylesheet">
		<link href="<?php echo URL ?>/style/global/css/cursor.css" rel="stylesheet">
        <!--/ END PAGE LEVEL STYLES -->

        <!-- START @THEME STYLES -->
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery/dist/jquery.min.js"></script>
        <link href="<?php echo URL ?>/style/home/css/reset.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/home/css/layout.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/home/css/components.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/home/css/plugins.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/home/css/themes/default.theme.css" rel="stylesheet" id="theme">
        <link href="<?php echo URL ?>/style/home/css/pages/dashboard-retail.css" rel="stylesheet">
		<script type="text/javascript" src="<?php echo URL ?>/style/home/js/pages/jssor.slider.min.js"></script>
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
    <body class="page-session page-sound page-header-fixed page-sidebar-fixed demo-dashboard-session">

        <!--[if lt IE 9]>
        <p class="upgrade-browser">Upps!! You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/" target="_blank">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- START @WRAPPER -->
        <section id="wrapper">

            <?php echo template::nav_top() ?>
            <?php echo template::left_menu('dash') ?>

            <!-- START @PAGE CONTENT -->
            <section id="page-content">

                <!-- Start body content -->
                <div class="body-content animated fadeIn" style="background-image: url(<?php echo URL ?>/game/bgs/<?php echo rand(1,11) ?>.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="mini-stat clearfix bg-bitbucket">
                                        <span class="mini-stat-icon"><i class="fa fa-globe fg-danger"></i></span><!-- /.mini-stat-icon -->
                                        <div class="mini-stat-info">
                                            <span class="counter"><?php echo number_format($db->query('SELECT id FROM lol_matches')->num_rows,0,',',',') ?></span>
                                            <?php echo lang::trans('home_dash_boxes_matches') ?>
                                        </div><!-- /.mini-stat-info -->
                                    </div><!-- /.mini-stat -->
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="mini-stat clearfix bg-bitbucket">
                                        <span class="mini-stat-icon"><i class="fa fa-child fg-success"></i></span><!-- /.mini-stat-icon -->
                                        <div class="mini-stat-info">
                                            <span class="counter"><?php echo number_format($db->query('SELECT id FROM lol_summoners')->num_rows,0,',',',') ?></span>
                                            <?php echo lang::trans('home_dash_boxes_summoners') ?>
                                        </div><!-- /.mini-stat-info -->
                                    </div><!-- /.mini-stat -->
                                </div>
								<?php
						$blue_team_wins = $db->query('SELECT value FROM lol_stats WHERE stat="red_side_wins"')->fetch_row()[0];
						$red_team_wins = $db->query('SELECT value FROM lol_stats WHERE stat="blue_side_wins"')->fetch_row()[0];
						$total_wins = $blue_team_wins+$red_team_wins;
						if($total_wins == 0){$total_wins = 1;}
						$percent_blue_team_wins = number_format((($blue_team_wins*100)/$total_wins),1);
						$percent_red_team_wins = number_format((($red_team_wins*100)/$total_wins),1);
						?>
                                <div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading">&nbsp;</div>
                                        <div class="panel-body text-center">
                                            <div class="easy-pie-chart easy-pie-sm">
                                                <div class="percentage" data-percent="<?php echo $percent_red_team_wins ?>" data-size="100" data-bar-color="#E9573F">
                                                    <span> <?php echo $percent_red_team_wins ?> </span> %
                                                </div>
                                                <a class="title" href="javascript:void(0);">
                                                    <i class="fa fa-bar-chart"></i> <?php echo lang::trans('home_dash_boxes_stats_red') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<div class="col-md-3 col-sm-3 col-xs-3">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">&nbsp;</div>
                                        <div class="panel-body text-center">
                                            <div class="easy-pie-chart easy-pie-sm">
                                                <div class="percentage" data-percent="<?php echo $percent_blue_team_wins ?>" data-size="100" data-bar-color="#00B1E1">
                                                    <span> <?php echo $percent_blue_team_wins ?> </span> %
                                                </div>
                                                <a class="title" href="javascript:void(0);">
                                                    <i class="fa fa-bar-chart"></i> <?php echo lang::trans('home_dash_boxes_stats_blue') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
							 <div id="jssor_1" class="col-md-3">
        <!-- Loading Screen -->
        <div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
            <div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
            <div style="position:absolute;display:block;background:url('img/loading.gif') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>
        </div>
        <div data-u="slides" class="col-md-3">
            <div data-p="225.00" style="display: none;">
                <img data-u="image" src="img/red.jpg" />
                <div style="position: absolute; top: 30px; left: 30px; width: 480px; height: 120px; font-size: 50px; color: #ffffff; line-height: 60px;">TOUCH SWIPE SLIDER</div>
                <div style="position: absolute; top: 300px; left: 30px; width: 480px; height: 120px; font-size: 30px; color: #ffffff; line-height: 38px;">Build your slider with anything, includes image, content, text, html, photo, picture</div>
                <div data-u="caption" data-t="0" style="position: absolute; top: 100px; left: 600px; width: 445px; height: 300px;">
                    <img src="img/c-phone.png" style="position: absolute; top: 0px; left: 0px; width: 445px; height: 300px;" />
                    <img src="img/c-jssor-slider.png" data-u="caption" data-t="1" style="position: absolute; top: 70px; left: 130px; width: 102px; height: 78px;" />
                    <img src="img/c-text.png" data-u="caption" data-t="2" style="position: absolute; top: 153px; left: 163px; width: 80px; height: 53px;" />
                    <img src="img/c-fruit.png" data-u="caption" data-t="3" style="position: absolute; top: 60px; left: 220px; width: 140px; height: 90px;" />
                    <img src="img/c-navigator.png" data-u="caption" data-t="4" style="position: absolute; top: -123px; left: 121px; width: 200px; height: 155px;" />
                </div>
                <div data-u="caption" data-t="5" style="position: absolute; top: 120px; left: 650px; width: 470px; height: 220px;">
                    <img src="img/c-phone-horizontal.png" style="position: absolute; top: 0px; left: 0px; width: 470px; height: 220px;" />
                    <div style="position: absolute; top: 4px; left: 45px; width: 379px; height: 213px; overflow: hidden;">
                        <img src="img/c-slide-1.jpg" data-u="caption" data-t="6" style="position: absolute; top: 0px; left: 0px; width: 379px; height: 213px;" />
                        <img src="img/c-slide-3.jpg" data-u="caption" data-t="7" style="position: absolute; top: 0px; left: 379px; width: 379px; height: 213px;" />
                    </div>
                    <img src="img/c-navigator-horizontal.png" style="position: absolute; top: 4px; left: 45px; width: 379px; height: 213px;" />
                    <img src="img/c-finger-pointing.png" data-u="caption" data-t="8" style="position: absolute; top: 740px; left: 1600px; width: 257px; height: 300px;" />
                </div>
            </div>
            <div data-p="225.00" style="display: none;">
                <img data-u="image" src="img/purple.jpg" />
            </div>
            <div data-p="225.00" style="display: none;">
                <img data-u="image" src="img/blue.jpg" />
            </div>
        
        </div>
        <!-- Bullet Navigator -->
        <div data-u="navigator" class="jssorb05" style="bottom:16px;right:16px;" data-autocenter="1">
            <!-- bullet navigator item prototype -->
            <div data-u="prototype" style="width:16px;height:16px;"></div>
        </div>
    </div>
    <script>
        var jssor_1_SlideoTransitions = [
              [{b:5500,d:3000,o:-1,r:240,e:{r:2}}],
              [{b:-1,d:1,o:-1,c:{x:51.0,t:-51.0}},{b:0,d:1000,o:1,c:{x:-51.0,t:51.0},e:{o:7,c:{x:7,t:7}}}],
              [{b:-1,d:1,o:-1,sX:9,sY:9},{b:1000,d:1000,o:1,sX:-9,sY:-9,e:{sX:2,sY:2}}],
              [{b:-1,d:1,o:-1,r:-180,sX:9,sY:9},{b:2000,d:1000,o:1,r:180,sX:-9,sY:-9,e:{r:2,sX:2,sY:2}}],
              [{b:-1,d:1,o:-1},{b:3000,d:2000,y:180,o:1,e:{y:16}}],
              [{b:-1,d:1,o:-1,r:-150},{b:7500,d:1600,o:1,r:150,e:{r:3}}],
              [{b:10000,d:2000,x:-379,e:{x:7}}],
              [{b:10000,d:2000,x:-379,e:{x:7}}],
              [{b:-1,d:1,o:-1,r:288,sX:9,sY:9},{b:9100,d:900,x:-1400,y:-660,o:1,r:-288,sX:-9,sY:-9,e:{r:6}},{b:10000,d:1600,x:-200,o:-1,e:{x:16}}]
            ];
            
            var jssor_1_options = {
              $AutoPlay: true,
              $SlideDuration: 800,
              $SlideEasing: $Jease$.$OutQuint,
              $CaptionSliderOptions: {
                $Class: $JssorCaptionSlideo$,
                $Transitions: jssor_1_SlideoTransitions
              },
              $ArrowNavigatorOptions: {
                $Class: $JssorArrowNavigator$
              },
              $BulletNavigatorOptions: {
                $Class: $JssorBulletNavigator$
              }
            };
            
            var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);
            
            //responsive code begin
            //you can remove responsive code if you don't want the slider scales while window resizing
            function ScaleSlider() {
                var refSize = jssor_1_slider.$Elmt.parentNode.clientWidth;
                if (refSize) {
                    refSize = Math.min(refSize, 1920);
                    jssor_1_slider.$ScaleWidth(refSize);
                }
                else {
                    window.setTimeout(ScaleSlider, 30);
                }
            }
            ScaleSlider();
            $Jssor$.$AddEvent(window, "load", ScaleSlider);
            $Jssor$.$AddEvent(window, "resize", ScaleSlider);
            $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
            //responsive code ends
    </script>

                        </div>
                        <div class="col-md-4">
                            <div class="row hidden-xs">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="mini-stat clearfix bg-bitbucket">
                                        <span class="mini-stat-icon"><i class="fa fa-tag fg-primary"></i></span><!-- /.mini-stat-icon -->
                                        <div class="mini-stat-info">
                                            <span><?php echo onlol::config('lol_patch') ?></span>
                                            <?php echo lang::trans('home_dash_boxes_patch') ?>
                                        </div><!-- /.mini-stat-info -->
                                    </div><!-- /.mini-stat -->
                                </div>
                                <div class="col-md-12">
                                    <div class="panel panel-tab shadow">
                                        <!-- Start tabs heading -->
                                        <div class="panel-heading no-padding">
                                            <ul class="nav nav-tabs" style="width: 100%;">
                                                <li class="active nav-border nav-border-top-danger" style="min-width: 50%;">
                                                    <a href="#tab-product" data-toggle="tab" class="text-center">
                                                        <?php echo lang::trans('home_dash_top_champions') ?>
                                                    </a>
                                                </li>
                                                <li class="nav-border nav-border-top-success" style="min-width: 50%;">
                                                    <a href="#tab-store" data-toggle="tab" class="text-center no-border">
                                                        <?php echo lang::trans('home_dash_top_summoners') ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div><!-- /.panel-heading -->
                                        <!--/ End tabs heading -->

                                        <!-- Start tabs content -->
                                        <div class="panel-body">
                                            <div class="tab-content">
                                                <div class="tab-pane fade in active" id="tab-product">
                                                    <ul class="chart top-product-chart no-margin" style="height: inherit;">
                                                        <?php
													$top_summoners = $db->query('SELECT champ_id,wins FROM lol_stats_champs ORDER BY wins DESC LIMIT 14');
													while($row = $top_summoners->fetch_array())
													{
														echo '<li class="current" title="'.onlol::champid_to_champname($row['champ_id']).'">
                                                            <img draggable="false" src="'.URL.'/game/champions/'.onlol::champid_to_champkeyname($row['champ_id']).'/base/0.png" alt="'.onlol::champid_to_champkeyname($row['champ_id']).'"/>
                                                            <span class="bar" data-number="'.$row['wins'].'"></span>
                                                            <span class="number"><span class="counter">'.$row['wins'].'</span>%</span>
                                                        </li>';
													}
													?>
                                                    </ul>
                                                </div>
                                                <div class="tab-pane fade" id="tab-store">
                                                    <ul class="chart top-store-chart no-margin" style="height: inherit;">
													<?php
													$top_summoners = $db->query('SELECT name,lp,region FROM lol_stats_bestsummoners ORDER BY lp DESC LIMIT 14');
													while($row = $top_summoners->fetch_array())
													{
														echo '<li class="current" title="'.$row['name'].' ('.strtoupper($row['region']).')">
                                                            <img src="http://avatar.leagueoflegends.com/'.$row['region'].'/'.$row['name'].'.png" alt="'.$row['name'].'"/>
                                                            <span class="bar" data-number="'.$row['lp'].'"></span>
                                                            <span class="number"><span class="counter">'.number_format($row['lp'],0,',',',').'</span></span>
                                                        </li>';
													}
													?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div><!-- /.panel-body -->
                                        <!--/ End tabs content -->
                                    </div><!-- /.panel -->
                                </div>
                            </div>
                        </div>
                    </div>

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
<!-- START @ADDITIONAL ELEMENT -->
        <?php
		if(empty($_SESSION['first_time']))
		{
			$_SESSION['first_time'] = true;
			?><div class="modal modal-success fade" id="modal-bootstrap-tour" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo lang::trans('tour_title') ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="media">
                            <div class="media-left">
                                <a href="#">
                                    <img data-no-retina class="media-object" src="<?php echo URL ?>/style/home/images/tour.png" alt="Tour" style="width: 100px;">
                                </a>
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading"><?php echo lang::trans('tour_subtitle_1') ?></h4>
								<br>
                                <?php echo lang::trans('tour_subtitle_2') ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="BlankonUiFeatureBootstrapTour.handleTour()" data-dismiss="modal"><?php echo lang::trans('tour_start_button') ?> <i class="fa fa-arrow-circle-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
		<?php } ?>

        <div class="modal modal-danger fade" id="modal-bootstrap-tour-end" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo lang::trans('tour_end_title') ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="media">
                            <div class="media-left">
                                <a href="#">
                                    <img data-no-retina class="media-object" src="<?php echo URL ?>/style/home/images/tour_end.png" alt="..." style="width: 100px;">
                                </a>
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading"><?php echo lang::trans('tour_end_subtitle_1') ?></h4>
								<br>
                                <?php echo lang::trans('tour_end_subtitle_2') ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal"> <?php echo lang::trans('tour_end_button') ?> <i class="fa fa-arrow-circle-right"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!--/ END ADDITIONAL ELEMENT -->
        <!-- START JAVASCRIPT SECTION (Load javascripts at bottom to reduce load time) -->
        <!-- START @CORE PLUGINS -->
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery-cookie/jquery.cookie.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
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
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/bootstrap-tour/build/js/bootstrap-tour.min.js"></script>
        <!--/ END PAGE LEVEL PLUGINS -->

        <script>var BlankonUiFeatureBootstrapTour = function () {

    return {

        // =========================================================================
        // CONSTRUCTOR APP
        // =========================================================================
        init: function () {
            BlankonUiFeatureBootstrapTour.callModal();
        },

        // =========================================================================
        // CALL MODAL FIRST
        // =========================================================================
        callModal: function () {
            $('#modal-bootstrap-tour').modal(
                {
                    show: true,
                    backdrop: 'static',
                    keyboard: false
                }
            );
        },

        // =========================================================================
        // INITIALIZE THE TOUR
        // =========================================================================
        handleTour: function () {
            // Instance the tour
            var tour = new Tour({
                name: "tour",
                steps: [
                    {
                        element: "#tour-2",
                        title: "<?php echo lang::trans('tour_2_title') ?>",
                        content: "<?php echo lang::trans('tour_2_desc') ?>",
                        placement: "left"
                    },
                    {
                        element: "#tour-3",
						title: "<?php echo lang::trans('tour_3_title') ?>",
                        content: "<?php echo lang::trans('tour_3_desc') ?>",
                        placement: "left"
                    },
                    {
                        element: "#tour-8",
                        title: "<?php echo lang::trans('tour_6_title') ?>",
                        content: "<?php echo lang::trans('tour_6_desc') ?>",
                        placement: "left"
                    },
                    {
                        element: "#tour-1",
                        title: "<?php echo lang::trans('tour_1_title') ?>",
                        content: "<?php echo lang::trans('tour_1_desc') ?>",
                        placement: "bottom"
                    },
                    {
                        element: "#tour-4",
						title: "<?php echo lang::trans('tour_4_title') ?>",
                        content: "<?php echo lang::trans('tour_4_desc') ?>",
                        placement: "bottom"
                    },
                    {
                        element: "#tour-5",
                        title: "<?php echo lang::trans('tour_5_title') ?>",
                        content: "<?php echo lang::trans('tour_5_desc') ?>",
                        placement: "right"
                    },
                    {
                        element: "#tour-10",
                        title: "<?php echo lang::trans('tour_7_title') ?>",
                        content: "<?php echo lang::trans('tour_7_desc') ?>",
                        placement: "right"
                    },
                    {
                        element: "#logout",
                        title: "<?php echo lang::trans('tour_8_title') ?>",
                        content: "<?php echo lang::trans('tour_8_desc') ?>",
                        placement: "top"
                    },
                    {
                        element: "#fullscreen",
                        title: "<?php echo lang::trans('tour_9_title') ?>",
                        content: "<?php echo lang::trans('tour_9_desc') ?>",
                        placement: "top"
                    },
                    {
                        element: "#tour-13",
                        title: "<?php echo lang::trans('tour_10_title') ?>",
                        content: "<?php echo lang::trans('tour_10_desc') ?>",
                        placement: "top"
                    },
                    {
                        element: "#tour-14",
                        title: "<?php echo lang::trans('tour_11_title') ?>",
                        content: "<?php echo lang::trans('tour_11_desc') ?>",
                        placement: "top"
                    }
                ],
                container: "body",
                keyboard: true,
                storage: false,
                debug: false,
                show: true,
                backdrop: false,
                redirect: true,
                orphan: false,
                duration: false,
                delay: false,
                basePath: "",
                template: "<div class='popover tour'>" +
                "<div class='arrow'></div>" +
                "<h3 class='popover-title'></h3>" +
                "<div class='popover-content'></div>" +
                "<div class='popover-navigation'>" +
                "<button class='btn btn-primary btn-sm' data-role='prev'><i class='fa fa-angle-double-left'></i> <?php echo lang::trans('tour_prev') ?></button>" +
                "<span data-role='separator'></span>" +
                "<button class='btn btn-primary btn-sm' data-role='next'><?php echo lang::trans('tour_next') ?> <i class='fa fa-angle-double-right'></i></button>" +
                "<span data-role='separator'></span>" +
                "<button class='btn btn-danger btn-sm' data-role='end'><?php echo lang::trans('tour_end') ?></button>" +
                "</div>" +
                "</div>" +
                "</div>",
                afterGetState: function (key, value) {},
                afterSetState: function (key, value) {},
                afterRemoveState: function (key, value) {},
                onStart: function (tour) {},
                onEnd: function (tour) {
                    $('#modal-bootstrap-tour-end').modal(
                        {
                            show: true
                        }
                    );
                },
                onShow: function (tour) {},
                onShown: function (tour) {},
                onHide: function (tour) {},
                onHidden: function (tour) {},
                onNext: function (tour) {},
                onPrev: function (tour) {},
                onPause: function (tour, duration) {},
                onResume: function (tour, duration) {},
                onRedirectError: function (tour) {}
        });

            // Initialize the tour
            tour.init();

            // Start the tour
            tour.start();
        }

    };

}();

// Call main app init
BlankonUiFeatureBootstrapTour.init();</script>
        <!-- START @PAGE LEVEL PLUGINS -->
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/bootstrap-session-timeout/dist/bootstrap-session-timeout.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery.gritter/js/jquery.gritter.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/waypoints/lib/jquery.waypoints.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/counter-up/jquery.counterup.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery-jvectormap-2.0.3.min/index.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jvectormap/tests/assets/jquery-jvectormap-world-mill-en.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/horizontal-chart/build/js/jquery.horizBarChart.min.js"></script>
        <!--/ END PAGE LEVEL PLUGINS -->
        <!-- START @PAGE LEVEL SCRIPTS -->
        <script src="<?php echo URL ?>/style/home/js/apps.js"></script>
        <script src="<?php echo URL ?>/style/home/js/pages/dashboard.retail.js"></script>
        <!--/ END PAGE LEVEL SCRIPTS -->
        <!--/ END JAVASCRIPT SECTION -->

        <!-- START GOOGLE ANALYTICS -->
        <?php echo template::analytics(); ?>
        <!--/ END GOOGLE ANALYTICS -->
		<?php
		if(!empty($_GET['skip_intro']))
		{
			echo '<script>$.gritter.add({
								title: \''.lang::trans('index_disabled_notification').'\',
								text: \''.lang::trans('index_disabled_notification_sub').'\',
								image: \'/style/home/images/poro_notification.png\',
								sticky: false,
								time: \'4000\'
							});</script>';
		}
		if(!empty($_GET['new_lang']))
		{
			echo '<script>$.gritter.add({
					title: \''.lang::trans('new_lang_notification').'\',
					text: \''.lang::trans('new_lang_notification_sub').'\',
					image: \'/style/home/images/poro_notification.png\',
					sticky: false,
					time: \'4000\'
			});</script>';
		}
		if(!empty($_GET['new_region']))
		{
			echo '<script>$.gritter.add({
					title: \''.lang::trans('new_region_notification').'\',
					text: \''.lang::trans('new_region_notification_sub').'\',
					image: \'/style/home/images/poro_notification.png\',
					sticky: false,
					time: \'4000\'
			});</script>';
		}
		if(!empty($_GET['error']) && @$_GET['error'] == 'summoner_not_found')
		{
			echo '<script>$.gritter.add({
					title: \''.lang::trans('summoner_not_found').'\',
					text: \''.str_replace('{{region}}',@$_GET['region'],str_replace('{{summoner}}',@$_GET['summoner'],lang::trans('summoner_not_found_sub'))).'\',
					image: \'/style/home/images/poro_notification.png\',
					sticky: false,
					time: \'4000\'
			});</script>';
		}
		?>
    </body>
    <!--/ END BODY -->

</html>
