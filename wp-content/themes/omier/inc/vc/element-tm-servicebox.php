<?php

/* Options for ThemetechMount Servicebox */
$bgcolor_custom = array();
$bgcolor_custom[__( 'Transparent', 'boldman' )] = 'transparent';
$bgcolor_custom[__( 'Skin color', 'boldman' )]  = 'skincolor';
$boxcolor =   array_merge( $bgcolor_custom , themetechmount_getVcShared( 'colors-dashed' ) ) ;

/**
 * Heading Element
 */
$heading_element = vc_map_integrate_shortcode( 'tm-heading', '', '',
	array(
		'exclude' => array(
			'seperator',
			'el_class',
			'css',
			'css_animation'
		),
	)
);

$params = array_merge(
	
	array(
		array(
			'type'        => 'dropdown',
			'heading'     => esc_attr__( 'Icon position', 'boldman' ),
			'description' => esc_attr__( 'Icon position in the Service box.', 'boldman' ),
			'param_name'  => 'add_icon',
			'std'         => 'left-spacing',
			'value'       => array(
				esc_attr__( 'Before Heading', 'boldman' )           => 'before-heading',
				esc_attr__( 'Top Center', 'boldman' )               => 'top-center',
				esc_attr__( 'Top Left', 'boldman' )                 => 'top-left',
				esc_attr__( 'Left with spacing', 'boldman' )        => 'left-spacing',
				esc_attr__( 'Bottom Center', 'boldman' )            => 'bottom-center',
				esc_attr__( 'Top Right (RTL)', 'boldman' )          => 'top-right',
				esc_attr__( 'Right with spacing (RTL)', 'boldman' ) => 'right-spacing',
				esc_attr__( 'After Heading (RTL)', 'boldman' )      => 'after-heading',
				esc_attr__( 'Without Icon', 'boldman' )      		 => 'without-icon',
			),
		),
	),
	
	$heading_element,
	array(
		array(
			'type'       => 'dropdown',
			'heading'    => esc_attr__( 'Background Color', 'boldman' ),
			'param_name' => 'bgcolor',
			'value'      => array( 'Transparent' => 'transparent' ) + themetechmount_getVcShared('pre-bg-colors'),
			'std'         => 'transparent',
			'description' => esc_attr__( 'Select Service Box display style.', 'boldman' ),
		),
		array(
			'type'       => 'dropdown',
			'heading'    => esc_attr__( 'Text Color', 'boldman' ),
			'param_name' => 'textcolor',
			'value'      => array( esc_attr__('Default', 'boldman') => '' ) + themetechmount_getVcShared('pre-text-colors'),
			'std'         => '',
			'description' => esc_attr__( 'Select Service Box display style.', 'boldman' ),
		)
	),
	array(
		array(
			'type'        => 'dropdown',
			'heading'     => esc_attr__( 'Add button', 'boldman' ) . '?',
			'description' => esc_attr__( 'Add button to Service Box.', 'boldman' ),
			'param_name'  => 'add_button',
			'value'       => array(
				esc_attr__( 'No', 'boldman' )  => '',
				esc_attr__( 'Yes', 'boldman' ) => 'bottom',
			),
			'std' 		  => '',
			
		),
	),
	vc_map_integrate_shortcode( 'tm-btn', 'btn_', esc_attr__( 'Button', 'boldman' ),
		array(
		'exclude' => array(
			'align',
			'button_block',
			'el_class',
			'css_animation',
			'css',
		),
	),
		array(
			'element' => 'add_button',
			'not_empty' => true,
		)
	),
	
	vc_map_integrate_shortcode( 'tm-icon', 'i_', esc_attr__( 'Icon', 'boldman' ),
		array(
			'exclude' => array( 'align', 'el_class', 'css_animation', 'link', 'css' ),
		),
		array(
			'element' => 'add_icon',
			'not_empty' => true,
		)
	),
	
	array(
		
		array(
			"type"       => "dropdown",
			"heading"    => esc_attr__("Box Hover Effect",'boldman'),
			"param_name" => "hover",
			"value"      => array(
				esc_attr__('None','boldman')                   => 'none',
				esc_attr__('Float Shadow','boldman')           => 'hvr-float-shadow',
				esc_attr__('Grow','boldman')                   => 'hvr-grow',
				esc_attr__('Shrink','boldman')                 => 'hvr-shrink',
				esc_attr__('Pulse','boldman')                  => 'hvr-pulse',
				esc_attr__('Pulse Grow','boldman')             => 'hvr-pulse-grow',
				esc_attr__('Pulse Shrink','boldman')           => 'hvr-pulse-shrink',
				esc_attr__('Push','boldman')                   => 'hvr-push',
				esc_attr__('Pop','boldman')                    => 'hvr-pop',
				esc_attr__('Bounce In','boldman')              => 'hvr-bounce-in',
				esc_attr__('Bounce Out','boldman')             => 'hvr-bounce-out',
				esc_attr__('Rotate','boldman')                 => 'hvr-rotate',
				esc_attr__('Grow Rotate','boldman')            => 'hvr-grow-rotate',
				esc_attr__('Float','boldman')                  => 'hvr-float',
				esc_attr__('Sink','boldman')                   => 'hvr-sink',
				esc_attr__('Bob','boldman')                    => 'hvr-bob',
				esc_attr__('Hang','boldman')                   => 'hvr-hang',
				esc_attr__('Skew','boldman')                   => 'hvr-skew',
				esc_attr__('Skew Forward','boldman')           => 'hvr-skew-forward',
				esc_attr__('Wobble Horizontal','boldman')      => 'hvr-wobble-horizontal',
				esc_attr__('Wobble Vertical','boldman')        => 'hvr-wobble-vertical',
				esc_attr__('Wobble To Bottom Right','boldman') => 'hvr-wobble-to-bottom-right',
				esc_attr__('Wobble To Top Right','boldman')    => 'hvr-wobble-to-top-right',
				esc_attr__('Wobble Top','boldman')             => 'hvr-wobble-top',
				esc_attr__('Wobble Bottom','boldman')          => 'hvr-wobble-bottom',
				esc_attr__('Wobble Skew','boldman')            => 'hvr-wobble-skew',
				esc_attr__('Buzz','boldman')                   => 'hvr-buzz',
				esc_attr__('Buzz Out','boldman')               => 'hvr-buzz-out',
			),
			"description"      => esc_attr__("Select hover effect.",'boldman') . ' <a href="' . esc_url('http://ianlunn.github.io/Hover/') . '" target="_blank">' . esc_attr__("Click here to view sample animation of each.",'boldman') . '</a>',
			'std'              => 'none',
			'group'            => esc_attr__( 'Animations', 'boldman' ),
		),	
	),
	
	array(
		/// cta3
		vc_map_add_css_animation(),
		themetechmount_vc_ele_extra_class_option(),
		themetechmount_vc_ele_css_editor_option(),
	)
);

