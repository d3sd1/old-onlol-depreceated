<?php
require('../class/constants.php');
require('../class/database.php');
require('../class/lol.php');
require('../class/onlol.php');
require('../class/riot.php');
$last_version = onlol::readjson('https://ddragon.leagueoflegends.com/api/versions.json')[0];
if(onlol::config('lol_patch_images') != $last_version)
{
	/* Update config */
	$db->query('UPDATE config SET value="'.$last_version.'" WHERE name="lol_patch_images"') or die($db->error);
	$champions = onlol::readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion?champData=all&api_key='.LOL_API_KEY);
	/* Champions base,splash,loading,kit image */
	foreach($champions['data'] as $keyname => $data)
	{
		/* Kit */
		if(!file_exists(ROOTPATH.'/game/champions/'.$keyname.'/kit/'))
		{
			mkdir(ROOTPATH.'/game/champions/'.$keyname.'/kit/', 0777, true);
			onlol::setlog('cron_images', 'Created path [kit] for '.$keyname);
		}
		/* P */
		onlol::setlog('cron_images', 'Downloaded kit [P] image for '.$keyname);
		onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/passive/'.str_replace(' ', '%20',$data['passive']['image']['full']), ROOTPATH.'/game/champions/'.$keyname.'/kit/p.png');
		/* Q */
		onlol::setlog('cron_images', 'Downloaded kit [P] image for '.$keyname);
		onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/spell/'.str_replace(' ', '%20',$data['spells'][0]['image']['full']), ROOTPATH.'/game/champions/'.$keyname.'/kit/q.png');
		/* W */
		onlol::setlog('cron_images', 'Downloaded kit [W] image for '.$keyname);
		onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/spell/'.str_replace(' ', '%20',$data['spells'][1]['image']['full']), ROOTPATH.'/game/champions/'.$keyname.'/kit/w.png');
		/* E */
		onlol::setlog('cron_images', 'Downloaded kit [E] image for '.$keyname);
		onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/spell/'.str_replace(' ', '%20',$data['spells'][2]['image']['full']), ROOTPATH.'/game/champions/'.$keyname.'/kit/e.png');
		/* R */
		onlol::setlog('cron_images', 'Downloaded kit [R] image for '.$keyname);
		onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/spell/'.str_replace(' ', '%20',$data['spells'][3]['image']['full']), ROOTPATH.'/game/champions/'.$keyname.'/kit/r.png');
		/* Champ images */
		$skin_num = 0;
		$no_more_skins = false;
		if(!file_exists(ROOTPATH.'/game/champions/'.$keyname.'/base/'))
		{
			mkdir(ROOTPATH.'/game/champions/'.$keyname.'/base/', 0777, true);
			onlol::setlog('cron_images', 'Created path [base] for '.$keyname);
		}
		if(!file_exists(ROOTPATH.'/game/champions/'.$keyname.'/splash/'))
		{
			mkdir(ROOTPATH.'/game/champions/'.$keyname.'/splash/', 0777, true);
			onlol::setlog('cron_images', 'Created path [splash] for '.$keyname);
		}
		if(!file_exists(ROOTPATH.'/game/champions/'.$keyname.'/loading/'))
		{
			mkdir(ROOTPATH.'/game/champions/'.$keyname.'/loading/', 0777, true);
			onlol::setlog('cron_images', 'Created path [loading] for '.$keyname);
		}
		onlol::setlog('cron_images', 'Downloaded base image for '.$keyname.' and skin number '.$skin_num);
		onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/champion/'.$keyname.'.png', ROOTPATH.'/game/champions/'.$keyname.'/base/0.png');
		while($no_more_skins == false)
		{
			if(onlol::check_url('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/champion/'.$keyname.'_'.$skin_num.'.png') == TRUE)
			{
				onlol::setlog('cron_images', 'Downloaded loading image for '.$keyname.' and skin number '.$skin_num);
				onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/img/champion/loading/'.$keyname.'_'.$skin_num.'.jpg', ROOTPATH.'/game/champions/'.$keyname.'/loading/'.$skin_num.'.jpg');
				
				onlol::setlog('cron_images', 'Downloaded splash image for '.$keyname.' and skin number '.$skin_num);
				onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/img/champion/splash/'.$keyname.'_'.$skin_num.'.jpg', ROOTPATH.'/game/champions/'.$keyname.'/splash/'.$skin_num.'.jpg');
			}
			else
			{
				$no_more_skins = true;
			}
			$skin_num++;
		}
	}
	/* Summoner icons */
	$summoner_icons = onlol::readjson('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/data/en_US/profileicon.json');
	foreach($summoner_icons['data'] as $icon)
	{
		onlol::setlog('cron_images', 'Downloaded icon image '.$icon['id']);
		onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/profileicon/'.$icon['image']['full'], ROOTPATH.'/game/icons/'.$icon['image']['full']);
	}
	/* Summoner spell icons */
	$summoner_spells = onlol::readjson('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/data/en_US/summoner.json');
	foreach($summoner_spells['data'] as $spell => $data)
	{
		onlol::setlog('cron_images', 'Downloaded summoner spell image '.$data['image']['full']);
		onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/spell/'.$data['image']['full'], ROOTPATH.'/game/spells/'.$data['image']['full']);
	}
	/* Rune icons */
	$runes = onlol::readjson('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/data/en_US/rune.json');
	foreach($runes['data'] as $rune => $data)
	{
		onlol::setlog('cron_images', 'Downloaded rune image '.$rune);
		copy('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/rune/'.$data['image']['full'], ROOTPATH.'/game/runes/'.$rune.'.png'); // For saving transparencies
	}
	/* Masteries icons */
	$masteries = onlol::readjson('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/data/en_US/mastery.json');
	foreach($masteries['data'] as $mastery => $data)
	{
		onlol::setlog('cron_images', 'Downloaded mastery image '.$data['image']['full']);
		onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/mastery/'.$data['image']['full'], ROOTPATH.'/game/masteries/'.$data['image']['full']);
	}
	/* Items */
	$masteries = onlol::readjson('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/data/en_US/item.json');
	foreach($masteries['data'] as $item => $data)
	{
		onlol::setlog('cron_images', 'Downloaded item image '.$data['image']['full']);
		onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/item/'.$data['image']['full'], ROOTPATH.'/game/items/'.$data['image']['full']);
	}
	/* Map images */
	$maps = array(1,10,11,12);
	foreach($maps as $nothing => $map)
	{
		onlol::setlog('cron_images', 'Downloaded item image '.$map);
		onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$last_version.'/img/map/map'.$map.'.png', ROOTPATH.'/game/maps/'.$map.'.png');
	}
}