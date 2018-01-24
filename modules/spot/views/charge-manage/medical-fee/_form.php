<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\MedicalFee */
/* @var $form yii\widgets\ActiveForm */
$attribute=$model->attributeLabels();
?>

<div class="medical-fee-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>

    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'price')->textInput(['maxlength' => true, 'placeholder'=>'请填写原价金额'])->label($attribute['price'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'remarks')->textInput(['maxlength' => false]) ?>
        </div>
    </div>

    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'note')->textInput(['maxlength' => false,'placeholder'=>'请填写备注内容，例如适用区域（最多30个字）']) ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'status')->dropDownList($model::$getStatus) ?>
        </div>
    </div>
    
    <div class="form-group">
        <?= Html::a('取消',['medical-fee-index'],['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
