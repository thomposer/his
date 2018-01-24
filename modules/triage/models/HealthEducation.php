<?php

namespace app\modules\triage\models;

use Yii;

/**
 * This is the model class for table "{{%health_education}}".
 *
 * @property string $id
 * @property string $record_id
 * @property string $spot_id
 * @property string $education_content
 * @property integer $education_object
 * @property integer $education_method
 * @property integer $accept_barrier
 * @property integer $accept_ability
 * @property string $create_time
 * @property string $update_time
 */
class HealthEducation extends \app\common\base\BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%health_education}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['record_id', 'spot_id', 'education_object', 'education_method', 'accept_barrier', 'accept_ability'], 'integer'],
            [['education_content'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'record_id' => '流水ID',
            'spot_id' => '诊所ID',
            'education_content' => '宣教内容',
            'education_object' => '宣教对象',
            'education_method' => '宣教方式',
            'accept_barrier' => '接受障碍',
            'accept_ability' => '接受能力',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * 宣教对象
     */
    public static $getEducationObject = [
        1 => '患者本人',
        2 => '法定监护人',
        3 => '其他亲属'
    ];

    /**
     * 宣教方式
     */
    public static $getEducationMethod = [
        1 => '口头指导',
        2 => '示范',
        3 => '视听教材',
        4 => '授课',
        5 => '药品信息单',
        6 => '电话',
        7 => '邮寄资料/邮件'
    ];

    /**
     * 接受障碍
     */
    public static $getAcceptBarrier = [
        1 => '无',
        2 => '认知能力受限',
        3 => '情绪',
        4 => '经济能力',
        5 => '语言',
        6 => '学习动机/期望',
        7 => '身体情况',
        8 => '宗教/文化',
        9 => '视力障碍',
        10 => '听力障碍'
    ];

    /**
     * 接受能力
     */
    public static $getAcceptAbility = [
        1 => '能重复信息或演示示范动作',
        2 => '需要进一步指导',
        3 => '不能记清内容或不能示范',
        4 => '不具备学习能力'
    ];

    /**
     * @param $id 流水id
     * @return 健康教育记录
     */
    public static function getHealthEducationRecord($id) {
        $healthRecord = HealthEducation::find()->select(['education_content', 'education_object', 'education_method', 'accept_barrier', 'accept_ability', 'record_id'])->where(['record_id' => $id])->asArray()->all();
        $data = [];
        if (!empty($id) && is_array($id)) {
            foreach ($id as $val) {
                foreach ($healthRecord as $v) {
                    if ($val == $v['record_id']) {
                        $data[$v['record_id']][] = $v;
                    }
                }
            }
        }
        return $data;
    }

}
