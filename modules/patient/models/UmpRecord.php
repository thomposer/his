<?php

namespace app\modules\patient\models;

use app\modules\spot_set\models\SpotType;
use Yii;
use app\modules\triage\models\TriageInfo;
use app\modules\report\models\Report;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\outpatient\models\InspectRecord;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\CheckRecord;
use app\modules\charge\models\ChargeRecord;
use yii\db\ActiveQuery;
use app\modules\charge\models\ChargeInfo;
use yii\db\Exception;

/**
 * This is the model class for table "{{%patient_family}}".
 *
 * @property string $id
 * @property string $patient_id
 * @property integer $relation
 * @property string $name
 * @property integer $sex
 * @property string $birthday
 * @property string $iphone
 */
class UmpRecord extends \app\modules\triage\models\TriageInfo
{

    public $appointment_time;
    public $appointment_type;
    public $isEdit = 2;
    public $chargeTime; //收费时间
    public $discountType; //优惠方式
    public $discountPrice; //优惠金额
    public $discountReason; //优惠原因
    public $payType; //支付方式
    public $medicalFee; //诊疗费

    /**
     * @inheritdoc
     */

    public function rules() {
        return [
            [['isEdit'], 'integer'],
            [['diagnosis_time', 'appointment_time', 'doctor_id', 'room_id'], 'required', 'on' => 'appointment'],
            [['diagnosis_time', /* 'appointment_type', */ 'doctor_id', 'room_id'], 'required', 'on' => 'report'],
            [['discountPrice'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
            [['chargeTime', 'payType', 'medicalFee'], 'required', 'on' => 'charge']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'appointment_time' => '预约时间',
            'diagnosis_time' => '接诊日期',
            'appointment_type' => '接诊类型',
            'doctor_id' => '接诊医生',
            'room_id' => '接诊诊室',
            'chargeTime' => '收费时间',
            'discountType' => '优惠方式',
            'discountPrice' => '优惠折扣/金额',
            'discountReason' => '优惠原因',
            'payType' => '支付方式',
            'medicalFee' => '诊疗费（元）',
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['appointment'] = ['diagnosis_time', 'appointment_time', 'doctor_id', 'room_id'];
        $parent['report'] = ['diagnosis_time', 'appointment_type', 'doctor_id', 'room_id'];
        $parent['charge'] = ['chargeTime', 'payType', 'medicalFee', 'discountType', 'discountPrice', 'discountReason'];
        return $parent;
    }

    /**
     * @param type $recordId 就诊流水ID
     * @param type $doctorId 医生ID
     * @return 通过预约补录
     */
    public static function makeupByAppointment($patientId, $recordId, $doctorId, $diagnosisTime, $roomId) {
        //修改预约的就诊记录为结束就诊 并修改为补录
        $record = PatientRecord::updateAll(['status' => 5, 'makeup' => 2], ['id' => $recordId]);
        //分诊表增加记录/修改记录
        $triageInfoModel = TriageInfo::findModel($recordId);
        $triageInfoModel->record_id = $recordId;
        $triageInfoModel->spot_id = self::$staticSpotId;
        $triageInfoModel->doctor_id = $doctorId;
        $triageInfoModel->room_id = $roomId;
        $triageInfoModel->diagnosis_time = strtotime($diagnosisTime);
        $triageInfoModel->save();
        //增加报到数据
        self::addMakeupReport($patientId, $recordId, $doctorId);
        $firstRecord = PatientRecord::getFirstRecord($patientId, $recordId);

        $patientInfo = Patient::findOne(['id' => $patientId]);
        if($patientInfo && $patientInfo->patient_number == '0000000'){
            Patient::updateAll(['first_record' => $firstRecord,'patient_number' => Patient::generatePatientNumber()], ['id' => $patientId, 'spot_id' => self::$staticParentSpotId]);
        }else{
            Patient::updateAll(['first_record' => $firstRecord], ['id' => $patientId, 'spot_id' => self::$staticParentSpotId]);
        }


        //修改病例库的接诊时间
        self::modifyPatientDiagnosisTime($patientId, $diagnosisTime);
    }

    /**
     * @return 通过登记补录
     */
    public static function makeupByReport($patientId, $appointmentType = 1, $doctorId, $diagnosisTime, $isEdit, $recordId, $roomId) {
        if ($isEdit == 2) {
            //添加就诊流水
            $patientRecord = new PatientRecord();
            $patientRecord->spot_id = self::$staticSpotId;
            $patientRecord->patient_id = $patientId;
            $patientRecord->status = 5;
            /* $patientRecord->type = $appointmentType;
              $spotTypeInfo = SpotType::getSpotType(['id' => $appointmentType,'status' => 1])[0];
              $patientRecord->type_description = $spotTypeInfo['name'];
              $patientRecord->type_time = $spotTypeInfo['time']; */
            $patientRecord->makeup = 2;
            $patientRecord->save();
            //添加分诊信息
            $triageInfo = new TriageInfo();
            $triageInfo->record_id = $patientRecord->getAttribute('id');
            $triageInfo->spot_id = self::$staticSpotId;
            $triageInfo->doctor_id = $doctorId;
            $triageInfo->room_id = $roomId;
            $triageInfo->diagnosis_time = strtotime($diagnosisTime);
            $triageInfo->save();
            //增加报到数据
            self::addMakeupReport($patientId, $patientRecord->getAttribute('id'), $doctorId);
        } else {
            $patientRecord = PatientRecord::findOne(['id' => $recordId]);
            /* $patientRecord->type = $appointmentType;
              $spotTypeInfo = SpotType::getSpotType(['id' => $appointmentType,'status' => 1])[0];
              $patientRecord->type_description = $spotTypeInfo['name'];
              $patientRecord->type_time = $spotTypeInfo['time']; */
            $patientRecord->save();
            $triageInfo = TriageInfo::findOne(['record_id' => $recordId]);
            $triageInfo->doctor_id = $doctorId;
            $triageInfo->room_id = $roomId;
            $triageInfo->diagnosis_time = strtotime($diagnosisTime);
            $triageInfo->save();
        }

        $firstRecord = PatientRecord::getFirstRecord($patientId, $recordId);
        $patientInfo = Patient::findOne(['id' => $patientId]);
        if($patientInfo && $patientInfo->patient_number == '0000000'){
            Patient::updateAll(['first_record' => $firstRecord, 'patient_number' => Patient::generatePatientNumber()], ['id' => $patientId, 'spot_id' => self::$staticParentSpotId]);
        }else{
            Patient::updateAll(['first_record' => $firstRecord], ['id' => $patientId, 'spot_id' => self::$staticParentSpotId]);
        }

        //修改病例库的接诊时间
        self::modifyPatientDiagnosisTime($patientId, $diagnosisTime);
    }

    public static function addMakeupReport($patientId, $recordId, $doctorId) {
        $model = Report::find()->where(['record_id' => $recordId])->one();
        if (empty($model)) {
            $model = new Report();
            $model->scenario = 'reportUmp';
            $model->patient_id = $patientId;
            $model->spot_id = self::$staticSpotId;
            $model->record_id = $recordId;
            $model->doctor_id = $doctorId;
            $model->save();
        } else {
            $model->doctor_id = $doctorId;
            $model->save(false);
        }
    }

    /**
     * 
     * @param type $patientId 患者ID
     * @param type $diagnosisTime 接诊时间
     * @return boolean 修改病例库的接诊时间
     */
    public static function modifyPatientDiagnosisTime($patientId, $diagnosisTime) {
        return true;
    }

    /**
     * 
     * @param type $id  流水ID
     * @return RecipeRecord 获取RecipeRecord 的Model
     */
    public static function findRecipeRecord($id) {
        $model = RecipeRecord::findOne(['record_id' => $id]);
        if ($model != null || !empty($model)) {
            $model->billingTime = date('Y-m-d H:i', $model->create_time);
            return $model;
        } else {
            return new RecipeRecord();
        }
    }

    /**
     * 
     * @param type $id 流水ID
     * @return RecipeRecord  获取RecipeRecord 的Model
     */
    public static function findInspectRecord($id) {
        $model = InspectRecord::findOne(['record_id' => $id]);
        if ($model != null || !empty($model)) {
            $model->billingTime = date('Y-m-d H:i', $model->create_time);
            return $model;
        } else {
            return new InspectRecord();
        }
    }

    /**
     * 
     * @param type $id
     * @return 治疗
     */
    public static function findCureRecord($id) {
        $model = CureRecord::findOne(['record_id' => $id]);
        if ($model != null || !empty($model)) {
            $model->billingTime = date('Y-m-d H:i', $model->create_time);
            return $model;
        } else {
            return new CureRecord();
        }
    }

    /**
     * 
     * @param type $id
     * @return 获取CkecRecord的Model
     */
    public static function findCheckRecord($id) {
        $model = CheckRecord::findOne(['record_id' => $id]);
        if ($model != null || !empty($model)) {
            $model->billingTime = date('Y-m-d H:i', $model->create_time);
            return $model;
        } else {
            return new CheckRecord();
        }
    }

    /**
     * 获取补录收费信息
     * public $chargeTime; //收费时间
      public $discountType; //优惠方式
      public $discountPrice; //优惠金额
      public $discountReason; //优惠原因
      public $payType; //支付方式
      public $medicalFee; //诊疗费
     */
    public static function getChargeModel($recordId) {
        $query = new ActiveQuery(self::className());
        $query->from(['t1' => self::tableName()]);
        $res = $query->from(['t1' => PatientRecord::tableName()])
                ->select(['medicalFee' => 't1.price', 'payType' => 't2.type', 'discountType' => 't2.discount_type', 'discountPrice' => 't2.discount_price', 'discountReason' => 't2.discount_reason', 'chargeTime' => 't2.create_time'])
                ->leftJoin(['t2' => ChargeRecord::tableName()], '{{t1}}.id={{t2}}.record_id')
                ->where(['t1.id' => $recordId])
                ->one();
        return $res;
    }

    /**
     * 
     * @param type $model post数据   对象
     * @return 插入收费信息
     */
    public static function saveChargeRecord($model, $patientId, $recordId, $doctorId) {
        //插入到收费记录表中
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            //修改patient_record表中门诊费用
            $patientRecord = PatientRecord::findOne(['id' => $recordId]);
            $oldPrice = $patientRecord->price;
            $patientRecord->price = $model->medicalFee;
            $patientRecord->completed_price = $patientRecord->completed_price + ($model->medicalFee - $oldPrice);
            $patientRecord->save();
            //修改/插入收费记录表 
            $chargeRecord = self::findChargeModel($recordId);
            $chargeInfo = self::findChargeInfoModel($recordId, $chargeRecord->id);
            $chargeRecord->spot_id = static::$staticSpotId;
            $chargeRecord->patient_id = $patientId;
            $chargeRecord->record_id = $recordId;
            if ($model->discountType == 2) {//金额扣减
                $chargeRecord->price = $patientRecord->completed_price - $model->discountPrice;
            } elseif ($model->discountType == 3) {//折扣
                $chargeRecord->price = $patientRecord->completed_price * ($model->discountPrice / 100);
            } else {
                $chargeRecord->price = $patientRecord->completed_price;
            }
//            $chargeRecord->price = $chargeRecord->price ? $chargeRecord->price + ($model->medicalFee - $chargeInfo->unit_price) : $model->medicalFee;
            $chargeRecord->status = 1;
            $chargeRecord->type = $model->payType;
            $chargeRecord->discount_type = $model->discountType;
            if ($model->discountType != 1) {
                $chargeRecord->discount_price = $model->discountPrice;
                $chargeRecord->discount_reason = $model->discountReason;
            } else {
                $chargeRecord->discount_price = 0;
                $chargeRecord->discount_reason = '';
            }
            $isNewRecord = $chargeRecord->isNewRecord;
            $chargeRecord->create_time = strtotime($model->chargeTime);
            $chargeRecord->save();
            if ($isNewRecord) {
                $chargeRecord->create_time = strtotime($model->chargeTime);
                $chargeRecord->save();
            }
            //插入到收费详情表中
            $chargeInfo->charge_record_id = $chargeRecord->id;
            $chargeInfo->outpatient_id = 0;
            $chargeInfo->spot_id = self::$staticSpotId;
            $chargeInfo->record_id = $recordId;
            $chargeInfo->unit_price = $model->medicalFee;
            $chargeInfo->type = 5;
            $chargeInfo->name = '诊疗费';
            $chargeInfo->unit = '次';
            $chargeInfo->num = 1;
            $chargeInfo->doctor_id = $doctorId;
            $chargeInfo->status = 1;
            $chargeInfo->save();
            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
        }
    }

    public static function findChargeModel($recordId) {
        $model = ChargeRecord::findOne(['record_id' => $recordId]);
        if (empty($model) || is_null($model)) {
            $model = new ChargeRecord();
        }
        return $model;
    }

    public static function findChargeInfoModel($recordId, $chargeRecordId) {
        $model = ChargeInfo::findOne(['record_id' => $recordId, 'type' => 5, 'charge_record_id' => $chargeRecordId]);
        if (empty($model) || is_null($model)) {
            $model = new ChargeInfo();
        }
        return $model;
    }

    /**
     * 
     * @param model $triageInfo  
     * @return  同步分诊的患者基本信息到   患者库 
     */
    public static function SyncPatientInformation($triageInfo, $patientId) {
        $patientModel = Patient::findOne(['id' => $patientId, 'spot_id' => self::$staticParentSpotId]);
        $patientModel->heightcm = $triageInfo['heightcm'];
        $patientModel->weightkg = $triageInfo['weightkg'];
        $patientModel->bloodtype = $triageInfo['bloodtype'];
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
        $patientModel->blood_type_supplement = $triageInfo['blood_type_supplement'];
        $patientModel->save();
    }

}
