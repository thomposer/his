<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use app\modules\spot\models\CheckCode;
/* @var $this yii\web\View */
/* @var $model app\modules\check_code\models\search\checkCodeSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="check-code-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['index'],
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    <?= $form->field($model, 'major_code')->textInput(['placeholder' => '请输入'.$attributeLabels['major_code'] ]) ?>

    <?= $form->field($model, 'add_code')->textInput(['placeholder' => '请输入'.$attributeLabels['add_code'] ]) ?>

    <?= $form->field($model, 'name')->textInput(['placeholder' => '请输入'.$attributeLabels['name'] ]) ?>

    <?= $form->field($model, 'help_code')->textInput(['placeholder' => '请输入'.$attributeLabels['help_code'] ]) ?>

    <?= $form->field($model, 'status')->dropDownList(CheckCode::$getStatus,['prompt' => '请选择状态']) ?>











    <?php // echo $form->field($model, 'remark')->textInput(['placeholder' => '请输入'.$attributeLabels['remark'] ]) ?>

    <?php // echo $form->field($model, 'create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['create_time'] ]) ?>

    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>