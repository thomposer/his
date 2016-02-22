<?php

namespace app\modules\behavior\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\behavior\models\BehaviorRecord;

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
            [['user_id', 'ip', 'spot', 'module', 'action', 'data', 'operation_time'], 'safe'],
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
        $query = BehaviorRecord::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize' => $pageSize]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'operation_time' => $this->operation_time,
        ]);

        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'spot', $this->spot])
            ->andFilterWhere(['like', 'module', $this->module])
            ->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'data', $this->data]);
        
        $query->addOrderBy(['operation_time' => SORT_DESC]);

        return $dataProvider;
    }
}
