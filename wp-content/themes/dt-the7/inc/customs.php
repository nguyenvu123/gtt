<?php

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
