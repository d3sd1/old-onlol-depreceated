<?php
include('kernel/core.php');
include('kernel/class/summonerReload.php');
$pageName = 'title.summoner'; //Lang key
$pageMenu = 'summoners';
$pageSubMenu = null;
$pageNameVarKey = '{summoner}'; 
$pageNameVarVal = $summonerInfo['name']; 
$pageTemplates = '<link href="'.URL.'/style/css/bootstrap-select.min.css" rel="stylesheet"><link href="'.URL.'/style/css/css-chart.css" rel="stylesheet"><link href="'.URL.'/style/css/sidebar-nav.min.css" rel="stylesheet">'; // CSS Scripts to load
$pageScripts = '<script src="'.URL.'/style/js/bootstrap-select.min.js" type="text/javascript"></script><script src="'.URL.'/style/js/index/morris.js"></script><script src="'.URL.'/style/js/Chart.min.js"></script><script src="'.URL.'/style/js/sidebar-nav.min.js"></script><script src="'.URL.'/style/js/sidebar-nav.min.js"></script>'; // JS Scripts to load
require('kernel/template/header.tpl');
?>
  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="row bg-title" style="margin-bottom: 0px !important;">
       
      </div>
	  <?php if($summonerInfo['actualGame']['status'] == true)
	  {
		echo '<a href="'.URL.'/live/'.$summonerRegion.'/'.$summonerInfo['name'].'" style="text-align:center;"><div class="alert alert-info alert-dismissable animated flash">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              '.str_replace('{{name}}',$summonerInfo['name'],$lang['profile.actualgame']).' </div></a>';  
	  } ?>
      <!-- /.row -->
      <!-- .row -->
      <div class="row">
        <div class="col-md-4 col-xs-12">
          <div class="white-box">
            <div class="user-bg"> <img width="100%" alt="user" src="<?php echo URL ?>/style/game/champions/splash/<?php if(!empty($summonerInfo['champMastery'][0]['championId'])) { echo $summonerInfo['champMastery'][0]['championId']; } else { if(!empty($summonerInfo['games']['recent'][0]['championId'])) { echo $summonerInfo['games']['recent'][0]['championId']; } else { echo 1; } } ?>_0.jpg">
              <div class="overlay-box">
                <div class="user-content"> <a href="javascript:void(0)"><img src="<?php echo URL ?>/style/game/summoners/icons/<?php echo $summonerInfo['icon'] ?>.png" class="thumb-lg img-circle" alt="summonerIcon" draggable="false"></a>
                  <h4 class="text-white"><?php echo $summonerInfo['name'] ?></h4>
                  <h5 class="text-white"><?php echo $lang['profile.level'].' '.$summonerInfo['level'] ?></h5>
                </div>
              </div>
            </div>
            <div class="user-btm-box">
              <div class="col-md-4 col-sm-4 text-center">
                <h1 style="font-size: 20px; line-height:0px"><?php echo $lang['profile.league.queue.3x3'] ?></h1>
				<?php if(empty($summonerInfo['leagues']['RANKED_TEAM_3x3']['tier'])) { $summonerInfo['leagues']['RANKED_TEAM_3x3']['tier'] = 'UNRANKED'; }
				if(empty($summonerInfo['leagues']['RANKED_TEAM_3x3']['division'])) { $summonerInfo['leagues']['RANKED_TEAM_3x3']['division'] = 'V'; } ?>
                <img draggable="false" width="100%" height="100%" src="<?php echo URL ?>/style/game/summoners/divisions/<?php echo $summonerInfo['leagues']['RANKED_TEAM_3x3']['tier'] ?>/<?php echo $summonerInfo['leagues']['RANKED_TEAM_3x3']['division'] ?>.png">
                <h4><?php echo $lang['league.'.$summonerInfo['leagues']['RANKED_TEAM_3x3']['tier']].' '.($summonerInfo['leagues']['RANKED_TEAM_3x3']['tier'] != 'UNRANKED' ? $summonerInfo['leagues']['RANKED_TEAM_3x3']['division'] : null) ?></h4>
				<?php
				if($summonerInfo['leagues']['RANKED_TEAM_3x3']['tier'] != 'UNRANKED') { echo '<h5> <b style="color:#67b125">'.$summonerInfo['leagues']['RANKED_TEAM_3x3']['wins'].$lang['profile.matchhistory.chart.win.short'].'</b> <b style="color:#b12525">'.$summonerInfo['leagues']['RANKED_TEAM_3x3']['losses'].$lang['profile.matchhistory.chart.lose.short'].'</b>';
				
				if($summonerInfo['leagues']['RANKED_TEAM_3x3']['miniSeries'] == null) { echo '<br><b>'.$summonerInfo['leagues']['RANKED_TEAM_3x3']['lp'].' '.$lang['profile.matchhistory.chart.lp.short'].'</b>'; } else { echo '<br> <a style="color:black;" class="mytooltip" href="javascript:void(0)">'.str_replace(array('N','W','L'),array('<i style="color:black;" class="fa fa-circle-o"></i>','<i style="color:#67b125;" class="fa fa-check-circle-o"></i>','<i style="color:#b12525;" class="fa fa-times-circle-o"></i>'),$summonerInfo['leagues']['RANKED_TEAM_3x3']['miniSeries']['progress']).' <span class="tooltip-content3">'.$lang['profile.leagues.promo.prev'].'</span></a>'; } echo '</h5>'; } ?>
              </div>
              <div class="col-md-4 col-sm-4 text-center">
                <h1 style="font-size: 20px; line-height:0px"><?php echo $lang['profile.league.queue.solo'] ?></h1>
				<?php if(empty($summonerInfo['leagues']['RANKED_SOLO_5x5']['tier'])) { $summonerInfo['leagues']['RANKED_SOLO_5x5']['tier'] = 'UNRANKED'; }
				if(empty($summonerInfo['leagues']['RANKED_SOLO_5x5']['division'])) { $summonerInfo['leagues']['RANKED_SOLO_5x5']['division'] = 'V'; } ?>
                <img draggable="false" width="100%" height="100%" src="<?php echo URL ?>/style/game/summoners/divisions/<?php echo $summonerInfo['leagues']['RANKED_SOLO_5x5']['tier'] ?>/<?php echo $summonerInfo['leagues']['RANKED_SOLO_5x5']['division'] ?>.png">
                <h4><?php echo $lang['league.'.$summonerInfo['leagues']['RANKED_SOLO_5x5']['tier']].' '.($summonerInfo['leagues']['RANKED_SOLO_5x5']['tier'] != 'UNRANKED' ? $summonerInfo['leagues']['RANKED_SOLO_5x5']['division'] : null) ?></h4>
				<?php
				if($summonerInfo['leagues']['RANKED_SOLO_5x5']['tier'] != 'UNRANKED') { echo '<h5> <b style="color:#67b125">'.$summonerInfo['leagues']['RANKED_SOLO_5x5']['wins'].$lang['profile.matchhistory.chart.win.short'].'</b> <b style="color:#b12525">'.$summonerInfo['leagues']['RANKED_SOLO_5x5']['losses'].$lang['profile.matchhistory.chart.lose.short'].'</b>';
				
				if($summonerInfo['leagues']['RANKED_SOLO_5x5']['miniSeries'] == null) { echo '<br><b>'.$summonerInfo['leagues']['RANKED_SOLO_5x5']['lp'].' '.$lang['profile.matchhistory.chart.lp.short'].'</b>'; } else { echo '<br> <a style="color:black;" class="mytooltip" href="javascript:void(0)">'.str_replace(array('N','W','L'),array('<i style="color:black;" class="fa fa-circle-o"></i>','<i style="color:#67b125;" class="fa fa-check-circle-o"></i>','<i style="color:#b12525;" class="fa fa-times-circle-o"></i>'),$summonerInfo['leagues']['RANKED_SOLO_5x5']['miniSeries']['progress']).' <span class="tooltip-content3">'.$lang['profile.leagues.promo.prev'].'</span></a>'; } echo '</h5>'; } ?>
              </div>
              <div class="col-md-4 col-sm-4 text-center">
			    <h1 style="font-size: 20px; line-height:0px"><?php echo $lang['profile.league.queue.5x5'] ?></h1>
				<?php if(empty($summonerInfo['leagues']['RANKED_TEAM_5x5']['tier'])) { $summonerInfo['leagues']['RANKED_TEAM_5x5']['tier'] = 'UNRANKED'; }
				if(empty($summonerInfo['leagues']['RANKED_TEAM_5x5']['division'])) { $summonerInfo['leagues']['RANKED_TEAM_5x5']['division'] = 'V'; } ?>
                <img draggable="false" width="100%" height="100%" src="<?php echo URL ?>/style/game/summoners/divisions/<?php echo $summonerInfo['leagues']['RANKED_TEAM_5x5']['tier'] ?>/<?php echo $summonerInfo['leagues']['RANKED_TEAM_5x5']['division'] ?>.png">
                <h4><?php echo $lang['league.'.$summonerInfo['leagues']['RANKED_TEAM_5x5']['tier']].' '.($summonerInfo['leagues']['RANKED_TEAM_5x5']['tier'] != 'UNRANKED' ? $summonerInfo['leagues']['RANKED_TEAM_5x5']['division'] : null) ?></h4>
				<?php
				if($summonerInfo['leagues']['RANKED_TEAM_5x5']['tier'] != 'UNRANKED') { echo '<h5> <b style="color:#67b125">'.$summonerInfo['leagues']['RANKED_TEAM_5x5']['wins'].$lang['profile.matchhistory.chart.win.short'].'</b> <b style="color:#b12525">'.$summonerInfo['leagues']['RANKED_TEAM_5x5']['losses'].$lang['profile.matchhistory.chart.lose.short'].'</b>';
				
				if($summonerInfo['leagues']['RANKED_TEAM_5x5']['miniSeries'] == null) { echo '<br><b>'.$summonerInfo['leagues']['RANKED_TEAM_5x5']['lp'].' '.$lang['profile.matchhistory.chart.lp.short'].'</b>'; } else { echo '<br> <a style="color:black;" class="mytooltip" href="javascript:void(0)">'.str_replace(array('N','W','L'),array('<i style="color:black;" class="fa fa-circle-o"></i>','<i style="color:#67b125;" class="fa fa-check-circle-o"></i>','<i style="color:#b12525;" class="fa fa-times-circle-o"></i>'),$summonerInfo['leagues']['RANKED_TEAM_5x5']['miniSeries']['progress']).' <span class="tooltip-content3">'.$lang['profile.leagues.promo.prev'].'</span></a>'; } echo '</h5>'; } ?>
              </div>
            </div>
          </div>
		  <?php 
		if(count($summonerInfo['games']) > 0)
		{
			echo '<div class="white-box">
            <canvas id="playerPerspective" height="158"></canvas>
		  </div>';
		}?>
        </div>
        <div class="col-md-8 col-xs-12">
          <div class="white-box">
            <ul class="nav nav-tabs tabs customtab">
              <li class="active tab"><a> <span class="visible-xs"><i class="fa fa-history"></i></span> <span class="hidden-xs"><?php echo $lang['profile.matchhistory'] ?></span> </a> </li>
              <li class="tab"><a href="<?php echo URL ?>/summoner/<?php echo $summonerRegion ?>/<?php echo $summonerInfo['name'] ?>/leagues"> <span class="visible-xs"><i class="fa fa-trophy"></i></span> <span class="hidden-xs"><?php echo $lang['profile.leagues'] ?></span> </a> </li>
              <li class="tab"><a href="<?php echo URL ?>/summoner/<?php echo $summonerRegion ?>/<?php echo $summonerInfo['name'] ?>/stats"> <span class="visible-xs"><i class="fa fa-line-chart"></i></span> <span class="hidden-xs"><?php echo $lang['profile.stats'] ?></span> </a> </li>
              <li class="tab"><a href="<?php echo URL ?>/summoner/<?php echo $summonerRegion ?>/<?php echo $summonerInfo['name'] ?>/champmastery"> <span class="visible-xs"><i class="fa fa-bar-chart"></i></span> <span class="hidden-xs"><?php echo $lang['profile.champs'] ?></span> </a> </li>
			  <li class="tab"><a href="<?php echo URL ?>/summoner/<?php echo $summonerRegion ?>/<?php echo $summonerInfo['name'] ?>/runes"> <span class="visible-xs"><i class="fa fa-balance-scale"></i></span> <span class="hidden-xs"><?php echo $lang['profile.runes'] ?></span> </a> </li>
			  <li class="tab"><a href="<?php echo URL ?>/summoner/<?php echo $summonerRegion ?>/<?php echo $summonerInfo['name'] ?>/masteries"> <span class="visible-xs"><i class="fa fa-balance-scale"></i></span> <span class="hidden-xs"><?php echo $lang['profile.masteries'] ?></span> </a> </li>
            </ul>
            <div class="tab-content">
                <!-- start -->
				<?php 
				if(count($summonerInfo['games']) > 0)
				{?>
				<div class="row">
					<div class="col-lg-12">
					  <div class="white-box">
						<table id="matchHistory" class="table toggle-arrow-tiny" data-page-size="7">
						  <thead>
							<tr>
							  <th data-toggle="true"><?php echo $lang['profile.matchhistory.champ'] ?></th>
							  <th><?php echo $lang['profile.matchhistory.kda'] ?></th>
							  <th><?php echo $lang['profile.matchhistory.matchtype'] ?></th>
							  <th data-sort-initial="descending" data-type="numeric"><?php echo $lang['profile.matchhistory.date'] ?></th>
							</tr>
						  </thead>
						  <tbody>
						  <?php
						  foreach($summonerInfo['games']['recent'] as $gameInfo)
						  {
							switch($gameInfo['stats']['win'])
							{
								case true:
								$bgColor = 'a3cfec';
								$borderColor = '99b9cf';
								break;
								case false:
								$bgColor = 'e2b6b3';
								$borderColor = 'cea7a7';
								break;
							}
							echo '<tr style="background-color: #'.$bgColor.'; border-color: #'.$borderColor.'; border: 1px solid;">
							  <td><img style="width:50px; height:50px; margin-right:5px" src="'.URL.'/style/game/champions/square/'.$gameInfo['championId'].'.png"> '.$convert->champId2Name($gameInfo['championId']).'</td>
							  <td>'.(int) @$gameInfo['stats']['championsKilled'].'/'.(int) @$gameInfo['stats']['numDeaths'].'/'.(int) @$gameInfo['stats']['assists'].'</td>
							  <td><b>'.$lang['matchtype.'.$gameInfo['gameType']].'</b> ('.$lang['map.'.$gameInfo['mapId']].')</td>
							  <td data-value="'.$gameInfo['createDate'].'">'.$lang['time.elapsed.ago'].' '.$core->timeElapsed($gameInfo['createDate']).'</td>
							</tr>';
						  } ?>
						  </tbody>
						</table>
					  </div>
					</div>
				  </div>
				  <?php
				}
				else
				{
					echo '<h1 style="text-align: center">'.$lang['profile.leagues.nomatches'].'</h1>
					<img style="width:100%;height:100%" src="'.URL.'/style/game/champions/splash/4_7.jpg">';
				}
				?>
					  </div>
					</div>
				<!-- end -->
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    <footer class="footer text-center"> <?php echo date('Y'); ?>  <?php echo $lang['footer.copy'] ?> </footer>
  </div>
