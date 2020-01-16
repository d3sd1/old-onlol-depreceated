<?php
require('core/core.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
         <title>ONLoL ~ Estadísticas de victorias</title>
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
		 
        <!--Stylesheet-->
        <link rel="stylesheet" type="text/css" href="<?php echo URL ?>/style/css/style.css">
        
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
			<script src="<?php echo URL ?>/style/js/highcharts/highcharts.js"></script>
<script src="<?php echo URL ?>/style/js/highcharts/modules/funnel.js"></script>
<script src="<?php echo URL ?>/style/js/highcharts/modules/exporting.js"></script>
<script>
$(function () {
    $('#1_normals').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Normales'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victory_normals_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victory_normals_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#1_rankeds').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatorias en solitario'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victory_soloq_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victory_soloq_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#1_teams').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatorias por equipos'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victory_rankedteam_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victory_rankedteam_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#1_teamcreator').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Creador de equipos'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victory_teamcreator_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victory_teamcreator_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#1_aram').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'ARAM'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victory_aram_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victory_aram_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#1_dominion').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Dominion'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victory_dominion_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victory_dominion_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#2_soloq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria en solitario'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfblood_soloq_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfblood_soloq_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#2_teamq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria por equipos'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfblood_rankedteam_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfblood_rankedteam_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#3_soloq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria en solitario'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryftower_soloq_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryftower_soloq_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#3_teamq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria por equipos'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryftower_rankedteam_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryftower_rankedteam_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#4_soloq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria en solitario'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfherald_soloq_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfherald_soloq_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#4_teamq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria por equipos'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfherald_rankedteam_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfherald_rankedteam_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#5_soloq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria en solitario'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfinhib_soloq_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfinhib_soloq_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#5_teamq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria por equipos'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfinhib_rankedteam_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfinhib_rankedteam_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#6_soloq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria en solitario'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfdragon_soloq_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfdragon_soloq_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#6_teamq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria por equipos'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfdragon_rankedteam_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfdragon_rankedteam_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#7_soloq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria en solitario'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfbaron_soloq_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfbaron_soloq_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#7_teamq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria por equipos'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfbaron_rankedteam_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfbaron_rankedteam_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
	
