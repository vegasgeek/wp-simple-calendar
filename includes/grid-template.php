<?php
$time = current_time( 'timestamp', $gmt = 0 );

// Setup the date and month navigation
if( isset( $_GET['mnth'] ) ) {
	$month = absint( $_GET['mnth'] );

} else {
	$month = date( 'm', $time );
}

if( isset( $_GET['yr'] ) ) {
	$year = absint( $_GET['yr'] );
} else {
	$year = date( 'Y', $time );
}
$next_month_link		= '<a href="?mnth='.($month != 12 ? $month + 1 : 1).'&yr='.($month != 12 ? $year : $year + 1).'" class="control next">Next Month ></a>';
$previous_month_link	= '<a href="?mnth='.($month != 1 ? $month - 1 : 12).'&yr='.($month != 1 ? $year : $year - 1).'" class="control last">< Last Month</a>';

// Load the date and month navigation
echo '<form id="wpsc-grid-nav" method="get">' . $previous_month_link . ' &nbsp;&nbsp;&nbsp;<strong>' . date('F',mktime(0,0,0,$month,1,$year)) . ' ' . $year . '</strong>&nbsp;&nbsp;&nbsp; ' . $next_month_link . '</form>';


// Load calendar grid
echo wpsimplecalendar_setup_grid( $month, $year, $category, $location );
