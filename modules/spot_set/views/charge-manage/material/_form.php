<?php

use app\modules\spot_set\models\Material;
use app\modules\spot\models\Tag;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\Material */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
    $attributeLabels = $model->attributeLabels();
?>


<div class="material-form col-md-8">

    <?php $form = ActiveForm ::begin(); ?>

    <div class="row">
        <div class='col-md-6'>
            <?= $form -> field($model, 'name') -> textInput(['maxlength' => true])->label($attributeLabels['name'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'product_name') -> textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class='col-md-6'>
            <?= $form -> field($model, 'en_name') -> textInput(['maxlength' => true]) ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'type') -> dropDownList(Material::$typeOption,['prompt' => '请选择'])->label($attributeLabels['type'].'<span class = "label-required">*</span>') ?>
        </div>
    </div>

    <?= $form->field($model, 'attribute')->radioList(Material::$attributeOption)
        ->label($attributeLabels['attribute'].'<span class = "label-required">*</span>')
    ?>

    <div class="row">
        <div class='col-md-6'>
            <?= $form -> field($model, 'specification') -> textInput(['maxlength' => true])->label($attributeLabels['specification'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'unit') ->textInput()->label($attributeLabels['unit'].'<span class = "label-required">*</span>') ?>
        </div>
    </div>

    <div class="row">
        <div class='col-md-6'>
            <?= $form -> field($model, 'price') -> textInput(['maxlength' => true])->label($attributeLabels['price'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'default_price') -> textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class='col-md-6'>
            <?= $form -> field($model, 'meta') -> textInput(['maxlength' => true]) ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'manufactor') -> textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?= $form -> field($model, 'remark') -> textInput(['maxlength' => true]) ?>

    <div class="row" style="<?= $model->attribute == 1 ? 'display:none' : '' ?>" id="warning-container">
        <div class='col-md-6'>
            <?= $form -> field($model, 'warning_num') -> textInput(['maxlength' => true,'disabled' => true,'value' => 10])->label($attributeLabels['warning_num'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'warning_day') -> textInput(['maxlength' => true,'disabled' => true,'value' => 180])->label($attributeLabels['warning_day'].'<span class = "label-required">*</span>') ?>
        </div>
    </div>

    <div class="row">
        <div class='col-md-6'>
            <?= $form -> field($model, 'status') -> dropDownList(Material::$getStatus,['prompt' => '请选择'])->label($attributeLabels['status'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'tag_id') -> dropDownList(array_column(Tag::getTagList(['id','name'],['type' => 1]), 'name','id'), ['prompt' => '请选择充值卡折扣标签']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html ::a('取消', Url::to(['@spot_setChargeManageMaterialIndex']), ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html ::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm ::end(); ?>

</div>