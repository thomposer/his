<?php

namespace app\modules\spot_set\controllers;

use Yii;
use yii\web\Response;
use app\modules\spot_set\models\PaymentConfig;
use app\modules\spot_set\models\search\PaymenyConfigSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\charge\models\Order;

/**
 * PaymentConfigController implements the CRUD actions for PaymentConfig model.
 */
class PaymentConfigController extends BaseController
{

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function __construct($id, $module, $config = array()) {
        parent::__construct($id, $module, $config);
        include_once(Yii::getAlias('@ServicePath') . "/wechat/WxPay.Api.php");
        include_once(Yii::getAlias('@ServicePath') . "/wechat/WxPay.NativePay.php");
        include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/model/builder/ExtendParams.php");
        include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/model/builder/GoodsDetail.php");
        include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/model/builder/AlipayTradePrecreateContentBuilder.php");
        include_once(Yii::getAlias('@ServicePath') . "/pay/f2fpay/service/AlipayTradeService.php");
    }

    /**
     * Lists all PaymentConfig models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new PaymenyConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new PaymentConfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * 支付宝支付配置
     */
    public function actionPay() {
        $type = 2;//支付宝支付类型
        $model = $this->findModel($type);
        $model->type = $type;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
        }
        return $this->render('pay', [
                    'model' => $model,
                    'title' => '支付宝支付',
                    'type' => $type
        ]);
    }
    /**
     * @return 支付宝支付预览二维码
     */
    public function actionPayView(){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if($request->isAjax){
            $sendType = Yii::$app->request->post('sendType',2);
            if ($sendType == 1) {//只生成二维码
              $params = Yii::$app->request->post();
              if(!$params['appid'] || !$params['mchid'] || !$params['payment_key']){
                    $this->result['errorCode'] = 1001;//参数错误
                    $this->result['msg'] = '请填写完整相关信息';
                    return $this->result;
               }
                // (必填) 商户网站订单系统中唯一订单号，64个字符以内，只能包含字母、数字、下划线，
                // 需保证商户系统端不能重复，建议通过数据库sequence生成，
                $outTradeNo = Order::generateOutTradeNo();
            
                // (必填) 订单标题，粗略描述用户的支付目的。如“xxx品牌xxx门店当面付扫码消费”
                $subject = '测试二维码(1分钱)';
            
                // (必填) 订单总金额，单位为元，不能超过1亿元
                // 如果同时传入了【打折金额】,【不可打折金额】,【订单总金额】三者,则必须满足如下条件:【订单总金额】=【打折金额】+【不可打折金额】
                $totalAmount = 0.01;
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
                $body = "测试二维码(1分钱)";
            
                //商户操作员编号，添加此参数可以为商户操作员做销售统计
                $operatorId = "";
            
                // (必填) 商户门店编号，通过门店号和商家后台可以配置精准到门店的折扣信息，详询支付宝技术支持
                $storeId = "";
            
                // 支付宝的店铺编号
                $alipayStoreId= "";
            
                // 业务扩展参数，目前可添加由支付宝分配的系统商编号(通过setSysServiceProviderId方法)，系统商开发使用,详情请咨询支付宝技术支持
                $providerId = ""; //系统商pid,作为系统商返佣数据提取的依据
                $extendParams = new \ExtendParams();
                $extendParams->setSysServiceProviderId($providerId);
                $extendParamsArr = $extendParams->getExtendParams();
            
                // 支付超时，线下扫码交易定义为5分钟
                $timeExpress = "5m";
            
                // 商品明细列表，需填写购买商品详细信息，
                $goodsDetailList = array();
            
                // 创建一个商品信息，参数含义分别为商品id（使用国标）、名称、单价（单位为分）、数量，如果需要添加商品类别，详见GoodsDetail
                $goods1 = new \GoodsDetail();
                $goods1->setGoodsId("体验支付");
                $goods1->setGoodsName("体验支付");
                $goods1->setPrice(0.1);
                $goods1->setQuantity(1);
                //得到商品1明细数组
                $goods1Arr = $goods1->getGoodsDetail();
            
                $goodsDetailList = array($goods1Arr);
            
                //第三方应用授权令牌,商户授权系统商开发模式下使用
                $appAuthToken = "";//根据真实值填写
            
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
                $config['app_id'] = $params['appid'];
                $config['merchant_private_key'] = $params['mchid'];//商户私钥
                $config['alipay_public_key'] = $params['payment_key'];//支付宝密钥
                $config['notify_url'] = Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@apiPayIndex')]);
                // 调用qrPay方法获取当面付应答
                $qrPay = new \AlipayTradeService($config);
                $qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);
                //	根据状态值进行业务处理
                switch ($qrPayResult->getTradeStatus()){
                    case "SUCCESS":
                        //echo "支付宝创建订单二维码成功:"."<br>---------------------------------------<br>";
                        Order::addOrder(0, $this->userInfo->id,$outTradeNo,$subject,$totalAmount,2);
                        $response = $qrPayResult->getResponse();
                        $this->result['code_url'] = $response->qr_code;
                        
                        return $this->result;
                        break;
                    case "FAILED":
//                         echo "支付宝创建订单二维码失败!!!"."<br>--------------------------<br>";
//                         if(!empty($qrPayResult->getResponse())){
//                             print_r($qrPayResult->getResponse());
//                         }
                        $content = '创建订单二维码失败，请检查配置项';
                        if($qrPayResult->getResponse()->code == 40002){
                            $content = '您使用的商户私钥格式错误';
                        }
                        $this->result['errorCode'] = $qrPayResult->getResponse()->code;
                        $this->result['msg'] = $content;
                        return $this->result;
                        break;
                    case "UNKNOWN":
//                         echo "系统异常，状态未知!!!"."<br>--------------------------<br>";
//                         if(!empty($qrPayResult->getResponse())){
//                             print_r($qrPayResult->getResponse());
//                         }
                        $this->result['errorCode'] = $qrPayResult->getResponse()->code;
                        $this->result['msg'] = '系统异常，状态未知!!!';
                        return $this->result;
                        break;
                    default:
                        $this->result['errorCode'] = 1001;
                        $this->result['msg'] = '不支持的返回状态，创建订单二维码返回异常!!!';
                        return $this->result;
                        break;
                }
                return ;
            }else {
                $code_url = Yii::$app->request->post('codeUrl');
                return [
                    'title' => "支付宝扫码支付二维码预览",
                    'content' => $this->renderAjax('_qrcode', [
                        'code_url' => $code_url,
                    ]),
                ];
            }
        }else{
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
    /**
     * 生成预览二维码
     */
    public function actionQrcodeView() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $sendType = Yii::$app->request->post('sendType');
        if ($sendType == 1) {//只生成二维码
            $paymentConfig = Yii::$app->request->post('PaymentConfig');
            $cacheKey = Yii::getAlias('@wxPayConfig') . $_COOKIE['wechatSpotId'];
            if ($paymentConfig['appid'] && $paymentConfig['mchid'] && $paymentConfig['payment_key']) {//先设置缓存
                $config['appid'] = $paymentConfig['appid'];
                $config['mchid'] = $paymentConfig['mchid'];
                $config['key'] = $paymentConfig['payment_key'];
                Yii::$app->cache->set($cacheKey, $config, 0);
            }
            $input = new \WxPayUnifiedOrder();
            $input->SetBody("测试二维码(1分钱)");
            $input->SetAttach("测试二维码");
            $input->SetOut_trade_no($config['mchid'] . date("YmdHis")); //订单ID
            $input->SetTotal_fee("1");
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("测试二维码");
            $notify_url=Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@apiWechatCallbackNotify')]);
            $input->SetNotify_url($notify_url);
            $input->SetTrade_type("NATIVE");
            $input->SetProduct_id("123456789"); //商品ID
            $notify = new \NativePay();
            $result = $notify->GetPayUrl($input);
            $code_url = $result["code_url"];
            if (empty($code_url)) {
                //二维码连接生成失败  则删除缓存
                Yii::$app->cache->delete($cacheKey);
            }
            $this->result['code_url'] = $code_url;
            return $this->result;
        } else {
            $code_url = Yii::$app->request->post('codeUrl');
            return [
                'title' => "微信扫码支付二维码预览",
                'content' => $this->renderAjax('_qrcode', [
                    'code_url' => $code_url,
                ]),
            ];
        }
    }

    /**
     * Updates an existing PaymentConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate() {
        $type = 1;//支付宝支付类型
        $model = $this->findModel($type);
        $model->type = $type;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id){
        if (($model = PaymentConfig::findOne(['id' => $id,'spot_id' => $this->spotId])) !== null) {
                $model->delete();
                Yii::$app->getSession()->setFlash('success', '删除成功');
                $this->redirect(Yii::$app->request->referrer);
            } else {
                throw new NotFoundHttpException('你所请求的页面不存在');
            }        
    }
    /**
     * Finds the PaymentConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PaymentConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($type) {
        if (($model = PaymentConfig::findOne(['spot_id' => $this->spotId,'type' => $type])) !== null) {
            return $model;
        } else {
            return new PaymentConfig();
        }
    }

}
