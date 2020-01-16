<?php
require('cache.php');
$api = new riotapi(new FileSystemCache('kernel/cache'),$config['riot.api.key.type']);
define('API_KEY',$config['riot.api.key']);
define('CACHE_LIFETIME_SECONDS',$config['riot.api.cachetime.default']);
define('DATABASE_PATH',$config['database.path']);
define('ACTUAL_SEASON',current(explode(',',$config['api.seasons'])));
$servers = array('br' => 'br1', 'eune' => 'eun1', 'euw' => 'euw1', 'kr' => 'kr', 'lan' => 'la1', 'las' => 'la2', 'na' => 'na1', 'oce' => 'oc1', 'tr' => 'tr1', 'ru' => 'ru','jp' => 'jp1'); //Region => Platform ID,  'PBE' => 'PBE1' DISABLED
class riotapi {
	
	
	const API_URL = 'https://{region}.api.pvp.net/api/lol/{region}/v{version}/';
	const API_URL_MASTERY = 'https://{region}.api.pvp.net/championmastery/location/{platform}/player/';
	const API_URL_FEATURED = 'https://{region}.api.pvp.net/observer-mode/rest/featured';
	const API_URL_STATIC = 'https://global.api.pvp.net/api/lol/static-data/{region}/v1.2/';
	const API_URL_SHARDS = 'http://status.leagueoflegends.com/shards';
	const API_URL_CURRENT_GAME = 'https://{region}.api.pvp.net/observer-mode/rest/consumer/getSpectatorGameInfo/';
	const API_LIMIT_LEAGUES = 10; //Max summoners leagues search per query
	const API_LIMIT_SUMMONERS = 40; //Max summoners search per query
	const API_LIMIT_TEAMS = 10; //Max teams search per query
	const API_RECENTGAMES_LIMIT = 10; //Matchs retrieved by this function
	private $FORCE_UPDATE = false; //Force update default value. Recommended is false

	private $LONG_LIMIT_INTERVAL;	
	private $RATE_LIMIT_LONG;	
	private $SHORT_LIMIT_INTERVAL;	
	private $RATE_LIMIT_SHORT;	
	private $cache;


	private static $errorCodes = array(0   => 'NO_RESPONSE',
									   400 => 'BAD_REQUEST',
									   401 => 'UNAUTHORIZED',
									   403 => 'ACCESS_DENIED',
									   404 => 'NOT_FOUND',
									   429 => 'RATE_LIMIT_EXCEEDED',
									   500 => 'SERVER_ERROR',
									   503 => 'UNAVAILABLE');

	public function __construct(CacheInterface $cache = null,$API_KEY_TYPE = 'DEV')
	{
		if($API_KEY_TYPE == 'DEV')
		{
			$this->LONG_LIMIT_INTERVAL = 600;
			$this->RATE_LIMIT_LONG = 500;
			$this->SHORT_LIMIT_INTERVAL = 10;
			$this->RATE_LIMIT_SHORT = 10;
		}
		else
		{
			$this->LONG_LIMIT_INTERVAL = 600;
			$this->RATE_LIMIT_LONG = 180000;
			$this->SHORT_LIMIT_INTERVAL = 10;
			$this->RATE_LIMIT_SHORT = 3000;
		}

		$this->shortLimitQueue = new SplQueue();
		$this->longLimitQueue = new SplQueue();

		$this->cache = $cache;
	}
	/* Internal Function */
	public function forceUpdate($status = true){
		if($status == true)
		{
			$this->FORCE_UPDATE = true;
		}
		else
		{
			$this->FORCE_UPDATE = false;
		}
	}
	/* Internal Function */
	private function updateLimitQueue($queue, $interval, $call_limit){
		
		while(!$queue->isEmpty()){
			$timeSinceOldest = time() - $queue->bottom();
			if($timeSinceOldest > $interval){
					$queue->dequeue();
			}
			elseif($queue->count() >= $call_limit){
				if($timeSinceOldest < $interval){ 
					sleep($interval - $timeSinceOldest);
				}
			}
			else {
				break;
			}
		}
		$queue->enqueue(time());
	}
	
