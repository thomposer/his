<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\specialModules\recharge\models\CardFlow;
use app\common\Common;

/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\CardRecharge */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
$attribute = $model->getAttributes();
$payTypeDis = 'block';
if ($model->f_record_type == 1) {
    $payTypeDis = 'block';
} else {
    $payTypeDis = 'none';
}
if ($model->isDonation) {
    $donationFeeDisplay = 'block';
} else {
    $donationFeeDisplay = 'none';
}
if ($model->f_record_type == 1 || !$model->f_record_type) {
    $isdonationDisplay = 'block';
} else {
    $isdonationDisplay = 'none';
}
$oldAmount = Common::num(($cardBasic['f_donation_fee'] + $cardBasic['f_card_fee']));
$donationFee = $cardBasic['f_donation_fee'];
$cardFee = $cardBasic['f_card_fee'];
$type = $model->f_record_type ? $model->f_record_type : 1;
?>

<div class="card-recharge-form col-md-12">

    <?php
    $form = ActiveForm::begin([
                'id' => 'card-recharge-record-form',
    ]);
    ?>
    <?= $form->field($model, 'f_flow_item')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'f_record_type')->dropDownList(CardFlow::$getEditRecordType) ?>
    <div class="row f_pay_type_dis" style="display: <?= $payTypeDis ?>">
        <div class="col-md-12">
           <?= $form->field($model, 'f_pay_type')->dropDownList(CardFlow::$getPayType,['prompt'=>'请选择']) ?>
        </div>
    </div>
    <?= $form->field($model, 'f_record_fee')->textInput(['maxlength' => 10, 'autocomplete' => 'off']) ?>
    <?= Html::hiddenInput('CardFlow[oldAmount]', $model->f_record_fee) ?>
    <?= Html::hiddenInput('CardFlow[oldDonation]', $model->donationFee) ?>
    <div class="row cardflow-isdonationfee" style="display: <?= $isdonationDisplay ?>">
        <div class="col-md-12">
            <?php // echo  Html::checkbox('donation', 0, ['class' => 'donation'])  ?>
            <?= $form->field($model, 'isDonation')->checkbox(['class' => 'donation-amount-checkbox']) ?>
        </div>
    </div>
    <div class="row cardflow-isEmpty">
        <div class="col-md-12">
            <?= $form->field($model, 'isEmpty')->checkbox(['class' => 'donation-amount-checkbox']) ?>
        </div>
    </div>
    <div class="row cardflow-returnDonation">
        <div class="col-md-12">
            <?= $form->field($model, 'returnDonation')->checkbox(['class' => 'donation-amount-checkbox']) ?>
        </div>
    </div>
    <div class="row cardflow-donationfee" style="display: <?= $donationFeeDisplay ?>">
        <div class="col-md-12">
            <?= $form->field($model, 'donationFee')->textInput(['maxlength' => 10, 'autocomplete' => 'off']) ?>
        </div>
    </div>
    <div class="form-group expect-amount-text">
        预计卡内余额（元）：<span class="expect-amount-num"><?= $oldAmount ?></span>
    </div>
    <div class="clearfix"></div>
    <?php if (isset($model->errors['isUpgradeRecord']) && $model->errors['isUpgradeRecord']): ?>
        <?php
        $data = $model->errors['isUpgradeRecord'][0];
        $model->isUpgradeRecord=1;
        ?>
        <div class="order-upgrade-area-record">
            <!--            <div class="form-group">
                            <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                                <label class="control-label" for="chargerecord-cash">原卡种</label>
                            </div>
                            <div class="col-xs-9 col-sm-9 line-h"><?php // echo Html::encode($cardBasic['f_category_name'])  ?></div>
                        </div>-->


            <div class='row'>
                <div class="col-md-12">
                    <div class="form-group field-order-is-upgrade">
                        <div class="col-xs-1 col-sm-1 text-left pay-option">可升级</div>
                        <div class="col-xs-10 col-sm-10 payment-upgrade">
                            <input type="hidden" class="order-isupgrade" name="CardFlow[isUpgradeRecord]" value="<?= $model->isUpgradeRecord ?>">
                            <input type="hidden"  name="Order[upgradeCheck]" value="1">
                            <label>
                                <input type="checkbox" class="order-check-upgrade"  class="payment-checkbox" name="upgrade" <?= $model->isUpgradeRecord == 1 ? 'checked' : '' ?>> 
                                升级
                                <span class="payment-upgrade-card"><?= Html::encode($data[2]) ?></span>
                                <span class="payment-upgrade-card-gray">(入账已累计<?= $data[1] ?>元)</span>
                            </label>
                            <div class="col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0">
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>                
                </div>
            </div>
        </div>
    <?php endif; ?>


    <?= $form->field($model, 'f_remark')->textarea(['maxlength' => 50, 'rows' => 4]) ?>


    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
   var cardFee = $cardFee;
   var donationFee = $donationFee;
   var payType = $type;
   var oldAmount= $oldAmount;
   require(["$baseUrl/public/js/recharge/record.js?v=$versionNumber"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>
