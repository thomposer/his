<?php

namespace app\modules\report\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\make_appointment\models\Appointment;
use yii\db\ActiveQuery;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use app\modules\user\models\User;

/**
 * AppointmentSearch represents the model behind the search form about `app\modules\make_appointment\models\Appointment`.
 */
class AppointmentSearch extends Appointment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['iphone','username'], 'trim'],
            [['id', 'patient_id', 'record_id', 'type', 'second_department_id', 'time', 'doctor_id', 'create_time', 'update_time'], 'integer'],
            [['iphone','username'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
    public function search($params,$pageSize = 20)
    {
        $query = new ActiveQuery(Appointment::className());
        $query->from(['a' => Appointment::tableName()]);
        $query->select(['b.username','b.iphone','b.birthday','a.time','a.patient_id','a.record_id','c.type_description','c.status','doctorName' => 'd.username','b.sex', 'b.birthday', 'firstRecord' => 'b.first_record']);
        $query->leftJoin(['b' => Patient::tableName()],'{{a}}.patient_id = {{b}}.id');
        $query->leftJoin(['c' => PatientRecord::tableName()],'{{a}}.record_id = {{c}}.id');
        $query->leftJoin(['d' => User::tableName()], '{{a}}.doctor_id={{d}}.id');
        $query->where(['c.status' => PatientRecord::$setStatus[1]]);
        $query->andWhere('a.time >= :begin_time',[':begin_time' => strtotime(date('Y-m-d',time()))]);
        $query->andWhere('a.time <= :end_time',[':end_time' => strtotime(date('Y-m-d',time()))+86400]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
//                 'defaultOrder' => ['id' => SORT_ASC],
                'attributes' => ['time']
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'b.id' => $this->id,
//             'b.iphone' => $this->iphone,
            'a.spot_id'=>$this->spotId
        ]);

        $query->andFilterWhere(['like', 'b.iphone', trim($this->iphone)]);
        $query->andFilterWhere(['like', 'b.username', trim($this->username)]);
        return $dataProvider;
    }
}
