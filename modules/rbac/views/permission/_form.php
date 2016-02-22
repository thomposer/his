<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\Permission */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="permission-form col-md-6" >

        <?php $form = ActiveForm::begin([
            'options' => ['class' => 'form-input'],
        ]); ?> 
    <?=  $form->field($model, 'category')->dropDownList(ArrayHelper::map($categories, 'name', 'description')) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    


    <div class="form-group" >
        <?= Html::submitButton($model->isNewRecord? '添加':'修改', ['class' => 'btn btn-success']) ?>
        <?= Html::a('返回列表', ['@rbacPermission'], ['class' => 'btn btn-primary  returnindex'])?>
    </div>
    <?php ActiveForm::end(); ?>

</div>