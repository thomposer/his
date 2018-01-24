<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\ChildCareTemplate;
use app\modules\user\models\User;
use yii\db\ActiveQuery;

/**
 * CaseTemplateSearch represents the model behind the search form about `app\modules\spot\models\CaseTemplate`.
 */
class ChildCareTemplateSearch extends ChildCareTemplate
{

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'spot_id', 'operating_id', 'type', 'create_time', 'update_time'], 'integer'],
            [['name'], 'safe'],
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
        $query = new ActiveQuery(ChildCareTemplate::className());
        $query->from(['a' => ChildCareTemplate::tableName()]);
        $query->select(['a.id', 'a.name', 'a.type', 'a.create_time', 'b.username as user_name']);
        $query->leftJoin(['b' => User::tableName()], '{{a}}.operating_id = {{b}}.id');
        if (isset($params['source']) && $params['source'] == 2) {//医生门诊 
            $query->andWhere([
                'or',
                [ 'a.type' => 1],
                [ 'a.operating_id' => $this->userInfo->id]
            ]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['create_time' => SORT_DESC],
//                'defaultOrder' => ['create_time' => SORT_DESC],
                'attributes' => ['type', 'create_time']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'a.type' => $this->type,
            'a.spot_id' => $this->spot_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

}
