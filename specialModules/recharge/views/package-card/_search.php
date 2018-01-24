<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\search\CardRechargeSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="package-card-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>


    <?= $form->field($model, 'iphone')->textInput(['placeholder' => '请输入'.$attributeLabels['iphone'] ]) ?>
    <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入'.$attributeLabels['username'] ]) ?>
    <?= $form->field($model, 'package_card_id')->dropDownList(ArrayHelper::map($packageCardList, 'id', 'name'),['prompt' => '请选择'.$attributeLabels['package_card_id']]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>