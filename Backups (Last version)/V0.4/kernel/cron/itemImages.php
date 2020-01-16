<?php
ini_set('max_execution_time', 0);
require('../core.php');
ob_start('ob_gzhandler');
$api->forceUpdate(true);
if(LOL_PATCH_FULL != $core->getReason('item_images'))
{
	foreach($api->staticData('item')['data'] as $item_id => $item_data)
	{
		$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/'.LOL_PATCH_FULL.'/img/item/'.$item_id.'.png',$config['web.basedir'].'/style/game/summoners/items/'.$item_id.'.png');
	}
	$core->setStatus('enabled','item_images',LOL_PATCH_FULL);
}
$api->forceUpdate(false);