<?php
require('../class/constants.php');
require('../class/database.php');
require('../class/lol.php');
require('../class/onlol.php');
require('../class/riot.php');
$last_version = onlol::readjson('https://ddragon.leagueoflegends.com/api/versions.json')[0];
$db->query('UPDATE config SET value="'.$last_version.'" WHERE name="lol_patch_champstatus"') or die($db->error);
$free_to_play_updated = false;
$freetoplay = array();
$db->query('TRUNCATE TABLE lol_champs_rotation') or die($db->error);
foreach($lol_servers as $region => $server_key)
{
	$region = strtoupper($region);
	$server_data = onlol::readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.2/champion?api_key='.LOL_API_KEY);
	foreach($server_data as $champ_data_key)
	{
		foreach($champ_data_key as $champ_data)
		{
			if($free_to_play_updated == false && $region = 'EUW' && $champ_data['freeToPlay'] == true)
			{
				$freetoplay[] = $champ_data['id'];
			}
			onlol::setlog('cron_champstatus', 'Updating '.$champ_data['id']);
			if($db->query('SELECT id FROM lol_champs_status WHERE champ_id='.$champ_data['id'].' AND region="'.$region.'"')->num_rows > 0)
			{
				$db->query('UPDATE lol_champs_status SET active="'.boolval($champ_data['active']).'",bot_enabled="'.$champ_data['botEnabled'].'",bot_coopvsall_enabled="'.$champ_data['botMmEnabled'].'",ranked_enabled="'.boolval($champ_data['rankedPlayEnabled']).'" WHERE champ_id='.$champ_data['id'].' AND region="'.$region.'"') or die($db->error);
			}
			else
			{
				$db->query('INSERT INTO lol_champs_status (champ_id,active,bot_enabled,bot_coopvsall_enabled,ranked_enabled,region) VALUES ('.$champ_data['id'].',"'.$champ_data['active'].'","'.$champ_data['botEnabled'].'","'.$champ_data['botMmEnabled'].'","'.$champ_data['rankedPlayEnabled'].'","'.$region.'")') or die($db->error);
			}
		}
	}
	if(count($freetoplay) > 0)
	{
		foreach($freetoplay as $champ_id)
		{
			onlol::setlog('cron_champrotation', 'New free to play champ: '.$champ_id);
			if($region != null)
			{
				$db->query('INSERT INTO lol_champs_rotation (champ_id,region) VALUES ('.$champ_id.',"'.$region.'")') or die($db->error);
			}
		}
		$free_to_play_updated = true;
	}
}