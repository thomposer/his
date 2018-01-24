<?php

namespace app\modules\spot_set\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\CheckListClinic;
use app\modules\spot\models\CheckList;
use yii\db\ActiveQuery;

/**
 * CheckListClinicSearch represents the model behind the search form about `app\modules\spot_set\models\CheckListClinic`.
 */
class CheckListClinicSearch extends CheckListClinic
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'check_id', 'status'], 'integer'],
            [['price', 'default_price'], 'number'],
            [['name'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $query = new ActiveQuery(CheckListClinic::className());
        $query->from(['a' => CheckListClinic::tableName()]);
        $query->select([
            'a.id', 'a.price','a.default_price','a.status','b.name','b.unit','b.meta','b.remark','b.international_code'
        ]);
        $query->leftJoin(['b' => CheckList::tableName()], '{{a}}.check_id={{b}}.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
            'defaultOrder' => [
                'id' => SORT_DESC
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
            'id' => $this->id,
            'a.spot_id' => $this->spotId,
            'check_id' => $this->check_id,
            'price' => $this->price,
            'default_price' => $this->default_price,
            'status' => $this->status,
        ]);
        $query->andFilterWhere(['like', 'name',trim($this->name)]);

        return $dataProvider;
    }
}
