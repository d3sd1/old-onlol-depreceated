<?php
if(!empty($_SERVER['SERVER_NAME']))
{
	$baseurl = str_replace('www.', null,$_SERVER['SERVER_NAME']);
}
else
{
	$baseurl = 'onlol.net';
}
define('URL','http://www.'.$baseurl);
define('BASEURL', $baseurl);
define('LOL_API_KEY', '1375edea-27ad-4f0a-80b0-e38402eaa69e');
define('ROOTPATH', substr(__DIR__, 0, -5));
class onlol{
	public static function mmr($inv_id, $server, $lp, $league, $division)
	{
		global $db;
		$server_valid = parseserver($server);
		switch($league)
		{
			case 'C':
			$division_data = 'CHALLENGER'.parsedisivion($division);
			break;
			case 'M':
			$division_data = 'MASTER'.parsedisivion($division);
			break;
			case 'D':
			$division_data = 'DIAMOND'.parsedisivion($division);
			break;
			case 'P':
			$division_data = 'PLATINUM'.parsedisivion($division);
			break;
			case 'G':
			$division_data = 'GOLD'.parsedisivion($division);
			break;
			case 'S':
			$division_data = 'SILVER'.parsedisivion($division);
			break;
			case 'B':
			$division_data = 'BRONZE'.parsedisivion($division);
			break;
		}
		$elo = divisionbasemmr($division_data);
		$finalmmr = round($elo+($lp*70)/100);
		$db->query('UPDATE inv_users SET mmr_last_update="'.time().'", mmr="'.$finalmmr.'" WHERE summoner_id="'.$inv_id.'" AND region="'.$server.'"');
		return $finalmmr;
	}
	
	public static function updatesummoner_leagues($summonerid,$summoner,$region,$level,$icon,$lolrevision)
	{
		global $db;
		if((int) $level == 30)
		{
			if($db->query('SELECT league_last_update FROM inv_users WHERE summoner_id="'.$summonerid.'" AND region="'.$region.'"')->fetch_row()[0] + config('profile_leagueupdate_interval') < time())
			{
				/* Unranked summoner info */
				$db->query('UPDATE inv_users SET ranked_league="U",ranked_division="1",ranked_division_name="UNRANKED",ranked_lp="0",ranked_wins="0",ranked_losses="0",ranked_streak="false",ranked_veteran="false",ranked_newbbie="false",ranked_inactive="false" WHERE summoner_id="'.$summonerid.'" LIMIT 1') or die($db->error);
			
				/* Leagues */
				/* Optimizer: Add some more players to the query for save some querys on api */
				$players = $summonerid;
				$players_query = $db->query('SELECT summoner_id FROM inv_users WHERE league_last_update  < '.(time() - config('profile_leagueupdate_interval')).' AND region="'.$region.'" AND ranked_league!="U" AND summoner_id!='.$summonerid.' ORDER BY league_last_update ASC LIMIT 9');
				while($row = $players_query->fetch_row())
				{
					$players .= ','.$row[0];
				}
				
				$teams = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v2.5/league/by-summoner/'.$players.'/entry?api_key='.LOL_API_KEY);
				$summonerteams_ids = explode(',',$players);
				$this_summonerteam_updating = 0;
				while($this_summonerteam_updating < count($summonerteams_ids) && is_array($teams))
				{
					if(array_key_exists($summonerteams_ids[$this_summonerteam_updating],$teams))
					{
						$totalteams = count($teams[$summonerteams_ids[$this_summonerteam_updating]]);
						$db->query('UPDATE inv_users SET league_last_update='.time().' WHERE summoner_id='.$summonerteams_ids[$this_summonerteam_updating].' AND region="'.$region.'"');
						$actualteam = 0;
						while($actualteam < $totalteams)
						{
							$entry = array_search($summoner, array_column($teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['entries'], 'playerOrTeamName'));	
							$qeue = $teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['queue'];
							$streakbase = $teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['entries'][$entry]['isHotStreak'];
							$veteranbase = $teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['entries'][$entry]['isVeteran'];
							$newbiebase = $teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['entries'][$entry]['isFreshBlood'];
							$inactivebase = $teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['entries'][$entry]['isInactive'];
							$tier = strtoupper(substr($teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['tier'], 0,1));
							$division = parsedisivion($teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['entries'][$entry]['division']);
							$lp = $teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['entries'][$entry]['leaguePoints'];
							$name = $teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['name']; /* Of actual team */
							$wins = $teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['entries'][$entry]['wins'];
							$losses = $teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['entries'][$entry]['losses'];
							/* Fix array data*/
							$streak = array();
							$veteran = array();
							$newbie = array();
							$inactive = array();
							if($qeue == 'RANKED_TEAM_5x5' OR $qeue == 'RANKED_TEAM_3x3')
							{
								$team_name = $teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['entries']['0']['playerOrTeamName'];
								$team_id = $teams[$summonerteams_ids[$this_summonerteam_updating]][$actualteam]['entries']['0']['playerOrTeamId'];
							}
									
							/* Ranked soloQ data */
							if($qeue == 'RANKED_SOLO_5x5')
							{
								if($streak == null) {$streak['soloq'] = 'false';} else {$streak['soloq'] = $streakbase;}
								if($veteran == null) {$veteran['soloq'] = 'false';} else {$veteran['soloq'] = $veteranbase;}
								if($newbie == null) {$newbbie['soloq'] = 'false';} else {$newbbie['soloq'] = $newbiebase;}
								if($inactive == null) {$inactive['soloq'] = 'false';} else {$inactive['soloq'] = $inactivebase;}
										
								$db->query('UPDATE inv_users SET ranked_league="'.$tier.'",ranked_division="'.$division.'",ranked_division_name="'.$name.'",ranked_lp="'.$lp.'",ranked_wins="'.$wins.'",ranked_losses="'.$losses.'",ranked_streak="'.$streak['soloq'].'",ranked_veteran="'.$veteran['soloq'].'",ranked_newbbie="'.$newbbie['soloq'].'",ranked_inactive="'.$inactive['soloq'].'" WHERE summoner_id="'.$summonerteams_ids[$this_summonerteam_updating].'" AND region="'.$region.'" LIMIT 1') or die($db->error);
							}	
							/* Ranked 5x5 data */
							if($qeue == 'RANKED_TEAM_5x5')
							{
								if($streak == null) {$streak['5x5'] = 'false';} else {$streak['5x5'] = $streakbase;}
								if($veteran == null) {$veteran['5x5'] = 'false';} else {$veteran['5x5'] = $veteranbase;}
								if($newbie == null) {$newbbie['5x5'] = 'false';} else {$newbbie['5x5'] = $newbiebase;}
								if($inactive == null) {$inactive['5x5'] = 'false';} else {$inactive['5x5'] = $inactivebase;}
										
								if($db->query('SELECT id FROM inv_users_teams WHERE team_id="'.$team_id.'" AND qeue="5x5"')->num_rows > 0)
								{
									if($db->query('SELECT id FROM inv_users_teams WHERE team_id="'.$team_id.'" AND qeue="5x5" AND summoner_ids NOT LIKE '.$summonerteams_ids[$this_summonerteam_updating].' LIMIT 1')->num_rows > 0)
									{
										$db->query('UPDATE inv_users_teams SET summoner_ids=(CONCAT(summoner_ids, ";'.$summonerteams_ids[$this_summonerteam_updating].'")) WHERE team_id="'.$team_id.'" AND qeue="5x5" LIMIT 1') or die($db->error);
									}
								}
								else
								{
									$db->query('INSERT INTO inv_users_teams (summoner_ids,team_name,league,division,lp,wins,losses,streak,veteran,newbbie,inactive,team_id,qeue) VALUES ('.$summonerteams_ids[$this_summonerteam_updating].',"'.$team_name.'","'.$tier.'","'.$division.'","'.$lp.'","'.$wins.'","'.$losses.'","'.$streak['5x5'].'","'.$veteran['5x5'].'","'.$newbbie['5x5'].'","'.$inactive['5x5'].'","'.$team_id.'","5x5")') or die($db->error);
								}
							}
							
							/* Ranked 3x3 data */
							if($qeue == 'RANKED_TEAM_3x3')
							{
								if($streak == null) {$streak['3x3'] = 'false';} else {$streak['3x3'] = $streakbase;}
								if($veteran == null) {$veteran['3x3'] = 'false';} else {$veteran['3x3'] = $veteranbase;}
								if($newbie == null) {$newbbie['3x3'] = 'false';} else {$newbbie['3x3'] = $newbiebase;}
								if($inactive == null) {$inactive['3x3'] = 'false';} else {$inactive['3x3'] = $inactivebase;}
								if($db->query('SELECT id FROM inv_users_teams WHERE team_id="'.$team_id.'" AND qeue="3x3"')->num_rows > 0)
								{
									if($db->query('SELECT id FROM inv_users_teams WHERE team_id="'.$team_id.'" AND qeue="3x3" AND summoner_ids NOT LIKE '.$summonerteams_ids[$this_summonerteam_updating].' LIMIT 1')->num_rows > 0)
									{
										$db->query('UPDATE inv_users_teams SET summoner_ids=(CONCAT(summoner_ids, ";'.$summonerteams_ids[$this_summonerteam_updating].'")) WHERE team_id="'.$team_id.'" AND qeue="3x3" LIMIT 1') or die($db->error);
									}
								}
								else
								{
									$db->query('INSERT INTO inv_users_teams (summoner_ids,team_name,league,division,lp,wins,losses,streak,veteran,newbbie,inactive,team_id,qeue) VALUES ('.$summonerteams_ids[$this_summonerteam_updating].',"'.$team_name.'","'.$tier.'","'.$division.'","'.$lp.'","'.$wins.'","'.$losses.'","'.$streak['3x3'].'","'.$veteran['3x3'].'","'.$newbbie['3x3'].'","'.$inactive['3x3'].'","'.$team_id.'","3x3")') or die($db->error);
								}
							}
							$actualteam++;
						}
						/* MMR */
						onlol::mmr($summonerteams_ids[$this_summonerteam_updating], $region, $db->query('SELECT ranked_lp FROM inv_users WHERE summoner_id="'.$summonerteams_ids[$this_summonerteam_updating].'" AND region="'.$region.'" LIMIT 1')->fetch_row()[0], $db->query('SELECT ranked_league FROM inv_users WHERE summoner_id="'.$summonerteams_ids[$this_summonerteam_updating].'" AND region="'.$region.'" LIMIT 1')->fetch_row()[0], $db->query('SELECT ranked_division FROM inv_users WHERE summoner_id="'.$summonerteams_ids[$this_summonerteam_updating].'" AND region="'.$region.'" LIMIT 1')->fetch_row()[0]);
					}
					$this_summonerteam_updating++;
				}
			}
		}
		else
		{
			/* Information for summoners which level is minor than 30 */
				$db->query('UPDATE inv_users SET ranked_league="U",ranked_division="1",ranked_division_name="UNRANKED",ranked_lp="0",ranked_wins="0",ranked_losses="0",ranked_streak="false",ranked_veteran="false",ranked_newbbie="false",ranked_inactive="false" WHERE summoner_id="'.$summonerid.'" LIMIT 1') or die($db->error);
		}
	}
	
