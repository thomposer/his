<?php

namespace app\modules\charge\models;

use Yii;
use yii\base\Object;
use app\modules\patient\models\PatientRecord;
use yii\helpers\Json;
use app\common\Common;
use app\modules\spot_set\models\PaymentConfig;
use yii\log\Logger;
use Exception;
use app\specialModules\recharge\models\CardFlow;
use app\modules\stock\models\MaterialStockInfo;
use app\modules\stock\models\ConsumablesStockInfo;

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
     * @return 若支付成功，修改订单状态，同时创建支付日志  支付报支付
     * @param 订单详情列表 $params
     * @param 支付类型(3-微信,4-支付宝) $type
     * @param 支付宝支付配置 $list
     */
    public static function addOrderLog($params, $type = 4, $list) {
        $dbOutTradeNo = substr($params['out_trade_no'], 0, 15);
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $lockSql = " SELECT status,type FROM {{%order}} WHERE out_trade_no='{$dbOutTradeNo}' AND total_amount=" . $params['total_amount'] . " FOR UPDATE ";
            $lockData = Yii::$app->db->createCommand($lockSql)->queryOne();
            $orderModel = Order::findOne(['out_trade_no' => $dbOutTradeNo, 'total_amount' => $params['total_amount']]);
            Yii::info('alipay lockData:[' . $lockData['status'] . ']');
            Yii::info('alipay orderModel:[' . $orderModel->status . ']');
            $notifyUrl = Yii::getAlias('@apiPayIndex');
            $result['errorCode'] = 0;
            if (!$lockData || !$orderModel) {//若订单不存在。则自动退款
                Order::refundOrder($params, $list, $notifyUrl);
                $result['errorCode'] = 1001; //订单不存在
            } else if ($lockData && in_array($lockData['status'], [1, 3])) {//若还没支付/支付失败。则更改订单状态
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
                $params['out_trade_no'] = $dbOutTradeNo;
                self::editChargeInfo($orderModel, $params, $type); //修改收费记录详情
                $patientInfo = json_decode($params['body'], true);
                $result['chargeRecordLogId'] = ChargeRecordLog::saveChargeRecordLog($orderModel->spot_id, $patientInfo['patient_id'], $orderModel->record_id, $params, 1, $type, $paymentLogModel->total_amount);
            } else if ($lockData['status'] == 5) {
                //若订单已过期。则自动退款
                Order::refundOrder($params, $list, $notifyUrl);
//             Order::deleteAll(['record_id' => $orderModel->record_id, 'spot_id' => $orderModel->spot_id, 'out_trade_no' => $dbOutTradeNo, 'status' => 5]);
                $result['errorCode'] = 1003; //订单已经过期
            } else if ($lockData['status'] == 2) {//若订单已支付，则判断支付类型以及
                if (in_array($lockData['type'], [1, 2, 5])) {
                    Order::refundOrder($params, $list, $notifyUrl);
                    $result['errorCode'] = 1004; //订单已经被现金／刷卡支付
                }
//                $hasRecord = PaymentLog::find()->select(['id', 'pay_type', 'transaction_id'])->where(['out_trade_no' => $dbOutTradeNo, 'status' => 2])->asArray()->one();
                $hasRecordSql = " SELECT id,pay_type,transaction_id FROM {{%payment_log}} WHERE out_trade_no='{$dbOutTradeNo}' AND status=2 FOR UPDATE";
                $hasRecord = Yii::$app->db->createCommand($hasRecordSql)->queryOne();
                Yii::info('alipay hasRecord data :【' . json_encode($hasRecord) . '】');
                if ($hasRecord && $lockData['type'] == 3 && $hasRecord['pay_type'] == 3) {//若有支付成功记录，同时支付方式为微信支付。并且交易号不一置。则判断为重复支付，直接退款
                    Order::refundOrder($params, $list, $notifyUrl);
                    $result['errorCode'] = 1004; //订单已经微信支付
                }
                Yii::info('alipay return params data :【' . json_encode($params) . '】');
                Yii::info('alipay return orderModel type :【' . $orderModel->type . '】');
                Yii::info('alipay return type type :【' . $type . '】');
                if ($lockData['type'] == 4 && $hasRecord['pay_type'] == 4 && $hasRecord['transaction_id'] != $params['trade_no']) {//若同为支付宝支付。则判断交易号是否一致，若不同，则为重复支付
                    Yii::info('alipay need refund 1111');
                    Order::refundOrder($params, $list, $notifyUrl);
                    $result['errorCode'] = 1004; //订单已经微信支付
                }
            }
            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
            Yii::info('addOrderLog By Alipay failed outTradeNo [' . $params['out_trade_no'] . '] ' . $e->getMessage());
            $result['errorCode'] = 1002; //数据库保存事务失败
        }
        return $result;
    }

    /**
     * @return 若支付成功，修改订单状态，同时创建支付日志 微信支付
     * @param 订单详情列表 $params
     * @param 支付类型(3-微信,4-支付宝) $type
     */
    public static function addOrderLogByWechat($params, $type = 3) {
        Yii::info('addOrderLogByWechat:[' . json_encode($params) . ']');
        $dbOutTradeNo = substr($params['out_trade_no'], 0, 15);
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $lockSql = " SELECT status,type FROM {{%order}} WHERE out_trade_no='{$dbOutTradeNo}' AND total_amount=" . ($params['total_fee'] / 100) . " FOR UPDATE ";
            $lockData = Yii::$app->db->createCommand($lockSql)->queryOne();
            $orderModel = Order::findOne(['out_trade_no' => $dbOutTradeNo, 'total_amount' => $params['total_fee'] / 100]);
            Yii::info('lockData:[' . $lockData['status'] . ']');
            Yii::info('orderModel:[' . $orderModel->status . ']');
            $result['errorCode'] = 0;
            if (!$lockData || !$orderModel) {
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
                $params['out_trade_no'] = $dbOutTradeNo;
                self::editChargeInfo($orderModel, $params, $type); //修改收费记录详情
                $patientInfo = json_decode($params['body'], true);
                Yii::info('增加收费流水 saveChargeRecordLog');
                $result['chargeRecordLogId'] = ChargeRecordLog::saveChargeRecordLog($orderModel->spot_id, $patientInfo['patient_id'], $orderModel->record_id, $params, 1, $type, $paymentLogModel->total_amount);
            } else if ($lockData['status'] == 5) {//支付了过时的订单  直接退款
                //调用微信退款接口TODO
                $refundRes = Wechat::refund($params['out_trade_no'], $params['out_trade_no'], $orderModel->total_amount * 100, $orderModel->total_amount * 100);
                if ($refundRes) {
                    Yii::info('wechat_refund_' . $_COOKIE['wechatSpotId'] . ' outTradeNo: ' . $params['out_trade_no'] . ':退款成功');
                } else {
                    Yii::info('wechat_refund_' . $_COOKIE['wechatSpotId'] . ' outTradeNo: ' . $params['out_trade_no'] . ':退款失败');
                }
            } else if ($lockData['status'] == 2) {//若支付成功
                if (in_array($lockData['type'], [1, 2, 5])) {
                    $refundRes = Wechat::refund($params['out_trade_no'], $params['out_trade_no'], $orderModel->total_amount * 100, $orderModel->total_amount * 100);
                    if ($refundRes) {
                        Yii::info('wechat_refund_' . $orderModel->type . $_COOKIE['wechatSpotId'] . ' outTradeNo: ' . $params['out_trade_no'] . ':退款成功');
                    } else {
                        Yii::info('wechat_refund_' . $orderModel->type . $_COOKIE['wechatSpotId'] . ' outTradeNo: ' . $params['out_trade_no'] . ':退款失败');
                    }
                    $result['errorCode'] = 1004; //订单已经被现金／刷卡支付
                    return $result;
                }
                $hasRecordSql = " SELECT id,pay_type,transaction_id FROM {{%payment_log}} WHERE out_trade_no='{$dbOutTradeNo}' AND status=2 FOR UPDATE";
                $hasRecord = Yii::$app->db->createCommand($hasRecordSql)->queryOne();
//                $hasRecord = PaymentLog::find()->select(['id', 'pay_type', 'transaction_id'])->where(['out_trade_no' => $dbOutTradeNo, 'status' => 2])->asArray()->one();
                Yii::info('wechatPay hasRecord data :【' . json_encode($hasRecord) . '】');
                if ($hasRecord && $lockData['type'] == 4 && $hasRecord['pay_type'] == 4) {//若有支付成功记录，同时支付方式为支付宝。则判断为重复支付，直接退款
                    //调用微信退款接口TODO
                    $refundRes = Wechat::refund($params['out_trade_no'], $params['out_trade_no'], $orderModel->total_amount * 100, $orderModel->total_amount * 100);
                    if ($refundRes) {
                        Yii::info('wechat_refund_' . $_COOKIE['wechatSpotId'] . ' outTradeNo: ' . $params['out_trade_no'] . ':退款成功');
                    } else {
                        Yii::info('wechat_refund_' . $_COOKIE['wechatSpotId'] . ' outTradeNo: ' . $params['out_trade_no'] . ':退款失败');
                    }
                }
                Yii::info('wechatPay return params data :【' . json_encode($params) . '】');
                if ($lockData['type'] == 3 && $hasRecord['pay_type'] == 3 && $hasRecord['transaction_id'] != $params['transaction_id']) {//若同微信支付。则判断交易号是否一致，若不同，则为重复支付
                    Yii::info('wechatPay need refund 1111');
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
        } catch (Exception $e) {
            $dbTrans->rollBack();
            Yii::info('addOrderLogByWechat failed outTradeNo [' . $params['out_trade_no'] . '] ' . $e->getMessage());
        }
        return $result;
    }

    /**
     * 
     * @param unknown $orderModel
     * @param 回调参数 $params
     * @param 支付类型(1-现金,2-刷卡,3-微信支付,4-支付宝支付) $type
     * @param $allPrice 总支付金额原价
     * @param $income 实收费用(只针对现金支付)
     * @param $change 找零(只针对现金支付)
     * @param $cardFlowId 充值卡交易流水id
     * @return 异步回调添加收费记录以及修改收费清单
     */
    public static function editChargeInfo($orderModel, $params, $type, $allPrice = null, $income = null, $change = null,$cardFlowId = 0) {
        $body = Json::decode($params['body'], true);
        if ($type == 3) {//如果为微信支付  则PKS需要从缓存中取
            $pksCacheKey = $body['pks'];
            $pks = Yii::$app->cache->get($pksCacheKey);
        } else {
            $pks = $body['pks'];
        }
        $chargeType = PatientRecord::find()->select(['charge_type'])->where(['id' => $orderModel->record_id,'spot_id' => $orderModel->spot_id])->asArray()->one();
        $check = ChargeInfo::find()->select(['status'])->where(['id' => $pks[0], 'record_id' => $orderModel->record_id, 'spot_id' => $orderModel->spot_id])->asArray()->one();
        if ($check['status'] == 0) {//若订单详情为未支付。则更改状态，创建收费记录
            $chargeRecordModel = new ChargeRecord();
            $chargeRecordModel->spot_id = $orderModel->spot_id;
            $chargeRecordModel->record_id = $orderModel->record_id;
            $chargeRecordModel->patient_id = $body['patient_id'];
            $chargeRecordModel->out_trade_no = $params['out_trade_no'];
            $chargeRecordModel->price = $orderModel->total_amount;
            $chargeRecordModel->status = 1;
            $chargeRecordModel->charge_type = $chargeType['charge_type'];
            $chargeRecordModel->type = $type;
            if ($type == 3 || $type == 4) {//支付类型
                $chargeRecordModel->income = $orderModel->total_amount;
            } else {
                $chargeRecordModel->income = $income;
            }
            $chargeRecordModel->change = $change ? $change : '0.00';
            $chargeRecordModel->discount_type = $orderModel->discount_type;
            $chargeRecordModel->discount_price = $orderModel->discount_price;
            $chargeRecordModel->discount_reason = $orderModel->discount_reason;
            $chargeRecordModel->save();
            
//            扣减库存逻辑在医生门诊做
//            //判断是否有其他，若是有其他收费，则验证其数量不能大于总库存量
//            $recordChargeType = PatientRecord::find()->select(['charge_type'])->where(['id' => $orderModel->record_id, 'spot_id' => $orderModel->spot_id])->asArray()->one();
//            if ($recordChargeType['charge_type'] == 1) {
//                $resultInfo = ChargeInfo::checkMaterialINum($pks,$orderModel->spot_id);
//                $consumablesInfo = ChargeInfo::checkConsumableslINum($pks,$orderModel->spot_id);
//            } else {
//                $resultInfo = ChargeInfo::checkMaterialINumSecond($pks,$orderModel->spot_id);
//            }
//            //判断是否有其他，若有，直接减库存
//            if(isset($resultInfo['num']) && !empty($resultInfo['num'])){
//                MaterialStockInfo::removeTotal($resultInfo['num'],$orderModel->spot_id);
//            }
//
//            //判断是否有医疗耗材，若有，直接减库存
//            if(isset($consumablesInfo['num']) && !empty($consumablesInfo['num'])){
//                ConsumablesStockInfo::removeTotal($consumablesInfo['num'],$orderModel->spot_id);
//            }
            
            
            Yii::info('验证:' . $chargeRecordModel->errors);
            if (!empty($params['chargeInfoArray'])) {//若有充值卡优惠  或者套餐卡支付
                foreach ($pks as $v) {
                    ChargeInfo::updateAll(['charge_record_id' => $chargeRecordModel->id, 'status' => 1, 'card_discount_price' => $params['chargeInfoArray'][$v]], ['id' => $v, 'record_id' => $orderModel->record_id, 'spot_id' => $orderModel->spot_id]);
                }
                if($cardFlowId != 0){//若为充值卡支付，则更新其收费交易流水id
                    CardFlow::updateAll(['f_charge_record_id' => $chargeRecordModel->id],['f_physical_id' => $cardFlowId,'f_spot_id' => self::$staticSpotId]);
                }
            } else {
                ChargeInfo::updateAll(['charge_record_id' => $chargeRecordModel->id, 'status' => 1], ['id' => $pks, 'record_id' => $orderModel->record_id, 'spot_id' => $orderModel->spot_id]);
            }
            return $chargeRecordModel->id;
            
        }
    }

    /**
     * @param unknown $orderModel
     * @param 回调参数 $params
     * @param 支付类型(1-现金,2-刷卡,3-微信支付,4-支付宝支付) $type
     * @param $allPrice 总支付金额原价
     * @param $income 实收费用(只针对现金支付)
     * @param $change 找零(只针对现金支付)
     * @param $chargeRecordModel chargeRecordModel 对象
     * @return 扫描枪支付
     */
    public static function scannerPayment($orderModel, $params, $type, $chargeRecordModel = null, $subject = '') {
        try {
            if ($type == 3) {
                return self::scannerPaymentByWechat($orderModel, $params, $type, $chargeRecordModel, $subject);
            } elseif ($type == 4) {
                return self::scannerPaymentByAlipay($orderModel, $params, $type, $chargeRecordModel, $subject);
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
    protected static function scannerPaymentByWechat($orderModel, $params, $type, $chargeRecordModel = null, $subject = '') {
        try {
            if (!$chargeRecordModel->wechatAuthCode) {
                throw new Exception('wechatAuthCode Wrong');
            }
            include_once(Yii::getAlias('@ServicePath') . "/wechat/WxPay.MicroPay.php");
            $input = new \WxPayMicroPay();
            $outTradeNo = $orderModel->out_trade_no . self::scanSuffix;
            $input->SetAuth_code($chargeRecordModel->wechatAuthCode);
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
                $pksCacheKey = Yii::getAlias('@wxPayChargeItem') . $orderModel->out_trade_no;
                $body = json_decode($params['body'], true);
                Yii::$app->cache->set($pksCacheKey, $body['pks']);
                $rows['pks'] = $pksCacheKey;
                $rows['patient_id'] = $body['patient_id'];
                $data['attach'] = json_encode($rows);
                return self::addOrderLogByWechat($data, 3);
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
    protected static function scannerPaymentByAlipay($orderModel, $params, $type, $chargeRecordModel, $subject) {
        try {
            if (!$chargeRecordModel->alipayAuthCode) {
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
            $barPayRequestBuilder->setAuthCode($chargeRecordModel->alipayAuthCode);
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
                $data['body'] = $params['body'];
                $data['buyer_id'] = isset($data['buyer_id']) ? $data['buyer_id'] : '';
                $data['seller_id'] = isset($data['seller_id']) ? $data['seller_id'] : '';
                $data['seller_email'] = isset($data['seller_email']) ? $data['seller_email'] : '';
                return self::addOrderLog($data, 4, $pConfig);
            } else{
                $data = (array) $qrPayResult->getResponse();
                $result = self::checkQueryOrder($data, $config);//轮询查询订单状态
                if($result['code'] == '10000' || $result['trade_status'] == 'TRADE_SUCCESS' || $result['trade_status'] == 'TRADE_FINISHED'){//支付成功
                    $result['body'] = $params['body'];
                    $result['buyer_id'] = isset($result['buyer_user_id']) ? $result['buyer_user_id'] : '';
                    $result['seller_id'] = isset($result['seller_id']) ? $result['seller_id'] : '';
                    $result['seller_email'] = isset($result['seller_email']) ? $result['seller_email'] : '';
                    $result['gmt_payment'] = isset($result['send_pay_date'])?$result['send_pay_date']:date('Y-m-d H:i',time());
                    Yii::info($result,'checkQueryOrder-result');
                    return self::addOrderLog($result, 4, $pConfig);
                }else{
                    Yii::error($data,'payment by alipay failed-error-data');
                    Yii::error($result,'payment by alipay failed-error-result');
                    throw new Exception('payment by alipay failed orderNo: ' . $orderModel->out_trade_no);
                }
                
            }
            
            //            switch ($qrPayResult->getTradeStatus()) {
            //                case "SUCCESS":
            //                    $result['errorCode'] = 0;
            //                    $result['msg'] = 'success';
            //                    break;
            //                case "FAILED":
            //                    $result['errorCode'] = $qrPayResult->getResponse()->code;
            //                    $result['msg'] = '支付宝支付失败!!!';
            //                    break;
            //                case "UNKNOWN":
            //                    $result['errorCode'] = $qrPayResult->getResponse()->code;
            //                    $result['msg'] = '系统异常，状态未知!!!';
            //                    break;
            //                default:
            //                    $result['errorCode'] = 1001;
            //                    $result['msg'] = '不支持的交易状态，交易返回异常!!!';
            //                    break;
            //            }
        } catch (Exception $e) {
            Yii::info('scannerPaymentByAlipay failed; ' . $e->getMessage());
            throw new Exception('scannerPaymentByAlipay failed; ' . $e->getMessage());
        }
    }
    
    /**
     * @desc 查询支付宝订单支付状态
     * @param string $outTradeNo 订单号
     */
    public static function alipayQueryOrder($outTradeNo,$config){
        //第三方应用授权令牌,商户授权系统商开发模式下使用
        $appAuthToken = "";//根据真实值填写
        
        //构造查询业务请求参数对象
        $queryContentBuilder = new \AlipayTradeQueryContentBuilder();
        $queryContentBuilder->setOutTradeNo($outTradeNo);
        
        $queryContentBuilder->setAppAuthToken($appAuthToken);
        
        
        //初始化类对象，调用queryTradeResult方法获取查询应答
        $queryResponse = new \AlipayTradeService($config);
        $queryResult = $queryResponse->queryTradeResult($queryContentBuilder);
        
        //根据查询返回结果状态进行业务处理
        switch ($queryResult->getTradeStatus()){
            case "SUCCESS":
                Yii::info('支付宝查询交易成功','alipayQueryOrder-success');
                return (array)$queryResult->getResponse();
                break;
            case "FAILED":
                Yii::error('支付宝查询交易失败或者交易已关闭!!!','alipayQueryOrder-error');
                
                if(!empty($queryResult->getResponse())){
                    return (array)$queryResult->getResponse();
                }
                break;
            case "UNKNOWN":
                Yii::error('系统异常，订单状态未知!!!','alipayQueryOrder-error');
                
                if(!empty($queryResult->getResponse())){
                    return (array)$queryResult->getResponse();
                }
                break;
            default:
                Yii::error('不支持的查询状态，交易返回异常!!!','alipayQueryOrder-error');
                return [
                    'code' => 20000,
                    'msg' => '不支持的查询状态，交易返回异常!!!'
                ];
                break;
        }
        return ;
    }
    /**
     * @desc 轮询去查询支付宝订单状态。并返回
     * @param array $data 支付订单返回信息
     * @param array $config 支付宝配置
     * @return void|array|number[]|string[]
     */
    public static function checkQueryOrder($data,$config){
        
        if($data['code'] == '20000'){//系统异常
            
            for ($i = 0;$i < 5;$i++){//轮询5次，若返回成功，跳出轮询
                try{
                    sleep(3);
                }catch (Exception $e){
                    Yii::info($e->getMessage(),'checkQueryOrder-error');
                    exit();
                }
                
                $result = self::alipayQueryOrder($data['out_trade_no'], $config);
                if(!empty($result)){
                    if(self::stopQuery($result)){
                        return $result;
                    }
                    $queryResult = $result;
                }
            }
            return $queryResult;
            
        }
    }
    // 判断是否停止查询
    public static function stopQuery($response){
        if("10000"==$response['code']){
            if("TRADE_FINISHED"==$response['trade_status']||
                "TRADE_SUCCESS"==$response['trade_status']||
                "TRADE_CLOSED"==$response['trade_status']){
                return true;
            }
        }
        return false;
    }

}
