<?php

namespace app\modules\spot_set\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\SecondDepartment;
use yii\db\ActiveQuery;
use app\modules\spot_set\models\OnceDepartment;

/**
 * SecondDepartmentSearch represents the model behind the search form about `app\modules\spot_set\models\SecondDepartment`.
 */
class SecondDepartmentSearch extends SecondDepartment
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'status', 'appointment_status', 'room_type','spot_id', 'create_time', 'update_time'], 'integer'],
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
        $query = new ActiveQuery(SecondDepartment::className());
        $query->from(['a' => SecondDepartment::tableName()]);
        $query->select(['a.id','a.name','a.parent_id','a.status','a.appointment_status','a.room_type','b.name as parent_name']);
        $query->leftJoin(['b' => OnceDepartment::tableName()],'{{a}}.parent_id = {{b}}.id');
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

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'a.id' => $this->id,
            'a.parent_id' => $this->parent_id,
            'a.appointment_status' => $this->appointment_status,
            'a.room_type' => $this->room_type,
            'a.spot_id' => $this->spot_id,
            'a.create_time' => $this->create_time,
            'a.update_time' => $this->update_time,
        ]);
        $query->andWhere('a.status != 3');
        $query->andFilterWhere(['like', 'a.name', trim($this->name)]);
    
        return $dataProvider;
    }
}
