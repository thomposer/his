<?php

namespace app\modules\api\controllers;

use app\modules\cure\models\Cure;
use app\modules\inspect\models\Inspect;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\report\models\Report;
use app\modules\spot\models\CheckList;
use app\modules\spot\models\RecipeList;
use app\modules\spot_set\models\InspectClinic;
use Yii;
use yii\helpers\Json;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\modules\spot\models\Spot;
use app\modules\outpatient\models\InspectRecord;
use app\common\Common;
use app\modules\pharmacy\models\PharmacyRecord;
use app\modules\patient\models\PatientRecord;
use app\modules\triage\models\TriageInfo;
use app\modules\patient\models\Patient;
use app\modules\make_appointment\models\Appointment;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\triage\models\NursingRecord;
use app\modules\triage\models\HealthEducation;
use yii\db\Query;
use app\modules\check\models\Check;
use app\modules\outpatient\models\CheckRecord;
use app\modules\outpatient\models\RecipeTemplateInfo;
use app\modules\outpatient\models\RecipeTemplate;
use app\modules\spot\models\CheckCode;
use app\modules\user\models\User;
use app\modules\inspect\models\InspectRecordUnion;
use app\modules\charge\models\ChargeInfo;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\FirstCheck;
use app\modules\outpatient\models\AllergyOutpatient;
use app\modules\outpatient\models\DentalHistory;
use app\modules\outpatient\models\OutpatientRelation;
use app\modules\outpatient\models\DentalHistoryRelation;
use app\modules\outpatient\models\InspectTemplateInfo;
use app\modules\outpatient\models\InspectTemplate;
use app\modules\outpatient\models\ChildExaminationBasic;
use app\modules\outpatient\models\ChildExaminationGrowth;
use app\modules\outpatient\models\ChildExaminationCheck;
use app\modules\outpatient\models\ChildExaminationInfo;
use app\modules\outpatient\models\ChildExaminationAssessment;
use app\common\Percentage;
use app\modules\outpatient\models\ConsumablesRecord;
use app\modules\outpatient\models\CureTemplate;
use app\modules\outpatient\models\CureTemplateInfo;
use app\modules\spot\models\SpotConfig;
use app\modules\triage\models\ChildAssessment;
use app\modules\outpatient\models\CheckTemplate;
use app\modules\outpatient\models\CheckTemplateInfo;
use app\modules\outpatient\models\OrthodonticsReturnvisitRecord;
use app\modules\outpatient\models\OrthodonticsFirstRecord;
use app\modules\spot_set\models\CheckListClinic;
use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\ClinicCure;
use app\modules\outpatient\models\RecipeTypeTemplate;
use app\modules\spot_set\models\RecipelistClinic;
use app\modules\stock\models\Stock;
use app\modules\stock\models\StockInfo;
use app\modules\pharmacy\models\RecipeBatch;
use app\modules\spot_set\models\ConsumablesClinic;
use app\modules\stock\models\ConsumablesStockInfo;
use app\modules\spot_set\models\Material;
use app\modules\outpatient\models\MaterialRecord;
use app\modules\stock\models\MaterialStockInfo;

/**
 *
 * @author 庄少雄
 * @property 医生门诊接口API
 */
class OutpatientController extends CommonController
{

