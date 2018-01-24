<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\spot\models\CheckCode;
/* @var $this yii\web\View */
/* @var $model app\modules\check_code\models\checkCode */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>

<div class="check-code-form col-md-8">

    <?php $form = ActiveForm::begin([
//        'options' => ['class' => 'form-horizontal form-card'],
//        'fieldConfig' => [
//            'template' => "<div class='col-xs-4 col-sm-4 text-left'>{label}</div><div class='col-xs-8 col-sm-8'>{input}<div class='col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0'>{error}</div></div>",
//        ]
    ]); ?>
    <div class='row'>
        <div class='col-md-6'>
            <?= $form->field($model, 'major_code')->textInput() ?>
        </div>
        <div class='col-md-6'>
            <?= $form->field($model, 'add_code')->textInput() ?>
        </div>

    </div>

    <div class='row'>
        <div class='col-md-6'>
            <?= $form->field($model, 'name')->textInput()->label($attribute['name'].'<span class = "label-required">*</span>') ?>
        </div>
        <div class='col-md-6'>
            <?= $form->field($model, 'help_code')->textInput() ?>
        </div>

    </div>

    <div class='row'>
        <div class='col-md-6'>
            <?= $form->field($model, 'status')->dropDownList(CheckCode::$getStatus,['prompt'=>'请选择状态'])->label($attribute['status'].'<span class = "label-required">*</span>') ?>
        </div>

    </div>

    <div class='row'>
        <div class='col-md-12'>
            <?= $form->field($model, 'remark')->textInput() ?>
        </div>

    </div>

    <div class="form-group">
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
