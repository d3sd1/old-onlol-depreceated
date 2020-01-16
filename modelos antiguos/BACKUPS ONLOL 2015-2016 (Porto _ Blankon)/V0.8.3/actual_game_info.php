<?php
require('core/core.php');
if(empty($_GET['name']))
{
	header('Location: '.URL.'/?invalid_data=game_lookup');
}
else
{
	$summoner = str_replace(' ', '', strtolower($_GET['name']));
}
/* Parse server */
$server_lookup = parseserver($_GET['server']);

			$region = parseserver($_GET['server']);
			$gamedata = readjson('https://'.$region.'.api.pvp.net/observer-mode/rest/consumer/getSpectatorGameInfo/'.strtoupper($lol_servers[$region]).'/'.$db->query('SELECT summoner_id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="'.$_GET['server'].'"')->fetch_row()[0].'?api_key='.LOL_API_KEY);
			if($gamedata == 'NOT_FOUND')
			{
				redirect(URL.'/summoner/'.$region.'/'.$_GET['name'].'&game=not_ingame');
			}
			?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ONLoL ~ Partida de <?php echo $_GET['name'] ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo URL ?>/style/images/favicon.ico"/>

        <!--Fonts-->
        <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,400italic%7CRaleway:400,600' rel='stylesheet' type='text/css'>

        <!--jQuery and plugins-->
        <script type="text/javascript" src="<?php echo URL ?>/style/js/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/underscore-min.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/backbone-min.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/backbone-paginated-collection.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/isotope.pkgd.min.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/imagesloaded.pkgd.min.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/jquery.waypoints.min.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/jquery.validate.min.js"></script>

        <!--Bootstrap-->
        <link rel="stylesheet" href="<?php echo URL ?>/style/plugins/bootstrap/css/bootstrap.min.css">
        <script type="text/javascript" src="<?php echo URL ?>/style/plugins/bootstrap/js/bootstrap.min.js"></script>

        <!--Icons and Animates-->
        <link rel="stylesheet" href="<?php echo URL ?>/style/plugins/elegant-font/style.css">
        <link rel="stylesheet" href="<?php echo URL ?>/style/plugins/et-line-font/style.css">
        <link rel="stylesheet" href="<?php echo URL ?>/style/css/animate.min.css">

        <!--Media Element Player-->
        <link rel="stylesheet" href="<?php echo URL ?>/style/plugins/mediaelement/mediaelementplayer.min.css">
        <link rel="stylesheet" href="<?php echo URL ?>/style/css/mediaelementplayer.css">
        <script type="text/javascript" src="<?php echo URL ?>/style/plugins/mediaelement/mediaelement-and-player.min.js"></script>

        <!--Owl Carousel-->
        <link rel="stylesheet" href="<?php echo URL ?>/style/plugins/owl-carousel/owl.carousel.css">
        <script type="text/javascript" src="<?php echo URL ?>/style/plugins/owl-carousel/owl.carousel.min.js"></script>

        <!--Magnific lightbox-->
        <link rel="stylesheet" href="<?php echo URL ?>/style/plugins/magnific/magnific-popup.css">
        <script type="text/javascript" src="<?php echo URL ?>/style/plugins/magnific/jquery.magnific-popup.min.js"></script>

        <!--Masterslider-->
        <link rel="stylesheet" href="<?php echo URL ?>/style/plugins/masterslider/style/masterslider.css">
        <link rel="stylesheet" href="<?php echo URL ?>/style/plugins/masterslider/skins/default/style.css">
        <link rel="stylesheet" href="<?php echo URL ?>/style/css/masterslider.css">
        <script type="text/javascript" src="<?php echo URL ?>/style/plugins/masterslider/jquery.easing.min.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/plugins/masterslider/masterslider.min.js"></script>

        <!--Stylesheet-->
        <link rel="stylesheet" type="text/css" href="<?php echo URL ?>/style/css/style.css">
		
		<!-- SWAL -->
        <script type="text/javascript" src="<?php echo URL ?>/style/js/sweetalert.min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo URL ?>/style/css/sweetalert.css"><?php echo rclickmenu() ?>
        <!--Template: Item style 5 - TITLE CAT HOVER-->
        <script type="text/template" id="tpl-item-style5">
            <div class="grid-item tpl-item-style5 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <div class="background" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"></div>
            <div class="entry-hover">
            <div class="inner-table">
            <div class="inner-row">
            <h2><a href="<?php echo URL ?>/style/<%= link %>" class="post-permalink"><%= skill %></a></h2>
            </div>
            </div>
            </div>
            </div>
            </div>
        </script>

        <!--Template: Item style 1 - MASONRY FOR PORTFOLIO-->
        <script type="text/template" id="tpl-masonry-style1">
            <div class="grid-item tpl-masonry-style1" data-id="<%= id %>">
            <div class="grid-item-entry">
            <div class="entry-media">
            <a href="<?php echo URL ?>/style/<%= link %>" class="post-permalink"><img src="<?php echo URL ?>/style/<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>" ></a>
            </div>
            <div class="entry-meta">
            <h2><a href="<?php echo URL ?>/style/<%= link %>" class="post-permalink"><%= title %></a></h2>
            </div>
            </div>
            </div>
        </script>


        <!--Template: Item style 2 - MASONRY FOR POST FORMAT-->
        <script type="text/template" id="tpl-masonry-style2">
            <div class="grid-item tpl-masonry-style2" data-id="<%= id %>">
            <div class="grid-item-entry entry-format-<%= format %>">
            <% if (typeof(media) != "undefined") { %>
            <div class="entry-media"><%= media %></div>
            <% } %>
            <div class="entry-meta">
            <h3><a href="<?php echo URL ?>/style/<%= link %>" class="post-permalink"><%= title %></a></h3>
            <div class="meta">
            <% if (typeof(categories) != "undefined") { %>
            <span class="categories">
            <i class="icon_tag_alt"></i>
            <a href="<?php echo URL ?>/style/<%= _.last(categories).link %>"><%= _.last(categories).title %></a> 
            </span>
            <% } %>
            <span class="date">
            <i class="icon_clock_alt"></i><%= date %>
            </span>
            </div>
            <div class="excerpt"><%= excerpt %></div>
            </div>
            </div>
            </div>
        </script>


        <!--Template: Item style 3 - MASONRY-->
        <script type="text/template" id="tpl-masonry-style3" data-id="<%= id %>">
            <div class="grid-item tpl-masonry-style3">
            <div class="grid-item-entry">
            <% if (typeof(thumb) != "undefined") { %>
            <div class="entry-media">
            <a href="<?php echo URL ?>/style/<%= link %>"><img src="<?php echo URL ?>/style/<%= thumb %>" ></a>
            </div>
            <% } %>
            <div class="entry-meta">
            <h3><a href="<?php echo URL ?>/style/<%= link %>" class="post-permalink"><%= title %></a></h3>
            <div class="meta">
            <% if (typeof(categories) != "undefined") { %>
            <span class="categories">
            <% _.each(categories, function(cat){ %>
            <a href="<?php echo URL ?>/style/<%= _.escape(cat.link) %>"><%= _.escape(cat.title) %></a>
            <% }); %>
            </span>
            <% } %>
            </div>
            </div>
            </div>
            </div>
        </script><!--Template: Push State-->
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
            <a href="#">
            <i class="switch-share icon_link_alt"></i>
            <i class="switch-item social_facebook"></i>
            </a>
            <a href="#"><i class="social_googleplus"></i></a>
            <a href="#"><i class="social_twitter"></i></a>
            <a href="#"><i class="social_pinterest"></i></a>
            </span>
            </h2>
            <%= content %>
			<div class="meta row">
            <div class="col-md-6 text-left">
            <div class="date"><%= cooldowns %></div>
            </div>
			<div class="col-md-6 text-right">
            <div class="date"></div>
            </div>
            </div>
            </article>
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
			window.location='<?php echo URL ?>/search/<?php echo parseserver(@$_COOKIE['onlol_region']); ?>/' + $('#string').val();
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
        <div class="wrapper">

            <!-- Header
            ================================================== -->
            <header id="header" class="navbar navbar-default">
                <div class="container">

                    <!-- Logo
                    ================================================== -->
                    <div class="navbar-header">
                        <a class="navbar-brand" href="<?php echo URL ?>">ONLoL</a>
                    </div>

                    <!-- Main navigation
