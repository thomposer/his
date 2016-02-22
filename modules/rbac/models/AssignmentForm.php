<?php

namespace app\modules\rbac\models;

use Yii;
use app\common\base\BaseModel;
use yii\base\Object;
use yii\db\Query;
/**
 * This is the model class for table "{{%auth_assignment}}".
 *
 * @property string $item_name
 * @property string $user_id
 * @property integer $created_at
 *
 * @property AuthItem $itemName
 */
class AssignmentForm extends \app\common\base\BaseActiveRecord
{
    public $item_name;
    public $user_id;
    public $created_at;
    public $roles;
    
    public static function tableName(){
        
        return '{{%auth_assignment}}';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','item_name'], 'required'],
            [['created_at'], 'integer'],
            [['user_id'], 'string', 'max' => 64],
            [['item_name','roles'],'safe']
           
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_name' => '角色列表',
            'user_id' => '用户名',
            'created_at' => 'Created At',
            'roles' => '角色列表'
            
        ];
    }
    /**
     * 
     * @param 角色名称 $role_name
     */
   public static function getUser_id($role_name,$limit = NULL) {
       
       return AssignmentForm::find()->select(['user_id'])->where(['item_name' => $role_name])->asArray()->orderBy(['created_at' => SORT_ASC])->limit($limit)->all();
   }
}
