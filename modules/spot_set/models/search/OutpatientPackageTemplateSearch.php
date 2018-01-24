<?php
namespace app\modules\spot_set\models\search;
use app\modules\spot_set\models\OutpatientPackageTemplate;
use app\modules\user\models\User;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
class OutpatientPackageTemplateSearch extends OutpatientPackageTemplate{

    public function rules()
    {
        return [
            [['id', 'spot_id'], 'integer'],

        ];
    }


    public function search($params,$pageSize=20){
        $query = new ActiveQuery(self::className());
        $query->from(['a'=>self::tableName()]);
        $query->select(['a.id','a.spot_id','a.name','a.type','a.price','a.create_time','userName'=>'b.username']);
        $query->leftJoin(['b'=>User::tableName()],'{{a.user_id}}={{b.id}}');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'attributes' => ['id']
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
       $query->andFilterWhere([
            'a.spot_id' => $this->spot_id,

            'a.name' => $this->name,
        ]);
        return $dataProvider;

    }
}