<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\message\models\search\MessageCenterSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="message-center-search hidden-xs">
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

    <?= $form->field($model, 'type')->textInput(['placeholder' => '请输入'.$attributeLabels['type'] ]) ?>

    <?php // echo $form->field($model, 'content')->textInput(['placeholder' => '请输入'.$attributeLabels['content'] ]) ?>

    <?php // echo $form->field($model, 'patient_id')->textInput(['placeholder' => '请输入'.$attributeLabels['patient_id'] ]) ?>

    <?php // echo $form->field($model, 'status')->textInput(['placeholder' => '请输入'.$attributeLabels['status'] ]) ?>

    <?php // echo $form->field($model, 'create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['create_time'] ]) ?>

    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>