<?php

namespace app\modules\check\controllers;

use app\modules\outpatient\models\AllergyOutpatient;
use app\modules\outpatient\models\CheckRecord;
use Yii;
use app\modules\check\models\Check;
use app\modules\check\models\search\CheckSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\modules\patient\models\Patient;
use yii\helpers\Html;
use yii\helpers\Json;
use app\modules\spot\models\Spot;
use app\modules\pharmacy\models\PharmacyRecord;
use app\modules\check\models\CheckRecordFile;
use app\modules\triage\models\TriageInfo;
use app\modules\message\models\MessageCenter;
use yii\db\Query;
use app\modules\patient\models\PatientRecord;
use app\modules\spot\models\SpotConfig;
use app\modules\outpatient\models\Outpatient;

/**
 * IndexController implements the CRUD actions for Check model.
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
     * Lists all Check models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CheckSearch();
        $params = Yii::$app->request->queryParams;
        $type = (isset($params['type']) && $params['type']) ? $params['type'] : 3;
        $params['type'] = $type;
        if ($type == 3 || $type == 5) {
            $dataProvider = $searchModel->search($params, $this->pageSize);
        } else {
            $dataProvider = $searchModel->specialSearch($params, $this->pageSize);
        }
        $checkStatusCount = Check::getCheckNumByList($dataProvider);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'checkStatusCount' => $checkStatusCount,
        ]);
    }

    /**
     * Displays a single Check model.
     * @param integer $id 就诊流水id
     * @return mixed
     */
    public function actionCheck($id) {
        $model = new Check();
        $model->scenario = 'check';
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                Check::updateAll(['status' => 2, 'check_in_time' => time()], ['id' => $model->check]);
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'forceClose' => true
                ];
            } else {
                $checkList = Check::getCheckListByRecord($id);
                $userInfo = Patient::getPatientName($id);
                $model->check = array_column($checkList,'id');
                return [
                    'title' => "选择检查项目",
                    'content' => $this->renderAjax('check', [
                        'model' => $model,
                        'checkList' => $checkList,
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

    /**
     * Creates a new Check model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUnderCheck($id) {
        $model = new Check();
        if (!empty($param = Yii::$app->request->post())) {
            if (!empty($param['idArr'])) {
                $checkModel = $this->findModel($param['idArr']);
                $checkModel->status = 1;
                $checkModel->report_time = time();
                $checkModel->check_finish_time = time();
                $checkModel->report_user_id = $this->userInfo->id;
                $checkModel->result = $param['result'];
                $checkModel->description = $param['description'];
                if (!$checkModel->save()) {
                    $this->result['errorCode'] = 10001;
                    return Json::encode($this->result);
                }
                //增加 已出报告数量 
                Outpatient::setMadeReport($this->spotId, $id, 1);
                $query = new Query();
                $query->from(['a' => CheckRecord::tableName()]);
                $query->select(['b.doctor_id', 'b.spot_id', 'c.patient_id', 'a.record_id', 'a.name']);
                $query->leftJoin(['b' => TriageInfo::tableName()], '{{a}}.record_id = {{b}}.record_id');
                $query->leftJoin(['c' => PatientRecord::tableName()], '{{a}}.record_id = {{c}}.id');
                $query->where(['a.id' => $param['idArr']]);
                $message = $query->all();

                //影像学检查报告消息推送
                MessageCenter::saveMessageCenter($message['0']['doctor_id'], $message['0']['patient_id'], Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@outpatientOutpatientUpdate'), 'id' => $message[0]['record_id']]) . '#report', $message['0']['name'], '报告', 0, $message['0']['record_id']);

                Yii::$app->getSession()->setFlash('success', '保存成功');
                return Json::encode($this->result);
            } else {
                $this->result['errorCode'] = 10001;
                return Json::encode($this->result);
            }
        } else {
            /* 检验项目 */
            $status = 2;
            $checkList = Check::getCheckListByRecord($id, $status);
            if (!$checkList) {
                throw new NotFoundHttpException('你所请求的页面找不到');
            }

            /* 患者个人就诊信息 */
            $triageInfo = Patient::findTriageInfo($id);

            $allergy = AllergyOutpatient::getAllergyByRecord($id);
            $allergy = isset($allergy[$id]) ? $allergy[$id] : [];

            return $this->render('under-check', [
                        'status' => $status,
                        'model' => $model,
                        'triageInfo' => $triageInfo,
                        'checkList' => $checkList,
                        'allergy' => $allergy
            ]);
        }
    }

    /**
     * Updates an existing Check model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionComplete($id) {
        $model = new Check();

        if (!empty($param = Yii::$app->request->post())) {
            if (!empty($param['idArr'])) {

                $checkModel = $this->findModel($param['idArr'], 1);
                $checkModel->report_time = time();
                $checkModel->report_user_id = $this->userInfo->id;
                $checkModel->result = $param['result'];
                $checkModel->description = $param['description'];

                if (!$checkModel->save()) {
                    $this->result['errorCode'] = 10001;
                    return Json::encode($this->result);
                }

                $query = new Query();
                $query->from(['a' => CheckRecord::tableName()]);
                $query->select(['b.doctor_id', 'b.spot_id', 'c.patient_id', 'a.record_id', 'a.name']);
                $query->leftJoin(['b' => TriageInfo::tableName()], '{{a}}.record_id = {{b}}.record_id');
                $query->leftJoin(['c' => PatientRecord::tableName()], '{{a}}.record_id = {{c}}.id');
                $query->where(['a.id' => $param['idArr']]);
                $message = $query->all();

                //影像学检查报告消息推送
                MessageCenter::saveMessageCenter($message['0']['doctor_id'], $message['0']['patient_id'], Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@outpatientOutpatientUpdate'), 'id' => $message[0]['record_id']]) . '#report', $message['0']['name'], '报告', 0, $message['0']['record_id']);

                Yii::$app->getSession()->setFlash('success', '保存成功');
                return Json::encode($this->result);
            } else {
                $this->result['errorCode'] = 10001;
                return Json::encode($this->result);
            }
        } else {
            /* 检验项目 */
            $status = 1;
            $checkList = Check::getCheckListByRecord($id, $status);

            if (!$checkList) {
                throw new NotFoundHttpException('你所请求的页面不存在');
            }
            /* 患者个人就诊信息 */

            $triageInfo = Patient::findTriageInfo($id);


            $soptInfo = Spot::find()->select(['spot_name', 'telephone', 'icon_url'])->where(['id' => $this->spotId])->asArray()->one();

            $allergy = AllergyOutpatient::getAllergyByRecord($id);
            $allergy = isset($allergy[$id]) ? $allergy[$id] : [];
            $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name','logo_shape']);
            return $this->render('complete', [
                        'status' => $status,
                        'model' => $model,
                        'triageInfo' => $triageInfo,
                        'checkList' => $checkList,
                        'soptInfo' => $soptInfo,
                        'allergy' => $allergy,
                        'spotConfig' => $spotConfig
            ]);
        }
    }

    /**
     * Finds the Check model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id 影像学检查id
     * @param integer $status 状态(1-已完成,2-执行中,3-未执行)
     * @return Check the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $status = 2) {
        if (($model = Check::findOne(['id' => $id, 'spot_id' => $this->spotId, 'status' => $status])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
