<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\Organization;

/**
 * OrganizationSearch represents the model behind the search form about `app\modules\spot\models\Organization`.
 */
class OrganizationSearch extends Organization
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_spot', 'status', 'create_time', 'update_time'], 'integer'],
            [['user_id', 'spot', 'spot_name', 'template', 'contact_iphone', 'contact_name', 'contact_email', 'address', 'fax_number', 'telephone', 'icon_url'], 'safe'],
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
        $query = Organization::find()->select(['id','spot','spot_name','contact_name','contact_iphone','status','create_time'])->where(['parent_spot' => 0]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => ['id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
   
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'parent_spot' => $this->parent_spot,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'spot', trim($this->spot)])
            ->andFilterWhere(['like', 'spot_name', trim($this->spot_name)])
            ->andFilterWhere(['like', 'template', $this->template])
            ->andFilterWhere(['like', 'contact_iphone', $this->contact_iphone])
            ->andFilterWhere(['like', 'contact_name', $this->contact_name])
            ->andFilterWhere(['like', 'contact_email', $this->contact_email])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'fax_number', $this->fax_number])
            ->andFilterWhere(['like', 'telephone', $this->telephone])
            ->andFilterWhere(['like', 'icon_url', $this->icon_url]);

        return $dataProvider;
    }
}
