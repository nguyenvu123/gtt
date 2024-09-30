<?php

/**
 * Configuration
 */

defined('ABSPATH') || exit;

get_header();
include 'pdf-mappings.php';
$thumb = get_the_post_thumbnail_url(get_the_ID());
$current_language = get_locale();
$slug = get_page_template_slug();
$posts_by_year_month = [];
if ($query->have_posts()) {
  while ($query->have_posts()) {
    $query->the_post();
    global $post;

    $year = get_field('doc_published_date');
    $date = new DateTime($year);
    $year = $date->format('Y');
    $month = $date->format('m');

    if (!isset($posts_by_year_month[$year])) {
        $posts_by_year_month[$year] = [];
    }

    if (!isset($posts_by_year_month[$year][$month])) {
        $posts_by_year_month[$year][$month] = [];
    }

    $posts_by_year_month[$year][$month][] = get_the_ID();
  }
  wp_reset_postdata();
}

$years = array_keys($posts_by_year_month);
rsort($years);
?>

<div id="content" class="content" role="main">
  <?php get_template_part('template-parts/banner-list');  ?>
  
  <div class="wrap-list-pdf">
    <div class="bloc-left">
      <div class="list-presse">
        <?php 
        $locale = get_locale();
          foreach ($years as $year) :
            $months = array_keys($posts_by_year_month[$year]);
            rsort($months);

            echo "<p class='year'>{$year}</p>";
            foreach ($months as $month) :
                if($locale == 'en_GB') {
                    $month_string = convertMonthNumberToEn($month);
                }else {
                    $month_string = convertMonthNumberToFrench($month);
                }
                if($slug !='pages/presentations-financieres.php') {
                    echo "<h3 class='month'>{$month_string}</h3>";
                }
                
                echo "<div class='list-pdf'>";
                foreach ($posts_by_year_month[$year][$month] as $post_id) :
                    $post = get_post($post_id);
                    setup_postdata($post);
                    $date = get_field('doc_published_date');
                    if(!empty($date)) {
                        $date = new Datetime($date);
                    }
                    $doc_description =  get_field('doc_description');

		    /**
		     * Manage Document
		     */
		    $migrated_document_nid = get_post_meta( $post_id, '_fgd2wp_old_node_id', true);
			
		    if(!empty($migrated_document_nid)) {
                        if($current_language == 'fr_FR' && isset($pdfs_mapping[$migrated_document_nid]['fr_FR'])) {
                            $document_url = $pdfs_mapping[$migrated_document_nid]['fr_FR'];
                        } elseif(isset($pdfs_mapping[$migrated_document_nid]['en'])) {
                            $document_url = $pdfs_mapping[$migrated_document_nid]['en'];
                        }
                        if(!empty($document_url)) {
                            $document_url = '/wp-content/uploads/pdfs/' . $document_url;
                        }
                    } else {
                        $doc_document = get_field('doc_document');
                        $document_url = $doc_document['url'];
                    }

?>
                    <div class="title-item-month">
                        <span class="icon"></span>
                        <?php if (!empty($document_url)) { ?>
                            <a href="<?= $document_url ?>"> <?= get_the_title($post_id) ?></a>
                        <?php } else {
                        ?>
                            <a href="<?= get_permalink($post_id) ?>"> <?= get_the_title($post_id) ?></a>
                        <?php
                        } ?>
                        <?php if(!empty($date)) : ?>
                            <span class="date"><?php echo __('Published on') . ' ' . $date->format($date_format) ?></span>
                        <?php endif; ?>
                    </div>
                  <?php
                            
                endforeach;
                wp_reset_postdata();
                echo "</div>";
            endforeach;
          endforeach;
                  ?>
      </div>
    </div>

    <div class="right">
        <div class="form-item-document-year">
            <form class="views-exposed-form" data-drupal-selector="views-exposed-form-document-list-doc-year-month" action="/fr/media-center/communiques-de-presse" method="get" id="views-exposed-form-document-list-doc-year-month" accept-charset="UTF-8" data-once="exposed-form">
                <div class="js-form-item form-item js-form-type-select form-item-date-effective-filter js-form-item-date-effective-filter form-no-label">
                    <select class="gtt-document-date-effective-filter form-select" data-drupal-selector="edit-date-effective-filter" id="edit-date-effective-filter--GF_eX9qPyOU" name="date_effective_filter">
                        <option value="All" selected="selected"><?php echo __('- All -') ?></option>
                        <?php foreach ($years as $year) : ?>
                            <option value="<?php print $year ?>"><?php print $year ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>

<script>
    // A $( document ).ready() block.
    jQuery(document).ready(function() {
        jQuery('.gtt-document-date-effective-filter').on('change', function() {

            var selectedValue = jQuery(this).val();

            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
                'action': '<?php print $ajax_filter ?>',
                'year': selectedValue,
            };
            jQuery.post(ajaxurl, data, function(response) {

                if (response != '') {
                    jQuery('.list-presse').html(response);
                }
            });

        })
    });
</script>
