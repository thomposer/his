<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\make_appointment\models\Appointment;
use yii\helpers\ArrayHelper;
use app\modules\patient\models\PatientRecord;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DatePickerAsset;
use dosamigos\datepicker\DatePickerLanguageAsset;
/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\search\AppointmentSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();

?>

<div class="appointment-search hidden-xs" style="float:right;width: 920px;">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['list','appointment[type]' => 1],
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <div class="search-middle">

            <span class = 'search-default'>筛选：</span>
<!--            <span class="clearfix"></span>-->


        <div>
            <?= $form->field($model, 'username')->textInput(['placeholder' =>$attributeLabels['username'] ]) ?>
            <?= $form->field($model, 'iphone')->textInput(['placeholder' => $attributeLabels['iphone'] ]) ?>
            <?= $form->field($model, 'doctor_id')->dropDownList(ArrayHelper::map($doctorInfo, 'id', 'username'),['prompt' => '请选择医生','class'=>'form-control department-width']) ?>

            <?= $form->field($model, 'appointment_begin_time')->widget(
                DatePicker::className(),[
                'inline' => false,
                'language' => 'zh-CN',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ],
                'options' =>    [
                    'placeholder' => $attributeLabels['appointment_begin_time']
                ],
            ]) ?>
            <span style="float: left;">-</span>
            <?= $form->field($model, 'appointment_end_time')->widget(
                DatePicker::className(),[
                'inline' => false,
                'language' => 'zh-CN',
                'options' =>    [
                    'placeholder' => $attributeLabels['appointment_end_time']
                ],
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ],

            ]) ?>
            <?= $form->field($model, 'type')->dropDownList($spotTypeList?ArrayHelper::map($spotTypeList, 'id', 'name'):[],['prompt' => '服务类型','class'=>'form-control department-width']) ?>
            <?= $form->field($model, 'status')->dropDownList(PatientRecord::$getStatus,['prompt' => '就诊状态']) ?>
            <?= $form->field($model, 'appointment_operator')->dropDownList(ArrayHelper::map($getAppointmentOperator, 'id', 'username'),['prompt' => '预约操作人']) ?>
            <!--            <span class="clearfix"></span>-->
        </div>

    </div>
    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <div class="more">
            <a state='1' class="more-word">更多条件</a>
            <i class="fa fa-caret-down "></i>
        </div>

    </div>


    <?php ActiveForm::end(); ?>

</div>
