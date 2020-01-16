<?php 
/* 
 * DO NOT Edit this file!! 
 * The Theme's Custom Styles are written here and it is intended for internal use only
 * 
 */
 
$root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
if ( file_exists( $root.'/wp-load.php' ) ) {
    require_once( $root.'/wp-load.php' );
} elseif ( file_exists( $root.'/wp-config.php' ) ) {
    require_once( $root.'/wp-config.php' );
}

if ( get_theme_mod( 'udesign_custom_styles_use_css_file' ) && !$file_was_included ) 
    exit( '<b>"U-Design" Message</b>: Direct access to this file is not permitted! Try <a href="'.get_template_directory_uri() . '/styles/custom/custom_style.css">custom_style.css</a> instead.' );

$logo_img_url = ( $udesign_options['custom_logo_img'] ) ? esc_url($udesign_options['custom_logo_img']) : '../style1/images/logo.png';
$font_family = preg_replace('/:.*/','', $udesign_options['font_family']);
$title_headings_font_family = preg_replace('/:.*/','', $udesign_options['title_headings_font_family']);
$top_nav_font_family = preg_replace('/:.*/','', $udesign_options['top_nav_font_family']);


if ( !get_theme_mod( 'udesign_custom_styles_use_css_file' ) ) header("Content-type: text/css");

// start output buffer
ob_start();

/* Styles Other Than "Custom Colors" section
------------------------------------------------------------------------------*/ ?>
/* Custom Styles */
body, #page-content-title .title-description { font-family:'<?php echo $font_family; ?>'; }
body { font-size:<?php echo $udesign_options['font_size']; ?>px; }
body { line-height:<?php echo $udesign_options['body_font_line_height']; ?>; }
h1, h2, h3, h4, h5, h6, #slogan, .single-post-categories { font-family:'<?php echo $title_headings_font_family; ?>'; }
h1, h2, h3, h4, h5, h6 { line-height:<?php echo $udesign_options['heading_font_line_height']; ?>; }
#top-elements { height:<?php echo $udesign_options['top_area_height']; ?>px; }
#logo h1 a, #logo .site-name a { background:transparent url( <?php echo $logo_img_url; ?> ) no-repeat 0 100%; width:<?php echo $udesign_options['logo_width']; ?>px; height:<?php echo $udesign_options['logo_height']; ?>px; }       
<?php if ($udesign_options['logo_position_center'] == 'yes') : ?>
    #logo { position: absolute; left: 50%; margin-left: -<?php echo ($udesign_options['logo_width'] / 2); ?>px; padding: 0; }
<?php endif; ?>
#slogan { 
    top:<?php echo $udesign_options['slogan_distance_from_the_top']; ?>px;
    <?php echo ( is_rtl() ) ? 'right:' : 'left:'; echo $udesign_options['slogan_distance_from_the_left']; ?>px;
}
#slogan { font-size:<?php echo $udesign_options['slogan_font_size']; ?>px; }
#navigation-menu { font-family:'<?php echo $top_nav_font_family; ?>'; }
#navigation-menu { font-size:<?php echo $udesign_options['top_nav_font_size']; ?>px; }
#navigation-menu > ul.sf-menu { margin-top:<?php echo ($udesign_options['main_menu_vertical_positioning'] > 0) ? '-'.$udesign_options['main_menu_vertical_positioning'].'px' : 0; ?>; }
<?php if ($udesign_options['remove_border_under_menu'] == 'yes') : ?>
    #main-menu { background:none; }
<?php endif; ?>
<?php $heading_font_size_coefficient = $udesign_options['heading_font_size_coefficient']; ?>
h1 {font-size:<?php echo (1.883 * $heading_font_size_coefficient); ?>em; }
h2 {font-size:<?php echo (1.667 * $heading_font_size_coefficient); ?>em; }
h3 {font-size:<?php echo (1.5 * $heading_font_size_coefficient); ?>em; }
h4 {font-size:<?php echo (1.333 * $heading_font_size_coefficient); ?>em; }
h5 {font-size:<?php echo (1.25 * $heading_font_size_coefficient); ?>em; }
h6 {font-size:<?php echo (1.083 * $heading_font_size_coefficient); ?>em; }
    
