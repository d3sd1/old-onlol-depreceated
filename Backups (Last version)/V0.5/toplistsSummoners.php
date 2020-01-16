<?php
include('kernel/core.php');

try {
	$api->forceUpdate(false);
    $r = $api->challengerLeague($userRegion);
    $r = $api->masterLeague($userRegion);
    print_r($r);
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
};

?>