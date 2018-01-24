<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\Spot;

/**
 * SpotSearch represents the model behind the search form about `app\modules\spot\models\Spot`.
 */
class SpotSearch extends Spot
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_spot', 'status', 'create_time', 'update_time'], 'integer'],
            [['spot_name'],'trim'],
            [['user_id', 'spot', 'spot_name', 'template', 'contact_iphone', 'contact_name', 'contact_email', 'province', 'city', 'area', 'detail_address', 'fax_number', 'telephone', 'icon_url'], 'safe'],
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
        $query = Spot::find()->select(['id','spot_name','telephone','fax_number','contact_iphone','contact_name','status']);
            
    
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['status' => SORT_ASC],
                'attributes' => ['id','status']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'parent_spot' => Yii::$app->request->get('parent_spot',$this->parentSpotId),
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'spot', $this->spot])
            ->andFilterWhere(['like', 'spot_name', $this->spot_name])
            ->andFilterWhere(['like', 'template', $this->template])
            ->andFilterWhere(['like', 'contact_iphone', $this->contact_iphone])
            ->andFilterWhere(['like', 'contact_name', $this->contact_name])
            ->andFilterWhere(['like', 'contact_email', $this->contact_email])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'detail_address', $this->detail_address])
            ->andFilterWhere(['like', 'fax_number', $this->fax_number])
            ->andFilterWhere(['like', 'telephone', $this->telephone])
            ->andFilterWhere(['like', 'icon_url', $this->icon_url]);

        return $dataProvider;
    }
}
