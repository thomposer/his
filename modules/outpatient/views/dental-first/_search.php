<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\modules\outpatient\models\search\DentalFirstTemplateSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="dental-first-template-search hidden-xs">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['index'],
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
    <span class = 'search-default'>筛选：</span>
    <?= $form->field($model, 'id')->textInput(['placeholder' => '请输入'.$attributeLabels['id'] ]) ?>

    <?= $form->field($model, 'spot_id')->textInput(['placeholder' => '请输入'.$attributeLabels['spot_id'] ]) ?>

    <?= $form->field($model, 'name')->textInput(['placeholder' => '请输入'.$attributeLabels['name'] ]) ?>

    <?php // echo $form->field($model, 'chiefcomplaint')->textInput(['placeholder' => '请输入'.$attributeLabels['chiefcomplaint'] ]) ?>

    <?php // echo $form->field($model, 'historypresent')->textInput(['placeholder' => '请输入'.$attributeLabels['historypresent'] ]) ?>

    <?php // echo $form->field($model, 'pasthistory')->textInput(['placeholder' => '请输入'.$attributeLabels['pasthistory'] ]) ?>

    <?php // echo $form->field($model, 'oral_check')->textInput(['placeholder' => '请输入'.$attributeLabels['oral_check'] ]) ?>

    <?php // echo $form->field($model, 'auxiliary_check')->textInput(['placeholder' => '请输入'.$attributeLabels['auxiliary_check'] ]) ?>

    <?php // echo $form->field($model, 'diagnosis')->textInput(['placeholder' => '请输入'.$attributeLabels['diagnosis'] ]) ?>

    <?php // echo $form->field($model, 'cure_plan')->textInput(['placeholder' => '请输入'.$attributeLabels['cure_plan'] ]) ?>

    <?php // echo $form->field($model, 'cure')->textInput(['placeholder' => '请输入'.$attributeLabels['cure'] ]) ?>

    <?php // echo $form->field($model, 'advice')->textInput(['placeholder' => '请输入'.$attributeLabels['advice'] ]) ?>

    <?php // echo $form->field($model, 'remark')->textInput(['placeholder' => '请输入'.$attributeLabels['remark'] ]) ?>

    <?php // echo $form->field($model, 'create_time')->textInput(['placeholder' => '请输入'.$attributeLabels['create_time'] ]) ?>

    <?php // echo $form->field($model, 'update_time')->textInput(['placeholder' => '请输入'.$attributeLabels['update_time'] ]) ?>

    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
        <?php // Html::a('重置',[$this->params['requestUrl']], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>