<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\charge\models\ChargeRecord;
use app\common\Common;
use app\modules\charge\models\ChargeInfo;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;

?>

<div class="charge-record-form">
    
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "<div class='col-xs-2 col-sm-2 text-left'>{label}</div><div class='col-xs-10 col-sm-10'>{input}<div class='col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0'>{error}</div></div>",
        ]
    ]); ?>
    <div class = 'row'>
    <div class = 'col-md-12'>
    <?= $form->field($model, 'total_price')->textInput(['maxlength' => true,'readonly'=> true])->label($attributes['refund_total_price'].'<span class = "label-required">*</span>') ?>
    </div>
    </div>
    <div class = 'row'>
    <div class = 'col-md-12'>
        <?= $form->field($model, 'reason')->dropDownList(ChargeInfo::$getRefundChargeReason)->label($attributes['reason'].'<span class = "label-required">*</span>') ?>
    </div>
    <div class = 'col-md-12'>
        <?= $form->field($model, 'reason_description')->textarea(['maxlength' => true])->label($attributes['reason_description']) ?>
        <?= $form->field($model, 'allPrice')->hiddenInput()->label(false) ?>
        
    </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    var type = "<?= $model->type; ?>";
    var baseUrl = "<?= $baseUrl;?>";
    if(type == 1){
		$('.field-chargerecord-cash').css({'display':'block'});
        var result = sub((Math.floor($('#chargerecord-cash').val() * 100)/100),$('#chargerecord-price').val());
		$('.cash').html('￥'+toDecimal2(result));
    }
</script>
