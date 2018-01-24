<?php

namespace app\modules\doctor\models;

use Yii;

/**
 * This is the model class for table "gzh_triage_info".
 *
 * @property integer $id
 */
class Doctor extends \app\modules\triage\models\TriageInfo
{

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'gzh_triage_info';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['record_id', 'spot_id', 'incidence_date', 'heightcm', 'temperature_type', 'temperature', 'breathing', 'pulse', 'shrinkpressure', 'diastolic_pressure', 'oxygen_saturation', 'pain_score', 'doctor_id', 'room_id', 'create_time', 'update_time', 'diagnosis_time', 'triage_time'], 'integer'],
            [['spot_id'], 'required'],
            [['weightkg'], 'number'],
            [['bloodtype'], 'string'],
            [['personalhistory', 'genetichistory', 'chiefcomplaint', 'historypresent', 'case_reg_img', 'pasthistory', 'physical_examination', 'examination_check', 'first_check', 'cure_idea', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'record_id' => 'Record ID',
            'spot_id' => 'Spot ID',
            'incidence_date' => 'Incidence Date',
            'heightcm' => 'Heightcm',
            'weightkg' => 'Weightkg',
            'bloodtype' => 'Bloodtype',
            'temperature_type' => 'Temperature Type',
            'temperature' => 'Temperature',
            'breathing' => 'Breathing',
            'pulse' => 'Pulse',
            'shrinkpressure' => 'Shrinkpressure',
            'diastolic_pressure' => 'Diastolic Pressure',
            'oxygen_saturation' => 'Oxygen Saturation',
            'pain_score' => 'Pain Score',
            'personalhistory' => 'Personalhistory',
            'genetichistory' => 'Genetichistory',
            'allergy' => 'Allergy',
            'chiefcomplaint' => 'Chiefcomplaint',
            'historypresent' => 'Historypresent',
            'case_reg_img' => 'Case Reg Img',
            'pasthistory' => 'Pasthistory',
            'physical_examination' => 'Physical Examination',
            'examination_check' => 'Examination Check',
            'first_check' => 'First Check',
            'cure_idea' => 'Cure Idea',
            'remark' => 'Remark',
            'doctor_id' => 'Doctor ID',
            'room_id' => 'Room ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'diagnosis_time' => 'Diagnosis Time',
            'triage_time' => 'Triage Time',
        ];
    }

}