</div>
<div id="reloaded" class="myadmin-alert myadmin-alert-img alert-info myadmin-alert-top-right"> <img src="<?php echo URL ?>/style/game/summoners/icons/<?php echo $summonerInfo['icon'] ?>.png" class="img" alt="img"><a href="#" class="closed">&times;</a><h4><?php echo $lang['profile.notify.reloaded'] ?></h4><?php echo str_replace('{{name}}','<b>'.$summonerInfo['name'].'</b>',$lang['profile.notify.reloaded.dsc']) ?></div></div>
<?php
require('kernel/template/scripts.tpl');
?>
<?php
  if($reloadStatus == true)
  {
	  echo '<script>$(function() {
        $("#reloaded").fadeToggle(350); setTimeout(function() {$("#reloaded").fadeToggle(350)}, 3000);
	});</script>';
  }
  ?>
<script type="text/javascript">
$( document ).ready(function() {
    var ctx6 = document.getElementById("playerPerspective").getContext("2d");
    var playerPerspective = {
        labels: ["<?php echo $lang['profile.chart.perspective.kills'].'", "'.$lang['profile.chart.perspective.deaths'].'", "'.$lang['profile.chart.perspective.assists'].'", "'.$lang['profile.chart.perspective.cs'].'", "'.$lang['profile.chart.perspective.winrate'].'", "'.$lang['profile.chart.perspective.tilt'] ?>"],
        datasets: [
            {
                label: "My Second dataset",
                fillColor: "rgba(97,100,193,0.8)",
                strokeColor: "rgba(97,100,193,1)",
                pointColor: "rgba(97,100,193,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(97,100,193,1)",
                data: [<?php 
				$kills = @round((($summonerInfo['games']['kills']/($summonerInfo['games']['kills']+$summonerInfo['games']['deaths']+$summonerInfo['games']['assists'])) * 100) * 2);
				($kills > 100) ? $kills = 100:null;
				$deaths = @round((($summonerInfo['games']['deaths']/($summonerInfo['games']['kills']+$summonerInfo['games']['deaths']+$summonerInfo['games']['assists'])) * 100) * 2);
				($deaths > 100) ? $deaths = 100:null;
				$assists = @round((($summonerInfo['games']['assists']/($summonerInfo['games']['kills']+$summonerInfo['games']['deaths']+$summonerInfo['games']['assists'])) * 100) * 2);
				($assists > 100) ? $assists = 100:null;
				$cs = @round((($summonerInfo['games']['cs']/(($summonerInfo['games']['kills']+$summonerInfo['games']['deaths']+$summonerInfo['games']['assists'])) * 100) / 13) * 2);
				($cs > 100) ? $cs = 100:null;
				$winrate = @round((100/($summonerInfo['games']['wins']+$summonerInfo['games']['losses']))*$summonerInfo['games']['wins']);
				($winrate > 100) ? $winrate = 100:null;
				$tilt = @round(($winrate+$kills+$deaths+$assists)/4);
				($tilt > 100) ? $tilt = 100:null;
				echo $kills.','.$deaths.','.$assists.','.$cs.','.$winrate.','.$tilt; ?>]
            }
        ]
    };
    var myRadarChart = new Chart(ctx6).Radar(playerPerspective, {
        scaleShowLine : true,
        angleShowLineOut : true,
        scaleShowLabels : false,
        scaleBeginAtZero : true,
        angleLineColor : "rgba(0,0,0,.1)",
        angleLineWidth : 1,
        pointLabelFontFamily : "'Arial'",
        pointLabelFontStyle : "normal",
        pointLabelFontSize : 13,
        pointLabelFontColor : "#666",
        pointDot : false,
        pointDotRadius : 5,
		tooltipCornerRadius: 2,
        pointDotStrokeWidth : 1,
        pointHitDetectionRadius : 20,
        datasetStroke : true,
        datasetStrokeWidth : 2,
        datasetFill : true,
		scaleOverride: true,
		scaleSteps: 5,
		scaleStepWidth: 20,
		scaleStartValue: 0,
        legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
        responsive: true,
		showTooltips: false
    });
    
});
</script>
</body>
</html>
