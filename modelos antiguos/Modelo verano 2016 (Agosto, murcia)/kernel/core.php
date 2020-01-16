<?php
$config = parse_ini_file('config.conf');
if($config['web.tracking.exectime'] == true)
{
	$time_start = microtime(true);
}
ini_set('max_execution_time', $config['web.maxexectime.int']);
require('class/database.php');
require('class/security.php');
require('class/onlol.php');
$langs = array('cs_CZ' => 'Czech (Czech Republic)', 'de_DE' => 'German (Germany)', 'el_GR' => 'Greek (Greece)', 'en_AU' => 'English (Australia)', 'en_GB' => 'English (United Kingdom)', 'en_PH' => 'English (Republic of the Philippines)', 'en_PL' => 'English (Poland)', 'en_SG' => 'English (Singapore)', 'en_US' => 'English (United States)', 'es_AR' => 'Spanish (Argentina)', 'es_ES' => 'Spanish (Spain)', 'es_MX' => 'Spanish (Mexico)', 'fr_FR' => 'French (France)', 'hu_HU' => 'Hungarian (Hungary)', 'id_ID' => 'Indonesian (Indonesia)', 'it_IT' => 'Italian (Italy)', 'ja_JP' => 'Japanese (Japan)', 'ko_KR' => 'Korean (Korea)', 'ms_MY' => 'Malay (Malaysia)', 'pl_PL' => 'Polish (Poland)', 'pt_BR' => 'Portuguese (Brazil)', 'ro_RO' => 'Romanian (Romania)', 'ru_RU' => 'Russian (Russia)', 'th_TH' => 'Thai (Thailand)', 'tr_TR' => 'Turkish (Turkey)', 'vn_VN' => 'Vietnamese (Viet Nam)', 'zh_CN' => 'Chinese (China)', 'zh_MY' => 'Chinese (Malaysia)', 'zh_TW' => 'Chinese (Taiwan)');
$regions = array('br', 'eune', 'euw', 'jp', 'kr', 'lan', 'las', 'na', 'oce', 'ru', 'tr');
$seasons = array('SEASON2016', 'SEASON2015', 'SEASON2014', 'SEASON3'); //SEASONS VALID, FROM LAST TO FIRST
require('class/riot.php');

/* Language detector */
$default_lang = $config['default.lang'];
if(empty($_COOKIE['onlol_lang']))
{
	require('class/lang.php');
	$user_lang = geoip::GetLang(geoip::getCountryFromIP(core::user_ip()));
	setcookie('onlol_lang',$user_lang,(time()+31557600));
}
else{
$user_lang = core::check_valid_lang($_COOKIE['onlol_lang']);
}
/* Region Detector */
if(empty($_COOKIE['onlol_region']))
{
	require_once('class/lang.php');
	$user_region = geoip::GetRegion(geoip::getCountryFromIP(core::user_ip()));
	setcookie('onlol_region',$user_region,(time()+31557600));
}
else{
	if(core::check_valid_region($_COOKIE['onlol_region']) == TRUE)
	{
		$user_region = $_COOKIE['onlol_region'];
	}
	else{
		$user_region = $config['default.region'];
	}
}

$lang = core::readlang($user_lang);
if(!isset($_COOKIE['onlol_ssums']))
{
	setcookie('onlol_ssums',json_encode(array(0 => array('name' => $lang['cookieSSumsDefaultName'],'time' => core::current_time()))),(time()+31557600));
}

require('class/template.php');