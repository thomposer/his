<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%orthodontics_first_record_teeth_check}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $record_id
 * @property string $dental_caries
 * @property string $reverse
 * @property string $impacted
 * @property string $ectopic
 * @property string $defect
 * @property string $retention
 * @property string $repair_body
 * @property string $other
 * @property string $other_remark
 * @property string $orthodontic_target
 * @property string $cure
 * @property string $special_risk
 * @property integer $create_time
 * @property integer $update_time
 */
class OrthodonticsFirstRecordTeethCheck extends \app\common\base\BaseActiveRecord
{
    public function init(){
        
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%orthodontics_first_record_teeth_check}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'record_id', 'orthodontic_target', 'cure', 'special_risk'], 'required'],
            [['spot_id', 'record_id', 'create_time', 'update_time'], 'integer'],
            [['orthodontic_target', 'cure', 'special_risk'], 'string','max' => 1000],
            [['dental_caries', 'reverse', 'impacted', 'ectopic', 'defect', 'retention', 'repair_body', 'other'], 'string', 'max' => 64],
            [['other_remark'], 'string', 'max' => 100],
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
            'record_id' => '就诊流水ID',
            'dental_caries' => '龋齿',
            'reverse' => '扭转',
            'impacted' => '阻生',
            'ectopic' => '异位',
            'defect' => '缺失',
            'retention' => '滞留',
            'repair_body' => '修复体',
            'other' => '其他',
            'other_remark' => '其他-备注',
            'orthodontic_target' => '矫治目标',
            'cure' => '治疗计划',
            'special_risk' => '特殊风险',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
