<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\make_appointment\models\Appointment;
use yii\helpers\ArrayHelper;
use app\common\AutoLayout;

/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\search\AppointmentSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
?>

<div class="appointment-search hidden-xs">

    <?php $form = ActiveForm::begin([

        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?php // $form->field($model, 'type')->dropDownList(Appointment::$getType,['prompt' => '请选择预约类型']) ?>

    <?= $form->field($model, 'doctor_id')->dropDownList(ArrayHelper::map($doctorInfo, 'id', 'username'),['prompt' => '请选择医生','class'=>'form-control doctor-width']) ?>
    <?= $form->field($model, 'type')->dropDownList(ArrayHelper::map($spot_type, 'id', 'name'),['prompt' => '请选择服务类型','class'=>'form-control doctor-width']) ?>

    <div class="form-group search_button">
        <button type="button" class="btn btn-default" >搜索</button>

    </div>

    <?php ActiveForm::end(); ?>

</div>
