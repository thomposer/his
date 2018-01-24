<?php

namespace app\modules\api\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\modules\charge\models\PaymentLog;
use yii\log\Logger;
use app\modules\spot_set\models\PaymentConfig;
use app\modules\charge\models\Order;
use app\specialModules\recharge\models\PackagePaymentLog;
use app\specialModules\recharge\models\CardOrder;
class PayController extends Controller
{
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                   'index' => ['post'],
                    'recharge-index' => ['post']
                ],
            ],
        ];
    }
    public function beforeAction($action)
    {
        require_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/model/builder/AlipayTradeRefundContentBuilder.php");
        require_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/service/AlipayTradeService.php");
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    
    /**
     * index
     * @param string $trade_status 订单状态
     * @param string $out_trade_no 订单号
     * 
     * @return string success 支付成功
     * @return string fail 支付失败
     * @desc 支付宝异步通知接口api
     */
    public function actionIndex() {
        
        $params = Yii::$app->request->post();
        include_once(Yii::getAlias('@ServicePath') . "/pay/aop/AopClient.php");
        //若支付成功，修改订单状态，同时创建支付日志
        if(isset($params['trade_status']) && ($params['trade_status'] == 'TRADE_SUCCESS' || $params['trade_status'] == 'TRADE_FINISHED')){
            Yii::$app->log->logger->log($params, Logger::LEVEL_INFO,'/api/pay/index');
            $list = PaymentConfig::getConfig($params['out_trade_no'],2);
            Yii::$app->log->logger->log($list, Logger::LEVEL_INFO,'/api/pay/index');
            if($list){
                $config = include(Yii::getAlias('@ServicePath') . "/pay/f2fpay/config/config.php");
                $aopClient = new \AopClient();
                $aopClient->alipayrsaPublicKey = $list['payment_key'];
                $aopClient->rsaPrivateKey = $list['mchid'];
                $aopClient->appId = $list['appid'];
                $aopClient->gatewayUrl = $config['gatewayUrl'];
                $aopClient->apiVersion = "1.0";
                $aopClient->postCharset = $config['charset'];
                $aopClient->format = 'json';
                $result = $aopClient->rsaCheckV1($params,$list['payment_key']);//验签
                Yii::$app->log->logger->log($result, Logger::LEVEL_INFO,'/api/pay/index');
                if($result){
                   $rows = PaymentLog::addOrderLog($params,4,$list);
                   if($rows['errorCode'] == 0){
                       echo 'success';
                   }
                }
            }else{
                echo  'fail';
            }
            
            
        }else{
            echo 'fail';
        }
        
    }
    
    /**
     * recharge-index
     * @param string $trade_status 订单状态
     * @param string $out_trade_no 订单号
     *
     * @return string success 支付成功
     * @return string fail 支付失败
     * @desc 支付宝异步通知接口api-充值卡充值回调
     */
    public function actionRechargeIndex() {
    
        $params = Yii::$app->request->post();
        include_once(Yii::getAlias('@ServicePath') . "/pay/aop/AopClient.php");
        //若支付成功，修改订单状态，同时创建支付日志
        if(isset($params['trade_status']) && ($params['trade_status'] == 'TRADE_SUCCESS' || $params['trade_status'] == 'TRADE_FINISHED')){
            Yii::$app->log->logger->log($params, Logger::LEVEL_INFO);
            $list = \app\specialModules\recharge\models\Order::getPaymentConfig($params['out_trade_no'],2);
            Yii::$app->log->logger->log($list, Logger::LEVEL_INFO);
            if($list){
                $config = include(Yii::getAlias('@ServicePath') . "/pay/f2fpay/config/config.php");
                $aopClient = new \AopClient();
                $aopClient->alipayrsaPublicKey = $list['payment_key'];
                $aopClient->rsaPrivateKey = $list['mchid'];
                $aopClient->appId = $list['appid'];
                $aopClient->gatewayUrl = $config['gatewayUrl'];
                $aopClient->apiVersion = "1.0";
                $aopClient->postCharset = $config['charset'];
                $aopClient->format = 'json';
                $result = $aopClient->rsaCheckV1($params,$list['payment_key']);//验签
                Yii::$app->log->logger->log($result, Logger::LEVEL_INFO);
                if($result){
                    $rows = \app\specialModules\recharge\models\PaymentLog::addOrderLog($params,4,$list);
                    if($rows['errorCode'] == 0){
                        return 'success';
                    }
                }
            }else{
                return 'fail';
            }
    
    
        }
    
    }
    
    /**
     * index
     * @param string $trade_status 订单状态
     * @param string $out_trade_no 订单号
     *
     * @return string success 支付成功
     * @return string fail 支付失败
     * @desc 套餐卡--支付宝异步通知接口api
     */
    public function actionPackageCard() {
    
        $params = Yii::$app->request->post();
        include_once(Yii::getAlias('@ServicePath') . "/pay/aop/AopClient.php");
        //若支付成功，修改订单状态，同时创建支付日志
        if(isset($params['trade_status']) && ($params['trade_status'] == 'TRADE_SUCCESS' || $params['trade_status'] == 'TRADE_FINISHED')){
            Yii::$app->log->logger->log($params, Logger::LEVEL_INFO,'apiPayPackageCard');
            $list = CardOrder::getConfig($params['out_trade_no'],2);
            Yii::$app->log->logger->log($list, Logger::LEVEL_INFO,'apiPayPackageCard');
            if($list){
                $config = include(Yii::getAlias('@ServicePath') . "/pay/f2fpay/config/config.php");
                $aopClient = new \AopClient();
                $aopClient->alipayrsaPublicKey = $list['payment_key'];
                $aopClient->rsaPrivateKey = $list['mchid'];
                $aopClient->appId = $list['appid'];
                $aopClient->gatewayUrl = $config['gatewayUrl'];
                $aopClient->apiVersion = "1.0";
                $aopClient->postCharset = $config['charset'];
                $aopClient->format = 'json';
                $result = $aopClient->rsaCheckV1($params,$list['payment_key']);//验签
                Yii::$app->log->logger->log($result, Logger::LEVEL_INFO,'/api/pay/index');
                if($result){
                    $rows = PackagePaymentLog::addOrderLog($params,4,$list);
                    if($rows['errorCode'] == 0){
                        echo 'success';
                    }
                }
            }else{
                echo  'fail';
            }
    
    
        }else{
            echo 'fail';
        }
    
    }
    
}
