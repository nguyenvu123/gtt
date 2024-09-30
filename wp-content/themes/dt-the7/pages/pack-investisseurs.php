<?php

/**
 * Template Name: Pack investisseurs
 */

$thumb = get_the_post_thumbnail_url(get_the_ID());
$current_language = get_locale();
$date_format = ($current_language == 'fr_FR') ? 'Y-m-d - H:i' : 'Y-m-d - H:i';
$ajax_filter = 'filter_investisseurs';

$args = array(
	'post_type'      => 'document',
	'orderby'        => 'date',
	'post_status'    => 'publish',
	'order'          => 'DESC',
	'posts_per_page' => -1,
	'tax_query'     => array(
			'relation' => 'OR',
			array(
					'taxonomy'     => 'taxo_document',
					'field'   => 'term_id',
					'terms' => 831,
			),
			array(
					'taxonomy'     => 'taxo_document',
					'field'   => 'term_id',
					'terms' => 842,
			),
			array(
				'taxonomy'     => 'taxo_document',
				'field'   => 'term_id',
				'terms' => 836,
		),
		array(
				'taxonomy'     => 'taxo_document',
				'field'   => 'term_id',
				'terms' => 834,
		),
		array(
			'taxonomy'     => 'taxo_document',
			'field'   => 'term_id',
			'terms' => 27,
		),
	),
);

$query = new WP_Query($args);

include 'list-page-group-by-taxonomy.php';
