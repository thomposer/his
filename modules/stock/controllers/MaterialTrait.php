<?php

namespace app\modules\stock\controllers;

use app\common\base\MultiModel;
use app\modules\stock\models\MaterialOutbound;
use app\modules\stock\models\MaterialOutboundInfo;
use app\modules\stock\models\MaterialStock;
use app\modules\stock\models\MaterialStockInfo;
use app\modules\stock\models\search\MaterialOutboundSearch;
use app\modules\stock\models\search\MaterialStockInfoSearch;
use app\modules\stock\models\search\MaterialStockSearch;
use app\modules\spot\models\SupplierConfig;
use app\modules\spot_set\models\Material;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * IndexController implements the CRUD actions for MaterialStock model.
 */
trait MaterialTrait
{

    /**
     * Lists all MaterialStock models.
     * @return mixed
     */
    public function actionMaterialInboundIndex() {
        $searchModel = new MaterialStockSearch();
        $params = Yii::$app->request->queryParams;
        if (isset($params['ValidSearch']['status']) && $params['ValidSearch']['status'] == 2) {//单号
            $dataProvider = $searchModel->searchDrugs($params, $this->pageSize);
            $view = '/material/inbound-drugs';
        } else {
            $dataProvider = $searchModel->search($params, $this->pageSize);
            $view = '/material/inbound-index';
        }
        return $this->render($view, [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new MaterialStock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionMaterialInboundCreate() {
        $request = Yii::$app->request;
        $model = new MultiModel([
            'models' => [
                'stock' => new MaterialStock(),
                'stockInfo' => new MaterialStockInfo()
            ]
        ]);
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $stockInfoModel = $model->getModel('stockInfo');
                    if (isset($stockInfoModel->material_id)) {
                        $result = $model->getModel('stock')->save();
                        if ($result) {
                            $rows = [];
                            if (count($stockInfoModel->material_id) > 0) {
                                foreach ($stockInfoModel->material_id as $key => $v) {
                                    if ($stockInfoModel->deleted[$key] == null) {
                                        $rows[] = [
                                            $this->spotId,
                                            $model->getModel('stock')->id,
                                            $v,
                                            $stockInfoModel->total_num[$key],
                                            $stockInfoModel->total_num[$key],
                                            $stockInfoModel->invoice_number[$key] ? $stockInfoModel->invoice_number[$key] : '',
                                            $stockInfoModel->default_price[$key],
                                            strtotime($stockInfoModel->expire_time[$key]),
                                            time(),
                                            time()
                                        ];
                                    }
                                }
                            }
                            if (count($rows) > 0) {
                                Yii::$app->db->createCommand()->batchInsert(MaterialStockInfo::tableName(), ['spot_id', 'material_stock_id', 'material_id', 'total_num', 'num', 'invoice_number', 'default_price', 'expire_time', 'create_time', 'update_time'], $rows)->execute();
                            }
                        }
                    } else {
                        $this->result['errorCode'] = 1001;
                        $this->result['msg'] = '请选择入库其他';
                        return $this->result;
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    Yii::error(json_encode($e->errorInfo), 'material-inbound-create');
                    $dbTrans->rollBack();
                }
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = $model->errors['stockInfo'][0][0];
                return $this->result;
            }
        } else {
            $supplierConfig = SupplierConfig::getList();
            $materialList = Material::getList(['id', 'name', 'product_number', 'specification', 'type', 'unit', 'manufactor', 'price', 'default_price', 'remark'], ['status' => 1, 'attribute' => 2]);
            $query = MaterialStockInfo::find()->select(['id'])->where(['id' => 0]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
                'sort' => [
                    'attributes' => ['']
                ]
            ]);
            return $this->render('/material/inbound-create', [
                        'model' => $model,
                        'supplierConfig' => $supplierConfig,
                        'materialList' => $materialList,
                        'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Updates an existing MaterialStock model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionMaterialInboundUpdate($id) {
        $request = Yii::$app->request;
        $stockModel = $this->findMaterialStockModel($id);
        $stockModel->inbound_time = date('Y-m-d', $stockModel->inbound_time);
        $model = new MultiModel([
            'models' => [
                'stock' => $stockModel,
                'stockInfo' => new MaterialStockInfo()
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
                        if (count($stockInfoModel->material_id) > 0) {
                            foreach ($stockInfoModel->material_id as $key => $v) {
                                //新增或者修改的
                                if ($stockInfoModel->deleted[$key] != 1) {
                                    if ($stockInfoModel->materialStockInfoId[$key] == null) {
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

                                        $db->createCommand()->update(MaterialStockInfo::tableName(), [
                                            'total_num' => $stockInfoModel->total_num[$key],
                                            'num' => $stockInfoModel->total_num[$key],
                                            'invoice_number' => $stockInfoModel->invoice_number[$key] ? $stockInfoModel->invoice_number[$key] : '',
                                            'default_price' => $stockInfoModel->default_price[$key],
                                            'expire_time' => strtotime($stockInfoModel->expire_time[$key]),
                                            'update_time' => time()
                                                ], ['id' => $stockInfoModel->materialStockInfoId[$key], 'material_stock_id' => $id, 'spot_id' => $this->spotId])->execute();
                                    }
                                } else {
                                    //删除操作
                                    //若id值不为null，则直接删除该记录
                                    if ($stockInfoModel->materialStockInfoId[$key] != null) {
                                        $db->createCommand()->delete(MaterialStockInfo::tableName(), ['id' => $stockInfoModel->materialStockInfoId[$key], 'material_stock_id' => $id, 'spot_id' => $this->spotId])->execute();
                                    }
                                }
                            }
                            if (count($rows) > 0) {
                                $db->createCommand()->batchInsert(MaterialStockInfo::tableName(), ['spot_id', 'material_stock_id', 'material_id', 'total_num', 'num', 'invoice_number', 'default_price', 'expire_time', 'create_time', 'update_time'], $rows)->execute();
                            }
                        } else {
                            $this->result['errorCode'] = 1001;
                            $this->result['msg'] = '请选择入库其他';
                            return $this->result;
                        }
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    Yii::error(json_encode($e->errorInfo, true), 'material-inbound-update');
                }
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = $model->errors['stockInfo'][0][0];
                return $this->result;
            }
        } else {
            $supplierConfig = SupplierConfig::getList();
            $materialList = Material::getList(['id', 'name', 'product_number', 'specification', 'type', 'unit', 'manufactor', 'price', 'default_price', 'remark'], ['status' => 1, 'attribute' => 2]);
            $dataProvider = $this->getMaterialInboundUpdateData($id);
            return $this->render('/material/inbound-update', [
                        'model' => $model,
                        'supplierConfig' => $supplierConfig,
                        'materialList' => $materialList,
                        'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * @property 审核入库信息
     * @param  其他库存公共信息表id $id
     */
    public function actionMaterialInboundApply($id, $status = null) {
        $model = $this->findMaterialStockModel($id);
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
                return ['forceClose' => true, 'forceRedirect' => Url::to(['@materialIndexInboundIndex'])];
            }
        } else {
            $dataProvider = $this->getMaterialInboundUpdateData($id);
            return $this->render('/material/inbound-apply', [
                        'dataProvider' => $dataProvider,
                        'model' => $model,
            ]);
        }
    }

    /**
     * @property 查看入库信息
     * @param  其他库存公共信息表id $id
     */
    public function actionMaterialInboundView($id) {
        $model = $this->findMaterialStockModel($id, [1, 3]);
        $applyName = User::getUserInfo($model->apply_user_id, ['username'])['username'];
        $dataProvider = $this->getMaterialInboundUpdateData($id);
        return $this->render('/material/inbound-apply', [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
                    'applyName' => $applyName
        ]);
    }

    /**
     * Delete an existing MaterialStock model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionMaterialInboundDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            $this->findMaterialInboundModel($id)->delete();
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
    public function actionMaterialOutboundIndex() {
        $searchModel = new MaterialOutboundSearch();
        $params = Yii::$app->request->queryParams;
        if (isset($params['ValidSearch']['status']) && $params['ValidSearch']['status'] == 2) {//名称
            $dataProvider = $searchModel->searchDrugs($params, $this->pageSize);
            $view = '/material/outbound-drugs';
        } else {
            $dataProvider = $searchModel->search($params, $this->pageSize);
            $view = '/material/outbound-index';
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
    public function actionMaterialOutboundCreate() {
        $request = Yii::$app->request;
        $model = new MultiModel([
            'models' => [
                'outbound' => new MaterialOutbound(),
                'outboundInfo' => new MaterialOutboundInfo()
            ]
        ]);
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $outboundInfoModel = $model->getModel('outboundInfo');
                    if (isset($outboundInfoModel->material_stock_info_id)) {
                        $result = $model->getModel('outbound')->save();
                        if ($result) {
                            $rows = [];
                            foreach ($outboundInfoModel->material_stock_info_id as $key => $v) {
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
                                Yii::$app->db->createCommand()->batchInsert(MaterialOutboundInfo::tableName(), ['spot_id', 'material_outbound_id', 'material_stock_info_id', 'num', 'create_time', 'update_time'], $rows)->execute();
                            }
                        }
                    } else {
                        $this->result['errorCode'] = true;
                        $this->result['msg'] = '请选择出库其他';
                        return $this->result;
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    Yii::error(json_encode($e->errorInfo), 'material-outbound-create');
                }
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = $model->errors['outboundInfo'][0][0];
                return $this->result;
            }
        } else {
            $departmentList = SecondDepartment::getList();
            $userList = User::getUserList($this->spotId);
            $query = MaterialOutboundInfo::find()->select(['id'])->where(['id' => 0]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
                'sort' => [
                    'attributes' => ['']
                ]
            ]);
            $materialList = MaterialStockInfo::getList();
            return $this->render('/material/outbound-create', [
                        'model' => $model,
                        'departmentList' => $departmentList,
                        'userList' => $userList,
                        'dataProvider' => $dataProvider,
                        'materialList' => $materialList,
            ]);
        }
    }

    public function actionMaterialOutboundUpdate($id) {
        $request = Yii::$app->request;
        $outboundInfo = $this->findMaterialOutboundInfo($id);
        $outboundInfo->outbound_time = date('Y-m-d', $outboundInfo->outbound_time);
        $model = new MultiModel([
            'models' => [
                'outbound' => $outboundInfo,
                'outboundInfo' => new MaterialOutboundInfo()
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
                        foreach ($outboundInfoModel->material_stock_info_id as $key => $v) {
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
                                    $db->createCommand()->update(MaterialOutboundInfo::tableName(), [
//                                         'material_stock_info_id' => $v,
                                        'num' => $outboundInfoModel->num[$key],
                                        'update_time' => time()
                                            ], [
                                        'id' => $outboundInfoModel->outboundInfoId[$key],
                                        'spot_id' => $this->spotId,
                                        'material_outbound_id' => $model->getModel('outbound')->id
                                    ])->execute();
                                }
                            } else {
                                //删除操作
                                if ($outboundInfoModel->outboundInfoId[$key] != null) {
                                    $db->createCommand()->delete(MaterialOutboundInfo::tableName(), ['id' => $outboundInfoModel->outboundInfoId[$key], 'spot_id' => $this->spotId, 'material_outbound_id' => $model->getModel('outbound')->id])->execute();
                                }
                            }
                        }
                        if (count($rows) > 0) {
                            Yii::$app->db->createCommand()->batchInsert(MaterialOutboundInfo::tableName(), ['spot_id', 'material_outbound_id', 'material_stock_info_id', 'num', 'create_time', 'update_time'], $rows)->execute();
                        }
                    }
                    $this->result['errorCode'] = 0;
                    $this->result['msg'] = '保存成功';
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTran->commit();
                    return $this->result;
                } catch (Exception $e) {
                    $dbTran->rollBack();
                    Yii::error(json_encode($e->errorInfo), 'material-outbound-update');
                }
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = $model->errors['outboundInfo'][0][0];
                return $this->result;
            }
        } else {
            $departmentList = SecondDepartment::getList();
            $userList = User::getUserList($this->spotId);
            $dataProvider = $this->getMaterialDataProvider($id);
            $materialList = MaterialStockInfo::getList();
            return $this->render('/material/outbound-update', [
                        'model' => $model,
                        'departmentList' => $departmentList,
                        'userList' => $userList,
                        'dataProvider' => $dataProvider,
                        'materialList' => $materialList,
            ]);
        }
    }

    /**
     *
     * @param 其他出库公共信息表id $id
     * @property 删除出库记录
     * @return Response
     */
    public function actionMaterialOutboundDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $this->findMaterialOutboundInfo($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceReload' => '#crud-datatable-pjax', 'forceClose' => true,];
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * @property 审核出库信息
     * @param  其他出库公共信息表id $id
     */
    public function actionMaterialOutboundApply($id, $status = null) {
        $model = $this->findMaterialOutboundInfo($id);
        $model->scenario = 'outboundApply';
        $dataProvider = $this->getMaterialDataProvider($id);
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
                    return ['forceClose' => true, 'forceRedirect' => Url::to(['@materialIndexOutboundIndex'])];
                }
            }
            $db = Yii::$app->db;
            $dbTrans = $db->beginTransaction();
            try {
                $outboundNumArray = [];
                $rows = [];
                $totals = '';
                $query = new Query();
                $query->from(['a' => MaterialOutboundInfo::tableName()]);
                $query->select(['a.material_stock_info_id', 'outbound_num' => 'a.num', 'inbound_num' => 'b.num']);
                $query->leftJoin(['b' => MaterialStockInfo::tableName()], '{{a}}.material_stock_info_id = {{b}}.id');
                $query->where(['a.material_outbound_id' => $id, 'a.spot_id' => $this->spotId]);
                $outboundInfoResult = $query->all();
                if ($outboundInfoResult) {
                    foreach ($outboundInfoResult as $v) {
                        $outboundNumArray[$v['material_stock_info_id']]['outbound_num'][] = $v['outbound_num'];
                        $outboundNumArray[$v['material_stock_info_id']]['inbound_num'] = $v['inbound_num'];
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
                        $stockInfoModel = MaterialStockInfo::findOne(['id' => $k, 'spot_id' => $this->spotId]);
                        $stockInfoModel->scenario = 'outboundApply';
                        $stockInfoModel->num = $stockInfoModel->num - $num;
                        $stockInfoModel->save();
                    }
                    $model->status = $status;
                    $model->apply_user_id = $this->userInfo->id;
                    if ($model->save()) {
                        $dbTrans->commit();
                        Yii::$app->getSession()->setFlash('success', '操作成功');
                        return ['forceClose' => true, 'forceRedirect' => Url::to(['@materialIndexOutboundIndex'])];

//                         return $this->redirect(Url::to(['@materialIndexOutboundIndex']));
                    }
                    $dbTrans->rollBack();
                }else{
                    return ['forceClose' => true,'forceMessage'=>'出库失败','forceType'=>2];
                }
            } catch (Exception $e) {

                $dbTrans->rollBack();
            }
        }
        return $this->render('/material/outbound-apply', [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
        ]);
    }

    /**
     * @property 查看出库信息
     * @param  其他出库公共信息表id $id
     */
    public function actionMaterialOutboundView($id) {
        $model = $this->findMaterialOutboundInfo($id, [1, 3]);
        $dataProvider = $this->getMaterialDataProvider($id);
        $applyName = User::getUserInfo($model->apply_user_id, ['username'])['username'];
        return $this->render('/material/outbound-apply', [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
                    'applyName' => $applyName
        ]);
    }

    /**
     * Finds the MaterialStock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MaterialStock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findMaterialInboundModel($id) {
        if (($model = MaterialStock::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @param $id
     * @param int $status
     * @return array|null|ActiveRecord
     * @throws NotFoundHttpException
     */
    private function findMaterialOutboundInfo($id, $status = 2) {
        $query = new ActiveQuery(MaterialOutbound::className());
        $query->from(['a' => MaterialOutbound::tableName()]);
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
     * @param 其他出库公共信息表id $id
     */
    private function getMaterialDataProvider($id) {
        $query = new ActiveQuery(MaterialOutboundInfo::className());
        $query->from(['a' => MaterialOutboundInfo::tableName()]);
        $query->select(['a.id', 'a.material_stock_info_id', 'a.num', 'b.material_id', 'inbound_num' => 'b.num', 'b.default_price', 'b.expire_time', 'c.name', 'c.price', 'c.specification', 'c.unit', 'c.manufactor', 'c.product_number']);
        $query->leftJoin(['b' => MaterialStockInfo::tableName()], '{{a}}.material_stock_info_id = {{b}}.id');
        $query->leftJoin(['c' => Material::tableName()], '{{b}}.material_id = {{c}}.id');
        $query->where(['a.material_outbound_id' => $id, 'a.spot_id' => $this->spotId]);
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
    private function findMaterialStockModel($id, $status = 2) {
        $query = new ActiveQuery(MaterialStock::className());
        $query->from(['a' => MaterialStock::tableName()]);
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
    private function getMaterialInboundUpdateData($id) {
        $query = new ActiveQuery(MaterialStockInfo::className());
        $query->from(['a' => MaterialStockInfo::tableName()]);
        $query->select(['a.id', 'a.material_id', 'a.total_num', 'a.invoice_number', 'a.default_price', 'a.expire_time', 'b.name', 'b.specification', 'b.unit', 'b.manufactor', 'b.product_number']);
        $query->leftJoin(['b' => Material::tableName()], '{{a}}.material_id = {{b}}.id');
        $query->where(['a.material_stock_id' => $id, 'a.spot_id' => $this->spotId]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
    }

    public function actionMaterialStockInfo() {

        $searchModel = new MaterialStockInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        $counts = [];
        $materialId = [];
        foreach ($dataProvider->getModels() as $model) {
            $materialId[] = $model['material_id'];
        }
        $status = isset(Yii::$app->request->queryParams['MaterialStockInfoSearch']) ? Yii::$app->request->queryParams['MaterialStockInfoSearch']['status'] : 0;
        $numArr = MaterialStockInfo::getStockByMaterial($materialId, $status);
        $data = [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'numArr' => $numArr
        ];
        return $this->render('/material/stock-info', $data);
    }

}
