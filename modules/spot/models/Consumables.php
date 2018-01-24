<?php

namespace app\modules\spot\models;

use Yii;
use app\modules\spot_set\models\ConsumablesClinic;
use yii\db\ActiveQuery;
use yii\db\Query;
use app\modules\charge\models\ChargeInfo;

/**
 * This is the model class for table "{{%consumables}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property string $product_number
 * @property string $name
 * @property string $product_name
 * @property string $en_name
 * @property integer $type
 * @property string $specification
 * @property string $unit
 * @property string $meta
 * @property string $manufactor
 * @property string $remark
 * @property integer $status
 * @property integer $tag_id
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property ConsumablesClinic[] $consumablesClinics
 */
class Consumables extends \app\common\base\BaseActiveRecord
{
    public $unionSpotId;
    public function init() {

        parent::init();
        $this->spot_id = $this->parentSpotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%consumables}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'name', 'type', 'unit', 'status', 'specification','unionSpotId'], 'required'],
            [['spot_id', 'type', 'status', 'tag_id', 'create_time', 'update_time'], 'integer'],
            [['name', 'product_name', 'en_name', 'specification', 'unit', 'meta', 'manufactor'], 'string', 'max' => 64],
            [['product_name', 'en_name', 'meta', 'manufactor', 'unit', 'remark'], 'default', 'value' => ''],
            [['remark'], 'string', 'max' => 255],
            [['product_number'], 'string', 'max' => 16],
            [['name'], 'validateUnique'],
            ['type', 'in', 'range' => [1]],
            ['tag_id', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'product_number' => '商品编号',
            'spot_id' => '机构id',
            'name' => '名称',
            'product_name' => '商品名称',
            'en_name' => '英文名称',
            'type' => '分类',
            'specification' => '规格',
            'unit' => '包装单位',
            'meta' => '拼音码',
            'manufactor' => '生产厂家',
            'remark' => '备注',
            'status' => '状态',
            'tag_id' => '标签',
            'unionSpotId' => '适用诊所',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
    public function scenarios(){
        
        $parents = parent::scenarios();
        $parents['updateStatus'] = ['status'];
        return $parents;
    }
    
    public static $getType = [
        1 => '医疗耗材/设备'
    ];

    public function validateUnique($attribute) {
        $parentSpotId = $this->parentSpotId;
        if ($this->isNewRecord) {
            $hasRecord = self::find()->select(['id'])->where(['spot_id' => $parentSpotId, $attribute => trim($this->$attribute), 'specification' => trim($this->specification)])->asArray()->limit(1)->one();
            if ($hasRecord) {
                $this->addError($attribute, '该名称已存在');
            }
        } else {
            $oldName = $this->getOldAttribute('name');
            $oldSpecification = $this->getOldAttribute('specification');
            if ($oldName != $this->name || $this->specification != $oldSpecification) {
                $hasRecord = self::find()->select(['id'])->where(['spot_id' => $parentSpotId, $attribute => trim($this->$attribute), 'specification' => trim($this->specification)])->asArray()->limit(1)->one();

                if ($hasRecord) {
                    $this->addError($attribute, '该名称已存在');
                }
            }
        }
    }

    /**
     * 
     * @var array 分类
     */
    public static $typeOption = array(
        '1' => '医疗耗材/设备',
    );

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsumablesClinics() {
        return $this->hasMany(ConsumablesClinic::className(), ['consumables_id' => 'id']);
    }

    /**
     * @desc 获取医疗耗材配置列表数据,默认加上了机构id查询条件
     * @param string $fields 字段
     * @param array|string $where 查询条件
     * @return \yii\db\ActiveRecord[]
     */
    public static function getList($fields = '*', $where = [],$clinicUnion = false) {
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select($fields);
        $query->addSelect(['a.id', 'tag_name' => 'b.name']);
        $query->leftJoin(['b' => Tag::tableName()],'{{a}}.tag_id = {{b}}.id');
        if($clinicUnion){
            $query->leftJoin(['c' => ConfigureClinicUnion::tableName()],'{{a}}.id = {{c}}.configure_id');
            $query->andWhere(['c.type' => ChargeInfo::$consumablesType, 'c.spot_id' => self::$staticSpotId]);
        }
        $query->andWhere(['a.spot_id' => self::$staticParentSpotId]);
        $query->andFilterWhere($where);
        $query->indexBy('id');
        $query->orderBy(['a.id' => SORT_DESC]);
        return $query->all();
    }
    
    /**
     * @desc 获取某一记录医疗耗材管理列表数据,默认加上了机构id查询条件
     * @param integer $id 记录id
     * @param string $fields 字段
     * @param array|string $where 查询条件
     * @return \yii\db\ActiveRecord[]
     */
    public static function getOneInfo($id, $fields = '*', $where = []) {
        return self::find()->select($fields)->where(['id' => $id, 'spot_id' => self::$staticParentSpotId])->andFilterWhere($where)->asArray()->one();
    }

    /**
     * 
     * @return type 获取诊所下正常的医疗耗材
     */
    public static function getConsumablesClinic() {
        $query = new \yii\db\Query();
        $data = $query->from(['a' => self::tableName()])
                ->select(['a.id', 'a.name', 'a.product_number', 'a.specification', 'a.type', 'a.unit', 'a.manufactor', 'b.price', 'b.default_price', 'a.remark'])
                ->leftJoin(['b' => ConsumablesClinic::tableName()], '{{a}}.id={{b}}.consumables_id')
                ->where(['a.status' => 1,'b.spot_id'=>self::$staticSpotId])
                ->indexBy('id')->orderBy(['id' => SORT_DESC])
                ->all();
        return $data;
    }

    public function beforeSave($insert) {

        if ($this->isNewRecord) {
            $productName = self::find()->select(['product_number'])->where(['spot_id' => $this->parentSpotId])->orderBy(['id' => SORT_DESC])->asArray()->one();
            if (!$productName) {
                $sn = sprintf('%06d', ($productName['product_number'] + 1));
                $this->product_number = '2' . $sn;
            } else {
                $this->product_number = sprintf('%07d', ($productName['product_number'] + 1));
            }
        }
        return parent::beforeSave($insert);
    }

}
