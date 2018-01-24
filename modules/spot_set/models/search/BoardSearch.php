<?php

namespace app\modules\spot_set\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\Board;

/**
 * BoardSearch represents the model behind the search form about `app\modules\spot_set\models\Board`.
 */
class BoardSearch extends Board
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'type', 'status', 'create_time', 'update_time'], 'integer'],
            [['name', 'file_name', 'size', 'file_url'], 'safe'],
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
    public function search($params,$pageSize = 20,$entrance)
    {
        if($entrance == 1){
            $query = Board::find()->where(['!=','status',2]);
        }else{
            $query = Board::find();
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],

            'sort'=>[
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
                'attributes'=>['id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => trim($this->id),
            'spot_id' => $this->spot_id,
            'type' => $this->type,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', trim($this->name)])
            ->andFilterWhere(['like', 'file_name', $this->file_name])
            ->andFilterWhere(['like', 'size', $this->size])
            ->andFilterWhere(['like', 'file_url', $this->file_url]);

        return $dataProvider;
    }
}
