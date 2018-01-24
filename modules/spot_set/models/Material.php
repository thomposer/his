<?php

namespace app\modules\spot_set\models;

/**
 * This is the model class for table "gzh_material".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $name
 * @property string $product_name
 * @property string $en_name
 * @property integer $type
 * @property integer $attribute
 * @property string $specification
 * @property string $unit
 * @property string $price
 * @property string $default_price
 * @property string $meta
 * @property string $manufactor
 * @property integer $warning_num
 * @property integer $warning_day
 * @property string $remark
 * @property integer $status
 * @property string $tag_id
 * @property string $product_number
 * @property string $create_time
 * @property string $update_time
 */
class Material extends \app\common\base\BaseActiveRecord
{
    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%material}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'name', 'type', 'unit', 'price', 'status','specification'], 'required'],
            [['spot_id', 'type', 'attribute', 'status', 'tag_id', 'create_time', 'update_time', 'warning_num', 'warning_day'], 'integer'],
            [['price','default_price'], 'number', 'min' => 0, 'max' => 100000],
            [['name', 'product_name', 'en_name', 'specification','unit', 'meta', 'manufactor'], 'string', 'max' => 64],
            [['product_name', 'en_name', 'meta', 'manufactor','unit','remark'],'default', 'value' => ''],
            [['remark'], 'string', 'max' => 255],
            [['product_number'],'string','max' => 16],
            [['warning_num','warning_day'], 'integer', 'min' => 0],
            [['warning_num'], 'default', 'value' => 10],
            [['warning_day'],'default','value' => 180],
            //['warning_num', 'validateWaringNum','skipOnEmpty'=>false],
            //['warning_day', 'validateWaringDay','skipOnEmpty'=>false],
            [['name'] ,'validateUnique'],
            ['attribute','in','range' => [1, 2]],
            ['tag_id', 'default', 'value' => 0],
            [['price', 'default_price'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_number' => '商品编号',
            'spot_id' => '诊所id',
            'name' => '名称',
            'product_name' => '商品名称',
            'en_name' => '英文名称',
            'type' => '分类',
            'attribute' => '商品属性',
            'specification' => '规格',
            'unit' => '包装单位',
            'price' => '零售价',
            'default_price' => '成本价',
            'meta' => '拼音码',
            'manufactor' => '生产厂家',
            'warning_num' => '物资库存的预警数量',
            'warning_day' => '物资库存的预警天数',
            'remark' => '备注',
            'status' => '状态',
            'tag_id' => '标签',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    public static $attributeOption = array(
        '1' => '虚拟物品（无库存）',
        '2' => '实物物品（有库存）'
    );
    /**
     * 
     * @var array 分类
     */
    public static $typeOption = array(
//        '1' => '医疗耗材/设备',
        '2' => '课程',
        '3' => '书籍',
        '4' => '玩具',
        '5' => '套餐',
        '6' => '其他',
    );
//     public function validateWaringDay() {
//         if ($this->attribute == 2) {
//             if (!isset($this->warning_day) || $this->warning_day === null || $this->warning_day == '') {
//                 $this -> addError('warning_day', '物资库存的预警天数不能为空。');
//             }
//         }
//     }
//     public function validateWaringNum() {
//         if ($this ->attribute == 2) {
//             if (!isset($this -> warning_num) || $this -> warning_num === null || $this->warning_num == '') {
//                 $this -> addError('warning_num', '物资库存的预警数量不能为空。');
//             }
//         }
//     }
    
    public function validateUnique($attribute){
        $parentSpotId = $this->spotId;
        if ($this->isNewRecord) {
            $hasRecord = self::find()->select(['id'])->where(['spot_id' => $parentSpotId, $attribute => $this->$attribute,'specification' => $this->specification])->asArray()->limit(1)->one();
            if ($hasRecord) {
                $this->addError($attribute, '该名称已存在');
            }
        } else {
            $oldName = $this->getOldAttribute('name');
            $oldSpecification = $this->getOldAttribute('specification');
            if ($oldName != $this->name || $this->specification != $oldSpecification) {
                $hasRecord = self::find()->select(['id'])->where(['spot_id' => $parentSpotId, $attribute => $this->$attribute,'specification' => $this->specification])->asArray()->limit(1)->one();
                
                if ($hasRecord) {
                    $this->addError($attribute,'该名称已存在');
                }
            }
        }
    }
    
    public function beforeSave($insert){
        
        if($this->attribute == 1){
            $this->warning_day == '';
            $this->warning_num == '';
        }else{
            $this->warning_day == 180;
            $this->warning_num == 10;
        }
        if($this->isNewRecord){
            $productName = self::find()->select(['product_number'])->where(['spot_id' => $this->spotId])->orderBy(['id' => SORT_DESC])->asArray()->one();
            if(!$productName){
                $sn = sprintf('%06d', ($productName['product_number'] + 1));
                $this->product_number = '1' .$sn;
            }else{
                $this->product_number = sprintf('%07d', ($productName['product_number'] + 1));
            }
            
        }
        return parent::beforeSave($insert);
    }
    
    /**
     * @desc 获取物资管理列表数据,默认加上了诊所id查询条件
     * @param string $fields 字段
     * @param array|string $where 查询条件
     * @return \yii\db\ActiveRecord[]
     */
    public static function getList($fields = '*',$where = []){
        return self::find()->select($fields)->where(['spot_id' => self::$staticSpotId])->andFilterWhere($where)->indexBy('id')->orderBy(['id' => SORT_DESC])->asArray()->all();
    }
    
    /**
     * @desc 获取某一记录物资管理列表数据,默认加上了诊所id查询条件
     * @param integer $id 记录id
     * @param string $fields 字段
     * @param array|string $where 查询条件
     * @return \yii\db\ActiveRecord[]
     */
    public static function getOneInfo($id,$fields = '*',$where = []){
        return self::find()->select($fields)->where(['id' => $id,'spot_id' => self::$staticSpotId])->andFilterWhere($where)->asArray()->one();
    }
}
