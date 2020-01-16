<?php
include('kernel/core.php');

if($db->query('SELECT id FROM api_summoners_actualgame WHERE onGame="true"')->num_rows > 0)
{
	$liveGames = array();
	$liveGamesPlaying = $db->query('SELECT id,gameInfo FROM api_summoners_actualgame WHERE onGame="true"');
	while($row = $liveGamesPlaying->fetch_row())
	{
		$liveGames[$row[0]] = json_decode($row[1],true);
	}
}
else
{
	$liveGames = false;
}

print_r($liveGames);
?>