<?php

namespace app\modules\make_appointment\models;

use Yii;

/**
 * This is the model class for table "{{%appointment_config}}".
 *
 * @property string $id
 * @property integer $spot_id
 * @property string $department_id
 * @property string $begin_time
 * @property string $end_time
 * @property string $doctor_count
 * @property string $create_time
 * @property string $update_time
 */
class DoctorTimeConfig extends \app\common\base\BaseActiveRecord
{

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%doctor_time_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['doctor_id', 'spot_id', 'begin_time', 'end_time'], 'required'],
            [['doctor_id', 'spot_id', 'begin_time', 'end_time', 'create_time', 'update_time'], 'integer'],
            ['end_time', 'compare', 'operator' => '>', 'compareAttribute' => 'begin_time']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'doctor_id' => '医生ID',
            'begin_time' => '开始时间',
            'end_time' => '结束时间',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    

}
