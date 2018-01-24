<?php

namespace app\modules\cure\controllers;

use app\modules\outpatient\models\AllergyOutpatient;
use Yii;
use app\modules\cure\models\Cure;
use app\modules\cure\models\search\CureSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Html;
use app\modules\patient\models\Patient;
use yii\helpers\Url;
use app\modules\pharmacy\models\PharmacyRecord;
use app\modules\outpatient\models\CureRecord;
use yii\helpers\Json;
use yii\base\Object;
use yii\data\ActiveDataProvider;
use app\modules\charge\models\ChargeInfo;
use yii\db\ActiveQuery;
use app\modules\spot\models\Spot;
use app\modules\triage\models\TriageInfo;
use app\modules\spot\models\SpotConfig;
/**
 * IndexController implements the CRUD actions for Cure model.
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
     * Lists all Cure models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CureSearch();
        $params = Yii::$app->request->queryParams;
        $type = (isset($params['type']) && $params['type']) ? $params['type'] : 3;
        if ($type == 3) {
            $dataProvider = $searchModel->search($params, $this->pageSize);
        } else {
            $dataProvider = $searchModel->specialSearch($params, $this->pageSize);
        }
        $cureStatusCount = Cure::getCureNumByList($dataProvider);
        $cureNameList = Cure::getCureArrayByList($dataProvider);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'cureStatusCount' => $cureStatusCount,
                    'cureNameList' => $cureNameList,
        ]);
    }

    /*
     * 治疗中
     */

    public function actionCure($id) {
        $model = new Cure();
        $model->scenario = 'cure';
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $cureArr = Yii::$app->request->post()['Cure']['cure'];
                if (!empty($cureArr)) {
//                $connection->createCommand()->update('user', ['status' => 1], 'age > 30')->execute();
                    Cure::updateAll(['status' => 2,'cure_in_time'=>time()], ['id' => $cureArr]);
                    return [
                        'forceReload'=>'#crud-datatable-pjax',
                        'forceClose' => true
        
                    ];
                }
            } else {
                $cureList = $model->getCureListByRecord($id);
                $userInfo = Patient::getPatientName($id);
                $model ->cure = array_column($cureList,'id');
                return [
                    'title' => "选择治疗项目",
                    'content' => $this->renderAjax('cure', [
                        'model' => $model,
                        'cureList' => $cureList,
                        'userInfo' => $userInfo,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }


    public function actionComplete($id){
        if (($param = Yii::$app->request->post()) != null) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!empty($param['idArr'])) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    foreach ($param['idArr'] as $k => $v) {
                        if(mb_strlen(trim($param['cureResultArr'][$k])) > 10){
                            $this->result['errorCode'] = 1002;
                            $this->result['msg'] = '执行结果不能大于10个字符';
                            $dbTrans->rollBack();
                            return $this->result;
                        }
                        if(mb_strlen(trim($param['remarkArr'][$k])) > 255){
                            $this->result['errorCode'] = 1003;
                            $this->result['msg'] = '备注不能大于255个字符';
                            $dbTrans->rollBack();
                            return $this->result;
                        }
                        $cureRecordModel = $this->findModel($v);
                        if($cureRecordModel->type == 1 && !Cure::$getCureResult[$param['cureResultArr'][$k]]){//若为固定的治疗医嘱,则执行结果只有 阴性和阳性
                            $this->result['errorCode'] = 1004;
                            $this->result['msg'] = '参数错误';
                            $dbTrans->rollBack();
                            return $this->result;
                        }
                        $cureRecordModel->status = 1;
                        $cureRecordModel->remark = $param['remarkArr'][$k];
                        $cureRecordModel->cure_result = $param['cureResultArr'][$k];
                        $cureRecordModel->save();
                    }
                    $dbTrans->commit();
                    return $this->result;
                }catch (\yii\db\Exception $e){
                    $dbTrans->rollBack();
                    Yii::error('治疗保存失败信息:'.json_encode($e->errorInfo,true));
                }
            } else {
                $this->result['errorCode'] = 10001;
                return $this->result;
            }
        }

        $status=1;

        $curname =  Cure::getCureNum($id,$status);
        if (empty($curname)) {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }

        $cureList = $this->getUnderCureListByRecord($id,$status);

        $triageInfo = Patient::findTriageInfo($id);


        $soptInfo = Spot::find()->select(['spot_name','spot','status','province','city','area','telephone','icon_url'])->where(['id' => $this->spotId])->asArray()->one();

        $repiceInfo = PharmacyRecord::getRepiceInfo($id,3);
        $allergy = $repiceInfo["allergy"];
        $allergy = isset($allergy[$id]) ? $allergy[$id] : [];
        $spotConfig = SpotConfig::getConfig(['logo_img','pub_tel','spot_name','logo_shape']);
        return $this->render('complete', [
                    'allergy' => $allergy,
                    'status' => $status,
                    'triageInfo' => $triageInfo,
                    'recipeRecordDataProvider' => $cureList,
                    'repiceInfo' => $repiceInfo,
                    'soptInfo' => $soptInfo,
                    'spotConfig' => $spotConfig
            
        ]);
    }

    public function actionUnderCure($id) {
        if ($param = Yii::$app->request->post()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                if (!empty($param['idArr'])) {
                    
                    foreach ($param['idArr'] as $k => $v) {
                        if(mb_strlen(trim($param['cureResultArr'][$k])) > 10){
                            $this->result['errorCode'] = 1002;
                            $this->result['msg'] = '执行结果不能大于10个字符';
                            $dbTrans->rollBack();
                            return $this->result;
                        }
                        if(mb_strlen(trim($param['remarkArr'][$k])) > 255){
                            $this->result['errorCode'] = 1003;
                            $this->result['msg'] = '备注不能大于255个字符';
                            $dbTrans->rollBack();
                            return $this->result;
                        }
                        $cureRecordModel = $this->findModel($v);
                        if($cureRecordModel->type == 1 && !Cure::$getCureResult[$param['cureResultArr'][$k]]){//若为固定的治疗医嘱,则执行结果只有 阴性和阳性
                            $this->result['errorCode'] = 1004;
                            $this->result['msg'] = '参数错误';
                            $dbTrans->rollBack();
                            return $this->result;
                        }
                        $cureRecordModel = $this->findModel($v);
                        $cureRecordModel->status = 1;
                        $cureRecordModel->cure_finish_time = time();
                        $cureRecordModel->remark = $param['remarkArr'][$k];
                        $cureRecordModel->cure_result = $param['cureResultArr'][$k];
                        $cureRecordModel->save();
                    }
                    $dbTrans->commit();
                    return $this->result;
                } else {
                    $this->result['errorCode'] = 10001;
                    return $this->result;
                }
            }catch (\yii\db\Exception $e){
                $dbTrans->rollBack();
                Yii::error('治疗保存失败信息:'.json_encode($e->errorInfo,true));
            }
        }

        $status=2;

        $curname =  Cure::getCureNum($id,$status);

        if (empty($curname)) {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }

        $cureList = $this->getUnderCureListByRecord($id,$status);

        $triageInfo = Patient::findTriageInfo($id);

        $allergy = AllergyOutpatient::getAllergyByRecord($id);
        $allergy = isset($allergy[$id]) ? $allergy[$id] : [];

        return $this->render('under-cure', [
            'allergy' => $allergy,
            'triageInfo' => $triageInfo,
            'recipeRecordDataProvider' => $cureList,
            'status' => $status,
        ]);
    }

    /**
     * Displays a single Cure model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Cure model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Cure();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Cure model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Cure model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('success', '删除成功');
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param $record_id
     * @param $status(1、已完成 2,治疗中)
     * @return 治疗情况
     */
    private function getUnderCureListByRecord($record_id, $status) {
        $query = new ActiveQuery(CureRecord::className());
        $query->from(['a' => CureRecord::tableName()]);
        $query->select(['a.id', 'a.record_id', 'a.spot_id', 'a.name', 'a.unit', 'a.price', 'a.time', 'a.description', 'a.create_time','a.remark','a.cure_result','a.type']);
        $query->where(['a.record_id' => $record_id, 'a.status' => $status,'a.spot_id'=>$this->spotId]);
//         if(ChargeInfo::getChargeRecordNum($record_id)){
//             $query->addSelect(['b.status']);
//             $query->leftJoin(['b' => ChargeInfo::tableName()],'{{a}}.id = {{b}}.outpatient_id');
//             $query->andWhere(['b.type' => ChargeInfo::$cureType]);
//         }
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
     * Finds the Cure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Cure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Cure::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
