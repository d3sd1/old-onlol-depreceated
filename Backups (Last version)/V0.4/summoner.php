<?php
include('kernel/core.php');
$region = $core->regionDepure($_GET['region']);
try {
	$api->forceUpdate(false);
	/* Basic info */
	$summonerApi = $api->summonerByName("$_GET[name]",$region)[mb_strtolower(str_replace(' ',null,$_GET['name']), 'UTF-8')];
	if($db->query('SELECT id FROM api_summoners WHERE name="'.$_GET['name'].'"')->num_rows == 0)
	{
		$db->query('INSERT INTO api_summoners (id,region,name,icon,level,revision) VALUES ("'.$summonerApi['id'].'","'.$region.'","'.$summonerApi['name'].'","'.$summonerApi['profileIconId'].'","'.$summonerApi['summonerLevel'].'","'.$summonerApi['revisionDate'].'")');
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
		$db->query('UPDATE api_summoners SET region="'.$region.'",name="'.$summonerApi['name'].'",icon="'.$summonerApi['profileIconId'].'",level="'.$summonerApi['summonerLevel'].'",revision="'.$summonerApi['revisionDate'].'" WHERE name="'.$summonerApi['name'].'"');
		$summonerQuery = $db->query('SELECT id,region,name,icon,level,revision FROM api_summoners WHERE name="'.$summonerApi['name'].'"')->fetch_row();
	}
	$summonerInfo = array('id' => $summonerQuery[0],'region' => $region,'name' => $summonerQuery[2],'icon' => $summonerQuery[3],'level' => $summonerQuery[4],'revision' => $summonerQuery[5], 'leagues' => array(), 'runes' => array(), 'masteries' => array(), 'stats' => array(ACTUAL_SEASON => array()), 'actualGame' => array(), 'champMastery' => array(), 'champSkill' => array(), 'games' => array());
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
	$summonerLeagueApi = $api->league($summonerInfo['id'],$region,true)[$summonerInfo['id']];
	$summonerMMR = $api->getMMR($summonerInfo['id'],$region);
	$summonerLeagueSoloQ = array('tier' => 'UNRANKED', 'entries' => array('0' => array('division' => 'V', 'playerOrTeamName' => 'NONE', 'leaguePoints' => 0, 'wins' => 0, 'losses' => 0, 'playstyle' => 'NONE', 'isHotStreak' => 0, 'isVeteran' => 0, 'isFreshBlood' => 0, 'isInactive' => 0)));
	$summonerLeagueTeam3x3 = array('tier' => 'UNRANKED', 'entries' => array('0' => array('division' => 'V', 'playerOrTeamName' => 'NONE', 'leaguePoints' => 0, 'wins' => 0, 'losses' => 0, 'isHotStreak' => 0, 'isVeteran' => 0, 'isFreshBlood' => 0, 'isInactive' => 0)));
	$summonerLeagueTeam5x5 = array('tier' => 'UNRANKED', 'entries' => array('0' => array('division' => 'V', 'playerOrTeamName' => 'NONE', 'leaguePoints' => 0, 'wins' => 0, 'losses' => 0, 'isHotStreak' => 0, 'isVeteran' => 0, 'isFreshBlood' => 0, 'isInactive' => 0)));
		$summonerTeams = array();
	foreach($summonerLeagueApi as $leagueData)
	{
		(empty($leagueData['entries']['division'])) ? $leagueData['entries']['division'] = 'UNRANKED':null;
		if($leagueData['queue'] == 'RANKED_SOLO_5x5')
		{
			$summonerLeagueSoloQ = $leagueData;
		}
		elseif($leagueData['queue'] == 'RANKED_TEAM_3x3')
		{
			if($core->compareLeague($leagueData['tier'],$leagueData['entries']['division'],$summonerLeagueTeam3x3['tier'],$summonerLeagueTeam3x3['entries'][0]['division']) == 1)
			{
				$summonerLeagueTeam3x3 = $leagueData;
				$summonerTeams[] = $leagueData['entries'][0]['playerOrTeamId'];
			}
		}
		elseif($leagueData['queue'] == 'RANKED_TEAM_5x5')
		{
			if($core->compareLeague($leagueData['tier'],$leagueData['entries']['division'],$summonerLeagueTeam5x5['tier'],$summonerLeagueTeam5x5['entries'][0]['division']) == 1)
			{
				$summonerLeagueTeam5x5 = $leagueData;
				$summonerTeams[] = $leagueData['entries'][0]['playerOrTeamId'];
			}
		}
	}
		
	if($db->query('SELECT id FROM api_summoners_league WHERE id="'.$summonerInfo['id'].'"')->num_rows == 0)
	{
		$db->query('INSERT INTO api_summoners_league VALUES ("'.$summonerApi['id'].'","'.$summonerLeagueSoloQ['tier'].'","'.$summonerLeagueSoloQ['entries'][0]['division'].'","'.$summonerLeagueSoloQ['entries'][0]['leaguePoints'].'","'.$summonerLeagueSoloQ['entries'][0]['wins'].'","'.$summonerLeagueSoloQ['entries'][0]['losses'].'","'.$summonerLeagueSoloQ['entries'][0]['playstyle'].'","'.$summonerLeagueSoloQ['entries'][0]['isHotStreak'].'","'.$summonerLeagueSoloQ['entries'][0]['isVeteran'].'","'.$summonerLeagueSoloQ['entries'][0]['isFreshBlood'].'","'.$summonerLeagueSoloQ['entries'][0]['isInactive'].'", "'.$summonerMMR['MMR'].'", "'.implode(';',$summonerTeams).'", "'.$summonerLeagueTeam5x5['tier'].'","'.$summonerLeagueTeam5x5['entries'][0]['division'].'","'.$summonerLeagueTeam5x5['entries'][0]['leaguePoints'].'",
			"'.$summonerLeagueTeam5x5['entries'][0]['wins'].'","'.$summonerLeagueTeam5x5['entries'][0]['losses'].'","'.$summonerLeagueTeam5x5['entries'][0]['isHotStreak'].'","'.$summonerLeagueTeam5x5['entries'][0]['isVeteran'].'","'.$summonerLeagueTeam5x5['entries'][0]['isFreshBlood'].'","'.$summonerLeagueTeam5x5['entries'][0]['isInactive'].'","'.$summonerLeagueTeam5x5['entries'][0]['playerOrTeamName'].'", "'.$summonerLeagueTeam3x3['tier'].'","'.$summonerLeagueTeam3x3['entries'][0]['division'].'","'.$summonerLeagueTeam3x3['entries'][0]['leaguePoints'].'",
			"'.$summonerLeagueTeam3x3['entries'][0]['wins'].'","'.$summonerLeagueTeam3x3['entries'][0]['losses'].'","'.$summonerLeagueTeam3x3['entries'][0]['isHotStreak'].'","'.$summonerLeagueTeam3x3['entries'][0]['isVeteran'].'","'.$summonerLeagueTeam3x3['entries'][0]['isFreshBlood'].'",
			"'.$summonerLeagueTeam3x3['entries'][0]['isInactive'].'","'.$summonerLeagueTeam3x3['entries'][0]['playerOrTeamName'].'")') or die($db->error);
	}
	elseif($reloadStatus == true)
	{
		$db->query('UPDATE api_summoners_league SET soloq_tier="'.$summonerLeagueSoloQ['tier'].'",soloq_division="'.$summonerLeagueSoloQ['entries'][0]['division'].'",soloq_lp="'.$summonerLeagueSoloQ['entries'][0]['leaguePoints'].'",soloq_wins="'.$summonerLeagueSoloQ['entries'][0]['wins'].'",soloq_losses="'.$summonerLeagueSoloQ['entries'][0]['losses'].'",soloq_playstyle="'.$summonerLeagueSoloQ['entries'][0]['playstyle'].'",soloq_isHotStreak="'.$summonerLeagueSoloQ['entries'][0]['isHotStreak'].'",soloq_isVeteran="'.$summonerLeagueSoloQ['entries'][0]['isVeteran'].'",soloq_isFreshBlood="'.$summonerLeagueSoloQ['entries'][0]['isFreshBlood'].'",soloq_isInactive="'.$summonerLeagueSoloQ['entries'][0]['isInactive'].'",mmr="'.$summonerMMR['MMR'].'", teams="'.implode(';',$summonerTeams).'",5x5_tier="'.$summonerLeagueTeam5x5['tier'].'",5x5_division="'.$summonerLeagueTeam5x5['entries'][0]['division'].'",5x5_lp="'.$summonerLeagueTeam5x5['entries'][0]['leaguePoints'].'",5x5_wins="'.$summonerLeagueTeam5x5['entries'][0]['wins'].'",5x5_losses="'.$summonerLeagueTeam5x5['entries'][0]['losses'].'",5x5_isHotStreak="'.$summonerLeagueTeam5x5['entries'][0]['isHotStreak'].'",5x5_isVeteran="'.$summonerLeagueTeam5x5['entries'][0]['isVeteran'].'",5x5_isFreshBlood="'.$summonerLeagueTeam5x5['entries'][0]['isFreshBlood'].'",5x5_isInactive="'.$summonerLeagueTeam5x5['entries'][0]['isInactive'].'",5x5_name="'.$summonerLeagueTeam5x5['entries'][0]['playerOrTeamName'].'",5x5_tier="'.$summonerLeagueTeam5x5['tier'].'",3x3_division="'.$summonerLeagueTeam3x3['entries'][0]['division'].'",3x3_lp="'.$summonerLeagueTeam3x3['entries'][0]['leaguePoints'].'",3x3_wins="'.$summonerLeagueTeam3x3['entries'][0]['wins'].'",3x3_losses="'.$summonerLeagueTeam3x3['entries'][0]['losses'].'",3x3_isHotStreak="'.$summonerLeagueTeam3x3['entries'][0]['isHotStreak'].'",3x3_isVeteran="'.$summonerLeagueTeam3x3['entries'][0]['isVeteran'].'",3x3_isFreshBlood="'.$summonerLeagueTeam3x3['entries'][0]['isFreshBlood'].'",3x3_isInactive="'.$summonerLeagueTeam3x3['entries'][0]['isInactive'].'",3x3_name="'.$summonerLeagueTeam3x3['entries'][0]['playerOrTeamName'].'" WHERE id="'.$summonerApi['id'].'"');
	}
	
	$summonerLeagueQuery = $db->query('SELECT * FROM api_summoners_league WHERE id="'.$summonerApi['id'].'"')->fetch_row();
	$summonerInfo['leagues'] = array('RANKED_SOLO_5x5' => array('tier' => $summonerLeagueQuery[1],'division' => $summonerLeagueQuery[2],'lp' => $summonerLeagueQuery[3],'wins' => $summonerLeagueQuery[4],'losses' => $summonerLeagueQuery[5],'playstyle' => $summonerLeagueQuery[6], 'isHotStreak' => $summonerLeagueQuery[7], 'isVeteran' => $summonerLeagueQuery[8], 'isFreshBlood' => $summonerLeagueQuery[9], 'isInactive' => $summonerLeagueQuery[10], 'mmr' => $summonerLeagueQuery[11]),'RANKED_TEAM_5x5' => array('tier' => $summonerLeagueQuery[13],'division' => $summonerLeagueQuery[14],'lp' => $summonerLeagueQuery[15],'wins' => $summonerLeagueQuery[16],'losses' => $summonerLeagueQuery[17], 'isHotStreak' => $summonerLeagueQuery[18], 'isVeteran' => $summonerLeagueQuery[19], 'isFreshBlood' => $summonerLeagueQuery[20], 'isInactive' => $summonerLeagueQuery[21], 'name' => $summonerLeagueQuery[22]),'RANKED_TEAM_3x3' => array('tier' => $summonerLeagueQuery[23],'division' => $summonerLeagueQuery[24],'lp' => $summonerLeagueQuery[25],'wins' => $summonerLeagueQuery[26],'losses' => $summonerLeagueQuery[27], 'isHotStreak' => $summonerLeagueQuery[28], 'isVeteran' => $summonerLeagueQuery[29], 'isFreshBlood' => $summonerLeagueQuery[30], 'isInactive' => $summonerLeagueQuery[31], 'name' => $summonerLeagueQuery[32]));
} catch(Exception $e) {
	if($e->getMessage() != 'NOT_FOUND')
	{
		echo 'Se produjo un error recargando las ligas ';
	}
};
try{
	$summonerRunesApi = $api->summonerById($summonerInfo['id'],$region,'runes');
	$summonerInfo['runes'] = $summonerRunesApi[$summonerApi['id']]['pages'];
	$summonerMasteriesApi = $api->summonerById($summonerInfo['id'],$region,'masteries');
	$summonerInfo['masteries'] = $summonerMasteriesApi[$summonerApi['id']]['pages'];
} catch(Exception $e) {
	if($e->getMessage() != 'NOT_FOUND')
	{
		echo 'Se produjo un error recargando las runas y maestrias';
	}
};
try{
	$summonerStatsSummaryApi = $api->stats($summonerInfo['id'],$region,'summary');
	$summonerInfo['stats'][ACTUAL_SEASON]['summary'] = $summonerStatsSummaryApi['playerStatSummaries'];
	$summonerStatsRankedApi = $api->stats($summonerInfo['id'],$region,'ranked');
	$summonerInfo['stats'][ACTUAL_SEASON]['ranked'] = $summonerStatsRankedApi['champions'];
	if($db->query('SELECT id FROM api_summoners_stats WHERE id="'.$summonerInfo['id'].'"')->num_rows == 0)
	{
		$statsAction = 'insert';
	}
	elseif($summonerStatsRankedApi['modifyDate'] != $db->query('SELECT revision FROM api_summoners_stats WHERE id="'.$summonerApi['id'].'"')->fetch_row()[0])
	{
		$statsAction = 'update';
	}
	if(!empty($statsAction))
	{
		$summaryStats = array('CoopVsAIWins' => 0,'CoopVsAI3x3Wins' => 0,'RankedTeam3x3Wins' => 0,'RankedTeam3x3Losses' => 0,'RankedTeam5x5Wins' => 0,'RankedTeam5x5Losses' => 0,'CAP5x5Wins' => 0,'OdinUnrankedWins' => 0,'Unranked3x3Wins' => 0,'OneForAll5x5Wins' => 0,'URFWins' => 0,'KingPoroWins' => 0,'AramUnranked5x5Wins' => 0,'UnrankedWins' => 0,'RankedSolo5x5Wins' => 0,'RankedSolo5x5Losses' => 0,'AscensionWins' => 0,'BilgewaterWins' => 0,'CounterPickWins' => 0,'FirstBlood1x1Wins' => 0,'FirstBlood2x2Wins' => 0,'HexakillWins' => 0,'NightmareBotWins' => 0,'SummonersRift6x6Wins' => 0,'URFBotsWins' => 0,'SiegeWins' => 0);
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
					$core->setNotify('Update needed on summoner -> Stats (SUMMARY). '.$summaryVal['playerStatSummaryType'].' needs to be added to database and stuff.','summonerStatsSummary_'.$summaryVal['playerStatSummaryType']);
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
					$core->setNotify('Update needed on summoner -> Stats (SUMMARY). '.$summaryVal['playerStatSummaryType'].' needs to be added to database and stuff.','summonerStatsSummary_'.$summaryVal['playerStatSummaryType']);
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
		if($statsAction == 'insert')
		{
			$db->query('INSERT INTO api_summoners_stats VALUES ("'.$summonerApi['id'].'","'.$summonerStatsRankedApi['modifyDate'].'","'.addslashes(json_encode($rankedStats)).'","'.$summaryStats['CoopVsAIWins'].'","'.$summaryStats['CoopVsAI3x3Wins'].'","'.$summaryStats['RankedTeam3x3Wins'].'","'.$summaryStats['RankedTeam3x3Losses'].'","'.$summaryStats['RankedTeam5x5Wins'].'","'.$summaryStats['RankedTeam5x5Losses'].'","'.$summaryStats['CAP5x5Wins'].'","'.$summaryStats['OdinUnrankedWins'].'","'.$summaryStats['Unranked3x3Wins'].'","'.$summaryStats['OneForAll5x5Wins'].'","'.$summaryStats['URFWins'].'","'.$summaryStats['KingPoroWins'].'","'.$summaryStats['AramUnranked5x5Wins'].'","'.$summaryStats['UnrankedWins'].'","'.$summaryStats['RankedSolo5x5Wins'].'","'.$summaryStats['RankedSolo5x5Losses'].'","'.$summaryStats['AscensionWins'].'","'.$summaryStats['BilgewaterWins'].'","'.$summaryStats['CounterPickWins'].'","'.$summaryStats['FirstBlood1x1Wins'].'","'.$summaryStats['FirstBlood2x2Wins'].'","'.$summaryStats['HexakillWins'].'","'.$summaryStats['NightmareBotWins'].'","'.$summaryStats['SummonersRift6x6Wins'].'","'.$summaryStats['URFBotsWins'].'","'.$summaryStats['SiegeWins'].'")') or die($db->error);
		}
		elseif($statsAction == 'update')
		{
			$db->query('UPDATE api_summoners_stats SET revision="'.$summonerStatsRankedApi['modifyDate'].'",ranked="'.addslashes(json_encode($rankedStats)).'",CoopVsAIWins="'.$summaryStats['CoopVsAIWins'].'",CoopVsAI3x3Wins="'.$summaryStats['CoopVsAI3x3Wins'].'",RankedTeam3x3Wins="'.$summaryStats['RankedTeam3x3Wins'].'",RankedTeam3x3Losses="'.$summaryStats['RankedTeam3x3Losses'].'",RankedTeam5x5Wins="'.$summaryStats['RankedTeam5x5Wins'].'",RankedTeam5x5Losses="'.$summaryStats['RankedTeam5x5Losses'].'",CAP5x5Wins="'.$summaryStats['CAP5x5Wins'].'",OdinUnrankedWins="'.$summaryStats['OdinUnrankedWins'].'",Unranked3x3Wins="'.$summaryStats['Unranked3x3Wins'].'",OneForAll5x5Wins="'.$summaryStats['OneForAll5x5Wins'].'",URFWins="'.$summaryStats['URFWins'].'",KingPoroWins="'.$summaryStats['KingPoroWins'].'",AramUnranked5x5Wins="'.$summaryStats['AramUnranked5x5Wins'].'",UnrankedWins="'.$summaryStats['UnrankedWins'].'",RankedSolo5x5Wins="'.$summaryStats['RankedSolo5x5Wins'].'",RankedSolo5x5Losses="'.$summaryStats['RankedSolo5x5Losses'].'",AscensionWins="'.$summaryStats['AscensionWins'].'",BilgewaterWins="'.$summaryStats['BilgewaterWins'].'",CounterPickWins="'.$summaryStats['CounterPickWins'].'",FirstBlood1x1Wins="'.$summaryStats['FirstBlood1x1Wins'].'",FirstBlood2x2Wins="'.$summaryStats['FirstBlood2x2Wins'].'",HexakillWins="'.$summaryStats['HexakillWins'].'",NightmareBotWins="'.$summaryStats['NightmareBotWins'].'",SummonersRift6x6Wins="'.$summaryStats['SummonersRift6x6Wins'].'",URFBotsWins="'.$summaryStats['URFBotsWins'].'",SiegeWins="'.$summaryStats['SiegeWins'].'" WHERE id="'.$summonerApi['id'].'"');
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
		
		foreach($summonerInfo['stats'][ACTUAL_SEASON]['ranked'] as $champSkillData)
		{
			$summonerThisChampSkill = round($summonerMMR + $champSkillData['stats']['totalSessionsPlayed'] - $champSkillData['stats']['totalSessionsLost'] + ($champSkillData['stats']['totalSessionsWon'] / 2) + ($champSkillData['stats']['totalChampionKills'] / 4) + ($champSkillData['stats']['totalAssists'] / 8) - ($champSkillData['stats']['totalDeathsPerSession'] / 4) + ($champSkillData['stats']['totalTurretsKilled'] * 1.5) + $champSkillData['stats']['mostChampionKillsPerSession'] - $champSkillData['stats']['maxNumDeaths'] + ($champSkillData['stats']['totalPentaKills'] * 10) + ($champSkillData['stats']['totalUnrealKills'] * 10) + ($champSkillData['stats']['totalQuadraKills'] * 5) + ($champSkillData['stats']['totalTripleKills'] * 2) + $champSkillData['stats']['totalFirstBlood'] + ($champSkillData['stats']['totalGoldEarned'] / 1000) + ($champSkillData['stats']['totalMinionKills'] / 1000) + ($champSkillData['stats']['totalDamageDealt'] / 1000000) + ($champSkillData['stats']['totalDamageTaken'] / 1000000));
			$summonerChampSkill[$champSkillData['id']] = $summonerThisChampSkill;
		}
		$db->query('UPDATE api_summoners_champskill SET data="'.addslashes(json_encode($summonerChampSkill)).'" WHERE id="'.$summonerInfo['id'].'"');
	}
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
		$summonerActualGameApi = $api->currentGame($summonerInfo['id'],$region);
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
	$summonerChampmasteryApi = $api->championMastery($summonerInfo['id'],$region);
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
	echo 'Se produjo un error recargando la maestria de campeon';
};
try{
	$summonerRecentGamesApi = $api->recentGames($summonerInfo['id'],$region);
	$summonerInfo['games'] = $summonerRecentGamesApi['games'];
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
				if(json_decode($db->query('SELECT data FROM api_matches WHERE id="'.$recentGameData['gameId'].'"')->fetch_row()[0],true)[$summonerApi['id']]['updated'] != true)
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
					
					foreach($recentGameData['fellowPlayers'] as $playerData)
					{
						$recentGameDataPlayerIds[] = $playerData['summonerId'];
						$recentGameDataPlayerData[$playerData['summonerId']] = array();
						$recentGameDataPlayerData[$playerData['summonerId']]['updated'] = false;
						$recentGameDataPlayerData[$playerData['summonerId']]['team'] = $playerData['teamId'];
						$recentGameDataPlayerData[$playerData['summonerId']]['champ'] = $playerData['championId'];
					}
					
					$matchDataInfo['playerIds'] = implode(',',$recentGameDataPlayerIds);
					$db->query('INSERT INTO api_matches VALUES ("'.$matchDataInfo['id'].'","'.$region.'","'.$matchDataInfo['invalid'].'","'.$matchDataInfo['gameType'].'","'.$matchDataInfo['mapId'].'","'.$matchDataInfo['createDate'].'","'.$matchDataInfo['playerIds'].'","'.addslashes(json_encode($recentGameDataPlayerData)).'","'.$matchDataInfo['winner'].'",0)');
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
} catch(Exception $e) {
	echo 'Se produjo un error recargando las ultimas partidas';
};
$pageName = 'title.summoner'; //Lang key
$pageNameVarKey = '{summoner}'; 
$pageNameVarVal = $summonerInfo['name']; 
$pageTemplates = '<link href="'.URL.'/style/css/footable.core.css" rel="stylesheet"><link href="'.URL.'/style/css/bootstrap-select.min.css" rel="stylesheet">'; // CSS Scripts to load
$pageScripts = '<script src="'.URL.'/style/js/footable.all.min.js"></script><script src="'.URL.'/style/js/bootstrap-select.min.js" type="text/javascript"></script>'; // JS Scripts to load
require('kernel/template/header.tpl');
?>
  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="row bg-title">
       
      </div>
      <!-- /.row -->
      <!-- .row -->
      <div class="row">
        <div class="col-md-4 col-xs-12">
          <div class="white-box">
            <div class="user-bg"> <img width="100%" alt="user" src="<?php echo URL ?>/style/game/champions/splash/<?php echo $summonerInfo['champMastery'][0]['championId'] ?>_0.jpg">
              <div class="overlay-box">
                <div class="user-content"> <a href="javascript:void(0)"><img src="<?php echo URL ?>/style/game/summoners/icons/<?php echo $summonerInfo['icon'] ?>.png" class="thumb-lg img-circle" alt="summonerIcon" draggable="false"></a>
                  <h4 class="text-white"><?php echo $summonerInfo['name'] ?></h4>
                  <h5 class="text-white"><?php echo $lang['profile.level'].' '.$summonerInfo['level'] ?></h5>
                </div>
              </div>
            </div>
            <div class="user-btm-box">
              <div class="col-md-4 col-sm-4 text-center">
                <h1>3v3</h1>
                <img draggable="false" width="150px" height="150px" src="<?php echo URL ?>/style/game/summoners/divisions/<?php echo $summonerInfo['leagues']['RANKED_TEAM_3x3']['tier'] ?>/<?php echo $summonerInfo['leagues']['RANKED_TEAM_3x3']['division'] ?>.png">
                <h3><?php echo $lang['league.'.$summonerInfo['leagues']['RANKED_TEAM_3x3']['tier']].' '.($summonerInfo['leagues']['RANKED_TEAM_3x3']['tier'] != 'UNRANKED' ? $summonerInfo['leagues']['RANKED_TEAM_3x3']['division'] : null) ?></h3>
              </div>
              <div class="col-md-4 col-sm-4 text-center">
                <h1>SoloQ</h1>
                <img draggable="false" width="150px" height="150px" src="<?php echo URL ?>/style/game/summoners/divisions/<?php echo $summonerInfo['leagues']['RANKED_SOLO_5x5']['tier'] ?>/<?php echo $summonerInfo['leagues']['RANKED_SOLO_5x5']['division'] ?>.png">
                <h3><?php echo $lang['league.'.$summonerInfo['leagues']['RANKED_SOLO_5x5']['tier']].' '.($summonerInfo['leagues']['RANKED_SOLO_5x5']['tier'] != 'UNRANKED' ? $summonerInfo['leagues']['RANKED_SOLO_5x5']['division'] : null) ?></h3>
              </div>
              <div class="col-md-4 col-sm-4 text-center">
			    <h1>5v5</h1>
                <img draggable="false" width="150px" height="150px" src="<?php echo URL ?>/style/game/summoners/divisions/<?php echo $summonerInfo['leagues']['RANKED_TEAM_5x5']['tier'] ?>/<?php echo $summonerInfo['leagues']['RANKED_TEAM_5x5']['division'] ?>.png">
                <h3><?php echo $lang['league.'.$summonerInfo['leagues']['RANKED_TEAM_5x5']['tier']].' '.($summonerInfo['leagues']['RANKED_TEAM_5x5']['tier'] != 'UNRANKED' ? $summonerInfo['leagues']['RANKED_TEAM_5x5']['division'] : null) ?></h3>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8 col-xs-12">
          <div class="white-box">
            <ul class="nav nav-tabs tabs customtab">
              <li class="active tab"><a href="#matchhistory" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-history"></i></span> <span class="hidden-xs"><?php echo $lang['profile.matchhistory'] ?></span> </a> </li>
              <li class="tab"><a href="#leagues" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-trophy"></i></span> <span class="hidden-xs"><?php echo $lang['profile.leagues'] ?></span> </a> </li>
              <li class="tab"><a href="#stats" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa fa-line-chart"></i></span> <span class="hidden-xs"><?php echo $lang['profile.stats'] ?></span> </a> </li>
              <li class="tab"><a href="#champmastery" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i class="fa fa-bar-chart"></i></span> <span class="hidden-xs"><?php echo $lang['profile.champmastery'] ?></span> </a> </li>
			  <li class="tab"><a href="#champskill" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i class="fa fa-area-chart"></i></span> <span class="hidden-xs"><?php echo $lang['profile.champskill'] ?></span> </a> </li>
			  <li class="tab"><a href="#runes" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i class="fa fa-balance-scale"></i></span> <span class="hidden-xs"><?php echo $lang['profile.runes'] ?></span> </a> </li>
			  <li class="tab"><a href="#masteries" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i class="fa fa-balance-scale"></i></span> <span class="hidden-xs"><?php echo $lang['profile.masteries'] ?></span> </a> </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="matchhistory">
                <!-- srtart -->
				<div class="row">
        <div class="col-lg-12">
          <div class="white-box">
            <table id="matchHistory" class="table toggle-arrow-tiny" data-page-size="7">
              <thead>
                <tr>
                  <th data-sort-initial="true" data-toggle="true"><?php echo $lang['profile.masteries'] ?></th>
                  <th>Last Name</th>
                  <th data-hide="phone, tablet">Job Title</th>
                  <th data-hide="all">DOB</th>
                  <th data-hide="all">Status</th>
                </tr>
              </thead>
              <div class="form-inline padding-bottom-15">
                <div class="row">
                  <div class="col-sm-6">
                  </div>
                  <div class="col-sm-6 text-right m-b-20">
                    <div class="form-group">
                      <input id="summonerGamesSearch" type="text" placeholder="Search" class="form-control"
                        autocomplete="off">
                    </div>
                  </div>
                </div>
              </div>
              <tbody>
                <tr>
                  <td>Shona</td>
                  <td>Woldt</td>
                  <td>Airline Transport Pilot</td>
                  <td>3 Oct 2016</td>
                  <td><span class="label label-table label-success">Active</span> </td>
                  <td><button type="button" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn"
                      data-toggle="tooltip" data-original-title="Delete"><i class="ti-close" aria-hidden="true"></i></button></td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="6"><div class="text-right">
                      <ul class="pagination">
                      </ul>
                    </div></td>
                </tr>
              </tfoot>
            </table>
          </div>
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

<?php
require('kernel/template/scripts.tpl');
?>
<script>
$(window).on('load', function() {
	$('#summonerGamesSearch').on('input', function (e) {
		e.preventDefault();
		$('#matchHistory').trigger('footable_filter', {filter: $(this).val()});
	});
	$('#matchHistory').footable();
});
</script>
</body>
</html>
