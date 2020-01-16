<?php
require_once('../core.php');
if(empty($_POST['invid']) or empty($_POST['server'])) { exit('No data pls');}
if($db->query('SELECT id FROM inv_users WHERE summoner_id="'.$_POST['invid'].'" AND region="'.parseserver($_POST['server']).'"')->num_rows > 0)
{
setcookie('onlol_baseinv',$_POST['invid'].'/'.parseserver($_POST['server']),2147483648);
echo '<button style="cursor:default;" type="button" class="btn btn-danger" data-toggle="tooltip" data-placement="bottom" title="Este usuario ha sido establecido como principal para ir directamente a él cada vez que accedas a ONLoL."><span><i class="icon_house_alt"></i> Usuario principal</span></button>';
}