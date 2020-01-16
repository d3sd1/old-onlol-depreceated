<?php
$update_e = true;
require($_SERVER['DOCUMENT_ROOT'].'/core/core.php');
set_time_limit(0);
/* Check if next version is avaliable */
if(config('lol_patch_time') + config('lol_patch_interval') < time())
{
	$version = readjson('https://ddragon.leagueoflegends.com/api/versions.json')[0]; //Getting last version
	if(config('lol_patch') != $version)
	{
		$isthisanewpatch = true;
		setconfig('lol_patch', $version);
	}
	else
	{
		$isthisanewpatch = false;
	}
	setconfig('lol_patch_time', time());
}
else
{
	$version = config('lol_patch');
	$isthisanewpatch = false;
}

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
		$db->query('UPDATE cron SET last_time="'.time().'" WHERE task="'.$cron_name.'" LIMIT 1'); 
	}
	
	public function timer($cron_name)
	{
		global $db; 
		$ret = $db->query('SELECT only_on_new_patch,seconds_interval,last_time FROM cron WHERE task="'.$cron_name.'" LIMIT 1')->fetch_array(); 
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
		/* Put executables crons here */
		$this->wipe_images_summoner_spells();
		$this->wipe_images_champions_kit();
		$this->wipe_images_champions_square();
		$this->wipe_images_champions_loading();
		$this->wipe_images_summoner_icon();
		$this->wipe_images_champions_splash();
		$this->wipe_info_champions_skins();
		$this->wipe_info_summoners_challengers();
		$this->wipe_info_summoners_teamchallengers();
		$this->wipe_info_champions_kit();
		$this->wipe_info_champions_stats();
		$this->wipe_info_champions_rotation();
		$this->wipe_info_league_averagemmr();
		$this->update_info_skinsales();
		$this->wipe_images_game_items();
		$this->wipe_info_game_items();
		
		$this->update_stats_rankdistribution();
		$this->update_actual_summoners();
	}
	public function logger($cat,$str)
	{
		echo '['.date('H:i:s').'] ['.$cat.'] '.$str.'<br>';
	}
	public function coding()
	{
		global $db;
		$db->query('UPDATE cron SET last_time=0');
		$db->query('UPDATE config SET value=0 WHERE name="lol_patch"');
		$db->query('UPDATE config SET value=0 WHERE name="lol_patch_time"');
	}
	public function update_stats_rankdistribution()
	{
		if($this->timer('update_stats_rankdistribution') == TRUE)
		{
			global $db;
			$summoners_b = $db->query('SELECT id FROM inv_users WHERE ranked_league="B"')->num_rows;
			$summoners_s = $db->query('SELECT id FROM inv_users WHERE ranked_league="S"')->num_rows;
			$summoners_g = $db->query('SELECT id FROM inv_users WHERE ranked_league="G"')->num_rows;
			$summoners_p = $db->query('SELECT id FROM inv_users WHERE ranked_league="P"')->num_rows;
			$summoners_d = $db->query('SELECT id FROM inv_users WHERE ranked_league="D"')->num_rows;
			$summoners_m = $db->query('SELECT id FROM inv_users WHERE ranked_league="M"')->num_rows;
			$summoners_c = $db->query('SELECT id FROM inv_users WHERE ranked_league="C"')->num_rows;
			
			$db->query('UPDATE lol_stats SET value="'.$summoners_b.'" WHERE stat="rank_distribution_bronze"');
			$this->logger('stats_rankdistribution', $summoners_b.' Invocadores en bronce.');
			$db->query('UPDATE lol_stats SET value="'.$summoners_s.'" WHERE stat="rank_distribution_bronze"');
			$this->logger('stats_rankdistribution', $summoners_s.' Invocadores en plata.');
			$db->query('UPDATE lol_stats SET value="'.$summoners_g.'" WHERE stat="rank_distribution_bronze"');
			$this->logger('stats_rankdistribution', $summoners_g.' Invocadores en oro.');
			$db->query('UPDATE lol_stats SET value="'.$summoners_p.'" WHERE stat="rank_distribution_bronze"');
			$this->logger('stats_rankdistribution', $summoners_p.' Invocadores en platino.');
			$db->query('UPDATE lol_stats SET value="'.$summoners_d.'" WHERE stat="rank_distribution_bronze"');
			$this->logger('stats_rankdistribution', $summoners_d.' Invocadores en diamante.');
			$db->query('UPDATE lol_stats SET value="'.$summoners_m.'" WHERE stat="rank_distribution_bronze"');
			$this->logger('stats_rankdistribution', $summoners_m.' Invocadores en maestro.');
			$db->query('UPDATE lol_stats SET value="'.$summoners_c.'" WHERE stat="rank_distribution_bronze"');
			$this->logger('stats_rankdistribution', $summoners_c.' Invocadores en aspirante.');
		}
		else
		{
			$this->logger('stats_rankdistribution', 'No ejecutado.');
		}
	}
	public function update_actual_summoners()
	{
		if($this->timer('update_actual_summoners') == TRUE)
		{
			global $db;
			$db->query('UPDATE cron SET last_time=0 WHERE task="update_actual_summoners"');
			$db->query('UPDATE config SET value=0 WHERE name="lol_patch_time"');
		
			$summoners = $db->query('SELECT id FROM inv_users WHERE onlol_last_update<'.(time()-86400).'')->num_rows;
			$this_summoner = 1;
			while($this_summoner < $summoners)
			{
				if($db->query('SELECT id FROM inv_users WHERE id="'.$this_summoner.'"')->num_rows > 0)
				{
					$summoner = $db->query('SELECT name,region FROM inv_users WHERE id="'.$this_summoner.'"')->fetch_row();
					summonerupdate($summoner[0],$summoner[1]);
					$this->logger('update_summoners', 'Actualizando invocador '.$summoner[0].' de '.$summoner[1]);
				}
				$this_summoner++;
			}
		}
		else
		{
			$this->logger('update_summoners', 'No ejecutado.');
		}
	}
	public function update_info_skinsales()
	{
		if($this->timer('update_info_skinsales') == TRUE)
		{
			global $db;
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
		}
		else
		{
			$this->logger('images_summoner_spells', 'No ejecutado.');
		}
	}
	public function wipe_images_summoner_spells()
	{
		if($this->timer('wipe_images_summoner_spells') == TRUE)
		{
			$spell_data = readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/summoner-spell?api_key='.LOL_API_KEY);
			$done_spells = 0;
			
			while($done_spells < count($spell_data['data']))
			{
				$spell_info = array_slice($spell_data['data'], $done_spells,1);
				$spell_name = key($spell_info);
				$spell_id = $spell_info[$spell_name]['id'];
				
				copy('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$spell_name.'.png',$_SERVER['DOCUMENT_ROOT'].'/style/images/base/summoners/spells/'.$spell_id.'.png');

				$done_spells++;
				$this->logger('images_summoner_spells', 'Descargando imagen: ID -> '.$spell_id.' Nombre -> '.$spell_name);
			}
		}
		else
		{
			$this->logger('images_summoner_spells', 'No ejecutado.');
		}
	}
	public function wipe_images_game_items()
	{
		if($this->timer('wipe_images_game_items') == TRUE)
		{
			$item_data = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/item.json');
			$done_item = 0;
			
			while($done_item < count($item_data['data']))
			{
				$item_id = array_slice($item_data['data'],$done_item,1);
				$itemimage = $item_id['0']['image']['full'];
				copy('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/item/'.$itemimage.'',$_SERVER['DOCUMENT_ROOT'].'/style/images/base/game/items/'.$itemimage.'');

				$done_item++;
				$this->logger('images_game_items', 'Descargando imagen: ID -> '.$itemimage);
			}
		}
		else
		{
			$this->logger('images_game_items', 'No ejecutado.');
		}
	}
	public function wipe_info_game_items()
	{
		if($this->timer('wipe_info_game_items') == TRUE)
		{
			global $db;
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
		}
		else
		{
			$this->logger('info_game_items', 'No ejecutado.');
		}
	}
	public function wipe_images_champions_kit()
	{
		if($this->timer('wipe_images_champions_kit') == TRUE)
		{
			$dec_en = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/en_US/champion.json');
			$done_champs = 0;
				
			while($done_champs < count($dec_en['data']))
			{
				$champ_info = array_slice($dec_en['data'], $done_champs,1);
				$champ_name = key($champ_info);
				
				$this->logger('images_champions_kit', 'Descargando imágenes del kit de '.$champ_name);
				
				$are_kitarts_avaliable = true;
				$skin_num = 0;
				$kit_img = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/champion/'.$champ_name.'.json');
				// Q
						
						$q_spell = curl_init('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_name]['spells'][0]['image']['full'].'');
						curl_setopt($q_spell,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($q_spell);
						$httpCode = curl_getinfo($q_spell, CURLINFO_HTTP_CODE);
						if($httpCode == 404) {
							$are_kitarts_avaliable = false;
						}
						else
						{
								if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/'))
								{
									 mkdir($_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/', 0777, true);
									 $this->logger('images_champions_kit', 'Creando directorio '.$_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/');
								}
								
								copy('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_name]['spells'][0]['image']['full'],$_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/q.png');
								
						}
						@curl_close($q_spell);
				// W
						
						$w_spell = curl_init('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_name]['spells'][1]['image']['full'].'');
						curl_setopt($w_spell,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($w_spell);
						$httpCode = curl_getinfo($w_spell, CURLINFO_HTTP_CODE);
						if($httpCode == 404) {
							$are_kitarts_avaliable = false;
						}
						else
						{
								if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/'))
								{
									 mkdir($_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/', 0777, true);
								}
								copy('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_name]['spells'][1]['image']['full'].'',$_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/w.png');
						}
					
						@curl_close($w_spell);
				// E
						
						$e_spell = curl_init('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_name]['spells'][2]['image']['full'].'');
						curl_setopt($e_spell,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($e_spell);
						$httpCode = curl_getinfo($e_spell, CURLINFO_HTTP_CODE);
						if($httpCode == 404) {
							$are_kitarts_avaliable = false;
						}
						else
						{
								if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/'))
								{
									 mkdir($_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/', 0777, true);
								}
								copy('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_name]['spells'][2]['image']['full'].'',$_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/e.png');						}
						@curl_close($e_spell);
				// R
						$r_spell = curl_init('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_name]['spells'][3]['image']['full'].'');
						curl_setopt($r_spell,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($r_spell);
						$httpCode = curl_getinfo($r_spell, CURLINFO_HTTP_CODE);
						if($httpCode == 404) {
							$are_kitarts_avaliable = false;
						}
						else
						{
								if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/'))
								{
									 mkdir($_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/', 0777, true);
								}
								copy('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/spell/'.$kit_img['data'][$champ_name]['spells'][3]['image']['full'].'',$_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/r.png');
						}
						@curl_close($r_spell);
				// PASSIVE
						$p_spell = curl_init('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/passive/'.$kit_img['data'][$champ_name]['passive']['image']['full'].'');
						curl_setopt($p_spell,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($p_spell);
						$httpCode = curl_getinfo($p_spell, CURLINFO_HTTP_CODE);
						if($httpCode == 404) {
							$are_kitarts_avaliable = false;
						}
						else
						{
								if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/'))
								{
									 mkdir($_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/', 0777, true);
								}
								copy('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/passive/'.$kit_img['data'][$champ_name]['passive']['image']['full'].'',$_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/kit/'.$champ_name.'/passive.png');
						}
						@curl_close($p_spell);
				$done_champs++;
			}
		}
		else
		{
			$this->logger('images_champions_kit', 'No ejecutado.');
		}
	}
	public function wipe_images_champions_square()
	{
		if($this->timer('wipe_images_champions_square') == TRUE)
		{
			$dec_en = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/en_US/champion.json');
			$done_champs = 0;
				
			while($done_champs < count($dec_en['data']))
			{
				/* Base de datos de los campeones */
				$champ_info = array_slice($dec_en['data'], $done_champs,1);
				$champ_name = key($champ_info);
				copy('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/champion/'.$champ_name.'.png',$_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/little/'.$champ_name.'.png');
				$this->logger('images_champions_square', 'Descargada imagen base de '.$champ_name.'');
				$done_champs++;
			}
		}
		else
		{
			$this->logger('images_champions_square', 'No ejecutado.');
		}
	}
	public function wipe_images_champions_loading()
	{
		if($this->timer('wipe_images_champions_loading') == TRUE)
		{
			$dec_en = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/en_US/champion.json');
			$done_champs = 0;
				
			while($done_champs < count($dec_en['data']))
			{
				$champ_info = array_slice($dec_en['data'], $done_champs,1);
				$champ_name = key($champ_info);

				$are_loadingarts_avaliable = true;
				$skin_num = 0;
				while($are_loadingarts_avaliable == true)
				{
						$handle = curl_init('http://ddragon.leagueoflegends.com/cdn/img/champion/loading/'.$champ_name.'_'.$skin_num.'.jpg');
						curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
						$response = curl_exec($handle);
						$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
						if($httpCode == 404) {
							$are_loadingarts_avaliable = false;
						}
						else
						{
							copy('http://ddragon.leagueoflegends.com/cdn/img/champion/loading/'.$champ_name.'_'.$skin_num.'.jpg',$_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/loading/'.$champ_name.'_'.$skin_num.'.jpg');
							$this->logger('images_champions_loading', 'Descargada imagen loading de '.$champ_name);
						}
				$skin_num++;
						@curl_close($handle);
				}
				$done_champs++;
			}
		}
		else
		{
			$this->logger('images_champions_loading', 'No ejecutado.');
		}
	}
	public function wipe_images_summoner_icon()
	{
		if($this->timer('wipe_images_summoner_icon') == TRUE)
		{
			$icon_num = 0;
			$finished = false;
			$margin_error = 50; //Defines how many broken links has to be to end the execution
			while($finished == false)
			{
				if($icon_num <= readjson('https://global.api.pvp.net/api/lol/static-data/euw/v1.2/realm?api_key='.LOL_API_KEY)['profileiconmax'])
				{
					copy('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/profileicon/'.$icon_num.'.png',$_SERVER['DOCUMENT_ROOT'].'/style/images/base/summoners/icon/'.$icon_num.'.png');
					$this->logger('images_summoner_icons', 'Copiado icono con ID '.$icon_num.' de la versión '.$this->lol_version);
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
						if($httpCode == 404) {
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
							if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/style/images/base/summoners/icon/'.$icon_num.'.png')) //The +500 ID icons doesn't update so let's not replace it.
							{
							copy('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/img/profileicon/'.$icon_num.'.png',$_SERVER['DOCUMENT_ROOT'].'/style/images/base/summoners/icon/'.$icon_num.'.png');
							$this->logger('images_summoner_icons', 'Copiado icono con ID '.$icon_num.' de la versión '.$this->lol_version);
							}
						}
				}
					$icon_num++;
					@curl_close($handle);
			}
		}
		else
		{
			$this->logger('images_summoner_icons', 'No ejecutado.');
		}
	}
	public function wipe_images_champions_splash()
	{
		if($this->timer('wipe_images_champions_splash') == TRUE)
		{
			$dec_en = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/en_US/champion.json');
			$done_champs = 0;
				
			while($done_champs < count($dec_en['data']))
			{
				$champ_info = array_slice($dec_en['data'], $done_champs,1);
				$champ_name = key($champ_info);

				$are_splasharts_avaliable = true;
				$skin_num = 0;
				while($are_splasharts_avaliable == true)
				{
							$handle = curl_init('http://ddragon.leagueoflegends.com/cdn/img/champion/splash/'.$champ_name.'_'.$skin_num.'.jpg');
							curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
							$response = curl_exec($handle);
							$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
							if($httpCode == 404) {
								$are_splasharts_avaliable = false;
							}
							else
							{
								copy('http://ddragon.leagueoflegends.com/cdn/img/champion/splash/'.$champ_name.'_'.$skin_num.'.jpg',$_SERVER['DOCUMENT_ROOT'].'/style/images/base/champions/splash/'.$champ_name.'_'.$skin_num.'.jpg');
								$this->logger('images_champions_splash', 'Descargado splash art de '.$champ_name.' para la skin '.$skin_num);
							}
				$skin_num++;
						@curl_close($handle); //Si la sesión no estaba iniciada daba error, así que sencillamente los tapamos ya que no son errores críticos
				}
				$done_champs++;
			}

		}
		else
		{
			$this->logger('images_champions_splash', 'No ejecutado.');
		}
	}
	public function wipe_info_champions_skins()
	{
		if($this->timer('wipe_info_champions_skins') == TRUE)
		{
			global $db;
			updating('CHAMPION_SKINS');
			$db->query('TRUNCATE TABLE lol_skins');
			$dec_en = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/en_US/champion.json');
			$done_champs = 0;
				
			while($done_champs < count($dec_en['data']))
			{
				$champ_info = array_slice($dec_en['data'], $done_champs,1);
				$champ_name = key($champ_info);
				
				$champ_endata = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/en_US/champion/'.$champ_name.'.json');
				
				$champ_esdata = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/champion/'.$champ_name.'.json');
				
				$are_skins_avaliable = true;
				$skin_count = 0; //0 Defines no skin
				while($are_skins_avaliable == true)
				{
					if(isset($champ_endata['data'][$champ_name]['skins'][$skin_count]))
					{
						if($skin_count == 0)
						{
							/* Chroma fixer */
							if($champ_endata['data'][$champ_name]['skins'][$skin_count]['chromas'] == null){
								$haschroma = 'false';
							}
							else
							{
								$haschroma = $champ_endata['data'][$champ_name]['skins'][$skin_count]['chromas'];
							}
							
								$db->query('INSERT INTO lol_skins (champname,skin_num,en_skin_name,es_skin_name,has_chroma) VALUES ("'.$champ_name.'","'.$champ_endata['data'][$champ_name]['skins'][$skin_count]['num'].'","Basic skin","Aspecto básico", "'.$haschroma.'")') or die($db->error);
								$this->logger('info_champions_skins', 'Agregada la skin base de '.$champ_name.'. Chroma: '.$haschroma);
						}
						else
						{
							/* Chroma fixer */
							if($champ_endata['data'][$champ_name]['skins'][$skin_count]['chromas'] == null){
								$haschroma = 'false';
							}
							else
							{
								$haschroma = $champ_endata['data'][$champ_name]['skins'][$skin_count]['chromas'];
							}
							
								$db->query('INSERT INTO lol_skins (champname,skin_num,en_skin_name,es_skin_name,has_chroma) VALUES ("'.$champ_name.'","'.$champ_endata['data'][$champ_name]['skins'][$skin_count]['num'].'","'.$champ_endata['data'][$champ_name]['skins'][$skin_count]['name'].'","'.$champ_esdata['data'][$champ_name]['skins'][$skin_count]['name'].'", "'.$haschroma.'")') or die($db->error);
							$this->logger('info_champions_skins', 'Agregada la skin '.$champ_esdata['data'][$champ_name]['skins'][$skin_count]['name'].' de '.$champ_name.'. Chroma: '.$haschroma);
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
		}
		else
		{
			$this->logger('info_champions_skins', 'No ejecutado.');
		}
	}
	public function wipe_info_summoners_challengers()
	{
		if($this->timer('wipe_info_summoners_challengers') == TRUE)
		{
			global $db;
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
					$this->logger('info_summoners_solochallengers', 'Invocador europeo agregado: '.$data_bestsummons_euw['entries'][$done_summoners_euw]['playerOrTeamName']);
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
					$this->logger('info_summoners_solochallengers', 'Invocador nórdico agregado: '.$data_bestsummons_eune['entries'][$done_summoners_eune]['playerOrTeamName']);
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
					$this->logger('info_summoners_solochallengers', 'Invocador brasileño agregado: '.$data_bestsummons_br['entries'][$done_summoners_br]['playerOrTeamName']);
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
					$this->logger('info_summoners_solochallengers', 'Invocador coreano agregado: '.$data_bestsummons_kr['entries'][$done_summoners_kr]['playerOrTeamName']);
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
					$this->logger('info_summoners_solochallengers', 'Invocador latinoamericano agregado: '.$data_bestsummons_las['entries'][$done_summoners_las]['playerOrTeamName']);
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
					$this->logger('info_summoners_solochallengers', 'Invocador latinoamericano agregado: '.$data_bestsummons_lan['entries'][$done_summoners_lan]['playerOrTeamName']);
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
					$this->logger('info_summoners_solochallengers', 'Invocador norteamericano agregado: '.$data_bestsummons_na['entries'][$done_summoners_na]['playerOrTeamName']);
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
					$this->logger('info_summoners_solochallengers', 'Invocador oceánico agregado: '.$data_bestsummons_oce['entries'][$done_summoners_oce]['playerOrTeamName']);
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
					$this->logger('info_summoners_solochallengers', 'Invocador rumano agregado: '.$data_bestsummons_ru['entries'][$done_summoners_ru]['playerOrTeamName']);
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
					$this->logger('info_summoners_solochallengers', 'Invocador turco agregado: '.$data_bestsummons_tr['entries'][$done_summoners_tr]['playerOrTeamName']);
					$done_summoners_tr++;
				}
			}
			not_updating('CHALLENGERLIST');
		}
		else
		{
			$this->logger('info_summoners_solochallengers', 'No ejecutado.');
		}
	}
	public function wipe_info_summoners_teamchallengers()
	{
		if($this->timer('wipe_info_summoners_teamchallengers') == TRUE)
		{
			global $db;
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 5x5 '.$data_bestteams5x5_euw['entries'][$done_bestteams5x5_euw]['playerOrTeamName'].' al servidor europeo.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 5x5 '.$data_bestteams5x5_eune['entries'][$done_bestteams5x5_eune]['playerOrTeamName'].' al servidor nórdico.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 5x5 '.$data_bestteams5x5_br['entries'][$done_bestteams5x5_br]['playerOrTeamName'].' al servidor brasileño.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 5x5 '.$data_bestteams5x5_kr['entries'][$done_bestteams5x5_kr]['playerOrTeamName'].' al servidor coreano.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 5x5 '.$data_bestteams5x5_las['entries'][$done_bestteams5x5_las]['playerOrTeamName'].' al servidor latino.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 5x5 '.$data_bestteams5x5_lan['entries'][$done_bestteams5x5_lan]['playerOrTeamName'].' al servidor latino.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 5x5 '.$data_bestteams5x5_na['entries'][$done_bestteams5x5_na]['playerOrTeamName'].' al servidor norteamericano.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 5x5 '.$data_bestteams5x5_oce['entries'][$done_bestteams5x5_oce]['playerOrTeamName'].' al servidor oceánico.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 5x5 '.$data_bestteams5x5_ru['entries'][$done_bestteams5x5_ru]['playerOrTeamName'].' al servidor rumano.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 5x5 '.$data_bestteams5x5_tr['entries'][$done_bestteams5x5_tr]['playerOrTeamName'].' al servidor turco.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 3x3 '.$data_bestteams3x3_euw['entries'][$done_bestteams3x3_euw]['playerOrTeamName'].' al servidor europeo.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 3x3 '.$data_bestteams3x3_eune['entries'][$done_bestteams3x3_eune]['playerOrTeamName'].' al servidor nórdico.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 3x3 '.$data_bestteams3x3_br['entries'][$done_bestteams3x3_br]['playerOrTeamName'].' al servidor brasileño.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 3x3 '.$data_bestteams3x3_kr['entries'][$done_bestteams3x3_kr]['playerOrTeamName'].' al servidor coreano.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 3x3 '.$data_bestteams3x3_las['entries'][$done_bestteams3x3_las]['playerOrTeamName'].' al servidor latino.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 3x3 '.$data_bestteams3x3_lan['entries'][$done_bestteams3x3_lan]['playerOrTeamName'].' al servidor latino.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 3x3 '.$data_bestteams3x3_na['entries'][$done_bestteams3x3_na]['playerOrTeamName'].' al servidor norteamericano.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 3x3 '.$data_bestteams3x3_oce['entries'][$done_bestteams3x3_oce]['playerOrTeamName'].' al servidor oceánico.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 3x3 '.$data_bestteams3x3_ru['entries'][$done_bestteams3x3_ru]['playerOrTeamName'].' al servidor rumano.');
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
					$this->logger('info_summoners_teamchallengers', 'Agregado equipo 3x3 '.$data_bestteams3x3_tr['entries'][$done_bestteams3x3_tr]['playerOrTeamName'].' al servidor turco.');
					$done_bestteams3x3_tr++;
				}
			}
			not_updating('CHALLENGERTEAMLIST');
		}
		else
		{
			$this->logger('info_summoners_teamchallengers', 'No ejecutado.');
		}
	}
	public function wipe_info_champions_kit() //NEEDS AN UPDATE
	{
		if($this->timer('wipe_info_champions_kit') == TRUE)
		{
			global $db;
			updating('CHAMPIONSKIT');
			$dec_en = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/en_US/champion.json');
			$done_champs = 0;
				
			while($done_champs < count($dec_en['data']))
			{
				$champ_info = array_slice($dec_en['data'], $done_champs,1);
				$champ_name = key($champ_info);
				$this->logger('info_champions_kit', 'Agregando información del kit de '.$champ_name);
				$champ_esdata = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/champion/'.$champ_name.'.json');
				// PASSIVE
					if($db->query('SELECT id FROM lol_champs_skills WHERE champname="'.$champ_name.'"')->num_rows > 0)
					{
						$db->query('UPDATE lol_champs_skills SET passive_name="'.$champ_esdata['data'][$champ_name]['passive']['name'].'", passive_description="'.addslashes($champ_esdata['data'][$champ_name]['passive']['description']).'" WHERE champname="'.$champ_name.'"') or die($db->error);
					}
					else
					{
						$db->query('INSERT INTO lol_champs_skills (passive_name,passive_description,champname'.$p_colum.') VALUES ("'.$champ_esdata['data'][$champ_name]['passive']['name'].'","'.addslashes($champ_esdata['data'][$champ_name]['passive']['description']).'","'.$champ_name.'"'.$p_cooldowns.')') or die($db->error);
					}
					
				// Q
					if($db->query('SELECT id FROM lol_champs_skills WHERE champname="'.$champ_name.'"')->num_rows > 0)
					{
						if(isset($champ_esdata['data'][$champ_name]['spells']['0']['cooldown']))
						{
							$q_cooldowns = ', q_cooldown="'.implode('/',$champ_esdata['data'][$champ_name]['spells']['0']['cooldown']).'"';
						}
						$db->query('UPDATE lol_champs_skills SET q_name="'.$champ_esdata['data'][$champ_name]['spells']['0']['name'].'", q_description="'.addslashes($champ_esdata['data'][$champ_name]['spells']['0']['description']).'"'.$q_cooldowns.' WHERE champname="'.$champ_name.'"') or die($db->error);
					}
					else
					{
						if(isset($champ_esdata['data'][$champ_name]['spells']['0']['cooldown']))
						{
							$q_colum = ',q_cooldown';
							$q_cooldowns = ', "'.implode('/',$champ_esdata['data'][$champ_name]['spells']['0']['cooldown']).'"';
						}
						$db->query('INSERT INTO lol_champs_skills (q_name,q_description,champname'.$q_colum.') VALUES ("'.$champ_esdata['data'][$champ_name]['spells']['0']['name'].'","'.addslashes($champ_esdata['data'][$champ_name]['spells']['0']['description']).'","'.$champ_name.'"'.$q_cooldowns.')') or die($db->error);
					}
					
				// W
					if($db->query('SELECT id FROM lol_champs_skills WHERE champname="'.$champ_name.'"')->num_rows > 0)
					{
						if(isset($champ_esdata['data'][$champ_name]['spells']['1']['cooldown']))
						{
							$w_cooldowns = ', w_cooldown="'.implode('/',$champ_esdata['data'][$champ_name]['spells']['1']['cooldown']).'"';
						}
						$db->query('UPDATE lol_champs_skills SET w_name="'.$champ_esdata['data'][$champ_name]['spells']['1']['name'].'", w_description="'.addslashes($champ_esdata['data'][$champ_name]['spells']['1']['description']).'"'.$w_cooldowns.' WHERE champname="'.$champ_name.'"') or die($db->error);
					}
					else
					{
						if(isset($champ_esdata['data'][$champ_name]['spells']['1']['cooldown']))
						{
							$w_colum = ',w_cooldown';
							$w_cooldowns = ', "'.implode('/',$champ_esdata['data'][$champ_name]['spells']['1']['cooldown']).'"';
						}
						$db->query('INSERT INTO lol_champs_skills (w_name,w_description,champname'.$w_colum.') VALUES ("'.$champ_esdata['data'][$champ_name]['spells']['1']['name'].'","'.addslashes($champ_esdata['data'][$champ_name]['spells']['1']['description']).'","'.$champ_name.'"'.$w_cooldowns.')') or die($db->error);
					}
					
				// E
					if($db->query('SELECT id FROM lol_champs_skills WHERE champname="'.$champ_name.'"')->num_rows > 0)
					{
						if(isset($champ_esdata['data'][$champ_name]['spells']['2']['cooldown']))
						{
							$e_cooldowns = ', e_cooldown="'.implode('/',$champ_esdata['data'][$champ_name]['spells']['2']['cooldown']).'"';
						}
						$db->query('UPDATE lol_champs_skills SET e_name="'.$champ_esdata['data'][$champ_name]['spells']['2']['name'].'", e_description="'.addslashes($champ_esdata['data'][$champ_name]['spells']['2']['description']).'"'.$e_cooldowns.' WHERE champname="'.$champ_name.'"') or die($db->error);
					}
					else
					{
						if(isset($champ_esdata['data'][$champ_name]['spells']['2']['cooldown']))
						{
							$e_colum = ',e_cooldown';
							$e_cooldowns = ', "'.implode('/',$champ_esdata['data'][$champ_name]['spells']['2']['cooldown']).'"';
						}
						$db->query('INSERT INTO lol_champs_skills (e_name,e_description,champname'.$e_colum.') VALUES ("'.$champ_esdata['data'][$champ_name]['spells']['2']['name'].'","'.addslashes($champ_esdata['data'][$champ_name]['spells']['2']['description']).'","'.$champ_name.'"'.$e_cooldowns.')') or die($db->error);
					}
					
				// R
					if($db->query('SELECT id FROM lol_champs_skills WHERE champname="'.$champ_name.'"')->num_rows > 0)
					{
						if(isset($champ_esdata['data'][$champ_name]['spells']['3']['cooldown']))
						{
							$r_cooldowns = ', r_cooldown="'.implode('/',$champ_esdata['data'][$champ_name]['spells']['3']['cooldown']).'"';
						}
						$db->query('UPDATE lol_champs_skills SET r_name="'.$champ_esdata['data'][$champ_name]['spells']['3']['name'].'", r_description="'.addslashes($champ_esdata['data'][$champ_name]['spells']['3']['description']).'"'.$r_cooldowns.' WHERE champname="'.$champ_name.'"') or die($db->error);
					}
					else
					{
						if(isset($champ_esdata['data'][$champ_name]['spells']['3']['cooldown']))
						{
							$r_colum = ',r_cooldown';
							$r_cooldowns = ', "'.implode('/',$champ_esdata['data'][$champ_name]['spells']['3']['cooldown']).'"';
						}
						$db->query('INSERT INTO lol_champs_skills (r_name,r_description,champname'.$r_colum.') VALUES ("'.$champ_esdata['data'][$champ_name]['spells']['3']['name'].'","'.addslashes($champ_esdata['data'][$champ_name]['spells']['3']['description']).'","'.$champ_name.'"'.$r_cooldowns.')') or die($db->error);
					}
				$done_champs++;
			}
			not_updating('CHAMPIONSKIT');
		}
		else
		{
			$this->logger('info_champions_kit', 'No ejecutado.');
		}
	}
	public function wipe_info_champions_stats()
	{
		if($this->timer('wipe_info_champions_stats') == TRUE)
		{
			global $db;
			updating('CHAMPIONSSTATS');
			$db->query('TRUNCATE TABLE lol_champs');
			$dec_en = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/champion.json');
			$done_champs = 0;
				
			while($done_champs < count($dec_en['data']))
			{
				
				$champ_info = array_slice($dec_en['data'], $done_champs,1);
				$champ_name = key($champ_info);
				$this->logger('info_champions_stats', 'Agregando información base de '.$champ_name);
				if(!empty($champ_info[$champ_name]['tags']['1'])) { $secondbar_c = 'role_2,'; $secondbar_v = ',"'.$champ_info[$champ_name]['tags']['1'].'"'; } else { $secondbar_c = null; $secondbar_v = null; }
				
				$db->query('INSERT INTO lol_champs (champ_id,champname,es_title,info_attack,
				info_defense,info_magic,info_difficulty,
				img_little,role_1,'.$secondbar_c.'
				kit_bar,base_hp,scale_hp_lvl,
				base_bar,scale_bar_lvl,movspeed,
				base_armor,scale_armor_lvl,base_spellblock,
				scale_spellblock_lvl,attackrange,base_hpregen,
				scale_hpregen_lvl,base_manareg,scale_manareg_lvl,
				base_crit,scale_crit_lvl,base_ad,
				scale_ad_lvl,offset_as,scale_as_lvl) VALUES (
				"'.$champ_info[$champ_name]['key'].'","'.$champ_name.'","'.$champ_info[$champ_name]['title'].'",
				"'.$champ_info[$champ_name]['info']['attack'].'","'.$champ_info[$champ_name]['info']['defense'].'","'.$champ_info[$champ_name]['info']['magic'].'","'.$champ_info[$champ_name]['info']['difficulty'].'","'.$champ_info[$champ_name]['image']['full'].'","'.strtolower($champ_info[$champ_name]['tags']['0']).'"'.strtolower($secondbar_v).',"'.strtolower($champ_info[$champ_name]['partype']).'","'.$champ_info[$champ_name]['stats']['hp'].'","'.$champ_info[$champ_name]['stats']['hpperlevel'].'","'.$champ_info[$champ_name]['stats']['mp'].'","'.$champ_info[$champ_name]['stats']['mpperlevel'].'","'.$champ_info[$champ_name]['stats']['movespeed'].'","'.$champ_info[$champ_name]['stats']['armor'].'","'.$champ_info[$champ_name]['stats']['armorperlevel'].'","'.$champ_info[$champ_name]['stats']['spellblock'].'","'.$champ_info[$champ_name]['stats']['spellblockperlevel'].'","'.$champ_info[$champ_name]['stats']['attackrange'].'","'.$champ_info[$champ_name]['stats']['hpregen'].'","'.$champ_info[$champ_name]['stats']['hpregenperlevel'].'","'.$champ_info[$champ_name]['stats']['mpregen'].'","'.$champ_info[$champ_name]['stats']['mpregenperlevel'].'","'.$champ_info[$champ_name]['stats']['crit'].'","'.$champ_info[$champ_name]['stats']['critperlevel'].'","'.$champ_info[$champ_name]['stats']['attackdamage'].'","'.$champ_info[$champ_name]['stats']['attackdamageperlevel'].'","'.$champ_info[$champ_name]['stats']['attackspeedoffset'].'","'.$champ_info[$champ_name]['stats']['attackspeedperlevel'].'")') or die($db->error); //Si el valor es dintinto, actualizar información.
				/* Lore */
				$lore_es = readjson('http://ddragon.leagueoflegends.com/cdn/'.$this->lol_version.'/data/es_ES/champion/'.$champ_name.'.json');

				$db->query('UPDATE lol_champs SET es_lore="'.$lore_es['data'][$champ_name]['lore'].'" WHERE champname="'.$champ_name.'"') or die($db->error);
				$done_champs++;
			}
			not_updating('CHAMPIONSSTATS');
		}
		else
		{
			$this->logger('info_champions_stats', 'No ejecutado.');
		}
	}
	public function wipe_info_champions_rotation()
	{
		if($this->timer('wipe_info_champions_rotation') == TRUE)
		{
			global $db;
			updating('CHAMPIONSROTATION');
			$db->query('UPDATE lol_champs SET is_rotation=0');
			$ftp_weekly = readjson("https://euw.api.pvp.net/api/lol/euw/v1.2/champion?freeToPlay=true&api_key=".LOL_API_KEY);
			$actual_champ = 0;
			while($actual_champ < count($ftp_weekly['champions']))
			{
				$this->logger('info_champions_rotation', 'El campeón '.champidtoname($ftp_weekly['champions'][$actual_champ]['id']).' está en rotación.');
				$db->query('UPDATE lol_champs SET is_rotation="1" WHERE champ_id="'.$ftp_weekly['champions'][$actual_champ]['id'].'"');
				$actual_champ++;
			}
			not_updating('CHAMPIONSROTATION');
		}
		else
		{
			$this->logger('info_champions_rotation', 'No ejecutado.');
		}
	}
	public function wipe_info_league_averagemmr()
	{
		if($this->timer('wipe_info_league_averagemmr') == TRUE)
		{
			$this->logger('info_summoners_mmr_leagueaverage', 'Actualizando mmr de ligas.');
			global $db;
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
}