<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\search\RecipelistClinicSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="recipelist-clinic-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['@spot_setChargeManageRecipeClinicIndex']),
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    <?= $form->field($model, 'name')->textInput(['placeholder' => '请输入'.$attributeLabels['name'] ]) ?>

    <?= $form->field($model, 'product_name')->textInput(['placeholder' => '请输入'.$attributeLabels['product_name'] ]) ?>


    <?php // echo $form->field($model, 'price')->textInput(['placeholder' => '请输入'.$attributeLabels['price'] ]) ?>

    <?php // echo $form->field($model, 'default_price')->textInput(['placeholder' => '请输入'.$attributeLabels['default_price'] ]) ?>

    <?php // echo $form->field($model, 'status')->textInput(['placeholder' => '请输入'.$attributeLabels['status'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>