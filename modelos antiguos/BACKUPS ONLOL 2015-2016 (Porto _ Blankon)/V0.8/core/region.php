<?php
/* REGIONS PERMITTED: NA, EUW, BR, LAN, LAS, OCE, EUNE, TR, RU, KR */
require('ip/geoiploc.php');
/* DEFAULT REGION */
$default_region = 'EUW';
$valid_langs = array('cs_CZ' => 'Czech', 'de_DE' => 'German', 'el_GR' => 'Greek', 'en_US' => 'English', 'es_ES' => 'Spanish', 'fr_FR' => 'French', 'hu_HU' => 'Hungarian', 'id_ID' => 'Indonesian', 'it_IT' => 'Italian', 'ja_JP' => 'Japanese',
'ko_KR' => 'Korean', 'ms_MY' => 'Malay', 'pl_PL' => 'Polish', 'pt_BR' => 'Portuguese', 'ro_RO' => 'Romanian', 'ru_RU' => 'Russian', 'th_TH' => 'Thai', 'tr_TR' => 'Turkish', 'vn_VN' => 'Vietnamese', 'zh_CN' => 'Chinese');
if(!empty($_COOKIE['onlol_region'])) //Si ya seleccionó una región, se la dejamos
{
	$region_wanted = $_COOKIE['onlol_region'];
}
else //Si no tenía región, escaneamos la mejor
{
	switch(getCountryFromIP(ip(), "code"))
	{
		/* EUW */
		case 'ES':
		$region_wanted = 'EUW';
		$region_lang = 'es_ES';
		break;
		case 'PT':
		$region_wanted = 'EUW';
		$region_lang = 'pt_BR';
		break;
		case 'FR':
		$region_wanted = 'EUW';
		$region_lang = 'fr_FR';
		break;
		case 'CH':
		$region_wanted = 'EUW';
		$region_lang = 'de_DE';
		break;
		case 'IT':
		$region_wanted = 'EUW';
		$region_lang = 'it_IT';
		break;
		case 'AT':
		$region_wanted = 'EUW';
		$region_lang = 'de_DE';
		break;
		case 'DE':
		$region_wanted = 'EUW';
		$region_lang = 'de_DE';
		break;
		case 'LU':
		$region_wanted = 'EUW';
		$region_lang = 'fr_FR';
		break;
		case 'BE':
		$region_wanted = 'EUW';
		$region_lang = 'fr_FR';
		break;
		case 'NL':
		$region_wanted = 'EUW';
		$region_lang = 'de_DE';
		break;
		case 'GB':
		$region_wanted = 'EUW';
		$region_lang = 'en_US';
		break;
		case 'IE':
		$region_wanted = 'EUW';
		$region_lang = 'en_US';
		break;
		
		default:
		$region_wanted = $default_region;
	}
}

if(!empty($_COOKIE['onlol_region_lang'])) //Si ya seleccionó una región, se la dejamos
{
	$lang = $_COOKIE['onlol_region_lang'];
}

if(empty($region_wanted))
{
	$region_wanted = 'EUW';
}

if($region_wanted == 'NA' or $region_wanted == 'EUW' or $region_wanted == 'BR' or $region_wanted == 'LAN' or $region_wanted == 'LAS' or $region_wanted == 'OCE' or $region_wanted == 'EUNE' or $region_wanted == 'TR' or $region_wanted == 'RU' or $region_wanted == 'KR')
{
	setcookie("onlol_region", strtolower($region_wanted),2147483648);
}

setcookie("onlol_region_lang", substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2),2147483648);