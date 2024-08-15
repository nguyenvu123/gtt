<?php

function athena_scripts()
{
	wp_enqueue_style('custom-style-custom', get_stylesheet_directory_uri() . '/css/customs.css', array(), null);
    wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/js/customs.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'athena_scripts');

function convertMonthNumberToFrench($monthNumber) {
    $monthsInFrench = array(
        1  => 'Janvier',
        2  => 'Février',
        3  => 'Mars',
        4  => 'Avril',
        5  => 'Mai',
        6  => 'Juin',
        7  => 'Juillet',
        8  => 'Août',
        9  => 'Septembre',
        10 => 'Octobre',
        11 => 'Novembre',
        12 => 'Décembre'
    );
    $monthNumber = (int)$monthNumber;
    return $monthsInFrench[$monthNumber];
}

function convertType($typeNumber) {
    $monthsInFrench = array(
        84 => 'Assemblée Générale Extraordinaire du 14 Novembre 2019',
        50 => 'Communiqué de presse généraux',
        80 => 'Autres communiqués de presse financiers',
        83 => 'Journée investisseurs',
        73 => 'Communiqués d’activité du premier trimestre',
        75 => 'Communiqués d’activité du troisième trimestre',
        77 => 'Communiqués de résultats',
        62 => 'Résultats annuels',
        65 => 'Activité des neuf premiers mois',
        67 => 'Résultats semestriels',
        69 => 'Activité du premier trimestre',
        24 => 'Communiqués de presse financiers',
        29 => 'Présentations financières',
        15 => 'Rapports financiers',
        46 => 'Assemblée générale',
        48 => 'Doc financier - home investisseurs',
        52 => 'Publications techniques',
        44 => 'Doc footer footer FR',
        42 => 'Autres informations',
        23 => 'Webcast',
    );

    return $monthsInFrench[$typeNumber];
}

//add theme setting acf
if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title' => 'Theme General Settings',
        'menu_title' => 'Theme Settings',
        'menu_slug'  => 'theme-general-settings',
        'capability' => 'edit_posts',
        'redirect'   => false
    ));
}


function add_custom_body_class($classes) {
    // Get the current page template
    $template = get_post_meta(get_the_ID(), '_wp_page_template', true);

    // Check if the template is 'pages/essai-gratuit.php'
    if ($template == 'pages/assemblee-generale.php' || $template == 'pages/pack-investisseurs.php' || $template == 'pages/presentations-financieres.php' || $template == 'pages/presse.php' || $template == 'pages/publications-techniques.php') {
        // Add custom class 'essai-gratuit' to the body classes array
        $classes[] = 'page-list-pdf';
    }

    return $classes;
}
add_filter('body_class', 'add_custom_body_class');