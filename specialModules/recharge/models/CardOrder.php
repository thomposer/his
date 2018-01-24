<?php

namespace app\specialModules\recharge\models;

use Yii;
use app\modules\spot\models\PackageServiceUnion;
use yii\helpers\Json;
use yii\log\Logger;
use yii\db\Exception;
use app\modules\spot_set\models\PaymentConfig;
use yii\db\Query;

/**
 * This is the model class for table "{{%card_order}}".
 *
 * @property integer $id
 * @property integer $card_id
 * @property integer $spot_id
 * @property integer $user_id
 * @property integer $type
 * @property string $out_trade_no
 * @property string $subject
 * @property string $total_amount
 * @property integer $status
 * @property integer $income
 * @property string $remark
 * @property integer $create_time
 * @property integer $update_time
 */
class CardOrder extends \app\common\base\BaseActiveRecord
{

    public $scanMode = 2; //扫码
    public $wechatAuthCode; //微信授权码
    public $alipayAuthCode; //支付宝授权码
    public $cash;
    public $cardType;//充值卡id

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%card_order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['card_id', 'spot_id', 'user_id', 'type', 'status', 'create_time', 'update_time','scanMode','cardType'], 'integer'],
            [['total_amount', 'income'], 'number'],
            [['total_amount', 'income'], 'default', 'value' => 0],
            [['out_trade_no'], 'string', 'max' => 64],
            [['subject', 'remark'], 'string', 'max' => 255],
            [['remark'], 'default', 'value' => ''],
            [['wechatAuthCode', 'alipayAuthCode'], 'string', 'max' => 18],
            [['wechatAuthCode', 'alipayAuthCode'], 'default', 'value' => ''],
            [['out_trade_no'], 'unique'],
            [['income'], 'validatePrice']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'card_id' => '卡ID',
            'spot_id' => '诊所ID',
            'user_id' => '操作人ID',
            'type' => '支付方式(1-现金,2-刷卡,3-微信支付,4-支付宝支付,5-会员卡)',
            'out_trade_no' => '订单号',
            'subject' => '订单名称',
            'total_amount' => '支付总金额(本次交易支付的订单金额，单位为人民币（元）)',
            'status' => '订单支付状态[ 1-待支付,2-支付成功,3-支付失败 ,4-已退款,5-已过期]',
            'income' => '实收现金(元)',
            'remark' => '备注',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
    
    public static $getType = [
        1 => '现金',
        2 => '刷卡',
        3 => '微信',
        4 => '支付宝',
        5 => '会员卡',
        6 => '美团',
    ];
    /**
     * 
     * @param unknown $attribute
     * @param unknown $params
     * @return 验证cash字段
     */
    public function validatePrice($attribute, $params) {
        if (!$this->hasErrors()) {
            if ($this->type == 1) {
                if ($this->income < $this->total_amount) {
                    $this->addError($attribute, '实收现金金额不能小于实际应付金额。');
                }
            }
        }
    }

    /**
     * 
     * @param 就诊流水id $record_id
     * @param 用户id $user_id
     * @param 订单号 $out_trade_no
     * @param 订单名称 $subject
     * @param 订单总金额 $total_amount
     * @param 订单类型(0-不祥 1-微信,2-支付宝) $type
     * @return 添加一个支付订单
     */
    public static function addOrder($cardId, $user_id, $out_trade_no, $subject, $total_amount, $type = 0) {
        $model = new static();
        $model->out_trade_no = $out_trade_no;
        $model->card_id = $cardId;
        $model->spot_id = self::$staticSpotId;
        $model->user_id = $user_id;
        $model->subject = $subject;
        $model->total_amount = $total_amount;
        $model->type = $type;
        $model->status = 1;
        $model->create_time = time();
        $model->update_time = time();
        return $model->save();
    }

    /**
     * @desc 保存会员卡服务类型信息和订单信息
     * @param integer $out_trade_no 订单号
     * @param integer $rows 表单数据
     * @param integer $type 支付方式
     * @param integer $income 实收金额
     * @param string  $remark 订单备注 
     */
    public static function saveOrder($out_trade_no, $rows, $type, $income = 0, $remark = '') {

        $membershipPackageCardModel = new MembershipPackageCard();
        $membershipPackageCardModel->spot_id = $rows['spotId'];
        $membershipPackageCardModel->package_card_id = $rows['package_card_id'];
        $membershipPackageCardModel->status = $rows['status'];
        $membershipPackageCardModel->remark = $rows['remark'];
        if($type == 3 || $type == 4){//微信／支付宝时，不验证
            $membershipPackageCardModel->save(false);
        }else{
            $membershipPackageCardModel->save();
        }
        if($membershipPackageCardModel->errors){
            return false;
        }
        \Yii::info('saveOrder error ' . json_encode($membershipPackageCardModel->errors), 'saveOrder');
        $unionModel = new MembershipPackageCardUnion();
        $unionModel->scenario = 'stepTwo';
        $unionModel->spot_id = $rows['spotId'];
        $unionModel->membership_package_card_id = $membershipPackageCardModel->id;
        $unionModel->patient_id = $rows['patient_id'];
        $unionModel->save(false);
        \Yii::info('saveOrder error ' . json_encode($unionModel->errors), 'saveOrder');
        $list = PackageServiceUnion::find()->select(['package_card_service_id', 'time'])->where(['package_card_id' => $rows['package_card_id'], 'spot_id' => $rows['parentSpotId']])->asArray()->all();
        $params = [];
        if (!empty($list)) {//保存会员卡与服务类型关系
            $insertRows = [];
            $service = [];
            $params[0] = ['membership_package_card_id' => $membershipPackageCardModel->id, 'service' => []];
            foreach ($list as $value) {
                $insertRows[] = [$rows['spotId'], $membershipPackageCardModel->id, $value['package_card_service_id'], $value['time'], $value['time'], time(), time()];
                $service[] = [
                    'package_card_service_id' => $value['package_card_service_id'],
                    'time' => $value['time']
                ];
            }
            $params[0]['service'] = $service;
            Yii::$app->db->createCommand()->batchInsert(MembershipPackageCardService::tableName(), ['spot_id', 'membership_package_card_id', 'package_card_service_id', 'total_time', 'remain_time', 'create_time', 'update_time'], $insertRows)->execute();
        }
        $orderModel = self::findOne(['out_trade_no' => $out_trade_no, 'spot_id' => $rows['spotId']]);
        $orderModel->card_id = $membershipPackageCardModel->id;
        $orderModel->status = 2;
        $orderModel->type = $type;
        $orderModel->income = $income;
        $orderModel->remark = $remark;
        MembershipPackageCardFlow::addFlow($rows['spotId'], $rows['patient_id'], '套餐卡购买', 2, $orderModel->type, 4, $orderModel->user_id, $params, 0, 0, $orderModel->total_amount, $remark);

        return $orderModel->save();
    }

    /**
     * @desc 生成微信支付的二维码URL
     * @param type $outTradeNo
     * @param type $subject
     * @param type $totalAmount
     * @param type $recordId
     * @return type 
     */
    public static function generateWechatQrCode($outTradeNo, $subject, $totalAmount, $rows) {
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
        $notify_url = Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@apiCallbackPackageCardNotify')]);
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($rows['package_card_id']); //商品ID (卡ID)
        $notify = new \NativePay();
        $result = $notify->GetPayUrl($input);
        Yii::info('generateWechatQrCode res' . json_encode($result));
        $code_url = $result["code_url"];
        return $code_url;
    }

    /**
     * @param 订单号 $outTradeNo
     * @param 订单名称 $subject
     * @param 价格 $totalAmount
     * @param 支付宝配置 $paymentConfig
     * @param 支付详情列表id $rows
     * @param 患者信息 $info
     * @return 创建支付宝二维码
     *
     */
    public static function generateQrCode($outTradeNo, $subject, $totalAmount, $paymentConfig, $rows) {
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

        $list['detail'] = $rows;
        // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
        $body = Json::encode($list);
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
        $goods1->setGoodsId($rows['package_card_id']);
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
        $config['notify_url'] = Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@apiPayPackageCard')]);
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

    /**
     *
     * @param 订单号 $out_trade_no
     * @param 支付类型(1-微信，2-支付宝) $type
     * @return 根据订单号以及支付类型来获取诊所支付配置信息
     */
    public static function getConfig($out_trade_no, $type) {
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['a.out_trade_no', 'b.appid', 'b.mchid', 'b.payment_key', 'b.spot_id']);
        $query->leftJoin(['b' => PaymentConfig::tableName()], '{{a}}.spot_id = {{b}}.spot_id');
        $query->where(['a.out_trade_no' => $out_trade_no, 'b.type' => $type]);
        return $query->one();
    }

}
