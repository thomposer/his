<?php

/*
 * time: 2017-10-19 11:20:23.
 * author : yu.li.
 */

namespace app\modules\stock\controllers;

use app\common\base\MultiModel;
use app\modules\stock\models\Outbound;
use app\modules\stock\models\OutboundInfo;
use app\modules\stock\models\search\InboundSearch;
use app\modules\stock\models\search\OutboundSearch;
use app\modules\stock\models\search\StockInfoSearch;
use app\modules\stock\models\Stock;
use app\modules\stock\models\StockInfo;
use app\modules\spot\models\RecipeList;
use app\modules\spot\models\SupplierConfig;
use app\modules\spot_set\models\RecipelistClinic;
use app\modules\spot_set\models\SecondDepartment;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\modules\user\models\User;
use PHPExcel;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Style_Color;
use PHPExcel_Cell_DataType;


/**
 * Description of PharmacyTrait
 *
 * @author Administrator
 */
trait PharmacyTrait
{

    /**
     * @property 入库管理
     */
    public function actionPharmacyInboundIndex() {
        $searchModel = new InboundSearch();
        $params = Yii::$app->request->queryParams;
        if (isset($params['ValidSearch']['status']) && $params['ValidSearch']['status'] == 2) {//单号
            $dataProvider = $searchModel->searchDrugs($params, $this->pageSize);
            $view = '/pharmacy/inbound-drugs';
        } else {
            $dataProvider = $searchModel->search($params, $this->pageSize);
            $view = '/pharmacy/inbound-index';
        }
        $data = [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ];
//        if (Yii::$app->request->isPjax) {
//
//            return $this->renderAjax('inbound-index', $data);
//        }
        return $this->render($view, $data);
    }

    /**
     * @property 库存管理
     * @return string
     */
    public function actionPharmacyStockInfo() {
        $searchModel = new StockInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        $counts = [];
        $recipeId = [];
        foreach ($dataProvider->getModels() as $model) {
            $recipeId[] = $model['recipe_id'];
        }
        $status = isset(Yii::$app->request->queryParams['ValidSearch']) ? Yii::$app->request->queryParams['ValidSearch']['status'] : 0;
        $numArr = StockInfo::getStockByRecipe($recipeId, $status);
        $data = [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'numArr' => $numArr
        ];
        return $this->render('/pharmacy/stock-info', $data);
    }

