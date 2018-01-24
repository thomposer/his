<?php

namespace app\modules\spot_set\models;

use Yii;
use yii\db\Query;
use app\modules\spot_set\models\RecipelistClinic;
use app\modules\spot\models\RecipeList;
use app\modules\spot\models\CureList;

/**
 * This is the model class for table "{{%outpatient_package_recipe}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $clinic_recipe_id
 * @property integer $outpatient_package_id
 * @property string $dose
 * @property integer $dose_unit
 * @property integer $used
 * @property integer $frequency
 * @property integer $day
 * @property integer $num
 * @property string $description
 * @property integer $type
 * @property integer $skin_test_status
 * @property string $skin_test
 * @property integer $outpatient_package_cure_id
 * @property integer $curelist_id
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property OutpatientPackageTemplate $outpatientPackage
 * @property RecipelistClinic $clinicRecipe
 */
class OutpatientPackageRecipe extends \app\common\base\BaseActiveRecord
{
    public $recipeName;
    public $name;
    public $dosage_form;
    public $deleted;
    public function init(){
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%outpatient_package_recipe}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id'], 'required'],
            [['recipeName','name','dosage_form','outpatient_package_cure_id','unit', 'used', 'frequency', 'dose', 'type', 'day', 'num', 'deleted', 'dose_unit', 'skin_test_status', 'curelist_id'],'safe'],
            [['skin_test_status','curelist_id','outpatient_package_cure_id'],'default','value' => 0],
            [['clinic_recipe_id'], 'validateInteger'],
            ['description', 'validateDescription'],
            
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '机构id',
            'clinic_recipe_id' => '诊所处方配置id',
            'outpatient_package_id' => '医嘱模板套餐公共信息表id',
            'dose' => '剂量',
            'dose_unit' => '剂量单位',
            'used' => '用法',
            'frequency' => '用药频次',
            'day' => '天数',
            'num' => '数量',
            'description' => '说明',
            'type' => '类型(1-本院,2-外购)',
            'skin_test_status' => '是否需要皮试(0-没，1-是,2-否)',
            'skin_test' => '皮试内容',
            'outpatient_package_cure_id' => '关联医嘱模板套餐--治疗表id(皮试)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'recipeName' => '选择处方医嘱',
            'name' => '处方名称',
            'dosage_form' => '剂型',
            'curelist_id' => '机构治疗配置表id(皮试)'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutpatientPackage()
    {
        return $this->hasOne(OutpatientPackageTemplate::className(), ['id' => 'outpatient_package_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClinicRecipe()
    {
        return $this->hasOne(RecipelistClinic::className(), ['id' => 'clinic_recipe_id']);
    }
    
    /*
     * 获取处方医嘱模板详情
     * @param $idArr 套餐id或id数组
     */
    static public function getrecipeList($idArr) {
        $recipeListQuery = new Query();
        $recipeList = $recipeListQuery->from(['a' => self::tableName()])
                ->leftJoin(['b' => RecipelistClinic::tableName()], '{{a}}.clinic_recipe_id = {{b}}.id')
                ->leftJoin(['c' => RecipeList::tableName()], '{{b}}.recipelist_id = {{c}}.id')
                ->select(['a.outpatient_package_id', 'recipe_id' => 'c.id', 'c.name', 'c.product_name',
                    'c.en_name',
                    'c.specification', 'b.price', 'a.dose', 'a.used', 'a.day', 'a.num', 'c.unit',
                    'c.unit', 'dosage_form' => 'c.type', 'a.frequency', 'type' => 'a.type',
                    'a.description', 'a.dose_unit', 'c.medicine_description_id', 'a.skin_test_status',
                    'c.skin_test', 'c.remark', 'c.tag_id', 'a.outpatient_package_cure_id', 'c.high_risk','c.drug_type'])
                ->where(['a.spot_id' => self::$staticSpotId, 'a.outpatient_package_id' => $idArr, 'b.status' => 1])
                ->all();
        return $recipeList;
    }
    
    public function validateInteger($attribute, $params) {
        $rows = [];
        $totalNum = [];
        $count = 0;
        if (count($this->clinic_recipe_id) > 0) {
            foreach ($this->clinic_recipe_id as $key => $v) {
               
                if ($this->deleted[$key] != 1) {
                    if (!preg_match("/^\s*[+-]?\d+\s*$/",$v)) {
                        $this->addError($attribute, '参数错误');
                    } else if ($this->dose[$key] < 0 || $this->dose[$key] > 1000) {
                        $this->addError($attribute, '剂量必须在0~1000范围内');
                    } else if ($this->dose_unit[$key] == '' || $this->dose_unit[$key] == 0) {
                        $this->addError($attribute, '剂量不能为空');
                    } else if (!preg_match("/^\s*[+-]?\d+\s*$/", $this->day[$key]) || !isset($this->day[$key])) {
                        $this->addError($attribute, '天数必须为一个数字');
                    }  else if ($this->day[$key] < 0 || $this->day[$key] > 100) {
                        $this->addError($attribute, '天数必须在0~100范围内');
                    } else if ($this->num[$key] <= 0 || $this->num[$key] > 100) {
                        $this->addError($attribute, '数量必须在1~100范围内');
                    } else if (!preg_match("/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/", $this->dose[$key]) || !isset($this->dose[$key])) {
                        $this->addError($attribute, '剂量必须为一个数字');
                    } else if (strlen(explode('.', $this->dose[$key])[1]) > 7) {
                        $this->addError($attribute, '剂量最多可输入7位小数');
                    }else if (!preg_match("/^\s*[+-]?\d+\s*$/", $this->num[$key]) || !isset($this->num[$key])) {
                        $this->addError($attribute, '数量必须为一个整数');
                    } else if ($this->used[$key] == 0) {
                        $this->addError($attribute, '用法不能为空');
                    } else if ($this->frequency[$key] == 0) {
                        $this->addError($attribute, '用药频次不能为空');
                    } else if ($this->scenario != 'makeup' && $this->skin_test_status[$key] == 1 && empty($this->curelist_id[$key])) {
                        $this->addError($attribute, '皮试类型不能为空');
                    }
                    $count += 1;
                }
            }
            if($count <= 0){
                $this->addError($attribute, '请选择处方医嘱');
            }
        }
    }
    
    public function validateDescription($attribute, $params) {
        if (count($this->description)) {
            if (is_array($this->description)) {
                foreach ($this->description as $key => $v) {
                    if ($this->deleted[$key] != 1) {
                        if (strlen($v) > 35) {
                            $this->addError('recipe_id', '描述说明不能超过35个字符');
                        }
                    }
                }
            }
        }
    }
    
    public static function saveInfo($templateId,$model,$isNewRecord = 1){
        
        $rows = [];
        $cureModel = '';
        if ($isNewRecord == 2){
            self::deleteAll(['outpatient_package_id' => $templateId,'spot_id' => self::$staticSpotId]);
        }
        if(count($model->clinic_recipe_id)){
            foreach ($model->clinic_recipe_id as $key => $v) {
                    if(!$cureModel){
                        $cureQuery = new Query();
                        $cureQuery->from(['a' => ClinicCure::tableName()]);
                        $cureQuery->select(['a.id']);
                        $cureQuery->leftJoin(['b' => CureList::tableName()],'{{a}}.cure_id = {{b}}.id');
                        $cureQuery->where(['a.spot_id' => self::$staticSpotId,'a.status' => 1,'b.type' => 1]);
                        $cureModel = $cureQuery->one();
                    }
                    if($model->skin_test_status[$key] == 1){
                        $packageCureModel = new OutpatientPackageCure();
                        $packageCureModel->scenario = 'normal';
                        $packageCureModel->cure_id = $cureModel['id'];
                        $packageCureModel->outpatient_package_id = $templateId;
                        $packageCureModel->description = '';
                        $packageCureModel->time = 1;
                        $packageCureModel->save();
                    }
                    $rows[] = [
                        self::$staticSpotId,
                        $v,
                        $templateId,
                        $model->dose[$key],
                        $model->dose_unit[$key],
                        $model->used[$key],
                        $model->frequency[$key],
                        $model->day[$key],
                        $model->num[$key],
                        $model->description[$key],
                        $model->type[$key],
                        $model->skin_test_status[$key]? $model->skin_test_status[$key] : 0,
                        $model->curelist_id[$key]?$model->curelist_id[$key] : 0,
                        $model->skin_test_status[$key] == 1?$packageCureModel->id : 0,
                        time(),
                        time()
                    ];
                    
            }
            if (count($rows) > 0) {
                Yii::$app->db->createCommand()->batchInsert(self::tableName(), [
                    'spot_id', 'clinic_recipe_id', 'outpatient_package_id', 'dose', 'dose_unit', 'used',
                    'frequency', 'day', 'num', 'description', 'type', 'skin_test_status', 'curelist_id',
                    'outpatient_package_cure_id','create_time', 'update_time'
                ], $rows)->execute();
            }
        }
    }

}
