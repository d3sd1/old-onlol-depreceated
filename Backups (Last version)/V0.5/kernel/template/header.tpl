<!DOCTYPE html>
<html lang="<?php echo $lang['core.page.lang'] ?>">
<head>
<meta charset="<?php echo $lang['core.page.encode'] ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo $lang['core.page.description'] ?>">
<meta name="author" content="<?php echo $lang['core.page.author'] ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo URL ?>/favicon.ico">
<title><?php if($pageNameVarKey != null) { echo str_replace($pageNameVarKey,$pageNameVarVal,$lang[$pageName]); } else { echo $lang[$pageName]; } ?></title>
<link href="<?php echo URL ?>/style/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo URL ?>/style/css/sidebar-nav.min.css" rel="stylesheet">
<link href="<?php echo URL ?>/style/css/animate.css" rel="stylesheet">
<link href="<?php echo URL ?>/style/css/toastr.css" rel="stylesheet">
<link href="<?php echo URL ?>/style/css/main.css" rel="stylesheet">
<link href="<?php echo URL ?>/style/css/custom.css" rel="stylesheet">
<!-- Custom page styles -->
<?php echo $pageTemplates ?>

<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.style/js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body > <!-- class="fix-header" -->
<!-- Preloader -->
<div class="preloader">
  <div class="cssload-speeding-wheel"></div>
