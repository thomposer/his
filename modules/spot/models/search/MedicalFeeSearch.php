<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\MedicalFee;

/**
 * MedicalFeeSearch represents the model behind the search form about `app\modules\spot\models\MedicalFee`.
 */
class MedicalFeeSearch extends MedicalFee
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['price'], 'number'],
            [['remarks','note'], 'string'],
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
        $query = MedicalFee::find()->select(['id','remarks','price','note','status','create_time']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort'=>[
                'defaultOrder'=>['id'=>SORT_DESC],
                'attributes'=>['id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'spot_id' => $this->spot_id,
            'price' => $this->price,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);
        
        $query->andFilterWhere(['like', 'remarks', trim($this->remarks)]);
        $query->andFilterWhere(['like', 'note', trim($this->note)]);
                
        return $dataProvider;
    }
}
