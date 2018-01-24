<?php

namespace app\modules\spot\models;

use Yii;
use app\modules\stock\models\StockInfo;
use app\common\base\BaseActiveRecord;
use yii\db\Query;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;

/**
 * This is the model class for table "{{%recipelist}}".
 * 处方医嘱配置表
 * @property string $id
 * @property integer $spot_id
 * @property string $name
 * @property string $product_name
 * @property string $en_name
 * @property string $specification
 * @property string $unit
 * @property integer $type
 * @property string $manufactor
 * @property string $app_number
 * @property string $drug_type
 * @property string $medicine_code
 * @property string $meta
 * @property string $product_batch
 * @property string $address
 * @property integer $default_used
 * @property integer $default_frequency
 * @property integer $default_consumption
 * @property string $bar_code
 * @property string $international_code
 * @property string $remark
 * @property integer $high_risk
 * @property integer $tag_id
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property string $medicine_description_id 用药指南表id
 * @property integer $skin_test_status 是否需要皮试(0-没，1-是)
 * @property string $skin_test 皮试内容
 */
class RecipeList extends BaseActiveRecord
{

    public $recipelist_id; //诊所下的处方ID
    public $adviceTagId;//通用标签
    public $unionSpotId;//适用诊所id
    public $totalName; //名称
    public function init() {

        parent::init();
        $this->spot_id = $this->parentSpotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%recipelist}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['type', 'spot_id', 'drug_type', 'default_used', 'default_frequency', 'default_consumption', 'status', 'create_time', 'update_time', 'insurance', 'medicine_description_id', 'skin_test_status', 'tag_id'], 'integer'],
            [['name', 'spot_id', 'unit', 'drug_type', 'dose_unit', 'type', 'specification', 'manufactor', 'high_risk'], 'required'],
            [['price', 'default_price'], 'number', 'min' => 0, 'max' => 100000],
            [['price'], 'default', 'value' => '0.00'],
            [['en_name', 'manufactor', 'app_number', 'medicine_code', 'product_batch', 'address', 'bar_code'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 100],
            [['unit', 'meta', 'international_code'], 'string', 'max' => 16],
            [['import_regist_no'], 'string', 'max' => 20],
            [['default_frequency', 'default_consumption', 'type', 'dose_unit', 'insurance', 'default_used', 'medicine_description_id'], 'default', 'value' => '0'],
            [['skin_test'], 'string', 'max' => 64],
            [['skin_test'], 'default', 'value' => ''],
            [['tag_id', 'high_risk'], 'default', 'value' => 0],
            [['adviceTagId'],'validateAdviceTagId'],
            [['name'], 'string', 'max' => 20],
            [['product_name'], 'string', 'max' => 30],
            [[ 'specification'], 'string', 'max' => 15],
            [['name'],'validateName'],
            [['unionSpotId'], 'required', 'on' => 'update'],
//            [['unionSpotId'],'validateUnionSpotId'],


        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '机构ID',
            'name' => '药品名称',
            'product_name' => '商品名',
            'en_name' => '英文名称',
            'specification' => '规格',
            'unit' => '包装单位',
            'type' => '剂型',
            'price' => '零售价',
            'default_price' => '成本价',
            'manufactor' => '生产厂家',
            'app_number' => '国药准字',
            'drug_type' => '药品分类',
            'medicine_code' => '上药编码',
            'meta' => '拼音码',
            'product_batch' => '产品批次',
            'address' => '取药地点',
            'default_used' => '默认用法',
            'default_frequency' => '默认频次',
            'default_consumption' => '默认用量',
            'bar_code' => '药品条形码',
            'international_code' => '国际编码',
            'remark' => '用药须知',
            'tag_id' => '充值卡折扣标签',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'dose_unit' => '剂量单位',
            'insurance' => '是否医保',
            'medicine_description_id' => '关联用药指南',
            'skin_test_status' => '是否需要皮试提示',
            'skin_test' => '皮试内容',
            'import_regist_no' => '进口注册证号',
            'high_risk' => '是否为高危药品',
            'adviceTagId' => '通用标签',
            'unionSpotId' => '适用诊所',
            'totalName'=>'名称',
        ];
    }
    public function scenarios(){
        
        $parents = parent::scenarios();
        $parents['updateStatus'] = ['status'];
        return $parents;
    }
    /**
     *
     * @var 剂型
     */
    public static $getType = [
        1 => '片剂',
        2 => '肠溶片',
        3 => '含片',
        4 => '胶囊剂',
        5 => '颗粒剂',
        6 => '混悬剂',
        7 => '滴剂',
        8 => '散剂',
        9 => '胶浆剂',
        10 => '糖浆剂',
        11 => '注射液',
        12 => '冻干粉针剂',
        13 => '粉针剂',
        14 => '滴眼剂',
        15 => '滴耳剂',
        30 => '滴鼻剂',
        16 => '眼膏剂',
        17 => '栓剂',
        18 => '阴道片',
        19 => '泡腾片',
        20 => '鼻喷剂',
        21 => '喷雾剂',
        22 => '溶液剂',
        23 => '乳膏剂',
        24 => '软膏剂',
        25 => '贴剂',
        26 => '气雾剂',
        27 => '非药品'
    ];

    /**
     * 盒、袋、瓶、支、贴
     * @var 单位
     */
    public static $getUnit = [
        1 => '盒',
        2 => '袋',
        3 => '瓶',
        4 => '支',
        5 => '贴',
        6 => '片',
        7 => '粒',
        8 => '板',
    ];

    /**
     *
     * @var 默认用法
     */
    public static $getDefaultUsed = [
        1 => '肌肉注射',
        2 => '皮下注射',
        3 => '皮内注射',
        4 => '静脉滴注',
        5 => '静脉注射',
        6 => '口服',
        7 => '含服',
        8 => '舌下含服',
        9 => '化水溶解后口服',
        10 => '嚼碎后口服',
        11 => '塞肛用',
        12 => '阴道用',
        13 => '外用',
        14 => '雾化吸入',
        15 => '吸入',
        16 => '滴耳',
        17 => '滴鼻',
        18 => '滴眼',
        19 => '喷鼻',
        20 => '外涂',
        21 => '含漱',
    ];

    /**
     * 
     * @var 默认用量
     */
    public static $getDefaultConsumption = [
        15 => '1小时一次（Q1h）',
        16 => '2小时一次（Q2h）',
        17 => '4小时一次（Q4h）',
        18 => '6小时一次（Q6h）',
        19 => '8小时一次（Q8h）',
        20 => '12小时一次（Q12h）',
        1 => '每天四次（QID）',
        2 => '每天三次（TID）',
        3 => '每天两次（BID）',
        4 => '每天一次（QD）',
        5 => '隔天一次（QOD）',
//        6 => '每周两次',
        7 => '每周一次（QW）',
        21 => '晚上一次（QN）',
        8 => '必要时（PRN）',
        9 => '立即',
        10 => '空腹',
        11 => '饭前',
        12 => '饭中',
        13 => '饭后',
        14 => '睡前',
    ];

    /**
     *
     * @var 取药地点
     */
    public static $getAddress = [
        1 => '内购',
        2 => '外购'
    ];

    /*
     * @var 药品分类
     */
    public static $getDrugType = [
        1 => '消化系统用药',
        2 => '呼吸系统用药',
        3 => '非甾体解热镇痛药',
        4 => '抗感染类药物',
        5 => '类固醇',

        7 => '电解质及溶媒',
        8 => '流体和电解质失衡',
        9 => '矿物质类',
        10 => '维生素',

        12 => '眼科用药',
        13 => '耳科用药',
        14 => '鼻科用药',
        15 => '口腔科用药',
        16 => '皮肤科用药',
        17 => '局部麻醉药',
        18 => '中成药',
        19 => '心血管系统用药',
        20 => '精二药品',
        21 => '内分泌系统用药',
        22 => '抗过敏药',
        23 => '营养辅助类',
        24 => '血液系统',
        25 => '急救用针剂',

    ];

    /*
     * @var 是否医保
     */
    public static $getInsurance = [
        1 => '是',
        2 => '否',
        3 => '限',
    ];

    /*
     * @var 剂量单位
     */
    public static $getDoseUnit = [
        1 => 'g',
        2 => 'mg',
        3 => 'ug',
        4 => 'ml',
        5 => '粒',
        6 => '片',
        7 => '揿',
        8 => '滴',
        11 => '喷',
        9 => '单位',
        12 => '万单位',
        10 => '国际单位',
    ];

    /**
     * @return 皮试状态
     * @var 0-没,1-需要
     */
    public static $getSkinTestStatus = [
        0 => '否',
        1 => '是'
    ];

    /**
     * @return 高危药品
     * @var 0-否,1-是
     */
    public static $getHighRiskStatus = [
        2 => '否',
        1 => '是',
    ];
    public static $getHighRiskDesc = [
        0 => '',
        1 => '高危',
        2 => '',
    ];

