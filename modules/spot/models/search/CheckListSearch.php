<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\CheckList;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;
use yii\db\ActiveQuery;

/**
 * CheckListSearch represents the model behind the search form about `app\modules\spot\models\CheckList`.
 */
class CheckListSearch extends CheckList
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status','spot_id', 'create_time', 'update_time','unionSpotId'], 'integer'],
            [['name', 'unit', 'default_price', 'price', 'meta', 'remark', 'international_code'], 'safe'],
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
        $query = new ActiveQuery(CheckList::className());
        $query->from(['a' => CheckList::tableName()]);
        $query->select(['a.id','a.name','a.unit','a.price','a.meta','a.remark','a.status']);

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
            'a.id' => $this->id,
            'a.spot_id' => $this->spot_id,
            'a.status' => $this->status,
            'a.create_time' => $this->create_time,
            'a.update_time' => $this->update_time,
        ]);

        if($this->unionSpotId){
            $query->leftJoin(['b' => ConfigureClinicUnion::tableName()], '{{a}}.id={{b}}.configure_id');
            $query->andWhere(['b.type' => ChargeInfo::$checkType, 'b.spot_id' => $this->unionSpotId]);
        }

        $query->andFilterWhere(['like', 'a.name', trim($this->name)])
            ->andFilterWhere(['like', 'a.unit', $this->unit])
            ->andFilterWhere(['like', 'a.default_price', $this->default_price])
            ->andFilterWhere(['like', 'a.price', $this->price])
            ->andFilterWhere(['like', 'a.meta', $this->meta])
            ->andFilterWhere(['like', 'a.remark', $this->remark])
            ->andFilterWhere(['like', 'a.international_code', $this->international_code]);

        return $dataProvider;
    }
}
