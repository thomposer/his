<?php

namespace app\modules\make_appointment\models;

use Yii;

/**
 * This is the model class for table "{{%appointment_time_and_server}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $time_config_id
 * @property integer $spot_type_id
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property AppointmentConfig $timeConfig
 */
class AppointmentTimeAndServer extends \app\common\base\BaseActiveRecord
{
    public function init(){
        
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%appointment_time_and_server}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time_config_id', 'spot_type_id','spot_id'], 'required'],
            [['time_config_id', 'spot_type_id', 'create_time', 'update_time'], 'integer'],
            [['time_config_id'], 'exist', 'skipOnError' => true, 'targetClass' => AppointmentConfig::className(), 'targetAttribute' => ['time_config_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'time_config_id' => '时间段设置id',
            'spot_type_id' => '服务类型id',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTimeConfig()
    {
        return $this->hasOne(AppointmentConfig::className(), ['id' => 'time_config_id']);
    }
}
