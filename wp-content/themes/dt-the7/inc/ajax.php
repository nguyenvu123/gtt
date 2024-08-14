<?php
add_action('wp_ajax_filter_presse', 'filter_presse');
add_action('wp_ajax_nopriv_filter_presse', 'filter_presse');
function filter_presse()
{
    ob_start();
    include(locate_template('template-parts/categorys/presse-item.php', false, false));
    $content = ob_get_clean();
    echo $content;
    die();
}

add_action('wp_ajax_filter_techniques', 'filter_techniques');
add_action('wp_ajax_nopriv_filter_techniques', 'filter_techniques');

function filter_techniques()
{
    ob_start();
    include(locate_template('template-parts/categorys/techniques-item.php', false, false));
    $content = ob_get_clean();
    echo $content;
    die();
}


add_action('wp_ajax_filter_financieres', 'filter_financieres');
add_action('wp_ajax_nopriv_filter_financieres', 'filter_financieres');

function filter_financieres()
{
    ob_start();
    include(locate_template('template-parts/categorys/financieres-item.php', false, false));
    $content = ob_get_clean();
    echo $content;
    die();
}


add_action('wp_ajax_filter_generale', 'filter_generale');
add_action('wp_ajax_nopriv_filter_generale', 'filter_generale');
function filter_generale()
{
    ob_start();
    include(locate_template('template-parts/categorys/generale-item.php', false, false));
    $content = ob_get_clean();
    echo $content;
    die();
}

add_action('wp_ajax_filter_investisseurs', 'filter_investisseurs');
add_action('wp_ajax_nopriv_filter_investisseurs', 'filter_investisseurs');
function filter_investisseurs()
{
    ob_start();
    include(locate_template('template-parts/categorys/investisseurs-item.php', false, false));
    $content = ob_get_clean();
    echo $content;
    die();
}






