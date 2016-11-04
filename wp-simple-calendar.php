<?php
/*
Plugin Name: WP Simple Calendar
Plugin URI: http://vegasgeek.com/
Description: A simple plugin to make it simple to add a calendar or list of events to your site. Simply.
Version: 2.0
Requires at least: WordPress 3.0
Tested up to: WordPress 4.6.1
License: GPLv2 or later
Author: John Hawkins, Toby Cryns, Adrienne Peirce
Author URI: http://vegasgeek.com/
Modified: Nikki Blight <nblight@nlb-creations.com>

------------------------------------------------------------------------

Copyright 2013-2016
John Hawkins (email : john@vegasgeek.com)
Toby Cryns  (email : toby@themightymo.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

define( 'WPSC_VERSION', '2.0' );
require_once( 'upgrade.php' );

/**
 * @since  1.0
 * Include the core file
 */
add_action( 'init', 'wpsimplecalendar_init' );
function wpsimplecalendar_init() {
	wp_register_style( 'wpsimplecalendarcolorboxcss', plugins_url( 'includes/js/colorbox/colorbox.css', __FILE__) );
	wp_register_style( 'wpsimplecalendarcss', plugins_url( 'includes/css/screen.css', __FILE__ ), array( 'wpsimplecalendarcolorboxcss' ) );

	require_once( 'includes/core.php' );
}

/**
 * @since  1.0
 * Enqueue front end style sheets
 */
add_action( 'wp_enqueue_scripts', 'wpsimplecalendar_stylesheet' );
function wpsimplecalendar_stylesheet() {
	if( ! is_admin() ) :
		wp_enqueue_script(
			'wpsimplecalendarcolorbox',
			plugins_url( '/includes/js/colorbox/jquery.colorbox.js', __FILE__ ),
			array( 'jquery' )
		);

		wp_enqueue_script(
			'wpsimplecalendarpublic',
			plugins_url( '/includes/js/wp-simple-calendar.js?v=1.1.0', __FILE__ ),
			array( 'jquery' )
		);
			wp_localize_script(
				'wpsimplecalendarpublic',
				'wpsimplecalendar',
				array(
					'ajaxurl'	=> admin_url( 'admin-ajax.php' )
				)
			);

		wp_enqueue_style( 'wpsimplecalendarcss' );
	endif;
}

/**
 * @since  1.1.1
 * register a start date column and remove the existing publish date column from the admin screen
 */
function wpsimplecalendar_add_column( $defaults ) {
	unset( $defaults['date'] );
	$new = array();
	//shift the order so that it's not the last column
	foreach( $defaults as $key => $value ) {
		if ( $key == 'taxonomy-wpsccategory' ) {
			$new['wpsc_start_date_time'] = 'Event Start Date';
		}
		$new[$key] = $value;
	}
	return $new;
}
add_filter( 'manage_wpscevents_posts_columns', 'wpsimplecalendar_add_column', 10 );

/**
 * @since  1.1.1
 * register the content for the new column
 */
function wpsimplecalendar_columns_content( $column_name, $post_ID ) {
	if ( $column_name == 'wpsc_start_date_time' ) {
			wp_die( get_post_meta( $post_ID, $column_name, true ) );
		echo date_i18n( get_option( 'date_format' ), get_post_meta( $post_ID, $column_name, true ) );

	}
}
add_action( 'manage_wpscevents_posts_custom_column', 'wpsimplecalendar_columns_content', 10, 2 );

/**
 * @since 1.0
 * Setup Custom Post Type for events
 */

add_action( 'init', 'wpsimplecalendar_create_custom_post_type' );
function wpsimplecalendar_create_custom_post_type()
{
	$labels = array(
		'name'					=> _x( 'Simple Calendar Events', 'post type general name' ),
		'singular_name'			=> _x( 'Event', 'post type singular name' ),
		'add_new'				=> _x( 'Add New Event', 'wpsimplecalendar' ),
		'add_new_item'			=> __( 'Add New Event' ),
		'edit_item'				=> __( 'Edit Event' ),
		'new_item'				=> __( 'New Event' ),
		'all_items'				=> __( 'All Events' ),
		'view_item'				=> __( 'View Event' ),
		'search_items'			=> __( 'Search Events' ),
		'not_found'				=>  __( 'No Events found' ),
		'not_found_in_trash'	=> __( 'No Events found in Trash' ),
		'parent_item_colon'		=> '',
		'menu_name'				=> 'Simple Calendar'
	);

	$args = array(
		'labels'				=> $labels,
		'public'				=> true,
		'publicly_queryable'	=> true,
		'show_ui'				=> true,
		'show_in_menu'			=> true,
		'query_var'				=> true,
		'rewrite'				=> false,
		'capability_type'		=> 'post',
		'has_archive'			=> true,
		'hierarchical'			=> false,
		'menu_position'			=> null,
		'supports'				=> array( 'title','editor' )
	);

	register_post_type( 'wpscevents',$args );

	// import custom metaboxes
	require_once( 'includes/metaboxes.php' );

}

