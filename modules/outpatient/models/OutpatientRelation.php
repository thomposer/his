<?php

namespace app\modules\outpatient\models;

use Yii;
use yii\db\Query;
use app\modules\triage\models\TriageInfo;
use app\modules\patient\models\PatientRecord;
use app\modules\triage\models\TriageInfoRelation;

/**
 * This is the model class for table "{{%outpatient_relation}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $record_id
 * @property string $chiefcomplaint
 * @property string $historypresent
 * @property string $pasthistory
 * @property string $personalhistory
 * @property string $genetichistory
 * @property string $physical_examination
 * @property string $remark
 * @property integer $create_time
 * @property integer $update_time
 */
class OutpatientRelation extends \app\common\base\BaseActiveRecord
{
    
    public $hasAllergy=1;
    
    public function init(){
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%outpatient_relation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chiefcomplaint','historypresent','physical_examination'],'required','on' => 'basic'],
            [['spot_id', 'record_id','hasAllergy'], 'required'],
            [['spot_id', 'record_id', 'create_time', 'update_time'], 'integer'],
            [['chiefcomplaint', 'historypresent', 'pasthistory', 'personalhistory', 'genetichistory', 'physical_examination'], 'string','max' => 1000],
            [['remark'], 'string', 'max' => '255'],
            [['pasthistory', 'personalhistory', 'genetichistory', 'remark','chiefcomplaint','historypresent','physical_examination'], 'default', 'value' => ''],
            [['record_id'], 'unique'],
            
            
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
            'chiefcomplaint' => '主诉',
            'historypresent' => '现病史',
            'pasthistory' => '既往病史',
            'personalhistory' => '个人史',
            'genetichistory' => '家族史',
            'physical_examination' => '体格检查',
            'remark' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'hasAllergy' => '过敏史',
        ];
    }
    /**
     * 
     * @param integer $id 就诊流水id
     * @return 返回当前诊所门诊-病历相关信息和字段
     */
    public static function getOutpatientInfo($id){
        
        $query = new Query();
        $query->from(['a' => PatientRecord::tableName()]);
        $query->select([
            'a.id','c.examination_check',
            'c.first_check','c.cure_idea','c.meditation_allergy','c.food_allergy',
            'b.chiefcomplaint','b.historypresent','b.pasthistory','b.personalhistory','b.genetichistory','b.physical_examination',
            'd.pastdraghistory','d.followup'
        ]);
        $query->leftJoin(['b' => self::tableName()],'{{a}}.id = {{b}}.record_id');
        $query->leftJoin(['c' => TriageInfo::tableName()],'{{a}}.id = {{c}}.record_id');
        $query->leftJoin(['d' => TriageInfoRelation::tableName()],'{{a}}.id = {{d}}.record_id');
        $query->where(['a.id' => $id,'a.spot_id' => self::$staticSpotId]);
        return $query->one();
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
