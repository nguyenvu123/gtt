<div class="list-page-template">
    <?php
    $presse_link =  get_field('communiques_de_presse', 'option');
    $techniques_link =  get_field('publications_techniques', 'option');
    $financieres_link =  get_field('presentations_financieres', 'option');
    $generale_link = get_field('assemblee_generale', 'option');
    $investisseurs =  get_field('pack_investisseurs', 'option');

    ?>
    <ul>
        <?php if ($presse_link): ?>
            <li class="<?php if (is_page_template('pages/presse.php')) echo 'active'; ?>"><a href="<?= $presse_link['url'] ?>"><?= $presse_link['title']  ?></a></li>
        <?php endif; ?>
        <?php if ($techniques_link): ?>
            <li class="<?php if (is_page_template('pages/publications-techniques.php')) echo 'active'; ?>"><a href="<?= $techniques_link['url'] ?>"><?= $techniques_link['title']  ?></a></li>
        <?php endif; ?>
        <?php if ($financieres_link): ?>
            <li class="<?php if (is_page_template('pages/presentations-financieres.php')) echo 'active'; ?>"><a href="<?= $financieres_link['url'] ?>"><?= $financieres_link['title']  ?></a></li>
        <?php endif; ?>
        <?php if ($generale_link): ?>
            <li class="<?php if (is_page_template('pages/assemblee-generale.php')) echo 'active'; ?>"><a href="<?= $generale_link['url'] ?>"><?= $generale_link['title']  ?></a></li>
        <?php endif; ?>
        <?php if ($investisseurs): ?>
            <li class="<?php if (is_page_template('pack-investisseurs.php')) echo 'active'; ?>"><a href="<?= $investisseurs['url'] ?>"><?= $investisseurs['title']  ?></a></li>
        <?php endif; ?>
    </ul>
</div>