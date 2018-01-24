<?php

namespace app\modules\outpatient\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\outpatient\models\DentalFirstTemplate;
use yii\db\ActiveQuery;
use app\modules\user\models\User;

/**
 * DentalFirstTemplateSearch represents the model behind the search form about `app\modules\outpatient\models\DentalFirstTemplate`.
 */
class DentalFirstTemplateSearch extends DentalFirstTemplate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'create_time', 'update_time'], 'integer'],
            [['name', 'chiefcomplaint', 'historypresent', 'pasthistory', 'oral_check', 'auxiliary_check', 'diagnosis', 'cure_plan', 'cure', 'advice', 'remark'], 'safe'],
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
        $query->select(['a.id','a.name','a.type','a.create_time','a.user_id','b.username']);
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
                'attributes' => ['id','type','create_time']
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
            'a.user_id' => $this->userInfo->id
        ]);

        return $dataProvider;
    }
    
}
