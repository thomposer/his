<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\module\models\Menu;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\module\models\search\MenuSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="menu-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]       
    ]); ?>
   <span class = 'search-default'>筛选：</span>
    <?= $form->field($model, 'description')->textInput(['placeholder' => '请输入'.$attributeLabels['description'] ]); ?>
    <?= $form->field($model, 'menu_url')->textInput(['placeholder' => '请输入'.$attributeLabels['menu_url']]) ?>
    <?= $form->field($model, 'type')->dropDownList(Menu::$left_menu,['prompt' => '请选择渲染状态']) ?>    
    <?= $form->field($model, 'parent_id')->dropDownList(ArrayHelper::map($titleList, 'id', 'module_description','data'),['prompt' => '请选择模块']) ?>
    <?= $form->field($model, 'status')->dropDownList(Menu::$menu_status,['prompt' => '请选择状态']) ?>

   <div class="form-group">
    <?= Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