#page-content-title #page-title h1,
#page-content-title #page-title h2,
#page-content-title #page-title h3,
#page-content-title #page-title .single-post-categories {<?php echo 'font-size:'.(1.667 * $heading_font_size_coefficient).'em;'; ?>}

.post-top h1, .post-top h2, .post-top h3 { font-size:<?php echo (1.9 * $heading_font_size_coefficient); ?>em; }

#sidebarSubnav h3, h3.bottom-col-title {font-size:<?php echo (1.21 * $heading_font_size_coefficient); ?>em; }

.portfolio-items-wrapper h2 {font-size:<?php echo (1.333 * $heading_font_size_coefficient); ?>em !important; }
h2.portfolio-single-column {font-size:<?php echo (1.667 * $heading_font_size_coefficient); ?>em !important; }

#gs-header #header-content { width:<?php echo $udesign_options['gs_image_width']; ?>px; }
<?php if (@$udesign_options['c1_remove_image_frame'] == 'yes') : ?>
    #c1-slider { background-image:none; width:940px; }
    #c1-slider li { width: 940px; }
    #c1-header .c1-slideshow { padding: 10px 0; }
    .c1-slide-img-wrapper { padding: 13px 0; }
    #c1-shadow { margin:-309px auto 0; }
<?php endif; ?>
#c2-slider .slide-desc h2, #c2-slider .slide-desc { color:#<?php echo $udesign_options['c2_text_color']; ?>; }
#c2-slider .slide-desc h2 { font-size:<?php echo ($udesign_options['c2_slider_text_size']+0.6); ?>em !important; font-family:'<?php echo $font_family; ?>'; line-height:<?php echo $udesign_options['c2_slider_text_line_height']; ?>; }
#c2-slider .slide-desc p, #c2-slider .slide-desc ul { font-size:<?php echo $udesign_options['c2_slider_text_size']; ?>em; line-height:<?php echo $udesign_options['c2_slider_text_line_height']; ?>; }
#c3-slider .sliding-text { color:#<?php echo $udesign_options['c3_text_color']; ?>; font-size:<?php echo $udesign_options['c3_slider_text_size']; ?>em; line-height:<?php echo $udesign_options['c3_slider_text_line_height']; ?>; }

<?php if ($udesign_options['feedback_position_fixed'] == 'yes') : ?>
    #feedback a.feedback { position: fixed; }
<?php endif; ?>

<?php if ($udesign_options['main_menu_alignment'] == 'right') : ?>
    #navigation-menu > ul.sf-menu { float:right; }
<?php elseif ($udesign_options['main_menu_alignment'] == 'left') : ?>
    @media screen and (min-width: 720px) {
        #navigation-menu { left: 0; padding-left: 4px; }
        .u-design-responsive-on #navigation-menu { left: 0; padding-left: 17px; }
    }
    @media screen and (min-width: 960px) {
        .u-design-responsive-on #navigation-menu { left: 0; padding-left: 4px; }
    }
<?php else : // when "center" menu alignement option is chosen ?>
    @media screen and (min-width: 720px) {
        #navigation-menu > ul.sf-menu {
          float: none;
          display: table;
          margin-left: auto;
          margin-right: auto;
        }
    }
<?php endif; ?>
    
/* secondary nav bar items' alignments */
#sec-nav-text-area-1 { text-align: <?php echo $udesign_options['secondary_menu_text_area_1_alignment']; ?>; }
#sec-nav-text-area-2 { text-align: <?php echo $udesign_options['secondary_menu_text_area_2_alignment']; ?>; }
#sec-nav-menu-area { text-align: <?php echo $udesign_options['secondary_menu_text_alignment']; ?>; }
    
