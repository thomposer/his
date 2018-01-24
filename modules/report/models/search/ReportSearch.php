<?php

namespace app\modules\report\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\patient\models\Patient;
use yii\db\ActiveQuery;
use app\modules\report\models\Report;
use app\modules\patient\models\PatientRecord;

/**
 * PatientSearch represents the model behind the search form about `app\modules\patient\models\Patient`.
 */
class ReportSearch extends Report
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','username'],'trim'],
            [['id', 'spot_id','status'], 'integer'],
            [['username'], 'safe'],
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
        $query = new ActiveQuery(Report::className());
        $query->from(['a' => PatientRecord::tableName()]);
        $query->select(['a.id','c.username','c.sex','c.birthday','c.iphone','b.type','b.type_description','a.status','c.patient_number', 'firstRecord' => 'c.first_record']);
        $query->leftJoin(['b' => Report::tableName()],'{{a}}.id = {{b}}.record_id');
        $query->leftJoin(['c' => Patient::tableName()],'{{a}}.patient_id = {{c}}.id');
        $query->where(['between','b.create_time',strtotime(date('Y-m-d',time())),strtotime(date('Y-m-d',time()))+86400]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' =>[
                'attributes' =>['']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'a.spot_id' => $this->spot_id,
            'c.type' => $this->type,
        ]);
        $query->andFilterWhere(['not in','a.status',[1,7]]);
        $query->andFilterWhere(['like', 'c.username', $this->username]);

        return $dataProvider;
    }
}
