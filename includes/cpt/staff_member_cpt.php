<?php

namespace ORCOptions\Includes\CPT;

defined( 'ABSPATH' ) || die;

use ORCOptions\Includes\Config;

class orcStaffMember {

	/**
	 * Class constructor
	 *
	 * Performs all the initialization for the class
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ), 0 );
		add_action( 'save_post', array( $this, 'save_meta' ), 1, 2 );
		add_filter( 'single_template', array( $this, 'load_template' ) );
		add_filter( 'archive_template', array( $this, 'load_archive' ) );
		add_filter( 'manage_edit-orc_staff_member_columns', array( $this, 'table_head' ) );
		add_action( 'manage_orc_staff_member_posts_custom_column', array( $this, 'table_content' ), 10, 2 );
		add_shortcode( 'orc_staff', array( $this, 'orc_staff_shortcode' ) );
	} // __construct

	/**
	 * Register the custom post type for the class
	 */
	public function register_cpt() {

		$labels = array(
			'name'                  => __( 'Staff', 'orcoptions' ),
			'singular_name'         => __( 'Staff', 'orcoptions' ),
			'menu_name'             => __( 'Staff', 'orcoptions' ),
			'name_admin_bar'        => __( 'Staff', 'orcoptions' ),
			'add_new'               => __( 'Add New', 'orcoptions' ),
			'add_new_item'          => __( 'Add New Staff', 'orcoptions' ),
			'new_item'              => __( 'New Staff', 'orcoptions' ),
			'edit_item'             => __( 'Edit Staff', 'orcoptions' ),
			'view_item'             => __( 'View Staff', 'orcoptions' ),
			'all_items'             => __( 'All Staff', 'orcoptions' ),
			'search_items'          => __( 'Search Staff', 'orcoptions' ),
			'parent_item_colon'     => __( 'Parent Staff:', 'orcoptions' ),
			'not_found'             => __( 'No Staff found.', 'orcoptions' ),
			'not_found_in_trash'    => __( 'No Staff found in Trash.', 'orcoptions' ),
			'featured_image'        => __( 'Staff Image', 'orcoptions' ),
			'set_featured_image'    => __( 'Set Staff image', 'orcoptions' ),
			'remove_featured_image' => __( 'Remove Staff image', 'orcoptions' ),
			'use_featured_image'    => __( 'Use as Staff image', 'orcoptions' ),
			'archives'              => __( 'Staff archives', 'orcoptions' ),
			'insert_into_item'      => __( 'Insert into Staff', 'orcoptions' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Staff', 'orcoptions' ),
			'filter_items_list'     => __( 'Filter Staff list', 'orcoptions' ),
			'items_list_navigation' => __( 'Staff list navigation', 'orcoptions' ),
			'items_list'            => __( 'Staff list', 'orcoptions' ),
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
				'slug'       => 'staff_members',
				'with_front' => true,
			),
			'capability_type'      => 'post',
			'has_archive'          => true,
			'hierarchical'         => false,
			'menu_position'        => 20,
			'menu_icon'            => 'dashicons-groups',
			'supports'             => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'register_meta_box_cb' => array( $this, 'register_meta_box' ),
		);

		register_post_type( 'orc_staff_member', $args );

	} // register_cpt

	/**
	 * Add the meta box to the custom post type
	 */
	public function register_meta_box() {

		add_meta_box( 'orc_staff_data', 'Staff Information', array( $this, 'meta_box' ), 'orc_staff_member', 'normal', 'high' );

	} // register_meta_box

	/**
	 * Display the meta box
	 *
	 * @global type $post - The current post
	 */
	public function meta_box() {

		global $post;

		// Nonce field to validate form request from current site.
		wp_nonce_field( basename( __FILE__ ), 'orc_staff_data' );

		// Get the staff information if it's already entered.
		$position       = get_post_meta( $post->ID, 'position', true );
		$qualifications = get_post_meta( $post->ID, 'qualifications', true );
		$on_home_page   = get_post_meta( $post->ID, 'on_home_page', true );

		// Output the fields.
		echo '<label for="position">Job Title: </label><input type="text" id="position" name="position" required value="' . sanitize_text_field( $position ) . '" class="widefat">';
		echo '<label for="qualifications">Qualifications: </label><input type="text" id="qualifications" name="qualifications" value="' . sanitize_text_field( $qualifications ) . '" class="widefat">';
		echo '<label for="on_home_page">Show On Home Page: </label><input type="checkbox" id="on_home_page" name="on_home_page" value="1" ' . ( '1' === sanitize_text_field( $on_home_page ) ? 'checked' : '' ) . '>';

	} // meta_box

