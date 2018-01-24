<?php

namespace app\modules\pharmacy\controllers;

use app\modules\outpatient\models\CureRecord;
use app\modules\patient\models\PatientRecord;
use Yii;
use app\modules\outpatient\models\RecipeRecord;
use app\common\base\BaseController;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\pharmacy\models\PharmacyRecord;
use app\modules\patient\models\Patient;
use yii\helpers\Json;
use app\modules\pharmacy\models\search\PharmacyRecordSearch;
use app\modules\pharmacy\models\search\StockInfoSearch;
use app\modules\pharmacy\models\search\InboundSearch;
use app\modules\stock\models\Stock;
use app\modules\stock\models\StockInfo;
use app\modules\spot\models\SupplierConfig;
use app\common\base\MultiModel;
use app\modules\spot\models\RecipeList;
use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use app\modules\user\models\User;
use app\modules\pharmacy\models\search\OutboundSearch;
use app\modules\stock\models\Outbound;
use app\modules\stock\models\OutboundInfo;
use app\modules\spot_set\models\SecondDepartment;
use yii\db\Query;
use yii\db\Exception;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\modules\spot\models\Spot;
use app\modules\triage\models\TriageInfo;
use app\modules\pharmacy\models\RecipeBatch;
use app\modules\spot_set\models\RecipelistClinic;
use app\modules\spot\models\SpotConfig;

