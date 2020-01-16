<?php
require_once('../core.php');
if(empty($_POST['invid']) or empty($_POST['server']) or empty($_POST['name'])) { exit('No data pls');}

if(summonerinfo($_POST['invid'],'onlol_last_update')+config('profilereload_interval') < time())
			{
				summonerupdate($_POST['name'],$_POST['server']);
				echo 1;
			}
			else
			{
				echo 2;
			}