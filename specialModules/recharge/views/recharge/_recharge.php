<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\specialModules\recharge\models\CardFlow;
use app\common\Common;
use yii\helpers\Url;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\specialModules\recharge\models\CardRecharge */
/* @var $form yii\widgets\ActiveForm */
$baseUrl = Yii::$app->request->baseUrl;
$attribute = $model->attributeLabels();
$payTypeDis = 'block';
if ($model->isDonation) {
    $donationFeeDisplay = 'block';
} else {
    $donationFeeDisplay = 'none';
}
$oldAmount = Common::num(($cardBasic['f_donation_fee'] + $cardBasic['f_card_fee']));
if ($model->donation_fee == 0) {
    $model->donation_fee = '';
}
AppAsset::addCss($this, '@web/public/css/recharge/history.css');
?>

<div class="card-recharge-form-pay">

    <?php
    $form = ActiveForm::begin([
                'action' => Url::to(['@rechargeIndexRecharge', 'id' => $model->record_id]),
                'options' => ['class' => 'form-horizontal-recharge'],
                'fieldConfig' => [
                    'template' => "<div class='col-xs-3 col-sm-3 text-left pay-option'>{label}</div><div class='col-xs-9 col-sm-9'>{input}<div class='col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0'>{error}</div></div>",
                ]
    ]);
    ?>
    <?= $this->render('_stepTab', ['step' => 1]) ?>
    <?= $form->field($model, 'total_amount')->textInput(['maxlength' => 10, 'autocomplete' => 'off'])->label($attribute['total_amount'] . '<span class="label-required">*</span>') ?>
    <?= Html::hiddenInput('Order[oldAmount]', $model->total_amount) ?>
    <?= Html::hiddenInput('Order[oldDonation]', $model->donation_fee) ?>
    <div class="row order-isdonationfee">
        <div class="col-md-12">
            <?php // echo  Html::checkbox('donation', 0, ['class' => 'donation'])  ?>
            <?= $form->field($model, 'isDonation')->checkbox(['class' => 'donation-amount-checkbox'])->label(false) ?>
        </div>
    </div>
    <div class="row order-donation_fee" style="display: <?= $donationFeeDisplay ?>">
        <div class="col-md-12">
            <?= $form->field($model, 'donation_fee')->textInput(['maxlength' => 10, 'autocomplete' => 'off']) ?>
        </div>
    </div>
    <div class="form-group padd-bot-0  col-md-12">
        预计卡内余额（元）：<span class="expect-amount-num"><?= $oldAmount ?></span>
    </div>
    <div class="clearfix"></div>
    <?php if (isset($model->errors['is_upgrade']) && $model->errors['is_upgrade']): ?>
        <?php
        $data = $model->errors['is_upgrade'][0];
        ?>
        <div class="order-upgrade-area">
            <div class="form-group">
                <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                    <label class="control-label" for="chargerecord-cash">原卡种</label>
                </div>
                <div class="col-xs-9 col-sm-9 line-h"><?= Html::encode($cardBasic['f_category_name']) ?></div>
            </div>


            <div class='row'>
                <div class="col-md-12">
                    <div class="form-group field-order-is-upgrade">
                        <div class="col-xs-3 col-sm-3 text-left pay-option">可升级</div>
                        <div class="col-xs-9 col-sm-9 payment-upgrade">
                            <input type="hidden" class="order-isupgrade" name="Order[is_upgrade]" value="<?= $model->is_upgrade ?>">
                            <input type="hidden"  name="Order[upgradeCheck]" value="1">
                            <label>
                                <input type="checkbox" class="order-check-upgrade"  class="payment-checkbox" name="upgrade" <?= $model->is_upgrade == 1 ? 'checked' : '' ?>> 
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

    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
   var oldAmount= $oldAmount;
   require(["$baseUrl/public/js/recharge/record.js?v=$versionNumber"], function (main) {
        main.init();
    });
JS;
$this->registerJs($js);
?>