//     public static $DoesUnit =[
//             1=>['id'=>1 ,'name'=>'g'],
//             2=>['id'=>2 ,'name'=>'mg'],
//             3=>['id'=>3 ,'name'=>'ug'],
//             4=>['id'=>4 ,'name'=>'ml'],
//             5=>['id'=>5 ,'name'=>'粒'],
//             6=>['id'=>6 ,'name'=>'片'],
//             7=>['id'=>7 ,'name'=>'揿'],
//             8=>['id'=>8 ,'name'=>'滴'],
//             9=>['id'=>9 ,'name'=>'单位'],
//             10=>['id'=>10 ,'name'=>'国际单位'],
//     ];

    public function beforeSave($insert) {

        if ($insert) {
            $this->create_time = time();
        }
        if($this->dose_unit && is_array($this->dose_unit)){
            $this->dose_unit = implode(',',$this->dose_unit);
        }
        $this->update_time = time();
        return parent::beforeSave($insert);
    }
    /**
     * @property 获取status ＝ 1的处方的基本信息
     */
    public static function getList($fields = null, $type = 1) {
        if (!$fields) {
            $fields = ['id', 'name', 'specification', 'unit', 'manufactor', 'price', 'default_price', 'remark', 'medicine_description_id'];
        }
        $query = self::find()->select($fields)->where(['spot_id' => self::$staticParentSpotId]);
        $type == 1 && $query->andWhere(['status' => 1]);
        return $query->indexBy('id')->asArray()->all();
    }
    
    /*
     * 根据适用诊所拉取列表
     */
    public static function getListByClinicUnion($where = '1 != 0') {
        $query = new Query();
        return $query->from(['a' => self::tableName()])
                        ->select(['a.id', 'a.name', 'a.specification', 'a.unit', 'a.manufactor', 'a.price', 'a.default_price', 'a.remark', 'a.medicine_description_id', 'a.dose_unit', 'a.drug_type', 'a.type', 'a.high_risk'])
                        ->leftJoin(['b' => ConfigureClinicUnion::tableName()], '{{a}}.id={{b}}.configure_id')
                        ->where(['a.spot_id' => self::$staticParentSpotId, 'b.type' => ChargeInfo::$recipeType, 'b.spot_id' => self::$staticSpotId])
                        ->andWhere($where)
                        ->indexBy('id')
                        ->all();
    }

    /**
     * @property 根据库存量 来获取处方医嘱列表
     */
    public static function getReciptListByStock() {
        $fields = [
            't1.id',
            't1.name',
            't1.product_name',
            't1.en_name',
            't1.specification',
            't1.unit',
            't1.type',
            't1.manufactor',
            't1.price',
            't1.default_price',
            't1.medicine_description_id',
            't2.num',
            't1.dose_unit',
            't1.specification',
            't1.skin_test_status',
            't1.skin_test',
            't1.meta',
            't1.remark',
            't1.tag_id',
            't1.high_risk'
        ];
        $query = new \yii\db\Query();
        $recipetList = $query->from(['t1' => self::tableName()])->select($fields)
                ->leftJoin(['t2' => StockInfo::tableName()], '{{t1}}.id={{t2}}.recipe_id')
                ->where(['t1.spot_id' => self::$staticParentSpotId, 't1.status' => 1])->indexBy('id')
                ->orderBy(['t2.num' => SORT_DESC])
                ->all();
        foreach ($recipetList as $key => $val) {
            $dose_unit = explode(',', $val['dose_unit']);
            $dose_unit_all = array();
            $unit_num = count($dose_unit);
            foreach ($dose_unit as $value) {
                $dose_unit_all[$value] = self::$getDoseUnit[$value];
            }
            $recipetList[$key]['dose_unit'] = $dose_unit_all;
            $recipetList[$key]['dose_unit_num'] = count($dose_unit);
        }
        return $recipetList;
    }
    
    public function validateAdviceTagId($attribute,$params){
        
        if(!$this->hasErrors()){
            if(!empty($this->adviceTagId)){
                if(count($this->adviceTagId) > 5){
                    $this->addError($attribute,'通用标签最多关联5个');
                }
            }
        }
    }
    
