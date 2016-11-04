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
