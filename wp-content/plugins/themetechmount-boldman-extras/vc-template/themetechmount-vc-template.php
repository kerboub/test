<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'WPBakeryVisualComposerAbstract' ) ) {
	require_once TMTE_DIR . 'vc-template/themetechmount-default-templates.php';

	add_action( 'admin_print_scripts-post.php', 'themetechmount_templates_scripts_styles' );
	add_action( 'admin_print_scripts-post-new.php', 'themetechmount_templates_scripts_styles' );
}

/* Enqueue script for VC templates. */

function themetechmount_templates_scripts_styles() {
	$post_type = get_post_type();

	if ( vc_check_post_type( $post_type ) ) {		
		wp_enqueue_script( 'themetechmount_templates_js', TMTE_URI . '/vc-template/vc-templates.min.js', array( 'jquery' ) );			
	}
}