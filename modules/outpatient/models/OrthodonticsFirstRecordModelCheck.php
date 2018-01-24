<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%orthodontics_first_record_model_check}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $record_id
 * @property string $crowded_maxillary
 * @property string $crowded_mandible
 * @property string $canine_maxillary
 * @property string $canine_mandible
 * @property string $molar_maxillary
 * @property string $molar_mandible
 * @property string $spee_curve
 * @property integer $transversal_curve
 * @property string $bolton_nterior_teeth
 * @property string $bolton_all_teeth
 * @property string $examination
 * @property integer $create_time
 * @property integer $update_time
 */
class OrthodonticsFirstRecordModelCheck extends \app\common\base\BaseActiveRecord
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
        return '{{%orthodontics_first_record_model_check}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'record_id', 'examination','crowded_maxillary', 'crowded_mandible', 'canine_maxillary', 'canine_mandible', 'molar_maxillary', 'molar_mandible', 'spee_curve','transversal_curve','bolton_nterior_teeth','bolton_all_teeth'], 'required'],
            [['spot_id', 'record_id', 'transversal_curve', 'create_time', 'update_time'], 'integer'],
            [['examination'], 'string','max' => 1000],
            [['crowded_maxillary', 'crowded_mandible', 'canine_maxillary', 'canine_mandible', 'molar_maxillary', 'molar_mandible', 'spee_curve'], 'number', 'max' => 10000,'min' => 0],
            [['crowded_maxillary', 'crowded_mandible', 'canine_maxillary', 'canine_mandible', 'molar_maxillary', 'molar_mandible', 'spee_curve'], 'string', 'max' => 32],
            [['bolton_nterior_teeth', 'bolton_all_teeth'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'record_id' => '就诊流水ID',
            'crowded_maxillary' => '上颌',
            'crowded_mandible' => '下颌',
            'canine_maxillary' => '上颌',
            'canine_mandible' => '下颌',
            'molar_maxillary' => '上颌',
            'molar_mandible' => '下颌',
            'spee_curve' => 'spee曲线',
            'transversal_curve' => '横合曲线',
            'bolton_nterior_teeth' => '前牙',
            'bolton_all_teeth' => '全牙',
            'examination' => '影像学检查',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    /**
     * 横合曲线(1-陡，2-平，3-凹)
     * @var array
     */
    public static $getTransversalCurve = [
        1 => '陡',
        2 => '平',
        3 => '凹'
    ];
}
