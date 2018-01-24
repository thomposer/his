<?php

namespace app\modules\outpatient\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\outpatient\models\Outpatient;
use yii\db\ActiveQuery;
use app\modules\report\models\Report;
use app\modules\patient\models\Patient;
use app\modules\triage\models\TriageInfo;
use app\modules\user\models\User;
use app\modules\spot_set\models\Room;
use app\modules\make_appointment\models\Appointment;
use yii\data\ArrayDataProvider;
use app\modules\spot_set\models\SecondDepartment;

/**
 * OutpatientSearch represents the model behind the search form about `app\modules\outpatient\models\Outpatient`.
 */
class OutpatientSearch extends Outpatient
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['username'], 'string'],
            [['id', 'patient_id', 'status', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function searchInfo($params, $pageSize = 20) {
        //医生门诊
        $outData = [];
        if (empty($params['OutpatientSearch']['status']) || in_array($params['OutpatientSearch']['status'], [3, 4, 5])) {
            $outData = $this->outSearch($params, 1,$pageSize);
            foreach ($outData as $k => $v) {
                if ($v['record_status'] < 5) {
                    continue;
                } else {
                    $pendingReport = Yii::$app->cache->get(Yii::getAlias('@pendingReportNum') . $this->spotId . '_' . $v['id']); //待出报告
                    $allReportTime = Yii::$app->cache->get(Yii::getAlias('@allReportTime') . $this->spotId . '_' . $v['id']); //全部报告已出时间

                    //病历类型是正畸初诊，则时间延长5天
                    $triageTime = $v['record_type'] == 6?strtotime(date('Y-m-d',$v['triage_time'])) + 4*86400:$v['triage_time'];
                    $endTime = $v['record_type'] == 6?strtotime(date('Y-m-d',$v['end_time'])) + 4*86400:$v['end_time'];


                    if($pendingReport == 0){
                        //报告全部已出且有报告全部已出时间的判断是否已超过设定时间（正畸初诊5天，其他病历类型1天），超过则过滤
                        if($allReportTime){
                            $allReportPushTime = $v['record_type'] == 6?strtotime(date('Y-m-d',$allReportTime)) + 4*86400:$allReportTime;
                            if ( $allReportPushTime < strtotime(date("Y-m-d")) && $triageTime < strtotime(date("Y-m-d")) && $endTime < strtotime(date("Y-m-d"))) {//结束就诊
                                unset($outData[$k]);
                            }
                        }else{
                            //报告全部已出但没有报告全部已出时间的判断为历史数据，保持原来的逻辑
                            if ($triageTime < strtotime(date("Y-m-d")) && $endTime < strtotime(date("Y-m-d"))) {//结束就诊
                                unset($outData[$k]);
                            }
                        }
                    }

                }
            }

        }
        //已预约
        $appointmentData = [];
        if (empty($params['OutpatientSearch']['status']) || $params['OutpatientSearch']['status'] == 1) {
            $appointmentData = $this->outSearch($params, 2,$pageSize);
        }
        //已报到
        $reportData = [];
        if (empty($params['OutpatientSearch']['status']) || $params['OutpatientSearch']['status'] == 2) {
            $reportData = $this->outSearch($params, 3,$pageSize);
        }
        $data = array_merge($outData, $reportData, $appointmentData);
//        print_r($data);exit;
        $provider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
//            'sort' => [
//                'attributes' => ['id', 'name'],
//            ],
        ]);
        return $provider;
    }

    public function outSearch($params, $type,$pageSize = 20) {
        $query = new \yii\db\Query();
        $query->from(['a' => self::tableName()]);
        $fields = [
            'a.id', 'a.patient_id', 'record_status' => 'a.status', 'patients_type' => 'a.type', 'd.first_record', 'a.end_time', 'a.spot_id',
            'd.username', 'd.patient_number','chose_room' => 'e.clinic_name', 'reg_time' => 'c.time', 'appointment_id' => 'c.id',
            'user_sex' => 'd.sex', 'd.birthday', 'phone' => 'd.iphone', 'g.triage_time',
            'room_chose' => 'g.room_id', 'g.diagnosis_time', 'second_department' => 'h.name',
            'g.diagnosis_time','f.record_type','g.pain_score', 'g.fall_score'
        ];
        $query->leftJoin(['c' => Appointment::tableName()], '{{a}}.id={{c}}.record_id');
        $query->leftJoin(['d' => Patient::tableName()], '{{a}}.patient_id={{d}}.id');
        $query->leftJoin(['g' => TriageInfo::tableName()], '{{a}}.id={{g}}.record_id');
        $query->leftJoin(['e' => Room::tableName()], '{{g}}.room_id={{e}}.id');
        $query->leftJoin(['f' => Report::tableName()], '{{f}}.record_id={{a}}.id');
        if ($type == 2) {
            $query->leftJoin(['h' => SecondDepartment::tableName()], '{{h}}.id={{c}}.second_department_id');
        } else {
            $query->leftJoin(['h' => SecondDepartment::tableName()], '{{h}}.id={{f}}.second_department_id');
        }
        $nowTime = strtotime(date('Y-m-d'));
        $this->load($params);
        if ($type == 1) {//门诊
            $andWhere = [
//                'or',
//                ['between', 'g.triage_time', $nowTime, $nowTime + 86400],
//                ['between', 'a.end_time', $nowTime, $nowTime + 86400],
                'and',
                ['a.status' => [3, 4, 5]],
//                ['>', 'g.triage_time', strtotime('2017-10-10')]
            ];
            $query->andFilterWhere([
                'a.spot_id' => $this->spotId,
            ]);
            $query->andFilterWhere([
                'a.status' => $this->status,
            ]);
            $query->andFilterWhere(['!=', 'a.status', 9]);
            $query->andFilterWhere($andWhere);
            if (!$params['isSuperSystem']) {
                $query->andFilterWhere([ 'f.doctor_id' => $params['this_user_id']]);
            }
            $orderBy = ['g.triage_time' => SORT_DESC];
            $fields[] = 'f.type_description';
        } elseif ($type == 2) {//已预约
//             $query->andFilterCompare('c.time', strtotime(date("Y-m-d")), '>=');
            $query->andFilterWhere(['between', 'c.time', strtotime(date("Y-m-d")), strtotime(date("Y-m-d")) + 86400]);
            $query->andFilterWhere(['a.status' => 1]);
            $query->andFilterWhere([
                'a.spot_id' => $this->spotId,
            ]);
            if (!$params['isSuperSystem']) {
                $query->andFilterWhere([ 'c.doctor_id' => $params['this_user_id']]);
            }
            $orderBy = ['c.time' => SORT_DESC];
            $fields[] = 'a.type_description';
        } elseif ($type == 3) {//已报到
            $query->andFilterWhere(['a.status' => 2]);
            $query->andFilterWhere([
                'a.spot_id' => $this->spotId,
            ]);
            if (!$params['isSuperSystem']) {
                $query->andFilterWhere([ 'f.doctor_id' => $params['this_user_id']]);
            }
            $query->andFilterWhere(['between', 'f.create_time', strtotime(date("Y-m-d")), strtotime(date("Y-m-d")) + 86400]);
            $orderBy = ['f.create_time' => SORT_DESC];
//            array_push($fields, 'f.type_description');
            $fields[] = 'f.type_description';
        }
        $query->select($fields);
        $query->andFilterWhere(['a.delete_status' => 1]);
        $query->andFilterWhere(['like', 'd.username', trim($this->username)]);
        $query->orderBy($orderBy);
        $query->limit(60);
        $data = $query->all();
        return $data;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 20) {
//        $query = Outpatient::find();

        $query = new ActiveQuery(Outpatient::className());
        $query->from(['a' => Outpatient::tableName()]);
        $query->select([
            'a.id', 'a.patient_id', 'record_status' => 'a.status', 'd.first_record', 'c.type_description',
            'd.username','d.patient_number','chose_room' => 'e.clinic_name', 'reg_time' => 'c.create_time',
            'user_sex' => 'd.sex', 'd.birthday', 'phone' => 'd.iphone',
            'room_chose' => 'g.room_id', 'g.diagnosis_time', 'g.triage_time',
            'g.pain_score', 'g.fall_score'
        ]);
        $query->leftJoin(['c' => Report::tableName()], '{{a}}.id={{c}}.record_id');
        $query->leftJoin(['d' => Patient::tableName()], '{{a}}.patient_id={{d}}.id');
        $query->leftJoin(['g' => TriageInfo::tableName()], '{{a}}.id={{g}}.record_id');
        $query->leftJoin(['e' => Room::tableName()], '{{g}}.room_id={{e}}.id');
        $type = (isset($params['type']) && !empty($params['type'])) ? $params['type'] : 3;
        $nowTime = strtotime(date('Y-m-d'));
        if ($type == 4) {//历史
            $andWhere = [
                'and',
                ['<', 'a.end_time', strtotime(date('Y-m-d'))],
                ['a.status' => 5]
            ];
        } else {
            $andWhere = [
                'or',
                ['between', 'g.triage_time', $nowTime, $nowTime + 86400],
                ['between', 'a.end_time', $nowTime, $nowTime + 86400],
                ['a.status' => [3, 4]],
            ];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['g.triage_time' => SORT_DESC],
                'attributes' => ['g.triage_time']
            ]
        ]);


        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'patient_id' => $this->patient_id,
//            'a.status' => [3, 4, 5],
            'a.spot_id' => $this->spotId,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);
        $query->andFilterWhere($andWhere);
        if (!$params['isSuperSystem']) {
            $query->andFilterWhere([ 'g.doctor_id' => $params['this_user_id']]);
        }
        $query->andFilterWhere(['like', 'd.username', trim($this->username)]);
        return $dataProvider;
    }

}
