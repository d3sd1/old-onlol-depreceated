<?php
ini_set('max_execution_time', 0);
require('../kernel/core.php');
$db->query('DELETE FROM web_lol_api_rating WHERE time < "'.(core::current_time()-600000).'"');