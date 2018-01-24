<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\InspectItem;

/**
 * InspectItemSearch represents the model behind the search form about `app\modules\spot\models\InspectItem`.
 */
class InspectItemSearch extends InspectItem
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'spot_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['item_name', 'english_name', 'unit', 'reference'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params, $pageSize = 20) {
        $query = InspectItem::find()->select(['id','item_name','english_name','unit','reference','status']);

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
            'id' => $this->id,
            'spot_id' => $this->spot_id,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'item_name', trim($this->item_name)])
                ->andFilterWhere(['like', 'english_name', $this->english_name])
                ->andFilterWhere(['like', 'unit', $this->unit])
                ->andFilterWhere(['like', 'reference', $this->reference]);

        return $dataProvider;
    }

}
