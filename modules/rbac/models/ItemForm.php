<?php

namespace app\modules\rbac\models;

use Yii;
use app\common\base\BaseActiveRecord;
/**
 * This is the model class for table "{{%auth_item}}".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 */
class ItemForm extends BaseActiveRecord
{

    public $child;
    public $category;
    public $parentName;
//     public $isNewRecord = TRUE;
    
    public static function tableName(){
        
        return '{{%auth_item}}';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type','description','category'], 'required'],
            [['name', 'type','parentName','category','description'], 'trim'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data','category','parentName'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 50,'min' => 4, 'tooLong' => '请输入长度为4-50个字符','tooShort' => '请输入长度为4-50个字符'],
            [['child'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => '角色名称',
            'type' => '类型',
            'description' => '描述',
            'rule_name' => '权限规则',
            'data' => '内容',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'child' => '权限管理',
            
        ];
    }
    

}
