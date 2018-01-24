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

<div class="charge-record-log-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['index'],
        'options' => ['class' => 'form-horizontal search-form', 'data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class='search-default'>筛选：</span>
    <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入' . $attributeLabels['username']]) ?>

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
    <?= $form->field($model, 'pay_type')->dropDownList(ChargeRecord::$getType, ['prompt' => '请选择' . $attributeLabels['pay_type'], 'style' => 'width:150px;']) ?>

    <?= $form->field($model, 'doctor_name')->textInput(['placeholder' => '请输入' . $attributeLabels['doctor_name']]) ?>

    <?= $form->field($model, 'type')->dropDownList(ChargeRecordLog::$getType, ['prompt' => '请选择' . $attributeLabels['type'], 'style' => 'width:150px;']) ?>
    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>