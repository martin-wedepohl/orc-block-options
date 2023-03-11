<?php

/**
 *
 * @param int $postid The post ID
 */
function orc_image_shortcode( $atts, $content = null ) {
	$thumbnailwidth  = get_option( 'thumbnail_size_w' );
	$thumbnailheight = get_option( 'thumbnail_size_h' );

	extract(
		shortcode_atts(
			array(
				'postid'   => '',
				'roundimg' => false,
				'width'    => $thumbnailwidth,
				'height'   => $thumbnailheight,
				'imgclass' => '',
			),
			$atts
		)
	);
	$roundimg = ( false == $roundimg ) ? '' : '-webkit-border-radius:50%;-moz-border-radius:50%;border-radius:50%';

	$retstr = '';
	if ( has_post_thumbnail( $postid ) ) {
		$thumbid         = get_post_thumbnail_id( $postid );
		$url             = get_the_post_thumbnail_url( $postid, array( $width, $height ) );
		$forcedimensions = '';
		$alt             = get_post_meta( $thumbid, '_wp_attachment_image_alt', true );
		$retstr          = '<div class="aligncenter" style="display:table;max-width:' . $width . 'px;max-height:' . $height . 'px;">';
		$retstr         .= '<img id="imageid-' . $thumbid . '" style="' . $roundimg . '" ';
		$retstr         .= 'src="' . $url . '" ';
		$retstr         .= 'srcset="' . wp_get_attachment_image_srcset( $thumbid, array( $width, $height ) ) . '" ';
		$retstr         .= 'sizes="100vw, 100vh"';
		$retstr         .= 'class="attachment-' . $width . 'x' . $height . ' ' . $imgclass . '" ';
		$retstr         .= 'alt="' . $alt . '">';
		$retstr         .= '</div>';
	}
	return $retstr;
}
add_shortcode( 'orc_image', 'orc_image_shortcode' );

function orc_facebook_shortcode( $atts = null, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'height'        => 500,
				'width'         => 340,
				'tabs'          => 'timeline,events',
				'small_header'  => false,
				'hide_cover'    => false,
				'show_facepile' => true,
			),
			$atts
		)
	);

	$facebookappid = get_option( 'orc_options_facebookappid' );

	if ( 0 === strlen( $facebookappid ) ) {
		$html = '<p>MISSING FACEBOOK APP ID</p>';
	} else {
		$html  = '<div id="fb-root"></div><script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.12&appId=' . $facebookappid . '&autoLogAppEvents=1";fjs.parentNode.insertBefore(js, fjs);}(document, "script", "facebook-jssdk"));</script>';
		$html .= '<div class="fb-page" data-href="https://www.facebook.com/OrchardRecovery" data-tabs="' . $tabs . '" data-width="' . $width . '" data-height="' . $height . '" data-small-header="' . $small_header . '" data-adapt-container-width="true" data-hide-cover="' . $hide_cover . '" data-show_facepile="' . $show_facepile . '"><blockquote cite="https://www.facebook.com/OrchardRecovery" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/OrchardRecovery">Orchard Recovery Center</a></blockquote></div>';
	}

	return $html;
}
add_shortcode( 'orc_facebook', 'orc_facebook_shortcode' );

function orc_family_programs_shortcode( $atts = null, $content = null ) {
	$a = shortcode_atts(
		array(
			'title'        => '',
			'monthsbefore' => 0,
			'monthsafter'  => 2,
		),
		$atts
	);

	date_default_timezone_set( get_option( 'timezone_string' ) );
	$today           = date( 'Y-m-d' );
	$monthsbeforestr = '-' . $a['monthsbefore'] . ' months';
	$monthsafterstr  = '+' . $a['monthsafter'] . ' months';
	$startdate       = date( 'Y-m-d', strtotime( $monthsbeforestr, strtotime( $today ) ) );
	$enddate         = date( 'Y-m-d', strtotime( $monthsafterstr, strtotime( $today ) ) );

	$retstr = '';

	$termid     = 1;
	$args       = array(
		'public'   => true,
		'_builtin' => false,
	);
	$output     = 'names';
	$operator   = 'and';
	$taxonomies = get_taxonomies( $args, $output, $operator );
	if ( $taxonomies ) {
		foreach ( $taxonomies  as $taxonomy ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => true,
					'name'       => 'Family Programming',
				)
			);
			if ( count( $terms ) > 0 ) {
				$termid = $terms[0]->term_id;
			}
		}
	}

	$args = array(
		'post_type'  => 'event',
		'showposts'  => -1,
		'meta_key'   => '_event_start_date',
		'orderby'    => 'meta_value',
		'order'      => 'ASC',
		'tax_query'  => array(
			array(
				'taxonomy' => 'event-categories',
				'field'    => 'term_id',
				'terms'    => $termid,
			),
		),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => '_event_start_date',
				'value'   => $startdate,
				'compare' => '>=',
				'type'    => 'CHAR',
			),
			array(
				'key'     => '_event_start_date',
				'value'   => $enddate,
				'compare' => '<=',
				'type'    => 'CHAR',
			),
		),
	);

	$family_programs_array = array();
	$query                 = new WP_Query( $args );
	while ( $query->have_posts() ) {
		$query->the_post();
		$id          = get_the_ID();
		$fields      = get_post_custom( $id );
		$title       = get_the_title();
		$extdweekend = false;
		if ( array_key_exists( 'EXTDWEEKEND', $fields ) ) {
			$extdweekend = true;
		}
		$permalink = '';
		if ( strlen( get_the_content() ) > 0 ) {
			$permalink = get_the_permalink();
		}
		$family_programs_array[] = array(
			'id'          => $id,
			'title'       => $title,
			'permalink'   => $permalink,
			'extdweekend' => $extdweekend,
			'date'        => $fields['_event_start_date'][0],
			'time'        => $fields['_event_start_time'][0],
		);
	}
	wp_reset_query();

	$retstr    = '';
	$currmonth = -1;
	foreach ( $family_programs_array as $family_program ) {
		$year     = date( 'Y', strtotime( $family_program['date'] . ' ' . $family_program['time'] ) );
		$month    = 0 + date( 'm', strtotime( $family_program['date'] . ' ' . $family_program['time'] ) );
		$monthstr = date( 'F', strtotime( $family_program['date'] . ' ' . $family_program['time'] ) );
		$progdate = date( 'D M j', strtotime( $family_program['date'] . ' ' . $family_program['time'] ) );
		if ( $month != $currmonth ) {
			if ( -1 != $currmonth ) {
				$retstr .= '</ul>';
			}
			$retstr   .= '<br><strong>' . $monthstr . ' ' . $year . '</strong><br>';
			$currmonth = $month;
			$retstr   .= '<ul>';
		}
		$boldeventstart = ( ( $family_program['extdweekend'] ) ? '<strong>' : '' );
		$boldeventend   = ( ( $family_program['extdweekend'] ) ? '</strong>' : '' );
		if ( strlen( $family_program['permalink'] ) > 0 ) {
			$retstr .= '<li>' . $boldeventstart . $progdate . ' - ' . '<a href="' . $family_program['permalink'] . '" title="View topic details">' . $family_program['title'] . '</a>' . $boldeventend . '</li>';
		} else {
			$retstr .= '<li>' . $boldeventstart . $progdate . ' - ' . $family_program['title'] . $boldeventend . '</li>';
		}
	}
	if ( strlen( $retstr ) > 0 ) {
		$retstr .= '</ul>';
	}

	return $retstr;
}
add_shortcode( 'orc_family_programs', 'orc_family_programs_shortcode' );

