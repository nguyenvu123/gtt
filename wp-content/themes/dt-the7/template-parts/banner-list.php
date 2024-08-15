<?php
$thumb = get_the_post_thumbnail_url(get_the_ID());
?>

<div class="banner">
    <div class="brc">
        <ul>
            <li><a href="<?= get_home_url() ?>"><?php _e('Home')  ?> </a></li>
            <li><?= get_the_title() ?></li>
        </ul>
    </div>
    <div class="title">
        <h1><?= get_the_title() ?></h1>
    </div>
    <img src="<?= $thumb ?>" alt="<?= get_the_title() ?>">
</div>