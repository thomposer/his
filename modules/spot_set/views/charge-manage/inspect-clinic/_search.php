<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\modules\spot_set\models\search\InspectClinicSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="inspect-clinic-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['@spot_setChargeManageInspectClinicIndex']),
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'inspectName')->textInput(['placeholder' => '请输入'.$attributeLabels['inspect_id'] ]) ?>

    <?php // echo $form->field($model, 'inspect_price')->textInput(['placeholder' => '请输入'.$attributeLabels['inspect_price'] ]) ?>

    <?php // echo $form->field($model, 'cost_price')->textInput(['placeholder' => '请输入'.$attributeLabels['cost_price'] ]) ?>

    <?php // echo $form->field($model, 'deliver')->textInput(['placeholder' => '请输入'.$attributeLabels['deliver'] ]) ?>

    <?php // echo $form->field($model, 'specimen_type')->textInput(['placeholder' => '请输入'.$attributeLabels['specimen_type'] ]) ?>

    <?php // echo $form->field($model, 'cuvette')->textInput(['placeholder' => '请输入'.$attributeLabels['cuvette'] ]) ?>

    <?php // echo $form->field($model, 'inspect_type')->textInput(['placeholder' => '请输入'.$attributeLabels['inspect_type'] ]) ?>

    <?php // echo $form->field($model, 'remark')->textInput(['placeholder' => '请输入'.$attributeLabels['remark'] ]) ?>

    <?php // echo $form->field($model, 'description')->textInput(['placeholder' => '请输入'.$attributeLabels['description'] ]) ?>

    <?php // echo $form->field($model, 'status')->textInput(['placeholder' => '请输入'.$attributeLabels['status'] ]) ?>

    <?php // echo $form->field($model, 'create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['create_time'] ]) ?>

    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>