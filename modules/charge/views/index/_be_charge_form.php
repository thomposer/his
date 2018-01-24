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
$attributes = $model->attributeLabels();
$baseUrl = Yii::$app->request->baseUrl;
$versionNumber = Yii::getAlias("@versionNumber");
AppAsset::addCss($this, '@web/public/css/check/print.css');
//print_r($model);exit;
?>

<div class="charge-record-form">
    <div class='cost-bg'>
        <p class='text-price'>实际应付</p>
        <p class='price'><i class='fa fa-cny'></i><?= Common::num($allPrice) ?></p>
    </div>
    <?php
    $form = ActiveForm::begin([
                'id' => 'charge-form',
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "<div class='col-xs-3 col-sm-3 text-left pay-option'>{label}</div><div class='col-xs-9 col-sm-9'>{input}<div class='col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0'>{error}</div></div>",
                ]
    ]);
    ?>
    <div class='row hide'>
        <div class='col-md-12'>
            <?= $form->field($model, 'price')->hiddenInput(['maxlength' => true, 'readonly' => true])->label($attributes['price'] . '<span class = "label-required">*</span>') ?>
        </div>
    </div>
    <?php if ($allPrice != 0): ?>
        <div class='charge-type-total'>
            <div class='row'>
                <div class='col-md-12'>
                    <?php // echo $form->field($model, 'type')->radioList($type)  ?>
                    <div class="form-group field-chargerecord-type">
                        <div class="col-xs-3 col-sm-3 text-left pay-option"><label class="control-label" style = "padding-top: 12px;">支付方式</label></div>
                        <div class="col-xs-9 col-sm-9">
                            <input type="hidden" name="ChargeRecord[type]" class="active-type" value=<?= $model->type ?>>
                            <input type="hidden" name="ChargeRecord[scanMode]" class="active-scan-mode" value=<?= $model->scanMode ?>>
                            <div id="chargerecord-type">
                                <?php foreach ($type as $k => $v): ?>
                                    <label type="<?= $k ?>" class="pay-type <?= $k == $model->type ? 'active' : '' ?>"> <?= $v ?></label>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-xs-12 col-xs-offset-3 col-sm-12 col-sm-offset-0"><div class="help-block"></div></div></div>
                    </div>  
                </div>
            </div>
            <div class='row'>
                <div class='col-md-12'>
                    <?= $form->field($model, 'cash')->textInput(['maxlength' => 8, 'type' => 'string'])->label($attributes['cash'] . '<span class = "label-required">*</span>') ?>
                    <div class="form-group field-chargerecord-cash">
                        <div class="col-xs-3 col-sm-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-cash">找零</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 cash card"><?= '¥-' . Common::num($allPrice) ?></div>
                    </div>
                    <div class="form-group field-chargerecord-card">
                        <div class="col-xs-3 col-sm-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-cash">刷卡支付</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 card alipay"><?= '¥' . Common::num($allPrice) ?></div>
                    </div>
                    <div class="form-group field-chargerecord-wechat">
                        <div class="col-xs-3 col-sm-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-wechat">微信支付</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 card alipay">
                            <?php // '¥' . $model->price ?>
                            <label class="scan-code active" mode="1">扫码枪扫码</label>
                            <label class="scan-code" mode="2">用户扫码</label>
                        </div>
                    </div>
                    <div class="form-group field-chargerecord-wechat">
                        <div class="col-xs-3 col-sm-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-wechat">&nbsp;</label>
                        </div>
                        <div class="scan-mode scan-mode-first" id='wechatPayInput'>
                            <div class="col-xs-9 col-sm-9 ">
                                注：请用<span class="notice">扫码枪扫描</span>用户的付款码或者<span class="notice">输入</span>条形编码
                            </div>
                            <?= $form->field($model, 'wechatAuthCode')->textInput(['maxlength' => 18])->label(false) ?>
                        </div>
                        <div class="col-xs-9 col-sm-9 scan-mode scan-mode-second"  id='wechatPayCode'></div>
                    </div>
                    <div class="form-group field-chargerecord-alipay">
                        <div class="col-xs-3 col-sm-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-alipay">支付宝支付</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 card alipay">
                            <?php // '¥' . $model->price ?>
                            <label class="scan-code active" mode="1">扫码枪扫码</label>
                            <label class="scan-code" mode="2">用户扫码</label>
                        </div>
                    </div>
                    <div class="form-group field-chargerecord-alipay">
                        <div class="col-xs-3 col-sm-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-alipay">&nbsp;</label>
                        </div>     
                        <div class="scan-mode scan-mode-first" id='alipayInput'>
                            <div class="col-xs-9 col-sm-9 ">
                                注：请用<span class="notice">扫码枪扫描</span>用户的付款码或者<span class="notice">输入</span>条形编码
                            </div>
                            <?= $form->field($model, 'alipayAuthCode')->textInput(['maxlength' => 18])->label(false) ?>
                        </div>
                        <div class="col-xs-9 col-sm-9 scan-mode scan-mode-second"  id='alipayCode'></div>
                    </div>

                    <div class = 'recharge-card-pay'>
                        <div class = 'form-group recharge-card-radio'>
                            <div id="recharge-card">
                                <div class="card-select">
                                    <span style="margin-right:25px;">会员卡类型</span>
                                    <label class="card-type-select"><input type="radio" name="recharge-card">充值卡</label>
                                    <label class="card-type-select"><input type="radio" name="service-card">服务卡</label>
                                    <label class="card-type-select"><input type="radio" name="package-card">套餐卡</label>
                                </div>
                               

                                <div class="card-content">
                                    <div class="card-content-list recharge-card" style="display:none;">
                                            <?php if (!empty($cardInfo)): ?>
                                                <span class = "cardTypeLabel">
                                                	<input type="hidden" name="ChargeRecord[cardType]" value="" >
                                                    <?php foreach ($cardInfo as $v): ?>
                                                        <div class = 'recharge-card-div' data-id = "<?= $v['card_id'] ?>">
                                                        	
                                                            <p class = 'overflow-multiple-one'><?= Html::encode($v['name']); ?></p>
