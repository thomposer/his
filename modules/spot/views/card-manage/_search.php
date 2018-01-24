<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use app\modules\spot\models\CardManage;

/* @var $this yii\web\View */
/* @var $model app\modules\spot\models\search\CardManageSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();

?>

<div class="card-manage-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    <?php //echo $form->field($model, 'f_physical_id')->textInput(['placeholder' => '请输入'.$attributeLabels['f_physical_id'] ]) ?>

    <?= $form->field($model, 'f_card_id')->textInput(['placeholder' => '请输入'.$attributeLabels['f_card_id'] ]) ?>

    <?= $form->field($model, 'f_card_type_code')->textInput(['placeholder' => '请输入'.$attributeLabels['f_card_type_code'] ]) ?>

    <?php // echo $form->field($model, 'f_identifying_code')->textInput(['placeholder' => '请输入'.$attributeLabels['f_identifying_code'] ]) ?>

    <?php  echo $form->field($model, 'f_status')->dropDownList(CardManage::$getStatus, ['prompt' => '请选择状态']) ?>
    <?php  echo $form->field($model, 'f_card_desc')->textInput(['placeholder' => '请输入'.$attributeLabels['f_card_desc'] ])  ?>
    <?php  echo $form->field($model, 'cardName')->dropDownList(CardManage::$cardTypeCode, ['prompt' => '请选择卡名称']) ?>

    <?php // echo $form->field($model, 'f_card_desc')->textInput(['placeholder' => '请输入'.$attributeLabels['f_card_desc'] ]) ?>

    <?php // echo $form->field($model, 'f_is_issue')->textInput(['placeholder' => '请输入'.$attributeLabels['f_is_issue'] ]) ?>

    <?php // echo $form->field($model, 'f_create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['f_create_time'] ]) ?>

    <?php // echo $form->field($model, 'f_effective_time')->textInput(['placeholder' => '请输入'.$attributeLabels['f_effective_time'] ]) ?>

    <?php // echo $form->field($model, 'f_activate_time')->textInput(['placeholder' => '请输入'.$attributeLabels['f_activate_time'] ]) ?>

    <?php // echo $form->field($model, 'f_invalid_time')->textInput(['placeholder' => '请输入'.$attributeLabels['f_invalid_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>