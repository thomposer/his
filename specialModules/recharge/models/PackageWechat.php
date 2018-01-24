<?php

namespace app\specialModules\recharge\models;

use Yii;
use app\modules\spot_set\models\PaymentConfig;
use app\specialModules\recharge\models\PackagePaymentLog;

class PackageWechat extends \yii\db\ActiveRecord
{

    public function __construct($config = array()) {
        parent::__construct($config);
        include_once(Yii::getAlias('@ServicePath') . "/wechat/WxPay.Data.php");
        include_once(Yii::getAlias('@ServicePath') . "/wechat/WxPay.Api.php");
    }

    public function behaviors() {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }

    /**
     * 
     * 回调入口
     * @param bool $needSign  是否需要签名输出
     */
    final public function Handle($needSign = true) {
        $msg = "OK";
        //当返回false的时候，表示notify中调用NotifyCallBack回调失败获取签名校验失败，此时直接回复失败
        $this->setSpotInfo();
        $result = \WxpayApi::notify(array($this, 'NotifyCallBack'), $msg);
        $wpdb = new \WxPayNotifyReply();
        if ($result == false) {
            $wpdb->SetReturn_code("FAIL");
            $wpdb->SetReturn_msg($msg);
            $this->ReplyNotify(false, $wpdb);
            return;
        } else {
            //该分支在成功回调到NotifyCallBack方法，处理完成之后流程
            $wpdb->SetReturn_code("SUCCESS");
            $wpdb->SetReturn_msg("OK");
        }
        $this->ReplyNotify($needSign, $wpdb);
    }

    /**
     * 
     * 回调方法入口，子类可重写该方法
     * 注意：
     * 1、微信回调超时时间为2s，建议用户使用异步处理流程，确认成功之后立刻回复微信服务器
     * 2、微信服务器在调用失败或者接到回包为非确认包的时候，会发起重试，需确保你的回调是可以重入
     * @param array $data 回调解释出的参数
     * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
     * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    public function NotifyProcess($data, &$msg) {
        Yii::info("call back:" . json_encode($data));
        $notfiyOutput = array();

        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            return false;
        }
        //修改订单状态等  一系列操作
        $this->modifyOrder($data);
        return true;
    }

    /**
     * 
     * notify回调方法，该方法中需要赋值需要输出的参数,不可重写
     * @param array $data
     * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    final public function NotifyCallBack($data) {
        $msg = "OK";
        $result = $this->NotifyProcess($data, $msg);
        return $result;
    }

    /**
     * 
     * 回复通知
     * @param bool $needSign 是否需要签名输出
     */
    final private function ReplyNotify($needSign = true, $wpdb) {
        //如果需要签名
        if ($needSign == true && $wpdb->GetReturn_code() == "SUCCESS") {
            $wpdb->SetSign();
        }
        \WxpayApi::replyNotify($wpdb->ToXml());
    }

    //查询订单
    protected function Queryorder($transaction_id) {
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = \WxPayApi::orderQuery($input);
        Yii::info("query:" . json_encode($result));
        if (array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
            return true;
        }
        return false;
    }

    protected function setSpotInfo() {
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        libxml_disable_entity_loader(true);
        $requestData = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        Yii::info('requestData:[' . json_encode($requestData) . ']');
        if ($requestData['return_code'] == 'SUCCESS') {//回调成功
            $outTradeNo = $requestData['out_trade_no'];
            $list = PaymentConfig::getConfigByPackage($requestData['out_trade_no'], 1); //此处需要调用充值卡的order 获取支付配置信息
            Yii::info('paymentConfigList:[' . json_encode($list) . ']');
            if ($list) {
                $expireTime = time() + 600;
                $res = setcookie('wechatSpotId', $list['spot_id'], $expireTime, '/', null, null, true);
            }
        }
    }

    /**
     * 修改订单状态  
     */
    public function modifyOrder($data) {
        Yii::info('modifyOrder:[' . json_encode($data) . ']');
        PackagePaymentLog::addOrderLogByWechat($data, 3);
    }

    /**
     * 
     * @param type $refund_no 退款单号(商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔)
     * @param type $transaction_id 交易号
     * @param type $total_fee 订单总金额(订单总金额，单位为分，只能为整数)
     * @param type $refund_fee 退款金额(退款总金额，订单总金额，单位为分)
     * @return boolean 退款 
     */
    public static function refund($refund_no, $out_trade_no, $total_fee, $refund_fee) {
        Yii::info('wechat_refund_' . $_COOKIE['wechatSpotId'] . ':]' . json_encode(['refund_no' => $refund_no, 'total_fee' => $total_fee]) . ']');
        $input = new \WxPayRefund();
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee($total_fee);
        $input->SetRefund_fee($refund_fee);
        $input->SetOut_refund_no($refund_no);
        $cacheKey = Yii::getAlias('@wxPayConfig') . $_COOKIE['wechatSpotId'];
        $config = Yii::$app->cache->get($cacheKey);
        $input->SetOp_user_id($config ? $config['mchid'] : '');
        $result = \WxPayApi::refund($input);
        Yii::info('wechat_refund_result_' . $_COOKIE['wechatSpotId'] . ':]' . json_encode($result) . ']');
        if (array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
            return true;
        }
        return false;
    }

}
