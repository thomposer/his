<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\search\CategoryHistorySearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="category-history-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    <?= $form->field($model, 'f_physical_id')->textInput(['placeholder' => '请输入'.$attributeLabels['f_physical_id'] ]) ?>

    <?= $form->field($model, 'f_record_id')->textInput(['placeholder' => '请输入'.$attributeLabels['f_record_id'] ]) ?>

    <?= $form->field($model, 'f_beg_category')->textInput(['placeholder' => '请输入'.$attributeLabels['f_beg_category'] ]) ?>

    <?php // echo $form->field($model, 'f_end_category')->textInput(['placeholder' => '请输入'.$attributeLabels['f_end_category'] ]) ?>

    <?php // echo $form->field($model, 'f_user_id')->textInput(['placeholder' => '请输入'.$attributeLabels['f_user_id'] ]) ?>

    <?php // echo $form->field($model, 'f_user_name')->textInput(['placeholder' => '请输入'.$attributeLabels['f_user_name'] ]) ?>

    <?php // echo $form->field($model, 'f_state')->textInput(['placeholder' => '请输入'.$attributeLabels['f_state'] ]) ?>

    <?php // echo $form->field($model, 'f_create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['f_create_time'] ]) ?>

    <?php // echo $form->field($model, 'f_update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['f_update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>