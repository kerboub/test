<?php

/* Options */

// Getting social list
$global_social_list = themetechmount_shared_social_list();

$sociallist = array_merge(
	array('' => esc_attr__('Select service','boldman')),
	$global_social_list,
	array('rss'     => 'Rss Feed')
);
$sociallist = array_flip($sociallist);


$params = array_merge(
	themetechmount_vc_heading_params(),
	array(
		array(
			'type'        => 'param_group',
			'heading'     => esc_attr__( 'Social Services List', 'boldman' ),
			'param_name'  => 'socialservices',
			'description' => esc_attr__( 'Select social service and add URL for it.', 'boldman' ).'<br><strong>'.esc_attr__('NOTE:','boldman').'</strong> '.esc_attr__("You don't need to add URL if you are selecting 'RSS' in the social service",'boldman'),
			'group'       => esc_attr__( 'Social Services', 'boldman' ),
			'params'     => array(
				array( // First social service
					'type'        => 'dropdown',
					'heading'     => esc_attr__( 'Select Social Service', 'boldman' ),
					'param_name'  => 'servicename',
					'std'         => 'none',
					'value'       => $sociallist,
					'description' => esc_attr__( 'Select social service', 'boldman' ),
					'group'       => esc_attr__( 'Social Services', 'boldman' ),
					'admin_label' => true,
					'edit_field_class' => 'vc_col-sm-4 vc_column',
				),
				array(
					'type'        => 'textarea',
					'heading'     => esc_attr__( 'Social URL', 'boldman' ),
					'param_name'  => 'servicelink',
					'dependency'  => array(
						'element'            => 'servicename',
						'value_not_equal_to' => array( 'rss' )
					),
					'value'       => '',
					'description' => esc_attr__( 'Fill social service URL', 'boldman' ),
					'group'       => esc_attr__( 'Social Services', 'boldman' ),
					'admin_label' => true,
					'edit_field_class' => 'vc_col-sm-8 vc_column',
				),
			),
		),
		array( // First social service
			'type'        => 'dropdown',
			'heading'     => esc_attr__( 'Select column', 'boldman' ),
			'param_name'  => 'column',
			'value'       => array(
				esc_attr__('One column','boldman')   => 'one',
				esc_attr__('Two column','boldman')   => 'two',
				esc_attr__('Three column','boldman') => 'three',
				esc_attr__('Four column','boldman')  => 'four',
				esc_attr__('Five column','boldman')  => 'five',
				esc_attr__('Six column','boldman')   => 'six',
			),
			'std'         => 'six',
			'description' => esc_attr__( 'Select how many column will show the social icons', 'boldman' ),
			'group'       => esc_attr__( 'Social Services', 'boldman' ),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
		),
		array( // First social service
			'type'        => 'dropdown',
			'heading'     => esc_attr__( 'Social icon size', 'boldman' ),
			'param_name'  => 'iconsize',
			'std'         => 'large',
			'value'       => array(
				esc_attr__('Small icon','boldman')  => 'small',
				esc_attr__('Medium icon','boldman') => 'medium',
				esc_attr__('Large icon','boldman')  => 'large',
			),
			'description' => esc_attr__( 'Select social icon size', 'boldman' ),
			'group'       => esc_attr__( 'Social Services', 'boldman' ),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
		),
	),
	
	array( vc_map_add_css_animation() ),
	array( themetechmount_vc_ele_extra_class_option() ),
	array( themetechmount_vc_ele_css_editor_option() )
	
);

global $tm_sc_params_socialbox;
$tm_sc_params_socialbox = $params;


vc_map( array(
	'name'        => esc_attr__( 'ThemetechMount Social Box', 'boldman' ),
	'base'        => 'tm-socialbox',
	"icon"        => "icon-themetechmount-vc",
	'category'    => esc_attr__( 'ThemetechMount Special Elements', 'boldman' ),
	'params'      => $params,
) );