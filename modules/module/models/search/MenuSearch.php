<?php

namespace app\modules\module\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\module\models\Menu;
use app\modules\module\models\Title;
use yii\base\Object;

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
    public function search($params,$pageSize = 10,$where = NULL)
    {
        $query = Menu::find()->select(['gzh_menu.menu_url','gzh_menu.parent_id','gzh_menu.description','gzh_menu.type','gzh_menu.status','gzh_menu.id','gzh_title.module_description'])->leftJoin('gzh_title','gzh_menu.parent_id = gzh_title.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'gzh_menu.id' => $this->id,
            'gzh_menu.type' => $this->type,
            'gzh_menu.parent_id' => $this->parent_id,
            'gzh_menu.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'gzh_menu.menu_url', $this->menu_url])
            ->andFilterWhere(['like', 'gzh_menu.description', $this->description]);

        return $dataProvider;
    }
}
