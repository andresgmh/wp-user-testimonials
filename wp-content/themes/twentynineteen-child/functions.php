<?php

/**
 * Enqueue child scripts
 */
if ( ! function_exists( 'twentynineteen_child_scripts' ) ) {
	function twentynineteen_child_scripts() {
		wp_enqueue_style( 'twentynineteen-child-style', get_template_directory_uri() . "/style.css" );
	}

}

add_action( 'wp_enqueue_scripts', 'twentynineteen_child_scripts' );

?>


