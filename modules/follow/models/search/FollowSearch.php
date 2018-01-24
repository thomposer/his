<?php

namespace app\modules\follow\models\search;

use app\modules\spot\models\Spot;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\follow\models\Follow;
use app\modules\patient\models\PatientRecord;
use app\modules\patient\models\Patient;
use app\modules\user\models\User;
use app\modules\triage\models\TriageInfo;
use yii\db\Query;

/**
 * FollowSearch represents the model behind the search form about `app\modules\follow\models\Follow`.
 */
class FollowSearch extends Follow
{

   

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['record_id', 'patient_id', 'spot_id', 'follow_state','complete_time','patientNumber','planCreatorName','execute_role','follow_executor','follow_plan_executor'], 'integer'],
            [['content', 'follow_remark', 'cancel_reason','username','iphone','patientNumber'], 'safe'],
            [['username','iphone'],'string'],
            [['follow_begin_time','follow_end_time','diagnosis_begin_time','diagnosis_end_time'],'date'],
            ['follow_end_time','compare', 'operator'=>'>=', 'compareAttribute'=>'follow_begin_time'],
            ['diagnosis_end_time','compare', 'operator'=>'>=', 'compareAttribute'=>'diagnosis_begin_time'],
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
    public function search($params, $pageSize = 20, $patientId = null) {
        $query = new \yii\db\ActiveQuery(self::className());
        $query->from(['a' => self::tableName()]);
        $query->select([
            'a.id', 'a.record_id', 'a.patient_id', 'a.complete_time', 'a.execute_role', 'planCreatorName' => 'd.username', 'followPlanExecutorName' => 'f.username','a.content', 'followExecutorName' => 'e.username', 'a.follow_remark', 'a.follow_state', 'a.create_time',
            'patientNumber' => 'c.patient_number','h.diagnosis_time','c.username', 'c.birthday', 'c.sex', 'g.spot_name'
        ]);
        // $query->leftJoin(['b' => PatientRecord::tableName()], '{{b}}.id={{a}}.record_id');
        $query->leftJoin(['c' => Patient::tableName()], '{{c}}.id={{a}}.patient_id');
        $query->leftJoin(['d' => User::tableName()], '{{a}}.plan_creator={{d}}.id');
        $query->leftJoin(['e' => User::tableName()], '{{a}}.follow_executor={{e}}.id');
        $query->leftJoin(['f' => User::tableName()], '{{a}}.follow_plan_executor={{f}}.id');
        $query->leftJoin(['g' => Spot::tableName()], '{{a}}.spot_id={{g}}.id');
        $query->leftJoin(['h' => Triageinfo::tableName()], '{{a}}.record_id={{h}}.record_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [ 'a.complete_time' => SORT_DESC],
                'attributes' => ['a.id','a.complete_time']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        if ($this->follow_state) {
            if ($this->follow_state == 2 || $this->follow_state == 3) {//已随访
                $query->where(['a.follow_state' => [2,3]]);
            } else {
                $query->where(['a.follow_state' => $this->follow_state]);
            }
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'record_id' => $this->record_id,
            'patient_id' => $this->patient_id,
            'a.spot_id' => $this->spot_id,
            'c.iphone' => trim($this->iphone),
            'd.id' => $this->planCreatorName,
            'execute_role' => $this->execute_role,
            'follow_plan_executor' => $this->follow_plan_executor,
        ]);

        $query->andFilterWhere(['like', 'content', $this->content])
                ->andFilterWhere(['like', 'follow_remark', $this->follow_remark])
                ->andFilterWhere(['like', 'cancel_reason', $this->cancel_reason])
                ->andFilterWhere(['like', 'c.username', trim($this->username)])
                ->andFilterWhere(['like', 'c.patient_number', trim($this->patientNumber)]);
        if($this->follow_begin_time){
            $query->andFilterCompare('a.complete_time', strtotime($this->follow_begin_time),'>=');
        }
        if($this->follow_end_time){
            $query->andFilterCompare('a.complete_time', strtotime($this->follow_end_time)+86400,'<');
        }

        if($this->diagnosis_begin_time){
            $query->andFilterCompare('h.diagnosis_time', strtotime($this->diagnosis_begin_time),'>=');
        }
        if($this->diagnosis_end_time){
            $query->andFilterCompare('h.diagnosis_time', strtotime($this->diagnosis_end_time)+86400,'<');
        }

        $query->andFilterWhere(["patient_id" => $patientId]);

        return $dataProvider;
    }

    public function getFollowStateCount($spotId = null, $patientId = null) {
        $query = new Query();
        $query->from(['f' => Follow::tableName()]);
        $query->select(['followState' => 'f.follow_state', 'count' => 'count(f.follow_state)']);
        $query->groupBy(['follow_state']);
        $query -> andFilterWhere(['f.spot_id' => $spotId]);
        $query -> andFilterWhere(['f.patient_id' => $patientId]);
        $query->indexBy('followState');
        $result = $query->all();

        $result[0]['count'] = 0;
        foreach ($result as $value) {
            $result[0]['count'] += $value['count'];
        }
        if (!isset($result[2]["count"])) {
            $result[2]["count"] = 0;
        }
        if (isset($result[3]['count'])) {
            $result[2]['count'] = $result[2]['count'] + $result[3]['count'];
        }

        return $result;
    }
}
