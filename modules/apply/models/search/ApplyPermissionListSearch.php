<?php

namespace app\modules\apply\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\apply\models\ApplyPermissionList;
use app\common\base\BaseController;
/**
 * ApplyPermissionListSearch represents the model behind the search form about `app\modules\apply\models\ApplyPermissionList`.
 */
class ApplyPermissionListSearch extends ApplyPermissionList
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'create_time', 'update_time'], 'integer'],
            [['user_id','spot', 'item_name','item_name_description','spot_name','reason'], 'safe'],
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
    public function search($params,$pageSize = 10,$where = 1,$field = NULL)
    {
       
       
        $query = ApplyPermissionList::find()->select($field)->where($where)->orderBy(['status' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            
        ]);

        $this->load($params);
      
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'spot', $this->spot])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'reason', $this->reason]);

        return $dataProvider;
    }
}
