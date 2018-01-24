<?php

namespace app\modules\triage\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\triage\models\NursingRecord;

/**
 * NursingRecordSearch represents the model behind the search form about `app\modules\triage\models\NursingRecord`.
 */
class NursingRecordSearch extends NursingRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'creater_id', 'execute_time', 'create_time', 'update_time'], 'integer'],
            [['executor', 'name', 'content'], 'safe'],
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
    public function search($params,$pageSize = 20,$recordId)
    {
        $query = NursingRecord::find()->select(['id','name','executor','create_time']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => $pageSize,
//            ],
            'pagination' => false,
            'sort'=>[
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
                'attributes'=>['id']
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
            'spot_id' => $this->spot_id,
            'record_id' => $recordId,
            'creater_id' => $this->creater_id,
            'execute_time' => $this->execute_time,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'executor', $this->executor])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
