<?php

namespace app\specialModules\recharge\models;

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
class CardServiceLeft extends \app\common\base\BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'gzh_card_service_left';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['service_left', 'card_physical_id', 'card_id', 'service_id', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'service_left' => '剩余次数',
            'card_physical_id' => '会员卡主键ID',
            'service_id' => '服务ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * 
     */
    public static function createUpdateService($serviceLeft, $serviceId, $cardId, $cardPhysicalId) {
        if (!empty($serviceLeft)) {
            $service = self::find()->select(['id'])->where(['card_id' => $cardId, 'service_id' => $serviceId[0]])->asArray()->one();
            if (!empty($service)) {//修改
                foreach ($serviceLeft as $key => $val) {
                    CardServiceLeft::updateAll(['service_left' => $val], ['card_id' => $cardId, 'service_id' => $serviceId[$key]]);
                }
            } else {//新增
                $activateTime = time();
                $invalidTime = $activateTime + 365 * 24 * 60 * 60;
                $row = [];
                foreach ($serviceLeft as $key => $val) {
                    $row[] = [$val, $cardPhysicalId, $serviceId[$key], $cardId, $activateTime, $invalidTime, $activateTime, $activateTime];
                }
                //批量插入 卡关联的服务
                Yii::$app->db->createCommand()->batchInsert(CardServiceLeft::tableName(), ['service_left', 'card_physical_id', 'service_id', 'card_id', 'activate_time', 'invalid_time', 'create_time', 'update_time'], $row)->execute();
            }
        }
    }

}
