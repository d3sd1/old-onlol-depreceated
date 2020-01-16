<?php
$db_host = 'localhost';
$db_user = 'sczhmszf_root';
$db_pass = '?t!uQ?Nx^TJZ';
$db_base = 'sczhmszf_onlol';
$db = new mysqli($db_host,$db_user,$db_pass,$db_base);
if ($db->connect_errno) {
    exit();
}
$db ->query("SET NAMES 'utf8'"); //Encode ES types