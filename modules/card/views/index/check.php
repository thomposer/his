<?php

use yii\helpers\Html;
use app\modules\card\models\UserCard;
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

        <?= $form->field($model, 'checkNum')->textInput(['maxlength' => true])->label($attribute['checkNum'] . '<span class = "label-required">*</span>') ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
