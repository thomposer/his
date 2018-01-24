<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\spot\models\ChildCareTemplate;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CaseTemplate */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>

<div class="case-template-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label($attribute['name'] . '<span class = "label-required">*</span>') ?>


    <div class="form-group">
        <?= Html::a('取消', Yii::$app->request->referrer, ['class' => 'btn btn-cancel btn-form','data-pjax' => 0]) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
