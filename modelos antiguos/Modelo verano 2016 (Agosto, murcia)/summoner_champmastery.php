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
if($db->query('SELECT id FROM lol_summoners_champmastery WHERE summoner_region="'.$_GET['region'].'" AND summoner_id='.$summonerInfoId)->num_rows == 0)
	{
		$db->query('INSERT INTO lol_summoners_champmastery (summoner_region,summoner_id,updated) VALUES ("'.$_GET['region'].'",'.$summonerInfoId.',0)');
		$summoner_reload = true;
	}
	else
	{
		if($db->query('SELECT updated FROM lol_summoners_champmastery WHERE summoner_region="'.$_GET['region'].'" AND summoner_id='.$summonerInfoId)->fetch_row()[0] < (core::current_time()-($config['profile.champmastery.reload.time']*1000)))
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
		$summonerChampMastery = core::extjson(str_replace(array('{{region}}','{{platform}}','{{summoner_id}}','{{riotapi}}'),array($_GET['region'],core::region2platform($_GET['region']),$summonerInfoId,$config['riot.api.key']),core::$api_url_summonerprofChampMastery));

		$champMasteryTotalLevels = 0;
		$champMasteryTotalPoints = 0;
		$champMasteryMain = null;
		$champMasteryMainPoints = 0;
		$champMasteryData = array();
		$champMasteryData['lvl6Champs'] = 0;
		$champMasteryData['lvl7Champs'] = 0;
		if(is_array($summonerChampMastery))
		{
			foreach($summonerChampMastery as $currentChampMasteryData)
			{
				$champMasteryTotalLevels = ($champMasteryTotalLevels+$currentChampMasteryData['championLevel']);
				$champMasteryTotalPoints = ($champMasteryTotalPoints+$currentChampMasteryData['championPoints']);
				$champMasteryData['champs'][$currentChampMasteryData['championId']] = array();
				$champMasteryData['champs'][$currentChampMasteryData['championId']]['level'] = $currentChampMasteryData['championLevel'];
				$champMasteryData['champs'][$currentChampMasteryData['championId']]['points'] = $currentChampMasteryData['championPoints'];
				$champMasteryData['champs'][$currentChampMasteryData['championId']]['lastPlayTime'] = $currentChampMasteryData['lastPlayTime'];
				$champMasteryData['champs'][$currentChampMasteryData['championId']]['chestGranted'] = $currentChampMasteryData['chestGranted'];
				$champMasteryData['champs'][$currentChampMasteryData['championId']]['tokensEarned'] = $currentChampMasteryData['tokensEarned'];
				$champMasteryData['champs'][$currentChampMasteryData['championId']]['lastLvlPlus'] = $currentChampMasteryData['championPointsSinceLastLevel'];
				$champMasteryData['champs'][$currentChampMasteryData['championId']]['nextLvlNeed'] = $currentChampMasteryData['championPointsUntilNextLevel'];
				($currentChampMasteryData['championLevel'] == 6) ? $champMasteryData['lvl6Champs']=$champMasteryData['lvl6Champs']+1:null;
				($currentChampMasteryData['championLevel'] == 7) ? $champMasteryData['lvl7Champs']=$champMasteryData['lvl7Champs']+1:null;
				if($champMasteryMainPoints < $currentChampMasteryData['championPoints']) { $champMasteryMain=$currentChampMasteryData['championId'];$champMasteryMainPoints=$currentChampMasteryData['championPoints']; }
			}
		}
		
		$champMasteryData = addslashes(json_encode($champMasteryData));
		$db->query('UPDATE lol_summoners_champmastery SET updated="'.core::current_time().'",totalLevels='.$champMasteryTotalLevels.',totalPoints='.$champMasteryTotalPoints.',data="'.$champMasteryData.'",mainChamp='.$champMasteryMain.' WHERE summoner_region="'.$_GET['region'].'" AND summoner_id='.$summonerInfoId);
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
					<li class="active">
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/champmastery"><i class="si si-badge"></i> <?php echo $lang['summonerProfileTabsChampMastery'] ?></a>
					</li>
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/leagues"><i class="si si-trophy"></i> <?php echo $lang['summonerProfileTabsLeagues'] ?></a>
					</li>
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/teams"><i class="si si-users"></i> <?php echo $lang['summonerProfileTabsTeams'] ?></a>
					</li>
					<li>
						<a href="<?php echo $config['web.url'].'/summoner/'.$_GET['region'].'/'.core::format_summonername($_GET['summoner']) ?>/champs"><i class="si si-chemistry"></i> <?php echo $lang['summonerProfileTabsChamps'] ?></a>
					</li>
				</ul>
				<div class="block-content tab-content">
					<div class="tab-pane active">
						<?php
						if($db->query('SELECT id FROM lol_summoners_champmastery WHERE summoner_region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id'])->num_rows > 0)
						{
							$summonerChampMasteryData = $db->query('SELECT data,totalLevels,totalPoints FROM lol_summoners_champmastery WHERE summoner_region="'.$_GET['region'].'" AND summoner_id='.$summoner_info['summoner_id'])->fetch_row();
							$summonerChampMasteryDataFull = json_decode($summonerChampMasteryData[0],true);
						?>
						<div class="block">
							<div class="block-content">
								<table id="TableChampMastery" class="table js-dataTable-full table-striped" style="width:100%">
									<thead>
										<tr>
											<th class="text-center"></th>
											<th class="text-center"><?php echo $lang['profileChampMasteryHeadLevel'] ?></th>
											<th class="text-center"><?php echo $lang['profileChampMasteryHeadPoints'] ?></th>
											<th class="text-center"><?php echo $lang['profileChampMasteryHeadChest'] ?></th>
											<th class="text-center"><?php echo $lang['profileChampMasteryHeadNextLevel'] ?></th>
											<th class="text-center"><?php echo $lang['profileChampMasteryHeadPlayed'] ?></th>
										</tr>
									</thead>
									<tbody>
									<?php
									if(@is_array($summonerChampMasteryDataFull['champs']))
									{
										foreach($summonerChampMasteryDataFull['champs'] as $ChampMasteryChampId => $ChampMasteryChampData)
										{
											if($ChampMasteryChampData['chestGranted'] == true)
											{
												$ChampMasteryChampDataChEstAvaliable = 'off';
											}
											else
											{
												$ChampMasteryChampDataChEstAvaliable = 'on';
											}
											if($ChampMasteryChampData['nextLvlNeed'] == 0)
											{
												$ChampMasteryChampDataNextLevelPoints = $lang['profileChampMasteryMaxLvlReached'];
											}
											else
											{
												$ChampMasteryChampDataNextLevelPoints = str_replace('{{points}}',$ChampMasteryChampData['nextLvlNeed'],$lang['profileChampMasteryNextLvlNeeded']);
											}
											echo '<tr class="text-center">
												<td class="vcenter"><img class="ChampMasteryChamp" draggable="false" src="'.$config['web.url'].'/assets/game/champions/square/'.$champdata[$ChampMasteryChampId]['key'].'.png"> <div class="ChampMasteryChampName">'.$champdata[$ChampMasteryChampId]['name'].'</div></td>
												<td class="vcenter" data-sort="'.$ChampMasteryChampData['level'].'"><img draggable="false" src="'.$config['web.url'].'/assets/game/champions/mastery/tier'.$ChampMasteryChampData['level'].'.png"></td>
												<td class="vcenter">'.number_format($ChampMasteryChampData['points'],0,',',',').'</td>
												<td class="vcenter"><img draggable="false" src="'.$config['web.url'].'/assets/game/champions/mastery/chest_'.$ChampMasteryChampDataChEstAvaliable.'.png"> <div class="ChampMasteryChestStatus">'.$lang['profileChampMasteryChestStatus_'.$ChampMasteryChampDataChEstAvaliable].'</div></td>
												<td data-sort="'.(int) $ChampMasteryChampDataNextLevelPoints.'" class="vcenter">'.$ChampMasteryChampDataNextLevelPoints.'</td>
												<td data-sort="'.$ChampMasteryChampData['lastPlayTime'].'" class="vcenter">'.str_replace('{{time}}',core::time_elapsed($ChampMasteryChampData['lastPlayTime'] / 1000),$lang['profileChampMasteryPlayed']).'</td>
											</tr>';
										}
									}
									else
									{
										echo '<div class="col-sm-6 col-sm-offset-3">
										<h1 class="font-s128 font-w300 text-modern animated zoomInDown">'.$lang['profileChampMasteryErrorTitle'].'</h1>
										<h2 class="h3 font-w300 push-50 animated fadeInUp">'.$lang['profileChampMasteryErrorContent'].'</h2>
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
									<h1 class="font-s128 font-w300 text-modern animated zoomInDown">'.$lang['profileChampMasteryErrorTitle'].'</h1>
									<h2 class="h3 font-w300 push-50 animated fadeInUp">'.$lang['profileChampMasteryErrorContent'].'</h2>
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
	echo '<script>$.notify("'.$lang['ajaxChampMasteryLoaded'].'", "info");</script>';
} ?>
</body>
</html>