<?php
ini_set('max_execution_time', 0);
require('../kernel/core.php');
$game_shards = core::extjson('http://status.leagueoflegends.com/shards');
$game_json = array();
foreach($game_shards as $region_id => $region_data)
{
	$game_json[$region_data['slug']] = array();
	$region_shards = core::extjson('http://status.leagueoflegends.com/shards/'.$region_data['slug']);
	$region_status_points = 0;
	foreach($region_shards['services'] as $service_id => $service_data)
	{
		$game_json[$region_data['slug']][$service_data['slug']] = array('status' => $service_data['status']);
		if($service_data['status'] == 'online')
		{
			$region_status_points = $region_status_points+1;
		}
		else
		{
			$region_status_points = $region_status_points-1;
		}
	}
	$status_maxpoints = count($region_shards['services']);
	if($region_status_points == $status_maxpoints)
	{
		$game_json[$region_data['slug']]['general_status'] = 'online';
	}
	elseif($region_status_points > (int) ($status_maxpoints/2))
	{
		$game_json[$region_data['slug']]['general_status'] = 'troubles';
	}
	elseif($region_status_points < (int) ($status_maxpoints/2))
	{
		$game_json[$region_data['slug']]['general_status'] = 'offline';
	}
}
$status_jsonfile = fopen($config['web.basedir'].'/database/game_status.json', 'w');
fwrite($status_jsonfile, json_encode($game_json));
fclose($status_jsonfile);
$db->query('INSERT INTO web_activity (type,web_version,time) VALUES ("shardsUpdated","","'.core::current_time().'")');