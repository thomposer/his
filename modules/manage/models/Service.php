<?php

namespace app\modules\manage\models;

use Yii;

/**
 * This is the model class for table "service".
 *
 * @property integer $id
 * @property string $wxname
 * @property string $wxcode
 * @property string $maininfo
 * @property string $appid
 * @property string $appsecret
 * @property string $url
 * @property string $token
 * @property string $aeskey
 * @property string $remark
 */
class Service extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wxname', 'wxcode', 'maininfo', 'appid', 'appsecret', 'url', 'token', 'aeskey'], 'required'],
            [['url', 'remark'], 'string'],
            [['wxname', 'maininfo', 'appsecret', 'token', 'aeskey'], 'string', 'max' => 50],
            [['wxcode', 'appid'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wxname' => '公众号名称',
            'wxcode' => '微信号',
            'maininfo' => '主体信息',
            'appid' => 'AppID',
            'appsecret' => 'AppSecret',
            'url' => '服务器地址Url',
            'token' => '微信开发模式token',
            'aeskey' => '消息加解密密钥',
            'remark' => '备注',
        ];
    }
}
