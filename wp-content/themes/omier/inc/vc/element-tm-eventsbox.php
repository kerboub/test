<?php

/* Options */

$allParams = array(
	array(
		"type"        => "dropdown",
		"holder"      => "div",
		"class"       => "",
		"heading"     => esc_attr__("Show Pagination",'boldman'),
		"description" => esc_attr__("Show pagination links below Event boxes.",'boldman'),
		"param_name"  => "pagination",
		"value"       => array(
			esc_attr__('No','boldman')  => 'no',
			esc_attr__('Yes','boldman') => 'yes',
		),
		"std"         => "no",
		'dependency'  => array(
			'element'    => 'sortable',
			'value_not_equal_to' => array( 'yes' ),
		),
	),
	array(
		"type"        => "dropdown",
		"holder"      => "div",
		"class"       => "",
		"heading"     => esc_attr__("Show Events Item",'boldman'),
		"description" => esc_attr__("How many events you want to show.",'boldman'),
		"param_name"  => "show",
		"value"       => array(
			esc_attr__('All','boldman') => '-1',
			esc_attr__('1','boldman')  => '1',
			esc_attr__('2','boldman') => '2',
			esc_attr__('3','boldman')=>'3',
			esc_attr__('4','boldman')=>'4',
			esc_attr__('5','boldman')=>'5',
			esc_attr__('6','boldman')=>'6',
			esc_attr__('7','boldman')=>'7',
			esc_attr__('8','boldman')=>'8',
			esc_attr__('9','boldman')=>'9',
			esc_attr__('10','boldman')=>'10',
			esc_attr__('11','boldman')=>'11',
			esc_attr__('12','boldman')=>'12',
			esc_attr__('13','boldman')=>'13',
			esc_attr__('14','boldman')=>'14',
			esc_attr__('15','boldman')=>'15',
			esc_attr__('16','boldman')=>'16',
			esc_attr__('17','boldman')=>'17',
			esc_attr__('18','boldman')=>'18',
			esc_attr__('19','boldman')=>'19',
			esc_attr__('20','boldman')=>'20',
			esc_attr__('21','boldman')=>'21',
			esc_attr__('22','boldman')=>'22',
			esc_attr__('23','boldman')=>'23',
			esc_attr__('24','boldman')=>'24',
		),
		"std"  => "3",
	),
	array(
		"type"        => "dropdown",
		"heading"     => esc_attr__("Box Style", "boldman"),
		"description" => esc_attr__("Select box style.", "boldman"),
		"group"       => esc_attr__( "Box Design", "boldman" ),
		"param_name"  => "view",
		"value"       => array(
			esc_attr__("Default Style", "boldman")  => "top-image",
			esc_attr__("Detailed Style", "boldman") => "top-image-details",
		),
		"std"         => "default",
	),
	array(
		"type"        => "dropdown",
		"heading"     => esc_attr__("Box Spacing", "boldman"),
		"param_name"  => "box_spacing",
		"description" => esc_attr__("Spacing between each box.", "boldman"),
		"value"       => array(
			esc_attr__("Default", "boldman")                        => "",
			esc_attr__("0 pixel spacing (joint boxes)", "boldman")  => "0px",
			esc_attr__("5 pixel spacing", "boldman")                => "5px",
			esc_attr__("10 pixel spacing", "boldman")               => "10px",
		),
		"std"  => "",
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

$boxParams = themetechmount_box_params();
$params    = array_merge( $heading_element, $allParams, $boxParams );

// Changing default values
$i = 0;
foreach( $params as $param ){
	$param_name = (isset($param['param_name'])) ? $param['param_name'] : '' ;
	if( $param_name == 'h2' ){
		$params[$i]['std'] = 'Latest Events';
		
	} else if( $param_name == 'h2_use_theme_fonts' ){
		$params[$i]['std'] = 'yes';
		
	} else if( $param_name == 'h4_use_theme_fonts' ){
		$params[$i]['std'] = 'yes';	
	}
	$i++;
}

global $tm_sc_params_eventsbox;
$tm_sc_params_eventsbox = $params;

vc_map( array(
	"name"     => esc_attr__("ThemetechMount Events Box", "boldman"),
	"base"     => "tm-eventsbox",
	"icon"     => "icon-themetechmount-vc",
	'category' => esc_attr__( 'ThemetechMount Special Elements', 'boldman' ),
	"params"   => $params
) );