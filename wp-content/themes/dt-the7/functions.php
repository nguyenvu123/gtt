<?php
/**
 * The7 theme.
 *
 * @since   1.0.0
 *
 * @package The7
 */

defined( 'ABSPATH' ) || exit;
define('APP_PATH',dirname(__FILE__));

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since 1.0.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1200; /* pixels */
}

/***********************
RESOLVE ISSUE WITH ACF DATE FIELDS NOT RETURNING INFORMATION USING THE WP TIMEZONE
***********************/
/*add_filter('date_i18n', function ($date, $format, $timestamp, $gmt) { 
	if ( is_admin() )
		return $date;
	return wp_date($format, $timestamp); 
}, 99, 4);*/

/**
 * Initialize theme.
 *
 * @since 1.0.0
 */
require trailingslashit( get_template_directory() ) . 'inc/init.php';

include APP_PATH.'/inc/customs.php';
include APP_PATH.'/inc/ajax.php';