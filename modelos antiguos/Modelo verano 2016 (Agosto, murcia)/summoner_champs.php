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
if($db->query('SELECT id FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summonerInfoId)->num_rows > 0)
{
	$summonerMMR = $db->query('SELECT mmr,tier,division,lp FROM lol_summoners_leagues WHERE region="'.$_GET['region'].'" AND summonerId='.$summonerInfoId)->fetch_row();
	if($summonerMMR[0] == 0)
	{
		$summonerMMRFinal = core::summonerMMR($summonerMMR[3],$summonerMMR[1],$summonerMMR[2]);
		$db->query('UPDATE lol_summoners_leagues SET mmr='.$summonerMMR.' WHERE region="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id']);
	}
	else
	{
		$summonerMMRFinal = $summonerMMR[0];
	}
}
else
{
	$summoner_reload = false;
}
if($db->query('SELECT id FROM lol_summoners_champs WHERE summonerRegion="'.$_GET['region'].'" AND summonerId='.$summonerInfoId)->num_rows == 0)
	{
		$db->query('INSERT INTO lol_summoners_champs (summonerRegion,summonerId,updated) VALUES ("'.$_GET['region'].'",'.$summonerInfoId.',0)');
		$summoner_reload = true;
	}
	else
	{
		if($db->query('SELECT updated FROM lol_summoners_champs WHERE summonerRegion="'.$_GET['region'].'" AND summonerId='.$summonerInfoId)->fetch_row()[0] < (core::current_time()-($config['profile.champmastery.reload.time']*1000)))
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
		$summonerChampsStats = core::extjson(str_replace(array('{{region}}','{{summoner_id}}','{{season}}','{{riotapi}}'),array($_GET['region'],$summonerInfoId,$seasons[0],$config['riot.api.key']),core::$api_url_summonerprofChampsStats));
		if($db->query('SELECT modifyDate FROM lol_summoners_champs WHERE summonerRegion="'.$_GET['region'].'" AND summonerId='.$summonerInfoId)->fetch_row()[0] != $summonerChampsStats['modifyDate'])
		{
			$summonerChampsStatsData = array();
			
			foreach($summonerChampsStats['champions'] as $thisSummonerChampData)
			{
				($thisSummonerChampData['id'] == 0) ? $thisSummonerChampData['id']='all':null;
				
				$summonerChampsStatsData[$thisSummonerChampData['id']] = array();
				$summonerChampsStatsData[$thisSummonerChampData['id']]['gamesPlayed'] = $thisSummonerChampData['stats']['totalSessionsPlayed'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['gamesLost'] = $thisSummonerChampData['stats']['totalSessionsLost'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['gamesWon'] = $thisSummonerChampData['stats']['totalSessionsWon'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalKills'] = $thisSummonerChampData['stats']['totalChampionKills'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalDeaths'] = $thisSummonerChampData['stats']['totalDeathsPerSession'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalAssists'] = $thisSummonerChampData['stats']['totalAssists'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalTurrets'] = $thisSummonerChampData['stats']['totalTurretsKilled'];
				if(!empty($thisSummonerChampData['stats']['maxChampionsKilled']))
				{
					$summonerChampsStatsData[$thisSummonerChampData['id']]['maxKillsSingleGame'] = @$thisSummonerChampData['stats']['maxChampionsKilled'];
				}
				else
				{
					$summonerChampsStatsData[$thisSummonerChampData['id']]['maxKillsSingleGame'] = (int) @$thisSummonerChampData['stats']['mostChampionKillsPerSession'];
				}
				$summonerChampsStatsData[$thisSummonerChampData['id']]['maxDeathsSingleGame'] = $thisSummonerChampData['stats']['maxNumDeaths'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalDoubleKills'] = $thisSummonerChampData['stats']['totalDoubleKills'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalTripleKills'] = $thisSummonerChampData['stats']['totalTripleKills'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalQuadraKills'] = $thisSummonerChampData['stats']['totalQuadraKills'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalPentaKills'] = $thisSummonerChampData['stats']['totalPentaKills'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalUnrealKills'] = $thisSummonerChampData['stats']['totalUnrealKills'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalDmgDealt'] = $thisSummonerChampData['stats']['totalDamageDealt'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalDmgDealtAd'] = $thisSummonerChampData['stats']['totalPhysicalDamageDealt'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalDmgDealtAp'] = $thisSummonerChampData['stats']['totalMagicDamageDealt'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalDmgTaken'] = $thisSummonerChampData['stats']['totalDamageTaken'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalCs'] = $thisSummonerChampData['stats']['totalMinionKills'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalGold'] = $thisSummonerChampData['stats']['totalGoldEarned'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['totalFirstBloods'] = $thisSummonerChampData['stats']['totalFirstBlood'];
				$summonerChampsStatsData[$thisSummonerChampData['id']]['mostSpellsCast'] = $thisSummonerChampData['stats']['mostSpellsCast'];
				/* Champ skill formule */
				$thisSummonerChampSkill = round($summonerMMRFinal+($summonerChampsStatsData[$thisSummonerChampData['id']]['gamesPlayed']/2)-$summonerChampsStatsData[$thisSummonerChampData['id']]['gamesLost']+$summonerChampsStatsData[$thisSummonerChampData['id']]['gamesWon']+($summonerChampsStatsData[$thisSummonerChampData['id']]['totalCs']/100)+$summonerChampsStatsData[$thisSummonerChampData['id']]['maxKillsSingleGame']-$summonerChampsStatsData[$thisSummonerChampData['id']]['maxDeathsSingleGame']+$summonerChampsStatsData[$thisSummonerChampData['id']]['totalFirstBloods']+$summonerChampsStatsData[$thisSummonerChampData['id']]['totalDoubleKills']+$summonerChampsStatsData[$thisSummonerChampData['id']]['totalTripleKills']+($summonerChampsStatsData[$thisSummonerChampData['id']]['totalQuadraKills']*2)+($summonerChampsStatsData[$thisSummonerChampData['id']]['totalPentaKills']*2)+number_format(($summonerChampsStatsData[$thisSummonerChampData['id']]['totalKills']+$summonerChampsStatsData[$thisSummonerChampData['id']]['totalAssists'])/$summonerChampsStatsData[$thisSummonerChampData['id']]['totalDeaths'],2)*100);
				$summonerChampsStatsData[$thisSummonerChampData['id']]['skill'] = $thisSummonerChampSkill;
			}
			$summonerChampsStatsData = addslashes(json_encode($summonerChampsStatsData));
			$db->query('UPDATE lol_summoners_champs SET updated="'.core::current_time().'",modifyDate="'.$summonerChampsStats['modifyDate'].'",data="'.$summonerChampsStatsData.'" WHERE summonerRegion="'.$_GET['region'].'" AND summonerId='.$summonerInfoId);
		}
		else
		{
			$db->query('UPDATE lol_summoners_champs SET updated="'.core::current_time().'" WHERE summonerRegion="'.$_GET['region'].'" AND summonerId='.$summonerInfoId);
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
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/teams"><i class="si si-users"></i> <?php echo $lang['summonerProfileTabsTeams'] ?></a>
					</li>
					<li class="active">
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/champs"><i class="si si-chemistry"></i> <?php echo $lang['summonerProfileTabsChamps'] ?></a>
					</li>
				</ul>
				<div class="block-content tab-content">
					<div class="tab-pane active">
						<?php
						if($db->query('SELECT id FROM lol_summoners_champs WHERE summonerRegion="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->num_rows > 0)
						{
							$summonerChampsData = $db->query('SELECT data FROM lol_summoners_champs WHERE summonerRegion="'.$_GET['region'].'" AND summonerId='.$summoner_info['summoner_id'])->fetch_row();
							$summonerChampsData = json_decode($summonerChampsData[0],true);
						?>
						<div class="block">
							<div class="block-content">
								<table id="TableChampMastery" class="table js-dataTable-full table-striped" style="width:100%">
									<thead>
										<tr>
											<th class="text-center"></th>
											<th class="text-center">skill</th>
											<th class="text-center">partidas</th>
											<th class="text-center">kda</th>
											<th class="text-center">asesinatos</th>
											<th class="text-center">daño</th>
										</tr>
									</thead>
									<tbody>
									<?php
									if(@is_array($summonerChampsData))
									{
										foreach($summonerChampsData as $summonerChampsDataId => $summonerChampsDataInfo)
										{
											if($summonerChampsDataId != 'all')
											{
												$summonerChampsTurrets = number_format(($summonerChampsDataInfo['totalTurrets']/$summonerChampsDataInfo['gamesPlayed']),0);
												($summonerChampsTurrets == 1) ? $summonerChampsTurretFix = $lang['summonerTableChampsTurret']:$summonerChampsTurretFix = $lang['summonerTableChampsTurrets'];
												echo '<tr class="text-center">
													<td class="vcenter"><img class="ChampMasteryChamp" draggable="false" src="'.$config['web.url'].'/assets/game/champions/square/'.$champdata[$summonerChampsDataId]['key'].'.png"> <div class="ChampMasteryChampName">'.$champdata[$summonerChampsDataId]['name'].'</div></td>
													<td class="vcenter" data-sort="'.$summonerChampsDataInfo['skill'].'"><span class="ChampsProfileSkill">'.$summonerChampsDataInfo['skill'].'</span></td>
													<td class="vcenter"><div class="ChampsProfileGames"><span class="leaguesGameBase">'.$summonerChampsDataInfo['gamesPlayed'].'</span> '.$lang['summonerTableChampsGamesPlayed'].' (<span class="leaguesGameWon">'.$summonerChampsDataInfo['gamesWon'].'</span>/<span class="leaguesGameLost">'.$summonerChampsDataInfo['gamesLost'].'</span>)</div>
													<div class=""><span class="ChampsProfileCsNumber">'.number_format(($summonerChampsDataInfo['totalCs']/$summonerChampsDataInfo['gamesPlayed']),0).'</span> <span class="ChampsProfileCsTxt">'.$lang['summonerTableChampsCs'].'</span></div>
													<div class=""><span class="ChampsProfileCsNumber">'.number_format(($summonerChampsDataInfo['totalGold']/$summonerChampsDataInfo['gamesPlayed']),0).'</span> <span class="ChampsProfileCsTxt">'.$lang['summonerTableChampsGold'].'</span></div>
													<div class=""><span class="ChampsProfileCsNumber">'.$summonerChampsTurrets.'</span> <span class="ChampsProfileCsTxt">'.$summonerChampsTurretFix.'</span></div></td>
													<td class="vcenter"><div class="matchHistoryKDA">
													<span class="matchHistoryKA">'.number_format(($summonerChampsDataInfo['totalKills']/$summonerChampsDataInfo['gamesPlayed']),0).'</span> / <span class="matchHistoryD">'.number_format(($summonerChampsDataInfo['totalDeaths']/$summonerChampsDataInfo['gamesPlayed']),0).'</span> / <span class="matchHistoryKA">'.number_format(($summonerChampsDataInfo['totalAssists']/$summonerChampsDataInfo['gamesPlayed']),0).'</span></div><div class="matchHistoryKDARatio" style="margin-top: 0px !important;"><span class="matchHistoryKDARatio">'.number_format((($summonerChampsDataInfo['totalKills']/$summonerChampsDataInfo['gamesPlayed'])+($summonerChampsDataInfo['totalAssists']/$summonerChampsDataInfo['gamesPlayed']))/($summonerChampsDataInfo['totalDeaths']/$summonerChampsDataInfo['gamesPlayed']),2).':1</span> KDA</div> <div>'.$lang['summonerTableChampsRecordKills'].': <b>'.$summonerChampsDataInfo['maxKillsSingleGame'].'</b></div> <div>'.$lang['summonerTableChampsRecordDeaths'].': <b>'.$summonerChampsDataInfo['maxDeathsSingleGame'].'</b></div></td>
													<td data-sort="" class="vcenter">
													<div>'.$lang['summonerTableChampsKillsDouble'].': <b>'.$summonerChampsDataInfo['totalDoubleKills'].'</b></div>
													<div>'.$lang['summonerTableChampsKillsTriple'].': <b>'.$summonerChampsDataInfo['totalTripleKills'].'</b></div>
													<div>'.$lang['summonerTableChampsKillsQuadra'].': <b>'.$summonerChampsDataInfo['totalQuadraKills'].'</b></div>
													<div>'.$lang['summonerTableChampsKillsPenta'].': <b>'.($summonerChampsDataInfo['totalUnrealKills']+$summonerChampsDataInfo['totalPentaKills']).'</b></div></td>
													<td data-sort="" class="vcenter">
													<div>'.$lang['summonerTableChampsDmgTotalDealt'].': <b>'.number_format(($summonerChampsDataInfo['totalDmgDealt']/$summonerChampsDataInfo['gamesPlayed']),0).'</b></div>
													<div>'.$lang['summonerTableChampsDmgAdDealt'].': <b>'.number_format(($summonerChampsDataInfo['totalDmgDealtAd']/$summonerChampsDataInfo['gamesPlayed']),0).'</b></div>
													<div>'.$lang['summonerTableChampsDmgApDealt'].': <b>'.number_format(($summonerChampsDataInfo['totalDmgDealtAp']/$summonerChampsDataInfo['gamesPlayed']),0).'</b></div>
													<div>'.$lang['summonerTableChampsDmgTotalTaken'].': <b>'.number_format(($summonerChampsDataInfo['totalDmgTaken']/$summonerChampsDataInfo['gamesPlayed']),0).'</b></div>
												</tr>';
											}
										}
									}
									else
									{
										echo '<div class="col-sm-6 col-sm-offset-3">
										<h1 class="font-s128 font-w300 text-modern animated zoomInDown">'.$lang['profileChampDataErrorTitle'].'</h1>
										<h2 class="h3 font-w300 push-50 animated fadeInUp">'.$lang['profileChampDataErrorContent'].'</h2>
										</div>';
									}
								?>
									</tbody>
								</table>
							</div>
						</div>
					<?php
						}
						else
						{
							echo '<div class="col-sm-6 col-sm-offset-3">
									<h1 class="font-s128 font-w300 text-modern animated zoomInDown">'.$lang['profileChampDataErrorTitle'].'</h1>
									<h2 class="h3 font-w300 push-50 animated fadeInUp">'.$lang['profileChampDataErrorContent'].'</h2>
								</div>';
						}
					?>
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
<script src="<?php echo $config['web.url'] ?>/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script>
var BaseTableDatatables = function() {
	ChampMastery = function() {
            jQuery("#TableChampMastery").dataTable({
				responsive: false,
                columnDefs: [{
                    targets: [4]
                }],
				"order": [[ 1, "desc" ],[ 2, "desc" ]],
                pageLength: 10,
				retrieve: true,
                lengthMenu: [
                    [5, 10, 15, 20],
                    [5, 10, 15, 20]
                ]
            })
        },
        n = function() {
            var e = jQuery.fn.dataTable;
            jQuery.extend(!0, e.defaults, {
                dom: "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<tr>><'row'<'col-sm-6'i><'col-sm-6'p>>",
                renderer: "bootstrap",
                oLanguage: {
                    sLengthMenu: "_MENU_",
                    sInfo: "<?php echo $lang['jsTableShowing'] ?> <strong>_START_</strong>-<strong>_END_</strong> <?php echo $lang['jsTableOf'] ?> <strong>_TOTAL_</strong>",
                    oPaginate: {
                        sPrevious: '<i class="fa fa-angle-left"></i>',
                        sNext: '<i class="fa fa-angle-right"></i>'
                    }
                }
            }), jQuery.extend(e.ext.classes, {
                sWrapper: "dataTables_wrapper form-inline dt-bootstrap",
                sFilterInput: "form-control",
                sLengthSelect: "form-control"
            }), e.ext.renderer.pageButton.bootstrap = function(a, t, n, s, o, l) {
                var r, i, u = new e.Api(a),
                    d = a.oClasses,
                    c = a.oLanguage.oPaginate,
                    b = function(e, t) {
                        var s, g, f, p, T = function(e) {
                            e.preventDefault(), jQuery(e.currentTarget).hasClass("disabled") || u.page(e.data.action).draw(!1)
                        };
                        for(s = 0, g = t.length; g > s; s++)
                            if(p = t[s], jQuery.isArray(p)) b(e, p);
                            else {
                                switch(r = "", i = "", p) {
                                    case "ellipsis":
                                        r = "&hellip;", i = "disabled";
                                        break;
                                    case "first":
                                        r = c.sFirst, i = p + (o > 0 ? "" : " disabled");
                                        break;
                                    case "previous":
                                        r = c.sPrevious, i = p + (o > 0 ? "" : " disabled");
                                        break;
                                    case "next":
                                        r = c.sNext, i = p + (l - 1 > o ? "" : " disabled");
                                        break;
                                    case "last":
                                        r = c.sLast, i = p + (l - 1 > o ? "" : " disabled");
                                        break;
                                    default:
                                        r = p + 1, i = o === p ? "active" : ""
                                }
                                r && (f = jQuery("<li>", {
                                    "class": d.sPageButton + " " + i,
                                    "aria-controls": a.sTableId,
                                    tabindex: a.iTabIndex,
                                    id: 0 === n && "string" == typeof p ? a.sTableId + "_" + p : null
                                }).append(jQuery("<a>", {
                                    href: "#"
                                }).html(r)).appendTo(e), a.oApi._fnBindAction(f, {
                                    action: p
                                }, T))
                            }
                    };
                b(jQuery(t).empty().html('<ul class="pagination"/>').children("ul"), s)
            }, e.TableTools && (jQuery.extend(!0, e.TableTools.classes, {
                container: "DTTT btn-group",
                buttons: {
                    normal: "btn btn-default",
                    disabled: "disabled"
                },
                collection: {
                    container: "DTTT_dropdown dropdown-menu",
                    buttons: {
                        normal: "",
                        disabled: "disabled"
                    }
                },
                print: {
                    info: "DTTT_print_info"
                },
                select: {
                    row: "active"
                }
            }), jQuery.extend(!0, e.TableTools.DEFAULTS.oTags, {
                collection: {
                    container: "ul",
                    button: "li",
                    liner: "a"
                }
            }))
        };
    return {
        init: function() {
            n(), ChampMastery()
        }
    }
}();
jQuery(function() {
    BaseTableDatatables.init()
});</script>
<?php if(@$summoner_reload == true)
{
	echo '<script>$.notify("'.$lang['ajaxChampsProfileLoaded'].'", "info");</script>';
} ?>
</body>
</html>