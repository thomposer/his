<?php

namespace app\specialModules\recharge\models\search;

use app\modules\user\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use app\specialModules\recharge\models\MembershipPackageCard;
use app\specialModules\recharge\models\MembershipPackageCardUnion;
use app\modules\spot\models\PackageCard;
use app\modules\patient\models\Patient;

/**
 * CardRechargeSearch represents the model behind the search form about `app\specialModules\recharge\models\CardRecharge`.
 */
class MembershipPackageCardSearch extends MembershipPackageCard
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iphone','package_card_id','patient_id'], 'integer'],
            [['username'], 'string'],
            [['iphone', 'username', 'package_card_id'], 'safe'],
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
     * @param boolean $pagination 是否分页
     * @param boolean $spotId 查询是否过滤当前诊所
     * @return ActiveDataProvider
     */
    public function search($params,$pageSize = 20,$pagination = true,$spotId = true)
    {
        $query = new ActiveQuery(MembershipPackageCard::className());
        $query->from(['a' => MembershipPackageCard::tableName()]);
        $query->select(['a.id','a.spot_id','a.status','a.create_time','c.username', 'c.sex','c.birthday','c.iphone','d.name','active_time' => 'a.create_time', 'd.validity_period']);
        $query->leftJoin(['b' => MembershipPackageCardUnion::tableName()], '{{a}}.id = {{b}}.membership_package_card_id');
        $query->leftJoin(['c' => Patient::tableName()], '{{b}}.patient_id = {{c}}.id');
        $query->leftJoin(['d' => PackageCard::tableName()], '{{a}}.package_card_id = {{d}}.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination?[
                'pageSize' => $pageSize,
            ]:false,
            'sort' => [
                'defaultOrder' => [
                    'a.status' => SORT_ASC,
                    'a.create_time' => SORT_DESC,
                ],
                'attributes' => ['a.status','a.create_time']
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'a.package_card_id' => $this->package_card_id,
            'c.iphone' => trim($this->iphone),
            'a.spot_id' => $spotId?$this->spot_id:null,
            'b.patient_id' => $this->patient_id
        ]);
        $query->andFilterWhere(['like', 'c.username', trim($this->username)]);
        return $dataProvider;

    }
}
