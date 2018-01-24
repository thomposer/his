<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%child_examination_growth}}".
 * 儿童体检-生长评估
 * @property integer $id
 * @property integer $spot_id
 * @property integer $record_id
 * @property integer $result
 * @property string $remark
 * @property integer $create_time
 * @property integer $update_time
 */
class ChildExaminationGrowth extends \app\common\base\BaseActiveRecord
{
    
    public $hasAllergy = 1;
        
    public function init(){
    
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%child_examination_growth}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'record_id', 'hasAllergy'], 'required'],
            [['spot_id', 'record_id', 'result', 'create_time', 'update_time'], 'integer'],
            [['remark'], 'string', 'max' => 255],
            [['result'],'default','value' => 0],
            [['remark'],'default','value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'spot_id' => '诊所id',
            'record_id' => '就诊流水id',
            'result' => '生长评估总结',
            'remark' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'hasAllergy' => '过敏史',
        ];
    }
}
