<?php
/**
 * Class for the Orchard Recovery Center Testimonial.
 *
 * @package ORC_Block_Options
 */

namespace ORCOptions\Includes\CPT;

defined( 'ABSPATH' ) || die;

use ORCOptions\Includes\Config;

/**
 * Class for the Orchard Recovery Center Testimonials.
 */
class OrcTestimonial {

	/**
	 * Class constructor
	 *
	 * Performs all the initialization for the class
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'save_post', array( $this, 'save_meta' ), 1, 2 );
		add_action( 'save_post', array( $this, 'save_inline' ) );
		add_filter( 'single_template', array( $this, 'load_template' ) );
		add_filter( 'archive_template', array( $this, 'load_archive' ) );
		add_filter( 'manage_orc_testimonial_posts_columns', array( $this, 'table_head' ) );
		add_action( 'manage_orc_testimonial_posts_custom_column', array( $this, 'table_content' ), 10, 2 );
		add_filter( 'manage_edit-orc_testimonial_sortable_columns', array( $this, 'sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'posts_orderby' ) );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_js' ) );
		add_shortcode( 'orc_testimonial', array( $this, 'orc_testimonial_shortcode' ) );
	} // __construct

	/**
	 * Register the custom post type for the class
	 */
	public function register_cpt() {

		$labels = array(
			'name'                  => __( 'Testimonials', 'orcoptions' ),
			'singular_name'         => __( 'Testimonial', 'orcoptions' ),
			'menu_name'             => __( 'Testimonials', 'orcoptions' ),
			'name_admin_bar'        => __( 'Testimonials', 'orcoptions' ),
			'add_new'               => __( 'Add New', 'orcoptions' ),
			'add_new_item'          => __( 'Add New Testimonial', 'orcoptions' ),
			'new_item'              => __( 'New Testimonial', 'orcoptions' ),
			'edit_item'             => __( 'Edit Testimonial', 'orcoptions' ),
			'view_item'             => __( 'View Testimonials', 'orcoptions' ),
			'all_items'             => __( 'All Testimonials', 'orcoptions' ),
			'search_items'          => __( 'Search Testimonials', 'orcoptions' ),
			'parent_item_colon'     => __( 'Parent Testimonial:', 'orcoptions' ),
			'not_found'             => __( 'No Testimonials found.', 'orcoptions' ),
			'not_found_in_trash'    => __( 'No Testimonials found in Trash.', 'orcoptions' ),
			'featured_image'        => __( 'Testimonial Image', 'orcoptions' ),
			'set_featured_image'    => __( 'Set Testimonial image', 'orcoptions' ),
			'remove_featured_image' => __( 'Remove Testimonial image', 'orcoptions' ),
			'use_featured_image'    => __( 'Use as Testimonial image', 'orcoptions' ),
			'archives'              => __( 'Testimonial archives', 'orcoptions' ),
			'insert_into_item'      => __( 'Insert into Testimonial', 'orcoptions' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Testimonial', 'orcoptions' ),
			'filter_items_list'     => __( 'Filter Testimonial list', 'orcoptions' ),
			'items_list_navigation' => __( 'Testimonials list navigation', 'orcoptions' ),
			'items_list'            => __( 'Testimonials list', 'orcoptions' ),
		);

		$args = array(
			'labels'               => $labels,
			'public'               => true,
			'publicly_queryable'   => true,
			'show_ui'              => true,
			'show_in_menu'         => Config::MENU_SLUG,
			'show_in_rest'         => true,
			'query_var'            => false,
			'rewrite'              => array(
				'slug'       => 'testimonials',
				'with_front' => true,
			),
			'capability_type'      => 'post',
			'has_archive'          => true,
			'hierarchical'         => false,
			'menu_position'        => 20,
			'menu_icon'            => 'dashicons-testimonial',
			'supports'             => array( 'title', 'editor', 'excerpt' ),
			'register_meta_box_cb' => array( $this, 'register_meta_box' ),
		);

		register_post_type( 'orc_testimonial', $args );

	} // register_cpt

	/**
	 * Add the meta box to the custom post type
	 */
	public function register_meta_box() {

		add_meta_box( 'orc_testimonial_data', 'Testimonial Information', array( $this, 'meta_box' ), 'orc_testimonial', 'side', 'high' );

	} // register_meta_box

	/**
	 * Display the meta box
	 *
	 * @global type $post - The current post
	 */
	public function meta_box() {

		global $post;

		// Nonce field to validate form request from current site.
		wp_nonce_field( basename( __FILE__ ), 'orc_testimonial_data' );

		// Get the testimonial information if it's already entered.
		$city          = sanitize_text_field( get_post_meta( $post->ID, 'city', true ) );
		$province      = sanitize_text_field( get_post_meta( $post->ID, 'province', true ) );
		$display_order = sanitize_text_field( get_post_meta( $post->ID, 'display_order', true ) );
		$on_home_page  = sanitize_text_field( get_post_meta( $post->ID, 'on_home_page', true ) );

		$display_order = '' === $display_order ? '0' : $display_order;
		$checked       = '1' === $christmas ? 'checked' : '';

		// Output the fields.
		?>
		<label for="city">City: </label>
		<input type="text" id="city" name="city" required value="<?php echo esc_html( $city ); ?>" class="widefat">
		<label for="province">Province: </label>
		<input type="text" id="province" name="province" value="<?php echo esc_html( $province ); ?>" class="widefat">
		<label for="display_order">Display Order: </label>
		<input type="number" id="display_order" name="display_order" min="0" required value="<?php echo esc_html( $display_order ); ?>" class="widefat" minimum="0">
		<label for="christmas">Christmas Testimonial: </label>
		<input type="checkbox" id="christmas" name="christmas" value="1" <?php echo '1' === $christmas ? 'checked' : ''; ?>>
		<?php
	} // meta_box

	/**
	 * Save the meta box data
	 *
	 * @param int   $post_id - The post ID.
	 * @param array $post - The post.
	 *
	 * @return int - The post ID
	 */
	public function save_meta( $post_id, $post ) {

		// Checks save status.
		$is_autosave    = wp_is_post_autosave( $post_id );
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST['orc_testimonial_data'] ) && wp_verify_nonce( $_POST['orc_testimonial_data'], basename( __FILE__ ) ) ) ? true : false;
		$can_edit       = current_user_can( 'edit_post', $post_id );

