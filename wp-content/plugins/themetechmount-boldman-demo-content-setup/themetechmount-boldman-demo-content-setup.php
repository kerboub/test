<?php
/*
 * Plugin Name: ThemetechMount Boldman Demo Content Setup
 * Plugin URI: https://www.themetechmount.com
 * Description: Boldman Demo Content Setup Plugin By ThemetechMount
 * Version: 1.0
 * Author: ThemetechMount
 * Author URI: https://www.themetechmount.com
 * Text Domain: boldman-demosetup
 * Domain Path: /languages
 */
 
 
 
/**
 *  Version and directory
 */
define( 'BOLDMAN_TMDC_VERSION', '1.0' );
define( 'BOLDMAN_TMDC_DIR', plugin_dir_path( __FILE__ ) );
define( 'BOLDMAN_TMDC_URI', plugins_url( '', __FILE__ ) );



/**
 *  Demo Content setup
 */
require_once BOLDMAN_TMDC_DIR . 'one-click-demo/demo-content.php';



/**
 *  Translation
 */
function boldman_demosetup_load_plugin_textdomain() {
	$domain = 'boldman-demo-content-setup';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	if ( $loaded = load_textdomain( 'boldman-demosetup', trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' ) ) {
		return $loaded;
	} else {
		load_plugin_textdomain( 'boldman-demosetup', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
}
add_action( 'init', 'boldman_demosetup_load_plugin_textdomain' );



/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function boldman_demosetup_load_textdomain() {
	load_plugin_textdomain( 'boldman-demosetup', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'boldman_demosetup_load_textdomain' );







function boldman_demo_content_scripts_styles(){

	wp_enqueue_style(
		'tm-one-click-demo-style',
		plugin_dir_url( __FILE__ ) . 'style.css',
		time(),
		true
	);
	wp_enqueue_script(
		'tm-one-click-demo-set-js',
		plugin_dir_url( __FILE__ ) . 'functions.js',
		array( 'jquery' ),
		time(),
		true
	);
	


}
add_action( 'admin_enqueue_scripts', 'boldman_demo_content_scripts_styles', 20 );



/**
 * HTML Output for the one click demo setup
 *
 * @since 1.0.0
 */
if( !function_exists('themetechmount_boldman_one_click_html') ){
function themetechmount_boldman_one_click_html() {
	?>
	
	<div id="import-demo-data-results">
				
		<div class="import-demo-data-text-w">
		
			<div class="import-demo-data-layout">
				<!-- <h3>Select demo data type  <small>(select below)</small>: </h3> -->
				
				<div class="tm-import-demo-left">
					<div class="tm-import-demo-left-inner">
						
						<select id="import-layout-type" name="import-layout-type">
							<option value="Classic">Classic Site</option>
							<option value="Overlay">Overlay Site</option>
							<option value="InfoStack">InfoStack Site</option>
							<option value="StackCenter">Stack Center Site</option>
							<option value="RTL">RTL Site</option>
							<option value="landingpage">Landing page</option>
						</select>
						
						<br><br><hr>
						
						<div class="import-demo-data-text">
						
							<strong><?php esc_attr_e('NOTE:', 'boldman'); ?></strong>
							<?php esc_attr_e('This process may overwrite your existing content or settings. So please do this on fresh WordPress setup only.', 'boldman'); ?>
							<br /><br />
							<?php esc_attr_e('Also if you already included demo data than this will add multiple menu links and you need to remove the repeated menu items by going to "Admin > Appearance > menus" section.', 'boldman'); ?>
							
						</div>

						
					</div>
				</div>
				
				<div class="tm-import-demo-right">
				
					<!-- Multi purpose -->
					<span class="import-demo-thumb-w import-demo-thumb-classic">
						<div class="tm-import-demo-preview-text">Preview:</div>
						<a href="http://boldman.themetechmount.com/" target="_blank">
							<img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/layout-classic.png" alt="Classic">
							<span class="tm-import-demo-link-text">View demo online</span>
						</a>
					</span>
					
					<!-- overlay -->
					<span class="import-demo-thumb-w import-demo-thumb-overlay" style="display:none;">
						<div class="tm-import-demo-preview-text">Preview:</div>
						<a href="http://boldman.themetechmount.com/boldman-overlay/" target="_blank">
							<img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/layout-overlay.png" alt="Overlay">
							<span class="tm-import-demo-link-text">View demo online</span>
						</a>
					</span>
					
					<!-- infostack -->
					<span class="import-demo-thumb-w import-demo-thumb-infostack" style="display:none;">
						<div class="tm-import-demo-preview-text">Preview:</div>
						<a href="http://boldman.themetechmount.com/boldman-infostack/" target="_blank">
							<img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/layout-infostack.png" alt="InfoStack">
							<span class="tm-import-demo-link-text">View demo online</span>
						</a>
					</span>
					
					<!-- StackCenter -->
					<span class="import-demo-thumb-w import-demo-thumb-stackcenter" style="display:none;">
						<div class="tm-import-demo-preview-text">Preview:</div>
						<a href="http://boldman.themetechmount.com/boldman-stackcenter/" target="_blank">
							<img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/layout-stackcenter.png" alt="StackCenter">
							<span class="tm-import-demo-link-text">View demo online</span>
						</a>
					</span>		

					<!-- RTL -->
					<span class="import-demo-thumb-w import-demo-thumb-rtl" style="display:none;">
						<div class="tm-import-demo-preview-text">Preview:</div>
						<a href="http://boldman.themetechmount.com/boldman-rtl/" target="_blank">
							<img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/layout-rtl.png" alt="RTL">
							<span class="tm-import-demo-link-text">View demo online</span>
						</a>
					</span>	

				<!-- Landingpage -->
					<span class="import-demo-thumb-w import-demo-thumb-landingpage" style="display:none;">
						<div class="tm-import-demo-preview-text">Preview:</div>
						<a href="http://boldman.themetechmount.com/boldman-landing/" target="_blank">
							<img src="<?php echo plugin_dir_url( __FILE__ ) ?>images/layout-landingpage.png" alt="Landingpage">
							<span class="tm-import-demo-link-text">View demo online</span>
						</a>
					</span>							
					
				</div>
				
				<div class="clear clr"></div>
				
			</div>
		
			
			<br /><br />
			<input type="button" class="button button-primary" id="themetechmount_one_click_demo_content" value="<?php esc_attr_e('I agree, continue demo content setup', 'boldman'); ?>" /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
			<a href="#" class="tm-one-click-error-close"><?php esc_attr_e('Cancel', 'boldman' ); ?></a>
		</div>
	
	</div>
	
	<div class="clear"></div>
	
	<?php
}
}