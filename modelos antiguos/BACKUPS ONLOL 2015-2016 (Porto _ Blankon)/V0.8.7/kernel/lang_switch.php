<?php
require('core.php');
if(!empty($_GET['lang']))
{
	$_SESSION['onlol_lang'] = lang::parselang($_GET['lang']);
	die(onlol::redirect(URL.'/home/?new_lang='.lang::parselang($_GET['lang'])));
}
else
{
	die(onlol::redirect(URL.'?error=lang_code'));
}