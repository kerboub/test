<?php

/* Options */

$allParams1 =  array(
	array(
		'type'			=> 'textfield',
		'holder'		=> 'div',
		'class'			=> '',
		'heading'		=> esc_attr__('Header (optional)', 'boldman'),
		'param_name'	=> 'title',
		'std'			=> esc_attr__('Title Text', 'boldman'),
		'description'	=> esc_attr__('Enter text for the title. Leave blank if no title is needed.', 'boldman')
	),
	array(
		"type"			=> "dropdown",
		"holder"		=> "div",
		"class"			=> "",
		"heading"		=> esc_attr__("Design", 'boldman'),
		"param_name"	=> "view",
		"description"	=> esc_attr__('Select box design.' , 'boldman'),
		'value' => array(
			esc_attr__( 'Top Center icon', 'boldman' )           => 'topicon',
			esc_attr__( 'Left icon', 'boldman' )                 => 'lefticon',
			esc_attr__( 'Right icon', 'boldman' )                => 'righticon',
			esc_attr__( 'Circle Progress Style', 'boldman' ) 	 => 'circle-progress',
			esc_attr__( 'Left icon Style2', 'boldman' ) 		 => 'lefticon-style2',
			esc_attr__( 'Behind Center Icon', 'boldman' ) 		 => 'behindicon',
		),
		'std'           => 'topicon',
	),
	array(
		'type'       => 'checkbox',
		'heading'    => esc_attr__( 'Add icon?', 'boldman' ),
		'param_name' => 'add_icon',
		'std'        => 'true',
		'edit_field_class'	=> 'vc_col-sm-6 vc_column',
		'dependency'  => array(
					'element'            => 'view',
					'value_not_equal_to' => array( 'circle-progress' ),
				),
	),
	array(
		'type'       => 'checkbox',
		'heading'    => esc_attr__( 'Add border?', 'boldman' ),
		'param_name' => 'add_border',
		'std'        => 'false',
		'edit_field_class'	=> 'vc_col-sm-6 vc_column',
		'dependency'  => array(
					'element'            => 'view',
					'value_not_equal_to' => array( 'circle-progress' ),
				),
	),
	array(
		'type'       => 'dropdown',
		'heading'    => esc_attr__( 'Circle fill color', 'boldman' ),
		'param_name' => 'circle_fill_color',
		'value'      => array(
				esc_attr__( 'Skincolor', 'boldman' )      => 'skincolor',
				esc_attr__( 'Dark Grey', 'boldman' )      => '20292f',
				esc_attr__( 'White', 'boldman' ) 		   => '#fff',
			),
		'std'         => 'skincolor',
		'description' => esc_attr__( 'Select circle fill color.', 'boldman' ),
		'param_holder_class' => 'tm_vc_colored-dropdown',
		'edit_field_class'   => 'vc_col-sm-6 vc_column',
		'dependency'  => array(
					'element'            => 'view',
					'value_not_equal_to' => array( 'topicon','lefticon','lefticon-style2','righticon','lefticon-border','righticon-border','behindicon' ),
				),
	),
	array(
		'type'       => 'dropdown',
		'heading'    => esc_attr__( 'Circle empty color', 'boldman' ),
		'param_name' => 'circle_empty_color',
		'value'      => array(
				esc_attr__( 'Skincolor', 'boldman' )      => 'skincolor',
				esc_attr__( 'Dark Grey', 'boldman' )      => '20292f',
				esc_attr__( 'White', 'boldman' ) 		   => 'fff',
			),
		'std'         => '20292f',
		'description' => esc_attr__( 'Select circle empty color.', 'boldman' ),
		'param_holder_class' => 'tm_vc_colored-dropdown',
		'edit_field_class'   => 'vc_col-sm-6 vc_column',
		'dependency'  => array(
					'element'            => 'view',
					'value_not_equal_to' => array( 'topicon','lefticon','lefticon-style2','righticon','lefticon-border','righticon-border','behindicon'),
				),
	),
);


$icons_params = vc_map_integrate_shortcode( 'tm-icon', 'i_', '', array(
	'include_only_regex' => '/^(type|icon_\w*)/',
	// we need only type, icon_fontawesome, icon_blabla..., NOT color and etc
), array(
	'element' => 'add_icon',
	'value' => 'true',
) );