	/* Internal Function */
	private function request($call, $dbPath, $dbFile, $dbTime = CACHE_LIFETIME_SECONDS, $region = null, $otherQueries = false, $static = false) {
		$url = str_replace('{region}', $region, $call) . ($otherQueries ? '&' : '?') . 'api_key=' . API_KEY;
		$result = array();
		if($dbFile == '{MERGE_GAMES}')
		{
			if(!file_exists(DATABASE_PATH.'/'.$dbPath))
			{
				$dbFile = 0;
			}
			else
			{
				$dbFilesMerge = scandir(DATABASE_PATH.'/'.$dbPath,SCANDIR_SORT_ASCENDING);
				$dbFile = (int) str_replace('.json',null,end($dbFilesMerge));
				$dbRecentGames = true;
			}
		}
		if($this->cache !== null){
			if(stristr($url,'%2c')) //Fix for multi query searchs
			{
				$baseUrl = explode('%2c',strtolower($url));
				$singleBaseUrl = null;
						
				$singleEndUrlData = explode('?',array_pop($baseUrl));
				$singleEndUrl = '?'.$singleEndUrlData[1];
				array_push($baseUrl,$singleEndUrlData[0]);
				if(strstr(end($baseUrl),'/'))
				{
					$baseUrl = str_replace('/'.explode('/',end($baseUrl))[1],null,$baseUrl); // Fix for /query urls
				}
				foreach($baseUrl as $strNum => $strData)
				{
					if($strNum == 0)
					{
						$strClear = explode('/',$strData);
						$strSingle = array_pop($strClear);
						$singleBaseUrl = implode('/',$strClear).'/';
					}
					else
					{
						$strSingle = $strData;
					}
					$finalDataSerialized = str_replace(' ', null,strtolower(rawurldecode($strSingle)));
					$dbFileResult = str_replace('{strSingle}',$finalDataSerialized,$dbFile);
					if($this->cache->has($dbPath, $dbFileResult))
					{
						$result[$finalDataSerialized] = $this->cache->get($dbPath, $dbFileResult)[$finalDataSerialized];
					}
					else
					{
						$updateNeeded = true;
					}
				}
			}
			else
			{
				if($this->cache->has($dbPath, $dbFile))
				{
					if(!empty($dbRecentGames))
					{
						$reloadOldFile = json_decode(json_decode(file_get_contents($this->cache->getPath($dbPath, ($dbFile-1))),true)['data'],true);
						$reloadNewFile = json_decode(json_decode(file_get_contents($this->cache->getPath($dbPath, $dbFile)),true)['data'],true);
						sort($reloadNewFile['games'],SORT_NUMERIC );
						$thisMatch = count($reloadNewFile['games']);
						$neededDbResultMatches = self::API_RECENTGAMES_LIMIT - count($reloadNewFile['games']);
						$result = $reloadNewFile;
						foreach($reloadOldFile['games'] as $gameCount => $gameData)
						{
							if($thisMatch <= $neededDbResultMatches)
							{
								$result['games'][$thisMatch] = $reloadOldFile['games'][$gameCount];
							}
							$thisMatch++;
						}
						$resultData = $result;
					}
					else
					{
						$resultData = $this->cache->get($dbPath, $dbFile);
					}
					if(is_array($resultData))
					{
						$result = $resultData;
					}
					else
					{
						$result = json_decode($resultData,true);
					}
				}
				else
				{
					$updateNeeded = true;
				}
			}
		}
		if($this->FORCE_UPDATE == true)
		{
			$updateNeeded = true;
		}
		if(!empty($updateNeeded)) {
			
			if ($static == true) {
				$this->updateLimitQueue($this->longLimitQueue, $this->LONG_LIMIT_INTERVAL, $this->RATE_LIMIT_LONG);
				$this->updateLimitQueue($this->shortLimitQueue, $this->SHORT_LIMIT_INTERVAL, $this->RATE_LIMIT_SHORT);
			}
			
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);	
		
			$result = curl_exec($ch);
			$resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
			curl_close($ch);
			if($resultCode == 429) {
				$time_header = get_headers($url,1);
				($time_header) ? $time_needed = (int) @$time_header['Retry-After']:$time_needed = 'NOT_SET';
				if(is_int($time_needed))
				{
					sleep($time_needed); //Sleep needed time and then reload function
					$this->request($call, $dbPath, $dbFile, $dbTime, $region, $otherQueries, $static);
				}
				else
				{
					throw new Exception(self::$errorCodes[429]);
				}
			}
			if($resultCode == 404) {
				$jsonFile = $this->cache->getPath($dbPath, $dbFile);
				if(file_exists($jsonFile))
				{
					unlink($jsonFile);
				}
			}
			
			if($resultCode == 0) {
				$this->request($call, $dbPath, $dbFile, $dbTime, $region, $otherQueries, $static);
			}
			
			if($resultCode == 200) {
				if($this->cache !== null){
					if(stristr($url,'%2c')) //Fix for multi query searchs
					{
						$baseUrl = explode('%2c',strtolower($url));
						$singleBaseUrl = null;
						
						$singleEndUrlData = explode('?',array_pop($baseUrl));
						$singleEndUrl = '?'.$singleEndUrlData[1];
						array_push($baseUrl,$singleEndUrlData[0]);
						if(strstr(end($baseUrl),'/'))
						{
							$baseUrl = str_replace('/'.explode('/',end($baseUrl))[1],null,$baseUrl); // Fix for /query urls
						}
						$baseData = json_decode($result,true);
						foreach($baseUrl as $strNum => $strData)
						{
							if($strNum == 0)
							{
								$strClear = explode('/',$strData);
								$strSingle = array_pop($strClear);
								$singleBaseUrl = implode('/',$strClear).'/';
							}
							else
							{
								$strSingle = $strData;
							}
							$finalDataSerialized = str_replace(' ', null,strtolower(rawurldecode($strSingle)));
							
							$finalDataResult = array($finalDataSerialized => $baseData[$finalDataSerialized]);
							$dbFileResult = str_replace('{strSingle}',$finalDataSerialized,$dbFile);
							$this->cache->put($dbPath, $dbFileResult, $finalDataResult, $dbTime);
						}
					}
					else
					{
						if(!empty($dbRecentGames)) // RECENT GAMES SPECIAL ALLOCATION. COULD GIVE ERRORS!
						{
							if(file_exists(DATABASE_PATH.'/'.$dbPath.'/'.$dbFile.'.json'))
							{
								$reloadOldFile = json_decode(json_decode(file_get_contents($this->cache->getPath($dbPath, $dbFile)),true)['data'],true);
								$reloadNewFile = json_decode($result,true);
								$reloadOldFileIds = null;
								$reloadNewFileIds = null;
								foreach($reloadOldFile['games'] as $gameData)
								{
									$reloadOldFileIds .= $gameData['gameId'].',';
								}
								foreach($reloadNewFile['games'] as $gameData)
								{
									$reloadNewFileIds .= $gameData['gameId'].',';
								}
								if($reloadOldFileIds == $reloadNewFileIds)
								{
									$this->cache->put($dbPath, $dbFile, json_encode($reloadNewFile), $dbTime);
								}
								else
								{
									if(count($reloadOldFile['games']) >= self::API_RECENTGAMES_LIMIT)
									{
										foreach($reloadOldFile['games'] as $oldGameId)
										{
											foreach($reloadNewFile['games'] as $newGameCount => $newGameId)
											{
												if($oldGameId == $newGameId)
												{
													unset($reloadNewFile['games'][$newGameCount]);
												}
											}
										}
										$this->cache->put($dbPath, ($dbFile+1), json_encode($reloadNewFile), $dbTime);
										$return = $reloadNewFile;
										$neededReturnGames = self::API_RECENTGAMES_LIMIT - count($reloadNewFile['games']);
										$neededReturnStart = count($reloadNewFile['games']);
										for($i = 0; $i < $neededReturnGames; $i++)
										{
											$return['games'][$neededReturnStart] = $reloadOldFile['games'][$i];
											$neededReturnStart++;
										}
									}
									else
									{
										foreach($reloadOldFile['games'] as $oldGameId)
										{
											foreach($reloadNewFile['games'] as $newGameCount => $newGameId)
											{
												if($oldGameId == $newGameId)
												{
													unset($reloadNewFile['games'][$newGameCount]);
												}
											}
										}
										if((count($reloadOldFile['games'])+count($reloadNewFile['games'])) <= self::API_RECENTGAMES_LIMIT) //Se añaden las partidas y al no haber excedente, no se crea un nuevo archivo.
										{
											$startCount = 0;
											foreach($reloadOldFile['games'] as $gameCount => $gameData)
											{
												$reloadOldFile['games'][$startCount] = $reloadOldFile['games'][$gameCount];
												$startCount++;
											}
											foreach($reloadNewFile['games'] as $gameCount => $gameData)
											{
												$reloadOldFile['games'][$startCount] = $reloadNewFile['games'][$gameCount];
												$startCount++;
											}
											$this->cache->put($dbPath, $dbFile, json_encode($reloadOldFile), $dbTime);
											$result = $reloadOldFile;
										}
										else //Se añaden las partidas que quepan, y se meten las excedentes en un nuevo archivo
										{
											$neededDbOldFileMatches = self::API_RECENTGAMES_LIMIT - count($reloadOldFile['games']);
											$newGamesToAdd = count($reloadNewFile);
											$gamesToAddData = $reloadNewFile;
											$gamesAdded = 0;
											foreach($reloadNewFile['games'] as $gameCount => $gameData)
											{
												if($gamesAdded < $neededDbOldFileMatches)
												{
													$reloadOldFile['games'][(self::API_RECENTGAMES_LIMIT-$neededDbOldFileMatches)] = $reloadNewFile['games'][$gameCount];
													unset($reloadNewFile['games'][$gameCount]);
													$gamesAdded++;
												}
											}
											$this->cache->put($dbPath, $dbFile, json_encode($reloadOldFile), $dbTime);
											sort($reloadNewFile['games'],SORT_NUMERIC );
											$this->cache->put($dbPath, ($dbFile+1), json_encode($reloadNewFile), $dbTime);
											$thisMatch = count($reloadNewFile['games']);
											$neededDbResultMatches = self::API_RECENTGAMES_LIMIT - count($reloadNewFile['games']);
											$result = $reloadNewFile;
											foreach($reloadOldFile['games'] as $gameCount => $gameData)
											{
												if($thisMatch <= $neededDbResultMatches)
												{
													$result['games'][$thisMatch] = $reloadOldFile['games'][$gameCount];
												}
												$thisMatch++;
											}
										}
									}
								}
							}
							else
							{
								$this->cache->put($dbPath, $dbFile, $result, $dbTime);
							}
						}
						else
						{
							$this->cache->put($dbPath, $dbFile, $result, $dbTime);
						}
					}
				}
			} else {
				throw new Exception(self::$errorCodes[$resultCode]);
			}
			if(is_array($result))
			{
				$result = $result;
			}
			else
			{
				$result = json_decode($result,true);
			}
		}
		return $result;
	}
	
	/* Champion Data: Null returns all champ data. If ID has been set, it return only champ data */
	public function champion($id = null,$region='euw'){
		if($id == null)
		{
			$dbPath = 'champions';
			$dbFile = 'champions'; 
		}
		else
		{
			$dbPath = 'champions';
			$dbFile = $id;
		}
		$dbTime = 3600;
		$call = 'champion';
		($id != null) ? $call .= '/'.$id:null;
		$call = str_replace('{version}','1.2',self::API_URL) . $call;
		return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
	}
	/* Returns Free To Play champions */
	public function championFreeToPlay($region='euw'){
		$dbPath = 'champions';
		$dbFile = 'freetoplay_'.$region;
		$dbTime = 3600;
		$call = 'champion?freeToPlay=true';
		$call = str_replace('{version}','1.2',self::API_URL) . $call;
		return $this->request($call,$dbPath,$dbFile,$dbTime,$region,true);
	}
	
	/* Returns Champion Mastery for given user ID. */
	public function championMastery($summonerId,$region){
		global $servers;
		$dbPath = 'summoner/mastery';
		$dbFile = $summonerId.'_'.$region; 
		$dbTime = 1800;
		$call = $summonerId.'/champions';
		$call = str_replace('{platform}',$servers[$region],self::API_URL_MASTERY) . $call;
		return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
	}

	/* Returns Current Match info for given user ID. */
	public function currentGame($summonerId,$region){
		global $servers;
		$dbPath = 'summoner/livegame';
		$dbFile = $summonerId.'_'.$region; 
		$dbTime = 150;
		$call = self::API_URL_CURRENT_GAME . strtoupper($servers[$region]) . '/' . $summonerId;
		return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
	}
	
	/* Returns Featured Games for the given server. */
	public function featuredGames($region = 'euw'){
		$dbPath = 'featured';
		$dbFile = $region; 
		$dbTime = 300;
		$call = self::API_URL_FEATURED;
		return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
	}
	
	/* Return Recent Games for given user ID */
	public function recentGames($summonerId,$region){  // REVISION
		$dbPath = 'summoner/games/'.$summonerId.'_'.$region;
		$dbFile = '{MERGE_GAMES}'; 
		$dbTime = 900;
		$call = 'game/by-summoner/' . $summonerId . '/recent';
		$call = str_replace('{version}','1.3',self::API_URL). $call;
		return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
	}
	
	/* Returns League for given user ID, entry parameter shows only the summoner given if set . You can set multiple summoners. */
	public function league($summonerId, $region, $entry=null){
		($entry != null) ? $entry = '/entry':$entry=null;
		$dbPath = 'summoner/league';
		$dbTime = 1800;
		$leagueVersion = '2.5';
		$call = 'league/by-summoner/';
		if (is_array($summonerId) && count($summonerId) > 1) {
			$dbFile = '{strSingle}'.'_'.$region.str_replace('/','_',$entry);
			if(count($summonerId) > self::API_LIMIT_LEAGUES)
			{
				$summonerIds = array_chunk($summonerId,self::API_LIMIT_LEAGUES,true);
				$return = null;
				foreach($summonerIds as $summonersChunked)
				{
					$call .= rawurlencode(implode(",", $summonersChunked));
					$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call . $entry;
					$return .= $this->request($call,$dbPath,$dbFile,$dbTime,$region);
				}
				return $return;
			}
			else
			{
				$call .= rawurlencode(implode(",", $summonerId));
				$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call . $entry;
				return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
			}
		}
		else {
			(is_array($summonerId)) ? $summonerId = implode(null,$summonerId):null;
			$dbFile = $summonerId.'_'.$region . str_replace('/','_',$entry); 
			$call .= $summonerId;
			$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call . $entry;
			return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
		}
	}
	
	/* Returns League for given team ID, entry parameter (if $entry is null it returns all league summoners). You can set multiple teams. */ 
	public function teamLeague($teamId, $region, $entry=null){
		($entry != null) ? $entry = '/entry':$entry=null;
		$dbPath = 'team/league';
		$dbFile = $teamId.'_'.$region . str_replace('/','_',$entry); 
		$dbTime = 1800;
		$leagueVersion = '2.5';
		$call = 'league/by-team/';
		if (is_array($teamId) && count($teamId) > 1) {
			$dbFile = '{strSingle}'.'_'.$region.str_replace('/','_',$entry);
			if(count($teamId) > self::API_LIMIT_LEAGUES)
			{
				$summonerIds = array_chunk($teamId,self::API_LIMIT_LEAGUES,true);
				$return = null;
				foreach($summonerIds as $summonersChunked)
				{
					$call .= rawurlencode(implode(",", $summonersChunked));
					$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call . $entry;
					$return .= $this->request($call,$dbPath,$dbFile,$dbTime,$region);
				}
				return $return . $entry;
			}
			else
			{
				$call .= rawurlencode(implode(",", $teamId));
				$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call . $entry;
				return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
			}
		}
		else {
			(is_array($teamId)) ? $teamId = implode(null,$teamId):null;
			$dbFile = $teamId.'_'.$region . str_replace('/','_',$entry); 
			$call .= $teamId;
			$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call . $entry;
			return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
		}
	}
	
	/* Returns challenger lader, valid queues are: RANKED_SOLO_5x5, RANKED_TEAM_5x5, RANKED_TEAM_3x3 */
	public function challengerLeague($region,$queue = 'RANKED_SOLO_5x5') {
		$dbPath = 'league';
		$dbFile = 'challenger_'.$region.'_'.$queue; 
		$dbTime = 3600;
		$call = 'league/challenger?type='.$queue;
		$call = str_replace('{version}','2.5',self::API_URL) . $call;
		return $this->request($call,$dbPath,$dbFile,$dbTime,$region, true);
	}
	
	/* Returns master lader, valid queues are: RANKED_SOLO_5x5, RANKED_TEAM_5x5, RANKED_TEAM_3x3 */
	public function masterLeague($region,$queue = 'RANKED_SOLO_5x5') {
		$dbPath = 'league';
		$dbFile = 'master_'.$region.'_'.$queue; 
		$dbTime = 3600;
		$call = 'league/master?type='.$queue;
		$call = str_replace('{version}','2.5',self::API_URL) . $call;
		return $this->request($call,$dbPath,$dbFile,$dbTime,$region, true);
	}
	
	/* Static data loader. It's not counted on rate limit. $id is an optional value, just set if needed. Given Static data options (for $call) are [$call -> explain]:
	champion -> Full game champions data
	champion/$id -> Full data for given champion
	item -> Full game items data
	item/$id -> Full data for given item
	language-strings ->  Language names
	languages -> LoL Languages
	map -> Maps
	mastery -> Full game masteries data
	mastery/$id -> Full data for given mastery
	realm -> LoL Official assets links
	rune -> Full game runes data
	rune/$id -> Full data for given rune
	summoner-spell -> Full game summoner spells data
	summoner-spell/$id -> Full data for given summoner spell
	versions -> Full LoL Api versions */
	public function staticData($call, $fulldata = false, $locale = 'en_US', $version = null, $region='euw', $id=null) {
		$dbPath = 'static/'.$call;
		$dbFile = ($id != null ? $id.'_' : null).$locale.'_'.($version != null ? $version.'_' : null).$region.'fulldata_'.$fulldata;
		$dbTime = 86400;
		$basecall = $call;
		$call = self::API_URL_STATIC . $call . ($id != null ? '/'.$id : null);
		$call .= '?locale='.$locale;
		($version != null) ? $call .= '&version='.$version:null;
		if($fulldata == true)
		{
			switch($basecall)
			{
				case 'champion':
				$call .= '&champData=all';
				break;
				case 'item':
				$call .= '&itemData=all';
				break;
				case 'mastery':
				$call .= '&masteryData=all';
				break;
				case 'rune':
				$call .= '&runeData=all';
				break;
				case 'summoner-spell':
				$call .= '&spellData=all';
				break;
			}
		}
		return $this->request($call,$dbPath,$dbFile,$dbTime, $region, (strpos($call,'?') !== false), true);
	}
	
	/* League of legends game status. You can set a region for retrieve only shards for it. Regions avaliable -> https://developer.riotgames.com/docs/regional-endpoints */
	public function shards($region=null) {
		($region != null) ? $region='/'.$region:null;
		$dbPath = 'shards';
		$dbFile = $region; 
		$dbTime = 3600;
		$call = self::API_URL_SHARDS . $region;
		return $this->request($call,$dbPath,$dbFile,$dbTime);
	}

	/* Returns details for given match id. TimeLine can be requested. If timeline data is requested, but doesn't exist, then the response won't include it. */
	public function match($matchId, $region, $timeLine=false) {
		$dbPath = 'match';
		$dbFile = $matchId.'_'.$region.($timeLine ? '_timeline' : null); 
		$dbTime = 86400;
		$call = str_replace('{version}','2.2',self::API_URL)  . 'match/' . $matchId . ($timeLine ? '?includeTimeline=true' : '');
		return $this->request($call,$dbPath,$dbFile,$dbTime,$region,$timeLine);
	}

	/* Returns all ranked games played (since S3) given summoner id. */
	public function matchHistory($summonerId,$region) {
		$dbPath = 'summoner/matchlist';
		$dbFile = $summonerId.'_'.$region; 
		$dbTime = 10800;
		$call = str_replace('{version}','2.2',self::API_URL) . 'matchlist/by-summoner/' . $summonerId;
		return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
	}
	
	/* Returns a summoner's stats given summoner id. $option can be summary/ranked. */
	public function stats($summonerId,$region,$option='summary',$season=ACTUAL_SEASON){
		$dbPath = 'summoner/stats/'.$option;
		$dbFile = $summonerId.'_'.$region.'_'.strtoupper($season); 
		$dbTime = 3600;
		$call = 'stats/by-summoner/' . $summonerId . '/' . $option.'?season='.strtoupper($season);
		$call = str_replace('{version}','1.3',self::API_URL) . $call;
		return $this->request($call,$dbPath,$dbFile,$dbTime,$region,true);
	}


	/* Returns summoner info giving name */
	public function summonerByName($summonerName,$region){
		$call = 'summoner/by-name/';
		$dbPath = 'summoner/name';
		$dbTime = 900;
		$leagueVersion = '1.4';
		if (is_array($summonerName) && count($summonerName) > 1) {
			$dbFile = '{strSingle}'.'_'.$region;
			if(count($summonerName) > self::API_LIMIT_SUMMONERS)
			{
				$summonerNames = array_chunk($summonerName,self::API_LIMIT_SUMMONERS,true);
				$return = null;
				foreach($summonerNames as $summonersChunked)
				{
					$dbFile = base64_encode($summonerName).'_'.$region; 
					$call .= strtolower(rawurlencode(implode(",",  $summonersChunked)));
					$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call;
					$return .= $this->request($call,$dbPath,$dbFile,$dbTime,$region);
				}
				return $return;
			}
			else
			{
				$dbFile = base64_encode($summonerName).'_'.$region; 
				$call .= strtolower(rawurlencode(implode(",", $summonerName)));
				$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call;
				return $this->request($call,$region);
			}
		}
		else {
			(is_array($summonerName)) ? $summonerName = implode(null,$summonerName):null;
			$dbFile = base64_encode($summonerName).'_'.$region; 
			$call .= strtolower(rawurlencode($summonerName));
			$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call;
			return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
		}
	}
	
	/* Returns summoner info given summoner id. You can set multiple summoners. $option can be: masteries,runes,name. */
	public function summonerById($summonerId,$region,$option=null){
		$dbPath = 'summoner/id';
		$dbTime = 900;
		$call = 'summoner/';
		$leagueVersion = '1.4';
		switch ($option) {
			case 'masteries':
				$option = '/masteries';
				break;
			case 'runes':
				$option = '/runes';
				break;
			case 'name':
				$option = '/name';
				break;
			default:
			$option = null;
				break;
		}
		if (is_array($summonerId) && count($summonerId) > 1) {
			$dbFile = '{strSingle}'.'_'.$region.str_replace('/','_',$option);
			if(count($summonerId) > self::API_LIMIT_SUMMONERS)
			{
				$summonerIds = array_chunk($summonerId,self::API_LIMIT_SUMMONERS,true);
				$return = null;
				foreach($summonerIds as $summonersChunked)
				{
					$call .= rawurlencode(implode(",", $summonersChunked));
					$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call . $option;
					$return .= $this->request($call,$dbPath,$dbFile,$dbTime,$region);
				}
				return $return;
			}
			else
			{
				$call .= rawurlencode(implode(",", $summonerId));
				$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call . $option;
				return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
			}
		}
		else {
			(is_array($summonerId)) ? $summonerId = implode(null,$summonerId):null;
			$dbFile = $summonerId.'_'.$region.($option != null ? '_'.$option : null); 
			$call .= $summonerId;
			$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call . $option;
			return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
		}
	}

	/* Gets the teams of a summoner, given summoner id. It can be multiple ids. */
	public function teamsBySummoner($summonerId,$region){
		$dbPath = 'summoner/team';
		$dbTime = 900;
		$call = 'team/by-summoner/';
		$leagueVersion = '2.4';
		if (is_array($summonerId) && count($summonerId) > 1) {
			$dbFile = '{strSingle}'.'_'.$region;
			if(count($summonerId) > self::API_LIMIT_TEAMS)
			{
				$summonerIds = array_chunk($summonerId,self::API_LIMIT_TEAMS,true);
				$return = null;
				foreach($summonerIds as $summonersChunked)
				{
					$call .= rawurlencode(implode(",", $summonersChunked));
					$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call;
					$return .= $this->request($call,$dbPath,$dbFile,$dbTime,$region);
				}
				return $return;
			}
			else
			{
				$call .= rawurlencode(implode(",", $summonerId));
				$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call;
				return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
			}
		}
		else {
			(is_array($summonerId)) ? $summonerId = implode(null,$summonerId):null;
			$dbFile = $summonerId.'_'.$region.($option != null ? '_'.$option : null); 
			$call .= $summonerId;
			$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call;
			return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
		}
	}
	
	/* Gets the teams of a summoner, given team id. It can be multiple ids. */
	public function teamsData($teamId,$region){
		$dbPath = 'team';
		$dbTime = 900;
		$call = 'team/';
		$leagueVersion = '2.4';
		if (is_array($summonerId) && count($summonerId) > 1) {
			$dbFile = '{strSingle}'.'_'.$region;
			if(count($teamId) > self::API_LIMIT_TEAMS)
			{
				$teamIds = array_chunk($teamId,self::API_LIMIT_TEAMS,true);
				$return = null;
				foreach($teamIds as $teamsChunked)
				{
					$call .= rawurlencode(implode(",", $teamsChunked));
					$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call;
					$return .= $this->request($call,$dbPath,$dbFile,$dbTime,$region);
				}
				return $return;
			}
			else
			{
				$call .= rawurlencode(implode(",", $teamId));
				$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call;
				return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
			}
		}
		else {
			$call .= $teamId;
			$call = str_replace('{version}',$leagueVersion,self::API_URL) . $call;
			return $this->request($call,$dbPath,$dbFile,$dbTime,$region);
		}
	}
}
