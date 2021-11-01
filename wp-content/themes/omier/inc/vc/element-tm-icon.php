<?php

/* Options for ThemetechMount Icon */


/*
 * Icon Element
 * @since 4.4
 */

/**
 *  Show selected icon library only
 */
global $boldman_theme_options;

// Temporary new list of icon libraries
$icon_library_array = array( // all icon library list array
	'themify'        => array( esc_attr__( 'Themify icons', 'boldman' ),   'themifyicon ti-thumb-up'),
	'linecons'       => array( esc_attr__( 'Linecons', 'boldman' ), 'vc_li vc_li-star'),
	'kw_boldman'   => array( esc_attr__( 'Special Icons', 'boldman' ), 'flaticon-honey'),
);


$icon_library = array();
if( isset($boldman_theme_options['icon_library']) && is_array($boldman_theme_options['icon_library']) && count($boldman_theme_options['icon_library'])>0 ){
	// if selected icon library
	foreach( $boldman_theme_options['icon_library'] as $i_library ){
		$icon_library[$i_library] = $icon_library_array[$i_library];
	}
}

$icon_element_array  = array();
$icon_dropdown_array = array( esc_attr__( 'Font Awesome', 'boldman' )    => 'fontawesome' );   // Font Awesome icons
$icon_dropdown_array[ esc_attr__( 'Special Icons', 'boldman' ) ] = 'kw_boldman'; // Special icons

if( is_array($icon_library) && count($icon_library)>0 ){
foreach( $icon_library as $library_id=>$library ){
	
	$icon_dropdown_array[$library[0]] = $library_id;
	
	$icon_element_array[]  = array(
		'type'        => 'themetechmount_iconpicker',
		'heading'     => esc_attr__( 'Icon', 'boldman' ),
		'param_name'  => 'icon_'.$library_id,
		'value'       => $library[1], // default value to backend editor admin_label
		'settings'    => array(
			'emptyIcon'    => false, // default true, display an "EMPTY" icon?
			'type'         => $library_id,
		),
		'dependency'  => array(
			'element'   => 'type',
			'value'     => $library_id,
		),
		'description' => esc_attr__( 'Select icon from library.', 'boldman' ),
		'edit_field_class' => 'vc_col-sm-9 vc_column',
	);	
}
}
/* Select icon library code end here */

	$new_iclass              = '';
	$icon_library = themetechmount_get_option('icon_library');
	
	if( is_array($icon_library) && in_array('kw_boldman', $icon_library) ){
		$new_iclass = 'hide_1cpicker';
	}	


// All icon related elements
$icon_elements = array_merge(
	array(
		array(
			'type'        => 'dropdown',
			'heading'     => esc_attr__( 'Icon library', 'boldman' ),
			'value'       => $icon_dropdown_array,
			'std'         => '',
			'admin_label' => true,
			'param_name'  => 'type',
			'description' => esc_attr__( 'Select icon library.', 'boldman' ),
			'edit_field_class' => 'vc_col-sm-3 vc_column',
		)
	),
	array(
		array(  // Font Awesome icons
			'type'       => 'themetechmount_iconpicker',
			'heading'    => esc_attr__( 'Icon', 'boldman' ),
			'param_name' => 'icon_fontawesome',
			'value'      => 'fa fa-thumbs-o-up', // default value to backend editor admin_label
			'settings'   => array(
				'emptyIcon'    => false, // default true, display an "EMPTY" icon?
				'type'         => 'fontawesome',
			),
			'dependency' => array(
				'element'  => 'type',
				'value'    => 'fontawesome',
			),
			'description' => esc_attr__( 'Select icon from library.', 'boldman' ),
			'edit_field_class' => 'vc_col-sm-9 vc_column',
		),
	),
	
	array(
		array(  // Boldman special icons
			'type'       => 'themetechmount_iconpicker',
			'heading'    => esc_attr__( 'Icon', 'boldman' ),
			'param_name' => 'icon_kw_boldman',
			'value'      => 'flaticon-honey', // default value to backend editor admin_label
			'settings'   => array(
				'emptyIcon'    => false, // default true, display an "EMPTY" icon?
				'type'         => 'kw_boldman',
			),
			'dependency' => array(
				'element'  => 'type',
				'value'    => 'kw_boldman',
			),
			'description' => esc_attr__( 'Select icon from library.', 'boldman' ),
			'edit_field_class' => 'vc_col-sm-9 vc_column '.$new_iclass.'',
		)
	),
	$icon_element_array	
);

