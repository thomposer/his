<?php

namespace app\modules\spot_set\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\Material;

/**
 * MaterialSearch represents the model behind the search form about `app\modules\material\models\Material`.
 */
class MaterialSearch extends Material
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'type', 'attribute', 'unit', 'warning_num', 'warning_day', 'status', 'tag_id', 'create_time', 'update_time'], 'integer'],
            [['name', 'product_name', 'en_name', 'specification', 'meta', 'manufactor', 'remark'], 'safe'],
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
        $query = Material::find()->select(['id','product_number','name','type','specification','unit','price','meta','remark','status']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'attributes' => ['id', 'product_number','status','price'],
                'defaultOrder' => ['id' => SORT_DESC]
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
            'type' => $this->type,
            'attribute' => $this->attribute,
            'unit' => $this->unit,
            'price' => $this->price,
            'default_price' => $this->default_price,
            'warning_num' => $this->warning_num,
            'warning_day' => $this->warning_day,
            'status' => $this->status,
            'tag_id' => $this->tag_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', trim($this->name)])
            ->andFilterWhere(['like', 'product_name', trim($this->product_name)])
            ->andFilterWhere(['like', 'en_name', trim($this->en_name)])
            ->andFilterWhere(['like', 'specification', trim($this->specification)])
            ->andFilterWhere(['like', 'meta', trim($this->meta)])
            ->andFilterWhere(['like', 'manufactor', trim($this->manufactor)])
            ->andFilterWhere(['like', 'remark', trim($this->remark)]);

        return $dataProvider;
    }
}
