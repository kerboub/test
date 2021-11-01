<?php

/**
 *  ThemetechMount: Schedule Box
 */

	$params = array_merge(
		themetechmount_vc_heading_params(),
		array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Extra class name', 'boldman' ),
				'param_name'  => 'el_class',
				'description' => esc_attr__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'boldman' ),
			),
			array(
			'type' => 'param_group',
			'heading' => esc_attr__( 'Pricelist', 'boldman' ),
			'param_name' => 'pricelist',
			'group'       => esc_attr__( 'Pricelist', 'boldman' ),
			'description' => esc_attr__( 'Set Service price', 'boldman' ),
			'value' => urlencode( json_encode( array(
				array(
					'service_name' => esc_attr__( 'Developemnt', 'boldman' ),
					'price' => '$30',
				),
			
			))),
			'params' => array(
				array(
						'type'        => 'textarea',
						'heading'     => esc_attr__( 'Service Name', 'boldman' ),
						'param_name'  => 'service_name',
						'description' => esc_attr__( 'Fill Service information here', 'boldman' ),
						// 'value'       => '',
						'group'       => esc_attr__( 'Pricelist', 'boldman' ),
						'admin_label' => true,
						'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
				array(
						'type'        => 'textarea',
						'heading'     => esc_attr__( 'Price', 'boldman' ),
						'param_name'  => 'price',
						// 'value'       => '',
						'description' => esc_attr__( 'Fill Price details here eg: $30', 'boldman' ),
						'group'       => esc_attr__( 'Pricelist', 'boldman' ),
						'admin_label' => true,
						'edit_field_class' => 'vc_col-sm-6 vc_column',
				),
			),
		),	
		)
	);

	global $tm_vc_custom_element_pricelistbox;
	$tm_vc_custom_element_pricelistbox = $params;

	vc_map( array(
		'name'        => esc_attr__( 'ThemetechMount Pricelist Box', 'boldman' ),
		'base'        => 'tm-pricelistbox',
		"class"    => "",
		"icon"        => "icon-themetechmount-vc",
		'category'    => esc_attr__( 'ThemetechMount Special Elements', 'boldman' ),
		'params'      => $params,
	) );