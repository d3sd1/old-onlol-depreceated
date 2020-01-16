<?php 
require('../kernel/core.php');
$region = lol::parseserver($_GET['region']);
/* Check profile at first */
if($db->query('SELECT id FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->num_rows == 0 or !empty($_GET['reload']))
{
	if($db->query('SELECT id FROM lol_summoners WHERE region="'.$region.'" AND name="'.$_GET['summoner'].'"')->num_rows == 0) //IMPORT profile on DB
	{
		/* Basic data */
		if($db->query('SELECT id FROM lol_summoners WHERE region="'.$region.'" AND last_update<'.(time()-onlol::timing('profile_reload')).' ORDER BY last_update LIMIT 39')->num_rows > 0)
		{
			$api_query_summoners_basic = ','.implode(',',$db->query('SELECT name FROM lol_summoners WHERE region="'.$region.'" AND last_update<'.(time()-onlol::timing('profile_reload')).' ORDER BY last_update DESC LIMIT 39')->fetch_row());
		}
		else
		{
			$api_query_summoners_basic = null;
		}
		$summoner_info = onlol::readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.4/summoner/by-name/'.onlol::api_format_name($_GET['summoner']).onlol::api_format_name($api_query_summoners_basic).'?api_key='.LOL_API_KEY);
		if(is_array($summoner_info))
		{
			/* Optimized api summoners */
			foreach($summoner_info as $keyname => $data)
			{
				$db->query('UPDATE lol_summoners SET name="'.$data['name'].'",icon="'.$data['profileIconId'].'",level="'.$data['summonerLevel'].'",last_update='.time().' WHERE summoner_id='.$data['id'].' AND region="'.$region.'"');
			}
			/* Summoner info */
			if(!array_key_exists(onlol::api_format_name($_GET['summoner']),$summoner_info))
			{
				onlol::redirect(URL.'/home?error=summoner_not_found&&summoner='.$_GET['summoner'].'&&region='.$region);
			}
		}
		else
		{
			onlol::redirect(URL.'/home?error=summoner_not_found&&summoner='.$_GET['summoner'].'&&region='.$region);
		}
		$db->query('UPDATE lol_summmoners SET revision_date="'.$summoner_info[onlol::api_format_name($_GET['summoner'])]['revisionDate'].'" WHERE summoner_id="'.$summoner_info[onlol::api_format_name($_GET['summoner'])]['id'].'" AND region="'.$region.'"');
		$reload_summoner_level = $summoner_info[onlol::api_format_name($_GET['summoner'])]['summonerLevel'];
		$db->query('INSERT INTO lol_summoners (summoner_id,name,icon,level,revision_date,last_update,region) VALUES ('.$summoner_info[onlol::api_format_name($_GET['summoner'])]['id'].',"'.$summoner_info[onlol::api_format_name($_GET['summoner'])]['name'].'","'.$summoner_info[onlol::api_format_name($_GET['summoner'])]['profileIconId'].'","'.$summoner_info[onlol::api_format_name($_GET['summoner'])]['summonerLevel'].'","'.onlol::microtime_to_unix($summoner_info[onlol::api_format_name($_GET['summoner'])]['revisionDate']).'","'.time().'","'.$region.'")');
	}
	else //RELOAD profile on DB
	{
		$summoner_revision_date_before = $db->query('SELECT revision_date FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->fetch_row()[0];
		/* Basic data */
		if($db->query('SELECT id FROM lol_summoners WHERE region="'.$region.'" AND last_update<'.(time()-onlol::timing('profile_reload')).' ORDER BY last_update DESC LIMIT 39')->num_rows > 0)
		{
			$api_query_array = array();
			$api_query_query = $db->query('SELECT name FROM lol_summoners WHERE region="'.$region.'" AND last_update<'.(time()-onlol::timing('profile_reload')).' AND name!="'.$_GET['summoner'].'" ORDER BY last_update DESC LIMIT 39');
			while($row = $api_query_query->fetch_row())
			{
				$api_query_array[$row[0]] = $row[0];
			}
			$api_query_summoners_basic = ','.implode(',',$api_query_array);
		}
		else
		{
			$api_query_summoners_basic = null;
		}
		$summoner_info = onlol::readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.4/summoner/by-name/'.onlol::api_format_name($_GET['summoner']).onlol::api_format_name($api_query_summoners_basic).'?api_key='.LOL_API_KEY);
		
		/* Optimized api summoners */
		if(is_array($summoner_info))
		{
			foreach($summoner_info as $keyname => $data)
			{
				if($_GET['summoner'] == $data['name'])
				{
					$db->query('UPDATE lol_summoners SET revision_date="'.onlol::microtime_to_unix($data['revisionDate']).'" WHERE summoner_id='.$data['id'].' AND region="'.$region.'"');
				}
					$db->query('UPDATE lol_summoners SET name="'.$data['name'].'",icon="'.$data['profileIconId'].'",level="'.$data['summonerLevel'].'",last_update='.time().' WHERE summoner_id='.$data['id'].' AND region="'.$region.'"');
			}
			$reload_summoner_level = $summoner_info[onlol::api_format_name($_GET['summoner'])]['summonerLevel'];
			$summoner_id = $summoner_info[onlol::api_format_name($_GET['summoner'])]['id'];
		}
		else
		{
			$reload_summoner_level = 0;
			$load_notification_error_reload_apibusy = true;
		}
	}
	$s_data = $db->query('SELECT summoner_id,revision_date FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->fetch_row();
	$summoner_id = $s_data[0];
	$summoner_revision_date_after = $s_data[1];
	if(!empty($summoner_revision_date_before))
	{
		if($summoner_revision_date_before != $summoner_revision_date_after)
		{
			$profile_got_reload = true;
		}
		else
		{
			$profile_got_reload = false;
		}
	}
	else
	{
		$profile_got_reload = true;
	}
	if($profile_got_reload == true)
	{
		/* Leagues */
		if($reload_summoner_level >= 30)
		{
			if($db->query('SELECT id FROM lol_summoners_leagues WHERE region="'.$region.'" AND last_update<'.(time()-onlol::timing('profile_leagues_reload')).' ORDER BY last_update LIMIT 9')->num_rows > 0)
			{
				$summoners_to_reload_league = $db->query('SELECT summoner_id FROM lol_summoners WHERE region="'.$region.'" AND last_update<'.(time()-onlol::timing('profile_reload')).' ORDER BY last_update DESC LIMIT 9');
				$summoners_to_reload_league_array = array();
				while($row = $summoners_to_reload_league->fetch_row())
				{
					$summoners_to_reload_league_array[$row[0]] = $row[0];
				}
				$api_query_summoners_leagues = ','.implode(',',$summoners_to_reload_league_array);
			}
			else
			{
				$api_query_summoners_leagues = null;
			}
			/* Clear summoner teams for if he leaved 'em */
			$summoner_teams = $db->query('SELECT participants,team_id FROM lol_teams WHERE participants LIKE "%'.$summoner_id.'%"') or die($db->error);
			while($row = $summoner_teams->fetch_row())
			{
				$participants_cleared = str_replace($summoner_id,'',$row[0]);
				$participants_cleared = str_replace(';;','',$participants_cleared);
				if($participants_cleared == ';')
				{
					$participants_cleared = null;
				}
				$db->query('UPDATE lol_teams SET participants="'.$participants_cleared.'" WHERE team_id="'.$row[1].'" AND region="'.$region.'"') or die($db->error);
			}

			$league_data = onlol::readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v2.5/league/by-summoner/'.$summoner_id.$api_query_summoners_leagues.'?api_key='.LOL_API_KEY);
			foreach($league_data as $summoner_id => $queues)
			{
				/* Foreach queues */
				foreach($queues as $data)
				{
					if($data['queue'] == 'RANKED_SOLO_5x5')
					{
						$this_active_qq_summoner = 0;
						foreach($data['entries'] as $active_qq_summoner)
						{
							if($data['entries'][$this_active_qq_summoner]['isHotStreak'] == null){$streak='false';}else{$streak = $data['entries'][$this_active_qq_summoner]['isHotStreak'];}
							if($data['entries'][$this_active_qq_summoner]['isFreshBlood'] == null){$recent_joined='false';}else{$recent_joined = $data['entries'][$this_active_qq_summoner]['isFreshBlood'];}
							if($data['entries'][$this_active_qq_summoner]['isVeteran'] == null){$veteran='false';}else{$veteran = $data['entries'][$this_active_qq_summoner]['isVeteran'];}
							if($data['entries'][$this_active_qq_summoner]['isInactive'] == null){$inactive='false';}else{$inactive = $data['entries'][$this_active_qq_summoner]['isInactive'];}
							if(array_key_exists('miniSeries',$data['entries'][$this_active_qq_summoner])){$miniseries='true';$miniseries_target=$data['entries'][$this_active_qq_summoner]['miniSeries']['target'];$miniseries_wins=$data['entries'][$this_active_qq_summoner]['miniSeries']['wins'];$miniseries_losses=$data['entries'][$this_active_qq_summoner]['miniSeries']['losses'];$miniseries_progress=$data['entries'][$this_active_qq_summoner]['miniSeries']['progress'];}else{$miniseries='false';$miniseries_target=0;$miniseries_wins=0;$miniseries_losses=0;$miniseries_progress='N/A';}
							
							if($db->query('SELECT id FROM lol_summoners_leagues WHERE summoner_id='.$data['entries'][$this_active_qq_summoner]['playerOrTeamId'].' AND region="'.$region.'"')->num_rows == 0)
							{
								$db->query('INSERT INTO lol_summoners_leagues (summoner_id,region,tier,division,lp,wins,losses,streak,veteran,recent_joined,inactive,last_update,miniseries,miniseries_matches,miniseries_wins,miniseries_losses,miniseries_progress,mmr) VALUES ('.$data['entries'][$this_active_qq_summoner]['playerOrTeamId'].',"'.$region.'","'.substr($data['tier'],0,1).'","'.$data['entries'][$this_active_qq_summoner]['division'].'",'.$data['entries'][$this_active_qq_summoner]['leaguePoints'].','.$data['entries'][$this_active_qq_summoner]['wins'].','.$data['entries'][$this_active_qq_summoner]['losses'].',"'.$streak.'","'.$veteran.'","'.$recent_joined.'","'.$inactive.'",'.time().',"'.$miniseries.'","'.$miniseries_target.'","'.$miniseries_wins.'","'.$miniseries_losses.'","'.$miniseries_progress.'",'.lol::getmmr($data['entries'][$this_active_qq_summoner]['leaguePoints'],substr($data['tier'],0,1),$data['entries'][$this_active_qq_summoner]['division']).')') or die($db->error);
							}
							else
							{
								$db->query('UPDATE lol_summoners_leagues SET tier="'.substr($data['tier'],0,1).'",division="'.$data['entries'][$this_active_qq_summoner]['division'].'",lp='.$data['entries'][$this_active_qq_summoner]['leaguePoints'].',wins='.$data['entries'][$this_active_qq_summoner]['wins'].',losses='.$data['entries'][$this_active_qq_summoner]['losses'].',streak="'.$streak.'",veteran="'.$veteran.'",recent_joined="'.$recent_joined.'",inactive="'.$inactive.'",last_update='.time().',miniseries="'.$miniseries.'",miniseries_matches="'.$miniseries_target.'",miniseries_wins="'.$miniseries_wins.'",miniseries_losses="'.$miniseries_losses.'",miniseries_progress="'.$miniseries_progress.'",mmr='.lol::getmmr($data['entries'][$this_active_qq_summoner]['leaguePoints'],substr($data['tier'],0,1),$data['entries'][$this_active_qq_summoner]['division']).' WHERE summoner_id='.$data['entries'][$this_active_qq_summoner]['playerOrTeamId'].' AND region="'.$region.'"') or die($db->error);
							}
							$this_active_qq_summoner++;
						}
					}
					if($data['queue'] == 'RANKED_TEAM_5x5')
					{
						$this_active_qq_team_5x5 = 0;
						foreach($data['entries'] as $active_qq_summoner)
						{
							if($data['entries'][$this_active_qq_team_5x5]['isHotStreak'] == null){$streak='false';}else{$streak = $data['entries'][$this_active_qq_team_5x5]['isHotStreak'];}
							if($data['entries'][$this_active_qq_team_5x5]['isFreshBlood'] == null){$recent_joined='false';}else{$recent_joined = $data['entries'][$this_active_qq_team_5x5]['isFreshBlood'];}
							if($data['entries'][$this_active_qq_team_5x5]['isVeteran'] == null){$veteran='false';}else{$veteran = $data['entries'][$this_active_qq_team_5x5]['isVeteran'];}
							if($data['entries'][$this_active_qq_team_5x5]['isInactive'] == null){$inactive='false';}else{$inactive = $data['entries'][$this_active_qq_team_5x5]['isInactive'];}
							if(array_key_exists('miniSeries',$data['entries'][$this_active_qq_team_5x5])){$miniseries='true';$miniseries_target=$data['entries'][$this_active_qq_team_5x5]['miniSeries']['target'];$miniseries_wins=$data['entries'][$this_active_qq_team_5x5]['miniSeries']['wins'];$miniseries_losses=$data['entries'][$this_active_qq_team_5x5]['miniSeries']['losses'];$miniseries_progress=$data['entries'][$this_active_qq_team_5x5]['miniSeries']['progress'];}else{$miniseries='false';$miniseries_target=0;$miniseries_wins=0;$miniseries_losses=0;$miniseries_progress='N/A';}
							if($data['participantId'] == $data['entries'][$this_active_qq_team_5x5]['playerOrTeamId'])
							{
								$participants_added = $db->query('SELECT participants FROM lol_teams WHERE team_id="'.$data['entries'][$this_active_qq_team_5x5]['playerOrTeamId'].'" AND region="'.$region.'"')->fetch_row()[0];
								if($participants_added != null)
								{
									$participants_added = $participants_added.';'.$summoner_id;
								}
								else
								{
									$participants_added = $summoner_id;
								}
								$db->query('UPDATE lol_teams SET participants="'.$participants_added.'" WHERE team_id="'.$data['entries'][$this_active_qq_team_5x5]['playerOrTeamId'].'" AND region="'.$region.'"') or die($db->error);
							}
							else
							{
								$participant = null;
							}
							if($db->query('SELECT id FROM lol_teams WHERE team_id="'.$data['entries'][$this_active_qq_team_5x5]['playerOrTeamId'].'" AND region="'.$region.'"')->num_rows == 0)
							{
								$db->query('INSERT INTO lol_teams (participants,team_id,region,name,5x5_tier,5x5_division,5x5_lp,5x5_wins,5x5_losses,5x5_streak,5x5_veteran,5x5_recent_joined,5x5_inactive,5x5_set,5x5_miniseries,5x5_miniseries_matches,5x5_miniseries_wins,5x5_miniseries_losses,5x5_miniseries_progress) VALUES ("'.$participant.'","'.$data['entries'][$this_active_qq_team_5x5]['playerOrTeamId'].'","'.$region.'","'.$data['entries'][$this_active_qq_team_5x5]['playerOrTeamName'].'","'.substr($data['tier'],0,1).'","'.$data['entries'][$this_active_qq_team_5x5]['division'].'",'.$data['entries'][$this_active_qq_team_5x5]['leaguePoints'].','.$data['entries'][$this_active_qq_team_5x5]['wins'].','.$data['entries'][$this_active_qq_team_5x5]['losses'].',"'.$streak.'","'.$veteran.'","'.$recent_joined.'","'.$inactive.'","true","'.$miniseries.'","'.$miniseries_target.'","'.$miniseries_wins.'","'.$miniseries_losses.'","'.$miniseries_progress.'")') or die($db->error);
							}
							else
							{
								$db->query('UPDATE lol_teams SET '.$participant.'5x5_set="true",5x5_tier="'.substr($data['tier'],0,1).'",5x5_division="'.$data['entries'][$this_active_qq_team_5x5]['division'].'",name="'.$data['entries'][$this_active_qq_team_5x5]['playerOrTeamName'].'",5x5_lp='.$data['entries'][$this_active_qq_team_5x5]['leaguePoints'].',5x5_wins='.$data['entries'][$this_active_qq_team_5x5]['wins'].',5x5_losses='.$data['entries'][$this_active_qq_team_5x5]['losses'].',5x5_streak="'.$streak.'",5x5_veteran="'.$veteran.'",5x5_recent_joined="'.$recent_joined.'",5x5_inactive="'.$inactive.'",5x5_miniseries="'.$miniseries.'",5x5_miniseries_matches="'.$miniseries_matches.'",5x5_miniseries_wins="'.$miniseries_matches_wins.'",5x5_miniseries_losses=="'.$miniseries_losses.'",5x5_miniseries_progress=="'.$miniseries_progress.'" WHERE team_id="'.$data['entries'][$this_active_qq_team_5x5]['playerOrTeamId'].'" AND region="'.$region.'"') or die($db->error);
							}
							$this_active_qq_team_5x5++;
						}
					}
					if($data['queue'] == 'RANKED_TEAM_3x3')
					{
						$this_active_qq_team_3x3 = 0;
						foreach($data['entries'] as $active_qq_summoner)
						{
							if($data['entries'][$this_active_qq_team_3x3]['isHotStreak'] == null){$streak='false';}else{$streak = $data['entries'][$this_active_qq_team_3x3]['isHotStreak'];}
							if($data['entries'][$this_active_qq_team_3x3]['isFreshBlood'] == null){$recent_joined='false';}else{$recent_joined = $data['entries'][$this_active_qq_team_3x3]['isFreshBlood'];}
							if($data['entries'][$this_active_qq_team_3x3]['isVeteran'] == null){$veteran='false';}else{$veteran = $data['entries'][$this_active_qq_team_3x3]['isVeteran'];}
							if($data['entries'][$this_active_qq_team_3x3]['isInactive'] == null){$inactive='false';}else{$inactive = $data['entries'][$this_active_qq_team_3x3]['isInactive'];}
							if(array_key_exists('miniSeries',$data['entries'][$this_active_qq_team_3x3])){$miniseries='true';$miniseries_target=$data['entries'][$this_active_qq_team_3x3]['miniSeries']['target'];$miniseries_wins=$data['entries'][$this_active_qq_team_3x3]['miniSeries']['wins'];$miniseries_losses=$data['entries'][$this_active_qq_team_3x3]['miniSeries']['losses'];$miniseries_progress=$data['entries'][$this_active_qq_team_3x3]['miniSeries']['progress'];}else{$miniseries='false';$miniseries_target=0;$miniseries_wins=0;$miniseries_losses=0;$miniseries_progress='N/A';}
							if($data['participantId'] == $data['entries'][$this_active_qq_team_3x3]['playerOrTeamId'])
							{
								$participants_added = $db->query('SELECT participants FROM lol_teams WHERE team_id="'.$data['entries'][$this_active_qq_team_3x3]['playerOrTeamId'].'" AND region="'.$region.'"')->fetch_row()[0];
								if($participants_added != null)
								{
									$participants_added = $participants_added.';'.$summoner_id;
								}
								else
								{
									$participants_added = $summoner_id;
								}
								$db->query('UPDATE lol_teams SET participants="'.$participants_added.'" WHERE team_id="'.$data['entries'][$this_active_qq_team_3x3]['playerOrTeamId'].'" AND region="'.$region.'"') or die($db->error);
							}
							else
							{
								$participant = null;
							}
							if($db->query('SELECT id FROM lol_teams WHERE team_id="'.$data['entries'][$this_active_qq_team_3x3]['playerOrTeamId'].'" AND region="'.$region.'"')->num_rows == 0)
							{
								$db->query('INSERT INTO lol_teams (participants,team_id,region,name,3x3_tier,3x3_division,3x3_lp,3x3_wins,3x3_losses,3x3_streak,3x3_veteran,3x3_recent_joined,3x3_inactive,3x3_set,3x3_miniseries,3x3_miniseries_matches,3x3_miniseries_wins,3x3_miniseries_losses,3x3_miniseries_progress) VALUES ("'.$participant.'","'.$data['entries'][$this_active_qq_team_3x3]['playerOrTeamId'].'","'.$region.'","'.$data['entries'][$this_active_qq_team_3x3]['playerOrTeamName'].'","'.substr($data['tier'],0,1).'","'.$data['entries'][$this_active_qq_team_3x3]['division'].'",'.$data['entries'][$this_active_qq_team_3x3]['leaguePoints'].','.$data['entries'][$this_active_qq_team_3x3]['wins'].','.$data['entries'][$this_active_qq_team_3x3]['losses'].',"'.$streak.'","'.$veteran.'","'.$recent_joined.'","'.$inactive.'","true","'.$miniseries.'","'.$miniseries_target.'","'.$miniseries_wins.'","'.$miniseries_losses.'","'.$miniseries_progress.'")') or die($db->error);
							}
							else
							{
								$db->query('UPDATE lol_teams SET '.$participant.'3x3_set="true",3x3_tier="'.substr($data['tier'],0,1).'",3x3_division="'.$data['entries'][$this_active_qq_team_3x3]['division'].'",name="'.$data['entries'][$this_active_qq_team_3x3]['playerOrTeamName'].'",3x3_lp='.$data['entries'][$this_active_qq_team_3x3]['leaguePoints'].',3x3_wins='.$data['entries'][$this_active_qq_team_3x3]['wins'].',3x3_losses='.$data['entries'][$this_active_qq_team_3x3]['losses'].',3x3_streak="'.$streak.'",3x3_veteran="'.$veteran.'",3x3_recent_joined="'.$recent_joined.'",3x3_inactive="'.$inactive.'",3x3_miniseries="'.$miniseries.'",3x3_miniseries_matches="'.$miniseries_target.'",3x3_miniseries_wins="'.$miniseries_wins.'",3x3_miniseries_losses="'.$miniseries_losses.'",3x3_miniseries_progress="'.$miniseries_progress.'" WHERE team_id="'.$data['entries'][$this_active_qq_team_3x3]['playerOrTeamId'].'" AND region="'.$region.'"') or die($db->error);
							}
							$this_active_qq_team_3x3++;
						}
					}
				}
			}
		}
		/* Recent games */
		$s_data = $db->query('SELECT summoner_id,revision_date FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->fetch_row();
		$summoner_id = $s_data[0];
		$recent_games = onlol::readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.3/game/by-summoner/'.$summoner_id.'/recent?api_key='.LOL_API_KEY);
		$add_summoner_to_db = array();
		if(is_array($recent_games))
		{
			if(array_key_exists('games',$recent_games))
			{
				foreach($recent_games['games'] as $data)
				{
					/* Data */
					$match_id = $data['gameId'];
					if($data['invalid'] == 'true')
					{
						$loss_prevented = 'true';
					}
					else
					{
						$loss_prevented = 'false';
					}
					$queue = $data['subType'];
					$map = $data['mapId'];
					$game_start = $data['createDate'];
					// FALTA AQUI PONER LOS DATOS DE LOS PARTICIPANTES, POR CLAVE ID Y VALORES DE LA PARTIDA. SI LA PARTIDA YA ESTABA CREADA AÑADIR CLAVE.
					if(array_key_exists('fellowPlayers',$data))
					{
						$participant_data = array();
						foreach($data['fellowPlayers'] as $players_data)
						{
							$participant_data[$players_data['summonerId']] = array();
							$participant_data[$players_data['summonerId']]['team_id'] = $players_data['teamId'];
							$participant_data[$players_data['summonerId']]['champ_id'] = $players_data['championId'];
							if($db->query('SELECT id FROM lol_summoners WHERE summoner_id='.$players_data['summonerId'].' AND region="'.$region.'"')->num_rows == 0)
							{
								$add_summoner_to_db[$players_data['summonerId']] = $players_data['summonerId'];
							}
						}
					}
					else
					{
						$participant_data = 'N/A';
					}
					/* Add data to DB */
					if($db->query('SELECT id FROM lol_matches WHERE match_id='.$match_id)->num_rows == 0)
					{
						$all_participants = array();
						$summoner_ids = $summoner_id.';';
						$count_participants = 0;
						foreach($data['fellowPlayers'] as $summonerdatainfo)
						{
							$all_participants[$summonerdatainfo['summonerId']] = array();
							if($count_participants != 0)
							{
								$summoner_ids .= ';';
							}
							$summoner_ids .= $summonerdatainfo['summonerId'];
							$all_participants[$summonerdatainfo['summonerId']]['team_id'] = $summonerdatainfo['teamId'];
							$all_participants[$summonerdatainfo['summonerId']]['champ_id'] = $summonerdatainfo['championId'];
							$count_participants++;
						}
						$all_participants[$summoner_id] = array();
						$all_participants[$summoner_id]['champ_id'] = @$data['championId'];
						$all_participants[$summoner_id]['spell_1'] = @$data['spell1'];
						$all_participants[$summoner_id]['spell_2'] = @$data['spell2'];
						$all_participants[$summoner_id]['ip_earned'] = @$data['ipEarned'];
						$all_participants[$summoner_id]['level'] = @$data['stats']['level'];
						$all_participants[$summoner_id]['gold'] = @$data['stats']['goldEarned'];
						$all_participants[$summoner_id]['gold_spent'] = @$data['stats']['goldSpent'];
						$all_participants[$summoner_id]['killing_sprees'] = @$data['stats']['killingSprees'];
						$all_participants[$summoner_id]['killing_sprees_largest'] = @$data['stats']['largestKillingSpree'];
						$all_participants[$summoner_id]['kills'] = @$data['stats']['championsKilled'];
						$all_participants[$summoner_id]['deaths'] = @$data['stats']['numDeaths'];
						$all_participants[$summoner_id]['assists'] = @$data['stats']['assists'];
						$all_participants[$summoner_id]['largest_multi_kill'] = @$data['stats']['largestMultiKill'];
						$all_participants[$summoner_id]['minions'] = @$data['stats']['minionsKilled'];
						$all_participants[$summoner_id]['minions_neutral'] = @$data['stats']['neutralMinionsKilled'];
						$all_participants[$summoner_id]['dmg_dealt_total'] = @$data['stats']['totalDamageDealt'];
						$all_participants[$summoner_id]['dmg_dealt_taken'] = @$data['stats']['totalDamageTaken'];
						$all_participants[$summoner_id]['dmg_dealt_ad_tochamps'] = @$data['stats']['physicalDamageDealtToChampions'];
						$all_participants[$summoner_id]['dmg_dealt_ad'] = @$data['stats']['physicalDamageDealtPlayer'];
						$all_participants[$summoner_id]['dmg_dealt_ap_tochamps'] = @$data['stats']['magicDamageDealtToChampions'];
						$all_participants[$summoner_id]['dmg_dealt_tochamps'] = @$data['stats']['totalDamageDealtToChampions'];
						$all_participants[$summoner_id]['dmg_dealt_true'] = @$data['stats']['trueDamageDealtPlayer'];
						$all_participants[$summoner_id]['dmg_dealt_true_tochamps'] = @$data['stats']['trueDamageDealtToChampions'];
						$all_participants[$summoner_id]['dmg_taken_true'] = @$data['stats']['trueDamageTaken'];
						$all_participants[$summoner_id]['dmg_dealt_ap'] = @$data['stats']['magicDamageDealtPlayer'];
						$all_participants[$summoner_id]['dmg_taken_ad'] = @$data['stats']['physicalDamageTaken'];
						$all_participants[$summoner_id]['dmg_taken_ap'] = @$data['stats']['magicDamageTaken'];
						$all_participants[$summoner_id]['time_played'] = @$data['stats']['timePlayed'];
						$all_participants[$summoner_id]['total_healed'] = @$data['stats']['totalHeal'];
						$all_participants[$summoner_id]['total_units_healed'] = @$data['stats']['totalUnitsHealed'];
						$all_participants[$summoner_id]['team_id'] = @$data['stats']['team'];
						$all_participants[$summoner_id]['winner'] = @$data['stats']['win'];
						$all_participants[$summoner_id]['wards_killed'] = @$data['stats']['wardKilled'];
						$all_participants[$summoner_id]['wards_placed'] = @$data['stats']['wardPlaced'];
						$all_participants[$summoner_id]['wards_placed_pinks'] = @$data['stats']['visionWardsBought'];
						$all_participants[$summoner_id]['minions_neutral_enemyjgl'] = @$data['stats']['neutralMinionsKilledEnemyJungle'];
						$all_participants[$summoner_id]['minions_neutral_selfjgl'] = @$data['stats']['neutralMinionsKilledYourJungle'];
						$all_participants[$summoner_id]['time_on_cc'] = @$data['stats']['totalTimeCrowdControlDealt'];
						$all_participants[$summoner_id]['player_position'] = @$data['stats']['playerPosition'];
						$all_participants[$summoner_id]['items'] = array();
						$all_participants[$summoner_id]['items'][0] = @$data['stats']['item0'];
						$all_participants[$summoner_id]['items'][1] = @$data['stats']['item1'];
						$all_participants[$summoner_id]['items'][2] = @$data['stats']['item2'];
						$all_participants[$summoner_id]['items'][3] = @$data['stats']['item3'];
						$all_participants[$summoner_id]['items'][4] = @$data['stats']['item4'];
						$all_participants[$summoner_id]['items'][5] = @$data['stats']['item5'];
						$all_participants[$summoner_id]['items'][6] = @$data['stats']['item6'];
						$participant_data = addslashes(json_encode($all_participants));
						$db->query('INSERT INTO lol_matches (match_id,region,summoner_ids,loss_prevented,creation_timestamp,queue,map,participants,advanced_info) VALUES ('.$match_id.',"'.$region.'","'.$summoner_ids.'","'.$loss_prevented.'","'.onlol::microtime_to_unix($game_start).'","'.$queue.'",'.$map.',"'.$participant_data.'","false")') or die($db->error);
					}
					else
					{
						if($db->query('SELECT advanced_info FROM lol_matches WHERE match_id='.$match_id.' AND region="'.$region.'"')->fetch_row()[0] == 'false')
						{
							$all_participants = array();
							$summoner_ids = $summoner_id.';';
							$count_participants = 0;
							foreach($data['fellowPlayers'] as $summonerdatainfo)
							{
								$all_participants[$summonerdatainfo['summonerId']] = array();
								if($count_participants != 0)
								{
									$summoner_ids .= ';';
								}
								$summoner_ids .= $summonerdatainfo['summonerId'];
								$all_participants[$summonerdatainfo['summonerId']]['team_id'] = $summonerdatainfo['teamId'];
								$all_participants[$summonerdatainfo['summonerId']]['champ_id'] = $summonerdatainfo['championId'];
								$count_participants++;
							}
							$all_participants[$summoner_id] = array();
							$all_participants[$summoner_id]['champ_id'] = @$data['championId'];
							$all_participants[$summoner_id]['spell_1'] = @$data['spell1'];
							$all_participants[$summoner_id]['spell_2'] = @$data['spell2'];
							$all_participants[$summoner_id]['ip_earned'] = @$data['ipEarned'];
							$all_participants[$summoner_id]['level'] = @$data['stats']['level'];
							$all_participants[$summoner_id]['gold'] = @$data['stats']['goldEarned'];
							$all_participants[$summoner_id]['gold_spent'] = @$data['stats']['goldSpent'];
							$all_participants[$summoner_id]['killing_sprees'] = @$data['stats']['killingSprees'];
							$all_participants[$summoner_id]['killing_sprees_largest'] = @$data['stats']['largestKillingSpree'];
							$all_participants[$summoner_id]['kills'] = @$data['stats']['championsKilled'];
							$all_participants[$summoner_id]['deaths'] = @$data['stats']['numDeaths'];
							$all_participants[$summoner_id]['assists'] = @$data['stats']['assists'];
							$all_participants[$summoner_id]['largest_multi_kill'] = @$data['stats']['largestMultiKill'];
							$all_participants[$summoner_id]['minions'] = @$data['stats']['minionsKilled'];
							$all_participants[$summoner_id]['minions_neutral'] = @$data['stats']['neutralMinionsKilled'];
							$all_participants[$summoner_id]['dmg_dealt_total'] = @$data['stats']['totalDamageDealt'];
							$all_participants[$summoner_id]['dmg_dealt_taken'] = @$data['stats']['totalDamageTaken'];
							$all_participants[$summoner_id]['dmg_dealt_ad_tochamps'] = @$data['stats']['physicalDamageDealtToChampions'];
							$all_participants[$summoner_id]['dmg_dealt_ad'] = @$data['stats']['physicalDamageDealtPlayer'];
							$all_participants[$summoner_id]['dmg_dealt_ap_tochamps'] = @$data['stats']['magicDamageDealtToChampions'];
							$all_participants[$summoner_id]['dmg_dealt_tochamps'] = @$data['stats']['totalDamageDealtToChampions'];
							$all_participants[$summoner_id]['dmg_dealt_true'] = @$data['stats']['trueDamageDealtPlayer'];
							$all_participants[$summoner_id]['dmg_dealt_true_tochamps'] = @$data['stats']['trueDamageDealtToChampions'];
							$all_participants[$summoner_id]['dmg_taken_true'] = @$data['stats']['trueDamageTaken'];
							$all_participants[$summoner_id]['dmg_dealt_ap'] = @$data['stats']['magicDamageDealtPlayer'];
							$all_participants[$summoner_id]['dmg_taken_ad'] = @$data['stats']['physicalDamageTaken'];
							$all_participants[$summoner_id]['dmg_taken_ap'] = @$data['stats']['magicDamageTaken'];
							$all_participants[$summoner_id]['time_played'] = @$data['stats']['timePlayed'];
							$all_participants[$summoner_id]['total_healed'] = @$data['stats']['totalHeal'];
							$all_participants[$summoner_id]['total_units_healed'] = @$data['stats']['totalUnitsHealed'];
							$all_participants[$summoner_id]['team_id'] = @$data['stats']['team'];
							$all_participants[$summoner_id]['winner'] = @$data['stats']['win'];
							$all_participants[$summoner_id]['wards_killed'] = @$data['stats']['wardKilled'];
							$all_participants[$summoner_id]['wards_placed'] = @$data['stats']['wardPlaced'];
							$all_participants[$summoner_id]['wards_placed_pinks'] = @$data['stats']['visionWardsBought'];
							$all_participants[$summoner_id]['minions_neutral_enemyjgl'] = @$data['stats']['neutralMinionsKilledEnemyJungle'];
							$all_participants[$summoner_id]['minions_neutral_selfjgl'] = @$data['stats']['neutralMinionsKilledYourJungle'];
							$all_participants[$summoner_id]['time_on_cc'] = @$data['stats']['totalTimeCrowdControlDealt'];
							$all_participants[$summoner_id]['player_position'] = @$data['stats']['playerPosition'];
							$all_participants[$summoner_id]['items'] = array();
							$all_participants[$summoner_id]['items'][0] = @$data['stats']['item0'];
							$all_participants[$summoner_id]['items'][1] = @$data['stats']['item1'];
							$all_participants[$summoner_id]['items'][2] = @$data['stats']['item2'];
							$all_participants[$summoner_id]['items'][3] = @$data['stats']['item3'];
							$all_participants[$summoner_id]['items'][4] = @$data['stats']['item4'];
							$all_participants[$summoner_id]['items'][5] = @$data['stats']['item5'];
							$all_participants[$summoner_id]['items'][6] = @$data['stats']['item6'];
							$pre_participants = json_decode($db->query('SELECT participants FROM lol_matches WHERE match_id='.$match_id.' AND region="'.$region.'"')->fetch_row()[0],true);
							foreach($pre_participants as $summonerd_id => $value)
							{
								if(array_key_exists('gold',$value))
								{
									unset($all_participants[$summonerd_id]);
									$all_participants[$summonerd_id] = $value;
								}
							}
							$participant_data = addslashes(json_encode($all_participants));
							$db->query('UPDATE lol_matches SET participants="'.$participant_data.'" WHERE match_id='.$match_id.' AND region="'.$region.'"') or die($db->error);
						}
					}
				}
			}
		}
		if(!empty($add_summoner_to_db))
		{
			$summoners_to_add_chunk = array_chunk($add_summoner_to_db,40);
			$array_chunk_num = 0;
			foreach($summoners_to_add_chunk as $count => $add_summoner_to_db)
			{
				if(count($summoners_to_add_chunk[$array_chunk_num]) < 40)
				{
					$summoners_to_reload_opt = 40-count($summoners_to_add_chunk[$array_chunk_num]);
					/* Basic data */
					if($db->query('SELECT id FROM lol_summoners WHERE region="'.$region.'" AND last_update<'.(time()-onlol::timing('profile_reload')).' ORDER BY last_update LIMIT '.$summoners_to_reload_opt)->num_rows > 0)
					{
						$summoners_query = $db->query('SELECT summoner_id FROM lol_summoners WHERE region="'.$region.'" AND last_update<'.(time()-onlol::timing('profile_reload')).' AND name!="'.$_GET['summoner'].'" ORDER BY last_update DESC LIMIT '.$summoners_to_reload_opt);
						while($row = $summoners_query->fetch_row())
						{
							$add_summoner_to_db[$row[0]] = $row[0];
						}
					}
				}
				$summoners_to_add = implode(',',$add_summoner_to_db);
				$summoner_info = onlol::readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.4/summoner/'.onlol::api_format_name($summoners_to_add).'?api_key='.LOL_API_KEY);
				if(is_array($summoner_info))
				{
					foreach($summoner_info as $summoner)
					{
						$db->query('INSERT INTO lol_summoners (summoner_id,region,name,icon,level,last_update) VALUES ('.$summoner['id'].',"'.$region.'","'.$summoner['name'].'","'.$summoner['profileIconId'].'","'.$summoner['summonerLevel'].'",'.time().')');
					}
				}
				$array_chunk_num++;
			}
		}
	}
}
/* Summoner data variables */
$summoner_info = $db->query('SELECT summoner_id,name,icon,level,revision_date,last_update FROM lol_summoners WHERE name="'.$_GET['summoner'].'" AND region="'.$region.'"')->fetch_row();
$summoner_id = $summoner_info[0];
$summoner_name = $summoner_info[1];
$summoner_icon = $summoner_info[2];
$summoner_level = $summoner_info[3];
$summoner_revision = $summoner_info[4];
$summoner_lastupdate = $summoner_info[5];
if($db->query('SELECT id FROM lol_summoners_leagues WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'"')->num_rows > 0)
{
	$summoner_league = $db->query('SELECT tier,division,lp,wins,losses,miniseries,miniseries_progress,mmr FROM lol_summoners_leagues WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'"')->fetch_row();
	$summoner_tier = $summoner_league[0];
	$summoner_division = $summoner_league[1];
	$summoner_lp = $summoner_league[2];
	$summoner_wins = $summoner_league[3];
	$summoner_losses = $summoner_league[4];
	$summoner_miniseries = $summoner_league[5];
	$summoner_miniseries_progress = $summoner_league[6];
	$summoner_mmr = $summoner_league[7];
}
else //Is unranked
{
	$summoner_tier = 'U';
	$summoner_division = 'I';
	$summoner_lp = 0;
	$summoner_wins = 0;
	$summoner_losses = 0;
	$summoner_miniseries = 'false';
	$summoner_miniseries_progress = 'N/A';
	$summoner_mmr = 0;
}
if($db->query('SELECT id FROM lol_teams WHERE participants LIKE "%'.$summoner_id.'%" AND region="'.$region.'"')->num_rows > 0)
{
	$summoner_league_5x5 = $db->query('SELECT 5x5_tier,5x5_division,5x5_lp,5x5_wins,5x5_losses,5x5_miniseries,5x5_miniseries_progress FROM lol_teams WHERE participants LIKE "%'.$summoner_id.'%" AND region="'.$region.'"')->fetch_row();
	$summoner_5x5_tier = $summoner_league_5x5[0];
	$summoner_5x5_division = $summoner_league_5x5[1];
	$summoner_5x5_lp = $summoner_league_5x5[2];
	$summoner_5x5_wins = $summoner_league_5x5[3];
	$summoner_5x5_losses = $summoner_league_5x5[4];
	$summoner_5x5_miniseries = $summoner_league_5x5[5];
	$summoner_5x5_miniseries_progress = $summoner_league_5x5[6];
}
else
{
	$summoner_5x5_tier = 'U';
	$summoner_5x5_division = 'I';
	$summoner_5x5_lp = 0;
	$summoner_5x5_wins = 0;
	$summoner_5x5_losses = 0;
	$summoner_5x5_miniseries = 'false';
	$summoner_5x5_miniseries_progress = 'N/A';
}
if($db->query('SELECT id FROM lol_teams WHERE participants LIKE "%'.$summoner_id.'%" AND region="'.$region.'"')->num_rows > 0)
{
	$summoner_league_3x3 = $db->query('SELECT 3x3_tier,3x3_division,3x3_lp,3x3_wins,3x3_losses,3x3_miniseries,3x3_miniseries_progress FROM lol_teams WHERE participants LIKE "%'.$summoner_id.'%" AND region="'.$region.'"')->fetch_row();
	$summoner_3x3_tier = $summoner_league_3x3[0];
	$summoner_3x3_division = $summoner_league_3x3[1];
	$summoner_3x3_lp = $summoner_league_3x3[2];
	$summoner_3x3_wins = $summoner_league_3x3[3];
	$summoner_3x3_losses = $summoner_league_3x3[4];
	$summoner_3x3_miniseries = $summoner_league_3x3[5];
	$summoner_3x3_miniseries_progress = $summoner_league_3x3[6];
}
else
{
	$summoner_3x3_tier = 'U';
	$summoner_3x3_division = 'I';
	$summoner_3x3_lp = 0;
	$summoner_3x3_wins = 0;
	$summoner_3x3_losses = 0;
	$summoner_3x3_miniseries = 'false';
	$summoner_3x3_miniseries_progress = 'N/A';
}
/* Log of searchs */
if(@$summoner_id != 0 or !empty($summoner_id))
{
	onlol::addsearchlog($summoner_name,$region,$summoner_icon);
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- START @HEAD -->
    <head>
        <?php echo template::meta('profile_title'); ?>
		<title><?php echo lang::trans('profile_title').' '.$summoner_name ?></title>
        <!-- START @FAVICONS -->
        <link href="<?php echo URL ?>/style/favicons/144x144.png" rel="apple-touch-icon-precomposed" sizes="144x144">
        <link href="<?php echo URL ?>/style/favicons/114x114.png" rel="apple-touch-icon-precomposed" sizes="114x114">
        <link href="<?php echo URL ?>/style/favicons/72x72.png" rel="apple-touch-icon-precomposed" sizes="72x72">
        <link href="<?php echo URL ?>/style/favicons/57x57.png" rel="apple-touch-icon-precomposed">
        <link href="<?php echo URL ?>/style/favicons/favicon.png" rel="shortcut icon">
        <!--/ END FAVICONS -->

        <!-- START @FONT STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700" rel="stylesheet">
        <link href="http://fonts.googleapis.com/css?family=Oswald:700,400" rel="stylesheet">
        <!--/ END FONT STYLES -->

        <!-- START @GLOBAL MANDATORY STYLES -->
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <!--/ END GLOBAL MANDATORY STYLES -->
		<script>
		var lang_exit_txt_title = "<?php echo lang::trans('web_exit_title') ?>";
		var lang_exit_txt = "<?php echo lang::trans('web_exit_txt') ?>";
		var lang_exit_y = "<?php echo lang::trans('web_exit_y') ?>";
		var lang_exit_n = "<?php echo lang::trans('web_exit_n') ?>";
		</script>
        <!-- START @PAGE LEVEL STYLES -->
        
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/fontawesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/animate.css/animate.min.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/jquery.gritter/css/jquery.gritter.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/fuelux/dist/css/fuelux.min.css" rel="stylesheet">
		<link href="<?php echo URL ?>/style/global/css/cursor.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/global/plugins/bower_components/c3js-chart/c3.min.css" rel="stylesheet">
        <!--/ END PAGE LEVEL STYLES -->
		<?php if($summoner_tier != 'U')
		{
			echo '<style>
			.ranking_border{
				-moz-border-image: url('.URL.'/game/borders/'.$summoner_tier.'.png) 2 2 2 2 stretch stretch;
				-webkit-border-image: url('.URL.'/game/borders/'.$summoner_tier.'.png) 2 2 2 2 stretch stretch;
				border-image-source: url('.URL.'/game/borders/'.$summoner_tier.'.png) 2 2 2 2 stretch stretch;
				width: 151px;
				height: 150px;
			}
			</style>';
		}
		?>
        <!-- START @THEME STYLES -->
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery/dist/jquery.min.js"></script>
        <link href="<?php echo URL ?>/style/home/css/reset.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/home/css/layout.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/home/css/components.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/home/css/plugins.css" rel="stylesheet">
        <link href="<?php echo URL ?>/style/home/css/themes/default.theme.css" rel="stylesheet" id="theme">
        <link href="<?php echo URL ?>/style/home/css/pages/dashboard-retail.css" rel="stylesheet">
        <!--/ END THEME STYLES -->

        <!-- START @IE SUPPORT -->
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/html5shiv/dist/html5shiv.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/respond-minmax/dest/respond.min.js"></script>
        <![endif]-->
        <!--/ END IE SUPPORT -->
    </head>
    <!--/ END HEAD -->

    <!--

    |=========================================================================================================================|
    |  TABLE OF CONTENTS (Use search to find needed section)                                                                  |
    |=========================================================================================================================|
    |  01. @HEAD                        |  Container for all the head elements                                                |
    |  02. @META SECTION                |  The meta tag provides metadata about the HTML document                             |
    |  03. @FAVICONS                    |  Short for favorite icon, shortcut icon, website icon, tab icon or bookmark icon    |
    |  04. @FONT STYLES                 |  Font from google fonts                                                             |
    |  05. @GLOBAL MANDATORY STYLES     |  The main 3rd party plugins css file                                                |
    |  06. @PAGE LEVEL STYLES           |  Specific 3rd party plugins css file                                                |
    |  07. @THEME STYLES                |  The main theme css file                                                            |
    |  08. @IE SUPPORT                  |  IE support of HTML5 elements and media queries                                     |
    |=========================================================================================================================|
    |  09. @BODY                        |  Contains all the contents of an HTML document                                      |
    |  10. @WRAPPER                     |  Wrapping page section                                                              |
    |  11. @HEADER                      |  Header page section contains about logo, top navigation, notification menu         |
    |  12. @SIDEBAR LEFT                |  Sidebar page section contains all sidebar menu left                                |
    |  13. @PAGE CONTENT                |  Contents page section contains breadcrumb, content page, footer page               |
    |  14. @SIDEBAR RIGHT               |  Sidebar page section contains all sidebar menu right                               |
    |  15. @BACK TOP                    |  Element back to top and action                                                     |
    |=========================================================================================================================|
    |  16. @CORE PLUGINS                |  The main 3rd party plugins script file                                             |
    |  17. @PAGE LEVEL PLUGINS          |  Specific 3rd party plugins script file                                             |
    |  18. @PAGE LEVEL SCRIPTS          |  The main theme script file                                                         |
    |=========================================================================================================================|

    START @BODY
    |=========================================================================================================================|
	|  TABLE OF CONTENTS (Apply to body class)                                                                                |
	|=========================================================================================================================|
    |  01. page-boxed                   |  Page into the box is not full width screen                                         |
	|  02. page-header-fixed            |  Header element become fixed position                                               |
	|  03. page-sidebar-fixed           |  Sidebar element become fixed position with scroll support                          |
	|  04. page-sidebar-minimize        |  Sidebar element become minimize style width sidebar                                |
	|  05. page-footer-fixed            |  Footer element become fixed position with scroll support on page content           |
	|  06. page-sound                   |  For playing sounds on user actions and page events                                 |
	|=========================================================================================================================|

	-->
    <body>

        <!--[if lt IE 9]>
        <p class="upgrade-browser">Upps!! You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/" target="_blank">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- START @WRAPPER -->
        <section id="wrapper">

            <?php echo template::nav_top() ?>
            <?php echo template::left_menu('profile') ?>

            <!-- START @PAGE CONTENT -->
            <section id="page-content" >
                <!-- Start body content -->
                <div class="body-content animated fadeIn" style="background-image: url(<?php echo URL ?>/game/champions/Aatrox/splash/0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">

                    <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="profile-cover" style="background-color:rgba(255,255,255,0.2);">
                        <div class="cover rounded shadow no-overflow">
                            <div class="inner-cover"  style="height:400px">
								<div class="col-lg-1 col-md-2 col-sm-1">
									<div class="ranking_border"><img draggable="false" src="<?php echo URL ?>/game/icons/<?php echo $summoner_icon ?>.png" style="width:128px; height:128px; margin-top:10.5px; margin-left:10px;"></div>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2" style="margin-left: 50px; margin-top:40px; text-align:center;">
									<div class="alert alert-default" style="color:black;">
									<?php 
									$size = 2;
									if(strlen($summoner_name) >= 14)
									{
										$size = 3;
									}
									if(strlen($summoner_name) >= 17)
									{
										$size = 4;
									}
									?>
                                        <h<?php echo $size ?> style="margin-top:4px"><?php echo $summoner_name ?></h<?php echo $size ?>>
                                    </div>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2" style="margin-top:40px; text-align:center;">
								<?php
								if($summoner_mmr > 0)
								{
									if($summoner_mmr >= lol::division_mmr($summoner_tier.'_'.$summoner_division) && $summoner_mmr >= lol::division_mmr(lol::tier_oneless($summoner_tier,$summoner_division)))
									{
										$mmr_status = 'success';
										$mmr_msg = lang::trans('profile_mmr_normal');
									}
									if($summoner_mmr < lol::division_mmr($summoner_tier.'_'.$summoner_division) && $summoner_mmr > lol::division_mmr(lol::tier_oneless($summoner_tier,$summoner_division)))
									{
										$mmr_status = 'warning';
										$mmr_msg = lang::trans('profile_mmr_low');
									}
									if($summoner_mmr < lol::division_mmr($summoner_tier.'_'.$summoner_division) && $summoner_mmr < lol::division_mmr(lol::tier_oneless($summoner_tier,$summoner_division)))
									{
										$mmr_status = 'danger';
										$mmr_msg = lang::trans('profile_mmr_elohell');
									}
									if($summoner_mmr > lol::division_mmr($summoner_tier.'_'.$summoner_division) && $summoner_mmr > lol::division_mmr(lol::tier_onemore($summoner_tier,$summoner_division)))
									{
										$mmr_status = 'teals';
										$mmr_msg = lang::trans('profile_mmr_high');
									}
									if(empty($mmr_status))
									{
										$mmr_status = 'success';
										$mmr_msg = lang::trans('profile_mmr_normal');
									}
								}
								else
								{
									$mmr_status = 'lilac';
									$mmr_msg = lang::trans('profile_mmr_nommr');
								}
								?>
									<div class="alert alert-<?php echo $mmr_status ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $mmr_msg ?>">
                                        <h2 style="margin-top:4px"><?php if($summoner_mmr > 0){ echo lang::trans('profile_mmr').' : '.$summoner_mmr; } else { echo lang::trans('profile_mmr_nommr_title').': '.$summoner_level; }?></h2>
                                    </div>
								</div>
								<!-- Leagues -->
								<div class="col-lg-2 col-md-2 col-sm-2" style="margin-left: 40px; margin-top:10px; text-align:center;">
									<div class="panel panel-default"  style="color:black;height:300px; ">
										<div class="panel-heading">
											<h3 class="panel-title"><?php echo lang::trans('profile_box_title_soloq') ?></h3>
										</div><!-- /.panel-heading -->
										<div class="panel-body">
											<img draggable="false" src="<?php echo URL ?>/game/divisions/<?php echo $summoner_tier ?>/<?php echo $summoner_division ?>.png" style="margin-top:-20px;">
											<h3 style="margin-top:-20px;"><?php echo lol::league_name($summoner_tier).' '; if($summoner_tier != 'U') { echo $summoner_division; } ?></h3>
											<?php if($summoner_tier != 'U') { if($summoner_miniseries == 'false') { echo '<strong>'.$summoner_lp.' '.lang::trans('league_points_short').'</strong>'; } else { lol::series_to_icons($summoner_miniseries_progress); } echo ' - '.$summoner_wins.' '.lang::trans('victory_single').' / '.$summoner_losses.' '.lang::trans('lost_single').' ('.number_format((($summoner_wins/($summoner_wins+$summoner_losses))*100),1).'%)<br> KDA: 2.3/5.4/30.5'; } else{ echo '<br><br>'; }?>
										</div><!-- /.panel-body -->
									</div>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2" style="margin-top:10px; text-align:center;">
									<div class="panel panel-default"  style="color:black;height:300px; ">
										<div class="panel-heading">
											<h3 class="panel-title"><?php echo lang::trans('profile_box_title_5x5') ?></h3>
										</div><!-- /.panel-heading -->
										<div class="panel-body">
											<img draggable="false" src="<?php echo URL ?>/game/divisions/<?php echo $summoner_5x5_tier ?>/<?php echo $summoner_5x5_division ?>.png" style="margin-top:-20px;">
											<h3 style="margin-top:-20px;"><?php echo lol::league_name($summoner_5x5_tier).' '; if($summoner_5x5_tier != 'U') { echo $summoner_5x5_division; } ?></h3>
											<?php if($summoner_5x5_tier != 'U') { echo '<strong>'.$summoner_5x5_lp.' '.lang::trans('league_points_short').'</strong> - '.$summoner_5x5_wins.' '.lang::trans('victory_single').' / '.$summoner_5x5_losses.' '.lang::trans('lost_single').' ('.number_format((($summoner_5x5_wins/($summoner_5x5_wins+$summoner_5x5_losses))*100),1).'%)<br> KDA: 2.3/5.4/30.5'; } else{ echo '<br><br>'; }?>
										</div><!-- /.panel-body -->
									</div>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-2" style="margin-top:10px; text-align:center;">
									<div class="panel panel-default"  style="color:black;height:300px; ">
										<div class="panel-heading">
											<h3 class="panel-title"><?php echo lang::trans('profile_box_title_3x3') ?></h3>
										</div><!-- /.panel-heading -->
										<div class="panel-body">
											<img draggable="false" src="<?php echo URL ?>/game/divisions/<?php echo $summoner_3x3_tier ?>/<?php echo $summoner_3x3_division ?>.png" style="margin-top:-20px;">
											<h3 style="margin-top:-20px;"><?php echo lol::league_name($summoner_3x3_tier).' '; if($summoner_3x3_tier != 'U') { echo $summoner_3x3_division; } ?></h3>
											<?php if($summoner_3x3_tier != 'U') { echo '<strong>'.$summoner_3x3_lp.' '.lang::trans('league_points_short').'</strong> - '.$summoner_3x3_wins.' '.lang::trans('victory_single').' / '.$summoner_3x3_losses.' '.lang::trans('lost_single').' ('.number_format((($summoner_3x3_wins/($summoner_3x3_wins+$summoner_3x3_losses))*100),1).'%)<br> KDA: 2.3/5.4/30.5'; } else{ echo '<br><br>'; }?>
										</div><!-- /.panel-body -->
									</div>
								</div>
								<div class="col-lg-5 col-md-5 col-sm-5" style="margin-top:-175px; margin-left:45px; text-align:center;">
									<div class="panel panel-default">
										<!-- Start area chart -->
										<div class="panel rounded shadow panel-info">
											<div class="panel-body">
												<div id="c3js-area-chart" class="chart" style="height:180px;"></div>
											</div><!-- /.panel-body -->
										</div><!-- /.panel -->
										<!--/ End area chart -->
									</div>
								</div>
                                <!-- Start offcanvas btn group menu: This menu will take position at the top of profile cover (mobile only). -->
                                <div class="btn-group cover-menu-mobile hidden-lg hidden-md">
                                    <button type="button" class="btn btn-theme btn-sm dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right no-border" role="menu">
                                        <li class="active"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>"><i class="fa fa-fw fa-clock-o"></i> <span><?php echo lang::trans('profile_menu_history') ?></span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/stats"><i class="fa fa-fw fa-user"></i> <span><?php echo lang::trans('profile_menu_stats') ?></span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/runes"><i class="fa fa-fw fa-photo"></i> <span><?php echo lang::trans('profile_menu_runes') ?></span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/masteries"><i class="fa fa-fw fa-users"></i><span> <?php echo lang::trans('profile_menu_masteries') ?> </span></a></li>
                                        <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/champmastery"><i class="fa fa-fw fa-envelope"></i> <span><?php echo lang::trans('profile_menu_champmastery') ?></span></a></li>
										<li class="pull-right"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>&&reload=true"><i class="fa fa-fw fa-refresh"></i> <span><?php echo lang::trans('profile_menu_reload') ?></span></a></li>
                                    </ul>
                                </div>
                            </div>
                            <ul class="list-unstyled no-padding hidden-sm hidden-xs cover-menu">
                                <li class="active"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>"><i class="fa fa-fw fa-clock-o"></i> <span><?php echo lang::trans('profile_menu_history') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/stats"><i class="fa fa-line-chart"></i> <span><?php echo lang::trans('profile_menu_stats') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/runes"><i class="fa fa-cubes"></i> <span><?php echo lang::trans('profile_menu_runes') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/masteries"><i class="fa fa-bookmark"></i> <span><?php echo lang::trans('profile_menu_masteries') ?></span></a></li>
                                <li><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>/champmastery"><i class="fa fa-fire"></i> <span><?php echo lang::trans('profile_menu_champmastery') ?></span></a></li>
								<li class="pull-right"><a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>&&reload=true"><i class="fa fa-fw fa-refresh"></i> <span><?php echo lang::trans('profile_menu_reload') ?></span></a></li>
                            </ul>
                        </div><!-- /.cover -->
                    </div><!-- /.profile-cover -->
                    </div>
					
					<div class="col-lg-3 col-md-3 col-sm-4">
                    <div class="divider"></div>

                        <div class="panel panel-theme rounded shadow">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <h3 class="panel-title"><?php echo lang::trans('profile_box_title') ?></h3>
                                </div>
                                <div class="pull-right">
                                    <a href="<?php echo URL ?>/summoner/<?php echo $region ?>/<?php echo $summoner_name ?>&&reload=true" class="btn btn-sm btn-success"><i class="fa fa-refresh"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div><!-- /.panel-heading -->
                            <div class="panel-body no-padding rounded">
                               <div class="inner-all">
                                    <ul class="list-unstyled">
                                        <li>
                                            <a href="" class="btn btn-success text-center btn-block">poner aqui los consejos</a>
                                        </li>
                                        <li><br/></li>
                                        <li>
                                            <div class="btn-group-vertical btn-block">
                                                <a href="" class="btn btn-default"><i class="fa fa-cog pull-right"></i>Edit Account</a>
                                                <a href="" class="btn btn-default"><i class="fa fa-sign-out pull-right"></i>Logout</a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div><!-- /.panel-body -->
                        </div><!-- /.panel -->
					</div>
					
					<div class="col-lg-9 col-md-9 col-sm-8">
                    <div class="divider"></div>
                    <div class="row">
                        <div class="col-md-12">
                            
                              <!-- Start repeater -->
                                    <div class="fuelux">
                                        <div class="repeater" data-staticheight="800" id="myRepeater">
                                            <div class="repeater-header">
                                                <div class="repeater-header-left">
                                                    <div class="repeater-search">
                                                        <div class="search input-group">
                                                            <input type="search" class="form-control" placeholder="<?php echo lang::trans('games_table_search_placeholder') ?>"/>
                                                          <span class="input-group-btn">
                                                            <button class="btn btn-default" type="button">
                                                                <span class="glyphicon glyphicon-search"></span>
                                                                <span class="sr-only"><?php echo lang::trans('games_table_search') ?></span>
                                                            </button>
                                                          </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="repeater-header-right">
                                                    <div class="btn-group selectlist repeater-filters" data-resize="auto">
                                                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                                            <span class="selected-label">&nbsp;</span>
                                                            <span class="caret"></span>
                                                            <span class="sr-only"><?php echo lang::trans('games_table_filter') ?></span>
                                                        </button>
                                                        <ul id="test" class="dropdown-menu" role="menu">
                                                            <li data-value="all" data-selected="true" class="text-left"><a href="#"><?php echo lang::trans('games_table_filter') ?></a></li>
                                                            <li data-value="ranked"><a href="#"><?php echo lang::trans('games_table_filter_ranked') ?></a></li>
                                                            <li data-value="ranked_team"><a href="#"><?php echo lang::trans('games_table_filter_rankedteam') ?></a></li>
                                                            <li data-value="normal"><a href="#"><?php echo lang::trans('games_table_filter_normal') ?></a></li>
                                                            <li data-value="aram"><a href="#"><?php echo lang::trans('games_table_filter_aram') ?></a></li>
                                                        </ul>
                                                        <input class="hidden hidden-field" name="filterSelection" readonly="readonly" aria-hidden="true" type="text"/>
                                                    </div>
                                                    <div class="btn-group repeater-views" data-toggle="buttons">
                                                        <label class="btn btn-success active">
                                                            <input name="repeaterViews" type="radio" value="list"><span class="glyphicon glyphicon-list"></span>
                                                        </label>
                                                        <label class="btn btn-success">
                                                            <input name="repeaterViews" type="radio" value="thumbnail"><span class="glyphicon glyphicon-th"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="repeater-viewport">
                                                <div class="repeater-canvas"></div>
                                                <div class="loader repeater-loader"></div>
                                            </div>
                                            <div class="repeater-footer">
                                                <div class="repeater-footer-left">
                                                    <div class="repeater-itemization">
                                                        <span><span class="repeater-start"></span> - <span class="repeater-end"></span> <?php echo lang::trans('games_table_matches_per_game_of') ?> <span class="repeater-count"></span> <?php echo lang::trans('games_table_matches_per_game_matches') ?></span>
                                                        <div class="btn-group selectlist" data-resize="auto">
                                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                                <span class="selected-label">&nbsp;</span>
                                                                <span class="caret"></span>
                                                                <span class="sr-only"><?php echo lang::trans('games_table_matches_per_game') ?></span>
                                                            </button>
                                                            <ul class="dropdown-menu" role="menu">
                                                                <li data-value="5"><a href="#">5</a></li>
                                                                <li data-value="10" data-selected="true"><a href="#">10</a></li>
                                                                <li data-value="20"><a href="#">20</a></li>
                                                                <li data-value="50" data-foo="bar" data-fizz="buzz"><a href="#">50</a></li>
                                                                <li data-value="100"><a href="#">100</a></li>
                                                            </ul>
                                                            <input class="hidden hidden-field" name="itemsPerPage" readonly="readonly" aria-hidden="true" type="text"/>
                                                        </div>
                                                        <span><?php echo lang::trans('games_table_matches_per_game_page') ?></span>
                                                    </div>
                                                </div>
                                                <div class="repeater-footer-right">
                                                    <div class="repeater-pagination">
                                                        <button type="button" class="btn btn-default btn-sm repeater-prev">
                                                            <span class="glyphicon glyphicon-chevron-left"></span>
                                                            <span class="sr-only"><?php echo lang::trans('games_table_matches_per_game_prev') ?></span>
                                                        </button>
                                                        <label class="page-label" id="myPageLabel"><?php echo lang::trans('games_table_matches_per_game_page_single') ?></label>
                                                        <div class="repeater-primaryPaging active">
                                                            <div class="input-group input-append dropdown combobox">
                                                                <input type="text" class="form-control" aria-labelledby="myPageLabel">
                                                                <div class="input-group-btn">
                                                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                                        <span class="caret"></span>
                                                                        <span class="sr-only"><?php echo lang::trans('games_table_matches_per_game_pagefilter') ?></span>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-right"></ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input type="text" class="form-control repeater-secondaryPaging" aria-labelledby="myPageLabel">
                                                        <span><?php echo lang::trans('games_table_matches_per_game_of') ?> <span class="repeater-pages"></span></span>
                                                        <button type="button" class="btn btn-default btn-sm repeater-next">
                                                            <span class="glyphicon glyphicon-chevron-right"></span>
                                                            <span class="sr-only"><?php echo lang::trans('games_table_matches_per_game_next') ?></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/ End repeater -->
									<br>
                        </div>
                    </div>
                    </div>
                    </div><!-- /.row -->

                </div><!-- /.body-content -->
                <!--/ End body content -->

            </section><!-- /#page-content -->
            <!--/ END PAGE CONTENT -->
        </section><!-- /#wrapper -->
        <!--/ END WRAPPER -->

        <!-- START @BACK TOP -->
        <div id="back-top" class="animated pulse circle">
            <i class="fa fa-angle-up"></i>
        </div><!-- /#back-top -->
        <!--/ END BACK TOP -->

        <!-- START JAVASCRIPT SECTION (Load javascripts at bottom to reduce load time) -->
        <!-- START @CORE PLUGINS -->
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery-cookie/jquery.cookie.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery.gritter/js/jquery.gritter.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/typehead.js/dist/handlebars.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/typehead.js/dist/typeahead.bundle.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery-nicescroll/jquery.nicescroll.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery.sparkline.min/index.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery-easing-original/jquery.easing.1.3.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/ionsound/js/ion.sound.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/bootbox/bootbox.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/retina.js/dist/retina.min.js"></script>
        <!--/ END CORE PLUGINS -->

        <!-- START @PAGE LEVEL PLUGINS -->
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/datatables/js/jquery.dataTables.min.js"></script>
        <script src="<?php echo URL ?>/style/global/plugins/bower_components/fuelux/dist/js/fuelux.min.js"></script>
		<script src="<?php echo URL ?>/style/global/plugins/bower_components/d3/d3.min.js" charset="utf-8"></script>
		<script src="<?php echo URL ?>/style/global/plugins/bower_components/c3js-chart/c3.min.js"></script>
		<script>var BlankonChartC3JS = function () {

    return {

        // =========================================================================
        // CONSTRUCTOR APP
        // =========================================================================
        init: function () {
            BlankonChartC3JS.areaChartC3JS();
        },


        // =========================================================================
        // C3JS CHART / AREA
        // =========================================================================
        areaChartC3JS: function () {
            var chart = c3.generate({
                bindto: '#c3js-area-chart',
                data: {
                    columns: [
                        ['data1', 300, 350, 300, 0, 0, 0],
                        ['data2', 130, 100, 140, 200, 150, 50]
                    ],
                    names: {
                        data1: 'Data 1',
                        data2: 'Data 2'
                    },
                    types: {
                        data1: 'area',
                        data2: 'area-spline'
                    }
                },
                color: {
                    pattern: ['#E9573F', '#00B1E1']
                }
            });
            // Expand panel
            BlankonChartC3JS.expandPanel(chart);
        }
	};

}();

// Call main app init
BlankonChartC3JS.init();
</script>
        <!--/ END PAGE LEVEL PLUGINS -->

        <!-- START @PAGE LEVEL SCRIPTS -->
        <script src="<?php echo URL ?>/style/home/js/apps.js"></script>
        <script>'use strict';
var BlankonTable = function () {

    // =========================================================================
    // SETTINGS APP
    // =========================================================================
    var globalPluginsPath = BlankonApp.handleBaseURL()+'/assets/global/plugins/bower_components';

    return {

        // =========================================================================
        // CONSTRUCTOR APP
        // =========================================================================
        init: function () {
            BlankonTable.datatable();
        },

        // =========================================================================
        // DATATABLE
        // =========================================================================
        datatable: function () {
            var responsiveHelperAjax = undefined;
            var responsiveHelperDom = undefined;
            var breakpointDefinition = {
                tablet: 1024,
                phone : 480
            };

            var tableDom = $('#datatable-dom');

            // Using DOM
            // Remove arrow datatable
            $.extend( true, $.fn.dataTable.defaults, {
                "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0, 1, 2, 5 ] } ]
            } );
            tableDom.dataTable({
                autoWidth        : false,
                preDrawCallback: function () {
                    // Initialize the responsive datatables helper once.
                    if (!responsiveHelperDom) {
                        responsiveHelperDom = new ResponsiveDatatablesHelper(tableDom, breakpointDefinition);
                    }
                },
                rowCallback    : function (nRow) {
                    responsiveHelperDom.createExpandIcon(nRow);
                },
                drawCallback   : function (oSettings) {
                    responsiveHelperDom.respond();
                }
            });

            // Repeater
            var columns = [
                {
                    label: '<?php echo lang::trans('games_table_head_name_image') ?>',
                    property: 'row_1',
                    sortable: false,
					width:'15%'
                },
                {
                    label: '<?php echo lang::trans('games_table_head_name_items') ?>',
                    property: 'row_3',
                    sortable: true,
					width:'20%'
                },
                {
                    label: '<?php echo lang::trans('games_table_head_name_summoners') ?>',
                    property: 'row_4',
                    sortable: true,
					width:'30%'
                },
                {
                    label: 'Item Condition',
                    property: 'itemCondition',
                    sortable: true
                },
                {
                    label: 'Sold',
                    property: 'sold',
                    sortable: true
                },
                {
                    label: 'Review',
                    property: 'review',
                    sortable: true
                }
            ];
            var delays = ['300', '600', '900', '1200'];
            var products = [
			<?php
			$games = $db->query('SELECT id,creation_timestamp,participants,queue FROM lol_matches WHERE summoner_ids LIKE "%'.$summoner_id.'%" ORDER BY creation_timestamp DESC');
			$games_count = 0;
			while($row = $games ->fetch_row())
			{
				if($games_count != 0)
				{
					echo ',';
				}
				$all_players_data = array();
				$all_players_data['100'] = array();
				$all_players_data['200'] = array();
				foreach(json_decode($row[2],true) as $game_summoner_id => $game_summoner_data)
				{
					if($game_summoner_data['team_id'] == 100)
					{
						$team_side = 'right';
						$team_side_anverse = 'left';
					}
					else
					{
						$team_side = 'left';
						$team_side_anverse = 'right';
					}
					$all_players_data[$game_summoner_data['team_id']][] = '<div style=\"float:'.$team_side_anverse.'\"><div style=\"float:'.$team_side_anverse.'\"><img height=\"20px\" src=\"'.URL.'/game/champions/'.lol::champ_id2key($game_summoner_data['champ_id']).'/base/0.png\"></div> '.lol::summoner_id2name($game_summoner_id).'</div>';
				}
				$all_players_data_rows = null;
				if(count($all_players_data[100]) == count($all_players_data[200]) && count($all_players_data[200]) > 0 && count($all_players_data[100]) > 0)
				{
					for($i = 0; $i < count($all_players_data[100]); $i++)
					{
						if($i != 0)
						{
							$gamerow_margin_top = '2px';
						}
						else
						{
							$gamerow_margin_top = '0px';
						}
						$all_players_data_rows .= '<div class=\"row\" style=\"margin-top:'.$gamerow_margin_top.'\"><div class=\"col-md-6\">'.$all_players_data[100][$i].'</div> <div class=\"col-md-6\">'.$all_players_data[200][$i].'</div></div>';
					}
				}
				else
				{
					
				}
				$player_data = json_decode($row[2],true)[$summoner_id];
				echo '{
                    "row_1": "<div class=\"row\" style=\"text-align:center;\">'.lol::champ_id2name($player_data['champ_id']).'</div><br><div class=\"col-md-8\"><img style=\"max-width:100%;width:70px;\" height=\"70px\" src=\"'.URL.'/game/champions/'.lol::champ_id2key($player_data['champ_id']).'/base/0.png\"></div><div class=\"col-md-3\" style=\"margin-left:1%;padding: 0 !important;margin: 0 !important;\"><img style=\"max-width:100%;width:35px;\" height=\"35px\" src=\"'.URL.'/game/spells/'.lol::summonerspell_id2key($player_data['spell_1']).'.png\"><img style=\"max-width:100%;width:35px;\" height=\"35px\" src=\"'.URL.'/game/spells/'.lol::summonerspell_id2key($player_data['spell_2']).'.png\"></div>",
                    "row_3": "<div class=\"col-md-3\"><img width=\"40px\" height=\"40px\" src=\"'.URL.'/game/items/'.(int)@$player_data['items'][0].'.png\"><img width=\"40px\" height=\"40px\" src=\"'.URL.'/game/items/'.(int)@$player_data['items'][3].'.png\"></div><div class=\"col-md-3\"><img width=\"40px\" height=\"40px\" src=\"'.URL.'/game/items/'.(int)@$player_data['items'][1].'.png\"><img width=\"40px\" height=\"40px\" src=\"'.URL.'/game/items/'.(int)@$player_data['items'][4].'.png\"></div><div class=\"col-md-3\"><img width=\"40px\" height=\"40px\" src=\"'.URL.'/game/items/'.(int)@$player_data['items'][2].'.png\"><img width=\"40px\" height=\"40px\" src=\"'.URL.'/game/items/'.(int)@$player_data['items'][5].'.png\"></div><div class=\"col-md-3\" style=\"margin-top:20px\"><img width=\"40px\" height=\"40px\" src=\"'.URL.'/game/items/'.(int)@$player_data['items'][6].'.png\"></div>",
                    "row_4": "'.$all_players_data_rows.'",
                    "itemCondition": "Manufacturer",
                    "sold": "5",
                    "review": "253 people",
                    "ThumbnailAltText": "Canon EOS Rebel",
                    "ThumbnailImage": "'.URL.'/game/champions/'.lol::champ_id2key($player_data['champ_id']).'/base/0.png",
                    "type": "'.$row[3].'",
                    "thumb_name": "'.lol::champ_id2name($player_data['champ_id']).'"
                }';
				$games_count++;
			}
			?>
            ];
            var dataSource, filtering;

            dataSource = function(options, callback){
                var items = filtering(options);
                var resp = {
                    count: items.length,
                    items: [],
                    page: options.pageIndex,
                    pages: Math.ceil(items.length/(options.pageSize || 50))
                };
                var i, items, l;

                i = options.pageIndex * (options.pageSize || 50);
                l = i + (options.pageSize || 50);
                l = (l <= resp.count) ? l : resp.count;
                resp.start = i + 1;
                resp.end = l;

                if(options.view==='list' || options.view==='thumbnail'){
                    if(options.view==='list'){
                        resp.columns = columns;
                        for(i; i<l; i++){
                            resp.items.push(items[i]);
                        }
                    }else{
                        for(i; i<l; i++){
                            resp.items.push({
                                name: items[i].thumb_name,
                                src: items[i].ThumbnailImage
                            });
                        }
                    }

                    setTimeout(function(){
                        callback(resp);
                    }, delays[Math.floor(Math.random() * 4)]);
                }
            };

            filtering = function(options){
                var items = $.extend([], products);
                var search;
                if(options.filter.value!=='all'){
                    items = $.grep(items, function(item){
                        return (item.type.search(options.filter.value)>=0);
                    });
                }
                if(options.search){
                    search = options.search.toLowerCase();
                    items = $.grep(items, function(item){
                        return (
                        (item.row_2.toLowerCase().search(options.search)>=0) ||
                        (item.row_4.toLowerCase().search(options.search)>=0)
                        );
                    });
                }
                if(options.sortProperty){
                    items = $.grep(items, function(item){
                        if(options.sortProperty==='id' || options.sortProperty==='height' || options.sortProperty==='weight'){
                            return parseFloat(item[options.sortProperty]);
                        }else{
                            return item[options.sortProperty];
                        }
                    });
                    if(options.sortDirection==='desc'){
                        items.reverse();
                    }
                }

                return items;
            };

            // REPEATER
            $('#repeaterIllustration').repeater({
                dataSource: dataSource
            });

            $('#myRepeater').repeater({
                dataSource: dataSource
            });

            $('#myRepeaterList').repeater({
                dataSource: dataSource
            });

            $('#myRepeaterThumbnail').repeater({
                dataSource: dataSource,
                thumbnail_template: '<div class="thumbnail repeater-thumbnail" style="background: {{color}};"><img height="75" src="{{src}}" width="65"><span>{{name}}</span></div>'
            });

        }

    };

}();

// Call main app init
BlankonTable.init();</script>
        <!--/ END PAGE LEVEL SCRIPTS -->
        <!--/ END JAVASCRIPT SECTION -->

        <!-- START GOOGLE ANALYTICS -->
        <?php echo template::analytics(); ?>
        <!--/ END GOOGLE ANALYTICS -->
		<?php
		if(!empty($load_notification_error_reload_apibusy))
		{
			echo '<script>$.gritter.add({
					title: \''.lang::trans('server_api_too_busy').'\',
					text: \''.lang::trans('server_api_too_busy_sub').'\',
					image: \'/style/home/images/lee_notification.png\',
					sticky: false,
					time: \'4000\'
			});</script>';
		}
		?>
    </body>
    <!--/ END BODY -->

</html>
