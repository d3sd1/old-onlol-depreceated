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