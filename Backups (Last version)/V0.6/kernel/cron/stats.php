<?php
/* THEYRE ONLY FOR SUMMONERS RIFT */
/* EXECUTE THIS CRON EVERY DAY! */
include('../core.php');
$core->setStatus('updating','stats');
$champStats = array();
$champStats['NORMAL'] = array();
$champStats['RANKEDSOLO'] = array();
$champStats['RANKEDTEAM'] = array();
$champStats['NORMAL']['champions'] = array();
$champStats['RANKEDSOLO']['champions'] = array();
$champStats['RANKEDTEAM']['champions'] = array();

$champStats['NORMAL']['totalSpellsSet'] = 0;
$champStats['RANKEDSOLO']['totalSpellsSet'] = 0;
$champStats['RANKEDTEAM']['totalSpellsSet'] = 0;

$gamesFullData = $db->query('SELECT id,playersData,teamsData,matchType,duration FROM api_matches_full WHERE mapId=11 AND patch="'.LOL_PATCH.'"');
$gameChampions = $api->staticData('champion');

foreach($gameChampions['data'] as $keyName => $champData)
{
	foreach(array_keys($champStats) as $matchType)
	{
		$champStats[$matchType]['champions'][$champData['id']]['bansCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['banPercent'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['winRate'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['matchesCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['matchesWonCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['spells'] = array();
		$champStats[$matchType]['champions'][$champData['id']]['position'] = array();
		$champStats[$matchType]['champions'][$champData['id']]['tier'] = array();
		$champStats[$matchType]['champions'][$champData['id']]['items'] = array();
		$champStats[$matchType]['champions'][$champData['id']]['runes'] = array();
		$champStats[$matchType]['champions'][$champData['id']]['masteries'] = array();
		$champStats[$matchType]['champions'][$champData['id']]['team100Winrate'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['team100Count'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['team200Winrate'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['team200Count'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['firstBloodKillsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['firstBloodKillsWinrate'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['firstBloodAssistsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['firstBloodAssistsWinrate'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['firstTowersKillsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['firstTowersKillsWinrate'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['firstTowersAssistsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['firstTowersAssistsWinrate'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['firstInhibsKillsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['firstInhibsKillsWinrate'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['firstInhibsAssistsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['firstInhibsAssistsWinrate'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['killsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['killsAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['deathsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['deathsAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['assistsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['assistsAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['towersCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['towersAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['inhibsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['inhibsAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['doubleKillsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['doubleKillsAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['tripleKillsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['tripleKillsAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['quadraKillsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['quadraKillsAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['pentaKillsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['pentaKillsAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['goldCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['goldAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['minionsCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['minionsAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['levelCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['levelAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['dmgDealtCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['dmgDealtAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['dmgTakenCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['dmgTakenAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['pinkWardsBoughtCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['pinkWardsBoughtAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['ccTimeCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['ccTimeAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['wardsPutCount'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['wardsPutAverage'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records'] = array();
		$champStats[$matchType]['champions'][$champData['id']]['records']['kills'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['killsSummonerId'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['deaths'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['deathsSummonerId'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['assists'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['assistsSummonerId'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['killingSpree'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['killingSpreeSummonerId'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['minions'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['minionsSummonerId'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['largestMultiKill'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['largestMultiKillSummonerId'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['largestCriticalStrike'] = 0;
		$champStats[$matchType]['champions'][$champData['id']]['records']['largestCriticalStrikeSummonerId'] = 0;
	}
}

foreach(array_keys($champStats) as $matchType)
{
	$champStats[$matchType]['matchDurationCount'] = 0;
	$champStats[$matchType]['matchDurationAverage'] = 0;
	$champStats[$matchType]['matches'] = 0;

	$champStats[$matchType]['teams'] = array();

	$champStats[$matchType]['summoners'] = array();
	$champStats[$matchType]['summoners']['winnerIcon'] = array();
	$champStats[$matchType]['summoners']['winnerIcon']['count']  = array();
	$champStats[$matchType]['summoners']['winnerIcon']['average'] = array();
	$champStats[$matchType]['summoners']['tier'] = array();
	$champStats[$matchType]['summoners']['position'] = array();
	$champStats[$matchType]['summoners']['runes'] = array();
	$champStats[$matchType]['summoners']['masteries'] = array();
}
$gamesWithSummonerData = 0;
while($fullGame = $gamesFullData->fetch_row())
{
	switch($fullGame[3])
	{
		case 'TEAM_BUILDER_DRAFT_RANKED_5x5':
		$matchType = 'RANKEDSOLO';
		break;
		case 'TEAM_BUILDER_DRAFT_UNRANKED_5x5':
		$matchType = 'NORMAL';
		break;
		case 'RANKED_SOLO_5x5':
		$matchType = 'RANKEDSOLO';
		break;
		case 'RANKED_PREMADE_5x5':
		$matchType = 'RANKEDTEAM';
		break;
		case 'RANKED_TEAM_5x5':
		$matchType = 'RANKEDTEAM';
		break;
		default:
		$matchType = 'NORMAL';
		break;
	}
	$champStats[$matchType]['matches']++;
	$playersData = json_decode($fullGame[1],true);
	$teamsData = json_decode($fullGame[2],true);
	$gameId = $fullGame[1];
	
	$champStats[$matchType]['matchDurationCount'] = $fullGame[4];
	/* Teams data */
	foreach($teamsData as $teamId => $teamData)
	{
		if(array_key_exists($teamId,$champStats[$matchType]['teams']) == FALSE)
		{
			$champStats[$matchType]['teams'][$teamId] = array();
			$champStats[$matchType]['teams'][$teamId]['winnerCount'] = 0;
			$champStats[$matchType]['teams'][$teamId]['winnerPercent'] = 0;
			$champStats[$matchType]['teams'][$teamId]['winnerFirstDragonCount'] = 0;
			$champStats[$matchType]['teams'][$teamId]['winnerFirstDragonPercent'] = 0;
			$champStats[$matchType]['teams'][$teamId]['winnerFirstNashorCount'] = 0;
			$champStats[$matchType]['teams'][$teamId]['winnerFirstNashorPercent'] = 0;
			$champStats[$matchType]['teams'][$teamId]['winnerFirstBloodCount'] = 0;
			$champStats[$matchType]['teams'][$teamId]['winnerFirstBloodPercent'] = 0;
			$champStats[$matchType]['teams'][$teamId]['winnerFirstTowerCount'] = 0;
			$champStats[$matchType]['teams'][$teamId]['winnerFirstTowerPercent'] = 0;
			$champStats[$matchType]['teams'][$teamId]['winnerFirstInhibCount'] = 0;
			$champStats[$matchType]['teams'][$teamId]['winnerFirstInhibPercent'] = 0;
			$champStats[$matchType]['teams'][$teamId]['killsBaronCount'] = 0;
			$champStats[$matchType]['teams'][$teamId]['killsBaronAverage'] = 0;
			$champStats[$matchType]['teams'][$teamId]['killsDragonCount'] = 0;
			$champStats[$matchType]['teams'][$teamId]['killsDragonAverage'] = 0;
			$champStats[$matchType]['teams'][$teamId]['killsTowersCount'] = 0;
			$champStats[$matchType]['teams'][$teamId]['killsTowersAverage'] = 0;
			$champStats[$matchType]['teams'][$teamId]['killsInhibsCount'] = 0;
			$champStats[$matchType]['teams'][$teamId]['killsInhibsAverage'] = 0;
		}
		if($teamData['winner'] == true)
		{
			$champStats[$matchType]['teams'][$teamId]['winnerCount']++;
			if($teamData['firstBaron'] == true) { $champStats[$matchType]['teams'][$teamId]['winnerFirstNashorCount']++; }
			if($teamData['firstDragon'] == true) { $champStats[$matchType]['teams'][$teamId]['winnerFirstDragonCount']++; }
			if($teamData['firstBlood'] == true) { $champStats[$matchType]['teams'][$teamId]['winnerFirstBloodCount']++; }
			if($teamData['firstTower'] == true) { $champStats[$matchType]['teams'][$teamId]['winnerFirstTowerCount']++; }
			if($teamData['firstInhib'] == true) { $champStats[$matchType]['teams'][$teamId]['winnerFirstInhibCount']++; }
		}
		
		$champStats[$matchType]['teams'][$teamId]['killsBaronCount'] = $teamData['killsBaron'] + $champStats[$matchType]['teams'][$teamId]['killsBaronCount'];
		$champStats[$matchType]['teams'][$teamId]['killsDragonCount'] = $teamData['killsDragon'] + $champStats[$matchType]['teams'][$teamId]['killsDragonCount'];
		$champStats[$matchType]['teams'][$teamId]['killsTowersCount'] = $teamData['killsTowers'] + $champStats[$matchType]['teams'][$teamId]['killsTowersCount'];
		$champStats[$matchType]['teams'][$teamId]['killsInhibsCount'] = $teamData['killsInhibs'] + $champStats[$matchType]['teams'][$teamId]['killsInhibsCount'];
		
		if(array_key_exists('bans',$teamData) == TRUE && count($teamData['bans']) > 0)
		{
			foreach($teamData['bans'] as $banData)
			{
				$champStats[$matchType]['champions'][$banData['championId']]['bansCount']++;
			}
		}
	}
	
	/* Players data */
	foreach($playersData as $summonerId => $summonerData)
	{
		if(array_key_exists($summonerData['spell1'],$champStats[$matchType]['champions'][$summonerData['champ']]['spells']) == FALSE)
		{
			$champStats[$matchType]['champions'][$summonerData['champ']]['spells'][$summonerData['spell1']] = array();
			$champStats[$matchType]['champions'][$summonerData['champ']]['spells'][$summonerData['spell1']]['spellWinRate'] = 0;
			$champStats[$matchType]['champions'][$summonerData['champ']]['spells'][$summonerData['spell1']]['countTimesPicked'] = 0;
			$champStats[$matchType]['champions'][$summonerData['champ']]['spells'][$summonerData['spell1']]['countTimesWon'] = 0;
		}
		if(array_key_exists($summonerData['spell2'],$champStats[$matchType]['champions'][$summonerData['champ']]['spells']) == FALSE)
		{
			$champStats[$matchType]['champions'][$summonerData['champ']]['spells'][$summonerData['spell2']] = array();
			$champStats[$matchType]['champions'][$summonerData['champ']]['spells'][$summonerData['spell2']]['spellWinRate'] = 0;
			$champStats[$matchType]['champions'][$summonerData['champ']]['spells'][$summonerData['spell2']]['countTimesPicked'] = 0;
			$champStats[$matchType]['champions'][$summonerData['champ']]['spells'][$summonerData['spell2']]['countTimesWon'] = 0;
		}
		
		$champStats[$matchType]['champions'][$summonerData['champ']]['spells'][$summonerData['spell1']]['countTimesPicked']++;
		$champStats[$matchType]['champions'][$summonerData['champ']]['spells'][$summonerData['spell2']]['countTimesPicked']++;
		
		for($itemId = 0; $itemId < 7; $itemId++)
		{
			if($summonerData['stats']['item'.$itemId] != 0)
			{
				if(array_key_exists($summonerData['stats']['item'.$itemId],$champStats[$matchType]['champions'][$summonerData['champ']]['items']) == FALSE)
				{
					$champStats[$matchType]['champions'][$summonerData['champ']]['items'][$summonerData['stats']['item'.$itemId]] = array();
					$champStats[$matchType]['champions'][$summonerData['champ']]['items'][$summonerData['stats']['item'.$itemId]]['countTimesWon'] = 0;
					$champStats[$matchType]['champions'][$summonerData['champ']]['items'][$summonerData['stats']['item'.$itemId]]['countMatches'] = 0;
					$champStats[$matchType]['champions'][$summonerData['champ']]['items'][$summonerData['stats']['item'.$itemId]]['itemWinrate'] = 0;
				}
				$champStats[$matchType]['champions'][$summonerData['champ']]['items'][$summonerData['stats']['item'.$itemId]]['countMatches']++;

				if($summonerData['stats']['winner'] == true)
				{
					$champStats[$matchType]['champions'][$summonerData['champ']]['items'][$summonerData['stats']['item'.$itemId]]['countTimesWon']++;
				}
			}
		}
		
		if(array_key_exists($summonerData['highestTierLastSeason'],$champStats[$matchType]['champions'][$summonerData['champ']]['tier']) == FALSE) //Es el de la season pasada pero nvm.
		{
			$champStats[$matchType]['champions'][$summonerData['champ']]['tier'][$summonerData['highestTierLastSeason']] = array();
			$champStats[$matchType]['champions'][$summonerData['champ']]['tier'][$summonerData['highestTierLastSeason']]['countTimesWon'] = 0;
			$champStats[$matchType]['champions'][$summonerData['champ']]['tier'][$summonerData['highestTierLastSeason']]['countMatches'] = 0;
			$champStats[$matchType]['champions'][$summonerData['champ']]['tier'][$summonerData['highestTierLastSeason']]['tierWinrate'] = 0;
		}
		
		if(array_key_exists($summonerData['highestTierLastSeason'],$champStats[$matchType]['summoners']['tier']) == FALSE) //Es el de la season pasada pero nvm.
		{
			$champStats[$matchType]['summoners']['tier'][$summonerData['highestTierLastSeason']] = array();
			$champStats[$matchType]['summoners']['tier'][$summonerData['highestTierLastSeason']]['countTimesWon'] = 0;
			$champStats[$matchType]['summoners']['tier'][$summonerData['highestTierLastSeason']]['countMatches'] = 0;
			$champStats[$matchType]['summoners']['tier'][$summonerData['highestTierLastSeason']]['tierWinrate'] = 0;
		}
		if(is_array($summonerData['timeline']) && count($summonerData['timeline']) > 0)
		{
			switch($summonerData['timeline']['lane'])
			{
				case 'MID':
				$summonerPosition = 'MID';
				break;
				case 'MIDDLE':
				$summonerPosition = 'MID';
				break;
				case 'TOP':
				$summonerPosition = 'TOP';
				break;
				case 'JUNGLE':
				$summonerPosition = 'JGL';
				break;
				case 'BOT':
				switch($summonerData['timeline']['role'])
				{
					case 'DUO':
					$summonerPosition = 'SUP';
					break;
					case 'NONE':
					$summonerPosition = 'SUP';
					break;
					case 'SOLO':
					$summonerPosition = 'ADC';
					break;
					case 'DUO_CARRY':
					$summonerPosition = 'ADC';
					break;
					case 'DUO_SUPPORT':
					$summonerPosition = 'SUPPORT';
					break;
					default: $summonerPosition = 'NOT_SET';
				}
				break;
				case 'BOTTOM':
				switch($summonerData['timeline']['role'])
				{
					case 'DUO':
					$summonerPosition = 'SUP';
					break;
					case 'NONE':
					$summonerPosition = 'SUP';
					break;
					case 'SOLO':
					$summonerPosition = 'ADC';
					break;
					case 'DUO_CARRY':
					$summonerPosition = 'ADC';
					break;
					case 'DUO_SUPPORT':
					$summonerPosition = 'SUPPORT';
					break;
					default: $summonerPosition = 'NOT_SET';
				}
				break;
				default: $summonerPosition = 'NOT_SET';
			}
			if($summonerPosition != 'NOT_SET')
			{
				if(array_key_exists($summonerPosition,$champStats[$matchType]['champions'][$summonerData['champ']]['position']) == FALSE) //Es el de la season pasada pero nvm.
				{
					$champStats[$matchType]['champions'][$summonerData['champ']]['position'][$summonerPosition] = array();
					$champStats[$matchType]['champions'][$summonerData['champ']]['position'][$summonerPosition]['countTimesWon'] = 0;
					$champStats[$matchType]['champions'][$summonerData['champ']]['position'][$summonerPosition]['countMatches'] = 0;
					$champStats[$matchType]['champions'][$summonerData['champ']]['position'][$summonerPosition]['positionWinrate'] = 0;
				}
				
				if(array_key_exists($summonerPosition,$champStats[$matchType]['summoners']['position']) == FALSE) //Es el de la season pasada pero nvm.
				{
					$champStats[$matchType]['summoners']['position'][$summonerPosition] = array();
					$champStats[$matchType]['summoners']['position'][$summonerPosition]['countTimesWon'] = 0;
					$champStats[$matchType]['summoners']['position'][$summonerPosition]['countMatches'] = 0;
					$champStats[$matchType]['summoners']['position'][$summonerPosition]['positionWinrate'] = 0;
				}
				
				$champStats[$matchType]['champions'][$summonerData['champ']]['position'][$summonerPosition]['countMatches']++;
				$champStats[$matchType]['summoners']['position'][$summonerPosition]['countMatches']++;
		
				if($summonerData['stats']['winner'] == true)
				{
					$champStats[$matchType]['champions'][$summonerData['champ']]['position'][$summonerPosition]['countTimesWon']++;
					$champStats[$matchType]['summoners']['position'][$summonerPosition]['countTimesWon']++;
				}
			}
		}
		
		foreach($summonerData['runes'] as $runeData)
		{
			if(array_key_exists($runeData['runeId'],$champStats[$matchType]['summoners']['runes']) == FALSE)
			{
				$champStats[$matchType]['summoners']['runes'][$runeData['runeId']] = array();
				$champStats[$matchType]['summoners']['runes'][$runeData['runeId']]['countTimesWon'] = 0;
				$champStats[$matchType]['summoners']['runes'][$runeData['runeId']]['countMatches'] = 0;
				$champStats[$matchType]['summoners']['runes'][$runeData['runeId']]['runeWinrate'] = 0;
			}
			if(array_key_exists($runeData['runeId'],$champStats[$matchType]['champions'][$summonerData['champ']]['runes']) == FALSE)
			{
				$champStats[$matchType]['champions'][$summonerData['champ']]['runes'][$runeData['runeId']] = array();
				$champStats[$matchType]['champions'][$summonerData['champ']]['runes'][$runeData['runeId']]['countTimesWon'] = 0;
				$champStats[$matchType]['champions'][$summonerData['champ']]['runes'][$runeData['runeId']]['countMatches'] = 0;
				$champStats[$matchType]['champions'][$summonerData['champ']]['runes'][$runeData['runeId']]['runeWinrate'] = 0;
			}
			$champStats[$matchType]['summoners']['runes'][$runeData['runeId']]['countMatches']++;
			$champStats[$matchType]['champions'][$summonerData['champ']]['runes'][$runeData['runeId']]['countMatches']++;
			if($summonerData['stats']['winner'] == true)
			{
				$champStats[$matchType]['summoners']['runes'][$runeData['runeId']]['countTimesWon']++;
				$champStats[$matchType]['champions'][$summonerData['champ']]['runes'][$runeData['runeId']]['countTimesWon']++;
			}
		}
		
		foreach($summonerData['masteries'] as $masteryData)
		{
			if(array_key_exists($masteryData['masteryId'],$champStats[$matchType]['summoners']['masteries']) == FALSE)
			{
				$champStats[$matchType]['summoners']['masteries'][$masteryData['masteryId']] = array();
				$champStats[$matchType]['summoners']['masteries'][$masteryData['masteryId']]['countTimesWon'] = 0;
				$champStats[$matchType]['summoners']['masteries'][$masteryData['masteryId']]['countMatches'] = 0;
				$champStats[$matchType]['summoners']['masteries'][$masteryData['masteryId']]['masteryWinrate'] = 0;
			}
			if(array_key_exists($masteryData['masteryId'],$champStats[$matchType]['champions'][$summonerData['champ']]['masteries']) == FALSE)
			{
				$champStats[$matchType]['champions'][$summonerData['champ']]['masteries'][$masteryData['masteryId']] = array();
				$champStats[$matchType]['champions'][$summonerData['champ']]['masteries'][$masteryData['masteryId']]['countTimesWon'] = 0;
				$champStats[$matchType]['champions'][$summonerData['champ']]['masteries'][$masteryData['masteryId']]['countMatches'] = 0;
				$champStats[$matchType]['champions'][$summonerData['champ']]['masteries'][$masteryData['masteryId']]['masteryWinrate'] = 0;
			}
			$champStats[$matchType]['summoners']['masteries'][$masteryData['masteryId']]['countMatches']++;
			$champStats[$matchType]['champions'][$summonerData['champ']]['masteries'][$masteryData['masteryId']]['countMatches']++;
			if($summonerData['stats']['winner'] == true)
			{
				$champStats[$matchType]['summoners']['masteries'][$masteryData['masteryId']]['countTimesWon']++;
				$champStats[$matchType]['champions'][$summonerData['champ']]['masteries'][$masteryData['masteryId']]['countTimesWon']++;
			}
		}
		
		$champStats[$matchType]['champions'][$summonerData['champ']]['team'.$summonerData['team'].'Count']++;
		$champStats[$matchType]['champions'][$summonerData['champ']]['tier'][$summonerData['highestTierLastSeason']]['countMatches']++;
		$champStats[$matchType]['summoners']['tier'][$summonerData['highestTierLastSeason']]['countMatches']++;
		
		if($summonerData['stats']['winner'] == true)
		{
			$champStats[$matchType]['champions'][$summonerData['champ']]['team'.$summonerData['team'].'Winrate']++;
			$champStats[$matchType]['champions'][$summonerData['champ']]['tier'][$summonerData['highestTierLastSeason']]['countTimesWon']++;
			$champStats[$matchType]['summoners']['tier'][$summonerData['highestTierLastSeason']]['countTimesWon']++;
			
			$champStats[$matchType]['champions'][$summonerData['champ']]['spells'][$summonerData['spell1']]['countTimesWon']++;
			$champStats[$matchType]['champions'][$summonerData['champ']]['spells'][$summonerData['spell2']]['countTimesWon']++;
			if($summonerData['icon'] != null)
			{
				if(array_key_exists($summonerData['icon'],$champStats[$matchType]['summoners']['winnerIcon']['count']) == FALSE)
				{
					$champStats[$matchType]['summoners']['winnerIcon']['count'][$summonerData['icon']] = 0;
				}
				$champStats[$matchType]['summoners']['winnerIcon']['count'][$summonerData['icon']]++;
				$gamesWithSummonerData++;
			}
			$champStats[$matchType]['champions'][$summonerData['champ']]['matchesWonCount']++;
			
		}
		$champStats[$matchType]['totalSpellsSet']++;
		$champStats[$matchType]['totalSpellsSet']++;
		$champStats[$matchType]['champions'][$summonerData['champ']]['firstBloodKillsCount']++;
		$champStats[$matchType]['champions'][$summonerData['champ']]['firstBloodAssistsCount']++;
		$champStats[$matchType]['champions'][$summonerData['champ']]['firstTowersKillsCount']++;
		$champStats[$matchType]['champions'][$summonerData['champ']]['firstTowersAssistsCount']++;
		$champStats[$matchType]['champions'][$summonerData['champ']]['firstInhibsKillsCount']++;
		$champStats[$matchType]['champions'][$summonerData['champ']]['firstInhibsAssistsCount']++;
		$champStats[$matchType]['champions'][$summonerData['champ']]['killsCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['killsCount'] + $summonerData['stats']['kills'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['deathsCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['deathsCount'] + $summonerData['stats']['deaths'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['assistsCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['assistsCount'] + $summonerData['stats']['assists'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['towersCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['towersCount'] + $summonerData['stats']['towerKills'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['inhibsCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['inhibsCount'] + $summonerData['stats']['inhibitorKills'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['doubleKillsCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['doubleKillsCount'] + $summonerData['stats']['doubleKills'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['tripleKillsCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['tripleKillsCount'] + $summonerData['stats']['tripleKills'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['quadraKillsCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['quadraKillsCount'] + $summonerData['stats']['quadraKills'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['pentaKillsCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['pentaKillsCount'] + $summonerData['stats']['pentaKills'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['goldCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['goldCount'] + $summonerData['stats']['goldEarned'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['levelCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['levelCount'] + $summonerData['stats']['champLevel'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['dmgDealtCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['dmgDealtCount'] + $summonerData['stats']['totalDamageDealt'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['dmgTakenCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['dmgTakenCount'] + $summonerData['stats']['totalDamageTaken'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['pinkWardsBoughtCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['pinkWardsBoughtCount'] + $summonerData['stats']['visionWardsBoughtInGame'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['wardsPutCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['wardsPutCount'] + $summonerData['stats']['wardsPlaced'];
		$champStats[$matchType]['champions'][$summonerData['champ']]['ccTimeCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['ccTimeCount'] + $summonerData['stats']['totalTimeCrowdControlDealt'];
		
		$champStats[$matchType]['champions'][$summonerData['champ']]['minionsCount'] = $champStats[$matchType]['champions'][$summonerData['champ']]['minionsCount'] + $summonerData['stats']['minionsKilled'] + $summonerData['stats']['neutralMinionsKilled'];
		if($champStats[$matchType]['champions'][$summonerData['champ']]['records']['killingSpree'] < $summonerData['stats']['largestKillingSpree'])
		{
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['killingSpree'] = $summonerData['stats']['largestKillingSpree'];
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['killingSpreeSummonerId'] = $summonerId;
		}
		if($champStats[$matchType]['champions'][$summonerData['champ']]['records']['kills'] < $summonerData['stats']['kills'])
		{
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['kills'] = $summonerData['stats']['kills'];
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['killsSummonerId'] = $summonerId;
		}
		if($champStats[$matchType]['champions'][$summonerData['champ']]['records']['deaths'] < $summonerData['stats']['deaths'])
		{
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['deaths'] = $summonerData['stats']['deaths'];
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['deathsSummonerId'] = $summonerId;
		}
		if($champStats[$matchType]['champions'][$summonerData['champ']]['records']['assists'] < $summonerData['stats']['assists'])
		{
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['assists'] = $summonerData['stats']['assists'];
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['assistsSummonerId'] = $summonerId;
		}
		if($champStats[$matchType]['champions'][$summonerData['champ']]['records']['minions'] < ($summonerData['stats']['minionsKilled'] + $summonerData['stats']['neutralMinionsKilled'] + $summonerData['stats']['wardsKilled']))
		{
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['minions'] = ($summonerData['stats']['minionsKilled'] + $summonerData['stats']['neutralMinionsKilled'] + $summonerData['stats']['wardsKilled']);
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['minionsSummonerId'] = $summonerId;
		}
		if($champStats[$matchType]['champions'][$summonerData['champ']]['records']['largestMultiKill'] < $summonerData['stats']['largestMultiKill'])
		{
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['largestMultiKill'] = $summonerData['stats']['largestMultiKill'];
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['largestMultiKillSummonerId'] = $summonerId;
		}
		if($champStats[$matchType]['champions'][$summonerData['champ']]['records']['largestCriticalStrike'] < $summonerData['stats']['largestCriticalStrike'])
		{
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['largestCriticalStrike'] = $summonerData['stats']['largestCriticalStrike'];
			$champStats[$matchType]['champions'][$summonerData['champ']]['records']['largestCriticalStrikeSummonerId'] = $summonerId;
		}
		$champStats[$matchType]['champions'][$summonerData['champ']]['matchesCount']++;
	}
}

/* Averages */
foreach($champStats as $matchType => $matchData)
{
	$champStats[$matchType]['matchDurationAverage'] = round((($champStats[$matchType]['matchDurationCount']/($champStats[$matchType]['matches'] == 0 ? 1 : $champStats[$matchType]['matches']))));
	
	foreach($champStats[$matchType]['teams'] as $teamId => $teamData)
	{
		$champStats[$matchType]['teams'][$teamId]['winnerPercent'] = round((($champStats[$matchType]['teams'][$teamId]['winnerCount']/($champStats[$matchType]['matches'] == 0 ? 1 : $champStats[$matchType]['matches'])) * 100));
		$champStats[$matchType]['teams'][$teamId]['winnerFirstDragonPercent'] = round((($champStats[$matchType]['teams'][$teamId]['winnerFirstDragonCount']/($champStats[$matchType]['teams'][$teamId]['winnerCount'] == 0 ? 1 : $champStats[$matchType]['teams'][$teamId]['winnerCount'])) * 100));
		$champStats[$matchType]['teams'][$teamId]['winnerFirstNashorPercent'] = round((($champStats[$matchType]['teams'][$teamId]['winnerFirstNashorCount']/($champStats[$matchType]['teams'][$teamId]['winnerCount'] == 0 ? 1 : $champStats[$matchType]['teams'][$teamId]['winnerCount'])) * 100));
		$champStats[$matchType]['teams'][$teamId]['winnerFirstBloodPercent'] = round((($champStats[$matchType]['teams'][$teamId]['winnerFirstBloodCount']/($champStats[$matchType]['teams'][$teamId]['winnerCount'] == 0 ? 1 : $champStats[$matchType]['teams'][$teamId]['winnerCount'])) * 100));
		$champStats[$matchType]['teams'][$teamId]['winnerFirstTowerPercent'] = round((($champStats[$matchType]['teams'][$teamId]['winnerFirstTowerCount']/($champStats[$matchType]['teams'][$teamId]['winnerCount'] == 0 ? 1 : $champStats[$matchType]['teams'][$teamId]['winnerCount'])) * 100));
		$champStats[$matchType]['teams'][$teamId]['winnerFirstInhibPercent'] = round((($champStats[$matchType]['teams'][$teamId]['winnerFirstInhibCount']/($champStats[$matchType]['teams'][$teamId]['winnerCount'] == 0 ? 1 : $champStats[$matchType]['teams'][$teamId]['winnerCount'])) * 100));
		$champStats[$matchType]['teams'][$teamId]['killsBaronAverage'] = number_format(($champStats[$matchType]['teams'][$teamId]['killsBaronCount']/($champStats[$matchType]['matches'] == 0 ? 1 : $champStats[$matchType]['matches'])),2);
		$champStats[$matchType]['teams'][$teamId]['killsDragonAverage'] = number_format(($champStats[$matchType]['teams'][$teamId]['killsDragonCount']/($champStats[$matchType]['matches'] == 0 ? 1 : $champStats[$matchType]['matches'])),2);
		$champStats[$matchType]['teams'][$teamId]['killsTowersAverage'] = number_format(($champStats[$matchType]['teams'][$teamId]['killsTowersCount']/($champStats[$matchType]['matches'] == 0 ? 1 : $champStats[$matchType]['matches'])),2);
		$champStats[$matchType]['teams'][$teamId]['killsInhibsAverage'] = number_format(($champStats[$matchType]['teams'][$teamId]['killsInhibsCount']/($champStats[$matchType]['matches'] == 0 ? 1 : $champStats[$matchType]['matches'])),2);
	}
	foreach($champStats[$matchType]['champions'] as $champId => $champData)
	{
		$champStats[$matchType]['champions'][$champId]['banPercent'] = round((($champData['bansCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount'])) * 100));
		$champStats[$matchType]['champions'][$champId]['winRate'] = round((($champData['matchesWonCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount'])) * 100));
		$champStats[$matchType]['champions'][$champId]['team100Winrate'] = round((($champData['team100Count']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount'])) * 100));
		$champStats[$matchType]['champions'][$champId]['team200Winrate'] = round((($champData['team200Count']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount'])) * 100));
		$champStats[$matchType]['champions'][$champId]['firstBloodKillsWinrate'] = round((($champData['firstBloodKillsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount'])) * 100));
		$champStats[$matchType]['champions'][$champId]['firstBloodAssistsWinrate'] = round((($champData['firstBloodAssistsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount'])) * 100));
		$champStats[$matchType]['champions'][$champId]['firstTowersKillsWinrate'] = round((($champData['firstTowersKillsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount'])) * 100));
		$champStats[$matchType]['champions'][$champId]['firstTowersAssistsWinrate'] = round((($champData['firstTowersAssistsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount'])) * 100));
		$champStats[$matchType]['champions'][$champId]['firstInhibsKillsWinrate'] = round((($champData['firstInhibsKillsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount'])) * 100));
		$champStats[$matchType]['champions'][$champId]['goldAverage'] = round((($champData['goldCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))));
		$champStats[$matchType]['champions'][$champId]['minionsAverage'] = round((($champData['minionsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))));
		$champStats[$matchType]['champions'][$champId]['levelAverage'] = round((($champData['levelCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))));
		$champStats[$matchType]['champions'][$champId]['dmgDealtAverage'] = round((($champData['dmgDealtCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))));
		$champStats[$matchType]['champions'][$champId]['dmgTakenAverage'] = round((($champData['dmgTakenCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))));
		$champStats[$matchType]['champions'][$champId]['pinkWardsBoughtAverage'] = round((($champData['pinkWardsBoughtCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))));
		$champStats[$matchType]['champions'][$champId]['ccTimeAverage'] = round((($champData['ccTimeCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))));
		$champStats[$matchType]['champions'][$champId]['wardsPutAverage'] = round((($champData['wardsPutCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))));
		$champStats[$matchType]['champions'][$champId]['killsAverage'] = number_format((($champData['killsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))),1);
		$champStats[$matchType]['champions'][$champId]['deathsAverage'] = number_format((($champData['deathsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))),1);
		$champStats[$matchType]['champions'][$champId]['assistsAverage'] = number_format((($champData['assistsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))),1);
		$champStats[$matchType]['champions'][$champId]['towersAverage'] = number_format((($champData['towersCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))),1);
		$champStats[$matchType]['champions'][$champId]['inhibsAverage'] = number_format((($champData['inhibsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))),1);
		$champStats[$matchType]['champions'][$champId]['doubleKillsAverage'] = number_format((($champData['doubleKillsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))),2);
		$champStats[$matchType]['champions'][$champId]['tripleKillsAverage'] = number_format((($champData['tripleKillsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))),2);
		$champStats[$matchType]['champions'][$champId]['quadraKillsAverage'] = number_format((($champData['quadraKillsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))),2);
		$champStats[$matchType]['champions'][$champId]['pentaKillsAverage'] = number_format((($champData['pentaKillsCount']/($champData['matchesCount'] == 0 ? 1 : $champData['matchesCount']))),2);
		
		if(is_array($champStats[$matchType]['champions'][$champId]['spells']) && count($champStats[$matchType]['champions'][$champId]['spells']) > 0)
		{
			foreach($champStats[$matchType]['champions'][$champId]['spells'] as $spellId => $spellData)
			{
				$champStats[$matchType]['champions'][$champId]['spells'][$spellId]['spellWinRate'] = round((($spellData['countTimesWon']/$spellData['countTimesPicked']) * 100));
			}
		}
		
		if(is_array($champStats[$matchType]['champions'][$champId]['tier']) && count($champStats[$matchType]['champions'][$champId]['tier']) > 0)
		{
			foreach($champStats[$matchType]['champions'][$champId]['tier'] as $tierName => $tierData)
			{
				$champStats[$matchType]['champions'][$champId]['tier'][$tierName]['tierWinrate'] = round((($tierData['countTimesWon']/$tierData['countMatches']) * 100));
			}
		}
		
		if(is_array($champStats[$matchType]['champions'][$champId]['position']) && count($champStats[$matchType]['champions'][$champId]['position']) > 0)
		{
			foreach($champStats[$matchType]['champions'][$champId]['position'] as $positionName => $positionData)
			{
				$champStats[$matchType]['champions'][$champId]['position'][$positionName]['positionWinrate'] = round((($positionData['countTimesWon']/$positionData['countMatches']) * 100));
			}
		}
		
		if(is_array($champStats[$matchType]['champions'][$champId]['items']) && count($champStats[$matchType]['champions'][$champId]['items']) > 0)
		{
			foreach($champStats[$matchType]['champions'][$champId]['items'] as $itemId => $itemData)
			{
				$champStats[$matchType]['champions'][$champId]['items'][$itemId]['itemWinrate'] = round((($itemData['countTimesWon']/$itemData['countMatches']) * 100));
			}
		}
		
		if(is_array($champStats[$matchType]['champions'][$champId]['runes']) && count($champStats[$matchType]['champions'][$champId]['runes']) > 0)
		{
			foreach($champStats[$matchType]['champions'][$champId]['runes'] as $runeId => $runeData)
			{
				$champStats[$matchType]['champions'][$champId]['runes'][$runeId]['runeWinrate'] = round((($runeData['countTimesWon']/$runeData['countMatches']) * 100));
			}
		}
		
		if(is_array($champStats[$matchType]['champions'][$champId]['masteries']) && count($champStats[$matchType]['champions'][$champId]['masteries']) > 0)
		{
			foreach($champStats[$matchType]['champions'][$champId]['masteries'] as $masteryId => $masteryData)
			{
				$champStats[$matchType]['champions'][$champId]['masteries'][$masteryId]['masteryWinrate'] = round((($masteryData['countTimesWon']/$masteryData['countMatches']) * 100));
			}
		}
	}

	$champStats[$matchType]['teams']['global']['winnerPercent'] = (int) @round(($champStats[$matchType]['teams']['100']['winnerPercent'] + $champStats[$matchType]['teams']['200']['winnerPercent']) / 2);
	$champStats[$matchType]['teams']['global']['winnerFirstDragonPercent'] = (int) @round(($champStats[$matchType]['teams']['100']['winnerFirstDragonPercent'] + $champStats[$matchType]['teams']['200']['winnerFirstDragonPercent']) / 2);
	$champStats[$matchType]['teams']['global']['winnerFirstNashorPercent'] = (int) @round(($champStats[$matchType]['teams']['100']['winnerFirstNashorPercent'] + $champStats[$matchType]['teams']['200']['winnerFirstNashorPercent']) / 2);
	$champStats[$matchType]['teams']['global']['winnerFirstBloodPercent'] = (int) @round(($champStats[$matchType]['teams']['100']['winnerFirstBloodPercent'] + $champStats[$matchType]['teams']['200']['winnerFirstBloodPercent']) / 2);
	$champStats[$matchType]['teams']['global']['winnerFirstTowerPercent'] = (int) @round(($champStats[$matchType]['teams']['100']['winnerFirstTowerPercent'] + $champStats[$matchType]['teams']['200']['winnerFirstTowerPercent']) / 2);
	$champStats[$matchType]['teams']['global']['winnerFirstInhibPercent'] = (int) @round(($champStats[$matchType]['teams']['100']['winnerFirstInhibPercent'] + $champStats[$matchType]['teams']['200']['winnerFirstInhibPercent']) / 2);
	$champStats[$matchType]['teams']['global']['killsBaronAverage'] = (int) @round(($champStats[$matchType]['teams']['100']['killsBaronAverage'] + $champStats[$matchType]['teams']['200']['killsBaronAverage']) / 2);
	$champStats[$matchType]['teams']['global']['killsDragonAverage'] = (int) @round(($champStats[$matchType]['teams']['100']['killsDragonAverage'] + $champStats[$matchType]['teams']['200']['killsDragonAverage']) / 2);
	$champStats[$matchType]['teams']['global']['killsTowersAverage'] = (int) @round(($champStats[$matchType]['teams']['100']['killsTowersAverage'] + $champStats[$matchType]['teams']['200']['killsTowersAverage']) / 2);
	$champStats[$matchType]['teams']['global']['killsInhibsAverage'] = (int) @round(($champStats[$matchType]['teams']['100']['killsInhibsAverage'] + $champStats[$matchType]['teams']['200']['killsInhibsAverage']) / 2);

	/* Summoners */
	foreach($champStats[$matchType]['summoners']['winnerIcon']['count'] as $iconId => $winCount)
	{
		$champStats[$matchType]['summoners']['winnerIcon']['average'][$iconId] = round((($winCount/$gamesWithSummonerData) * 100));
		
		if(is_array($champStats[$matchType]['summoners']['tier']) && count($champStats[$matchType]['summoners']['tier']) > 0)
		{
			foreach($champStats[$matchType]['summoners']['tier'] as $tierName => $tierData)
			{
				$champStats[$matchType]['summoners']['tier'][$tierName]['tierWinrate'] = round((($tierData['countTimesWon']/($tierData['countMatches'] == 0 ? 1 : $tierData['countMatches'])) * 100));
			}
		}
		
		if(is_array($champStats[$matchType]['summoners']['position']) && count($champStats[$matchType]['summoners']['position']) > 0)
		{
			foreach($champStats[$matchType]['summoners']['position'] as $positionName => $positionData)
			{
				$champStats[$matchType]['summoners']['position'][$positionName]['positionWinrate'] = round((($positionData['countTimesWon']/($positionData['countMatches'] == 0 ? 1 : $positionData['countMatches'])) * 100));
			}
		}
		
		if(is_array($champStats[$matchType]['summoners']['runes']) && count($champStats[$matchType]['summoners']['runes']) > 0)
		{
			foreach($champStats[$matchType]['summoners']['runes'] as $runeId => $runeData)
			{
				$champStats[$matchType]['summoners']['runes'][$runeId]['runeWinrate'] = round((($runeData['countTimesWon']/($runeData['countMatches'] == 0 ? 1 : $runeData['countMatches'])) * 100));
			}
		}
		
		if(is_array($champStats[$matchType]['summoners']['masteries']) && count($champStats[$matchType]['summoners']['masteries']) > 0)
		{
			foreach($champStats[$matchType]['summoners']['masteries'] as $masteryId => $masteryData)
			{
				$champStats[$matchType]['summoners']['masteries'][$masteryId]['runeWinrate'] = round((($masteryData['countTimesWon']/($masteryData['countMatches'] == 0 ? 1 : $masteryData['countMatches'])) * 100));
			}
		}
	}

}

$champStats['totalSpellsSet'] = $champStats['NORMAL']['totalSpellsSet'] + $champStats['RANKEDSOLO']['totalSpellsSet'] + $champStats['RANKEDTEAM']['totalSpellsSet'];
$champStats['matches'] = $champStats['NORMAL']['matches'] + $champStats['RANKEDSOLO']['matches'] + $champStats['RANKEDTEAM']['matches'];
$dbPath = md5(JSON_STORE_SECRET_KEY.LOL_PATCH.JSON_STORE_SECRET_KEY2);
if ( ! file_exists(WEB_BASEDIR . '/' . DATABASE_PATH_STATS .'/' . $dbPath)){ mkdir(WEB_BASEDIR . '/' . DATABASE_PATH_STATS . '/' . $dbPath, 0777, true); }
file_put_contents(WEB_BASEDIR . '/' . DATABASE_PATH_STATS . '/' . $dbPath . '/stats.json', json_encode($champStats));
file_put_contents(WEB_BASEDIR . '/' . DATABASE_PATH_STATS . '/' . $dbPath . '/stats_rankedsolo.json', json_encode($champStats['RANKEDSOLO']));
file_put_contents(WEB_BASEDIR . '/' . DATABASE_PATH_STATS . '/' . $dbPath . '/stats_rankedteam.json', json_encode($champStats['RANKEDTEAM']));
file_put_contents(WEB_BASEDIR . '/' . DATABASE_PATH_STATS . '/' . $dbPath . '/stats_normal.json', json_encode($champStats['NORMAL']));
/* Index stats */
$champStatsIndex = array('blueSideWR' => $champStats['RANKEDSOLO']['teams']['100']['winnerPercent'], 'redSideWR' => $champStats['RANKEDSOLO']['teams']['200']['winnerPercent']);
if(file_exists(WEB_BASEDIR . '/' . DATABASE_PATH_STATS . '/stats_indexpage.json'))
{
	$preIndexData = json_decode(file_get_contents(WEB_BASEDIR . '/' . DATABASE_PATH_STATS . '/stats_indexpage.json'),true);
	$champStatsIndex['charts'] = $preIndexData['charts'];
	if(count($champStatsIndex['charts']) > $config['index.chart.limitdays'])
	{
		$timesToShift = count($champStatsIndex['charts']) - $config['index.chart.limitdays'];
		for($i = 0; $i < $timesToShift; $i++)
		{
			array_shift($champStatsIndex['charts']);
		}
	}
	$champStatsIndex['charts'][date('m-d')] = array('soloqGames' => $champStats['RANKEDSOLO']['matches'],'teamsqGames' => $champStats['RANKEDTEAM']['matches'],'normalGames' => $champStats['NORMAL']['matches']);
}
else
{
	$champStatsIndex['charts'][date('m-d')] = array('soloqGames' => $champStats['RANKEDSOLO']['matches'],'teamsqGames' => $champStats['RANKEDTEAM']['matches'],'normalGames' => $champStats['NORMAL']['matches']);
}
ksort($champStatsIndex['charts']);
file_put_contents(WEB_BASEDIR . '/' . DATABASE_PATH_STATS . '/stats_indexpage.json', json_encode($champStatsIndex));

$core->setStatus('enabled','stats');