<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\PackageCardService;

$attributes = $model->attributeLabels();
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\PackageCardService */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="package-card-service-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => '请输入服务名称（不超过20个字）'])->label('服务类型名称' . '<span class = "label-required">*</span>') ?>

    <?= $form->field($model, 'status')->dropDownList(PackageCardService::$getStatus,['prompt' => '请选择'])->label($attributes['status'] . '<span class = "label-required">*</span>') ?>

    <?php ActiveForm::end(); ?>
    
</div>
