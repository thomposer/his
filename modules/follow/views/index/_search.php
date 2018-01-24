<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DatePickerAsset;
use dosamigos\datepicker\DatePickerLanguageAsset;
use app\modules\user\models\User;
use app\modules\follow\models\Follow;
/* @var $this yii\web\View */
/* @var $model app\modules\follow\models\search\FollowSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="follow-search hidden-xs clearfix">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['index'],
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <div class="clearfix">
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'patientNumber')->textInput(['placeholder' => $attributeLabels['patientNumber'] ]) ?>

    <?= $form->field($model, 'username')->textInput(['placeholder' => $attributeLabels['username'] ]) ?>

    <?= $form->field($model, 'iphone')->textInput(['placeholder' => $attributeLabels['iphone'] ]) ?>

    <?= $form->field($model, 'follow_begin_time')->widget(
        DatePicker::className(),[
        'inline' => false,
        'language' => 'zh-CN',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ],
        'options' =>    [
            'placeholder' => $attributeLabels['follow_begin_time']
        ],
    ]) ?>
    <div class="form-link">-</div>
    <?= $form->field($model, 'follow_end_time')->widget(
        DatePicker::className(),[
        'inline' => false,
        'language' => 'zh-CN',
        'options' =>    [
            'placeholder' => $attributeLabels['follow_end_time']
        ],
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
        ],

    ]) ?>

    <?= $form->field($model, 'planCreatorName')->dropDownList(ArrayHelper::map($userInfo, 'id', 'username'),['prompt' => '创建人','class'=>'form-control department-width']) ?>

    <?= $form->field($model, 'execute_role')->dropDownList(Follow::$getExecuteRole,['prompt' => '计划执行角色','class'=>'form-control department-width']) ?>

    <?= $form->field($model, 'follow_plan_executor')->dropDownList(ArrayHelper::map($userInfo, 'id', 'username'),['prompt' => '计划执行人','class'=>'form-control department-width']) ?>
	
	<?= $form->field($model, 'follow_state')->hiddenInput() ?>
    
    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default btn-follow-search']) ?>
        <div class="more">
            <a state='1' class="more-word">更多条件</a>
            <i class="fa fa-caret-down"></i>
        </div>
    </div>
    </div>
    <div class="follow-search-line clearfix">
        <?= $form->field($model, 'diagnosis_begin_time',[
            'options' => [
                'class' => 'pull-left follow-search-date'
            ]
        ])->widget(
            DatePicker::className(),[
            'inline' => false,
            'language' => 'zh-CN',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            ],
            'options' =>    [
                'placeholder' => $attributeLabels['diagnosis_begin_time']
            ],
        ]) ?>
        <div class="form-link">-</div>
        <?= $form->field($model, 'diagnosis_end_time')->widget(
            DatePicker::className(),[
            'inline' => false,
            'language' => 'zh-CN',
            'options' =>    [
                'placeholder' => $attributeLabels['diagnosis_end_time']
            ],
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
            ],

        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
