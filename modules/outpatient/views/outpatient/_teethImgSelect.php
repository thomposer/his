<?php

use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\outpatient\models\DentalHistoryRelation;
$baseUrl = Yii::$app->request->baseUrl;
?>
<?php $this->beginBlock('renderCss') ?>
<?php AppAsset::addCss($this, '@web/public/css/outpatient/index.css') ?>
<?php $this->endBlock() ?>
<div class="teeth-print">
    <div class = 'row'>
        <div class = 'col-md-12 teeth-print-list' data-id="<?= $recordId ?>">
            <?php foreach($hasMarkTypeList as $type ):?>
            <label class="teeth-select-label">
                <input class="teeth-check" type="checkbox" name="hasMarkTypeList[]" checked>
                <span class="teeth-check-text"><?php echo DentalHistoryRelation::$getType[$type['type']];?></span>
            </label>
            <?php endforeach;?>
        </div>
    </div>
</div>