<?php
/* BEGIN Styles from "Custom Colors" section
------------------------------------------------------------------------------*/
if ( $udesign_options['custom_colors_switch'] == 'enable' ) { ?>

body, .posts-counter, h3.accordion-toggle a { color:#<?php echo $udesign_options['body_text_color']; ?>; }
a, #isotope-options li a:hover, #isotope-options li a.selected, h3.accordion-toggle.active a { color:#<?php echo $udesign_options['main_link_color']; ?>; }
a:hover, .post-top h2 a:hover, .post-top h3 a:hover, #isotope-options li a, .single-post-nav-links .prev-title a:hover, .single-post-nav-links .next-title a:hover { color:#<?php echo $udesign_options['main_link_color_hover']; ?>; }
.custom-formatting li.current_page_item > a, .custom-formatting li.current-menu-item > a, .custom-formatting li.current-cat > a, .custom-formatting li.current > a { color: #<?php echo $udesign_options['main_link_color_hover']; ?>; }
.custom-formatting li.current_page_item > a:hover, .custom-formatting li.current-menu-item > a:hover,.custom-formatting li.current-cat > a:hover, .custom-formatting li.current > a:hover { color: #<?php echo $udesign_options['main_link_color']; ?>; }
h1, h2, h3, h4, h5, h6, .post-top h2 a, .post-top h3 a, .single-post-nav-links .prev-title a, .single-post-nav-links .next-title a { color:#<?php echo $udesign_options['main_headings_color']; ?>; }
<?php if ($udesign_options['top_bg_img'] != '') : ?>
    #top-wrapper { background: url("<?php echo $udesign_options['top_bg_img']; ?>") <?php echo $udesign_options['top_bg_img_repeat']; ?> scroll <?php echo $udesign_options['top_bg_img_position_horizontal']; ?> <?php echo $udesign_options['top_bg_img_position_vertical']; ?> #<?php echo $udesign_options['top_bg_color']; ?>; }
<?php else : ?>
    #top-wrapper { background-color:#<?php echo $udesign_options['top_bg_color']; ?>; }
<?php endif; ?>
#slogan, #top-elements .phone-number, #top-elements .social_media_title, #search input.blur, #search input.inputbox_focus { color:#<?php echo $udesign_options['top_text_color']; ?>; }
#slogan{ color:#<?php echo $udesign_options['top_text_color']; ?>; }
<?php if ($udesign_options['header_bg_img'] != '') : ?>
    #gs-header, #piecemaker-header, #c1-header, #c2-header, #c3-header, #rev-slider-header { background: url("<?php echo $udesign_options['header_bg_img']; ?>") <?php echo $udesign_options['header_bg_img_repeat']; ?> scroll <?php echo $udesign_options['header_bg_img_position_horizontal']; ?> <?php echo $udesign_options['header_bg_img_position_vertical']; ?> #<?php echo $udesign_options['header_bg_color']; ?>; }
<?php else : ?>
    #gs-header, #piecemaker-header, #c1-header, #c2-header, #c3-header, #rev-slider-header { background-color:#<?php echo $udesign_options['header_bg_color']; ?>; }
<?php endif; ?>
<?php if ($udesign_options['main_menu_vertical_positioning'] > 0) : ?>
    #navigation-menu > ul.sf-menu {
        background-color: #<?php echo $udesign_options['top_nav_background_color']; ?>; /* the fallback */
        background-color: rgba(<?php echo implode( ",", udesign_hex2rgb( $udesign_options['top_nav_background_color'] ) ) . ','. $udesign_options['top_nav_background_opacity']; ?>);
    }
    <?php if ($udesign_options['top_nav_background_opacity'] != 0)  : 
            if ($udesign_options['main_menu_alignment'] == 'right' ) : ?>
                #navigation-menu > ul.sf-menu { margin-right: 20px; }
    <?php   elseif ($udesign_options['main_menu_alignment'] == 'left' ) : ?>
                #navigation-menu > ul.sf-menu { margin-left: 16px; }
                @media screen and (min-width: 720px) {
                    .u-design-responsive-on #navigation-menu > ul.sf-menu { margin-left: 3px; }
                }
                @media screen and (min-width: 960px) {
                    .u-design-responsive-on #navigation-menu > ul.sf-menu { margin-left: 16px; }
                }
    <?php   endif; ?>
    <?php endif; ?>
<?php else : ?>
    #main-menu {
        background-color: #<?php echo $udesign_options['top_nav_background_color']; ?>; /* the fallback */
        background-color: rgba(<?php echo implode( ",", udesign_hex2rgb( $udesign_options['top_nav_background_color'] ) ) . ','. $udesign_options['top_nav_background_opacity']; ?>);
    }
<?php endif; ?>
@media screen and (min-width: 720px) {
    .u-design-fixed-menu-on.fixed-menu #main-menu {
        background-color: #<?php echo $udesign_options['top_nav_background_color']; ?>; /* the fallback */
        background-color: rgba(<?php echo implode( ",", udesign_hex2rgb( $udesign_options['top_nav_background_color'] ) ) . ','. $udesign_options['top_nav_background_opacity']; ?>);
    }
}
#navigation-menu ul.sf-menu > li > a > span { color:#<?php echo $udesign_options['top_nav_link_color']; ?>; }
#navigation-menu ul.sf-menu > li.current-menu-item > a > span, #navigation-menu ul.sf-menu > li.current_page_item > a > span { color:#<?php echo $udesign_options['top_nav_active_link_color']; ?>; }
#navigation-menu ul.sf-menu > li.current-menu-item > a > span:hover, #navigation-menu ul.sf-menu > li.current_page_item > a > span:hover { color:#<?php echo $udesign_options['top_nav_hover_link_color']; ?>; }
#navigation-menu ul.sf-menu > li > a:hover span { color:#<?php echo $udesign_options['top_nav_hover_link_color']; ?>; }
#navigation-menu ul.sf-menu a, #navigation-menu ul.sf-menu a:visited {color: #<?php echo $udesign_options['dropdown_nav_link_color']; ?>; }
#navigation-menu ul.sf-menu a:hover  { color:#<?php echo $udesign_options['dropdown_nav_hover_link_color']; ?>; }
#navigation-menu ul.sf-menu li li {
    background-image: none;
    margin-bottom: 1px;
    background-color: #<?php echo $udesign_options['dropdown_nav_background_color']; ?>; /* the fallback */
    background-color: rgba(<?php echo implode( ",", udesign_hex2rgb( $udesign_options['dropdown_nav_background_color'] ) ) . ','. $udesign_options['dropdown_nav_background_opacity']; ?>);
}
#secondary-navigation-bar-wrapper  {
    background-color: #<?php echo $udesign_options['sec_menu_bg_color']; ?>; /* the fallback */
    background-color: rgba(<?php echo implode( ",", udesign_hex2rgb( $udesign_options['sec_menu_bg_color'] ) ) . ','. $udesign_options['sec_menu_bg_opacity']; ?>);
}
#secondary-navigation-bar-content  { color:#<?php echo $udesign_options['sec_menu_text_color']; ?>; }
#secondary-navigation-bar-content a { color:#<?php echo $udesign_options['sec_menu_link_color']; ?>; }
#secondary-navigation-bar-content a:hover { color:#<?php echo $udesign_options['sec_menu_link_hover_color']; ?>; }
#page-content-title #page-title h1,
#page-content-title #page-title h2,
#page-content-title #page-title h3,
#page-content-title #page-title .single-post-categories { color:#<?php echo $udesign_options['page_title_color']; ?>; }
<?php if ($udesign_options['page_title_bg_img'] != '') : ?>
    #page-content-title { background: url("<?php echo $udesign_options['page_title_bg_img']; ?>") <?php echo $udesign_options['page_title_bg_img_repeat']; ?> scroll <?php echo $udesign_options['page_title_bg_img_position_horizontal']; ?> <?php echo $udesign_options['page_title_bg_img_position_vertical']; ?> #<?php echo $udesign_options['page_title_bg_color']; ?>; }
