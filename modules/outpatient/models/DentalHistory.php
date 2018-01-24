<?php

namespace app\modules\outpatient\models;

use Yii;
use app\modules\outpatient\models\DentalHistoryRelation;
use yii\db\Query;

/**
 * This is the model class for table "{{%dental_history}}".
 *
 * @property string $id
 * @property string $record_id
 * @property string $spot_id
 * @property integer $type
 * @property string $chiefcomplaint
 * @property string $historypresent
 * @property string $pasthistory
 * @property string $returnvisit
 * @property string $advice
 * @property string $remarks
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property DentalHistoryRelation[] $dentalHistoryRelations
 */
class DentalHistory extends \app\common\base\BaseActiveRecord
{

    public $hasAllergy = 1;

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%dental_history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'hasAllergy'], 'required'],
            [['record_id', 'spot_id', 'type', 'create_time', 'update_time'], 'integer'],
            [['chiefcomplaint', 'historypresent', 'pasthistory'], 'required', 'on' => 'first'],
            [['chiefcomplaint', 'historypresent', 'pasthistory', 'returnvisit', 'advice', 'remarks'], 'string', 'max' => 1000],
            [['chiefcomplaint', 'historypresent', 'pasthistory', 'returnvisit', 'advice', 'remarks'], 'default', 'value' => ' '],
        ];
    }

    public function scenarios() {
        $result = parent::scenarios();
        $result['first'] = ['spot_id', 'record_id', 'type', 'chiefcomplaint', 'historypresent', 'pasthistory', 'advice', 'remarks', 'returnvisit', 'hasAllergy'];
        $result['return'] = ['spot_id', 'record_id', 'type', 'chiefcomplaint', 'historypresent', 'pasthistory', 'advice', 'remarks', 'returnvisit', 'hasAllergy'];
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'record_id' => '流水ID',
            'spot_id' => '诊所ID',
            'type' => '就诊类型(1-初诊,2-复诊)',
            'chiefcomplaint' => '主诉',
            'historypresent' => '现病史',
            'pasthistory' => '既往病史',
            'returnvisit' => '复诊',
            'advice' => '医嘱',
            'remarks' => '备注',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'hasAllergy' => '过敏史'
        ];
    }

    public static $getRecordType = [
        1 => '初诊',
        2 => '复诊',
    ];

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDentalHistoryRelations() {
        return $this->hasMany(DentalHistoryRelation::className(), ['dental_history_id' => 'id']);
    }

    /**
     * @desc 返回该就诊记录的病历信息
     * @param integer $recordId 就诊流水记录id
     * @param string|array $fields 查询字段
     * @return \yii\db\ActiveRecord|NULL
     */
    public static function getFieldsList($recordId, $fields = '*') {
        return self::find()->select($fields)->where(['record_id' => $recordId, 'spot_id' => self::$staticSpotId])->asArray()->one();
    }

    public static function getDentalHistoryData($recordIds, $spotId = null) {
        $query = new Query();
        $query->select(['a.record_id', 'b.type', 'b.content', 'b.position','b.dental_disease']);
        $query->from(['a' => DentalHistory::tableName()]);
        $query->leftJoin(['b' => DentalHistoryRelation::tableName()], '{{a}}.id = {{b}}.dental_history_id');
        $query->where(['a.record_id' => $recordIds]);
        $query->filterWhere(['a.spot_id' => $spotId]);
        $data = $query->all();
        $result = array();
        foreach ($data as $val) {
            $result [$val['record_id']][$val['type']][] = $val;
        }
        return $result;
    }

}
