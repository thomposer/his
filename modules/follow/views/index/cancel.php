<?php

use yii\helpers\Html;
use app\modules\card\models\UserCard;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\wechat\models\ChargeRecord */
$baseUrl = Yii::$app->request->baseUrl;
$modelAttribute = $model->attributeLabels();
?>

<div class="charge-record-view">
    <div class = "box-form">
        <?php
        $form = ActiveForm::begin([
                    'class' => 'follow-box',
                    'id' => 'follow-box'
        ]);
        ?>

        <div class = 'row'>
            <div class="col-sm-4">
                <?php
                $model->cancelUserName = $model->cancelUserName ? $model->cancelUserName : Yii::$app->user->identity->username;
                echo $form->field($model, 'cancelUserName')->textInput(['maxlength' => 11, 'readonly' => 'readonly'])->label($modelAttribute['cancelUserName'] . '<span class = "label-required">*</span>')
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?=
                $form->field($model, 'cancel_reason')->textarea(['readonly' => false, 'rows' => 7])->label($modelAttribute['cancel_reason'] . '<span class = "label-required">*</span>')
                ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
