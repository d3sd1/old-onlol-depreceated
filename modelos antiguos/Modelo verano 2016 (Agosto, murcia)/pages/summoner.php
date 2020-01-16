<?php
require('../kernel/core.php');
if(empty($_GET['summoner']))
{
	echo '<script>$.notify("'.$lang['SummonerNotSet'].'", "error"); loadurl(\'index.php\')</script>';
	die();
}
if(empty($_GET['region']))
{
	echo '<script>$.notify("'.$lang['RegionNotSet'].'", "error"); loadurl(\'index.php\')</script>';
	die();
}
if(core::check_valid_region($_GET['region']) == TRUE)
{
	
	if($db->query('SELECT id FROM lol_summoners WHERE region="'.$_GET['region'].'" AND name="'.$_GET['summoner'].'"')->num_rows == 0)
	{
		$summoner_json_info=core::extjson(str_replace(array('{{region}}','{{summoner_name}}','{{riotapi}}'),array($_GET['region'],core::format_summonername($_GET['summoner']),$config['riot.api.key']),core::$api_url_summonerprofname));
		if($summoner_json_info == FALSE)
		{
			echo '<script>$.notify("'.str_replace(array('{{name}}','{{region}}'),array($_GET['summoner'],strtoupper($_GET['region'])),$lang['summonerNotFound']).'", "success"); loadurl(\'index.php\')</script>';
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
	echo '<script>$.notify("'.$lang['RegionNotFound'].'", "error"); loadurl(\'index.php\')</script>';
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
$summoner_info['quality'] = $db->query('SELECT quality FROM lol_summoners WHERE region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id'])->fetch_row()[0];
?>
<script>
document.title = '<?php echo str_replace('{{name}}',$summoner_info['name'],$lang['pageMetaTitleSummoner']); ?>';
</script>
<script src="<?php echo $config['web.url'] ?>/assets/js/oneui.min-2.1.js"></script>
<script src="<?php echo $config['web.url'] ?>/assets/js/oneui.app_nomenu.js"></script>
<script src="<?php echo $config['web.url'] ?>/assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js"></script>
<script src="<?php echo $config['web.url'] ?>/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>

<main id="main-container" style="background-image: url(<?php echo $config['web.url'] ?>/assets/game/champions/splash/lux_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
	<div class="content bg-image" style="background-color: rgba(0,0,0,0.6);">
		<div class="push-50-t push-15 clearfix">
			<div class="push-15-r pull-left animated fadeIn">
				<img draggable="false" class="img-avatar img-avatar-thumb" src="<?php echo $config['web.url'] ?>/assets/game/summoners/icons/<?php echo $summoner_info['icon'] ?>.png" alt="">
			</div>
			<h1 class="h2 text-white push-5-t animated zoomIn"><?php echo $summoner_info['name'] ?></h1>
			<h2 class="h5 text-white-op animated zoomIn">Nivel <?php echo $summoner_info['level'] ?></h2>
		</div>
	</div>
	<div class="content bg-white border-b">
		<div class="row items-push text-uppercase">
			<div class="col-xs-6 col-sm-3">
				<div class="font-w700 text-gray-darker animated fadeIn"><?php echo $lang['profileHeadMatches'] ?></div>
				<a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0)"><?php echo $summoner_info['matches'] ?></a>
			</div>
			
			<div class="col-xs-6 col-sm-3">
				<div class="font-w700 text-gray-darker animated fadeIn">Valoración</div>
				<a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0)">27</a>
			</div>
			
			<div class="col-xs-6 col-sm-3">
				<div class="font-w700 text-gray-darker animated fadeIn">Ranking <?php echo $_GET['region'] ?></div>
				<a class="h2 font-w300 text-primary animated flipInX" href="javascript:void(0)">1360</a>
			</div>
			
			<div class="col-xs-6 col-sm-3">
				<div class="font-w700 text-gray-darker animated fadeIn"><?php echo $lang['profileHeadQuality'] ?></div>
				<div class="text-warning push-10-t animated flipInX" data-toggle="popover" data-placement="left" data-content="<?php echo $lang['profilePopoverQualityDsc'] ?>" data-original-title="<?php echo $lang['profilePopoverQualityTitle'] ?>">
				<?php
				$summonerQualityStars = ($summoner_info['quality']/2);
				for($i = 0; $i<5;$i++)
				{
					if($summonerQualityStars >= 10)
					{
						$summonerQualityStars = $summonerQualityStars-10;
						echo '<i class="fa fa-star"></i>';
					}
					elseif($summonerQualityStars >= 5)
					{
						$summonerQualityStars = $summonerQualityStars-5;
						echo '<i class="fa fa-star-half-o"></i>';
					}
					elseif(true)
					{
						echo '<i class="fa fa-star-o"></i>';
					}
				}
				?>
				</div>
			</div>
		</div>
	</div>
	<div class="content">
		<div class="row">
			<div class="block">
				<ul class="nav nav-tabs nav-tabs-alt nav-justified" data-toggle="tabs">
					<li<?php if(empty($_GET['sect']) or @$_GET['sect'] == null) { echo ' class="active"'; } ?>>
						<a href="#profileMatchHistory"><i class="si si-list"></i> <?php echo $lang['summonerProfileTabsMatchHistory'] ?></a>
					</li>
					<li onclick="javascript:loadChampMastery()"<?php if(!empty($_GET['sect']) && $_GET['sect'] == 'champmastery') { echo ' class="active"'; } ?>>
						<a href="#profileChampMastery"><i class="si si-badge"></i> <?php echo $lang['summonerProfileTabsChampMastery'] ?></a>
					</li>
					<li onclick="javascript:loadSummonerLeague()"<?php if(!empty($_GET['sect']) && $_GET['sect'] == 'league') { echo ' class="active"'; } ?>>
						<a href="#profileLeague"><i class="si si-trophy"></i> <?php echo $lang['summonerProfileTabsLeagues'] ?></a>
					</li>
					<li onclick="javascript:loadSummonerLeagueTeams()"<?php if(!empty($_GET['sect']) && $_GET['sect'] == 'teams') { echo ' class="active"'; } ?>>
						<a href="#profileLeagueTeams"><i class="si si-users"></i> <?php echo $lang['summonerProfileTabsTeams'] ?></a>
					</li>
				</ul>
				<div class="block-content tab-content">
					<div class="tab-pane<?php if(empty($_GET['sect']) or @$_GET['sect'] == null) { echo ' active'; } ?>" id="profileMatchHistory">
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
														$matchBlueTeam .= '<a href="javascript:loadurl(\'summoner.php?region='.$_GET['region'].'&summoner='.core::format_summonername($player_data['name']).'\')">'.core::cutstring($player_data['name']).'</a>';
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
														$matchRedTeam .= '<a href="javascript:loadurl(\'summoner.php?region='.$_GET['region'].'&summoner='.core::format_summonername($player_data['name']).'\')">'.$player_data['name'].'</a>';
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
					<div class="tab-pane<?php if(!empty($_GET['sect']) && $_GET['sect'] == 'champmastery') { echo ' active'; } ?>" id="profileChampMastery">
						<?php
						if($db->query('SELECT id FROM lol_summoners_champmastery WHERE summoner_region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id'])->num_rows > 0)
						{
							$summonerChampMasteryData = $db->query('SELECT data,totalLevels,totalPoints FROM lol_summoners_champmastery WHERE summoner_region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id'])->fetch_row();
							$summonerChampMasteryDataFull = json_decode($summonerChampMasteryData[0],true);
						?>
						<div class="block">
							<div class="block-content">
								<table id="TableChampMastery" class="table js-dataTable-full" style="width:100%">
									<thead>
										<tr>
											<th class="text-center"></th>
											<th class="text-center"><?php echo $lang['profileChampMasteryHeadLevel'] ?></th>
											<th class="text-center"><?php echo $lang['profileChampMasteryHeadPoints'] ?></th>
											<th class="text-center"><?php echo $lang['profileChampMasteryHeadChest'] ?></th>
											<th class="text-center"><?php echo $lang['profileChampMasteryHeadNextLevel'] ?></th>
											<th class="text-center"><?php echo $lang['profileChampMasteryHeadPlayed'] ?></th>
										</tr>
									</thead>
									<tbody>
									<?php
									if(@is_array($summonerChampMasteryDataFull['champs']))
									{
										foreach($summonerChampMasteryDataFull['champs'] as $ChampMasteryChampId => $ChampMasteryChampData)
										{
											if($ChampMasteryChampData['chestGranted'] == true)
											{
												$ChampMasteryChampDataChEstAvaliable = 'off';
											}
											else
											{
												$ChampMasteryChampDataChEstAvaliable = 'on';
											}
											if($ChampMasteryChampData['nextLvlNeed'] == 0)
											{
												$ChampMasteryChampDataNextLevelPoints = $lang['profileChampMasteryMaxLvlReached'];
											}
											else
											{
												$ChampMasteryChampDataNextLevelPoints = str_replace('{{points}}',$ChampMasteryChampData['nextLvlNeed'],$lang['profileChampMasteryNextLvlNeeded']);
											}
											echo '<tr class="text-center">
												<td class="vcenter"><img class="ChampMasteryChamp" draggable="false" src="'.$config['web.url'].'/assets/game/champions/square/'.$champdata[$ChampMasteryChampId]['key'].'.png"> <div class="ChampMasteryChampName">'.$champdata[$ChampMasteryChampId]['name'].'</div></td>
												<td class="vcenter" data-sort="'.$ChampMasteryChampData['level'].'"><img draggable="false" src="'.$config['web.url'].'/assets/game/champions/mastery/tier'.$ChampMasteryChampData['level'].'.png"></td>
												<td class="vcenter">'.number_format($ChampMasteryChampData['points'],0,',',',').'</td>
												<td class="vcenter"><img draggable="false" src="'.$config['web.url'].'/assets/game/champions/mastery/chest_'.$ChampMasteryChampDataChEstAvaliable.'.png"> <div class="ChampMasteryChestStatus">'.$lang['profileChampMasteryChestStatus_'.$ChampMasteryChampDataChEstAvaliable].'</div></td>
												<td data-sort="'.(int) $ChampMasteryChampDataNextLevelPoints.'" class="vcenter">'.$ChampMasteryChampDataNextLevelPoints.'</td>
												<td data-sort="'.$ChampMasteryChampData['lastPlayTime'].'" class="vcenter">'.str_replace('{{time}}',core::time_elapsed($ChampMasteryChampData['lastPlayTime'] / 1000),$lang['profileChampMasteryPlayed']).'</td>
											</tr>';
										}
									}
									else
									{
										echo '<div class="col-sm-6 col-sm-offset-3">
										<h1 class="font-s128 font-w300 text-modern animated zoomInDown">'.$lang['profileChampMasteryErrorTitle'].'</h1>
										<h2 class="h3 font-w300 push-50 animated fadeInUp">'.$lang['profileChampMasteryErrorContent'].'</h2>
										</div>';
									}
								?>
									</tbody>
								</table>
							</div>
						</div>
					<?php
						}
						else
						{
							echo '<div class="col-sm-6 col-sm-offset-3">
									<h1 class="font-s128 font-w300 text-modern animated zoomInDown">'.$lang['profileChampMasteryErrorTitle'].'</h1>
									<h2 class="h3 font-w300 push-50 animated fadeInUp">'.$lang['profileChampMasteryErrorContent'].'</h2>
								</div>';
						}
					?>
					</div>
					<div class="tab-pane<?php if(!empty($_GET['sect']) && $_GET['sect'] == 'league') { echo ' active'; } ?>" id="profileLeague">
					<?php
					if($db->query('SELECT id FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->num_rows > 0)
					{
						$summonerLeagueData = $db->query('SELECT name,division,tier FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->fetch_row();
						$leagueDataDivStatus[1] = null;$leagueDataDivStatus[2] = null;$leagueDataDivStatus[3] = null;$leagueDataDivStatus[4] = null;$leagueDataDivStatus[5] = null;
						
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
								<a href="#leagueDivison1">I</a>
							</li>
							<li class="'.$leagueDataDivStatus[2].'">
								<a href="#leagueDivison2">II</a>
							</li>
							<li class="'.$leagueDataDivStatus[3].'">
								<a href="#leagueDivison3">III</a>
							</li>
							<li class="'.$leagueDataDivStatus[4].'">
								<a href="#leagueDivison4">IV</a>
							</li>
							<li class="'.$leagueDataDivStatus[5].'">
								<a href="#leagueDivison5">V</a>
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
											<img draggable="false" class="img-avatar img-avatar48" src="http://avatar.leagueoflegends.com/euw/'.core::format_summonername($row[0]).'.png" alt="">
											</td>
											<td class="font-w600"><a href="javascript:loadurl(\'summoner.php?region='.$_GET['region'].'&summoner='.core::format_summonername($row[0]).'\')">'.$row[0].'</a></td>
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
					<div class="tab-pane<?php if(!empty($_GET['sect']) && $_GET['sect'] == 'teams') { echo ' active'; } ?>" id="profileLeagueTeams">
					lolaso
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
<script>
function loadGame(gameId){
    $.ajax({url:"ajax/loadGame.php",data:{region: "<?php echo $_GET['region'] ?>", gameid: gameId},type:"GET",
		success: function(data) {
			console.log(data);
			if(data == 'MAINTENANCE' || data == 'MAINTENANCEERROR')
			{
				$(function () {
				   $('#loadNewAjax').modal('hide');
				   $('.modal-backdrop').remove();
				});
				$.notify("<?php echo $lang['apiError'] ?>", "success");
				loadurl('maintenance.php');
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
					loadurl('summoner.php?region=<?php echo $_GET['region'] ?>&summoner=<?php echo core::format_summonername($_GET['summoner']) ?>');
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
        loadurl('summoner.php?region=<?php echo $_GET['region'] ?>&summoner=<?php echo core::format_summonername($_GET['summoner']) ?>');
    }});
};
function loadSummonerLeague(){
    $.ajax({url:"ajax/loadSummonerLeagues.php",data:{region: "<?php echo $_GET['region'] ?>", summonerid: <?php echo $summoner_info['summoner_id'] ?>},type:"GET",
		success: function(data) {
			if(data == 'ERRORNOLVL')
			{
				$.notify("<?php echo $lang['ajaxLeagueNoLvl'] ?>", "warning");
			}
			else if(data != 'ERROR')
			{
				$.notify("<?php echo $lang['ajaxLeagueLoaded'] ?>", "success");
				loadurl('summoner.php?region=<?php echo $_GET['region'] ?>&summoner=<?php echo core::format_summonername($_GET['summoner']) ?>&sect=league');
			}
    },
    error: function(xhr) { 
		$.notify("<?php echo $lang['ajaxLeagueLoadError'] ?>", "danger");
    }});
};
function loadSummonerLeagueTeams(){
    $.ajax({url:"ajax/loadSummonerTeamLeagues.php",data:{region: "<?php echo $_GET['region'] ?>", summonerid: <?php echo $summoner_info['summoner_id'] ?>},type:"GET",
		success: function(data) {
			if(data == 'ERRORNOLVL')
			{
				$.notify("<?php echo $lang['ajaxLeagueNoLvl'] ?>", "warning");
			}
			else if(data != 'ERROR')
			{
				$.notify("<?php echo $lang['ajaxLeagueLoaded'] ?>", "success");
				loadurl('summoner.php?region=<?php echo $_GET['region'] ?>&summoner=<?php echo core::format_summonername($_GET['summoner']) ?>&sect=teams');
			}
    },
    error: function(xhr) { 
		$.notify("<?php echo $lang['ajaxLeagueLoadError'] ?>", "danger");
    }});
};
function loadChampMastery(){
    $.ajax({url:"ajax/loadChampMastery.php",data:{region: "<?php echo $_GET['region'] ?>", summoner: <?php echo $summoner_info['summoner_id'] ?>},type:"GET",
		success: function(data) {
			if(data != 'ERROR')
			{
				$(function () {
				   $('#loadNewAjax').modal('hide');
				   $('.modal-backdrop').remove();
				});
				$.notify("<?php echo $lang['ajaxChampMasteryLoaded'] ?>", "success");
				loadurl('summoner.php?region=<?php echo $_GET['region'] ?>&summoner=<?php echo core::format_summonername($_GET['summoner']) ?>&sect=champmastery');
			}
    },
    error: function(xhr) { 
		$(function () {
			   $('#loadNewAjax').modal('hide');
			   $('.modal-backdrop').remove();
			});
		$.notify("<?php echo $lang['ajaxChampMasteryLoadError'] ?>", "danger");
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
	ChampMastery = function() {
            jQuery("#TableChampMastery").dataTable({
				responsive: false,
                columnDefs: [{
                    targets: [4]
                }],
				"order": [[ 1, "desc" ],[ 2, "desc" ]],
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
            n(), MatchHistory(), ChampMastery()
        }
    }
}();
jQuery(function() {
    BaseTableDatatables.init()
});</script>