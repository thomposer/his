<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\message\models\MessageCenter */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="message-center-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'spot_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'patient_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'update_time')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
        <?= Html::a('取消',Yii::$app->request->referrer,['class' => 'btn btn-cancel btn-form second-cancel']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
