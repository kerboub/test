<?php
// [tm-stepbox]
if( !function_exists('themetechmount_stepbox') ){
function themetechmount_stepbox( $atts, $content=NULL ){
	
	$return = '';
	
	if( function_exists('vc_map') ){ 
	
	global $tm_vc_custom_element_stepbox;
	$options_list = themetechmount_create_options_list($tm_vc_custom_element_stepbox);
	
	extract( shortcode_atts(
		$options_list
	, $atts ) );
	

		// boximage size
			$boximg_size   = ( !empty($boximg_size) ) ? $boximg_size : 'full' ;
		
		$return .= themetechmount_box_wrapper( 'start', 'stepbox', get_defined_vars() );		

		// Heading element
		$return .= themetechmount_vc_element_heading( get_defined_vars() );
	
		// Getting $args for WP_Query
		$args = themetechmount_get_query_args( 'box_content', get_defined_vars() );

	
		if( !empty($box_content) ){
		
			$static_boxes = (array) vc_param_group_parse_atts( $box_content );

				
				$return .= '<div class="row multi-columns-row themetechmount-boxes-row-wrapper tm-stepbox-wrapper">';
				$x = 1;
				foreach( $static_boxes as $tm_box ){
					$staticbox_desc  = ( !empty($tm_box['static_boxcontent']) ) ? '<div class="tm-box-description">'.$tm_box['static_boxcontent'].'</div>' : '' ;
					
					$tm_bicon= do_shortcode('[tm-icon type="' . $tm_box['i_type'] . '" icon_linecons="' . $tm_box['i_icon_linecons'] . '" icon_themify="' . $tm_box['i_icon_themify'] . '" icon_fontawesome="' . $tm_box['i_icon_fontawesome'] . '" icon_kw_boldman="' . $tm_box['i_icon_kw_boldman'] . '" ]');
										
					$static_boxtitle      = ( !empty($tm_box['static_boxtitle']) ) ? '<div class="tm-box-title"><h5>'.$tm_box['static_boxtitle'].'</h5></div>' : '' ;
					
					    $return .= themetechmount_column_div('start', $column );
						$return .= '
					   <div class="tm-static-box-wrapper tm-feature-plans">	
						 <div class="tm-stepbox">
						    <div class="border-box">
							</div>
								<div class="tm-box-icon"> 
									<div class="tm-step-icon">
									' . $tm_bicon . '
									
										<div class="step-num"><span class="number">0' . $x . '</span></div>
									</div>
								</div>
							
							<div class="tm-box-content" >
								'.$static_boxtitle.'
								'.$staticbox_desc.'
							</div>
						 </div>
					  </div>	
						';
				$return .= themetechmount_column_div('end', $column );
					$x++;
				} // end foreach
				$return .= '</div>';
				
			} // end if
			
		$return .= themetechmount_box_wrapper( 'end', 'static', get_defined_vars() );	

		/* Restore original Post Data */
		wp_reset_postdata();
	
} else {
		$return .= '<!-- Visual Composer plugin not installed. Please install it to make this shortcode work. -->';
	}

	return $return;	
	
}
}
add_shortcode( 'tm-stepbox', 'themetechmount_stepbox' );