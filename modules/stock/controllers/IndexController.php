<?php

namespace app\modules\stock\controllers;

use app\modules\stock\models\search\ConsumablesStockInfoSearch;
use Yii;
use yii\web\Response;
use app\modules\stock\models\ConsumablesStock;
use app\modules\stock\models\search\ConsumablesStockSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\common\base\MultiModel;
use app\modules\stock\models\ConsumablesStockInfo;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\Consumables;
use app\modules\spot\models\SupplierConfig;
use yii\db\Exception;
use yii\db\ActiveQuery;
use app\modules\user\models\User;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use app\modules\stock\models\search\ConsumablesOutboundSearch;
use app\modules\stock\models\ConsumablesOutbound;
use app\modules\stock\models\ConsumablesOutboundInfo;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\spot_set\models\ConsumablesClinic;
use yii\db\Query;

/**
 * IndexController implements the CRUD actions for ConsumablesStock model.
 */
class IndexController extends BaseController
{

    use MaterialTrait;

use PharmacyTrait;

    /**
     * Lists all ConsumablesStock models.
     * @return mixed
     */
    public function actionConsumablesInboundIndex() {
        $searchModel = new ConsumablesStockSearch();
        $params = Yii::$app->request->queryParams;
        if (isset($params['ValidSearch']['status']) && $params['ValidSearch']['status'] == 2) {//名称
            $dataProvider = $searchModel->searchDrugs($params, $this->pageSize);
            $view = 'inbound-drugs';
        } else {
            $dataProvider = $searchModel->search($params, $this->pageSize);
            $view = 'inbound-index';
        }
        return $this->render($view, [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ConsumablesStock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionConsumablesInboundCreate() {
        $request = Yii::$app->request;
        $model = new MultiModel([
            'models' => [
                'stock' => new ConsumablesStock(),
                'stockInfo' => new ConsumablesStockInfo()
            ]
        ]);
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $stockInfoModel = $model->getModel('stockInfo');
                    if (isset($stockInfoModel->consumables_id)) {
                        $result = $model->getModel('stock')->save();
                        if ($result) {
                            $rows = [];
                            if (count($stockInfoModel->consumables_id) > 0) {
                                foreach ($stockInfoModel->consumables_id as $key => $v) {
                                    if ($stockInfoModel->deleted[$key] == null) {
                                        $rows[] = [
                                            $this->spotId,
                                            $model->getModel('stock')->id,
                                            $v,
                                            $stockInfoModel->total_num[$key],
                                            $stockInfoModel->invoice_number[$key] ? $stockInfoModel->invoice_number[$key] : '',
                                            $stockInfoModel->total_num[$key],
                                            $stockInfoModel->default_price[$key],
                                            strtotime($stockInfoModel->expire_time[$key]),
                                            time(),
                                            time()
                                        ];
                                    }
                                }
                            }
                            if (count($rows) > 0) {
                                Yii::$app->db->createCommand()->batchInsert(ConsumablesStockInfo::tableName(), ['spot_id', 'consumables_stock_id', 'consumables_id', 'total_num', 'invoice_number', 'num', 'default_price', 'expire_time', 'create_time', 'update_time'], $rows)->execute();
                            }
                        }
                    } else {
                        $this->result['errorCode'] = 1001;
                        $this->result['msg'] = '请选择入库医疗耗材';
                        return $this->result;
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    Yii::error(json_encode($e->errorInfo), 'stock-inbound-create');
                    $dbTrans->rollBack();
                }
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = $model->errors['stockInfo'][0][0];
                return $this->result;
            }
        } else {
            $supplierConfig = SupplierConfig::getList();
            $consumablesList = Consumables::getConsumablesClinic();
            $query = ConsumablesStockInfo::find()->select(['id'])->where(['id' => 0]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
                'sort' => [
                    'attributes' => ['']
                ]
            ]);
            return $this->render('inbound-create', [
                        'model' => $model,
                        'supplierConfig' => $supplierConfig,
                        'consumablesList' => $consumablesList,
                        'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Updates an existing ConsumablesStock model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionConsumablesInboundUpdate($id) {
        $request = Yii::$app->request;
        $stockModel = $this->findConsumablesStockModel($id);
        $stockModel->inbound_time = date('Y-m-d', $stockModel->inbound_time);
        $model = new MultiModel([
            'models' => [
                'stock' => $stockModel,
                'stockInfo' => new ConsumablesStockInfo()
            ]
        ]);
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $result = $model->getModel('stock')->save();
                    if ($result) {
                        $rows = [];
                        $db = Yii::$app->db;
                        $stockInfoModel = $model->getModel('stockInfo');
                        if (count($stockInfoModel->consumables_id) > 0) {
                            foreach ($stockInfoModel->consumables_id as $key => $v) {
                                //新增或者修改的
                                if ($stockInfoModel->deleted[$key] != 1) {
                                    if ($stockInfoModel->consumablesStockInfoId[$key] == null) {
                                        $rows[] = [
                                            $this->spotId,
                                            $id,
                                            $v,
                                            $stockInfoModel->total_num[$key],
                                            $stockInfoModel->total_num[$key],
                                            $stockInfoModel->invoice_number[$key] ? $stockInfoModel->invoice_number[$key] : '',
                                            $stockInfoModel->default_price[$key],
                                            strtotime($stockInfoModel->expire_time[$key]),
                                            time(),
                                            time()
                                        ];
                                    } else {

                                        $db->createCommand()->update(ConsumablesStockInfo::tableName(), [
                                            'total_num' => $stockInfoModel->total_num[$key],
                                            'num' => $stockInfoModel->total_num[$key],
                                            'invoice_number' => $stockInfoModel->invoice_number[$key] ? $stockInfoModel->invoice_number[$key] : '',
                                            'default_price' => $stockInfoModel->default_price[$key],
                                            'expire_time' => strtotime($stockInfoModel->expire_time[$key]),
                                            'update_time' => time()
                                                ], ['id' => $stockInfoModel->consumablesStockInfoId[$key], 'consumables_stock_id' => $id, 'spot_id' => $this->spotId])->execute();
                                    }
                                } else {
                                    //删除操作
                                    //若id值不为null，则直接删除该记录
                                    if ($stockInfoModel->consumablesStockInfoId[$key] != null) {
                                        $db->createCommand()->delete(ConsumablesStockInfo::tableName(), ['id' => $stockInfoModel->consumablesStockInfoId[$key], 'consumables_stock_id' => $id, 'spot_id' => $this->spotId])->execute();
                                    }
                                }
                            }
                            if (count($rows) > 0) {
                                $db->createCommand()->batchInsert(ConsumablesStockInfo::tableName(), ['spot_id', 'consumables_stock_id', 'consumables_id', 'total_num', 'num', 'invoice_number', 'default_price', 'expire_time', 'create_time', 'update_time'], $rows)->execute();
                            }
                        } else {
                            $this->result['errorCode'] = 1001;
                            $this->result['msg'] = '请选择入库医疗耗材';
                            return $this->result;
                        }
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    Yii::error(json_encode($e->errorInfo, true), 'stock-inbound-update');
                }
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = $model->errors['stockInfo'][0][0];
                return $this->result;
            }
        } else {
            $supplierConfig = SupplierConfig::getList();
            $consumablesList = Consumables::getConsumablesClinic();
            $dataProvider = $this->getInboundUpdateData($id);
            return $this->render('inbound-update', [
                        'model' => $model,
                        'supplierConfig' => $supplierConfig,
                        'consumablesList' => $consumablesList,
                        'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * @property 审核入库信息
     * @param  非药品库存公共信息表id $id
     */
    public function actionConsumablesInboundApply($id, $status = null) {
        $model = $this->findConsumablesStockModel($id);
        $model->scenario = 'inboundApply';
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!$status || !in_array($status, [1, 3])) {//若审核时，参数缺失／status不是[1,3]时，审核失败，并返回请求错误400
                throw new BadRequestHttpException();
            }
            $model->status = $status;
            $model->apply_user_id = $this->userInfo->id;
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', '操作成功');
                return ['forceClose' => true, 'forceRedirect' => Url::to(['@stockIndexConsumablesInboundIndex'])];
            }
        } else {
            $dataProvider = $this->getInboundUpdateData($id);
            return $this->render('inbound-apply', [
                        'dataProvider' => $dataProvider,
                        'model' => $model,
            ]);
        }
    }

    /**
     * @property 查看入库信息
     * @param  非药品库存公共信息表id $id
     */
    public function actionConsumablesInboundView($id) {
        $model = $this->findConsumablesStockModel($id, [1, 3]);
        $applyName = User::getUserInfo($model->apply_user_id, ['username'])['username'];
        $dataProvider = $this->getInboundUpdateData($id);
        return $this->render('inbound-apply', [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
                    'applyName' => $applyName
        ]);
    }

    /**
     * Delete an existing ConsumablesStock model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionConsumablesInboundDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            $this->findInboundModel($id)->delete();
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
     * Lists all Outbound models.
     * @property 出库管理
     * @return mixed
     */
    public function actionConsumablesOutboundIndex() {
        $searchModel = new ConsumablesOutboundSearch();
        $params = Yii::$app->request->queryParams;
        if (isset($params['ValidSearch']['status']) && $params['ValidSearch']['status'] == 2) {//单号
            $dataProvider = $searchModel->searchDrugs($params, $this->pageSize);
            $view = 'outbound-drugs';
        } else {
            $dataProvider = $searchModel->search($params, $this->pageSize);
            $view = 'outbound-index';
        }
        return $this->render($view, [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Outbound models.
     * @property 出库管理-新增出库
     * @return mixed
     */
    public function actionConsumablesOutboundCreate() {
        $request = Yii::$app->request;
        $model = new MultiModel([
            'models' => [
                'outbound' => new ConsumablesOutbound(),
                'outboundInfo' => new ConsumablesOutboundInfo()
            ]
        ]);
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $outboundInfoModel = $model->getModel('outboundInfo');
                    if (isset($outboundInfoModel->consumables_stock_info_id)) {
                        $result = $model->getModel('outbound')->save();
                        if ($result) {
                            $rows = [];
                            foreach ($outboundInfoModel->consumables_stock_info_id as $key => $v) {
                                if ($outboundInfoModel->deleted[$key] == null) {
                                    $rows[] = [
                                        $this->spotId,
                                        $model->getModel('outbound')->id,
                                        $v,
                                        $outboundInfoModel->num[$key],
                                        time(),
                                        time()
                                    ];
                                }
                            }
                            if (count($rows) > 0) {
                                Yii::$app->db->createCommand()->batchInsert(ConsumablesOutboundInfo::tableName(), ['spot_id', 'consumables_outbound_id', 'consumables_stock_info_id', 'num', 'create_time', 'update_time'], $rows)->execute();
                            }
                        }
                    } else {
                        $this->result['errorCode'] = true;
                        $this->result['msg'] = '请选择出库医疗耗材';
                        return $this->result;
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    Yii::error(json_encode($e->errorInfo), 'consumables-outbound-create');
                }
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = $model->errors['outboundInfo'][0][0];
                return $this->result;
            }
        } else {
            $departmentList = SecondDepartment::getList();
            $userList = User::getUserList($this->spotId);
            $query = ConsumablesOutboundInfo::find()->select(['id'])->where(['id' => 0]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
                'sort' => [
                    'attributes' => ['']
                ]
            ]);
            $consumablesList = ConsumablesStockInfo::getList();
            return $this->render('outbound-create', [
                        'model' => $model,
                        'departmentList' => $departmentList,
                        'userList' => $userList,
                        'dataProvider' => $dataProvider,
                        'consumablesList' => $consumablesList,
            ]);
        }
    }

    public function actionConsumablesOutboundUpdate($id) {
        $request = Yii::$app->request;
        $outboundInfo = $this->findOutboundInfo($id);
        $outboundInfo->outbound_time = date('Y-m-d', $outboundInfo->outbound_time);
        $model = new MultiModel([
            'models' => [
                'outbound' => $outboundInfo,
                'outboundInfo' => new ConsumablesOutboundInfo()
            ]
        ]);
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTran = Yii::$app->db->beginTransaction();
                try {
                    $result = $model->getModel('outbound')->save();
                    if ($result) {
                        $rows = [];
                        $outboundInfoModel = $model->getModel('outboundInfo');
                        $db = Yii::$app->db;
                        foreach ($outboundInfoModel->consumables_stock_info_id as $key => $v) {
                            //新增/编辑操作
                            if ($outboundInfoModel->deleted[$key] == null) {
                                if ($outboundInfoModel->outboundInfoId[$key] == null) {
                                    //新增记录
                                    $rows[] = [
                                        $this->spotId,
                                        $model->getModel('outbound')->id,
                                        $v,
                                        $outboundInfoModel->num[$key],
                                        time(),
                                        time()
                                    ];
                                } else {
                                    //编辑记录
                                    $db->createCommand()->update(ConsumablesOutboundInfo::tableName(), [
//                                         'consumables_stock_info_id' => $v,
                                        'num' => $outboundInfoModel->num[$key],
                                        'update_time' => time()
                                            ], [
                                        'id' => $outboundInfoModel->outboundInfoId[$key],
                                        'spot_id' => $this->spotId,
                                        'consumables_outbound_id' => $model->getModel('outbound')->id
                                    ])->execute();
                                }
                            } else {
                                //删除操作
                                if ($outboundInfoModel->outboundInfoId[$key] != null) {
                                    $db->createCommand()->delete(ConsumablesOutboundInfo::tableName(), ['id' => $outboundInfoModel->outboundInfoId[$key], 'spot_id' => $this->spotId, 'consumables_outbound_id' => $model->getModel('outbound')->id])->execute();
                                }
                            }
                        }
                        if (count($rows) > 0) {
                            Yii::$app->db->createCommand()->batchInsert(ConsumablesOutboundInfo::tableName(), ['spot_id', 'consumables_outbound_id', 'consumables_stock_info_id', 'num', 'create_time', 'update_time'], $rows)->execute();
                        }
                    }
                    $this->result['errorCode'] = 0;
                    $this->result['msg'] = '保存成功';
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTran->commit();
                    return $this->result;
                } catch (Exception $e) {
                    $dbTran->rollBack();
                    Yii::error(json_encode($e->errorInfo), 'consumables-outbound-update');
                }
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = $model->errors['outboundInfo'][0][0];
                return $this->result;
            }
        } else {
            $departmentList = SecondDepartment::getList();
            $userList = User::getUserList($this->spotId);
            $dataProvider = $this->getDataProvider($id);
            $consumablesList = ConsumablesStockInfo::getList();
            return $this->render('outbound-update', [
                        'model' => $model,
                        'departmentList' => $departmentList,
                        'userList' => $userList,
                        'dataProvider' => $dataProvider,
                        'consumablesList' => $consumablesList,
            ]);
        }
    }

