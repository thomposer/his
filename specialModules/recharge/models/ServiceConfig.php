<?php

namespace app\specialModules\recharge\models;

use Yii;

/**
 * This is the model class for table "{{%service_config}}".
 *
 * @property string $id
 * @property string $card_type
 * @property string $service_name
 * @property string $service_desc
 * @property string $service_total
 * @property string $service_left
 * @property string $create_time
 * @property string $update_time
 */
class ServiceConfig extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%service_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'service_total', 'service_left', 'create_time', 'update_time','card_type'], 'integer'],
            [['service_desc'], 'string'],
            [['service_name'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'card_type' => '卡类型',
            'service_name' => '服务名称',
            'service_desc' => '服务描述',
            'service_total' => '总服务次数',
            'service_left' => '剩余次数',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
