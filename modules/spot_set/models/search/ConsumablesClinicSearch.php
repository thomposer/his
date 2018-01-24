<?php

namespace app\modules\spot_set\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\ConsumablesClinic;
use yii\db\ActiveQuery;
use app\modules\spot\models\Consumables;

/**
 * ConsumablesClinicSearch represents the model behind the search form about `app\modules\spot_set\models\ConsumablesClinic`.
 */
class ConsumablesClinicSearch extends ConsumablesClinic
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'],'string'],
            [['id', 'spot_id', 'consumables_id', 'status', 'create_time', 'update_time'], 'integer'],
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
        $query = new ActiveQuery(self::className());
        $query->from(['a' => self::tableName()]);
        $query->select(['a.id','a.price','a.status','b.product_number','b.name','b.type','b.specification','b.unit','b.remark']);
        $query->leftJoin(['b' => Consumables::tableName()],'{{a}}.consumables_id = {{b}}.id');
        
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
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'a.id' => $this->id,
            'a.spot_id' => $this->spot_id,
            'a.consumables_id' => $this->consumables_id,
            'a.price' => $this->price,
            'a.default_price' => $this->default_price,
            'a.status' => $this->status,
        ]);
        $query->andFilterWhere(['like','b.name',trim($this->name)]);
        return $dataProvider;
    }
}
