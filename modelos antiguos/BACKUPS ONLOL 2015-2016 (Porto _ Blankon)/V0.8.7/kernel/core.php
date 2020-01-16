<?php
/* Ini */
date_default_timezone_set('Europe/Madrid');
ini_set("session.cookie_lifetime","604800"); // 1 Week
ini_set("session.gc_maxlifetime","604800"); // 1 Week
ini_set('display_errors','on');
session_start();
/* Riot class */
require('security.php');
/* Constants */
require('class/constants.php');
/* Internal variables */
require('class/database.php');
/* ONLoL Class */
require('class/onlol.php');
/* Templates */
require('class/template.php');
/* LoL class */
require('class/lol.php');
/* Language detector */
$default_lang = 'es';
if(empty($_SESSION['onlol_lang']))
{
	if(!empty($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
	{
		$real_nav_lang = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2);
	}
	else
	{
		$real_nav_lang = $default_lang;
	}
	$_SESSION['onlol_lang'] = $real_nav_lang;
}
require('class/lang.php');
require('langs/'.lang::parselang($_SESSION['onlol_lang']).'.php');

/* Mobile detector */
if(empty($_SESSION['onlol_is_mobile']))
{
	include('class/mobile.php');
	$detect = new Mobile_Detect(); 
    if ($detect->isMobile() == true or $detect->isTablet() == true)
	{
		$_SESSION['onlol_is_mobile'] = true;
		die(lang::trans('php_error_mobile'));
    } 
	else
	{
		$_SESSION['onlol_is_mobile'] = false;
    } 
}
else
{
	if($_SESSION['onlol_is_mobile'] == true)
	{
		die(lang::trans('php_error_mobile'));
	}
}
/* Region detector */
if(empty($_SESSION['onlol_region']))
{
	require('class/geoiploc.php');
	$_SESSION['onlol_region'] = strtoupper(geoip::GetRegion(onlol::ip()));
}
else
{
	$_SESSION['onlol_region'] = strtoupper(lol::parseserver($_SESSION['onlol_region']));
}
/* Riot class */
require('class/riot.php');