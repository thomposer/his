<?php

namespace app\modules\charge\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\charge\models\ChargeRecordLog;
use app\modules\charge\models\ChargeRecord;
use app\modules\spot\models\Spot;
use app\modules\patient\models\PatientRecord;

/**
 * chargeRecordLogSearch represents the model behind the search form about `app\modules\charge\models\chargeRecordLog`.
 */
class ChargeRecordLogSearch extends ChargeRecordLog
{
    public $trade_begin_time; //开始时间
    public $trade_end_time; //结束时间
    public $spot_name; //诊所名称
    public $case_id; //门诊号
    public $patient_id;//患者id
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels["pay_type"] = "支付方式";
        return $labels;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'record_id', 'diagnosis_time', 'create_time', 'update_time', 'doctor_id','case_id','patient_id'], 'integer'],
            [['out_trade_no', 'username', 'sex', 'age', 'doctor_name', 'record_type', 'type'], 'safe'],
            [['trade_begin_time', 'trade_end_time'], 'date'],
            [['doctor_name','pay_type'], 'string'],
            [['price'], 'number'],
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
    public function search($params, $pageSize = 20)
    {
        $query = ChargeRecordLogSearch::find()->select(['id', 'username', 'sex', 'age', 'diagnosis_time', 'doctor_name', 'type_description', 'create_time', 'pay_type', 'type', 'price']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => ['id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'spot_id' => $this->spot_id,
            'record_id' => $this->record_id,
            'price' => $this->price,
            'diagnosis_time' => $this->diagnosis_time,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'doctor_id' => $this->doctor_id,
            'type' => $this->type,
            'pay_type' => $this->pay_type
        ]);

        $query->andFilterWhere(['like', 'username', trim($this->username)])
            ->andFilterWhere(['like', 'doctor_name', trim($this->doctor_name)])
            ->andFilterWhere(['like', 'type_description', trim($this->type_description)]);


        if ($this->trade_begin_time) {
            $query->andFilterCompare('create_time', strtotime($this->trade_begin_time), '>=');
        }
        if ($this->trade_end_time) {
            $query->andFilterCompare('create_time', strtotime($this->trade_end_time) + 86400, '<=');
        }

        return $dataProvider;
    }

    /**
     * 搜索查询用户在当前机构下所有诊所的收费信息
     *
     * @param array $params 搜索参数
     * @param int $pageSize 分页大小
     * @param int $patientId 患者ID
     * @return 返回含有收费信息的ActiveDataProvider
     */
    
    public function searchByPatient($params, $pageSize = 20){
        $query = ChargeRecordLogSearch::find();
        $query->from(['a'=>chargeRecordLogSearch::tableName()]);
        $query->select(['a.id', 'a.username', 'a.sex', 'a.age', 'a.diagnosis_time', 'a.doctor_name', 'a.type_description', 'a.create_time', 'a.pay_type', 'a.type', 'a.price','c.spot_name','d.case_id','d.spot_id']);
        $query->leftJoin(['c'=>Spot::tableName()],'{{a.spot_id}} = {{c.id}}');
        $query->leftJoin(['d'=>PatientRecord::tableName()],'{{a.record_id}} = {{d.id}}');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => ['id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'a.record_id' => $this->record_id,
            'a.price' => $this->price,
            'a.diagnosis_time' => $this->diagnosis_time,
            'a.doctor_id' => $this->doctor_id,
            'a.type' => $this->type,
            'a.pay_type' => $this->pay_type,
            'd.patient_id' => $this->patient_id
        ]);
        $query->andFilterWhere(['like', 'a.username', trim($this->username)])
            ->andFilterWhere(['like', 'a.doctor_name', trim($this->doctor_name)])
            ->andFilterWhere(['like', 'a.type_description', trim($this->type_description)])
            ->andFilterWhere(['like', 'd.case_id', trim($this->case_id)]);
            // var_dump($query);

        if ($this->trade_begin_time) {
            $query->andFilterCompare('a.create_time', strtotime($this->trade_begin_time), '>=');
        }
        if ($this->trade_end_time) {
            $query->andFilterCompare('a.create_time', strtotime($this->trade_end_time) + 86400, '<=');
        }

        return $dataProvider;
    }
}
