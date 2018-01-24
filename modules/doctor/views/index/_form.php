<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\doctor\models\Doctor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="doctor-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'record_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'spot_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'incidence_date')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'heightcm')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'weightkg')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bloodtype')->dropDownList([ 0 => '0', 'ABO' => 'ABO', 'AB' => 'AB', 'O' => 'O', 'B' => 'B', 'A' => 'A', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'temperature_type')->textInput() ?>

    <?= $form->field($model, 'temperature')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'breathing')->textInput() ?>

    <?= $form->field($model, 'pulse')->textInput() ?>

    <?= $form->field($model, 'shrinkpressure')->textInput() ?>

    <?= $form->field($model, 'diastolic_pressure')->textInput() ?>

    <?= $form->field($model, 'oxygen_saturation')->textInput() ?>

    <?= $form->field($model, 'pain_score')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'personalhistory')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'genetichistory')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'allergy')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'chiefcomplaint')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'historypresent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'case_reg_img')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pasthistory')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'physical_examination')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'examination_check')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'first_check')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cure_idea')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'doctor_id')->textInput() ?>

    <?= $form->field($model, 'room_id')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <?= $form->field($model, 'diagnosis_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'triage_time')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
        <?= Html::a('取消',['index'],['class' => 'btn btn-cancel btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
