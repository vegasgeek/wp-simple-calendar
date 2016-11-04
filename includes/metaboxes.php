<?php
/**
 * @category WP Simple Calendar
 * @package  WP Simple Calendar
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

add_action( 'cmb2_admin_init', 'wpsimplecalendar_register_calendar_box' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function wpsimplecalendar_register_calendar_box() {
	$prefix = 'wpsc_';

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$cmb = new_cmb2_box( array(
		'id'            => 'wpsimplecalendar_event_meta',
		'title'         => __( 'Event Details', 'cmb2' ),
		'object_types'  => array( 'wpscevents', ), // Post type
	) );

	$cmb->add_field( array(
		'name'             => __( 'All Day Event?', 'cmb2' ),
		'desc'             => __( '', 'cmb2' ),
		'id'               => $prefix . 'all_day_event',
		'type'             => 'radio_inline',
		'options'          => array(
			'1' => __( 'Yes', 'cmb2' ),
			'0'   => __( 'No', 'cmb2' ),
		),
		'default'          => '1',
	) );

	$cmb->add_field( array(
		'name' => __( 'Event Start', 'cmb2' ),
		'desc' => __( '', 'cmb2' ),
		'id'   => $prefix . 'start_date_time',
		'type' => 'text_datetime_timestamp',
		'attributes'	=> array(
			'required'	=> 'required',
		),
	) );

	$cmb->add_field( array(
		'name' => __( 'Event End', 'cmb2' ),
		'desc' => __( '', 'cmb2' ),
		'id'   => $prefix . 'end_date_time',
		'type' => 'text_datetime_timestamp',
		'attributes'	=> array(
			'required'	=> 'required',
		),
	) );

	$cmb->add_field( array(
		'name'       => __( 'Registration URL', 'cmb2' ),
		'desc'       => __( 'Full URL, including http://', 'cmb2' ),
		'id'         => $prefix . 'url',
		'type'       => 'text',
	) );

	$cmb->add_field( array(
		'name'       => __( 'Registration Text', 'cmb2' ),
		'desc'       => __( 'For Example: "Click Here" or "For More Information"', 'cmb2' ),
		'id'         => $prefix . 'reg_text',
		'type'       => 'text',
	) );

}