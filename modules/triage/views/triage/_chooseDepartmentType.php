<?php

use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
use app\assets\AppAsset;
use yii\helpers\Html;
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/triage/triage.css');
?>

<div>
    <?php
    $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal common'],
    ]);
    ?>
<!--    <hr style="height:0.5px;border:none;border-top:1px solid #CACFD8;margin-top:-3px;" />-->
    <div class = 'row filemanager'>
        <div class = 'col-md-1 text-right second-department-report'>
            科室<span class = "label-required">*</span>
        </div>
        <div class = 'col-md-10 report-choose-department'>
            <?= $form->field($reportModel, 'second_department_id')->radioList(ArrayHelper::map($departmentData, 'id', 'name'))->label(false); ?>&nbsp;
        </div>
        <div class = 'col-md-2 text-right type-report'>
            服务类型<span class = "label-required">*</span>
        </div>
        <div class = 'col-md-10 report-choose-department'>
            <?= $form->field($reportModel, 'type')->radioList(ArrayHelper::map($typeData, 'id', 'type'))->label(false); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>