/**
 * PharmacyController implements the CRUD actions for RecipeRecord model.
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
     * Lists all RecipeRecord models.
     * @return mixed
     */
    public function actionIndex() {
        $request = Yii::$app->request;
        $searchModel = new PharmacyRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        $status = isset($request->queryParams['PharmacyRecordSearch']) ? $request->queryParams['PharmacyRecordSearch']['status'] : 0;
        $keys = $dataProvider->getKeys();
        $info = PharmacyRecord::getRecipeRecordInfo($keys,$status);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'info' => $info
        ]);
    }

    /*
     * 发药
     * id 流水ID
     */

    public function actionDispense($id) {
        $status = 3;
        $model = new PharmacyRecord();
        if (($param = json_decode(file_get_contents('php://input'), true))) {
            if ($this->validateRemark($param)) {  // 备注长度超过100返回错误
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'forceClose' => true,
                    'forceType' => 2,
                    'forceMessage' => '用药须知不得超过100个字'
                ];
            }
            $expireList = $this->validateDispense($param);
            if (!empty($expireList)) {
                return $this->viewExpireBatch($expireList);
            } else {
                return $this->viewBatch($param, $model);
            }
        } elseif (Yii::$app->request->post()) {
            return $this->dispenseByBatch($model, $id);
        }
        $recipeRecordDataProvider = $model->findRecipeRecordDataProvider($id, $status);
        if (empty(count($recipeRecordDataProvider->models))) {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        $recipeData=$recipeRecordDataProvider->query->asArray()->all();
            //用法用量状态转换传入JS中渲染
        foreach($recipeData as $key=>$value){
            $recipeData[$key]['dose_unit']=RecipeList::$getDoseUnit[$value['dose_unit']];
            $recipeData[$key]['used']=RecipeList::$getDefaultUsed[$value['used']];
            $recipeData[$key]['frequency']=RecipeList::$getDefaultConsumption[$value['frequency']];
            $recipeData[$key]['type']=RecipeList::$getAddress[$value['type']];
        }
        $patientModel = new Patient();
        $triageInfo = $patientModel->findTriageInfo($id);


        $repiceInfo = PharmacyRecord::getRepiceInfo($id, 4);
        $allergy = $repiceInfo["allergy"];
        $allergy = isset($allergy[$id]) ? $allergy[$id] : [];

        $model->record_id = $id;

        $recipePrintData = $this->recipePrintData($recipeRecordDataProvider, $triageInfo);


        return $this->render('dispense', [
                    'model' => $model,
                    'allergy' => $allergy,
                    'triageInfo' => $triageInfo,
                    'recipeRecordDataProvider' => $recipeRecordDataProvider,
                    'status' => $status,
                    'repiceInfo' => $repiceInfo,
                    'recipePrintData' => $recipePrintData,
                    'recipeData'=>$recipeData
            ]);
    }

    /*
     * 待退药
     * id 流水ID
     */

    public function actionPrebatch($id) {
        $status = 4;
        $model = new PharmacyRecord();
        $params = Yii::$app->request->post();
        if ($params) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!$this->isLegal($params['remarkArr'])) {  // 备注长度超过90返回错误
                $this->result['errorCode'] = '1001';
                $this->result['msg'] = '用药须知不得超过100个字';
                return $this->result;
            } else {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $recipeBatchInfo = RecipeBatch::find()->select(['recipe_record_id', 'stock_info_id', 'num'])->where(['recipe_record_id' => $params['idArr'], 'record_id' => $id, 'spot_id' => $this->spotId])->asArray()->all();
                    foreach ($params['idArr'] as $k => $v) {
                        $repiceRecordModel = $this->findModel($v);
                        $repiceRecordModel->remark = $params['remarkArr'][$k];
                        $repiceRecordModel->status = 5;
                        $repiceRecordModel->save();
                    }
                    foreach ($recipeBatchInfo as $value) {
                        $stockInfoModel = StockInfo::findOne(['id' => $value['stock_info_id'], 'spot_id' => $this->spotId]);
                        $stockInfoModel->scenario = 'batch';
                        $stockInfoModel->num = $stockInfoModel->num + $value['num'];
                        $stockInfoModel->save();
                    }
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    Yii::error(json_encode($e->errorInfo), 'prebatch');
                    $this->result['errorCode'] = '1002';
                    $this->result['msg'] = '操作失败';
                    return $this->result;
                }
            }
        }
        $recipeRecordDataProvider = $model->findRecipeRecordDataProvider($id, $status);
        if (empty(count($recipeRecordDataProvider->models))) {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        $patientModel = new Patient();
        $triageInfo = $patientModel->findTriageInfo($id);


        $repiceInfo = PharmacyRecord::getRepiceInfo($id, 4);
        $allergy = $repiceInfo["allergy"];
        $allergy = isset($allergy[$id]) ? $allergy[$id] : [];

        $model->record_id = $id;
        $skinTestData = PharmacyRecord::findRecipeRecordSkinTest($id);
        return $this->render('prebatch', [
                    'model' => $model,
                    'allergy' => $allergy,
                    'triageInfo' => $triageInfo,
                    'recipeRecordDataProvider' => $recipeRecordDataProvider,
                    'status' => $status,
                    'repiceInfo' => $repiceInfo,
                    'skinTestData' => $skinTestData
        ]);
    }

    /*
     * 按批次发药  修改医嘱状态  并跟新库存
     */

    private function dispenseByBatch($model, $id) {
        $model->load(Yii::$app->request->post());
        $param = Yii::$app->request->post();
        $model->batch_id = $param['batch_id'];
        $model->recipe_record_id = $param['recipe_record_id'];
        $model->recipe_record = $param['recipe_record'];
        $model->storage_limit = $param['storage_limit'];
        $model->idArr = $param['idArr'];
        $outIdArr = [];
        if ($model->validate()) {
            $stockInfo = new StockInfo();
            $idArr = json_decode($param['idArr'], true);
            $remarkArr = json_decode($param['remark'], true);
            if (!empty($idArr)) {
                foreach ($idArr as $k => $v) {
                    $repiceRecordModel = $this->findModel($v);
                    $repiceRecordModel->status = 1;
                    $repiceRecordModel->recipe_finish_time = time();
                    $repiceRecordModel->remark = $remarkArr[$k];
                    $repiceRecordModel->save();
                }
                //更新批次的库存
                if (!empty($param['PharmacyRecord']['need_num'])) {
                    foreach ($param['PharmacyRecord']['need_num'] as $key => $need) {
                        if ($need) {
                            $stockInfoModel = $stockInfo->findStockInfoModel($param['batch_id'][$key]);
                            $stockInfoModel->num = ($stockInfoModel->num - $need);
                            $stockInfoModel->scenario = 'outboundApply';
                            $stockInfoModel->save();
                            RecipeBatch::saveInfo($param['recipe_record_id'][$key], $id, $param['batch_id'][$key], $need);
                        }
                    }
                } else {
                    $this->result['errorCode'] = true;
                    $this->result['msg'] = '该药品库存数量为零';
                    return Json::encode($this->result);
                }
                return Json::encode($this->result);
            } else {
                $this->result['errorCode'] = true;
                $this->result['msg'] = '操作失败';
                return Json::encode($this->result);
            }
        } else {
            $this->result['errorCode'] = true;
            $this->result['msg'] = $model->errors['need_num'][0];
            return Json::encode($this->result);
        }
    }

    /*
     * 按批次发药的弹框 页面
     */

    private function viewBatch($param, $model) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!empty($param['idArr'])) {
            $recipe_record = RecipeRecord::find()->select(['num', 'id'])->where(['id' => $param['idArr'], 'type' => 1])->asArray()->all();
            $recipe_record = ArrayHelper::map($recipe_record, 'id', 'num');
            $batchDataProvider = $model->findBatchDataProvider($param['idArr']);
            $remark = $param['remarkArr'];
            $idArr = $param['idArr'];
            return [
                'title' => "选择药品批次和数量",
                'content' => $this->renderAjax('_batch', [
                    'model' => $model,
                    'remark' => json_encode($remark),
                    'idArr' => json_encode($idArr),
                    'recipe_record' => json_encode($recipe_record),
                    'batchDataProvider' => $batchDataProvider,
                    'totalRecipeList' => $recipe_record
                ]),
            ];
        } else {
            $this->result['errorCode'] = 10001;
            return $this->result;
        }
    }
    /*
     * 保存用药须知
     * id 流水ID
     */
    public function actionPreserve(){

       if($param=Yii::$app->request->post()){
           if(!empty($param['idArr']) && ($ret = $this->isLegal($param['remarkArr']))){
               foreach($param['idArr'] as $k=>$v){
                    $repiceRecordModel=$this->findModel($v);
                       $repiceRecordModel->remark = $param['remarkArr'][$k];
                        $repiceRecordModel->save();
               }

               return Json::encode($this->result);
           }else{
               $this->result['errorCode']=10001;
               if($ret === false){
                   $this->result['message']='用药须知不能超过100个字';
               }
               return Json::encode($this->result);
           }
       }



    }
    /*
     * 发药 已完成 
     * id 流水ID
     */

    public function actionComplete($id) {
        if ($param = Yii::$app->request->post()) {
            if (!empty($param['idArr'])) {
                if ($this->isLegal($param['remarkArr'])) {
                    foreach ($param['idArr'] as $k => $v) {
                        $repiceRecordModel = $this->findModel($v);
                        $repiceRecordModel->remark = $param['remarkArr'][$k];
                        $repiceRecordModel->status = 1;
                        $repiceRecordModel->save();
                    }
                    return Json::encode($this->result);
                } else {
                    $this->result['errorCode'] = 10001;
                    $this->result['message'] = '用药须知不能超过100个字';
                    return Json::encode($this->result);
                }
            } else {
                $this->result['errorCode'] = 10001;
                return Json::encode($this->result);
            }
        }
//        $status = Yii::$app->request->get('status');
        $status = 1;
        $model = new PharmacyRecord();
        $recipeRecordDataProvider = $model->findRecipeRecordDataProvider($id, $status);
        if (empty(count($recipeRecordDataProvider->getModels()))) {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        $recipeData=$recipeRecordDataProvider->query->asArray()->all();
            //用法用量状态转换传入JS中渲染
        foreach($recipeData as $key=>$value){
            $recipeData[$key]['dose_unit']=RecipeList::$getDoseUnit[$value['dose_unit']];
            $recipeData[$key]['used']=RecipeList::$getDefaultUsed[$value['used']];
            $recipeData[$key]['frequency']=RecipeList::$getDefaultConsumption[$value['frequency']];
            $recipeData[$key]['type']=RecipeList::$getAddress[$value['type']];
        }

        $patientModel = new Patient();
        $triageInfo = $patientModel->findTriageInfo($id);
        $repiceInfo = $model->getRepiceInfo($id, 4);
        $model->record_id = $id;

        $soptInfo = Spot::find()->select(['spot_name', 'spot', 'status', 'province', 'city', 'area', 'telephone', 'icon_url'])->where(['id' => $this->spotId])->asArray()->one();
        $recipePrintData = $this->recipePrintData($recipeRecordDataProvider, $triageInfo);

        $allergy = $repiceInfo["allergy"];
        $allergy = isset($allergy[$id]) ? $allergy[$id] : [];
        $spotConfig = SpotConfig::getConfig(['logo_img','pub_tel','spot_name','logo_shape']);
        return $this->render('dispense', [
                    'model' => $model,
                    'triageInfo' => $triageInfo,
                    'recipeRecordDataProvider' => $recipeRecordDataProvider,
                    'status' => $status,
                    'repiceInfo' => $repiceInfo,
                    'allergy' => $allergy,
                    'soptInfo' => $soptInfo,
                    'recipePrintData' => $recipePrintData,
                    'spotConfig' => $spotConfig,
                    'recipeData'=>$recipeData

        ]);
    }

    public function actionPrintLabel($id,$status = 1) {
        $model = new PharmacyRecord();
        $model->scenario = 'printLabel';
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $recipePrintData = PharmacyRecord::printRecipeLabelData($model->recipeList);
                $spotConfig = SpotConfig::getConfig(['label_tel','spot_name']);
                $this->result['spotConfig'] = $spotConfig;
                $this->result['recipePrintData'] = $recipePrintData;
                return $this->result;
            } else {
                $recipeList = PharmacyRecord::getRecipeListByRecord($id,$status);
                if (empty($recipeList) || is_null($recipeList)) {
                    throw new NotFoundHttpException('你所请求的页面不存在');
                }
                $userInfo = Patient::getPatientName($id);
                return [
                    'title' => "选择药品项目",
                    'content' => $this->renderAjax('printLabel', [
                        'model' => $model,
                        'recipeList' => $recipeList,
                        'userInfo' => $userInfo,
                    ])
                ];
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }


    /*
     * 待退药
     * id 流水ID
     */

    public function actionEndbatch($id) {
        $status = 5;
        $model = new PharmacyRecord();
        $params = Yii::$app->request->post();
        if ($params) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!$this->isLegal($params['remarkArr'])) {  // 备注长度超过90返回错误
                $this->result['errorCode'] = 10001;
                $this->result['msg'] = '用药须知不得超过100个字';
                return $this->result;
            } else {
                foreach ($params['idArr'] as $k => $v) {
                    $repiceRecordModel = $this->findModel($v);
                    $repiceRecordModel->remark = $params['remarkArr'][$k];
                    $repiceRecordModel->status = 5;
                    $repiceRecordModel->save();
                }
                return $this->result;
            }
        }
        $recipeRecordDataProvider = $model->findRecipeRecordDataProvider($id, $status);
        if (empty(count($recipeRecordDataProvider->models))) {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        $patientModel = new Patient();
        $triageInfo = $patientModel->findTriageInfo($id);


        $repiceInfo = PharmacyRecord::getRepiceInfo($id, 4);
        $allergy = $repiceInfo["allergy"];
        $allergy = isset($allergy[$id]) ? $allergy[$id] : [];

        $model->record_id = $id;
        $skinTestData = PharmacyRecord::findRecipeRecordSkinTest($id);
        return $this->render('endbatch', [
                    'model' => $model,
                    'triageInfo' => $triageInfo,
                    'recipeRecordDataProvider' => $recipeRecordDataProvider,
                    'status' => $status,
                    'allergy' => $allergy,
                    'repiceInfo' => $repiceInfo,
                    'skinTestData' => $skinTestData
        ]);
    }

    protected function recipePrintData($dataProvider, $triageInfo) {
        $res = [];
        if (!empty($dataProvider->getModels())) {
            $data = $dataProvider->getModels();
            foreach ($data as $val) {
                if ($val['type'] == 1) {
                    $res[] = [
                        'userName' => $triageInfo['username'],
                        'patientNumber' => $triageInfo['patient_number'],
                        'sex' => isset(Patient::$getSex[$triageInfo['sex']]) ? Patient::$getSex[$triageInfo['sex']] : '',
                        'age' => $triageInfo['birthday'] . '(' . date('Y-m-d', $triageInfo['birthtime']) . ')',
                        'recipeName' => $val['name'],
                        'unit' => $val['num'] . RecipeList::$getUnit[$val['unit']], //1盒
                        'specification' => $val['specification'], //1盒
                        'used' => RecipeList::$getDefaultUsed[$val['used']], //1盒
                        'frequency' => '每次' . $val['dose'] . RecipeList::$getDoseUnit[$val['dose_unit']] . ',' . RecipeList::$getDefaultConsumption[$val['frequency']] . ',' . $val['day'] . '天', //1盒,
                        'remark' => \yii\helpers\Html::encode($val['remark'])
                    ];
                }
            }
        }
        return $res;
    }

    /**
     * @return string 验证要发的药品是否已过期或库存为0
     */
    public function validateDispense($param) {
        $idArr = $param['idArr'];

        if (!empty($param['recipeOutArr'])) {
            foreach ($param['recipeOutArr'] as $key => $v) {
                if ($v == 2) {
                    unset($idArr[$key]);
                }
            }
        }
        $query = new Query();
        $query->from(['pr' => PharmacyRecord::tableName()]);
        $query->select([
            'si.expire_time', 'si.num', 'pr.name', 'si.recipe_id', 'pr.id',
        ]);
        $query->leftJoin(['si' => StockInfo::tableName()], '{{si}}.recipe_id={{pr}}.recipe_id');
        $query->leftJoin(['c' => Stock::tableName()], '{{si}}.stock_id = {{c}}.id');
        $query->where(['si.spot_id' => $this->spotId, 'pr.id' => $idArr, 'pr.type' => 1, 'c.status' => 1]);
        $query->andWhere('si.expire_time >= :expire_time', [':expire_time' => strtotime(date("Y-m-d"))]);
        $query->andWhere('si.num > :num', [':num' => 0]);
        $query->indexBy('id');
        $data = $query->all();


        if (count($idArr) == count($data)) {
            return [];
        }
        if (!empty($data)) {
            foreach ($idArr as $key => $v) {
                if (isset($data[$v])) {
                    unset($idArr[$key]);
                }
            }
        }
        return $idArr;
    }

    /**
     * @return boolean 备注长度是否超过90个字
     */
    public function validateRemark($param) {
        $remarkArr = $param['remarkArr'];
        if (!empty($remarkArr)) {
            foreach ($remarkArr as $key => $value) {

                if (mb_strlen($value, "UTF8") > 200) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $param 获取到的参数，这里只需要id
     * @param $model
     * @return array
     */
    public function viewExpireBatch($expireList) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = new \yii\db\ActiveQuery(PharmacyRecord::className());
        $query->from(['pr' => PharmacyRecord::tableName()]);
        $query->select(['pr_id' => 'pr.id', 'recipe_name' => 'pr.name', 'pr.unit', 'pr.price', 'pr.dose', 'pr.used', 'pr.frequency', 'pr.day', 'recipe_record_id' => 'pr.id',
            'pr.num', 'pr.description', 'pr.type', 'pr.status', 'pr.remark', 'storage' => 'si.num', 'si.batch_number', 'si.expire_time', 'batch_id' => 'si.id', 'r.specification'
        ]);
        $query->leftJoin(['si' => StockInfo::tableName()], '{{si}}.recipe_id={{pr}}.recipe_id');
        $query->leftJoin(['s' => Stock::tableName()], '{{si}}.stock_id={{s}}.id');
        $query->leftJoin(['r' => RecipeList::tableName()], '{{r}}.id={{pr}}.recipe_id');
        $query->where(['si.spot_id' => $this->spotId, 'pr.id' => $expireList, 's.status' => 1, 'pr.type' => 1]);
        $expireDataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'pr.num' => SORT_ASC,
                ],
                'attributes' => ['pr.num']
            ]
        ]);
        return [
            'title' => "操作失败",
            'content' => $this->renderAjax('_expireBatch', [
                'expireDataProvider' => $expireDataProvider,
            ]),
        ];
    }


    /**
     * Finds the RecipeRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return RecipeRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = RecipeRecord::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            return new RecipeRecord();
        }
    }

    protected function isLegal($arr){

        foreach($arr as $k=>$v){
          if(mb_strlen($v,'UTF-8')>100){
             return false;
          }
        }
        return true;

    }


}
