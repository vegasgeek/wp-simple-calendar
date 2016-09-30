<?php

	// event posts settings
	if( isset( $wp_query ) ) {
		$query_temp = $wp_query;
	}
	$wp_query = null;
	
	global $post;
	global $paged;
	
	//WordPress has it's own date/time handling functions.  Use them, or all dates will be output relative to server time, not Wordpress datetime settings.
	$time = current_time( 'timestamp', $gmt = 0 );
	$currentdate = date('Y-m-d', $time);
	
	/* Because dates are being stored in postmeta as strings, we need to explictly tell MySQL to think of them as dates in order 
	 * to do a comparison.  To do this, we have to hook into WordPress' filters and alter the query before it runs.  We replace
	 * the default MySQL CAST function with the MySQL STR_TO_DATE function.  The query will the run correctly and compare dates.
	 */
	function filter_posts_where( $where ) {
		global $wpdb;
		$where = str_replace("CAST({$wpdb->postmeta}.meta_value AS CHAR)", "STR_TO_DATE( {$wpdb->postmeta}.meta_value, '%m/%d/%Y' )", $where);
		return $where;
	}
	add_filter( 'posts_where' , 'filter_posts_where' );
		
	$query_args = array(
				
		'post_type'        => 'wpscevents',
		'orderby'          => 'meta_value',
		'meta_key'         => 'wpsc_start_date',
		'meta_value'       => $currentdate,
		'meta_compare'     => '>=',
		'order'            => 'ASC',
		'wpsccategory' 	   => $category,
		'posts_per_page'   => $quantity,
		'paged'            => $paged
		
	);
	
	$wp_query = new WP_Query( $query_args );
	
	//print_r($wp_query);

	// event posts loop
	?>
	<ul>
	<?php 
	while ( $wp_query->have_posts() ) : $wp_query->the_post();
	
		$startdate = get_post_meta( $post->ID, "wpsc_start_date", $single = true );
		
		$startstring = strtotime($startdate);
		$starttext = date('M j, Y', $startstring);	
		?>
		<li><strong><?php echo $starttext; ?></strong> - <?php the_title(); ?></li>				
	<?php endwhile; ?>
	</ul>
	
	<?php
	if( isset( $query_temp ) ) {
		$wp_query = null; $wp_query = $query_temp;
	}

	wp_reset_postdata();
	