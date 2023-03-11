<?php

/**
 *
 * @param int $postid The post ID
 */
function orc_staff_shortcode( $atts, $content = null ) {

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
	$the_query = new WP_Query( $args );
	$data      = array();
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$id     = get_the_ID();
			$img_id = get_post_thumbnail_id( $id );
			$size   = 'thumbnail';
			$img_src = wp_get_attachment_image_url( $img_id, $size );
			$img_srcset = wp_get_attachment_image_srcset( $img_id, $size );
			$title = get_post($img_id)->post_title;
			$alt = isset(get_post_meta($img_id, '_wp_attachment_image_alt')[0]) ? get_post_meta($img_id, '_wp_attachment_image_alt')[0] : $title;
			$fields = get_post_custom( $id );
			if ( $homepage ) {
				if ( strlen( $fields['on_home_page'][0] ) > 0 ) {
					$data[] = array(
						'id'             => $id,
						'name'           => get_the_title(),
						'homepage'       => true,
						'job'            => $fields['position'][0],
						'qualifications' => $fields['qualifications'][0],
						'permalink' => get_the_permalink(),
						'img_src'   => $img_src,
						'img_srcset' => $img_srcset,
						'alt'        => $alt,
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
						'permalink' => get_the_permalink(),
						'img_src'   => $img_src,
						'img_srcset' => $img_srcset,
						'alt'        => $alt,
					);
				} else {
					$data[] = array(
						'id'        => $id,
						'name'      => get_the_title(),
						'homepage'  => false,
						'job'       => $fields['position'][0],
						'permalink' => get_the_permalink(),
						'img_src'   => $img_src,
						'img_srcset' => $img_srcset,
						'alt'        => $alt,
					);
				}
			}
		}
	}
	wp_reset_postdata();

	$retstr = '<div class="staff-members" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1em">';
	foreach ( $data as $staff ) {
		$retstr .= "<div class='staff-member' style='display: flex; flex-wrap: wrap; flex-direction: column; align-items: center; text-align: center'><a href=\"{$staff['permalink']}\">{$staff['name']}</a>";
		$retstr .= "<figure class=\"imge-wrapper\"><img src=\"{$staff['img_src']}\"srcset=\"{$staff['img_srcset']}\"sizes=\"(max-width: 300px) 300px,(max-width: 640px) 640px,(max-width: 1024px) 1024px\" alt=\"{$staff['alt']}\" class=\"img\" loading=\"lazy\"></figure>";
		$retstr .= "   <div class='staff-job'>{$staff['job']}</div>";
		if ( isset( $staff['qualifications'] ) ) {
			$retstr .= "   <div class='staff-qualifications'>{$staff['qualifications']}</div>";
		}
		$retstr .= '</div> <!-- /.staff-member -->';
	}
	$retstr .= '</div> <!-- /.staff-members -->';

	return $retstr;
}
add_shortcode( 'orc_staff', 'orc_staff_shortcode' );
