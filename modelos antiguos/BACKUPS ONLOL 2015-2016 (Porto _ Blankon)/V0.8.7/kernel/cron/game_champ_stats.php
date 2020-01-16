<?php
require('../class/constants.php');
require('../class/database.php');
require('../class/lol.php');
require('../class/onlol.php');
require('../class/riot.php');
$today_games = $db->query('SELECT participants,creation_timestamp,winner FROM lol_matches WHERE creation_timestamp<'.(time()+86400).' AND creation_timestamp>'.(time()-86400))->fetch_row();
while($row = $today_games)
{
	//add champ stats on this day
	$game_champs = json_decode($row[0],true);
	$game_date = $row[1];
	foreach($game_champs as $summoner_id => $data)
	{
		
	}
}