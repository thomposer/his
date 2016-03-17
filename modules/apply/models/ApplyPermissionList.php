<?php

namespace app\modules\apply\models;

use Yii;
use app\modules\rbac\models\ItemForm;
use app\modules\wxinfo\models\Wxinfo;

/**
 * This is the model class for table "{{%apply_permission_list}}".
 *
 * @property string $id
 * @property string $user_id 用户简称
 * @property string $username 用户名
 * @property string $spot 站点缩写
 * @property string $spot_name 站点名称
 * @property string $item_name 角色简称
 * @property integer $status 状态
 * @property string $reason 审批理由
 * @property string $apply_persons 审批人
 * @property string $item_name_description 角色名称
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 */
class ApplyPermissionList extends \app\common\base\BaseActiveRecord
{
	const VERIFYING = 0;
	const VERIFIED = 1;
	const FREEZE = 2;
	
    public $item_data;
    public static $apply_status = array(
        0 => '待审核',
        1 => '已通过',
        2 => '已冻结'
    );
    public static $color = array(
        '0' => 'text-muted',
        '1' => 'text-success',
        '2' => 'text-danger'
    );
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%apply_permission_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','reason','item_data','status'], 'required','on' => 'create'],
            [['user_id','reason','item_data','status'],'required','on' => 'update'],
            [[ 'reason', 'item_name','item_data','item_name_description', 'spot','spot_name','username','apply_persons'], 'trim'],
            [['status', 'create_time', 'update_time'], 'integer'],
            [['reason','item_name_description','spot_name','user_id','username','apply_persons'], 'string'],
            [['spot','user_id','username'], 'string', 'max' => 64]
           
        ];
    }
   

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'user_id' => '申请人',
            'username' => '申请人',
            'spot' => '站点名称',
            'spot_name' => '站点名称',
            'item_name' => '角色名称',
            'item_name_description'=>'角色名称',
            'status' => '申请状态',
            'reason' => '审批理由',
            'item_data' => '角色名称',
            'apply_persons' => '当前处理人',
            'create_time' => '申请时间',
            'update_time' => '审批时间',
            
        ];
    }
    /**
     * 
     * @param array $where -- where条件
     * @param array $field -- select字段 
     */
    public static function getSpot($where = NULL,$field = NULL){
       
        return ApplyPermissionList::find()->select($field)->where($where)->asArray()->all();
    }
    public function getAuthItem(){
        return $this->hasOne(ItemForm::className(), ['name' =>'item_name']);
    }
}
