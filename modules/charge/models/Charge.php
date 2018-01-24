<?php

namespace app\modules\charge\models;

use Yii;

/**
 * This is the model class for table "gzh_patient_record".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $patient_id
 * @property string $price
 * @property integer $type
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class charge extends \app\modules\patient\models\PatientRecord
{

    public $username;
    public $sex;
    public $birthday;
    public $diagnosis_time;
    public $diagnosis_doctor;
    public $charge_time;
    public $type;
    public $iphone;
    public $firstRecord;//是否第一次就诊

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'username' => '姓名',
            'sex' => '性别',
            'birthday' => '年龄',
            'diagnosis_time' => '接诊时间',
            'charge_time' => '收费时间',
            'diagnosis_doctor' => '接诊医生',
            'type_description' => '服务类型',
            'price' => '收费(金额)',
            'iphone' => '手机号码',
        ];
    }

    public static $getPatientsType = [
//        0 => '未知',
        1 => '初诊',
        2 => '复诊',
    ];

}
