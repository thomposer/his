<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\spot\models\InspectItem;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\InspectItem */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>

<div class="inspect-item-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>

    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'item_name')->textInput(['maxlength' => true])->label($attribute['item_name'] . '<span class = "label-required">*</span>') ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'english_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class = 'row'>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'unit')->textInput(['maxlength' => true]) ?>
        </div>
        <div class = 'col-md-6'>
            <?= $form->field($model, 'status')->dropDownList(InspectItem::$getStatus) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::a('取消', ['item-index'], ['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
