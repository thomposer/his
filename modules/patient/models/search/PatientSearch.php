<?php

namespace app\modules\patient\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use app\modules\report\models\Report;
use yii\db\ActiveQuery;
use app\modules\spot_set\models\SpotType;
use app\modules\spot\models\OrganizationType;
use app\modules\triage\models\TriageInfo;

/**
 * PatientSearch represents the model behind the search form about `app\modules\patient\models\Patient`.
 */
class PatientSearch extends Patient
{

    public $start_birthday;
    public $end_birthday;
    public $record_spot_id;//就诊诊所id
    public $record_id;
    public $second_department_id;
    public $doctor_id;
    public $type;
    public $record_start_time;
    public $record_end_time;
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id','sex','record_spot_id','record_id','doctor_id','type','second_department_id'], 'integer'],
//            [['id','username','name_phone'],'trim'],
            [['card'],'trim'],
            [['record_start_time','record_end_time','start_birthday','end_birthday','search_start_date', 'search_end_date', 'name_phone', 'patient_number', 'iphone'], 'string'],
            [['username'], 'safe'],
            ['search_end_date', 'validateEndSearchDate'],
        ];
    }

    public function validateEndSearchDate($attribute, $params) {

        if ($this->search_end_date < $this->search_start_date) {
            $this->addError($attribute, '结束时间必须大于或等于开始时间');
        }
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        $parentData = parent::attributeLabels();
        $data = [
            'start_birthday' => '出生日期',
            'record_id' => '门诊号',
            'record_spot_id' => '就诊诊所',
            'second_department_id' => '就诊科室',
            'doctor_id' => '就诊医生',
            'type' => '就诊类型',
            'record_start_time' => '就诊时间'
        ];
        return array_merge($parentData, $data);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 20) {
        $query = new ActiveQuery(Patient::className());
        $query->from(['a' => Patient::tableName()]);
        $query->select(['a.id', 'a.username', 'a.iphone', 'a.sex', 'a.birthday', 'a.diagnosis_time', 'a.patient_number', 'a.first_record','record_count' => 'group_concat(b.status)',]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id']
            ]
        ]);
        $query->leftJoin(['b' => PatientRecord::tableName()],'{{a}}.id = {{b}}.patient_id');
        $query->leftJoin(['c' => Report::tableName()],'{{b}}.id = {{c}}.record_id');
        $query->leftJoin(['d' => SpotType::tableName()],'{{c}}.type = {{d}}.id');
        $query->leftJoin(['e' => OrganizationType::tableName()],'{{d}}.organization_type_id = {{e}}.id');
        $query->leftJoin(['f' => TriageInfo::tableName()],'{{b}}.id = {{f}}.record_id');
        $query->andWhere('patient_number != :patientNumber AND patient_number != :pNumber', [':patientNumber' => '0000000', ':pNumber' => '']);
//        $query->andWhere(['b.status'=>5]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([ 'a.id' => $this->id])
                ->andFilterWhere(['a.spot_id' => $this->parentSpotId])
                ->andFilterWhere(['like', 'a.patient_number', trim($this->patient_number)])
                ->andFilterWhere(['like', 'a.username', trim($this->username)])
                ->andFilterWhere(['like', 'a.card', trim($this->card)])
                ->andFilterWhere(['a.sex' => $this->sex])
                ->andFilterWhere(['a.iphone' => trim($this->iphone)]);
        
        $query->andFilterWhere(['like', 'b.case_id', trim($this->record_id)]);
        $query->andFilterWhere(['c.spot_id' => $this->record_spot_id]);
        $query->andFilterWhere(['c.doctor_id' => $this->doctor_id]);
        $query->andFilterWhere(['c.second_department_id' => $this->second_department_id]);
        $query->andFilterWhere(['e.id' => $this->type]);
        if ($this->search_start_date) {
            $query->andFilterCompare('a.diagnosis_time', strtotime($this->search_start_date), '>=');
        }
        if ($this->search_end_date) {
            $query->andFilterCompare('a.diagnosis_time', strtotime($this->search_end_date), '<=');
        }
        if ($this->start_birthday) {
            $query->andFilterCompare('a.birthday', strtotime($this->start_birthday), '>=');
        }
        if ($this->end_birthday) {
            $query->andFilterCompare('a.birthday', strtotime($this->end_birthday), '<=');
        }
        if ($this->record_start_time) {
            $query->andFilterCompare('f.diagnosis_time', strtotime($this->record_start_time), '>=');
        }
        if ($this->record_end_time) {
            $query->andFilterCompare('f.diagnosis_time', strtotime($this->record_end_time), '<=');
        }
        $query->groupBy('a.id');
        $query->orderBy('a.patient_number desc');
        return $dataProvider;
    }

}
