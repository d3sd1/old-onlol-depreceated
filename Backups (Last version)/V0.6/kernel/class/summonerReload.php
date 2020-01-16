<?php
$summonerRegion = $core->regionDepure($_GET['region']);
try {
	$api->forceUpdate(false);
	/* Basic info */
	$summonerApi = $api->summonerByName("$_GET[name]",$summonerRegion)[mb_strtolower(str_replace(' ',null,$_GET['name']), 'UTF-8')];
	if($db->query('SELECT id FROM api_summoners WHERE name="'.$_GET['name'].'"')->num_rows == 0)
	{
		$db->query('INSERT INTO api_summoners (id,region,name,icon,level,revision) VALUES ("'.$summonerApi['id'].'","'.$summonerRegion.'","'.$summonerApi['name'].'","'.$summonerApi['profileIconId'].'","'.$summonerApi['summonerLevel'].'","'.$summonerApi['revisionDate'].'")');
	}
	$summonerQuery = $db->query('SELECT id,region,name,icon,level,revision FROM api_summoners WHERE name="'.$summonerApi['name'].'"')->fetch_row();
	if($summonerQuery[5] != $summonerApi['revisionDate'])
	{
		$reloadStatus = true;
	}
	else
	{
		$reloadStatus = false;
	}
	if($reloadStatus == true)
	{
		$db->query('UPDATE api_summoners SET region="'.$summonerRegion.'",name="'.$summonerApi['name'].'",icon="'.$summonerApi['profileIconId'].'",level="'.$summonerApi['summonerLevel'].'",revision="'.$summonerApi['revisionDate'].'" WHERE name="'.$summonerApi['name'].'"');
		$summonerQuery = $db->query('SELECT id,region,name,icon,level,revision FROM api_summoners WHERE name="'.$summonerApi['name'].'"')->fetch_row();
	}
	$summonerInfo = array('id' => $summonerQuery[0],'region' => $summonerRegion,'name' => $summonerQuery[2],'icon' => $summonerQuery[3],'level' => $summonerQuery[4],'revision' => $summonerQuery[5], 'leagues' => array(), 'runes' => array(), 'masteries' => array(), 'stats' => array(), 'actualGame' => array(), 'champMastery' => array(), 'champSkill' => array(), 'games' => array());
}
 catch(Exception $e) {
	header('Location: '.URL.'/?error=summonernotfound');
	die();
};
if(!empty($_GET['searched']))
{
	$_SESSION['onlol_lastSearch'] = $summonerInfo['name'];
}
if(empty($_GET['name']) or empty($_GET['region']))
{
	header('Location: '.URL);
	die();
}
if(empty($_SESSION['onlol_summonersSearched']))
{
	$_SESSION['onlol_summonersSearched'] = array();
}
if(array_key_exists($summonerInfo['name'],$_SESSION['onlol_summonersSearched']) == FALSE)
{
	if(count($_SESSION['onlol_summonersSearched']) < $config['summoners.recent.maxsearchs'])
	{
		$_SESSION['onlol_summonersSearched'][$summonerInfo['name']] = $summonerInfo['region'];
	}
	else
	{
		array_shift($_SESSION['onlol_summonersSearched']);
		$_SESSION['onlol_summonersSearched'][$summonerInfo['name']] = $summonerInfo['region'];
	}
}
$_SESSION['userregion'] = $core->regionDepure($_GET['region']);
$userRegion = $_SESSION['userregion'];
try{
	/* Leagues */
	$summonerLeagueApi = $api->league($summonerInfo['id'],$summonerRegion,null)[$summonerInfo['id']];
	foreach ($summonerLeagueApi[0]['entries'] as $summonerLeagueNum => $summonerLeagueVal) {
       if ($summonerLeagueVal['playerOrTeamId'] === $summonerInfo['id']) {
           $summonerLeagueRow = $summonerLeagueNum;
       }
   }
	$summonerMMR = $api->getMMR($summonerInfo['id'],$summonerRegion);
	$summonerLeagueSoloQ = array('tier' => 'UNRANKED', 'entries' => array('0' => array('division' => 'V', 'playerOrTeamName' => 'NONE', 'leaguePoints' => 0, 'wins' => 0, 'losses' => 0, 'playstyle' => 'NONE', 'isHotStreak' => 0, 'isVeteran' => 0, 'isFreshBlood' => 0, 'isInactive' => 0)));
	$summonerLeagueTeam3x3 = array('tier' => 'UNRANKED', 'entries' => array('0' => array('division' => 'V', 'playerOrTeamName' => 'NONE', 'leaguePoints' => 0, 'wins' => 0, 'losses' => 0, 'isHotStreak' => 0, 'isVeteran' => 0, 'isFreshBlood' => 0, 'isInactive' => 0)));
	$summonerLeagueTeam5x5 = array('tier' => 'UNRANKED', 'entries' => array('0' => array('division' => 'V', 'playerOrTeamName' => 'NONE', 'leaguePoints' => 0, 'wins' => 0, 'losses' => 0, 'isHotStreak' => 0, 'isVeteran' => 0, 'isFreshBlood' => 0, 'isInactive' => 0)));
		$summonerTeams = array();
		$team3x3LeagueRow = 0;
		$team5x5LeagueRow = 0;
	foreach($summonerLeagueApi as $leagueData)
	{
		(empty($leagueData['entries'][$summonerLeagueRow]['division'])) ? $leagueData['entries'][$summonerLeagueRow]['division'] = 'V':null;
		(empty($leagueData['tier'])) ? $leagueData['tier'] = 'UNRANKED':null;
		if($leagueData['queue'] == 'RANKED_SOLO_5x5')
		{
			$summonerLeagueSoloQ = $leagueData;
		}
		elseif($leagueData['queue'] == 'RANKED_TEAM_3x3')
		{
			foreach ($leagueData['entries'] as $team3x3Key => $team3x3Val) {
			   if (@$team3x3Val['playerOrTeamId'] === $leagueData['participantId']) {
				   $team3x3LeagueRow = $team3x3Key;
			   }
		   }
			if($core->compareLeague($leagueData['tier'],$leagueData['entries'][$team3x3LeagueRow]['division'],$summonerLeagueTeam3x3['tier'],$summonerLeagueTeam3x3['entries'][0]['division']) == 1)
			{
				$summonerLeagueTeam3x3 = $leagueData;
				$summonerTeams[] = $leagueData['entries'][$team3x3LeagueRow]['playerOrTeamId'];
			}
		}
		elseif($leagueData['queue'] == 'RANKED_TEAM_5x5')
		{
			foreach ($leagueData['entries'] as $team5x5Key => $team5x5Val) {
			   if (@$team5x5Val['playerOrTeamId'] === $leagueData['participantId']) {
				   $team5x5LeagueRow = $team5x5Key;
			   }
		   }
			if($core->compareLeague($leagueData['tier'],$leagueData['entries'][$team5x5LeagueRow]['division'],$summonerLeagueTeam5x5['tier'],$summonerLeagueTeam5x5['entries'][0]['division']) == 1)
			{
			
				$summonerLeagueTeam5x5 = $leagueData;
				$summonerTeams[] = $leagueData['entries'][$team5x5LeagueRow]['playerOrTeamId'];
			}
		}
	}
	if($db->query('SELECT id FROM api_summoners_league WHERE id="'.$summonerInfo['id'].'"')->num_rows == 0)
	{
		$db->query('INSERT INTO api_summoners_league VALUES ("'.$summonerApi['id'].'","'.$summonerLeagueSoloQ['tier'].'","'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['division'].'","'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['leaguePoints'].'","'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['wins'].'","'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['losses'].'","'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['playstyle'].'","'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['isHotStreak'].'","'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['isVeteran'].'","'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['isFreshBlood'].'","'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['isInactive'].'", "'.@addslashes(json_encode($summonerLeagueSoloQ['entries'][$summonerLeagueRow]['miniSeries'])).'", "'.$summonerMMR['MMR'].'", "'.implode(';',$summonerTeams).'", "'.$summonerLeagueTeam5x5['tier'].'","'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['division'].'","'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['leaguePoints'].'",
			"'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['wins'].'","'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['losses'].'","'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['isHotStreak'].'","'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['isVeteran'].'","'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['isFreshBlood'].'","'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['isInactive'].'", "'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['playerOrTeamName'].'", "'.@addslashes(json_encode($summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['miniSeries'])).'", "'.$summonerLeagueTeam3x3['tier'].'","'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['division'].'","'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['leaguePoints'].'",
			"'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['wins'].'","'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['losses'].'","'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['isHotStreak'].'","'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['isVeteran'].'","'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['isFreshBlood'].'",
			"'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['isInactive'].'","'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['playerOrTeamName'].'","'.@addslashes(json_encode($summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['miniSeries'])).'")') or die($db->error);
	}
	elseif($reloadStatus == true)
	{
		$db->query('UPDATE api_summoners_league SET soloq_tier="'.$summonerLeagueSoloQ['tier'].'",soloq_division="'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['division'].'",soloq_lp="'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['leaguePoints'].'",soloq_wins="'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['wins'].'",soloq_losses="'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['losses'].'",soloq_playstyle="'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['playstyle'].'",soloq_isHotStreak="'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['isHotStreak'].'",soloq_isVeteran="'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['isVeteran'].'",soloq_isFreshBlood="'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['isFreshBlood'].'",soloq_isInactive="'.$summonerLeagueSoloQ['entries'][$summonerLeagueRow]['isInactive'].'",mmr="'.$summonerMMR['MMR'].'", teams="'.implode(';',$summonerTeams).'",5x5_tier="'.$summonerLeagueTeam5x5['tier'].'",5x5_division="'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['division'].'",5x5_lp="'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['leaguePoints'].'",5x5_wins="'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['wins'].'",5x5_losses="'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['losses'].'",5x5_isHotStreak="'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['isHotStreak'].'",5x5_isVeteran="'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['isVeteran'].'",5x5_isFreshBlood="'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['isFreshBlood'].'",5x5_isInactive="'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['isInactive'].'",5x5_name="'.$summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['playerOrTeamName'].'",3x3_tier="'.$summonerLeagueTeam5x5['tier'].'",3x3_division="'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['division'].'",3x3_lp="'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['leaguePoints'].'",3x3_wins="'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['wins'].'",3x3_losses="'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['losses'].'",3x3_isHotStreak="'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['isHotStreak'].'",3x3_isVeteran="'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['isVeteran'].'",3x3_isFreshBlood="'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['isFreshBlood'].'",3x3_isInactive="'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['isInactive'].'",3x3_name="'.$summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['playerOrTeamName'].'", 3x3_miniSeries="'.@addslashes(json_encode($summonerLeagueTeam3x3['entries'][$team3x3LeagueRow]['miniSeries'])).'",5x5_miniSeries="'.@addslashes(json_encode($summonerLeagueTeam5x5['entries'][$team5x5LeagueRow]['miniSeries'])).'",soloq_miniSeries="'.@addslashes(json_encode($summonerLeagueSoloQ['entries'][$summonerLeagueRow]['miniSeries'])).'" WHERE id="'.$summonerApi['id'].'"') or die($db->error);
	}
	
	$summonerLeagueQuery = $db->query('SELECT * FROM api_summoners_league WHERE id="'.$summonerApi['id'].'"')->fetch_row();
	$summonerInfo['leagues'] = array('RANKED_SOLO_5x5' => array('tier' => $summonerLeagueQuery[1],'division' => $summonerLeagueQuery[2],'lp' => $summonerLeagueQuery[3],'wins' => $summonerLeagueQuery[4],'losses' => $summonerLeagueQuery[5],'playstyle' => $summonerLeagueQuery[6], 'isHotStreak' => $summonerLeagueQuery[7], 'isVeteran' => $summonerLeagueQuery[8], 'isFreshBlood' => $summonerLeagueQuery[9], 'isInactive' => $summonerLeagueQuery[10], 'miniSeries' => json_decode($summonerLeagueQuery[11],true), 'mmr' => $summonerLeagueQuery[12]),'RANKED_TEAM_5x5' => array('tier' => $summonerLeagueQuery[14],'division' => $summonerLeagueQuery[15],'lp' => $summonerLeagueQuery[16],'wins' => $summonerLeagueQuery[17],'losses' => $summonerLeagueQuery[18], 'isHotStreak' => $summonerLeagueQuery[19], 'isVeteran' => $summonerLeagueQuery[20], 'isFreshBlood' => $summonerLeagueQuery[21], 'isInactive' => $summonerLeagueQuery[22], 'name' => $summonerLeagueQuery[23], 'miniSeries' => json_decode($summonerLeagueQuery[24],true)),'RANKED_TEAM_3x3' => array('tier' => $summonerLeagueQuery[25],'division' => $summonerLeagueQuery[26],'lp' => $summonerLeagueQuery[27],'wins' => $summonerLeagueQuery[28],'losses' => $summonerLeagueQuery[29], 'isHotStreak' => $summonerLeagueQuery[30], 'isVeteran' => $summonerLeagueQuery[31], 'isFreshBlood' => $summonerLeagueQuery[32], 'isInactive' => $summonerLeagueQuery[33], 'name' => $summonerLeagueQuery[34], 'miniSeries' => json_decode($summonerLeagueQuery[35],true)));
} catch(Exception $e) {
	if($e->getMessage() != 'NOT_FOUND')
	{
		echo 'Se produjo un error recargando las ligas ';
	}
};
try{
	$summonerRunesApi = $api->summonerById($summonerInfo['id'],$summonerRegion,'runes');
	$summonerInfo['runes'] = $summonerRunesApi[$summonerApi['id']]['pages'];
	$summonerMasteriesApi = $api->summonerById($summonerInfo['id'],$summonerRegion,'masteries');
	$summonerInfo['masteries'] = $summonerMasteriesApi[$summonerApi['id']]['pages'];
} catch(Exception $e) {
	if($e->getMessage() != 'NOT_FOUND')
	{
		echo 'Se produjo un error recargando las runas y maestrias';
	}
};
try{
	$statsSeason = array();
	$statsAction = array();
	$summonerStatsSummaryApi = $api->stats($summonerInfo['id'],$summonerRegion,'summary');
	$summonerStatsRankedApi = $api->stats($summonerInfo['id'],$summonerRegion,'ranked');
	$summonerStatsRankedApiActualSeason = $summonerStatsRankedApi;
	/* Actual season */
	if($db->query('SELECT id FROM api_summoners_stats WHERE id="'.$summonerInfo['id'].'"')->num_rows == 0)
	{
		$statsAction[] = 'insert';
		$statsSeason[] = ACTUAL_SEASON;
	}
	elseif($summonerStatsRankedApi['modifyDate'] != $db->query('SELECT revision FROM api_summoners_stats WHERE id="'.$summonerApi['id'].'"')->fetch_row()[0])
	{
		$statsAction[] = 'update';
		$statsSeason[] = ACTUAL_SEASON;
	}
	/* Past seasons */
	foreach(explode(',',$config['api.seasons']) as $seasonVal)
	{
		if($seasonVal != ACTUAL_SEASON)
		{
			if($db->query('SHOW TABLES LIKE "api_summoners_stats_'.$seasonVal.'"')->num_rows == 0)
			{
				$db->query('CREATE TABLE api_summoners_stats_'.$seasonVal.' LIKE api_summoners_stats');
			}
			if($db->query('SELECT id FROM api_summoners_stats_'.$seasonVal.' WHERE id="'.$summonerInfo['id'].'"')->num_rows == 0)
			{
				$statsAction[] = 'insert';
				$statsSeason[] = $seasonVal;
			}
		}
	}
	if(count($statsAction) > 0)
	{
		for($i = 0; $i < count($statsAction); $i++)
		{
			if($statsSeason[$i] != ACTUAL_SEASON)
			{
				$summonerStatsSummaryApi = $api->stats($summonerInfo['id'],$summonerRegion,'summary',$statsSeason[$i]);
				$summonerStatsRankedApi = $api->stats($summonerInfo['id'],$summonerRegion,'ranked',$statsSeason[$i]);
			}
			if($statsSeason[$i] == ACTUAL_SEASON)
			{
				$dbColumn = 'api_summoners_stats';
			}
			else
			{
				$dbColumn = 'api_summoners_stats_'.$statsSeason[$i];
			}
			$rowsOfTable = $db->query('SELECT * FROM '.$dbColumn.'')->fetch_fields();
			$summaryStats = array();
			foreach($rowsOfTable as $val)
			{
				$summaryStats[$val->name] = 0;
			}
			foreach($summonerStatsSummaryApi['playerStatSummaries'] as $summaryVal)
			{
				if($summaryVal['playerStatSummaryType'] == 'RankedPremade3x3') { $summaryVal['playerStatSummaryType'] = 'RankedTeam3x3'; }
				if($summaryVal['playerStatSummaryType'] == 'RankedPremade5x5') { $summaryVal['playerStatSummaryType'] = 'RankedTeam5x5'; }
				if(strstr($summaryVal['playerStatSummaryType'],'Ranked') && strstr($summaryVal['playerStatSummaryType'],'Unranked') == false)
				{
					if(array_key_exists($summaryVal['playerStatSummaryType'].'Wins',$summaryStats) && array_key_exists($summaryVal['playerStatSummaryType'].'Losses',$summaryStats))
					{
						$summaryStats[$summaryVal['playerStatSummaryType'].'Wins'] = $summaryVal['wins'];
						$summaryStats[$summaryVal['playerStatSummaryType'].'Losses'] = $summaryVal['losses'];
					}
					else
					{
						if($db->query('SHOW COLUMNS FROM '.$dbColumn.' LIKE "'.$summaryVal['playerStatSummaryType'].'Wins"')->fetch_row()[0] == 0)
						{
							$db->query('ALTER TABLE '.$dbColumn.' ADD '.$summaryVal['playerStatSummaryType'].'Wins INT(11) NOT NULL');
						}
						if($db->query('SHOW COLUMNS FROM '.$dbColumn.' LIKE "'.$summaryVal['playerStatSummaryType'].'Losses"')->fetch_row()[0] == 0)
						{
							$db->query('ALTER TABLE '.$dbColumn.' ADD '.$summaryVal['playerStatSummaryType'].'Losses INT(11) NOT NULL');
						}
						$summaryStats[$summaryVal['playerStatSummaryType'].'Wins'] = $summaryVal['wins'];
						$summaryStats[$summaryVal['playerStatSummaryType'].'Losses'] = $summaryVal['losses'];
					}
				}
				else
				{
					if(array_key_exists($summaryVal['playerStatSummaryType'].'Wins',$summaryStats))
					{
						$summaryStats[$summaryVal['playerStatSummaryType'].'Wins'] = $summaryVal['wins'];
					}
					else
					{
						if($db->query('SHOW COLUMNS FROM '.$dbColumn.' LIKE "'.$summaryVal['playerStatSummaryType'].'Wins"')->fetch_row()[0] == 0)
						{
							$db->query('ALTER TABLE '.$dbColumn.' ADD '.$summaryVal['playerStatSummaryType'].'Wins INT(11) NOT NULL');
						}
						$summaryStats[$summaryVal['playerStatSummaryType'].'Wins'] = $summaryVal['wins'];
					}
				}
			}
			$rankedStats = array();
			$rankedStats['global'] = array();
			$rankedStats['champions'] = array();
			foreach($summonerStatsRankedApi['champions'] as $statsRanked)
			{
				$rankedStats['champions'][$statsRanked['id']] = array();
				$rankedStats['champions'][$statsRanked['id']]['games'] = $statsRanked['stats']['totalSessionsPlayed'];
				$rankedStats['champions'][$statsRanked['id']]['wins'] = $statsRanked['stats']['totalSessionsWon'];
				$rankedStats['champions'][$statsRanked['id']]['losses'] = $statsRanked['stats']['totalSessionsLost'];
				$rankedStats['champions'][$statsRanked['id']]['kills'] = $statsRanked['stats']['totalChampionKills'];
				$rankedStats['champions'][$statsRanked['id']]['deaths'] = $statsRanked['stats']['totalDeathsPerSession'];
				$rankedStats['champions'][$statsRanked['id']]['assists'] = $statsRanked['stats']['totalAssists'];
				$rankedStats['champions'][$statsRanked['id']]['maxKillsOnSingleGame'] = $statsRanked['stats']['maxChampionsKilled'];
				$rankedStats['champions'][$statsRanked['id']]['maxDeathsOnSingleGame'] = $statsRanked['stats']['maxNumDeaths'];
				$rankedStats['champions'][$statsRanked['id']]['firstBloods'] = $statsRanked['stats']['totalFirstBlood'];
				$rankedStats['champions'][$statsRanked['id']]['killsDouble'] = $statsRanked['stats']['totalDoubleKills'];
				$rankedStats['champions'][$statsRanked['id']]['killsTriple'] = $statsRanked['stats']['totalTripleKills'];
				$rankedStats['champions'][$statsRanked['id']]['killsQuadra'] = $statsRanked['stats']['totalQuadraKills'];
				$rankedStats['champions'][$statsRanked['id']]['killsPenta'] = $statsRanked['stats']['totalPentaKills'];
				$rankedStats['champions'][$statsRanked['id']]['cs'] = $statsRanked['stats']['totalMinionKills'];
				$rankedStats['champions'][$statsRanked['id']]['dmgDealtTotal'] = $statsRanked['stats']['totalDamageDealt'];
				$rankedStats['champions'][$statsRanked['id']]['dmgTakenTotal'] = $statsRanked['stats']['totalDamageTaken'];
				$rankedStats['champions'][$statsRanked['id']]['dmgDealtAd'] = $statsRanked['stats']['totalPhysicalDamageDealt'];
				$rankedStats['champions'][$statsRanked['id']]['dmgDealtAp'] = $statsRanked['stats']['totalMagicDamageDealt'];
				$rankedStats['champions'][$statsRanked['id']]['towerKills'] = $statsRanked['stats']['totalTurretsKilled'];
				$rankedStats['champions'][$statsRanked['id']]['goldEarnedTotal'] = $statsRanked['stats']['totalGoldEarned'];
			}
			$rankedStats['global'] = $rankedStats['champions'][0];
			if($statsAction[$i] == 'insert')
			{
				$queryConstructerFixed_columns = null;
				$queryConstructerFixed_data = null;
				foreach($summaryStats as $summary => $value)
				{
					if($summary != 'id' && $summary != 'revision' && $summary != 'ranked')
					{						
						$queryConstructerFixed_columns .= ','.$summary;
						$queryConstructerFixed_data .= ',"'.$value.'"';
					}
				}
				$db->query('INSERT INTO '.$dbColumn.' (id,revision,ranked'.$queryConstructerFixed_columns.') VALUES ("'.$summonerApi['id'].'","'.$summonerStatsRankedApi['modifyDate'].'","'.addslashes(json_encode($rankedStats)).'"'.$queryConstructerFixed_data.')') or die($db->error);
			}
			elseif($statsAction[$i] == 'update')
			{
				foreach($summaryStats as $summary => $value)
				{
					if($summary != 'id' && $summary != 'revision' && $summary != 'ranked')
					{	
						$queryConstructerFixed_update = null;
						$queryConstructerFixed_update .= ','.$summary.'="'.$value.'"';
					}
				}
				$db->query('UPDATE '.$dbColumn.' SET revision="'.$summonerStatsRankedApi['modifyDate'].'",ranked="'.addslashes(json_encode($rankedStats)).'"'.$queryConstructerFixed_update.' WHERE id="'.$summonerApi['id'].'"') or die($db->error);
			}
		}
	}
	foreach(explode(',',$config['api.seasons']) as $seasonVal)
	{
		$summonerInfo['stats'][$seasonVal] = array();
		if($seasonVal == ACTUAL_SEASON)
		{
			$dbColumn = 'api_summoners_stats';
		}
		else
		{
			$dbColumn = 'api_summoners_stats_'.$seasonVal;
		}
		$statsSummaryVal = $db->query('SELECT * FROM '.$dbColumn.' WHERE id="'.$summonerInfo['id'].'"')->fetch_row();
		$statsSummaryFields = $db->query('SELECT * FROM '.$dbColumn.' WHERE id="'.$summonerInfo['id'].'"')->fetch_fields();
		for($i = 0; $i < count($statsSummaryVal); $i++)
		{
			$summonerInfo['stats'][$seasonVal]['summary'][$statsSummaryFields[$i]->name] = $statsSummaryVal[$i];
		}
		$statsRankedVal = $db->query('SELECT ranked FROM '.$dbColumn.' WHERE id="'.$summonerInfo['id'].'"')->fetch_row();
		$summonerInfo['stats'][$seasonVal]['ranked'] = json_decode($statsRankedVal[0]);
	}
	/* Champion skill */
	if(empty($summonerInfo['leagues']['RANKED_SOLO_5x5']['mmr']))
	{
		$summonerMMR = 0;
	}
	$summonerMMR = (int) $summonerInfo['leagues']['RANKED_SOLO_5x5']['mmr'];
	$summonerChampSkill = array();
	if($db->query('SELECT id FROM api_summoners_champskill WHERE id="'.$summonerInfo['id'].'"')->num_rows == 0)
	{
		$db->query('INSERT INTO api_summoners_champskill VALUES ("'.$summonerInfo['id'].'","")');
	}
	foreach($summonerStatsRankedApiActualSeason['champions'] as $champSkillData)
	{
		$summonerThisChampSkill = round($summonerMMR + $champSkillData['stats']['totalSessionsPlayed'] - $champSkillData['stats']['totalSessionsLost'] + ($champSkillData['stats']['totalSessionsWon'] / 2) + ($champSkillData['stats']['totalChampionKills'] / 4) + ($champSkillData['stats']['totalAssists'] / 8) - ($champSkillData['stats']['totalDeathsPerSession'] / 4) + ($champSkillData['stats']['totalTurretsKilled'] * 1.5) + $champSkillData['stats']['mostChampionKillsPerSession'] - $champSkillData['stats']['maxNumDeaths'] + ($champSkillData['stats']['totalPentaKills'] * 10) + ($champSkillData['stats']['totalUnrealKills'] * 10) + ($champSkillData['stats']['totalQuadraKills'] * 5) + ($champSkillData['stats']['totalTripleKills'] * 2) + $champSkillData['stats']['totalFirstBlood'] + ($champSkillData['stats']['totalGoldEarned'] / 1000) + ($champSkillData['stats']['totalMinionKills'] / 1000) + ($champSkillData['stats']['totalDamageDealt'] / 1000000) + ($champSkillData['stats']['totalDamageTaken'] / 1000000));
		$summonerChampSkill[$champSkillData['id']] = $summonerThisChampSkill;
	}
	$db->query('UPDATE api_summoners_champskill SET data="'.addslashes(json_encode($summonerChampSkill)).'" WHERE id="'.$summonerInfo['id'].'"');
	$summonerInfo['champSkill'] = json_decode($db->query('SELECT data FROM api_summoners_champskill WHERE id="'.$summonerInfo['id'].'"')->fetch_row()[0],true);
} catch(Exception $e) {
	if($e->getMessage() != 'NOT_FOUND')
	{
		echo 'Se produjo un error recargando las estadísticas';
	}
};
try{
	if($db->query('SELECT id FROM api_summoners_actualgame WHERE id="'.$summonerApi['id'].'"')->num_rows == 0)
	{
		$db->query('INSERT INTO api_summoners_actualgame VALUES ("'.$summonerApi['id'].'","false","",0)');
	}
	
	if($db->query('SELECT time FROM api_summoners_actualgame WHERE id="'.$summonerApi['id'].'"')->fetch_row()[0] > ($core->time() - ($config['api.ongamecheck.interval']*1000)))
	{
		if($db->query('SELECT ongame FROM api_summoners_actualgame WHERE id="'.$summonerApi['id'].'"')->fetch_row()[0] == 'true')
		{
			$summonerInfo['actualGame']['status'] = true;
			$summonerInfo['actualGame']['info'] = $db->query('SELECT gameinfo FROM api_summoners_actualgame WHERE id="'.$summonerApi['id'].'"')->fetch_row()[0];
		}
		else
		{
			$summonerInfo['actualGame']['status'] = false;
		}
	}
	else
	{
		$summonerActualGameApi = $api->currentGame($summonerInfo['id'],$summonerRegion);
		if(is_array($summonerActualGameApi))
		{
			$db->query('UPDATE api_summoners_actualgame SET onGame="true",time="'.$core->time().'",gameinfo="'.addslashes(json_encode($summonerActualGameApi)).'" WHERE id="'.$summonerApi['id'].'"');
			$summonerInfo['actualGame']['status'] = true;
			$summonerInfo['actualGame']['info'] = $summonerActualGameApi;
		}
		else
		{
			$db->query('UPDATE api_summoners_actualgame SET onGame="false",gameinfo="", time="'.$core->time().'" WHERE id="'.$summonerApi['id'].'"');
			$summonerInfo['actualGame']['status'] = false;
		}
	}
	
} catch(Exception $e) {
	$db->query('UPDATE api_summoners_actualgame SET onGame="false",gameinfo="", time="'.$core->time().'" WHERE id="'.$summonerApi['id'].'"');
	$summonerInfo['actualGame']['status'] = false;
};
try{
	$summonerChampmasteryApi = $api->championMastery($summonerInfo['id'],$summonerRegion);
	$summonerInfo['champMastery'] = $summonerChampmasteryApi;
	if($db->query('SELECT id FROM api_summoners_champmastery WHERE id="'.$summonerApi['id'].'"')->num_rows == 0)
	{
		$champMasteryAction = 'insert';
	}
	elseif($reloadStatus == true)
	{
		$champMasteryAction = 'update';
	}
	if(!empty($champMasteryAction))
	{
		$summonerChampmasteryToDb = array();
		$summonerChampmasteryToDbLevel7 = 0;
		$summonerChampmasteryToDbLevel6 = 0;
		$summonerChampmasteryToDbPoints = 0;
		$summonerChampmasteryMainChampId = 0;
		$summonerChampmasteryMainChampPoints = 0;
		foreach($summonerChampmasteryApi as $champMasterySingleData)
		{
			$summonerChampmasteryToDb[$champMasterySingleData['championId']] = array();
			if($champMasterySingleData['championLevel'] == 7) { $summonerChampmasteryToDbLevel7++; }
			if($champMasterySingleData['championLevel'] == 6) { $summonerChampmasteryToDbLevel6++; }
			$summonerChampmasteryToDbPoints = $summonerChampmasteryToDbPoints + $champMasterySingleData['championPoints'];
			if($summonerChampmasteryMainChampPoints < $champMasterySingleData['championPoints']) { $summonerChampmasteryMainChampPoints = $champMasterySingleData['championPoints']; $summonerChampmasteryMainChampId = $champMasterySingleData['championId']; }
			$summonerChampmasteryToDb[$champMasterySingleData['championId']]['level'] = $champMasterySingleData['championLevel'];
			$summonerChampmasteryToDb[$champMasterySingleData['championId']]['points'] = $champMasterySingleData['championPoints'];
			$summonerChampmasteryToDb[$champMasterySingleData['championId']]['lastTimePlayed'] = $champMasterySingleData['lastPlayTime'];
			$summonerChampmasteryToDb[$champMasterySingleData['championId']]['chestGranted'] = $champMasterySingleData['chestGranted'];
			$summonerChampmasteryToDb[$champMasterySingleData['championId']]['plusPointsLevel'] = $champMasterySingleData['championPointsSinceLastLevel'];
			$summonerChampmasteryToDb[$champMasterySingleData['championId']]['neededPointsLevel'] = $champMasterySingleData['championPointsUntilNextLevel'];
		}
		if($champMasteryAction == 'insert')
		{
			$db->query('INSERT INTO api_summoners_champmastery VALUES ("'.$summonerApi['id'].'","'.addslashes(json_encode($summonerChampmasteryToDb)).'","'.$summonerChampmasteryMainChampId.'","'.$summonerChampmasteryToDbPoints.'","'.$summonerChampmasteryToDbLevel7.'","'.$summonerChampmasteryToDbLevel6.'")');
		}
		elseif($champMasteryAction == 'update')
		{
			$db->query('UPDATE api_summoners_champmastery SET data="'.addslashes(json_encode($summonerChampmasteryToDb)).'", mainChampId="'.$summonerChampmasteryMainChampId.'", totalPoints="'.$summonerChampmasteryToDbPoints.'", totalLevels7="'.$summonerChampmasteryToDbLevel7.'", totalLevels6="'.$summonerChampmasteryToDbLevel6.'" WHERE id="'.$summonerApi['id'].'"');	
		}
	}
} catch(Exception $e) {
	if($e->getMessage() != 'NOT_FOUND')
	{
		echo 'Se produjo un error recargando la maestria de campeon';
	}
};
try{
	$summonerRecentGamesApi = $api->recentGames($summonerInfo['id'],$summonerRegion);
	if(is_array($summonerRecentGamesApi['games']))
	{
		foreach($summonerRecentGamesApi['games'] as $recentGameData)
		{
			if($db->query('SELECT id FROM api_matches WHERE id="'.$recentGameData['gameId'].'"')->num_rows == 0)
			{
				$matchAction = 'insert';
			}
			else
			{
				if(array_key_exists($summonerApi['id'],json_decode($db->query('SELECT data FROM api_matches WHERE id="'.$recentGameData['gameId'].'"')->fetch_row()[0],true)))
				{
					if(json_decode($db->query('SELECT data FROM api_matches WHERE id="'.$recentGameData['gameId'].'"')->fetch_row()[0],true)[$summonerApi['id']]['updated'] != true)
					{
						$matchAction = 'update';
					}
				}
				else
				{
					$matchAction = 'update';
				}
			}
			if(!empty($matchAction))
			{
				if($recentGameData['gameType'] == 'CUSTOM_GAME')
				{
					$recentGameDataGameType = 'CUSTOM_GAME';
				}
				elseif($recentGameData['gameType'] == 'TUTORIAL_GAME')
				{
					$recentGameDataGameType = 'TUTORIAL_GAME';
				}
				elseif(true)
				{
					$recentGameDataGameType = $recentGameData['subType'];
				}
				if($recentGameData['stats']['win'] == true)
				{
					$recentGameWinnerTeamId = $recentGameData['teamId'];
				}
				else
				{
					if($recentGameData['teamId'] == 100)
					{
						$recentGameWinnerTeamId = 200;
					}
					else
					{
						$recentGameWinnerTeamId = 100;
					}
				}
				$matchDataInfo = array('id' => $recentGameData['gameId'], 'invalid' => $recentGameData['invalid'], 'gameType' => $recentGameDataGameType, 'mapId' => $recentGameData['mapId'], 'createDate' => $recentGameData['createDate'], 'playerIds' => null, 'winner' => $recentGameWinnerTeamId);
				if($matchAction == 'insert')
				{
					$recentGameDataPlayerIds = array();
					$recentGameDataPlayerData = array();
					$recentGameDataPlayerIds[] = $summonerApi['id'];
					$recentGameDataPlayerData[$summonerApi['id']] = array();
					$recentGameDataPlayerData[$summonerApi['id']]['updated'] = true;
					$recentGameDataPlayerData[$summonerApi['id']]['team'] = $recentGameData['teamId'];
					$recentGameDataPlayerData[$summonerApi['id']]['team'] = $recentGameData['teamId'];
					$recentGameDataPlayerData[$summonerApi['id']]['champ'] = $recentGameData['championId'];
					$recentGameDataPlayerData[$summonerApi['id']]['spell1'] = $recentGameData['spell1'];
					$recentGameDataPlayerData[$summonerApi['id']]['spell2'] = $recentGameData['spell2'];
					$recentGameDataPlayerData[$summonerApi['id']]['ipEarned'] = $recentGameData['ipEarned'];
					$recentGameDataPlayerData[$summonerApi['id']]['stats'] = $recentGameData['stats'];
					if(array_key_exists('fellowPlayers',$recentGameData))
					{
						foreach($recentGameData['fellowPlayers'] as $playerData)
						{
							$recentGameDataPlayerIds[] = $playerData['summonerId'];
							$recentGameDataPlayerData[$playerData['summonerId']] = array();
							$recentGameDataPlayerData[$playerData['summonerId']]['updated'] = false;
							$recentGameDataPlayerData[$playerData['summonerId']]['team'] = $playerData['teamId'];
							$recentGameDataPlayerData[$playerData['summonerId']]['champ'] = $playerData['championId'];
						}
					}
					else
					{
						$recentGameDataPlayerData = array();
					}
					
					$matchDataInfo['playerIds'] = implode(',',$recentGameDataPlayerIds);
					$db->query('INSERT INTO api_matches VALUES ("'.$matchDataInfo['id'].'","'.$summonerRegion.'","'.$matchDataInfo['invalid'].'","'.$matchDataInfo['gameType'].'","'.$matchDataInfo['mapId'].'","'.$matchDataInfo['createDate'].'","'.$matchDataInfo['playerIds'].'","'.addslashes(json_encode($recentGameDataPlayerData)).'","'.$matchDataInfo['winner'].'",0)');
				}
				elseif($matchAction == 'update')
				{
					$recentGamePreData = json_decode($db->query('SELECT data FROM api_matches WHERE id="'.$matchDataInfo['id'].'"')->fetch_row()[0],true);
					$recentGamePreData[$summonerApi['id']]['updated'] = true;
					$recentGamePreData[$summonerApi['id']]['team'] = $recentGameData['teamId'];
					$recentGamePreData[$summonerApi['id']]['team'] = $recentGameData['teamId'];
					$recentGamePreData[$summonerApi['id']]['champ'] = $recentGameData['championId'];
					$recentGamePreData[$summonerApi['id']]['spell1'] = $recentGameData['spell1'];
					$recentGamePreData[$summonerApi['id']]['spell2'] = $recentGameData['spell2'];
					$recentGamePreData[$summonerApi['id']]['ipEarned'] = $recentGameData['ipEarned'];
					$recentGamePreData[$summonerApi['id']]['stats'] = $recentGameData['stats'];
					$recentGamePreData = addslashes(json_encode($recentGamePreData));
					$db->query('UPDATE api_matches SET data="'.$recentGamePreData.'" WHERE id="'.$matchDataInfo['id'].'"');
				}
			}
		}
	}
	$summonerInfo['games'] = array();
	$summonerInfo['games']['wins'] = 0;
	$summonerInfo['games']['losses'] = 0;
	$summonerInfo['games']['kills'] = 0;
	$summonerInfo['games']['deaths'] = 0;
	$summonerInfo['games']['assists'] = 0;
	$summonerInfo['games']['cs'] = 0;
	$summonerInfo['games']['recent'] = array();
	$i = 0;
	$recentSummonerGames = $db->query('SELECT id,invalid,gameType,mapId,createDate,data FROM api_matches WHERE playerIds LIKE "%'.$summonerInfo['id'].'%" ORDER BY createDate DESC');
	while($row = $recentSummonerGames->fetch_row())
	{
		$summonerInfo['games']['recent'][$i]['gameId'] = $row[0];
		$summonerInfo['games']['recent'][$i]['invalid'] = $row[1];
		$summonerInfo['games']['recent'][$i]['gameType'] = $row[2];
		$summonerInfo['games']['recent'][$i]['mapId'] = $row[3];
		$summonerInfo['games']['recent'][$i]['createDate'] = $row[4];
		$playerInfoBase = json_decode($row[5],true);
		if(array_key_exists($summonerInfo['id'],$playerInfoBase))
		{
			$playerInfo = $playerInfoBase[$summonerInfo['id']];
			$summonerInfo['games']['recent'][$i]['teamId'] = (int) @$playerInfo['team'];
			$summonerInfo['games']['recent'][$i]['championId'] = (int) @$playerInfo['champ'];
			$summonerInfo['games']['recent'][$i]['spell1'] = (int) @$playerInfo['spell1'];
			$summonerInfo['games']['recent'][$i]['spell2'] = (int) @$playerInfo['spell2'];
			$summonerInfo['games']['recent'][$i]['ipEarned'] = (int) @$playerInfo['ipEarned'];
			$summonerInfo['games']['kills'] += (int) @$playerInfo['stats']['championsKilled'];
			$summonerInfo['games']['deaths'] += (int) @$playerInfo['stats']['numDeaths'];
			$summonerInfo['games']['assists'] += (int) @$playerInfo['stats']['assists'];
			$summonerInfo['games']['cs'] += ((int) @$playerInfo['stats']['minionsKilled'] + (int) @$playerInfo['stats']['neutralMinionsKilled'] + (int) @$playerInfo['stats']['turretsKilled'] + (int) @$playerInfo['stats']['wardKilled']);
			$summonerInfo['games']['recent'][$i]['fellowPlayers'] = $playerInfoBase;
			$summonerInfo['games']['recent'][$i]['stats'] = (array) @$playerInfo['stats'];
			if(empty($summonerInfo['games']['recent'][$i]['stats']['win'])){ $summonerInfo['games']['recent'][$i]['stats']['win'] = false; }
			if($i < $config['web.summoner.recentgames.stats.limit'])
			{
				if(!empty($playerInfo['stats']['win']) && @$playerInfo['stats']['win'] == true)
				{
					$summonerInfo['games']['wins']++;
				}
				else
				{
					$summonerInfo['games']['losses']++;
				}
			}
			$i++;
		}
	}
} catch(Exception $e) {
	if($e->getMessage() != 'NOT_FOUND')
	{
		echo 'Se produjo un error recargando las ultimas partidas';
	}
};