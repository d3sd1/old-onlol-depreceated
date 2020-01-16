<?php
require('core/core.php');
if(empty($_GET['key']))
{
	header('Location: '.URL.'/champions?invalid_champion=true');
}

if($db->query('SELECT id FROM lol_champs WHERE champ_keyname="'.$_GET['key'].'"')->num_rows == 0)
{
	header('Location: '.URL.'/champions?invalid_champion=true');
}

$champ = $db->query('SELECT champ_id,is_rotation,id,champ_keyname,champname,lore,title,role_1,role_2,kit_bar,base_hp,scale_hp_lvl,base_bar,scale_bar_lvl,movspeed,base_armor,scale_armor_lvl,base_spellblock,scale_spellblock_lvl,attackrange,base_hpregen,scale_hpregen_lvl,base_manareg,scale_manareg_lvl,base_crit,scale_crit_lvl,base_ad,scale_ad_lvl,offset_as,scale_as_lvl FROM lol_champs WHERE champ_keyname="'.$_GET['key'].'"')->fetch_array(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ONLoL ~ <?php echo $champ['champname'].', '.$champ['title']; ?></title>
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
        <!-- Tabs -->
		<link rel="stylesheet" href="<?php echo URL ?>/style/css/tabs.css">
		<!-- SWAL -->
        <script type="text/javascript" src="<?php echo URL ?>/style/js/sweetalert.min.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo URL ?>/style/css/sweetalert.css">
		<!-- Players table -->
		<script type="text/javascript" language="javascript" src="<?php echo URL ?>/style/js/jquery.dataTables.js"></script>
        <link rel="stylesheet" href="<?php echo URL ?>/style/css/jquery.dataTables.min.css">
		<script type="text/javascript" language="javascript">$(document).ready(function() {
		$('#bestsummoners').DataTable( {
			"order": [[ 0, "asc" ]],
			initComplete: function () {
				this.api().columns().every( function () {
					var column = this;
					var select = $('<select><option value=""></option></select>')
						.appendTo( $(column.footer()).empty() )
						.on( 'change', function () {
							var val = $.fn.dataTable.util.escapeRegex(
								$(this).val()
							);
							column
								.search( val ? '^'+val+'$' : '', true, false )
								.draw();
						} );

					column.data().unique().sort().each( function ( d, j ) {
						select.append( '<option value="'+d+'">'+d+'</option>' )
					} );
				} );
			}
		} );
		
		$('#moreplayssummoners').DataTable( {
			"order": [[ 0, "asc" ]],
			initComplete: function () {
				this.api().columns().every( function () {
					var column = this;
					var select = $('<select><option value=""></option></select>')
						.appendTo( $(column.footer()).empty() )
						.on( 'change', function () {
							var val = $.fn.dataTable.util.escapeRegex(
								$(this).val()
							);
							column
								.search( val ? '^'+val+'$' : '', true, false )
								.draw();
						} );

					column.data().unique().sort().each( function ( d, j ) {
						select.append( '<option value="'+d+'">'+d+'</option>' )
					} );
				} );
			}
		} );
		$('#masterysummoners').DataTable( {
			"order": [[ 0, "asc" ]],
			initComplete: function () {
				this.api().columns().every( function () {
					var column = this;
					var select = $('<select><option value=""></option></select>')
						.appendTo( $(column.footer()).empty() )
						.on( 'change', function () {
							var val = $.fn.dataTable.util.escapeRegex(
								$(this).val()
							);
							column
								.search( val ? '^'+val+'$' : '', true, false )
								.draw();
						} );

					column.data().unique().sort().each( function ( d, j ) {
						select.append( '<option value="'+d+'">'+d+'</option>' )
					} );
				} );
			}
		} );
		} );</script>
		
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
            <div class="date"><%= cost %></div>
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
                        <a class="navbar-brand" href="<?php echo URL ?>">OnLoL</a>
                    </div>

                    <!-- Main navigation
================================================== -->
                    <?php echo nav('champs'); ?>                
                </div>
            </header>


            <!-- Content
            ================================================== -->
            <section class="content single-portfolio padding2x" style="background-image: url(<?php echo URL ?>/style/images/base/champions/splash/<?php echo $champ['champ_keyname'] ?>_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
                <div class="container">

                    <article class="entry">
                        <div class="row">

                            <!-- Primary
                            ================================================== -->
                            <div class="primary">
                                <div class="col-md-8">

                                    <div class="entry-media">
                                        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                            <!-- Indicators -->
                                            <ol class="carousel-indicators">
											<?php
											$active_skin = 0;
											while($active_skin < $db->query('SELECT id FROM lol_skins WHERE champname="'.$champ['champ_keyname'].'"')->num_rows)
											{
												if($active_skin == 0)
												{
													echo '<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>';
												}
												else
												{
													echo '<li data-target="#carousel-example-generic" data-slide-to="'.$active_skin.'"></li>';
												}
												$active_skin++;
											}
											?>
                                            </ol>

                                            <!-- Wrapper for slides -->
                                            <div class="carousel-inner" role="listbox">
											<?php
											$are_splasharts_avaliable = true;
											$skin_num = 0;
											while($are_splasharts_avaliable == true)
												{
													if(file_exists($_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/splash/'.$champ['champ_keyname'].'_'.$skin_num.'.jpg'))
														{
															if($skin_num == 0)
																{
																	if($db->query('SELECT has_chroma FROM lol_skins WHERE champname="'.$champ['champ_keyname'].'" AND skin_num='.$skin_num.'')->fetch_row()[0] == 'true')
																	{
																	echo '<div class="item active"><img draggable="false" src="'.URL.'/style/images/base/champions/chroma.png" style="width:30%; top:2%; left:1%; position:absolute; z-index:1;"> 
																	<img draggable="false" src="'.URL.'/style/images/base/champions/splash/'.$champ['champ_keyname'].'_0.jpg"/>
																	</div>';
																	}
																else
																	{
																	echo ' <div class="item active">
																		<img draggable="false" src="'.URL.'/style/images/base/champions/splash/'.$champ['champ_keyname'].'_0.jpg"/>
																		<img draggable="false" src="'.URL.'/style/images/base/shop/rp_icon.png" style="width:4%; bottom:2%; left:1%; position:absolute; z-index:1;"> <div style="width:4%; bottom:3%; left:6%; color:white; position:absolute; z-index:1;">'. $db->query('SELECT rp FROM lol_champs_prize WHERE champ_id='.$champ['champ_id'].'')->fetch_row()[0].'</div>
																		<img draggable="false" src="'.URL.'/style/images/base/shop/ip_icon.png" style="width:4%; bottom:2%; left:10%; position:absolute; z-index:1;"> <div style="width:4%; bottom:3%; left:14%; color:white; position:absolute; z-index:1;">'. $db->query('SELECT ip FROM lol_champs_prize WHERE champ_id='.$champ['champ_id'].'')->fetch_row()[0].'</div>
																		</div>';
																	}
																}
														else
															{
																if($db->query('SELECT has_chroma FROM lol_skins WHERE champname="'.$champ['champ_keyname'].'" AND skin_num='.$skin_num.'')->fetch_row()[0] == 'true')
																	{
																		$skin = $db->query('SELECT price,skin_name FROM lol_skins WHERE champname="'.$champ['champ_keyname'].'" AND skin_num='.$skin_num)->fetch_row();
																		if($skin[0] == '-1')
																		{
																			$prize = 'No disponible';
																		}
																		if($skin[0] == '0')
																		{
																			$prize = '¡Gratis!';
																		}
																		if(empty($prize))
																		{
																			$prize = $skin[0];
																		}
																	echo '<div class="item">
																	
																	<img src="'.URL.'/style/images/base/champions/chroma.png" style="width:30%; right:2%; bottom:2%; position:absolute; z-index:1;"> 
																	<img draggable="false" src="'.URL.'/style/images/base/champions/splash/'.$champ['champ_keyname'].'_'.$skin_num.'.jpg"/>
																	<img draggable="false" src="'.URL.'/style/images/base/shop/rp_icon.png" style="width:4%; bottom:2%; left:1%; position:absolute; z-index:1;"> <div style="width:20%; bottom:3%; left:6%; color:white; position:absolute; z-index:1;">'.$prize.'</div> 
																	
																	<h1 style="width:50%; top:0%; left:3%; color:white; position:absolute; z-index:1;">'.$skin[1].'</h1>
																	</div>';
																	}
																else
																	{
																		$skin = $db->query('SELECT price,skin_name FROM lol_skins WHERE champname="'.$champ['champ_keyname'].'" AND skin_num='.$skin_num)->fetch_row();
																		if($skin[0] == '-1')
																		{
																			$prize = 'No disponible';
																		}
																		if($skin[0] == '0')
																		{
																			$prize = '¡Gratis!';
																		}
																		if(empty($prize))
																		{
																			$prize = $skin[0];
																		}
																	echo ' <div class="item">
																	<img draggable="false" src="'.URL.'/style/images/base/champions/splash/'.$champ['champ_keyname'].'_'.$skin_num.'.jpg"/>
																	<img draggable="false" src="'.URL.'/style/images/base/shop/rp_icon.png" style="width:4%; bottom:2%; left:1%; position:absolute; z-index:1;"> <div style="width:15%; bottom:3%; left:6%; color:white; position:absolute; z-index:1;">'.$prize.'</div>
																	<h1 style="width:50%; top:0%; left:3%; color:white; position:absolute; z-index:1;">'.$skin[1].'</h1>
																	</div>';
																	}
															}
														}
													else
														{
															$are_splasharts_avaliable = false;
														}
												$skin_num++;
												}
											?>
                                            </div>

                                            <!-- Controls -->
                                            <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                                <span class="sr-only"><-</span>
                                            </a>
                                            <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                                <span class="sr-only">-></span>
                                            </a>
                                        </div>
                                    </div>

                                </div><!-- end col-md-8 -->
                            </div><!-- end primary -->


                            <!-- Secondary: Project details
                            ================================================== -->
                            <div class="secondary">
                                <div class="col-md-4"style="background-color: white; padding: 2% 1.5% 2% 1.5%;">

                                    <h2 class="heading-dash"><b><?php echo $champ['champname'];?> <?php if($champ['is_rotation'] == 1) { echo ' <i class="icon_check_alt" data-toggle="tooltip" title="¡En rotación!" data-placement="bottom"></i>'; } ?></b></h2>
                                    <ul class="details">
									<h3><?php echo $champ['title']; ?></h3>
									<?php
									$info_attack = $db->query('SELECT info_attack FROM lol_champs WHERE champ_id="'.$champ['champ_id'].'"')->fetch_row()[0]*10;
									if($info_attack < 20) { $spacer_attack  = ' style="text-align:center;"'; } else { $spacer_attack = ''; }
									echo '<li'.$spacer_attack.'><strong>Ataque:</strong> <div class="progress"> <div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="'.$info_attack.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$info_attack.'%;"><span>'.$info_attack.'%</span></div></div></li>';
									
									$info_defense = $db->query('SELECT info_defense FROM lol_champs WHERE champ_id="'.$champ['champ_id'].'"')->fetch_row()[0]*10;
									if($info_defense < 20) { $spacer_defense  = ' style="text-align:center;"'; } else { $spacer_defense = ''; }
									echo '<li'.$spacer_defense.'><strong>Defensa:</strong> <div class="progress"> <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="'.$info_defense.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$info_defense.'%;"><span>'.$info_defense.'%</span></div></div></li>';
									
									$info_magic = $db->query('SELECT info_magic FROM lol_champs WHERE champ_id="'.$champ['champ_id'].'"')->fetch_row()[0]*10;
									if($info_magic < 20) { $spacer_magic  = ' style="text-align:center;"'; } else { $spacer_magic  = null; }
									echo '<li'.$spacer_magic.'><strong>Magia:</strong> <div class="progress"> <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="'.$info_magic.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$info_magic.'%;"><span>'.$info_magic.'%</span></div></div></li>';
									
									$info_difficult = $db->query('SELECT info_difficulty FROM lol_champs WHERE champ_id="'.$champ['champ_id'].'"')->fetch_row()[0]*10;
									if($info_difficult < 20) { $spacer_difficult  = ' style="text-align:center;"'; } else { $spacer_difficult = ''; }
									echo '<li'.$spacer_difficult.'><strong>Dificultad:</strong> <div class="progress"> <div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="'.$info_difficult.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$info_difficult.'%;"><span>'.$info_difficult.'%</span></div></div></li>';
									?>
                                    </ul>
                                    <a href="<?php echo URL ?>/champions?back_from=<?php echo $champ['champ_id']?>" class="btn btn-info btn-sm" style="float: right;"><span><i class="arrow_back"></i> Volver a la lista de campeones</span></a>
                                </div>
                            </div><!-- end .secondary -->
                        </div><!-- end row -->
                    </article>

					<br>
                    <!-- Portfolio footer
                    ================================================== -->
						<!-- tabs -->
						<div class="pcss3t pcss3t-layout-top-center pcss3t-effect-slide-left pcss3t-effect-slide-top pcss3t-height-auto">
							<input type="radio" name="pcss3t" checked  id="tab1"class="tab-content-first">
							<label for="tab1"><i class="icon-building"></i>Habilidades y atributos</label>
							
							<input type="radio" name="pcss3t" id="tab3" class="tab-content-3">
							<label for="tab3"><i class="icon-inbox"></i>Ítems recomendados</label>
							
							<input type="radio" name="pcss3t" id="tab2" class="tab-content-2">
							<label for="tab2"><i class="icon-book"></i>Maestría</label>
							
							<input type="radio" name="pcss3t" id="tab4" class="tab-content-4">
							<label for="tab4"><i class="icon-globe"></i>Mejores invocadores</label>
							
							<input type="radio" name="pcss3t" id="tab5" class="tab-content-5">
							<label for="tab5"><i class="icon-bolt"></i>Entusiastas</label>
				
							<ul>
								<li class="tab-content tab-content-first">
								<div id="grid_54f5908f6efc8" class="col-sm-6 col-md-6 grid-container grid-x" style="padding: 3% 3% 0% 3%; margin-top:-3%;" data-item-style="tpl-item-style5" data-open-state="modal" data-size-width="64" data-size-height="64" data-item-size="4x3" data-column="4" data-size-gutter="10">
                                <div class="grid-viewport row"></div>
								<?php
								$bartype = $db->query('SELECT kit_bar FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0];
								$costs =  $db->query('SELECT q_cost,w_cost,e_cost,r_cost FROM lol_champs_skills WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row();
								if($costs[0] == '0/0/0/0/0')
								{
									$q_cost = 'Sin coste';
								}
								else
								{
									$q_cost = 'Costes: '.$costs[0].' '.spellbartolang($bartype);
								}
								if($costs[1] == '0/0/0/0/0')
								{
									$w_cost = 'Sin coste';
								}
								else
								{
									$w_cost = 'Costes: '.$costs[1].' '.spellbartolang($bartype);
								}
								if($costs[2] == '0/0/0/0/0')
								{
									$e_cost = 'Sin coste';
								}
								else
								{
									$e_cost = 'Costes: '.$costs[2].' '.spellbartolang($bartype);
								}
								if($costs[3] == '0/0/0/0/0')
								{
									$r_cost = 'Sin coste';
								}
								else
								{
									$r_cost = 'Costes: '.$costs[3].' '.spellbartolang($bartype);
								}
								
								$cds = $db->query('SELECT q_cooldown,w_cooldown,e_cooldown,r_cooldown FROM lol_champs_skills WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row();
								if($cds[0] == '0/0/0/0/0')
								{
									$q_cd = 'Sin enfriamiento';
								}
								else
								{
									$q_cd = 'Enfriamientos: '.$cds[0];
								}
								if($cds[1] == '0/0/0/0/0')
								{
									$w_cd = 'Sin enfriamiento';
								}
								else
								{
									$w_cd = 'Enfriamientos: '.$cds[1];
								}
								if($cds[2] == '0/0/0/0/0')
								{
									$e_cd = 'Sin enfriamiento';
								}
								else
								{
									$e_cd = 'Enfriamientos: '.$cds[2];
								}
								if($cds[3] == '0/0/0/0/0')
								{
									$r_cd = 'Sin enfriamiento';
								}
								else
								{
									$r_cd = 'Enfriamientos: '.$cds[3];
								}
								?>
                                <script type="text/template" class="grid-data">{"posts":[
								{"id":1,"skill":"P","cooldowns":"","cost":"","title":"Pasiva - <?php echo $db->query('SELECT passive_name FROM lol_champs_skills WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] ?>","format":"video","media":"<div class=\"mejs-wrapper video\"><video poster=\"<?php echo URL ?>/style/images/video/error.png\" preload=\"auto\" autoplay loop width=\"100%\">\r\n<source type=\"video\/mp4\" src=\"http://d28xe8vt774jo5.cloudfront.net/abilities/videos/0<?php if($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] > 100) {echo $db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0];} elseif($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] < 10){ echo '00'.$db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]; }elseif($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] < 100){ echo '0'.$db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]; } ?>_01.mp4\" \/><\/video><\/div>","link":"#","post_type":"post","grid_size":"normal cat4","thumb":"<?php echo URL ?>/style/images/base/champions/kit/<?php echo $champ['champ_keyname'] ?>/passive.png","content":"<?php echo addslashes($db->query('SELECT passive_description FROM lol_champs_skills WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]) ?>","_logo":"images\/logo\/2.png","categories":[{"cat_id":4,"title":"Entertainment","color":"#007AFF","slug":"category_4","link":"#"}],"tags":[]},
								{"id":2,"skill":"Q","cooldowns":"<?php echo $q_cd ?>","cost":"<?php echo $q_cost ?>","title":"Q - <?php echo addslashes($db->query('SELECT q_name FROM lol_champs_skills WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]) ?>","format":"video","media":"<div class=\"mejs-wrapper video\"><video preload=\"auto\" poster=\"<?php echo URL ?>/style/images/video/error.png\" autoplay loop width=\"100%\">\r\n<source type=\"video\/mp4\" src=\"http://d28xe8vt774jo5.cloudfront.net/abilities/videos/0<?php if($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] > 100) {echo $db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0];} elseif($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] < 10){ echo '00'.$db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]; }elseif($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] < 100){ echo '0'.$db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]; } ?>_02.mp4\" \/><\/video><\/div>","link":"#","post_type":"post","grid_size":"normal cat4","thumb":"<?php echo URL ?>/style/images/base/champions/kit/<?php echo $champ['champ_keyname'] ?>/q.png","content":"<?php echo addslashes($db->query('SELECT q_description FROM lol_champs_skills WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]) ?>","_logo":"images\/logo\/2.png","categories":[{"cat_id":4,"title":"Entertainment","color":"#007AFF","slug":"category_4","link":"#"}],"tags":[]},
								{"id":3,"skill":"W","cooldowns":"<?php echo $w_cd ?>","cost":"<?php echo $w_cost ?>","title":"W - <?php echo addslashes($db->query('SELECT w_name FROM lol_champs_skills WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]) ?>","format":"video","media":"<div class=\"mejs-wrapper video\"><video preload=\"auto\" poster=\"<?php echo URL ?>/style/images/video/error.png\" autoplay loop width=\"100%\">\r\n<source type=\"video\/mp4\" src=\"http://d28xe8vt774jo5.cloudfront.net/abilities/videos/0<?php if($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] > 100) {echo $db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0];} elseif($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] < 10){ echo '00'.$db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]; }elseif($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] < 100){ echo '0'.$db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]; } ?>_03.mp4\" \/><\/video><\/div>","link":"#","post_type":"post","grid_size":"normal cat4","thumb":"<?php echo URL ?>/style/images/base/champions/kit/<?php echo $champ['champ_keyname'] ?>/w.png","content":"<?php echo addslashes($db->query('SELECT w_description FROM lol_champs_skills WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]) ?>","_logo":"images\/logo\/2.png","categories":[{"cat_id":4,"title":"Entertainment","color":"#007AFF","slug":"category_4","link":"#"}],"tags":[]},
								{"id":4,"skill":"E","cooldowns":"<?php echo $e_cd ?>","cost":"<?php echo $e_cost ?>","title":"E - <?php echo addslashes($db->query('SELECT e_name FROM lol_champs_skills WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]) ?>","format":"video","media":"<div class=\"mejs-wrapper video\"><video preload=\"auto\" poster=\"<?php echo URL ?>/style/images/video/error.png\" autoplay loop width=\"100%\">\r\n<source type=\"video\/mp4\" src=\"http://d28xe8vt774jo5.cloudfront.net/abilities/videos/0<?php if($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] > 100) {echo $db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0];} elseif($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] < 10){ echo '00'.$db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]; }elseif($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] < 100){ echo '0'.$db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]; } ?>_04.mp4\" \/><\/video><\/div>","link":"#","post_type":"post","grid_size":"normal cat4","thumb":"<?php echo URL ?>/style/images/base/champions/kit/<?php echo $champ['champ_keyname'] ?>/e.png","content":"<?php echo addslashes($db->query('SELECT e_description FROM lol_champs_skills WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]) ?>","_logo":"images\/logo\/2.png","categories":[{"cat_id":4,"title":"Entertainment","color":"#007AFF","slug":"category_4","link":"#"}],"tags":[]},
								{"id":5,"skill":"R","cooldowns":"<?php echo $r_cd ?>","cost":"<?php echo $r_cost ?>","title":"R - <?php echo addslashes($db->query('SELECT r_name FROM lol_champs_skills WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]) ?>","format":"video","media":"<div class=\"mejs-wrapper video\"><video preload=\"auto\" poster=\"<?php echo URL ?>/style/images/video/error.png\" autoplay loop width=\"100%\">\r\n<source type=\"video\/mp4\" src=\"http://d28xe8vt774jo5.cloudfront.net/abilities/videos/0<?php if($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] > 100) {echo $db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0];} elseif($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] < 10){ echo '00'.$db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]; }elseif($db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0] < 100){ echo '0'.$db->query('SELECT champ_id FROM lol_champs WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]; } ?>_05.mp4\" \/><\/video><\/div>","link":"#","post_type":"post","grid_size":"normal cat4","thumb":"<?php echo URL ?>/style/images/base/champions/kit/<?php echo $champ['champ_keyname'] ?>/r.png","content":"<?php echo addslashes($db->query('SELECT r_description FROM lol_champs_skills WHERE champ_keyname="'.$champ['champ_keyname'].'"')->fetch_row()[0]) ?>","_logo":"images\/logo\/2.png","categories":[{"cat_id":4,"title":"Entertainment","color":"#007AFF","slug":"category_4","link":"#"}],"tags":[]}
								]}</script>
								 <div class="text-light" style="padding: 2% 2% 1.5% 2%; background-color: rgb(250, 250, 250);color:inherit;">
                                      <h2>Estadísticas de <?php echo $champ['champname'] ?></h2>
										  <div class="table-responsive">          
										  <table class="table">
											<thead>
											  <tr>
												<th>Atributo</th>
												<th>Valor</th>
											  </tr>
											</thead>
											<tbody>
											  <tr>
												<td>Rol principal</td>
												<td><?php echo roletolang($champ['role_1']) ?></td>
											  </tr>
											  <?php if($champ['role_2'] != 'null') { ?>
											  <tr>
												<td>Rol secundario</td>
												<td><?php echo roletolang($champ['role_2']) ?></td>
											  </tr> <?php } ?>
											   <tr>
												<td>Barra de habilidades</td>
												<td><?php echo spellbartolang($champ['kit_bar']) ?></td>
											  </tr>
											
											  <tr>
												<td><?php echo spellbartolang($champ['kit_bar']) ?></td>
												<td><?php echo $champ['base_bar'] ?> (+ <?php echo $champ['scale_bar_lvl'] ?> por nivel)</td>
											  </tr>
											  <tr>
												<td>Velocidad de movimiento</td>
												<td><?php echo $champ['movspeed'] ?></td>
											  </tr>
											  
											  <tr>
												<td>Rango de ataque</td>
												<td><?php echo $champ['attackrange'] ?></td>
											  </tr>
											  
											  <?php if($champ['kit_bar'] == 'mana') { ?>
											  <tr>
												<td>Regeneración de maná por segundo</td>
												<td><?php echo $champ['base_manareg'] ?> (+<?php echo $champ['scale_manareg_lvl'] ?> por nivel)</td>
											  </tr>	  <?php } ?>
											   <tr>
												<td>Daño de ataque</td>
												<td><?php echo $champ['base_ad'] ?> (+<?php echo $champ['scale_ad_lvl'] ?> por nivel)</td>
											  </tr>
											  <tr>
												<td>Probabilidad de crítico</td>
												<td><?php echo $champ['base_crit'] ?> (+<?php echo $champ['scale_crit_lvl'] ?> por nivel)</td>
											  </tr>
											  <tr>
												<td>Velocidad de ataque</td>
												<td><?php echo $champ['offset_as'] ?> (+<?php echo $champ['scale_as_lvl'] ?> por nivel)</td>
											  </tr>
											  
											   <tr>
												<td>Puntos de salud</td>
												<td><?php echo $champ['base_hp'] ?> (+<?php echo $champ['scale_hp_lvl'] ?> por nivel)</td>
											  </tr>
											   <tr>
												<td>Armadura</td>
												<td><?php echo $champ['base_armor'] ?> (+<?php echo $champ['scale_armor_lvl'] ?> por nivel)</td>
											  </tr>
											  <tr>
												<td>Resistencia mágica</td>
												<td><?php echo $champ['base_spellblock'] ?> (+<?php echo $champ['scale_spellblock_lvl'] ?> por nivel)</td>
											  </tr>
											  <tr>
												<td>Regeneración de salud por segundo</td>
												<td><?php echo $champ['base_hpregen'] ?> (+<?php echo $champ['scale_hpregen_lvl'] ?> por nivel)</td>
											  </tr>
											</tbody>
										  </table>
										  </div>
  
                                    </div>
                            </div>
							<div class="row"><div class="col-sm-12 col-md-12 text-light" style="background-color: white; opacity: 0.95; color:black; word-wrap: break-word;margin-top:2%;">
                                            <h3>Historia</h3>
                                            <p><?php echo $champ['lore']?></p>
							</div></div>
                                   
									
								</li>
								
								<li class="tab-content tab-content-3">
									<div class="row">
											 <!-- Nav tabs -->
                                    <ul role="tablist_sub">
                                        <li class="active" style="display:inline;"><button type="button" href="#11" data-toggle="tab" style="background:url(<?php echo URL ?>/style/images/home/bg/rift.jpg);background-repeat: repeat; background-size: 100%; background-attachment: fixed; float:left;" class="btn btn-primary rounded btn-lg">Grieta del invocador</button></li>
                                        <li style="display:inline;"><button type="button" href="#10" data-toggle="tab" style="background:url(<?php echo URL ?>/style/images/home/bg/tree.jpg);background-repeat: repeat; background-size: 100%; background-attachment: fixed; margin-left:2.5%;" class="btn btn-primary rounded btn-lg">Bosque retorcido</button></li>
                                        <li style="display:inline;"><button type="button" href="#8" data-toggle="tab" style="background:url(<?php echo URL ?>/style/images/home/bg/scar.jpg);background-repeat: repeat; background-size: 100%; background-attachment: fixed; margin-left:2.5%;" class="btn btn-primary rounded btn-lg">Cicatriz de cristal</button></li>
                                        <li style="display:inline;"><button type="button" href="#12" data-toggle="tab" style="background:url(<?php echo URL ?>/style/images/home/bg/abysm.jpg);background-repeat: repeat; background-size: 100%; background-attachment: fixed; float:right; margin-right:2%;" class="btn btn-primary rounded btn-lg">Abismo de los lamentos</button></li>
                                    </ul></div>
											

                                   
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <?php
													$build = json_decode($db->query('SELECT build_items FROM lol_champs_builds WHERE champ_id='.$champ['id'])->fetch_row()['0']);
													foreach($build as $map => $item_data)
													{
														if($map == 11)
														{
															$is_active = 'active';
														}
														else
														{
															$is_active = null;
														}
															echo '<div role="tabpanel" class="tab-pane fade in '.$is_active.'" id="'.$map.'">';
															$data = stdtoarray($item_data);
															/* Starting items */
															echo '<div class="col-md-4"><div class="service-box style-bordered">
															<div class="service-icon"><span class="icon_building_alt"></span></div>
															<h3>Iniciales</h3>
															<p>';
															$starting_count = 0;
															while($starting_count < count($data['starting']))
															{
																echo '<img draggable="false" src="'.URL.'/style/images/base/game/items/'.array_keys($data['starting'])[$starting_count].'.png">';
																$starting_count++;
															}
															echo '</p></div></div>';
															
															
															/* Core items */
															echo '<div class="col-md-4"><div class="service-box style-bordered">
															<div class="service-icon"><span class="icon_balance"></span></div>
															<h3>Esenciales</h3>
															<p>';
															$coreitems_count = 0;
															while($coreitems_count < count($data['essential']))
															{
																echo '<img draggable="false" src="'.URL.'/style/images/base/game/items/'.array_keys($data['essential'])[$coreitems_count].'.png">';
																$coreitems_count++;
															}
															echo '</p></div></div>';
															
															/* Cosumable items */
															echo '<div class="col-md-4"><div class="service-box style-bordered">
															<div class="service-icon"><span class="icon_bag"></span></div>
															<h3>Consumibles</h3>
															<p>';
															$consumables_count = 0;
															while($consumables_count < count($data['consumables']))
															{
																echo '<img draggable="false" src="'.URL.'/style/images/base/game/items/'.array_keys($data['consumables'])[$consumables_count].'.png">';
																$consumables_count++;
															}
															echo '</p></div></div>';
															
															/* Offensive items */
															echo '<div class="col-md-6"><div class="service-box style-bordered">
															<div class="service-icon"><span class="icon_upload"></span></div>
															<h3>Ofensivos</h3>
															<p>';
															$attack_count = 0;
															while($attack_count < count($data['offensive']))
															{
																echo '<img draggable="false" src="'.URL.'/style/images/base/game/items/'.array_keys($data['offensive'])[$attack_count].'.png">';
																$attack_count++;
															}
															echo '</p></div></div>';
															
															/* Defensive items */
															echo '<div class="col-md-6"><div class="service-box style-bordered">
															<div class="service-icon"><span class="icon_download"></span></div>
															<h3>Defensivos</h3>
															<p>';
															$defense_count = 0;
															while($defense_count < count($data['defensive']))
															{
																echo '<img draggable="false" src="'.URL.'/style/images/base/game/items/'.array_keys($data['defensive'])[$defense_count].'.png">';
																$defense_count++;
															}
															echo '</p></div></div></div>';
													}
													?>
                                       
                                    </div>		
								</li>
								
								<li class="tab-content tab-content-4">
									<div class="col-sm-12 col-md-12 text-light padding1x" data-background="#white" style="background-color: white; opacity: 0.95; color:black; word-wrap: break-word;">
                                        <aside class="widget widget_recent_news">
											<table id="bestsummoners" class="display" cellspacing="0" width="100%">
												<thead >
													<tr>
														<th><button type="button" class="btn btn-sm btn-default">Rango</button></th>
														<th></th>
														<th><button type="button" class="btn btn-sm btn-default">Invocador</button></th>
														<th><button type="button" class="btn btn-sm btn-default">KDA</button></th>
														<th><button type="button" class="btn btn-sm btn-default">Partidas</button></th>
														<th><button type="button" class="btn btn-sm btn-default">% Victoria</button></th>
														<th><button type="button" class="btn btn-sm btn-default">Servidor</button></th>
														<th><button type="button" class="btn btn-sm btn-default">Puntuación</button></th>
													</tr>
												</thead>
												<tbody>
													<?php
																	/* Best summoners of the world */
																	$all_sums_count = 1;
																	$all_sums_max = config('max_soloq_bestsums');
																	$get_best_summs_all = $db->query('SELECT rank,icon,name,kda,matches,winrate,region,score FROM inv_users_bestchampplayers WHERE champ_id='.$champ['champ_id'].' ORDER BY rank DESC LIMIT 1000');
																	
																	while($all_sums_count <= $all_sums_max && $row = $get_best_summs_all->fetch_array(MYSQLI_ASSOC))
																	{
																		if($row['winrate'] > 50)
																		{$all_winrate_color = 'green';} else {$all_winrate_color = 'red';}
																		echo '<tr>
																		<td>'.$row['rank'].'</td>
																		<td><img draggable="false" width="40%" src="'.URL.'/style/images/base/summoners/icon/'.$row['icon'].'.png" alt="Thumb"></td>
																		<td>'.$row['name'].'</td>
																		<td>'.$row['kda'].'</td>
																		<td><font color="red">'.$row['matches'].'</font></td>
																		<td><font color="'.$all_winrate_color.'">'.$row['winrate'].' %</font></td>
																		<td>'.strtoupper($row['region']).'</td>
																		<td>'.$row['score'].'</td>
																		</tr>';
																		$all_sums_count++;
																	}
																	?>
												</tbody>
											</table>
										</aside>
									</div>
								</li>
								<li class="tab-content tab-content-5">
									<div class="col-sm-12 col-md-12 text-light padding1x" data-background="#white" style="background-color: white; opacity: 0.95; color:black; word-wrap: break-word;">
                                        <aside class="widget widget_recent_news">
										   <table id="moreplayssummoners" class="display" cellspacing="0" width="100%">
												<thead >
													<tr>
														<th><button type="button" class="btn btn-sm btn-default">Rango</button></th>
														<th></th>
														<th><button type="button" class="btn btn-sm btn-default">Invocador</button></th>
														<th><button type="button" class="btn btn-sm btn-default">KDA</button></th>
														<th><button type="button" class="btn btn-sm btn-default">Partidas</button></th>
														<th><button type="button" class="btn btn-sm btn-default">% Victoria</button></th>
														<th><button type="button" class="btn btn-sm btn-default">Servidor</button></th>
														<th><button type="button" class="btn btn-sm btn-default">Puntuación</button></th>
													</tr>
												</thead>
												<tbody>
													<?php
																	/* Best summoners of the world */
																	$all_sums_count = 1;
																	$all_sums_max = config('max_soloq_bestsums');
																	$get_best_summs_all = $db->query('SELECT rank,icon,name,kda,matches,winrate,region,score FROM inv_users_morechampplayers WHERE champ_id='.$champ['champ_id'].' ORDER BY rank DESC LIMIT 1000');
																	
																	while($all_sums_count <= $all_sums_max && $row = $get_best_summs_all->fetch_array(MYSQLI_ASSOC))
																	{
																		if($row['winrate'] > 50)
																		{$all_winrate_color = 'green';} else {$all_winrate_color = 'red';}
																		echo '<tr>
																		<td>'.$row['rank'].'</td>
																		<td><img draggable="false" width="40%" src="'.URL.'/style/images/base/summoners/icon/'.$row['icon'].'.png" alt="Thumb"></td>
																		<td>'.$row['name'].'</td>
																		<td>'.$row['kda'].'</td>
																		<td><font color="red">'.$row['matches'].'</font></td>
																		<td><font color="'.$all_winrate_color.'">'.$row['winrate'].' %</font></td>
																		<td>'.strtoupper($row['region']).'</td>
																		<td>'.$row['score'].'</td>
																		</tr>';
																		$all_sums_count++;
																	}
																	?>
												</tbody>
											</table>
										</aside>
									</div>
								</li>
								
								<li class="tab-content tab-content-2">
									<div class="col-sm-12 col-md-12 text-light padding1x" data-background="#white" style="background-color: white; opacity: 0.95; color:black; word-wrap: break-word;">
                                        <aside class="widget widget_recent_news">
										   <table id="masterysummoners" class="display" cellspacing="0" width="100%">
												<thead >
													<tr>
														<th><button type="button" class="btn btn-sm btn-default">Rango</button></th>
														<th></th>
														<th><button type="button" class="btn btn-sm btn-default">Invocador</button></th>
														<th><button type="button" class="btn btn-sm btn-default">Región</button></th>
														<th><button type="button" class="btn btn-sm btn-default">Puntuación</button></th>
													</tr>
												</thead>
												<tbody>
													<?php
																	$all_sums_count = 1;
																	$all_sums_max = config('max_soloq_bestsums');
																	$get_best_summs_all = $db->query('SELECT rank,icon,region,score,name FROM inv_users_moremasterychamplayers WHERE champ_id='.$champ['champ_id'].' ORDER BY rank DESC LIMIT 1000');
																	
																	while($all_sums_count <= $all_sums_max && $row = $get_best_summs_all->fetch_array(MYSQLI_ASSOC))
																	{
																		echo '<tr>
																		<td>'.$row['rank'].'</td>
																		<td><img draggable="false" width="50px" src="'.URL.'/style/images/base/summoners/icon/'.$row['icon'].'.png" alt="Thumb"></td>
																		<td>'.$row['name'].'</td>
																		<td>'.$row['region'].'</td>
																		<td>'.number_format($row['score'], 0, ',',',').'</td>
																		</tr>';
																		$all_sums_count++;
																	}
																	?>
												</tbody>
											</table>
										</aside>
									</div>
								</li>
								
								
							</ul>
						</div>
						<!--/ tabs -->
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
		<script>
		$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
		});
		</script>
    </body>
</html>