<?php

namespace app\modules\rbac\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\rbac\models\PermissionForm;

/**
 * PermissionSearch represents the model behind the search form about `app\modules\rbac\models\PermissionForm`.
 */
class PermissionSearch extends PermissionForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'rule_name', 'data','spotCode','category'], 'safe'],
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
        $query = PermissionForm::find()->select(['name','description','data','created_at'])->orderBy(['created_at' => SORT_DESC]);

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

        $query->andFilterWhere(['like', 'data', trim($this->category)])
            ->andFilterWhere(['like', 'description', trim($this->description)])
            ->andFilterWhere(['like', 'rule_name', trim($this->rule_name)])
            ->andFilterWhere(['like', 'data', trim($this->data)]);
        return $dataProvider;
    }
}
