<?php

namespace app\modules\make_appointment\models;

use Yii;
use app\common\base\BaseActiveRecord;

/**
 * This is the model class for table "{{%appointment_time_config}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $begin_time
 * @property string $end_time
 * @property string $close_reason
 * @property string $create_time
 * @property string $update_time
 */
class AppointmentTimeConfig extends BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%appointment_time_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'begin_time', 'end_time', 'close_reason', 'create_time', 'update_time'], 'required'],
            [['spot_id', 'begin_time', 'end_time', 'create_time', 'update_time'], 'integer'],
            [['close_reason'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'spot_id' => '诊所id',
            'begin_time' => '关闭预约开始时间',
            'end_time' => '关闭预约结束时间',
            'close_reason' => '关闭预约时间原因',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    /**
     * @return 返回诊所大于当前时间的关闭的预约时间段列表
     * @param 诊所id $spotId
     */
    public static function getSpotConfig($spotId){
        
        return self::find()->select(['begin_time','end_time'])->where(['spot_id' => $spotId])->andWhere('end_time >= :end_time',[':end_time' => time()])->asArray()->all();
    }
}
