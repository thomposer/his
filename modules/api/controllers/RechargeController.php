<?php

namespace app\modules\api\controllers;
use app\modules\patient\models\Patient;
use Yii;
use app\modules\api\controllers\CommonController;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\specialModules\recharge\models\Order;
use app\specialModules\recharge\models\CardRecharge;
use yii\db\Query;
use app\modules\spot\models\CardRechargeCategory;
use app\modules\spot_set\models\CardDiscountClinic;
use app\common\Common;
use app\specialModules\recharge\models\CardOrder;

class RechargeController extends CommonController{
    
    public function behaviors() {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'check' => ['post'],
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }
    
        /**
         * check
         * @param string $out_trade_no 订单号
         * @param string $total_amount 总金额
         * @return int errorCode 错误代码(0-支付成功,1001-参数错误,1002-订单不存在,1003-订单未支付,1004-订单支付失败,1005-订单已过期)
         * @return string msg 提示信息
         * @desc 检查订单支付状态
         */
        public function actionCheck(){
            
            $out_trade_no = Yii::$app->request->post('out_trade_no');
            $total_amount = Yii::$app->request->post('total_amount');
            Yii::$app->response->format = Response::FORMAT_JSON;
            if(!$out_trade_no || !$total_amount){
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '参数错误';
                return $this->result;
            }
            $result = Order::find()->select(['status'])->where(['out_trade_no' => $out_trade_no,'total_amount' => $total_amount,'spot_id' => $this->spotId])->asArray()->one();
            if(!$result){
                $this->result['errorCode'] = 1002;
                $this->result['msg'] = '订单不存在';
                return $this->result;
            }
            if ($result['status'] == 1){
                $this->result['errorCode'] = 1003;
                $this->result['msg'] = '订单未支付';
            }else if ($result['status'] == 3){
                $this->result['errorCode'] = 1004;
                $this->result['msg'] = '订单支付失败';
            }else if($result['status'] == 5){
                $this->result['errorCode'] = 1005;
                $this->result['msg'] = '订单已过期';
            }else{
                $this->result['msg'] = '订单支付成功';
            }
            return $this->result;
        }

    /**
     * @param string $phone 用户填写的手机号
     * @return int errorCode 错误代码(0-支付成功,1001-参数错误)
     * @return string msg 提示信息
     * @return array data 手机号已经购买的充值卡卡种信息
     * @desc 获取当前手机号已经购买的充值卡卡种信息
     */
    public function actionGetPhoneCardCategory(){
        $params = Yii::$app->request->post();
        $phone = $params['phone'];
        $search_type = empty($params['search_type']) ? 0 : $params['search_type'] ;

        Yii::$app->response->format = Response::FORMAT_JSON;
        if(!$phone){
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }else{
            $data = [];
            if(strlen($phone) == 11){
                $data = CardRecharge::getPhoneCardCategory($phone);
            }
            $this->result['data'] = $data;

            $dataFamily = [];
            if($search_type == 0){
                $dataFamily = Patient::findFamilyInfo($phone);
            }
            $this->result['familyData'] = $dataFamily;
            return $this->result;
        }
    }
    
    
    /**
     * get-card-discount-price
     * @param string price 收费金额
     * @param integer id 卡种id
     * @param integer originalPrice 是否打折，1-不打折，0-打折。默认打折
     * @desc 计算套餐卡支付时卡种相关标签折扣的优惠金额
     */
    public function actionGetCardDiscountPrice() {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isAjax) {
            $id = $request->post('id'); //卡id
            $outTradeNo = $request->post('outTradeNo');//订单号
            $originalPrice = $request->post('originalPrice', 0); //是否打折
            if (!$outTradeNo || !$id) {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '参数错误';
                return $this->result;
            }
            $cardCacheKey = Yii::getAlias('@wxPayPackageCardItem') . $outTradeNo;
            $info = json_decode(Yii::$app->cache->get($cardCacheKey), true);
            $totalPrice = [];
            $query = new Query();
            $query->from(['a' => CardRecharge::tableName()]);
            $query->select(['a.f_physical_id', 'b.f_medical_fee_discount', 'c.tag_id', 'c.discount']);
            $query->leftJoin(['b' => CardRechargeCategory::tableName()], '{{a}}.f_category_id = {{b}}.f_physical_id');
            $query->leftJoin(['c' => CardDiscountClinic::tableName()], '{{b}}.f_physical_id = {{c}}.recharge_category_id');
            $query->where(['a.f_physical_id' => $id, 'c.spot_id' => $this->spotId]);
            $query->indexBy('tag_id');
            $cardDiscountInfo = $query->all(CardRecharge::getDb());
            if (empty($cardDiscountInfo)) {
                $this->result['errorCode'] = 1006;
                $this->result['msg'] = '请先设置会员卡的折扣比例';
                return $this->result;
            }
            //获取订单价格
            $orderInfo = CardOrder::find()->select(['total_amount'])->where(['out_trade_no' => $outTradeNo,'spot_id' => $this->spotId,'status' => 1])->asArray()->one();
            $allPrice = Common::num($orderInfo['total_amount']);
            $balance = CardRecharge::find()->select(['totalFee' => '(f_donation_fee+f_card_fee)'])->where(['f_physical_id' => $id, 'f_parent_spot_id' => $this->parentSpotId])->asArray()->one();
            $this->result['allPrice'] = $allPrice;
            $this->result['totalFee'] = $balance['totalFee'];
            if ($balance['totalFee'] < $allPrice) {
                $this->result['errorCode'] = 1005;
                $this->result['msg'] = '卡内余额不足';
                return $this->result;
            }
    
            $this->result['price'] = $allPrice;
            return $this->result;
        } else {
            $this->result['errorCode'] = 405;
            $this->result['msg'] = '非法请求';
            return $this->result;
        }
    }
    
    
    
}