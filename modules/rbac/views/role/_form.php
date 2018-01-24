<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Item */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>

<div class="item-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'description')->textInput(['maxLength' => 20])->label($attribute['description'].'<span class = "label-required">*</span>') ?>
    
    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::a('取消',['index'],['class' => 'btn btn-cancel btn-form']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
