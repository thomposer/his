<?php

namespace app\modules\stock\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\stock\models\ConsumablesStock;
use yii\db\ActiveQuery;
use app\modules\spot\models\SupplierConfig;
use app\modules\user\models\User;
use app\modules\stock\models\ConsumablesStockInfo;
use app\modules\spot\models\Consumables;
/**
 * ConsumablesStockSearch represents the model behind the search form about `app\modules\stock\models\ConsumablesStock`.
 */
class ConsumablesStockSearch extends ConsumablesStock
{
    public $name;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'inbound_time', 'inbound_type', 'supplier_id', 'user_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['id','name'], 'string'],
            [['begin_time', 'end_time'], 'date'],
            ['end_time', 'compare', 'operator' => '>=', 'compareAttribute' => 'begin_time']
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
        $query = new ActiveQuery(self::className());
        $query->from(['a' => ConsumablesStock::tableName()]);
        $query->select(['a.id', 'a.inbound_time', 'a.inbound_type', 'a.status', 'supplierName' => 'b.name', 'c.username']);
        $query->leftJoin(['b' => SupplierConfig::tableName()], '{{a}}.supplier_id = {{b}}.id');
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
                'attributes' => ['id', 'status', 'inbound_time']
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
            'a.inbound_time' => $this->inbound_time,
            'a.inbound_type' => $this->inbound_type,
            'a.status' => $this->status
        ]);
        if ($this->begin_time) {
            $query->andFilterCompare('a.inbound_time', strtotime($this->begin_time), '>=');
        }
        if ($this->end_time) {
            $query->andFilterCompare('a.inbound_time', strtotime($this->end_time), '<=');
        }
        $query->andFilterWhere(['like', 'a.remark', $this->remark]);

        return $dataProvider;
    }

    /**
     * 
     * @param type $params
     * @param type $pageSize
     * @return ActiveDataProvider
     * @desc 按药品
     */
    public function searchDrugs($params, $pageSize = 20) {
        $query = new ActiveQuery(ConsumablesStockInfo::className());
        $query->from(['a' => ConsumablesStockInfo::tableName()]);
        $query->select(['a.id','b.name', 'b.specification', 'b.manufactor', 'a.consumables_stock_id', 'a.total_num', 'c.inbound_time', 'a.invoice_number',  'userName'=>'e.username', 'c.status', 'supplier' => 'd.name']);
        $query->leftJoin(['b' => Consumables::tableName()], '{{a}}.consumables_id = {{b}}.id');
        $query->leftJoin(['c' => ConsumablesStock::tableName()], '{{a}}.consumables_stock_id= {{c}}.id');
        $query->leftJoin(['d' => SupplierConfig::tableName()], '{{c}}.supplier_id= {{d}}.id');
        $query->leftJoin(['e' => User::tableName()], '{{c}}.user_id= {{e}}.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'consumables_stock_id'=>SORT_DESC,
                    'id' => SORT_ASC,
                ],
                'attributes' => ['id', 'status', 'inbound_time','consumables_stock_id']
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
            $query->andFilterCompare('c.inbound_time', strtotime($this->begin_time), '>=');
        }
        if ($this->end_time) {
            $query->andFilterCompare('c.inbound_time', strtotime($this->end_time), '<=');
        }
        $query->andFilterWhere(['like', 'b.name', $this->name]);

        return $dataProvider;
    }

}