================================================== -->
                    <?php echo nav('active_game'); ?>                
                </div>
            </header>

			
            <!-- Content
            ================================================== -->
			<section class="content single-portfolio padding2x" style="background-image: url(<?php echo URL ?>/style/images/game/lookup/<?php echo mapidtotxt($gamedata['mapId'], 'code') ?>.png); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;min-height:100vh;">
				<script>
					function spectate()
					{
						swal({   html: true, title: "¡Ya casi lo tienes!",   html: "Ejecuta el archivo game_<?php echo $gamedata['gameId'] ?> y tras unos 10 segundos ya estarás en modo espectador. <br> Si la pantalla de tu cliente se queda en negro, pulsa <b>ctrl + alt + supr</b> y se te desbloqueará.",   imageUrl: "<?php echo URL ?>/style/images/spectate/bg.png", imageSize:"200x200", timer:4000 });
					}
				</script>
				<form action="<?php echo URL ?>/spectate_game" method="post">
			    <input type="hidden" name="game_key" value="<?php echo $gamedata['observers']['encryptionKey'] ?>">
			    <input type="hidden" name="game_id" value="<?php echo $gamedata['gameId'] ?>">
				<input type="hidden" name="server" value="<?php echo $_GET['server'] ?> ">
				<button onclick="javascript:spectate()" type="submit" class="btn btn-success" style="margin-top:-4%"><i class="icon_download"></i> Espectear partida</button>
				</form>
				
				
				<?php
				$timing = (time()-datetounix($gamedata['gameStartTime']));
				$minutes = number_format(($timing/60),0);
				$seconds = $timing % 60;
				?>
				<button type="button" id="counter" style="float:right;margin-top:-4%;" class="btn btn-default"><span><i class="icon_clock_alt"></i><script language="javascript" > 
				var mins = <?php echo $minutes ?>; 
				var secs = <?php echo $timing ?>; 
				var hrs = 0; 
				var pepe; 
				function adjust(n)    { 
					var outpot = ""; 
					if (n < 10)    outpot = "0" + n ; 
					else outpot = "" + n; 
					return outpot; 
				} 

				function startimer()    { 

					document.timing.seconds.value = adjust(secs); 
					document.timing.minutes.value = adjust(mins); 
					setTimeout("setseconds()", 1000); 
				} 

				function setseconds()    { 
					if (++secs > 59)    { 
						setmins(); 
						secs = 0; 
					} 
					document.forms.timing.seconds.value = adjust(secs); 
					pepe=setTimeout("setseconds()", 1000); 
				} 

				function setmins()    { 
					if (++mins > 59)    { 
						mins = 0; 
						location.reload();
					} 
					document.forms.timing.minutes.value = adjust(mins); 
				} 

				 window.onload = startimer;
				</script> 
				Duración: <form name="timing" style="margin-left:2%;"><input style="border:none;width:20px;" type="text" name="minutes" size="2" maxlength="2" value="38">: <input style="border:none;width:20px;" type="text" name="seconds" size="2" maxlength="2" value="5"></form>  </span></button>
				
				<div class="container-fluid">
					<center><font color="white"><h1><?php echo mapidtotxt($gamedata['mapId']) ?> ~ <?php echo gametypestr(@$gamedata['gameQueueConfigId']) ?></h1></h1></font></center>
					

					<?php
					 function parsename_lookup($summoner)
				 {
					 $limit = 9;
					 if(strpos($summoner, ' ') == TRUE)
					 {
						 if(strlen($summoner) <= $limit)
						 {
							return '<h2 style="padding-top:4%;"> '.$summoner.' </h2>';
						 }
						 elseif(strlen($summoner)+3 < $limit)
						 {
							return '<h4 style="padding-top:4%;"> '.$summoner.' </h4>';
						 }
						 elseif(strlen($summoner)+3 > $limit)
						 {
							return '<h3 style="padding-top:4%;"> '.$summoner.' </h3>';
						 }
						 elseif(strlen($summoner) > $limit)
						 {
							return '<h5 style="padding-top:4%;"> '.$summoner.' </h5>';
						 }
					 }
					 elseif(strlen($summoner) <= $limit)
					 {
						return '<h1 style="padding-top:4%;"> '.$summoner.' </h1>';
					 }
					 elseif(strlen($summoner)+3 < $limit)
					 {
						return '<h3 style="padding-top:4%;"> '.$summoner.' </h3>';
					 }
					 elseif(strlen($summoner)+3 > $limit)
					 {
						return '<h2 style="padding-top:4%;"> '.$summoner.' </h2>';
					 }
					 elseif(strlen($summoner) > $limit)
					 {
						return '<h4 style="padding-top:4%;"> '.$summoner.' </h4>';
					 }
				 }
					?>
					//Falta poner a los putos jugadores con el foreach, y que si no estan en la DB se les cargue la liga .
								
                </div> <!-- end container -->
				
            </section><!-- .content --> 

            <!-- Footer
            ================================================== -->
         <?php echo footer(); ?>
            <!-- Back to top anchor -->
            <span class="back-to-top"></span>
        </div><!-- end .wrapper -->

        <!-- Javascript -->
        <script type="text/javascript" src="<?php echo URL ?>/style/js/grid.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/scripts.js"></script>
		<script>
		$(document).ready(function(){
			$('[data-toggle="tooltip"]').tooltip(); 
		});
		</script>
    </body>
</html>