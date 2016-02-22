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

    <?= $form->field($model, 'parent_id')->dropDownList(ArrayHelper::map($titleList, 'id','module_description')) ?>

    <?= $form->field($model, 'status')->textInput()->dropDownList(Menu::$menu_status) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => 'btn btn-success']) ?>
         <?= Html::a('返回列表', ['@moduleMenuIndex'], ['class' => 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
