<?php

/************************** Custom Template *****************************/
add_filter( 'vc_load_default_templates', 'themetechmount_custom_template_for_vc' );
if( !function_exists('themetechmount_custom_template_for_vc') ){
function themetechmount_custom_template_for_vc($maindata) {
	
	$maindata = array();
	
	/* ***************** */		
	
	// Our Team
    $data               = array();
    $data['name']       = esc_attr__( 'Our Team', 'boldman' );
    $data['custom_class'] = 'boldman_our_team';
    $data['content']    = <<<TMCONTENTTILLTHIS
[vc_row tm_bgimagefixed="" css=".vc_custom_1550064560699{padding-bottom: 105px !important;}" tm_responsive_css="54266547|colbreak_no|||||||||colbreak_no|||||50px||70px||colbreak_no||||||||||colbreak_no|||||||||"][vc_column][tm-teambox h2="" reverse_heading="" show="6" column="three" el_class="tm-res-twocolumn-view "][/tm-teambox][/vc_column][/vc_row]
TMCONTENTTILLTHIS;
	$maindata[] = $data;
	
	
	
	
	/************* END of Visual Composer Template list ***************/
	
	
	// Return all VC templates
	return $maindata;
	
	
	
}
}
