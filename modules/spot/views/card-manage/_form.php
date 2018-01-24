<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\spot\models\CardManage;
use johnitvn\ajaxcrud\CrudAsset;

CrudAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\CardManage */
/* @var $form yii\widgets\ActiveForm */
if ($model->f_status == '正常') {
    $title = '停用';
}

if ($model->f_status == '未激活') {
    $title = '激活';
}

if ($model->f_status == '停用') {
    $title = '启用';
}
?>

<div class="card-manage-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>
    <div class = 'row'>
        <div class = 'col-sm-6'>
            <?= $form->field($model, 'f_card_id')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class = 'col-sm-6'>
            <?= $form->field($model, 'f_card_type_code')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
    </div>

    <div class = 'row'>
        <div class = 'col-sm-6'>
            <?= $form->field($model, 'f_identifying_code')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class = 'col-sm-6'>
            <?= $form->field($model, 'f_status')->textInput(['disabled' => true]) ?>
        </div>
    </div>

    <div class = 'row'>
        <div class = 'col-sm-6'>
            <?= $form->field($model, 'f_is_issue')->textInput(['disabled' => true]) ?>
        </div>
        <div class = 'col-sm-6'>
            <?= $form->field($model, 'f_create_time')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
    </div>

    <div class = 'row'>
        <div class = 'col-sm-6'>
            <?= $form->field($model, 'f_effective_time')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class = 'col-sm-6'>
            <?= $form->field($model, 'f_invalid_time')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
    </div>

    <div class = 'row'>
        <div class = 'col-sm-6'>
            <?= $form->field($model, 'f_activate_time')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
    </div>

    <div class = 'row'>
        <div class = 'col-sm-12'>
            <?= $form->field($model, 'f_card_desc')->textarea(['maxlength' => true, 'disabled' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::a('取消', ['index'], ['class' => 'btn btn-cancel btn-form second-cancel']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