<!--                                                             <span class="clearfix"></span> -->
			
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
                                                </span>            
                                            <?php else: ?>
                                                <span class="empty-tips" style="color: #FF5000;">该患者的手机号下，未检测到充值卡，请购卡或更新手机号
                                            <?php endif; ?>

                                        </div>
                                        <div class="row recharge-checkbox-border">
                                            <?php if ($firstFreeChance == 1): ?>
                                                <div class="label-checkbox col-md-12"><label><input type="checkbox" id="charge-first-free" name="ChargeRecord[firstDiagnosisFree]" value="1">首单减免诊金</label></div>	
                                            <?php endif; ?>
                                            <div class="label-checkbox col-md-12"><label><input type="checkbox" id="charge-card-used" name="ChargeRecord[originalPrice]" value="1">不使用卡折扣</label></div>	
                                        </div> 
                                        <div class = 'recharge-card-solid'></div>
                                        <div class = "recharge-total-price">
                                       		充值卡支付：<span id = 'recharge-total-price-span'></span>
                                       		<p class = 'recharge-discount-price'>优惠金额：<span id = 'discount-total-price-span'></span></p>
                                		</div> 
                                    <div class="card-content-list service-card" style="display:none;">
                                        <?php if($serviceCardInfoList): ?>
                                            <?php foreach($serviceCardInfoList as $cardInfo): ?>
                                                <div class="service-card-info">
                                                    <div class="service-card-name"><span><?= $cardInfo['cardName'] ?></span><span class="fr" style="font-size:14px;padding-right: 20px;"><?= '验证码：'.$cardInfo['idCode'] ?></span></div>
                                                    <?php foreach($cardInfo['serviceList'] as $serviceInfo): ?>
                                                        <div class="service-card-service-list card-service-list">
                                                        <?php if($serviceInfo['serviceLeft'] == 0): ?>
                                                            <div class="service-card-service-item card-service-item">
                                                                <div class="service-card-service-title" style="color: #97A3B6;width: 100%;">
                                                                    <input type="checkbox" disabled name="ServiceCardServiceId[<?= $cardInfo['cardId'] ?>][]" value="<?= $serviceInfo['serviceId'] ?>"> 
                                                                    <span class="service-card-service-name"><?= $serviceInfo['serviceName'] ?></span>
                                                                    （剩下 <span class="service-card-service-time card-service-time"><?= $serviceInfo['serviceLeft'] ?></span> 次）
                                                                </div>
                                                                <div style="clear:both;"></div>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="service-card-service-item card-service-item">
                                                                <div class="service-card-service-title" style="<?= $disabled ? 'color: #97A3B6;' : '' ?>">
                                                                    <input type="checkbox" name="ServiceCardServiceId[<?= $cardInfo['cardId'] ?>][]" value="<?= $serviceInfo['serviceId'] ?>"> 
                                                                    <span class="service-card-service-name"><?= $serviceInfo['serviceName'] ?></span>
                                                                    （剩下 <span class="service-card-service-time card-service-time"><?= $serviceInfo['serviceLeft'] ?></span> 次）
                                                                </div>
                                                                <div >扣减 <input name="ServiceCardServiceTime[<?= $cardInfo['cardId'] ?>][<?= $serviceInfo['serviceId'] ?>]" disabled class="service-card-service-input card-service-input" type="text"> 次</div>
                                                                <div style="clear:both;"></div>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="help-block" style="color: rgb(255, 80, 0);padding-left: 345px;"></div>
                                                        </div>
                                                    <?php endforeach; ?>

                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="empty-tips" style="color: #FF5000;">您当前没有可用于支付的服务卡。</span>
                                        <?php endif;?>
                                    </div>
                                    <div class="card-content-list package-card" style="display:none;">
                                        <?php if($packageCardInfoList): ?>
                                            <?php foreach($packageCardInfoList as $cardInfo): ?>
                                                <div class="package-card-info">
                                                    <div class="package-card-name"><?= array_column($cardInfo, 'cardName')[0] ?></div>
                                                    <?php foreach($cardInfo as $serviceInfo): ?>
                                                        <div class="package-card-service-list card-service-list">
                                                        <?php if($serviceInfo['remainTime'] == 0): ?>
                                                            <div class="package-card-service-item card-service-item">
                                                                <div class="package-card-service-title" style="color: #97A3B6;width: 100%;">
                                                                    <input type="checkbox" disabled name="PackageCardServiceId[<?= $serviceInfo['cardId'] ?>][]" value="<?= $serviceInfo['serviceId'] ?>"> 
                                                                    <span class="package-card-service-name"><?= $serviceInfo['serviceName'] ?></span>
                                                                    （剩下 <span class="package-card-service-time card-service-time"><?= $serviceInfo['remainTime'] ?></span> 次）
                                                                </div>
                                                                <div style="clear:both;"></div>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="package-card-service-item card-service-item">
                                                                <div class="package-card-service-title" style="<?= $disabled ? 'color: #97A3B6;' : '' ?>">
                                                                    <input type="checkbox" name="PackageCardServiceId[<?= $serviceInfo['cardId'] ?>][]" value="<?= $serviceInfo['serviceId'] ?>"> 
                                                                    <span class="package-card-service-name"><?= $serviceInfo['serviceName'] ?></span>
                                                                    （剩下 <span class="package-card-service-time card-service-time"><?= $serviceInfo['remainTime'] ?></span> 次）
                                                                </div>
                                                                <div >扣减 <input name="ServiceTime[<?= $serviceInfo['serviceId'] ?>]" disabled class="package-card-service-input card-service-input" type="text"> 次</div>
                                                                <div style="clear:both;"></div>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="help-block" style="color: rgb(255, 80, 0);padding-left: 345px;"></div>
                                                        </div>
                                                    <?php endforeach; ?>

                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="empty-tips" style="color: #FF5000;">您当前没有可用于支付的套餐。</span>
                                        <?php endif;?>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                    </div>
                    <div class="form-group field-chargerecord-meituan">
                        <div class="col-xs-3 col-sm-3 text-left pay-option">
                            <label class="control-label" for="chargerecord-meituan">美团支付</label>
                        </div>
                        <div class="col-xs-9 col-sm-9 card"><?= '¥' . Common::num($allPrice) ?></div>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class = 'hide'>
        <?= $form->field($model, 'allPrice')->hiddenInput()->label(false); ?>
        <?= $form->field($model, 'out_trade_no')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'readonly')->hiddenInput()->label(false) ?>
        <input type="hidden" id="chargerecord-allprice" class="form-control" name="codeUrl[aliPayUrl]" value="<?= isset($aliPayUrl) ? $aliPayUrl : '' ?>">
        <input type="hidden" id="chargerecord-allprice" class="form-control" name="codeUrl[wechatUrl]" value="<?= isset($wechatUrl) ? $wechatUrl : '' ?>">
        <input type="hidden" id="chargerecord-allprice" class="form-control" name="codeUrl[type]" value=<?= isset($type) ? json_encode($type) : '' ?>>
        <input type="hidden" id="cardTotalPrice" class="form-control" name="cardTotalPrice" value = "">
        <input type="hidden" id="cardInfo" class="form-control" name="cardInfo" value = "">
    </div>

    <?php ActiveForm::end(); ?>
    <div id="print-charge-preview"></div>
