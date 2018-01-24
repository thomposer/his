<?php

namespace app\modules\spot_set\models;

use Yii;
use app\modules\spot\models\Consumables;
use yii\db\Query;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;
/**
 * This is the model class for table "{{%consumables_clinic}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $consumables_id
 * @property string $price
 * @property string $default_price
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property Consumables $consumables
 */
class ConsumablesClinic extends \app\common\base\BaseActiveRecord
{
    public $product_number;
    public $name;
    public $product_name;
    public $en_name;
    public $type;
    public $specification;
    public $unit;
    public $meta;
    public $manufactor;
    public $remark;
    public $tag_name;
    
    
    public function init(){
        
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%consumables_clinic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'consumables_id','price'], 'required'],
            [['spot_id', 'consumables_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['price', 'default_price'],'number','min' => 0,'max' => 100000],
            [['price', 'default_price'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
            [['consumables_id'], 'exist', 'skipOnError' => true, 'targetClass' => Consumables::className(), 'targetAttribute' => ['consumables_id' => 'id']],
            [['consumables_id'], 'validateConsumablesId'],
            [['status'],'default','value' => 1],
            
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
            'consumables_id' => '耗材名称',
            'price' => '零售价',
            'product_number' => '商品编号',
            'default_price' => '成本价',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'name' => '耗材名称',
            'product_name' => '商品名称',
            'en_name' => '英文名称',
            'type' => '分类',
            'specification' => '规格',
            'unit' => '包装单位',
            'meta' => '拼音码',
            'manufactor' => '生产厂家',
            'remark' => '备注',
            'status' => '状态',
            'tag_name' => '标签',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsumables()
    {
        return $this->hasOne(Consumables::className(), ['id' => 'consumables_id']);
    }
    
    /**
     * @desc 返回医疗耗材列表
     * @param $where 条件
     * @return 
     */
    public static function getConsumablesList($where = []) {
        return (new Query())
            ->select([
                "a.id", "a.spot_id", "a.consumables_id", "a.price", "a.default_price", "a.status",
                "b.name", "b.product_name", "b.en_name", "b.type", "b.specification", 
                "b.unit", "b.meta", "b.manufactor", "b.remark", "b.tag_id"
            ])
            ->from(["a" => self::tableName()])
            ->leftJoin(["b" => Consumables::tableName()], "a.consumables_id = b.id")
            ->where(["a.spot_id" => self::$staticSpotId, "a.status" => 1])
            ->andFilterWhere($where)
            ->orderBy(["a.id" => SORT_DESC])
            ->indexBy("id")
            ->all();
    }
    
    /**
     * @desc 获取某一记录医疗耗材管理列表数据,默认加上了诊所id查询条件
     * @param integer $id 记录id
     * @param string $fields 字段
     * @param array|string $where 查询条件
     * @return \yii\db\ActiveRecord[]
     */
    public static function getOneInfo($id,$fields = '*',$where = []){
        return self::find()->select($fields)->where(['id' => $id,'spot_id' => self::$staticSpotId])->andFilterWhere($where)->asArray()->one();
    }
    
    /*
     * 验证consumablesId是否重复
     */
    public function validateConsumablesId($attribute,$params){
        if ($this->isNewRecord) {
            if ($this->checkDuplicate($attribute, $this->$attribute)) {
                $this->addError($attribute, '该耗材已经存在');
            }
            $count = ConfigureClinicUnion::find()->where(['parent_spot_id' => $this->parentSpotId,'configure_id' => $this->$attribute,'type' => ChargeInfo::$consumablesType,'spot_id' => $this->spotId])->count(1);
            if(!$count){
                $this->addError($attribute, '该耗材已取消关联');
            }
        }
    }
    
    protected function checkDuplicate($attribute, $params) {
        $hasRecord = ConsumablesClinic::find()->select(['consumables_id'])->where([$attribute => trim($this->$attribute),'spot_id' => $this->spot_id])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }
    
}
