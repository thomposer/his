<?php

use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Html;

$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/check/common.css');
?>

<div class="check-application-print">
    <div class = 'application-bg'>
        <h5 class = 'title'>影像学检查项目</h5>
    </div>
    <?php
    $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal common']
    ]);
    ?>
    <div class = 'row'>
        <div class = 'col-md-12'>
            <?= $form->field($model, 'check')->checkboxList(ArrayHelper::map($checkList, 'id', 'name'))->label(false); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
