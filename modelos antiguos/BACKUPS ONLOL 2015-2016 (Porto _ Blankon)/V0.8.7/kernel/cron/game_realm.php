<?php
require('../class/constants.php');
require('../class/database.php');
require('../class/lol.php');
require('../class/onlol.php');
require('../class/riot.php');
$last_versions = onlol::readjson('https://ddragon.leagueoflegends.com/realms/na.json');
$db->query('TRUNCATE TABLE lol_versions');
foreach($last_versions as $name => $value)
{
	if(!is_array($value))
	{
		onlol::setlog('cron_realm', 'New realm updated: '.$name.' to '.$value);
		$db->query('INSERT INTO lol_realm (name,value) VALUES ("'.$name.'","'.$value.'")');
	}
	else
	{
		foreach($value as $subname => $subvalue)
		{
			if(!is_array($subvalue))
			{
				onlol::setlog('cron_realm', 'New realm updated: '.$subname.' to '.$subvalue);
				$db->query('INSERT INTO lol_realm (name,value) VALUES ("'.$subname.'","'.$subvalue.'")');
			}
			else
			{
				onlol::setlog('cron_realm', '[ERROR] Multidimensional array not parsed: '.serialize($subvalue));
				error_log('[ERROR] Multidimensional array not parsed: '.serialize($subvalue));
			}
		}
	}
}
