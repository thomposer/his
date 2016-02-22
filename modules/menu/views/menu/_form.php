<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\menu\models\Menu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-form col-md-6">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'menu_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parent_id')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'role_type')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => 'btn btn-success']) ?>
        <?= Html::a('返回列表',['index'],['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
