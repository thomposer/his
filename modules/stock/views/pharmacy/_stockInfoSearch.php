<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\AppAsset;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\search\OutpatientSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();

?>
<div class="recipe-record-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>


    <?= $form->field($model, 'name')->textInput(['placeholder' => '请输入'.$attributeLabels['name'] ]) ?>

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
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ],
        'options' =>    [
            'placeholder' => $attributeLabels['end_time']
        ],
    ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default']) ?>
    </div>

    <div class="form-group export-data-button">
        <?php if (isset($this->params['permList']['role']) || in_array($this->params['requestModuleController'] . '/stock-export-data', $this->params['permList'])): ?>
            <?= Html::a("导出", '#', ['class' => 'btn btn-default font-body2', 'data-pjax' => 0]) ?>
        <?php endif; ?>
    </div>


    <?php ActiveForm::end(); ?>
</div>
