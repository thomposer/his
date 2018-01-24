<?php

namespace app\modules\spot\models;

use Yii;
use yii\db\Query;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;

/**
 * This is the model class for table "{{%curelist}}".
 * 治疗医嘱配置表
 * @property string $id
 * @property integer $spot_id
 * @property string $name
 * @property string $unit
 * @property string $default_price
 * @property string $price
 * @property integer $discount
 * @property string $meta
 * @property string $remark
 * @property string $international_code
 * @property integer $tag_id
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property integer $type 类型(0-普通，1-固定)
 */
class CureList extends \app\common\base\BaseActiveRecord
{

    public $tag_name;
    public $unionSpotId;//适用诊所id
        
    public function init() {

        parent::init();
        $this->spot_id = $this->parentSpotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%curelist}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['discount', 'spot_id', 'status', 'create_time', 'update_time','tag_id','type'], 'integer'],
            [['name', 'spot_id'], 'required'],
            [['price', 'default_price'], 'number', 'min' => 0, 'max' => 100000],
            [['remark'], 'string'],
            [['price', 'default_price'], 'default', 'value' => '0.00'],
            [['name'], 'string', 'max' => 64],
            [['unit', 'meta', 'international_code', 'tag_name'], 'string', 'max' => 16],
            [['tag_id','type','discount'], 'default', 'value' => 0],
            [['name'],'validateName'],
            [['unionSpotId'], 'required', 'on' => 'update'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '机构ID',
            'name' => '医嘱名称',
            'unit' => '单位',
            'default_price' => '成本价',
            'price' => '零售价',
            'discount' => '允许折扣',
            'meta' => '拼音码',
            'remark' => '备注',
            'international_code' => '国际编码',
            'tag_id' => '标签',
            'status' => '状态',
            'type' => '类型',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'tag_name' => '标签名称',
            'unionSpotId' => '适用诊所'
        ];
    }

    public static $getStatus = [
        1 => '正常',
        2 => '停用'
    ];
    public static $getDisCount = [
        2 => '否',
        1 => '是',
    ];

    public function beforeSave($insert) {

        if ($insert) {
            $this->create_time = time();
        }
        if( !$this->isNewRecord && $this->type == 1){//若为固定，不可删除的记录。则姓名和状态不可修改
        
            $this->name = $this->oldAttributes['name'];
            $this->status = $this->oldAttributes['status'];
        }
        $this->update_time = time();
        return parent::beforeSave($insert);
    }

    public static function getCureList($type = null,$where = '1 != 0') {
        return CureList::find()->select(['id', 'name', 'unit', 'price','tag_id'])->where(['spot_id' => self::$staticParentSpotId, 'status' => 1])->andWhere($where)->andFilterWhere(['type' => $type])->indexBy('id')->asArray()->all(); //治疗医嘱列表
    }
    /**
     * @desc 返回对应id的记录信息
     * @param integer $id 记录id
     * @param string $fields 查询字段
     * @param string $where 查询条件
     * @return \yii\db\ActiveRecord|NULL
     */
    public static function getCureOne($id = null,$fields = '*',$where = []){
//         $query = new Query();
//         $query->from(self::tableName());
//         $query->select($fields);
//         $query->where(['spot_id' => self::$staticParentSpotId]);
//         $query->andFilterWhere(['id' => $id]);
//         if(!empty($where))
        return CureList::find()->select($fields)->where(['spot_id' => self::$staticParentSpotId])->andFilterWhere(['id' => $id])->andFilterWhere($where)->asArray()->one();
    }
    public static function getValidCureList($type = null,$where = '1 != 0') {
        return (new Query())
            ->select([
                "a.id", "a.name", "a.unit", "a.meta", "a.remark", "a.tag_id", "a.international_code",
                "tag_name" => "b.name"
            ])
            ->from(["a" => self::tableName()])
            ->leftJoin(["b" => Tag::tableName()], "a.tag_id = b.id")
            ->leftJoin(['c' => ConfigureClinicUnion::tableName()], '{{c}}.configure_id = {{a}}.id')
            ->where(['a.spot_id' => self::$staticParentSpotId, 'a.status' => 1, 'c.spot_id' => self::$staticSpotId, 'c.type' => ChargeInfo::$cureType])
            ->andWhere($where)->andFilterWhere(['a.type' => $type])
            ->orderBy(["a.id" => SORT_DESC])
            ->indexBy('id')
            ->all(); //治疗医嘱列表
    }

    /**
     * 在创建新机构时，会插入一些治疗医嘱
     * @param int $parentSpotId 机构id
     */
    public static function insertCureRecord($parentSpotId)
    {
        $cure = new CureList();
        $cure -> name = "青霉素皮试";
        $cure -> price = "0";
        $cure -> discount = 1;
        $cure -> status = 1;
        $cure -> type = 1;
        $cure -> spot_id = $parentSpotId;
        $cure -> remark = ' ';
        $cure -> save();
    }

    /**
     * @param $attribute
     * @param $params
     * @return bool   返回治疗医嘱唯一性判断
     */
    public function validateName($attribute,$params){

        if(!$this->hasErrors()){
            $hasRecord=self::find()->where(['name'=>trim($this->name),'spot_id'=>$this->parentSpotId])->count();
            if($this->isNewRecord){
                if($hasRecord){
                    $this->addError('name',   '该医嘱已存在');
                }
            }else{
                $oldName=$this->getOldAttribute('name');
                if($oldName != trim($this->name) && $hasRecord){

                    $this->addError('name',   '该医嘱已存在');
                }

            }
        }
    }



}
