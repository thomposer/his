<?php

namespace app\modules\spot\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;
use app\modules\spot_set\models\CardDiscountClinic;

/**
 * This is the model class for table "t_card_recharge_category".
 *
 * @property string $f_physical_id
 * @property string $f_category_name
 * @property string $f_category_desc
 * @property integer $f_state
 * @property string $f_parent_id
 * @property string $f_medical_fee_discount
 * @property string $f_inspect_discount
 * @property string $f_check_discount
 * @property string $f_cure_discount
 * @property string $f_recipe_discount
 * @property string $f_create_time
 * @property string $f_update_time
 */
class CardRechargeCategory extends ActiveRecord
{

    public $service;
    public static $staticParentSpotId; //静态变量，当前机构id

    public function init() {
        parent::init();
        $this->f_upgrade_time = 365; //TODO  可能会变 暂时先写死
        $this->f_spot_id = isset($_COOKIE['parentSpotId']) ? $_COOKIE['parentSpotId'] : 0;
        self::$staticParentSpotId = $this->f_spot_id;
    }

    public function behaviors() {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%card_recharge_category}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('cardCenter');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['f_category_name'], 'required', 'on' => 'group'],
            [['f_category_name', 'f_parent_id'], 'required', 'on' => 'category'],
            [['f_upgrade_amount'], 'validateUpgradeAmount', 'on' => 'category'],
            [['f_auto_upgrade'], 'validateAutoUpgrade', 'on' => 'category'],
            [['f_state', 'f_parent_id', 'f_auto_upgrade', 'f_upgrade_time', 'f_create_time', 'f_update_time', 'f_spot_id', 'f_level'], 'integer'],
            [['f_medical_fee_discount', 'f_inspect_discount', 'f_check_discount', 'f_cure_discount', 'f_recipe_discount', 'f_upgrade_amount'], 'number'],
            [['f_medical_fee_discount'], 'match', 'pattern' => '/^(((\d|[1-9]\d)(\.[0-9]{1,2})?)|(100(\.0{1,2})?))?$/', 'message' => '诊金只能精确到小数点后两位,且在0-100间'],
            [['f_inspect_discount'], 'match', 'pattern' => '/^(((\d|[1-9]\d)(\.[0-9]{1,2})?)|(100(\.0{1,2})?))?$/', 'message' => '诊金只能精确到小数点后两位,且在0-100间'],
            [['f_check_discount'], 'match', 'pattern' => '/^(((\d|[1-9]\d)(\.[0-9]{1,2})?)|(100(\.0{1,2})?))?$/', 'message' => '诊金只能精确到小数点后两位,且在0-100间'],
            [['f_cure_discount'], 'match', 'pattern' => '/^(((\d|[1-9]\d)(\.[0-9]{1,2})?)|(100(\.0{1,2})?))?$/', 'message' => '诊金只能精确到小数点后两位,且在0-100间'],
            [['f_recipe_discount'], 'match', 'pattern' => '/^(((\d|[1-9]\d)(\.[0-9]{1,2})?)|(100(\.0{1,2})?))?$/', 'message' => '诊金只能精确到小数点后两位,且在0-100间'],
            [['f_category_name'], 'string', 'max' => 15],
            [['f_category_desc'], 'string', 'max' => 200],
            [['f_medical_fee_discount'], 'default', 'value' => 100],
            [['f_level'], 'default', 'value' => 0]
        ];
    }

    public function scenarios() {

        $parent = parent::scenarios();
        $parent['group'] = ['f_category_name', 'f_category_desc', 'f_state', 'f_level']; //卡组场景
        $parent['category'] = ['f_category_name', 'f_parent_id', 'f_category_desc', 'f_medical_fee_discount', 'f_inspect_discount', 'f_check_discount', 'f_cure_discount', 'f_recipe_discount', 'f_auto_upgrade', 'f_upgrade_time', 'f_upgrade_amount']; //卡种场景
        return $parent;
    }

    public function beforeSave($insert) {
        //判断当前表中是否相应更新时间字段
        if ($insert) {
            $this->f_spot_id = isset($_COOKIE['parentSpotId']) ? $_COOKIE['parentSpotId'] : 0;
            $this->f_create_time = time();
        }
        $this->f_update_time = time();
//        if (!$this->f_upgrade_amount) {
//            $this->f_upgrade_amount = 0.00;
//        }
        if ($this->f_auto_upgrade == 2) {
            $this->f_upgrade_amount = null;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'f_physical_id' => 'F Physical ID',
            'f_category_name' => '名称',
            'f_category_desc' => '描述',
            'f_state' => '状态',
            'f_parent_id' => '所属卡组',
            'f_medical_fee_discount' => '诊金(%)',
            'f_inspect_discount' => '实验室检查(%)',
            'f_check_discount' => '影像学检查(%)',
            'f_cure_discount' => '治疗(%)',
            'f_recipe_discount' => '药品(%)',
            'f_create_time' => 'F Create Time',
            'f_update_time' => '最后修改时间',
            'service' => '服务',
            'f_upgrade_amount' => '充值额累计（元）',
            'f_upgrade_time' => '时长范围',
            'f_level' => '等级'
        ];
    }

    /**
     * @param type $id 卡组ID
     * @return 获取卡种分类
     */
    public static function findSubDataProvider($id) {
        $query = self::find()->select(['f_physical_id', 'f_category_name', 'f_category_desc', 'f_state', 'f_update_time', 'f_medical_fee_discount', 'f_inspect_discount', 'f_check_discount', 'f_cure_discount', 'f_recipe_discount'])->where(['f_parent_id' => $id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
    }

    public static function getCategory() {
        $list = self::find()->select(['f_physical_id', 'f_category_name'])->where(['f_parent_id' => 0, 'f_state' => 2, 'f_spot_id' => $_COOKIE['parentSpotId']])->indexBy('f_physical_id')->asArray()->all();
        return $list;
    }

    public static $getState = [
        1 => '未发行',
        2 => '已发行',
        3 => '停止发行'
    ];
    public static $getAutoUpgrage = [
        '2' => '不可从其他卡种自动升级',
        '1' => '可从其他卡种自动升级',
    ];

    public static function getLevel() {
        $level = [];
        for ($i = 1; $i <= 5; $i++) {
            $data = self::find()->select(['f_category_name'])->where(['f_level' => $i, 'f_state' => 2, 'f_spot_id' => self::$staticParentSpotId])->asArray()->limit(10)->all();
            $cateArr = '';
            if (!empty($data)) {
                $cateArr = implode('，', array_column($data, 'f_category_name'));
            }
            $cateArr = $cateArr ? \app\common\Common::cutStr($cateArr, 25) : '';
            $cateArr = $cateArr ? '(' . $cateArr . ')' : '';
            $level[$i] = $i . $cateArr;
        }
        return $level;
    }

    /**
     * 
     * @return type 获取卡种二级结构
     */
    public static function getCardCategory() {
        $data = self::find()->select(['f_physical_id', 'f_category_name', 'f_parent_id'])->where(['f_state' => 2, 'f_spot_id' => $_COOKIE['parentSpotId']])->asArray()->all();
        $parent = [];
        $sub = [];
        $res = [];
        foreach ($data as $v) {
            if ($v['f_parent_id'] == 0) {//parent
                $parent[$v['f_physical_id']] = $v;
            } else {
                $sub[$v['f_parent_id']][$v['f_physical_id']] = $v['f_category_name'];
            }
        }
        foreach ($parent as $p) {
            $res[$p['f_category_name']] = isset($sub[$p['f_physical_id']]) ? $sub[$p['f_physical_id']] : [];
        }
        return $res;
    }

    /**
     * 
     * @param type $id 卡种ID
     * @return 获取卡种信息
     */
    public static function getCategoryById($id) {
        return self::find()->select(['f_physical_id', 'f_category_name'])->where(['f_physical_id' => $id])->asArray()->one();
    }

    public static function getCategoryService() {
        $data = self::find()->select(['f_physical_id', 'f_category_name', 'f_parent_id', 'f_medical_fee_discount', 'f_inspect_discount', 'f_check_discount', 'f_cure_discount', 'f_recipe_discount', 'f_category_desc'])->where(['f_state' => 2, 'f_spot_id' => $_COOKIE['parentSpotId']])->andWhere(['!=', 'f_parent_id', 0])->asArray()->all();
        $res = [];
        if (!empty($data)) {
            foreach ($data as $val) {
                $text = [];
//                $cardDiscountList = CardDiscount::getCardTagDiscount($val['f_physical_id']);
                $cardDiscountList = CardDiscountClinic::cardDiscountListClinic($val['f_physical_id']);
                foreach ($cardDiscountList as $v) {
                    $text[] = $v['name'] . $v['discount'] . '%';
                }
//                 $val['f_inspect_discount'] != null && $text[] = '实验室检查' . $val['f_inspect_discount'] . '%';
//                 $val['f_check_discount'] != null && $text[] = '影像学检查' . $val['f_check_discount'] . '%';
//                 $val['f_cure_discount'] != null && $text[] = '治疗' . $val['f_cure_discount'] . '%';
//                 $val['f_recipe_discount'] != null && $text[] = '药品' . $val['f_recipe_discount'] . '%';
                $textStr = implode('，', $text);
                $res[$val['f_physical_id']] = [
                    'service' => \yii\helpers\Html::encode($textStr),
                    'serviceDesc' => \yii\helpers\Html::encode($val['f_category_desc'])
                ];
            }
        }
        return $res;
    }

    /**
     * 
     * @param type $catId 获取机构下 某个卡种所有诊所的折扣信息
     */
    public static function getCatServiceListById($catId) {
        $data = [];
        $cardDiscount = CardDiscountClinic::find()->select(['spot_id', 'recharge_category_id', 'tag_id', 'discount'])->where(['recharge_category_id' => $catId, 'parent_spot_id' => $_COOKIE['parentSpotId']])->asArray()->all();
        $spotIdArr = array_unique(array_column($cardDiscount, 'spot_id'));
        $tagIdArr = array_unique(array_column($cardDiscount, 'tag_id'));
        $spotInfo = Spot::find()->select(['spot_name', 'id'])->where(['id' => $spotIdArr])->indexBy('id')->asArray()->all();
        $tagInfo = Tag::find()->select(['name', 'id'])->where(['id' => $tagIdArr])->indexBy('id')->asArray()->all();
        if (!empty($cardDiscount)) {
            foreach ($cardDiscount as &$val) {
                $val['tagName'] = ($val['tag_id'] == 0) ? '诊金' : $tagInfo[$val['tag_id']]['name'];
                $val['spotName'] = isset($spotInfo[$val['spot_id']]) ? $spotInfo[$val['spot_id']]['spot_name'] : '';
                if (isset($data[$val['spot_id']])) {
                    $data[$val['spot_id']]['discount'] = $data[$val['spot_id']]['discount'] . '，' . $val['tagName'] . $val['discount'] . '%';
                } else {
                    $data[$val['spot_id']] = [
                        'spotName' => $val['spotName'],
                        'discount' => $val['tagName'] . $val['discount'] . '%'
                    ];
                }
            }
        }
        return $data;
    }

    public function validateUpgradeAmount($attribute) {
        if ($this->f_auto_upgrade == 1) {//需要升级金额
            if ($this->$attribute == '') {
                $this->addError($attribute, '充值额累计不能为空');
            }
            if (!preg_match("/^([0-9][0-9]*)+(.[0-9]{1,2})?$/", $this->$attribute)) {
                $this->addError($attribute, '最多保留两位小数.');
            }
            if ($this->$attribute < 0.01 || $this->$attribute > 999999.99) {
                $this->addError($attribute, '金额必须在0.01-999,999.99元之间');
            }
            $record = self::find()->select(['f_physical_id'])->where(['f_upgrade_amount' => $this->$attribute, 'f_spot_id' => $_COOKIE['parentSpotId']])->andFilterWhere(['<>', 'f_physical_id', $this->f_physical_id])->asArray()->one();
            if (!empty($record)) {
                $this->addError($attribute, '不可与其他充值卡数值相同');
            }
        }
    }

    public function validateAutoUpgrade($attribute) {
        if ($this->$attribute == 1) {//需要升级金额
            if ($this->f_upgrade_amount == '') {
                $this->addError('f_upgrade_amount', '充值额累计不能为空');
            }
        }
    }

    /**
     * @param $level 卡种等级
     * @return array 获取可升级的卡种
     */
    public static function getCardUpgradeCategroy($level) {
        $query = new \yii\db\Query();
        $data = $query->from(['t1' => CardRechargeCategory::tableName()])
                ->select(['t2.f_level', 'categoryName' => 't1.f_category_name', 't1.f_upgrade_amount', 'groupName' => 't2.f_category_name',])
                ->leftJoin(['t2' => CardRechargeCategory::tableName()], "{{t1}}.f_parent_id={{t2}}.f_physical_id")
                ->where(['t1.f_spot_id' => $_COOKIE['parentSpotId'], 't1.f_state' => 2, 't1.f_auto_upgrade' => 1])
                ->andWhere(['>', 't2.f_level', $level])
                ->orderBy(['t2.f_level' => SORT_ASC, 't1.f_upgrade_amount' => SORT_ASC])
                ->all(self::getDb());
        return $data;
    }

}
