<?php

namespace app\modules\behavior\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\behavior\models\BehaviorRecord;
use yii\db\ActiveQuery;
use app\modules\user\models\User;
use app\modules\spot\models\Spot;

/**
 * BehaviorRecordSearch represents the model behind the search form about `app\modules\behavior\models\BehaviorRecord`.
 */
class BehaviorRecordSearch extends BehaviorRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['username', 'ip', 'spot_id', 'module', 'action', 'data', 'operation_time'], 'trim'],
            [['user_id', 'ip', 'spot_id', 'module', 'action', 'data', 'operation_time'], 'safe'],
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
    public function search($params, $pageSize = 10)
    {
        $query = new ActiveQuery(BehaviorRecord::className());
        $query->from(['a' => BehaviorRecord::tableName()]);
        $query->select(['a.id','a.ip','a.module','a.action','a.operation_time','b.username','c.spot_name','a.data']);
        $query->leftJoin(['b' => User::tableName()],'{{a}}.user_id = {{b}}.id');
        $query->leftJoin(['c' => Spot::tableName()],'{{a}}.spot_id = {{c}}.id');
        $dataProvider = new ActiveDataProvider([
            'db' => Yii::$app->get('db'),
            'query' => $query,
        	'pagination' => ['pageSize' => $pageSize],
            'sort' => [
                'attributes' => ['operation_time']
            ]
        ]);
    
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'a.operation_time' => $this->operation_time,
            'a.spot_id' => $this->spot_id
        ]);

        $query->andFilterWhere(['like', 'b.username', trim($this->username)])
            ->andFilterWhere(['like', 'a.ip', trim($this->ip)])
            ->andFilterWhere(['like', 'a.module', trim($this->module)])
            ->andFilterWhere(['like', 'a.action', trim($this->action)])
            ->andFilterWhere(['like', 'a.data', trim($this->data)]);
        
        $query->addOrderBy(['a.operation_time' => SORT_DESC]);

        return $dataProvider;
    }
}
