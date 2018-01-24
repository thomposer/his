<?php

namespace app\modules\module\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\module\models\Menu;
use app\modules\module\models\Title;
use yii\base\Object;
use app\common\Common;
use yii\db\ActiveQuery;

/**
 * MenuSearch represents the model behind the search form about `app\modules\module\models\Menu`.
 */
class MenuSearch extends Menu
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'parent_id', 'status'], 'integer'],
            [['menu_url', 'description'], 'safe'],
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
    public function search($params,$pageSize = 20,$where = NULL)
    {
        $query = new ActiveQuery(Menu::className());
        $query->from(['m' => Menu::tableName()]);
        $query->select(['m.menu_url','m.parent_id','m.description','m.type','m.status','m.id','t.module_description'])->leftJoin(['t'=>Title::tableName()],'{{m}}.parent_id = {{t}}.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
                'attributes' => ['id']
            ]
        ]);

        

        if ($this->load($params) && !$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'm.id' => $this->id,
            'm.type' => $this->type,
            'm.parent_id' => $this->parent_id,
            'm.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'm.menu_url', trim($this->menu_url)])
            ->andFilterWhere(['like', 'm.description', trim($this->description)]);

        return $dataProvider;
    }
}
