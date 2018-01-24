<?php

namespace app\modules\inspect\models;

use Yii;

/**
 * This is the model class for table "{{%inspect_record_union}}".
 *
 * @property string $id
 * @property string $inspect_record_id
 * @property string $spot_id
 * @property string $record_id
 * @property string $name
 * @property string $unit
 * @property string $reference
 * @property string $result
 * @property string $result_identification
 * @property string $create_time
 * @property string $update_time
 *
 * @property InspectRecord $inspectRecord
 */
class InspectRecordUnion extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
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
            [['result_identification'],'string','max' => 32],
            [['result_identification'],'default','value' => ''],
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
    public function getInspectRecord()
    {
        return $this->hasOne(InspectRecord::className(), ['id' => 'inspect_record_id']);
    }
    /**
     * 
     * @param string $val 结果标识
     * @return string 返回结果判断
     */
    public static function getResultIdentification($val){
        $result = '';
        switch (strtoupper($val)){
            case 'H' :
                $result = '<i class = "red fa fa-long-arrow-up" aria-hidden="true"></i>';    
                break;
            case 'HH' :
                $result = '<i class = "red fa fa-long-arrow-up" aria-hidden="true"></i>';
                break;
            case 'L' :
                $result = '<i class="blue fa fa-long-arrow-down" aria-hidden="true"></i>';
                break;
            case 'LL' :
                $result = '<i class="blue fa fa-long-arrow-down" aria-hidden="true"></i>';
                break;
            case 'P' : 
                $result = '<i class = "red">+</i>';
                break;
            case 'Q' : 
                $result = '<i class = "red">±</i>';
                break;
            case 'E' : 
                $result = '<span class = "red">错误值</span>';
                break;
        }
        return $result;
    }
    
    
}
