<?php 
require('../kernel/core.php'); 
if(!empty($_GET['skip_intro']))
{
	$_SESSION['onlol_skip_intro'] = true;
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
		<script>
			/* Server status: 0 Offline, 1 Troubles, 2 Online */
			var NA_STATUS = <?php echo lol::shards('NA') ?>;
			var LAN_STATUS = <?php echo lol::shards('LAN') ?>;
			var LAS_STATUS = <?php echo lol::shards('LAS') ?>;
			var BR_STATUS = <?php echo lol::shards('BR') ?>;
			var EUW_STATUS = <?php echo lol::shards('EUW') ?>;
			var EUNE_STATUS = <?php echo lol::shards('EUNE') ?>;
			var TR_STATUS = <?php echo lol::shards('TR') ?>;
			var RU_STATUS = <?php echo lol::shards('RU') ?>;
			var OCE_STATUS = <?php echo lol::shards('OCE') ?>;
			var KR_STATUS = <?php echo lol::shards('KR') ?>;
			var STATUS = "<?php echo lang::trans('server_status') ?>";
			var STATUS_ONLINE = "<?php echo lang::trans('server_status_online') ?>";
			var STATUS_OFFLINE = "<?php echo lang::trans('server_status_offline') ?>";
			var STATUS_ONLINE_TROUBLES = "<?php echo lang::trans('server_status_online_troubles') ?>";
		</script>
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

                <!-- Start page header -->
                <div class="header-content">
                    <h2><i class="fa fa-home"></i><?php echo lang::trans('home_dash_title') ?> <span><?php echo lang::trans('home_dash_subtitle') ?></span></h2>
                </div><!-- /.header-content -->
                <!--/ End page header -->

                <!-- Start body content -->
                <div class="body-content animated fadeIn" style="background-image: url(<?php echo URL ?>/game/bgs/<?php echo rand(1,11) ?>.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">

                    <div class="row" style=" margin-bottom:-500px;">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="mini-stat clearfix bg-bitbucket">
                                        <span class="mini-stat-icon"><i class="fa fa-tag fg-primary"></i></span><!-- /.mini-stat-icon -->
                                        <div class="mini-stat-info">
                                            <span><?php echo onlol::config('lol_patch') ?></span>
                                            <?php echo lang::trans('home_dash_boxes_patch') ?>
                                        </div><!-- /.mini-stat-info -->
                                    </div><!-- /.mini-stat -->
                                </div>
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
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="mini-stat clearfix bg-bitbucket">
                                        <span class="mini-stat-icon"><i class="fa fa-shopping-cart fg-teals"></i></span><!-- /.mini-stat-icon -->
                                        <div class="mini-stat-info">
                                            <span class="counter"><?php echo number_format($db->query('SELECT id FROM lol_champs_skins')->num_rows,0,',',',') ?></span>
                                            <?php echo lang::trans('home_dash_boxes_skins') ?>
                                        </div>
                                    </div><!-- /.mini-stat-info -->
                                </div><!-- /.mini-stat -->
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-tab shadow">
                                        <div class="panel-heading no-border">
                                            <div class="pull-left">
                                                <h3 class="panel-title"><?php echo lang::trans('home_dash_server_status') ?></h3>
                                            </div>
                                            <div class="pull-right">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li role="presentation" class="active"></li>
                                                </ul>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div><!-- /.panel-heading -->
                                        <div class="panel-body no-padding">
                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane fade in active" id="visualization">
                                                    <div id="map-world-visualization" class="map" style="height: 500px;"></div>
                                                </div>
                                            </div>
                                        </div><!-- /.panel-body -->
                                    </div><!-- /.panel -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
						<?php
						$blue_team_wins = $db->query('SELECT value FROM lol_stats WHERE stat="red_side_wins"')->fetch_row()[0];
						$red_team_wins = $db->query('SELECT value FROM lol_stats WHERE stat="blue_side_wins"')->fetch_row()[0];
						$total_wins = $blue_team_wins+$red_team_wins;
						$percent_blue_team_wins = number_format((($blue_team_wins*100)/$total_wins),0);
						$percent_red_team_wins = number_format((($red_team_wins*100)/$total_wins),0);
						?>
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-6">
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
                                <div class="col-md-6 col-sm-6 col-xs-6">
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
                            <div class="row hidden-xs">
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
													$top_summoners = $db->query('SELECT champ_id,winrate FROM lol_stats_bestchamps ORDER BY winrate DESC LIMIT 14');
													while($row = $top_summoners->fetch_array())
													{
														echo '<li class="current" title="'.onlol::champid_to_champname($row['champ_id']).'">
                                                            <img draggable="false" src="'.URL.'/game/champions/'.onlol::champid_to_champkeyname($row['champ_id']).'/base/0.png" alt="'.onlol::champid_to_champkeyname($row['champ_id']).'"/>
                                                            <span class="bar" data-number="'.$row['winrate'].'"></span>
                                                            <span class="number"><span class="counter">'.$row['winrate'].'</span>%</span>
                                                        </li>';
													}
													?>
                                                    </ul>
                                                </div>
                                                <div class="tab-pane fade" id="tab-store">
                                                    <ul class="chart top-store-chart no-margin" style="height: inherit;">
													<?php
													$top_summoners = $db->query('SELECT name,icon,lp,region FROM lol_stats_bestsummoners ORDER BY lp DESC LIMIT 14');
													while($row = $top_summoners->fetch_array())
													{
														echo '<li class="current" title="'.$row['name'].' ('.strtoupper($row['region']).')">
                                                            <img src="'.URL.'/game/icons/'.$row['icon'].'.png" alt="'.$row['icon'].'"/>
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