	/**
	 * Register the taxonomies for the Staff Members as tags
	 */
	public function register_taxonomies() {

		$tags = array(
			'name'                       => __( 'Staff Department', 'orcoptions' ),
			'singular_name'              => __( 'Staff Department', 'orcoptions' ),
			'search_items'               => __( 'Search Staff Departments', 'orcoptions' ),
			'popular_items'              => __( 'Popular Staff Departments', 'orcoptions' ),
			'all_items'                  => __( 'All Staff Departments', 'orcoptions' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Staff Department', 'orcoptions' ),
			'update_item'                => __( 'Update Staff Department', 'orcoptions' ),
			'add_new_item'               => __( 'Add New Staff Department', 'orcoptions' ),
			'new_item_name'              => __( 'New Staff Department Name', 'orcoptions' ),
			'separate_items_with_commas' => __( 'Separate Staff departments with commas', 'orcoptions' ),
			'add_or_remove_items'        => __( 'Add or remove Staff departments', 'orcoptions' ),
			'choose_from_most_used'      => __( 'Choose from the most used Staff departments', 'orcoptions' ),
			'menu_name'                  => __( 'Staff Departments' ),
		);

		$args = array(
			'public'            => true,
			'hierarchical'      => false,
			'labels'            => $tags,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'query_var'         => false,
			'rewrite'           => false,
		);

		register_taxonomy( 'orc_staff_member_departments', 'orc_staff_member', $args );

	} // register_taxonomies

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
		$is_valid_nonce = ( isset( $_POST['orc_staff_data'] ) && wp_verify_nonce( $_POST['orc_staff_data'], basename( __FILE__ ) ) ) ? true : false;
		$can_edit       = current_user_can( 'edit_post', $post_id );

		// Exits script depending on save status.
		if ( $is_autosave || $is_revision || ! $is_valid_nonce || ! $can_edit ) {
			return;
		}

		// Now that we're authenticated, time to save the data.
		// This sanitizes the data from the field and saves it into an array $events_meta.
		$staff_meta                   = array();
		$staff_meta['position']       = isset( $_POST['position'] ) ? sanitize_text_field( $_POST['position'] ) : '';
		$staff_meta['qualifications'] = isset( $_POST['qualifications'] ) ? sanitize_text_field( $_POST['qualifications'] ) : '';
		$staff_meta['on_home_page']   = isset( $_POST['on_home_page'] ) ? sanitize_text_field( $_POST['on_home_page'] ) : '0';

		// Cycle through the $events_meta array.
		foreach ( $staff_meta as $key => $value ) {
			// Don't store custom data twice.
			if ( get_post_meta( $post_id, $key, false ) ) {
				// If the custom field already has a value, update it.
				update_post_meta( $post_id, $key, $value );
			} else {
				// If the custom field doesn't have a value, add it.
				add_post_meta( $post_id, $key, $value );
			} // if

			if ( ! $value ) {
				// Delete the meta key if there's no value.
				delete_post_meta( $post_id, $key );
			} // if( !$value)
		} // foreach

	} // save_meta

