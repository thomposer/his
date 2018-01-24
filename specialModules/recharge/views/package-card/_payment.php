<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\charge\models\ChargeRecord;
use app\common\Common;
use yii\helpers\Url;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\charge\models\ChargeRecord */
/* @var $form yii\widgets\ActiveForm */
$attributes = $orderModel->attributeLabels();
$packageCardModel = $model->getModel('membershipPackageCard');
$unionModel = $model->getModel('union');
$baseUrl = Yii::$app->request->baseUrl;
AppAsset::addCss($this, '@web/public/css/recharge/payment.css');
AppAsset::addCss($this, '@web/public/css/recharge/history.css');
if ($orderModel->income == 0) {
    $orderModel->income = '';
}

?>

<div class="card-recharge-form-pay">
    <?php
    $form = ActiveForm::begin([
                'action' => Url::to(['@rechargeIndexCreatePackageCard', 'step' => 2]),
                'options' => ['class' => 'form-horizontal-payment'],
                'fieldConfig' => [
                    'template' => "<div class='col-xs-3 col-sm-3 text-left pay-option'>{label}</div><div class='col-xs-9 col-sm-9'>{input}<div class='col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0'>{error}</div></div>",
                ]
    ]);
    ?>
    <div class="step-wrapper">
    <?= $this->render('_stepTab', ['step' => 2]) ?>
    </div>
    <?php if ($orderModel->total_amount != 0): ?>
        <div class='charge-type-total'>
            <div class="recharge-banner-wrapper">
                <div class='recharge-banner'>
                    <div class='text-price'>实际应付</div>
                    <div class='price'><i class='fa fa-cny'></i><?= Common::num($orderModel->total_amount) ?></div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-12'>
                    <div class="form-group field-cardflow-type">
                        <div class="col-xs-3 col-sm-3 text-left pay-option"><label class="control-label">支付方式</label></div>
                        <div class="col-xs-9 col-sm-9 prn">
                            <input type="hidden" name="CardOrder[type]" class="active-type" value=<?= $orderModel->type ?>>
                            <input type="hidden" name="CardOrder[scanMode]" class="active-scan-mode" value=<?= $orderModel->scanMode ?>>
                            <div id="cardflow-type">
                                <?php foreach ($type as $k => $v): ?>
                                    <label type="<?= $k ?>" class="pay-type <?= $k == $orderModel->type ? 'active' : '' ?>"> <?= $v ?></label>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0"><div class="help-block"></div></div></div>
                    </div>  
                    <?= $form->field($orderModel,'out_trade_no')->hiddenInput()->label(false) ?>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-12'>
                    <?= $form->field($orderModel, 'income')->textInput(['maxlength' => 10, 'type' => 'string'])->label($attributes['income'] . '<span class = "label-required">*</span>') ?>
                </div>
            </div>
            <div class="row">
                <div class='col-md-12'>
                    <div class="form-group field-cardflow-f_income">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-cash">找零</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 cash card"><?= '¥' . Common::num(($orderModel->income?$orderModel->income:0) - $orderModel->total_amount) ?></div>
                    </div>
                    <div class="form-group field-order-card">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-card">刷卡支付</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 col-md-3 card alipay"><?= '¥' . $orderModel->total_amount ?></div>
                    </div>
                    <!-- <div class="form-group field-order-wechat">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-wechat">支付金额</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 col-md-3 card alipay"><?= '¥' . $orderModel->total_amount ?></div>
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
                            <?= $form->field($orderModel, 'wechatAuthCode')->textInput(['maxlength' => 18])->label(false) ?>
                        </div>
                        <div class="col-xs-9 col-sm-9 charge-suc scan-mode-second" id='wechatPayCode'></div>
                    </div>
                    <!-- <div class="form-group field-order-alipay recharge-card-total-amount">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="cardflow-alipay">支付金额</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 card alipay"><?= '¥' . $orderModel->total_amount ?></div>
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
                            <?= $form->field($orderModel, 'alipayAuthCode')->textInput(['maxlength' => 18])->label(false) ?>
                        </div>
                        <div class="col-xs-12 col-sm-12 charge-suc scan-mode-second" id='alipayCode'></div>
                        <div class style="clear:both;"></div>
                    </div>
                    
                    <div class = 'recharge-card-pay'>
                        <div class = 'form-group recharge-card-radio'>
                        	<p>
								会员卡类型<input type="radio" class = 'recharge-card-type' value="0" checked >充值卡
							</p>
                            <div id="recharge-card">
                                <div class = "cardTypeLabel clearfix">
                                    <?php if (!empty($cardInfo)): ?>
                                        <input type="hidden" name="CardOrder[cardType]" value="" >
                                        <?php foreach ($cardInfo as $v): ?>
                                           <div class = 'recharge-card-div' data-id = "<?= $v['card_id'] ?>">
                                           <p class = 'overflow-multiple-one'><?= Html::encode($v['name']); ?></p>
                                           <?php $cardTagTotalInfo = '<p class = "cardTagInfo overflow-multiple">'; ?>
                                           <?php if (!empty($v['tagInfo'])): ?>
                                           <?php
                                                  foreach ($v['tagInfo'] as $value) {
                                                       $cardTagTotalInfo .= Html::encode($value['name']) . $value['discount'] . '%，';
                                                  }
                                            ?> 
                                            <?php endif; ?>
                                            <?php
                                                 $cardTagTotalInfo = trim($cardTagTotalInfo, '，');
                                                 $cardTagTotalInfo .= '</p>';
                                                 echo $cardTagTotalInfo;
                                           ?>
                                           <p>余额： <span class = 'totalFee'><?= $v['total_fee'] ?></span> </p>
	                                       </div>   
                                        <?php endforeach; ?>

                                    <?php else: ?>
                                        该患者的手机号下，未检测到充值卡，请购卡或更新手机号
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class = "recharge-card-total-price">
                        	
							<div class="row recharge-checkbox-border">
                                 
