<?php

namespace app\modules\api\controllers;

use app\modules\outpatient\models\CheckRecord;
use app\modules\outpatient\models\InspectRecord;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\Outpatient;
use app\modules\outpatient\models\PackageRecord;
use app\modules\outpatient\models\RecipeRecord;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\modules\charge\models\Order;
use yii\web\Response;
use app\modules\charge\models\ChargeInfo;
use app\common\Common;
use app\modules\charge\models\ChargeRecord;
use app\modules\spot_set\models\PaymentConfig;
use yii\helpers\Html;
use yii\log\Logger;
use app\modules\user\models\User;
use yii\db\Query;
use app\modules\spot\models\Spot;
use app\modules\patient\models\Patient;
use app\modules\spot\models\RecipeList;
use yii\web\NotAcceptableHttpException;
use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use app\specialModules\recharge\models\CardRecharge;
use app\modules\spot\models\CardDiscount;
use app\modules\spot_set\models\CardDiscountClinic;
use app\modules\spot\models\CardRechargeCategory;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use app\common\base\MultiModel;
use app\modules\charge\models\MaterialCharge;
use app\modules\spot_set\models\Material;
use app\modules\patient\models\PatientRecord;
use app\modules\charge\models\FirstOrderFree;
use yii\helpers\Url;
use app\modules\outpatient\models\MaterialRecord;
use app\modules\stock\models\MaterialStockInfo;
use app\modules\stock\models\MaterialStock;
use app\modules\charge\models\ChargeRecordLog;
use app\modules\charge\models\ChargeInfoLog;
use app\modules\spot\models\SpotConfig;
use app\specialModules\recharge\models\CardOrder;
use app\modules\stock\models\MaterialStockDeductionRecord;

class ChargeController extends CommonController
{

    public function behaviors() {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'check' => ['post'],
                    'generate-code' => ['post'],
                    'print-info' => ['post'],
                    'get-card-discount-prie' => ['post'],
                    'charge-log-print-data' => ['post'],
                    'charge-print-data' => ['post'],
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    /**
     * check
     * @param string $out_trade_no 订单号
     * @param string $total_amount 总金额
     *
     * @return int errorCode 错误代码(0-支付成功,1001-参数错误,1002-订单不存在,1003-订单未支付,1004-订单支付失败,1005-订单已过期)
     * @return string msg 提示信息
     * @desc 检查订单支付状态
     */
    public function actionCheck() {

        $out_trade_no = Yii::$app->request->post('out_trade_no');
        $total_amount = Yii::$app->request->post('total_amount');
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$out_trade_no || !$total_amount) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $result = Order::find()->select(['status'])->where(['out_trade_no' => $out_trade_no, 'total_amount' => $total_amount, 'spot_id' => $this->spotId])->asArray()->one();
        if (!$result) {
            $this->result['errorCode'] = 1002;
            $this->result['msg'] = '订单不存在';
            return $this->result;
        }
        if ($result['status'] == 1) {
            $this->result['errorCode'] = 1003;
            $this->result['msg'] = '订单未支付';
        } else if ($result['status'] == 3) {
            $this->result['errorCode'] = 1004;
            $this->result['msg'] = '订单支付失败';
        } else if ($result['status'] == 5) {
            $this->result['errorCode'] = 1005;
            $this->result['msg'] = '订单已过期';
        } else {
            $this->result['msg'] = '订单支付成功';
            $this->result["data"]["charge_log_id"] = $this->findChargeLogIdByOrderId($out_trade_no);
        }
        return $this->result;
    }

    private function findChargeLogIdByOrderId($orderId) {
        $result = (new Query())
                ->select("id")
                ->from(ChargeRecordLog::tableName())
                ->where(["out_trade_no" => $orderId])
                ->one();
        return $result["id"];
    }

    /**
     * generate-code
     * @param string $pks 收费清单id，已逗号隔开
     * @param int $recordId 就诊流水id
     * @param int $discountType 优惠类型(1-无，2-金额扣减，3-折扣)
     * @param int $discountPrice 优惠金额/折扣比例
     * @param string $discountReason 优惠原因
     * @param string $username 患者姓名
     * @param int $patient_id 患者id
     *
     * @return int errorCode 错误代码(0-成功,1009-参数错误,1008-最多保留两位小数，1007-订单已支付,1006-优惠金额不能小于0,1005-优惠折扣不能大于100%,1004-优惠折扣不能小于0,1003-优惠金额不能大于实际应付金额)
     * @return string msg 错误提示信息
     * @return string aliPayUrl 支付宝支付二维码url
     * @return string wechatUrl 微信支付二维码url
     * @return string outTradeNo 订单号
     * @return string price 打折后需支付的总金额
     *
     * @desc 收费打折时，重新生成订单
     */
    public function actionGenerateCode() {
        $pks = Yii::$app->request->post('pks'); //收费清单id
        $id = Yii::$app->request->post('recordId'); //就诊流水id
        $discountType = Yii::$app->request->post('discountType'); //优惠类型
        $discountPrice = Yii::$app->request->post('discountPrice'); //优惠金额
        $username = Yii::$app->request->post('username'); //患者姓名
        $patientId = Yii::$app->request->post('patient_id');
        $discountReason = Yii::$app->request->post('discountReason');
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$pks || !$id || !$discountType || $discountPrice === null || !$username || !$patientId) {
            $this->result['errorCode'] = 1009; //参数错误
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        if ($discountPrice >= 0 && !preg_match("/^([0-9][0-9]*)+(.[0-9]{1,2})?$/", $discountPrice)) {
            $this->result['errorCode'] = 1008;
            $this->result['msg'] = '最多保留两位小数';
            return $this->result;
        }
        $info = [
            'username' => $username,
            'patient_id' => $patientId
        ];
        $pks = explode(',', $pks); // Array or selected records primary keys
        $rows = ChargeInfo::find()->select(['id', 'name', 'unit_price', 'num'])->where(['id' => $pks, 'record_id' => $id, 'spot_id' => $this->spotId, 'status' => 0])->asArray()->all();
        if (!empty($rows)) {
            $totalPrice[] = 0;
            foreach ($rows as $v) {
                $totalPrice[] = $v['unit_price'] * intval($v['num']);
            }
            $price = Common::num(array_sum($totalPrice));
        } else {
            $this->result['errorCode'] = 1007; //已支付
            $this->result['msg'] = '该订单已支付';
            return $this->result;
        }
        if ($discountType == 2) {//金额扣减
            if ($discountPrice < 0) {
                $this->result['errorCode'] = 1006;
                $this->result['msg'] = '优惠金额不能小于0';
                return $this->result;
            }
            $price = $price - $discountPrice;
        } else if ($discountType == 3) {//折扣
            if ($discountPrice > 100) {
                $this->result['errorCode'] = 1005;
                $this->result['msg'] = '优惠折扣不能大于100%';
                return $this->result;
            }
            if ($discountPrice < 0) {
                $this->result['errorCode'] = 1004;
                $this->result['msg'] = '优惠折扣不能小于0';
                return $this->result;
            }
            $price = $price * $discountPrice / 100;
        }
        if ($price < 0) {
            $this->result['errorCode'] = 1003;
            $this->result['msg'] = '优惠金额不能大于实际应付金额';
            return $this->result;
        }
        $price = Common::num($price);
        $this->result['aliPayUrl'] = '';
        $this->result['wechatUrl'] = '';
        //生成支付订单
        $outTradeNo = Order::generateOutTradeNo(); //订单号
        $subject = Html::encode(Yii::$app->cache->get(Yii::getAlias('@spotName') . $this->spotId . $this->userInfo->id)) . '医疗费用-' . Html::encode($username);
        //将未支付订单更改状态为过期
        Order::updateAll(['status' => 5], ['record_id' => $id, 'spot_id' => $this->spotId, 'status' => [1, 3]]);
        Order::addOrder($id, Yii::$app->user->identity->id, $outTradeNo, $subject, $price, 0, $discountType, $discountPrice, $discountReason);
        if ($price != 0) {
            $paymentConfig = PaymentConfig::getPaymentConfigList();
            if (!empty($paymentConfig)) {//若支付配置有设置
                if (isset($paymentConfig[2])) {//若设置了支付宝配置，则生成支付宝订单二维码
                    $qrCode = Order::generateQrCode($outTradeNo, $subject, $price, $paymentConfig[2], $rows, $info);
                    Yii::$app->log->logger->log($qrCode, Logger::LEVEL_INFO);
                    if ($qrCode['errorCode'] == 0 && isset($qrCode['codeUrl'])) {
                        $this->result['aliPayUrl'] = $qrCode['codeUrl'];
                    }
                }
                if (isset($paymentConfig[1])) {//微信支付
                    $qrCode = Order::generateWechatQrCode($outTradeNo, $subject, $price, $id, $pks, $patientId);
                    Yii::$app->log->logger->log($qrCode, Logger::LEVEL_INFO);
                    if ($qrCode) {
                        $this->result['wechatUrl'] = $qrCode;
                    }
                }
            }
        }
        $this->result['outTradeNo'] = $outTradeNo;
        $this->result['price'] = $price;
        return $this->result;
    }

