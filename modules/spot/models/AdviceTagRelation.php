<?php

namespace app\modules\spot\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%advice_tag_relation}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $advice_id
 * @property integer $tag_id
 * @property integer $type
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property Tag $tag
 */
class AdviceTagRelation extends \app\common\base\BaseActiveRecord
{
    public static $inspectType = 1; //实验室检查
    public static $checkType = 2; // 影像学检查
    public static $cureType = 3;  // 治疗
    public static $recipeType = 4; // 处方
    public static $priceType = 5; // 诊疗费
    public static $materialType = 7; //物资管理
    public static $consumablesType = 8; //医疗耗材
    public static $packgeType = 9; //医嘱套餐
    
    public $discountTag;//充值卡折扣标签
    public $commonTag;//通用标签
    
    public function init(){
        
        parent::init();
        $this->spot_id = $this->parentSpotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%advice_tag_relation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'advice_id', 'tag_id'], 'required'],
            [['spot_id', 'advice_id', 'tag_id', 'type', 'create_time', 'update_time'], 'integer'],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::className(), 'targetAttribute' => ['tag_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '机构id',
            'advice_id' => '医嘱ID',
            'tag_id' => '标签ID',
            'type' => '医嘱类型(1-实验室检查，2-影像学检查，3-治疗，4-处方，7-其他，8-医疗耗材，9-医嘱套餐)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'discountTag' => '充值卡折扣标签',
            'commonTag' => '通用标签'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::className(), ['id' => 'tag_id']);
    }
    /**
     * @desc 返回当前机构下医嘱关联标签的信息
     * @param string $fields 查询字段
     * @param array $where 查询条件
     * @return \yii\db\ActiveRecord[]
     */
    public static function getTagList($fields = '*', $where = []){
        return self::find()->select($fields)->where(['spot_id' => self::$staticParentSpotId])->andFilterWhere($where)->asArray()->all();
    }
    
    /**
     * @desc 返回对应关联的通用标签的名称列表，以逗号隔开
     * @param integer $adviceId 医嘱ID
     * @param integer $type 医嘱类型
     */
    public static function getTagInfoView($adviceId,$type){
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['a.tag_id','b.name']);
        $query->leftJoin(['b' => Tag::tableName()],'{{a}}.tag_id = {{b}}.id');
        $query->where(['a.spot_id' => self::$staticParentSpotId,'a.advice_id' => $adviceId,'a.type' => $type,'b.status' => 1]);
        $result = $query->all();
        if(!empty($result)){
            $rows = [];
            foreach ($result as $v){
                $rows[] = $v['name'];
            }
            return implode('，', $rows);
        }
        return '';
    }
}
