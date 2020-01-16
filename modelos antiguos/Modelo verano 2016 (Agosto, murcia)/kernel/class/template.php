<?php
class template{
	public static function headerNavBar($user_region)
	{
		global $regions;
		global $lang;
		$return = '<div class="modal fade" id="langSelector" tabindex="-1" role="dialog" aria-hidden="true">
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
					</div>
<header id="header-navbar" class="content-mini content-mini-full">
		<ul class="nav-header pull-right">
			<li>
				<button class="btn btn-default" data-toggle="modal" data-target="#langSelector" type="button">
					<i class="fa fa-language"></i>
				</button>
			</li>
			<li>
				<button class="btn btn-default" data-toggle="layout" data-action="side_overlay_toggle" type="button">
					<i class="fa fa-info"></i>
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
					<i class="glyphicon glyphicon-menu-hamburger"></i>
				</button>
			</li>
			<li class="visible-xs">
				<button class="btn btn-default" data-toggle="class-toggle" data-target=".js-header-search" data-class="header-search-xs-visible" type="button">
					<i class="fa fa-search"></i>
				</button>
			</li>
			<li class="js-header-search header-search">
				
				<div class="form-material form-material-primary input-group remove-margin-t remove-margin-b">
					<div class="input-group-btn">
						<button class="btn btn-default" data-toggle="dropdown" type="button" aria-expanded="false" id="regionBtn">'.strtoupper($user_region).'</button>
						<ul class="dropdown-menu">';
							
							foreach($regions as $regioncount => $regioncode)
							{
								($regioncode == $user_region) ? $regionstatus = ' class="active"':$regionstatus = null;
								$return .= '<li id="search_'.$regioncode.'"'.$regionstatus.'><a tabindex="-1" href="javascript:changestrregion(\''.strtoupper($regioncode).'\')">'.strtoupper($regioncode).'</a></li>
								';
							}
							
						$return .= '</ul>
					</div>
					<input class="form-control" autocomplete="off" type="text" value="'.@$_COOKIE['onlol_lastSummonerSearch'].'" onkeypress="javascript:searchSummoner(event)" id="summonerName" placeholder="'.$lang['searchSummoner'].'">
					<span class="input-group-addon" onclick=""><i class="si si-magnifier"></i></span>
				</div>
			</li>
		</ul>
	</header>';
	return $return;
	}
	public static function footer()
	{
		global $lang;
		global $config;
		if($config['web.tracking.exectime'] == true)
		{
			global $time_start;
			$time_end = microtime(true);
			$time = ' // <b>'.($time_end - $time_start).'</b>';
		}
		else
		{
			$time = null;
		}
		return '<footer id="page-footer" class="content-mini content-mini-full font-s12 bg-gray-lighter clearfix">
		<div class="pull-right">
			'.$lang['footerCopyOwnerCreate'].' <i class="fa fa-heart text-city"></i> '.$lang['footerCopyOwner'].'
		</div>
		<div class="pull-left">
			'.$lang['footerCopy'].' &copy; 2015 - '.date('Y').' '.$time.'</span>
		</div>
	</footer>';
	}
	public static function basehead($pagetitle)
	{
		global $lang;
		global $config;
		return '<title>'.$pagetitle.'</title>
	<meta name="description" content="'.$lang['pageMetaDescription'].'">
	<meta name="author" content="'.$lang['pageMetaAuthor'].'">
	<meta name="robots" content="noindex, nofollow">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
	<link rel="shortcut icon" href="'.$config['web.url'].'/favicon.ico">
	<link rel="icon" type="image/png" href="'.$config['web.url'].'/favicon.ico" sizes="16x16">
	<link rel="icon" type="image/png" href="'.$config['web.url'].'/favicon.ico" sizes="32x32">
	<link rel="icon" type="image/png" href="'.$config['web.url'].'/favicon.ico" sizes="96x96">
	<link rel="icon" type="image/png" href="'.$config['web.url'].'/favicon.ico" sizes="160x160">
	<link rel="icon" type="image/png" href="'.$config['web.url'].'/favicon.ico" sizes="192x192">
	<link rel="apple-touch-icon" sizes="57x57" href="'.$config['web.url'].'/favicon.ico">
	<link rel="apple-touch-icon" sizes="60x60" href="'.$config['web.url'].'/favicon.ico">
	<link rel="apple-touch-icon" sizes="72x72" href="'.$config['web.url'].'/favicon.ico">
	<link rel="apple-touch-icon" sizes="76x76" href="'.$config['web.url'].'/favicon.ico">
	<link rel="apple-touch-icon" sizes="114x114" href="'.$config['web.url'].'/favicon.ico">
	<link rel="apple-touch-icon" sizes="120x120" href="'.$config['web.url'].'/favicon.ico">
	<link rel="apple-touch-icon" sizes="144x144" href="'.$config['web.url'].'/favicon.ico">
	<link rel="apple-touch-icon" sizes="152x152" href="'.$config['web.url'].'/favicon.ico">
	<link rel="apple-touch-icon" sizes="180x180" href="'.$config['web.url'].'/favicon.ico">
	<link rel="stylesheet" href="'.$config['web.url'].'/assets/css/bootstrap.min.css">
	<link rel="stylesheet" id="css-main" href="'.$config['web.url'].'/assets/css/oneui.min-2.1.css">

	<noscript>
		<meta http-equiv="Refresh" content="0;url='.$config['web.url'].'/js_disabled">
	</noscript>';
	}
	public static function sidebar($menuact,$submenuact = null)
	{
		global $lang;
		global $config;
		$menuclass = array();
		($menuact == 'start') ? $menuclass['start'] = ' class="active"':$menuclass['start'] = null;
		($menuact == 'forums') ? $menuclass['forums'] = ' class="active"':$menuclass['forums'] = null;
		
		($menuact == 'rankings') ? $menuclass['rankings'] = ' class="open"':$menuclass['rankings'] = null;
		($submenuact == 'rankingsSummoners') ? $submenuclass['rankingsSummoners'] = ' class="active"':$submenuclass['rankingsSummoners'] = null;
		
		return '<nav id="sidebar">
		<div id="sidebar-scroll">
			<div class="sidebar-content">
				<div class="side-header side-content bg-white-op">
					<button class="btn btn-link text-gray pull-right hidden-md hidden-lg" type="button" data-toggle="layout" data-action="sidebar_close">
						<i class="fa fa-times"></i>
					</button>
					<a class="h5 text-white" href="index.php">
						<i class="fa fa-power-off text-primary"></i> <span class="h4 font-w600 sidebar-mini-hide">nlol</span>
					</a>
						<img style="width:20%;float:right;" draggable="false" src="'.$config['web.url'].'/assets/images/logo2.png">
					
				</div>
			<div class="side-content">
				<ul class="nav-main" id="nav-main">
					<li>
						<a'.$menuclass['start'].' href="'.$config['web.url'].'/start"><i class="fa fa-home"></i><span class="sidebar-mini-hide">'.$lang['menuStart'].'</span></a>
					</li>
					<li>
						<a'.$menuclass['forums'].' href="'.$config['web.url'].'/forums"><i class="si si-book-open"></i><span class="sidebar-mini-hide">'.$lang['menuForums'].'</span></a>
					</li>
					<li class="nav-main-heading"><span class="sidebar-mini-hide">'.$lang['menuStats'].'</span></li>
					
					<li>
						<a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="si si-bar-chart"></i><span class="sidebar-mini-hide">'.$lang['menuCharts'].'</span></a>
						<ul>
							<li>
								<a href="base_ui_widgets.php">Example 1</a>
							</li>
						</ul>
					</li>
					
					<li'.$menuclass['rankings'].'>
						<a class="nav-submenu" data-toggle="nav-submenu" href="#"><i class="fa fa-line-chart"></i><span class="sidebar-mini-hide">'.$lang['menuRankings'].'</span></a>
						<ul>
							<li>
								<a'.$submenuclass['rankingsSummoners'].' href="'.$config['web.url'].'/rankings/summoners"> '.$lang['menuRankingsSummoners'].'</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			</div>
		</div>
	</nav>';
	}
	public static function sideBarRight()
	{
		global $lang;
		global $regions;
		$return = '<aside id="side-overlay">
		<div id="side-overlay-scroll">
			<div class="side-header side-content">
				<button class="btn btn-default pull-right" type="button" data-toggle="layout" data-action="side_overlay_close">
					<i class="fa fa-times"></i>
				</button>
				<span>
					<i class="si si-badge"></i>
					<span class="font-w600 push-10-l">'.$lang['sidebarRightTitle'].'</span>
				</span>
			</div>
			<div class="side-content remove-padding-t">
				<div class="block pull-r-l border-t">
					<ul class="nav nav-tabs nav-tabs-alt nav-justified" data-toggle="tabs">
						<li class="active">
							<a href="#tabs-side-overlay-overview"><i class="fa fa-fw fa-server"></i> '.$lang['regionShardsMenuTitle'].'</a>
						</li>
						<li>
							<a href="#tabs-side-overlay-sales"><i class="fa fa-fw fa-users"></i> '.$lang['recentSummonersMenuTitle'].'</a>
						</li>
					</ul>
					<div class="block-content tab-content">
						<div class="tab-pane fade fade-right in active" id="tabs-side-overlay-overview">
							<div class="block pull-r-l">
								<div class="block-header bg-gray-lighter">
									<h3 class="block-title">'.$lang['regionShardsTitle'].'</h3>
								</div>
								<div class="block-content block-content-full">';
								
								$game_status = core::readjson('game_status');
								foreach($regions as $regioncount => $regioncode)
								{
									if($game_status[$regioncode]['general_status'] == 'online') {$button_mode='success';$button_position='checked';} elseif($game_status[$regioncode]['general_status'] == 'troubles') {$button_mode='warning';$button_position='checked';} elseif($game_status[$regioncode]['general_status'] == 'offline') {$button_mode='danger';$button_position=null;}
									$return .= '<div class="form-group">
										<div class="row" data-toggle="popover" data-html="true" data-placement="top" data-content="<b>'.$lang['regionShardsGame'].'</b>: '.$lang['regionShardsStatus'.ucwords($game_status[$regioncode]['game']['status'])].' </br><b>'.$lang['regionShardsStore'].'</b>: '.$lang['regionShardsStatus'.ucwords($game_status[$regioncode]['store']['status'])].' </br><b>'.$lang['regionShardsWebsite'].'</b>: '.$lang['regionShardsStatus'.ucwords($game_status[$regioncode]['website']['status'])].' </br><b>'.$lang['regionShardsClient'].'</b>: '.$lang['regionShardsStatus'.ucwords($game_status[$regioncode]['client']['status'])].'" data-original-title="'.$lang['regionShardsShow'].'">
											<div class="col-xs-8">
												<div class="font-s13 font-w600">'.$lang['regionName_'.$regioncode].'</div>
												<div class="font-s13 font-w400 text-muted">'.strtoupper($regioncode).'</div>
											</div>
											<div class="col-xs-4 text-right">
												<label class="css-input switch switch-'.$button_mode.'">
												<input type="checkbox" disabled '.$button_position.'><span></span>
												</label>
											</div>
										</div>
									</div>
									';
								}
								$return .= '</div>
							</div>
						</div>
						<div class="tab-pane fade fade-left" id="tabs-side-overlay-sales">
							<div class="block pull-r-l">
								<div class="block-content block-content-full block-content-mini bg-gray-lighter">
									<div class="row">
										<div class="col-xs-12">
											<span class="font-w600 font-s13 text-gray-darker text-uppercase"><i class="glyphicon glyphicon-search"></i> '.$lang['sidebarRightSummonersRecent'].'</span>
										</div>
									</div>
								</div>
								<div class="block-content">
									<ul class="list list-activity pull-r-l" id="lastsummonersearch"></ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</aside>';
	return $return;
	}
	public static function scripts()
	{
		global $config;
		return '<script type="text/javascript" src="'.$config['web.url'].'/assets/js/jquery-1.9.1.js"></script>
		<script src="'.$config['web.url'].'/assets/js/oneui.min-2.1.php"></script>
		<script src="'.$config['web.url'].'/assets/js/onlol.php"></script>
		<script src="'.$config['web.url'].'/assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js"></script>';
	}
	public static function profileHead($summoner_infoName,$summoner_infoLevel,$summoner_infoIcon,$summoner_infoMatches,$summoner_infoTier,$summoner_infoDivision,$summoner_infoMMR,$summoner_infoQuality,$summoner_infoId,$summoner_infoLeague)
	{
		global $lang;
		global $config;
		global $db;
		$return = '<div class="content bg-image" style="background-color: rgba(0,0,0,0.6);">
			<div class="push-50-t push-15 clearfix">
				<div class="push-15-r pull-left animated fadeIn">
					<img draggable="false" class="img-avatar img-avatar-thumb" src="'.$config['web.url'].'/assets/game/summoners/icons/'.$summoner_infoIcon.'.png" alt="">
				</div>
				<h1 class="h2 text-white push-5-t animated zoomIn">'.$summoner_infoName.'</h1>
				<h2 class="h5 text-white-op animated zoomIn">'.$lang['profileHeadLevel'].' '.$summoner_infoLevel.'</h2>
			</div>
		</div>
		<div class="content bg-white border-b">
			<div class="row items-push text-uppercase">
				<div class="col-xs-6 col-sm-3">
					<div class="font-w700 text-gray-darker animated fadeIn">'.$lang['profileHeadMatches'].'</div>
					<a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0)">'.$summoner_infoMatches.'</a>
				</div>
				
