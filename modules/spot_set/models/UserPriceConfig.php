<?php

namespace app\modules\spot_set\models;

use Yii;

/**
 * This is the model class for table "{{%user_price_config}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $user_id
 * @property string $price
 * @property string $create_time
 * @property string $update_time
 */
class UserPriceConfig extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_price_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'user_id', 'price'], 'required'],
            [['spot_id', 'user_id', 'create_time', 'update_time', 'type'], 'integer'],
            [['price'], 'number', 'max' => 100000],
            [['price'], 'trim'],
            [['price'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
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
            'user_id' => '医生id',
            'price' => '诊金',
            'type' => '类型(1-方便门诊)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @param $doctorId 医生id
     * @return array|null|\yii\db\ActiveRecord 返回医生加关联服务配置的诊金
     */
    public static function getMedicalFee($where = '1 != 0'){
        $result = self::find()->select(['price'])->where(['spot_id' => self::$staticSpotId])->andFilterWhere($where)->asArray()->one();
        return $result;
    }
}
