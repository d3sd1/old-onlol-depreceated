<?php
require('core/core.php');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <title>ONLoL ~ Calculadora de maestrías</title>
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
        <link rel="stylesheet" type="text/css" href="<?php echo URL ?>/style/css/font-awesome.min.css">
		
		<?php echo rclickmenu() ?>
		
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



        <!--Template: Header Search-->
        <script type="text/template" id="tpl-header-search">
            <div class="search-template">
            <div class="inner-table">
            <div class="inner-row">
            <div class="container">
            <form role="search" method="get" class="search-form" action="blog.html">
            <div class="input-group">
            <input type="search" class="search-field" placeholder="Type and hit Enter ..." value="" name="s" autocomplete="off">
            <input type="submit" class="search-submit" value="Go">
            <a href="javascript:;" class="close-search"><i class="icon_close"></i></a>
            </div>
            </form>
            </div>
            </div>
            </div>
            </div>
        </script>    </head>
    <body>

        <!-- Document wrapper
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
                   <?php echo nav('utilities/mastery'); ?>
				   </div>
            </header>

            <!-- Content
            ================================================== -->
            <section class="content no-padding-bottom" style="background-image: url(<?php echo URL ?>/style/images/game/contact/map.jpg); height:200px; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
                <div class="container" >
                    <div class="row padding2x" >

                       
                        <div class="col-md-12" >
                            <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
							<link rel="stylesheet" href="<?php echo URL ?>/style/css/masteries/style.css" type="text/css" />
							 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
							<script type="text/javascript" src="<?php echo URL ?>/style/js/masteries/mousewheel/jquery.mousewheel.js"></script>
							<script type="text/javascript" src="<?php echo URL ?>/style/js/masteries/howler/howler.js"></script>
							<script type="type/javascript">
								// Preload sprites
								(new Image()).src = "images/button-icons.jpg";
								(new Image()).src = "images/frames.png";
								(new Image()).src = "images/keystone-bars.png";
								(new Image()).src = "images/tree-icons.png";
							</script>
						</head>
						<body>
							<div id="s2masteries">
								<div class="art-top"></div>
								<div class="art-bottom"></div>
								<div id="panel">
									<div class="left">
										<div id="points">
											<span>Points Available: </span><span class="count"></span>
										</div>
									</div>
									<div class="center">
										<a id="exportLink" href="#">Link</a>
									</div>
									<div class="right">
										<button id="return">Return Points</button>
									</div>
									<div id="tree-summaries"></div>
								</div>
								<div id="calculator"></div>
								
							</div>

							<script type="text/javascript" src="<?php echo URL ?>/style/js/masteries/data.js"></script>
							<script type="text/javascript">
							var languagePack = {
								tree_names: ["Ferocidad", "Cunning", "Resolve"],
								rank: "Rango",
								requires: "Requirere",
								points_in: "puntos en",
								swap_warning: "Escogiendo esta maestría quitarás puntos de",
								doubleclick_to_reset: "Doble click para reiniciar el árbol",
							};
							</script> 
							<script type="text/javascript" src="<?php echo URL ?>/style/js/masteries/calculator.js"></script>
                            
                        </div>

                    </div><!-- .row -->


                 

                </div><!-- end .container -->
            </section><!-- .content -->
 <div class="fullwidth-section no-padding" style="background-image: url(<?php echo URL ?>/style/images/game/contact/map.jpg); height:400px; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;"></div>
            <!-- Sub Footer
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