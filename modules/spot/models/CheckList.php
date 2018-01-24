<?php

namespace app\modules\spot\models;

use app\modules\charge\models\ChargeInfo;
use Yii;
use app\common\base\BaseActiveRecord;
use yii\db\Query;
use app\modules\spot_set\models\CheckListClinic;

/**
 * This is the model class for table "{{%checklist}}".
 * 检查医嘱配置表
 * @property string $id
 * @property integer $spot_id 机构ID
 * @property string $name 医嘱名称
 * @property string $unit 单位
 * @property string $default_price 成本价
 * @property string $price 零售价
 * @property string $meta 拼音码
 * @property string $remark 备注
 * @property string $international_code 国际编码
 * @property integer $tag_id
 * @property integer $status 状态
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class CheckList extends BaseActiveRecord
{

    public $unionSpotId;//适用诊所ID

    public function init() {
        parent::init();
        //机构ID
        $this->spot_id = $this->parentSpotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%checklist}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'spot_id'], 'required'],
            [['remark'], 'string'],
            [['status', 'spot_id', 'create_time', 'update_time','tag_id'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['price', 'default_price'], 'number'],
            [['price', 'default_price'], 'number', 'min' => 0, 'max' => 100000],
            [['price', 'default_price'], 'default', 'value' => '0.00'],
            [['unit', 'meta', 'international_code'], 'string', 'max' => 16],
            [['tag_id'], 'default', 'value' => 0],
            [['name'],'validateName'],
            [['unionSpotId'], 'required','on'=>'unionSpotId'],
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
            'meta' => '拼音码',
            'remark' => '备注',
            'international_code' => '国际编码',
            'tag_id' => '标签',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'unionSpotId' => '适用诊所',
        ];
    }

    public static $getStatus = [
        1 => '正常',
        2 => '停用'
    ];

    /**
     * 
     * @return type 获取影像学检验医嘱List
     */
    public static function getCkeckList() {
        return self::find()->select(['id', 'name', 'unit', 'price'])->where(['spot_id' => self::$staticParentSpotId, 'status' => 1])->indexBy('id')->asArray()->all();
    }

    /**
     * @return array 获取当前机构下的所有检查医嘱
     */
    public static function getParentSpotCheckList($where = null,$status = null,$ifUnion = 0){
        if(!$where){
            $where = '1 != 0';
        }
        $query = new Query();
        $query->from(['a' => CheckList::tableName()]);
        $query->select(['a.id','a.name','unit','meta','remark','international_code','tagName'=>'b.name']);
        $query->leftJoin(['b' => Tag::tableName()], '{{a}}.tag_id = {{b}}.id');
        $query->where([ 'a.spot_id' =>self::$staticParentSpotId]);
            if($ifUnion) {
                $query->leftJoin(['c' => ConfigureClinicUnion::tableName()], '{{a}}.id={{c}}.configure_id');
                $query->andFilterWhere([ 'c.type' => ChargeInfo::$checkType, 'c.spot_id' => self::$staticSpotId]);
            }
        $query->andWhere($where);
        $query->andFilterWhere(['a.status' => $status]);
        $query->orderBy([ 'a.id' => SORT_DESC ]);
        $query->indexBy('id');
        $checkList = $query->all();
        return $checkList;
    }


    /**
     * @param $id 诊所下的医嘱id
     * @return array|bool 获取机构下医嘱的状态
     */
    public static function getCheckStatus($id){
        $query = new Query();
        $query->from(['a' => CheckList::tableName()]);
        $query->select(['a.status']);
        $query->leftJoin(['b' => CheckListClinic::tableName()], '{{a}}.id = {{b}}.check_id');
        $query->where([ 'a.spot_id' => self::$staticParentSpotId,'b.id' => $id]);
        $spotCheck = $query->one();
        return $spotCheck;
    }

    /**
     * @param $attribute
     * @param $params
     * @return bool   返回影像学检查唯一性判断
     */
    public function validateName($attribute,$params){

        if(!$this->hasErrors()){
            $hasRecord = self::find()->where(['name' =>trim($this->name),'spot_id' =>$this->parentSpotId])->count();
            if($this->isNewRecord){
                if($hasRecord){
                    $this->addError('name',   '该影像学检查已存在');
                }
            }else{
                $oldName = $this->getOldAttribute('name');
                if($oldName != trim($this->name) && $hasRecord){
                        $this->addError('name',   '该影像学检查已存在');

                }
            }
        }
    }




}
