<?php
ini_set('max_execution_time', 0);
require('../kernel/core.php');
ob_start('ob_gzhandler');
$last_version = core::extjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/versions?api_key='.$config['riot.api.key'])[0];
$web_version = core::get_web_versions('champion_images');
if($last_version != $web_version)
{
	foreach(core::extjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion?api_key='.$config['riot.api.key'])['data'] as $champ_key => $champ_data)
	{
		/* Splash images */
		core::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/champion/'.$champ_data['key'].'.png',$config['web.basedir'].'/assets/game/champions/square/'.$champ_data['key'].'.png');
		foreach($champ_data['skins'] as $skin_id => $skin_data)
		{
			core::imgcompress('http://ddragon.leagueoflegends.com/cdn/img/champion/splash/'.$champ_data['key'].'_'.$skin_id.'.jpg',$config['web.basedir'].'/assets/game/champions/splash/'.$champ_data['key'].'_'.$skin_id.'.jpg');
			core::imgcompress('http://ddragon.leagueoflegends.com/cdn/img/champion/loading/'.$champ_data['key'].'_'.$skin_id.'.jpg',$config['web.basedir'].'/assets/game/champions/loading/'.$champ_data['key'].'_'.$skin_id.'.jpg');
		}
		/* Kit images */
		core::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/passive/'.$champ_data['passive']['image']['full'],$config['web.basedir'].'/assets/game/champions/kit/'.$champ_data['key'].'_p.png');
		core::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/spell/'.$champ_data['spells']['0']['image']['full'],$config['web.basedir'].'/assets/game/champions/kit/'.$champ_data['key'].'_q.png');
		core::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/spell/'.$champ_data['spells']['1']['image']['full'],$config['web.basedir'].'/assets/game/champions/kit/'.$champ_data['key'].'_w.png');
		core::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/spell/'.$champ_data['spells']['2']['image']['full'],$config['web.basedir'].'/assets/game/champions/kit/'.$champ_data['key'].'_e.png');
		core::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/spell/'.$champ_data['spells']['3']['image']['full'],$config['web.basedir'].'/assets/game/champions/kit/'.$champ_data['key'].'_r.png');
	}
	core::set_web_versions('champion_images',$last_version);
	$db->query('INSERT INTO web_activity (type,web_version,time) VALUES ("champsImages","'.$last_version.'","'.core::current_time().'")');
}