<?php
session_start();
require('../kernel/class/database.php');
class admin{
	public static function redirect($url,$exit = false)
	{
		header('Location: '.$url);
		if($exit == true)
		{
			exit();
		}
	}
}
if(empty($dont_check_session))
{
	if(empty($_SESSION['onlol_adminpanel_logged']))
	{
		return admin::redirect('login.php'); 
	}
}