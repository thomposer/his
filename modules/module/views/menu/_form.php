<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\module\models\Menu;

/* @var $this yii\web\View */
/* @var $model app\modules\module\models\Menu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-form col-md-6">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'menu_url')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'type')->dropDownList(Menu::$left_menu) ?>
    <?= $form->field($model, 'parent_id')->dropDownList(ArrayHelper::map($titleList, 'id','module_description','data')) ?>

    <?= $form->field($model, 'status')->dropDownList(Menu::$menu_status) ?>
    
    <?= $form->field($model, 'role_type')->dropDownList(Menu::$role_type) ?>
    
    <?= $form->field($model, 'sort')->textInput() ?>
    <div class="form-group">
        <?= Html::a('取消', ['@moduleMenuIndex'], ['class' => 'btn btn-cancel'])?>
        <?= Html::submitButton($model->isNewRecord ? '新增' : '保存', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
