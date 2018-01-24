<?php

use yii\helpers\Html;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\ConsumablesClinic */
?>
<div class="consumables-clinic-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
<?php AppAsset::addScript($this, '@web/public/js/lib/common.js') ?>