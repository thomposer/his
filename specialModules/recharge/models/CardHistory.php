<?php

namespace app\specialModules\recharge\models;

use Yii;

/**
 * This is the model class for table "{{%card_history}}".
 *
 * @property string $f_physical_id
 * @property string $f_record_id
 * @property string $f_update_beg
 * @property string $f_update_end
 * @property string $f_user_id
 * @property string $f_user_name
 * @property integer $f_state
 * @property string $f_property
 * @property string $f_create_time
 * @property string $f_update_time
 */
class CardHistory extends \yii\db\ActiveRecord
{
    
    public $payType;
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%card_history}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('cardCenter');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['f_record_id', 'f_state', 'f_property','f_user_id'], 'integer'],
            [['f_create_time', 'f_update_time'], 'safe'],
            [['f_update_beg', 'f_update_end'], 'string', 'max' => 1024],
            [['f_user_name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'f_physical_id' => '序号',
            'f_record_id' => '操作数据ID',
            'f_update_beg' => '修改前金额d',
            'f_update_end' => '修改后金额',
            'f_user_id' => '后台操作用户ID',
            'f_user_name' => '后台操作用户名',
            'f_state' => '状态',
            'f_property' => '预留',
            'f_create_time' => '时间',
            'payType' => '支付方式',
            'f_update_time' => 'F Update Time',
        ];
    }
}