	/**
	 * Load the single post template with the following order:
	 * - Theme single post template (THEME/plugins/orc_options/templates/single-staff-member.php)
	 * - Plugin single post template (PLUGIN/templates/single-staff-member.php)
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

		// Check if this is a staff member.
		if ( 'orc_staff_member' === $post->post_type ) {

			// Plugin/Theme path.
			$plugin_path = plugin_dir_path( __FILE__ ) . '../../templates/';
			$theme_path  = get_stylesheet_directory() . '/plugins/orc_options/templates/';

			// The name of custom post type single template.
			$template_name = 'single-staff-member.php';

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

		// This is not a staff member, do nothing with $template.
		return $template;

	} // load_template

	/**
	 * Load the archive page.
	 *
	 * @param string $template The template to use.
	 */
	public function load_archive( $template ) {

		global $post;

		// Check if this is a staff member.
		if ( 'orc_staff_member' === $post->post_type ) {
			// Plugin/Theme path.
			$plugin_path = plugin_dir_path( __FILE__ ) . '../../templates/';
			$theme_path  = get_stylesheet_directory() . '/plugins/orc_options/templates/';

			// The name of custom post type single template.
			$template_name = 'archive-staff-member.php';

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

		// This is not a staff member, do nothing with $template.
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
		$newcols['position']       = 'Position';
		$newcols['qualifications'] = 'Qualifications';
		$newcols['on_home_page']   = 'Show On Home Page?';

		// Want date last.
		unset( $columns['date'] );

		// Add all other selected columns.
		foreach ( $columns as $col => $title ) {
			$newcols[ $col ] = $title;
		}

		// Add the date back.
		$newcols['date'] = 'Date';

		return $newcols;

	} // table_head

	/**
	 * Display the meta data associated with a post on the administration table.
	 *
	 * @param string $column_name - The header of the column.
	 * @param int    $post_id - The ID of the post being displayed.
	 */
	public function table_content( $column_name, $post_id ) {

		if ( 'position' === $column_name ) {
			$position = get_post_meta( $post_id, 'position', true );
			echo $position;
		}
		if ( 'qualifications' === $column_name ) {
			$qualifications = get_post_meta( $post_id, 'qualifications', true );
			echo $qualifications;
		}
		if ( 'on_home_page' === $column_name ) {
			$on_home_page = get_post_meta( $post_id, 'on_home_page', true );
			$on_home_page = ( strlen( $on_home_page ) > 0 ) ? 'YES' : '';
			echo $on_home_page;
		}

	} // table_content

	/**
	 * Shortcode to display the staff.
	 *
	 * @param array $atts     Attributes for the shortcode.
	 * @param string $content The content for the shortcode.
	 *
	 * @return string HTML content result for the shortcode.
	 */
	public function orc_staff_shortcode( $atts, $content = null ) {

		extract(
			shortcode_atts(
				array(
					'postid'   => null,
					'homepage' => null,
				),
				$atts
			)
		);

		if ( $homepage ) {
			$args = array(
				'post_type'      => 'orc_staff_member',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'posts_per_page' => -1,
				'meta_key'       => 'on_home_page',
				'meta_value'     => '1',
			);
		} else {
			$args = array(
				'post_type'      => 'orc_staff_member',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'posts_per_page' => -1,
			);
		}
		$the_query = new \WP_Query( $args );
		$data      = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$taxonomy_str       = '';
				$taxonomies         = get_object_taxonomies( 'orc_staff_member' );
					$taxonomy_names = wp_get_object_terms( get_the_ID(), $taxonomies, array( 'fields' => 'names' ) );
				if ( ! empty( $taxonomy_names ) ) {
					foreach ( $taxonomy_names as $tax_name ) {
						$taxonomy_str .= ',' . $tax_name;
					}
				}
				$id         = get_the_ID();
				$img_id     = get_post_thumbnail_id( $id );
				$img_src    = wp_get_attachment_image_url( $img_id, array( 300, 300 ) );
				$img_srcset = wp_get_attachment_image_srcset( $img_id, array( 300, 300 ) );
				$title      = get_post( $img_id )->post_title;
				$alt        = isset( get_post_meta( $img_id, '_wp_attachment_image_alt' )[0] ) ? get_post_meta( $img_id, '_wp_attachment_image_alt' )[0] : $title;
				$fields     = get_post_custom( $id );
				if ( $homepage ) {
					if ( strlen( $fields['on_home_page'][0] ) > 0 ) {
						$data[] = array(
							'id'             => $id,
							'name'           => get_the_title(),
							'homepage'       => true,
							'job'            => $fields['position'][0],
							'qualifications' => $fields['qualifications'][0],
							'permalink'      => get_the_permalink(),
							'img_src'        => $img_src,
							'img_srcset'     => $img_srcset,
							'alt'            => $alt,
							'departments'    => $taxonomy_str,
						);
					}
				} else {
					if ( array_key_exists( 'qualifications', $fields ) ) {
						$data[] = array(
							'id'             => $id,
							'name'           => get_the_title(),
							'homepage'       => false,
							'job'            => $fields['position'][0],
							'qualifications' => $fields['qualifications'][0],
							'permalink'      => get_the_permalink(),
							'img_src'        => $img_src,
							'img_srcset'     => $img_srcset,
							'alt'            => $alt,
							'departments'    => $taxonomy_str,
						);
					} else {
						$data[] = array(
							'id'          => $id,
							'name'        => get_the_title(),
							'homepage'    => false,
							'job'         => $fields['position'][0],
							'permalink'   => get_the_permalink(),
							'img_src'     => $img_src,
							'img_srcset'  => $img_srcset,
							'alt'         => $alt,
							'departments' => $taxonomy_str,
						);
					}
				}
			}
		}
		wp_reset_postdata();

		$retstr = '<div class="staff-members" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1em">';
		foreach ( $data as $staff ) {
			$retstr .= "<div class='staff-member' style='display: flex; flex-wrap: wrap; flex-direction: column; align-items: center; text-align: center'><a href=\"{$staff['permalink']}\">{$staff['name']}</a>";
			$retstr .= "<figure class=\"imge-wrapper\"><img style=\"object-fit: cover;width: 100%;display: block;\" src=\"{$staff['img_src']}\"srcset=\"{$staff['img_srcset']}\"sizes=\"(min-width: 1800px) 300px,(min-width: 550px) 150px\" alt=\"{$staff['alt']}\" class=\"img\" loading=\"lazy\"></figure>";
			$retstr .= "   <div class='staff-job'>{$staff['job']}</div>";
			if ( isset( $staff['qualifications'] ) ) {
				$retstr .= "   <div class='staff-qualifications'>{$staff['qualifications']}</div>";
			}
			$retstr .= "    <div class='staff-departments'>{$staff['departments']}</div>";
			$retstr .= '</div> <!-- /.staff-member -->';
		}
		$retstr .= '</div> <!-- /.staff-members -->';

		return $retstr;
	}

} // orcStaffMember

new orcStaffMember();
