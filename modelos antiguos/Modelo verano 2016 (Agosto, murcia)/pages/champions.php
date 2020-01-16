<?php
require('kernel/core.php');
if(empty($_GET['key']))
{
	header('Location: '.$config['web.url'].'/?error=champ_not_found');
}
if($db->query('SELECT id FROM lol_champions WHERE keyname="'.$_GET['key'].'" AND lang="'.$user_lang.'"')->num_rows == 0)
{
	header('Location: '.$config['web.url'].'/?error=champ_not_found');
}
else
{
	$champ_info=$db->query('SELECT * FROM lol_champions WHERE keyname="'.$_GET['key'].'" AND lang="'.$user_lang.'"')->fetch_array();
	$champ_info_stats=$db->query('SELECT * FROM lol_champions_stats WHERE keyname="'.$_GET['key'].'"')->fetch_array();
}
?><!DOCTYPE html>
<!--[if IE 9]>         <html class="ie9 no-focus"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-focus"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<title>OneUI - Admin Dashboard Template &amp; UI Framework | DEMO</title>
<meta name="description" content="OneUI - Admin Dashboard Template &amp; UI Framework created by pixelcave and published on Themeforest | This is the demo of OneUI! You need to purchase a license for legal use! | DEMO">
<meta name="author" content="pixelcave">
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
<link rel="shortcut icon" href="<?php echo $config['web.url'] ?>/assets/img/favicons/favicon.png">
<link rel="icon" type="image/png" href="<?php echo $config['web.url'] ?>/assets/img/favicons/favicon-16x16.png" sizes="16x16">
<link rel="icon" type="image/png" href="<?php echo $config['web.url'] ?>/assets/img/favicons/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="<?php echo $config['web.url'] ?>/assets/img/favicons/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/png" href="<?php echo $config['web.url'] ?>/assets/img/favicons/favicon-160x160.png" sizes="160x160">
<link rel="icon" type="image/png" href="<?php echo $config['web.url'] ?>/assets/img/favicons/favicon-192x192.png" sizes="192x192">
<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $config['web.url'] ?>/assets/img/favicons/apple-touch-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $config['web.url'] ?>/assets/img/favicons/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $config['web.url'] ?>/assets/img/favicons/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $config['web.url'] ?>/assets/img/favicons/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $config['web.url'] ?>/assets/img/favicons/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $config['web.url'] ?>/assets/img/favicons/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $config['web.url'] ?>/assets/img/favicons/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $config['web.url'] ?>/assets/img/favicons/apple-touch-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $config['web.url'] ?>/assets/img/favicons/apple-touch-icon-180x180.png">
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">
<link rel="stylesheet" href="<?php echo $config['web.url'] ?>/assets/js/plugins/slick/slick.min.css">
<link rel="stylesheet" href="<?php echo $config['web.url'] ?>/assets/js/plugins/slick/slick-theme.min.css">
<link rel="stylesheet" href="<?php echo $config['web.url'] ?>/assets/css/bootstrap.min-1.4.css">
<link rel="stylesheet" id="css-main" href="<?php echo $config['web.url'] ?>/assets/css/oneui.min-2.1.css">
</head>
<body><div id="page-container" class="sidebar-l sidebar-o side-scroll header-navbar-fixed">
<aside id="side-overlay">
<div id="side-overlay-scroll">
<div class="side-header side-content">
<button class="btn btn-default pull-right" type="button" data-toggle="layout" data-action="side_overlay_close">
<i class="fa fa-times"></i>
</button>
<span>
<img class="img-avatar img-avatar32" src="assets/img/avatars/avatar10.jpg" alt="">
<span class="font-w600 push-10-l">Eric Lawson</span>
</span>
</div>
<div class="side-content remove-padding-t">
<div class="block pull-r-l border-t">
<ul class="nav nav-tabs nav-tabs-alt nav-justified" data-toggle="tabs">
<li class="active">
<a href="#tabs-side-overlay-overview"><i class="fa fa-fw fa-coffee"></i> Overview</a>
</li>
<li>
<a href="#tabs-side-overlay-sales"><i class="fa fa-fw fa-line-chart"></i> Sales</a>
</li>
</ul>
<div class="block-content tab-content">
<div class="tab-pane fade fade-right in active" id="tabs-side-overlay-overview">
<div class="block pull-r-l">
<div class="block-header bg-gray-lighter">
<ul class="block-options">
<li>
<button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
</li>
<li>
<button type="button" data-toggle="block-option" data-action="content_toggle"></button>
</li>
</ul>
<h3 class="block-title">Recent Activity</h3>
</div>
<div class="block-content">
<ul class="list list-activity">
<li>
<i class="si si-wallet text-success"></i>
<div class="font-w600">New sale ($15)</div>
<div><a href="javascript:void(0)">Admin Template</a></div>
<div><small class="text-muted">3 min ago</small></div>
</li>
<li>
<i class="si si-pencil text-info"></i>
<div class="font-w600">You edited the file</div>
<div><a href="javascript:void(0)"><i class="fa fa-file-text-o"></i> Documentation.doc</a></div>
<div><small class="text-muted">15 min ago</small></div>
</li>
<li>
<i class="si si-close text-danger"></i>
<div class="font-w600">Project deleted</div>
<div><a href="javascript:void(0)">Line Icon Set</a></div>
<div><small class="text-muted">4 hours ago</small></div>
</li>
</ul>
</div>
</div>
<div class="block pull-r-l">
<div class="block-header bg-gray-lighter">
<ul class="block-options">
<li>
<button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
</li>
<li>
<button type="button" data-toggle="block-option" data-action="content_toggle"></button>
</li>
</ul>
<h3 class="block-title">Online Friends</h3>
</div>
<div class="block-content block-content-full">
<ul class="nav-users remove-margin-b">
<li>
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar1.jpg" alt="">
<i class="fa fa-circle text-success"></i> Ann Parker
<div class="font-w400 text-muted"><small>Copywriter</small></div>
</a>
</li>
<li>
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar12.jpg" alt="">
<i class="fa fa-circle text-success"></i> Jack Greene
<div class="font-w400 text-muted"><small>Web Developer</small></div>
</a>
</li>
<li>
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar7.jpg" alt="">
<i class="fa fa-circle text-success"></i> Amanda Powell
<div class="font-w400 text-muted"><small>Web Designer</small></div>
</a>
</li>
<li>
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar2.jpg" alt="">
<i class="fa fa-circle text-warning"></i> Tiffany Kim
<div class="font-w400 text-muted"><small>Photographer</small></div>
</a>
</li>
<li>
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar13.jpg" alt="">
<i class="fa fa-circle text-warning"></i> Eric Lawson
<div class="font-w400 text-muted"><small>Graphic Designer</small></div>
</a>
</li>
</ul>
</div>
</div>
<div class="block pull-r-l">
<div class="block-header bg-gray-lighter">
<ul class="block-options">
<li>
<button type="button" data-toggle="block-option" data-action="content_toggle"></button>
</li>
</ul>
<h3 class="block-title">Quick Settings</h3>
</div>
<div class="block-content">
<form class="form-bordered" action="index.php" method="post" onsubmit="return false;">
<div class="form-group">
<div class="row">
<div class="col-xs-8">
<div class="font-s13 font-w600">Online Status</div>
<div class="font-s13 font-w400 text-muted">Show your status to all</div>
</div>
<div class="col-xs-4 text-right">
<label class="css-input switch switch-sm switch-primary push-10-t">
<input type="checkbox"><span></span>
</label>
</div>
</div>
</div>
<div class="form-group">
<div class="row">
<div class="col-xs-8">
<div class="font-s13 font-w600">Auto Updates</div>
<div class="font-s13 font-w400 text-muted">Keep up to date</div>
</div>
<div class="col-xs-4 text-right">
<label class="css-input switch switch-sm switch-primary push-10-t">
<input type="checkbox"><span></span>
</label>
</div>
</div>
</div>
<div class="form-group">
<div class="row">
<div class="col-xs-8">
<div class="font-s13 font-w600">Notifications</div>
<div class="font-s13 font-w400 text-muted">Do you need them?</div>
</div>
<div class="col-xs-4 text-right">
<label class="css-input switch switch-sm switch-primary push-10-t">
<input type="checkbox" checked><span></span>
</label>
</div>
</div>
</div>
<div class="form-group">
<div class="row">
<div class="col-xs-8">
<div class="font-s13 font-w600">API Access</div>
<div class="font-s13 font-w400 text-muted">Enable/Disable access</div>
</div>
<div class="col-xs-4 text-right">
<label class="css-input switch switch-sm switch-primary push-10-t">
<input type="checkbox" checked><span></span>
</label>
</div>
</div>
</div>
</form>
</div>
</div>
</div>
<div class="tab-pane fade fade-left" id="tabs-side-overlay-sales">
<div class="block pull-r-l">
<div class="block-content pull-t">
<div class="row items-push">
<div class="col-xs-6">
<div class="font-w700 text-gray-darker text-uppercase">Sales</div>
<a class="h3 font-w300 text-primary" href="javascript:void(0)">22030</a>
</div>
<div class="col-xs-6">
<div class="font-w700 text-gray-darker text-uppercase">Balance</div>
<a class="h3 font-w300 text-primary" href="javascript:void(0)">$ 4.589,00</a>
</div>
</div>
</div>
<div class="block-content block-content-full block-content-mini bg-gray-lighter">
<div class="row">
<div class="col-xs-6">
<span class="font-w600 font-s13 text-gray-darker text-uppercase">Today</span>
</div>
<div class="col-xs-6 text-right">
<span class="font-s13 text-muted">$996</span>
</div>
</div>
</div>
<div class="block-content">
<ul class="list list-activity pull-r-l">
<li>
<i class="fa fa-circle text-success"></i>
<div class="font-w600">New sale! + $249</div>
<div><small class="text-muted">3 min ago</small></div>
</li>
<li>
<i class="fa fa-circle text-success"></i>
<div class="font-w600">New sale! + $129</div>
<div><small class="text-muted">50 min ago</small></div>
</li>
<li>
<i class="fa fa-circle text-success"></i>
<div class="font-w600">New sale! + $119</div>
<div><small class="text-muted">2 hours ago</small></div>
</li>
<li>
<i class="fa fa-circle text-success"></i>
<div class="font-w600">New sale! + $499</div>
<div><small class="text-muted">3 hours ago</small></div>
</li>
</ul>
</div>
<div class="block-content block-content-full block-content-mini bg-gray-lighter">
<div class="row">
<div class="col-xs-6">
<span class="font-w600 font-s13 text-gray-darker text-uppercase">Yesterday</span>
</div>
<div class="col-xs-6 text-right">
<span class="font-s13 text-muted">$765</span>
</div>
</div>
</div>
<div class="block-content">
<ul class="list list-activity pull-r-l">
<li>
<i class="fa fa-circle text-success"></i>
<div class="font-w600">New sale! + $249</div>
<div><small class="text-muted">26 hours ago</small></div>
</li>
<li>
<i class="fa fa-circle text-danger"></i>
<div class="font-w600">Product Purchase - $50</div>
<div><small class="text-muted">28 hours ago</small></div>
</li>
<li>
<i class="fa fa-circle text-success"></i>
<div class="font-w600">New sale! + $119</div>
<div><small class="text-muted">29 hours ago</small></div>
</li>
<li>
<i class="fa fa-circle text-danger"></i>
<div class="font-w600">Paypal Withdrawal - $300</div>
<div><small class="text-muted">37 hours ago</small></div>
</li>
<li>
<i class="fa fa-circle text-success"></i>
<div class="font-w600">New sale! + $129</div>
<div><small class="text-muted">39 hours ago</small></div>
</li>
<li>
<i class="fa fa-circle text-success"></i>
<div class="font-w600">New sale! + $119</div>
<div><small class="text-muted">45 hours ago</small></div>
</li>
<li>
<i class="fa fa-circle text-success"></i>
<div class="font-w600">New sale! + $499</div>
<div><small class="text-muted">46 hours ago</small></div>
</li>
</ul>
</div>
<div class="text-center">
<small><a href="javascript:void(0)">Load More..</a></small>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</aside><nav id="sidebar">
<div id="sidebar-scroll">
<div class="sidebar-content">
<div class="side-header side-content bg-white-op">
<button class="btn btn-link text-gray pull-right hidden-md hidden-lg" type="button" data-toggle="layout" data-action="sidebar_close">
<i class="fa fa-times"></i>
</button>
<div class="btn-group pull-right">
<button class="btn btn-link text-gray dropdown-toggle" data-toggle="dropdown" type="button">
<i class="si si-drop"></i>
</button>
<ul class="dropdown-menu dropdown-menu-right font-s13 sidebar-mini-hide">
<li>
<a data-toggle="theme" data-theme="default" tabindex="-1" href="javascript:void(0)">
<i class="fa fa-circle text-default pull-right"></i> <span class="font-w600">Default</span>
</a>
</li>
<li>
<a data-toggle="theme" data-theme="assets/css/themes/amethyst.min-1.4.css" tabindex="-1" href="javascript:void(0)">
<i class="fa fa-circle text-amethyst pull-right"></i> <span class="font-w600">Amethyst</span>
</a>
</li>
<li>
<a data-toggle="theme" data-theme="assets/css/themes/city.min-1.4.css" tabindex="-1" href="javascript:void(0)">
<i class="fa fa-circle text-city pull-right"></i> <span class="font-w600">City</span>
</a>
</li>
<li>
<a data-toggle="theme" data-theme="assets/css/themes/flat.min-1.4.css" tabindex="-1" href="javascript:void(0)">
<i class="fa fa-circle text-flat pull-right"></i> <span class="font-w600">Flat</span>
</a>
</li>
<li>
<a data-toggle="theme" data-theme="assets/css/themes/modern.min-1.4.css" tabindex="-1" href="javascript:void(0)">
<i class="fa fa-circle text-modern pull-right"></i> <span class="font-w600">Modern</span>
</a>
</li>
<li>
<a data-toggle="theme" data-theme="assets/css/themes/smooth.min-1.4.css" tabindex="-1" href="javascript:void(0)">
<i class="fa fa-circle text-smooth pull-right"></i> <span class="font-w600">Smooth</span>
</a>
</li>
</ul>
</div>
<a class="h5 text-white" href="index.php">
<i class="fa fa-circle-o-notch text-primary"></i> <span class="h4 font-w600 sidebar-mini-hide">ne</span>
</a>
</div>
<div class="side-content">
<ul class="nav-main">
<li>
<a href="index.php"><i class="si si-speedometer"></i><span class="sidebar-mini-hide">Dashboard</span></a>
</li>
<li class="nav-main-heading"><span class="sidebar-mini-hide">User Interface</span></li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-badge"></i><span class="sidebar-mini-hide">UI Elements</span></a>
<ul>
<li>
<a href="base_ui_widgets.php">Widgets</a>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#">Blocks</a>
<ul>
<li>
<a href="base_ui_blocks.php">Styles</a>
</li>
<li>
<a href="base_ui_blocks_api.php">Blocks API</a>
</li>
<li>
<a href="base_ui_blocks_draggable.php">Draggable</a>
</li>
</ul>
</li>
<li>
<a href="base_ui_grid.php">Grid</a>
</li>
<li>
<a href="base_ui_typography.php">Typography</a>
</li>
<li>
<a href="base_ui_icons.php">Icons</a>
</li>
<li>
<a href="base_ui_buttons.php">Buttons</a>
</li>
<li>
<a href="base_ui_activity.php">Activity</a>
</li>
<li>
<a href="base_ui_tabs.php">Tabs</a>
</li>
<li>
<a href="base_ui_tiles.php">Tiles</a>
</li>
<li>
<a href="base_ui_cards.php">Cards</a>
</li>
<li>
<a href="base_ui_ribbons.php">Ribbons</a>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#">Chat</a>
<ul>
<li>
<a href="base_ui_chat_full.php">Full</a>
</li>
<li>
<a href="base_ui_chat_fixed.php">Fixed</a>
</li>
<li>
<a href="base_ui_chat_popup.php">Popup</a>
</li>
</ul>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#">Timeline</a>
<ul>
<li>
<a href="base_ui_timeline.php">Various</a>
</li>
<li>
<a href="base_ui_timeline_social.php">Social</a>
</li>
</ul>
</li>
<li>
<a href="base_ui_navigation.php">Navigation</a>
</li>
<li>
<a href="base_ui_modals_tooltips.php">Modals &amp; Tooltips</a>
</li>
<li>
<a href="base_ui_color_themes.php">Color Themes</a>
</li>
</ul>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-grid"></i><span class="sidebar-mini-hide">Tables</span></a>
<ul>
<li>
<a href="base_tables_styles.php">Styles</a>
</li>
<li>
<a href="base_tables_responsive.php">Responsive</a>
</li>
<li>
<a href="base_tables_tools.php">Tools</a>
</li>
<li>
<a href="base_tables_pricing.php">Pricing</a>
</li>
<li>
<a href="base_tables_datatables.php">DataTables</a>
</li>
</ul>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-note"></i><span class="sidebar-mini-hide">Forms</span></a>
<ul>
<li>
<a href="base_forms_premade.php">Pre-made</a>
</li>
<li>
<a href="base_forms_elements.php">Elements</a>
</li>
<li>
<a href="base_forms_pickers_more.php">Pickers &amp; More</a>
</li>
<li>
<a href="base_forms_editors.php">Text Editors</a>
</li>
<li>
<a href="base_forms_validation.php">Validation</a>
</li>
<li>
<a href="base_forms_wizard.php">Wizard</a>
</li>
</ul>
</li>
<li class="nav-main-heading"><span class="sidebar-mini-hide">Develop</span></li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-wrench"></i><span class="sidebar-mini-hide">Components</span></a>
<ul>
<li>
<a href="base_comp_images.php">Images</a>
</li>
<li>
<a href="base_comp_charts.php">Charts</a>
</li>
<li>
<a href="base_comp_calendar.php">Calendar</a>
</li>
<li>
<a href="base_comp_sliders.php">Sliders</a>
</li>
<li>
<a href="base_comp_animations.php">Animations</a>
</li>
<li>
<a href="base_comp_scrolling.php">Scrolling</a>
</li>
<li>
<a href="base_comp_syntax_highlighting.php">Syntax Highlighting</a>
</li>
<li>
<a href="base_comp_rating.php">Rating</a>
</li>
<li>
<a href="base_comp_treeview.php">Tree View</a>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#">Maps</a>
<ul>
<li>
<a href="base_comp_maps.php">Google</a>
</li>
<li>
<a href="base_comp_maps_full.php">Google Full</a>
</li>
<li>
<a href="base_comp_maps_vector.php">Vector</a>
</li>
</ul>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#">Gallery</a>
<ul>
<li>
<a href="base_comp_gallery_simple.php">Simple</a>
</li>
<li>
<a href="base_comp_gallery_advanced.php">Advanced</a>
</li>
</ul>
</li>
</ul>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-magic-wand"></i><span class="sidebar-mini-hide">Layouts</span></a>
<ul>
<li>
<a href="base_layouts_api.php">Layout API</a>
</li>
<li>
<a href="base_layouts_default.php">Default</a>
</li>
<li>
<a href="base_layouts_default_flipped.php">Default Flipped</a>
</li>
<li>
<a href="base_layouts_header_static.php">Static Header</a>
</li>
<li>
<a href="base_layouts_sidebar_mini_hoverable.php">Mini Sidebar (Hoverable)</a>
</li>
<li>
<a href="base_layouts_side_overlay_hoverable.php">Side Overlay (Hoverable)</a>
</li>
<li>
<a href="base_layouts_side_overlay_open.php">Side Overlay (Open)</a>
</li>
<li>
<a href="base_layouts_side_native_scrolling.php">Side Native Scrolling</a>
</li>
<li>
<a href="base_layouts_sidebar_hidden.php">Hidden Sidebar</a>
</li>
</ul>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-puzzle"></i><span class="sidebar-mini-hide">Multi Level Menu</span></a>
<ul>
<li>
<a href="#">Link 1-1</a>
</li>
<li>
<a href="#">Link 1-2</a>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#">Sub Level 2</a>
<ul>
<li>
<a href="#">Link 2-1</a>
</li>
<li>
<a href="#">Link 2-2</a>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#">Sub Level 3</a>
<ul>
<li>
<a href="#">Link 3-1</a>
</li>
<li>
<a href="#">Link 3-2</a>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#">Sub Level 4</a>
<ul>
<li>
<a href="#">Link 4-1</a>
</li>
<li>
<a href="#">Link 4-2</a>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#">Sub Level 5</a>
<ul>
<li>
<a href="#">Link 5-1</a>
</li>
<li>
<a href="#">Link 5-2</a>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#">Sub Level 6</a>
<ul>
<li>
<a href="#">Link 6-1</a>
</li>
<li>
<a href="#">Link 6-2</a>
</li>
</ul>
</li>
</ul>
</li>
</ul>
</li>
</ul>
</li>
</ul>
</li>
</ul>
</li>
<li class="nav-main-heading"><span class="sidebar-mini-hide">Pages</span></li>
<li class="open">
<a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-layers"></i><span class="sidebar-mini-hide">Generic</span></a>
<ul>
<li>
<a href="base_pages_blank.php">Blank</a>
</li>
<li>
<a href="base_pages_search.php">Search Results</a>
</li>
<li>
<a href="base_pages_invoice.php">Invoice</a>
</li>
<li>
<a href="base_pages_faq.php">FAQ</a>
</li>
<li>
<a href="base_pages_inbox.php">Inbox</a>
</li>
<li>
<a href="base_pages_files.php">Files</a>
</li>
<li>
<a href="base_pages_tickets.php">Tickets</a>
</li>
<li class="open">
<a class="nav-submenu" data-toggle="nav-submenu" href="#">User Profile</a>
<ul>
<li>
<a href="base_pages_profile.php">Profile</a>
</li>
<li>
<a class="active" href="base_pages_profile_v2.php">Profile v2</a>
</li>
<li>
<a href="base_pages_profile_edit.php">Profile Edit</a>
</li>
</ul>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#">Forum</a>
<ul>
<li>
<a href="base_pages_forum_categories.php">Categories</a>
</li>
<li>
<a href="base_pages_forum_topics.php">Topics</a>
</li>
<li>
<a href="base_pages_forum_discussion.php">Discussion</a>
</li>
<li>
<a href="base_pages_forum_new_topic.php">New Topic</a>
</li>
</ul>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#">Authentication</a>
<ul>
<li>
<a href="base_pages_login.php">Log In</a>
</li>
<li>
<a href="base_pages_login_v2.php">Log In v2</a>
</li>
<li>
<a href="base_pages_register.php">Register</a>
</li>
<li>
<a href="base_pages_register_v2.php">Register v2</a>
</li>
<li>
<a href="base_pages_lock.php">Lock Screen</a>
</li>
<li>
<a href="base_pages_lock_v2.php">Lock Screen v2</a>
</li>
<li>
<a href="base_pages_reminder.php">Password Reminder</a>
</li>
<li>
<a href="base_pages_reminder_v2.php">Password Reminder v2</a>
</li>
</ul>
</li>
<li>
<a href="base_pages_coming_soon.php">Coming Soon</a>
</li>
<li>
<a href="base_pages_maintenance.php">Maintenance</a>
</li>
</ul>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-bag"></i><span class="sidebar-mini-hide">e-Commerce</span></a>
<ul>
<li>
<a href="base_pages_ecom_dashboard.php">Dashboard</a>
</li>
<li>
<a href="base_pages_ecom_orders.php">Orders</a>
</li>
<li>
<a href="base_pages_ecom_order.php">Order</a>
</li>
<li>
<a href="base_pages_ecom_products.php">Products</a>
</li>
<li>
<a href="base_pages_ecom_product_edit.php">Product Edit</a>
</li>
<li>
<a href="base_pages_ecom_customer.php">Customer</a>
</li>
</ul>
</li>
<li>
<a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-fire"></i><span class="sidebar-mini-hide">Error Pages</span></a>
<ul>
<li>
<a href="base_pages_400.php">400</a>
</li>
<li>
<a href="base_pages_401.php">401</a>
</li>
<li>
<a href="base_pages_403.php">403</a>
</li>
<li>
<a href="base_pages_404.php">404</a>
</li>
<li>
<a href="base_pages_500.php">500</a>
</li>
<li>
<a href="base_pages_503.php">503</a>
</li>
</ul>
</li>
<li class="nav-main-heading"><span class="sidebar-mini-hide">Apps</span></li>
<li>
<a href="frontend_home.php"><i class="si si-rocket"></i><span class="sidebar-mini-hide">Frontend</span></a>
</li>
<li>
<a href="/oneui-angularjs"><i class="si si-plane"></i><span class="sidebar-mini-hide">AngularJS Version</span></a>
</li>
</ul>
</div>
</div>
</div>
</nav><header id="header-navbar" class="content-mini content-mini-full">
<ul class="nav-header pull-right">
<li>
<div class="btn-group">
<button class="btn btn-default btn-image dropdown-toggle" data-toggle="dropdown" type="button">
<img src="assets/img/avatars/avatar10.jpg" alt="Avatar">
<span class="caret"></span>
</button>
<ul class="dropdown-menu dropdown-menu-right">
<li class="dropdown-header">Profile</li>
<li>
<a tabindex="-1" href="base_pages_inbox.php">
<i class="si si-envelope-open pull-right"></i>
<span class="badge badge-primary pull-right">3</span>Inbox
</a>
</li>
<li>
<a tabindex="-1" href="base_pages_profile.php">
<i class="si si-user pull-right"></i>
<span class="badge badge-success pull-right">1</span>Profile
</a>
</li>
<li>
<a tabindex="-1" href="javascript:void(0)">
<i class="si si-settings pull-right"></i>Settings
</a>
</li>
<li class="divider"></li>
<li class="dropdown-header">Actions</li>
<li>
<a tabindex="-1" href="base_pages_lock.php">
<i class="si si-lock pull-right"></i>Lock Account
</a>
</li>
<li>
<a tabindex="-1" href="base_pages_login.php">
<i class="si si-logout pull-right"></i>Log out
</a>
</li>
</ul>
</div>
</li>
<li>
<button class="btn btn-default" data-toggle="layout" data-action="side_overlay_toggle" type="button">
<i class="fa fa-tasks"></i>
</button>
</li>
</ul>
<ul class="nav-header pull-left">
<li class="hidden-md hidden-lg">
<button class="btn btn-default" data-toggle="layout" data-action="sidebar_toggle" type="button">
<i class="fa fa-navicon"></i>
</button>
</li>
<li class="hidden-xs hidden-sm">
<button class="btn btn-default" data-toggle="layout" data-action="sidebar_mini_toggle" type="button">
<i class="fa fa-ellipsis-v"></i>
</button>
</li>
<li>
<button class="btn btn-default pull-right" data-toggle="modal" data-target="#apps-modal" type="button">
<i class="si si-grid"></i>
</button>
</li>
<li class="visible-xs">
<button class="btn btn-default" data-toggle="class-toggle" data-target=".js-header-search" data-class="header-search-xs-visible" type="button">
<i class="fa fa-search"></i>
</button>
</li>
<li class="js-header-search header-search">
<form class="form-horizontal" action="base_pages_search.php" method="post">
<div class="form-material form-material-primary input-group remove-margin-t remove-margin-b">
<input class="form-control" type="text" id="base-material-text" name="base-material-text" placeholder="Search..">
<span class="input-group-addon"><i class="si si-magnifier"></i></span>
</div>
</form>
</li>
</ul>
</header><main id="main-container" style="background-image: url(<?php echo $config['web.url'] ?>/assets/game/champions/splash/<?php echo $champ_info['keyname'] ?>_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;"><div class="content content-boxed">
<div class="block" style="background-color: rgba(255,255,255,0.15);">
<div class="block-content bg-primary-dark-op text-center overflow-hidden" style="background-color: transparent;">
<div class="push-30-t push animated fadeInDown">
<img class="img-avatar img-avatar96 img-avatar-thumb" src="<?php echo $config['web.url'] ?>/assets/game/champions/square/<?php echo $champ_info['keyname'] ?>.png" alt="">
</div>
<div class="push-30 animated fadeInUp">
<h2 class="h4 font-w600 text-white push-5"><?php echo $champ_info['champ_name'] ?></h2>
<h3 class="h5 text-gray"><?php echo $champ_info['title'] ?></h3>
</div>
</div>
<div class="block-content text-center" style="background-color: rgba(255,255,255,0.95);">
<div class="row items-push text-uppercase">
<div class="col-xs-6 col-sm-3">
<div class="font-w700 text-gray-darker animated fadeIn">Sales</div>
<a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0)">22000</a>
</div>
<div class="col-xs-6 col-sm-3">
<div class="font-w700 text-gray-darker animated fadeIn">Products</div>
<a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0)">16</a>
</div>
<div class="col-xs-6 col-sm-3">
<div class="font-w700 text-gray-darker animated fadeIn">Followers</div>
<a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0)">2600</a>
</div>
<div class="col-xs-6 col-sm-3">
<div class="font-w700 text-gray-darker animated fadeIn">3603 Ratings</div>
<div class="text-warning push-10-t animated flipInX">
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
</div>
</div>
</div>
</div>
</div>
<div class="row">
<div class="col-sm-5 col-sm-push-7 col-lg-4 col-lg-push-8">

