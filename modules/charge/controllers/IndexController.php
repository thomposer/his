<?php

namespace app\modules\charge\controllers;

use app\modules\charge\models\ChargeRecordLog;
use app\modules\outpatient\models\PackageRecord;
use app\modules\spot\models\Spot;
use app\modules\user\models\UserSpot;
use Yii;
use app\modules\charge\models\Charge;
use app\modules\charge\models\search\ChargeSearch;
use app\modules\charge\models\search\ChargeRecordLogSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Object;
use yii\helpers\Url;
use yii\db\Query;
use app\modules\patient\models\PatientRecord;
use app\modules\patient\models\Patient;
use yii\data\ActiveDataProvider;
use app\modules\charge\models\ChargeInfo;
use yii\db\ActiveQuery;
use app\modules\user\models\User;
use yii\web\Response;
use app\modules\charge\models\ChargeRecord;
use yii\helpers\Html;
use app\common\Common;
use yii\db\Exception;
use yii\web\NotAcceptableHttpException;
use app\modules\charge\models\Order;
use app\modules\spot_set\models\PaymentConfig;
use yii\log\Logger;
use app\modules\charge\models\PaymentLog;
use yii\helpers\Json;
use app\specialModules\recharge\models\CardRecharge;
use app\modules\spot\models\CardDiscount;
use app\specialModules\recharge\models\CardFlow;
use app\modules\report\models\Report;
use app\modules\charge\models\ChargeInfoLog;
use app\common\base\MultiModel;
use app\modules\spot_set\models\Material;
use app\modules\charge\models\MaterialCharge;
use app\modules\charge\models\FirstOrderFree;
use app\modules\stock\models\MaterialStockInfo;
use app\modules\spot_set\models\CardDiscountClinic;
use app\specialModules\recharge\models\MembershipPackageCard;
use app\specialModules\recharge\models\MembershipPackageCardService;
use app\specialModules\recharge\models\MembershipPackageCardFlow;
use app\modules\stock\models\MaterialStockDeductionRecord;
use app\specialModules\recharge\models\UserCard;
use app\specialModules\recharge\models\CardServiceLeft;

/**
 * IndexController implements the CRUD actions for Charge model.
 */
