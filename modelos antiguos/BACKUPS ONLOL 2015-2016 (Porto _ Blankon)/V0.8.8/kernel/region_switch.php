<?php
require('core.php');
if(!empty($_GET['region']))
{
	$_SESSION['onlol_region'] = lol::parseserver($_GET['region']);
	die(onlol::redirect(URL.'/home/?new_region='.lol::parseserver($_GET['region'])));
}
else
{
	die(onlol::redirect(URL.'?error=region_code'));
}