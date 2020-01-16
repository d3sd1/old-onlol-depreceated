<?php
require('core/core.php');
if(empty($_GET['name']))
{
	redirect(URL.'/?invalid_data=summoner_lookup');
}
else
{
	$summoner = str_replace(' ', '', strtolower($_GET['name']));
}

/* Parse server */
$server_lookup = parseserver($_GET['server']);
/* Parse inv */
if($db->query('SELECT id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="'.$server_lookup.'" LIMIT 1')->num_rows == 0) // Look on database
{
	$summoner_info_url = "https://".$server_lookup.".api.pvp.net/api/lol/".$server_lookup."/v1.4/summoner/by-name/".$summoner."?api_key=".LOL_API_KEY."";
	
	if(url_exists($summoner_info_url)) //SUMMONER EXISTS
	{
       summonerupdate($_GET['name'],$server_lookup, URL.$_SERVER['REQUEST_URI'].'&&reloaded=true');
	}
	else
	{
		redirect(URL.'/search/'.$server_lookup.'/'.$_GET['name']);
	}
	
}

$summonerid = $db->query('SELECT summoner_id FROM inv_users WHERE name="'.$_GET['name'].'" AND region="'.$server_lookup.'" LIMIT 1')->fetch_row()[0];
$renewtimer = summonerinfo($summonerid,'onlol_last_update') + config('profile_autorenew');

if($renewtimer < time())
{
	summonerupdate($_GET['name'],$server_lookup);
}
$profile_region = strtoupper(summonerinfo($summonerid,'region'));
$profile_name = summonerinfo($summonerid,'name');
$profile_level = summonerinfo($summonerid,'level');
$profile_icon = summonerinfo($summonerid,'icon');
$profile_league = summonerinfo($summonerid,'ranked_league');
$profile_division = summonerinfo($summonerid,'ranked_division');
$profile_leaguepoints = summonerinfo($summonerid,'ranked_lp');
$profile_wins = summonerinfo($summonerid,'ranked_wins');
$profile_losses = summonerinfo($summonerid,'ranked_losses');
$profile_mmr = summonerinfo($summonerid,'mmr');
$profile_mainchamp = summonerinfo($summonerid,'main_champ');

