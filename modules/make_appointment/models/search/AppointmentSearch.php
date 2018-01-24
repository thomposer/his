<?php

namespace app\modules\make_appointment\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\make_appointment\models\Appointment;
use yii\db\ActiveQuery;
use app\modules\patient\models\Patient;
use app\modules\spot_set\models\SecondDepartment;
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
            [['id', 'patient_id', 'record_id', 'type','status', 'second_department_id', 'time', 'doctor_id', 'create_time', 'update_time','appointment_operator'], 'integer'],
            [['remarks','username'], 'safe'],
            [['appointment_begin_time','appointment_end_time'],'date'],
            ['appointment_end_time','compare', 'operator'=>'>=', 'compareAttribute'=>'appointment_begin_time'],
            [['username','iphone','doctorName'],'string']
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
//         $query = Appointment::find();
//        print_r($params);exit();
        $query = new ActiveQuery(Appointment::className());
        $query->from(['a' => Appointment::tableName()]);

        $query->select(['a.id','a.appointment_origin','a.appointment_operator','a.time','a.remarks','a.illness_description','d.type','d.type_description','b.username','b.iphone','e.username as doctorName','a.record_id','b.sex','b.birthday','c.name as departmentName','d.status','firstRecord'=>'b.first_record']);
        $query->leftJoin(['d' => PatientRecord::tableName()],'{{a}}.record_id = {{d}}.id');
        $query->leftJoin(['b' => Patient::tableName()],'{{a}}.patient_id = {{b}}.id');
        $query->leftJoin(['c' => SecondDepartment::tableName()],'{{a}}.second_department_id = {{c}}.id');
        $query->leftJoin(['e'=>User::tableName()],'{{a.doctor_id}} = {{e.id}}');
        $query->where('a.spot_id = :spotId',[':spotId' => $this->spotId]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'time' => SORT_DESC
                ],
                'attributes' => ['id','time','status']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $status = 0;
        if($this->status == 8){
            $query->andWhere('(a.time <= '.strtotime(date("Y-m-d")).' and d.status = 1) or d.status = 8');
        }else if($this->status == 1){
            $query->andWhere('a.time >= '.strtotime(date("Y-m-d")).' and d.status = 1');
        }else{
            $query->andFilterWhere([
                'd.status'=> $this->status,
            ]);
        }
        $query->andFilterWhere([
            'a.id' => $this->id,
            'a.patient_id' => $this->patient_id,
            'a.record_id' => $this->record_id,
            'd.type' => $this->type,
            'a.second_department_id' => $this->second_department_id,
            'a.time' => $this->time,
            'a.doctor_id' => $this->doctor_id,
            'a.create_time' => $this->create_time,
            'a.update_time' => $this->update_time,
            'a.appointment_operator' => $this->appointment_operator
        ]);
        $query->andFilterWhere(['like','b.iphone',trim($this->iphone)]);
        $query->andFilterWhere(['like', 'a.remarks', $this->remarks]);
        $query->andFilterWhere(['like','b.username',trim($this->username)]);

        if($this->appointment_begin_time){
            $query->andFilterCompare('a.time', strtotime($this->appointment_begin_time),'>=');
        }
        if($this->appointment_end_time){
            $query->andFilterCompare('a.time', strtotime($this->appointment_end_time)+86400,'<=');
        }
        return $dataProvider;
    }
}
