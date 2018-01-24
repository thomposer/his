<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\ChildCareTemplate;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CaseTemplate */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>

<div class="case-template-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label($attribute['name'] . '<span class = "label-required">*</span>') ?>

    <?php if (!isset($hidden)): ?>
        <?= $form->field($model, 'type')->radioList(ChildCareTemplate::$getType, ['class' => 'radio-inline', 'itemOptions' => ['labelOptions' => ['class' => 'radio-inline']]])->label($attribute['type'] . '<span class = "label-required">*</span>') ?>
    <?php endif; ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 30,'maxlength'=>true,'placeholder'=>'请填写指导意见内容框架，不超过1000字']) ?>

    <div class="form-group">
        <?= Html::a('取消', ['child-index'], ['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
