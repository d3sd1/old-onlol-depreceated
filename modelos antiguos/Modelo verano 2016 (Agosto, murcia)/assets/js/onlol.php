<?php
require('../../kernel/core.php');
if($config['web.js.disabletxtsel'] == true)
{
	echo 'document.onselectstart=new Function ("return false"); if (window.sidebar){document.onmousedown=false;document.onclick=true;}';
}
?>
var dtableTranslateSearch = "<?php echo $lang['jsTableSearch'] ?>";
var dtableTranslateSearchPlaceholder = "<?php echo $lang['jsTableSearchPlaceholder'] ?>";
var dtableTranslateProcessing = "<?php echo $lang['jsTableProcessing'] ?>";
var dtableTranslateLoading = "<?php echo $lang['jsTableLoading'] ?>";
var dtableTranslateNoRecords = "<?php echo $lang['jsTableNoRecords'] ?>";
var dtableTranslateShowEntries = "<?php echo str_replace('{{menu}}','_MENU_',$lang['jsTableShowRecords']) ?>";
var dtableTranslateNoRecordsAtAll = "<?php echo $lang['jsTableNoRecordsAtAll'] ?>";
var dtableTranslateFirst = "<?php echo $lang['jsTableFirst'] ?>";
var dtableTranslateLast = "<?php echo $lang['jsTableLast'] ?>";
var dtableTranslateNext = "<?php echo $lang['jsTableNext'] ?>";
var dtableTranslatePrev = "<?php echo $lang['jsTablePrev'] ?>";
var dtableTranslateSortASC = "<?php echo $lang['jsTableSortASC'] ?>";
var dtableTranslateSortDSC = "<?php echo $lang['jsTableSortDSC'] ?>";
var dtableTranslateShowing0 = "<?php echo $lang['jsTableShowing0'] ?>";
var dtableTranslateShowingEntries = "<?php echo str_replace(array('{{START}}','{{END}}','{{TOTAL}}'),array('_START_','_END_','_TOTAL_'),$lang['jsTableShowingEntries']) ?>";
var dtableTranslateShowingEntriesFilter = "<?php echo str_replace('{{max}}','_MAX_',$lang['jsTableShowingEntriesFilter']) ?>";
function changestrregion(region) {
	$(function () {
		document.getElementById("regionBtn").innerHTML = region;
		document.getElementById("regionBtn").value = region;
		document.getElementById("search_" + readCookie('onlol_region').toLowerCase()).className = null;
		document.cookie = "onlol_region=" + region.toLowerCase() + "; expires=Thu, 18 Dec 2037 12:00:00 UTC";
		document.getElementById("search_" + region.toLowerCase()).className = 'active';
	});
}
function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}
function searchSummoner(key)
{
	if(key != 'fastpass') { keypress = (document.all) ? key.keyCode : key.which; } else {keypress = 0;}
	if (keypress==13 || key == "fastpass") {
		if(document.getElementById("summonerName").value.length > 0){
			document.cookie = "onlol_lastSummonerSearch=" + document.getElementById("summonerName").value + "; expires=Thu, 18 Dec 2037 12:00:00 UTC";
			window.location='<?php echo $config['web.url'] ?>/summoner/' + document.getElementById("regionBtn").innerHTML.toLowerCase() + '/' + document.getElementById("summonerName").value.toLowerCase();
		}
	}
}