</div>
<div id="wrapper">
  <!-- Navigation -->
  <script>
		var searchnotfound = 'NOT_FOUND';
		var search_region = "<?php echo strtoupper($userRegion) ?>";
		function switch_search_region(region)
		{
			search_region = region;
			$(document).ready(function(){
				$("#search_region").html(region);
			});
		}
		function searchsummoner() {
			var summoner = document.getElementById('summoner').value;
			if(summoner != "")
			{
				window.location = "<?php echo URL ?>/summoner/" + search_region.toLowerCase() + "/" + summoner + "&searched=true";
			}
			else
			{
				$(document).ready(function() {
					   $.toast({
						heading: '<?php echo $lang['menu.search.error.input'] ?>',
						text: '<?php echo $lang['menu.search.error.input.dsc'] ?>',
						position: 'top-right',
						loaderBg:'#ff6849',
						icon: 'error',
						hideAfter: 5000, 
						stack: 6
					  });
				 

			});
			}
		}
		<?php 
		if($config['web.js.disabletxtsel'] == true)
		{
			echo 'document.onselectstart=new Function ("return false"); if (window.sidebar){document.onmousedown=false;document.onclick=true;}';
		}
		if($config['web.js.disablerightclick'] == true)
		{
			echo 'document.oncontextmenu = function(){return false}';
		}
		?>
		</script>
  <nav class="navbar navbar-default navbar-static-top m-b-0">
    <div class="navbar-header"> <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse"><i class="ti-menu"></i></a>
      <div class="top-left-part"><a class="logo" href="<?php echo URL ?>"><b><img draggable="false" src="<?php echo URL ?>/style/images/logo.png" alt="home" style="margin-top:5px;"/></b></a></div>
      <ul class="nav navbar-top-links navbar-left hidden-xs">
        <li><a href="javascript:void(0)" class="open-close hidden-xs waves-effect waves-light"><i class="icon-arrow-left-circle ti-menu"></i></a></li>
        <li>
          <form role="search" class="app-search hidden-xs" action="javascript:searchsummoner()">
            <input autocomplete="off" id="summoner" type="text" value="<?php echo @$_SESSION['onlol_lastSearch'] ?>" placeholder="<?php echo $lang['menu.search.placeholder'] ?>" class="form-control">
            <a href="javascript:searchsummoner()"><i class="fa fa-search"></i></a>
          </form>
        </li>
		<li class="dropdown"> <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" id="search_region"><?php echo strtoupper($userRegion) ?> <span class="caret"></span></i></a>
          <ul class="dropdown-menu mailbox animated">
		  <?php
		  foreach($servers as $region => $platform)
		  {
			  echo '<li>
              <div class="message-center"> <a href="javascript:switch_search_region(\''.strtoupper($region).'\')">
                <div class="mail-contnet">
                  <h5>'.strtoupper($region).'</h5>
                  <span class="mail-desc">'.$lang['server.name_'.$region].'</span> </div>
                </a> </div>
            </li>';
		  }
		  ?>
          </ul>
          <!-- /.dropdown-messages -->
        </li>
      </ul>
      <ul class="nav navbar-top-links navbar-right pull-right">
	  
	  <li class="dropdown"> <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="true"> <b class="hidden-xs"><img src="<?php echo URL ?>/style/images/flags/<?php echo $userLang ?>.png" /> <?php echo $lang['header.lang.'.$userLang] ?></b> </a>
          <ul class="dropdown-menu">
		  <?php 
		  foreach(explode(',',$config['langs']) as $langCode)
		  {
			echo '<li><a href="'.URL.'/swaplang/'.$langCode.'"><img src="'.URL.'/style/images/flags/'.$langCode.'.png">  '.$lang['header.lang.'.$langCode].'</a></li>';
		  }
		  ?>
          </ul>
          <!-- /.dropdown-user -->
        </li>
		
        <li class="mega-dropdown">
          <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#"><span class="hidden-xs"><?php echo $lang['menu.complete'] ?></span> <i class="icon-options-vertical"></i></a>
          <ul class="dropdown-menu mega-dropdown-menu animated bounceInDown">
            <li class="col-sm-3">
              <ul>
                <li class="dropdown-header"><?php echo $lang['menu.complete.header.champs'] ?></li>
                <li><a href="<?php echo URL ?>/champs/list"><?php echo $lang['menu.complete.champs.full'] ?></a></li>
                <li><a href="<?php echo URL ?>/champs/builds"><?php echo $lang['menu.complete.champs.builds'] ?></a></li>
                <li><a href="<?php echo URL ?>/champs/info"><?php echo $lang['menu.complete.champs.info'] ?></a></li>
                <li><a href="<?php echo URL ?>/champs/stats"><?php echo $lang['menu.complete.champs.stats'] ?></a></li>
				
                
              </ul>
            </li>
            <li class="col-sm-3">
              <ul>
                <li class="dropdown-header"><?php echo $lang['menu.complete.header.summoners'] ?></li>
                <li><a href="<?php echo URL ?>/summoners/top"><?php echo $lang['menu.complete.summoners.top'] ?></a></li>
                <li><a href="<?php echo URL ?>/summoners/search"><?php echo $lang['menu.complete.summoners.search'] ?></a></li>
                <li><a href="<?php echo URL ?>/summoners/streams"><?php echo $lang['menu.complete.summoners.streams'] ?></a></li>
                <li><a href="<?php echo URL ?>/summoners/progames"><?php echo $lang['menu.complete.summoners.progames'] ?></a></li>
                <li><a href="<?php echo URL ?>/summoners/games"><?php echo $lang['menu.complete.summoners.games'] ?></a></li>

              </ul>
            </li>
            <li class="col-sm-3">
              <ul>
                <li class="dropdown-header"><?php echo $lang['menu.complete.header.rankings'] ?></li>
                <li><a href="<?php echo URL ?>/rankings/summoners"><?php echo $lang['menu.complete.rankings.summoners'] ?></a></li>
                <li><a href="<?php echo URL ?>/rankings/champmastery"><?php echo $lang['menu.complete.rankings.champmastery'] ?></a></li>
                <li><a href="<?php echo URL ?>/rankings/champs"><?php echo $lang['menu.complete.rankings.champs'] ?></a></li>
              </ul>
            </li>
            <li class="col-sm-3">
              <ul>
			  
			  
              </ul>
            </li>
			<?php
			   if(is_array(@$_SESSION['onlol_summonersSearched']))
			   {
					echo '<li class="col-sm-12 m-t-40 demo-box">
						<div class="row">';
			   
				   $summonerRecentSearchedColor = 0;
					foreach(array_reverse($_SESSION['onlol_summonersSearched']) as $summonerName => $summonerRegion)
					{
						switch($summonerRecentSearchedColor)
						{
							case 0:
							$boxColor = 'bg-purple';
							break;
							case 1:
							$boxColor = 'bg-success';
							break;
							case 2:
							$boxColor = 'bg-info';
							break;
							case 3:
							$boxColor = 'bg-inverse';
							break;
							case 4:
							$boxColor = 'bg-warning';
							break;
							case 5:
							$boxColor = 'bg-danger';
							break;
						}
						echo '<div class="col-sm-2"><div class="white-box text-center '.$boxColor.'"><a href="'.URL.'/summoner/'.$summonerRegion.'/'.$summonerName.'" class="text-white"><i class="fa fa-user fa-fw" data-icon="v"></i><br/>'.$summonerName.'</a></div></div>';
						$summonerRecentSearchedColor++;
					}
					echo '</div>     
						</li>';
				}
			   ?>
          </ul>
        </li>
        <!-- /.Megamenu -->
      </ul>
    </div>
    <!-- /.navbar-header -->
    <!-- /.navbar-top-links -->
    <!-- /.navbar-static-side -->
  </nav>
  <!-- Left navbar-header -->
  <div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse slimscrollsidebar">
     <ul class="nav" id="side-menu">
        <li class="sidebar-search hidden-sm hidden-md hidden-lg">
          <!-- input-group -->
          <div class="input-group custom-search-form">
		  <div class="input-group-btn">
                        <button type="button" class="btn waves-effect waves-light btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" id="search_region_page"><?php echo strtoupper($userRegion) ?> <span class="caret"></span></button>
                        <ul class="dropdown-menu">
						<?php
						  foreach($servers as $region => $platform)
						  {
							  echo '<li>
							  <div class="message-center"> <a href="javascript:switch_search_region_page(\''.strtoupper($region).'\')">
								<div class="mail-contnet">
								  <h5>'.strtoupper($region).'</h5>
								  <p class="mail-desc">'.$lang['server.name_'.$region].'</p> </div>
								</a> </div>
							</li>';
						  }
						  ?>
                        </ul>
                      </div>
            <form action="javascript:searchsummoner()">
            <input autocomplete="off" id="summoner" type="text" value="<?php echo @$_SESSION['onlol_lastSearch'] ?>" placeholder="<?php echo $lang['menu.search.placeholder'] ?>" class="form-control">
			</form>
            <span class="input-group-btn">
            <button class="btn btn-default" type="button" onclick="javascript:searchsummoner()"> <i class="fa fa-search"></i> </button>
            </span> </div>
          <!-- /input-group -->
        </li>
        <li> <a href="<?php echo URL ?>" class="waves-effect<?php if($pageMenu == 'index') { echo ' active'; } ?>"><i class="linea-icon linea-basic fa-fw" data-icon="v"></i> <span class="hide-menu"> <?php echo $lang['menu.main.start'] ?> <span class="fa arrow"></span> </span></a></li>
		
		<li> <a href="#" class="waves-effect"><i data-icon="/" class="linea-icon linea-basic fa-fw"></i> <span class="hide-menu"><?php echo $lang['menu.main.champs'] ?><span class="fa arrow"></span></span></a>
          <ul class="nav nav-second-level">
            <li><a href="<?php echo URL ?>/champs/list"><?php echo $lang['menu.main.champs.list'] ?></a></li>
            <li><a href="<?php echo URL ?>/champs/stats"><?php echo $lang['menu.main.champs.stats'] ?></a></li>
            <li><a href="<?php echo URL ?>/champs/ranking"><?php echo $lang['menu.main.champs.ranking'] ?></a></li>
          </ul>
        </li>
		
		 <li> <a href="#" class="waves-effect<?php if($pageMenu == 'summoners') { echo ' active'; } ?>"><i data-icon="/" class="linea-icon linea-basic fa-fw"></i> <span class="hide-menu"><?php echo $lang['menu.main.summoners'] ?><span class="fa arrow"></span></span></a>
          <ul class="nav nav-second-level">
            <li><a href="<?php echo URL ?>/summoners/search"><?php echo $lang['menu.main.summoners.search'] ?></a></li>
            <li><a href="<?php echo URL ?>/summoners/top"><?php echo $lang['menu.main.summoners.top'] ?></a></li>
            <li><a href="<?php echo URL ?>/summoners/top/champmastery"><?php echo $lang['menu.main.summoners.champmasteryranking'] ?></a></li>
          </ul>
        </li>
        <li> <a href="<?php echo URL ?>/game/serverstatus" class="waves-effect<?php if($pageMenu == 'serverStatus') { echo ' active'; } ?>"><i data-icon="/" class="linea-icon linea-basic fa-fw"></i> <span class="hide-menu"><?php echo $lang['menu.main.serverstatus'] ?><span class="fa arrow"></span></span></a> </li>
</ul>
    </div>
  </div>