<?php
require('kernel/core.php');
if(!empty(@$_GET['key']))
{
	if(in_array($_GET['key'],explode(',',$config['langs'])) == TRUE && $_GET['key'] != null)
	{
		$_SESSION['userlang'] = $_GET['key'];
		header('Location: '.$config['web.url']);
	}
	else
	{
		header('Location: '.$config['web.url']);
	}
}
else
{
	header('Location: '.$config['web.url']);
}
?>