<?php

use yii\helpers\Html;
use app\specialModules\recharge\models\UserCard;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\wechat\models\ChargeRecord */
$baseUrl = Yii::$app->request->baseUrl;
$attribute = $model->attributeLabels();
?>

<div class="charge-record-view">
    <div class = "box-form">
        <?php $form = ActiveForm::begin([
            'class'=>'card-check-box',
            'id'=>'card-check-box'
        ]); ?>
        <?= $form->field($model, 'checkType')->radioList(UserCard::$chekcTypeItem, ['class' => 'radio-inline', 'itemOptions' => ['labelOptions' => ['class' => 'radio-inline']]])->label($attribute['checkType'] . '<span class = "label-required">*</span>') ?>

        <?= $form->field($model, 'checkNum')->textInput(['maxlength' => true])->label('号　码<span class = "label-required">*</span>') ?>
        <?= $form->field($model, 'user_name')->textInput(['maxlength' => true])->label('姓　名<span class = "label-required">*</span>') ?>
        <?= $form->field($model, 'phone')->textInput(['maxlength' => true])->label($attribute['phone'] . '<span class = "label-required">*</span>') ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
