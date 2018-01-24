<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\search\PaymenyConfigSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="payment-config-search hidden-xs">
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

    <?= $form->field($model, 'appid')->textInput(['placeholder' => '请输入'.$attributeLabels['appid'] ]) ?>

    <?php // echo $form->field($model, 'mchid')->textInput(['placeholder' => '请输入'.$attributeLabels['mchid'] ]) ?>

    <?php // echo $form->field($model, 'payment_key')->textInput(['placeholder' => '请输入'.$attributeLabels['payment_key'] ]) ?>

    <?php // echo $form->field($model, 'type')->textInput(['placeholder' => '请输入'.$attributeLabels['type'] ]) ?>

    <?php // echo $form->field($model, 'create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['create_time'] ]) ?>

    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>