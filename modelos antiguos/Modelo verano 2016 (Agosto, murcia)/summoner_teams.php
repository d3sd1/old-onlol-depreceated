<?php
require('kernel/core.php');

if(empty($_GET['summoner']))
{
	header('Location: '.$config['web.url'].'/?notify=error_summonernotset');
	die();
}
if(empty($_GET['region']))
{
	header('Location: '.$config['web.url'].'/?notify=error_regionnotset');
	die();
}
if($db->query('SELECT id FROM lol_summoners WHERE region="'.$_GET['region'].'" AND name="'.$_GET['summoner'].'"')->num_rows == 0)
{
	header('Location: '.$config['web.url'].'/summoner/'.$_GET['region'].'/'.$_GET['summoner']);
	die();
}
if(core::check_valid_region($_GET['region']) == false)
{
	header('Location: '.$config['web.url'].'/?notify=error_regionnotset');
	die();
}
$summonerInfoId = $db->query('SELECT summoner_id FROM lol_summoners WHERE region="'.$_GET['region'].'" AND name="'.$_GET['summoner'].'"')->fetch_row()[0];
if($db->query('SELECT level FROM lol_summoners WHERE region="'.$_GET['region'].'" AND summoner_id='.$summonerInfoId)->fetch_row()[0] < 30)
{
	header('Location: '.$config['web.url'].'/summoner/'.$_GET['region'].'/'.$_GET['summoner'].'?notify=leaguenolvl');
	die();
}
	
