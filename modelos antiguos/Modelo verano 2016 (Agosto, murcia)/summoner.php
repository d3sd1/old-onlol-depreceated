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
if(core::check_valid_region($_GET['region']) == TRUE)
{
	if($db->query('SELECT id FROM lol_summoners WHERE region="'.$_GET['region'].'" AND name="'.$_GET['summoner'].'"')->num_rows == 0)
	{
		$summoner_json_info=core::extjson(str_replace(array('{{region}}','{{summoner_name}}','{{riotapi}}'),array($_GET['region'],core::format_summonername($_GET['summoner']),$config['riot.api.key']),core::$api_url_summonerprofname));
		if($summoner_json_info == FALSE)
		{
			header('Location: '.$config['web.url'].'/?notify=error_summonernotfoundbyregion&summoner='.$_GET['summoner'].'&region='.$_GET['region']);
			die();
		}
		else
		{
			if($db->query('SELECT id FROM lol_summoners WHERE region="'.$_GET['region'].'" AND summoner_id='.$summoner_json_info[str_replace('%20',null,core::format_summonername($_GET['summoner']))]['id'])->num_rows == 0)
			{
				$db->query('INSERT INTO lol_summoners (summoner_id,name,icon,revision,level,region,updated) VALUES ("'.$summoner_json_info[str_replace('%20',null,core::format_summonername($_GET['summoner']))]['id'].'","'.$summoner_json_info[str_replace('%20',null,core::format_summonername($_GET['summoner']))]['name'].'","'.$summoner_json_info[str_replace('%20',null,core::format_summonername($_GET['summoner']))]['profileIconId'].'","'.$summoner_json_info[str_replace('%20',null,core::format_summonername($_GET['summoner']))]['revisionDate'].'","'.$summoner_json_info[str_replace('%20',null,core::format_summonername($_GET['summoner']))]['summonerLevel'].'","'.$_GET['region'].'","'.core::current_time().'")') or die('<script>console.log(\'Error '.addslashes($db->error).'\')</script>');
			}
			else
			{
				$db->query('UPDATE lol_summoners SET name="'.$summoner_json_info[str_replace('%20',null,core::format_summonername($_GET['summoner']))]['name'].'",icon='.$summoner_json_info[str_replace('%20',null,core::format_summonername($_GET['summoner']))]['profileIconId'].',revision="'.$summoner_json_info[str_replace('%20',null,core::format_summonername($_GET['summoner']))]['revisionDate'].'",level='.$summoner_json_info[str_replace('%20',null,core::format_summonername($_GET['summoner']))]['summonerLevel'].',updated="'.core::current_time().'" WHERE region="'.$_GET['region'].'" AND summoner_id='.$summoner_json_info[str_replace('%20',null,core::format_summonername($_GET['summoner']))]['id']);
			}
		}
		$summoner_reload=true;
	}
	else
	{
		if($db->query('SELECT updated FROM lol_summoners WHERE region="'.$_GET['region'].'" AND name="'.$_GET['summoner'].'"')->fetch_row()[0] < (core::current_time()-($config['profile.reload.time']*1000)))
		{
			$summoner_json_id = $db->query('SELECT summoner_id FROM lol_summoners WHERE name="'.$_GET['summoner'].'"')->fetch_row()[0];
			$summoner_json_info=core::extjson(str_replace(array('{{region}}','{{summoner_id}}','{{riotapi}}'),array($_GET['region'],$summoner_json_id,$config['riot.api.key']),core::$api_url_summonerprof));
			$db->query('UPDATE lol_summoners SET name="'.$summoner_json_info[$summoner_json_id]['name'].'",icon="'.$summoner_json_info[$summoner_json_id]['profileIconId'].'",revision="'.$summoner_json_info[$summoner_json_id]['revisionDate'].'",level="'.$summoner_json_info[$summoner_json_id]['summonerLevel'].'",updated='.core::current_time().' WHERE summoner_id='.$summoner_json_id.' AND region="'.$_GET['region'].'"');
			$summoner_reload=true;
		}
		else
		{
			$summoner_reload=false;
		}
	}
}
else
{
	header('Location: '.$config['web.url'].'/?notify=error_regionnotset');
	die();
}