<div class="block">
<div class="block-content block-content-full text-center">
<label for="material-text"><?php echo $lang['champion_attack'] ?></label>
<div class="progress">
  <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar"
  aria-valuenow="<?php echo $champ_info_stats['info_attack'] ?>" aria-valuemin="0" aria-valuemax="10" style="width:<?php echo $champ_info_stats['info_attack']*10 ?>%">
    <?php echo $champ_info_stats['info_attack']*10 ?>%
  </div>
</div>
<label for="material-text"><?php echo $lang['champion_defense'] ?></label>
<div class="progress">
  <div class="progress-bar progress-bar-striped progress-bar-success active" role="progressbar"
  aria-valuenow="<?php echo $champ_info_stats['info_defense'] ?>" aria-valuemin="0" aria-valuemax="10" style="width:<?php echo $champ_info_stats['info_defense']*10 ?>%">
    <?php echo $champ_info_stats['info_defense']*10 ?>%
  </div>
</div>
<label for="material-text"><?php echo $lang['champion_magic'] ?></label>
<div class="progress">
  <div class="progress-bar progress-bar-striped progress-bar-info active" role="progressbar"
  aria-valuenow="<?php echo $champ_info_stats['info_magic'] ?>" aria-valuemin="0" aria-valuemax="10" style="width:<?php echo $champ_info_stats['info_magic']*10 ?>%">
    <?php echo $champ_info_stats['info_magic']*10 ?>%
  </div>