<?php else : ?>
    #page-content-title { background-color:#<?php echo $udesign_options['page_title_bg_color']; ?>; }
<?php endif; ?>
<?php if ( $udesign_options['udesign_remove_horizontal_rulers'] != 'yes' ) : ?>
    #before-content { background: url("../common-images/home-page-before-content-top.png") repeat-x scroll 0 0; }
    #home-page-content, #page-content, #footer-bg { background: url("../common-images/home-page-content-top.png") repeat-x scroll 0 0; }
<?php endif; ?>
<?php if ($udesign_options['main_content_bg_img'] != '') : ?>
    #home-page-content, #page-content, .no_title_section #page-content { background: url("<?php echo $udesign_options['main_content_bg_img']; ?>") <?php echo $udesign_options['main_content_bg_img_repeat']; ?> scroll <?php echo $udesign_options['main_content_bg_img_position_horizontal']; ?> <?php echo $udesign_options['main_content_bg_img_position_vertical']; ?> #<?php echo $udesign_options['main_content_bg']; ?>; }
<?php else : ?>
    #home-page-content, #page-content, .no_title_section #page-content { background-color:#<?php echo $udesign_options['main_content_bg']; ?>; }
<?php endif; ?>
h3.before_cont_title { color:#<?php echo $udesign_options['widget_title_color']; ?>; }
#before-content { color:#<?php echo $udesign_options['widget_text_color']; ?>; }
<?php if ($udesign_options['home_page_before_content_bg_img'] != '') : ?>
    #before-content { background: url("<?php echo $udesign_options['home_page_before_content_bg_img']; ?>") <?php echo $udesign_options['home_page_before_content_bg_img_repeat']; ?> scroll <?php echo $udesign_options['home_page_before_content_bg_img_position_horizontal']; ?> <?php echo $udesign_options['home_page_before_content_bg_img_position_vertical']; ?> #<?php echo $udesign_options['widget_bg_color']; ?>; }
<?php else : ?>
    #before-content { background-color:#<?php echo $udesign_options['widget_bg_color']; ?>; }
<?php endif; ?>
<?php if ($udesign_options['bottom_bg_img'] != '') : ?>
    #bottom-bg { background: url("<?php echo $udesign_options['bottom_bg_img']; ?>") <?php echo $udesign_options['bottom_bg_img_repeat']; ?> scroll <?php echo $udesign_options['bottom_bg_img_position_horizontal']; ?> <?php echo $udesign_options['bottom_bg_img_position_vertical']; ?> #<?php echo $udesign_options['bottom_bg_color']; ?>; }
<?php else : ?>
    #bottom-bg { background-color: #<?php echo $udesign_options['bottom_bg_color']; ?>; }
<?php endif; ?>
h3.bottom-col-title { color: #<?php echo $udesign_options['bottom_title_color']; ?>; }
#bottom, #bottom .textwidget, #bottom #wp-calendar, #bottom .posts-counter { color: #<?php echo $udesign_options['bottom_text_color']; ?>; }
#bottom a { color: #<?php echo $udesign_options['bottom_link_color']; ?>; }
#bottom a:hover { color: #<?php echo $udesign_options['bottom_hover_link_color']; ?>; }
<?php if ($udesign_options['footer_bg_img'] != '') : ?>
    #footer-bg { background: url("<?php echo $udesign_options['footer_bg_img']; ?>") <?php echo $udesign_options['footer_bg_img_repeat']; ?> scroll <?php echo $udesign_options['footer_bg_img_position_horizontal']; ?> <?php echo $udesign_options['footer_bg_img_position_vertical']; ?> #<?php echo $udesign_options['footer_bg_color']; ?>; }
<?php else : ?>
    #footer-bg { background-color: #<?php echo $udesign_options['footer_bg_color']; ?>; }
<?php endif; ?>
body { background-color: #<?php echo $udesign_options['footer_bg_color']; ?>; }
#footer, #footer_text { color: #<?php echo $udesign_options['footer_text_color']; ?>; }
#footer a, #footer_text a { color: #<?php echo $udesign_options['footer_link_color']; ?>; }
#footer a:hover, #footer_text a:hover { color: #<?php echo $udesign_options['footer_hover_link_color']; ?>; }

<?php if ($udesign_options['one_continuous_bg_img'] != '') : 
    $one_continuous_bg_img_fixed = ($udesign_options['one_continuous_bg_img_fixed'] == 'yes') ? 'fixed' : 'scroll';
    $one_continuous_bg_img_with_other_bg_imgs = ($udesign_options['one_continuous_bg_img_with_other_bg_imgs'] == 'yes') ? 'background-color:transparent;' : 'background:none;'; ?>
<?php   if ( $udesign_options['enable_responsive'] ) : ?>
    @media screen and (min-width: <?php echo ( $udesign_options['responsive_remove_bg_images_960-720']) ? '960px' : '720px'?>) {
<?php   endif; ?>
        #wrapper-1 { background: url("<?php echo $udesign_options['one_continuous_bg_img']; ?>") <?php echo $udesign_options['one_continuous_bg_img_repeat']; ?> <?php echo $one_continuous_bg_img_fixed; ?>  <?php echo $udesign_options['one_continuous_bg_img_position_horizontal']; ?> <?php echo $udesign_options['one_continuous_bg_img_position_vertical']; ?> #<?php echo $udesign_options['top_bg_color']; ?>; }
        #top-wrapper, #gs-header, #piecemaker-header, #c1-header, #c2-header, #c3-header, #rev-slider-header, #page-content-title, #home-page-content, #page-content, .no_title_section #page-content, #before-content, #bottom-bg, #footer-bg { <?php echo $one_continuous_bg_img_with_other_bg_imgs; ?> }
<?php   if ( $udesign_options['enable_responsive'] ) : ?>
    }
<?php   endif; ?>
<?php endif; ?>
<?php
}
/* END Styles from "Custom Colors" section
------------------------------------------------------------------------------*/ ?>

<?php /* BEGIN Responsive Custom Styles */
if ( $udesign_options['enable_responsive'] ) : 
    $responsive_logo_img_url = ( $udesign_options['responsive_logo_img'] ) ? esc_url( $udesign_options['responsive_logo_img'] ) : $logo_img_url;
    $responsive_logo_height = ( $udesign_options['responsive_logo_height'] === '' ) ? $udesign_options['logo_height'] : $udesign_options['responsive_logo_height']; ?>
    @media screen and (max-width: 719px) {
        #logo h1 a, #logo .site-name a { background:transparent url( <?php echo $responsive_logo_img_url; ?> ) no-repeat 0 100%; height:<?php echo $responsive_logo_height; ?>px; }
<?php   if ( $udesign_options['responsive_remove_secondary_menu'] == 'yes' ) echo '#secondary-navigation-bar-wrapper { display:none; }'; ?>
<?php   if ( $udesign_options['responsive_remove_slider_area'] == 'yes' ) echo '#gs-header, #piecemaker-header, #c1-header, #c2-header, #c3-header, #rev-slider-header { display:none; }'; ?>
        #wrapper-1, #top-wrapper, #gs-header, #piecemaker-header, #c1-header, #c2-header, #c3-header, #rev-slider-header, #page-content-title, #home-page-content, #page-content, .no_title_section #page-content, #before-content, #bottom-bg, #footer-bg { background-image:none; }
    }
    @media screen and (max-width: 959px) {
        #c2-slider .slide-desc p, #c2-slider .slide-desc ul  { font-size:<?php echo $udesign_options['c2_slider_text_size']-0.1; ?>em; line-height:<?php echo $udesign_options['c2_slider_text_line_height']-0.1; ?>; }
        ul#c2-slider li ul li { font-size:<?php echo $udesign_options['c2_slider_text_size']-0.4; ?>em; }
    }
    @media screen and (max-width: 480px) {
        #c2-slider .slide-desc h2 { font-size:<?php echo ($udesign_options['c2_slider_text_size']+0.2); ?>em !important; line-height:<?php echo $udesign_options['c2_slider_text_line_height']-0.2; ?>; }
    }
    @media screen and (min-width: 720px) and (max-width: 959px) {
        #c2-slider .slide-desc h2 { font-size:<?php echo ($udesign_options['c2_slider_text_size']+0.5); ?>em !important; line-height:<?php echo $udesign_options['c2_slider_text_line_height']-0.1; ?>; }
<?php   if ( $udesign_options['responsive_remove_bg_images_960-720'] == 'yes' ) : ?>
        #wrapper-1, #top-wrapper, #gs-header, #piecemaker-header, #c1-header, #c2-header, #c3-header, #rev-slider-header, #page-content-title, #home-page-content, #page-content, .no_title_section #page-content, #before-content, #bottom-bg, #footer-bg { background-image:none; }
<?php   endif; ?>
    }
<?php 
endif; /* END Responsive Custom Styles */

// BEGIN "Stay-On-Top" Main Menu Styles ?>
@media screen and (min-width: 720px) {
    .u-design-fixed-menu-on.fixed-menu #top-wrapper { background-color:#<?php echo $udesign_options['top_bg_color']; ?>; }
    .u-design-fixed-menu-on.fixed-menu #navigation-menu > ul.sf-menu { background-color: transparent; }
<?php if ($udesign_options['main_menu_alignment'] != 'center') : ?>
        .u-design-fixed-menu-on.fixed-menu #navigation-menu > ul.sf-menu {
          margin-left: 0;
          margin-right: 0;
        }
<?php endif; ?>
}
<?php 
if ( $udesign_options['add_fixed_menu_shadow'] == 'yes' ) : ?>
    @media screen and (min-width: 720px) {
        .u-design-fixed-menu-on.fixed-menu #top-wrapper {
            -webkit-box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
               -moz-box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
                    box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
        }
    }
