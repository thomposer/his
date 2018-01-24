<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\search\BoardSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="board-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    <?= $form->field($model, 'id')->textInput(['placeholder' => '请输入'.$attributeLabels['id'] ]) ?>

    <?= $form->field($model, 'name')->textInput(['placeholder' => '请输入'.$attributeLabels['name'] ]) ?>

    <?php // echo $form->field($model, 'file_name')->textInput(['placeholder' => '请输入'.$attributeLabels['file_name'] ]) ?>

    <?php // echo $form->field($model, 'size')->textInput(['placeholder' => '请输入'.$attributeLabels['size'] ]) ?>

    <?php // echo $form->field($model, 'file_url')->textInput(['placeholder' => '请输入'.$attributeLabels['file_url'] ]) ?>

    <?php // echo $form->field($model, 'type')->textInput(['placeholder' => '请输入'.$attributeLabels['type'] ]) ?>

    <?php // echo $form->field($model, 'status')->textInput(['placeholder' => '请输入'.$attributeLabels['status'] ]) ?>

    <?php // echo $form->field($model, 'create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['create_time'] ]) ?>

    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>