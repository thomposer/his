<?php

namespace app\modules\spot_set\models\search;

use app\modules\spot\models\CureList;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\ClinicCure;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * ClinicCureSearch represents the model behind the search form about `app\modules\spot_set\models\ClinicCure`.
 */
class ClinicCureSearch extends ClinicCure
{
    public $name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'cure_id', 'status'], 'integer'],
            [['price', 'default_price'], 'number'],
            [['name'], 'string'],
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
        $query =  new ActiveQuery(ClinicCure::className());
        $query->select([
            "a.id", "a.spot_id", "a.cure_id", "a.price", "a.default_price", "a.status",
            "b.name", "b.unit", "b.meta", "b.remark", "b.tag_id", "b.international_code", "b.type"
        ]);
        $query->from(["a" => self::tableName()]);
        $query->leftJoin(["b" => CureList::tableName()], "{{a}}.cure_id = {{b}}.id");

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
            'id' => $this->id,
            'a.spot_id' => $this->spot_id,
            'cure_id' => $this->cure_id,
            'price' => $this->price,
            'default_price' => $this->default_price,
            'status' => $this->status
        ]);
        $query->andFilterWhere(['like', 'b.name', trim($this->name)]);

        return $dataProvider;
    }
}