</div>
<label for="material-text"><?php echo $lang['champion_difficulty'] ?></label>
<div class="progress">
  <div class="progress-bar progress-bar-striped progress-bar-warning active" role="progressbar"
  aria-valuenow="<?php echo $champ_info_stats['info_difficulty'] ?>" aria-valuemin="0" aria-valuemax="10" style="width:<?php echo $champ_info_stats['info_difficulty']*10 ?>%">
    <?php echo $champ_info_stats['info_difficulty']*10 ?>%
  </div>
</div>
</div>
</div>
<div class="block">
<div class="block-content">
<p><?php echo $champ_info['blurb'] ?> <br><button class="btn btn-info" data-toggle="modal" data-target="#lore" type="button">Leer más...</button></p>

</div>
</div>

<div class="block block-opt-refresh-icon6">
<div class="block-header">
<ul class="block-options">
<li>
<button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
</li>
</ul>
<h3 class="block-title"><i class="fa fa-fw fa-briefcase"></i> Products</h3>
</div>
<div class="block-content">
<ul class="list list-simple list-li-clearfix">
<li>
<a class="item item-rounded pull-left push-10-r bg-info" href="javascript:void(0)">
<i class="si si-rocket text-white-op"></i>
</a>
<h5 class="push-10-t">MyPanel</h5>
<div class="font-s13">Responsive App Template</div>
</li>
<li>
<a class="item item-rounded pull-left push-10-r bg-amethyst" href="javascript:void(0)">
<i class="si si-calendar text-white-op"></i>
</a>
<h5 class="push-10-t">Project Time</h5>
<div class="font-s13">Web application</div>
</li>
<li>
<a class="item item-rounded pull-left push-10-r bg-danger" href="javascript:void(0)">
<i class="si si-speedometer text-white-op"></i>
</a>
<h5 class="push-10-t">iDashboard</h5>
<div class="font-s13">Bootstrap Admin Template</div>
</li>
</ul>
<div class="text-center push">
<small><a href="javascript:void(0)">View More..</a></small>
</div>
</div>
</div>
<div class="block block-opt-refresh-icon6">
<div class="block-header">
<ul class="block-options">
<li>
<button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
</li>
</ul>
<h3 class="block-title"><i class="fa fa-fw fa-star"></i> Ratings</h3>
</div>
<div class="block-content">
<ul class="list list-simple">
<li>
<div class="push-5 clearfix">
<div class="text-warning pull-right">
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
</div>
<a class="font-w600" href="base_pages_profile.php">Julia Cole</a>
<span class="text-muted">(5/5)</span>
</div>
<div class="font-s13">Flawless design execution! I'm really impressed with the product, it really helped me build my app so fast! Thank you!</div>
</li>
<li>
<div class="push-5 clearfix">
<div class="text-warning pull-right">
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
</div>
<a class="font-w600" href="base_pages_profile.php">Laura Bell</a>
<span class="text-muted">(5/5)</span>
</div>
<div class="font-s13">Great value for money and awesome support! Would buy again and again! Thanks!</div>
</li>
<li>
<div class="push-5 clearfix">
<div class="text-warning pull-right">
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
<i class="fa fa-star"></i>
</div>
<a class="font-w600" href="base_pages_profile.php">Laura Bell</a>
<span class="text-muted">(5/5)</span>
</div>
<div class="font-s13">Working great in all my devices, quality and quantity in a great package! Thank you!</div>
</li>
</ul>
<div class="text-center push">
<small><a href="javascript:void(0)">Read More..</a></small>
</div>
</div>
</div>
</div>
<div class="col-sm-7 col-sm-pull-5 col-lg-8 col-lg-pull-4">
<div class="block block-opt-refresh-icon6">
<div class="block-header">
<ul class="block-options">
<li>
<button type="button" data-toggle="block-option" data-action="fullscreen_toggle"></button>
</li>
<li>
<button type="button" data-toggle="block-option" data-action="refresh_toggle" data-action-mode="demo"><i class="si si-refresh"></i></button>
</li>
</ul>
<h3 class="block-title"><i class="fa fa-newspaper-o"></i> Información del campeón</h3>
</div>
<div class="block-content">
<a class="block block-link-hover3" href="javascript:void(0)">

