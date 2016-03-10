<?php

namespace app\modules\behavior\models;

use Yii;

/**
 * This is the model class for table "{{%behavior_record}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $ip
 * @property string $spot
 * @property string $module
 * @property string $action
 * @property string $data
 * @property string $operation_time
 */
class BehaviorRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%behavior_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'ip', 'spot', 'module', 'action', 'operation_time'], 'required'],
            [['operation_time'], 'safe'],
            [['user_id', 'module'], 'string', 'max' => 64],
            [['ip'], 'string', 'max' => 15],
            [['spot'], 'string', 'max' => 20],
            [['action'], 'string', 'max' => 255],
            [['data'], 'string', 'max' => 2048]
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
            'spot' => '站点简称',
            'module' => '模块名称',
            'action' => '操作的url',
            'data' => '调用action时传递的参数',
            'operation_time' => '操作时间',
        ];
    }
    
    public static function log($userId, $ip, $spot, $module, $action, $data = null)
    {
    	$record = new static();
    	$record->user_id = $userId;
    	$record->ip = $ip;
    	$record->spot = $spot;
    	$record->module = $module;
    	$record->action = $action;
    	$record->data = $data;
    	$record->operation_time = date('Y-m-j H:i:s');
    	return $record->save();
    }
}
