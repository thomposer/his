<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\CardRechargeCategory;

/**
 * CardRechargeCategorySearch represents the model behind the search form about `app\modules\spot\models\CardRechargeCategory`.
 */
class CardRechargeCategorySearch extends CardRechargeCategory
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['f_physical_id', 'f_state', 'f_parent_id', 'f_create_time', 'f_update_time'], 'integer'],
            [['f_category_name', 'f_category_desc'], 'safe'],
            [['f_medical_fee_discount', 'f_inspect_discount', 'f_check_discount', 'f_cure_discount', 'f_recipe_discount'], 'number'],
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
        $query = CardRechargeCategory::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['f_physical_id' => SORT_DESC],
                'attributes' => ['f_physical_id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'f_physical_id' => $this->f_physical_id,
            'f_spot_id' => $this->f_spot_id,
            'f_state' => $this->f_state,
            'f_parent_id' => 0,
            'f_medical_fee_discount' => $this->f_medical_fee_discount,
            'f_inspect_discount' => $this->f_inspect_discount,
            'f_check_discount' => $this->f_check_discount,
            'f_cure_discount' => $this->f_cure_discount,
            'f_recipe_discount' => $this->f_recipe_discount,
        ]);

        $query->andFilterWhere(['like', 'f_category_name', $this->f_category_name])
                ->andFilterWhere(['like', 'f_category_desc', $this->f_category_desc]);

        return $dataProvider;
    }

    

}
