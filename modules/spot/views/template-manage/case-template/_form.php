<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\CaseTemplate;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CaseTemplate */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>

<div class="case-template-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label($attribute['name'] . '<span class = "label-required">*</span>') ?>

    <?php $model->saveType = 0; ?>

    <?= $form->field($model, 'saveType')->hiddenInput()->label(false) ?>

    <?php if (!isset($hidden)): ?>
        <?= $form->field($model, 'type')->radioList(CaseTemplate::$getType, ['class' => 'radio-inline', 'itemOptions' => ['labelOptions' => ['class' => 'radio-inline']]])->label($attribute['type'] . '<span class = "label-required">*</span>') ?>
    <?php endif; ?>

    <?= $form->field($model, 'chiefcomplaint')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'historypresent')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'pasthistory')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'pastdraghistory')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'personalhistory')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'genetichistory')->textarea(['rows' => 5 ]) ?>

    <?= $form->field($model, 'physical_examination')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'cure_idea')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'followup')->textarea(['rows' => 5]) ?>

    <div class="form-group">
        <?= Html::a('取消', ['case-index'], ['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
