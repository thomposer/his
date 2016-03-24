<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\make_appointment\models\Patient;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\Patient */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="patient-form col-md-12">

    <?php $form = ActiveForm::begin(); ?>
    <div class = 'col-md-6'>
    <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sex')->textInput() ?>

    <?= $form->field($model, 'birthday')->widget(
        DatePicker::className(),[
            'inline' => true,
            'language' => 'zh-CN',
            'clientOptions' => [
                'autoclose' => true,
                'size' => 'ms',               
                'format' => 'yyyy-mm-dd'
            ]
        ]
        ) ?>
    
    <?= $form->field($model, 'address')->textInput(['maxlength' => true,'data-toggle' => 'city-picker']) ?>
    
    </div>
    <div class ='col-md-6'>
    <?= $form->field($model, 'nation')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'marriage')->dropDownList(Patient::$marriage) ?>
    
    <?= $form->field($model, 'occupation')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'detail_address')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-12 form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => 'btn btn-success']) ?>
        <?= Html::a('返回列表',['index'],['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
