<?php

namespace app\modules\stock\models;

use Yii;
use app\modules\spot\models\RecipeList;
use yii\base\Object;
use yii\db\Query;
use app\modules\stock\models\Stock;
use yii\web\NotFoundHttpException;
use app\modules\spot_set\models\RecipelistClinic;

/**
 * This is the model class for table "{{%stock_info}}".
 *
 * @property integer $id
 * @property integer $spot_id 诊所id
 * @property integer $stock_id 
 * @property integer $recipe_id 处方id
 * @property integer $total_num 总库存量
 * @property integer $num 剩余数量
 * @property string $invoice_number 发票号
 * @property string $default_price 成本价
 * @property string $batch_number 批号
 * @property integer $expire_time 有效期
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property Stock $stock
 * @property Recipelist $recipe
 * @property string $name 名称
 * @property string $specification 规格
 * @property string $unit 单位
 * @property string $manufactor 生产厂商
 * @property decimal $price 零售价
 */
class StockInfo extends \app\common\base\BaseActiveRecord
{

    public $name;
    public $specification;
    public $unit;
    public $manufactor;
    public $price;
    public $recipeName;
    public $deleted; //删除数组
    public $stockInfoId; //id组合
    public $inbound_time; //入库日期
    public $begin_time; //有效期开始时间
    public $end_time; //有效期结束时间
    public $shelves;   //货架号
    public $status;
    public $supplier;
    public $userName;

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%stock_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id'], 'required'],
            [['spot_id', 'stock_id', 'create_time', 'update_time'], 'integer'],
            [['stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stock::className(), 'targetAttribute' => ['stock_id' => 'id']],
            [['recipeName', 'deleted', 'stockInfoId'], 'safe'],
            [['total_num', 'default_price', 'batch_number', 'expire_time', 'recipe_id','invoice_number'], 'validateError'],
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['outboundApply'] = ['num'];
        $parent['batch'] = ['num'];
        return $parent;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'stock_id' => '入库单号',
            'recipe_id' => '处方ID',
            'num' => '数量',
            'invoice_number'=>'发票号',
            'total_num' => '数量',
            'default_price' => '成本价',
            'batch_number' => '批号',
            'expire_time' => '有效期',
            'create_time' => '创建时间',
            'inbound_time' => '入库日期',
            'update_time' => '更新时间',
            'name' => '名称',
            'specification' => '规格',
            'unit' => '单位',
            'manufactor' => '生产厂商',
            'price' => '零售价',
            'begin_time' => '有效期开始时间',
            'end_time' => '有效期结束时间',
            'shelves'=>'货架号',
            'status'=>'状态',
            'suppiler'=>'供应商',
            'userName'=>'制单人',
            'recipeName'=>'名称 ',
            'supplier'=>'供应商'
        ];
    }

    public function validateError($attribute, $params) {
        if ($this->scenario != 'outboundApply') {
            $num = 0;
            if (count($this->total_num) > 0) {
                if (count($this->deleted)) {
                    foreach ($this->deleted as $key => $v) {
                        if ($v == 0) {
                            $num++;
                            if (!preg_match("/^\s*[+-]?\d+\s*$/", $this->total_num[$key])) {
                                $this->addError($attribute, '数量必须是一个整数');
                            } else if ($this->total_num[$key] < 1 || $this->total_num[$key] > 10000) {
                                $this->addError($attribute, '数量必须在1~10000范围内');
                            }else if($this->invoice_number[$key]&&mb_strlen($this->invoice_number[$key],'UTF-8') > 64){
                                $this->addError($attribute, '发票号长度不能超过64字符.');
                            } else if ($this->default_price[$key] && (!preg_match("/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/", $this->default_price[$key]) || !isset($this->default_price[$key]))) {
                                $this->addError($attribute, '成本价必须是一个数字');
                            } else if ($this->default_price[$key] && ($this->default_price[$key] < 0 || $this->default_price[$key] > 100000)) {
                                $this->addError($attribute, '成本价必须在0~100000范围内');
                            } else if ($this->batch_number[$key] == null) {
                                $this->addError($attribute, '批号不能为空');
                            } else if (!strlen($this->batch_number[$key]) >= 32) {
                                $this->addError($attribute, '批号长度不能超过32字符');
                            } else if ($this->expire_time[$key] == null) {
                                $this->addError($attribute, '有效期不能为空');
                            } else if ($this->expire_time[$key] < date('Y-m-d')) {
                                $this->addError($attribute, '有效期不能小于当前时间');
                            } else if ($this->recipe_id[$key] == null) {
                                $this->addError($attribute, '参数错误');
                            } else if (!preg_match('/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', $this->default_price[$key])) {
                                $this->addError($attribute, '成本价最多保留两位小数.');
                            }
                        }
                    }
                    if ($num == 0) {
                        $this->addError($attribute, '请选择入库药品');
                    }
                } else {
                    $this->addError($attribute, '请选择入库药品');
                }
            } else {
                $this->addError($attribute, '数量不能为空');
            }
        }
    }

