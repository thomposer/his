<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\search\CaseTemplateSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="case-template-search hidden-xs">
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

    <?= $form->field($model, 'user_id')->textInput(['placeholder' => '请输入'.$attributeLabels['user_id'] ]) ?>

    <?php // echo $form->field($model, 'name')->textInput(['placeholder' => '请输入'.$attributeLabels['name'] ]) ?>

    <?php // echo $form->field($model, 'type')->textInput(['placeholder' => '请输入'.$attributeLabels['type'] ]) ?>

    <?php // echo $form->field($model, 'chiefcomplaint')->textInput(['placeholder' => '请输入'.$attributeLabels['chiefcomplaint'] ]) ?>

    <?php // echo $form->field($model, 'historypresent')->textInput(['placeholder' => '请输入'.$attributeLabels['historypresent'] ]) ?>

    <?php // echo $form->field($model, 'pasthistory')->textInput(['placeholder' => '请输入'.$attributeLabels['pasthistory'] ]) ?>

    <?php // echo $form->field($model, 'personalhistory')->textInput(['placeholder' => '请输入'.$attributeLabels['personalhistory'] ]) ?>

    <?php // echo $form->field($model, 'genetichistory')->textInput(['placeholder' => '请输入'.$attributeLabels['genetichistory'] ]) ?>

    <?php // echo $form->field($model, 'physical_examination')->textInput(['placeholder' => '请输入'.$attributeLabels['physical_examination'] ]) ?>

    <?php // echo $form->field($model, 'cure_idea')->textInput(['placeholder' => '请输入'.$attributeLabels['cure_idea'] ]) ?>

    <?php // echo $form->field($model, 'create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['create_time'] ]) ?>

    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'delete-btn btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>