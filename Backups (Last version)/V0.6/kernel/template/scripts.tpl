<script>
var lang = {sSortDescendingsortDesc:"<?php echo $lang['datatable.sortDesc'] ?>",first:"<?php echo $lang['datatable.first'] ?>",last:"<?php echo $lang['datatable.last'] ?>",next:"<?php echo $lang['datatable.next'] ?>",prev:"<?php echo $lang['datatable.prev'] ?>",nodata:"<?php echo $lang['datatable.noData'] ?>",info:"<?php echo $lang['datatable.info'] ?>",noInfo:"<?php echo $lang['datatable.noInfo'] ?>",filtered:"<?php echo $lang['datatable.filtered'] ?>",show:"<?php echo $lang['datatable.show'] ?>",loading:"<?php echo $lang['datatable.loading'] ?>",procesing:"<?php echo $lang['datatable.procesing'] ?>",search:"<?php echo $lang['datatable.search'] ?>",searchPlaceholder:"<?php echo $lang['datatable.searchPlaceholder'] ?>",noRecords:"<?php echo $lang['datatable.noRecords'] ?>"};
</script>
<script src="<?php echo URL ?>/style/js/jquery.min.js"></script>
<script src="<?php echo URL ?>/style/js/jquery.slimscroll.js"></script>
<script src="<?php echo URL ?>/style/js/scripts.min.js"></script>
<script src="<?php echo URL ?>/style/js/bootstrap.min.js"></script>
<script src="<?php echo URL ?>/style/js/jquery.toast.js"></script>
<script src="<?php echo URL ?>/style/js/sidebar-nav.min.js"></script>
<script src="<?php echo URL ?>/style/js/waves.js"></script>
<?php echo $pageScripts;

if($config['web.php.time.track'] == true && !empty($_SERVER['REQUEST_TIME_FLOAT']))
{
	$time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
    echo '<!-- Process Time: '.$time.'-->';
}
?>