    /**
     * Creates a new RecipeRecord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionPharmacyInboundCreate() {
        $request = Yii::$app->request;
        $model = new MultiModel([
            'models' => [
                'stock' => new Stock(),
                'stockInfo' => new StockInfo()
            ]
        ]);
        if ($request->isAjax) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $stockInfoModel = $model->getModel('stockInfo');
                    if (isset($stockInfoModel->recipe_id)) {
                        $result = $model->getModel('stock')->save();
                        if ($result) {
                            $rows = [];
                            if (count($stockInfoModel->recipe_id) > 0) {
                                foreach ($stockInfoModel->recipe_id as $key => $v) {
                                    if ($stockInfoModel->deleted[$key] == null) {
                                        $rows[] = [
                                            $this->spotId,
                                            $model->getModel('stock')->id,
                                            $v,
                                            $stockInfoModel->total_num[$key],
                                            $stockInfoModel->total_num[$key],
                                            $stockInfoModel->invoice_number[$key] ? $stockInfoModel->invoice_number[$key] : '',
                                            $stockInfoModel->default_price[$key],
                                            $stockInfoModel->batch_number[$key],
                                            strtotime($stockInfoModel->expire_time[$key]),
                                            time(),
                                            time()
                                        ];
                                    }
                                }
                            }
                            if (count($rows) > 0) {
                                Yii::$app->db->createCommand()->batchInsert(StockInfo::tableName(), ['spot_id', 'stock_id', 'recipe_id', 'total_num', 'num', 'invoice_number', 'default_price', 'batch_number', 'expire_time', 'create_time', 'update_time'], $rows)->execute();
                            }
                        }
                    } else {
                        $this->result['errorCode'] = 1001;
                        $this->result['msg'] = '请选择入库药品';
                        return Json::encode($this->result);
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return Json::encode($this->result);
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                }
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = $model->errors['stockInfo'][0][0];
                return Json::encode($this->result);
            }
        } else {
            $supplierConfig = SupplierConfig::getList();
            $recipeList = RecipelistClinic::getList();
            $query = StockInfo::find()->select(['id'])->where(['id' => 0]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
                'sort' => [
                    'attributes' => ['']
                ]
            ]);
            return $this->render('/pharmacy/inbound-create', [
                        'model' => $model,
                        'supplierConfig' => $supplierConfig,
                        'recipeList' => $recipeList,
                        'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Updates an existing RecipeRecord model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionPharmacyInboundUpdate($id) {
        $request = Yii::$app->request;
        $stockModel = $this->findPharmacyStockModel($id);
        $stockModel->inbound_time = date('Y-m-d', $stockModel->inbound_time);
        $model = new MultiModel([
            'models' => [
                'stock' => $stockModel,
                'stockInfo' => new StockInfo()
            ]
        ]);
        if ($request->isAjax) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $result = $model->getModel('stock')->save();
                    if ($result) {
                        $rows = [];
                        $db = Yii::$app->db;
                        $stockInfoModel = $model->getModel('stockInfo');
                        if (count($stockInfoModel->recipe_id) > 0) {
                            foreach ($stockInfoModel->recipe_id as $key => $v) {
                                //新增或者修改的
                                if ($stockInfoModel->deleted[$key] != 1) {
                                    if ($stockInfoModel->stockInfoId[$key] == null) {
                                        $rows[] = [
                                            $this->spotId,
                                            $id,
                                            $v,
                                            $stockInfoModel->total_num[$key],
                                            $stockInfoModel->total_num[$key],
                                            $stockInfoModel->invoice_number[$key] ? $stockInfoModel->invoice_number[$key] : '',
                                            $stockInfoModel->default_price[$key],
                                            $stockInfoModel->batch_number[$key],
                                            strtotime($stockInfoModel->expire_time[$key]),
                                            time(),
                                            time()
                                        ];
                                    } else {

                                        $db->createCommand()->update(StockInfo::tableName(), [
                                            'total_num' => $stockInfoModel->total_num[$key],
                                            'num' => $stockInfoModel->total_num[$key],
                                            'invoice_number' => $stockInfoModel->invoice_number[$key] ? $stockInfoModel->invoice_number[$key] : '',
                                            'default_price' => $stockInfoModel->default_price[$key],
                                            'batch_number' => $stockInfoModel->batch_number[$key],
                                            'expire_time' => strtotime($stockInfoModel->expire_time[$key]),
                                            'update_time' => time()
                                                ], ['id' => $stockInfoModel->stockInfoId[$key], 'spot_id' => $this->spotId])->execute();
                                    }
                                } else {
                                    //删除操作
                                    //若id值不为null，则直接删除该记录
                                    if ($stockInfoModel->stockInfoId[$key] != null) {
                                        $db->createCommand()->delete(StockInfo::tableName(), ['id' => $stockInfoModel->stockInfoId[$key], 'spot_id' => $this->spotId])->execute();
                                    }
                                }
                            }
                            if (count($rows) > 0) {
                                $db->createCommand()->batchInsert(StockInfo::tableName(), ['spot_id', 'stock_id', 'recipe_id', 'total_num', 'num', 'invoice_number', 'default_price', 'batch_number', 'expire_time', 'create_time', 'update_time'], $rows)->execute();
                            }
                        } else {
                            $this->result['errorCode'] = 1001;
                            $this->result['msg'] = '请选择入库药品';
                            return Json::encode($this->result);
                        }
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return Json::encode($this->result);
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                }
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = $model->errors['stockInfo'][0][0];
                return Json::encode($this->result);
            }
        } else {
            $supplierConfig = SupplierConfig::getList();
            $recipeList = RecipelistClinic::getList();
            $dataProvider = $this->getPharmacyInboundUpdateData($id);
            return $this->render('/pharmacy/inbound-update', [
                        'model' => $model,
                        'supplierConfig' => $supplierConfig,
                        'recipeList' => $recipeList,
                        'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * @property 审核入库信息
     * @param  药房库存公共信息表id $id
     */
    public function actionPharmacyInboundApply($id) {
        $model = $this->findPharmacyStockModel($id);
        $model->scenario = 'inboundApply';
        if (Yii::$app->request->isPost) {
            $model->status = 1;
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', '审核成功');
                return $this->redirect(Url::to(['@pharmacyIndexInboundIndex']));
            }
        }
        $dataProvider = $this->getPharmacyInboundUpdateData($id);
        return $this->render('/pharmacy/inbound-apply', [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
        ]);
    }

    /**
     * @property 查看入库信息
     * @param  药房库存公共信息表id $id
     */
    public function actionPharmacyInboundView($id) {
        $model = $this->findPharmacyStockModel($id, 1);
        $dataProvider = $this->getPharmacyInboundUpdateData($id);
        return $this->render('/pharmacy/inbound-apply', [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
        ]);
    }

    /**
     *
     * @param 药房库存公共信息表id $id
     * @return 返回药房库存信息详情表ar记录
     */
    private function getPharmacyInboundUpdateData($id) {
        $query = new ActiveQuery(StockInfo::className());
        $query->from(['a' => StockInfo::tableName()]);
        $query->select(['a.id', 'a.recipe_id', 'a.total_num', 'a.default_price', 'a.invoice_number', 'a.batch_number', 'a.expire_time', 'b.name', 'b.specification', 'b.unit', 'b.manufactor', 'c.price']);
        $query->leftJoin(['b' => RecipeList::tableName()], '{{a}}.recipe_id = {{b}}.id');
        $query->leftJoin(['c' => RecipelistClinic::tableName()], '{{b}}.id = {{c}}.recipelist_id');
        $query->where(['a.stock_id' => $id, 'a.spot_id' => $this->spotId]);
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
     * Lists all Outbound models.
     * @property 出库管理
     * @return mixed
     */
    public function actionPharmacyOutboundIndex() {
        $searchModel = new OutboundSearch();
        $params = Yii::$app->request->queryParams;
        if (isset($params['ValidSearch']['status']) && $params['ValidSearch']['status'] == 2) {//单号
            $dataProvider = $searchModel->searchDrugs($params, $this->pageSize);
            $view = '/pharmacy/outbound-drugs';
        } else {
            $dataProvider = $searchModel->search($params, $this->pageSize);
            $view = '/pharmacy/outbound-index';
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
    public function actionPharmacyOutboundCreate() {
        $request = Yii::$app->request;
        $model = new MultiModel([
            'models' => [
                'outbound' => new Outbound(),
                'outboundInfo' => new OutboundInfo()
            ]
        ]);
        if ($request->isAjax) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $outboundInfoModel = $model->getModel('outboundInfo');
                    if (isset($outboundInfoModel->stock_info_id)) {
                        $result = $model->getModel('outbound')->save();
                        if ($result) {
                            $rows = [];
                            foreach ($outboundInfoModel->stock_info_id as $key => $v) {
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
                                Yii::$app->db->createCommand()->batchInsert(OutboundInfo::tableName(), ['spot_id', 'outbound_id', 'stock_info_id', 'num', 'create_time', 'update_time'], $rows)->execute();
                            }
                        }
                    } else {
                        $this->result['errorCode'] = true;
                        $this->result['msg'] = '请选择出库药品';
                        return Json::encode($this->result);
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return Json::encode($this->result);
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                }
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = $model->errors['outboundInfo'][0][0];
                return Json::encode($this->result);
            }
        } else {
            $departmentList = SecondDepartment::getList();
            $userList = User::getUserList($this->spotId);
            $query = OutboundInfo::find()->select(['id'])->where(['id' => 0]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
                'sort' => [
                    'attributes' => ['']
                ]
            ]);
            $recipeList = StockInfo::getList();
            return $this->render('/pharmacy/outbound-create', [
                        'model' => $model,
                        'departmentList' => $departmentList,
                        'userList' => $userList,
                        'dataProvider' => $dataProvider,
                        'recipeList' => $recipeList,
            ]);
        }
    }

    public function actionPharmacyOutboundUpdate($id) {
        $request = Yii::$app->request;
        $outboundInfo = $this->findPharmacyOutboundInfo($id);
        $outboundInfo->outbound_time = date('Y-m-d', $outboundInfo->outbound_time);
        $model = new MultiModel([
            'models' => [
                'outbound' => $outboundInfo,
                'outboundInfo' => new OutboundInfo()
            ]
        ]);
        if ($request->isAjax) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTran = Yii::$app->db->beginTransaction();
                try {
                    $result = $model->getModel('outbound')->save();
                    if ($result) {
                        $rows = [];
                        $outboundInfoModel = $model->getModel('outboundInfo');
                        $db = Yii::$app->db;
                        foreach ($outboundInfoModel->stock_info_id as $key => $v) {
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
                                    $db->createCommand()->update(OutboundInfo::tableName(), [
                                        'stock_info_id' => $v,
                                        'num' => $outboundInfoModel->num[$key],
                                        'update_time' => time()
                                            ], [
                                        'id' => $outboundInfoModel->outboundInfoId[$key],
                                        'spot_id' => $this->spotId,
                                        'outbound_id' => $model->getModel('outbound')->id
                                    ])->execute();
                                }
                            } else {
                                //删除操作
                                if ($outboundInfoModel->outboundInfoId[$key] != null) {
                                    $db->createCommand()->delete(OutboundInfo::tableName(), ['id' => $outboundInfoModel->outboundInfoId[$key], 'spot_id' => $this->spotId, 'outbound_id' => $model->getModel('outbound')->id])->execute();
                                }
                            }
                        }
                        if (count($rows) > 0) {
                            Yii::$app->db->createCommand()->batchInsert(OutboundInfo::tableName(), ['spot_id', 'outbound_id', 'stock_info_id', 'num', 'create_time', 'update_time'], $rows)->execute();
                        }
                    }
                    $this->result['errorCode'] = 0;
                    $this->result['msg'] = '保存成功';
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTran->commit();
                    return Json::encode($this->result);
                } catch (Exception $e) {
                    $dbTran->rollBack();
                }
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = $model->errors['outboundInfo'][0][0];
                return Json::encode($this->result);
            }
        } else {
            $departmentList = SecondDepartment::getList();
            $userList = User::getUserList($this->spotId);
            $dataProvider = $this->getPharmacyDataProvider($id);
            $recipeList = StockInfo::getList();
            return $this->render('/pharmacy/outbound-update', [
                        'model' => $model,
                        'departmentList' => $departmentList,
                        'userList' => $userList,
                        'dataProvider' => $dataProvider,
                        'recipeList' => $recipeList,
            ]);
        }
    }

    /**
     * @property 审核出库信息
     * @param  药房出库公共信息表id $id
     */
    public function actionPharmacyOutboundApply($id) {

        $model = $this->findPharmacyOutboundInfo($id);
        $model->scenario = 'outboundApply';
        $dataProvider = $this->getPharmacyDataProvider($id);
        if (Yii::$app->request->isPost) {
            $db = Yii::$app->db;
            $dbTrans = $db->beginTransaction();
            try {
                $outboundNumArray = [];
                $rows = [];
                $totals = '';
                $query = new Query();
                $query->from(['a' => OutboundInfo::tableName()]);
                $query->select(['a.stock_info_id', 'outbound_num' => 'a.num', 'inbound_num' => 'b.num']);
                $query->leftJoin(['b' => StockInfo::tableName()], '{{a}}.stock_info_id = {{b}}.id');
                $query->where(['a.outbound_id' => $id, 'a.spot_id' => $this->spotId]);
                $outboundInfoResult = $query->all();
                if ($outboundInfoResult) {
                    foreach ($outboundInfoResult as $v) {
                        $outboundNumArray[$v['stock_info_id']]['outbound_num'][] = $v['outbound_num'];
                        $outboundNumArray[$v['stock_info_id']]['inbound_num'] = $v['inbound_num'];
                    }
                    foreach ($outboundNumArray as $key => $v) {
                        $totals = array_sum($v['outbound_num']);
                        //若出库总数量大于库存数量，则提示
                        if ($totals > $v['inbound_num']) {
                            Yii::$app->getSession()->setFlash('error', '出库数大于库存数');
                            return $this->render('/pharmacy/outbound-apply', [
                                        'dataProvider' => $dataProvider,
                                        'model' => $model,
                            ]);
                        }
                        $rows[$key] = $totals;
                    }
                    //更新库存总数量
                    foreach ($rows as $k => $num) {
                        $stockInfoModel = StockInfo::findOne(['id' => $k, 'spot_id' => $this->spotId]);
                        $stockInfoModel->scenario = 'outboundApply';
                        $stockInfoModel->num = $stockInfoModel->num - $num;
                        $stockInfoModel->save();
                    }
                    $model->status = 1;
                    if ($model->save()) {
                        $dbTrans->commit();
                        Yii::$app->getSession()->setFlash('success', '审核成功');
                        return $this->redirect(Url::to(['@pharmacyIndexOutboundIndex']));
                    }
                    $dbTrans->rollBack();
                }
            } catch (Exception $e) {
                $dbTrans->rollBack();
            }
        }
        return $this->render('/pharmacy/outbound-apply', [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
        ]);
    }

    /**
     * @property 查看出库信息
     * @param  药房出库公共信息表id $id
     */
    public function actionPharmacyOutboundView($id) {
        $model = $this->findPharmacyOutboundInfo($id, 1);
        $dataProvider = $this->getPharmacyDataProvider($id);
        return $this->render('/pharmacy/outbound-apply', [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
        ]);
    }

    /**
     * @return 返回出库信息列表
     * @param 药房出库公共信息表id $id
     */
    private function getPharmacyDataProvider($id) {
        $query = new ActiveQuery(OutboundInfo::className());
        $query->from(['a' => OutboundInfo::tableName()]);
        $query->select(['a.id', 'a.stock_info_id', 'a.num', 'b.recipe_id', 'inbound_num' => 'b.num', 'b.default_price', 'b.batch_number', 'b.expire_time', 'c.name', 'd.price', 'c.specification', 'c.unit', 'c.manufactor']);
        $query->leftJoin(['b' => StockInfo::tableName()], '{{a}}.stock_info_id = {{b}}.id');
        $query->leftJoin(['c' => RecipeList::tableName()], '{{b}}.recipe_id = {{c}}.id');
        $query->leftJoin(['d' => RecipelistClinic::tableName()], '{{b}}.recipe_id = {{d}}.recipelist_id');
        $query->where(['a.outbound_id' => $id, 'a.spot_id' => $this->spotId]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
    }

    private function findPharmacyOutboundInfo($id, $status = 2) {
        $query = new ActiveQuery(Outbound::className());
        $query->from(['a' => Outbound::tableName()]);
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
     *
     * @param 药房库存公共信息表id $id
     * @return 返回该记录数据
     */
    private function findPharmacyStockModel($id, $status = 2) {
        $query = new ActiveQuery(Stock::className());
        $query->from(['a' => Stock::tableName()]);
        $query->select(['a.*', 'b.name', 'c.username']);
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
     * Deletes an existing Stock model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param 药房库存公共信息表id $id
     * @property 删除入库记录
     * @return mixed
     */
    public function actionPharmacyInboundDelete($id) {

        $request = Yii::$app->request;
        if ($request->isAjax) {
            $this->findPharmacyStockModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     *
     * @param 药房出库公共信息表id $id
     * @property 删除出库记录
     * @return Response    
     */
    public function actionPharmacyOutboundDelete($id) {

        $request = Yii::$app->request;
        if ($request->isAjax) {
            $this->findPharmacyOutboundInfo($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceReload' => '#crud-datatable-pjax', 'forceClose' => true,];
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }


    /**
     * 处方导出excel
     */
    public function actionStockExportData(){
        $searchModel = new StockInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, false);
        $exportData = $dataProvider->getModels();
        $result =   array();
        foreach($exportData as $k=>$v){
            $result[$v['recipe_id']]['shelves']    =   $v['shelves'];
            $result[$v['recipe_id']]['name']    =   $v['name'];
            $result[$v['recipe_id']]['specification']    =   $v['specification'];
            $result[$v['recipe_id']]['manufactor']    =   $v['manufactor'];
            $result[$v['recipe_id']]['unit']    =   $v['unit'];
            $result[$v['recipe_id']]['item'][]    =   $v;
            $result[$v['recipe_id']]['count']    =   $result[$v['recipe_id']]['count'] + $v['num'];
        }
        $data = array_values($result);

        $objectPHPExcel = new PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);
        $objectPHPExcel->setActiveSheetIndex(0);
        $n = 2;
        foreach ($data as $key => $value) {
            //明细的输出
            $objectPHPExcel->getActiveSheet()->setCellValue('A' . ($n ), $value['shelves']);
            $objectPHPExcel->getActiveSheet()->setCellValue('B' . ($n), " " . $value['name']);
            $objectPHPExcel->getActiveSheet()->setCellValue('C' . ($n), " " . $value['specification']);
            $objectPHPExcel->getActiveSheet()->setCellValue('D' . ($n), " " . $value['manufactor']);
            $objectPHPExcel->getActiveSheet()->setCellValue('E' . ($n), " " . $value['count'].RecipeList::$getUnit[$value['unit']]);

            $flowNum = $n;
            $flowCount = count($value['item']);
            if ($flowCount > 0) {
                foreach ($value['item'] as $k => $v) {
                    $objectPHPExcel->getActiveSheet()->setCellValue('F' . ($flowNum), $v['num'].RecipeList::$getUnit[$v['unit']]);

                    if($v['num']<= 10){
                        //设置颜色
                        $objectPHPExcel->getActiveSheet()->getStyle('F' . ($flowNum))->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
                    }

                    $objectPHPExcel->getActiveSheet()->setCellValue('G' . ($flowNum), $v['default_price']);
                    //设置为文本格式
                    $objectPHPExcel->getActiveSheet()->setCellValueExplicit('G' . ($flowNum),$v['default_price'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objectPHPExcel->getActiveSheet()->setCellValue('H' . ($flowNum), $v['batch_number']);
                    //设置为文本格式
                    $objectPHPExcel->getActiveSheet()->setCellValueExplicit('H' . ($flowNum),$v['batch_number'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objectPHPExcel->getActiveSheet()->setCellValue('I' . ($flowNum), date(('Y-m-d'),$v['expire_time']));

                    if($v['expire_time']<= strtotime(date('Y-m-d'))+86400*180){
                        //设置颜色
                        $objectPHPExcel->getActiveSheet()->getStyle('I' . ($flowNum))->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ) );
                    }

                    $objectPHPExcel->getActiveSheet()->setCellValue('J' . ($flowNum), date(('Y-m-d'),$v['inbound_time']));
                    $flowNum++;
                }
                //合并
                $objectPHPExcel->getActiveSheet()->mergeCells('A' . ($n) . ':A' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('A' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('B' . ($n ) . ':B' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('B' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('C' . ($n ) . ':C' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('C' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('D' . ($n) . ':D' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('D' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objectPHPExcel->getActiveSheet()->mergeCells('E' . ($n ) . ':E' . ($n + $flowCount - 1)); //合并表头
                $objectPHPExcel->getActiveSheet()->getStyle('E' . $n)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            }
            $step = $flowCount > 1 ? $flowCount : 1;
            $n = $n+$step;
        }
        $objectPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $objectPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);


        //表格头的输出
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '货架号');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objectPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '名称');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
        $objectPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '规格');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
        $objectPHPExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '生产厂商');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '总库存量');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '单库存量');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '成本价');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getStyle('G1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '批号');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getStyle('H1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '有效期');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '入库日期');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getStyle('J1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



        $objWriter = new PHPExcel_Writer_Excel2007($objectPHPExcel);
        $date = date("Y年m月d日 H时i分");
        $outputFileName = '处方库存管理-' . $date . ".xls";
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


}
