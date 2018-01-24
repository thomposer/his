<?php

namespace app\modules\outpatient\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\CaseTemplate;
use app\modules\user\models\User;
use yii\db\ActiveQuery;


/**
 * CaseTemplateSearch represents the model behind the search form about `app\modules\spot\models\CaseTemplate`.
 */
class OutpatientCaseTemplateSearch extends CaseTemplate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'user_id', 'type', 'create_time', 'update_time'], 'integer'],
            [['name', 'chiefcomplaint', 'historypresent', 'pasthistory', 'personalhistory', 'genetichistory', 'physical_examination', 'cure_idea'], 'safe'],
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
        $query = new ActiveQuery(CaseTemplate::className());
        $query->from(['a' => CaseTemplate::tableName()]);
        $query->select(['a.id','a.name','a.type','a.create_time','b.username as user_name']);
        $query->leftJoin(['b' => User::tableName()],'{{a}}.user_id = {{b}}.id');
        $query->andWhere([
            'or',
            [ 'a.type'=>1],
            [ 'a.user_id'=>$this->userInfo->id]
        ]);
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
            'a.type' => $this->type,
            'a.spot_id' => $this->spot_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'chiefcomplaint', $this->chiefcomplaint])
            ->andFilterWhere(['like', 'historypresent', $this->historypresent])
            ->andFilterWhere(['like', 'pasthistory', $this->pasthistory])
            ->andFilterWhere(['like', 'personalhistory', $this->personalhistory])
            ->andFilterWhere(['like', 'genetichistory', $this->genetichistory])
            ->andFilterWhere(['like', 'physical_examination', $this->physical_examination])
            ->andFilterWhere(['like', 'cure_idea', $this->cure_idea]);

        return $dataProvider;
    }
}