	public static function updatesummoner_rankedgames($summonerid,$summoner,$region,$level,$league,$season = 'ACTUAL')
	{
		global $db;
		if($db->query('SELECT rkdprof_last_update FROM inv_users WHERE summoner_id="'.$summonerid.'" AND region="'.$region.'"')->fetch_row()[0] + config('profile_rkdprofupdate_interval') < time())
		{
			$db->query('UPDATE inv_users SET rkdprof_last_update='.time().' WHERE summoner_id='.$summonerid.' AND region="'.$region.'"');
			if($level == '30' && $league != 'U')
			{
				if($season == 'ACTUAL')
				{
					if(date('m') == 01 or date('m') == 02)
					{
						$loadseason = 'SEASON'.(date('Y')-1);
					}
					else
					{
						$loadseason = 'SEASON'.date('Y');
					}
				}
				else
				{
					$loadseason = $season;
				}
				$seasons = explode(';', $loadseason);
				$this_season_match = 0;
				$seasondata_array = array();
				
				while($this_season_match < count($seasons))
				{
					if(empty(stdtoarray(json_decode($db->query('SELECT data FROM inv_users_rankedstats WHERE summoner_id="'.$summonerid.'" AND region="'.$region.'"')->fetch_row()[0]))[$seasons[$this_season_match]]))
					{
						$seasondata_array[$seasons[$this_season_match]] = array();
						
						$seasondata = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.3/stats/by-summoner/'.$summonerid.'/summary?season='.$seasons[$this_season_match].'&api_key='.LOL_API_KEY);
						$championdata = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.3/stats/by-summoner/'.$summonerid.'/ranked?season='.$seasons[$this_season_match].'&api_key='.LOL_API_KEY);
						$champions = null;
						
						if(!empty($seasondata['playerStatSummaries']))
						{
							$this_readingdata = 0;
							$seasondata_array[$seasons[$this_season_match]] = array();
							while($this_readingdata < count($seasondata['playerStatSummaries']))
							{
								/* Season data */
								if($seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedPremade3x3' or $seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedTeam3x3')
								{
									$seasondata_array[$seasons[$this_season_match]]['ranked_team_3x3']['wins'] = $seasondata['playerStatSummaries'][$this_readingdata]['wins'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_team_3x3']['losses'] = $seasondata['playerStatSummaries'][$this_readingdata]['losses'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_team_3x3']['kills'] = $seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalChampionKills'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_team_3x3']['turrets'] = $seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalTurretsKilled'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_team_3x3']['minions'] = $seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalMinionKills'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_team_3x3']['assists'] = $seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalAssists'];
								}
								if($seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedPremade5x5' or $seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedTeam5x5')
								{
									$seasondata_array[$seasons[$this_season_match]]['ranked_team_5x5']['wins'] = $seasondata['playerStatSummaries'][$this_readingdata]['wins'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_team_5x5']['losses'] = $seasondata['playerStatSummaries'][$this_readingdata]['losses'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_team_5x5']['kills'] = $seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalChampionKills'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_team_5x5']['turrets'] = $seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalTurretsKilled'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_team_5x5']['minions'] = $seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalMinionKills'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_team_5x5']['assists'] = $seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalAssists'];
								}
								if($seasondata['playerStatSummaries'][$this_readingdata]['playerStatSummaryType'] == 'RankedSolo5x5')
								{
									$seasondata_array[$seasons[$this_season_match]]['ranked_solo_5x5']['wins'] = $seasondata['playerStatSummaries'][$this_readingdata]['wins'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_solo_5x5']['losses'] = $seasondata['playerStatSummaries'][$this_readingdata]['losses'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_solo_5x5']['kills'] = $seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalChampionKills'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_solo_5x5']['turrets'] = $seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalTurretsKilled'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_solo_5x5']['minions'] = $seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalMinionKills'];
									$seasondata_array[$seasons[$this_season_match]]['ranked_solo_5x5']['assists'] = $seasondata['playerStatSummaries'][$this_readingdata]['aggregatedStats']['totalAssists'];
								}
								
								/* Champions data */
								$keep_alive_champions = true;
								$this_champ_info = 0;
								if($championdata != 'NOT_FOUND')
								{
									$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'] = array();
									while($keep_alive_champions == true)
									{
										if(!empty($championdata['champions'][$this_champ_info]))
										{
											$champ_id = $championdata['champions'][$this_champ_info]['id'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id] = $champ_id;
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id] = array();
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['wins'] = $championdata['champions'][$this_champ_info]['stats']['totalSessionsWon'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['losses'] = $championdata['champions'][$this_champ_info]['stats']['totalSessionsLost'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['kills'] = $championdata['champions'][$this_champ_info]['stats']['totalChampionKills'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['deaths'] = $championdata['champions'][$this_champ_info]['stats']['totalDeathsPerSession'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['assists'] = $championdata['champions'][$this_champ_info]['stats']['totalAssists'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['dmg_dealt'] = $championdata['champions'][$this_champ_info]['stats']['totalDamageDealt'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['dmg_dealt_ad'] = $championdata['champions'][$this_champ_info]['stats']['totalPhysicalDamageDealt'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['dmg_dealt_ap'] = $championdata['champions'][$this_champ_info]['stats']['totalMagicDamageDealt'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['dmg_taken'] = $championdata['champions'][$this_champ_info]['stats']['totalDamageTaken'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['maxkillsonsinglegame'] = $championdata['champions'][$this_champ_info]['stats']['mostChampionKillsPerSession'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['maxdeathsonsinglegame'] = $championdata['champions'][$this_champ_info]['stats']['maxNumDeaths'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['minions'] = $championdata['champions'][$this_champ_info]['stats']['totalMinionKills'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['kills_double'] = $championdata['champions'][$this_champ_info]['stats']['totalDoubleKills'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['kills_triple'] = $championdata['champions'][$this_champ_info]['stats']['totalTripleKills'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['kills_quadra'] = $championdata['champions'][$this_champ_info]['stats']['totalQuadraKills'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['kills_penta'] = $championdata['champions'][$this_champ_info]['stats']['totalPentaKills'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['gold'] = $championdata['champions'][$this_champ_info]['stats']['totalGoldEarned'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['turrets'] = $championdata['champions'][$this_champ_info]['stats']['totalTurretsKilled'];
											$seasondata_array[$seasons[$this_season_match]]['ranked_champions']['champions'][$champ_id]['firstblood'] = $championdata['champions'][$this_champ_info]['stats']['totalFirstBlood'];
											
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
						
						if(empty($seasondata_array[$seasons[$this_season_match]]))
						{
							unset($seasondata_array[$seasons[$this_season_match]]);
						}
					}
					$this_season_match++;
				}
				if(!empty($seasondata_array))
				{
					if($db->query('SELECT id FROM inv_users_rankedstats WHERE summoner_id="'.$summonerid.'" AND region="'.$region.'" LIMIT 1')->num_rows == 0)
					{
						$db->query("INSERT INTO inv_users_rankedstats (data,region,summoner_id) VALUES ('NOT_PLAYED','".$region."',".$summonerid.")") or die($db->error);
					}
					
					$db->query("UPDATE inv_users_rankedstats SET data='".json_encode($seasondata_array)."' WHERE region='".$region."' AND summoner_id='".$summonerid."'") or die($db->error);
				}
			}
		}
	}
	
	public static function updatesummoner_last10matches($summonerid,$region)
	{
		global $db;
		if($db->query('SELECT lastgames_last_update FROM inv_users WHERE summoner_id="'.$summonerid.'" AND region="'.$region.'"')->fetch_row()[0] + config('profile_last10matchesupdate_interval') < time())
		{
			$db->query('UPDATE inv_users SET lastgames_last_update='.time().' WHERE summoner_id='.$summonerid.' AND region="'.$region.'"');
			
			$match_history_normals_db = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.3/game/by-summoner/'.$summonerid.'/recent?api_key='.LOL_API_KEY);
			$actualnormalgame = 0;
			while($actualnormalgame < count($match_history_normals_db['games']))
			{
				if($db->query('SELECT id FROM lol_matches WHERE match_id="'.$match_history_normals_db['games'][$actualnormalgame]['gameId'].'" AND region="'.$region.'" LIMIT 1')->num_rows == 0) //Just add game
			{
					$specific_match_details = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v2.2/match/'.$match_history_normals_db['games'][$actualnormalgame]['gameId'].'?includeTimeline=true&api_key='.LOL_API_KEY);
					
					switch($match_history_normals_db['games'][$actualnormalgame]['subType'])
					{
						case 'NONE':
						$normalmatchinfo_qeue = 'NONE';
						break;
						case 'NORMAL':
						$normalmatchinfo_qeue = 'NORMAL';
						break;
						case 'BOT':
						$normalmatchinfo_qeue = 'BOT';
						break;
						case 'RANKED_SOLO_5x5':
						$normalmatchinfo_qeue = 'RANKED_SOLO_5x5';
						break;
						case 'RANKED_PREMADE_3x3': //DEPRECEATED
						$normalmatchinfo_qeue = 'RANKED_TEAM_3x3';
						break;
						case 'RANKED_PREMADE_5x5': //DEPRECEATED
						$normalmatchinfo_qeue = 'RANKED_TEAM_5x5';
						break;
						case 'ODIN_UNRANKED':
						$normalmatchinfo_qeue = 'ODIN_UNRANKED';
						break;
						case 'RANKED_TEAM_3x3':
						$normalmatchinfo_qeue = 'RANKED_TEAM_3x3';
						break;
						case 'RANKED_TEAM_5x5':
						$normalmatchinfo_qeue = 'RANKED_TEAM_5x5';
						break;
						case 'NORMAL_3x3':
						$normalmatchinfo_qeue = 'NORMAL_3x3';
						break;
						case 'BOT_3x3':
						$normalmatchinfo_qeue = 'BOT_3x3';
						break;
						case 'CAP_5x5':
						$normalmatchinfo_qeue = 'CAP_5x5';
						break;
						case 'ARAM_UNRANKED_5x5':
						$normalmatchinfo_qeue = 'ARAM_UNRANKED_5x5';
						break;
						case 'ONEFORALL_5x5':
						$normalmatchinfo_qeue = 'ONEFORALL_5x5';
						break;
						case 'FIRSTBLOOD_1x1':
						$normalmatchinfo_qeue = 'FIRSTBLOOD_1x1';
						break;
						case 'FIRSTBLOOD_2x2':
						$normalmatchinfo_qeue = 'FIRSTBLOOD_2x2';
						break;
						case 'SR_6x6':
						$normalmatchinfo_qeue = 'SR_6x6';
						break;
						case 'URF':
						$normalmatchinfo_qeue = 'URF';
						break;
						case 'URF_BOT':
						$normalmatchinfo_qeue = 'URF_BOT';
						break;
						case 'NIGHTMARE_BOT':
						$normalmatchinfo_qeue = 'NIGHTMARE_BOT';
						break;
						case 'ASCENSION':
						$normalmatchinfo_qeue = 'ASCENSION';
						break;
						case 'HEXAKILL':
						$normalmatchinfo_qeue = 'HEXAKILL';
						break;
						case 'KING_PORO':
						$normalmatchinfo_qeue = 'KING_PORO';
						break;
						case 'COUNTER_PICK':
						$normalmatchinfo_qeue = 'COUNTER_PICK';
						break;
						case 'BILGEWATER':
						$normalmatchinfo_qeue = 'BILGEWATER';
						break;
						default: $normalmatchinfo_qeue = 'NONE';
					}
								 
					if($match_history_normals_db['games'][$actualnormalgame]['gameType'] == 'CUSTOM_GAME')
					{
						$normalmatchinfo_qeue = 'CUSTOM_GAME';
					}
					if($match_history_normals_db['games'][$actualnormalgame]['gameType'] == 'TUTORIAL_GAME')
					{
						$normalmatchinfo_qeue = 'TUTORIAL_GAME';
					}
								
					if($match_history_normals_db['games'][$actualnormalgame]['stats']['win'] == 1)
					{
					$match_result = 'win';
					}
					else
					{
						$match_result = 'lost';
					}
						
					if(is_int($match_history_normals_db['games'][$actualnormalgame]['createDate']))
					{
						$datetounix = $match_history_normals_db['games'][$actualnormalgame]['createDate'];
						$lolrevision = datetounix($datetounix);
					}
					else
					{
						$lolrevision = time();
					}
						
					if(empty($lolrevision))
					{
						$lolrevision = time();
					}
						
					if($match_history_normals_db['games'][$actualnormalgame]['invalid'] == '')
					{
						$refused_status = 'false';
					}
					else
					{
						$refused_status = 'true';
					}
					$summoner_ids = $summonerid;
					$this_summoner_onmatch = 0;
					while($this_summoner_onmatch < count($match_history_normals_db['games'][$actualnormalgame]['fellowPlayers']))
					{
						$summoner_ids .= ',';
						$summoner_ids .= $match_history_normals_db['games'][$actualnormalgame]['fellowPlayers'][$this_summoner_onmatch]['summonerId'];
						$this_summoner_onmatch++;
					}
					
					/* Start to do match data */
					$data_decoded = array();
					$data_decoded['version'] = $specific_match_details['matchVersion'];
					$data_decoded['season'] = $specific_match_details['season'];
					$data_decoded['teams'] = array();
					$this_teamdata_count = 0;
					while($this_teamdata_count < count($specific_match_details['teams']))
					{
						$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']] = array();
						if($specific_match_details['teams'][$this_teamdata_count]['winner'] == '')
						{
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['winner'] = false;
						}
						else
						{
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['winner'] = true;
						}
						if($specific_match_details['teams'][$this_teamdata_count]['firstBlood'] == '')
						{
							$data_first_firstblood_teamid = 'NOT_SET';
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['first_blood'] = false;
						}
						else
						{
							$data_first_firstblood_teamid = $specific_match_details['teams'][$this_teamdata_count]['teamId'];
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['first_blood'] = true;
						}
						if($specific_match_details['teams'][$this_teamdata_count]['firstTower'] == '')
						{
							$data_first_tower_teamid = 'NOT_SET';
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['first_tower'] = false;
						}
						else
						{
							$data_first_tower_teamid = $specific_match_details['teams'][$this_teamdata_count]['teamId'];
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['first_tower'] = true;
						}
						if($specific_match_details['teams'][$this_teamdata_count]['firstInhibitor'] == '')
						{
							$data_first_inhib_teamid = 'NOT_SET';
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['first_inhib'] = false;
						}
						else
						{
							$data_first_inhib_teamid = $specific_match_details['teams'][$this_teamdata_count]['teamId'];
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['first_inhib'] = true;
						}
						if($specific_match_details['teams'][$this_teamdata_count]['firstBaron'] == '')
						{
							$data_first_baron_teamid = 'NOT_SET';
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['first_baron'] = false;
						}
						else
						{
							$data_first_baron_teamid = $specific_match_details['teams'][$this_teamdata_count]['teamId'];
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['first_baron'] = true;
						}
						if($specific_match_details['teams'][$this_teamdata_count]['firstDragon'] == '')
						{
							$data_first_dragon_teamid = 'NOT_SET';
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['first_dragon'] = false;
						}
						else
						{
							$data_first_dragon_teamid = $specific_match_details['teams'][$this_teamdata_count]['teamId'];
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['first_dragon'] = true;
						}
						if($specific_match_details['teams'][$this_teamdata_count]['firstRiftHerald'] == '')
						{
							$data_first_herald_teamid = 'NOT_SET';
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['first_herald'] = false;
						}
						else
						{
							$data_first_herald_teamid = $specific_match_details['teams'][$this_teamdata_count]['teamId'];
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['first_herald'] = true;
						}
						$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['tower_kills'] = $specific_match_details['teams'][$this_teamdata_count]['towerKills'];
						$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['inhib_kills'] = $specific_match_details['teams'][$this_teamdata_count]['inhibitorKills'];
						if($match_history_normals_db['games'][$actualnormalgame]['mapId'] == 11)
						{
						$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['baron_kills'] = $specific_match_details['teams'][$this_teamdata_count]['baronKills'];
						$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['dragon_kills'] = $specific_match_details['teams'][$this_teamdata_count]['dragonKills'];
						$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['herald_kills'] = $specific_match_details['teams'][$this_teamdata_count]['riftHeraldKills'];
						}
						if($match_history_normals_db['games'][$actualnormalgame]['mapId'] == 10)
						{
						$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['vilemaw_kills'] = $specific_match_details['teams'][$this_teamdata_count]['vilemawKills'];
						}
						if($match_history_normals_db['games'][$actualnormalgame]['mapId'] == 8)
						{
							$data_decoded['teams'][$specific_match_details['teams'][$this_teamdata_count]['teamId']]['dominion_score'] = $specific_match_details['teams'][$this_teamdata_count]['dominionVictoryScore'];
						}
						$this_teamdata_count++;
					}
					$data_decoded['players'] = array();
					$this_gameplayer_ret = 0;
					
					$player_id = explode(',',$summoner_ids);
					while($this_gameplayer_ret < count($specific_match_details['participants']))
					{
						/* Self player */
						if($match_history_normals_db['games'][$actualnormalgame]['teamId'] == $specific_match_details['participants'][$this_gameplayer_ret]['teamId'] AND $match_history_normals_db['games'][$actualnormalgame]['championId'] == $specific_match_details['participants'][$this_gameplayer_ret]['championId'])
						{
							$summonner_ongame_id = $match_history_normals_db['summonerId'];
						}
						foreach($match_history_normals_db['games'][$actualnormalgame]['fellowPlayers'] as $mathforid)
						{
							/* Other players */
							if($mathforid['teamId'] == $specific_match_details['participants'][$this_gameplayer_ret]['teamId'] AND $mathforid['championId'] == $specific_match_details['participants'][$this_gameplayer_ret]['championId'])
							{
								$summonner_ongame_id = $mathforid['summonerId'];
							}
						}
						
						if(empty($summonner_ongame_id))
						{
							if($specific_match_details['participantIdentities'][$specific_match_details['participants'][$this_gameplayer_ret]['participantId']] > 20)
							{
								$summonner_ongame_id = $specific_match_details['participantIdentities'][$specific_match_details['participants'][$this_gameplayer_ret]['participantId']];
							}
							else
							{
								$summonner_ongame_id = 'NOT_FOUND';
							}
							
						}
						$data_decoded['players'][$summonner_ongame_id] = array();
						$data_decoded['players'][$summonner_ongame_id]['team'] = $specific_match_details['participants'][$this_gameplayer_ret]['teamId'];
						$data_decoded['players'][$summonner_ongame_id]['spell_1'] = $specific_match_details['participants'][$this_gameplayer_ret]['spell1Id'];
						$data_decoded['players'][$summonner_ongame_id]['spell_2'] = $specific_match_details['participants'][$this_gameplayer_ret]['spell2Id'];
						$data_decoded['players'][$summonner_ongame_id]['champ_id'] = $specific_match_details['participants'][$this_gameplayer_ret]['championId'];
						if(array_key_exists('highestAchievedSeasonTier',$specific_match_details['participants'][$this_gameplayer_ret]))
						{
							$data_decoded['players'][$summonner_ongame_id]['league'] = $specific_match_details['participants'][$this_gameplayer_ret]['highestAchievedSeasonTier'];
						}
						else
						{
							$data_decoded['players'][$summonner_ongame_id]['league'] = 'UNRANKED';
						}
						$data_decoded['players'][$summonner_ongame_id]['player_id'] = $specific_match_details['participants'][$this_gameplayer_ret]['participantId'];
						if($specific_match_details['participants'][$this_gameplayer_ret]['stats']['firstBloodKill'] == '')
						{
							$data_decoded['players'][$summonner_ongame_id]['first_blood'] = 'false';
						}
						else
						{
							$data_decoded['players'][$summonner_ongame_id]['first_blood'] = 'true';
						}
						if($specific_match_details['participants'][$this_gameplayer_ret]['stats']['firstBloodAssist'] == '')
						{
							$data_decoded['players'][$summonner_ongame_id]['first_blood_assist'] = 'false';
						}
						else
						{
							$data_decoded['players'][$summonner_ongame_id]['first_blood_assist'] = 'true';
						}
						if($specific_match_details['participants'][$this_gameplayer_ret]['stats']['firstTowerKill'] == '')
						{
							$data_decoded['players'][$summonner_ongame_id]['first_tower'] = 'false';
						}
						else
						{
							$data_decoded['players'][$summonner_ongame_id]['first_tower'] = 'true';
						}
						if($specific_match_details['participants'][$this_gameplayer_ret]['stats']['firstTowerAssist'] == '')
						{
							$data_decoded['players'][$summonner_ongame_id]['first_tower_assist'] = 'false';
						}
						else
						{
							$data_decoded['players'][$summonner_ongame_id]['first_tower_assist'] = 'true';
						}
						if($specific_match_details['participants'][$this_gameplayer_ret]['stats']['firstInhibitorKill'] == '')
						{
							$data_decoded['players'][$summonner_ongame_id]['first_inhib'] = 'false';
						}
						else
						{
							$data_decoded['players'][$summonner_ongame_id]['first_inhib'] = 'true';
						}
						if($specific_match_details['participants'][$this_gameplayer_ret]['stats']['firstInhibitorAssist'] == '')
						{
							$data_decoded['players'][$summonner_ongame_id]['first_inhib_assist'] = 'false';
						}
						else
						{
							$data_decoded['players'][$summonner_ongame_id]['first_inhib_assist'] = 'true';
						}
						
						if($specific_match_details['participants'][$this_gameplayer_ret]['timeline']['lane'] == 'TOP')
						{
							$data_decoded['players'][$summonner_ongame_id]['position'] = 'TOP';
						}
						if($specific_match_details['participants'][$this_gameplayer_ret]['timeline']['lane'] == 'MID' or $specific_match_details['participants'][$this_gameplayer_ret]['timeline']['lane'] == 'MIDDLE')
						{
							$data_decoded['players'][$summonner_ongame_id]['position'] = 'MID';
						}
						if($specific_match_details['participants'][$this_gameplayer_ret]['timeline']['lane'] == 'JUNGLE')
						{
							$data_decoded['players'][$summonner_ongame_id]['position'] = 'JUNGLE';
						}
						
						if($specific_match_details['participants'][$this_gameplayer_ret]['timeline']['lane'] == 'BOTTOM' or $specific_match_details['participants'][$this_gameplayer_ret]['timeline']['lane'] == 'BOT')
						{
							if($specific_match_details['participants'][$this_gameplayer_ret]['timeline']['role'] == 'DUO_CARRY')
							{
								$data_decoded['players'][$summonner_ongame_id]['position'] = 'ADC';
							}
							if($specific_match_details['participants'][$this_gameplayer_ret]['timeline']['role'] == 'DUO_SUPPORT')
							{
								$data_decoded['players'][$summonner_ongame_id]['position'] = 'SUPPORT';
							}
						}
						if(empty($data_decoded['players'][$summonner_ongame_id]['position']))
						{
							$data_decoded['players'][$summonner_ongame_id]['position'] = 'NOT_FOUND';
						}
						
						if(array_key_exists('timeline',$specific_match_details['participants'][$this_gameplayer_ret]))
						{
							$data_decoded['players'][$summonner_ongame_id]['timeline'] = array();
							$data_decoded['players'][$summonner_ongame_id]['timeline']['minions_permin'] = array();
							$data_decoded['players'][$summonner_ongame_id]['timeline']['minions_permin']['0to10'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['creepsPerMinDeltas']['zeroToTen'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['minions_permin']['10to20'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['creepsPerMinDeltas']['tenToTwenty'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['exp_permin'] = array();
							$data_decoded['players'][$summonner_ongame_id]['timeline']['exp_permin']['0to10'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['xpPerMinDeltas']['zeroToTen'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['exp_permin']['10to20'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['xpPerMinDeltas']['tenToTwenty'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['gold_permin'] = array();
							$data_decoded['players'][$summonner_ongame_id]['timeline']['gold_permin']['0to10'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['goldPerMinDeltas']['zeroToTen'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['gold_permin']['10to20'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['goldPerMinDeltas']['tenToTwenty'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['lanecomparation_cs_permin'] = array();
							$data_decoded['players'][$summonner_ongame_id]['timeline']['lanecomparation_cs_permin']['0to10'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['csDiffPerMinDeltas']['zeroToTen'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['lanecomparation_cs_permin']['10to20'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['csDiffPerMinDeltas']['tenToTwenty'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['lanecomparation_exp_permin'] = array();
							$data_decoded['players'][$summonner_ongame_id]['timeline']['lanecomparation_exp_permin']['0to10'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['xpDiffPerMinDeltas']['zeroToTen'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['lanecomparation_exp_permin']['10to20'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['xpDiffPerMinDeltas']['tenToTwenty'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['dmgtaken_permin'] = array();
							$data_decoded['players'][$summonner_ongame_id]['timeline']['dmgtaken_permin']['0to10'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['damageTakenPerMinDeltas']['zeroToTen'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['dmgtaken_permin']['10to20'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['damageTakenPerMinDeltas']['tenToTwenty'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['lanecomparation_dmgtaken_permin'] = array();
							$data_decoded['players'][$summonner_ongame_id]['timeline']['lanecomparation_dmgtaken_permin']['0to10'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['damageTakenDiffPerMinDeltas']['zeroToTen'];
							$data_decoded['players'][$summonner_ongame_id]['timeline']['lanecomparation_dmgtaken_permin']['10to20'] = @$specific_match_details['participants'][$this_gameplayer_ret]['timeline']['damageTakenDiffPerMinDeltas']['tenToTwenty'];
								
						}
						$data_decoded['players'][$summonner_ongame_id]['masteries'] = array();
						$this_masterynum_count = 0;
						if(array_key_exists('runes',$specific_match_details['participants'][$this_gameplayer_ret]))
						{
							while($this_masterynum_count < count($specific_match_details['participants'][$this_gameplayer_ret]['masteries']))
							{
								$data_decoded['players'][$summonner_ongame_id]['masteries'][$this_masterynum_count] = array();
								$data_decoded['players'][$summonner_ongame_id]['masteries'][$this_masterynum_count]['id'] = $specific_match_details['participants'][$this_gameplayer_ret]['masteries'][$this_masterynum_count]['masteryId'];
								$data_decoded['players'][$summonner_ongame_id]['masteries'][$this_masterynum_count]['points'] = $specific_match_details['participants'][$this_gameplayer_ret]['masteries'][$this_masterynum_count]['rank'];
								$this_masterynum_count++;
							}
						}
						else
						{
							$data_decoded['players'][$summonner_ongame_id]['masteries'][$this_masterynum_count] = 'NOT_USING';
						}
						
						$data_decoded['players'][$summonner_ongame_id]['stats'] = array();
						if($specific_match_details['participants'][$this_gameplayer_ret]['stats']['winner'] == '')
						{
							$data_decoded['players'][$summonner_ongame_id]['stats']['winner'] = 'false';
						}
						else
						{
							$data_decoded['players'][$summonner_ongame_id]['stats']['winner'] = 'true';
							$winner_side_id = $specific_match_details['participants'][$this_gameplayer_ret]['teamId'];
						}
						$data_decoded['players'][$summonner_ongame_id]['stats']['champ_level'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['champLevel'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['items'] = array();
						$data_decoded['players'][$summonner_ongame_id]['stats']['items']['0'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['item0'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['items']['1'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['item1'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['items']['2'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['item2'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['items']['3'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['item3'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['items']['4'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['item4'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['items']['5'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['item5'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['items']['6'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['item6'];
						
						$data_decoded['players'][$summonner_ongame_id]['stats']['kills'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['kills'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['deaths'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['deaths'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['assists'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['assists'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['killscount_doubles'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['doubleKills'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['killscount_triples'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['tripleKills'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['killscount_quadras'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['quadraKills'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['killscount_pentas'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['pentaKills'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['killscount_extrakills'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['unrealKills'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['largest_killing_spree'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['largestKillingSpree'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['dmg_dealt'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['totalDamageDealt'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['dmg_dealt_ap'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['magicDamageDealt'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['dmg_dealt_ad'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['physicalDamageDealt'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['dmg_dealt_true'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['trueDamageDealt'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['dmg_dealt_to_champions'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['totalDamageDealtToChampions'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['dmg_dealt_ap_to_champions'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['magicDamageDealtToChampions'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['dmg_dealt_ad_to_champions'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['physicalDamageDealtToChampions'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['dmg_dealt_true_to_champions'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['trueDamageDealtToChampions'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['dmg_taken'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['totalDamageTaken'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['best_critical_strike'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['largestCriticalStrike'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['healed'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['totalHeal'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['minions'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['minionsKilled'];
						/* Fix for bot games */
						if(array_key_exists('neutralMinionsKilled',$specific_match_details['participants'][$this_gameplayer_ret]['stats']))
						{
							$data_decoded['players'][$summonner_ongame_id]['stats']['jungle_minions'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['neutralMinionsKilled'];
						}
						if(array_key_exists('neutralMinionsKilledTeamJungle',$specific_match_details['participants'][$this_gameplayer_ret]['stats']))
						{
							$data_decoded['players'][$summonner_ongame_id]['stats']['jungle_minions_teamjungle'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['neutralMinionsKilledTeamJungle'];
						}
						if(array_key_exists('neutralMinionsKilledEnemyJungle',$specific_match_details['participants'][$this_gameplayer_ret]['stats']))
						{
							$data_decoded['players'][$summonner_ongame_id]['stats']['jungle_minions_enemyjungle'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['neutralMinionsKilledEnemyJungle'];
						}
						$data_decoded['players'][$summonner_ongame_id]['stats']['total_gold'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['goldEarned'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['spent_gold'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['goldSpent'];
						if($match_history_normals_db['games'][$actualnormalgame]['mapId'] == 8)
						{
							$data_decoded['players'][$summonner_ongame_id]['stats']['dominion_kills_score'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['combatPlayerScore'];
							$data_decoded['players'][$summonner_ongame_id]['stats']['dominion_objectives_score'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['objectivePlayerScore'];
							$data_decoded['players'][$summonner_ongame_id]['stats']['dominion_total_score'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['totalPlayerScore'];
							$data_decoded['players'][$summonner_ongame_id]['stats']['dominion_position'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['totalScoreRank'];
						}
						$data_decoded['players'][$summonner_ongame_id]['stats']['wards_pink'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['visionWardsBoughtInGame'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['wards_green'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['sightWardsBoughtInGame'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['wards_green'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['sightWardsBoughtInGame'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['dmg_taken_ap'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['magicDamageTaken'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['dmg_taken_ad'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['physicalDamageTaken'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['dmg_taken_true'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['trueDamageTaken'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['inhibs_killed_count'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['inhibitorKills'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['towers_killed_count'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['towerKills'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['wards_placed'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['wardsPlaced'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['wards_killed'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['wardsKilled'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['killingsprees_count'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['killingSprees'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['healed_units_count'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['totalUnitsHealed'];
						$data_decoded['players'][$summonner_ongame_id]['stats']['time_on_cc'] = $specific_match_details['participants'][$this_gameplayer_ret]['stats']['totalTimeCrowdControlDealt'];
						$data_decoded['players'][$summonner_ongame_id]['runes'] = array();
						$this_runesnum_count = 0;
						if(array_key_exists('runes',$specific_match_details['participants'][$this_gameplayer_ret]))
						{
							while($this_runesnum_count < count($specific_match_details['participants'][$this_gameplayer_ret]['runes']))
							{
								$data_decoded['players'][$summonner_ongame_id]['runes'][$this_runesnum_count] = array();
								$data_decoded['players'][$summonner_ongame_id]['runes'][$this_runesnum_count]['id'] = $specific_match_details['participants'][$this_gameplayer_ret]['runes'][$this_runesnum_count]['runeId'];
								$data_decoded['players'][$summonner_ongame_id]['runes'][$this_runesnum_count]['count'] = $specific_match_details['participants'][$this_gameplayer_ret]['runes'][$this_runesnum_count]['rank'];
								$this_runesnum_count++;
							}
						}
						else
						{
							$data_decoded['players'][$summonner_ongame_id]['runes']['0'] = 'NOT_USING';
						}
						$this_gameplayer_ret++;
					}
					$data = addslashes(json_encode($data_decoded));
					if(!empty($specific_match_details))
					{
						$db->query('INSERT INTO lol_matches (region,match_id,map_id,loss_prevented,qeue,timestamp_start,timestamp_end,summoner_ids,data,winner_team_id,first_tower,first_herald,first_dragon,first_baron,first_blood,first_inhib) VALUES ("'.$region.'","'.$match_history_normals_db['games'][$actualnormalgame]['gameId'].'","'.$match_history_normals_db['games'][$actualnormalgame]['mapId'].'","'.$refused_status.'","'.$normalmatchinfo_qeue.'","'.datetounix($match_history_normals_db['games'][$actualnormalgame]['createDate']).'","'.(datetounix($match_history_normals_db['games'][$actualnormalgame]['createDate']) + $specific_match_details['matchDuration']).'","'.$summoner_ids.'","'.$data.'","'.$winner_side_id.'","'.$data_first_tower_teamid.'","'.$data_first_herald_teamid.'","'.$data_first_dragon_teamid.'","'.$data_first_baron_teamid.'","'.$data_first_firstblood_teamid.'","'.$data_first_inhib_teamid.'")') or die($db->error);
						
						$actual_match_ips = stdtoarray(json_decode($db->query('SELECT ips_earned FROM lol_matches WHERE match_id="'.$match_history_normals_db['games'][$actualnormalgame]['gameId'].'" AND region="'.$region.'"')->fetch_row()[0]));
						if(is_array($actual_match_ips))
						{
							if(array_key_exists($summonerid,$actual_match_ips) == FALSE)
							{
								$actual_match_ips[$summonerid] = $match_history_normals_db['games'][$actualnormalgame]['ipEarned'];
								$db->query('UPDATE lol_matches SET ips_earned="'.addslashes(json_encode($actual_match_ips)).'" WHERE match_id="'.$match_history_normals_db['games'][$actualnormalgame]['gameId'].'" AND region="'.$region.'"');
							}
						}
						else
						{
							$actual_match_ips[$summonerid] = $match_history_normals_db['games'][$actualnormalgame]['ipEarned'];
							$db->query('UPDATE lol_matches SET ips_earned="'.addslashes(json_encode($actual_match_ips)).'" WHERE match_id="'.$match_history_normals_db['games'][$actualnormalgame]['gameId'].'" AND region="'.$region.'"');
						}
					}
					else
					{
						error_log('RATE_LIMIT_EXCEED: Could not load matchinfo of the game ID ('.$match_history_normals_db['games'][$actualnormalgame]['gameId'].') of ('.$region.').');
					}
			}
			else
			{
				$actual_match_ips = stdtoarray(json_decode($db->query('SELECT ips_earned FROM lol_matches WHERE match_id="'.$match_history_normals_db['games'][$actualnormalgame]['gameId'].'" AND region="'.$region.'"')->fetch_row()[0]));
				
				if(is_array($actual_match_ips))
				{
					if(array_key_exists($summonerid,$actual_match_ips) == FALSE)
					{
						$actual_match_ips[$summonerid] = $match_history_normals_db['games'][$actualnormalgame]['ipEarned'];
						$db->query('UPDATE lol_matches SET ips_earned="'.addslashes(json_encode($actual_match_ips)).'" WHERE match_id="'.$match_history_normals_db['games'][$actualnormalgame]['gameId'].'" AND region="'.$region.'"');
					}
				}
				else
				{
					$actual_match_ips[$summonerid] = $match_history_normals_db['games'][$actualnormalgame]['ipEarned'];
					$db->query('UPDATE lol_matches SET ips_earned="'.addslashes(json_encode($actual_match_ips)).'" WHERE match_id="'.$match_history_normals_db['games'][$actualnormalgame]['gameId'].'" AND region="'.$region.'"');
				}
			}
		
				(int) $actualnormalgame++;
		} 
	}
	}
	
	public static function updatesummoner_runesandmast($summonerid,$region)
	{
		global $db;
		if($db->query('SELECT runesansmast_last_update FROM inv_users WHERE summoner_id="'.$summonerid.'" AND region="'.$region.'"')->fetch_row()[0] + config('profile_runesandmastupdate_interval') < time())
		{
			/* Optimizer: Add some more players to the query for save some querys on api */
			$players = $summonerid;
			$players_query = $db->query('SELECT summoner_id FROM inv_users WHERE runesansmast_last_update  < '.(time() - config('profile_runesandmastupdate_interval')).' AND region="'.$region.'" AND summoner_id!='.$summonerid.' ORDER BY runesansmast_last_update ASC LIMIT 39');
			while($row = $players_query->fetch_row())
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
	public static function updatesummoner_championskill($summonerid,$region,$level,$mmr)
	{
		global $db;
		if(date('m') == 01 or date('m') == 02)
		{
			$typeseason = 'SEASON'.(date('Y')-1);
		}
		else
		{
			$typeseason = 'SEASON'.date('Y');
		}
		$champ_pool = stdtoarray(json_decode($db->query('SELECT data FROM inv_users_rankedstats WHERE summoner_id="'.$summonerid.'" AND region="'.$region.'"')->fetch_row()[0]))[$typeseason]['ranked_champions']['champions'];
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
				$champskill_val = number_format($mmr+(($val['wins']+$val['losses'])+$val['wins']-$val['losses'])+(($val['kills']+$val['assists'])/$deathsfix),0,'','');
				(int) $user_champ_skill_pool[$champskill_id]['skill'] = (int) $champskill_val;
				(int) $user_champ_skill_pool[$champskill_id]['champ_id'] = (int) $champskill_id;
				(int) $user_champ_skill_pool[$champskill_id]['kda'] = number_format((($val['kills']+$val['assists'])/$deathsfix),1);
				(int) $user_champ_skill_pool[$champskill_id]['matches'] = (int) ($val['wins']+$val['losses']);
				(int) $user_champ_skill_pool[$champskill_id]['winrate'] = (int) number_format((100/($val['wins']+$val['losses']))*$val['wins']);
			}
			rsort($user_champ_skill_pool);
		if($db->query('SELECT id FROM inv_users_champskill WHERE summoner_id='.$summonerid.' AND region="'.$region.'"')->num_rows == 0)
		{
			$db->query('INSERT INTO inv_users_champskill (data,summoner_id,region) VALUES ("'.addslashes(json_encode($user_champ_skill_pool)).'",'.$summonerid.',"'.$region.'")') or die($db->error);
		}
		else
		{
			$db->query('UPDATE inv_users_champskill SET data="'.json_encode($user_champ_skill_pool).'" WHERE summoner_id='.$summonerid.' AND region="'.$region.'",)');
		}
		}
	}
	public static function updatesummoner_mainchamp($summonerid,$region,$level)
	{
		global $db;
		if($level == 30 && $db->query('SELECT ranked_league FROM inv_users WHERE summoner_id="'.$summonerid.'" AND region="'.$region.'" LIMIT 1')->fetch_row()[0] != 'U')
		{
			if($db->query('SELECT id FROM inv_users_champskill WHERE summoner_id='.$summonerid.' AND region="'.$region.'"')->num_rows > 0)
			{
				$db->query('UPDATE inv_users SET main_champ="'.champidtokeyname(stdtoarray(json_decode($db->query('SELECT data FROM inv_users_champskill WHERE summoner_id='.$summonerid.' AND region="'.$region.'"')->fetch_row()[0]))[1]['champ_id']).'" WHERE region="'.$region.'" AND summoner_id="'.$summonerid.'"');
			}
			else
			{
				$db->query('UPDATE inv_users SET main_champ="INV_NO_MAIN" WHERE region="'.$region.'" AND summoner_id="'.$summonerid.'"');
			}
		}
		else
		{
			$db->query('UPDATE inv_users SET main_champ="INV_NO_MAIN" WHERE region="'.$region.'" AND summoner_id="'.$summonerid.'"');
		}
	}
	public static function imgcompress($source, $destination, $quality = 90)
	{
		$info = getimagesize($source);
		if ($info['mime'] == 'image/jpeg')
		{
			$image = imagecreatefromjpeg($source);
		}
		elseif($info['mime'] == 'image/gif')
		{
			$image = imagecreatefromgif($source);
		}
		elseif($info['mime'] == 'image/png')
		{
			$image = imagecreatefrompng($source);
		}
		imagejpeg($image, $destination, $quality);
		return $destination;
	}
}
 function config($str)
	{
		global $db;
		$ret = $db->query('SELECT value FROM config WHERE name="'.$str.'" LIMIT 1')->fetch_array(); 
		return $ret['value'];
	}

function retstat($str)
{
	global $db;
	$ret = $db->query('SELECT value FROM lol_stats WHERE stat="'.$str.'" LIMIT 1')->fetch_array(); 
	if($ret['value'] == 0)
	{
		return '0';
	}
	else
	{
		return $ret['value'];
	}
}
function champidtoname($str)
{
	global $db;
	$ret = $db->query('SELECT champname FROM lol_champs WHERE champ_id="'.$str.'" LIMIT 1')->fetch_array(); 
    return $ret['champname'];
}
function leaguetotxt($str)
{
	switch($str)
	{
		case 'U':
		return 'Sin clasificar';
		break;
		case 'B':
		return 'Bronce';
		break;
		case 'S':
		return 'Plata';
		break;
		case 'G':
		return 'Oro';
		break;
		case 'P':
		return 'Platino';
		break;
		case 'D':
		return 'Diamante';
		break;
		case 'M':
		return 'Maestro';
		break;
		case 'C':
		return 'Aspirante';
		break;
	}
}
function spellidtoname($str)
{
	global $db;
	$ret = $db->query('SELECT name FROM lol_spells WHERE spell_id="'.$str.'" LIMIT 1')->fetch_array(); 
    return $ret['name'];
}
function spellidtodesc($str)
{
	global $db;
	$ret = $db->query('SELECT description FROM lol_spells WHERE spell_id="'.$str.'" LIMIT 1')->fetch_array(); 
    return $ret['description'];
}
function champnametoid($str)
{
	global $db;
	$ret = $db->query('SELECT champ_id FROM lol_champ WHERE champname="'.$str.'" LIMIT 1')->fetch_array(); 
    return $ret['champ_id'];
}
function champidtokeyname($str)
{
	global $db;
	$ret = $db->query('SELECT champ_keyname FROM lol_champs WHERE champ_id="'.$str.'" LIMIT 1')->fetch_array(); 
    return $ret['champ_keyname'];
}
function setconfig($cnf, $val)
{
	global $db;
	$ret = $db->query('UPDATE config SET value="'.$val.'" WHERE name="'.$cnf.'"  LIMIT 1');
}
function updating($str)
{
	global $db;
	if(config('updating') == '')
	{
		$strfix = $str;
	}
	else
	{
		$strfix = ';'.$str;
	}
	$ret = $db->query('UPDATE config SET value="'.$db->query('SELECT value FROM config WHERE name="updating"')->fetch_row()[0].$strfix.'" WHERE name="updating" LIMIT 1');
}
function not_updating($str)
{
	global $db;
	if(config('updating') == $str)
	{
		$strfix = str_replace($str, null, $db->query('SELECT value FROM config WHERE name="updating"')->fetch_row()[0]);
	}
	else
	{
		$strfix = str_replace(';'.$str, null, $db->query('SELECT value FROM config WHERE name="updating"')->fetch_row()[0]);
	}
	$ret = $db->query('UPDATE config SET value="'.$strfix.'" WHERE name="updating" LIMIT 1');
}

function nav($str = null)
{
	if($str == 'index'){$index_active = ' class="active"';}else{$index_active = null;}
	if($str == 'champs'){$champs_active = ' class="active"';}else{$champs_active = null;}
	echo '<nav> <ul class="nav navbar-nav main-menu">
                            <li'.$index_active.'><a href="'.URL.'">Inicio</a>
							<ul>
                                    <li><a href="'.URL.'/search">Buscador avanzado</a></li>
                                    <li><a href="'.URL.'/contact">Contacto</a></li>
                                </ul>
							</li>
                            <li>
                                <a href="'.URL.'/statistics">Estadísticas</a>
                                <ul>
                                    <li><a href="'.URL.'/statistics/distribution">Distribución de invocadores</a></li>
                                    <li><a href="'.URL.'/statistics/victory">Estadísticas de victorias</a></li>
                                   <!-- DO  <li><a href="'.URL.'/statistics/length">Duración de partidas</a></li>
                                    <li><a href="'.URL.'/statistics/trinkets">Talismanes</a></li>
                                    <li><a href="'.URL.'/statistics/champions">Campeones</a></li>
                                    <li><a href="'.URL.'/statistics/matchups">Emparejamientos</a></li>
                                    <li><a href="'.URL.'/statistics/matchups">AFKS por liga</a></li>
                                    <li><a href="'.URL.'/statistics/items">Objetos</a></li>
                                    <li><a href="'.URL.'/statistics/spells">Hechizos de invocador</a></li>
                                    <li><a href="'.URL.'/statistics/runes">Runas</a></li>
                                    <li><a href="'.URL.'/statistics/masteries">Maestrías</a></li> -->
                                </ul>
                            </li>
                            <li'.$champs_active.'>
                                <a href="'.URL.'/game">Juego</a>
                                <ul>
                                    <li'.$champs_active.'><a href="'.URL.'/champions">Campeones</a></li>
									<li class="dropdown"><a href="'.URL.'/wiki" class="dropdown-toggle">Wiki</a>
                                        <ul class="dropdown-menu">
                                            <li><a href="'.URL.'/wiki/pbe">PBE</a></li>
                                            <li><a href="'.URL.'/wiki/patches">Parches</a></li>
                                            <li><a href="'.URL.'/wiki/proplayers">Proplayers</a></li>
                                        </ul>
                                    </li>
									
									<li class="dropdown">
										<a href="">Rankings</a>
										<ul>
										 <li><a href="'.URL.'/best/players">Mejores jugadores</a></li>
											<li><a href="'.URL.'/best/teams">Mejores equipos</a></li>
											<li><a href="'.URL.'/records">Récords</a></li>
										</ul>
									</li>
                                    <li><a href="'.URL.'/offers">Ofertas</a></li>
                                    <li><a href="'.URL.'/champs/rotation">Rotación de campeones</a></li>
									<li><a href="'.URL.'/lol_status">Estado de servidores</a></li>
									<li><a href="'.URL.'/promoted">Partidas promocionadas</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="http://news.'.BASEURL.'">Noticias</a>
                            </li>
							
							
							 <!-- <li>
                                <a href="headers.html">Utilidades</a>
                                <ul>
                                    <li><a href="headers.html">Páginas de runas</a></li>
                                    <li><a href="'.URL.'/utilities/masterycalc">Páginas de maestrias</a></li>
                                    <li><a href="home-4.html">Calculadora de PI</a></li>
                                    <li><a href="home-4.html">Repeticiones</a></li>
									 <ul>
                                    <li><a href="headers.html">Todas</a></li>
                                    <li><a href="home-4.html">Con pentakills</a></li>
                                    <li><a href="home-4.html">Con KDA alto</a></li>
                                    <li><a href="home-4.html">Repeticiones de la LCS</a></li>
									</ul>
                                    <li><a href="home-4.html">Ping al acabar partida</a></li>
                                    <li><a href="home-4.html">Chat lol</a></li>
                                    <li><a href="home-4.html">Calculadora MMR</a></li>
                                    <li><a href="home-4.html">¿He salido en promocionadas?</a></li>
                                    <li><a href="home-4.html">Tiempo jugado</a></li>
                                </ul>
                            </li> -->
							<!-- <li>
                                <a href="#">Competitivo</a>
								<ul>
									<li><a href="'.URL.'/competitive/lcs_na" class="dropdown-toggle">NA LCS</a></li>
									<li><a href="'.URL.'/competitive/lcs_eu" class="dropdown-toggle">EU LCS</a></li>
									<li><a href="'.URL.'/competitive/lck" class="dropdown-toggle">LCK - Campeones de corea</a></li>
									<li><a href="'.URL.'/competitive/lpl" class="dropdown-toggle">LPL</a></li>
									<li><a href="'.URL.'/competitive/lmc" class="dropdown-toggle">LMS</a></li>
									<li><a href="'.URL.'/competitive/challengerseries_na" class="dropdown-toggle">NA Challenger series</a></li>
									<li><a href="'.URL.'/competitive/challengerseries_eu" class="dropdown-toggle">EU Challenger series</a></li>
									<li><a href="'.URL.'/competitive/allstar" class="dropdown-toggle">All-Star</a></li>
									<li><a href="'.URL.'/competitive/interwildcard" class="dropdown-toggle">International wildcard</a></li>
									<li><a href="'.URL.'/competitive/invitational" class="dropdown-toggle">Mid season invitational</a></li>
									<li><a href="'.URL.'/competitive/worlds" class="dropdown-toggle">Worlds</a></li>
								</ul>
                            </li> -->
							<li>
                                <a href="doc.html">Guías y consejos</a>
								<ul>
                                    <li><a href="home-4.html">Builds de profesionales</a></li>
								</ul>
                            </li>
                        </ul>

                        <!-- Top links
                        ================================================== -->
                        <ul class="nav navbar-nav navbar-right">
                            <li class="header-search-form"><a href="javascript:;"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a></li>
                            <li class="header-shop-cart"><a href="#"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span></a></li>
                            <li class="header-menu-icon"><a class="nav-main-trigger" href="javascript:;"><span class="nav-menu-icon"></span></a></li>
                        </ul>
                    </nav>';
}
function footer()
{
	if(config('coding') == 'true')
	{
		global $starttime;
		$timer_end = explode(' ',microtime());
		$finaltime = $timer_end[0] + $timer_end[1];
		$finalgeneratedtime = round($finaltime - $starttime,3);
		$loadingtime_final = 'Pagina generada en '.$finalgeneratedtime.' segundos';
	}
	else
	{
		$loadingtime_final = null;
	}
	return '<div id="sub-footer" class="sub-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-md-6">
                            © ONLoL por <a href="'.URL.'/about/owner">Andrei García</a>. '.$loadingtime_final.'
                        </div>
                        <div class="col-sm-6 col-md-6 text-right">
                            <ul class="footer-menu list-inline">
                                <li><a href="'.URL.'/contact">Contacto</a></li>
                                <li><a href="'.URL.'/config">Configuración</a></li>
                            </ul>
                            &nbsp;&nbsp;&nbsp;
                            <a href="https://twitter.com/ONLoLweb" class="social_twitter no-style"></a>
                            <a href="https://www.facebook.com/Onlol-790065187789568/" class="social_facebook no-style"></a>      
                        </div>
                    </div><!-- .row -->
                </div><!-- .container -->
            </div>
			<script>
  (function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,"script","//www.google-analytics.com/analytics.js","ga");

  ga("create", "UA-55250743-2", "auto");
  ga("send", "pageview");

</script>';
}
function roletolang($str)
{
	if($str == 'tank')
	{
		return 'Tanque';
	}
	if($str == 'support')
	{
		return 'Soporte';
	}
	if($str == 'marksman')
	{
		return 'Tirador';
	}
	if($str == 'mage')
	{
		return 'Mago';
	}
	if($str == 'fighter')
	{
		return 'Luchador';
	}
	if($str == 'assassin')
	{
		return 'Asesino';
	}
}
function spellbartolang($str)
{
	if($str == 'bloodwell' or $str == 'mana' or $str == 'energy' or $str == 'wind' or $str == 'none' or $str == 'gnarfury' or $str == 'battlefury' or $str == 'rage' or $str == 'ferocity' or $str == 'heat' or $str == 'dragonfury' )
	{
	if($str == 'bloodwell')
	{
		return 'Pozo sangriento';
	}
	if($str == 'mana')
	{
		return 'Maná';
	}
	if($str == 'energy')
	{
		return 'Energía';
	}
	if($str == 'wind')
	{
		return 'Viento';
	}
	if($str == 'none')
	{
		return 'Sin barra secundaria';
	}
	if($str == 'gnarfury')
	{
		return 'Furia ¡GNAR!';
	}
	if($str == 'battlefury')
	{
		return 'Furia de batalla';
	}
	if($str == 'rage')
	{
		return 'Furia';
	}
	if($str == 'ferocity')
	{
		return 'Ferocidad';
	}
	if($str == 'heat')
	{
		return 'Calor';
	}
	if($str == 'dragonfury')
	{
		return 'Furia dragón';
	}
	}
	else
	{
		return $str;
	}
}
function mapidtotxt($str)
{
	if($str == '1' or $str == '10' or $str == '11' or $str == '12')
	{
	if($str == '1')
	{
		return 'Antigua grieta del invocador';
	}
	if($str == '8')
	{
		return 'Cicatriz de cristal';
	}
	if($str == '10')
	{
		return 'Bosque retorcido';
	}
	if($str == '11')
	{
		return 'Grieta del invocador';
	}
	if($str == '12')
	{
		return 'Abismo de los lamentos';
	}
	}
	else
	{
		return 'Mapa desconocido';
	}
}
function gametypestr($str)
{
	switch($str)
	{
		case 0:
		return 'Personalizada';
		break;
		case 8:
		return 'Normal a ciegas 3x3';
		break;
		case 2:
		return 'Normal a ciegas 5x5';
		break;
		case 14:
		return 'Normal de reclutamiento 5x5';
		break;
		case 41:
		return 'Clasificatoria de equipos 3x3';
		break;
		case 42:
		return 'Clasificatoria de equipos 5x5';
		break;
		case 16:
		return 'Normal a ciegas 5x5';
		break;
		case 17:
		return 'Normal de reclutamiento 5x5';
		break;
		case 4:
		return 'Clasificatoria en solitario';
		break;
		case 25:
		return 'Cooperativo vs bots 5x5';
		break;
		case 31:
		return 'Cooperativo vs bots introducción 5x5';
		break;
		case 32:
		return 'Cooperativo vs bots principiante 5x5';
		break;
		case 33:
		return 'Cooperativo vs bots intermedio 5x5';
		break;
		case 52:
		return 'Cooperativo vs bots 3x3';
		break;
		case 61:
		return 'Creador de equipos';
		break;
		case 65:
		return 'ARAM';
		break;
		/* Special modes */
		case 70:
		return '¡Uno para todos!';
		break;
		case 72:
		return 'Uno contra uno';
		break;
		case 73:
		return 'Dos contra dos';
		break;
		case 75:
		return 'Hexakill'; //RIFT
		break;
		case 76:
		return 'URF';
		break;
		case 83:
		return 'URF contra bots';
		break;
		case 91:
		return 'Bots de pesadilla - Nivel 1';
		break;
		case 92:
		return 'Bots de pesadilla - Nivel 2';
		break;
		case 93:
		return 'Bots de pesadilla - Nivel 5';
		break;
		case 96:
		return 'Ascensión';
		break;
		case 98:
		return 'Hexakil'; //TREELINE
		break;
		case 100:
		return 'Puente del carnicero';
		break;
		case 300:
		return 'Rey poro';
		break;
		case 310:
		return 'Némesis';
		break;
		case 313:
		return 'Mercado negro';
		break;
	}
}

function url_exists($url) 
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	curl_exec($ch);
	$webcurlstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($webcurlstatus == 200 or $webcurlstatus == 301)
	{
		$urlfound = true;
		return true;
	}
	elseif($webcurlstatus == 429)
	{
		$urlfound = true;
		return true;
	}
	elseif(empty($urlfound))
	{
		return false;
	}
}
function summonerupdate($name, $server, $redirect = false)
{
	set_time_limit(0); /* Fix the timeout */
	global $db;
	$region = parseserver($server);
	/* Get another update summoners */
	
	$all_summoners = $db->query('SELECT name FROM inv_users WHERE onlol_last_update < '.(time()-config('profile_autorenew')).' AND region="'.$region.'" LIMIT '.config('max_invreloads_per_time'));
	
	$summonerbase = str_replace(' ', '', strtolower($name));
	$all_summoners_str = str_replace(' ', '', strtolower($name));
	$all_summoners_count = 0;
	while($row = $all_summoners->fetch_row())
	{
		if(str_replace(' ', '', strtolower($row[0])) != $summonerbase)
		{
			$all_summoners_str .= ',';
			$all_summoners_str .= str_replace(' ', '', strtolower($row[0]));
		}
		$all_summoners_count++;
	}
	$summoner_info = readjson('https://'.$region.'.api.pvp.net/api/lol/'.$region.'/v1.4/summoner/by-name/'.$all_summoners_str.'?api_key='.LOL_API_KEY);

	$this_updated_summoner = 0;
	while($this_updated_summoner < count($summoner_info))
	{
		$this_summoner_name = explode(',',$all_summoners_str);
		if(array_key_exists($this_summoner_name[$this_updated_summoner],$summoner_info))
		{
			$summonerid = $summoner_info[$this_summoner_name[$this_updated_summoner]]['id'];
			$summoner = $summoner_info[$this_summoner_name[$this_updated_summoner]]['name'];
			$level = $summoner_info[$this_summoner_name[$this_updated_summoner]]['summonerLevel'];
			$icon = $summoner_info[$this_summoner_name[$this_updated_summoner]]['profileIconId'];
			$lolrevision = datetounix($summoner_info[$this_summoner_name[$this_updated_summoner]]['revisionDate']);
				
			/* Add summoner baseinfo to database */
			if($db->query('SELECT id FROM inv_users WHERE summoner_id='.$summonerid.'')->num_rows == 0)
			{
			$db->query('INSERT INTO inv_users (summoner_id,name,region,level,icon,lol_last_update) VALUES ('.$summonerid.',"'.$summoner.'","'.$region.'",'.$level.','.$icon.','.$lolrevision.')');
			}
			/* Improve profile data */
			onlol::updatesummoner_leagues($summonerid,$summoner,$region,$level,$icon,$lolrevision);
			onlol::updatesummoner_rankedgames($summonerid,$summoner,$region,$level,$db->query('SELECT ranked_league FROM inv_users WHERE summoner_id='.$summonerid.' AND region="'.$region.'"')->fetch_row()[0]);
			onlol::updatesummoner_last10matches($summonerid,$region);
			onlol::updatesummoner_championskill($summonerid,$region,$level,$db->query('SELECT mmr FROM inv_users WHERE summoner_id='.$summonerid.' AND region="'.$region.'"')->fetch_row()[0]);
			onlol::updatesummoner_mainchamp($summonerid,$region,$level);
			onlol::updatesummoner_runesandmast($summonerid,$region);
			
			$db->query('UPDATE inv_users SET onlol_last_update="'.time().'",name="'.$summoner.'",region="'.$server.'",level="'.$level.'",icon="'.$icon.'",lol_last_update="'.$lolrevision.'" WHERE summoner_id='.$summonerid.''); //falta lol last update y poner WHERE USER_ID=ID
		}
		else
		{
			return false;
		}
		$this_updated_summoner++;
	}
	if($redirect != false)
	{
		redirect($redirect);
	}
}



function divisionbasemmr($str)
{
	switch($str)
	{
			case 'BRONZEV':
				$elo = 870;
			break;
			case 'BRONZEIV':
                $elo = 940;
                break;
            case 'BRONZEIII':
                $elo = 1010;
                break;
            case 'BRONZEII':
                $elo = 1080;
                break;
            case 'BRONZEI':
                $elo = 1150;
                break;
            case 'SILVERV':
                $elo = 1220;
                break;
            case 'SILVERIV':
                $elo = 1290;
                break;
            case 'SILVERIII':
                $elo = 1360;
                break;
            case 'SILVERII':
                $elo = 1430;
                break;
            case 'SILVERI':
                $elo = 1500;
                break;
            case 'GOLDV':
                $elo = 1570;
                break;
            case 'GOLDIV':
                $elo = 1640;
                break;
            case 'GOLDIII':
                $elo = 1710;
                break;
            case 'GOLDII':
                $elo = 1780;
                break;
            case 'GOLDI':
                $elo = 1850;
                break;
            case 'PLATINUMV':
                $elo = 1920;
                break;
            case 'PLATINUMIV':
                $elo = 1990;
                break;
            case 'PLATINUMIII':
                $elo = 2060;
                break;
            case 'PLATINUMII':
                $elo = 2130;
                break;
            case 'PLATINUMI':
                $elo = 2200;
                break;
            case 'DIAMONDV':
                $elo = 2270;
                break;
            case 'DIAMONDIV':
                $elo = 2340;
                break;
            case 'DIAMONDIII':
                $elo = 2410;
                break;
            case 'DIAMONDII':
                $elo = 2480;
                break;
            case 'DIAMONDI':
                $elo = 2550;
                break;
            case 'MASTERI':
                $elo = 2600;
                break;
            case 'CHALLENGERI':
                $elo = 2900;
                break;
			default:
				$elo = 0;
}
return $elo;
}
function summonerinfo($summonerid, $row)
{
	global $db;
	$ret = $db->query('SELECT '.$row.' FROM inv_users WHERE summoner_id="'.$summonerid.'" LIMIT 1')->fetch_array(); 
    return $ret[$row];
}
function summonerinfoteams($summonerid, $row, $qeue = '5x5')
{
	global $db;
	$ret = $db->query('SELECT '.$row.' FROM inv_users_teams WHERE summoner_ids LIKE "'.$summonerid.'" AND qeue="'.$qeue.'" ORDER BY FIELD(division,"U", "B", "S", "G", "P", "D", "M", "C") DESC,FIELD(division,"1", "2", "3", "4", "5") ASC,lp ASC LIMIT 1')->fetch_array(); 
    return $ret[$row];
}
function parsedisivion($parse)
{
		if(is_numeric($parse) == TRUE)
		{
			switch($parse)
			{
				case 1:
				return 'I';
				break;
				case 2:
				return 'II';
				break;
				case 3:
				return 'III';
				break;
				case 4:
				return 'IV';
				break;
				case 5:
				return 'V';
				break;
				default: return 'I';
			}
		}
		else
		{
			switch($parse)
			{
				case 'I':
				return 1;
				break;
				case 'II':
				return 2;
				break;
				case 'III':
				return 3;
				break;
				case 'IV':
				return 4;
				break;
				case 'V':
				return 5;
				break;
				default: return 1;
			}
		}
	
}
function readarray($str, $die = true)
{
return '<pre>'.var_dump($str).'</pre>';
if($die == true)
{
die();
}
}
function rclickmenu()
{
	if(config('coding') != 'true')
	{
	if(!empty($_COOKIE['onlol_baseinv']))
	{
		global $db;
		$databaseinv = explode('/',$_COOKIE['onlol_baseinv']);
		$summonerbase = $databaseinv[0];
		$regionbase = parseserver($databaseinv[1]);
		$summonerbasename = $db->query('SELECT name FROM inv_users WHERE summoner_id="'.$summonerbase.'" AND region="'.$regionbase.'" LIMIT 1')->fetch_row()[0];
		return '
		<!-- Context menu -->
		<script src="'.URL.'/style/js/jquery.nu-context-menu.min.js"></script>
		<link rel="stylesheet" type="text/css" href="'.URL.'/style/css/nu-context-menu.css">
		<script>
			$(function() {
			var context = $("#rclick")
				.nuContextMenu({
        
				hideAfterClick: true,
          
				items: "",

				callback: function(key, element) {
					if(key == "profile")
					{
						window.location="'.URL.'/summoner/'.$regionbase.'/'.$summonerbasename.'";
					}
					if(key == "activegame")
					{
						window.location="'.URL.'/game/'.$regionbase.'/'.$summonerbasename.'";
					}
				},
		
				menu: {

					"profile": {
					title: "Perfil de '.$summonerbasename.'",
					icon: "icon_house",
					},
					"void": "separator",

					"activegame": {
					title: "Partida de '.$summonerbasename.'",
					icon: "icon_search_alt",
					},
				}
				});

			});
		</script>';
	}
	else
	{
		return '
		<!-- Context menu -->
		<script src="'.URL.'/style/js/jquery.nu-context-menu.min.js"></script>
		<link rel="stylesheet" type="text/css" href="'.URL.'/style/css/nu-context-menu.css">
		<script>
			$(function() {
			var context = $("#rclick")
				.nuContextMenu({
        
				hideAfterClick: true,
          
				items: "",

				callback: function(key, element) {
					
				},
		
				menu: {

					"profile": {
					title: "¡Haz un usuario principal y comienza a usar el menú rápido!",
					icon: "icon_profile",
					},
				}
				});

			});
		</script>';
	}
	}
}
function parseserver($server, $all = false)
{
	if($all == false)
	{
		switch(strtolower($server))
		{
		case 'euw':
		return 'euw';
		break;
		case 'na':
		return 'na';
		break;
		case 'br':
		return 'br';
		break;
		case 'kr':
		return 'kr';
		break;
		case 'tr':
		return 'tr';
		break;
		case 'eune':
		return 'eune';
		break;
		case 'lan':
		return 'lan';
		break;
		case 'las':
		return 'las';
		break;
		case 'ru':
		return 'ru';
		break;
		case 'oce':
		return 'oce';
		break;
		case 'pbe':
		return 'pbe';
		break;
		default: return 'euw';
		}
	}
	if($all == true)
	{
		switch(strtolower($server))
		{
		case 'euw':
		return 'euw';
		break;
		case 'na':
		return 'na';
		break;
		case 'br':
		return 'br';
		break;
		case 'kr':
		return 'kr';
		break;
		case 'tr':
		return 'tr';
		break;
		case 'eune':
		return 'eune';
		break;
		case 'lan':
		return 'lan';
		break;
		case 'las':
		return 'las';
		break;
		case 'ru':
		return 'ru';
		break;
		case 'oce':
		return 'oce';
		break;
		case 'pbe':
		return 'pbe';
		break;
		default: return 'all';
		}
	}
}
function time_elapsed_string($ptime)
{
    $etime = time() - $ptime;

    if ($etime < 1)
    {
        return '0 segundos';
    }

    $a = array( 365 * 24 * 60 * 60  =>  'año',
                 30 * 24 * 60 * 60  =>  'mes',
                      24 * 60 * 60  =>  'día',
                           60 * 60  =>  'hora',
                                60  =>  'minuto',
                                 1  =>  'segundo'
                );
    $a_plural = array( 'año'   => 'años',
                       'mes'  => 'meses',
                       'día'    => 'días',
                       'hora'   => 'horas',
                       'minuto' => 'minutos',
                       'segundo' => 'segundos'
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . '';
        }
    }
}
function redirect($url, $permanent = false) {
	if($permanent) {
		header('HTTP/1.1 301 Moved Permanently');
	}
	header('Location: '.$url);
	exit();
}

function legaveragemmr($league, $division)
{
	global $db;
	switch($league.$division)
	{
			case 'B5':
				$transformed_div = 'BRONZE5';
			break;
			case 'B4':
                $transformed_div = 'BRONZE4';
                break;
            case 'B3':
                $transformed_div = 'BRONZE3';
                break;
            case 'B2':
                $transformed_div = 'BRONZE2';
                break;
            case 'B1':
                $transformed_div = 'BRONZE1';
                break;
            case 'S5':
                $transformed_div = 'SILVER5';
                break;
            case 'S4':
                $transformed_div = 'SILVER4';
                break;
            case 'S3':
                $transformed_div = 'SILVER3';
                break;
            case 'S2':
                $transformed_div = 'SILVER2';
                break;
            case 'S1':
                $transformed_div = 'SILVER1';
                break;
            case 'G5':
                $transformed_div = 'GOLD5';
                break;
            case 'G4':
                $transformed_div = 'GOLD4';
                break;
            case 'G3':
                $transformed_div = 'GOLD3';
                break;
            case 'G2':
                $transformed_div = 'GOLD2';
                break;
            case 'G1':
                $transformed_div = 'GOLD1';
                break;
            case 'P5':
                $transformed_div = 'PLATINUM5';
                break;
            case 'P4':
                $transformed_div = 'PLATINUM4';
                break;
            case 'P3':
                $transformed_div = 'PLATINUM3';
                break;
            case 'P2':
                $transformed_div = 'PLATINUM2';
                break;
            case 'P1':
                $transformed_div = 'PLATINUM1';
                break;
            case 'D5':
                $transformed_div = 'DIAMOND5';
                break;
            case 'D4':
                $transformed_div = 'DIAMOND4';
                break;
            case 'D3':
                $transformed_div = 'DIAMOND3';
                break;
            case 'D2':
                $transformed_div = 'DIAMOND2';
                break;
            case 'D1':
                $transformed_div = 'DIAMOND1';
                break;
            case 'M1':
                $transformed_div = 'MASTER1';
                break;
            case 'C1':
                $transformed_div = 'CHALLENGER1';
                break;
			default:
				$transformed_div = 'BRONZE5';
	}
	return (int) $db->query('SELECT average FROM mmr_leagueaverage WHERE league="'.$transformed_div.'"')->fetch_row()[0];
}
function adblock()
{
	echo '

				<h5 class="bg-success" id="fuck-adb-not-enabled" style="display: none;">AdBlock is not enabled</h5>
				<h5 class="bg-danger" id="fuck-adb-enabled" style="display: none;">AdBlock is enabled</h5>
			
	
	<script src="'.URL.'/style/js/adblock.js"></script>
	<script>
		function adBlockDetected() {
			swal({   title: "¿Adblock?",  html: "<p>Somos una web gratuita con anuncios no molestos. Ofrecemos un gran sercivio mejorado a diario y activando los anuncios contribuirías a pagar los servidores. Si te gusta ONLoL, considera desactivar AdBlock.</p><br><input type=\"checkbox\" id=\"adblock_status\" value=\"enabled\" checked> Quiero apoyar a ONLoL, mostrar anuncios no molestos.<br><br> Además, al apoyar a ONLoL, tendrás tiempos de carga más lentos y mejores funciones. Solo debes <a style=\"color:blue\"href=\"'.URL.'/adblock\">desactivar adblock.</a>", imageUrl:"'.URL.'/style/images/adblock.png",  showCancelButton: false, allowOutsideClick:false, allowEscapeKey:false,   closeOnConfirm: false }, function() {   if($("#adblock_status").val() == "enabled") { window.location="'.URL.'/adblock/enabled"; } else { window.location="'.URL.'/adblock/enabled"; }     });
		}
		function adBlockNotDetected() {
			//Do nothing
		}
		
		if(typeof fuckAdBlock === "undefined") {
			adBlockDetected();
		} else {
			fuckAdBlock.setOption({ debug: true });
			fuckAdBlock.onDetected(adBlockDetected).onNotDetected(adBlockNotDetected);
		}
		
	</script>
';
}
function datetounix($str)
{
	$epoch = substr($str, 0, -3);
	$dt = new DateTime("@$epoch");
	return $dt->getTimestamp();
}
function readjson($url)
{
	//se podrian cachear los archivos localmente para que fuese mas rapido
		$url = utf8_encode($url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url);
		$result=curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpCode == 404) {
			error_log('[readjson] NOT_FOUND: '.$url);
			return 'NOT_FOUND';
		}
		
		if($httpCode == 429) {
			error_log('[readjson] RATE_LIMIT_EXCEED: '.$url);
		}
		
		if($httpCode == 200) {
			return json_decode($result, true);
		}
		curl_close($ch);
	
}

define('REPORT_URL', 'http://localhost/report.php');
function generatecode($name = 'Torneo por defecto', $password=null, $nameid='onlol_match_code', $map, $type, $size, $spectators)
{
switch($map)
{
	case 'rift':
	$generator_map = 'map11';
	break;
	case 'tree':
	$generator_map = 'map10';
	break;
	case 'scar':
	$generator_map = 'map8';
	break;
	case 'abyss':
	$generator_map = 'map12';
	break;
	default: $generator_map = 'map11';
}
switch($type)
{
	case 'BLIND_PICK':
	$generator_type = 'pick1';
	break;
	case 'DRAFT_MODE':
	$generator_type = 'pick2';
	break;
	case 'ALL_RAMDOM':
	$generator_type = 'pick4';
	break;
	case 'TOURNAMENT_DRAFT':
	$generator_type = 'pick6';
	break;
	default: $generator_type = 'pick6';
}
switch($size)
{
	case '1v1':
	$generator_size = 'team1';
	break;
	case '2v2':
	$generator_size = 'team2';
	break;
	case '3v3':
	$generator_size = 'team3';
	break;
	case '4v4':
	$generator_size = 'team4';
	break;
	case '5v5':
	$generator_size = 'team5';
	break;
	default: $generator_size = 'team5';
}
switch($spectators)
{
	case 'ALLOW_ALL':
	$generator_spectators = 'specALL';
	break;
	case 'ALLOW_NOTHING':
	$generator_spectators = 'specNONE';
	break;
	case 'ALLOW_LOBBY':
	$generator_spectators = 'specLOBBYONLY';
	break;
	default: $generator_spectators = 'specALL';
}

$url_format = 'pvpnet://lol/customgame/joinorcreate/'.$generator_map.'/'.$generator_type.'/'.$generator_size.'/'.$generator_spectators.'/'.base64_encode(json_encode(array('name' => $name, 'extra' => $nameid, 'password' => $password, 'report' => REPORT_URL), JSON_UNESCAPED_SLASHES)).'';
return $url_format;
}
function parserole($role)
{
	switch($role)
	{
		case 'support':
		return 'Soporte';
		break;
		case 'tank':
		return 'Tanque';
		break;
		case 'marksman':
		return 'Tirador';
		break;
		case 'mage':
		return 'Mago';
		break;
		case 'fighter':
		return 'Luchador';
		break;
		case 'assasin':
		return 'Asesino';
		break;
	}
}
 function checkrage($wl, $diff) {
	 $ragemeter = 0;
	
                    
                    if($diff == 0)
					{
						$ragemeter += 5;
					}
					if($diff < 3)
					{
						$ragemeter += 2;
					}
					if($diff < 5)
					{
						$ragemeter += 1;
					}
					if($diff < 10)
					{
						$ragemeter += 0.5;
					}
					if($diff > 10)
					{
						$ragemeter += 0.1;
					}
                   
                    
					if ($diff > 0) {
                        if ($ragemeter > 0) 
						{
							$ragemeter += -(6 * $diff * $ragemeter) / 100;
						}
                        if ($ragemeter < 0)
						{
							$ragemeter += (6 * $diff * $ragemeter) / 100;
						}
                    }
					
                    if ($wl > 0) {
                        if ($ragemeter > 0) $ragemeter += -(10 * $wl * $ragemeter) / 100;
                        if ($ragemeter < 0) $ragemeter += (10 * $wl * $ragemeter) / 100;
                     }

                $total = round(($ragemeter * 100) / $wl);
                if ($total > 100) 
				{
					$total = 100;
				}
                else $total = $total;
	return $total;
}
function stdtoarray($d) {
	if (is_object($d)) {
	// Gets the properties of the given object
	// with get_object_vars function
	$d = get_object_vars($d);
	}
	
	if (is_array($d)) {
	/*
	* Return array converted to object
	* Using __FUNCTION__ (Magic constant)
	* for recursive call
	*/
	return array_map(__FUNCTION__, $d);
	}
	else {
	// Return array
	return $d;
	}
}
function gametypes($str)
{
	 switch($str)
	 {
	case 'NONE':
	return 'NONE';
	break;
	case 'NORMAL':
	return 'Normal 5x5';
	break;
	case 'BOT':
	return 'Cooperativo vs bots';
	break;
	case 'RANKED_SOLO_5x5':
	return 'Clasificatoria en solitario';
	break;
	case 'RANKED_PREMADE_3x3': //DEPRECEATED
	return 'Clasificatoria por equipos 3x3';
	break;
	case 'RANKED_PREMADE_5x5': //DEPRECEATED
	return 'Clasificatoria por equipos 5x5';
	break;
	case 'ODIN_UNRANKED':
	return 'Dominion';
	break;
	case 'RANKED_TEAM_3x3':
	return 'Clasificatoria por equipos 3x3';
	break;
	case 'RANKED_TEAM_5x5':
	return 'Clasificatoria por equipos 5x5';
	break;
	case 'NORMAL_3x3':
	return 'Partida normal 3x3';
	break;
	case 'BOT_3x3':
	return 'Cooperativo vs IA 3v3';
	break;
	case 'CAP_5x5':
	return 'Creador de equipos';
	break;
	case 'ARAM_UNRANKED_5x5':
	return 'ARAM';
	break;
	case 'ONEFORALL_5x5':
	return 'Uno para todos';
	break;
	case 'FIRSTBLOOD_1x1':
	return 'Primera sangre 1x1';
	break;
	case 'FIRSTBLOOD_2x2':
	return 'Primera sangre 2x2';
	break;
	case 'SR_6x6':
	return 'Hexakill grieta del invocador';
	break;
	case 'URF':
	return 'URF';
	break;
	case 'URF_BOT':
	return 'URF Bots';
	break;
	case 'NIGHTMARE_BOT':
	return 'URF Bots malditos';
	break;
	case 'ASCENSION':
	return 'Ascensión';
	break;
	case 'HEXAKILL':
	return 'Hexakill bosque retorcido';
	break;
	case 'KING_PORO':
	return 'Rey poro';
	break;
	case 'COUNTER_PICK':
	return 'némesis';
	break;
	case 'BILGEWATER':
	return 'Aguas estancadas';
	break;
	case 'CUSTOM_GAME':
	return 'Personalizada';
	break;
	default: return $str;
	}
}
function killstr($str)
{
		switch($str)
		{
			case 'NONE':
			return 'Asesinatos aislados';
			break;
			case 'DOUBLE':
			return 'Asesinato doble';
			break;
			case 'TRIPLE':
			return 'Asesinato triple';
			break;
			case 'QUADRA':
			return 'Asesinato cuádruple';
			break;
			case 'PENTA':
			return 'Pentakill';
			break;
			default: return 'Uno';
		}
}
function positionstr($str)
{
		switch($str)
		{
			case 'TOP':
			return 'Top';
			break;
			case 'JUNGLE':
			return 'Jungla';
			break;
			case 'MID':
			return 'Mid';
			break;
			case 'SUPPORT':
			return 'Soporte';
			break;
			case 'ADC':
			return 'Adc';
			break;
			default: return $str;
		}
}
function runestatstr($str)
{
	switch($str)
	{
		case 'FlatHPPoolMod':
		return 'HP';
		break;
		case 'rFlatHPModPerLevel':
		return 'HP por nivel';
		break;
		case 'FlatMPPoolMod':
		
		break;
		case 'rFlatMPModPerLevel':
		
		break;
		case 'PercentHPPoolMod':
		return '% HP';
		break;
		case 'FlatHPRegenMod':
		return 'Regeneración de HP';
		break;
		case 'rFlatHPRegenModPerLevel':
		return 'Regeneración de HP por nivel';
		break;
		case 'PercentHPRegenMod':
		return '% Regeneración de HP';
		break;
		case 'FlatMPRegenMod':
		return '% Regeneración de maná';
		break;
		case 'rFlatMPRegenModPerLevel':
		return '% Regeneración de mana por nivel';
		break;
		case 'PercentMPRegenMod':
		return '% Regeneración de maná por nivel';
		break;
		case 'FlatArmorMod':
		return 'Armadura';
		break;
		case 'rFlatArmorModPerLevel':
		return 'Armadura por nivel';
		break;
		case 'PercentArmorMod':
		return '% Armadura';
		break;
		case 'rFlatArmorPenetrationMod':
		return 'Penetración de armadura';
		break;
		case 'rFlatArmorPenetrationModPerLevel':
		return 'Penetración de armadura por nivel';
		break;
		case 'rPercentArmorPenetrationMod':
		return '% Penetración de armadura';
		break;
		case 'rPercentArmorPenetrationModPerLevel':
		return '% Penetración de armadura por nivel';
		break;
		case 'FlatPhysicalDamageMod':
		return 'Daño de ataque';
		break;
		case 'rFlatPhysicalDamageModPerLevel':
		return 'Daño de ataque por nivel';
		break;
		case 'PercentPhysicalDamageMod':
		return '% Daño de ataque';
		break;
		case 'FlatMagicDamageMod':
		return 'Poder de habilidad';
		break;
		case 'rFlatMagicDamageModPerLevel':
		return 'Poder de habilidad por nivel';
		break;
		case 'PercentMagicDamageMod':
		return '% Poder de habilidad';
		break;
		case 'FlatMovementSpeedMod':
		return 'Velocidad de movimiento';
		break;
		case 'rFlatMovementSpeedModPerLevel':
		return 'Velocidad de movimiento por nivel';
		break;
		case 'PercentMovementSpeedMod':
		return '% Velocidad de movimiento';
		break;
		case 'rPercentMovementSpeedModPerLevel':
		return '% Velocidad de movimiento por nivel';
		break;
		case 'FlatAttackSpeedMod':
		return 'Velocidad de ataque';
		break;
		case 'PercentAttackSpeedMod':
		return '% Velocidad de ataque';
		break;
		case 'rPercentAttackSpeedModPerLevel':
		
		break;
		case 'rFlatDodgeMod':
		
		break;
		case 'rFlatDodgeModPerLevel':
		
		break;
		case 'PercentDodgeMod':
		
		break;
		case 'FlatCritChanceMod':
		
		break;
		case 'rFlatCritChanceModPerLevel':
		
		break;
		case 'PercentCritChanceMod':
		
		break;
		case 'FlatCritDamageMod':
		
		break;
		case 'rFlatCritDamageModPerLevel':
		
		break;
		case 'PercentCritDamageMod':
		
		break;
		case 'FlatBlockMod':
		
		break;
		case 'PercentBlockMod':
		
		break;
		case 'FlatSpellBlockMod':
		
		break;
		case 'rFlatSpellBlockModPerLevel':
		
		break;
		case 'PercentSpellBlockMod':
		
		break;
		case 'FlatEXPBonus':
		
		break;
		case 'PercentEXPBonus':
		
		break;
		case 'rPercentCooldownMod':
		
		break;
		case 'rPercentCooldownModPerLevel':
		
		break;
		case 'rFlatTimeDeadMod':
		
		break;
		case 'rFlatTimeDeadModPerLevel':
		
		break;
		case 'rPercentTimeDeadMod':
		
		break;
		case 'rPercentTimeDeadModPerLevel':
		
		break;
		case 'rFlatGoldPer10Mod':
		
		break;
		case 'rFlatMagicPenetrationMod':
		
		break;
		case 'rFlatMagicPenetrationModPerLevel':
		
		break;
		case 'rPercentMagicPenetrationMod':
		
		break;
		case 'rPercentMagicPenetrationModPerLevel':
		
		break;
		case 'FlatEnergyRegenMod':
		
		break;
		case 'rFlatEnergyRegenModPerLevel':
		
		break;
		case 'FlatEnergyPoolMod':
		
		break;
		case 'rFlatEnergyModPerLevel':
		
		break;
		case 'PercentLifeStealMod':
		
		break;
		case 'PercentSpellVampMod':
		
		break;
	}
}