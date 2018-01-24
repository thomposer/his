<?php

namespace app\modules\nurse\models;

use Yii;
use app\modules\spot_set\models\Room;
/**
 * This is the model class for table "{{%room}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $clinic_name
 * @property integer $floor
 * @property integer $clinic_type
 * @property integer $status
 * @property integer $clean_status
 * @property string $treatment_time
 * @property string $create_time
 * @property integer $update_time
 * @property integer $record_id;
 * @property integer $is_delete
 */
class Nurse extends Room
{
   
    public $doctorName;
    public $patientName;
    public $inspect;
    public $check;
    public $cure;
    public $recipe;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['clinic_name', 'clean_status', 'patient_username','record_id'], 'integer'],
            [['clinic_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'clinic_name' => '诊室名称',
            'clean_status' => '诊室状态',
            'doctorName' => '医生/病人',
            'inspect' => '实验室检查',
            'check' => '影像学检查',
            'cure' => '治疗',
            'recipe' => '处方',
            'record_id' => '就诊流水id'
        ];
    }

    public static $getClinicStatus = [
        1 => '空闲中',
        2 => '待整理',
        3 => '接诊中',
    ];

}