class IndexController extends BaseController
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

    /**
     * Lists all Charge models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ChargeSearch();
        $params = Yii::$app->request->queryParams;
        $type = (isset($params['type']) && !empty($params['type'])) ? $params['type'] : 3;
        if ($type == 3) {
            $dataProvider = $searchModel->search($params, $this->pageSize);
        } else if ($type == 6) {
            $searchRecordLogModel = new ChargeRecordLogSearch();
            $dataProvider = $searchRecordLogModel->search($params, $this->pageSize);
        } else {
            $dataProvider = $searchModel->chargeRecord($params, $this->pageSize);
        }

        $cardInfo = CardRecharge::getCardInfoByQueryNurse($dataProvider->query);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'searchRecordLogModel' => $searchRecordLogModel,
                    'dataProvider' => $dataProvider,
                    'cardInfo' => $cardInfo,
        ]);
    }

    /**
     * Displays a single Charge model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Charge model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id) {
        $request = Yii::$app->request;
        $info = $this->getUserInfo($id);

        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            $model = new ChargeRecord();
            $model->scenario = 'create';
            $model->record_id = $id;
            $model->patient_id = $info['patient_id'];
            $model->pks = $request->get('pks');
            Yii::$app->response->format = Response::FORMAT_JSON;
            $postData = $request->post();
            $subject = Html::encode(Yii::$app->cache->get(Yii::getAlias('@spotName') . $this->spotId . $this->userInfo->id)) . '医疗费用-' . Html::encode($info['username']);
            //获取用户会员卡信息
            $cardInfo = CardRecharge::getCardInfo(null, $info['iphone']);
            $packageCardInfoList = MembershipPackageCard::getCardInfo($info['patient_id']);
            $serviceCardInfoList = UserCard::getCardInfo($info['iphone']);
            if ($model->load($postData) && $model->validate()) {
               $ret = $this->validatePostData($model, $postData);
               if($ret['errorCode'] != 0){
                   return [
                        'forceClose' => true,
                        'forceType' => 2,
                        'forceMessage' => $ret['message']
                    ];
               }
                $ret = $this->showTipView($model, $postData);
                if(!empty($ret)){
                    return $ret;
                }
                
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $pks = explode(',', $model->pks);
                    $orderModel = Order::findOne(['out_trade_no' => $model->out_trade_no, 'total_amount' => $model->price, 'spot_id' => $this->spotId]);
                    if ($orderModel && in_array($orderModel->status, [1, 3]) && !empty($pks)) {//若还没支付/支付失败。则更改订单状态
                        if (($model->type == 3 && $model->scanMode == 2) || ($model->type == 4 && $model->scanMode == 2)) {
                            $message = '请选择正常的支付类型';
                            return [
                                'forceClose' => true,
                                'forceType' => 2,
                                'forceMessage' => $message
                            ];
                        } else {
                            $cardFlowId = 0; //会员卡交易流水id，默认为0
                            $chargeType = PatientRecord::find()->select(['charge_type'])->where(['id' => $id, 'spot_id' => $this->spotId])->asArray()->one();

                            if ($model->price == 0) {//若支付总金额为0，则支付方式一致为 无 。
                                $model->type = 0;
                            }
                            if ($model->type == 2 || $model->type == 9) {//
                                $model->cash = $model->price;
                                $change = 0.00;
                            } else if ($model->type == 1) {
                                $change = $model->cash - $model->price; //找零
                            } else if ($model->type == 6 && $model->cardType != 0) {
                                //扣减会员卡金额
                                $cardTotalPrice = Yii::$app->request->post('cardTotalPrice');
                                $chargeInfoArray = Yii::$app->request->post('cardInfo');
                                $params['chargeInfoArray'] = json_decode($chargeInfoArray, true);
                                $ret = $this->insertRechargeRecord($cardTotalPrice,$info,$model,$chargeType['charge_type']);
                                if($ret['errorCode'] != 0){
                                    $dbTrans->rollBack();
                                    return [
                                        'forceClose' => true,
                                        'forceType' => 2,
                                        'forceMessage' => $ret['message'],
                                    ];
                                }
                                $cardFlowId = $ret['flowId'];
                                $orderModel->total_amount = Yii::$app->request->post('cardTotalPrice');
                                $model->cash = $orderModel->total_amount;
                            }else if($model->type == 7){
                                $serviceIdArr = $postData['ServiceCardServiceId'];//选中的服务
                                $serviceTime = $postData['ServiceCardServiceTime'];//扣减的次数
                                $ret = $this->updateServiceCardTime($serviceIdArr,$serviceTime,$serviceCardInfoList);
                                if ($ret['errorCode'] != 0) {
                                    $dbTrans->rollBack();
                                    return [
                                        'forceClose' => true,
                                        'forceType' => 2,
                                        'forceMessage' => $ret['message'],
                                    ];
                                }
                            }else if($model->type == 8){
                                $rows = ChargeInfo::find()->select(['id', 'name', 'unit_price', 'num', 'discount_price', 'type'])->where(['id' => $pks, 'record_id' => $id, 'spot_id' => $this->spotId, 'status' => 0])->asArray()->all();
                                $chargeInfoArray = [];
                                foreach ($rows as $v) {
                                    $chargeInfoArray[$v['id']] = ($v['unit_price'] * intval($v['num'])) - $v['discount_price'];
                                }
                                $params['chargeInfoArray'] = $chargeInfoArray;//会员卡折扣金额
                                $serviceIdArr = $postData['PackageCardServiceId'];//选中的服务
                                $serviceTime = $postData['PackageCardServiceTime'];//扣减的次数
                                $ret = $this->updateServiceTime($serviceIdArr,$serviceTime,$packageCardInfoList);
                                if ($ret['errorCode'] != 0) {
                                    $dbTrans->rollBack();
                                    return [
                                        'forceClose' => true,
                                        'forceType' => 2,
                                        'forceMessage' => $ret['message'],
                                    ];
                                }
                            }

                            $params['out_trade_no'] = $model->out_trade_no;
                            $body = [
                                'patient_id' => $model->patient_id,
                                'pks' => $pks
                            ];
                            $params['body'] = Json::encode($body);
//                            $params['materialNum'] = $resultInfo['num'];

                            if ($model->type == 3 || $model->type == 4) {//微信或支付宝  扫描枪支付
                                $scpRes = PaymentLog::scannerPayment($orderModel, $params, $model->type, $model, $subject);
                                Yii::info($scpRes, 'chargeCreate');
                                if (!$scpRes || $scpRes['errorCode'] != 0) {
                                    throw new Exception('PaymentLog::scannerPayment Exception ');
                                } else {
                                    $chargeRecordLogId = $scpRes['chargeRecordLogId'];
                                }
                            } else {
                                $orderModel->status = 2; //支付成功
                                $orderModel->type = $model->type; //支付类型
                                $orderModel->save();
                                $chargeRecordId = PaymentLog::editChargeInfo($orderModel, $params, $model->type, $model->allPrice, $model->cash, $change, $cardFlowId); //修改收费记录详情
                                //记录交易流水
                                $chargeRecordLogId = ChargeRecordLog::saveChargeRecordLog($this->spotId, $model->patient_id, $model->record_id, $params, 1, $model->type, $model->cash, $change, 0, 0, '', $cardFlowId);
                            }
                            
                            if($model->type == 8){//套餐卡支付  增加流水
                                $recordInfo = Report::getDoctorName($id);
                                $packageCardParams = [];
                                foreach ($serviceIdArr as $key => $value) {
                                    $packageCardParams[$key]['membership_package_card_id'] = $key;
                                    foreach ($value as $v) {
                                        $packageCardParams[$key]['service'][$v]['package_card_service_id'] = $packageCardInfoList[$key][$v]['package_card_service_id'];
                                        $packageCardParams[$key]['service'][$v]['time'] = $serviceTime[$v];
                                    }
                                }
                                if(2 == $recordInfo['charge_type']){
                                    $ret = MembershipPackageCardFlow::addFlow($this->spotId, $info['patient_id'], '其他收费' , 1, 0, 3, $this->userInfo->id, $packageCardParams, $chargeRecordId, $chargeRecordLogId);
                                }else{
                                    $ret = MembershipPackageCardFlow::addFlow($this->spotId, $info['patient_id'], $recordInfo['username'].'医生门诊' , 1, 0, 1, $this->userInfo->id, $packageCardParams, $chargeRecordId, $chargeRecordLogId);
                                }
                                if (!$ret) {
                                    $dbTrans->rollBack();
                                    return [
                                        'forceClose' => true,
                                        'forceMessage' => '操作失败',
                                        'forceRedirect' => Url::to(['create', 'id' => $id]),
                                    ];
                                }
                            }
                            $message = '支付成功';
                        }


                        Yii::$app->getSession()->setFlash('success', $message);
                    } else {
                        //若已被支付，或者该订单已过期。则自动退款
                        switch ($orderModel->status) {
                            case 2 :
                                $message = '该订单已支付';
                                Yii::$app->getSession()->setFlash('success', $message);
                                break;
                            case 4 :
                                $message = '该订单已退款';
                                Yii::$app->getSession()->setFlash('success', $message);
                                break;
                            case 5 :
                                $message = '该订单已过期';
                                Yii::$app->getSession()->setFlash('success', $message);
                                Order::deleteAll(['record_id' => $orderModel->record_id, 'spot_id' => $orderModel->spot_id, 'out_trade_no' => $model->out_trade_no, 'status' => 5]);
                                break;
                        }
                    }
                    $dbTrans->commit();
                    return [
                        'forceReload' => false,
                        'forceClose' => true,
                        'forceRedirect' => Url::to(['@chargeIndexTradeLog', 'id' => $chargeRecordLogId]),
                    ];
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    if ($model->type == 8) {//套餐卡
                        return [
                            'forceClose' => true,
                            'forceMessage' => '操作失败',
                            'forceRedirect' => Url::to(['create', 'id' => $id]),
                        ];
                    }
                    Yii::info(json_encode($e->errorInfo), 'error');
                    $resetData = $this->resetChargeFrom($model, $cardInfo[$info['iphone']], $info);
                    return $resetData;
                }
            } else {
                $pks = explode(',', $model->pks); // Array or selected records primary keys
               
                //扣减库存逻辑在医生门诊做
//                $ret = $this->checkStockNum($id, $pks);
//                if ($ret['errorCode'] != 0) {
//                    Yii::$app->getSession()->setFlash('success', $ret['errorMessage']);
//                    return [
//                        'forceClose' => true,
//                        'forceRedirect' => Url::to(['create', 'id' => $id]),
//                    ];
//                }
                $ret = $this->showRechargeView($model,$cardInfo,$packageCardInfoList,$subject,$info,$serviceCardInfoList);
                if($ret['errorCode'] != 0){
                    Yii::$app->getSession()->setFlash('success', '该订单已收费');
                    return [
                        'forceClose' => true,
                        'forceRedirect' => Url::to(['create', 'id' => $id]),
                    ];
                }
                return $ret['data'];
            }
        } else {
            $dataProvider = $this->getChargeInfo($id);
            $chargeAll = $dataProvider->query->asArray()->all();
            $doctorName = $chargeAll[0]['username'];
            $chargeType = $this->setChargeType($chargeAll);
            $updateMaterialButtonType = $chargeAll[0]['charge_type'];
            $recipeState = ChargeInfo::recipeStateData($chargeAll);
            $inspectState = ChargeInfo::inspectStateData($chargeAll);
            $recipeInspectState = [
                'recipe' => $recipeState,
                'inspectState' => $inspectState,
            ];
            return $this->render('create', [
                        'id' => $id,
                        'dataProvider' => $dataProvider,
                        'userInfo' => $info,
                        'chargeType' => $chargeType,
                        'doctorName' => $doctorName,
                        'updateMaterialButtonType' => $updateMaterialButtonType,
                        'entrance' => 1,
                        'recipeInspectState' => $recipeInspectState
            ]);
        }
    }

    private function resetChargeFrom($model, $cardInfo = null, $info, $packageCardInfoList = null) {
        $params = Yii::$app->request->post('codeUrl');
        $model->wechatAuthCode = '';
        $model->alipayAuthCode = '';
        if ($model->cash == '0.00') {
            $model->cash = '';
        }
        return [
            'title' => "收费",
            'content' => $this->renderAjax('_be_charge_form', [
                'model' => $model,
                'type' => json_decode($params['type'], true),
                'info' => $info, //患者姓名
                'outTradeNo' => $model->out_trade_no,
                'aliPayUrl' => $params['aliPayUrl'], //支付宝二维码url
                'wechatUrl' => $params['wechatUrl'], //微信二维码url
                'readonly' => $model->readonly,
                'allPrice' => $model->allPrice,
                'cardInfo' => $cardInfo
            ]),
            'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
            Html::button('打印费用信息', ['class' => 'btn btn-cancel btn-form print-info']) .
            Html::button('确认收费', ['class' => 'btn btn-default btn-form create-rebate', 'type' => "submit"]),
            'forceType' => 2,
            'forceMessage' => '支付失败,请重新支付'
        ];
    }

    /**
     *
     * @param 就诊流水ID|收费记录id $id
     * @param number $status 状态 0-未收费,1-已收费,2-已退费
     * @return 返回当前就诊状态的列表
     */
    private function getChargeInfo($id, $status = 0) {
        $chargeQuery = new ActiveQuery(ChargeInfo::className());
        $chargeQuery->from(['a' => ChargeInfo::tableName()]);
        $chargeQuery->select(['a.is_charge_again', 'a.id', 'a.charge_record_id', 'a.record_id', 'a.type', 'a.name', 'a.unit', 'a.unit_price', 'a.num', 'a.discount_price', 'a.discount_reason', 'a.card_discount_price', 'a.doctor_id', 'a.outpatient_id', 'b.username', 'pay_type' => 'c.type', 'c.discount_type', 'recordDiscountPrice' => 'c.discount_price', 'recordDiscountReason' => 'c.discount_reason', 'c.charge_type', 'd.fee_remarks','packageRecordId' => 'h.id']);
        $chargeQuery->leftJoin(['d' => PatientRecord::tableName()], '{{a}}.record_id = {{d}}.id');
        $chargeQuery->leftJoin(['g' => Report::tableName()], '{{g}}.record_id={{d}}.id');
        $chargeQuery->leftJoin(['b' => User::tableName()], '{{g}}.doctor_id = {{b}}.id');
        $chargeQuery->leftJoin(['c' => ChargeRecord::tableName()], '{{a}}.charge_record_id = {{c}}.id');
        $chargeQuery->leftJoin(['h' => PackageRecord::tableName()], '{{d}}.id = {{h}}.record_id');
        if ($status == 0) {
            $chargeQuery->where(['a.spot_id' => $this->spotId, 'a.record_id' => $id, 'a.status' => $status]);
        } else if ($status == 1) {
            $chargeQuery->addSelect(['c.income', 'c.change', 'c.create_time']);
            $chargeQuery->where(['a.spot_id' => $this->spotId, 'a.record_id' => $id, 'a.status' => $status]);
            $chargeQuery->orderBy(['a.charge_record_id' => SORT_ASC]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $chargeQuery,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
    }

    /**
     *
     * @param 就诊流水ID $id
     * @return 返回患者基本信息
     */
    private function getUserInfo($id) {
        $query = new Query();
        $query->from(['a' => PatientRecord::tableName()]);
        $query->select(['a.patient_id', 'b.username', 'b.patient_number', 'b.iphone', 'b.sex', 'b.head_img', 'b.birthday', 'b.first_record', 'a.update_time', 'a.makeup']);
        $query->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id');
        $query->where(['a.id' => $id, 'a.spot_id' => $this->spotId]);
        return $query->one();
    }

    /**
     * Updates an existing Charge model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id 收费记录ID
     * @return mixed
     */
    public function actionUpdate($id) {

        $request = Yii::$app->request;

        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            $model = new ChargeInfo();
            $model->record_id = $id;
            $model->scenario = 'update';
            $model->pks = $request->get('pks');
            $model->charge_record_id = $id;
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                $totalPrice = [];
                $pks = explode(',', $model->pks); // Array or selected records primary keys


                $totalPrice = $this->findTotalPrice($id, $pks, 1);
                $model->total_price = Common::num(abs(array_sum($totalPrice)));
                $model->allPrice = $model->total_price;
                return [
                    'title' => "退费",
                    'content' => $this->renderAjax('_refund_charge_form', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确定', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
            } else if ($model->load($request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    //修改收费详情表里的状态，以及添加退费原因
                    //新增退费记录
                    $pks = explode(',', $model->pks);
                    $recordData = $this->getChargeRecordInfo($id, $pks);

                    foreach ($recordData as $value) {
                        $ret = $this->saveRecordInfo($value['charge_record_id'], $model, $value['pks']);
                        if ($ret === false) {
                            $dbTrans->rollBack();
                            return [
                                'forceClose' => true,
                                'forceType' => 2,
                                'forceMessage' => '退费失败！',
                            ];
                        }
                    }

                    Yii::$app->getSession()->setFlash('success', '退费成功');
                    $dbTrans->commit();
                    if (count($recordData) > 1) {//多个收费单，生成多个流水记录，跳转到列表
                        return [
                            'forceReload' => false,
                            'forceClose' => true,
                            'forceRedirect' => Url::to(['@chargeIndexIndex', 'type' => 6]),
                        ];
                    } else {//单个收费单，跳到详情
                        return [
                            'forceReload' => false,
                            'forceClose' => true,
                            'forceRedirect' => Url::to(['@chargeIndexTradeLog', 'id' => $ret]),
                        ];
                    }
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    return [
                        'forceReload' => true,
                        'forceClose' => true,
                    ];
                }
            } else {
                return [
                    'title' => "退费",
                    'content' => $this->renderAjax('_refund_charge_form', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('确定', ['class' => 'btn btn-default btn-form', 'type' => "submit"]) .
                    Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"])
                ];
            }
        } else {
            $query = new Query();
            $query->from(['a' => ChargeRecord::tableName()]);
            $query->select(['id']);
            $query->where(['a.record_id' => $id, 'a.status' => 1]);
            $chargeRecordIdList = $query->all();
            $chargeRecordIdList = array_column($chargeRecordIdList, 'id');
            $flowListCount = CardFlow::getCardChargeRecord($chargeRecordIdList);
            $dataProvider = $this->getChargeInfo($id, 1);
            $chargeType['total'][] = 0;
            $cardTotalDiscount = 0;
            $chargeAll = $dataProvider->query->asArray()->all();
            $chargeType = $this->setChargeType($chargeAll);
            if (!empty($chargeType)) {
                $refundAmount = $this->findRefundAmount($id); //退费总金额
            }
            $refund_reason = '';
            $reason_description = '';

            $soptInfo = Spot::find()->select(['spot_name', 'spot', 'status', 'province', 'city', 'area', 'telephone', 'icon_url'])->where(['id' => $this->spotId])->asArray()->one();
            $doctorName = $chargeAll[0]['username'];
            $recipeState = ChargeInfo::recipeStateData($chargeAll);
            $inspectState = ChargeInfo::inspectStateData($chargeAll);
            $recipeInspectState = [
                'recipe' => $recipeState,
                'inspectState' => $inspectState,
            ];
            return $this->render('update', [
                        'dataProvider' => $dataProvider,
                        'refundAmount' => $refundAmount,
                        'refund_reason' => $refund_reason,
                        'reason_description' => $reason_description,
                        'userInfo' => $this->getUserInfo($chargeAll[0]['record_id']),
                        'chargeType' => $chargeType,
                        'doctor_name' => $chargeAll[0]['username'],
                        'soptInfo' => $soptInfo,
                        'discountType' => $chargeAll[0]['discount_type'],
                        'discountPrice' => $chargeAll[0]['recordDiscountPrice'],
                        'discountReason' => $chargeAll[0]['recordDiscountReason'],
                        'income' => $chargeAll[0]['income'],
                        'change' => $chargeAll[0]['change'],
                        'chargeCreateTime' => $chargeAll[0]['create_time'],
                        'chargeId' => trim($chargeType['chargeId'], ','),
                        'doctorName' => $doctorName,
                        'flowListCount' => $flowListCount,
                        'entrance' => 1,
                        'recipeInspectState' => $recipeInspectState,
            ]);
        }
    }

    /**
     * 交易流水
     * @param  integer $id 交易流水表id
     */
    public function actionTradeLog($id) {
        $dataProvider = ChargeInfoLog::chargeInfoLog($id);
        $chargeRecordLogList = ChargeRecordLog::getChargeLogList($id);
        return $this->render('tradeLog', [
                    'chargeRecordLogList' => $chargeRecordLogList,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param string $id 收费记录ID
     * @return mixed
     * @desc 重新收费
     */
    public function chargeAgain($id) {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $pks = explode(',', $request->post('pks'));

        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $query = new Query();
            $query->select(['charge_record_id' => 'a.id', 'charge_info_id' => 'b.id', 'a.record_id', 'b.unit_price', 'b.num', 'b.discount_price', 'b.card_discount_price']);
            $query->from(['a' => ChargeRecord::tableName()]);
            $query->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.charge_record_id');
            $query->where(['a.record_id' => $id, 'a.spot_id' => $this->spotId, 'b.id' => $pks]);
            $chargeInfos = $query->all();

            $totalPrice = []; //实际退还费用
            $chargeRecordChild = [];
            foreach ($chargeInfos as $chargeInfo) {
                $totalPrice[$chargeInfo['charge_record_id']] += $chargeInfo['unit_price'] * $chargeInfo['num'] - $chargeInfo['discount_price'] - $chargeInfo['card_discount_price'];
                $chargeRecordChild[$chargeInfo['charge_record_id']][] = $chargeInfo['charge_info_id'];
            }
            $chargeRecordId = ChargeRecord::find()->select(['id'])->where(['record_id' => $id, 'status' => 2, 'spot_id' => $this->spotId])->one()['id'];
            foreach ($chargeRecordChild as $key => $value) {



                Yii::$app->db->createCommand()->update(ChargeInfo::tableName(), ['status' => 0, 'is_charge_again' => 1, 'discount_price' => 0.00, 'card_discount_price' => 0.00, 'discount' => 100.00, 'discount_reason' => '', 'charge_record_id' => $chargeRecordId], ['id' => $value, 'spot_id' => $this->spotId])->execute();

                $chargeInfoCount = ChargeInfo::find()->where(['charge_record_id' => $key, 'spot_id' => $this->spotId])->count();
                if ($chargeInfoCount == 0) {  // 没有记录,则删除
                    Yii::$app->db->createCommand()->delete(ChargeRecord::tableName(), ['id' => $key, 'spot_id' => $this->spotId])->execute();
                } else { // 有记录,扣减金额
                    $chargeRecordModel = ChargeRecord::findOne(['id' => $key, 'spot_id' => $this->spotId]);
                    $chargeRecordModel->price = $chargeRecordModel->price - $totalPrice[$key];
                    $chargeRecordModel->save();
                }
            }


            $dbTrans->commit();

            return [
                'forceReload' => false,
                'forceClose' => true,
                'forceMessage' => '操作成功',
                'forceRedirect' => Url::to(['index', 'type' => 3]),
            ];
        } catch (Exception $e) {
            $dbTrans->rollBack();
        }
    }

    /*
     * 退费记录
     * @param $id 收费记录ID
     */

    public function actionRefund($id) {

        $request = Yii::$app->request;
        $flowCount = CardFlow::find()->where(['f_charge_record_id' => $id, 'f_spot_id' => $this->spotId])->count();
        if ($request->isAjax) {

            return $this->chargeAgain($id); // 确定重新收费
        } else {

            $chargeQuery = (new ActiveQuery(ChargeInfo::className()));
            $chargeQuery->from(['a' => ChargeInfo::tableName()]);
            $chargeQuery->select(['a.is_charge_again', 'a.charge_record_id', 'a.id', 'a.record_id', 'a.type', 'a.outpatient_id', 'a.name', 'a.unit', 'a.unit_price', 'a.num', 'a.doctor_id', 'b.username', 'a.reason', 'a.discount_price', 'a.discount_reason', 'a.reason_description', 'a.card_discount_price', 'c.discount_type', 'recordDiscountPrice' => 'c.discount_price', 'recordDiscountReason' => 'c.discount_reason', 'pay_type' => 'c.type', 'c.income', 'd.fee_remarks','packageRecordId' => 'e.id']);
            $chargeQuery->leftJoin(['b' => User::tableName()], '{{a}}.doctor_id = {{b}}.id');
            $chargeQuery->leftJoin(['c' => ChargeRecord::tableName()], '{{a}}.charge_record_id = {{c}}.id');
            $chargeQuery->leftJoin(['d' => PatientRecord::tableName()], '{{a}}.record_id = {{d}}.id');
            $chargeQuery->leftJoin(['e' => PackageRecord::tableName()], '{{e}}.record_id = {{d}}.id');
            $chargeQuery->where(['a.spot_id' => $this->spotId, 'a.record_id' => $id, 'a.status' => 2]);
            $dataProvider = new ActiveDataProvider([
                'query' => $chargeQuery,
                'pagination' => false,
                'sort' => [
                    'attributes' => ['']
                ]
            ]);
            $chargeType = [];
            $chargeType['total'][] = 0;
            $chargeAll = $dataProvider->query->asArray()->all();
            $refundInfo = [];
            foreach ($chargeAll as $value) {
                if (!isset($refundInfo[$value['reason'] . $value['reason_description']])) {
                    $refundInfo[$value['reason'] . $value['reason_description']]['reason'] = ChargeInfo::$getRefundChargeReason[$value['reason']];
                    $refundInfo[$value['reason'] . $value['reason_description']]['reasonDescription'] = $value['reason_description'];
                    $refundInfo[$value['reason'] . $value['reason_description']]['name'][] = $value['name'];
                } else {
                    $refundInfo[$value['reason'] . $value['reason_description']]['name'][] = $value['name'];
                }
            }
//            $refund_reason = $chargeAll[0]['reason'];
//            $reason_description = $chargeAll[0]['reason_description'];
            $doctorName = $chargeAll[0]['username'];
            $chargeType = $this->setChargeType($chargeAll);
            $recipeState = ChargeInfo::recipeStateData($chargeAll);
            $inspectState = ChargeInfo::inspectStateData($chargeAll);
            $recipeInspectState = [
                'recipe' => $recipeState,
                'inspectState' => $inspectState,
            ];
            return $this->render('refund', [
                        'dataProvider' => $dataProvider,
                        'refundInfo' => $refundInfo,
                        'userInfo' => $this->getUserInfo($chargeAll[0]['record_id']),
                        'chargeType' => $chargeType,
                        'discountType' => $chargeAll[0]['discount_type'],
                        'discountPrice' => $chargeAll[0]['recordDiscountPrice'],
                        'discountReason' => $chargeAll[0]['recordDiscountReason'],
                        'doctorName' => $doctorName,
                        'payType' => $chargeAll[0]['pay_type'],
                        'income' => $chargeAll[0]['income'],
                        'flowCount' => $flowCount,
                        'entrance' => 2,
                        'recipeInspectState' => $recipeInspectState,
            ]);
        }
    }

    public function actionPrintRebate($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                $printQuery = (new ActiveQuery(ChargeInfo::className()));
                $printQuery->from(['a' => ChargeInfo::tableName()]);
                $printQuery->select([ 'pay_type' => 'c.type', 'a.id', 'a.record_id', 'a.type', 'a.name', 'a.unit', 'a.unit_price', 'a.num', 'a.doctor_id', 'b.username']);
                $printQuery->leftJoin(['b' => User::tableName()], '{{a}}.doctor_id = {{b}}.id');
                $printQuery->leftJoin(['c' => ChargeRecord::tableName()], '{{a}}.charge_record_id = {{c}}.id');
                $printQuery->where(['a.spot_id' => $this->spotId, 'a.charge_record_id' => $id, 'a.status' => 1]);
                $dataProvider = new ActiveDataProvider([
                    'query' => $printQuery,
                    'pagination' => false,
                    'sort' => [
                        'attributes' => ['']
                    ]
                ]);
                $chargeType = [];
                $chargeType['total'][] = 0;

                $chargeAll = $dataProvider->query->all();

                if ($chargeAll) {
                    foreach ($chargeAll as $v) {
                        $chargeType['total'][] = $v['unit_price'] * $v['num'];
                        $chargeType[$v['type']][] = $v['unit_price'] * $v['num']; //各类医嘱的已收费的总费用
                    }
                } else {
                    throw new NotFoundHttpException('你所请求的页面不存在');
                }
                $refund_reason = '';
                $reason_description = '';

                $soptInfo = Spot::find()->select(['spot_name', 'spot', 'status', 'province', 'city', 'area', 'telephone'])->where(['id' => $this->spotId])->asArray()->one();

                return [
                    'title' => "打印预览",
                    'content' => $this->renderAjax('_print_rebate_form', [
                        'dataProvider' => $dataProvider,
                        'refund_reason' => $refund_reason,
                        'reason_description' => $reason_description,
                        'userInfo' => $this->getUserInfo($chargeAll[0]['record_id']),
                        'chargeType' => $chargeType,
                        'doctor_name' => $chargeAll[0]['username'],
                        'pay_type' => $chargeAll[0]['pay_type'],
                        'soptInfo' => $soptInfo,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('打印', ['class' => 'btn btn-default btn-form rebate', 'type' => "button"])
                ];
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    public function actionCreateMaterial() {
        $request = Yii::$app->request;
        $id = $request->get('patientId');
        $model = new MultiModel([
            'models' => [
                'patientModel' => $this->findPatientModel($id),
                'materialModel' => new MaterialCharge()
            ]
        ]);
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (($postParam = Yii::$app->request->post())) {
                if (isset($postParam['postParam'])) {
                    $postParam = json_decode($postParam['postParam'], true);
                    $id = $postParam['Patient']['id'];
                    $id && $model->setModel('patientModel', $this->findPatientModel($postParam['Patient']['id']));
                    $model->load($postParam);
                    return $this->saveChargeInfo($model, 2);
                } else {
                    if ($model->load($postParam) && $model->validate()) {
                        //提示 是否有老用户
                        $similarUser = $this->checkOldUser($model);
                        if ($similarUser['patientType'] == 2 && !empty($similarUser['similarUser'])) {
                            $postParam['Patient']['id'] = $id;
                            $this->result['similarUser'] = $similarUser;
                            $this->result['postParam'] = $postParam;
                            $this->result['errorCode'] = 1002;
                            return $this->result;
                        } else {
                            return $this->saveChargeInfo($model, 1);
                        }
                    } else {
                        $this->result['errorCode'] = 1001;
                        $materialModel = $model->getModel('materialModel');
                        $patientModel = $model->getModel('patientModel');
                        $error[] = !empty($materialModel->errors['num']) ? $materialModel->errors['num'][0] : '';
                        $error[] = !empty($patientModel->errors['birthTime']) ? $patientModel->errors['birthTime'][0] : '';
                        $error[] = !empty($patientModel->errors['hourMin']) ? $patientModel->errors['hourMin'][0] : '';
                        $error[] = !empty($patientModel->errors['username']) ? $patientModel->errors['username'][0] : '';
                        $errorData = array_values(array_filter($error));
                        $this->result['msg'] = $errorData[0];
                        return $this->result;
                    }
                }
            }
        } else {
            $query = MaterialCharge::find()->select(['id'])->where(['id' => 0]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
                'sort' => [
                    'attributes' => ['']
                ]
            ]);
            $list = Material::getList(['id', 'name', 'meta', 'specification', 'unit', 'price', 'remark', 'manufactor', 'attribute'], ['status' => 1]);
            $materialTotal = MaterialStockInfo::getTotal();
            return $this->render('create-material', [
                        'model' => $model,
                        'list' => $list,
                        'dataProvider' => $dataProvider,
                        'materialTotal' => $materialTotal
                            ]
            );
        }
    }

    public function actionConfirmCharge() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $postParam = Yii::$app->request->post();
        $ret = [
            'title' => "",
            'content' => $this->renderAjax('@reportRecordConfirmReportView', [
                'postParam' => json_encode($postParam['postParam']),
                'similarUser' => $postParam['similarUser'],
                'actionUrl' => $postParam['actionUrl']
            ]),
            'footer' =>
            Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
            Html::button('确认保存', ['class' => 'btn btn-default btn-form card-check-btn', 'type' => "submit"])
        ];
        return $ret;
    }

    /**
     *
     * @param type $model
     * @return 检测是否存在老用户  并提示
     */
    protected function checkOldUser($mutilModel) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $patientModel = $mutilModel->getModel('patientModel');
        if ($patientModel->patient_number && $patientModel->patient_number != '000000') {//老用户
            $patientType = 1;
            $similarUser = Patient::similarPatient($patientModel, $patientType);
        } else {//新用户
            $patientType = 2;
            $similarUser = Patient::similarPatient($patientModel, $patientType);
        }
        return ['patientType' => $patientType, 'similarUser' => $similarUser];
    }

    protected function saveChargeInfo($model, $type = 1) {
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $materialModel = $model->getModel('materialModel');
            if (isset($materialModel->stockId)) {
                $result = $model->getModel('patientModel')->save();
                if ($result) {
                    $rows = [];
                    $recordModel = new PatientRecord();
                    $recordModel->patient_id = $model->getModel('patientModel')->id;
                    $recordModel->status = 6;
                    $recordModel->end_time = time();
                    $recordModel->makeup = 1;
                    $recordModel->charge_type = 2;
                    $recordModel->save();

                    $chargeRecordModel = new ChargeRecord();
                    $chargeRecordModel->patient_id = $model->getModel('patientModel')->id;
                    $chargeRecordModel->parent_id = 0;
                    $chargeRecordModel->charge_type = 2; //物资管理收费
                    $chargeRecordModel->record_id = $recordModel->id;
                    $chargeRecordModel->status = 2;
                    $chargeRecordModel->save();

                    if (count($materialModel->stockId) > 0) {
                        $idList = Material::getList(['id', 'name', 'unit', 'price', 'specification', 'tag_id'], ['id' => $materialModel->stockId]);
                        foreach ($materialModel->stockId as $key => $v) {
                            if ($materialModel->deleted[$key] != 1) {
                                $rows[] = [
                                    $this->spotId,
                                    $recordModel->id,
                                    $chargeRecordModel->id,
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
                                    time(),
                                    time()
                                ];
                            }
                        }
                    }
                    if (count($rows) > 0) {
                        Yii::$app->db->createCommand()->batchInsert(ChargeInfo::tableName(), ['spot_id', 'record_id', 'charge_record_id', 'type', 'outpatient_id', 'name', 'specification', 'unit', 'unit_price', 'tag_id', 'num', 'remark', 'origin', 'create_time', 'update_time'], $rows)->execute();
                    }
                }
            } else {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '请选择收费项';
                return $this->result;
            }
            
            $ret = MaterialStockDeductionRecord::updateStockInfo($recordModel->id, MaterialStockDeductionRecord::$chargeInfo);
            if ($ret['errorCode']) {
                $dbTrans->rollBack();
                $this->result['errorCode'] = 1014;
                $this->result['msg'] = $ret['message'];
                return $this->result;
            }

            $dbTrans->commit();
            if ($type == 1) {
                $this->result['errorCode'] = 0;
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->result;
            } else {
                return [
                    'forceClose' => true,
                    'forceMessage' => '保存成功',
                    'forceRedirect' => Url::to(['index'])
                ];
            }
        } catch (Exception $e) {
            Yii::info('saveChargeInfo ' . $e->getMessage());
            $dbTrans->rollBack();
        }
    }

    protected function findPatientModel($id = 0) {
        $model = Patient::findOne(['id' => $id, 'spot_id' => $this->parentSpotId]);
        if (!$model) {
            $model = new Patient();
            $model->personalhistory = '';
            $model->first_record = 1;
        } else {
            $model->birthTime = $model->birthday == 0 ? '' : date('Y-m-d', $model->birthday);
            $model->hourMin = $model->birthday == 0 ? '' : date('H:i', $model->birthday);
            $firstRecord = PatientRecord::getFirstRecord($model->id);
            $model->first_record = $firstRecord;
        }
        $model->scenario = 'createMaterial';
        return $model;
    }

    /**
     * Finds the Charge model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Charge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ChargeInfo::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /*
     * author:zhangchuangtong
     * 获取退费总金额
     * @param $id 收费记录ID
     * return float
     */

    protected function findRefundAmount($id) {
        $query = new \yii\db\Query();
        $chargeAll = $query->from(['a' => ChargeRecord::tableName()])
                ->select(['b.unit_price', 'b.num', 'b.status', 'b.discount_price'])
                ->leftJoin(['b' => ChargeInfo::tableName()], '{{b}}.charge_record_id = {{a}}.id')
                ->where(['a.spot_id' => $this->spotId, 'a.parent_id' => $id, 'b.status' => 2])
                ->all();
        $total = 0.00;
        foreach ($chargeAll as $v) {
            $total += $v['unit_price'] * $v['num'] - $v['discount_price'];
        }
        return Common::num($total);
    }

    /**
     *
     * @param unknown $dataProvider
     * @throws NotFoundHttpException
     * @desc 返回各项收费的汇总信息
     */
    protected function setChargeType($chargeAll = null) {
        if ($chargeAll) {
            $chargeTotalDiscount = 0;
            $cardTotalDiscount = 0;
            $chargeId = '';
            foreach ($chargeAll as $v) {
                $chargeType['originalPrice'][] = $v['unit_price'] * $v['num']; //原价
                $chargeType['discountPrice'][] = $v['unit_price'] * $v['num'] - $v['discount_price'] - $v['card_discount_price']; //折后价
                if ($v['type'] == ChargeInfo::$consumablesType) {
                    $v['type'] = ChargeInfo::$materialType;
                }
                $chargeType[$v['type']][] = $v['unit_price'] * $v['num'] - $v['discount_price'] - $v['card_discount_price']; //各类医嘱的已收费的总费用
                $chargeType['discount'][$v['type']][] = $v['discount_price'] + $v['card_discount_price']; //各类医嘱的优惠金额汇总

                $chargeId .= $v['id'] . ',';
                $chargeTotalDiscount += $v['discount_price'];
                $cardTotalDiscount += $v['card_discount_price'];
                if ($v['pay_type'] != 0) {
                    $chargeType['payType'][] = ChargeRecord::$getType[$v['pay_type']];
                }
            }
            $chargeType['fee_remarks'] = $chargeAll[0]['fee_remarks']; //诊金备注
            $chargeType['chargeId'] = $chargeId;
            $chargeType['chargeTotalDiscount'] = $chargeTotalDiscount;
            $chargeType['cardTotalDiscount'] = $cardTotalDiscount;
            $chargeType['payType'] = !empty($chargeType['payType']) ? implode(',', $chargeType['payType']) : '';
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        return $chargeType;
    }

    /**
     * 自己写的退款接口  支付宝
     */
    public function actionRefundMyOrder() {
        $params['out_trade_no'] = '904954960713580';
        $params['total_amount'] = 63;
        $paymentConfig = PaymentConfig::getPaymentConfigList();
        $pConfig = $paymentConfig[2];
        $notifyUrl = Yii::getAlias('@apiPayIndex');
        $res = Order::refundOrder($params, $pConfig, $notifyUrl);
        var_dump($res);
    }

    /**
     * 自己写的退款接口  微信 
     */
    public function actionRefundMyWechat() {
        $out_trade_no = '418034767723347E';
        $refund_no = $out_trade_no;
        $total_fee = 550;
        $total_fee = $total_fee * 100;
        $refund_fee = $total_fee;
//        $res = Order::refundOrder($params, $pConfig, $notifyUrl);
        $res = \app\modules\charge\models\Wechat::refund($refund_no, $out_trade_no, $total_fee, $refund_fee);
        var_dump($res);
    }

    /**
     * @desc 返回对应记录详情的总价格
     * @param integer $id 收费记录id或流水id(type=0为收费记录id)
     * @param array $pks 收费详情记录id数组
     * @param integer $type 
     * @return number[]
     */
    protected function findTotalPrice($id, $pks, $type = 0) {
        $totalPrice = [];
        $query = new Query();
        $query->from(['a' => ChargeRecord::tableName()]);
        $query->select(['a.discount_reason', 'b.unit_price', 'b.num', 'b.discount_price', 'b.card_discount_price']);
        $query->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.charge_record_id');
        $query->where(['a.spot_id' => $this->spotId, 'b.status' => 1, 'b.id' => $pks]);
        if ($type) {
            $query->andWhere(['a.record_id' => $id]);
        } else {
            $query->andWhere(['a.id' => $id]);
        }
        $rows = $query->all();
        foreach ($rows as $v) {
            $totalPrice[] = $v['unit_price'] * intval($v['num']) - $v['discount_price'] - $v['card_discount_price'];
        }
        return $totalPrice;
    }

    /*
     * @desc 退费流程
     * @param id 收费记录id
     * @param model model
     * @param pks 项目列表
     * @return 
     */

    protected function saveRecordInfo($id, $model, $pks) {
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            //查找收费记录信息
            $oldRecord = ChargeRecord::find()->select(['id', 'record_id', 'patient_id', 'type', 'discount_price', 'discount_type', 'discount_reason', 'income'])->where(['id' => $id, 'spot_id' => $this->spotId])->asArray()->one();

            $chargeType = PatientRecord::find()->select(['charge_type'])->where(['id' => $oldRecord['record_id'], 'spot_id' => $this->spotId])->asArray()->one();
            $pks = explode(',', $pks);
            $refundMoney = $this->findTotalPrice($id, $pks);
            $refundMoney = Common::num(array_sum($refundMoney));

            //保存退费记录
            $crModel = new ChargeRecord();
            $crModel->parent_id = $id;
            $crModel->record_id = $oldRecord['record_id'];
            $crModel->patient_id = $oldRecord['patient_id'];
            $crModel->price = $refundMoney;
            $crModel->status = 3;
            $crModel->discount_price = $oldRecord['discount_price'] ? $oldRecord['discount_price'] : '';
            $crModel->discount_type = $oldRecord['discount_type'] ? $oldRecord['discount_type'] : 1;
            $crModel->discount_reason = $oldRecord['discount_reason'] ? $oldRecord['discount_reason'] : '';
            $crModel->type = $oldRecord['type'];
            $crModel->charge_type = $chargeType['charge_type'];
            $crModel->save();
            //更改项目收费状态
            ChargeInfo::updateAll(['charge_record_id' => $crModel->id, 'reason' => $model->reason, 'reason_description' => $model->reason_description, 'status' => 2], ['id' => $pks, 'spot_id' => $this->spotId]);

            $chargeInfoCount = ChargeInfo::find()->where(['charge_record_id' => $id, 'spot_id' => $this->spotId])->count();

            //修改已收费金额
            $chargeRecordModel = ChargeRecord::findOne(['id' => $id, 'spot_id' => $this->spotId]);
            $chargeRecordModel->price = $chargeRecordModel->price - $refundMoney;

            //若该收费记录已全部退完费，则更改状态为 4（已全部退费）
            if ($chargeInfoCount == 0) {
                $chargeRecordModel->status = 4;
            }

            $chargeRecordModel->save();
            $cardFlowId = 0;
            if (in_array($crModel->type,[5,6]) && $oldRecord['income'] != 0) {//若为充值卡退费，则费用原路退回,新增充值卡消费退换货交易流水  5为旧数据  6为新数据
                $cardFlowId = CardFlow::addRefundFlow($oldRecord['id'], $crModel->id, $this->userInfo, $this->parentSpotId, $this->spotId, $refundMoney, $crModel->charge_type);
                if (!$cardFlowId) {
                    $dbTrans->rollBack();
                    return false;
                }
            }else if($crModel->type == 8){//若为套餐卡支付的，则回退套餐卡服务次数
                
               $result = MembershipPackageCardFlow::refundFlow($id);
               if(!$result){
                   $dbTrans->rollBack();
                   return false;
               }
                
            }
            $body = [
                'pks' => $pks
            ];
            $params['body'] = json_encode($body, true);
            $infoData = [
                'oldRecord' => $oldRecord,
                'params' => $params
            ];
            Yii::info('refund saveChargeRecordLog.【' . json_encode($infoData) . '】');
            $chargeRecordLogId = ChargeRecordLog::saveChargeRecordLog($this->spotId, $oldRecord['patient_id'], $oldRecord['record_id'], $params, 2, $oldRecord['type'], 0, 0, $refundMoney, $model->reason, $model->reason_description, $cardFlowId);
            $dbTrans->commit();
            return $chargeRecordLogId;
        } catch (Exception $e) {
            var_dump($e->errorInfo);
            $dbTrans->rollBack();
            return false;
        }
    }

    /**
     * @desc 根据项目列表反查收费记录信息
     * @param 流水id $id
     * @param 项目列表 $pks
     * @return 
     */
    private function getChargeRecordInfo($id, $pks) {
        $query = new Query();
        $query->from(['a' => ChargeRecord::tableName()]);
        $query->leftJoin(['b' => ChargeInfo::tableName()], '{{b}}.charge_record_id = {{a}}.id');
        $query->select(['charge_record_id' => 'a.id', 'pks' => 'group_concat(b.id)', 'a.type']);
        $query->where(['b.record_id' => $id, 'b.id' => $pks, 'a.spot_id' => $this->spotId]);
        $query->groupBy('a.id');
        $query->indexBy('charge_record_id');
        return $query->all();
    }

    /**
     * @param $recordId 流水id
     * @return array 返回该条就诊流水下的所有交易流水
     */
    public function actionViewChargeRecordLog($recordId) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        //获取患者名称以及门诊号
        $query = new Query();
        $query->from(['a' => PatientRecord::tableName()]);
        $query->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id');
        $query->select(['case_id', 'b.username', 'a.charge_type']);
        $query->where(['a.id' => $recordId, 'a.spot_id' => $this->spotId]);
        $userInfo = $query->one();
        //直接收费不显示门诊号
        $caseId = $userInfo['charge_type'] == 1 ? '（' . '门诊号：' . $userInfo['case_id'] . '）' : '';
        //获取该条就诊流水的所有交易记录
        $query = ChargeRecordLog::find()->select(['id', 'username', 'sex', 'age', 'diagnosis_time', 'doctor_name', 'type_description', 'create_time', 'pay_type', 'type', 'price'])->where(['record_id' => $recordId, 'spot_id' => $this->spotId])->orderBy(['create_time' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => false
        ]);
        return [
            'title' => "交易流水一" . $userInfo['username'] . $caseId,
            'content' => $this->renderAjax('_viewChargeRecordLog', [
                'dataProvider' => $dataProvider
            ]),
            'footer' => Html::button('返回', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"])
        ];
    }

    public function checkStockNum($id, $pks) {
        //判断是否有非药品，若是有非药品收费，则验证其数量不能大于总库存量
        $recordChargeType = PatientRecord::find()->select(['charge_type'])->where(['id' => $id, 'spot_id' => $this->spotId])->asArray()->one();
        if ($recordChargeType['charge_type'] == 1) {
            $resultInfo = ChargeInfo::checkMaterialINum($pks);
            $consumablesInfo = ChargeInfo::checkConsumableslINum($pks);
        } else {
            $resultInfo = ChargeInfo::checkMaterialINumSecond($pks);
        }
        if ($resultInfo['errorCode'] != 0 || $consumablesInfo['errorCode'] != 0) {
            return [
                'errorCode' => 1001,
                'errorMessage' => $resultInfo['msg'] ? $resultInfo['msg'] : $consumablesInfo['msg'],
            ];
        }
        return [
            'errorCode' => 0,
            'errorMessage' => '',
        ];
    }
    
    /*
     * 更新服务次数
     * @param $serviceIdArr 选中的服务类型
     * @param $serviceIdArr 服务类型对应的次数
     * @param $packageCardInfoList 该患者套餐卡信息
     * return 
     */
    protected function updateServiceTime($serviceIdArr, $serviceTime, $packageCardInfoList) {
        foreach ($serviceIdArr as $key => $serviceIdList) {
            if (!array_key_exists($key, $packageCardInfoList)) {//不存在的卡id
                return [
                    'message' => '套餐卡不存在',
                    'errorCode' => 1009,
                ];
            } else {
                $msg = '';
                $cardInfo = $packageCardInfoList[$key];
                foreach ($serviceIdList as $serviceId) {
                    if (!$serviceTime[$serviceId]) {
                        $msg = '次数不能为0';
                    } else if (!preg_match("/^\s*[+-]?\d+\s*$/", $serviceTime[$serviceId])) {
                        $msg = '次数不为整数';
                    } else if ($serviceTime[$serviceId] > $cardInfo[$serviceId]['remainTime']) {
                        $msg = '剩余次数不足';
                    }
                    if ($msg) {
                        return [
                            'message' => $msg,
                            'errorCode' => 1009,
                        ];
                    }
                    $PackageCardServiceModel = MembershipPackageCardService::findOne(['id' => $serviceId, 'spot_id' => $this->spotId, 'membership_package_card_id' => $key]);
                    $PackageCardServiceModel->remain_time = $cardInfo[$serviceId]['remainTime'] - $serviceTime[$serviceId];
                    $PackageCardServiceModel->save();
                }
            }
        }
        return [
            'message' => 'success',
            'errorCode' => 0,
        ];
    }
    
    
    
    /*
     * 更新服务次数
     * @param $serviceIdArr 选中的服务类型
     * @param $serviceIdArr 服务类型对应的次数
     * @param $packageCardInfoList 该患者套餐卡信息
     * return 
     */
    protected function updateServiceCardTime($serviceIdArr, $serviceTime, $serviceCardInfoList) {
        foreach ($serviceIdArr as $cardId => $serviceIdList) {
            if (!array_key_exists($cardId, $serviceCardInfoList)) {//不存在的卡id
                return [
                    'message' => '服务卡不存在',
                    'errorCode' => 1009,
                ];
            } else {
                $msg = '';
                $cardInfo = $serviceCardInfoList[$cardId];
                foreach ($serviceTime[$cardId] as $serviceId => $value) {
                    if (empty($value)) {
                        $msg = '次数不能为0';
                    } else if (!preg_match("/^\s*[+-]?\d+\s*$/", $value)) {
                        $msg = '次数不为整数';
                    } else if ($value > $cardInfo['serviceList'][$serviceId]['serviceLeft']) {
                        $msg = '剩余次数不足';
                    }
                    if ($msg) {
                        return [
                            'message' => $msg,
                            'errorCode' => 1009,
                        ];
                    }
                    $ServiceCardServiceModel = CardServiceLeft::findOne(['service_id' => $serviceId, 'card_id' => $cardId]);
                    $ServiceCardServiceModel->service_left = $ServiceCardServiceModel->service_left - $value;
                    $ServiceCardServiceModel->save();
                }
            }
        }
        return [
            'message' => 'success',
            'errorCode' => 0,
        ];
    }
    
    /*
     * 插入会员卡消费流水记录
     * @param $cardTotalPrice 会员卡消费总金额
     * @param $info 患者基本信息
     * @param $model
     * @param $charge_type
     * return 成功返回流水记录id
     */
    protected function insertRechargeRecord($cardTotalPrice,$info,$model,$charge_type) {
        $where = ['f_phone' => $info['iphone'], 'f_physical_id' => $model->cardType, 'f_parent_spot_id' => $this->parentSpotId];
        $cardRechargeInfo = CardRecharge::find()->select(['f_donation_fee', 'f_card_fee', 'f_category_id'])->where($where)->asArray()->one();
        //查看会员卡是否有配置 折扣
        Yii::info('f_category_id:' . $cardRechargeInfo['f_category_id']);
        $cardDiscount = CardDiscountClinic::cardDiscountListClinic($cardRechargeInfo['f_category_id']);
        if (empty($cardDiscount)) {
            return [
                'errorCode' => 1002,
                'message' => '请先设置会员卡的折扣比例',
            ];
        }
        if ($cardTotalPrice > ($cardRechargeInfo['f_card_fee'] + $cardRechargeInfo['f_donation_fee'])) {
            return [
                'errorCode' => 1002,
                'message' => '卡余额不足',
            ];
        }
        if ($cardTotalPrice <= $cardRechargeInfo['f_card_fee']) {//若基本金额大于支付金额，全部使用基本余额
            $removePrice = $cardRechargeInfo['f_card_fee'] - $cardTotalPrice;
            CardRecharge::updateAll(['f_card_fee' => $removePrice], $where);
            $consumDonation = 0;
        } else {
            $donationFee = $cardTotalPrice - $cardRechargeInfo['f_card_fee'];
            $removePrice = $cardRechargeInfo['f_donation_fee'] - $donationFee;
            CardRecharge::updateAll(['f_card_fee' => 0, 'f_donation_fee' => $removePrice], $where);
            $consumDonation = $donationFee;
        }
        $cardFlowModel = new CardFlow();
        $cardFlowModel->f_record_id = $model->cardType;
        $cardFlowModel->f_user_id = $this->userInfo->id;
        $cardFlowModel->f_record_type = 2;
        $cardFlowModel->f_record_fee = $cardTotalPrice - $consumDonation;
        $cardFlowModel->f_pay_type = 0;
        $cardFlowModel->f_operate_origin = $charge_type == 1 ? 1 : 4;
        $cardFlowModel->f_card_fee_beg = $cardRechargeInfo['f_card_fee'] + $cardRechargeInfo['f_donation_fee'];
        $cardFlowModel->f_user_name = $this->userInfo->username;
        $cardFlowModel->f_card_fee_end = $cardRechargeInfo['f_card_fee'] + $cardRechargeInfo['f_donation_fee'] - Yii::$app->request->post('cardTotalPrice');
        $cardFlowModel->f_spot_id = $this->spotId;
        $cardFlowModel->f_update_time = date('Y-m-d H:i', time());
        $cardFlowModel->f_consum_donation = $consumDonation;
        //如果 是首单免诊金
        if ($model->firstDiagnosisFree == 1) {
            $hasFirstFree = FirstOrderFree::check($info['patient_id'], $info['iphone']);
            if ($hasFirstFree) {
                //插入首单免诊金的记录表
                FirstOrderFree::saveData($info['patient_id'], $model->record_id, 0, $model->cardType);
                $cardFlowModel->f_remark = '首单减免诊金';
            }
        }
        $cardFlowModel->save(false);
        Yii::info('cardFlow save error ' . var_export($cardFlowModel->errors, true));
        //会员卡消费成功  发送短信
        CardRecharge::sendMessage($model->cardType, 2, $cardTotalPrice);
        
        return [
                'errorCode' => 0,
                'message' => 'success',
                'flowId' => $cardFlowModel->f_physical_id,
        ]; 
    }
    
    
    /*
     * 验证post数据是否正确
     * @param $model 
     * @param $postData post数据
     * return 
     */
    protected function validatePostData($model,$postData) {
        $serviceCard = isset($postData['serviceCard']) ? $postData['serviceCard'] : '';
        $packageCard = isset($postData['packageCard']) ? $postData['packageCard'] : '';
        if ($model->type == 5) {//充值卡
            $message = '请选择会员卡';
        }
        if ($model->type == 6 && $model->cardType === null && $model->price != 0) {//充值卡
            $message = '无法收费';
        }
        if ($model->type == 7 && empty($serviceCard) && $model->price != 0 && empty($postData['ServiceCardServiceId'])) {//服务卡 没选服务卡，无法收费
            $message = '没选择任何服务，无法收费';
        }
        if ($model->type == 8 && empty($packageCard) && $model->price != 0 && empty($postData['PackageCardServiceId'])) {//套餐卡 没选套餐卡，无法收费
            $message = '没选择任何服务，无法收费';
        }
        
        if(isset($message)){
            return [
                'errorCode' => 1002,
                'message' => $message,
            ];
        }else{
            return [
                'errorCode' => 0,
                'message' => 'success',
            ];
        }
    }
    
    /*
     * 返回服务卡，套餐卡二次弹窗内容
     * @param $model 
     * @param $postData post数据
     * return 
     */
    protected function showTipView($model,$postData) {
        $serviceCard = isset($postData['serviceCard']) ? $postData['serviceCard'] : '';
        $packageCard = isset($postData['packageCard']) ? $postData['packageCard'] : '';
        $data = '';

        
        if ($model->type == 7 && empty($serviceCard) && $model->price != 0) {//套餐卡
            $data = [
                'title' => "收费-服务卡支付",
                'content' => $this->renderAjax('_serviceCardForm', [
                    'model' => $model,
                    'type' => $model->type,
                    'outTradeNo' => $model->out_trade_no,
                    'price' => $model->price,
                    'allPrice' => $model->allPrice,
                    'serviceIdArr' => $postData['ServiceCardServiceId'], //选中的服务
                    'serviceTime' => $postData['ServiceCardServiceTime'], //扣减的次数
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-card btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                Html::button('确定', ['class' => 'btn btn-card btn-default btn-form create-rebate', 'type' => "submit"])
            ];
        }
        
        if ($model->type == 8 && empty($packageCard) && $model->price != 0) {//套餐卡
            $data = [
                'title' => "收费-套餐卡支付",
                'content' => $this->renderAjax('_packageCardForm', [
                    'model' => $model,
                    'type' => $model->type,
                    'outTradeNo' => $model->out_trade_no,
                    'price' => $model->price,
                    'allPrice' => $model->allPrice,
                    'serviceIdArr' => $postData['PackageCardServiceId'], //选中的服务
                    'serviceTime' => $postData['PackageCardServiceTime'], //扣减的次数
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-card btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                Html::button('确定', ['class' => 'btn btn-card btn-default btn-form create-rebate', 'type' => "submit"])
            ];
        }
        return $data;
    }
    
    /*
     * 返回收费弹窗
     * @param $model
     * @param $cardInfo 充值卡信息
     * @param $packageCardInfoList 套餐卡信息
     * @param $subject
     * @param $info 患者信息
     * @param $serviceCardInfoList 服务卡信息
     * return 
     */
    protected function showRechargeView($model,$cardInfo,$packageCardInfoList,$subject,$info,$serviceCardInfoList) {
        $pks = explode(',', $model->pks); // Array or selected records primary keys
        $rows = ChargeInfo::find()->select(['id', 'name', 'unit_price', 'num', 'discount_price', 'type'])->where(['id' => $pks, 'record_id' => $model->record_id, 'spot_id' => $this->spotId, 'status' => 0])->asArray()->all();
        $typeArr = [];
        if (!empty($rows)) {//若有没收费的记录，则走流程支付，否则提示 订单已收费
            $totalPrice[] = 0;
            foreach ($rows as $v) {
                $totalPrice[] = ($v['unit_price'] * intval($v['num'])) - $v['discount_price'];
                $typeArr[] = $v['type'];
            }
            $model->price = Common::num(array_sum($totalPrice));
        } else {
            return [
                'errorCode' => 1003,
                'message' => '该订单已收费',
                ];
        }

        $allPrice = $model->price; //应收费用
        $model->allPrice = $allPrice;

        $aliPayUrl = '';
        $wechatUrl = '';
        $outTradeNo = '';
        $type = [];
        //生成支付订单
        $outTradeNo = Order::generateOutTradeNo(); //订单号
        $model->out_trade_no = $outTradeNo;
        //$model->price=0.01;//测试先弄一分钱
        //将未支付订单更改状态为过期
        Order::updateAll(['status' => 5], ['record_id' => $model->record_id, 'spot_id' => $this->spotId, 'status' => [1, 3]]);
        //生成订单记录
        Order::addOrder($model->record_id, $this->userInfo->id, $outTradeNo, $subject, $model->price, 0);
        if ($allPrice != 0) {//若订单待收费总金额为0.则不显示支付方式(包括支付宝，微信)，实收现金
            $type = ChargeRecord::$getType; //支付类型
            $paymentConfig = PaymentConfig::getPaymentConfigList();
            if (!empty($paymentConfig)) {//若支付配置有设置
                if (!isset($paymentConfig[2])) {//若设置了支付宝配置，则生成支付宝订单二维码
                    unset($type[4]);
                }
                if (!isset($paymentConfig[1])) {//微信支付
                    unset($type[3]);
                }
            } else {
                unset($type[3]);
                unset($type[4]);
            }
            unset($type[6]);
            unset($type[7]);
            unset($type[8]);
        }
        !$model->type && $model->type = 1;
        $model->scanMode = 1;
        if ($model->cash == '0.00') {
            $model->cash = '';
        }
        //获取用户是否   具有首单免诊金的机会
        $firstFreeChance = 2;
        if (in_array(5, $typeArr)) {
            $hasFirstFree = FirstOrderFree::check($info['patient_id'], $info['iphone']);
            if ($hasFirstFree) {
                $firstFreeChance = 1;
            }
        }
        $data = [
            'title' => "收费",
            'content' => $this->renderAjax('_be_charge_form', [
                'model' => $model,
                'type' => $type,
                'info' => $info, //患者姓名
                'outTradeNo' => $outTradeNo,
                'aliPayUrl' => $aliPayUrl, //支付宝二维码url
                'wechatUrl' => $wechatUrl, //微信二维码url
                'readonly' => $model->readonly,
                'allPrice' => $allPrice,
                'cardInfo' => $cardInfo[$info['iphone']],
                'firstFreeChance' => $firstFreeChance,
                'packageCardInfoList' => $packageCardInfoList,
                'serviceCardInfoList' => $serviceCardInfoList,
            ]),
            'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
            Html::button('打印费用信息', ['class' => 'btn btn-cancel btn-form print-info']) .
            Html::button('确认收费', ['class' => 'btn btn-default btn-form create-rebate', 'type' => "submit"]),
        ];
        return [
            'errorCode' => 0,
            'message' => 'success',
            'data' => $data,
        ];
        
    }

}
