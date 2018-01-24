<?php

namespace app\specialModules\recharge\models;

use Yii;
use app\modules\spot\models\PackageCardService;
use app\specialModules\recharge\models\MembershipPackageCard;

/**
 * This is the model class for table "{{%membership_package_card_service}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $membership_package_card_id
 * @property integer $package_card_service_id
 * @property integer $total_time
 * @property integer $remain_time
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property MembershipPackageCard $membershipPackageCard
 * @property PackageCardService $packageCardService
 */
class MembershipPackageCardService extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%membership_package_card_service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'membership_package_card_id', 'package_card_service_id', 'total_time', 'remain_time'], 'required'],
            [['spot_id', 'membership_package_card_id', 'package_card_service_id', 'total_time', 'remain_time', 'create_time', 'update_time'], 'integer'],
            [['membership_package_card_id'], 'exist', 'skipOnError' => true, 'targetClass' => MembershipPackageCard::className(), 'targetAttribute' => ['membership_package_card_id' => 'id']],
            [['package_card_service_id'], 'exist', 'skipOnError' => true, 'targetClass' => PackageCardService::className(), 'targetAttribute' => ['package_card_service_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'membership_package_card_id' => '会员卡-套餐卡配置表id',
            'package_card_service_id' => '套餐卡服务类型配置表id',
            'total_time' => '总次数',
            'remain_time' => '剩余次数',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembershipPackageCard()
    {
        return $this->hasOne(MembershipPackageCard::className(), ['id' => 'membership_package_card_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPackageCardService()
    {
        return $this->hasOne(PackageCardService::className(), ['id' => 'package_card_service_id']);
    }
}
