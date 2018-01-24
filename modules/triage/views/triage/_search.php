<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\triage\models\Triage;
use dosamigos\datepicker\DatePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\modules\triage\models\search\TriageSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="triage-search hidden-xs">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form'],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
<span class = 'search-default'>筛选：</span>
    <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入'.$attributeLabels['username'] ]) ?>
    <?= $form->field($model, 'iphone')->textInput(['placeholder' => '请输入'.$attributeLabels['iphone']]) ?>
    <?= $form->field($model, 'second_department_id')->dropDownList(ArrayHelper::map($secondDepartmentInfo, 'id', 'name'),['prompt' => '请选择就诊科室','class'=>'form-control drop-down-box']) ?>
    <?= $form->field($model, 'doctor_id')->dropDownList(ArrayHelper::map($doctorInfo, 'id', 'username'),['prompt' => '请选择接诊医生','class'=>'form-control department-width drop-down-box']) ?>
    <?= $form->field($model, 'arrival_time')->widget(
        DatePicker::className(),[
            'inline' => false,
            'language' => 'zh-CN',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ],
        'options'=>[
        'placeholder'=>'报到时间'
    ]
        ]
    )->label('选择报到时间') ?>
    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default delete-left-padding']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