$(function () {
    $('#8_soloq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria en solitario'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfblood_soloq_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfblood_soloq_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#8_teamq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria por equipos'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfblood_rankedteam_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfblood_rankedteam_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
	$(function () {
    $('#9_soloq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria en solitario'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfinhib_soloq_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfinhib_soloq_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
$(function () {
    $('#9_teamq').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Clasificatoria por equipos'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Estadísticas de victoria por equipo',
            data: [
                {
                    name: 'Lado morado',
                    y: <?php echo retstat('side_victoryfinhib_rankedteam_purple'); ?>,
                    color: 'purple'
                },
				{
                    name: 'Lazo azul',
                    y: <?php echo retstat('side_victoryfinhib_rankedteam_blue'); ?>,
                    color: 'blue'
                }
            ]
        }]
    });
});
</script>


            <!-- Content
            ================================================== -->
            <section class="content container no-padding" style="opacity:0.90;">
                <div class="row">
                    <div class="col-md-12" style="background-image: url(<?php echo URL ?>/style/images/home/bg/11.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
							<div class="row" style="background:white; text-align:center;border-radius: 0px 0px 28px 28px;-moz-border-radius: 0px 0px 28px 28px;-border: 0px solid #000000;"><h1>Estadísticas de victorias por lado</h1></div>
							
							<div class="row"><div class="col-md-4" id="1_normals" style="margin-top:2%;margin-left:-2%"></div>
							<div class="col-md-4" id="1_rankeds" style="margin-top:2%;margin-left:1%"></div>
							<div class="col-md-4" id="1_teams" style="margin-top:2%;margin-left:1%"></div></div>
							
							<div class="row"><div class="col-md-4" id="1_teamcreator" style="margin-top:2%;margin-left:-2%"></div>
							<div class="col-md-4" id="1_aram" style="margin-top:2%;margin-left:1%"></div>
							<div class="col-md-4" id="1_dominion" style="margin-top:2%;margin-left:1%"></div></div>
							
							<div class="row" style="margin-top:2%;background:white; text-align:center;border-radius: 28px 28px 28px 28px;-moz-border-radius: 28px 28px 28px 28px;-border: 0px solid #000000;"><h1>Estadísticas de victorias por primera sangre</h1></div>
							
							<div class="row"><div class="col-md-6" id="2_soloq" style="margin-top:2%;margin-left:-2%"></div>
							<div class="col-md-6" id="2_teamq" style="margin-top:2%;margin-left:1%"></div></div>
							
							<div class="row" style="margin-top:2%;background:white; text-align:center;border-radius: 28px 28px 28px 28px;-moz-border-radius: 28px 28px 28px 28px;-border: 0px solid #000000;"><h1>Estadísticas de victorias por primera torre</h1></div>
							
							<div class="row"><div class="col-md-6" id="3_soloq" style="margin-top:2%;margin-left:-2%"></div>
							<div class="col-md-6" id="3_teamq" style="margin-top:2%;margin-left:1%"></div></div>
							
							<div class="row" style="margin-top:2%;background:white; text-align:center;border-radius: 28px 28px 28px 28px;-moz-border-radius: 28px 28px 28px 28px;-border: 0px solid #000000;"><h1>Estadísticas de victorias por primer heraldo</h1></div>
							
							<div class="row"><div class="col-md-6" id="4_soloq" style="margin-top:2%;margin-left:-2%"></div>
							<div class="col-md-6" id="4_teamq" style="margin-top:2%;margin-left:1%"></div></div>
							
							<div class="row" style="margin-top:2%;background:white; text-align:center;border-radius: 28px 28px 28px 28px;-moz-border-radius: 28px 28px 28px 28px;-border: 0px solid #000000;"><h1>Estadísticas de victorias por primer inhibidor</h1></div>
							
							<div class="row"><div class="col-md-6" id="5_soloq" style="margin-top:2%;margin-left:-2%"></div>
							<div class="col-md-6" id="5_teamq" style="margin-top:2%;margin-left:1%"></div></div>
							
							<div class="row" style="margin-top:2%;background:white; text-align:center;border-radius: 28px 28px 28px 28px;-moz-border-radius: 28px 28px 28px 28px;-border: 0px solid #000000;"><h1>Estadísticas de victorias por primer dragón</h1></div>
							
							<div class="row"><div class="col-md-6" id="6_soloq" style="margin-top:2%;margin-left:-2%"></div>
							<div class="col-md-6" id="6_teamq" style="margin-top:2%;margin-left:1%"></div></div>
							
							<div class="row" style="margin-top:2%;background:white; text-align:center;border-radius: 28px 28px 28px 28px;-moz-border-radius: 28px 28px 28px 28px;-border: 0px solid #000000;"><h1>Estadísticas de victorias por primer Barón</h1></div>
							
							<div class="row"><div class="col-md-6" id="7_soloq" style="margin-top:2%;margin-left:-2%"></div>
							<div class="col-md-6" id="7_teamq" style="margin-top:2%;margin-left:1%"></div></div>
							
							<div class="row" style="margin-top:2%;background:white; text-align:center;border-radius: 28px 28px 28px 28px;-moz-border-radius: 28px 28px 28px 28px;-border: 0px solid #000000;"><h1>Estadísticas de victorias por primera sangre</h1></div>
							
							<div class="row"><div class="col-md-6" id="8_soloq" style="margin-top:2%;margin-left:-2%"></div>
							<div class="col-md-6" id="8_teamq" style="margin-top:2%;margin-left:1%"></div></div>
							
							<div class="row" style="margin-top:2%;background:white; text-align:center;border-radius: 28px 28px 28px 28px;-moz-border-radius: 28px 28px 28px 28px;-border: 0px solid #000000;"><h1>Estadísticas de victorias por primer inhibidor</h1></div>
							
							<div class="row"><div class="col-md-6" id="9_soloq" style="margin-top:2%;margin-left:-2%"></div>
							<div class="col-md-6" id="9_teamq" style="margin-top:2%;margin-left:1%"></div></div>
							
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
        <script type="text/javascript" src="<?php echo URL ?>/style/js/grid.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/scripts.js"></script>
    </body>
</html>