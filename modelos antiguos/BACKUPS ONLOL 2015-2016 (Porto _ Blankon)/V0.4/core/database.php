<?php
#MYSQLI CON
$db = new mysqli('localhost','sczhmszf_root','?t!uQ?Nx^TJZ','sczhmszf_onlol');
if ($db->connect_errno) {
    exit();
}
$db ->query("SET NAMES 'utf8'"); //Encode ES types