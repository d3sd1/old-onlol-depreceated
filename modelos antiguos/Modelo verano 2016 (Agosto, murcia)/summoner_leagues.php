<?php
require('kernel/core.php');

if(empty($_GET['summoner']))
{
	header('Location: '.$config['web.url'].'/?notify=error_summonernotset');
	die();
}
if(empty($_GET['region']))
{
	header('Location: '.$config['web.url'].'/?notify=error_regionnotset');
	die();
}
if($db->query('SELECT id FROM lol_summoners WHERE region="'.$_GET['region'].'" AND name="'.$_GET['summoner'].'"')->num_rows == 0)
{
	header('Location: '.$config['web.url'].'/summoner/'.$_GET['region'].'/'.$_GET['summoner']);
	die();
}
if(core::check_valid_region($_GET['region']) == false)
{
	header('Location: '.$config['web.url'].'/?notify=error_regionnotset');
	die();
}
$summonerInfoId = $db->query('SELECT summoner_id FROM lol_summoners WHERE region="'.$_GET['region'].'" AND name="'.$_GET['summoner'].'"')->fetch_row()[0];
	
	
	if($db->query('SELECT level FROM lol_summoners WHERE region="'.$_GET['region'].'" AND summoner_id='.$summonerInfoId)->fetch_row()[0] < 30)
	{
		header('Location: '.$config['web.url'].'/summoner/'.$_GET['region'].'/'.$_GET['summoner'].'?notify=leaguenolvl');
		die();
	}
	
	if($db->query('SELECT id FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summonerInfoId)->num_rows == 0)
	{
		$db->query('INSERT INTO lol_summoners_leagues (region,summonerId,updated) VALUES ("'.$_GET['region'].'",'.$summonerInfoId.',0)');
		$summoner_reload = true;
	}
	else
	{
		if($db->query('SELECT updated FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summonerInfoId)->fetch_row()[0] < (core::current_time()-($config['profile.leagues.reload.time']*1000)))
		{
			$summoner_reload = true;
		}
		else
		{
			$summoner_reload = false;
		}
	}
	
	
	if($summoner_reload == true)
	{
		$summonerLeagues = core::extjson(str_replace(array('{{region}}','{{summoner_id}}','{{riotapi}}'),array($_GET['region'],$summonerInfoId,$config['riot.api.key']),core::$api_url_summonerprofLeague));
		
		if($summonerLeagues == FALSE) //Is unranked
		{
			$db->query('UPDATE lol_summoners_leagues SET updated="'.core::current_time().'" WHERE region="'.$_GET['region'].'" AND summonerId='.$summonerInfoId);
		}
		else
		{
			foreach($summonerLeagues[$summonerInfoId] as $leagueCount => $leagueData)
			{
				if($leagueData['queue'] == 'RANKED_SOLO_5x5')
				{
					$summonersToMultipleInsert = null;
					$summonersToMultipleUpdate = null;
					foreach($leagueData['entries'] as $playerCount => $playerData)
					{
						($playerData['isHotStreak'] == false) ? $playerData['isHotStreak'] = 0:$playerData['isHotStreak'] = 1;
						($playerData['isVeteran'] == false) ? $playerData['isVeteran'] = 0:$playerData['isVeteran'] = 1;
						($playerData['isFreshBlood'] == false) ? $playerData['isFreshBlood'] = 0:$playerData['isFreshBlood'] = 1;
						($playerData['isInactive'] == false) ? $playerData['isInactive'] = 0:$playerData['isInactive'] = 1;
						(!empty($playerData['miniSeries'])) ? $playerData['miniSeries'] = addslashes(json_encode($playerData['miniSeries'])):$playerData['miniSeries'] = 'NOT_ENABLED';
						$playerMMR = core::summonerMMR($playerData['leaguePoints'],substr($leagueData['tier'],0,1),$playerData['division']);
						if($db->query('SELECT id FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$playerData['playerOrTeamId'])->num_rows == 0)
						{
							$summonersToMultipleInsert .= '("'.addslashes($leagueData['name']).'","'.$playerData['division'].'",'.$playerData['leaguePoints'].','.$playerData['wins'].','.$playerData['losses'].','.$playerData['isHotStreak'].','.$playerData['isVeteran'].','.$playerData['isFreshBlood'].','.$playerData['isInactive'].',"'.$playerData['playerOrTeamName'].'","'.substr($leagueData['tier'],0,1).'","'.core::current_time().'","'.$_GET['region'].'","'.$playerData['playerOrTeamId'].'","'.$playerData['miniSeries'].'",'.$playerMMR.'),';
						}
						else
						{
							$db->query('UPDATE lol_summoners_leagues SET mmr='.$playerMMR.',name="'.$leagueData['name'].'",division="'.$playerData['division'].'",lp='.$playerData['leaguePoints'].',wins='.$playerData['wins'].',losses='.$playerData['losses'].',isHotStreak='.$playerData['isHotStreak'].',isVeteran='.$playerData['isVeteran'].',isFreshBlood='.$playerData['isFreshBlood'].',isInactive='.$playerData['isInactive'].',summonerName="'.$playerData['playerOrTeamName'].'",tier="'.substr($leagueData['tier'],0,1).'",updated="'.core::current_time().'",miniseries="'.$playerData['miniSeries'].'" WHERE region="'.$_GET['region'].'" AND summonerId='.$playerData['playerOrTeamId']) or die($db->error);
						}
					}
					if($summonersToMultipleInsert != null)
					{
						$db->query('INSERT INTO lol_summoners_leagues (name,division,lp,wins,losses,isHotStreak,isVeteran,isFreshBlood,isInactive,summonerName,tier,updated,region,summonerId,miniseries,mmr) VALUES '.trim($summonersToMultipleInsert, ',')) or die($db->error);
					}
				}
			}
		}
	}

/* Now retrieve Data */
$summoner_info=$db->query('SELECT region,summoner_id,name,icon,revision,level,matches,quality FROM lol_summoners WHERE region="'.$_GET['region'].'" AND name="'.$_GET['summoner'].'"')->fetch_array();
$champdata = core::readjson('champs/full/'.$user_lang);
if($db->query('SELECT id FROM lol_summoners_champmastery WHERE summoner_region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id'])->num_rows > 0)
{
	$userMainChamp = $db->query('SELECT mainChamp FROM lol_summoners_champmastery WHERE summoner_region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id'])->fetch_row()[0];
	if($userMainChamp == 0)
	{
		$userMainChamp = $config['web.profile.defaultmainchamp'];
	}
}
else
{
	$userMainChamp = $config['web.profile.defaultmainchamp'];
}
if($db->query('SELECT id FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->num_rows > 0)
{
	$summoner_infoLeague = $db->query('SELECT mmr,tier,division FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->fetch_row();
}
else
{
	$summoner_infoLeague = false;
}
?>
<!DOCTYPE html>
<!--[if IE 9]>
	<html class="ie9 no-focus">
<![endif]-->
<!--[if gt IE 9]><!-->
	<html class="no-focus"> 
<!--<![endif]-->
<head>
	<?php echo template::basehead($lang['pageMetaTitleIndex']); ?>
	<link rel="stylesheet" id="css-main" href="<?php echo $config['web.url'] ?>/assets/js/plugins/datatables/jquery.dataTables.min.css">
</head>
<body>

<div id="page-container" class="sidebar-l sidebar-o side-scroll header-navbar-fixed <?php if(!empty($_COOKIE['onlol_sidebar']) && @$_COOKIE['onlol_sidebar'] == 'min') {echo 'sidebar-mini';} ?>">
	
	
	<?php echo template::sideBarRight(); ?>
	<?php echo template::headerNavBar($user_region); ?>
	<?php echo template::sideBar(null); ?>
	<!-- Start Summoner -->

<main id="main-container" style="background-image: url(<?php echo $config['web.url'] ?>/assets/game/champions/splash/<?php echo $champdata[$userMainChamp]['key'] ?>_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
	<?php echo template::profileHead($summoner_info['name'],$summoner_info['level'],$summoner_info['icon'],$summoner_info['matches'],$summoner_infoLeague[1],$summoner_infoLeague[2],$summoner_infoLeague[0],$summoner_info['quality'],$summoner_info['summoner_id'],$summoner_infoLeague) ?>
	<div class="content">
		<div class="row">
			<div class="block">
				<ul class="nav nav-tabs nav-tabs-alt nav-justified">
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>"><i class="si si-list"></i> <?php echo $lang['summonerProfileTabsMatchHistory'] ?></a>
					</li>
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/champmastery"><i class="si si-badge"></i> <?php echo $lang['summonerProfileTabsChampMastery'] ?></a>
					</li>
					<li class="active">
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/leagues"><i class="si si-trophy"></i> <?php echo $lang['summonerProfileTabsLeagues'] ?></a>
					</li>
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/teams"><i class="si si-users"></i> <?php echo $lang['summonerProfileTabsTeams'] ?></a>
					</li>
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/champs"><i class="si si-chemistry"></i> <?php echo $lang['summonerProfileTabsChamps'] ?></a>
					</li>
				</ul>
				<div class="block-content tab-content">
					<div class="tab-pane active">
					<?php
					if($db->query('SELECT id FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->num_rows > 0)
					{
						$summonerLeagueData = $db->query('SELECT name,division,tier FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->fetch_row();
						$leagueDataDivStatus[1] = null;$leagueDataDivStatus[2] = null;$leagueDataDivStatus[3] = null;$leagueDataDivStatus[4] = null;$leagueDataDivStatus[5] = null;
						$leagueDataDivStatusColor[1] = null;$leagueDataDivStatusColor[2] = null;$leagueDataDivStatusColor[3] = null;$leagueDataDivStatusColor[4] = null;$leagueDataDivStatusColor[5] = null;
						
						switch($summonerLeagueData[1])
						{
							case 'I':
							$leagueDataDivStatus[1] = 'active';
							break;
							case 'II':
							$leagueDataDivStatus[2] = 'active';
							break;
							case 'III':
							$leagueDataDivStatus[3] = 'active';
							$leagueDataDivStatusColor[3] = 'style="background-color: #87CEFA !important"';
							break;
							case 'IV':
							$leagueDataDivStatus[4] = 'active';
							break;
							case 'V':
							$leagueDataDivStatus[5] = 'active';
							break;
						}
						echo '<div class="block">
						<ul class="nav nav-tabs" data-toggle="tabs">
							<li class="'.$leagueDataDivStatus[1].'">
								<a '.$leagueDataDivStatusColor[1].' href="#leagueDivison1">I</a>
							</li>
							<li class="'.$leagueDataDivStatus[2].'">
								<a '.$leagueDataDivStatusColor[2].' href="#leagueDivison2">II</a>
							</li>
							<li class="'.$leagueDataDivStatus[3].'">
								<a '.$leagueDataDivStatusColor[3].' href="#leagueDivison3">III</a>
							</li>
							<li class="'.$leagueDataDivStatus[4].'">
								<a '.$leagueDataDivStatusColor[4].' href="#leagueDivison4">IV</a>
							</li>
							<li class="'.$leagueDataDivStatus[5].'">
								<a '.$leagueDataDivStatusColor[5].' href="#leagueDivison5">V</a>
							</li>
							<li class="pull-right">
							<a>'.$lang['leagueName_'.$summonerLeagueData[2]].'</a>
							</li>
						</ul>';
						
						
					echo '<div class="block-content tab-content">';
						for($i = 1; $i < 6; $i++)
						{
							echo '<div class="tab-pane '.$leagueDataDivStatus[$i].'" id="leagueDivison'.$i.'">
									<div class="table">
										<table class="table table-striped table-vcenter">
										<thead>
											<tr>
											<th class="text-center" style="width: 120px;"><i class="si si-user"></i></th>
												<th>'.$lang['summonerProfileLeaguesName'].'</th>
												<th class="text-center">'.$lang['summonerProfileLeaguesLP'].'</th>
												<th class="text-center">'.$lang['summonerProfileLeaguesWins'].'</th>
												<th class="text-center">'.$lang['summonerProfileLeaguesLosses'].'</th>
												<th class="text-center">'.$lang['summonerProfileLeaguesIsOnX'].'</th>
											</tr>
										</thead>
										<tbody>';
										switch($i)
										{
											case 1:
											$leagueDataDivisionFix = 'I';
											break;
											case 2:
											$leagueDataDivisionFix = 'II';
											break;
											case 3:
											$leagueDataDivisionFix = 'III';
											break;
											case 4:
											$leagueDataDivisionFix = 'IV';
											break;
											case 5:
											$leagueDataDivisionFix = 'V';
											break;
										}
										$leagueDataAllPlayers = $db->query('SELECT summonerName,lp,wins,losses,isHotStreak,isVeteran,isFreshBlood,isInactive,miniseries FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND division="'.$leagueDataDivisionFix.'" ORDER BY miniseries DESC,lp DESC');
										while($row = $leagueDataAllPlayers->fetch_row())
										{
											$leagueDataThisPlayerEmblems = null;
											($row[4] == 1) ? $leagueDataThisPlayerEmblems .= '<i class="fa fa-fire" data-toggle="popover" data-placement="left" data-content="'.$lang['profilePopoverLeagueSpreeDsc'].'" data-original-title="'.$lang['profilePopoverLeagueSpree'].'"></i>':null;
											($row[5] == 1) ? $leagueDataThisPlayerEmblems .= '<i class="si si-badge" data-toggle="popover" data-placement="left" data-content="'.$lang['profilePopoverLeagueVeteranDsc'].'" data-original-title="'.$lang['profilePopoverLeagueVeteran'].'"></i>':null;
											($row[6] == 1) ? $leagueDataThisPlayerEmblems .= '<i class="fa fa-star" data-toggle="popover" data-placement="left" data-content="'.$lang['profilePopoverLeagueNoobDsc'].'" data-original-title="'.$lang['profilePopoverLeagueNoob'].'"></i>':null;
											($row[7] == 1) ? $leagueDataThisPlayerEmblems .= '<i class="si si-close" data-toggle="popover" data-placement="left" data-content="'.$lang['profilePopoverLeagueInactiveDsc'].'" data-original-title="'.$lang['profilePopoverLeagueInactive'].'"></i>':null;
											
											if($row[8] != 'NOT_ENABLED')
											{
												$row[8] = json_decode($row[8],true);
												$miniSeriesAndLp = str_replace(array('N','L','W'),array('<i class="fa fa-circle-o" data-toggle="popover" data-placement="top" data-content="'.$lang['profilePopoverLeagueMiniseriesNotPlayed'].'" data-original-title="'.$lang['profilePopoverLeagueMiniseries'].'"></i>','<i class="fa fa-times-circle-o" data-toggle="popover" data-placement="top" data-content="'.$lang['profilePopoverLeagueMiniseriesLost'].'" data-original-title="'.$lang['profilePopoverLeagueMiniseries'].'"></i>','<i class="fa fa-check-circle-o" data-toggle="popover" data-placement="top" data-content="'.$lang['profilePopoverLeagueMiniseriesWon'].'" data-original-title="'.$lang['profilePopoverLeagueMiniseries'].'"></i>'),$row[8]['progress']);
											}
											else
											{
												$miniSeriesAndLp = $row[1];
											}
											echo '<tr>
											<td class="text-center">
											<img draggable="false" class="lazy img-avatar img-avatar48" data-original="http://avatar.leagueoflegends.com/euw/'.core::format_summonername($row[0]).'.png" alt="">
											</td>
											<td class="font-w600"><a href="'.$config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($row[0]).'">'.$row[0].'</a></td>
											<td class="text-center">'.$miniSeriesAndLp.'</td>
											<td class="text-center"><div class="leaguesGameWon">'.$row[2].'</div></td>
											<td class="text-center"><div class="leaguesGameLost">'.$row[3].'</div></td>
											<td class="text-center">'.$leagueDataThisPlayerEmblems.'</td>
											</tr>';
										}
										echo '</tbody>
										</table>
									</div>
							</div>';
						}
						echo '</div>
					</div>';
					}
					else
					{
						echo '<div class="col-sm-6 col-sm-offset-3">
								<h1 class="font-s128 font-w300 text-modern animated zoomInDown">'.$lang['profileLeagueErrorTitle'].'</h1>
								<h2 class="h3 font-w300 push-50 animated fadeInUp">'.$lang['profileLeagueErrorContent'].'</h2>
							</div>';
					}
					?>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<div class="modal" id="loadNewAjax" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="block block-themed block-transparent remove-margin-b">
				<div class="block-header bg-primary-dark">
					<h3 class="block-title"><?php echo $lang['profileModalLoadingTitle'] ?></h3>
				</div>
				<div class="block-content"><center><i class="fa fa-cog fa-5x fa-spin"></i><p> <?php echo $lang['profileModalLoading'] ?></p></center>
				</div>
			</div>
		</div>
	</div>
</div>
	<!-- End Summoner -->
	<?php echo template::footer(); ?>
</div>
<?php echo template::scripts(); ?>
<script src="<?php echo $config['web.url'] ?>/assets/js/lazyload.min.js"></script>
<script>
$(function() {
    $("img.lazy").lazyload({threshold : 200});
});</script>
<?php if(@$summoner_reload == true)
{
	echo '<script>$.notify("'.$lang['ajaxLeagueLoaded'].'", "info");</script>';
} ?>
</body>
</html>