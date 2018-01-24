<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\CardManage;

/**
 * CardManageSearch represents the model behind the search form about `app\modules\spot\models\CardManage`.
 */
class CardManageSearch extends CardManage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['f_physical_id', 'f_status', 'f_is_issue', 'f_create_time', 'f_effective_time', 'f_activate_time', 'f_invalid_time'], 'integer'],
            [['f_card_id', 'f_identifying_code', 'f_card_desc','cardName','f_card_type_code'], 'safe'],
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
        $query = CardManage::find();

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
            'f_card_type_code' => $this->f_card_type_code,
            'f_status' => $this->f_status,
            'f_is_issue' => $this->f_is_issue,
            'f_create_time' => $this->f_create_time,
            'f_effective_time' => $this->f_effective_time,
            'f_activate_time' => $this->f_activate_time,
            'f_invalid_time' => $this->f_invalid_time,
        ]);
        if(!empty($this->cardName)){
           $query->andWhere(['f_card_type_code'=>$this->cardName]);
        }

        $query->andFilterWhere(['like', 'f_card_id', trim($this->f_card_id)])
            ->andFilterWhere(['like', 'f_identifying_code', $this->f_identifying_code])
            ->andFilterWhere(['like', 'f_card_desc', $this->f_card_desc]);

        return $dataProvider;
    }
}
