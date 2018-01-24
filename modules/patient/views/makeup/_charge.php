<?php

use yii\widgets\ActiveForm;
use app\modules\charge\models\ChargeRecord;
use dosamigos\datetimepicker\DateTimePicker;
use app\modules\spot\models\MedicalFee;
use yii\helpers\ArrayHelper;
$versionNumber = Yii::getAlias("@versionNumber");
$baseUrl = Yii::$app->request->baseUrl;
$attribute = $model->attributeLabels();
if($model->discountType==1||empty($model->discountType)){
    $display='none';
}else {
    $display='block';
}
?>



<?php 
$css = <<<CSS
   #ajaxCrudModal .modal-body{
                padding: 0px 30px;
    }
    #ajaxCrudModal .form-group .btn-form {
         margin: 0px 10px 0px 10px !important;
    }
    .modal-footer .form-group{
        margin-bottom: 26px;
   }
CSS;
$this->registerCss($css);
?>
<?php
$form = ActiveForm::begin([
            'id' => 'makeupCharge'
        ]);
?>
<div class='row'>
    <div class="family-modal">
        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?=
                $form->field($model, 'chargeTime')->widget(
                        DateTimePicker::className(), [
                    'inline' => false,
                    'language' => 'zh-CN',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd hh:ii:ss',
                        'minuteStep' => 1,
                    ]
                        ]
                )->label($attribute['chargeTime'] . '<span class = "label-required">*</span>')
                ?>
            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'discountType')->dropDownList(ChargeRecord::$getDiscountType) ?>
            </div>
        </div>
        <div class = 'row discount' style=" display: <?= $display ?>">
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'discountPrice')->input('text', ['maxlength' => true]) ?>
            </div>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'discountReason')->input('text', ['maxlength' => true]) ?>
            </div>
        </div>

        <div class = 'row'>
            <div class = 'col-sm-6'>
                <?= $form->field($model, 'payType')->dropDownList(ChargeRecord::$getType)->label($attribute['payType'] . '<span class = "label-required">*</span>') ?>
            </div>
            <div class = 'col-sm-6'>
                
                <?= $form->field($model, 'medicalFee')->dropDownList(ArrayHelper::map(MedicalFee::getMedicalFeeList(), 'price', 'price',['prompt' => '请选择']))->label($attribute['medicalFee'] . '<span class = "label-required">*</span>') ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
   var baseUrl = '$baseUrl';
   require(["$baseUrl/public/js/patient/ump_record.js?v=$versionNumber"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>
