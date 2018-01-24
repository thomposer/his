<?php

namespace app\specialModules\recharge\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\specialModules\recharge\models\CategoryHistory;
use app\modules\spot\models\CardRechargeCategory;
use yii\db\ActiveQuery;

/**
 * CategoryHistorySearch represents the model behind the search form about `app\specialModules\recharge\models\CategoryHistory`.
 */
class CategoryHistorySearch extends CategoryHistory
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['f_physical_id', 'f_record_id', 'f_beg_category', 'f_end_category', 'f_user_id', 'f_state', 'f_create_time', 'f_update_time'], 'integer'],
            [['f_user_name'], 'safe'],
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
        $query = new ActiveQuery(CategoryHistory::className());
        $query->from(['t1' => self::tableName()]);
        $query->select(['t1.f_create_time', 't1.f_record_id', 't1.f_user_name', 't1.f_change_reason', 'beg_category_name' => 't2.f_category_name', 'end_category_name' => 't3.f_category_name','t1.f_spot_id']);
        $query->leftJoin(['t2' => CardRechargeCategory::tableName()], '{{t1}}.f_beg_category={{t2}}.f_physical_id');
        $query->leftJoin(['t3' => CardRechargeCategory::tableName()], '{{t1}}.f_end_category={{t3}}.f_physical_id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    't1.f_physical_id' => SORT_DESC,
                ],
                'attributes' => ['t1.f_physical_id']
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $query->where(['f_record_id' => $params['f_physical_id']]);
        $query->andFilterWhere([
            'f_physical_id' => $this->f_physical_id,
            'f_record_id' => $this->f_record_id,
            'f_beg_category' => $this->f_beg_category,
            'f_end_category' => $this->f_end_category,
            'f_user_id' => $this->f_user_id,
            'f_state' => $this->f_state,
            'f_create_time' => $this->f_create_time,
            'f_update_time' => $this->f_update_time,
        ]);

        $query->andFilterWhere(['like', 'f_user_name', $this->f_user_name]);

        return $dataProvider;
    }

}