/**
 * @since  1.0
 * Register taxonomies for categories and locations
 */
add_action( 'init', 'wpsimplecalendar_taxonomies', 0 );

function wpsimplecalendar_taxonomies()
{
	// Setup taxonomy for category
	$labels = array(
		'name'				=> _x( 'Categories', 'taxonomy general name' ),
		'singular_name'		=> _x( 'Category', 'taxonomy singular name' ),
		'search_items'		=> __( 'Search Categories' ),
		'all_items'			=> __( 'All Categories' ),
		'parent_item'		=> __( 'Parent Category' ),
		'parent_item_colon'	=> __( 'Parent Category:' ),
		'edit_item'			=> __( 'Edit Category' ),
		'update_item'		=> __( 'Update Category' ),
		'add_new_item'		=> __( 'Add New Category' ),
		'new_item_name'		=> __( 'New Category Name' ),
		'menu_name'			=> __( 'Categories' )
	);

	$args = array(
		'hierarchical'		=> true,
		'labels'			=> $labels,
		'show_ui'			=> true,
		'show_admin_column'	=> true,
		'query_var'			=> true,
		'rewrite'			=> false
    );

	register_taxonomy( 'wpsccategory', array( 'wpscevents' ), $args );


	// Setup taxonomy for location
	$labels = array(
		'name'				=> _x( 'Locations', 'taxonomy general name' ),
		'singular_name'		=> _x( 'Location', 'taxonomy singular name' ),
		'search_items'		=> __( 'Search Locations' ),
		'all_items'			=> __( 'All Locations' ),
		'parent_item'		=> __( 'Parent Location' ),
		'parent_item_colon'	=> __( 'Parent Location:' ),
		'edit_item'			=> __( 'Edit Location' ),
		'update_item'		=> __( 'Update Location' ),
		'add_new_item'		=> __( 'Add New Location' ),
		'new_item_name'		=> __( 'New Location Name' ),
		'menu_name'			=> __( 'Locations' )
	);

	$args = array(
		'hierarchical'		=> true,
		'labels'			=> $labels,
		'show_ui'			=> true,
		'show_admin_column'	=> true,
		'query_var'			=> true,
		'rewrite'			=> false
    );

	register_taxonomy( 'wpsclocation', array( 'wpscevents' ), $args );
}


/**
 * @since 1.0
 * Define Shortcode for list view
 * Format: [wpsclist quantity="5" category="category-slug"]
 */

add_shortcode( 'wpsclist', 'wpsimplecalendar_list_shortcode' );
function wpsimplecalendar_list_shortcode( $atts ) {
	extract( shortcode_atts( array (
		'category' => '',
		'quantity'  => '',
	), $atts ) );

	ob_start();

	// wpsimplecalendar_list( $category=$category_att, $quantity=$quantity_att );
	wpsimplecalendar_events_list( $category, $quantity );

	$make_my_calendar_content = ob_get_clean();

	return $make_my_calendar_content;
}

/**
 * @since  1.0
 * Function to display the events in list format
 *
 * To include a list of upcoming events in your template:
 * <?php if(function_exists( 'wpsimplecalendar_list' )) { wpsimplecalendar_list(); } ?>
 */

function wpsimplecalendar_events_list( $category='', $quantity='' ) {
	include( 'includes/list-template.php' );
}

/**
 * @since  1.0
 * Define shortcode for grid view
 * Format: [wpscgrid category="category-slug" location="location-slug"]
 */

