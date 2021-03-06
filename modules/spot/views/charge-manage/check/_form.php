<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\spot\models\CheckList;
use app\modules\spot\models\Tag;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CheckList */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>

<div class="check-list-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>
    <div class = 'row'>
    <div class = 'col-md-6'>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label($attribute['name'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-md-6'>
    <?= $form->field($model, 'unit')->textInput(['maxlength' => true]) ?>
    </div>
    </div>
    <div class = 'row'>
    <div class = 'col-md-6'>   
    <?= $form->field($model, 'meta')->textInput(['maxlength' => true]) ?>
    </div>
    <div class = 'col-md-6'>
    <?= $form->field($model, 'international_code')->textInput(['maxlength' => true]) ?>
    </div>
    </div>
    <div class = 'row'>
    <div class = 'col-md-6'>
    <?= $form->field($model, 'remark')->textInput() ?>
    </div>
    <div class = 'col-md-6'>
    <?= $form->field($model, 'tag_id')->dropDownList(array_column(Tag::getTagList(['id','name'],['type' => 1]), 'name','id'),['prompt' => '请选择充值卡折扣标签']) ?>
    </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model,'unionSpotId')->checkboxList(ArrayHelper::map($spotList,'id','spot_name'))->label($attribute['unionSpotId'].'<span class = "label-required">*</span>') ?>
        </div>
    </div>

    <div class = 'row'>
    <div class = 'col-md-6'>
    <?= $form->field($model, 'status')->dropDownList(CheckList::$getStatus) ?>
    </div>
    </div>

    <div class="form-group">
        <?= Html::a('取消',['check-index'],['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
