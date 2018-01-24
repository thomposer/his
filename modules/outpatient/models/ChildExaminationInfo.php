<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%child_examination_info}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $record_id
 * @property string $sleep
 * @property string $shit
 * @property string $pee
 * @property string $visula_check
 * @property string $hearing_check
 * @property string $feeding_patterns
 * @property string $feeding_num
 * @property string $substitutes
 * @property string $dietary_supplement
 * @property string $food_types
 * @property string $inspect_content
 * @property string $create_time
 * @property string $update_time
 */
class ChildExaminationInfo extends \app\common\base\BaseActiveRecord
{

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%child_examination_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'record_id'], 'required'],
            [['spot_id', 'record_id', 'create_time', 'update_time'], 'integer'],
            [['inspect_content'], 'string'],
            [['inspect_content'], 'string', 'max' => 1000],
            [['sleep', 'shit', 'pee', 'visula_check', 'hearing_check', 'feeding_patterns', 'feeding_num', 'substitutes', 'dietary_supplement'], 'string', 'max' => 100],
            [['sleep', 'shit', 'pee', 'visula_check', 'hearing_check', 'feeding_patterns', 'feeding_num', 'substitutes', 'dietary_supplement', 'food_types', 'inspect_content'], 'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '自增id',
            'spot_id' => '诊所ID',
            'record_id' => '就诊流水ID',
            'sleep' => '睡眠',
            'shit' => '大便',
            'pee' => '小便',
            'visula_check' => '视力检查',
            'hearing_check' => '听力筛查',
            'feeding_patterns' => '喂养方式',
            'feeding_num' => '每日母乳喂养次数(24h/次)',
            'substitutes' => '母乳代用品',
            'dietary_supplement' => '营养补充剂使用情况',
            'food_types' => '食物种类(1-谷类 2-蔬菜与水果 3-鱼禽肉食 4-烹调油)',
            'inspect_content' => '实验室检查',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    public static $getFoodType = [
        1 => '谷物',
        2 => '蔬菜与水果',
        3 => '鱼禽肉食',
        4 => '烹调油',
    ];

    public static function foodType($foodType) {
        $tmp = explode(',', $foodType);
        $typeArray=array_map(function ($v) {
            return self::$getFoodType[$v];
        },$tmp);
        return implode($typeArray, '、');
    }

}
