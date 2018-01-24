<?php

namespace app\modules\spot\models;

use Yii;
use app\modules\spot\models\CureList;
use app\modules\spot\models\Inspect;
use app\modules\spot\models\CheckList;
use app\modules\spot\models\RecipeList;
use app\modules\spot_set\models\Material;
use app\modules\spot\models\Consumables;
use app\modules\spot\models\AdviceTagRelation;

/**
 * This is the model class for table "{{%tag}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $name
 * @property string $type 
 * @property string $description
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class Tag extends \app\common\base\BaseActiveRecord
{
    
    public static $inspectType = 1; //实验室检查
    public static $checkType = 2; // 影像学检查
    public static $cureType = 3;  // 治疗
    public static $recipeType = 4; // 处方
    public static $materialType = 7; //其他
    public static $consumablesType = 8; //医疗耗材

    public function init() {
        parent::init();
        $this->spot_id = $this->parentSpotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'type', 'create_time', 'update_time'], 'integer'],
            [['name','type'], 'required'],
            [['name'], 'validateName'],
            [['name'], 'trim'],
            [['name'], 'string', 'max' => 10],
            [['description'], 'string', 'max' => 100],
            [['type'], 'validateType', 'on' => 'update'],
            [['status'], 'default', 'value' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '标签ID',
            'spot_id' => '机构ID',
            'name' => '标签名称',
            'type' => '分类',
            'description' => '描述',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    public function validateName($attribute) {
        $newAttribute = trim($this->$attribute);
        if($newAttribute != $this->getOldAttributes()['name']){
            $count = self::find()->select(['count' => 'COUNT(1)'])->where(['name' => $newAttribute,'spot_id' => $this->parentSpotId])->asArray()->one()['count'];
            if(0 != $count){
                $this->addError($attribute, '标签名称不能重复');
            }
        }
    }

    public static $getStatus = [
        '1' => '正常',
        '2' => '停用'
    ];
    
    /*
     * 标签分类
     */
    public static $getType = [
        '1' => '充值卡折扣标签',
        '2' => '通用标签',
    ];
    
    /**
     * @param array|string $where 查询条件 默认为全部
     * @return type 获取标签List
     */
    public static function getTagList($fields = ['id', 'name'],$where = []) {
        
        return self::find()->select($fields)->where(['spot_id' => self::$staticParentSpotId,'status'=>1])->andFilterWhere($where)->asArray()->all();
    }
    
    /**
     * 
     * @param type $id 主键ID
     * @return 根据id获取标签信息
     */
    public static function getTag($id){
        return self::find()->select(['id','name','description'])->where(['id'=>$id])->asArray()->one();
    }
    
    /*
     * 验证分类是否能修改
     */
    public function validateType($attribute) {
        if($this->attributes['type'] != $this->oldAttributes['type'] && self::haveUnion($this->id)){
            $this->addError($attribute, '标签关联项目后，分类不可再修改');
        }
    }
    
    static public function haveUnion($tagId) {
        $model = self::findOne(['id' => $tagId, 'spot_id' => self::$staticParentSpotId]);
        if(1 == $model->type){//充值卡打折标签
            $count = Inspect::find()->where(['spot_id' => self::$staticParentSpotId, 'tag_id' => $tagId])->count();
            $count == 0 && $count = CheckList::find()->where(['spot_id' => self::$staticParentSpotId, 'tag_id' => $tagId])->count();
            $count == 0 && $count = CureList::find()->where(['spot_id' => self::$staticParentSpotId, 'tag_id' => $tagId])->count();
            $count == 0 && $count = RecipeList::find()->where(['spot_id' => self::$staticParentSpotId, 'tag_id' => $tagId])->count();
            $count == 0 && $count = Material::find()->where(['tag_id' => $tagId])->count();//其他类跟诊所关联 直接使用tag_id获取数据  tag_id唯一
            $count == 0 && $count = Consumables::find()->where(['spot_id' => self::$staticParentSpotId, 'tag_id' => $tagId])->count();
        }else{
            $count = AdviceTagRelation::find()->where(['spot_id' => self::$staticParentSpotId, 'tag_id' => $tagId])->count();
        }
        return (boolean) $count;
    }
    
}
