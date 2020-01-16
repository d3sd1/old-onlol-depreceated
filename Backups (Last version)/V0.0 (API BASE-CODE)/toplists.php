<?php
include('kernel/core.php');

if($core->getStatus('toplists') == 'enabled')
{
	$toplist = array();
	$toplistsSummoners = $db->query('SELECT id,champ,position,skill FROM cron_toplists');
	while($row = $toplistsSummoners->fetch_row())
	{
		if(array_key_exists($row[1],$toplist) == FALSE)
		{
			$toplist[$row[1]] = array();
		}
		$toplist[$row[1]][$row[2]] = array('summonerId' => $row[0], 'skill' => $row[3]);
	}
	print_r($toplist);
}
elseif($core->getStatus('toplists') == 'updating')
{
	echo 'rankings being updated. Wait a bit!';
}
elseif($core->getStatus('toplists') == 'disabled')
{
	echo 'rankings disabled';
}
?>