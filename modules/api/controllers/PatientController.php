<?php

namespace app\modules\api\controllers;

use app\modules\make_appointment\models\Appointment;
use app\modules\patient\models\PatientRecord;
use app\modules\spot\models\Spot;
use app\modules\user\models\User;
use Yii;
use app\modules\patient\models\Patient;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Json;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\modules\outpatient\models\Report;
use yii\web\Response;
use app\modules\outpatient\models\CheckRecord;
use app\modules\triage\models\TriageInfo;
use app\modules\triage\models\NursingRecord;
use app\modules\triage\models\HealthEducation;
use app\modules\outpatient\models\InspectRecord;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\outpatient\models\DentalHistory;
use app\modules\outpatient\models\FirstCheck;
use app\modules\outpatient\models\AllergyOutpatient;
use app\modules\triage\models\ChildAssessment;
use app\modules\patient\models\PatientAllergy;
use app\modules\follow\models\search\FollowSearch;
use app\modules\charge\models\search\ChargeRecordLogSearch;
use app\specialModules\recharge\models\search\CardRechargeSearch;
use app\specialModules\recharge\models\search\MembershipPackageCardSearch;
use app\specialModules\recharge\models\search\UserCardSearch;
use yii\helpers\Url;
use app\common\Common;
use yii\db\Exception;

/**
 *
 * @author 张震宇
 * @property 患者接口API
 */
class PatientController extends CommonController
{
    public function behaviors()
    {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-patients' => ['post'],
                    'get-iphone' => ['post'],
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    /**
     * @param int $patientName 患者名称
     * @return array data 患者信息
     * @desc 通过患者姓名来获取对应的患者基本信息卡片
     */
    public function actionGetPatients()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $patientName = Yii::$app->request->post('patientName', null);
        if ($patientName) {
            $this->result['data'] = Patient::find()->select(['id', 'username', 'iphone', 'head_img', 'sex'])->where(['spot_id' => $this->parentSpotId])->andWhere(['like', 'username', $patientName])->asArray()->all();
        }
        return $this->result;
    }

    public function actionGetIphone()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $patientIphone = Yii::$app->request->post('patientIphone', null);
        $oldPatient = Yii::$app->request->post('oldPatient',2);//是否只想拉取患者编号患者
        $where = [];
        if($oldPatient == 1){
            $where = ['!=','patient_number','0000000'];
        }
        if($patientIphone){
            $data = Patient::find()->select(['id', 'username', 'iphone', 'head_img', 'sex','birthday'])->where(['spot_id' => $this->parentSpotId])->andWhere(['like', 'iphone', $patientIphone])->andFilterWhere($where)->asArray()->all();
            if(!empty($data)){
                foreach ($data as &$value){
                    $value['birthday'] = Patient::dateDiffage($value['birthday'],time());
                    $value['sexText'] = Patient::$getSex[$value['sex']];   
                }
            }
            $this->result['data'] = $data;
        }

