<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\patient\models\PatientRecord */
/* @var $form ActiveForm */
?>
<div class="end">

    <?php $form = ActiveForm::begin(['options' =>['style' => 'margin-top:5px;']]); ?>

        <?= $form->field($model, 'type_description')->textInput(['disabled' => true])->label('本次就诊服务类型：') ?>

        <?= $form->field($model, 'price')->textInput(['disabled' => true])->label('本次就诊诊金金额：') ?>

        <?= $form->field($model, 'fee_remarks')->textInput(['maxlength' => true,'placeholder'=>'请填写备注，不超过25个字'])->label('备注：') ?>
        <input type="hidden" id="cardrecharge-submitType" class="form-control cardSubmitType" name="PatientRecord[submitType]" value="1">
    
    <?php ActiveForm::end(); ?>

</div><!-- end -->

<?php
$this->registerCss("
                .follow-create{
                    border: 1px solid #76a6ef;
                    color: #76a6ef;
                }
                .modal-footer .form-group .follow-create:hover,.modal-footer .form-group .follow-create:focus{
                    color: #76a6ef;
                    opacity: 0.8;
                }
                .modal-footer .form-group .btn-form-custom{
                    margin-top: 0px;
                    margin-bottom: 15px !important;
                }
")
?>
