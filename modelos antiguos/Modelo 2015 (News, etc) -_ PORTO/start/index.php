<?php
require('../kernel/core.php');
/* Skip intro */
if(!empty($_SESSION['onlol_inactivity_disabled']))
{
	onlol::redirect(URL.'/home',true);
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<!-- START @HEAD -->
<head>
	<title><?php echo lang::trans('inactivity_title'); ?></title>
	<?php echo template::meta(); ?>
	<!-- START @FAVICONS -->
	<link href="<?php echo URL ?>/style/favicons/144x144.png" rel="apple-touch-icon-precomposed" sizes="144x144">
	<link href="<?php echo URL ?>/style/favicons/114x114.png" rel="apple-touch-icon-precomposed" sizes="114x114">
	<link href="<?php echo URL ?>/style/favicons/72x72.png" rel="apple-touch-icon-precomposed" sizes="72x72">
	<link href="<?php echo URL ?>/style/favicons/57x57.png" rel="apple-touch-icon-precomposed">
	<link href="<?php echo URL ?>/style/favicons/favicon.png" rel="shortcut icon">
	<!--/ END FAVICONS -->

	<!-- START @FONT STYLES -->
	<link href="http://fonts.googleapis.com/css?family=Raleway:300,900,100,600,400,500,800" rel="stylesheet" property="stylesheet" type="text/css" media="all" />
	<!--/ END FONT STYLES -->

	<!-- START @PAGE LEVEL STYLES -->
	<link href="<?php echo URL ?>/style/index/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" rel="stylesheet">
	<link href="<?php echo URL ?>/style/index/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">

	<!-- REVOLUTION STYLE SHEETS -->
	<link href="<?php echo URL ?>/style/index/css/settings.css" rel="stylesheet">
	<link href="<?php echo URL ?>/style/global/css/cursor.css" rel="stylesheet">
	<!-- REVOLUTION LAYERS STYLES -->
	<link href="<?php echo URL ?>/style/index/css/layers.css" rel="stylesheet">

	<!-- REVOLUTION NAVIGATION STYLES -->
	<link href="<?php echo URL ?>/style/index/css/navigation.css" rel="stylesheet">
	<!--/ END PAGE LEVEL STYLES -->
</head>

		<!-- SLIDER EXAMPLE -->
		<section class="example" style="margin-top:-0.4%;">
			<article class="content">			
				<div id="rev_slider_46_1_wrapper" class="rev_slider_wrapper fullscreen-container" data-alias="notgeneric1" style="background-color:transparent;padding:0px;">
				<!-- START REVOLUTION SLIDER 5.0.7 fullscreen mode -->
					<div id="rev_slider_46_1" class="rev_slider fullscreenbanner" style="display:none;" data-version="5.0.7">
						<ul>	<!-- SLIDE  -->
							<li data-index="rs-148" data-transition="zoomout" data-slotamount="default"  data-easein="Power4.easeInOut" data-easeout="Power4.easeInOut" data-masterspeed="2000"  data-rotate="0"  data-fstransition="fade" data-fsmasterspeed="1500" data-fsslotamount="7" data-saveperformance="off"  data-title="Intro" data-description="">
								<!-- MAIN IMAGE -->
								<img src="<?php echo URL ?>/style/index/images/slider_1.jpg"  alt="..."  data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="10" class="rev-slidebg" data-no-retina>
								<!-- LAYERS -->

								<!-- LAYER NR. 1 -->
								<div class="tp-caption NotGeneric-Title   tp-resizeme rs-parallaxlevel-0" 
									id="slide-148-layer-1" 
									data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']" 
									data-fontsize="['70','70','70','45']"
									data-lineheight="['70','70','70','50']"
									data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
									data-transform_in="x:[105%];z:0;rX:45deg;rY:0deg;rZ:90deg;sX:1;sY:1;skX:0;skY:0;s:2000;e:Power4.easeInOut;" 
									data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									data-mask_in="x:0px;y:0px;s:inherit;e:inherit;" 
									data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="1000" 
									data-splitin="chars" 
									data-splitout="none" 
									data-responsive_offset="on" 
									data-delay="2000"
									data-elementdelay="0.05" 
									
									style="z-index: 5; white-space: nowrap;"><?php echo lang::trans('start_slider_title') ?>
								</div>

								<!-- LAYER NR. 2 -->
								<div class="tp-caption NotGeneric-SubTitle   tp-resizeme rs-parallaxlevel-0" 
									 id="slide-148-layer-4" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['52','52','52','51']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
						 
									 data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" 
									 data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
									 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="1500" 
									data-splitin="none" 
									data-splitout="none" 
									data-responsive_offset="on" 

									
									style="z-index: 6; white-space: nowrap;"><?php echo lang::trans('start_slider_subtitle') ?>
								</div>

								<!-- LAYER NR. 3 -->
								<a href="<?php echo URL ?>/home/"><div class="tp-caption NotGeneric-Icon   tp-resizeme rs-parallaxlevel-0" 
									 id="slide-148-layer-8" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']"
									 data-y="['middle','middle','middle','middle']" data-voffset="['-100','-100','-100','-100']"
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
										data-style_hover=""
						 
									 data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:1500;e:Power4.easeInOut;" 
									 data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
									 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="2000" 
									data-splitin="none" 
									data-splitout="none" 
									data-responsive_offset="on" 

									
									style="z-index: 7; white-space: nowrap;"><img src="<?php echo URL ?>/style/logo/120x120.png" alt="logo"/>
								</div></a>

								<!-- LAYER NR. 4 -->
								<a href="<?php echo URL ?>/home/"><div class="tp-caption NotGeneric-Button rev-btn  rs-parallaxlevel-0" 
									 id="slide-148-layer-7" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['124','124','124','123']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
										data-transform_hover="o:1;rX:0;rY:0;rZ:0;z:0;s:300;e:Power1.easeInOut;"
										data-style_hover="c:rgba(255, 255, 255, 1.00);bc:rgba(255, 255, 255, 1.00);"
						 
									 data-transform_in="y:50px;opacity:0;s:1500;e:Power4.easeInOut;" 
									 data-transform_out="y:[175%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_out="x:inherit;y:inherit;" 
									data-start="2000" 
									data-splitin="none" 
									data-splitout="none" 
									data-responsive_offset="on" 
									data-responsive="off"
									
									style="z-index: 8; white-space: nowrap;outline:none;box-shadow:none;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;"><?php echo lang::trans('start_slider_button') ?>
								</div></a>

								<!-- LAYER NR. 5 -->
								<div class="tp-caption rev-scroll-btn  rs-parallaxlevel-0" 
									 id="slide-148-layer-9" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['bottom','bottom','bottom','bottom']" data-voffset="['50','50','50','50']" 
												data-width="35"
									data-height="55"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
										data-style_hover=""
						 
									 data-transform_in="y:50px;opacity:0;s:1500;e:Power3.easeInOut;" 
									 data-transform_out="y:50px;opacity:0;s:1000;s:1000;" 
									data-start="2000" 
									data-splitin="none" 
									data-splitout="none" 
									data-actions='[{"event":"click","action":"scrollbelow","offset":"0px"}]'
									data-basealign="slide" 
									data-responsive_offset="off" 
									data-responsive="off"
									
									style="z-index: 9; min-width: 35px; max-width: 35px; max-width: 55px; max-width: 55px; white-space: nowrap; font-size: px; line-height: px; font-weight: 100;border-color:rgba(255, 255, 255, 0.50);border-style:solid;border-width:1px;border-radius:23px 23px 23px 23px;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;">							
													<span>
													</span>							
												 
								</div>
							</li>
							<!-- SLIDE  -->
							<li data-index="rs-149" data-transition="fadetotopfadefrombottom" data-slotamount="default"  data-easein="Power3.easeInOut" data-easeout="Power3.easeInOut" data-masterspeed="1500"  data-thumb="/img/frontend-themes/one-page-revolution-slider/not-generic/paralax/1-100x50.jpg"  data-rotate="0"  data-saveperformance="off"  data-title="Chill" data-description="">
								<!-- MAIN IMAGE -->
								<img src="<?php echo URL ?>/style/index/images/slider_2.jpg"  alt="..."  data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="10" class="rev-slidebg" data-no-retina>
								<!-- LAYERS -->

								<!-- LAYER NR. 1 -->
								<div class="tp-caption NotGeneric-Title   tp-resizeme rs-parallaxlevel-3" 
									 id="slide-149-layer-1" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']" 
												data-fontsize="['70','70','70','45']"
									data-lineheight="['70','70','70','50']"
									data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
						 
									 data-transform_in="y:[100%];z:0;rZ:-35deg;sX:1;sY:1;skX:0;skY:0;s:2000;e:Power4.easeInOut;" 
									 data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_in="x:0px;y:0px;s:inherit;e:inherit;" 
									 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="1000" 
									data-splitin="chars" 
									data-splitout="none" 
									data-responsive_offset="on" 

									data-elementdelay="0.05" 
									
									style="z-index: 5; white-space: nowrap;"><?php echo lang::trans('start_slider_stats'); ?> 
								</div>

								<!-- LAYER NR. 2 -->
								<div class="tp-caption NotGeneric-SubTitle   tp-resizeme rs-parallaxlevel-2" 
									 id="slide-149-layer-4" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['52','52','52','51']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
						 
									 data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" 
									 data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
									 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="1500" 
									data-splitin="none" 
									data-splitout="none" 
									data-responsive_offset="on" 

									
									style="z-index: 6; white-space: nowrap;"><?php echo lang::trans('start_slider_stats_sub'); ?> 
								</div>

								<!-- LAYER NR. 3 -->
								<div class="tp-caption NotGeneric-Icon   tp-resizeme rs-parallaxlevel-1" 
									 id="slide-149-layer-8" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['-68','-68','-68','-68']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
										data-style_hover=""
						 
									 data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:1500;e:Power4.easeInOut;" 
									 data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
									 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="2000" 
									data-splitin="none" 
									data-splitout="none" 
									data-responsive_offset="on" 

									
									style="z-index: 7; white-space: nowrap;"><i class="pe-7s-mouse"></i> 
								</div>

								<!-- LAYER NR. 4 -->
								<div class="tp-caption NotGeneric-Button rev-btn  rs-parallaxlevel-0" 
									 id="slide-149-layer-7" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['124','124','124','123']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
										data-transform_hover="o:1;rX:0;rY:0;rZ:0;z:0;s:300;e:Power1.easeInOut;"
										data-style_hover="c:rgba(255, 255, 255, 1.00);bc:rgba(255, 255, 255, 1.00);"
						 
									 data-transform_in="y:50px;opacity:0;s:1500;e:Power4.easeInOut;" 
									 data-transform_out="y:[175%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_out="x:inherit;y:inherit;" 
									data-start="2000" 
									data-splitin="none" 
									data-splitout="none" 
									data-actions='[{"event":"click","action":"jumptoslide","slide":"next","delay":""}]'
									data-responsive_offset="on" 
									data-responsive="off"
									
									style="z-index: 8; white-space: nowrap;outline:none;box-shadow:none;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;"><?php echo lang::trans('start_slider_button'); ?> 
								</div>

								<!-- LAYER NR. 5 -->
								<div class="tp-caption rev-scroll-btn  rs-parallaxlevel-0" 
									 id="slide-149-layer-9" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['bottom','bottom','bottom','bottom']" data-voffset="['50','50','50','50']" 
												data-width="35"
									data-height="55"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
										data-style_hover=""
						 
									 data-transform_in="y:50px;opacity:0;s:1500;e:Power3.easeInOut;" 
									 data-transform_out="y:50px;opacity:0;s:1000;s:1000;" 
									data-start="2000" 
									data-splitin="none" 
									data-splitout="none" 
									data-actions='[{"event":"click","action":"scrollbelow","offset":"0px"}]'
									data-basealign="slide" 
									data-responsive_offset="off" 
									data-responsive="off"
									
									style="z-index: 9; min-width: 35px; max-width: 35px; max-width: 55px; max-width: 55px; white-space: nowrap; font-size: px; line-height: px; font-weight: 100;border-color:rgba(255, 255, 255, 0.50);border-style:solid;border-width:1px;border-radius:23px 23px 23px 23px;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;">							
													<span>
													</span>							
												 
								</div>

								<!-- LAYER NR. 6 -->
								<div class="tp-caption   tp-resizeme rs-parallaxlevel-8" 
									 id="slide-149-layer-10" 
									 data-x="['left','left','left','left']" data-hoffset="['680','680','680','680']" 
									 data-y="['top','top','top','top']" data-voffset="['632','632','632','632']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
						 
									 data-transform_in="opacity:0;s:1000;e:Power2.easeInOut;" 
									 data-transform_out="opacity:0;s:1000;s:1000;" 
									data-start="2000" 
									data-responsive_offset="on" 

									
									style="z-index: 10;">
										<div class="rs-looped rs-pendulum"  data-easing="linearEaseNone" data-startdeg="-20" data-enddeg="360" data-speed="35" data-origin="50% 50%"><img src="<?php echo URL ?>/style/index/images/blur_4.png" alt="..." width="240" height="240" data-ww="['240px','240px','240px','240px']" data-hh="['240px','240px','240px','240px']" data-no-retina>
										</div>
								</div>

								<!-- LAYER NR. 7 -->
								<div class="tp-caption   tp-resizeme rs-parallaxlevel-7" 
									 id="slide-149-layer-11" 
									 data-x="['left','left','left','left']" data-hoffset="['948','948','948','948']" 
									 data-y="['top','top','top','top']" data-voffset="['487','487','487','487']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
						 
									 data-transform_in="opacity:0;s:1000;e:Power2.easeInOut;" 
									 data-transform_out="opacity:0;s:1000;s:1000;" 
									data-start="2000" 
									data-responsive_offset="on" 

									
									style="z-index: 11;">
										<div class="rs-looped rs-wave"  data-speed="20" data-angle="0" data-radius="50px" data-origin="50% 50%"><img src="<?php echo URL ?>/style/index/images/blur_3.png" alt="..." width="170" height="170" data-ww="['170px','170px','170px','170px']" data-hh="['170px','170px','170px','170px']" data-no-retina>
										</div>
								</div>

								<!-- LAYER NR. 8 -->
								<div class="tp-caption   tp-resizeme rs-parallaxlevel-4" 
									 id="slide-149-layer-12" 
									 data-x="['left','left','left','left']" data-hoffset="['719','719','719','719']" 
									 data-y="['top','top','top','top']" data-voffset="['200','200','200','200']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
						 
									 data-transform_in="opacity:0;s:1000;e:Power2.easeInOut;" 
									 data-transform_out="opacity:0;s:1000;s:1000;" 
									data-start="2000" 
									data-responsive_offset="on" 

									
									style="z-index: 12;">
										<div class="rs-looped rs-rotate"  data-easing="Power2.easeInOut" data-startdeg="-20" data-enddeg="360" data-speed="20" data-origin="50% 50%"><img src="<?php echo URL ?>/style/index/images/blur_2.png" alt="..." width="50" height="51" data-ww="['50px','50px','50px','50px']" data-hh="['51px','51px','51px','51px']" data-no-retina>
										</div>
								</div>

								<!-- LAYER NR. 9 -->
								<div class="tp-caption   tp-resizeme rs-parallaxlevel-6" 
									 id="slide-149-layer-13" 
									 data-x="['left','left','left','left']" data-hoffset="['187','187','187','187']" 
									 data-y="['top','top','top','top']" data-voffset="['216','216','216','216']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
						 
									 data-transform_in="opacity:0;s:1000;e:Power2.easeInOut;" 
									 data-transform_out="opacity:0;s:1000;s:1000;" 
									data-start="2000" 
									data-responsive_offset="on" 

									
									style="z-index: 13;">
										<div class="rs-looped rs-wave"  data-speed="4" data-angle="0" data-radius="10" data-origin="50% 50%"><img src="<?php echo URL ?>/style/index/images/blur_1.png" alt="..." width="120" height="120" data-ww="['120px','120px','120px','120px']" data-hh="['120px','120px','120px','120px']" data-no-retina>
										</div>
								</div>
							</li>
							<!-- SLIDE  -->
							<li data-index="rs-150" data-transition="zoomin" data-slotamount="7"  data-easein="Power4.easeInOut" data-easeout="Power4.easeInOut" data-masterspeed="2000"  data-thumb="/img/frontend-themes/one-page-revolution-slider/not-generic/ken-burns/1-100x50.jpg"  data-rotate="0"  data-saveperformance="off"  data-title="Enjoy Nature" data-description="">
								<!-- MAIN IMAGE -->
								<img src="<?php echo URL ?>/style/index/images/slider_3.jpg"  alt="..."  data-bgposition="center center" data-kenburns="on" data-duration="30000" data-ease="Linear.easeNone" data-scalestart="100" data-scaleend="120" data-rotatestart="0" data-rotateend="0" data-offsetstart="0 0" data-offsetend="0 0" data-bgparallax="10" class="rev-slidebg" data-no-retina>
								<!-- LAYERS -->

								<!-- LAYER NR. 1 -->
								<div class="tp-caption NotGeneric-Title   tp-resizeme rs-parallaxlevel-0" 
									 id="slide-150-layer-1" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']" 
												data-fontsize="['70','70','70','45']"
									data-lineheight="['70','70','70','50']"
									data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
						 
									 data-transform_in="y:[-100%];z:0;rZ:35deg;sX:1;sY:1;skX:0;skY:0;s:2000;e:Power4.easeInOut;" 
									 data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_in="x:0px;y:0px;s:inherit;e:inherit;" 
									 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="1000" 
									data-splitin="chars" 
									data-splitout="none" 
									data-responsive_offset="on" 

									data-elementdelay="0.05" 
									
									style="z-index: 5; white-space: nowrap;"><?php echo lang::trans('start_slider_search') ?>
								</div>

								<!-- LAYER NR. 2 -->
								<div class="tp-caption NotGeneric-SubTitle   tp-resizeme rs-parallaxlevel-0" 
									 id="slide-150-layer-4" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['52','52','52','51']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
						 
									 data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" 
									 data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
									 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="1500" 
									data-splitin="none" 
									data-splitout="none" 
									data-responsive_offset="on" 

									
									style="z-index: 6; white-space: nowrap;"><?php echo lang::trans('start_slider_search_sub') ?>
								</div>

								<!-- LAYER NR. 3 -->
								<div class="tp-caption NotGeneric-Icon   tp-resizeme rs-parallaxlevel-0" 
									 id="slide-150-layer-8" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['-68','-68','-68','-68']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
										data-style_hover=""
						 
									 data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:1500;e:Power4.easeInOut;" 
									 data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
									 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="2000" 
									data-splitin="none" 
									data-splitout="none" 
									data-responsive_offset="on" 

									
									style="z-index: 7; white-space: nowrap;"><i class="pe-7s-expand1"></i> 
								</div>

								<!-- LAYER NR. 4 -->
								<a href="<?php echo URL ?>/home/"><div class="tp-caption NotGeneric-Button rev-btn  rs-parallaxlevel-0" 
									 id="slide-150-layer-7" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['124','124','124','123']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
										data-transform_hover="o:1;rX:0;rY:0;rZ:0;z:0;s:300;e:Power1.easeInOut;"
										data-style_hover="c:rgba(255, 255, 255, 1.00);bc:rgba(255, 255, 255, 1.00);"
						 
									 data-transform_in="y:50px;opacity:0;s:1500;e:Power4.easeInOut;" 
									 data-transform_out="y:[175%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_out="x:inherit;y:inherit;" 
									data-start="2000" 
									data-splitin="none" 
									data-splitout="none" 
									data-actions='[{"event":"click","action":"jumptoslide","slide":"next","delay":""}]'
									data-responsive_offset="on" 
									data-responsive="off"
									
									style="z-index: 8; white-space: nowrap;outline:none;box-shadow:none;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;"><?php echo lang::trans('start_slider_button') ?>
								</div></a>

								<!-- LAYER NR. 5 -->
								<div class="tp-caption rev-scroll-btn  rs-parallaxlevel-0" 
									 id="slide-150-layer-9" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['bottom','bottom','bottom','bottom']" data-voffset="['50','50','50','50']" 
												data-width="35"
									data-height="55"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
										data-style_hover=""
						 
									 data-transform_in="y:50px;opacity:0;s:1500;e:Power3.easeInOut;" 
									 data-transform_out="y:50px;opacity:0;s:1000;s:1000;" 
									data-start="2000" 
									data-splitin="none" 
									data-splitout="none" 
									data-actions='[{"event":"click","action":"scrollbelow","offset":"0px"}]'
									data-basealign="slide" 
									data-responsive_offset="off" 
									data-responsive="off"
									
									style="z-index: 9; min-width: 35px; max-width: 35px; max-width: 55px; max-width: 55px; white-space: nowrap; font-size: px; line-height: px; font-weight: 100;border-color:rgba(255, 255, 255, 0.50);border-style:solid;border-width:1px;border-radius:23px 23px 23px 23px;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;">							
													<span>
													</span>							
												 
								</div>
							</li>
							<!-- SLIDE  -->
							<li data-index="rs-151" data-transition="zoomout" data-slotamount="default"  data-easein="Power4.easeInOut" data-easeout="Power4.easeInOut" data-masterspeed="2000"  data-thumb="/img/frontend-themes/one-page-revolution-slider/not-generic/video/1-100x50.jpg"  data-rotate="0"  data-saveperformance="off"  data-title="Iceberg" data-description="">
								<!-- MAIN IMAGE -->
								<img src="<?php echo URL ?>/style/index/images/slider_4.jpg"  alt="..."  data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="10" class="rev-slidebg" data-no-retina>
								<!-- LAYERS -->

								<!-- BACKGROUND VIDEO LAYER -->
								<div class="rs-background-video-layer" 
									data-forcerewind="on" 
									data-volume="mute" 
									data-videowidth="100%" 
									data-videoheight="100%" 
									data-videomp4="<?php echo URL ?>/style/index/video/cinematic.mp4"
									data-videopreload="preload" 
									data-videoloop="loopandnoslidestop" 
									data-forceCover="1" 
									data-aspectratio="16:9" 
									data-autoplay="true" 
									data-autoplayonlyfirsttime="false" 
									data-nextslideatend="true" 
								></div>
								<!-- LAYER NR. 1 -->
								<div class="tp-caption tp-shape tp-shapewrapper   rs-parallaxlevel-0" 
									 id="slide-151-layer-10" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']" 
												data-width="full"
									data-height="full"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
						 
									 data-transform_in="opacity:0;s:2000;e:Power3.easeInOut;" 
									 data-transform_out="opacity:0;s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									data-start="2000" 
									data-basealign="slide" 
									data-responsive_offset="on" 
									data-responsive="off"
									
									style="z-index: 5;background-color:rgba(0, 0, 0, 0.25);border-color:rgba(0, 0, 0, 0);"> 
								</div>

								<!-- LAYER NR. 2 -->
								<div class="tp-caption NotGeneric-Title   tp-resizeme rs-parallaxlevel-0" 
									 id="slide-151-layer-1" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']" 
												data-fontsize="['70','70','70','45']"
									data-lineheight="['70','70','70','50']"
									data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
						 
									 data-transform_in="z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;s:1500;e:Power3.easeInOut;" 
									 data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="1000" 
									data-splitin="chars" 
									data-splitout="none" 
									data-responsive_offset="on" 

									data-elementdelay="0.05" 
									
									style="z-index: 6; white-space: nowrap;"><?php echo lang::trans('start_slider_wiki') ?>
								</div>

								<!-- LAYER NR. 3 -->
								<div class="tp-caption NotGeneric-SubTitle   tp-resizeme rs-parallaxlevel-0" 
									 id="slide-151-layer-4" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['52','52','52','51']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
						 
									 data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" 
									 data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
									 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="1500" 
									data-splitin="none" 
									data-splitout="none" 
									data-responsive_offset="on" 

									
									style="z-index: 7; white-space: nowrap;"><?php echo lang::trans('start_slider_wiki_sub') ?>
								</div>

								<!-- LAYER NR. 4 -->
								<div class="tp-caption NotGeneric-Icon   tp-resizeme rs-parallaxlevel-0" 
									 id="slide-151-layer-8" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['-68','-68','-68','-68']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
										data-style_hover=""
						 
									 data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:1500;e:Power4.easeInOut;" 
									 data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" 
									 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="2000" 
									data-splitin="none" 
									data-splitout="none" 
									data-responsive_offset="on" 

									
									style="z-index: 8; white-space: nowrap;"><i class="pe-7s-anchor"></i> 
								</div>

								<!-- LAYER NR. 5 -->
								<a href="<?php echo URL ?>/home/"><div class="tp-caption NotGeneric-Button rev-btn  rs-parallaxlevel-0" 
									 id="slide-151-layer-7" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['middle','middle','middle','middle']" data-voffset="['124','124','124','123']" 
												data-width="none"
									data-height="none"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
										data-transform_hover="o:1;rX:0;rY:0;rZ:0;z:0;s:300;e:Power1.easeInOut;"
										data-style_hover="c:rgba(255, 255, 255, 1.00);bc:rgba(255, 255, 255, 1.00);"
						 
									 data-transform_in="y:50px;opacity:0;s:1500;e:Power4.easeInOut;" 
									 data-transform_out="y:[175%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" 
									 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" 
									data-start="2000" 
									data-splitin="none" 
									data-splitout="none" 
									data-responsive_offset="on" 
									data-responsive="off"
									
									style="z-index: 9; white-space: nowrap;outline:none;box-shadow:none;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;"><?php echo lang::trans('start_slider_button') ?>
								</div></a>

								<!-- LAYER NR. 6 -->
								<div class="tp-caption rev-scroll-btn  rs-parallaxlevel-0" 
									 id="slide-151-layer-9" 
									 data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" 
									 data-y="['bottom','bottom','bottom','bottom']" data-voffset="['50','50','50','50']" 
												data-width="35"
									data-height="55"
									data-whitespace="nowrap"
									data-transform_idle="o:1;"
										data-style_hover=""
						 
									 data-transform_in="y:50px;opacity:0;s:1500;e:Power3.easeInOut;" 
									 data-transform_out="y:50px;opacity:0;s:1000;s:1000;" 
									data-start="2000" 
									data-splitin="none" 
									data-splitout="none" 
									data-actions='[{"event":"click","action":"scrollbelow","offset":"0px"}]'
									data-basealign="slide" 
									data-responsive_offset="off" 
									data-responsive="off"
									
									style="z-index: 10; min-width: 35px; max-width: 35px; max-width: 55px; max-width: 55px; white-space: nowrap; font-size: px; line-height: px; font-weight: 100;border-color:rgba(255, 255, 255, 0.50);border-style:solid;border-width:1px;border-radius:23px 23px 23px 23px;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;">							
													<span>
													</span>							
												 
								</div>
							</li>
						</ul>
						<div class="tp-static-layers">

							<!-- LAYER NR. 3 -->
							<a href="<?php echo URL ?>/home/"><div class="tp-caption NotGeneric-BigButton rev-btn  rs-parallaxlevel-0 tp-static-layer" 
								 id="slide-16-layer-7" 
								 data-x="['left','left','left','left']" data-hoffset="['30','30','30','30']"
								 data-y="['top','top','top','top']" data-voffset="['29','29','29','29']" 
											data-width="none"
								data-height="none"
								data-whitespace="nowrap"
								data-visibility="['on','on','off','off']"
								data-transform_idle="o:1;"
									data-transform_hover="o:1;rX:0;rY:0;rZ:0;z:0;s:300;e:Power1.easeInOut;"
									data-style_hover="c:rgba(255, 255, 255, 1.00);bc:rgba(255, 255, 255, 1.00);"
					 
								 data-transform_in="opacity:0;s:1500;e:Power3.easeInOut;" 
								 data-transform_out="s:300;s:300;" 
								data-start="500" 
								data-splitin="none" 
								data-splitout="none" 
								data-actions='[{"event":"click","action":"jumptoslide","slide":"rs-148","delay":""}]'
								data-basealign="slide" 
								data-responsive_offset="off" 
								data-responsive="off"
								data-startslide="0" 
								data-endslide="4" 
								
								style="z-index: 7; white-space: nowrap;outline:none;box-shadow:none;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;"><?php echo lang::trans('start_slider_menu_gohome') ?> 
							</div></a>
							
							<!-- LAYER NR. 4 -->
							<a href="<?php echo URL ?>/home/?skip_inactivity=true"><div class="tp-caption NotGeneric-BigButton rev-btn  rs-parallaxlevel-0 tp-static-layer" 
								 id="slide-16-layer-7" 
								 data-x="['right','right','right','right']" data-hoffset="['30','30','30','30']"
								 data-y="['top','top','top','top']" data-voffset="['29','29','29','29']" 
											data-width="none"
								data-height="none"
								data-whitespace="nowrap"
								data-visibility="['on','on','off','off']"
								data-transform_idle="o:1;"
									data-transform_hover="o:1;rX:0;rY:0;rZ:0;z:0;s:300;e:Power1.easeInOut;"
									data-style_hover="c:rgba(255, 255, 255, 1.00);bc:rgba(255, 255, 255, 1.00);"
					 
								 data-transform_in="opacity:0;s:1500;e:Power3.easeInOut;" 
								 data-transform_out="s:300;s:300;" 
								data-start="500" 
								data-splitin="none" 
								data-splitout="none" 
								data-actions='[{"event":"click","action":"jumptoslide","slide":"rs-148","delay":""}]'
								data-basealign="slide" 
								data-responsive_offset="off" 
								data-responsive="off"
								data-startslide="0" 
								data-endslide="4" 
								
								style="z-index: 7; white-space: nowrap;outline:none;box-shadow:none;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;"><?php echo lang::trans('start_slider_menu_gohome_skipindex') ?> 
							</div></a>
						</div>
						<div class="tp-bannertimer tp-bottom" style="visibility: hidden !important;"></div>	
					</div>
				</div><!-- END REVOLUTION SLIDER -->
			</article>
		</section>

		<!-- START JAVASCRIPT SECTION (Load javascripts at bottom to reduce load time) -->
		<!-- START @CORE PLUGINS -->
		<script src="<?php echo URL ?>/style/global/plugins/bower_components/jquery/dist/jquery.min.js"></script>
		<!--/ END CORE PLUGINS -->

		<!-- START @PAGE LEVEL PLUGINS -->
		<!-- REVOLUTION JS FILES -->
		<script src="<?php echo URL ?>/style/index/js/jquery.themepunch.tools.min.js"></script>
		<script src="<?php echo URL ?>/style/index/js/jquery.themepunch.revolution.min.js"></script>

		<!-- SLIDER REVOLUTION 5.0 EXTENSIONS  (Load Extensions only on Local File Systems !  The following part can be removed on Server for On Demand Loading) -->
		<script src="<?php echo URL ?>/style/index/js/revolution.extension.actions.min.js"></script>
		<script src="<?php echo URL ?>/style/index/js/revolution.extension.carousel.min.js"></script>
		<script src="<?php echo URL ?>/style/index/js/revolution.extension.kenburn.min.js"></script>
		<script src="<?php echo URL ?>/style/index/js/revolution.extension.layeranimation.min.js"></script>
		<script src="<?php echo URL ?>/style/index/js/revolution.extension.migration.min.js"></script>
		<script src="<?php echo URL ?>/style/index/js/revolution.extension.navigation.min.js"></script>
		<script src="<?php echo URL ?>/style/index/js/revolution.extension.parallax.min.js"></script>
		<script src="<?php echo URL ?>/style/index/js/revolution.extension.slideanims.min.js"></script>
		<script src="<?php echo URL ?>/style/index/js/revolution.extension.video.min.js"></script>
		<!--/ END PAGE LEVEL PLUGINS -->

		<!-- START @PAGE LEVEL SCRIPTS -->
		<script src="<?php echo URL ?>/style/index/js/blankon.rs-not-generic.js"></script>
		<!--/ END PAGE LEVEL SCRIPTS -->
		<!--/ END JAVASCRIPT SECTION -->

		<!-- START GOOGLE ANALYTICS -->
		<?php echo template::analytics() ?>
		<!--/ END GOOGLE ANALYTICS -->
	</body>
</html>
