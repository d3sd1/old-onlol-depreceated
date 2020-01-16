<?php
/* REGIONS PERMITTED: NA, EUW, BR, LAN, LAS, OCE, EUNE, TR, RU, KR */
require('ip/geoiploc.php');
/* DEFAULT REGION */
$default_region = 'EUW';
$valid_langs = array('cs_CZ' => 'Czech', 'de_DE' => 'German', 'el_GR' => 'Greek', 'en_EN' => 'English', 'es_ES' => 'Spanish', 'fr_FR' => 'French', 'hu_HU' => 'Hungarian', 'id_ID' => 'Indonesian', 'it_IT' => 'Italian', 'ja_JP' => 'Japanese',
'ko_KR' => 'Korean', 'ms_MY' => 'Malay', 'pl_PL' => 'Polish', 'pt_BR' => 'Portuguese', 'ro_RO' => 'Romanian', 'ru_RU' => 'Russian', 'th_TH' => 'Thai', 'tr_TR' => 'Turkish', 'vn_VN' => 'Vietnamese', 'zh_CN' => 'Chinese');
if(empty($_COOKIE['onlol_region'])) //Si ya seleccionó una región, se la dejamos
{
	switch(GetRegion(ip()))
	{
		
		
		default:
		$region_wanted = $default_region;
	}
	if(array_key_exists(substr(@$_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2),$valid_langs))
	{
		switch(substr(@$_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2))
		{
			case 'en':
			$region_lang = 'en_EN';
			break;
			case 'es':
			$region_lang = 'es_ES';
			break;
			case 'cz':
			$region_lang = 'cs_CZ';
			break;
			case 'de':
			$region_lang = 'de_DE';
			break;
			case 'gr':
			$region_lang = 'el_GR';
			break;
			case 'fr':
			$region_lang = 'fr_FR';
			break;
			case 'hu':
			$region_lang = 'hu_HU';
			break;
			case 'id':
			$region_lang = 'id_ID';
			break;
			case 'it':
			$region_lang = 'it_IT';
			break;
			case 'ja':
			$region_lang = 'ja_JP';
			break;
			case 'kr':
			$region_lang = 'ko_kr';
			break;
			case 'my':
			$region_lang = 'ms_my';
			break;
			case 'pl':
			$region_lang = 'pl_PL';
			break;
			case 'pt':
			$region_lang = 'pt_BR';
			break;
			case 'ro':
			$region_lang = 'ro_RO';
			break;
			case 'ru':
			$region_lang = 'ru_RU';
			break;
			case 'th':
			$region_lang = 'th_TH';
			break;
			case 'tr':
			$region_lang = 'tr_TR';
			break;
			case 'vn':
			$region_lang = 'vn_VN';
			break;
			case 'zh':
			$region_lang = 'zh_CN';
			break;
			default: $region_lang = 'en_EN';
		}
	}
	else
	{
		$region_lang = 'en_EN';
	}
	if($region_wanted == 'NA' or $region_wanted == 'EUW' or $region_wanted == 'BR' or $region_wanted == 'LAN' or $region_wanted == 'LAS' or $region_wanted == 'OCE' or $region_wanted == 'EUNE' or $region_wanted == 'TR' or $region_wanted == 'RU' or $region_wanted == 'KR')
	{
		setcookie("onlol_region", strtolower($region_wanted),2147483648);
		setcookie("onlol_region_lang", $region_lang,2147483648);
	}
}