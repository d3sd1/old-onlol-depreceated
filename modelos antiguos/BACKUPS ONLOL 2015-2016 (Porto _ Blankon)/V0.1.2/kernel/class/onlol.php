<?php
class onlol{
	public static function redirect($url,$exit = false) {
		header('Location: '.$url);
		if($exit == true)
		{
			exit();
		}
	}
	public static function parselang($lang)
	{
		if(array_key_exists($lang,$GLOBALS['langs']) == true)
		{
			return $lang;
		}
		else
		{
			return $GLOBALS['default_lang'];
			return $GLOBALS['default_lang'];
		}
	}
	public static function config($str)
	{
		global $db;
		if($db->query('SELECT id FROM config WHERE name="'.$str.'"')->num_rows == 0)
		{
			error_log('[class->config] CONSTANT_NOT_FOUND: '.$str);
		}
		else
		{
			$ret = $db->query('SELECT value FROM config WHERE name="'.$str.'" LIMIT 1')->fetch_array(); 
			return $ret['value'];
		}
	}
	public static function ip()
    {
		global $setcron;
		if($setcron == true)
		{
		global $_SERVER;
		}
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
	public static function time_elapsed_string($ptime)
	{
		$etime = time() - $ptime;

		if ($etime < 1)
		{
			return '0 segundos';
		}

		$a = array( 365 * 24 * 60 * 60  =>  lang::trans('time_elapsed_year'),
					 30 * 24 * 60 * 60  =>  lang::trans('time_elapsed_month'),
						  24 * 60 * 60  =>  lang::trans('time_elapsed_day'),
							   60 * 60  =>  lang::trans('time_elapsed_hour'),
									60  =>  lang::trans('time_elapsed_minute'),
									 1  =>  lang::trans('time_elapsed_second')
					);
		$a_plural = array( lang::trans('time_elapsed_year')   => lang::trans('time_elapsed_years'),
						   lang::trans('time_elapsed_month')  => lang::trans('time_elapsed_months'),
						   lang::trans('time_elapsed_day')    => lang::trans('time_elapsed_days'),
						   lang::trans('time_elapsed_hour')   => lang::trans('time_elapsed_hours'),
						   lang::trans('time_elapsed_minute') => lang::trans('time_elapsed_minutes'),
						   lang::trans('time_elapsed_second') => lang::trans('time_elapsed_seconds')
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
	public static function readjson($url)
	{
		onlol::setlog('api', $url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url);
		$result=curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode == 404) {
			error_log('[readjson] NOT_FOUND: '.$url);
			return 'NOT_FOUND';
		}
		
		if($httpCode == 429) {
		return 'RATE_LIMIT';
			}
			
		if($httpCode == 200) {
			return json_decode($result, true);
		}
		if($httpCode != 200 && $httpCode != 429 && $httpCode != 404)
		{
			error_log('[readjson] UNDEFINED_ERROR -> '.$httpCode.': '.$url);
		}
		curl_close($ch);
		
	}
	public static function timing($timer_name)
	{
		global $db;
		$query = $db->query('SELECT \'interval\' FROM timer WHERE name="'.$timer_name.'"')->fetch_row();
		return $query[0];
	}
   public static function setlog($filename, $msg)
   { 
	   $fd = fopen(ROOTPATH.'/kernel/logs/'.$filename.'.log', "a+");
	   fwrite($fd, '['.date('d/m/Y h:m:i').'] '.$msg."\n");
	   fclose($fd);
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
			$image = @imagecreatefrompng($source);
		}
		@imagejpeg($image, $destination, $quality);
		return $destination;
	}
	public static function check_url($url)
	{
		$headers = @get_headers($url);
		if(substr($headers[0], 9, 3)>=200 && substr($headers[0], 9, 3)<300){
			return true;
		} else {
			return false;
		}
	}
	public static function api_format_name($name)
	{
		$formated_name = str_replace(' ', null, $name);
		$formated_name = strtolower($formated_name);
		return $formated_name;
	}
	public static function addsearchlog_ordersession($x, $y){
		if($x['time'] == $y['time'])
		{
			return 0;
		}
		elseif($x['time'] > $y['time'])
		{
			return -1;
		}
		else
		{
			return 1;
		}
	}
	public static function addsearchlog($summoner_name,$region,$summoner_icon)
	{
		if(@$_SESSION['search_history'] == onlol::config('max_searchs_on_history'))
		{
			array_pop($_SESSION['search_history']);
		}
		if(@$_SESSION['search_history'] > onlol::config('max_searchs_on_history'))
		{
			$total_search = count($_SESSION['search_history']);
			$max_search = onlol::config('max_searchs_on_history');
			$this_search = 1;
			foreach($_SESSION['search_history'] as $summoner => $data)
			{
				if($this_search > $max_search)
				{
					array_pop($_SESSION['search_history']);
				}
				$this_search++;
			}
		}
		$_SESSION['search_history'][$summoner_name] = array();
		$_SESSION['search_history'][$summoner_name]['region'] = $region;
		$_SESSION['search_history'][$summoner_name]['time'] = time();
		$_SESSION['search_history'][$summoner_name]['icon'] = $summoner_icon;
		$_SESSION['search_history'][$summoner_name]['search_num'] = (int) (@$_SESSION['search_history'][$summoner_name]['search_num'] + 1);
		$max_search_value = 0;
		$max_search_summoner = null;
		foreach($_SESSION['search_history'] as $summoner_name_re => $data)
		{
			if(@$data['search_num'] > $max_search_value)
			{
				$max_search_value = $data['search_num'];
				$max_search_summoner = $summoner_name_re;
			}
		}
		if($max_search_summoner == null)
		{
			$max_search_summoner = $summoner_name;
		}
		$_SESSION['search_history']['most_search'] = $max_search_summoner;
		
		uasort($_SESSION['search_history'],array('onlol','addsearchlog_ordersession'));
	}
	public static function microtime_to_unix($str)
	{
		$epoch = substr($str, 0, -3);
		$dt = new DateTime("@$epoch");
		return $dt->getTimestamp();
	}
}