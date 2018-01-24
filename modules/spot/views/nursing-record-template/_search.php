<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\search\NursingRecordTemplateSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="nursing-record-template-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    <?= $form->field($model, 'id')->textInput(['placeholder' => '请输入'.$attributeLabels['id'] ]) ?>

    <?= $form->field($model, 'spot_id')->textInput(['placeholder' => '请输入'.$attributeLabels['spot_id'] ]) ?>

    <?= $form->field($model, 'operating_id')->textInput(['placeholder' => '请输入'.$attributeLabels['operating_id'] ]) ?>

    <?php // echo $form->field($model, 'nursing_item')->textInput(['placeholder' => '请输入'.$attributeLabels['nursing_item'] ]) ?>

    <?php // echo $form->field($model, 'content_template')->textInput(['placeholder' => '请输入'.$attributeLabels['content_template'] ]) ?>

    <?php // echo $form->field($model, 'create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['create_time'] ]) ?>

    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>