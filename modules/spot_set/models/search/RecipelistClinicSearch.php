<?php

namespace app\modules\spot_set\models\search;

use app\modules\spot\models\RecipeList;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\RecipelistClinic;
use yii\db\ActiveQuery;

/**
 * RecipelistClinicSearch represents the model behind the search form about `app\modules\spot_set\models\RecipelistClinic`.
 */
class RecipelistClinicSearch extends RecipelistClinic
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','product_name'],'string'],
            [['id', 'spot_id', 'recipelist_id', 'status'], 'integer'],
            [['price', 'default_price'], 'number'],
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

        $query = new ActiveQuery(RecipeList::className());
        $query->from(['a' => RecipelistClinic::tableName()]);
        $query->select(['a.id','b.name','b.manufactor','b.specification','b.unit','a.price','b.meta','b.remark','b.status','b.product_name']);
        $query->leftJoin(['b' => RecipeList::tableName()],'{{a}}.recipelist_id = {{b}}.id');

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
            'a.spot_id' => $this->spotId,
        ]);

        $query->andFilterWhere(['like','b.name',trim($this->name)]);
        $query->andFilterWhere(['like','b.product_name',trim($this->product_name)]);
        return $dataProvider;
    }
}
