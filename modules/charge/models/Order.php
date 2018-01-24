<?php

namespace app\modules\charge\models;

use Yii;
use yii\log\Logger;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $id
 * @property integer $record_id
 * @property integer $spot_id
 * @property integer $user_id
 * @property string $out_trade_no
 * @property string $subject
 * @property string $total_amount
 * @property integer $status
 * @property integer $type
 * @property integer $create_time
 * @property integer $update_time
 * @property string $discount_reason 优惠原因
 * @property integer $discount_type 优惠类型(1-无，2-金额扣减，3-折扣)
 * @property number $discount_price 优惠金额
 */
class Order extends \app\common\base\BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['record_id', 'spot_id', 'user_id', 'status', 'total_amount', 'out_trade_no', 'type'], 'required'],
            [['record_id', 'spot_id', 'user_id', 'status', 'create_time', 'update_time', 'type', 'discount_type'], 'integer'],
            [['total_amount'], 'number'],
            [['status', 'discount_type'], 'default', 'value' => 1],
            [['out_trade_no'], 'string', 'max' => 64],
            [['subject', 'discount_reason'], 'string', 'max' => 255],
            [['discount_price'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
            [['discount_reason', 'discount_price'], 'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'record_id' => '流水ID',
            'spot_id' => '诊所ID',
            'user_id' => '操作人ID',
            'out_trade_no' => '订单号',
            'subject' => '订单名称',
            'type' => '订单类型(1-现金,2-刷卡,3-微信支付,4-支付宝支付)',
            'total_amount' => '支付总金额(本次交易支付的订单金额，单位为人民币（元）)',
            'status' => '订单支付状态[ 1-待支付 2-支付成功 3-支付失败 4-已退款 5-已过期 ]',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'discount_type' => '优惠方式',
            'discount_price' => '优惠金额',
            'discount_reason' => '优惠原因'
        ];
    }

    /**
     * 
     * @param 就诊流水id $record_id
     * @param 用户id $user_id
     * @param 订单号 $out_trade_no
     * @param 订单名称 $subject
     * @param 订单总金额 $total_amount
     * @param 订单类型(0-不祥 1-微信,2-支付宝) $type
     * @param 优惠方式 $discount_type
     * @param 优惠金额 $discount_price
     * @param 优惠原因 $discount_reason 
     * @return 添加一个支付订单
     */
    public static function addOrder($record_id, $user_id, $out_trade_no, $subject, $total_amount, $type = 0, $discount_type = 1, $discount_price = 0, $discount_reason = '') {
        $model = new static();
        $model->out_trade_no = $out_trade_no;
        $model->record_id = $record_id;
        $model->spot_id = self::$staticSpotId;
        $model->user_id = $user_id;
        $model->subject = $subject;
        $model->total_amount = $total_amount;
        $model->type = $type;
        $model->status = 1;
        $model->discount_type = $discount_type;
        $model->discount_price = $discount_price;
        $model->discount_reason = $discount_reason;
        $model->create_time = time();
        $model->update_time = time();
        return $model->save();
    }

    /**
     * 
     * @return 生成订单号
     */
    public static function generateOutTradeNo() {
//    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
//    $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        $orderSn = strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        !empty($str) && $orderSn = $str . $orderSn;
        return $orderSn;
//    return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
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
    public static function generateQrCode($outTradeNo, $subject, $totalAmount, $paymentConfig, $rows, $info) {
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
        $pks = [];
        foreach ($rows as $k => $v) {
            $pks[] = $v['id'];
            $detail[] = $v['name'];
        }
        $list['detail'] = $detail;
        $list['pks'] = $pks;
        $list['username'] = '患者姓名:' . $info['username'];
        $list['patient_id'] = $info['patient_id'];
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
        $goods1->setGoodsId($patient_id);
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
        $config['notify_url'] = Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@apiPayIndex')]);
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
     * @param array $params 支付宝异步通知参数
     * @param array $list 支付宝支付配置信息
     * @param string $notifyUrl 支付宝回调url
     * @desc 支付宝退费共用方法
     */
    public static function refundOrder($params, $list, $notifyUrl) {
        include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/model/builder/AlipayTradeRefundContentBuilder.php");
        include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/service/AlipayTradeService.php");
        if (!empty($params['out_trade_no']) && trim($params['out_trade_no']) != "") {

            $out_trade_no = trim($params['out_trade_no']);
            $refund_amount = trim($params['total_amount']);
            $config = include(Yii::getAlias('@ServicePath') . "/pay/f2fpay/config/config.php");
            $config['app_id'] = $list['appid'];
            $config['merchant_private_key'] = $list['mchid']; //商户私钥
            $config['alipay_public_key'] = $list['payment_key']; //支付宝密钥
            $config['notify_url'] = Yii::$app->urlManager->createAbsoluteUrl([$notifyUrl]);
            Yii::$app->log->logger->log($config, Logger::LEVEL_INFO);
            $out_request_no = '';
            //第三方应用授权令牌,商户授权系统商开发模式下使用
            $appAuthToken = ""; //根据真实值填写
            //创建退款请求builder,设置参数
            $refundRequestBuilder = new \AlipayTradeRefundContentBuilder();
            $refundRequestBuilder->setOutTradeNo($out_trade_no);
            $refundRequestBuilder->setRefundAmount($refund_amount);
            $refundRequestBuilder->setOutRequestNo($out_request_no);
            $refundRequestBuilder->setAppAuthToken($appAuthToken);
            //初始化类对象,调用refund获取退款应答
            $refundResponse = new \AlipayTradeService($config);
            $refundResult = $refundResponse->refund($refundRequestBuilder);
            //根据交易状态进行处理
            switch ($refundResult->getTradeStatus()) {
                case "SUCCESS":
                    Yii::info($refundResult->getResponse(), 'alipay');
                    break;
                case "FAILED":
//                    echo "支付宝退款失败!!!"."<br>--------------------------<br>";
                    if (!empty($refundResult->getResponse())) {
                        Yii::error($refundResult->getResponse(), 'alipay');
                    }
                    break;
                case "UNKNOWN":
//                    echo "系统异常，订单状态未知!!!"."<br>--------------------------<br>";
                    if (!empty($refundResult->getResponse())) {
                        Yii::error($refundResult->getResponse(), 'alipay');
                    }
                    break;
                default:
                    echo "不支持的交易状态，交易返回异常!!!";
                    Yii::error('不支持的交易状态，交易返回异常!!!', 'alipay');
                    break;
            }
            return;
        }
    }

    public static function generateWechatQrCode($outTradeNo, $subject, $totalAmount, $recordId, $pks, $patient_id) {
        include_once(Yii::getAlias('@ServicePath') . "/wechat/WxPay.Api.php");
        include_once(Yii::getAlias('@ServicePath') . "/wechat/WxPay.NativePay.php");
        $pksCacheKey = Yii::getAlias('@wxPayChargeItem') . $outTradeNo;
        Yii::$app->cache->set($pksCacheKey, $pks);
        $rows['pks'] = $pksCacheKey;
        $rows['patient_id'] = $patient_id;
        // 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
        $body = json_encode($rows);
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($subject);
        $input->SetAttach($body);
        $input->SetOut_trade_no($outTradeNo); //订单ID
        $input->SetTotal_fee($totalAmount * 100);
//        $input->SetTotal_fee('1');//测试状态设置为1分
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600)); //订单过期时间10分钟
        $input->SetGoods_tag($subject);
        $notify_url = Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@apiWechatCallbackNotify')]);
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
     * @param integer $recordId 就诊流水id
     * @desc 将所有待收费订单制成过期
     */
    public static function updateOrderStatus($recordId){
        self::updateAll(['status' => 5],['record_id' => $recordId,'status' => 1,'spot_id' => self::$staticSpotId]);
    }

}
