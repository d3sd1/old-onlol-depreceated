<?php
include('kernel/core.php');

if($core->getStatus('toplists_champmastery') == 'enabled')
{
	$toplist = array();
	$toplistsSummoners = $db->query('SELECT id,champ,position,points FROM cron_toplists_champmastery');
	while($row = $toplistsSummoners->fetch_row())
	{
		if(array_key_exists($row[1],$toplist) == FALSE)
		{
			$toplist[$row[1]] = array();
		}
		$toplist[$row[1]][$row[2]] = array('summonerId' => $row[0], 'points' => $row[3]);
	}
	print_r($toplist);
}
elseif($core->getStatus('toplists_champmastery') == 'updating')
{
	echo 'rankings being updated. Wait a bit!';
}
elseif($core->getStatus('toplists_champmastery') == 'disabled')
{
	echo 'rankings disabled';
}
?>