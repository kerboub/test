<?php

/* Options for ThemetechMount Blogbox */


$portfolioCatList = array();
if( taxonomy_exists('tm_portfolio_category') ){
	$portfolioCatList_data = get_terms( 'tm_portfolio_category', array( 'hide_empty' => false ) );
	$portfolioCatList      = array();
	foreach($portfolioCatList_data as $cat){
		$portfolioCatList[ esc_attr($cat->name) . ' (' . esc_attr($cat->count) . ')' ] = esc_attr($cat->slug);
	}
}

// Getting Options
$boldman_theme_options   = get_option('boldman_theme_options');
$pf_type_title          = ( !empty($boldman_theme_options['pf_type_title']) ) ? $boldman_theme_options['pf_type_title'] : 'Portfolio' ;
$pf_type_title_singular = ( !empty($boldman_theme_options['pf_type_title_singular']) ) ? $boldman_theme_options['pf_type_title_singular'] : 'Portfolio' ;
$pf_cat_title           = ( !empty($boldman_theme_options['pf_cat_title']) ) ? $boldman_theme_options['pf_cat_title'] : 'Portfolio Categories' ;
$pf_cat_title_singular  = ( !empty($boldman_theme_options['pf_cat_title_singular']) ) ? $boldman_theme_options['pf_cat_title_singular'] : 'Portfolio Category' ;

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
$boxParams = themetechmount_box_params();

$allParams = array_merge(
		
		$heading_element,
		array( 
			array(
				"type"        => "dropdown",
				"holder"      => "div",
				"class"       => "",
				"heading"     => esc_attr__("Show Sortable Category Links",'boldman'),
				"description" => sprintf( esc_attr__("Show sortable category links above %s items so user can sort by just single click.",'boldman'), $pf_type_title_singular ),
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
				'type'        => 'dropdown',
				'heading'     => esc_attr__( 'Sortable Button Type', 'boldman' ),
				'description' => esc_attr__( 'Sortable Button type square or round', 'boldman' ),
				'param_name'  => 'sortable_buttontype',
				"value"       => array(
					esc_attr__('Square','boldman')  => 'square',
					esc_attr__('Round','boldman') => 'round',
				),
				"std"         => "square",
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
				"description" => sprintf( esc_attr__("Show pagination links below %s boxes.",'boldman'), $pf_type_title ),
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
				"type"        => "checkbox",
				"heading"     => sprintf( esc_attr__("From %s", "boldman"), $pf_cat_title_singular ),
				"description" => sprintf( esc_attr__('If you like to show %1$s from selected %2$s than select the category here.', "boldman"), $pf_type_title, $pf_cat_title ),
				
				"param_name"  => "category",
				"value"       => $portfolioCatList,
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
				"holder"      => "div",
				"class"       => "",
				"heading"     => esc_attr__("Show",'boldman'),
				"description" => sprintf( esc_attr__("How many %s item you want to show.", "boldman"), $pf_type_title ),
				"param_name"  => "show",
				"value"       => array(
					esc_attr__("All", "boldman") => "-1",
					esc_attr__('1', "boldman")   => "1",
					esc_attr__('2', "boldman")   => "2",
					esc_attr__('3','boldman')    =>'3',
					esc_attr__('4','boldman')    =>'4',
					esc_attr__('5','boldman')    =>'5',
					esc_attr__('6','boldman')    =>'6',
					esc_attr__('7','boldman')    =>'7',
					esc_attr__('8','boldman')    =>'8',
					esc_attr__('9','boldman')    =>'9',
					esc_attr__('10','boldman')   =>'10',
					esc_attr__('11','boldman')   =>'11',
					esc_attr__('12','boldman')   =>'12',
					esc_attr__('13','boldman')   =>'13',
					esc_attr__('14','boldman')   =>'14',
					esc_attr__('15','boldman')   =>'15',
					esc_attr__('16','boldman')   =>'16',
					esc_attr__('17','boldman')   =>'17',
					esc_attr__('18','boldman')   =>'18',
					esc_attr__('19','boldman')   =>'19',
					esc_attr__('20','boldman')   =>'20',
					esc_attr__('21','boldman')   =>'21',
					esc_attr__('22','boldman')   =>'22',
					esc_attr__('23','boldman')   =>'23',
					esc_attr__('24','boldman')   =>'24',
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
				"value"       => themetechmount_global_portfolio_template_list( true ),
				"std"         => "overlay",
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
				"std"  => "10px",
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
		$params[$i]['std'] = 'Our Work';
		
	} else if( $param_name == 'h2_use_theme_fonts' ){
		$params[$i]['std'] = 'yes';
		
	} else if( $param_name == 'h4_use_theme_fonts' ){
		$params[$i]['std'] = 'yes';
		
	} else if( $param_name == 'txt_align' ){
		$params[$i]['std'] = 'center';
	}
	$i++;
}

global $tm_sc_params_portfoliobox;
$tm_sc_params_portfoliobox = $params;

vc_map( array(
	"name"     => sprintf( esc_attr__("ThemetechMount %s Box",'boldman'), $pf_type_title_singular ),
	"base"     => "tm-portfoliobox",
	"class"    => "",
	'category' => esc_attr__( 'ThemetechMount Special Elements', 'boldman' ),
	"icon"     => "icon-themetechmount-vc",
	"params"   => $params,
) );