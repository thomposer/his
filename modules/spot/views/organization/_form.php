<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Organization */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="organization-form col-md-12">

    <?php $form = ActiveForm::begin(); ?>
    <div class = 'row'>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'spot_name')->textInput(['maxlength' => true])->label($attributeLabels['spot_name'].'<span class = "label-required">*</span>') ?>
    </div>
     <?php if($model->isNewRecord): ?>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'spot')->textInput(['maxlength' => true])->label($attributeLabels['spot'].'<span class = "label-required">*</span>') ?>
    </div>
    <?php endif;?>
    </div>
    <div class = 'row'>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'contact_iphone')->textInput(['maxlength' => true])->label($attributeLabels['contact_iphone'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'contact_name')->textInput(['maxlength' => true])->label($attributeLabels['contact_name'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-sm-4'>
    <?= $form->field($model, 'contact_email')->textInput(['maxlength' => true])->label($attributeLabels['contact_email'].'<span class = "label-required">*</span>') ?>
    </div>
    </div>
    <div class = 'row'>
    <div class = 'col-sm-4'>
      <?= $form->field($model, 'address')->textInput(['maxlength' => true,'data-toggle' => 'city-picker']) ?>
    </div>
    <div class = 'col-sm-4'>
      <?= $form->field($model, 'detail_address')->textInput(['maxlength' => true]) ?>
    </div>
    <div class = 'col-sm-4'>
      <?= $form->field($model, 'template')->dropDownList(ArrayHelper::map($templateList, 'spot', 'spot_name'),['class' => 'form-control select2','style' => 'width:100%']) ?>
    </div>
    </div>
    <div class="form-group">
        <?= Html::a('取消',['index'],['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
