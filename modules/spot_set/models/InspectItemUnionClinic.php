<?php

namespace app\modules\spot_set\models;

use Yii;
use app\modules\spot\models\InspectItem;

/**
 * This is the model class for table "{{%inspect_item_unin_clinic}}".
 *
 * @property string $id
 * @property string $inspect_id
 * @property string $item_id
 * @property string $clinic_inspect_id
 * @property string $spot_id
 * @property string $create_time
 * @property string $update_time
 */
class InspectItemUnionClinic extends \app\common\base\BaseActiveRecord
{

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%inspect_item_union_clinic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['inspect_id', 'item_id', 'clinic_inspect_id', 'spot_id', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'inspect_id' => '机构检验医嘱ID',
            'item_id' => '机构检验项目ID',
            'clinic_inspect_id' => '诊所检验医嘱ID',
            'spot_id' => '诊所ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * 
     * @param type $inspectClinicId 诊所检验医嘱ID
     * @return type 诊所下和医嘱想关联的所有项目
     */
    public static function getInspectItemClinic($inspectClinicId) {
        $data = [];
        if (!$inspectClinicId) {
            return $data;
        }
        $query = new \yii\db\Query();
        $item = $query->from(['t1' => self::tableName()])
                ->select(['t2.id', 't2.item_name', 't2.english_name', 't1.clinic_inspect_id', 't2.unit', 't2.reference'])
                ->leftJoin(['t2' => InspectItem::tableName()], "{{t1}}.item_id={{t2}}.id")
                ->where(['t1.clinic_inspect_id' => $inspectClinicId, 't2.status' => 1, 't1.spot_id' => self::$staticSpotId])
                ->all();
        if ($item) {
            foreach ($item as $val) {
                $val['item_name'] = \yii\helpers\Html::encode($val['item_name']);
                $val['english_name'] = \yii\helpers\Html::encode($val['english_name']);
                $data[$val['clinic_inspect_id']][] = $val;
            }
        }
        return $data;
    }

}
