<?php
require('../class/constants.php');
require('../class/database.php');
require('../class/lol.php');
require('../class/onlol.php');
require('../class/riot.php');
$versions = onlol::readjson('https://ddragon.leagueoflegends.com/api/versions.json');
$db->query('UPDATE config SET value="'.$versions[0].'" WHERE name="lol_patch"');

$db->query('TRUNCATE TABLE lol_versions');
foreach($versions as $patch)
{
	onlol::setlog('cron_versions', 'New version added: '.$versions[0]);
	$db->query('INSERT INTO lol_versions (patch) VALUES ("'.$patch.'")');
}