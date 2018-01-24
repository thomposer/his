<?php

namespace app\modules\triage\models;

use Yii;
use \app\modules\patient\models\PatientRecord;

/**
 * This is the model class for table "{{%patient_record}}".
 *
 * @property string $id
 * @property string $patient_id
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class Triage extends PatientRecord {

    public $username;
    public $iphone;
    public $department_name;
    public $doctor_name;
    public $time;
    public $arrival_time;
    public $arrival;
    public $user_sex;
    public $birthday; //出生日期
    public $doctor_chose;
    public $room_chose;
    public $temperature;
    public $breathing;
    public $pulse;
    public $shrinkpressure;
    public $diastolic_pressure;
    public $appointment_doctor;
    public $pain_score;
    public $fall_score;
    public $firstRecord;//是否第一次就诊

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'patient_id' => '患者ID',
            'username' => '患者信息',
            'iphone' => '手机号',
            'department_name' => '就诊科室',
            'doctor_name' => '接诊医生',
            'time' => '预约时间',
            'arrival_time' => '报到时间',
            'arrival' => '报到详情',
            'temperature' => '体温(℃)',
            'breathing' => '呼吸(次/分)',
            'pulse' => '脉搏(次/分)',
            'shrinkpressure' => '血压(mmHg)',
            'doctor_id' => '选择的医生ID',
            'room_id' => '诊室ID',
            'appointment_doctor' => '预约的医生ID',
            'birthday' => '出生日期',
        ];
    }

}
