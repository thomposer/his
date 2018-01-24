<?php

namespace app\modules\rbac\models;

use Yii;
use app\modules\rbac\models\ItemForm;
use yii\helpers\ArrayHelper;
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
class RoleForm extends Item
{
    public function init() {
       parent::init();
       $this->type = \yii\rbac\Item::TYPE_ROLE;
    }
    
    public function rules(){
        
        $parentRule = parent::rules();
        $roleRule =  [           
            [['child'],'required'],
            [['data','description'],'trim'],
            ['description','string', 'max' => 20],
            ['data','string','max' => '200'],
            ['description','validateDescription','on' => 'onlyRole']
        ];
        return ArrayHelper::merge($parentRule, $roleRule);
    }
    public function scenarios(){
        
        $parent = parent::scenarios();
        $parent['onlyRole'] = ['name','description','data','created_at','updated_at'];
        return $parent;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        
        return [
            'name' => '角色简称',
            'type' => '类型',
            'description' => '角色名称',
            'rule_name' => '权限规则',
            'data' => '描述',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'child' => '权限列表',
            
        ];
    }
    public function validateDescription($attribute){
        if ($this->isNewRecord) {
            
            if ($this->checkDuplicate($attribute, $this->$attribute)) {
                $this->addError($attribute, '该角色名称已存在');
            }
        } else {
            $oldDescription = $this->getOldAttribute($attribute);
            if ($oldDescription != $this->$attribute) {
                $hasRecord = $this->checkDuplicate($attribute, $this->$attribute);
                if ($hasRecord) {
                    $this->addError($attribute,'该角色名称已存在');
                }
            }
        }
    }
    protected function checkDuplicate($attribute, $params) {
        $parentSpotCode = Yii::$app->cache->get(Yii::getAlias('@parentSpotCode').$_COOKIE['spotId'].Yii::$app->user->identity->id);
        $hasRecord = RoleForm::find()->select(['name'])->where([$attribute => $this->$attribute])->andWhere(['like','name',$parentSpotCode.'_roles_%',false])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }
}
