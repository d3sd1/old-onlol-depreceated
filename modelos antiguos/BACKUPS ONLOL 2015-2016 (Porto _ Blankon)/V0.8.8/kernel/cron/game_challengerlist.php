<?php
require('../class/constants.php');
require('../class/database.php');
require('../class/lol.php');
require('../class/onlol.php');
require('../class/riot.php');
$db->query('TRUNCATE TABLE lol_stats_bestsummoners');
$db->query('TRUNCATE TABLE lol_stats_bestteams');
foreach($lol_servers as $region => $key_region)
{
	$region = lol::parseserver($region);
	$queuesolo5x5_data = onlol::readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key='.LOL_API_KEY);
	if(array_key_exists('entries',$queuesolo5x5_data))
	{
		foreach($queuesolo5x5_data['entries'] as $queue_data)
		{
			$db->query('INSERT INTO lol_stats_bestsummoners (name,lp,region,wins,losses) VALUES ("'.$queue_data['playerOrTeamName'].'",'.$queue_data['leaguePoints'].',"'.$region.'",'.$queue_data['wins'].','.$queue_data['losses'].')');
		}
	}
	
	$queueteam5x5_data = onlol::readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v2.5/league/challenger?type=RANKED_TEAM_5x5&api_key='.LOL_API_KEY);
	if(array_key_exists('entries',$queueteam5x5_data))
	{
		foreach($queueteam5x5_data['entries'] as $queue_data)
		{
			$db->query('INSERT INTO lol_stats_bestteams (name,lp,region,queue,wins,losses) VALUES ("'.$queue_data['playerOrTeamName'].'","'.$queue_data['leaguePoints'].'","'.$region.'","RANKED_TEAM_5x5","'.$queue_data['wins'].'","'.$queue_data['losses'].'")');
		}
	}
	
	$queueteam3x3_data = onlol::readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v2.5/league/challenger?type=RANKED_TEAM_3x3&api_key='.LOL_API_KEY);
	
	if(array_key_exists('entries',$queueteam3x3_data))
	{
		foreach($queueteam3x3_data['entries'] as $queue_data)
		{
			$db->query('INSERT INTO lol_stats_bestteams (name,lp,region,queue,wins,losses) VALUES ("'.$queue_data['playerOrTeamName'].'","'.$queue_data['leaguePoints'].'","'.$region.'","RANKED_TEAM_3x3","'.$queue_data['wins'].'","'.$queue_data['losses'].'")');
		}
	}
}