<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\patient\models\PatientRecord;

/* @var $this yii\web\View */
/* @var $model app\modules\report\models\search\PatientSearch */
/* @var $form yii\widgets\ActiveForm */
$attributeLabels = $model->attributeLabels();
?>

<div class="patient-search hidden-xs">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' =>  ['class' => 'form-horizontal search-form','data-pjax' => true],
        'fieldConfig' => [
            'template' => "{input}",
        ]
    ]); ?>
<span class = 'search-default'>筛选：</span>

    <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入'.$attributeLabels['username'] ]) ?>

    <?php //echo $form->field($model, 'status')->dropDownList(PatientRecord::$getStatus,['prompt' => '请选择状态']) ?>


    <div class="form-group search_button">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