<div class="block-content">
<div class="row items-push text-center">
<div class="col-xs-4">
<div class="push-5"><img src="<?php echo $config['web.url'] ?>/assets/game/champions/kit/<?php echo $champ_info['keyname'] ?>_p.png" data-toggle="modal" data-target="#kit_p"> </div>
<div class="h5 font-w300 text-muted"><?php echo $lang['champion_skill_p'] ?></div>
</div>
<div class="col-xs-2">
<div class="push-5"><img src="<?php echo $config['web.url'] ?>/assets/game/champions/kit/<?php echo $champ_info['keyname'] ?>_q.png" data-toggle="modal" data-target="#kit_q"> </div> 
<div class="h5 font-w300 text-muted"><?php echo $lang['champion_skill_q'] ?></div>
</div>
<div class="col-xs-2">
<div class="push-5"><img src="<?php echo $config['web.url'] ?>/assets/game/champions/kit/<?php echo $champ_info['keyname'] ?>_w.png" data-toggle="modal" data-target="#kit_w"> </div> 
<div class="h5 font-w300 text-muted"><?php echo $lang['champion_skill_w'] ?></div>
</div>
<div class="col-xs-2">
<div class="push-5"><img src="<?php echo $config['web.url'] ?>/assets/game/champions/kit/<?php echo $champ_info['keyname'] ?>_e.png" data-toggle="modal" data-target="#kit_e"> </div> 
<div class="h5 font-w300 text-muted"><?php echo $lang['champion_skill_e'] ?></div>
</div>
<div class="col-xs-2">
<div class="push-5"><img src="<?php echo $config['web.url'] ?>/assets/game/champions/kit/<?php echo $champ_info['keyname'] ?>_r.png" data-toggle="modal" data-target="#kit_r"> </div> 
<div class="h5 font-w300 text-muted"><?php echo $lang['champion_skill_r'] ?></div>
</div>
</div>
</div>
</a>
<div class="block block-transparent pull-r-l">
<div class="block-header bg-gray-lighter">
<ul class="block-options">
<li>
<span><em class="text-muted">4 hrs ago</em></span>
</li>
<li>
<span><i class="fa fa-briefcase text-modern"></i></span>
</li>
</ul>
<h3 class="block-title">3 New Products were added!</h3>
</div>
<div class="block-content block-content-full">
<a class="item item-rounded push-10-r bg-info" data-toggle="tooltip" title="MyPanel" href="javascript:void(0)">
<i class="si si-rocket text-white-op"></i>
</a>
<a class="item item-rounded push-10-r bg-amethyst" data-toggle="tooltip" title="Project Time" href="javascript:void(0)">
<i class="si si-calendar text-white-op"></i>
</a>
<a class="item item-rounded push-10-r bg-city" data-toggle="tooltip" title="iDashboard" href="javascript:void(0)">
<i class="si si-speedometer text-white-op"></i>
</a>
</div>
</div>
<div class="block block-transparent pull-r-l">
<div class="block-header bg-gray-lighter">
<ul class="block-options">
<li>
<span><em class="text-muted">12 hrs ago</em></span>
</li>
<li>
<span><i class="fa fa-twitter text-info"></i></span>
</li>
</ul>
<h3 class="block-title">+ 1150 Followers</h3>
</div>
<div class="block-content">
<p class="font-s13">You’re getting more and more followers, keep it up!</p>
</div>
</div>
<div class="block block-transparent pull-r-l">
<div class="block-header bg-gray-lighter">
<ul class="block-options">
<li>
<span><em class="text-muted">1 day ago</em></span>
</li>
<li>
<span><i class="fa fa-database text-smooth"></i></span>
</li>
</ul>
<h3 class="block-title">Database backup completed!</h3>
</div>
<div class="block-content">
<p class="font-s13">Download the <a href="javascript:void(0)">latest backup</a>.</p>
</div>
</div>
<div class="block block-transparent pull-r-l">
<div class="block-header bg-gray-lighter">
<ul class="block-options">
<li>
<span><em class="text-muted">2 days ago</em></span>
</li>
<li>
<span><i class="fa fa-user-plus text-success"></i></span>
</li>
</ul>
<h3 class="block-title">+ 5 Friend Requests</h3>
</div>
<div class="block-content">
<ul class="nav-users push">
<li>
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar7.jpg" alt="">
<i class="fa fa-circle text-success"></i> Julia Cole
<div class="font-w400 text-muted"><small>Web Designer</small></div>
</a>
</li>
<li>
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar9.jpg" alt="">
<i class="fa fa-circle text-success"></i> Eric Lawson
<div class="font-w400 text-muted"><small>Graphic Designer</small></div>
</a>
</li>
<li>
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar1.jpg" alt="">
<i class="fa fa-circle text-warning"></i> Helen Silva
<div class="font-w400 text-muted"><small>Photographer</small></div>
</a>
</li>
<li>
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar13.jpg" alt="">
<i class="fa fa-circle text-warning"></i> Joshua Munoz
<div class="font-w400 text-muted"><small>Copywriter</small></div>
</a>
</li>
<li>
<a href="base_pages_profile.php">
<img class="img-avatar" src="assets/img/avatars/avatar11.jpg" alt="">
<i class="fa fa-circle text-danger"></i> Joshua Munoz
<div class="font-w400 text-muted"><small>UI Designer</small></div>
</a>
</li>
</ul>
</div>
</div>
<div class="block block-transparent pull-r-l">
<div class="block-header bg-gray-lighter">
<ul class="block-options">
<li>
<span><em class="text-muted">1 week ago</em></span>
</li>
<li>
<span><i class="fa fa-cog text-primary-dark"></i></span>
</li>
</ul>
<h3 class="block-title">System updated to v2.02</h3>
</div>
<div class="block-content">
<p class="font-s13">Check the complete changelog at the <a href="javascript:void(0)">activity page</a>.</p>
</div>
</div>
<div class="block block-transparent pull-r-l">
<div class="block-header bg-gray-lighter">
<ul class="block-options">
<li>
<span><em class="text-muted">2 weeks ago</em></span>
</li>
<li>
<span><i class="fa fa-briefcase text-modern"></i></span>
</li>
</ul>
<h3 class="block-title">1 New Product was added!</h3>
</div>
<div class="block-content block-content-full">
<a class="item item-rounded push-10-r bg-modern" data-toggle="tooltip" title="eSettings" href="javascript:void(0)">
<i class="si si-settings text-white-op"></i>
</a>
</div>
</div>
<div class="block block-transparent pull-r-l">
<div class="block-header bg-gray-lighter">
<ul class="block-options">
<li>
<span><em class="text-muted">2 months ago</em></span>
</li>
<li>
<span><i class="fa fa-cog text-primary-dark"></i></span>
</li>
</ul>
<h3 class="block-title">System updated to v2.01</h3>
</div>
<div class="block-content">
<p class="font-s13">Check the complete changelog at the <a href="javascript:void(0)">activity page</a>.</p>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
<footer id="page-footer" class="content-mini content-mini-full font-s12 bg-gray-lighter clearfix">
<div class="pull-right">
Crafted with <i class="fa fa-heart text-city"></i> by <a class="font-w600" href="http://goo.gl/vNS3I" target="_blank">pixelcave</a>
</div>
<div class="pull-left">
<a class="font-w600" href="http://goo.gl/6LF10W" target="_blank">OneUI 2.1</a> &copy; <span class="js-year-copy"></span>
</div>
</footer>
</div>
<div class="modal fade" id="apps-modal" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-sm modal-dialog modal-dialog-top">
<div class="modal-content">
<div class="block block-themed block-transparent">
<div class="block-header bg-primary-dark">
<ul class="block-options">
<li>
<button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
</li>
</ul>
<h3 class="block-title">Apps</h3>
</div>
<div class="block-content">
<div class="row text-center">
<div class="col-xs-6">
<a class="block block-rounded" href="index.php">
<div class="block-content text-white bg-default">
<i class="si si-speedometer fa-2x"></i>
<div class="font-w600 push-15-t push-15">Backend</div>
</div>
</a>
</div>
<div class="col-xs-6">
<a class="block block-rounded" href="frontend_home.php">
<div class="block-content text-white bg-modern">
<i class="si si-rocket fa-2x"></i>
<div class="font-w600 push-15-t push-15">Frontend</div>
</div>
</a>
</div>
<div class="col-xs-12">
<a class="block block-rounded" href="/oneui-angularjs">
<div class="block-content text-white bg-city">
<i class="si si-plane fa-2x"></i>
<div class="font-w600 push-15-t push-15">AngularJS Version</div>
</div>
</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div><script src="<?php echo $config['web.url'] ?>/assets/js/oneui.min-2.1.js"></script>

