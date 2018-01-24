<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\patient\models\PatientSubmeter;
use yii\widgets\Pjax;
$submeterModel = $model->getModel('patientSubmeter');
$attributeLabels = $submeterModel->attributeLabels();
?>
<?php
Pjax::begin([
    'id' => 'child-pjax'
])
?>
<?php
$form = ActiveForm::begin([
            'id' => 'basic-child',
            'options' => ['data' => ['pjax' => true]],
        ]);
?>
<div class='row basic-form patient-form-top'>
    <div class=" basic-header">
        <span class = 'basic-left-info'>
            儿童特有信息
        </span>
        <span class = 'basic-right-up basic-right-up-child'>
            <i class="fa his-pencil"></i>修改
        </span>
    </div>
    <div class="basic-form-content basic-form-content-child">
        
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($submeterModel, 'parent_education')->dropDownList(PatientSubmeter::$getParentEducation,['prompt' => '请选择']) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($submeterModel, 'parent_occupation')->dropDownList(PatientSubmeter::$getParentOccupation,['prompt' => '请选择']) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($submeterModel, 'parent_marriage')->dropDownList(PatientSubmeter::$getParentMarriage,['prompt' => '请选择']) ?>
            </div>
        </div>
        <div class = 'row'>
            <div class = 'col-sm-4'>
                <?= $form->field($submeterModel, 'guardian')->dropDownList(PatientSubmeter::$getGuardian,['prompt' => '请选择']) ?>
            </div>
            <div class = 'col-sm-4'>
                <?= $form->field($submeterModel, 'other_guardian')->textInput(['maxlength' => true,'style'=>'margin-top: 5px;'])->label(' ') ?>
            </div>
        </div>

        <?php echo $this->render('_childbirthPregnancy', ['model' => $submeterModel,'form' => $form]) ?>

        <div class="form-group basic-btn">
            <?= Html::button('取消', ['class' => 'btn btn-cancel btn-form btn-cancel-child']) ?>
            <?= Html::submitButton('保存', ['class' => 'btn btn-default btn-form child-submit']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php $this->registerJs("
    $('#child-pjax .form-control').attr({'disabled': true});
    $('[type=radio]').attr({'disabled': true});
    $('[type=checkbox]').attr({'disabled': true});
") ?>
<?php Pjax::end() ?>    
