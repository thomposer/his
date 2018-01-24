<?php

namespace app\modules\inspect\controllers;

use app\modules\outpatient\models\AllergyOutpatient;
use app\modules\patient\models\PatientRecord;
use Yii;
use app\modules\inspect\models\Inspect;
use app\modules\inspect\models\search\InspectSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\common\base\BaseController;
use app\modules\patient\models\Patient;
use yii\web\Response;
use yii\bootstrap\Html;
use app\modules\inspect\models\InspectRecordUnion;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\db\Exception;
use app\modules\outpatient\models\InspectRecord;
use app\modules\spot\models\Spot;
use app\modules\pharmacy\models\PharmacyRecord;
use app\modules\triage\models\TriageInfo;
use yii\db\Query;
use app\modules\message\models\MessageCenter;
use app\modules\spot\models\SpotConfig;
use app\modules\outpatient\models\Outpatient;

/**
 * IndexController implements the CRUD actions for Inspect model.
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
     * Lists all Inspect models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new InspectSearch();

        $params = Yii::$app->request->queryParams;
        $type = (isset($params['type']) && $params['type']) ? $params['type'] : 3;
        $params['type'] = $type;

        if ($type == 3 || $type == 5) {
            $dataProvider = $searchModel->search($params, $this->pageSize);
        } elseif ($type == 4) {
            $dataProvider = $searchModel->specialSearch($params, $this->pageSize);
        }
        $inspectStatusCount = Inspect::getInspectNumByList($dataProvider);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'inspectStatusCount' => $inspectStatusCount,
        ]);
    }

    /**
     * @param $id
     * @return array 待检查
     * @throws NotFoundHttpException
     */
    public function actionOnInspect($id) {
        $model = new Inspect();
        $model->scenario = 'on-inspect';
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $list = Inspect::find()->select(['id' => 'group_concat(id)'])->where(['id' => $model->onInspect, 'spot_id' => $this->spotId])->andWhere('specimen_type != 0 and cuvette != 0 and inspect_type != :inspect_type', [':inspect_type' => ''])->groupBy(['deliver', 'specimen_type', 'cuvette', 'inspect_type'])->asArray()->all();
                $rows = [];
                $idList = [];
                foreach ($list as $v) {
                    $number = Inspect::generateSpecimenNumber();
                    $id = explode(',', $v['id']);
                    foreach ($id as $value) {
                        $idList[] = $value;
                    }
                    $rows[] = $number;
                    Inspect::updateAll(['status' => 2, 'inspect_in_time' => time(), 'specimen_number' => $number], ['id' => $id]);
                }
                $diffList = array_diff($model->onInspect, $idList);
                if (!empty($diffList)) {
                    foreach ($diffList as $value) {
                        $number = Inspect::generateSpecimenNumber();
                        Inspect::updateAll(['status' => 2, 'inspect_in_time' => time(), 'specimen_number' => $number], ['id' => $value]);
                    }
                }

                $specimenNumberInfo = Inspect::getSpecimenByInspect($model->onInspect);
                $this->result['count'] = count($list);
                $this->result['specimenNumber'] = $rows;
                $this->result['specimenNumberInfo'] = $specimenNumberInfo;
                return $this->result;
            } else {
                $inspectList = $model::getInspectListByRecord($id);
                $userInfo = Patient::getPatientName($id);
                $model->onInspect = array_column($inspectList,'id');
                return [
                    'title' => "选择检查项目",
                    'content' => $this->renderAjax('on-inspect', [
                        'model' => $model,
                        'inspectList' => $inspectList,
                        'userInfo' => $userInfo,
                    ]),
                ];
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @param $id
     * @return string 检查中
     */
    public function actionUnderInspect($id) {
        if (!empty($param = Yii::$app->request->post())) {
            if (!empty($param['unionidArr'])) {
                $outerTrans = Yii::$app->db->beginTransaction();
                try {
                    $num = InspectRecordUnion::find()->select(['id'])->where(['spot_id' => $this->spotId, 'inspect_record_id' => $param['idArr']])->count();

                    $query = new Query();
                    $query->from(['a' => InspectRecord::tableName()]);
                    $query->select(['b.doctor_id', 'b.spot_id', 'c.patient_id', 'a.record_id', 'a.name']);
                    $query->leftJoin(['b' => TriageInfo::tableName()], '{{a}}.record_id = {{b}}.record_id');
                    $query->leftJoin(['c' => PatientRecord::tableName()], '{{a}}.record_id = {{c}}.id');
                    $query->where(['a.id' => $param['idArr']]);
                    $message = $query->all();

                    //实验室检查报告消息推送
                    MessageCenter::saveMessageCenter($message['0']['doctor_id'], $message['0']['patient_id'], Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@outpatientOutpatientUpdate'), 'id' => $message[0]['record_id']]) . '#report', $message['0']['name'], '报告', 0, $message['0']['record_id']);

                    if ($num != 0) {
                        InspectRecord::updateAll(['status' => 1, 'report_time' => time(), 'inspect_finish_time' => time(), 'report_user_id' => $this->userInfo->id, 'update_time' => time()], ['id' => $param['idArr']]);
                        foreach ($param['unionidArr'] as $k => $v) {
                            //参考值置空
                            InspectRecordUnion::updateAll(['result' => $param['remarkArr'][$k], 'update_time' => time()], ['id' => $param['unionidArr'][$k]]);
                        }
                        //设置 已出报告数量
                        Outpatient::setMadeReport($this->spotId, $id, 1);
                        $outerTrans->commit();
                        Yii::$app->getSession()->setFlash('success', '保存成功');
                        return Json::encode($this->result);
                    } else {
                        $this->result['errorCode'] = 1001;
                        $this->result['msg'] = '请添加检验项目';
                        return Json::encode($this->result);
                    }
                } catch (Exception $e) {
                    $outerTrans->rollBack();
                    $this->result['errorCode'] = 1001;
                    $this->result['msg'] = '系统异常,请稍后再试';
                    return Json::encode($this->result);
                }
            } else {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '请选择检查项目';
                return Json::encode($this->result);
            }
        } else {
            /* 检验项目 */
            $status = 2;
            $inspectList = Inspect::getInspectListByRecord($id, $status);
            if (!$inspectList) {
                throw new NotFoundHttpException('你所请求的页面不存在');
            }
            /* 患者个人就诊信息 */
            $triageInfo = Patient::findTriageInfo($id);
            /* 关联项目 */
            $inspectId = [];
            foreach ($inspectList as $key => $val) {
                $inspectUnionDataProvider[$val['id']] = $this->findInspectUnionDataProvider($val['id']);
                $inspectId[] = $val['id'];
            }
            $inspectSpecimen = Inspect::getSpecimenByInspect($inspectId);

            $allergy = AllergyOutpatient::getAllergyByRecord($id);
            $allergy = isset($allergy[$id]) ? $allergy[$id] : [];
            return $this->render('under-inspect', [
                        'status' => $status,
                        'triageInfo' => $triageInfo,
                        'inspectList' => $inspectList,
                        'inspectSpecimen' => $inspectSpecimen,
                        'inspectUnionList' => $inspectUnionDataProvider,
                        'allergy' => $allergy
            ]);
        }
    }

    /**
     * @param $id
     * @param $type 0-已完成，1-已取消
      * @return string 检查完成
     */
    public function actionComplete($id,$type = 0) {
        $model = new Inspect();

        if (!empty($param = Yii::$app->request->post())) {
            if (!empty($param['unionidArr'])) {
                $outerTrans = Yii::$app->db->beginTransaction();
                try {
                    $num = InspectRecordUnion::find()->select(['id'])->where(['spot_id' => $this->spotId, 'inspect_record_id' => $param['idArr']])->count();

                    $query = new Query();
                    $query->from(['a' => InspectRecord::tableName()]);
                    $query->select(['b.doctor_id', 'b.spot_id', 'c.patient_id', 'a.record_id', 'a.name']);
                    $query->leftJoin(['b' => TriageInfo::tableName()], '{{a}}.record_id = {{b}}.record_id');
                    $query->leftJoin(['c' => PatientRecord::tableName()], '{{a}}.record_id = {{c}}.id');
                    $query->where(['a.id' => $param['idArr']]);
                    $message = $query->all();

                    //实验室检查报告消息推送
                    MessageCenter::saveMessageCenter($message['0']['doctor_id'], $message['0']['patient_id'], Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@outpatientOutpatientIndex'), 'id' => $message[0]['record_id']]) . '#report', $message['0']['name'], '报告', 0, $message['0']['record_id']);

                    if ($num != 0) {
                        InspectRecord::updateAll(['report_time' => time(), 'report_user_id' => $this->userInfo->id, 'update_time' => time()], ['id' => $param['idArr']]);
                        foreach ($param['unionidArr'] as $k => $v) {
                            InspectRecordUnion::updateAll(['result' => $param['remarkArr'][$k], 'update_time' => time()], ['id' => $param['unionidArr'][$k]]);
                        }
                        $outerTrans->commit();
                        Yii::$app->getSession()->setFlash('success', '保存成功');
                        return Json::encode($this->result);
                    } else {
                        $this->result['errorCode'] = 1001;
                        $this->result['msg'] = '请添加检验项目';
                        return Json::encode($this->result);
                    }
                } catch (Exception $e) {
                    $outerTrans->rollBack();
                    $this->result['errorCode'] = 1001;
                    $this->result['msg'] = '系统异常,请稍后再试';
                    return Json::encode($this->result);
                }
            } else {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '请选择检查项目';
                return Json::encode($this->result);
            }
        } else {
            /* 患者个人就诊信息 */
            $triageInfo = Patient::findTriageInfo($id);

            /* 检验项目 */
            $status = $type == 1 ? 4:1;
            $inspectList = Inspect::getInspectListByRecord($id, $status);
            if (!$inspectList) {
                throw new NotFoundHttpException('你所请求的页面不存在');
            }
            /* 关联项目 */
            $inspectId = [];
            foreach ($inspectList as $key => $val) {
                $inspectUnionDataProvider[$val['id']] = $this->findInspectUnionDataProvider($val['id']);
                $inspectId[] = $val['id'];
            }
            $inspectSpecimen = Inspect::getSpecimenByInspect($inspectId);
            $soptInfo = Spot::find()->select(['spot_name', 'spot', 'status', 'province', 'city', 'area', 'telephone', 'icon_url'])->where(['id' => $this->spotId])->asArray()->one();

            $allergy = AllergyOutpatient::getAllergyByRecord($id);
            $allergy = isset($allergy[$id]) ? $allergy[$id] : [];
            $warning = Inspect::getWarning($id);
            $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name','logo_shape']);
            return $this->render('complete', [
                        'status' => $status,
                        'triageInfo' => $triageInfo,
                        'inspectList' => $inspectList,
                        'inspectUnionList' => $inspectUnionDataProvider,
                        'soptInfo' => $soptInfo,
                        'inspectSpecimen' => $inspectSpecimen,
                        'allergy' => $allergy,
                        'warning' => $warning,
                        'spotConfig' => $spotConfig
            ]);
        }
    }

    public function actionGenNum() {
        echo Inspect::genSpecimenNumber();
    }

    /**
     * @param $id
     * @return ActiveDataProvider 查实验室关联项目
     */
    protected function findInspectUnionDataProvider($id) {
        $query = new ActiveQuery(InspectRecordUnion::className());
        $query->from(InspectRecordUnion::tableName());
        $query->select(['id', 'name', 'unit', 'reference', 'result', 'inspect_record_id', 'result_identification']);
        $query->where(['inspect_record_id' => $id, 'spot_id' => $this->spotId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);

        return $dataProvider;
    }

}