<div class="modal fade" id="kit_p" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-dialog-popout">
<div class="modal-content">
<div class="block block-themed block-transparent remove-margin-b">
<div class="block-header bg-primary-dark">
<ul class="block-options">
<li>
<button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
</li>
</ul>
<h3 class="block-title"><?php echo $lang['champion_skill_preview'] ?></h3>
</div>
<div class="block-content">
<video width="100%" height="100%" controls>
  <source src="http://d28xe8vt774jo5.cloudfront.net/abilities/videos/<?php if(strlen($champ_info['champ_id']) == 1){ $videofix = '000'; } if(strlen($champ_info['champ_id']) == 2){ $videofix = '00'; } if(strlen($champ_info['champ_id']) == 3){ $videofix = '0'; } echo @$videofix.$champ_info['champ_id'] ?>_01.mp4" type="video/mp4">
<?php echo $lang['browser_not_supported'] ?>
</video>
<p> <h2> <?php $passive = json_decode($champ_info['spell_p'],true); echo $passive['name'] ?> </h2> <br> <?php echo str_replace(array('class="color','#FFFFFF','#99FF99','#F88017'),array('style="color:#','#000000; font-weight: bold','#0000FF','#FF0000'),$passive['description']) ?> </p>
</div>
</div>
</div>
</div>
</div>

