<?php

namespace app\modules\stock\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\stock\models\StockInfo;
use yii\db\ActiveQuery;
use app\modules\spot\models\RecipeList;
use app\modules\stock\models\Stock;
use app\modules\spot_set\models\RecipelistClinic;

/**
 * StockInfoSearch represents the model behind the search form about `app\modules\pharmacy\models\StockInfo`.
 */
class StockInfoSearch extends StockInfo
{

    public $count;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'trim'],
            [['id', 'spot_id', 'stock_id', 'recipe_id', 'num', 'expire_time', 'create_time', 'update_time', 'count'], 'integer'],
            [['default_price'], 'number'],
            [['begin_time', 'end_time'], 'date'],
            ['end_time', 'compare', 'operator' => '>=', 'compareAttribute' => 'begin_time'],
            [['batch_number'], 'safe'],
            ['name', 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function attributeLabels() {
        $labels = parent::attributeLabels();
        $labels['count'] = '总库存量';
        $labels['num'] = '单库存量';
        return $labels;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 20) {
        $query = new ActiveQuery(StockInfoSearch::className());
        $query->from(['a' => StockInfoSearch::tableName()]);
        $query->select(['a.id', 'c.inbound_time', 'a.default_price', 'a.batch_number', 'a.expire_time', 'a.num', 'b.name', 'b.specification', 'b.unit', 'b.manufactor', 'a.recipe_id', 'd.shelves', 'd.shelves_sort']);
        $query->leftJoin(['b' => RecipeList::tableName()], '{{a}}.recipe_id = {{b}}.id');
        $query->leftJoin(['d' => RecipelistClinic::tableName()], '{{a}}.recipe_id = {{d}}.recipelist_id');
        $query->leftJoin(['c' => Stock::tableName()], '{{a}}.stock_id = {{c}}.id');
        $query->andWhere("a.num > 0");
        if (isset($params['ValidSearch'])) {
            if ($params['ValidSearch']['status'] == 3) {
                $query->andWhere('expire_time <= :time', [':time' => strtotime(date('Y-m-d')) + 86400 * 180]);
            } else if ($params['ValidSearch']['status'] == 1) {
                $query->andWhere('num <= :num', [':num' => 10]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'recipe_id' => SORT_DESC,
                ],
                'attributes' => ['recipe_id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }


        $query->andFilterWhere([
            'a.id' => $this->id,
            'a.spot_id' => $this->spot_id,
            'c.status' => 1,
            'a.num' => $this->num,
            'a.default_price' => $this->default_price,
            'a.expire_time' => $this->expire_time,
            'd.spot_id' => $this->spot_id
        ]);
        $query->andFilterWhere(['like', 'b.name', trim($this->name)]);
        if ($this->begin_time) {
            $query->andFilterCompare('a.expire_time', strtotime($this->begin_time), '>=');
        }
        if ($this->end_time) {
            $query->andFilterCompare('a.expire_time', strtotime($this->end_time), '<=');
        }
        $query->orderBy(['shelves_sort' => SORT_DESC, 'recipe_id' => SORT_DESC, 'inbound_time' => SORT_DESC]);

        return $dataProvider;
    }

}
