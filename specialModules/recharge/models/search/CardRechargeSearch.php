<?php

namespace app\specialModules\recharge\models\search;

use app\modules\user\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\specialModules\recharge\models\CardRecharge;
use yii\db\ActiveQuery;
use app\modules\spot\models\CardRechargeCategory;

/**
 * CardRechargeSearch represents the model behind the search form about `app\specialModules\recharge\models\CardRecharge`.
 */
class CardRechargeSearch extends CardRecharge
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['f_physical_id', 'f_card_fee', 'f_pay_fee', 'f_order_status', 'f_pay_type', 'f_state', 'f_property', 'f_sale_id'], 'integer'],
            [['f_card_id', 'f_user_name', 'f_id_info', 'f_baby_name', 'f_phone', 'f_create_time', 'f_update_time'], 'safe'],
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
    public function search($params,$pageSize = 20,$pagination = true)
    {
        $query = new ActiveQuery(CardRecharge::className());

        $query->from(['a' => CardRecharge::tableName()]);
        $query->select(['a.f_physical_id', 'a.f_user_name', 'a.f_phone', 'a.f_sale_id', 'a.f_baby_name', 'a.f_card_fee', 'a.f_donation_fee','a.f_buy_time', 'a.f_is_logout', 'a.f_create_time','category_name' => 'b.f_category_name']);
        $query->leftJoin(['b' => CardRechargeCategory::tableName()], '{{a}}.f_category_id = {{b}}.f_physical_id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination?[
                'pageSize' => $pageSize,
            ]:false,
            'sort' => [
                'defaultOrder' => [
                    'f_create_time' => SORT_DESC,
                ],
                'attributes' => ['f_user_name','f_phone','f_id_info','f_baby_name','f_buy_time','f_create_time','f_is_logout','category_name', 'f_sale_id']
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'a.f_physical_id' => $this->f_physical_id,
            'a.f_parent_spot_id' => $this->f_parent_spot_id,
            'a.f_card_fee' => $this->f_card_fee,
            'a.f_pay_fee' => $this->f_pay_fee,
            'a.f_order_status' => $this->f_order_status,
            'a.f_pay_type' => $this->f_pay_type,
            'a.f_state' => $this->f_state,
            'a.f_property' => $this->f_property,
            'a.f_create_time' => $this->f_create_time,
            'a.f_update_time' => $this->f_update_time,
            'a.f_sale_id' => $this->f_sale_id,
        ]);

        $query->andFilterWhere(['like', 'a.f_card_id', $this->f_card_id])
            ->andFilterWhere(['like', 'a.f_user_name', trim($this->f_user_name)])
            ->andFilterWhere(['like', 'a.f_id_info', trim($this->f_id_info)])
            ->andFilterWhere(['like', 'a.f_baby_name', trim($this->f_baby_name)])
            ->andFilterWhere(['like', 'a.f_phone', trim($this->f_phone)]);

        $query->orderBy('a.f_is_logout');

        return $dataProvider;

    }
}
