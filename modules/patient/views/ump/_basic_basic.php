<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\modules\patient\models\Patient;
use yii\widgets\Pjax;

$attribute = $model->attributeLabels();
$patientId= Yii::$app->request->get('patientId')?Yii::$app->request->get('patientId'):0 ;
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
                <?= $form->field($model, 'username')->textInput(['maxlength' => true , 'autocomplete'=>"off"])->label($attribute['username'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-sm-4'>
                <?=
                $form->field($model, 'birthTime')->widget(
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
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'sex')->radioList(Patient::$getSex,['class' => 'sex'])->label($attribute['sex'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'iphone')->textInput(['maxlength' => true, 'autocomplete'=>"off"])->label($attribute['iphone'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'wechat_num')->textInput(['maxlength' => true]) ?> 
            </div>
             <div class = 'col-sm-4'>
                <?= $form->field($model, 'patient_source')->dropDownList(Patient::$getPatientSource, ['prompt' => '请选择']) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'card')->textInput(['maxlength' => true]) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'nation')->dropDownList(Patient::$getNation,['prompt' => '请选择']) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'marriage')->dropDownList(Patient::$getMarriage,['prompt' => '请选择']) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?> 
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'occupation')->dropDownList(Patient::$getOccupation, ['prompt' => '请选择']) ?>
            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'worker')->textInput(['maxlength' => true]) ?> 
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'detail_address')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-12'>
                <?= $form->field($model, 'remark')->textarea(['rows' => 5]) ?>
                <?= $form->field($model, 'hourMin')->hiddenInput()->label(false) ?>
            </div>
        </div>
        <div class="form-group basic-btn">
            <?php 
                if(!$model->isNewRecord){
                    echo Html::button('取消', ['class' => 'btn btn-cancel btn-form btn-cancel-basic']);
                }
            ?>
            <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form basic-submit']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php $this->registerJs("
    if($patientId){
        $('#basic-pjax .form-control').attr({'disabled': true});
        $('[type=radio]').attr({'disabled': true});
        $('.input-group-addon').hide();
    }
") ?>
<?php Pjax::end() ?>    
