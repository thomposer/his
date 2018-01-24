<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\search\ChargeSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
$params = Yii::$app->request->queryParams;
$type = isset($params['type']) ? $params['type'] : 3;
?>

<div class="charge-record-log-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['index'],
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入'.$attributeLabels['username'] ]) ?>
    <?= $form->field($model, 'iphone')->textInput(['placeholder' => '请输入'.$attributeLabels['iphone'] ]) ?>

    <?= $form->field($model, 'search_begin_time')->widget(
        DatePicker::className(),[
        'inline' => false,
        'language' => 'zh-CN',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ],
        'options' =>    [
            'placeholder' => $model->getSearchTimeLabel(1, $type)
        ],
    ]) ?>

    <?= $form->field($model, 'search_end_time')->widget(
        DatePicker::className(),[
        'inline' => false,
        'language' => 'zh-CN',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ],
        'options' =>    [
            'placeholder' => $model->getSearchTimeLabel(2, $type)
        ],
    ]) ?>

    <?php  echo $form->field($model, 'search_doctor_name')->textInput(['placeholder' => '请输入'.$attributeLabels['diagnosis_doctor'] ]) ?>

    <?= Html::hiddenInput('type',$type) ?>
    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>