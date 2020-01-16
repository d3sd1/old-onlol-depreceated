<?php
class core{
	static $api_url_summonerprof = 'https://{{region}}.api.pvp.net/api/lol/{{region}}/v1.4/summoner/{{summoner_id}}?api_key={{riotapi}}';
	static $api_url_summonerprofname = 'https://{{region}}.api.pvp.net/api/lol/{{region}}/v1.4/summoner/by-name/{{summoner_name}}?api_key={{riotapi}}';
	static $api_url_summonerrecentmatches = 'https://{{region}}.api.pvp.net/api/lol/{{region}}/v1.3/game/by-summoner/{{summoner_id}}/recent?api_key={{riotapi}}';
	static $api_url_gamedata = 'https://{{region}}.api.pvp.net/api/lol/{{region}}/v2.2/match/{{game_id}}?includeTimeline=true&api_key={{riotapi}}';
	static $api_url_summonerprofChampMastery = 'https://{{region}}.api.pvp.net/championmastery/location/{{platform}}/player/{{summoner_id}}/champions?api_key={{riotapi}}';
	static $api_url_summonerprofLeague = 'https://{{region}}.api.pvp.net/api/lol/{{region}}/v2.5/league/by-summoner/{{summoner_id}}?api_key={{riotapi}}';
	static $api_url_summonerprofChampsStats = 'https://{{region}}.api.pvp.net/api/lol/{{region}}/v1.3/stats/by-summoner/{{summoner_id}}/ranked?season={{season}}&api_key={{riotapi}}';
	public static function extjson($url)
	{
		global $config;
		global $db;
		// Check rate-limiting queues if this is not a static call.
			if (strstr($url,'/api/lol/static-data/') != FALSE) {
				if($config['api.staticdata.rates'] == false)
				{
					$rateLimitCounts = false;
				}
				else
				{
					$rateLimitCounts = true;
				}
			}
			else
			{
				$rateLimitCounts = true;
			}
			if($config['api.rating.keytype'] == 'production')
			{
				$requestsPer10Min = 180000;
				$requestsPer10Sec = 3000;
			}
			else
			{
				$requestsPer10Min = 500;
				$requestsPer10Sec = 10;
			}
			if($db->query('SELECT id FROM web_lol_api_rating WHERE time > "'.(core::current_time()-600000).'"')->num_rows >= $requestsPer10Min)
			{
				$ratingExceed = true;
			}
			else
			{
				$ratingExceed = false;
			}
			if($db->query('SELECT id FROM web_lol_api_rating WHERE time > "'.(core::current_time()-10000).'"')->num_rows >= $requestsPer10Sec && $ratingExceed == false)
			{
				$ratingExceed = true;
			}
			elseif($ratingExceed == false)
			{
				$ratingExceed = false;
			}
			if($ratingExceed == false)
			{
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);			
				$result = curl_exec($ch);
				$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				switch($responseCode)
				{
					case 0:
					(!empty($_SERVER)) ? $callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']:null;return die(header('Location: '.$callbackUrl));
					break;
					case 200:
					$responseCodeValid = true;
					break;
					case 400:
					(!empty($_SERVER)) ? $callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']:null;return die(header('Location: '.$config['web.url'].'/maintenance?callback='.@$callbackUrl));
					break;
					case 401:
					(!empty($_SERVER)) ? $callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']:null;return die(header('Location: '.$config['web.url'].'/maintenance?callback='.@$callbackUrl));
					break;
					case 403:
					(!empty($_SERVER)) ? $callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']:null;return die(header('Location: '.$config['web.url'].'/maintenance?callback='.@$callbackUrl));
					break;
					case 404:
					return FALSE;
					break;
					case 429:
					(!empty($_SERVER)) ? $callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']:null;return die(header('Location: '.$config['web.url'].'/maintenance?callback='.@$callbackUrl));
					break;
					case 500:
					(!empty($_SERVER)) ? $callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']:null;return die(header('Location: '.$config['web.url'].'/maintenance?riotapi=busy'));
					break;
					case 503:
					(!empty($_SERVER)) ? $callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']:null;return die(header('Location: '.$config['web.url'].'/maintenance?riotapi=busy'));
					break;
				}
				curl_close($ch);
				$db->query('INSERT INTO web_lol_api_rating (url,time,ip) VALUES ("'.$url.'","'.core::current_time().'","'.core::user_ip().'")');
				if(!empty($responseCodeValid)) {
					return json_decode($result, true);	
				}
			}
			else
			{
				if(!empty($_SERVER))
				{
					$callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				}
				die(header('Location: '.$config['web.url'].'/maintenance?callback='.@$callbackUrl));
			}
	}
	public static function readlang($lang)
	{
		global $config;
		return json_decode(fread(fopen($config['web.basedir'].'/kernel/langs/'.$lang.'.lang','r'),filesize($config['web.basedir'].'/kernel/langs/'.$lang.'.lang')),true);
	}
	public static function region2platform($region)
	{
		global $regions;
		$region = strtolower($region);
		switch($region)
		{
			case 'euw':
			return 'EUW1';
			break;
			case 'br':
			return 'BR1';
			break;
			case 'eune':
			return 'EUN1';
			break;
			case 'jp':
			return 'JP1';
			break;
			case 'kr':
			return 'KR';
			break;
			case 'lan':
			return 'LA1';
			break;
			case 'las':
			return 'LA2';
			break;
			case 'na':
			return 'NA1';
			break;
			case 'oce':
			return 'OC1';
			break;
			case 'tr':
			return 'TR1';
			break;
			case 'ru':
			return 'RU';
			break;
			default: return 'EUW1'; break;
		}
	}
	public static function get_web_versions($key)
	{
		global $db;
		return $db->query('SELECT value FROM web_versions WHERE param="'.$key.'"')->fetch_row()[0];
	}
	public static function set_web_versions($key,$value)
	{
		global $db;
		return $db->query('UPDATE web_versions SET value="'.$value.'" WHERE param="'.$key.'"') or die($db->error);
	}
	public static function imgcompress($source, $destination, $quality = 90)
    {
		$info = @getimagesize($source);
		if ($info['mime'] == 'image/jpeg')
		{
			$image = @imagecreatefromjpeg($source);
		}
		elseif($info['mime'] == 'image/gif')
		{
			$image = @imagecreatefromgif($source);
		}
		elseif($info['mime'] == 'image/png')
		{
			@imagealphablending($image, false);
			@imagesavealpha($image, true);
			$image = @imagecreatefrompng($source);
		}
		@imagejpeg($image, $destination, $quality);
		return $destination;
	}
	public static function format_summonername($summoner)
	{
		return str_replace(' ','%20',strtolower($summoner));
	}
	public static function time_ms($convert)
	{
		global $lang;
		$minutes = number_format($convert/60,0);
		$seconds = $convert % 60;
		return $minutes.$lang['summonerMatchHistoryTimeMinuteShort'].' '.$seconds.$lang['summonerMatchHistoryTimeSecondShort'];
	}
	public static function check_valid_region($region)
	{
		global $regions;
		if(in_array($region,$regions))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public static function check_valid_lang($lang)
	{
		global $langs;
		global $config;
		if(array_key_exists($lang,$langs))
		{
			return $lang;
		}
		else
		{
			return $config['default.lang'];
		}
	}
	public static function current_time()
	{
		return round(microtime(true)*1000);
	}
	public static function user_ip()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"]))
        {
			$ivegotanip = true;
            return $_SERVER["HTTP_CLIENT_IP"];
        }
        elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
			$ivegotanip = true;
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
        {
			$ivegotanip = true;
            return $_SERVER["HTTP_X_FORWARDED"];
        }
        elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
        {
			$ivegotanip = true;
            return $_SERVER["HTTP_FORWARDED_FOR"];
        }
        elseif (isset($_SERVER["HTTP_FORWARDED"]))
        {
			$ivegotanip = true;
            return $_SERVER["HTTP_FORWARDED"];
        }
        elseif(empty($ivegotanip))
        {
			if($_SERVER["REMOTE_ADDR"] != '::1')
			{
            return $_SERVER["REMOTE_ADDR"];
			}
			else
			{
				return '127.0.0.1';
			}
        }
    }
	public static function readjson($location)
	{
		global $config;
		return json_decode(fread(fopen($config['web.basedir'].'/database/'.$location.'.json','r'),filesize($config['web.basedir'].'/database/'.$location.'.json')),true);
	}
	public static function cutstring($name,$length = 11)
	{
		if(strlen($name) <= $length)
		{
			return $name;
		}
		else
		{
			return substr($name, 0, $length).'...';
		}
	}
	public static function time_elapsed($ptime)
	{
		global $lang;
		$etime = time() - $ptime;

		if ($etime < 1)
		{
			return $lang['coreTimeNow'];
		}

		$a = array( 365 * 24 * 60 * 60  =>  $lang['coreTimeYear'],
					 30 * 24 * 60 * 60  =>  $lang['coreTimeMonth'],
						  24 * 60 * 60  =>  $lang['coreTimeDay'],
							   60 * 60  =>  $lang['coreTimeHour'],
									60  =>  $lang['coreTimeMinute'],
									 1  =>  $lang['coreTimeSecond']
					);
		$a_plural = array( $lang['coreTimeYear']   => $lang['coreTimeYears'],
						   $lang['coreTimeMonth']  => $lang['coreTimeMonths'],
						   $lang['coreTimeDay']    => $lang['coreTimeDays'],
						   $lang['coreTimeHour']   => $lang['coreTimeHours'],
						   $lang['coreTimeMinute'] => $lang['coreTimeMinutes'],
						   $lang['coreTimeSecond'] => $lang['coreTimeSeconds']
					);

		foreach ($a as $secs => $str)
		{
			$d = $etime / $secs;
			if ($d >= 1)
			{
				$r = round($d);
				return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . '';
			}
		}
	}
	public static function summonerMMR($lp, $league, $division)
	{
		global $db;
		switch($league.$division)
		{
			case 'CI':
			$elo = 3000;
			break;
			case 'MI':
			$elo = 2600;
			break;
			case 'DI':
			$elo = 2575;
			break;
			case 'DII':
			$elo = 2507;
			break;
			case 'DIII':
			$elo = 2437;
			break;
			case 'DIV':
			$elo = 2367;
			break;
			case 'DV':
			$elo = 2293;
			break;
			case 'PI':
			$elo = 2228;
			break;
			case 'PII':
			$elo = 2159;
			break;
			case 'PIII':
			$elo = 2090;
			break;
			case 'PIV':
			$elo = 2020;
			break;
			case 'PV':
			$elo = 1947;
			break;
			case 'GI':
			$elo = 1880;
			break;
			case 'GII':
			$elo = 1807;
			break;
			case 'GIII':
			$elo = 1737;
			break;
			case 'GIV':
			$elo = 1666;
			break;
			case 'GV':
			$elo = 1595;
			break;
			case 'SI':
			$elo = 1522;
			break;
			case 'SII':
			$elo = 1452;
			break;
			case 'SIII':
			$elo = 1382;
			break;
			case 'SIV':
			$elo = 1312;
			break;
			case 'SV':
			$elo = 1240;
			break;
			case 'BI':
			$elo = 1173;
			break;
			case 'BII':
			$elo = 1105;
			break;
			case 'BIII':
			$elo = 1034;
			break;
			case 'BIV':
			$elo = 964;
			break;
			case 'BV':
			$elo = 893;
			break;
			default:
			$elo = 1000;
			break;
		}
		$finalmmr = round($elo+($lp*70)/100);
		return $finalmmr;
	}
}