<?php
require('../kernel/core.php');
/* 1. La partida debia estar ya en la DB mediante el recent matches, debido a que sino no tendríamos las IDS de los usuarios. */
if(isset($_GET['region']) && isset($_GET['gameid']))
{
	if(core::check_valid_region($_GET['region']) == true)
	{
		$gameregion = $_GET['region'];
	}
	else
	{
		die('ERROR');
	}
	if($db->query('SELECT id FROM lol_matches WHERE gameId='.$_GET['gameid'])->num_rows > 0 AND $db->query('SELECT fullData FROM lol_matches WHERE region="'.$_GET['region'].'" AND gameId='.$_GET['gameid'])->fetch_row()[0] == 'false')
	{
		$gameinfo = core::extjson(str_replace(array('{{region}}','{{game_id}}','{{riotapi}}'),array($gameregion,$_GET['gameid'],$config['riot.api.key']),core::$api_url_gamedata));
		if($gameinfo == 'MAINTENANCE')
		{
			die('MAINTENANCE');
		}
		elseif($gameinfo == false)
		{
			die('ERROR');
		}
		
		$matchParticipants = json_decode($db->query('SELECT participants FROM lol_matches WHERE region="'.$_GET['region'].'" AND gameId='.$_GET['gameid'])->fetch_row()[0],true);
		if(count($gameinfo['participants']) == count($matchParticipants))
		{
			foreach($matchParticipants as $playerid => $playerdata)
			{
				foreach($gameinfo['participants'] as $key => $val)
				{
					if ($val['championId'] === $playerdata['champ']) {
						$matchParticipantId = $key;
					}
				}
				$matchParticipants[$playerid]['spell1'] = $gameinfo['participants'][$matchParticipantId]['spell1Id'];
				$matchParticipants[$playerid]['spell2'] = $gameinfo['participants'][$matchParticipantId]['spell2Id'];
				$matchParticipants[$playerid]['lastSeasonTier'] = $gameinfo['participants'][$matchParticipantId]['highestAchievedSeasonTier'];
				$matchParticipants[$playerid]['timeline'] = $gameinfo['participants'][$matchParticipantId]['timeline'];
				$matchParticipants[$playerid]['masteries'] = $gameinfo['participants'][$matchParticipantId]['masteries'];
				
				if(array_key_exists('stats',$matchParticipants[$playerid]))
				{
					$matchParticipants[$playerid]['stats'] = array_merge($gameinfo['participants'][$matchParticipantId]['stats'],$matchParticipants[$playerid]['stats']);
				}
				else
				{
					$matchParticipants[$playerid]['stats'] = $gameinfo['participants'][$matchParticipantId]['stats'];
				}
				$matchParticipants[$playerid]['participantId'] = $gameinfo['participants'][$matchParticipantId]['participantId'];
				$matchParticipants[$playerid]['runes'] = $gameinfo['participants'][$matchParticipantId]['runes'];
			}
		}
		else
		{
			foreach($matchParticipants as $playerid => $playerdata)
			{
				foreach($gameinfo['participants'] as $key => $val)
				{
					if ($val['championId'] === $playerdata['champ']) {
						$matchParticipantId = $key;
					}
				}
				$matchParticipants[$playerid]['spell1'] = $gameinfo['participants'][$matchParticipantId]['spell1Id'];
				$matchParticipants[$playerid]['spell2'] = $gameinfo['participants'][$matchParticipantId]['spell2Id'];
				$matchParticipants[$playerid]['lastSeasonTier'] = $gameinfo['participants'][$matchParticipantId]['highestAchievedSeasonTier'];
				$matchParticipants[$playerid]['timeline'] = $gameinfo['participants'][$matchParticipantId]['timeline'];
				$matchParticipants[$playerid]['masteries'] = $gameinfo['participants'][$matchParticipantId]['masteries'];
				
				if(array_key_exists('stats',$matchParticipants[$playerid]))
				{
					$matchParticipants[$playerid]['stats'] = array_merge($gameinfo['participants'][$matchParticipantId]['stats'],$matchParticipants[$playerid]['stats']);
				}
				else
				{
					$matchParticipants[$playerid]['stats'] = $gameinfo['participants'][$matchParticipantId]['stats'];
				}
				$matchParticipants[$playerid]['participantId'] = $gameinfo['participants'][$matchParticipantId]['participantId'];
				$matchParticipants[$playerid]['runes'] = $gameinfo['participants'][$matchParticipantId]['runes'];
			}
			$matchParticipantsStartNum = (count($gameinfo['participants'])-count($matchParticipants));
			$matchParticipantsActualNum = 0;
			foreach($gameinfo['participants'] as $participantCount => $participantData)
			{
				if($matchParticipantsActualNum >= $matchParticipantsStartNum)
				{
					$matchParticipants['{{BOT_'.$matchParticipantsActualNum.'}}']['champ'] = $participantData['championId'];
					$matchParticipants['{{BOT_'.$matchParticipantsActualNum.'}}']['team'] = $participantData['teamId'];
					$matchParticipants['{{BOT_'.$matchParticipantsActualNum.'}}']['spell1'] = $participantData['spell1Id'];
					$matchParticipants['{{BOT_'.$matchParticipantsActualNum.'}}']['spell2'] = $participantData['spell2Id'];
					$matchParticipants['{{BOT_'.$matchParticipantsActualNum.'}}']['lastSeasonTier'] = 'UNRANKED';
					$matchParticipants['{{BOT_'.$matchParticipantsActualNum.'}}']['timeline'] = $participantData['timeline'];
					$matchParticipants['{{BOT_'.$matchParticipantsActualNum.'}}']['masteries'] = null;
					$matchParticipants['{{BOT_'.$matchParticipantsActualNum.'}}']['stats'] = $participantData['stats'];
					$matchParticipants['{{BOT_'.$matchParticipantsActualNum.'}}']['participantId'] = $participantData['participantId'];
					$matchParticipants['{{BOT_'.$matchParticipantsActualNum.'}}']['runes'] = null;
				}
				$matchParticipantsActualNum++;
			}
		}
		
		
		$matchParticipants = addslashes(json_encode($matchParticipants));
		
		$matchTimeline = addslashes(json_encode($gameinfo['timeline']));
		
		$matchTeams = array();
		foreach($gameinfo['teams'] as $teamcount => $teamdata)
		{
			$teamdataId = $teamdata['teamId'];
			unset($teamdata['teamId']);
			$matchTeams[$teamdataId] = $teamdata;
		}
		$matchTeams = addslashes(json_encode($matchTeams));
		if($gameinfo['participants'][0]['stats']['winner'] == true)
		{
			$matchWinnerTeam = $gameinfo['participants'][0]['teamId'];
		}
		else
		{
			if($gameinfo['participants'][0]['teamId'] == 100)
			{
				$matchWinnerTeam = 200;
			}
			else
			{
				$matchWinnerTeam = 100;
			}
		}
		
		$db->query('UPDATE `lol_matches` SET `fullData`="true",`version`="'.$gameinfo['matchVersion'].'",`gameType`="'.$gameinfo['queueType'].'",`mapId`='.$gameinfo['mapId'].',`createDate`="'.$gameinfo['matchCreation'].'",`gameLength`='.$gameinfo['matchDuration'].',`winnerTeam`="'.$matchWinnerTeam.'",`participants`="'.$matchParticipants.'",`teams`="'.$matchTeams.'",`timeline`="'.$matchTimeline.'" WHERE region="'.$_GET['region'].'" AND gameId='.$_GET['gameid']);
	}
	else
	{
		die('ERROR');
	}
}
else
{
	die('ERROR');
}