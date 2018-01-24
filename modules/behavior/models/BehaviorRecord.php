<?php

namespace app\modules\behavior\models;

use Yii;

/**
 * This is the model class for table "{{%behavior_record}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $ip
 * @property string $spot_id
 * @property string $module
 * @property string $action
 * @property string $data
 * @property string $operation_time
 * @property string $username 用户名
 */
class BehaviorRecord extends \yii\db\ActiveRecord
{
    public $username;
    public $spot_name;
    private static $database;
    public function init(){
        parent::init();
        self::resetDataBase();
    }
//     public function behaviors()
//     {
//         return [
//             'bedezign\yii2\audit\AuditTrailBehavior'
//         ];
//     }

    private static function resetDataBase(){
        $hostInfo = Yii::$app->request->hostInfo;
        if (strpos($hostInfo, 'beta')){
            self::$database = 'd_easyhin_his_beta_record';
        }else{
            self::$database = 'd_easyhin_his_record';
        }
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
    public static function tableName()
    {
        if(self::$database){
            return self::$database.'.{{%behavior_record}}';
        }else{
            return '{{%behavior_record}}';
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'ip', 'spot_id', 'module', 'action', 'operation_time'], 'required'],
            [['user_id','operation_time','spot_id'],'integer'],
            [['operation_time'], 'safe'],
            [['module'], 'string', 'max' => 64],
            [['ip'], 'string', 'max' => 15],
            [['action'], 'string', 'max' => 255],
            [['data'], 'string', 'max' => 2048],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'ip' => '访问用户的IP地址',
            'spot_id' => '站点id',
            'spot_name' => '站点名称',
            'module' => '模块名称',
            'action' => '操作的url',
            'data' => '调用action时传递的参数',
            'operation_time' => '操作时间',
            'username' => '用户名'
        ];
    }
    
    public static function log($userId, $ip, $spot, $module, $action, $data = null)
    {
    	$record = new static();
    	$record->user_id = $userId;
    	$record->ip = $ip;
    	$record->spot_id = $spot;
    	$record->module = $module;
    	$record->action = $action;
    	$record->data = $data;
    	$record->operation_time = time();
    	return $record->save();
    }
}
