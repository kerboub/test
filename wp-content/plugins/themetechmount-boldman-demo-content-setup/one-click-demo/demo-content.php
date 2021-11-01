<?php


/******************* Helper Functions ************************/

/**
 *
 * Encode string for backup options
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'cs_encode_string' ) ) {
	function cs_encode_string( $string ) {
		return rtrim( strtr( call_user_func( 'base'. '64' .'_encode', addslashes( gzcompress( serialize( $string ), 9 ) ) ), '+/', '-_' ), '=' );
	}
}

/**
 *
 * Decode string for backup options
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! function_exists( 'cs_decode_string' ) ) {
	function cs_decode_string( $string ) {
		return unserialize( gzuncompress( stripslashes( call_user_func( 'base'. '64' .'_decode', rtrim( strtr( $string, '-_', '+/' ), '=' ) ) ) ) );
	}
}



/*************** Demo Content Settings *******************/
function themetechmount_action_rss2_head(){
	// Get theme configuration
	$sidebars = get_option('sidebars_widgets');
	// Get Widgests configuration
	$sidebars_config = array();
	foreach ($sidebars as $sidebar => $widget) {
		if ($widget && is_array($widget)) {
			foreach ($widget as $name) {
				$name = preg_replace('/-\d+$/','',$name);
				$sidebars_config[$name] = get_option('widget_'.$name);
			}
		}
	}
	
	// Get Menus
	$locations = get_nav_menu_locations();
	$menus     = wp_get_nav_menus();
	$menuList  = array();
	foreach( $locations as $location => $menuid ){
		if( $menuid!=0 && $menuid!='' && $menuid!=false ){
			if( is_array($menus) && count($menus)>0 ){
				foreach( $menus as $menu ){
					if( $menu->term_id == $menuid ){
						$menuList[$location] = $menu->name;
					}
				}
			}
		}
	}
	
	$config = array(
			'page_for_posts'   => get_the_title( get_option('page_for_posts') ),
			'show_on_front'    => get_option('show_on_front'),
			'page_on_front'    => get_the_title( get_option('page_on_front') ),
			'posts_per_page'   => get_option('posts_per_page'),
			'sidebars_widgets' => $sidebars,
			'sidebars_config'  => $sidebars_config,
			'menu_list'        => $menuList,
		);            
	if ( defined('THEMETECHMOUNT_THEME_DEVELOPMENT') ) {
		echo sprintf('<wp:theme_custom>%s</wp:theme_custom>', base64_encode(serialize($config)));
	}
}

if ( defined('THEMETECHMOUNT_THEME_DEVELOPMENT') ) {
	add_action('rss2_head', 'themetechmount_action_rss2_head');
}

/**********************************************************/




/********************* Ajax Callback Init **************************/
add_action( 'admin_footer', 'themetechmount_one_click_js_code' );
function themetechmount_one_click_js_code() {
	$images   = array();
	$images[] = get_template_directory_uri() . '/cs-framework-override/fields/themetechmount_one_click_demo_content/import-alert.jpg';
	$images[] = get_template_directory_uri() . '/cs-framework-override/fields/themetechmount_one_click_demo_content/import-loader.gif';
	$images[] = get_template_directory_uri() . '/cs-framework-override/fields/themetechmount_one_click_demo_content/import-success.jpg';
	
	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		
		/*********** Preload images **************/
		function preload(arrayOfImages) {
			$(arrayOfImages).each(function(){
				$('<img/>')[0].src = this;
			});
		}
		preload([
			<?php
			$total = count($images);
			$x     = 1;
			foreach( $images as $image ){
				echo '"'. $image . '"' ;
				if( $total != $x ){
					echo ',';
				}
				$x++;
			}
			?>
		]);
		/*****************************************/
		
	});
	</script>
	<?php
}




