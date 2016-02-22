<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\wxinfo\models\search\WxinfoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wxinfo-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
       'options' =>  ['class' => 'form-horizontal search-form'],
        'fieldConfig' => [
            'template' => "<div class='search-labels text-right'>{label}</div><div class='col-xs-9 col-sm-7'>{input}</div>",
        ]
    ]); ?>
   
    <?= $form->field($model, 'spot')->dropDownList(ArrayHelper::map($spot, 'spot', 'spot_name'),[
        'prompt' => '请选择站点'
    ])->label('站点名称') ?>
    <?= $form->field($model, 'render')->dropDownList($status) ?>
    <div class = "form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary btn-submit']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
   </div>

    <?php ActiveForm::end(); ?>

</div>
