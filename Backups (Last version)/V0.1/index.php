<?php require('kernel/core.php'); ?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js" data-ng-app="MetronicApp"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js" data-ng-app="MetronicApp"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" data-ng-app="MetronicApp">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <title data-ng-bind="'Metronic AngularJS | ' + $state.current.data.pageTitle"></title>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="<?php echo $config['web.url'] ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $config['web.url'] ?>/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $config['web.url'] ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $config['web.url'] ?>/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $config['web.url'] ?>/assets/global/plugins/typeahead/typeahead.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN DYMANICLY LOADED CSS FILES(all plugin and page related styles must be loaded between GLOBAL and THEME css files ) -->
        <link id="ng_load_plugins_before" />
        <!-- END DYMANICLY LOADED CSS FILES -->
        <!-- BEGIN THEME STYLES -->
        <!-- DOC: To use 'rounded corners' style just load 'components-rounded.css' stylesheet instead of 'components.css' in the below style tag -->
        <link href="<?php echo $config['web.url'] ?>/assets/global/css/components.min.css" id="style_components" rel="stylesheet" type="text/css" />
        <link href="<?php echo $config['web.url'] ?>/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $config['web.url'] ?>/assets/layouts/layout3/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $config['web.url'] ?>/assets/layouts/layout3/css/themes/default.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="<?php echo $config['web.url'] ?>/assets/layouts/layout3/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME STYLES -->
        <link rel="shortcut icon" href="favicon.ico" /> </head>
    <!-- END HEAD -->
    <!-- BEGIN BODY -->
    <!-- DOC: Apply "page-header-menu-fixed" class to set the mega menu fixed  -->
    <!-- DOC: Apply "page-header-top-fixed" class to set the top menu fixed  -->

    <body ng-controller="AppController" class="page-on-load page-header-menu-fixed">
        <!-- BEGIN PAGE SPINNER -->
        <div ng-spinner-bar class="page-spinner-bar">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
        <!-- END PAGE SPINNER -->
        <div class="page-wrapper">
            <div class="page-wrapper-row">
                <div class="page-wrapper-top">
                    <!-- BEGIN HEADER -->
                    <div class="page-header"> <!-- BEGIN HEADER TOP -->
						<div class="page-header-top">
							<div class="container">
								<!-- BEGIN LOGO -->
								<div class="page-logo">
									<a ui-sref="dashboard">
										<img src="{{settings.layoutPath}}/img/logo-default.png" alt="logo" class="logo-default"> </a>
								</div>
								<!-- END LOGO -->
								<!-- BEGIN RESPONSIVE MENU TOGGLER -->
								<a href="javascript:;" class="menu-toggler"></a>
								<!-- END RESPONSIVE MENU TOGGLER -->
								<!-- BEGIN TOP NAVIGATION MENU -->
								<div class="top-menu">
									<ul class="nav navbar-nav pull-right">
										<!-- BEGIN TODO DROPDOWN -->
										<li class="dropdown dropdown-language">
											<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
												<img alt="" src="<?php echo $config['web.url'] ?>/assets/images/flags/<?php echo $userLang ?>.png">
												<span class="langname"> <?php echo strtoupper($userLang) ?> </span>
												<i class="fa fa-angle-down"></i>
											</a>
											<ul class="dropdown-menu dropdown-menu-default">
											<?php
											foreach(explode(',',$config['langs']) as $langKey)
											{
												echo '<li>
												<a href="'.$config['web.url'].'/swapLang/'.$langKey.'">
												<img alt="" src="'.$config['web.url'].'/assets/images/flags/'.$langKey.'.png"> '.$lang['langKey.'.$langKey].' </a>
												</li>';
											}
											?>
											</ul>
										</li>
										<li class="droddown dropdown-separator">
											<span class="separator"></span>
										</li>
										<!-- BEGIN INBOX DROPDOWN -->
										<li class="dropdown dropdown-extended dropdown-dark dropdown-inbox">
											<a href="javascript:;" class="dropdown-toggle" dropdown-menu-hover data-toggle="dropdown" data-close-others="true">
												<i class="fa fa-desktop" style="color:red;"></i>
											</a>
											<ul class="dropdown-menu">
												<li>
													<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
														<li>
															<a href="inbox.html?a=view">
																<span class="photo">
																	<img src="{{settings.layoutPath}}/img/avatar2.jpg" class="img-circle" alt=""> </span>
																<span class="subject">
																	<span class="from"> Lisa Wong </span>
																	<span class="time">Just Now </span>
																</span>
																<span class="message"> Vivamus sed auctor nibh congue nibh. auctor nibh auctor nibh... </span>
															</a>
														</li>
														<li>
															<a href="inbox.html?a=view">
																<span class="photo">
																	<img src="{{settings.layoutPath}}/img/avatar3.jpg" class="img-circle" alt=""> </span>
																<span class="subject">
																	<span class="from"> Richard Doe </span>
																	<span class="time">16 mins </span>
																</span>
																<span class="message"> Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
															</a>
														</li>
														<li>
															<a href="inbox.html?a=view">
																<span class="photo">
																	<img src="{{settings.layoutPath}}/img/avatar1.jpg" class="img-circle" alt=""> </span>
																<span class="subject">
																	<span class="from"> Bob Nilson </span>
																	<span class="time">2 hrs </span>
																</span>
																<span class="message"> Vivamus sed nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
															</a>
														</li>
														<li>
															<a href="inbox.html?a=view">
																<span class="photo">
																	<img src="{{settings.layoutPath}}/img/avatar2.jpg" class="img-circle" alt=""> </span>
																<span class="subject">
																	<span class="from"> Lisa Wong </span>
																	<span class="time">40 mins </span>
																</span>
																<span class="message"> Vivamus sed auctor 40% nibh congue nibh... </span>
															</a>
														</li>
														<li>
															<a href="inbox.html?a=view">
																<span class="photo">
																	<img src="{{settings.layoutPath}}/img/avatar3.jpg" class="img-circle" alt=""> </span>
																<span class="subject">
																	<span class="from"> Richard Doe </span>
																	<span class="time">46 mins </span>
																</span>
																<span class="message"> Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
															</a>
														</li>
													</ul>
												</li>
											</ul>
										</li>
										<!-- END INBOX DROPDOWN -->
									</ul>
								</div>
								<!-- END TOP NAVIGATION MENU -->
							</div>
						</div>
						<!-- END HEADER TOP -->
						<!-- BEGIN HEADER MENU -->
						<div class="page-header-menu">
							<div class="container">
								<!-- BEGIN HEADER SEARCH BOX -->
								<form class="search-form" action="extra_search.html" method="GET">
									<div class="input-group" >
										<input type="text" class="form-control" placeholder="Search" name="query">
										<span class="input-group-btn">
											<a href="javascript:;" class="btn submit">
												<i class="icon-magnifier"></i>
											</a>
										</span>
									</div>
								</form>
								<!-- END HEADER SEARCH BOX -->
								<!-- BEGIN MEGA MENU -->
								<!-- DOC: Apply "hor-menu-light" class after the "hor-menu" class below to have a horizontal menu with white background -->
								<!-- DOC: Remove dropdown-menu-hover and data-close-others="true" attributes below to disable the dropdown opening on mouse hover -->
								<div class="hor-menu hor-menu-light">
									<ul class="nav navbar-nav">
										<li class="active">
											<a ui-sref="dashboard">Dashboard</a>
										</li>
										<li class="menu-dropdown mega-menu-dropdown">
											<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="#" class="dropdown-toggle"> AngularJS
												<i class="fa fa-angle-down"></i>
											</a>
											<ul class="dropdown-menu" style="min-width: 410px">
												<li>
													<div class="mega-menu-content">
														<div class="row">
															<div class="col-md-6">
																<ul class="mega-menu-submenu">
																	<li>
																		<h3>AngularJS Features</h3>
																	</li>
																	<li>
																		<a ui-sref="uibootstrap" class="iconify">
																			<i class="icon-puzzle"></i> UI Bootstrap </a>
																	</li>
																	<li>
																		<a ui-sref="fileupload" class="iconify">
																			<i class="icon-paper-clip"></i> File Upload </a>
																	</li>
																	<li>
																		<a ui-sref="uiselect" class="iconify">
																			<i class="icon-check"></i> UI Select </a>
																	</li>
																</ul>
															</div>
															<div class="col-md-6">
																<ul class="mega-menu-submenu">
																	<li>
																		<h3>Sample Column</h3>
																	</li>
																	<li>
																		<a href="#" class="iconify">
																			<i class="icon-cursor-move"></i> Sample Section 1 </a>
																	</li>
																	<li>
																		<a href="#" class="iconify">
																			<i class="icon-pin"></i> Sample Section 1 </a>
																	</li>
																	<li>
																		<a href="#" class="iconify">
																			<i class="icon-bar-chart"></i> Sample Section 1 </a>
																	</li>
																</ul>
															</div>
														</div>
													</div>
												</li>
											</ul>
										</li>
										<li class="menu-dropdown mega-menu-dropdown mega-menu-full">
											<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;" class="dropdown-toggle"> jQuery
												<i class="fa fa-angle-down"></i>
											</a>
											<ul class="dropdown-menu">
												<li>
													<div class="mega-menu-content">
														<div class="row">
															<div class="col-md-3">
																<ul class="mega-menu-submenu">
																	<li>
																		<h3>jQuery Integration</h3>
																	</li>
																	<li>
																		<a ui-sref="formtools">
																			<i class="icon-puzzle"></i> Form Tools </a>
																	</li>
																	<li>
																		<a ui-sref="pickers">
																			<i class="icon-calendar"></i> Date & Time Pickers </a>
																	</li>
																	<li>
																		<a ui-sref="dropdowns">
																			<i class="icon-refresh"></i> Custom Dropdowns </a>
																	</li>
																	<li>
																		<a ui-sref="tree">
																			<i class="icon-share"></i> Tree View </a>
																	</li>
																	<li>
																		<a ui-sref="datatablesmanaged">
																			<i class="icon-tag"></i> Managed Datatables </a>
																	</li>
																	<li>
																		<a ui-sref="datatablesajax">
																			<i class="icon-refresh"></i> Ajax Datatables </a>
																	</li>
																</ul>
															</div>
															<div class="col-md-3">
																<ul class="mega-menu-submenu">
																	<li>
																		<h3>Sample Column</h3>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link </a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link
																			<span class="badge badge-roundless badge-danger">new</span>
																		</a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link </a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link </a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link </a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link </a>
																	</li>
																</ul>
															</div>
															<div class="col-md-3">
																<ul class="mega-menu-submenu">
																	<li>
																		<h3>Sample Column</h3>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link </a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link</a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link</a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link
																			<span class="badge badge-roundless badge-success">new</span>
																		</a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link</a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link</a>
																	</li>
																</ul>
															</div>
															<div class="col-md-3">
																<ul class="mega-menu-submenu">
																	<li>
																		<h3>Sample Column</h3>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link</a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link</a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link</a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link</a>
																	</li>
																	<li>
																		<a href="#">
																			<i class="fa fa-angle-right"></i> Sample Link</a>
																	</li>
																</ul>
															</div>
														</div>
													</div>
												</li>
											</ul>
										</li>
										<li class="menu-dropdown classic-menu-dropdown ">
											<a data-hover="megamenu-dropdown" data-close-others="true" data-toggle="dropdown" href="javascript:;"> Pages
												<i class="fa fa-angle-down"></i>
											</a>
											<ul class="dropdown-menu pull-left">
												<li>
													<a ui-sref="profile.dashboard" id="sidebar_menu_link_profile">
														<i class="icon-user"></i> User Profile </a>
												</li>
												<li>
													<a ui-sref="todo">
														<i class="icon-check"></i> Task & Todo </a>
												</li>
												<li class=" dropdown-submenu">
													<a href="javascript:;">
														<i class="icon-puzzle"></i> Multi Level </a>
													<ul class="dropdown-menu">
														<li class=" ">
															<a href="javascript:;">
																<i class="icon-settings"></i> Item 1 </a>
														</li>
														<li class=" ">
															<a href="javascript:;">
																<i class="icon-user"></i> Item 2 </a>
														</li>
														<li class=" ">
															<a href="javascript:;">
																<i class="icon-globe"></i> Item 3 </a>
														</li>
														<li class=" dropdown-submenu">
															<a href="#">
																<i class="icon-folder"></i> Sub Items </a>
															<ul class="dropdown-menu">
																<li class=" ">
																	<a href="javascript:;"> Item 1 </a>
																</li>
																<li class=" ">
																	<a href="javascript:;"> Item 2 </a>
																</li>
																<li class=" ">
																	<a href="javascript:;"> Item 3 </a>
																</li>
																<li class=" ">
																	<a href="javascript:;"> Item 4 </a>
																</li>
															</ul>
														</li>
														<li class=" ">
															<a href="javascript:;">
																<i class="icon-share"></i> Item 4 </a>
														</li>
														<li class=" ">
															<a href="javascript:;">
																<i class="icon-bar-chart"></i> Item 5 </a>
														</li>
													</ul>
												</li>
												<li>
													<a ui-sref="blank">
														<i class="icon-refresh"></i>
														<span class="title">Blank Page</span>
													</a>
												</li>
											</ul>
										</li>
									</ul>
								</div>
								<!-- END MEGA MENU -->
							</div>
						</div>
						<!-- END HEADER MENU -->
						</div>
                    <!-- END HEADER -->
                </div>
            </div>
            <div class="page-wrapper-row full-height">
                <div class="page-wrapper-middle">
                    <!-- BEGIN CONTAINER -->
                    <div class="page-container">
                        <!-- BEGIN PAGE CONTENT -->
                        <div class="page-content" style="background: url(assets/images/background.jpg) no-repeat center center fixed; -webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;">
                            <div class="container">
                                <!-- BEGIN ACTUAL CONTENT -->
                                <div ui-view class="fade-in-up"> </div>
                                <!-- END ACTUAL CONTENT -->
                            </div>
                        </div>
                        <!-- END PAGE CONTENT -->
                    </div>
                    <!-- END CONTAINER -->
                </div>
            </div>
            <div class="page-wrapper-row">
                <div class="page-wrapper-bottom">
					<div class="page-footer">
						<div class="container"><?php echo date('Y').' '.$lang['footer.copy'] ?> </div>
					</div>
					<div class="scroll-to-top">
						<i class="icon-arrow-up"></i>
					</div>
                </div>
            </div>
        </div>
        <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
        <!-- BEGIN CORE JQUERY PLUGINS -->
        <!--[if lt IE 9]>
	<script src="assets/global/plugins/respond.min.js"></script>
	<script src="assets/global/plugins/excanvas.min.js"></script> 
	<![endif]-->
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE JQUERY PLUGINS -->
        <!-- BEGIN CORE ANGULARJS PLUGINS -->
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/angularjs/angular.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/angularjs/angular-sanitize.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/angularjs/angular-touch.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/angularjs/plugins/angular-ui-router.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/angularjs/plugins/ocLazyLoad.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/global/plugins/angularjs/plugins/ui-bootstrap-tpls.min.js" type="text/javascript"></script>
        <!-- END CORE ANGULARJS PLUGINS -->
        <!-- BEGIN APP LEVEL ANGULARJS SCRIPTS -->
        <script src="<?php echo $config['web.url'] ?>/assets/js/main.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/js/directives.js" type="text/javascript"></script>
        <!-- END APP LEVEL ANGULARJS SCRIPTS -->
        <!-- BEGIN APP LEVEL JQUERY SCRIPTS -->
        <script src="<?php echo $config['web.url'] ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/layouts/layout3/scripts/layout.min.js" type="text/javascript"></script>
        <script src="<?php echo $config['web.url'] ?>/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
        <!-- END APP LEVEL JQUERY SCRIPTS -->
        <!-- END JAVASCRIPTS -->
    <!-- Google Code for Universal Analytics -->
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
	  ga('create', 'UA-55250743-2', 'auto');
	  ga('send', 'pageview');
	</script>
	<!-- End -->
</body>
</html>