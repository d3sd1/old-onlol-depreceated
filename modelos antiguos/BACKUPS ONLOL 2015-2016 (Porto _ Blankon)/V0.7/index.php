<?php
require('core/core.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ONLoL</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="style/images/favicon.ico"/>

        <!--Fonts-->
        <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,400italic%7CRaleway:400,600' rel='stylesheet' type='text/css'>

        <!--jQuery and plugins-->
        <script type="text/javascript" src="style/js/jquery.min.js"></script>

        <!--Bootstrap-->
        <link rel="stylesheet" href="style/plugins/bootstrap/css/bootstrap.min.css">
        <script type="text/javascript" src="style/plugins/bootstrap/js/bootstrap.min.js"></script>

        <!--Icons and Animates-->
        <link rel="stylesheet" href="style/plugins/elegant-font/style.css">
        <link rel="stylesheet" href="style/plugins/et-line-font/style.css">
        <link rel="stylesheet" href="style/css/animate.min.css">

        <!--Masterslider-->
        <link rel="stylesheet" href="style/plugins/masterslider/style/masterslider.css">
        <link rel="stylesheet" href="style/plugins/masterslider/skins/default/style.css">
        <link rel="stylesheet" href="style/css/masterslider.css">
        <script type="text/javascript" src="style/plugins/masterslider/jquery.easing.min.js"></script>
        <script type="text/javascript" src="style/plugins/masterslider/masterslider.min.js"></script>
		<?php
		/* GET REGION */
		$region = parseserver(@$_COOKIE['onlol_region']);
		?>

        <!--Stylesheet-->
        <link rel="stylesheet" type="text/css" href="style/css/style.css">
		<style type="text/css">
		html { overflow-y:hidden; }
		</style>
		<!-- SWAL -->
        <script type="text/javascript" src="<?php echo URL ?>/style/js/sweetalert.min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo URL ?>/style/css/sweetalert.css">
		
		<?php echo rclickmenu() ?>
		<?php
		if(!empty($_GET['invalid_data']))
		{
			if($_GET['invalid_data'] == 'game_lookup')
			{
				echo '<script>swal(   "Error: Partida actual",   "Debes introducir un invocador a buscar.",   "error" )</script>';
			}
		}
		?>
		<script type="text/javascript">$(function(){

     $('a[href*=#]').click(function() {

     if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
         && location.hostname == this.hostname) {

             var $target = $(this.hash);

             $target = $target.length && $target || $('[name=' + this.hash.slice(1) +']');

             if ($target.length) {

                 var targetOffset = $target.offset().top;

                 $('html,body').animate({scrollTop: targetOffset}, 1000);

                 return false;

            }

       }

   });

});
					function goonenterinv(e) {
						tecla = (document.all) ? e.keyCode : e.which;
						if (tecla==13) {
							var inv_name = $('#inv_name').val();
							var inv_server = $('#inv_region').val();
							swal({   title: "Cargando...",   html: "Este proceso puede tomar unos minutos...<br>", showConfirmButton: false, showCancelButton: false, allowEscapeKey:false, allowOutsideClick: false, imageUrl: "<?php echo URL ?>/style/images/loading.gif", imageSize:"300x300"});
							if($('#inv_region').val() == 'ALL') { window.location= '<?php echo URL ?>/search/all/' + inv_name + '&&truesearch=true'} else {window.location= '<?php echo URL ?>/summoner/' + inv_server +'/' +  inv_name; }
						}
					}	
					
					function goonentergame(e) {
						tecla = (document.all) ? e.keyCode : e.which;
						if (tecla==13) {
							var inv_name = $('#inv_name').val();
							var inv_server = $('#inv_region').val();
							swal({   title: "Cargando...",   html: "¡Pedalea Twisted!<br>", showConfirmButton: false, showCancelButton: false, allowEscapeKey:false, allowOutsideClick: false, imageUrl: "<?php echo URL ?>/style/images/loading.gif", imageSize:"300x300"});
							window.location= '<?php echo URL ?>/game/' + inv_server +       '/' +       inv_name;
						}
					}	
					function search_inv_modalbox()
					{
						swal({   title: 'Búsqueda de invocador',   html: '<div class="col-sm-8"><input onkeypress="goonenterinv(event)" id="inv_name" type="text" class="form-control" required placeholder="Nombre del invocador" autofocus></div><div class="col-sm-4"><select id="inv_region" class="form-control" required> <option value="EUW"<?php if($region == 'EUW'){ echo ' selected';}?>>EUW</option><option value="NA"<?php if($region == 'NA'){ echo ' selected';}?>>NA</option><option value="BR"<?php if($region == 'BR'){ echo ' selected';}?>>BR</option><option value="LAN"<?php if($region == 'LAN'){ echo ' selected';}?>>LAN</option><option value="LAS"<?php if($region == 'LAS'){ echo ' selected';}?>>LAS</option><option value="OCE"<?php if($region == 'OCE'){ echo ' selected';}?>>OCE</option><option value="EUNE"<?php if($region == 'EUNE'){ echo ' selected';}?>>EUNE</option><option value="TR"<?php if($region == 'TR'){ echo ' selected';}?>>TR</option><option value="RU"<?php if($region == 'RU'){ echo ' selected';}?>>RU</option><option value="KR"<?php if($region == 'KR'){ echo ' selected';}?>>KR</option><option value="ALL">Todos</option></select></div>',   showCancelButton: true,   closeOnConfirm: false, cancelButtonText:'Cancelar', confirmButtonText:"Buscar" }, function() {   if($('#inv_region').val() == 'ALL') { var inv_name = $('#inv_name').val(); swal({   title: "Cargando...",   html: "Este proceso puede tomar unos minutos...<br>", showConfirmButton: false, showCancelButton: false, allowEscapeKey:false, allowOutsideClick: false, imageUrl: "<?php echo URL ?>/style/images/loading.gif", imageSize:"300x300"});window.location= '<?php echo URL ?>/search/all/' +  inv_name + '&&truesearch=true'} else { var inv_name = $('#inv_name').val(); var inv_server = $('#inv_region').val();swal({   title: "Cargando...",   html: "Este proceso puede tomar unos minutos...<br>", showConfirmButton: false, showCancelButton: false, allowEscapeKey:false, allowOutsideClick: false, imageUrl: "<?php echo URL ?>/style/images/loading.gif", imageSize:"300x300"}); window.location= '<?php echo URL ?>/summoner/' + inv_server +       '/' +       inv_name; }});
					}	
					function search_game_modalbox()
					{
						swal({   title: 'Búsqueda de invocador',   html: '<div class="col-sm-8"><input onkeypress="goonentergame(event)" id="inv_name" type="text" class="form-control" required placeholder="Nombre del invocador" autofocus></div><div class="col-sm-4"><select id="inv_region" class="form-control" required> <option value="EUW"<?php if($region == 'EUW'){ echo ' selected';}?>>EUW</option><option value="NA"<?php if($region == 'NA'){ echo ' selected';}?>>NA</option><option value="BR"<?php if($region == 'BR'){ echo ' selected';}?>>BR</option><option value="LAN"<?php if($region == 'LAN'){ echo ' selected';}?>>LAN</option><option value="LAS"<?php if($region == 'LAS'){ echo ' selected';}?>>LAS</option><option value="OCE"<?php if($region == 'OCE'){ echo ' selected';}?>>OCE</option><option value="EUNE"<?php if($region == 'EUNE'){ echo ' selected';}?>>EUNE</option><option value="TR"<?php if($region == 'TR'){ echo ' selected';}?>>TR</option><option value="RU"<?php if($region == 'RU'){ echo ' selected';}?>>RU</option><option value="KR"<?php if($region == 'KR'){ echo ' selected';}?>>KR</option></select></div>',   showCancelButton: true,   closeOnConfirm: false, cancelButtonText:'Cancelar', confirmButtonText:"Buscar" }, function() {   var inv_name = $('#inv_name').val(); var inv_server = $('#inv_region').val(); swal({   title: "Cargando...",   html: "¡Pedalea Twisted!<br>", showConfirmButton: false, showCancelButton: false, allowEscapeKey:false, allowOutsideClick: false, imageUrl: "<?php echo URL ?>/style/images/loading.gif", imageSize:"300x300"}); window.location= '<?php echo URL ?>/game/' + inv_region +       '/' +       inv_name; });
					}	
				</script>
        <!--Template: Item style 1 - COLORED CATEGORY-->
        <script type="text/template" id="tpl-item-style1">
            <div class="grid-item tpl-item-style1 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <div class="background" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"></div>
            <% if (typeof(categories) != "undefined") { %>
            <a href="javascript:;" class="category" style="background-color:<%= _.last(categories).color %>;"><%= _.last(categories).title %></a>
            <% } %>
            <div class="entry-item">
            <span class="format <%= format %>"></span>
            <h2><a href="<%= link %>" class="post-permalink"><%= title %></a></h2>
            <span class="date"><%= date %></span>
            </div>
            </div>
            </div>
        </script>


        <!--Template: Item style 2 - SWAP IMAGE-->
        <script type="text/template" id="tpl-item-style2">
            <div class="grid-item tpl-item-style2 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <a href="<%= link %>" class="post-permalink" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);">
            <span style="background-image:url(<%= typeof(_logo)!== 'undefined' ?  _logo : '' %>);"></span>
            </a>
            </div>
            </div>
        </script>


        <!--Template: Item style 3 - BOOK COVER-->
        <script type="text/template" id="tpl-item-style3">
            <div class="grid-item tpl-item-style3 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <div class="image" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"></div>
            <div class="hover">
            <div class="overlay" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>"></div>
            <span class="inner-table">
            <span class="inner-row"><h3><a href="<%= link %>" class="post-permalink"><%= title %></a></h3></span>
            </span>
            </div>
            </div>
            </div>
        </script>


        <!--Template: Item style 4 - PORTFOLIO with lightbox-->
        <script type="text/template" id="tpl-item-style4">
            <div class="grid-item tpl-item-style4 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <div class="image" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"></div>
            <div class="hover">
            <div class="overlay" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>"></div>
            <span class="inner-table">
            <span class="inner-row">
            <span class="link-icon">
            <a href="javascript:;"><i class="glyphicon glyphicon-link"></i></a>&nbsp;
            <a href="javascript:;" class="popup"><i class="glyphicon glyphicon-search"></i></a>
            </span>
            <h3><a href="<%= link %>" class="post-permalink"><%= title %></a></h3>
            </span>
            </span>
            </div>
            </div>
            </div>
        </script>


        <!--Template: Item style 5 - TITLE CAT HOVER-->
        <script type="text/template" id="tpl-item-style5">
            <div class="grid-item tpl-item-style5 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <div class="background" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"></div>
            <div class="entry-hover">
            <div class="inner-table">
            <div class="inner-row">
            <% if (typeof(categories) != "undefined") { %>
            <div class="categories">
            <% _.each(categories, function(cat){ %>
            <a href="javascript:;"><%= _.escape(cat.title) %></a>
            <% }); %>
            </div>
            <% } %>
            <h2><a href="<%= link %>" class="post-permalink"><%= title %></a></h2>
            </div>
            </div>
            </div>
            </div>
            </div>
        </script>


        <!--Template: Item style 6 - CUBIC HOVER-->
        <script type="text/template" id="tpl-item-style6">
            <div class="grid-item tpl-item-style6 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);">
            <div class="entry-hover">
            <div class="inner-table">
            <div class="inner-row">
            <h2><a href="<%= link %>" class="post-permalink"><%= title %></a></h2>
            <% if (typeof(categories) != "undefined") { %>
            <div class="categories">
            <% _.each(categories, function(cat){ %>
            <a href="javascript:;"><%= _.escape(cat.title) %></a> 
            <% }); %>
            </div>
            <% } %>
            </div>
            </div>
            </div>
            </div>
            </div>
        </script>



        <!--Template: Item style 7 - GRAY SCALE-->
        <script type="text/template" id="tpl-item-style7">
            <div class="grid-item tpl-item-style7 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <a href="<%= link %>" class="post-permalink" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);">
            <span style="background-image:url(<%= typeof(_logo)!== 'undefined' ?  _logo : '' %>);"></span>
            </a>
            </div>
            </div>
        </script>


        <!--Template: Item style 8 - TITLE BOTTOM HOVER-->
        <script type="text/template" id="tpl-item-style8">
            <div class="grid-item tpl-item-style8 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <div class="image" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"></div>
            <div class="hover">
            <span class="inner-table">
            <span class="inner-row">
            <h3><a href="<%= link %>" class="post-permalink"><%= title %></a></h3>
            <% if (typeof(categories) != "undefined") { %>
            <div class="categories">
            <% _.each(categories, function(cat){ %>
            <a href="javascript:;"><%= _.escape(cat.title) %></a> 
            <% }); %>
            </div>
            <% } %>
            </span>
            </span>
            </div>
            </div>
            </div>
        </script>


        <!--Template: Item style 9 - FLIP EFFECT-->
        <script type="text/template" id="tpl-item-style9">
            <div class="grid-item tpl-item-style9 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <div class="front" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"></div>
            <div class="back">
            <span class="inner-table">
            <span class="inner-row">
            <h3><a href="<%= link %>" class="post-permalink"><%= title %></a></h3>
            <% if (typeof(categories) != "undefined") { %>
            <div class="categories">
            <% _.each(categories, function(cat){ %>
            <a href="javascript:;"><%= _.escape(cat.title) %></a> 
            <% }); %>
            </div>
            <% } %>
            </span>
            </span>
            </div>
            </div>
            </div>
        </script>


        <!--Template: Item style 10 - AUTO FLIP EFFECT-->
        <script type="text/template" id="tpl-item-style10">
            <div class="grid-item tpl-item-style10 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <div class="front" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"><a href="#" class="post-permalink"></a></div>
            <div class="back" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"><a href="#" class="post-permalink"></a></div>
            </div>
            </div>
        </script>
        <script type="text/template" id="tpl-item-style11">
            <div class="grid-item tpl-item-style11 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <div class="front" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"><a href="#" class="post-permalink"></a></div>
            <div class="back" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"><a href="#" class="post-permalink"></a></div>
            </div>
            </div>
        </script>


        <!--Template: Item style 1 - MASONRY FOR PORTFOLIO-->
        <script type="text/template" id="tpl-masonry-style1">
            <div class="grid-item tpl-masonry-style1" data-id="<%= id %>">
            <div class="grid-item-entry">
            <div class="entry-media">
            <a href="<%= link %>" class="post-permalink"><img src="<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>" ></a>
            </div>
            <div class="entry-meta">
            <h2><a href="<%= link %>" class="post-permalink"><%= title %></a></h2>
            <% if (typeof(categories) != "undefined") { %>
            <div class="categories">
            <% _.each(categories, function(cat){ %>
            <a href="<%= _.escape(cat.link) %>"><%= _.escape(cat.title) %></a>
            <% }); %>
            </div>
            <% } %>
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
            <h3><a href="<%= link %>" class="post-permalink"><%= title %></a></h3>
            <div class="meta">
            <% if (typeof(categories) != "undefined") { %>
            <span class="categories">
            <i class="icon_tag_alt"></i>
            <a href="<%= _.last(categories).link %>"><%= _.last(categories).title %></a> 
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
            <a href="<%= link %>"><img src="<%= thumb %>" ></a>
            </div>
            <% } %>
            <div class="entry-meta">
            <h3><a href="<%= link %>" class="post-permalink"><%= title %></a></h3>
            <div class="meta">
            <% if (typeof(categories) != "undefined") { %>
            <span class="categories">
            <% _.each(categories, function(cat){ %>
            <a href="<%= _.escape(cat.link) %>"><%= _.escape(cat.title) %></a>
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
            <div class="col-md-6">
            <% if (typeof(categories) != "undefined") { %>
            <ul class="categories">
            <% _.each(categories, function(cat){ %>
            <li><a href="<%= _.escape(cat.link) %>"><%= _.escape(cat.title) %></a></li>
            <% }); %>
            </ul>
            <% } %>
            </div>
            <div class="col-md-6 text-right">
            <div class="date"><%= date %> <span class="comments">0 comments</span></div>
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
        </script>    </head>
    <body id="rclick" class="header-transparent header-fixed-top menu-light">

        <!-- Document wrapper
        ================================================== -->
        <div class="wrapper">

            <!-- Header
            ================================================== -->
            <header id="header" class="navbar navbar-default">
                <div class="container">

                    <!-- Logo
                    ================================================== -->
                    
                    <!-- Main navigation
================================================== -->
                    <?php echo nav('index'); ?>
					</div>
            </header>


            <!-- Top slider
            ================================================== -->
            <section class="top-slider fullscreen top-slider-3">
                <div class="slider-background" style="background: url(style/images/home/bg/<?php echo rand(1,11) ?>.jpg); background-repeat: no-repeat; background-size: 100%;">
                    <img src="style/images/home/ashe.png" alt="sona" style="bottom: 0%; left:-4%" width="30%" class="animated fadeInUp">
                    <img src="style/images/home/nunu.png" alt="nunu" style="bottom:0%; right:-4%;" width="25%" class="animated fadeInRight">
                </div>
                <!-- masterslider -->
                <div class="master-slider ms-skin-default" id="masterslider_01">

                    <div class="ms-slide slide-1">
                        <div class="ms-layer text-center slider-caption" data-type="text" data-effect="top(45)" data-duration="1000" data-ease="easeOutExpo">
                            <h2 class="big-text text-white bold-text no-margin">Buscar invocadores</h2>
                            <p class="text-bold text-white thin-text small-text">
								Busca el invocador deseado y analiza sus partidas.
                            </p>
                           <a href="javascript:search_inv_modalbox()"> <button class="btn styled btn-success" style="border-width:2px; margin-right:10px;">¡Buscar ahora!</button> </a>
                        </div>

                    </div>
					<div class="ms-slide slide-1">
                        <div class="ms-layer text-center slider-caption" data-type="text" data-effect="top(45)" data-duration="1000" data-ease="easeOutExpo">
                            <h2 class="big-text text-white bold-text no-margin">Partida actual</h2>
                            <p class="text-bold text-white thin-text small-text">
								¡Busca la partida actual del invocador elegido!
                            </p>
                            <a href="javascript:search_game_modalbox()"> <button class="btn styled btn-primary" style="border-width:2px; margin-right:10px;">¡Buscar ahora!</button> </a>
                        </div>

                    </div>
                    <div class="ms-slide slide-2">

                        <div class="ms-layer text-center slider-caption" data-type="text" data-effect="top(45)" data-delay="0" data-duration="1000" data-ease="easeOutExpo">
                            <h2 class="big-text text-white bold-text no-margin">Estadísticas</h2>
                            <p class="text-bold text-white thin-text small-text">
                                Información óptima sobre tí y sobre los mejores jugadores.
                            </p>
                            <a href="<?php echo URL ?>/statistics"><button class="btn styled btn-danger">Ver estadísticas generales</button></a>
                        </div>

                    </div>
                    <div class="ms-slide slide-3">

                        <div class="ms-layer text-center slider-caption" data-type="text" data-effect="top(45)" data-delay="0" data-duration="1000" data-ease="easeOutExpo">
                            <h2 class="big-text text-white bold-text no-margin">Noticias</h2>
                            <p class="text-bold text-white thin-text small-text">
                                Entérate de las últimas novedades y ofertas de League of Legends.
                            </p>
                            <a href="<?php echo URL ?>/news"><button class="btn styled btn-info">Últimas noticias</button></a>
                        </div>
                    </div>

                </div>
                <!-- end of masterslider -->

                <span class="mouse-wheel">
                    <i class="wheel"></i>
                </span>

            </section>

            <!-- Content
            ================================================== -->
            <section class="content no-padding">
                <div class="container">
                    
                </div><!-- .container -->
            </section><!-- .content -->

            <!-- Back to top anchor -->
            <span class="back-to-top"></span>
        </div><!-- end .wrapper -->

        <!-- Javascript -->
		<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-55250743-2', 'auto');
  ga('send', 'pageview');

</script>
        <script type="text/javascript" src="style/js/grid.js"></script>
        <script type="text/javascript" src="style/js/scripts.js"></script>
    </body>
</html>