<div class="modal fade" id="kit_q" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-dialog-popout">
<div class="modal-content">
<div class="block block-themed block-transparent remove-margin-b">
<div class="block-header bg-primary-dark">
<ul class="block-options">
<li>
<button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
</li>
</ul>
<h3 class="block-title"><?php echo $lang['champion_skill_preview'] ?></h3>
</div>
<div class="block-content">
<video width="100%" height="100%" controls>
  <source src="http://d28xe8vt774jo5.cloudfront.net/abilities/videos/<?php if(strlen($champ_info['champ_id']) == 1){ $videofix = '000'; } if(strlen($champ_info['champ_id']) == 2){ $videofix = '00'; } if(strlen($champ_info['champ_id']) == 3){ $videofix = '0'; } echo @$videofix.$champ_info['champ_id'] ?>_02.mp4" type="video/mp4">
<?php echo $lang['browser_not_supported'] ?>
</video>
<p> <h2> <?php $spell_q = json_decode($champ_info['spell_q'],true); echo $spell_q['name'] ?> </h2> <br> <?php 
$q_tooltip = $spell_q['tooltip'];
if(count(@$spell_q['effectBurn']))
{
	foreach($spell_q['effectBurn'] as $num => $value)
	{
		$q_tooltip = @str_replace('{{ e'.$num.' }}',$value,$q_tooltip);
		$q_cost_resource = @str_replace('{{ e'.$num.' }}',$value,$spell_q['resource']);
	}
}
if(count(@$spell_q['vars']))
{
	foreach($spell_q['vars'] as $value)
	{
		$q_tooltip = @str_replace('{{ '.$value['key'].' }}',implode($value['coeff']).' * '.$value['link'],$q_tooltip);
	}
}
if(count(@$spell_q['effect']))
{
	foreach($spell_q['effect'] as $num => $value)
	{
		$q_tooltip = @str_replace('{{ f'.$num.' }}',implode('/',$value),$q_tooltip);
	}
}
$q_tooltip = @str_replace('class="color','style="color:#',$q_tooltip);
$q_tooltip = @str_replace(array('spelldamage','bonusattackdamage','attackdamage'),array($lang['champion_ap'],$lang['champion_extraad'],$lang['champion_ad']),$q_tooltip);
$q_tooltip = @str_replace(array('#FFFFFF','#99FF99','#F88017'),array('#000000; font-weight: bold','#0000FF','#FF0000'),$q_tooltip);
echo $q_tooltip;
?> </p>
</div>
</div>
</div>
</div>
</div>

