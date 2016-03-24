<?php

namespace app\modules\make_appointment\models;

use Yii;

/**
 * This is the model class for table "{{%patient}}".
 * 患者用户表
 * @property integer $id
 * @property string $user_name
 * @property integer $sex
 * @property string $birthday
 * @property string $nation
 * @property integer $marriage
 * @property string $occupation
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $detail_address
 * @property string $address 省/市/区
 */
class Patient extends \app\common\base\BaseActiveRecord
{
    public $address;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%patient}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sex'], 'required'],
            [['sex', 'marriage'], 'integer'],
            [['birthday'], 'safe'],
            [['user_name','address', 'occupation', 'detail_address'], 'string', 'max' => 64],
            [['nation', 'province', 'city', 'area'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => '姓名',
            'sex' => '性别',
            'birthday' => '出生日期',
            'nation' => '民族',
            'marriage' => '婚姻状况',
            'occupation' => '职业',
            'address' => '省/城市/区',
            'city' => '城市',
            'area' => '区县',
            'detail_address' => '详细地址',
        ];
    }
    /**
     * @property 婚姻状况(1-未婚,2-已婚)
     * @var unknown
     */
    public static $marriage = [
        1 => '未婚',
        2 => '已婚'
    ];
}
