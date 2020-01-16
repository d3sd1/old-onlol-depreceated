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
		<script type="text/javascript" src="<?php echo URL ?>/style/js/underscore-min.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/backbone-min.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/backbone-paginated-collection.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/isotope.pkgd.min.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/imagesloaded.pkgd.min.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/jquery.waypoints.min.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/jquery.validate.min.js"></script>
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
		<!-- Pnotify -->
		<script type="text/javascript" src="<?php echo URL ?>/style/pnotify/pnotify.js"></script>
		<link href="<?php echo URL ?>/style/pnotify/pnotify.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo URL ?>/style/pnotify/pnotify.brighttheme.css" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="<?php echo URL ?>/style/pnotify/pnotify.animate.js"></script>
		<script type="text/javascript" src="<?php echo URL ?>/style/pnotify/pnotify.buttons.js"></script>
		<link href="<?php echo URL ?>/style/pnotify/pnotify.buttons.css" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="<?php echo URL ?>/style/pnotify/pnotify.confirm.js"></script>
		<link href="<?php echo URL ?>/style/pnotify/pnotify.nonblock.css" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="<?php echo URL ?>/style/pnotify/pnotify.nonblock.js"></script>
		<script type="text/javascript" src="<?php echo URL ?>/style/pnotify/pnotify.mobile.js"></script>
		<link href="<?php echo URL ?>/style/pnotify/pnotify.mobile.css" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="<?php echo URL ?>/style/pnotify/pnotify.desktop.js"></script>
		<script type="text/javascript" src="<?php echo URL ?>/style/pnotify/pnotify.history.js"></script>
		<link href="<?php echo URL ?>/style/pnotify/pnotify.history.css" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="<?php echo URL ?>/style/pnotify/pnotify.callbacks.js"></script>
		<script type="text/javascript" src="<?php echo URL ?>/style/pnotify/pnotify.reference.js"></script>
		<script type="text/javascript">
    var stack_modal = {"dir1":"down", "dir2":"left", "push":"top", "modal": true, "overlay_close": true};

	function showlookup(type) {
		var opts = {
			addclass: "stack-custom",
			stack: stack_modal
		};
		switch (type) {
		case 'info':
			opts.title = "Buscador";
			opts.text = "Clicka la lupa y comienza tu búsqueda.";
			opts.icon = "arrow_left";
			break;
		}
		new PNotify(opts);
	}
	</script>


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
		if(!empty($_GET['not_found_user']))
		{
			echo '<script type="text/javascript">
							$( document ).ready(function() {
								new PNotify({
								title: "Invocador no encontrado",
								text: "'.$_GET['not_found_user'].' no existe en '.@$_GET['region'].'",
								type: "error"
								});
							});
						</script>';
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
				</script>
				<script>
		function searchbar()
		{
			new PNotify({
    title: 'Estas siendo redireccionado',
    text: 'Estás siendo redireccionado hacia ' + $('#string').val(),
    type: 'success',
    hide: false
});
			window.location='<?php echo URL ?>/' + $('#type').val() + '/' + $('#region').val() + '/' + $('#string').val();
		}
		</script>

        <!--Template: Header Search-->
        <script type="text/template" id="tpl-header-search">
            <div class="search-template" style="background:rgba(255,255,255,0.8);">
            <div class="inner-table">
            <div class="inner-row">
            <div class="container">
            <form class="search-form" action="javascript:searchbar();">
            <div class="input-group">
			<select class="search-region" id="region" class="wrapper-dropdown-1">
			<?php
			foreach($lol_servers as $server => $code)
			{
				if(parseserver(@$_COOKIE['onlol_region']) == $server)
				{
					$actualserver = 'selected';
				}
				else
				{
					$actualserver = null;
				}
				echo '<option value="'.$server.'" '.$actualserver.'>'.strtoupper($server).'</option>';
			}
			?>
			</select>
            <input type="search" class="search-field" placeholder="Introduce un invocador y pulsa enter...." id="string" autocomplete="off">
			<select class="search-type" id="type" class="wrapper-dropdown-1">
			<option value="summoner">Perfil invocador</option>
			<option value="game">Partida actual</option>
			</select>
            <input type="submit" class="search-submit" value="Buscar">
            <a href="javascript:;" class="close-search"><i style="color: #DC5555; margin-top:1%;" class="icon_close"></i></a>
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
                           <a href="javascript:showlookup('info');"> <button class="btn styled btn-success" style="border-width:2px; margin-right:10px;">¡Buscar ahora!</button> </a>
                        </div>

                    </div>
					<div class="ms-slide slide-1">
                        <div class="ms-layer text-center slider-caption" data-type="text" data-effect="top(45)" data-duration="1000" data-ease="easeOutExpo">
                            <h2 class="big-text text-white bold-text no-margin">Partida actual</h2>
                            <p class="text-bold text-white thin-text small-text">
								¡Busca la partida actual del invocador elegido!
                            </p>
                            <a href="javascript:showlookup('info');"> <button class="btn styled btn-primary" style="border-width:2px; margin-right:10px;">¡Buscar ahora!</button> </a>
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
                            <a href="http://news.<?php echo BASEURL ?>"><button class="btn styled btn-info">Últimas noticias</button></a>
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