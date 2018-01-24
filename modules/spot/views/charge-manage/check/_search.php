<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\search\CheckListSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="check-list-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'action' => ['check-index'],
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'name')->textInput(['placeholder' => '请输入'.$attributeLabels['name'] ]) ?>

    <?= $form->field($model, 'unionSpotId')->dropDownList(array_column($spotList, 'spot_name', 'id'),['prompt'=>'请选择'.$attributeLabels['unionSpotId'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default delete-btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
