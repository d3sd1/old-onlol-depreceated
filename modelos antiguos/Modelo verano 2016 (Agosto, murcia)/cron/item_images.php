<?php
ini_set('max_execution_time', 0);
require('../kernel/core.php');
ob_start('ob_gzhandler');
$last_version = core::extjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/versions?api_key='.$config['riot.api.key'])[0];
$web_version = core::get_web_versions('item_images');
if($last_version != $web_version)
{
	foreach(core::extjson('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/data/en_US/item.json')['data'] as $item_id => $item_data)
	{
		core::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/item/'.$item_id.'.png',$config['web.basedir'].'/assets/game/summoners/items/'.$item_id.'.png');
	}
	core::set_web_versions('item_images',$last_version);
	$db->query('INSERT INTO web_activity (type,web_version,time) VALUES ("itemImages","'.$last_version.'","'.core::current_time().'")');
}