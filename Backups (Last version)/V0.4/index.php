<?php
include('kernel/core.php');
$pageName = 'title.index'; //Lang key
$pageNameVarKey = null; 
$pageNameVarVal = null; 
$pageTemplates = '<link href="'.URL.'/style/css/morris.css" rel="stylesheet">'; // CSS Scripts to load
$pageScripts = '<script src="'.URL.'/style/js/index/jquery.waypoints.js"></script>
<script src="'.URL.'/style/js/index/jquery.counterup.min.js"></script>
<script src="'.URL.'/style/js/index/raphael-min.js"></script>
<script src="'.URL.'/style/js/index/morris.js"></script>
<script src="'.URL.'/style/js/index/jquery.sparkline.min.js"></script>
<script src="'.URL.'/style/js/index/jquery.charts-sparkline.js"></script>'; // JS Scripts to load
require('kernel/template/header.tpl');

$indexData = json_decode(file_get_contents(WEB_BASEDIR . '/' . DATABASE_PATH_STATS . '/stats_indexpage.json'),true);
?>

  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="row bg-title">
      
      </div>
	   <script>
		var searchnotfound = 'NOT_FOUND';
		var search_region = "<?php echo strtoupper($userRegion) ?>";
		function switch_search_region_page(region)
		{
			search_region = region;
			document.getElementById('search_region_pag').text = region;
		}
		function searchsummoner_page() {
			var summoner = document.getElementById('summonerPage').value;
			if(summoner != "")
			{
				window.location = "<?php echo URL ?>/summoner/" + search_region.toLowerCase() + "/" + summoner + "&searched=true";
			}
			else
			{
				$(document).ready(function() {
					   $.toast({
						heading: '<?php echo $lang['menu.search.error.input'] ?>',
						text: '<?php echo $lang['menu.search.error.input.dsc'] ?>',
						position: 'top-right',
						loaderBg:'#ff6849',
						icon: 'error',
						hideAfter: 5000, 
						stack: 6
					  });
				 

			});
			}
		}
		</script>
      <!-- .row -->
      <div class="row">
        <div class="col-lg-6 col-sm-12 col-xs-12">
          <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
              <div class="white-box" style="background: rgba(255,255,255,0.86) !important;">
                <h1 class="box-title"><?php echo $lang['index.searchSummoner'] ?></h1>
                <div class="input-group m-t-10">
                      <div class="input-group-btn">
                        <button type="button" class="btn waves-effect waves-light btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><a style="color: #fff !important;" id="search_region_pag"><?php echo strtoupper($userRegion) ?></a> <span class="caret"></span></button>
                        <ul class="dropdown-menu">
						<?php
						  foreach($servers as $region => $platform)
						  {
							  echo '<li>
							  <div class="message-center"> <a href="javascript:switch_search_region_page(\''.strtoupper($region).'\')">
								<div class="mail-contnet">
								  <h5>'.strtoupper($region).'</h5>
								  <p class="mail-desc">'.$lang['server.name_'.$region].'</p> </div>
								</a> </div>
							</li>';
						  }
						  ?>
                        </ul>
                      </div>
					  <form action="javascript:searchsummoner_page()">
                      <input autocomplete="off" type="text" id="summonerPage" class="form-control" value="<?php echo @$_SESSION['onlol_lastSearch'] ?>" placeholder="<?php echo $lang['menu.search.placeholder'] ?>" >
					  </form>
                      <div class="input-group-btn">
                        <a onclick="javascript:searchsummoner_page()"><button type="button" class="btn waves-effect waves-light btn-info dropdown-toggle" data-toggle="dropdown"><?php echo $lang['index.search.button'] ?></button></a>
                      </div>
                </div>
				<br>
              </div>
            </div>
            <div class="col-lg-6 col-sm-6 col-xs-12">
              <div class="white-box" style="background: rgba(255,255,255,0.86) !important;">
                <h3 class="box-title"><?php echo $lang['index.webstatus'] ?></h3>
                <ul class="list-inline two-part">
                  <li><i class="fa fa-power-off text-danger"></i></li>
                  <li class="text-right"><span class=""><?php if($db->query('SELECT status FROM web_status WHERE status="disabled"')->num_rows > 0){ echo $lang['index.webstatus.off']; } elseif($db->query('SELECT status FROM web_status WHERE status="updating"')->num_rows > 0) {echo $lang['index.webstatus.updating'];} else {echo $lang['index.webstatus.on'];}?></span></li>
                </ul>
              </div>
            </div>
            <div class="col-lg-6 col-sm-6 col-xs-12">
              <div class="white-box" style="background: rgba(255,255,255,0.86) !important;">
                <h3 class="box-title"><?php echo $lang['index.lolpatch'] ?></h3>
                <ul class="list-inline two-part">
                  <li><i class="fa fa-code-fork text-success"></i></li>
                  <li class="text-right"><span class=""><?php echo LOL_PATCH ?></span></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-sm-12 col-xs-12">
          <div class="news-slide m-b-15">
            <div class="vcarousel slide">
              <!-- Carousel items -->
              <div class="carousel-inner">
			  <?php
			  $news = $db->query('SELECT id,title,image,cat FROM web_news WHERE lang="'.$userLang.'"');
			  $i = 0;
			  while($row = $news->fetch_row())
			  {
				  if($i == 0)
				  {
					  $status = 'active ';
				  }
				  else
				  {
					  $status = null;
				  }
				  echo '<div class="'.$status.'item">
                  <div class="overlaybg"><img src="'.URL.'/style/images/news/'.$row[2].'"/></div>
                  <div class="news-content"><span class="label label-danger label-rounded">'.$lang['index.news.cat.'.$row[3]].'</span>
                    <h2>'.$row[1].'</h2>
                    <a href="'.URL.'/news/'.$row[0].'">'.$lang['index.news.readmore'].'</a></div>
                </div>';
			  }
			  ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->
      <!-- .row -->
      <div class="row">
        <div class="col-md-12">
          <div class="white-box" style="background: rgba(255,255,255,0.86) !important;">
            <div class="row">
              <div class="col-lg-3 col-sm-3 col-xs-12 text-center"> <small><?php echo $lang['index.summonersondb'] ?></small>
                <h2><i class="ti-arrow-up text-warning"></i> <?php echo $db->query('SELECT id FROM api_summoners')->num_rows ?></h2>
                <div id="sparklinedash"></div>
              </div>
              <div class="col-lg-3 col-sm-3 col-xs-12 text-center"> <small><?php echo $lang['index.matchesondb'] ?></small>
                <h2><i class="ti-arrow-up text-warning"></i> <?php echo $db->query('SELECT id FROM api_matches')->num_rows ?></h2>
                <div id="sparklinedash2"></div>
              </div>
              <div class="col-lg-3 col-sm-3 col-xs-12 text-center"> <small><?php echo $lang['index.bluesidewr'] ?></small>
                <h2><i class="ti-arrow-<?php if($indexData['blueSideWR'] == 50) { echo 'right text-info'; } elseif($indexData['blueSideWR'] > 50) { echo 'up text-success'; } elseif($indexData['blueSideWR'] < 50) { echo 'down text-danger'; } ?>"></i> <?php echo $indexData['blueSideWR'] ?>%</h2>
                <div id="sparklinedash3"></div>
              </div>
              <div class="col-lg-3 col-sm-3 col-xs-12 text-center"> <small><?php echo $lang['index.redsidewr'] ?></small>
                <h2><i class="ti-arrow-<?php if($indexData['redSideWR'] == 50) { echo 'right text-info'; } elseif($indexData['redSideWR'] > 50) { echo 'up text-success'; } elseif($indexData['redSideWR'] < 50) { echo 'down text-danger'; } ?>"></i> <?php echo $indexData['redSideWR'] ?>%</h2>
                <div id="sparklinedash4"></div>
              </div>
            </div>
            <ul class="list-inline text-center">
              <li>
                <h5><i class="fa fa-circle m-r-5" style="color: #00bfc7;"></i><?php echo $lang['index.chart.soloq'] ?></h5>
              </li>
              <li>
                <h5><i class="fa fa-circle m-r-5" style="color: #fdc006;"></i><?php echo $lang['index.chart.teamsq'] ?></h5>
              </li>
              <li>
                <h5><i class="fa fa-circle m-r-5" style="color: #9675ce;"></i><?php echo $lang['index.chart.normals'] ?></h5>
              </li>
            </ul>
            <div id="morris-area-chart" style="height: 340px;"></div>
          </div>
        </div>
      </div>
    </div>
    <footer class="footer text-center"> <?php echo date('Y'); ?>  <?php echo $lang['footer.copy'] ?> </footer>
  </div>