<!--                              	<div class="label-checkbox col-md-12"><label><input type="checkbox" id="charge-card-used" name="CardOrder[originalPrice]" value="1">不使用卡折扣</label></div>	 -->
                             </div> 
                             <div class = 'recharge-card-solid'></div>
                             <div class = "recharge-total-price">
                                 充值卡支付：<span id = 'recharge-total-price-span'></span>
                                 <p class = 'recharge-discount-price'>优惠金额：<span id = 'discount-total-price-span'>0.00</span></p>
                            </div> 
                        </div>
                    </div>
                    
                    <div class="form-group field-order-meituan">
                        <div class="col-xs-3 col-sm-3 col-md-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-meituan">美团支付</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 col-md-3 card alipay"><?= '¥' . $orderModel->total_amount ?></div>
                    </div>
                    
                </div>
            </div>
            <div class='row'>
                    <div class='col-md-12'>
                        <?= $form->field($orderModel, 'remark')->textarea(['rows' => 4, 'maxlength' => 50]) ?>
                    </div>
            </div>
        </div>
    <?php endif; ?>
    <?= $form->field($packageCardModel, 'package_card_id')->hiddenInput()->label(false) ?>
    <?= $form->field($packageCardModel, 'status')->hiddenInput()->label(false) ?>
    <?= $form->field($packageCardModel, 'remark')->hiddenInput()->label(false) ?>
    <?= $form->field($unionModel, 'patient_id')->hiddenInput()->label(false) ?>
    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    var type = "<?= $orderModel->type; ?>";
    var baseUrl = "<?= $baseUrl; ?>";
    var aliPayUrl = "<?= $aliPayUrl ?>";
    var wechatUrl = "<?= $wechatUrl ?>";
    var checkUrl = "<?= Url::to(['@apiChargePackageCardCheck']) ?>";
    var outTradeNo = "<?= $orderModel->out_trade_no; ?>";
    var price = "<?= $orderModel->total_amount ?>";
    var apiRechargeGetCardDiscountPrice = '<?= Url::to(['@apiRechargeGetCardDiscountPrice']) ?>';
    require([baseUrl + "/public/js/recharge/memberPayment.js"], function (main) {
        main.init();
        window.main = main;
    });
</script>
