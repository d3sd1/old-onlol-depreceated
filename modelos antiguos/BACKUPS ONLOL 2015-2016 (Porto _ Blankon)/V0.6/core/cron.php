<?php
$update_e = true;
/* Incluir los filtros de seguridad */
require('security.php');
/* Conectar a la base de datos */
require('database.php');
/* Incluir las clases */
require('class.php');
header('Content-Type: text/html; charset=utf-8');
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
		/* Put executables crons here 
		$this->wipe_images_summoner();
		$this->wipe_images_game();
		$this->wipe_images_champions();
		
		$this->wipe_info_champions();
		$this->wipe_info_challengers();
		$this->wipe_info_game();
		
		$this->update_actual_summoners();
		$this->wipe_stats();
		$this->wipe_info_champions();
		$this->wipe_stats_mmr();**/
		$this->bestplayers_champions();
	}
	public function logger($cat,$str)
	{
		echo '['.date('H:i:s').'] ['.$cat.'] '.$str.'<br>';
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
			$champ_data = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/champion.json');
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
				$lore_es = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/champion/'.$champ_key.'.json');
				$lore = $lore_es['data'][$champ_key]['lore']; //AGREGAR AQUI MULTIIDIOMA ;)
				$db->query('INSERT INTO lol_champs (champ_id,champ_keyname,champname,title,info_attack,info_defense,info_magic,info_difficulty,role_1,'.$secondbar_c.'kit_bar,base_hp,scale_hp_lvl,base_bar,scale_bar_lvl,movspeed,base_armor,scale_armor_lvl,base_spellblock,scale_spellblock_lvl,attackrange,base_hpregen,scale_hpregen_lvl,base_manareg,scale_manareg_lvl,base_crit,scale_crit_lvl,base_ad,scale_ad_lvl,offset_as,scale_as_lvl,lore) VALUES ("'.$champ_info[$champ_key]['key'].'","'.$champ_key.'","'.$champ_name.'","'.$champ_info[$champ_key]['title'].'","'.$champ_info[$champ_key]['info']['attack'].'","'.$champ_info[$champ_key]['info']['defense'].'","'.$champ_info[$champ_key]['info']['magic'].'","'.$champ_info[$champ_key]['info']['difficulty'].'","'.strtolower($champ_info[$champ_key]['tags']['0']).'"'.strtolower($secondbar_v).',"'.strtolower($champ_info[$champ_key]['partype']).'","'.$champ_info[$champ_key]['stats']['hp'].'","'.$champ_info[$champ_key]['stats']['hpperlevel'].'","'.$champ_info[$champ_key]['stats']['mp'].'","'.$champ_info[$champ_key]['stats']['mpperlevel'].'","'.$champ_info[$champ_key]['stats']['movespeed'].'","'.$champ_info[$champ_key]['stats']['armor'].'","'.$champ_info[$champ_key]['stats']['armorperlevel'].'","'.$champ_info[$champ_key]['stats']['spellblock'].'","'.$champ_info[$champ_key]['stats']['spellblockperlevel'].'","'.$champ_info[$champ_key]['stats']['attackrange'].'","'.$champ_info[$champ_key]['stats']['hpregen'].'","'.$champ_info[$champ_key]['stats']['hpregenperlevel'].'","'.$champ_info[$champ_key]['stats']['mpregen'].'","'.$champ_info[$champ_key]['stats']['mpregenperlevel'].'","'.$champ_info[$champ_key]['stats']['crit'].'","'.$champ_info[$champ_key]['stats']['critperlevel'].'","'.$champ_info[$champ_key]['stats']['attackdamage'].'","'.$champ_info[$champ_key]['stats']['attackdamageperlevel'].'","'.$champ_info[$champ_key]['stats']['attackspeedoffset'].'","'.$champ_info[$champ_key]['stats']['attackspeedperlevel'].'","'.$lore.'")') or die($db->error); //Si el valor es dintinto, actualizar información.
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
			/* SoloQ challengers */
			updating('CHALLENGERLIST');
			$db->query('TRUNCATE TABLE lol_bestsummoners');

			/* Best summoners EUW -> SOLOQ 5x5 */
			$data_bestsummons_euw = readjson("https://euw.api.pvp.net/api/lol/euw/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key=".LOL_API_KEY."");
			$done_summoners_euw = 0;
			if(!empty($data_bestsummons_euw['entries']))
			{	
				while($done_summoners_euw < count($data_bestsummons_euw['entries']))
				{
					if($data_bestsummons_euw['entries'][$done_summoners_euw]['isHotStreak'] == null) { $euw_streak = 0; } else { $euw_streak = $data_bestsummons_euw['entries'][$done_summoners_euw]['isHotStreak']; }
					if($data_bestsummons_euw['entries'][$done_summoners_euw]['isVeteran'] == null) { $euw_veteran = 0; } else { $euw_veteran = $data_bestsummons_euw['entries'][$done_summoners_euw]['isVeteran']; }
					if($data_bestsummons_euw['entries'][$done_summoners_euw]['isInactive'] == null) { $euw_inactive = 0; } else { $euw_inactive = $data_bestsummons_euw['entries'][$done_summoners_euw]['isInactive']; }
					if($data_bestsummons_euw['entries'][$done_summoners_euw]['isFreshBlood'] == null) { $euw_recentjoined = 0; } else { $euw_recentjoined = $data_bestsummons_euw['entries'][$done_summoners_euw]['isFreshBlood']; }
					if($db->query('SELECT id FROM inv_users WHERE summoner_id="'.$data_bestsummons_euw['entries'][$done_summoners_euw]['playerOrTeamId'].'" AND region="euw" LIMIT 1')->num_rows == 0)
					{
						@$summoner_icon_euw = readjson("https://euw.api.pvp.net/api/lol/euw/v1.4/summoner/by-name/".str_replace(" ","%20",$data_bestsummons_euw['entries'][$done_summoners_euw]['playerOrTeamName'])."?api_key=".LOL_API_KEY."")[str_replace(" ","",strtolower($data_bestsummons_euw['entries'][$done_summoners_euw]['playerOrTeamName']))]['profileIconId'];
						if(!empty($summoner_icon_euw))
						{
						$db->query('INSERT INTO inv_users (summoner_id,name,onlol_last_update,icon,region) VALUES ("'.$data_bestsummons_euw['entries'][$done_summoners_euw]['playerOrTeamId'].'","'.$data_bestsummons_euw['entries'][$done_summoners_euw]['playerOrTeamName'].'","0","'.$summoner_icon_euw.'", "euw")');
						}
					}
					else
					{
						$summoner_icon_euw = $db->query('SELECT icon FROM inv_users WHERE summoner_id="'.$data_bestsummons_euw['entries'][$done_summoners_euw]['playerOrTeamId'].'" AND region="euw" LIMIT 1')->fetch_row()[0];
					}
					$db->query('INSERT INTO lol_bestsummoners (region,type,player_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined,icon) VALUES ("euw","RANKED_SOLO_5x5","'.$data_bestsummons_euw['entries'][$done_summoners_euw]['playerOrTeamId'].'","'.$data_bestsummons_euw['entries'][$done_summoners_euw]['playerOrTeamName'].'","'.$data_bestsummons_euw['entries'][$done_summoners_euw]['leaguePoints'].'","'.$data_bestsummons_euw['entries'][$done_summoners_euw]['wins'].'","'.$data_bestsummons_euw['entries'][$done_summoners_euw]['losses'].'","'.$euw_streak.'","'.$euw_veteran.'","'.$euw_inactive.'","'.$euw_recentjoined.'","'.$summoner_icon_euw.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/solo', 'Invocador europeo agregado: '.$data_bestsummons_euw['entries'][$done_summoners_euw]['playerOrTeamName']);
					$done_summoners_euw++;
				}
			}
			/* Best summoners EUNE -> SOLOQ 5x5 */
			$data_bestsummons_eune = readjson("https://eune.api.pvp.net/api/lol/eune/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key=".LOL_API_KEY."");
			$done_summoners_eune = 0;
			if(!empty($data_bestsummons_eune['entries']))
			{	
				while($done_summoners_eune < count($data_bestsummons_eune['entries']))
				{
					if($data_bestsummons_eune['entries'][$done_summoners_eune]['isHotStreak'] == null) { $eune_streak = 0; } else { $eune_streak = $data_bestsummons_eune['entries'][$done_summoners_eune]['isHotStreak']; }
					if($data_bestsummons_eune['entries'][$done_summoners_eune]['isVeteran'] == null) { $eune_veteran = 0; } else { $eune_veteran = $data_bestsummons_eune['entries'][$done_summoners_eune]['isVeteran']; }
					if($data_bestsummons_eune['entries'][$done_summoners_eune]['isInactive'] == null) { $eune_inactive = 0; } else { $eune_inactive = $data_bestsummons_eune['entries'][$done_summoners_eune]['isInactive']; }
					if($data_bestsummons_eune['entries'][$done_summoners_eune]['isFreshBlood'] == null) { $eune_recentjoined = 0; } else { $eune_recentjoined = $data_bestsummons_eune['entries'][$done_summoners_eune]['isFreshBlood']; }
					
					if($db->query('SELECT id FROM inv_users WHERE summoner_id="'.$data_bestsummons_eune['entries'][$done_summoners_eune]['playerOrTeamId'].'" AND region="eune" LIMIT 1')->num_rows == 0)
					{
						@$summoner_icon_eune = readjson("https://eune.api.pvp.net/api/lol/eune/v1.4/summoner/by-name/".str_replace(" ","%20",$data_bestsummons_eune['entries'][$done_summoners_eune]['playerOrTeamName'])."?api_key=".LOL_API_KEY."")[str_replace(" ","",strtolower($data_bestsummons_eune['entries'][$done_summoners_eune]['playerOrTeamName']))]['profileIconId'];
						if(!empty($summoner_icon_eune))
						{
						$db->query('INSERT INTO inv_users (summoner_id,name,onlol_last_update,icon,region) VALUES ("'.$data_bestsummons_eune['entries'][$done_summoners_eune]['playerOrTeamId'].'","'.$data_bestsummons_eune['entries'][$done_summoners_eune]['playerOrTeamName'].'","0","'.$summoner_icon_eune.'", "eune")');
						}
					}
					else
					{
						$summoner_icon_eune = $db->query('SELECT icon FROM inv_users WHERE summoner_id="'.$data_bestsummons_eune['entries'][$done_summoners_eune]['playerOrTeamId'].'" AND region="eune" LIMIT 1')->fetch_row()[0];
					}
					$db->query('INSERT INTO lol_bestsummoners (region,type,player_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined,icon) VALUES 
					("eune","RANKED_SOLO_5x5","'.$data_bestsummons_eune['entries'][$done_summoners_eune]['playerOrTeamId'].'","'.$data_bestsummons_eune['entries'][$done_summoners_eune]['playerOrTeamName'].'","'.$data_bestsummons_eune['entries'][$done_summoners_eune]['leaguePoints'].'","'.$data_bestsummons_eune['entries'][$done_summoners_eune]['wins'].'","'.$data_bestsummons_eune['entries'][$done_summoners_eune]['losses'].'","'.$eune_streak.'","'.$eune_veteran.'","'.$eune_inactive.'","'.$eune_recentjoined.'","'.$summoner_icon_eune.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/solo', 'Invocador nórdico agregado: '.$data_bestsummons_eune['entries'][$done_summoners_eune]['playerOrTeamName']);
					$done_summoners_eune++;
				}
			}

			/* Best summoners BR -> SOLOQ 5x5 */
			$data_bestsummons_br = readjson("https://br.api.pvp.net/api/lol/br/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key=".LOL_API_KEY."");
			$done_summoners_br = 0;
			if(!empty($data_bestsummons_br['entries']))
			{	
				while($done_summoners_br < count($data_bestsummons_br['entries']))
				{
					if($data_bestsummons_br['entries'][$done_summoners_br]['isHotStreak'] == null) { $br_streak = 0; } else { $br_streak = $data_bestsummons_br['entries'][$done_summoners_br]['isHotStreak']; }
					if($data_bestsummons_br['entries'][$done_summoners_br]['isVeteran'] == null) { $br_veteran = 0; } else { $br_veteran = $data_bestsummons_br['entries'][$done_summoners_br]['isVeteran']; }
					if($data_bestsummons_br['entries'][$done_summoners_br]['isInactive'] == null) { $br_inactive = 0; } else { $br_inactive = $data_bestsummons_br['entries'][$done_summoners_br]['isInactive']; }
					if($data_bestsummons_br['entries'][$done_summoners_br]['isFreshBlood'] == null) { $br_recentjoined = 0; } else { $br_recentjoined = $data_bestsummons_br['entries'][$done_summoners_br]['isFreshBlood']; }
					
					if($db->query('SELECT id FROM inv_users WHERE summoner_id="'.$data_bestsummons_br['entries'][$done_summoners_br]['playerOrTeamId'].'" AND region="br" LIMIT 1')->num_rows == 0)
					{
						@$summoner_icon_br = readjson("https://br.api.pvp.net/api/lol/br/v1.4/summoner/by-name/".str_replace(" ","%20",$data_bestsummons_br['entries'][$done_summoners_br]['playerOrTeamName'])."?api_key=".LOL_API_KEY."")[str_replace(" ","",strtolower($data_bestsummons_br['entries'][$done_summoners_br]['playerOrTeamName']))]['profileIconId'];
						if(!empty($summoner_icon_br))
						{
						$db->query('INSERT INTO inv_users (summoner_id,name,onlol_last_update,icon,region) VALUES ("'.$data_bestsummons_br['entries'][$done_summoners_br]['playerOrTeamId'].'","'.$data_bestsummons_br['entries'][$done_summoners_br]['playerOrTeamName'].'","0","'.$summoner_icon_br.'", "br")');
						}
					}
					else
					{
						$summoner_icon_br = $db->query('SELECT icon FROM inv_users WHERE summoner_id="'.$data_bestsummons_br['entries'][$done_summoners_br]['playerOrTeamId'].'" AND region="br" LIMIT 1')->fetch_row()[0];
					}
					$db->query('INSERT INTO lol_bestsummoners (region,type,player_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined,icon) VALUES  ("br","RANKED_SOLO_5x5","'.$data_bestsummons_br['entries'][$done_summoners_br]['playerOrTeamId'].'", "'.$data_bestsummons_br['entries'][$done_summoners_br]['playerOrTeamName'].'","'.$data_bestsummons_br['entries'][$done_summoners_br]['leaguePoints'].'","'.$data_bestsummons_br['entries'][$done_summoners_br]['wins'].'","'.$data_bestsummons_br['entries'][$done_summoners_br]['losses'].'","'.$br_streak.'","'.$br_veteran.'","'.$br_inactive.'","'.$br_recentjoined.'","'.$summoner_icon_br.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/solo', 'Invocador brasileño agregado: '.$data_bestsummons_br['entries'][$done_summoners_br]['playerOrTeamName']);
					$done_summoners_br++;
				}
			}
			/* Best summoners KR -> SOLOQ 5x5 */
			$data_bestsummons_kr = readjson("https://kr.api.pvp.net/api/lol/kr/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key=".LOL_API_KEY."");
			$done_summoners_kr = 0;
			if(!empty($data_bestsummons_kr['entries']))
			{	
				while($done_summoners_kr < count($data_bestsummons_kr['entries']))
				{
					if($data_bestsummons_kr['entries'][$done_summoners_kr]['isHotStreak'] == null) { $kr_streak = 0; } else { $kr_streak = $data_bestsummons_kr['entries'][$done_summoners_kr]['isHotStreak']; }
					if($data_bestsummons_kr['entries'][$done_summoners_kr]['isVeteran'] == null) { $kr_veteran = 0; } else { $kr_veteran = $data_bestsummons_kr['entries'][$done_summoners_kr]['isVeteran']; }
					if($data_bestsummons_kr['entries'][$done_summoners_kr]['isInactive'] == null) { $kr_inactive = 0; } else { $kr_inactive = $data_bestsummons_kr['entries'][$done_summoners_kr]['isInactive']; }
					if($data_bestsummons_kr['entries'][$done_summoners_kr]['isFreshBlood'] == null) { $kr_recentjoined = 0; } else { $kr_recentjoined = $data_bestsummons_kr['entries'][$done_summoners_kr]['isFreshBlood']; }
					
					if($db->query('SELECT id FROM inv_users WHERE summoner_id="'.$data_bestsummons_kr['entries'][$done_summoners_kr]['playerOrTeamId'].'" AND region="kr" LIMIT 1')->num_rows == 0)
					{
						@$summoner_icon_kr = readjson("https://kr.api.pvp.net/api/lol/kr/v1.4/summoner/by-name/".str_replace(" ","%20",$data_bestsummons_kr['entries'][$done_summoners_kr]['playerOrTeamName'])."?api_key=".LOL_API_KEY."")[str_replace(" ","",strtolower($data_bestsummons_kr['entries'][$done_summoners_kr]['playerOrTeamName']))]['profileIconId'];
						if(!empty($summoner_icon_kr))
						{
						$db->query('INSERT INTO inv_users (summoner_id.name,onlol_last_update,icon,region) VALUES ("'.$data_bestsummons_kr['entries'][$done_summoners_kr]['playerOrTeamId'].'","'.$data_bestsummons_kr['entries'][$done_summoners_kr]['playerOrTeamName'].'","0","'.$summoner_icon_kr.'", "kr")');
						}
					}
					else
					{
						$summoner_icon_kr = $db->query('SELECT icon FROM inv_users WHERE summoner_id="'.$data_bestsummons_kr['entries'][$done_summoners_kr]['playerOrTeamId'].'" AND region="kr" LIMIT 1')->fetch_row()[0];
					}
					$db->query('INSERT INTO lol_bestsummoners (region,type,player_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined,icon) VALUES ("kr","RANKED_SOLO_5x5","'.$data_bestsummons_kr['entries'][$done_summoners_kr]['playerOrTeamId'].'", "'.$data_bestsummons_kr['entries'][$done_summoners_kr]['playerOrTeamName'].'","'.$data_bestsummons_kr['entries'][$done_summoners_kr]['leaguePoints'].'","'.$data_bestsummons_kr['entries'][$done_summoners_kr]['wins'].'","'.$data_bestsummons_kr['entries'][$done_summoners_kr]['losses'].'","'.$kr_streak.'","'.$kr_veteran.'","'.$kr_inactive.'","'.$kr_recentjoined.'","'.$summoner_icon_kr.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/solo', 'Invocador coreano agregado: '.$data_bestsummons_kr['entries'][$done_summoners_kr]['playerOrTeamName']);
					$done_summoners_kr++;
				}
			}
			/* Best summoners LAS -> SOLOQ 5x5 */

			$data_bestsummons_las = readjson("https://las.api.pvp.net/api/lol/las/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key=".LOL_API_KEY."");
			$done_summoners_las = 0;
			if(!empty($data_bestsummons_las['entries']))
			{	
				while($done_summoners_las < count($data_bestsummons_las['entries']))
				{
					if($data_bestsummons_las['entries'][$done_summoners_las]['isHotStreak'] == null) { $las_streak = 0; } else { $las_streak = $data_bestsummons_las['entries'][$done_summoners_las]['isHotStreak']; }
					if($data_bestsummons_las['entries'][$done_summoners_las]['isVeteran'] == null) { $las_veteran = 0; } else { $las_veteran = $data_bestsummons_las['entries'][$done_summoners_las]['isVeteran']; }
					if($data_bestsummons_las['entries'][$done_summoners_las]['isInactive'] == null) { $las_inactive = 0; } else { $las_inactive = $data_bestsummons_las['entries'][$done_summoners_las]['isInactive']; }
					if($data_bestsummons_las['entries'][$done_summoners_las]['isFreshBlood'] == null) { $las_recentjoined = 0; } else { $las_recentjoined = $data_bestsummons_las['entries'][$done_summoners_las]['isFreshBlood']; }
					if($db->query('SELECT id FROM inv_users WHERE summoner_id="'.$data_bestsummons_las['entries'][$done_summoners_las]['playerOrTeamId'].'" AND region="las" LIMIT 1')->num_rows == 0)
					{
						@$summoner_icon_las = readjson("https://las.api.pvp.net/api/lol/las/v1.4/summoner/by-name/".str_replace(" ","%20",$data_bestsummons_las['entries'][$done_summoners_las]['playerOrTeamName'])."?api_key=".LOL_API_KEY."")[str_replace(" ","",strtolower($data_bestsummons_las['entries'][$done_summoners_las]['playerOrTeamName']))]['profileIconId'];
						if(!empty($summoner_icon_las))
						{
						$db->query('INSERT INTO inv_users (summoner_id,name,onlol_last_update,icon,region) VALUES ("'.$data_bestsummons_las['entries'][$done_summoners_las]['playerOrTeamId'].'","'.$data_bestsummons_las['entries'][$done_summoners_las]['playerOrTeamName'].'","0","'.$summoner_icon_las.'", "las")');
						}
					}
					else
					{
						$summoner_icon_las = $db->query('SELECT icon FROM inv_users WHERE summoner_id="'.$data_bestsummons_las['entries'][$done_summoners_las]['playerOrTeamId'].'" AND region="las" LIMIT 1')->fetch_row()[0];
					}
					$db->query('INSERT INTO lol_bestsummoners (region,type,player_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined,icon) VALUES ("las","RANKED_SOLO_5x5","'.$data_bestsummons_las['entries'][$done_summoners_las]['playerOrTeamId'].'","'.$data_bestsummons_las['entries'][$done_summoners_las]['playerOrTeamName'].'","'.$data_bestsummons_las['entries'][$done_summoners_las]['leaguePoints'].'","'.$data_bestsummons_las['entries'][$done_summoners_las]['wins'].'","'.$data_bestsummons_las['entries'][$done_summoners_las]['losses'].'","'.$las_streak.'","'.$las_veteran.'","'.$las_inactive.'","'.$las_recentjoined.'","'.$summoner_icon_las.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/solo', 'Invocador latinoamericano agregado: '.$data_bestsummons_las['entries'][$done_summoners_las]['playerOrTeamName']);
					$done_summoners_las++;
				}
			}

			/* Best summoners LAN -> SOLOQ 5x5 */
			$data_bestsummons_lan = readjson("https://lan.api.pvp.net/api/lol/lan/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key=".LOL_API_KEY."");
			$done_summoners_lan = 0;
			if(!empty($data_bestsummons_lan['entries']))
			{	
				while($done_summoners_lan < count($data_bestsummons_lan['entries']))
				{
					if($data_bestsummons_lan['entries'][$done_summoners_lan]['isHotStreak'] == null) { $lan_streak = 0; } else { $lan_streak = $data_bestsummons_lan['entries'][$done_summoners_lan]['isHotStreak']; }
					if($data_bestsummons_lan['entries'][$done_summoners_lan]['isVeteran'] == null) { $lan_veteran = 0; } else { $lan_veteran = $data_bestsummons_lan['entries'][$done_summoners_lan]['isVeteran']; }
					if($data_bestsummons_lan['entries'][$done_summoners_lan]['isInactive'] == null) { $lan_inactive = 0; } else { $lan_inactive = $data_bestsummons_lan['entries'][$done_summoners_lan]['isInactive']; }
					if($data_bestsummons_lan['entries'][$done_summoners_lan]['isFreshBlood'] == null) { $lan_recentjoined = 0; } else { $lan_recentjoined = $data_bestsummons_lan['entries'][$done_summoners_lan]['isFreshBlood']; }
					if($db->query('SELECT id FROM inv_users WHERE summoner_id="'.$data_bestsummons_lan['entries'][$done_summoners_lan]['playerOrTeamId'].'" AND region="lan" LIMIT 1')->num_rows == 0)
					{
						@$summoner_icon_lan = readjson("https://lan.api.pvp.net/api/lol/lan/v1.4/summoner/by-name/".str_replace(" ","%20",$data_bestsummons_lan['entries'][$done_summoners_lan]['playerOrTeamName'])."?api_key=".LOL_API_KEY."")[str_replace(" ","",strtolower($data_bestsummons_lan['entries'][$done_summoners_lan]['playerOrTeamName']))]['profileIconId'];
						if(!empty($summoner_icon_lan))
						{
						$db->query('INSERT INTO inv_users (summoner_id,name,onlol_last_update,icon,region) VALUES ("'.$data_bestsummons_lan['entries'][$done_summoners_lan]['playerOrTeamId'].'","'.$data_bestsummons_lan['entries'][$done_summoners_lan]['playerOrTeamName'].'","0","'.$summoner_icon_lan.'", "lan")');
						}
					}
					else
					{
						$summoner_icon_lan = $db->query('SELECT icon FROM inv_users WHERE summoner_id="'.$data_bestsummons_lan['entries'][$done_summoners_lan]['playerOrTeamId'].'" AND region="lan" LIMIT 1')->fetch_row()[0];
					}
					$db->query('INSERT INTO lol_bestsummoners (region,type,player_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined,icon) VALUES ("lan","RANKED_SOLO_5x5","'.$data_bestsummons_lan['entries'][$done_summoners_lan]['playerOrTeamId'].'","'.$data_bestsummons_lan['entries'][$done_summoners_lan]['playerOrTeamName'].'","'.$data_bestsummons_lan['entries'][$done_summoners_lan]['leaguePoints'].'","'.$data_bestsummons_lan['entries'][$done_summoners_lan]['wins'].'","'.$data_bestsummons_lan['entries'][$done_summoners_lan]['losses'].'","'.$lan_streak.'","'.$lan_veteran.'","'.$lan_inactive.'","'.$lan_recentjoined.'","'.$summoner_icon_lan.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/solo', 'Invocador latinoamericano agregado: '.$data_bestsummons_lan['entries'][$done_summoners_lan]['playerOrTeamName']);
					$done_summoners_lan++;
				}
			}

			/* Best summoners NA -> SOLOQ 5x5 */
			$data_bestsummons_na = readjson("https://na.api.pvp.net/api/lol/na/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key=".LOL_API_KEY."");
			$done_summoners_na = 0;
			if(!empty($data_bestsummons_na['entries']))
			{	
				while($done_summoners_na < count($data_bestsummons_na['entries']))
				{
					if($data_bestsummons_na['entries'][$done_summoners_na]['isHotStreak'] == null) { $na_streak = 0; } else { $na_streak = $data_bestsummons_na['entries'][$done_summoners_na]['isHotStreak']; }
					if($data_bestsummons_na['entries'][$done_summoners_na]['isVeteran'] == null) { $na_veteran = 0; } else { $na_veteran = $data_bestsummons_na['entries'][$done_summoners_na]['isVeteran']; }
					if($data_bestsummons_na['entries'][$done_summoners_na]['isInactive'] == null) { $na_inactive = 0; } else { $na_inactive = $data_bestsummons_na['entries'][$done_summoners_na]['isInactive']; }
					if($data_bestsummons_na['entries'][$done_summoners_na]['isFreshBlood'] == null) { $na_recentjoined = 0; } else { $na_recentjoined = $data_bestsummons_na['entries'][$done_summoners_na]['isFreshBlood']; }
					if($db->query('SELECT id FROM inv_users WHERE summoner_id="'.$data_bestsummons_na['entries'][$done_summoners_na]['playerOrTeamId'].'" AND region="na" LIMIT 1')->num_rows == 0)
					{
						@$summoner_icon_na = readjson("https://na.api.pvp.net/api/lol/na/v1.4/summoner/by-name/".str_replace(" ","%20",$data_bestsummons_na['entries'][$done_summoners_na]['playerOrTeamName'])."?api_key=".LOL_API_KEY."")[str_replace(" ","",strtolower($data_bestsummons_na['entries'][$done_summoners_na]['playerOrTeamName']))]['profileIconId'];
						if(!empty($summoner_icon_na))
						{
						$db->query('INSERT INTO inv_users (summoner_id,name,onlol_last_update,icon,region) VALUES ("'.$data_bestsummons_na['entries'][$done_summoners_na]['playerOrTeamId'].'","'.$data_bestsummons_na['entries'][$done_summoners_na]['playerOrTeamName'].'","0","'.$summoner_icon_na.'", "na")');
						}
					}
					else
					{
						$summoner_icon_na = $db->query('SELECT icon FROM inv_users WHERE summoner_id="'.$data_bestsummons_na['entries'][$done_summoners_na]['playerOrTeamId'].'" AND region="na" LIMIT 1')->fetch_row()[0];
					}
					$db->query('INSERT INTO lol_bestsummoners (region,type,player_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined,icon) VALUES ("na","RANKED_SOLO_5x5","'.$data_bestsummons_na['entries'][$done_summoners_na]['playerOrTeamId'].'","'.$data_bestsummons_na['entries'][$done_summoners_na]['playerOrTeamName'].'","'.$data_bestsummons_na['entries'][$done_summoners_na]['leaguePoints'].'","'.$data_bestsummons_na['entries'][$done_summoners_na]['wins'].'","'.$data_bestsummons_na['entries'][$done_summoners_na]['losses'].'","'.$na_streak.'","'.$na_veteran.'","'.$na_inactive.'","'.$na_recentjoined.'","'.$summoner_icon_na.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/solo', 'Invocador norteamericano agregado: '.$data_bestsummons_na['entries'][$done_summoners_na]['playerOrTeamName']);
					$done_summoners_na++;
				}
			}

			/* Best summoners OCE -> SOLOQ 5x5 */
			$data_bestsummons_oce = readjson("https://oce.api.pvp.net/api/lol/oce/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key=".LOL_API_KEY."");
			$done_summoners_oce = 0;
			if(!empty($data_bestsummons_oce['entries']))
			{	
				while($done_summoners_oce < count($data_bestsummons_oce['entries']))
				{
					if($data_bestsummons_oce['entries'][$done_summoners_oce]['isHotStreak'] == null) { $oce_streak = 0; } else { $oce_streak = $data_bestsummons_oce['entries'][$done_summoners_oce]['isHotStreak']; }
					if($data_bestsummons_oce['entries'][$done_summoners_oce]['isVeteran'] == null) { $oce_veteran = 0; } else { $oce_veteran = $data_bestsummons_oce['entries'][$done_summoners_oce]['isVeteran']; }
					if($data_bestsummons_oce['entries'][$done_summoners_oce]['isInactive'] == null) { $oce_inactive = 0; } else { $oce_inactive = $data_bestsummons_oce['entries'][$done_summoners_oce]['isInactive']; }
					if($data_bestsummons_oce['entries'][$done_summoners_oce]['isFreshBlood'] == null) { $oce_recentjoined = 0; } else { $oce_recentjoined = $data_bestsummons_oce['entries'][$done_summoners_oce]['isFreshBlood']; }
					if($db->query('SELECT id FROM inv_users WHERE summoner_id="'.$data_bestsummons_oce['entries'][$done_summoners_oce]['playerOrTeamId'].'" AND region="oce" LIMIT 1')->num_rows == 0)
					{
						@$summoner_icon_oce = readjson("https://oce.api.pvp.net/api/lol/oce/v1.4/summoner/by-name/".str_replace(" ","%20",$data_bestsummons_oce['entries'][$done_summoners_oce]['playerOrTeamName'])."?api_key=".LOL_API_KEY."")[str_replace(" ","",strtolower($data_bestsummons_oce['entries'][$done_summoners_oce]['playerOrTeamName']))]['profileIconId'];
						if(!empty($summoner_icon_oce))
						{
						$db->query('INSERT INTO inv_users (summoner_id,name,onlol_last_update,icon,region) VALUES ("'.$data_bestsummons_oce['entries'][$done_summoners_oce]['playerOrTeamId'].'","'.$data_bestsummons_oce['entries'][$done_summoners_oce]['playerOrTeamName'].'","0","'.$summoner_icon_oce.'", "oce")');
						}
					}
					else
					{
						$summoner_icon_oce = $db->query('SELECT icon FROM inv_users WHERE summoner_id="'.$data_bestsummons_oce['entries'][$done_summoners_oce]['playerOrTeamId'].'" AND region="oce" LIMIT 1')->fetch_row()[0];
					}
					$db->query('INSERT INTO lol_bestsummoners (region,type,player_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined,icon)VALUES ("oce","RANKED_SOLO_5x5","'.$data_bestsummons_oce['entries'][$done_summoners_oce]['playerOrTeamId'].'","'.$data_bestsummons_oce['entries'][$done_summoners_oce]['playerOrTeamName'].'","'.$data_bestsummons_oce['entries'][$done_summoners_oce]['leaguePoints'].'","'.$data_bestsummons_oce['entries'][$done_summoners_oce]['wins'].'","'.$data_bestsummons_oce['entries'][$done_summoners_oce]['losses'].'","'.$oce_streak.'","'.$oce_veteran.'","'.$oce_inactive.'","'.$oce_recentjoined.'","'.$summoner_icon_oce.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/solo', 'Invocador oceánico agregado: '.$data_bestsummons_oce['entries'][$done_summoners_oce]['playerOrTeamName']);
					$done_summoners_oce++;
				}
			}

			/* Best summoners RU -> SOLOQ 5x5 */
			$data_bestsummons_ru = readjson("https://ru.api.pvp.net/api/lol/ru/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key=".LOL_API_KEY."");
			$done_summoners_ru = 0;
			if(!empty($data_bestsummons_ru['entries']))
			{		
				while($done_summoners_ru < count($data_bestsummons_ru['entries']))
				{
					if($data_bestsummons_ru['entries'][$done_summoners_ru]['isHotStreak'] == null) { $ru_streak = 0; } else { $ru_streak = $data_bestsummons_ru['entries'][$done_summoners_ru]['isHotStreak']; }
					if($data_bestsummons_ru['entries'][$done_summoners_ru]['isVeteran'] == null) { $ru_veteran = 0; } else { $ru_veteran = $data_bestsummons_ru['entries'][$done_summoners_ru]['isVeteran']; }
					if($data_bestsummons_ru['entries'][$done_summoners_ru]['isInactive'] == null) { $ru_inactive = 0; } else { $ru_inactive = $data_bestsummons_ru['entries'][$done_summoners_ru]['isInactive']; }
					if($data_bestsummons_ru['entries'][$done_summoners_ru]['isFreshBlood'] == null) { $ru_recentjoined = 0; } else { $ru_recentjoined = $data_bestsummons_ru['entries'][$done_summoners_ru]['isFreshBlood']; }
					if($db->query('SELECT id FROM inv_users WHERE summoner_id="'.$data_bestsummons_ru['entries'][$done_summoners_ru]['playerOrTeamId'].'" AND region="ru" LIMIT 1')->num_rows == 0)
					{
						@$summoner_icon_ru = readjson("https://ru.api.pvp.net/api/lol/ru/v1.4/summoner/by-name/".str_replace(" ","%20",$data_bestsummons_ru['entries'][$done_summoners_ru]['playerOrTeamName'])."?api_key=".LOL_API_KEY."")[str_replace(" ","",strtolower($data_bestsummons_ru['entries'][$done_summoners_ru]['playerOrTeamName']))]['profileIconId'];
						if(!empty($summoner_icon_ru))
						{
						$db->query('INSERT INTO inv_users (summoner_id,name,onlol_last_update,icon,region) VALUES ("'.$data_bestsummons_ru['entries'][$done_summoners_ru]['playerOrTeamId'].'","'.$data_bestsummons_ru['entries'][$done_summoners_ru]['playerOrTeamName'].'","0","'.$summoner_icon_ru.'", "ru")');
						}
					}
					else
					{
						$summoner_icon_ru = $db->query('SELECT icon FROM inv_users WHERE summoner_id="'.$data_bestsummons_ru['entries'][$done_summoners_ru]['playerOrTeamId'].'" AND region="ru" LIMIT 1')->fetch_row()[0];
					}
					$db->query('INSERT INTO lol_bestsummoners (region,type,player_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined,icon) VALUES ("ru","RANKED_SOLO_5x5","'.$data_bestsummons_ru['entries'][$done_summoners_ru]['playerOrTeamId'].'","'.$data_bestsummons_ru['entries'][$done_summoners_ru]['playerOrTeamName'].'","'.$data_bestsummons_ru['entries'][$done_summoners_ru]['leaguePoints'].'","'.$data_bestsummons_ru['entries'][$done_summoners_ru]['wins'].'","'.$data_bestsummons_ru['entries'][$done_summoners_ru]['losses'].'","'.$ru_streak.'","'.$ru_veteran.'","'.$ru_inactive.'","'.$ru_recentjoined.'","'.$summoner_icon_ru.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/solo', 'Invocador rumano agregado: '.$data_bestsummons_ru['entries'][$done_summoners_ru]['playerOrTeamName']);
					$done_summoners_ru++;
				}
			}
			/* Best summoners TR -> SOLOQ 5x5 */
			$data_bestsummons_tr = readjson("https://tr.api.pvp.net/api/lol/tr/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key=".LOL_API_KEY."");
			$done_summoners_tr = 0;
			if(!empty($data_bestsummons_tr['entries']))
			{	
				while($done_summoners_tr < count($data_bestsummons_tr['entries']))
				{
					if($data_bestsummons_tr['entries'][$done_summoners_tr]['isHotStreak'] == null) { $tr_streak = 0; } else { $tr_streak = $data_bestsummons_tr['entries'][$done_summoners_tr]['isHotStreak']; }
					if($data_bestsummons_tr['entries'][$done_summoners_tr]['isVeteran'] == null) { $tr_veteran = 0; } else { $tr_veteran = $data_bestsummons_tr['entries'][$done_summoners_tr]['isVeteran']; }
					if($data_bestsummons_tr['entries'][$done_summoners_tr]['isInactive'] == null) { $tr_inactive = 0; } else { $tr_inactive = $data_bestsummons_tr['entries'][$done_summoners_tr]['isInactive']; }
					if($data_bestsummons_tr['entries'][$done_summoners_tr]['isFreshBlood'] == null) { $tr_recentjoined = 0; } else { $tr_recentjoined = $data_bestsummons_tr['entries'][$done_summoners_tr]['isFreshBlood']; }
					if($db->query('SELECT id FROM inv_users WHERE summoner_id="'.$data_bestsummons_tr['entries'][$done_summoners_tr]['playerOrTeamId'].'" AND region="tr" LIMIT 1')->num_rows == 0)
					{
						@$summoner_icon_tr = readjson("https://tr.api.pvp.net/api/lol/tr/v1.4/summoner/by-name/".str_replace(" ","%20",$data_bestsummons_tr['entries'][$done_summoners_tr]['playerOrTeamName'])."?api_key=".LOL_API_KEY."")[str_replace(" ","",strtolower($data_bestsummons_tr['entries'][$done_summoners_tr]['playerOrTeamName']))]['profileIconId'];
						if(!empty($summoner_icon_tr))
						{
						$db->query('INSERT INTO inv_users (summoner_id,name,onlol_last_update,icon,region) VALUES ("'.$data_bestsummons_tr['entries'][$done_summoners_tr]['playerOrTeamId'].'","'.$data_bestsummons_tr['entries'][$done_summoners_tr]['playerOrTeamName'].'","0","'.$summoner_icon_tr.'", "tr")');
						}
					}
					else
					{
						$summoner_icon_tr = $db->query('SELECT icon FROM inv_users WHERE summoner_id="'.$data_bestsummons_tr['entries'][$done_summoners_tr]['playerOrTeamId'].'" AND region="tr" LIMIT 1')->fetch_row()[0];
					}
					$db->query('INSERT INTO lol_bestsummoners (region,type,player_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined,icon) VALUES ("tr","RANKED_SOLO_5x5","'.$data_bestsummons_tr['entries'][$done_summoners_tr]['playerOrTeamId'].'","'.$data_bestsummons_tr['entries'][$done_summoners_tr]['playerOrTeamName'].'","'.$data_bestsummons_tr['entries'][$done_summoners_tr]['leaguePoints'].'","'.$data_bestsummons_tr['entries'][$done_summoners_tr]['wins'].'","'.$data_bestsummons_tr['entries'][$done_summoners_tr]['losses'].'","'.$tr_streak.'","'.$tr_veteran.'","'.$tr_inactive.'","'.$tr_recentjoined.'","'.$summoner_icon_tr.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/solo', 'Invocador turco agregado: '.$data_bestsummons_tr['entries'][$done_summoners_tr]['playerOrTeamName']);
					$done_summoners_tr++;
				}
			}
			not_updating('CHALLENGERLIST');
			
			/* Team challengers */
			updating('CHALLENGERTEAMLIST');
			$db->query('TRUNCATE TABLE lol_bestteams');

			/* Best summoners EUW -> TEAM 5x5 */
			$data_bestteams5x5_euw = readjson("https://euw.api.pvp.net/api/lol/euw/v2.5/league/challenger?type=RANKED_TEAM_5x5&api_key=".LOL_API_KEY."");
			$done_bestteams5x5_euw = 0;
			if(!empty($data_bestteams5x5_euw['entries']))
			{	
				while($done_bestteams5x5_euw < count($data_bestteams5x5_euw['entries']))
				{
					if($data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['isHotStreak'] == null) { $euw_streak = 0; } else { $euw_streak = $data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['isHotStreak']; }
					if($data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['isVeteran'] == null) { $euw_veteran = 0; } else { $euw_veteran = $data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['isVeteran']; }
					if($data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['isInactive'] == null) { $euw_inactive = 0; } else { $euw_inactive = $data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['isInactive']; }
					if($data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['isFreshBlood'] == null) { $euw_recentjoined = 0; } else { $euw_recentjoined = $data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("euw","RANKED_TEAM_5x5","'.$data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['playerOrTeamId'].'",
					"'.$data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['playerOrTeamName'].'","'.$data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['leaguePoints'].'",
					"'.$data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['wins'].'","'.$data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['losses'].'","'.$euw_streak.'","'.$euw_veteran.'","'.$euw_inactive.'","'.$euw_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 5x5 '.$data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['playerOrTeamName'].' al servidor europeo.');
					$done_bestteams5x5_euw++;
				}
			}
			/* Best teams 5x5 EUNE */
			$data_bestteams5x5_eune = readjson("https://eune.api.pvp.net/api/lol/eune/v2.5/league/challenger?type=RANKED_TEAM_5x5&api_key=".LOL_API_KEY."");
			$done_bestteams5x5_eune = 0;
			if(!empty($data_bestteams5x5_eune['entries']))
			{
				while($done_bestteams5x5_eune < count($data_bestteams5x5_eune['entries']))
				{
					if($data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['isHotStreak'] == null) { $eune_streak = 0; } else { $eune_streak = $data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['isHotStreak']; }
					if($data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['isVeteran'] == null) { $eune_veteran = 0; } else { $eune_veteran = $data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['isVeteran']; }
					if($data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['isInactive'] == null) { $eune_inactive = 0; } else { $eune_inactive = $data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['isInactive']; }
					if($data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['isFreshBlood'] == null) { $eune_recentjoined = 0; } else { $eune_recentjoined = $data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("eune","RANKED_TEAM_5x5","'.$data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['playerOrTeamId'].'",
					"'.$data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['playerOrTeamName'].'","'.$data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['leaguePoints'].'",
					"'.$data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['wins'].'","'.$data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['losses'].'","'.$eune_streak.'","'.$eune_veteran.'","'.$eune_inactive.'","'.$eune_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 5x5 '.$data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['playerOrTeamName'].' al servidor nórdico.');
					$done_bestteams5x5_eune++;
				}
			}
			/* Best teams 5x5 BR */
			$data_bestteams5x5_br = readjson("https://br.api.pvp.net/api/lol/br/v2.5/league/challenger?type=RANKED_TEAM_5x5&api_key=".LOL_API_KEY."");
			$done_bestteams5x5_br = 0;
			if(!empty($data_bestteams5x5_br['entries']))
			{
				while($done_bestteams5x5_br < count($data_bestteams5x5_br['entries']))
				{
					if($data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['isHotStreak'] == null) { $br_streak = 0; } else { $br_streak = $data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['isHotStreak']; }
					if($data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['isVeteran'] == null) { $br_veteran = 0; } else { $br_veteran = $data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['isVeteran']; }
					if($data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['isInactive'] == null) { $br_inactive = 0; } else { $br_inactive = $data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['isInactive']; }
					if($data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['isFreshBlood'] == null) { $br_recentjoined = 0; } else { $br_recentjoined = $data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("br","RANKED_TEAM_5x5","'.$data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['playerOrTeamId'].'",
					"'.$data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['playerOrTeamName'].'","'.$data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['leaguePoints'].'",
					"'.$data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['wins'].'","'.$data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['losses'].'","'.$br_streak.'","'.$br_veteran.'","'.$br_inactive.'","'.$br_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 5x5 '.$data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['playerOrTeamName'].' al servidor brasileño.');
					$done_bestteams5x5_br++;
				}
			}
			/* Best teams 5x5 KR */
			$data_bestteams5x5_kr = readjson("https://kr.api.pvp.net/api/lol/kr/v2.5/league/challenger?type=RANKED_TEAM_5x5&api_key=".LOL_API_KEY."");
			$done_bestteams5x5_kr = 0;
			if(!empty($data_bestteams5x5_kr['entries']))
			{
				while($done_bestteams5x5_kr < count($data_bestteams5x5_kr['entries']))
				{
					if($data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['isHotStreak'] == null) { $kr_streak = 0; } else { $kr_streak = $data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['isHotStreak']; }
					if($data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['isVeteran'] == null) { $kr_veteran = 0; } else { $kr_veteran = $data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['isVeteran']; }
					if($data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['isInactive'] == null) { $kr_inactive = 0; } else { $kr_inactive = $data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['isInactive']; }
					if($data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['isFreshBlood'] == null) { $kr_recentjoined = 0; } else { $kr_recentjoined = $data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("kr","RANKED_TEAM_5x5","'.$data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['playerOrTeamId'].'",
					"'.$data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['playerOrTeamName'].'","'.$data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['leaguePoints'].'",
					"'.$data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['wins'].'","'.$data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['losses'].'","'.$kr_streak.'","'.$kr_veteran.'","'.$kr_inactive.'","'.$kr_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 5x5 '.$data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['playerOrTeamName'].' al servidor coreano.');
					$done_bestteams5x5_kr++;
				}
			}
			/* Best teams 5x5 LAS */
			$data_bestteams5x5_las = readjson("https://las.api.pvp.net/api/lol/las/v2.5/league/challenger?type=RANKED_TEAM_5x5&api_key=".LOL_API_KEY."");
			$done_bestteams5x5_las = 0;
			if(!empty($data_bestteams5x5_las['entries']))
			{
				while($done_bestteams5x5_las < count($data_bestteams5x5_las['entries']))
				{
					if($data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['isHotStreak'] == null) { $las_streak = 0; } else { $las_streak = $data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['isHotStreak']; }
					if($data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['isVeteran'] == null) { $las_veteran = 0; } else { $las_veteran = $data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['isVeteran']; }
					if($data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['isInactive'] == null) { $las_inactive = 0; } else { $las_inactive = $data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['isInactive']; }
					if($data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['isFreshBlood'] == null) { $las_recentjoined = 0; } else { $las_recentjoined = $data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("las","RANKED_TEAM_5x5","'.$data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['playerOrTeamId'].'",
					"'.$data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['playerOrTeamName'].'","'.$data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['leaguePoints'].'",
					"'.$data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['wins'].'","'.$data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['losses'].'","'.$las_streak.'","'.$las_veteran.'","'.$las_inactive.'","'.$las_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 5x5 '.$data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['playerOrTeamName'].' al servidor latino.');
					$done_bestteams5x5_las++;
				}
			}

			/* Best teams 5x5 LAN */
			$data_bestteams5x5_lan = readjson("https://lan.api.pvp.net/api/lol/lan/v2.5/league/challenger?type=RANKED_TEAM_5x5&api_key=".LOL_API_KEY."");
			$done_bestteams5x5_lan = 0;
			if(!empty($data_bestteams5x5_lan['entries']))
			{
				while($done_bestteams5x5_lan < count($data_bestteams5x5_lan['entries']))
				{
					if($data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['isHotStreak'] == null) { $lan_streak = 0; } else { $lan_streak = $data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['isHotStreak']; }
					if($data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['isVeteran'] == null) { $lan_veteran = 0; } else { $lan_veteran = $data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['isVeteran']; }
					if($data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['isInactive'] == null) { $lan_inactive = 0; } else { $lan_inactive = $data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['isInactive']; }
					if($data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['isFreshBlood'] == null) { $lan_recentjoined = 0; } else { $lan_recentjoined = $data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("lan","RANKED_TEAM_5x5","'.$data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['playerOrTeamId'].'",
					"'.$data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['playerOrTeamName'].'","'.$data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['leaguePoints'].'",
					"'.$data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['wins'].'","'.$data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['losses'].'","'.$lan_streak.'","'.$lan_veteran.'","'.$lan_inactive.'","'.$lan_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 5x5 '.$data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['playerOrTeamName'].' al servidor latino.');
					$done_bestteams5x5_lan++;
				}
			}

			/* Best teams 5x5 NA */
			$data_bestteams5x5_na = readjson("https://na.api.pvp.net/api/lol/na/v2.5/league/challenger?type=RANKED_TEAM_5x5&api_key=".LOL_API_KEY."");
			$done_bestteams5x5_na = 0;
			if(!empty($data_bestteams5x5_na['entries']))
			{
				while($done_bestteams5x5_na < count($data_bestteams5x5_na['entries']))
				{
					if($data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['isHotStreak'] == null) { $na_streak = 0; } else { $na_streak = $data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['isHotStreak']; }
					if($data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['isVeteran'] == null) { $na_veteran = 0; } else { $na_veteran = $data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['isVeteran']; }
					if($data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['isInactive'] == null) { $na_inactive = 0; } else { $na_inactive = $data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['isInactive']; }
					if($data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['isFreshBlood'] == null) { $na_recentjoined = 0; } else { $na_recentjoined = $data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("na","RANKED_TEAM_5x5","'.$data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['playerOrTeamId'].'",
					"'.$data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['playerOrTeamName'].'","'.$data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['leaguePoints'].'",
					"'.$data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['wins'].'","'.$data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['losses'].'","'.$na_streak.'","'.$na_veteran.'","'.$na_inactive.'","'.$na_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 5x5 '.$data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['playerOrTeamName'].' al servidor norteamericano.');
					$done_bestteams5x5_na++;
				}
			}
			/* Best teams 5x5 OCE */
			$data_bestteams5x5_oce = readjson("https://oce.api.pvp.net/api/lol/oce/v2.5/league/challenger?type=RANKED_TEAM_5x5&api_key=".LOL_API_KEY."");
			$done_bestteams5x5_oce = 0;
			if(!empty($data_bestteams5x5_oce['entries']))
			{
				while($done_bestteams5x5_oce < count($data_bestteams5x5_oce['entries']))
				{
					if($data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['isHotStreak'] == null) { $oce_streak = 0; } else { $oce_streak = $data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['isHotStreak']; }
					if($data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['isVeteran'] == null) { $oce_veteran = 0; } else { $oce_veteran = $data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['isVeteran']; }
					if($data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['isInactive'] == null) { $oce_inactive = 0; } else { $oce_inactive = $data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['isInactive']; }
					if($data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['isFreshBlood'] == null) { $oce_recentjoined = 0; } else { $oce_recentjoined = $data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("oce","RANKED_TEAM_5x5","'.$data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['playerOrTeamId'].'",
					"'.$data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['playerOrTeamName'].'","'.$data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['leaguePoints'].'",
					"'.$data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['wins'].'","'.$data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['losses'].'","'.$oce_streak.'","'.$oce_veteran.'","'.$oce_inactive.'","'.$oce_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 5x5 '.$data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['playerOrTeamName'].' al servidor oceánico.');
					$done_bestteams5x5_oce++;
				}
			}

			/* Best teams 5x5 RU */
			$data_bestteams5x5_ru = readjson("https://ru.api.pvp.net/api/lol/ru/v2.5/league/challenger?type=RANKED_TEAM_5x5&api_key=".LOL_API_KEY."");
			$done_bestteams5x5_ru = 0;
			if(!empty($data_bestteams5x5_ru['entries']))
			{	
				while($done_bestteams5x5_ru < count($data_bestteams5x5_ru['entries']))
				{
					if($data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['isHotStreak'] == null) { $ru_streak = 0; } else { $ru_streak = $data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['isHotStreak']; }
					if($data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['isVeteran'] == null) { $ru_veteran = 0; } else { $ru_veteran = $data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['isVeteran']; }
					if($data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['isInactive'] == null) { $ru_inactive = 0; } else { $ru_inactive = $data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['isInactive']; }
					if($data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['isFreshBlood'] == null) { $ru_recentjoined = 0; } else { $ru_recentjoined = $data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("ru","RANKED_TEAM_5x5","'.$data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['playerOrTeamId'].'",
					"'.$data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['playerOrTeamName'].'","'.$data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['leaguePoints'].'",
					"'.$data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['wins'].'","'.$data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['losses'].'","'.$ru_streak.'","'.$ru_veteran.'","'.$ru_inactive.'","'.$ru_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 5x5 '.$data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['playerOrTeamName'].' al servidor rumano.');
					$done_bestteams5x5_ru++;
				}
			}

			/* Best teams 5x5 TR */
			$data_bestteams5x5_tr = readjson("https://tr.api.pvp.net/api/lol/tr/v2.5/league/challenger?type=RANKED_TEAM_5x5&api_key=".LOL_API_KEY."");
			$done_bestteams5x5_tr = 0;
			if(!empty($data_bestteams5x5_tr['entries']))
			{
				while($done_bestteams5x5_tr < count($data_bestteams5x5_ru['entries']))
				{
					if($data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['isHotStreak'] == null) { $tr_streak = 0; } else { $tr_streak = $data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['isHotStreak']; }
					if($data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['isVeteran'] == null) { $tr_veteran = 0; } else { $tr_veteran = $data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['isVeteran']; }
					if($data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['isInactive'] == null) { $tr_inactive = 0; } else { $tr_inactive = $data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['isInactive']; }
					if($data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['isFreshBlood'] == null) { $tr_recentjoined = 0; } else { $tr_recentjoined = $data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("tr","RANKED_TEAM_5x5","'.$data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['playerOrTeamId'].'",
					"'.$data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['playerOrTeamName'].'","'.$data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['leaguePoints'].'",
					"'.$data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['wins'].'","'.$data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['losses'].'","'.$tr_streak.'","'.$tr_veteran.'","'.$tr_inactive.'","'.$tr_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 5x5 '.$data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['playerOrTeamName'].' al servidor turco.');
					$done_bestteams5x5_tr++;
				}
			}


			/* Best teams 3x3 EUW */
			$data_bestteams3x3_euw = readjson("https://euw.api.pvp.net/api/lol/euw/v2.5/league/challenger?type=RANKED_TEAM_3x3&api_key=".LOL_API_KEY."");
			$done_bestteams3x3_euw = 0;
			if(!empty($data_bestteams3x3_euw['entries']))
			{
				while($done_bestteams3x3_euw < count($data_bestteams3x3_euw['entries']))
				{
					if($data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['isHotStreak'] == null) { $euw_streak = 0; } else { $euw_streak = $data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['isHotStreak']; }
					if($data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['isVeteran'] == null) { $euw_veteran = 0; } else { $euw_veteran = $data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['isVeteran']; }
					if($data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['isInactive'] == null) { $euw_inactive = 0; } else { $euw_inactive = $data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['isInactive']; }
					if($data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['isFreshBlood'] == null) { $euw_recentjoined = 0; } else { $euw_recentjoined = $data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("euw","RANKED_TEAM_3x3","'.$data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['playerOrTeamId'].'",
					"'.$data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['playerOrTeamName'].'","'.$data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['leaguePoints'].'",
					"'.$data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['wins'].'","'.$data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['losses'].'","'.$euw_streak.'","'.$euw_veteran.'","'.$euw_inactive.'","'.$euw_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 3x3 '.$data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['playerOrTeamName'].' al servidor europeo.');
					$done_bestteams3x3_euw++;
				}
			}

			/* Best teams 3x3 EUNE */
			$data_bestteams3x3_eune = readjson("https://eune.api.pvp.net/api/lol/eune/v2.5/league/challenger?type=RANKED_TEAM_3x3&api_key=".LOL_API_KEY."");
			$done_bestteams3x3_eune = 0;
			if(!empty($data_bestteams3x3_eune['entries']))
			{
				while($done_bestteams3x3_eune < count($data_bestteams3x3_eune['entries']))
				{
					if($data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['isHotStreak'] == null) { $eune_streak = 0; } else { $eune_streak = $data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['isHotStreak']; }
					if($data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['isVeteran'] == null) { $eune_veteran = 0; } else { $eune_veteran = $data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['isVeteran']; }
					if($data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['isInactive'] == null) { $eune_inactive = 0; } else { $eune_inactive = $data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['isInactive']; }
					if($data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['isFreshBlood'] == null) { $eune_recentjoined = 0; } else { $eune_recentjoined = $data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("eune","RANKED_TEAM_3x3","'.$data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['playerOrTeamId'].'",
					"'.$data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['playerOrTeamName'].'","'.$data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['leaguePoints'].'",
					"'.$data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['wins'].'","'.$data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['losses'].'","'.$eune_streak.'","'.$eune_veteran.'","'.$eune_inactive.'","'.$eune_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 3x3 '.$data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['playerOrTeamName'].' al servidor nórdico.');
					$done_bestteams3x3_eune++;
				}
			}

			/* Best teams 3x3 BR */
			$data_bestteams3x3_br = readjson("https://br.api.pvp.net/api/lol/br/v2.5/league/challenger?type=RANKED_TEAM_3x3&api_key=".LOL_API_KEY."");
			$done_bestteams3x3_br = 0;
			if(!empty($data_bestteams3x3_br['entries']))
			{
				while($done_bestteams3x3_br < count($data_bestteams3x3_br['entries']))
				{
					if($data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['isHotStreak'] == null) { $br_streak = 0; } else { $br_streak = $data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['isHotStreak']; }
					if($data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['isVeteran'] == null) { $br_veteran = 0; } else { $br_veteran = $data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['isVeteran']; }
					if($data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['isInactive'] == null) { $br_inactive = 0; } else { $br_inactive = $data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['isInactive']; }
					if($data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['isFreshBlood'] == null) { $br_recentjoined = 0; } else { $br_recentjoined = $data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("br","RANKED_TEAM_3x3","'.$data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['playerOrTeamId'].'",
					"'.$data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['playerOrTeamName'].'","'.$data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['leaguePoints'].'",
					"'.$data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['wins'].'","'.$data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['losses'].'","'.$br_streak.'","'.$br_veteran.'","'.$br_inactive.'","'.$br_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 3x3 '.$data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['playerOrTeamName'].' al servidor brasileño.');
					$done_bestteams3x3_br++;
				}
			}
			/* Best teams 3x3 KR */
			$data_bestteams3x3_kr = readjson("https://kr.api.pvp.net/api/lol/kr/v2.5/league/challenger?type=RANKED_TEAM_3x3&api_key=".LOL_API_KEY."");
			$done_bestteams3x3_kr = 0;
			if(!empty($data_bestteams3x3_kr['entries']))
			{
				while($done_bestteams3x3_kr < count($data_bestteams3x3_kr['entries']))
				{
					if($data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['isHotStreak'] == null) { $kr_streak = 0; } else { $kr_streak = $data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['isHotStreak']; }
					if($data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['isVeteran'] == null) { $kr_veteran = 0; } else { $kr_veteran = $data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['isVeteran']; }
					if($data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['isInactive'] == null) { $kr_inactive = 0; } else { $kr_inactive = $data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['isInactive']; }
					if($data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['isFreshBlood'] == null) { $kr_recentjoined = 0; } else { $kr_recentjoined = $data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("kr","RANKED_TEAM_3x3","'.$data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['playerOrTeamId'].'",
					"'.$data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['playerOrTeamName'].'","'.$data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['leaguePoints'].'",
					"'.$data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['wins'].'","'.$data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['losses'].'","'.$kr_streak.'","'.$kr_veteran.'","'.$kr_inactive.'","'.$kr_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 3x3 '.$data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['playerOrTeamName'].' al servidor coreano.');
					$done_bestteams3x3_kr++;
				}
			}
			/* Best teams 3x3 LAS */
			$data_bestteams3x3_las = readjson("https://las.api.pvp.net/api/lol/las/v2.5/league/challenger?type=RANKED__TEAM_3x3&api_key=".LOL_API_KEY);
			$done_bestteams3x3_las = 0;
			if(!empty($data_bestteams3x3_las['entries']))
			{
			while($done_bestteams3x3_las < count($data_bestteams3x3_las['entries']))
				{
					if($data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['isHotStreak'] == null) { $las_streak = 0; } else { $las_streak = $data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['isHotStreak']; }
					if($data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['isVeteran'] == null) { $las_veteran = 0; } else { $las_veteran = $data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['isVeteran']; }
					if($data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['isInactive'] == null) { $las_inactive = 0; } else { $las_inactive = $data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['isInactive']; }
					if($data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['isFreshBlood'] == null) { $las_recentjoined = 0; } else { $las_recentjoined = $data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("las","RANKED_TEAM_3x3","'.$data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['playerOrTeamId'].'",
					"'.$data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['playerOrTeamName'].'","'.$data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['leaguePoints'].'",
					"'.$data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['wins'].'","'.$data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['losses'].'","'.$las_streak.'","'.$las_veteran.'","'.$las_inactive.'","'.$las_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 3x3 '.$data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['playerOrTeamName'].' al servidor latino.');
					$done_bestteams3x3_las++;
				}
			}

			/* Best teams 3x3 LAN */
			$data_bestteams3x3_lan = readjson("https://lan.api.pvp.net/api/lol/lan/v2.5/league/challenger?type=RANKED_TEAM_3x3&api_key=".LOL_API_KEY."");
			$done_bestteams3x3_lan = 0;
			if(!empty($data_bestteams3x3_lan['entries']))
			{	
				while($done_bestteams3x3_lan < count($data_bestteams3x3_lan['entries']))
				{
					if($data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['isHotStreak'] == null) { $lan_streak = 0; } else { $lan_streak = $data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['isHotStreak']; }
					if($data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['isVeteran'] == null) { $lan_veteran = 0; } else { $lan_veteran = $data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['isVeteran']; }
					if($data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['isInactive'] == null) { $lan_inactive = 0; } else { $lan_inactive = $data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['isInactive']; }
					if($data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['isFreshBlood'] == null) { $lan_recentjoined = 0; } else { $lan_recentjoined = $data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("lan","RANKED_TEAM_3x3","'.$data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['playerOrTeamId'].'",
					"'.$data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['playerOrTeamName'].'","'.$data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['leaguePoints'].'",
					"'.$data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['wins'].'","'.$data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['losses'].'","'.$lan_streak.'","'.$lan_veteran.'","'.$lan_inactive.'","'.$lan_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 3x3 '.$data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['playerOrTeamName'].' al servidor latino.');
					$done_bestteams3x3_lan++;
				}
			}

			/* Best teams 3x3 NA */
			$data_bestteams3x3_na = readjson("https://na.api.pvp.net/api/lol/na/v2.5/league/challenger?type=RANKED_TEAM_3x3&api_key=".LOL_API_KEY."");
			$done_bestteams3x3_na = 0;
			if(!empty($data_bestteams3x3_na['entries']))
			{
				while($done_bestteams3x3_na < count($data_bestteams3x3_na['entries']))
				{
					if($data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['isHotStreak'] == null) { $na_streak = 0; } else { $na_streak = $data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['isHotStreak']; }
					if($data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['isVeteran'] == null) { $na_veteran = 0; } else { $na_veteran = $data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['isVeteran']; }
					if($data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['isInactive'] == null) { $na_inactive = 0; } else { $na_inactive = $data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['isInactive']; }
					if($data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['isFreshBlood'] == null) { $na_recentjoined = 0; } else { $na_recentjoined = $data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("na","RANKED_TEAM_3x3","'.$data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['playerOrTeamId'].'",
					"'.$data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['playerOrTeamName'].'","'.$data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['leaguePoints'].'",
					"'.$data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['wins'].'","'.$data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['losses'].'","'.$na_streak.'","'.$na_veteran.'","'.$na_inactive.'","'.$na_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 3x3 '.$data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['playerOrTeamName'].' al servidor norteamericano.');
					$done_bestteams3x3_na++;
				}
			}
			/* Best teams 3x3 OCE */
			$data_bestteams3x3_oce = readjson("https://oce.api.pvp.net/api/lol/oce/v2.5/league/challenger?type=RANKED_TEAM_3x3&api_key=".LOL_API_KEY."");
			$done_bestteams3x3_oce = 0;
			if(!empty($data_bestteams3x3_oce['entries']))
			{	
				while($done_bestteams3x3_oce < count($data_bestteams3x3_oce['entries']))
				{
					if($data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['isHotStreak'] == null) { $oce_streak = 0; } else { $oce_streak = $data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['isHotStreak']; }
					if($data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['isVeteran'] == null) { $oce_veteran = 0; } else { $oce_veteran = $data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['isVeteran']; }
					if($data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['isInactive'] == null) { $oce_inactive = 0; } else { $oce_inactive = $data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['isInactive']; }
					if($data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['isFreshBlood'] == null) { $oce_recentjoined = 0; } else { $oce_recentjoined = $data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("oce","RANKED_TEAM_3x3","'.$data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['playerOrTeamId'].'",
					"'.$data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['playerOrTeamName'].'","'.$data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['leaguePoints'].'",
					"'.$data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['wins'].'","'.$data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['losses'].'","'.$oce_streak.'","'.$oce_veteran.'","'.$oce_inactive.'","'.$oce_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 3x3 '.$data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['playerOrTeamName'].' al servidor oceánico.');
					$done_bestteams3x3_oce++;
				}
			}
			/* Best teams 3x3 RU */
			$data_bestteams3x3_ru = readjson("https://ru.api.pvp.net/api/lol/ru/v2.5/league/challenger?type=RANKED_TEAM_3x3&api_key=".LOL_API_KEY."");
			$done_bestteams3x3_ru = 0;
			if(!empty($data_bestteams3x3_oce['entries']))
			{	
				while($done_bestteams3x3_ru < count($data_bestteams3x3_ru['entries']))
				{
					if($data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['isHotStreak'] == null) { $ru_streak = 0; } else { $ru_streak = $data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['isHotStreak']; }
					if($data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['isVeteran'] == null) { $ru_veteran = 0; } else { $ru_veteran = $data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['isVeteran']; }
					if($data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['isInactive'] == null) { $ru_inactive = 0; } else { $ru_inactive = $data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['isInactive']; }
					if($data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['isFreshBlood'] == null) { $ru_recentjoined = 0; } else { $ru_recentjoined = $data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("ru","RANKED_TEAM_3x3","'.$data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['playerOrTeamId'].'",
					"'.$data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['playerOrTeamName'].'","'.$data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['leaguePoints'].'",
					"'.$data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['wins'].'","'.$data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['losses'].'","'.$ru_streak.'","'.$ru_veteran.'","'.$ru_inactive.'","'.$ru_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 3x3 '.$data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['playerOrTeamName'].' al servidor rumano.');
					$done_bestteams3x3_ru++;
				}
			}
			/* Best teams 3x3 TR */
			$data_bestteams3x3_tr = readjson("https://tr.api.pvp.net/api/lol/tr/v2.5/league/challenger?type=RANKED_TEAM_3x3&api_key=".LOL_API_KEY."");
			$done_bestteams3x3_tr = 0;
			if(!empty($data_bestteams3x3_tr['entries']))
			{	
				while($done_bestteams3x3_tr < count($data_bestteams3x3_ru['entries']))
				{
					if($data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['isHotStreak'] == null) { $tr_streak = 0; } else { $tr_streak = $data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['isHotStreak']; }
					if($data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['isVeteran'] == null) { $tr_veteran = 0; } else { $tr_veteran = $data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['isVeteran']; }
					if($data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['isInactive'] == null) { $tr_inactive = 0; } else { $tr_inactive = $data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['isInactive']; }
					if($data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['isFreshBlood'] == null) { $tr_recentjoined = 0; } else { $tr_recentjoined = $data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['isFreshBlood']; }
					
					$db->query('INSERT INTO lol_bestteams (region,type,team_id,name,lp,wins,losses,streak,veteran,inactive,recent_joined)
					VALUES 
					("tr","RANKED_TEAM_3x3","'.$data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['playerOrTeamId'].'",
					"'.$data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['playerOrTeamName'].'","'.$data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['leaguePoints'].'",
					"'.$data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['wins'].'","'.$data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['losses'].'","'.$tr_streak.'","'.$tr_veteran.'","'.$tr_inactive.'","'.$tr_recentjoined.'")') or die($db->error); //Insertar invocador
					$this->logger('info_challengerlist/teams', 'Agregado equipo 3x3 '.$data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['playerOrTeamName'].' al servidor turco.');
					$done_bestteams3x3_tr++;
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
	
	public function update_actual_summoners()
	{
		if($this->timer('reloadinvs') == TRUE)
		{
			global $db;
			updating('SUMMONERS');
			$summoners = $db->query('SELECT name,region FROM inv_users WHERE onlol_last_update<'.(time()-86400).'');
			while($row = $summoners->fetch_row())
			{
				$this->logger('update_summoners', 'Actualizando invocador '.$row[0].' de '.$row[1]);
				summonerupdate($row[0],$row[1]);
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
			not_updating('STATS');
		}
		else
		{
			$this->logger('wipe_stats', 'No ejecutado.');
		}
	}
	public function bestplayers_champions()
	{
		$this->coding();
		if($this->timer('bestplayers_champions') == TRUE)
		{
			global $db;
			updating('bestplayers_champions');
			$db->query('TRUNCATE TABLE inv_users_bestchampplayers');
			$db->query('TRUNCATE TABLE inv_users_morechampplayers');
			
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
							$finalchampsarray_moregamessumms[$data['winrate']][$data['skill']]['summoner_id'] = $this_user_id;
							$finalchampsarray_moregamessumms[$data['winrate']][$data['skill']]['kda'] = $data['kda'];
							$finalchampsarray_moregamessumms[$data['winrate']][$data['skill']]['matches'] = $data['matches'];
							$finalchampsarray_moregamessumms[$data['winrate']][$data['skill']]['champ_id'] = $data['champ_id'];
							krsort($finalchampsarray_moregamessumms[$data['winrate']]);
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
						$db->query('INSERT INTO inv_users_badges (badge_keyname,title,description,summoner_id) VALUES ("best_player_of_'.champidtokeyname($champ_id).'","Mejor jugador universal de '.champidtoname($champ_id).'","#'.$summonersonthischamp.' Mejor jugador del mundo de '.champidtoname($champ_id).'",'.$summoner_datainfo['summoner_id'].')') or die($db->error);
						$summonersonthischamp++;
					}
			}
			ksort($finalchampsarray_moregamessumms);
			foreach($finalchampsarray_moregamessumms as $winrate => $data)
			{
					$summonersonthischamp = 1;
					foreach($data as $skill => $summoner_datainfo)
					{
						$summoner_data = $db->query('SELECT icon,name FROM inv_users WHERE summoner_id='.$summoner_datainfo['summoner_id'].'')->fetch_row();
						/* Add to enthusiastic players of the champ */
						$db->query('INSERT INTO inv_users_morechampplayers (champ_id,rank,score,icon,name,kda,matches,winrate) VALUES ('.$summoner_datainfo['champ_id'].','.$summonersonthischamp.','.$skill.','.$summoner_data[0].',"'.$summoner_data[1].'","'.$summoner_datainfo['kda'].'",'.$summoner_datainfo['matches'].','.$winrate.')');
						/* Add enthusiastic badge to the user */
						$db->query('INSERT INTO inv_users_badges (badge_keyname,title,description,summoner_id) VALUES ("enthusiastic_player_of_'.champidtokeyname($summoner_datainfo['champ_id']).'","Entusiasta universal de '.champidtoname($summoner_datainfo['champ_id']).'","#'.$summonersonthischamp.' Entusiasta de '.champidtoname($summoner_datainfo['champ_id']).'",'.$summoner_datainfo['summoner_id'].')') or die($db->error);
						$summonersonthischamp++;
					}
			}
			
			not_updating('bestplayers_champions');
		}
		else
		{
			$this->logger('bestplayers_champions', 'No ejecutado.');
		}
	}
}