<?php

namespace app\modules\charge\models;

use Yii;
use app\specialModules\recharge\models\CardRecharge;

/**
 * This is the model class for table "{{%first_order_free}}".
 *
 * @property string $id
 * @property string $patient_id
 * @property integer $type
 * @property string $card_id
 * @property string $record_id
 * @property string $charge_record_id
 * @property string $spot_id
 * @property string $create_time
 * @property string $update_time
 */
class FirstOrderFree extends \app\common\base\BaseActiveRecord
{

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%first_order_free}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['patient_id', 'card_id', 'record_id', 'charge_record_id', 'spot_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'patient_id' => '患者ID',
            'type' => '1/首单免诊金',
            'card_id' => '会员卡ID',
            'record_id' => '就诊流水ID',
            'charge_record_id' => '收费ID',
            'spot_id' => '诊所ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * 
     * @param type $patientId
     * @param type $phone
     * @return 用户是否有首单免诊金的机会 true/有机会 false/无机会
     */
    public static function check($patientId, $phone) {
        $vipInfo = CardRecharge::find()->select(['cardId' => 'f_physical_id'])->where(['f_phone' => $phone, 'f_is_logout' => 0])->asArray()->one();
        $status = false;
        if (!empty($vipInfo)) {
            //查看是否 已经使用了  免诊金的机会
            $data = self::find()->select(['patient_id', 'card_id'])->where(['patient_id' => $patientId])->asArray()->one();
            if (empty($data)) {
                $status = true;
            }
        }
        return $status;
    }

    /**
     * 
     * @param type $patientId 患者ID
     * @param type $cardId 卡ID
     * @param type $recordId 就诊流水ID
     * @param type $chargeRecordId 收费ID （预留  目前都为0）
     * @return 保存首单免诊金的用户数据
     */
    public static function saveData($patientId, $recordId, $chargeRecordId = 0, $cardId = 0) {
        $model = new self();
        $model->patient_id = $patientId;
        $model->record_id = $recordId;
        $model->charge_record_id = $chargeRecordId; //预留
        $model->card_id = $cardId;
        $model->save();
    }

}
