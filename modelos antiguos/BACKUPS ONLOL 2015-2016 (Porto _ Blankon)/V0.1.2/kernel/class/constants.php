<?php
$testing = true;
$lol_servers = array('br' => 'br1', 'eune' => 'eun1', 'euw' => 'euw1', 'kr' => 'kr', 'lan' => 'la1', 'las' => 'la2', 'na' => 'na1', 'oce' => 'oc1', 'tr' => 'tr1', 'ru' => 'ru'); //Region => Platform ID,  'PBE' => 'PBE1' DISABLED
$lol_seasons = array('SEASON3','SEASON2014','SEASON2015','SEASON2016');
$langs = array('es' => 'Español','en' => 'English','de' => 'Deutsche','fr' => 'Français','ja' => '日本語');
$lol_default_server = 'euw';
if(!empty($_SERVER['SERVER_NAME']))
{
	define('URL','http://'.$_SERVER['SERVER_NAME']);
	define('BASEURL', $_SERVER['SERVER_NAME']);
}
if(!empty($_SERVER['REQUEST_URI']))
{
	define('ACTUAL_URL', URL.'/'.$_SERVER['REQUEST_URI']);
}

define('LOL_API_KEY', 'a91c969a-21ce-4466-b5cc-1b5aff8b22d7');
define('ROOTPATH', '/home/sczhmszf/public_html');