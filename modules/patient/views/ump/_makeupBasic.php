<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use dosamigos\datetimepicker\DateTimePicker;
use app\modules\patient\models\PatientRecord;
use app\modules\spot_set\models\SpotType;
//use kartik\datetime\DateTimePicker;

$labels = $model->attributeLabels();
?>
<?php $form = ActiveForm::begin(['id' => 'makeup_record']); ?>

<div class="row">
    <div class="col-md-6">
        <?php
        echo $form->field($model, 'diagnosis_time')->widget(
               DateTimePicker::className(), [
                    'inline' => false,
                    'language' => 'zh-CN',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd hh:ii',
                        'minuteStep'=>1,
                    ]
                        ]
        )->label($labels['diagnosis_time'] . '<span class = "label-required">*</span>')
        ?>
        <?php // echo  $form->field($model, 'diagnosis_time')->textInput(['maxlength' => 30])->label($labels['diagnosis_time'] . '<span class = "label-required">*</span>') ?>
    </div>
    <?php if ($makeupType == 1): ?>
        <div class="col-md-6">
            <?php if ($model->isEdit == 2): ?>
                <?= $form->field($model, 'appointment_time')->dropDownList(ArrayHelper::map($appointmentTimeList, 'record_id', 'time'), ['prompt' => '请选择'])->label($labels['appointment_time'] . '<span class = "label-required">*</span>') ?>
            <?php else: ?>
                <?= $form->field($model, 'appointment_time')->textInput(['maxlength' => true,'readonly'=>'readonly'])->label($labels['appointment_time'] . '<span class = "label-required">*</span>') ?>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!--<div class="col-md-6">
            <?/*= $form->field($model, 'appointment_type')->dropDownList(ArrayHelper::map(SpotType::getSpotType(),'id','name'), ['prompt' => '请选择'])->label($labels['appointment_type'] . '<span class = "label-required">*</span>') */?>
        </div>-->
    <?php endif; ?>
</div>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'doctor_id')->dropDownList(ArrayHelper::map($doctorList, 'doctor_id', 'doctor_name'), ['prompt' => '请选择'])->label($labels['doctor_id'] . '<span class = "label-required">*</span>') ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'room_id')->dropDownList(ArrayHelper::map($roomList, 'room_id', 'room_name'), ['prompt' => '请选择'])->label($labels['room_id'] . '<span class = "label-required">*</span>') ?>
    </div>
</div>


<?php ActiveForm::end(); ?>