<?php
$update_e = true;
/* Incluir los filtros de seguridad */
require('security.php');
/* Conectar a la base de datos */
require('database.php');
/* Incluir las clases */
require('class.php');
header('Content-Type: text/html; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>';
/* Fix the fkin mysqli */
ini_set('mysqli.reconnect', '1');

set_time_limit(0);
header('Content-Type: text/html; charset=utf-8');
/* Check if next version is avaliable */
$version = readjson('https://ddragon.leagueoflegends.com/api/versions.json')[0]; //Getting last version
if(config('lol_patch') != $version)
{
	$isthisanewpatch = true;
	setconfig('lol_patch', $version);
}
else
{
	$version = config('lol_patch');
	$isthisanewpatch = false;
}
setconfig('lol_patch_time', time());
$cron = new cron();
$cron->exec_onlol_cron();

class cron
{
	private $lol_version;
	private $isthisanewpatch;

	public function __construct() {
		$this->lol_version=$GLOBALS['version'];
		$this->isthisanewpatch=$GLOBALS['isthisanewpatch'];
	}
	public function setlasttime($cron_name)
	{
		global $db; 
		$db->query('UPDATE config_cron_timing SET last_time="'.time().'" WHERE task="'.$cron_name.'" LIMIT 1'); 
	}
	
	public function timer($cron_name)
	{
		global $db; 
		/* Fix mysql server down */
		if(!$db->ping())
		{
			global $db_host;
			global $db_user;
			global $db_pass;
			global $db_base;
			
			$this->logger('CRON DATABASE CRITICAL ERROR','MYSQLI DOWN -> Reconnectring');
			if (!$db->real_connect($db_host, $db_user, $db_pass, $db_base))
			{
                 die($this->logger('CRON DATABASE CRITICAL ERROR','MYSQLI DOWN -> Couldnt reconnect'.$db->error));
            }               
            else
            {
               $this->logger('CRON DATABASE CRITICAL ERROR','RECONNECTED');
            }
		}
		
		if($db->query('SELECT id FROM config_cron_timing WHERE task="'.$cron_name.'" LIMIT 1')->num_rows == 0)
		{
			$this->logger('CRON DATABASE CRITICAL ERROR', trigger_error($db->error).' No se ha ejecutado la consulta correctamente.<br>'.$cron_name.'<br>SELECT only_on_new_patch,seconds_interval,last_time FROM config_cron_timing WHERE task="'.$cron_name.'"');
		}
		$ret = $db->query('SELECT * FROM config_cron_timing WHERE task="'.$cron_name.'"')->fetch_array() or trigger_error($db->error); 

		if($ret['only_on_new_patch'] == '1')
		{
			if($this->isthisanewpatch == true)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if(($ret['seconds_interval']+$ret['last_time']) < time())
			{
				$this->setlasttime($cron_name);
				return true;	
			}
			else
			{
				return false;
			}
		}
	}
	public function exec_onlol_cron()
	{
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		/* Put executables crons her
		$this->wipe_images_summoner();
		$this->wipe_images_game();
		$this->wipe_images_champions();
		
		$this->wipe_info_champions();
		$this->wipe_info_challengers();
		$this->wipe_info_game();
		
		$this->wipe_stats();
		$this->wipe_stats_mmr(); 
		$this->bestplayers_champions();
		
		$this->update_actual_summoners(); */
		$this->wipe_info_champions();
		$this->clean();
	}
	public function clean()
	{
		global $db; 
		$db->query('UPDATE config SET value="" WHERE name="updating" LIMIT 1'); 
	}
	public function logger($cat,$str)
	{
		echo '['.date('H:i:s').'] ['.$cat.'] '.$str.'<br>';
		
		$arch = fopen('logs/cron.log', 'a+'); 

		fwrite($arch, '['.date('H:i:s').'] ['.$cat.'] '.$str.'\r\n');
		fclose($arch);
	}
	public function coding()
	{
		global $db;
		$db->query('UPDATE config_cron_timing SET last_time=0');
		$db->query('UPDATE config SET value=0 WHERE name="lol_patch"');
		$db->query('UPDATE config SET value=0 WHERE name="lol_patch_time"');
	}
	
	public function wipe_images_summoner()
	{
		if($this->timer('invimages') == TRUE)
		{
			/* Summoner icon images */
			$icon_num = 0;
			$finished = false;
			$margin_error = 50; //Defines how many broken links has to be to end the execution
			while($finished == false)
			{
				$max_icon = readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/realm?api_key='.LOL_API_KEY)['profileiconmax'];
				if($icon_num <= $max_icon)
				{
					onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/profileicon/'.$icon_num.'.png',ROOTPATH.'/style/images/base/summoners/icon/'.$icon_num.'.png');
					$this->logger('images_summoner/icons', 'Copiado icono con ID '.$icon_num.'/'.$max_icon.' de la versión '.$this->lol_version);
					$isabaseicon = true;
				}
				else
				{
					if($icon_num < 501)
					{
						$icon_num = 501;
					}
						$handle = curl_init('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/profileicon/'.$icon_num.'.png');
						curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($handle);
						$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
						if($httpCode == 404)
						{
							if(empty($last_icons_errors))
							{
								$last_icons_errors = 1;
							}
							else
							{
								$last_icons_errors++;
							}
							if($last_icons_errors > $margin_error)
							{
								$finished = true;
							}
						}
						else
						{
							unset($last_icons_errors);
							if(!file_exists(ROOTPATH.'/style/images/base/summoners/icon/'.$icon_num.'.png')) //The +500 ID icons doesn't update so let's not replace it.
							{
								onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/profileicon/'.$icon_num.'.png',ROOTPATH.'/style/images/base/summoners/icon/'.$icon_num.'.png');
								$this->logger('images_summoner/icons', 'Copiado icono con ID '.$icon_num.' de la versión '.$this->lol_version);
							}
						}
				}
					$icon_num++;
					@curl_close($handle);
			}
			/* Summoner spell images */
			$spell_data = readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/summoner-spell?api_key='.LOL_API_KEY);
			$done_spells = 0;
			
			while($done_spells < count($spell_data['data']))
			{
				$spell_info = array_slice($spell_data['data'], $done_spells,1);
				$spell_name = key($spell_info);
				$spell_id = $spell_info[$spell_name]['id'];
				
				onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$spell_name.'.png',ROOTPATH.'/style/images/base/summoners/spells/'.$spell_id.'.png');

				$done_spells++;
				$this->logger('images_summoner/spells', 'Descargando imagen: ID -> '.$spell_id.' Nombre -> '.$spell_name);
			}
		}
		else
		{
			$this->logger('images_summoner', 'No ejecutado.');
		}
	}
	
	public function wipe_images_champions()
	{
		if($this->timer('champimages') == TRUE)
		{
			/* Champion kit images */
			$champ_json = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/champion.json');
			$kit_done_champs = 0;
				
			while($kit_done_champs < count($champ_json['data']))
			{
				$champ_info = array_slice($champ_json['data'], $kit_done_champs,1);
				$champ_id = $champ_info[key($champ_info)]['key'];
				$champ_keyname = $champ_info[key($champ_info)]['id'];
				
				$this->logger('images_champions/kit', 'Descargando imágenes del kit de '.$champ_info[key($champ_info)]['name']);
				
				$are_kitarts_avaliable = true;
				$skin_num = 0;
				$kit_img = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/champion/'.$champ_info[key($champ_info)]['id'].'.json');
				// Q
						
						$q_spell = curl_init('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_keyname]['spells'][0]['image']['full'].'');
						curl_setopt($q_spell,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($q_spell);
						$httpCode = curl_getinfo($q_spell, CURLINFO_HTTP_CODE);
						if($httpCode == 404) {
							$are_kitarts_avaliable = false;
						}
						else
						{
								if(!file_exists(ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/'))
								{
									 mkdir(ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/', 0777, true);
									 $this->logger('images_champions/kit', 'Creando directorio '.ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/');
								}
								
								onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_keyname]['spells'][0]['image']['full'],ROOTPATH.'/style/images/base/champions/kit/'.$champ_info[key($champ_info)]['id'].'/q.png');
								
						}
						@curl_close($q_spell);
				// W
						
						$w_spell = curl_init('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_keyname]['spells'][1]['image']['full'].'');
						curl_setopt($w_spell,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($w_spell);
						$httpCode = curl_getinfo($w_spell, CURLINFO_HTTP_CODE);
						if($httpCode == 404) {
							$are_kitarts_avaliable = false;
						}
						else
						{
								if(!file_exists(ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/'))
								{
									 mkdir(ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/', 0777, true);
								}
								onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_keyname]['spells'][1]['image']['full'].'',ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/w.png');
						}
					
						@curl_close($w_spell);
				// E
						
						$e_spell = curl_init('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_keyname]['spells'][2]['image']['full'].'');
						curl_setopt($e_spell,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($e_spell);
						$httpCode = curl_getinfo($e_spell, CURLINFO_HTTP_CODE);
						if($httpCode == 404) {
							$are_kitarts_avaliable = false;
						}
						else
						{
								if(!file_exists(ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/'))
								{
									 mkdir(ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/', 0777, true);
								}
								onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_keyname]['spells'][2]['image']['full'].'',ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/e.png');						}
						@curl_close($e_spell);
				// R
						$r_spell = curl_init('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_keyname]['spells'][3]['image']['full'].'');
						curl_setopt($r_spell,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($r_spell);
						$httpCode = curl_getinfo($r_spell, CURLINFO_HTTP_CODE);
						if($httpCode == 404) {
							$are_kitarts_avaliable = false;
						}
						else
						{
								if(!file_exists(ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/'))
								{
									 mkdir(ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/', 0777, true);
								}
								onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_keyname]['spells'][3]['image']['full'].'',ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/r.png');
						}
						@curl_close($r_spell);
				// PASSIVE
						$p_spell = curl_init('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/passive/'.$kit_img['data'][$champ_keyname]['passive']['image']['full'].'');
						curl_setopt($p_spell,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($p_spell);
						$httpCode = curl_getinfo($p_spell, CURLINFO_HTTP_CODE);
						if($httpCode == 404) {
							$are_kitarts_avaliable = false;
						}
						else
						{
								if(!file_exists(ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/'))
								{
									 mkdir(ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/', 0777, true);
								}
								onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/passive/'.$kit_img['data'][$champ_keyname]['passive']['image']['full'].'',ROOTPATH.'/style/images/base/champions/kit/'.$champ_keyname.'/passive.png');
						}
						@curl_close($p_spell);
				$kit_done_champs++;
			}
			/* Champion base image */
			$little_done_champs = 0;
			while($little_done_champs < count($champ_json['data']))
			{
				/* Base de datos de los campeones */
				$champ_info = array_slice($champ_json['data'], $little_done_champs,1);
				$champ_id = $champ_info[key($champ_info)]['key'];
				$champ_keyname = $champ_info[key($champ_info)]['id'];
				
				if(!file_exists(ROOTPATH.'/style/images/base/champions/little/'))
				{
					mkdir(ROOTPATH.'/style/images/base/champions/little/', 0777, true);
				}
				onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/champion/'.$champ_keyname.'.png',ROOTPATH.'/style/images/base/champions/little/'.$champ_keyname.'.png');
				$this->logger('images_champions/square', 'Descargada imagen base de '.$champ_info[key($champ_info)]['name'].'');
				$little_done_champs++;
			}
			/* Champion loading image */
			$loading_done_champs = 0;
			while($loading_done_champs < count($champ_json['data']))
			{
				/* Base de datos de los campeones */
				$champ_info = array_slice($champ_json['data'], $loading_done_champs,1);
				$champ_id = $champ_info[key($champ_info)]['key'];
				$champ_keyname = $champ_info[key($champ_info)]['id'];
				
				if(!file_exists(ROOTPATH.'/style/images/base/champions/loading/'))
				{
					mkdir(ROOTPATH.'/style/images/base/champions/loading/', 0777, true);
				}
				$are_loadingarts_avaliable = true;
				$skin_num = 0;
				while($are_loadingarts_avaliable == true)
				{
						$handle = curl_init('http://ddragon.leagueoflegends.com/cdn/img/champion/loading/'.$champ_keyname.'_'.$skin_num.'.jpg');
						curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($handle);
						$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
						if($httpCode == 404) {
							$are_loadingarts_avaliable = false;
						}
						else
						{
							onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/img/champion/loading/'.$champ_keyname.'_'.$skin_num.'.jpg',ROOTPATH.'/style/images/base/champions/loading/'.$champ_keyname.'_'.$skin_num.'.jpg');
							$this->logger('images_champions/loading', 'Descargada imagen loading de '.$champ_info[key($champ_info)]['name'].' para el aspecto numero '.$skin_num);
						}
				$skin_num++;
						@curl_close($handle);
				}
				$loading_done_champs++;
			}
			/* Champion splash art images */
			$splash_done_champs = 0;
				
			while($splash_done_champs < count($champ_json['data']))
			{
				$champ_info = array_slice($champ_json['data'], $splash_done_champs,1);
				$champ_id = $champ_info[key($champ_info)]['key'];
				$champ_keyname = $champ_info[key($champ_info)]['id'];
				
				if(!file_exists(ROOTPATH.'/style/images/base/champions/splash/'))
				{
					mkdir(ROOTPATH.'/style/images/base/champions/splash/', 0777, true);
				}
				$are_splasharts_avaliable = true;
				$skin_num = 0;
				while($are_splasharts_avaliable == true)
				{
							$handle = curl_init('http://ddragon.leagueoflegends.com/cdn/img/champion/splash/'.$champ_keyname.'_'.$skin_num.'.jpg');
							curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
							$response = curl_exec($handle);
							$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
							if($httpCode == 404) {
								$are_splasharts_avaliable = false;
							}
							else
							{
								onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/img/champion/splash/'.$champ_keyname.'_'.$skin_num.'.jpg',ROOTPATH.'/style/images/base/champions/splash/'.$champ_keyname.'_'.$skin_num.'.jpg');
								$this->logger('images_champions/splash', 'Descargado splash art de '.$champ_keyname.' para la skin '.$skin_num);
							}
				$skin_num++;
						@curl_close($handle); //Si la sesión no estaba iniciada daba error, así que sencillamente los tapamos ya que no son errores críticos
				}
				$splash_done_champs++;
			}
		}
		else
		{
			$this->logger('images_champions', 'No ejecutado.');
		}
	}
	
	public function wipe_images_game()
	{
		if($this->timer('gameimages') == TRUE)
		{
			/* Game item images */
			$item_data = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/item.json');
			$done_item = 0;
			
			if(!file_exists(ROOTPATH.'/style/images/base/game/items/'))
			{
				mkdir(ROOTPATH.'/style/images/base/game/items/', 0777, true);
			}
			while($done_item < count($item_data['data']))
			{
				$item_id = array_slice($item_data['data'],$done_item,1);
				$itemimage = $item_id['0']['image']['full'];
				onlol::imgcompress('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/item/'.$itemimage.'',ROOTPATH.'/style/images/base/game/items/'.$itemimage.'');

				$done_item++;
				$this->logger('images_game/items', 'Descargando imagen del item: ID -> '.$itemimage);
			}
			/* Rune images */
			$runes_data = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/rune.json');
			
			foreach($runes_data['data'] as $runeid => $runedata)
			{
				copy('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/rune/'.$runedata['image']['full'],ROOTPATH.'/style/images/base/summoners/runes/'.$runeid.'.png');
				$this->logger('images_summoner/runes', 'Descargando runa: ID -> '.$runeid.' Nombre -> '.$runedata['name']);
			}
			/* Masteries images */
			$masteries_data = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/mastery.json');
			
			foreach($masteries_data['data'] as $masteryid => $masterydata)
			{
				copy('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/mastery/'.$masterydata['image']['full'],ROOTPATH.'/style/images/base/summoners/mastery/'.$masteryid.'.png');
				$this->logger('images_summoner/masteries', 'Descargando maestría: ID -> '.$masteryid.' Nombre -> '.$masterydata['name']);
			}
		}
		else
		{
			$this->logger('images_game', 'No ejecutado.');
		}
	}
	
	public function wipe_info_champions()
	{
		if($this->timer('champinfo') == TRUE)
		{
			global $db;
			$champ_data = readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion?champData=all&locale=es_ES&api_key='.LOL_API_KEY);
			/* Champion info */
			updating('CHAMPION_STATS');
			$db->query('TRUNCATE TABLE lol_champs');
			$done_champs = 0;
				
			while($done_champs < count($champ_data['data']))
			{
				$champ_info = array_slice($champ_data['data'], $done_champs,1);
				$champ_key = key($champ_info);
				$champ_name = $champ_info[key($champ_info)]['name'];
				
				$this->logger('info_champions_stats', 'Agregando información base de '.$champ_name);
				if(!empty($champ_info[$champ_key]['tags']['1'])) { $secondbar_c = 'role_2,'; $secondbar_v = ',"'.$champ_info[$champ_key]['tags']['1'].'"'; } else { $secondbar_c = null; $secondbar_v = null; }
				
				/* Lore */
				$lore = $champ_info[$champ_key]['lore']; //AGREGAR AQUI MULTIIDIOMA ;)
				
				$tips = array();
				if(is_array($champ_info[$champ_key]['allytips']))
				{
					$tips['ally'] = array();
					
					$ally_tips_amount = 0;
					foreach($champ_info[$champ_key]['allytips'] as $text)
					{
						$tips['ally'][$ally_tips_amount] = $text;
						$ally_tips_amount++;
					}
				}
				if(is_array($champ_info[$champ_key]['enemytips']))
				{
					$tips['enemy'] = array();
					
					$enemy_tips_amount = 0;
					foreach($champ_info[$champ_key]['enemytips'] as $text)
					{
						$tips['enemy'][$enemy_tips_amount] = $text;
						$enemy_tips_amount++;
					}
				}
				$db->query('INSERT INTO lol_champs (champ_id,champ_keyname,champname,title,info_attack,info_defense,info_magic,info_difficulty,role_1,'.$secondbar_c.'kit_bar,base_hp,scale_hp_lvl,base_bar,scale_bar_lvl,movspeed,base_armor,scale_armor_lvl,base_spellblock,scale_spellblock_lvl,attackrange,base_hpregen,scale_hpregen_lvl,base_manareg,scale_manareg_lvl,base_crit,scale_crit_lvl,base_ad,scale_ad_lvl,offset_as,scale_as_lvl,lore,tips) VALUES ("'.$champ_info[$champ_key]['id'].'","'.$champ_key.'","'.$champ_name.'","'.$champ_info[$champ_key]['title'].'","'.$champ_info[$champ_key]['info']['attack'].'","'.$champ_info[$champ_key]['info']['defense'].'","'.$champ_info[$champ_key]['info']['magic'].'","'.$champ_info[$champ_key]['info']['difficulty'].'","'.strtolower($champ_info[$champ_key]['tags']['0']).'"'.strtolower($secondbar_v).',"'.strtolower($champ_info[$champ_key]['partype']).'","'.$champ_info[$champ_key]['stats']['hp'].'","'.$champ_info[$champ_key]['stats']['hpperlevel'].'","'.$champ_info[$champ_key]['stats']['mp'].'","'.$champ_info[$champ_key]['stats']['mpperlevel'].'","'.$champ_info[$champ_key]['stats']['movespeed'].'","'.$champ_info[$champ_key]['stats']['armor'].'","'.$champ_info[$champ_key]['stats']['armorperlevel'].'","'.$champ_info[$champ_key]['stats']['spellblock'].'","'.$champ_info[$champ_key]['stats']['spellblockperlevel'].'","'.$champ_info[$champ_key]['stats']['attackrange'].'","'.$champ_info[$champ_key]['stats']['hpregen'].'","'.$champ_info[$champ_key]['stats']['hpregenperlevel'].'","'.$champ_info[$champ_key]['stats']['mpregen'].'","'.$champ_info[$champ_key]['stats']['mpregenperlevel'].'","'.$champ_info[$champ_key]['stats']['crit'].'","'.$champ_info[$champ_key]['stats']['critperlevel'].'","'.$champ_info[$champ_key]['stats']['attackdamage'].'","'.$champ_info[$champ_key]['stats']['attackdamageperlevel'].'","'.$champ_info[$champ_key]['stats']['attackspeedoffset'].'","'.$champ_info[$champ_key]['stats']['attackspeedperlevel'].'","'.$lore.'","'.addslashes(json_encode($tips)).'")') or die($db->error); //Si el valor es dintinto, actualizar información.
				$done_champs++;
			}
			not_updating('CHAMPION_STATS');
			
			/* Champion skin info */
			updating('CHAMPION_SKINS');
			$done_champs = 0;
				
			while($done_champs < count($champ_data['data']))
			{
				$champ_info = array_slice($champ_data['data'], $done_champs,1);
				$champ_key = key($champ_info);
				$champ_name = $champ_info[key($champ_info)]['name'];
				
				$are_skins_avaliable = true;
				$skin_count = 0; //0 Defines no skin
				$champ_data_skin = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/champion/'.$champ_key.'.json');
				
				while($are_skins_avaliable == true)
				{
					if(isset($champ_data_skin['data'][$champ_key]['skins'][$skin_count]))
					{
						if($skin_count == 0)
						{
							/* Chroma fixer */
							if($champ_data_skin['data'][$champ_key]['skins'][$skin_count]['chromas'] == null){
								$haschroma = 'false';
							}
							else
							{
								$haschroma = $champ_data_skin['data'][$champ_key]['skins'][$skin_count]['chromas'];
							}
							
							if($db->query('SELECT id FROM lol_skins WHERE champname="'.$champ_key.'" AND skin_num="'.$skin_count.'"')->num_rows == 0)
							{
								$db->query('INSERT INTO lol_skins (champname,skin_num,skin_name,has_chroma) VALUES ("'.$champ_key.'","'.$champ_data_skin['data'][$champ_key]['skins'][$skin_count]['num'].'","BASE", "'.$haschroma.'")') or die($db->error);
							}
							else
							{
								$db->query('UPDATE lol_skins SET has_chroma="'.$haschroma.'" WHERE champname="'.$champ_key.'" AND skin_num="'.$skin_count.'"');
							}
							$this->logger('info_champions/skins', 'Agregada la skin base de '.$champ_key.'. Chroma: '.$haschroma);
						}
						else
						{
							/* Chroma fixer */
							if($champ_data_skin['data'][$champ_key]['skins'][$skin_count]['chromas'] == null){
								$haschroma = 'false';
							}
							else
							{
								$haschroma = $champ_data_skin['data'][$champ_key]['skins'][$skin_count]['chromas'];
							}
							if($db->query('SELECT id FROM lol_skins WHERE champname="'.$champ_key.'" AND skin_num="'.$skin_count.'"')->num_rows == 0)
							{
								$db->query('INSERT INTO lol_skins (champname,skin_num,skin_name,has_chroma) VALUES ("'.$champ_key.'","'.$champ_data_skin['data'][$champ_key]['skins'][$skin_count]['num'].'","'.$champ_data_skin['data'][$champ_key]['skins'][$skin_count]['name'].'", "'.$haschroma.'")') or die($db->error);	
							}
							else
							{
								$db->query('UPDATE lol_skins SET skin_name="'.$champ_data_skin['data'][$champ_key]['skins'][$skin_count]['name'].'", has_chroma="'.$haschroma.'" WHERE champname="'.$champ_key.'" AND skin_num="'.$skin_count.'"');
							}
							$this->logger('info_champions/skins', 'Agregada la skin '.$champ_data_skin['data'][$champ_key]['skins'][$skin_count]['name'].' de '.$champ_key.'. Chroma: '.$haschroma);
						}
					}
					else
					{
						$are_skins_avaliable = false;
					}
				$skin_count++;
				}
				$done_champs++;
			}
			not_updating('CHAMPION_SKINS');
			
			/* Champ recommended builds */
			updating('CHAMPION_BUILDS');
			$db->query('TRUNCATE TABLE lol_champs_builds');
			$this_champdata = 1;
			$recommended_jsonfile = readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion?locale=es_ES&champData=recommended&api_key='.LOL_API_KEY);

			while($this_champdata < count($recommended_jsonfile['data']))
			{
				$data_pre = array_slice($recommended_jsonfile['data'],$this_champdata,1);
				$champid = $data_pre[key($data_pre)]['id'];
				$data = $data_pre[key($data_pre)]['recommended'];
				$this->logger('info_champions_build', 'Actualizados los objetos recomendados de '.champidtoname($champid));
				$initial_build = 0;
				$champ_build = array();
				while($initial_build < count($data))
				{
					if($data[$initial_build]['mode'] == 'ARAM')
					{
						$initial_items = 0;
						while($initial_items < count($data[$initial_build]['blocks']))
						{
							$champ_build['12'][$data[$initial_build]['blocks'][$initial_items]['type']] = $data[$initial_build]['blocks'][$initial_items]['type'];
							$type_items = 0;
							while($type_items < count($data[$initial_build]['blocks'][$initial_items]['items']))
							{
								if(!is_array($champ_build['12'][$data[$initial_build]['blocks'][$initial_items]['type']]))
								{
									$champ_build['12'][$data[$initial_build]['blocks'][$initial_items]['type']] = array();
								}	
								
								$champ_build['12'][$data[$initial_build]['blocks'][$initial_items]['type']][$data[$initial_build]['blocks'][$initial_items]['items'][$type_items]['id']]['count'] = $data[$initial_build]['blocks'][$initial_items]['items'][$type_items]['count'];
							$type_items++;
							}
							$initial_items++;
						}
					}
					if($data[$initial_build]['mode'] == 'ASCENSION')
					{
						$initial_items = 0;
						while($initial_items < count($data[$initial_build]['blocks']))
						{
							$champ_build['8'][$data[$initial_build]['blocks'][$initial_items]['type']] = $data[$initial_build]['blocks'][$initial_items]['type'];
							$type_items = 0;
							while($type_items < count($data[$initial_build]['blocks'][$initial_items]['items']))
							{
								if(!is_array($champ_build['8'][$data[$initial_build]['blocks'][$initial_items]['type']]))
								{
									$champ_build['8'][$data[$initial_build]['blocks'][$initial_items]['type']] = array();
								}	
								
								$champ_build['8'][$data[$initial_build]['blocks'][$initial_items]['type']][$data[$initial_build]['blocks'][$initial_items]['items'][$type_items]['id']]['count'] = $data[$initial_build]['blocks'][$initial_items]['items'][$type_items]['count'];
							$type_items++;
							}
							$initial_items++;
						}
					}
					if($data[$initial_build]['mode'] == 'CLASSIC' AND $data[$initial_build]['map'] == 'SR')
					{
						$initial_items = 0;
						while($initial_items < count($data[$initial_build]['blocks']))
						{
							$champ_build['11'][$data[$initial_build]['blocks'][$initial_items]['type']] = $data[$initial_build]['blocks'][$initial_items]['type'];
							$type_items = 0;
							while($type_items < count($data[$initial_build]['blocks'][$initial_items]['items']))
							{
								if(!is_array($champ_build['11'][$data[$initial_build]['blocks'][$initial_items]['type']]))
								{
									$champ_build['11'][$data[$initial_build]['blocks'][$initial_items]['type']] = array();
								}	
								
								$champ_build['11'][$data[$initial_build]['blocks'][$initial_items]['type']][$data[$initial_build]['blocks'][$initial_items]['items'][$type_items]['id']]['count'] = $data[$initial_build]['blocks'][$initial_items]['items'][$type_items]['count'];
							$type_items++;
							}
							$initial_items++;
						}
					}
					if($data[$initial_build]['mode'] == 'CLASSIC' AND $data[$initial_build]['map'] == 'TT')
					{
						$initial_items = 0;
						while($initial_items < count($data[$initial_build]['blocks']))
						{
							$champ_build['10'][$data[$initial_build]['blocks'][$initial_items]['type']] = $data[$initial_build]['blocks'][$initial_items]['type'];
							$type_items = 0;
							while($type_items < count($data[$initial_build]['blocks'][$initial_items]['items']))
							{
								if(!is_array($champ_build['10'][$data[$initial_build]['blocks'][$initial_items]['type']]))
								{
									$champ_build['10'][$data[$initial_build]['blocks'][$initial_items]['type']] = array();
								}	
								
								$champ_build['10'][$data[$initial_build]['blocks'][$initial_items]['type']][$data[$initial_build]['blocks'][$initial_items]['items'][$type_items]['id']]['count'] = $data[$initial_build]['blocks'][$initial_items]['items'][$type_items]['count'];
							$type_items++;
							}
							$initial_items++;
						}
					}
					$initial_build++;
				}
				$db->query('INSERT INTO lol_champs_builds (champ_id,build_items) VALUES ('.$champid.',"'.addslashes(json_encode($champ_build)).'")') or die($db->error);
				$this_champdata++;
			}
			not_updating('CHAMPION_BUILDS');
			/* Champion kit info */
			updating('CHAMPION_KIT');
			$db->query('TRUNCATE TABLE lol_champs_skills');
			$done_champs = 0;
			
			while($done_champs < count($champ_data['data']))
			{
				$champ_info = array_slice($champ_data['data'], $done_champs,1);
				$champ_key = key($champ_info);
				$champ_name = $champ_info[key($champ_info)]['name'];
				
				$this->logger('info_champions_kit', 'Agregando información del kit de '.$champ_name);
				$champ_info = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/champion/'.$champ_key.'.json');
				
				// PASSIVE
					$p_description = $champ_info['data'][$champ_key]['passive']['description'];
					$p_description = str_replace('<span', '<font', $p_description);
					$p_description = str_replace('</span>', '</font>', $p_description);
					$db->query('INSERT INTO lol_champs_skills (passive_name,passive_description,champ_keyname) VALUES ("'.$champ_info['data'][$champ_key]['passive']['name'].'","'.addslashes($p_description).'","'.$champ_key.'")') or die($db->error);
					$this->logger('info_champions_kit/P', 'Pasiva de '.$champ_name.': '.$champ_info['data'][$champ_key]['passive']['name']);
				// Q
					if(isset($champ_info['data'][$champ_key]['spells']['0']['cooldown']))
					{
						$q_cd = ',q_cooldown="'.implode('/',$champ_info['data'][$champ_key]['spells']['0']['cooldown']).'"';
					}
					if(isset($champ_info['data'][$champ_key]['spells']['0']['cost']))
					{
						$q_cost = ',q_cost="'.implode('/',$champ_info['data'][$champ_key]['spells']['0']['cost']).'"';
					}
					
					/* {{ a }} parsing */
					$q_description = $champ_info['data'][$champ_key]['spells']['0']['tooltip'];
					
					foreach($champ_info['data'][$champ_key]['spells']['0']['vars'] as $val)
					{
						if($val['link'] == 'bonusattackdamage')
						{
							$coeff = ($val['coeff']*100).' % AD';
						}
						if($val['link'] == '@dynamic.attackdamage')
						{
							$coeff = ($val['coeff']*100).' % AD';
						}
						if($val['link'] == '@dynamic.spelldamage')
						{
							$coeff = ($val['coeff']*100).' % AP';
						}
						if($val['link'] == 'spelldamage')
						{
							$coeff = ($val['coeff']*100).' % AP';
						}
						if(empty($coeff))
						{
							$coeff = ($val['coeff']*100).' %';
						}
						$q_description = str_replace('{{ '.$val['key'].' }}', $coeff, $q_description);
					}
					/* {{ f }} parsing */
					foreach($champ_info['data'][$champ_key]['spells']['0']['effectBurn'] as $key => $val)
					{
						$q_description = str_replace('{{ f'.($key-1).' }}', $val, $q_description);
					}
					
					/* {{ e }} parsing */
					foreach($champ_info['data'][$champ_key]['spells']['0']['effectBurn'] as $key => $val)
					{
						$q_description = str_replace('{{ e'.$key.' }}', $val, $q_description);
					}
					
					$q_description = str_replace('class=', 'color=', $q_description);
					$q_description = str_replace('<span', '<font', $q_description);
					$q_description = str_replace('</span>', '</font>', $q_description);
					$q_description = str_replace('{{ cost }}', implode('/', $champ_info['data'][$champ_key]['spells']['0']['cost']), $q_description);
					$db->query('UPDATE lol_champs_skills SET q_name="'.$champ_info['data'][$champ_key]['spells']['0']['name'].'", q_description="'.addslashes($q_description).'" '.$q_cd.' '.$q_cost.' WHERE champ_keyname="'.$champ_key.'"') or die($db->error);
					$this->logger('info_champions_kit/Q', 'Q de '.$champ_name.': '.$champ_info['data'][$champ_key]['spells']['0']['name']);
				// W
					if(isset($champ_info['data'][$champ_key]['spells']['1']['cooldown']))
					{
						$w_cd = ',w_cooldown="'.implode('/',$champ_info['data'][$champ_key]['spells']['1']['cooldown']).'"';
					}
					if(isset($champ_info['data'][$champ_key]['spells']['1']['cost']))
					{
						$w_cost = ',w_cost="'.implode('/',$champ_info['data'][$champ_key]['spells']['1']['cost']).'"';
					}
					
					/* {{ a }} parsing */
					$w_description = $champ_info['data'][$champ_key]['spells']['1']['tooltip'];
					
					foreach($champ_info['data'][$champ_key]['spells']['1']['vars'] as $val)
					{
						if($val['link'] == 'bonusattackdamage')
						{
							$coeff = ($val['coeff']*100).' % AD';
						}
						if($val['link'] == 'spelldamage')
						{
							$coeff = ($val['coeff']*100).' % AP';
						}
						if(empty($coeff))
						{
							$coeff = ($val['coeff']*100).' %';
						}
						$w_description = str_replace('{{ '.$val['key'].' }}', $coeff, $w_description);
					}
					/* {{ f }} parsing */
					foreach($champ_info['data'][$champ_key]['spells']['1']['effectBurn'] as $key => $val)
					{
						$w_description = str_replace('{{ f'.($key-1).' }}', $val, $w_description);
					}
					/* {{ e }} parsing */
					foreach($champ_info['data'][$champ_key]['spells']['1']['effectBurn'] as $key => $val)
					{
						$w_description = str_replace('{{ e'.$key.' }}', $val, $w_description);
					}
					$w_description = str_replace('class=', 'color=', $w_description);
					$w_description = str_replace('<span', '<font', $w_description);
					$w_description = str_replace('</span>', '</font>', $w_description);
					$w_description = str_replace('{{ cost }}', implode('/', $champ_info['data'][$champ_key]['spells']['1']['cost']), $w_description);
					$db->query('UPDATE lol_champs_skills SET w_name="'.$champ_info['data'][$champ_key]['spells']['1']['name'].'", w_description="'.addslashes($w_description).'" '.$w_cd.' '.$w_cost.' WHERE champ_keyname="'.$champ_key.'"') or die($db->error);
					$this->logger('info_champions_kit/W', 'W de '.$champ_name.': '.$champ_info['data'][$champ_key]['spells']['1']['name']);
				// E
					
					if(isset($champ_info['data'][$champ_key]['spells']['2']['cooldown']))
					{
						$e_cd = ',e_cooldown="'.implode('/',$champ_info['data'][$champ_key]['spells']['2']['cooldown']).'"';
					}
					if(isset($champ_info['data'][$champ_key]['spells']['2']['cost']))
					{
						$e_cost = ',e_cost="'.implode('/',$champ_info['data'][$champ_key]['spells']['2']['cost']).'"';
					}
					
					/* {{ a }} parsing */
					$e_description = $champ_info['data'][$champ_key]['spells']['2']['tooltip'];
					
					foreach($champ_info['data'][$champ_key]['spells']['2']['vars'] as $val)
					{
						if($val['link'] == 'bonusattackdamage')
						{
							$coeff = ($val['coeff']*100).' % AD';
						}
						if($val['link'] == 'spelldamage')
						{
							$coeff = ($val['coeff']*100).' % AP';
						}
						if(empty($coeff))
						{
							$coeff = ($val['coeff']*100).' %';
						}
						$e_description = str_replace('{{ '.$val['key'].' }}', $coeff, $e_description);
					}
					/* {{ f }} parsing */
					foreach($champ_info['data'][$champ_key]['spells']['2']['effectBurn'] as $key => $val)
					{
						$e_description = str_replace('{{ f'.($key-1).' }}', $val, $e_description);
					}
					/* {{ e }} parsing */
					foreach($champ_info['data'][$champ_key]['spells']['2']['effectBurn'] as $key => $val)
					{
						$e_description = str_replace('{{ e'.$key.' }}', $val, $e_description);
					}
					
					$e_description = str_replace('class=', 'color=', $e_description);
					$e_description = str_replace('<span', '<font', $e_description);
					$e_description = str_replace('</span>', '</font>', $e_description);
					$e_description = str_replace('{{ cost }}', implode('/', $champ_info['data'][$champ_key]['spells']['2']['cost']), $e_description);
					$db->query('UPDATE lol_champs_skills SET e_name="'.$champ_info['data'][$champ_key]['spells']['2']['name'].'", e_description="'.addslashes($e_description).'" '.$e_cd.' '.$e_cost.' WHERE champ_keyname="'.$champ_key.'"') or die($db->error);
					$this->logger('info_champions_kit/E', 'E de '.$champ_name.': '.$champ_info['data'][$champ_key]['spells']['2']['name']);
				// R
					if(isset($champ_info['data'][$champ_key]['spells']['3']['cooldown']))
					{
						$r_cd = ',r_cooldown="'.implode('/',$champ_info['data'][$champ_key]['spells']['3']['cooldown']).'"';
					}
					if(isset($champ_info['data'][$champ_key]['spells']['3']['cost']))
					{
						$r_cost = ',r_cost="'.implode('/',$champ_info['data'][$champ_key]['spells']['3']['cost']).'"';
					}
					
					/* {{ a }} parsing */
					$r_description = $champ_info['data'][$champ_key]['spells']['3']['tooltip'];
					
					foreach($champ_info['data'][$champ_key]['spells']['3']['vars'] as $val)
					{
						if($val['link'] == 'bonusattackdamage')
						{
							$coeff = ($val['coeff']*100).' % AD';
						}
						if($val['link'] == 'spelldamage')
						{
							$coeff = ($val['coeff']*100).' % AP';
						}
						if(empty($coeff))
						{
							$coeff = ($val['coeff']*100).' %';
						}
						$r_description = str_replace('{{ '.$val['key'].' }}', $coeff, $r_description);
					}
					/* {{ f }} parsing */
					foreach($champ_info['data'][$champ_key]['spells']['3']['effectBurn'] as $key => $val)
					{
						$r_description = str_replace('{{ f'.($key-1).' }}', $val, $r_description);
					}
					/* {{ e }} parsing */
					foreach($champ_info['data'][$champ_key]['spells']['3']['effectBurn'] as $key => $val)
					{
						$r_description = str_replace('{{ e'.$key.' }}', $val, $r_description);
					}
					
					$r_description = str_replace('class=', 'color=', $r_description);
					$r_description = str_replace('<span', '<font', $r_description);
					$r_description = str_replace('</span>', '</font>', $r_description);
					$r_description = str_replace('{{ cost }}', implode('/', $champ_info['data'][$champ_key]['spells']['3']['cost']), $r_description);
					$db->query('UPDATE lol_champs_skills SET r_name="'.$champ_info['data'][$champ_key]['spells']['3']['name'].'", r_description="'.addslashes($r_description).'" '.$r_cd.' '.$r_cost.' WHERE champ_keyname="'.$champ_key.'"') or die($db->error);
					$this->logger('info_champions_kit/R', 'R de '.$champ_name.': '.$champ_info['data'][$champ_key]['spells']['3']['name']);
				$done_champs++;
			}
			not_updating('CHAMPION_KIT');
			/* Champion rotation info */
			updating('CHAMPION_ROTATION');
			$db->query('UPDATE lol_champs SET is_rotation=0');
			$ftp_weekly = readjson("https://euw.api.pvp.net/api/lol/euw/v1.2/champion?freeToPlay=true&api_key=".LOL_API_KEY);
			$actual_champ = 0;
			while($actual_champ < count($ftp_weekly['champions']))
			{
				$this->logger('info_champions_rotation', 'El campeón '.champidtoname($ftp_weekly['champions'][$actual_champ]['id']).' está en rotación.');
				$db->query('UPDATE lol_champs SET is_rotation="1" WHERE champ_id="'.$ftp_weekly['champions'][$actual_champ]['id'].'"');
				$actual_champ++;
			}
			not_updating('CHAMPION_ROTATION');
			/* Champion skin sales */
			updating('CHAMPION_SALES');
			$sales = readjson('http://api.lolskinsales.com');
			$db->query('DELETE FROM lol_sales WHERE end_date<'.time().'');
			$this_skin = 0;
			while($this_skin < count($sales))
			{
				$startdate = explode('.',$sales[$this_skin]['start_date']);
				$startdateshown = strtotime($startdate['1'].'-'.$startdate['0'].'-'.$startdate['2']);
				
				$enddate = explode('.',$sales[$this_skin]['end_date']);
				$enddateshown = strtotime($enddate['1'].'-'.$enddate['0'].'-'.$enddate['2']);
				$db->query('INSERT INTO lol_sales (end_date,start_date,champion_id,new_price,old_price,skin_id) VALUES ("'.$enddateshown.'", "'.$startdateshown.'","'.$sales[$this_skin]['champion_id'].'","'.$sales[$this_skin]['new_price'].'","'.$sales[$this_skin]['old_price'].'","'.substr($sales[$this_skin]['skin_loading_url'], -5, -4).'")');
				$this->logger('update_skin_sales', 'Aspecto número '.substr($sales[$this_skin]['skin_loading_url'], -5, -4).' de '.champidtoname($sales[$this_skin]['champion_id']).' está en oferta.');
				$this_skin++;
			}
			not_updating('CHAMPION_SALES');
		}
		else
		{
			$this->logger('info_champions', 'No ejecutado.');
		}
	}
	
	public function wipe_info_challengers()
	{
		if($this->timer('challengerlist') == TRUE)
		{
			global $db;
			global $lol_servers;
			/* SoloQ challengers */
			updating('CHALLENGERLIST');
			$db->query('TRUNCATE TABLE lol_bestsummoners');

			$getplayericon = array();
			foreach($lol_servers as $server => $server_id)
			{
				$data_bestsummons = readjson("https://".$server.".api.pvp.net/api/lol/".$server."/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key=".LOL_API_KEY);
				$done_summoners = 0;
				if(!empty($data_bestsummons['entries']))
				{	
					while($done_summoners < count($data_bestsummons['entries']))
					{
						if($data_bestsummons['entries'][$done_summoners]['isHotStreak'] == null) { $streak = 0; } else { $streak = $data_bestsummons['entries'][$done_summoners]['isHotStreak']; }
						if($data_bestsummons['entries'][$done_summoners]['isVeteran'] == null) { $veteran = 0; } else { $veteran = $data_bestsummons['entries'][$done_summoners]['isVeteran']; }
						if($data_bestsummons['entries'][$done_summoners]['isInactive'] == null) { $inactive = 0; } else { $inactive = $data_bestsummons['entries'][$done_summoners]['isInactive']; }
						if($data_bestsummons['entries'][$done_summoners]['isFreshBlood'] == null) { $recentjoined = 0; } else { $recentjoined = $data_bestsummons['entries'][$done_summoners]['isFreshBlood']; }
						$getplayericon[$server][str_replace(' ','%20',$data_bestsummons['entries'][$done_summoners]['playerOrTeamName'])] = $data_bestsummons['entries'][$done_summoners]['playerOrTeamId'];
						$db->query('INSERT INTO lol_bestsummoners (region,type,player_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined) VALUES ("'.$server.'","RANKED_SOLO_5x5","'.$data_bestsummons['entries'][$done_summoners]['playerOrTeamId'].'","'.$data_bestsummons['entries'][$done_summoners]['playerOrTeamName'].'","'.$data_bestsummons['entries'][$done_summoners]['leaguePoints'].'","'.$data_bestsummons['entries'][$done_summoners]['wins'].'","'.$data_bestsummons['entries'][$done_summoners]['losses'].'","'.$streak.'","'.$veteran.'","'.$inactive.'","'.$recentjoined.'")') or die($db->error); //Insertar invocador
						$this->logger('info_challengerlist/solo', 'Invocador de '.$server.' agregado: '.$data_bestsummons['entries'][$done_summoners]['playerOrTeamName']);
						$done_summoners++;
					}
				}
				/* Optimise API requests on icons ;) */
				$getplayericon_chunked = array_chunk($getplayericon[$server], 38, true);
				$getplayericon_chunked_num = 0;
				foreach($getplayericon_chunked as $chunksummoners)
				{
					$chunked_summoners = 0;
					$chunked_summoners_data = null;
					foreach($chunksummoners as $summoner_name => $summoner_id)
					{
						if($chunked_summoners != 0)
						{
							$chunked_summoners_data .= ',';
						}
						$chunked_summoners_data .= $summoner_name;
						$chunked_summoners++;
					}
					$actualchunkedicons = readjson('https://'.$server.'.api.pvp.net/api/lol/'.$server.'/v1.4/summoner/by-name/'.$chunked_summoners_data.'?api_key=1375edea-27ad-4f0a-80b0-e38402eaa69e');
					
					if(is_array($actualchunkedicons))
					{
						foreach($actualchunkedicons as $keyname => $data)
						{
							if($db->query('SELECT id FROM inv_users WHERE summoner_id="'.$data['id'].'" AND region="'.$server.'" LIMIT 1')->num_rows == 0)
							{
								$db->query('INSERT INTO inv_users (summoner_id,name,onlol_last_update,icon,region) VALUES ("'.$data['id'].'","'.$data['name'].'","0","'.$data['profileIconId'].'", "'.$server.'")');
								$this->logger('info_challengerlist/solo', 'Icono de invocador de '.$data['name'].' en '.$server.' actualizado');
							}
							else
							{
								$db->query('UPDATE inv_users SET icon='.$data['profileIconId'].' WHERE summoner_id="'.$data['id'].'"');
								$this->logger('info_challengerlist/solo', 'Icono de invocador de '.$data['name'].' en '.$server.' actualizado');
							}
						}
						$getplayericon_chunked_num++;
					}
				}
			}
			
			not_updating('CHALLENGERLIST');
			
			/* Team challengers 5x5 */
			updating('CHALLENGERTEAMLIST');
			$db->query('TRUNCATE TABLE lol_bestteams');
			foreach($lol_servers as $server => $server_id)
			{
				$data_bestteams5x5 = readjson("https://".$server.".api.pvp.net/api/lol/".$server."/v2.5/league/challenger?type=RANKED_TEAM_5x5&api_key=".LOL_API_KEY."");
				$done_bestteams5x5 = 0;
				if(!empty($data_bestteams5x5['entries']))
				{	
					while($done_bestteams5x5 < count($data_bestteams5x5['entries']))
					{
						if($data_bestteams5x5['entries'][$done_bestteams5x5]['isHotStreak'] == null) { $streak = 0; } else { $streak = $data_bestteams5x5['entries'][$done_bestteams5x5]['isHotStreak']; }
						if($data_bestteams5x5['entries'][$done_bestteams5x5]['isVeteran'] == null) { $veteran = 0; } else { $veteran = $data_bestteams5x5['entries'][$done_bestteams5x5]['isVeteran']; }
						if($data_bestteams5x5['entries'][$done_bestteams5x5]['isInactive'] == null) { $inactive = 0; } else { $inactive = $data_bestteams5x5['entries'][$done_bestteams5x5]['isInactive']; }
						if($data_bestteams5x5['entries'][$done_bestteams5x5]['isFreshBlood'] == null) { $recentjoined = 0; } else { $recentjoined = $data_bestteams5x5['entries'][$done_bestteams5x5]['isFreshBlood']; }
						
						$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
						VALUES 
						("'.$server.'","RANKED_TEAM_5x5","'.$data_bestteams5x5['entries'][$done_bestteams5x5]['playerOrTeamId'].'",
						"'.$data_bestteams5x5['entries'][$done_bestteams5x5]['playerOrTeamName'].'","'.$data_bestteams5x5['entries'][$done_bestteams5x5]['leaguePoints'].'",
						"'.$data_bestteams5x5['entries'][$done_bestteams5x5]['wins'].'","'.$data_bestteams5x5['entries'][$done_bestteams5x5]['losses'].'","'.$streak.'","'.$veteran.'","'.$inactive.'","'.$recentjoined.'")') or die($db->error); //Insertar invocador
						$this->logger('info_challengerlist/teams', 'Agregado equipo 5x5 '.$data_bestteams5x5['entries'][$done_bestteams5x5]['playerOrTeamName'].' al servidor '.$server.'.');
						$done_bestteams5x5++;
					}
				}
			}

			/* Best teams 3x3 */
			foreach($lol_servers as $server => $server_id)
			{
				$data_bestteams3x3 = readjson("https://".$server.".api.pvp.net/api/lol/".$server."/v2.5/league/challenger?type=RANKED_TEAM_3x3&api_key=".LOL_API_KEY."");
				$done_bestteams3x3 = 0;
				if(!empty($data_bestteams3x3['entries']))
				{
					while($done_bestteams3x3 < count($data_bestteams3x3['entries']))
					{
						if($data_bestteams3x3['entries'][$done_bestteams3x3]['isHotStreak'] == null) { $streak = 0; } else { $streak = $data_bestteams3x3['entries'][$done_bestteams3x3]['isHotStreak']; }
						if($data_bestteams3x3['entries'][$done_bestteams3x3]['isVeteran'] == null) { $veteran = 0; } else { $veteran = $data_bestteams3x3['entries'][$done_bestteams3x3]['isVeteran']; }
						if($data_bestteams3x3['entries'][$done_bestteams3x3]['isInactive'] == null) { $inactive = 0; } else { $inactive = $data_bestteams3x3['entries'][$done_bestteams3x3]['isInactive']; }
						if($data_bestteams3x3['entries'][$done_bestteams3x3]['isFreshBlood'] == null) { $recentjoined = 0; } else { $recentjoined = $data_bestteams3x3['entries'][$done_bestteams3x3]['isFreshBlood']; }
						
						$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
						VALUES 
						("'.$server.'","RANKED_TEAM_3x3","'.$data_bestteams3x3['entries'][$done_bestteams3x3]['playerOrTeamId'].'",
						"'.$data_bestteams3x3['entries'][$done_bestteams3x3]['playerOrTeamName'].'","'.$data_bestteams3x3['entries'][$done_bestteams3x3]['leaguePoints'].'",
						"'.$data_bestteams3x3['entries'][$done_bestteams3x3]['wins'].'","'.$data_bestteams3x3['entries'][$done_bestteams3x3]['losses'].'","'.$streak.'","'.$veteran.'","'.$inactive.'","'.$recentjoined.'")') or die($db->error); //Insertar invocador
						$this->logger('info_challengerlist/teams', 'Agregado equipo 3x3 '.$data_bestteams3x3['entries'][$done_bestteams3x3]['playerOrTeamName'].' al servidor '.$server.'.');
						$done_bestteams3x3++;
					}
				}
			}

			
			not_updating('CHALLENGERTEAMLIST');
		}
		else
		{
			$this->logger('info_challengerlist', 'No ejecutado.');
		}
	}
	
	public function wipe_info_game()
	{
		if($this->timer('gameinfo') == TRUE)
		{
			global $db;
			/* Summoner spells info */
			$this->logger('info_game/spells', 'Actualizando hechizos.');
			$db->query('TRUNCATE TABLE lol_spells');
			updating('GAME_SPELLS');
			$item_data = readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/summoner-spell?locale=es_ES&spellData=all&api_key='.LOL_API_KEY)['data'];
			foreach($item_data as $key => $value)
			{
				$countvaluing = 0;
				$gamemodes = null;
				foreach($value['modes'] as $val2)
				{
					if($countvaluing != 0)
					{
						$gamemodes .= ';';
					}
					$gamemodes .= $val2;
					$countvaluing++;
				}
				$db->query('INSERT INTO lol_spells (spell_id,name,description,summoner_level,keyname,cooldown,gamemodes,lang) VALUES ('.$value['id'].',"'.$value['name'].'","'.$value['description'].'",'.$value['summonerLevel'].',"'.$value['key'].'","'.$value['cooldown'][0].'","'.$gamemodes.'","es_ES")');
				$this->logger('info_game/spells', 'Agregado hechizo: '.$value['name']);
			}
			not_updating('GAME_SPELLS');
			/* Game item data*/
			updating('GAME_ITEMS');
			$db->query('TRUNCATE TABLE lol_items');
			$item_data = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/item.json');
			$done_item = 0;
			while($done_item < count($item_data['data']))
			{
				$item_id = array_slice($item_data['data'],$done_item,1);
				$this_map = 0;
				$valid_maps = 0;
				$avaliablemaps = null;
				if($item_id['0']['maps']['1'] == 1) //Original summoners rift
				{
					$avaliablemaps .= '1;';
				}
				if($item_id['0']['maps']['10'] == 1) //Current twisted treeline
				{
					$avaliablemaps .= '10;';
				}
				if($item_id['0']['maps']['11'] == 1) //Current summoners rift
				{
					$avaliablemaps .= '11;';
				}
				if($item_id['0']['maps']['12'] == 1) //Howling abyss
				{
					$avaliablemaps .= '12;';
				}
					
				$avaliablemaps = substr($avaliablemaps,0,-1);
				$db->query('INSERT INTO lol_items (item_id,name,item_group,description,description_short,builds_into,gold,sell,maps) VALUES ("'.str_replace('.png',null,$item_id['0']['image']['full']).'","'.$item_id['0']['name'].'","'.@$item_id['0']['group'].'","'.$item_id['0']['description'].'","'.$item_id['0']['plaintext'].'","'.implode(';',$item_id['0']['into']).'","'.$item_id['0']['gold']['base'].'","'.$item_id['0']['gold']['sell'].'","'.$avaliablemaps.'")') or die($db->error);
				$done_item++;
				$this->logger('info_game_items', 'Actualizando item: ID -> '.str_replace('.png',null,$item_id['0']['image']['full']).', Nombre -> '.$item_id['0']['name']);
			}
			not_updating('GAME_ITEMS');
			/* Runes info */
			$this->logger('info_game/runes', 'Actualizando Runas.');
			$db->query('TRUNCATE TABLE lol_runes');
			updating('GAME_RUNES');
			$rune_data = readjson('http://ddragon.leagueoflegends.com/cdn/6.1.1/data/es_ES/rune.json')['data'];
			foreach($rune_data as $rune_id => $value)
			{
				if(!array_key_exists(3,$rune_data[$rune_id]['tags']))
				{
					if(!array_key_exists(2,$rune_data[$rune_id]['tags']))
					{
						if(!array_key_exists(1,$rune_data[$rune_id]['tags']))
						{
							$rune_type = $rune_data[$rune_id]['tags'][0];
						}
						else
						{
							$rune_type = $rune_data[$rune_id]['tags'][1];
						}
					}
					else
					{
						$rune_type = $rune_data[$rune_id]['tags'][2];
					}
				}
				else
				{
					$rune_type = $rune_data[$rune_id]['tags'][3];
				}
				$db->query('INSERT INTO lol_runes (rune_id,name,description,tier,type,stats) VALUES ('.$rune_id.',"'.$rune_data[$rune_id]['name'].'","'.$rune_data[$rune_id]['description'].'","'.$rune_data[$rune_id]['rune']['tier'].'","'.$rune_type.'","'.addslashes(json_encode($rune_data[$rune_id]['stats'])).'")');
				$this->logger('info_game/runes', 'Agregada runa: '.$value['name']);
			}
			not_updating('GAME_RUNES');
			
			/* Masteries info */
			$this->logger('info_game/masteries', 'Actualizando maestrías.');
			$db->query('TRUNCATE TABLE lol_masteries');
			updating('GAME_MASTERIES');
			$rune_data = readjson('http://ddragon.leagueoflegends.com/cdn/6.1.1/data/es_ES/mastery.json');
			foreach($rune_data['data'] as $mastery_id => $value)
			{
				$db->query('INSERT INTO lol_masteries (mastery_id,name,description,ranks) VALUES ('.$mastery_id.',"'.$value['name'].'","'.addslashes(json_encode($value['description'])).'","'.$value['ranks'].'")');
				$this->logger('info_game/masteries', 'Agregada maestría: '.$value['name']);
			}
			not_updating('GAME_MASTERIES');
		}
		else
		{
			$this->logger('info_game', 'No ejecutado.');
		}
	}
	public function wipe_stats_mmr()
	{
		if($this->timer('wipe_stats_mmr') == TRUE)
		{
			global $db;
			$this->logger('info_game/mmr_leagueaverage', 'Actualizando mmr de ligas.');
			updating('SUMMONERSMMRAVERAGE');
			/* Bronze 5 */
			$total_summons_b5 = $db->query('SELECT id FROM inv_users WHERE ranked_league="B" AND ranked_division="5"')->num_rows;
			$muestry_b5 = (10* (int) $total_summons_b5) / 100;
			$result_b5 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="B" AND ranked_division="5" LIMIT '.$total_summons_b5.'');
			if($total_summons_b5 != 0)
			{
				$elo_sum_b5 = array();
				while($row = $result_b5->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_b5,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_b5))/$total_summons_b5)+divisionbasemmr('BRONZEV'))/2).'" WHERE league="BRONZE5"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="870" WHERE league="BRONZE5"');
			}
			/* Bronze 4 */
			$total_summons_b4 = $db->query('SELECT id FROM inv_users WHERE ranked_league="B" AND ranked_division="4"')->num_rows;
			$muestry_b4 = (10* (int) $total_summons_b4) / 100;
			$result_b4 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="B" AND ranked_division="4" LIMIT '.$total_summons_b4.'');
			if($total_summons_b4 != 0)
			{
				$elo_sum_b4 = array();
				while($row = $result_b4->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_b4,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_b4))/$total_summons_b4)+divisionbasemmr('BRONZEIV'))/2).'" WHERE league="BRONZE4"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="940" WHERE league="BRONZE4"');
			}
			/* Bronze 3 */
			$total_summons_b3 = $db->query('SELECT id FROM inv_users WHERE ranked_league="B" AND ranked_division="3"')->num_rows;
			$muestry_b3 = (10* (int) $total_summons_b3) / 100;
			$result_b3 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="B" AND ranked_division="3" LIMIT '.$total_summons_b3.'');
			if($total_summons_b3 != 0)
			{
				$elo_sum_b3 = array();
				while($row = $result_b3->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_b3,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_b3))/$total_summons_b3)+divisionbasemmr('BRONZEIII'))/2).'" WHERE league="BRONZE3"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1010" WHERE league="BRONZE3"');
			}
			/* Bronze 2 */
			$total_summons_b2 = $db->query('SELECT id FROM inv_users WHERE ranked_league="B" AND ranked_division="2"')->num_rows;
			$muestry_b2 = (10* (int) $total_summons_b2) / 100;
			$result_b2 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="B" AND ranked_division="2" LIMIT '.$total_summons_b2.'');
			if($total_summons_b2 != 0)
			{
				$elo_sum_b2 = array();
				while($row = $result_b2->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_b2,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_b2))/$total_summons_b2)+divisionbasemmr('BRONZEII'))/2).'" WHERE league="BRONZE2"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1080" WHERE league="BRONZE2"');
			}
			/* Bronze 1 */
			$total_summons_b1 = $db->query('SELECT id FROM inv_users WHERE ranked_league="B" AND ranked_division="1"')->num_rows;
			$muestry_b1 = (10* (int) $total_summons_b1) / 100;
			$result_b1 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="B" AND ranked_division="1" LIMIT '.$total_summons_b1.'');
			if($total_summons_b1 != 0)
			{
				$elo_sum_b1 = array();
				while($row = $result_b1->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_b1,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_b1))/$total_summons_b1)+divisionbasemmr('BRONZEI'))/2).'" WHERE league="BRONZE1"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1150" WHERE league="BRONZE1"');
			}
			/* Silver 5 */
			$total_summons_s5 = $db->query('SELECT id FROM inv_users WHERE ranked_league="S" AND ranked_division="5"')->num_rows;
			$muestry_s5 = (10* (int) $total_summons_s5) / 100;
			$result_s5 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="S" AND ranked_division="5" LIMIT '.$total_summons_s5.'');
			if($total_summons_s5 != 0)
			{
				$elo_sum_s5 = array();
				while($row = $result_s5->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_s5,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_s5))/$total_summons_s5)+divisionbasemmr('SILVERV'))/2).'" WHERE league="SILVER5"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1220" WHERE league="SILVER5"');
			}
			/* Silver 4 */
			$total_summons_s4 = $db->query('SELECT id FROM inv_users WHERE ranked_league="S" AND ranked_division="4"')->num_rows;
			$muestry_s4 = (10* (int) $total_summons_s4) / 100;
			$result_s4 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="S" AND ranked_division="4" LIMIT '.$total_summons_s4.'');
			if($total_summons_s4 != 0)
			{
				$elo_sum_s4 = array();
				while($row = $result_s4->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_s4,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_s4))/$total_summons_s4)+divisionbasemmr('SILVERIV'))/2).'" WHERE league="SILVER4"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1290" WHERE league="SILVER4"');
			}
			/* Silver 3 */
			$total_summons_s3 = $db->query('SELECT id FROM inv_users WHERE ranked_league="S" AND ranked_division="3"')->num_rows;
			$muestry_s3 = (10* (int) $total_summons_s3) / 100;
			$result_s3 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="S" AND ranked_division="3" LIMIT '.$total_summons_s3.'');
			if($total_summons_s3 != 0)
			{
				$elo_sum_s3 = array();
				while($row = $result_s3->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_s3,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_s3))/$total_summons_s3)+divisionbasemmr('SILVERIII'))/2).'" WHERE league="SILVER3"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1360" WHERE league="SILVER3"');
			}
			/* Silver 2 */
			$total_summons_s2 = $db->query('SELECT id FROM inv_users WHERE ranked_league="S" AND ranked_division="2"')->num_rows;
			$muestry_s2 = (10* (int) $total_summons_s2) / 100;
			$result_s2 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="S" AND ranked_division="2" LIMIT '.$total_summons_s2.'');
			if($total_summons_s2 != 0)
			{
				$elo_sum_s2 = array();
				while($row = $result_s2->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_s2,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_s2))/$total_summons_s2)+divisionbasemmr('SILVERII'))/2).'" WHERE league="SILVER2"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1430" WHERE league="SILVER2"');
			}
			/* Silver 1 */
			$total_summons_s1 = $db->query('SELECT id FROM inv_users WHERE ranked_league="S" AND ranked_division="1"')->num_rows;
			$muestry_s1 = (10* (int) $total_summons_s1) / 100;
			$result_s1 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="S" AND ranked_division="1" LIMIT '.$total_summons_s1.'');
			if($total_summons_s1 != 0)
			{
				$elo_sum_s1 = array();
				while($row = $result_s1->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_s1,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_s1))/$total_summons_s1)+divisionbasemmr('SILVERI'))/2).'" WHERE league="SILVER1"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1500" WHERE league="SILVER1"');
			}
			/* Gold 5 */
			$total_summons_g5 = $db->query('SELECT id FROM inv_users WHERE ranked_league="G" AND ranked_division="5"')->num_rows;
			$muestry_g5 = (10* (int) $total_summons_g5) / 100;
			$result_g5 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="G" AND ranked_division="5" LIMIT '.$total_summons_g5.'');
			if($total_summons_g5 != 0)
			{
				$elo_sum_g5 = array();
				while($row = $result_g5->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_g5,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_g5))/$total_summons_g5)+divisionbasemmr('GOLDV'))/2).'" WHERE league="GOLD5"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1570" WHERE league="GOLD5"');
			}
			/* Gold 4 */
			$total_summons_g4 = $db->query('SELECT id FROM inv_users WHERE ranked_league="G" AND ranked_division="4"')->num_rows;
			$muestry_g4 = (10* (int) $total_summons_g4) / 100;
			$result_g4 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="G" AND ranked_division="4" LIMIT '.$total_summons_g4.'');
			if($total_summons_g4 != 0)
			{
				$elo_sum_g4 = array();
				while($row = $result_g4->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_g4,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_g4))/$total_summons_g4)+divisionbasemmr('GOLDIV'))/2).'" WHERE league="GOLD4"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1640" WHERE league="GOLD4"');
			}
			/* Gold 3 */
			$total_summons_g3 = $db->query('SELECT id FROM inv_users WHERE ranked_league="G" AND ranked_division="3"')->num_rows;
			$muestry_g3 = (10* (int) $total_summons_g3) / 100;
			$result_g3 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="G" AND ranked_division="3" LIMIT '.$total_summons_g3.'');
			if($total_summons_g3 != 0)
			{
				$elo_sum_g3 = array();
				while($row = $result_g3->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_g3,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_g3))/$total_summons_g3)+divisionbasemmr('GOLDIII'))/2).'" WHERE league="GOLD3"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1710" WHERE league="GOLD3"');
			}
			/* Gold 2 */
			$total_summons_g2 = $db->query('SELECT id FROM inv_users WHERE ranked_league="G" AND ranked_division="2"')->num_rows;
			$muestry_g2 = (10* (int) $total_summons_g2) / 100;
			$result_g2 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="G" AND ranked_division="2" LIMIT '.$total_summons_g2.'');
			if($total_summons_g2 != 0)
			{
				$elo_sum_g2 = array();
				while($row = $result_g2->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_g2,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_g2))/$total_summons_g2)+divisionbasemmr('GOLDII'))/2).'" WHERE league="GOLD2"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1780" WHERE league="GOLD2"');
			}
			/* Gold 1 */
			$total_summons_g1 = $db->query('SELECT id FROM inv_users WHERE ranked_league="G" AND ranked_division="1"')->num_rows;
			$muestry_g1 = (10* (int) $total_summons_g1) / 100;
			$result_g1 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="G" AND ranked_division="1" LIMIT '.$total_summons_g1.'');
			if($total_summons_g1 != 0)
			{
				$elo_sum_g1 = array();
				while($row = $result_g1->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_g1,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_g1))/$total_summons_g1)+divisionbasemmr('GOLDI'))/2).'" WHERE league="GOLD1"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1850" WHERE league="GOLD1"');
			}
			/* Platinum 5 */
			$total_summons_p5 = $db->query('SELECT id FROM inv_users WHERE ranked_league="P" AND ranked_division="5"')->num_rows;
			$muestry_p5 = (10* (int) $total_summons_p5) / 100;
			$result_p5 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="P" AND ranked_division="5" LIMIT '.$total_summons_p5.'');
			if($total_summons_p5 != 0)
			{
				$elo_sum_p5 = array();
				while($row = $result_p5->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_p5,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_p5))/$total_summons_p5)+divisionbasemmr('PLATINUMV'))/2).'" WHERE league="PLATINUM5"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1920" WHERE league="PLATINUM5"');
			}
			/* Platinum 4 */
			$total_summons_p4 = $db->query('SELECT id FROM inv_users WHERE ranked_league="P" AND ranked_division="4"')->num_rows;
			$muestry_p4 = (10* (int) $total_summons_p4) / 100;
			$result_p4 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="P" AND ranked_division="4" LIMIT '.$total_summons_p4.'');
			if($total_summons_p4 != 0)
			{
				$elo_sum_p4 = array();
				while($row = $result_p4->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_p4,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_p4))/$total_summons_p4)+divisionbasemmr('PLATINUMIV'))/2).'" WHERE league="PLATINUM4"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="1990" WHERE league="PLATINUM4"');
			}
			/* Platinum 3 */
			$total_summons_p3 = $db->query('SELECT id FROM inv_users WHERE ranked_league="P" AND ranked_division="3"')->num_rows;
			$muestry_p3 = (10* (int) $total_summons_p3) / 100;
			$result_p3 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="P" AND ranked_division="3" LIMIT '.$total_summons_p3.'');
			if($total_summons_p3 != 0)
			{
				$elo_sum_p3 = array();
				while($row = $result_p3->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_p3,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_p3))/$total_summons_p3)+divisionbasemmr('PLATINUMIII'))/2).'" WHERE league="PLATINUM3"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="2060" WHERE league="PLATINUM3"');
			}
			/* Platinum 2 */
			$total_summons_p2 = $db->query('SELECT id FROM inv_users WHERE ranked_league="P" AND ranked_division="2"')->num_rows;
			$muestry_p2 = (10* (int) $total_summons_p2) / 100;
			$result_p2 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="P" AND ranked_division="2" LIMIT '.$total_summons_p2.'');
			if($total_summons_p2 != 0)
			{
				$elo_sum_p2 = array();
				while($row = $result_p2->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_p2,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_p2))/$total_summons_p2)+divisionbasemmr('PLATINUMII'))/2).'" WHERE league="PLATINUM2"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="2130" WHERE league="PLATINUM2"');
			}
			/* Platinum 1 */
			$total_summons_p1 = $db->query('SELECT id FROM inv_users WHERE ranked_league="P" AND ranked_division="1"')->num_rows;
			$muestry_p1 = (10* (int) $total_summons_p1) / 100;
			$result_p1 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="P" AND ranked_division="1" LIMIT '.$total_summons_p1.'');
			if($total_summons_p1 != 0)
			{
				$elo_sum_p1 = array();
				while($row = $result_p1->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_p1,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_p1))/$total_summons_p1)+divisionbasemmr('PLATINUMI'))/2).'" WHERE league="PLATINUM1"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="2200" WHERE league="PLATINUM1"');
			}
			/* Diamond 5 */
			$total_summons_d5 = $db->query('SELECT id FROM inv_users WHERE ranked_league="D" AND ranked_division="5"')->num_rows;
			$muestry_d5 = (10* (int) $total_summons_d5) / 100;
			$result_d5 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="D" AND ranked_division="5" LIMIT '.$total_summons_d5.'');
			if($total_summons_d5 != 0)
			{
				$elo_sum_d5 = array();
				while($row = $result_d5->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_d5,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_d5))/$total_summons_d5)+divisionbasemmr('DIAMONDV'))/2).'" WHERE league="DIAMOND5"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="2270" WHERE league="DIAMOND5"');
			}
			/* Diamond 4 */
			$total_summons_d4 = $db->query('SELECT id FROM inv_users WHERE ranked_league="D" AND ranked_division="4"')->num_rows;
			$muestry_d4 = (10* (int) $total_summons_d4) / 100;
			$result_d4 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="D" AND ranked_division="4" LIMIT '.$total_summons_d4.'');
			if($total_summons_d4 != 0)
			{
				$elo_sum_d4 = array();
				while($row = $result_d4->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_d4,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_d4))/$total_summons_d4)+divisionbasemmr('DIAMONDIV'))/2).'" WHERE league="DIAMOND4"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="2340" WHERE league="DIAMOND4"');
			}
			/* Diamond 3 */
			$total_summons_d3 = $db->query('SELECT id FROM inv_users WHERE ranked_league="D" AND ranked_division="3"')->num_rows;
			$muestry_d3 = (10* (int) $total_summons_d3) / 100;
			$result_d3 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="D" AND ranked_division="3" LIMIT '.$total_summons_d3.'');
			if($total_summons_d3 != 0)
			{
				$elo_sum_d3 = array();
				while($row = $result_d3->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_d3,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_d3))/$total_summons_d3)+divisionbasemmr('DIAMONDIII'))/2).'" WHERE league="DIAMOND3"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="2410" WHERE league="DIAMOND3"');
			}
			/* Diamond 2 */
			$total_summons_d2 = $db->query('SELECT id FROM inv_users WHERE ranked_league="D" AND ranked_division="2"')->num_rows;
			$muestry_d2 = (10* (int) $total_summons_d2) / 100;
			$result_d2 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="D" AND ranked_division="2" LIMIT '.$total_summons_d2.'');
			if($total_summons_d2 != 0)
			{
				$elo_sum_d2 = array();
				while($row = $result_d2->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_d2,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_d2))/$total_summons_d2)+divisionbasemmr('DIAMONDII'))/2).'" WHERE league="DIAMOND2"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="2480" WHERE league="DIAMOND2"');
			}
			/* Diamond 1 */
			$total_summons_d1 = $db->query('SELECT id FROM inv_users WHERE ranked_league="D" AND ranked_division="1"')->num_rows;
			$muestry_d1 = (10* (int) $total_summons_d1) / 100;
			$result_d1 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="D" AND ranked_division="1" LIMIT '.$total_summons_d1.'');
			if($total_summons_d1 != 0)
			{
				$elo_sum_d1 = array();
				while($row = $result_d1->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_d1,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_d1))/$total_summons_d1)+divisionbasemmr('DIAMONDI'))/2).'" WHERE league="DIAMOND1"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="2550" WHERE league="DIAMOND1"');
			}
			/* Master 1 */
			$total_summons_m1 = $db->query('SELECT id FROM inv_users WHERE ranked_league="M" AND ranked_division="1"')->num_rows;
			$muestry_m1 = (10* (int) $total_summons_m1) / 100;
			$result_m1 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="M" AND ranked_division="1" LIMIT '.$total_summons_m1.'');
			if($total_summons_m1 != 0)
			{
				$elo_sum_m1 = array();
				while($row = $result_m1->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_m1,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_m1))/$total_summons_m1)+divisionbasemmr('MASTERI'))/2).'" WHERE league="MASTER1"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="2600" WHERE league="MASTER1"');
			}
			/* Challenger 1 */
			$total_summons_c1 = $db->query('SELECT id FROM inv_users WHERE ranked_league="C" AND ranked_division="1"')->num_rows;
			$muestry_c1 = (10* (int) $total_summons_c1) / 100;
			$result_c1 = $db->query('SELECT mmr FROM inv_users WHERE ranked_league="C" AND ranked_division="1" LIMIT '.$total_summons_c1.'');
			if($total_summons_c1 != 0)
			{
				$elo_sum_c1= array();
				while($row = $result_c1->fetch_array(MYSQLI_NUM))
				{
					array_push($elo_sum_c1,$row[0]);
				}
				$db->query('UPDATE mmr_leagueaverage SET average="'.round((((array_sum($elo_sum_c1))/$total_summons_c1)+divisionbasemmr('CHALLENGERI'))/2).'" WHERE league="CHALLENGER1"');
				
			}
			else
			{
				$db->query('UPDATE mmr_leagueaverage SET average="2900" WHERE league="CHALLENGER1"');
			}
			not_updating('SUMMONERSMMRAVERAGE');
		}
		else
		{
			$this->logger('info_summoners_mmr_leagueaverage', 'No ejecutado.');
		}
	}
	
	public function update_actual_summoners() // Set up -> It doesn't working properly
	{
		if($this->timer('reloadinvs') == TRUE)
		{
			global $db;
			updating('SUMMONERS');
			
			$stop_ret_game = false;
			while($stop_ret_game == false)
			{
				$summoners  = $db->query('SELECT summoner_id,region FROM inv_users WHERE onlol_last_update<'.(time()-config('cron_inv_reload')).' ORDER by onlol_last_update ASC LIMIT 20');
				/* Update only matches because it would be lost data */
				 while ($row = $summoners->fetch_row() && $stop_ret_game == false) {
					$this->logger('update_summoners', 'Cargando partidas del usuario '.$row[0]);
					$match_history_normals_db = readjson('https://'.$row[1].'.api.pvp.net/api/lol/'.$row[1].'/v1.3/game/by-summoner/'.$row[0].'/recent?api_key='.LOL_API_KEY);
					$region = $row[1];
					$summoner_id = $row[0];
					if($match_history_normals_db == 'RATE_LIMIT')
					{
						$stop_ret_game = true;
					}
					else
					{
						if(!empty($match_history_normals_db['games'])) //User has to played some games
						{
							foreach($match_history_normals_db['games'] as $gameid => $game_normal_data)
							{
								if($db->query('SELECT id FROM lol_matches WHERE match_id="'.$game_normal_data['gameId'].'" AND region="'.$region.'" LIMIT 1')->num_rows == 0) //Just add game
								{
									if($game_normal_data['stats']['win'] == 'true')
									{
										$game_winner_team_id = $game_normal_data['teamId'];
									}
									else
									{
										if($game_normal_data['teamId'] == 100)
										{
											$game_winner_team_id = 200;
										}
										else
										{
											$game_winner_team_id = 100;
										}
									}
									
									if($game_normal_data['invalid'] == 'true')
									{
										$loss_prevented = 'true';
									}
									else
									{
										$loss_prevented = 'false';
									}
									
									$game_data_array = array();
									$game_data_array['season'] = end($lol_seasons);
									$game_data_array['players'] = array();
									$game_data_array['players'][$summoner_id] = array();
									$game_data_array['players'][$summoner_id]['team'] = $game_normal_data['teamId'];
									$game_data_array['players'][$summoner_id]['spell_1'] = $game_normal_data['spell1'];
									$game_data_array['players'][$summoner_id]['spell_2'] = $game_normal_data['spell2'];
									$game_data_array['players'][$summoner_id]['champ_id'] = $game_normal_data['championId'];
									$game_data_array['players'][$summoner_id]['stats'] = array();
									if($game_normal_data['stats']['win'] == 'true')
									{
										$game_data_array['players'][$summoner_id]['stats']['winner'] = 'true';
									}
									else
									{
										$game_data_array['players'][$summoner_id]['stats']['winner'] = 'false';
									}
									$game_data_array['players'][$summoner_id]['stats']['total_gold'] = (int) @$game_normal_data['stats']['goldEarned'];
									$game_data_array['players'][$summoner_id]['stats']['champ_level'] = $game_normal_data['stats']['level'];
									$game_data_array['players'][$summoner_id]['stats']['kills'] = (int) @$game_normal_data['stats']['championsKilled'];
									$game_data_array['players'][$summoner_id]['stats']['deaths'] = (int) @$game_normal_data['stats']['numDeaths'];
									$game_data_array['players'][$summoner_id]['stats']['assists'] = (int) @$game_normal_data['stats']['assists'];
									$game_data_array['players'][$summoner_id]['stats']['minions'] = (int)@($game_normal_data['stats']['minionsKilled']+$game_normal_data['stats']['neutralMinionsKilled']);
									$game_data_array['players'][$summoner_id]['stats']['items'] = array();
									$game_data_array['players'][$summoner_id]['stats']['items'][0] = (int) @$game_normal_data['stats']['item0'];
									$game_data_array['players'][$summoner_id]['stats']['items'][1] = (int) @$game_normal_data['stats']['item1'];
									$game_data_array['players'][$summoner_id]['stats']['items'][2] = (int) @$game_normal_data['stats']['item2'];
									$game_data_array['players'][$summoner_id]['stats']['items'][3] = (int) @$game_normal_data['stats']['item3'];
									$game_data_array['players'][$summoner_id]['stats']['items'][4] = (int) @$game_normal_data['stats']['item4'];
									$game_data_array['players'][$summoner_id]['stats']['items'][5] = (int) @$game_normal_data['stats']['item5'];
									$game_data_array['players'][$summoner_id]['stats']['items'][6] = (int) @$game_normal_data['stats']['item6'];
									$db->query('INSERT INTO lol_matches (region,match_id,map_id,loss_prevented,winner_team_id,qeue,ips_earned,timestamp_start,timestamp_end,summoner_ids,data) VALUES ("'.$region.'","'.$game_normal_data['gameId'].'","'.$game_normal_data['mapId'].'","'.$loss_prevented.'",'.$game_winner_team_id.',"'.$game_normal_data['subType'].'","{\"'.$summoner_id.'\":'.$game_normal_data['ipEarned'].'}","'.datetounix($game_normal_data['createDate']).'","'.(datetounix($game_normal_data['createDate'])+$game_normal_data['stats']['timePlayed']).'",'.$summoner_id.',"'.addslashes(json_encode($game_data_array)).'")') or die($db->error);
								}
								elseif($db->query('SELECT id FROM lol_matches WHERE match_id='.$game_normal_data['gameId'].' AND summoner_ids LIKE "%'.$summoner_id.'%"')->num_rows == 0) //Game exists, okay. Add new fellow players if they doesn't exists and it's stats
								{
									if($game_normal_data['stats']['win'] == 'true')
									{
										$game_winner_team_id = $game_normal_data['teamId'];
									}
									else
									{
										if($game_normal_data['teamId'] == 100)
										{
											$game_winner_team_id = 200;
										}
										else
										{
											$game_winner_team_id = 100;
										}
									}
									
									if($game_normal_data['invalid'] == 'true')
									{
										$loss_prevented = 'true';
									}
									else
									{
										$loss_prevented = 'false';
									}
									
									$game_data_array = stdtoarray(json_decode($db->query('SELECT data FROM lol_matches WHERE match_id="'.$game_normal_data['gameId'].'" AND region="'.$region.'"')->fetch_row()[0]));
									$game_data_array['players'][$summoner_id] = array();
									$game_data_array['players'][$summoner_id]['team'] = $game_normal_data['teamId'];
									$game_data_array['players'][$summoner_id]['spell_1'] = $game_normal_data['spell1'];
									$game_data_array['players'][$summoner_id]['spell_2'] = $game_normal_data['spell2'];
									$game_data_array['players'][$summoner_id]['champ_id'] = $game_normal_data['championId'];
									$game_data_array['players'][$summoner_id]['stats'] = array();
									if($game_normal_data['stats']['win'] == 'true')
									{
										$game_data_array['players'][$summoner_id]['stats']['winner'] = 'true';
									}
									else
									{
										$game_data_array['players'][$summoner_id]['stats']['winner'] = 'false';
									}
									$game_data_array['players'][$summoner_id]['stats']['total_gold'] = (int) @$game_normal_data['stats']['goldEarned'];
									$game_data_array['players'][$summoner_id]['stats']['champ_level'] = $game_normal_data['stats']['level'];
									$game_data_array['players'][$summoner_id]['stats']['kills'] = (int) @$game_normal_data['stats']['championsKilled'];
									$game_data_array['players'][$summoner_id]['stats']['deaths'] = (int) @$game_normal_data['stats']['numDeaths'];
									$game_data_array['players'][$summoner_id]['stats']['assists'] = (int) @$game_normal_data['stats']['assists'];
									$game_data_array['players'][$summoner_id]['stats']['minions'] = (int)@($game_normal_data['stats']['minionsKilled']+$game_normal_data['stats']['neutralMinionsKilled']);
									$game_data_array['players'][$summoner_id]['stats']['items'] = array();
									$game_data_array['players'][$summoner_id]['stats']['items'][0] = (int) @$game_normal_data['stats']['item0'];
									$game_data_array['players'][$summoner_id]['stats']['items'][1] = (int) @$game_normal_data['stats']['item1'];
									$game_data_array['players'][$summoner_id]['stats']['items'][2] = (int) @$game_normal_data['stats']['item2'];
									$game_data_array['players'][$summoner_id]['stats']['items'][3] = (int) @$game_normal_data['stats']['item3'];
									$game_data_array['players'][$summoner_id]['stats']['items'][4] = (int) @$game_normal_data['stats']['item4'];
									$game_data_array['players'][$summoner_id]['stats']['items'][5] = (int) @$game_normal_data['stats']['item5'];
									$game_data_array['players'][$summoner_id]['stats']['items'][6] = (int) @$game_normal_data['stats']['item6'];
									
									$actual_match_ips = stdtoarray(json_decode($db->query('SELECT ips_earned FROM lol_matches WHERE match_id="'.$game_normal_data['gameId'].'" AND region="'.$region.'"')->fetch_row()[0]));
									if(array_key_exists($summoner_id,$actual_match_ips) == FALSE)
									{
										$actual_match_ips[$summoner_id] = $game_normal_data['ipEarned'];
									}
									$actual_match_summoners = $db->query('SELECT summoner_ids FROM lol_matches WHERE match_id="'.$game_normal_data['gameId'].'" AND region="'.$region.'"')->fetch_row()[0];
									$actual_match_summoners_array = explode(';',$actual_match_summoners);
									if(!in_array($summoner_id, $actual_match_summoners_array)) {
										$actual_match_summoners = $actual_match_summoners.';'.$summoner_id;
									}
									
									
									$db->query('UPDATE lol_matches SET ips_earned="'.addslashes(json_encode($actual_match_ips)).'",summoner_ids="'.$actual_match_summoners.'",data="'.addslashes(json_encode($game_data_array)).'" WHERE match_id='.$game_normal_data['gameId']) or die($db->error);
								}
							}
						}
						$db->query('UPDATE inv_users SET onlol_last_update='.time().' WHERE summoner_id='.$row[0]);
					}
					sleep(5);
				}
			}
			not_updating('SUMMONERS');
		}
		else
		{
			$this->logger('update_summoners', 'No ejecutado.');
		}
	}
	
	public function wipe_stats()
	{
		if($this->timer('wipe_stats') == TRUE)
		{
			global $db;
			updating('STATS');
			/* Rank distribution */
			$summoners_b = $db->query('SELECT id FROM inv_users WHERE ranked_league="B"')->num_rows;
			$summoners_s = $db->query('SELECT id FROM inv_users WHERE ranked_league="S"')->num_rows;
			$summoners_g = $db->query('SELECT id FROM inv_users WHERE ranked_league="G"')->num_rows;
			$summoners_p = $db->query('SELECT id FROM inv_users WHERE ranked_league="P"')->num_rows;
			$summoners_d = $db->query('SELECT id FROM inv_users WHERE ranked_league="D"')->num_rows;
			$summoners_m = $db->query('SELECT id FROM inv_users WHERE ranked_league="M"')->num_rows;
			$summoners_c = $db->query('SELECT id FROM inv_users WHERE ranked_league="C"')->num_rows;
				
			$db->query('UPDATE lol_stats SET value="'.$summoners_b.'" WHERE stat="rank_distribution_bronze"');
			$this->logger('stats/rankdistribution', $summoners_b.' Invocadores en bronce.');
			$db->query('UPDATE lol_stats SET value="'.$summoners_s.'" WHERE stat="rank_distribution_silver"');
			$this->logger('stats/rankdistribution', $summoners_s.' Invocadores en plata.');
			$db->query('UPDATE lol_stats SET value="'.$summoners_g.'" WHERE stat="rank_distribution_gold"');
			$this->logger('stats/rankdistribution', $summoners_g.' Invocadores en oro.');
			$db->query('UPDATE lol_stats SET value="'.$summoners_p.'" WHERE stat="rank_distribution_platinum"');
			$this->logger('stats/rankdistribution', $summoners_p.' Invocadores en platino.');
			$db->query('UPDATE lol_stats SET value="'.$summoners_d.'" WHERE stat="rank_distribution_diamond"');
			$this->logger('stats/rankdistribution', $summoners_d.' Invocadores en diamante.');
			$db->query('UPDATE lol_stats SET value="'.$summoners_m.'" WHERE stat="rank_distribution_master"');
			$this->logger('stats/rankdistribution', $summoners_m.' Invocadores en maestro.');
			$db->query('UPDATE lol_stats SET value="'.$summoners_c.'" WHERE stat="rank_distribution_challenger"');
			$this->logger('stats/rankdistribution', $summoners_c.' Invocadores en aspirante.');
			
			/* Per side statistics */
			
			$this->logger('stats/perside','Actualizando estadísticas por lado...');
			
			$lolmatches_1 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="NORMAL"')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_1.'" WHERE stat="side_victory_normals_blue"');
			$lolmatches_2 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="NORMAL"')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_2.'" WHERE stat="side_victory_normals_purple"');
			$lolmatches_3 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_SOLO_5x5"')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_3.'" WHERE stat="side_victory_soloq_purple"');
			$lolmatches_4 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_SOLO_5x5"')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_4.'" WHERE stat="side_victory_soloq_blue"');
			$lolmatches_5 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_TEAM_5x5"')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_5.'" WHERE stat="side_victory_rankedteam_purple"');
			$lolmatches_6 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_TEAM_5x5"')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_6.'" WHERE stat="side_victory_rankedteam_blue"');
			$lolmatches_7 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="CAP_5x5"')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_7.'" WHERE stat="side_victory_teamcreator_purple"');
			$lolmatches_8 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="CAP_5x5"')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_8.'" WHERE stat="side_victory_teamcreator_blue"');
			$lolmatches_9 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="ARAM_UNRANKED_5x5"')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_9.'" WHERE stat="side_victory_aram_purple"');
			$lolmatches_10 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="ARAM_UNRANKED_5x5"')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_10.'" WHERE stat="side_victory_aram_blue"');
			
			$this->logger('stats/perside','Estadísticas por lado actualizadas.');
			
			$this->logger('stats/perside','Actualizando estadísticas por primera acción...');
			
			$lolmatches_x_fix1 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_SOLO_5x5" AND first_tower=200')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_x_fix1.'" WHERE stat="side_victoryftower_soloq_purple"');
			$lolmatches_x_fix2 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_SOLO_5x5" AND first_tower=100')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_x_fix2.'" WHERE stat="side_victoryftower_soloq_blue"');
			
			$lolmatches_13 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_TEAM_5x5" AND first_tower=200')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_13.'" WHERE stat="side_victoryftower_rankedteam_purple"');
			$lolmatches_14 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_TEAM_5x5" AND first_tower=100')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_14.'" WHERE stat="side_victoryftower_rankedteam_blue"');
			
			
			$lolmatches_15 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_SOLO_5x5" AND first_herald=200')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_15.'" WHERE stat="side_victoryfherald_soloq_purple"');
			$lolmatches_16 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_SOLO_5x5" AND first_herald=100')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_16.'" WHERE stat="side_victoryfherald_soloq_blue"');
			
			$lolmatches_15_fix = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_TEAM_5x5" AND first_herald=200')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_15_fix.'" WHERE stat="side_victoryfherald_rankedteam_purple"');
			$lolmatches_16_fix = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_TEAM_5x5" AND first_herald=100')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_16_fix.'" WHERE stat="side_victoryfherald_rankedteam_blue"');
			
			$lolmatches_17 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_TEAM_5x5" AND first_dragon=200')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_17.'" WHERE stat="side_victoryfdragon_rankedteam_purple"');
			$lolmatches_18 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_TEAM_5x5" AND first_dragon=100')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_18.'" WHERE stat="side_victoryfdragon_rankedteam_blue"');
			
			$lolmatches_17_fix = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_SOLO_5x5" AND first_dragon=200')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_17_fix.'" WHERE stat="side_victoryfdragon_soloq_purple"');
			$lolmatches_18_fix = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_SOLO_5x5" AND first_dragon=100')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_18_fix.'" WHERE stat="side_victoryfdragon_soloq_blue"');
			
			$lolmatches_19 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_SOLO_5x5" AND first_baron=200')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_19.'" WHERE stat="side_victoryfbaron_soloq_purple"');
			$lolmatches_20 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_SOLO_5x5" AND first_baron=100')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_20.'" WHERE stat="side_victoryfbaron_soloq_blue"');
			
			$lolmatches_20 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_TEAM_5x5" AND first_baron=200')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_20.'" WHERE stat="side_victoryfbaron_rankedteam_purple"');
			$lolmatches_21 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_TEAM_5x5" AND first_baron=100')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_21.'" WHERE stat="side_victoryfbaron_rankedteam_blue"');
			
			$lolmatches_22 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_SOLO_5x5" AND first_blood=200')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_22.'" WHERE stat="side_victoryfblood_soloq_purple"');
			$lolmatches_23 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_SOLO_5x5" AND first_blood=100')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_23.'" WHERE stat="side_victoryfblood_soloq_blue"');
			
			$lolmatches_24 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_TEAM_5x5" AND first_blood=200')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_24.'" WHERE stat="side_victoryfblood_rankedteam_purple"');
			$lolmatches_25 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_TEAM_5x5" AND first_blood=100')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_25.'" WHERE stat="side_victoryfblood_rankedteam_blue"');
			
			$lolmatches_26 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_SOLO_5x5" AND first_inhib=200')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_22.'" WHERE stat="side_victoryfinhib_soloq_purple"');
			$lolmatches_27 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_SOLO_5x5" AND first_inhib=100')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_23.'" WHERE stat="side_victoryfinhib_soloq_blue"');
			
			$lolmatches_28 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=200 AND qeue="RANKED_TEAM_5x5" AND first_inhib=200')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_24.'" WHERE stat="side_victoryfinhib_rankedteam_purple"');
			$lolmatches_29 = $db->query('SELECT id FROM lol_matches WHERE winner_team_id=100 AND qeue="RANKED_TEAM_5x5" AND first_inhib=100')->num_rows;
			$db->query('UPDATE lol_stats SET value="'.$lolmatches_25.'" WHERE stat="side_victoryfinhib_rankedteam_blue"');
			
			$this->logger('stats/perside','Estadísticas por primera acción actualizadas.');
			
			not_updating('STATS');
		}
		else
		{
			$this->logger('wipe_stats', 'No ejecutado.');
		}
	}
	public function bestplayers_champions()
	{
		if($this->timer('bestplayers_champions') == TRUE)
		{
			global $db;
			updating('bestplayers_champions');
			$db->query('TRUNCATE TABLE inv_users_bestchampplayers');
			$db->query('TRUNCATE TABLE inv_users_morechampplayers');
			$db->query('TRUNCATE TABLE inv_users_moremasterychamplayers');
			$db->query('TRUNCATE TABLE inv_users_moremasteryplayers');
			$db->query('DELETE FROM inv_users_badges WHERE badge_keyname LIKE "best_player_of_%"');
			$db->query('DELETE FROM inv_users_badges WHERE badge_keyname LIKE "enthusiastic_player_of_%"');
			$db->query('DELETE FROM inv_users_badges WHERE badge_keyname LIKE "mastery_player_of_%"');
			$db->query('DELETE FROM inv_users_badges WHERE badge_keyname LIKE "mastery_player_global_level_%"');
			$db->query('DELETE FROM inv_users_badges WHERE badge_keyname LIKE "mastery_player_global_points_%"');
			
			$users_data = $db->query('SELECT data,summoner_id,region FROM inv_users_champskill');
			$finalchampsarray_bestsumms = array();
			$finalchampsarray_moregamessumms = array();
			while($row = $users_data->fetch_row())
			{
				if($row[0] != '[]')
				{
					$this_user_champs = stdtoarray(json_decode($row[0]));
					$this_user_id = $row[1];
					$this_user_region = $row[2];
					foreach($this_user_champs as $data)
					{
						if($data['champ_id'] != 0)
						{
							$finalchampsarray_bestsumms[$data['champ_id']][$data['skill']]['summoner_id'] = $this_user_id;
							$finalchampsarray_bestsumms[$data['champ_id']][$data['skill']]['kda'] = $data['kda'];
							$finalchampsarray_bestsumms[$data['champ_id']][$data['skill']]['matches'] = $data['matches'];
							$finalchampsarray_bestsumms[$data['champ_id']][$data['skill']]['winrate'] = $data['winrate'];
							krsort($finalchampsarray_bestsumms[$data['champ_id']]);
							/* Order to the more games players */
							$finalchampsarray_moregamessumms[$data['champ_id']][$data['matches']]['summoner_id'] = $this_user_id;
							$finalchampsarray_moregamessumms[$data['champ_id']][$data['matches']]['kda'] = $data['kda'];
							$finalchampsarray_moregamessumms[$data['champ_id']][$data['matches']]['skill'] = $data['skill'];
							$finalchampsarray_moregamessumms[$data['champ_id']][$data['matches']]['winrate'] = $data['winrate'];
							krsort($finalchampsarray_moregamessumms[$data['champ_id']]);
						}
					}
				}
			}
			ksort($finalchampsarray_bestsumms);
			foreach($finalchampsarray_bestsumms as $champ_id => $data)
			{
					$summonersonthischamp = 1;
					foreach($data as $skill => $summoner_datainfo)
					{
						$summoner_data = $db->query('SELECT icon,name FROM inv_users WHERE summoner_id='.$summoner_datainfo['summoner_id'].'')->fetch_row();
						/* Add to best players of the champ */
						$db->query('INSERT INTO inv_users_bestchampplayers (champ_id,rank,score,icon,name,kda,matches,winrate) VALUES ('.$champ_id.','.$summonersonthischamp.','.$skill.','.$summoner_data[0].',"'.$summoner_data[1].'","'.$summoner_datainfo['kda'].'",'.$summoner_datainfo['matches'].','.$summoner_datainfo['winrate'].')');
						/* Add badge to the user */
						$db->query('INSERT INTO inv_users_badges (badge_keyname,title,description,summoner_id,extradata) VALUES ("best_player_of_'.champidtokeyname($champ_id).'","Mejor jugador universal de '.champidtoname($champ_id).'","#'.$summonersonthischamp.' Mejor jugador del mundo de '.champidtoname($champ_id).'",'.$summoner_datainfo['summoner_id'].','.$summonersonthischamp.')');
						$summonersonthischamp++;
					}
			}
			ksort($finalchampsarray_moregamessumms);
			foreach($finalchampsarray_moregamessumms as $champ_id => $data)
			{
					$summonersonthischamp_more = 1;
					foreach($data as $matches => $summoner_datainfo)
					{
						$summoner_data = $db->query('SELECT icon,name FROM inv_users WHERE summoner_id='.$summoner_datainfo['summoner_id'].'')->fetch_row();
						/* Add to best players of the champ */
						$db->query('INSERT INTO inv_users_morechampplayers (champ_id,rank,score,icon,name,kda,matches,winrate) VALUES ('.$champ_id.','.$summonersonthischamp_more.','.$summoner_datainfo['skill'].','.$summoner_data[0].',"'.$summoner_data[1].'","'.$summoner_datainfo['kda'].'",'.$matches.','.$summoner_datainfo['winrate'].')');
						/* Add badge to the user */
						$db->query('INSERT INTO inv_users_badges (badge_keyname,title,description,summoner_id,extradata) VALUES ("enthusiastic_player_of_'.champidtokeyname($champ_id).'","Entusiasta universal de '.champidtoname($champ_id).'","#'.$summonersonthischamp_more.' Entusiasta de '.champidtoname($champ_id).'",'.$summoner_datainfo['summoner_id'].','.$summonersonthischamp_more.')');
						$summonersonthischamp_more++;
					}
			}
			
			$users_data_mastery = $db->query('SELECT data,summoner_id,region FROM inv_users_champmastery');
			$finalchampsarray_mastery = array();
			$finalchampsarray_mastery['champs'] = array();
			$finalchampsarray_mastery['ranking_level'] = array();
			$finalchampsarray_mastery['ranking_score'] = array();
			while($row = $users_data_mastery->fetch_row())
			{
				if($row[0] != '[]' AND $row[0] != null AND $row[0] != '""')
				{
					$data = stdtoarray(json_decode($row[0]));
					if(array_key_exists('champs',$data))
					{
					$champs = $data['champs'];
					
					$total_score = $data['total_score'];
					$total_levels = $data['total_levels'];
					$finalchampsarray_mastery['ranking_level'][$total_levels] = $row[1];
					$finalchampsarray_mastery['ranking_score'][$total_score] = $row[1];
					
					foreach($champs as $champdata)
					{
						if(!array_key_exists($champdata['champ_id'],$finalchampsarray_mastery['champs']))
						{
							$finalchampsarray_mastery['champs'][$champdata['champ_id']] = array();
						}
						$finalchampsarray_mastery['champs'][$champdata['champ_id']][$champdata['mastery_points']] = $row[1];
					}
						
					}
				}
			}
			
			ksort($finalchampsarray_mastery['ranking_level']);
			ksort($finalchampsarray_mastery['ranking_score']);
			$count_mastery_2 = 0;
			foreach($finalchampsarray_mastery['champs'] as $champ_id => $data)
			{
				krsort($data);
				
				$summonersonthischamp = 1;
				foreach($data as $skill => $summoner_datainfo)
				{
					if($summonersonthischamp >= config('max_users_per_ranking'))
					{
						break;
					}
					$summoner_data = $db->query('SELECT icon,region,name FROM inv_users WHERE summoner_id='.$summoner_datainfo.'')->fetch_row();
					/* Add to best mastery of the champ */
					$this->logger('rankings/champ_mastery', $summoner_data[2].' Agregado a los rankings de maestría.');
					$db->query('INSERT INTO inv_users_moremasterychamplayers (champ_id,rank,score,icon,region,name) VALUES ('.$champ_id.','.$summonersonthischamp.','.$skill.','.$summoner_data[0].',"'.$summoner_data[1].'","'.$summoner_data[2].'")') or die($db->error);
					/* Add badge to the user */
					$db->query('INSERT INTO inv_users_badges (badge_keyname,title,description,summoner_id,extradata) VALUES ("mastery_player_of_'.champidtokeyname($champ_id).'","Jugador con más maestría de '.champidtoname($champ_id).'","#'.$summonersonthischamp.' Jugador con más maestría de '.champidtoname($champ_id).'",'.$summoner_datainfo.','.$summonersonthischamp.')') or die($db->error);
					$summonersonthischamp++;
				}
				$count_mastery_2++;
			}
			$count_mastery = 1;
			foreach($finalchampsarray_mastery['ranking_level'] as $score => $summoner_id)
			{
				if($count_mastery >= config('max_users_per_ranking'))
				{
					break;
				}
				
					$summoner_data = $db->query('SELECT icon,region,name FROM inv_users WHERE summoner_id='.$summoner_id)->fetch_row();
					/* Add to best mastery of the champ */
					$this->logger('rankings/champ_mastery', $summoner_data[2].' Agregado a los rankings de maestría.');
					$db->query('INSERT INTO inv_users_moremasteryplayers (rank,score,icon,region,name,type) VALUES ('.$count_mastery.','.$score.','.$summoner_data[0].',"'.$summoner_data[1].'","'.$summoner_data[2].'","level")') or die($db->error);
					/* Add badge to the user */
					$db->query('INSERT INTO inv_users_badges (badge_keyname,title,description,summoner_id,extradata) VALUES ("mastery_player_global_level_'.$count_mastery.'","Jugador con más nivel de maestría #'.$count_mastery.'","#'.$count_mastery.' Jugador con más nivel de maestría",'.$summoner_id.','.$summonersonthischamp.')') or die($db->error);

				$count_mastery++;
			}
			$count_mastery_3 = 1;
			foreach($finalchampsarray_mastery['ranking_score'] as $score => $summoner_id)
			{
				if($count_mastery_3 >= config('max_users_per_ranking'))
				{
					break;
				}
					$summoner_data = $db->query('SELECT icon,region,name FROM inv_users WHERE summoner_id='.$summoner_id)->fetch_row();
					$this->logger('rankings/champ_mastery', $summoner_data[2].' Agregado a los rankings de maestría.');
					$db->query('INSERT INTO inv_users_moremasteryplayers (rank,score,icon,region,name,type) VALUES ('.$count_mastery_3.','.$score.','.$summoner_data[0].',"'.$summoner_data[1].'","'.$summoner_data[2].'","points")') or die($db->error);
					/* Add badge to the user */
					$db->query('INSERT INTO inv_users_badges (badge_keyname,title,description,summoner_id,extradata) VALUES ("mastery_player_global_level_'.$count_mastery_3.'","Jugador con más puntuación de maestría #'.$count_mastery_3.'","#'.$count_mastery_3.' Jugador con más puntuación de maestría",'.$summoner_id.','.$count_mastery_3.')') or die($db->error);
				$count_mastery_3++;
			}
			
			not_updating('bestplayers_champions');
		}
		else
		{
			$this->logger('bestplayers_champions', 'No ejecutado.');
		}
	}
}