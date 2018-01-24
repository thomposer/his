<?php

namespace app\modules\spot\models;

use app\specialModules\recharge\models\MembershipPackageCard;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "gzh_package_card".
 *
 * @property string $id
 * @property string $parent_spot_id
 * @property string $name
 * @property string $product_name
 * @property string $meta
 * @property string $validity_period
 * @property string $price
 * @property string $default_price
 * @property string $content
 * @property string $remarks
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 *
 * @property PackageServiceUnion[] $packageServiceUnions
 */
class PackageCard extends \app\common\base\BaseActiveRecord
{
    
    public function init() {
        parent::init();
        $this->spot_id = $this->parentSpotId;
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%package_card}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'name', 'price', 'validity_period', 'status'], 'required'],
            [['spot_id', 'status', 'update_time'], 'integer'],
            [['price', 'default_price'], 'number', 'min' => 0, 'max' => 100000],
            [['price', 'default_price'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
            [['validity_period'], 'integer', 'min' => 1, 'max' => 30],
            [['create_time'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm:ss', 'max' => '2038-01-01 00:00:00', 'min' => '1970-01-01 00:00:00', 'on' => 'create'],
            [['product_name', 'meta'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 20],
            ['name','validateName'],
            [['status'], 'default', 'value' => 0],
            [['content', 'remarks'], 'string', 'max' => 500],
            [['content', 'remarks'], 'default', 'value' => ''],
        ];
    }

    
    public function beforeSave($insert) {
        $this->name = trim($this->name);
        //判断当前表中是否相应更新时间字段
        if ($insert) {
            $this->create_time = time();
        }else{
            $this->create_time = $this->oldAttributes['create_time'];//维持旧数据
        }
        $this->update_time = time();
        return parent::beforeSave($insert);
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'spot_id' => '机构id',
            'name' => '名称',
            'product_name' => '商品名称',
            'meta' => '拼音码',
            'validity_period' => '有效期（年）',
            'price' => '零售价',
            'default_price' => '成本价',
            'content' => '套餐内容',
            'remarks' => '备注内容',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPackageServiceUnions()
    {
        return $this->hasMany(PackageServiceUnion::className(), ['package_card_id' => 'id']);
    }
    /**
     * @desc 返回当前机构的卡中心--套餐卡信息
     * @param string|array $fields 查询字段
     * @param array $where 查询条件
     * @return \yii\db\ActiveRecord[]
     */
    public static function getPackageCardList($fields = '*',$where = []){
        
        return self::find()->select($fields)->where(['spot_id' => self::$staticParentSpotId])->andFilterWhere($where)->indexBy('id')->asArray()->all();
    }
    
    public function validateName($attribute){
        if(MembershipPackageCard::getBuyCount($this->id)){//已有用户购买
            if($this->name != $this->oldAttributes['name'] || $this->validity_period != $this->oldAttributes['validity_period'] || $this->price != $this->oldAttributes['price']){
                $this->addError($attribute, '套餐名称，有效期，零售价不可编辑');
            }
        }
        if($this->isNewRecord){
            $haveRecord = self::find()->select(['id'])->where(['spot_id' => $this->spot_id, $attribute => trim($this->$attribute)])->asArray()->limit(1)->one();
        }else{
            $haveRecord = self::find()->select(['id'])->where(['spot_id' => $this->spot_id, $attribute => trim($this->$attribute)])->andWhere(['<>', 'id',$this->id])->asArray()->limit(1)->one();  
        }
        if($haveRecord){
            $this->addError($attribute, '套餐名称不可重复');
        }
        
    }
    
    /**
     * @desc 返回某条记录的信息
     * @param integer $id 记录id
     * @param string $fields 查询字段
     * @param array $where 查询条件
     * @return \yii\db\ActiveRecord|NULL
     */
    public static function getInfo($id,$fields = '*',$where = []){
    
        return self::find()->select($fields)->where(['id' => $id,'spot_id' => self::$staticParentSpotId])->andFilterWhere($where)->asArray()->one();
    } 
    /**
     * @desc 返回当前机构服务类型状态为正常的套餐卡信息
     */
    public static function getNormalPackageCardList(){
        
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['a.id', 'a.name', 'a.price', 'a.validity_period', 'a.content']);
        $query->leftJoin(['b' => PackageServiceUnion::tableName()],'{{a}}.id = {{b}}.package_card_id');
        $query->leftJoin(['c' => PackageCardService::tableName()],'{{b}}.package_card_service_id = {{c}}.id');
        $query->where(['a.status' => 1,'a.spot_id' => self::$staticParentSpotId,'c.status' => 1]);
        $query->indexBy('id');
        return $query->all();
    }
}
