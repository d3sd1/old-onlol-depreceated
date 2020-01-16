<?php
/* REGIONS PERMITTED: NA, EUW, BR, LAN, LAS, OCE, EUNE, TR, RU, KR */
require('ip/geoiploc.php');
/* DEFAULT REGION */
$default_region = 'EUW';
if(!empty($_COOKIE['onlol_region'])) //Si ya seleccionó una región, se la dejamos
{
	$region_wanted = $_COOKIE['onlol_region'];
}
if(!empty($_COOKIE['onlol_region_lang'])) //Si ya seleccionó una región, se la dejamos
{
	$lang = $_COOKIE['onlol_region_lang'];
}
else //Si no tenía región, escaneamos la mejor
{
	switch(getCountryFromIP(ip(), "code"))
	{
	case 'ES':
	$region_wanted = 'EUW';
	break;	
	default:
	$region_wanted = $default_region;
	}
}
if(empty($region_wanted))
{
	$region_wanted = 'EUW';
}
if($region_wanted == 'NA' or $region_wanted == 'EUW' or $region_wanted == 'BR' or $region_wanted == 'LAN' or $region_wanted == 'LAS' or $region_wanted == 'OCE' or $region_wanted == 'EUNE' or $region_wanted == 'TR' or $region_wanted == 'RU' or $region_wanted == 'KR')
{
	setcookie("onlol_region", strtolower($region_wanted),2147483648);
}
if(substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2) == 'es' or substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2) == 'en')
{
setcookie("onlol_region_lang", substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2),2147483648);
}
else
{
setcookie("onlol_region_lang", 'es',2147483648);
}