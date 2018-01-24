<?php

namespace app\modules\card\models;

use Yii;

/**
 * This is the model class for table "gzh_card_service_left".
 *
 * @property string $id
 * @property string $service_left
 * @property string $card_physical_id
 * @property string $service_id
 * @property string $create_time
 * @property string $update_time
 */
class CardSpotConfig extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%card_spot_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['card_type', 'spot_id', 'parent_spot_id', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_left' => '剩余次数',
            'card_physical_id' => '会员卡主键ID',
            'service_id' => '服务ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
