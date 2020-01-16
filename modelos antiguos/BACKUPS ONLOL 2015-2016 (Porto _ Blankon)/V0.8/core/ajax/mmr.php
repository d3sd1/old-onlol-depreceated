<?php
require_once('../core.php');
if(empty($_POST['invid']) or empty($_POST['server'])) { exit('No data pls');}
if($db->query('SELECT level FROM inv_users WHERE summoner_id="'.$_POST['invid'].'" AND region="'.parseserver($_POST['server']).'" LIMIT 1')->fetch_row()[0] == '30')
{
	
	$profile_newmmr = mmr($_POST['invid'], parseserver($_POST['server']), $db->query('SELECT ranked_lp FROM inv_users WHERE summoner_id="'.$_POST['invid'].'" AND region="'.parseserver($_POST['server']).'" LIMIT 1')->fetch_row()[0], $db->query('SELECT ranked_league FROM inv_users WHERE summoner_id="'.$_POST['invid'].'" AND region="'.parseserver($_POST['server']).'" LIMIT 1')->fetch_row()[0], $db->query('SELECT ranked_division FROM inv_users WHERE summoner_id="'.$_POST['invid'].'" AND region="'.parseserver($_POST['server']).'" LIMIT 1')->fetch_row()[0]);
	$profile_league = summonerinfo($_POST['invid'],'ranked_league');
	$profile_division = summonerinfo($_POST['invid'],'ranked_division');
	$profile_mmr = summonerinfo($_POST['invid'],'mmr');
	$distancetojump = legaveragemmr($profile_league, $profile_division) + config('mmr_interval_to_jumpandhell');
	$distancetohell = legaveragemmr($profile_league, $profile_division) - config('mmr_interval_to_jumpandhell'); 
	echo '<div ';
	if($profile_mmr >= $distancetojump) 
	{ echo 'class="alert alert-info fadeinanim" title="¡GG! Es probable que saltes dos divisiones."'; }
	elseif($profile_mmr >= legaveragemmr($profile_league, $profile_division)) 
	{ echo 'class="alert alert-success fadeinanim" title="Tienes un mmr normal."'; }
	if($profile_mmr <= $distancetohell) 
	{ echo 'class="alert alert-danger fadeinanim" title="¡Cuidado! Estás en elohell."'; } 
	elseif($profile_mmr <= legaveragemmr($profile_league, $profile_division)) 
	{ echo 'class="alert alert-warning" title="Cuidado, tu mmr está por debajo de la liga."'; } 
	echo ' data-toggle="tooltip" data-placement="left" role="alert">MMR: '.$profile_newmmr.' <i style="font-size: 16px; float:right; color: #3c763d; cursor: not-allowed;" class="icon_close"></i></div>';
}