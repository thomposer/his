<?php

namespace app\modules\outpatient\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\outpatient\models\CureTemplate;
use yii\db\ActiveQuery;
use app\modules\outpatient\models\RecipeTypeTemplate;
use app\modules\user\models\User;

class CureTemplateSearch extends CureTemplate
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'template_type_id', 'type', 'user_id', 'create_time', 'update_time'], 'integer'],
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
        $query = new ActiveQuery(self::className());
        $query->from(['a' => self::tableName()]);
        $query->select(['a.id','a.name','a.type','a.create_time', 'typeTemplateName' => 'b.name','userName'=>'c.username']);
        $query->leftJoin(['b' => RecipeTypeTemplate::tableName()],'{{a}}.template_type_id = {{b}}.id');
        $query->leftJoin(['c' => User::tableName()],'{{a}}.user_id = {{c}}.id');
        
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
                'attributes' => ['id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'a.id' => $this->id,
            'a.spot_id' => $this->spot_id,
            'a.type' => $this->type,
            'a.user_id' => $this->userInfo->id,
            'a.create_time' => $this->create_time,
            'a.update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'a.name', trim($this->name)]);

        return $dataProvider;
    }
}