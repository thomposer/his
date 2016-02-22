<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\module\models\Menu;

/* @var $this yii\web\View */
/* @var $model app\modules\module\models\search\MenuSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form'],
        'fieldConfig' => [
            'template' => "<div class='search-labels text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div>",
        ]
        
    ]); ?>
   
    <?= $form->field($model, 'description') ?>
    <?php //echo $form->field($model, 'menu_url') ?>
    <?= $form->field($model, 'type')->dropDownList(Menu::$left_menu,['prompt' => '全部']) ?>    
    <?= $form->field($model, 'parent_id')->dropDownList(ArrayHelper::map($titleList, 'id', 'module_description'),['prompt' => '全部']) ?>
    <?= $form->field($model, 'status')->dropDownList(Menu::$menu_status,['prompt' => '全部']) ?>

   <div class="form-group">
    <?= Html::submitButton('搜索', ['class' => 'btn btn-primary btn-submit']) ?>
    <?= Html::a('重置',['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
