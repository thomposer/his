<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\report\models\search\AppointmentSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="appointment-search hidden-xs">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入'.$attributeLabels['username'] ]) ?>

    <?= $form->field($model, 'iphone')->textInput(['placeholder' => '请输入'.$attributeLabels['iphone'] ]) ?>

    <?php // echo $form->field($model, 'time')->textInput(['placeholder' => '请输入'.$attributeLabels['time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