$icons_params_new = array();

/* Adding class for two column */
foreach( $icons_params as $param ){
	$icons_params_new[] = $param;
}

$allParams2 = array(
			array(
				'type'				=> 'textfield',
				'holder'			=> 'div',
				'class'				=> '',
				'heading'			=> esc_attr__('Rotating Number', 'boldman'),
				'param_name'		=> 'digit',
				'std'				=> '100',
				'description'		=> esc_attr__('Enter rotating number digit here.', 'boldman'),
			),
			array(
				'type'				=> 'textfield',
				'holder'			=> 'div',
				'heading'			=> esc_attr__('Text Before Number', 'boldman'),
				'param_name'		=> 'before',
				'description'		=> esc_attr__('Enter text which appear just before the rotating numbers.', 'boldman'),
				'edit_field_class'	=> 'vc_col-sm-6 vc_column',
			),
			array(
				"type"			=> "dropdown",
				"holder"		=> "div",
				"heading"		=> esc_attr__("Text Style",'boldman'),
				"param_name"	=> "beforetextstyle",
				"description"	=> esc_attr__('Select text style for the text.', 'boldman') . '<br>' . esc_attr__('Superscript text appears half a character above the normal line, and is rendered in a smaller font.','boldman') . '<br>' . esc_attr__('Subscript text appears half a character below the normal line, and is sometimes rendered in a smaller font.','boldman'),
				'value' => array(
					esc_attr__( 'Superscript', 'boldman' ) => 'sup',
					esc_attr__( 'Subscript', 'boldman' )   => 'sub',
					esc_attr__( 'Normal', 'boldman' )      => 'span',
				),
				'std' => 'sup',
				'edit_field_class'	=> 'vc_col-sm-6 vc_column',
			),
			array(
				'type'				=> 'textfield',
				'holder'			=> 'div',
				'class'				=> '',
				'heading'			=> esc_attr__('Text After Number', 'boldman'),
				'param_name'		=> 'after',
				'description'		=> esc_attr__('Enter text which appear just after the rotating numbers.', 'boldman'),
				'edit_field_class'	=> 'vc_col-sm-6 vc_column',
			),
			array(
				"type"			=> "dropdown",
				"holder"		=> "div",
				"class"			=> "",
				"heading"		=> esc_attr__("Text Style",'boldman'),
				"param_name"	=> "aftertextstyle",
				"description"	=> esc_attr__('Select text style for the text.', 'boldman') . '<br>' . esc_attr__('Superscript text appears half a character above the normal line, and is rendered in a smaller font.','boldman') . '<br>' . esc_attr__('Subscript text appears half a character below the normal line, and is sometimes rendered in a smaller font.','boldman'),
				'value' => array(
					esc_attr__( 'Superscript', 'boldman' ) => 'sup',
					esc_attr__( 'Subscript', 'boldman' )   => 'sub',
					esc_attr__( 'Normal', 'boldman' )      => 'span',
				),
				'std' => 'sub',
				'edit_field_class'	=> 'vc_col-sm-6 vc_column',
			),
			array(
				'type'			=> 'textfield',
				'holder'		=> 'div',
				'class'			=> '',
				'heading'		=> esc_attr__('Rotating digit Interval', 'boldman'),
				'param_name'	=> 'interval',
				'std'			=> '5',
				'description'	=> esc_attr__('Enter rotating interval number here.', 'boldman')
			)
);

// merging all options
$params = array_merge( $allParams1, $icons_params_new, $allParams2 );

// merging extra options like css animation, css options etc
$params = array_merge(
	$params,
	array( vc_map_add_css_animation() ),
	array( themetechmount_vc_ele_extra_class_option() ),
	array( themetechmount_vc_ele_css_editor_option() )
);

global $tm_sc_params_facts_in_digits;
$tm_sc_params_facts_in_digits = $params;

vc_map( array(
	'name'		=> esc_attr__( 'ThemetechMount Facts in digits', 'boldman' ),
	'base'		=> 'tm-facts-in-digits',
	'class'		=> '',
	'icon'		=> 'icon-themetechmount-vc',
	'category'	=> esc_attr__( 'ThemetechMount Special Elements', 'boldman' ),
	'params'	=> $params
) );