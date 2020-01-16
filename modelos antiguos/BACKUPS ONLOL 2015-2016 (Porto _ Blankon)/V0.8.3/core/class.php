<?php
$lol_servers = array('br' => 'br1', 'eune' => 'eun1', 'euw' => 'euw1', 'kr' => 'kr', 'lan' => 'la1', 'las' => 'la2', 'na' => 'na1', 'oce' => 'oc1', 'tr' => 'tr1', 'ru' => 'ru'); //Region => Platform ID,  'PBE' => 'PBE1' DISABLED
$lol_seasons = array('SEASON3','SEASON2014','SEASON2015','SEASON2016');
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

class lang{
	public static function get($key)
	{
		global $lang;
		if(array_key_exists($key,$lang))
		{
			return $lang[$key];
		}
		else
		{
			if(LANG != 'en_EN')
			{
				require('langs/en_EN.php');
			}
			return $lang[$key];
			error_log('[class->lang] CONSTANT_NOT_FOUND: '.$key);
		}
	}
}
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
	public static function checkingame($region,$summonerid)
	{
		global $db;
		global $lol_servers;
		if($db->query('SELECT ingame_last_check FROM inv_users WHERE summoner_id='.$summonerid)->fetch_row()[0] < time()-config('ingame_check_interval'))
		{
			$game_info = readjson('https://'.$region.'.api.pvp.net/observer-mode/rest/consumer/getSpectatorGameInfo/'.strtoupper($lol_servers[$region]).'/'.$summonerid.'?api_key='.LOL_API_KEY);
			if($game_info == 'NOT_FOUND')
			{
				$db->query('UPDATE inv_users SET ingame_last_check='.time().',is_ingame="0" WHERE summoner_id="'.$summonerid.'"');
				return 'NOT_INGAME';
			}
			else
			{
				foreach($game_info['participants'] as $data)
				{
					if($data['bot'] == 'false')
					{
						if($db->query('SELECT id FROM inv_users WHERE summoner_id='.$data['summonerId'])->num_rows == 0)
						{
							$db->query('INSERT INTO inv_users (summoner_id,icon,name,ingame_last_check,is_ingame) VALUES ("'.$data['summonerId'].'","'.$data['profileIconId'].'","'.$data['summonerName'].'",'.time().',1)');
						}
						else
						{
							$db->query('UPDATE inv_users SET ingame_last_check='.time().',is_ingame="1",active_game_id="'.$game_info['gameId'].'" WHERE summoner_id="'.$data['summonerId'].'"');
						}
					}
				}
				return 'INGAME';
			}
		}
		else
		{
			if($db->query('SELECT is_ingame FROM inv_users WHERE summoner_id='.$summonerid)->fetch_row()[0] == 1)
			{
				return 'INGAME';
			}
			else
			{
				return 'NOT_INGAME';
			}
		}
	}
}
 function config($str)
	{
		global $db;
		if($db->query('SELECT id FROM config WHERE name="'.$str.'"')->num_rows == 0)
		{
			error_log('[class->config] CONSTANT_NOT_FOUND: '.$str);
		}
		else
		{
			$ret = $db->query('SELECT value FROM config WHERE name="'.$str.'" LIMIT 1')->fetch_array(); 
			return $ret['value'];
		}
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
function format_summoner_name($str)
{
	return str_replace(' ', '', strtolower(utf8_decode($str)));
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
	return '

				<h5 class="bg-success" id="fuck-adb-not-enabled" style="display: none;">AdBlock is not enabled</h5>
				<h5 class="bg-danger" id="fuck-adb-enabled" style="display: none;">AdBlock is enabled</h5>
			
	
	<script src="'.URL.'/style/js/adblock.js"></script>
	<script>
		function adBlockDetected() {
			$( document ).ready(function() {
			var stack_bottomleft = {"dir1": "right", "dir2": "up", "push": "top"};
			var opts = {
                title: "¿¡Adblock!?",
                text: "Elimina esta notificación desactivando Adblock. Yasuo te lo agradecerá.",
                addclass: "stack-bottomleft",
                stack: stack_bottomleft
            };
           
                    opts.title = "¿¡Adblock!?";
                    opts.text = "Elimina esta notificación desactivando Adblock. Yasuo te lo agradecerá.";
                    opts.type = "info";
            new PNotify(opts);
			});
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
function mapidtotxt($str,$return = 'text')
{
	if($str == '1' or $str == '10' or $str == '11' or $str == '12')
	{
	if($str == '1')
	{
		if($return == 'code')
		{
			return 'rift';
		}
		else
		{
			return 'Antigua grieta del invocador';
		}
	}
	if($str == '8')
	{
		if($return == 'code')
		{
			return 'scar';
		}
		else
		{
			return 'Cicatriz de cristal';
		}
	}
	if($str == '10')
	{
		if($return == 'code')
		{
			return 'tree';
		}
		else
		{
			return 'Bosque retorcido';
		}
	}
	if($str == '11')
	{
		if($return == 'code')
		{
			return 'rift';
		}
		else
		{
			return 'Grieta del invocador';
		}
	}
	if($str == '12')
	{
		if($return == 'code')
		{
			return 'abysm';
		}
		else
		{
			return 'Abismo de los lamentos';
		}
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
		return 'Clasificatoria';
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
	curl_setopt ($ch, CURLOPT_ENCODING, 'gzip');
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
			return 'RATE_LIMIT';
		}
		
		if($httpCode == 200) {
			return json_decode($result, true);
		}
		if($httpCode != 200 && $httpCode != 429 && $httpCode != 404)
		{
			error_log('[readjson] UNDEFINED_ERROR -> '.$httpCode.': '.$url);
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
				if($wl == 0)
				{
					$wl = 1;
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
	return 'Clasificatoria';
	break;
	case 'TEAM_BUILDER_DRAFT_RANKED_5x5':
	return 'Clasificatoria';
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
		return '% Velocidad de ataque por nivel';
		break;
		case 'rFlatDodgeMod':
		return 'Esquivar';
		break;
		case 'rFlatDodgeModPerLevel':
		return 'Esquivar por nivel';
		break;
		case 'PercentDodgeMod':
		return '% Esquivar';
		break;
		case 'FlatCritChanceMod':
		return 'Golpe crítico';
		break;
		case 'rFlatCritChanceModPerLevel':
		return 'Golpe crítico por nivel';
		break;
		case 'PercentCritChanceMod':
		return '% Golpe crítico';
		break;
		case 'FlatCritDamageMod':
		return 'Dañi crítico';
		break;
		case 'rFlatCritDamageModPerLevel':
		return 'Daño crítico por nivel';
		break;
		case 'PercentCritDamageMod':
		return '% Daño crítico';
		break;
		case 'FlatBlockMod':
		return 'Bloqueo';
		break;
		case 'PercentBlockMod':
		return '% Bloqueo';
		break;
		case 'FlatSpellBlockMod':
		return 'Resistencia mágica';
		break;
		case 'rFlatSpellBlockModPerLevel':
		return 'Resistencia mágica por nvel';
		break;
		case 'PercentSpellBlockMod':
		return '% Bloqueo de hechizos';
		break;
		case 'FlatEXPBonus':
		return 'Experiencia extra';
		break;
		case 'PercentEXPBonus':
		return '% Experiencia extra';
		break;
		case 'rPercentCooldownMod':
		return '% Reducción de enfriamientos';
		break;
		case 'rPercentCooldownModPerLevel':
		return '% Reducción de enfriamientos por nivel';
		break;
		case 'rFlatTimeDeadMod':
		return 'Menor tiempo muerto';
		break;
		case 'rFlatTimeDeadModPerLevel':
		return 'Menor tiempo muerto por nivel';
		break;
		case 'rPercentTimeDeadMod':
		return '% Menor tiempo muerto';
		break;
		case 'rPercentTimeDeadModPerLevel':
		return '% Menor tiempo muerto por nivel';
		break;
		case 'rFlatGoldPer10Mod':
		return 'Oro por 10s';
		break;
		case 'rFlatMagicPenetrationMod':
		return 'Penetración mágica';
		break;
		case 'rFlatMagicPenetrationModPerLevel':
		return 'Penetración mágica por nivel';
		break;
		case 'rPercentMagicPenetrationMod':
		return '% Penetración mágica';
		break;
		case 'rPercentMagicPenetrationModPerLevel':
		return '% Penetración mágica por nivel';
		break;
		case 'FlatEnergyRegenMod':
		return 'Regeneración de energía';
		break;
		case 'rFlatEnergyRegenModPerLevel':
		return 'Regeneración de energía por nivel';
		break;
		case 'FlatEnergyPoolMod':
		return 'Energía';
		break;
		case 'rFlatEnergyModPerLevel':
		return 'Energía por nivel';
		break;
		case 'PercentLifeStealMod':
		return 'Robo de vida';
		break;
		case 'PercentSpellVampMod':
		return '% Succión de hechizos';
		break;
		default: return $str;
	}
}