add_shortcode( 'wpscgrid', 'wpsimplecalendar_grid_shortcode' );
function wpsimplecalendar_grid_shortcode( $atts ) {
	extract( shortcode_atts( array (
		'category' => '',
		'location' => ''
	), $atts ) );

	// $category_att = $category;

	ob_start();

	// wpsimplecalendar_grid( $category=$category_att );
	wpsimplecalendar_grid( $category, $location );

	$wpsimplecalendar_content = ob_get_clean();

	return $wpsimplecalendar_content;
}

/**
 * @since  1.0
 * Funciton to display the events in a grid format
 *
 * To include the calendar using a grid format in your template:
 * <?php if(function_exists( 'wpsimplecalendar_grid' )) { wpsimplecalendar_grid(); } ?>
 */

function wpsimplecalendar_grid( $category='', $location='' ) {
	include( 'includes/grid-template.php' );
}

/**
 * @since  1.0
 * redirect template for single events pop-up
 */

add_action( 'template_redirect', 'wpsimplecalendar_template_redirect' );
function wpsimplecalendar_template_redirect()
{
    if( is_singular( 'wpscevents' ) )
    {
        include( plugin_dir_path( __FILE__).'/includes/single-wpscevents.php' );
        exit();
    }
}

add_action( 'wp_ajax_wpsimplecalendar-next', 'wpsimplecalendar_handle_next_ajax' );
add_action( 'wp_ajax_nopriv_wpsimplecalendar-next', 'wpsimplecalendar_handle_next_ajax' );
function wpsimplecalendar_handle_next_ajax() {
	// die( print_r( $_POST ) );

	$next_month = (int) $_POST['month'];
	$year		= (int) $_POST['year'];
	$category	= $_POST['category'];
	$location	= $_POST['location'];
	die( json_encode( array( 'title' => date( 'F Y', strtotime( $next_month.'/1/'.$year ) ), 'grid' => wpsimplecalendar_setup_grid( $next_month, $year, $category, $location ) ) ) );
	die(0);
}

add_action( 'wp_ajax_wpsimplecalendar-last', 'wpsimplecalendar_handle_last_ajax' );
add_action( 'wp_ajax_nopriv_wpsimplecalendar-last', 'wpsimplecalendar_handle_last_ajax' );
function wpsimplecalendar_handle_last_ajax() {
	// die( print_r( $_POST ) );

	$last_month = (int) $_POST['month'];
	$year		= (int) $_POST['year'];
	$category	= $_POST['category'];
	$location	= $_POST['location'];
	die( json_encode( array( 'title' => date( 'F Y', strtotime( $last_month.'/1/'.$year ) ), 'grid' => wpsimplecalendar_setup_grid( $last_month, $year, $category, $location ) ) ) );
	die(0);
}

/**
 * @since  1.1.0
 * Function to setup the calendar grid
 */