    /**
     * print-info
     * @param int $id 收费记录id
     * @param string $pks 收费详情表id
     * @param string $outTradeNo 订单号
     *
     * @return int errorCode 错误代码(1009-参数错误,404-页面不存在)
     * @return string msg 错误提示信息
     * @return array chargeInfo 订单详情记录
     * @return array discount 订单优惠信息
     * @return array chargeType 订单每项的总金额
     * @return array spotInfo 诊所基本信息
     * @return array userInfo 患者基本信息
     * @return string doctorName 接诊医生
     * @desc 返回该收费记录的打印详情信息
     */
    public function actionPrintInfo() {
        $id = Yii::$app->request->post('id');
        $pks = Yii::$app->request->post('pks');
        $outTradeNo = Yii::$app->request->post('outTradeNo');
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$id || !$pks || !$outTradeNo) {
            $this->result['errorCode'] = 1009; //参数错误
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $pks = explode(',', $pks);
        $chargeQuery = new Query();
        $chargeQuery->from(['a' => ChargeInfo::tableName()]);
        $chargeQuery->select(['a.id', 'a.record_id', 'a.type', 'a.outpatient_id', 'a.name', 'a.unit', 'a.unit_price', 'a.num', 'a.doctor_id', 'a.discount_price', 'a.card_discount_price', 'b.username', 'pay_type' => 'c.type', 'c.discount_type']);
        $chargeQuery->leftJoin(['b' => User::tableName()], '{{a}}.doctor_id = {{b}}.id');
        $chargeQuery->leftJoin(['c' => Order::tableName()], '{{a}}.record_id = {{c}}.record_id');
        $chargeQuery->where(['a.id' => $pks, 'a.spot_id' => $this->spotId, 'a.record_id' => $id, 'a.status' => 0, 'c.out_trade_no' => $outTradeNo, 'c.status' => 1]);
        $chargeAll = $chargeQuery->all();
//        var_dump($chargeAll);
        $chargeType = [];
        $chargeId = '';
        $total = 0;
        $chargeTotalDiscount = 0;
        $cardTotalDiscount = 0;
        $materiaTotal = 0;
        $materiaDiscount = 0;
        $materiaItem = false;
        $otherItem = false;
        $recipeState = ChargeInfo::recipeStateData($chargeAll);
        if ($chargeAll) {
            foreach ($chargeAll as $key => &$v) {
                $total += $v['unit_price'] * $v['num']; //单价*数量=总价
                $chargeTotalDiscount += $v['discount_price']; //非会员的打折费用
                $cardTotalDiscount += $v['card_discount_price']; //会员的打折费用
                $chargeType[$v['type']][] = ($v['unit_price'] * $v['num']) - ($v['discount_price'] + $v['card_discount_price']); //各类医嘱的已收费的总费用
                $chargeAll[$key]['total'] = Common::num($v['unit_price'] * $v['num']); //单项总价
                $chargeAll[$key]['discount_price_unit_total'] = $v['discount_price'] + $v['card_discount_price']; //单项总优惠金额 = 非会员优惠+会员优惠
                $chargeAll[$key]['rest'] = Common::num($chargeAll[$key]['total'] - $chargeAll[$key]['discount_price_unit_total']);  //两种方式打折后 实际应付价格
                if ($v['type'] == ChargeInfo::$recipeType) {
                    if (isset($recipeState[$v['outpatient_id']]) && $recipeState[$v['outpatient_id']]['status'] == 5) {
                        $v['name'] = $v['name'] . ' (已退药)';
                    }
                    $chargeAll[$key]['unit'] = RecipeList::$getUnit[$v['unit']];
                }
                if ('0' == $chargeAll[$key]['discount_price_unit_total']) {
                    $chargeAll[$key]['discount_price_unit_total'] = '--';
                }
                if ($v['type'] == ChargeInfo::$materialType) {
                    $materiaTotal += $v['unit_price'] * $v['num'];
                    $materiaDiscount += $v['discount_price'] + $v['card_discount_price'];
                    $materiaItem = true;
                } else {
                    $ortherItem = true;
                }
            }
        } else {
            $this->result['errorCode'] = 404; //参数错误
            $this->result['msg'] = '你所请求的页面不存在';
            return $this->result;
        }
        $this->result['printWay'] = 0;
        if ($materiaItem && $ortherItem) {
            $this->result['printWay'] = 1; //其他项和非药品项都存在，打印两页
        }
        $oneDiscount = '';
        $secondDiscount = '';
        $receipt_amount = 0; //实收金额
        $discount = ChargeRecord::getDiscount($total, $chargeAll[0]['discount_type'], $chargeAll[0]['discount_price'], $chargeTotalDiscount, $cardTotalDiscount);
        $userInfo = Patient::getUserInfo($chargeAll[0]['record_id']);
        $userInfo['sex'] = Patient::$getSex[$userInfo['sex']];
        $userInfo['birth'] = date("Y-m-d", $userInfo['birthday']); //格式化出生日期
        $userInfo['birthday'] = Patient::dateDiffage($userInfo['birthday'], time());
        $userInfo['update_time'] = date('Y-m-d H:i:s', $userInfo['update_time']);
        $this->result['discount'] = $discount; //优惠金额
        $this->result['chargeInfo'] = $chargeAll;
        $this->result['chargeType']['total'] = Common::num($total); //所有项目的总价
        $this->result['chargeType']['totalDiscount'] = ($chargeTotalDiscount + $cardTotalDiscount) ? Common::num($chargeTotalDiscount + $cardTotalDiscount) : Common::num(0); //所有项目总优惠金额
        $this->result['chargeType']['totalRest'] = ($total - $chargeTotalDiscount - $cardTotalDiscount) ? Common::num($total - $chargeTotalDiscount - $cardTotalDiscount) : Common::num(0);
        $this->result['chargeType']['checkType'] = isset($chargeType[ChargeInfo::$checkType]) ? Common::num(array_sum($chargeType[ChargeInfo::$checkType]) - $chargeType[ChargeInfo::$checkType]['discount']) : '';
        $this->result['chargeType']['cureType'] = isset($chargeType[ChargeInfo::$cureType]) ? Common::num(array_sum($chargeType[ChargeInfo::$cureType]) - $chargeType[ChargeInfo::$cureType]['discount']) : '';
        $this->result['chargeType']['inspectType'] = isset($chargeType[ChargeInfo::$inspectType]) ? Common::num(array_sum($chargeType[ChargeInfo::$inspectType]) - $chargeType[ChargeInfo::$inspectType]['discount']) : '';
        $this->result['chargeType']['recipeType'] = isset($chargeType[ChargeInfo::$recipeType]) ? Common::num(array_sum($chargeType[ChargeInfo::$recipeType]) - $chargeType[ChargeInfo::$recipeType]['discount']) : '';
        $this->result['chargeType']['priceType'] = isset($chargeType[ChargeInfo::$priceType]) ? Common::num(array_sum($chargeType[ChargeInfo::$priceType]) - $chargeType[ChargeInfo::$recipeType]['discount']) : '';
        $this->result['chargeType']['materialType'] = isset($chargeType[ChargeInfo::$materialType]) ? Common::num(array_sum($chargeType[ChargeInfo::$materialType]) - $chargeType[ChargeInfo::$materialType]['discount']) : '';
        if ($this->result['printWay']) {
            $this->result['chargeType']['otherTotal'] = Common::num($total - $materiaTotal); //其他项应收
            $this->result['chargeType']['otherDiscount'] = Common::num($chargeTotalDiscount + $cardTotalDiscount - $materiaDiscount); //其他项优惠
            $this->result['chargeType']['otherRest'] = Common::num($this->result['chargeType']['otherTotal'] - $this->result['chargeType']['otherDiscount']); //其他项实收
            $this->result['chargeType']['materialTotal'] = Common::num($materiaTotal); //非药品项应收
            $this->result['chargeType']['materialDiscount'] = Common::num($materiaDiscount); //非药品项优惠
            $this->result['chargeType']['materialRest'] = Common::num($this->result['chargeType']['materialTotal'] - $this->result['chargeType']['materialDiscount']); //非药品实收
        }
        $this->result['typeList'] = [
            'material' => ChargeInfo::$materialType
        ];
        $this->result['spotInfo'] = Spot::getSpot();
        $this->result['userInfo'] = $userInfo;
        $this->result['doctorName'] = $chargeAll[0]['username'] ? $chargeAll[0]['username'] : '--';
        return $this->result;
    }

