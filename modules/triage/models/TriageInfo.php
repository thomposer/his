<?php

namespace app\modules\triage\models;

use Yii;
use yii\helpers\Json;
use app\modules\patient\models\PatientRecord;
use app\modules\report\models\Report;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use app\modules\spot_set\models\DoctorRoomUnion;
use app\modules\spot_set\models\Room;
use app\modules\message\models\MessageCenter;

/**
 * This is the model class for table "{{%triage_info}}".
 *
 * @property integer $id
 * @property string $record_id
 * @property date $morbidityDate 发病日期
 * @property integer $incidence_date 发病日期
 * @property decimal $heightcm
 * @property decimal $weightkg
 * @property string $bloodtype
 * @property integer $temperature_type
 * @property string $temperature
 * @property integer $breathing
 * @property integer $pulse
 * @property integer $shrinkpressure
 * @property integer $diastolic_pressure
 * @property integer $oxygen_saturation
 * @property string $pain_score
 * @property string $case_reg_img
 * @property string $examination_check
 * @property string $first_check
 * @property string $cure_idea
 * @property integer doctor_id
 * @property integer room_id
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $diagnosis_time  接诊时间
 * @property  integer $state 是否第一次保存病历 1:是，0：否
 */
class TriageInfo extends \app\common\base\BaseActiveRecord
{

    public $morbidityDate; //发病日期
    public $modal_tab; //个人信息的tab
    public $template;
    public $incidence_date_print; //打印病历发病日期字段

    /**
     * @inheritdoc
     */

    public static function tableName() {
        return '{{%triage_info}}';
    }

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['record_id', 'state', 'incidence_date', 'temperature_type', 'treatment_type', 'breathing', 'pulse', 'shrinkpressure', 'diastolic_pressure', 'oxygen_saturation', 'pain_score', 'doctor_id', 'room_id', 'create_time', 'update_time'], 'integer'],
            [['weightkg'], 'number', 'min' => 0, 'max' => 200],
            ['heightcm', 'match', 'pattern' => '/^([0-9]\d*(.[0-9]){0,1})$/', 'message' => '身高只能精确到小数点后一位'],
            [['heightcm'], 'number', 'max' => 250, 'min' => 40],
            [['bloodtype'], 'string'],
            ['morbidityDate', 'date', 'max' => date('Y-m-d')],
//            [['chiefcomplaint'], 'required', 'on' => 'saveType'],
            //[['temperature'], 'integer', 'max' => 45, 'min' => 30],
            ['temperature', 'match', 'pattern' => '/^(((3[0-9])|(4[0-4]))(.[0-9]){0,1})|(45|45.0)$/', 'message' => '温度必须为30到45之间的一位小数'],
            [['breathing'], 'integer', 'max' => 100, 'min' => 1],
            ['head_circumference', 'match', 'pattern' => '/^([0-9]\d*(.[0-9]){0,1})$/', 'message' => '头围只能精确到小数点后一位'],
            [['head_circumference'], 'number', 'max' => 60, 'min' => 30],
            [['pulse'], 'integer', 'max' => 300, 'min' => 0],
            [['shrinkpressure'], 'integer', 'max' => 300, 'min' => 50], //收缩压
            [['diastolic_pressure'], 'integer', 'max' => 150, 'min' => 30], //舒张压
            [['oxygen_saturation'], 'integer', 'max' => 300, 'min' => 0], //舒张压
            [['pain_score'], 'integer', 'max' => 10, 'min' => 0], //疼痛评分
            [['fall_score'], 'integer', 'max' => 20, 'min' => 6], //跌倒评分
