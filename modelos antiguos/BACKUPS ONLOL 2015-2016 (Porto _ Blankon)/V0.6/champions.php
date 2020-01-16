<?php
require('core/core.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ONLoL ~ Listado de campeones</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="style/images/favicon.ico"/>
		<!-- Data -->
		<!-- for Google -->
		<meta name="description" content="ONLoL: Un sitio web dedicado a League of Legends. Encuentra estadísticas, invocadores, campeones, información, noticias y wiki. ¡Mejora tu habilidad con ONLoL!" />
		<meta name="keywords" content="League of legends, LOL, Estadísticas LoL, Invocadores LoL, Invocadores, Invocador, Mejorar en LoL, Mejorar en League of Legends, Perfil League of legends" />

		<meta name="author" content="Andrei García" />
		<meta name="copyright" content="ONLoL" />
		<meta name="application-name" content="ONLoL" />

		<!-- for Facebook -->          
		<meta property="og:title" content="OnLoL ~ Campeones" />
		<meta property="og:type" content="article" />
		<meta property="og:image" content="http://www.onlol.net/style/images/base/champions/splash/Lucian_0.jpg" />
		<meta property="og:url" content="http://www.onlol.net/champions/" />
		<meta property="og:description" content="ONLoL: Un sitio web dedicado a League of Legends. Encuentra estadísticas, invocadores, campeones, información, noticias y wiki. ¡Mejora tu habilidad con ONLoL!" />

		<!-- for Twitter -->          
		<meta name="twitter:card" content="summary" />
		<meta name="twitter:title" content="ONLoL" />
		<meta name="twitter:description" content="ONLoL: Un sitio web dedicado a League of Legends. Encuentra estadísticas, invocadores, campeones, información, noticias y wiki. ¡Mejora tu habilidad con ONLoL!" />
		<meta name="twitter:image" content="http://www.onlol.net/style/images/base/champions/splash/Lucian_0.jpg" />
		
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

        <!--Masterslider-->
        <link rel="stylesheet" href="style/plugins/masterslider/style/masterslider.css">
        <link rel="stylesheet" href="style/plugins/masterslider/skins/default/style.css">
        <link rel="stylesheet" href="style/css/masterslider.css">
        <script type="text/javascript" src="style/plugins/masterslider/jquery.easing.min.js"></script>
        <script type="text/javascript" src="style/plugins/masterslider/masterslider.min.js"></script>
		<!-- SWAL -->
        <script type="text/javascript" src="<?php echo URL ?>/style/js/sweetalert.min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo URL ?>/style/css/sweetalert.css">
        <link rel="stylesheet" type="text/css" href="<?php echo URL ?>/style/css/font-awesome.min.css">
        <!--Stylesheet-->
        <link rel="stylesheet" type="text/css" href="style/css/style.css">
        
		 <script src="<?php echo URL ?>/style/js/autocomplete.jquery.js"></script>
		 <link type="text/css" rel="stylesheet" href="<?php echo URL ?>/style/css/autocomplete.css"></link>
		
		<?php echo rclickmenu() ?>
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
			
            </h2>
			<a href="<?php echo URL ?>/champions/<%= keyname %>"><button type="button" style="float:right; margin-top:-15%;" class="btn btn-success btn-animated"><span><i class="arrow_right"></i>Ver perfil</span></button></a>
			<div style="margin-top:-5%;">
            <%= content %>
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
                        <a class="navbar-brand" href="<?php echo URL ?>">Logo</a>
                    </div>

                    <!-- Main navigation
================================================== -->
                           <?php echo nav('champs'); ?>
                </div>
            </header>
            <!-- Page title
            ================================================== -->

            <!-- Content
            ================================================== -->
            <section class="content container no-padding" >
                <div class="row">
                    <div class="col-md-12">

                        <div class="fullwidth-section no-padding">
							 <div id="grid_54f5908f6efc8" style="background-image: url(<?php echo URL ?>/style/images/home/bg/11.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" class="grid-container grid-x" data-item-style="tpl-item-style1" data-open-state="modal" data-size-width="120" data-size-height="120" data-item-size="4x3" data-column="4" data-size-gutter="10">
								<script>
								$(document).ready(function(){
									/* Una vez que se cargo la pagina , llamo a todos los autocompletes y
									 * los inicializo */
									$('.autocomplete').autocomplete();
								});
							</script>
							<div class="row"><div class="col-md-4"></div><div class="col-md-4"><div class="autocomplete" style="text-align:center; background:white; opacity:0.95; padding:0% 2% 2% 2%;border-radius: 26px 26px 26px 26px;-moz-border-radius: 26px 26px 26px 26px;-webkit-border-radius: 26px 26px 26px 26px;border: 0px solid #000000;">
								<label style="display:block;">Buscador de campeones</label>
								<input  type="text" value="" data-source="<?php echo URL ?>/search_champ/" />
							</div></div></div>
							   <div class="grid-pager">
                                        <button data-filter="*" class="active">Todos los campeones</button>
                                        <button data-filter=".tank">Tanques</button>
                                        <button data-filter=".support">Soportes</button>
                                        <button data-filter=".marksman">Tiradores</button>
                                        <button data-filter=".fighter">Luchadores</button>
                                        <button data-filter=".assassin">Asesinos</button>
                                        <button data-filter=".mage">Magos</button>
                                    </div>

								<div class="grid-viewport row"></div>
                                <script type="text/template" class="grid-data">
							{"posts":[<?php
		$champ_getting = 1;
		$total_champs = $db->query('SELECT id FROM lol_champs')->num_rows;
		$champs_data = $db->query('SELECT id, champname,champ_id,champ_keyname,lore, title,role_1 FROM lol_champs ORDER BY champname');

		while ($champ_info = $champs_data->fetch_array(MYSQL_ASSOC)) {
			if($champ_info['id'] < $total_champs)
			{
				echo '{"id":'.$champ_info['id'].',"champ":"'.$champ_info['champname'].'","title":"<b>'.$champ_info['champname'].'</b>, '.$champ_info['title'].'","keyname":"'.$champ_info['champ_keyname'].'","format":"standard","media":"<img width=\"100%\" src=\"'.URL.'/style/images/base/champions/splash/'.$champ_info['champ_keyname'].'_0.jpg\">","link":"#","post_type":"post","grid_size":"1x1 '.$champ_info['role_1'].'","thumb":"'.URL.'/style/images/base/champions/little/'.$champ_info['champ_keyname'].'.png","content":"'.$champ_info['lore'].'","categories":[{"cat_id":3,"title":"Technology","color":"#5856D6","slug":"category_3","link":"#"}],"tags":[]},';
			}
			else
			{
				echo '{"id":'.$champ_info['id'].',"champ":"'.$champ_info['champname'].'","title":"<b>'.$champ_info['champname'].'</b>, '.$champ_info['title'].'","keyname":"'.$champ_info['champ_keyname'].'","format":"standard","media":"<img width=\"100%\" src=\"'.URL.'/style/images/base/champions/splash/'.$champ_info['champ_keyname'].'_0.jpg\">","link":"#","post_type":"post","grid_size":"1x1 '.$champ_info['role_1'].'","thumb":"'.URL.'/style/images/base/champions/little/'.$champ_info['champ_keyname'].'.png","content":"'.$champ_info['lore'].'","categories":[{"cat_id":3,"title":"Technology","color":"#5856D6","slug":"category_3","link":"#"}],"tags":[]}';
			}
			$champ_getting++;
		}
		?>]}</script></div>
								
                            <!--For demo
                            ==================================================-->
                            <script type="text/javascript">
function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) {
            return sParameterName[1];
        }
    }
}

$(function () {

    var p_grid = getUrlParameter('grid');
    var p_style = getUrlParameter('style');
    var p_open = getUrlParameter('open');

    p_style = typeof p_style !== 'undefined' && p_style != '' ? p_style : '1';
    p_open = typeof p_open !== 'undefined' && p_open != '' ? p_open : 'modal';

    if (typeof p_grid !== 'undefined' && p_grid != '') {

        p_style = p_grid == "masonry" ? "tpl-masonry-style" + p_style : "tpl-item-style" + p_style;

        $('.grid-container')
                .removeClass('grid-x grid-style grid-masonry')
                .addClass('grid-' + p_grid)
                .attr('data-item-style', p_style)
                .attr('data-open-state', p_open)
                .attr('data-size-gutter', p_grid == "masonry" ? 50 : 10);
    }
});
                            </script><!-- end demo script -->

                        </div>

                    </div>
                </div>
            </section>

            <!-- Footer
            ================================================== -->
            <?php echo footer(); ?>
            <span class="back-to-top"></span>
        </div><!-- end .wrapper -->

        <!-- Javascript -->
        <script type="text/javascript" src="style/js/grid.js"></script>
        <script type="text/javascript" src="style/js/scripts.js"></script>
    </body>
</html>