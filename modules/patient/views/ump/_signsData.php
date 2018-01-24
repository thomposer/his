<?php

use yii\widgets\ActiveForm;

$attribute = $model->attributeLabels();
?>
<?php
$form = ActiveForm::begin([
            'id' => '_signsData'
        ]);
?>
<div class='row'>
    <div class="family-modal">
        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'heightcm')->input('text', ['maxlength' => true]) ?>
            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'weightkg')->input('text', ['maxlength' => true]) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'bloodtype')->dropDownList($model::$bloodtype) ?>
            </div>
            <div class = 'col-sm-3'>
                <?= $form->field($model, 'temperature_type')->dropDownList($model::$temperature_type) ?>
            </div>
            <div class = 'col-sm-3'>
                <?= $form->field($model, 'temperature')->input('text', ['maxlength' => true])->label('　') ?>
            </div>
        </div>

        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'breathing')->textInput(['maxlength' => true]) ?>
            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'pulse')->textInput() ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'shrinkpressure')->textInput(['maxlength' => true]) ?>
            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'diastolic_pressure')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'oxygen_saturation')->textInput() ?>
            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'pain_score')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'head_circumference')->input('text', ['maxlength' => true]) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
