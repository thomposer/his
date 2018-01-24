<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\rbac\models\PermissionForm */
/* @var $form yii\widgets\ActiveForm */
$attribute = $model->attributeLabels();
?>

<div class="permission-form-form col-md-8">

    <?php $form = ActiveForm::begin(); ?>
	<?=  $form->field($model, 'category')->dropDownList(ArrayHelper::map($categories, 'name', 'description','data'),['prompt' => '请选择权限分类'])->label($attribute['category'].'<span class = "label-required">*</span>') ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label($attribute['name'].'<span class = "label-required">*</span>') ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6])->label($attribute['description'].'<span class = "label-required">*</span>') ?>

    <div class="form-group">
        <?= Html::a('取消',Yii::$app->request->referrer,['class' => 'btn btn-cancel btn-form second-cancel']) ?>
        <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