$allparams = array(
	array(
		'type'        => 'dropdown',
		'heading'     => esc_attr__( 'Icon color', 'boldman' ),
		'param_name'  => 'color',
		'value'       => array_merge( 
			themetechmount_getVcShared( 'colors' ),
			array(
				esc_attr__( 'Classic Grey', 'boldman' )      => 'bar_grey',
				esc_attr__( 'Classic Blue', 'boldman' )      => 'bar_blue',
				esc_attr__( 'Classic Turquoise', 'boldman' ) => 'bar_turquoise',
				esc_attr__( 'Classic Green', 'boldman' )     => 'bar_green',
				esc_attr__( 'Classic Orange', 'boldman' )    => 'bar_orange',
				esc_attr__( 'Classic Red', 'boldman' )       => 'bar_red',
				esc_attr__( 'Classic Black', 'boldman' )     => 'bar_black',
			),
			array( esc_attr__( 'Custom color', 'boldman' ) => 'custom' )
		),
		'std'         => 'skincolor',
		'description' => esc_attr__( 'Select icon color.', 'boldman' ),
		'param_holder_class' => 'tm_vc_colored-dropdown',
	),
	array(
		'type'        => 'colorpicker',
		'heading'     => esc_attr__( 'Custom color', 'boldman' ),
		'param_name'  => 'custom_color',
		'description' => esc_attr__( 'Select custom icon color.', 'boldman' ),
		'dependency'  => array(
			'element'   => 'color',
			'value'     => 'custom',
		),
	),
	array(
		'type'        => 'dropdown',
		'heading'     => esc_attr__( 'Background shape', 'boldman' ),
		'param_name'  => 'background_style',
		'value'       => array(
			esc_attr__( 'None', 'boldman' ) => '',
			esc_attr__( 'Circle', 'boldman' ) => 'rounded',
			esc_attr__( 'Square', 'boldman' ) => 'boxed',
			esc_attr__( 'Rounded', 'boldman' ) => 'rounded-less',
			esc_attr__( 'Outline Circle', 'boldman' ) => 'rounded-outline',
			esc_attr__( 'Outline Square', 'boldman' ) => 'boxed-outline',
			esc_attr__( 'Outline Rounded', 'boldman' ) => 'rounded-less-outline',
		),
		'std'         => '',
		'description' => esc_attr__( 'Select background shape and style for icon.', 'boldman' ),
		'param_holder_class' => 'tm-simplify-textarea',
	),
	array(
		'type'        => 'dropdown',
		'heading'     => esc_attr__( 'Background color', 'boldman' ),
		'param_name'  => 'background_color',
		'value'       => array_merge( array( esc_attr__( 'Transparent', 'boldman' ) => 'transparent' ), themetechmount_getVcShared( 'colors' ), array( esc_attr__( 'Custom color', 'boldman' ) => 'custom' ) ),
		'std'         => 'grey',
		'description' => esc_attr__( 'Select background color for icon.', 'boldman' ),
		'param_holder_class' => 'tm_vc_colored-dropdown',
		'dependency'  => array(
			'element'   => 'background_style',
			'not_empty' => true,
		),
	),
	array(
		'type'        => 'colorpicker',
		'heading'     => esc_attr__( 'Custom background color', 'boldman' ),
		'param_name'  => 'custom_background_color',
		'description' => esc_attr__( 'Select custom icon background color.', 'boldman' ),
		'dependency'  => array(
			'element'   => 'background_color',
			'value'     => 'custom',
		),
	),
	array(
		'type'        => 'dropdown',
		'heading'     => esc_attr__( 'Size', 'boldman' ),
		'param_name'  => 'size',
		'value'       => array_merge( themetechmount_getVcShared( 'sizes' ), array( 'Extra Large' => 'xl' ) ),
		'std'         => 'md',
		'description' => esc_attr__( 'Icon size.', 'boldman' )
	),
	array(
		'type'       => 'dropdown',
		'heading'    => esc_attr__( 'Icon alignment', 'boldman' ),
		'param_name' => 'align',
		'value'      => array(
			esc_attr__( 'Left', 'boldman' )   => 'left',
			esc_attr__( 'Right', 'boldman' )  => 'right',
			esc_attr__( 'Center', 'boldman' ) => 'center',
		),
		'std'         => 'left',
		'description' => esc_attr__( 'Select icon alignment.', 'boldman' ),
	),
	array(
		'type'        => 'vc_link',
		'heading'     => esc_attr__( 'URL (Link)', 'boldman' ),
		'param_name'  => 'link',
		'description' => esc_attr__( 'Add link to icon.', 'boldman' )
	),
	vc_map_add_css_animation(),
	themetechmount_vc_ele_extra_class_option(),
	themetechmount_vc_ele_css_editor_option(),
);

// All params
$params = array_merge( $icon_elements, $allparams );

	
global $tm_sc_params_icon;
$tm_sc_params_icon = $params;	

vc_map( array(
	'name'     => esc_attr__( 'ThemetechMount Icon', 'boldman' ),
	'base'     => 'tm-icon',
	'icon'     => 'icon-themetechmount-vc',
	'category' => array( esc_attr__( 'ThemetechMount Special Elements', 'boldman' ) ),
	'admin_enqueue_css' => array(get_template_directory_uri().'/assets/themify-icons/themify-icons.css', get_template_directory_uri().'/assets/twemoji-awesome/twemoji-awesome.css' ),
	'params'   => $params,
	'js_view'  => 'VcIconElementView_Backend',
) );