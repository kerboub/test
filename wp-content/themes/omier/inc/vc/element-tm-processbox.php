<?php

/**
 *  ThemetechMount: Process Box
 */

	$allParams =
		array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Extra class name', 'boldman' ),
				'param_name'  => 'el_class',
				'description' => esc_attr__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'boldman' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Box Image size', 'boldman' ),
				'param_name'  => 'boximg_size',
				'value'			=> 'full',
				'description' => esc_attr__( 'Enter image size (Example: "thumbnail", "medium", "large", "full"). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'boldman' ),
				'group'       => esc_attr__( 'Content', 'boldman' ),
			),
			array(
			'type' => 'param_group',
			'heading' => esc_attr__( 'Box Content', 'boldman' ),
			'param_name' => 'box_content',
			'group'       => esc_attr__( 'Content', 'boldman' ),
			'description' => esc_attr__( 'Set box content', 'boldman' ),
			'params' => array(
				array(
						'type'        => 'attach_image',
						'heading'     => esc_attr__( 'Box Image', 'boldman' ),
						'param_name'  => 'static_boximage',
						'description' => esc_attr__( 'Select image', 'boldman' ),
						'group'       => esc_attr__( 'Content', 'boldman' ),
						'admin_label' => true,
						'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'Box Title', 'boldman' ),
						'param_name'  => 'static_boxtitle',
						'description' => esc_attr__( 'Enter text used as title', 'boldman' ),
						'group'       => esc_attr__( 'Content', 'boldman' ),
						'admin_label' => true,
				),
				array(
						'type'        => 'textarea',
						'heading'     => esc_attr__( 'Box Content', 'boldman' ),
						'param_name'  => 'static_boxcontent',
						'description' => esc_attr__( 'Enter box content', 'boldman' ),
						'group'       => esc_attr__( 'Content', 'boldman' ),
						'admin_label' => true,
				),				
			),
		),
			
	);
	
/**
 * Heading Element
 */
$heading_element = vc_map_integrate_shortcode( 'tm-heading', '', '',
	array(
		'exclude' => array(
			'el_class',
			'css',
			'css_animation'
		),
	)
);

$params    = array_merge( $heading_element, $allParams );
	
	global $tm_vc_custom_element_processbox;
	$tm_vc_custom_element_processbox = $params;
	
	vc_map( array(
		'name'        => esc_attr__( 'ThemetechMount Process Box', 'boldman' ),
		'base'        => 'tm-processbox',
		"class"    => "",
		"icon"        => "icon-themetechmount-vc",
		'category'    => esc_attr__( 'ThemetechMount Special Elements', 'boldman' ),
		'params'      => $params,
	) );