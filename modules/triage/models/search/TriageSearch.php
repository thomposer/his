<?php

namespace app\modules\triage\models\search;

use app\modules\patient\models\PatientRecord;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\triage\models\Triage;
use yii\db\ActiveQuery;
use app\modules\make_appointment\models\Appointment;
use app\modules\patient\models\Patient;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use app\modules\triage\models\TriageInfo;
use app\modules\report\models\Report;

/**
 * TriageSearch represents the model behind the search form about `app\modules\triage\models\Triage`.
 */
class TriageSearch extends Triage
{
    public $second_department_id;//二级科室ID
    public $doctor_id;
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'patient_id', 'status', 'create_time', 'update_time','second_department_id','doctor_id'], 'integer'],
            [['username', 'arrival_time', 'iphone'], 'safe']
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
//        $query = Triage::find();
        $query = new ActiveQuery(Triage::className());

        $query->from(['a' => Triage::tableName()]);

        $query->select([
            'a.id', 'a.patient_id','a.status','appointment_doctor'=>'b.doctor_id', 'b.time', 'username'=>'d.username','iphone'=>'d.iphone', 'department_name' => 'e.name',
            'arrival_time' => 'c.create_time',
            'doctor_name' => 'f.username', 'user_sex' => 'd.sex', 'd.birthday',
            'doctor_chose' => 'c.doctor_id', 'room_chose' => 'g.room_id',
            'g.temperature', 'g.breathing', 'g.pulse', 'g.shrinkpressure', 'g.diastolic_pressure','g.pain_score','g.fall_score',
            'firstRecord' => 'd.first_record'
        ]);
        $query->leftJoin(['c' => Report::tableName()], '{{a}}.id={{c}}.record_id');
        $query->leftJoin(['d' => Patient::tableName()], '{{a}}.patient_id={{d}}.id');
        $query->leftJoin(['g' => TriageInfo::tableName()], '{{a}}.id={{g}}.record_id');
        $query->leftJoin(['b' => Appointment::tableName()], '{{a}}.id={{b}}.record_id');
        $query->leftJoin(['f' => User::tableName()], '{{c}}.doctor_id={{f}}.id');
        $query->leftJoin(['e' => SecondDepartment::tableName()], '{{c}}.second_department_id={{e}}.id');
        $type = (isset($params['type']) && !empty($params['type'])) ? $params['type'] : 3;
        if ($type == 4) {
            $query->where('a.status in(3,4,5)');
            $query->orWhere('a.status = 9 and g.triage_time > 0');
        } else {
            $query->where('a.status = :type', [':type' => 2]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'arrival_time' => SORT_DESC
                ],
                'attributes' => ['id','arrival_time']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'a.id' => $this->id,
            'a.patient_id' => $this->patient_id,
            'a.spot_id' => $this->spotId, //当前诊所下的患者
            'a.delete_status' => 1,
            'c.second_department_id' => $this->second_department_id,
            'c.doctor_id' => $this->doctor_id,
        ]);

        $query->andFilterWhere(['like', 'd.username',trim($this->username)]);

        if(isset($this->arrival_time) && !empty($this->arrival_time)){
            $query->andFilterWhere(['between','c.create_time',strtotime($this->arrival_time),strtotime($this->arrival_time)+86400]);
        }

//         if(isset($this->iphone) && !empty($this->iphone)){
        $query->andFilterWhere(['like', 'd.iphone', trim($this->iphone)]);
//         }
        

        return $dataProvider;
    }

}
