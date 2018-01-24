<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\Consumables;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;
use yii\db\ActiveQuery;

/**
 * ConsumablesSearch represents the model behind the search form about `app\modules\spot\models\Consumables`.
 */
class ConsumablesSearch extends Consumables
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'type', 'status', 'tag_id', 'create_time', 'update_time','unionSpotId'], 'integer'],
            [['product_number', 'name', 'product_name', 'en_name', 'specification', 'unit', 'meta', 'manufactor', 'remark'], 'safe'],
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
        $query->select(['a.id','a.product_number','a.name','a.type','a.specification','a.unit','a.meta','a.remark','a.status']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'attributes' => ['id', 'product_number','status'],
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        if($this->unionSpotId){
            $query->leftJoin(['b' => ConfigureClinicUnion::tableName()], '{{a}}.id={{b}}.configure_id');
            $query->andWhere(['b.type' => ChargeInfo::$consumablesType, 'b.spot_id' => $this->unionSpotId]);
        }
        $query->andFilterWhere([
            'a.id' => $this->id,
            'a.spot_id' => $this->spot_id,
            'a.type' => $this->type,
            'a.unit' => $this->unit,
            'a.status' => $this->status,
            'a.tag_id' => $this->tag_id,
            'a.create_time' => $this->create_time,
            'a.update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'a.name', trim($this->name)])
            ->andFilterWhere(['like', 'a.product_name', trim($this->product_name)])
            ->andFilterWhere(['like', 'a.en_name', trim($this->en_name)])
            ->andFilterWhere(['like', 'a.specification', trim($this->specification)])
            ->andFilterWhere(['like', 'a.meta', trim($this->meta)])
            ->andFilterWhere(['like', 'a.manufactor', trim($this->manufactor)])
            ->andFilterWhere(['like', 'a.remark', trim($this->remark)]);

        return $dataProvider;
    }
}
