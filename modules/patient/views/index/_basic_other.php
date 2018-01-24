<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\triage\models\TriageInfo;
use yii\widgets\Pjax;
$attribute = $model->attributeLabels();
?>

<?php
Pjax::begin([
    'id' => 'other-pjax'
])
?>
<?php
$form = ActiveForm::begin([
            'id' => 'basic-other',
            'options' => ['data' => ['pjax' => true]],
        ]);
?>
<div class='row basic-form patient-form-top'>
    <div class=" basic-header">
        <span class = 'basic-left-info'>
            其他信息
        </span>
        <span class = 'basic-right-up basic-right-up-other'>
            <i class="fa his-pencil"></i>修改
        </span>
    </div>
    <div class="basic-form-content basic-form-content-other">
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'heightcm')->textInput(['maxlength' => true]) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'weightkg')->textInput(['maxlength' => true]) ?> 
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($model, 'bloodtype')->dropDownList(TriageInfo::$bloodtype) ?>
            </div>
            <div class = 'col-sm-4'>
                <?php // $form->field($model, 'weightkg')->textInput(['maxlength' => true]) ?> 
            </div>
        </div>
        <div class="form-group basic-btn basic-btn-basic">
            <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form other-submit']) ?>
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form btn-cancel-other']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php $this->registerJs("
    $('#other-pjax .form-control').attr({'disabled': true});
") ?>
<?php Pjax::end() ?>

