<?php

use app\specialModules\recharge\models\CardRecharge;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\search\CardRechargeSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="card-recharge-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'f_sale_id')->dropDownList(CardRecharge::getSales(), ['prompt' => '请选择'.$attributeLabels['f_sale_id']]) ?>

    <?= $form->field($model, 'f_user_name')->textInput(['placeholder' => '请输入'.$attributeLabels['f_user_name'] ]) ?>

    <?php // echo $form->field($model, 'f_id_info')->textInput(['placeholder' => '请输入'.$attributeLabels['f_id_info'] ]) ?>

    <?= $form->field($model, 'f_baby_name')->textInput(['placeholder' => '请输入'.$attributeLabels['f_baby_name'] ]) ?>

    <?= $form->field($model, 'f_phone')->textInput(['placeholder' => '请输入'.$attributeLabels['f_phone'] ]) ?>

    <?php // echo $form->field($model, 'f_card_fee')->textInput(['placeholder' => '请输入'.$attributeLabels['f_card_fee'] ]) ?>

    <?php // echo $form->field($model, 'f_pay_fee')->textInput(['placeholder' => '请输入'.$attributeLabels['f_pay_fee'] ]) ?>

    <?php // echo $form->field($model, 'f_order_status')->textInput(['placeholder' => '请输入'.$attributeLabels['f_order_status'] ]) ?>

    <?php // echo $form->field($model, 'f_pay_type')->textInput(['placeholder' => '请输入'.$attributeLabels['f_pay_type'] ]) ?>

    <?php // echo $form->field($model, 'f_state')->textInput(['placeholder' => '请输入'.$attributeLabels['f_state'] ]) ?>

    <?php // echo $form->field($model, 'f_property')->textInput(['placeholder' => '请输入'.$attributeLabels['f_property'] ]) ?>

    <?php // echo $form->field($model, 'f_create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['f_create_time'] ]) ?>

    <?php // echo $form->field($model, 'f_update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['f_update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>