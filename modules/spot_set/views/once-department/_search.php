<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\search */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="once-department-search hidden-xs">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
<span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'name')->textInput(['placeholder' => '请输入'.$attributeLabels['name'] ]) ?>


    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