				<div class="col-xs-6 col-sm-3">
					<div class="font-w700 text-gray-darker animated fadeIn">'.$lang['profileHeadMMR'].'</div>
					<a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0)">';
					if($summoner_infoLeague != false) { $differenceBetweenLeague = $db->query('SELECT mmr FROM web_medium_mmr WHERE tier="'.$summoner_infoTier.'" AND division="'.$summoner_infoDivision.'"')->fetch_row()[0]-$summoner_infoMMR;if($differenceBetweenLeague > 50) { $return .= '<div data-toggle="popover" data-placement="top" data-content="'.$lang['profilePopoverMMRHighDsc'].'" data-original-title="'.$lang['profilePopoverMMRHigh'].'">'.$lang['profileHeadMMRHigh'].'</div>'; } elseif($differenceBetweenLeague < 50 && $differenceBetweenLeague > -50) { $return .=  '<div data-toggle="popover" data-placement="top" data-content="'.$lang['profilePopoverMMRNormalDsc'].'" data-original-title="'.$lang['profilePopoverMMRNormal'].'">'.$lang['profileHeadMMRNormal'].'</div>'; } elseif($differenceBetweenLeague < -50) { $return .=  '<div data-toggle="popover" data-placement="top" data-content="'.$lang['profilePopoverMMRLowDsc'].'" data-original-title="'.$lang['profilePopoverMMRLow'].'">'.$lang['profileHeadMMRLow'].'</div>'; } } else { $return .=  $lang['profileHeadNoMMR']; }
				$return .= '</a></div>
				
				<div class="col-xs-6 col-sm-3">
					<div class="font-w700 text-gray-darker animated fadeIn">'.str_replace('{{region}}',$_GET['region'],$lang['profileHeadRanking']).'</div>
					<a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0)">';
					if($summoner_infoLeague != false) { $return .= $db->query('SELECT positionRegion FROM lol_summoners_ranking WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_infoId)->fetch_row()[0]; } else { $return .= $lang['profileHeadNoRanking']; }
					$return .= '</a>
				</div>
				
				<div class="col-xs-6 col-sm-3">
					<div class="font-w700 text-gray-darker animated fadeIn">'.$lang['profileHeadQuality'].'</div>
					<div class="text-warning push-10-t animated flipInX" data-toggle="popover" data-placement="left" data-content="'.$lang['profilePopoverQualityDsc'].'" data-original-title="'.$lang['profilePopoverQualityTitle'].'">';
					
					$summonerQualityStars = ($summoner_infoQuality/2);
					for($i = 0; $i<5;$i++)
					{
						if($summonerQualityStars >= 10)
						{
							$summonerQualityStars = $summonerQualityStars-10;
							$return .= '<i class="fa fa-star"></i>';
						}
						elseif($summonerQualityStars >= 5)
						{
							$summonerQualityStars = $summonerQualityStars-5;
							$return .= '<i class="fa fa-star-half-o"></i>';
						}
						elseif(true)
						{
							$return .= '<i class="fa fa-star-o"></i>';
						}
					}
					$return .= '</div>
				</div>
			</div>
		</div>';
		return $return;
	}
}