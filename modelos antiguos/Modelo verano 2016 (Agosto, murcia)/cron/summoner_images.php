<?php
ini_set('max_execution_time', 0);
require('../kernel/core.php');
ob_start('ob_gzhandler');
$last_version = core::extjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/versions?api_key='.$config['riot.api.key'])[0];
$web_version = core::get_web_versions('summoner_images');
if($last_version != $web_version)
{
	foreach(core::extjson('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/data/en_US/profileicon.json')['data'] as $icon_id => $icon_data)
	{
		core::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/profileicon/'.$icon_id.'.png',$config['web.basedir'].'/assets/game/summoners/icons/'.$icon_id.'.png');
	}
	foreach(core::extjson('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/data/en_US/summoner.json')['data'] as $spell_keyname => $spell_data)
	{
		core::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/spell/'.$spell_keyname.'.png',$config['web.basedir'].'/assets/game/summoners/spells/'.$spell_data['key'].'.png');
	}
	foreach(core::extjson('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/data/en_US/mastery.json')['data'] as $mastery_id => $mastery_data)
	{
		core::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/mastery/'.$mastery_id.'.png',$config['web.basedir'].'/assets/game/summoners/masteries/'.$mastery_id.'.png');
	}
	core::set_web_versions('summoner_images',$last_version);
	$db->query('INSERT INTO web_activity (type,web_version,time) VALUES ("summonerImages","'.$last_version.'","'.core::current_time().'")');
}