if( !class_exists( 'themetechmount_boldman_one_click_demo_setup' ) ) {
	

	class themetechmount_boldman_one_click_demo_setup{
		
		
		function __construct(){
			add_action( 'wp_ajax_boldman_install_demo_data', array( &$this , 'ajax_install_demo_data' ) );
		}
		
		
		/**
		 * Decide if the given meta key maps to information we will want to import
		 *
		 * @param string $key The meta key to check
		 * @return string|bool The key if we do want to import, false if not
		 */
		function is_valid_meta_key( $key ) {
			// skip attachment metadata since we'll regenerate it from scratch
			// skip _edit_lock as not relevant for import
			if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) )
				return false;
			return $key;
		}
		
		
		
		
		/**
		 * Added to http_request_timeout filter to force timeout at 60 seconds during import
		 * @return int 60
		 */
		function bump_request_timeout() {
			return 600;
		}
		
		
		
		/**
		 * Map old author logins to local user IDs based on decisions made
		 * in import options form. Can map to an existing user, create a new user
		 * or falls back to the current user in case of error with either of the previous
		 */
		function get_author_mapping() {
			
			if ( ! isset( $_POST['imported_authors'] ) )
				return;

			$create_users = $this->allow_create_users();

			foreach ( (array) $_POST['imported_authors'] as $i => $old_login ) {
				// Multisite adds strtolower to sanitize_user. Need to sanitize here to stop breakage in process_posts.
				$santized_old_login = sanitize_user( $old_login, true );
				$old_id = isset( $this->authors[$old_login]['author_id'] ) ? intval($this->authors[$old_login]['author_id']) : false;

				if ( ! empty( $_POST['user_map'][$i] ) ) {
					$user = get_userdata( intval($_POST['user_map'][$i]) );
					if ( isset( $user->ID ) ) {
						if ( $old_id )
							$this->processed_authors[$old_id] = $user->ID;
						$this->author_mapping[$santized_old_login] = $user->ID;
					}
				} else if ( $create_users ) {
					if ( ! empty($_POST['user_new'][$i]) ) {
						$user_id = wp_create_user( $_POST['user_new'][$i], wp_generate_password() );
					} else if ( $this->version != '1.0' ) {
						$user_data = array(
							'user_login' => $old_login,
							'user_pass' => wp_generate_password(),
							'user_email' => isset( $this->authors[$old_login]['author_email'] ) ? $this->authors[$old_login]['author_email'] : '',
							'display_name' => $this->authors[$old_login]['author_display_name'],
							'first_name' => isset( $this->authors[$old_login]['author_first_name'] ) ? $this->authors[$old_login]['author_first_name'] : '',
							'last_name' => isset( $this->authors[$old_login]['author_last_name'] ) ? $this->authors[$old_login]['author_last_name'] : '',
						);
						$user_id = wp_insert_user( $user_data );
					}

					if ( ! is_wp_error( $user_id ) ) {
						if ( $old_id )
							$this->processed_authors[$old_id] = $user_id;
						$this->author_mapping[$santized_old_login] = $user_id;
					} else {
						printf( __( 'Failed to create new user for %s. Their posts will be attributed to the current user.', 'boldman-demosetup' ), esc_html($this->authors[$old_login]['author_display_name']) );
						if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
							echo ' ' . $user_id->get_error_message();
						echo '<br />';
					}
				}

				// failsafe: if the user_id was invalid, default to the current user
				if ( ! isset( $this->author_mapping[$santized_old_login] ) ) {
					if ( $old_id )
						$this->processed_authors[$old_id] = (int) get_current_user_id();
					$this->author_mapping[$santized_old_login] = (int) get_current_user_id();
				}
			}
		}
		
		
		
		/**
		 * Install demo data
		 **/
		function ajax_install_demo_data() {
		
			// Maximum execution time
			@ini_set('max_execution_time', 60000);
			@set_time_limit(60000);

			define('WP_LOAD_IMPORTERS', true);
			include_once( BOLDMAN_TMDC_DIR .'one-click-demo/wordpress-importer/wordpress-importer.php' );
			$included_files = get_included_files();


			$WP_Import = new themetechmount_WP_Import;
			
			$WP_Import->fetch_attachments = true;
			
			// Getting layout type
			$layout_type = 'default';

			$filename = 'demo.xml';
			if( !empty($_POST['layout_type']) && $_POST['layout_type']=='rtl' ){
				$filename = 'rtl-demo.xml';
			}
			if( !empty($_POST['layout_type']) && $_POST['layout_type']=='landingpage' ){
				$filename = 'landingpage-demo.xml';
			}
			
			$WP_Import->import_start( BOLDMAN_TMDC_DIR .'one-click-demo/'.$filename );
			
			
			$_POST     = stripslashes_deep( $_POST );
			$subaction = $_POST['subaction'];
			if( !empty($_POST['layout_type']) ){
				$layout_type = $_POST['layout_type'];
				$layout_type = strtolower($layout_type);
				$layout_type = str_replace(' ','-',$layout_type);
				$layout_type = str_replace(' ','-',$layout_type);
				$layout_type = str_replace(' ','-',$layout_type);
				$layout_type = str_replace(' ','-',$layout_type);
			}
			$data      = isset( $_POST['data'] ) ? unserialize( base64_decode( $_POST['data'] ) ) : array();
			$answer    = array();
			echo '';  //Patch for ob_start()   If you remove this the ob_start() will not work.
			
			
			switch( $subaction ) {
				
				case( 'start' ):
				
					$answer['answer']         = 'ok';
					$answer['next_subaction'] = 'install_demo_cat';
					$answer['message']        = __('Inserting Categories...', 'boldman-demosetup');
					$answer['data']           = '';
					$answer['layout_type']	  = $layout_type;
				
					die( json_encode( $answer ) );
				
				break;
				
				
				case( 'install_demo_cat' ):
					wp_suspend_cache_invalidation( true );
					$WP_Import->process_categories();
					wp_suspend_cache_invalidation( false );
					
					// Output message
					$answer['answer']         = 'ok';
					$answer['next_subaction'] = 'install_demo_tags';
					$answer['message']        = __('All Categories were inserted successfully. Inserting Tags...', 'boldman-demosetup');
					$answer['data']           = base64_encode( serialize( $data ) );
					$answer['layout_type']	  = $layout_type;
					
					die( json_encode( $answer ) );
				break;
				
				case( 'install_demo_tags' ):
					wp_suspend_cache_invalidation( true );
					$WP_Import->process_tags();
					wp_suspend_cache_invalidation( false );
					
					// Output message
					$answer['answer']         = 'ok';
					$answer['next_subaction'] = 'install_demo_terms';
					$answer['message']        = __('All Tags were inserted successfully. Inserting Terms...', 'boldman-demosetup');
					$answer['data']           = base64_encode( serialize( $data ) );
					$answer['layout_type']	  = $layout_type;
					
					die( json_encode( $answer ) );
				break;
				
				case( 'install_demo_terms' ):
					
					wp_suspend_cache_invalidation( true );
					ob_start();
					$WP_Import->process_terms();
					ob_end_clean();
					wp_suspend_cache_invalidation( false );
					
					// Output message
					$answer['answer']         = 'ok';
					$answer['next_subaction'] = 'install_demo_posts';
					$answer['message']        = __('All Terms were inserted successfully. Inserting Posts...', 'boldman-demosetup');
					$answer['data']           = base64_encode( serialize( $data ) );
					$answer['layout_type']	  = $layout_type;
					
					die( json_encode( $answer ) );
				break;
				
				
				case( 'install_demo_posts' ):
					//wp_suspend_cache_invalidation( true );
					echo '';  //Patch for ob_start()   If you remove this the ob_start() will not work.
					ob_start();
					echo '';  //Patch for ob_start()   If you remove this the ob_start() will not work.
					$WP_Import->process_posts();
					ob_end_clean();
					
					// Output message
					$answer['answer']         = 'ok';
					$answer['next_subaction'] = 'install_demo_images';
					$answer['message']        = __('All Posts were inserted successfully. Importing images...', 'boldman-demosetup');
					$answer['data']           = base64_encode( serialize( $data ) );
					$answer['layout_type']	  = $layout_type;
					$answer['missing_menu_items']   = base64_encode( serialize( $WP_Import->missing_menu_items ) );
					$answer['processed_terms']      = base64_encode( serialize( $WP_Import->processed_terms ) );
					$answer['processed_posts']      = base64_encode( serialize( $WP_Import->processed_posts ) );
					$answer['processed_menu_items'] = base64_encode( serialize( $WP_Import->processed_menu_items ) );
					$answer['menu_item_orphans']    = base64_encode( serialize( $WP_Import->menu_item_orphans ) );
					$answer['url_remap']            = base64_encode( serialize( $WP_Import->url_remap ) );
					$answer['featured_images']      = base64_encode( serialize( $WP_Import->featured_images ) );
					
					die( json_encode( $answer ) );
				break;
				
				
				
				case( 'install_demo_images' ):
					$WP_Import->missing_menu_items   = unserialize( base64_decode( $_POST['missing_menu_items'] ) );
					$WP_Import->processed_terms      = unserialize( base64_decode( $_POST['processed_terms'] ) );
					$WP_Import->processed_posts      = unserialize( base64_decode( $_POST['processed_posts'] ) );
					$WP_Import->processed_menu_items = unserialize( base64_decode( $_POST['processed_menu_items'] ) );
					$WP_Import->menu_item_orphans    = unserialize( base64_decode( $_POST['menu_item_orphans'] ) );
					$WP_Import->url_remap            = unserialize( base64_decode( $_POST['url_remap'] ) );
					$WP_Import->featured_images      = unserialize( base64_decode( $_POST['featured_images'] ) );
					
					
					ob_start();
					$WP_Import->backfill_parents();
					$WP_Import->backfill_attachment_urls();
					$WP_Import->remap_featured_images();
					$WP_Import->import_end();
					ob_end_clean();
					
					// Output message
					$answer['answer']         = 'ok';
					$answer['next_subaction'] = 'install_demo_slider';
					$answer['message']        = __('All Images were inserted successfully. Inserting demo sliders...', 'boldman-demosetup');
					$answer['data']           = base64_encode( serialize( $data ) );
					$answer['layout_type']	  = $layout_type;
					
					die( json_encode( $answer ) );
				break;
				
				
				
				
				case( 'install_demo_slider' ):
					
					$json_message		= __('RevSlider plugin not found. Setting the widgets and options...', 'boldman-demosetup');
					
					if ( class_exists( 'RevSlider' ) ){
						$json_message	= __('All demo sliders inserted successfully. Setting the widgets and options...', 'boldman-demosetup');
						
						// List of slider backup ZIP that we will import
						$slider_array	= array(
							BOLDMAN_TMDC_DIR . 'sliders/home-maincorporate-slider.zip',
							BOLDMAN_TMDC_DIR . 'sliders/home-mainoverlay-slider.zip',
							BOLDMAN_TMDC_DIR . 'sliders/home-mainclassic-slider.zip',
							BOLDMAN_TMDC_DIR . 'sliders/home-mainshop-slider.zip',
							BOLDMAN_TMDC_DIR . 'sliders/home-mainclassic02-slider.zip',
							BOLDMAN_TMDC_DIR . 'sliders/home-overlaymain-slider2.zip',
							BOLDMAN_TMDC_DIR . 'sliders/home-landingpage-slider.zip',
						);
						
						$slider			= new RevSlider();
						foreach($slider_array as $filepath){
							if( file_exists($filepath) ){
								$result = $slider->importSliderFromPost(true,true,$filepath);  
							}
						}

					}
					
					// Output message
					$answer['answer']         = 'ok';
					$answer['next_subaction'] = 'install_demo_settings';
					$answer['message']        = $json_message;
					$answer['data']           = base64_encode( serialize( $data ) );
					$answer['layout_type']	  = $layout_type;
					
					die( json_encode( $answer ) );
					
				break;
				
				
				
				
				
				case( 'install_demo_settings' ):
					
					
					/**** Breacrumb NavXT related changes ****/
					$breadcrumb_navxt_settings						= array();
					$breadcrumb_navxt_settings['hseparator']		= '<span class="tm-bread-sep">&nbsp; &#047; &nbsp;</span>';  // General > Breadcrumb Separator
					$breadcrumb_navxt_settings['Hhome_template']	= '<span typeof="v:Breadcrumb"><a rel="v:url" property="v:title" title="Go to %title%." href="%link%" class="%type%"><i class="themifyicon ti-home"></i><span class="hide">%htitle%</span></a></span>';  // General > Home Template
					$breadcrumb_navxt_settings['Hhome_template_no_anchor']	= '<span typeof="v:Breadcrumb"><span property="v:title"><span class="hide">%htitle%</span></span></span>';  // General > Home Template
					
					// Getting existing settings
					$bcn_options    = get_option('bcn_options');
					if( !empty($bcn_options) && is_array($bcn_options) ){
						// options already exists... so merging changes with existing options
						$breadcrumb_navxt_settings = array_merge($bcn_options, $breadcrumb_navxt_settings);
					}
					update_option( 'bcn_options', $breadcrumb_navxt_settings );
					
					/**** Finish Breadcrumb NavXT changes ****/
					
					
					
					/**** START CodeStart theme options import ****/
					
					$theme_options = array();
					
					$theme_options['classic']	= 'eNrVXety3Day_p8qvwN2UlsV14rS8DLXSNrVOt5sqhI7seTas5VysTAkZoYRh-CSHMlaxw90XuM82enGjZchqZEtOVIcWTNgA2h8aDS6Gw2azp3RdP4hn8_mg_wySgIe82zwbT6fzAdfL0NqOwv8Np4PYnrDtwV-8eaD6yhk-NGezgfLbRz7WOCzmG1YUuSDb-ncnn-I5kNZdc1oyKDVj1AB2l3FfEFjf0GDy1XGt0mI9BNkYjQfRBu6Ek0P5wPFhyiKwkohtJmxlNGiUgacpDyPiohcJ5VSG37ToqDBGjmrPIBB5NF_qz1B5_XRi_8k19BhlCQse0pMw4zGHIGPNqtq49OyHFj0g21e8E2VwIEpYjBYmKMlx_4pcvRBjGBJN1F8o3r7madplOSikjsfIDbb1CpJHKhwQdd8Qw_I99DiFfzOaZJbOXS-VI1cXNEsonKUE0RotY2pGI4Noy3Y-8IqMqiz5FmVSRgc8mZpOIBn2xO1bBhelDBrzaLVulDPnIluMWZFwTIrT2kQJQIXqDBsg3IazpzZRCNC49hCVqV8i0blnHmSEzXDK85XMZNCMxScXFxa1VZDtqTbuDATYZ5blZFDk18Ph4IplLwK0ZpfMcPghC3o0BVUwPva9nGdwaDMrM0-adKArTOYkviA_JPFV6yIglumDVoZK27vOGXesHvKvNHdp8yeOq7r9s8KQuU8PajcUQ9Uw4eDyn2CUPVIlfuAUuU9PaicHqlyH1CqRk8QquEfA9X46UFl90iV80BQOdLMfFqKatyj08cPJlL5dvHHQTXqgwow2qYpywKas0-x8rxezOx-K69XtgJ4AqbflwbsMy3iyUNZxN1Y2cI5XFyx4glpdqcHpunDLEOou9gWBU8MTOPHAVNzCfYPvXuA0I8KB_hFVGDplxzi6BM3rumtg1ouaGZVfXaY_UwLjCbwFyvfiATwH9LscpWxG-2rSZr-UMIM6q2LXCKdHx0teBxuaHJYrAHQggXrDdQqDgO-0Y-skBb06Dq1lJ46EqS5fnwkWs2PluD4FzCFOIbF6vC3dLVftAKgSrhVfv_0sIVYK8qHba4b7c9-rICEs1dCCaXX66iQ0-XUSEQUw6-1F4auWocYdRK0a5qEMcv8KEDOKXD2QdDG0Vwio5kWSQQvWsrZmpmHYqXSa5bzDdOu-ZKSJbVolvFrK-TXSemyyyqoUKCrXFz7-1cBFBHxt7XYLhYx01wioWs0-lZfkV9SRBZcMLsVXDA5bsuI_CDmOfvscXn1cW3Tex2VM9wZlWK7Ou9yOG3zbuZyUSS1ddaMYDpeg7BFRGw2cRdSnHTPavcCsu0m8cuoXCcqTn9sJK-6kjJGL1MeJTo6ajtS_TizLkKrjLpV6HFXyNf82i94usAtfzG3FZCypE-vIFcVqsZgcb4zYOKb4YHjegfu6GB4OJs-14Cqij2rzWsQNdp3bNM-gf-d0Qh-Hzqj51p2VOWYLQvRglSXXDDq8TYmQUzz_ARphAKjQUGKjbUGtRqjarWw1uD0OI5OjyNNLEU0XfOEwaMjeJQXGU9Wpy9iDNKRc5ZdRQHL58dH6gEZkm9sz31OvNGYTKazyfERtni0jU8bOAiFXrI57WSznSmWXFyxmKfM4pIzoll7uaFRPFwnhqNjStYZW54MsLzg8yhZ8r-x93STxgzVO_4MTpulx0f0tGT9V0Aq5wFskBYGCnNScB4XUXoCKnvwrj6wHlH1Wmg6pBSbwy29KZAwy2UFobwM1S1b3ej-tzrd933uc7i9B9Aly8iCF3qgn7HrmVXpugfe7GDiwKqc4pr52FwwsGdh2hWyR-oBI0QBTaOCxgqQtliw222Fjz4haOAMnZmz7LXnhNBr7P44r_gznbx7DrbcFTdUF2GQbTeLx4jbXqLXG1P4BAjH0zHYFbe6EgbCq4hdq7o0jsVmp7f9cmVrnlxcdEqNVSvR3266FTEewlXmqM02GJu9W_w5HA2fmz3BMIAHvGU7Ch3YYhSnNLzyCyQMQD1ngdZuL2jBVjy7IWdZsI6uYDsmOzUKutI1PDwwXFw1iNHWNcSgjwux2UbLiIWazZ-hNFwnZTmBrYZlqq9ppTrdFmue6e5cMMAzUdBkz9WH1nXgbbXYRuZxFfamd1Ih6bLG0E7SPwp0mAzYKVD6Y1-38FgOnTv3saYEDW25iaGBl7E8BWcB0PX3QcQ-kP-DfTp-rhWDqlhpqu4geNIWVqOL-YoXN6lm2VwwpR8puw5cMPu7NBqUXsCHPUpsJBTUhj8a1e-M7ze-B6p_6PTHdSYSJZFSQGFJfBCd1UROeK2gXCKsbRYL1B8gkIE8HKZyNCoF5ct0p3xUISkb-t5vO4cF0chh6i9vfJ34XCL9OPR8a1rFl2Sq9mSoFUetdreKmbVR7r-uhGzIuTSM7DmlpVwiqVwiL0o_yiE0XDCqj9SdNJRszkAjr42_ixCIEj9KUul9Y5sXsKTJv3gWkos1S8hLNMQPD7X-kTVw0eiAo9wa_hnBauTX5AVNyL8YLsyU_Jtv_6qXjmQhL25kFRBwuY0EdQLjDc6AbfS5FgXGLqCjk8Gbl7-8fXl-QX55-_ri5YCItk4GfFvgsoTva5rC9_w_W5rBVzE3XCeDRQzqY0DQZzsZXDCC8z877u-__y5dtoqoYMjH7wo4XCIduoc56O1LHbNAj1iAApgdh9GVdk5lg5aIfYFPWnlcIotKN7YZdeKLKNZe9hHU03_vNq7WEdCu3dMXYM0cH8GH4_Xo9C-242rPewilI9VI5zCKa6lpXCf2PQ0D1j1ubRaNi7uPRbjuZjC7Lnl9PE7beMDVV0lz03saUczB2gKxsMCyvvuQzsIQNtTcDIo4HvlHtCzWIMEH5Eeek7NkxWKWH5C352ctQ1QLBUfqi0CMWSRTmNBj2HGSroHsRm-OkPz0gscx-QdcMEXmxCYVkbGHu4JS73OMcqIDKl8PTr8Hq4dQ8ssWNvxmxwGsxMKSy0h1T0-1JlELrxaq8kAvHKenjnc0IS83LFuxJLgBnZKx46MU0HNOG-LtmJiStl1qMSVXtveG_WfLcmAz0c2cpVwi9IKCalpBNaZaUdHJ_rB4RXNcMIO0vkmNR20KpnOTmezS3eY-uPZzXbW-P_X25Hqd5M1Y41B1aDsz2NFGB-Mx9Dp5rtQ3bj0549qmXdtrvmE8ic3pi2y3KxKGU7ZhK4pUFhq7mch1VVu4s1O9I0iGhDRKkPiRRWP2OWTviSnYs27DEroG7_ETz0QdYzdVgetoBd0kTebToECXXDC30ppLgMcBgi23i7jlSEBn0ErBDjOe4sHOHzCPnxkW6gmn2Q9zqO0ovPaek3bi_jmZVardl198W9h3aMK-BU_v46RzOV3OltRIvRYyqfFyltKMFgYxc8Bjl4rJ7AP6aH326I_Wx188XCcMAWuRFd9-fKn7xnXqYNl5eiy7T49l7-mxPHp6LI-fHsuTp8fy9OmxPHvELDsdW8nw8fIs8ng4OL-ZHxTUX_D3xoupP7kt1aXWhqK2pQ03lbGwXFzmWsBjsnZOBufbRR5k0YKRC05ebzPyil3ncmsfXDCBCLH7XCKDAkyB7ERs3z6OCDhL3_-OBoLyWcFESd9DHe9kcFYQsKY4YTxcJww85lwwDEW62YovEdqMhIfhdrO5OTzECj2d2G6jE1t0Evl4ZmBiLFggDhnU953gC8OI0IBsc32HTnSZ-4hAkW0ZNoD9nQziVRuZp8je_XpUg_BdL_CONgV_3QTedeqj5UWi8AR8tumgv6pbla1ZnajL_R_vkDXM84aBWiGuxygqXCc06L-LS6OKuDuHZLpDSOO4PYXEfYBsSdHrAyWQyF8PdvAmJi7K8kJjt7PIoRnP93zPzFxclbovr06IToO4a1ruzQW778wbo9jdxmB6XCJrGM_CvMewE1OgcH3xxxzl1CpUYUVXSPhO4O0p9nFh7FT4gtDeTSa7biCPwLulJk-kPp7-uKWc2juAtVPhkYCFuaTRe5mecDtiQ9t18ehQIVYfVH_mlxHE9EbGl3XyCKYsvtCl5P_-lzhDe0ZMeFxcbNrQDh7dvRucmq9cIp7wDoPhh-QsjomonpOM4fbEwsPm_mL6NVwnUu5QmQT9KYljEaeNkltTA50HOc2Nkjup9XYz7dPiQnu8bVwwni2ASTn3cbSJimokxLXlY3X64ouMYXW25Vwn282iPHHGDVS0hHk7G56VhyUg52-gjPwEheq7IKykXCJVj0E9-bi0JZSZqI8anfK5bkFcXH8HabTMXFxqbpCqPjRMrVF5qK7AGCw1IT8wOIpcXOsDXQWe04BcMOSBQmu5CM86KvedJXQRs9AUwYzK7B81vWfmCw5W4qdm-YX8lovpwDzrKDdtiQNyYc3IZQgfv1MfUTlIgcHBq9SniOXqWUFXqsIFXWHbH-WmsjMSbBrtuprwOV4LJTSJK6xq2O_QXDBLNZq2HiUwVTJ7Uk7WbfDaXZDsolwnLibcEafeCeqe149qe6-Noh3aYYOqCWvzeRPSZi-7cOL1gpRnxZLHEffTjP_GgsIPgTiK84ZdXtLhWjKZCZ5cXE9aBZdUQgFkLIahhVWWSgr1sFxcR6h_38hC8rNkJt9tWFfTS3omcsTLBY3itktdKghMYNSH3u0tI-tKt5kXWaRLjYu4aKIujuHLZHR2ir1DVBkZ9KNGRF5ReaMF569ZoecGkG3LiHL1TkzPdZmRvi6zXDAZXpp7cx33ZWbquoylpLzr7g_ekomsBLbZlIZiJckFRhte0scI5nsfYGCMRF6NeHhIhhoS8Hqz3ttDQw2HoWzHQ9w5UER9WDh7YTEtE1MfHgxzTWzB-eUGfGCLf1FI3L0gqWxjDwuHuV0W0JglId1TPoI4SvvWi7gGYSDuR8TbV0i-22ZUW3sPi4prUIl5cJuEeAaUKoS9wJSUfcCM9gXmR5V49AVXz4amFk7tLQrFYBNXWOxXstgy5oT1QKMstcaeW7VRzcY7rZG1WrHAzbmsI_2lCn2UrGKG9__ErcUdu3fWQyzNjVr0OIXtqOjcvnGrlBTtW3bZQmWbnpmbAYxufLDiM1qb-lxc2cjV_XrWSV3Zt6Htl-9Thm_YChjRx9Nd9e7jAi-MlW3S4uahL-0W0Ybtu4PvAxXUPY-eNEhonTaTM_dSUM7dZAoEXFwlOZIfkiX_DLweWGvdGUBvB8CK9dmrxxAUBKCqGWCsGCSN6U2Z2iEpOjRDpY0d3TCRzzYMIxEaYOHzm7RsUCw_syznCY3FxKA_plU1hpvV1V2_zgEKjc4JbhDtMGEbgt3YB3L_0-s3L8l3Ly_OfvjxXFy3yLKMZ97Q8xfR6h5lYaZlQaTQ5xa_9U0BWoN3aBhxL6ye-6tfrVkbQiXWA0Xk5Zs3r99o-A3hhoXRdmNoXCdcMPLFOspJCpsE2dAbsqZXjCwYS8gGZCQkPCMhixl4b4fk74zk24yRgpNgzYJLcsO3GclTFsNwVoeaW9NZPc8fhdg8wv7-iHvC0LeFfT_qYx5zu752X-xQnfXYnr79kICY5_r1klgJ07xf8WINs0GWiKtM9k5Pz3mW3RyQxbYgiXoOaxAmMVRzKNojwPsmPyQ_xwwUCymyG0JXNErIdYRZ4iDhJIyWS4YxeXLJbq55FuaHmMqs11kehUxf29t5W0r1uZri2vNZ-VxcqGypUmpXDzGu0kLjoxSljaiJ0BqGH2VF1Vsbt1CggjGuYrW5StdK1zTDNN4ORWdjNVK5Sqo3LCtQXFxzLoJhWbCLWIWlxVwixY1vh6TSEbvSMbQaBcIk7VZUgrmJ_HnaqhMhQMGSMm_V2BIVb8FjW4pn2fzSMF-n1LYqiOjXxgK6rVncWEDw9Hrao1Vnj1Zxq46j4HLvRt39EMB6oFuTW5qVB5FqFV8HVvUuSCWKX5l0q9wRddjMaVCkGQ-3QWGBOW3plWW2Rkf0I50HCzdOqxk79KoU6mFbr2473TYpajE9TDhHQSoy4CniFbcGjzLEboOHTwR-v8Vru-RFhVibR_UW1sUmlsu_ccdpm1iY6S1Vurnt8uyrBk3bY3EKhpcH3slbLvVKOUtFNPLUPFxc29XLNZiuevr21Xcv35AXr1-dX7x5--Lih9evQN_agtgzDZn3DAxAC8PmJZSvMhhIgWhcXMO2C3PJQPOeF_C52CYs_BM05WFTuv_yKo7XhOeWfdQeDu9_I61xcK-76R7HbB0Hrr052OWBGy6JOoBBDnYlD1VqCVT9W7TBDYFss_gbAV0O2IlEnkOZcUzTKBeoQdW_yhTrk9cpS_5yThP08ocH8BMdePDBww9j-DDGDxP4XDA_0eD5t8--WvDw5sOzr1IaooDMyRDKwH1YRYn8_JE8--pQic8BOSxl6QM8ECnXsus5GQywd4K9Dw5IX5Z3o9kPqiGRKkXGw_Q9EFRytqFsBGVQo56sPVwntiCtsis_W_JEe_7sK2yM1AsJJl99q_q81j0MsXazfUcQikmck6-dqRuOplBABPcVKGoDcNzdAbjDWktTZxrMxt_uM6Aalwn6LbGGr03z4KxoUZ4TushBhxZM9LSEBkbDP8NnEOo58Ub40bpExVam1M-J-IiK9X--sYD8eVnwb1mAtTb8v3erQ7BSfud--F1r3JGcfAQcMXcgEHoY0WsIi1wnZy6M8hSc1TmJEjGxCwya4kRo6ztdimS-cqeZ4W2IqsnnNIhENE1ftmlS2xXqPN6uFE3aQoPeaN3R1USkftCpQqyGvsZcMO54OxWN86mqaU7sasjRqhqY-EQbHHU8QAOelzarM2mjq3FUr2A3K2heyvImWQ0YZEw114Rl3FKljs14p-5NeQm8rGngKcvr4IxUJKiODM7jBRSTn4RcJ5HX4p0d4OCkVOpUgyh1eEQ0Hkqt0pURL0dDQuGy1AOios3vsTzXzk6TtM7HsFqnNkhZxfAxVHwYPwnv00WJCDEoq9PFjXsl1CgusPKFDyPxitdi3bjyKG7OVl6WMh3qV0kEmdy3ofCGyaQKFIBa6_Idb21duPhvAbT2MO7tYdLsQVgP7R2MR15bB97M23MIFVXQ3sPEax3CaDTu7kHcdtY91MSmA6Zpex-zPpjwqjqmOZtcMJfwbuS_3Vwi3RgVl4qEWFQDxypXSacEV2wmHcHyzNPfdh-aqrjpiUu_jZyMylM0jPKCZkXjXzJpkLAkbKQJySyzHhZnOwPwlZlZ2qrS-GsMDNxr-e-oqOjjQkBmy6woGc_KRaqefDBT75esEovEkvLdTGi5q-xW1Zh5GYhMuDP1xm1ZUjpcIqqp0LvqOIpqku4EVHVkeGeufBU8qAUq3Yb9bPjEi-v44qiQbbiv36HN05Lm4_8D_1xc6X0';
					$theme_options['infostack']	= 'eNrVXX1z2zbS_78z_Q44dW6mmce0xRe91vadL821nWmTNnbm7qaT4UAkJLGmCB5J2fGl-UD3NZ5Pdrt4I0hJtJzYqZ03S-ACWPyw2F0sFgydeoPx9H05nUx75WWSRTzlRe-bcjqa9r6ax9T1ZvhtOO2l9IavK_wSTHvXSczwozue9ubrNA2xIGQpW7GsKnvf0Kk7fZ9M-7LqktGYQasfoAK0u0j5jKbhjEaXi4KvsxjpR8jEYNpLVnQhmu5Pe4oPUZTEViG0WbCc0coqA05yXiZVwjOr1IWftKpotETOrAcwiDL5j90TdN4cvfgluYYOkyxjxVNiGmY05Qh8slrYjY_rcmAxjNZlxVc2gQdTxGCwMEdzjv1T5Oi9GMGcrpL0RvX2M8_zJCtFJX_aQ2zWuVOTeFDhgi75ih6Q76DFK_hZ0qx0Suh8rhq5okVC5ShHiNBinVIxHBdGW7F3lVMVUGfOC5tJGBzy5mg4gGc3ELVcXBhekjFnyZLFslLPvJFuMWVVxQqnzGmUZAIXqNDfBuU4nniTkUaEpqmDrEr5Fo3KOQskXCdqhhecL1ImhaYvOLl07FZjNqfrtDITYZ471sihya_6fcEUSp5FtORXzDA4YjPa9wUV8L50Q1xcZzAoM2uTj5o0YOsMpiQ9IN-z9IpVSXTLtEErQ8XtHacs6O-esmBw9ylzx57v-92zglB5Tw8qf9ABVf_hoPKfIFQdUuU_oFQFTw8qr0Oq_AeUqsEThKr_x0A1fHpQuR1S5T0QVJ50M5-Wohp26PThg4lUuZ79cVANuqACjNZ5zoqIluxjvLygEzO328vrlK0InoDr97kB-0SPePRQHvFurFxcsTlcXLDqCWl2rwOm8cMsQ6g7W1cVzwxMw8cBU3sJdg9991wwoR8VDgirpMLSzznEwUcarvGtg5rPaOHYe3aY_UILjCYIZ4vQiATwH9PiclGwG71XkzTdoYQJ1FtWVT49OprxNF7R7LBaAqAVi5YrqFUdRnylHzkxrejRde4oPXUkSEv9-Ei0Wh7NYeNfwRTiGGaLw9_yxX7RCoAq4079_ePDFmKtqD1se93o_ewHCyScvRpKKL1eJpWcLq9BXCKiGGGjvTj21TrEqJOgXdIsTlkRJhFyToGz94I2TWYFLbRIXCJ4yVxcztbEPBQrlV6zkq-Y3prPKZlThxYFv3Zifp3VW3ZZBRUKdFXq_f5VBEVE_OvM1rNZyrRI6BqtvtVX5JdUiQPArgVAnr9lRGGU8pJ98riC5rjW-b2OyutvjEqxbc-7HM62eTdzOauyxjprRzC9oEW4RURcXDbyZ1KcdM_KegHZepWFddQTFWc4NJJnr6SC0cucXCeZjo66nlQ_3mQXoVNH3Sx6tArlkl-HFc9naPJnU1cBKUu69ApyZVG1BovzXVwwE1_3Dzw_OPAHB_3DyfiZBlRV7FhtQYuo1b7nmvYJ_PEGA_h56A2eadlRlVM2r0QLYgag5-N1SqKUluUJkgj9RaOqd3qcJqf_4uuCXFwU0BWLiReQ7-F7Sc5ZcZVEjPxcXPCrJGbFn46PgPb4aJ2etoYj9LLpzZ1cMEK_Viun5BGYEwfDaiWpOE-rJD8BBdd7u50ZAnWW0FSKzTlS2Qv-jsuq4Nni9DjRteS6yZc8Y0BylJxe0PSSXFxw8uId2NSKTI-PVB1yDOY0M72x1AEGTvvkazfwn5FgMCSj8WQE5EB1umuEHaIXbKHZIXXYHJrotoDBrNUVhDIyVLeYrsH9my7d933aLTTXEXTJCjLjlR7oXCdYMbPKfP8gmByMPFhlY1xcAx9aXDB2LDTXXCJ7pDtahCiieVLRVAGyLbbr7_aqBx8RBPD63sSbd_pnQug1dn_cLvcTN233HDy5K26oLuKoWK9mjxG3vUSvM0bwERAOx0PwE27dGhgIrxJ2rerSNEWTp_0ha2VrnqBioDbqQC_RX692K2I8VLPmaJutHxpbLH4fDvrPjE0wDOCBbd2OQgdskOKUxldhhYQRqOdcItLa7Tmt2IIXN-SsiJbJFSunZKNGRRe6RoAHgIsWMfquhhj0cSWMYDJPWKzZ_BlKS1KXEzA1rFB9ja3qdF0teaG7A1wwz0RBmz1fH0I3gXcHQ3N8Jh_bsLd3GxbJLu8K_R79V4EOkwGWAqU_DXULj-UQeacda0tQ35VGzPex_zIH5x_QDfdBxD2Qf8DfHD7TikFVtJpqOvyB9G3V6FK-4NVNrlk2QOlH2p2c9v4mnQalF_BhhxIbCAW14o9G9XvD-43Xgerve91xmpFESaQIUFgS70VnDZETu1BQEc66SAXqDxCYQB4OczkalVLyebpTe04hKSv6LmzG4D0tGiVM_eVNqBNZ5L4Md7INrRJKMlV71NeKo1F7t4qZbKPcf10J2ZBzaRjZc0prRWIjL0o_yCG0XDBqjtQftZRsyUAjL83-FSEQJWGS5XI3jW1ewJIm_-BFTC6WLCMv0BE_PNT6R9bARaMDiNI0fJ_AauTX5Dnsnv7BcGHmBDaKf9FLR7JQVjepdg-SbA7mBBZ3k8RsDFwnvtwXziqMRkBXXCe91y9-efPi_IL88ubVxYseEa2d9Pi6woUJ35c0h-_lv9e0gK9idk56sxT7ILivPOkBhtM_e_7vv__ee9trCgsGccJdIUSkMwzrKARuXCcFLEB5HFwnV3rbKBt0RDTr1H6gSsy-tB1G4rMkVTvU4yOop__dbFstJKBd-qcvVjRJj4_g0_FycIps_pW9o6s8ZbgI4cFANbRzJNW1VLfjexoJdIvmzaFpdffhnMUxWKDSDMgLyN-TebWE-T4gP_KSnGULlrLygLw5P2uPzts2umXBVF7c6H7Gl3Lwv0BMHPC1P32A5K4jlB5YhAMNRVTDLJoxzG4jhNEeyEYoRMUyLniakr8DTmRKXFzier4Odrj9Talp9jmEtXtMCYA8P-l91Tv9DvwgQskva3AB2h1HsDLrYI3onp5q3aIWYiMYFeCM5SADRyPyYsWKBcuiG9AyBTs-ygE979TitQ9YeSYYo72ZRrjJl-29Zv9esxLYzHQzZ7kIxqDYmlZQsalWVPyxO_BtaRJgkDbN1nCwTeHsNDujTbrbNhS--0xXbVqszp78YFwneTua2Fcdut4EbNzgYDiEXkfPlGODxqhkXFx7uUt3yVeMZ6k5X5Ht7oqN4ZSt2IJcIpWD7m8hslmVUfc2qu8ImyEhTTIkfmTxmX2O0TuiDO5kt6sJXcN-8iNPPT3jSdnA7WgFN06aLKRRhZsENK2NTQIG_AVb_i7iLUF_nSMrBTsueI5HN3_APH5ioKgjwOY-zLG1p_Dae062E3fPycSqdl875dsCwX0TCK54fh9nmVYetufXQiY1XslyWtDKIGaOcNxaMRk7oA_PXCeP_vB8-NmzvhCwLbISuo8vOd9spnaw7D09lv2nx3Lw9FgePD2Wh0-P5dHTY3n89FiePGKWvR2mpP94eRaZOhw2v0UYVTSc8XdmF9N8clsyS6MNRe1KH26sciZk0gU8JkvvpHe-npVRkcwY5ja8WhfkJbsupWnvAYEIuodcIn0CXFyB4kSY7xBHBJzl735HB0HtWcFFyd9BneCkd1YR8KY4YbwkDHbMETiKdLUWXxL0GQmP4_VqdXN4iBU6OnH9VlwnrugkCfEUwcRYsEAcO6jvG8EXhuGuHlmX-pac6LIMEYGqWDNsXDD7O-mli21kgVwie_vrUQPCt53Ae9oV_HUVBdd5iJ4XSeIT2LONe91VfVu2Jk2iXdv_4QZZyz1vOagWcTNGYZ3Z4P5dXFwLVcS7s0rGG4Q0TbdcJ5X4D5APKXp9oJQS-ePBjuLExCVFWWnsNhY5NBOEQRiYmbOpuzLnhOi0iHdNy71twe47F8codr81mI7IGsazMLMx3okpUPih-G0OdxoVbFhxKyT2TrDbU-zjwtio8BmhvZtM7rpjPJiP59RkjjTH0x23lFN7B7A2KjwSsDBbNHlcJxMWbkes7_o-XtJUiDUH1Z0LZgQxv5HxZZ1OMgZGn-tS8v__JV7fnRATHhdGG9rBw7y3vVPzVcQT3mIw_JCcpSkR1UtSMDRPLD5s2xfTrzmh8vu3pVGafGG-SLJbkwW9BznfTbI7qfXtbtrHxYX2eJ9cMDybAZNy7tNklVR2JMR35WN1-hKKnGB10hVm69WsPoNGAypawkyeFS_qwxKQ89dQRn6CQvVdEFrJSap9HZrBx7UvodxEffTo1c91C-KCO0ijY-ZSc4NUzaFhso3KTPUFxuCpCfmBwVHkWh_xKvC8FgQgDxRaK0V41lPZ7Syjs5TFpghmVOYDqek9M19wsBI_NcvP5bdSTAdmUlwnpWlLHJkLb0YuQ_j4rfqIykEKDA5eJUMlrFTPKrpQFS7oAtv-II3KxkiwafTrGsLnBVsooUlcXGG2Y79BAyw1aLb1KIGxydxRPVm3wevugmQTPXH14I44dU7Q7nn9oMx7YxTboe23qNqwtp-3IW33sglcJ14gyHlRzXma8DAv-G8sqsIYiJO0bPnlNR2uJZOrEMj1pFVwTSUUQMFSGFpss1RTqIf1OkL9-1oWYqY8MlNuNqyr6SU9EVnj9YJGcdukrhUEpjTqM-_tLSPrSreZV1Xkc42LuEqirobh62J0voq7QWSNDPpRI1wiL6m8s4Lz167QccfHdWVE2b710nEhZqAvxMxAhufmZtyOGzETdSHGUVK-63YP3oNJnAzMbE5jsZLkAqOtXdKHBOZ7H2BgjOR5mlje08NB0teQwK636Lwf1NdwGMrteIhbCIqoCwtvLyzGdarqw4NhLoLNOL9cXMEe2OGfFRJ_L0gsM_awcJj7YxFNWRbTPeUjSpO8a72IixEG4m5Egn2F5Nt1QbW397Co-AaVlEe3SUhgQLEh7ASmpuwCZrAvMD-qxKPPuHpWNHdwam9RKAab1GKxW8liy5gh1gGN8tRaNtf2UY3hHTfItnqxwM25rCP3SxZ9ki1Shjf8xL3EDb930kEs3Y1G9DgHc1TtNN9oKiXFdpNdt2CZ6Ym5K8DoKgQvvqCNqS-Vj2zb68lOastuQ9viGhxYqIgRfTy9q959XFzRhbGyVV7dPPS13CpZsX0t-D5QQd3z5EmDhN5pO1VzLwXl3U2mQMBVkiP5IZvzT8DrgbXWnQEMNlwwtLzPTj2GoCBcMLZmgLFikDSlN3Vqh6TYoRmsNjZ0w0g-WzGMRGiAxZ7fJGqDYvmZFSXPaComBvdjWlVjuFmehZRhkwMUGpUgPGoRbTDhGoLN2Ady_9Or1y_Ity8uzn748Vxct8iKghdBPwhnyeIeZWGiZUEk1ZcOv_VdXDBag-_QMOKmWDP3V788szEEK9YDReTF69evXmv4DeGKxcl6VV-6BpAvlklJcjASZEVvyJJeMTJjLCMrkJGY8ILELGWwezskf2OkXFwXjFScREsWXZIbvJNd5iyF4SwONbems2bmPwqxeYT9_RE3h6FvB_t-1Mc85v584wbZoTrrcQN9H1wiAzEv9QsksRKmeb_k1RJmg8wRV5nsnZ-e86K4OSCzdUUy9RzWIExirOZQtEeA91V5SH5OGSgWUhU3hC5okpHrBLPEQcJJnMznDGPy5JLdXFzzXCIuDzGVWa-zMomZvsi38T4U-7ma4sbzSf1cXKhsqVIalxExrrKFJkQpyltRE6E1DD_Ki2q2NtxCgQrGbBXt5qyula5ph2mCDYqdjTVI5Sqx71xcWlBcXHMugmFFtImYxdJslqPh2yCxOmJXOobWoECYpN-KSrA0kb9Ae3VcIgQoWFLurRpbpuIteGxL8SybXxrmm5TaVwUR_cp4QLc1i4YFBE-vpz1a9fZoFU11mkSXezfq74dcMNYD3Zrd0qw8iFSr-Dpy7KsgVhTfmnSntog6bOa1KPKCx-uocsCddvTKMqbRE_3IzYODhtNpxw4Dm0I93Narv51unVWNmB4mnKMgVQXwlHBrW4NHGcLa4OETgZ9v8FwiL3luEWv3qNnCslqlcvl7zZs068zBTG-p0s1tly-_aNFseyxOwfDywFt5y6VZqWS5iEaemodL175cXIPpqqdvXn774jV5_url-cXrN88vfnj1EvStK4gD05B580APtDAYL6F8lcNAKkTjGswuzCUDzXtewedqnbH4T9BUgE3p_uurOEEbnlvsqNvv378hbXBwr9Z0j2O2HQeu--Zge21cMKMS_Eoeq9QSqPrXZIUGgayL9GsBXQnYiUSeQ5lxTPOkFKhB1b_IFOuTVznL_u-cZrjL7x_A3-QggA8BfhjChyF-GMEH-Jv0nn3z5RczHt-8__KLnMYoIFPShzLYPiySTH7-QL784lCJzwE5rGXpPTwQKdey6ynp9bB3gr33DkhXlner2feqIZEqRYb9_B0QWDnbUDaAMqjRTNaeEleQ2uzKz4480Z5--QU2RpqFBJOvvlF9Xuse-li73b5cJwjFJE7JV97YjwdjKCCCewuKxlwwPH9zXDB-v9HS2BtHk-E3-wyowWWG-5ZUw7dN8-CsaFGeEjorQYdWTPQ0hwYG_T_DZxDqKQkG-NG5RMVWp9RPifiIivWfXztA_qwu-JcswFor_p-71SFYqbxzP_yuNe5ITj5cMI6YOxAJPYzotYQlkDMXXCdlDpvVKUkyMbEzDJriRGjvO5-LZL7a0kzwNoTt8nktXCIRTdOXbdrUrkVdpuuFosm30OButLnR1USkedCpQqyGvsFcMFq8jYpm86mqaU5cXDvk6NgOJj7RDkcTD9CA57XP6o220TU4alZw2xU0L3V5m6wBDDKm39TVgmW4pUoTm-FG3Zv6Wnhd08BTlzfBGahIUBMZnMcLKCY_iZ1E2Yh37lwwB1wnxapjB1Ga8IhoPJQ69VZGvP4MCcWWpRkQFW1-h-Wl3uy0SZt89O06jUHKKoaPvuLD7JPwPl2SiRCD8jp9NNwLoUZxgdWvgBiIl7hWy9aVR3Fz1np9yrivXy4RFdJuQ-ENk0kVKFwwjdYduRPa0gW-AmewtYdhZw-jdg_Ce9jewXAQbOsgmAR7DsFSBdt7GAVbhzCQ75jZ3oO47ax7aIjNDpjG2_uYdMGEN9UxzdkEuMTuRv7vLHIbo-JSiRALO3CscpV0SrDlM-kIVmCe_rb50FRFo1wnLv22cjKsp-gYlRUtqtb_VdJcImFZ3EoTkllmHSxONgYQKjez9lWl89caGGyv5f-UoqKPMwGZK7OiZDyrFKl68sFEvUHSJhaJJfXbmtBzV9mtqjHzehCZcGfqDbdlSemIqKbC3dWOo6g26UZAVUeGN-YqVMGDRqDSb_nPhk-8uI6vkorZiof6Ldk8r2k-_A8ltuFR';
					$theme_options['overlay']	= 'eNrVPWtz20aS31OV_zDH1FbFdYJEPPiMpF2to82mKrETS669rS0XaggMSUQgBguAkrWOf9D9jftl1z0vDEASomzJkeLIXCIHPT09PT39mh6YTr3BePqhnE6mvfIqyVwinvKi9105HU1738xj6noz_Dac9lJ6y9cVfgmmvZskZvjRHU9783WahtgQspStWFaVve_o1J1-SKZ92XXJaMxcMOtH6FwweBcpn9E0nNHoalHwdRYj_AiJGEx7yYouBOr-tKfoEE1JbDUCzoLljFZWG1CS8zKpEp5ZrS78plVFoyVSZj2ASZTJf-yRYPDm7MV_kmoYMMkyVjxcJ6JhRVOOjE9WCxv5uG4HEsNoXVZ8ZQN4sEQMJgtrNOc4PkWKPogZzOkqSW_VaL_wPE-yUnTypz3kzTp3ahAPOlxc0iVf0QPyA2C8ht8lzUqnhMHnCsk1LRIqZzlCDi3WKRXTcWG2FXtfOVUBfea8sImEySFtjmYH0OwGopcL00sy5ixZslhW6pk30hhTVlWscMqcRkkm-AId-ttYOY5cJ95kpDlC09RBUqV8C6RyzQJJiVrhBeeLlEmh6QtKrhwba8zmdJ1WZiHMc8eaOaD8pt8XRKHkWUBLfs0MgSM2o31fQAHtSzfEfQaTMqs2-aRFA7LOYEnSA_J3ll6zKonuWDbAMlTU3nPJgv7uJQsG918yd-z5vt-9Ksgq7_mxyh90sKr_eKzynyGrOqTKf0SpCp4fq7wOqfIfUaoGz5BV_T-GVcPnxyq3Q6q8R2KVXCfdzOelqIYdOn34aFwiVa5nfxyrBl2sAh6t85wVES3Zp3h5QVwnz9xuL69TtlwieAKu35dm2Gd6xKPH8oh388oVweGCVc9Is3sdbBo_zjaEvrN1VfHMsGn4NNjU3oLdU989QRhHpQPCKqmw9UtOcfCJhmt856TmM1o4dswOq19ogdFcMOFsERqRXDD6Y1pcXC0KdqtjNQnTnUqYQL9lVeXTo6MZT-MVzQ6rJTC0YtFyBb2qw4iv9CNcJ6YVPbrJHaWnjgRoqR8fCazl0RwC_wqWEOcwWxz-li_2y1ZcMKsy7tTfPz1tIfaKimHb-0bHsx8tJuHq1ayE1ptlUsnl8hogXCKLETbwxbGv9iFmnQTskmZxyoowiZByCpR9ELBpMitooUUSmZfM5WpNzEOxU-kNK_mK6dB8TsmcOrQo-I0T85usDtllF1QoMFSp4_3rCJqI-NuZrWezlGmR0D1aY6uvSC-pEgcYuxYM8vwtMwqjlJfss-cVNOe1zh90Vl5_Y1aKbHvd5XS2rbtZy1mVNfZZO4PpBS3ALVwi4rKRP5PipEdW1gvA1qssrLOeqDjDoZE8e1wnFYxe5TzJdHbU9aT68Sa7XDCdOutmwaNVKJf8Jqx4PkOTP5u6ipGypUuvIFUWVGuyuN4FEPFt_8DzgwN_cNA_nIxfaIaqjh27LWgBtfB7rsFP4H9vMIDfh97ghZYd1Tll80pgkOoSmHq8TkmU0rI8QRihwGhUkWrlLEGtpqhaHezVOz1Ok9PjRANLEc2XPGPw6AgelVXBs8XpyxSTdOSCFddJxMrp8ZF6QPrkWzfwX5BgMCSj8WR0fIQYj9bpaYsPQqHXZI53krmdKJZds5TnzOGSMqJJO1_RJJ0SQ9ExJcuCzU962F7xaZLN-V_Ye7rKU4bqHX96p-3W4yN6WpP-L-BUySMwkA4mCktScZ5WSX4CKrv3rjmxDlENtsDskFJEhya9LZCwynUHobwM1B2mbvDwpk6P_ZB2Ds17BEOygsx4pVwn-hlWz-xK3z8IJgcjD3blGPfMxxYDOzama4E90QgYWRTRPKloqhiyLRfs7_bCB5-QNPD63sSbd_pzQug17_64qPgzg7wHTrbcl2-oLuKoWK9mT5Fve4leZ07hE1g4HA_Br7gzlDAsvE7YjepL01QYO232652tafLxaMQoYsn99Wq3XCLGQzhrjbb5BkNju8Wfw0H_hbEJhlwwPOCt8SjugIlRlNL4OqwQMAL1XFxEWru9pBVb8OKWnBXRMrkGc0w2elR0oXsEeGC4aAGjr2uAQR9Xwtgm84TFmsxfoLUkdTsBU8MKNdbY6k7X1ZIXejhg4JloaJPn60PrsCEMk4k5bZNP7_DHLKhd_hh6SvpHsR2WA2wFyn8aagxP5dh5pyVry1DflWYMXbyClTmEC8DfcB-OuAfyf_BQhy-0alAdLVTNECGQ3FezS_mCV7e5JtkwSj9Snh0w7K_SbVCaAR92qLGBUFEr_mSUvzd82AwfKP--153ZGUkuiaICCtrogxisIXJcIm4FJeGsi1R8Dx7ev0MaHOEFHeZyTqoU5UsOqlwiViE1K_o-3HYqC2JSghhcXN2GugxGRnUYBzd0TCjBVO9RXyuRRu9OjTPZBrz_NhOiXCKX1tCy5wrXesVeAtH6Uc6ixaPmZP1RS-uWDFT00gTAyAXREiZZLsNxxHkJO5z8gxcxuVxcsoyco2d-eKjVkeyBe0hnIKWt-HsCm5PfkJc0I_9guE9z8k--_rPeSZKEsrqt0xjSsEQOar6U3jYhTZw4ga2B0diswqwGjHjSe3P-69vzi0vy69vXl-c9XCKQnvT4usLtCt-XNIfv5b_XtICvYpFOrCwGwYjupAfsnP7J83___XcZ0FmigwmhcFc6EuEweCxBp1_pjAbGy4JDXDB5HFwn1zp0lQgdkRk7tR-oFhPjtlNSfJakOgQ_gn76703canMB7NKXsfDxEXw6Xg62xLjQKBHtnEl1IxXx-IFmAsOi4XNoWt1_OmdxDLapNBPyAvK3ZF4tYc0PyE-8JGfZgqWsPCBvL87as_O2zW5ZMFVjN3qY-aUcfDMQEwf88M-fILnvDNUmwomGXCJtYzbOGFb3GKxTtmtcIpu5niMEP73kaUr-BnxcIlPiEtfzdWrH7W9KTXPMIexfk375pnf6A3hIhJJf1-ActAeOYHdWjtxWanh6qtWM2oiNxFaAK5aDDByNyPmKFQuWRbegcAp2fJQD97xTi9Y-8MozGSjt5zQyUL7E94b9e81KIDPTaM5ykahBsTVYUMcpLCqX2Z1EtzQJEEibRmw42KZwbCPUQDfahLsr2PDdF7pr03h1juQHO8Hbmcm-GtD1JmDuBgfDIYw6eqFcXB60SyXj2v9duku-YjxLzVmNxLsrb4ZLtmILilDCPBSiMlaZeG-j-46UGgLSJEPgXCeWu9nnSL4jA-FOdjuhMDTEml1VrF05CONX2YzbgQVDKg0W0qjC8AFNayN8QLMryPJ3AW85QKjHQMGOC57jMdAfsI6fmUTqSL65j3ME7il-7b0m24G712RidXuoGPquJHHfJIkrnj_EuahV0-35tZBJjVeynBa0MhwzwYBbKyZjB_RB_OTJH8QPv3gFGTJsi6yE7tMr9Ddx1Q6SvedHsv_8SA6eH8mD50fy8PmRPHp-JI-fH8mTXCdMsrfDlPSfLs2i6odD8FuEUUXDGX9vopjmk7sKYxo4FLQrfbixzI-VsjIDHpOld9K7WM_KqEhmjFxccvJ6XZBX7KaUpr0HXDBcIh0finoLcAWKE2G-Q5wRUJa__x0dBBWzgouSv4c-wUnvrFwi4E1xwnhJGETMETiKdLUWXxL0GQmP4_VqdXt4iB06BnH91iCuGCQJ8XzB5FiwQRxIqO8byReG6a4eWZf6xp0YsgyRA1WxZohcMMc76aWLbWCBAnv3r6MGC991Mt7TruC_VlFwk4foeZEkPoGYbdzr7urbsjVpAu0K_4cbYC33vOWgWsDNHIV1moPxu7hiqoB3V5yMN1wwaZpuLzjxH6G2Uoz6SOUm8tejHdKJhUuKstK829jkgCYIgzAwK2dDd1XhCdFpAe9algcLwR66Tscodr81mY7MGuazsEoy3slTgPBD8ccc9TQ62GzFUEjEThDtKfJxY2x0-IKsvZ9M7rqvPJiP59RUlTTn0523lEt7D2ZtdHhcIszCytPkvSxmuJtjfdf38WhRcaw5qe46MSOI-a3ML-tSEyxwfKlbyf_9L_H67oSY9Lgw2oAHz_Xe9U7NV5FPeIfJ8ENylqZEdC9JwdA8sfiwbV_MuOaEyu8rl6C7gHEo8rRJdmchofcoZ75Jdi-1vt1N-7S80B7vJoBnMyBSrn2arJLKzoT4rnysTl9CUV-sTrrCbL2a1Vwn0mhABSas8lnxoj4sATl_A23kZ2hU3wWgVbik8OvUDD6ufQnlJuqjR69-rjGIy_IgjY5ZS00NQjWnBphcXFW16gseg6cm5AcmR5FqfdqrmOe1WFww8kABWynSs56qlGcZnaUsNk2worJWSC3vmfmCk5X8U6v8Un4rxXLgmXxSGlxc4vRcXHgzchvCx-_VR1QOUmBw8qpQKmGlelbRhepwSReI-6M0KhszQdTo1zWEzwu2QAJK3GG2Y78BAyQ1YLaNKBljg7mjerHuYq-7iyWb3BPXGO7Jp84F2r2uH5V5b8xiO2v7Lag2W9vP2yxtj7LJTryMkPOimvM04WFe8N9YVIUxXDBcJ2nZ8strONxLpmwhkPtJq-AaSiiAgqUwtdgmqYZQD-t9hPr3jWwkv0hiyk3Eupve0hNRUV5vaBS3TehaQWC5oz7z3o4ZSVe6zbz2XCKfa76Iaynqmhm-ekaXrrgbQNbMYBw1I_KKyvsvuH7tDh33hVxcV2aU7Rs0HZdrBvpyzQxkeG5u2e24XTNRl2scJeW7bgrhnZrEycDM5jQWO0luMNqKkj4msN77MAbmSORFisdnSV-zBKLeovOuUV-zw0Bu54e4oaCAunjh7cWLcV3G-vjMMJfKZpxfrSAGdvgXZYm_F0ssM_a47DB30VwimrIspnvKR5Qmedd-EZcmDIu7ORLsKyTfrwuqvb3H5YpvuJLy6C4JCQxTbBZ2MqaG7GLMYF_G_KQKj77g7lnR3MGlvUOhGN6kFondShYxY4VYB2uUp9ayubaPagzvuAG21YsFai5kHxkvWfBJtkgZ3hYUdxw3_N5JB7B0NxrZ4xzMUbXTfKOplBDbTXaNwTLTE3OPgNFVCF58QRtLXyof2bbXk53Qlt0G3Ofvc4bv44oY0cfTu_o9xHVfmCtb5dXtY1_xrZIV29eC78Mq6HuRPGsmoXfaLtXcS0F595MpEHBV5Eh-zOb8M_j1yFrr3gwMNhhoeZ-degyZggywNQPM1S6EDmqIHZrBwrGhG0by2YphJkIzWMT8pmYbFMsvrCh5RlOxMBiPaVWN6WZ10TdsUoBCowqERy2gDVwiXFwDsJn7QOp_fv3mnHx_fnn2408XGiMrCl4E_SCcJYsHlIWJlgVRX186_M73CmgNvkPDiFtkzdpf_VwizsYUrFxcDzSR8zdvXr_R7DeAKxZcJ-uVgR0Bky-XSUlyMBJkRW_Jkl4zMmMsIyuQkZjwgsQsZRC9HZK_MlKuC0YqTqIli67ILV8XpMxZCtNZHGpqzWDNS1wwKMTmEY73R9wqhrEdHPtJH_OYu_iNu2WH6qzHDfTViAzEvNQvo8ROWOb9ildLWA0yR77KYu_89IIXxe0Bma0rkqnnsAdhEWO1hgIfAdpX5SH5JWWgWEhV3BK6oElGbhKsEgcJXCdxMp8zzMmTK3Z7w4u4PMRSZr3PyiRm-pLfxrtV7OdqiRvPXCf1c6GypUppXFxUxLzKFpgQpShvZU2E1jD0KC-qiW24BQIVjAkVbXTW0ErXtNM0wQbETmQNULlL7PuYFituOBfJsFwi2uSYRdJslqPh21wwsQZi1zqH1oBANkm_FZVgaTJ_gfbqRApQkKTcWzW3TOVb8NiW4lk2vzLENyG1rwpcIvqN8YDuQouGBQRP76c9sHp7YEVTnSbR1d5I_f04gP1At2Z3oJUHkWoX30SOfRXEyuJbi-7UFlGnzbwWRF7weB1VDrjTjt5ZxjR6YhwZPDhoOJ127jCwIdTDbaP62-HWWdXI6WHBOQpSVVwwTQm3who8yhDWBg-fCPx-i5d8yUsLWLtHTQzLapXK7e81b9KsMwcrvaVKN7ddvv6qBbPtsTgFw8sD7-Qtl2ankuVcIht5ah4uXftyDZarnr599f35G_Ly9auLyzdvX17--PoV6FtXXDAHBpF5K0EPtDAYL6F8lcNAKuTGDZhdWEsGmveigs_VOmPxfwGqXDBR6fHrqzhBmz132FG33394Q9qg4EGt6R7HbDsOXFz3rcH22gyMSvAreaxKS6DrX5IVGgSyLtJvBetK4J0o5DmUFcc0T0rBNej6Z1liffI6Z9l_X9AMo_z-AfwkBwF8CPDDED4M8cMIPsBP0nvx3ddfzXh8--Hrr3Iao4BMSR_aIHxYJJn8_JF8_dWhEp8DcljL0gd4IEqu5dBT0uvh6ARH7x2QrirvFtoPCpEolVwiw37-HlwwrJptaBtAG_RoFmtPiStAbXLlZ0eeaE-__gqRkWYjweKr79SYN3qEPvZu4_cEoFjEKfnGG_vxYAwNRFBvsaIxAc_fnIDfb2Aae-NoMvxunwk1qMwwbkk1-7ZpHlxcFS3KU0JnJejQiomR5oBg0P8TfAahnpJggB-dK1RsdUn9lIiPqFj_51sHwF_UDf-UDdhrxf9zvz4EO5X3Hofft8c9wclH4CPWDkRCDyP3WsISyJWLkzKHYHVKkkws7AyTprgQ2vvO56KYr7Y0E7wNYbt8XgtIZNP0ZZs2tGtBl-l6oWDyLTAYjTYDXQ1EmgedKsVq4BsEoMXb6GiCT9VNU-LaKUfHdjDxiXY4mvxcMA14Ufus3mgbXFyDomYHt91B01K3t8EajEHCFLo2W4ZbujR5M9zoe1vfEK97GvbU7U3mDFQmqMkZXFzHS2gmP4tIomzkO3cwBxfF6mNcJ1Ga7BHZeGh16lBGvEoNAUXI0kyICpw_YHupg502aJOOvt2nMUnZxdDRV3SYOAnv0yWZSDEor9NHw70QahQ3WP1yiIF4IWy1bF15FDdnrXfajPv6tRNRIe02NN4yWVSBAtDALt8It22I-vU47RGGnSOM2iMI72H7XDDDQbBtgGAS7DkFSxVsH2EUbJ3CYDDcPYK47axHaIjNDjaNt48x6WIT3lTHMmeT4BLRjfyXXmQYo_JSiRALO3GsapV0SbDlM-kMVmCe_rb50HRFo1wnLv22ajKsp-gYlRUtqta_e9ICYVncKhOSVWYdJE42JhAqN7P2VaXz15oYhNfyX11R2ceZYJkrq6JkPqsUpXrywUS9jdIGFoUl9Zuc0HNX1a0KmXlZiCy4M_2G26qkdEZUQ2F0teMoqg26kVDVmeGNtQpV8qCRqPRb_rOhEy-u42umYrbioX7jNs9rmI__D1kD_90';
					$theme_options['stackcenter']	=  'eNrVXX1z2zbS_78z_Q44dW6mmce0xRe91vadL821nWmTNnbm7qaT4UAkJLGmCB5J2fGl-UD3NZ5Pdrt4I0hJtJzYqd3UsQQugMUPi8XuYsHQqTfsT9-X08m0V14mWcRTXvS-Kaejae-reUxdb4bfhtNeSm_4usIvwbR3ncQMP7rjaW--TtMQC0KWshXLqrL3DZ260_fJtC-rLhmNGbT6ASpAu4uUz2gazmh0uSj4OouRfoRMDKa9ZEUXoun-tKf4EEVJbBVCmwXLGa2sMuAk52VSJTyzSl34TauKRkvkzHpcMIMok__YPUHnzdGL_yTX0GGSZax4SkzDjKYcgU9WC7vxcV0OLIbRuqz4yibwYIoYDBbmaM6xf4ocvRcjmNNVkt6o3n7meZ5kpajkT3uIzTp3ahIPKlxc0CVf0QPyHbR4Bb9LmpVOCZ3PVSNXtEioHOUIEVqsUyqG48JoK_aucqoC6sx5YTMJg0PeHA0H8OwGopYLw0sy5ixZslhW6pk30i2mrKpY4ZQ5jZJM4AIV-tugHMcTbzLSiNA0dZBVKd-iUTlngeREzfCC80XKpND0BVwnl47daszmdJ1WZlwizHPHGjk0-VW_L5hCybOIlvyKGQZHbEb7vqAC3pduiOsMBmVmbfJRkwZsncGUpAfke5ZesSqJbpk2aGWouL3jlAX93VMWDO4-Ze7Y832_e1YQKu_pQeUPOqDqPxxU_hOEqkOq_AeUquDpQeV1SJX_gFI1eIJQ9f8YqIZPDyq3Q6q8B4LKk2bm01JUww6dPnwwkSrXsz8OqkEXVIDROs9ZEdGSfYyVF3Ri5nZbeZ2yFcETMP0-N2CfaBGPHspcIt6NlSucwwWrnpBm9zpgGj_MMoS6s3VV8czANHwcMLWXYPfQdw8Q-lHhgLBKKiz9nEMcfOTGNb51UPMZLRzbZ4fZL7TAaIJwtgiNSFww_zEtLhcFu9G-mqTpDiVMoN6yqvLp0dGMp_GKZofVElwwrVi0XFxBreow4iv9yIlpRY-uc0fpqSNBWurHR6LV8mgOjn8FU4hjmC0Of8sX-0UrXDCqjDv1948PW4i1onzY9rrR_uwHCyScvRpKKL1eJpWcLq9BXCKiGGGjvTj21TrEqJOgXdIsTlkRJhFyToGz94I2TWYFLbRIXCJ4yVxcztbEPBQrlV6zkq-Yds3nlMypQ4uCXzsxv85ql11WQYUCXZXa37-KoIiIv53ZejZLmRYJXaPVt_qK_JIqcVww2LVcMMjzt4wojFJesk8eV9Ac1zq_11F5_Y1RKbbteZfD2TbvZi5nVdZYZ-0Iphe0CLeIiMtG_kyKk-5Z7V5Atl5lYR31RMUZDo3k2SupYPQy50mmo6OuXCfVjzfZRejUUTeLHneFcsmvw4rnM9zyZ1NXASlLusfrNchao8UJL4CLr_sHnh8c-IOD_uFk_Ewjqip2LLegRdRq33NN-wT-9wYD-H3oDZ5p4VGVUzavRAtiCqDn43VKopSW5QmSCAVGo6p3epwmp__i64JcXBTQFYuJF5Dv4XtJzllxlUSM_Fxc8KskZsWfjo-A9vhonZ62hiMUs-nNHfvd3R1cJ_qRlH-WXbGU58zh8PwoOSXHZVXwbHH6YkWTdEqOj9T3Y0qWBZuf9LC84tMkm_O_snd0lacM1TT-9E7bpcdH9LRm_ddq5ZQ8go3OwYBfSSrO0yrJT0D19t42B9YhcsEWmh3Shs3h1twWLJisuoJQQobqli1rcP9blu77Pvcr3KYj6JIVZMYrPdBP2L3M4vL9g2ByMPJgcY1R9D-0XDDsWF-uRfZIPVmEKKJ5UtFUAbItpuvvtqYHH-H8e31v4s077TIh9Bq7P867_URn7Z6DJnfFDdVFHBXr1ewx4raX6HXGBj4CwuF4CPbBrS6BgfAqYdeqLk1T3Om0HWStbM2Tj86lsU4l-uvVbkWMh2nWHG3b4odmCxZ_Dgf9Z2ZPMAzgQW3djkIHthjFKY2vwgoJI1DPRaS123NasQUvbshZES2TK1ZOyUaNii50jVwwD_4WLWK0WQ0x6ONKbLbJPGGxZvNnKC1JXU5gq2GF6mtsVafraskL3R1cMHgmCtrs-frwuQm8qxTRwDy2YW97GRbJLqMKzR39o0CHyYCdAqU_DXULj-XweOc-1pagvis3Md_H_sscjH5AN9wHEfdA_g9m5vCZVgyqotVU09APpK-sRpfyBa9ucs2yAUo_0lbktPc3aTQovYAPO5TYQCioFX80qt8b3m-cDlR_3-uOz4wkSlwiNYDCkngvOmuInPA-QUU461wiFag_QEACeTjM5WhUKsnn6U75mkJSVvRduO3oGUSjhKm_vAl1Aov0x9CDbWiVUJKp2qO-VhyN2rtVzGQb5f7rSsiGnEvDyJ5TWisSG3lR-kEOoQVQc6S-yY4oGajiZZhkuXSWseoFrFxc8g9exORiyTLyAu3tw0OtZmQNXFwbOj4od4DvE1h0_Jo8pxn5B8P1lxNwA_-iV4iEqKxuZBXpEiGXTQLj9E2AAl2rWYWhBujopPf6xS9vXpxfkF_evLp40SOirZMeuPm4-uD7kubwvfz3mhbwVUzBieVqE3TPTnpcMNb0z57_---_S-_MkgqM0oS7YoRIh55gCSr6UocZeKZCSkB5HFwnV9oPlQ06XCJcXHVqP1AlxmFtx4n4LAGAhNd6fAT19N-bbasVA7RLXzq2x0fw6Xg52OKwQqFsaOdIqms5E-N7Ggl0i_uYQ9Pq7sM5i2PYakozIC8gf0_m1RLm_ID8yEtyli1YysoD8ub8rD06b9vowM1XiW-j-xlfysHQAjFxwKj-9AGSu45QmloRDjTMlyCGZuGMYXaPYbPJdg1ExkhEJc04kp9e8DQlfwecyJS4xPV8EgyGZDSeuP1NqWn2OYT1a2IpX_VOvwODh1Dyyxr2-nbHEazOypHLSnVPT7V2UQuxEWwKcMZykIGjEXmxYsWCZdEN6JmCHR_lgJ53avHaB6w8E07SZksjnOTL9l6zf69ZCWxmupmzXFxEXVBsTSuo2lQrKsDYHdm2NAkwSJv703CgGZNRjFBoaWi3WioD1x-ZEM88AW0pVdKKFoska_jiba21c5MabdLd5n747jNdtbm_dfbkBzvJ2yHHvurQ9SawIw4OhkPodfRM7QsISsm4tomX7pKvGM9Scwoj290VSUPsVmxBkcpBY7kQOa_KBPA2qu8IsiEhTTIkfmTRnH0O2ztiEu5kt2EKXYP3-ZFno56xu2zgdrSCbpYmC2lUoUuB-3PDpcC9W7Dl71wi3nI0oDNppWDHBc_xgOcPmMdPDCt1hOPchznc9hRee8_JduLuOZlY1e7Lr74tbNw3YWOwOe_jxNPK1vb8WsikxitZTgtaGcT0ATJGirViMpuJPmKfPPoj9uFnzw1DwLbISug-vhR-43rtYNl7eiz7T4_l4OmxPHh6LA-fHsujp8fy-OmxPHnELHs7tpL-4-VZ5PNwjn5iVNFwxt8ZL6b55LaUl0YbitqVNtxYBtlKmZkBj8nSO-mdr2dlVCQzRi44ebUuyEt2XcqtvQcEXCJEH4oMDDAFihOxfYc4XCLgLH_3OxoIyvEFEyV_B3WCk95ZRcCa4oTxkjBwuyMwFOlqLb4kaDMSHsfr1erm8BArdHTi-q1OXFzRSRLimYMJ1GCBOKRQ3zdcIjgMY2Y9si71XTrRZRlcIgJVsWbYXDD2d9JLF9vIAkX29tejBoRvO4H3tCn46yoKrvMQLS-SxFwn4LONe91VfVu2Jk2iXe7_cIOsZZ63DFSLuBnosE540H8Xl0cV8e4clPEGIU3T7Sko_gNkTYpeHygBRf56sIM7MXFcIgKkZ6S9yKGZIAzCwMycTd2VbyZEp0W8a1ruzQW778wdo9j91mA6wnMYz8L8x3hcJ6ZA4YfijzkKalSwYUVXSPhO4O0p9nFhbFT4jNDeTSZ33UQezMdzavJMmuPpDn7Kqb0DWBsVHglYmFOavJPpDbcj1nd9H69yKsSag-rOHDOCmN_IILVOPsGUx-e6lPz_f4nXd1wnxMTYxaYN7eDR39veqfkq4glvMaJ-SM7SlIjqJSkYbk8sPmzvL6Zfc8zl95VJ0J3SOBRx2iS7NbXQe5DT4CS7k1rfbqZ9XFxcXGiPtw7AsxkwKec-TVZJZUdCfFc-Vkc4ocgcVsdlYbZezeoTa9xARUuY97PiRX3iAnL-GsrIT1CovgtCK5VJta9DM_i4tiWUmajPL736uW5BXFyDB2l0zFxcam6Qqjk0TM1Reay-wBgsNSE_MDiKXFzrk2IFnteCXDDkgUJrpQjPeioHnmV0lrLYFMGMyuwhNb1n5gsOVuKnZvm5_FaK6cB7HElp2hIH7MKakcsQPn6rPqJykAKDg1epUwkr1bOKLlSFC7rAtj_ITWVjJNg02nUN4fOCLZTQJK4w27DfoAGWGjTbepTA2GTuqJ6s2-B1d0GyiZ64oHBHnDpcJ2j3vH5Q23tjFNuh7beo2rC2n7chbfeyCVwnXjPIeVHNeZrwMC_4byyqwhiIk7Rs2eU1Ha4lk_IQyPWkVXBNJRRAwVIYWmyzVFOoh_U6Qv37WhZiOj0yU242rKvpJT0RSRf1gkZx26SuFQQmQOqD8-0tI-tKt5kXWuRzjYu4cKIukOFLZXR2i7tBZI0M-lEjXCIvqbzZgvPXrtBxE8h1ZUTZvhvTcW1moK_NzECG5-b-3I57MxN1bcZRUr7rDhDelkmcDLbZnMZiJckFRlte0ocE5nsfYGCM5HmaWNbTw0HS15CA11t03lwi6ms4DOV2PMSdBUXUhYW3FxbjOrH14cEw18VmnF-uwAd2-GeFxN8LEmsbe1g4zC2ziKYsi-me8hGlSd61XsQ1CgNxN1wiwb5C8u26oNrae1hUfINKyqPbJCQwoNgQdgJTU3YBM9gXmB9V9tJnXFw9K5o7OLW3KBSDTWqx2K1ksWVMM-uARllqrT3XtlHNxjtukG21YoGbc1lH-ksWfZItUob3XDDF7cUNu3fSQSzNjUb0OIftqNq5feNWKSm2b9l1C9Y2PTE3CxhdhWDFF7Qx9aWyke39erKT2tq3oe0X73KGb9qKGNHH07vq3cdFXhgrW-XVzUNf3q2SFdt3B98HKqh7njxpkNA6bed77qWgvLvJFAi4ypQkP2Rz_gl4PbDWujOAwQaAlvXZqccQFATA1gwwVgySpvSmTu2QFDs0g9XGhm4YyWcrhpEIDbDw-U2-NyiWn1lR8oymYmLQH9OqGsPN8iykDJscoNCoLONRi2iDCdcQbMY-kPufXr1-Qb59cXH2w4_nukVWFLwI-kE4Sxb3KAsTLQtcIgW_dPitbwzQGnyHhhH3ypoJxPoVm40hWLEeKFwiL16_fvVaw28IVyxO1qv6ZjaAfLFMSpLDJkFW9IYs6RUjM8YysgIZiQkvSMxSBt7bIfkbI-W6YKTiJFqy6JLc4MXtMmcpDGdxqLk1ncnkfztaYR5hf3_EPWPo28G-H_Uxj7lk37hvdqjOetxAX6vIQMxL_ZpJrIS54i95tYTZIHPEVWaM56fnvChuDshsXZFMPYc1CJMYqzkU7RHgfVUekp9TBoqFVMUNoQuaZOQ6wVRzkHASXCfzOcOYPLlkN9e8iMtDzIc2F0SSmOlrfxtvTbGfqyluPJ_Uz4XKliqlcXUR4ypbaEKUorwVNRFaw_CjrKhma8MtFKhgjKtoN2d1rXRNO0wTbFDsbKxBKleJfUPTguKacxEMK6JNxCyWZrMcN74NEqsjdqVjaA0KhEnaragESxP5C7RVXCdCgIIlZd6qsWUq3oLHthTPsvmlYb5JqW1VENGvjAV0W7O4sYDg6fW0R6veHq3iVp0m0eXejfr7IYD1QLdmtzQrD1wi1Sq-jhz7PokVxbcm3al3RB0281oUecHjdVQ5YE47emWZrdET_UjnwcGN02nHDgObQj3c1qu_nW6dVY2YHiacoyBVBfCUcMutwaMMsdvg4ROB32_w2i95bhFr86jZwrJapXL5e83rOOvMwUxvqdLNlZkvv2jRbHssTsHw8sBbeVWmWalkuYhGnpqHS9e-oYPpqqdvXn774jV5_url-cXrN88vfnj1EvStK4gD05B5T0EPtDBsXkL5KoOBVIjGNWy7MJcMNO95BZ-rdcbiP0FTATal-6_v8wRteG7ZR91-__430gYH97qb7nHMtuPAdd8cbK8NYFSCXcljlVoCVf-arHBDIOtcIv1aQFcCdlwikedQZhzTPCkFalD1LzLF-uRVzrL_O6cZevn9A_hJDgL4EOCHIXwY4ocRfICfpPfsmy-_mPH45v2XX-Q0RgGZkj6Uyds78vMH8uUXh0p8DshhLUvv4YFIuZZdT0mvh70T7L13QLqyvFvNvlcNiVQpMuzn74DAytmGsgGUQY1msvaUuILUZld-duSJ9vTLL7Ax0iwkmHz1jerzWvfQx9rt9j1BKCZxSr7yxn48GEMBEdxbUDQG4PmbA_D7jZbG3jiaDL_ZZ0ANLjP0W1IN3zbNg7OiRXlK6KwEHVox0dMcGhj0_wyfQainJBjgR-cSFVudUj8l4iMq1n9-7Vww-bO64F-yXDBrrfh_7laHYKXyzv3wu9a4Izn5XDA4Yu5AJPQwotcSlkDOXFyclDk4q1OSZGJiZxg0xYnQ1nc-F8l89U4zwdsQtsnntYhENE1ftmlTuxZ1ma4XiibfQoPeaNPR1USkedCpQqyGvsFcMO54GxWN86mqaU5cXDvk6NgGJj7RBkcTD9CA57XN6o220TU4alZw2xU0L3V5m6wBDDKmX-fVgmW4pUoTm-FG3Zv6dnld08BTlzfBGahIUBMZnMcLKCY_CU-ibMQ7d4CDk2LVsYMoTXhENB5KndqVEe9IQ0LhsjQDoqLN77C81M5Om7TJR9-u0xikrGL46Cs-jJ-E9-mSTIQYlNXp48a9EGoUF1j9woiBeNWrvHNqXXkU12-tl62M-_pVFFEh920ovGEyqQIFoNG6Iz2hLV34-GKGrT0MO3sYtXsQ1sP2DoaDYFsHwSTYcwiWKtjewyjYOoTBYLi7B3FlWvfQEJsdMI239zHpggmvu2OaswlwCe9G_hsu0o1RcalEiIUdOFa5Sjol2LKZdAQrME9_23xoquKmXCcu_bZyMqynaBiVFS2q1r9o0lwiYVncShOSWWYdLE42BhAqM7O2VaXx1xoYuNfy31NR0ceZgMyVWVEynlWKVD35YKLeM2kTi8SS-t1OaLmr7Na6MXUZug6d9XXoRNyVlZl4psHhtvQpHSrVVOh27TijapNuRFp1yHhjEkMVVWhEMP2WYW34xGvx-EaqmK14qF-yzfOa5sP_XDCjx_jB';
					$theme_options['rtl']	=  'eNrVPWtzG0dy313l_zCB66qsOi6JfeBpkjmdzudL1Z19keSqpFxcqq0BMFww1lxc7O7tLkjxZH2QbSmKk_-QSjmxLV1sn2w5ib7mV1ww_ybd89rZBbAkJVImLVMEZnt6enp6-jU9K9p32s3-vazf6zeygyAaxmGcNt7J-p1-463xiNrOXDC_tfuNkB7H8xy_eP3GUTBi-NHu9hvjeRj62OCzkM1YlGeNd2jf7t8L-k3RdcroiAHW-9AB8E7CeEBDf0CHB5M0nkcjhO8gEa1-I5jRCUfd7DckHbwpGBmNgDNlCaO50QaUJHEW5EEcGa02_KZ5TodTpMx4XDCTyII_myPB4OXZ8_8E1TBgEEUsvUpEw4qGMTI-mE1M5N2iHUj0h_Msj2cmgANLxGCysEbjGMenSNE9PoMxnQXhsRztj3GSBFHGO7n9BvJmnlgFiAMdbtNpPKNb5D3AeAi_MxplVgaDjyWSQ5oGVMyygxyazEPKp2PDbHN2N7fyFPqM49QkEiaHtFmKHUCz7fFeNkwviJg1ZcFkmstnTkdhDFmes9TKEjoMXCLOF-jQXFzHyu6o5_Q6iiM0DC0kVcg3RyrWzBOUyBWexPEkZEJompySA8vEOmJjOg9zvRD6uWXMHFC-1WxyolDyDKBpfMg0gR02oE2XQwHtU9vHfQaT0qvWe6lFA7Kuw5KEW-R3LDxkeTA8YdlcMEtbUnvGJfOam5fMa519yeyu47pu_aogq5yrxyq3VcOq5sWxyr2CrKqRKvcCpcq7eqxyaqTKvUCpal1BVjV_Hla1rx6r7Bqpci6IVY5wM6-WomrX6PT2hYlUNh_8fKxq1bEKeDRPEpYOacZexsvzanlm13t5tbI1hFwn4Pq9boa9okfcuSiPeDOvbB4cTlh-hTS7U8Om7sVsQ-g7mOd5HGk2tS8Hm6pbsH7qm1wnCOPIdICfBzm2vs4ptl7ScHVPnNR4QFPLjNlh9VMlMArAH0x8LRJA_4imB5OUHatYTcDUpxJ6QOY0z5P-zk4YD2k4jbN8ZxCHoxmN1G8rnwKLd44SS-qnHd6QaTiOLdsZQ8Cfw9Ih7YPJ9sfJ5HRZCmBRFFvF95dPV_A9XCJj1-p-UXHsfYM5uGoFC6H1aBrkYpmcEgjPXvglfKORK_cfZps47JRGo5ClfjBEyilQdo_DhsEgpakSRWReMBar1NMP-Q6lRyyLZ0yF5GNKxtSiaRofWaP4KCpCddEFFQkMlak4_3AITYT_bQ3mg0HIlCioHpWx5Vekl-SBBYydcwY57poZ-cMwztgrz8srz2uenOusnObKrCTZ5rqL6axbd72Wgzwq7S9P7C8-hleBWSMdNuu4AyFJalBpsFwwbD6L_Fwi0Ym60m9roTM3UcroQRIHkUqI2o7QOE5vE6BVJNoMeDQE2TQ-8vM4GaCVH_RtyUPRUqdKkCoDqjJZXFzqFIh4u7nluN6W29pqbve61xQvZceajeZVgCr4HVvjXCfwv9Nqwe9tp3VNiY3sHLJxzjEIIQOad-chGYY0y_YQhusuOsxJPrOmoElD1KaW0Kn7u2GwvxsoaCGeyTSOGDzagUdZnsbRZH_x3eL75cPFE7L4evn54ofFN4uvF18tH_V3dyRcMGmSt23PvUa8Vpt0ur3O7g5i3pmH-xWG8IE1vQ4u0Hp61xPHokMWxgmzYkEh0SRcImXfLJ4tv1h8L8j8z-Xny88WT6Hp8fLR8os-0dTuUjJN2XivMaNBmMf9IBrHv2J36SwJ2fYwnuFPY7_aurtD94tpfQTszOIhGE4LE4gZyeM4zINkD1R640550jXy7K2B2SDKiA5NfVVqQRSKDlxcuWmoE0xgV5tAadC2uX3LGZga6JXjrLUtTPNwRHN6sjVUw5-nKUTLP4QhWUoGca7m-gqGUe9e193yelsdB3ZvF_fW_QoPazawbYBd0uAYWTSkSZDTUDJkXZrY3eygt15cIp_gNJ2eM6519bjcK979fAHzK8Z_55yHOSvfUGOMhul8NriMfDuV6NWmG16Che1uG_yPE6MMzcLDgB3JvjQM0ZQqF8vY2YomF-NV7fgK7s9nm3Uxns8Za7TOh2hrG8__bLea17RZ0ATg2W-BR3IHrIyklI4O_RwBh6Ce06Eaeflg8ZW02GC6v10-WD5e_C_Y7af9lY45nciOLmBefLl4tni-_AIQXDC0tKWL5_Dlme6OfrLuDoo650Y6GAeMa3gX-MMHFkN-tXyg6PgJTPEDJOvp4ltFStfARef5NE7VNDyTGoXiP-DvB30lzeLwu7xKttyZLf3YXFyjarRjgGxy8dD5Uj9yhYDDYFZwq4S-wnBZDq83Gr2quDVtYfFcXBfHzxIIPoJD5p-GI_aW-B-c3vY1pUVkRwNVOeAwYgl-TD6J8-NEkawZpR5JHxEY9mvhYUglgg9rNF6La7NZfGnshNM-3zwh2ImmU58f6ggu8dIEClviHh-sJHI8CgZ9Ys3TkH-3L8QbRDK2EzEhWc3y2kaUkS-Xlxm965cPlTpKQDIQgINjX5XRiBAR4-mSbvEFmOzdaSr1Ueq9WdH01kGefndxCRErqgk55cIW6sRkPm-9L6ZQYVB5pgWfJOEZA_U81aE0soC3-EGUyMDe4-ofg69vyPIzrrVB5f9l-ZCAOv9-8d3yc7K9rTSS6I3bSKUygXWAFTo9AyPxzfJfFl-Txb9cIlwitALw10P4-Gj5CJoB5kcwMC9cMOlTGO_f1EYTpGb5sUAH20EYqGEZQMehPWAaRnSDHDNcJ0DEXmPxPxhQotH5FOcBo_zYIBzjXiOe57iV4fuUJvA9-9OcpvCVr-ReYxCCymkQjAv3GsDv_i8c95NPPhFhoSFYmHbyNyU7EQ5D0Ax0_YFKnmBkztmGYf4oOFTBsUBo8fwbxMTGE9FUhNHVzFc8CEIV7e9AP_X3KnK561wwduru31wwd2l3Bz7sTlv7v7QdV0X-TWhtSSQbp5EfCe3csc9pGqAo0BxaNMzPPpd3MQ2gXCezGvaX5-Osm880ZbJgr3tOM8KkNIqFBa772ad0fTQCI5zpSRHHI78NxvkUJHiL_D7OyPVowkKWbZEPb11fM0W5XXCmPk8I6a3ShQXdBSsVbZrIahZpB8H3b8dhSH4LjFwifWITQ2Ts5qqglMdso5yopM1bjf33wFNcIpT8_RychOrAQ9iJRX6LD0_3la6RG6-UM_NAO-wm-4630yHvzlg6YdHwmNxcMDS7Owlwz9mviLejc1rK3ynltFxcge8m-9OcZUBmpNBcXE94egcFVWNBF1dikWnS-tS8oTmAQFo2ae2WXCJM5Ep8rtgBbz6VnrHb0bmkcVww-lGooBlNXCdBVIr4q1pqo13rrMKdFOS49jXVtWwSa0dCo7IBvJo5bcoBbacHRrS11W7DqJ1r0hIgUzIWK2d6ak_jGYujUB8fCbybUnbIuxmbUISy0MtOebGu9Bqcle4bsnkISIMIgS9Zzug0VQI1mQ-7t9mjhaEhxn3JQ11Hu2om4zZgwfhMgfl0mGMsgva4FItgRTZcJ8vdBLzmgEOVXDALwR6lcYJcJ1Q_wzq-YvKqJulnX8ypvCP5deo1WQ-8Zk26Q8cejZQLrbudV0B-UnK6qZPTeZycx5HtuDvujamWeiVkQuNlLKEpzTXH9HGVXSgmbUxUbUDv0tcGtM83E9h03YFH6-XRXisrvn357h7oaG0Dyc7VI9m9eiR7V4_k1tUjuX31SO5cXD2Su1eP5N4lJtnZYEqal5dmXpAUxxhcJw5z6g_iuzqKKT85qXCnhENC82JgD0NhXijB0sNgyOAxmTp7jcXXi-e8MOMzsnyw_IIsH8H3Z4unPKVcJ8-bvuMpNyzneNKATjzf7_PSEHAP0j1u0n2cJVCb3P0EnQYZDIPbktyFPt6eOIN6uPwUa0AW_05cMPFPyweLbxdPMHEIQ_24_AJcMP4JSOCQn_Izrq_I4sXykaCDEwk0_IBtny__mSy-XFw-JovnmNQkQPDjxX8vXojOD3AYfmKGc8Lxa2i23QrNNqc58PE8ROeCsIEfoMjvK0lcIoaZqwaZZ-qeIR8y85HJeTpniFwwx9trhJN1YJ4Eu_PRTmmV7tSuraO8zY9mQ-8o8dG5I8FoD8LCbqO-q2uKb68MtCnD0F4Bq0RcMBUf2FwwLudSzEq2prxYK4E319N0V1wwaRiuL6dpX8hxhhj4goppxK8LO1fka8fzTGpRqqoE0Hi-53t68Uxoc1WqF6i59FSAN63MuQV6512FpM2HW5lMTRIQs2ZYJjrayFOAcH3-R59RlTqYbMWAi0doEFNK8nFvrHR4jaw9m0xuuqjdghia6pqZ8nzqU6xiac_ArJUOl4RZWH8b3BUVGVwnc6xpuy7edJUcK0-qvgpOC2JyLFLhupAGk6WLb5efLh8vPxVmkpt68n9PCPoFw3mKjLSOGU3vEJ3h5y4D4MezyjuNff2VZzPuYD5_mwgLjDZaoTeqXFyebFetkCZNn6-5Temb1BdxtnnCOIhOLKZsXdRhdhCdSfmvdxlfLkd1ilc3wLMBECkkJAxmQW5mZVxcWzyWx0k-r8WWh3V-NJ8NigN3tLQcE1Y6zeK0OP2xRTURuldfogP22eIvi2dym_AeRhWXeczriceF9yF9V3WI6hTPFQb-UgEQXkuvq1wiC6HKc8RCI1nF63Jmg2_HZQlmSZF8dZgtuYjGcBVKOl2ZPM4XL_VAp4OiXCcWg7cScPZg9e9RkPPNHvQdsSqTJJxnOmYyGQ2CR4HUjFwnpB15bYFFdBCykW4CgkS9lRSi6_oLclKskpSlG-KbGAzr5INM4-JVCKiShFwwXCIPb9CcTeI0YJl2vIS6gI-_kR-xDo9OZONtOkHc94WBW5kJdkc3syTijrcGElDiVjZDmRUYILQEs25EwRgTzO4UknASe21sWjftVe55L70S9UzfxF63Mov1rG1WoKpsrT6vsrQ6yio78XpIEqf5OA6D2E_S-GM2zP0RXDAHYaY8HQixsArxXCeM01T933Mezb1QTmCBAzexLvnwxEZWdqCA4iooZSFMe2SSW0DIh8UG5uWUD0UJJASDEPD9VZRG_gTm58nqEAqB0io9XuRf6BQUylXoQkdh-amqKFiPGVwnIfWsfkNJMlbc4zeJ5I1AVCiqUMheATLmaKOShYj44Ro-43JXe9bc9bJtkXI3bz_VXFyMaqmLUQOQ3bG-GbnhZlRPXoyypHRvuuWF96ECKwLzn9AR33hiP9JKjHcf9etpOAR7b_Fi-RBF8eJ50lQ8gaA9rb0o1lT80JDrGcKvj0igOma4p2IGDivSHlhlfPH80JcC0SbOIIq34tNxZRgGSZ2Y8EscGms9a7zTs-ap1BXfXTxr9L3CIQ1ZNKL14uJpxpjQtcwpIOuY0z6dmmlJuXm4-F7fXCeDTfWIXCf6LpxXruZVGA9PkCE-kJ_l9SziVwXK1Vq1bGqdWoaAKxDWgMr57DVurxlNLNwI7JRSpCZ-siZGzFieV8Md6f1VbLXpLmuD3S2B1bndGIJsXDA9XCffGyNJY4QgmoQML5zya7IrUUCvBlj4R6UEfwIGMd_oSWAMIyDWew8FBsNj6OkrJozOfIhpUlqSK5MfSlh7G6FXFN9_QZz2jdzM3c3dzuOuOEyVzZL8-KLvh-fBjNVva-dMnOL-5PLR4q-vg0ldtbfjg1dXd2exCPaZeIJAYBXwmOUxns6oq0cvwDY8hk-PXgevzqwIz3xNv9OvrfSt14643XH6pkpcMISYWg7pcVF2IyA2qAQDx4pS6IhnM4aZGcVenvowDXh1mZ4vvsMQTex5m1wnqIVi9Wt1V68CWErS6BLxCtAKybYGWM0cyYsI6G38wC-G466TXCd7RWCpxmFpGqde0_MHweQcbWtPiRS_lZFZ8YmvtFDq_2wux8oU9OUGfIWhR969efODm8qV0IAzNgrmMw3bAdbfngYZSWCVyIwekyk9ZGTAWERmIGcjEqdkxEIGUeg2-TUj2TxlJI_JcMqGB-Q4nqckS1gI05lsK2r1YOWrI2iC9CMc72e6sA7DWzj8pT5j06-DKN1F3JYHbban7tFEIP-ZegUqdsLrXDDvx_kUFoSMkbXiUkCyfytO0-MtMpjnJJLPZzSHdRzJZeT4CNA-y7bJH0MGgTnJ02NCXCc0iAg4RFOCQk5GwXjMMI9PDtjxUZyOsm0seVfKJgtGTF0TXXmjj_lcXK5y6XmveM71vtBM1QRPay2Uj6KUVGC5QtEUST-sdKcWsz0rEKh7MLl2XFxFZwwt1VCVOG8FYiOyEqjYKuZlX4MZR3HM04LpcJVnBkmDQYL3QFZAjIHYocomliCQTcJXRk2Y6WSnp_xCnvXkJEmXWs4toobbofzotZDK2wUhfUs7DFwnoTV98dNhdU6BFc1-GAwPTo3UPR0HsB8o2OgEtOIcWO7jo6Fl3hoyjkeMRbcKY6lygE4FXCJJ49F8mFsJYFJ7S1tNh48jwg8LbapVTYl6JoR8uG5Udz3cPMpLCUq8VYCClKdAUxAbgREGaNzk4Bkfgd8fRjB5csNcMFauVhnDNJ-FYvtXbsPNIwvL-YVS1_ei3nyjArPuMT9sxBtcInfEfahyp4wlPLW6rx9ObfMaFtYk73_4_m_evUlufPD-rds3P7xx--8-eB80rs2BPY1Iv_KiAXoYLBhXv9JrIDly4whsL6wlA917K4fP-Txio78BVB6iUuMXl7a8KntOMqbO-b1cMK008Lma0VMcW2445q6try8OMHFcJ5T5NszA04zFC_h7LnT9VTBDO0Dmafg2Z1kGPOMVVNuimpwmQcZdD-j6t6J8fu-DhEW_vEUjTA80t-BcJ9jy4IOHH9rwoY0fOvABfoLGtXfefGMQj47vvflGQkcoF33ShDZxM0t8vk_efGNbSs0W2S5E6B484OX0Yug-aTRwdIKjN7ZIXQV_Be09iYjXqJF2M7kLXDBGPT60taANepQL8fvE5qAmueKzJeoI-m--gchIuZFg1ds7cswjNUITe1fxOxyQL2KfvOV03VGrCw2EU2-wojQBx12dgNssYeo63WGv_c5pJlSiMsITs1Cxb53CwVVRotxcJ3SQgerMGR9pDAhazV_AZxDqPvFa-NE6QH1WXFyX6BP-EfXpP7xtAfi1ouEfRQP2msV_Plsfgp2yM48Tn7XHGcHJfeAjlmMMufpF7lWExRMrNwqyBILdPglcIr6wA8zk4kIotzsZ8yrKwsD08KaL6ek5FSCehlMXqarQtgGdhfOJhEnWwGB8WgTKTQMRKZ_Nytyvhi8RgIZupaMOPGU3RYltJkIt06_EXCfKzyjzAzTgrcJVdTrr4EoUlTvY1Q6KlqK9ClZiDBIm0VXZ0l7Tpcyb9krf4-I9AkVPzZ6ivcyclswjlTmD63gbmskfeFwwkZUSpRuYg4ti9DGTMGX28NwZtFpFDMNf44eAPFIx6FA438P2TMU4VdAyHU2zT2mSooumoynp0OER3pUMXCKeXpDOpouGe8LVKG6w4i1cIi3-_mFxn9i4zsqvVhtv4Ok21ftJhqmw29B4zES-HAWghN0SAdCaIVxc_Icq1o7Qrh2hUx2Bew_rB2i3vHUDeD3vlFMwVMH6ETre2im0Wu3NI_Dr8GqEkthsYFN3_Ri9OjbhuwywvlxcXCe3eFCjz0GKnFRxDqLyULL2S9ViGz6Tyl55-unHqw91VzR6_EJ3pfrEeIqOEeal88o_s1MBYVEJh60K92pI7K1MwJduZuGrCuevMjGIqsU_8iPzkQPOMlsUl4lcXFbGCyTFg558E6oJzEtoireDocMua4olMv1uGVHDqNsLrssQvpQzdCvurB4OXzSAbxIbsVnsq_etx0kBc___AcIzn9c';
					$theme_options['landingpage']	=  'eNrVXety3Day_p-qvAN2UqcqrhWl4WWukbWrtbXZVGXtxJZrz9aWi4XhYGYYkQSXF8laHz_QeY3zZKcbN4KcGUqyJUeKY2sGbDQaHxqN7gZA0bk3Hs4_lvPZfFBexFnEE14Mfijnk_ngu9WSut4Cv43ng4Re87rCL8F8cBUvGX50p_PBqk6SEAtClrCUZVU5-IHO3fnHeD6UVTeMLhlw_QQVgO864QuahAsaXawLXmdLpJ-gEKP5IE7pWrAezgdKDlEUL61C4FmwnNHKKgNJcl7GVcwzq9SFn7SqaLRByawH0Iky_o_dEjTe7r34T0oNDcZZxoqnJDSMaMIR-Dhd28ynTTmIGEZ1WfHUJvDnA56xMMenIBs0t5i7-MCDsWOAAgzeiqNgFEX9KLq2ommcXFwrMX7heR5npeaGoNW505B4UOGcbnhKD8iPwPESfpY0K50SpFopJpe0iKns_gShW9cJFf10AYaKfaicqoA6K17Y0kOvUTZH4wQyu4Go5UK_44w5GxavN5V65k00x4RVFSucMqdRnAnAoMJwF8bT5cybyXrAgSaJg6JKxRdM5WAGUhI19GvO1wmT2jQUklxcODbXJVvROqnMCJnnjtVzYPndcCiEQpW0iDb8khkBXCdsQYe-oALZN26IExA6ZUZt9lmDBmKdwpAkB-RvLLlkVRzdMGzAZaykveOQBcP9QxaM7j5k7tTzfb9_VBAq7-lB5Y96oBo-HFT-E4SqR6v8B9Sq4OlB5fVolf-AWjV6glANfx-oxk8PKrdHq7wHgsqT_ufTMlTjHps-fjCVKuvF7wfVqA8qwKjOc1ZEtGSf4-UFvZi5_V5er25F8ARcXL-vDdgXesSTh_KI92PliqhxzapcJ2TZvR6Ypg8zDaHuoq4qnhmYxo8Dpu4U7O_6_g5COypPEFZxhaVfs4ujz1xcuKY3dmq1oIVjB_Mw-oVWGE0QLtahUQmQf0mLi3XBrnWsJmn6cwzuEEDeVFU-Pzpa8GSZ0uyw2lwwohWLNilUqw4jnupHTkIztOhHV7mjbNWRoC41xZHgXFwerRJOK6DEfizWh7_l69ulMlwwrow7zffPz2mI-aLi2O7c0THtXCcLKBzBBk4ovdqIdAUMmddcIhEpjrDFb7n01VxcxJSUoN1cMFIJK8I4QskpMPwoaJN4UdBCqyWCF6_kiM3MQzFb6RUrecp0eL6iZEUdWhT8ylnyq6wJ22UVNCrQVKlj_ssIioj411nUi0XCtFroGp221VeUl1SxA8DWTf5A1ri4CtUwGxVLYJShhrMq6hhTFoCo5--AIIwSXrIvBlwiaANR5_cKgzfcgkGJ_Tk4GM2S_d-lWUZbFlXWms3dBKoXdAh3KKHLJv5CKqxuWa2RQFanWdgkXdE8h2Oj2_ZcXC0Yvch5nOnkrOtJI-fN9hE6TdLPose1p9zwq7Di-QIdC5X2A9FkSZ_1Qqksqk5nUUEKEOL74YHnBwf-6GB4OJs-04Cqij3zOegQdfh7ruFP4H9vNIKfh97omVY2VTlhq0pwkEYZQD2uExIltCyfI40wkTSqSJU6GzDeCRpwB2sNTo6T-OQ41sRSp_MNzxg8OoJHZVXwbH3yXCLBVCB5y4rLOGLl_PhIPSBD8r0b-M9IMBqTyXQ2OT5Cjkd1ctLBQSwbjZjTvWLuFopllyzhOXO4lIxo0c5SGlwnc2IkOqZkU7DV8wGWV3weZyv-Z_aBpnnCcA3Bv4OTbunxET1pRP8XIFXyCJZhB9ORJak4T6o4fw6LwuB9u2M9qhrsoNmjpcgOHYeuQsIoNxWEeTRU_QvqbPog66lu_j4XU_QjXCJokhVkwSvd1y9YWs3E9P2DYHYw8WBiTnHafOpg2DM3XYvskYbaCFFE87iiiQJkV9LZ3-_ujz4jO-ENvZm36nUchd5r7H6_8PsLo8l7zurcFTe0GMuoqNPFY8TtVqrXm7z4DAjH0zG4FjfGLAbCy5hdqbo0ScR6p1f-ZmZrmXyMfo3rLNGv0_22GHf7rDHa5R6MzfIt_hyOhs_MsmAEwC3mho9CB1YZJSldXoYVEka0okWkrdsLWrE1L67JaRFt4ktYkclWjYqudY1cMHcm1x1i9I8NMdjjSqy38SpmSy3mL1BakqacwGrDCtXW1KpO62rDC90cXDB4Kgq64vl627wNvKsm28g8tmHvhkAWyT6HDF0l_VeBDoMBKwVqfxJqDo9l23vvOtbVoKErFzH08QpW5hBgXDC64W0QcQ_k_-Cijp9pw6AqWqzaMUIg3WHVu4SveXWda5ENUPqRcu1cMLC_NFHJTD7sMWIjYaBS_mhMvze-30RcIpj-odefQJpIlMShBgpT4qNorKVyXCI0BhPh1EVcIr4_TMIExTjMZYfUOZiv1qIKVoW-pPRDuGvbFxSkBAW4uA71ARwZ0GHM3LItoSRTtVwnQ20-WrX3G5rZLsrbzy6hIXJEjSC3HNjGnNjgi9JPsgsdgNo9bXBSgpcM7PLGPu9cIkvCOMtlGI48z2Fik3_wYknONywjZ-iOHx5qKyRr4NTR-U25QPwthjnJr8gLmpF_MJyeOflcJ6__pFwnkBShrK5lFVBzuZhEbQITFnoBlB0v40sTF6bOZYTZBl-GhzAhC2IVxhnO0QFGfa0a6kiPRWrXwsnupMtW0YZCcFn-u6YFa5Wj7A6vK2zHfnAlsHcyTloy4rmVRQImbKDC0O8GRED2fDA4eXP267uzt-fk13evz89kyAl9PRm09RdTXuG-pCvSYfBawpJyoTMqGK-LkfLa6EmGjsj9QcRsPZFFTZDdzbrxRZzoHICQUf27zVxcTW6g3fhcJy_A0To-gg_Hm9HJH13P13mBIZSOOt3d6kZ1JReBiXtP3Vwwe4SrrkOT6u59EYkF05nthEG7P96u_oAGqKTd9J56lHBwBEEtHHD6796l0-US1vrSdIp4AflrvKo2pKwOyM-8JKfZmiWsPCDv3p7u6KKavdjTUKSJzMydwoAew2KY7evIdm7pCMlPznmSkL8CUGROXFxiqYw73FaUdptj1BNq5tnJj-CQEUp-rcEX6TYcwbyuHDmNVPP0RJs3NfFaibRcMIzVcX7iBUcTcpayAixKdA2GrmDHRzmg55101NszGS_tVrUyXr7k94b9u2YliJlpNqe5SAyhohouaFsVF5U77d8WsCwHCEjbK-d4pAWTWZZQrB_At9ooB9yfmCzUKgZzLU1QSot1nLVyBV0rtXf5nGzT3RQe-e4zXbW98va25Ad7ybvp1KFq0PVmsFaPDsZjaHXyTC1MCErJuPbZN-6Gp4xnidnGknz3JfsQu5StKVI56MwX4jSxck68rep78oBICIscEj-ybNNtTiv05Ezc2X7HGZqG6PgzN5c94xHawO3hgmGgJgtpVGHIgxneVsiDOx5CLH8f8Y5dD31GWSr2suA57o79DuP4hWmvnnSh-zCnAzyF163HZDdx_5jMrGr3FffflNYemrR2xfP72C5eTVezFTVar5VMWryS5bSglUHM7GG5jWEyi4k-ozB79GcUxl_9cB0CtkNXQvfxXY4wQeEekb2nXCey__REDp6eyKOnXCfy-OmJPHl6XCJPn57Is0cssrdnKRk-XpnF2SbOMU6MKhou-AcTxbSf3HSap8VDUbvShwPBxRELeZwEHpON93zwtl6UUREvGDnn5HVdkFfsqpRL-1wwCMQWQmiygM_F8h1ij0Cy_MP_oIOgAl9wUfIPUFwneD44rQh4U5wwXhIGYXcEjlwiTWvxJUafkfDlsk7T68NDrNDTiOt3GnFFI3GIe1wiJlGDBWITRX3fyuAwTCsNSF3qW4qiyTJEBKqiZshcMNt7PkjWu8gCRfb-X0ctCN_3Au9pV_BfaRRcXOUhel4kXj6HmG066K_q27o1axPtC__HW2Qd97zjoFrE7USHtQOF8bu4lquI7cbRQRQeJfjASunNLd6GnCbJ7rMy44c5eyoafqCTMvLHl0YVxlCNdeZHj0R3ckOtIAzCwIyYTd13ZFCoTId430DcW-h131wnigxOfqczPWk5zGPhGdDlXkyBwg_FH7M51apwg4bjhNiq8BWhvZsK7rvbPYKolprzL-3-9FwnPeXQ3gGsrQqPBCw8Jht_kMcubkZs6Po-bvIpxNqd6j_RZhQxv5bJaX0oBi3gC11K_u9_CS7PUV0ges41o8V7YhLtYuUGprgz-X5wYr6KpMJ7TKsfktMkIYJXSQqGaxRbHnYXGSOE2dvyh8ov6D96ORbJ2ji78Qjk6KH2q-PsTlZ9t7v2efmhW7zXAZ4tQEipC0mcxpWdEfFd-Vht5YTicLTaKAuzOl00e-q4hApOeD4p5UWz8wJ6_-bs9CX5--s3Z-q7ILSOXFzZG72BfNz4FMpd1PuWXvNcXHMQ7xNcMO10mitcItOGqt01PEKkjtz6AmPw2IQKQecoSq23rBV4uNptUylXqlQb9fJFH-hEUPSvOPggsUAFj-pexcIrhRJPDsY6T-rShCk2vqBvFEQtRQ7YU5cOWEYXCVuaXCIQSB6hUrpzar4gknJwlAq9kN9kY3hePS4NL3G-QLhMcs7Dx5fqI1pcIqmNiKw6PxazUj2r6FpVOKdr5P1JrmBbPUHW6Dy2NNsLdlACS5zBdvSwRQNcIrVodrUogbHJ3EmjCTfB6-6DZBu9XDBJ74hT71ww7R_XT8qXaPViN7TDDlUX1u7zLqTdVrbhxGsaOS-qFU9iHuYF_41FVbgE4jgp9e7XL7KYvLSKZ3Y9nLjmoEcgXCevNvkNlbA2BUugq0tbxIZCPWwmLdr7N7KQKCnKbca6mrYfM3H2vrEeqH7b1I01wlOhert-N2cUXRlS8xqSfKVxEjd-1LU_NB36sI-7RWT1zGtwfUXl1VwiHM9uhZ67W64r09j25aSee0sjfW9pATq9Mrce91xcXFyaqXtLjtL6fbe28LpS7GSwrOd0-XmXlOQMpZ1jtp_Q4t4GSVwwhcg7Kg-P4VBjCLF50Xvva6jxM5S7ARSXPxqi-wPPuxV40-Y88cOjZ64Q4pKaQmjv8MeNoX8rDK2V9mHxMzcPI5qwbElvqYFREud9U1hcXHcxY3LPEAa3VcOXdUG1e_ywMPoGxoRHN-lgYFC0Me9FskV5f0iObovkz-qc2Fec0CnNHVSeG4yiATOxROxfWZAztV8jdg9YKv-245nYAYFxT6Ytsr7AAmOrPaT3FF1giGy1XDABa8Lwaqu4kLsV58x6iKUH2No1yMEjqPZ6UOitSIrdXlPDwfKUZubGC6NpCFFbQVuKaOOhVXu2l9pynYD32Yec4SvqXCJG9LGEffXu4_Y79JWleXX90DfeqzhlD-ZE3QZbqPs2ftKoYkTRPQr8MAbZu5vWwhRSZ3DJT9mKfwHAD2yl74x4sIV4O8S4R7uNKFwiYraxAnAwb5_Q6-aUkaTYY6wsHlvmalwin6UMk2F6RETaydx9XDBb9wsrSp7RRIwkRu16LROJf2ntw16DOusQtnJj5jB8h2hLWtcQbOfpsJuYoyMvz85Pf_r5rebIioIXwTAIF_H6HrVsprVMXFxoKR1-4ytC9OqzxzqKu5rtQ--f9w6Mbp-tRCYUkbM3b16_0QNrCFO2jOvU0E5gVM43cUnwJbIkpddkQy8ZWTCWkRS0b0l4QZYsYRVbHpK_MFLWeJeEk2jDogtyzeuClDlLoP_rQy2taax9TQfnk3kkXlr7-9z3h-YdbP5R72KaF2W07n0eqsuSbqDvL2UwNUr9PlqshHdcIl7xagNQkBVCK29G5Fwnb3lRXFwfkEVdkUw9hwkO47hUwyj4EZA9LQ_JLwkDM0eq4prQNY0zAg7chuCsIMt4tWK4i0Iu2PUVL5blIZ7713OzjJdMX7_der2S_VxcjXLr-ax5LhYQaa9aV4gxtbeDJkRFyjuJOmFpjDzKa2xzG--gQKNkcgc2O6tpZZ-6mcFgi2IvsxapnCj2TWkLiivORT62iLYRs0RaLHJcXIa3SKyG2KVO47YoECbp2aOVKU3yOdBerMhCC5FUXDCg-papFF_L699JqX1zUNHvjAN3E1s7crgdV-8WXFzRcUji6OLWTP3bIYD1wLxmN7CVG-9qFl9Fjn1xytqlsgbdaVZRnan1OhR5wZd1VDkQPjh6Zpnl1BPtyGDJwcXW6aarA5tCPdzVqr-brs6qVhoZL1agXCJVBcgUcyuMw3BSLDi4v0rg5zu8fk9eWMR6OWxz2FRpXCKnf-dCYJ05eKNBmnRzNezbbzo0ux6LjV68JPNeXglrVypZri5i6ocb176JhseyT969enn2hrx4_ert-Zt3L85_ev0K7K0riAPDyLwvZFwwVhjWL2F8lZNBKkTjClZeGEsGlvdtBZ-rOmPLP1wwq1wwWen2m3trQReeG99F5z_IWtoS4l4X1FvsJO85Y9B73aDZU8ZZ0cYwKsEd5fIXJsx8qPrnOMU1gdRF8r1ArwT4xJm1Q3m4nuZxKYCDqn-Stwmev85Z9se3NMPExvBcMP7GBwF8CPDDGD6M8cMEPsDfePDsh2-_WfDl9cdvv8npEkGfkyGUyYtq8vNcJ_LtN4dKgw7IYaNOH-GBuF0gm56TwVwwW1wn2PrggPRdaOiw_agYiVOBZDzMP1wwgXU9AcpGUAY12vcS5sQVpLa48rMjD3HMv_0GmZF2IcFzhj-oNq90C0Os3eXvCUIxiHPynTf1l6MpFBAhvQVFqwOev90Bf9jiNPWm0Wz8w2061JIyw7go0fDtMj44KlqV54QuSjCjFRMtrYDBaPhf8BmUek6CEX50LtC2NbdH5kR8RNv63987QP6sKfinLMBaKf_P3eoQrFTeuR1-1xp3JFwnn1wwRzwhEwlTjOh1lCWQI7eMyxyC4TmRF-SdBaa5cSC0A56vxLnVZrGZ4cUf2-vzOkRcIoGo75V1qV2LukzqtaLJd9BgENsOpDURaW-3qxy3oW8JgIveVkUTs6pqWhLXTuE6to-JT7TP0cYDLODbxm31JrvoWhK1K7jdClqWprxL1gIGBVPsurCMd1RpYzPeqnvdvMmhqWngacrb4IxUaqqNDI7jORSTv4tgomylePeAg4Ni1bGTNG14xP4JlDpNNCNedYiEXCJqaeeABc8fsbzU8U6XtC3H0K7T6qSsYuQYKjlMqIRXR-NMZCaU4-njwr2Wb5PAMxbmCM5IvBZaXq-2bveKm-bWe4-mQ_1WmKiQ6zYUXjOVsph2uMs3Nu5qwsffH7KzhXFvC5NuC8J72N3AeBTsaiCYBbfsgmUKdrcwCXZ2YTQa729BvB1At9BSmz0wTXe3MeuDCV_tgFwn-k1erL2D06Szmh0cncKSOzioS3aeSh3S02fiLU9Kp8MC8_S37YemKi6F4tZ757yQ9RTdpbKiRdX5ZUkdEpa1eLj6hGWPiLOtDoTK-Ww8WOkSdjoGcbf8VU0qlbkQQM7Uq2LtMnG2qXnHGnru6jS3fO6a1_nIM6WmvAFXxfJl51dBtXxZ0xy-dAHfx7ZkKQ_1O_B53tB8-n8xFkF0';
				
					
					if ( !function_exists( 'tm_cs_decode_string' ) ) {
						function tm_cs_decode_string( $string ) {
							
							// decode the encrypted theme opitons
							$options = unserialize( gzuncompress( stripslashes( call_user_func( 'base'. '64' .'_decode', rtrim( strtr( $string, '-_', '+/' ), '=' ) ) ) ) );
							
							
							// Getting layout type
							$layout_type = 'default';
							if( isset($_POST['layout_type']) && !empty($_POST['layout_type']) ){
								$layout_type = strtolower($_POST['layout_type']);
								$layout_type = str_replace(' ','-',$layout_type);
								$layout_type = str_replace(' ','-',$layout_type);
								$layout_type = str_replace(' ','-',$layout_type);
								$layout_type = str_replace(' ','-',$layout_type);
							}
							
							foreach( $options as $key=>$val ){
								
								// changing image path with client website url so image will be fetched from client server directly
								$demo_domains = array(
									'http://boldman.themetechmount.com/boldman-data/',
									'http://boldman.themetechmount.com/',
									'http://boldman.themetechmount.com/boldman-overlay/',
									'http://boldman.themetechmount.com/boldman-infostack',
									'http://boldman.themetechmount.com/boldman-stackcenter/',
								);
								
								// getting current site URL
								$current_url = get_site_url() . '/';
								
								if( substr($val,0,7) == 'http://' ){
									$val = str_replace( $demo_domains, $current_url, $val );
									$options[$key] = $val;
								}
							
								
							}  // foreach
						
							return $options;
						}
					}
					
					
					
					// Update theme options according to selected layout
					if( !empty($theme_options[$layout_type]) ){
						$new_options = tm_cs_decode_string( $theme_options[$layout_type] );
						
						// Image path URL change is pending
						// we need to replace image path with correct path 
						
						update_option('boldman_theme_options', $new_options);
					}
					
					/**** END CodeStart theme options import ****/
					
					
					
					
					
					/**** START - Edit "Hello World" post and change *****/
					$hello_world_post = get_post(1);
					if( !empty($hello_world_post) ){
						$newDate = array(
							'ID'		=> '1',
							'post_date'	=> "2014-12-10 0:0:0" // [ Y-m-d H:i:s ]
						);
						
						wp_update_post($newDate);
					}
					/**** END - Edit "Hello World" post and change *****/
					
					
					
					
				
			        // Import custom configuration
					$content = file_get_contents( BOLDMAN_TMDC_DIR .'one-click-demo/'.$filename );
					
					if ( false !== strpos( $content, '<wp:theme_custom>' ) ) {
						preg_match('|<wp:theme_custom>(.*?)</wp:theme_custom>|is', $content, $config);
						if ($config && is_array($config) && count($config) > 1){
							$config = unserialize(base64_decode($config[1]));
							if (is_array($config)){
								$configs = array(
										'page_for_posts',
										'show_on_front',
										'page_on_front',
										'posts_per_page',
										'sidebars_widgets',
									);
								foreach ($configs as $item){
									if (isset($config[$item])){
										if( $item=='page_for_posts' || $item=='page_on_front' ){
											$page = get_page_by_title( $config[$item] );
											if( isset($page->ID) ){
												$config[$item] = $page->ID;
											}
										}
										update_option($item, $config[$item]);
									}
								}
								if (isset($config['sidebars_widgets'])){
									$sidebars = $config['sidebars_widgets'];
									update_option('sidebars_widgets', $sidebars);
									// read config
									$sidebars_config = array();
									if (isset($config['sidebars_config'])){
										$sidebars_config = $config['sidebars_config'];
										if (is_array($sidebars_config)){
											foreach ($sidebars_config as $name => $widget){
												update_option('widget_'.$name, $widget);
											}
										}
									}
								}
								
								if ( isset($config['menu_list']) && is_array($config['menu_list']) && count($config['menu_list'])>0 ){
									foreach( $config['menu_list'] as $location=>$menu_name ){
										$locations = get_theme_mod('nav_menu_locations'); // Get all menu Locations of current theme
										
										// Get menu name by id
										$term = get_term_by('name', $menu_name, 'nav_menu');
										$menu_id = $term->term_id;
										
										$locations[$location] = $menu_id;  //$foo is term_id of menu
										set_theme_mod('nav_menu_locations', $locations); // Set menu locations
									}
								}
								
							}
						}
					}
					
					
					// Overlay - change homepage slider
					if( !empty($layout_type) && $layout_type=='overlay' ){
						$show_on_front  = get_option( 'show_on_front' );
						$page_on_front  = get_option( 'page_on_front' );
						$page           = get_page( $page_on_front );
						$theme_options = get_option('boldman_theme_options');
						update_option('boldman_theme_options', $theme_options);
						if( $show_on_front == 'page' && !empty($page) ){
							$post_meta = get_post_meta( $page_on_front, '_themetechmount_metabox_group', true );
							$post_meta['revslider'] = 'home-mainoverlay-slider';
							update_post_meta( $page_on_front, '_themetechmount_metabox_group', $post_meta );
						}
					}
					
					
					
					
					// Infostack - Change Topbar right content and remove phone number area
					if( !empty($layout_type) && ($layout_type=='infostack' || $layout_type=='classic-infostack') ){
						$theme_options = get_option('boldman_theme_options');
						update_option('boldman_theme_options', $theme_options);
					}
					

					
					// Update term count in admin section
					tm_update_term_count();
					flush_rewrite_rules(); // flush rewrite rule
					
					$answer['answer'] = 'finished';
					$answer['reload'] = 'yes';
					die( json_encode( $answer ) );
					
				break;
				
			}
			die;
		}
		
		
		
		/**
		 * Fetch and save image
		 **/
		function grab_image($url,$saveto){
			$ch = curl_init ($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
			$raw=curl_exec($ch);
			curl_close ($ch);
			if(file_exists($saveto)){
				unlink($saveto);
			}
			$fp = fopen($saveto,'x');
			fwrite($fp, $raw);
			fclose($fp);
		}



	} // END class

} // END if



if( !function_exists('tm_update_term_count') ){
function tm_update_term_count(){
	$get_taxonomies = get_taxonomies();
	foreach( $get_taxonomies as $taxonomy=>$taxonomy2 ){
		$terms = get_terms( $taxonomy, 'hide_empty=0' );
		$terms_array = array();
		foreach( $terms as $term ){
			$terms_array[] = $term->term_id;
		}
		if( !empty($terms_array) && count($terms_array)>0 ){
			$output = wp_update_term_count_now( $terms_array, $taxonomy );
		}
	}
}
}




// For AJAX callback
$themetechmount_boldman_one_click_demo_setup = new themetechmount_boldman_one_click_demo_setup;