    /**
     * @return 获取库存里数量不为0,并且已审核的处方信息
     */
    public static function getList() {
        $result = [];
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['a.id', 'a.recipe_id', 'a.num', 'a.default_price', 'a.batch_number', 'a.expire_time', 'c.name', 'c.specification', 'c.unit', 'd.price', 'c.manufactor']);
        $query->leftJoin(['b' => Stock::tableName()], '{{a}}.stock_id = {{b}}.id');
        $query->leftJoin(['c' => RecipeList::tableName()], '{{a}}.recipe_id = {{c}}.id');
        $query->leftJoin(['d' => RecipelistClinic::tableName()],'{{a}}.recipe_id = {{d}}.recipelist_id');
        $query->where(['a.spot_id' => self::$staticSpotId, 'b.status' => 1]);
        $rows = $query->all();
        if ($rows) {
            foreach ($rows as $v) {
                $result[$v['recipe_id']]['recipe_id'] = $v['recipe_id'];
                $result[$v['recipe_id']]['name'] = $v['name'];
                $result[$v['recipe_id']]['specification'] = $v['specification'];
                $result[$v['recipe_id']]['unit'] = $v['unit'];
                $result[$v['recipe_id']]['price'] = $v['price'];
                $result[$v['recipe_id']]['manufactor'] = $v['manufactor'];
                $result[$v['recipe_id']]['batch_number'][$v['id']] = [
                    'id' => $v['id'],
                    'num' => $v['num'],
                    'default_price' => $v['default_price'],
                    'expire_time' => date('Y-m-d', $v['expire_time']),
                    'batch_number' => $v['batch_number']
                ];
            }
        }
        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStock() {
        return $this->hasOne(Stock::className(), ['id' => 'stock_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipe() {
        return $this->hasOne(RecipeList::className(), ['id' => 'recipe_id']);
    }

    public function findStockInfoModel($id) {
        if (($model = self::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * 
     * @param type $recipeId
     * @param type $status
     * @return type  根据药品ID获取相应的库存信息
     */
    public static function getStockByRecipe($recipeId, $status = 0) {
        $query = new Query();
        $query->from(['a' => StockInfo::tableName()])
                ->select(['total' => 'SUM(a.num)', 'a.recipe_id'])
                ->leftJoin(['b' => Stock::tableName()], '{{a}}.stock_id = {{b}}.id')
                ->where(['a.recipe_id' => $recipeId,'a.spot_id'=>self::$staticSpotId,'b.status'=>1])
                ->andWhere("a.num > 0");
        if ($status) {
            if ($status == 3) {
                $query->andWhere('expire_time <= :time', [':time' => strtotime(date('Y-m-d')) + 86400 * 180]);
            } else if ($status == 1) {
                $query->andWhere('num <= :num', [':num' => 10]);
            }
        }
        $query->groupBy('recipe_id');
        $query->indexBy('recipe_id');
        $data = $query->all();
        return $data;
    }

}
