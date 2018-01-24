<?php

namespace app\modules\spot_set\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\InspectClinic;
use app\modules\spot\models\Inspect;
use yii\db\ActiveQuery;

/**
 * InspectClinicSearch represents the model behind the search form about `app\modules\spot_set\models\InspectClinic`.
 */
class InspectClinicSearch extends InspectClinic
{
    public $inspectName;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['inspectName'], 'string'],
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
        $query = new ActiveQuery(self::className());
        $query->from(['a' => InspectClinic::tableName()]);
        $query->select(['a.id', 'a.cost_price', 'a.status', 'inspectName' => 'b.inspect_name', 'inspectUnit' => 'b.inspect_unit', 'b.phonetic', 'doctorRemark' => 'b.remark', 'a.inspect_price','a.deliver_organization','a.deliver']);
        $query->leftJoin(['b' => Inspect::tableName()], '{{a}}.inspect_id = {{b}}.id');

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
            'a.spot_id' => $this->spot_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'b.inspect_name', trim($this->inspectName)])
                ->andFilterWhere(['like', 'inspect_type', $this->inspect_type])
                ->andFilterWhere(['like', 'remark', $this->remark])
                ->andFilterWhere(['like', 'description', $this->description]);
        return $dataProvider;
    }

}
