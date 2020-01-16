<?php
/* For reloading data from Riot's api */
class riot{
	public static $url_league_by_summoner = 'https://{{region}}.api.pvp.net/api/lol/{{region}}/v2.5/league/by-summoner/{{summoners_query}}?api_key='.LOL_API_KEY;
	public static $url_summonerinfo_by_name = 'https://{{region}}.api.pvp.net/api/lol/{{region}}/v1.4/summoner/by-name/{{summoners_search}}?api_key='.LOL_API_KEY;
	public static $url_summonerinfo_by_id = 'https://{{region}}.api.pvp.net/api/lol/{{region}}/v1.4/summoner/{{summoners_query}}?api_key='.LOL_API_KEY;
	public static $url_recentgames_by_summonerid = 'https://{{region}}.api.pvp.net/api/lol/{{region}}/v1.3/game/by-summoner/{{summoner_id}}/recent?api_key='.LOL_API_KEY;
	public static $url_champmastery_by_summonerid = 'https://{{region}}.api.pvp.net/championmastery/location/{{region_key}}/player/{{summoner_id}}/champions?api_key='.LOL_API_KEY;
	public static $url_rankedmatchlist_by_summonerid = 'https://{{region}}.api.pvp.net/api/lol/{{region}}/v2.2/matchlist/by-summoner/{{summoner_id}}?api_key='.LOL_API_KEY;
	public static $url_runes_by_summonerid = 'https://{{region}}.api.pvp.net/api/lol/{{region}}/v1.4/summoner/{{summoner_id}}/runes?api_key='.LOL_API_KEY;
	public static $url_actualgame_by_summonerid = 'https://{{region}}.api.pvp.net/observer-mode/rest/consumer/getSpectatorGameInfo/{{region_code}}/{{summoner_id}}?api_key='.LOL_API_KEY;
}
	class check extends riot{
		public static function ingame($summoner_id,$region)
		{
			global $lol_servers;
			global $db;
			if($db->query('SELECT id FROM lol_summoner_ingame WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->num_rows == 0)
			{
			$db->query('INSERT INTO lol_summoner_ingame (ingame,summoner_id,region,timestamp) VALUES ("0",'.$summoner_id.',"'.$region.'","0")');	
			}
			if($db->query('SELECT timestamp FROM lol_summoner_ingame WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->fetch_row()[0] < (time()-onlol::timing('profile_ingame_interval')))
			{
				if(onlol::readjson(str_replace(array('{{region}}','{{region_code}}','{{summoner_id}}'),array($region,strtoupper($lol_servers[$region]),$summoner_id),self::$url_actualgame_by_summonerid)) == 'NOT_FOUND')
				{
					$db->query('UPDATE lol_summoner_ingame SET ingame="0",timestamp='.time().' WHERE region="'.$region.'" AND summoner_id='.$summoner_id);
					return false;
				}
				else
				{
					$db->query('UPDATE lol_summoner_ingame SET ingame="1",timestamp='.time().' WHERE region="'.$region.'" AND summoner_id='.$summoner_id);
					return true;
				}
			}
		}
	}
	class reload extends riot{
		public static function leagues($summoner_id,$region)
		{
			global $db;
			/* Leagues */
			$reload_summoner_level = $db->query('SELECT level FROM lol_summoners WHERE summoner_id='.$summoner_id)->fetch_row()[0];
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

				$league_data = onlol::readjson(str_replace(array('{{region}}','{{summoners_query}}'),array($region,$summoner_id.$api_query_summoners_leagues),self::$url_league_by_summoner));
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
		}
		public static function basicdata($summoner_name,$region)
		{
			global $db;
			if($db->query('SELECT id FROM lol_summoners WHERE region="'.$region.'" AND name="'.$summoner_name.'"')->num_rows == 0) //IMPORT profile on DB
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
				$summoner_info = onlol::readjson(str_replace(array('{{region}}','{{summoners_search}}'),array($region,onlol::api_format_name($summoner_name).onlol::api_format_name($api_query_summoners_basic)),self::$url_summonerinfo_by_name));
				if(is_array($summoner_info))
				{
					/* Optimized api summoners */
					foreach($summoner_info as $keyname => $data)
					{
						$db->query('UPDATE lol_summoners SET name="'.$data['name'].'",icon="'.$data['profileIconId'].'",level="'.$data['summonerLevel'].'",last_update='.time().' WHERE summoner_id='.$data['id'].' AND region="'.$region.'"');
					}
					/* Summoner info */
					if(!array_key_exists(onlol::api_format_name($summoner_name),$summoner_info))
					{
						onlol::redirect(URL.'/home?error=summoner_not_found&&summoner='.$summoner_name.'&&region='.$region);
					}
				}
				else
				{
					onlol::redirect(URL.'/home?error=summoner_not_found&&summoner='.$summoner_name.'&&region='.$region);
				}
				$db->query('UPDATE lol_summmoners SET revision_date="'.$summoner_info[onlol::api_format_name($summoner_name)]['revisionDate'].'" WHERE summoner_id="'.$summoner_info[onlol::api_format_name($summoner_name)]['id'].'" AND region="'.$region.'"');
				$db->query('INSERT INTO lol_summoners (summoner_id,name,icon,level,revision_date,last_update,region) VALUES ('.$summoner_info[onlol::api_format_name($summoner_name)]['id'].',"'.$summoner_info[onlol::api_format_name($summoner_name)]['name'].'","'.$summoner_info[onlol::api_format_name($summoner_name)]['profileIconId'].'","'.$summoner_info[onlol::api_format_name($summoner_name)]['summonerLevel'].'","'.onlol::microtime_to_unix($summoner_info[onlol::api_format_name($summoner_name)]['revisionDate']).'","'.time().'","'.$region.'")');
			}
			else
			{
				/* Basic data */
				if($db->query('SELECT id FROM lol_summoners WHERE region="'.$region.'" AND last_update<'.(time()-onlol::timing('profile_reload')).' ORDER BY last_update DESC LIMIT 39')->num_rows > 0)
				{
					$api_query_array = array();
					$api_query_query = $db->query('SELECT name FROM lol_summoners WHERE region="'.$region.'" AND last_update<'.(time()-onlol::timing('profile_reload')).' AND name!="'.$summoner_name.'" ORDER BY last_update DESC LIMIT 39');
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
				$summoner_info = onlol::readjson(str_replace(array('{{region}}','{{summoners_search}}'),array($region,onlol::api_format_name($summoner_name).onlol::api_format_name($api_query_summoners_basic)),self::$url_summonerinfo_by_name));
				
				/* Optimized api summoners */
				if(is_array($summoner_info))
				{
					foreach($summoner_info as $keyname => $data)
					{
						if($summoner_name == $data['name'])
						{
							$db->query('UPDATE lol_summoners SET revision_date="'.onlol::microtime_to_unix($data['revisionDate']).'" WHERE summoner_id='.$data['id'].' AND region="'.$region.'"');
						}
							$db->query('UPDATE lol_summoners SET name="'.$data['name'].'",icon="'.$data['profileIconId'].'",level="'.$data['summonerLevel'].'",last_update='.time().' WHERE summoner_id='.$data['id'].' AND region="'.$region.'"');
					}
					$reload_summoner_level = $summoner_info[onlol::api_format_name($summoner_name)]['summonerLevel'];
					$summoner_id = $summoner_info[onlol::api_format_name($summoner_name)]['id'];
				}
				else
				{
					$reload_summoner_level = 0;
					$load_notification_error_reload_apibusy = true;
				}
			}
		}
		public static function recent_matches($summoner_id,$region)
		{
			global $db;
			/* Recent games */
			$recent_games = onlol::readjson(str_replace(array('{{region}}','{{summoner_id}}'),array($region,$summoner_id),self::$url_recentgames_by_summonerid));
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
							if(@$data['stats']['win'] == true)
							{
								$winner_team_id = $data['teamId'];
							}
							else
							{
								if($data['stats']['team'] == 100)
								{
									$winner_team_id = 200;
								}
								else
								{
									$winner_team_id = 100;
								}
							}
							$participant_data = addslashes(json_encode($all_participants));
							$db->query('INSERT INTO lol_matches (match_id,region,summoner_ids,loss_prevented,creation_timestamp,queue,map,participants,advanced_info,winner,lol_patch_played) VALUES ('.$match_id.',"'.$region.'","'.$summoner_ids.'","'.$loss_prevented.'","'.onlol::microtime_to_unix($game_start).'","'.$queue.'",'.$map.',"'.$participant_data.'","false","'.$winner_team_id.'","'.onlol::config('lol_patch').'")') or die($db->error);
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
			/* Recently played with */
			$recent_games_playedwith = $db->query('SELECT summoner_ids FROM lol_matches WHERE summoner_ids LIKE "%'.$summoner_id.'%" AND creation_timestamp > '.(time()-onlol::timing('played_with_interval')));
			$all_summoners_playedwith = null;
			while($row = $recent_games_playedwith->fetch_row())
			{
				$all_summoners_playedwith .= $row[0].';';
			}
			$all_summoners_playedwith = explode(';',$all_summoners_playedwith);
			$all_summoners_playedwith_summoners = array_count_values($all_summoners_playedwith);
			unset($all_summoners_playedwith_summoners[$summoner_id]);
			foreach($all_summoners_playedwith_summoners as $summonerplayewith_id => $count)
			{
				if($count < 2)
				{
					unset($all_summoners_playedwith_summoners[$summonerplayewith_id]);
				}
				else
				{
					$all_summoners_playedwith_summoners_totalgames = $all_summoners_playedwith_summoners[$summonerplayewith_id];
					$all_summoners_playedwith_summoners[$summonerplayewith_id] = array();
					$all_summoners_playedwith_summoners_games = $db->query('SELECT participants,winner FROM lol_matches WHERE summoner_ids LIKE "%'.$summoner_id.'%" AND summoner_ids LIKE "%'.$summonerplayewith_id.'%"');
					$all_summoners_playedwith_summoners_games_won = 0;
					while($row = $all_summoners_playedwith_summoners_games->fetch_row())
					{
						$all_summoners_playedwith_summoners_participants = json_decode($row[0],true);
						if($all_summoners_playedwith_summoners_participants[$summoner_id]['team_id'] == $all_summoners_playedwith_summoners_participants[$summonerplayewith_id]['team_id'])
						{
							if($row[1] == $all_summoners_playedwith_summoners_participants[$summoner_id]['team_id'])
							{
								$all_summoners_playedwith_summoners_games_won++;
							}
						}
						else
						{
							$count = $count-1;
						}
					}
					$all_summoners_playedwith_summoners_gamestatus2 = $all_summoners_playedwith_summoners_games_won+($count-$all_summoners_playedwith_summoners_games_won);
					if($all_summoners_playedwith_summoners_gamestatus2 > 0 && $count > 1)
					{
						if($all_summoners_playedwith_summoners_games_won > 0)
						{
							$all_summoners_playedwith_summoners_games_won_fix = $all_summoners_playedwith_summoners_games_won;
						}
						else
						{
							$all_summoners_playedwith_summoners_games_won_fix = 1;
						}
						$all_summoners_playedwith_summoners[$summonerplayewith_id]['winrate'] = number_format((($all_summoners_playedwith_summoners_games_won_fix/($all_summoners_playedwith_summoners_gamestatus2))*100),0);
						$all_summoners_playedwith_summoners[$summonerplayewith_id]['games'] = $count;
					}
					else
					{
						unset($all_summoners_playedwith_summoners[$summonerplayewith_id]);
					}
				}
			}
			function order_recent_played_summoners ($a, $b) {
				/* ORDER DESC */
				return $b['games'] - $a['games'];
			}
			uasort($all_summoners_playedwith_summoners, 'order_recent_played_summoners');
			if($db->query('SELECT id FROM lol_summoners_recentlyplayed WHERE summoner_id='.$summoner_id)->num_rows == 0)
			{
				$db->query('INSERT INTO lol_summoners_recentlyplayed (summoner_id,data,timestamp) VALUES ('.$summoner_id.',"'.addslashes(json_encode($all_summoners_playedwith_summoners)).'",'.time().' )');
			}
			else
			{
				$db->query('UPDATE lol_summoners_recentlyplayed SET data="'.addslashes(json_encode($all_summoners_playedwith_summoners)).'",timestamp='.time().' WHERE summoner_id='.$summoner_id);
			}
			/* Add played with summoners data to db  */
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
					$summoner_info = onlol::readjson(str_replace(array('{{region}}','{{summoners_query}}'),array($region,onlol::api_format_name($summoners_to_add)),self::$url_summonerinfo_by_id));
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
		public static function champ_mastery($summoner_id,$region)
		{
			global $db;
			global $lol_servers;
			$champ_mastery = onlol::readjson(str_replace(array('{{region}}','{{summoner_id}}','{{region_key}}'),array($region,$summoner_id,$lol_servers[$region]),self::$url_champmastery_by_summonerid));
			$champ_data = array();
			if(is_array($champ_mastery))
			{
				$champ_data_total_points = 0;
				$champ_data_total_levels = 0;
				foreach($champ_mastery as $mastery_data)
				{
					$champ_data[$mastery_data['championId']] = array();
					$champ_data[$mastery_data['championId']]['points'] = $mastery_data['championPoints'];
					$champ_data[$mastery_data['championId']]['level'] = $mastery_data['championLevel'];
					$champ_data[$mastery_data['championId']]['last_time_played'] = $mastery_data['lastPlayTime'];
					$champ_data[$mastery_data['championId']]['points_plus_lastlevel'] = $mastery_data['championPointsSinceLastLevel'];
					$champ_data[$mastery_data['championId']]['points_need_nextlevel'] = $mastery_data['championPointsUntilNextLevel'];
					$champ_data_total_points = $champ_data_total_points+$mastery_data['championPoints'];
					$champ_data_total_levels = $champ_data_total_levels+$mastery_data['championLevel'];
				}
				function order_champ_mastery ($a, $b) {
					/* ORDER DESC */
					return $b['points'] - $a['points'];
				}
				uasort($champ_data, 'order_champ_mastery');
				$main_champ = array_keys($champ_data)[0];
			}
			else
			{
				$main_champ = 0;
			}
			if($db->query('SELECT id FROM lol_summoners_champmastery WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->num_rows == 0)
			{
				$db->query('INSERT INTO lol_summoners_champmastery (summoner_id,region,data,main_champ_id,timestamp,total_points,total_levels) VALUES ('.$summoner_id.',"'.$region.'","'.addslashes(json_encode($champ_data)).'",'.$main_champ.','.time().',"'.$champ_data_total_points.'","'.$champ_data_total_levels.'")');
			}
			else
			{
				$db->query('UPDATE lol_summoners_champmastery SET data="'.addslashes(json_encode($champ_data)).'",main_champ_id='.$main_champ.',timestamp='.time().',total_points="'.$champ_data_total_points.'",total_levels="'.$champ_data_total_levels.'" WHERE summoner_id='.$summoner_id.' AND region="'.$region.'"');
			}
		}
		public static function matchlist($summoner_id,$region)
		{
			global $db;
			$match_list = onlol::readjson(str_replace(array('{{region}}','{{summoner_id}}'),array($region,$summoner_id),self::$url_rankedmatchlist_by_summonerid));
			if($db->query('SELECT id FROM lol_summoner_stats_matchlist WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->num_rows == 0)
			{
				$db->query('INSERT INTO lol_summoner_stats_matchlist (summoner_id,region,total_games,timestamp) VALUES ('.$summoner_id.',"'.$region.'",0,0)');
			}
			$user_imported_games = $db->query('SELECT total_games FROM lol_summoner_stats_matchlist WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->fetch_row()[0];
			if($match_list['totalGames'] > $user_imported_games)
			{
				$match_data = array();
				$match_data['times_played'] = array();
				$match_data['champ_times'] = array();
				$match_data['times_played']['TOP'] = 0;
				$match_data['times_played']['JUN'] = 0;
				$match_data['times_played']['SUP'] = 0;
				$match_data['times_played']['ADC'] = 0;
				$match_data['times_played']['MID'] = 0;
				$match_data['games'] = array();
				$match_data['times_played']['SEASONS'] = array();
				$match_data['champ_times']['SEASONS'] = array();
				foreach($match_list['matches'] as $game_count => $game_data)
				{
					switch(@$game_data['lane'])
					{
						case 'JUNGLE':
						$position = 'JUN';
						break;
						case 'TOP':
						$position = 'TOP';
						break;
						case 'MIDDLE':
						$position = 'MID';
						break;
						case 'MID':
						$position = 'MID';
						break;
						case 'BOT':
						switch($game_data['role'])
						{
							case 'DUO':
							$position = 'SUP';
							break;
							case 'NONE':
							$position = 'SUP';
							break;
							case 'DUO_CARRY':
							$position = 'ADC';
							break;
							case 'DUO_SUPPORT':
							$position = 'SUP';
							break;
							default: $position = 'SUP';
						}
						break;
						case 'BOTTOM':
						switch($game_data['role'])
						{
							case 'DUO':
							$position = 'SUP';
							break;
							case 'NONE':
							$position = 'SUP';
							break;
							case 'DUO_CARRY':
							$position = 'ADC';
							break;
							case 'DUO_SUPPORT':
							$position = 'SUP';
							break;
							default: $position = 'SUP';
						}
						break;
						default: $position = 'TOP';
					}
					$match_data['times_played'][$position] = ($match_data['times_played'][$position]+1);
					if(!array_key_exists($game_data['season'],$match_data['games']))
					{
						$match_data['games'][$game_data['season']] = array();
					}
					if(!array_key_exists($game_data['season'],$match_data['times_played']['SEASONS']))
					{
						$match_data['times_played']['SEASONS'][$game_data['season']] = array();
						$match_data['times_played']['SEASONS'][$game_data['season']]['TOP'] = 0;
						$match_data['times_played']['SEASONS'][$game_data['season']]['JUN'] = 0;
						$match_data['times_played']['SEASONS'][$game_data['season']]['SUP'] = 0;
						$match_data['times_played']['SEASONS'][$game_data['season']]['ADC'] = 0;
						$match_data['times_played']['SEASONS'][$game_data['season']]['MID'] = 0;
					}
					if(!array_key_exists($game_data['champion'],$match_data['champ_times']))
					{
						$match_data['champ_times'][$game_data['champion']] = 0;
					}
					if(!array_key_exists($game_data['season'],$match_data['champ_times']['SEASONS']))
					{
						$match_data['champ_times']['SEASONS'][$game_data['season']] = array();
					}
					if(!array_key_exists($game_data['champion'],$match_data['champ_times']['SEASONS'][$game_data['season']]))
					{
						$match_data['champ_times']['SEASONS'][$game_data['season']][$game_data['champion']] = 0;
					}
					$match_data['champ_times'][$game_data['champion']] = ($match_data['champ_times'][$game_data['champion']]+1);
					$match_data['games'][$game_data['season']][$game_count] = array();
					$match_data['games'][$game_data['season']][$game_count]['position'] = $position;
					$match_data['times_played']['SEASONS'][$game_data['season']][$position] = ($match_data['times_played']['SEASONS'][$game_data['season']][$position]+1);
					$match_data['champ_times']['SEASONS'][$game_data['season']][$game_data['champion']] = ($match_data['champ_times']['SEASONS'][$game_data['season']][$game_data['champion']]+1);
					$match_data['games'][$game_data['season']][$game_count]['timestamp'] = onlol::microtime_to_unix($game_data['timestamp']);
					$match_data['games'][$game_data['season']][$game_count]['champ_id'] = $game_data['champion'];
					$match_data['games'][$game_data['season']][$game_count]['match_id'] = $game_data['matchId'];
					$match_data['games'][$game_data['season']][$game_count]['queue'] = $game_data['queue'];
					
				}
				/* Main position */
				$current_gamescount_main_position = 0;
				$match_data_main_position = null;
				foreach($match_data['times_played'] as $position => $count)
				{
					if(is_int($count))
					{
						if($count > $current_gamescount_main_position)
						{
							$match_data_main_position = $position;
						}
					}
				}
				/* Main position per season */
				foreach($match_data['times_played']['SEASONS'] as $season => $season_array)
				{
					arsort($match_data['times_played']['SEASONS'][$season]);
					reset($match_data['times_played']['SEASONS'][$season]);
					$match_data['times_played']['SEASONS'][$season]['MOST_PLAYED'] = key($match_data['times_played']['SEASONS'][$season]);
				}
				arsort($match_data['times_played']);
				/* Main champ */
				$match_data_mostplayed_champid = 0;
				$match_data_mostplayed_champ_times = 0;
				foreach($match_data['champ_times'] as $champid => $count)
				{
					if(is_int($champid))
					{
						if($count > $match_data_mostplayed_champ_times)
						{
							$match_data_mostplayed_champid = $champid;
							$match_data_mostplayed_champ_times = $count;
						}
					}
				}
				/* Main champ per season */
				foreach($match_data['champ_times']['SEASONS'] as $season => $season_array)
				{
					arsort($match_data['champ_times']['SEASONS'][$season]);
					reset($match_data['champ_times']['SEASONS'][$season]);
					$match_data['champ_times']['SEASONS'][$season]['MOST_PLAYED'] = key($match_data['champ_times']['SEASONS'][$season]);
				}
				arsort($match_data['champ_times']);
				$db->query('UPDATE lol_summoner_stats_matchlist SET total_games='.$match_list['totalGames'].',timestamp='.time().',main_position="'.$match_data_main_position.'",mostplayed_champ_id="'.$match_data_mostplayed_champid.'",data="'.addslashes(json_encode($match_data)).'",mostplayed_champ_times="'.$match_data_mostplayed_champ_times.'" WHERE region="'.$region.'" AND summoner_id='.$summoner_id) or die($db->error);
			}
		}
		public static function runes($summoner_id,$region)
		{
			global $db;
			$rune_data = onlol::readjson(str_replace(array('{{region}}','{{summoner_id}}'),array($region,$summoner_id),self::$url_runes_by_summonerid))[$summoner_id];
			
			if($db->query('SELECT id FROM lol_summoner_runes WHERE region="'.$region.'" AND summoner_id='.$summoner_id)->num_rows == 0)
			{
				$db->query('INSERT INTO lol_summoner_runes (region,summoner_id,timestamp) VALUES ("'.$region.'","'.$summoner_id.'",'.time().')');
			}
			else
			{
				$db->query('UPDATE lol_summoner_runes SET data="" AND timestamp='.time().' WHERE region="'.$region.'" AND summoner_id='.$summoner_id);
			}
			$rune_json_encode = array();
			foreach($rune_data['pages'] as $runepage_id => $runepage_data)
			{
				if($runepage_data['current'] == true)
				{
					$rune_json_encode['current'] = $runepage_id;
				}
				$rune_json_encode['pages'][$runepage_id]['name'] = $runepage_data['name'];
				$rune_json_encode['pages'][$runepage_id]['slots'] = array();
				foreach($runepage_data['slots'] as $count => $runepage_slot_data)
				{
					$rune_json_encode['pages'][$runepage_id]['slots'][$runepage_slot_data['runeSlotId']] = $runepage_slot_data['runeId'];
				}
			}
			$db->query('UPDATE lol_summoner_runes SET data="'.addslashes(json_encode($rune_json_encode)).'" WHERE region="'.$region.'" AND summoner_id='.$summoner_id) or die($db->error);
		}
	}