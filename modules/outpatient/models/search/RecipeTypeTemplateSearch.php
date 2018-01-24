<?php

namespace app\modules\outpatient\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\outpatient\models\RecipeTypeTemplate;
use yii\db\ActiveQuery;
use app\modules\user\models\User;

/**
 * RecipeTypeTemplateSearch represents the model behind the search form about `app\modules\outpatient\models\RecipeTypeTemplate`.
 */
class RecipeTypeTemplateSearch extends RecipeTypeTemplate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'user_id', 'type', 'create_time', 'update_time'], 'integer'],
            [['name'], 'safe'],
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
        $query = new ActiveQuery(RecipeTypeTemplate::className());
        $query->from(['a' => RecipeTypeTemplate::tableName()]);
        $query->select(['a.id','a.name','a.type','a.create_time','b.username as user_name']);
        $query->leftJoin(['b' => User::tableName()],'{{a}}.user_id = {{b}}.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
                'attributes' => ['type','create_time','id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'a.user_id'=>$this->userInfo->id,
            'a.spot_id' => $this->spot_id,
        ]);

        return $dataProvider;
    }
}