<?php 
endif;
if ( $udesign_options['remove_fixed_menu_background_image'] == 'yes' ) : ?>
    @media screen and (min-width: 720px) {
        .u-design-fixed-menu-on.fixed-menu #top-wrapper { background-image:none; ?>; }
    }
<?php 
endif;
// END "Stay-On-Top" Main Menu Styles

// If Max Width
if ( $udesign_options['max_theme_width'] ) : 
    
    $main_content_width = 100 - $udesign_options['global_sidebar_width'];
    $global_sidebar_width = $udesign_options['global_sidebar_width']; ?>
    
    @media screen and (min-width: 960px) {
    
        /* Set the Container widths first */
        .container_24 {
            width: 94%;
            margin-left: 3%;
            margin-right: 3%;
        }
        /* Sidebar */
        #main-content.grid_16 { width: <?php echo $main_content_width;?>%; }
        #sidebar.grid_8 { width: <?php echo $global_sidebar_width; ?>%; }
        #sidebar.push_8, #main-content.push_8 { left: <?php echo $global_sidebar_width; ?>%; }
        #main-content.pull_16, #sidebar.pull_16 { right: <?php echo $main_content_width;?>%; }
    
    }
<?php   
elseif ( $udesign_options['global_theme_width'] > 960 ) : // When specific theme width is set greater than 960px styles 
    
    $global_theme_width = $udesign_options['global_theme_width'];
    $main_content_width = 100 - $udesign_options['global_sidebar_width'];
    $global_sidebar_width = $udesign_options['global_sidebar_width']; ?>
    
    @media screen and (min-width: 960px) {
        /* Set the Container widths first */
        .container_24 {
            max-width: <?php echo $global_theme_width; ?>px;
            width: auto;
        }
        /* Sidebar */
        #main-content.grid_16 { width: <?php echo $main_content_width;?>%; }
        #sidebar.grid_8 { width: <?php echo $global_sidebar_width; ?>%; }
        #sidebar.push_8, #main-content.push_8 { left: <?php echo $global_sidebar_width; ?>%; }
        #main-content.pull_16, #sidebar.pull_16 { right: <?php echo $main_content_width;?>%; }
    }
    @media screen and (max-width: <?php echo ($global_theme_width + 40); ?>px) {
        #feedback { display: none; }
    }
    @media screen and (max-width: <?php echo ($global_theme_width + 100); ?>px) {
        #page-peel { display: none; }
    }
    
