<?php

namespace app\specialModules\recharge\models;

use Yii;

/**
 * This is the model class for table "{{%membership_package_card_flow_service}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $flow_id
 * @property integer $package_card_service_id
 * @property integer $time
 * @property integer $create_time
 * @property integer $update_time
 */
class MembershipPackageCardFlowService extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%membership_package_card_flow_service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'flow_id', 'package_card_service_id', 'time', 'create_time', 'update_time'], 'required'],
            [['spot_id', 'flow_id', 'package_card_service_id', 'time', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增ID',
            'spot_id' => '诊所id',
            'flow_id' => '会员--套餐卡流水ID',
            'package_card_service_id' => '套餐卡服务类型配置表id',
            'time' => '消费／回退次数',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
