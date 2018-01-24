<?php

namespace app\modules\outpatient\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%orthodontics_returnvisit_record}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $record_id
 * @property string $returnvisit
 * @property string $check
 * @property string $treatment
 * @property string $create_time
 * @property string $update_time
 */
class OrthodonticsReturnvisitRecord extends \app\common\base\BaseActiveRecord
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
        return '{{%orthodontics_returnvisit_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'record_id', 'returnvisit', 'treatment', 'hasAllergy'], 'required'],
            [['spot_id', 'record_id', 'create_time', 'update_time'], 'integer'],
            [['returnvisit', 'check', 'treatment'], 'string', 'max' => 1000],
            [['check'], 'default', 'value' => ''],
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
            'record_id' => '就诊流水id',
            'returnvisit' => '复诊',
            'check' => '影像学检查',
            'treatment' => '处理',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'hasAllergy' => '过敏史',
        ];
    }
    
    public static function getData($recordId) {
        return self::find()->select(['returnvisit','check','treatment'])->where(['spot_id' => self::$staticSpotId, 'record_id' => $recordId])->asArray()->one();
    }
}
