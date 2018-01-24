<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\NursingRecordTemplate */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>

<div class="nursing-record-template-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nursing_item')->textInput(['maxlength' => true,'placeholder'=>'请输入护理项'])->label($attribute['nursing_item'].'<span class = "label-required">*</span>') ?>

    <?= $form->field($model, 'content_template')->textarea(['rows' => 6,'placeholder'=>'请填写内容模板框架'])->label($attribute['content_template'].'<span class = "label-required">*</span>') ?>

    <div class="form-group">
        <?= Html::a('取消', ['nursing-index'], ['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
