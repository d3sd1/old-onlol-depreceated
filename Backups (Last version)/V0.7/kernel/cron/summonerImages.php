<?php
ini_set('max_execution_time', 0);
require('../core.php');
ob_start('ob_gzhandler');
$api->forceUpdate(true);
if(LOL_PATCH_FULL != $core->getReason('summoner_images'))
{
	foreach($api->staticData('icon')['data'] as $icon_id => $icon_data)
	{
		$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/'.LOL_PATCH_FULL.'/img/profileicon/'.$icon_id.'.png',$config['web.basedir'].'/style/game/summoners/icons/'.$icon_id.'.png');
	}
	foreach($api->staticData('summoner-spell')['data'] as $spell_keyname => $spell_data)
	{
		$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/'.LOL_PATCH_FULL.'/img/spell/'.$spell_keyname.'.png',$config['web.basedir'].'/style/game/summoners/spells/'.$spell_data['id'].'.png');
	}
	foreach($api->staticData('mastery')['data'] as $mastery_id => $mastery_data)
	{
		$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/'.LOL_PATCH_FULL.'/img/mastery/'.$mastery_id.'.png',$config['web.basedir'].'/style/game/summoners/masteries/'.$mastery_id.'.png');
	}
	foreach($api->staticData('rune')['data'] as $rune_id => $rune_data)
	{
		$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/'.LOL_PATCH_FULL.'/img/rune/'.$rune_id.'.png',$config['web.basedir'].'/style/game/summoners/runes/'.$rune_id.'.png');
	}
	$core->setStatus('enabled','summoner_images',LOL_PATCH_FULL);
}
$api->forceUpdate(false);