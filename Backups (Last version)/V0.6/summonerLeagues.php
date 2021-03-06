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
              <li class="tab"><a href="<?php echo URL ?>/summoner/<?php echo $summonerRegion ?>/<?php echo $summonerInfo['name'] ?>"> <span class="visible-xs"><i class="fa fa-history"></i></span> <span class="hidden-xs"><?php echo $lang['profile.matchhistory'] ?></span> </a> </li>
              <li class="active tab"><a> <span class="visible-xs"><i class="fa fa-trophy"></i></span> <span class="hidden-xs"><?php echo $lang['profile.leagues'] ?></span> </a> </li>
              <li class="tab"><a href="<?php echo URL ?>/summoner/<?php echo $summonerRegion ?>/<?php echo $summonerInfo['name'] ?>/stats"> <span class="visible-xs"><i class="fa fa-line-chart"></i></span> <span class="hidden-xs"><?php echo $lang['profile.stats'] ?></span> </a> </li>
              <li class="tab"><a href="<?php echo URL ?>/summoner/<?php echo $summonerRegion ?>/<?php echo $summonerInfo['name'] ?>/champmastery"> <span class="visible-xs"><i class="fa fa-bar-chart"></i></span> <span class="hidden-xs"><?php echo $lang['profile.champs'] ?></span> </a> </li>
			  <li class="tab"><a href="<?php echo URL ?>/summoner/<?php echo $summonerRegion ?>/<?php echo $summonerInfo['name'] ?>/runes"> <span class="visible-xs"><i class="fa fa-balance-scale"></i></span> <span class="hidden-xs"><?php echo $lang['profile.runes'] ?></span> </a> </li>
			  <li class="tab"><a href="<?php echo URL ?>/summoner/<?php echo $summonerRegion ?>/<?php echo $summonerInfo['name'] ?>/masteries"> <span class="visible-xs"><i class="fa fa-balance-scale"></i></span> <span class="hidden-xs"><?php echo $lang['profile.masteries'] ?></span> </a> </li>
            </ul>
            <div class="tab-content">
                <!-- start -->
				<?php
				if(strtoupper($summonerInfo['leagues']['RANKED_SOLO_5x5']['tier']) != 'UNRANKED')
				{
					foreach($summonerLeagueApi as $searchLeagueKey => $searchLeague)
					{
						if($searchLeague['queue'] == 'RANKED_SOLO_5x5')
						{
							$summonerLeaguePage = $summonerLeagueApi[$searchLeagueKey];
							foreach ($summonerLeaguePage['entries'] as $key => $row) {
								if($row['division'] == $summonerInfo['leagues']['RANKED_SOLO_5x5']['division'])
								{
									if(array_key_exists('miniSeries',$row))
									{
										$miniSeriesPoints = 0;
										for($i = 0;$i < strlen($row['miniSeries']['progress']);$i++)
										{
											$lastChar = ($i+1);
											$gameStatus = substr($row['miniSeries']['progress'],$i,$lastChar);
											if($gameStatus == 'W')
											{
												$miniSeriesPoints += 20;
											}
											elseif($gameStatus == 'L')
											{
												$miniSeriesPoints += 10;
											}
											elseif($gameStatus == 'N')
											{
												$miniSeriesPoints += 5;
											}
										}
										$aux[$key] = ($row['leaguePoints']+$miniSeriesPoints);
									}
									else
									{
										$aux[$key] = $row['leaguePoints'];
									}
								}
								else
								{
									unset($summonerLeaguePage['entries'][$key]);
								}
							}
							array_multisort($aux, SORT_DESC, $summonerLeaguePage['entries']);
							$leaguePageSummonerRow = array_search($summonerInfo['id'],array_column($summonerLeaguePage['entries'], 'playerOrTeamId'));
						}
					}
					if($leaguePageSummonerRow < $config['web.summoner.leagues.summonerstoshow'])
					{
						$inconsistantRowsBot = $config['web.summoner.leagues.summonerstoshow']-$leaguePageSummonerRow;
					}
					else
					{
						$inconsistantRowsBot = 0;
					}
					if($leaguePageSummonerRow > (count($summonerLeaguePage['entries']) - $config['web.summoner.leagues.summonerstoshow']))
					{
						$inconsistantRowsTop = count($summonerLeaguePage['entries'])-$config['web.summoner.leagues.summonerstoshow'];
					}
					else
					{
						$inconsistantRowsTop = 0;
					}
					?>
					<div class="row">
						<div class="col-lg-12">
						  <div class="white-box">
						  <h3 class="box-title m-b-0"><?php echo $lang['league.'.$summonerInfo['leagues']['RANKED_SOLO_5x5']['tier']].' '.$summonerInfo['leagues']['RANKED_SOLO_5x5']['division'].' - ';$divisionTrans = explode(' ',str_replace('\'s',null,strtolower($summonerLeaguePage['name'])));
						  if(array_key_exists('division.name.'.$divisionTrans[1],$lang)) { echo str_replace('{{champ}}',ucwords($divisionTrans[0]),$lang['division.name.'.$divisionTrans[1]]); } else {echo $summonerLeaguePage['name'];}?></h3>
							<table class="tablesaw table-bordered table-hover tablesaw-sortable-descending table" data-tablesaw-mode="columntoggle">
							  <thead>
								<tr>
								  <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="3"><?php echo $lang['summoner.leagues.table.id'] ?></th>
								  <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="persist"><?php echo $lang['summoner.leagues.table.name'] ?></th>
								  <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="2"><?php echo $lang['summoner.leagues.table.emblems'] ?></th>
								  <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="1"><?php echo $lang['summoner.leagues.table.winrate'] ?></th>
								  <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="4"><?php echo $lang['summoner.leagues.table.progess'] ?></th>
								</tr>
							  </thead>
							  <tbody>
							  <?php
								for($i = ($config['web.summoner.leagues.summonerstoshow']+$inconsistantRowsTop);$i > 0;$i--)
								{
									$actualForSummoner = ($leaguePageSummonerRow-$i);
									if(array_key_exists($actualForSummoner,$summonerLeaguePage['entries']))
									{
										if($summonerLeaguePage['entries'][$actualForSummoner]['playerOrTeamName'] == $summonerInfo['name'])
										{
											break;
										}
										
										if(empty($summonerLeaguePage['entries'][$actualForSummoner]['miniSeries']))
										{
											$progress = $summonerLeaguePage['entries'][$actualForSummoner]['leaguePoints'].$lang['profile.matchhistory.chart.lp.short'];
										}
										else
										{
											$progress = '<a style="color:black;" data-toggle="tooltip" data-original-title="'.$lang['profile.leagues.promo.prev'].'">'.str_replace(array('N','W','L'),array('<i style="color:black;" class="fa fa-circle-o"></i>','<i style="color:#67b125;" class="fa fa-check-circle-o"></i>','<i style="color:#b12525;" class="fa fa-times-circle-o"></i>'),$summonerLeaguePage['entries'][$actualForSummoner]['miniSeries']['progress']).'</a>';
										}
										echo '<tr>
										  <td>'.($actualForSummoner+1).'</td>
										  <td class="title">
											<a href="'.URL.'/summoner/'.$core->regionDepure($_GET['region']).'/'.$summonerLeaguePage['entries'][$actualForSummoner]['playerOrTeamName'].'"><img width="30px" src="http://avatar.leagueoflegends.com/euw/'.$summonerLeaguePage['entries'][$actualForSummoner]['playerOrTeamName'].'.png"> '.$summonerLeaguePage['entries'][$actualForSummoner]['playerOrTeamName'].'</a>
										  </td>
										  <td style="text-align:center">
										  
										  <a data-toggle="tooltip" data-original-title="'.($summonerLeaguePage['entries'][$actualForSummoner]['isVeteran'] == true ? $lang['summoner.profile.leagues.tooltip.veteran']:$lang['summoner.profile.leagues.tooltip.notveteran']).'" href="javascript:void(0)"><i style="'.($summonerLeaguePage['entries'][$actualForSummoner]['isVeteran'] == true ? 'color:black':'color:#D8D8D8').'" class="fa fa-trophy"></i> </a>
										  
										  <a data-toggle="tooltip" data-original-title="'.($summonerLeaguePage['entries'][$actualForSummoner]['isHotStreak'] == true ? $lang['summoner.profile.leagues.tooltip.streak']:$lang['summoner.profile.leagues.tooltip.notstreak']).'" href="javascript:void(0)"><i style="'.($summonerLeaguePage['entries'][$actualForSummoner]['isHotStreak'] == true ? 'color:black':'color:#D8D8D8').'" class="fa fa-fire"></i> </a>
										  
										  <a data-toggle="tooltip" data-original-title="'.($summonerLeaguePage['entries'][$actualForSummoner]['isFreshBlood'] == true ? $lang['summoner.profile.leagues.tooltip.noob']:$lang['summoner.profile.leagues.tooltip.notnoob']).'" href="javascript:void(0)"><i style="'.($summonerLeaguePage['entries'][$actualForSummoner]['isFreshBlood'] == true ? 'color:black':'color:#D8D8D8').'" class="fa fa-star"></i> </a>
										  
										  <a data-toggle="tooltip" data-original-title="'.($summonerLeaguePage['entries'][$actualForSummoner]['isInactive'] == true ? $lang['summoner.profile.leagues.tooltip.inactive']:$lang['summoner.profile.leagues.tooltip.notinactive']).'" href="javascript:void(0)"><i style="'.($summonerLeaguePage['entries'][$actualForSummoner]['isInactive'] == true ? 'color:black':'color:#D8D8D8').'" class="fa fa-ellipsis-h"></i> </a>

										  <a data-toggle="tooltip" data-original-title="'.str_replace('{{mode}}',$lang['playstyle.'.$summonerLeaguePage['entries'][$actualForSummoner]['playstyle']],$lang['summoner.profile.leagues.tooltip.playstyle']).'">';
										switch($summonerLeaguePage['entries'][$actualForSummoner]['playstyle'])
										{
										case 'NONE':
											echo '<i class="fa fa-user" style="color: #D8D8D8"></i>';
											break;
											case 'SOLO':
											echo '<i class="fa fa-user" style="color: black"></i>';
											break;
											case 'SQUAD':
											echo '<i class="fa fa-user-secret" style="color: black"></i>';
											break;
											case 'TEAM':
											echo '<i class="fa fa-users" style="color: black"></i>';
											break;
										} echo '</a>
										  </td>
										  <td><b>'.round((100/($summonerLeaguePage['entries'][$actualForSummoner]['wins']+$summonerLeaguePage['entries'][$actualForSummoner]['losses']))*$summonerLeaguePage['entries'][$actualForSummoner]['wins']).'%</b> (<b style="color:#67b125">'.$summonerLeaguePage['entries'][$actualForSummoner]['wins'].$lang['profile.matchhistory.chart.win.short'].'</b>/<b style="color:#b12525">'.$summonerLeaguePage['entries'][$actualForSummoner]['losses'].$lang['profile.matchhistory.chart.lose.short'].'</b>)</td>
										  <td style="text-align: center;">'.$progress.'</td>
										</tr>';
									}
								}
								
								if(empty($summonerLeaguePage['entries'][$leaguePageSummonerRow]['miniSeries']))
								{
									$progressSummoner = $summonerLeaguePage['entries'][$leaguePageSummonerRow]['leaguePoints'].$lang['profile.matchhistory.chart.lp.short'];
								}
								else
								{
									$progressSummoner = '<a style="color:black;" data-toggle="tooltip" data-original-title="'.$lang['profile.leagues.promo.prev'].'">'.str_replace(array('N','W','L'),array('<i style="color:black;" class="fa fa-circle-o"></i>','<i style="color:#67b125;" class="fa fa-check-circle-o"></i>','<i style="color:#b12525;" class="fa fa-times-circle-o"></i>'),$summonerLeaguePage['entries'][$leaguePageSummonerRow]['miniSeries']['progress']).'</a>';
								}
								?>
								<tr style="background-color:#CEF6F5">
								  <td><?php echo ($leaguePageSummonerRow+1) ?></td>
								  <td class="title">
									<a href="javascript:void(0)"><img width="30px" src="http://avatar.leagueoflegends.com/euw/<?php echo $summonerInfo['name'] ?>.png"> <?php echo $summonerInfo['name'] ?></a>
								  </td>
								  <td style="text-align:center">
										  
									<a data-toggle="tooltip" data-original-title="<?php if($summonerLeaguePage['entries'][$leaguePageSummonerRow]['isVeteran'] == true) { echo $lang['summoner.profile.leagues.tooltip.veteran']; } else { echo $lang['summoner.profile.leagues.tooltip.notveteran']; } ?>" href="javascript:void(0)"><i style="<?php if($summonerLeaguePage['entries'][$leaguePageSummonerRow]['isVeteran'] == true) { echo 'color:black'; } else { echo 'color:#D8D8D8'; } ?>" class="fa fa-trophy"></i> </a>
									
									<a data-toggle="tooltip" data-original-title="<?php if($summonerLeaguePage['entries'][$leaguePageSummonerRow]['isHotStreak'] == true) { echo $lang['summoner.profile.leagues.tooltip.streak']; } else { echo $lang['summoner.profile.leagues.tooltip.notstreak']; } ?>" href="javascript:void(0)"><i style="<?php if($summonerLeaguePage['entries'][$leaguePageSummonerRow]['isHotStreak'] == true) { echo 'color:black'; } else { echo 'color:#D8D8D8'; } ?>" class="fa fa-fire"></i> </a>
									
									<a data-toggle="tooltip" data-original-title="<?php if($summonerLeaguePage['entries'][$leaguePageSummonerRow]['isFreshBlood'] == true) { echo $lang['summoner.profile.leagues.tooltip.noob']; } else { echo $lang['summoner.profile.leagues.tooltip.notnoob']; } ?>" href="javascript:void(0)"><i style="<?php if($summonerLeaguePage['entries'][$leaguePageSummonerRow]['isFreshBlood'] == true) { echo 'color:black'; } else { echo 'color:#D8D8D8'; } ?>" class="fa fa-star"></i> </a>
									
									<a data-toggle="tooltip" data-original-title="<?php if($summonerLeaguePage['entries'][$leaguePageSummonerRow]['isInactive'] == true) { echo $lang['summoner.profile.leagues.tooltip.inactive']; } else { echo $lang['summoner.profile.leagues.tooltip.notinactive']; } ?>" href="javascript:void(0)"><i style="<?php if($summonerLeaguePage['entries'][$leaguePageSummonerRow]['isInactive'] == true) { echo 'color:black'; } else { echo 'color:#D8D8D8'; } ?>" class="fa fa-ellipsis-h"></i> </a>

										  <a data-toggle="tooltip" data-original-title="<?php echo str_replace('{{mode}}',$lang['playstyle.'.$summonerLeaguePage['entries'][$leaguePageSummonerRow]['playstyle']],$lang['summoner.profile.leagues.tooltip.playstyle']) ?>">
										<?php
										switch($summonerLeaguePage['entries'][$leaguePageSummonerRow]['playstyle'])
										{
										case 'NONE':
											echo '<i class="fa fa-user" style="color: #D8D8D8"></i>';
											break;
											case 'SOLO':
											echo '<i class="fa fa-user" style="color: black"></i>';
											break;
											case 'SQUAD':
											echo '<i class="fa fa-user-secret" style="color: black"></i>';
											break;
											case 'TEAM':
											echo '<i class="fa fa-users" style="color: black"></i>';
											break;
										} ?></a>
										  </td>
								  <td><b><?php echo round((100/($summonerLeaguePage['entries'][$leaguePageSummonerRow]['wins']+$summonerLeaguePage['entries'][$leaguePageSummonerRow]['losses']))*$summonerLeaguePage['entries'][$leaguePageSummonerRow]['wins']).'%</b> (<b style="color:#67b125">'.$summonerLeaguePage['entries'][$leaguePageSummonerRow]['wins'].$lang['profile.matchhistory.chart.win.short'].'</b>/<b style="color:#b12525">'.$summonerLeaguePage['entries'][$leaguePageSummonerRow]['losses'].$lang['profile.matchhistory.chart.lose.short'] ?></b>)</td>
								  <td style="text-align: center;"><?php echo $progressSummoner ?></td>
								</tr>
								<?php
								$isum = 0;
								for($i = ($config['web.summoner.leagues.summonerstoshow']+$inconsistantRowsBot);$i > 0;$i--)
								{
									$isum++;
									$actualForSummoner = ($leaguePageSummonerRow+$isum);
									if(array_key_exists($actualForSummoner,$summonerLeaguePage['entries']))
									{
										if($summonerLeaguePage['entries'][$actualForSummoner]['playerOrTeamName'] == $summonerInfo['name'])
										{
											break;
										}
										
										if(empty($summonerLeaguePage['entries'][$actualForSummoner]['miniSeries']))
										{
											$progress = $summonerLeaguePage['entries'][$actualForSummoner]['leaguePoints'].$lang['profile.matchhistory.chart.lp.short'];
										}
										else
										{
											$progress = '<a style="color:black;" data-toggle="tooltip" data-original-title="'.$lang['profile.leagues.promo.prev'].'">'.str_replace(array('N','W','L'),array('<i style="color:black;" class="fa fa-circle-o"></i>','<i style="color:#67b125;" class="fa fa-check-circle-o"></i>','<i style="color:#b12525;" class="fa fa-times-circle-o"></i>'),$summonerLeaguePage['entries'][$actualForSummoner]['miniSeries']['progress']).'</a>';
										}
										echo '<tr>
										  <td>'.($actualForSummoner+1).'</td>
										  <td class="title">
											<a href="'.URL.'/summoner/'.$summonerRegion.'/'.$summonerLeaguePage['entries'][$actualForSummoner]['playerOrTeamName'].'"><img width="30px" src="http://avatar.leagueoflegends.com/euw/'.$summonerLeaguePage['entries'][$actualForSummoner]['playerOrTeamName'].'.png"> '.$summonerLeaguePage['entries'][$actualForSummoner]['playerOrTeamName'].'</a>
										  </td>
										  <td style="text-align:center">
										  
										  <a data-toggle="tooltip" data-original-title="'.($summonerLeaguePage['entries'][$actualForSummoner]['isVeteran'] == true ? $lang['summoner.profile.leagues.tooltip.veteran']:$lang['summoner.profile.leagues.tooltip.notveteran']).'" href="javascript:void(0)"><i style="'.($summonerLeaguePage['entries'][$actualForSummoner]['isVeteran'] == true ? 'color:black':'color:#D8D8D8').'" class="fa fa-trophy"></i> </a>
										  
										  <a data-toggle="tooltip" data-original-title="'.($summonerLeaguePage['entries'][$actualForSummoner]['isHotStreak'] == true ? $lang['summoner.profile.leagues.tooltip.streak']:$lang['summoner.profile.leagues.tooltip.notstreak']).'" href="javascript:void(0)"><i style="'.($summonerLeaguePage['entries'][$actualForSummoner]['isHotStreak'] == true ? 'color:black':'color:#D8D8D8').'" class="fa fa-fire"></i> </a>
										  
										  <a data-toggle="tooltip" data-original-title="'.($summonerLeaguePage['entries'][$actualForSummoner]['isFreshBlood'] == true ? $lang['summoner.profile.leagues.tooltip.noob']:$lang['summoner.profile.leagues.tooltip.notnoob']).'" href="javascript:void(0)"><i style="'.($summonerLeaguePage['entries'][$actualForSummoner]['isFreshBlood'] == true ? 'color:black':'color:#D8D8D8').'" class="fa fa-star"></i> </a>
										  
										  <a data-toggle="tooltip" data-original-title="'.($summonerLeaguePage['entries'][$actualForSummoner]['isInactive'] == true ? $lang['summoner.profile.leagues.tooltip.inactive']:$lang['summoner.profile.leagues.tooltip.notinactive']).'" href="javascript:void(0)"><i style="'.($summonerLeaguePage['entries'][$actualForSummoner]['isInactive'] == true ? 'color:black':'color:#D8D8D8').'" class="fa fa-ellipsis-h"></i> </a>

										  <a data-toggle="tooltip" data-original-title="'.str_replace('{{mode}}',$lang['playstyle.'.$summonerLeaguePage['entries'][$actualForSummoner]['playstyle']],$lang['summoner.profile.leagues.tooltip.playstyle']).'">';
										switch($summonerLeaguePage['entries'][$actualForSummoner]['playstyle'])
										{
										case 'NONE':
											echo '<i class="fa fa-user" style="color: #D8D8D8"></i>';
											break;
											case 'SOLO':
											echo '<i class="fa fa-user" style="color: black"></i>';
											break;
											case 'SQUAD':
											echo '<i class="fa fa-user-secret" style="color: black"></i>';
											break;
											case 'TEAM':
											echo '<i class="fa fa-users" style="color: black"></i>';
											break;
										} echo '</a>
										  </td>
										  <td><b>'.round((100/($summonerLeaguePage['entries'][$actualForSummoner]['wins']+$summonerLeaguePage['entries'][$actualForSummoner]['losses']))*$summonerLeaguePage['entries'][$actualForSummoner]['wins']).'%</b> (<b style="color:#67b125">'.$summonerLeaguePage['entries'][$actualForSummoner]['wins'].$lang['profile.matchhistory.chart.win.short'].'</b>/<b style="color:#b12525">'.$summonerLeaguePage['entries'][$actualForSummoner]['losses'].$lang['profile.matchhistory.chart.lose.short'].'</b>)</td>
										  <td style="text-align: center;">'.$progress.'</td>
										</tr>';
									}
								}
								?>
							  </tbody>
							</table>
						  </div>
						</div>
					  </div>
					<!-- end -->
					<?php
				}
				else
				{
					echo '<h1 style="text-align: center">'.$lang['profile.leagues.noleagues'].'</h1>
					<img style="width:100%;height:100%" src="'.URL.'/style/game/champions/splash/1_10.jpg">';
				}
				?>
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
