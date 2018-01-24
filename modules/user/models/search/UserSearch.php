<?php

namespace app\modules\user\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use app\modules\user\models\User;
use app\modules\user\models\UserSpot;
use app\modules\spot\models\Spot;
/**
 * UserSearch represents the model behind the search form about `app\modules\user\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'iphone', 'sex', 'occupation', 'occupation_type', 'position_title', 'expire_time', 'spot_id', 'status', 'create_time', 'update_time'], 'integer'],
            [[ 'username', 'email', 'card', 'head_img', 'birthday', 'introduce', 'auth_key', 'password_hash', 'password_reset_token','clinic_id'], 'safe'],
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
        $query = new ActiveQuery(User::className());
        $query->from(['a' => User::tableName()]);
        
        $query->select(['a.id','a.username','a.email','a.iphone','a.sex','a.occupation','a.occupation_type','a.position_title','a.status','group_concat(distinct c.spot_name) as clinic_name','group_concat(distinct c.id) as clinic_id'])->where(['a.spot_id' => $this->parentSpotId,])->andWhere('a.status != 3');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC
                ],
                'attributes' => ['id']
            ]
        ]);
        $query->leftJoin(['b' => UserSpot::tableName()],'{{a}}.id = {{b}}.user_id');
        $query->leftJoin(['c' => Spot::tableName()],'{{b}}.spot_id = {{c}}.id');
        $query->groupBy('a.id');
        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'id' => $this->id,
            //'iphone' => $this->iphone,
            'sex' => $this->sex,
            'birthday' => $this->birthday,
            'occupation' => $this->occupation,
            'occupation_type' => $this->occupation_type,
            'position_title' => $this->position_title,
            'expire_time' => $this->expire_time,
            'spot_id' => $this->spot_id,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);
        
        $query->andFilterWhere(['like', 'username', trim($this->username)])
            ->andFilterWhere(['like', 'email', trim($this->email)])
            ->andFilterWhere(['like', 'iphone', trim($this->iphone)])
            ->andFilterWhere(['like', 'card', $this->card])
            ->andFilterWhere(['like', 'head_img', $this->head_img])
            ->andFilterWhere(['like', 'introduce', $this->introduce])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'c.id', $this->clinic_id]);

        return $dataProvider;
    }
}
