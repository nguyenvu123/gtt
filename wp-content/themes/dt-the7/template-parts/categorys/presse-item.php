<?php
$year_filter =  isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';
$args = array(
    'post_type'      => 'document',
    'orderby'        => 'date',
    'post_status'    => 'publish',
    'order'          => 'DESC',
    'posts_per_page' => -1,
    'meta_query'     => array(
        array(
            'key'     => 'document_type',
            'value'   => '50',
            'compare' => 'LIKE',
        ),
    ),
);

$query = new WP_Query($args);

$posts_by_year_month = [];
if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        global $post;

        $year = get_field('doc_published_date');
        $date = new DateTime($year);
        $year = $date->format('Y');
        $month = $date->format('m');

        if ($year_filter === 'All' || $year === $year_filter) {

            if (!isset($posts_by_year_month[$year])) {
                $posts_by_year_month[$year] = [];
            }

            if (!isset($posts_by_year_month[$year][$month])) {
                $posts_by_year_month[$year][$month] = [];
            }

            $posts_by_year_month[$year][$month][] = get_the_ID();
        }
    }
    wp_reset_postdata();
}

$years = array_keys($posts_by_year_month);
rsort($years);

foreach ($years as $year) {
    $months = array_keys($posts_by_year_month[$year]);
    sort($months);

    echo "<p class='year'>{$year}</p>";

    foreach ($months as $month) {
        $month_string = convertMonthNumberToFrench($month);

        echo "<h3 class='month'>{$month_string}</h3>";
        echo "<div class='list-pdf'>";
        foreach ($posts_by_year_month[$year][$month] as $post_id) {
            $post = get_post($post_id);
            $date =  get_field('date_effective');
            $doc_description =  get_field('doc_description');
            $doc_document = get_field('doc_document');
?>

            <div class="title-item-month">
                <span class="icon"></span>
                <?php if ($doc_document) { ?>
                    <a href="<?= $doc_document['url']  ?>"> <?= get_the_title($post_id) ?></a>
                <?php } else {
                ?>
                    <a href="<?= get_permalink($post_id) ?>"> <?= get_the_title($post_id) ?></a>
                <?php
                } ?>
                <span>Publié le <?= $date ?></span>
            </div>
<?php
        }
        echo "</div>";
    }
}

if (empty($posts_by_year_month)) {
    echo "<p>No posts found.</p>";
}
?>