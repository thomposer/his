<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\make_appointment\models\Appointment;
use yii\helpers\ArrayHelper;
use app\common\AutoLayout;
use app\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\make_appointment\models\search\AppointmentSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
?>

<div class="schedule-search hidden-xs" style="display: none">

    <?php $form = ActiveForm::begin([

        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'occupation')->dropDownList(User::$getOccuption,['prompt' => '请选择职位','class'=>'form-control doctor-width']) ?>


    <div class="form-group search_button">
        <button type="button" class="btn btn-default" >搜索</button>

    </div>

    <?php ActiveForm::end(); ?>

</div>
