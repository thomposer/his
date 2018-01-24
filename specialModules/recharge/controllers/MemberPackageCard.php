<?php

/**
 *
 * @author zhangzhenyu
 * @desc 会员卡--套餐卡模块
 */

namespace app\specialModules\recharge\controllers;

use app\specialModules\recharge\models\MembershipPackageCard;
use app\specialModules\recharge\models\MembershipPackageCardUnion;
use app\specialModules\recharge\models\search\MembershipPackageCardSearch;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\NotAcceptableHttpException;
use app\common\base\MultiModel;
use yii\helpers\Html;
use app\modules\patient\models\Patient;
use app\modules\spot\models\PackageCard;
use app\specialModules\recharge\models\CardOrder;
use app\modules\charge\models\Order;
use app\modules\charge\models\ChargeRecord;
use app\modules\spot_set\models\PaymentConfig;
use app\specialModules\recharge\models\PackagePaymentLog;
use app\specialModules\recharge\models\MembershipPackageCardService;
use app\modules\spot\models\PackageCardService;
use yii\log\Logger;
use yii\db\Exception;
use app\specialModules\recharge\models\CardRecharge;
use app\modules\spot_set\models\CardDiscountClinic;
use app\specialModules\recharge\models\CardFlow;
use app\specialModules\recharge\models\search\MembershipPackageCardFlowSearch;
use app\specialModules\recharge\models\MembershipPackageCardFlow;
use yii\helpers\Url;

trait MemberPackageCard
{