$profile_losses3x3 = summonerinfoteams($summonerid,'losses', '3x3');
$profile_losses5x5 = summonerinfoteams($summonerid,'losses', '5x5');
$profile_wins3x3 = summonerinfoteams($summonerid,'wins', '3x3');
$profile_wins5x5 = summonerinfoteams($summonerid,'wins', '5x5');
$profile_leaguepoints3x3 = summonerinfoteams($summonerid,'lp', '3x3');
$profile_leaguepoints5x5 = summonerinfoteams($summonerid,'lp', '5x5');
$profile_division3x3 = summonerinfoteams($summonerid,'division', '3x3');
$profile_division5x5 = summonerinfoteams($summonerid,'division', '5x5');
$profile_league3x3 = summonerinfoteams($summonerid,'league', '3x3');
$profile_league5x5 = summonerinfoteams($summonerid,'league', '5x5');
$profile_renewed = time_elapsed_string(summonerinfo($summonerid,'onlol_last_update'));
$profile_runes_data = stdtoarray(json_decode(summonerinfo($summonerid,'runes')));
$profile_masteries_data = stdtoarray(json_decode(summonerinfo($summonerid,'masteries')));
?>
<!DOCTYPE html>
<html lang="en">
    <head>
         <title>ONLoL ~ Perfil de <?php echo $profile_name ?></title>
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
		<!-- Tabs -->
		<link rel="stylesheet" href="<?php echo URL ?>/style/css/tabs.css">
		<!-- Players table -->
        <script type="text/javascript" language="javascript" src="<?php echo URL ?>/style/js/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" language="javascript" src="<?php echo URL ?>/style/js/jquery.dataTables.js"></script>
        <link rel="stylesheet" href="<?php echo URL ?>/style/css/jquery.dataTables.min.css">
		
		<script src="<?php echo URL ?>/style/js/highcharts/highcharts.js"></script>
		<script src="<?php echo URL ?>/style/js/highcharts/modules/funnel.js"></script>
		<script src="<?php echo URL ?>/style/js/highcharts/modules/exporting.js"></script>	
		
		<script type="text/javascript" language="javascript">$(document).ready(function() {
		$('#lastgamestable').DataTable( {
			"order": [[ 7, "desc" ]],
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
		
		$('#ranked2015data').DataTable( {
			"order": [[ 2, "desc" ]],
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
		} );
	</script>
		<?php echo rclickmenu() ?>
		 
        <?php
		if(!empty($_GET['game']))
		{
			if($_GET['game'] == 'not_ingame')
			{
				echo '<script>swal(   "Partida no encontrada",   "El invocador seleccionado no se encuentra en partida.",   "error" )</script>';
			}
		}
		if(!empty($_GET['reloaded']))
		{
			echo '<script>swal(   "Invocador actualizado",   "'.$profile_name.' se actualizó correctamente.",   "info" )</script>';
		}
		?>

        <script type="text/template" id="tpl-item-style1" id="rclick">
            <div class="grid-item tpl-item-style10 <%= typeof(grid_size)!== 'undefined' ?  grid_size : '' %>" data-id="<%= id %>" style="<% if (typeof(categories) != "undefined") { %>background-color:<%= _.last(categories).color %>;<% } %>">
            <div class="grid-item-entry">
            <div class="front" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"><a href="#" class="post-permalink"></a></div>
            <div class="back" style="background-image:url(<%= typeof(thumb)!== 'undefined' ?  thumb : '' %>);"><a href="#" class="post-permalink"></a></div>
            </div>
            </div>
        </script>
       
        <script type="text/template" id="tpl-single-open-state" >
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
</head>

<body id="rclick">
    

		<section class="content" style="background-image: url(<?php echo URL ?>/style/images/base/champions/splash/<?php echo $profile_mainchamp ?>_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
         <div class="container" style="background:rgba(255,255,255,0.85); border-radius: 16px 16px 16px 16px; -moz-border-radius: 16px 16px 16px 16px; -webkit-border-radius: 16px 16px 16px 16px; border: 0px solid #000000;">
		  <div class="row">
			<div class="col-xs-2 col-sm-2 col-md-2" style="margin-top:2%;">
                 <div class="profile_header">
				  <?php
					  if($profile_league == 'C' or $profile_league == 'M' or $profile_league == 'D' or $profile_league == 'P' or $profile_league == 'G' or $profile_league == 'S')
					  {
						  $marquee = true;
					  }
					  else
					  {
						  $no_marquee = true;
					  } 
					  ?>
                      <img class="profile_icon" draggable="false" src="<?php echo URL ?>/style/images/base/summoners/icon/<?php echo $profile_icon ?>.png" alt="<?php echo $profile_name ?>" <?php if(!empty($no_marquee)) { echo 'style="border-radius: 18px 18px 18px 18px;-moz-border-radius: 18px 18px 18px 18px;-border: 0px solid #000000;"'; } ?>>
					 <?php if(!empty($marquee)) { echo '<img draggable="false" class="profile_border" src="'.URL.'/style/images/base/summoners/icon/border/'.$profile_league.'.png" alt="'.$profile_name.'">'; } ?>
                 </div>
			</div>
			<div class="col-xs-2 col-sm2 col-md2">
			<div style="padding: 18% 0% 2% 0%; width:200px;">
			<?php if(strlen($profile_name) > 15) { echo '<h4 class="profile_title">'.$profile_name.'</h4>'; } if(strlen($profile_name) >= 10) { echo '<h3 class="profile_title">'.$profile_name.'</h3>'; } if(strlen($profile_name) < 10) { echo '<h2 class="profile_title">'.$profile_name.'</h2>'; } ?>
			<h3 class="">Nivel <?php echo $profile_level ?> <?php echo $profile_region ?></h3>
			</div>
			</div>
			<?php
			$rage_query = $db->query('SELECT timestamp_start,data FROM lol_matches WHERE summoner_ids LIKE "%'.$summonerid.'%" ORDER BY timestamp_start DESC LIMIT 10');
			
			$profile_last7days_wins = 0;
			$profile_last7days_total = 0;
			$profile_ragemeter_days_lower = time();
			$profile_ragemeter_days_higher = 0;
			while($row = $rage_query->fetch_array(MYSQLI_ASSOC))
			{
				if(stdtoarray(json_decode($row['data']))['players'][$summonerid]['stats']['winner'] == 'true')
				{
					$profile_last7days_wins++;
				}
				if($row['timestamp_start'] > $profile_ragemeter_days_higher)
				{
					$profile_ragemeter_days_higher = $row['timestamp_start'];
				}
				if($row['timestamp_start'] < $profile_ragemeter_days_lower)
				{
					$profile_ragemeter_days_lower = $row['timestamp_start'];
				}
				$profile_last7days_total++;
			}
			if($profile_last7days_total < 10)
			{
				$ragefinal = checkrage(5,4);
				$kdadata = $db->query('SELECT data FROM lol_matches WHERE summoner_ids LIKE "%'.$summonerid.'%" LIMIT 10');
				$finalkda = null;
				$position = 0;
				while($row = $kdadata->fetch_row())
				{
					$kda_data = stdtoarray(json_decode($row[0]))['players'][$summonerid]['stats'];
					$thiskda[$position] = (($kda_data['kills']+$kda_data['assists'])/$kda_data['deaths']);
					if($position != 0)
					{
						$finalkda = (($finalkda+$thiskda[$position])/2);
					}
					$position++;
				}
				$motivationfinal = number_format(($ragefinal/date('j',((($profile_ragemeter_days_higher-$profile_ragemeter_days_lower)/2.5)*((((100-$ragefinal)-20)/2.5)*$finalkda)))*3), 0);
				if($ragefinal > 100)
				{
					$ragefinal = 100;
				}
				if($motivationfinal > 100)
				{
					$motivationfinal = 100;
				}
			}
			else
			{
				$ragefinal = checkrage($profile_last7days_wins,date('j',($profile_ragemeter_days_higher-$profile_ragemeter_days_lower)));
				$kdadata = $db->query('SELECT data FROM lol_matches WHERE summoner_ids LIKE "%'.$summonerid.'%" LIMIT 10');
				$finalkda = null;
				$position = 0;
				while($row = $kdadata->fetch_row())
				{
					$kda_data = stdtoarray(json_decode($row[0]))['players'][$summonerid]['stats'];
					$thiskda[$position] = (($kda_data['kills']+$kda_data['assists'])/$kda_data['deaths']);
					if($position != 0)
					{
						$finalkda = (($finalkda+$thiskda[$position])/2);
					}
					$position++;
				}
				$motivationfinal = number_format(($ragefinal/date('j',((($profile_ragemeter_days_higher-$profile_ragemeter_days_lower)/2.5)*((((100-$ragefinal)-20)/2.5)*$finalkda)))*3), 0);
				if($ragefinal > 100)
				{
					$ragefinal = 100;
				}
				if($motivationfinal > 100)
				{
					$motivationfinal = 100;
				}
			}
			
			?>
			<div class="col-md-6" style="margin-top:3%;">
                                    <?php if($ragefinal < 30) {echo '<div style="text-align:center;">Medidor de tilt</div>'; } else { echo 'Medidor de tilt'; } ?>
                                    <div class="progress" style="background-color:#088A08;background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(102, 255, 51, .15) 50%, rgba(102, 255, 51, .15) 75%, transparent 75%, transparent);background-image:      -o-linear-gradient(45deg, rgba(102, 255, 51, .15) 25%, transparent 25%, transparent 50%, rgba(102, 255, 51, .15) 50%, rgba(102, 255, 51, .15) 75%, transparent 75%, transparent);background-image:         linear-gradient(45deg, rgba(102, 255, 51, .15) 25%, transparent 25%, transparent 50%, rgba(102, 255, 51, .15) 50%, rgba(102, 255, 51, .15) 75%, transparent 75%, transparent);-webkit-background-size: 40px 40px;background-size: 40px 40px;-webkit-animation: progress-bar-stripes 2s linear infinite;-o-animation: progress-bar-stripes 2s linear infinite;animation: progress-bar-stripes 2s linear infinite;background-repeat: repeat-x;">
                                        <div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $ragefinal ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $ragefinal ?>%;">
                                            <span><?php echo $ragefinal ?>%</span>
                                        </div>
                                    </div>
									
									
									 <?php if($motivationfinal < 30) {echo '<div style="text-align:center;">Motivación</div>'; } else { echo 'Motivación'; } ?>
                                    <div class="progress" style="background-color:#d9534f;background-image: -webkit-linear-gradient(45deg, rgba(201, 48, 44, .15) 25%, transparent 25%, transparent 50%, rgba(201, 48, 44, .15) 50%, rgba(102, 255, 51, .15) 75%, transparent 75%, transparent);background-image:      -o-linear-gradient(45deg, rgba(201, 48, 44, .15) 25%, transparent 25%, transparent 50%, rgba(201, 48, 44, .15) 50%, rgba(102, 255, 51, .15) 75%, transparent 75%, transparent);background-image:         linear-gradient(45deg, rgba(201, 48, 44, .15) 25%, transparent 25%, transparent 50%, rgba(201, 48, 44, .15) 50%, rgba(102, 255, 51, .15) 75%, transparent 75%, transparent);-webkit-background-size: 40px 40px;background-size: 40px 40px;-webkit-animation: progress-bar-stripes 2s linear infinite;-o-animation: progress-bar-stripes 2s linear infinite;animation: progress-bar-stripes 2s linear infinite;background-repeat: repeat-x;">
                                        <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $motivationfinal ?>%;">
                                            <span><?php echo $motivationfinal ?>%</span>
                                        </div>
                                    </div>

                                </div>
			<div class="col-sm-2 col-md-2">
			<?php
			if($server_lookup == 'euw') {$server_lookup_fx = 'EUW1';} if($server_lookup == 'na') {$server_lookup_fx = 'NA1';} if($server_lookup == 'br') {$server_lookup_fx = 'BR1';} if($server_lookup == 'lan') {$server_lookup_fx = 'LA1';} if($server_lookup == 'las') {$server_lookup_fx = 'LA2';} if($server_lookup == 'oce') {$server_lookup_fx = 'OC1';}if($server_lookup == 'eune') {$server_lookup_fx = 'EUN1';}if($server_lookup == 'tr') {$server_lookup_fx = 'TR1';}if($server_lookup == 'ru') {$server_lookup_fx = 'RU';}if($server_lookup == 'kr') {$server_lookup_fx = 'KR';}
			$game_info_url = 'https://'.$server_lookup.'.api.pvp.net/observer-mode/rest/consumer/getSpectatorGameInfo/'.$server_lookup_fx.'/'.$summonerid.'?api_key='.LOL_API_KEY;
			if(url_exists($game_info_url))
			{
			echo '<a href="'.URL.'/game/'.$server_lookup.'/'.$profile_name.'" style="text-decoration: none;"><div class="service-box style-boxed small" style="background-color:#1abc9c;padding:10% 0% 5% 0%; border-radius: 7px 7px 7px 7px;-moz-border-radius: 7px 7px 7px 7px;-webkit-border-radius: 7px 7px 7px 7px;border: 0px solid #000000;">
                                                <div class="service-icon"><span class="icon_close_alt2"></span></div>
                                                <h3>¡En partida!</h3>
                                            </div></a>';
			}
			else
			{
			echo '<div class="service-box style-boxed small" style="padding:10% 0% 5% 0%; border-radius: 7px 7px 7px 7px;-moz-border-radius: 7px 7px 7px 7px;-webkit-border-radius: 7px 7px 7px 7px;border: 0px solid #000000;">
                                                <div class="service-icon"><span class="icon_close_alt2"></span></div>
                                                <h4>No está en partida</h4>
                                            </div>';
			}
			?>
                                           
           </div>
		   </div>
		   <div class="row" style="margin-top:0.3%;">
			<div class="col-md-2">
			<?php
			$timetoreload_s = (summonerinfo($summonerid,'onlol_last_update')+config('profilereload_interval'))-time();
				if($timetoreload_s/60 > 1)
				{
					if(round($timetoreload_s/60) > 1)
					{
					$timetoreload = round($timetoreload_s/60) .' Minutos';
					}
					else
					{
						$timetoreload = round($timetoreload_s/60) .' Minuto';
					}
				}
				else
				{
					$timetoreload = $timetoreload_s.' Segundos';
				}
				if($timetoreload_s/3600 > 1)
				{
					if(round($timetoreload_s/3600) > 1)
					{
						$timetoreload .= round($timetoreload_s/3600) .' Horas';
					}
					else
					{
						$timetoreload .= round($timetoreload_s/3600) .' Hora';
					}
				}
				if($timetoreload < 0)
				{
					$timetoreload = 0;
				}
			?>
			<script>
			$(function(){$("#reloadprofile").click(function(){
				var invid = "<?php echo $summonerid ?>";
				var user = "<?php echo $profile_name ?>";
				var server =  "<?php echo $server_lookup ?>";
				var reloaded = false;
				$(document).ajaxStart(function(){
					swal({   title: "Recargando...",   html: "Este proceso puede tomar unos minutos...<br>", showConfirmButton: false, showCancelButton: false, allowEscapeKey:false, allowOutsideClick: false, imageUrl: "<?php echo URL ?>/style/images/loading.gif", imageSize:"300x300"});
				});
				$.ajax({type: "POST",url: "<?php echo URL ?>/profile_reload",data: {"invid": invid, "server": server, "name": user},
				success: function(data){if(data == 1) { window.location="<?php echo URL.$_SERVER['REQUEST_URI'] ?>&&reloaded=true"; } if(data == 2) { swal({   title: "Error al actualizar",   html: "<?php echo $profile_name ?> se podrá actualizar de nuevo en <?php echo $timetoreload ?>.<br><br><br>", showConfirmButton: true, type:"error"}); }}
				});
				return false;
				})
			;});
			</script>
				<button type="button" id="reloadprofile" style="padding-left:5%;padding-right:5%;" class="btn btn-success"><span><i class="icon_refresh"></i> Renovar datos</span></button>
				<div id="responseprofile"></div>
			</div>
			<div class="col-md-2">
			<?php 
			if($profile_level == '30' && $profile_league != 'U')
			{
				?>
			<script>
			$(function(){$("#reloadmmr").click(function(){
				var invid = "<?php echo $summonerid ?>";
				var server =  "<?php echo $server_lookup ?>";
				$.ajax({type: "POST",url: "<?php echo URL ?>/mmr_reload",data: {"invid": invid, "server": server},
				success: function(data){$("#responsemmr").html(data);}
				});
				return false;
				})
			;});
			</script>
				<div id="responsemmr"><div <?php $distancetojump = legaveragemmr($profile_league, $profile_division) + config('mmr_interval_to_jumpandhell'); if($profile_mmr >= $distancetojump) { echo 'class="alert alert-info" title="¡GG! Es probable que saltes dos divisiones."'; } elseif($profile_mmr >= legaveragemmr($profile_league, $profile_division)) { echo 'class="alert alert-success" title="Tienes un mmr normal."'; } $distancetohell = legaveragemmr($profile_league, $profile_division) - config('mmr_interval_to_jumpandhell'); if($profile_mmr <= $distancetohell) { echo 'class="alert alert-danger" title="¡Cuidado! Estás en elohell."'; } elseif($profile_mmr <= legaveragemmr($profile_league, $profile_division)) { echo 'class="alert alert-warning" title="Cuidado, tu mmr está por debajo de la liga."'; }  ?> data-toggle="tooltip" data-placement="left" role="alert">MMR: 

				<?php echo $profile_mmr;
				$isupdateavaliable =  (int) $db->query('SELECT mmr_last_update FROM inv_users WHERE summoner_id="'.$summonerid.'" AND region="'.$profile_region.'" LIMIT 1')->fetch_row()[0]+config('mmr_interval');
				if($isupdateavaliable < time() ) { echo ' <i style="font-size: 16px; float:right; color: #3c763d; cursor: url('.URL.'/style/images/cursors/link.cur), auto;" id="reloadmmr" class="icon_refresh"></i>';} 
				else { echo '<i style="font-size: 16px; float:right; color: #3c763d; cursor: url('.URL.'/style/images/cursors/unavailable.cur), auto;" class="icon_close"></i>';}?></div></div>
			<?php
			}
			else
			{
			?>
				<div class="alert alert-warning" data-toggle="tooltip" data-placement="left" title="Para saber tu MMR debesser nivel 30, haber jugar clasificatorias en solitario y estar adherido a una liga." role="alert">¿¡MMR!?</div>
			<?php
			}
			?>
			</div>
			<div class="col-md-2">
				<button type="button" style="margin-left:10%;" class="btn btn-warning"><span><i class="icon_download"></i> Guardar replays</span></button>
			</div>
			<div class="col-md-3" style="margin-left:3.5%; margin-top:-0.1%;">
				<div class="alert alert-info" role="alert">Renovado hace <?php echo $profile_renewed ?></div>
			</div>
			<div class="col-md-2" style="float:right; margin-right:3%;">
			<script>
			$(function(){$("#sethomeinv").click(function(){
				var invid = "<?php echo $summonerid ?>";
				var server =  "<?php echo $server_lookup ?>";
				$.ajax({type: "POST",url: "<?php echo URL ?>/sethome",data: {"invid": invid, "server": server},
				success: function(data){$("#responsehome").html(data);}
				});
				return false;
				})
			;});
			</script>
			
			<?php
			if(!empty($_COOKIE['onlol_baseinv']))
			{
				$databaseinv = explode('/',$_COOKIE['onlol_baseinv']);
				$summonerbase = $databaseinv[0];
				$regionbase = $databaseinv[1];
				if($summonerbase == $summonerid && $regionbase == $server_lookup)
				{
					echo '<button style="cursor: url('.URL.'/style/images/cursors/help.cur), auto;" type="button" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="Este usuario ha sido establecido como principal para ir directamente a él cada vez que accedas a ONLoL."><span><i class="icon_house_alt"></i> Usuario principal</span></button>';
				}
				else
				{
					echo '<div id="responsehome"><button id="sethomeinv" type="button" style="cursor: url('.URL.'/style/images/cursors/link.cur), auto;" class="btn btn-info" data-toggle="tooltip" title="Marcar este usuario como principal para el menú rápido."><span><i class="icon_house_alt"></i> Hacer principal</span></button></div>';
				}
			}
			else
			{
					echo '<div id="responsehome"><button id="sethomeinv" type="button" class="btn btn-info" data-toggle="tooltip" data-placement="bottom" title="Establece este usuario como principal para ir directamente a él cada vez que accedas a ONLoL."><span><i class="icon_house_alt"></i> Hacer principal</span></button></div>';
			}
			?>
			
			</div>
		   </div>
		   <div class="row">
			<div class="col-md-12">
                <div class="pcss3t pcss3t-layout-top-center pcss3t-effect-slide-left pcss3t-effect-slide-top pcss3t-height-auto">
					<input type="radio" name="pcss3t" <?php if($profile_level == '30' && $profile_league != 'U') { echo 'checked'; } ?> id="tab1"class="tab-content-first">
					<label for="tab1"><i class="icon-building"></i>Ligas</label>
											
					<input type="radio" name="pcss3t" id="tab2" class="tab-content-2">
					<label for="tab2"><i class="icon-book"></i>Estadísticas de clasificatorias</label>
											
					<input type="radio" <?php if($profile_level == '30' && $profile_league == 'U') { echo 'checked'; } ?> name="pcss3t" id="tab3" class="tab-content-3">
					<label for="tab3"><i class="icon-inbox"></i>Historial</label>
										
					<input type="radio" name="pcss3t" id="tab4" class="tab-content-4">
					<label for="tab4"><i class="icon-bookmark"></i>Placas</label>
											
					<input type="radio" name="pcss3t" id="tab5" class="tab-content-5">
					<label for="tab5"><i class="icon-eye-open"></i>Runas</label>
											
					<input type="radio" name="pcss3t" id="tab6" class="tab-content-6">
					<label for="tab6"><i class="icon-globe"></i>Maestrías</label>
											
					<input type="radio" name="pcss3t" id="tab7" class="tab-content-7">
					<label for="tab7"><i class="icon-bolt"></i>Equipos</label>
											
					<input type="radio" name="pcss3t" id="tab8" class="tab-content-8">
					<label for="tab8"><i class="icon-bolt"></i>Consejos</label>
					<ul>
						<li class="tab-content tab-content-first">
							<div class="row">
													<div class="col-md-3 profile_start_box1_header">
														<h1>Ranked 3v3</h1>
													</div>
													<div class="col-md-3 profile_start_box2_header">
														<h1>Ranked SoloQ</h1>
													</div>
													<div class="col-md-3 profile_start_box3_header">
														<h1>Ranked 5v5</h1>
													</div>
												</div>
												<div class="row profile_start">
													<div class="col-md-3 profile_start_box1">
													<?php
													if($profile_wins3x3+$profile_losses3x3 == 0)
													{
														echo '<img draggable="false" src="'.URL.'/style/images/base/summoners/division/U/1.png">
														<div class="division">
															<h2><div style="visibility:hidden;">unranked</div></h2>
														</div>
														<div class="lp">
															<h3>Sin clasificar</h3>
														</div>
														<div class="wlwr">
															<h5><div style="visibility:hidden;">unranked</div></h5>
													<h4><div style="visibility:hidden;">unranked</div></h4>
														</div>';
													}
													else
													{
													?>
														<img draggable="false" src="<?php echo URL ?>/style/images/base/summoners/division/<?php echo $profile_league3x3 ?>/<?php if($profile_league3x3 == 'C' or $profile_league3x3 == 'M' or $profile_league3x3 == 'U') {echo '1';} else{echo $profile_division3x3;} ?>.png">
														<div class="division">
															<h2><?php if($profile_league3x3 == 'C' or $profile_league3x3 == 'M' or $profile_league3x3 == 'U') {echo '<div style="visibility:hidden;">no_division</div>';} else {echo parsedisivion($profile_division3x3); } ?></h2>
														</div>
														<div class="lp">
															<h3><?php echo $profile_leaguepoints3x3 ?> Puntos de liga</h3>
														</div>
														<div class="wlwr">
															<h5><span class="wins"><?php echo $profile_wins3x3 ?> Victorias</span> - <span class="losses"><?php echo $profile_losses3x3 ?> Derrotas</span> </h5>
															<?php $profile_winrate3x3 = number_format((100/($profile_wins3x3+$profile_losses3x3))*$profile_wins3x3); if($profile_winrate3x3 >50) { $profile_winrate_status_3x3 = 'positive';} if($profile_winrate3x3 < 50) { $profile_winrate_status_3x3 = 'negative';} if($profile_winrate3x3 == 50) { $profile_winrate_status_3x3 = 'neutral';}  ?><h4 class="wr_<?php echo $profile_winrate_status_3x3 ?>"><?php echo $profile_winrate3x3 ?>% Porcentaje de victorias</font></h4>
														</div>
													<?php } ?>
													</div>
													<div class="col-md-3 profile_start_box2">
													<?php
													if($profile_wins+$profile_losses == 0)
													{
														echo '<img draggable="false" src="'.URL.'/style/images/base/summoners/division/U/1.png">
														<div class="division">
															<h2><div style="visibility:hidden;">unranked</div></h2>
														</div>
														<div class="lp">
															<h3>Sin clasificar</h3>
														</div>
														<div class="wlwr">
															<h5><div style="visibility:hidden;">unranked</div></h5>
													<h4><div style="visibility:hidden;">unranked</div></h4>
														</div>';
													}
													else
													{
													?>
														<img draggable="false" src="<?php echo URL ?>/style/images/base/summoners/division/<?php echo $profile_league ?>/<?php if($profile_league == 'C' or $profile_league == 'M' or $profile_league == 'U') {echo '1';} else{echo $profile_division;} ?>.png">
														<div class="division">
															<h2><?php if($profile_league == 'C' or $profile_league == 'M' or $profile_league == 'U') {echo '<div style="visibility:hidden;">no_division</div>';} else {echo parsedisivion($profile_division); }?></h2>
														</div>
														<div class="lp">
															<h3><?php echo $profile_leaguepoints ?> Puntos de liga</h3>
														</div>
														<div class="wlwr">
															<h5><span class="wins"><?php echo $profile_wins ?> Victorias</span> - <span class="losses"><?php echo $profile_losses ?> Derrotas</span> </h5>
															<?php $profile_winrate = number_format((100/($profile_wins+$profile_losses))*$profile_wins); if($profile_winrate >50) { $profile_winrate_status = 'positive';} if($profile_winrate < 50) { $profile_winrate_status = 'negative';} if($profile_winrate == 50) { $profile_winrate_status = 'neutral';}  ?><h4 class="wr_<?php echo $profile_winrate_status ?>"><?php echo $profile_winrate ?>% Porcentaje de victorias</font></h4>
														</div>
													<?php } ?>
													</div>
													<div class="col-md-3 profile_start_box3">
													<?php
													if($profile_wins5x5+$profile_losses5x5 == 0)
													{
														echo '<img draggable="false" src="'.URL.'/style/images/base/summoners/division/U/1.png">
														<div class="division">
															<h2><div style="visibility:hidden;">unranked</div></h2>
														</div>
														<div class="lp">
															<h3>Sin clasificar</h3>
														</div>
														<div class="wlwr">
															<h5><div style="visibility:hidden;">unranked</div></h5>
													<h4><div style="visibility:hidden;">unranked</div></h4>
														</div>';
													}
													else
													{
													?>
														<img draggable="false" src="<?php echo URL ?>/style/images/base/summoners/division/<?php echo $profile_league5x5 ?>/<?php if($profile_league5x5 == 'C' or $profile_league5x5 == 'M' or $profile_league5x5 == 'U') {echo '1';} else{echo $profile_division5x5;} ?>.png">
														<div class="division">
															<h2><?php if($profile_league5x5 == 'C' or $profile_league5x5 == 'M' or $profile_league5x5 == 'U') {echo '<div style="visibility:hidden;">no_division</div>';} else {echo parsedisivion($profile_division5x5); } ?></h2>
														</div>
														<div class="lp">
															<h3><?php echo $profile_leaguepoints5x5 ?> Puntos de liga</h3>
														</div>
														<div class="wlwr">
															<h5><span class="wins"><?php echo $profile_wins5x5 ?>  Victorias</span> - <span class="losses"><?php echo $profile_losses5x5 ?> Derrotas</span> </h5>
															<?php $profile_winrate5x5 = number_format((100/($profile_wins5x5+$profile_losses5x5))*$profile_wins5x5); if($profile_winrate5x5 >50) { $profile_winrate_status_5x5 = 'positive';} if($profile_winrate5x5 < 50) { $profile_winrate_status_5x5 = 'negative';} if($profile_winrate5x5 == 50) { $profile_winrate_status_5x5 = 'neutral';} ?><h4 class="wr_<?php echo $profile_winrate_status_5x5 ?>"><?php echo $profile_winrate5x5 ?>% Porcentaje de victorias</font></h4>
														</div>
													<?php } ?>
													</div>
												</div>
						</li>
						
						<li class="tab-content tab-content-2">
							<div role="tabpanel" class="tabs-bordered">
												<ul class="nav nav-tabs">
													<li><a href="#s5" data-toggle="tab">Season 2015</a></li>
												</ul>
												<!-- Tab panels -->
												<?php
												$profile_stats_source = $db->query('SELECT data FROM inv_users_rankedstats WHERE summoner_id='.$summonerid.' AND region="'.$server_lookup.'"')->fetch_row();
												$profile_stats_data = stdtoarray(json_decode($profile_stats_source[0]));
												?>
												<div class="tab-content">
													
													<div role="tabpanel" class="tab-pane fade active in" id="s5">
													
													<div class="col-md-2">
													<h3> Estadísticas clasificatorias </h3>
															<table class="table table-hover">
																<thead>
																  <tr>
																	<th>Tipo</th>
																	<th>SoloQ</th>
																  </tr>
																</thead>
																<tbody>
																  <tr>
																	<td>Victorias</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_solo_5x5']['wins'] ?></td>
																  </tr>
																  <tr>
																	<td>Derrotas</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_solo_5x5']['losses'] ?></td>
																  </tr>
																  <tr>
																	<td>Partidas totales</td>
																	<td><?php echo ($profile_stats_data['SEASON2015']['ranked_solo_5x5']['wins']+$profile_stats_data['SEASON2015']['ranked_solo_5x5']['losses']) ?></td>
																  </tr>
																  <tr>
																	<td>Porcentaje de victoria</td>
																	<td><?php echo number_format((100/($profile_stats_data['SEASON2015']['ranked_solo_5x5']['wins']+$profile_stats_data['SEASON2015']['ranked_solo_5x5']['losses']))*$profile_stats_data['SEASON2015']['ranked_solo_5x5']['wins']); ?>%</td>
																  </tr>
																  <tr>
																	<td>Asesinatos</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_solo_5x5']['kills'] ?></td>
																  </tr>
																  <tr>
																	<td>Asistencias</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_solo_5x5']['assists'] ?></td>
																  </tr>
																  <tr>
																	<td>Torretas</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_solo_5x5']['turrets'] ?></td>
																  </tr>
																  <tr>
																	<td>Minions</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_solo_5x5']['minions'] ?></td>
																  </tr>
																</tbody>
																<br>
																<thead>
																  <tr>
																	<th>Tipo</th>
																	<th>Equipo 5x5</th>
																  </tr>
																</thead>
																<tbody>
																  <tr>
																	<td>Victorias</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_team_5x5']['wins'] ?></td>
																  </tr>
																  <tr>
																	<td>Derrotas</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_team_5x5']['losses'] ?></td>
																  </tr>
																  <tr>
																	<td>Partidas totales</td>
																	<td><?php echo ($profile_stats_data['SEASON2015']['ranked_team_5x5']['wins']+$profile_stats_data['SEASON2015']['ranked_team_5x5']['losses']) ?></td>
																  </tr>
																  <tr>
																	<td>Porcentaje de victoria</td>
																	<td><?php echo number_format((100/($profile_stats_data['SEASON2015']['ranked_team_5x5']['wins']+$profile_stats_data['SEASON2015']['ranked_team_5x5']['losses']))*$profile_stats_data['SEASON2015']['ranked_team_5x5']['wins']); ?>%</td>
																  </tr>
																  <tr>
																	<td>Asesinatos</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_team_5x5']['kills'] ?></td>
																  </tr>
																  <tr>
																	<td>Asistencias</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_team_5x5']['assists'] ?></td>
																  </tr>
																  <tr>
																	<td>Torretas</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_team_5x5']['turrets'] ?></td>
																  </tr>
																  <tr>
																	<td>Minions</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_team_5x5']['minions'] ?></td>
																  </tr>
																</tbody>
																<br>
																<thead>
																  <tr>
																	<th>Tipo</th>
																	<th>Equipo 3x3</th>
																  </tr>
																</thead>
																<tbody>
																  <tr>
																	<td>Victorias</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_team_3x3']['wins'] ?></td>
																  </tr>
																  <tr>
																	<td>Derrotas</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_team_3x3']['losses'] ?></td>
																  </tr>
																  <tr>
																	<td>Partidas totales</td>
																	<td><?php echo ($profile_stats_data['SEASON2015']['ranked_team_3x3']['wins']+$profile_stats_data['SEASON2015']['ranked_team_3x3']['losses']) ?></td>
																  </tr>
																  <tr>
																	<td>Porcentaje de victoria</td>
																	<td><?php echo number_format((100/($profile_stats_data['SEASON2015']['ranked_team_3x3']['wins']+$profile_stats_data['SEASON2015']['ranked_team_3x3']['losses']))*$profile_stats_data['SEASON2015']['ranked_team_3x3']['wins']); ?>%</td>
																  </tr>
																  <tr>
																	<td>Asesinatos</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_team_3x3']['kills'] ?></td>
																  </tr>
																  <tr>
																	<td>Asistencias</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_team_3x3']['assists'] ?></td>
																  </tr>
																  <tr>
																	<td>Torretas</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_team_3x3']['turrets'] ?></td>
																  </tr>
																  <tr>
																	<td>Minions</td>
																	<td><?php echo $profile_stats_data['SEASON2015']['ranked_team_3x3']['minions'] ?></td>
																  </tr>
																</tbody>
															  </table>
														</div>
														
														<div class="col-md-10">
														 <table id="ranked2015data" class="display" cellspacing="-30px" width="100%">
				<thead >
					<tr>
						<th>Campeón</th>
						<th>KDA</th>
						<th>Partidas</th>
						<th>% Victoria</th>
						<th>Súbitos</th>
						<th>Asesinatos</th>
						<th>Daño</th>
						<th>Récords</th>
					</tr>
				</thead>
				<tbody>
					<?php
									
									foreach($profile_stats_data['SEASON2015']['ranked_champions']['champions'] as $key => $value)
									{
										if($key != 0) // 0 means all season data
										{
											$totalgames = $value['wins']+$value['losses'];
											$specialkills = null;
											if($value['kills_double'] != 0)
											{
												$specialkills .= '<div class="row"><img src="'.URL.'/style/images/base/game/match/champion.png"> Dobles: '.$value['kills_double'].'</div>';
											}
											if($value['kills_triple'] != 0)
											{
												$specialkills .= '<div class="row"><img src="'.URL.'/style/images/base/game/match/champion.png"> Triples: '.$value['kills_triple'].'</div>';
											}
											if($value['kills_quadra'] != 0)
											{
												$specialkills .= '<div class="row"><img src="'.URL.'/style/images/base/game/match/champion.png"> Cuádruples: '.$value['kills_quadra'].'</div>';
											}
											if($value['kills_penta'] != 0)
											{
												$specialkills .= '<div class="row"><img src="'.URL.'/style/images/base/game/match/champion.png"> Pentakills: '.$value['kills_penta'].'</div>';
											}
											if($value['deaths'] == 0)
											{
												$fixeddivision = (1/$totalgames);
											}
											else
											{
												$fixeddivision = ($value['deaths']/$totalgames);
											}
											echo '<tr color="black">
											<td data-order="" style="text-align:center;"><div class="row"><a href="'.URL.'/champions/'.champidtokeyname($key).'"><img width="70%" draggable="false" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($key).'.png"></a></div> <div class="row"><a style="color:blue;" href="'.URL.'/champions/'.champidtokeyname($key).'">'.champidtoname($key).'</a></div></td>
											<td data-order="'.number_format(((($value['kills']/$totalgames)
											+($value['assists']/$totalgames))/($fixeddivision)),1).'">
											'.number_format(($value['kills']/$totalgames),1).'/
											'.number_format(($value['deaths']/$totalgames),1).'/
											'.number_format(($value['assists']/$totalgames),1).'</td>
											
											<td data-order="'.($totalgames).'">'.($totalgames).'</td>
											<td data-order="'.number_format((100/($totalgames))*$value['wins']).'">'.number_format((100/($value['wins']+$value['losses']))*$value['wins']).' %</td>
											<td data-order="'.number_format(($value['minions']/($totalgames)), 0).'"><img style="margin-top:-1%;margin-right:5%;" draggable="false" src="'.URL.'/style/images/base/game/match/minion.png"> '.number_format(($value['minions']/($totalgames))).' / partida</td>
											<td data-order="'.$value['kills_penta'].'"><div class="row">'.$specialkills.'</td>
											<td data-order="'.$row['timestamp'].'"></td>
											<td data-order=""><div class="row">'.$value['maxkillsonsinglegame'].' Asesinatos</div>
											<div class="row">'.$value['maxdeathsonsinglegame'].' Muertes</div>
											<div class="row"><img src="'.URL.'/style/images/base/game/match/gold.png"> '.substr($value['gold'],0,-3).' Mil</div>
											<div class="row"><img src="'.URL.'/style/images/base/game/match/turret.png"> '.$value['turrets'].' Torres</div></td>
											
											</tr>';
										}
									}
									?>
				</tbody>
			</table>
														</div>
														</div>
													<!-- add here new seasons -->
												</div>
												</div>
						</li>
						
						<li class="tab-content tab-content-3">
							<script>
$(function () {
    $('#chart_winrate').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        title: {
            text: 'Estadísticas de victoria',
        },
       plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y:.1f}% {point.name}'
                }
            }
        },
        series: [{
			name: 'Porcentaje',
            data: [
                {
                name: 'Victorias',
                y: <?php echo $profile_last7days_wins*10; ?>,
                color: '#2ec64a'
            },
			
			{
                name: 'Derrotas',
                y: <?php echo ($profile_last7days_total-$profile_last7days_wins)*10 ?>,
                color: '#ba6227'
            }
            ]
        }]
    });
});
<?php

		$kda_charts = $db->query('SELECT data FROM lol_matches WHERE summoner_ids LIKE "%'.$summonerid.'%" ORDER BY timestamp_start DESC LIMIT 10');
		$kda_chart_kills = 0;
		$kda_chart_deaths = 0;
		$kda_chart_assists = 0;
		while($row = $kda_charts->fetch_row())
		{
			$kda_data = stdtoarray(json_decode($row[0]))['players'][$summonerid]['stats'];
			$kda_chart_kills += $kda_data['kills'];
			$kda_chart_deaths += $kda_data['deaths'];
			$kda_chart_assists += $kda_data['assists'];
		}
		$kda_chart_total = $kda_chart_kills+$kda_chart_deaths+$kda_chart_assists;
		$kda_chart_kills = $kda_chart_kills;
		$kda_chart_deaths = $kda_chart_deaths;
		$kda_chart_assists = $kda_chart_assists;
		?>
	$(function () {
    $('#chart_kda').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        title: {
            text: 'Estadísticas de victoria',
        },
      plotOptions: {
            pie: {
                innerSize: 100,
                depth: 45
            }
        },
        series: [{
			name: 'Totales',
            data: [{
                name: "Asesinatos",
				color: '#508600',
                y: <?php echo $kda_chart_kills ?>
            }, {
                name: "Muertes",
				color: '#8d2d2c',
                y: <?php echo $kda_chart_deaths ?>
            }, {
                name: "Asistencias",
				color: '#848484',
                y: <?php echo $kda_chart_assists ?>
            }]
        }]
    });
});
<?php
		$position_charts = $db->query('SELECT data FROM lol_matches WHERE summoner_ids LIKE "%'.$summonerid.'%" ORDER BY timestamp_start DESC LIMIT 10');
		$position_chart_top = 0;
		$position_chart_jgl = 0;
		$position_chart_mid = 0;
		$position_chart_adc = 0;
		$position_chart_sup = 0;
		while($row = $position_charts->fetch_row())
		{
			$pos_data = stdtoarray(json_decode($row[0]))['players'][$summonerid]['position'];
			if($pos_data == 'TOP')
			{
				$position_chart_top++;
			}
			if($pos_data == 'JUNGLE')
			{
				$position_chart_jgl++;
			}
			if($pos_data == 'MID')
			{
				$position_chart_mid++;
			}
			if($pos_data == 'ADC')
			{
				$position_chart_adc++;
			}
			if($pos_data == 'SUPPORT')
			{
				$position_chart_sup++;
			}
		}
		?>
	$(function () {
    $('#chart_positions').highcharts({
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        title: {
            text: 'Posiciones',
        },
       plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
			name: 'Partidas',
            data: [
			<?php
			if($position_chart_top > 0)
			{
				echo '{
                name: "'.positionstr('TOP').'",
                y: '.$position_chart_top.'
				},';
			}
			if($position_chart_jgl > 0)
			{
				echo '{
                name: "'.positionstr('JUNGLE').'",
                y: '.$position_chart_jgl.'
				},';
			}
			if($position_chart_mid > 0)
			{
				echo '{
                name: "'.positionstr('MID').'",
                y: '.$position_chart_mid.'
				},';
			}
			if($position_chart_adc > 0)
			{
				echo '{
                name: "'.positionstr('ADC').'",
                y: '.$position_chart_adc.'
				},';
			}
			if($position_chart_sup > 0)
			{
				echo '{
                name: "'.positionstr('SUPPORT').'",
                y: '.$position_chart_sup.'
				},';
			}
			?>
            ]
        }]
    });
});
</script>
<div class="row"><div id="chart_winrate" style="<?php if(!empty($fixwidthofprofstats)) { echo 'width:30%;'; } else { echo 'width:19%;'; } ?> margin-left:2%; height: 300px; margin-bottom:4%; opacity:0.95;"></div>
<div id="chart_kda" style="<?php if(!empty($fixwidthofprofstats)) { echo 'width:30%; margin-left:35%;'; } else { echo 'width:19%; margin-left:34.5%;'; } ?> height: 300px; margin-top:-29.6%; margin-bottom:4%; opacity:0.95;"></div>
<div id="chart_positions" style="<?php if(!empty($fixwidthofprofstats)) { echo 'width:30%; margin-right:2%; float:right;'; } else { echo 'width:19%; margin-left:67%;'; } ?> height: 300px; margin-top:-29.6%; margin-bottom:4%; opacity:0.95;"></div></div>
													<div class="col-md-12">
														<table id="lastgamestable" class="display" cellspacing="-30px" width="100%">
				<thead >
					<tr>
						<th></th>
						<th>Campeón</th>
						<th>KDA</th>
						<th>BUILD</th>
						<th>Posición</th>
						<th>Súbitos</th>
						<th>Cola</th>
						<th>Fecha</th>
						<th>Ver</th>
					</tr>
				</thead>
				<tbody>
					<?php
									$profile_lastmatches = $db->query('SELECT data,timestamp_start,timestamp_end,ips_earned,qeue,match_id FROM lol_matches WHERE summoner_ids LIKE "%'.$summonerid.'%" ORDER BY timestamp_start DESC LIMIT '.config('max_profile_matches_to_show'));
									
									while($row = $profile_lastmatches->fetch_row())
									{
										$match_data = stdtoarray(json_decode($row[0]))['players'][$summonerid];
										if($match_data['team'] == 100)
										{
											$bgcolor_team = '#5960e6';
										}
										if($match_data['team'] == 200)
										{
											$bgcolor_team = '#bc3cff';
										}
										if($match_data['stats']['winner'] == 'true')
										{
											$bgcolor_result = '#2ec64a';
										}
										if($match_data['stats']['winner'] == 'false')
										{
											$bgcolor_result = '#ba6227';
										}
										$itemsize = 20;
										if($match_data['stats']['deaths'] == 0)
										{
											$match_data['stats']['deaths'] = 1;
										}
										$kdaorder = ($match_data['stats']['kills']+$match_data['stats']['assists'])/$match_data['stats']['deaths'];
										if($match_data['stats']['killscount_doubles'] > 0)
										{
											$strmaxkills = 'DOUBLE';
											$intmaxkills = 2;
										}
										if($match_data['stats']['killscount_triples'] > 0)
										{
											$strmaxkills = 'TRIPLE';
											$intmaxkills = 3;
										}
										if($match_data['stats']['killscount_quadras'] > 0)
										{
											$strmaxkills = 'QUADRA';
											$intmaxkills = 4;
										}
										if($match_data['stats']['killscount_pentas'] > 0)
										{
											$strmaxkills = 'PENTA';
											$intmaxkills = 5;
										}
										if(empty($intmaxkills))
										{
											$strmaxkills = 'NONE';
											$intmaxkills = 1;
										}
										$ips_array = stdtoarray(json_decode($row[3]));
										if(array_key_exists($summonerid,$ips_array))
										{
											$ip_earned = $ips_array[$summonerid];
										}
										else
										{
											$ip_earned = 'No disponible';
										}
										echo '<tr style="background:'.$bgcolor_result.';color:white;">
										<td data-order="'.$match_data['champ_id'].'" width="8%" style="background-color:'.$bgcolor_team.';"><a href="'.URL.'/champions/'.champidtokeyname($match_data['champ_id']).'"><img width="100%" draggable="false" src="'.URL.'/style/images/base/champions/little/'.champidtokeyname($match_data['champ_id']).'.png"></a><div style="margin-top:-28%;">'.$match_data['stats']['champ_level'].'</div><div class="row" style="margin-left:4%;"><img draggable="false" width="40%" src="'.URL.'/style/images/base/summoners/spells/'.$match_data['spell_1'].'.png"><img width="40%" draggable="false" src="'.URL.'/style/images/base/summoners/spells/'.$match_data['spell_2'].'.png"></div></td>
										<td data-order="'.$match_data['champ_id'].'"><a style="color:white;" href="'.URL.'/champions/'.champidtokeyname($match_data['champ_id']).'">'.champidtoname($match_data['champ_id']).'<a></td>
										<td data-order="'.$kdaorder.'">'.$match_data['stats']['kills'].'/'.$match_data['stats']['deaths'].'/'.$match_data['stats']['assists'].'</td>
										
										<td>
										<div style="margin-left:4%;"class="row"><img draggable="false" width="'.$itemsize.'%" src="'.URL.'/style/images/base/game/items/'.$match_data['stats']['items']['0'].'.png"><img width="'.$itemsize.'%" draggable="false" src="'.URL.'/style/images/base/game/items/'.$match_data['stats']['items']['1'].'.png"><img width="'.$itemsize.'%" draggable="false" src="'.URL.'/style/images/base/game/items/'.$match_data['stats']['items']['2'].'.png"></div>
										<div style="margin-left:4%;"class="row"><img width="'.$itemsize.'%" draggable="false" src="'.URL.'/style/images/base/game/items/'.$match_data['stats']['items']['3'].'.png"><img width="'.$itemsize.'%" draggable="false" src="'.URL.'/style/images/base/game/items/'.$match_data['stats']['items']['4'].'.png"><img width="'.$itemsize.'%" draggable="false" src="'.URL.'/style/images/base/game/items/'.$match_data['stats']['items']['5'].'.png"></div>
										
										<div style="float:right; margin-right:2.5%;margin-top:-30%"><img width="50%" draggable="false" src="'.URL.'/style/images/base/game/items/'.$match_data['stats']['items']['6'].'.png"></div></td>
										<td>'.positionstr($match_data['position']).'</td>
										<td data-order="'.$match_data['stats']['minions'].'"><div class="row"><img draggable="false" src="'.URL.'/style/images/base/game/match/minion.png">'.$match_data['stats']['minions'].' ('.number_format(($match_data['stats']['minions']/((($row[2]-$row[1])/60))), 0).' CS/min)</div>
										<div class="row"><img src="'.URL.'/style/images/base/game/match/champion.png"> '.killstr($strmaxkills).'</div>
										<div class="row"><img src="'.URL.'/style/images/base/game/match/gold.png"> '.substr($match_data['stats']['total_gold'],0,-3).' mil</div>
										<div class="row"><img src="'.URL.'/style/images/base/game/match/score.png"> '.$ip_earned.' PI</div></td>
										<td>'.gametypes($row[4]).'</td>
										<td data-order="'.$row[2].'"><div class="row">Hace '.time_elapsed_string($row[2]).'</div><div class="row" style="margin-left:5%;">'.number_format((($row[2]-$row[1])/60),0).':'.((substr(number_format((($row[2]-$row[1])/60),1),-1)/10)*60).'</div></td>
										<td data-order="'.$row[5].'"><a href="'.URL.'/games/'.$row[5].'?player_id='.$profile_name.'"><button type="button" class="btn btn-sm btn-info">Ver más</button></a></td>
										
										</tr>';
									}
									?>
				</tbody>
			</table>		
						</li>
						
						<li class="tab-content tab-content-4">
							<table class="table table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>Título</th>
        <th>Descripción</th>
      </tr>
    </thead>
    <tbody>
											<?php
											if($db->query('SELECT id FROM inv_users_badges WHERE summoner_id='.$summonerid.'')->num_rows != 0)
											{
												$userbadges = $db->query('SELECT badge_keyname,title,description,summoner_id FROM inv_users_badges WHERE summoner_id='.$summonerid.'');
												while($row = $userbadges->fetch_row())
												{
													if(strstr($row[0], 'best_player_of_'))
													{
														$depured = true;
														$fiximg = URL.'/style/images/base/champions/little/'.str_replace('best_player_of_', null, $row[0]).'.png';
													}
													if(strstr($row[0], 'enthusiastic_player_of_'))
													{
														$depured = true;
														$fiximg = URL.'/style/images/base/champions/little/'.str_replace('enthusiastic_player_of_', null, $row[0]).'.png';
													}
													if(empty($depured))
													{
														$fiximg = URL.'/style/images/badges/'.$row[0].'.png.';
													}
													echo '<tr>
															<td><img draggable="false" width="25%" src="'.$fiximg.'"></td>
															<td>'.$row[1].'</td>
															<td>'.$row[2].'</td>
														  </tr>';
												}
											}
											else
											{
												echo '<td><img draggable="false" width="25%" src="'.URL.'/style/images/base/champions/little/_0.png"></td>
															<td>Este usuario aún no posee placas</td>
															<td>Este usuario aún no posee placas</td>
														  </tr>';
											}
											?>
											</tbody>
  </table>
						</li>
						<li class="tab-content tab-content-5">
						<div role="tabpanel" class="tabs-bordered">
												 <?php
												  /* Menu */
												  echo ' <ul class="nav nav-tabs">';
												  
												  foreach($profile_runes_data as $getrunepagedata)
												  {
													if($getrunepagedata['current'] == 1)
													{
													 $active_status = ' class="active"';
													}
													else
													{
														$active_status = null;
													}
													echo '<li'.$active_status.'><a href="#'.$getrunepagedata['id'].'" data-toggle="tab">'.$getrunepagedata['name'].'</a></li>
													';
												  }
														echo '</ul>';
														
														/* Rune pages */
														echo ' <div class="tab-content">';
												  foreach($profile_runes_data as $getrunepagedatadetails)
												  {
													if($getrunepagedatadetails['current'] == 1)
													{
													 $active_status = ' in active';
													}
													else
													{
														$active_status = null;
													}
													
													$actual_runepagecount = 0;
													$rune = array();
													$rune_info = array();
													$rune_stats = array();
													while($actual_runepagecount < 30)
													{
														if(!empty($getrunepagedatadetails['slots'][$actual_runepagecount]))
														{
															$rune[$actual_runepagecount] = $getrunepagedatadetails['slots'][$actual_runepagecount]['runeId'];
															$rune_info[$actual_runepagecount] = $db->query('SELECT name FROM lol_runes WHERE rune_id='.$getrunepagedatadetails['slots'][$actual_runepagecount]['runeId'].'')->fetch_row()[0];
															$rune_stats[$actual_runepagecount]= $db->query('SELECT stats FROM lol_runes WHERE rune_id='.$getrunepagedatadetails['slots'][$actual_runepagecount]['runeId'].'')->fetch_row()[0];
														}
														else
														{
															if($actual_runepagecount <= 8) /* Mark */
															{
																$rune[$actual_runepagecount] = 'empty_mark';
																$rune_info[$actual_runepagecount] = 'Marca vacía.';
																$rune_stats[$actual_runepagecount] = null;
															}
															if($actual_runepagecount > 8 AND $actual_runepagecount <= 17) /* Seal */
															{
																$rune[$actual_runepagecount] = 'empty_seal';
																$rune_info[$actual_runepagecount] = 'Sello vacío.';
																$rune_stats[$actual_runepagecount] = null;
															}
															if($actual_runepagecount > 17 AND $actual_runepagecount <= 26) /* Glyph */
															{
																$rune[$actual_runepagecount] = 'empty_glyph';
																$rune_info[$actual_runepagecount] = 'Glifo vacío.';
																$rune_stats[$actual_runepagecount] = null;
															}
															if($actual_runepagecount > 26) /* Quints */
															{
																$rune[$actual_runepagecount] = 'empty_quint';
																$rune_info[$actual_runepagecount] = 'Quintaesencia vacía.';
																$rune_stats[$actual_runepagecount] = null;
															}
															
														}
														$actual_runepagecount++;
													}
													 echo '<div role="tabpanel" class="tab-pane fade'.$active_status.'" id="'.$getrunepagedatadetails['id'].'">
													 
													 <div class="rune-page-runes" style="border: 1px solid #111; box-shadow: 0 0 5px #111 inset; background-image: url(\''.URL.'/style/images/base/summoners/runes/background.png\'); float: left; height: 436px; position: relative; width: 655px;">
                                                                                    <div title="'.$rune_info[0].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 370px; left: 24px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[0].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[1].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 370px; left: 86px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[1].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[2].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 371px; left: 158px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[2].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[3].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 312px; left: 8px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[3].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[4].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 311px; left: 70px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[4].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[5].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 323px; left: 125px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[5].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[6].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 262px; left: 29px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[6].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[7].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 225px; left: 72px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[7].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[8].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 262px; left: 115px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[8].'.png); background-size: 50px 56px;"></div>
                                                    
                                                                                    <div title="'.$rune_info[9].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 185px; left: 38px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[9].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[10].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 138px; left: 66px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[10].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[11].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 179px; left: 107px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[11].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[12].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 112px; left: 119px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[12].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[13].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 70px; left: 154px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[13].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[14].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 43px; left: 204px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[14].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[15].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 24px; left: 265px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[15].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[16].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 5px; left: 343px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[16].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[17].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 55px; left: 371px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[17].'.png); background-size: 50px 56px;"></div>
                                                    
                                                                                    <div title="'.$rune_info[18].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 6px; left: 399px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[18].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[19].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 8px; left: 463px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[19].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[20].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 6px; left: 550px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[20].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[21].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 53px; left: 431px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[21].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[22].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 40px; left: 507px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[22].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[23].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 40px; left: 599px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[23].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[24].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 87px; left: 482px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[24].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[25].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 79px; left: 555px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[25].'.png); background-size: 50px 56px;"></div>
                                                                                                                <div title="'.$rune_info[26].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 133px; left: 576px; z-index: 2; width: 50px; height: 56px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[26].'.png); background-size: 50px 56px;"></div>
                                                    
                                                                                    <div title="'.$rune_info[27].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 170px; left: 413px; z-index: 2; width: 80px; height: 80px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune[27].'.png); background-size: 80px;"></div>
                                                                                                                <div title="'.$rune_info[28].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 228px; left: 192px; z-index: 2; width: 80px; height: 80px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune['28'].'.png); background-size: 80px;"></div>
                                                                                                                <div title="'.$rune_info[29].'" data-toggle="tooltip" data-placement="right" style="position: absolute; top: 40px; left: 44px; z-index: 2; width: 80px; height: 80px; background-image: url('.URL.'/style/images/base/summoners/runes/'.$rune['29'].'.png); background-size: 80px;"></div>
                                                                        </div>

                <div style="float: left; width: 181px; ">
                    <div style="background:rgb(17, 17, 17);border: 1px solid rgb(17, 17, 17); box-shadow: 0pt 0pt 5px rgb(17, 17, 17) inset; background: -moz-linear-gradient(center top , rgb(57, 57, 57) 0%, rgb(48, 48, 48) 100%) repeat scroll 0% 0% transparent; height: 398px;">
                        <ul class="rune_statistics" style="list-style-type:none">
															<h1 style="color:#BBB;">'.$getrunepagedatadetails['name'].'</h1>';
															
															/* Sum all data of runes */
															$runecount_sum = 0;
															foreach($rune_stats as $key => $val)
															{
																$runedataname = (array) key(json_decode($val));
																$runedatadesc = (array) json_decode($val);
																
																if(empty($runesdata[$runedataname[0]]))
																{
																	$runesdata[$runedataname[0]] = 0;
																}
																$runesdata[$runedataname[0]] = ($runesdata[$runedataname[0]]+$runedatadesc[$runedataname[0]]);
																$runecount_sum++;
															}
															
															foreach($runesdata as $key => $val)
															{
																echo runestatstr($key);
															}
                                                           
                        echo '</ul>
                    </div>
                </div></div>';
													
												  }
													 
												  ?>
												</div></div>
						</li>
						<li class="tab-content tab-content-6">
						66666666666666666666666666666666666666666666666666666666666666666666666
						</li>
						
						<li class="tab-content tab-content-7">
						8
						</li>
						
						<li class="tab-content tab-content-8">
						0
						</li>
					</ul>
                                     
				</div>
         </div>
		 
    </section>
          <?php echo footer(); ?>
            <span class="back-to-top"></span>
        </div><!-- end .wrapper -->

         <!-- Javascript -->
        <script type="text/javascript" src="<?php echo URL ?>/style/js/grid.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>/style/js/scripts.js"></script>
		<?php echo adblock() ?>
		<script src="<?php echo URL ?>/style/plugins/bootstrap/js/bootstrap.min.js"></script>
		<script>
		$(document).ready(function(){
		$('[data-toggle="tooltip"]').tooltip();   
		});
		</script>
    </body>
</html>