function wpsimplecalendar_setup_grid( $month, $year, $eventcategory = '', $eventlocation = '' ) {
	$time = current_time( 'timestamp', $gmt = 0 );
	
	$running_day		= date( 'w', mktime( 0, 0, 0, $month, 1, $year ) );
	$days_in_month		= date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
	$start_of_month		= mktime( 0, 0, 0, $month, 1, $year );
	$end_of_month		= mktime( 23, 59, 59, $month, $days_in_month, $year );
	$days_in_this_week	= 1;
	$day_counter		= 0;
	$current			= date( 'j', $time );
	$dates_array		= array();

	global $post;

	$cal_args = array(
		'post_type'			=> 'wpscevents',
		'posts_per_page'	=> -1,
		'orderby'			=> 'meta_value_num',
		'meta_key'			=> 'wpsc_start_date_time',
		'order'				=> 'ASC',
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'		=> 'wpsc_start_date_time',
				'value'		=> $end_of_month,
				'compare'	=> '<=',
   			),
			array(
				'key'		=> 'wpsc_end_date_time',
				'value'		=> $start_of_month,
				'compare'	=> '>=',
   			)
			)
	);

	// if both category and event args are present
	if( strlen( $eventcategory ) && strlen( $eventlocation ) ) {
		$cal_args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'wpsccategory',
				'field' => 'slug',
				'terms' => $eventcategory
			),
			array(
				'taxonomy' => 'wpsclocation',
				'field' => 'slug',
				'terms' => $eventlocation
			)
		);
	} elseif ( strlen( $eventcategory ) ) {
		// If only a category arg is present
		$cal_args['tax_query']  = array(
			array(
				'taxonomy' => 'wpsccategory',
				'field' => 'slug',
				'terms' => $eventcategory
			)
		);
	} elseif ( strlen( $eventlocation ) ) {
		$cal_args['tax_query']  = array(
			array(
				'taxonomy' => 'wpsclocation',
				'field' => 'slug',
				'terms' => $eventlocation
			)
		);
	}
		
	$eventsloop = new WP_Query( $cal_args );
	// event posts loop
	// gets all possible events for this time period and puts it into a structure for easy access later
	$events = $eventbyday = array();
	while ( $eventsloop->have_posts() ) : $eventsloop->the_post();
		$meta = get_post_meta(get_the_ID());
		$classes = get_post_class();
		$event =array('event'=>$eventsloop->post,'meta'=>$meta,'classes'=>$classes);
		$events[] = $event;
	endwhile;

	//now that we're done with the database lets organize this into something the calendar can use for reference
	for($i = 1; $i <= $days_in_month; $i++) {
		foreach($events as $e) {
			$today = mktime( 0, 0, 0, $month, $i, $year );
			$tomorrow = mktime( 0, 0, 0, $month, $i+1, $year );
			if( $e['meta']['wpsc_start_date_time'][0] < $tomorrow
			   && $e['meta']['wpsc_end_date_time'][0] >= $today ) {
				$eventbyday[$i][] = $e;
			}
		}
	}
	// Tables do have their uses...
	$calendar = '<table cellpadding="0" cellspacing="0" class="wpsc-grid" data-month="'.esc_attr( $month ).'" data-year="'.esc_attr( $year ).'" data-category="'.esc_attr( $eventcategory ).'" data-location="'.esc_attr( $eventlocation ).'">';

	// Table headings
	$headings = array(
		'Sun',
		'Mon',
		'Tue',
		'Wed',
		'Thu',
		'Fri',
		'Sat'
	);

	$calendar.= '<tr class="wpsc-grid-row"><td class="wpsc-grid-day-head">' . implode( '</td><td class="wpsc-grid-day-head">', $headings ) . '</td></tr>';

	// Row for week one
	$calendar.= '<tr class="wpsc-grid-row">';

	// Print "blank" days until the first of the current week
	for( $x = 0; $x < $running_day; $x++ ) {
		$calendar.= '<td class="wpsc-grid-day-np">&nbsp;</td>';
		$days_in_this_week++;
	}

	// Keep going with days...
	for( $list_day = 1; $list_day <= $days_in_month; $list_day++ ) {
		if( ( $list_day == $currentday) && ($month == date( 'm' ) ) ) {
			$calendar.= '<td class="wpsc-grid-day current-day">';
		} else {
			$calendar.= '<td class="wpsc-grid-day">';
		}

		// Add in the day number
		$calendar.= '<div class="wpsc-date">' . $list_day . '</div><div class="clear"></div>';

		$calendar.= '<ul class="simple-cal-list">';
			//all the db/query access happened way up there so this is a simple loop and 
			//check to see if our data store has anything for this day
			if ( isset($eventbyday[$list_day]) ) {
				foreach($eventbyday[$list_day] as $e) {
					$classes = join( ' ', $e['classes'] );
					$calendar.= '<li class="' . $classes . '" ><a class="iframe cboxElement" href="'. get_permalink( $e['event']->ID ) .'" rel="bookmark" title="' . get_the_title( $e['event']->ID ) . '">' . get_the_title( $e['event']->ID ) . '</a></li>';
				}
			}
		$calendar.= '</ul>';

		$calendar.= '</td>';

		if($running_day == 6) {
			$calendar.= '</tr>';
			if( ( $day_counter+1 ) != $days_in_month ) {
				$calendar.= '<tr class="wpsc-grid-row">';
			}

			$running_day       = -1;
			$days_in_this_week = 0;
		}

		$days_in_this_week++; $running_day++; $day_counter++;
	}

	// Finish the rest of the days in the week
	if( $days_in_this_week < 8 ) {
		for( $x = 1; $x <= ( 8 - $days_in_this_week ); $x++ ) {
			$calendar.= '<td class="wpsc-grid-day-np">&nbsp;</td>';
		}
	}

	// Final row
	$calendar.= '</tr>';	// End the table, finally!
	$calendar.= '</table>';	// All done, return result
	$jscode = "<script>
   		jQuery( 'a.cboxElement' ).colorbox({height:'400', width:'400', rel:'nofollow'});
		</script>";

	return $calendar . $jscode;
}
