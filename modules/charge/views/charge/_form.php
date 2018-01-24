<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\chargeRecordLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="charge-record-log-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'spot_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'record_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'out_trade_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sex')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'age')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'attending_doctor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'record_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'diagnosis_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'trade_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'create_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'update_time')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::a('取消',Yii::$app->request->referrer,['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
