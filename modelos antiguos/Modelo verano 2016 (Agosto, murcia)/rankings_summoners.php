<?php
require('kernel/core.php');
?>
<!DOCTYPE html>
<!--[if IE 9]>
	<html class="ie9 no-focus">
<![endif]-->
<!--[if gt IE 9]><!-->
	<html class="no-focus"> 
<!--<![endif]-->
<head>
	<?php echo template::basehead($lang['pageMetaTitleRankingSummoners']); ?>
	<link rel="stylesheet" id="css-main" href="<?php echo $config['web.url'] ?>/assets/js/plugins/datatables/jquery.dataTables.min.css">
</head>
<body>

<div id="page-container" class="sidebar-l sidebar-o side-scroll header-navbar-fixed <?php if(!empty($_COOKIE['onlol_sidebar']) && @$_COOKIE['onlol_sidebar'] == 'min') {echo 'sidebar-mini';} ?>">
	
	
	<?php echo template::sideBarRight(); ?>
	<?php echo template::headerNavBar($user_region); ?>
	<?php echo template::sideBar('rankings','rankingsSummoners'); ?>
	<!-- Start Forums -->
<main id="main-container" style="background-image: url(<?php echo $config['web.url'] ?>/assets/game/champions/splash/Rammus_0.jpg); background-repeat: no-repeat; background-size: cover; background-attachment: fixed;">
<div class="content">
<div class="block">
							<div class="block-content">
								<table id="TableRankingSummoners" class="table table-bordered js-dataTable-full" style="width:100%">
									<thead>
										<tr>
											<th class="text-center"><?php echo $lang['rankingsSummonerPosition'] ?></th>
											<th class="text-center"><?php echo $lang['rankingsSummonerName'] ?></th>
											<th class="text-center"><?php echo $lang['rankingsSummonerRegion'] ?></th>
										</tr>
									</thead>
									<tbody>
									<?php
									$summonersToRank = $db->query('SELECT id,position,summonerName,region FROM lol_summoners_ranking');
									while($row = $summonersToRank->fetch_row())
									{
										echo '<tr>
											<td class="text-center">'.$row[1].'</td>
											<td class="font-w600"><img draggable="false" class="lazy img-avatar img-avatar48" data-original="http://avatar.leagueoflegends.com/euw/'.core::format_summonername($row[2]).'.png" alt=""> <a href="'.$config['web.url'].'/summoner/'.$row[3].'/'.$row[2].'">'.$row[2].'</a></td>
											<td class="hidden-xs">'.$row[3].'</td>
											</tr>';
									}
									?>
									</tbody>
								</table>
							</div>
						</div>

</div>
</main>
	<!-- End Forums -->
	<?php echo template::footer(); ?>
</div>
<?php echo template::scripts(); ?>
<script src="<?php echo $config['web.url'] ?>/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script>
var BaseTableDatatables = function() {
    var RankingSummoners = function() {
            jQuery("#TableRankingSummoners").dataTable({
				responsive: false,
                columnDefs: [{
                    targets: [0,2]
                },{
					orderable: false, targets: [1]
				}],
				"order": [[ 0, "asc" ]],
                pageLength: 10,
				retrieve: true,
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
            n(), RankingSummoners()
        }
    }
}();
jQuery(function() {
    BaseTableDatatables.init()
});</script>
</body>
</html>