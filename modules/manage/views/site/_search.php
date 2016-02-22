<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\awpmanage\models\ServiceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="service-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'wxname') ?>

    <?= $form->field($model, 'wxcode') ?>

    <?= $form->field($model, 'maininfo') ?>

    <?= $form->field($model, 'appid') ?>

    <?php // echo $form->field($model, 'appsecret') ?>

    <?php // echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'token') ?>

    <?php // echo $form->field($model, 'aeskey') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
