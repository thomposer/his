<?php

namespace app\modules\behavior\models;

use Yii;

/**
 * This is the model class for table "{{%sms_record}}".
 *
 * @property integer $id
 * @property string $iphone
 * @property string $content
 * @property integer $status
 * @property integer $send_time
 * @property integer $create_time
 * @property integer $update_time
 */
class SmsRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sms_record}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('recordDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'send_time', 'create_time', 'update_time'], 'integer'],
            [['create_time', 'update_time'], 'required'],
            [['iphone'], 'string', 'max' => 11],
            [['content'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'iphone' => 'Iphone',
            'content' => 'Content',
            'status' => 'Status',
            'send_time' => 'Send Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