<div class="modal fade" id="kit_w" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-dialog-popout">
<div class="modal-content">
<div class="block block-themed block-transparent remove-margin-b">
<div class="block-header bg-primary-dark">
<ul class="block-options">
<li>
<button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
</li>
</ul>
<h3 class="block-title"><?php echo $lang['champion_skill_preview'] ?></h3>
</div>
<div class="block-content">
<video width="100%" height="100%" controls>
  <source src="http://d28xe8vt774jo5.cloudfront.net/abilities/videos/<?php if(strlen($champ_info['champ_id']) == 1){ $videofix = '000'; } if(strlen($champ_info['champ_id']) == 2){ $videofix = '00'; } if(strlen($champ_info['champ_id']) == 3){ $videofix = '0'; } echo @$videofix.$champ_info['champ_id'] ?>_03.mp4" type="video/mp4">
<?php echo $lang['browser_not_supported'] ?>
</video>
<p> <h2> <?php $spell_w = json_decode($champ_info['spell_w'],true); echo $spell_w['name'] ?> </h2> <br> <?php 
$w_tooltip = $spell_w['tooltip'];
if(count(@$spell_w['effectBurn']))
{
	foreach($spell_w['effectBurn'] as $num => $value)
	{
		$w_tooltip = @str_replace('{{ e'.$num.' }}',$value,$w_tooltip);
		$w_cost_resource = @str_replace('{{ e'.$num.' }}',$value,$spell_w['resource']);
	}
}
if(count(@$spell_w['vars']))
{
	foreach($spell_w['vars'] as $value)
	{
		$w_tooltip = @str_replace('{{ '.$value['key'].' }}',implode($value['coeff']).' * '.$value['link'],$w_tooltip);
	}
}
if(count(@$spell_w['effect']))
{
	foreach($spell_w['effect'] as $num => $value)
	{
		$w_tooltip = @str_replace('{{ f'.$num.' }}',implode('/',$value),$w_tooltip);
	}
}
$w_tooltip = @str_replace('class="color','style="color:#',$w_tooltip);
$w_tooltip = @str_replace(array('spelldamage','bonusattackdamage','attackdamage'),array($lang['champion_ap'],$lang['champion_extraad'],$lang['champion_ad']),$w_tooltip);
$w_tooltip = @str_replace(array('#FFFFFF','#99FF99','#F88017'),array('#000000; font-weight: bold','#0000FF','#FF0000'),$w_tooltip);
echo $w_tooltip;
?> </p>
</div>
</div>
</div>
</div>
</div>

