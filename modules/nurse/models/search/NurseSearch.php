<?php

namespace app\modules\nurse\models\search;

use app\modules\spot_set\models\SecondDepartment;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\patient\models\PatientRecord;
use app\modules\triage\models\TriageInfo;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use app\modules\report\models\Report;
use app\modules\make_appointment\models\Appointment;

class NurseSearch extends Patient
{
    public $time;//预约时间
    public $appointmentDepartment;//预约科室
    public $doctorName;//预约医生
    public $appointmentType;//预约服务类型
    public $remarks;//预约备注
    public $reportTime;//报到时间
    public $reportDepartment;//就诊科室
    public $reportDoctor;//接诊医生
    public $reportType;//服务类型
    public $status;//接诊状态
    public $date;//筛选日期
    public $patientId;//筛选日期
    public $appointmentId;//预约的ID
    public $doctor_chose;
    public $room_chose;
    public $record_type;
    public $illnessDescription;//病情自述
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'record_id', 'spot_id'], 'integer'],
            ['date', 'date'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        return Model::scenarios();
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        $parent_attribute = parent::attributeLabels();
        $new_attribute =  [
            'time' => '预约时间',
            'appointmentDepartment' => '预约科室',
            'doctorName' => '预约医生',
            'appointmentType' => '预约服务',
            'remarks' => '备注',
            'reportTime' => '报到时间',
            'reportDepartment' => '就诊科室',
            'reportDoctor' => '接诊医生',
            'reportType' => '服务类型',
            'status' => '状态',
            'illnessDescription'=>'病情自述'
        ];
        $arr=array_merge($parent_attribute,$new_attribute);
        return $arr;
    }


    /**
     * @param $params，目前暂不做分页
     * @return ActiveDataProvider 获取预约未到店人数信息
     */
    public function appointmentSearch($params,$doctorId) {
        $query = new \yii\db\ActiveQuery(NurseSearch::className());
        $query->from(['a' => Appointment::tableName()]);
        $query->select([
            'appointmentId'=>'a.id','a.time','a.remarks','a.record_id','illnessDescription'=>'a.illness_description','appointmentType' => 'b.type_description','b.status','patientId'=>'c.id','c.username','c.iphone','c.sex','c.birthday','c.patient_number','c.first_record',
           'appointmentDepartment' => 'd.name','doctorName' => 'e.username', 'f.pain_score','f.fall_score',

        ]);
        $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id={{b}}.id');
        $query->leftJoin(['c' => Patient::tableName()], '{{c}}.id={{b}}.patient_id');
        $query->leftJoin(['d' => SecondDepartment::tableName()], '{{a}}.second_department_id={{d}}.id');
        $query->leftJoin(['e' => User::tableName()], '{{a}}.doctor_id={{e}}.id');
        $query->leftJoin(['f' => TriageInfo::tableName()], '{{f}}.record_id={{b}}.id');
        $query->orderBy(['a.time' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'a.spot_id' => $this->spotId,
            'b.status' => 1,
            'e.id' => $doctorId
        ]);
        $nurseDate = $params['date']? $params['date']:date('Y-m-d');
        $query->andFilterWhere(['between', 'a.time', strtotime($nurseDate), strtotime($nurseDate) + 86400]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @param int $pageSize 目前暂不做分页
     * @return ActiveDataProvider 获取已到店人数信息
     */
    public function reportSearch($params,$doctorId){
        $query = new \yii\db\ActiveQuery(NurseSearch::className());
        $query->from(['a' => Report::tableName()]);
        $query->select([
            'reportTime' => 'a.create_time','reportType' => 'a.type_description','doctor_chose'=>'a.doctor_id','room_chose'=>'g.room_id','a.record_type',
            'record_id'=>'b.id', 'b.status','patientId'=>'c.id', 'c.username','c.iphone','c.sex','c.birthday','c.first_record','c.iphone','c.patient_number',
            'reportDepartment' => 'd.name','reportDoctor' => 'e.username',
            'f.time','g.pain_score','g.fall_score'
        ]);
        $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id={{b}}.id');
        $query->leftJoin(['c' => Patient::tableName()], '{{c}}.id={{b}}.patient_id');
        $query->leftJoin(['d' => SecondDepartment::tableName()], '{{a}}.second_department_id={{d}}.id');
        $query->leftJoin(['e' => User::tableName()], '{{a}}.doctor_id={{e}}.id');
        $query->leftJoin(['f' => Appointment::tableName()], '{{f}}.record_id={{b}}.id');
        $query->leftJoin(['g' => TriageInfo::tableName()], '{{g}}.record_id={{b}}.id');
        $query->where(['b.status' => [2,3,4,5]]);
        $query->orderBy(['a.create_time' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'a.spot_id' => $this->spotId,
            'e.id' => $doctorId
        ]);
        $nurseDate = $params['date']? $params['date']:date('Y-m-d');
        $query->andFilterWhere(['between','a.create_time', strtotime($nurseDate), strtotime($nurseDate) + 86400]);
        return $dataProvider;
    }

}
