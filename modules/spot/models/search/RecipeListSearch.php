<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\RecipeList;
use app\modules\spot\models\AdviceTagRelation;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;
use yii\db\ActiveQuery;
/**
 * RecipeListSearch represents the model behind the search form about `app\modules\spot\models\RecipeList`.
 */
class RecipeListSearch extends RecipeList
{
    public $discountTagChecked;
    public $commonTagChecked;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','spot_id', 'type', 'drug_type', 'default_used', 'default_frequency', 'default_consumption', 'status', 'create_time', 'update_time','unionSpotId'], 'integer'],
            [['adviceTagId'],'string'],
            [['name', 'product_name', 'en_name', 'specification', 'unit', 'default_price', 'manufactor', 'app_number', 'medicine_code', 'meta', 'product_batch', 'address', 'bar_code', 'international_code', 'remark','adviceTagId','discountTagChecked','commonTagChecked'], 'safe'],
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
        $query = new ActiveQuery(RecipeList::className());
        $query->from(['a' => RecipeList::tableName()]);
        $query->select(['a.id','a.name','a.specification','a.unit','a.price','a.meta','a.remark','a.status','a.manufactor','a.product_name']);
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
            $query->where('1=0');
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'a.id' => $this->id,
            'a.type' => $this->type,
            'a.drug_type' => $this->drug_type,
            'a.default_used' => $this->default_used,
            'a.default_frequency' => $this->default_frequency,
            'a.default_consumption' => $this->default_consumption,
            'a.status' => $this->status,
        ]);
        
        if($this->discountTagChecked && !$this->commonTagChecked){
            $query->andFilterWhere(['a.spot_id' => $this->spot_id,'a.tag_id' => $this->discountTagChecked]);
        }else if($this->commonTagChecked){
            $recipeIdList = AdviceTagRelation::getTagList(['advice_id'],['tag_id' => $this->commonTagChecked,'type' => AdviceTagRelation::$recipeType]);
            if(!$this->discountTagChecked){
                if(!empty($recipeIdList)){
                    $query->andWhere(['a.id' => array_column($recipeIdList, 'advice_id'),'a.spot_id' => $this->spot_id]);
                }else{
                    $query->andWhere(['a.id' => 0]);
                }
            }else{
                $query->orFilterWhere(['a.spot_id' => $this->spot_id,'a.tag_id' => $this->discountTagChecked]);
                if(!empty($recipeIdList)){
                    $query->orFilterWhere(['a.spot_id'=>$this->spot_id,'a.id' => array_column($recipeIdList, 'advice_id')]);
                }else{
                    $query->orFilterWhere(['a.id' => 0]);
                }
            }
        }else{
            $query->andFilterWhere(['a.spot_id' => $this->spot_id]);
        }
        if($this->unionSpotId){
            $query->leftJoin(['b' => ConfigureClinicUnion::tableName()], '{{a}}.id={{b}}.configure_id');
            $query->andWhere(['b.type' => ChargeInfo::$recipeType, 'b.spot_id' => $this->unionSpotId]);
        }
        $query->andFilterWhere(['like', 'a.name', trim($this->name)])
            ->andFilterWhere(['like', 'a.product_name', trim($this->product_name)])
            ->andFilterWhere(['like', 'a.en_name', $this->en_name])
            ->andFilterWhere(['like', 'a.specification', $this->specification])
            ->andFilterWhere(['like', 'a.unit', $this->unit])
            ->andFilterWhere(['like', 'a.default_price', $this->default_price])
            ->andFilterWhere(['like', 'a.manufactor', $this->manufactor])
            ->andFilterWhere(['like', 'a.app_number', $this->app_number])
            ->andFilterWhere(['like', 'a.medicine_code', $this->medicine_code])
            ->andFilterWhere(['like', 'a.meta', $this->meta])
            ->andFilterWhere(['like', 'a.product_batch', $this->product_batch])
            ->andFilterWhere(['like', 'a.address', $this->address])
            ->andFilterWhere(['like', 'a.bar_code', $this->bar_code])
            ->andFilterWhere(['like', 'a.international_code', $this->international_code])
            ->andFilterWhere(['like', 'a.remark', $this->remark]);
        
        return $dataProvider;
    }
}
