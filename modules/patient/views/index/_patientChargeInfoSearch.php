<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
use yii\helpers\ArrayHelper;
use app\modules\charge\models\ChargeRecordLog;
use app\modules\charge\models\ChargeRecord;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\search\chargeRecordLogSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="patient-charge-info-search hidden-xs clearfix">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => ['class' => 'form-horizontal search-form', 'data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class='search-default'>筛选：</span>
    <?= $form->field($model, 'trade_begin_time')->widget(
        DatePicker::className(), [
        'inline' => false,
        'language' => 'zh-CN',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ],
        'options' => [
            'placeholder' => '请选择' . $attributeLabels['trade_begin_time']
        ],
    ]) ?>

    <?= $form->field($model, 'trade_end_time')->widget(
        DatePicker::className(), [
        'inline' => false,
        'language' => 'zh-CN',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ],
        'options' => [
            'placeholder' => '请选择' . $attributeLabels['trade_end_time']
        ],
    ]) ?>

	<?= $form->field($model, 'case_id')->textInput(['placeholder' => '请输入' . $attributeLabels['case_id']]) ?>
    <?= $form->field($model, 'pay_type')->dropDownList(ChargeRecord::$getType, ['prompt' => '请选择' . $attributeLabels['pay_type'], 'style' => 'width:150px;']) ?>

    <?= $form->field($model, 'doctor_name')->textInput(['placeholder' => '请输入' . $attributeLabels['doctor_name']]) ?>

    <?= $form->field($model, 'type')->dropDownList(ChargeRecordLog::$getType, ['prompt' => '请选择' . $attributeLabels['type'], 'style' => 'width:150px;']) ?>

    <?php
    $params = Yii::$app->request->queryParams;
    $type = isset($params['type']) ? $params['type'] : 6;
    ?>
    <?= Html::hiddenInput('type', $type) ?>
    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>