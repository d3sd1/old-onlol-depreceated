<?php
/* For reloading data from Riot's api */
class riot{
	public static function shards($region = 'all')
	{
		global $db;
		if($region = 'all')
		{
			global $lol_servers;
			foreach($lol_servers as $region => $key_region)
			{
				if($region != 'kr') // KR has no shards :/
				{
					$region = lol::parseserver($region);
					$region_shard = onlol::readjson('http://status.leagueoflegends.com/shards/'.$region);
					$server_status = 2;
					
					if(is_array($region_shard['services']))
					{
						onlol::setlog('cron_shards','Updated shards for region '.$region);
						$stop_region_status = false;
						foreach($region_shard['services'] as $this_region)
						{
							if($stop_region_status == false)
							{
								if($this_region['status'] == 'offline' && $this_region['slug'] != 'client')
								{
									$server_status = 1;
								}
								elseif($this_region['status'] == 'offline' && $this_region['slug'] == 'client')
								{
									$server_status = 0;
									$stop_region_status = true;
								}
							}
						}
					}
					else
					{
						$server_status = 2;
					}
					$db->query('UPDATE lol_shards SET status="'.$server_status.'",timestamp_last_check='.time().' WHERE region="'.$region.'"');
				}
				else
				{
					$db->query('UPDATE lol_shards SET status="2",timestamp_last_check='.time().' WHERE region="'.$region.'"');
				}
			}
		}
		else
		{
			$region = lol::parseserver($region);
			$region_shard = onlol::readjson('http://status.leagueoflegends.com/shards/'.$region);
			$server_status = 2;
			foreach($region_shard['services'] as $service)
			{
				onlol::setlog('cron_shards','Updated shards for region '.$region);
				if($services['status'] == 'offline' && $services['slug'] != 'client')
				{
					$server_status = 2;
				}
				elseif($services['status'] == 'offline' && $services['slug'] == 'client')
				{
					$server_status = 0;
					break;
				}
			}
			$db->query('UPDATE lol_shards SET status="'.$server_status.'",timestamp_last_check='.time().' WHERE region="'.$region.'"');
		}
	}
}