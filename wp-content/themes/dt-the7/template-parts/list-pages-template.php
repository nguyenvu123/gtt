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
            <li><a href="<?= $presse_link['url'] ?>"><?= $presse_link['title']  ?></a></li>
        <?php endif; ?>
        <?php if ($techniques_link): ?>
            <li><a href="<?= $techniques_link['url'] ?>"><?= $techniques_link['title']  ?></a></li>
        <?php endif; ?>
        <?php if ($financieres_link): ?>
            <li><a href="<?= $financieres_link['url'] ?>"><?= $financieres_link['title']  ?></a></li>
        <?php endif; ?>
        <?php if ($generale_link): ?>
            <li><a href="<?= $generale_link['url'] ?>"><?= $generale_link['title']  ?></a></li>
        <?php endif; ?>
        <?php if ($investisseurs): ?>
            <li><a href="<?= $investisseurs['url'] ?>"><?= $investisseurs['title']  ?></a></li>
        <?php endif; ?>
    </ul>
</div>