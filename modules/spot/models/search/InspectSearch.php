<?php

namespace app\modules\spot\models\search;

use app\modules\charge\models\ChargeInfo;
use app\modules\spot\models\ConfigureClinicUnion;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\Inspect;
use yii\db\ActiveQuery;

/**
 * InspectSearch represents the model behind the search form about `app\modules\spot\models\Inspect`.
 */
class InspectSearch extends Inspect
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id','spot_id', 'inspect_price', 'status', 'create_time', 'update_time','unionSpotId'], 'integer'],
//            [['inspect_name'], 'string'],
            [['inspect_name','spot_id', 'inspect_unit', 'phonetic', 'international_code', 'remark'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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

        $query = new ActiveQuery(Inspect::className());
        $query->from(['a' => Inspect::tableName()]);
        $query->select(['a.id','a.inspect_name','a.inspect_unit','a.inspect_price','a.phonetic','a.remark','a.status']);
        $query->where(['a.spot_id' => $this->spot_id]);

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
            return $dataProvider;
        }

        $query->andFilterWhere([
            'a.id' => $this->id,
            'a.inspect_price' => $this->inspect_price,
            'a.status' => $this->status,
        ]);

        if($this->unionSpotId){
            $query->leftJoin(['b' => ConfigureClinicUnion::tableName()], '{{a}}.id={{b}}.configure_id');
            $query->andWhere(['b.type' => ChargeInfo::$inspectType, 'b.spot_id' => $this->unionSpotId]);
        }
        $query->andFilterWhere(['like', 'a.inspect_name', trim($this->inspect_name)]);
        return $dataProvider;
    }

}
