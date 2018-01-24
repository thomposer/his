<?php

namespace app\modules\stock\models\search;
use app\modules\stock\models\ConsumablesStock;
use app\modules\spot\models\Consumables;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\stock\models\ConsumablesStockInfo;
use yii\db\ActiveQuery;

/**
 * ConsumablesStockInfoSearch represents the model behind the search form about `app\modules\stock\models\ConsumablesStockInfo`.
 */
class ConsumablesStockInfoSearch extends ConsumablesStockInfo
{
    public $type;
    public $count = 1;
    public $status;
    public $inboundTime;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'consumables_stock_id', 'consumables_id', 'total_num', 'num', 'expire_time', 'create_time', 'update_time', 'type', 'status', 'inboundTime'], 'integer'],
            [['default_price'], 'number'],
            [['consumablesName'],'string']
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
        $query->from(['a' => ConsumablesStockInfo::tableName()]);
        $query->select(['b.product_number', 'consumablesName' =>  'b.name', 'b.unit','b.specification', 'a.num', 'b.type', 'b.manufactor', 'a.default_price', 'a.create_time', 'a.expire_time', 'inboundTime' => 'c.inbound_time','consumables_id']);
        $query->leftJoin(['c' => ConsumablesStock::tableName()],'{{a}}.consumables_stock_id = {{c}}.id');
        $query->leftJoin(['b' => Consumables::tableName()],'{{a}}.consumables_id = {{b}}.id');
        $query->andWhere("c.status = 1");
        $query->andWhere("a.num > 0");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'product_number' => SORT_ASC
                ],
                'attributes' => ['product_number']
            ]
        ]);

        $this->load($params);

        if ($this->status == 3) {
            //有效预警
            $query->andWhere('a.expire_time <= :time',[':time'=>strtotime(date('Y-m-d'))+ 86400 * 180]);
        } else if ($this->status == 1) {
            //库存预警
            $query->andWhere('a.num <= :num',[':num'=>10]);
        }
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $query->andFilterWhere([
            'a.id' => $this->id,
            'a.spot_id' => $this->spot_id,
            'b.type' => $this->type
        ]);
        $query->andFilterWhere(['like', 'b.name', trim($this->consumablesName)]);
        $query->addOrderBy(['product_number' => SORT_ASC,'inboundTime' => SORT_DESC]);
        return $dataProvider;
    }

    public function attributeLabels() {
        $labels = parent::attributeLabels();
        $labels["product_number"] = "商品编码";
        $labels["unit"] = "规格";
        $labels["type"] = "分类";
        $labels["price"] = "零售价";
        $labels["count"] = "总库存量";
        $labels["consumablesName"] = "名称";
        $labels["inboundTime"] = "入库日期";
        return $labels;
    }
}