//    
//    public function validateUnionSpotId($attribute,$params){
//        $spotList = Spot::getSpotList();
//        $spotIdList = array_column($spotList, 'id');
//        if(!$this->hasErrors()){
//            if(!empty($this->unionSpotId)){
//                if(count(array_diff($this->unionSpotId, $spotIdList)) > 0 ){
//                    $this->addError($attribute,'适用诊所不能跨机构');
//                }
//            }
//        }
//    }

    /**
     * @param $attribute
     * @param $params
     * @return bool   返回处方医嘱唯一性判断
     */
    public function validateName($attribute,$params){

        if(!$this->hasErrors()){
            $hasRecord = self::find()->where(['name' =>trim($this->name),'specification' =>trim($this->specification),'manufactor' =>trim($this->manufactor),'spot_id' =>$this->parentSpotId])->count();
            if($this->isNewRecord){
                if($hasRecord){
                    $this->showError();
                }
            }else{
                $oldName = $this->getOldAttribute('name');

                $oldSpecification = $this->getOldAttribute('specification');

                $oldManufactor = $this->getOldAttribute('manufactor');
                if(($oldName != trim($this->name) || $oldSpecification != trim($this->specification) || $oldManufactor != trim($this->manufactor)) && $hasRecord){

                    $this->showError();
                }

            }
        }
    }

    protected function showError(){
        $this->addError('name',   '该医嘱已存在');
        $this->addError('specification',   '该医嘱已存在');
        $this->addError('manufactor',   '该医嘱已存在');
    }

}
