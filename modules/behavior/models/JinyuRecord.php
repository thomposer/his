<?php

namespace app\modules\behavior\models;

use Yii;

/**
 * This is the model class for table "gzh_jinyu_record".
 *
 * @property string $id
 * @property string $ip
 * @property string $spot_id
 * @property string $type
 * @property string $data
 * @property string $operation_time
 */
class JinyuRecord extends \yii\db\ActiveRecord
{

    private static $database;
    const getLisRequest = 1;//获取标本
    const affirmRequest = 2;//确认信息
    const uploadLisRepData = 3;//回填结果

    public function init() {
        parent::init();
        self::resetDataBase();
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }

    private static function resetDataBase() {
        $hostInfo = Yii::$app->request->hostInfo;
        if (strpos($hostInfo, 'beta')) {
            self::$database = 'd_easyhin_his_beta_record';
        } else {
            self::$database = 'd_easyhin_his_record';
        }
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('recordDb');
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        if (self::$database) {
            return self::$database . '.{{%jinyu_record}}';
        } else {
            return '{{%jinyu_record}}';
        }
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['operation_time'], 'required'],
            [['spot_id', 'operation_time','type'], 'integer'],
            [['ip'], 'string', 'max' => 15],
            [['data'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'ip' => '访问用户的IP地址',
            'spot_id' => '机构/诊所id',
            'type' => '区分接口',
            'data' => '金域传递的参数',
            'operation_time' => '操作时间',
        ];
    }

    public static function log($ip, $spot, $type = 1, $data = '') {
        $record = new static();
        $record->ip = $ip;
        $record->spot_id = $spot;
        $record->type = $type;
        $record->data = $data;
        $record->operation_time = time();
        return $record->save();
    }

}
