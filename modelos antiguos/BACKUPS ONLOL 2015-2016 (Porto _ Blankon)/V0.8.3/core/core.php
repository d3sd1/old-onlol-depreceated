<?php
date_default_timezone_set('Europe/Madrid');
ini_set("session.cookie_lifetime","604800"); // 1 Week
ini_set("session.gc_maxlifetime","604800"); // 1 Week
ini_set('display_errors','on');
/* Incluir los filtros de seguridad */
require('security.php');
/* Conectar a la base de datos */
require('database.php');
/* Incluir el corrector de regiones */
require('region.php');
/* Incluir las clases */
require('class.php');
/* Incluir la caché */
require('cache.php');
if(config('updating') == 'true' && empty($update_e))
{
	exit('Estamos realizando mejoras en el sistema, disculpen las molestias');
}
if(config('coding') == 'true')
{
	$timer = explode(" ",microtime());
	$starttime = $timer[0] + $timer[1];
}
/* Include lang */
if(!empty($_COOKIE['onlol_region_lang']))
{
	if(array_key_exists($_COOKIE['onlol_region_lang'],$valid_langs))
	{
		require('langs/'.$_COOKIE['onlol_region_lang'].'.php');
	}
	else
	{
		require('langs/en_EN.php');
	}
}
else
{
	require('langs/en_EN.php');
}