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

        <!--Masterslider-->
        <link rel="stylesheet" href="style/plugins/masterslider/style/masterslider.css">
        <link rel="stylesheet" href="style/plugins/masterslider/skins/default/style.css">
        <link rel="stylesheet" href="style/css/masterslider.css">
        <script type="text/javascript" src="style/plugins/masterslider/jquery.easing.min.js"></script>
        <script type="text/javascript" src="style/plugins/masterslider/masterslider.min.js"></script>

		
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
            <section class="content container no-padding" style="opacity:0.90;">
                <div class="row">
                    <div class="col-md-12" style="background-image: url(<?php echo URL ?>/style/images/home/bg/11.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
						<img draggable="false" style="margin-left:42%;" src="<?php echo URL ?>/style/images/game/server_status/heimer.png" width="15%">
                            <div class="panel-group" id="accordion2" role="tablist" aria-multiselectable="false">
									<!-- EUW -->
                                        <div class="panel panel-default">
										<?php
											$euw_status = readjson("http://status.leagueoflegends.com/shards/euw");
											
											if(array_slice($euw_status['services'], 1, 1)['0']['incidents'] != '')
											{
												$euw_color = 'background-color:#e3f832';
												$euw_txtstatus = 'Disponible pero con incidencias';
											} 
											else
											{
												
												$euw_color = 'background-color:green';
												$euw_txtstatus = 'Disponible';
											}
											if(array_slice($euw_status['services'], 1, 1)['0']['status'] == 'offline')
											{
												$euw_color = 'background-color:red';
												$euw_txtstatus = 'No disponible';
											}
										?>
                                            <div class="panel-heading" role="tab" id="server_euw" style="<?php echo $euw_color ?>">
                                                <h4 class="panel-title">
                                                    <a aria-expanded="false" aria-controls="collapse_server_euw" class="collapsed" data-toggle="collapse" href="#collapse_server_euw">
                                                        Estado de EUW: <?php echo $euw_txtstatus ?>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_server_euw" class="panel-collapse collapse" role="tabpanel" aria-labelledby="server_euw" style="height: 0px;<?php echo $euw_color ?>" aria-expanded="false">
                                                <div class="panel-body">
												<h3>Foros: <?php if(array_slice($euw_status['services'], 0, 1)['0']['status'] == 'online') {if(empty(array_slice($euw_status['services'], 0, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_forums_euw=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Juego: <?php if(array_slice($euw_status['services'], 1, 1)['0']['status'] == 'online') {if(empty(array_slice($euw_status['services'], 1, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_game_euw=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Tienda: <?php if(array_slice($euw_status['services'], 2, 1)['0']['status'] == 'online') {if(empty(array_slice($euw_status['services'], 2, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_shop_euw=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Web: <?php if(array_slice($euw_status['services'], 3, 1)['0']['status'] == 'online') {if(empty(array_slice($euw_status['services'], 3, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_web_euw=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<?php if(!empty($incidents_web_euw)) { print_r(array_slice($euw_status['services'], 3, 1)['0']['incidents']); } ?>
												<br>
												</div>
                                            </div>
                                        </div>
										<!-- NA -->
										<?php
											$na_status = readjson("http://status.leagueoflegends.com/shards/na");
											
											if(array_slice($na_status['services'], 1, 1)['0']['incidents'] != '')
											{
												$na_color = 'background-color:#e3f832';
												$na_txtstatus = 'Disponible pero con incidencias';
											} 
											else
											{
												
												$na_color = 'background-color:green';
												$na_txtstatus = 'Disponible';
											}
											if(array_slice($na_status['services'], 1, 1)['0']['status'] == 'offline')
											{
												$na_color = 'background-color:red';
												$na_txtstatus = 'No disponible';
											}
										?>
										<div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="server_na" style="<?php echo $na_color ?>">
                                                <h4 class="panel-title">
                                                    <a aria-expanded="false" aria-controls="collapse_server_na" class="collapsed" data-toggle="collapse" href="#collapse_server_na">
                                                       Estado de NA: <?php echo $na_txtstatus ?>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_server_na" class="panel-collapse collapse" role="tabpanel" aria-labelledby="server_na" style="height: 0px;<?php echo $na_color ?>" aria-expanded="false">
                                                <div class="panel-body">
												<h3>Foros: <?php if(array_slice($na_status['services'], 0, 1)['0']['status'] == 'online') {if(empty(array_slice($na_status['services'], 0, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_forums_na=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Juego: <?php if(array_slice($na_status['services'], 1, 1)['0']['status'] == 'online') {if(empty(array_slice($na_status['services'], 1, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_game_na=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Tienda: <?php if(array_slice($na_status['services'], 2, 1)['0']['status'] == 'online') {if(empty(array_slice($na_status['services'], 2, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_shop_na=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Web: <?php if(array_slice($na_status['services'], 3, 1)['0']['status'] == 'online') {if(empty(array_slice($na_status['services'], 3, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_web_na=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<?php if(!empty($incidents_web_na)) { print_r(array_slice($na_status['services'], 3, 1)['0']['incidents']); } ?>
												<br>
												</div></div>
                                        </div>
										
										<!-- BR -->
										<?php
											$br_status = readjson("http://status.leagueoflegends.com/shards/br");
											
											if(array_slice($br_status['services'], 1, 1)['0']['incidents'] != '')
											{
												$br_color = 'background-color:#e3f832';
												$br_txtstatus = 'Disponible pero con incidencias';
											} 
											else
											{
												
												$br_color = 'background-color:green';
												$br_txtstatus = 'Disponible';
											}
											if(array_slice($br_status['services'], 1, 1)['0']['status'] == 'offline')
											{
												$br_color = 'background-color:red';
												$br_txtstatus = 'No disponible';
											}
										?>
										<div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="server_br" style="<?php echo $br_color ?>">
                                                <h4 class="panel-title">
                                                    <a aria-expanded="false" aria-controls="collapse_server_br" class="collapsed" data-toggle="collapse" href="#collapse_server_br">
                                                       Estado de BR: <?php echo $br_txtstatus ?>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_server_br" class="panel-collapse collapse" role="tabpanel" aria-labelledby="server_br" style="height: 0px;<?php echo $br_color ?>" aria-expanded="false">
                                                <div class="panel-body">
												<h3>Foros: <?php if(array_slice($br_status['services'], 0, 1)['0']['status'] == 'online') {if(empty(array_slice($br_status['services'], 0, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_forums_br=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Juego: <?php if(array_slice($br_status['services'], 1, 1)['0']['status'] == 'online') {if(empty(array_slice($br_status['services'], 1, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_game_br=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Tienda: <?php if(array_slice($br_status['services'], 2, 1)['0']['status'] == 'online') {if(empty(array_slice($br_status['services'], 2, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_shop_br=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Web: <?php if(array_slice($br_status['services'], 3, 1)['0']['status'] == 'online') {if(empty(array_slice($br_status['services'], 3, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_web_br=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<?php if(!empty($incidents_web_br)) { print_r(array_slice($br_status['services'], 3, 1)['0']['incidents']); } ?>
												<br>
												</div></div>
                                        </div>
                                        
										<!-- LAN -->
										<?php
											$lan_status = readjson("http://status.leagueoflegends.com/shards/lan");
											
											if(array_slice($lan_status['services'], 1, 1)['0']['incidents'] != '')
											{
												$lan_color = 'background-color:#e3f832';
												$lan_txtstatus = 'Disponible pero con incidencias';
											} 
											else
											{
												
												$lan_color = 'background-color:green';
												$lan_txtstatus = 'Disponible';
											}
											if(array_slice($lan_status['services'], 1, 1)['0']['status'] == 'offline')
											{
												$lan_color = 'background-color:red';
												$lan_txtstatus = 'No disponible';
											}
										?>
										<div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="server_lan" style="<?php echo $lan_color ?>">
                                                <h4 class="panel-title">
                                                    <a aria-expanded="false" aria-controls="collapse_server_lan" class="collapsed" data-toggle="collapse" href="#collapse_server_lan">
                                                       Estado de LAN: <?php echo $lan_txtstatus ?>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_server_lan" class="panel-collapse collapse" role="tabpanel" aria-labelledby="server_lan" style="height: 0px;<?php echo $lan_color ?>" aria-expanded="false">
                                                <div class="panel-body">
												<h3>Foros: <?php if(array_slice($lan_status['services'], 0, 1)['0']['status'] == 'online') {if(empty(array_slice($lan_status['services'], 0, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_forums_lan=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Juego: <?php if(array_slice($lan_status['services'], 1, 1)['0']['status'] == 'online') {if(empty(array_slice($lan_status['services'], 1, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_game_lan=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Tienda: <?php if(array_slice($lan_status['services'], 2, 1)['0']['status'] == 'online') {if(empty(array_slice($lan_status['services'], 2, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_shop_lan=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Web: <?php if(array_slice($lan_status['services'], 3, 1)['0']['status'] == 'online') {if(empty(array_slice($lan_status['services'], 3, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_web_lan=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<?php if(!empty($incidents_web_lan)) { print_r(array_slice($lan_status['services'], 3, 1)['0']['incidents']); } ?>
												<br>
												</div></div>
                                        </div>
										
										<!-- LAS -->
										<?php
											$las_status = readjson("http://status.leagueoflegends.com/shards/las");
											
											if(array_slice($las_status['services'], 1, 1)['0']['incidents'] != '')
											{
												$las_color = 'background-color:#e3f832';
												$las_txtstatus = 'Disponible pero con incidencias';
											} 
											else
											{
												
												$las_color = 'background-color:green';
												$las_txtstatus = 'Disponible';
											}
											if(array_slice($las_status['services'], 1, 1)['0']['status'] == 'offline')
											{
												$las_color = 'background-color:red';
												$las_txtstatus = 'No disponible';
											}
										?>
										<div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="server_las" style="<?php echo $las_color ?>">
                                                <h4 class="panel-title">
                                                    <a aria-expanded="false" aria-controls="collapse_server_las" class="collapsed" data-toggle="collapse" href="#collapse_server_las">
                                                       Estado de LAS: <?php echo $las_txtstatus ?>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_server_las" class="panel-collapse collapse" role="tabpanel" aria-labelledby="server_las" style="height: 0px;<?php echo $las_color ?>" aria-expanded="false">
                                                <div class="panel-body">
												<h3>Foros: <?php if(array_slice($las_status['services'], 0, 1)['0']['status'] == 'online') {if(empty(array_slice($las_status['services'], 0, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_forums_las=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Juego: <?php if(array_slice($las_status['services'], 1, 1)['0']['status'] == 'online') {if(empty(array_slice($las_status['services'], 1, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_game_las=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Tienda: <?php if(array_slice($las_status['services'], 2, 1)['0']['status'] == 'online') {if(empty(array_slice($las_status['services'], 2, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_shop_las=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Web: <?php if(array_slice($las_status['services'], 3, 1)['0']['status'] == 'online') {if(empty(array_slice($las_status['services'], 3, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_web_las=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<?php if(!empty($incidents_web_las)) { print_r(array_slice($las_status['services'], 3, 1)['0']['incidents']); } ?>
												<br>
												</div></div>
                                        </div>
										
										<!-- OCE -->
										<?php
											$oce_status = readjson("http://status.leagueoflegends.com/shards/oce");
											
											if(array_slice($oce_status['services'], 1, 1)['0']['incidents'] != '')
											{
												$oce_color = 'background-color:#e3f832';
												$oce_txtstatus = 'Disponible pero con incidencias';
											} 
											else
											{
												
												$oce_color = 'background-color:green';
												$oce_txtstatus = 'Disponible';
											}
											if(array_slice($oce_status['services'], 1, 1)['0']['status'] == 'offline')
											{
												$oce_color = 'background-color:red';
												$oce_txtstatus = 'No disponible';
											}
										?>
										<div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="server_oce" style="<?php echo $oce_color ?>">
                                                <h4 class="panel-title">
                                                    <a aria-expanded="false" aria-controls="collapse_server_oce" class="collapsed" data-toggle="collapse" href="#collapse_server_oce">
                                                       Estado de OCE: <?php echo $oce_txtstatus ?>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_server_oce" class="panel-collapse collapse" role="tabpanel" aria-labelledby="server_oce" style="height: 0px;<?php echo $oce_color ?>" aria-expanded="false">
                                                <div class="panel-body">
												<h3>Foros: <?php if(array_slice($oce_status['services'], 0, 1)['0']['status'] == 'online') {if(empty(array_slice($oce_status['services'], 0, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_forums_oce=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Juego: <?php if(array_slice($oce_status['services'], 1, 1)['0']['status'] == 'online') {if(empty(array_slice($oce_status['services'], 1, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_game_oce=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Tienda: <?php if(array_slice($oce_status['services'], 2, 1)['0']['status'] == 'online') {if(empty(array_slice($oce_status['services'], 2, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_shop_oce=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Web: <?php if(array_slice($oce_status['services'], 3, 1)['0']['status'] == 'online') {if(empty(array_slice($oce_status['services'], 3, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_web_oce=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<?php if(!empty($incidents_web_oce)) { print_r(array_slice($oce_status['services'], 3, 1)['0']['incidents']); } ?>
												<br>
												</div></div>
                                        </div>
										
										<!-- EUNE -->
										<?php
											$eune_status = readjson("http://status.leagueoflegends.com/shards/eune");
											
											if(array_slice($eune_status['services'], 1, 1)['0']['incidents'] != '')
											{
												$eune_color = 'background-color:#e3f832';
												$eune_txtstatus = 'Disponible pero con incidencias';
											} 
											else
											{
												
												$eune_color = 'background-color:green';
												$eune_txtstatus = 'Disponible';
											}
											if(array_slice($eune_status['services'], 1, 1)['0']['status'] == 'offline')
											{
												$eune_color = 'background-color:red';
												$eune_txtstatus = 'No disponible';
											}
										?>
										<div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="server_eune" style="<?php echo $eune_color ?>">
                                                <h4 class="panel-title">
                                                    <a aria-expanded="false" aria-controls="collapse_server_eune" class="collapsed" data-toggle="collapse" href="#collapse_server_eune">
                                                       Estado de EUNE: <?php echo $eune_txtstatus ?>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_server_eune" class="panel-collapse collapse" role="tabpanel" aria-labelledby="server_eune" style="height: 0px;<?php echo $eune_color ?>" aria-expanded="false">
                                                <div class="panel-body">
												<h3>Foros: <?php if(array_slice($eune_status['services'], 0, 1)['0']['status'] == 'online') {if(empty(array_slice($eune_status['services'], 0, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_forums_eune=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Juego: <?php if(array_slice($eune_status['services'], 1, 1)['0']['status'] == 'online') {if(empty(array_slice($eune_status['services'], 1, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_game_eune=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Tienda: <?php if(array_slice($eune_status['services'], 2, 1)['0']['status'] == 'online') {if(empty(array_slice($eune_status['services'], 2, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_shop_eune=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Web: <?php if(array_slice($eune_status['services'], 3, 1)['0']['status'] == 'online') {if(empty(array_slice($eune_status['services'], 3, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_web_eune=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<?php if(!empty($incidents_web_eune)) { print_r(array_slice($eune_status['services'], 3, 1)['0']['incidents']); } ?>
												<br>
												</div></div>
                                        </div>
										
										<!-- TR -->
										<?php
											$tr_status = readjson("http://status.leagueoflegends.com/shards/tr");
											
											if(array_slice($tr_status['services'], 1, 1)['0']['incidents'] != '')
											{
												$tr_color = 'background-color:#e3f832';
												$tr_txtstatus = 'Disponible pero con incidencias';
											} 
											else
											{
												
												$tr_color = 'background-color:green';
												$tr_txtstatus = 'Disponible';
											}
											if(array_slice($tr_status['services'], 1, 1)['0']['status'] == 'offline')
											{
												$tr_color = 'background-color:red';
												$tr_txtstatus = 'No disponible';
											}
										?>
										<div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="server_tr" style="<?php echo $tr_color ?>">
                                                <h4 class="panel-title">
                                                    <a aria-expanded="false" aria-controls="collapse_server_tr" class="collapsed" data-toggle="collapse" href="#collapse_server_tr">
                                                       Estado de TR: <?php echo $tr_txtstatus ?>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_server_tr" class="panel-collapse collapse" role="tabpanel" aria-labelledby="server_tr" style="height: 0px;<?php echo $tr_color ?>" aria-expanded="false">
                                                <div class="panel-body">
												<h3>Foros: <?php if(array_slice($tr_status['services'], 0, 1)['0']['status'] == 'online') {if(empty(array_slice($tr_status['services'], 0, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_forums_tr=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Juego: <?php if(array_slice($tr_status['services'], 1, 1)['0']['status'] == 'online') {if(empty(array_slice($tr_status['services'], 1, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_game_tr=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Tienda: <?php if(array_slice($tr_status['services'], 2, 1)['0']['status'] == 'online') {if(empty(array_slice($tr_status['services'], 2, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_shop_tr=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Web: <?php if(array_slice($tr_status['services'], 3, 1)['0']['status'] == 'online') {if(empty(array_slice($tr_status['services'], 3, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_web_tr=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<?php if(!empty($incidents_web_tr)) { print_r(array_slice($tr_status['services'], 3, 1)['0']['incidents']); } ?>
												<br>
												</div></div>
                                        </div>
										<!-- RU -->
										<?php
											$ru_status = readjson("http://status.leagueoflegends.com/shards/ru");
											
											if(array_slice($ru_status['services'], 1, 1)['0']['incidents'] != '')
											{
												$ru_color = 'background-color:#e3f832';
												$ru_txtstatus = 'Disponible pero con incidencias';
											} 
											else
											{
												
												$ru_color = 'background-color:green';
												$ru_txtstatus = 'Disponible';
											}
											if(array_slice($ru_status['services'], 1, 1)['0']['status'] == 'offline')
											{
												$ru_color = 'background-color:red';
												$ru_txtstatus = 'No disponible';
											}
										?>
										<div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="server_ru" style="<?php echo $ru_color ?>">
                                                <h4 class="panel-title">
                                                    <a aria-expanded="false" aria-controls="collapse_server_ru" class="collapsed" data-toggle="collapse" href="#collapse_server_ru">
                                                       Estado de RU: <?php echo $ru_txtstatus ?>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_server_ru" class="panel-collapse collapse" role="tabpanel" aria-labelledby="server_ru" style="height: 0px;<?php echo $ru_color ?>" aria-expanded="false">
                                                <div class="panel-body">
												<h3>Foros: <?php if(array_slice($ru_status['services'], 0, 1)['0']['status'] == 'online') {if(empty(array_slice($ru_status['services'], 0, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_forums_ru=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Juego: <?php if(array_slice($ru_status['services'], 1, 1)['0']['status'] == 'online') {if(empty(array_slice($ru_status['services'], 1, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_game_ru=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Tienda: <?php if(array_slice($ru_status['services'], 2, 1)['0']['status'] == 'online') {if(empty(array_slice($ru_status['services'], 2, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_shop_ru=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Web: <?php if(array_slice($ru_status['services'], 3, 1)['0']['status'] == 'online') {if(empty(array_slice($ru_status['services'], 3, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_web_ru=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<?php if(!empty($incidents_web_ru)) { print_r(array_slice($ru_status['services'], 3, 1)['0']['incidents']); } ?>
												<br>
												</div></div>
                                        </div>
										<!-- pbe -->
										<?php
											$pbe_status = readjson("http://status.pbe.leagueoflegends.com/shards/pbe");
											
											if(array_slice($pbe_status['services'], 1, 1)['0']['incidents'] != '')
											{
												$pbe_color = 'background-color:#e3f832';
												$pbe_txtstatus = 'Disponible pero con incidencias';
											} 
											else
											{
												
												$pbe_color = 'background-color:green';
												$pbe_txtstatus = 'Disponible';
											}
											if(array_slice($pbe_status['services'], 1, 1)['0']['status'] == 'offline')
											{
												$pbe_color = 'background-color:red';
												$pbe_txtstatus = 'No disponible';
											}
										?>
										<div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="server_pbe" style="<?php echo $pbe_color ?>">
                                                <h4 class="panel-title">
                                                    <a aria-expanded="false" aria-controls="collapse_server_pbe" class="collapsed" data-toggle="collapse" href="#collapse_server_pbe">
                                                       Estado de PBE: <?php echo $pbe_txtstatus ?>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_server_pbe" class="panel-collapse collapse" role="tabpanel" aria-labelledby="server_pbe" style="height: 0px;<?php echo $pbe_color ?>" aria-expanded="false">
                                                <div class="panel-body">
												<h3>Foros: <?php if(array_slice($pbe_status['services'], 0, 1)['0']['status'] == 'online') {if(empty(array_slice($pbe_status['services'], 0, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_forums_pbe=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Juego: <?php if(array_slice($pbe_status['services'], 1, 1)['0']['status'] == 'online') {if(empty(array_slice($pbe_status['services'], 1, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_game_pbe=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Tienda: <?php if(array_slice($pbe_status['services'], 2, 1)['0']['status'] == 'online') {if(empty(array_slice($pbe_status['services'], 2, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_shop_pbe=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<br>
												<h3>Web: <?php if(array_slice($pbe_status['services'], 3, 1)['0']['status'] == 'online') {if(empty(array_slice($pbe_status['services'], 3, 1)['0']['incidents'])) {echo '<i class="icon_check"></i>';} else{echo '<i class="icon_error-triangle_alt"></i>';$incidents_web_pbe=true;}} else {echo '<i class="icon_close_alt2"></i>';} ?></h3>
												<?php if(!empty($incidents_web_pbe)) { print_r(array_slice($pbe_status['services'], 3, 1)['0']['incidents']); } ?>
												<br>
												</div></div>
                                        </div>
                                    </div>

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