    /**
     *
     * @param 非药品出库公共信息表id $id
     * @property 删除出库记录
     * @return \yii\web\Response
     */
    public function actionConsumablesOutboundDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $this->findOutboundInfo($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceReload' => '#crud-datatable-pjax', 'forceClose' => true,];
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * @property 审核出库信息
     * @param  非药品出库公共信息表id $id
     */
    public function actionConsumablesOutboundApply($id, $status = null) {

        $model = $this->findOutboundInfo($id);
        $model->scenario = 'outboundApply';
        $dataProvider = $this->getDataProvider($id);
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!$status || !in_array($status, [1, 3])) {//若审核时，参数缺失／status不是[1,3]时，审核失败，并返回请求错误400
                throw new BadRequestHttpException();
            }
            //审核不成功，保存状态与操作人，不减库存
            if ($status == 3) {
                $model->status = $status;
                $model->apply_user_id = $this->userInfo->id;
                if ($model->save()) {
                    Yii::$app->getSession()->setFlash('success', '操作成功');
                    return ['forceClose' => true, 'forceRedirect' => Url::to(['@stockIndexConsumablesOutboundIndex'])];
                }
            }
            $db = Yii::$app->db;
            $dbTrans = $db->beginTransaction();
            try {
                $outboundNumArray = [];
                $rows = [];
                $totals = '';
                $query = new Query();
                $query->from(['a' => ConsumablesOutboundInfo::tableName()]);
                $query->select(['a.consumables_stock_info_id', 'outbound_num' => 'a.num', 'inbound_num' => 'b.num']);
                $query->leftJoin(['b' => ConsumablesStockInfo::tableName()], '{{a}}.consumables_stock_info_id = {{b}}.id');
                $query->where(['a.consumables_outbound_id' => $id, 'a.spot_id' => $this->spotId]);
                $outboundInfoResult = $query->all();
                if ($outboundInfoResult) {
                    foreach ($outboundInfoResult as $v) {
                        $outboundNumArray[$v['consumables_stock_info_id']]['outbound_num'][] = $v['outbound_num'];
                        $outboundNumArray[$v['consumables_stock_info_id']]['inbound_num'] = $v['inbound_num'];
                    }
                    foreach ($outboundNumArray as $key => $v) {
                        $totals = array_sum($v['outbound_num']);
                        //若出库总数量大于库存数量，则提示
                        if ($totals > $v['inbound_num']) {
                            Yii::$app->getSession()->setFlash('error', '出库数大于库存数');
                            return ['forceClose' => true, 'forceReloadPage' => true];
//                             return $this->render('outbound-apply', [
//                                 'dataProvider' => $dataProvider,
//                                 'model' => $model,
//                             ]);
                        }
                        $rows[$key] = $totals;
                    }
                    //更新库存总数量
                    foreach ($rows as $k => $num) {
                        $stockInfoModel = ConsumablesStockInfo::findOne(['id' => $k, 'spot_id' => $this->spotId]);
                        $stockInfoModel->scenario = 'outboundApply';
                        $stockInfoModel->num = $stockInfoModel->num - $num;
                        $stockInfoModel->save();
                    }
                    $model->status = $status;
                    $model->apply_user_id = $this->userInfo->id;
                    if ($model->save()) {
                        $dbTrans->commit();
                        Yii::$app->getSession()->setFlash('success', '操作成功');
                        return ['forceClose' => true, 'forceRedirect' => Url::to(['@stockIndexConsumablesOutboundIndex'])];

//                         return $this->redirect(Url::to(['@stockIndexOutboundIndex']));
                    }
                    $dbTrans->rollBack();
                }
            } catch (Exception $e) {

                $dbTrans->rollBack();
            }
        }
        return $this->render('outbound-apply', [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
        ]);
    }

    /**
     * @property 查看出库信息
     * @param  非药品出库公共信息表id $id
     */
    public function actionConsumablesOutboundView($id) {
        $model = $this->findOutboundInfo($id, [1, 3]);
        $dataProvider = $this->getDataProvider($id);
        $applyName = User::getUserInfo($model->apply_user_id, ['username'])['username'];
        return $this->render('outbound-apply', [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
                    'applyName' => $applyName
        ]);
    }

    /**
     * Finds the ConsumablesStock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ConsumablesStock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findInboundModel($id) {
        if (($model = ConsumablesStock::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @param $id
     * @param int $status
     * @return array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    private function findOutboundInfo($id, $status = 2) {
        $query = new ActiveQuery(ConsumablesOutbound::className());
        $query->from(['a' => ConsumablesOutbound::tableName()]);
        $query->select(['a.*', 'department_name' => 'b.name', 'c.username']);
        $query->leftJoin(['b' => SecondDepartment::tableName()], '{{a}}.leading_department_id = {{b}}.id');
        $query->leftJoin(['c' => User::tableName()], '{{a}}.leading_user_id = {{c}}.id');
        $query->where(['a.id' => $id, 'a.spot_id' => $this->spotId, 'a.status' => $status]);
        $model = $query->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @return 返回出库信息列表
     * @param 非药品出库公共信息表id $id
     */
    private function getDataProvider($id) {
        $query = new ActiveQuery(ConsumablesOutboundInfo::className());
        $query->from(['a' => ConsumablesOutboundInfo::tableName()]);
        $query->select(['a.id', 'a.consumables_stock_info_id', 'a.num', 'b.consumables_id', 'inbound_num' => 'b.num', 'b.default_price', 'b.expire_time', 'c.name', 'c.specification', 'c.unit', 'c.manufactor', 'c.product_number']);
        $query->leftJoin(['b' => ConsumablesStockInfo::tableName()], '{{a}}.consumables_stock_info_id = {{b}}.id');
        $query->leftJoin(['c' => Consumables::tableName()], '{{b}}.consumables_id = {{c}}.id');
        $query->where(['a.consumables_outbound_id' => $id, 'a.spot_id' => $this->spotId]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
    }

    /**
     *
     * @param 非药品库存公共信息表id $id
     * @return 返回该记录数据
     */
    private function findConsumablesStockModel($id, $status = 2) {
        $query = new ActiveQuery(ConsumablesStock::className());
        $query->from(['a' => ConsumablesStock::tableName()]);
        $query->select(['a.id', 'a.spot_id', 'a.inbound_time', 'a.inbound_type', 'a.supplier_id', 'a.user_id', 'a.apply_user_id', 'a.status', 'a.remark', 'a.create_time', 'a.update_time', 'supplierName' => 'b.name', 'c.username']);
        $query->leftJoin(['b' => SupplierConfig::tableName()], '{{a}}.supplier_id = {{b}}.id');
        $query->leftJoin(['c' => User::tableName()], '{{a}}.user_id = {{c}}.id');
        $query->where(['a.id' => $id, 'a.spot_id' => $this->spotId, 'a.status' => $status]);
        $model = $query->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     *
     * @param 非药品库存公共信息表id $id
     * @return 返回非药品库存信息详情表ar记录
     */
    private function getInboundUpdateData($id) {
        $query = new ActiveQuery(ConsumablesStockInfo::className());
        $query->from(['a' => ConsumablesStockInfo::tableName()]);
        $query->select(['a.id', 'a.consumables_id', 'a.total_num', 'a.invoice_number', 'a.default_price', 'a.expire_time', 'b.name', 'b.specification', 'b.unit', 'b.manufactor', 'b.product_number']);
        $query->leftJoin(['b' => Consumables::tableName()], '{{a}}.consumables_id = {{b}}.id');
        $query->where(['a.consumables_stock_id' => $id, 'a.spot_id' => $this->spotId]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
    }

    /**
     * @return string
     * @desc string 医疗耗材库存管理列表
     */
    public function actionConsumablesStockInfo() {

        $searchModel = new ConsumablesStockInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        $consumablesId = [];
        foreach ($dataProvider->getModels() as $model) {
            $consumablesId[] = $model['consumables_id'];
        }
        $status = isset(Yii::$app->request->queryParams['ConsumablesStockInfoSearch']) ? Yii::$app->request->queryParams['ConsumablesStockInfoSearch']['status'] : 0;
        $numArr = ConsumablesStockInfo::getStockByConsumables($consumablesId, $status);
        $data = [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'numArr' => $numArr
        ];
        return $this->render('stock-info', $data);
    }

}
