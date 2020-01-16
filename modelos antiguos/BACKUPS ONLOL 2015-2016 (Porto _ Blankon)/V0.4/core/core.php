<?php
date_default_timezone_set('Europe/Madrid');
ini_set("session.cookie_lifetime","604800"); // 1 Week
ini_set("session.gc_maxlifetime","604800"); // 1 Week
ini_set('display_errors','on');
define('LOL_API_KEY', '1375edea-27ad-4f0a-80b0-e38402eaa69e');
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