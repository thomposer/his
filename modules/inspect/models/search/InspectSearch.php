<?php

namespace app\modules\inspect\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\inspect\models\Inspect;
use app\modules\patient\models\PatientRecord;
use app\modules\triage\models\TriageInfo;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use app\modules\spot_set\models\Room;
use app\modules\report\models\Report;

/**
 * InspectSearch represents the model behind the search form about `app\modules\inspect\models\Inspect`.
 */
class InspectSearch extends Inspect
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'record_id', 'spot_id', 'status', 'inspect_in_time', 'inspect_finish_time', 'create_time', 'status'], 'integer'],
            ['username', 'trim'],
            [['username'], 'safe'],
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
     * @param $params
     * @param int $pageSize
     * @return ActiveDataProvider 今日病人
     */
    public function search($params, $pageSize = 20) {
        $query = new \yii\db\ActiveQuery(InspectSearch::className());

        $query->from(['in' => InspectSearch::tableName()]);

        $query->select([
            'in.id', 'inspect_name' => 'group_concat(in.name)', 'status' => 'in.status', 'doctor_name' => 'u.username', 'rt.type_description', 'p.username', 'p.sex',
            'p.birthday', 'p.patient_number', 'in.record_id', 'room_name' => 'rm.clinic_name', 'ti.pain_score', 'ti.fall_score'
        ]);
        $query->leftJoin(['r' => PatientRecord::tableName()], '{{r}}.id={{in}}.record_id');
        $query->leftJoin(['ti' => TriageInfo::tableName()], '{{ti}}.record_id={{r}}.id');
        $query->leftJoin(['rt' => Report::tableName()], '{{rt}}.record_id={{r}}.id');
        $query->leftJoin(['u' => User::tableName()], '{{u}}.id={{rt}}.doctor_id');
        $query->leftJoin(['p' => Patient::tableName()], '{{p}}.id={{r}}.patient_id');
        $query->leftJoin(['rm' => Room::tableName()], '{{rm}}.id={{ti}}.room_id');
        if ($params['type'] == 5) {
            $subQuery = InspectSearch::find()->select('record_id')->where(['not in', 'status', [1,4]]);
            $query->orWhere(['not in', 'in.record_id', $subQuery]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [ 'in.id' => SORT_DESC],
                'attributes' => ['in.id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'in.spot_id' => $this->spot_id,
            'in.status' => $this->status,
            'r.delete_status' => 1,
        ]);
        if ($params['type'] == 3) {//今日病人
            $query->andFilterWhere(['between', 'in.create_time', strtotime(date('Y-m-d')), strtotime(date('Y-m-d')) + 86400]);
        }
        $query->andFilterWhere(['like', 'p.username', $this->username])
                ->andFilterWhere(['like', 'in.unit', $this->unit]);
        $query->groupBy(['in.record_id']);
        return $dataProvider;
    }

    /**
     * @param $params
     * @param int $pageSize
     * @return ActiveDataProvider  特需病人
     */
    public function specialSearch($params, $pageSize = 20) {
        $query = new \yii\db\ActiveQuery(InspectSearch::className());
        $query->from(['in' => InspectSearch::tableName()]);
        $query->select([
            'in.id', 'inspect_name' => 'group_concat(in.name)', 'status' => 'in.status', 'doctor_name' => 'u.username', 'rt.type_description', 'p.username', 'p.sex',
            'p.birthday', 'p.patient_number','in.record_id', 'room_name' => 'rm.clinic_name', 'ti.pain_score', 'ti.fall_score'
        ]);

        $query->leftJoin(['r' => PatientRecord::tableName()], '{{r}}.id={{in}}.record_id');
        $query->leftJoin(['ti' => TriageInfo::tableName()], '{{ti}}.record_id={{r}}.id');
        $query->leftJoin(['rt' => Report::tableName()], '{{rt}}.record_id={{r}}.id');
        $query->leftJoin(['u' => User::tableName()], '{{u}}.id={{rt}}.doctor_id');
        $query->leftJoin(['p' => Patient::tableName()], '{{p}}.id={{r}}.patient_id');
        $query->leftJoin(['rm' => Room::tableName()], '{{rm}}.id={{ti}}.room_id');

        //子查询  针对需求说如果需要全部完成   才算已经完成
//       if(isset($params['InspectSearch']['status']) && !empty($params['InspectSearch']['status']) && $params['InspectSearch']['status']!=1){
//        if ($params['type'] == 5) {
//            $subQuery = InspectSearch::find()->select('id')->where(['status' => 1]);
//            $query->andWhere(['not in', 'in.id', $subQuery]);
//        }
//        else{
//               $subQuery = InspectSearch::find()->select('record_id')->where(['<>','status',1]);
//               $specialQuery = InspectSearch::find()->select('record_id')->where(['between','inspect_finish_time',strtotime(date('Y-m-d')),strtotime(date('Y-m-d')." 23:59:59")])->andWhere(['=','status',1]);
////               $specialQuery = InspectSearch::find()->select('record_id')->where(['between','inspect_finish_time',strtotime(date('Y-m-d')),strtotime(date('Y-m-d')." 12:00:00")])->andWhere(['=','status',1]); //FIXME 这里为了测试，将测试时间改为12点，后续需要改回来
//               $query->andWhere(['in', 'in.record_id', $subQuery]);
//               $query->orWhere(['in', 'in.record_id', $specialQuery]);
//       }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['in.id' => SORT_ASC],
                'attributes' => ['in.id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'in.spot_id' => $this->spotId,
            'in.status' => $this->status,
            'r.delete_status' => 1
        ]);

        $query->andFilterWhere(['not between', 'in.create_time', strtotime(date('Y-m-d')), strtotime(date('Y-m-d')) + 86400]);
        $query->andFilterWhere(['not between', 'in.inspect_finish_time', 1, strtotime(date('Y-m-d'))]);
        $query->andFilterWhere(['like', 'p.username', $this->username]);
        $query->groupBy(['in.record_id']);
        return $dataProvider;
    }

}
