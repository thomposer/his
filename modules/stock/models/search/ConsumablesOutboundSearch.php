<?php

namespace app\modules\stock\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\stock\models\ConsumablesOutbound;
use yii\db\ActiveQuery;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use app\modules\stock\models\ConsumablesOutboundInfo;
use app\modules\spot\models\Consumables;
use app\modules\stock\models\ConsumablesStockInfo;

/**
 * OutboundSearch represents the model behind the search form about `app\modules\pharmacy\models\Outbound`.
 */
class ConsumablesOutboundSearch extends ConsumablesOutbound
{

    public $name;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'outbound_time', 'outbound_type', 'leading_department_id', 'leading_user_id', 'user_id', 'create_time', 'update_time', 'status'], 'integer'],
            [['id', 'name'], 'string'],
            [['begin_time', 'end_time'], 'date'],
            ['end_time', 'compare', 'operator' => '>=', 'compareAttribute' => 'begin_time'],
            [['id'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params, $pageSize = 20) {
        $query = new ActiveQuery(ConsumablesOutbound::className());
        $query->from(['a' => ConsumablesOutbound::tableName()]);
        $query->select(['a.id', 'a.outbound_time', 'a.outbound_type', 'a.leading_user_id', 'a.status', 'department_name' => 'b.name', 'c.username']);
        $query->leftJoin(['b' => SecondDepartment::tableName()], '{{a}}.leading_department_id = {{b}}.id');
        $query->leftJoin(['c' => User::tableName()], '{{a}}.user_id = {{c}}.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id', 'status', 'outbound_time']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'a.id' => trim($this->id),
            'a.spot_id' => $this->spot_id,
            'a.status' => $this->status,
            'a.outbound_time' => $this->outbound_time,
            'a.outbound_type' => $this->outbound_type,
            'a.leading_department_id' => $this->leading_department_id,
            'a.leading_user_id' => $this->leading_user_id,
        ]);
        if ($this->begin_time) {
            $query->andFilterCompare('a.outbound_time', strtotime($this->begin_time), '>=');
        }
        if ($this->end_time) {
            $query->andFilterCompare('a.outbound_time', strtotime($this->end_time), '<=');
        }

        $query->andFilterWhere(['like', 'a.remark', $this->remark]);

        return $dataProvider;
    }

    /**
     * 
     * @param type $params
     * @param type $pageSize
     * @return ActiveDataProvider
     * @desc 按入库药品
     */
    public function searchDrugs($params, $pageSize = 20) {
        $query = new ActiveQuery(ConsumablesOutboundInfo::className());
        $query->from(['a' => ConsumablesOutboundInfo::tableName()]);
        $query->select(['a.id', 'b.name', 'b.specification', 'b.manufactor', 'a.consumables_outbound_id', 'a.num', 'c.outbound_time', 'userName' => 'e.username', 'leadingUser' => 'g.username', 'c.status', 'department_name' => 'd.name']);
        $query->leftJoin(['f' => ConsumablesStockInfo::tableName()], '{{a}}.consumables_stock_info_id={{f}}.id');
        $query->leftJoin(['b' => Consumables::tableName()], '{{f}}.consumables_id = {{b}}.id');
        $query->leftJoin(['c' => ConsumablesOutbound::tableName()], '{{a}}.consumables_outbound_id= {{c}}.id');
        $query->leftJoin(['d' => SecondDepartment::tableName()], '{{c}}.leading_department_id= {{d}}.id');
        $query->leftJoin(['e' => User::tableName()], '{{c}}.user_id= {{e}}.id');
        $query->leftJoin(['g' => User::tableName()], '{{c}}.leading_user_id= {{g}}.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'consumables_outbound_id' => SORT_DESC,
                    'id' => SORT_ASC,
                ],
                'attributes' => ['id', 'consumables_outbound_id', 'outbound_time']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'a.spot_id' => $this->spot_id,
        ]);
        if ($this->begin_time) {
            $query->andFilterCompare('c.outbound_time', strtotime($this->begin_time), '>=');
        }
        if ($this->end_time) {
            $query->andFilterCompare('c.outbound_time', strtotime($this->end_time), '<=');
        }
        $query->andFilterWhere(['like', 'b.name', $this->name]);

        return $dataProvider;
    }

}
