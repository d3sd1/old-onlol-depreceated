<?php
include('kernel/core.php');

try {
	$api->forceUpdate(false);
    $r = $api->summonerById(38581095,'euw');
	//$r = $api->summonerByName(array("r2f desdi","chechi33","myb anibal"),'euw');
    print_r($r);
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
};

?>