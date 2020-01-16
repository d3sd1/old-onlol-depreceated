<?php
require('core/core.php');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ONLoL ~ Rotación de campeones</title>
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

        <!--Template: Item style 3 - MASONRY-->
        <script type="text/template" id="tpl-masonry-style3" data-id="<%= id %>">
            <div class="grid-item tpl-masonry-style3">
            <div class="grid-item-entry">
            <% if (typeof(thumb) != "undefined") { %>
            <div class="entry-media">
            <a href="<%= link %>"><img style="border-radius: 30px 30px 30px 30px;-moz-border-radius: 30px 30px 30px 30px;-webkit-border-radius: 30px 30px 30px 30px;border: 0px solid #000000;" src="<%= thumb %>" ></a>
            </div>
            <% } %>
            <div class="entry-meta" style="background:white; width:60%; margin: auto; padding: 2% 0% 5% 0%;border-radius: 0px 0px 30px 30px;-moz-border-radius: 0px 0px 30px 30px;-webkit-border-radius: 0px 0px 30px 30px;border: 0px solid #000000;">
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
                        <a class="navbar-brand" href="<?php echo URL ?>">OnLoL</a>
                    </div>

                    <!-- Main navigation
================================================== -->
                    <?php echo nav('champs'); ?>                
                </div>
            </header>


            <!-- Content
            ================================================== -->
            <section class="content single-portfolio padding2x" style="background-image: url(<?php echo URL ?>/style/images/home/bg/1.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
                <div class="container">
<div class="col-md-12">
                                            <div id="grid_54f5908ef15b8" class="grid-container grid-masonry container-fluid" data-item-style="tpl-masonry-style3" data-open-state="modal" data-column="2" data-size-gutter="10">
                                                <div class="grid-viewport row" style="position: relative; height: 981px;"></div>
                                                <script type="text/template" class="grid-data">{"posts":[
												<?php
												$rotationchamps = $db->query('SELECT id,end_date,start_date,champion_id,new_price,old_price,skin_id FROM lol_sales') or die($db->error);
												$skin_orderid = 1;
												$totalsales = $db->query('SELECT id FROM lol_sales')->num_rows;
												while($row = $rotationchamps->fetch_array(MYSQL_ASSOC))
												{
													echo '{"id":'.$skin_orderid.',"title":"'.$db->query('SELECT skin_name FROM lol_skins WHERE champname="'.champidtokeyname($row['champion_id']).'" AND skin_num="'.$row['skin_id'].'"')->fetch_row()[0].'","link":"'.URL.'/champions/'.champidtokeyname($row['champion_id']).'","post_type":"portfolio","thumb":"'.URL.'/style/images/base/champions/splash/'.champidtokeyname($row['champion_id']).'_'.$row['skin_id'].'.jpg","categories":[{"cat_id":1,"title":"'.$row['old_price'].' -> '.$row['new_price'].'","link":""}],"tags":[]}';
													if($skin_orderid != $totalsales)
													{
														echo ',';
														$skin_orderid++;
													}
												}
												?>
												]}</script>
                                            <div class="clearfix"></div></div>
                                        </div>
                </div><!-- end container -->
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
    </body>
</html>