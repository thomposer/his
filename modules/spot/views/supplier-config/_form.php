<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\SupplierConf */
/* @var $form yii\widgets\ActiveForm */
$attribute=$model->attributeLabels();
?>

<div class="supplier-conf-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label($attribute['name'].'<span class=label-required>*</span>') ?>

    <?= $form->field($model, 'status')->dropDownList($model::$getStatus) ?>


    <div class="form-group">
        <?= Html::a('取消',['index'],['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
