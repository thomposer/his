<?php

namespace app\modules\triage\models;

use Yii;
use app\modules\spot\models\Spot;
/**
 * This is the model class for table "{{%triage_info_relation}}".
 *
 * @property integer $id
 * @property integer $record_id
 * @property integer $spot_id
 * @property string $pastdraghistory
 * @property string $followup
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property Spot $spot
 */
class TriageInfoRelation extends \app\common\base\BaseActiveRecord
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
        return '{{%triage_info_relation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['record_id', 'spot_id', 'create_time', 'update_time'], 'integer'],
            [['followup'], 'string', 'max' => 255],
            [['pastdraghistory'],'string','max' => 1000],
            [['spot_id'], 'exist', 'skipOnError' => true, 'targetClass' => Spot::className(), 'targetAttribute' => ['spot_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'record_id' => '就诊流水ID',
            'spot_id' => '诊所ID',
            'pastdraghistory' => '过去用药史',
            'followup' => '随诊',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpot()
    {
        return $this->hasOne(Spot::className(), ['id' => 'spot_id']);
    }
    
    /**
     * @desc 获取当前诊所该就诊记录id的病历字段信息
     * @param integer $recordId 就诊流水id
     * @param array|string $fields 字段属性
     */
    public static function getFieldsList($recordId,$fields = '*'){
    
        return self::find()->select($fields)->where(['record_id' => $recordId,'spot_id' => self::$staticSpotId])->asArray()->one();
    
    }
    
}
