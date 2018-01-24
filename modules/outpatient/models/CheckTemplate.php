<?php

namespace app\modules\outpatient\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\db\Query;

/**
 * This is the model class for table "{{%check_template}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $name
 * @property string $template_type_id
 * @property integer $type
 * @property string $user_id
 * @property string $create_time
 * @property string $update_time
 *
 * @property CheckTemplateInfo[] $checkTemplateInfos
 */
class CheckTemplate extends \app\common\base\BaseActiveRecord
{

    public $typeTemplateName;
    public $userName;

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%check_template}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'name'], 'required'],
            [['spot_id', 'template_type_id', 'type', 'user_id', 'create_time', 'update_time'], 'integer'],
            [['template_type_id'], 'default', 'value' => 0],
            [['name'], 'string', 'max' => 30],
            [['name'],'validateName'],
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
            'spot_id' => '诊所id',
            'name' => '模板名称',
            'template_type_id' => '模板分类',
            'type' => '模板类型',
            'user_id' => '创建人',
            'userName' => '创建人',
            'typeTemplateName' => '模板分类',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     *
     * @var array 模板类型
     */
    public static $getType = [
        1 => '通用',
        2 => '个人'
    ];

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->user_id = $this->userInfo->id;
        }
        return parent::beforeSave($insert);
    }

    /**
     *
     * @param type $id
     * @return type
     * @throws NotFoundHttpException
     */
    public static function findCheckTemplateModel($id) {
        if (($model = self::findOne(['id' => $id, 'spot_id' => self::$staticSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckTemplateInfos()
    {
        return $this->hasMany(CheckTemplateInfo::className(), ['check_template_id' => 'id']);
    }

    /**
     * @return 检查医嘱模板树形结构menu
     */
    public static function getCheckTemplateMenu() {
        $query = new Query();
        $query->from(['a' => CheckTemplate::tableName()]);
        $query->leftJoin(['b' => RecipeTypeTemplate::tableName()], '{{a}}.template_type_id = {{b}}.id');
        $query->select(['a.id', 'a.name', 'a.template_type_id', 'a.type', 'template_name' => 'IFNULL(b.name,"未分类")', 'type_spot_id' => 'b.spot_id']);
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.user_id' => Yii::$app->user->id]);
        $query->orderBy(['a.type' => SORT_DESC, 'a.template_type_id' => SORT_ASC, 'a.id' => SORT_DESC]);
        $result = $query->all();
        return $result;
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



}
