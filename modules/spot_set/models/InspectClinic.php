<?php

namespace app\modules\spot_set\models;

use Yii;
use app\modules\spot_set\models\InspectItemUnionClinic;
use app\modules\spot\models\InspectItem;
use app\modules\spot\models\Inspect;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;

/**
 * This is the model class for table "{{%inspect_clinic}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $inspect_id
 * @property string $inspect_price
 * @property string $cost_price
 * @property integer $deliver
 * @property integer $specimen_type
 * @property integer $cuvette
 * @property string $inspect_type
 * @property string $remark
 * @property string $description
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class InspectClinic extends Inspect
{

    public $inspectUnit; //单位
    public $phonetic; //拼音码
    public $doctorRemark; //医嘱备注
    public $internationalCode; //国际编码
    public $englishName; //英文名称
    public $tagId; //标签
    public $item; //检验项目
    public $parentStatus;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%inspect_clinic}}';
    }

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'inspect_id', 'deliver', 'specimen_type', 'cuvette'], 'integer'],
            [['inspect_price', 'cost_price'], 'number'],
            [['inspect_price', 'cost_price'], 'number', 'min' => 0, 'max' => 100000],
            [['inspect_price', 'deliver', 'inspect_id', 'item', 'specimen_type'], 'required'],
            [['inspect_price', 'cost_price'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
            [['specimen_type', 'cuvette'], 'default', 'value' => 0],
            [['inspect_type'], 'string', 'max' => 15],
            [['inspect_type'], 'trim'],
            [['inspect_type'], 'default', 'value' => ''],
            [['description', 'remark'], 'string', 'max' => 100],
            ['deliver_organization', 'validateDeliverOrganization', 'skipOnEmpty' => false],
            ['deliver_organization', 'default', 'value' => 0],
            [['inspectUnit', 'phonetic', 'doctorRemark', 'internationalCode', 'tagId', 'item', 'parentStatus', 'englishName'], 'safe'],
               [['inspect_id'],'validateInspect']
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['union'] = ['item'];
        return $parent;
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            if ($this->deliver == 2) {
                $this->deliver_organization = 0;
            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'inspect_id' => '实验室检查ID',
            'inspect_price' => '零售价',
            'cost_price' => '成本价',
            'deliver' => '是否外送',
            'specimen_type' => '标本种类',
            'cuvette' => '试管颜色',
            'inspect_type' => '检验类型',
            'remark' => '备注',
            'description' => '说明',
            'status' => '状态',
            'parentStatus' => '状态',
            'inspect_id' => '实验室检查名称',
            'inspectUnit' => '单位',
            'phonetic' => '拼音码',
            'doctorRemark' => '备注',
            'internationalCode' => '国际编码',
            'tagId' => '标签',
            'item' => '检验项目',
            'inspectName' => '名称',
            'englishName' => '英文名称',
            'deliver_organization' => '外送机构'
        ];
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
     * @property 实验室
     * @var array
     */
    public static $laboratory = [
        1 => '金域',
        2 => '诊所内'
    ];

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
     *
     * @desc 外送机构
     */
    public static $getDeliverOrganization = [
        1 => '金域',
        2 => '迪安',
        3 => '艾迪康',
        4 => '达安',
        5 => '嘉检'
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

    public function validateDeliverOrganization($attribute, $param) {
        if (!$this->hasErrors()) {
            if ($this->deliver == 1 && !$this->deliver_organization) {
                $this->addError('deliver_organization', '外送机构不能为空。');
            }
        }
    }

    /**
     * 
     * @param type $inspectClinicId 诊所检验医嘱ID
     * @return 获取诊所下检验医嘱关联的项目
     */
    public static function itemtList($inspectClinicId) {
        $query = new \yii\db\Query();
        $data = $query->from(['a' => InspectItemUnionClinic::tableName()])
                ->select(['a.item_id', 'b.item_name', 'b.unit', 'b.reference', 'english_name'])
                ->leftJoin(['b' => InspectItem::tableName()], '{{a}}.item_id={{b}}.id')
                ->where(['a.clinic_inspect_id' => $inspectClinicId, 'a.spot_id' => self::$staticSpotId, 'b.status' => 1])
                ->all();
        return $data;
    }

    /**
     * 
     * @param type $clinicInspectId 诊所检验医嘱ID
     * @return  获取诊所关联结构的检验医嘱信息
     */
    public static function inspectAndClinic($clinicInspectId) {
        return (new \yii\db\Query())->from(['a' => self::tableName()])
                        ->select(['a.inspect_price', 'a.cost_price', 'a.deliver', 'clinicStatus' => 'a.status', 'b.status'])
                        ->leftJoin(['b' => Inspect::tableName()], '{{a}}.inspect_id={{b}}.id')
                        ->where(['a.id' => $clinicInspectId, 'a.spot_id' => self::$staticSpotId])
                        ->one();
    }

    /**
     * @param array|string $where 查询条件
     * @property 返回实验室检查列表信息
     * @return \yii\db\ActiveRecord[]
     */
    public static function getInspectClinicList($where = []) {
        $query = new \yii\db\Query();
        $inspect = $query->from(['a' => self::tableName()])
                        ->select(['a.id', 'name' => 'b.inspect_name', 'unit' => 'b.inspect_unit', 'b.international_code', 'b.phonetic', 'b.remark', 'b.inspect_english_name', 'price' => 'a.inspect_price', 'b.tag_id', 'a.deliver', 'a.deliver_organization', 'a.specimen_type',
                            'a.cuvette', 'a.inspect_type', 'a.inspect_id', 'a.remark', 'a.description'])
                        ->leftJoin(['b' => Inspect::tableName()], '{{a}}.inspect_id={{b}}.id')
                        ->where(['a.spot_id' => self::$staticSpotId, 'a.status' => 1])->andFilterWhere($where)->indexBy('id')->all();
        if ($inspect) {
            $inspectClinicId = array_column($inspect, 'id');
            $inspectItemClinicArr = InspectItemUnionClinic::getInspectItemClinic($inspectClinicId);
            foreach ($inspect as &$val) {
                $val['inspectItem'] = isset($inspectItemClinicArr[$val['id']]) ? $inspectItemClinicArr[$val['id']] : [];
            }
        }
        return $inspect;
    }

    /**
     * @param $attribute
     * @param $params
     * @return bool   返回实验室检查唯一性判断
     */
    public function validateInspect($attribute,$params){

        if(!$this->hasErrors()){
            $hasRecord=self::find()->where(['inspect_id' => $this->inspect_id,'spot_id' => $this->spotId])->count();
            if($this->isNewRecord){
                $unionClinicRecord = ConfigureClinicUnion::find()->where(['configure_id' => $this->inspect_id, 'spot_id' => $this->spotId, 'type' => ChargeInfo::$inspectType])->count();
                if (empty($unionClinicRecord)) {
                    $this->addError('inspect_id', '该实验室检查已取消关联');
                }
                if($hasRecord){
                    $this->addError('inspect_id',   '该实验室检查已存在');
                }
            }else{
                $oldInpectId=$this->getOldAttribute('inspect_id');
                if($oldInpectId != $this->inspect_id && $hasRecord){
                    $this->addError('inspect_id',   '该实验室检查已存在');
                }
            }
        }
    }

}
