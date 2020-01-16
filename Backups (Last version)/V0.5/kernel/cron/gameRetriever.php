<?php
set_time_limit(0);
include('../core.php');
if($db->query('SELECT id FROM api_matches WHERE fulldata=0')->num_rows > 0)
{
	$gamesToRetrieve = $db->query('SELECT id,region FROM api_matches WHERE fulldata=0');
	while($updateGame = $gamesToRetrieve->fetch_row())
	{
		$gameID = $updateGame[0];
		$region = $updateGame[1];
		try {
			if($db->query('SELECT id FROM api_matches_full WHERE id="'.$gameID.'"')->num_rows == 0)
			{
				$gameDataApi = $api->match($gameID,$region,true);
				if($gameDataApi['matchType'] == 'CUSTOM_GAME')
				{
					$recentGameDataGameType = 'CUSTOM_GAME';
				}
				elseif($gameDataApi['matchType'] == 'TUTORIAL_GAME')
				{
					$recentGameDataGameType = 'TUTORIAL_GAME';
				}
				elseif(true)
				{
					$recentGameDataGameType = $gameDataApi['queueType'];
				}
				$gameDataFullPlayers = array();
				$gameDataFullTeams = array();
				$participantIdIfNotAllowed = 0;
				foreach($gameDataApi['participants'] as $playerData)
				{
					foreach($gameDataApi['participantIdentities'] as $key => $val)
					{
						if ($val['participantId'] === $playerData['participantId']) {
							if(array_key_exists('player', $val))
							{
								$playerPrivateData = $val['player'];
							}
							else
							{
								$playerPrivateData = null;
							}
						}
					}
					if($playerPrivateData != null)
					{
						$participantId = $playerPrivateData['summonerId'];
						$gameDataFullPlayers[$playerPrivateData['summonerId']] = array();
						$gameDataFullPlayers[$playerPrivateData['summonerId']]['name'] = $playerPrivateData['summonerName'];
						$gameDataFullPlayers[$playerPrivateData['summonerId']]['icon'] = $playerPrivateData['profileIcon'];
						$gameDataFullPlayers[$playerPrivateData['summonerId']]['dataSet'] = true;
						$actualPlayerData = $gameDataFullPlayers[$playerPrivateData['summonerId']];
					}
					else
					{
						$participantId = $participantIdIfNotAllowed;
						$gameDataFullPlayers[$participantId] = array();
						$gameDataFullPlayers[$participantId]['name'] = null;
						$gameDataFullPlayers[$participantId]['icon'] = null;
						$gameDataFullPlayers[$participantId]['dataSet'] = false;
						$participantIdIfNotAllowed++;
					}
					$gameDataFullPlayers[$participantId]['participantId'] = $playerData['participantId'];
					$gameDataFullPlayers[$participantId]['team'] = $playerData['teamId'];
					$gameDataFullPlayers[$participantId]['champ'] = $playerData['championId'];
					$gameDataFullPlayers[$participantId]['spell1'] = $playerData['spell1Id'];
					$gameDataFullPlayers[$participantId]['spell2'] = $playerData['spell2Id'];
					$gameDataFullPlayers[$participantId]['highestTierLastSeason'] = $playerData['highestAchievedSeasonTier'];
					$gameDataFullPlayers[$participantId]['runes'] = @$playerData['runes'];
					$gameDataFullPlayers[$participantId]['masteries'] = @$playerData['masteries'];
					$gameDataFullPlayers[$participantId]['stats'] = $playerData['stats'];
					$gameDataFullPlayers[$participantId]['timeline'] = @$playerData['timeline'];
				}
				foreach($gameDataApi['teams'] as $teamData)
				{
					$gameDataFullTeams[$teamData['teamId']] = array();
					$gameDataFullTeams[$teamData['teamId']]['winner'] = $teamData['winner'];
					$gameDataFullTeams[$teamData['teamId']]['firstBlood'] = $teamData['firstBlood'];
					if($gameDataApi['mapId'] == 11)
					{
						$gameDataFullTeams[$teamData['teamId']]['killsBaron'] = $teamData['baronKills'];
						$gameDataFullTeams[$teamData['teamId']]['killsDragon'] = $teamData['dragonKills'];
						$gameDataFullTeams[$teamData['teamId']]['firstBaron'] = $teamData['firstBaron'];
						$gameDataFullTeams[$teamData['teamId']]['firstDragon'] = $teamData['firstDragon'];
						$gameDataFullTeams[$teamData['teamId']]['firstTower'] = $teamData['firstTower'];
						$gameDataFullTeams[$teamData['teamId']]['firstInhib'] = $teamData['firstInhibitor'];
						$gameDataFullTeams[$teamData['teamId']]['killsTowers'] = $teamData['towerKills'];
						$gameDataFullTeams[$teamData['teamId']]['killsInhibs'] = $teamData['inhibitorKills'];
					}
					if($gameDataApi['mapId'] == 10)
					{
						$gameDataFullTeams[$teamData['teamId']]['killsVilemaw'] = $teamData['vilemawKills'];
						$gameDataFullTeams[$teamData['teamId']]['firstTower'] = $teamData['firstTower'];
						$gameDataFullTeams[$teamData['teamId']]['firstInhib'] = $teamData['firstInhibitor'];
						$gameDataFullTeams[$teamData['teamId']]['killsTowers'] = $teamData['towerKills'];
						$gameDataFullTeams[$teamData['teamId']]['killsInhibs'] = $teamData['inhibitorKills'];
					}
					if($gameDataApi['mapId'] == 8)
					{
						$gameDataFullTeams[$teamData['teamId']]['scoreDominion'] = $teamData['dominionVictoryScore'];
					}
					$gameDataFullTeams[$teamData['teamId']]['bans'] = @$teamData['bans'];
				}
				$db->query('INSERT INTO api_matches_full VALUES ("'.$gameDataApi['matchId'].'","'.$gameDataApi['region'].'","'.$recentGameDataGameType.'","'.$gameDataApi['matchCreation'].'","'.$gameDataApi['matchDuration'].'","'.$gameDataApi['mapId'].'","'.$gameDataApi['season'].'","'.$gameDataApi['matchVersion'].'","'.LOL_PATCH.'","'.addslashes(json_encode($gameDataFullPlayers)).'","'.addslashes(json_encode($gameDataFullTeams)).'","'.addslashes(json_encode($gameDataApi['timeline'])).'")') or die($db->error);
			}
			$db->query('UPDATE api_matches SET fulldata=1 WHERE id="'.$updateGame[0].'"');
			sleep(10);
		} catch(Exception $e) {
			if($e->getMessage() == 'RATE_LIMIT_EXCEEDED')
			{
				sleep(60);
			}
		};
	}
}
else
{
	die();
}