        return $this->result;
    }
    
    
    /**
     * @desc 返回患者就诊信息记录
     * @param integer $id 患者ID
     * @return string
     */
    public function actionGetPatientRecord($id){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $historyPatientInfo = Patient::findTriageRecord($id);
        $recordIdArr = [];
        foreach ($historyPatientInfo as $key => $val) {
            $historyPatientInfo[$key]['bmi'] = Patient::getBmi($val['heightcm'], $val['weightkg']);
            $historyPatientInfo[$key]['bloodtype'] = TriageInfo::$bloodtype[$val['bloodtype']];
            $recordIdArr[] = $val['recordId'];
        }
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
        
        return $this->renderAjax('@patientIndexInformationView',[
            
            'historyPatientInfo' => $historyPatientInfo,
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
            'assessment' => $assessment,
        ]);
    }

    public function actionGetFollowRecord($id){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $followSearch = new FollowSearch();
        $followDataProvider = $followSearch->search(Yii::$app->request->queryParams, $this->pageSize, $id);
        $countData = $followSearch->getFollowStateCount(null, $id);
        return $this->renderAjax('@patientIndexFollowView',[
            'followSearch' => $followSearch,
            'followDataProvider' => $followDataProvider,
            'countData' => $countData,
        ]);
    }

    /**
     * @param $id  患者ID
     * @return  患者收费信息视图
     */
    public function actionGetChargeRecord($id){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $patientChargeSearch = new ChargeRecordLogSearch();
        $patientChargeSearch->patient_id = $id;
        $chargeDataProvider = $patientChargeSearch->searchByPatient(Yii::$app->request->queryParams, $this->pageSize);
        return $this->renderAjax('@patientIndexChargeInfoView',[
            'patientChargeSearch' => $patientChargeSearch,
            'chargeDataProvider' => $chargeDataProvider,
            'spotId' => $this->spotId
        ]);
    }
    /**
     * @param $id  患者ID
     * @return  患者预约信息
     */
    public function actionGetAppointmentRecord($id){
        Yii::$app->response->format=Response::FORMAT_JSON;
        $appointmentDataProvider=$this->findAppointmentRecord($id);
        return $this->renderAjax('@patientIndexAppointmentView',[
            'appointmentDataProvider' => $appointmentDataProvider,
            'spotId' => $this->spotId

        ]);
    }
    /**
     * @param integer $id 患者ID
     * @desc 患者会员卡信息
     */
    public function actionGetCardList($id){
        
        Yii::$app->response->format=Response::FORMAT_JSON;
        $iphone = Patient::getPatientInfo($id,['iphone'])['iphone'];
        //充值卡
        $searchModel = new CardRechargeSearch();
        $searchModel->f_phone = $iphone;
        $rechargeCardDataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize,false);//充值卡
        $doctorListInfo = [];
        if(!empty($rechargeCardDataProvider->models)){
            $doctorList = [];
            foreach ($rechargeCardDataProvider->models as $v){
                $doctorList[] = $v->f_sale_id;
            }
            $doctorListInfo = User::find()->select(['id','username'])->where(['id' => $doctorList])->indexBy('id')->asArray()->all();
        }
        //套餐卡
        $membershipPackageCardSearchModel = new MembershipPackageCardSearch();
        $membershipPackageCardSearchModel->patient_id = $id;
        $membershipPackageCarddataProvider = $membershipPackageCardSearchModel->search(Yii::$app->request->queryParams, $this->pageSize,false,false);//套餐卡
        
        //服务卡
        $userCardSearchModel = new UserCardSearch();
        $userCardSearchModel->phone = $iphone;
        $userCardDataProvider = $userCardSearchModel->search(Yii::$app->request->queryParams, $this->pageSize,false,false);
        $card_physical_id = [];
        $userCardInfo = [];
        if ($userCardDataProvider->getModels()) {
            foreach ($userCardDataProvider->getModels() as $model) {
                $card_physical_id[] = $model->getAttributes();
            }
        }
        if ($card_physical_id) {
            try {
                $url = Yii::$app->request->getHostInfo() . Url::to(['@cardCenterCardInfoBySn']);
                $userCardInfo = Common::curlPost($url, ['f_card_id' => $card_physical_id]);
                $userCardInfo = $userCardInfo ? json_decode($userCardInfo, true) : '';
            } catch (Exception $exc) {
                $userCardInfo = [];
            }
        }
        
        return $this->renderAjax('@patientIndexCardView',[
            'rechargeCardDataProvider' => $rechargeCardDataProvider,//充值卡
            'doctorListInfo' => $doctorListInfo,
            'membershipPackageCarddataProvider' => $membershipPackageCarddataProvider,//套餐卡
            'userCardDataProvider' => $userCardDataProvider,
            'cardInfo' => $userCardInfo,
            'spotId' => $this->spotId
        ]);
        
    }
    
    
    public function findAppointmentRecord($id){
            $query=(new ActiveQuery(Appointment::className()));
            $query->from(['a'=>Appointment::tableName()]);
            $query->select(['a.id','a.record_id','a.spot_id','a.remarks','a.illness_description', 'a.time','c.type', 'c.type_description','c.status','d.username as doctorName','f.username as appointmentOperator','h.spot_name']);
            $query->leftJoin(['c' => PatientRecord::tableName()], '{{a}}.record_id = {{c}}.id');
            $query->leftJoin(['d' => User::tableName()], '{{a}}.doctor_id = {{d}}.id');
            $query->leftJoin(['f' => User::tableName()], '{{a}}.appointment_operator = {{f}}.id');
            $query->leftJoin(['h'=>Spot::tableName()],'{{a}}.spot_id={{h}}.id');
            $query->andWhere(['a.patient_id'=>$id]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => [
                    
                    'attributes' => ['']
                ],
                'pagination' => [
                    'pageSize' => $this->pageSize
                ],
            ]);
            $query->orderBy(['a.update_time' => SORT_DESC]);
            return $dataProvider;
    }

}
