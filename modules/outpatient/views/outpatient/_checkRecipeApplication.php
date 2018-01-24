<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;

$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/check/common.css');
?>

<div class="check-recipe-application-print">
    <div class = 'application-bg'>
        <h5 class = 'title'><?= $title != ''?$title : '医嘱项' ?></h5>
    </div>
    <?php
    $form = ActiveForm::begin([
                'options' => ['class' => 'form-horizontal common']
    ]);
    ?>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'name')->checkboxList(ArrayHelper::map($dataProvider, 'id', 'displayName'))->label(false); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>