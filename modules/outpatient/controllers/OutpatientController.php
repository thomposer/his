<?php

namespace app\modules\outpatient\controllers;

use app\common\Common;
use app\modules\spot_set\models\CheckListClinic;
use app\modules\spot_set\models\ClinicCure;
use app\modules\spot_set\models\UserAppointmentConfig;
use Yii;
use app\modules\outpatient\models\Outpatient;
use app\modules\outpatient\models\search\OutpatientSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Object;
use app\modules\triage\models\TriageInfo;
use app\modules\patient\models\Patient;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\CheckRecord;
use app\modules\patient\models\PatientRecord;
use yii\helpers\Url;
use yii\db\ActiveQuery;
use app\modules\spot\models\CureList;
use yii\data\ActiveDataProvider;
use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\db\Query;
use yii\web\Response;
use app\modules\spot\models\CheckList;
use app\modules\spot\models\Inspect;
use app\modules\outpatient\models\InspectRecord;
use app\modules\spot\models\RecipeList;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\spot\models\MedicalFee;
use app\modules\charge\models\ChargeInfo;
use app\modules\charge\models\ChargeRecord;
use app\modules\spot_set\models\Room;
use app\modules\outpatient\models\InspectRecordUnion;
use app\modules\spot\models\InspectItemUnion;
use app\modules\spot\models\InspectItem;
use yii\db\Exception;
use yii\data\ArrayDataProvider;
use app\modules\stock\models\StockInfo;
use app\modules\stock\models\Stock;
use app\modules\spot\models\Spot;
use app\modules\check\models\Check;
use app\modules\pharmacy\models\PharmacyRecord;
use app\modules\outpatient\models\MedicalFile;
use app\common\base\MultiModel;
use app\modules\triage\models\TriageInfoRelation;
use app\modules\outpatient\models\ChildExaminationBasic;
use app\modules\outpatient\models\ChildExaminationGrowth;
use app\modules\outpatient\models\ChildExaminationCheck;
use app\modules\outpatient\models\ChildExaminationAssessment;
use app\common\Percentage;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\outpatient\models\OutpatientRelation;
use app\modules\outpatient\models\Report;
use app\modules\charge\models\Order;
use app\modules\spot_set\models\Material;
use app\modules\outpatient\models\MaterialRecord;
use app\modules\stock\models\MaterialStockInfo;
use app\modules\spot\models\ChildCareTemplate;
use app\modules\follow\models\Follow;
use app\modules\outpatient\models\RecipeTemplate;
use app\modules\outpatient\models\RecipeTypeTemplate;
use app\modules\outpatient\models\DentalHistory;
use app\modules\outpatient\models\DentalHistoryRelation;
use app\modules\outpatient\models\FirstCheck;
use app\modules\pharmacy\models\RecipeBatch;
use app\modules\message\models\MessageCenter;
use app\modules\patient\models\PatientAllergy;
use app\modules\outpatient\models\AllergyOutpatient;
use app\modules\outpatient\models\ChildExaminationInfo;
use app\modules\patient\models\PatientSubmeter;
use app\modules\report\models\Report as ReportRecord;
use app\modules\spot_set\models\InspectClinic;
use app\modules\spot_set\models\InspectItemUnionClinic;
use app\modules\outpatient\models\InspectTemplate;
use app\modules\spot_set\models\RecipelistClinic;
use app\modules\spot_set\models\MedicalFeeClinic;
use app\modules\spot_set\models\ConsumablesClinic;
use app\modules\outpatient\models\ConsumablesRecord;
use app\modules\stock\models\ConsumablesStockInfo;
use app\modules\outpatient\models\CureTemplate;
use app\modules\spot\models\SpotConfig;
use app\modules\outpatient\models\PackageRecord;
use app\modules\spot_set\models\OutpatientPackageTemplate;
use app\modules\spot_set\models\OutpatientPackageInspect;
use app\modules\spot_set\models\OutpatientPackageCheck;
use app\modules\spot_set\models\OutpatientPackageCure;
use app\modules\spot_set\models\OutpatientPackageRecipe;
use app\modules\outpatient\models\CheckTemplate;
use app\modules\stock\models\MaterialStockDeductionRecord;
use app\modules\stock\models\ConsumablesStockDeductionRecord;
use app\modules\outpatient\models\DentalFirstTemplate;
use app\modules\outpatient\models\DentalReturnvisitTemplate;
use app\modules\outpatient\models\OrthodonticsReturnvisitRecord;
use app\modules\outpatient\models\OrthodonticsFirstRecord;
use app\modules\outpatient\models\OrthodonticsFirstRecordExamination;
use app\modules\outpatient\models\OrthodonticsFirstRecordFeatures;
use app\modules\outpatient\models\OrthodonticsFirstRecordModelCheck;
use app\modules\outpatient\models\OrthodonticsFirstRecordTeethCheck;
use app\modules\spot_set\models\UserPriceConfig;
/**
 * OutpatientController implements the CRUD actions for Outpatient model.
 */
class OutpatientController extends BaseController
{

    /**
     * 医生门诊  模板管理的Trait 便于复用
     */
    use TemplateTrait;

