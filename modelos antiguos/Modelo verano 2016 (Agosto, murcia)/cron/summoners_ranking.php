<?php
require('../kernel/core.php');
$allSummoners = $db->query('SELECT summonerId,summonerName,region FROM lol_summoners_leagues ORDER BY FIELD(tier,\'C\', \'M\', \'D\', \'P\', \'G\', \'S\', \'B\', \'U\'), FIELD(division,\'I\', \'II\', \'III\', \'IV\', \'V\'), lp');
$db->query('TRUNCATE TABLE lol_summoners_ranking');
$positionGlobal = 1;
$positionRegion = array();
foreach($regions as $region)
{
	$positionRegion[$region] = 1;
}
while($row = $allSummoners->fetch_row())
{
	$db->query('INSERT INTO lol_summoners_ranking (summonerId,summonerName,region,positionRegion,position) VALUES ("'.$row[0].'","'.$row[1].'","'.$row[2].'","'.$positionRegion[$row[2]].'","'.$positionGlobal.'")');
	$positionGlobal++;
	$positionRegion[$row[2]]++;
}
$db->query('INSERT INTO web_activity (type,web_version,time) VALUES ("rankingsUpdated","","'.core::current_time().'")');