    /**
     * Lists all PackageCard.
     * @return mixed
     */
    public function actionPackageCard() {
        $searchModel = new MembershipPackageCardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        //获取机构下所有套餐卡
        $packageCardList = PackageCard::getPackageCardList(['id', 'name']);
        return $this->render('/package-card/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'packageCardList' => $packageCardList,
        ]);
    }

    /**
     * @param $id 会员卡下套餐卡id
     * @return array
     * @throws NotFoundHttpException
     * @desc 套餐卡的启用停用
     */
    public function actionPackageCardDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $model = $this->findMembershipPackageCardModel($id);
            $model->status = $model->status == 1 ? 2 : 1;
            $model->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['index']);
        }
    }

    /**
     * @param $id 会员卡下套餐卡id
     * @return static
     * @throws NotFoundHttpException
     */
    protected function findMembershipPackageCardModel($id) {
        if (($model = MembershipPackageCard::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    public function actionCreatePackageCard($step = 1) {

        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isAjax) {
            $model = new MultiModel([
                'models' => [
                    'membershipPackageCard' => new MembershipPackageCard(),
                    'union' => new MembershipPackageCardUnion(),
                ]
            ]);
            if ($model->load($request->post()) && $model->validate() && $step == 1) {
                $orderModel = new CardOrder();
                return $this->renderPayment($model, $orderModel);
            }if ($step == 2) {
                //微信扫码枪支付
                $orderModel = CardOrder::findOne(['out_trade_no' => $request->post()['CardOrder']['out_trade_no'], 'spot_id' => $this->spotId]);
                if ($model->load($request->post()) && $orderModel->load($request->post()) && $orderModel->validate()) {
                    if (($orderModel->type == 3 && $orderModel->scanMode == 2) || ($orderModel->type == 4 && $orderModel->scanMode == 2)) {
                        $message = '请选择正常的支付类型';
                        return [
                            'forceClose' => true,
                            'forceType' => 2,
                            'forceMessage' => $message
                        ];
                    }
                    $dbTrans = Yii::$app->db->beginTransaction();
                    try {
                        if ($orderModel->total_amount == 0) {//若支付总金额为0，则支付方式一致为 无 。
                            $orderModel->type = 0;
                        }
                        if ($orderModel->type == 2 || $orderModel->type == 6) {//
                            $orderModel->income = $orderModel->total_amount;
                            $change = 0.00;
                        } else if ($orderModel->type == 1) {
                            $change = $orderModel->income - $orderModel->total_amount; //找零
                        } else if ($orderModel->type == 3 || $orderModel->type == 4) {//微信或支付宝  扫描枪支付
                            $scpRes = PackagePaymentLog::scannerPaymentCard($orderModel);
                            Yii::info($scpRes, 'chargeCreate');
                            if (!$scpRes || $scpRes['errorCode'] != 0) {
                                $dbTrans->rollBack();
                                throw new Exception('PaymentLog::scannerPayment Exception ');
                            } else {
                                $dbTrans->commit();
                                return [
                                    'forceReload' => '#crud-datatable-pjax',
                                    'forceClose' => true,
                                    'forceType' => 1,
                                    'forceMessage' => '保存成功',
                                    'forceCallback' => 'window.clearInterval(main.oderStatus)'
                                ];
                            }
                        } else if ($orderModel->type == 5) {

                            $where = ['f_physical_id' => $orderModel->cardType, 'f_parent_spot_id' => $this->parentSpotId];
                            $cardRechargeInfo = CardRecharge::find()->select(['f_donation_fee', 'f_card_fee', 'f_category_id'])->where($where)->asArray()->one();
                            //查看会员卡是否有配置 折扣
                            Yii::info('f_category_id:' . $cardRechargeInfo['f_category_id']);
                            $cardDiscount = CardDiscountClinic::cardDiscountListClinic($cardRechargeInfo['f_category_id']);
                            if (empty($cardDiscount)) {
                                return [
                                    'forceClose' => true,
                                    'forceType' => 2,
                                    'forceMessage' => '请先设置会员卡的折扣比例'
                                ];
                            }
                            if ($orderModel->total_amount > ($cardRechargeInfo['f_card_fee'] + $cardRechargeInfo['f_donation_fee'])) {
                                $message = '卡余额不足';
                                return [
                                    'forceClose' => true,
                                    'forceType' => 2,
                                    'forceMessage' => $message
                                ];
                            }
                            if ($orderModel->total_amount <= $cardRechargeInfo['f_card_fee']) {//若基本金额大于支付金额，全部使用基本余额
                                $removePrice = $cardRechargeInfo['f_card_fee'] - $orderModel->total_amount;
                                CardRecharge::updateAll(['f_card_fee' => $removePrice], $where);
                                $consumDonation = 0;
                            } else {
                                $donationFee = $orderModel->total_amount - $cardRechargeInfo['f_card_fee'];
                                $removePrice = $cardRechargeInfo['f_donation_fee'] - $donationFee;
                                CardRecharge::updateAll(['f_card_fee' => 0, 'f_donation_fee' => $removePrice], $where);
                                $consumDonation = $donationFee;
                            }
                            $cardFlowModel = new CardFlow();
                            $cardFlowModel->f_record_id = $orderModel->cardType;
                            $cardFlowModel->f_user_id = $this->userInfo->id;
                            $cardFlowModel->f_record_type = 2;
                            $cardFlowModel->f_record_fee = $orderModel->total_amount - $consumDonation;
                            $cardFlowModel->f_pay_type = 0;
                            $cardFlowModel->f_operate_origin = 5;
                            $cardFlowModel->f_flow_item = '套餐卡购买';
                            $cardFlowModel->f_card_fee_beg = $cardRechargeInfo['f_card_fee'] + $cardRechargeInfo['f_donation_fee'];
                            $cardFlowModel->f_user_name = $this->userInfo->username;
                            $cardFlowModel->f_card_fee_end = $cardRechargeInfo['f_card_fee'] + $cardRechargeInfo['f_donation_fee'] - $orderModel->total_amount;
                            $cardFlowModel->f_spot_id = $this->spotId;
                            $cardFlowModel->f_update_time = date('Y-m-d H:i', time());
                            $cardFlowModel->f_consum_donation = $consumDonation;
                            $cardFlowModel->save(false);
                            //会员卡消费成功  发送短信
                            CardRecharge::sendMessage($orderModel->cardType, 2, $orderModel->total_amount);
                        }
                        $cardCacheKey = Yii::getAlias('@wxPayPackageCardItem') . $orderModel->out_trade_no;
                        $result = CardOrder::saveOrder($orderModel->out_trade_no, json_decode(Yii::$app->cache->get($cardCacheKey), true), $orderModel->type, $orderModel->income, $orderModel->remark);
                        $dbTrans->commit();
                        return [
                            'forceReload' => '#crud-datatable-pjax',
                            'forceClose' => true,
                            'forceType' => 1,
                            'forceMessage' => '操作成功',
                            'forceCallback' => 'window.clearInterval(main.oderStatus)'
                        ];
                    } catch (\yii\db\Exception $e) {

                        return $this->renderPayment($model, $orderModel, true);
                    }
                } else {
                    return $this->renderPayment($model, $orderModel);
                }
            } else {
                $cardList = PackageCard::getNormalPackageCardList();
                $outTradeNo = $request->get('out_trade_no');
                if ($outTradeNo) {
                    $cardCacheKey = Yii::getAlias('@wxPayPackageCardItem') . $outTradeNo;
                    $info = json_decode(Yii::$app->cache->get($cardCacheKey), true);
                    $model->getModel('membershipPackageCard')->package_card_id = $info['package_card_id'];
                    $model->getModel('membershipPackageCard')->status = $info['status'];
                    $model->getModel('membershipPackageCard')->remark = $info['remark'];
                    $model->getModel('union')->patient_id = $info['patient_id'];
                    $patientInfo = Patient::find()->select(['id', 'username', 'iphone', 'sex', 'birthday'])->where(['id' => $info['patient_id'], 'spot_id' => $this->parentSpotId])->asArray()->one();
                    $model->getModel('union')->iphone = $patientInfo['iphone'];
                    $model->getModel('union')->patientInfo = $patientInfo['username'] . '（' . Patient::dateDiffage($patientInfo['birthday'], time()) . Patient::$getSex[$patientInfo['birthday']] . '）';
                }
                return [
                    'title' => "套餐卡",
                    'content' => $this->renderAjax('/package-card/create', [
                        'model' => $model,
                        'cardList' => $cardList,
                        'outTradeNo' => $outTradeNo
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::submitButton('下一步', ['class' => 'btn btn-default btn-form '])
                ];
            }
        } else {

            throw new NotAcceptableHttpException('非法操作');
        }
    }

    protected function renderPayment($model, $orderModel, $error = false) {
        $aliPayUrl = '';
        $wechatUrl = '';
        $outTradeNo = '';
        $type = [];
        //生成支付订单
        $info = PackageCard::getInfo($model->getModel('membershipPackageCard')->package_card_id, ['id', 'price']);
        $outTradeNo = Order::generateOutTradeNo(); //订单号
        $orderModel->out_trade_no = $outTradeNo;
        $orderModel->total_amount = $info['price'];
        //$model->price=0.01;//测试先弄一分钱
        //生成订单记录
        $subject = '套餐卡支付';
        CardOrder::addOrder(0, $this->userInfo->id, $outTradeNo, $subject, $info['price'], 0);
        $rows['package_card_id'] = $model->getModel('membershipPackageCard')->package_card_id;
        $rows['status'] = $model->getModel('membershipPackageCard')->status;
        $rows['remark'] = $model->getModel('membershipPackageCard')->remark;
        $rows['patient_id'] = $model->getModel('union')->patient_id;
        $rows['spotId'] = $this->spotId;
        $rows['parentSpotId'] = $this->parentSpotId;
        $cardCacheKey = Yii::getAlias('@wxPayPackageCardItem') . $outTradeNo;
        Yii::$app->cache->set($cardCacheKey, json_encode($rows), 86400);
        if ($info['price'] != 0) {//若订单待收费总金额为0.则不显示支付方式(包括支付宝，微信)，实收现金
            $type = CardOrder::$getType; //支付类型
            $paymentConfig = PaymentConfig::getPaymentConfigList();
            if (!empty($paymentConfig)) {//若支付配置有设置
                if (!isset($paymentConfig[2])) {//若设置了支付宝配置，则生成支付宝订单二维码
                    unset($type[4]);
                } else {
                    $qrCode = CardOrder::generateQrCode($outTradeNo, $subject, $info['price'], $paymentConfig[2], $rows);
                    Yii::$app->log->logger->log($qrCode, Logger::LEVEL_INFO);
                    if ($qrCode['errorCode'] == 0 && isset($qrCode['codeUrl'])) {
                        $aliPayUrl = $qrCode['codeUrl'];
                    }
                }
                if (!isset($paymentConfig[1])) {//微信支付
                    unset($type[3]);
                } else {
                    $qrCode = CardOrder::generateWechatQrCode($outTradeNo, $subject, $info['price'], $rows);
                    Yii::$app->log->logger->log($qrCode, Logger::LEVEL_INFO);
                    if ($qrCode) {
                        $wechatUrl = $qrCode;
                    }
                }
            } else {
                unset($type[3]);
                unset($type[4]);
            }
        }
        !$orderModel->type && $orderModel->type = 1;
        $orderModel->scanMode = 1;
        if ($orderModel->cash == '0.00') {
            $orderModel->cash = '';
        }
        $patientInfo = Patient::patientInfo(['iphone'], ['id' => $rows['patient_id']]);
        //获取用户会员卡信息
        $cardInfo = CardRecharge::getCardInfo(null, $patientInfo['iphone']);
        if ($error) {
            $orderModel->wechatAuthCode = '';
            $orderModel->alipayAuthCode = '';
        }
        $return = [
            'title' => "套餐卡",
            'content' => $this->renderAjax('/package-card/_payment', [
                'model' => $model,
                'orderModel' => $orderModel,
                'type' => $type,
                'aliPayUrl' => $aliPayUrl,
                'wechatUrl' => $wechatUrl,
                'cardInfo' => $cardInfo[$patientInfo['iphone']]
            ]),
            'footer' => Html::a('上一步', ['create-package-card', 'step' => 1, 'out_trade_no' => $orderModel->out_trade_no], ['class' => 'btn btn-cancel btn-form', 'role' => 'modal-remote', 'data-toggle' => 'tooltip', 'data-pjax' => 0]) .
            Html::submitButton('确定', ['class' => 'btn btn-default btn-form  create-rebate'])
        ];
        if ($error) {
            $return['forceType'] = 2;
            $return['forceMessage'] = '支付失败,请重新支付';
        }
        return $return;
    }

    /**
     * @param $id 套餐卡id
     * @return mixed
     * @throws NotFoundHttpException
     * @desc 套餐卡查看
     */
    public function actionPackageCardView($id) {
        $membershipCardModel = $this->findPackageCardModel($id);
        $membershipCardUnionModel = $this->findPackageCardUnionModel($id);
        $cardList = PackageCard::getPackageCardList(['id', 'name', 'price', 'validity_period', 'content']);
        $vidateTime = $this->getPackageCardValidateTime($membershipCardModel->buyTime, $membershipCardModel->validityTime);
        $model = new MultiModel([
            'models' => [
                'membershipPackageCard' => $membershipCardModel,
                'union' => $membershipCardUnionModel
            ]
        ]);
        return $this->render('/package-card/update', [
                    'model' => $model,
                    'cardList' => $cardList,
                    'vidateTime' => $vidateTime,
                    'canPatientCreate' => false, //是否显示新增患者提示
        ]);
    }

    /**
     * @param $id 套餐卡id
     * @return mixed
     * @throws NotFoundHttpException
     * @desc 套餐卡查看
     */
    public function actionPackageCardUpdate($id) {
        $request = Yii::$app->request;
        $membershipCardModel = $this->findPackageCardModel($id);
        $membershipCardUnionModel = $this->findPackageCardUnionModel($id);
        $vidateTime = $this->getPackageCardValidateTime($membershipCardModel->buyTime, $membershipCardModel->validityTime);
        $cardList = PackageCard::getPackageCardList(['id', 'name', 'price', 'validity_period', 'content']);
        $model = new MultiModel([
            'models' => [
                'membershipPackageCard' => $membershipCardModel,
                'union' => $membershipCardUnionModel
            ]
        ]);
        if ($request->isAjax) {
            $model->getModel('union')->scenario = 'update';
            $model->getModel('membershipPackageCard')->scenario = 'update';
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return [
                    'forceReloadPage' => true,
                    'forceClose' => true
                ];
            } else {
                $ret = [
                    'title' => "编辑套餐卡",
                    'content' => $this->renderAjax('/package-card/create', [
                        'model' => $model,
                        'cardList' => $cardList,
                        'vidateTime' => $vidateTime
                    ]),
                    'footer' =>
                    Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确定', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
                return $ret;
            }
        }
    }

    /**
     * @param $id 会员卡下套餐卡id
     * @return static
     * @throws NotFoundHttpException
     */
    protected function findMembershipPackageCardUnionModel($id) {
        if (($model = MembershipPackageCardUnion::findOne(['membership_package_card_id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @param $id 会员卡下套餐卡id
     * @return static
     * @throws NotFoundHttpException
     */
    protected function findPackageCardModel($id) {
        $query = New ActiveQuery(MembershipPackageCard::className());
        $query->from(['a' => MembershipPackageCard::tableName()]);
        $query->select(['a.id', 'a.package_card_id', 'a.status', 'a.remark', 'buyTime' => 'a.create_time', 'd.price', 'd.name', 'active_time' => 'd.create_time', 'validityTime' => 'd.validity_period', 'd.content']);
        $query->leftJoin(['b' => MembershipPackageCardUnion::tableName()], '{{a}}.id = {{b}}.membership_package_card_id');
        $query->leftJoin(['c' => Patient::tableName()], '{{b}}.patient_id = {{c}}.id');
        $query->leftJoin(['d' => PackageCard::tableName()], '{{a}}.package_card_id = {{d}}.id');
        $query->where(['a.id' => $id, 'a.spot_id' => $this->spotId]);
        $model = $query->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @param $id 会员卡下套餐卡id
     * @return static
     * @throws NotFoundHttpException
     */
    protected function findPackageCardUnionModel($id) {
        $query = New ActiveQuery(MembershipPackageCardUnion::className());
        $query->from(['a' => MembershipPackageCardUnion::tableName()]);
        $query->select(['a.id', 'patient_id' => 'c.id', 'c.username', 'c.sex', 'c.birthday', 'c.iphone']);
        $query->leftJoin(['c' => Patient::tableName()], '{{a}}.patient_id = {{c}}.id');
        $query->where(['a.membership_package_card_id' => $id, 'a.spot_id' => $this->spotId]);
        $model = $query->one();
        $birth = Patient::dateDiffage($model->birthday, time());
        $text = Html::encode($model->username) . '( ' . Patient::$getSex[$model->sex] . ' ' . $birth . ' )';
        $model->patientInfo = $text;
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @param $id 套餐卡id
     * @return mixed
     * @desc 套餐卡流水
     */
    public function actionPackageCardFlow($id) {
        $query = new Query();
        $query->from(['a' => MembershipPackageCardService::tableName()]);
        $query->select(['a.total_time', 'a.remain_time', 'b.name']);
        $query->leftJoin(['b' => PackageCardService::tableName()], '{{a}}.package_card_service_id = {{b}}.id');
        $query->where(['a.membership_package_card_id' => $id, 'a.spot_id' => $this->spotId]);
        $data = $query->all();
        $params = Yii::$app->request->queryParams;
        $params['membership_package_card_id'] = $id;
        $searchModel = new MembershipPackageCardFlowSearch();
        $dataProvider = $searchModel->search($params, $this->pageSize);
        $followIds = $dataProvider->getKeys();
        $transDetail = MembershipPackageCardFlowSearch::getTransDetail($followIds);
        return $this->render('/package-card/flow', [
                    'data' => $data,
                    'id' => $id,
                    'spotId' => $this->spotId,
                    'dataProvider' => $dataProvider,
                    'transDetail' => $transDetail,
        ]);
    }

    /**
     * @param $createTime 卡的购买时间
     * @param $period 卡的过期年限
     * @return int 返回卡的过期时间
     * @desc  //获取卡的过期时间
     */
    public function getPackageCardValidateTime($createTime, $period) {
        $year = date('Y', $createTime);
        $time = date('m-d H:i:s', $createTime);
        $vidateTime = strtotime(($year + $period) . '-' . $time);
        return $vidateTime;
    }

    /**
     * @param $id 套餐卡id
     * @return mixed
     * @desc 套餐卡流水
     */
    public function actionPackageRecord($id) {
        $request = Yii::$app->request;
        $cardBasic = MembershipPackageCard::getCardBasicInfo($id);
        if ($request->isAjax && $cardBasic) {
            $model = new MembershipPackageCardFlow();
            $model->scenario = 'record';
            $model->membership_package_card_id = $id;
            Yii::$app->response->format = Response::FORMAT_JSON;
            $serviceIdList = $request->post()['PackageCardServiceId']; //选中的服务
            $serviceTimeList = $request->post()['PackageCardServiceTime']; //扣减的次数
            $packageCard = isset($request->post()['packageCard']) ? $request->post()['packageCard'] : '';
            if ($model->load($request->post()) && $model->validate()) {
                if (empty($serviceIdList)) {//明细为空
                    $ret = [
                        'title' => "手动登记",
                        'content' => $this->renderAjax('/package-card/_record', [
                            'model' => $model,
                            'cardBasic' => $cardBasic,
                            'serviceIdList' => $serviceIdList ? $serviceIdList : [],
                            'serviceTimeList' => $serviceTimeList ? $serviceTimeList : [],
                        ]),
                        'footer' =>
                        Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                        Html::button('确定', ['class' => 'btn btn-default btn-form card-check-btn', 'type' => "submit"]),
                        'forceType' => 2,
                        'forceMessage' => '必须选择一项',
                    ];
                    return $ret;
                }
                if (empty($packageCard)) {
                    return [
                        'title' => "系统提示",
                        'content' => $this->renderAjax('/package-card/_packageCardForm', [
                            'model' => $model,
                            'serviceIdList' => $serviceIdList, //选中的服务
                            'serviceTimeList' => $serviceTimeList, //扣减的次数
                        ]),
                        'footer' => Html::button('取消', ['class' => 'btn btn-card btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                        Html::button('确定', ['class' => 'btn btn-card btn-default btn-form create-rebate', 'type' => "submit"])
                    ];
                }
                $ret = $this->updateServiceTime($serviceIdList, $serviceTimeList, $cardBasic, $model);
                if ($ret['errorCode'] != 0) {
                    return [
                        'forceClose' => true,
                        'forceMessage' => $ret['message'],
                        'forceType' => 2,
                    ];
                } else {
                    return [
                        'forceClose' => true,
                        'forceMessage' => '保存成功',
                        'forceRedirect' => Url::to(['package-card-flow', 'id' => $id, 'type' => 2]),
                    ];
                }
            } else {
                $ret = [
                    'title' => "手动登记",
                    'content' => $this->renderAjax('/package-card/_record', [
                        'model' => $model,
                        'cardBasic' => $cardBasic,
                        'serviceIdList' => $serviceIdList ? $serviceIdList : [],
                        'serviceTimeList' => $serviceTimeList ? $serviceTimeList : [],
                    ]),
                    'footer' =>
                    Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确定', ['class' => 'btn btn-default btn-form card-check-btn', 'type' => "submit"]),
                ];
                return $ret;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * 扣减或增加服务次数
     * @param $serviceIdList 勾选的服务
     * @param $serviceTimeList 服务对应的次数
     * @param $cardBasic 原卡片的信息
     * @param $model
     */

    protected function updateServiceTime($serviceIdList, $serviceTimeList, $cardBasic, $model) {
        $db = Yii::$app->db;
        $dbTrans = $db->beginTransaction();
        try {
            if (empty($serviceIdList)) {
                $msg = '必须选择一项';
                return ['message' => $msg, 'errorCode' => 1009];
            }
            foreach ($serviceIdList as $serviceId) {
                if (empty($serviceTimeList[$serviceId])) {
                    $msg = '次数不能为0或为空';
                } else if (!preg_match("/^\s*[+-]?\d+\s*$/", $serviceTimeList[$serviceId])) {
                    $msg = '次数不为整数';
                } else if ($model->transaction_type == 1 && $serviceTimeList[$serviceId] > $cardBasic[$serviceId]['remainTime']) {
                    $msg = '剩余次数不足';
                } else if ($model->transaction_type == 3 && $serviceTimeList[$serviceId] > 999) {
                    $msg = '增加次数超过999';
                }
                if ($msg) {
                    $dbTrans->rollBack();
                    return ['message' => $msg, 'errorCode' => 1009];
                }
                if ($model->transaction_type == 1) {//消费
                    $db->createCommand()
                            ->update(MembershipPackageCardService::tableName(), ['remain_time' => $cardBasic[$serviceId]['remainTime'] - $serviceTimeList[$serviceId], 'update_time' => time()], ['spot_id' => $this->spotId, 'membership_package_card_id' => $model->membership_package_card_id, 'id' => $serviceId])
                            ->execute();
                } else if ($model->transaction_type == 3) {//消费退还
                    $db->createCommand()
                            ->update(MembershipPackageCardService::tableName(), ['remain_time' => $cardBasic[$serviceId]['remainTime'] + $serviceTimeList[$serviceId], 'update_time' => time()], ['spot_id' => $this->spotId, 'membership_package_card_id' => $model->membership_package_card_id, 'id' => $serviceId])
                            ->execute();
                }

                $packageCardParamsService[] = [
                    'package_card_service_id' => $cardBasic[$serviceId]['package_card_service_id'],
                    'time' => $serviceTimeList[$serviceId],
                ];
            }

            $packageCardParams[0]['membership_package_card_id'] = $model->membership_package_card_id;
            $packageCardParams[0]['service'] = $packageCardParamsService;
            //流水
            $query = new Query();
            $query->from(['a' => MembershipPackageCardUnion::tableName()]);
            $query->select(['a.patient_id']);
            $query->where(['a.membership_package_card_id' => $model->membership_package_card_id, 'a.spot_id' => $this->spotId]);
            $patientId = $query->one()['patient_id'];
            $ret = MembershipPackageCardFlow::addFlow($this->spotId, $patientId, '手动登记-' . MembershipPackageCardFlow::$getTransactionType[$model->transaction_type], $model->transaction_type, 0, 2, $this->userInfo->id, $packageCardParams, 0, 0, 0, $model->remark);
            if (!$ret) {
                $dbTrans->rollBack();
                return ['message' => '保存流水失败', 'errorCode' => 1009];
            }
            $dbTrans->commit();
            return ['message' => '保存成功', 'errorCode' => 0];
        } catch (Exception $e) {
            $dbTrans->rollBack();
            Yii::error($e->errorInfo, 'MembershipPackageCard-updateServiceTime');
            return ['message' => '保存失败', 'errorCode' => 1002];
        }
    }

}
