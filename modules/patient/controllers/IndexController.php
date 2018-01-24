<?php

namespace app\modules\patient\controllers;

use app\common\base\BaseController;
use app\common\base\MultiModel;
use app\common\Percentage;
use app\modules\check\models\Check;
use app\modules\follow\models\search\FollowSearch;
use app\modules\make_appointment\models\Appointment;
use app\modules\outpatient\models\AllergyOutpatient;
use app\modules\outpatient\models\CheckRecord;
use app\modules\outpatient\models\ChildExaminationAssessment;
use app\modules\outpatient\models\ChildExaminationBasic;
use app\modules\outpatient\models\ChildExaminationCheck;
use app\modules\outpatient\models\ChildExaminationGrowth;
use app\modules\outpatient\models\ChildExaminationInfo;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\DentalHistory;
use app\modules\outpatient\models\FirstCheck;
use app\modules\outpatient\models\InspectRecord;
use app\modules\outpatient\models\MedicalFile;
use app\modules\outpatient\models\OrthodonticsFirstRecord;
use app\modules\outpatient\models\OrthodonticsFirstRecordExamination;
use app\modules\outpatient\models\OrthodonticsFirstRecordFeatures;
use app\modules\outpatient\models\OrthodonticsFirstRecordModelCheck;
use app\modules\outpatient\models\OrthodonticsFirstRecordTeethCheck;
use app\modules\outpatient\models\OrthodonticsReturnvisitRecord;
use app\modules\outpatient\models\OutpatientRelation;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\outpatient\models\Report;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientAllergy;
use app\modules\patient\models\PatientFamily;
use app\modules\patient\models\PatientRecord;
use app\modules\patient\models\PatientSubmeter;
use app\modules\patient\models\search\PatientSearch;
use app\modules\patient\models\UmpRecord;
use app\modules\spot\models\OrganizationType;
use app\modules\spot\models\Spot;
use app\modules\spot_set\models\Room;
use app\modules\spot_set\models\SpotType;
use app\modules\triage\models\ChildAssessment;
use app\modules\triage\models\HealthEducation;
use app\modules\triage\models\NursingRecord;
use app\modules\triage\models\TriageInfo;
use app\modules\triage\models\TriageInfoRelation;
use app\modules\user\models\User;
use app\specialModules\recharge\models\CardHistory;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * PatientController implements the CRUD actions for Patient model.
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
     * Lists all Patient models.
     * @return mixed
     */
    public function actionIndex() {
        $query = new Query();
        $params = Yii::$app->request->queryParams;
        $searchModel = new PatientSearch();
        $nurseRecordData = []; //护理记录
        $healthEducationData = []; //健康教育
        $inspectData = []; //实验室检查
        $checkData = []; //影像学检查
        $cureData = []; //治疗
        $recipeData = []; // 处方
        $checkReportData = []; // 处方
        $inspectReportData = []; // 处方

        $searchModel->load($params);
        $query->from(['a' => PatientRecord::tableName()]);
        $query->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id');
        $query->leftJoin(['c' => Spot::tableName()], '{{a}}.spot_id = {{c}}.id');
        $query->leftJoin(['d' => TriageInfo::tableName()], '{{d}}.record_id = {{a}}.id');
        $query->leftJoin(['e' => Report::tableName()], '{{e}}.record_id = {{a}}.id');
        $query->leftJoin(['f' => User::tableName()], '{{d}}.doctor_id = {{f}}.id');
        $query->leftJoin(['l' => OutpatientRelation::tableName()], '{{l}}.record_id = {{a}}.id');
        $query->leftJoin(['m' => TriageInfoRelation::tableName()], '{{m}}.record_id = {{a}}.id');
        $query->leftJoin(['o' => MedicalFile::tableName()], '{{a}}.id = {{o}}.record_id');
        $query->leftJoin(['p' => ChildExaminationBasic::tableName()], '{{p}}.record_id = {{a}}.id');
        $query->leftJoin(['s' => ChildExaminationGrowth::tableName()], '{{s}}.record_id = {{a}}.id');
        $query->leftJoin(['t' => ChildExaminationCheck::tableName()], '{{t}}.record_id = {{a}}.id');
        $query->leftJoin(['u' => ChildExaminationAssessment::tableName()], '{{u}}.record_id = {{a}}.id');


        $query->leftJoin(['st' => SpotType::tableName()], '{{e}}.type = {{st}}.id');
        $query->leftJoin(['ot' => OrganizationType::tableName()], '{{st}}.organization_type_id = {{ot}}.id');
        $query->leftJoin(['j' => ChildExaminationInfo::tableName()], '{{a}}.id = {{j}}.record_id');

        $query->leftJoin(['i' => DentalHistory::tableName()], '{{a}}.id = {{i}}.record_id');

        $query->leftJoin(['k' => OrthodonticsReturnvisitRecord::tableName()], '{{a}}.id = {{k}}.record_id');
        $query->leftJoin(['t1' => OrthodonticsFirstRecord::tableName()], '{{a}}.id = {{t1}}.record_id');
        $query->leftJoin(['t2' => OrthodonticsFirstRecordExamination::tableName()], '{{a}}.id = {{t2}}.record_id');
        $query->leftJoin(['t3' => OrthodonticsFirstRecordFeatures::tableName()], '{{a}}.id = {{t3}}.record_id');
        $query->leftJoin(['t4' => OrthodonticsFirstRecordModelCheck::tableName()], '{{a}}.id = {{t4}}.record_id');
        $query->leftJoin(['t5' => OrthodonticsFirstRecordTeethCheck::tableName()], '{{a}}.id = {{t5}}.record_id');

        $query->select(
                [
                    'recordId' => 'a.id', 'a.case_id', 'a.patient_id',
                    'b.username', 'b.birthday', 'b.sex', 'b.head_img', 'l.personalhistory', 'l.genetichistory',
                    'c.spot_name',
                    'd.diagnosis_time', 'd.cure_idea',
                    'e.type_description', 'reportTime' => 'e.create_time', 'e.record_type',
                    'd.doctor_id', 'd.first_check', 'd.treatment_type', 'd.treatment', 'd.heightcm', 'd.weightkg', 'd.head_circumference', 'd.bloodtype', 'd.temperature', 'd.temperature_type',
                    'd.breathing', 'd.pulse', 'd.shrinkpressure', 'd.diastolic_pressure', 'd.oxygen_saturation', 'd.pain_score', 'd.fall_score', 'triage_remark' => 'd.remark',
                    'd.incidence_date', 'd.food_allergy', 'd.meditation_allergy', 'd.examination_check', 'd.blood_type_supplement',
                    'l.chiefcomplaint', 'l.historypresent', 'l.pasthistory', 'l.physical_examination', 'l.remark',
                    'm.pastdraghistory', 'm.followup',
                    'doctorName' => 'f.username',
                    'file_id' => 'group_concat(o.id)', 'file_url' => 'group_concat(o.file_url)', 'file_name' => 'group_concat(o.file_name)', 'size' => 'group_concat(o.size)',
                    'p.bregmatic', 'p.jaundice',
                    'growthResult' => 's.result', 'growthRemark' => 's.remark',
                    't.appearance', 't.appearance_remark', 't.skin', 't.skin_remark', 't.headFace', 't.headFace_remark', 't.eye', 't.eye_remark', 't.ear', 't.ear_remark',
                    't.nose', 't.nose_remark', 't.throat', 't.throat_remark', 't.tooth', 't.tooth_remark', 't.chest', 't.chest_remark', 't.bellows', 't.bellows_remark', 't.cardiovascular', 't.cardiovascular_remark', 't.belly', 't.belly_remark', 't.genitals',
                    't.genitals_remark', 't.back', 't.back_remark', 't.limb', 't.limb_remark', 't.nerve', 't.nerve_remark',
                    'u.communicate', 'u.coarse_action', 'u.fine_action', 'u.solve_problem', 'u.personal_society', 'u.score', 'u.evaluation_result', 'u.evaluation_type_result', 'u.evaluation_diagnosis', 'u.evaluation_guidance',
                    'dental_type' => 'i.type', 'dental_chiefcomplaint' => 'i.chiefcomplaint',
                    'dental_historypresent' => 'i.historypresent', 'dental_pasthistory' => 'i.pasthistory',
                    'i.advice', 'i.remarks', 'i.returnvisit',
                    'j.sleep', 'j.shit', 'j.pee', 'j.visula_check', 'j.hearing_check', 'j.feeding_patterns', 'j.feeding_num', 'j.substitutes', 'j.dietary_supplement',
                    'j.food_types', 'j.inspect_content',
                    //正畸复诊
                    'orthReturnvisit' => 'k.returnvisit', 'k.check', 'orthTreatment' => 'k.treatment',
                    //口腔正畸初诊病历
                    'orthChiefcomplaint' => 't1.chiefcomplaint', 't1.motivation', 'orthHistorypresent' => 't1.historypresent', 't1.all_past_history', 'orthPastdraghistory' => 't1.pastdraghistory', 'recordRetention' => 't1.retention', 't1.early_loss', 't1.bad_habits', 't1.bad_habits_abnormal', 't1.bad_habits_abnormal_other',
                    't1.traumahistory', 't1.feed', 't1.immediate', 't1.oral_function', 't1.oral_function_abnormal', 't1.mandibular_movement', 't1.mandibular_movement_abnormal', 't1.mouth_open', 't1.mouth_open_abnormal', 't1.left_temporomandibular_joint',
                    't1.left_temporomandibular_joint_abnormal', 't1.left_temporomandibular_joint_abnormal_other', 't1.right_temporomandibular_joint', 't1.right_temporomandibular_joint_abnormal', 't1.right_temporomandibular_joint_abnormal_other',
                    //口腔正畸初诊病历关联口腔组织检查
                    't2.hygiene', 't2.periodontal', 't2.ulcer', 't2.gums', 't2.tonsil', 't2.frenum', 't2.soft_palate', 't2.lip', 't2.tongue', 't2.dentition', 't2.arch_form', 't2.arch_coordination', 't2.overbite_anterior_teeth',
                    't2.overbite_anterior_teeth_abnormal', 't2.overbite_anterior_teeth_other', 't2.overbite_posterior_teeth', 't2.overbite_posterior_teeth_abnormal', 't2.overbite_posterior_teeth_other', 't2.cover_anterior_teeth', 't2.cover_anterior_teeth_abnormal', 't2.cover_posterior_teeth',
                    't2.cover_posterior_teeth_abnormal', 't2.left_canine', 't2.right_canine', 't2.left_molar', 't2.right_molar', 't2.midline_teeth', 't2.midline_teeth_value', 't2.midline', 't2.midline_value',
                    //口腔正畸初诊病历关联全身状态与颜貌信息表
                    't3.dental_age', 't3.bone_age', 't3.second_features', 't3.frontal_type', 't3.symmetry', 't3.abit', 't3.face', 't3.smile', 't3.smile_other', 't3.upper_lip', 't3.lower_lip',
                    't3.side', 't3.nasolabial_angle', 't3.chin_lip', 't3.mandibular_angle', 't3.upper_lip_position', 't3.lower_lip_position', 't3.chin_position',
                    //口腔正畸初诊病历关联模型检查t
                    't4.crowded_maxillary', 't4.crowded_mandible', 't4.canine_maxillary', 't4.canine_mandible', 't4.molar_maxillary', 't4.molar_mandible', 't4.spee_curve', 't4.transversal_curve', 't4.bolton_nterior_teeth', 't4.bolton_all_teeth', 't4.examination',
                    //口腔正畸初诊病历关联牙齿检查
                    't5.dental_caries', 't5.reverse', 't5.impacted', 't5.ectopic', 't5.defect', 't5.retention', 't5.repair_body', 't5.other', 't5.other_remark', 't5.orthodontic_target', 't5.cure', 't5.special_risk'
                ]
        );
        $query->where(['b.spot_id' => $this->parentSpotId, 'a.status' => 5]);
        $searchParams = $params['PatientSearch'];
        $query->andFilterWhere(['like', 'b.patient_number', trim($searchParams['patient_number'])])
                ->andFilterWhere(['like', 'b.username', trim($searchParams['username'])])
                ->andFilterWhere(['like', 'b.card', trim($searchParams['card'])])
                ->andFilterWhere(['b.sex' => $searchParams['sex']])
                ->andFilterWhere(['b.iphone' => trim($searchParams['iphone'])]);

        if ($searchParams['start_birthday']) {
            $query->andFilterCompare('b.birthday', strtotime($searchParams['start_birthday']), '>=');
        }
        if ($searchParams['end_birthday']) {
            $query->andFilterCompare('b.birthday', strtotime($searchParams['end_birthday']) + 86400, '<=');
        }

        $query->andFilterWhere(['like', 'a.case_id', trim($searchParams['record_id'])]);
        $query->andFilterWhere(['e.spot_id' => $searchParams['record_spot_id']]);
        $query->andFilterWhere(['e.doctor_id' => $searchParams['doctor_id']]);
        $query->andFilterWhere(['e.second_department_id' => $searchParams['second_department_id']]);
        $query->andFilterWhere(['ot.id' => $searchParams['type']]);


        if ($searchParams['record_start_time']) {
            $query->andFilterCompare('d.diagnosis_time', strtotime($searchParams['record_start_time']), '>=');
        }
        if ($searchParams['record_end_time']) {
            $query->andFilterCompare('d.diagnosis_time', strtotime($searchParams['record_end_time']) + 86400, '<=');
        }
        $query->groupBy('a.id');
        $query->orderBy('d.diagnosis_time desc,b.patient_number desc');

        // 得到文章的总数（但是还没有从数据库取数据）
        $count = $query->count();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        $listCount = array(
            'userListCount' => $dataProvider->totalCount,
            'recordListCount' => $count,
        );
        if ($params['type'] == 2) {
            // 使用总数来创建一个分页对象
            $pagination = new Pagination(['totalCount' => $count]);

            // 使用分页对象来填充 limit 子句并取得文章数据
            $patientRecord = $query->offset($pagination->offset)
                    ->limit($pagination->limit)
                    ->all();



            $recordIdArr = array_column($patientRecord, 'recordId');
            $nurseRecordData = NursingRecord::getNurseRecord($recordIdArr);
            $healthEducationData = HealthEducation::getHealthEducationRecord($recordIdArr);
            $inspectData = InspectRecord::getInspectRecord($recordIdArr, 4);
            $checkData = CheckRecord::getCheckRecord($recordIdArr);
            $cureData = CureRecord::getCureRecord($recordIdArr);
            $recipeData = RecipeRecord::getRecipeRecordDetail($recordIdArr);
            $checkReportData = Report::checkReportData($recordIdArr, 1, 2);
            $inspectReportData = Report::checkReportData($recordIdArr, 1, 1);
            $dentalHistoryData = DentalHistory::getDentalHistoryData($recordIdArr);
            $firstCheckData = FirstCheck::getPatientRecordFirstCheckInfo($recordIdArr);
            $allergyOutpatient = AllergyOutpatient::getAllergyByRecord($recordIdArr);
            $assessment = ChildAssessment::getAssessmentByRecord($recordIdArr);
        } else {
            $recordNum = PatientRecord::getRecordNum($dataProvider->keys);
            $patientIdArr = $dataProvider->getKeys();
            $makeUpData = PatientRecord::getMakeup($patientIdArr);
        }


        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'recordNum' => $recordNum,
                    'pagination' => $pagination,
                    'patientRecord' => $patientRecord,
                    'nurseRecordData' => $nurseRecordData,
                    'healthEducationData' => $healthEducationData,
                    'inspectData' => $inspectData,
                    'checkData' => $checkData,
                    'cureData' => $cureData,
                    'recipeData' => $recipeData,
                    'makeUpData' => $makeUpData,
                    'inspectReportData' => $inspectReportData,
                    'checkReportData' => $checkReportData,
                    'listCount' => $listCount,
                    'dentalHistoryData' => $dentalHistoryData,
                    'firstCheckData' => $firstCheckData,
                    'allergyOutpatient' => $allergyOutpatient,
                    'assessment' => $assessment
        ]);
    }

    /**
     * Displays a single Patient model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $patient_family_id = null) {
        $isAjax = Yii::$app->request->isAjax;
        $isPjax = Yii::$app->request->isPjax;
        $isGet = Yii::$app->request->isGet;
        $param = Yii::$app->request->post();
        $paramContent = Yii::$app->request->get();

        if ($patient_family_id != null) { //添加家庭成员
            if ($patient_family_id != 0 && $paramContent['type'] == 2) {
                //删除家庭成员
                return $this->deleteFamily($paramContent);
            } else {
                return $this->viewFamilyForm($paramContent, $id);
            }
        }
        $model = $this->findModel($id);
        $submeterModel = $this->findSubmeterModel($id);
        $submeterModel->childbirth_case = $submeterModel->childbirth_case ? explode(',', $submeterModel->childbirth_case) : '';
        $mutilModel = new MultiModel([
            'models' => [
                'patient' => $model,
                'patientSubmeter' => $submeterModel
            ]
        ]);
        $model->scenario = 'baseInformation';
        if ($isAjax && !$isPjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($mutilModel->load(Yii::$app->request->post()) && $mutilModel->save()) {//两个model同时提交数据（患者信息提交）
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->refresh();
            } else if (!$mutilModel->load(Yii::$app->request->post()) && $submeterModel->load(Yii::$app->request->post()) && $submeterModel->save()) {  // 儿童特有信息提交
                $this->result['errorCode'] = 0;
                $this->result['msg'] = '保存成功';
            } else {
                $this->result['errorCode'] = 1001;
                $errors = $model->errors;
                if (isset($errors['username'])) {
                    $this->result['msg'] = $errors['username'];
                } else if (isset($errors['birthTime'])) {
                    $this->result['msg'] = $errors['birthTime'];
                } elseif (isset($errors['hourMin']) && !empty($errors['hourMin'])) {
                    $this->result['msg'] = $errors['hourMin'];
                }
            }
            return $this->result;
        }

        $model->address = $model->province ? ($model->province . ($model->city ? '/' . $model->city : '') . ($model->area ? '/' . $model->area : '')) : '';
        $model->birthTime = $model->birthday != 0 ? date('Y-m-d', $model->birthday) : '';
        $model->hourMin = $model->birthday != 0 ? date('H:i', $model->birthday) : '';
        $familyData = [];
        if (!$isPjax) {
            //获取患者最新一条已分诊就诊记录信息
            $recordId = PatientRecord::find()->select(['id'])->where(['patient_id' => $id, 'status' => [2, 3, 4, 5]])->orderBy(['id' => SORT_DESC])->limit(1)->asArray()->one();

            $triageInfo = Patient::findTriageInfo($recordId['id']);
            $triageInfo["patient_number"] = $model->patient_number;
            $triageInfo["username"] = $model->username;
            $triageInfo["first_record"] = $model->first_record;
            $triageInfo["sex"] = $model->sex;
            $triageInfo["birth"] = $model->birthTime;
            $triageInfo["birthday"] = Patient::dateDiffage($model->birthday);
            $triageInfo["head_img"] = $model->head_img;

            $familyData = PatientFamily::find()->where(['patient_id' => $id])->asArray()->all();
            $model->patientAllergy = PatientAllergy::getPatientAllergy($id);
        }

        

        $allergy = PatientAllergy::getPatientAllergyArray($id);

        return $this->render('view', [
                    'familyData' => $familyData,
                    'model' => $mutilModel,
                    'patientInfo' => $triageInfo,
                    'allergy' => $allergy
        ]);
    }

    protected function setPatientInfo($model) {
        $bmi = Patient::getBmi($model->heightcm, $model->weightkg);
        $patientInfo['head_img'] = $model->head_img;
        $patientInfo['username'] = $model->username;
        $patientInfo['modalUrl'] = $model->head_img; //
        $patientInfo['sex'] = $model->sex;
        $patientInfo['birth'] = date("Y-m-d", $model->birthday);
        $patientInfo['patient_number'] = $model->patient_number;
        $patientInfo['birthday'] = Patient::dateDiffage($model->birthday);
        $patientInfo['sex'] = $model->sex;
        $patientInfo['patient_source'] = $model->patient_source;
        $patientInfo['bmi'] = $bmi;
        $patientInfo['heightcm'] = $model->heightcm;
        $patientInfo['weightkg'] = $model->weightkg;
        $patientInfo['temperature_type'] = TriageInfo::$temperature_type[$model->temperature_type];
        $patientInfo['temperature'] = $model->temperature;
        $patientInfo['breathing'] = $model->breathing;
        $patientInfo['head_circumference'] = $model->head_circumference;
        $patientInfo['pulse'] = $model->pulse;
        $patientInfo['shrinkpressure'] = $model->shrinkpressure;
        $patientInfo['diastolic_pressure'] = $model->diastolic_pressure;
        $patientInfo['pain_score'] = $model->pain_score;
        $patientInfo['fall_score'] = $model->fall_score;
        $patientInfo['treatment_type'] = $model->treatment_type;
        $patientInfo['treatment'] = $model->treatment;
        $patientInfo['has_born_info'] = false;
        $patientInfo['has_medical'] = false;
        $patientInfo['has_info'] = false;
        $patientInfo['bloodtype'] = $model->bloodtype ? TriageInfo::$bloodtype[$model->bloodtype] : '';
        $patientInfo['oxygen_saturation'] = $model->oxygen_saturation;
        $patientInfo['first_record'] = $model->first_record;
        $bloodTypeSupplement = $model->blood_type_supplement ? explode(',', $model->blood_type_supplement) : '';
        if (!empty($bloodTypeSupplement)) {
            $bloodTypeSupplementStr = '';
            foreach ($bloodTypeSupplement as $key => $value) {
                $bloodTypeSupplementStr .= TriageInfo::$bloodTypeSupplement[$value] . '，';
            }
            $bloodTypeSupplement = rtrim($bloodTypeSupplementStr, '，');
        }
        if (5 == $patientInfo['treatment_type']) {
            $patientInfo['treatment'] = $patientInfo['treatment'];
        } else if (0 == $patientInfo['treatment_type']) {
            $patientInfo['treatment'] = null;
        } else {
            $patientInfo['treatment'] = TriageInfo::$treatment_type[$patientInfo['treatment_type']];
        }
        $patientInfo['bloodTypeSupplementStr'] = $bloodTypeSupplement;
        return $patientInfo;
    }

    /**
     * 
     * @param type $param
     * @param type $model
     * @return 渲染添加家庭成员的表单弹窗操作
     */
    protected function viewFamilyForm($param, $patient_id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {

            if (!empty($param['patient_family_id'])) {
                $model = PatientFamily::findOne(['id' => $param['patient_family_id'], 'patient_id' => $param['id']]);

                $model->birthday = $model->birthday ? date('Y-m-d', $model->birthday) : '';
            } else {
                $model = new PatientFamily();
                $model->patient_id = $patient_id;
            }
            $title = empty($param['patient_family_id']) ? '添加家庭成员' : '修改家庭成员';
            Yii::$app->response->format = Response::FORMAT_JSON;


            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return [
                    'forceReloadPage' => true,
                    'forceClose' => true
                ];
            } else {

                $ret = [
                    'title' => $title,
                    'content' => $this->renderAjax('_family_modal', [
                        'param' => $param,
                        'model' => $model,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确认', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
                return $ret;
            }
        }
    }

    /**
     * 
     * @param 家庭成员id $id
     * @throws NotFoundHttpException
     * @return 返回家庭成员信息model
     */
    protected function findFamilyModel($id) {
        $model = PatientFamily::findOne($id);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
    }

    /**
     * 
     * @param type $param
     * @property 删除家庭成员
     */
    public function deleteFamily($param) {

        $request = Yii::$app->request;
        if ($request->isAjax) {

            if ($request->isPost) {
                /*
                 *   Process for ajax request
                 */
                $this->findFamilyModel($param['patient_family_id'])->delete();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'forceClose' => true,
                    'forceReload' => '#basic-family-pjax'
                ];
            } else {
                throw new NotAcceptableHttpException();
            }
        } else {
            /*
             *   Process for non-ajax request
             */
            throw new NotAcceptableHttpException();
        }
    }

    /**
     * 
     * @param object $model
     * @return model 设置过敏史
     */
//     protected function setAllergy($model, $param) {
//         $allergy = [];
//         if (isset($param['has_allergy_type']) && $param['has_allergy_type'] == 1 && isset($param['allergySource'][0]) && $param['allergySource'][0]) {
//             foreach ($param['allergySource'] as $key => $val) {
//                 if ($val && $param['allergyReaction'][$key] && $param['allergyDegree'][$key]) {
//                     $allergy[] = [
//                         'source' => $val, //源
//                         'reaction' => $param['allergyReaction'][$key], //反应
//                         'degree' => $param['allergyDegree'][$key], //程度
//                     ];
//                 }
//             }
//             $model->allergy = $allergy ? Json::encode($allergy) : '';
//         } else {
//             if (isset($param['has_allergy_type']) && $param['has_allergy_type'] == 2) {
//                 $model->allergy = '';
//             }
//         }
//         return $model;
//     }

    /**
     * Creates a new Patient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Patient();
        $model->scenario = 'createPatient';
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
     * Updates an existing Patient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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
     * Deletes an existing Patient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('success', '删除成功');
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Patient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Patient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Patient::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            if ($id) {
                throw new NotFoundHttpException('你所请求的页面不存在.');
            } else {
                return new Patient();
            }
        }
    }

    /**
     * @param 患者id
     * @return 获取患者分表信息
     */
    protected function findSubmeterModel($id = null) {
        $model = PatientSubmeter::findOne(['patient_id' => $id, 'spot_id' => $this->parentSpotId]);
        if ($model !== null) {
            return $model;
        } else {
            $model = new PatientSubmeter();
            $model->patient_id = $id;
            return $model;
        }
    }

    public function actionMedicalRecord($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $historyPatientInfo = Patient::findTriageRecord($id);
        $isAjax = Yii::$app->request->isAjax;
        if (!$isAjax) {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        /* 处方 */
        $recipeRecordDataProvider = RecipeRecord::getRecipeRecordDataProvider($id);
//         $recipeList = RecipeList::getReciptListByStock();

        foreach ($historyPatientInfo as $key => $val) {
            $historyPatientInfo[$key]['bmi'] = Patient::getBmi($val['heightcm'], $val['weightkg']);

            $historyPatientInfo[$key]['bloodtype'] = TriageInfo::$bloodtype[$val['bloodtype']];
            $recordIdArr[] = $val['recordId'];
        }
        $nurseRecordData = NursingRecord::getNurseRecord($recordIdArr);
        $healthEducationData = HealthEducation::getHealthEducationRecord($recordIdArr);
        //已取消的医嘱不显示
        $inspectData = InspectRecord::getInspectRecord($recordIdArr, 4);
        $checkData = CheckRecord::getCheckRecord($recordIdArr);
        $cureData = CureRecord::getCureRecord($recordIdArr);
        $recipeData = RecipeRecord::getRecipeRecordDetail($recordIdArr);
        $checkReportData = Report::checkReportData($recordIdArr, 1, 2);
        $inspectReportData = Report::checkReportData($recordIdArr, 1, 1);
        $dentalHistoryData = DentalHistory::getDentalHistoryData($recordIdArr);
        $firstCheckData = FirstCheck::getPatientRecordFirstCheckInfo($recordIdArr);
        $allergyOutpatient = AllergyOutpatient::getAllergyByRecord($recordIdArr);
        $assessment = ChildAssessment::getAssessmentByRecord($recordIdArr);
        $ret = [
            'title' => "历史病历",
            'content' => $this->renderAjax('_information', [
                'historyPatientInfo' => $historyPatientInfo,
                'recipeRecordDataProvider' => $recipeRecordDataProvider,
//                 'recipeList' => $recipeList,
                'hidden' => true,
                'isReturn' => true,
                'nurseRecordData' => $nurseRecordData,
                'healthEducationData' => $healthEducationData,
                'inspectData' => $inspectData,
                'checkData' => $checkData,
                'cureData' => $cureData,
                'recipeData' => $recipeData,
                'inspectReportData' => $inspectReportData,
                'checkReportData' => $checkReportData,
                'dentalHistoryData' => $dentalHistoryData,
                'firstCheckData' => $firstCheckData,
                'allergyOutpatient' => $allergyOutpatient,
                'assessment' => $assessment
            ])
        ];
        return $ret;
    }

    /**
     * @param $id  流水ID
     * @param $reportType 
     * @param $isReturn 0表示在病历库，1表示在医生门诊弹窗
     * @param $patientId 患者ID
     * @return array
     * 查看报告弹窗
     */
    public function actionInformation($id, $reportType, $isReturn = 0, $patientId = 0) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $inspectCheckList = Report::reportData($id, $reportType);
        $checkRecordModel = new Check();
        $ret = [
            'title' => "门诊报告",
            'content' => $this->renderAjax('_report', [
                'checkRecordModel' => $checkRecordModel,
                'inspectCheckList' => $inspectCheckList,
            ]),
            'footer' => $isReturn ? (Html::a('返回', ['@medicalRecord', 'id' => $patientId, 'recordId' => $id], ['class' => 'btn btn-cancel btn-form', 'role' => 'modal-remote', 'data-toggle' => 'tooltip'])) :
                    (Html::button('关闭', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"])),
        ];
        return $ret;
    }

    /**
     * @return ump补录
     */
    public function actionMakeup($patient_family_id = null) {
        $isAjax = Yii::$app->request->isAjax;
        $isPjax = Yii::$app->request->isPjax;
        $isGet = Yii::$app->request->isGet;
        $param = Yii::$app->request->post();
        $paramContent = Yii::$app->request->get();


        if ($patient_family_id != null) { //添加家庭成员
            if ($patient_family_id != 0 && $paramContent['type'] == 2) {
                //删除家庭成员
                return $this->deleteFamily($paramContent);
            } else {
                return $this->viewFamilyForm($paramContent, $paramContent['id']);
            }
        }

        if (isset($paramContent['patientId'])) {
            $model = $this->findModel($paramContent['patientId']);
        } else {
            $model = $this->findModel(null);
        }

        $model->scenario = 'createPatient';

        //$model->scenario = 'baseInformation';
        if ($isAjax && !$isPjax) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

//                $model = $this->setAllergy($model, $param['Patient']);
                if ($model->save()) {
                    $patientNew = Patient::find('id')->where(['username' => Yii::$app->request->post()['Patient']['username']])->orderBy('id desc')->asArray()->one();
                    $this->result['errorCode'] = 0;
                    $this->result['msg'] = '保存成功';
                    $this->result['id'] = $model->id;
                    $this->result['url'] = Url::to(['@patientIndexMakeup', 'patientId' => $model->id]);
                } else {
                    $this->result['errorCode'] = 1;
                    $errors = $model->getErrors();
                }
            } else {
                $this->result['errorCode'] = 1001;
                $errors = $model->errors;
                if (isset($errors['username'])) {
                    $this->result['msg'] = $errors['username'];
                } else if (isset($errors['birthTime'])) {
                    $this->result['msg'] = $errors['birthTime'];
                }
            }
            return Json::encode($this->result);
        }

        $model->address = $model->province ? ($model->province . ($model->city ? '/' . $model->city : '') . ($model->area ? '/' . $model->area : '')) : '';
        $model->birthTime = $model->birthday != 0 ? date('Y-m-d', $model->birthday) : '';
        $model->hourMin = $model->birthday != 0 ? date('H:i', $model->birthday) : '';
//        $patientInfo = $this->setPatientInfo($model);
        $patientInfo = [];
        $familyData = [];
        if (isset($paramContent['patientId']) && $paramContent['patientId']) {
            $familyData = PatientFamily::find()->where(['patient_id' => $paramContent['patientId']])->asArray()->all();
        }
        if (isset($paramContent['patientId']) && $paramContent['patientId']) {
            $historyPatientInfo = Patient::findTriageRecord($paramContent['patientId'], 2);
        } else {
            $historyPatientInfo = [];
        }
//        $patientAppointmentInfo = Patient::getPatientAppointment($id);
        $recordIdArr = [];
        foreach ($historyPatientInfo as $key => $value) {
            if ($value['spot_id'] != $this->spotId) {
                unset($historyPatientInfo[$key]);
            } else {
                $recordIdArr[] = $value['recordId'];
            }
        }
        $historyPatientInfo = array_values($historyPatientInfo);

        $nurseRecordData = NursingRecord::getNurseRecord($recordIdArr);
        $healthEducationData = HealthEducation::getHealthEducationRecord($recordIdArr);
        //已取消的医嘱不显示
        $inspectData = InspectRecord::getInspectRecord($recordIdArr, 4);
        $checkData = CheckRecord::getCheckRecord($recordIdArr);
        $cureData = CureRecord::getCureRecord($recordIdArr);
        $recipeData = RecipeRecord::getRecipeRecordDetail($recordIdArr);
        $checkReportData = Report::checkReportData($recordIdArr, 1, 2);
        $inspectReportData = Report::checkReportData($recordIdArr, 1, 1);
        return $this->render('/ump/view', [
//                    'allergy_list' => $allergy_list,
                    'familyData' => $familyData,
                    'model' => $model,
                    'patientInfo' => $patientInfo,
                    'historyPatientInfo' => $historyPatientInfo,
                    'nurseRecordData' => $nurseRecordData,
                    'healthEducationData' => $healthEducationData,
                    'inspectData' => $inspectData,
                    'checkData' => $checkData,
                    'cureData' => $cureData,
                    'recipeData' => $recipeData,
                    'inspectReportData' => $inspectReportData,
                    'checkReportData' => $checkReportData,
//                    'patientAppointmentInfo' => $patientAppointmentInfo,
        ]);
    }

    /**
     * 登记类型
     */
    public function actionMakeupType() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ret = [
            'title' => "选择登记类型",
            'content' => $this->renderAjax('/ump/_makeupType', [
            ]),
            'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
            Html::button('确定', ['class' => 'btn btn-default union_submit btn-form makeup_type_ok', 'data-url' => Url::to(['makeup-base']), 'type' => "button", 'id' => 'union_submit', 'role' => 'modal-create', 'contenttype' => 'application/x-www-form-urlencoded', 'data-modal-size' => 'large'])
        ];
        return $ret;
    }

    /**
     * 补录接诊信息
     */
    public function actionMakeupBase() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new UmpRecord();
        $recordId = 0;
        if (($recordId = Yii::$app->request->get('recordId'))) {
            $isAppointment = Appointment::getAppointment($recordId);
            $triageInfo = TriageInfo::findModel($recordId);
            if ($isAppointment != NULL && !empty($isAppointment)) {
                $patientId = $isAppointment->patient_id;
                $makeupType = 1;
                $model->appointment_time = date('Y-m-d H:i:s', $isAppointment->time);
            } else {
                $patientRecord = PatientRecord::findOne(['id' => $recordId]);
                $patientId = $patientRecord->patient_id;
                $makeupType = 2;
                $model->appointment_type = $patientRecord->type;
            }
            $model->diagnosis_time = date('Y-m-d H:i', $triageInfo->diagnosis_time);
            $model->doctor_id = $triageInfo->doctor_id;
            $model->room_id = $triageInfo->room_id;
            $model->isEdit = 1;
        } else {
            $patientId = Yii::$app->request->get('patientId');
            $makeupType = Yii::$app->request->get('makeupType');
        }
        if ($makeupType == 1) {
            $model->scenario = 'appointment';
        } else {
            $model->scenario = 'report';
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            //走预约补录
            if ($makeupType == 1) {
                UmpRecord::makeupByAppointment($patientId, $recordId ? $recordId : $model->appointment_time, $model->doctor_id, $model->diagnosis_time, $model->room_id);
            } else {//走登记补录
                UmpRecord::makeupByReport($patientId, $model->appointment_type, $model->doctor_id, $model->diagnosis_time, $model->isEdit, $recordId, $model->room_id);
            }
//            return ['forceClose' => true, 'forceReload' => '#ump-record','forceRedirect'=>  Url::to(['makeup','patientId'=>$patientId,'#'=>'treatment'])];
            return ['forceClose' => true, 'forceReloadPage' => true, 'forceMessage' => "保存成功"];
        }
        $appointmentTimeList = Appointment::getTimeList($patientId);