// Changing modifying, adding extra options
$i = 0;
foreach( $params as $param ){
	
	$param_name = (isset($param['param_name'])) ? $param['param_name'] : '' ;
	
	if( $param_name == 'txt_align' ){ // Remove Text Alignment option
		$params[$i]['dependency'] = array(  // This is to hide this option forever
			'element'  => 'btn_style',
			'value'    => array( 'abcdefg' )
		);
		
	} else if( $param_name == 'btn_style' ){
		$style = $param['value'];
		if( is_array($style) ){
			$params[$i]['std']   = 'text';
		}
		
	} else if( $param_name == 'btn_color' ){
		$colors = $param['value'];
		if( is_array($colors) ){
			$params[$i]['std']   = 'skincolor';
		}
	
	} else if( $param_name == 'color' ){
		$colors = $param['value'];
		if( is_array($colors) ){
			$colors = array_reverse($colors);
			$colors[__( 'Skin color', 'boldman' )] = 'skincolor';
			$params[$i]['value'] = array_reverse($colors);
			$params[$i]['std']   = 'grey';
		}
	
	} else if( $param_name == 'btn_shape' ){
		$params[$i]['dependency'] = array(
			'element'            => 'btn_style',
			'value_not_equal_to' => array( 'text' )
		);
	} else if( $param_name == 'btn_title' ){
		$params[$i]['std'] = esc_attr__( 'Read More', 'boldman' );
	
	} else if( $param_name == 'btn_add_icon' ){
		$params[$i]['std']   = false;
	
	} else if( $param_name == 'i_background_style' ){
		$params[$i]['value'][__( 'None', 'boldman' )] = 'none';
		$params[$i]['std'] = 'none';
		
	} else if( $param_name == 'i_background_color' ){
		$params[$i]['value'][__( 'None', 'boldman' )] = 'none';
		$params[$i]['std'] = 'grey';
		$params[$i]['dependency'] = array(
			'element'               => 'i_background_style',
			'value_not_equal_to'    => array( 'none' )
		);
		
	} else if( $param_name == 'separator' ){
		$params[$i]['dependency'] = array(
			'element'  => 'i_type',
			'value'    => array( 'notavailablevalue' ),
		);
	
	
	} else if( $param_name == 'i_size' ){
		$params[$i]['std'] = 'md';
		
	} else if( $param_name == 'h2_use_theme_fonts' ){
		$params[$i]['std'] = 'yes';
		
	} else if( $param_name == 'h4_use_theme_fonts' ){
		$params[$i]['std'] = 'yes';
		
	} else if( $param_name == 'h2_google_fonts' ){
		$params[$i]['std'] = 'font_family:Arimo%3Aregular%2Citalic%2C700%2C700italic|font_style:700%20bold%20regular%3A700%3Anormal';
	
	} else if( $param_name == 'h4_google_fonts' ){
		$params[$i]['std'] = 'font_family:Lato%3A100%2C100italic%2C300%2C300italic%2Cregular%2Citalic%2C700%2C700italic%2C900%2C900italic|font_style:300%20light%20regular%3A300%3Anormal';
	
	} else if( $param_name == 'css_animation' ){
		$params[$i]['group'] = esc_attr__( 'Animations', 'boldman' );
	
	}
	
	$i++;
} // Foreach

global $tm_sc_params_servicebox;
$tm_sc_params_servicebox = $params;

vc_map( array(
	'name'        => esc_attr__( 'ThemetechMount Icon Box', 'boldman' ),
	'base'        => 'tm-servicebox',
	"icon"        => "icon-themetechmount-vc",
	'category'    => esc_attr__( 'ThemetechMount Special Elements', 'boldman' ),
	'params'      => $params,
) );