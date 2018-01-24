<?php

namespace app\specialModules\recharge\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\specialModules\recharge\models\UserCard;
use app\specialModules\recharge\models\CardSpotConfig;
use yii\db\ActiveQuery;
use app\specialModules\recharge\models\CardServiceLeft;

/**
 * UserCardSearch represents the model behind the search form about `app\modules\card\models\UserCard`.
 */
class UserCardSearch extends UserCard
{
    
    public $spot_id;
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'card_physical_id', 'parent_spot_id', 'card_type', 'update_time', 'create_time','spot_id'], 'integer'],
            [['card_type_code', 'cardName'], 'string'],
            [['card_id', 'user_name', 'phone', 'card_type_code', 'f_card_desc'], 'safe'],
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
    public function search($params, $pageSize = 20,$pagination = true,$spotId = true) {
//        $query = UserCard::find();
        $query = new ActiveQuery(self::className());
        $query->select(['uc.*', 'f_activate_time' => 'csl.activate_time', 'f_invalid_time' => 'csl.invalid_time','csc.spot_id']);
        $query->from(['uc' => UserCard::tableName()]);
        $query->leftJoin(['csc' => CardSpotConfig::tableName()], '{{csc}}.card_type={{uc}}.card_type_code');
        $query->leftJoin(['csl' => CardServiceLeft::tableName()], '{{csl}}.card_id={{uc}}.card_id');
        if($spotId){
            $query->where([
                'or',
                ['csc.parent_spot_id' => $this->parentSpotId],
                ['csc.spot_id' => $this->spotId]
            ]);
        }
        $query->andWhere(['uc.parent_spot_id' => $this->parentSpotId]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination?[
                'pageSize' => $pageSize,
            ]:false,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => ['id']
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'uc.card_id' => $this->card_id,
            'card_type_code' => $this->card_type_code,
        ]);
        if (!empty($this->cardName)) {
            $query->andWhere(['card_type_code' => $this->cardName]);
        }

        $query->andFilterWhere(['like', 'uc.card_id', trim($this->card_id)])
                ->andFilterWhere(['like', 'user_name', trim($this->user_name)])
                ->andFilterWhere(['like', 'uc.phone', trim($this->phone)]);
        $query->groupBy('card_id');
        return $dataProvider;
    }

}
