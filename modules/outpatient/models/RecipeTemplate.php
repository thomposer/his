<?php

namespace app\modules\outpatient\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%recipe_template}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property string $name
 * @property integer $recipe_type_template_id
 * @property integer $type
 * @property integer $user_id
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property RecipeTemplateInfo[] $recipeTemplateInfos
 */
class RecipeTemplate extends \app\common\base\BaseActiveRecord
{
    public $typeTemplateName;
    public $username;
    
    public function init(){
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%recipe_template}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id','name'], 'required'],
            [['spot_id', 'recipe_type_template_id', 'type', 'user_id', 'create_time', 'update_time'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['recipe_type_template_id'],'default','value' => 0],
            [['type'],'default','value' => 2],
            [['name'],'validateName'],
            [['typeTemplateName','username'],'safe'],
            [['name'],'trim']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'spot_id' => '机构id',
            'name' => '模板名称',

            'recipe_type_template_id' => '模板分类',
            'typeTemplateName' => '模板分类',
            'type' => '模板类型',
            'user_id' => '创建人',
            'username' => '创建人',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    public function validateName($attribute,$params){
        
        if(!$this->hasErrors()){
            if($this->isNewRecord){
                $hasRecord = $this->type == 2 ? $this->checkDuplicate() : $this->checkCommonName();
                if($hasRecord){
                    $this->addError('name',   '该模板名称已存在');
                }
            }else{
                $oldTemplateName = $this->getOldAttribute('name');
                if (trim($oldTemplateName) != trim($this->name)) {
                    $hasRecord = $this->type == 2 ? $this->checkDuplicate() : $this->checkCommonName();
                    if ($hasRecord) {
                        $this->addError('name',   '该模板名称已存在');
                    }
                }
            }
        }
    }
    
    protected function checkCommonName(){
        $hasRecord = self::find()->where(['spot_id' => $this->spot_id, 'name' => trim($this->name)])->count(1);
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }

    protected function checkDuplicate() {
        $query = new Query();
        $query->from(self::tableName());
        $query->orFilterWhere(['spot_id' => $this->spot_id,'type' => 1,'name' => trim($this->name)]);
        $query->orFilterWhere(['spot_id' => $this->spot_id,'user_id' => $this->userInfo->id    ,'name' => trim($this->name)]);
        $hasRecord = $query->count(1);
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipeTemplateInfos()
    {
        return $this->hasMany(RecipeTemplateInfo::className(), ['recipe_template_id' => 'id']);
    }
    
    /**
     * 
     * @var array 模板类型
     */
    public static $getType = [
        1 => '通用',
        2 => '个人'
    ];
    
    
    public function beforeSave($insert){
        if($this->isNewRecord){
            $this->user_id = $this->userInfo->id;
        }
        return parent::beforeSave($insert);
    }
}
