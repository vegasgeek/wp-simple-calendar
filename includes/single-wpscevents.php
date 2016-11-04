<?php
while ( have_posts() ) {
	the_post();

	echo '<h1>' . get_the_title() .'</h1>';
	// date
		// See if event is single day or multiple day
		if( !strlen( get_post_meta( get_the_ID(), 'wpsc_end_date', true ) ) ) {
			// single day event
			$date = date_i18n( get_option('date_format'), strtotime( get_post_meta( get_the_ID(), 'wpsc_start_date_time', true ) ) );
		} else {
			// multi day event
			$date = date_i18n( get_option('date_format'), strtotime( get_post_meta( get_the_ID(), 'wpsc_start_date_time', true ) ) );
			$date .= ' - ';
			$date .= date_i18n( get_option('date_format'), strtotime( get_post_meta( get_the_ID(), 'wpsc_end_date_time', true ) ) );
		}

	// time
		// see if there's a time
		if( strlen( get_post_meta( get_the_ID(), 'wpsc_start_date_time', true ) ) ) {
			$time = '<br />' . date( 'g:i A', strtotime( get_post_meta( get_the_ID(), 'wpsc_start_date_time', true ) ) );
		}

		// see if there's a time
		if( strlen( get_post_meta( get_the_ID(), 'wpsc_end_time', true ) ) ) {
			$time .= ' - ' . get_post_meta( get_the_ID(), 'wpsc_end_time', true );
		}

	// RSVP
		// see if there is a registration URL
		if( strlen( get_post_meta( get_the_ID(), 'wpsc_url', true ) ) ) {
			$rsvp = '<br />';
			// see if there is reg text
			if( !strlen( get_post_meta( get_the_ID(), 'wpsc_reg_text', true ) ) ) {
				$rsvp .= '<a href="'. get_post_meta( get_the_ID(), 'wpsc_url', true ) .'" target="_blank">Click Here</a>';
			} else {
				$rsvp .= '<a href="'. get_post_meta( get_the_ID(), 'wpsc_url', true ) .'" target="_blank">' . get_post_meta( get_the_ID(), 'wpsc_reg_text', true ) . '</a>';
			}
		}

		echo '<p><strong>' . $date . '</strong>';
		if( isset( $time ) )
			echo $time;

		if( isset( $rsvp ) )
			echo $rsvp;
		echo '</p>';

		the_content();

	// Location
		$locations = get_the_terms( get_the_ID(), 'wpsclocation' );
		
		if( isset( $locations ) && is_array( $locations ) ) {
			foreach ( $locations as $loc ) {
				$location = $loc->name;
				if ( strlen( $loc->description ) ) {
					$location .= '<br />' . $loc->description;
				}
			}
			echo '<p><strong>Location</strong><br />' . $location .'</p>';
		}
}
