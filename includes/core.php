<?php
// define ( 'makemycalendar_IS_INSTALLED', 1 );
// define ( 'makemycalendar_VERSION', '0.1' );
// define ( 'makemycalendar_DB_VERSION', '1' );

/**
 * @since  1.0
 * Load admin scripts
 */
// add_action( 'admin_init', 'load_wpsimplecalendar_scripts' );
function load_wpsimplecalendar_scripts() {
  global $pagenow, $typenow;
  if ( empty( $typenow ) && !empty( $_GET['post'] ) ) {
    $post = get_post( $_GET['post'] );
    $typenow = $post->post_type;
  }
  if (is_admin() && $typenow=='makemycalendar') {
    if ($pagenow=='post-new.php' OR $pagenow=='post.php') { 
      //require ( dirname(__FILE__) .'/makemycalendar-cssjs.php' );
    }
  }
}