<div class="modal fade" id="kit_e" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-dialog-popout">
<div class="modal-content">
<div class="block block-themed block-transparent remove-margin-b">
<div class="block-header bg-primary-dark">
<ul class="block-options">
<li>
<button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
</li>
</ul>
<h3 class="block-title"><?php echo $lang['champion_skill_preview'] ?></h3>
</div>
<div class="block-content">
<video width="100%" height="100%" controls>
  <source src="http://d28xe8vt774jo5.cloudfront.net/abilities/videos/<?php if(strlen($champ_info['champ_id']) == 1){ $videofix = '000'; } if(strlen($champ_info['champ_id']) == 2){ $videofix = '00'; } if(strlen($champ_info['champ_id']) == 3){ $videofix = '0'; } echo @$videofix.$champ_info['champ_id'] ?>_04.mp4" type="video/mp4">
<?php echo $lang['browser_not_supported'] ?>
</video>
<p> <h2> <?php $spell_e = json_decode($champ_info['spell_e'],true); echo $spell_e['name'] ?> </h2> <br> <?php 
$e_tooltip = $spell_e['tooltip'];
if(count(@$spell_e['effectBurn']))
{
	foreach($spell_e['effectBurn'] as $num => $value)
	{
		$e_tooltip = @str_replace('{{ e'.$num.' }}',$value,$e_tooltip);
	}
}
if(count(@$spell_e['vars']))
{
	foreach($spell_e['vars'] as $value)
	{
		$e_tooltip = @str_replace('{{ '.$value['key'].' }}',implode($value['coeff']).' * '.$value['link'],$e_tooltip);
	}
}
if(count(@$spell_e['effect']))
{
	foreach($spell_e['effect'] as $num => $value)
	{
		$e_tooltip = @str_replace('{{ f'.$num.' }}',implode('/',$value),$e_tooltip);
	}
}
$e_tooltip = @str_replace('class="color','style="color:#',$e_tooltip);
$e_tooltip = @str_replace(array('spelldamage','bonusattackdamage','attackdamage'),array($lang['champion_ap'],$lang['champion_extraad'],$lang['champion_ad']),$e_tooltip);
$e_tooltip = @str_replace(array('#FFFFFF','#99FF99','#F88017'),array('#000000; font-weight: bold','#0000FF','#FF0000'),$e_tooltip);
echo $e_tooltip;
?> </p>
</div>
</div>
</div>
</div>
</div>

<div class="modal fade" id="kit_r" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-dialog-popout">
<div class="modal-content">
<div class="block block-themed block-transparent remove-margin-b">
<div class="block-header bg-primary-dark">
<ul class="block-options">
<li>
<button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
</li>
</ul>
<h3 class="block-title"><?php echo $lang['champion_skill_preview'] ?></h3>
</div>
<div class="block-content">
<video width="100%" height="100%" controls>
  <source src="http://d28xe8vt774jo5.cloudfront.net/abilities/videos/<?php if(strlen($champ_info['champ_id']) == 1){ $videofix = '000'; } if(strlen($champ_info['champ_id']) == 2){ $videofix = '00'; } if(strlen($champ_info['champ_id']) == 3){ $videofix = '0'; } echo @$videofix.$champ_info['champ_id'] ?>_05.mp4" type="video/mp4">
<?php echo $lang['browser_not_supported'] ?>
</video>
<p> <h2> <?php $spell_r = json_decode($champ_info['spell_r'],true); echo $spell_r['name'] ?> </h2> <br> <?php 
$r_tooltip =$spell_r['tooltip'];
if(count(@$spell_r['effectBurn']))
{
	foreach($spell_r['effectBurn'] as $num => $value)
	{
		$r_tooltip = @str_replace('{{ e'.$num.' }}',$value,$r_tooltip);
	}
}
if(count(@$spell_r['vars']))
{
	foreach($spell_r['vars'] as $value)
	{
		$r_tooltip = @str_replace('{{ '.$value['key'].' }}',implode($value['coeff']).' * '.$value['link'],$r_tooltip);
	}
}
if(count(@$spell_r['effect']))
{
	foreach($spell_r['effect'] as $num => $value)
	{
		$r_tooltip = @str_replace('{{ f'.$num.' }}',implode('/',$value),$r_tooltip);
	}
}
$r_tooltip = @str_replace('class="color','style="color:#',$r_tooltip);
$r_tooltip = @str_replace(array('spelldamage','bonusattackdamage','attackdamage'),array($lang['champion_ap'],$lang['champion_extraad'],$lang['champion_ad']),$r_tooltip);
$r_tooltip = @str_replace(array('#FFFFFF','#99FF99','#F88017'),array('#000000; font-weight: bold','#0000FF','#FF0000'),$r_tooltip);
echo $r_tooltip;
?> </p>
</div>
</div>
</div>
</div>
</div>

<div class="modal fade" id="lore" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-dialog-popout">
<div class="modal-content">
<div class="block block-themed block-transparent remove-margin-b">
<div class="block-header bg-primary-dark">
<ul class="block-options">
<li>
<button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
</li>
</ul>
<h3 class="block-title"><?php echo $lang['champion_lore'] ?> - <?php echo $champ_info['champ_name'] ?></h3>
</div>
<div class="block-content">
<p><?php echo $champ_info['lore'] ?></p>
</div>
</div>
</div>
</div>
</div>

</body>
</html>