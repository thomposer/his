<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientSubmeter;
use yii\widgets\Pjax;
$patientModel = $model->getModel('patient');
$submeterModel = $model->getModel('patientSubmeter');
$attribute = $patientModel->attributeLabels();
$attributeLabels = $submeterModel->attributeLabels();
?>
<?php
Pjax::begin([
    'id' => 'basic-pjax'
])
?>
<?php
$form = ActiveForm::begin([
            'id' => 'basic-basic',
            'options' => ['data' => ['pjax' => true]],
        ]);
?>
<div class='row basic-form patient-form-top'>
    <div class=" basic-header">
        <span class = 'basic-left-info'>
            基本信息
        </span>
        <span class = 'basic-right-up basic-right-up-basic'>
            <i class="fa his-pencil"></i>修改
        </span>
    </div>
    <div class="basic-form-content basic-form-content-basic">
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'username')->textInput(['maxlength' => true])->label($attribute['username'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'sex')->radioList(Patient::$getSex,['class' => 'sex'])->label($attribute['sex'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'card')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?=
                $form->field($patientModel, 'birthTime')->widget(
                    DatePicker::className(), [
                    'addon' => false,
                    'inline' => false,
                    'language' => 'zh-CN',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ]
                ])->label($attribute['birthday'] . '<span class = "label-required">*</span>')
                ?>
            </div>
            <div class="col-sm-4  bootstrap-timepicker">
                <?php echo $form->field($patientModel, 'hourMin')->textInput(['class' => 'form-control timepicker']) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'patient_source')->dropDownList(Patient::$getPatientSource, ['prompt' => '请选择']) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'iphone')->textInput(['maxlength' => true])->label($attribute['iphone'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'email')->textInput(['maxlength' => true]) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'wechat_num')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'marriage')->dropDownList(Patient::$getMarriage,['prompt' => '请选择']) ?>
            </div>
            <div class = 'col-md-4'>
                <?= $form->field($submeterModel, 'nationality')->dropDownList(PatientSubmeter::$getNationality,['class' => 'form-control select2','style' => 'width:100%','prompt' => '请选择国家/地区'])->label($attributeLabels['nationality']) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'nation')->dropDownList(Patient::$getNation,['prompt' => '请选择']) ?>
            </div>
        </div>
        <div class = 'row'>

            <div class = 'col-sm-4' id="languages_select_div">
                <?= $form->field($model->getModel('patientSubmeter'),  'languages')->dropDownList(PatientSubmeter::$getLanguages,['prompt' => '请选择']) ?>
            </div>

            <div class = 'col-sm-4 other_div' id="languages_input_div">
                <?= $form->field($model->getModel('patientSubmeter'), 'other_languages')->textInput(['maxlength' => true])->label(false) ?>
            </div>

            <div class = 'col-sm-4' id="faiths_select_div">
                <?= $form->field($model->getModel('patientSubmeter'), 'faiths')->dropDownList(PatientSubmeter::$getFaiths,['prompt' => '请选择']) ?>
            </div>

            <div  class = 'col-sm-4 other_div' id="faiths_input_div">
                <?= $form->field($model->getModel('patientSubmeter'), 'other_faiths')->textInput(['maxlength' =>
                    true])->label(false) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'mommyknows_account')->textInput(['maxlength'=>true]) ?>
            </div>

        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'occupation')->dropDownList(Patient::$getOccupation, ['prompt' => '请选择']) ?>
            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($patientModel, 'worker')->textInput(['maxlength' => true]) ?> 
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($patientModel, 'address')->textInput(['maxlength' => true]) ?>

            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($patientModel, 'detail_address')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-12'>
                <?= $form->field($patientModel, 'remark')->textarea(['rows' => 5]) ?>
            </div>
        </div>
        <div class=" basic-btn">
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form btn-cancel-basic']) ?>
            <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form basic-submit']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php $this->registerJs("
    
    // 编辑初始化页面
    var langSelect = $('#patientsubmeter-languages'),fatSelect = $('#patientsubmeter-faiths');
    5 == langSelect.val() ? $('#languages_input_div').show() : 1; //  其他时显示文本框
    6 == fatSelect.val() ? $('#faiths_input_div').show() : 1;
    if (5 == langSelect.val() && fatSelect.val()) { // 同时为其他时换行
        $('#languages_input_div').removeClass('col-sm-4').addClass('col-sm-8');
        $('#faiths_input_div').removeClass('col-sm-4').addClass('col-sm-8');
    }
    $('#basic-pjax .form-control').attr({'disabled': true});
    $('[type=radio]').attr({'disabled': true});
    $('.input-group-addon').hide();
") ?>
<?php Pjax::end() ?>    
