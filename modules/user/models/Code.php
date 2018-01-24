<?php

namespace app\modules\user\models;

use Yii;

/**
 * This is the model class for table "{{%code}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $iphone
 * @property string $code
 * @property string $expire_time
 * @property string $spot_id
 * @property string $create_time
 * @property string $update_time
 */
class Code extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%code}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'iphone', 'code', 'expire_time', 'spot_id'], 'required'],
            [['user_id', 'iphone', 'code', 'expire_time', 'spot_id', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'user_id' => '用户id',
            'iphone' => '手机号码',
            'code' => '验证码',
            'expire_time' => '有效期',
            'spot_id' => '机构id',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
