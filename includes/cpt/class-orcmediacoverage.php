<?php
/**
 * Class for the Orchard Recovery Center Media Coverage.
 *
 * @package ORC_Block_Options
 */

namespace ORCOptions\Includes\CPT;

defined( 'ABSPATH' ) || die;

use ORCOptions\Includes\Config;

/**
 * Class for the Orchard Recovery Center Media Coverage.
 */
class OrcMediaCoverage {

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
		add_filter( 'manage_orc_media_coverage_posts_columns', array( $this, 'table_head' ) );
		add_action( 'manage_orc_media_coverage_posts_custom_column', array( $this, 'table_content' ), 10, 2 );
		add_filter( 'manage_edit-orc_media_coverage_sortable_columns', array( $this, 'sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'posts_orderby' ) );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_js' ) );
		add_shortcode( 'orc_media_coverage', array( $this, 'orc_media_coverage_shortcode' ) );
	} // __construct

	/**
	 * Register the custom post type for the class
	 */
	public function register_cpt() {

		$labels = array(
			'name'                  => __( 'Media Coverage', 'orcoptions' ),
			'singular_name'         => __( 'Media Coverage', 'orcoptions' ),
			'menu_name'             => __( 'Media Coverage', 'orcoptions' ),
			'name_admin_bar'        => __( 'Media Coverage', 'orcoptions' ),
			'add_new'               => __( 'Add New', 'orcoptions' ),
			'add_new_item'          => __( 'Add New Media Coverage', 'orcoptions' ),
			'new_item'              => __( 'New Media Coverage', 'orcoptions' ),
			'edit_item'             => __( 'Edit Media Coverage', 'orcoptions' ),
			'view_item'             => __( 'View Media Coverage', 'orcoptions' ),
			'all_items'             => __( 'All Media Coverage', 'orcoptions' ),
			'search_items'          => __( 'Search Media Coverage', 'orcoptions' ),
			'parent_item_colon'     => __( 'Parent Media Coverage:', 'orcoptions' ),
			'not_found'             => __( 'No Media Coverage found.', 'orcoptions' ),
			'not_found_in_trash'    => __( 'No Media Coverage found in Trash.', 'orcoptions' ),
			'featured_image'        => __( 'Media Coverage Image', 'orcoptions' ),
			'set_featured_image'    => __( 'Set Media Coverage image', 'orcoptions' ),
			'remove_featured_image' => __( 'Remove Media Coverage image', 'orcoptions' ),
			'use_featured_image'    => __( 'Use as Media Coverage image', 'orcoptions' ),
			'archives'              => __( 'Media Coverage archives', 'orcoptions' ),
			'insert_into_item'      => __( 'Insert into Media Coverage', 'orcoptions' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Media Coverage', 'orcoptions' ),
			'filter_items_list'     => __( 'Filter Media Coverage list', 'orcoptions' ),
			'items_list_navigation' => __( 'Media Coverage list navigation', 'orcoptions' ),
			'items_list'            => __( 'Media Coverage list', 'orcoptions' ),
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
				'slug'       => 'media-coverage',
				'with_front' => true,
			),
			'capability_type'      => 'post',
			'has_archive'          => true,
			'hierarchical'         => false,
			'menu_position'        => 20,
			'menu_icon'            => 'dashicons-video',
			'supports'             => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'register_meta_box_cb' => array( $this, 'register_meta_box' ),
		);

		register_post_type( 'orc_media_coverage', $args );

	} // register_cpt

	/**
	 * Add the meta box to the custom post type
	 */
	public function register_meta_box() {

		add_meta_box( 'orc_media_coverage_data', 'Media Coverage Information', array( $this, 'meta_box' ), 'orc_media_coverage', 'side', 'high' );

	} // register_meta_box

	/**
	 * Display the meta box
	 *
	 * @global type $post - The current post
	 */
	public function meta_box() {

		global $post;

		// Nonce field to validate form request from current site.
		wp_nonce_field( basename( __FILE__ ), 'orc_media_coverage_data' );

		// Get the media coverage information if it's already entered.
		$featured_on = sanitize_text_field( get_post_meta( $post->ID, 'featured_on', true ) );
		$featured_in = sanitize_text_field( get_post_meta( $post->ID, 'featured_in', true ) );

		// Output the fields.
		?>
		<label for="featured_on">Featured On: </label>
		<input type="text" id="featured_on" name="featured_on" min="0" required value="<?php echo esc_html( $featured_on ); ?>" class="widefat" minimum="0">
		<label for="featured_in">Featured In: </label>
		<input type="text" id="featured_in" name="featured_in" min="0" required value="<?php echo esc_html( $featured_in ); ?>" class="widefat" minimum="0">
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
		$is_valid_nonce = ( isset( $_POST['orc_media_coverage_data'] ) && wp_verify_nonce( $_POST['orc_media_coverage_data'], basename( __FILE__ ) ) ) ? true : false;
		$can_edit       = current_user_can( 'edit_post', $post_id );

		// Exits script depending on save status.
		if ( $is_autosave || $is_revision || ! $is_valid_nonce || ! $can_edit ) {
			return;
		}

		// Now that we're authenticated, time to save the data.
		// This sanitizes the data from the field and saves it into an array $events_meta.
		$media_coverage_meta                = array();
		$media_coverage_meta['featured_on'] = isset( $_POST['featured_on'] ) ? sanitize_text_field( $_POST['featured_on'] ) : '0';
		$media_coverage_meta['featured_in'] = isset( $_POST['featured_in'] ) ? sanitize_text_field( $_POST['featured_in'] ) : '0';

		// Cycle through the $events_meta array.
		foreach ( $media_coverage_meta as $key => $value ) {
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

		// update featured on.
		$featured_on = ! empty( $_POST['featured_on'] ) ? sanitize_text_field( $_POST['featured_on'] ) : '';
		update_post_meta( $post_id, 'featured_on', $featured_on );

		// update featured in.
		$featured_in = ! empty( $_POST['featured_in'] ) ? sanitize_text_field( $_POST['featured_in'] ) : '';
		update_post_meta( $post_id, 'featured_in', $featured_in );
	} // save_inline

	/**
	 * Load the single post template with the following order:
	 * - Theme single post template (THEME/plugins/orc_options/templates/single-media-coverage.php)
	 * - Plugin single post template (PLUGIN/templates/single-media-coverage.php)
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

		// Check if this is a media coverage.
		if ( 'orc_media_coverage' === $post->post_type ) {

			// Plugin/Theme path.
			$plugin_path = plugin_dir_path( __FILE__ ) . '../../templates/';
			$theme_path  = get_stylesheet_directory() . '/plugins/orc_options/templates/';

			// The name of custom post type single template.
			$template_name = 'single-media-coverage.php';

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

		// This is not a media coverage, do nothing with $template.
		return $template;

	} // load_template

	/**
	 * Load the archive page.
	 *
	 * @param string $template The template to use.
	 */
	public function load_archive( $template ) {

		global $post;

		// Check if this is a media coverage.
		if ( 'orc_media_coverage' === $post->post_type ) {
			// Plugin/Theme path.
			$plugin_path = plugin_dir_path( __FILE__ ) . '../../templates/';
			$theme_path  = get_stylesheet_directory() . '/plugins/orc_options/templates/';

			// The name of custom post type single template.
			$template_name = 'archive-media-coverage.php';

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

		// This is not a media coverage, do nothing with $template.
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
		$newcols['featured_on'] = 'Featured On';
		$newcols['featured_in'] = 'Featured In';

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

		if ( 'featured_on' === $column_name ) {
			$featured_on = get_post_meta( $post_id, 'featured_on', true );
			echo esc_html( $featured_on );
		}

		if ( 'featured_in' === $column_name ) {
			$featured_in = get_post_meta( $post_id, 'featured_in', true );
			echo esc_html( $featured_in );
		}

	} // table_content

	/**
	 * Make columns in admin page sortable.
	 *
	 * @param array $columns The columns array.
	 */
	public function sortable_columns( $columns ) {
		$columns['featured_on'] = 'featured_on';
		$columns['featured_in'] = 'featured_in';
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

		if ( 'featured_in' === $query->get( 'orderby' ) ) {
			$order_direction = $query->get( 'order' );
			$query->set(
				'meta_query',
				array(
					'media_clause' => array(
						'key' => 'featured_in',
					),
				)
			);
			$query->set(
				'orderby',
				array(
					'media_clause' => $order_direction,
					'title'        => 'ASC',
				)
			);
		}

		if ( 'featured_on' === $query->get( 'orderby' ) ) {
			$order_direction = $query->get( 'order' );
			$query->set(
				'meta_query',
				array(
					'media_clause' => array(
						'key' => 'featured_on',
					),
				)
			);
			$query->set(
				'orderby',
				array(
					'media_clause' => $order_direction,
					'title'        => 'ASC',
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

		// Are we on the Media Coverage page.
		if ( 'orc_media_coverage' !== $post_type ) {
			return;
		}

		switch ( $column_name ) {
			case 'featured_on':
				?>
				<div style="clear:both;">Custom Fields</div>
				<hr style="border: 1px solid #eee;">
				<fieldset class="inline-edit-col-left">
					<div class="inline-edit-col">
						<label>
							<span>Featured On</span>
							<input type="text" name="featured_on">
						</label>
					</div>
				<?php
				break;
			case 'featured_in':
				?>
					<div class="inline-edit-col">
						<label>
							<span>Featured In</span>
							<input type="text" name="featured_in">
						</label>
					</div>
				</fieldset>
				<?php
				break;
			default:
				break;
		}
	}

	/**
	 * Add javascript for the quick editing.
	 *
	 * @param string $page The page executing.
	 */
	public function add_js( $page ) {
		// Are we editing the media coverage page.
		$post_type = get_post_type();
		if ( 'edit.php' !== $page || 'orc_media_coverage' !== $post_type ) {
			return;
		}

		$full_path = plugins_url() . '/orc-options/dist/js/orc.mediacoverage-admin.min.js';
		$siteurl   = get_option( 'siteurl' );
		$script    = str_replace( $siteurl, '', $full_path );
		wp_enqueue_script( 'custom-media-coverage-quickedit-box', $script, array( 'jquery', 'inline-edit-post' ), Config::getVersion(), true );
	}

	/**
	 * Shortcode to display the media coverage.
	 *
	 * @param array  $atts     Attributes for the shortcode.
	 * @param string $content The content for the shortcode.
	 *
	 * @return string HTML content result for the shortcode.
	 */
	public function orc_media_coverage_shortcode( $atts, $content = null ) {

		$postid = isset( $atts['postid'] ) ? $atts['postid'] : null;

		$args = array(
			'post_type'      => 'orc_media_coverage',
			'posts_per_page' => -1,
			'orderby'        => array(
				'date'  => 'DESC',
				'title' => 'ASC',
			),
		);

		$the_query = new \WP_Query( $args );
		$data      = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$id         = get_the_ID();
				$img_id     = get_post_thumbnail_id( $id );
				$img_src    = wp_get_attachment_image_url( $img_id, array( 300, 300 ) );
				$img_srcset = wp_get_attachment_image_srcset( $img_id, array( 300, 300 ) );
				$title      = get_post( $img_id )->post_title;
				$alt        = isset( get_post_meta( $img_id, '_wp_attachment_image_alt' )[0] ) ? get_post_meta( $img_id, '_wp_attachment_image_alt' )[0] : $title;
				$fields     = get_post_custom( $id );
				$data[]     = array(
					'id'          => $id,
					'name'        => get_the_title(),
					'img_src'     => $img_src,
					'img_srcset'  => $img_srcset,
					'alt'         => $alt,
					'featured_on' => $fields['featured_on'][0],
					'featured_in' => $fields['featured_in'][0],
				);
			}
		}
		wp_reset_postdata();

		if ( 0 === count( $data ) ) {
			return '<div class="media-coverage">No Media Coverage</div>';
		}

		$retstr = '<div class="media-coverage" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1em">';
		foreach ( $data as $media_coverage ) {
			$featured = '';
			if ( '' !== $media_coverage['featured_on'] ) {
				$featured .= 'Featured on ' . $media_coverage['featured_on'];
			} elseif ( '' !== $media_coverage['featured_in'] ) {
				$featured .= 'Featured in ' . $media_coverage['featured_ion'];
			}
			$retstr .= "<div class='a-media-coverage' style='display: flex; flex-wrap: wrap; flex-direction: column; align-items: center; text-align: center'><a href=\"{$media_coverage['permalink']}\">{$media_coverage['name']}</a>";
			$retstr .= "<figure class=\"imge-wrapper\"><img style=\"object-fit: cover;width: 100%;display: block;\" src=\"{$media_coverage['img_src']}\"srcset=\"{$media_coverage['img_srcset']}\"sizes=\"(min-width: 1800px) 300px,(min-width: 550px) 150px\" alt=\"{$media_coverage['alt']}\" class=\"img\" loading=\"lazy\"></figure>";
			$retstr .= '' === $featured ? '' : $featured;
			$retstr .= '</div> <!-- /.a-media-coverage -->';
		}
		$retstr .= '</div> <!-- /.media-coverage -->';

		return $retstr;
	}

} // OrcMediaCoverage

new OrcMediaCoverage();
