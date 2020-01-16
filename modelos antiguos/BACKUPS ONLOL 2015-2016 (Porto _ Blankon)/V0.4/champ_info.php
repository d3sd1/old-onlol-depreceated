<?php
require('core/core.php');
if(empty($_GET['name']))
{
	header('Location: '.URL.'/champions?invalid_champion=true');
}

if($db->query('SELECT id FROM lol_champs WHERE champname="'.$_GET['name'].'"')->num_rows == 0)
{
	header('Location: '.URL.'/champions?invalid_champion=true');
}

$champ = $db->query('SELECT champ_id,is_rotation,id,champname,es_lore,es_title,role_1,role_2,spotlight,kit_bar,base_hp,scale_hp_lvl,base_bar,scale_bar_lvl,movspeed,base_armor,scale_armor_lvl,base_spellblock,scale_spellblock_lvl,attackrange,base_hpregen,scale_hpregen_lvl,base_manareg,scale_manareg_lvl,base_crit,scale_crit_lvl,base_ad,scale_ad_lvl,offset_as,scale_as_lvl FROM lol_champs WHERE champname="'.$_GET['name'].'"')->fetch_array(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ONLoL ~ <?php echo $champ['champname'].', '.$champ['es_title']; ?></title>
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
                        <a class="navbar-brand" href="<?php echo URL ?>">OnLoL</a>
                    </div>

                    <!-- Main navigation
================================================== -->
                    <?php echo nav('champs'); ?>                
                </div>
            </header>


            <!-- Content
            ================================================== -->
            <section class="content single-portfolio padding2x" style="background-image: url(<?php echo URL ?>/style/images/base/champions/splash/<?php echo $champ['champname'] ?>_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
                <div class="container">

                    <article class="entry">
                        <div class="row">

                            <!-- Primary
                            ================================================== -->
                            <div class="primary">
                                <div class="col-md-8">

                                    <div class="entry-media">
                                        <!-- <a href="<?php echo URL ?>/style/images/demo-images/6.png"><img class="img-responsive" src="<?php echo URL ?>/style/images/demo-images/2.png" alt="Post Image"></a> -->
                                        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                            <!-- Indicators -->
                                            <ol class="carousel-indicators">
											<?php
											$active_skin = 0;
											while($active_skin < $db->query('SELECT id FROM lol_skins WHERE champname="'.$champ['champname'].'"')->num_rows)
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
													if(file_exists($_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/splash/'.$champ['champname'].'_'.$skin_num.'.jpg'))
														{
															if($skin_num == 0)
																{
																	if($db->query('SELECT has_chroma FROM lol_skins WHERE champname="'.$champ['champname'].'" AND skin_num='.$skin_num.'')->fetch_row()[0] == 'true')
																	{
																	echo '<div class="item active"><img src="'.URL.'/style/images/base/champions/chroma.png" style="width:30%; top:2%; left:1%; position:absolute; z-index:1;"> 
																	<img src="'.URL.'/style/images/base/champions/splash/'.$champ['champname'].'_0.jpg" alt="Image"/>
																	</div>';
																	}
																else
																	{
																	echo ' <div class="item active">
																		<img src="'.URL.'/style/images/base/champions/splash/'.$champ['champname'].'_0.jpg"/>
																		</div>';
																	}
																}
														else
															{
																if($db->query('SELECT has_chroma FROM lol_skins WHERE champname="'.$champ['champname'].'" AND skin_num='.$skin_num.'')->fetch_row()[0] == 'true')
																	{
																	echo '<div class="item"><img src="'.URL.'/style/images/base/champions/chroma.png" style="width:30%; position:absolute; z-index:1;"> 
																	<img src="'.URL.'/style/images/base/champions/splash/'.$champ['champname'].'_'.$skin_num.'.jpg" alt="Image"/>
																	</div>';
																	}
																else
																	{
																	echo ' <div class="item">
																	<img src="'.URL.'/style/images/base/champions/splash/'.$champ['champname'].'_'.$skin_num.'.jpg" alt="Image"/>
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
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </div>
                                    </div>

                                </div><!-- end col-md-8 -->
                            </div><!-- end primary -->


                            <!-- Secondary: Project details
                            ================================================== -->
                            <div class="secondary">
                                <div class="col-md-4"style="background-color: white; padding: 2% 1.5% 2% 1.5%;">

                                    <h2 class="heading-dash"><b><?php echo $champ['champname'];?></b></h2>
                                    <ul class="details">
									<h3><?php echo $champ['es_title']; ?></h3>
									<?php
									$info_attack = $db->query('SELECT info_attack FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]*10;
									if($info_attack < 20) { $spacer_attack  = ' style="text-align:center;"'; } else { $spacer_attack = ''; }
									echo '<li'.$spacer_attack.'><strong>Ataque:</strong> <div class="progress"> <div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="'.$info_attack.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$info_attack.'%;"><span>'.$info_attack.'%</span></div></div></li>';
									
									$info_defense = $db->query('SELECT info_defense FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]*10;
									if($info_defense < 20) { $spacer_defense  = ' style="text-align:center;"'; } else { $spacer_defense = ''; }
									echo '<li'.$spacer_defense.'><strong>Defensa:</strong> <div class="progress"> <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="'.$info_defense.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$info_defense.'%;"><span>'.$info_defense.'%</span></div></div></li>';
									
									$info_magic = $db->query('SELECT info_magic FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]*10;
									if($info_magic < 20) { $spacer_magic  = ' style="text-align:center;"'; } else { $spacer_magic  = null; }
									echo '<li'.$spacer_magic.'><strong>Magia:</strong> <div class="progress"> <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="'.$info_magic.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$info_magic.'%;"><span>'.$info_magic.'%</span></div></div></li>';
									
									$info_difficult = $db->query('SELECT info_difficulty FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]*10;
									if($info_difficult < 20) { $spacer_difficult  = ' style="text-align:center;"'; } else { $spacer_difficult = ''; }
									echo '<li'.$spacer_difficult.'><strong>Dificultad:</strong> <div class="progress"> <div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="'.$info_difficult.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$info_difficult.'%;"><span>'.$info_difficult.'%</span></div></div></li>';
									?>
                                    </ul>
                                    <a href="<?php echo URL ?>/champions?back_from=<?php echo $champ['champname']?>" class="btn btn-info btn-sm" style="float: right;"><span><i class="arrow_back"></i> Volver a la lista de campeones</span></a>
                                </div>
                            </div><!-- end .secondary -->
                        </div><!-- end row -->
                    </article>

					<br>
                    <!-- Portfolio footer
                    ================================================== -->
                    <div class="row">
					<div class="col-sm-6 col-md-12" style="background-color: white; opacity: 0.95;">
						<ul class="nav nav-tabs" role="tablist">
                                        <li class="active"><a href="#id0" data-toggle="tab" aria-expanded="true">Habilidades y atributos</a></li>
                                        <li class=""><a href="#id1" data-toggle="tab" aria-expanded="false">Lore</a></li>
                                        <li class=""><a href="#id2" data-toggle="tab" aria-expanded="false">Objetos recomendados</a></li>
                                        <li><a href="#id3" data-toggle="tab">Mejores invocadores</a></li>
                                    </ul>
					</div>
									<div class="tab-content">
                                        <div role="tabpanel" class="tab-pane fade active in" id="id0">
										<div class="col-sm-6 col-md-12 text-light padding1x" data-background="#white" style="background-color: white; opacity: 0.95; color:black;">
                            <!--
            container-class :
                .grid-x:
                    data-item-style="" //tpl-item-style[1-8]
                    data-size-width="150"
                    data-size-height="150"
                    data-open-state=""  // modal | push-state | blank
                    data-size-gutter=""
                .grid-style:
                    data-item-style="" //tpl-item-style[1-8]
                    data-item-size="4x3" // 1x1 | 16x9 | 4x3 | 3x4
                    data-column="5"
                    data-open-state=""  // modal | open-gi | blank
                    data-size-gutter=""
                .grid-masonry:
                    data-item-style="" //tpl-masonry-style[1-2]
                    data-column="5"
                    data-open-state="" // modal | blank
                    data-size-gutter=""
            

            -example html-
            
                            -->
                            <div id="grid_54f5908f6efc8" class="col-sm-6 col-md-6 grid-container grid-x" style="padding: 3% 3% 0% 3%;" data-item-style="tpl-item-style5" data-open-state="modal" data-size-width="64" data-size-height="64" data-item-size="4x3" data-column="4" data-size-gutter="10">
                                <div class="grid-viewport row"></div>
								
                                <script type="text/template" class="grid-data">{"posts":[
								{"id":1,"skill":"P","cooldowns":"","title":"Pasiva - <?php echo $db->query('SELECT passive_name FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0] ?>","format":"video","media":"<div class=\"mejs-wrapper video\"><video autoplay loop width=\"100%\">\r\n<source type=\"video\/mp4\" src=\"http://d28xe8vt774jo5.cloudfront.net/abilities/videos/0<?php if($db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0] > 100) {echo $db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0];} else { echo '0'.$db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]; } ?>_01.mp4\" \/><\/video><\/div>","link":"#","post_type":"post","grid_size":"normal cat4","thumb":"<?php echo URL ?>/style/images/base/champions/kit/<?php echo $champ['champname'] ?>/passive.png","content":"<?php echo addslashes($db->query('SELECT passive_description FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]) ?>","_logo":"images\/logo\/2.png","categories":[{"cat_id":4,"title":"Entertainment","color":"#007AFF","slug":"category_4","link":"#"}],"tags":[]},
								{"id":2,"skill":"Q","cooldowns":"Enfriamientos: <?php echo $db->query('SELECT q_cooldown FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0] ?>","title":"Q - <?php echo addslashes($db->query('SELECT q_name FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]) ?>","format":"video","media":"<div class=\"mejs-wrapper video\"><video autoplay loop width=\"100%\">\r\n<source type=\"video\/mp4\" src=\"http://d28xe8vt774jo5.cloudfront.net/abilities/videos/0<?php if($db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0] > 100) {echo $db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0];} else { echo '0'.$db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]; } ?>_02.mp4\" \/><\/video><\/div>","link":"#","post_type":"post","grid_size":"normal cat4","thumb":"<?php echo URL ?>/style/images/base/champions/kit/<?php echo $champ['champname'] ?>/q.png","content":"<?php echo addslashes($db->query('SELECT q_description FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]) ?>","_logo":"images\/logo\/2.png","categories":[{"cat_id":4,"title":"Entertainment","color":"#007AFF","slug":"category_4","link":"#"}],"tags":[]},
								{"id":3,"skill":"W","cooldowns":"Enfriamientos: <?php echo $db->query('SELECT w_cooldown FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0] ?>","title":"W - <?php echo addslashes($db->query('SELECT w_name FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]) ?>","format":"video","media":"<div class=\"mejs-wrapper video\"><video autoplay loop width=\"100%\">\r\n<source type=\"video\/mp4\" src=\"http://d28xe8vt774jo5.cloudfront.net/abilities/videos/0<?php if($db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0] > 100) {echo $db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0];} else { echo '0'.$db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]; } ?>_03.mp4\" \/><\/video><\/div>","link":"#","post_type":"post","grid_size":"normal cat4","thumb":"<?php echo URL ?>/style/images/base/champions/kit/<?php echo $champ['champname'] ?>/w.png","content":"<?php echo addslashes($db->query('SELECT w_description FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]) ?>","_logo":"images\/logo\/2.png","categories":[{"cat_id":4,"title":"Entertainment","color":"#007AFF","slug":"category_4","link":"#"}],"tags":[]},
								{"id":4,"skill":"E","cooldowns":"Enfriamientos: <?php echo $db->query('SELECT e_cooldown FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0] ?>","title":"E - <?php echo addslashes($db->query('SELECT e_name FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]) ?>","format":"video","media":"<div class=\"mejs-wrapper video\"><video autoplay loop width=\"100%\">\r\n<source type=\"video\/mp4\" src=\"http://d28xe8vt774jo5.cloudfront.net/abilities/videos/0<?php if($db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0] > 100) {echo $db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0];} else { echo '0'.$db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]; } ?>_04.mp4\" \/><\/video><\/div>","link":"#","post_type":"post","grid_size":"normal cat4","thumb":"<?php echo URL ?>/style/images/base/champions/kit/<?php echo $champ['champname'] ?>/e.png","content":"<?php echo addslashes($db->query('SELECT e_description FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]) ?>","_logo":"images\/logo\/2.png","categories":[{"cat_id":4,"title":"Entertainment","color":"#007AFF","slug":"category_4","link":"#"}],"tags":[]},
								{"id":5,"skill":"R","cooldowns":"Enfriamientos: <?php echo $db->query('SELECT r_cooldown FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0] ?>","title":"R - <?php echo addslashes($db->query('SELECT r_name FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]) ?>","format":"video","media":"<div class=\"mejs-wrapper video\"><video autoplay loop width=\"100%\">\r\n<source type=\"video\/mp4\" src=\"http://d28xe8vt774jo5.cloudfront.net/abilities/videos/0<?php if($db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0] > 100) {echo $db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0];} else { echo '0'.$db->query('SELECT champ_id FROM lol_champs WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]; } ?>_05.mp4\" \/><\/video><\/div>","link":"#","post_type":"post","grid_size":"normal cat4","thumb":"<?php echo URL ?>/style/images/base/champions/kit/<?php echo $champ['champname'] ?>/r.png","content":"<?php echo addslashes($db->query('SELECT r_description FROM lol_champs_skills WHERE champname="'.$champ['champname'].'"')->fetch_row()[0]) ?>","_logo":"images\/logo\/2.png","categories":[{"cat_id":4,"title":"Entertainment","color":"#007AFF","slug":"category_4","link":"#"}],"tags":[]}
								]}</script>
                            </div>
<div class="col-sm-6 col-md-6 text-light" style="padding: 2% 2% -'.1% 2%;">
  <div class="table-responsive">      
<h2 style="color:black;">Tienda</h2>  
  <table class="table">
    <thead>
      <tr>
        <th style="color:black;">Precio</th>
        <th style="color:black;"><img draggable="false" src="<?php echo URL ?>/style/images/base/shop/rp_icon.png"> <?php echo $db->query('SELECT rp FROM lol_champs_prize WHERE champ_id='.$champ['champ_id'].'')->fetch_row()[0] ?> / <img draggable="false" src="<?php echo URL ?>/style/images/base/shop/ip_icon.png"> <?php echo $db->query('SELECT ip FROM lol_champs_prize WHERE champ_id='.$champ['champ_id'].'')->fetch_row()[0] ?></th>
      </tr>
    </thead>
    <tbody>
       <tr>
        <th style="color:black;">Estado</th>
        <th style="color:black;"><?php if($champ['is_rotation'] == 1) { echo ' <i class="icon_check_alt"></i> En rotación'; } else { echo ' <i class="icon_close_alt"></i> No está en rotación'; } ?></th>
      </tr>
	  
    </tbody>
  </table>
  </div>
                                    </div>
                                    <div class="col-sm-6 col-md-4 text-light" data-background="#3498db" style="padding: 2% 2% 1.8% 2%; background-color: rgb(52, 152, 219);">
                                      <h2>Base</h2>
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
        <td><?php echo spellbartolang($champ['kit_bar']) ?> base</td>
        <td><?php echo $champ['base_bar'] ?></td>
      </tr>
	  <tr>
        <td><?php echo spellbartolang($champ['kit_bar']) ?> por nivel</td>
        <td>+<?php echo $champ['scale_bar_lvl'] ?></td>
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
        <td>Regeneración de mana/segundo base</td>
        <td><?php echo $champ['base_manareg'] ?></td>
      </tr>
	 <tr>
        <td>Regeneración de mana/segundo por nivel</td>
        <td>+<?php echo $champ['scale_manareg_lvl'] ?></td>
      </tr>	  <?php } ?>
	  
	  
    </tbody>
  </table>
  </div>
  
                                    </div>
									
									<div class="col-sm-6 col-md-4 text-light" data-background="#B43104" style="padding: 2% 2% 6.5% 2%; background-color: rgb(52, 152, 219);">
  <div class="table-responsive">      
<h2>Ataque</h2>  
  <table class="table">
    <thead>
      <tr>
        <th>Atributo</th>
        <th>Valor</th>
      </tr>
    </thead>
    <tbody>
       <tr>
        <td>Daño de ataque base</td>
        <td><?php echo $champ['base_ad'] ?></td>
      </tr>
	  <tr>
        <td>Daño de ataque por nivel</td>
        <td>+<?php echo $champ['scale_ad_lvl'] ?></td>
      </tr>
	  <tr>
        <td>Probabilidad de crítico base</td>
        <td><?php echo $champ['base_crit'] ?></td>
      </tr>
	  <tr>
        <td>Probabilidad de crítico por nivel</td>
        <td>+<?php echo $champ['scale_crit_lvl'] ?></td>
      </tr>
	  <tr>
        <td>Velocidad de ataque base</td>
        <td><?php echo $champ['offset_as'] ?></td>
      </tr>
	  <tr>
        <td>Velocidad de ataque por nivel</td>
        <td><?php echo $champ['scale_as_lvl'] ?></td>
      </tr>
	 
    </tbody>
  </table>
  </div>
                                    </div>
									
									<div class="col-sm-6 col-md-4 text-light" data-background="#04B404" style="padding: 2% 2% 0% 2%; background-color: rgb(52, 152, 219);">
  <div class="table-responsive">      
<h2>Defensa</h2>  
  <table class="table">
    <thead>
      <tr>
        <th>Atributo</th>
        <th>Valor</th>
      </tr>
    </thead>
    <tbody>
       <tr>
        <td>Vida base</td>
        <td><?php echo $champ['base_hp'] ?></td>
      </tr>
	  <tr>
        <td>Vida por nivel</td>
        <td>+<?php echo $champ['scale_hp_lvl'] ?></td>
      </tr>
	   <tr>
        <td>Armadura base</td>
        <td><?php echo $champ['base_armor'] ?></td>
      </tr>
	   <tr>
        <td>Armadura por nivel</td>
        <td>+<?php echo $champ['scale_armor_lvl'] ?></td>
      </tr>
	  <tr>
        <td>Resistencia mágica base</td>
        <td><?php echo $champ['base_spellblock'] ?></td>
      </tr>
	  <tr>
        <td>Resistencia mágica por nivel</td>
        <td>+<?php echo $champ['scale_spellblock_lvl'] ?></td>
      </tr>
	  <tr>
        <td>Regeneración de vida/segundo base</td>
        <td><?php echo $champ['base_hpregen'] ?></td>
      </tr>
	  <tr>
        <td>Regeneración de vida/segundo por nivel</td>
        <td>+<?php echo $champ['scale_hpregen_lvl'] ?></td>
      </tr>
    </tbody>
  </table>
  </div>
                                    </div>
									
                                    </div>
									</div>
                                        
										<div role="tabpanel" class="tab-pane fade" id="id1">
										
										<div class="col-sm-12 col-md-12 text-light padding1x" data-background="#white" style="background-color: white; opacity: 0.95; color:black; word-wrap: break-word;">
                                        <div class="service-box large">
                                            <div class="service-icon"><span class="icon_book"></span></div>
                                            <h3>Historia</h3>
                                            <p><?php echo $champ['es_lore']?></p>
                                        </div>
										</div>
									</div>
									
										<div role="tabpanel" class="tab-pane fade" id="id2">Assertively underwhelm impactful channels with user friendly models. Quickly architect pandemic processes through just in time manufactured products. Monotonectally evolve web-enabled potentialities through.</div>
                                        <div role="tabpanel" class="tab-pane fade" id="id3">Impactful channels with user friendly models. Quickly architect pandemic processes through just in time manufactured products. Monotonectally evolve web-enabled potentialities through.</div>
                                  </div>
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