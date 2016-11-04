<?php
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

// Move query here, pull all events at once
// Daily events loop
		global $post;

		$cal_args = array(
			'post_type'			=> 'wpscevents',
			'posts_per_page'	=> -1,
			'orderby'			=> 'meta_value_num',
			'meta_key'			=> 'wpsc_start_date_time',
			'order'				=> 'ASC',
			'meta_query' => array(
				array(
					'key'		=> 'wpsc_start_date_time',
					'value'		=> array( $start_of_month, $end_of_month ),
					'compare'	=> 'BETWEEN',
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
		while ( $eventsloop->have_posts() ) : $eventsloop->the_post();

			echo $post->post_title . '<br />';
		endwhile;





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

// new location to just do a display


// removed loop
		$calendar.= '<ul class="simple-cal-list">';
		while ( $eventsloop->have_posts() ) : $eventsloop->the_post();

			$startdate  = date( 'Y-m-d', strtotime( get_post_meta( $post->ID, "wpsc_start_date", $single = true ) ) ) ;
			$list_month = date( 'm',mktime(0,0,0,$month,1,$year));
			$listdate = $year . '-' . $list_month . '-' . $list_day;

			if ( $listdate == $startdate ) {
				$classes = join( ' ', get_post_class() );
				$calendar.= '<li class="' . $classes . '" ><a class="iframe cboxElement" href="'. get_permalink( $post->ID ) .'" rel="bookmark" title="' . get_the_title( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a></li>';
			}
		endwhile;
		$calendar.= '</ul>';

		wp_reset_postdata();

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