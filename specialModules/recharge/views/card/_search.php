<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use app\modules\spot\models\CardManage;
/* @var $this yii\web\View */
/* @var $model app\modules\card\models\serarch\UserCardSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="user-card-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'card_id')->textInput(['placeholder' => '请输入'.$attributeLabels['card_id'] ]) ?>

    <?= $form->field($model, 'card_type_code')->textInput(['placeholder' => '请输入'.$attributeLabels['card_type_code'] ]) ?>

    <?php // echo $form->field($model, 'user_name')->textInput(['placeholder' => '请输入'.$attributeLabels['user_name'] ]) ?>

    <?php echo $form->field($model, 'phone')->textInput(['placeholder' => '请输入'.$attributeLabels['phone'] ]) ?>
   <?php  echo $form->field($model, 'cardName')->dropDownList(CardManage::$cardTypeCode, ['prompt' => '请选择卡名称']) ?>

    <?php // echo $form->field($model, 'service_id')->textInput(['placeholder' => '请输入'.$attributeLabels['service_id'] ]) ?>

    <?php // echo $form->field($model, 'parent_spot_id')->textInput(['placeholder' => '请输入'.$attributeLabels['parent_spot_id'] ]) ?>

    <?php // echo $form->field($model, 'service_left')->textInput(['placeholder' => '请输入'.$attributeLabels['service_left'] ]) ?>

    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <?php // echo $form->field($model, 'create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['create_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>