function orc_department_shortcode( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'emailto' => '',
			),
			$atts
		)
	);

	if ( strlen( $emailto ) > 0 ) {
		if ( 0 === strcmp( 'intakedepartment', $emailto ) ) {
			$retstr = 'Orchard Recovery Center Intake Department';
		} elseif ( 0 === strcmp( 'communicationsdepartment', $emailto ) ) {
			$retstr = 'Orchard Recovery Center Communications & Social Media Department';
		} elseif ( 0 === strcmp( 'hrdepartment', $emailto ) ) {
			$retstr = 'Orchard Recovery Center Human Resources Department';
		} elseif ( 0 === strcmp( 'alumnidepartment', $emailto ) ) {
			$retstr = 'Orchard Recovery Center Alumni Coordinator';
		} elseif ( 0 === strcmp( 'websitedepartment', $emailto ) ) {
			$retstr = 'Orchard Recovery Website Administrator';
		} elseif ( 0 === strcmp( 'privacydepartment', $emailto ) ) {
			$retstr = 'Orchard Recovery Privacy Officer';
		}
	} else {
		$retstr = 'Orchard Recovery Intake Department';
	}

	return $retstr;
}
add_shortcode( 'orc_department', 'orc_department_shortcode' );

function orc_email_id_shortcode( $atts, $content = null ) {
	extract(
		shortcode_atts(
			array(
				'emailto' => '',
			),
			$atts
		)
	);

	// Default to the intake id
	$retstr = get_option( 'orc_options_wpcf7id' );
	if ( strlen( $emailto ) > 0 ) {
		if ( 0 === strcmp( 'communicationsdepartment', $emailto ) ) {
			$retstr = get_option( 'orc_options_wpcf7id_comm' );
		} elseif ( 0 === strcmp( 'hrdepartment', $emailto ) ) {
			$retstr = get_option( 'orc_options_wpcf7id_hr' );
		} elseif ( 0 === strcmp( 'alumnidepartment', $emailto ) ) {
			$retstr = get_option( 'orc_options_wpcf7id_alumni' );
		} elseif ( 0 === strcmp( 'websitedepartment', $emailto ) ) {
			$retstr = get_option( 'orc_options_wpcf7id_website' );
		} elseif ( 0 === strcmp( 'privacydepartment', $emailto ) ) {
			$retstr = get_option( 'orc_options_wpcf7id_privacy' );
		}
	}

	return $retstr;
}
add_shortcode( 'orc_email_id', 'orc_email_id_shortcode' );

function orc_date_to_years_shortcode( $atts, $content = null ) {
	date_default_timezone_set( get_option( 'timezone_string' ) );
	$today = date( 'Y-m-d' );

	extract(
		shortcode_atts(
			array(
				'year'  => date( 'Y' ),
				'month' => date( 'm' ),
				'day'   => date( 'd' ),
			),
			$atts
		)
	);

	$todaydt = new DateTime( $today );
	$datedt  = new DateTime( $year . '-' . $month . '-' . $day );
	$diff    = $datedt->diff( $todaydt );

	return $diff->y . ' years';

}
add_shortcode( 'orc_date_to_years', 'orc_date_to_years_shortcode' );

function orc_post_date_shortcode( $atts = null, $content = null ) {
	return get_the_date();
}
add_shortcode( 'orc_post_date', 'orc_post_date_shortcode' );

