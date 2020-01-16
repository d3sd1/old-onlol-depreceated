<?php
require('core/core.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ONLoL ~ Estado de servidores</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="style/images/favicon.ico"/>

        <!--Fonts-->
        <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,400italic%7CRaleway:400,600' rel='stylesheet' type='text/css'>

        <!--jQuery and plugins-->
        <script type="text/javascript" src="style/js/jquery.min.js"></script>
        <script type="text/javascript" src="style/js/underscore-min.js"></script>
        <script type="text/javascript" src="style/js/backbone-min.js"></script>
        <script type="text/javascript" src="style/js/backbone-paginated-collection.js"></script>
        <script type="text/javascript" src="style/js/isotope.pkgd.min.js"></script>
        <script type="text/javascript" src="style/js/imagesloaded.pkgd.min.js"></script>
        <script type="text/javascript" src="style/js/jquery.waypoints.min.js"></script>
        <script type="text/javascript" src="style/js/jquery.validate.min.js"></script>

        <!--Bootstrap-->
        <link rel="stylesheet" href="style/plugins/bootstrap/css/bootstrap.min.css">
        <script type="text/javascript" src="style/plugins/bootstrap/js/bootstrap.min.js"></script>

        <!--Icons and Animates-->
        <link rel="stylesheet" href="style/plugins/elegant-font/style.css">
        <link rel="stylesheet" href="style/plugins/et-line-font/style.css">
        <link rel="stylesheet" href="style/css/animate.min.css">

        <!--Media Element Player-->
        <link rel="stylesheet" href="style/plugins/mediaelement/mediaelementplayer.min.css">
        <link rel="stylesheet" href="style/css/mediaelementplayer.css">
        <script type="text/javascript" src="style/plugins/mediaelement/mediaelement-and-player.min.js"></script>

        <!--Owl Carousel-->
        <link rel="stylesheet" href="style/plugins/owl-carousel/owl.carousel.css">
        <script type="text/javascript" src="style/plugins/owl-carousel/owl.carousel.min.js"></script>

        <!--Magnific lightbox-->
        <link rel="stylesheet" href="style/plugins/magnific/magnific-popup.css">
        <script type="text/javascript" src="style/plugins/magnific/jquery.magnific-popup.min.js"></script>

        <script type="text/javascript" src="<?php echo URL ?>/style/js/jquery.devrama.slider.js"></script>
		<!-- SWAL -->
        <script type="text/javascript" src="<?php echo URL ?>/style/js/sweetalert.min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo URL ?>/style/css/sweetalert.css">
		<?php echo rclickmenu() ?>
        <!--Stylesheet-->
        <link rel="stylesheet" type="text/css" href="style/css/style.css">
        
        <script type="text/template" id="tpl-item-style1">
            <div class="grid-item tpl-item-style10 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <div class="front" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"><a href="#" class="post-permalink"></a></div>
            <div class="back" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"><a href="#" class="post-permalink"></a></div>
            </div>
            </div>
        </script>
       
        <script type="text/template" id="tpl-single-open-state">
            <div class="open-state viewport-single-post">
            <div class="entry-media"><%= typeof(media)!== 'undefined' ?  media : '' %></div>
            <div class="post-action">
            <span class="actions">
            <a href="javascript:;" class="prev">
            <i class="switch-close arrow_carrot-left"></i>
            <i class="switch-holder icon_grid-3x3"></i>
            </a>
            <a href="javascript:;" class="next"><i class="arrow_carrot-right"></i></a>
            <a href="javascript:;" class="close"><i class="icon_close"></i></a>
            </span>
            </div>
            <article>
            <h2 class="post-title">
            <%= title %>
            <span class="share">
            <a href="style/#">
            <i class="switch-share icon_link_alt"></i>
            <i class="switch-item social_facebook"></i>
            </a>
            <a href="style/#"><i class="social_googleplus"></i></a>
            <a href="style/#"><i class="social_twitter"></i></a>
            <a href="style/#"><i class="social_pinterest"></i></a>
            </span>
            </h2>
            <%= content %>
            <div class="meta row">
            <div class="col-md-6">
            <% if (typeof(categories) != "undefined") { %>
            
            <% } %>
            </div>
            <div class="col-md-6 text-right">
            <a href="<?php echo URL ?>/champions/<%= champ %>"><button type="button" class="btn btn-success full-rounded btn-animated"><span><i class="arrow_right"></i> Ver perfil de <%= champ %></span></button></a>
            </div>
            </div>
            </article>
            </div>
        </script>


        <!--Template: Open GI-->
        <script type="text/template" id="tpl-single-open-gi">
            <div class="grid-open-gi">
            <div class="panel-gi row">
            <div class="col-md-7">
            <div class="entry-media">
            <%= typeof(media)!== 'undefined' ?  media : '' %>
            </div>
            </div>
            <div class="col-md-5">
            <article>
            <h2 class="post-title"><%= title %></h2>
            <p><%= excerpt %></p>
            </article>

            <a href="javascript:;" class="item-action prev"><i class="arrow_carrot-left"></i></a>
            <a href="javascript:;" class="item-action next"><i class="arrow_carrot-right"></i></a>
            </div>
            </div>
            <a href="javascript:;" class="close-viewport"><i class="icon_close"></i></a>
            </div>
        </script>


        <!--Template: Modal-->
        <script type="text/template" id="tpl-single-modal">
            <div id="gx-single-modal" class="modal single-modal fade" role="dialog" aria-labelledby="single-modal-label" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-body"></div>
            </div>
            </div>
            </div>
        </script>

<script>
		function searchbar()
		{
			swal({   title: "Cargando...",   html: "Este proceso puede tomar unos minutos...<br>", showConfirmButton: false, showCancelButton: false, allowEscapeKey:false, allowOutsideClick: false, imageUrl: "<?php echo URL ?>/style/images/loading.gif", imageSize:"300x300"});
			window.location='<?php echo URL ?>/search/<?php echo parseserver(@$_COOKIE['onlol_region']); ?>/' + $('#string').val();;
		}
		</script>

        <!--Template: Header Search-->
        <script type="text/template" id="tpl-header-search">
            <div class="search-template">
            <div class="inner-table">
            <div class="inner-row">
            <div class="container">
            <form class="search-form" action="javascript:searchbar();">
            <div class="input-group">
            <input type="search" class="search-field" placeholder="Introduce un invocador y pulsa enter...." id="string" autocomplete="off">
            <input type="submit" class="search-submit" value="Buscar">
            <a href="javascript:;" class="close-search"><i class="icon_close"></i></a>
            </div>
            </form>
            </div>
            </div>
            </div>
            </div>
        </script> 
    </head>
    <body id="rclick" class="header-light">

        <!-- Document Wrapper
        ================================================== -->
        <div class="wrapper"style="background-image: url(<?php echo URL ?>/style/images/home/bg/11.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed; ">

            <!-- Header
            ================================================== -->
            <header id="header" class="navbar navbar-default">
                <div class="container">

                    <!-- Logo
                    ================================================== -->
                    <div class="navbar-header">
                        <a class="navbar-brand" href="<?php echo URL ?>">Logo</a>
                    </div>

                    <!-- Main navigation
================================================== -->
                           <?php echo nav('champs'); ?>
                </div>
            </header>
            <!-- Content
            ================================================== -->
            <section class="content container no-padding">
                <div class="row">
                    <div class="col-md-12" style="background-image: url(<?php echo URL ?>/style/images/home/bg/11.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
		<br>
		<h1 style="color:white; text-align:center;">Partidas promocionadas</h1>
         <br>
<script type="text/javascript">
            $(document).ready(function(){
                $('#server_euw').DrSlider({
                    width: 1024,
                    height: 600,
					showNavigation: false,
					showProgress: false,
					showControl: true
                }); 
            });
			
			$(document).ready(function(){
                $('#server_eune').DrSlider({
                    width: 1024,
                    height: 600,
					showNavigation: false,
					showProgress: false,
					showControl: true
                }); 
            });
			
			$(document).ready(function(){
                $('#server_br').DrSlider({
                    width: 1024,
                    height: 600,
					showNavigation: false,
					showProgress: false,
					showControl: true
                }); 
            });
			
			$(document).ready(function(){
                $('#server_kr').DrSlider({
                    width: 1024,
                    height: 600,
					showNavigation: false,
					showProgress: false,
					showControl: true
                }); 
            });
			
			$(document).ready(function(){
                $('#server_lan').DrSlider({
                    width: 1024,
                    height: 600,
					showNavigation: false,
					showProgress: false,
					showControl: true
                }); 
            });
			
			$(document).ready(function(){
                $('#server_las').DrSlider({
                    width: 1024,
                    height: 600,
					showNavigation: false,
					showProgress: false,
					showControl: true
                }); 
            });
			
			$(document).ready(function(){
                $('#server_na').DrSlider({
                    width: 1024,
                    height: 600,
					showNavigation: false,
					showProgress: false,
					showControl: true
                }); 
            });
			
			$(document).ready(function(){
                $('#server_oce').DrSlider({
                    width: 1024,
                    height: 600,
					showNavigation: false,
					showProgress: false,
					showControl: true
                }); 
            });
			
			$(document).ready(function(){
                $('#server_pbe').DrSlider({
                    width: 1024,
                    height: 600,
					showNavigation: false,
					showProgress: false,
					showControl: true
                }); 
            });
			
			$(document).ready(function(){
                $('#server_ru').DrSlider({
                    width: 1024,
                    height: 600,
					showNavigation: false,
					showProgress: false,
					showControl: true
                }); 
            });
			
			$(document).ready(function(){
                $('#server_tr').DrSlider({
                    width: 1024,
                    height: 600,
					showNavigation: false,
					showProgress: false,
					showControl: true
                }); 
            });
        </script>
		<?php
		 $server_euw = readjson('https://euw.api.pvp.net/observer-mode/rest/featured?api_key='.LOL_API_KEY);
		 /* Parse reloading */
		 
		 echo '<script> setTimeout("location.reload();", '.($server_euw['clientRefreshInterval']*1000).') </script>';
		 /* Loading data */
		 $totalgames_euw = count($server_euw['gameList']);
		 $actualoutpotgame_euw = 0;
		 $actualoutpotgame_timer_euw = 0;
		 echo '<script>
					function spectate()
					{
						swal({   html: true, title: "¡Ya casi lo tienes!",   html: "Ejecuta el archivo y tras unos 10 segundos ya estarás en modo espectador. <br> Si la pantalla de tu cliente se queda en negro, pulsa <b>ctrl + alt + supr</b> y se te desbloqueará.",   imageUrl: "'.URL.'/style/images/spectate/bg.png", imageSize:"200x200", timer:4000 });
					}
				</script>
				<div id="server_euw" style="background-image:url('.URL.'/style/images/game/featured/subbg.png);background-size:100% 100%; opacity:0.95;border-radius: 35px 35px 35px 35px;-moz-border-radius: 35px 35px 35px 35px;-webkit-border-radius: 35px 35px 35px 35px;border: 0px solid #000000;">';
		 while($actualoutpotgame_euw < $totalgames_euw)
		 {
			 if($server_euw['gameList'][$actualoutpotgame_euw]['gameQueueConfigId'] == 4)
			 {
				 echo '
					<div style="background-image:url('.URL.'/style/images/game/featured/bg.png);background-size:100% 100%;">
					<div class="row">
						<button style="float:left; margin-left:2%;margin-top:1%;" type="button" class="btn styled btn-info">EUW</button>
						<div style="float:left; margin-left:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['bannedChampions']['0']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['bannedChampions']['1']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['bannedChampions']['2']['championId']).'.png">
						</div>
						<form action="'.URL.'/spectate_game" method="post">
					<input type="hidden" name="game_key" value="'.$server_euw['gameList'][$actualoutpotgame_euw]['observers']['encryptionKey'].'">
					<input type="hidden" name="game_id" value="'.$server_euw['gameList'][$actualoutpotgame_euw]['gameId'].'">
					<input type="hidden" name="server" value="'.$server_euw['gameList'][$actualoutpotgame_euw]['platformId'].'">
					<button  onclick="javascript:spectate()" type="submit" style="float:left; margin-left:10%;margin-top:1%;" type="button" class="btn styled btn-warning">Espectar</button>
					</form>
					<button style="float:right; margin-right:2%;margin-top:1%;" type="button" class="btn styled btn-info">'.time_elapsed_string(datetounix($server_euw['gameList'][$actualoutpotgame_euw]['gameStartTime'])).'</button>
						<div style="float:right; margin-right:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['bannedChampions']['3']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['bannedChampions']['4']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['bannedChampions']['5']['championId']).'.png">
						</div>
						
					</div>
					<div class="row" style="margin-top:7%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_euw['gameList'][$actualoutpotgame_euw]['participants']['0']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['participants']['0']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_euw['gameList'][$actualoutpotgame_euw]['participants']['5']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['participants']['5']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_euw['gameList'][$actualoutpotgame_euw]['participants']['1']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['participants']['1']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_euw['gameList'][$actualoutpotgame_euw]['participants']['6']['summonerName'].' 
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['participants']['6']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_euw['gameList'][$actualoutpotgame_euw]['participants']['2']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['participants']['2']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_euw['gameList'][$actualoutpotgame_euw]['participants']['7']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['participants']['7']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_euw['gameList'][$actualoutpotgame_euw]['participants']['3']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['participants']['3']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_euw['gameList'][$actualoutpotgame_euw]['participants']['8']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['participants']['8']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_euw['gameList'][$actualoutpotgame_euw]['participants']['4']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['participants']['4']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_euw['gameList'][$actualoutpotgame_euw]['participants']['9']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_euw['gameList'][$actualoutpotgame_euw]['participants']['9']['championId']).'.png">
						</div>
					</div>
				</div>';
			 }
			 $actualoutpotgame_euw++;
		 }
		 ?> 
        </div>
         <br>
		 
		 <?php
		 $server_eune = readjson('https://eune.api.pvp.net/observer-mode/rest/featured?api_key='.LOL_API_KEY);
		 /* Loading data */
		 $totalgames_eune = count($server_eune['gameList']);
		 $actualoutpotgame_eune = 0;
		 $actualoutpotgame_timer_eune = 0;
		 echo '<script>
					function spectate()
					{
						swal({   html: true, title: "¡Ya casi lo tienes!",   html: "Ejecuta el archivo y tras unos 10 segundos ya estarás en modo espectador. <br> Si la pantalla de tu cliente se queda en negro, pulsa <b>ctrl + alt + supr</b> y se te desbloqueará.",   imageUrl: "'.URL.'/style/images/spectate/bg.png", imageSize:"200x200", timer:4000 });
					}
				</script>
				<div id="server_eune" style="background-image:url('.URL.'/style/images/game/featured/subbg.png);background-size:100% 100%; opacity:0.95;border-radius: 35px 35px 35px 35px;-moz-border-radius: 35px 35px 35px 35px;-webkit-border-radius: 35px 35px 35px 35px;border: 0px solid #000000;">';
		 while($actualoutpotgame_eune < $totalgames_eune)
		 {
			 if($server_eune['gameList'][$actualoutpotgame_eune]['gameQueueConfigId'] == 4)
			 {
				 echo '
					<div style="background-image:url('.URL.'/style/images/game/featured/bg.png);background-size:100% 100%;">
					<div class="row">
						<button style="float:left; margin-left:2%;margin-top:1%;" type="button" class="btn styled btn-info">eune</button>
						<div style="float:left; margin-left:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['bannedChampions']['0']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['bannedChampions']['1']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['bannedChampions']['2']['championId']).'.png">
						</div>
						<form action="'.URL.'/spectate_game" method="post">
					<input type="hidden" name="game_key" value="'.$server_eune['gameList'][$actualoutpotgame_eune]['observers']['encryptionKey'].'">
					<input type="hidden" name="game_id" value="'.$server_eune['gameList'][$actualoutpotgame_eune]['gameId'].'">
					<input type="hidden" name="server" value="'.$server_eune['gameList'][$actualoutpotgame_eune]['platformId'].'">
					<button  onclick="javascript:spectate()" type="submit" style="float:left; margin-left:10%;margin-top:1%;" type="button" class="btn styled btn-warning">Espectar</button>
					</form>
					<button style="float:right; margin-right:2%;margin-top:1%;" type="button" class="btn styled btn-info">'.time_elapsed_string(datetounix($server_eune['gameList'][$actualoutpotgame_eune]['gameStartTime'])).'</button>
						<div style="float:right; margin-right:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['bannedChampions']['3']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['bannedChampions']['4']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['bannedChampions']['5']['championId']).'.png">
						</div>
						
					</div>
					<div class="row" style="margin-top:7%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_eune['gameList'][$actualoutpotgame_eune]['participants']['0']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['participants']['0']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_eune['gameList'][$actualoutpotgame_eune]['participants']['5']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['participants']['5']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_eune['gameList'][$actualoutpotgame_eune]['participants']['1']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['participants']['1']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_eune['gameList'][$actualoutpotgame_eune]['participants']['6']['summonerName'].' 
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['participants']['6']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_eune['gameList'][$actualoutpotgame_eune]['participants']['2']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['participants']['2']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_eune['gameList'][$actualoutpotgame_eune]['participants']['7']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['participants']['7']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_eune['gameList'][$actualoutpotgame_eune]['participants']['3']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['participants']['3']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_eune['gameList'][$actualoutpotgame_eune]['participants']['8']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['participants']['8']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_eune['gameList'][$actualoutpotgame_eune]['participants']['4']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['participants']['4']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_eune['gameList'][$actualoutpotgame_eune]['participants']['9']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_eune['gameList'][$actualoutpotgame_eune]['participants']['9']['championId']).'.png">
						</div>
					</div>
				</div>';
			 }
			 $actualoutpotgame_eune++;
		 }
		 ?> 
        </div>
         <br>
		 <?php
		 $server_br = readjson('https://br.api.pvp.net/observer-mode/rest/featured?api_key='.LOL_API_KEY);
		 /* Loading data */
		 $totalgames_br = count($server_br['gameList']);
		 $actualoutpotgame_br = 0;
		 $actualoutpotgame_timer_br = 0;
		 echo '<script>
					function spectate()
					{
						swal({   html: true, title: "¡Ya casi lo tienes!",   html: "Ejecuta el archivo y tras unos 10 segundos ya estarás en modo espectador. <br> Si la pantalla de tu cliente se queda en negro, pulsa <b>ctrl + alt + supr</b> y se te desbloqueará.",   imageUrl: "'.URL.'/style/images/spectate/bg.png", imageSize:"200x200", timer:4000 });
					}
				</script>
				<div id="server_br" style="background-image:url('.URL.'/style/images/game/featured/subbg.png);background-size:100% 100%; opacity:0.95;border-radius: 35px 35px 35px 35px;-moz-border-radius: 35px 35px 35px 35px;-webkit-border-radius: 35px 35px 35px 35px;border: 0px solid #000000;">';
		 while($actualoutpotgame_br < $totalgames_br)
		 {
			 if($server_br['gameList'][$actualoutpotgame_br]['gameQueueConfigId'] == 4)
			 {
				 echo '
					<div style="background-image:url('.URL.'/style/images/game/featured/bg.png);background-size:100% 100%;">
					<div class="row">
						<button style="float:left; margin-left:2%;margin-top:1%;" type="button" class="btn styled btn-info">br</button>
						<div style="float:left; margin-left:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['bannedChampions']['0']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['bannedChampions']['1']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['bannedChampions']['2']['championId']).'.png">
						</div>
						<form action="'.URL.'/spectate_game" method="post">
					<input type="hidden" name="game_key" value="'.$server_br['gameList'][$actualoutpotgame_br]['observers']['encryptionKey'].'">
					<input type="hidden" name="game_id" value="'.$server_br['gameList'][$actualoutpotgame_br]['gameId'].'">
					<input type="hidden" name="server" value="'.$server_br['gameList'][$actualoutpotgame_br]['platformId'].'">
					<button  onclick="javascript:spectate()" type="submit" style="float:left; margin-left:10%;margin-top:1%;" type="button" class="btn styled btn-warning">Espectar</button>
					</form>
					<button style="float:right; margin-right:2%;margin-top:1%;" type="button" class="btn styled btn-info">'.time_elapsed_string(datetounix($server_br['gameList'][$actualoutpotgame_br]['gameStartTime'])).'</button>
						<div style="float:right; margin-right:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['bannedChampions']['3']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['bannedChampions']['4']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['bannedChampions']['5']['championId']).'.png">
						</div>
						
					</div>
					<div class="row" style="margin-top:7%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_br['gameList'][$actualoutpotgame_br]['participants']['0']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['participants']['0']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_br['gameList'][$actualoutpotgame_br]['participants']['5']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['participants']['5']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_br['gameList'][$actualoutpotgame_br]['participants']['1']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['participants']['1']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_br['gameList'][$actualoutpotgame_br]['participants']['6']['summonerName'].' 
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['participants']['6']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_br['gameList'][$actualoutpotgame_br]['participants']['2']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['participants']['2']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_br['gameList'][$actualoutpotgame_br]['participants']['7']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['participants']['7']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_br['gameList'][$actualoutpotgame_br]['participants']['3']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['participants']['3']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_br['gameList'][$actualoutpotgame_br]['participants']['8']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['participants']['8']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_br['gameList'][$actualoutpotgame_br]['participants']['4']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['participants']['4']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_br['gameList'][$actualoutpotgame_br]['participants']['9']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_br['gameList'][$actualoutpotgame_br]['participants']['9']['championId']).'.png">
						</div>
					</div>
				</div>';
			 }
			 $actualoutpotgame_br++;
		 }
		 ?> 
        </div>
         <br>
		 <?php
		 $server_kr = readjson('https://kr.api.pvp.net/observer-mode/rest/featured?api_key='.LOL_API_KEY);
		 /* Loading data */
		 $totalgames_kr = count($server_kr['gameList']);
		 $actualoutpotgame_kr = 0;
		 $actualoutpotgame_timer_kr = 0;
		 echo '<script>
					function spectate()
					{
						swal({   html: true, title: "¡Ya casi lo tienes!",   html: "Ejecuta el archivo y tras unos 10 segundos ya estarás en modo espectador. <br> Si la pantalla de tu cliente se queda en negro, pulsa <b>ctrl + alt + supr</b> y se te desbloqueará.",   imageUrl: "'.URL.'/style/images/spectate/bg.png", imageSize:"200x200", timer:4000 });
					}
				</script>
				<div id="server_kr" style="background-image:url('.URL.'/style/images/game/featured/subbg.png);background-size:100% 100%; opacity:0.95;border-radius: 35px 35px 35px 35px;-moz-border-radius: 35px 35px 35px 35px;-webkit-border-radius: 35px 35px 35px 35px;border: 0px solid #000000;">';
		 while($actualoutpotgame_kr < $totalgames_kr)
		 {
			 if($server_kr['gameList'][$actualoutpotgame_kr]['gameQueueConfigId'] == 4)
			 {
				 echo '
					<div style="background-image:url('.URL.'/style/images/game/featured/bg.png);background-size:100% 100%;">
					<div class="row">
						<button style="float:left; margin-left:2%;margin-top:1%;" type="button" class="btn styled btn-info">kr</button>
						<div style="float:left; margin-left:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['bannedChampions']['0']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['bannedChampions']['1']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['bannedChampions']['2']['championId']).'.png">
						</div>
						<form action="'.URL.'/spectate_game" method="post">
					<input type="hidden" name="game_key" value="'.$server_kr['gameList'][$actualoutpotgame_kr]['observers']['encryptionKey'].'">
					<input type="hidden" name="game_id" value="'.$server_kr['gameList'][$actualoutpotgame_kr]['gameId'].'">
					<input type="hidden" name="server" value="'.$server_kr['gameList'][$actualoutpotgame_kr]['platformId'].'">
					<button  onclick="javascript:spectate()" type="submit" style="float:left; margin-left:10%;margin-top:1%;" type="button" class="btn styled btn-warning">Espectar</button>
					</form>
					<button style="float:right; margin-right:2%;margin-top:1%;" type="button" class="btn styled btn-info">'.time_elapsed_string(datetounix($server_kr['gameList'][$actualoutpotgame_kr]['gameStartTime'])).'</button>
						<div style="float:right; margin-right:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['bannedChampions']['3']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['bannedChampions']['4']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['bannedChampions']['5']['championId']).'.png">
						</div>
						
					</div>
					<div class="row" style="margin-top:7%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_kr['gameList'][$actualoutpotgame_kr]['participants']['0']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['participants']['0']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_kr['gameList'][$actualoutpotgame_kr]['participants']['5']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['participants']['5']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_kr['gameList'][$actualoutpotgame_kr]['participants']['1']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['participants']['1']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_kr['gameList'][$actualoutpotgame_kr]['participants']['6']['summonerName'].' 
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['participants']['6']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_kr['gameList'][$actualoutpotgame_kr]['participants']['2']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['participants']['2']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_kr['gameList'][$actualoutpotgame_kr]['participants']['7']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['participants']['7']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_kr['gameList'][$actualoutpotgame_kr]['participants']['3']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['participants']['3']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_kr['gameList'][$actualoutpotgame_kr]['participants']['8']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['participants']['8']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_kr['gameList'][$actualoutpotgame_kr]['participants']['4']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['participants']['4']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_kr['gameList'][$actualoutpotgame_kr]['participants']['9']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_kr['gameList'][$actualoutpotgame_kr]['participants']['9']['championId']).'.png">
						</div>
					</div>
				</div>';
			 }
			 $actualoutpotgame_kr++;
		 }
		 ?> 
        </div>
         <br>
		 <?php
		 $server_lan = readjson('https://lan.api.pvp.net/observer-mode/rest/featured?api_key='.LOL_API_KEY);
		 /* Loading data */
		 $totalgames_lan = count($server_lan['gameList']);
		 $actualoutpotgame_lan = 0;
		 $actualoutpotgame_timer_lan = 0;
		 echo '<script>
					function spectate()
					{
						swal({   html: true, title: "¡Ya casi lo tienes!",   html: "Ejecuta el archivo y tras unos 10 segundos ya estarás en modo espectador. <br> Si la pantalla de tu cliente se queda en negro, pulsa <b>ctrl + alt + supr</b> y se te desbloqueará.",   imageUrl: "'.URL.'/style/images/spectate/bg.png", imageSize:"200x200", timer:4000 });
					}
				</script>
				<div id="server_lan" style="background-image:url('.URL.'/style/images/game/featured/subbg.png);background-size:100% 100%; opacity:0.95;border-radius: 35px 35px 35px 35px;-moz-border-radius: 35px 35px 35px 35px;-webkit-border-radius: 35px 35px 35px 35px;border: 0px solid #000000;">';
		 while($actualoutpotgame_lan < $totalgames_lan)
		 {
			 if($server_lan['gameList'][$actualoutpotgame_lan]['gameQueueConfigId'] == 4)
			 {
				 echo '
					<div style="background-image:url('.URL.'/style/images/game/featured/bg.png);background-size:100% 100%;">
					<div class="row">
						<button style="float:left; margin-left:2%;margin-top:1%;" type="button" class="btn styled btn-info">lan</button>
						<div style="float:left; margin-left:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['bannedChampions']['0']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['bannedChampions']['1']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['bannedChampions']['2']['championId']).'.png">
						</div>
						<form action="'.URL.'/spectate_game" method="post">
					<input type="hidden" name="game_key" value="'.$server_lan['gameList'][$actualoutpotgame_lan]['observers']['encryptionKey'].'">
					<input type="hidden" name="game_id" value="'.$server_lan['gameList'][$actualoutpotgame_lan]['gameId'].'">
					<input type="hidden" name="server" value="'.$server_lan['gameList'][$actualoutpotgame_lan]['platformId'].'">
					<button  onclick="javascript:spectate()" type="submit" style="float:left; margin-left:10%;margin-top:1%;" type="button" class="btn styled btn-warning">Espectar</button>
					</form>
					<button style="float:right; margin-right:2%;margin-top:1%;" type="button" class="btn styled btn-info">'.time_elapsed_string(datetounix($server_lan['gameList'][$actualoutpotgame_lan]['gameStartTime'])).'</button>
						<div style="float:right; margin-right:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['bannedChampions']['3']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['bannedChampions']['4']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['bannedChampions']['5']['championId']).'.png">
						</div>
						
					</div>
					<div class="row" style="margin-top:7%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_lan['gameList'][$actualoutpotgame_lan]['participants']['0']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['participants']['0']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_lan['gameList'][$actualoutpotgame_lan]['participants']['5']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['participants']['5']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_lan['gameList'][$actualoutpotgame_lan]['participants']['1']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['participants']['1']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_lan['gameList'][$actualoutpotgame_lan]['participants']['6']['summonerName'].' 
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['participants']['6']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_lan['gameList'][$actualoutpotgame_lan]['participants']['2']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['participants']['2']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_lan['gameList'][$actualoutpotgame_lan]['participants']['7']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['participants']['7']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_lan['gameList'][$actualoutpotgame_lan]['participants']['3']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['participants']['3']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_lan['gameList'][$actualoutpotgame_lan]['participants']['8']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['participants']['8']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_lan['gameList'][$actualoutpotgame_lan]['participants']['4']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['participants']['4']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_lan['gameList'][$actualoutpotgame_lan]['participants']['9']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_lan['gameList'][$actualoutpotgame_lan]['participants']['9']['championId']).'.png">
						</div>
					</div>
				</div>';
			 }
			 $actualoutpotgame_lan++;
		 }
		 ?> 
        </div>
         <br>
		 <?php
		 $server_las = readjson('https://las.api.pvp.net/observer-mode/rest/featured?api_key='.LOL_API_KEY);
		 /* Loading data */
		 $totalgames_las = count($server_las['gameList']);
		 $actualoutpotgame_las = 0;
		 $actualoutpotgame_timer_las = 0;
		 echo '<script>
					function spectate()
					{
						swal({   html: true, title: "¡Ya casi lo tienes!",   html: "Ejecuta el archivo y tras unos 10 segundos ya estarás en modo espectador. <br> Si la pantalla de tu cliente se queda en negro, pulsa <b>ctrl + alt + supr</b> y se te desbloqueará.",   imageUrl: "'.URL.'/style/images/spectate/bg.png", imageSize:"200x200", timer:4000 });
					}
				</script>
				<div id="server_las" style="background-image:url('.URL.'/style/images/game/featured/subbg.png);background-size:100% 100%; opacity:0.95;border-radius: 35px 35px 35px 35px;-moz-border-radius: 35px 35px 35px 35px;-webkit-border-radius: 35px 35px 35px 35px;border: 0px solid #000000;">';
		 while($actualoutpotgame_las < $totalgames_las)
		 {
			 if($server_las['gameList'][$actualoutpotgame_las]['gameQueueConfigId'] == 4)
			 {
				 echo '
					<div style="background-image:url('.URL.'/style/images/game/featured/bg.png);background-size:100% 100%;">
					<div class="row">
						<button style="float:left; margin-left:2%;margin-top:1%;" type="button" class="btn styled btn-info">las</button>
						<div style="float:left; margin-left:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['bannedChampions']['0']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['bannedChampions']['1']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['bannedChampions']['2']['championId']).'.png">
						</div>
						<form action="'.URL.'/spectate_game" method="post">
					<input type="hidden" name="game_key" value="'.$server_las['gameList'][$actualoutpotgame_las]['observers']['encryptionKey'].'">
					<input type="hidden" name="game_id" value="'.$server_las['gameList'][$actualoutpotgame_las]['gameId'].'">
					<input type="hidden" name="server" value="'.$server_las['gameList'][$actualoutpotgame_las]['platformId'].'">
					<button  onclick="javascript:spectate()" type="submit" style="float:left; margin-left:10%;margin-top:1%;" type="button" class="btn styled btn-warning">Espectar</button>
					</form>
					<button style="float:right; margin-right:2%;margin-top:1%;" type="button" class="btn styled btn-info">'.time_elapsed_string(datetounix($server_las['gameList'][$actualoutpotgame_las]['gameStartTime'])).'</button>
						<div style="float:right; margin-right:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['bannedChampions']['3']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['bannedChampions']['4']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['bannedChampions']['5']['championId']).'.png">
						</div>
						
					</div>
					<div class="row" style="margin-top:7%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_las['gameList'][$actualoutpotgame_las]['participants']['0']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['participants']['0']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_las['gameList'][$actualoutpotgame_las]['participants']['5']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['participants']['5']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_las['gameList'][$actualoutpotgame_las]['participants']['1']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['participants']['1']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_las['gameList'][$actualoutpotgame_las]['participants']['6']['summonerName'].' 
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['participants']['6']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_las['gameList'][$actualoutpotgame_las]['participants']['2']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['participants']['2']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_las['gameList'][$actualoutpotgame_las]['participants']['7']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['participants']['7']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_las['gameList'][$actualoutpotgame_las]['participants']['3']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['participants']['3']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_las['gameList'][$actualoutpotgame_las]['participants']['8']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['participants']['8']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_las['gameList'][$actualoutpotgame_las]['participants']['4']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['participants']['4']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_las['gameList'][$actualoutpotgame_las]['participants']['9']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_las['gameList'][$actualoutpotgame_las]['participants']['9']['championId']).'.png">
						</div>
					</div>
				</div>';
			 }
			 $actualoutpotgame_las++;
		 }
		 ?> 
        </div>
         <br>
		 
		 <?php
		 $server_na = readjson('https://na.api.pvp.net/observer-mode/rest/featured?api_key='.LOL_API_KEY);
		 /* Loading data */
		 $totalgames_na = count($server_na['gameList']);
		 $actualoutpotgame_na = 0;
		 $actualoutpotgame_timer_na = 0;
		 echo '<script>
					function spectate()
					{
						swal({   html: true, title: "¡Ya casi lo tienes!",   html: "Ejecuta el archivo y tras unos 10 segundos ya estarás en modo espectador. <br> Si la pantalla de tu cliente se queda en negro, pulsa <b>ctrl + alt + supr</b> y se te desbloqueará.",   imageUrl: "'.URL.'/style/images/spectate/bg.png", imageSize:"200x200", timer:4000 });
					}
				</script>
				<div id="server_na" style="background-image:url('.URL.'/style/images/game/featured/subbg.png);background-size:100% 100%; opacity:0.95;border-radius: 35px 35px 35px 35px;-moz-border-radius: 35px 35px 35px 35px;-webkit-border-radius: 35px 35px 35px 35px;border: 0px solid #000000;">';
		 while($actualoutpotgame_na < $totalgames_na)
		 {
			 if($server_na['gameList'][$actualoutpotgame_na]['gameQueueConfigId'] == 4)
			 {
				 echo '
					<div style="background-image:url('.URL.'/style/images/game/featured/bg.png);background-size:100% 100%;">
					<div class="row">
						<button style="float:left; margin-left:2%;margin-top:1%;" type="button" class="btn styled btn-info">na</button>
						<div style="float:left; margin-left:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['bannedChampions']['0']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['bannedChampions']['1']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['bannedChampions']['2']['championId']).'.png">
						</div>
						<form action="'.URL.'/spectate_game" method="post">
					<input type="hidden" name="game_key" value="'.$server_na['gameList'][$actualoutpotgame_na]['observers']['encryptionKey'].'">
					<input type="hidden" name="game_id" value="'.$server_na['gameList'][$actualoutpotgame_na]['gameId'].'">
					<input type="hidden" name="server" value="'.$server_na['gameList'][$actualoutpotgame_na]['platformId'].'">
					<button  onclick="javascript:spectate()" type="submit" style="float:left; margin-left:10%;margin-top:1%;" type="button" class="btn styled btn-warning">Espectar</button>
					</form>
					<button style="float:right; margin-right:2%;margin-top:1%;" type="button" class="btn styled btn-info">'.time_elapsed_string(datetounix($server_na['gameList'][$actualoutpotgame_na]['gameStartTime'])).'</button>
						<div style="float:right; margin-right:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['bannedChampions']['3']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['bannedChampions']['4']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['bannedChampions']['5']['championId']).'.png">
						</div>
						
					</div>
					<div class="row" style="margin-top:7%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_na['gameList'][$actualoutpotgame_na]['participants']['0']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['participants']['0']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_na['gameList'][$actualoutpotgame_na]['participants']['5']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['participants']['5']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_na['gameList'][$actualoutpotgame_na]['participants']['1']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['participants']['1']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_na['gameList'][$actualoutpotgame_na]['participants']['6']['summonerName'].' 
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['participants']['6']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_na['gameList'][$actualoutpotgame_na]['participants']['2']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['participants']['2']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_na['gameList'][$actualoutpotgame_na]['participants']['7']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['participants']['7']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_na['gameList'][$actualoutpotgame_na]['participants']['3']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['participants']['3']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_na['gameList'][$actualoutpotgame_na]['participants']['8']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['participants']['8']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_na['gameList'][$actualoutpotgame_na]['participants']['4']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['participants']['4']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_na['gameList'][$actualoutpotgame_na]['participants']['9']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_na['gameList'][$actualoutpotgame_na]['participants']['9']['championId']).'.png">
						</div>
					</div>
				</div>';
			 }
			 $actualoutpotgame_na++;
		 }
		 ?> 
        </div>
         <br>
		 
		 <?php
		 $server_oce = readjson('https://oce.api.pvp.net/observer-mode/rest/featured?api_key='.LOL_API_KEY);
		 /* Loading data */
		 $totalgames_oce = count($server_oce['gameList']);
		 $actualoutpotgame_oce = 0;
		 $actualoutpotgame_timer_oce = 0;
		 echo '<script>
					function spectate()
					{
						swal({   html: true, title: "¡Ya casi lo tienes!",   html: "Ejecuta el archivo y tras unos 10 segundos ya estarás en modo espectador. <br> Si la pantalla de tu cliente se queda en negro, pulsa <b>ctrl + alt + supr</b> y se te desbloqueará.",   imageUrl: "'.URL.'/style/images/spectate/bg.png", imageSize:"200x200", timer:4000 });
					}
				</script>
				<div id="server_oce" style="background-image:url('.URL.'/style/images/game/featured/subbg.png);background-size:100% 100%; opacity:0.95;border-radius: 35px 35px 35px 35px;-moz-border-radius: 35px 35px 35px 35px;-webkit-border-radius: 35px 35px 35px 35px;border: 0px solid #000000;">';
		 while($actualoutpotgame_oce < $totalgames_oce)
		 {
			 if($server_oce['gameList'][$actualoutpotgame_oce]['gameQueueConfigId'] == 4)
			 {
				 echo '
					<div style="background-image:url('.URL.'/style/images/game/featured/bg.png);background-size:100% 100%;">
					<div class="row">
						<button style="float:left; margin-left:2%;margin-top:1%;" type="button" class="btn styled btn-info">oce</button>
						<div style="float:left; margin-left:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['bannedChampions']['0']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['bannedChampions']['1']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['bannedChampions']['2']['championId']).'.png">
						</div>
						<form action="'.URL.'/spectate_game" method="post">
					<input type="hidden" name="game_key" value="'.$server_oce['gameList'][$actualoutpotgame_oce]['observers']['encryptionKey'].'">
					<input type="hidden" name="game_id" value="'.$server_oce['gameList'][$actualoutpotgame_oce]['gameId'].'">
					<input type="hidden" name="server" value="'.$server_oce['gameList'][$actualoutpotgame_oce]['platformId'].'">
					<button  onclick="javascript:spectate()" type="submit" style="float:left; margin-left:10%;margin-top:1%;" type="button" class="btn styled btn-warning">Espectar</button>
					</form>
					<button style="float:right; margin-right:2%;margin-top:1%;" type="button" class="btn styled btn-info">'.time_elapsed_string(datetounix($server_oce['gameList'][$actualoutpotgame_oce]['gameStartTime'])).'</button>
						<div style="float:right; margin-right:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['bannedChampions']['3']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['bannedChampions']['4']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['bannedChampions']['5']['championId']).'.png">
						</div>
						
					</div>
					<div class="row" style="margin-top:7%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_oce['gameList'][$actualoutpotgame_oce]['participants']['0']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['participants']['0']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_oce['gameList'][$actualoutpotgame_oce]['participants']['5']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['participants']['5']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_oce['gameList'][$actualoutpotgame_oce]['participants']['1']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['participants']['1']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_oce['gameList'][$actualoutpotgame_oce]['participants']['6']['summonerName'].' 
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['participants']['6']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_oce['gameList'][$actualoutpotgame_oce]['participants']['2']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['participants']['2']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_oce['gameList'][$actualoutpotgame_oce]['participants']['7']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['participants']['7']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_oce['gameList'][$actualoutpotgame_oce]['participants']['3']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['participants']['3']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_oce['gameList'][$actualoutpotgame_oce]['participants']['8']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['participants']['8']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_oce['gameList'][$actualoutpotgame_oce]['participants']['4']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['participants']['4']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_oce['gameList'][$actualoutpotgame_oce]['participants']['9']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_oce['gameList'][$actualoutpotgame_oce]['participants']['9']['championId']).'.png">
						</div>
					</div>
				</div>';
			 }
			 $actualoutpotgame_oce++;
		 }
		 ?> 
        </div>
         <br>
		 
		 
		 <?php
		 $server_ru = readjson('https://ru.api.pvp.net/observer-mode/rest/featured?api_key='.LOL_API_KEY);
		 /* Loading data */
		 $totalgames_ru = count($server_ru['gameList']);
		 $actualoutpotgame_ru = 0;
		 $actualoutpotgame_timer_ru = 0;
		 echo '<script>
					function spectate()
					{
						swal({   html: true, title: "¡Ya casi lo tienes!",   html: "Ejecuta el archivo y tras unos 10 segundos ya estarás en modo espectador. <br> Si la pantalla de tu cliente se queda en negro, pulsa <b>ctrl + alt + supr</b> y se te desbloqueará.",   imageUrl: "'.URL.'/style/images/spectate/bg.png", imageSize:"200x200", timer:4000 });
					}
				</script>
				<div id="server_ru" style="background-image:url('.URL.'/style/images/game/featured/subbg.png);background-size:100% 100%; opacity:0.95;border-radius: 35px 35px 35px 35px;-moz-border-radius: 35px 35px 35px 35px;-webkit-border-radius: 35px 35px 35px 35px;border: 0px solid #000000;">';
		 while($actualoutpotgame_ru < $totalgames_ru)
		 {
			 if($server_ru['gameList'][$actualoutpotgame_ru]['gameQueueConfigId'] == 4)
			 {
				 echo '
					<div style="background-image:url('.URL.'/style/images/game/featured/bg.png);background-size:100% 100%;">
					<div class="row">
						<button style="float:left; margin-left:2%;margin-top:1%;" type="button" class="btn styled btn-info">ru</button>
						<div style="float:left; margin-left:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['bannedChampions']['0']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['bannedChampions']['1']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['bannedChampions']['2']['championId']).'.png">
						</div>
						<form action="'.URL.'/spectate_game" method="post">
					<input type="hidden" name="game_key" value="'.$server_ru['gameList'][$actualoutpotgame_ru]['observers']['encryptionKey'].'">
					<input type="hidden" name="game_id" value="'.$server_ru['gameList'][$actualoutpotgame_ru]['gameId'].'">
					<input type="hidden" name="server" value="'.$server_ru['gameList'][$actualoutpotgame_ru]['platformId'].'">
					<button  onclick="javascript:spectate()" type="submit" style="float:left; margin-left:10%;margin-top:1%;" type="button" class="btn styled btn-warning">Espectar</button>
					</form>
					<button style="float:right; margin-right:2%;margin-top:1%;" type="button" class="btn styled btn-info">'.time_elapsed_string(datetounix($server_ru['gameList'][$actualoutpotgame_ru]['gameStartTime'])).'</button>
						<div style="float:right; margin-right:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['bannedChampions']['3']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['bannedChampions']['4']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['bannedChampions']['5']['championId']).'.png">
						</div>
						
					</div>
					<div class="row" style="margin-top:7%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_ru['gameList'][$actualoutpotgame_ru]['participants']['0']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['participants']['0']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_ru['gameList'][$actualoutpotgame_ru]['participants']['5']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['participants']['5']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_ru['gameList'][$actualoutpotgame_ru]['participants']['1']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['participants']['1']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_ru['gameList'][$actualoutpotgame_ru]['participants']['6']['summonerName'].' 
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['participants']['6']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_ru['gameList'][$actualoutpotgame_ru]['participants']['2']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['participants']['2']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_ru['gameList'][$actualoutpotgame_ru]['participants']['7']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['participants']['7']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_ru['gameList'][$actualoutpotgame_ru]['participants']['3']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['participants']['3']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_ru['gameList'][$actualoutpotgame_ru]['participants']['8']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['participants']['8']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_ru['gameList'][$actualoutpotgame_ru]['participants']['4']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['participants']['4']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_ru['gameList'][$actualoutpotgame_ru]['participants']['9']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_ru['gameList'][$actualoutpotgame_ru]['participants']['9']['championId']).'.png">
						</div>
					</div>
				</div>';
			 }
			 $actualoutpotgame_ru++;
		 }
		 ?> 
        </div>
         <br>
		 <?php
		 $server_tr = readjson('https://tr.api.pvp.net/observer-mode/rest/featured?api_key='.LOL_API_KEY);
		 /* Loading data */
		 $totalgames_tr = count($server_tr['gameList']);
		 $actualoutpotgame_tr = 0;
		 $actualoutpotgame_timer_tr = 0;
		 echo '<script>
					function spectate()
					{
						swal({   html: true, title: "¡Ya casi lo tienes!",   html: "Ejecuta el archivo y tras unos 10 segundos ya estarás en modo espectador. <br> Si la pantalla de tu cliente se queda en negro, pulsa <b>ctrl + alt + supr</b> y se te desbloqueará.",   imageUrl: "'.URL.'/style/images/spectate/bg.png", imageSize:"200x200", timer:4000 });
					}
				</script>
				<div id="server_tr" style="background-image:url('.URL.'/style/images/game/featured/subbg.png);background-size:100% 100%; opacity:0.95;border-radius: 35px 35px 35px 35px;-moz-border-radius: 35px 35px 35px 35px;-webkit-border-radius: 35px 35px 35px 35px;border: 0px solid #000000;">';
		 while($actualoutpotgame_tr < $totalgames_tr)
		 {
			 if($server_tr['gameList'][$actualoutpotgame_tr]['gameQueueConfigId'] == 4)
			 {
				 echo '
					<div style="background-image:url('.URL.'/style/images/game/featured/bg.png);background-size:100% 100%;">
					<div class="row">
						<button style="float:left; margin-left:2%;margin-top:1%;" type="button" class="btn styled btn-info">tr</button>
						<div style="float:left; margin-left:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['bannedChampions']['0']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['bannedChampions']['1']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['bannedChampions']['2']['championId']).'.png">
						</div>
						<form action="'.URL.'/spectate_game" method="post">
					<input type="hidden" name="game_key" value="'.$server_tr['gameList'][$actualoutpotgame_tr]['observers']['encryptionKey'].'">
					<input type="hidden" name="game_id" value="'.$server_tr['gameList'][$actualoutpotgame_tr]['gameId'].'">
					<input type="hidden" name="server" value="'.$server_tr['gameList'][$actualoutpotgame_tr]['platformId'].'">
					<button  onclick="javascript:spectate()" type="submit" style="float:left; margin-left:10%;margin-top:1%;" type="button" class="btn styled btn-warning">Espectar</button>
					</form>
					<button style="float:right; margin-right:2%;margin-top:1%;" type="button" class="btn styled btn-info">'.time_elapsed_string(datetounix($server_tr['gameList'][$actualoutpotgame_tr]['gameStartTime'])).'</button>
						<div style="float:right; margin-right:10%;margin-top:1%;">
						<img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['bannedChampions']['3']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['bannedChampions']['4']['championId']).'.png"><img draggable="false" width="50px" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['bannedChampions']['5']['championId']).'.png">
						</div>
						
					</div>
					<div class="row" style="margin-top:7%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_tr['gameList'][$actualoutpotgame_tr]['participants']['0']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['participants']['0']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_tr['gameList'][$actualoutpotgame_tr]['participants']['5']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['participants']['5']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_tr['gameList'][$actualoutpotgame_tr]['participants']['1']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['participants']['1']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_tr['gameList'][$actualoutpotgame_tr]['participants']['6']['summonerName'].' 
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['participants']['6']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_tr['gameList'][$actualoutpotgame_tr]['participants']['2']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['participants']['2']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_tr['gameList'][$actualoutpotgame_tr]['participants']['7']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['participants']['7']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_tr['gameList'][$actualoutpotgame_tr]['participants']['3']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['participants']['3']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_tr['gameList'][$actualoutpotgame_tr]['participants']['8']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['participants']['8']['championId']).'.png">
						</div>
					</div>
					
					<div class="row" style="margin-top:2%">
						<div class="col-md-3" style="color:white;font-size:20pt;margin-top:1%;margin-left:12%">
						'.$server_tr['gameList'][$actualoutpotgame_tr]['participants']['4']['summonerName'].'
						</div>
						<div class="col-md-1" style="margin-left:-3%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['participants']['4']['championId']).'.png">
						</div>
						
						<div class="col-md-3" style="float:right; color:white;font-size:20pt;margin-top:1%;margin-right:11%">
						'.$server_tr['gameList'][$actualoutpotgame_tr]['participants']['9']['summonerName'].'
						</div>
						<div class="col-md-1" style="float:right; margin-right:-2%;">
						<img draggable="false" width="" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($server_tr['gameList'][$actualoutpotgame_tr]['participants']['9']['championId']).'.png">
						</div>
					</div>
				</div>';
			 }
			 $actualoutpotgame_tr++;
		 }
		 ?> 
        </div>
         <br>
        
         
					</div>
                </div>
            </section>

            <!-- Footer
            ================================================== -->
           <?php echo footer(); ?>
            <!-- Back to top anchor -->
            <span class="back-to-top"></span>
        </div><!-- end .wrapper -->

        <!-- Javascript -->
        <script type="text/javascript" src="style/js/grid.js"></script>
        <script type="text/javascript" src="style/js/scripts.js"></script>
    </body>
</html>