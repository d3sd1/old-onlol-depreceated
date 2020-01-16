<?php
ini_set('max_execution_time', 0);
require('../core.php');
ob_start('ob_gzhandler');
$api->forceUpdate(true);
if(LOL_PATCH_FULL != $core->getReason('champion_images'))
{
	foreach($api->staticData('champion',true)['data'] as $champ_key => $champ_data)
	{
		/* Splash images */
		$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/'.LOL_PATCH_FULL.'/img/champion/'.$champ_data['key'].'.png',$config['web.basedir'].'/style/game/champions/square/'.$champ_data['id'].'.png');
		foreach($champ_data['skins'] as $skin_id => $skin_data)
		{
			$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/img/champion/splash/'.$champ_data['key'].'_'.$skin_id.'.jpg',$config['web.basedir'].'/style/game/champions/splash/'.$champ_data['id'].'_'.$skin_id.'.jpg');
			$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/img/champion/loading/'.$champ_data['key'].'_'.$skin_id.'.jpg',$config['web.basedir'].'/style/game/champions/loading/'.$champ_data['id'].'_'.$skin_id.'.jpg');
		}
		/* Kit images */
		$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/'.LOL_PATCH_FULL.'/img/passive/'.$champ_data['passive']['image']['full'],$config['web.basedir'].'/style/game/champions/kit/'.$champ_data['id'].'_p.png');
		$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/'.LOL_PATCH_FULL.'/img/spell/'.$champ_data['spells']['0']['image']['full'],$config['web.basedir'].'/style/game/champions/kit/'.$champ_data['id'].'_q.png');
		$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/'.LOL_PATCH_FULL.'/img/spell/'.$champ_data['spells']['1']['image']['full'],$config['web.basedir'].'/style/game/champions/kit/'.$champ_data['id'].'_w.png');
		$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/'.LOL_PATCH_FULL.'/img/spell/'.$champ_data['spells']['2']['image']['full'],$config['web.basedir'].'/style/game/champions/kit/'.$champ_data['id'].'_e.png');
		$core->imgCompress('http://ddragon.leagueoflegends.com/cdn/'.LOL_PATCH_FULL.'/img/spell/'.$champ_data['spells']['3']['image']['full'],$config['web.basedir'].'/style/game/champions/kit/'.$champ_data['id'].'_r.png');
	}
	$core->setStatus('enabled','champion_images',LOL_PATCH_FULL);
}
$api->forceUpdate(false);