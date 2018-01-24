<?php

namespace app\specialModules\recharge\models;

use Yii;
use app\common\Common;
use app\specialModules\recharge\models\Wechat;
use app\modules\user\models\User;
use app\modules\spot_set\models\PaymentConfig;
use yii\log\Logger;
use Exception;

/**
 * This is the model class for table "{{%payment_log}}".
 *
 * @property integer $id
 * @property string $out_trade_no
 * @property string $buyer_id
 * @property string $buyer_logon_id
 * @property string $seller_id
 * @property string $seller_email
 * @property string $total_amount
 * @property string $receipt_amount
 * @property string $buyer_pay_amount
 * @property string $refund_fee
 * @property string $send_back_fee
 * @property string $transaction_id
 * @property integer $payment_time
 * @property integer $refund_time
 * @property integer $close_time
 * @property integer $status
 * @property integer $pay_type
 * @property integer $create_time
 * @property integer $update_time
 */
class PaymentLog extends \app\common\base\BaseActiveRecord
{

    /**
     * 扫描枪支付的时候  需要区别订单号 此处多增加一位
     */
    const scanSuffix = 'E';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%payment_log}}';
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
            [['total_amount', 'receipt_amount', 'buyer_pay_amount', 'refund_fee', 'send_back_fee'], 'number'],
            [['payment_time', 'refund_time', 'close_time', 'status', 'create_time', 'update_time', 'pay_type'], 'integer'],
            [['out_trade_no', 'buyer_id', 'seller_id', 'transaction_id'], 'string', 'max' => 64],
            [['buyer_logon_id', 'seller_email'], 'string', 'max' => 255],
            [['refund_fee', 'refund_time', 'close_time', 'send_back_fee'], 'default', 'value' => 0],
            [['out_trade_no'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '主键自动增长',
            'out_trade_no' => '订单编号',
            'buyer_id' => '买家用户号',
            'buyer_logon_id' => '买家账号',
            'seller_id' => '卖家用户号',
            'seller_email' => '卖家账号',
            'total_amount' => '订单总金额(本次交易支付的订单金额，单位为人民币（元）)',
            'receipt_amount' => '实收金额(商家在交易中实际收到的款项，单位为元)',
            'buyer_pay_amount' => '付款金额(用户在交易中支付的金额)',
            'refund_fee' => '总退款金额(退款通知中，返回总退款金额，单位为元)',
            'send_back_fee' => '实际退款金额(商户实际退款给用户的金额，单位为元)',
            'payment_time' => '交易付款时间',
            'refund_time' => '交易退款时间',
            'close_time' => '交易结束时间',
            'status' => '订单支付状态[ 1待支付 2支付成功 3支付失败 ]',
            'transaction_id' => '支付交易号',
            'pay_type' => '支付类型(1-微信，2-支付宝)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return 若支付成功，修改订单状态，同时创建支付日志
     * @param 订单详情列表 $params
     * @param 支付类型(1-微信,2-支付宝) $type
     * @param 支付宝支付配置 $list
     */
    public static function addOrderLog($params, $type, $list) {
        $dbOutTradeNo = substr($params['out_trade_no'], 0, 15);
        $orderModel = Order::findOne(['out_trade_no' => $dbOutTradeNo, 'total_amount' => $params['total_amount']]);
        $notifyUrl = Yii::getAlias('@apiPayRechargeIndex');
        $db = $orderModel->getDb();
        $dbTrans = $db->beginTransaction();
        try {
            $lockSql = " SELECT status,type FROM {{%order}} WHERE out_trade_no='{$dbOutTradeNo}' AND total_amount=" . $params['total_amount'] . " FOR UPDATE ";
            $lockData = $db->createCommand($lockSql)->queryOne();
            Yii::info('alipay lockData:[' . $lockData['status'] . ']');
            Yii::info('alipay orderModel:[' . $orderModel->status . ']');
            $result['errorCode'] = 0;
            if (!$lockData) {//若订单不存在。则自动退款
                \app\modules\charge\models\Order::refundOrder($params, $list, $notifyUrl);
                $result['errorCode'] = 1001; //订单不存在
            } else if (in_array($lockData['status'], [1, 3])) {//若还没支付/支付失败。则更改订单状态
                $orderModel->status = 2; //支付成功
                $orderModel->type = $type;
                $orderModel->save();
                $paymentLogModel = self::findOne(['out_trade_no' => $dbOutTradeNo]);
                if ($paymentLogModel) {
                    $paymentLogModel->status = 2;
                    $paymentLogModel->save();
                } else {
                    $paymentLogModel = new static();
                    $paymentLogModel->out_trade_no = $dbOutTradeNo;
                    $paymentLogModel->buyer_id = $params['buyer_id'];
                    $paymentLogModel->buyer_logon_id = $params['buyer_logon_id'];
                    $paymentLogModel->seller_id = $params['seller_id'];
                    $paymentLogModel->seller_email = $params['seller_email'];
                    $paymentLogModel->status = 2;
                    $paymentLogModel->total_amount = $params['total_amount'];
                    $paymentLogModel->receipt_amount = $params['receipt_amount'];
                    $paymentLogModel->buyer_pay_amount = $params['buyer_pay_amount'];
                    $paymentLogModel->create_time = time();
                    $paymentLogModel->pay_type = $type;
                    $paymentLogModel->transaction_id = $params['trade_no'];
                    $paymentLogModel->payment_time = strtotime($params['gmt_payment']); //交易付款时间
                    $paymentLogModel->update_time = time();
                    $paymentLogModel->save();
                }
                $res = self::addCardFlow($orderModel);
                if (!$res) {
                    $dbTrans->rollBack();
                    $result['errorCode'] = 1002; //验证不通过
                }
            } else if ($lockData['status'] == 5) {
                //若已过期。则自动退款
                \app\modules\charge\models\Order::refundOrder($params, $list, $notifyUrl);
//             Order::deleteAll(['record_id' => $orderModel->record_id, 'out_trade_no' => $params['out_trade_no'], 'status' => 5]);
                $result['errorCode'] = 1004;
            } else if ($lockData['status'] == 2) {
                if (in_array($lockData['type'], [1, 2])) {
                    \app\modules\charge\models\Order::refundOrder($params, $list, $notifyUrl);
                    $result['errorCode'] = 1004; //订单已经被现金／刷卡支付
                }
//                $hasRecord = PaymentLog::find()->select(['id', 'pay_type', 'transaction_id'])->where(['out_trade_no' => $dbOutTradeNo, 'status' => 2])->asArray()->one();
                $hasRecordSql = " SELECT id,pay_type,transaction_id FROM {{%payment_log}} WHERE out_trade_no='{$dbOutTradeNo}' AND status=2 FOR UPDATE";
                $hasRecord = $db->createCommand($hasRecordSql)->queryOne();
                if ($hasRecord && $lockData['type'] == 3 && $hasRecord['pay_type'] == 3) {//若有支付成功记录，同时支付方式为微信支付。则判断为重复支付，直接退款
                    \app\modules\charge\models\Order::refundOrder($params, $list, $notifyUrl);
                    $result['errorCode'] = 1004; //订单已经微信支付
                }
                if ($lockData['type'] == 4 && $hasRecord['pay_type'] == 4 && $hasRecord['transaction_id'] != $params['trade_no']) {//若同为支付宝支付。则判断交易号是否一致，若不同，则为重复支付
                    \app\modules\charge\models\Order::refundOrder($params, $list, $notifyUrl);
                    $result['errorCode'] = 1004;
                }
            }
            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
            $result['errorCode'] = 1003;
            Yii::info('recharge addOrderLog by Alipay failed outTradeNo .[' . $params['out_trade_no'] . '] ' . $e->getMessage());
        }
        return $result;
    }

    public static function addOrderLogByWechat($params, $type) {
//        $params['out_trade_no'] = substr($params['out_trade_no'], 0, 15);
        $dbOutTradeNo = substr($params['out_trade_no'], 0, 15);
        Yii::info('addOrderLogByWechat:[' . json_encode($params) . ']');
        $orderModel = Order::findOne(['out_trade_no' => $dbOutTradeNo, 'total_amount' => $params['total_fee'] / 100]);
        Yii::info('orderModel:[' . $orderModel->status . ']');
        $db = $orderModel->getDb();
        $dbTrans = $db->beginTransaction();
        try {
            $lockSql = " SELECT status,type FROM {{%order}} WHERE out_trade_no='{$dbOutTradeNo}' AND total_amount=" . ($params['total_fee'] / 100) . " FOR UPDATE ";
            $lockData = $db->createCommand($lockSql)->queryOne();
            if (!$lockData) {
                $result['errorCode'] = 1001; //订单不存在
                return $result;
            } else if ($lockData && in_array($lockData['status'], [1, 3])) {
                $orderModel->status = 2; //支付成功
                $orderModel->type = $type;
                $orderModel->save();
                $paymentLogModel = self::findOne(['out_trade_no' => $dbOutTradeNo]);
                if ($paymentLogModel) {
                    $paymentLogModel->status = 2;
                    $paymentLogModel->save();
                } else {
                    $paymentLogModel = new static();
                    $paymentLogModel->out_trade_no = $dbOutTradeNo;
                    $paymentLogModel->buyer_id = $params['openid'];
                    $paymentLogModel->seller_id = $params['mch_id'];
                    $paymentLogModel->status = 2;
                    $paymentLogModel->transaction_id = $params['transaction_id'];
                    $paymentLogModel->pay_type = 3;
                    $paymentLogModel->total_amount = $params['total_fee'] / 100;
                    $paymentLogModel->create_time = time();
                    $paymentLogModel->payment_time = strtotime($params['time_end']); //交易付款时间
                    $paymentLogModel->update_time = time();
                    $paymentLogModel->save();
                }
                $params['body'] = $params['attach'];
                $res = self::addCardFlow($orderModel);
                if (!$res) {
                    $dbTrans->rollBack();
                    $result['errorCode'] = 1002; //验证不通过
                    return $result;
                    //调用微信退款接口TODO
//                     $refundRes = Wechat::refund($orderModel->out_trade_no, $orderModel->out_trade_no, $orderModel->total_amount * 100, $orderModel->total_amount * 100);
//                     if ($refundRes) {
//                         Yii::info('wechat_refund_' . $_COOKIE['wechatSpotId'] . ':退款成功');
//                     } else {
//                         Yii::info('wechat_refund_' . $_COOKIE['wechatSpotId'] . ':退款失败');
//                     }
                }
            } else if ($lockData['status'] == 5) {//支付了过时的订单  直接退款
                //调用微信退款接口TODO
                $refundRes = Wechat::refund($params['out_trade_no'], $params['out_trade_no'], $orderModel->total_amount * 100, $orderModel->total_amount * 100);
                if ($refundRes) {
                    Yii::info('wechat_refund_' . $orderModel->status . '--' . $_COOKIE['wechatSpotId'] . ':退款成功');
                } else {
                    Yii::info('wechat_refund_' . $orderModel->status . '--' . $_COOKIE['wechatSpotId'] . ':退款失败');
                }
            } else if ($lockData['status'] == 2) {
                if (in_array($lockData['type'], [1, 2])) {
                    $refundRes = Wechat::refund($params['out_trade_no'], $params['out_trade_no'], $orderModel->total_amount * 100, $orderModel->total_amount * 100);
                    if ($refundRes) {
                        Yii::info('wechat_refund_' . $orderModel->type . $_COOKIE['wechatSpotId'] . ' outTradeNo: ' . $params['out_trade_no'] . ':退款成功');
                    } else {
                        Yii::info('wechat_refund_' . $orderModel->type . $_COOKIE['wechatSpotId'] . ' outTradeNo: ' . $params['out_trade_no'] . ':退款失败');
                    }
                    $result['errorCode'] = 1004; //订单已经被现金／刷卡支付
                    return $result;
                }

//                $hasRecord = PaymentLog::find()->select(['id', 'pay_type', 'transaction_id'])->where(['out_trade_no' => $dbOutTradeNo, 'status' => 2])->asArray()->one();
                $hasRecordSql = " SELECT id,pay_type,transaction_id FROM {{%payment_log}} WHERE out_trade_no='{$dbOutTradeNo}' AND status=2 FOR UPDATE";
                $hasRecord = $db->createCommand($hasRecordSql)->queryOne();
                Yii::info('订单状态:' . $orderModel->type, 'order');
                Yii::info('支付类型:' . $hasRecord['pay_type'], 'order');
                if ($hasRecord && $lockData['type'] == 4 && $hasRecord['pay_type'] == 4) {//若有支付成功记录，同时支付方式为支付宝支付。则判断为重复支付，直接退款
                    //调用微信退款接口TODO
                    $refundRes = Wechat::refund($params['out_trade_no'], $params['out_trade_no'], $orderModel->total_amount * 100, $orderModel->total_amount * 100);
                    if ($refundRes) {
                        Yii::info('wechat_refund_' . $orderModel->status . '--' . $_COOKIE['wechatSpotId'] . ':退款成功');
                    } else {
                        Yii::info('wechat_refund_' . $orderModel->status . '--' . $_COOKIE['wechatSpotId'] . ':退款失败');
                    }
                }
                if ($lockData['type'] == 3 && $hasRecord['pay_type'] == 3 && $hasRecord['transaction_id'] != $params['transaction_id']) {//若同微信支付。则判断交易号是否一致，若不同，则为重复支付
                    //调用微信退款接口TODO
                    $refundRes = Wechat::refund($params['out_trade_no'], $params['out_trade_no'], $orderModel->total_amount * 100, $orderModel->total_amount * 100);
                    if ($refundRes) {
                        Yii::info('wechat_refund_' . $_COOKIE['wechatSpotId'] . ' outTradeNo: ' . $params['out_trade_no'] . ':退款成功');
                    } else {
                        Yii::info('wechat_refund_' . $_COOKIE['wechatSpotId'] . ' outTradeNo: ' . $params['out_trade_no'] . ':退款失败');
                    }
                }
            }
            $dbTrans->commit();
        } catch (\Exception $e) {
            $dbTrans->rollBack();
            Yii::info('recharge addOrderLogByWechat failed out_trade_no :[' . $params['out_trade_no'] . '] ' . $e->getMessage());
        }
    }

    /**
     * 
     * @param object $orderModel 订单表信息
     */
    public static function addCardFlow($orderModel) {
        $model = new CardFlow();
        $model->f_record_id = $orderModel->record_id;
        $model->f_user_id = $orderModel->user_id;
        $model->f_record_type = 1;
        $model->f_record_fee = $orderModel->total_amount;
        $model->f_user_name = User::getUserInfo($orderModel->user_id, ['username'])['username'];
        $model->f_pay_type = $orderModel->type;
        $model->f_spot_id = $orderModel->spot_id;
        $model->isDonation = $orderModel->donation_fee > 0 ? 1 : 0;
        $model->donationFee = $orderModel->donation_fee;
        $model->isUpgrade = $orderModel->is_upgrade;
        $result = CardFlow::addFlow($model, $orderModel->record_id);
        return $result;
    }

    /**
     * @param unknown $orderModel
     * @param 回调参数 $params
     * @param 支付类型(1-现金,2-刷卡,3-微信支付,4-支付宝支付) $type
     * @param $flowModel flowModel 对象
     * @param $subject 
     * @return 扫描枪支付
     */
    public static function scannerPayment($orderModel, $params, $type, $flowModel = null, $subject = '') {
        try {
            if ($type == 3) {
                self::scannerPaymentByWechat($orderModel, $params, $type, $flowModel, $subject);
            } elseif ($type == 4) {
                self::scannerPaymentByAlipay($orderModel, $params, $type, $flowModel, $subject);
            } else {
                throw new Exception('wrong payment type');
            }
            return true;
        } catch (Exception $e) {
            Yii::info('scannerPayment failed ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 微信扫描枪支付
     */
    protected static function scannerPaymentByWechat($orderModel, $params, $type, $flowModel = null, $subject = '') {
        try {
            if (!$flowModel->wechatAuthCode) {
                throw new Exception('wechatAuthCode Wrong');
            }
            include_once(Yii::getAlias('@ServicePath') . "/wechat/WxPay.MicroPay.php");
            $input = new \WxPayMicroPay();
            $outTradeNo = $orderModel->out_trade_no . self::scanSuffix;
            $input->SetAuth_code($flowModel->wechatAuthCode);
            $input->SetBody($subject);
            $input->SetTotal_fee($orderModel->total_amount * 100); //微信支付中 都是以分为单位
            $input->SetOut_trade_no($outTradeNo);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600)); //订单过期时间 10分钟
            $microPay = new \MicroPay();
            $payResult = $microPay->pay($input);
            if (array_key_exists("return_code", $payResult) && array_key_exists("result_code", $payResult) && $payResult["return_code"] == "SUCCESS" && $payResult["result_code"] == "SUCCESS") {
                //支付成功  调用支付的回调（本不需要回调此处只是为了修改订单状态和记录流水）
                $data = $payResult;
                self::addOrderLogByWechat($data, 3);
            } else {
                throw new Exception('payment by wechat failed orderNo: ' . $orderModel->out_trade_no);
            }
        } catch (Exception $e) {
            Yii::info('scannerPaymentByWechat failed; ' . $e->getMessage());
            throw new Exception('scannerPaymentByWechat failed; ' . $e->getMessage());
        }
    }

    /**
     * 支付宝扫描枪支付
     */
    protected static function scannerPaymentByAlipay($orderModel, $params, $type, $flowModel, $subject) {
        try {
            if (!$flowModel->alipayAuthCode) {
                throw new Exception('alipayAuthCode Wrong');
            }
            include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/model/builder/ExtendParams.php");
            include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/model/builder/GoodsDetail.php");
            include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/model/builder/AlipayTradePayContentBuilder.php");
            include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/service/AlipayTradeService.php");
            $outTradeNo = $orderModel->out_trade_no . self::scanSuffix; //订单号 不能重复 扫描枪的时候  多增加一个后缀
            $undiscountableAmount = "";
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
            // 支付超时，10分钟
            $timeExpress = "10m";
            // 创建请求builder，设置请求参数
            $barPayRequestBuilder = new \AlipayTradePayContentBuilder();
            $barPayRequestBuilder->setOutTradeNo($outTradeNo);
            $barPayRequestBuilder->setTotalAmount($orderModel->total_amount); //支付宝  以元为单位
            $barPayRequestBuilder->setAuthCode($flowModel->alipayAuthCode);
            $barPayRequestBuilder->setTimeExpress($timeExpress);
            $barPayRequestBuilder->setSubject($subject);
            $barPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);
            $barPayRequestBuilder->setExtendParams($extendParamsArr);
            $barPayRequestBuilder->setStoreId($storeId);
            $barPayRequestBuilder->setOperatorId($operatorId);
            $barPayRequestBuilder->setAlipayStoreId($alipayStoreId);
            $paymentConfig = PaymentConfig::getPaymentConfigList();
            $pConfig = $paymentConfig[2];
            $config = include(Yii::getAlias('@ServicePath') . "/pay/f2fpay/config/config.php");
            $config['app_id'] = $pConfig['appid'];
            $config['merchant_private_key'] = $pConfig['mchid']; //商户私钥
            $config['alipay_public_key'] = $pConfig['payment_key']; //支付宝密钥
            $config['MaxQueryRetry'] = 10; //最大轮询次数
            $config['QueryDuration'] = 3;
            
            Yii::$app->log->logger->log($config, Logger::LEVEL_INFO);

            //调barPay方法获取条形码支付应答
            $barPay = new \AlipayTradeService($config);
            $qrPayResult = $barPay->barPay($barPayRequestBuilder);

            //根据状态值进行业务处理
            $barRes = $qrPayResult->getTradeStatus();
            if ($barRes == 'SUCCESS') {
                //支付成功  调用支付的回调（本不需要回调此处只是为了修改订单状态和记录流水）
                $data = (array) $qrPayResult->getResponse();
                $data['buyer_id'] = isset($data['buyer_id']) ? $data['buyer_id'] : '';
                $data['seller_id'] = isset($data['seller_id']) ? $data['seller_id'] : '';
                $data['seller_email'] = isset($data['seller_email']) ? $data['seller_email'] : '';
                self::addOrderLog($data, 4, $pConfig);
            } else {
                $data = (array) $qrPayResult->getResponse();
                $result = \app\modules\charge\models\PaymentLog::checkQueryOrder($data, $config);//轮询查询订单状态
                if($result['code'] == '10000' || $result['trade_status'] == 'TRADE_SUCCESS' || $result['trade_status'] == 'TRADE_FINISHED'){//支付成功
                    $result['buyer_id'] = isset($result['buyer_user_id']) ? $result['buyer_user_id'] : '';
                    $result['seller_id'] = isset($result['seller_id']) ? $result['seller_id'] : '';
                    $result['seller_email'] = isset($result['seller_email']) ? $result['seller_email'] : '';
                    $result['gmt_payment'] = isset($result['send_pay_date'])?$result['send_pay_date']:date('Y-m-d H:i',time());
                    Yii::info($result,'recharge-checkQueryOrder-result');
                    return self::addOrderLog($result, 4, $pConfig);
                }else{
                    Yii::error($data,'recharge-payment by alipay failed-error-data');
                    Yii::error($result,'recharge-payment by alipay failed-error-result');
                    throw new Exception('recharge-payment by alipay failed orderNo: ' . $orderModel->out_trade_no);
                }
            }
        } catch (Exception $e) {
            Yii::info('scannerPaymentByAlipay failed; ' . $e->getMessage());
            throw new Exception('scannerPaymentByAlipay failed; ' . $e->getMessage());
        }
    }

}