/* Now retrieve Data */
$summoner_info=$db->query('SELECT region,summoner_id,name,icon,revision,level,matches,quality FROM lol_summoners WHERE region="'.$_GET['region'].'" AND name="'.$_GET['summoner'].'"')->fetch_array();
$champdata = core::readjson('champs/full/'.$user_lang);

/* Recent games */
if(@$summoner_reload == true)
{
	$recent_matches = core::extjson(str_replace(array('{{region}}','{{summoner_id}}','{{riotapi}}'),array($_GET['region'],$summoner_info['summoner_id'],$config['riot.api.key']),core::$api_url_summonerrecentmatches));
	if(is_array($recent_matches) && @array_key_exists('games',$recent_matches))
	{
		$matchParticipantsAddIds = array();
		foreach($recent_matches['games'] as $matchcount => $matchdata)
		{
			if($db->query('SELECT id FROM lol_matches WHERE region="'.strtoupper($_GET['region']).'" AND gameId='.$matchdata['gameId'])->num_rows == 0)
			{
				$matchPlayerIds = array();
				$matchParticipants = array();
				if(array_key_exists('fellowPlayers',$matchdata))
				{
					foreach($matchdata['fellowPlayers'] as $playercount => $playerdata)
					{
						$matchParticipants[$playerdata['summonerId']] = array();
						$matchParticipants[$playerdata['summonerId']]['team'] = $playerdata['teamId'];
						$matchParticipants[$playerdata['summonerId']]['champ'] = $playerdata['championId'];
						$matchParticipants[$playerdata['summonerId']]['dataLoaded'] = false;
						if($db->query('SELECT id FROM lol_summoners WHERE region="'.$_GET['region'].'" AND summoner_id='.$playerdata['summonerId'])->num_rows == 0)
						{
							$matchGameIdString = ''.$matchdata['gameId'].'';
							if(!array_key_exists($matchGameIdString,$matchParticipantsAddIds))
							{
								$matchParticipantsAddIds[$matchGameIdString] = array();
							}
							$matchParticipantsAddIds[$matchGameIdString][] = $playerdata['summonerId'];
						}
						
						$matchPlayerIds[$playerdata['summonerId']] = $playerdata['summonerId'];
					}
				}
				$matchParticipants[$summoner_info['summoner_id']] = array();
				$matchParticipants[$summoner_info['summoner_id']]['team'] = $matchdata['teamId'];
				$matchParticipants[$summoner_info['summoner_id']]['champ'] = $matchdata['championId'];
				$matchParticipants[$summoner_info['summoner_id']]['dataLoaded'] = true;
				$matchParticipants[$summoner_info['summoner_id']]['spell1'] = $matchdata['spell1'];
				$matchParticipants[$summoner_info['summoner_id']]['spell2'] = $matchdata['spell2'];
				$matchParticipants[$summoner_info['summoner_id']]['ipEarned'] = $matchdata['ipEarned'];
				$matchParticipants[$summoner_info['summoner_id']]['stats'] = $matchdata['stats'];
				
				$matchPlayerIds[$summoner_info['summoner_id']] = $summoner_info['summoner_id'];
				
				$matchParticipants = addslashes(json_encode($matchParticipants));
				
				if($matchdata['stats']['win'] == true)
				{
					$matchWinnerTeam = $matchdata['teamId'];
				}
				else
				{
					if($matchdata['teamId'] == 100)
					{
						$matchWinnerTeam = 200;
					}
					else
					{
						$matchWinnerTeam = 100;
					}
				}
				
				$matchPlayerIds = implode(';',$matchPlayerIds);
				($matchdata['invalid'] == false) ? $matchdata['invalid'] = 'false':$matchdata['invalid'] = 'true';
				
				$db->query('INSERT INTO lol_matches (gameId,playerIds,fullData,region,invalid,gameType,mapId,createDate,participants,teams,timeline,winnerTeam) VALUES ('.$matchdata['gameId'].',"'.$matchPlayerIds.'","false","'.strtoupper($_GET['region']).'","'.$matchdata['invalid'].'","'.$matchdata['subType'].'","'.$matchdata['mapId'].'","'.$matchdata['createDate'].'","'.$matchParticipants.'","NOT_SET","NOT_SET","'.$matchWinnerTeam.'")');
				$db->query('UPDATE lol_summoners SET matches=matches+1 WHERE region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id']);
			}
			else
			{
				$prevParticipantsData = json_decode($db->query('SELECT participants FROM lol_matches WHERE region="'.strtoupper($_GET['region']).'" AND gameId='.$matchdata['gameId'])->fetch_row()[0],true);
				if($db->query('SELECT fullData FROM lol_matches WHERE region="'.strtoupper($_GET['region']).'" AND gameId='.$matchdata['gameId'])->fetch_row()[0] == 'false' OR $prevParticipantsData[$summoner_info['summoner_id']]['dataLoaded'] != true)
				{
					$matchParticipants = array();
					$matchParticipants[$summoner_info['summoner_id']] = array();
					$matchParticipants[$summoner_info['summoner_id']]['team'] = $matchdata['teamId'];
					$matchParticipants[$summoner_info['summoner_id']]['champ'] = $matchdata['championId'];
					$matchParticipants[$summoner_info['summoner_id']]['dataLoaded'] = true;
					$matchParticipants[$summoner_info['summoner_id']]['spell1'] = $matchdata['spell1'];
					$matchParticipants[$summoner_info['summoner_id']]['spell2'] = $matchdata['spell2'];
					$matchParticipants[$summoner_info['summoner_id']]['ipEarned'] = $matchdata['ipEarned'];
					if(array_key_exists('stats',$prevParticipantsData[$summoner_info['summoner_id']]))
					{
						$matchParticipants[$summoner_info['summoner_id']]['stats'] = array_merge($matchdata['stats'],$prevParticipantsData[$summoner_info['summoner_id']]['stats']);
					}
					else
					{
						$matchParticipants[$summoner_info['summoner_id']]['stats'] = $matchdata['stats'];
					}
					$matchParticipants = addslashes(json_encode(array_replace($prevParticipantsData,$matchParticipants)));
					$db->query('UPDATE lol_matches SET participants="'.$matchParticipants.'" WHERE region="'.strtoupper($_GET['region']).'" AND gameId='.$matchdata['gameId']);
					$db->query('UPDATE lol_summoners SET matches=matches+1 WHERE region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id']);
				}
			}
		}
		if($matchParticipantsAddIds != null)
		{
			$matchParticipantsAddIdsString = null;
			foreach($matchParticipantsAddIds as $matchParticipantsGameSummonerId)
			{
				foreach($matchParticipantsGameSummonerId as $matchParticipantsSummonerId)
				{
					$matchParticipantsAddIdsString .= $matchParticipantsSummonerId.',';
				}
			}
			
			$matchLostSummonerIds = array_chunk(explode(',',$matchParticipantsAddIdsString),$config['api.summoners.maxperquery']);
			foreach($matchLostSummonerIds as $chunkCount => $chunkData)
			{
				$matchLostSummonerIdsJson = core::extjson(str_replace(array('{{region}}','{{summoner_id}}','{{riotapi}}'),array($_GET['region'],implode(',',$chunkData),$config['riot.api.key']),core::$api_url_summonerprof));
				if(is_array($matchLostSummonerIdsJson))
				{
					foreach($matchLostSummonerIdsJson as $matchLostSummonerIdsJsonSummonerId => $matchLostSummonerIdsJsonSummonerData)
					{
						if($db->query('SELECT id FROM lol_summoners WHERE region="'.$_GET['region'].'" AND summoner_id='.$matchLostSummonerIdsJsonSummonerId)->num_rows == 0)
						{
							$db->query('INSERT INTO lol_summoners (summoner_id,region,name,icon,revision,level,updated) VALUES ("'.$matchLostSummonerIdsJsonSummonerId.'","'.$_GET['region'].'","'.$matchLostSummonerIdsJsonSummonerData['name'].'",'.$matchLostSummonerIdsJsonSummonerData['profileIconId'].',"'.$matchLostSummonerIdsJsonSummonerData['revisionDate'].'",'.$matchLostSummonerIdsJsonSummonerData['summonerLevel'].',0)') or die($db->error);
						}
					}	
				}
			}
		}
	}
	/* Quality calculator */
		$qualityLastSummonerGames = $db->query('SELECT participants,gameId FROM lol_matches WHERE region="'.$_GET['region'].'" AND playerIds LIKE "%'.$summoner_info['summoner_id'].'%" LIMIT '.$config['api.summoners.quality.lastgames']);
		$qualityTotalPoints = 0;
		$qualityTotalMatches = 0;
		$qualityMaxPoints = 7;
		while($qualityGameData = $qualityLastSummonerGames->fetch_row())
		{
					$qualityActualPoints = 0;
					$qualityGameData = json_decode($qualityGameData[0],true);
					if(@$qualityGameData[$summoner_info['summoner_id']]['stats']['win'] == true)
					{
						$qualityActualPoints = $qualityActualPoints+3;
					}
					(empty($qualityGameData[$summoner_info['summoner_id']]['stats']['numDeaths']) || @$qualityGameData[$summoner_info['summoner_id']]['stats']['numDeaths'] == 0) ? $qualityGameData[$summoner_info['summoner_id']]['stats']['numDeaths']=1:null;
					$qualityGameKDA = (((int) @$qualityGameData[$summoner_info['summoner_id']]['stats']['championsKilled'])+(int) @$qualityGameData[$summoner_info['summoner_id']]['stats']['assists'])/((int) @$qualityGameData[$summoner_info['summoner_id']]['stats']['numDeaths']);
					if($qualityGameKDA > 2)
					{
						$qualityActualPoints = $qualityActualPoints+1;
					}
					if($qualityGameKDA > 3)
					{
						$qualityActualPoints = $qualityActualPoints+1;
					}
					if($qualityGameKDA > 5)
					{
						$qualityActualPoints = $qualityActualPoints+2;
					}
					($qualityActualPoints > $qualityMaxPoints) ? $qualityActualPoints = $qualityMaxPoints:null;
					$qualityTotalPoints = $qualityTotalPoints+$qualityActualPoints;
					$qualityTotalMatches++;
				
		}
		$qualitySummoner = number_format((($qualityTotalPoints*$qualityTotalMatches)/$qualityMaxPoints),0);
		($qualitySummoner > 100) ? $qualitySummoner=100:null;
		$db->query('UPDATE lol_summoners SET quality='.$qualitySummoner.' WHERE region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id']) or die($db->error);
}
$summoner_info['matches'] = $db->query('SELECT matches FROM lol_summoners WHERE region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id'])->fetch_row()[0];
if($db->query('SELECT id FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->num_rows > 0)
{
	$summoner_infoLeague = $db->query('SELECT mmr,tier,division FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->fetch_row();
}
else
{
	$summoner_infoLeague = false;
}
	$summoner_info['quality'] = $db->query('SELECT quality FROM lol_summoners WHERE region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id'])->fetch_row()[0];
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
					<li class="active">
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>"><i class="si si-list"></i> <?php echo $lang['summonerProfileTabsMatchHistory'] ?></a>
					</li>
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/champmastery"><i class="si si-badge"></i> <?php echo $lang['summonerProfileTabsChampMastery'] ?></a>
					</li>
					<li>
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
						<div class="block">
							<div class="block-content">
								<table id="TableMatchHistory" class="table table-bordered js-dataTable-full" style="width:100%">
									<thead>
										<tr>
											<th class="text-center"><?php echo $lang['summonerMatchHistoryTableHeadGeneral'] ?></th>
											<th class="text-center" style="min-width:140px !important"><?php echo $lang['summonerMatchHistoryTableHeadChamp'] ?></th>
											<th class="text-center"><?php echo $lang['summonerMatchHistoryTableHeadStats'] ?></th>
											<th class="matchHistoryItems text-center" style="min-width:170px !important;"><?php echo $lang['summonerMatchHistoryTableHeadItems'] ?></th>
											<th class="text-center"><?php echo $lang['summonerMatchHistoryTableHeadPlayers'] ?></th>
										</tr>
									</thead>
									<tbody>
									<?php 
									$summonerGames = $db->query('SELECT winnerTeam,participants,createDate,gameType,gameLength,gameId FROM lol_matches WHERE playerIds LIKE "%'.$summoner_info['summoner_id'].'%"');
									while($gameData = $summonerGames->fetch_row())
									{
										$playersData = json_decode($gameData[1],true);
										if($playersData[$summoner_info['summoner_id']]['team'] == $gameData[0])
										{
											/* Won */
											$gameColorStatus = 'Won'; 
											$gameStatus = $lang['matchHistoryWon'];
										}
										if($playersData[$summoner_info['summoner_id']]['team'] != $gameData[0])
										{
											/* Lost */
											$gameColorStatus = 'Lost'; 
											$gameStatus = $lang['matchHistoryLost']; 
										}
										if($gameData[4] != 0)
										{
											$gameLength = '<div class="matchHistoryStatus'.$gameColorStatus.'">'.$gameStatus.'</div>'.core::time_ms($gameData[4]);
										}
										else
										{
											$gameLength	= '<div class="matchHistoryStatusButton"><button class="btn btn-success" data-toggle="modal" data-target="#loadNewAjax" onclick="javascript:loadGame(\''.$gameData[5].'\');"  type="button">'.$lang['summonerProfileGamesLengthLoad'].'</button></div>';
										}
										if(array_key_exists('masteries',$playersData[$summoner_info['summoner_id']]))
										{
											(!empty(array_search(6361, array_column($playersData[$summoner_info['summoner_id']]['masteries'], 'masteryId')))) ? $gameKeyStoneId = 6361:null;
											(!empty(array_search(6362, array_column($playersData[$summoner_info['summoner_id']]['masteries'], 'masteryId')))) ? $gameKeyStoneId = 6362:null;
											(!empty(array_search(6363, array_column($playersData[$summoner_info['summoner_id']]['masteries'], 'masteryId')))) ? $gameKeyStoneId = 6363:null;
											(!empty(array_search(6261, array_column($playersData[$summoner_info['summoner_id']]['masteries'], 'masteryId')))) ? $gameKeyStoneId = 6261:null;
											(!empty(array_search(6262, array_column($playersData[$summoner_info['summoner_id']]['masteries'], 'masteryId')))) ? $gameKeyStoneId = 6262:null;
											(!empty(array_search(6263, array_column($playersData[$summoner_info['summoner_id']]['masteries'], 'masteryId')))) ? $gameKeyStoneId = 6263:null;
											(!empty(array_search(6161, array_column($playersData[$summoner_info['summoner_id']]['masteries'], 'masteryId')))) ? $gameKeyStoneId = 6161:null;
											(!empty(array_search(6162, array_column($playersData[$summoner_info['summoner_id']]['masteries'], 'masteryId')))) ? $gameKeyStoneId = 6162:null;
											(!empty(array_search(6164, array_column($playersData[$summoner_info['summoner_id']]['masteries'], 'masteryId')))) ? $gameKeyStoneId = 6164:null;
											if(!empty($gameKeyStoneId))
											{
												$gameKeyStone = '<div class="matchHistoryKeyStone"><img draggable="false" class="matchHistorySingleKeyStone" src="'.$config['web.url'].'/assets/game/summoners/masteries/'.$gameKeyStoneId.'.png"></div>';
											}
											else
											{
												$gameKeyStone = null;
											}
										}
										else
										{
											$gameKeyStone = null;
										}
										$gameMultiKill = null;
										if(@$playersData[$summoner_info['summoner_id']]['stats']['largestMultiKill'] > 1 && @$playersData[$summoner_info['summoner_id']]['stats']['largestMultiKill'] <= 6)
										{
											$gameMultiKill = '<span class="label label-danger">'.$lang['summonerMatchHistoryMultiKill_'.$playersData[$summoner_info['summoner_id']]['stats']['largestMultiKill']].'</span>';
										}
										$matchBlueTeam = null;
										$matchRedTeam = null;
										
										foreach($playersData as $player_id => $player_data)
										{
											$botPlayerIds = array('{{BOT_0}}' => null,'{{BOT_1}}' => null,'{{BOT_2}}' => null,'{{BOT_3}}' => null,'{{BOT_4}}' => null,'{{BOT_5}}' => null,'{{BOT_6}}' => null,'{{BOT_7}}' => null,'{{BOT_8}}' => null,'{{BOT_9}}' => null);
											if(!array_key_exists($player_id,$botPlayerIds))
											{
												$player_data['name'] = $db->query('SELECT name FROM lol_summoners WHERE region="'.$_GET['region'].'" AND summoner_id='.$player_id)->fetch_row()[0];
												$player_data['bot'] = false;
											}
											else
											{
												$player_data['name'] = str_replace('{{champname}}',$champdata[$playersData[$player_id]['champ']]['name'],$lang['profileMatchHistoryBotPlayer']);
												$player_data['bot'] = true;
											}
											if($player_data['team'] == 100)
											{
												$matchBlueTeam .= '
												<div class="matchHistoryPlayersSummoner">
													<div class="matchHistoryPlayersSummonerImg">
														<img draggable="false" class="matchHistoryPlayersSummonerImg2" src="'.$config['web.url'].'/assets/game/champions/square/'.$champdata[$player_data['champ']]['key'].'.png">
													</div>
													<div class="matchHistoryPlayersSummonerName">';
													if($player_data['bot'] == true)
													{
														$matchBlueTeam .= core::cutstring($player_data['name']);
													}
													else
													{
														$matchBlueTeam .= '<a href="'.$config['web.url'].'/summoner/'.$_GET['region'].'/'.$player_data['name'].'">'.core::cutstring($player_data['name']).'</a>';
													}
													$matchBlueTeam .= '</div>
												</div>';
											}
											if($player_data['team'] == 200)
											{
												$matchRedTeam .= '
												<div class="matchHistoryPlayersSummoner">
													<div class="matchHistoryPlayersSummonerImg">
														<img draggable="false" class="matchHistoryPlayersSummonerImg2" src="'.$config['web.url'].'/assets/game/champions/square/'.$champdata[$player_data['champ']]['key'].'.png">
													</div>
													<div class="matchHistoryPlayersSummonerName">';
													if($player_data['bot'] == true)
													{
														$matchRedTeam .= $player_data['name'];
													}
													else
													{
														$matchRedTeam .= '<a href="'.$config['web.url'].'/summoner/'.$_GET['region'].'/'.$player_data['name'].'">'.$player_data['name'].'</a>';
													}
													$matchRedTeam .= '</div>
												</div>';
											}
										}
										echo '
										<tr class="matchHistory'.$gameColorStatus.'">
											<td data-sort="'.$gameData[2].'" class="matchHistoryBorder'.$gameColorStatus.' text-center">
												<b>'.$lang['lolGameType_'.$gameData[3]].'</b>
												<br>
												'.str_replace('{{time}}',core::time_elapsed($gameData[2] / 1000),$lang['summonerProfileTimeAgo']).'
												<hr class="matchHistoryBorder'.$gameColorStatus.'">
												'.$gameLength.'
											</td>
											<td data-sort="'.$champdata[$playersData[$summoner_info['summoner_id']]['champ']]['key'].'" class="matchHistoryBorder'.$gameColorStatus.' text-center">
												<img draggable="false" class="matchHistoryChamp" src="'.$config['web.url'].'/assets/game/champions/square/'.$champdata[$playersData[$summoner_info['summoner_id']]['champ']]['key'].'.png">
												<div class="matchHistorySpells">
													<img draggable="false" class="matchHistorySingleSpell" src="'.$config['web.url'].'/assets/game/summoners/spells/'.(int) @$playersData[$summoner_info['summoner_id']]['spell1'].'.png">
													<img draggable="false" class="matchHistorySingleSpell" src="'.$config['web.url'].'/assets/game/summoners/spells/'.(int) @$playersData[$summoner_info['summoner_id']]['spell2'].'.png">
												</div>
												'.$gameKeyStone.'
												<div class="matchHistoryChampDetails">
													<span class="matchHistoryChampName">'.$champdata[$playersData[$summoner_info['summoner_id']]['champ']]['name'].'</span> 
												</div>
											</td>
											<td data-sort="'.@number_format(((@$playersData[$summoner_info['summoner_id']]['stats']['championsKilled'])+(int) @$playersData[$summoner_info['summoner_id']]['stats']['assists'])/max(@$playersData[$summoner_info['summoner_id']]['stats']['numDeaths'],1),5).'" class="matchHistoryBorder'.$gameColorStatus.' text-center vcenter">
												<div class="matchHistoryKDA">
													<span class="matchHistoryKA">'.(int) (@$playersData[$summoner_info['summoner_id']]['stats']['championsKilled']).'</span> / <span class="matchHistoryD">'.(int) (@$playersData[$summoner_info['summoner_id']]['stats']['numDeaths']).'</span> / <span class="matchHistoryKA">'.(int) @$playersData[$summoner_info['summoner_id']]['stats']['assists'].'</span>
												</div>
												<div class="matchHistoryKDARatio">
													<span class="matchHistoryKDARatio">'.@number_format(((@$playersData[$summoner_info['summoner_id']]['stats']['championsKilled'])+(int) @$playersData[$summoner_info['summoner_id']]['stats']['assists'])/(max(@$playersData[$summoner_info['summoner_id']]['stats']['numDeaths'],1)),2).':1</span> '.$lang['summonerMatchHistoryKDA'].'
												</div>
												'.$gameMultiKill.'
											</td>
											<td class="matchHistoryBorder'.$gameColorStatus.' text-center vcenter">
												<div class="matchHistoryItemsList">
													<img draggable="false" class="matchHistoryItemsSingle" style="margin-left: -40px !important;" src="'.$config['web.url'].'/assets/game/summoners/items/'.(int) @$playersData[$summoner_info['summoner_id']]['stats']['item0'].'.png">
													<img draggable="false" class="matchHistoryItemsSingle" src="'.$config['web.url'].'/assets/game/summoners/items/'.(int) @$playersData[$summoner_info['summoner_id']]['stats']['item1'].'.png">
													<img draggable="false" class="matchHistoryItemsSingle" src="'.$config['web.url'].'/assets/game/summoners/items/'.(int) @$playersData[$summoner_info['summoner_id']]['stats']['item2'].'.png"><br>
													<img draggable="false" class="matchHistoryItemsSingle" src="'.$config['web.url'].'/assets/game/summoners/items/'.(int) @$playersData[$summoner_info['summoner_id']]['stats']['item3'].'.png">
													<img draggable="false" class="matchHistoryItemsSingle" src="'.$config['web.url'].'/assets/game/summoners/items/'.(int) @$playersData[$summoner_info['summoner_id']]['stats']['item4'].'.png">
													<img draggable="false" class="matchHistoryItemsSingle" src="'.$config['web.url'].'/assets/game/summoners/items/'.(int) @$playersData[$summoner_info['summoner_id']]['stats']['item5'].'.png">
													<img draggable="false" class="matchHistoryItemsSingle" style="margin-top: -40px !important;" src="'.$config['web.url'].'/assets/game/summoners/items/'.(int) @$playersData[$summoner_info['summoner_id']]['stats']['item6'].'.png">
												</div>
											</td>
											<td class="matchHistoryBorder'.$gameColorStatus.' vcenter text-center">
												<div class="matchHistoryPlayers">
												
													<div class="matchHistoryPlayersTeams">
														'.$matchBlueTeam.'
													</div>
													<div class="matchHistoryPlayersTeams">
														'.$matchRedTeam.'
													</div>
												</div>
											</td>
										</tr>';
									}
									?>
									</tbody>
								</table>
							</div>
						</div>
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
<script src="<?php echo $config['web.url'] ?>/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script>
function loadGame(gameId){
    $.ajax({url:"<?php echo $config['web.url'] ?>/ajax/loadGame.php",data:{region: "<?php echo $_GET['region'] ?>", gameid: gameId},type:"GET",
		success: function(data) {
			console.log(data);
			if(data == 'MAINTENANCE' || data == 'MAINTENANCEERROR')
			{
				$(function () {
				   $('#loadNewAjax').modal('hide');
				   $('.modal-backdrop').remove();
				});
				$.notify("<?php echo $lang['apiError'] ?>", "error");
				window.location='maintenance.php';
			}
			else
			{
				if(data != 'ERROR' && data != 'MAINTENANCE')
				{
					$(function () {
					   $('#loadNewAjax').modal('hide');
					   $('.modal-backdrop').remove();
					});
					$.notify("<?php echo $lang['ajaxMatchLoaded'] ?>", "success");
					location.reload();
				}
				else
				{
					$(function () {
					   $('#loadNewAjax').modal('hide');
					   $('.modal-backdrop').remove();
					});
					$.notify("<?php echo $lang['ajaxMatchLoadError'] ?>", "error");
				}
			}
    },
    error: function(xhr) { 
		$(function () {
			   $('#loadNewAjax').modal('hide');
			   $('.modal-backdrop').remove();
			});
		$.notify("<?php echo $lang['ajaxMatchLoadError'] ?>", "danger");
    }});
};
var BaseTableDatatables = function() {
    var MatchHistory = function() {
            jQuery("#TableMatchHistory").dataTable({
				responsive: false,
                columnDefs: [{
                    targets: [4]
                },{
					orderable: false, targets: [3,4]
				}],
				"order": [[ 0, "desc" ]],
                pageLength: 10,
				retrieve: true,
                lengthMenu: [
                    [5, 10, 15, 20],
                    [5, 10, 15, 20]
                ]
            })
        },
        n = function() {
            var e = jQuery.fn.dataTable;
            jQuery.extend(!0, e.defaults, {
                dom: "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<tr>><'row'<'col-sm-6'i><'col-sm-6'p>>",
                renderer: "bootstrap",
                oLanguage: {
                    sLengthMenu: "_MENU_",
                    sInfo: "<?php echo $lang['jsTableShowing'] ?> <strong>_START_</strong>-<strong>_END_</strong> <?php echo $lang['jsTableOf'] ?> <strong>_TOTAL_</strong>",
                    oPaginate: {
                        sPrevious: '<i class="fa fa-angle-left"></i>',
                        sNext: '<i class="fa fa-angle-right"></i>'
                    }
                }
            }), jQuery.extend(e.ext.classes, {
                sWrapper: "dataTables_wrapper form-inline dt-bootstrap",
                sFilterInput: "form-control",
                sLengthSelect: "form-control"
            }), e.ext.renderer.pageButton.bootstrap = function(a, t, n, s, o, l) {
                var r, i, u = new e.Api(a),
                    d = a.oClasses,
                    c = a.oLanguage.oPaginate,
                    b = function(e, t) {
                        var s, g, f, p, T = function(e) {
                            e.preventDefault(), jQuery(e.currentTarget).hasClass("disabled") || u.page(e.data.action).draw(!1)
                        };
                        for(s = 0, g = t.length; g > s; s++)
                            if(p = t[s], jQuery.isArray(p)) b(e, p);
                            else {
                                switch(r = "", i = "", p) {
                                    case "ellipsis":
                                        r = "&hellip;", i = "disabled";
                                        break;
                                    case "first":
                                        r = c.sFirst, i = p + (o > 0 ? "" : " disabled");
                                        break;
                                    case "previous":
                                        r = c.sPrevious, i = p + (o > 0 ? "" : " disabled");
                                        break;
                                    case "next":
                                        r = c.sNext, i = p + (l - 1 > o ? "" : " disabled");
                                        break;
                                    case "last":
                                        r = c.sLast, i = p + (l - 1 > o ? "" : " disabled");
                                        break;
                                    default:
                                        r = p + 1, i = o === p ? "active" : ""
                                }
                                r && (f = jQuery("<li>", {
                                    "class": d.sPageButton + " " + i,
                                    "aria-controls": a.sTableId,
                                    tabindex: a.iTabIndex,
                                    id: 0 === n && "string" == typeof p ? a.sTableId + "_" + p : null
                                }).append(jQuery("<a>", {
                                    href: "#"
                                }).html(r)).appendTo(e), a.oApi._fnBindAction(f, {
                                    action: p
                                }, T))
                            }
                    };
                b(jQuery(t).empty().html('<ul class="pagination"/>').children("ul"), s)
            }, e.TableTools && (jQuery.extend(!0, e.TableTools.classes, {
                container: "DTTT btn-group",
                buttons: {
                    normal: "btn btn-default",
                    disabled: "disabled"
                },
                collection: {
                    container: "DTTT_dropdown dropdown-menu",
                    buttons: {
                        normal: "",
                        disabled: "disabled"
                    }
                },
                print: {
                    info: "DTTT_print_info"
                },
                select: {
                    row: "active"
                }
            }), jQuery.extend(!0, e.TableTools.DEFAULTS.oTags, {
                collection: {
                    container: "ul",
                    button: "li",
                    liner: "a"
                }
            }))
        };
    return {
        init: function() {
            n(), MatchHistory()
        }
    }
}();
jQuery(function() {
    BaseTableDatatables.init()
});</script>
<?php if(@$summoner_reload == true)
{
	echo '<script>$.notify("'.$lang['ajaxProfileLoaded'].'", "info");</script>';
}
if(!empty(@$_GET['notify']))
{
	if($_GET['notify'] == 'leaguenolvl')
	{
		echo '<script>$.notify("'.$lang['ajaxLeagueNoLvl'].'", "error");</script>';
	}
}?>
</body>
</html>