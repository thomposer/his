<?php

namespace app\modules\rbac\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\rbac\models\Item;
use app\modules\rbac\models\RoleForm;

/**
 * RoleSearch represents the model behind the search form about `app\modules\rbac\models\Item`.
 */
class RoleSearch extends RoleForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'rule_name', 'data'], 'safe'],
            [['type', 'created_at', 'updated_at'], 'integer'],
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
    public function search($params,$pageSize = 20,$parentRolePrefix)
    {
        $query = RoleForm::find()->select(['name','description','data','created_at'])->orderBy(['created_at' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'attributes' => [''],
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'name',$parentRolePrefix.'%',false])
            ->andFilterWhere(['like', 'description', trim($this->description)])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
