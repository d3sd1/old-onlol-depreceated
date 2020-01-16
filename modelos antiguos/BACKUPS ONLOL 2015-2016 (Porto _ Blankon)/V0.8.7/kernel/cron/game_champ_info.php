<?php
require('../class/constants.php');
require('../class/database.php');
require('../class/lol.php');
require('../class/onlol.php');
require('../class/riot.php');
$last_version = onlol::readjson('https://ddragon.leagueoflegends.com/api/versions.json')[0];
if(onlol::config('lol_patch_champinfo') != $last_version)
{
	$db->query('UPDATE config SET value="'.$last_version.'" WHERE name="lol_patch_champinfo"') or die($db->error);
	$builds_executed = false;
	foreach($langs as $lang_key => $lang_name)
	{
		switch($lang_key)
		{
			case 'es':
			$locale = 'es_ES';
			break;
			case 'en':
			$locale = 'en_US';
			break;
			case 'de':
			$locale = 'de_DE';
			break;
			case 'fr':
			$locale = 'fr_FR';
			break;
			case 'ja':
			$locale = 'ja_JP';
			break;
			default: $locale = 'es_ES';
		}
		onlol::setlog('cron_champs', 'Champion data updated ['.$lang_key.']');
		$champions = onlol::readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion?champData=all&locale='.$locale.'&api_key='.LOL_API_KEY);
		foreach($champions['data'] as $keyname => $data)
		{
			if($db->query('SELECT id FROM lol_champs WHERE champ_id='.$data['id'].' AND lang="'.$lang_key.'"')->num_rows > 0)
			{
				$db->query('UPDATE lol_champs SET champ_name="'.$data['name'].'",champ_key="'.$data['key'].'",champ_title="'.$data['title'].'",skins_count='.(count($data['skins'])-1).',champ_lore="'.addslashes($data['lore']).'",champ_blurb="'.addslashes($data['blurb']).'",allytips="'.addslashes(json_encode($data['allytips'])).'",enemytips="'.addslashes(json_encode($data['enemytips'])).'",roles="'.addslashes(json_encode($data['tags'])).'",kit_bar="'.$data['partype'].'",info="'.addslashes(json_encode($data['info'])).'",stats="'.addslashes(json_encode($data['stats'])).'" WHERE champ_id='.$data['id'].' AND lang="'.$lang_key.'"') or die($db->error);
			}
			else
			{
				$db->query('INSERT INTO lol_champs (champ_name,champ_id,lang,champ_key,champ_title,skins_count,champ_lore,champ_blurb,allytips,enemytips,roles,kit_bar,info,stats) VALUES ("'.$data['name'].'",'.$data['id'].',"'.$lang_key.'","'.$data['key'].'","'.$data['title'].'",'.(count($data['skins'])-1).',"'.addslashes($data['lore']).'","'.addslashes($data['blurb']).'","'.addslashes(json_encode($data['allytips'])).'","'.addslashes(json_encode($data['enemytips'])).'","'.addslashes(json_encode($data['tags'])).'","'.$data['partype'].'","'.addslashes(json_encode($data['info'])).'","'.addslashes(json_encode($data['stats'])).'")') or die($db->error);
			}
			foreach($data['skins'] as $count => $skin_data)
			{
				if($db->query('SELECT id FROM lol_champs_skins WHERE champ_id='.$data['id'].' AND lang="'.$lang_key.'"')->num_rows > 0)
				{
					$db->query('UPDATE lol_champs_skins SET skin_name="'.$skin_data['name'].'",skin_num="'.$skin_data['num'].'",skin_id="'.$skin_data['id'].'" WHERE champ_id='.$data['id'].' AND lang="'.$lang_key.'"') or die($db->error);
				}
				else
				{
					$db->query('INSERT INTO lol_champs_skins (skin_name,skin_num,lang,skin_id,champ_id) VALUES ("'.$skin_data['name'].'","'.$skin_data['num'].'","'.$lang_key.'","'.$skin_data['id'].'",'.$data['id'].')') or die($db->error);
				}
			}
		}
		if($builds_executed == false)
		{
			/* Non - lang data */
			foreach($champions['data'] as $keyname => $data)
			{
				onlol::setlog('cron_champs', 'Champion build updated: '.$data['id']);
				if($db->query('SELECT id FROM lol_champs_kit WHERE champ_id='.$data['id'].' AND lang="'.$lang_key.'"')->num_rows > 0)
				{
					$db->query('UPDATE lol_champs_kit SET p="'.addslashes(json_encode($data['passive'])).'",q="'.addslashes(json_encode($data['spells'][0])).'",w="'.addslashes(json_encode($data['spells'][1])).'",e="'.addslashes(json_encode($data['spells'][2])).'",r="'.addslashes(json_encode($data['spells'][3])).'" WHERE champ_id='.$data['id'].' AND lang="'.$lang_key.'"') or die($db->error);
				}
				else
				{
					$db->query('INSERT INTO lol_champs_kit (champ_id,p,q,w,e,r,lang) VALUES ('.$data['id'].',"'.addslashes(json_encode($data['passive'])).'","'.addslashes(json_encode($data['spells'][0])).'","'.addslashes(json_encode($data['spells'][1])).'","'.addslashes(json_encode($data['spells'][2])).'","'.addslashes(json_encode($data['spells'][3])).'","'.$lang_key.'")') or die($db->error);
				}
				foreach($data['recommended'] as $map_data)
				{
					switch($map_data['map'])
					{
						case 'TT':
						$map_id = 10;
						break;
						case 'SR':
						$map_id = 11;
						break;
						case 'HA':
						$map_id = 12;
						break;
						default: $map_id = 11;
					}
					if($db->query('SELECT id FROM lol_champs_builds WHERE champ_id='.$data['id'].'')->num_rows > 0)
					{
						$db->query('UPDATE lol_champs_builds SET map='.$map_id.',mode="'.$map_data['mode'].'",builds="'.addslashes(json_encode($map_data['blocks'])).'" WHERE champ_id='.$data['id'].'') or die($db->error);
					}
					else
					{
							$db->query('INSERT INTO lol_champs_builds (champ_id,map,mode,builds) VALUES ('.$data['id'].','.$map_id.',"'.$map_data['mode'].'","'.addslashes(json_encode($map_data['blocks'])).'")') or die($db->error);
					}
				}
				$builds_executed = true;
			}
		}
	}
}