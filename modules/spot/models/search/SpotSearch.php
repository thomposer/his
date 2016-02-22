<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\Spot;
use yii\db\Query;

/**
 * WxinfoSearch represents the model behind the search form about `app\modules\spot\models\Spot`.
 */
class SpotSearch extends Spot
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'render'], 'integer'],
            [['user_id', 'spot', 'spot_name', 'template'], 'safe'],
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
    public function search($params,$pageSize = 10,$where = null)
    {
        
        $query = Spot::find()->where($where)->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
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
            'render' => $this->render,
        ]);

        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'spot', $this->spot])
            ->andFilterWhere(['like', 'spot_name', $this->spot_name])
            
            ->andFilterWhere(['like', 'template', $this->template]);

        return $dataProvider;
    }
}
