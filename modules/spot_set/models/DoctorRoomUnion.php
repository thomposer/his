<?php

namespace app\modules\spot_set\models;

use Yii;

/**
 * This is the model class for table "{{%doctor_room_union}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $doctor_id
 * @property string $room_id
 * @property string $create_time
 * @property string $update_time
 */
class DoctorRoomUnion extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%doctor_room_union}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'doctor_id', 'room_id'], 'required'],
            [['id', 'spot_id', 'doctor_id', 'room_id', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'spot_id' => '诊所ID',
            'doctor_id' => '医生ID',
            'room_id' => '诊室ID',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
