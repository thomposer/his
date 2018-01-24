<?php

use app\modules\spot_set\models\Material;
use app\modules\spot\models\Tag;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\Consumables;
use yii\helpers\ArrayHelper;
$attributeLabels = $model->attributeLabels();

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\Material */
/* @var $form yii\widgets\ActiveForm */
$this->registerCss('
    #consumables-unionspotid label {
        width:33%;
    }
');
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
            <?= $form -> field($model, 'type') -> dropDownList(Consumables::$getType)->label($attributeLabels['type'].'<span class = "label-required">*</span>') ?>
        </div>
    </div>
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
            <?= $form -> field($model, 'meta') -> textInput(['maxlength' => true]) ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'manufactor') -> textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?= $form -> field($model, 'remark') -> textInput(['maxlength' => true]) ?>
	<div class="row">
            <div class="col-md-12">
                <?= $form->field($model,'unionSpotId')->checkboxList(ArrayHelper::map($spotList,'id','spot_name'))->label($attributeLabels['unionSpotId'].'<span class = "label-required">*</span>') ?>
            </div>
    </div>
    <div class="row">
        <div class='col-md-6'>
            <?= $form -> field($model, 'status') -> dropDownList(Consumables::$getStatus,['prompt' => '请选择'])->label($attributeLabels['status'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class='col-md-6'>
            <?= $form -> field($model, 'tag_id') -> dropDownList(array_column(Tag::getTagList(['id','name'],['type' => 1]), 'name','id'), ['prompt' => '请选择充值卡折扣标签']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html ::a('取消', ['consumables-index'], ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html ::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm ::end(); ?>

</div>