<?php 
endif; 

if ( $udesign_options['udesign_sticky_footer'] ) : ?>
    /* BEGIN: STICKY FOOTER CSS */
    html, body { height: 100%; }
    #wrapper-1 { min-height: 100%; }
    #wrapper-1 { background-color: <?php echo ( $udesign_options['one_continuous_bg_img'] ) ? '#'.$udesign_options['top_bg_color'] : 'inherit'; ?>;}
    .push {
        overflow:auto;
        padding-bottom: 44px; 
    }
    #footer-bg {
        position: relative;
        margin-top: -44px; /* negative value of footer height */
        height: 44px;
        clear:both;
    }
    /* Opera Fix */
    body:before {
        content:"";
        height:100%;
        float:left;
        width:0;
        margin-top:-32767px;
    }
    /* END: STICKY FOOTER CSS */
<?php 
endif; 



// write the custom styles to the appropriate file
$udesign_custom_styles = ob_get_clean();
if ( get_theme_mod( 'udesign_custom_styles_use_css_file' ) ) { // write the styles to "custom_style.css" file if file is writable
    $handling = fopen($udesign_custom_style_css, 'w');
    fwrite($handling, $udesign_custom_styles);
    fclose($handling);
    // update the custom_styles in the database
    if( ! $udesign_options['reset_to_defaults']  ) {
        $udesign_options['custom_styles'] = $udesign_custom_styles;
        update_option( 'udesign_options', $udesign_options );
    }
} else { // otherwise wtrite the styles to "custom_style.php" file
    echo $udesign_custom_styles;
}

