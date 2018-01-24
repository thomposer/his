<?php

namespace app\specialModules\recharge\models;

use Yii;
use yii\log\Logger;
use yii\helpers\Json;
use yii\helpers\Html;
use app\modules\spot_set\models\PaymentConfig;
use yii\web\Controller;
use app\common\base\BaseActiveRecord;
use yii\db\Exception;
use yii\db\Query;

/**
 * This is the model class for table "{{%order}}".
 */
class Order extends BaseActiveRecord
{

    public $isDonation = 0; //默认 不赠送
    public $upgradeCheck;
    public $oldAmount;
    public $oldDonation;

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%order}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('cardCenter');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['record_id', 'user_id', 'status', 'total_amount', 'out_trade_no', 'type', 'spot_id'], 'required'],
            [['record_id', 'user_id', 'status', 'create_time', 'update_time', 'type', 'spot_id', 'is_upgrade', 'upgradeCheck'], 'integer'],
            [['total_amount'], 'number', 'min' => '0.01'],
            [['total_amount', 'donation_fee'], 'trim'],
            [['oldAmount', 'oldDonation', 'donation_fee'], 'number'],
            [['total_amount'], 'number', 'max' => '999999.99'],
            [['status'], 'default', 'value' => 1],
            [['donation_fee'], 'default', 'value' => 0.0],
            [['out_trade_no'], 'string', 'max' => 64],
            [['subject'], 'string', 'max' => 255],
            ['total_amount', 'match', 'pattern' => '/^(\d*\.?[0-9]{0,2})$/', 'message' => '金额只能精确到分'],
            ['donation_fee', 'match', 'pattern' => '/^(\d*\.?[0-9]{0,2})$/', 'message' => '金额只能精确到分'],
            [['create_time', 'update_time', 'isDonation'], 'safe'],
            [['isDonation'], 'validateIsDonation', 'on' => 'recharge'],
            [['is_upgrade'], 'default', 'value' => 1],
            [['is_upgrade'], 'validateIsUpgrade', 'on' => 'recharge'],
            [['total_amount'], 'validateTotalAmount', 'on' => 'recharge'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'record_id' => '流水ID',
            'spot_id' => '诊所id',
            'user_id' => '操作人ID',
            'out_trade_no' => '订单号',
            'subject' => '订单名称',
            'type' => '订单类型(1-现金,2-刷卡,3-微信支付,4-支付宝支付)',
            'total_amount' => '充值金额',
            'status' => '订单支付状态[ 1-待支付 2-支付成功 3-支付失败 4-已退款 5-已过期 ]',
            'donation_fee' => '赠送金额(元)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'isDonation' => '赠送金额',
            'is_upgrade' => '是否升级 1/升级 2/不升级'
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['recharge'] = ['spot_id', 'total_amount', 'donation_fee', 'isDonation', 'is_upgrade', 'upgradeCheck', 'oldAmount', 'oldDonation'];
        return $scenarios;
    }

    public function validateIsDonation($attribute, $params) {
//        if (!$this->hasErrors()) {
        if ($this->$attribute == 1) {//需要升级金额
            if ($this->donation_fee == '') {
                $this->addError('donation_fee', "请填写赠送金额");
            }
            if ($this->donation_fee == 0) {
                $this->addError('donation_fee', "赠送金额的值必须不小于0.01。");
            }
            $feeEnd = CardRecharge::find()->select('f_donation_fee')->where(['f_physical_id' => $this->record_id])->asArray()->one();
            if ($feeEnd['f_donation_fee'] + $this->donation_fee > 999999.99) {
                $this->addError('donation_fee', "卡内赠送金额过大");
            }
        }
//        }
    }

    public function validateTotalAmount($attribute, $params) {
        $feeEnd = CardRecharge::find()->select('f_card_fee')->where(['f_physical_id' => $this->record_id])->asArray()->one();
        if ($feeEnd['f_card_fee'] + $this->total_amount > 999999.99) {
            $this->addError($attribute, "卡内金额过大");
        }
    }

    public function validateIsUpgrade($attribute, $params) {
//        if (!$this->hasErrors()) {
        if (empty($this->errors['total_amount']) && ( $this->oldAmount != $this->total_amount || !$this->oldAmount || ($this->isDonation && ($this->oldDonation != $this->donation_fee || !$this->oldDonation)))) {
            $upgradeCardInfo = CardRecharge::upgradeCard($this->record_id, false, $this->total_amount + $this->donation_fee);
            $totalAmount = $upgradeCardInfo ? $upgradeCardInfo['totalAmount'] : '';
            $upgradeCard = $upgradeCardInfo ? $upgradeCardInfo['f_category_name'] : '';
            \Yii::info('validateIsUpgrade:' . json_encode($upgradeCardInfo));
//        if ($totalAmount && $this->is_upgrade == 1) {//可自动升级
            if (($totalAmount)) {//可自动升级
                $this->addError($attribute, ['is_upgrade', $totalAmount, $upgradeCard]);
            }
        }

//        }
    }

    /**
     * 
     * @param type $orderSn 订单号 
     */
    public static function findModelBySn($orderSn) {
        $model = self::findOne(['out_trade_no' => $orderSn]);
        if ($model && $model != null) {
            return $model;
        } else {
            return null;
        }
    }

    /**
     * 
     * @var 支付方式
     * 1现金/2刷卡/3微信/4支付宝
     */
    public static $getPayType = [
        1 => '现金',
        2 => '刷卡',
        3 => '微信',
        4 => '支付宝',
        5 => '美团',
    ];

    /**
     * 
     * @param integer $recordId 充值卡id
     * @param \app\specialModules\recharge\models $model 
     * @param unknown $flowModel
     * @return unknown[]|\app\specialModules\recharge\models\支付方式[]|\app\specialModules\recharge\models\生成订单号[]|string[]|\app\specialModules\recharge\models\获取卡相关信息（包括卡种）[]|\app\specialModules\recharge\models\创建支付宝二维码[]|成功时返回，其他抛异常[]|boolean
     */
    public static function setOrderInfo($recordId, $model, $flowModel) {
        $dbTrans = $flowModel->getDb()->beginTransaction();
        try {
            $aliPayUrl = '';
            $wechatUrl = '';
            $outTradeNo = '';
            $type = [];
            //生成支付订单
            $outTradeNo = \app\modules\charge\models\Order::generateOutTradeNo(); //订单号
            $model->out_trade_no = $outTradeNo;
            $subject = Html::encode(Yii::$app->cache->get(Yii::getAlias('@spotName') . self::$staticSpotId . Yii::$app->user->identity->id)) . '充值';
            //$model->price=0.01;//测试先弄一分钱
            //将未支付订单更改状态为过期
            Order::updateAll(['status' => 5], ['record_id' => $recordId, 'status' => [1, 3]]);
            //生成订单记录
            self::addOrder($model->record_id, Yii::$app->user->identity->id, $outTradeNo, $subject, $model->total_amount, 0, $model->donation_fee, $model->is_upgrade);
            $type = self::$getPayType; //支付类型
            $paymentConfig = PaymentConfig::getPaymentConfigList();
            if (!empty($paymentConfig)) {//若支付配置有设置
                if (isset($paymentConfig[2])) {//若设置了支付宝配置，则生成支付宝订单二维码
                    $qrCode = self::generateQrCode($outTradeNo, $subject, $model->total_amount, $paymentConfig[2]);
                    Yii::$app->log->logger->log($qrCode, Logger::LEVEL_INFO);
                    if ($qrCode['errorCode'] == 0 && isset($qrCode['codeUrl'])) {
                        $aliPayUrl = $qrCode['codeUrl'];
                    }
                } else {
                    unset($type[4]);
                }
                if (isset($paymentConfig[1])) {//微信支付
                    $qrCode = self::generateWechatQrCode($outTradeNo, $subject, $model->total_amount, $recordId);
                    Yii::$app->log->logger->log($qrCode, Logger::LEVEL_INFO);
                    if ($qrCode) {
                        $wechatUrl = $qrCode;
                    }
                } else {
                    unset($type[3]);
                }
            } else {
                unset($type[3]);
                unset($type[4]);
            }
            $dbTrans->commit();
            $flowModel->f_pay_type = $flowModel->f_pay_type ? $flowModel->f_pay_type : 1;
            $flowModel->f_record_fee = $model->total_amount;
            $flowModel->isDonation = $model->isDonation;
            $flowModel->isUpgrade = $model->is_upgrade;
            $flowModel->upgradeCheck = $model->upgradeCheck;
            $flowModel->donationFee = $model->donation_fee;
            $flowModel->f_record_type = 1;
            $flowModel->orderSn = $outTradeNo;
            $upgradeCardInfo = CardRecharge::upgradeCard($recordId, false, $model->total_amount + $model->donation_fee);
            $upgradeCard = $upgradeCardInfo ? $upgradeCardInfo['f_category_name'] : '';
            $totalAmount = $upgradeCardInfo ? $upgradeCardInfo['totalAmount'] : '';
            $oldCardInfo = CardRecharge::getCardInfo($recordId);
            $oldCard = $oldCardInfo ? $oldCardInfo['f_category_name'] : '';
            return [
                'model' => $model,
                'flowModel' => $flowModel,
                'type' => $type,
                'outTradeNo' => $outTradeNo,
                'aliPayUrl' => $aliPayUrl, //支付宝二维码url
                'wechatUrl' => $wechatUrl, //微信二维码url
                'oldCard' => $oldCard,
                'upgradeCard' => $upgradeCard,
                'totalAmount' => $totalAmount,
            ];
        } catch (Exception $e) {
            $dbTrans->rollBack();
            return false;
        }
    }

    /**
     * 
     * @param integer $record_id 充值卡id
     * @param integer $user_id 操作人id
     * @param string $out_trade_no 订单号
     * @param string $subject 订单名称
     * @param decimal $total_amount 订单总金额
     * @param integer $type 订单类型(0-不祥 1-微信,2-支付宝)
     * @param decimal $donation_fee 赠送金额
     * @param integer $isUpgrade 是否自动升级(1-是,2-否)。默认为否
     * @return 添加一个支付订单
     */
    public static function addOrder($record_id, $user_id, $out_trade_no, $subject, $total_amount, $type = 0, $donation_fee = 0, $isUpgrade = 2) {
        $model = new static();
        $model->out_trade_no = $out_trade_no;
        $model->record_id = $record_id;
        $model->user_id = $user_id;
        $model->subject = $subject;
        $model->total_amount = $total_amount;
        $model->type = $type;
        $model->status = 1;
        $model->donation_fee = $donation_fee;
        $model->is_upgrade = $isUpgrade;
        $ret = $model->save();
        return $ret;
    }

    /**
     * @param 订单号 $outTradeNo
     * @param 订单名称 $subject
     * @param 价格 $totalAmount
     * @param 支付宝配置 $paymentConfig
     * @return 创建支付宝二维码
     * 
     */
    public static function generateQrCode($outTradeNo, $subject, $totalAmount, $paymentConfig) {
        include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/model/builder/ExtendParams.php");
        include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/model/builder/GoodsDetail.php");
        include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/model/builder/AlipayTradePrecreateContentBuilder.php");
        include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/service/AlipayTradeService.php");
//         Yii::$app->response->format = Response::FORMAT_JSON;
        $result['errorCode'] = 0;
        $result['msg'] = '';
        // (必填) 商户网站订单系统中唯一订单号，64个字符以内，只能包含字母、数字、下划线，
        // 需保证商户系统端不能重复，建议通过数据库sequence生成，
//         $outTradeNo = self::generateOutTradeNo();
        // (必填) 订单标题，粗略描述用户的支付目的。如“xxx品牌xxx门店当面付扫码消费”
//         $subject = $subject;
        // (必填) 订单总金额，单位为元，不能超过1亿元
        // 如果同时传入了【打折金额】,【不可打折金额】,【订单总金额】三者,则必须满足如下条件:【订单总金额】=【打折金额】+【不可打折金额】
//         $totalAmount = 0.1;
        // (不推荐使用) 订单可打折金额，可以配合商家平台配置折扣活动，如果订单部分商品参与打折，可以将部分商品总价填写至此字段，默认全部商品可打折
        // 如果该值未传入,但传入了【订单总金额】,【不可打折金额】 则该值默认为【订单总金额】- 【不可打折金额】
        //String discountableAmount = "1.00"; //
        // (可选) 订单不可打折金额，可以配合商家平台配置折扣活动，如果酒水不参与打折，则将对应金额填写至此字段
        // 如果该值未传入,但传入了【订单总金额】,【打折金额】,则该值默认为【订单总金额】-【打折金额】
        $undiscountableAmount = "";
        // 卖家支付宝账号ID，用于支持一个签约账号下支持打款到不同的收款账号，(打款到sellerId对应的支付宝账号)
        // 如果该字段为空，则默认为与支付宝签约的商户的PID，也就是appid对应的PID
        $sellerId = "";

        // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
        $body = $subject . '--充值金额为：' . $totalAmount;
        Yii::$app->log->logger->log($body, Logger::LEVEL_INFO);
        //商户操作员编号，添加此参数可以为商户操作员做销售统计
        $operatorId = "";
        // (必填) 商户门店编号，通过门店号和商家后台可以配置精准到门店的折扣信息，详询支付宝技术支持
        $storeId = "";
        // 支付宝的店铺编号
        $alipayStoreId = "";
        // 业务扩展参数，目前可添加由支付宝分配的系统商编号(通过setSysServiceProviderId方法)，系统商开发使用,详情请咨询支付宝技术支持
        $providerId = ""; //系统商pid,作为系统商返佣数据提取的依据
        $extendParams = new \ExtendParams();
        $extendParams->setSysServiceProviderId($providerId);
        $extendParamsArr = $extendParams->getExtendParams();
        // 支付超时，线下扫码交易定义为1天
        $timeExpress = "1d";
        // 商品明细列表，需填写购买商品详细信息，
        $goodsDetailList = array();
        // 创建一个商品信息，参数含义分别为商品id（使用国标）、名称、单价（单位为分）、数量，如果需要添加商品类别，详见GoodsDetail
        $goods1 = new \GoodsDetail();
        $goods1->setGoodsId('卡充值');
        $goods1->setGoodsName($subject);
        $goods1->setPrice($totalAmount);
        $goods1->setQuantity(1);
        //得到商品1明细数组
        $goods1Arr = $goods1->getGoodsDetail();
        $goodsDetailList = array($goods1Arr);
        //第三方应用授权令牌,商户授权系统商开发模式下使用
        $appAuthToken = ""; //根据真实值填写
        // 创建请求builder，设置请求参数
        $qrPayRequestBuilder = new \AlipayTradePrecreateContentBuilder();
        $qrPayRequestBuilder->setOutTradeNo($outTradeNo);
        $qrPayRequestBuilder->setTotalAmount($totalAmount);
        $qrPayRequestBuilder->setTimeExpress($timeExpress);
        $qrPayRequestBuilder->setSubject($subject);
        $qrPayRequestBuilder->setBody($body);
        $qrPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);
        $qrPayRequestBuilder->setExtendParams($extendParamsArr);
        $qrPayRequestBuilder->setGoodsDetailList($goodsDetailList);
        $qrPayRequestBuilder->setStoreId($storeId);
        $qrPayRequestBuilder->setOperatorId($operatorId);
        $qrPayRequestBuilder->setAlipayStoreId($alipayStoreId);
        //                 $qrPayRequestBuilder->setAppAuthToken($appAuthToken);
        $config = include(Yii::getAlias('@ServicePath') . "/pay/f2fpay/config/config.php");
        $config['app_id'] = $paymentConfig['appid'];
        $config['merchant_private_key'] = $paymentConfig['mchid']; //商户私钥
        $config['alipay_public_key'] = $paymentConfig['payment_key']; //支付宝密钥
        $config['notify_url'] = Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@apiPayRechargeIndex')]);
        Yii::$app->log->logger->log($config, Logger::LEVEL_INFO);
        // 调用qrPay方法获取当面付应答
        $qrPay = new \AlipayTradeService($config);
        $qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);
        //	根据状态值进行业务处理
        switch ($qrPayResult->getTradeStatus()) {
            case "SUCCESS":
                //echo "支付宝创建订单二维码成功:"."<br>---------------------------------------<br>";
                $response = $qrPayResult->getResponse();
                $result['codeUrl'] = $response->qr_code;
                return $result;
                break;
            case "FAILED":
                $content = '创建订单二维码失败，请检查配置项';
                if ($qrPayResult->getResponse()->code == 40002) {
                    $content = '您使用的商户私钥格式错误';
                }
                $result['errorCode'] = $qrPayResult->getResponse()->code;
                $result['msg'] = $content;
                return $result;
                break;
            case "UNKNOWN":
                $result['errorCode'] = $qrPayResult->getResponse()->code;
                $result['msg'] = '系统异常，状态未知!!!';
                return $result;
                break;
            default:
                $result['errorCode'] = 1001;
                $result['msg'] = '不支持的返回状态，创建订单二维码返回异常!!!';
                return $result;
                break;
        }
        return;
    }

    public static function generateWechatQrCode($outTradeNo, $subject, $totalAmount, $recordId) {
        include_once(Yii::getAlias('@ServicePath') . "/wechat/WxPay.Api.php");
        include_once(Yii::getAlias('@ServicePath') . "/wechat/WxPay.NativePay.php");

        // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
        $body = $subject . '--充值金额为：' . $totalAmount;
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($subject);
        $input->SetAttach($body);
        $input->SetOut_trade_no($outTradeNo); //订单ID
        $input->SetTotal_fee($totalAmount * 100);
//        $input->SetTotal_fee('1');//测试状态设置为1分
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 86400));
        $input->SetGoods_tag($subject);
        $notify_url = Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@apiCallbackRechargeNotify')]);
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($recordId); //商品ID
        $notify = new \NativePay();
        $result = $notify->GetPayUrl($input);
        $code_url = $result["code_url"];
        return $code_url;
    }

    /**
     *
     * @param 订单号 $out_trade_no
     * @param 支付类型(1-微信，2-支付宝) $type
     * @return 根据订单号以及支付类型来获取诊所支付配置信息
     */
    public static function getPaymentConfig($out_trade_no, $type) {

        $info = self::find()->select(['spot_id'])->where(['out_trade_no' => $out_trade_no])->asArray()->one();
        return PaymentConfig::find()->select(['appid', 'mchid', 'payment_key', 'spot_id'])->where(['spot_id' => $info['spot_id'], 'type' => $type])->asArray()->one();
    }

}