</div>
<script type="text/javascript">
    var type = "<?= $model->type; ?>";
    var baseUrl = "<?= $baseUrl; ?>";
    var checkUrl = "<?= Url::to(['@apiChargeCheck']) ?>";
    var outTradeNo = "<?= $outTradeNo; ?>";
    var price = "<?= $model->price ?>";
    var allPrice = "<?= Common::num($allPrice) ?>";
    var returnUrl = "<?= Url::to(['@chargeIndexTradeLog']) ?>";
    var generateCodeUrl = '<?= Url::to(['@apiChargeGenerateCode']) ?>';
    var info = <?= json_encode($info, true); ?>;
    var discountType = '<?= $model->discount_type ?>';
    var readonly = '<?= $readonly ?>';
    var printInfoUrl = '<?= Url::to(['@apiChargePrintInfo']); ?>';
    var chargePrintDataUrl = '<?= Url::to(['@apiChargePrintData']); ?>';
    var apiChargeNewGenerateCodeUrl = '<?= Url::to(['@apiChargeNewGenerateCode']) ?>';
    var apiChargeGetCardDiscountPrice = '<?= Url::to(['@apiChargeGetCardDiscountPrice']) ?>';
    var hasGenerateCode = 0;
    require(["<?= $baseUrl ?>" + "/public/js/charge/chargeForm.js?v=" + '<?= $versionNumber ?>'], function (main) {
        main.init();
    });
</script>
