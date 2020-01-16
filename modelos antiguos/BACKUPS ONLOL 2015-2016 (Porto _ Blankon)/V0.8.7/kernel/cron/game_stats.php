<?php
require('../class/constants.php');
require('../class/database.php');
require('../class/lol.php');
require('../class/onlol.php');
require('../class/riot.php');
/* Side stats */
$db->query('UPDATE lol_stats SET value="'.$db->query('SELECT id FROM lol_matches WHERE winner="200"')->num_rows.'" WHERE stat="red_side_wins"') or die($db->error);
$db->query('UPDATE lol_stats SET value="'.$db->query('SELECT id FROM lol_matches WHERE winner="100"')->num_rows.'" WHERE stat="blue_side_wins"') or die($db->error);