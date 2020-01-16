<?php 

/**
 * Generate U-Design theme related CMB2 metaboxes, custom fields, or forms
 * 
 */
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Get the cmb2 bootstrap!
 */
if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
    require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
    require_once dirname( __FILE__ ) . '/CMB2/init.php';
}


add_action( 'cmb2_init', 'udesign_cmb2_metaboxes' );
/**
 * Define the metabox and field configurations.
 */
function udesign_cmb2_metaboxes() {
    
    
    // Start with an underscore to hide fields from custom fields list
    $prefix = '_udesign_';

    /**
     * Initiate the metabox
     */
    $cmb = new_cmb2_box( array(
        'id'            => 'udesign_metabox',
        'title'         => __( 'U-Design Options', 'udesign' ),
        'object_types'  => array( 'post', 'page' ), // Post type
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true, // Show field names on the left
        'closed'        => false, // Keep the metabox closed by default
    ) );

    
    // Page Title select options
    $cmb->add_field( array(
            'name'              => __( 'Title Position', 'udesign' ),
            'desc'              => __( 'Overwrite the global title position (applies to this page only)', 'udesign' ),
            'id'                => $prefix . 'page_title',
            'type'              => 'select',
            'show_option_none'  => false,
            'default'           => 'default_position',
            'options'           => array(
                'default_position'   => __( 'Use the default position', 'udesign' ),
                'position1'         => __( 'Title Position 1 (before the main content)', 'udesign' ),
                'position2'         => __( 'Title Position 2 (inside the main content)', 'udesign' ),
                'remove1'           => __( 'Remove Title (SEO-friendly)', 'udesign' ),
                'remove2'           => __( 'Remove Title Completely', 'udesign' ),
            ),
    ) );
    
    // Breadcrumbs option checkbox
    $cmb->add_field( array(
            'name' => __( 'Disable Breadcrumbs', 'udesign' ),
            'desc' => __( 'This option will disable/hide the breadcrumbs on this page only', 'udesign' ),
            'id'   => $prefix . 'disable_breadcrumbs',
            'type' => 'checkbox',
    ) );

    
}