//        $appointmentTypeList = [['id' => 1, 'name' => '初诊'], ['id' => 2, 'name' => '复诊']];
//        $doctorList = [['id' => 1, 'username' => '王医生'], ['id' => 2, 'username' => '李医生']];
        $doctorList = User::getWorkerList('', '', $this->spotId, 2);
        $roomList = Room::getRoomList([1]);
        try {
            $patientInfo = $this->findModel($patientId);
        } catch (NotFoundHttpException $exc) {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
        $ret = [
            'title' => "补录接诊信息-" . Html::encode($patientInfo->username),
            'content' => $this->renderAjax('/ump/_makeupBasic', [
                'model' => $model,
                'appointmentTimeList' => $appointmentTimeList,
                'makeupType' => $makeupType,
                'patientId' => $patientId,
                'doctorList' => $doctorList,
                'roomList' => $roomList,
            ]),
            'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
            Html::button('保存', ['class' => 'btn btn-default union_submit btn-form makeup_type_ok', 'type' => "submit", 'id' => 'union_submit'])
        ];
        return $ret;
    }

    /**
     * @return ump补录关键体征数据
     */
    public function actionSignsData($recordId) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $model = TriageInfo::findOne(['record_id' => $recordId]);
            $title = '关键体征数据';
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->blood_type_supplement = $model->blood_type_supplement ? implode(',', $model->blood_type_supplement) : '';
                if ($model->save()) {
                    //如果补录的接诊时间小于最新已有结束就诊时间则不同步，否则就将就诊信息同步到患者库
                    $patientRecord = PatientRecord::getPatientRecord($recordId);
                    $patientId = $patientRecord['patient_id'];
                    $latestEndTime = PatientRecord::find()->where(['patient_id' => $patientId])->max('end_time');
                    if ($model->diagnosis_time > $latestEndTime) {//同步
                        $triageInfo = $model->toArray();
                        UmpRecord::SyncPatientInformation($model, $patientId);
                    }
                    return [
                        'forceReload' => '#ump_signsData' . $recordId,
                        'forceClose' => true,
                        'forceMessage' => "保存成功"
                    ];
                }
            } else {
                $model->blood_type_supplement = is_string($model->blood_type_supplement) ? explode(',', $model->blood_type_supplement) : $model->blood_type_supplement; //如果是拉取数据，将字符串转成数组，如果验证不通过，则维持数组原状
                $ret = [
                    'title' => $title,
                    'content' => $this->renderAjax('@signMeasurementTemplate', [
//                    'content' => $this->renderAjax('/ump/_signsData', [
                        'model' => $model,
                        'action' => Url::to(['signs-data', 'recordId' => $recordId]),
                        'isFormSubmit' => false
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form btn-custom makeup-signsdata', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn btn-default btn-form btn-custom makeup-signsdata', 'type' => "submit", 'id' => 'btn-custom'])
//                    'footer'=>''
                ];
                return $ret;
            }
        }
    }

    /**
     * @return ump补录病例信息
     */
    public function actionRecordInfo($recordId) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $model = TriageInfo::findOne(['record_id' => $recordId]);
            $model->morbidityDate = $model->incidence_date ? date('Y-m-d', $model->incidence_date) : '';
            $model->scenario = 'outpatientMedicalRecord';
            $title = '病历信息';

            $multiModel = new MultiModel([
                'models' => [
                    'triageInfo' => $model,
                    'triageInfoRelation' => $this->findTriageInfoRelation($recordId),
                    'outpatientRelation' => $this->findOutpatientRelation($recordId)
                ]
            ]);
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($multiModel->load(Yii::$app->request->post()) && $multiModel->save()) {
                return [
                    'forceReload' => '#ump_recordInfo' . $recordId,
                    'forceClose' => true,
                    'forceMessage' => "操作成功"
                ];
            } else {
                $ret = [
                    'title' => $title,
                    'content' => $this->renderAjax('/ump/_recordInfo', [
                        'model' => $multiModel,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn btn-default btn-form ', 'type' => "submit"])
                ];
                return $ret;
            }
        }
    }

    /**
     *
     * @author JeanneWu
     * @time 2017年2月16日 10:33
     * @param 就诊流水id $id
     * @return TriageInfoRelation 返回 门诊病历关联的记录信息
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
     * $type 百分率的类型（1/身高 2/体重 3/头围 4/BMI）
     */
    public function actionTestPercent() {
        $sex = 2;
        $data = 20.82;
        $type = 4;
        $before = '1518796800';
        $zscore = Percentage::getZScore($data, $sex, $type, $before);
        $percent = Percentage::getPercentage($data, $sex, $type, $before);
//        echo strtotime('2018-2-17');exit;
//        $age=  \app\common\Percentage::getAge('1307635200');
//        $age=  Patient::dateDiffage('1330358400');
//        $age=  Patient::dateDiffageTime('1327593600');
//        print_r($age);exit;
        echo 'zscore:' . $zscore . '<br>' . 'percent:' . $percent;
    }

    public function actionTestTrans() {
        $model = new CardHistory();
        $db = Yii::$app->getDb();
        $t = $db->beginTransaction();
        try {
            $model->f_record_id = 1;
            $model->f_update_beg = '11';
            $model->f_update_end = '11';
            $model->f_user_id = 1;
            $model->f_user_name = '11';
            $res = $model->save();
            if ($res) {
                throw new Exception('保存失败');
            }
            $t->commit();
        } catch (Exception $exc) {
            $t->rollBack();
            echo $exc->getMessage();
        }
    }

}
