<?php

/**
 * Template Name: Présentations financières
 */

defined('ABSPATH') || exit;

get_header();
?>

<div id="content" class="content" role="main">

	<?php get_template_part('template-parts/banner-list'); ?>
	<?php get_template_part('template-parts/list-pages-template'); ?>
	<div class="wrap-list-pdf">
		<div class="bloc-left">

			<div class="list-presse">
				<?php
				$args = array(
					'post_type' => 'document',
					'orderby' => 'date',
					'post_status' => 'publish',
					'order' => 'DESC',
					'posts_per_page' => -1,
					'meta_query' => array(
						array(
							'key' => 'document_type',
							'value' => '29',
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

						if (!isset($posts_by_year_month[$year])) {
							$posts_by_year_month[$year] = [];
							// var_dump($posts_by_year_month);
						}

						if (!isset($posts_by_year_month[$year][$month])) {
							$posts_by_year_month[$year][$month] = [];
						}

						$posts_by_year_month[$year][$month][] = get_the_ID();
					}
					wp_reset_postdata();
				}

				// var_dump($posts_by_year_month);

				$years = array_keys($posts_by_year_month);
				rsort($years);

				foreach ($years as $year) {
					$months = array_keys($posts_by_year_month[$year]);
					sort($months);

					echo "<p class='year'>{$year}</p>";
					echo "<div class='list-pdf 123'>";
					foreach ($months as $month) {
					
						
						foreach ($posts_by_year_month[$year][$month] as $post_id) {
							$post = get_post($post_id);
							setup_postdata($post);
							$date = get_field('date_effective');
							$doc_description = get_field('doc_description');
							$doc_document = get_field('doc_document');
				?>
							<div class="title-item-month">
								<span class="icon"></span>
								<?php if ($doc_document) { ?>
									<a href="<?= $doc_document['url'] ?>"> <?= get_the_title($post_id) ?></a>
								<?php } else {
								?>
									<a href="<?= get_permalink($post_id) ?>"> <?= get_the_title($post_id) ?></a>
								<?php
								} ?>
								<span class="date">Publié le <?= $date ?></span>
							</div>
				<?php

						}
						wp_reset_postdata();
						
					}
					echo "</div>";
				}
				?>
			</div>
		</div>


		<div class="right">
			<div class="form-item-document-year">
				<form class="views-exposed-form" data-drupal-selector="views-exposed-form-document-list-doc-year-month"
					action="/fr/media-center/communiques-de-presse" method="get"
					id="views-exposed-form-document-list-doc-year-month" accept-charset="UTF-8" data-once="exposed-form">
					<div
						class="js-form-item form-item js-form-type-select form-item-date-effective-filter js-form-item-date-effective-filter form-no-label">
						<select class="gtt-document-date-effective-filter form-select"
							data-drupal-selector="edit-date-effective-filter" id="edit-date-effective-filter--GF_eX9qPyOU"
							name="date_effective_filter">
							<option value="All" selected="selected">- Tout -</option>
							<option value="2024">2024</option>
							<option value="2023">2023</option>
							<option value="2022">2022</option>
							<option value="2021">2021</option>
							<option value="2020">2020</option>
							<option value="2019">2019</option>
							<option value="2018">2018</option>
							<option value="2017">2017</option>
							<option value="2016">2016</option>
							<option value="2015">2015</option>
							<option value="2014">2014</option>
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
				'action': 'filter_financieres',
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