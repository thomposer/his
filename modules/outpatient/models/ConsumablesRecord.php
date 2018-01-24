<?php

namespace app\modules\outpatient\models;

use Yii;
use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\Consumables;
use app\modules\spot_set\models\ConsumablesClinic;
use app\modules\stock\models\ConsumablesStockInfo;
use app\modules\charge\models\ChargeInfo;
use app\modules\patient\models\PatientRecord;
use yii\helpers\Json;
use Exception;
use yii\db\Query;
use app\modules\stock\models\ConsumablesStockDeductionRecord;

/**
 * This is the model class for table "{{%consumables_record}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $record_id
 * @property string $consumables_id
 * @property string $product_number
 * @property string $name
 * @property string $product_name
 * @property string $en_name
 * @property integer $type
 * @property string $specification
 * @property string $unit
 * @property string $num
 * @property string $meta
 * @property string $manufactor
 * @property string $remark
 * @property string $tag_id
 * @property string $price
 * @property string $default_price
 * @property integer $create_time
 * @property integer $update_time
 */
class ConsumablesRecord extends \app\common\base\BaseActiveRecord
{
    
    public $consumablesName;
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
    public static function tableName()
    {
        return '{{%consumables_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'record_id'], 'required'],
            [['spot_id', 'record_id', 'type', 'tag_id', 'create_time', 'update_time'], 'integer'],
            [['price', 'default_price'], 'number'],
            [['num'],'validateNum'],
            [['name', 'product_name', 'en_name', 'specification', 'unit', 'meta', 'manufactor'], 'string', 'max' => 64],
            [['consumablesName','chargeStatus','consumables_id','totalNum','deleted', 'remark'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'record_id' => '就诊流水id',
            'consumables_id' => '医疗耗材配置表id',
            'name' => '名称',
            'product_name' => '商品名称',
            'en_name' => '英文名称',
            'type' => '分类(1-医疗耗材/设备)',
            'specification' => '规格',
            'unit' => '包装单位',
            'num' => '数量',
            'meta' => '拼音码',
            'manufactor' => '生产厂家',
            'remark' => '备注',
            'tag_id' => '标签id',
            'price' => '零售价',
            'default_price' => '成本价',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'consumablesName' => '新增医疗耗材',
            'charge_status' => '状态',
            'totalNum' => '库存',
        ];
    }
    
    /**
     * @desc 返回医疗耗材记录dataProvider
     * @param integer $id 就诊流水id
     * @return \yii\data\ActiveDataProvider
     */
    public static function getDataProvider($id, $consumablesIds = null) {
        $query = new ActiveQuery(self::className());
        $query->from(['a' => self::tableName()]);
        $query->select(['a.id', 'a.name','a.consumables_id', 'a.specification','a.unit', 'a.price', 'a.remark', 'a.num','a.specification','a.manufactor']);
        $query->where(['a.record_id' => $id, 'a.spot_id' => self::$staticSpotId]);
        $query->andFilterWhere(['a.id' => $consumablesIds]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
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
                    $list = Json::decode($this->consumables_id[$key]);
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
                    }else if (!isset($this->consumables_id[$key])) {
                        $this->addError('num', '参数错误');
                    }
                    if(!isset($list['isNewRecord']) || $list['isNewRecord'] != 0){
                        $consumables_id = ConsumablesClinic::getOneInfo( $list['id'],['id','consumables_id'],['status' => 1])['consumables_id'];
                        if($consumables_id){
                            $rows[$consumables_id][] = $this->num[$key];
                        }else{
                            $this->addError('num','该记录已被禁用/删除');
                        }
                    }else{
                        $consumables_id = ConsumablesRecord::getOneInfo($list['id'],['id','consumables_id'])['consumables_id'];
                        $rows[$consumables_id][] = $this->num[$key];
                        $alreadyChargeId[] = $list['id'];
                    }
                }else{
                    if(isset($list['isNewRecord']) && $list['isNewRecord'] == 0){
                        $alreadyChargeId[] = $list['id'];
                    }
                }

            }
            
            if (!empty($rows)) {
                $consumablesIdList = array_keys($rows);
                $deductNum = ConsumablesStockDeductionRecord::getDeductTotal($this->record_id, $alreadyChargeId);
                $totalNum = ConsumablesStockInfo::getTotal($consumablesIdList);
                foreach ($rows as $key => $v){
                    $tmpNum = $deductNum[$key] + $totalNum[$key];
                    if(array_sum($v) > $tmpNum){
                        $this->addError('num', '数量不能大于库存数量');
                    }
                }
            }

            $query = new Query();
            $query->from(['a' => self::tableName()]);
            $query->select(['b.id','c.charge_type']);
            $query->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.outpatient_id');
            $query->leftJoin(['c' => PatientRecord::tableName()],'{{b}}.record_id = {{c}}.id');
            $query->where(['a.spot_id' => $this->spotId, 'b.status' => [1, 2], 'a.id' => $alreadyChargeId, 'b.type' => ChargeInfo::$consumablesType,'c.charge_type' => 1]);
            $result = $query->all();
            if ($result) {  
                $this->addError('num', '存在已经收费或退费的项目');
            }
        } else {
            $this->addError('num', '数量不能为空');
        }
    }
    
    /**
     * 
     * @param string $fields 搜索字段，必须带id
     * @param string | array  $where 查询条件
     * @return \yii\db\ActiveRecord[]
     * @desc 返回医疗耗材记录信息
     */
    public static function getList($fields = '*', $where = []){
        return self::find()->select($fields)->where(['spot_id' => self::$staticSpotId])->andFilterWhere($where)->orderBy(['id' => SORT_DESC])->asArray()->all();
    }
    
    /**
     * @param integer $id 自增记录id 
     * @param string $fields 搜索字段，必须带id
     * @param string | array  $where 查询条件
     * @return \yii\db\ActiveRecord[]
     * @desc 返回医疗耗材对应记录信息
     */
    public static function getOneInfo($id,$fields = '*', $where = []){
        return self::find()->select($fields)->where(['id' => $id,'spot_id' => self::$staticSpotId])->andFilterWhere($where)->asArray()->one();
    }
}
