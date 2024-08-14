<?php
$year_filter =  isset($_POST['year']) ? sanitize_text_field($_POST['year']) : '';

$args = array(
    'post_type'      => 'document',
    'orderby'        => 'date',
    'post_status'    => 'publish',
    'order'          => 'DESC',
    'posts_per_page' => -1,
    'meta_query'     => array(
        'relation' => 'OR',
        array(
            'key'     => 'document_type',
            'value'   => '84',
            'compare' => 'LIKE',
        ),
        array(
            'key'     => 'document_type',
            'value'   => '46',
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
        $document = get_field('document_type');

        if ($year_filter === 'All' || $year === $year_filter) {

            if (!isset($posts_by_year_month[$year])) {
                $posts_by_year_month[$year] = [];
            }

            if (!isset($posts_by_year_month[$year][$document[0]])) {
                $posts_by_year_month[$year][$document[0]] = [];
            }

            $posts_by_year_month[$year][$document[0]][] = get_the_ID();
        }
    }
    wp_reset_postdata();
}

$years = array_keys($posts_by_year_month);
rsort($years);

foreach ($years as $year) {
    $types = array_keys($posts_by_year_month[$year]);
    sort($types);

    echo "<p class='year'>{$year}</p>";

    foreach ($types as $type) {
        $typeTring = convertType($type);

        ?>
        <p><?=$typeTring ?></p>
        <?php
        foreach ($posts_by_year_month[$year][$document[0]] as $post_id) {
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
    }
}

if (empty($posts_by_year_month)) {
    echo "<p>No posts found.</p>";
}
?>