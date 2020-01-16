<?php
ini_set('max_execution_time', 0);
require('../kernel/core.php');
$tiers = array('B', 'S', 'G', 'P', 'D', 'M', 'C');
$divisions = array('I', 'II', 'III', 'IV', 'V');
$db->query('TRUNCATE TABLE web_medium_mmr');
foreach($tiers as $thisTier)
{
	if($thisTier != 'M' && $thisTier != 'C')
	{
		foreach($divisions as $thisDivision)
		{
			$thisMMR = $db->query('SELECT Avg(mmr) AS mediumMMR FROM lol_summoners_leagues WHERE tier="'.$thisTier.'" AND division="'.$thisDivision.'"')->fetch_assoc()['mediumMMR'];
			if($thisMMR == null OR $thisMMR == 0)
			{
				$thisMMR = 0;
			}
			$db->query('INSERT INTO web_medium_mmr (mmr,division,tier) VALUES ('.round($thisMMR).',"'.$thisDivision.'","'.$thisTier.'")');
		}
	}
}
$db->query('INSERT INTO web_activity (type,web_version,time) VALUES ("mmrAlgorythm","","'.core::current_time().'")');