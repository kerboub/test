<?php
/*
 * Custom PHP code for child theme will be here
 */

/** Adding child theme's style.css **/
function tm_boldman_child_style_css(){
	wp_enqueue_style( 'boldman-child-style', get_stylesheet_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'tm_boldman_child_style_css', 18 );