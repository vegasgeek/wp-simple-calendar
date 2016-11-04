<?php
/**
 * @since  2.0
 * 
 * Upgrade routine to convert data to new style
 * 
 */
register_activation_hook( __FILE__, 'wpsc_upgrade_routine' );
// A secondary way of hitting the routine in case of upgrade
add_action( 'upgrader_process_complete', 'wpsc_upgrade_routine', 10, 2 );

function wpsc_upgrade_routine() {
	$options = get_option( 'wpsc_options' );

	if( !isset( $options['wpsc_version'] ) ) {
		// 1.x or older
		// Convert old style event data to new
		
		$args = array(
			'post_type'			=> 'wpscevents',
			'posts_per_page'	=> -1,
		);

		$the_query = new WP_Query( $args );
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				// Setup start date
				if( get_post_meta( get_the_id(), 'wpsc_start_date', true ) ) {
					$old_start_date = get_post_meta( get_the_id(), 'wpsc_start_date', true );
				}
				if( get_post_meta( get_the_id(), 'wpsc_start_time', true ) ) {
					$old_start_time = get_post_meta( get_the_id(), 'wpsc_start_time', true );
				}

				if( isset( $old_start_time ) ) {
					$old_start_date_time = $old_start_date . ' ' . $old_start_time;
				} else {
					$old_start_date_time = $old_start_date;
				}

				$new_start_date_string = date( "U", strtotime( $old_start_date_time ) );

				update_post_meta( get_the_id(), 'wpsc_start_date_time', $new_start_date_string );

				// Setup end date
				if( get_post_meta( get_the_id(), 'wpsc_end_date', true ) ) {
					$old_end_date = get_post_meta( get_the_id(), 'wpsc_end_date', true );
				
					if( get_post_meta( get_the_id(), 'wpsc_end_time', true ) ) {
						$old_end_time = get_post_meta( get_the_id(), 'wpsc_end_time', true );
					}

					if( isset( $old_end_time ) ) {
						$old_end_date_time = $old_end_date . ' ' . $old_end_time;
					} else {
						$old_end_date_time = $old_end_date;
					}

					$old_end_date_string = date( "U", strtotime( $old_end_date_time ) );

					update_post_meta( get_the_id(), 'wpsc_end_date_time', $old_end_date_string );
				} else {
					update_post_meta( get_the_id(), 'wpsc_end_date_time', $new_start_date_string );
				}

				// See if there was a start time set
				// Set "All day" setting accordingly
				if( isset( $old_start_time ) ) {
					update_post_meta( get_the_id(), 'wpsc_all_day_event', 1 );
				} else {
					update_post_meta( get_the_id(), 'wpsc_all_day_event', 0 );
				}

				// update version
				$options['wpsc_version'] = WPSC_VERSION;
				update_option( 'wpsc_options', $options );
			}
		}
	}
}