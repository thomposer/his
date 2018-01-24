<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\common\AutoLayout;
use app\modules\charge\models\ChargeRecord;
use app\common\Common;
use yii\helpers\Url;
use yii\helpers\Json;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$attributes = $flowModel->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/recharge/payment.css');
AppAsset::addCss($this, '@web/public/css/recharge/history.css');
if ($flowModel->f_income == 0) {
    $flowModel->f_income = '';
}
?>

<div class="card-recharge-form-pay">
    <?php
    $form = ActiveForm::begin([
                'action' => Url::to(['@rechargeIndexRecharge', 'id' => $flowModel->f_record_id]),
                'options' => ['class' => 'form-horizontal-payment'],
                'fieldConfig' => [
                    'template' => "<div class='col-xs-3 col-sm-3 text-left pay-option'>{label}</div><div class='col-xs-9 col-sm-9'>{input}<div class='col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0'>{error}</div></div>",
                ]
    ]);
    ?>
    <?= $this->render('_stepTab', ['step' => 2]) ?>
    <?php if ($model->total_amount != 0): ?>
        <div class='charge-type-total'>
            <div class="recharge-banner-wrapper">
                <div class='recharge-banner'>
                    <div class='text-price'>实际应付</div>
                    <div class='price'><i class='fa fa-cny'></i><?= Common::num($model->total_amount) ?></div>
                </div>
            </div>
            <div class="chargerecord-cash-wrapper clearfix">
                <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                    <label class="control-label" for="chargerecord-cash">充值前卡种</label>
                </div>
                <div class="col-xs-9 col-sm-9 line-h"><?= Html::encode($oldCard) ?></div>
            </div>
            <?php if ($flowModel->isUpgrade == 1 && $upgradeCard): ?>
                <div class='row'>
                    <div class="col-md-12">
                        <div class="form-group field-cardflow-isupgrade">
                            <div class="col-xs-3 col-sm-3 text-left pay-option">充值后卡种</div>
                            <div class="col-xs-9 col-sm-9 payment-upgrade">
                                <input type="hidden" name="CardFlow[isUpgrade]" value="<?= $flowModel->isUpgrade ?>">
                                <label>
                                    将升级 
                                    <span class="payment-upgrade-card"><?= Html::encode($upgradeCard) ?></span>
                                </label>
                                <div class="col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0">
                                    <div class="help-block"></div>
                                </div>
                            </div>
                        </div>                
                    </div>
                </div>
            <?php endif; ?>
            <div class='row'>
                <div class='col-md-12'>
                    <div class="form-group field-cardflow-type">
                        <div class="col-xs-3 col-sm-3 text-left pay-option"><label class="control-label">支付方式</label></div>
                        <div class="col-xs-9 col-sm-9">
                            <input type="hidden" name="CardFlow[f_pay_type]" class="active-type" value=<?= $flowModel->f_pay_type ?>>
                            <input type="hidden" name="CardFlow[scanMode]" class="active-scan-mode" value=<?= $flowModel->scanMode ?>>
                            <div id="cardflow-type">
                                <?php foreach ($type as $k => $v): ?>
                                    <label type="<?= $k ?>" class="pay-type <?= $k == $flowModel->f_pay_type ? 'active' : '' ?>"> <?= $v ?></label>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0"><div class="help-block"></div></div></div>
                    </div>  
                    <?= Html::hiddenInput('CardFlow[f_record_fee]', $flowModel->f_record_fee) ?>
                    <?= Html::hiddenInput('CardFlow[isDonation]', $flowModel->isDonation) ?>
                    <?= Html::hiddenInput('CardFlow[upgradeCheck]', $flowModel->upgradeCheck) ?>
                    <?= Html::hiddenInput('CardFlow[donationFee]', $flowModel->donationFee) ?>
                    <?= Html::hiddenInput('CardFlow[f_record_type]', $flowModel->f_record_type) ?>
                    <?= Html::hiddenInput('CardFlow[orderSn]', $flowModel->orderSn) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-12'>
                    <?= $form->field($flowModel, 'f_income')->textInput(['maxlength' => 10, 'type' => 'string'])->label($attributes['f_income'] . '<span class = "label-required">*</span>') ?>
                </div>
            </div>
            <div class="row">
                <div class='col-md-12'>
                    <div class="form-group field-cardflow-f_income">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-cash">找零</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 cash card"><?= '¥' . Common::num($flowModel->f_income - $model->total_amount) ?></div>
                    </div>
                    <div class="form-group field-order-card">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-card">刷卡支付</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 col-md-3 card alipay"><?= '¥' . $model->total_amount ?></div>
                    </div>
                    <!-- <div class="form-group field-order-wechat">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-wechat">支付金额</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 col-md-3 card alipay"><?= '¥' . $model->total_amount ?></div>
                    </div> -->
                     <div class="form-group field-order-wechat">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-wechat">微信支付</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 card alipay">
                            <label class="scan-code active" mode="1">扫码枪扫码</label>
                            <label class="scan-code" mode="2">用户扫码</label>
                        </div>
                    </div>
                    <div class="form-group field-order-wechat">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="cardflow-alipay">&nbsp;</label>
                        </div>     
                        <div class="scan-mode scan-mode-first" id='wechatPayInput'>
                            <div class="col-xs-9 col-sm-9 scan-desc">
                                注：请用<span class="notice">扫码枪扫描</span>用户的付款码或者<span class="notice">输入</span>条形编码
                            </div>
                            <?= $form->field($flowModel, 'wechatAuthCode')->textInput(['maxlength' => 18])->label(false) ?>
                        </div>
                        <div class="col-xs-9 col-sm-9 charge-suc scan-mode-second" id='wechatPayCode'></div>
                    </div>
                    <!-- <div class="form-group field-order-alipay">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="cardflow-alipay">支付金额</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 card alipay"><?= '¥' . $model->total_amount ?></div>
                    </div> -->
                    <div class="form-group field-order-alipay">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="cardflow-alipay">支付宝支付</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 card alipay">
                            <label class="scan-code active" mode="1">扫码枪扫码</label>
                            <label class="scan-code" mode="2">用户扫码</label>
                        </div>
                    </div>
                    <div class="form-group field-order-alipay">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="cardflow-alipay">&nbsp;</label>
                        </div>     
                        <div class="scan-mode scan-mode-first" id='alipayInput'>
                            <div class="col-xs-9 col-sm-9 scan-desc">
                                注：请用<span class="notice">扫码枪扫描</span>用户的付款码或者<span class="notice">输入</span>条形编码
                            </div>
                            <?= $form->field($flowModel, 'alipayAuthCode')->textInput(['maxlength' => 18])->label(false) ?>
                        </div>
                        <div class="col-xs-12 col-sm-12 charge-suc scan-mode-second" id='alipayCode'></div>
                        <div class style="clear:both;"></div>
                    </div>
                    <div class="form-group field-order-meituan">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-meituan">美团支付</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 col-md-3 card meituan"><?= '¥' . $model->total_amount ?></div>
                    </div>
                </div>
            </div>
            <?php if ($flowModel->f_pay_type == 1 || $flowModel->f_pay_type == 2): ?>
                <div class='row'>
                    <div class='col-md-12'>
                        <?= $form->field($flowModel, 'f_remark')->textarea(['rows' => 4, 'maxlength' => 50]) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php ActiveForm::end(); ?>
    <div id="print-charge-preview"></div>
</div>
<script type="text/javascript">
    var type = "<?= $flowModel->f_pay_type; ?>";
    var baseUrl = "<?= $baseUrl; ?>";
    var aliPayUrl = "<?= $aliPayUrl ?>";
    var wechatUrl = "<?= $wechatUrl ?>";
    var checkUrl = "<?= Url::to(['@apiChargeCheck']) ?>";
    var outTradeNo = "<?= $outTradeNo; ?>";
    var price = "<?= $model->total_amount ?>";
    var apiRechargeCheckUrl = '<?= Url::to(['@apiRechargeCheck']) ?>';
    require([baseUrl + "/public/js/recharge/payment.js"], function (main) {
        main.init();
        window.main = main;
    });
</script>
