<?php
/**
 * Class for the Orchard Recovery Center Alumni Meetings (FOO).
 *
 * @package ORC_Block_Options
 */

namespace ORCOptions\Includes\CPT;

defined( 'ABSPATH' ) || die;

use ORCOptions\Includes\Config;

/**
 * Class for the Orchard Recovery Center Alumni Meetings (FOO).
 */
class OrcAlumniMeeting {

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
		add_filter( 'manage_orc_alumni_meeting_posts_columns', array( $this, 'table_head' ) );
		add_action( 'manage_orc_alumni_meeting_posts_custom_column', array( $this, 'table_content' ), 10, 2 );
		add_filter( 'manage_edit-orc_alumni_meeting_sortable_columns', array( $this, 'sortable_columns' ) );
		add_action( 'pre_get_posts', array( $this, 'posts_orderby' ) );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_js' ) );
		add_shortcode( 'orc_alumni_meeting', array( $this, 'orc_alumni_meeting_shortcode' ) );
	} // __construct

	/**
	 * Register the custom post type for the class
	 */
	public function register_cpt() {

		$labels = array(
			'name'                  => __( 'Alumni Meetings', 'orcoptions' ),
			'singular_name'         => __( 'Alumni Meeting', 'orcoptions' ),
			'menu_name'             => __( 'Alumni Meetings', 'orcoptions' ),
			'name_admin_bar'        => __( 'Alumni Meetings', 'orcoptions' ),
			'add_new'               => __( 'Add New', 'orcoptions' ),
			'add_new_item'          => __( 'Add New Alumni Meeting', 'orcoptions' ),
			'new_item'              => __( 'New Alumni Meeting', 'orcoptions' ),
			'edit_item'             => __( 'Edit Alumni Meeting', 'orcoptions' ),
			'view_item'             => __( 'View Alumni Meetings', 'orcoptions' ),
			'all_items'             => __( 'All Alumni Meetings', 'orcoptions' ),
			'search_items'          => __( 'Search Alumni Meetings', 'orcoptions' ),
			'parent_item_colon'     => __( 'Parent Alumni Meeting:', 'orcoptions' ),
			'not_found'             => __( 'No Alumni Meetings found.', 'orcoptions' ),
			'not_found_in_trash'    => __( 'No Alumni Meetings found in Trash.', 'orcoptions' ),
			'featured_image'        => __( 'Alumni Meeting Image', 'orcoptions' ),
			'set_featured_image'    => __( 'Set Alumni Meeting image', 'orcoptions' ),
			'remove_featured_image' => __( 'Remove Alumni Meeting image', 'orcoptions' ),
			'use_featured_image'    => __( 'Use as Alumni Meeting image', 'orcoptions' ),
			'archives'              => __( 'Alumni Meeting archives', 'orcoptions' ),
			'insert_into_item'      => __( 'Insert into Alumni Meeting', 'orcoptions' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Alumni Meeting', 'orcoptions' ),
			'filter_items_list'     => __( 'Filter Alumni Meeting list', 'orcoptions' ),
			'items_list_navigation' => __( 'Alumni Meetings list navigation', 'orcoptions' ),
			'items_list'            => __( 'Alumni Meetings list', 'orcoptions' ),
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
				'slug'       => 'alumni/foo-meetings',
				'with_front' => true,
			),
			'capability_type'      => 'post',
			'has_archive'          => true,
			'hierarchical'         => false,
			'menu_position'        => 20,
			'menu_icon'            => 'dashicons-multisite',
			'supports'             => array( 'title', 'editor', 'excerpt' ),
			'register_meta_box_cb' => array( $this, 'register_meta_box' ),
		);

		register_post_type( 'orc_alumni_meeting', $args );

	} // register_cpt

	/**
	 * Add the meta box to the custom post type
	 */
	public function register_meta_box() {

		add_meta_box( 'orc_alumni_meeting_data', 'Alumni Meeting Information', array( $this, 'meta_box' ), 'orc_alumni_meeting', 'side', 'high' );

	} // register_meta_box

	/**
	 * Display the meta box
	 *
	 * @global type $post - The current post
	 */
	public function meta_box() {

		global $post;

		// Nonce field to validate form request from current site.
		wp_nonce_field( basename( __FILE__ ), 'orc_alumni_meeting_data' );

		// Get the alumni meeting information if it's already entered.
		$display_order = sanitize_text_field( get_post_meta( $post->ID, 'display_order', true ) );

		$display_order = '' === $display_order ? '0' : $display_order;

		// Output the fields.
		?>
		<label for="display_order">Display Order: </label>
		<input type="number" id="display_order" name="display_order" min="0" required value="<?php echo esc_html( $display_order ); ?>" class="widefat" minimum="0">
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
		$is_valid_nonce = ( isset( $_POST['orc_alumni_meeting_data'] ) && wp_verify_nonce( $_POST['orc_alumni_meeting_data'], basename( __FILE__ ) ) ) ? true : false;
		$can_edit       = current_user_can( 'edit_post', $post_id );

		// Exits script depending on save status.
		if ( $is_autosave || $is_revision || ! $is_valid_nonce || ! $can_edit ) {
			return;
		}

		// Now that we're authenticated, time to save the data.
		// This sanitizes the data from the field and saves it into an array $events_meta.
		$alumni_meeting_meta                  = array();
		$alumni_meeting_meta['display_order'] = isset( $_POST['display_order'] ) ? sanitize_text_field( $_POST['display_order'] ) : '0';

		if ( '' === $alumni_meeting_meta['display_order'] ) {
			$alumni_meeting_meta['display_order'] = '0';
		}

		// Cycle through the $events_meta array.
		foreach ( $alumni_meeting_meta as $key => $alumni_meeting ) {
			// Don't store custom data twice.
			if ( get_post_meta( $post_id, $key, false ) ) {
				// If the custom field already has a value, update it.
				update_post_meta( $post_id, $key, $alumni_meeting );
			} else {
				// If the custom field doesn't have a value, add it.
				add_post_meta( $post_id, $key, $alumni_meeting );
			}

			if ( '' === $alumni_meeting ) {
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

		// update the display_order.
		$display_order = ! empty( $_POST['display_order'] ) ? sanitize_text_field( $_POST['display_order'] ) : '';
		update_post_meta( $post_id, 'display_order', $display_order );

	} // save_inline

	/**
	 * Load the single post template with the following order:
	 * - Theme single post template (THEME/plugins/orc_options/templates/single-alumni-meeting.php)
	 * - Plugin single post template (PLUGIN/templates/single-alumni-meeting.php)
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

		// Check if this is an alumni meeting.
		if ( 'orc_alumni_meeting' === $post->post_type ) {

			// Plugin/Theme path.
			$plugin_path = plugin_dir_path( __FILE__ ) . '../../templates/';
			$theme_path  = get_stylesheet_directory() . '/plugins/orc_options/templates/';

			// The name of custom post type single template.
			$template_name = 'single-alumni_meeting.php';

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

		// This is not an alumni meeting, do nothing with $template.
		return $template;

	} // load_template

	/**
	 * Load the archive page.
	 *
	 * @param string $template The template to use.
	 */
	public function load_archive( $template ) {

		global $post;

		// Check if this is an alumni meeting.
		if ( 'orc_alumni_meeting' === $post->post_type ) {
			// Plugin/Theme path.
			$plugin_path = plugin_dir_path( __FILE__ ) . '../../templates/';
			$theme_path  = get_stylesheet_directory() . '/plugins/orc_options/templates/';

			// The name of custom post type single template.
			$template_name = 'archive-alumni-meeting.php';

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

		// This is not an alumni meeting, do nothing with $template.
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
		$newcols['display_order'] = 'Display Order';

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

		if ( 'display_order' === $column_name ) {
			$display_order = get_post_meta( $post_id, 'display_order', true );
			if ( '' === $display_order ) {
				$display_order = '0';
			}
			echo esc_html( $display_order );
		}

	} // table_content

	/**
	 * Make columns in admin page sortable.
	 *
	 * @param array $columns The columns array.
	 */
	public function sortable_columns( $columns ) {
		$columns['display_order'] = 'display_order';
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

		if ( 'display_order' === $query->get( 'orderby' ) ) {
			$order_direction = $query->get( 'order' );
			$query->set(
				'meta_query',
				array(
					'meeting_clause' => array(
						'key'        => 'display_order',
						'type'       => 'numeric',
					),
				)
			);
			$query->set(
				'orderby',
				array(
					'meeting_clause' => $order_direction,
					'title'          => 'ASC',
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

		// Are we on the alumni meetings page.
		if ( 'orc_alumni_meeting' !== $post_type ) {
			return;
		}

		switch ( $column_name ) {
			case 'display_order':
				?>
					<div style="clear:both;">Custom Fields</div>
					<hr style="border: 1px solid #eee;">
					<fieldset class="inline-edit-col-left"">
						<div class="inline-edit-col">
							<label>
								<span>Display Order</span>
								<input type="number" min="0" name="display_order">
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
		if ( 'edit.php' !== $page || 'orc_alumni_meeting' !== $post_type ) {
			return;
		}

		$full_path = plugins_url() . '/orc-options/dist/js/orc.alumnimeeting-admin.min.js';
		$siteurl   = get_option( 'siteurl' );
		$script    = str_replace( $siteurl, '', $full_path );
		wp_enqueue_script( 'custom-quickedit-box', $script, array( 'jquery', 'inline-edit-post' ), Config::getVersion(), true );
	}

	/**
	 * Shortcode to display the alumni meeting.
	 *
	 * @param array  $atts     Attributes for the shortcode.
	 * @param string $content The content for the shortcode.
	 *
	 * @return string HTML content result for the shortcode.
	 */
	public function orc_alumni_meeting_shortcode( $atts, $content = null ) {

		$postid    = isset( $atts['postid'] ) ? $atts['postid'] : null;

		$args = array(
			'post_type'      => 'orc_alumni_meeting',
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

		$the_query = new \WP_Query( $args );
		$data      = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$id         = get_the_ID();
				$fields     = get_post_custom( $id );
				$data[]    = array(
					'id'        => $id,
					'name'      => get_the_title(),
					'permalink' => get_the_permalink(),
				);
			}
		}
		wp_reset_postdata();

		if ( 0 === count( $data ) ) {
			return '<div class="alumni-meetings">No Alumni Meetings Found</div>';
		}

		$retstr = '<div class="alumni-meetings" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1em">';
		foreach ( $data as $alumni_meeting ) {
			$retstr .= "<div class='alumni-meeting' style='display: flex; flex-wrap: wrap; flex-direction: column; align-items: center; text-align: center'><a href=\"{$alumni_meeting['permalink']}\">{$alumni_meeting['name']}</a>";
			$retstr .= '</div> <!-- /.alumni-meeting -->';
		}
		$retstr .= '</div> <!-- /.alumni-meetings -->';

		return $retstr;
	}

} // OrcAlumniMeeting

new OrcAlumniMeeting();
