<?php
require('../class/constants.php');
require('../class/database.php');
require('../class/lol.php');
require('../class/onlol.php');
require('../class/riot.php');
$last_version = onlol::readjson('https://ddragon.leagueoflegends.com/api/versions.json')[0];
if(onlol::config('lol_patch_summonerinfo') != $last_version)
{
	$item_groups_done_status = false;
	$maps_done_status = false;
	$db->query('UPDATE config SET value="'.$last_version.'" WHERE name="lol_patch_summonerinfo"') or die($db->error);
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
		onlol::setlog('cron_summoners', 'Summoner data updated ['.$lang_key.']');
		/* Summoner spells data */
		$summonerspells = onlol::readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/summoner-spell?locale='.$locale.'&spellData=all&api_key='.LOL_API_KEY);
		foreach($summonerspells['data'] as $keyname => $data)
		{
			if($db->query('SELECT id FROM lol_summonerspells WHERE spell_id='.$data['id'].' AND lang="'.$lang_key.'"')->num_rows > 0)
			{
				$db->query('UPDATE lol_summonerspells SET spell_key="'.$data['key'].'",spell_name="'.$data['name'].'",spell_description="'.$data['description'].'",minimum_level="'.$data['summonerLevel'].'",spell_range="'.$data['rangeBurn'].'",modes="'.addslashes(json_encode($data['modes'])).'" WHERE spell_id='.$data['id'].' AND lang="'.$lang_key.'"') or die($db->error);
			}
			else
			{
				$db->query('INSERT INTO lol_summonerspells (spell_key,spell_name,spell_description,minimum_level,spell_range,modes,spell_id,lang) VALUES ("'.$data['key'].'","'.$data['name'].'","'.$data['description'].'","'.$data['summonerLevel'].'","'.$data['rangeBurn'].'","'.addslashes(json_encode($data['modes'])).'",'.$data['id'].',"'.$lang_key.'")') or die($db->error);
			}
		}
		/* Item data */
		$items = onlol::readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/item?locale='.$locale.'&itemListData=all&api_key='.LOL_API_KEY);
		foreach($items['data'] as $item_id => $data)
		{
			if($db->query('SELECT id FROM lol_items WHERE item_id='.$data['id'].' AND lang="'.$lang_key.'"')->num_rows > 0)
			{
				$db->query('UPDATE lol_items SET item_id='.$data['id'].',lang="'.$lang_key.'",name="'.$data['name'].'",item_group="'.$data['group'].'",description="'.$data['description'].'",short_description="'.$data['plaintext'].'",builds_into="'.addslashes(json_encode($data['into'])).'",tags="'.addslashes(json_encode($data['tags'])).'",maps="'.addslashes(json_encode($data['maps'])).'",stats="'.addslashes(json_encode($data['stats'])).'",gold="'.addslashes(json_encode($data['gold'])).'",effect="'.addslashes(json_encode($data['effect'])).'" WHERE item_id='.$data['id'].' AND lang="'.$lang_key.'"') or die($db->error);
			}
			else
			{
				$db->query('INSERT INTO lol_items (item_id,lang,name,item_group,description,short_description,builds_into,tags,maps,stats,gold,effect) VALUES ("'.$data['key'].'","'.$lang_key.'","'.$data['name'].'","'.$data['group'].'","'.$data['description'].'","'.$data['plaintext'].'","'.addslashes(json_encode($data['into'])).'","'.addslashes(json_encode($data['tags'])).'","'.addslashes(json_encode($data['maps'])).'","'.addslashes(json_encode($data['stats'])).'","'.addslashes(json_encode($data['gold'])).'","'.addslashes(json_encode($data['effect'])).'")') or die($db->error);
			}
		}
		if($item_groups_done_status == false)
		{
			foreach($items['groups'] as $group_data)
			{
				if($db->query('SELECT id FROM lol_items_groups WHERE group_key="'.$group_data['key'].'"')->num_rows > 0)
				{
						$db->query('UPDATE lol_items_groups SET group_key="'.$group_data['key'].'",max_group_items="'.$group_data['MaxGroupOwnable'].'" WHERE group_key="'.$group_data['key'].'"') or die($db->error);
				}
				else
				{
					$db->query('INSERT INTO lol_items_groups (group_key,max_group_items) VALUES ("'.$group_data['key'].'","'.$group_data['MaxGroupOwnable'].'")') or die($db->error);
				}
			}
			$item_groups_done_status = true;
		}
		/* Map data */
		if($maps_done_status == false)
		{
			$maps = onlol::readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/map?&api_key='.LOL_API_KEY);
			foreach($maps['data'] as $map_id => $data)
			{
				if($db->query('SELECT id FROM lol_maps WHERE map_id='.$data['mapId'])->num_rows > 0)
				{
					$db->query('UPDATE lol_maps SET map_name="'.$data['mapName'].'" WHERE map_id='.$data['mapId']) or die($db->error);
				}
				else
				{
					$db->query('INSERT INTO lol_maps (map_id,map_name) VALUES ('.$data['mapId'].',"'.$data['mapName'].'")') or die($db->error);
				}
			}
			$maps_done_status = true;
		}
		/* Translation data */
		$riot_lang_trans = onlol::readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/language-strings?locale='.$locale.'&api_key='.LOL_API_KEY);
		foreach($riot_lang_trans['data'] as $key => $value)
		{
			if($db->query('SELECT id FROM lol_trans WHERE trans_key="'.$key.'" AND lang="'.$lang_key.'"')->num_rows > 0)
			{
				$db->query('UPDATE lol_trans SET trans_value="'.$value.'" WHERE trans_key="'.$key.'" AND lang="'.$lang_key.'"') or die($db->error);
			}
			else
			{
				$db->query('INSERT INTO lol_trans (trans_key,trans_value,lang) VALUES ("'.$key.'","'.$value.'","'.$lang_key.'")') or die($db->error);
			}
		}
		/* Masteries data */
		$masteries = onlol::readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/mastery?locale='.$locale.'&masteryListData=all&api_key='.LOL_API_KEY);
		foreach($masteries['data'] as $data)
		{
			if($db->query('SELECT id FROM lol_masteries WHERE mastery_id="'.$data['id'].'" AND lang="'.$lang_key.'"')->num_rows > 0)
			{
				$db->query('UPDATE lol_masteries SET name="'.$data['name'].'",description="'.addslashes(json_encode($data['description'])).'",ranks='.$data['ranks'].',tree="'.$data['masteryTree'].'" WHERE mastery_id="'.$data['id'].'" AND lang="'.$lang_key.'"') or die($db->error);
			}
			else
			{
				$db->query('INSERT INTO lol_masteries (mastery_id,name,lang,description,ranks,tree) VALUES ("'.$data['id'].'","'.$data['name'].'","'.$lang_key.'","'.addslashes(json_encode($data['description'])).'",'.$data['ranks'].',"'.$data['masteryTree'].'")') or die($db->error);
			}
		}
		/* Runes data */
		$runes = onlol::readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/rune?locale='.$locale.'&runeListData=all&api_key='.LOL_API_KEY);
		foreach($runes['data'] as $data)
		{
			if($db->query('SELECT id FROM lol_runes WHERE rune_id="'.$data['id'].'" AND lang="'.$lang_key.'"')->num_rows > 0)
			{
				$db->query('UPDATE lol_runes SET name="'.$data['name'].'",description="'.$data['description'].'",tags="'.addslashes(json_encode($data['tags'])).'",stats="'.addslashes(json_encode($data['stats'])).'",tier="'.$data['rune']['tier'].'",type="'.$data['rune']['type'].'",is_enabled="'.$data['rune']['isRune'].'" WHERE rune_id="'.$data['id'].'" AND lang="'.$lang_key.'"') or die($db->error);
			}
			else
			{
				$db->query('INSERT INTO lol_runes (rune_id,name,description,tags,stats,tier,type,is_enabled,lang) VALUES ("'.$data['id'].'","'.$data['name'].'","'.$data['description'].'","'.addslashes(json_encode($data['tags'])).'","'.addslashes(json_encode($data['stats'])).'","'.$data['rune']['tier'].'","'.$data['rune']['type'].'","'.$data['rune']['isRune'].'","'.$lang_key.'")') or die($db->error);
			}
		}
	}
}