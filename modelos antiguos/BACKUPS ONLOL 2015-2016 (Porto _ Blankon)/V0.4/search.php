<?php
require('core/core.php');
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
        <link rel="stylesheet" type="text/css" href="<?php echo URL ?>/style/css/sweetalert.css">
		<?php echo rclickmenu() ?>
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

		<script>
		function openmodal()
		{
			swal({   title: "Cargando...",   html: "¡Pedalea Twisted!<br>", showConfirmButton: false, showCancelButton: false, allowEscapeKey:false, allowOutsideClick: false, imageUrl: "<?php echo URL ?>/style/images/loading.gif", imageSize:"300x300"});
		}
		</script>
			<?php
/* Parse server */
if(empty($_GET['server']))
{
	$_GET['server'] = 'all';
}
$server_lookup = parseserver($_GET['server'], true);

if($db->query('SELECT id FROM inv_users WHERE name="'.@$_GET['name'].'" AND region="'.parseserver(@$_COOKIE['onlol_region']).'"  LIMIT 1')->num_rows > 0 && empty($_GET['truesearch']))
{
	echo '<script language="javascript"> window.location = "'.URL.'/summoner/'.parseserver(@$_COOKIE['onlol_region']).'/'.$_GET['name'].'"</script>';
}
else
{
	
			if(empty($_GET['name']))
			{
				echo '<script>
				function search()
				{
					window.location="'.URL.'/search/all/" + document.getElementById("summonername").value + "&&truesearch=true";
				}
				</script>
				<section class="content padding3x error-404 not-found" style="background-image: url('.URL.'/style/images/not_found/background.jpg); background-size:100%; color:white;">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="page-header">
                                <h1><img src="'.URL.'/style/images/not_found/teemo.png" width="50%"></h1>
                            </div><!-- .page-header -->
                        </div><!-- .col-md-6 -->
                        <div class="col-md-6">
                            <div class="page-content">
                                <h2>Buscar invocador</h2>
                                <p>Buscar un invocador en todos los servidores.</p>
                                <form role="search" method="get" class="search-form" action="javascript:search();">
                                    <div class="input-group">
                                        <input type="search" autocomplete="off" id="summonername" class="search-field" placeholder="Nombre de invocador" required>
                                        <input type="submit" class="search-submit" value="Buscar">
                                    </div><!-- end .input-group -->
                                </form>

                                <p>La búsqueda soporta:</p>
                                <ul class="borderlist-not">
                                    <li>Buscar 1 invocador simultáneamente.</li>
                                    <li>Buscar en todas las regiones simultáneamente.</li>
                                    <li>Buscar en todas las codificaciones (Todos los alfabetos).</li>
                                </ul>
                            </div><!-- .page-content -->
                        </div><!-- .col-md-6 -->
                    </div><!-- .row -->

                </div><!-- .container -->
            </section>';
			}
			elseif(!empty($_GET['name']))
			{ 
				$summoner = str_replace(' ', '', strtolower($_GET['name']));
				if(url_exists('https://'.$server_lookup.'.api.pvp.net/api/lol/'.$server_lookup.'/v1.4/summoner/by-name/'.$_GET['name'].'?api_key='.LOL_API_KEY.'') == true)
				{
					echo '<script>window.location="'.URL.'/summoner/'.$server_lookup.'/'.$_GET['name'].'";</script>';
				}
				echo '<script>
				function search()
				{
					window.location="'.URL.'/search/all/" + document.getElementById("summonername").value;
				}
				</script>
				<section class="content padding3x error-404 not-found" style="background-image: url('.URL.'/style/images/not_found/background.jpg); background-size:100%; color:white;">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="page-header" style="background-color:#b3b3b3; padding: 2%; opacity:0.90;border-radius: 29px 29px 29px 29px;--webkit-border-radius: 29px 29px 29px 29px;border: 0px solid #000000;">
								<h2 style="text-align:center;"> '.$_GET['name'].':</h2>
								<table class="table table-bordered" style="">
    <thead>
      <tr>
        <th>Servidor</th>
        <th>Encontrado</th>
      </tr>
    </thead>
    <tbody>
      
    ';
					$search_summoner_filtered = str_replace(' ', '', strtolower($_GET['name']));
					/* EUW */
					if($db->query('SELECT id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="euw" LIMIT 1')->num_rows == 0)
					{
						if(url_exists('https://euw.api.pvp.net/api/lol/euw/v1.4/summoner/by-name/'.$search_summoner_filtered.'?api_key='.LOL_API_KEY.'') == false)
						{
							echo '<tr>
							<td>EUW</td>
							<td bgcolor="#b12525"></td></tr>';
						}
						else
						{
							echo '<tr>
							<td>EUW</td>
							<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/euw/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
						}
					}
					else
					{
						echo '<tr>
						<td>EUW</td>
						<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/euw/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
					}
					
					/* NA */
					if($db->query('SELECT id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="na" LIMIT 1')->num_rows == 0)
					{
						if(url_exists('https://na.api.pvp.net/api/lol/na/v1.4/summoner/by-name/'.$search_summoner_filtered.'?api_key='.LOL_API_KEY.'') == false)
						{
							echo '<tr>
							<td>na</td>
							<td bgcolor="#b12525"></td></tr>';
						}
						else
						{
							echo '<tr>
							<td>na</td>
							<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/na/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
						}
					}
					else
					{
						echo '<tr>
						<td>na</td>
						<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/na/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
					}
					
					/* BR */
					if($db->query('SELECT id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="br" LIMIT 1')->num_rows == 0)
					{
						if(url_exists('https://br.api.pvp.net/api/lol/br/v1.4/summoner/by-name/'.$search_summoner_filtered.'?api_key='.LOL_API_KEY.'') == false)
						{
							echo '<tr>
							<td>br</td>
							<td bgcolor="#b12525"></td></tr>';
						}
						else
						{
							echo '<tr>
							<td>br</td>
							<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/br/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
						}
					}
					else
					{
						echo '<tr>
						<td>br</td>
						<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/br/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
					}
					
					/* LAN */
					if($db->query('SELECT id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="lan" LIMIT 1')->num_rows == 0)
					{
						if(url_exists('https://lan.api.pvp.net/api/lol/lan/v1.4/summoner/by-name/'.$search_summoner_filtered.'?api_key='.LOL_API_KEY.'') == false)
						{
							echo '<tr>
							<td>lan</td>
							<td bgcolor="#b12525"></td></tr>';
						}
						else
						{
							echo '<tr>
							<td>lan</td>
							<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/lan/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
						}
					}
					else
					{
						echo '<tr>
						<td>lan</td>
						<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/lan/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
					}
					
					/* LAS */
					if($db->query('SELECT id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="las" LIMIT 1')->num_rows == 0)
					{
						if(url_exists('https://las.api.pvp.net/api/lol/las/v1.4/summoner/by-name/'.$search_summoner_filtered.'?api_key='.LOL_API_KEY.'') == false)
						{
							echo '<tr>
							<td>las</td>
							<td bgcolor="#b12525"></td></tr>';
						}
						else
						{
							echo '<tr>
							<td>las</td>
							<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/las/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
						}
					}
					else
					{
						echo '<tr>
						<td>las</td>
						<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/las/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
					}
					
					/* OCE */
					if($db->query('SELECT id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="oce" LIMIT 1')->num_rows == 0)
					{
						if(url_exists('https://oce.api.pvp.net/api/lol/oce/v1.4/summoner/by-name/'.$search_summoner_filtered.'?api_key='.LOL_API_KEY.'') == false)
						{
							echo '<tr>
							<td>oce</td>
							<td bgcolor="#b12525"></td></tr>';
						}
						else
						{
							echo '<tr>
							<td>oce</td>
							<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/oce/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
						}
					}
					else
					{
						echo '<tr>
						<td>oce</td>
						<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/oce/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
					}
					
					/* EUNE */
					if($db->query('SELECT id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="eune" LIMIT 1')->num_rows == 0)
					{
						if(url_exists('https://eune.api.pvp.net/api/lol/eune/v1.4/summoner/by-name/'.$search_summoner_filtered.'?api_key='.LOL_API_KEY.'') == false)
						{
							echo '<tr>
							<td>eune</td>
							<td bgcolor="#b12525"></td></tr>';
						}
						else
						{
							echo '<tr>
							<td>eune</td>
							<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/eune/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
						}
					}
					else
					{
						echo '<tr>
						<td>eune</td>
						<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/eune/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
					}
					
					/* TR */
					if($db->query('SELECT id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="tr" LIMIT 1')->num_rows == 0)
					{
						if(url_exists('https://tr.api.pvp.net/api/lol/tr/v1.4/summoner/by-name/'.$search_summoner_filtered.'?api_key='.LOL_API_KEY.'') == false)
						{
							echo '<tr>
							<td>tr</td>
							<td bgcolor="#b12525"></td></tr>';
						}
						else
						{
							echo '<tr>
							<td>tr</td>
							<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/tr/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
						}
					}
					else
					{
						echo '<tr>
						<td>tr</td>
						<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/tr/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
					}
					
					/* RU */
					if($db->query('SELECT id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="ru" LIMIT 1')->num_rows == 0)
					{
						if(url_exists('https://ru.api.pvp.net/api/lol/ru/v1.4/summoner/by-name/'.$search_summoner_filtered.'?api_key='.LOL_API_KEY.'') == false)
						{
							echo '<tr>
							<td>ru</td>
							<td bgcolor="#b12525"></td></tr>';
						}
						else
						{
							echo '<tr>
							<td>ru</td>
							<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/ru/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
						}
					}
					else
					{
						echo '<tr>
						<td>ru</td>
						<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/ru/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
					}
					
					/* KR */
					if($db->query('SELECT id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="kr" LIMIT 1')->num_rows == 0)
					{
						if(url_exists('https://kr.api.pvp.net/api/lol/kr/v1.4/summoner/by-name/'.$search_summoner_filtered.'?api_key='.LOL_API_KEY.'') == false)
						{
							echo '<tr>
							<td>kr</td>
							<td bgcolor="#b12525"></td></tr>';
						}
						else
						{
							echo '<tr>
							<td>kr</td>
							<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/kr/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
						}
					}
					else
					{
						echo '<tr>
						<td>kr</td>
						<td bgcolor="#67b123"><a style="color:white;" href="'.URL.'/summoner/kr/'.$_GET['name'].'"  onclick="javascript:openmodal();">Visitar perfil</a></td></a></tr>';
					}
					
				echo '			  
    </tbody>
  </table>
							</div><!-- .page-header -->
                        </div><!-- .col-md-6 -->
                        <div class="col-md-6">
                            <div class="page-content">
                                <h2>Buscar invocador</h2>
                                <p>Buscar un invocador en todos los servidores.</p>
                                <form role="search" method="get" class="search-form" action="javascript:search();">
                                    <div class="input-group">
                                        <input type="search" id="summonername" autocomplete="off" class="search-field" placeholder="Nombre de invocador" value="'.$_GET['name'].'" required>
                                        <input type="submit" class="search-submit" value="Buscar">
                                    </div><!-- end .input-group -->
                                </form>

                                <p>La búsqueda soporta:</p>
                                <ul class="borderlist-not">
                                    <li>Buscar 1 invocador simultáneamente.</li>
                                    <li>Buscar en todas las regiones simultáneamente.</li>
                                    <li>Buscar en todas las codificaciones (Todos los alfabetos).</li>
                                </ul>
                            </div><!-- .page-content -->
                        </div><!-- .col-md-6 -->
                    </div><!-- .row -->

                </div><!-- .container -->
            </section>';
			}
}
			?>
			


            <!-- Footer
            ================================================== -->
          <?php echo footer(); ?>
            <!-- Back to top anchor -->
            <span class="back-to-top"></span>
        </div><!-- end .wrapper -->

        <!-- Javascript -->
        <script type="text/javascript" src="<?php echo URL ?>/style/js/grid.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/scripts.js"></script>
    </body>
</html>