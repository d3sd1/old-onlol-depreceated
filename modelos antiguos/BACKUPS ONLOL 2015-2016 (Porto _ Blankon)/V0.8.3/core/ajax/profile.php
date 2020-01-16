<?php
ini_set('display_errors', 1);
if(!empty($_GET['region']) or !empty($_GET['summoner']))
{
	require('../core.php');
	set_time_limit(0); 
	ob_implicit_flush(true);
	ob_end_flush();
	$actual_progress = 0;
	
	$region = parseserver($_GET['region']);
	$summoner_name = $_GET['summoner'];
	$summoner_id = 0;
	$load_rkddata = 0;
	/* Verify if user exists */
	if($db->query('SELECT id FROM inv_users WHERE region="'.$region.'" AND name="'.$summoner_name.'"')->num_rows == 0)
	{
		$check_summoner_exists = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.4/summoner/by-name/'.format_summoner_name($summoner_name).'?api_key='.LOL_API_KEY);
		if($check_summoner_exists == 'NOT_FOUND')
		{
			die(redirect(URL.'?not_found_user='.$summoner_name.'&region='.$region));
		}
		else
		{
			$summoner_id = $check_summoner_exists[strtolower(str_replace(' ', null,$summoner_name))]['id'];
		}
		$user_was_on_db = false;
	}
	else
	{
		$summoner_id = $db->query('SELECT summoner_id FROM inv_users WHERE name="'.$summoner_name.'" AND region="'.$region.'"')->fetch_row()[0];
		$user_was_on_db = true;
	}
	/* Fix for extrange queries */
	if(!empty($_GET['summoner_id']))
	{
		$summoner_id = $_GET['summoner_id'];
	}
	/* Keep loading up */
	if($user_was_on_db == false OR $db->query('SELECT onlol_last_update FROM inv_users WHERE name="'.$summoner_name.'" AND region="'.$region.'"')->fetch_row()[0] + config('profilereload_interval') < time())
	{
		for($task = 0; $task < 10; $task++){
			if($task == 0)
			{
				$response = array(  'message' => 'Actualizando perfil...', 'progress' => $actual_progress);		
			}
			if($task == 1)
			{
				$actual_progress = $actual_progress + 10;
				$response = array(  'message' => 'Actualizando información básica...', 'progress' => $actual_progress);
				
				$summonerbase = str_replace(' ', '', strtolower($summoner_name));
				$all_summoners_str = str_replace(' ', '', strtolower($summoner_name));
				$all_summoners_count = 0;
				$all_summoners = $db->query('SELECT name FROM inv_users WHERE basicinfo_last_update < '.(time()-config('profile_autorenew')).' AND region="'.$region.'" LIMIT '.(config('max_invreloads_per_time')-1));
				while($row = $all_summoners->fetch_row())
				{
					if(str_replace(' ', '', strtolower($row[0])) != $summonerbase)
					{
						$all_summoners_str .= ',';
						$all_summoners_str .= format_summoner_name($row[0]);
					}
					$all_summoners_count++;
				}
				
				 /* Get another update summoners */
				$summoner_info = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.4/summoner/by-name/'.$all_summoners_str.'?api_key='.LOL_API_KEY);
				$countforeachinfo = 0;
				foreach($summoner_info as $basename => $data)
				{
					if($basename == format_summoner_name($summoner_name))
					{
						if($summoner_id == 0)
						{
							$summoner_id = $data['id'];
						}
						if($db->query('SELECT lol_last_update FROM inv_users WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'"')->fetch_row()[0] < datetounix($data['revisionDate']))
						{
							$reload_common_data = true;
						}
						else
						{
							$reload_common_data = false;
						}
					}
					/* Add summoner baseinfo to database */
					if($db->query('SELECT id FROM inv_users WHERE summoner_id='.$data['id'].'')->num_rows == 0)
					{
						$db->query('INSERT INTO inv_users (summoner_id,name,region,level,icon,lol_last_update,basicinfo_last_update) VALUES ('.$data['id'].',"'.$data['name'].'","'.$region.'",'.$data['summonerLevel'].','.$data['profileIconId'].','.$data['revisionDate'].','.time().')') or die($db->error);
					}
					else
					{
						$db->query('UPDATE inv_users SET name="'.$data['name'].'",region="'.$region.'",level="'.$data['summonerLevel'].'",icon="'.$data['profileIconId'].'",lol_last_update="'.$data['revisionDate'].'",basicinfo_last_update='.time().' WHERE summoner_id='.$data['id'].'') or die($db->error);
					}
					$countforeachinfo++;
				}
			}
			if(empty($reload_common_data) or @$reload_common_data != false)
			{
				/* $summoner_id avaliable */
				if($task == 2)
				{
					$level = $db->query('SELECT level FROM inv_users WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'"')->fetch_row()[0];
					$actual_progress = $actual_progress + 10;
					$response = array(  'message' => 'Actualizando ligas...', 'progress' => $actual_progress);
					
					if((int) $level == 30)
					{
							/* Unranked summoner info */
							$db->query('UPDATE inv_users SET ranked_league="U",ranked_division="1",ranked_division_name="UNRANKED",ranked_lp="0",ranked_wins="0",ranked_losses="0",ranked_streak="false",ranked_veteran="false",ranked_newbbie="false",ranked_inactive="false" WHERE summoner_id="'.$summoner_id.'" LIMIT 1') or die($db->error);
						
							/* Leagues */
							/* Optimizer: Add some more players to the query for save some querys on api */
							$players = $summoner_id;
							$players_query = $db->query('SELECT summoner_id FROM inv_users WHERE league_last_update  < '.(time() - config('profile_leagueupdate_interval')).' AND region="'.$region.'" AND ranked_league!="U" AND summoner_id!='.$summoner_id.' ORDER BY league_last_update ASC LIMIT '.(config('max_leaguereloads_per_time')-1));
							while($row = $players_query->fetch_row())
							{
								$players .= ','.$row[0];
							}
							
							$teams = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v2.5/league/by-summoner/'.$players.'/entry?api_key='.LOL_API_KEY);
							$summonerteams_ids = explode(',',$players);
							$this_summonerteam_updating = 0;
							/* Clear previous teams, for if the user left someone */
							$summoner_teams = $db->query('SELECT team_id,summoner_ids FROM inv_users_teams WHERE summoner_ids LIKE "%'.$summonerteams_ids[$this_summonerteam_updating].'%"');
							while($row = $summoner_teams->fetch_row())
							{
								$new_summonerteam_list = str_replace($summonerteams_ids[$this_summonerteam_updating],null,$row[1]);
								$new_summonerteam_list = str_replace(';;',';',$new_summonerteam_list);
								$db->query('UPDATE inv_users_teams SET summoner_ids="'.$new_summonerteam_list.'" WHERE team_id="'.$row[0].'"');
							}
							
							while($this_summonerteam_updating < count($summonerteams_ids) && is_array($teams))
							{
								if(array_key_exists($summonerteams_ids[$this_summonerteam_updating],$teams))
								{
									$db->query('UPDATE inv_users SET league_last_update='.time().' WHERE summoner_id='.$summonerteams_ids[$this_summonerteam_updating].' AND region="'.$region.'"');
									
									foreach($teams[$summonerteams_ids[$this_summonerteam_updating]] as $data)
									{
										if($data['entries'][0]['isHotStreak'] == null) {$streak = 'false';} else {$streak = $data['entries'][0]['isHotStreak'];}
										if($data['entries'][0]['isVeteran'] == null) {$veteran = 'false';} else {$veteran = $data['entries'][0]['isVeteran'];}
										if($data['entries'][0]['isFreshBlood'] == null) {$newbbie = 'false';} else {$newbbie = $data['entries'][0]['isFreshBlood'] ;}
										if($data['entries'][0]['isInactive'] == null) {$inactive = 'false';} else {$inactive = $data['entries'][0]['isInactive'];}
										
										/* Ranked soloQ data */
										if($data['queue'] == 'RANKED_SOLO_5x5')
										{
											$db->query('UPDATE inv_users SET ranked_league="'.strtoupper(substr($data['tier'], 0,1)).'",ranked_division="'.parsedisivion($data['entries'][0]['division']).'",ranked_division_name="'.$data['name'].'",ranked_lp="'.$data['entries'][0]['leaguePoints'].'",ranked_wins="'.$data['entries'][0]['wins'].'",ranked_losses="'.$data['entries'][0]['losses'].'",ranked_streak="'.$streak.'",ranked_veteran="'.$veteran.'",ranked_newbbie="'.$newbbie.'",ranked_inactive="'.$inactive.'" WHERE summoner_id="'.$summonerteams_ids[$this_summonerteam_updating].'" AND region="'.$region.'" LIMIT 1') or die($db->error);
										}	
										
										/* Ranked 5x5 / 3x3 data */
										if($data['queue'] == 'RANKED_TEAM_5x5' or $data['queue'] == 'RANKED_TEAM_3x3')
										{
											if($data['queue'] == 'RANKED_TEAM_5x5')
											{
												$queue = '5x5';
											}
											elseif($data['queue'] == 'RANKED_TEAM_3x3')
											{
												$queue = '3x3';
											}
											/* Start to add teamdata */
											if($db->query('SELECT id FROM inv_users_teams WHERE team_id="'.$data['entries']['playerOrTeamId'].'" AND qeue="'.$queue.'"')->num_rows > 0)
											{
												if($db->query('SELECT id FROM inv_users_teams WHERE team_id="'.$data['entries']['playerOrTeamId'].'" AND qeue="'.$queue.'" AND summoner_ids NOT LIKE '.$summonerteams_ids[$this_summonerteam_updating].' LIMIT 1')->num_rows > 0)
												{
													$db->query('UPDATE inv_users_teams SET summoner_ids=(CONCAT(summoner_ids, ";'.$summonerteams_ids[$this_summonerteam_updating].'")) WHERE team_id="'.$data['entries']['playerOrTeamId'].'" AND qeue="'.$queue.'" LIMIT 1') or die($db->error);
												}
												$db->query('UPDATE inv_users_teams SET league="'.strtoupper(substr($data['tier'], 0,1)).'",division="'.parsedisivion($data['entries'][0]['division']).'",lp="'.$data['entries'][0]['leaguePoints'].'",wins="'.$data['entries'][0]['wins'].'",losses="'.$data['entries'][0]['losses'].'",streak="'.$streak.'",veteran="'.$veteran.'",newbbie="'.$newbbie.'",inactive="'.$inactive.'" WHERE team_id="'.$data['entries'][0]['playerOrTeamId'].'" AND qeue="'.$queue.'" LIMIT 1') or die($db->error);
											}
											else
											{
												$db->query('INSERT INTO inv_users_teams (summoner_ids,team_name,league,division,lp,wins,losses,streak,veteran,newbbie,inactive,team_id,qeue) VALUES ('.$summonerteams_ids[$this_summonerteam_updating].',"'.$data['entries'][0]['playerOrTeamName'].'","'.strtoupper(substr($data['tier'], 0,1)).'","'.parsedisivion($data['entries'][0]['division']).'","'.$data['entries'][0]['leaguePoints'].'","'.$data['entries'][0]['wins'].'","'.$data['entries'][0]['losses'].'","'.$streak.'","'.$veteran.'","'.$newbbie.'","'.$inactive.'","'.$data['entries'][0]['playerOrTeamId'].'","'.$queue.'")') or die($db->error);
											}
										}
									}
									/* MMR */
									onlol::mmr($summonerteams_ids[$this_summonerteam_updating], $region, $db->query('SELECT ranked_lp FROM inv_users WHERE summoner_id="'.$summonerteams_ids[$this_summonerteam_updating].'" AND region="'.$region.'" LIMIT 1')->fetch_row()[0], $db->query('SELECT ranked_league FROM inv_users WHERE summoner_id="'.$summonerteams_ids[$this_summonerteam_updating].'" AND region="'.$region.'" LIMIT 1')->fetch_row()[0], $db->query('SELECT ranked_division FROM inv_users WHERE summoner_id="'.$summonerteams_ids[$this_summonerteam_updating].'" AND region="'.$region.'" LIMIT 1')->fetch_row()[0]);
								}
								$this_summonerteam_updating++;
							}
					}
					else
					{
						/* Information for summoners which level is minor than 30 */
							$db->query('UPDATE inv_users SET ranked_league="U",ranked_division="1",ranked_division_name="UNRANKED",ranked_lp="0",ranked_wins="0",ranked_losses="0",ranked_streak="false",ranked_veteran="false",ranked_newbbie="false",ranked_inactive="false" WHERE summoner_id="'.$summoner_id.'" LIMIT 1') or die($db->error);
					}
				}
				if($task == 3)
				{
					$actual_progress = $actual_progress + 10;
					$response = array(  'message' => 'Actualizando estadísticas de clasificatorias...', 'progress' => $actual_progress);
					$league = $db->query('SELECT ranked_league FROM inv_users WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'"')->fetch_row()[0];
					
					if($load_rkddata == 1) //This variable is set from the lastest update function; just reload info if the summoner played SOLOQ
					{
						if($level == '30' && $league != 'U')
						{
							$seasondata_array = array();
							foreach($lol_seasons as $this_season)
							{
								if(empty(stdtoarray(json_decode($db->query('SELECT data FROM inv_users_rankedstats WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'"')->fetch_row()[0]))))
								{
									
									$seasondata_array[$this_season] = array();
									$seasondata = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.3/stats/by-summoner/'.$summoner_id.'/summary?season='.$this_season.'&api_key='.LOL_API_KEY);
									
									$championdata = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.3/stats/by-summoner/'.$summoner_id.'/ranked?season='.$this_season.'&api_key='.LOL_API_KEY);
									$champions = null;
									
									if(!empty($seasondata['playerStatSummaries']))
									{
										$this_readingdata = 0;
										$seasondata_array[$this_season] = array();
										while($this_readingdata < count($seasondata['playerStatSummaries']))
										{
											/* Season data */
											if($seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedPremade3x3' or $seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedTeam3x3')
											{
												$seasondata_array[$this_season]['ranked_team_3x3']['wins'] = @$seasondata['playerStatSummaries'][$this_readingdata]['wins'];
												$seasondata_array[$this_season]['ranked_team_3x3']['losses'] = @$seasondata['playerStatSummaries'][$this_readingdata]['losses'];
												$seasondata_array[$this_season]['ranked_team_3x3']['kills'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalChampionKills'];
												$seasondata_array[$this_season]['ranked_team_3x3']['turrets'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalTurretsKilled'];
												$seasondata_array[$this_season]['ranked_team_3x3']['minions'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalMinionKills'];
												$seasondata_array[$this_season]['ranked_team_3x3']['assists'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalAssists'];
											}
											if($seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedPremade5x5' or $seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedTeam5x5')
											{
												$seasondata_array[$this_season]['ranked_team_5x5']['wins'] = @$seasondata['playerStatSummaries'][$this_readingdata]['wins'];
												$seasondata_array[$this_season]['ranked_team_5x5']['losses'] = @$seasondata['playerStatSummaries'][$this_readingdata]['losses'];
												$seasondata_array[$this_season]['ranked_team_5x5']['kills'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalChampionKills'];
												$seasondata_array[$this_season]['ranked_team_5x5']['turrets'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalTurretsKilled'];
												$seasondata_array[$this_season]['ranked_team_5x5']['minions'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalMinionKills'];
												$seasondata_array[$this_season]['ranked_team_5x5']['assists'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalAssists'];
											}
											if($seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedSolo5x5')
											{
												$seasondata_array[$this_season]['ranked_solo_5x5']['wins'] = @$seasondata['playerStatSummaries'][$this_readingdata]['wins'];
												$seasondata_array[$this_season]['ranked_solo_5x5']['losses'] = @$seasondata['playerStatSummaries'][$this_readingdata]['losses'];
												$seasondata_array[$this_season]['ranked_solo_5x5']['kills'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalChampionKills'];
												$seasondata_array[$this_season]['ranked_solo_5x5']['turrets'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalTurretsKilled'];
												$seasondata_array[$this_season]['ranked_solo_5x5']['minions'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalMinionKills'];
												$seasondata_array[$this_season]['ranked_solo_5x5']['assists'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalAssists'];
											}
											
											/* Champions data */
											$keep_alive_champions = true;
											$this_champ_info = 0;
											if($championdata != 'NOT_FOUND')
											{
												$seasondata_array[$this_season]['ranked_champions']['champions'] = array();
												while($keep_alive_champions == true)
												{
													if(!empty($championdata['champions'][$this_champ_info]))
													{
														$champ_id = $championdata['champions'][$this_champ_info]['id'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id] = $champ_id;
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id] = array();
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['wins'] = $championdata['champions'][$this_champ_info]['stats']['totalSessionsWon'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['losses'] = $championdata['champions'][$this_champ_info]['stats']['totalSessionsLost'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['kills'] = $championdata['champions'][$this_champ_info]['stats']['totalChampionKills'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['deaths'] = $championdata['champions'][$this_champ_info]['stats']['totalDeathsPerSession'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['assists'] = $championdata['champions'][$this_champ_info]['stats']['totalAssists'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['dmg_dealt'] = $championdata['champions'][$this_champ_info]['stats']['totalDamageDealt'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['dmg_dealt_ad'] = $championdata['champions'][$this_champ_info]['stats']['totalPhysicalDamageDealt'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['dmg_dealt_ap'] = $championdata['champions'][$this_champ_info]['stats']['totalMagicDamageDealt'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['dmg_taken'] = $championdata['champions'][$this_champ_info]['stats']['totalDamageTaken'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['maxkillsonsinglegame'] = $championdata['champions'][$this_champ_info]['stats']['mostChampionKillsPerSession'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['maxdeathsonsinglegame'] = $championdata['champions'][$this_champ_info]['stats']['maxNumDeaths'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['minions'] = $championdata['champions'][$this_champ_info]['stats']['totalMinionKills'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['kills_double'] = $championdata['champions'][$this_champ_info]['stats']['totalDoubleKills'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['kills_triple'] = $championdata['champions'][$this_champ_info]['stats']['totalTripleKills'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['kills_quadra'] = $championdata['champions'][$this_champ_info]['stats']['totalQuadraKills'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['kills_penta'] = $championdata['champions'][$this_champ_info]['stats']['totalPentaKills'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['gold'] = $championdata['champions'][$this_champ_info]['stats']['totalGoldEarned'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['turrets'] = $championdata['champions'][$this_champ_info]['stats']['totalTurretsKilled'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['firstblood'] = $championdata['champions'][$this_champ_info]['stats']['totalFirstBlood'];
														
													}
													else
													{
														$keep_alive_champions = false;
													}
													$this_champ_info++;
												}
											}
											$this_readingdata++;
										}
									}
									
									if(empty($seasondata_array[$this_season]))
									{
										unset($seasondata_array[$this_season]);
									}
								}
								elseif($this_season == end($lol_seasons)) //Renew season data if that's the current
								{	
									$seasondata_array[$this_season] = array();
									$seasondata = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.3/stats/by-summoner/'.$summoner_id.'/summary?season='.$this_season.'&api_key='.LOL_API_KEY);
									$championdata = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.3/stats/by-summoner/'.$summoner_id.'/ranked?season='.$this_season.'&api_key='.LOL_API_KEY);
									$champions = null;
									
									if(!empty($seasondata['playerStatSummaries']))
									{
										$this_readingdata = 0;
										$seasondata_array[$this_season] = array();
										while($this_readingdata < count($seasondata['playerStatSummaries']))
										{
											/* Season data */
											if($seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedPremade3x3' or $seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedTeam3x3')
											{
												$seasondata_array[$this_season]['ranked_team_3x3']['wins'] = @$seasondata['playerStatSummaries'][$this_readingdata]['wins'];
												$seasondata_array[$this_season]['ranked_team_3x3']['losses'] = @$seasondata['playerStatSummaries'][$this_readingdata]['losses'];
												$seasondata_array[$this_season]['ranked_team_3x3']['kills'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalChampionKills'];
												$seasondata_array[$this_season]['ranked_team_3x3']['turrets'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalTurretsKilled'];
												$seasondata_array[$this_season]['ranked_team_3x3']['minions'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalMinionKills'];
												$seasondata_array[$this_season]['ranked_team_3x3']['assists'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalAssists'];
											}
											if($seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedPremade5x5' or $seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedTeam5x5')
											{
												$seasondata_array[$this_season]['ranked_team_5x5']['wins'] = @$seasondata['playerStatSummaries'][$this_readingdata]['wins'];
												$seasondata_array[$this_season]['ranked_team_5x5']['losses'] = @$seasondata['playerStatSummaries'][$this_readingdata]['losses'];
												$seasondata_array[$this_season]['ranked_team_5x5']['kills'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalChampionKills'];
												$seasondata_array[$this_season]['ranked_team_5x5']['turrets'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalTurretsKilled'];
												$seasondata_array[$this_season]['ranked_team_5x5']['minions'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalMinionKills'];
												$seasondata_array[$this_season]['ranked_team_5x5']['assists'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalAssists'];
											}
											if($seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedSolo5x5')
											{
												$seasondata_array[$this_season]['ranked_solo_5x5']['wins'] = @$seasondata['playerStatSummaries'][$this_readingdata]['wins'];
												$seasondata_array[$this_season]['ranked_solo_5x5']['losses'] = @$seasondata['playerStatSummaries'][$this_readingdata]['losses'];
												$seasondata_array[$this_season]['ranked_solo_5x5']['kills'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalChampionKills'];
												$seasondata_array[$this_season]['ranked_solo_5x5']['turrets'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalTurretsKilled'];
												$seasondata_array[$this_season]['ranked_solo_5x5']['minions'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalMinionKills'];
												$seasondata_array[$this_season]['ranked_solo_5x5']['assists'] = @$seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalAssists'];
											}
											
											/* Champions data */
											$keep_alive_champions = true;
											$this_champ_info = 0;
											if($championdata != 'NOT_FOUND')
											{
												$seasondata_array[$this_season]['ranked_champions']['champions'] = array();
												while($keep_alive_champions == true)
												{
													if(!empty($championdata['champions'][$this_champ_info]))
													{
														$champ_id = $championdata['champions'][$this_champ_info]['id'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id] = $champ_id;
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id] = array();
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['wins'] = $championdata['champions'][$this_champ_info]['stats']['totalSessionsWon'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['losses'] = $championdata['champions'][$this_champ_info]['stats']['totalSessionsLost'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['kills'] = $championdata['champions'][$this_champ_info]['stats']['totalChampionKills'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['deaths'] = $championdata['champions'][$this_champ_info]['stats']['totalDeathsPerSession'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['assists'] = $championdata['champions'][$this_champ_info]['stats']['totalAssists'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['dmg_dealt'] = $championdata['champions'][$this_champ_info]['stats']['totalDamageDealt'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['dmg_dealt_ad'] = $championdata['champions'][$this_champ_info]['stats']['totalPhysicalDamageDealt'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['dmg_dealt_ap'] = $championdata['champions'][$this_champ_info]['stats']['totalMagicDamageDealt'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['dmg_taken'] = $championdata['champions'][$this_champ_info]['stats']['totalDamageTaken'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['maxkillsonsinglegame'] = $championdata['champions'][$this_champ_info]['stats']['mostChampionKillsPerSession'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['maxdeathsonsinglegame'] = $championdata['champions'][$this_champ_info]['stats']['maxNumDeaths'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['minions'] = $championdata['champions'][$this_champ_info]['stats']['totalMinionKills'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['kills_double'] = $championdata['champions'][$this_champ_info]['stats']['totalDoubleKills'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['kills_triple'] = $championdata['champions'][$this_champ_info]['stats']['totalTripleKills'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['kills_quadra'] = $championdata['champions'][$this_champ_info]['stats']['totalQuadraKills'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['kills_penta'] = $championdata['champions'][$this_champ_info]['stats']['totalPentaKills'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['gold'] = $championdata['champions'][$this_champ_info]['stats']['totalGoldEarned'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['turrets'] = $championdata['champions'][$this_champ_info]['stats']['totalTurretsKilled'];
														$seasondata_array[$this_season]['ranked_champions']['champions'][$champ_id]['firstblood'] = $championdata['champions'][$this_champ_info]['stats']['totalFirstBlood'];
														
													}
													else
													{
														$keep_alive_champions = false;
													}
													$this_champ_info++;
												}
											}
											$this_readingdata++;
										}
									}
									
									if(empty($seasondata_array[$this_season]))
									{
										unset($seasondata_array[$this_season]);
									}
								}
							}
							
							if(!empty($seasondata_array))
							{
								if($db->query('SELECT id FROM inv_users_rankedstats WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'" LIMIT 1')->num_rows == 0)
								{
									$db->query("INSERT INTO inv_users_rankedstats (data,region,summoner_id) VALUES ('".json_encode($seasondata_array)."','".$region."',".$summoner_id.")") or die($db->error);
								}
								else
								{	
									if($db->query('SELECT data FROM inv_users_rankedstats WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'" LIMIT 1')->fetch_row()[0] != null)
									{
										$mergedseasondata = array_merge(stdtoarray(json_decode($db->query('SELECT data FROM inv_users_rankedstats WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'" LIMIT 1')->fetch_row()[0])), $seasondata_array);
										$db->query("UPDATE inv_users_rankedstats SET data='".json_encode($mergedseasondata)."' WHERE region='".$region."' AND summoner_id='".$summoner_id."'") or die($db->error);
									}
									else
									{
										$db->query("UPDATE inv_users_rankedstats SET data='".json_encode($seasondata_array)."' WHERE region='".$region."' AND summoner_id='".$summoner_id."'") or die($db->error);
									}
								}
							}
							else
							{
								if($db->query('SELECT id FROM inv_users_rankedstats WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'" LIMIT 1')->num_rows == 0)
								{
									$db->query("INSERT INTO inv_users_rankedstats (data,region,summoner_id) VALUES ('NOT_SET','".$region."',".$summoner_id.")") or die($db->error);
								}
								else
								{	
									$db->query("INSERT INTO inv_users_rankedstats (data,region,summoner_id) VALUES ('NOT_SET','".$region."',".$summoner_id.")") or die($db->error);
								}
							}
						}
					}
				}
				if($task == 4)
				{
					$actual_progress = $actual_progress + 20;
					$response = array(  'message' => 'Actualizando historial de partidas...', 'progress' => $actual_progress);
						
						$match_history_normals_db = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.3/game/by-summoner/'.$summoner_id.'/recent?api_key='.LOL_API_KEY);
						if(!empty($match_history_normals_db['games'])) //User has to played some games
						{
							foreach($match_history_normals_db['games'] as $gameid => $game_normal_data)
							{
								if($db->query('SELECT id FROM lol_matches WHERE match_id="'.$game_normal_data['gameId'].'" AND region="'.$region.'" LIMIT 1')->num_rows == 0) //Just add game
								{
									if($game_normal_data['stats']['win'] == 'true')
									{
										$game_winner_team_id = $game_normal_data['teamId'];
									}
									else
									{
										if($game_normal_data['teamId'] == 100)
										{
											$game_winner_team_id = 200;
										}
										else
										{
											$game_winner_team_id = 100;
										}
									}
									
									if($game_normal_data['invalid'] == 'true')
									{
										$loss_prevented = 'true';
									}
									else
									{
										$loss_prevented = 'false';
									}
									
									$game_data_array = array();
									$game_data_array['season'] = end($lol_seasons);
									$game_data_array['players'] = array();
									$game_data_array['players'][$summoner_id] = array();
									$game_data_array['players'][$summoner_id]['team'] = $game_normal_data['teamId'];
									$game_data_array['players'][$summoner_id]['spell_1'] = $game_normal_data['spell1'];
									$game_data_array['players'][$summoner_id]['spell_2'] = $game_normal_data['spell2'];
									$game_data_array['players'][$summoner_id]['champ_id'] = $game_normal_data['championId'];
									$game_data_array['players'][$summoner_id]['stats'] = array();
									if($game_normal_data['stats']['win'] == 'true')
									{
										$game_data_array['players'][$summoner_id]['stats']['winner'] = 'true';
									}
									else
									{
										$game_data_array['players'][$summoner_id]['stats']['winner'] = 'false';
									}
									$game_data_array['players'][$summoner_id]['stats']['total_gold'] = (int) @$game_normal_data['stats']['goldEarned'];
									$game_data_array['players'][$summoner_id]['stats']['champ_level'] = $game_normal_data['stats']['level'];
									$game_data_array['players'][$summoner_id]['stats']['kills'] = (int) @$game_normal_data['stats']['championsKilled'];
									$game_data_array['players'][$summoner_id]['stats']['deaths'] = (int) @$game_normal_data['stats']['numDeaths'];
									$game_data_array['players'][$summoner_id]['stats']['assists'] = (int) @$game_normal_data['stats']['assists'];
									$game_data_array['players'][$summoner_id]['stats']['minions'] = (int)@($game_normal_data['stats']['minionsKilled']+$game_normal_data['stats']['neutralMinionsKilled']);
									$game_data_array['players'][$summoner_id]['stats']['items'] = array();
									$game_data_array['players'][$summoner_id]['stats']['items'][0] = (int) @$game_normal_data['stats']['item0'];
									$game_data_array['players'][$summoner_id]['stats']['items'][1] = (int) @$game_normal_data['stats']['item1'];
									$game_data_array['players'][$summoner_id]['stats']['items'][2] = (int) @$game_normal_data['stats']['item2'];
									$game_data_array['players'][$summoner_id]['stats']['items'][3] = (int) @$game_normal_data['stats']['item3'];
									$game_data_array['players'][$summoner_id]['stats']['items'][4] = (int) @$game_normal_data['stats']['item4'];
									$game_data_array['players'][$summoner_id]['stats']['items'][5] = (int) @$game_normal_data['stats']['item5'];
									$game_data_array['players'][$summoner_id]['stats']['items'][6] = (int) @$game_normal_data['stats']['item6'];
									$db->query('INSERT INTO lol_matches (region,match_id,map_id,loss_prevented,winner_team_id,qeue,ips_earned,timestamp_start,timestamp_end,summoner_ids,data) VALUES ("'.$region.'","'.$game_normal_data['gameId'].'","'.$game_normal_data['mapId'].'","'.$loss_prevented.'",'.$game_winner_team_id.',"'.$game_normal_data['subType'].'","{\"'.$summoner_id.'\":'.$game_normal_data['ipEarned'].'}","'.datetounix($game_normal_data['createDate']).'","'.(datetounix($game_normal_data['createDate'])+$game_normal_data['stats']['timePlayed']).'",'.$summoner_id.',"'.addslashes(json_encode($game_data_array)).'")') or die($db->error);
								}
								elseif($db->query('SELECT id FROM lol_matches WHERE match_id='.$game_normal_data['gameId'].' AND summoner_ids LIKE "%'.$summoner_id.'%"')->num_rows == 0) //Game exists, okay. Add new fellow players if they doesn't exists and it's stats
								{
									if($game_normal_data['stats']['win'] == 'true')
									{
										$game_winner_team_id = $game_normal_data['teamId'];
									}
									else
									{
										if($game_normal_data['teamId'] == 100)
										{
											$game_winner_team_id = 200;
										}
										else
										{
											$game_winner_team_id = 100;
										}
									}
									
									if($game_normal_data['invalid'] == 'true')
									{
										$loss_prevented = 'true';
									}
									else
									{
										$loss_prevented = 'false';
									}
									
									$game_data_array = stdtoarray(json_decode($db->query('SELECT data FROM lol_matches WHERE match_id="'.$game_normal_data['gameId'].'" AND region="'.$region.'"')->fetch_row()[0]));
									$game_data_array['players'][$summoner_id] = array();
									$game_data_array['players'][$summoner_id]['team'] = $game_normal_data['teamId'];
									$game_data_array['players'][$summoner_id]['spell_1'] = $game_normal_data['spell1'];
									$game_data_array['players'][$summoner_id]['spell_2'] = $game_normal_data['spell2'];
									$game_data_array['players'][$summoner_id]['champ_id'] = $game_normal_data['championId'];
									$game_data_array['players'][$summoner_id]['stats'] = array();
									if($game_normal_data['stats']['win'] == 'true')
									{
										$game_data_array['players'][$summoner_id]['stats']['winner'] = 'true';
									}
									else
									{
										$game_data_array['players'][$summoner_id]['stats']['winner'] = 'false';
									}
									$game_data_array['players'][$summoner_id]['stats']['total_gold'] = (int) @$game_normal_data['stats']['goldEarned'];
									$game_data_array['players'][$summoner_id]['stats']['champ_level'] = $game_normal_data['stats']['level'];
									$game_data_array['players'][$summoner_id]['stats']['kills'] = (int) @$game_normal_data['stats']['championsKilled'];
									$game_data_array['players'][$summoner_id]['stats']['deaths'] = (int) @$game_normal_data['stats']['numDeaths'];
									$game_data_array['players'][$summoner_id]['stats']['assists'] = (int) @$game_normal_data['stats']['assists'];
									$game_data_array['players'][$summoner_id]['stats']['minions'] = (int)@($game_normal_data['stats']['minionsKilled']+$game_normal_data['stats']['neutralMinionsKilled']);
									$game_data_array['players'][$summoner_id]['stats']['items'] = array();
									$game_data_array['players'][$summoner_id]['stats']['items'][0] = (int) @$game_normal_data['stats']['item0'];
									$game_data_array['players'][$summoner_id]['stats']['items'][1] = (int) @$game_normal_data['stats']['item1'];
									$game_data_array['players'][$summoner_id]['stats']['items'][2] = (int) @$game_normal_data['stats']['item2'];
									$game_data_array['players'][$summoner_id]['stats']['items'][3] = (int) @$game_normal_data['stats']['item3'];
									$game_data_array['players'][$summoner_id]['stats']['items'][4] = (int) @$game_normal_data['stats']['item4'];
									$game_data_array['players'][$summoner_id]['stats']['items'][5] = (int) @$game_normal_data['stats']['item5'];
									$game_data_array['players'][$summoner_id]['stats']['items'][6] = (int) @$game_normal_data['stats']['item6'];
									
									$actual_match_ips = stdtoarray(json_decode($db->query('SELECT ips_earned FROM lol_matches WHERE match_id="'.$game_normal_data['gameId'].'" AND region="'.$region.'"')->fetch_row()[0]));
									if(array_key_exists($summoner_id,$actual_match_ips) == FALSE)
									{
										$actual_match_ips[$summoner_id] = $game_normal_data['ipEarned'];
									}
									$actual_match_summoners = $db->query('SELECT summoner_ids FROM lol_matches WHERE match_id="'.$game_normal_data['gameId'].'" AND region="'.$region.'"')->fetch_row()[0];
									$actual_match_summoners_array = explode(';',$actual_match_summoners);
									if(!in_array($summoner_id, $actual_match_summoners_array)) {
										$actual_match_summoners = $actual_match_summoners.';'.$summoner_id;
									}
									
									
									$db->query('UPDATE lol_matches SET ips_earned="'.addslashes(json_encode($actual_match_ips)).'",summoner_ids="'.$actual_match_summoners.'",data="'.addslashes(json_encode($game_data_array)).'" WHERE match_id='.$game_normal_data['gameId']) or die($db->error);
								}
							}
						}
				}
				if($task == 5)
				{
					$actual_progress = $actual_progress + 10;
					$response = array(  'message' => 'Actualizando puntuaciones y maestría...', 'progress' => $actual_progress);
					$champ_pool = stdtoarray(json_decode($db->query('SELECT data FROM inv_users_rankedstats WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'"')->fetch_row()[0]))[end($lol_seasons)]['ranked_champions']['champions'];
					if(is_array($champ_pool)) //If is ranked as smt or has ranked data atleast
					{
						reset($champ_pool);
					
						$user_champ_skill_pool = array();
						while (list($key, $val) = each($champ_pool)) {
							$champskill_id = $key;
							if($val['deaths'] == 0)
							{
								$deathsfix = 1;
							}
							else
							{
								$deathsfix = $val['deaths'];
							}
							$champskill_val = number_format($db->query('SELECT mmr FROM inv_users WHERE summoner_id='.$summoner_id.' AND region="'.$region.'"')->fetch_row()[0]+(($val['wins']+$val['losses'])+$val['wins']-$val['losses'])+(($val['kills']+$val['assists'])/$deathsfix),0,'','');
							(int) $user_champ_skill_pool[$champskill_id]['skill'] = (int) $champskill_val;
							(int) $user_champ_skill_pool[$champskill_id]['champ_id'] = (int) $champskill_id;
							(int) $user_champ_skill_pool[$champskill_id]['kda'] = number_format((($val['kills']+$val['assists'])/$deathsfix),1);
							(int) $user_champ_skill_pool[$champskill_id]['matches'] = (int) ($val['wins']+$val['losses']);
							(int) $user_champ_skill_pool[$champskill_id]['winrate'] = (int) number_format((100/($val['wins']+$val['losses']))*$val['wins']);
						}
						rsort($user_champ_skill_pool);
						if($db->query('SELECT id FROM inv_users_champskill WHERE summoner_id='.$summoner_id.' AND region="'.$region.'"')->num_rows == 0)
						{
							$db->query('INSERT INTO inv_users_champskill (data,summoner_id,region) VALUES ("'.addslashes(json_encode($user_champ_skill_pool)).'",'.$summoner_id.',"'.$region.'")') or die($db->error);
						}
						else
						{
							$db->query('UPDATE inv_users_champskill SET data="'.json_encode($user_champ_skill_pool).'" WHERE summoner_id='.$summoner_id.' AND region="'.$region.'",)');
						}
					}
					/* Champion mastery */
					$champ_mastery = readjson('https://'.$region.'.api.pvp.net/championmastery/location/'.$lol_servers[$region].'/player/'.$summoner_id.'/champions?api_key='.LOL_API_KEY);
					$champ_mastery_db = array();
					$champ_mastery_db['total_score'] = 0;
					$champ_mastery_db['total_levels'] = 0;
					if(is_array($champ_mastery))
					{
						foreach($champ_mastery as $champ_id => $champ_data)
						{
							$champ_mastery_db['champs'][$champ_id] = array();
							$champ_mastery_db['champs'][$champ_id]['champ_id'] = $champ_data['championId'];
							$champ_mastery_db['champs'][$champ_id]['mastery_level'] = $champ_data['championLevel'];
							$champ_mastery_db['champs'][$champ_id]['mastery_points'] = $champ_data['championPoints'];
							$champ_mastery_db['champs'][$champ_id]['last_time_played'] = datetounix($champ_data['lastPlayTime']);
							$champ_mastery_db['champs'][$champ_id]['mastery_points_plus_lastlevel'] = $champ_data['championPointsSinceLastLevel'];
							$champ_mastery_db['champs'][$champ_id]['mastery_points_need_nextlevel'] = $champ_data['championPointsUntilNextLevel'];
							$champ_mastery_db['total_score'] = ($champ_mastery_db['total_score']+$champ_data['championPoints']);
							$champ_mastery_db['total_levels'] = ($champ_mastery_db['total_levels']+$champ_data['championLevel']);
						}
						if($db->query('SELECT id FROM inv_users_champmastery WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->num_rows > 0)
						{
							$db->query('UPDATE inv_users_champmastery SET data="'.addslashes(json_encode($champ_mastery_db)).'" WHERE region="'.$region.'" AND summoner_id='.$summoner_id);
						}
						else
						{
							$db->query('INSERT INTO inv_users_champmastery (data,region,summoner_id) VALUES ("'.addslashes(json_encode($champ_mastery_db)).'","'.$region.'",'.$summoner_id.')');
						}
					}
					if(!empty($champ_mastery_db['champs'][0]))
					{
						$main_champ = champidtokeyname($champ_mastery_db['champs'][0]['champ_id']);
					}
					else
					{
						$main_champ = 'INV_NO_MAIN';
					}
				}
				if($task == 6)
				{
					$actual_progress = $actual_progress + 10;
					$response = array(  'message' => 'Actualizando campeón principal...', 'progress' => $actual_progress);
					if(!empty($main_champ))
					{
						$db->query('UPDATE inv_users SET main_champ="'.$main_champ.'" WHERE region="'.$region.'" AND summoner_id="'.$summoner_id.'"') or die($db->error);
					}
					else
					{
						$db->query('UPDATE inv_users SET main_champ="INV_NO_MAIN" WHERE region="'.$region.'" AND summoner_id="'.$summoner_id.'"') or die($db->error);
					}
				}
				if($task == 7)
				{
					$actual_progress = $actual_progress + 10;
					$response = array(  'message' => 'Actualizando runas y maestrías...', 'progress' => $actual_progress);
					if($db->query('SELECT runesansmast_last_update FROM inv_users WHERE summoner_id="'.$summoner_id.'" AND region="'.$region.'"')->fetch_row()[0] + config('profile_runesandmastupdate_interval') < time())
					{
						$players = $summoner_id;
						$players_runesmast_query = $db->query('SELECT summoner_id FROM inv_users WHERE runesansmast_last_update  < '.(time() - config('profile_runesandmastupdate_interval')).' AND region="'.$region.'" AND summoner_id!='.$summoner_id.' ORDER BY runesansmast_last_update ASC LIMIT 39');
						while($row = $players_runesmast_query->fetch_row())
						{
							$players .= ','.$row[0];
						}
						$runes_data = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.4/summoner/'.$players.'/runes?api_key='.LOL_API_KEY);
						$masteries_data = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.4/summoner/'.$players.'/runes?api_key='.LOL_API_KEY);
						$players_array = explode(',',$players);
						$this_updating_runeset = 0;
						while($this_updating_runeset < count($players_array))
						{
							$this_summoner_id = $players_array[$this_updating_runeset];
							$db->query('UPDATE inv_users SET runesansmast_last_update="'.time().'", runes="'.addslashes(json_encode($runes_data[$this_summoner_id]['pages'])).'", masteries="'.addslashes(json_encode($masteries_data[$this_summoner_id]['pages'])).'" WHERE summoner_id="'.$this_summoner_id.'" AND region="'.$region.'"') or die($db->error);

							$this_updating_runeset++;
						}
					}
				}
				if($task == 8)
				{
					$actual_progress = $actual_progress + 10;
					$response = array(  'message' => 'Dando los últimos retoques...', 'progress' => $actual_progress);
					$db->query('UPDATE inv_users SET onlol_last_update="'.time().'" WHERE summoner_id='.$summoner_id);
				}
				if($task == 9)
				{
					$actual_progress = $actual_progress + 10;
					$response = array(  'message' => '¡Finalizado correctamente!', 'progress' => 100);
					sleep(1);
				}
			}
			echo json_encode($response);
		}
	}
	else
	{
		$timetoreload_s = ($db->query('SELECT onlol_last_update FROM inv_users WHERE name="'.$summoner_name.'" AND region="'.$region.'"')->fetch_row()[0]+config('profilereload_interval'))-time();
		if($timetoreload_s/60 > 1)
		{
			if(round($timetoreload_s/60) > 1)
			{
			$timetoreload = round($timetoreload_s/60) .' Minutos';
			}
			else
			{
				$timetoreload = round($timetoreload_s/60) .' Minuto';
			}
		}
		else
		{
			$timetoreload = $timetoreload_s.' Segundos';
		}
		if($timetoreload_s/3600 > 1)
		{
			if(round($timetoreload_s/3600) > 1)
			{
				$timetoreload .= round($timetoreload_s/3600) .' Horas';
			}
			else
			{
				$timetoreload .= round($timetoreload_s/3600) .' Hora';
			}
		}
		if($timetoreload < 0)
		{
			$timetoreload = 0;
		}
		$response = array(  'message' => 'ERROR_RELOAD_NOTYET', 'progress' => 0);
		echo json_encode($response);
	}
}
else
{
	die('This ain\'t no runaterra.');
}