    /**
     * create-discount
     * @param integer $id 就诊流水id
     * @return int errorCode 错误代码(0-保存成功,1002-折扣不能大于100%,1003-优惠金额不能小于0,1004-折扣只能精确到小数点后两位,1005-优惠金额只能精确到小数点后两位,1006-该待收费项目不存在,1007-优惠金额不能大于总金额,1008-折扣与优惠金额不等价,1009-优惠原因字数不能大于10个字符)
     * @desc 待收费-新增单项折扣信息
     */
    public function actionCreateDiscount($id) {
        if (Yii::$app->request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            if ($request->isPost) {
                $params = $request->post();
                if (!empty($params['discount'])) {
                    $discountPrice = $params['discount_price'];
                    $discountReason = $params['discount_reason'];
                    $dbTrans = Yii::$app->db->beginTransaction();
                    try {
                        foreach ($params['discount'] as $key => $v) {
                            if ($v == 100 && $discountPrice[$key] == '') {
                                $discountPrice[$key] = 0;
                            }
                            if ($v == '' && $discountPrice[$key] == 0.00) {
                                $v = 100;
                            }
                            if ($v == '' && $discountPrice[$key] != 0.00) {
                                $this->result['errorCode'] = 1002; //折扣错误，大于100
                                $this->result['msg'] = '折扣不能为空';
                                $dbTrans->rollBack();
                                return $this->result;
                            }
                            if ($v > 100) {
                                $this->result['errorCode'] = 1002; //折扣错误，大于100
                                $this->result['msg'] = '折扣不能大于100%';
                                $dbTrans->rollBack();
                                return $this->result;
                            }
                            if ($v < 0) {
                                $this->result['errorCode'] = 1001; //折扣错误，大于100
                                $this->result['msg'] = '折扣不能小于0';
                                $dbTrans->rollBack();
                                return $this->result;
                            }
                            if ($discountPrice[$key] < 0) {
                                $this->result['errorCode'] = 1003; //优惠金额不能小于0
                                $this->result['msg'] = '优惠金额不能小于0';
                                $dbTrans->rollBack();
                                return $this->result;
                            }
//                            if ((!preg_match('/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', $v)) && $discountPrice[$key] != 0.01) {
                            if ((!preg_match('/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', $v))) {
                                $this->result['errorCode'] = 1004;
                                $this->result['msg'] = '折扣只能精确到小数点后两位';
                                return $this->result;
                            }
//                            if ((!preg_match('/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', $discountPrice[$key])) && $v != 100.00) {
                            if ((!preg_match('/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', $discountPrice[$key]))) {
                                $this->result['errorCode'] = 1005;
                                $this->result['msg'] = '优惠金额只能精确到小数点后两位';
                                return $this->result;
                            }
                            $chargePrice = ChargeInfo::find()->select(['unit_price', 'num'])->where(['id' => $params['chargeInfoId'][$key], 'spot_id' => $this->spotId, 'status' => 0, 'record_id' => $id])->asArray()->one();
                            if (empty($chargePrice)) {
                                $this->result['errorCode'] = 1006; //
                                $this->result['msg'] = '该待收费项目不存在';
                                $dbTrans->rollBack();
                                return $this->result;
                            }
                            $totalPrice = Common::num($chargePrice['unit_price'] * $chargePrice['num']);
                            if (Common::num($discountPrice[$key]) > $totalPrice) {
                                $this->result['errorCode'] = 1007;
                                $this->result['nowTotalPrice'] = Common::num($discountPrice[$key]);
                                $this->result['aldTotalPrice'] = $totalPrice;
                                $this->result['msg'] = '优惠金额不能大于总金额';
                                $dbTrans->rollBack();
                                return $this->result;
                            }
                            $comparePrice = Common::num($totalPrice * ($v / 100)) - Common::num($totalPrice - $discountPrice[$key]);
                            if (abs($comparePrice > 0.1)) {
                                $this->result['errorCode'] = 1008;
                                $this->result['msg'] = '折扣与优惠金额不等价';
                                $dbTrans->rollBack();
                                return $this->result;
                            }
//                             if(strlen($discountReason[$key]) > 20){
//                                 $this->result['errorCode'] = 1009;
//                                 $this->result['msg'] = '优惠原因字数不能大于20个字符';
//                                 $dbTrans->rollBack();
//                                 return $this->result;
//                             }
                            ChargeInfo::updateAll(['discount' => $v, 'discount_price' => $discountPrice[$key], 'discount_reason' => $discountReason[$key]], ['id' => $params['chargeInfoId'][$key], 'spot_id' => $this->spotId]);
                        }
                        //将未支付订单更改状态为过期
                        Order::updateAll(['status' => 5], ['record_id' => $id, 'spot_id' => $this->spotId, 'status' => [1, 3]]);
                        $dbTrans->commit();
                        return $this->result;
                    } catch (Exception $e) {
                        $dbTrans->rollBack();
                        $this->result['errorCode'] = '500';
                        $this->result['msg'] = json_encode($e->errorInfo);
                        return $this->result;
                    }
                }
            } else {
                $query = ChargeInfo::find()->select(['id', 'name', 'unit_price', 'num', 'discount', 'discount_price', 'discount_reason', 'is_charge_again'])->where(['record_id' => $id, 'spot_id' => $this->spotId, 'status' => 0]);
                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => false,
                    'sort' => [
                        'attributes' => ['']
                    ]
                ]);
                return [
                    'title' => '折扣优惠',
                    'content' => $this->renderAjax('@chargeCreateDiscountView', [
                        'dataProvider' => $dataProvider,
                    ]),
                ];
            }
        } else {
            throw new NotAcceptableHttpException('非法请求');
        }
    }

    /**
     * get-card-discount-price
     * @param string pks 收费详情记录id
     * @param integer id 卡种id
     * @param integer originalPrice 是否打折，1-不打折，0-打折。默认打折
     * @desc 计算卡种相关标签折扣的优惠金额
     */
    public function actionGetCardDiscountPrice() {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isAjax) {
            $pks = $request->post('pks', ''); //收费详情记录id
            $id = $request->post('id'); //卡id
            $iphone = $request->post('iphone'); //手机号
            $patientId = $request->post('patientId'); //手机号
            $originalPrice = $request->post('originalPrice', 0); //是否打折
            $firstDiagnosisFree = $request->post('firstDiagnosisFree', 2); //默认不免金额
            if (!$pks || !$id || !$iphone || !$patientId) {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '参数错误';
                return $this->result;
            }
            $pks = explode(',', $pks);
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
            $chargeInfoData = ChargeInfo::find()->select(['id', 'outpatient_id', 'type', 'unit_price', 'num', 'discount_price', 'tag_id'])->where(['id' => $pks, 'spot_id' => $this->spotId])->asArray()->all();
            $hasFirstFree = false;
            if ($firstDiagnosisFree == 1) {
                $hasFirstFree = FirstOrderFree::check($patientId, $iphone);
            }
            foreach ($chargeInfoData as $key => $v) {
                $price = Common::num((($v['unit_price'] * $v['num']) - $v['discount_price']));
                $chargeInfoArray[$v['id']] = 0;
                if ($originalPrice == 1 && $v['type'] != ChargeInfo::$priceType) {
                    $totalPrice[] = $price;
                    continue;
                }
                if ($v['tag_id'] && isset($cardDiscountInfo[$v['tag_id']])) {//若匹配上折扣
                    $oncePrice = Common::num($price * ($cardDiscountInfo[$v['tag_id']]['discount'] / 100));
                    $totalPrice[] = $oncePrice;
                    $chargeInfoArray[$v['id']] = $price - $oncePrice;
                    Yii::info('oncePrice:' . $oncePrice);
                    Yii::info('price:' . $price);
                    Yii::info($v['id'] . ':' . $chargeInfoArray[$v['id']]);
                } else if ($v['type'] == ChargeInfo::$priceType) {
                    if ($originalPrice == 1) {//不使用折扣
                        if ($firstDiagnosisFree == 1 && $hasFirstFree) {//首单免诊金
                            $oncePrice = 0;
                            $totalPrice[] = $oncePrice;
                            $chargeInfoArray[$v['id']] =  Common::num($price - $oncePrice);
                        } else {
                            $totalPrice[] = $price;
                        }
                    } else {
                        $feeDiscount = '';
                        $feeDiscount = isset($cardDiscountInfo[0]) ? $cardDiscountInfo[0]['discount'] : 100; //诊金折扣
                        if ($firstDiagnosisFree == 1 && $hasFirstFree) {//首单免诊金
                            $feeDiscount = 0;
                        }
                        if ($feeDiscount !== '') {
                            $oncePrice = Common::num($price * ($feeDiscount / 100));
                            $totalPrice[] = $oncePrice;
                            $chargeInfoArray[$v['id']] = Common::num($price - $oncePrice);
                            Yii::info('oncePrice:' . $oncePrice);
                            Yii::info('price:' . $price);
                            Yii::info($v['id'] . ':' . $chargeInfoArray[$v['id']]);
                        }
                    }
                } else {
                    $totalPrice[] = $price;
                }
            }

            $allPrice = Common::num(array_sum($totalPrice));
            $balance = CardRecharge::find()->select(['totalFee' => '(f_donation_fee+f_card_fee)'])->where(['f_phone' => $iphone, 'f_physical_id' => $id, 'f_parent_spot_id' => $this->parentSpotId])->asArray()->one();
            $this->result['allPrice'] = $allPrice;
            $this->result['totalFee'] = $balance['totalFee'];
            if ($balance['totalFee'] < $allPrice) {
                $this->result['errorCode'] = 1005;
                $this->result['msg'] = '卡内余额不足';
                return $this->result;
            }

            $this->result['price'] = Common::num(array_sum($totalPrice));//应付价格
            $this->result['chargeInfoArray'] = json_encode($chargeInfoArray);
            $this->result['discountPrice'] = Common::num(array_sum($chargeInfoArray));
            return $this->result;
        } else {
            $this->result['errorCode'] = 405;
            $this->result['msg'] = '非法请求';
            return $this->result;
        }
    }

    /**
     * generate-code
     * @param string $pks 收费清单id，已逗号隔开
     * @param int $recordId 就诊流水id
     * @param int $discountType 优惠类型(1-无，2-金额扣减，3-折扣)
     * @param int $discountPrice 优惠金额/折扣比例
     * @param string $discountReason 优惠原因
     * @param string $username 患者姓名
     * @param int $patient_id 患者id
     *
     * @return int errorCode 错误代码(0-成功,1009-参数错误,1008-最多保留两位小数，1007-订单已支付,1006-优惠金额不能小于0,1005-优惠折扣不能大于100%,1004-优惠折扣不能小于0,1003-优惠金额不能大于实际应付金额)
     * @return string msg 错误提示信息
     * @return string aliPayUrl 支付宝支付二维码url
     * @return string wechatUrl 微信支付二维码url
     * @return string outTradeNo 订单号
     * @return string price 打折后需支付的总金额
     *
     * @desc 收费打折时，重新生成订单
     */
    public function actionNewGenerateCode() {
        $pks = Yii::$app->request->post('pks'); //收费清单id
        $id = Yii::$app->request->post('recordId'); //就诊流水id
        $username = Yii::$app->request->post('username'); //患者姓名
        $patientId = Yii::$app->request->post('patient_id');
        $outTradeNo = Yii::$app->request->post('outTradeNo'); //订单号
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$pks || !$id || !$username || !$patientId || !$outTradeNo) {
            $this->result['errorCode'] = 1009; //参数错误
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $info = [
            'username' => $username,
            'patient_id' => $patientId
        ];
        $pks = explode(',', $pks); // Array or selected records primary keys
        $orderInfo = Order::find()->select(['id', 'status', 'total_amount'])->where(['out_trade_no' => $outTradeNo, 'spot_id' => $this->spotId])->asArray()->one();
        if (!empty($orderInfo)) {
            if ($orderInfo['status'] == 2) {
                $this->result['errorCode'] = 1005;
                $this->result['msg'] = '该订单已支付';
                return $this->result;
            } else if ($orderInfo['status'] == 5) {
                $this->result['errorCode'] = 1006;
                $this->result['msg'] = '该订单已过期';
                return $this->result;
            }
        } else {
            $this->result['errorCode'] = 1007;
            $this->result['msg'] = '该订单不存在';
            return $this->result;
        }
        $this->result['aliPayUrl'] = '';
        $this->result['wechatUrl'] = '';
        $rows = ChargeInfo::find()->select(['id', 'name'])->where(['id' => $pks, 'record_id' => $id, 'spot_id' => $this->spotId, 'status' => 0])->asArray()->all();
        //生成支付订单
        $subject = Html::encode(Yii::$app->cache->get(Yii::getAlias('@spotName') . $this->spotId . $this->userInfo->id)) . '医疗费用-' . Html::encode($username);
        $paymentConfig = PaymentConfig::getPaymentConfigList();
        if (!empty($paymentConfig)) {//若支付配置有设置
            if (isset($paymentConfig[2])) {//若设置了支付宝配置，则生成支付宝订单二维码
                $qrCode = Order::generateQrCode($outTradeNo, $subject, $orderInfo['total_amount'], $paymentConfig[2], $rows, $info);
                Yii::$app->log->logger->log($qrCode, Logger::LEVEL_INFO);
                if ($qrCode['errorCode'] == 0 && isset($qrCode['codeUrl'])) {
                    $this->result['aliPayUrl'] = $qrCode['codeUrl'];
                }
            }
            if (isset($paymentConfig[1])) {//微信支付
                $qrCode = Order::generateWechatQrCode($outTradeNo, $subject, $orderInfo['total_amount'], $id, $pks, $patientId);
                Yii::$app->log->logger->log($qrCode, Logger::LEVEL_INFO);
                if ($qrCode) {
                    $this->result['wechatUrl'] = $qrCode;
                }
            }
        }
        return $this->result;
    }

    public function actionUpdateMaterial($id) {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isAjax) {
            $model = new MultiModel([
                'models' => [
                    'materialModel' => new MaterialCharge()
                ]
            ]);
            if ($request->isPost) {
                if ($model->load($request->post()) && $model->validate()) {
                    $dbTrans = Yii::$app->db->beginTransaction();
                    try {
                        $rows = [];
                        $this->result['msg'] = '保存成功';
                        $materialModel = $model->getModel('materialModel');
                        if (isset($materialModel->stockId)) {

                            if (count($materialModel->stockId) > 0) {
                                $idList = Material::getList(['id', 'name', 'unit', 'price', 'specification', 'tag_id'], ['id' => $materialModel->stockId]);
                                $chargeRecordId = ChargeRecord::find()->select(['id'])->where(['spot_id' => $this->spotId, 'record_id' => $id, 'status' => 2])->asArray()->one();
                                $doctorId = ChargeInfo::find()->select(['doctor_id'])->where(['record_id' => $id])->asArray()->one();
                                foreach ($materialModel->stockId as $key => $v) {
                                    //过滤不需要操作的删除的项目
                                    if ($materialModel->deleted[$key] == 1 && $materialModel->chargeInfoId[$key] == '') {
                                        continue;
                                    }
                                    //若是已存在的记录，并且deleted == 1 则删除
                                    if ($materialModel->deleted[$key] == 1 && $materialModel->chargeInfoId[$key] != '') {
                                        ChargeInfo::deleteAll(['id' => $materialModel->chargeInfoId[$key], 'spot_id' => $this->spotId, 'record_id' => $id]);
                                    } else if ($materialModel->deleted[$key] != 1 && $materialModel->chargeInfoId[$key] != '') {
                                        //若是已存在的记录，并且deleted != 1 则修改
                                        ChargeInfo::updateAll(['num' => $materialModel->num[$key], 'remark' => $materialModel->remark[$key], 'discount' => 100, 'discount_price' => 0, 'discount_reason' => ''], ['id' => $materialModel->chargeInfoId[$key], 'spot_id' => $this->spotId, 'record_id' => $id]);
                                    } else if ($materialModel->deleted[$key] != 1 && $materialModel->chargeInfoId[$key] == '') {//新增收费项目
                                        $rows[] = [
                                            $this->spotId,
                                            $id,
                                            $chargeRecordId['id'],
                                            ChargeInfo::$materialType,
                                            $v,
                                            $idList[$v]['name'],
                                            $idList[$v]['specification'],
                                            $idList[$v]['unit'],
                                            $idList[$v]['price'],
                                            $idList[$v]['tag_id'],
                                            $materialModel->num[$key],
                                            $materialModel->remark[$key],
                                            2,
                                            $doctorId['doctor_id'],
                                            time(),
                                            time()
                                        ];
                                    }
                                }
                            }
                            if (count($rows) > 0) {//若有新增收费记录
                                Yii::$app->db->createCommand()->batchInsert(ChargeInfo::tableName(), ['spot_id', 'record_id', 'charge_record_id', 'type', 'outpatient_id', 'name', 'specification', 'unit', 'unit_price', 'tag_id', 'num', 'remark', 'origin', 'doctor_id', 'create_time', 'update_time'], $rows)->execute();
                            } else {//若没有新增收费项目,查找是否还有待收费项目，若没有。则将对应的流水记录和收费记录一次性删除
                                $count = ChargeInfo::find()->where(['spot_id' => $this->spotId, 'record_id' => $id, 'charge_record_id' => $chargeRecordId['id'], 'status' => 0])->count(1);
                                if ($count == 0) {
                                    $aa = ChargeRecord::deleteAll(['spot_id' => $this->spotId, 'record_id' => $id, 'id' => $chargeRecordId['id'], 'status' => 2]);
                                    $bb = PatientRecord::deleteAll(['spot_id' => $this->spotId, 'id' => $id]);
                                    $this->result['msg'] = '保存成功，并已删除此待收费记录';
                                    $this->result['redirectUrl'] = Url::to(['@chargeIndexIndex']);
                                }
                            }
                            $ret = MaterialStockDeductionRecord::updateStockInfo($id, MaterialStockDeductionRecord::$chargeInfo);
                            if ($ret['errorCode']) {
                                $dbTrans->rollBack();
                                $this->result['errorCode'] = 1014;
                                $this->result['msg'] = $ret['message'];
                                return $this->result;
                            }
                        } else {
                            $this->result['errorCode'] = 1001;
                            $this->result['msg'] = '请选择收费项';
                            return $this->result;
                        }
                        $this->result['errorCode'] = 0;
                        $dbTrans->commit();
                        return $this->result;
                    } catch (Exception $e) {
                        $dbTrans->rollBack();
                    }
                } else {
                    $this->result['errorCode'] = 1001;
                    $this->result['msg'] = $model->getModel('materialModel')->errors['num'][0];
                    return $this->result;
                }
            } else {
                $query = new ActiveQuery(MaterialCharge::className());
                $query->from(['a' => ChargeInfo::tableName()]);

                $query->select(['a.id', 'a.name', 'a.num', 'a.unit', 'price' => 'a.unit_price', 'a.remark', 'a.specification', 'stockId' => 'b.id', 'b.manufactor', 'b.attribute']);
                $query->leftJoin(['b' => Material::tableName()], '{{a}}.outpatient_id = {{b}}.id');
                $query->where(['a.record_id' => $id, 'a.spot_id' => $this->spotId, 'a.status' => 0, 'a.type' => ChargeInfo::$materialType, 'a.origin' => 2]);
                $query->orderBy(['id' => SORT_ASC]);
                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                    'pagination' => false,
                    'sort' => [
                        'attributes' => ['']
                    ]
                ]);

                $list = Material::getList(['id', 'meta', 'name', 'specification', 'unit', 'price', 'remark', 'manufactor', 'attribute'], ['status' => 1]);
                $materialTotal = MaterialStockInfo::getTotal();

                return [
                    'title' => '修改其他收费项',
                    'content' => $this->renderAjax('@chargeUpdateMaterialView', [
                        'dataProvider' => $dataProvider,
                        'model' => $model,
                        'list' => $list,
                        'materialTotal' => $materialTotal
                    ]),
                ];
            }
        } else {
            throw new BadRequestHttpException();
        }
    }

    /**
     * check-material-record-num
     * @param string $selectedIds 收费清单id，已逗号隔开
     * @param integer $type 渠道(1-医生门诊，2-直接收费)
     * @return int errorCode 错误代码(0-成功,1001-参数错误,1002-数量不能大于总库存量)
     * @return string msg 错误提示信息
     *
     * @desc 验证待收费列表的非药品清单是否满足条件，即非药品数量不能大于其总库存量
     */
    public function actionCheckMaterialRecordNum() {
        $selectedIds = Yii::$app->request->post('selectedIds');
        $type = Yii::$app->request->post('updateMaterialButtonType');
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$selectedIds || !$type || !in_array($type, [1, 2])) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
        }
        //判断逻辑在医生门诊做
        return ['errorCode' => 0];
