<?php

/**
 * Template Name: Communiqués de presse
 */

defined('ABSPATH') || exit;

get_header();

$thumb = get_the_post_thumbnail_url(get_the_ID());
$current_language = get_locale();
$date_format = ($current_language == 'fr_FR') ? 'Y-m-d - H:i' : 'Y-m-d - H:i';
$ajax_filter = 'filter_presse';


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
            'terms' => 832,
        ),
    ),
);

$query = new WP_Query($args);

include 'list-page.php';
