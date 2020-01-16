<?php
session_start();
$config = parse_ini_file('config.conf');
require('class/riot.php');
if(empty($_SESSION['userlang']))
{
	(array_key_exists('HTTP_ACCEPT_LANGUAGE',$_SERVER)) ? $navLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2):$navLang=null;
	if(in_array($navLang,explode(',',$config['langs'])) == TRUE && $navLang != null)
	{
		$userLang = substr($navLang,0,2);
		$_SESSION['userlang'] = $userLang;
	}
	else
	{
		$userLang = $config['default.lang'];
		$_SESSION['userlang'] = $config['default.lang'];
	}
}
else
{
	if(in_array($_SESSION['userlang'],explode(',',$config['langs'])) == TRUE)
	{
		$userLang = substr($_SESSION['userlang'],0,2);
	}
	else
	{
		$userLang = $config['default.lang'];
	};
}
$lang = json_decode(utf8_encode(file_get_contents($config['web.basedir'].'/kernel/langs/'.$userLang.'.json')),true);