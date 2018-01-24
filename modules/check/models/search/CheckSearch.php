<?php

namespace app\modules\check\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\check\models\Check;
use yii\db\ActiveQuery;
use app\modules\outpatient\models\CheckRecord;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use app\modules\user\models\User;
use app\modules\triage\models\TriageInfo;
use app\modules\spot_set\models\Room;
use app\modules\report\models\Report;

/**
 * CheckSearch represents the model behind the search form about `app\modules\check\models\Check`.
 */
class CheckSearch extends Check
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'record_id', 'spot_id', 'status', 'check_in_time', 'check_finish_time', 'create_time', 'update_time'], 'integer'],
            [['name', 'unit'], 'safe'],
            [['price'], 'number'],
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
        $query = new ActiveQuery(Check::className());
        $query->from(['a' => Check::tableName()]);
        $query->select(['c.pain_score', 'c.fall_score', 'id' => 'a.record_id', 'g.type_description', 'name' => 'group_concat(a.name)', 'patientName' => 'f.username', 'f.sex', 'f.birthday', 'doctorName' => 'd.username', 'e.clinic_name']);
        $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id = {{b}}.id');
        $query->leftJoin(['c' => TriageInfo::tableName()], '{{c}}.record_id = {{a}}.record_id');
        $query->leftJoin(['g' => Report::tableName()], '{{g}}.record_id={{b}}.id');
        $query->leftJoin(['d' => User::tableName()], '{{d}}.id = {{g}}.doctor_id');
        $query->leftJoin(['e' => Room::tableName()], '{{c}}.room_id = {{e}}.id');
        $query->leftJoin(['f' => Patient::tableName()], '{{f}}.id = {{b}}.patient_id');
        if ($params['type'] == 5) {
            $subQuery = check::find()->select('record_id')->where(['<>', 'status', 1]);
            $query->orWhere(['not in', 'a.record_id', $subQuery]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [ 'a.create_time' => SORT_DESC],
                'attributes' => ['a.create_time']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        if ($params['type'] == 3) {//今日病人
            $query->andFilterWhere(['between', 'a.create_time', strtotime(date('Y-m-d')), strtotime(date('Y-m-d')) + 86400]);
        }
        $query->andFilterWhere([
            'a.spot_id' => $this->spot_id,
            'a.status' => $this->status,
            'b.delete_status' => 1
        ]);

        $query->andFilterWhere(['like', 'f.username', $this->name]);
        $query->groupBy('a.record_id');
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function specialSearch($params, $pageSize = 20) {
        $query = new ActiveQuery(Check::className());
        $query->from(['a' => Check::tableName()]);
        $query->select(['c.pain_score', 'c.fall_score', 'id' => 'a.record_id', 'g.type_description', 'name' => 'group_concat(a.name)', 'patientName' => 'f.username', 'f.sex', 'f.birthday', 'doctorName' => 'd.username', 'e.clinic_name']);
        $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id = {{b}}.id');
        $query->leftJoin(['c' => TriageInfo::tableName()], '{{c}}.record_id = {{a}}.record_id');
        $query->leftJoin(['g' => Report::tableName()], '{{g}}.record_id={{b}}.id');
        $query->leftJoin(['d' => User::tableName()], '{{d}}.id = {{g}}.doctor_id');
        $query->leftJoin(['e' => Room::tableName()], '{{c}}.room_id = {{e}}.id');
        $query->leftJoin(['f' => Patient::tableName()], '{{f}}.id = {{b}}.patient_id');

        //子查询  针对需求说如果需要全部完成   才算已经完成
//        if(isset($params['CheckSearch']['status']) && !empty($params['CheckSearch']['status']) && $params['CheckSearch']['status']!=1){
//                $subQuery = check::find()->select('id')->where(['status' => 1]);
//                $query->andWhere(['not in', 'a.id', $subQuery]);
//        }else{
//                $subQuery = check::find()->select('id')->where(['status' => 1])->andWhere(['not between','update_time',strtotime(date('Y-m-d')),strtotime(date('Y-m-d')." 23:59:59")]);
//                $query->andWhere(['not in', 'a.id', $subQuery]);
//        }
        //$subQuery = check::find()->select('record_id')->where(['<>','status',1]);
        //$specialQuery = check::find()->select('record_id')->where(['between','check_finish_time',strtotime(date('Y-m-d')),strtotime(date('Y-m-d')." 23:59:59")]) ->andWhere(['=','status',1]);
//          $specialQuery = check::find()->select('record_id')->where(['between','check_finish_time',strtotime(date('Y-m-d')),strtotime(date('Y-m-d')." 12:00:00")]) ->andWhere(['=','status',1]);//FIXME 这里为了测试，将测试时间改为12点，后续需要改回来
//                       ->andWhere(['between','create_time',strtotime(date('Y-m-d')),strtotime(date('Y-m-d')." 23:59:59")]);
        //$query->andWhere(['in', 'a.record_id', $subQuery]);
        //$query->orWhere(['in', 'a.record_id', $specialQuery]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'attributes' => ['']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $query->andFilterWhere(['not between', 'a.create_time', strtotime(date('Y-m-d')), strtotime(date('Y-m-d')) + 86400]);
        $query->andFilterWhere(['not between', 'a.check_finish_time', 1, strtotime(date('Y-m-d'))]);
        $query->andFilterWhere([
            'a.spot_id' => $this->spot_id,
            'a.status' => $this->status,
            'b.delete_status' => 1,
        ]);
        $query->andFilterWhere(['like', 'f.username', trim($this->name)]);
        $query->groupBy('a.record_id');
        return $dataProvider;
    }

}
