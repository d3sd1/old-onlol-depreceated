<?php
class template{
	public static function meta()
	{
		return '<!-- START @META SECTION -->
	<meta charset="'.lang::trans('php_lang_encoding').'">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="description" content="'.lang::trans('meta_description').'">
	<meta name="keywords" content="'.lang::trans('meta_keywords').'">
	<meta name="author" content="'.lang::trans('meta_author').'">
	<!--/ END META SECTION -->
	<script language="Javascript">
	//document.oncontextmenu = function(){return false}
	document.onselectstart=function(){
		if (event.srcElement.type != \'text\' && event.srcElement.type != \'textarea\' && event.srcElement.type != \'password\')
		return false
		else return true;
		};
		if (window.sidebar){
		document.onmousedown=function(e){
		var obj=e.target;
		if (obj.tagName.toUpperCase() == \'INPUT\' || obj.tagName.toUpperCase() == \'TEXTAREA\' || obj.tagName.toUpperCase() == \'PASSWORD\')
		return true;

		else
		return false;
		}
	}
	</script>';
	}
	public static function analytics()
	{
		return '<script>
		  (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');

		  ga(\'create\', \'UA-55250743-2\', \'auto\');
		  ga(\'send\', \'pageview\');

		</script>';
	}
	public static function nav_top()
	{
		$return = ' <!-- START @HEADER -->
            <header id="header">

                <!-- Start header left -->
                <div class="header-left">
                    <!-- Start offcanvas left: This menu will take position at the top of template header (mobile only). Make sure that only #header have the `position: relative`, or it may cause unwanted behavior -->
                    <div class="navbar-minimize-mobile left">
                        <i class="fa fa-bars"></i>
                    </div>
                    <!--/ End offcanvas left -->

                    <!-- Start navbar header -->
                    <div class="navbar-header">

                        <!-- Start brand -->
                        <a class="navbar-brand" href="'.URL.'/home/?web=start">
                            <img class="logo" src="'.URL.'/style/logo/175x50.png" draggable="false" alt="brand logo"/>
                        </a><!-- /.navbar-brand -->
                        <!--/ End brand -->

                    </div><!-- /.navbar-header -->
                    <!--/ End navbar header -->

                    <!-- Start offcanvas right: This menu will take position at the top of template header (mobile only). Make sure that only #header have the `position: relative`, or it may cause unwanted behavior -->
                    <div class="navbar-minimize-mobile right">
                        <i class="fa fa-cog"></i>
                    </div>
                    <!--/ End offcanvas right -->

                    <div class="clearfix"></div>
                </div><!-- /.header-left -->
                <!--/ End header left -->

            <script>
		var searchnotfound = \''.lang::trans('search_not_found').'\';
		var search_region = "'.strtolower($_SESSION['onlol_region']).'";
		function switch_search_region(region)
		{
			search_region = region;
			$(document).ready(function(){
				$("#search_region").html(region);
			});
		}
		function searchsummoner() {
			var summoner = document.getElementById(\'summoner\').value;
			if(summoner != "")
			{
				window.location = "'.URL.'/summoner/" + search_region + "/" + summoner;
			}
			else
			{
				$.gritter.add({
                            title: \''.lang::trans('search_error_noinput').'\',
                            text: \''.lang::trans('search_error_noinput_sub').'\',
                            image: \'/style/home/images/poro_notification.png\',
                            sticky: false,
                            time: \'4000\'
                        });
			}
		}
		</script>
		<!-- Start header right -->
                <div class="header-right">
                    <!-- Start navbar toolbar -->
                    <div class="navbar navbar-toolbar">

                        <!-- Start left navigation -->
                        <ul class="nav navbar-nav navbar-left">

                            <!-- Start sidebar shrink -->
                            <li id="tour-4" class="navbar-minimize">
                                <a href="javascript:void(0);" title="'.lang::trans('home_menu_minimize').'">
                                    <i class="fa fa-bars"></i>
                                </a>
                            </li>
                            <!--/ End sidebar shrink -->

                            <!-- Start form search -->
                            <li id="tour-1" class="navbar-search">
                                <!-- Just view on mobile screen-->
                                <a class="trigger-search"><i class="fa fa-search"></i></a>
                                <form class="navbar-form" action="javascript:searchsummoner()" >
                                    <div class="input-group">
                                                        <div class="input-group-btn">
                                                            <button type="button" id="search_region" class="btn btn-success" tabindex="-1">'.$_SESSION['onlol_region'].'</button>
                                                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" tabindex="-1">
                                                                <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu" role="menu">';
															foreach($GLOBALS['lol_servers'] as $region => $reion_code)
															{
																$return .= '<li><a href="javascript:switch_search_region(\''.strtoupper($region).'\')">'.strtoupper($region).'</a></li>';  
															} 
                                                                $return .= '
                                                            </ul>
                                                        </div>
                                                        <input class="form-control no-border-left no-border-right rounded" type="text" autocomplete="off" type="text" id="summoner" placeholder="'.lang::trans('home_menu_search').'">
														<a href="javascript:searchsummoner()" class="input-group-btn"><button class="btn btn-theme form-control-feedback"><i class="fa fa-search" style="margin-left:-2px;"></i></button></a>
                                                    </div>
                                </form>
                            </li>
                            <!--/ End form search -->

                        </ul><!-- /.nav navbar-nav navbar-left -->
                        <!--/ End left navigation -->

                        <!-- Start right navigation -->
                        <ul class="nav navbar-nav navbar-right"><!-- /.nav navbar-nav navbar-right -->

                        <!-- Start last users -->
                        <li id="tour-8" class="dropdown navbar-notification">

                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bell-o"></i><span class="count label label-danger rounded">'.count(@$_SESSION['search_history']).'</span></a>

                            <!-- Start dropdown menu -->
                            <div class="dropdown-menu animated bounce">
                                <div class="dropdown-header">
                                    <span class="title">'.lang::trans('recent_searchs_title').' <strong>('.count(@$_SESSION['search_history']).')</strong></span>
                                </div>
                                <div class="dropdown-body niceScroll" style="overflow: hidden; outline: none;" tabindex="6">

                                    <!-- Start notification list -->
                                    <div class="media-list small">';

                                        if(is_array(@$_SESSION['search_history']))
										{
											foreach($_SESSION['search_history'] as $summoner => $data)
											{
												$return .= '<a href="'.URL.'/summoner/'.$data['region'].'/'.$summoner.'" class="media">
													<div class="media-object pull-left"><img draggable="false" src="'.URL.'/game/icons/'.$data['icon'].'.png"></div>
													<div class="media-body">
														<span class="media-text">'.$summoner.'</span>
														<!-- Start meta icon -->
														<span class="media-meta">'.lang::trans('time_elapsed').' '.onlol::time_elapsed_string($data['time']).'</span>
														<!--/ End meta icon -->
													</div><!-- /.media-body -->
												</a><!-- /.media -->';
											}
										}
										else
										{
											$return .= '<a href="#" class="media">
												<div class="media-object pull-left"><img draggable="false" src="'.URL.'/game/icons/0.png"></div>
												<div class="media-body">
													<span class="media-text">'.lang::trans('no_recent_searchs').'</span>
													<!-- Start meta icon -->
													<span class="media-meta">'.lang::trans('no_recent_searchs_doit').'</span>
													<!--/ End meta icon -->
												</div><!-- /.media-body -->
											</a><!-- /.media -->';
										}
										
                                        $return .= '<!--/ End notification indicator -->

                                    </div>
                                    <!--/ End notification list -->

                                </div>
                            <div id="ascrail2006" class="nicescroll-rails" style="width: 10px; z-index: 1000; cursor: default; position: absolute; top: 37px; left: 290px; height: 281px; display: block; opacity: 0;"><div style="position: relative; top: 0px; float: right; width: 10px; height: 0px; border: 0px; border-radius: 5px; background-color: rgb(66, 66, 66); background-clip: padding-box;"></div></div><div id="ascrail2006-hr" class="nicescroll-rails" style="height: 10px; z-index: 1000; top: 308px; left: 0px; position: absolute; cursor: default; display: none; opacity: 0; width: 290px;"><div style="position: absolute; top: 0px; height: 10px; width: 0px; border: 0px; border-radius: 5px; left: 0px; background-color: rgb(66, 66, 66); background-clip: padding-box;"></div></div></div>
                            <!--/ End dropdown menu -->

                        </li>
                        <!--/ End last users -->

                        <!-- Start lang -->
                        <li id="tour-3" class="dropdown navbar-notification">

                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-language"></i></a>

                            <!-- Start dropdown menu -->
                            <div class="dropdown-menu animated bounce">
                                <div class="dropdown-header">
                                    <span class="title">'.lang::trans('pick_lang').'</span>
                                    <span class="option text-right">'.lang::trans('actual_lang').': <a href="#">'.$GLOBALS['langs'][$_SESSION['onlol_lang']].'</span>
                                </div>
                                <div class="dropdown-body niceScroll">

                                    <!-- Start notification list -->
                                    <div class="media-list small">';
									  foreach($GLOBALS['langs'] as $lang => $lang_desc)
									  {
										$return .= '<a href="'.URL.'/lang/'.$lang.'" class="media">
                                            <div class="media-body">
                                                <span class="media-text" style="vertical-align:middle;"><img width="30px" height="20px" src="'.URL.'/style/global/langs/'.$lang.'.png" draggable="false"> '.$lang_desc.'</span>
                                            </div><!-- /.media-body -->
                                        </a><!-- /.media -->';  
									  } 


                         $return .= '</div>
                                    <!--/ End notification list -->

                                </div>
                            </div>
                            <!--/ End dropdown menu -->

                        </li><!-- /.dropdown navbar-notification -->
                        <!--/ End lang -->
						
						<!-- Start region -->
                        <li id="tour-2" class="dropdown navbar-notification">

                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-globe"></i></a>

                            <!-- Start dropdown menu -->
                            <div class="dropdown-menu animated bounce">
                                <div class="dropdown-header">
                                    <span class="title">'.lang::trans('swap_region').'</span>
                                    <span class="option text-right"> <a href="#">'.$_SESSION['onlol_region'].'</span>
                                </div>
                                <div class="dropdown-body niceScroll">

                                    <!-- Start notification list -->
                                    <div class="media-list small">';
									  foreach($GLOBALS['lol_servers'] as $region => $reion_code)
									  {
										$return .= '<a href="'.URL.'/region/'.$region.'" class="media">
                                            <div class="media-body">
                                                <span class="media-text" style="vertical-align:middle;"> <i class="fa fa-external-link"></i>'.strtoupper($region).'</span>
                                            </div><!-- /.media-body -->
                                        </a><!-- /.media -->';  
									  } 


                         $return .= '</div>
                                    <!--/ End notification list -->

                                </div>
                            </div>
                            <!--/ End dropdown menu -->

                        </li><!-- /.dropdown navbar-notification -->
                        <!--/ End region -->
                        <!--/ End settings -->

                        </ul>
                        <!--/ End right navigation -->

                    </div><!-- /.navbar-toolbar -->
                    <!--/ End navbar toolbar -->
                </div><!-- /.header-right --></header> <!-- /#header -->
            <!--/ END HEADER -->';
				return $return;
	}
	public static function left_menu($actual_page = 'dash')
	{
		$return = '<!--

            START @SIDEBAR LEFT
            |=========================================================================================================================|
            |  TABLE OF CONTENTS (Apply to sidebar left class)                                                                        |
            |=========================================================================================================================|
            |  01. sidebar-box               |  Variant style sidebar left with box icon                                              |
            |  02. sidebar-rounded           |  Variant style sidebar left with rounded icon                                          |
            |  03. sidebar-circle            |  Variant style sidebar left with circle icon                                           |
            |=========================================================================================================================|

            -->
            <aside id="sidebar-left" class="sidebar-rounded">

                <!-- Start left navigation - profile shortcut -->
                <div class="sidebar-content">
                    <div id="tour-5" class="media">';
					if(!empty($_SESSION['onlol_default_user']))
					{
                        $return .= '<a class="pull-left has-notif avatar" href="'.URL.'/summoner/">
                            <img src="../../../img/avatar/50/1.png" alt="admin">
                            <i class="online"></i>
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading">Hello, <span>Lee</span></h4>
                            <small>Web Designer</small>
                        </div>';
					}
					else
					{
						 $return .= '<a class="pull-left has-notif avatar" data-toggle="tooltip" data-placement="right" data-title="'.lang::trans('default_user_tooltip').'" data-original-title="" >
                            <img src="'.URL.'/game/icons/default.png" alt="default">
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading">'.lang::trans('default_user_name').'</h4>
                            <small>'.lang::trans('default_user_name_nouser').'</small>
                        </div>';
					}
                    $return .= '</div>
                </div><!-- /.sidebar-content -->
                <!--/ End left navigation -  profile shortcut -->

                <!-- Start left navigation - menu -->
                <ul class="sidebar-menu" id="tour-10">';

                    $return .= '<!-- Start navigation - dashboard -->
                    <li class="submenu active">
                        <a href="javascript:void(0);">
                            <span class="icon"><i class="fa fa-home"></i></span>
                            <span class="text">ONLoL</span>
                            <span class="arrow"></span>
                            <span class="selected"></span>
                        </a>
                        <ul>
                            <li><a href="dashboard.html">Página principal</a></li>
                            <li><a href="dashboard.html">Página de inicio</a></li>
                            <li><a href="dashboard-ecommerce.html">Buscador avanzado</a></li>
                            <li><a href="dashboard-hr.html">Contáctanos</a></li>
                        </ul>
                    </li>
                    <!--/ End navigation - dashboard -->
				</ul><!-- /.sidebar-menu -->
                <!--/ End left navigation - menu -->
                <!-- Start left navigation - footer -->
                <div class="sidebar-footer hidden-xs hidden-sm hidden-md">';
					$return .= '<!-- id="logout" --> <a href="http://www.google.com"  class="pull-left" data-toggle="tooltip" data-placement="top" data-title="'.lang::trans('submenu_closeweb').'"><i class="fa fa-sign-out"></i></a>
                    <a id="fullscreen" class="pull-left" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-title="'.lang::trans('submenu_fullscreen').'"><i class="fa fa-desktop"></i></a>
                    <a id="tour-13" href="#" onclick="BlankonUiFeatureBootstrapTour.handleTour()" class="pull-left" data-toggle="tooltip" data-placement="top" data-title='.lang::trans('submenu_tour').'"><i class="fa fa-question-circle"></i></a>
                    <a id="tour-14" class="pull-left" href="'.URL.'/?force=true" data-toggle="tooltip" data-placement="top" data-title="'.lang::trans('submenu_go_index').'"><i class="fa fa-home"></i></a>
                </div><!-- /.sidebar-footer -->
                <!--/ End left navigation - footer -->

            </aside><!-- /#sidebar-left -->
            <!--/ END SIDEBAR LEFT -->';
			return $return;
	}
}
