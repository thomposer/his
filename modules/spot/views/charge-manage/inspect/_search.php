<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\search\InspectSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="inspect-search hidden-xs">

    <?php $form = ActiveForm::begin([
        'action' => ['inspect-index'],
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
<span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'inspect_name')->textInput(['placeholder' => '请输入'.$attributeLabels['inspect_name'] ]) ?>
    <?= $form->field($model, 'unionSpotId')->dropDownList(array_column($spotList,'spot_name','id'),['prompt'=>'请输入'.$attributeLabels['unionSpotId'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
