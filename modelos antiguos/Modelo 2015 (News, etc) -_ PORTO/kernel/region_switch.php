<?php
require('core.php');
if(!empty($_GET['region']))
{	setcookie('onlol_region',strtoupper(lol::parseserver($_GET['onlol_region']))); 
	die(onlol::redirect(URL.'/home/?new_region='.lol::parseserver($_GET['region'])));
}
else
{
	die(onlol::redirect(URL.'?error=region_code'));
}