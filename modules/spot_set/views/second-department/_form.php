<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\spot_set\models\SecondDepartment;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\SecondDepartment */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="second-department-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label($attributeLabels['name'].'<span class = "label-required">*</span>') ?>
    <div class = 'row'>
    <div class = 'col-sm-6'>
    <?= $form->field($model, 'appointment_status')->dropDownList(SecondDepartment::$getAppointmentStatus,['prompt' => '请选择'])->label($attributeLabels['appointment_status'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-sm-6'>
    <?= $form->field($model, 'parent_id')->dropDownList(ArrayHelper::map($onceDepartmentInfo, 'id', 'name'),['prompt' => '请选择'])->label($attributeLabels['parent_id'].'<span class = "label-required">*</span>') ?>
    </div>
    </div>
    <div class = 'row'>
    <div class = 'col-sm-6'>
    <?= $form->field($model, 'status')->dropDownList(SecondDepartment::$getStatus,['prompt' => '请选择'])->label($attributeLabels['status'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-sm-6'>
    <?= $form->field($model, 'room_type')->dropDownList(SecondDepartment::$getRoomType,['prompt' => '请选择']) ?>
    </div>
    </div>
    <div class="form-group">
        <?= Html::a('取消',['index'],['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
