<?php

namespace app\modules\charge\models\search;

use app\modules\charge\models\Charge;
use app\modules\charge\models\ChargeInfo;
use app\modules\charge\models\ChargeRecord;
use app\modules\patient\models\Patient;
use app\modules\report\models\Report;
use app\modules\triage\models\TriageInfo;
use app\modules\user\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use app\modules\patient\models\PatientRecord;

/**
 * SearchCharge represents the model behind the search form about `app\modules\charge\models\Charge`.
 */
class ChargeSearch extends Charge
{

    public $unit_price;
    public $num;
    public $search_begin_time;
    public $search_end_time;
    public $search_doctor_name;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['username', 'search_doctor_name'], 'string'],
            [['iphone'], 'integer'],
            [['num','unit_price'],'safe'],
            [['search_begin_time', 'search_end_time'],'date']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 20) {
//        $query = Charge::find();
        $query = new ActiveQuery(ChargeSearch::className());
        $query->from(['a' => Charge::tableName()]);
        $query->select([
            'a.id', 'a.patient_id','h.type_description', 'patients_type' => 'a.type',
            'd.username', 'a.price',
            'diagnosis_doctor' => 'b.username',
            'd.sex', 'd.birthday','d.iphone',
            'g.diagnosis_time',
            'unit_price' => 'group_concat(e.unit_price)','num' => 'group_concat(e.num)',
            'firstRecord'=>'d.first_record'
        ]);
        $query->leftJoin(['d' => Patient::tableName()], '{{a}}.patient_id={{d}}.id');
        $query->leftJoin(['g' => TriageInfo::tableName()], '{{a}}.id={{g}}.record_id');
        $query->leftJoin(['h' => Report::tableName()], '{{h}}.record_id={{a}}.id');
        $query->leftJoin(['b' => User::tableName()], '{{h}}.doctor_id={{b}}.id');
        $query->leftJoin(['e' => ChargeInfo::tableName()],'{{a}}.id = {{e}}.record_id');
        $query->where(['e.status' => 0]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['a.end_time' => SORT_DESC],
                'attributes' => ['a.end_time']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'a.spot_id' => $this->spot_id,
            'a.status' => [5,6],
            'a.delete_status' => 1,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);
        $query->andFilterWhere(['like', 'd.username', trim($this->username)]);
        $query->andFilterWhere(['like', 'd.iphone', trim($this->iphone)]);
        $query->andFilterWhere(['like', 'b.username', trim($this->search_doctor_name)]);
        if ($this->search_begin_time) {
            $query->andFilterCompare('g.diagnosis_time', strtotime($this->search_begin_time), '>=');
        }
        if ($this->search_end_time) {
            $query->andFilterCompare('g.diagnosis_time', strtotime($this->search_end_time) + 86400, '<=');
        }
        $query->andFilterWhere(['like', 'd.iphone', trim($this->iphone)]);
        $query->groupBy(['a.id']);
        return $dataProvider;
    }

    public function chargeRecord($params, $pageSize = 20) {
        $query = new ActiveQuery(Charge::className());
        $query->from(['b' => PatientRecord::tableName()]);
        $query->select([
            'b.id','d.iphone', 'a.patient_id','price' =>'sum(a.price)', 'charge_time' => 'max(a.create_time)', 'd.username', 'd.sex', 'd.birthday','firstRecord'=>'d.first_record','t.diagnosis_time','diagnosis_doctor' => 'u.username'
        ]);
        $query->leftJoin(['a' => ChargeRecord::tableName()],'{{b}}.id = {{a}}.record_id');
        $query->leftJoin(['d' => Patient::tableName()], '{{a}}.patient_id={{d}}.id');
        $query->leftJoin(['t' => TriageInfo::tableName()], '{{b}}.id={{t}}.record_id');
        $query->leftJoin(['r' => Report::tableName()], '{{r}}.record_id={{b}}.id');
        $query->leftJoin(['u' => User::tableName()], '{{r}}.doctor_id={{u}}.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['charge_time' => SORT_DESC],
                'attributes' => ['charge_time']
            ]
        ]);
        $type = (isset($params['type']) && !empty($params['type'])) ? $params['type'] : 4;
        $status = $type == 4 ? 1 : 3;
        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'a.spot_id' => $this->spot_id,
            'a.status' => $status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);
        $query->andFilterWhere(['like', 'd.username', trim($this->username)]);
        $query->andFilterWhere(['like', 'd.iphone', trim($this->iphone)]);
        $query->andFilterWhere(['like', 'u.username', trim($this->search_doctor_name)]);
        if ($this->search_begin_time) {
            $query->andFilterCompare('t.diagnosis_time', strtotime($this->search_begin_time), '>=');
        }
        if ($this->search_end_time) {
            $query->andFilterCompare('t.diagnosis_time', strtotime($this->search_end_time) + 86400, '<=');
        }
        $query->groupBy('id');
        return $dataProvider;
    }

    public function getSearchTimeLabel($timeType, $type) {
        $label = $timeType == 1 ? "开始" : "结束";
        if ($type == 4) {
            return "请选择接诊" . $label . "时间";
        } else if ($type == 5) {
            return "请选择接诊" . $label . "时间";
        } else {
            return "请选择接诊" . $label . "时间";
        }
    }
}
