<?php
require('../class/constants.php');
require('../class/database.php');
require('../class/lol.php');
require('../class/onlol.php');
require('../class/riot.php');
foreach($lol_servers as $region => $key_region)
{
	$region = lol::parseserver($region);
	$region_shard = onlol::readjson('http://status.leagueoflegends.com/shards/'.$region);
	$server_status = 2;
		
	onlol::setlog('cron_shards','Updated shards for region '.$region);
	foreach($region_shard['services'] as $this_region)
	{
		if($this_region['status'] == 'offline')
		{
			$server_status = 1;
		}
		if($this_region['status'] == 'offline' && $this_region['slug'] == 'game')
		{
			$server_status = 0;
			break;
		}
		if($this_region['status'] == 'offline' && $this_region['slug'] == 'client')
		{
			$server_status = 0;
			break;
		}
	}
	$db->query('UPDATE lol_shards SET status="'.$server_status.'",timestamp_last_check='.time().' WHERE region="'.$region.'"');
}