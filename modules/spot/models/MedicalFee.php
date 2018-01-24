<?php

namespace app\modules\spot\models;

use Yii;

/**
 * This is the model class for table "{{%medical_fee}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $name
 * @property string $remarks
 * @property string $note
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class MedicalFee extends \app\common\base\BaseActiveRecord
{

    public function init() {
        parent::init();
        $this->spot_id = $this->parentSpotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%medical_fee}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'price', 'status'], 'required'],
            [['spot_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['price'], 'number', 'max' => 100000],
            [['price'], 'validatePrice'],
            [['price'], 'trim'],
            [['price'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
            [['remarks'], 'string','max' => 20],
            [['note'], 'string','max' => 30],
            [['remarks','note'], 'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '机构ID',
            'price' => '诊金金额',
            'remarks' => '诊金说明',
            'note' => '诊金备注',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    public function validatePrice($attribute) {
        $parentSpotId = $this->parentSpotId;
        if ($this->isNewRecord) {
            $hasRecord = MedicalFee::find()->select(['id'])->where(['spot_id' => $parentSpotId, $attribute => $this->$attribute])->asArray()->limit(1)->one();
            if ($hasRecord) {
                $this->addError($attribute, '该诊疗费配置已存在');
            }
        } else {
            $oldPrice = $this->getOldAttribute('price');
            if ($oldPrice != $this->price) {
                $hasRecord = $this->checkDuplicate('price', $this->price);
                if ($hasRecord) {
                    $this->addError('price', '该诊疗费配置已存在');
                }
            }
        }
    }

    protected function checkDuplicate($attribute, $params) {
        $parentSpotId = $this->parentSpotId;
        $hasRecord = MedicalFee::find()->select(['id'])->where(['spot_id' => $parentSpotId, $attribute => $params])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }

    public static function getMedicalFeeList() {
        return self::find()->select(['price'])->where(['spot_id' => self::$staticParentSpotId, 'status' => 1])->asArray()->all();
    }

}
