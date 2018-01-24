<?php

namespace app\modules\growth\models;

use Yii;

/**
 * This is the model class for table "{{%growth_curve_zscore_head_circumference}}".
 *
 * @property integer $id
 * @property integer $age
 * @property string $lt
 * @property string $mt
 * @property string $st
 * @property string $sd3neg
 * @property string $sd2neg
 * @property string $sd1neg
 * @property string $sd0
 * @property string $sd1
 * @property string $sd2
 * @property string $sd3
 * @property integer $sex
 * @property integer $age_type
 * @property integer $create_time
 * @property integer $update_time
 */
class ZscoreHeadCircumference extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%growth_curve_zscore_head_circumference}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['age', 'sex', 'age_type', 'create_time', 'update_time'], 'integer'],
            [['lt', 'mt', 'st', 'sd3neg', 'sd2neg', 'sd1neg', 'sd0', 'sd1', 'sd2', 'sd3'], 'string', 'max' => 50],
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
            'lt' => 'L值',
            'mt' => 'M值',
            'st' => 'S值',
            'sd3neg' => 'sd3neg值',
            'sd2neg' => 'sd2neg值',
            'sd1neg' => 'sd1neg值',
            'sd0' => 'sd0值',
            'sd1' => 'sd1值',
            'sd2' => 'sd2值',
            'sd3' => 'sd3值',
            'sex' => '性别 [1/男，2/女]',
            'age_type' => '年龄类型（1/天数 2/月龄）',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
