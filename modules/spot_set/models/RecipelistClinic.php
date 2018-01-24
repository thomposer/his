<?php

namespace app\modules\spot_set\models;

use app\modules\spot\models\RecipeList;
use Yii;
use app\modules\stock\models\StockInfo;
use yii\db\Query;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;

/**
 * This is the model class for table "{{%recipelist_clinic}}".
 *
 * @property integer $id
 * @property string $spot_id
 * @property string $recipelist_id
 * @property string $price
 * @property string $default_price
 * @property integer $status
 *
 * @property Recipelist $recipelist
 */
class RecipelistClinic extends \app\common\base\BaseActiveRecord
{

    public $name; //药品名称
    public $specification; //规格
    public $unit; //包装单位
    public $meta; //拼音码
    public $remark; //用药须知
    public $drug_type; //药品分类
    public $type; //剂型
    public $dose_unit; //剂量单位
    public $manufactor; //生产厂家
    public $high_risk; //是否为高危药品
    public $insurance; //是否医保
    public $default_used; //默认用法
    public $default_consumption;
    public $skin_test_status;
    public $tag_name;
    public $general_name;
    public $product_name; //商品名
    public $en_name; //英文名
    public $app_number; //国药准字
    public $import_regist_no; //进口注册证号
    public $international_code;
    public $skin_test;
    public $shelves1;    //货架号
    public $shelves2;   //货架号
    public $shelves3;   //货架号
    public $totalName;  //名称

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%recipelist_clinic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['recipelist_id', 'price', 'address'], 'required'],
            [['spot_id', 'recipelist_id', 'status', 'shelves1', 'shelves2', 'shelves3'], 'integer'],
            [['price', 'default_price', 'address', 'shelves_sort'], 'number'],
            [['price', 'default_price'], 'number', 'max' => 100000, 'min' => 0],
            [['price', 'default_price'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
            [['recipelist_id'], 'exist', 'skipOnError' => true, 'targetClass' => RecipeList::className(), 'targetAttribute' => ['recipelist_id' => 'id']],
            [['recipelist_id'], 'validateRecipelistId'],
            [['shelves1', 'shelves2', 'shelves3'], 'integer', 'max' => 999, 'min' => 0],
            [['shelves1'], 'validateShelves', 'skipOnEmpty' => false],
            [['shelves'], 'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'recipelist_id' => '药品名称',
            'price' => '零售价',
            'default_price' => '成本价',
            'status' => '状态',
            'address' => '取药地点',
            'name' => '药品名称',
            'specification' => '规格',
            'unit' => '包装单位',
            'meta' => '拼音码',
            'remark' => '用药须知',
            'drug_type' => '药品分类',
            'type' => '剂型',
            'dose_unit' => '剂量单位',
            'manufactor' => '生产厂家',
            'high_risk' => '是否为高危药品',
            'product_name' => '商品名',
            'en_name' => '英文名称',
            'app_number' => '国药准字',
            'import_regist_no' => '进口注册证号',
            'international_code' => '国际编码',
            'default_used' => '默认用法',
            'default_consumption' => '默认用量',
            'skin_test_status' => '是否需要皮试提示',
            'skin_test' => '皮试内容',
            'insurance' => '是否医保',
            'tag_name' => '充值卡折扣标签',
            'general_name' => '通用标签',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'shelves1' => '货架号',
            'shelves2' => '货架号',
            'shelves3' => '货架号',
            'shelves' => '货架号',
            'shelves_sort' => '货架号排序',
            'totalName'=>'名称',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipelist() {
        return $this->hasOne(Recipelist::className(), ['id' => 'recipelist_id']);
    }

    /**
     * @property 根据库存量 来获取处方医嘱列表
     */
    public static function getReciptListByStock($where = []) {
        $fields = [
            't1.id',
            't1.recipelist_id',
            't1.price',
            't1.default_price',
            't2.num',
            't3.medicine_description_id',
            't3.dose_unit',
            't3.name',
            't3.product_name',
            't3.en_name',
            't3.specification',
            't3.unit',
            't3.type',
            't3.drug_type',
            't3.manufactor',
            't3.specification',
            't3.skin_test_status',
            't3.skin_test',
            't3.meta',
            't3.remark',
            't3.tag_id',
            't3.high_risk'
        ];
        $query = new \yii\db\Query();
        $recipetList = $query->from(['t1' => self::tableName()])->select($fields)
                ->leftJoin(['t3' => RecipeList::tableName()], '{{t1}}.recipelist_id = {{t3}}.id')
                ->leftJoin(['t2' => StockInfo::tableName()], '{{t3}}.id={{t2}}.recipe_id')
                ->where(['t1.spot_id' => self::$staticSpotId, 't1.status' => 1])
                ->andFilterWhere($where)->indexBy('id')
                ->orderBy(['t2.num' => SORT_DESC])
                ->all();
        foreach ($recipetList as $key => $val) {
            $dose_unit = explode(',', $val['dose_unit']);
            $dose_unit_all = array();
            foreach ($dose_unit as $value) {
                $dose_unit_all[$value] = RecipeList::$getDoseUnit[$value];
            }
            $recipetList[$key]['dose_unit'] = $dose_unit_all;
            $recipetList[$key]['dose_unit_num'] = count($dose_unit);
        }
        return $recipetList;
    }

    /**
     * @property 获取status ＝ 1的处方的基本信息
     */
    public static function getList($fields = null) {
        if (!$fields) {
            $fields = ['a.id', 'a.recipelist_id', 'a.price', 'a.default_price', 'b.name', 'b.specification', 'b.unit', 'b.manufactor', 'b.remark', 'b.medicine_description_id'];
        }
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select($fields);
        $query->leftJoin(['b' => RecipeList::tableName()], '{{a}}.recipelist_id = {{b}}.id');
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.status' => 1]);
        $query->indexBy('id');

        return $query->all();
    }

    /*
     * 验证recipelistId是否重复
     */

    public function validateRecipelistId($attribute, $params) {
        if ($this->isNewRecord) {
            $unionClinicRecord = ConfigureClinicUnion::find()->where(['configure_id' => $this->recipelist_id, 'spot_id' => $this->spotId, 'type' => ChargeInfo::$recipeType])->count();
            if (empty($unionClinicRecord)) {
                $this->addError('recipelist_id', '该处方医嘱已取消关联');
            }
            if ($this->checkDuplicate($attribute, $this->$attribute)) {
                $this->addError($attribute, '该处方医嘱已存在');
            }
        }else{
            $hasRecord = self::find()->where(['recipelist_id' =>$this->recipelist_id,'spot_id' =>$this->spotId])->count();
            $oldRecipelistId = $this->getOldAttribute('recipelist_id');

            if($oldRecipelistId != $this->recipelist_id && $hasRecord){

                $this->addError('recipelist_id',   '该处方医嘱已存在');

            }
        }
    }

    /*
     * 验证Shelves是否都填写或者都不填写
     */

    public function validateShelves($attribute, $params) {
        if (($this->shelves1 != null || $this->shelves2 != null || $this->shelves3 != null) && !($this->shelves1 != null && $this->shelves2 != null && $this->shelves3 != null)) {
            if ($this->shelves1 == '') {
                $this->addError('shelves1', '货架号填写有误');
            }
            if ($this->shelves2 == '') {
                $this->addError('shelves2', '货架号填写有误');
            }
            if ($this->shelves3 == '') {
                $this->addError('shelves3', '货架号填写有误');
            }
        }
        if ($this->shelves1 != null && $this->shelves2 != null && $this->shelves3 != null) {
            if (strlen($this->shelves1) > 3) {
                $this->addError('shelves1', '货架号填写有误');
            }
            if (strlen($this->shelves2) > 3) {
                $this->addError('shelves2', '货架号填写有误');
            }
            if (strlen($this->shelves3) > 3) {
                $this->addError('shelves3', '货架号填写有误');
            }
        }
    }

    protected function checkDuplicate($attribute, $params) {
        $hasRecord = RecipelistClinic::find()->select(['recipelist_id'])->where([$attribute => trim($this->$attribute), 'spot_id' => $this->spot_id])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }

    public function shelvesSum($shelves1, $shelves2, $shelves3) {
        $str1 = str_pad($shelves1, 3, '0', STR_PAD_LEFT);
        $str2 = str_pad($shelves2, 3, '0', STR_PAD_LEFT);
        $str3 = str_pad($shelves3, 3, '0', STR_PAD_LEFT);
        return intval($str1 . $str2 . $str3);
    }

    public function beforeSave($insert) {

        if ($this->shelves1 != null && $this->shelves2 != null && $this->shelves3 != null) {
            $this->shelves1 = trim(intval($this->shelves1));
            $this->shelves2 = trim(intval($this->shelves2));
            $this->shelves3 = trim(intval($this->shelves3));
            $this->shelves = $this->shelves1 . '-' . $this->shelves2 . '-' . $this->shelves3;
            $this->shelves_sort = $this->shelvesSum($this->shelves1, $this->shelves2, $this->shelves3);
        }

        return parent::beforeSave($insert);
    }

}
