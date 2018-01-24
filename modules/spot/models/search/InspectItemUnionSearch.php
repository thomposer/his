<?php

namespace app\modules\spot\models\search; 

use Yii; 
use yii\base\Model; 
use yii\data\ActiveDataProvider; 
use app\modules\spot\models\InspectItemUnion; 
use app\modules\spot\models\Inspect;
use app\modules\spot\models\InspectItem;
use yii\db\ActiveQuery;
/** 
 * InspectItemUnionSearch represents the model behind the search form about `app\modules\spot\models\InspectItemUnion`.
 */ 
class InspectItemUnionSearch extends InspectItemUnion 
{ 
    /** 
     * @inheritdoc 
     */ 
    public function rules() 
    { 
        return [ 
            [['id', 'inspect_id', 'item_id', 'sort', 'create_time', 'update_time'], 'integer'], 
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
//        $query = InspectItemUnion::find(); 
        $query = new \yii\db\Query;
        $query->from(['i' => Inspect::tableName()]);
        $query->select([
            'it.id','it.item_name','it.english_name','it.unit','it.reference'
        ]);
        $query->leftJoin(['itu' => InspectItemUnion::tableName()], '{{i}}.id={{itu}}.inspect_id');
        $query->leftJoin(['it' => InspectItem::tableName()], '{{it}}.id={{itu}}.item_id');
        $dataProvider = new ActiveDataProvider([ 
            'query' => $query, 
            'pagination' => [ 
                'pageSize' => $pageSize, 
            ] 
        ]); 

        $this->load($params); 
        if (!$this->validate()) { 
            $query->where('1=0'); 
            return $dataProvider; 
        } 

        $query->andFilterWhere([
            'id' => $this->id,
            'inspect_id' => $this->inspect_id,
            'item_id' => $this->item_id,
            'sort' => $this->sort,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        return $dataProvider; 
    } 
} 