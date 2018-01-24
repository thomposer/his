<?php

namespace app\specialModules\recharge\controllers;

use Yii;
use yii\db\Query;
use yii\web\Response;
use app\specialModules\recharge\models\CardRecharge;
use app\specialModules\recharge\models\CardHistory;
use app\specialModules\recharge\models\search\CardRechargeSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\specialModules\recharge\models\search\CardFlowSearch;
use app\modules\user\models\User;
use PHPExcel;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Style_NumberFormat;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use app\common\Common;
use app\specialModules\recharge\models\CardFlow;
use yii\helpers\Html;
use app\modules\spot\models\CardRechargeCategory;
use app\specialModules\recharge\models\search\CategoryHistorySearch;
use app\specialModules\recharge\models\CategoryHistory;
use yii\db\Exception;
use app\specialModules\recharge\models\search\UserCardSearch;
use app\specialModules\recharge\models\UserCard;
use yii\helpers\Url;
use app\specialModules\recharge\models\ServiceConfig;
use app\specialModules\recharge\models\CardServiceLeft;
use app\specialModules\recharge\models\Order;
use app\modules\spot_set\models\PaymentConfig;
use app\specialModules\recharge\models\PaymentLog;
use app\modules\charge\models\ChargeInfo;

/**
 * IndexController implements the CRUD actions for CardRecharge model.
 */
class IndexController extends BaseController
{
   
