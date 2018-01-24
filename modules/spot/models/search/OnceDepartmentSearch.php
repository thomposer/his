<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\OnceDepartment;

/**
 * OnceDepartmentSearch represents the model behind the search form about `app\modules\spot\models\OnceDepartment`.
 */
class OnceDepartmentSearch extends OnceDepartment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'create_time', 'update_time'], 'integer'],
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
    public function search($params,$pageSize = 20,$isPage = 1)
    {
        $query = OnceDepartment::find()->select(['id','name']);
        //机构下分页，诊所下不分页
        if($isPage){
            $page = [
                'pageSize' => $pageSize,
            ];
        }else{
            $page = false;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $page,
            'sort' => [
                'attributes' => ['']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'spot_id' => $this->parentSpotId,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