		// Exits script depending on save status.
		if ( $is_autosave || $is_revision || ! $is_valid_nonce || ! $can_edit ) {
			return;
		}

		// Now that we're authenticated, time to save the data.
		// This sanitizes the data from the field and saves it into an array $events_meta.
		$testimonial_meta                  = array();
		$testimonial_meta['city']          = isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';
		$testimonial_meta['province']      = isset( $_POST['province'] ) ? sanitize_text_field( $_POST['province'] ) : '';
		$testimonial_meta['display_order'] = isset( $_POST['display_order'] ) ? sanitize_text_field( $_POST['display_order'] ) : '0';
		$testimonial_meta['christmas']     = isset( $_POST['christmas'] ) ? sanitize_text_field( $_POST['christmas'] ) : '0';

		if ( '' === $testimonial_meta['display_order'] ) {
			$testimonial_meta['display_order'] = '0';
		}

		if ( '' === $testimonial_meta['christmas'] ) {
			$testimonial_meta['christmas'] = '0';
		}

		// Cycle through the $events_meta array.
		foreach ( $testimonial_meta as $key => $value ) {
			// Don't store custom data twice.
			if ( get_post_meta( $post_id, $key, false ) ) {
				// If the custom field already has a value, update it.
				update_post_meta( $post_id, $key, $value );
			} else {
				// If the custom field doesn't have a value, add it.
				add_post_meta( $post_id, $key, $value );
			}

			if ( '' === $value ) {
				// Delete the meta key if there's no value.
				delete_post_meta( $post_id, $key );
			}
		}

	} // save_meta

	/**
	 * Save the quick edit data.
	 *
	 * @param int $post_id The ID of the post.
	 */
	public function save_inline( $post_id ) {
		// Check inline edit nonce.
		if ( ! wp_verify_nonce( $_POST['_inline_edit'], 'inlineeditnonce' ) ) {
			return;
		}

		// update the city.
		$city = ! empty( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';
		update_post_meta( $post_id, 'city', $city );

		// update the province.
		$province = ! empty( $_POST['province'] ) ? sanitize_text_field( $_POST['province'] ) : '';
		update_post_meta( $post_id, 'province', $province );

		// update the display_order.
		$display_order = ! empty( $_POST['display_order'] ) ? sanitize_text_field( $_POST['display_order'] ) : '';
		update_post_meta( $post_id, 'display_order', $display_order );

		// update checkbox.
		$christmas = ( isset( $_POST['christmas'] ) && '1' === $_POST['christmas'] ) ? '1' : '0';
		update_post_meta( $post_id, 'christmas', $christmas );

	} // save_inline

	/**
	 * Load the single post template with the following order:
	 * - Theme single post template (THEME/plugins/orc_options/templates/single-testimonial.php)
	 * - Plugin single post template (PLUGIN/templates/single-testimonial.php)
	 * - Default template
	 *
	 * @param string $template - Default template.
	 *
	 * @global array $post - The post
	 *
	 * @return string Template to use
	 */
	public function load_template( $template ) {

		global $post;

		// Check if this is a testimonial.
		if ( 'orc_testimonial' === $post->post_type ) {

			// Plugin/Theme path.
			$plugin_path = plugin_dir_path( __FILE__ ) . '../../templates/';
			$theme_path  = get_stylesheet_directory() . '/plugins/orc_options/templates/';

			// The name of custom post type single template.
			$template_name = 'single-testimonial.php';

			$pluginfile = $plugin_path . $template_name;
			$themefile  = $theme_path . $template_name;

			// Check for templates.
			if ( ! file_exists( $themefile ) ) {
				if ( ! file_exists( $pluginfile ) ) {
					// No theme or plugin template.
					return $template;
				}

				// Have a plugin template.
				return $pluginfile;
			}

			// Have a theme template.
			return $themefile;
		}

		// This is not a testimonial, do nothing with $template.
		return $template;

	} // load_template

	/**
	 * Load the archive page.
	 *
	 * @param string $template The template to use.
	 */
	public function load_archive( $template ) {

		global $post;

		// Check if this is a testimonial.
		if ( 'orc_testimonial' === $post->post_type ) {
			// Plugin/Theme path.
			$plugin_path = plugin_dir_path( __FILE__ ) . '../../templates/';
			$theme_path  = get_stylesheet_directory() . '/plugins/orc_options/templates/';

			// The name of custom post type single template.
			$template_name = 'archive-testimonial.php';

			$pluginfile = $plugin_path . $template_name;
			$themefile  = $theme_path . $template_name;

			// Check for templates.
			if ( ! file_exists( $themefile ) ) {
				if ( ! file_exists( $pluginfile ) ) {
					// No theme or plugin template.
					return $template;
				}

				// Have a plugin template.
				return $pluginfile;
			}

			// Have a theme template.
			return $themefile;
		}

		// This is not a testimonial, do nothing with $template.
		return $template;

	} // load_template

	/**
	 * Display the table headers for custom columns in our order.
	 *
	 * @param array $columns - Array of headers.
	 *
	 * @return array - Modified array of headers.
	 */
	public function table_head( $columns ) {

		$newcols = array();

		// Want the selection box and title (name for our custom post type) first.
		$newcols['cb'] = $columns['cb'];
		unset( $columns['cb'] );
		$newcols['title'] = 'Name';
		unset( $columns['title'] );

		// Our custom meta data columns.
		$newcols['city']          = 'City';
		$newcols['province']      = 'Province';
		$newcols['display_order'] = 'Display Order';
		$newcols['christmas']     = 'Christmas Testimonial?';

		// Want date last.
		unset( $columns['date'] );

		// Add all other selected columns.
		foreach ( $columns as $col => $title ) {
			$newcols[ $col ] = $title;
		}

		// Add the date back.
		$newcols['date'] = 'Date';

		return $newcols;

	} // table_head.

	/**
	 * Display the meta data associated with a post on the administration table.
	 *
	 * @param string $column_name - The header of the column.
	 * @param int    $post_id - The ID of the post being displayed.
	 */
	public function table_content( $column_name, $post_id ) {

		if ( 'city' === $column_name ) {
			$city = get_post_meta( $post_id, 'city', true );
			echo esc_html( $city );
		}
		if ( 'province' === $column_name ) {
			$province = get_post_meta( $post_id, 'province', true );
			echo esc_html( $province );
		}
		if ( 'display_order' === $column_name ) {
			$display_order = get_post_meta( $post_id, 'display_order', true );
			if ( '' === $display_order ) {
				$display_order = '0';
			}
			echo esc_html( $display_order );
		}
		if ( 'christmas' === $column_name ) {
			$christmas = intval( get_post_meta( $post_id, 'christmas', true ) );
			$christmas = 1 === $christmas ? 'YES' : 'no';
			echo esc_html( $christmas );
		}

	} // table_content

	/**
	 * Make columns in admin page sortable.
	 *
	 * @param array $columns The columns array.
	 */
	public function sortable_columns( $columns ) {
		$columns['city']          = 'city';
		$columns['province']      = 'province';
		$columns['display_order'] = 'display_order';
		$columns['christmas']     = 'christmas';
		return $columns;
	}

	/**
	 * Sort the posts by custom fields order.
	 *
	 * @param object $query The database query.
	 */
	public function posts_orderby( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		/*
		 * If ordering by city.
		 * Sort first by city, then by display order ascending.
		 */
		if ( 'city' === $query->get( 'orderby' ) ) {
			$order_direction = $query->get( 'order' );
			$query->set(
				'meta_query',
				array(
					'city_clause'        => array(
						'key'  => 'city',
					),
					'testimonial_clause' => array(
						'key'  => 'display_order',
						'type' => 'numeric',
					),
				)
			);
			$query->set(
				'orderby',
				array(
					'city_clause'        => $order_direction,
					'testimonial_clause' => 'ASC',
					'title'              => 'ASC',
				)
			);
		}

		/*
		 * If ordering by province.
		 * Sort first by province, then by display order ascending.
		 */
		if ( 'province' === $query->get( 'orderby' ) ) {
			$order_direction = $query->get( 'order' );
			$query->set(
				'meta_query',
				array(
					'province_clause'    => array(
						'key'  => 'province',
					),
					'testimonial_clause' => array(
						'key'  => 'display_order',
						'type' => 'numeric',
					),
				)
			);
			$query->set(
				'orderby',
				array(
					'province_clause'    => $order_direction,
					'testimonial_clause' => 'ASC',
					'title'              => 'ASC',
				)
			);
		}

		if ( 'display_order' === $query->get( 'orderby' ) ) {
			$order_direction = $query->get( 'order' );
			$query->set(
				'meta_query',
				array(
					'testimonial_clause' => array(
						'key'  => 'display_order',
						'type' => 'numeric',
					),
				)
			);
			$query->set(
				'orderby',
				array(
					'testimonial_clause' => $order_direction,
					'title'              => 'ASC',
				)
			);
		}

		/*
		 * If ordering by christmas testimonial.
		 * Sort first by christmas testimonial, then by display order ascending.
		 */
		if ( 'christmas' === $query->get( 'orderby' ) ) {
			$order_direction = $query->get( 'order' );
			$query->set(
				'meta_query',
				array(
					'christmas_clause'   => array(
						'key'  => 'christmas',
						'type' => 'numeric',
					),
					'testimonial_clause' => array(
						'key'  => 'display_order',
						'type' => 'numeric',
					),
				)
			);
			$query->set(
				'orderby',
				array(
					'christmas_clause'   => $order_direction,
					'testimonial_clause' => 'ASC',
					'title'              => 'ASC',
				)
			);
		}
	}

	/**
	 * Adds custom variables to the quick edit box.
	 *
	 * @param string $column_name The name of the column to add.
	 * @param string $post_type   The type of the post.
	 */
	public function quick_edit( $column_name, $post_type ) {

		// Are we on the Testimonials page.
		if ( 'orc_testimonial' !== $post_type ) {
			return;
		}

		switch ( $column_name ) {
			case 'city':
				?>
				<div style="clear:both;">Custom Fields</div>
				<hr style="border: 1px solid #eee;">
				<fieldset class="inline-edit-col-left" style="clear:both;">
					<div class="inline-edit-col">
						<label>
							<span class="title">City</span>
							<span class="input-text-wrap">
								<input type="text" name="city">
							</span>
						</label>
					</div>
				<?php
				break;
			case 'province':
				?>
					<div class="inline-edit-col">
						<label>
							<span class="title">Province</span>
							<span class="input-text-wrap">
								<input type="text" name="province">
							</span>
						</label>
					</div>
				<?php
				break;
			case 'display_order':
				?>
					<div class="inline-edit-col">
						<label>
							<span>Display Order</span>
							<input type="number" min="0" name="display_order">
						</label>
					</div>
				<?php
				break;
			case 'christmas':
				?>
					<div class="inline-edit-col">
						<label>
							<input type="checkbox" value="1" name="christmas"> Christmas Testimonial?
						</label>
					</div>
				</fieldset>
				<?php
				break;
		}
	}

	/**
	 * Add javascript for the quick editing.
	 *
	 * @param string $page The page executing.
	 */
	public function add_js( $page ) {
		$post_type = get_post_type();
		if ( 'edit.php' !== $page || 'orc_testimonial' !== $post_type ) {
			return;
		}

		$full_path = plugins_url() . '/orc-options/dist/js/orc.testimonial-admin.min.js';
		$siteurl   = get_option( 'siteurl' );
		$script    = str_replace( $siteurl, '', $full_path );
		wp_enqueue_script( 'custom-quickedit-box', $script, array( 'jquery', 'inline-edit-post' ), Config::getVersion(), true );
	}

	/**
	 * Shortcode to display the testimonial.
	 *
	 * @param array  $atts     Attributes for the shortcode.
	 * @param string $content The content for the shortcode.
	 *
	 * @return string HTML content result for the shortcode.
	 */
	public function orc_testimonial_shortcode( $atts, $content = null ) {

		$postid    = isset( $atts['postid'] ) ? $atts['postid'] : null;
		$christmas = isset( $atts['christmas'] ) ? $atts['christmas'] : null;

		if ( $christmas ) {
			$args = array(
				'post_type'      => 'orc_testimonial',
				'posts_per_page' => -1,
				'meta_query'     => array(
					'display_order_clause' => array(
						'key'  => 'display_order',
						'type' => 'numeric',
					),
					'christmas_clause'     => array(
						'key'        => 'christmas',
						'meta_value' => '1',
					),
				),
				'orderby'        => array(
					'christmas_clause'     => 'ASC',
					'display_order_clause' => 'ASC',
					'title'                => 'ASC',
				),

			);
		} else {
			$args = array(
				'post_type'      => 'orc_testimonial',
				'orderby'        => array(
					'display_order_clause' => 'ASC',
					'title'                => 'ASC',
				),
				'posts_per_page' => -1,
				'meta_query'     => array(
					'display_order_clause' => array(
						'key'  => 'display_order',
						'type' => 'numeric',
					),
				),
			);
		}

		$the_query = new \WP_Query( $args );
		$data      = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$id         = get_the_ID();
				$fields     = get_post_custom( $id );
				$christmas = strlen( $fields['christmas'][0] ) > 0 ? true : false;
				$city      = array_key_exists( 'city', $fields ) ? $fields['city'][0] : '';
				$province  = array_key_exists( 'province', $fields ) ? $fields['province'][0] : '';
				$data[]    = array(
					'id'        => $id,
					'name'      => get_the_title(),
					'christmas' => $christmas,
					'city'      => $city,
					'province'  => $province,
					'permalink' => get_the_permalink(),
				);
			}
		}
		wp_reset_postdata();

		if ( 0 === count( $data ) ) {
			return '<div class="testimonials">No Testimonials Found</div>';
		}

		$retstr = '<div class="testimonials" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1em">';
		foreach ( $data as $testimonial ) {
			$retstr .= "<div class='testimonial' style='display: flex; flex-wrap: wrap; flex-direction: column; align-items: center; text-align: center'><a href=\"{$testimonial['permalink']}\">{$testimonial['name']}</a>";
			$retstr .= "   <div class='city'>{$testimonial['city']}</div>";
			$retstr .= "   <div class='province'>{$testimonial['province']}</div>";
			$retstr .= "   <div class='christmas'>{$testimonial['christmas']}</div>";
			$retstr .= '</div> <!-- /.testimonial -->';
		}
		$retstr .= '</div> <!-- /.testimonials -->';

		return $retstr;
	}

} // OrcTestimonial

new OrcTestimonial();