    public function behaviors() {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'inspect-application-print' => ['post'],
                    'check-application-print' => ['post'],
                    'get-doctor-record-data' => ['post'],
                    'doctor-check-inspect-list' => ['post'],
                    'get-cure-template-info' => ['post'],
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    /**
     * inspect-application-view
     * @param int $id 流水id
     * @return string title 实验室检查项页面弹窗标题
     * @return array content 实验室检查项弹窗页面
     * @throws NotFoundHttpException
     * @desc 渲染可供选择打印的实验室检查项弹窗页面
     */
    public function actionInspectApplication($id) {
        if (Yii::$app->request->isAjax) {
            $model = new \app\modules\inspect\models\Inspect();
            $inspectList = Inspect::getInspectRecordList(['record_id' => $id], ['id', 'name', 'status']);
            $inspectCancelId = InspectRecord::find()->select(['id'])->where(['record_id' => $id, 'status' => 4])->asArray()->all();
            if (!empty($inspectCancelId)) {
                $inspectId = array_column($inspectCancelId, 'id');
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "打印申请单",
                'content' => $this->renderAjax('@outpatientInspectApplicationView', [
                    'model' => $model,
                    'inspectList' => $inspectList,
                    'inspectId' => $inspectId
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                Html::button('确定', ['class' => 'btn btn-default btn-form btn-inspect-application-print ', 'data-dismiss' => "modal",'name' => 'inspect-application-print' . $id . 'myshow'])
            ];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     *  inspect-application-print
     *  @param int $inspect_id 实验室检查项id
     *  @param int $record_id 就诊流水id
     *  @return int errorCode 错误代码 （0-成功 1001-参数错误）
     *  @return string msg 错误信息
     *  @return array spotInfo 诊所信息
     *  @return array recipeInfo 就诊信息
     *  @return array inspectApplication 实验室检查项信息
     *  @return array inspectTotalPrice 实验室检查项总价格
     *  @return array inspectTime 实验室检查项最新一项开单时间
     * * @desc 返回实验室检查项打印接口详情数据
     */
    public function actionInspectApplicationPrint() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $inspectId = Yii::$app->request->post('inspect_id');
        $recordId = Yii::$app->request->post('record_id');
        if (empty($inspectId)) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '请勾选要打印的项目';
        } else {
            $spotInfo = Spot::getSpot();
            $inspectData = InspectRecord::find()->select(['name', 'price', 'specimen_type', 'deliver', 'deliver_organization', 'inspect_english_name', 'remark', 'description', 'status'])->where(['id' => $inspectId])->asArray()->all();
            $inspectTime = PharmacyRecord::getBillingTime($recordId, 1);
            $inspectTime = date("Y-m-d H:i:s", $inspectTime);
            $inspectTotalPrice = 0;
            if (!empty($inspectData)) {
                foreach ($inspectData as $key => $value) {
                    $inspectData[$key]['specimen_type'] = InspectClinic::$getSpecimenType[$value['specimen_type']];
//                    if(62 == $this->spotId){//临时方案，上海诊所外送项目显示外送，需要修改
//                        $inspectData[$key]['deliver'] = ($value['deliver'] == 1 ? '外送' : '诊所内');
//                    }else{
//                        $inspectData[$key]['deliver'] = InspectClinic::$laboratory[$value['deliver']];
//                    }
                    $inspectData[$key]['deliver'] = ($value['deliver'] == 1 ? InspectClinic::$getDeliverOrganization[$value['deliver_organization']] : '诊所内');
                    $inspectTotalPrice +=$value['price'];
                }
            }
            $inspectTotalPrice = Common::num($inspectTotalPrice);
            $pharmcyRecordModel = new PharmacyRecord();
            $recipeInfo = $pharmcyRecordModel->getRepiceInfo($recordId, 1);
            $triageInfo = Patient::findTriageInfo($recordId);
            $triageInfo['receive_sex'] = Patient::$getSex[$triageInfo['sex']];
            $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
            $this->result['spotInfo'] = $spotInfo;
            $this->result['recipeInfo'] = $recipeInfo;
            $this->result['inspectApplication'] = $inspectData;
            $this->result['inspectTotalPrice'] = $inspectTotalPrice;
            $this->result['inspectTime'] = $inspectTime;
            $this->result['triageInfo'] = $triageInfo;
            $this->result['spotConfig'] = $spotConfig;
        }
        return $this->result;
    }

    /**
     *  report-inspect-print-info
     *  @param int $id 实验室检查id
     *  @param int $record_id 就诊流水id
     *  @return int errorCode 错误代码 （0-成功 1001-参数错误）
     *  @return string msg 错误信息
     *  @return array spotInfo 诊所信息
     *  @return array inspectInfo 实验室检查项目信息
     *  @return array inspectRepiceInfo 处方信息
     * * @desc 返回实验室检查项打印接口详情数据
     */
    public function actionInspectReportPrint() {

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
            $inspectInfo[$key]['result_identification'] = InspectRecordUnion::getResultIdentification($v['result_identification']);
        }
        $firstCheck = FirstCheck::getFirstCheckInfo($record_id);
        $allergy = AllergyOutpatient::getAllergyByRecord($record_id);
        $allergy = isset($allergy) ? $allergy[$record_id] : [];
        if (!empty($allergy)) {
            foreach ($allergy as $key => $value) {
                $allergy[$key] = Html::encode($value);
            }
        }
        $triageInfo = Patient::findTriageInfo($record_id);
        $triageInfo['receive_sex'] = Patient::$getSex[$triageInfo['sex']];
        $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
        $this->result['spotConfig'] = $spotConfig;
        $this->result['spotInfo'] = $spotInfo;
        $this->result['inspectRepiceInfo'] = $inspect_repiceInfo;
        $this->result['inspectInfo'] = $inspectInfo;
        $this->result['triageInfo'] = $triageInfo;
        $this->result['firstCheck'] = Html::encode($firstCheck);
        $this->result['allergy'] = $allergy;

        return $this->result;
    }

    /**
     *  nursing-print-info
     *  @param int $record_id 就诊流水id
     *  @return int errorCode 错误代码 （0-成功 1001-参数错误）
     *  @return string msg 错误信息
     *  @return array spotInfo 诊所信息
     *  @return array basicInfo 基础信息
     *  @return array physicalInfo 体征信息
     *  @return array nursingRecord 护理记录信息
     *  @return array healthEducation 健康教育信息
     * * @desc 获取护理记录打印信息接口
     */
    public function actionNursingPrinkInfo() {
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
                        'e.username', 'e.sex', 'e.birthday', 'e.iphone', 'a.type', 'a.create_time', 'f.name as departmentName', 'a.case_id',
                        'b.treatment_type', 'b.treatment', 'b.pain_score', 'b.fall_score',
                        'b.heightcm', 'b.weightkg', 'b.head_circumference', 'b.temperature', 'b.breathing', 'b.pulse', 'b.shrinkpressure', 'b.diastolic_pressure',
//                        'GROUP_CONCAT(c.name)', 'GROUP_CONCAT(c.executor)','GROUP_CONCAT(c.execute_time)','GROUP_CONCAT(c.content)',
//                        'GROUP_CONCAT(d.education_content)', 'GROUP_CONCAT(d.education_object)','GROUP_CONCAT(d.education_method)','GROUP_CONCAT(d.accept_barrier)','GROUP_CONCAT(d.accept_ability)'
                    ]
            );
            $query->leftJoin(['b' => TriageInfo::tableName()], '{{a}}.id = {{b}}.record_id');
//            $query->leftJoin(['c' => NursingRecord::tableName()], '{{a}}.id = {{c}}.record_id');
//            $query->leftJoin(['d' => HealthEducation::tableName()], '{{a}}.id = {{d}}.record_id');
            $query->leftJoin(['e' => Patient::tableName()], '{{a}}.patient_id = {{e}}.id');
            $query->leftJoin(['g' => Report::tableName()], '{{a}}.id = {{g}}.record_id');
            $query->leftJoin(['f' => SecondDepartment::tableName()], '{{g}}.second_department_id = {{f}}.id');
            $query->where(['a.id' => $record_id, 'a.spot_id' => $this->spotId]);
            $data = $query->one();

            //基本信息只需要传科室名，其他信息已经获取过
            $basicInfo['departmentName'] = $data['departmentName'];

            //就诊方式，疼痛评分，跌倒评分
            $basicInfo['treatment'] = $data['treatment_type'] ? ( $data['treatment_type'] != 5 ? TriageInfo::$treatment_type[$data['treatment_type']] : $data['treatment']) : null;
            $assessment = ChildAssessment::getAssessmentByRecord($record_id);
            $basicInfo['pain_score'] = isset($assessment[$record_id][1]) ? $assessment[$record_id][1] : [];
            $basicInfo['fall_score'] = isset($assessment[$record_id][2]) ? $assessment[$record_id][2] : [];

            //体征测量
            $physicalInfo['heightcm'] = $data['heightcm'] ? $data['heightcm'] . 'cm' : null;
            $physicalInfo['weightkg'] = $data['weightkg'] ? $data['weightkg'] . 'kg' : null;
            $physicalInfo['head_circumference'] = $data['head_circumference'] ? $data['head_circumference'] . 'cm' : null;
            $bmi = Patient::getBmi($data['heightcm'], $data['weightkg']);
            $physicalInfo['bmi'] = $bmi ? $bmi . 'kg/m²' : null;
            $physicalInfo['temperature'] = $data['temperature'] ? $data['temperature'] . '°C' : null;
            $physicalInfo['breathing'] = $data['breathing'] ? $data['breathing'] . '次/分钟' : null;
            $physicalInfo['pulse'] = $data['pulse'] ? $data['pulse'] . '次/分钟' : null;

            //血压
            $data['shrinkpressure'] = $data['shrinkpressure'] ? $data['shrinkpressure'] : '&nbsp;';
            $data['diastolic_pressure'] = $data['diastolic_pressure'] ? $data['diastolic_pressure'] : '&nbsp;';
            $physicalInfo['bloodPressure'] = ($data['shrinkpressure'] == '&nbsp;' && $data['diastolic_pressure'] == '&nbsp;') ? '&nbsp;/&nbsp;（≥3岁）' : $data['shrinkpressure'] . '/' . $data['diastolic_pressure'] . 'mmHg'; //血压
            //护理记录
            $nursingRecord = NursingRecord::find()->select(['name', 'executor', 'execute_time', 'content'])->where(['spot_id' => $this->spotId, 'record_id' => $record_id])->all();
            foreach ($nursingRecord as &$value) {
                $value['execute_time'] = date('Y-m-d H:i', $value['execute_time']);
            }

            //健康教育
            $healthEducation = HealthEducation::find()->select(['education_content', 'education_object', 'education_method', 'accept_barrier', 'accept_ability'])->where(['spot_id' => $this->spotId, 'record_id' => $record_id])->all();
            foreach ($healthEducation as &$value) {
                $value['education_object'] = HealthEducation::$getEducationObject[$value['education_object']];
                $value['education_method'] = HealthEducation::$getEducationMethod[$value['education_method']];
                $value['accept_barrier'] = HealthEducation::$getAcceptBarrier[$value['accept_barrier']];
                $value['accept_ability'] = HealthEducation::$getAcceptAbility[$value['accept_ability']];
            }

            $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
            $this->result['spotConfig'] = $spotConfig;
            $this->result['spotInfo'] = $spotInfo;
            $this->result['basicInfo'] = $basicInfo;
            $this->result['physicalInfo'] = $physicalInfo;
            $this->result['nursingRecord'] = $nursingRecord;
            $this->result['healthEducation'] = $healthEducation;
            return $this->result;
        }
    }

    /**
     * check-application-view
     * @param int $id 流水id
     * @return string title 影像学检查项页面弹窗标题
     * @return array content 影像学检查项弹窗页面
     * @throws NotFoundHttpException
     * @desc 渲染可供选择打印的影像学检查项弹窗页面
     */
    public function actionCheckApplication($id) {
        if (Yii::$app->request->isAjax) {
            $model = new \app\modules\check\models\Check();
            $checkList = Check::getCheckItemList($id, ['id', 'name']);
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->check = array_column($checkList, 'id');
            return [
                'title' => "打印申请单",
                'content' => $this->renderAjax('@outpatientCheckApplicationView', [
                    'model' => $model,
                    'checkList' => $checkList,
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                Html::button('确定', ['class' => 'btn btn-default btn-form btn-check-application-print ', 'data-dismiss' => "modal", 'name' => 'check-application-print' . $id . 'myshow'])
            ];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * cure-application-view
     * @param int $id 流水id
     * @return string title 治疗页面弹窗标题
     * @return array content 治疗弹窗页面
     * @throws NotFoundHttpException
     * @desc 渲染可供选择打印的治疗弹窗页面
     */
    public function actionCureApplication($id) {
        if (Yii::$app->request->isAjax) {
            $model = new Cure();
            $cureList = Cure::getCureItemList($id, ['id', 'name']);
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->cure = array_column($cureList, 'id');
            return [
                'title' => "打印治疗",
                'content' => $this->renderAjax('@outpatientCureApplicationView', [
                    'model' => $model,
                    'cureList' => $cureList,
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确定', ['class' => 'btn btn-default btn-form btn-cure-application-print ', 'data-dismiss' => "modal", 'name' => $id . 'cure-myshow'])
            ];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     *  check-application-print
     *  @param int $check_id 影像学检查项id
     *  @param int $record_id 就诊流水id
     *  @return int errorCode 错误代码 （0-成功 1001-参数错误）
     *  @return string msg 错误信息
     *  @return array spotInfo 诊所信息
     *  @return array recipeInfo 就诊信息
     *  @return array inspectApplication 影像学检查项信息
     *  @return array inspectTotalPrice 影像学检查项总价格
     *  @return array inspectTime 影像学检查项最新一项开单时间
     * * @desc 返回影像学检查项打印接口详情数据
     */
    public function actionCheckApplicationPrint() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $checkId = Yii::$app->request->post('check_id');
        $recordId = Yii::$app->request->post('record_id');
        if (empty($checkId)) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '请勾选要打印的项目';
        } else {
            //诊所信息
            $spotInfo = Spot::getSpot();

            //影像学检查项申请打印数据
            $checkData = CheckRecord::find()->select(['name', 'price'])->where(['id' => $checkId])->asArray()->all();

            //最新一条的开单时间
            $checkTime = PharmacyRecord::getBillingTime($recordId, 2);
            $checkTime = date("Y-m-d H:i:s", $checkTime);

            //影像学申请检查项总额
            $checkTotalPrice = 0;
            if (!empty($checkData)) {
                foreach ($checkData as $key => $value) {
                    $checkTotalPrice +=$value['price'];
                }
            }
            $checkTotalPrice = Common::num($checkTotalPrice);

            //就诊信息
            $pharmcyRecordModel = new PharmacyRecord();
            $recipeInfo = $pharmcyRecordModel->getRepiceInfo($recordId, 1);
            $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
            $this->result['spotInfo'] = $spotInfo;
            $this->result['recipeInfo'] = $recipeInfo;
            $this->result['checkApplication'] = $checkData;
            $this->result['checkTotalPrice'] = $checkTotalPrice;
            $this->result['checkTime'] = $checkTime;
            $this->result['spotConfig'] = $spotConfig;
        }
        return $this->result;
    }


    /**
     *  get-recipe-template-info
     *  @return array inspectTime 处方数组
     * * @desc 获取处方模板详情
     */
    public function actionGetRecipeTemplateInfo() {
        $id = Yii::$app->request->post('id');
        if (!isset($id)) {
            return;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = new Query();
        $query->from(['a' => RecipeTemplate ::tableName()]);
        $query->leftJoin(['b' => RecipeTemplateInfo::tableName()], '{{b}}.recipe_template_id ={{a}}.id');
        $query->leftJoin(['c' => RecipeList::tableName()], '{{b}}.recipe_id ={{c}}.id');
        $query->where(['b.spot_id' => $this->spotId, 'b.recipe_template_id' => $id, 'a.user_id' => $this->userInfo->id, 'c.status' => 1]);
        $query->select([
            'b.clinic_recipe_id', 'b.recipe_id', 'b.dose', 'b.dose_unit', 'b.used', 
            'b.frequency', 'b.day', 'b.num', 'b.description',
            'b.type', 'b.skin_test_status', 'b.skin_test', 'b.curelist_id', 'c.high_risk'
        ]);
        $query->orderBy(['b.id' => SORT_ASC]);
        $result = $query->all();
        $recipeList = [];
        if(!empty($result)){
            $recipeList = RecipelistClinic::getReciptListByStock(['t1.id' => array_column($result, 'clinic_recipe_id')]);
            $recipeList = empty($recipeList)?[]:array_values($recipeList);
        }
        $this->result['data']['recipeTemplateInfo'] = $result;
        $this->result['data']['recipeList'] = $recipeList;
        return $this->result;
    }

    /**
     *  get-recipe-template-info
     *  @return array inspectTime 处方数组
     * * @desc 获取实验室模板详情
     */
    public function actionGetInspectTemplateInfo() {
        $id = Yii::$app->request->post('id');
        if (!isset($id)) {
            return;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = new Query();
        $query->from(['a' => InspectTemplate ::tableName()]);
        $query->leftJoin(['b' => InspectTemplateInfo::tableName()], '{{b}}.inspect_template_id ={{a}}.id');
        
        $query->where(['b.spot_id' => $this->spotId, 'b.inspect_template_id' => $id, 'a.user_id' => $this->userInfo->id]);
        $query->select(['b.clinic_inspect_id', 'b.id']);
        $query->orderBy(['b.id' => SORT_ASC]);
        $result = $query->all();
        $clinicInspectIdList = array_column($result, 'clinic_inspect_id');
        $this->result['data']['inspectTemplateInfo'] = $result; 
        $this->result['data']['inspectList'] = InspectClinic::getInspectClinicList(['a.id' => $clinicInspectIdList]);
        return $this->result;
    }

    /**
     *  get-check-template-info
     *  @return array  检查医嘱数组
     * * @desc 获取检查模板详情
     */
    public function actionGetCheckTemplateInfo() {
        $id = Yii::$app->request->post('id');
        if (!isset($id)) {
            return;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = new Query();
        $query->from(['a' => CheckTemplate ::tableName()]);
        $query->leftJoin(['b' => CheckTemplateInfo::tableName()], '{{b}}.check_template_id ={{a}}.id');
        $query->leftJoin(['c' => CheckListClinic::tableName()],'{{b}}.clinic_check_id = {{c}}.id');
        $query->leftJoin(['d' => CheckList::tableName()],'{{c}}.check_id = {{d}}.id');
        $query->where(['b.spot_id' => $this->spotId, 'b.check_template_id' => $id, 'a.user_id' => $this->userInfo->id,'c.status' => 1]);
        $query->select(['id' => 'b.clinic_check_id', 'check_template_info_id' => 'b.id','c.price','c.check_id','d.name','d.unit','d.tag_id','d.meta']);
        $query->orderBy(['b.id' => SORT_ASC]);
        $result = $query->all();
        return $result;
    }

    /**
     *  get-cure-template-info
     *  @return array cureTime 治疗数组
     * * @desc 获取治疗模板详情
     */
    public function actionGetCureTemplateInfo() {
        $id = Yii::$app->request->post('id');
        if (!isset($id)) {
            return;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = new Query();
        $query->from(['a' => CureTemplate ::tableName()]);
        $query->leftJoin(['b' => CureTemplateInfo::tableName()], '{{b}}.cure_template_id ={{a}}.id');
        $query->where(['b.spot_id' => $this->spotId, 'b.cure_template_id' => $id, 'a.user_id' => $this->userInfo->id]);
        $query->select(['b.clinic_cure_id', 'b.id', 'b.time', 'b.description']);
        $query->orderBy(['b.id' => SORT_ASC]);
        $result = $query->all();
        $this->result['data']['cureTemplateInfo'] = $result;
        $this->result['data']['cureList'] = ClinicCure::getCureList(null,['a.id' => array_column($result, 'clinic_cure_id')]);
        return $this->result;
    }

    /**
     *  get-check-code-list
     *  @return array
     * * @desc 获取诊断代码
     */
    public function actionGetCheckCodeList() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $search = Yii::$app->request->get('search');
        $infoSql = 'CONCAT(name,
                        CONCAT(IF(LENGTH(help_code) > 0,CONCAT("(",help_code,")"),"")),
                        CONCAT(IF(LENGTH(add_code) > 0,CONCAT("(",add_code,")"),"")),
                        CONCAT(IF(LENGTH(major_code) > 0,CONCAT("(",major_code,")"),""))
            )';
        $data = CheckCode::find()
                        ->select(['id', 'text' => $infoSql, 'name'])
                        ->where(['AND',
                                    ['spot_id' => $this->parentSpotId, 'status' => 1], 
                                    ['OR', 
                                        ['like', 'name', $search], 
                                        ['like', 'help_code', $search], 
                                        ['like', 'add_code', $search], 
                                        ['like', 'major_code', $search]
                                    ]
                                ])
                        ->orderBy(['help_code' => SORT_ASC])
                        ->limit(30)->asArray()->all();
        $this->result['list'] = $data;
        return $this->result;
    }

    /**
     *  recipe-print
     *  @param int $id 处方id
     *  @param int $record_id 就诊流水id
     *  @return int errorCode 错误代码 （0-成功 1001-参数错误）
     *  @return string msg 错误信息
     *  @return array spotInfo 诊所信息
     *  @return array PharmcyRepiceInfo 获取处方的开单医生和过敏史以及诊断信息
     *  @return array recipeRecordDataProvider 处方信息
     *  @return string totalPrice 总价
     *  @return string triageInfo 接诊分诊信息
     * * @desc 返回处方项打印接口详情数据
     */
    public function actionRecipePrint() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $recipe_id = Yii::$app->request->post('id');
        $record_id = Yii::$app->request->post('record_id');
        $filterType = Yii::$app->request->post('filterType') ? Yii::$app->request->post('filterType') : 0;
        if (empty($recipe_id || empty($record_id))) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
        } else {
            $pharmcyRecordModel = new PharmacyRecord();
            $pharmcyRepiceInfo = $pharmcyRecordModel->getRepiceInfo($record_id, 4);
            $spotInfo = Spot::getSpot();
            
            $recipeRecordDataProvider = RecipeRecord::findRecipeRecordPrintDataProvider($record_id, $recipe_id,$filterType);
            $triageInfo = Patient::findTriageInfo($record_id);
            $triageInfo['receive_sex'] = Patient::$getSex[$triageInfo['sex']];
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
                    $recipeRecordDataProvider[$key]['type'] = RecipeList::$getAddress[$v['type']]; //取药地址转换
                    $totalPrice += $recipeRecordDataProvider[$key]['single_total_price'];
                }
            }
            $firstCheck = FirstCheck::getFirstCheckInfo($record_id);
            $allergy = AllergyOutpatient::getAllergyByRecord($record_id);
            $allergy = isset($allergy) ? $allergy[$record_id] : [];
            if (!empty($allergy)) {
                foreach ($allergy as $key => $value) {
                    $allergy[$key] = Html::encode($value);
                }
            }
            $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape','recipe_rebate']);
            $this->result['spotConfig'] = $spotConfig;
            $this->result['spotInfo'] = $spotInfo;
            $this->result['PharmcyRepiceInfo'] = $pharmcyRepiceInfo;
            $this->result['recipeRecordDataProvider'] = $recipeRecordDataProvider;
            $this->result['totalPrice'] = Common::num($totalPrice);
            $this->result['triageInfo'] = $triageInfo;
            $this->result['firstCheck'] = Html::encode($firstCheck);
            $this->result['allergy'] = $allergy;
        }
        return $this->result;
    }

    /**
     *  cure-print
     *  @param int $id 治疗id
     *  @param int $record_id 就诊流水id
     *  @return int errorCode 错误代码 （0-成功 1001-参数错误）
     *  @return string msg 错误信息
     *  @return array spotInfo 诊所信息
     *  @return array cureRepiceInfo 获取处方的开单医生和过敏史以及诊断信息
     *  @return array pirntCureRecordInfo 治疗信息
     *  @return string totalPrice 总价
     *  @return string triageInfo 接诊分诊信息
     * * @desc 返回治疗项打印接口详情数据
     */
    public function actionCurePrint() {
        $totalPrice = 0;
        $record_id = Yii::$app->request->post('record_id');
        $cure_id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (empty($cure_id || empty($record_id))) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
        } else {
            $pharmcyRecordModel = new PharmacyRecord();
            $cureRepiceInfo = $pharmcyRecordModel->getRepiceInfo($record_id, 3); //获取处方的开单医生和过敏史 以及诊断信息
            $cureRecord = CureRecord::find()->select(['id', 'name', 'unit', 'price', 'time', 'description', 'status', 'remark'])->where(['spot_id' => $this->spotId, 'record_id' => $record_id, 'id' => $cure_id])->asArray()->all(); //治疗医嘱列表
            $soptInfo = Spot::getSpot();
            $triageInfo = Patient::findTriageInfo($record_id);
            $triageInfo['receive_sex'] = Patient::$getSex[$triageInfo['sex']];
            foreach ($cureRecord as $i => $value) {
                $cureRecord[$i]['columnTotalPrice'] = Common::num($value['time'] * $value['price']);
                $totalPrice += $cureRecord[$i]['columnTotalPrice'];
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
            $this->result['soptInfo'] = $soptInfo;
            $this->result['cureRepiceInfo'] = $cureRepiceInfo;
            $this->result['pirntCureRecordInfo'] = $cureRecord;
            $this->result['totalPrice'] = Common::num($totalPrice);
            $this->result['triageInfo'] = $triageInfo;
            $this->result['firstCheck'] = Html::encode($firstCheck);
            $this->result['allergy'] = $allergy;
        }
        return $this->result;
    }

    /**
     * @return 返回该就诊记录的处方记录信息
     * @param 就诊流水id $id
     * @param 包含的处方id $recipeIds
     */
    protected function findRecipeRecordDataProvider($id, $recipeIds = null) {
        $query = new Query();
        $query->from(['a' => RecipeRecord::tableName()]);
        $query->select(['DISTINCT(a.id)', 'a.recipe_id', 'a.name','a.product_name', 'a.unit', 'a.medicine_description_id', 'a.price', 'a.dosage_form', 'a.dose', 'a.used', 'a.frequency', 'a.day', 'a.num', 'a.description', 'a.type', 'a.status', 'a.specification', 'a.skin_test_status', 'a.skin_test','a.drug_type', 'a.dose_unit as r_dose_unit', 'a.curelist_id', 'a.high_risk', 'c.dose_unit as l_dose_unit', 'c.manufactor', 'a.remark', 'd.cure_result as cureResult', 'd.name as cureName', 'd.status as cureStatus', 'e.status as cureChargeStatus', 'a.package_record_id']);
        $query->leftJoin(['c' => RecipeList::tableName()], '{{a}}.recipe_id = {{c}}.id');
        $query->leftJoin(['d' => CureRecord::tableName()], '{{a}}.cure_id = {{d}}.id');
        $query->leftJoin(['e' => ChargeInfo::tableName()], '{{a}}.cure_id = {{e}}.outpatient_id AND {{e}}.type = 3');
        $query->where(['a.record_id' => $id, 'a.spot_id' => $this->spotId]);

        $query->andFilterWhere(['a.id' => $recipeIds]);


        $result = $query->all();
        foreach ($result as &$v) {
            $v['displayName'] = empty($v['specification']) ? $v['name'] : $v['name'] . '(' . $v[specification] . ')';
        }

        return $result;
    }

    /**
     *  doctor-recipe-list
     *  @param int $record_id 就诊流水id
     *  @return int errorCode 错误代码 （0-成功 1001-参数错误 1002-医生没有开处方）
     *  @return string msg 错误信息
     *  @return array recipeList 该条流水的所有处方记录
     * * @desc 返回该条流水的所有处方记录
     */
    public function actionDoctorRecipeList() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $recordId = Yii::$app->request->post('record_id');
        if (empty($recordId)) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '记录不存在';
        } else {
            $query = new Query();
            $query->from(['a' => RecipeRecord::tableName()]);
            $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id ={{b}}.id');
            $query->where(['a.spot_id' => $this->spotId, 'a.record_id' => $recordId]);
            $fields = [
                'name',
                'dose',
                'specification',
                'dose_unit',
                'used',
                'frequency',
                'day',
                'num',
                'unit'
            ];
            $query->select($fields);
            $result = $query->all();
            if (empty($result)) {
                $this->result['errorCode'] = 1002;
                $this->result['msg'] = '医生您还没有开处方';
            } else {
                foreach ($result as $key => &$value) {
                    $value['dose_unit'] = RecipeList::$getDoseUnit[$value['dose_unit']];
                    $value['used'] = RecipeList::$getDefaultUsed[$value['used']];
                    $value['frequency'] = RecipeList::$getDefaultConsumption[$value['frequency']];
                    $value['unit'] = RecipeList::$getUnit[$value['unit']];
                    $value['specification'] = $value['specification'] ? '(' . $value['specification'] . ')' : '';
                }
                $this->result['recipeList'] = $result;
            }
        }
        return $this->result;
    }

    /**
     * @param $id 病历id
     * @return 病历tab打印接口
     */
    protected function recordPrinkInfo($id) {
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
        $userInfo = Patient::getUserInfo($id);
        $userInfo['diagnosis_time'] = date('Y-m-d H时i分', $userInfo['diagnosis_time_timestamp']); //接诊时间;
        $userInfo['end_time'] = ($userInfo['end_time'] != 0 ? (date('Y-m-d H时i分', $userInfo['end_time'])) : '');
        $result['userInfo'] = $userInfo;
        $result['spotInfo'] = $soptInfo;
        $result['repiceInfo'] = $repiceInfo;
        $result['recipeRecordDataProvider'] = $recipeRecordDataProvider;
        $result['outpatientInfo'] = $outpatientInfo;
        $result['firstCheck'] = Html::encode($firstCheck);
        $result['allergy'] = $allergy;
        return $result;
    }

    /**
     * 　@param $id 病历id
     * @return 口腔打印
     */
    protected function teethPrintInfo($recordId) {
        $baseDentalInfo = DentalHistory::find()->select(['type', 'chiefcomplaint', 'historypresent', 'pasthistory', 'returnvisit', 'advice', 'remarks'])->where(['record_id' => $recordId, 'spot_id' => $this->spotId])->asArray()->one();
        $firstCheck = FirstCheck::getFirstCheckInfo($recordId);
        $allergyInfo = AllergyOutpatient::getAllergyByRecord($recordId);
        $dataDentalRelation = DentalHistoryRelation::find()->select(['type', 'position', 'content','dental_disease'])->where(['record_id' => $recordId, 'record_type' => $baseDentalInfo['type'], 'spot_id' => $this->spotId])->asArray()->all();

        $rowsDefault = [
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
            6 => '',
        ];
        foreach ($dataDentalRelation as $v) {
            $positionArray = explode(',', $v['position']);
            $rows[$v['type']][] = [
                'leftTop' => $positionArray[0],
                'rightTop' => $positionArray[1],
                'rightBottom' => $positionArray[2],
                'leftBottom' => $positionArray[3],
                'content' => $v['content'],
                'dental_disease' => $v['dental_disease'],
            ];
        }

        $baseDentalInfo['firstCheck'] = Html::encode($firstCheck);
        $userInfo = Patient::getUserInfo($recordId);
        $userInfo['temperature'] = $userInfo['temperature'] ? $userInfo['temperature'] . '℃ - ' . TriageInfo::$temperature_type[$userInfo['temperature_type']] : '';
        $userInfo['diagnosis_time'] = date('Y-m-d H时i分', $userInfo['diagnosis_time_timestamp']);//接诊时间
        $userInfo['end_time'] = ($userInfo['end_time'] != 0 ? (date('Y-m-d H时i分', $userInfo['end_time'])): '');
        $result['userInfo'] = $userInfo;
        $result['dentalBaseInfo'] = $baseDentalInfo;
        $result['dentalRelation'] = $rows ? $rows : $rowsDefault;
        $result['spotInfo'] = Spot::getSpot();
        $result['firstCheck'] = Html::encode($firstCheck);
        $result['allergyInfo'] = $allergyInfo[$recordId];
        return $result;
    }

    /**
     *  doctor-record-data
     *  @param int $record_id 就诊流水id
     *  @return int errorCode 错误代码 （0-成功 1001-参数错误）
     *  @return string msg 错误信息
     *  @return array 病历信息
     * * @desc 返回病历信息
     */
    public function actionGetDoctorRecordData() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $recordId = Yii::$app->request->post('record_id');
        if (empty($recordId)) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '记录不存在';
            return $this->result;
        }
        $recordType = Report::find()->select(['id', 'record_type'])->where(['record_id' => $recordId, 'spot_id' => $this->spotId])->asArray()->one()['record_type'];
        $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
        if (4 == $recordType || 5 == $recordType) {
            $this->result['data'] = $this->teethPrintInfo($recordId);
            $this->result['data']['recordType'] = $recordType;
        } else if (7 == $recordType) {
            $this->result['data'] = $this->orthodonticsReturnvisitData($recordId);
            $this->result['data']['recordType'] = $recordType;
        } else if (6 == $recordType) {//正畸 初诊
            $this->result['data'] = OrthodonticsFirstRecord::orthodonticsFirstData($recordId);
            $this->result['data']['recordType'] = $recordType;
        } else {
            $this->result['data'] = $this->recordPrinkInfo($recordId);
            $this->result['data']['recordType'] = $recordType;
        }
        $this->result['data']['spotConfig'] = $spotConfig;
        return $this->result;
    }

    /**
     *  doctor-check-inspect-list
     *  @param int $record_id 就诊流水id
     *  @return int errorCode 错误代码 （0-成功 1001-参数错误 1002-医生未开实验室检查或影像学检查 1003-实验室检查或影像学检查未出结果）
     *  @return string msg 错误信息
     *  @return array 该条就诊记录的医生开的实验室检查项目，实验室检查项目关联的检验项目，影像学检查项目
     * * @desc 返回该条就诊记录的医生开的实验室检查项目，实验室检查项目关联的检验项目，影像学检查项目信息
     */
    public function actionDoctorCheckInspectList() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $recordId = Yii::$app->request->post('record_id');
        if (empty($recordId)) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '记录不存在';
        } else {
            //获取所有状态下的影像学检查
            $allCheckData = CheckRecord::find()->select(['id', 'status', 'name'])->where(['record_id' => $recordId, 'spot_id' => $this->spotId])->asArray()->all();
            //获取所有状态下的实验室检查
            $allInspectData = InspectRecord::find()->select(['id', 'status', 'name'])->where(['record_id' => $recordId, 'spot_id' => $this->spotId])->asArray()->all();
            if (empty($allCheckData) && empty($allInspectData)) { //医生未开检查项
                $this->result['errorCode'] = 1002;
                $this->result['msg'] = '您还没有开实验室检查和影像学检查，暂时不能导入';
            } else {
                //判断是否有检查未出结果
                $undoCheckStatus = array_column($allCheckData, 'status');
                $undoInspectStatus = array_column($allInspectData, 'status');
                if (!empty(array_diff($undoCheckStatus, [1])) || !empty(array_diff($undoInspectStatus, [1]))) { //存在未完成的检查
                    $this->result['errorCode'] = 1003;
                    $this->result['msg'] = '您有检查还没有出结果，暂时不能导入最新的检查结果';
                } else {
                    //此时检查为且在且已全部检查完成
                    $doneInspectList = [];
                    $inspectListId = [];
                    if (!empty($allInspectData)) {
                        $inspectListId = array_column($allInspectData, 'id');
                        foreach ($allInspectData as $key => $value) {
                            $doneInspectList[$value['id']] = $value;
                        }
                    }
                    $inspectUnionList = $this->findInspectUnionData($inspectListId);
                    $this->result['data']['check'] = $allCheckData;
                    $this->result['data']['inspect'] = $doneInspectList;
                    $this->result['data']['inspectUnion'] = $inspectUnionList;
                }
            }
        }
        return $this->result;
    }

    /**
     * @param array $id 门诊患者-实验室检查信息表id
     * @return array 返回实验室关联项目
     */
    protected function findInspectUnionData($id) {
        $inspectUnionData = InspectRecordUnion::find()->select(['id', 'name', 'unit', 'reference', 'result', 'inspect_record_id', 'result_identification'])->where(['inspect_record_id' => $id, 'spot_id' => $this->spotId])->asArray()->all();
        $inspectUnionDataList = [];
        if (!empty($inspectUnionData)) {
            foreach ($inspectUnionData as $key => $value) {
                $inspectUnionDataList[$value['inspect_record_id']][] = $value;
            }
        }
        return $inspectUnionDataList;
    }

    /**
     * nurse-print
     * @param int $id 流水id
     * @return string title 标题
     * @return array content 内容
     * @throws NotFoundHttpException
     * @desc 收费打印弹窗页面
     */
    public function actionNursePrintList($id) {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $nursePrintList = [
//                ['id' => '1', 'name' => '通用病历'],
                ['id' => '2', 'name' => '儿童保健档案']
            ];
            return [
                'title' => "请选择病历",
                'content' => $this->renderAjax('@nursePrintListView', [
                    'printList' => $nursePrintList,
                    'recordId' => $id,
                ]),
                'footer' => Html::button('打印', ['class' => 'btn btn-default btn-form btn-nurse-list-print', 'name' => 'nurse-list-print' . $id . 'myshow'])
            ];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     *  doctor-record-data
     *  @param int $record_id 就诊流水id
     *  @return int errorCode 错误代码 （0-成功 1001-参数错误）
     *  @return string msg 错误信息
     *  @return array 儿童保健病历信息
     * * @desc 返回儿童保健病历信息
     */
    public function actionGetChildRecordData() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $recordId = Yii::$app->request->post('record_id');
        $selectId = Yii::$app->request->post('selectId');
        if (empty($recordId)) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '记录不存在';
            return $this->result;
        }
        $recordInfo = [];
        $childRecordInfo = [];
        if (in_array(1, $selectId)) {//通用病历
            $recordInfo = $this->recordPrinkInfo($recordId);
        }
        if (in_array(2, $selectId)) {//儿童保健
            $childRecordInfo = $this->childPrinkInfo($recordId);
        }
        $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
        $this->result['data'] = array_merge($recordInfo, $childRecordInfo);
        $this->result['data']['spotConfig'] = $spotConfig;
        return $this->result;
    }

    /*
     * get-child-info-data
     * @param int $record_id 就诊流水id
     * @return array 儿童保健病历信息
     * @desc 医生门诊获取儿童保健打印数据
     */

    public function actionGetChildInfoData() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $recordId = Yii::$app->request->post('record_id');
        if (empty($recordId)) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '记录不存在';
            return $this->result;
        }
        $this->result['data'] = $this->childPrinkInfo($recordId);
        $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
        $this->result['data']['spotConfig'] = $spotConfig;
        return $this->result;
    }

    /**
     * record_id string 就诊流水id
     * @return string 儿童检查记录
     */
    protected function childPrinkInfo($record_id) {
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
        $query->leftJoin(['r' => Report::tableName()], '{{a}}.id = {{r}}.record_id');
        $query->where(['a.id' => $record_id, 'a.spot_id' => $this->spotId]);
        $childExaminationInfo = $query->one();

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

        $allergy = AllergyOutpatient::getAllergyByRecord($record_id);
        $allergy = isset($allergy) ? $allergy[$record_id] : [];
        if (!empty($allergy)) {
            foreach ($allergy as $key => $value) {
                $allergy[$key] = Html::encode($value);
            }
        }

        $result['userInfo'] = Patient::getUserInfo($record_id);
        $result['userInfo']['ageAssessment'] = Patient::dateDiffage($result['userInfo']['birthtime'], $childExaminationInfo['reportTime']); //测评年龄
        $result['userInfo']['end_time'] = ($result['userInfo']['end_time'] != 0 ? (date('Y-m-d H时', $result['userInfo']['end_time']) . ((int) date('i', $result['userInfo']['end_time'])) . '分') : ''); //结束就诊时间
        $result['userInfo']['diagnosis_time'] = date('Y-m-d H时', $result['userInfo']['diagnosis_time_timestamp']) . ((int) date('i', $result['userInfo']['diagnosis_time_timestamp'])) . '分'; //接诊时间
        $result['spotInfo'] = $spotInfo;
        $result['childBasicConfig']['summary'] = ChildExaminationAssessment::$getSummary;
        $result['childBasicConfig']['communicate'] = ChildExaminationAssessment::$getCommunicate;
        $result['childBasicConfig']['type'] = ChildExaminationCheck::$getType;
        $result['childExaminationInfo'] = $childExaminationInfo;
        $result['allergy'] = $allergy;
        return $result;
    }

    /**
     * @return 医疗耗材打印接口
     */
    public function actionConsumablesPrinkInfo($id) {
        $request = Yii::$app->request;
        if (Yii::$app->request->isAjax) {
            if ($request->isGet) {
                $model = new ConsumablesRecord();
                $consumablesRecordDataProvider = ConsumablesRecord::getDataProvider($id);
                $dataProvider = $this->formatDataProvider($consumablesRecordDataProvider->query);
                Yii::$app->response->format = Response::FORMAT_JSON;
                $model->name = array_column($dataProvider, 'id');
                return [
                    'title' => "打印医疗耗材清单",
                    'content' => $this->renderAjax('@outpatientCheckRecipeApplicationView', [
                        'model' => $model,
                        'dataProvider' => $dataProvider,
                        'title' => '医疗耗材',
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确定', ['class' => 'btn btn-default btn-form btn-consumables-check-application-print ',  'data-dismiss' => "modal",'name' => $id . 'consumables-myshow'])
                ];
            } else {
                $consumablesId = Yii::$app->request->post('consumablesId');
                Yii::$app->response->format = Response::FORMAT_JSON;
                //未勾选直接返回错误。
                if (empty($consumablesId)) {
                    $this->result['errorCode'] = 1001;
                    $this->result['msg'] = '请勾选要打印的项目';
                    return $this->result;
                }

                $pharmcyRecordModel = new PharmacyRecord();
                $pharmcyRepiceInfo = $pharmcyRecordModel->getRepiceInfo($id, ChargeInfo::$consumablesType);
                !$pharmcyRepiceInfo['time'] && $pharmcyRepiceInfo['time'] = '';
                $spotInfo = Spot::getSpot();
                $dataProvider = ConsumablesRecord::getDataProvider($id, $consumablesId);
                $consumablesRecordDataProvider = $this->formatDataProvider($dataProvider->query);
                if ($consumablesRecordDataProvider) {
                    $totalPrice = '';
                    foreach ($consumablesRecordDataProvider as $key => &$v) {
                        $v['single_total_price'] = Common::num($v['num'] * $v['price']);
                        $totalPrice += $consumablesRecordDataProvider[$key]['single_total_price'];
                    }
                }
                $spotConfig = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
                $this->result['spotConfig'] = $spotConfig;
                $this->result['spotInfo'] = $spotInfo;
                $this->result['PharmcyRepiceInfo'] = $pharmcyRepiceInfo;
                $this->result['consumablesRecordDataProvider'] = $consumablesRecordDataProvider;
                $this->result['totalPrice'] = Common::num($totalPrice);

                return $this->result;
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    protected function formatDataProvider($query) {
        $data = $query->asArray()->all();
        if (!empty($data)) {
            foreach ($data as &$v) {
                $v['displayName'] = empty($v['specification']) ? $v['name'] : $v['name'] . '(' . $v['specification'] . ')';
            }
        }
        return $data;
    }

    //口腔牙位图 得到选择了的类型
    public function actionOutpatientMarkType($recordId) {
        if (Yii::$app->request->isAjax) {
            $hasMarkTypeList = DentalHistoryRelation::getHasMarkType($recordId);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "请选择要打印的牙位图",
                'content' => $this->renderAjax('@outpatientTeethImgSelectView', [
                    'hasMarkTypeList' => $hasMarkTypeList,
                    'recordId' => $recordId,
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                Html::button('确定', ['class' => 'btn btn-default btn-form btn-teeth-confirm teeth-print','data-dismiss' => "modal",'name' => 'teethPrint' . $recordId])
            ];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /*
     * 正畸复诊病历打印数据
     */

    public function actionGetOrthodonticsReturnvisitRecord() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $recordId = Yii::$app->request->post('record_id');
        if (empty($recordId)) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '记录不存在';
            return $this->result;
        }

        $this->result['data'] = $this->orthodonticsReturnvisitData($recordId);
        return $this->result;
    }

    /*
     * 正畸复诊病历获取数据
     */

    public function orthodonticsReturnvisitData($recordId) {
        $soptInfo = Spot::getSpot();
        $firstCheck = FirstCheck::getFirstCheckInfo($recordId);
        $allergy = AllergyOutpatient::getAllergyByRecord($recordId);
        $allergy = isset($allergy) ? $allergy[$recordId] : [];
        if (!empty($allergy)) {
            foreach ($allergy as $key => $value) {
                $allergy[$key] = Html::encode($value);
            }
        }
        $result['baseInfo'] = OrthodonticsReturnvisitRecord::getData($recordId);
        $result['baseInfo']['check'] = nl2br(Html::encode($result['baseInfo']['check']));
        $userInfo = Patient::getUserInfo($recordId);
        $userInfo['temperature'] = $userInfo['temperature'] ? $userInfo['temperature'] . '℃ - ' . TriageInfo::$temperature_type[$userInfo['temperature_type']] : '';
        $userInfo['weightkg'] = $userInfo['weightkg'] ? $userInfo['weightkg'] . 'kg' : '';
        $userInfo['end_time'] = ($userInfo['end_time'] != 0 ? (date('Y-m-d H时', $userInfo['end_time']) . ((int) date('i', $userInfo['end_time'])) . '分') : '');
        $userInfo['diagnosis_date'] = $userInfo['diagnosis_time']; //接诊日期
        $userInfo['diagnosis_time'] = date('Y-m-d H时', $userInfo['diagnosis_time_timestamp']) . ((int) date('i', $userInfo['diagnosis_time_timestamp'])) . '分'; //接诊时间
        $result['userInfo'] = $userInfo;
        $result['spotInfo'] = Spot::getSpot();
        $result['spotConfig'] = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
        ;
        $result['firstCheck'] = Html::encode($firstCheck);
        $result['allergy'] = $allergy;
        return $result;
    }
    
    
    /**
     * @desc 返回医生门诊-实验室检查tab信息
     * @param integer $id 就诊记录ID
     */
    public function actionGetInspectRecord($id){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* 实验室检查 */
        $inspectRecordDataProvider = $this->findInspectRecordDataProvider($id);
        $inspectRecordDone = InspectRecord::find()->select(['id'])->where(['record_id' => $id,'spot_id' => $this->spotId,'package_record_id'=>0,'status' => [1,2]])->asArray()->one();
        $inspectBackStatus = empty($inspectRecordDone) ? 0 : 1;
        $inspectRecordModel = new InspectRecord();
        
        $patientOtherInfo = [
            'firstCheckCount' => FirstCheck::getCount($id),
            'weightkg' => $weightkg,
        ];
        $chargeInfoList = ChargeInfo::getList($id,['outpatient_id'],['status' => [1, 2],'type' => ChargeInfo::$inspectType]);
        $inspectTemplateMenu = InspectTemplate::getInspectTemplateMenu();
        
        return $this->renderAjax('@outpatientInspectRecordFormView', [
            'model' => $inspectRecordModel,
            'inspectRecordDataProvider' => $inspectRecordDataProvider,
            'chargeInfoList' => $chargeInfoList ? array_column($chargeInfoList, 'outpatient_id') : [],
            'patientOtherInfo' => $patientOtherInfo,
            'inspectBackStatus' => $inspectBackStatus,
            'inspectTemplateMenu' => json_encode($inspectTemplateMenu,true)
        ]);
    }
    /**
     * @desc 返回医生门诊-影像学检查tab信息
     * @param integer $id 就诊记录ID
     */
    public function actionGetCheckRecord($id){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $checkRecordProvider = $this->findCheckRecordProvider($id);
        //            $checkList = CheckList::find()->select(['id', 'name', 'unit', 'price', 'tag_id'])->where(['spot_id' => $this->parentSpotId, 'status' => 1])->indexBy('id')->asArray()->all();
        $checkRecordModel = new CheckRecord();
        $chargeInfoList = ChargeInfo::getList($id,['outpatient_id'],['status' => [1, 2],'type' => ChargeInfo::$checkType]);
        $patientOtherInfo = [
            'firstCheckCount' => FirstCheck::getCount($id),
            'weightkg' => $weightkg,
        ];
        $checkTemplateMenu = CheckTemplate::getCheckTemplateMenu();
        return $this->renderAjax('@outpatientCheckRecordFormView', [
            'model' => $checkRecordModel,
            'checkRecordProvider' => $checkRecordProvider,
            'chargeInfoList' => $chargeInfoList ? array_column($chargeInfoList, 'outpatient_id') : [],
            'patientOtherInfo' => $patientOtherInfo,
            'checkTemplateMenu' => json_encode($checkTemplateMenu,true),
        ]);
    }
    /**
     * @desc 返回医生门诊-治疗tab信息
     * @param integer $id 就诊记录ID
     */
    public function actionGetCureRecord($id){
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $cureRecordDataProvider = $this->findCureRecordDataProvider($id); //治疗
        $cureRecordModel = new CureRecord(); //治疗
        
        $chargeInfoList = ChargeInfo::getList($id,['type', 'outpatient_id', 'status'],['status' => [1, 2],'type' => ChargeInfo::$cureType]);
        $patientOtherInfo = [
            'firstCheckCount' => FirstCheck::getCount($id),
            'weightkg' => $weightkg,
        ];
        $cureTemplateMenu = CureTemplate::getCureTemplateMenu();
        
        return $this->renderAjax('@outpatientCureRecordFormView', [
            'model' => $cureRecordModel,
            'cureRecordDataProvider' => $cureRecordDataProvider,
            'chargeInfoList' => $chargeInfoList ? array_column($chargeInfoList, 'outpatient_id') : [],
            'patientOtherInfo' => $patientOtherInfo,
            'cureTemplateMenu' => json_encode($cureTemplateMenu,true),
        ]);
    }
    
    /**
     * @desc 返回医生门诊-处方tab信息
     * @param integer $id 就诊记录ID
     */
    public function actionGetRecipeRecord($id){
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $recipeRecordDataProvider = $this->findRecipeRecordDataProvider($id);
        $recipeRecordModel = new RecipeRecord();
        $nowTotalNums = [];
        
        //获取四大医嘱模板
        $recipeTemplateMenu = $this->getRecipeTemplateMenu();
        /* 循环判断和获取对应的剂量单位 */
        foreach ($recipeRecordDataProvider as $key => $val) {
            $l_dose_unit = explode(',', $val['l_dose_unit']);
            $unit_num = count($l_dose_unit);
            $all_dose_unit = array();
            if ($unit_num != 1) {
                $all_dose_unit[''] = '';
            }
            if ($val['status'] == 3) {
                if ($val['type'] == 1) {
                    $nowTotalNums[$val['recipe_id']][] = $val['num'];
                }
            }
            foreach ($l_dose_unit as $vals) {
                $all_dose_unit[$vals] = RecipeList::$getDoseUnit[$vals];
            }
            
            $recipeRecordDataProvider[$key]['l_dose_unit'] = $all_dose_unit; //剂量单位(可编辑状态)
        }
        /* 处方的库存数量 */
        $recipeTotalNumsList = $this->getRecipeTotal();
        /* 已被占用的库存数量 */
        $recipeUsedTotalNums = [];
        $recipeUsedTotalNumsList = RecipeRecord::find()->select(['id', 'recipe_id', 'num'])->where(['spot_id' => $this->spotId, 'type' => 1])->andWhere('status = 3')->andWhere('record_id != :record_id', [':record_id' => $id])->asArray()->all();
        if ($recipeUsedTotalNumsList) {
            foreach ($recipeUsedTotalNumsList as $v) {
                $recipeUsedTotalNums[$v['recipe_id']][] = $v['num'];
            }
        }
        
        $chargeInfoList = ChargeInfo::getList($id,['outpatient_id'],['status' => [1, 2],'type' => ChargeInfo::$recipeType]);
        $weightkg = TriageInfo::getFieldsList($id,['weightkg']);
        $weightkg = ($weightkg['weightkg'] == NULL) ? FALSE : TRUE; //体重是否填写
        $patientOtherInfo = [
            'firstCheckCount' => FirstCheck::getCount($id),
            'weightkg' => $weightkg,
        ];
        $recipeTemplateMenu = $this->getRecipeTemplateMenu();
        /** 是否已有发药记录 * */
        $recipeBackData = RecipeBatch::find()->where(['record_id' => $id, 'spot_id' => $this->spotId])->count(1);
        $recipeBackStatus = empty($recipeBackData) ? 0 : 1;
        return $this->renderAjax('@outpatientRecipeRecordFormView', [
            'model' => $recipeRecordModel,
            'recipeRecordDataProvider' => $recipeRecordDataProvider,
            'recipeTotalNumsList' => $recipeTotalNumsList,
            'recipeUsedTotalNums' => $recipeUsedTotalNums,
            'chargeInfoList' => $chargeInfoList ? array_column($chargeInfoList, 'outpatient_id') : [],
            'nowTotalNums' => $nowTotalNums,
            'recipeBackStatus' => $recipeBackStatus,
            'patientOtherInfo' => $patientOtherInfo,
            'recipeTemplateMenu' => json_encode($recipeTemplateMenu,true),
            
        ]);
    }
    /**
     * @desc 返回医生门诊-医疗耗材tab信息
     * @param integer $id 就诊记录ID
     */
    public function actionGetConsumablesRecord($id){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $consumablesRecordDataProvider = ConsumablesRecord::getDataProvider($id);
        $consumablesRecordModel = new ConsumablesRecord();
        $consumablesTotal = ConsumablesStockInfo::getTotal();
        $chargeInfoList = ChargeInfo::getList($id,['outpatient_id'],['status' => [1, 2],'type' => ChargeInfo::$consumablesType]);
        
        return $this->renderAjax('@outpatientConsumablesRecordFormView', [
            'model' => $consumablesRecordModel,
            'consumablesRecordDataProvider' => $consumablesRecordDataProvider,
            'chargeInfoList' => $chargeInfoList ? array_column($chargeInfoList, 'outpatient_id') : [],
            'consumablesTotal' => $consumablesTotal,
        ]);
    }
    /**
     * @desc 返回医生门诊-其他tab信息
     * @param integer $id 就诊记录ID
     */
    public function actionGetMaterialRecord($id){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $materialList = Material::getList(['id', 'name','meta','manufactor', 'price', 'tag_id', 'specification', 'unit', 'attribute'], ['status' => 1]);
        $materialRecordDataProvider = MaterialRecord::getDataProvider($id);
        $materialRecordModel = new MaterialRecord();
        $materialTotal = MaterialStockInfo::getTotal();
        $chargeInfoList = ChargeInfo::getList($id,['outpatient_id'],['status' => [1, 2],'type' => ChargeInfo::$consumablesType]);
        
        return $this->renderAjax('@outpatientMaterialRecordFormView', [
            'model' => $materialRecordModel,
            'materialList' => $materialList,
            'materialRecordDataProvider' => $materialRecordDataProvider,
            'chargeInfoList' => $chargeInfoList ? array_column($chargeInfoList, 'outpatient_id') : [],
            'materialTotal' => $materialTotal
        ]);
    }
    /**
     * @desc 返回医生门诊-报告tab信息
     * @param integer $id 就诊记录ID
     */
    public function actionGetReportRecord($id){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $checkRecordModel = new CheckRecord();
        $status = [1, 2, 3];
        $inspectCheckList = \app\modules\outpatient\models\Report::reportData($id, $reportType = 3, $status);
        if(empty($inspectCheckList)){
            return $this->renderPartial('@outpatientReportRecordFormView', [
                'checkRecordModel' => $checkRecordModel,
                'inspectCheckList' => $inspectCheckList,
            ]);
        }
        return $this->renderAjax('@outpatientReportRecordFormView', [
            'checkRecordModel' => $checkRecordModel,
            'inspectCheckList' => $inspectCheckList,
        ]);
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
                $val['inspectItem'] = \app\modules\outpatient\models\InspectRecordUnion::getInspectItem($val['id']);
            }
        }
        return $inspectRecord;
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
     
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
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
     *  get-check-list
     *  @return array
     * @desc 获取影像学检查医嘱
     */
    public function actionGetCheckList() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $search = Yii::$app->request->get('search');
        $data =  (new Query())
            ->select([
                "a.id", "a.spot_id", "a.check_id", "a.price", "a.default_price", "a.status",
                "b.name", "b.unit", "b.meta", "b.remark", "b.tag_id", "b.international_code"
            ])
            ->from(["a" => CheckListClinic::tableName()])
            ->leftJoin(["b" => CheckList::tableName()], "a.check_id = b.id")
            ->where(["a.spot_id" => $this->spotId, "a.status" => 1])
            ->andWhere(['like','b.name',$search])
            ->all();
        $this->result['list'] = $data;
        return $this->result;
    }
    
}
