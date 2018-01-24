<?php

namespace app\modules\spot\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%card_manage}}".
 *
 * @property string $f_physical_id
 * @property string $f_card_id
 * @property string $f_card_type_code
 * @property string $f_identifying_code
 * @property integer $f_status
 * @property string $f_card_desc
 * @property integer $f_is_issue
 * @property string $f_create_time
 * @property string $f_effective_time
 * @property string $f_activate_time
 * @property string $f_invalid_time
 */
class VirtualCardManage extends ActiveRecord
{
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
        return '{{%virtual_card_manage}}';
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
            [['f_card_type_code', 'f_status', 'f_is_issue'], 'integer'],
            [['f_card_id', 'f_identifying_code'], 'string', 'max' => 32],
            [['f_card_desc'], 'string', 'max' => 128],
            [['f_card_id'], 'unique'],
            [['f_identifying_code'], 'unique'],
            [['f_create_time'],'safe'],
            [['f_card_id','f_card_type_code','f_identifying_code','f_status','f_effective_time','f_activate_time','f_invalid_time'],'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'f_physical_id' => 'ID',
            'f_card_id' => '卡号',
            'f_card_type_code' => '卡类型码',
            'f_identifying_code' => '卡验证码',
            'f_status' => '状态',
            'f_card_desc' => '卡描述',
            'f_is_issue' => '分发',
            'f_create_time' => '创建时间',
            'f_effective_time' => '生效时间',
            'f_activate_time' => '激活时间',
            'f_invalid_time' => '失效时间',
        ];
    }

    public static $getStatus = [
        '0' => '未激活',
        '1' => '正常',
        '2' => '停用'
    ];

    public static $getIssue = [
        '0' => '未分发',
        '1' => '已分发'
    ];

    public static function getDateTime($time)
    {
        if (0 == $time) return "";

        return date("Y-m-d H:i:s", $time);
    }


}