//        if ($type == 1) {
//            return ChargeInfo::checkMaterialINum($selectedIds);
//        }
//        if ($type == 2) {
//            return ChargeInfo::checkMaterialINumSecond($selectedIds);
//        }
    }

    /**
     * charge-record-log
     * @return int errorCode 错误代码(0-成功,405-非法请求，1009-参数错误,)
     * @return string msg 错误提示信息
     * @return array chargeInfoLogList 交易流水具体项目信息
     * @return array chargeRecordLogList 交易流水相关信息
     * @return array spotInfo 当前诊所信息
     * @return array price 打折后需支付的总金额
     *
     * @desc 交易流水各项信息接口
     */
    public function actionChargeRecordLog() {
        if (Yii::$app->request->isAjax) {
            $id = $id = Yii::$app->request->post('log_id'); //交易流水id
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!$id) {
                $this->result['errorCode'] = 1009;
                $this->result['msg'] = '参数错误';
                return $this->result;
            }
            $chargeQuery = new Query();
            $chargeQuery->from(['a' => ChargeRecordLog::tableName()]);
            $chargeQuery->select(['b.charge_record_log_id', 'b.id', 'b.record_id', 'b.name', 'b.is_charge_again', 'b.unit', 'b.unit_price', 'b.num', 'b.discount_reason', 'b.discount_price', 'b.card_discount_price', 'b.card_discount_price', 'b.type as chargeType', 'a.type']);
            $chargeQuery->leftJoin(['b' => ChargeInfoLog::tableName()], '{{b}}.charge_record_log_id = {{a}}.id');
            $chargeQuery->where(['b.spot_id' => self::$staticSpotId, 'b.charge_record_log_id' => $id]);
            $chargeInfoLogList = $chargeQuery->all();
            $chargeRecordLogList = ChargeRecordLog::getChargeLogList($id);
            $this->result['printWay'] = 1;
            if ($chargeRecordLogList['material_price'] && ($chargeRecordLogList['check_price'] != null || $chargeRecordLogList['cure_price'] != null || $chargeRecordLogList['inspect_price'] != null || $chargeRecordLogList['recipe_price'] != null || $chargeRecordLogList['diagnosis_price'] != null || $chargeRecordLogList['package_price'] != null)) {
                $this->result['printWay'] = 2; //混合打印
                if ($chargeRecordLogList['type'] == 1) {//收费
                    $chargeRecordLogList['otherPrice'] = Common::num($chargeRecordLogList['order_price'] - $chargeRecordLogList['material_price'] - $chargeRecordLogList['material_discount_price']); //四大医嘱总应收金额
                    $chargeRecordLogList['otherDiscount'] = Common::num($chargeRecordLogList['discount_price'] - $chargeRecordLogList['material_discount_price']); //四大医嘱总优惠金额
                    if ($chargeRecordLogList['pay_type'] == 1) {//现金收费 实收按比例
                        $chargeRecordLogList['otherIncome'] = Common::num($chargeRecordLogList['income'] * (1 - $chargeRecordLogList['material_price'] / $chargeRecordLogList['price'])); //四大医嘱实收金额
                        $chargeRecordLogList['otherChange'] = Common::num($chargeRecordLogList['otherIncome'] - $chargeRecordLogList['otherPrice'] + $chargeRecordLogList['otherDiscount']); //四大医嘱找零
                        $chargeRecordLogList['materialIncome'] = Common::num($chargeRecordLogList['income'] - $chargeRecordLogList['otherIncome']); //非药品实收金额
                        $chargeRecordLogList['materialChange'] = Common::num($chargeRecordLogList['materialIncome'] - $chargeRecordLogList['material_price']); //非药品找零
                    } else {//非现金收费  实收
                        $chargeRecordLogList['otherIncome'] = Common::num($chargeRecordLogList['price'] - $chargeRecordLogList['material_price']);
                        $chargeRecordLogList['materialIncome'] = Common::num($chargeRecordLogList['material_price']);
                    }
                    $chargeRecordLogList['materialPrice'] = Common::num($chargeRecordLogList['material_price'] + $chargeRecordLogList['material_discount_price']); //非药品应收金额
                } else {//退费
                    $chargeRecordLogList['refund_price'] = $chargeRecordLogList['refund_price'] - $chargeRecordLogList['material_price'];
                }
            }
            $chargeRecordLogList['sex'] = Patient::$getSex[$chargeRecordLogList['sex']];
            $chargeRecordLogList['birthday'] = date("Y-m-d", $chargeRecordLogList['birthday']);
            $chargeRecordLogList['pay_type'] = ChargeRecord::$getType[$chargeRecordLogList['pay_type']];
            $chargeRecordLogList['order_price'] = Common::num($chargeRecordLogList['order_price']);
            $chargeRecordLogList['create_time'] = date('Y-m-d', $chargeRecordLogList['create_time']);
            $chargeRecordLogList['doctor_name'] = Html::encode($chargeRecordLogList['doctor_name']);
            foreach ($chargeInfoLogList as $key => &$value) {
                $value['total_price'] = Common::num(abs($value['unit_price'] * $value['num'] - $value['discount_price'] - $value['card_discount_price']));
            }
            $spotInfo = Spot::find()->select(['spot_name', 'spot', 'status', 'province', 'city', 'area', 'telephone', 'icon_url'])->where(['id' => $this->spotId])->asArray()->one();
            $this->result['chargeInfoLogList'] = $chargeInfoLogList;
            $this->result['chargeRecordLogList'] = $chargeRecordLogList;
            $this->result['spotInfo'] = $spotInfo;
            return $this->result;
        } else {
            $this->result['errorCode'] = 405;
            $this->result['msg'] = '非法请求';
            return $this->result;
        }
    }

    /**
     * charge-print
     * @param int $id 流水id
     * @return string title 标题
     * @return array content 内容
     * @throws NotFoundHttpException
     * @desc 收费打印弹窗页面
     */
    public function actionChargePrintList($id) {
        if (Yii::$app->request->isAjax) {
            $chargeRrcordLog = ChargeRecordLog::findOne(['id' => $id]);
            if ($chargeRrcordLog->type == 1) {
                $chargePrintList = [
                    ['id' => '1', 'name' => '总收费清单（包含收费明细和收费汇总）'],
                    ['id' => '2', 'name' => '收费明细清单（可用于报销）']
                ];
            } else {
                $chargePrintList = [
                    ['id' => '1', 'name' => '总退费清单（包含退费明细和退费汇总）'],
                    ['id' => '2', 'name' => '退费明细清单（可用于报销）']
                ];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "请选择打印项",
                'content' => $this->renderAjax('@chargePrintListView', [
                    'printList' => $chargePrintList,
                    'logId' => $id,
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                Html::button('确定', ['class' => 'btn btn-default btn-form btn-charge-list-print ', 'data-dismiss' => "modal",'name' => 'charge-list-print' . $id . 'myshow'])
            ];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * charge-record-log
     * @return int errorCode 错误代码(0-成功,405-非法请求，1009-参数错误,)
     * @return string msg 错误提示信息
     * @return array chargeInfoLogList 交易流水具体项目信息
     * @return array chargeRecordLogList 交易流水相关信息
     * @return array spotInfo 当前诊所信息
     * @return array price 打折后需支付的总金额
     *
     * @desc 交易流水各项信息接口
     */
    public function actionChargeLogPrintData() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('logId'); //交易流水id
            $selectId = Yii::$app->request->post('selectId'); //交易流水id
            if (!$id) {
                $this->result['errorCode'] = 1009;
                $this->result['msg'] = '参数错误';
                return $this->result;
            }
            $chargeQuery = new Query();
            $chargeQuery->from(['a' => ChargeRecordLog::tableName()]);
            $chargeQuery->select(['b.charge_record_log_id', 'b.outpatient_id', 'b.id', 'b.record_id', 'b.name', 'b.is_charge_again', 'b.unit', 'b.unit_price', 'b.num', 'b.discount_reason', 'b.discount_price', 'b.card_discount_price', 'b.card_discount_price', 'b.type as chargeType', 'a.type']);
            $chargeQuery->leftJoin(['b' => ChargeInfoLog::tableName()], '{{b}}.charge_record_log_id = {{a}}.id');
            $chargeQuery->where(['b.spot_id' => self::$staticSpotId, 'b.charge_record_log_id' => $id]);
            $chargeQuery->orderBy(['chargeType' => SORT_ASC]);
            $chargeInfoLogList = $chargeQuery->all();

            $chargeRecordLog = ChargeRecordLog::getChargeLogList($id);
            $chargeRecordLog['sex'] = Patient::$getSex[$chargeRecordLog['sex']];
            $chargeRecordLog['birthday'] = date("Y-m-d", $chargeRecordLog['birthday']);
            $chargeRecordLog['pay_type'] = ChargeRecord::$getType[$chargeRecordLog['pay_type']];
            $chargeRecordLog['order_price'] = Common::num($chargeRecordLog['order_price']);
            $chargeRecordLog['create_time'] = date('Y-m-d', $chargeRecordLog['create_time']);
            $chargeRecordLog['doctor_name'] = Html::encode($chargeRecordLog['doctor_name']);
            $ordersList = [];
            $materialList = [];
            $packageRecordId = [];
            foreach ($chargeInfoLogList as &$value) {
                if ($value['chargeType'] == ChargeInfo::$recipeType) {
                    $value['unit'] = RecipeList::$getUnit[$value['unit']];
                }
                $value['total_price'] = Common::num(abs(Common::num($value['unit_price'] * $value['num']) - $value['discount_price'] - $value['card_discount_price']));
                if ($value['chargeType'] == ChargeInfo::$materialType || $value['chargeType'] == ChargeInfo::$consumablesType) {
                    $materialList[] = $value;
                } else {
                    $ordersList[] = $value;
                }
                if($value['chargeType'] == ChargeInfo::$packgeType){
                    $packageRecordId[] = $value['outpatient_id'];
                }
            }
            $packageRecordText = $this->getPackageRecordPrintData($packageRecordId, $chargeRecordLog['recordId']);
            $spotInfo = Spot::getSpot($this->spotId);
            $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name','logo_shape']);
            $this->result['data']['orderLogList'] = $ordersList;
            $this->result['data']['materialList'] = $materialList;
            $this->result['data']['chargeRecordLog'] = $chargeRecordLog;
            $this->result['data']['spotInfo'] = $spotInfo;
            $this->result['data']['print'] = $selectId;
            $this->result['data']['spotConfig'] = $spotConfig;
            $this->result['data']['packageRecord'] = $packageRecordText;
            return $this->result;
        } else {
            $this->result['errorCode'] = 405;
            $this->result['msg'] = '非法请求';
            return $this->result;
        }
    }

    /**
     * charge-record-log
     * @return int errorCode 错误代码(0-成功,405-非法请求，1009-参数错误,)
     * @return string msg 错误提示信息
     * @return array chargeInfoLogList 交易流水具体项目信息
     * @return array chargeRecordLogList 交易流水相关信息
     * @return array spotInfo 当前诊所信息
     * @return array price 打折后需支付的总金额
     *
     * @desc 交易流水各项信息接口
     */
    public function actionChargePrintData() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            $pks = Yii::$app->request->post('pks');
            $outTradeNo = Yii::$app->request->post('outTradeNo');
            $cardId = Yii::$app->request->post('cardId', 0);
            $firstDiagnosisFree = Yii::$app->request->post('firstDiagnosisFree', 0);

            if (!$id || !$pks || !$outTradeNo) {
                $this->result['errorCode'] = 1009; //参数错误
                $this->result['msg'] = '参数错误';
                return $this->result;
            }

            $pks = explode(',', $pks);
            $chargeQuery = new Query();
            $chargeQuery->from(['a' => ChargeInfo::tableName()]);
            $chargeQuery->select(['a.id', 'a.record_id', 'a.type', 'a.outpatient_id', 'a.name', 'a.unit', 'a.unit_price', 'a.num', 'a.doctor_id', 'a.discount_price', 'a.card_discount_price', 'b.username', 'a.tag_id', 'pay_type' => 'c.type', 'c.discount_type']);
            $chargeQuery->leftJoin(['b' => User::tableName()], '{{a}}.doctor_id = {{b}}.id');
            $chargeQuery->leftJoin(['c' => Order::tableName()], '{{a}}.record_id = {{c}}.record_id');
//            $chargeQuery->leftJoin(['d' => PackageRecord::tableName()], '{{a}}.record_id = {{d}}.record_id');
            $chargeQuery->where(['a.id' => $pks, 'a.spot_id' => $this->spotId, 'a.record_id' => $id, 'a.status' => 0, 'c.out_trade_no' => $outTradeNo, 'c.status' => 1]);
            $chargeInfoList = $chargeQuery->all();

            $cardDiscountInfo = [];
            if ($cardId) {//选择会员卡支付
                $query = new Query();
                $query->from(['a' => CardRecharge::tableName()]);
                $query->select(['a.f_physical_id', 'b.f_medical_fee_discount', 'c.tag_id', 'c.discount']);
                $query->leftJoin(['b' => CardRechargeCategory::tableName()], '{{a}}.f_category_id = {{b}}.f_physical_id');
                $query->leftJoin(['c' => CardDiscountClinic::tableName()], '{{b}}.f_physical_id = {{c}}.recharge_category_id');
                $query->where(['a.f_physical_id' => $cardId, 'c.spot_id' => $this->spotId]);
                $query->indexBy('tag_id');
                $cardDiscountInfo = $query->all(CardRecharge::getDb());
            }

            $recipeState = ChargeInfo::recipeStateData($chargeInfoList);
            $inspectState = ChargeInfo::inspectStateData($chargeInfoList);
            $ordersList = [];
            $materialList = [];
            $chargeType = [];
            $totalPrice = $toatlDiscount = 0.00;
            $packageRecordId = null;
            foreach ($chargeInfoList as &$value) {
                if ($value['tag_id'] && isset($cardDiscountInfo[$value['tag_id']])) {//存在会员卡优惠
                    $tmpPrice = (Common::num($value['unit_price'] * $value['num'])*100 - $value['discount_price']*100)/100;
                    $value['card_discount_price'] = Common::num($tmpPrice - Common::num($tmpPrice * ($cardDiscountInfo[$value['tag_id']]['discount'] / 100))); //原价减去折后价
                } else if ($value['type'] == ChargeInfo::$priceType) {//该收费单存在诊金
                    $tmpPrice = Common::num($value['unit_price'] * $value['num']) - $value['discount_price'];
                    if ($firstDiagnosisFree) {//首单免诊金
                        $value['card_discount_price'] = $tmpPrice;
                    } else if (!empty($cardDiscountInfo)) {//会员卡优惠
//                        foreach ($cardDiscountInfo as $v) {
//                            $feeDiscount = $v['f_medical_fee_discount'] ? $v['f_medical_fee_discount'] : 100;
//                            break;
//                        }
                        $feeDiscount = isset($cardDiscountInfo[0]) ? $cardDiscountInfo[0]['discount'] : 100;
                        $value['card_discount_price'] = Common::num($tmpPrice - Common::num($tmpPrice * ($feeDiscount / 100))); //原价减去折后价
                    }
                }
                $totalPrice += $value['unit_price'] * $value['num'];
                $toatlDiscount += $value['discount_price'] + $value['card_discount_price'];
                $value['total_price'] = Common::num(abs(Common::num($value['unit_price'] * $value['num']) - $value['discount_price'] - $value['card_discount_price']));
                $chargeType[$value['type']][] = $value['total_price']; //各类医嘱的已收费的总费用 
                if ($value['type'] == ChargeInfo::$recipeType) {
                    if (isset($recipeState[$value['outpatient_id']]) && $recipeState[$value['outpatient_id']]['status'] == 5) {
                        $value['name'] = $value['name'] . ' (已退药)';
                    }
                    $value['unit'] = RecipeList::$getUnit[$value['unit']];
                }
                if ($value['type'] == ChargeInfo::$inspectType) {
                    if (isset($inspectState[$value['outpatient_id']]) && $inspectState[$value['outpatient_id']]['status'] == 4) {
                        $value['name'] = $value['name'] . ' (已取消)';
                    }
                }

                if ($value['type'] == ChargeInfo::$materialType || $value['type'] == ChargeInfo::$consumablesType) {
                    $materialList[] = $value;
                } else  if($value['type'] == ChargeInfo::$packgeType){
                    $packageRecordData = $value;
                } else {
                    $ordersList[] = $value;
                }
                
                if($value['type'] == ChargeInfo::$packgeType){
                    $packageRecordId = $value['outpatient_id'];
                }
            }
            isset($packageRecordData) &&  $ordersList[] = $packageRecordData;
            
            $userInfo = Patient::getUserInfo($id);
            $userInfo['sex'] = Patient::$getSex[$userInfo['sex']];
//            $userInfo['birth'] = date("Y-m-d", $userInfo['birthday']); //格式化出生日期
//            $userInfo['birthday'] = Patient::dateDiffage($userInfo['birthday'], time());
            $userInfo['update_time'] = date('Y-m-d H:i:s', $userInfo['update_time']);

            $chargeRecordLog['doctorName'] = $chargeInfoList[0]['username'] ? $chargeInfoList[0]['username'] : '--';
            $chargeRecordLog['check_price'] = isset($chargeType[ChargeInfo::$checkType]) ? Common::num(array_sum($chargeType[ChargeInfo::$checkType])) : '';
            $chargeRecordLog['cure_price'] = isset($chargeType[ChargeInfo::$cureType]) ? Common::num(array_sum($chargeType[ChargeInfo::$cureType])) : '';
            $chargeRecordLog['inspect_price'] = isset($chargeType[ChargeInfo::$inspectType]) ? Common::num(array_sum($chargeType[ChargeInfo::$inspectType])) : '';
            $chargeRecordLog['recipe_price'] = isset($chargeType[ChargeInfo::$recipeType]) ? Common::num(array_sum($chargeType[ChargeInfo::$recipeType])) : '';
            $chargeRecordLog['material_price'] = isset($chargeType[ChargeInfo::$materialType]) ? Common::num(array_sum($chargeType[ChargeInfo::$materialType])) : 0.00;
            $chargeRecordLog['consumables_price'] = isset($chargeType[ChargeInfo::$consumablesType]) ? Common::num(array_sum($chargeType[ChargeInfo::$consumablesType])) : 0.00;
            $chargeRecordLog['material_price'] = Common::num($chargeRecordLog['material_price'] + $chargeRecordLog['consumables_price']);
            $chargeRecordLog['diagnosis_price'] = isset($chargeType[ChargeInfo::$priceType]) ? Common::num(array_sum($chargeType[ChargeInfo::$priceType])) : '';
            $chargeRecordLog['package_price'] = isset($chargeType[ChargeInfo::$packgeType]) ? Common::num(array_sum($chargeType[ChargeInfo::$packgeType])) : '';
            $chargeRecordLog['totalPrice'] = Common::num($totalPrice);
            $chargeRecordLog['totalDiscount'] = Common::num($toatlDiscount);
            $chargeRecordLog['price'] = Common::num($totalPrice - $toatlDiscount);
            $chargeRecordLog = array_merge($chargeRecordLog, $userInfo);
            $spotInfo = Spot::getSpot($this->spotId);
            $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name','logo_shape']);
            //获取医嘱套餐数据
            $packageRecordText = $this->getPackageRecordPrintData($packageRecordId, $chargeInfoList[0]['record_id']);
            $this->result['data']['orderLogList'] = $ordersList;
            $this->result['data']['materialList'] = $materialList;
            $this->result['data']['chargeRecordLog'] = $chargeRecordLog;
            $this->result['data']['spotInfo'] = $spotInfo;
            $this->result['data']['spotConfig'] = $spotConfig;
            $this->result['data']['packageRecord'] = $packageRecordText;
//            $this->result['data']['print'] = 1;//只打印总收费清单
            return $this->result;
        } else {
            $this->result['errorCode'] = 405;
            $this->result['msg'] = '非法请求';
            return $this->result;
        }
    }

    /**
     * package-record
     * @param int $packageRecordId 医嘱套餐流水id
     * @return int $record_id 就诊流水id
     * @return array content 渲染的视图内容
     * @throws NotFoundHttpException
     * @desc 返回医嘱套餐的详情页
     */
    public function actionPackageRecord($packageRecordId, $record_id) {
        $request = Yii::$app->request;
        $packageRrcord = Outpatient::getPackageRecord($packageRecordId, $record_id);
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => $packageRrcord['price']['name'],
                'content' => $this->renderAjax('@packageRecordView', [
                    'packageRecord' => $packageRrcord
                ]),
            ];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * getPackageRecordPrintData
     * @param int $packageRecordId 医嘱套餐流水id
     * @param int $recordId 就诊流水id
     * @return array content 医嘱套餐关联的医嘱详情
     * @desc 返回医嘱套餐的详情
     */
    public function getPackageRecordPrintData($packageRecordId, $recordId) {
        //获取医嘱套餐包含的模板记录
        $packageRecord = Outpatient::getPackageRecord($packageRecordId, $recordId);
        $inspectRecord = $packageRecord['inspect'];
        $checkRecord = $packageRecord['check'];
        $cureRecord = $packageRecord['cure'];
        $recipeRecord = $packageRecord['recipe'];
        $priceText = Html::encode($packageRecord['price']['remarks']);
        $priceNameText = Html::encode($packageRecord['price']['name']);
        $packageRecord['price']['remarks'] = $priceText;
        $packageRecord['price']['name'] = $priceNameText;
        $inspectText = '';
        if (!empty($inspectRecord)) {
            foreach ($inspectRecord as $key => $value) {
                $inspectText .= Html::encode($value['name']) . '、';
            }
        }
        $checkText = '';
        if (!empty($checkRecord)) {
            foreach ($checkRecord as $key => $value) {
                $checkText .= Html::encode($value['name']) . '、';
            }
        }
        $cureText = '';
        if (!empty($cureRecord)) {
            foreach ($cureRecord as $key => $value) {
                $cureText .= Html::encode($value['name']) . $value['time'] . Html::encode($value['unit']) . '、';
            }
        }
        $recipeText = '';
        if (!empty($recipeRecord)) {
            foreach ($recipeRecord as $key => $value) {
                $specification = '';
                $value['specification'] != '' && $specification = '（' . Html::encode($value['specification']) . '）';
                $recipeText .= Html::encode($value['name']) . $specification . $value['num'] . Html::encode(RecipeList::$getUnit[$value['unit']]) . '、';
            }
        }
        $inspectText = rtrim($inspectText, '、');
        $checkText = rtrim($checkText, '、');
        $cureText = rtrim($cureText, '、');
        $recipeText = rtrim($recipeText, '、');
        $packageRecordText = [
            'price' => $packageRecord['price'],
            'inspect' => $inspectText,
            'check' => $checkText,
            'cure' => $cureText,
            'recipe' => $recipeText,
        ];
        return $packageRecordText;
    }
    
    
    /**
     * check
     * @param string $out_trade_no 订单号
     * @param string $total_amount 总金额
     *
     * @return int errorCode 错误代码(0-支付成功,1001-参数错误,1002-订单不存在,1003-订单未支付,1004-订单支付失败,1005-订单已过期)
     * @return string msg 提示信息
     * @desc 检查会员-套餐卡订单支付状态
     */
    public function actionPackageCardCheck() {
    
        $out_trade_no = Yii::$app->request->post('out_trade_no');
        $total_amount = Yii::$app->request->post('total_amount');
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$out_trade_no || !$total_amount) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $result = CardOrder::find()->select(['status'])->where(['out_trade_no' => $out_trade_no, 'total_amount' => $total_amount, 'spot_id' => $this->spotId])->asArray()->one();
        if (!$result) {
            $this->result['errorCode'] = 1002;
            $this->result['msg'] = '订单不存在';
            return $this->result;
        }
        if ($result['status'] == 1) {
            $this->result['errorCode'] = 1003;
            $this->result['msg'] = '订单未支付';
        } else if ($result['status'] == 3) {
            $this->result['errorCode'] = 1004;
            $this->result['msg'] = '订单支付失败';
        } else if ($result['status'] == 5) {
            $this->result['errorCode'] = 1005;
            $this->result['msg'] = '订单已过期';
        } else {
            $this->result['msg'] = '订单支付成功';
//             $this->result["data"]["charge_log_id"] = $this->findChargeLogIdByOrderId($out_trade_no);
        }
        return $this->result;
    }

}
