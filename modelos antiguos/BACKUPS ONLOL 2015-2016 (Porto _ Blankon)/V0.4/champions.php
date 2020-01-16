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

        <!--Stylesheet-->
        <link rel="stylesheet" type="text/css" href="style/css/style.css">
        
		
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
                            <!--
            container-class :
                .grid-x:
                    data-item-style="" //tpl-item-style[1-8]
                    data-size-width="120"
                    data-size-height="120"
                    data-open-state=""  // modal | push-state | blank
                    data-size-gutter=""
                .grid-style:
                    data-item-style="" //tpl-item-style[1-8]
                    data-item-size="4x3" // 1x1 | 16x9 | 4x3 | 3x4
                    data-column="4"
                    data-open-state=""  // modal | open-gi | blank
                    data-size-gutter=""
                .grid-masonry:
                    data-item-style="" //tpl-masonry-style[1-2]
                    data-column="4"
                    data-open-state="" // modal | blank
                    data-size-gutter=""
            

            -example html-
            <div id="grid_uniq_id" class="grid-container grid-x"  
                                    data-item-style="tpl-item-style1"
                                    data-open-state="modal"
                                    data-size-width="120"
                                    data-size-height="120"
                                    data-size-gutter="0">
                <div class="grid-viewport row"></div>
                <script type="text/template" class="grid-data">{ include json format }</script>
            </div>
                            -->
							 <div id="grid_54f5908f6efc8" style="background-image: url(<?php echo URL ?>/style/images/home/bg/11.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" class="grid-container grid-x" data-item-style="tpl-item-style1" data-open-state="modal" data-size-width="120" data-size-height="120" data-item-size="4x3" data-column="4" data-size-gutter="10">
                                <div class="grid-pager">
                                        <button data-filter="*" class="active">Todos los campeones</button>
                                        <button data-filter=".tank">Tanques</button>
                                        <button data-filter=".support">Soportes</button>
                                        <button data-filter=".marksman">Tiradorres</button>
                                        <button data-filter=".fighter">Luchadores</button>
                                        <button data-filter=".assassin">Asesinos</button>
                                    </div>

								<div class="grid-viewport row"></div>
                                <script type="text/template" class="grid-data">
							{"posts":[<?php
		$champ_getting = 1;
		$total_champs = $db->query('SELECT id FROM lol_champs')->num_rows;
		$champs_data = $db->query('SELECT id, champname, img_little, es_lore, es_title,role_1 FROM lol_champs ORDER BY champname');

		while ($champ_info = $champs_data->fetch_array(MYSQL_ASSOC)) {
			if($champ_info['id'] < $total_champs)
			{
				echo '{"id":'.$champ_info['id'].',"champ":"'.$champ_info['champname'].'","title":"<b>'.$champ_info['champname'].'</b>, '.$champ_info['es_title'].'","format":"standard","media":"<img width=\"100%\" src=\"'.URL.'/style/images/base/champions/splash/'.$champ_info['champname'].'_0.jpg\">","link":"#","post_type":"post","grid_size":"1x1 '.$champ_info['role_1'].'","thumb":"'.URL.'/style/images/base/champions/little/'.$champ_info['img_little'].'","content":"'.$champ_info['es_lore'].'","categories":[{"cat_id":3,"title":"Technology","color":"#5856D6","slug":"category_3","link":"#"}],"tags":[]},';
			}
			else
			{
				echo '{"id":'.$champ_info['id'].',"champ":"'.$champ_info['champname'].'","title":"<b>'.$champ_info['champname'].'</b>, '.$champ_info['es_title'].'","format":"standard","media":"<img width=\"100%\" src=\"'.URL.'/style/images/base/champions/splash/'.$champ_info['champname'].'_0.jpg\">","link":"#","post_type":"post","grid_size":"1x1 '.$champ_info['role_1'].'","thumb":"'.URL.'/style/images/base/champions/little/'.$champ_info['img_little'].'","content":"'.$champ_info['es_lore'].'","categories":[{"cat_id":3,"title":"Technology","color":"#5856D6","slug":"category_3","link":"#"}],"tags":[]}';
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