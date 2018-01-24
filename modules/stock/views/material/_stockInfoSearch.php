<?php

use app\modules\spot_set\models\Material;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\material\models\search\MaterialStockInfoSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="material-stock-info-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['stock-info'],
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'type')->dropDownList(Material::$typeOption,['prompt' => '请选择'.$attributeLabels['type']]) ?>

    <?= $form->field($model, 'materialName')->textInput(['placeholder' => '请填写'.$attributeLabels['materialName'] ]) ?>
    <?= $form->field($model, 'status')->hiddenInput() ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>