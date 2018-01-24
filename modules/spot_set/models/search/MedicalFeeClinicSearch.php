<?php

namespace app\modules\spot_set\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\MedicalFeeClinic;
use app\modules\spot\models\MedicalFee;
use yii\db\ActiveQuery;

/**
 * MedicalFeeClinicSearch represents the model behind the search form about `app\modules\spot_set\models\MedicalFeeClinic`.
 */
class MedicalFeeClinicSearch extends MedicalFeeClinic
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           [['id','spot_id', 'fee_id', 'status', 'create_time', 'update_time'], 'integer'],
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
        $query = new ActiveQuery(self::className());
        $query->from(['a' => self::tableName()]);
        $query->leftJoin(['b' => MedicalFee::tableName()],'{{a}}.fee_id = {{b}}.id');
        $query->select(['a.id', 'b.remarks', 'b.price', 'b.note', 'a.create_time', 'a.status']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort'=>[
                'defaultOrder'=>['a.id'=>SORT_DESC],
                'attributes'=>['a.id']
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
            'b.price' => $this->price,
            'a.create_time' => $this->create_time,
            'a.update_time' => $this->update_time,
        ]);
        $query->andFilterWhere(['like', 'remarks', trim($this->remarks)]);
        $query->andFilterWhere(['like', 'note', trim($this->note)]);
                
        
        return $dataProvider;
    }
    
}