    /* 医生门诊  打印Trait 便于分组管理和复用 */

use PrintTrait;

use DentalFirstTrait;

use DentalReturnVisitTrait;

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'child-prink-info' => ['post'],
                    'record-prink-info' => ['post'],
                    'cure-prink-info' => ['post'],
                    'recipe-prink-info' => ['get', 'post'],
                    'get-recipe-template-info' => ['post'],
                    'teeth-print' => ['post']
                ],
            ],
        ];
    }

    /**
     * Lists all Outpatient models.
     * @return mixed
     */
    public function actionIndex() {
        $param = Yii::$app->request->queryParams;
        $param['this_user_id'] = $this->userInfo->id;
        $param['isSuperSystem'] = $this->isSuperSystem;
        $searchModel = new OutpatientSearch();

//        $dataProvider = $searchModel->search($param, $this->pageSize);
        $recordIdArr = [];
        if ($param['type'] == 3 || !isset($param['type'])) {
            $dataProvider = $searchModel->searchInfo($param, $this->pageSize);
            $models = $dataProvider->getModels();
            $recordIdArr = array_column($models, 'id');
        } else {
            $dataProvider = $searchModel->search($param, $this->pageSize);
            $recordIdArr = $dataProvider->keys;
        }
        //获取  随访信息
        $followData = Follow::getFollowByRecord($recordIdArr);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'followData' => $followData
        ]);
    }

    /*
     * 接诊
     */

    public function actionDiagnosis($id) {
        //修改接诊时间
        $triageModel = TriageInfo::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if ($triageModel != null && !empty($triageModel)) {
            $triageModel->diagnosis_time = time();
            $triageModel->save();
            $this->redirect(Url::to(['@outpatientOutpatientUpdate', 'id' => $id]));
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     *
     * @param type
     * @return 修改接诊时间
     */
    public function modifyDiagnosisTime($id) {
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $patientRecordModel = PatientRecord::find()->where(['id' => $id, 'spot_id' => $this->spotId])->one();
            if (empty($patientRecordModel) || is_null($patientRecordModel)) {
                throw new NotFoundHttpException('你所请求的页面不存在');
            }
            if ($patientRecordModel->status < 4) {
                $patientRecordModel->status = 4;
                $patientRecordModel->save();
            }
            $patientModel = Patient::findOne($patientRecordModel->patient_id);
            $patientModel->diagnosis_time = time();
            $patientModel->save();
//            //修改接诊时间
//            $triageModel = TriageInfo::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
//            $triageModel->diagnosis_time = time();
//            $triageModel->save();
            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
        }
    }

    /**
     * Displays a single Outpatient model.
     * 预览费用
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = $this->getViewList($id);
            if (empty($data)) {
                $data = [];
            }
            $dataProvider = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => false,
            ]);
            $chargeTotal = PatientRecord::find()->select(['check_price', 'cure_price', 'inspect_price', 'recipe_price', 'price', 'is_package'])->where(['id' => $id, 'spot_id' => $this->spotId])->asArray()->one();
            if ($chargeTotal['is_package'] == 1) {
                $chargeTotal['price'] = 0.00;
            }
            //就诊费用
            $total = array(
                'check_price' => 0,
                'inspect_price' => 0,
                'recipe_price' => 0,
                'cure_price' => 0,
                'materialPrice' => 0,
                'consumablesPrice' => 0,
                'packagePrice' => 0,
            );
            foreach ($data as $value) {//计算各项费用,不用查表,patient_record的四大医嘱价格字段已弃用
                if ($value['type'] == ChargeInfo::$checkType) {
                    $total['check_price'] += $value['price'];
                } else if ($value['type'] == ChargeInfo::$inspectType) {
                    $total['inspect_price'] += $value['price'];
                } else if ($value['type'] == ChargeInfo::$recipeType) {
                    $total['recipe_price'] += $value['price'] * $value['num'];
                } else if ($value['type'] == ChargeInfo::$cureType) {
                    $total['cure_price'] += $value['price'] * $value['num'];
                } else if ($value['type'] == ChargeInfo::$materialType) {
                    $total['materialPrice'] += $value['price'] * $value['num'];
                } else if ($value['type'] == ChargeInfo::$consumablesType) {
                    $total['materialPrice'] += $value['price'] * $value['num'];
                } else if ($value['type'] == ChargeInfo::$packgeType) {
                    $total['packagePrice'] += $value['price'] * $value['num'];
                }
            }
            $total['price'] = $chargeTotal['price'];
            return [

                'title' => "预览费用",
                'content' => $this->renderAjax('view', [
                    'dataProvider' => $dataProvider,
                    'chargeTotal' => $total,
                ]),
                'footer' => Html::button('关闭', ['class' => 'btn btn-cancel  btn-form', 'data-dismiss' => "modal"])
//                 .Html::button('关闭', ['class' => 'btn btn-default btn-form', 'data-dismiss' => "modal"])
            ];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @property $type (1-儿童体检，2-病历。默认为病历),
     * Updates an existing Outpatient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        $reportResult = $this->getRecordType($id);
        $recordType = $reportResult['record_type'];
        $model->scenario = 'outpatientMedicalRecord';
        $model->morbidityDate = $model->incidence_date ? date('Y-m-d', $model->incidence_date) : '';
        
        //儿童体检model
        if(1 == $recordType || $recordType == 0){//兼容历史数据，并且默认通用病历
            //病历的model
            $multiModel = new MultiModel([
                'models' => [
                    'triageInfo' => $model,
                    'triageInfoRelation' => $this->findTriageInfoRelation($id),
                    'outpatientRelation' => $this->findOutpatientRelation($id),
                ]
            ]);
            if ($reportResult['type_description'] != '方便门诊') {
                $multiModel->getModel('outpatientRelation')->scenario = 'basic';
            }
        }else if (2 == $recordType) {
            $childMultiModel = new MultiModel([
                'models' => [
//                    'basic' => $this->findChildExaminationBasic($id),
                    'growth' => $this->findChildExaminationGrowth($id),
                    'check' => $this->findChildExaminationCheck($id),
                    'assessment' => $this->findChildExaminationAssessment($id),
                    'info' => $this->findChildExaminationInfo($id),
                ]
            ]);
            /* 儿保模板list */
            $childTemplate = ChildCareTemplate::templateList();
        }

        //口腔专科model
        else if (4 == $recordType || 5 == $recordType) {
            $dentalHistory = $this->findDentalHistory($id);
//            $type = Yii::$app->request->get('recordType') ? Yii::$app->request->get('recordType') : ($dentalHistory->type ? $dentalHistory->type : 1);
            $type = 4 == $recordType ? 1 : 2;
            $dentalHistoryRelation = $this->findDentalHistoryRelation($id);
            if (2 == $type) {
                $dentalHistory->scenario = "return";
                $dentalHistory->type = 2;
            } else {
                $dentalHistory->scenario = "first";
                $dentalHistory->type = 1;
            }
        }
        
        else if($recordType == ReportRecord::$orthodonticsReturnvisit){
            $orthodonticsReturnvisit = $this->findOrthodonticsReturnbisit($id);
        } else if ($recordType == ReportRecord::$orthodonticsFirst) {
            $orthodonticsFirst = $this->findOrthodonticsFirst($id);
        }

        $firstCheckModel = new FirstCheck();
        $firstCheckModel->record_id = $id;
        if ($recordType == 1 && $multiModel->load(Yii::$app->request->post()) && $multiModel->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $multiModel->save();
                $firstCheckModel->load(Yii::$app->request->post());
//                if (!$firstCheckModel->firstCheckRecord() && Outpatient::getOrdersStatus($id)) {
//                    $dbTrans->rollBack();
//                    Yii::$app->getSession()->setFlash('error', '已开医嘱，初步诊断不能为空');
//                    return $this->redirect(Yii::$app->request->referrer);
//                }
                $firstCheckModel->outpatientSave(Yii::$app->request->post()['FirstCheck']);
                $allergyOutpatient = Yii::$app->request->post('allergyOutpatient');
                $allergyResult = AllergyOutpatient::saveAllergyOutpatient($multiModel->getModel('outpatientRelation')->hasAllergy, $allergyOutpatient, $id);
                if ($allergyResult['errorCode'] != 0) {
                    $dbTrans->rollBack();
                    $this->result['errorCode'] = $allergyResult['errorCode'];
                    $this->result['msg'] = $allergyResult['msg'];
                    return Json::encode($this->result);
                }
                MessageCenter::updateStatus($id, $this->spotId, 2);
                $dbTrans->commit();
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(Yii::$app->request->referrer . '&has_save=1');
            } catch (Exception $e) {
                $dbTrans->rollBack();
                Yii::$app->getSession()->setFlash('error', '保存失败');
                return $this->redirect(Yii::$app->request->referrer);
            }
        } else if (2 == $recordType && $childMultiModel->load(Yii::$app->request->post()) && $childMultiModel->validate()) {
            $firstCheckModel->load(Yii::$app->request->post());
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $infoModel = $childMultiModel->getModel('info');
                $infoModel->food_types = $infoModel->food_types ? implode(',', $infoModel->food_types) : '';
                $childMultiModel->models['info'] = $infoModel;
                if ($childMultiModel->save()) {
//                    $firstCheckModel->load(Yii::$app->request->post());
//                    if (!$firstCheckModel->firstCheckRecord() && Outpatient::getOrdersStatus($id)) {
//                        $dbTrans->rollBack();
//                        Yii::$app->getSession()->setFlash('error', '已开医嘱，初步诊断不能为空');
//                        return $this->redirect(Yii::$app->request->referrer);
//                    }
                    $firstCheckModel->outpatientSave(Yii::$app->request->post()['FirstCheck']);
                    $allergyOutpatient = Yii::$app->request->post('allergyOutpatient');
                    $allergyResult = AllergyOutpatient::saveAllergyOutpatient($childMultiModel->getModel('growth')->hasAllergy, $allergyOutpatient, $id);
                    if ($allergyResult['errorCode'] != 0) {
                        $dbTrans->rollBack();
                        $this->result['errorCode'] = $allergyResult['errorCode'];
                        $this->result['msg'] = $allergyResult['msg'];
                        return Json::encode($this->result);
                    }
                    PatientRecord::updateAll(['child_check_status' => 1], ['id' => $id, 'spot_id' => $this->spotId]);
                    $dbTrans->commit();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(Yii::$app->request->referrer);
                } else {
                    $dbTrans->rollBack();
                    Yii::$app->getSession()->setFlash('success', '保存失败');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            } catch (Exception $e) {
                Yii::error($e->errorInfo, 'outpatientUpdate');
                $dbTrans->rollBack();
                Yii::$app->getSession()->setFlash('error', '保存失败');
                return $this->redirect(Yii::$app->request->referrer);
            }
        } else if ((4 == $recordType || 5 == $recordType) && $dentalHistory->load(Yii::$app->request->post()) && $dentalHistory->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $dentalHistory->save();
                $db = Yii::$app->db;
                $db->createCommand()->delete(DentalHistoryRelation::tableName(), ['record_id' => $id, 'spot_id' => $this->spotId, 'dental_history_id' => $dentalHistory->id])->execute();
                $dentalHistoryRelationModel = new DentalHistoryRelation();
                $dentalHistoryRelationModel->spot_id = $this->spotId;
                $dentalHistoryRelationModel->dental_history_id = $dentalHistory->id;
                $dentalHistoryRelationModel->load(Yii::$app->request->post());
                if ($dentalHistoryRelationModel->validate()) {
                    foreach ($dentalHistoryRelationModel->content as $key => $value) {
                        if ($value || $dentalHistoryRelationModel->position[$key]) {
                            $rows[] = array(
                                'record_id' => $id,
                                'spot_id' => $this->spotId,
                                'record_type' => $type,
                                'dental_history_id' => $dentalHistory->id,
                                'type' => $dentalHistoryRelationModel->type[$key],
                                'position' => $dentalHistoryRelationModel->position[$key],
                                'content' => $dentalHistoryRelationModel->content[$key],
                                //牙位为空就不保存牙位病症
                                'dental_disease' => $dentalHistoryRelationModel->position[$key] != '' ?$dentalHistoryRelationModel->dental_disease[$key]:0,
                                'create_time' => time(),
                            );
                        }
                    }
                    $db->createCommand()->batchInsert(DentalHistoryRelation::tableName(), ['record_id', 'spot_id', 'record_type', 'dental_history_id', 'type', 'position', 'content', 'dental_disease','create_time'], $rows)->execute();
                    //保存过敏史
                    $allergyOutpatient = Yii::$app->request->post('allergyOutpatient');
                    $allergyResult = AllergyOutpatient::saveAllergyOutpatient($dentalHistory->hasAllergy, $allergyOutpatient, $id);
                    if ($allergyResult['errorCode'] != 0) {
                        $dbTrans->rollBack();
                        $this->result['errorCode'] = $allergyResult['errorCode'];
                        $this->result['msg'] = $allergyResult['msg'];
                        return Json::encode($this->result);
                    }
                    if ($dentalHistory->type == 2) {//若为复诊，因为没有必填字段，所以需要额外判断
                        if (trim($dentalHistory->returnvisit) != '' || trim($dentalHistory->advice) != '' || trim($dentalHistory->remarks) != '' || !empty($rows)) {
                            MessageCenter::updateStatus($id, $this->spotId, 2);
                        }
                    } else {
                        MessageCenter::updateStatus($id, $this->spotId, 2);
                    }
                    $firstCheckModel->load(Yii::$app->request->post());
//                    if (!$firstCheckModel->firstCheckRecord() && Outpatient::getOrdersStatus($id)) {
//                        $dbTrans->rollBack();
//                        Yii::$app->getSession()->setFlash('error', '已开医嘱，初步诊断不能为空');
//                        return $this->redirect(Yii::$app->request->referrer);
//                    }
                    $firstCheckModel->outpatientSave(Yii::$app->request->post()['FirstCheck']);
                    $dbTrans->commit();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(Url::to(['@outpatientOutpatientUpdate', 'id' => $id, 'has_save' => 1]));
                } else {
                    $dbTrans->rollBack();
                    $this->result['errorCode'] = 1009;
                    $this->result['msg'] = array_values($dentalHistoryRelationModel->errors)[0][0];
                    return Json::encode($this->result);
                }
            } catch (Exception $e) {
                Yii::error($e->errorInfo, 'outpatientUpdate');
                $dbTrans->rollBack();
                Yii::$app->getSession()->setFlash('error', '保存失败');
                return $this->redirect(Yii::$app->request->referrer);
            }
        } else if ($recordType == ReportRecord::$orthodonticsReturnvisit && $orthodonticsReturnvisit->load(Yii::$app->request->post()) && $orthodonticsReturnvisit->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $orthodonticsReturnvisit->save();
                //保存过敏史
                $allergyOutpatient = Yii::$app->request->post('allergyOutpatient');
                $allergyResult = AllergyOutpatient::saveAllergyOutpatient($orthodonticsReturnvisit->hasAllergy, $allergyOutpatient, $id);
                if ($allergyResult['errorCode'] != 0) {
                    $dbTrans->rollBack();
                    $this->result['errorCode'] = $allergyResult['errorCode'];
                    $this->result['msg'] = $allergyResult['msg'];
                    return Json::encode($this->result);
                }
                $firstCheckModel->outpatientSave(Yii::$app->request->post()['FirstCheck']);
                $dbTrans->commit();
                $this->result['errorCode'] = 0;
                $this->result['msg'] = '保存成功';
                return Json::encode($this->result);
            } catch (Exception $e) {
                Yii::error($e->errorInfo, 'outpatientUpdate');
                $dbTrans->rollBack();
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '保存失败';
                return Json::encode($this->result);
            }
        } else if ($recordType == ReportRecord::$orthodonticsFirst && $orthodonticsFirst->load(Yii::$app->request->post()) && $orthodonticsFirst->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $orthodonticsFirst->save();
//                 //保存过敏史
                $allergyOutpatient = Yii::$app->request->post('allergyOutpatient');
                $allergyResult = AllergyOutpatient::saveAllergyOutpatient($orthodonticsFirst->getModel('firstRecord')->hasAllergy, $allergyOutpatient, $id);
                if ($allergyResult['errorCode'] != 0) {
                    $dbTrans->rollBack();
                    $this->result['errorCode'] = $allergyResult['errorCode'];
                    $this->result['msg'] = $allergyResult['msg'];
                    return Json::encode($this->result);
                }
                $firstCheckModel->outpatientSave(Yii::$app->request->post()['FirstCheck']);
                $dbTrans->commit();
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(Url::to(['@outpatientOutpatientUpdate', 'id' => $id]));
            } catch (Exception $e) {
                Yii::error($e->errorInfo, 'outpatientUpdate');
                $dbTrans->rollBack();
                Yii::$app->getSession()->setFlash('error', '保存失败');
                return $this->redirect(Yii::$app->request->referrer);
            }
        } else {
            //存为病例模板
            if (Yii::$app->request->get('saveCase')) {
                return $this->viewCaseTemplate();
            }
            //修改接诊时间
            $this->modifyDiagnosisTime($id);

            /* 患者个人就诊信息 */
            $triageInfo = Patient::findTriageInfo($id);
            $userId = $triageInfo['id'];
            $triageInfo['ageAssessment'] = Patient::dateDiffage($triageInfo['birthtime'], $triageInfo['reportTime']); //测评年龄
            /* $triageInfo['birthtime'] = $triageInfo['birthday'];
              $triageInfo['ageAssessment'] = Patient::dateDiffage($triageInfo['birthday'],$triageInfo['diagnosis_time']);//测评年龄
              $triageInfo['birthday'] = Patient::dateDiffage($triageInfo['birthday'], time()); */
            $triageInfo['modalUrl'] = Url::to(['@triageTriageModal', 'id' => $id]);
            $triageInfo['medicalRecordUrl'] = Url::to(['@medicalRecord', 'id' => $userId]);
            $triageInfo['bornInfoModalUrl'] = Url::to(['@bornInfoModal', 'id' => $userId, 'recordId' => $id]);
            $triageInfo['ageOfYear'] = Patient::dateDiffageTime($triageInfo['birthtime'],$triageInfo['reportTime'])['year'];
            $triageInfo['has_medical'] = true;
            $triageInfo['has_info'] = true;
            $triageInfo['has_born_info'] = true;
            $triageInfo['receive_type'] = $triageInfo['type_description'];
            $triageInfo['receive_sex'] = Patient::$getSex[$triageInfo['sex']];
            $triageInfo['receive_birthtime'] = date('Y-m-d', $triageInfo['birthtime']);
            $triageInfo['receive_create_time'] = date('Y-m-d', $triageInfo['create_time']);
            if ($triageInfo['shrinkpressure'] || $triageInfo['diastolic_pressure']) {
                $triageInfo['shrinkpressure_and_diastolic_pressure'] = $triageInfo['shrinkpressure'] . "-" . $triageInfo['diastolic_pressure'];
            }
            /* 报告 */
            
            $has_save = Yii::$app->request->get('has_save');

            if (($case_id = Yii::$app->request->get('case_id')) && $has_save != 1) {
//                Outpatient::setModel($model, $case_id, $multiModel->getModel(triageInfoRelation));
                Outpatient::setModel($model, $case_id, $multiModel->getModel('triageInfoRelation'), $multiModel->getModel('outpatientRelation'));
            }
            $hasTemplateCase = isset($case_id) ? 1 : 2;

            if ((4 == $recordType || 5 == $recordType) && ($dental_case_id = Yii::$app->request->get('dental_case_id'))) {
                if (1 == $dentalHistory->type) {
                    DentalFirstTemplate::setModel($dentalHistory, $dental_case_id, $dentalHistoryRelation);
                } else {
                    DentalReturnvisitTemplate::setModel($dentalHistory, $dental_case_id, $dentalHistoryRelation);
                }
                $hasTemplateCase = isset($dental_case_id) ? 1 : 2;
            }
            /* 附件 */
            $medicalFile = $this->findMedicalFile($id);

            $triageInfo['incidence_date_print'] = date('Y-m-d', $model['incidence_date']); //病历打印发病日期
            $triageInfo['diagnosis_time'] = date('Y-m-d', $triageInfo['diagnosis_time']); //病历打印发病日期

        
            /* 初步诊断 */
            $firstCheckDataProvider = $this->findFirstCheckDataProvider($id);

            if ($recordType == 2) {
                //将食物种类专为数组
                if (!$childMultiModel->errors) {
                    $infoModel = $childMultiModel->getModel('info');
                    $infoModel->food_types = $infoModel->food_types ? explode(',', $infoModel->food_types) : '';
                    $childMultiModel->models['info'] = $infoModel;
                } else {
                    $triageInfo['child_check_status'] = 0; //如果验证规则不通过，则显示编辑态
                }

                //计算百分位
                $percentageArr['heightcm'] = $triageInfo['heightcm'] ? Percentage::getPercentage($triageInfo['heightcm'], $triageInfo['sex'], 1, $triageInfo['birthtime'], $triageInfo['reportTime']) : '';
                $percentageArr['weightkg'] = $triageInfo['weightkg'] ? Percentage::getPercentage($triageInfo['weightkg'], $triageInfo['sex'], 2, $triageInfo['birthtime'], $triageInfo['reportTime']) : '';
                $percentageArr['head_circumference'] = $triageInfo['head_circumference'] ? Percentage::getPercentage($triageInfo['head_circumference'], $triageInfo['sex'], 3, $triageInfo['birthtime'], $triageInfo['reportTime']) : '';
                $percentageArr['bmi'] = $triageInfo['bmi'] ? Percentage::getPercentage($triageInfo['bmi'], $triageInfo['sex'], 4, $triageInfo['birthtime'], $triageInfo['reportTime']) : '';
                $triageInfo['percentageArr'] = $percentageArr;
            }
            $allergy = AllergyOutpatient::getAllergyByRecord($id);
            $allergy = isset($allergy[$id]) ? $allergy[$id] : [];

//            $firstCheckCount = count($firstCheckDataProvider) ? TRUE : FALSE; //初步诊断是否填写
            $weightkg = ($triageInfo['weightkg'] == NULL) ? FALSE : TRUE; //体重是否填写
            $patientOtherInfo = [
                'firstCheckCount' => FirstCheck::getCount($id),
                'weightkg' => $weightkg,
            ];
            $chargeInfoList = ChargeInfo::getList($id,['outpatient_id'],['status' => [1, 2],'type' => [ChargeInfo::$priceType,ChargeInfo::$packgeType]]);
            
            return $this->render('update', [
                        'id' => $id,
                        'model' => $multiModel,
                        'childMultiModel' => $childMultiModel,
                        'triageInfo' => $triageInfo,
                        'hasTemplateCase' => $hasTemplateCase,
                        
                        'medicalFile' => $medicalFile,
                        'recipeTemplateMenu' => $recipeTemplateMenu,
                       
                        'childTemplate' => $childTemplate,
                        //口腔病历
                        'dentalHistory' => $dentalHistory,
                        'dentalHistoryRelation' => $dentalHistoryRelation,
                        'recordType' => $recordType,
                        'reportResult' => $reportResult,
                        /* 初步诊断 */
                        'firstCheckDataProvider' => $firstCheckDataProvider,
                        'allergy' => $allergy,
                        'patientOtherInfo' => $patientOtherInfo,
//                         'inspectBackStatus' => $inspectBackStatus,
                        //正畸
                        'orthodonticsReturnvisit' => $orthodonticsReturnvisit,
                        'orthodonticsFirst' => $orthodonticsFirst,
                        'chargeInfoList' => $chargeInfoList ? array_column($chargeInfoList, 'outpatient_id') : [],
            ]);
        }
    }

    /**
     * @desc 用户信息卡出生信息弹框.
     * @param integer $id 患者id
     * @return mixed
     * @throws NotFoundHttpException 异常页面
     */
    public function actionBornInfoModal($id) {
        $request = Yii::$app->request;

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = PatientSubmeter::findOne(['patient_id' => $id, 'spot_id' => $this->parentSpotId]);
            $recordId = \Yii::$app->request->get('recordId');
            if ($model == null) {
                $model = new PatientSubmeter();
                $model->patient_id = $id;
            }
            if ($model->load($request->post()) && $model->save()) {
                $birthInfo = PatientSubmeter::birthInfo($id);
                $spotInfo = Spot::getSpot();
                $triageInfo = Patient::findTriageInfo($recordId);
                $triageInfo['receive_sex'] = Patient::$getSex[$triageInfo['sex']];
                $triageInfo['diagnosis_time'] = date('Y-m-d', $triageInfo['diagnosis_time']); //病历打印发病日期
                $triageInfo['ageAssessment'] = Patient::dateDiffage($triageInfo['birthtime'], $triageInfo['reportTime']); //测评年龄
                $allergy = AllergyOutpatient::getAllergyByRecord($recordId);
                $allergy = isset($allergy[$recordId]) ? $allergy[$recordId] : [];
                $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
                $this->result['spotConfig'] = $spotConfig;
                $this->result['birthInfo'] = $birthInfo;
                $this->result['spotInfo'] = $spotInfo;
                $this->result['triageInfo'] = $triageInfo;
                $this->result['allergy'] = $allergy;
                return $this->result;
            } else if (!empty($model->errors)) {
                $intersectArr = array_intersect(array_keys($model->attributes), array_keys($model->errors));
                if (!empty($intersectArr)) {
                    $msg = [];
                    foreach ($intersectArr as $val) {
                        $msg[] = $model->errors[$val][0];
                    }
                }
                $this->result = [
                    'errorCode' => 10010,
                    'msg' => $msg
                ];
                return $this->result;
            } else {
                if (!is_array($model->childbirth_case)) {
                    $model->childbirth_case = $model->childbirth_case ? explode(',', $model->childbirth_case) : '';
                }
                $ret = [
                    'title' => "出生信息",
                    'content' => $this->renderAjax('@bornInfoModalView', ['model' => $model])
                ];
                return $ret;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     *
     * @return 病历tab打印接口
     */
    public function actionRecordPrinkInfo() {

        $id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;



        $soptInfo = Spot::getSpot();
        $pharmcyRecordModel = new PharmacyRecord();
        $repiceInfo = $pharmcyRecordModel->getRepiceInfo($id);
        $recipeRecordDataProvider = $this->findRecipeRecordDataProvider($id);
        if ($recipeRecordDataProvider) {
            foreach ($recipeRecordDataProvider as $key => $v) {
                $recipeRecordDataProvider[$key]['record_used'] = RecipeList::$getDefaultUsed[$v['used']];
                $recipeRecordDataProvider[$key]['record_unit'] = RecipeList::$getDoseUnit[$v['r_dose_unit']];
                $recipeRecordDataProvider[$key]['record_frequency'] = RecipeList::$getDefaultConsumption[$v['frequency']];
                $recipeRecordDataProvider[$key]['unit'] = RecipeList::$getUnit[$v['unit']];
                if ($v['status'] != 1) {
                    $nowTotalNums[$v['recipe_id']][] = $v['num'];
                }
            }
        }
        $outpatientInfo = OutpatientRelation::getOutpatientInfo($id);
        $outpatientInfo['cure_idea'] = nl2br(Html::encode($outpatientInfo['cure_idea']));
        $outpatientInfo['examination_check'] = nl2br(Html::encode($outpatientInfo['examination_check']));

        $firstCheck = FirstCheck::getFirstCheckInfo($id);
        $allergy = AllergyOutpatient::getAllergyByRecord($id);
        $allergy = isset($allergy) ? $allergy[$id] : [];
        if (!empty($allergy)) {
            foreach ($allergy as $key => $value) {
                $allergy[$key] = Html::encode($value);
            }
        }
        $this->result['soptInfo'] = $soptInfo;
        $this->result['repiceInfo'] = $repiceInfo;
        $this->result['recipeRecordDataProvider'] = $recipeRecordDataProvider;
        $this->result['outpatientInfo'] = $outpatientInfo;
        $this->result['firstCheck'] = Html::encode($firstCheck);
        $this->result['allergy'] = $allergy;
        return $this->result;
    }

    /**
     *
     * @return 治疗tab打印接口
     */
    public function actionCurePrinkInfo() {
        $totalPrice = 0;
        $id = Yii::$app->request->post('record_id');
        $cureId=Yii::$app->request->post('cureId');
        $where=[
            'spot_id' => $this->spotId,
            'record_id' => $id,
        ];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $pharmcyRecordModel = new PharmacyRecord();
        $cureRepiceInfo = $pharmcyRecordModel->getRepiceInfo($id, 3); //获取处方的开单医生和过敏史 以及诊断信息
        $cureRecord = CureRecord::find()->select(['id', 'name', 'unit', 'price', 'time', 'description', 'status', 'remark'])->where($where)->andFilterWhere(['id' => $cureId])->asArray()->all(); //治疗医嘱列表
        $soptInfo = Spot::getSpot();
        foreach ($cureRecord as $i => $value) {
            $cureRecord[$i]['columnTotalPrice'] = Common::num($value['time'] * $value['price']);
            $totalPrice += $cureRecord[$i]['columnTotalPrice'];
        }
        $firstCheck = FirstCheck::getFirstCheckInfo($id);
        $allergy = AllergyOutpatient::getAllergyByRecord($id);
        $allergy = isset($allergy) ? $allergy[$id] : [];
        if (!empty($allergy)) {
            foreach ($allergy as $key => $value) {
                $allergy[$key] = Html::encode($value);
            }
        }
        $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
        $this->result['soptInfo'] = $soptInfo;
        $this->result['cureRepiceInfo'] = $cureRepiceInfo;
        $this->result['pirntCureRecordInfo'] = $cureRecord;
        $this->result['totalPrice'] = Common::num($totalPrice);
        $this->result['firstCheck'] = Html::encode($firstCheck);
        $this->result['allergy'] = $allergy;
        $this->result['spotConfig'] = $spotConfig;
        return $this->result;
    }

    /**
     * @return 处方医嘱tab打印接口
     */
    public function actionRecipePrinkInfo($id,$filterType = 0) {
        $request = Yii::$app->request;
        if (Yii::$app->request->isAjax) {
            if ($request->isGet) {
                $model = new RecipeRecord();
                $recipeRecordDataProvider = $this->findRecipeRecordDataProvider($id,null,$filterType);
                Yii::$app->response->format = Response::FORMAT_JSON;
                $model->name = array_column($recipeRecordDataProvider, 'id');
                return [
                    'title' => "打印处方单",
                    'content' => $this->renderAjax('@outpatientCheckRecipeApplicationView', [
                        'model' => $model,
                        'dataProvider' => $recipeRecordDataProvider,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确定', ['class' => 'btn btn-default btn-form btn-recipe-check-application-print ', 'data-dismiss' => "modal",  'name' => $id . 'recipe-myshow','data-type' => $filterType])
                ];
            } else {
                $recipe_id = Yii::$app->request->post('recipe_id');
                Yii::$app->response->format = Response::FORMAT_JSON;

                //未勾选直接返回错误。
                if (empty($recipe_id)) {
                    $this->result['errorCode'] = 1001;
                    $this->result['msg'] = '请勾选要打印的项目';
                    return $this->result;
                }

                $pharmcyRecordModel = new PharmacyRecord();
                $pharmcyRepiceInfo = $pharmcyRecordModel->getRepiceInfo($id, 4);
                $spotInfo = Spot::getSpot();
                $recipeRecordDataProvider = $this->findRecipeRecordDataProvider($id, $recipe_id);
                $firstCheck = FirstCheck::getFirstCheckInfo($id);
                $allergy = AllergyOutpatient::getAllergyByRecord($id);
                $allergy = isset($allergy) ? $allergy[$id] : [];
                if (!empty($allergy)) {
                    foreach ($allergy as $key => $value) {
                        $allergy[$key] = Html::encode($value);
                    }
                }
                if ($recipeRecordDataProvider) {
                    $totalPrice = '';
                    foreach ($recipeRecordDataProvider as $key => $v) {

                        $recipeRecordDataProvider[$key]['record_used'] = RecipeList::$getDefaultUsed[$v['used']];
                        $recipeRecordDataProvider[$key]['record_unit'] = RecipeList::$getUnit[$v['unit']];
                        $recipeRecordDataProvider[$key]['record_frequency'] = RecipeList::$getDefaultConsumption[$v['frequency']];
                        $recipeRecordDataProvider[$key]['r_dose_unit'] = RecipeList::$getDoseUnit[$v['r_dose_unit']];
                        $recipeRecordDataProvider[$key]['dosage_form'] = RecipeList::$getType[$v['dosage_form']];
                        $recipeRecordDataProvider[$key]['single_total_price'] = Common::num($v['num'] * $v['price']);

                        $recipeRecordDataProvider[$key]['used_frequency'] = $recipeRecordDataProvider[$key]['record_used'] . ':' . $recipeRecordDataProvider[$key]['record_frequency'];
                        $recipeRecordDataProvider[$key]['type'] = RecipeList::$getAddress[$v['type']]; //取药地点转换
                        $totalPrice += $recipeRecordDataProvider[$key]['single_total_price'];
                    }
                }
                $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
                $this->result['spotInfo'] = $spotInfo;
                $this->result['PharmcyRepiceInfo'] = $pharmcyRepiceInfo;
                $this->result['recipeRecordDataProvider'] = $recipeRecordDataProvider;
                $this->result['totalPrice'] = Common::num($totalPrice);
                $this->result['firstCheck'] = Html::encode($firstCheck);
                $this->result['allergy'] = $allergy;
                $this->result['spotConfig'] = $spotConfig;

                return $this->result;
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

//     /**
//      * 选择打印的处方医嘱弹窗页面
//      */
//     public function actionRecipePrinkInfoCheck($id){
//         if (Yii::$app->request->isAjax) {
//             $model = new RecipeRecord();
//             $recipeRecordDataProvider = $this->findRecipeRecordDataProvider($id);
//             Yii::$app->response->format = Response::FORMAT_JSON;
//             return [
//                 'title' => "打印处方单",
//                 'content' => $this->renderAjax('@outpatientCheckRecipeApplicationView', [
//                     'model'=>$model,
//                     'recipeRecordDataProvider' => $recipeRecordDataProvider,
//                 ]),
//                 'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
//                     Html::button('确定', ['class' => 'btn btn-default btn-form btn-recipe-check-application-print ', 'name'=>$id.'recipe-myshow'])
//             ];
//         }else{
//             throw new NotFoundHttpException('你所请求的页面不存在');
//         }
//     }

    /**
     *
     * @return 门诊报告-实验室打印接口
     */
    public function actionReportInspectPrinkInfo() {

        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $record_id = Yii::$app->request->post('record_id');
        $pharmcyRecordModel = new PharmacyRecord();
        $inspect_repiceInfo = $pharmcyRecordModel->getRepiceInfo($record_id, 1, $id);
        $spotInfo = Spot::getSpot();
        $query = new Query();
        $query->from(['a' => InspectRecord::tableName()]);
        $query->select(['b.name', 'b.unit', 'b.reference', 'b.result', 'b.result_identification', 'a.report_time', 'inspect_name' => 'a.name']);
        $query->leftJoin(['b' => InspectRecordUnion::tableName()], '{{a}}.id = {{b}}.inspect_record_id');
        $query->where(['b.inspect_record_id' => $id, 'b.spot_id' => $this->spotId, 'a.record_id' => $record_id]);

        $inspectInfo = $query->all();

        foreach ($inspectInfo as $key => $v) {
            $inspectInfo[$key]['report_time'] = date('Y-m-d H:i:s', $inspectInfo[$key]['report_time']);
            $inspectInfo[$key]['result_identification'] = \app\modules\inspect\models\InspectRecordUnion::getResultIdentification($v['result_identification']);
        }

        $firstCheck = FirstCheck::getFirstCheckInfo($record_id);
        $allergy = AllergyOutpatient::getAllergyByRecord($record_id);
        $allergy = isset($allergy) ? $allergy[$record_id] : [];
        if (!empty($allergy)) {
            foreach ($allergy as $key => $value) {
                $allergy[$key] = Html::encode($value);
            }
        }
        $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
        $this->result['spotConfig'] = $spotConfig;
        $this->result['spotInfo'] = $spotInfo;
        $this->result['inspectRepiceInfo'] = $inspect_repiceInfo;
        $this->result['inspectInfo'] = $inspectInfo;
        $this->result['firstCheck'] = Html::encode($firstCheck);
        $this->result['allergy'] = $allergy;

        return $this->result;
    }

    /**
     * record_id string 就诊流水id
     * @return string 儿童检查记录
     */
    public function actionChildPrinkInfo() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $record_id = Yii::$app->request->post('record_id'); //流水id
        if (empty($record_id)) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '找不到该数据';
        } else {
            $spotInfo = Spot::getSpot();
            $query = new Query();
            $query->from(['a' => PatientRecord::tableName()]);
            $query->select(
                    [
                        'bregmatic', 'jaundice', 'result', 'c.remark', 'appearance', 'appearance_remark', 'skin',
                        'skin_remark', 'headFace', 'headFace_remark', 'eye', 'eye_remark', 'ear', 'ear_remark', 'nose',
                        'nose_remark', 'throat', 'throat_remark', 'tooth', 'throat_remark', 'chest', 'chest_remark', 'bellows',
                        'bellows_remark', 'cardiovascular', 'cardiovascular_remark', 'belly', 'belly_remark', 'genitals',
                        'genitals_remark', 'back', 'back_remark', 'limb', 'limb_remark', 'nerve', 'nerve_remark', 'communicate',
                        'coarse_action', 'fine_action', 'solve_problem', 'personal_society', 'score', 'evaluation_result',
                        'other_evaluation_type', 'other_evaluation_result', 'summary', 'summary_remark',
                        'f.heightcm', 'f.weightkg', 'f.head_circumference', 'g.sex', 'g.birthday', 'f.diagnosis_time',
                        'reportTime' => 'r.create_time', 'e.evaluation_type_result', 'e.evaluation_diagnosis', 'e.evaluation_guidance',
                        'h.sleep', 'h.shit', 'h.pee', 'h.visula_check', 'h.hearing_check', 'h.feeding_patterns', 'h.feeding_num', 'h.substitutes', 'h.dietary_supplement',
                        'h.food_types', 'h.inspect_content'
                    ]
            );
            $query->leftJoin(['b' => ChildExaminationBasic::tableName()], '{{a}}.id = {{b}}.record_id');
            $query->leftJoin(['c' => ChildExaminationGrowth::tableName()], '{{a}}.id = {{c}}.record_id');
            $query->leftJoin(['d' => ChildExaminationCheck::tableName()], '{{a}}.id = {{d}}.record_id');
            $query->leftJoin(['h' => ChildExaminationInfo::tableName()], '{{a}}.id = {{h}}.record_id');
            $query->leftJoin(['e' => ChildExaminationAssessment::tableName()], '{{a}}.id = {{e}}.record_id');
            $query->leftJoin(['f' => TriageInfo::tableName()], '{{a}}.id = {{f}}.record_id');
            $query->leftJoin(['g' => Patient::tableName()], '{{a}}.patient_id = {{g}}.id');
            $query->leftJoin(['r' => ReportRecord::tableName()], '{{a}}.id = {{r}}.record_id');
            $query->where(['a.id' => $record_id, 'a.spot_id' => $this->spotId]);
            $childExaminationInfo = $query->one();
            if (empty($childExaminationInfo)) {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '你所请求的页面找不到';
                return $this->result;
            }
            $childExaminationInfo['food_types'] = ChildExaminationInfo::foodType($childExaminationInfo['food_types']);
            $childExaminationInfo['bmi'] = Patient::getBmi($childExaminationInfo['heightcm'], $childExaminationInfo['weightkg']);
            $childExaminationInfo['heightPercentage'] = Percentage::getPercentage($childExaminationInfo['heightcm'], $childExaminationInfo['sex'], 1, $childExaminationInfo['birthday'], $childExaminationInfo['diagnosis_time']);
            $childExaminationInfo['weightPercentage'] = Percentage::getPercentage($childExaminationInfo['weightkg'], $childExaminationInfo['sex'], 2, $childExaminationInfo['birthday'], $childExaminationInfo['diagnosis_time']);
            $childExaminationInfo['headPercentage'] = Percentage::getPercentage($childExaminationInfo['head_circumference'], $childExaminationInfo['sex'], 3, $childExaminationInfo['birthday'], $childExaminationInfo['diagnosis_time']);
            $childExaminationInfo['bmiPercentage'] = Percentage::getPercentage($childExaminationInfo['bmi'], $childExaminationInfo['sex'], 4, $childExaminationInfo['birthday'], $childExaminationInfo['diagnosis_time']);
            $age = Patient::dateDiffageTime($childExaminationInfo['birthday'], $childExaminationInfo['reportTime']); //年龄是否大于5岁
            $childExaminationInfo['ageOldFive'] = false;
            if ($age['year'] <= 5) {
                $childExaminationInfo['ageOldFive'] = true;
            }
            $childExaminationInfo['allNormal'] = true;
            $allNormal = [
                $childExaminationInfo['appearance'], $childExaminationInfo['skin'],
                $childExaminationInfo['headFace'], $childExaminationInfo['eye'],
                $childExaminationInfo['ear'], $childExaminationInfo['nose'],
                $childExaminationInfo['throat'], $childExaminationInfo['tooth'],
                $childExaminationInfo['chest'],
                $childExaminationInfo['bellows'], $childExaminationInfo['cardiovascular'],
                $childExaminationInfo['belly'], $childExaminationInfo['genitals'],
                $childExaminationInfo['back'], $childExaminationInfo['limb'], $childExaminationInfo['nerve'],
            ];
            if (in_array(4, $allNormal) || (in_array(1, $allNormal) && (in_array(0, $allNormal) || in_array(4, $allNormal)))) {
                $childExaminationInfo['allNormal'] = false;
                $childExaminationInfo['isCheck'] = false;
            } elseif (in_array(0, $allNormal) && (array_sum($allNormal) == 0)) {
                $childExaminationInfo['allNormal'] = false;
                $childExaminationInfo['isCheck'] = true;
            }
            $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
            $this->result['spotConfig'] = $spotConfig;
            $this->result['spotInfo'] = $spotInfo;
            $this->result['childExaminationInfo'] = $childExaminationInfo;
        }
        return $this->result;
    }

    /**
     * @return 影像学打印接口
     */
    public function actionReportCheckPrinkInfo() {
        $id = Yii::$app->request->post('id');
        $record_id = Yii::$app->request->post('record_id');
        $pharmcyRecordModel = new PharmacyRecord();
        $check_repiceInfo = $pharmcyRecordModel->getRepiceInfo($record_id, 2);
        $spotInfo = Spot::getSpot();
        $checkInfo = Check::find()->select(['id', 'name', 'description', 'result', 'status', 'report_time'])->where(['id' => $this->spotId, 'record_id' => $record_id, 'id' => $id])->asArray()->one();

        $checkInfo['report_time'] = date('Y-m-d H:i:s', $checkInfo['report_time']);
        $firstCheck = FirstCheck::getFirstCheckInfo($record_id);
        $allergy = AllergyOutpatient::getAllergyByRecord($record_id);
        $allergy = isset($allergy) ? $allergy[$record_id] : [];
        if (!empty($allergy)) {
            foreach ($allergy as $key => $value) {
                $allergy[$key] = Html::encode($value);
            }
        }
        $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
        $this->result['spotConfig'] = $spotConfig;
        $this->result['spotInfo'] = $spotInfo;
        $this->result['checkRepiceInfo'] = $check_repiceInfo;
        $this->result['checkInfo'] = $checkInfo;
        $this->result['firstCheck'] = Html::encode($firstCheck);
        $this->result['allergy'] = $allergy;

        return Json::encode($this->result);
    }

    /**
     * @property 门诊-实验室检查api接口
     */
    public function actionInspectRecord($id) {
        $model = new InspectRecord();
        $model->record_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                if (($hasRecord = $this->checkRecord($model->record_id)) !== null) {
                    $db = Yii::$app->db;
                    $inspectList = [];
                    $inspectPrice = [];
                    $delNum = 0;
                    $addNum = 0;
                    if (is_array($model->inspect_id)) {
                        foreach ($model->inspect_id as $key => $v) {
                            $list = Json::decode($v);
                            //旧记录
                            if (isset($list['isNewRecord']) && $list['isNewRecord'] == 0) {
                                //若为删除操作 deleted == 1
                                if ($model->deleted[$key] == 1) {
                                    //删除相应的实验室检查信息记录
                                    $db->createCommand()->delete(InspectRecord::tableName(), ['id' => $list['id'], 'spot_id' => $this->spotId, 'record_id' => $model->record_id])->execute();
                                    $inspectPrice[] = -$list['price'];
                                    $delNum++;
                                    //若为接诊结束,则直接删除相应的收费详情记录
                                    if ($hasRecord['status'] == 5) {
                                        $result = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $list['id'], 'type' => ChargeInfo::$inspectType])->asArray()->one();
                                        //若为未收费
                                        if ($result['status'] == 0) {

                                            $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$inspectType, 'spot_id' => $this->spotId, 'record_id' => $model->record_id])->execute();
                                        } else if ($result['status'] == 2) {
                                            $inspectPrice[] = $list['price'];
                                        }
                                    }
                                }
                            } else {
                                //新记录,delete === 0或者空 为新增
                                if (!$model->deleted[$key]) {
                                    $inspectPrice[] = $list['price'];
                                    $itemList = [];
                                    $db->createCommand()->insert(
                                            InspectRecord::tableName(), [
                                        'record_id' => $model->record_id,
                                        'spot_id' => $this->spotId,
                                        'name' => $list['name'],
                                        'unit' => $list['unit'],
                                        'price' => $list['price'],
                                        'create_time' => time(),
                                        'update_time' => time(),
                                        'inspect_id' => $list['inspect_id'],
                                        'tag_id' => $list['tag_id'],
                                        'deliver' => $list['deliver'],
                                        'deliver_organization' => $list['deliver_organization'],
                                        'specimen_type' => $list['specimen_type'],
                                        'cuvette' => $list['cuvette'],
                                        'inspect_type' => $list['inspect_type'],
                                        'inspect_english_name' => $list['inspect_english_name'],
                                        'remark' => $list['remark'],
                                        'description' => $list['description'],
                                    ])->execute();
                                    $addNum++;
                                    $inspectRecordId = $db->lastInsertID;
//                                    $inspectItemList = InspectClinic::itemtList($list['id']);
                                    $inspectItemList = InspectItemUnionClinic::getInspectItemClinic($list['id']);
                                    if (isset($inspectItemList[$list['id']]) && !empty($inspectItemList[$list['id']])) {
                                        //保存检验医嘱配置关联的项目的列表
                                        foreach ($inspectItemList[$list['id']] as $v) {
                                            $itemList[] = [$inspectRecordId, $v['id'], $this->spotId, $model->record_id, $v['item_name'], $v['english_name'], $v['unit'], '', time(), time()];
                                        }
                                        $db->createCommand()->batchInsert(
                                                InspectRecordUnion::tableName(), ['inspect_record_id', 'item_id', 'spot_id', 'record_id', 'name', 'english_name', 'unit', 'reference', 'create_time', 'update_time'], $itemList)->execute();
                                    }
                                }
                            }
                        }
                        //若有新记录，则批量插入
                        if (array_sum($inspectPrice) > 0) {

                            //如果状态为接诊结束，则生成收费清单
                            if ($hasRecord['status'] == 5) {

                                $createList = $this->getChargeInfo(ChargeInfo::$inspectType, $model->record_id);
                                $chargeRecord = ChargeRecord::find()->select(['id'])->where(['spot_id' => $this->spotId, 'record_id' => $model->record_id, 'status' => 2])->asArray()->one();
                                if ($createList) {
                                    foreach ($createList as $c) {
                                        $rows[] = [$chargeRecord['id'], $this->spotId, $model->record_id, ChargeInfo::$inspectType, $c['id'], $c['name'], $c['unit'], $c['price'], 1, $this->userInfo->id, time(), $c['tag_id']];
                                    }
                                    //批量插入新增收费记录
                                    $db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'unit', 'unit_price', 'num', 'doctor_id', 'update_time', 'tag_id'], $rows)->execute();
                                }
                            }
                        }
                        //若有金额变动，则更新
                        if (count($inspectPrice) > 0) {
                            $updatePrice = array_sum($inspectPrice);
                            $this->savePrice($model->record_id, $updatePrice, ChargeInfo::$inspectType);
                        }
                    }
                    if ($addNum) {
                        //设置 待出报告的数量 
                        Outpatient::setPendingReport($this->spotId, $id, $addNum);
                    }
                    if ($delNum) {
                        //如果有删除 则删除待出报告的数量
                        Outpatient::setPendingReport($this->spotId, $id, $delNum, 2);
                    }
                    $dbTrans->commit();
                    $this->result['errorCode'] = 0;
                    $this->result['msg'] = '保存成功';
                }
            } catch (Exception $e) {
                $dbTrans->rollBack();
            }
        } else {
            $this->result['errorCode'] = 1014;
            $this->result['msg'] = $model->errors['inspect_id'][0];
        }
        return Json::encode($this->result);
    }

    /**
     * @property 门诊-影像学检查api接口
     */
    public function actionCheckRecord($id) {
        $model = new CheckRecord();
        $model->record_id = $id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                if (($hasRecord = $this->checkRecord($model->record_id)) !== null) {
                    $db = Yii::$app->db;
                    $checkList = [];
                    $checkPrice = [];
                    $delNum = 0;
                    $addNum = 0;
                    if (is_array($model->check_id)) {
                        foreach ($model->check_id as $key => $v) {
                            $list = Json::decode($v);
                            //旧记录
                            if (isset($list['isNewRecord']) && $list['isNewRecord'] === 0) {
                                //若未删除操作 deleted == 1
                                if ($model->deleted[$key] == 1) {
                                    //删除相应的影像学检查信息记录
                                    $db->createCommand()->delete(CheckRecord::tableName(), ['id' => $list['id'], 'spot_id' => $this->spotId, 'record_id' => $model->record_id])->execute();
                                    $checkPrice[] = -$list['price'];
                                    $delNum++;
                                    //若为接诊结束,则直接删除相应的收费详情记录
                                    if ($hasRecord['status'] == 5) {
                                        $result = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $list['id'], 'type' => ChargeInfo::$checkType])->asArray()->one();
                                        //若为未收费
                                        if ($result['status'] == 0) {
                                            $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$checkType, 'spot_id' => $this->spotId, 'record_id' => $model->record_id])->execute();
                                        }
                                        if ($result['status'] == 2) {
                                            $checkPrice[] = $list['price'];
                                        }
                                    }
                                }
                            } else {
                                //新记录,delete === 0或者空 为新增
                                if (!$model->deleted[$key]) {
                                    $checkPrice[] = $list['price'];
                                    $checkList[] = [$model->record_id, $this->spotId, $list['name'], $list['unit'], $list['price'], time(), time(), $list['tag_id']];
                                    $addNum++;
                                }
                            }
                        }
                        //若有新记录，则批量插入
                        if (count($checkList) > 0) {
                            $db->createCommand()->batchInsert(CheckRecord::tableName(), ['record_id', 'spot_id', 'name', 'unit', 'price', 'create_time', 'update_time', 'tag_id'], $checkList)->execute();

                            //如果状态为接诊结束，则生成收费清单
                            if ($hasRecord['status'] == 5) {

                                $createList = $this->getChargeInfo(ChargeInfo::$checkType, $model->record_id);
                                $chargeRecord = ChargeRecord::find()->select(['id'])->where(['spot_id' => $this->spotId, 'record_id' => $id])->asArray()->one();
                                $reportInfo = \app\modules\report\models\Report::reportRecord($model->record_id);
                                foreach ($createList as $c) {
                                    $rows[] = [$chargeRecord['id'], $this->spotId, $model->record_id, ChargeInfo::$checkType, $c['id'], $c['name'], $c['unit'], $c['price'], 1, $reportInfo['doctor_id'], time(), $c['tag_id']];
                                }
                                //批量插入新增收费记录
                                $db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'unit', 'unit_price', 'num', 'doctor_id', 'update_time', 'tag_id'], $rows)->execute();
                            }
                        }
                        //若有金额变动，则更新
                        if (count($checkPrice) > 0) {
                            $updatePrice = array_sum($checkPrice);
                            $this->savePrice($model->record_id, $updatePrice, ChargeInfo::$checkType);
                        }
                    }
                    if ($addNum) {
                        //设置 待出报告的数量 
                        Outpatient::setPendingReport($this->spotId, $id, $addNum);
                    }
                    if ($delNum) {
                        //如果有删除 则删除待出报告的数量
                        Outpatient::setPendingReport($this->spotId, $id, $delNum, 2);
                    }
                    $dbTrans->commit();
                    $this->result['errorCode'] = 0;
                    $this->result['msg'] = '保存成功';
                }
            } catch (Exception $e) {
                $dbTrans->rollBack();
            }
        } else {
            $this->result['errorCode'] = 1014;
            $this->result['msg'] = $model->errors['check_id'][0];
        }
        return Json::encode($this->result);
    }

    /**
     * @property 门诊-治疗api接口
     * @throws NotFoundHttpException
     */
    public function actionCureRecord($id) {

        $model = new CureRecord();
        $model->record_id = $id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                if (($hasRecord = $this->checkRecord($model->record_id)) !== null) {
                    $db = Yii::$app->db;
                    $cureList = [];
                    $curePrice = [];
                    if (count($model->cure_id) > 0) {
                        foreach ($model->cure_id as $key => $v) {
                            $list = Json::decode($v);
                            $cureWhere = ['id' => $list['id'], 'spot_id' => $this->spotId, 'record_id' => $model->record_id];
                            //旧记录
                            if (isset($list['isNewRecord']) && $list['isNewRecord'] === 0) {
                                //若未删除操作 deleted == 1 , 修改操作deleted == ''
                                if ($model->deleted[$key] == 1) {
                                    //删除相应的治疗检查信息记录
                                    $db->createCommand()->delete(CureRecord::tableName(), $cureWhere)->execute();
                                    $curePrice[] = -($list['price'] * intval($model->time[$key])); //删除的费用
                                    //若为接诊结束,则直接删除相应的收费详情记录
                                    if ($hasRecord['status'] == 5) {
                                        $result = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $list['id'], 'type' => ChargeInfo::$cureType])->asArray()->one();
                                        //若为未收费
                                        if ($result['status'] == 0) {

                                            $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$cureType])->execute();
                                        }
                                        if ($result['status'] == 2) {
                                            $curePrice[] = $list['price'] * intval($model->time[$key]);
                                        }
                                    }
                                } else {
                                    //修改对应的记录
                                    $time = CureRecord::find()->select(['time'])->where($cureWhere)->asArray()->one()['time'];
                                    $diffCount = intval($model->time[$key]) - intval($time);
                                    $db->createCommand()->update(CureRecord::tableName(), ['time' => $model->time[$key], 'description' => $model->description[$key]], $cureWhere)->execute();
                                    //若次数有改变，则更新收费金额
                                    $updateAttributes = [
                                        'num' => $model->time[$key],
                                    ];
                                    if ($diffCount != 0) {
                                        $curePrice[] = $list['price'] * $diffCount;
                                        $updateAttributes = [
                                            'num' => $model->time[$key],
                                            'discount' => 100,
                                            'discount_price' => 0
                                        ];
                                        Order::updateOrderStatus($id); //临时方案
                                    }
                                    $db->createCommand()->update(ChargeInfo::tableName(), $updateAttributes, ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$cureType])->execute();
                                }
                            } else {
                                //新记录,delete === 0或者空 为新增
                                if (!$model->deleted[$key]) {
                                    $curePrice[] = $list['price'] * intval($model->time[$key]);
                                    $cureList[] = [$model->record_id, $this->spotId, $list['name'], $list['unit'], $list['price'], $model->time[$key], $model->description[$key], time(), time(), $list['tag_id']];
                                }
                            }
                        }
                        //若有新记录，则批量插入
                        if (count($cureList) > 0) {
                            $db->createCommand()->batchInsert(CureRecord::tableName(), ['record_id', 'spot_id', 'name', 'unit', 'price', 'time', 'description', 'create_time', 'update_time', 'tag_id'], $cureList)->execute();

                            //如果状态为接诊结束，则生成收费清单
                            if ($hasRecord['status'] == 5) {

                                $createList = $this->getChargeInfo(ChargeInfo::$cureType, $model->record_id);
                                $chargeRecordId = $this->findChargeRecord($model->record_id);
                                $reportInfo = \app\modules\report\models\Report::reportRecord($model->record_id);
                                foreach ($createList as $c) {
                                    $rows[] = [$chargeRecordId, $this->spotId, $model->record_id, ChargeInfo::$cureType, $c['id'], $c['name'], $c['unit'], $c['price'], $c['time'], $reportInfo['doctor_id'], time(), $c['tag_id']];
                                }
                                //批量插入新增收费记录
                                $db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'unit', 'unit_price', 'num', 'doctor_id', 'update_time', 'tag_id'], $rows)->execute();
                            }
                        }
                        //若有金额变动，则更新
                        if (count($curePrice) > 0) {
                            $updatePrice = array_sum($curePrice); //变动的金额
                            $this->savePrice($model->record_id, $updatePrice, ChargeInfo::$cureType);
                        }
                    }
                    $dbTrans->commit();
                    $this->result['errorCode'] = 0;
                    $this->result['msg'] = '保存成功';
                }
            } catch (Exception $e) {
                $dbTrans->rollBack();
            }
        } else {

            $this->result['errorCode'] = '1014';
            $this->result['msg'] = $model->errors['time'][0];
        }

        return Json::encode($this->result);
    }

    /**
     * @property 门诊-处方api接口
     */
    public function actionRecipeRecord($id) {
        $model = new RecipeRecord();
        $model->record_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            $db = Yii::$app->db;
            try {
                if (($hasRecord = $this->checkRecord($model->record_id)) !== null) {
                    $recipeList = [];
                    $recipePrice = [];
                    if (count($model->recipe_id)) {
                        foreach ($model->recipe_id as $key => $v) {
                            $list = Json::decode($v);
                            $recipeWhere = ['id' => $list['id'], 'spot_id' => $this->spotId, 'record_id' => $model->record_id];
                            //判断该处方记录是否有配置 皮试，若没有，默认为0;
                            if ($model->type[$key] == 1) {//本院发药
                                //旧记录
                                if (isset($list['isNewRecord']) && $list['isNewRecord'] === 0) {
                                    //若为删除操作 deleted == 1 , 修改操作deleted == ''
                                    if ($model->deleted[$key] == 1) {
                                        //删除相应的处方检查信息记录
                                        $recipeRecordInfo = RecipeRecord::find()->select(['id', 'name', 'type', 'num', 'price', 'cure_id', 'curelist_id'])->where($recipeWhere)->asArray()->one();
                                        if ($this->getCureRecordStatus($recipeRecordInfo['cure_id']) && $this->getChargeInfoStatus($recipeRecordInfo['cure_id'], ChargeInfo::$cureType)) {
                                            $db->createCommand()->delete(CureRecord::tableName(), ['id' => $recipeRecordInfo['cure_id'], 'spot_id' => $this->spotId, 'record_id' => $model->record_id])->execute();
                                            $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $recipeRecordInfo['cure_id'], 'spot_id' => $this->spotId, 'type' => ChargeInfo::$cureType])->execute();
                                        }
                                        $db->createCommand()->delete(RecipeRecord::tableName(), $recipeWhere)->execute();
                                        $recipePrice[] = -($list['price'] * intval($model->num[$key])); //删除的费用
                                        //若为接诊结束,则直接删除相应的收费详情记录
                                        if ($hasRecord['status'] == 5) {
                                            $result = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $list['id'], 'type' => ChargeInfo::$recipeType])->asArray()->one();
                                            //若为未收费
                                            if ($result['status'] == 0) {

                                                $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$recipeType])->execute();
                                            }
                                            if ($result['status'] == 2) {
                                                $recipePrice[] = $list['price'] * intval($model->num[$key]);
                                            }
                                        }
                                    } else {
                                        //修改对应的记录
                                        $time = RecipeRecord::find()->select(['id', 'name', 'type', 'num', 'price', 'cure_id', 'curelist_id'])->where($recipeWhere)->asArray()->one();
                                        $diffCount = intval($model->num[$key]) - intval($time['num']);
                                        $cureListId = $time['curelist_id'];
                                        $cureId = $time['cure_id'];
                                        if ($model->skin_test_status[$key] != 1 && $cureId != 0) {//不需要皮试，删除记录
                                            if ($this->getCureRecordStatus($cureId) && $this->getChargeInfoStatus($cureId, ChargeInfo::$cureType)) {
                                                $db->createCommand()->delete(CureRecord::tableName(), ['id' => $cureId, 'spot_id' => $this->spotId, 'record_id' => $model->record_id])->execute();
                                                $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $cureId, 'spot_id' => $this->spotId, 'type' => ChargeInfo::$cureType])->execute();
                                                $cureListId = 0;
                                                $cureId = 0;
                                            }
                                        } else if ($model->skin_test_status[$key] == 1 && $cureId == 0) {//若为不需要皮试转需要皮试
                                            $cureListId = $model->curelist_id[$key];
                                            $cureInfo = ClinicCure::getCure($cureListId);
                                            $db->createCommand()->insert(CureRecord::tableName(), ['record_id' => $model->record_id, 'spot_id' => $this->spotId, 'name' => $cureInfo['name'], 'unit' => $cureInfo['unit'], 'price' => $cureInfo['price'], 'time' => 1, 'tag_id' => $cureInfo['tag_id'], 'type' => 1, 'create_time' => time(), 'update_time' => time()])->execute();
                                            $cureId = $db->lastInsertID;
                                            if ($hasRecord['status'] == 5) {//接诊结束，插入收费记录
                                                $chargeRecordId = $this->findChargeRecord($model->record_id);
                                                $reportInfo = \app\modules\report\models\Report::reportRecord($model->record_id);
                                                $db->createCommand()->insert(ChargeInfo::tableName(), ['charge_record_id' => $chargeRecordId, 'spot_id' => $this->spotId, 'record_id' => $model->record_id, 'type' => ChargeInfo::$cureType, 'outpatient_id' => $cureId, 'name' => $cureInfo['name'], 'unit' => $cureInfo['unit'], 'unit_price' => $cureInfo['price'], 'num' => 1, 'doctor_id' => $reportInfo['doctor_id'], 'update_time' => time(), 'tag_id' => $cureInfo['tag_id']])->execute(); //新增收费项
                                            }
                                        }
                                        $db->createCommand()->update(RecipeRecord::tableName(), ['dose' => $model->dose[$key], 'used' => $model->used[$key], 'frequency' => $model->frequency[$key], 'day' => $model->day[$key], 'num' => $model->num[$key], 'type' => $model->type[$key], 'description' => $model->description[$key], 'dose_unit' => $model->dose_unit[$key], 'skin_test_status' => $model->skin_test_status[$key] ? $model->skin_test_status[$key] : 0, 'curelist_id' => $cureListId, 'cure_id' => $cureId], $recipeWhere)->execute();
                                        //若为外购转内购
                                        if ($time['type'] == 2 && $model->type[$key] == 1) {
                                            $recipePrice[] = $list['price'] * intval($model->num[$key]); //则新增处方费用
                                            //如果状态为接诊结束，则生成收费清单
                                            if ($hasRecord['status'] == 5) {
                                                //插入新增收费记录
                                                $chargeRecordId = $this->findChargeRecord($model->record_id);
                                                $db->createCommand()->insert(ChargeInfo::tableName(), [
                                                    'charge_record_id' => $chargeRecordId,
                                                    'spot_id' => $this->spotId,
                                                    'record_id' => $model->record_id,
                                                    'type' => ChargeInfo::$recipeType,
                                                    'outpatient_id' => $time['id'],
                                                    'name' => $time['name'],
                                                    'unit' => $list['unit'],
                                                    'unit_price' => $time['price'],
                                                    'num' => $model->num[$key],
                                                    'doctor_id' => $this->userInfo->id,
                                                    'create_time' => time(),
                                                    'update_time' => time()
                                                ])->execute();

                                                //插入皮试收费记录
                                                if ($time['cure_id'] && $model->skin_test_status[$key] == 1) {
                                                    $cureInfo = ClinicCure::getCure($cureListId);
                                                    $db->createCommand()->insert(ChargeInfo::tableName(), [
                                                        'charge_record_id' => $chargeRecordId,
                                                        'spot_id' => $this->spotId,
                                                        'record_id' => $model->record_id,
                                                        'type' => ChargeInfo::$cureType,
                                                        'outpatient_id' => $time['cure_id'],
                                                        'name' => $cureInfo['name'],
                                                        'unit' => $cureInfo['unit'],
                                                        'unit_price' => $cureInfo['price'],
                                                        'num' => 1,
                                                        'doctor_id' => $this->userInfo->id,
                                                        'create_time' => time(),
                                                        'update_time' => time(),
                                                        'tag_id' => $cureInfo['tag_id'],
                                                    ])->execute();
                                                }
                                            }
                                        } else {
                                            $updateAttributes = [
                                                'num' => $model->num[$key],
                                            ];
                                            //若次数有改变，则更新收费金额
                                            if ($diffCount != 0) {
                                                $recipePrice[] = $list['price'] * $diffCount; //修改后的费用
                                                $updateAttributes = [
                                                    'num' => $model->num[$key],
                                                    'discount' => 100,
                                                    'discount_price' => 0
                                                ];
                                                Order::updateOrderStatus($id);
                                            }
                                            $db->createCommand()->update(ChargeInfo::tableName(), $updateAttributes, ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$recipeType])->execute();
                                        }
                                    }
                                } else {
                                    //新记录,delete === 0或者空 为新增
                                    if (!$model->deleted[$key]) {
                                        $cureListId = 0;
                                        $cureId = 0;
                                        if ($model->skin_test_status[$key] == 1) {//需要皮试，插入皮试记录
                                            $cureListId = $model->curelist_id[$key];
                                            $cureInfo = ClinicCure::getCure($cureListId);
                                            $db->createCommand()->insert(CureRecord::tableName(), ['record_id' => $model->record_id, 'spot_id' => $this->spotId, 'name' => $cureInfo['name'], 'unit' => $cureInfo['unit'], 'price' => $cureInfo['price'], 'time' => 1, 'tag_id' => $cureInfo['tag_id'], 'type' => 1, 'create_time' => time(), 'update_time' => time()])->execute();
                                            $cureId = $db->lastInsertID;
                                        }
                                        $recipePrice[] = $list['price'] * intval($model->num[$key]);
                                        $recipeList[] = [$list['recipelist_id'], $model->record_id, $this->spotId, $list['name'], $list['product_name'], $list['en_name'], $list['specification'], $list['price'], $model->dose[$key], $model->used[$key], $model->day[$key], $list['unit'], $list['type'], $model->frequency[$key], $model->num[$key], $model->type[$key], $model->description[$key], $model->dose_unit[$key], $list['medicine_description_id'], $model->skin_test_status[$key] ? $model->skin_test_status[$key] : 0, $list['skin_test'], time(), time(), $list['remark'], $list['tag_id'], $cureListId, $cureId, $list['high_risk'],$list['drug_type']];
                                    }
                                }
                            } else {//外购 不扣减库存不生成收费记录
                                RecipeRecord::recipeOut($list, $model, $key, $hasRecord);
                            }
                        }
                        //若有新记录，则批量插入
                        if (count($recipeList) > 0) {
                            $db->createCommand()->batchInsert(RecipeRecord::tableName(), ['recipe_id', 'record_id', 'spot_id', 'name', 'product_name', 'en_name', 'specification', 'price', 'dose', 'used', 'day', 'unit', 'dosage_form', 'frequency', 'num', 'type', 'description', 'dose_unit', 'medicine_description_id', 'skin_test_status', 'skin_test', 'create_time', 'update_time', 'remark', 'tag_id', 'curelist_id', 'cure_id', 'high_risk','drug_type'], $recipeList)->execute();
                            //如果状态为接诊结束，则生成收费清单
                            if ($hasRecord['status'] == 5) {
                                $createList = $this->getChargeInfo(ChargeInfo::$recipeType, $model->record_id);
                                $chargeRecordId = $this->findChargeRecord($model->record_id);
                                $reportInfo = \app\modules\report\models\Report::reportRecord($model->record_id);
                                foreach ($createList as $c) {
                                    $rows[] = [$chargeRecordId, $this->spotId, $model->record_id, ChargeInfo::$recipeType, $c['id'], $c['name'], $c['unit'], $c['price'], $c['num'], $reportInfo['doctor_id'], time(), $c['tag_id']];
                                }

                                $createCureList = $this->getChargeInfo(ChargeInfo::$cureType, $model->record_id);
                                foreach ($createCureList as $c) {
                                    $rows[] = [$chargeRecordId, $this->spotId, $model->record_id, ChargeInfo::$cureType, $c['id'], $c['name'], $c['unit'], $c['price'], $c['time'], $reportInfo['doctor_id'], time(), $c['tag_id']];
                                }
                                //批量插入新增收费记录
                                $db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'unit', 'unit_price', 'num', 'doctor_id', 'update_time', 'tag_id'], $rows)->execute();
                            }
                        }
                        //若有金额变动，则更新
                        if (count($recipePrice) > 0) {
                            $updatePrice = array_sum($recipePrice); //变动的金额
                            $this->savePrice($model->record_id, $updatePrice, ChargeInfo::$recipeType);
                        }
                    }
                    $db->createCommand()->update(PatientRecord::tableName(), ['pharmacy_record_time' => time()], ['id' => $id])->execute();
                    $dbTrans->commit();
                    $this->result['errorCode'] = 0;
                    $this->result['msg'] = '保存成功';
                }
            } catch (Exception $e) {
                Yii::error($e->errorInfo, 'recipeRecord');
                $dbTrans->rollBack();
            }
        } else {
            $this->result['errorCode'] = '1014';
            if (isset($model->errors['dose_unit'])) {
                $this->result['msg'] = $model->errors['dose_unit'][0];
            } else {
                $this->result['msg'] = $model->errors['recipe_id'][0];
            }
        }

        return Json::encode($this->result);
    }

    public function actionMaterialRecord($id) {
        $model = new MaterialRecord();
        $model->record_id = $id;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                if (($hasRecord = $this->checkRecord($model->record_id)) !== null) {
                    $db = Yii::$app->db;
                    $cureList = [];
                    $curePrice = [];
                    if (count($model->material_id) > 0) {
                        foreach ($model->material_id as $key => $v) {
                            $list = Json::decode($v);
                            $materialWhere = ['id' => $list['id'], 'spot_id' => $this->spotId, 'record_id' => $model->record_id];
                            //旧记录
                            if (isset($list['isNewRecord']) && $list['isNewRecord'] === 0) {
                                //若未删除操作 deleted == 1 , 修改操作deleted == ''
                                if ($model->deleted[$key] == 1) {
                                    //删除相应的治疗检查信息记录
                                    $db->createCommand()->delete(MaterialRecord::tableName(), $materialWhere)->execute();
                                    //若为接诊结束,则直接删除相应的收费详情记录
                                    if ($hasRecord['status'] == 5) {
                                        $result = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $list['id'], 'type' => ChargeInfo::$materialType])->asArray()->one();
                                        //若为未收费
                                        if ($result['status'] == 0) {

                                            $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$materialType])->execute();
                                        }
                                    }
                                } else {
                                    //修改对应的记录
                                    $num = MaterialRecord::find()->select(['num'])->where($materialWhere)->asArray()->one()['num'];
                                    $diffCount = intval($model->num[$key]) - intval($num);
                                    $db->createCommand()->update(MaterialRecord::tableName(), ['num' => $model->num[$key], 'remark' => $model->remark[$key]], $materialWhere)->execute();
                                    //若次数有改变，则更新收费金额
                                    $updateAttributes = [
                                        'num' => $model->num[$key],
                                    ];
                                    if ($diffCount != 0) {
                                        $updateAttributes = [
                                            'num' => $model->num[$key],
                                            'discount' => 100,
                                            'discount_price' => 0
                                        ];
                                        Order::updateOrderStatus($id); //临时方案
                                    }
                                    $db->createCommand()->update(ChargeInfo::tableName(), $updateAttributes, ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$materialType])->execute();
                                }
                            } else {
                                //新记录,delete === 0或者空 为新增
                                if (!$model->deleted[$key]) {
                                    $materialRecordInfo = Material::getOneInfo($list['id'], ['name', 'product_name', 'en_name', 'specification', 'type', 'attribute', 'unit', 'manufactor', 'meta', 'price', 'default_price', 'tag_id']);
                                    $materialList[] = [
                                        $model->record_id, $this->spotId, $list['id'], $materialRecordInfo['name'],
                                        $materialRecordInfo['product_name'], $materialRecordInfo['en_name'], $materialRecordInfo['specification'], $materialRecordInfo['type'],
                                        $materialRecordInfo['attribute'], $materialRecordInfo['unit'], $materialRecordInfo['manufactor'],
                                        $materialRecordInfo['meta'], $materialRecordInfo['price'], $materialRecordInfo['default_price'],
                                        $model->num[$key], $model->remark[$key], time(), time(), $materialRecordInfo['tag_id']];
                                }
                            }
                        }
                        //若有新记录，则批量插入
                        if (count($materialList) > 0) {
                            $db->createCommand()->batchInsert(MaterialRecord::tableName(), ['record_id', 'spot_id', 'material_id', 'name', 'product_name', 'en_name', 'specification', 'type', 'attribute', 'unit', 'manufactor', 'meta', 'price', 'default_price', 'num', 'remark', 'create_time', 'update_time', 'tag_id'], $materialList)->execute();

                            //如果状态为接诊结束，则生成收费清单
                            if ($hasRecord['status'] == 5) {

                                $createList = $this->getChargeInfo(ChargeInfo::$materialType, $model->record_id);
                                $chargeRecordId = $this->findChargeRecord($model->record_id);
                                $reportInfo = \app\modules\report\models\Report::reportRecord($model->record_id);
                                foreach ($createList as $c) {
                                    $rows[] = [$chargeRecordId, $this->spotId, $model->record_id, ChargeInfo::$materialType, $c['id'], $c['name'], $c['specification'], $c['unit'], $c['price'], $c['num'], $reportInfo['doctor_id'], time(), $c['tag_id']];
                                }
                                //批量插入新增收费记录
                                $db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'specification', 'unit', 'unit_price', 'num', 'doctor_id', 'update_time', 'tag_id'], $rows)->execute();
                            }
                        }
                    }
                    $ret = MaterialStockDeductionRecord::updateStockInfo($id, MaterialStockDeductionRecord::$materialRecord);
                    if ($ret['errorCode']) {
                        $dbTrans->rollBack();
                        $this->result['errorCode'] = 1014;
                        $this->result['msg'] = $ret['message'];
                        return $this->result;
                    }
                    $dbTrans->commit();
                    $this->result['errorCode'] = 0;
                    $this->result['msg'] = '保存成功';
                    $this->result['data'] = MaterialStockInfo::getTotal();
                }
            } catch (Exception $e) {
                $dbTrans->rollBack();
                Yii::error(json_encode($e->errorInfo, true), 'material-record');
            }
        } else {

            $this->result['errorCode'] = '1014';
            $this->result['msg'] = $model->errors['num'][0];
        }

        return $this->result;
    }

    public function actionConsumablesRecord($id) {
        $model = new ConsumablesRecord();
        $model->record_id = $id;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                if (($hasRecord = $this->checkRecord($model->record_id)) !== null) {
                    $db = Yii::$app->db;
                    $consumablesList = [];
                    $consumablesPrice = [];
                    if (count($model->consumables_id) > 0) {
                        foreach ($model->consumables_id as $key => $v) {
                            $list = Json::decode($v);
                            $consumablesWhere = ['id' => $list['id'], 'spot_id' => $this->spotId, 'record_id' => $model->record_id];
                            //旧记录
                            if (isset($list['isNewRecord']) && $list['isNewRecord'] === 0) {
                                //若未删除操作 deleted == 1 , 修改操作deleted == ''
                                if ($model->deleted[$key] == 1) {
                                    //删除相应的治疗检查信息记录
                                    $db->createCommand()->delete(ConsumablesRecord::tableName(), $consumablesWhere)->execute();
                                    //若为接诊结束,则直接删除相应的收费详情记录
                                    if ($hasRecord['status'] == 5) {
                                        $result = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $list['id'], 'type' => ChargeInfo::$consumablesType])->asArray()->one();
                                        //若为未收费
                                        if ($result['status'] == 0) {

                                            $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$consumablesType])->execute();
                                        }
                                    }
                                } else {
                                    //修改对应的记录
                                    $num = ConsumablesRecord::find()->select(['num'])->where($consumablesWhere)->asArray()->one()['num'];
                                    $diffCount = intval($model->num[$key]) - intval($num);
                                    $db->createCommand()->update(ConsumablesRecord::tableName(), ['num' => $model->num[$key], 'remark' => $model->remark[$key]], $consumablesWhere)->execute();
                                    //若次数有改变，则更新收费金额
                                    $updateAttributes = [
                                        'num' => $model->num[$key],
                                    ];
                                    if ($diffCount != 0) {
                                        $updateAttributes = [
                                            'num' => $model->num[$key],
                                            'discount' => 100,
                                            'discount_price' => 0
                                        ];
                                        Order::updateOrderStatus($id); //临时方案
                                    }
                                    $db->createCommand()->update(ChargeInfo::tableName(), $updateAttributes, ['outpatient_id' => $list['id'], 'type' => ChargeInfo::$consumablesType])->execute();
                                }
                            } else {
                                //新记录,delete === 0或者空 为新增
                                if (!$model->deleted[$key]) {
                                    $consumablesRecordInfo = ConsumablesClinic::getConsumablesList(['a.id' => $list['id']])[$list['id']];
                                    $consumablesList[] = [
                                        $this->spotId, $model->record_id, $consumablesRecordInfo['consumables_id'], $consumablesRecordInfo['name'],
                                        $consumablesRecordInfo['product_name'], $consumablesRecordInfo['en_name'], $consumablesRecordInfo['type'],
                                        $consumablesRecordInfo['specification'], $consumablesRecordInfo['unit'], $model->num[$key], $consumablesRecordInfo['meta'],
                                        $consumablesRecordInfo['manufactor'], $model->remark[$key], $consumablesRecordInfo['tag_id'],
                                        $consumablesRecordInfo['price'], $consumablesRecordInfo['default_price'], time(), time()];
                                }
                            }
                        }
                        //若有新记录，则批量插入
                        if (count($consumablesList) > 0) {
                            $db->createCommand()->batchInsert(ConsumablesRecord::tableName(), ['spot_id', 'record_id', 'consumables_id', 'name', 'product_name', 'en_name', 'type', 'specification', 'unit', 'num', 'meta', 'manufactor', 'remark', 'tag_id', 'price', 'default_price', 'create_time', 'update_time'], $consumablesList)->execute();

                            //如果状态为接诊结束，则生成收费清单
                            if ($hasRecord['status'] == 5) {

                                $createList = $this->getChargeInfo(ChargeInfo::$consumablesType, $model->record_id);
                                $chargeRecordId = $this->findChargeRecord($model->record_id);
                                $reportInfo = \app\modules\report\models\Report::reportRecord($model->record_id);
                                foreach ($createList as $c) {
                                    $rows[] = [$chargeRecordId, $this->spotId, $model->record_id, ChargeInfo::$consumablesType, $c['id'], $c['name'], $c['specification'], $c['unit'], $c['price'], $c['num'], $reportInfo['doctor_id'], time(), $c['tag_id']];
                                }
                                //批量插入新增收费记录
                                $db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'specification', 'unit', 'unit_price', 'num', 'doctor_id', 'update_time', 'tag_id'], $rows)->execute();
                            }
                        }
                    }

                    $ret = ConsumablesStockDeductionRecord::updateStockInfo($id);
                    if ($ret['errorCode']) {
                        $dbTrans->rollBack();
                        $this->result['errorCode'] = 1014;
                        $this->result['msg'] = $ret['message'];
                        return $this->result;
                    }

                    $dbTrans->commit();
                    $this->result['errorCode'] = 0;
                    $this->result['msg'] = '保存成功';
                    $this->result['data'] = ConsumablesStockInfo::getTotal();
                }
            } catch (Exception $e) {
                $dbTrans->rollBack();
                Yii::error(json_encode($e->errorInfo, true), 'consumables-record');
            }
        } else {
            Yii::error($model->errors, 'consumables-record');
            $this->result['errorCode'] = '1014';
            $this->result['msg'] = $model->errors['num'][0];
        }

        return $this->result;
    }

    /**
     *
     * @param 就诊流水ID $id
     */
    public function actionEnd($id) {
        if ($this->checkRecord($id)) {
            $request = Yii::$app->request;
            $model = PatientRecord::findOne(['id' => $id, 'spot_id' => $this->spotId]);
            $packagePrice = $model->price;
            $model->scenario = 'end';

            $saveText = $model->status == 5?'保存':'结束';
            $saveAndCreateFollowText = $model->status == 5?'保存并新建随访计划':'结束并新建随访计划';

            //展示结束就诊的服务类型
            $reportModel =  ReportRecord::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
            $model->type_description = $reportModel->type_description;

            if ($request->isAjax) {
                /*
                 *   Process for ajax request
                 */
                $recordCount = Follow::recordCount($model->id);
                //获取诊所下的诊金配置
//                $query = new Query();
//                $medicalFeeData = $query->from(['a' => MedicalFeeClinic::tableName()])
//                        ->leftJoin(['b' => MedicalFee::tableName()], '{{a}}.fee_id = {{b}}.id')
//                        ->select(['b.remarks', 'b.price'])
//                        ->where(['a.spot_id' => $this->spotId, 'a.status' => 1])
//                        ->orderBy('price asc')
//                        ->all();
//                $medicalFeeList = [];
//                foreach ($medicalFeeData as $key => $value) {
//                    $medicalFeeList[$value['price']] = $value['remarks'] ? $value['price'] . ' (' . $value['remarks'] . ')' : $value['price'];
//                }
                Yii::$app->response->format = Response::FORMAT_JSON;
                if ($request->isGet) {
//                    if ($model->price == 0.0 && $model->status < 5 && $model->is_package != 1) {
//                        $model->price = null;
//                    }
                    $btn = Html::button('取消', ['class' => 'btn btn-cancel btn-form btn-form-custom', 'data-dismiss' => "modal"]) .
                            Html::button($saveText, ['class' => 'btn btn-default btn-form record-end btn-form-custom', 'type' => "submit"]);
                    $btnFollow = '';
                    if (isset(Yii::$app->view->params['permList']['role']) || in_array(Yii::getAlias('@followIndexCreate'), Yii::$app->view->params['permList'])) {
                        $btnFollow = Html::button($saveAndCreateFollowText, ['class' => 'btn btn-cancel btn-form follow-create btn-form-custom']);
                    }
                    return [
                        'title' => "结束就诊",
                        'content' => $this->renderAjax('end', [
                            'model' => $model,
                            'isPackage' => $model->is_package
                        ]),
                        'footer' => empty($recordCount) ? $btn . $btnFollow : $btn
                    ];
                } else if ($model->load($request->post()) && $model->validate()) {
                    $dbTrans = Yii::$app->db->beginTransaction();
                    try {
                        if ($model->status == PatientRecord::$setStatus[5]) {//判断是否已为结束就诊状态。若是，则判断诊疗费是否已经收费
                            //需要区分是否开过医嘱套餐
                            $model->is_package == 2 ? $type = ChargeInfo::$priceType : $type = ChargeInfo::$packgeType;
                            $checkResult = ChargeInfo::find()->select(['status'])->where(['record_id' => $id, 'type' => $type])->asArray()->one();
                            if ($checkResult['status'] != 0) {//若不是没收费，则禁止结束就诊
                                Yii::$app->getSession()->setFlash('error', '本次的诊疗费已收');
                                return [
                                    'forceReload' => false,
                                    'forceClose' => true,
                                    'forceRedirect' => Url::to(['@outpatientOutpatientIndex']),
                                ];
                            }
                        }
                        //获取当前就诊记录的基本信息，录入病历库
                        $triageQuery = new Query();
                        $triageQuery->from(['a' => PatientRecord::tableName()]);
                        $triageQuery->select([
                            'a.id',
                            'b.heightcm', 'b.weightkg', 'b.bloodtype', 'b.blood_type_supplement', 'b.temperature_type', 'b.head_circumference',
                            'b.temperature', 'b.breathing', 'b.pulse', 'b.shrinkpressure', 'b.diastolic_pressure', 'b.oxygen_saturation',
                            'b.pain_score', 'b.room_id', 'b.fall_score', 'b.treatment_type', 'b.treatment',
                            'c.personalhistory', 'c.genetichistory'
                        ]);
                        $triageQuery->leftJoin(['b' => TriageInfo::tableName()], '{{a}}.id = {{b}}.record_id');
                        $triageQuery->leftJoin(['c' => OutpatientRelation::tableName()], '{{a}}.id = {{c}}.record_id');
                        $triageQuery->where(['a.id' => $id, 'a.spot_id' => $this->spotId]);
                        $triageInfo = $triageQuery->one();


                        if ($model->status == PatientRecord::$setStatus[4]) {//若就诊状态为接诊中，则生成收费清单
                            $model->status = PatientRecord::$setStatus[5];
                            $this->saveCharge($id, $model->patient_id, $model->price, $model->is_package);
                            $roomModel = Room::findOne(['id' => $triageInfo['room_id'], 'spot_id' => $this->spotId]);
                            if ($roomModel) {
                                $roomModel->clean_status = 1;
                                $roomModel->record_id = 0;
                                $roomModel->treatment_time = time();
                                $roomModel->save(); //更新诊室管理状态为待整理
                            }
                        }
                        //防止医嘱套餐诊疗费被修改
                        if ($model->is_package == 1) {
                            $model->price = $packagePrice;
                        }else{
                            $model->price = $model->getOldAttribute('price');//不让修改诊金，但需验证必填
                            $model->type_description = $model->getOldAttribute('type_description');//不让修改诊金，但需验证必填
                        }
                        $model->end_time = time();
                        if ($model->save()) {
                            $patientModel = Patient::findOne(['id' => $model->patient_id, 'spot_id' => $this->parentSpotId]);
                            if ($patientModel == null || empty($patientModel)) {
                                throw new NotFoundHttpException('你所请求的页面不存在');
                            }

                            $patientModel->heightcm = $triageInfo['heightcm'];
                            $patientModel->weightkg = $triageInfo['weightkg'];
                            $patientModel->bloodtype = $triageInfo['bloodtype'];
                            $patientModel->blood_type_supplement = $triageInfo['blood_type_supplement'];
                            $patientModel->temperature_type = $triageInfo['temperature_type'];
                            $patientModel->temperature = $triageInfo['temperature'];
                            $patientModel->breathing = $triageInfo['breathing'];
                            $patientModel->head_circumference = $triageInfo['head_circumference'];
                            $patientModel->pulse = $triageInfo['pulse'];
                            $patientModel->shrinkpressure = $triageInfo['shrinkpressure'];
                            $patientModel->diastolic_pressure = $triageInfo['diastolic_pressure'];
                            $patientModel->oxygen_saturation = $triageInfo['oxygen_saturation'];
                            $patientModel->pain_score = $triageInfo['pain_score'];
                            $patientModel->fall_score = $triageInfo['fall_score'];
                            $patientModel->treatment_type = $triageInfo['treatment_type'];
                            $patientModel->treatment = $triageInfo['treatment'];
                            $patientModel->personalhistory = $triageInfo['personalhistory'];
                            $patientModel->genetichistory = $triageInfo['genetichistory'];
                            //非医嘱套餐收费
                            if ($model->status == PatientRecord::$setStatus[5] && $model->is_package == 2) {
                                $chargeInfoModel = $this->findChargeInfoModel($id,$reportModel->doctor_id);
                                if ($model->makeup == 1) {//非补录才有诊疗费
                                    $chargeInfoModel->scenario = 'outpatientEnd';
                                    if ($model->price != $chargeInfoModel->unit_price) {
                                        $chargeInfoModel->discount = 100;
                                        $chargeInfoModel->discount_price = 0;
                                        $chargeInfoModel->card_discount_price = 0;
                                        $chargeInfoModel->discount_reason = '';
                                        Order::updateOrderStatus($id); //临时方案
                                    }
                                    $chargeInfoModel->unit_price = $model->price;
                                    $chargeInfoModel->save();
                                }
                            }
                            $firstRecord = PatientRecord::getFirstRecord($patientModel->id, $model->id);
                            $patientModel->first_record = $firstRecord;
                            if ($patientModel->save()) {
                                //同步过敏史
                                PatientAllergy::syncPatientAllergy($patientModel->id, $model->id);
                                $dbTrans->commit();
                                $submitType = Yii::$app->request->post('PatientRecord')['submitType'];
                                MessageCenter::saveMedicalTips($id); //检查是否有填写病历
                                if ($submitType == 1) {//普通保存
                                    Yii::$app->getSession()->setFlash('success', '结束就诊成功');
                                    return [
                                        'forceReload' => false,
                                        'forceClose' => true,
                                        'forceRedirect' => Url::to(['@outpatientOutpatientIndex']),
                                    ];
                                } else {//保存并新增随访计划
                                    return [
                                        'forceReload' => false,
                                        'forceClose' => true,
                                        'forceRedirect' => Url::to(['@followIndexCreate', 'patientId' => $model->patient_id, 'recordId' => $model->id]),
                                    ];
                                }
                            }
                        }
                        Yii::error($patientModel->errors, '结束就诊');
                        Yii::$app->getSession()->setFlash('error', '结束就诊失败，请确认病历中信息正确，并重新结束就诊');
                        $dbTrans->rollBack();
                        return [
                            'forceReload' => false,
                            'forceClose' => true,
                            'forceRedirect' => Url::to(['@outpatientOutpatientUpdate', 'id' => $id]),
                        ];
                    } catch (Exception $e) {
                        Yii::error($e->errorInfo, '结束就诊');
                        $dbTrans->rollBack();
                    }
                } else {
                    $btn = Html::button('取消', ['class' => 'btn btn-cancel btn-form btn-form-custom', 'data-dismiss' => "modal"]) .
                            Html::button($saveText, ['class' => 'btn btn-default btn-form record-end btn-form-custom', 'type' => "submit"]);
                    $btnFollow = '';
                    if (isset(Yii::$app->view->params['permList']['role']) || in_array(Yii::getAlias('@followIndexCreate'), Yii::$app->view->params['permList'])) {
                        $btnFollow = Html::button($saveAndCreateFollowText, ['class' => 'btn btn-cancel btn-form follow-create btn-form-custom']);
                    }
                    return [
                        'title' => "结束就诊",
                        'content' => $this->renderAjax('end', [
                            'model' => $model,
                            'isPackage' => $model->is_package
                        ]),
                        'footer' => empty($recordCount) ? $btn . $btnFollow : $btn
                    ];
                }
            } else {
                /*
                 *   Process for non-ajax request
                 */
                throw new NotFoundHttpException('你所请求的页面不存在');
            }
        }
    }

    /**
     * @return 返回预览费用清单
     * @param 就诊流水id $id
     */
    private function getViewList($id) {
        //所有退费的列表
        $rows = [];

        $chargeInfo = ChargeInfo::find()->select(['outpatient_id', 'type'])->where(['record_id' => $id, 'spot_id' => $this->spotId, 'status' => 2])->asArray()->all();
        if ($chargeInfo) {
            foreach ($chargeInfo as $v) {
                $rows[$v['type']][] = $v['outpatient_id'];
            }
        }
        $inspectList = InspectRecord::find()->select(['name', 'price', 'unit'])->where(['record_id' => $id, 'spot_id' => $this->spotId, 'package_record_id' => 0])->andFilterWhere(['not in', 'id', $rows[ChargeInfo::$inspectType]])->asArray()->all();
        $checkList = CheckRecord::find()->select(['name', 'unit', 'price'])->where(['record_id' => $id, 'spot_id' => $this->spotId, 'package_record_id' => 0])->andFilterWhere(['not in', 'id', $rows[ChargeInfo::$checkType]])->asArray()->all();
        $recipeList = RecipeRecord::find()->select(['name', 'unit', 'price', 'num'])->where(['record_id' => $id, 'spot_id' => $this->spotId, 'type' => 1, 'package_record_id' => 0])->andFilterWhere(['not in', 'id', $rows[ChargeInfo::$recipeType]])->asArray()->all();
        $cureList = CureRecord::find()->select(['name', 'unit', 'price', 'num' => 'time'])->where(['record_id' => $id, 'spot_id' => $this->spotId, 'package_record_id' => 0])->andFilterWhere(['not in', 'id', $rows[ChargeInfo::$cureType]])->asArray()->all();
        $materialList = MaterialRecord::find()->select(['name', 'unit', 'price', 'num'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->andFilterWhere(['not in', 'id', $rows[ChargeInfo::$materialType]])->asArray()->all();
        $consumablesList = ConsumablesRecord::find()->select(['name', 'unit', 'price', 'num'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->andFilterWhere(['not in', 'id', $rows[ChargeInfo::$consumablesType]])->asArray()->all();
        $packageList = PackageRecord::find()->select(['id', 'name', 'price', 'remarks'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->andFilterWhere(['not in', 'id', $rows[ChargeInfo::$packgeType]])->asArray()->all();
        if ($packageList) {
            $packageList[0]['detail'] = $this->getPackageDetail($packageList[0]['id']);
            $packageList[0]['detail']['feeRemarks'] = $packageList[0]['remarks'] ? Html::encode("{$packageList[0]['remarks']}") : '';
        }
        foreach ($packageList as &$value) {
            $value['unit'] = '-';
            $value['num'] = 1;
        }
        $inspectList = $this->addType($inspectList, ChargeInfo::$inspectType);
        $checkList = $this->addType($checkList, ChargeInfo::$checkType);
        $recipeList = $this->addType($recipeList, ChargeInfo::$recipeType);
        $cureList = $this->addType($cureList, ChargeInfo::$cureType);
        $materialList = $this->addType($materialList, ChargeInfo::$materialType);
        $consumablesList = $this->addType($consumablesList, ChargeInfo::$consumablesType);
        $packageList = $this->addType($packageList, ChargeInfo::$packgeType);
        $result = array_merge($inspectList, $checkList, $cureList, $recipeList, $materialList, $consumablesList, $packageList);
        return $result;
    }

    /**
     *
     * @param 医嘱数组列表 $list
     * @param 医嘱类型 $type
     * @return 添加医嘱类型进医嘱数组列表内
     */
    private function addType($list, $type) {
        if (!empty($list)) {
            foreach ($list as $key => $v) {
                $list[$key]['type'] = $type;
            }
        }
        return $list;
    }

    /**
     * @param 就诊流水ID $id
     * @param 就诊患者ID $patient_id
     * @param 诊疗费 $price
     * @property 生成就诊费用清单
     */
    private function saveCharge($id, $patient_id, $price, $isPackage) {
        $chargeRecordModel = new ChargeRecord();
        $chargeRecordModel->record_id = $id;
        $chargeRecordModel->patient_id = $patient_id;
        $chargeRecordModel->status = 2;
        $result = $chargeRecordModel->save();
        $reportInfo = \app\modules\report\models\Report::find()->select(['doctor_id'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->asArray()->one();
        if ($result) {
            if ($isPackage == 2) {
                $rows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$priceType, 0, '诊疗费', '', '次', $price, 1, $reportInfo['doctor_id'], time(), time(), 0];
            }
            $inspectResult = InspectRecord::find()->select(['id', 'name', 'unit', 'price', 'tag_id'])->where(['record_id' => $id, 'spot_id' => $this->spotId, 'package_record_id' => 0])->asArray()->all();
            if ($inspectResult) {
                foreach ($inspectResult as $v) {
                    $rows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$inspectType, $v['id'], $v['name'], '', $v['unit'], $v['price'], 1, $reportInfo['doctor_id'], time(), time(), $v['tag_id']];
                }
            }

            $checkResult = CheckRecord::find()->select(['id', 'name', 'unit', 'price', 'tag_id'])->where(['record_id' => $id, 'spot_id' => $this->spotId, 'package_record_id' => 0])->asArray()->all();
            if ($checkResult) {
                foreach ($checkResult as $v) {
                    $rows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$checkType, $v['id'], $v['name'], '', $v['unit'], $v['price'], 1, $reportInfo['doctor_id'], time(), time(), $v['tag_id']];
                }
            }
            $cureResult = CureRecord::find()->select(['id', 'name', 'unit', 'price', 'time', 'tag_id'])->where(['record_id' => $id, 'spot_id' => $this->spotId, 'package_record_id' => 0])->asArray()->all();
            if ($cureResult) {
                foreach ($cureResult as $v) {
                    $rows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$cureType, $v['id'], $v['name'], '', $v['unit'], $v['price'], $v['time'], $reportInfo['doctor_id'], time(), time(), $v['tag_id']];
                }
            }
            $recipeResult = RecipeRecord::find()->select(['id', 'name', 'unit', 'specification', 'price', 'num', 'tag_id'])->where(['record_id' => $id, 'spot_id' => $this->spotId, 'type' => 1, 'package_record_id' => 0])->asArray()->all(); //只取内购的
            if ($recipeResult) {
                foreach ($recipeResult as $v) {
                    $rows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$recipeType, $v['id'], $v['name'], $v['specification'], $v['unit'], $v['price'], $v['num'], $reportInfo['doctor_id'], time(), time(), $v['tag_id']];
                }
            }

            $materialResult = MaterialRecord::getList(['id', 'name', 'specification', 'num', 'price', 'unit', 'tag_id'], ['record_id' => $id, 'spot_id' => $this->spotId]);
            if ($materialResult) {
                foreach ($materialResult as $v) {
                    $rows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$materialType, $v['id'], $v['name'], $v['specification'], $v['unit'], $v['price'], $v['num'], $reportInfo['doctor_id'], time(), time(), $v['tag_id']];
                }
            }

            $consumablesResult = ConsumablesRecord::getList(['id', 'name', 'specification', 'num', 'price', 'unit', 'tag_id'], ['record_id' => $id, 'spot_id' => $this->spotId]);
            if ($consumablesResult) {
                foreach ($consumablesResult as $v) {
                    $rows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$consumablesType, $v['id'], $v['name'], $v['specification'], $v['unit'], $v['price'], $v['num'], $reportInfo['doctor_id'], time(), time(), $v['tag_id']];
                }
            }
            Yii::$app->db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'specification', 'unit', 'unit_price', 'num', 'doctor_id', 'create_time', 'update_time', 'tag_id'], $rows)->execute();
            if ($isPackage == 1) {
                $packgeResult = PackageRecord::find()->select(['id', 'name', 'price'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->asArray()->one(); //医嘱套餐收费流水
                $packageRows[] = [$chargeRecordModel->id, $this->spotId, $id, ChargeInfo::$packgeType, $packgeResult['id'], $packgeResult['name'], '', '', $packgeResult['price'], 1, $reportInfo['doctor_id'], time(), time(), 0];
                Yii::$app->db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'specification', 'unit', 'unit_price', 'num', 'doctor_id', 'create_time', 'update_time', 'tag_id'], $packageRows)->execute();
            }
        }
    }

    /**
     *
     * @param 就诊流水ID $record_id
     * @return 返回当前就诊水流的收费记录表ID
     */
    private function findChargeRecord($record_id) {
        return ChargeRecord::find()->select(['id'])->where(['spot_id' => $this->spotId, 'record_id' => $record_id])->asArray()->one()['id'];
    }

    /**
     *
     * @param 变更的金额数 $updatePrice
     * @param 就诊流水ID $record_id
     * @param 门诊金额变动类型 $type (1-实验室检查,2-影像学检查,3-治疗,4-处方)
     * @return 保存变更后的金额
     */
    private function savePrice($id, $updatePrice, $type) {
        return true;
        //该方法已被弃用
        $model = PatientRecord::findOne(['id' => $id, 'spot_id' => $this->spotId]);
        if ($type == 1) {
            $model->inspect_price = $model->inspect_price + $updatePrice;
        } else if ($type == 2) {
            $model->check_price = $model->check_price + $updatePrice;
        } else if ($type == 3) {
            $model->cure_price = $model->cure_price + $updatePrice;
        } else if ($type == 4) {
            $model->recipe_price = $model->recipe_price + $updatePrice;
        }

        $result = $model->save();
    }

    /**
     *
     * @param 收费项类型(1-实验室检查,2-影像学检查,3-治疗,4-处方) $type
     * @param 就诊流水ID $record_id
     * @property 获取新增未收费记录列表
     */
    private function getChargeInfo($type, $record_id) {

        $chargeList = ChargeInfo::find()->select(['outpatient_id'])->where(['record_id' => $record_id, 'type' => $type])->indexBy('outpatient_id')->asArray()->all();
        $outpatientList = [];
        $query = new Query();
        $andWhere = [];
        if ($type == ChargeInfo::$inspectType) {
            $query->from(InspectRecord::tableName());
            $query->select(['id', 'name', 'unit', 'price', 'tag_id']);
            $andWhere = ['package_record_id' => 0];
        } else if ($type == ChargeInfo::$checkType) {
            $query->from(CheckRecord::tableName());
            $query->select(['id', 'name', 'unit', 'price', 'tag_id']);
            $andWhere = ['package_record_id' => 0];
        } else if ($type == ChargeInfo::$cureType) {
            $query->from(CureRecord::tableName());
            $query->select(['id', 'name', 'unit', 'price', 'time', 'tag_id']);
            $andWhere = ['package_record_id' => 0];
        } else if ($type == ChargeInfo::$recipeType) {
            $query->from(RecipeRecord::tableName());
            $query->select(['id', 'name', 'unit', 'price', 'num', 'tag_id']);
            $andWhere = ['type' => 1, 'package_record_id' => 0];
        } else if ($type == ChargeInfo::$materialType) {
            $query->from(MaterialRecord::tableName());
            $query->select(['id', 'name', 'unit', 'price', 'num', 'tag_id', 'specification']);
        } else if ($type == ChargeInfo::$consumablesType) {
            $query->from(ConsumablesRecord::tableName());
            $query->select(['id', 'name', 'unit', 'price', 'num', 'tag_id', 'specification']);
        }
        $outpatientList = $query->where(['record_id' => $record_id, 'spot_id' => $this->spotId])->andWhere($andWhere)->indexBy('id')->all();
        return array_diff_key($outpatientList, $chargeList);
    }

    /**
     * 先判断该就诊记录是否属于该诊所，若属于则可操作。否则返回404
     * @param 就诊记录 $id
     */
    private function checkRecord($id) {

        $hasRecord = PatientRecord::find()->select(['id', 'status'])->where(['id' => $id, 'spot_id' => $this->spotId])->asArray()->one();
        if (!$hasRecord) {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        return $hasRecord;
    }

    /**
     * @property 获取医生门诊附件
     * @param record_id
     * @return array 获取医生门诊附件
     */
    protected function findMedicalFile($id) {
        $query = new Query();
        $query->from(['m' => MedicalFile::tableName()]);
        $query->select(['file_id' => 'group_concat(m.id)', 'file_url' => 'group_concat(m.file_url)', 'file_name' => 'group_concat(m.file_name)', 'size' => 'group_concat(m.size)']);
        $query->where(['m.record_id' => $id, 'm.spot_id' => $this->spotId]);
        $medicalFile = $query->one();
        return $medicalFile;
    }

    /**
     * @return 返回该就诊记录的处方记录信息
     * @param 就诊流水id $id
     * @param 包含的处方id $recipeIds
     */
    protected function findRecipeRecordDataProvider($id,$recipeIds = null,$filterType = 0) {
        //$filterType 要过滤的类型
        //默认值为0 数据不做过滤
        //1 - 只返回药品类型为精神类的药品
        //2 - 返回药品类型为除精神类药品的其他药品
        $query = new Query();
        $query->from(['a' => RecipeRecord::tableName()]);
        $query->select(['DISTINCT(a.id)', 'a.recipe_id', 'a.name', 'a.product_name', 'a.unit', 'a.medicine_description_id', 'a.price', 'a.dosage_form', 'a.dose', 'a.used', 'a.frequency', 'a.day', 'a.num', 'a.description', 'a.type', 'a.status', 'a.drug_type', 'a.specification', 'a.skin_test_status', 'a.skin_test', 'a.dose_unit as r_dose_unit', 'a.curelist_id', 'a.high_risk', 'c.dose_unit as l_dose_unit', 'c.manufactor', 'a.remark', 'd.cure_result as cureResult', 'd.name as cureName', 'd.status as cureStatus', 'e.status as cureChargeStatus', 'a.package_record_id']);
        $query->leftJoin(['c' => RecipeList::tableName()], '{{a}}.recipe_id = {{c}}.id');
        $query->leftJoin(['d' => CureRecord::tableName()], '{{a}}.cure_id = {{d}}.id');
        $query->leftJoin(['e' => ChargeInfo::tableName()], '{{a}}.cure_id = {{e}}.outpatient_id AND {{e}}.type = 3');
        $query->where(['a.record_id' => $id, 'a.spot_id' => $this->spotId]);
        $query->andFilterWhere(['a.id' => $recipeIds]);

        if($filterType == 1){ //打印精二处方（只打印精神类药品）
            $query->andFilterWhere(['a.drug_type' => '20']);
        }
        if($filterType == 2){ //打印儿科处方（打印除精神类药品的其他药品）
            $query->andFilterWhere(['!=','a.drug_type','20']);
        }


        $result = $query->all();
        foreach ($result as &$v) {
            $v['displayName'] = empty($v['specification']) ? $v['name'] : $v['name'] . '(' . $v[specification] . ')';
        }
        return $result;
    }

    /**
     * @property 返回当前就诊记录已开的实验室检查记录列表以及对应的检验项目列表
     * @param 就诊流水id $id
     * @return \yii\db\ActiveRecord[]
     */
    protected function findInspectRecordDataProvider($id) {
        $inspectRecord = InspectRecord::find()->select(['id', 'name', 'unit', 'price', 'status', 'package_record_id'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->asArray()->all();
        if (!empty($inspectRecord)) {
            foreach ($inspectRecord as &$val) {
                $val['name'] = \yii\helpers\Html::encode($val['name']);
                $val['inspectItem'] = InspectRecordUnion::getInspectItem($val['id']);
            }
        }
        return $inspectRecord;
    }

    /**
     * @property 返回当前就诊记录已开的影像学检查的记录
     * @param 就诊流水id $id
     * @return \yii\db\ActiveRecord[]
     */
    protected function findCheckRecordDataProvider($id) {

        return CheckRecord::find()->select(['id', 'name', 'unit', 'price', 'status', 'package_record_id'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->asArray()->all();
    }

    /**
     * @property 返回当前就诊记录已开的影像学检查的记录
     * @param 就诊流水id $id
     * @return \yii\data\ActiveDataProvider 返回当前就诊记录已开的影像学检查的记录
     */
    protected function findCheckRecordProvider($id) {

        $query = new ActiveQuery(CheckRecord::className());
        $query->from(CheckRecord::tableName());
        $query->select(['id', 'name', 'unit', 'price', 'status', 'package_record_id']);
        $query->where(['record_id' => $id, 'spot_id' => $this->spotId]);

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
     * @property 返回当前就诊记录已开的治疗项目的记录
     * @param 就诊流水id $id
     * @return \yii\data\ActiveDataProvider 返回当前就诊记录已开的治疗项目的记录
     */
    protected function findCureRecordDataProvider($id) {
        $query = new ActiveQuery(CureRecord::className());
        $query->from(['a' => CureRecord::tableName()]);
        $query->select(['a.id', 'a.name', 'a.unit', 'a.price', 'a.time', 'a.description', 'a.status', 'a.remark', 'a.type', 'a.package_record_id']);
        $query->where(['a.record_id' => $id, 'a.spot_id' => $this->spotId]);
//        if (ChargeInfo::getChargeRecordNum($id)) {
//            $query->addSelect(['charge_status' => 'b.status']);
//            $query->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.outpatient_id');
//            $query->andWhere(['b.type' => ChargeInfo::$cureType]);
//        }
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
     * @property 获取就诊记录的初步诊断
     * @param int  $id 就诊流水id
     * @return Array 返回当前就诊记录已开的治疗项目的记录
     */
    protected function findFirstCheckDataProvider($id) {
        $model = FirstCheck::find()
                ->select(['id', 'check_code_id', 'content', 'check_degree'])
                ->where(['record_id' => $id, 'spot_id' => $this->spotId])
                ->orderBy(['id' => SORT_ASC])
                ->all();
        if ($model !== null && !empty($model)) {
            return $model;
        } else {
            $model = new FirstCheck();
            $model->record_id = $id;
            $model->check_degree = 1;
            $model->check_code_type = 1;
            return [$model];
        }
    }

    /**
     * @return 返回库存总量
     * @param 就诊流水id $id
     */
    private function getRecipeTotal($where = '1 != 0') {
        $rows = [];
        $query = new Query();
        $query->from(['a' => Stock::tableName()]);
        $query->select(['b.recipe_id', 'b.num']);
        $query->leftJoin(['b' => StockInfo::tableName()], '{{a}}.id = {{b}}.stock_id');
        $query->where(['a.spot_id' => $this->spotId, 'a.status' => 1]);
        $query->andWhere('b.expire_time >= :expire_time', [':expire_time' => strtotime(date('Y-m-d'))]);
        $query->andWhere($where);
        $result = $query->all();
        foreach ($result as $v) {
            $rows[$v['recipe_id']][] = $v['num'];
        }
        return $rows;
    }

    /**
     * Deletes an existing Outpatient model.
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
     * Finds the Outpatient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Outpatient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        $model = TriageInfo::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);

        if (($model != null)) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     *
     * @author JeanneWu
     * @time 2017年2月16日 10:33
     * @param 就诊流水id $id
     * @return \app\modules\triage\models\TriageInfoRelation 返回 门诊病历关联的记录信息
     */
    protected function findTriageInfoRelation($id) {
        $model = TriageInfoRelation::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);

        if (($model != null)) {
            return $model;
        } else {
            $model = new TriageInfoRelation();
            $model->record_id = $id;
            return $model;
        }
    }

    /**
     *
     * @author zhenyuzhang
     * @time 2017年3月22日 
     * @param int $id 就诊流水id
     * @return \app\modules\outpatient\models\outpatientRelation 返回 门诊病历关联的记录信息
     */
    protected function findOutpatientRelation($id) {
        $model = OutpatientRelation::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if (($model != null)) {
            return $model;
        } else {
            $model = new OutpatientRelation();
            $model->record_id = $id;
            return $model;
        }
    }

    /**
     * 
     * @param 就诊流水id $id
     * @return \app\modules\outpatient\models\ChildExaminationBasic
     * @return 返回儿童体检-基本信息的 model信息，若有记录，则修改，没有则新增
     */
    protected function findChildExaminationBasic($id) {
        $model = ChildExaminationBasic::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if (($model != null)) {
            return $model;
        } else {
            $model = new ChildExaminationBasic();
            $model->record_id = $id;
            return $model;
        }
    }

    /**
     * 
     * @param 就诊流水id $id
     * @return \app\modules\outpatient\models\ChildExaminationBasic
     * @return 返回儿童体检-基本信息的 model信息，若有记录，则修改，没有则新增
     */
    protected function findChildExaminationInfo($id) {
        $model = ChildExaminationInfo::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if (($model != null)) {
            return $model;
        } else {
            $model = new ChildExaminationInfo();
            $model->record_id = $id;
            return $model;
        }
    }

    /**
     *
     * @param 就诊流水id $id
     * @return \app\modules\outpatient\models\ChildExaminationBasic
     * @return 返回儿童体检-生长评估 的model信息，若有记录，则修改，没有则新增
     */
    protected function findChildExaminationGrowth($id) {
        $model = ChildExaminationGrowth::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if (($model != null)) {
            return $model;
        } else {
            $model = new ChildExaminationGrowth();
            $model->result = 0;
            $model->record_id = $id;
            return $model;
        }
    }

    /**
     *
     * @param 就诊流水id $id
     * @return \app\modules\outpatient\models\ChildExaminationBasic
     * @return 返回儿童体检-基本信息的 model信息，若有记录，则修改，没有则新增
     */
    protected function findChildExaminationCheck($id) {
        $model = ChildExaminationCheck::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if (($model != null)) {
            return $model;
        } else {
            $model = new ChildExaminationCheck();
            $model->record_id = $id;
            $model->appearance = 0;
            $model->skin = 0;
            $model->headFace = 0;
            $model->eye = 0;
            $model->ear = 0;
            $model->nose = 0;
            $model->throat = 0;
            $model->tooth = 0;
            $model->chest = 0;
            $model->bellows = 0;
            $model->cardiovascular = 0;
            $model->belly = 0;
            $model->genitals = 0;
            $model->back = 0;
            $model->limb = 0;
            $model->nerve = 0;

            return $model;
        }
    }

    /**
     *
     * @param 就诊流水id $id
     * @return \app\modules\outpatient\models\ChildExaminationBasic
     * @return 返回儿童体检-基本信息的 model信息，若有记录，则修改，没有则新增
     */
    protected function findChildExaminationAssessment($id) {
        $model = ChildExaminationAssessment::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if (($model != null)) {
            return $model;
        } else {

            $model = new ChildExaminationAssessment();
            $model->record_id = $id;
            $model->communicate = 0;
            $model->coarse_action = 0;
            $model->fine_action = 0;
            $model->solve_problem = 0;
            $model->personal_society = 0;
            $model->summary = 0;
            $model->evaluation_result = 0;
            return $model;
        }
    }

//    private function saveCureRecord($cureListId,$recordId) {
//        $cureInfo = CureList::findOne(['id' => $cureListId, 'spot_id' => $this->spotId]);
//        $cureRecord = new CureRecord();
//        $cureRecord->record_id = array($recordId);
//        $cureRecord->spot_id = array($this->spotId);
//        $cureRecord->name = array($cureInfo->name);
//        $cureRecord->unit = array($cureInfo->unit);
//        $cureRecord->price = array($cureInfo->price);
//        $cureRecord->time = array(1);
//        $cureRecord->tag_id = array($cureInfo->tag_id);
//        $cureRecord->type = array(1);
//        return $cureRecord->id;
//    }

    /*
     * 判断治疗医嘱状态
     * 待治疗true 其他false
     */
    private function getCureRecordStatus($cureId) {
        $result = CureRecord::find()->select(['status'])->where(['id' => $cureId, 'spot_id' => $this->spotId])->asArray()->one();
        return $result['status'] == 3; //未执行
    }

    /*
     * 判断医嘱收费状态
     * 未收费true 其他false
     */

    private function getChargeInfoStatus($outpatientId, $type) {
        $result = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $outpatientId, 'type' => $type])->asArray()->one();
        if (!$result) {
            return true;
        } else {
            return $result['status'] == 0;
        }
    }




    private function getRecipeTemplateMenu() {
        $query = new Query();
        $query->from(['a' => RecipeTemplate::tableName()]);
        $query->leftJoin(['b' => RecipeTypeTemplate::tableName()], '{{a}}.recipe_type_template_id = {{b}}.id');
        $query->select(['a.id', 'a.name', 'a.recipe_type_template_id', 'a.type', 'recipe_type_template_name' => 'IFNULL(b.name,"未分类")', 'type_spot_id' => 'b.spot_id']);
        $query->where(['a.spot_id' => $this->spotId, 'a.user_id' => $this->userInfo->id]);
        $query->orderBy(['a.type' => SORT_DESC, 'a.recipe_type_template_id' => SORT_ASC, 'a.id' => SORT_DESC]);
        $result = $query->all();
        return $result;
    }

    private function getRecordType($id) {

        $result = Report::find()->select(['id', 'record_type', 'type_description'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->asArray()->one();
        return $result;
    }

    /**
     * 
     * @param 就诊流水id $id
     * @return \app\modules\outpatient\models\DentalHistory
     */
    protected function findDentalHistory($id) {
        $model = DentalHistory::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if (($model != null)) {
            return $model;
        } else {
            $model = new DentalHistory();
            $model->record_id = $id;
            $model->spot_id = $this->spotId;
            return $model;
        }
    }

    /**
     * 
     * @param 就诊流水id $id
     * @return \app\modules\outpatient\models\DentalHistory
     */
    protected function findOrthodonticsReturnbisit($id) {
        $model = OrthodonticsReturnvisitRecord::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if (($model != null)) {
            return $model;
        } else {
            $model = new OrthodonticsReturnvisitRecord();
            $model->record_id = $id;
            $model->check = '曲面断层片分析：
投影测量分析：
CBCT分析：';
            return $model;
        }
    }

    /**
     * @return 返回正畸初诊病历model
     * @param integer $id 就诊流水ID
     */
    protected function findOrthodonticsFirst($id) {

        //口腔正畸初诊病历
        $firstRecordModel = OrthodonticsFirstRecord::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if ($firstRecordModel == null) {
            $firstRecordModel = new OrthodonticsFirstRecord();
            $firstRecordModel->record_id = $id;
            $firstRecordModel->hasAllergy = 1;
        } else {
            $firstRecordModel->bad_habits_abnormal = explode(',', $firstRecordModel->bad_habits_abnormal);
            $firstRecordModel->feed = explode(',', $firstRecordModel->feed);
            $firstRecordModel->oral_function_abnormal = explode(',', $firstRecordModel->oral_function_abnormal);
            $firstRecordModel->left_temporomandibular_joint_abnormal = explode(',', $firstRecordModel->left_temporomandibular_joint_abnormal);
            $firstRecordModel->right_temporomandibular_joint_abnormal = explode(',', $firstRecordModel->right_temporomandibular_joint_abnormal);
        }
        //口腔正畸初诊病历关联口腔组织检查
        $firstRecordExaminationModel = OrthodonticsFirstRecordExamination::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if ($firstRecordExaminationModel == null) {
            $firstRecordExaminationModel = new OrthodonticsFirstRecordExamination();
            $firstRecordExaminationModel->record_id = $id;
        } else {
            $firstRecordExaminationModel->overbite_anterior_teeth_abnormal = explode(',', $firstRecordExaminationModel->overbite_anterior_teeth_abnormal);
            $firstRecordExaminationModel->overbite_posterior_teeth_abnormal = explode(',', $firstRecordExaminationModel->overbite_posterior_teeth_abnormal);
            $firstRecordExaminationModel->cover_anterior_teeth_abnormal = explode(',', $firstRecordExaminationModel->cover_anterior_teeth_abnormal);
            $firstRecordExaminationModel->cover_posterior_teeth_abnormal = explode(',', $firstRecordExaminationModel->cover_posterior_teeth_abnormal);
        }
        //全身状态与颜貌信息
        $firstRecordFeaturesModel = OrthodonticsFirstRecordFeatures::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if ($firstRecordFeaturesModel == null) {
            $firstRecordFeaturesModel = new OrthodonticsFirstRecordFeatures();
            $firstRecordFeaturesModel->record_id = $id;
        }
        $firstRecordModelCheckModel = OrthodonticsFirstRecordModelCheck::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);

        if ($firstRecordModelCheckModel == null) {
            $firstRecordModelCheckModel = new OrthodonticsFirstRecordModelCheck();
            $firstRecordModelCheckModel->examination = '
曲面断层片分析：
投影测量分析：
CBCT分析：';
            $firstRecordModelCheckModel->record_id = $id;
        }
        $firstRecordTeethCheckModel = OrthodonticsFirstRecordTeethCheck::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if ($firstRecordTeethCheckModel == null) {
            $firstRecordTeethCheckModel = new OrthodonticsFirstRecordTeethCheck();
            $firstRecordTeethCheckModel->record_id = $id;
        }
        $triageInfo = $this->findModel($id);
        $triageInfo->scenario = 'orthodonticsFirstRecord';
        $model = new MultiModel([
            'models' => [
                'triageInfo' => $triageInfo,
                'firstRecord' => $firstRecordModel,
                'firstRecordExamination' => $firstRecordExaminationModel,
                'firstRecordFeatures' => $firstRecordFeaturesModel,
                'firstRecordModelCheck' => $firstRecordModelCheckModel,
                'firstRecordTeethCheck' => $firstRecordTeethCheckModel
            ]
        ]);

        return $model;
    }

    /**
     * @param 就诊流水id $id
     * @return 
     */
    protected function findDentalHistoryRelation($id) {
        $result = array();
        $dentalHistoryRecord = DentalHistoryRelation::find()->select(['id', 'type', 'position', 'content','dental_disease'])->where(['record_id' => $id, 'spot_id' => $this->spotId])->asArray()->all();
        if (!empty($dentalHistoryRecord)) {
            foreach ($dentalHistoryRecord as $val) {
                $result[$val['type']][] = array(
                    'position' => $val['position'],
                    'content' => $val['content'],
                    'dental_disease' => $val['dental_disease'],
                );
            }
        }
        return $result;
    }

    public function actionRecipeBack($id) {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $model = new RecipeRecord();
            if ($request->isGet) {
                $recipeBackDataProvider = $this->findRecipeBackData($id);
                return [
                    'title' => "请选择退药药品及退药数量",
                    'content' => $this->renderAjax('@outpatientOutpatientRecipeBackView', [
                        'model' => $model,
                        'dataProvider' => $recipeBackDataProvider,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn btn-default btn-form btn-recipe-back', 'type' => "submit"])
                ];
            } else {
                $recipeId = Yii::$app->request->post('selection');
                if (!empty($recipeId)) {
                    RecipeRecord::updateAll(['status' => 4], ['id' => $recipeId, 'spot_id' => $this->spotId]);
                    return [
                        'forceClose' => true,
                        'forceType' => 1,
                        'forceMessage' => '操作成功',
                        'forceReload' => '#recipePjax'
                    ];
                } else {
                    return [
                        'forceClose' => true,
                        'forceType' => 2,
                        'forceMessage' => '请选择退药药品'
                    ];
                }
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    public function findRecipeBackData($id) {
        $query = new \yii\db\ActiveQuery(RecipeRecord::className());
        $query->from(['a' => RecipeRecord::tableName()]);
        $query->select(['a.id', 'a.name', 'a.dosage_form', 'a.dose', 'a.num', 'a.dose_unit', 'a.specification', 'a.high_risk']);
        $query->where(['a.record_id' => $id, 'a.status' => 1, 'a.spot_id' => $this->spotId, 'a.type' => 1, 'a.package_record_id' => 0]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
    }

    /*
     * @desc 保存初步诊断或体重
     * @param $id record_id
     * @param $type(1为实验室,影像学 2为治疗,处方)
     * return 
     */

    public function actionUpdatePatientInfo($id, $type) {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (2 == $type) {
            $triageInfoModel = TriageInfo::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
            $triageInfoModel->load($request->post());
            $triageInfoModel->scenario = 'updatePatientInfo';
        }
        if (Yii::$app->request->isAjax) {
            $reportResult = $this->getRecordType($id);
            if ($request->isGet) {
                $firstCheckDataProvider = $this->findFirstCheckDataProvider($id);
                return [
                    'title' => "系统提示",
                    'content' => $this->renderAjax('_patientInfoForm', [
                        'triageInfoModel' => $triageInfoModel,
                        'type' => $type,
                        'firstCheckDataProvider' => $firstCheckDataProvider,
                        'recordId' => $id,
                        'record_type' => $reportResult['record_type'],
                    ]),
//                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
//                    Html::button('保存', ['class' => 'btn btn-default btn-form btn-recipe-back', 'type' => "submit"])
                ];
            } else {
                $firstCheckData = $request->post('FirstCheck');
                if (!$firstCheckData) {
                    $error['firstCheck'] = true;
                }
                if ((2 == $type ? $triageInfoModel->validate() : true) && $firstCheckData) {//判断数据是否存在或验证规则是否通过
                    $dbTrans = Yii::$app->db->beginTransaction();
                    try {
                        $db = Yii::$app->db;
                        if (2 == $type) {
                            $triageInfoModel->save();
                        }
                        $db->createCommand()->delete(FirstCheck::tableName(), ['record_id' => $id, 'spot_id' => $this->spotId])->execute();
                        foreach ($firstCheckData['check_code_type'] as $key => $val) {
                            $row[] = [$this->spotId, $id, $firstCheckData['check_code_id'][$key], $firstCheckData['content'][$key], $firstCheckData['check_degree'][$key], time(), time()];
                        }
                        Yii::$app->db->createCommand()->batchInsert(FirstCheck::tableName(), ['spot_id', 'record_id', 'check_code_id', 'content', 'check_degree', 'create_time', 'update_time'], $row)->execute();
                        $dbTrans->commit();
                        return [
                            'forceClose' => true,
                            'forceType' => 1,
                            'forceMessage' => '操作成功',
                            'forceReloadPage' => true,
                            'errorCode' => 0
                        ];
                    } catch (Exception $e) {
                        Yii::error($e->errorInfo, 'outpatientUpdatePatientInfo');
                        $dbTrans->rollBack();
                        return [
                            'forceClose' => true,
                            'forceType' => 2,
                            'forceMessage' => '操作失败',
                            'forceReloadPage' => true,
                            'errorCode' => 1001
                        ];
                    }
                } else {
                    if ($firstCheckData) {
                        foreach ($firstCheckData['check_id'] as $value) {
                            $firstCheckDataProvider[] = json_decode($value, true);
                        }
                    } else {
                        $firstCheckDataProvider = [];
                    }
                    return [
                        'title' => "系统提示",
                        'content' => $this->renderAjax('_patientInfoForm', [
                            'triageInfoModel' => $triageInfoModel,
                            'type' => $type,
                            'firstCheckDataProvider' => $firstCheckDataProvider,
                            'errors' => $error,
                            'recordId' => $id,
                            'record_type' => $reportResult['record_type'],
                        ]),
//                        'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
//                        Html::button('保存', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                    ];
                }
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /*
     * @desc 选择套餐
     * @param $id record_id
     * return 
     */

    public function actionSelectPackage($id) {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isAjax) {
            $model = $this->findPackageRecord($id);
            if ($request->isGet) {
                $packageChargeStatus = $this->checkPackageChargeStatus($model); //判断诊疗费或套餐是否已收费
                $doctorAdviceStatus = $this->checkDoctorAdviceStatus($model); //判断四大医嘱是否已执行
                $packageList = OutpatientPackageTemplate::getPackageList(['id', 'name']);
                $packageDetail = OutpatientPackageTemplate::getPackageDetail(array_column($packageList, 'id'));
                $disabled = $packageChargeStatus || $doctorAdviceStatus;
                return [
                    'title' => "选择医嘱模板/套餐",
                    'content' => $this->renderAjax('selectPackageForm', [
                        'model' => $model,
                        'packageList' => $packageList,
                        'packageDetail' => $packageDetail,
                        'disabled' => $disabled,
                    ]),
                    'footer' => Html::button('保存', ['class' => 'btn btn-default btn-form btn-disalbed-custom', 'type' => "submit", 'disabled' => $disabled])
                ];
            } else {
                $model->load($request->post());
                $ret = $this->checkDataStatus($model); //验证是否可以使用套餐
                if ($ret['errorCode']) {
                    return [
                        'forceType' => 2,
                        'forceMessage' => $ret['errorMessage'],
                        'forceClose' => true,
                    ];
                }
                $dbTrans = Yii::$app->db->beginTransaction();
                $db = Yii::$app->db;
                try {
                    if ($model->template_id) {//template_id不为空，为新增或修改医嘱套餐
                        $packageInfo = OutpatientPackageTemplate::getPackageInfo(['a.id' => $model->template_id])[0];
                        $patientRecordModel = PatientRecord::findOne(['id' => $id, 'spot_id' => $this->spotId]);
                        $patientRecordModel->is_package = 1;
                        $patientRecordModel->price = $packageInfo['medical_fee_price'];
                        $patientRecordModel->save(); //修改诊疗费


                        $model->price = $packageInfo['packagePrice'];
                        $model->name = $packageInfo['packageName'];

                        if ($model->isNewRecord) { //新记录
                            $model->save();
                            $this->saveDoctorAdvice($model->template_id, $model->record_id, $model->id);
                        } else {//修改套餐，同一个套餐也算修改
                            $model->save();
                            $this->deleteDoctorAdvice($model->record_id, $model->id);
                            $this->saveDoctorAdvice($model->template_id, $model->record_id, $model->id);
                        }
                        if ($patientRecordModel->status == 5) { //已结束就诊，插入收费项
                            $chargeRecordId = $this->findChargeRecord($model->record_id);
                            $reportInfo = \app\modules\report\models\Report::reportRecord($model->record_id);
                            //先删除旧收费项（不一定有）
                            $db->createCommand()->delete(ChargeInfo::tableName(), ['spot_id' => $this->spotId, 'outpatient_id' => $model->id, 'record_id' => $id, 'type' => ChargeInfo::$packgeType])->execute();
                            $db->createCommand()->delete(ChargeInfo::tableName(), ['spot_id' => $this->spotId, 'record_id' => $id, 'type' => ChargeInfo::$priceType])->execute(); //删除存在的诊疗费
                            //插入新收费项
                            $db->createCommand()->insert(ChargeInfo::tableName(), [
                                'charge_record_id' => $chargeRecordId,
                                'spot_id' => $this->spotId,
                                'record_id' => $id,
                                'type' => ChargeInfo::$packgeType,
                                'outpatient_id' => $model->id,
                                'name' => $packageInfo['packageName'],
                                'unit_price' => $packageInfo['packagePrice'],
                                'num' => 1,
                                'doctor_id' => $reportInfo['doctor_id'],
                                'create_time' => time(),
                                'update_time' => time(),
                            ])->execute();
                        }
                    } else if (!$model->isNewRecord) { //template_id为空，且存在记录，则删除医嘱套餐
                        $this->deleteDoctorAdvice($model->record_id, $model->id);
                        $patientRecordModel = PatientRecord::findOne(['id' => $id, 'spot_id' => $this->spotId]);
                        $patientRecordModel->is_package = 2;

                        //保存诊金为分诊时的诊金
                        $patientRecordModel->price = $patientRecordModel->record_price;
                        $patientRecordModel->save(); //修改诊疗费

                        if ($patientRecordModel->status == 5) {//已结束就诊
                            //根据分诊后的诊金生成诊疗费
                            $chargeRecordId = $this->findChargeRecord($model->record_id);
                            $reportInfo = \app\modules\report\models\Report::reportRecord($model->record_id);
                            $rows[] = [$chargeRecordId, $this->spotId, $id, ChargeInfo::$priceType, 0, '诊疗费', '', '次', $patientRecordModel->record_price, 1, $reportInfo['doctor_id'], time(), time(), 0];
                            Yii::$app->db->createCommand()->batchInsert(ChargeInfo::tableName(), ['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'name', 'specification', 'unit', 'unit_price', 'num', 'doctor_id', 'create_time', 'update_time', 'tag_id'], $rows)->execute();
                            //删除套餐的收费项
                            $db->createCommand()->delete(ChargeInfo::tableName(), ['spot_id' => $this->spotId, 'outpatient_id' => $model->id, 'record_id' => $id, 'type' => ChargeInfo::$packgeType])->execute();
                        }
                        $model->delete(); //删除套餐
                    }
                    $dbTrans->commit();
                    return [
                        'forceClose' => true,
                        'forceMessage' => '保存成功',
                        'forceReloadPage' => true,
                    ];
                } catch (Exception $e) {
                    Yii::error($e, 'outpatientSelectPackage');
                    $dbTrans->rollBack();
                    return [
                        'forceClose' => true,
                        'forceType' => 2,
                        'forceMessage' => '保存失败',
                        'forceReloadPage' => true,
                    ];
                }
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * 
     * @param 就诊流水id $id
     * @return \app\modules\outpatient\models\DentalHistory
     */
    protected function findPackageRecord($id) {
        $model = PackageRecord::findOne(['record_id' => $id, 'spot_id' => $this->spotId]);
        if (($model != null)) {
            return $model;
        } else {
            $model = new PackageRecord();
            $model->record_id = $id;
            $model->spot_id = $this->spotId;
            return $model;
        }
    }

    /*
     * 检查是否能够保存
     * @param $model PackgeRecord 
     * return boolean
     */

    protected function checkDataStatus($model) {

        $result = [
            'errorCode' => 0,
            'errorMessage' => '',
        ];

        if ($this->checkPackageChargeStatus($model)) {
            $result['errorCode'] = 1002;
            $result['errorMessage'] = "诊疗费或套餐已收费";
            return $result;
        }

        if ($this->checkDoctorAdviceStatus($model)) {//判断四大医嘱是否已执行
            $result['errorCode'] = 1002;
            $result['errorMessage'] = "医嘱已执行";
            return $result;
        }

        if ($model->template_id) {
            $usedNum = [];
            $recipeList = OutpatientPackageRecipe::getrecipeList($model->template_id); //获取处方医嘱项目
            foreach ($recipeList as $value) {
                if ($value['type'] == 1) {//内购
                    if (!isset($usedNum[$value['recipe_id']])) {
                        $usedNum[$value['recipe_id']]['num'] = $value['num'];
                        $usedNum[$value['recipe_id']]['name'] = $value['name'];
                    } else {
                        $usedNum[$value['recipe_id']]['num'] += $value['num'];
                    }
                }
            }
            $recipeIdArr = array_column($recipeList, 'recipe_id');
            $recipeUsedTotalNumsList = RecipeRecord::find()->select(['id', 'recipe_id', 'num', 'name'])->where(['spot_id' => $this->spotId, 'type' => 1, 'recipe_id' => $recipeIdArr, 'status' => 3])->asArray()->all(); //其他记录
            foreach ($recipeUsedTotalNumsList as $value) {
                if (isset($usedNum[$value['recipe_id']])) {
                    $usedNum[$value['recipe_id']]['num'] += $value['num'];
                }
            }
            $totalNum = $this->getRecipeTotal(['b.recipe_id' => $recipeIdArr]);
            foreach ($usedNum as $key => $value) {
                if (!isset($totalNum[$key]) || $usedNum[$key]['num'] > array_sum($totalNum[$key])) {
                    $result['errorCode'] = 1002;
                    $result['errorMessage'] = "[{$usedNum[$key]['name']}]库存不足";
                    return $result;
                }
            }

            $feeInfo = OutpatientPackageTemplate::getPackageInfo(['a.id' => $model->template_id])[0];
            if ($feeInfo['medical_fee_price'] == null) {//套餐诊金为null
                $result['errorCode'] = 1002;
                $result['errorMessage'] = "请再次确认套餐内容";
                return $result;
            }
        }

        if(!$this->checkInspectConnect($model)){
            $result['errorCode']=1002;
            $result['errorMessage']='实验室检查没有关联检验项目';
            return $result;
        }
        return $result;
    }

    /**
     * @param $model PackgeRecord
     * return boolean
     */
    protected function checkInspectConnect($model){
        $inspectFlag=1;
        $inspectList=OutpatientPackageInspect::getInspectList($model->template_id);
        foreach($inspectList as $key=>$value){
            if(!$inspectList[$key]['inspectItem']){
                    $inspectFlag=0;
                break;
            }
        }
        if($inspectFlag){
            return true;
        }else{
            return false;
        }
    }

    /*
     * 检查四大医嘱
     * @param $model PackgeRecord 
     * return boolean
     */

    protected function checkDoctorAdviceStatus($model) {
        $count = InspectRecord::find()->where(['record_id' => $model->record_id, 'package_record_id' => $model->id, 'spot_id' => $this->spotId, 'status' => [1, 2]])->count();
        $count == 0 && $count = CheckRecord::find()->where(['record_id' => $model->record_id, 'package_record_id' => $model->id, 'spot_id' => $this->spotId, 'status' => [1, 2]])->count();
        $count == 0 && $count = CureRecord::find()->where(['record_id' => $model->record_id, 'package_record_id' => $model->id, 'spot_id' => $this->spotId, 'status' => [1, 2]])->count();
        $count == 0 && $count = RecipeRecord::find()->where(['record_id' => $model->record_id, 'package_record_id' => $model->id, 'spot_id' => $this->spotId, 'status' => [1, 2]])->count();
        return (boolean) $count;
    }

    /*
     * 检查收费
     * @param $model PackgeRecord 
     * return boolean
     */

    protected function checkPackageChargeStatus($model) {
        $packageChargeInfo = ChargeInfo::find()->select(['status', 'type'])->where(['spot_id' => $this->spotId, 'record_id' => $model->record_id, 'type' => [ChargeInfo::$packgeType, ChargeInfo::$priceType]])->asArray()->indexBy('type')->all();
        if (isset($packageChargeInfo[ChargeInfo::$priceType]) && $packageChargeInfo[ChargeInfo::$priceType]['status'] != 0) {//存在诊疗费且已收费
            return true;
        } else if (isset($packageChargeInfo[ChargeInfo::$packgeType]) && $packageChargeInfo[ChargeInfo::$packgeType]['status'] != 0) {//存在套餐且已收费
            return true;
        }
        return false;
    }

    /**
     * @param $recordId 流水id
     * 删除套餐四大医嘱
     */
    protected function deleteDoctorAdvice($recordId, $package_record_id) {
        $db = Yii::$app->db;
        $where = ['spot_id' => $this->spotId, 'record_id' => $recordId, 'package_record_id' => $package_record_id];
        $inspectDelNum = InspectRecord::find()->select(['id'])->where($where)->count();
        $checkDelNum = CheckRecord::find()->select(['id'])->where($where)->count();
        $db->createCommand()->delete(InspectRecord::tableName(), $where)->execute();
        $db->createCommand()->delete(CheckRecord::tableName(), $where)->execute();
        $db->createCommand()->delete(CureRecord::tableName(), $where)->execute();
        $db->createCommand()->delete(RecipeRecord::tableName(), $where)->execute();
        $delNum = $inspectDelNum + $checkDelNum;
        if ($delNum) {
            //如果有删除 则删除待出报告的数量
            Outpatient::setPendingReport($this->spotId, $recordId, $delNum, 2);
        }
    }

    /**
     * @param $templateId 套餐id
     * @param $recordId 流水id
     * 保存四大医嘱
     */
    protected function saveDoctorAdvice($templateId, $recordId, $package_record_id) {
        $db = Yii::$app->db;

        $inspectList = OutpatientPackageInspect::getInspectList($templateId); //获取检验医嘱项目
        $row = [];
        $addNum = 0;
        if (count($inspectList) > 0) {
            foreach ($inspectList as $value) {
                $itemList = [];
                $db->createCommand()->insert(InspectRecord::tableName(), [
                    'record_id' => $recordId,
                    'spot_id' => $this->spotId,
                    'name' => $value['name'],
                    'unit' => $value['unit'],
                    'price' => $value['price'],
                    'create_time' => time(),
                    'update_time' => time(),
                    'inspect_id' => $value['inspect_id'],
                    'tag_id' => $value['tag_id'],
                    'deliver' => $value['deliver'],
                    'deliver_organization' => $value['deliver_organization'],
                    'specimen_type' => $value['specimen_type'],
                    'cuvette' => $value['cuvette'],
                    'inspect_type' => $value['inspect_type'],
                    'inspect_english_name' => $value['inspect_english_name'],
                    'remark' => $value['remark'],
                    'description' => $value['description'],
                    'package_record_id' => $package_record_id,
                ])->execute();
                $addNum++;
                $inspectRecordId = $db->lastInsertID;
                $inspectItemList = InspectItemUnionClinic::getInspectItemClinic($value['clinic_inspect_id']);
                if (isset($inspectItemList[$value['clinic_inspect_id']]) && !empty($inspectItemList[$value['clinic_inspect_id']])) {
                    //保存检验医嘱配置关联的项目的列表
                    foreach ($inspectItemList[$value['clinic_inspect_id']] as $v) {
                        $itemList[] = [$inspectRecordId, $v['id'], $this->spotId, $recordId, $v['item_name'], $v['english_name'], $v['unit'], '', time(), time()];
                    }
                    $db->createCommand()->batchInsert(InspectRecordUnion::tableName(), ['inspect_record_id', 'item_id', 'spot_id', 'record_id', 'name', 'english_name', 'unit', 'reference', 'create_time', 'update_time'], $itemList)->execute();
                }
            }
        }
        $checkList = OutpatientPackageCheck::getCheckList($templateId); //获取检查医嘱项目
        $row = [];
        if (count($checkList) > 0) {
            foreach ($checkList as $value) {
                $row[] = [
                    'record_id' => $recordId,
                    'spot_id' => $this->spotId,
                    'name' => $value['name'],
                    'unit' => $value['unit'],
                    'price' => $value['price'],
                    'create_time' => time(),
                    'update_time' => time(),
                    'tag_id' => $value['tag_id'],
                    'package_record_id' => $package_record_id,
                ];
                $addNum++;
            }
        }
        $db->createCommand()->batchInsert(CheckRecord::tableName(), ['record_id', 'spot_id', 'name', 'unit', 'price', 'create_time', 'update_time', 'tag_id', 'package_record_id'], $row)->execute();
        if ($addNum) {
            //设置 待出报告的数量 
            Yii::info('saveDoctorAdvice setPendingReport add Num' . $addNum);
            Outpatient::setPendingReport($this->spotId, $recordId, $addNum);
        }
        $cureList = OutpatientPackageCure::getCureList($templateId); //获取治疗医嘱项目

        $recipeList = OutpatientPackageRecipe::getrecipeList($templateId); //获取处方医嘱项目
        $row = [];
        $cureUsedSave = [];
        if (count($recipeList) > 0) {
            foreach ($recipeList as $value) {
                $cureRecordId = 0;
                $curelistId = 0;
                if ($value['outpatient_package_cure_id'] && isset($cureList[$value['outpatient_package_cure_id']])) {
                    $cureUsedSave[] = $value['outpatient_package_cure_id'];
                    $db->createCommand()->insert(CureRecord::tableName(), [
                        'record_id' => $recordId,
                        'spot_id' => $this->spotId,
                        'name' => $cureList[$value['outpatient_package_cure_id']]['name'],
                        'unit' => $cureList[$value['outpatient_package_cure_id']]['name'],
                        'price' => $cureList[$value['outpatient_package_cure_id']]['price'],
                        'time' => $cureList[$value['outpatient_package_cure_id']]['time'],
                        'description' => $cureList[$value['outpatient_package_cure_id']]['description'],
                        'type' => $cureList[$value['outpatient_package_cure_id']]['type'],
                        'create_time' => time(),
                        'update_time' => time(),
                        'tag_id' => $cureList[$value['outpatient_package_cure_id']]['tag_id'],
                        'package_record_id' => $package_record_id,
                    ])->execute(); //插入皮试记录
                    $cureRecordId = $db->lastInsertID;
                    $curelistId = $cureList[$value['outpatient_package_cure_id']]['curelistId'];
                }
                $row[] = [
                    'recipe_id' => $value['recipe_id'], 'record_id' => $recordId, 'spot_id' => $this->spotId, 'name' => $value['name'],
                    'product_name' => $value['product_name'], 'en_name' => $value['en_name'], 'specification' => $value['specification'],
                    'price' => $value['price'], 'dose' => $value['dose'], 'used' => $value['used'], 'day' => $value['day'],
                    'unit' => $value['unit'], 'dosage_form' => $value['dosage_form'], 'frequency' => $value['frequency'],
                    'num' => $value['num'], 'type' => $value['type'], 'description' => $value['description'], 'dose_unit' => $value['dose_unit'],
                    'medicine_description_id' => $value['medicine_description_id'], 'skin_test_status' => $value['skin_test_status'],
                    'skin_test' => $value['skin_test'], 'create_time' => time(), 'update_time' => time(), 'remark' => $value['remark'],
                    'tag_id' => $value['tag_id'], 'curelist_id' => $curelistId, 'cure_id' => $cureRecordId,
                    'high_risk' => $value['high_risk'], 'package_record_id' => $package_record_id,$value['drug_type']
                ];
            }
            $db->createCommand()->batchInsert(RecipeRecord::tableName(), ['recipe_id', 'record_id', 'spot_id', 'name', 'product_name', 'en_name',
                'specification', 'price', 'dose', 'used', 'day', 'unit', 'dosage_form',
                'frequency', 'num', 'type', 'description', 'dose_unit', 'medicine_description_id',
                'skin_test_status', 'skin_test', 'create_time', 'update_time', 'remark',
                'tag_id', 'curelist_id', 'cure_id', 'high_risk', 'package_record_id','drug_type'], $row)->execute();
            $db->createCommand()->update(PatientRecord::tableName(), ['pharmacy_record_time' => time()], ['id' => $recordId])->execute();
        }

        $row = [];
        if (count($cureList) > 0) {
            foreach ($cureList as $value) {
                if (!in_array($value['id'], $cureUsedSave)) {//过滤已经保存的治疗
                    $row[] = [
                        'record_id' => $recordId,
                        'spot_id' => $this->spotId,
                        'name' => $value['name'],
                        'unit' => $value['unit'],
                        'price' => $value['price'],
                        'time' => $value['time'],
                        'description' => $value['description'],
                        'create_time' => time(),
                        'update_time' => time(),
                        'tag_id' => $value['tag_id'],
                        'package_record_id' => $package_record_id,
                    ];
                }
            }
            if (count($row) > 0) {
                $db->createCommand()->batchInsert(CureRecord::tableName(), ['record_id', 'spot_id', 'name', 'unit', 'price', 'time', 'description', 'create_time', 'update_time', 'tag_id', 'package_record_id'], $row)->execute();
            }
        }
    }

    /**
     * @param $templateId 套餐id
     * @param $recordId 流水id
     * 根据套餐记录id获取套餐详情
     */
    protected function getPackageDetail($packageRecordId) {
        $data = [];
        $inspectList = InspectRecord::find()->select(['name'])->where(['spot_id' => $this->spotId, 'package_record_id' => $packageRecordId])->asArray()->all();
        foreach ($inspectList as $value) {
            $data['inspectList'][] = Html::encode("{$value['name']}");
        }

        $checkList = CheckRecord::find()->select(['name'])->where(['spot_id' => $this->spotId, 'package_record_id' => $packageRecordId])->asArray()->all();
        foreach ($checkList as $value) {
            $data['checkList'][] = Html::encode("{$value['name']}");
        }

        $cureList = CureRecord::find()->select(['name', 'time', 'unit'])->where(['spot_id' => $this->spotId, 'package_record_id' => $packageRecordId])->asArray()->all();
        foreach ($cureList as $value) {
            $data['cureList'][] = Html::encode("{$value['name']}{$value['time']}" . ($value['unit'] ? "{$value['unit']}" : ''));
        }

        $recipeList = RecipeRecord::find()->select(['name', 'specification', 'num', 'unit'])->where(['spot_id' => $this->spotId, 'package_record_id' => $packageRecordId])->asArray()->all();
        foreach ($recipeList as $value) {
            $unit = RecipeList::$getUnit[$value['unit']];
            $data['recipeList'][] = Html::encode("{$value['name']}（{$value['specification']}）{$value['num']}{$unit}");
        }
        return $data;
    }

    /**
     * @param $id 流水id
     * @return array
     * @desc 实验室检查取消
     */
    public function actionInspectBack($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $model = new InspectRecord();
        $model->record_id = $id;
        $model->scenario = 'inspectBack';
        if ($model->load($request->post()) && $model->validate()) {
            InspectRecord::updateAll(['status' => 4], ['id' => $model->backInspectId, 'record_id' => $id, 'spot_id' => $this->spotId]);
            return [
                'forceClose' => true,
                'forceType' => 1,
                'forceMessage' => '操作成功',
                'forceReload' => '#inspectPjax'
            ];
        } else {
            $inspectRecordList = InspectRecord::getInspectList($id, ['id', 'name'], ['status' => [1, 2]]);
            return [
                'title' => "请选择需要取消的医嘱",
                'content' => $this->renderAjax('@outpatientOutpatientInspectBackView', [
                    'model' => $model,
                    'inspectRecordList' => $inspectRecordList,
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                Html::button('确定', ['class' => 'btn btn-default btn-form btn-recipe-back ', 'type' => "submit"])
            ];
        }
    }
    
    /**
     * 
     * @param integer $id 就诊记录
     * @return \app\modules\charge\models\ChargeInfo
     */
    public function findChargeInfoModel($id,$doctorId){
        $chargeInfoModel = ChargeInfo::findOne(['spot_id' => $this->spotId, 'record_id' => $id, 'type' => ChargeInfo::$priceType]);
        if($chargeInfoModel == null){
            $chargeRecordInfo = ChargeRecord::find()->select(['id'])->where(['spot_id' => $this->spotId, 'record_id' => $id,'status' => 2])->asArray()->one();
            $chargeInfoModel = new ChargeInfo();
            $chargeInfoModel->spot_id = $this->spotId;
            $chargeInfoModel->record_id = $id;
            $chargeInfoModel->type = ChargeInfo::$priceType;
            $chargeInfoModel->outpatient_id = 0;
            $chargeInfoModel->name = '诊疗费';
            $chargeInfoModel->specification = '';
            $chargeInfoModel->unit = '次';
            $chargeInfoModel->num = 1;
            $chargeInfoModel->doctor_id = $doctorId;
            $chargeInfoModel->tag_id = 0;
            $chargeInfoModel->charge_record_id = $chargeRecordInfo['id'];
        }
        return $chargeInfoModel;
        
    }


}
