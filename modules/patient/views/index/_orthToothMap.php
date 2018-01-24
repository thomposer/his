<?php

use yii\helpers\Html;

$leftTop = '';
$rightTop = '';
$leftBottom = '';
$rightBottom = '';
$infoArr = explode(',', $position);
if (is_array($infoArr) && count($infoArr) == 4) {
    $leftTop = $infoArr[0];
    $rightTop = $infoArr[1];
    $rightBottom = $infoArr[2];
    $leftBottom = $infoArr[3];
}
?>
<div class="tooth-map-content">
    <div class="dental-position">
        <div class="dental-left-top"><?= $leftTop ?></div>
        <div class="dental-right-top"><?= $rightTop ?></div>
        <div class="dental-left-bottom"><?= $leftBottom ?></div>
        <div class="dental-right-bottom"><?= $rightBottom ?></div>
    </div>
</div>
