<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%inspect_record_union}}".
 *
 * @property integer $id
 * @property integer $inspect_record_id
 * @property integer $spot_id
 * @property integer $record_id
 * @property string $name
 * @property string $unit
 * @property string $reference
 * @property integer $item_id 检验项目id
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property InspectRecord $inspectRecord
 */
class InspectRecordUnion extends \app\common\base\BaseActiveRecord
{

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%inspect_record_union}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['inspect_record_id', 'spot_id', 'record_id'], 'required'],
            [['inspect_record_id', 'spot_id', 'record_id', 'create_time', 'update_time','item_id'], 'integer'],
            [['name', 'unit', 'reference'], 'string', 'max' => 64],
            [['result'], 'string', 'max' => 255],
            [['item_id'],'default','value' => 0],
            [['inspect_record_id'], 'exist', 'skipOnError' => true, 'targetClass' => InspectRecord::className(), 'targetAttribute' => ['inspect_record_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '自增id',
            'inspect_record_id' => '门诊患者-实验室检查信息表id',
            'item_id' => '检验项目id',
            'spot_id' => '诊所id',
            'record_id' => '流水id',
            'name' => '项目名称',
            'unit' => '单位',
            'reference' => '参考值',
            'result' => '检查结果',
            'result_identification' => '结果判断',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInspectRecord() {
        return $this->hasOne(InspectRecord::className(), ['id' => 'inspect_record_id']);
    }

    public static function getInspectItem($inspect_id) {
        $item = [];
        if (!$inspect_id) {
            return $item;
        }
        $item = self::find()->where(['inspect_record_id' => $inspect_id])->asArray()->all();
        if ($item) {
            foreach ($item as &$val) {
                $val['name'] = \yii\helpers\Html::encode($val['name']);
                $val['english_name'] = \yii\helpers\Html::encode($val['english_name']);
            }
        }
        return $item;
    }

}
