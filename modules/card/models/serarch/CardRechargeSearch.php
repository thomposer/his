<?php

namespace app\modules\card\models\serarch;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\card\models\TCardRecharge;

/**
 * CardRechargeSearch represents the model behind the search form about `app\modules\card\models\TCardRecharge`.
 */
class CardRechargeSearch extends TCardRecharge
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['f_physical_id', 'f_card_fee', 'f_pay_fee', 'f_order_status', 'f_pay_type', 'f_state', 'f_property'], 'integer'],
            [['f_card_id', 'f_user_name', 'f_phone', 'f_create_time', 'f_update_time'], 'safe'],
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
        $query = TCardRecharge::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'f_physical_id' => $this->f_physical_id,
            'f_card_fee' => $this->f_card_fee,
            'f_pay_fee' => $this->f_pay_fee,
            'f_order_status' => $this->f_order_status,
            'f_pay_type' => $this->f_pay_type,
            'f_state' => $this->f_state,
            'f_property' => $this->f_property,
            'f_create_time' => $this->f_create_time,
            'f_update_time' => $this->f_update_time,
        ]);

        $query->andFilterWhere(['like', 'f_card_id', $this->f_card_id])
            ->andFilterWhere(['like', 'f_user_name', $this->f_user_name])
            ->andFilterWhere(['like', 'f_phone', $this->f_phone]);

        return $dataProvider;
    }
}
