<?php

	// event posts settings
	if( isset( $wp_query ) ) {
		$query_temp = $wp_query;
	}
	$wp_query = null;
	
	global $post;
		
	function filter_posts_where( $where ) {
		global $wpdb;
		$where = str_replace("CAST({$wpdb->postmeta}.meta_value AS CHAR)", "STR_TO_DATE( {$wpdb->postmeta}.meta_value, '%m/%d/%Y' )", $where);
		return $where;
	}
	add_filter( 'posts_where' , 'filter_posts_where' );
	
	if( $quantity < 1 ) {
		$quantity = -1;
	}

	$query_args = array(
				
		'post_type'        => 'wpscevents',
		'orderby'          => 'meta_value',
		'meta_key'         => 'wpsc_start_date_time',
		'meta_value'       => date( 'U' ),
		'meta_compare'     => '>=',
		'order'            => 'ASC',
		'wpsccategory' 	   => $category,
		'posts_per_page'   => $quantity
		
	);
	
	$wp_query = new WP_Query( $query_args );
	
	echo '<ul>';

	while ( $wp_query->have_posts() ) : $wp_query->the_post();
		echo '<li><strong>' . date_i18n( get_option( 'date_format' ), get_post_meta( $post->ID, "wpsc_start_date_time", $single = true ) ) . '</strong> - ' . get_the_title() . '</li>';
	endwhile;

	echo '</ul>';
	
	if( isset( $query_temp ) ) {
		$wp_query = null; $wp_query = $query_temp;
	}

	wp_reset_postdata();
	