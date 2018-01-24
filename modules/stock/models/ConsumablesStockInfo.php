<?php

namespace app\modules\stock\models;

use Yii;
use app\modules\spot\models\Consumables;
use yii\db\Query;
use app\modules\spot_set\models\ConsumablesClinic;

/**
 * This is the model class for table "{{%consumables_stock_info}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $consumables_stock_id
 * @property integer $consumables_id
 * @property integer $total_num
 * @property integer $num
 * @property string $invoice_number
 * @property string $default_price
 * @property string $batch_number
 * @property integer $expire_time
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property ConsumablesStock $consumablesStock
 * @property Consumables $consumables
 */
class ConsumablesStockInfo extends \app\common\base\BaseActiveRecord
{

    public $name;
    public $specification;
    public $product_number;
    public $inbound_num;
    public $unit;
    public $manufactor;
    public $price;
    public $consumablesName;
    public $deleted; //删除数组
    public $consumablesStockInfoId; //编辑时，需要操作的id记录组合 public $name;
    public $inbound_time;
    public $userName;
    public $status;
    public $supplier;

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%consumables_stock_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id'], 'required'],
            [['spot_id', 'consumables_stock_id', 'num', 'create_time', 'update_time'], 'integer'],
            [['consumables_stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => ConsumablesStock::className(), 'targetAttribute' => ['consumables_stock_id' => 'id']],
            [['consumablesName', 'deleted', 'consumablesStockInfoId'], 'safe'],
            [['total_num', 'default_price', 'expire_time', 'consumables_id','invoice_number'], 'validateError'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'consumables_stock_id' => '入库单号',
            'consumables_id' => 'gzh_consumables表ID',
            'total_num' => '数量',
            'num' => '单库存量',
            'invoice_number'=>'发票号',
            'default_price' => '成本价',
            'batch_number' => '批号',
            'expire_time' => '有效期',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'name' => '名称',
            'specification' => '规格',
            'unit' => '包装单位',
            'manufactor' => '生产厂商',
            'price' => '零售价',
            'product_number' => '商品编号',
            'inbound_num' => '库存数量',
            'inbound_time' => '入库时间',
            'supplier' => '供应商',
            'userName'=>'制单人',
            'status'=>'状态'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsumablesStock() {
        return $this->hasOne(ConsumablesStock::className(), ['id' => 'consumables_stock_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsumables() {
        return $this->hasOne(Consumables::className(), ['id' => 'consumables_id']);
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['outboundApply'] = ['num'];
        $parent['removeNum'] = ['num'];
        return $parent;
    }

    public function validateError($attribute, $params) {
        if ($this->scenario != 'outboundApply' && $this->scenario != 'removeNum') {
            $num = 0;
            if (count($this->total_num) > 0) {
                if (count($this->deleted)) {
                    foreach ($this->deleted as $key => $v) {
                        if ($v == 0) {
                            $num++;
                            if ($this->total_num[$key] == null) {
                                $this->addError($attribute, '数量不能为空');
                            } else if (!preg_match("/^\s*[+-]?\d+\s*$/", $this->total_num[$key])) {
                                $this->addError($attribute, '数量必须是一个整数');
                            } else if ($this->total_num[$key] < 1 || $this->total_num[$key] > 10000) {
                                $this->addError($attribute, '数量必须在1~10000范围内');
                            }else if($this->invoice_number[$key]&&mb_strlen($this->invoice_number[$key],'UTF-8') > 64){
                                $this->addError($attribute, '发票号长度不能超过64字符.');
                            } else if ($this->default_price[$key] && (!preg_match("/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/", $this->default_price[$key]) || !isset($this->default_price[$key]))) {
                                $this->addError($attribute, '成本价必须是一个数字');
                            } else if ($this->default_price[$key] && ($this->default_price[$key] < 0 || $this->default_price[$key] > 100000)) {
                                $this->addError($attribute, '成本价必须在0~100000范围内');
                            } else if ($this->expire_time[$key] == null) {
                                $this->addError($attribute, '有效期不能为空');
                            } else if ($this->expire_time[$key] < date('Y-m-d')) {
                                $this->addError($attribute, '有效期不能小于当前时间');
                            } else if ($this->consumables_id[$key] == null) {
                                $this->addError($attribute, '参数错误');
                            } else if (!preg_match('/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', $this->default_price[$key])) {
                                $this->addError($attribute, '成本价不能为空且最多保留两位小数.');
                            }
                        }
                    }
                    if ($num == 0) {
                        $this->addError($attribute, '请选择入库医疗耗材');
                    }
                } else {
                    $this->addError($attribute, '请选择入库医疗耗材');
                }
            } else {
                $this->addError($attribute, '数量不能为空');
            }
        }
    }

    /**
     * @return 获取库存里数量不为0,并且已审核的非药品信息
     */
    public static function getList() {
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['a.id', 'consumables_stock_info_id' => 'a.id', 'inbound_num' => 'a.num', 'a.consumables_id', 'a.num', 'a.default_price', 'expire_time' => 'FROM_UNIXTIME(a.expire_time, "%Y-%m-%d")', 'c.name', 'c.product_number', 'c.specification', 'c.unit', 'c.manufactor']);
        $query->leftJoin(['b' => ConsumablesStock::tableName()], '{{a}}.consumables_stock_id = {{b}}.id');
        $query->leftJoin(['c' => Consumables::tableName()], '{{a}}.consumables_id = {{c}}.id');
        $query->leftJoin(['d' => ConsumablesClinic::tableName()], '{{c}}.id = {{d}}.consumables_id');
        $query->where(['a.spot_id' => self::$staticSpotId, 'b.status' => 1])->andWhere('d.id>0');
        $query->indexBy('id');
        $rows = $query->all();
        return $rows;
    }

    /**
     * @param array | integer $consumablesId gzh_consumables表ID	
     * @return 返回对应库存总量
     */
    public static function getTotal($consumablesId = null) {
        $rows = [];
        $query = new Query();
        $query->from(['a' => ConsumablesStock::tableName()]);
        $query->select(['b.consumables_id', 'b.num']);
        $query->leftJoin(['b' => self::tableName()], '{{a}}.id = {{b}}.consumables_stock_id');
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.status' => 1]);
        $query->andWhere('b.expire_time >= :expire_time', [':expire_time' => strtotime(date('Y-m-d'))]);
        $query->andFilterWhere(['b.consumables_id' => $consumablesId]);
        $result = $query->all();
        foreach ($result as $v) {
            if (!isset($rows[$v['consumables_id']])) {
                $rows[$v['consumables_id']] = 0;
            }
            $rows[$v['consumables_id']] += $v['num'];
        }
        return $rows;
    }

    /**
     * @desc 删减库存方法，默认从有效期最短的开始删减
     * @param array $consumables 对应的库存删减的数量,例如 ['22' => 10]
     */
    public static function removeTotal($consumables,$spotId = null) {//回调没有诊所id，需要传入
        if(!$spotId){
            $spotId = self::$staticSpotId;
        }
        $consumablesId = array_keys($consumables);
        $query = new Query();
        $query->from(['a' => ConsumablesStock::tableName()]);
        $query->select(['b.id', 'b.consumables_id', 'b.num']);
        $query->leftJoin(['b' => self::tableName()], '{{a}}.id = {{b}}.consumables_stock_id');
        $query->where(['a.spot_id' => $spotId, 'a.status' => 1]);
        $query->andWhere('b.expire_time >= :expire_time', [':expire_time' => strtotime(date('Y-m-d'))]);
        $query->andFilterWhere(['b.consumables_id' => $consumablesId]);
        $query->orderBy(['b.expire_time' => SORT_ASC]);
        $result = $query->all();
        $rows = [];
        foreach ($result as $key => $v) {
            $removeTotalNum = $consumables[$v['consumables_id']];
            if ($removeTotalNum > $v['num']) {//若移除库存 > 单项库存，则直接单项库存直接扣减为0
                $rows[$v['id']] = $v['num'];
                $consumables[$v['consumables_id']] -= $v['num'];
            } else {
                $rows[$v['id']] = $removeTotalNum;
                $consumables[$v['consumables_id']] = 0;
            }
        }
        if (!empty($rows)) {
            foreach ($rows as $key => $value) {
                $model = ConsumablesStockInfo::findOne(['id' => $key, 'spot_id' => $spotId]);
                $model->scenario = 'removeNum';
                $model->num = $model->num - $value;
                if (!$model->save()) {
                    Yii::info(json_encode($model->errors, true), 'consumables-stock-info-remove');
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 
     * @param type $recipeId
     * @param type $status
     * @return type  根据医疗耗材ID获取相应的库存信息
     */
    public static function getStockByConsumables($consumablesId, $status) {
        $query = new Query();
        $query->from(['a' => ConsumablesStockInfo::tableName()])
                ->select(['total' => 'SUM(a.num)', 'a.consumables_id'])
                ->leftJoin(['b' => ConsumablesStock::tableName()], '{{a}}.consumables_stock_id = {{b}}.id')
                ->where(['a.consumables_id' => $consumablesId, 'a.spot_id' => self::$staticSpotId])
                ->andWhere("a.num > 0");
        $query->andWhere(['b.status' => 1]);
        if ($status) {
            if ($status == 3) {
                $query->andWhere('expire_time <= :time', [':time' => strtotime(date('Y-m-d')) + 86400 * 180]);
            } else if ($status == 1) {
                $query->andWhere('num <= :num', [':num' => 10]);
            }
        }
        $query->groupBy('consumables_id');
        $query->indexBy('consumables_id');
        $data = $query->all();
        return $data;
    }

}
