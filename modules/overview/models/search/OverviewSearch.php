<?php

namespace app\modules\overview\models\Search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\overview\models\Overview;
use app\modules\spot\models\Spot;

/**
 * OverviewSearch represents the model behind the search form about `app\modules\overview\models\Overview`.
 */
class OverviewSearch extends Overview
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_name'],'trim'],
            [['id', 'parent_spot', 'status', 'create_time', 'update_time'], 'integer'],
            [[ 'spot_name'], 'safe'],
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
    public function search($params, $pageSize = 20) {
        $query = Spot::find();
        $query->select(['id','spot_name', 'contact_name', 'contact_iphone', 'create_time']);
        $query->where(['<>', 'parent_spot', 0]);
        $query->andWhere(['status' => 1]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);    
	   $query->andFilterWhere(['like', 'spot_name', $this->spot_name]);
        return $dataProvider;
    }
    public function spotSearch($params,$pageSize = 20)
    {
        $query = Overview::find()->select(['id','spot_name','contact_name','contact_iphone','create_time']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'attributes' => ['']
            ]

        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'parent_spot' => 0,
            'status' => 1,

        ]);

        $query->andFilterWhere(['like', 'spot_name', $this->spot_name]);

        return $dataProvider;
    }

}
