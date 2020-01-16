<?php
include('../core.php');
$core->setStatus('updating','toplists_champmastery');
/* Clear all previous data */
$db->query('TRUNCATE TABLE cron_toplists_champmastery');
$toplists = array();
$toplists['champions'] = array();
$gameChampions = $api->staticData('champion');
foreach($gameChampions['data'] as $keyName => $champData)
{
	$toplists['champions'][$champData['id']] = array();
}
$maxChampsPerChampToplist = $config['cron.toplists.summonersperchamp'];
$validSummonersMinimumElo = $config['cron.toplists.minimumelo'];
switch($validSummonersMinimumElo)
{
	case 'UNRANKED':
	$minimumEloQuery = 'soloq_tier="UNRANKED" OR soloq_tier="BRONZE" OR soloq_tier="SILVER" OR soloq_tier="GOLD" OR soloq_tier="PLATINUM" OR soloq_tier="DIAMOND" OR soloq_tier="MASTER" OR soloq_tier="CHALLENGER"';
	break;
	case 'BRONZE':
	$minimumEloQuery = 'soloq_tier="BRONZE" OR soloq_tier="SILVER" OR soloq_tier="GOLD" OR soloq_tier="PLATINUM" OR soloq_tier="DIAMOND" OR soloq_tier="MASTER" OR soloq_tier="CHALLENGER"';
	break;
	case 'SILVER':
	$minimumEloQuery = 'soloq_tier="SILVER" OR soloq_tier="GOLD" OR soloq_tier="PLATINUM" OR soloq_tier="DIAMOND" OR soloq_tier="MASTER" OR soloq_tier="CHALLENGER"';
	break;
	case 'GOLD':
	$minimumEloQuery = 'soloq_tier="GOLD" OR soloq_tier="PLATINUM" OR soloq_tier="DIAMOND" OR soloq_tier="MASTER" OR soloq_tier="CHALLENGER"';
	break;
	case 'PLATINUM':
	$minimumEloQuery = 'soloq_tier="PLATINUM" OR soloq_tier="DIAMOND" OR soloq_tier="MASTER" OR soloq_tier="CHALLENGER"';
	break;
	case 'DIAMOND':
	$minimumEloQuery = 'soloq_tier="DIAMOND" OR soloq_tier="MASTER" OR soloq_tier="CHALLENGER"';
	break;
	case 'MASTER':
	$minimumEloQuery = 'soloq_tier="MASTER" OR soloq_tier="CHALLENGER"';
	break;
	case 'CHALLENGER':
	$minimumEloQuery = 'soloq_tier="CHALLENGER"';
	break;
}
$summonersAllowed = $db->query('SELECT id FROM api_summoners_league WHERE '.$minimumEloQuery);
while($row = $summonersAllowed->fetch_row())
{
	$summonerChampMasteryAllChamps = json_decode($db->query('SELECT data FROM api_summoners_champmastery WHERE id="'.$row[0].'"')->fetch_row()[0],true);
	foreach($summonerChampMasteryAllChamps as $champId => $champData)
	{
		$champPoints = $champData['points'];
		if(array_key_exists($champId,$toplists['champions']) == FALSE)
		{
			$toplists['champions'][$champId] = array();
		}
		if(count($toplists['champions'][$champId]) < $maxChampsPerChampToplist)
		{
			if(array_key_exists($champPoints,$toplists['champions'][$champId]) == FALSE)
			{
				$toplists['champions'][$champId][$champPoints] = $row[0];
			}
			else
			{
				if(array_key_exists(($champPoints+1),$toplists['champions'][$champId]) == FALSE)
				{
					$toplists['champions'][$champId][($champPoints+1)] = $row[0];
				}
				else
				{
					$i = 1;
					while(true)
					{
						if(array_key_exists(($champPoints+$i),$toplists['champions'][$champId]) == FALSE)
						{
							$toplists['champions'][$champId][($champPoints+$i)] = $row[0];
							break;
						}
						$i++;
					}
				}
			}
		}
		else
		{
			$lowestChampSkill = array_keys($toplists['champions'][$champId],min($toplists['champions'][$champId]));
			unset($toplists['champions'][$champId][$lowestChampSkill]);
			if(array_key_exists($champPoints,$toplists['champions'][$champId]) == FALSE)
			{
				$toplists['champions'][$champId][$champPoints] = $row[0];
			}
			else
			{
				if(array_key_exists(($champPoints+1),$toplists['champions'][$champId]) == FALSE)
				{
					$toplists['champions'][$champId][($champPoints+1)] = $row[0];
				}
				else
				{
					$i = 1;
					while(true)
					{
						if(array_key_exists(($champPoints+$i),$toplists['champions'][$champId]) == FALSE)
						{
							$toplists['champions'][$champId][($champPoints+$i)] = $row[0];
							break;
						}
						$i++;
					}
				}
			}
		}
	}
}
$queryFormer = null;
asort($toplists['champions'],SORT_NUMERIC);
foreach($toplists['champions'] as $champId => $pointsCount)
{
	krsort($toplists['champions'][$champId],SORT_NUMERIC);
	$positionOnChampSkillList = 1;
	foreach($toplists['champions'][$champId] as $pointsCount => $summonerId)
	{
		if($queryFormer != null) { $queryFormer .= ','; }
		$queryFormer .= '("'.$summonerId.'",'.$champId.','.$positionOnChampSkillList.','.$pointsCount.')';
		$positionOnChampSkillList++;
	}
}
$db->query('INSERT INTO cron_toplists_champmastery (id,champ,position,points) VALUES '.$queryFormer) or die($db->error);
$core->setStatus('enabled','toplists_champmastery');