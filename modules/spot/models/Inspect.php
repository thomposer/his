<?php

namespace app\modules\spot\models;

use app\modules\charge\models\ChargeInfo;
use Yii;
use app\common\base\BaseActiveRecord;
use app\modules\spot\models\Tag;

/**
 * This is the model class for table "{{%inspect}}".
 *
 * @property string $id
 * @property string $inspect_name
 * @property string $inspect_unit
 * @property string $inspect_price
 * @property string $cost_price
 * @property string $phonetic
 * @property string $international_code
 * @property string $remark
 * @property integer $tag_id
 * @property integer $status
 * @property integer $spot_id
 * @property integer $create_time
 * @property integer $update_time
 */
class Inspect extends BaseActiveRecord
{

    public $item_name;
    public $english_name;
    public $unit;
    public $reference;
    public $unionSpotId; //适用诊所id

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%inspect}}';
    }

    public function init() {

        parent::init();
        $this->spot_id = $this->parentSpotId;
//         $this->cost_price = '0.00';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['status', 'create_time', 'update_time', 'spot_id', 'tag_id'], 'integer'],
//            [['inspect_price', 'cost_price',], 'number'],
            [['phonetic', 'international_code', 'remark'], 'string', 'max' => 50],
            [['inspect_name','inspect_english_name'], 'string', 'max' => 100],
            [['inspect_unit'], 'string', 'max' => 20],
            [['inspect_name', 'spot_id'], 'required'],
            [['tag_id'], 'default', 'value' => 0],
            [['inspect_name'],'validateName'],
            [['unionSpotId'],'required','on'=>'unionSpotId'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'inspect_name' => '名称',
            'inspect_unit' => '单位',
            'inspect_price' => '零售价',
            'cost_price' => '成本价',
            'phonetic' => '拼音码',
            'international_code' => '国际编码',
            'remark' => '医嘱备注',
            'tag_id' => '标签',
            'status' => '状态',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'item_name' => '项目名称',
            'english_name' => '英文缩写',
            'inspect_english_name' => '英文名',
            'unit' => '单位',
            'deliver' => '标本是否外送检验',
            'specimen_type' => '标本种类',
            'cuvette' => '试管盖颜色',
            'inspect_type' => '检验类型',
            'reference' => '参考值',
            'spot_id' => '机构ID',
            'unionSpotId'=>'适用诊所',
        ];
    }

    /**
     * @property 返回实验室检查列表信息
     * @status 是否停用
     * @return \yii\db\ActiveRecord[]
     */
    public static function getInspectList($status = 1, $type = 1,$ifUnion = 0) {
        $query = new \yii\db\Query();
        $inspect = $query->from(['a' => self::tableName()])
                ->select(['a.id', 'name' => 'a.inspect_name', 'unit' => 'a.inspect_unit', 'a.international_code', 'a.phonetic', 'a.remark', 'price' => 'a.inspect_price', 'a.tag_id', 'a.deliver', 'a.specimen_type',
                    'a.cuvette', 'a.inspect_type', 'a.inspect_english_name', 'tagName' => 'b.name', 'a.status'])
                ->leftJoin(['b' => Tag::tableName()], '{{a}}.tag_id={{b}}.id')
                ->where(['a.spot_id' => self::$staticParentSpotId]);
                if($ifUnion) {
                    $query->leftJoin(['c' => ConfigureClinicUnion::tableName()], '{{a}}.id={{c}}.configure_id');
                    $query->andFilterWhere(['c.type' => ChargeInfo::$inspectType, 'c.spot_id' => self::$staticSpotId]);
        }
        if ($status == 1) {
            $query->andFilterWhere(['a.status' => 1]);
        }
        $inspect = $query->indexBy('id')->all();
        if ($inspect) {
            $inspectItemId = array_column($inspect, 'id');
            $inspectItemArr = InspectItemUnion::getInspectItem($inspectItemId, $type);
            foreach ($inspect as &$val) {
                $val['inspectItem'] = isset($inspectItemArr[$val['id']]) ? $inspectItemArr[$val['id']] : [];
            }
        }
        return $inspect;
    }

    /**
     * 
     * @param type $id 检验医嘱ID
     * @return 根据ID获取检验医嘱的信息
     */
    public static function getInspectById($id) {
        return self::find()->select(['id', 'inspect_name', 'inspect_unit', 'phonetic', 'inspect_english_name', 'international_code', 'tag_id', 'remark', 'status'])->where(['id' => $id, 'spot_id' => self::$staticParentSpotId])->asArray()->one();
    }

    /**
     * @property 是否外送
     * @var array
     */
    public static $getDeliver = [
        1 => '是',
        2 => '否'
    ];

    /**
     * @property 获取标本种类
     * @var array
     */

    /**
     * @property 获取标本种类
     * @var array
     */
    public static $getSpecimenType = [
        1 => '血清',
        2 => '血浆',
        3 => '全血',
        4 => '末梢血',
        5 => '全血/末梢血',
        6 => '尿液',
        7 => '粪便',
        8 => '痰液',
        9 => '宫颈上皮细胞',
        10 => '生殖器分泌物',
        11 => '咽分泌物',
        12 => '阴道分泌物',
        13 => '脑脊液',
        14 => '分泌物',
        15 => '咽拭子',
        16 => '皮肤组织病理',
        17 => '囊肿组织病理',
        18 => '宫颈分泌物',
        19 => '疱疹液',
        20 => '赘生物',
        21 => '宫颈息肉组织病理',
        22 => '其他组织病理',
        23 => '其他',
        24 => '精液',
    ];

    /**
     * @property 获取试管盖颜色
     * @var array
     */
    public static $getCuvette = [

        1 => '红',
        2 => '紫',
        3 => '黑',
        4 => '蓝',
        5 => '黄',
        6 => '绿',
        7 => '灰',
        8 => '橙'
    ];

    /**
     * @param $attribute
     * @param $params
     * @return bool   返回实验室检查唯一性判断
     */
    public function validateName($attribute,$params){

        if(!$this->hasErrors()){
            $hasRecord=self::find()->where(['inspect_name'=>trim($this->inspect_name),'spot_id'=>$this->parentSpotId])->count();
            if($this->isNewRecord){
                if($hasRecord){
                    $this->addError('inspect_name',   '该实验室检查已存在');
                }
            }else{
                $oldInpectName=$this->getOldAttribute('inspect_name');
                if($oldInpectName != trim($this->inspect_name) && $hasRecord){
                    $this->addError('inspect_name',   '该实验室检查已存在');
                    
                }
            }
        }
    }

}
