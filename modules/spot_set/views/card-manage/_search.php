<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\search\CardRechargeCategorySearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="card-recharge-category-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'f_category_name')->textInput(['placeholder' => '请输入'.$attributeLabels['f_category_name'] ]) ?>


    <?php // echo $form->field($model, 'f_state')->textInput(['placeholder' => '请输入'.$attributeLabels['f_state'] ]) ?>

    <?php // echo $form->field($model, 'f_parent_id')->textInput(['placeholder' => '请输入'.$attributeLabels['f_parent_id'] ]) ?>

    <?php // echo $form->field($model, 'f_medical_fee_discount')->textInput(['placeholder' => '请输入'.$attributeLabels['f_medical_fee_discount'] ]) ?>

    <?php // echo $form->field($model, 'f_inspect_discount')->textInput(['placeholder' => '请输入'.$attributeLabels['f_inspect_discount'] ]) ?>

    <?php // echo $form->field($model, 'f_check_discount')->textInput(['placeholder' => '请输入'.$attributeLabels['f_check_discount'] ]) ?>

    <?php // echo $form->field($model, 'f_cure_discount')->textInput(['placeholder' => '请输入'.$attributeLabels['f_cure_discount'] ]) ?>

    <?php // echo $form->field($model, 'f_recipe_discount')->textInput(['placeholder' => '请输入'.$attributeLabels['f_recipe_discount'] ]) ?>

    <?php // echo $form->field($model, 'f_create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['f_create_time'] ]) ?>

    <?php // echo $form->field($model, 'f_upadte_time')->textInput(['placeholder' => '请输入'.$attributeLabels['f_upadte_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>