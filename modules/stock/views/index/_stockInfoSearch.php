<?php

use app\modules\spot\models\Consumables;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$attributeLabels = $model->attributeLabels();
?>

<div class="consumables-stock-info-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['consumables-stock-info'],
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'type')->dropDownList(Consumables::$typeOption,['prompt' => '请选择'.$attributeLabels['type']]) ?>

    <?= $form->field($model, 'consumablesName')->textInput(['placeholder' => '请填写'.$attributeLabels['consumablesName'] ]) ?>
    <?= $form->field($model, 'status')->hiddenInput() ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>