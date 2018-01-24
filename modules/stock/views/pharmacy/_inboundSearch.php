<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DatePickerAsset;
use dosamigos\datepicker\DatePickerLanguageAsset;
/* @var $this yii\web\View */
/* @var $model app\modules\pharmacy\models\search\StockSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>
<div class="stock-search hidden-xs">
    
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    
    <?= $form->field($model, 'begin_time')->widget(
        DatePicker::className(),[
            'inline' => false,
            'language' => 'zh-CN',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ],
            'options' =>    [
                'placeholder' => $attributeLabels['begin_time']
            ],
        ]) ?>
        
    <?= $form->field($model, 'end_time')->widget(
        DatePicker::className(),[
            'inline' => false,
            'language' => 'zh-CN',
            'options' =>    [
                'placeholder' => $attributeLabels['end_time']
            ],
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
            ],
           
        ]) ?>
    
    <?php if($status==1):?>
    <?= $form->field($model, 'id')->textInput(['placeholder' => '请输入'.$attributeLabels['id'] ]) ?>
    <?php else: ?>
     <?= $form->field($model, 'name')->textInput(['placeholder' => '请输入名称' ]) ?>
    <?php endif;?>
    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