if($db->query('SELECT id FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summonerInfoId)->num_rows == 0)
{
	$db->query('INSERT INTO lol_summoners_leagues (region,summonerId,updated) VALUES ("'.$_GET['region'].'",'.$summonerInfoId.',0)');
	$summoner_reload = true;
}
else
{
	if($db->query('SELECT teamsUpdated FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summonerInfoId)->fetch_row()[0] < (core::current_time()-($config['profile.leagues.reload.time']*1000)))
	{
		$summoner_reload = true;
	}
	else
	{
		$summoner_reload = false;
	}
}
if($summoner_reload == true)
{
	$summonerLeagues = core::extjson(str_replace(array('{{region}}','{{summoner_id}}','{{riotapi}}'),array($_GET['region'],$summonerInfoId,$config['riot.api.key']),core::$api_url_summonerprofLeague));
		
	if($summonerLeagues == FALSE) //Is unranked
	{
		$db->query('UPDATE lol_summoners_leagues SET teamsUpdated="'.core::current_time().'" WHERE region="'.$_GET['region'].'" AND summonerId='.$summonerInfoId);
	}
	else
	{
		foreach($summonerLeagues[$summonerInfoId] as $leagueCount => $leagueData)
		{
			if($leagueData['queue'] != 'RANKED_SOLO_5x5')
			{
				$teamids = array();
				foreach($leagueData['entries'] as $teamCount => $teamData)
				{
					($teamData['isHotStreak'] == false) ? $teamData['isHotStreak'] = 0:$teamData['isHotStreak'] = 1;
					($teamData['isVeteran'] == false) ? $teamData['isVeteran'] = 0:$teamData['isVeteran'] = 1;
					($teamData['isFreshBlood'] == false) ? $teamData['isFreshBlood'] = 0:$teamData['isFreshBlood'] = 1;
					($teamData['isInactive'] == false) ? $teamData['isInactive'] = 0:$teamData['isInactive'] = 1;
					(!empty($teamData['miniSeries'])) ? $teamData['miniSeries'] = addslashes(json_encode($teamData['miniSeries'])):$teamData['miniSeries'] = 'NOT_ENABLED';
					if($db->query('SELECT id FROM lol_teams_leagues WHERE region="'.$_GET['region'].'" AND teamId="'.$teamData['playerOrTeamId'].'"')->num_rows == 0)
					{
						$db->query('INSERT INTO lol_teams_leagues (name,division,lp,wins,losses,isHotStreak,isVeteran,isFreshBlood,isInactive,teamName,tier,updated,region,teamId,teamQueue,miniseries) VALUES ("'.addslashes($leagueData['name']).'","'.$teamData['division'].'",'.$teamData['leaguePoints'].','.$teamData['wins'].','.$teamData['losses'].','.$teamData['isHotStreak'].','.$teamData['isVeteran'].','.$teamData['isFreshBlood'].','.$teamData['isInactive'].',"'.$teamData['playerOrTeamName'].'","'.substr($leagueData['tier'],0,1).'","'.core::current_time().'","'.$_GET['region'].'","'.$teamData['playerOrTeamId'].'","'.$leagueData['queue'].'","'.$teamData['miniSeries'].'")') or die($db->error);
					}
					else
					{
						$db->query('UPDATE lol_teams_leagues SET name="'.addslashes($leagueData['name']).'",division="'.$teamData['division'].'",lp='.$teamData['leaguePoints'].',wins='.$teamData['wins'].',losses='.$teamData['losses'].',isHotStreak='.$teamData['isHotStreak'].',isVeteran='.$teamData['isVeteran'].',isFreshBlood='.$teamData['isFreshBlood'].',isInactive='.$teamData['isInactive'].',teamName="'.$teamData['playerOrTeamName'].'",tier="'.substr($leagueData['tier'],0,1).'",updated="'.core::current_time().'",miniseries="'.$teamData['miniSeries'].'" WHERE region="'.$_GET['region'].'" AND teamQueue="'.$leagueData['queue'].'" AND teamId="'.$teamData['playerOrTeamId'].'"') or die($db->error);
					}
					if($teamData['playerOrTeamId'] == $leagueData['participantId'])
					{
						$teamids[] = $teamData['playerOrTeamId'];
					}
				}
				/* Drop teams of before */ 
				$summonerTeamsPrevious = $db->query('SELECT teamIds FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summonerInfoId)->fetch_row()[0];
				foreach(explode(';',$summonerTeamsPrevious) as $summonerTeamsPreviousId)
				{
					$purgedSummoners = explode(';',$db->query('SELECT players FROM lol_teams_leagues WHERE teamId="'.$summonerTeamsPreviousId.'"')->fetch_row()[0]);
					unset($purgedSummoners[array_search($summonerInfoId,$purgedSummoners)]);
					$purgedSummoners = implode(';',$purgedSummoners);
				
					$db->query('UPDATE lol_teams_leagues SET players="'.$purgedSummoners.'" WHERE teamId="'.$summonerTeamsPreviousId.'"') or die($db->error);
				}
				/* Add actual teams */ 
				foreach($teamids as $thisTeamId)
				{
					$purgedSummoners = explode(';',$db->query('SELECT players FROM lol_teams_leagues WHERE teamId="'.$thisTeamId.'"')->fetch_row()[0]);
					array_unshift($purgedSummoners,$summonerInfoId);
					$purgedSummoners = implode(';',$purgedSummoners);
					
					$db->query('UPDATE lol_teams_leagues SET players="'.$purgedSummoners.'" WHERE teamId="'.$thisTeamId.'"') or die($db->error);
				}
				$db->query('UPDATE lol_summoners_leagues SET teamIds="'.implode(';',$teamids).'" WHERE region="'.$_GET['region'].'" AND summonerId='.$summonerInfoId);
			}
		}
		$db->query('UPDATE lol_summoners_leagues SET teamsUpdated="'.core::current_time().'" WHERE region="'.$_GET['region'].'" AND summonerId='.$summonerInfoId);
	}
}
/* Now retrieve Data */
$summoner_info=$db->query('SELECT region,summoner_id,name,icon,revision,level,matches,quality FROM lol_summoners WHERE region="'.$_GET['region'].'" AND name="'.$_GET['summoner'].'"')->fetch_array();
$champdata = core::readjson('champs/full/'.$user_lang);
if($db->query('SELECT id FROM lol_summoners_champmastery WHERE summoner_region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id'])->num_rows > 0)
{
	$userMainChamp = $db->query('SELECT mainChamp FROM lol_summoners_champmastery WHERE summoner_region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id'])->fetch_row()[0];
	if($userMainChamp == 0)
	{
		$userMainChamp = $config['web.profile.defaultmainchamp'];
	}
}
else
{
	$userMainChamp = $config['web.profile.defaultmainchamp'];
}
if($db->query('SELECT id FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->num_rows > 0)
{
	$summoner_infoLeague = $db->query('SELECT mmr,tier,division FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->fetch_row();
}
else
{
	$summoner_infoLeague = false;
}
?>
<!DOCTYPE html>
<!--[if IE 9]>
	<html class="ie9 no-focus">
<![endif]-->
<!--[if gt IE 9]><!-->
	<html class="no-focus"> 
<!--<![endif]-->
<head>
	<?php echo template::basehead($lang['pageMetaTitleIndex']); ?>
	<link rel="stylesheet" id="css-main" href="<?php echo $config['web.url'] ?>/assets/js/plugins/datatables/jquery.dataTables.min.css">
</head>
<body>

<div id="page-container" class="sidebar-l sidebar-o side-scroll header-navbar-fixed <?php if(!empty($_COOKIE['onlol_sidebar']) && @$_COOKIE['onlol_sidebar'] == 'min') {echo 'sidebar-mini';} ?>">
	
	
	<?php echo template::sideBarRight(); ?>
	<?php echo template::headerNavBar($user_region); ?>
	<?php echo template::sideBar(null); ?>
	<!-- Start Summoner -->

<main id="main-container" style="background-image: url(<?php echo $config['web.url'] ?>/assets/game/champions/splash/<?php echo $champdata[$userMainChamp]['key'] ?>_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
	<?php echo template::profileHead($summoner_info['name'],$summoner_info['level'],$summoner_info['icon'],$summoner_info['matches'],$summoner_infoLeague[1],$summoner_infoLeague[2],$summoner_infoLeague[0],$summoner_info['quality'],$summoner_info['summoner_id'],$summoner_infoLeague) ?>
	<div class="content">
		<div class="row">
			<div class="block">
				<ul class="nav nav-tabs nav-tabs-alt nav-justified">
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>"><i class="si si-list"></i> <?php echo $lang['summonerProfileTabsMatchHistory'] ?></a>
					</li>
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/champmastery"><i class="si si-badge"></i> <?php echo $lang['summonerProfileTabsChampMastery'] ?></a>
					</li>
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/leagues"><i class="si si-trophy"></i> <?php echo $lang['summonerProfileTabsLeagues'] ?></a>
					</li>
					<li class="active">
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/teams"><i class="si si-users"></i> <?php echo $lang['summonerProfileTabsTeams'] ?></a>
					</li>
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/champs"><i class="si si-chemistry"></i> <?php echo $lang['summonerProfileTabsChamps'] ?></a>
					</li>
				</ul>
				<div class="block-content tab-content">
					<div class="bg-gray-light border-b">
						<div class="content content-narrow">
							<ul class="js-media-filter nav nav-pills push">
								<li class="active">
									<a href="javascript:void(0)" data-category="all">
										<i class="fa fa-fw fa-folder-open-o push-5-r"></i> <?php echo $lang['teams_all'] ?>
									</a>
								</li>
								<li class="">
									<a href="javascript:void(0)" data-category="RANKED_TEAM_3x3">
										<i class="fa fa-fw fa-file-movie-o push-5-r"></i> <?php echo $lang['teams_RANKED_TEAM_3x3'] ?>
									</a>
								</li>
								<li class="">
									<a href="javascript:void(0)" data-category="RANKED_TEAM_5x5">
										<i class="fa fa-fw fa-file-photo-o push-5-r"></i> <?php echo $lang['teams_RANKED_TEAM_5x5'] ?>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="content content-narrow">
					<div class="js-media-filter-items row">
					<?php
					$summonerTeamIdsValue = $db->query('SELECT teamIds FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->fetch_row()[0];
					$summonerTeamIds = explode(';',$summonerTeamIdsValue);
					if(is_array($summonerTeamIds) && $summonerTeamIdsValue != null)
					{
						foreach($summonerTeamIds as $thisTeamId)
						{
							$thisTeamData = $db->query('SELECT teamName,teamQueue,tier,division,isHotStreak,isVeteran,isFreshBlood,isInactive,lp,wins,losses,miniseries FROM lol_teams_leagues WHERE teamID="'.$thisTeamId.'"')->fetch_row();
							$leagueDataThisPlayerEmblems = null;
							($thisTeamData[4] == 1) ? $leagueDataThisPlayerEmblems .= '<i class="fa fa-fire" data-toggle="popover" data-placement="left" data-content="'.$lang['profilePopoverLeagueSpreeDsc'].'" data-original-title="'.$lang['profilePopoverLeagueSpree'].'"></i>':null;
							($thisTeamData[5] == 1) ? $leagueDataThisPlayerEmblems .= '<i class="si si-badge" data-toggle="popover" data-placement="left" data-content="'.$lang['profilePopoverLeagueVeteranDsc'].'" data-original-title="'.$lang['profilePopoverLeagueVeteran'].'"></i>':null;
							($thisTeamData[6] == 1) ? $leagueDataThisPlayerEmblems .= '<i class="fa fa-star" data-toggle="popover" data-placement="left" data-content="'.$lang['profilePopoverLeagueNoobDsc'].'" data-original-title="'.$lang['profilePopoverLeagueNoob'].'"></i>':null;
							($thisTeamData[7] == 1) ? $leagueDataThisPlayerEmblems .= '<i class="si si-close" data-toggle="popover" data-placement="left" data-content="'.$lang['profilePopoverLeagueInactiveDsc'].'" data-original-title="'.$lang['profilePopoverLeagueInactive'].'"></i>':null;
							if($thisTeamData[11] != 'NOT_ENABLED')
							{
								$thisTeamData[11] = json_decode($thisTeamData[11],true);
								$miniSeriesAndLp = str_replace(array('N','L','W'),array('<i class="fa fa-circle-o" data-toggle="popover" data-placement="top" data-content="'.$lang['profilePopoverLeagueMiniseriesNotPlayed'].'" data-original-title="'.$lang['profilePopoverLeagueMiniseries'].'"></i>','<i class="fa fa-times-circle-o" data-toggle="popover" data-placement="top" data-content="'.$lang['profilePopoverLeagueMiniseriesLost'].'" data-original-title="'.$lang['profilePopoverLeagueMiniseries'].'"></i>','<i class="fa fa-check-circle-o" data-toggle="popover" data-placement="top" data-content="'.$lang['profilePopoverLeagueMiniseriesWon'].'" data-original-title="'.$lang['profilePopoverLeagueMiniseries'].'"></i>'),$thisTeamData[11]['progress']);
							}
							else
							{
								$miniSeriesAndLp = $thisTeamData[8];
							}				
							echo '<div class="col-sm-6 col-md-4 col-lg-3" style="display: block;">
								<div class="block block-rounded animated fadeIn" data-category="'.$thisTeamData[1].'">
									<div class="block-header">
										<ul class="block-options">
											'.$leagueDataThisPlayerEmblems.'
										</ul>
									</div>
									<div class="block-content block-content-full text-center">
										<div class="item item-2x item-circle bg-warning-light text-warning">
											<img draggable="false" width="85px" height="85px" src="'.$config['web.url'].'/assets/game/summoners/divisions/'.$thisTeamData[2].'/'.$thisTeamData[3].'.png">
										</div>
									</div>
									<div class="block-content block-content-full text-center mheight-100">
										<span class="leaguesGameWon">'.$thisTeamData[9].' '.$lang['teamsWinShort'].'</span> <span class="leaguesGameLost">'.$thisTeamData[10].' '.$lang['teamsLostShort'].'</span>
										<div class="text-gray">'.$miniSeriesAndLp.' '.$lang['summonerProfileLeaguesLPShort'].'</div>
										<h3 class="h4 font-w300 text-black push-5">'.$thisTeamData[0].'</h3>
										<span class="text-gray">'.$lang['leagueName_'.$thisTeamData[2]].' '.$thisTeamData[3].'</span>
									</div>
								</div>
							</div>';
						}
					}
					else
					{
						echo '<div class="col-sm-6 col-sm-offset-3">
								<h1 class="font-s128 font-w300 text-modern animated zoomInDown">'.$lang['profileLeagueErrorTitle'].'</h1>
								<h2 class="h3 font-w300 push-50 animated fadeInUp">'.$lang['profileLeagueTeamsErrorContent'].'</h2>
							</div>';
					}
					?>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<div class="modal" id="loadNewAjax" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="block block-themed block-transparent remove-margin-b">
				<div class="block-header bg-primary-dark">
					<h3 class="block-title"><?php echo $lang['profileModalLoadingTitle'] ?></h3>
				</div>
				<div class="block-content"><center><i class="fa fa-cog fa-5x fa-spin"></i><p> <?php echo $lang['profileModalLoading'] ?></p></center>
				</div>
			</div>
		</div>
	</div>
</div>
	<!-- End Summoner -->
	<?php echo template::footer(); ?>
</div>
<?php echo template::scripts(); ?>
<script src="<?php echo $config['web.url'] ?>/assets/js/pages/base_pages_files.js"></script>

<?php if(@$summoner_reload == true)
{
	echo '<script>$.notify("'.$lang['ajaxLeagueLoaded'].'", "info");</script>';
} ?>
</body>
</html>