//             [['allergy'], 'string', 'max' => 1000, 'message' => '过敏史不能超过20条',],
            [['examination_check', 'first_check', 'cure_idea', 'case_reg_img', 'meditation_allergy', 'food_allergy', 'treatment'], 'string'],
            [['examination_check', 'first_check', 'cure_idea', 'case_reg_img', 'meditation_allergy', 'blood_type_supplement', 'food_allergy'], 'default', 'value' => ''],
            [['temperature_type'], 'default', 'value' => '0'],
            [['treatment_type'], 'default', 'value' => '0'],
            [['examination_check', 'first_check'], 'string', 'max' => '255'],
            [['meditation_allergy', 'food_allergy','cure_idea'], 'string', 'max' => 1000],
            [['weightkg'], 'required','on' => 'updatePatientInfo'],
            [['weightkg'], 'required','message' => '已开处方医嘱，体重不能为空','on' => 'updateModalInfo'],
            [['remark'],'string','max'=>30],
            [['room_id'],'required','on' => 'create-record']
        ];
    }

    public function scenarios() {

        $parent = parent::scenarios();
        $parent['outpatientMedicalRecord'] = ['state', 'morbidityDate', 'incidence_date', 'historypresent', 'examination_check', 'first_check', 'cure_idea', 'diagnosis_time'];
        $parent['updatePatientInfo'] = ['weightkg'];
        $parent['orthodonticsFirstRecord'] = ['heightcm','weightkg'];
//         $parent['saveType'] = ['chiefcomplaint', 'historypresent', 'pasthistory', 'physical_examination', 'remark'];
        return $parent;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'record_id' => 'Record ID',
            'incidence_date' => '发病日期',
            'morbidityDate' => '发病日期',
            'heightcm' => '身高(cm)',
            'weightkg' => '体重(kg)',
            'bloodtype' => '血型',
            'blood_type_supplement' => '血型补充',
            'temperature_type' => '体温(°C)',
            'temperature' => '温度',
            'head_circumference' => '头围(cm)',
            'breathing' => '呼吸（次/分钟）',
            'pulse' => '脉搏（次/分钟）',
            'shrinkpressure' => '收缩压（mmHg）',
            'diastolic_pressure' => '舒张压（mmHg）',
            'oxygen_saturation' => '氧饱和度（%）',
            'pain_score' => '疼痛评分（0-10）',
            'fall_score' => '跌倒评分（HDFS 6-20）',
            'remark'=>'备注',
            'treatment_type' => '就诊方式',
            'treatment' => '',
            'meditation_allergy' => '药物过敏',
            'food_allergy' => '食物过敏',
            'case_reg_img' => '病例登记图片',
            'examination_check' => '实验室及影像学检查',
            'first_check' => '初步诊断',
            'cure_idea' => '治疗意见',
            'doctor_id' => '医生ID',
            'room_id' => '接诊诊室',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'diagnosis_time' => '接诊时间',
            'birthday' => '出生日期',
        ];
    }

    /*
     * '0','ABO','AB','O','B','A'
     */

    public static $bloodtype = [
        '0' => '未查',
        'A' => 'A型',
        'B' => 'B型',
        'O' => 'O型',
        'AB' => 'AB型',
    ];
    public static $bloodTypeSupplement = [
        1 => 'RH阴性',
        2 => 'JK3型'
    ];
    public static $temperature_type = [
        3 => '额温',
        2 => '耳温',
        4 => '腋温',
        1 => '口温',
        5 => '肛温',
    ];
    public static $treatment_type = [
        1 => '步行',
        2 => '抱入',
        3 => '扶持',
        4 => '轮椅',
        5 => '其他',
    ];

    public function beforeSave($insert) {

        if ($insert) {
            $this->create_time = time();
        } else {
            if ($this->scenario == 'outpatientMedicalRecord') {
                $this->state = 1;
                $this->incidence_date = strtotime($this->morbidityDate);
            }
        }

        $this->update_time = time();
        return parent::beforeSave($insert);
    }

    /*
     * 获取医生的待接诊 和已接诊数据
     * 1待接诊 2已接诊
     */

    public function getDoctorDiagnoseNum($doctor_id, $type = 1) {
        $status = $type == 1 ? 3 : 5;
        $query = new \yii\db\Query();
        $num = $query->from(['t' => TriageInfo::tableName()])
                ->leftJoin(['p' => PatientRecord::tableName()], '{{t}}.record_id={{p}}.id')
                ->where(['t.spot_id' => $this->spotId, 'FROM_UNIXTIME(t.triage_time, \'%Y-%m-%d\')' => date('Y-m-d'), 't.doctor_id' => $doctor_id, 'p.status' => $status])
                ->count();
        return $num;
    }

    public static function findModel($recordId) {
        if (($model = self::findOne(['record_id' => $recordId, 'spot_id' => self::$staticSpotId])) !== null) {
            return $model;
        } else {
            return new self();
        }
    }

    /**
     * 
     * @param type $recordId
     * @return 根据就诊ID获取   分诊信息
     */
    public static function getTriageInfo($recordId) {
        $query = new \yii\db\Query();
        $data = $query->from(['a' => self::tableName()])
                ->select(['a.record_id', 'a.diagnosis_time', 'a.spot_id', 'departmentName' => 'c.name','b.doctor_id', 'doctorName' => 'd.username', 'b.type_description', 'b.patient_id'])
                ->leftJoin(['b' => Report::tableName()], '{{a}}.record_id={{b}}.record_id')
                ->leftJoin(['c' => SecondDepartment::tableName()], '{{b}}.second_department_id={{c}}.id')
                ->leftJoin(['d' => User::tableName()], '{{b}}.doctor_id={{d}}.id')
                ->where(['a.record_id' => $recordId, 'a.spot_id' => self::$staticSpotId])
                ->one();
        return $data;
    }

    /**
     * @desc 获取当前诊所该就诊记录id的病历字段信息
     * @param integer $recordId 就诊流水id
     * @param array|string $fields 字段属性
     */
    public static function getFieldsList($recordId, $fields = '*') {
        return self::find()->select($fields)->where(['record_id' => $recordId, 'spot_id' => self::$staticSpotId])->asArray()->one();
    }

    /**
     * @param int $doctorId 医生ID
     * @param int $recordId 就诊流水ID
     * @return 自动分诊
     */
    public static function autoTriage($doctorId, $recordId) {
        $doctorInfo = DoctorRoomUnion::find()->select(['id', 'room_id'])->where(['spot_id' => self::$staticSpotId, 'doctor_id' => $doctorId])->asArray()->all();
        if (count($doctorInfo) == 1) {//只有一条常用诊室的时候   才能自动分诊
            //常用诊室是否为正常的
            $roomId = $doctorInfo[0]['room_id'];
            $roomModel = Room::findOne(['id' => $roomId, 'spot_id' => self::$staticSpotId]);
            if ($roomModel->status == 1) {
                //修改  分诊的诊室、分诊时间
                $triageModel = self::findModel($recordId);
                $triageModel->room_id = $roomId;
                $triageModel->triage_time = time();
                $triageModel->save();
                //修改流水为已分诊
                $record = PatientRecord::findOne(['id' => $recordId, 'spot_id' => self::$staticSpotId]);
                $record->status = 3;
                $record->save();
                //将诊室的状态更改为分诊中
                $roomModel->clean_status = 3;
                $roomModel->record_id = $recordId;
                $roomModel->save();
                //待分诊消息推送存取
                MessageCenter::saveMessageCenter($doctorId, $record->patient_id, Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@outpatientOutpatientIndex')]), '', '待接诊', $roomId, $recordId);
            }
        }
    }

}
