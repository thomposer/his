<?php

namespace app\modules\growth\models;

use Yii;

/**
 * This is the model class for table "{{%growth_curve_head_circumference}}".
 *
 * @property integer $id
 * @property integer $age 月龄／天数
 * @property integer $age_type 年龄类型(1-天数，2-月龄)
 * @property string $lt
 * @property string $mt
 * @property string $st
 * @property string $th3
 * @property string $th15
 * @property string $th50
 * @property string $th85
 * @property string $th97
 * @property integer $sex
 * @property integer $create_time
 * @property integer $update_time
 */
class HeadCircumference extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%growth_curve_head_circumference}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['age','age_type', 'sex', 'create_time', 'update_time'], 'integer'],
            [['lt', 'mt', 'st', 'th3', 'th15', 'th50', 'th85', 'th97'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'age' => '月龄/天数',
            'age_type' => '年龄类型(1-天数，2-月龄)',
            'lt' => 'L值',
            'mt' => 'M值',
            'st' => 'S值',
            'th3' => '3th',
            'th15' => '15th',
            'th50' => '50th',
            'th85' => '85th',
            'th97' => '97th',
            'sex' => '性别 [1/男，2/女]',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
