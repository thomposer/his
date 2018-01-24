<?php

namespace app\modules\card\models\serarch;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\card\models\UserCard;
use app\modules\card\models\CardSpotConfig;
use yii\db\ActiveQuery;
use app\modules\card\models\CardServiceLeft;

/**
 * UserCardSearch represents the model behind the search form about `app\modules\card\models\UserCard`.
 */
class UserCardSearch extends UserCard
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'card_physical_id', 'parent_spot_id', 'card_type', 'update_time', 'create_time'], 'integer'],
            [['card_type_code'], 'string'],
            [['card_id', 'user_name', 'phone', 'card_type_code'], 'safe'],
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
//        $query = UserCard::find();
        $query = new ActiveQuery(UserCard::className());
        $query->select(['uc.*', 'f_activate_time' => 'csl.activate_time', 'f_invalid_time' => 'csl.invalid_time']);
        $query->from(['uc' => UserCard::tableName()]);
        $query->leftJoin(['csc' => CardSpotConfig::tableName()], '{{csc}}.card_type={{uc}}.card_type_code');
        $query->leftJoin(['csl' => CardServiceLeft::tableName()], '{{csl}}.card_id={{uc}}.card_id');
        $query->where([
            'or',
            ['csc.parent_spot_id' => $this->parentSpotId],
            ['csc.spot_id' => $this->spotId]
        ]);
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
            'uc.card_id' => $this->card_id,
            'phone' => $this->phone,
            'card_type_code' => $this->card_type_code,
        ]);

        $query->andFilterWhere(['like', 'uc.card_id', $this->card_id])
                ->andFilterWhere(['like', 'user_name', $this->user_name])
                ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }

}
