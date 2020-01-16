<?php
ini_set('max_execution_time', 0);
require('../kernel/core.php');

$last_version = core::extjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/versions?api_key='.$config['riot.api.key'])[0];
$web_version = core::get_web_versions('champion_data');
if($last_version != $web_version)
{
	$add_basedata = 0;
	$db->query('TRUNCATE TABLE lol_champions');
	$db->query('TRUNCATE TABLE lol_champions_stats');
	$db->query('TRUNCATE TABLE lol_champions_skins');
	foreach($langs as $code => $name)
	{
		$champs_json_array = array();
		$json_champ_data = core::extjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion?locale='.$code.'&champData=all&api_key='.$config['riot.api.key']); //1 Per region
		foreach($json_champ_data['data'] as $champ_key => $champ_basedata)
		{
			$champs_json_array[$champ_basedata['id']] = array('name' => $champ_basedata['name'],'key' => $champ_basedata['key']);
			if($add_basedata == 0)
			{
				$db->query('INSERT INTO `lol_champions_stats`(`champ_id`, `recommended_items`, `armor`, `armor_lvl`, `ad`, `ad_lvl`, `attackrange`, `attackspeedoffset`, `attackspeedperlevel`, `crit`, `critperlevel`, `hp`, `hpperlevel`, `hpregen`, `hpregenperlevel`, `movespeed`, `mp`, `mpperlevel`, `mpregen`, `mpregenperlevel`, `spellblock`, `spellblockperlevel`, `info_attack`, `info_defense`, `info_magic`, `info_difficulty`, `partype`, `tags`, `keyname`, `skins_count`) VALUES ("'.$champ_basedata['id'].'","'.addslashes(json_encode($champ_basedata['recommended'])).'","'.$champ_basedata['stats']['armor'].'","'.$champ_basedata['stats']['armorperlevel'].'","'.$champ_basedata['stats']['attackdamage'].'","'.$champ_basedata['stats']['attackdamageperlevel'].'","'.$champ_basedata['stats']['attackrange'].'","'.$champ_basedata['stats']['attackspeedoffset'].'","'.$champ_basedata['stats']['attackspeedperlevel'].'","'.$champ_basedata['stats']['crit'].'","'.$champ_basedata['stats']['critperlevel'].'","'.$champ_basedata['stats']['hp'].'","'.$champ_basedata['stats']['hpperlevel'].'","'.$champ_basedata['stats']['hpregen'].'","'.$champ_basedata['stats']['hpregenperlevel'].'","'.$champ_basedata['stats']['movespeed'].'","'.$champ_basedata['stats']['mp'].'","'.$champ_basedata['stats']['mpperlevel'].'","'.$champ_basedata['stats']['mpregen'].'","'.$champ_basedata['stats']['mpregenperlevel'].'","'.$champ_basedata['stats']['spellblock'].'","'.$champ_basedata['stats']['spellblockperlevel'].'","'.$champ_basedata['info']['attack'].'","'.$champ_basedata['info']['defense'].'","'.$champ_basedata['info']['magic'].'","'.$champ_basedata['info']['difficulty'].'","'.$champ_basedata['partype'].'","'.implode(';',$champ_basedata['tags']).'","'.$champ_basedata['key'].'","'.(count($champ_basedata['skins'])-1).'")');
			}
			$db->query('INSERT INTO `lol_champions`(`lang`, `champ_id`, `lore`, `blurb`, `allytips`, `enemytips`, `spell_p`, `spell_q`, `spell_w`, `spell_e`, `spell_r`, `title`, `keyname`, `champ_name`) VALUES ("'.$code.'","'.$champ_basedata['id'].'","'.addslashes($champ_basedata['lore']).'","'.addslashes($champ_basedata['blurb']).'","'.addslashes(json_encode($champ_basedata['allytips'])).'","'.addslashes(json_encode($champ_basedata['enemytips'])).'","'.addslashes(json_encode($champ_basedata['passive'])).'","'.addslashes(json_encode($champ_basedata['spells']['0'])).'","'.addslashes(json_encode($champ_basedata['spells']['1'])).'","'.addslashes(json_encode($champ_basedata['spells']['2'])).'","'.addslashes(json_encode($champ_basedata['spells']['3'])).'","'.addslashes($champ_basedata['title']).'","'.$champ_basedata['key'].'","'.$champ_basedata['name'].'")');
			foreach($champ_basedata['skins'] as $skin_num => $skin_data)
			{
				$db->query('INSERT INTO `lol_champions_skins`(`champ_id`, `lang`, `skin_id`, `name`, `skin_num`) VALUES ("'.$champ_basedata['id'].'","'.$code.'","'.$skin_data['id'].'","'.$skin_data['name'].'","'.$skin_data['num'].'")');
			}
		}
		$add_basedata++;
		
		$champs_json = fopen($config['web.basedir'].'/database/champs/full/'.$code.'.json', 'w');
		fwrite($champs_json, json_encode($champs_json_array));
		fclose($champs_json);
	}
	core::set_web_versions('champion_data',$last_version);
	$db->query('INSERT INTO web_activity (type,web_version,time) VALUES ("champsUpdated","'.$last_version.'","'.core::current_time().'")');
}


$db->query('TRUNCATE TABLE lol_champions_inactive');
$db->query('TRUNCATE TABLE lol_champions_freetoplay');
$db->query('TRUNCATE TABLE lol_champions_botdisabled');
$db->query('TRUNCATE TABLE lol_champions_rankeddisabled');
$champ_status = core::extjson('https://euw.api.pvp.net/api/lol/euw/v1.2/champion?freeToPlay=false&api_key='.$config['riot.api.key']); //Only 1
foreach($champ_status['champions'] as $champ_id => $champ_data)
{
	if($champ_data['active'] == false)
	{
		$db->query('INSERT INTO lol_champions_inactive (champ_id) VALUES ("'.$champ_data['id'].'")');
	}
	if($champ_data['freeToPlay'] == true)
	{
		$db->query('INSERT INTO lol_champions_freetoplay (champ_id) VALUES ("'.$champ_data['id'].'")');
	}
	if($champ_data['botEnabled'] == false)
	{
		$db->query('INSERT INTO lol_champions_botdisabled (champ_id) VALUES ("'.$champ_data['id'].'")');
	}
	if($champ_data['rankedPlayEnabled'] == false)
	{
		$db->query('INSERT INTO lol_champions_rankeddisabled (champ_id) VALUES ("'.$champ_data['id'].'")');
	}
}
$db->query('INSERT INTO web_activity (type,web_version,time) VALUES ("champsStatusUpdated","'.$last_version.'","'.core::current_time().'")');