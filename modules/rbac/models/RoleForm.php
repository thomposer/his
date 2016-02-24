<?php

namespace app\modules\rbac\models;

use Yii;
use app\modules\rbac\models\ItemForm;
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
class RoleForm extends ItemForm
{
    public function init() {
       parent::init();
       $this->type = \yii\rbac\Item::TYPE_ROLE;
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