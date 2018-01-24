<?php

namespace app\modules\spot\models;

use Yii;

/**
 * This is the model class for table "{{%inspect_item_union}}".
 *
 * @property string $id
 * @property string $inspect_id
 * @property string $item_id
 * @property string $sort
 * @property string $create_time
 * @property string $update_time
 */
class InspectItemUnion extends \yii\db\ActiveRecord
{

    public $item_name;
    public $english_name;
    public $unit;
    public $reference;
    public $unionId;

    public function behaviors() {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%inspect_item_union}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['inspect_id', 'item_id', 'sort', 'create_time', 'update_time'], 'integer'],
            [['item_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'item_name' => '项目名称',
            'english_name' => '英文缩写',
            'unit' => '单位',
            'reference' => '参考值',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * 
     * @param type $inspect_id 实验室医嘱项目ID
     * @return type 和医嘱想关联的所有项目
     */
    public static function getInspectItem($inspect_id, $type = 1) {
        $data = [];
        if (!$inspect_id) {
            return $item;
        }
        $query = new \yii\db\Query();
        $item = $query->from(['t1' => self::tableName()])
                ->select(['t2.id', 't2.item_name', 't2.english_name', 't1.inspect_id', 't2.unit', 't2.reference'])
                ->leftJoin(['t2' => InspectItem::tableName()], "{{t1}}.item_id={{t2}}.id")
                ->where(['t1.inspect_id' => $inspect_id, 't2.status' => 1])
                ->all();
        if ($item) {
            foreach ($item as $val) {
                if ($type == 1) {
                    $val['item_name'] = \yii\helpers\Html::encode($val['item_name']);
                    $val['english_name'] = \yii\helpers\Html::encode($val['english_name']);
                }
                $data[$val['inspect_id']][] = $val;
            }
        }
        return $data;
    }

}
