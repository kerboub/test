<?php

/* Options for ThemetechMount Testimonial box */

// Fetching all Testmonial group names
$testimonialGroups = array();
if( taxonomy_exists('tm_testimonial_group') ){
	$testimonial_groups = get_terms( 'tm_testimonial_group', array('hide_empty'=>false) );
	$testimonialGroups  = array();
	foreach( $testimonial_groups as $group ){
		$totalcount = 0;
		if( trim($group->count) > 0 ){
			$totalcount = $group->count;
		}
		$testimonialGroups[ $group->name.' ('.$totalcount.')' ] = $group->slug;
	}
}

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

/**
 * Box Design options
 */
$boxParams = themetechmount_box_params('testimonial');


$allParams = array_merge(

	$heading_element,
	array(
		array(
			"type"        => "dropdown",
			"holder"      => "div",
			"class"       => "",
			"heading"     => esc_attr__("Show Sortable Category Links",'boldman'),
			"description" => esc_attr__("Show sortable category links above Testimonial boxes so user can sort by category by just single click.",'boldman'),
			"param_name"  => "sortable",
			"value"       => array(
				esc_attr__('No','boldman')  => 'no',
				esc_attr__('Yes','boldman') => 'yes',
			),
			"std"         => "no",
			'dependency'  => array(
				'element'            => 'boxview',
				'value_not_equal_to' => array( 'carousel' ),
			),
		),
		array(
			'type'        => 'textfield',
			'heading'     => esc_attr__( 'Replace ALL word', 'boldman' ),
			'param_name'  => 'allword',
			'description' => esc_attr__( 'Replace ALL word in sortable category links. Default is ALL word.', 'boldman' ),
			"std"         => "All",
			'dependency'  => array(
				'element'   => 'sortable',
				'value'     => array( 'yes' ),
			),
		),
		array(
			"type"        => "dropdown",
			"holder"      => "div",
			"class"       => "",
			"heading"     => esc_attr__("Show Pagination",'boldman'),
			"description" => esc_attr__("Show pagination links below Testimonial boxes.",'boldman'),
			"param_name"  => "pagination",
			"value"       => array(
				esc_attr__('No','boldman')  => 'no',
				esc_attr__('Yes','boldman') => 'yes',
			),
			"std"         => "no",
			'dependency'  => array(
				'element'            => 'sortable',
				'value_not_equal_to' => array( 'yes' ),
			),
		),
		array(
			"type"        => "checkbox",
			"heading"     => esc_attr__("From Group", "boldman"),
			"param_name"  => "category",
			"description" => esc_attr__("Select group so it will show Testimonials from selected group only.", "boldman"),
			"value"       => $testimonialGroups,
			"std"         => "",
		),
		array(
			"type"        => "dropdown",
			"holder"      => "div",
			"class"       => "",
			"heading"     => esc_attr__("Order by",'boldman'),
			"description" => esc_attr__("Sort retrieved portfolio by parameter.",'boldman'),
			"param_name"  => "orderby",
			"value"       => array(
				esc_attr__('No order (none)','boldman')           => 'none',
				esc_attr__('Order by post id (ID)','boldman')     => 'ID',
				esc_attr__('Order by author (author)','boldman')  => 'author',
				esc_attr__('Order by title (title)','boldman')    => 'title',
				esc_attr__('Order by slug (name)','boldman')      => 'name',
				esc_attr__('Order by date (date)','boldman')      => 'date',
				esc_attr__('Order by last modified date (modified)','boldman') => 'modified',
				esc_attr__('Random order (rand)','boldman')       => 'rand',
				esc_attr__('Order by number of comments (comment_count)','boldman') => 'comment_count',
				
			),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
			"std"              => "date",
		),
		array(
			"type"        => "dropdown",
			"holder"      => "div",
			"class"       => "",
			"heading"     => esc_attr__("Order",'boldman'),
			"description" => esc_attr__("Designates the ascending or descending order of the 'orderby' parameter.",'boldman'),
			"param_name"  => "order",
			"value"       => array(
				esc_attr__('Ascending (1, 2, 3; a, b, c)','boldman')  => 'ASC',
				esc_attr__('Descending (3, 2, 1; c, b, a)','boldman') => 'DESC',
			),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
			"std"              => "DESC",
		),
		array(
			"type"        => "dropdown",
			"heading"     => esc_attr__("Show", "boldman"),
			"param_name"  => "show",
			"description" => esc_attr__("Total Testimonials you want to show.", "boldman"),
			"value"       => array(
				esc_attr__("All", "boldman") => "-1",
				esc_attr__("1", "boldman")  => "1",
				esc_attr__("2", "boldman") => "2",
				esc_attr__("3", "boldman") => "3",
				esc_attr__("4", "boldman") => "4",
				esc_attr__("5", "boldman") => "5",
				esc_attr__("6", "boldman") => "6",
				esc_attr__("7", "boldman") => "7",
				esc_attr__("8", "boldman") => "8",
				esc_attr__("9", "boldman") => "9",
				esc_attr__("10", "boldman") => "10",
				esc_attr__("11", "boldman") => "11",
				esc_attr__("12", "boldman") => "12",
				esc_attr__("13", "boldman") => "13",
				esc_attr__("14", "boldman") => "14",
				esc_attr__("15", "boldman") => "15",
				esc_attr__("16", "boldman") => "16",
				esc_attr__("17", "boldman") => "17",
				esc_attr__("18", "boldman") => "18",
				esc_attr__("19", "boldman") => "19",
				esc_attr__("20", "boldman") => "20",
			),
			"std"  => "3",
		),
		array(
			"type"        => "dropdown",
			"holder"      => "div",
			"class"       => "",
			"heading"     => esc_attr__("Box Design",'boldman'),
			"description" => esc_attr__("Select box design.",'boldman'),
			"param_name"  => "view",
			"value"       => themetechmount_global_testimonial_template_list( true ),
			"std"         => "style-1",
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
		)
	),
	$boxParams,
	array(
		themetechmount_vc_ele_css_editor_option(),
	)

	
);

$params = $allParams;

// Changing default values
$i = 0;
foreach( $params as $param ){
	$param_name = (isset($param['param_name'])) ? $param['param_name'] : '' ;
	if( $param_name == 'h2' ){
		$params[$i]['std'] = 'Testimonials';
		
	} else if( $param_name == 'h2_use_theme_fonts' ){
		$params[$i]['std'] = 'yes';
		
	} else if( $param_name == 'h4_use_theme_fonts' ){
		$params[$i]['std'] = 'yes';
			
	} else if( $param_name == 'txt_align' ){
		$params[$i]['std'] = 'center';
		
	} else if( $param_name == 'content' ){
		$params[$i]['std'] = '';
		
	}
	
	$i++;
}

global $tm_sc_params_testimonialbox;
$tm_sc_params_testimonialbox = $params;


vc_map( array(
	"name"     => esc_attr__("ThemetechMount Testimonial Box", "boldman"),
	"base"     => "tm-testimonialbox",
	"icon"     => "icon-themetechmount-vc",
	'category' => esc_attr__( 'ThemetechMount Special Elements', 'boldman' ),
	"params"   => $params,
) );