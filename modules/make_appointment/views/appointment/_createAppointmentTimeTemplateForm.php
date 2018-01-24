<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use rkit\yii2\plugins\ajaxform\Asset;
use dosamigos\datetimepicker\DateTimePickerAsset;
/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\AppointmentTimeTemplate */
/* @var $form yii\widgets\ActiveForm */
//\dosamigos\datetimepicker\DateTimePickerAsset::register($this);
Asset::register($this);
DateTimePickerAsset::register($this)->js[] = 'js/locales/bootstrap-datetimepicker.zh-CN.js';
$attribute = $model->attributeLabels();
?>



<div class="appointment-time-template-form col-md-8">

    <?php $form = ActiveForm::begin(
        [
            'id' => 'template-form',
        ]
    ); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label($attribute['name'].'<span class = "label-required">*</span>') ?>
    <?= $form->field($model,'appointment_times')->textInput(['type' => 'hidden'])->label(false) ?>
    <div id = 'setScheduleContent' class="modal-content-details">

    </div>


    <div class="form-group">
        <?= Html::a('取消',['@appointmentTimeTemplate'],['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form ajaxform-btn btn-appiontment-save']) ?>
    </div>



    <?php ActiveForm::end(); ?>

</div>