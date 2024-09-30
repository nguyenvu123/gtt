<?php

/**
 * Template Name: Assemblee
 */

get_header();

$thumb = get_the_post_thumbnail_url(get_the_ID());
$current_language = get_locale();
$date_format = ($current_language == 'fr_FR') ? 'Y-m-d' : 'Y-m-d';
$ajax_filter = 'filter_generale';

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
            'terms' => 843,
        ),
    ),
);

$query = new WP_Query($args);

include 'list-page-group-by-taxonomy.php';
