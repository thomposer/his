<?php

namespace app\modules\cure\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\cure\models\Cure;
use app\modules\patient\models\PatientRecord;
use app\modules\triage\models\TriageInfo;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use app\modules\spot_set\models\Room;
use app\modules\report\models\Report;

/**
 * CureSearch represents the model behind the search form about `app\modules\cure\models\Cure`.
 */
class CureSearch extends Cure
{

    public function rules() {
        return [
            [['username'], 'string'],
            [['iphone', 'status', 'cure_in_time', 'cure_finish_time'], 'integer'],
        ];
    }

    public function search($params, $pageSize = 20) {
        $query = new \yii\db\ActiveQuery(Cure::className());
        $query->from(['pr' => Cure::tableName()]);
        $query->select([
            'cure_name' => 'pr.name', 'cure_status' => 'pr.status', 'doctor_name' => 'u.username', 'rt.type_description', 'p.username', 'p.sex',
            'p.birthday', 'pr.record_id', 'room_name' => 'rm.clinic_name', 'ti.pain_score', 'ti.fall_score'
        ]);
        $query->leftJoin(['r' => PatientRecord::tableName()], '{{r}}.id={{pr}}.record_id');
        $query->leftJoin(['ti' => TriageInfo::tableName()], '{{ti}}.record_id={{r}}.id');
        $query->leftJoin(['rt' => Report::tableName()], '{{rt}}.record_id={{r}}.id');
        $query->leftJoin(['u' => User::tableName()], '{{u}}.id={{rt}}.doctor_id');
        $query->leftJoin(['p' => Patient::tableName()], '{{p}}.id={{r}}.patient_id');
        $query->leftJoin(['rm' => Room::tableName()], '{{rm}}.id={{ti}}.room_id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['pr.id' => SORT_DESC],
                'attributes' => ['pr.id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'pr.spot_id' => $this->spotId,
            'pr.status' => $this->status,
            'r.delete_status' => 1
        ]);
        $query->andFilterWhere(['between', 'pr.create_time', strtotime(date('Y-m-d')), strtotime(date('Y-m-d')) + 86400]);
        $query->andFilterWhere(['like', 'p.username', $this->username]);
        $query->groupBy(['pr.record_id']);
        return $dataProvider;
    }

    /*
     * 特需病人
     */

    public function specialSearch($params, $pageSize = 20) {
        $query = new \yii\db\ActiveQuery(Cure::className());
        $query->from(['pr' => Cure::tableName()]);
        $query->select([
            'cure_name' => 'pr.name', 'cure_status' => 'pr.status', 'doctor_name' => 'u.username', 'rt.type_description', 'p.username', 'p.sex',
            'p.birthday', 'pr.record_id', 'room_name' => 'rm.clinic_name', 'ti.pain_score', 'ti.fall_score'
        ]);
        $query->leftJoin(['r' => PatientRecord::tableName()], '{{r}}.id={{pr}}.record_id');
        $query->leftJoin(['ti' => TriageInfo::tableName()], '{{ti}}.record_id={{r}}.id');
        $query->leftJoin(['rt' => Report::tableName()], '{{rt}}.record_id={{r}}.id');
        $query->leftJoin(['u' => User::tableName()], '{{u}}.id={{rt}}.doctor_id');
        $query->leftJoin(['p' => Patient::tableName()], '{{p}}.id={{r}}.patient_id');
        $query->leftJoin(['rm' => Room::tableName()], '{{rm}}.id={{ti}}.room_id');
        //子查询
//        $subQuery = new \yii\db\Query();
//        if(isset($params['CureSearch']['status']) && !empty($params['CureSearch']['status']) && $params['CureSearch']['status']!=1){
//                $subQuery = Cure::find()->select('id')->where(['status' => 1]);
//                $query->andWhere(['not in', 'pr.id', $subQuery]);
//
//        }else{
//                $subQuery = Cure::find()->select('id')->where(['status' => 1])->andWhere(['not between','update_time',strtotime(date('Y-m-d')),strtotime(date('Y-m-d')." 23:59:59")]);
//                $query->andWhere(['not in', 'pr.id', $subQuery]);
//        }
//        $subQuery = Cure::find()->select('record_id')->where(['<>','status',1]);
//        $specialQuery = Cure::find()->select('record_id')->where(['between','cure_finish_time',strtotime(date('Y-m-d')),strtotime(date('Y-m-d')." 23:59:59")])->andWhere(['=','status',1]);
////        $specialQuery = Cure::find()->select('record_id')->where(['between','cure_finish_time',strtotime(date('Y-m-d')),strtotime(date('Y-m-d')." 12:00:00")])->andWhere(['=','status',1]);//FIXME 这里为了测试，将测试时间改为12点，后续需要改回来
////                       ->andWhere(['between','create_time',strtotime(date('Y-m-d')),strtotime(date('Y-m-d')." 23:59:59")]);
//        $query->andWhere(['in', 'pr.record_id', $subQuery]);
//        $query->orWhere(['in', 'pr.record_id', $specialQuery]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['pr.id' => SORT_DESC],
                'attributes' => ['pr.id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'pr.spot_id' => $this->spotId,
            'pr.status' => $this->status,
            'r.delete_status' => 1
        ]);

        $query->andFilterWhere(['not between', 'pr.create_time', strtotime(date('Y-m-d')), strtotime(date('Y-m-d')) + 86400]);
        $query->andFilterWhere(['not between', 'pr.cure_finish_time', 1, strtotime(date('Y-m-d'))]);
        $query->andFilterWhere(['like', 'p.username', trim($this->username)]);
        $query->groupBy(['pr.record_id']);
        return $dataProvider;
    }

}
