<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;

$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/check/common.css');
?>

<div class="inspect-reback">
    <?php
    $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal common']
    ]);
    ?>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'backInspectId')->checkboxList(ArrayHelper::map($inspectRecordList, 'id', 'name'))->label(false); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>


