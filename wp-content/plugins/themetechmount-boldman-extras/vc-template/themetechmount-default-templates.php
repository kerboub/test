<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class TMTE_themetechmount_templates {

	protected $all_templates = false;

	/* Template Type */
	protected $templates_type = 'tmte_vc_templates';

	/* Template Categories */
	protected $template_categories = array();

	/* VC Templates List */
	protected $vc_template_list = array();

	protected $enable_tmte_vc_templates = true;

	/* TMTE_themetechmount_templates Class Init	 */
	public function __construct() {

		$this->enable_tmte_vc_templates = apply_filters( 'enable_tmte_vc_templates', true );

		// Return if vc templates is not enabled
		if ( ! $this->enable_tmte_vc_templates ) {
			return;
		}

		$this->vc_template_list = $this->get_vc_templates_list();

		$this->prepare_template_categories();

		add_filter( 'vc_templates_render_category', array( $this, 'tmte_vc_templates_render_category' ), 10 );
		add_filter( 'vc_templates_render_template', array( $this, 'tmte_vc_templates_render_template' ), 10, 2 );
		add_filter( 'vc_get_all_templates', array( $this, 'themetechmount_add_templates_tab' ) );

		add_action( 'vc_templates_render_backend_template', array( $this, 'tmte_vc_render_backend_template' ) );
		add_action( 'vc_templates_render_frontend_template', array( $this, 'tmte_vc_render_frontend_template' ) );

	}

	/* Prepare Template Categories */
	protected function prepare_template_categories() {
		$templates = $this->get_templates();

		$template_categories = array();

		foreach ( $templates as $template_data ) {
			if ( isset( $template_data['template_category'] ) ) {
				$template_categories[ $template_data['template_category_slug'] ] = $template_data['template_category'];
			}
		}

		asort( $template_categories );

		$this->template_categories = $template_categories;
	}

	/* Add Template Tab To Navigation */
	public function themetechmount_add_templates_tab( $data ) {
		$new_category = array(
			'category'        => $this->templates_type,
			'category_name'   => esc_html__( 'Boldman Templates', 'boldman' ),
			'category_weight' => 1,
			'templates'       => $this->get_all_templates(),
		);

		$data[] = $new_category;

		return $data;
	}

	/**
	 * Get VC templates
	 *
	 * @since 1.0.0
	 */
	public function get_templates() {

		$vc_templates_uri = TMTE_URI. '/vc-template/templates/';
		$vc_templates_dir = TMTE_DIR.  '/vc-template/templates/';	
			
		$image_fallback = untrailingslashit( $vc_templates_uri ) . '/images/default.jpg';

		$templates      = array();
		$templates_list = array();

		$templates_list_raw = $this->vc_template_list;

		if ( is_array( $templates_list_raw ) && ! empty( $templates_list_raw ) ) {
			$templates_list = $templates_list_raw;
		}

		foreach ( $templates_list as $template_name ) {
			$template_file = untrailingslashit( $vc_templates_dir . 'template-list/' ) . "/$template_name.php";

			if ( file_exists( $template_file ) ) {

				$template_data = include( $template_file );

				if ( $template_data && is_array( $template_data ) && ( isset( $template_data['name'] ) && $template_data['name'] ) && ( isset( $template_data['content'] ) && $template_data['content'] ) ) {

					if ( ! isset( $template_data['new'] ) ) {
						$template_data['new'] = false;
					}

					if ( ! isset( $template_data['disabled'] ) ) {
						$template_data['disabled'] = true;
					}

					if ( ! isset( $template_data['template_category'] ) || '' === $template_data['template_category'] ) {
						$template_data['template_category'] = esc_html__( 'Misc', 'boldman' );
					}

					$template_category_slug                  = sanitize_title( $template_data['template_category'] );
					$template_data['template_category_slug'] = $template_category_slug;

					$image_path = untrailingslashit( $vc_templates_dir ) . "/images/$template_category_slug/$template_name.jpg";
					$image_uri  = untrailingslashit( $vc_templates_uri ) . "/images/$template_category_slug/$template_name.jpg";

					if ( ! file_exists( $image_path ) ) {
						$template_data['image_path_original'] = preg_replace( '/\s/', '%20', $image_uri );
						$image_uri                            = $image_fallback;
					}

					$template_data['image_path'] = preg_replace( '/\s/', '%20', $image_uri );
					$templates[]                 = $template_data;
				}
			}
		}

		return $templates;
	}

	/* Get VC templates List */
	public function get_vc_templates_list() {

		$templates_list = array(
			
			//About
			'about-1',
			'about-2',
			'about-3',
			'about-4',
			'about-5',
			'about-6',
			'about-7',
			
			//contact us
			'contact-form-1',
			'contact-form-2',
			
			//Blog
			'blog-style-1',
			
			//Services
			'services-style-1',
			'services-style-2',
			
			//Testimonial
			'testimonial-style-1',
			'testimonial-style-2',
						
			'portfolio-style-1',
			'portfolio-style-2',

			//FAQ
			'faq-1',

			//process
			'process-style-1',
			
			//pricetable
			'pricetable-style-1',
			
		);

		$templates_list = apply_filters( 'vc_templates_list', $templates_list, $templates_list );

		return $templates_list;
	}

	/* Render Template Category */
	public function tmte_vc_templates_render_category( $category ) {

		if ( $this->templates_type === $category['category'] ) {

			$category['output']  = '<div class="vc_col-md-2 cs-vc-sorting-container">';
			$category['output'] .= $this->get_template_categories();
			$category['output'] .= '</div>';

			$category['output'] .= '
			<div class="vc_column vc_col-sm-12 cs-vc-templates-container">
				<div class="vc_ui-template-list vc_templates-list-default_templates vc_ui-list-bar" data-vc-action="collapseAll">';
			if ( ! empty( $category['templates'] ) ) {
				foreach ( $category['templates'] as $template ) {
					$category['output'] .= $this->render_template_list_item( $template );
				}
			}
			$category['output'] .= '
			</div>
		</div>';

		}

		return $category;
	}

	/* Get template Category */
	protected function get_template_categories() {

		$output = '';

		$categories = $this->template_categories;

		$output .= '<div class="sortable_templates">';
		$output .= '<ul>';
		$output .= '<li class="active" data-sort="all">' . esc_html__( 'All', 'boldman' ) . ' <span class="count">0</span></li>';

		if ( $categories && is_array( $categories ) && ! empty( $categories ) ) {
			foreach ( $categories as $key => $value ) {
				$output .= '<li data-sort="' . $key . '">' . $value . ' <span class="count">0</span></li>';
			}
		}

		$output .= '</ul>';
		$output .= '</div>';

		return $output;

	}

	/**
	 * Render Templates Output in Dialogbox
	 *
	 * @since  1.0.0
	 *
	 * @param  string $template_name template name.
	 * @param  array  $template_data template details.
	 * @return string
	 */
	function tmte_vc_templates_render_template( $template_name, $template_data ) {

		if ( $this->templates_type === $template_data['type'] ) {
			return $this->render_template_tmte_vc_templates( $template_name, $template_data );
		}

		return $template_name;
	}

	/* Render VC Template */
	public function render_template_tmte_vc_templates( $template_name, $template_data ) {
		ob_start();
		$template_name      = esc_html( $template_name );
		$add_template_title = esc_attr__( 'Add template', 'boldman' );

		echo <<<HTML
		<button type="button" class="vc_ui-list-bar-item-trigger" title="$add_template_title" data-template-handler="" data-vc-ui-element="template-title">$template_name</button>
		<div class="vc_ui-list-bar-item-actions">
			<button type="button" class="vc_general vc_ui-control-button" title="$add_template_title" data-template-handler="">
				<i class="vc-composer-icon"></i>
			</button>
		</div>
HTML;

		return ob_get_clean();
	}

	/**
	 * Render Frontend Template
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	function tmte_vc_render_frontend_template() {
		vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie()->part( 'templates' )->can()->validateDie();

		add_filter( 'vc_frontend_template_the_content', array( $this, 'frontend_do_templates_shortcodes' ) );

		$template_id   = vc_post_param( 'template_unique_id' );
		$template_type = vc_post_param( 'template_type' );

		add_action( 'wp_print_scripts', array( $this, 'add_frontend_templates_shortcodes_custom_css' ) );

		if ( '' === $template_id ) {
			die( 'Error: Vc_Templates_Panel_Editor::tmte_vc_render_frontend_template:1' );
		}

		WPBMap::addAllMappedShortcodes();
		if ( $this->templates_type === $template_type ) {
			$this->render_frontend_default_template();
		} else {
			echo apply_filters( 'vc_templates_render_frontend_template', $template_id, $template_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped			
		}
		die(); // no needs to do anything more. optimization.
	}

	/**
	 * Load frontend default template content by index
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function render_frontend_default_template() {
		$template_index = (int) vc_post_param( 'template_unique_id' );
		$data           = $this->get_default_template( $template_index );
		if ( ! $data ) {
			die( 'Error: Vc_Templates_Panel_Editor::render_frontend_default_template:1' );
		}
		vc_frontend_editor()->setTemplateContent( trim( $data['content'] ) );
		vc_frontend_editor()->enqueueRequired();
		vc_include_template(
			'editors/frontend_template.tpl.php',
			array(
				'editor' => vc_frontend_editor(),
			)
		);
		die();
	}

	/**
	 * Calls do_shortcode for templates.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function frontend_do_templates_shortcodes( $content ) {
		return do_shortcode( $content );
	}

	/**
	 * Add custom css from shortcodes from template for template editor.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function add_frontend_templates_shortcodes_custom_css() {
		$shortcodes_custom_css = '';
		$shortcodes_custom_css = visual_composer()->parseShortcodesCustomCss( vc_frontend_editor()->getTemplateContent() );

		if ( ! empty( $shortcodes_custom_css ) ) {
			global $tmte_vc_custom_styles;
			$shortcodes_custom_css = strip_tags( $shortcodes_custom_css );
			$unique_class          = substr( md5( mt_rand( 0, 9999 ) ), 0, 9 );

			$tmte_vc_custom_styles[ $unique_class ] = $shortcodes_custom_css;
		}
	}

	/**
	 * Render Backend Template
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function tmte_vc_render_backend_template() {

		$template_id   = vc_post_param( 'template_unique_id' );
		$template_type = vc_post_param( 'template_type' );

		if ( ! isset( $template_id, $template_type ) || '' === $template_id || '' === $template_type ) {
			die( 'Error: TMTE_themetechmount_templates::tmte_vc_render_backend_template:1' );
		}

		WPBMap::addAllMappedShortcodes();

		if ( $this->templates_type === $template_type ) {
			$this->get_backend_default_template();
			die();
		} else {
			echo apply_filters( 'vc_templates_render_backend_template', $template_id, $template_type ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			die();
		}

		die();
	}

	/**
	 * Function to get all templates for display
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function get_all_templates() {

		$data               = array();
		$templates          = $this->get_templates();
		$category_templates = array();

		foreach ( $templates as $template_id => $template_data ) {

			$category_templates[] = array(
				'unique_id'              => $template_id,
				'name'                   => $template_data['name'],
				'type'                   => $this->templates_type,
				'new'                    => isset( $template_data['new'] ) ? $template_data['new'] : false,
				'image'                  => isset( $template_data['image_path'] ) ? $template_data['image_path'] : false,
				'image_original'         => isset( $template_data['image_path_original'] ) ? $template_data['image_path_original'] : false,
				'custom_class'           => isset( $template_data['custom_class'] ) ? $template_data['custom_class'] : false,
				'template_category'      => isset( $template_data['template_category'] ) ? $template_data['template_category'] : false,
				'template_category_slug' => isset( $template_data['template_category_slug'] ) ? $template_data['template_category_slug'] : false,
				'template_cat_class'     => isset( $template_data['template_category_slug'] ) ? $template_data['template_category_slug'] : false,
				'content'                => isset( $template_data['content'] ) ? $template_data['content'] : false,
			);

			if ( ! empty( $category_templates ) ) {
				$data = $category_templates;
			}
		}

		return $data;
	}

	/**
	 * Load default templates list and initialize variable
	 *
	 * @since  1.0.0
	 */
	public function load_default_templates() {

		if ( ! is_array( $this->all_templates ) ) {
			$this->all_templates = $this->get_all_templates();
		}

		return $this->all_templates;
	}

	/**
	 * Get default template data by template index in array.
	 *
	 * @since  1.0.0
	 */
	public function get_default_template( $template_index ) {

		$this->load_default_templates();
		if ( ! is_numeric( $template_index ) || ! is_array( $this->all_templates ) || ! isset( $this->all_templates[ $template_index ] ) ) {
			return false;
		}

		return $this->all_templates[ $template_index ];
	}

	/**
	 * Load default template content by index from ajax
	 *
	 * @since  1.0.0
	 */
	public function get_backend_default_template( $return = false ) {

		$template_index = (int) vc_request_param( 'template_unique_id' );

		$data = $this->get_default_template( $template_index );

		if ( ! $data ) {
			die( 'Error: TMTE_themetechmount_templates::get_backend_default_template:1' );
		}
		if ( $return ) {
			return trim( $data['content'] );
		} else {
			echo trim( $data['content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			die();
		}
	}

	/**
	 * Render Template Item
	 *
	 * @since  1.0.0
	 *
	 * @param  array $template template.
	 * @return string
	 */
	public function render_template_list_item( $template ) {

		$template_id             = esc_attr( $template['unique_id'] );
		$template_id_hash        = md5( $template_id ); // needed for jquery target for TTA
		$name                    = isset( $template['name'] ) ? esc_attr__( $template['name'] ) : esc_html__( 'No title', 'boldman' );
		$template_name           = esc_attr__( $name );
		$template_name_lower     = esc_attr( vc_slugify( $template_name ) );
		$new                     = esc_attr( isset( $template['new'] ) ? $template['new'] : '' );
		$template_type           = esc_attr( isset( $template['type'] ) ? $template['type'] : 'custom' );
		$custom_class            = esc_attr( isset( $template['custom_class'] ) ? ' ' . $template['custom_class'] : '' );
		$template_image          = esc_attr( isset( $template['image'] ) ? $template['image'] : '' );
		$template_image_original = esc_attr( isset( $template['image_original'] ) ? $template['image_original'] : '' );
		$template_category       = esc_attr( isset( $template['template_category'] ) ? $template['template_category'] : '' );
		$template_cat_class      = esc_attr( isset( $template['template_cat_class'] ) ? $template['template_cat_class'] : '' );

		$output  = <<<HTML
					<div class="vc_ui-template vc_templates-template-type-default_templates {$template_cat_class}{$custom_class}"
						data-template_id="$template_id"
						data-template_id_hash="$template_id_hash"
						data-category="$template_type"
						data-template_unique_id="$template_id"
						data-template_name="$template_name_lower"
						data-template_type="$template_type"
						data-vc-content=".vc_ui-template-content">
						<div class="vc_ui-list-bar-item">
HTML;
		$output .= '<div class="cs-vc-template-preview">';
		if ( $new ) {
			$output .= '<span class="cs-vc-badge-new">New</span>';
		}
		$output .= '<img class="tmte-tmpimg" src="' . esc_url( $template_image ) . '" data-src-original="' . esc_url( $template_image_original ) . '" alt="' . esc_attr( $name ) . '" width="300" height="200" /></div>';
		$output .= apply_filters( 'vc_templates_render_template', $name, $template );
		$output .= <<<HTML
						</div>
			
					</div>
HTML;

		return $output;
	}

}

new TMTE_themetechmount_templates();
