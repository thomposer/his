<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\search\MedicalFeeSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="medical-fee-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'action' => ['medical-fee-index'],
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'remarks')->textInput(['placeholder' => '请输入'.$attributeLabels['remarks'] ]) ?>
    
    <?= $form->field($model, 'note')->textInput(['placeholder' => '请输入'.$attributeLabels['note'] ]) ?>
    
    <?= $form->field($model, 'price')->textInput(['placeholder' => '请输入'.$attributeLabels['price'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
