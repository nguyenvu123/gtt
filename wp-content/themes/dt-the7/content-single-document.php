<?php
$doc  =  get_field('doc_description');
if ($doc) {
?>
    <div class="doc-description">
        <?= $doc ?>
    </div>
<?php
}
?>