<?php

namespace app\modules\outpatient\models;

use Yii;
use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use app\modules\charge\models\ChargeInfo;
use yii\helpers\Json;
use yii\db\Exception;
use yii\db\Query;
use app\modules\stock\models\MaterialStockInfo;
use app\modules\spot_set\models\Material;
use app\modules\patient\models\PatientRecord;
use app\modules\stock\models\MaterialStockDeductionRecord;

/**
 * This is the model class for table "{{%material_record}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $record_id
 * @property integer $material_id
 * @property string $name
 * @property string $product_name
 * @property string $en_name
 * @property string $specification
 * @property integer $type
 * @property integer $attribute
 * @property integer $num
 * @property string $unit
 * @property string $manufactor
 * @property string $meta
 * @property string $price
 * @property string $default_price
 * @property integer $tag_id
 * @property string $remark
 * @property integer $create_time
 * @property integer $update_time
 * @property string $materialName
 */
class MaterialRecord extends \app\common\base\BaseActiveRecord
{

    public $materialName;
    public $chargeStatus;//收费状态
    public $totalNum;//总库存
    public $deleted;//是否是删除,1-删除，
    public function init(){
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%material_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'record_id'], 'required'],
            [['spot_id', 'record_id', 'type', 'attribute', 'tag_id', 'create_time', 'update_time'], 'integer'],
            [['price', 'default_price'], 'number'],
            [['name', 'product_name', 'en_name', 'specification', 'unit', 'manufactor', 'meta'], 'string', 'max' => 64],
            [['num'],'validateNum'],
            [['materialName','chargeStatus','material_id','totalNum','deleted','remark'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'record_id' => '就诊流水id',
            'material_id' => '其他id',
            'name' => '名称',
            'product_name' => '商品名称',
            'en_name' => '英文名称',
            'specification' => '规格',
            'type' => '分类(1-医疗耗材/设备、2-课程、3-书籍、4-玩具、5-其他)',
            'attribute' => '商品属性(1-虚拟物品，2-实物物品)',
            'num' => '数量',
            'unit' => '包装单位',
            'manufactor' => '生产厂家',
            'meta' => '拼音码',
            'price' => '零售价',
            'default_price' => '成本价',
            'tag_id' => '标签id',
            'remark' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'materialName' => '新增其他',
            'charge_status' => '状态',
            'totalNum' => '库存',
        ];
    }

    ///^\s*[+-]?\d+\s*$/
    public function validateNum($attribute, $params) {
        if (count($this->num) > 0) {
            $alreadyChargeId = [];
            $isDeleteId = [];
            $rows = [];
            $oldNum = [];
            foreach ($this->num as $key => $v) {
                try {
                    $list = Json::decode($this->material_id[$key]);
                } catch (Exception $e) {
                    $this->addError('num', '参数错误');
                }
                if (!$this->deleted[$key]) {
                    if (!preg_match("/^\s*[+-]?\d+\s*$/", $v)) {
                        $this->addError('num', '数量必须是一个数字');
                    } else if ($v <= 0 || $v > 100) {
                        $this->addError('num', '数量必须在1~100范围内');
                    }else if(isset($this->remark[$key]) && mb_strlen($this->remark[$key]) > 100){
                        $this->addError('num','备注不能多于100个字符');
                    }else if (!isset($this->material_id[$key])) {
                        $this->addError('num', '参数错误');
                    }
                    if(!isset($list['isNewRecord']) || $list['isNewRecord'] != 0){
                        $materialIdKey = $list['id'];//新增
                        $attributeValue = Material::getOneInfo($materialIdKey,['id','attribute'],['status' => 1])['attribute'];
                        if($attributeValue && $attributeValue == 2){
                            $rows[$materialIdKey][] = $this->num[$key];
                        }else if(!$attributeValue){
                            $this->addError('num','该记录已被禁用/删除');
                        }
                    }else{
                        
                        $record = MaterialRecord::getOneInfo($list['id'],['id','material_id','attribute','num']);
                        if($record['attribute'] == 2){
                            $materialIdKey = $record['material_id'];
                            $rows[$materialIdKey][] = $this->num[$key];
                        }
                        $alreadyChargeId[] = $list['id'];
                    }
                    
                    
                    
                }else{
                    if(isset($list['isNewRecord']) && $list['isNewRecord'] == 0){
                        $record = MaterialRecord::getOneInfo($list['id'],['id','material_id']);
                        $alreadyChargeId[] = $list['id'];
                    }
                }

            }
            if (!empty($rows)) {
                $materialIdList = array_keys($rows);
                $deductNum = MaterialStockDeductionRecord::getDeductTotal($this->record_id, $alreadyChargeId, MaterialStockDeductionRecord::$materialRecord);
                $totalNum = MaterialStockInfo::getTotal($materialIdList);
                foreach ($rows as $key => $v){
                    $tmpNum = $deductNum[$key] + $totalNum[$key];
                    if(array_sum($v) > $tmpNum){
                        $this->addError('num', '数量不能大于库存数量');
                    }
                }
            }

            $query = new Query();
            $query->from(['a' => MaterialRecord::tableName()]);
            $query->select(['b.id','c.charge_type']);
            $query->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.outpatient_id');
            $query->leftJoin(['c' => PatientRecord::tableName()],'{{b}}.record_id = {{c}}.id');
            $query->where(['a.spot_id' => $this->spotId, 'b.origin' => 1, 'b.status' => [1, 2], 'a.id' => $alreadyChargeId, 'b.type' => ChargeInfo::$materialType,'c.charge_type' => 1]);
            $result = $query->all();
            if ($result) {  
                $this->addError('num', '存在已经收费或退费的项目');
            }
        } else {
            $this->addError('num', '数量不能为空');
        }
    }

    /**
     * @desc 返回其他非药品记录dataProvider
     * @param integer $id 就诊流水id
     * @return \yii\data\ActiveDataProvider
     */
    public static function getDataProvider($id, $materialIds = null) {
        $query = new ActiveQuery(self::className());
        $query->from(['a' => self::tableName()]);
        $query->select(['a.id', 'a.name','a.material_id', 'a.specification','a.unit', 'a.price', 'a.remark', 'a.attribute','a.num','a.specification','a.manufactor']);
        $query->where(['a.record_id' => $id, 'a.spot_id' => self::$staticSpotId]);
        $query->andFilterWhere(['a.id' => $materialIds]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
    }
    
    /**
     * 
     * @param string $fields 搜索字段，必须带id
     * @param string | array  $where 查询条件
     * @return \yii\db\ActiveRecord[]
     * @desc 返回其他-非药品记录信息
     */
    public static function getList($fields = '*', $where = []){
        return self::find()->select($fields)->where(['spot_id' => self::$staticSpotId])->andFilterWhere($where)->orderBy(['id' => SORT_DESC])->asArray()->all();
    }
    
    /**
     * @param integer $id 自增记录id 
     * @param string $fields 搜索字段，必须带id
     * @param string | array  $where 查询条件
     * @return \yii\db\ActiveRecord[]
     * @desc 返回其他-非药品对应记录信息
     */
    public static function getOneInfo($id,$fields = '*', $where = []){
        return self::find()->select($fields)->where(['id' => $id,'spot_id' => self::$staticSpotId])->andFilterWhere($where)->asArray()->one();
    }
}