</div>

<?php
require('kernel/template/scripts.tpl');
?>
<script>
$(".counter").counterUp({
        delay: 100,
        time: 1200
    });

 $('.vcarousel').carousel({
            interval: 3000
         })
 $(document).ready(function() {
    
   var sparklineLogin = function() { 
        $('#sparklinedash').sparkline([ <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>], {
            type: 'bar',
            height: '30',
            barWidth: '4',
            resize: true,
            barSpacing: '10',
            barColor: '#4caf50'
        });
         $('#sparklinedash2').sparkline([ <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>], {
            type: 'bar',
            height: '30',
            barWidth: '4',
            resize: true,
            barSpacing: '10',
            barColor: '#9675ce'
        });
          $('#sparklinedash3').sparkline([ <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>], {
            type: 'bar',
            height: '30',
            barWidth: '4',
            resize: true,
            barSpacing: '10',
            barColor: '#03a9f3'
        });
           $('#sparklinedash4').sparkline([ <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>, <?php echo rand(1,15) ?>], {
            type: 'bar',
            height: '30',
            barWidth: '4',
            resize: true,
            barSpacing: '10',
            barColor: '#f96262'
        });
        
   }
    var sparkResize;
 
        $(window).resize(function(e) {
            clearTimeout(sparkResize);
            sparkResize = setTimeout(sparklineLogin, 500);
        });
        sparklineLogin();

});
Morris.Area({
        element: 'morris-area-chart',
        data: [<?php
		$i = 0;
		foreach($indexData['charts'] as $date => $stats)
		{
			$date = explode('-',$date);
			$date = $date[1].' '.$lang['index.chart.of'].' '.$lang['index.chart.month.'.$date[0]];
			if(count($indexData['charts']) == ($i+1))
			{
				echo '{
            period: \''.$lang['index.chart.today'].'\',
            soloq: '.$stats['soloqGames'].',
            teamsq: '.$stats['teamsqGames'].',
            normals: '.$stats['normalGames'].'
        }';
			}
			elseif($i == $config['index.chart.limitdays'])
			{
				echo '{
            period: \''.$lang['index.chart.today'].'\',
            soloq: '.$stats['soloqGames'].',
            teamsq: '.$stats['teamsqGames'].',
            normals: '.$stats['normalGames'].'
        }';
			}
			elseif($i < $config['index.chart.limitdays'])
			{
				echo '{
            period: \''.$date.'\',
            soloq: '.$stats['soloqGames'].',
            teamsq: '.$stats['teamsqGames'].',
            normals: '.$stats['normalGames'].'
        },';
			}
			$i++;
		}
		?>],
        xkey: 'period',
        ykeys: ['soloq', 'teamsq', 'normals'],
        labels: ['<?php echo $lang['index.chart.soloq'] ?>', '<?php echo $lang['index.chart.teamsq'] ?>', '<?php echo $lang['index.chart.normals'] ?>'],
        pointSize: 3,
        fillOpacity: 0,
        pointStrokeColors:['#00bfc7', '#fdc006', '#9675ce'],
        behaveLikeLine: true,
        gridLineColor: '#e0e0e0',
        lineWidth: 1,
        hideHover: 'auto',
        lineColors: ['#00bfc7', '#fdc006', '#9675ce'],
        resize: true,
		parseTime: false
        
    });

$(document).ready(function() {
    
   var sparklineLogin = function() { 
        $('#sales1').sparkline([20, 40, 30], {
            type: 'pie',
            height: '100',
            resize: true,
            sliceColors: ['#808f8f', '#fecd36', '#f1f2f7']
        });
        $('#sales2').sparkline([6, 10, 9, 11, 9, 10, 12], {
            type: 'bar',
            height: '154',
            barWidth: '4',
            resize: true,
            barSpacing: '10',
            barColor: '#25a6f7'
        });
        
   }
    var sparkResize;
 
        $(window).resize(function(e) {
            clearTimeout(sparkResize);
            sparkResize = setTimeout(sparklineLogin, 500);
        });
        sparklineLogin();

});
</script>
<?php
if(!empty($_GET['error']))
{
	if($_GET['error'] == 'summonernotfound')
	{
		echo '<script>$(document).ready(function() {
					   $.toast({
						heading: \''.$lang['index.error.summonernotfoundtitle'].'\',
						text: \''.$lang['index.error.summonernotfound'].'\',
						position: \'top-right\',
						loaderBg:\'#ff6849\',
						icon: \'error\',
						hideAfter: 5000, 
						stack: 6
					  });});</script>';
		}
}
?>
</body>
</html>