    use MemberPackageCard;
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'subscribe' => ['post']
                ],
            ],
        ];
    }

    /**
     * Lists all CardRecharge models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CardRechargeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CardRecharge model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CardRecharge model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new CardRecharge();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $submitType = Yii::$app->request->post('CardRecharge')['submitType'];
//            $model->f_buy_time = strtotime($model->f_buy_time);
            $model->f_update_time = date('Y-m-d H:i:s'); //本地测试用
            $model->save(false);
            $cardCategoryHistoryModel = new CategoryHistory();
            $userModel = Yii::$app->user->identity;
            $recordId = $model->f_physical_id;
//            $cardModel = CardRecharge::findModel($recordId);
            $cardCategoryHistoryModel->f_record_id = $recordId;
            $cardCategoryHistoryModel->f_end_category = $model->f_category_id;
            $cardCategoryHistoryModel->f_user_id = $userModel->id;
            $cardCategoryHistoryModel->f_user_name = $userModel->username;
            $cardCategoryHistoryModel->f_change_reason = '新增卡片';
            $cardCategoryHistoryModel->save();

            if ($submitType == 1) {//普通新建
                return [
                    'forceReloadPage' => true,
                    'forceClose' => true
                ];
            } else {
                $recordId = $model->f_physical_id;
                $model = new Order();
                $model->record_id = $recordId;
                $cardBasic = CardRecharge::getCardInfo($recordId);
                $ret = [
                    'title' => "充值",
                    'content' => $this->renderAjax('/recharge/_recharge', [
                        'model' => $model,
                        'cardBasic' => $cardBasic
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('下一步', ['class' => 'btn btn-default btn-form card-check-btn', 'type' => "submit"])
                ];
                return $ret;
            }
        } else {
            $sales = CardRecharge::getSales();
            if ($model->f_buy_time != null && is_int($model->f_buy_time) && !$model->errors) {
                $model->f_buy_time = date('Y-m-d H:i', $model->f_buy_time);
            } else if (!$model->f_buy_time) {
                $model->f_buy_time = date('Y-m-d H:i', time());
            }
            $cardCategory = CardRechargeCategory::getCardCategory();
            $text = '';
            if (isset(Yii::$app->view->params['permList']['role']) || in_array(Yii::$app->view->params['requestModuleController'] . '/recharge', Yii::$app->view->params['permList'])) {
                if (0 == $cardBasic['f_is_logout']) {
                    $text = Html::button('新建并充值', ['class' => 'btn btn-default btn-form btn-create-card-flow', 'type' => "botton"]);
                }
            }
            $ret = [
                'title' => "新建卡片",
                'content' => $this->renderAjax('_information', [
                    'model' => $model,
                    'cardCategory' => $cardCategory,
                    'sales' => $sales
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                Html::button('新建', ['class' => 'btn btn-default btn-form btn-card-group btn-create-card', 'type' => "submit"]) .
                $text
            ];
            return $ret;
        }
    }

    /**
     * Updates an existing CardRecharge model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $beg_info = $model->toArray();
        $request = Yii::$app->request;

        if ($request->isAjax) {
            $model->scenario = 'update';
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->f_update_time = date('Y-m-d H:i:s'); //本地测试用
                $model->save();
                $userModel = Yii::$app->user->identity;
                $end_info = $this->findModel($id)->toArray();
                ksort($beg_info);
                ksort($end_info);
                //创建修改记录日志
                $hModal = new CardHistory();
                $hModal->f_record_id = $id;
                $hModal->f_update_beg = json_encode($beg_info);
                $hModal->f_update_end = json_encode($end_info);
                $hModal->f_user_id = $userModel->id;
                $hModal->f_user_name = $userModel->username;
                $hModal->f_update_time = date('Y-m-d H:i:s'); //本地测试用
                $hModal->save();
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return [
                    'forceReloadPage' => true,
                    'forceClose' => true
                ];
            } else {
                if ($model->f_buy_time != null && is_int($model->f_buy_time) && !$model->errors) {
                    $model->f_buy_time = date('Y-m-d H:i', $model->f_buy_time);
                }
                $cardCategory = CardRechargeCategory::getCardCategory();
                $sales = CardRecharge::getSales();
                $ret = [
                    'title' => "编辑充值卡",
                    'content' => $this->renderAjax('_information', [
                        'model' => $model,
                        'cardCategory' => $cardCategory,
                        'sales' => $sales
                    ]),
                    'footer' =>
                    Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确定', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
                return $ret;
            }
        }
    }

    /*
     * @param $id 
     * @return 
     * @desc 查看 
     */

    public function actionPreview($id) {
        $model = $this->findModel($id);
        if ($model->f_buy_time) {
            $model->f_buy_time = date('Y-m-d H:i', $model->f_buy_time);
        } else {
            $model->f_buy_time = '';
        }
        $model->f_pay_fee = $model->f_pay_fee;
        $model->f_donation_fee = $model->f_donation_fee;
        $model->f_card_fee = Common::num($model->f_card_fee + $model->f_donation_fee);
        ;
        $model->f_category_id = CardRechargeCategory::getCategoryById($model->f_category_id)['f_category_name'];
        return $this->render('update', [
                    'model' => $model,
                    'record_id' => $id
        ]);
    }

    public function actionRecord($id) {
        $request = Yii::$app->request;
        $cardBasic = CardRecharge::getCardById($id);
        if ($request->isAjax && 0 == $cardBasic['f_is_logout']) {
            $model = new CardFlow();
            $model->scenario = 'record';
            $model->f_record_id = $id;
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTrans = $model->getDb()->beginTransaction();
                try {
                    $recordModel = $this->findModel($id);
                    $recordModel->scenario = 'record';
                    $model->f_card_fee_beg = $recordModel->f_card_fee + $recordModel->f_donation_fee; //交易前金额
                    $fee = ($model->f_record_type == 1 || $model->f_record_type == 4) ? $model->f_record_fee : -$model->f_record_fee;
                    $recordFee = $model->f_record_fee;
                    $endFee = $recordModel->f_card_fee + $fee;
                    if ($endFee < 0) {//卡内基本余额不足
                        if ($model->f_record_type == 3) {//提现
                            $message = '提现金额超过余额';
                            return [
                                'forceClose' => true,
                                'forceType' => 2,
                                'forceMessage' => $message
                            ];
                        }
                        //先判断卡总额是否够减  基本+赠送
                        if (($endFee + $recordModel->f_donation_fee) < 0) {//不够
                            $message = '消费金额超过账户总额';
                            return [
                                'forceClose' => true,
                                'forceType' => 2,
                                'forceMessage' => $message
                            ];
                        } else {
                            $recordModel->f_donation_fee = strval($endFee + $recordModel->f_donation_fee);
                            $model->f_record_fee = $recordModel->f_card_fee;
                            $model->f_consum_donation = abs($fee) - $recordModel->f_card_fee;
                            $recordModel->f_card_fee = '0';
                        }
                    } else {
                        if ($model->f_record_type == 4 && $model->returnDonation == 1) {//消费退还金额退给赠送账户
                            $model->f_record_fee = 0;
                            $model->f_consum_donation = $fee;
                            $recordModel->f_donation_fee = strval($recordModel->f_donation_fee + $fee);
                        } else {
                            $recordModel->f_card_fee = strval($recordModel->f_card_fee + $fee);
                        }
                    }
                    $model->f_card_fee_end = strval($model->f_card_fee_beg + $fee); //交易后金额
                    $model->f_operate_origin = 2;
                    //判断是否  首次充值
                    $oneFlow = CardFlow::getOneFlow($id, 1);
                    if (empty($oneFlow) && $model->f_record_type == 1) {
                        //首次充值
                        $model->f_remark = '首次充值;' . $model->f_remark;
                    }
                    $model->save(); //充值流水
                    //赠送流水
                    if ($model->isDonation == 1 && $model->donationFee) {//有赠送数据
                        $newModel = new CardFlow();
                        $newModel->f_record_id = $id;
                        $newModel->f_record_type = 5;
                        $newModel->f_record_fee = $model->donationFee;
                        $newModel->f_card_fee_beg = $model->f_card_fee_end;
                        $newModel->f_flow_item = $model->f_flow_item ? $model->f_flow_item : '';
                        $newModel->f_card_fee_end = strval($model->f_card_fee_end + $newModel->f_record_fee);
                        $newModel->f_operate_origin = 2;
                        //判断是否  首次赠送
                        $onePresentFlow = CardFlow::getOnePresent($id);
                        if ($onePresentFlow) {
                            //首次充值
                            $newModel->f_remark = '首次充值赠送;' . $newModel->f_remark;
                        }
                        $newModel->save(); //赠送流水
                        $recordModel->f_donation_fee = strval($recordModel->f_donation_fee + $model->donationFee);
                    } else if ($model->f_record_type == 3 && $model->isEmpty == 1 && $recordModel->f_donation_fee > 0) {
                        $newModel = new CardFlow();
                        $newModel->f_record_id = $id;
                        $newModel->f_record_type = 6; //清空赠送账户
                        $newModel->f_record_fee = $recordModel->f_donation_fee;
                        $newModel->f_card_fee_beg = $recordModel->f_card_fee + $recordModel->f_donation_fee;
                        $newModel->f_flow_item = $model->f_flow_item ? $model->f_flow_item : '';
                        $newModel->f_card_fee_end = $recordModel->f_card_fee;
                        $newModel->f_operate_origin = 2;
                        $newModel->save(); //赠送流水
                        $recordModel->f_donation_fee = 0;
                    }
                    $recordModel->save();
                    //自动升级
                    /**
                     * 更新充值卡的基本余额  和 赠送余额
                     */
                    if ($model->isUpgradeRecord == 1) {//点击了自动升级
                        if ($model->f_record_type == 1) {//充值或赠送时判断  是否达到升级卡种
                            try {
                                $res = CardRecharge::upgradeCard($id, true);
                            } catch (Exception $e) {
                                Yii::info('CardRecharge upgradeCard failed ' . $e->getMessage());
                                throw new Exception('history save failed');
                            }
                        }
                    }
                    //发送短信
                    if (in_array($model->f_record_type, [1, 2, 3, 4])) {
                        CardRecharge::sendMessage($id, $model->f_record_type, $recordFee, $model->donationFee, $model->f_flow_item);
                    }
                    $dbTrans->commit();
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'forceClose' => true
                    ];
                } catch (Exception $e) {
                    Yii::info('add flow failed ' . $e->getMessage());
                    $dbTrans->rollBack();
                }
            } else {
                $ret = [
                    'title' => "交易",
                    'content' => $this->renderAjax('_record', [
                        'model' => $model,
                        'cardBasic' => $cardBasic
                    ]),
                    'footer' =>
                    Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确定', ['class' => 'btn btn-default btn-form card-check-btn', 'type' => "submit"])
                ];
                return $ret;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Delete an existing CardRecharge model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            $this->findModel($id)->delete();
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
     * Delete an existing CardRecharge model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionLogout($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            Yii::$app->cardCenter->createCommand()->update(CardRecharge::tableName(), ['f_is_logout' => 1], ['f_physical_id' => $id])->execute();
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
     * 
     * @param type $id
     * @return 交易流水
     */
    public function actionFlow($id) {
        $searchModel = new CardFlowSearch();
        $params = Yii::$app->request->queryParams;
        $params['f_physical_id'] = $id;
        $dataProvider = $searchModel->search($params, $this->pageSize);
//        $feeEnd = CardFlow::find()->select('f_card_fee_end')->where(['f_record_id'=>$id])->orderBy(['f_physical_id'=>SORT_DESC])->asArray()->one();
        $cardBasic = CardRecharge::getCardById($id);
        if (empty($cardBasic)) {//如果没有该会员卡
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        $chargeRecordId = CardFlow::find()->select(['f_charge_record_id'])->where(['and', ['f_record_id' => $id], ['!=', 'f_charge_record_id', '0']])->indexBy('f_charge_record_id')->asArray()->all();
        $chargeRecordId = array_keys($chargeRecordId);
        $cardDiscountPriceList = ChargeInfo::find()->select(['card_discount_price'])->where(['charge_record_id' => $chargeRecordId, 'status' => 1])->asArray()->all();
        $totalDiscountPrice = 0;
        //获取已累计充值并赠送总金额
        $total = CardFlow::find()->where(['f_record_id' => $id, 'f_record_type' => [1, 5]])->sum('f_record_fee');
        $cardLevel = CardRecharge::getCardLevel($id);
        //获取可升级的卡种，等级为0与5的不可升级
        if ($cardLevel['f_level'] == 0 || $cardLevel['f_level'] == 5) {
            $cardUpgradeCategroy = array();
        } else {
            $cardUpgradeCategroy = CardRechargeCategory::getCardUpgradeCategroy($cardLevel['f_level']);
        }
        if (!empty($cardDiscountPriceList)) {
            foreach ($cardDiscountPriceList as $value) {
                $totalDiscountPrice += $value['card_discount_price'];
            }
        }
        $totalDiscountPrice = Common::num($totalDiscountPrice);
        return $this->render('flow', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'record_id' => $id,
                    'cardBasic' => $cardBasic,
                    'totalDiscountPrice' => $totalDiscountPrice,
                    'total' => $total,
                    'cardUpgradeCategroy' => $cardUpgradeCategroy,
                    'spotId' => $this->spotId
        ]);
    }

    /**
     * Finds the CardRecharge model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CardRecharge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = CardRecharge::findOne(['f_physical_id' => $id, 'f_parent_spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @return 导出所有记录  以及流水记录
     */
    public function actionExportFlow() {
        $data = $this->exportData();
        $objectPHPExcel = new PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);
        $objectPHPExcel->setActiveSheetIndex(0);
        $page_size = 52;
        $count = count($data);
        $page_count = (int) ($count / $page_size) + 1;
        $current_page = 0;
        $n = 5;
        //设置C为文本格式
        $objectPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objectPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objectPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objectPHPExcel->getActiveSheet()->getStyle('M')->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

        foreach ($data as $key => $value) {
            if ($n % $page_size === 0) {
                $current_page = $current_page + 1;

                //报表头的输出
                $objectPHPExcel->setActiveSheetIndex(0)->getStyle('M1')
                        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                //设置居中
                $objectPHPExcel->getActiveSheet()->getStyle('B3:M4')
                        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            //明细的输出
            $objectPHPExcel->getActiveSheet()->setCellValue('A' . ($n), $key + 1);
            $objectPHPExcel->getActiveSheet()->setCellValue('B' . ($n ), $value['f_user_name']);
            $objectPHPExcel->getActiveSheet()->setCellValue('C' . ($n), " " . $value['f_id_info']);
            $objectPHPExcel->getActiveSheet()->setCellValue('D' . ($n), " " . $value['f_phone']);
            $objectPHPExcel->getActiveSheet()->setCellValue('E' . ($n ), $value['f_baby_name']);
            $objectPHPExcel->getActiveSheet()->setCellValue('F' . ($n ), CardRecharge::getSaleNameById($value['f_sale_id']));
            $objectPHPExcel->getActiveSheet()->setCellValue('G' . ($n ), $value['category_name']);
            $objectPHPExcel->getActiveSheet()->setCellValue('H' . ($n), Common::num($value['f_card_fee'] + $value['f_donation_fee']));
            $objectPHPExcel->getActiveSheet()->setCellValue('I' . ($n), Common::num($value['f_card_fee']));
            $objectPHPExcel->getActiveSheet()->setCellValue('J' . ($n), Common::num($value['f_donation_fee']));
            $flowNum = $n;
            $flowCount = count($value['flow']);
            if ($flowCount > 0) {
                foreach ($value['flow'] as $k => $v) {
                    if ($v['f_record_type'] == 1 || $v['f_record_type'] == 5) {//充值
                        $objectPHPExcel->getActiveSheet()->setCellValue('K' . ($flowNum), $v['f_record_type'] == 1 ? Common::num($v['f_record_fee']) : '');
                        $objectPHPExcel->getActiveSheet()->setCellValue('L' . ($flowNum), $v['f_record_type'] == 5 ? Common::num($v['f_record_fee']) : '');
                        $objectPHPExcel->getActiveSheet()->setCellValue('M' . ($flowNum), $v['f_pay_type'] != 0 ? CardFlow::$getPayType[$v['f_pay_type']] : '');
                        $objectPHPExcel->getActiveSheet()->setCellValue('N' . ($flowNum), $v['f_create_time']);
                    } else if ($v['f_record_type'] == 2) {//消费
                        $objectPHPExcel->getActiveSheet()->setCellValue('O' . ($flowNum), Common::num($v['f_record_fee'] + $v['f_consum_donation']));
                        $objectPHPExcel->getActiveSheet()->setCellValue('P' . ($flowNum), Common::num($v['f_record_fee']));
                        $objectPHPExcel->getActiveSheet()->setCellValue('Q' . ($flowNum), Common::num($v['f_consum_donation']));
                        $objectPHPExcel->getActiveSheet()->setCellValue('R' . ($flowNum), $v['f_create_time']);
                    } else if ($v['f_record_type'] == 4) {//消费退还
                        $objectPHPExcel->getActiveSheet()->setCellValue('S' . ($flowNum), Common::num($v['f_record_fee'] + $v['f_consum_donation']));
                        $objectPHPExcel->getActiveSheet()->setCellValue('T' . ($flowNum), Common::num($v['f_record_fee']));
                        $objectPHPExcel->getActiveSheet()->setCellValue('U' . ($flowNum), Common::num($v['f_consum_donation']));
                        $objectPHPExcel->getActiveSheet()->setCellValue('V' . ($flowNum), $v['f_create_time']);
                    } else if ($v['f_record_type'] == 3 || $v['f_record_type'] == 6) {//提现
                        $objectPHPExcel->getActiveSheet()->setCellValue('W' . ($flowNum), $v['f_record_type'] == 3 ? Common::num($v['f_record_fee']) : '');
                        $objectPHPExcel->getActiveSheet()->setCellValue('X' . ($flowNum), $v['f_record_type'] == 6 ? Common::num($v['f_record_fee']) : '');
                        $objectPHPExcel->getActiveSheet()->setCellValue('Y' . ($flowNum), $v['f_create_time']);
                    }
                    $objectPHPExcel->getActiveSheet()->setCellValue('Z' . ($flowNum), strval($v['f_remark']));
                    $flowNum++;
                }
                //合并
                $objectPHPExcel->getActiveSheet()->mergeCells('A' . $n . ':A' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('A' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('B' . ($n) . ':B' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('B' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('C' . ($n ) . ':C' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('C' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('D' . ($n ) . ':D' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('D' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('E' . ($n) . ':E' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('E' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('F' . ($n ) . ':F' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('F' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('G' . ($n ) . ':G' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('G' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('H' . ($n ) . ':H' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('H' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('I' . ($n ) . ':I' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('I' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('J' . ($n ) . ':J' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('J' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            }
            $step = $flowCount > 1 ? $flowCount : 1;
            $n = $n + $step;
        }
        //设置分页显示
        //设置C为文本格式
        $objectPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objectPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objectPHPExcel->getActiveSheet()->getStyle('S')->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

        $objectPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $objectPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        //报表头的输出
        $objectPHPExcel->getActiveSheet()->mergeCells('A1:Z1');
        $objectPHPExcel->getActiveSheet()->setCellValue('A1', 'vip会员卡使用情况一览表');
        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A1')
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getFont()->setSize(16);
        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A2')
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objectPHPExcel->getActiveSheet()->mergeCells('A2:Z2');
        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A2')->getFont()->setSize(10);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '*使用金额：请必须与His系统中收费明细单中的实收费用金额相符');

        //表格头的输出
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', '序号');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6.5);
        $objectPHPExcel->getActiveSheet()->mergeCells('A3:A4'); //合并表头
        $objectPHPExcel->getActiveSheet()->getStyle('A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', '家长姓名');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
        $objectPHPExcel->getActiveSheet()->mergeCells('B3:B4'); //合并表头
        $objectPHPExcel->getActiveSheet()->getStyle('B3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', '家长身份证号码');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
        $objectPHPExcel->getActiveSheet()->mergeCells('C3:C4'); //合并表头
        $objectPHPExcel->getActiveSheet()->getStyle('C3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('D3', '家长手机号码');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->mergeCells('D3:D4'); //合并表头
        $objectPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('E3', '家庭儿童');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->mergeCells('E3:E4'); //合并表头
        $objectPHPExcel->getActiveSheet()->getStyle('E3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('F3', '健康顾问');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->mergeCells('F3:F4'); //合并表头
        $objectPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('G3', '卡种');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->mergeCells('G3:G4'); //合并表头
        $objectPHPExcel->getActiveSheet()->getStyle('G3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('H3', '剩余金额(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->mergeCells('H3:H4'); //合并表头
        $objectPHPExcel->getActiveSheet()->getStyle('H3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('I3', '基本账户剩余(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objectPHPExcel->getActiveSheet()->mergeCells('I3:I4'); //合并表头
        $objectPHPExcel->getActiveSheet()->getStyle('I3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('J3', '赠送账户剩余(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objectPHPExcel->getActiveSheet()->mergeCells('J3:J4'); //合并表头
        $objectPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('K3', '预存情况');
        $objectPHPExcel->getActiveSheet()->mergeCells('K3:N3'); //合并表头
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('K4', '预存基本账户(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('LK4', '预存赠送账户(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('M4', '收款方式');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('N4', '预存日期');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(22);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('O3', '使用情况');
        $objectPHPExcel->getActiveSheet()->mergeCells('O3:R3'); //合并表头
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('O4', '使用金额(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('P4', '使用基本账号(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('Q4', '使用赠送账户(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('R4', '使用日期');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(22);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('S3', '消费退还');
        $objectPHPExcel->getActiveSheet()->mergeCells('S3:V3'); //合并表头
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('S4', '消费退还金额(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('T4', '退还基本账号(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('U4', '退还赠送账号(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('V4', '退还日期');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(22);


        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('W3', '卡内提现');
        $objectPHPExcel->getActiveSheet()->mergeCells('W3:Y3'); //合并表头
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('W4', '提现基本账户(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('X4', '清空赠送账户(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('Y4', '提现日期');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(22);



        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('Z3', '其他');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(22);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('Z4', '备注');


        //设置居中
        $objectPHPExcel->getActiveSheet()->getStyle('A3:Z3')
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        $objectPHPExcel->getActiveSheet()->getStyle('A3:Z3')
                ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('A3:Z3')
                ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('A3:Z3')
                ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('A3:Z3')
                ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('A3:Z3')
                ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //设置颜色
        $objectPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('E7D8E3');
        $objectPHPExcel->getActiveSheet()->getStyle('K3:N4')->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('DDEBF7');
        $objectPHPExcel->getActiveSheet()->getStyle('O3:R4')->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FCE4D6');
        $objectPHPExcel->getActiveSheet()->getStyle('H3')->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('C6E0B4');
        $objectPHPExcel->getActiveSheet()->getStyle('I3')->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('C6E0B4');
        $objectPHPExcel->getActiveSheet()->getStyle('J3')->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('C6E0B4');
        $objectPHPExcel->getActiveSheet()->getStyle('S3:V4')->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('E7D8E3');
        $objectPHPExcel->getActiveSheet()->getStyle('W3:Y4')->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('F2DDDC');
        $objectPHPExcel->getActiveSheet()->getStyle('Z3:Z4')->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('DDEBF7');

        //设置边框
        $objectPHPExcel->getActiveSheet()->getStyle('A1' . ':Z' . $n)
                ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('A1' . ':Z' . $n)
                ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('A1' . ':Z' . $n)
                ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('A1' . ':Z' . $n)
                ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('A1' . ':Z' . $n)
                ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $objWriter = new PHPExcel_Writer_Excel2007($objectPHPExcel);
        $date = date("Y-m-d", strtotime("+1 day"));
        $outputFileName = '充值卡流水记录' . $date . ".xls";
        ob_end_clean(); //清除缓冲区,避免乱码
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control:must-revalidate, post-check = 0, pre-check = 0');
        header('Content-Type:application/force-download');
        header('Content-Type:application/vnd.ms-execl');
        header('Content-Type:application/octet-stream');
        header('Content-Type:application/download');
        header('Content-Disposition:attachment;filename="' . $outputFileName . '"');
        header('Content-Transfer-Encoding:binary');
        $objWriter->save('php://output');
    }

    protected function exportData() {
//        $recharge = CardRecharge::getData();
        $recharge = CardRecharge::getExportData();
        $data = [];
        if (!empty($recharge)) {
            foreach ($recharge as &$card) {
                $flow = CardFlow::getFlow($card['f_physical_id']);
                $card['flow'] = $flow;
            }
        }
        return $recharge;
    }

    public function actionExport($id) {
        $objectPHPExcel = new PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);
        $objectPHPExcel->setActiveSheetIndex(0);
        $page_size = 52;
        $params = Yii::$app->request->queryParams;
        $params['f_physical_id'] = $id;
        $searchModel = new CardFlowSearch();
        $dataProviderObj = $searchModel->search($params, $this->pageSize);
        $dataProvider = $dataProviderObj->query->all();
        $count = count($dataProvider);
        $page_count = (int) ($count / $page_size) + 1;
        $current_page = 0;
        $n = 0;
        foreach ($dataProvider as $key => $value) {
            if ($n % $page_size === 0) {
                $current_page = $current_page + 1;

                //报表头的输出
                $objectPHPExcel->setActiveSheetIndex(0)->getStyle('B1')->getFont()->setSize(24);
                $objectPHPExcel->setActiveSheetIndex(0)->getStyle('B1')
                        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', '日期：' . date("Y年m月j日", strtotime("+1 day")));
                $objectPHPExcel->setActiveSheetIndex(0)->getStyle('G2')
                        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                //设置居中
                $objectPHPExcel->getActiveSheet()->getStyle('B3:I3')
                        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            //明细的输出
            $objectPHPExcel->getActiveSheet()->setCellValue('B' . ($n + 4), $key + 1);

            $objectPHPExcel->getActiveSheet()->setCellValue('C' . ($n + 4), Common::num($value['f_record_fee']));
            $objectPHPExcel->getActiveSheet()->setCellValue('D' . ($n + 4), $value['f_user_name']);
            $objectPHPExcel->getActiveSheet()->setCellValue('E' . ($n + 4), $value['f_pay_type'] ? CardFlow::$getPayType[$value['f_pay_type']] : '');
            $objectPHPExcel->getActiveSheet()->setCellValue('F' . ($n + 4), CardFlow::$getRecordType[$value['f_record_type']]);
            $objectPHPExcel->getActiveSheet()->setCellValue('G' . ($n + 4), Common::num($value['f_card_fee_beg']));
            $objectPHPExcel->getActiveSheet()->setCellValue('H' . ($n + 4), Common::num($value['f_card_fee_end']));
            $objectPHPExcel->getActiveSheet()->setCellValue('I' . ($n + 4), $value['f_create_time']);
            //设置边框
            $currentRowNum = $n + 4;
            $objectPHPExcel->getActiveSheet()->getStyle('B' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('B' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('B' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('B' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('B' . ($n + 4) . ':I' . $currentRowNum)
                    ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $n = $n + 1;
        }

        //设置分页显示

        $objectPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $objectPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        //报表头的输出
        $objectPHPExcel->getActiveSheet()->mergeCells('B1:I1');
        $objectPHPExcel->getActiveSheet()->setCellValue('B1', '流水记录表');
        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('B1')->getFont()->setSize(20);

        //表格头的输出
        $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', '序号');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6.5);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', '交易金额(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('D3', '操作用户名');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('E3', '支付类型');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('F3', '交易类型');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('G3', '交易前卡内余额(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('H3', '交易后卡内余额(元)');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(22);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('I3', '时间');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(22);


        //设置居中
        $objectPHPExcel->getActiveSheet()->getStyle('B3:H3')
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        $objectPHPExcel->getActiveSheet()->getStyle('B3:I3')
                ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('B3:I3')
                ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('B3:I3')
                ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('B3:I3')
                ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('B3:I3')
                ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //设置颜色
        $objectPHPExcel->getActiveSheet()->getStyle('B3:I3')->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF66CCCC');
        ob_end_clean();
        ob_start();
        $objWriter = new PHPExcel_Writer_Excel2007($objectPHPExcel);
        $date = date("Y-m-d", strtotime("+1 day"));
        $outputFileName = '充值卡流水记录' . $date . ".xls";
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control:must-revalidate, post-check = 0, pre-check = 0');
        header('Content-Type:application/force-download');
        header('Content-Type:application/vnd.ms-execl');
        header('Content-Type:application/octet-stream');
        header('Content-Type:application/download');
        header('Content-Disposition:attachment;filename="' . $outputFileName . '"');
        header('Content-Transfer-Encoding:binary');
        $objWriter->save('php://output');
    }

    /**
     * 
     * ***************卡种类变更信息****************
     */

    /**
     * 
     * @param type $id 卡片ID
     * @return type 历史变更记录
     */
    public function actionHistoryIndex($id) {
        $searchModel = new CategoryHistorySearch();
        $param = Yii::$app->request->queryParams;
        $param['f_physical_id'] = $id;
        $dataProvider = $searchModel->search($param, $this->pageSize);
        $cardInfo = CardRecharge::getCardInfo($id);
        if (empty($cardInfo)) {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        $categoryService = CardRechargeCategory::getCatServiceListById($cardInfo['f_category_id']);
        return $this->render('/category-history/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'record_id' => $id,
                    'cardInfo' => $cardInfo,
                    'categoryService' => $categoryService
        ]);
    }

    /**
     * @param type $id 卡片ID
     * @return type 增加变更信息
     */
    public function actionHistoryCreate($id) {
        $request = Yii::$app->request;
        $cardModel = CardRecharge::findModel($id);
        if ($request->isAjax && 0 == $cardModel->f_is_logout) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new CategoryHistory();
            if ($cardModel == null) {
                throw new NotFoundHttpException('你所请求的页面不存在');
            }
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $model->f_record_id = $id;
                    $model->f_beg_category = $cardModel->f_category_id;
                    $st1 = $model->save();
                    $cardModel->f_category_id = $model->f_end_category;
                    $cardModel->f_card_fee = strval($cardModel->f_card_fee);
                    $cardModel->f_donation_fee = strval($cardModel->f_donation_fee);
                    $res = $cardModel->save();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                } catch (Exception $exc) {
                    $dbTrans->rollBack();
//                    echo $exc->getTraceAsString();
                }
                return [
                    'forceReloadPage' => true,
                    'forceClose' => true
                ];
            } else {
                $ret = [
                    'title' => "变更卡种",
                    'content' => $this->renderAjax('/category-history/create', [
                        'model' => $model,
                        'record_id' => $id,
                        'cardCategory' => CardRechargeCategory::getCardCategory(),
                        'cardModel' => $cardModel,
                        'cardService' => json_encode(CardRechargeCategory::getCategoryService(), true)
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确认', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
                return $ret;
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @param type $id 卡片ID
     * @return type test
     */
    public function actionHistoryCreateTest($id) {
        $model = new CategoryHistory();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('/category-history/create-test', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Lists all UserCard models.
     * @return mixed
     */
    public function actionCardIndex() {
        $searchModel = new UserCardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        $card_physical_id = [];
        $cardInfo = [];
        if ($dataProvider->getModels()) {
//            $card_physical_id = $dataProvider->query->asArray()->all();
//            $card_physical_id = array_column($all, 'card_id');
            foreach ($dataProvider->getModels() as $model) {
                $card_physical_id[] = $model->getAttributes();
            }
        }
        if ($card_physical_id) {
            try {
                $url = Yii::$app->request->getHostInfo() . Url::to(['@cardCenterCardInfoBySn']);
                $cardInfo = Common::curlPost($url, ['f_card_id' => $card_physical_id]);
                $cardInfo = $cardInfo ? json_decode($cardInfo, true) : '';
            } catch (Exception $exc) {
                $cardInfo = [];
            }
        }
        return $this->render('/card/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'cardInfo' => $cardInfo,
        ]);
    }

    /**
     * 
     * @return 验证会员卡
     */
    public function actionCardCheck() {
        $model = new UserCard();
        $model->scenario = 'check';
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $record = UserCard::checkCard($model->checkType, $model->checkNum);
                if ($record) {//有数据
                    //有效的卡，并且未激活
                    $record["user_name"] = $model->user_name;
                    $record["phone"] = $model->phone;
                    return ['forceClose' => true, 'forceRedirect' => Url::to(['card-create', 'record' => $record])];
//                   $this->redirect(Url::to(['create', 'record' => $record]));
                } else {
                    Yii::$app->getSession()->setFlash('error', '验证失败');
//                    return $this->redirect(['index']);
                    return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
                }
            } else {
                return [
                    'title' => "验证",
                    'content' => $this->renderAjax('/card/check', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('验证', ['class' => 'btn btn-default btn-form card-check-btn', 'type' => "submit"])
                ];
            }
        } elseif (($id = Yii::$app->request->get('id'))) {
            $userCard = $this->findCardModel($id);
            $record = UserCard::checkCard(1, $userCard->card_id);
            if ($record) {//有数据
                $this->redirect(Url::to(['card-create', 'record' => $record]));
            } else {
                Yii::$app->getSession()->setFlash('error', '验证失败');
                return $this->redirect(['card-index']);
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * Creates a new UserCard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCardCreate() {
        $model = new UserCard();
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            $text = '保存成功';
            try {
                if ($model->id) {
                    $model->updateAll([
                        'user_name' => $model->user_name,
                        'phone' => $model->phone,
                            ], ['id' => $model->id]);
                } else {
                    //调用卡中心  激活接口激活卡   现在改为直接激活服务
                    UserCard::activateCard($model->card_id, $model->card_type);
                    if (!$model->card_physical_id) {
                        $model->card_type = 2;
                    }
                    $model->save(false);
                    $text = '激活成功';
                }
                $cardRecord = Yii::$app->request->get('record');
//                $serviceLeft = Yii::$app->request->post('UserCard')['service_left'];
//                $serviceId = Yii::$app->request->post('UserCard')['service_id'];
                if (!empty($model->service_left) && (!empty($model->service_id))) {//修改剩余次数
//                    $serviceRecord = CardServiceLeft::find()->where(['card_id' => $cardRecord['f_card_id'], 'service_id' => $service_id])->one();
//                    if ($serviceRecord) {//修改
//                        $serviceRecord->service_left = $service_left;
//                    } else {
//                        $serviceRecord = new CardServiceLeft();
//                        $serviceRecord->service_left = $service_left;
//                        $serviceRecord->card_physical_id = isset($cardRecord['f_physical_id']) ? $cardRecord['f_physical_id'] : 0;
//                        $serviceRecord->service_id = $service_id;
//                        $serviceRecord->card_id = $cardRecord['f_card_id'];
//                        $serviceRecord->activate_time = time();
//                        $serviceRecord->invalid_time = time() + 365 * 24 * 60 * 60;
//                    }
//                    $serviceRecord->save();
                    //新增/修改卡关联服务的剩余次数
                    CardServiceLeft::createUpdateService($model->service_left, $model->service_id, $model->card_id, $model->card_physical_id);
                }
                $dbTrans->commit();
            } catch (Exception $e) {
                Yii::info('cardCreate failed info :' . $e->getMessage());
                $dbTrans->rollBack();
            }
            Yii::$app->getSession()->setFlash('success', $text);
            return $this->redirect(['card-index']);
        } else {
            $record = Yii::$app->request->get('record');
            if ($record['dataFrom'] == 1) {//本地已经有数据  更新
                $model->id = $record['id'];
            }
            $model->user_name = $model->user_name ? $model->user_name : $record['user_name'];
            $model->phone = $model->phone ? $model->phone : $record['phone'];
            $model->card_id = $record['f_card_id'];
            $model->card_physical_id = isset($record['f_physical_id']) ? $record['f_physical_id'] : 0;
            $model->card_type_code = $record['f_card_type_code'];
            $model->parent_spot_id = $this->parentSpotId;
            $model->f_effective_time = $record['f_effective_time'];
            $model->card_type = $record['card_type'];
            //服务信息
            $service = ServiceConfig::find()->where(['card_type' => $record['f_card_type_code']])->asArray()->all();
            //剩余次数
            $left = [];
            if ($service) {
                $left = CardServiceLeft::find()->where(['card_id' => $record['f_card_id'], 'service_id' => array_column($service, 'id')])->indexBy('service_id')->asArray()->all();
            }
            return $this->render('/card/create', [
                        'model' => $model,
                        'record' => $record,
                        'service' => $service,
                        'left' => $left,
            ]);
        }
    }

    /**
     * Updates an existing UserCard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionCardUpdate($id) {
        $model = $this->findCardModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['card-index']);
        } else {
            return $this->render('/card/update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Delete an existing UserCard model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionCardDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            $this->findCardModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['card-index']);
        }
    }

    /**
     * 
     * 
     * ========给卡充值=========== 
     * 
     */
    public function actionRecharge($id, $orderSn = null) {
        $request = Yii::$app->request;
        $cardBasic = CardRecharge::getCardById($id);

        if ($request->isAjax && 0 == $cardBasic['f_is_logout']) {
            if ($orderSn) {
                $model = Order::findModelBySn($orderSn);
                if ($model->donation_fee != '0.0' || $model->donation_fee != 0) {
                    $model->isDonation = 1;
                }
                $model->scenario = 'recharge';
                $model->validate();
            } else {
                $model = new Order();
                $model->record_id = $id;
            }
            $model->scenario = 'recharge';
            $flowModel = new CardFlow();
            $flowModel->f_record_id = $id;
            $flowModel->scenario = 'recharge';
            $cardBasic = CardRecharge::getCardInfo($id);
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $res = Order::setOrderInfo($id, $model, $flowModel);
                if ($res) {
                    $ret = [
                        'title' => "充值",
                        'content' => $this->renderAjax('/recharge/_payment', $res),
                        'footer' => Html::a('上一步', Url::to(['@rechargeIndexRecharge', 'id' => $id, 'orderSn' => $res['outTradeNo']]), ['class' => 'btn btn-cancel btn-form btn-step-first', 'role' => 'modal-remote']) .
                        Html::button('确认收费', ['class' => 'btn btn-default btn-form create-rebate', 'type' => "submit"])
                    ];
                    return $ret;
                } else {
                    $message = '操作失败，请重试';
                    return [
                        'forceClose' => true,
                        'forceType' => 2,
                        'forceMessage' => $message
                    ];
                }
            } else if ($flowModel->load(Yii::$app->request->post())) {
                if ($flowModel->validate()) {
                    if (($flowModel->f_pay_type == 3 && $flowModel->scanMode == 2) || ($flowModel->f_pay_type == 4 && $flowModel->scanMode == 2)) {
                        $message = '请选择正常的支付类型';
                        return [
                            'forceClose' => true,
                            'forceType' => 2,
                            'forceMessage' => $message
                        ];
                    }
                    try {
                        if ($flowModel->f_pay_type == 3 || $flowModel->f_pay_type == 4) {//微信或支付宝  扫描枪支付
                            $orderModel = Order::findModelBySn($flowModel->orderSn);
                            $subject = Html::encode(Yii::$app->cache->get(Yii::getAlias('@spotName') . $this->spotId . $this->userInfo->id)) . '会员卡';
                            $scpRes = PaymentLog::scannerPayment($orderModel, $params, $flowModel->f_pay_type, $flowModel, $subject);
                            if (!$scpRes) {
                                throw new Exception('PaymentLog::scannerPayment Exception ');
                            } else {

                                return [
                                    'forceReload' => '#crud-datatable-pjax',
                                    'forceClose' => true,
                                    'forceType' => 1,
                                    'forceMessage' => '保存成功',
                                    'forceCallback' => 'window.clearInterval(main.oderStatus)'
                                ];
                            }
                        }
                    } catch (Exception $e) {
                        $flowModel->wechatAuthCode = '';
                        $flowModel->alipayAuthCode = '';
                        $orderModel = Order::findModelBySn($flowModel->orderSn);
                        if ($orderModel->donation_fee) {
                            $orderModel->isDonation = 1;
                        }
                        $res = Order::setOrderInfo($id, $orderModel, $flowModel);
                        if ($res) {
                            $ret = [
                                'title' => "充值",
                                'content' => $this->renderAjax('/recharge/_payment', $res),
                                'footer' => Html::a('上一步', Url::to(['@rechargeIndexRecharge', 'id' => $id, 'orderSn' => $res['outTradeNo']]), ['class' => 'btn btn-cancel btn-form btn-step-first', 'role' => 'modal-remote']) .
                                Html::button('确认收费', ['class' => 'btn btn-default btn-form create-rebate', 'type' => "submit"]),
                                'forceType' => 2,
                                'forceMessage' => '支付失败,请重新支付',
                            ];
                        }
                        return $ret;
                    }
                    //支付成功  修改订单   增加流水
                    $orderModel = Order::findModelBySn($flowModel->orderSn);
                    if ($orderModel && in_array($orderModel->status, [1, 3])) {
                        $orderModel->status = 2; //支付成功
                        $orderModel->type = $flowModel->f_pay_type; //支付类型
                        $orderModel->save();
                    }
                    $res = CardFlow::addFlow($flowModel, $id);
                    if ($res) {
                        $message = '保存成功';
                        $forceType = 1;
                    } else {
                        $message = '保存失败';
                        $forceType = 2;
                    }
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'forceClose' => true,
                        'forceType' => $forceType,
                        'forceMessage' => $message,
                        'forceCallback' => 'window.clearInterval(main.oderStatus)'
                    ];
                } else {
                    $orderModel = Order::findModelBySn($flowModel->orderSn);
                    if ($orderModel->donation_fee) {
                        $orderModel->isDonation = 1;
                    }
                    $res = Order::setOrderInfo($id, $orderModel, $flowModel);
                    if ($res) {
                        $ret = [
                            'title' => "充值",
                            'content' => $this->renderAjax('/recharge/_payment', $res),
                            'footer' => Html::a('上一步', Url::to(['@rechargeIndexRecharge', 'id' => $id, 'orderSn' => $res['outTradeNo']]), ['class' => 'btn btn-cancel btn-form btn-step-first', 'role' => 'modal-remote']) .
                            Html::button('确认收费', ['class' => 'btn btn-default btn-form create-rebate', 'type' => "submit"])
                        ];
                    }
                    return $ret;
                }
            } else {
                $ret = [
                    'title' => "充值",
                    'content' => $this->renderAjax('/recharge/_recharge', [
                        'model' => $model,
                        'cardBasic' => $cardBasic
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('下一步', ['class' => 'btn btn-default btn-form card-check-btn', 'type' => "submit"])
                ];
                return $ret;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     *
     *  订阅短信
     */
    public function actionSubscribe($id) {
        $model = $this->findModel($id);
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($model->f_message_subscribe == 1) {
            $model->f_message_subscribe = 2;
        } else {
            $model->f_message_subscribe = 1;
        }
        $model->save();
        return [
            'forceReload' => '#crud-datatable-pjax',
            'forceClose' => true
        ];
    }

    /**
     * Finds the UserCard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UserCard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCardModel($id) {
        if (($model = UserCard::findOne(['id' => $id, 'parent_spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
