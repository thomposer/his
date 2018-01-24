<?php

namespace app\specialModules\recharge\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\specialModules\recharge\models\CardFlow;

/**
 * CardRechargeSearch represents the model behind the search form about `app\specialModules\recharge\models\CardRecharge`.
 */
class CardFlowSearch extends CardFlow
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['f_physical_id', 'f_record_id'], 'integer'],
            [['f_user_name'], 'string'],
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
        $query = CardFlow::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'f_physical_id' => SORT_DESC
                ],
//                'attributes' => ['id', 'arrival_time']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $query->where(['f_record_id' => $params['f_physical_id']]);

        $query->andFilterWhere([
            'f_physical_id' => $this->f_physical_id,
            'f_create_time' => $this->f_create_time,
            'f_update_time' => $this->f_update_time,
        ]);

        $query->andFilterWhere(['like', 'f_user_name', trim($this->f_user_name)]);

        return $dataProvider;
    }

}
