<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\doctor\models\search\DoctorSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="doctor-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    <?= $form->field($model, 'id')->textInput(['placeholder' => '请输入'.$attributeLabels['id'] ]) ?>

    <?= $form->field($model, 'record_id')->textInput(['placeholder' => '请输入'.$attributeLabels['record_id'] ]) ?>

    <?= $form->field($model, 'spot_id')->textInput(['placeholder' => '请输入'.$attributeLabels['spot_id'] ]) ?>

    <?php // echo $form->field($model, 'incidence_date')->textInput(['placeholder' => '请输入'.$attributeLabels['incidence_date'] ]) ?>

    <?php // echo $form->field($model, 'heightcm')->textInput(['placeholder' => '请输入'.$attributeLabels['heightcm'] ]) ?>

    <?php // echo $form->field($model, 'weightkg')->textInput(['placeholder' => '请输入'.$attributeLabels['weightkg'] ]) ?>

    <?php // echo $form->field($model, 'bloodtype')->textInput(['placeholder' => '请输入'.$attributeLabels['bloodtype'] ]) ?>

    <?php // echo $form->field($model, 'temperature_type')->textInput(['placeholder' => '请输入'.$attributeLabels['temperature_type'] ]) ?>

    <?php // echo $form->field($model, 'temperature')->textInput(['placeholder' => '请输入'.$attributeLabels['temperature'] ]) ?>

    <?php // echo $form->field($model, 'breathing')->textInput(['placeholder' => '请输入'.$attributeLabels['breathing'] ]) ?>

    <?php // echo $form->field($model, 'pulse')->textInput(['placeholder' => '请输入'.$attributeLabels['pulse'] ]) ?>

    <?php // echo $form->field($model, 'shrinkpressure')->textInput(['placeholder' => '请输入'.$attributeLabels['shrinkpressure'] ]) ?>

    <?php // echo $form->field($model, 'diastolic_pressure')->textInput(['placeholder' => '请输入'.$attributeLabels['diastolic_pressure'] ]) ?>

    <?php // echo $form->field($model, 'oxygen_saturation')->textInput(['placeholder' => '请输入'.$attributeLabels['oxygen_saturation'] ]) ?>

    <?php // echo $form->field($model, 'pain_score')->textInput(['placeholder' => '请输入'.$attributeLabels['pain_score'] ]) ?>

    <?php // echo $form->field($model, 'personalhistory')->textInput(['placeholder' => '请输入'.$attributeLabels['personalhistory'] ]) ?>

    <?php // echo $form->field($model, 'genetichistory')->textInput(['placeholder' => '请输入'.$attributeLabels['genetichistory'] ]) ?>

    <?php // echo $form->field($model, 'allergy')->textInput(['placeholder' => '请输入'.$attributeLabels['allergy'] ]) ?>

    <?php // echo $form->field($model, 'chiefcomplaint')->textInput(['placeholder' => '请输入'.$attributeLabels['chiefcomplaint'] ]) ?>

    <?php // echo $form->field($model, 'historypresent')->textInput(['placeholder' => '请输入'.$attributeLabels['historypresent'] ]) ?>

    <?php // echo $form->field($model, 'case_reg_img')->textInput(['placeholder' => '请输入'.$attributeLabels['case_reg_img'] ]) ?>

    <?php // echo $form->field($model, 'pasthistory')->textInput(['placeholder' => '请输入'.$attributeLabels['pasthistory'] ]) ?>

    <?php // echo $form->field($model, 'physical_examination')->textInput(['placeholder' => '请输入'.$attributeLabels['physical_examination'] ]) ?>

    <?php // echo $form->field($model, 'examination_check')->textInput(['placeholder' => '请输入'.$attributeLabels['examination_check'] ]) ?>

    <?php // echo $form->field($model, 'first_check')->textInput(['placeholder' => '请输入'.$attributeLabels['first_check'] ]) ?>

    <?php // echo $form->field($model, 'cure_idea')->textInput(['placeholder' => '请输入'.$attributeLabels['cure_idea'] ]) ?>

    <?php // echo $form->field($model, 'remark')->textInput(['placeholder' => '请输入'.$attributeLabels['remark'] ]) ?>

    <?php // echo $form->field($model, 'doctor_id')->textInput(['placeholder' => '请输入'.$attributeLabels['doctor_id'] ]) ?>

    <?php // echo $form->field($model, 'room_id')->textInput(['placeholder' => '请输入'.$attributeLabels['room_id'] ]) ?>

    <?php // echo $form->field($model, 'create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['create_time'] ]) ?>

    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <?php // echo $form->field($model, 'diagnosis_time')->textInput(['placeholder' => '请输入'.$attributeLabels['diagnosis_time'] ]) ?>

    <?php // echo $form->field($model, 'triage_time')->textInput(['placeholder' => '请输入'.$attributeLabels['triage_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>