<?php

namespace app\modules\outpatient\models;

use Yii;
use yii\helpers\Json;
use yii\db\Exception;

/**
 * This is the model class for table "{{%recipe_template_info}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $recipe_id
 * @property integer $recipe_template_id
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
 * @property integer $curelist_id
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property RecipeTemplate $recipeTemplate
 */
class RecipeTemplateInfo extends \app\common\base\BaseActiveRecord
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
        return '{{%recipe_template_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id'], 'required'],
            [['spot_id', 'create_time', 'update_time'], 'integer'],
            [['recipe_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => RecipeTemplate::className(), 'targetAttribute' => ['recipe_template_id' => 'id']],
            [['recipeName','name','dosage_form','unit', 'used', 'frequency', 'dose', 'type', 'day', 'num', 'deleted', 'dose_unit', 'skin_test_status','skin_test', 'curelist_id'],'safe'],
            [['recipe_id'], 'validateInteger'],
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
            'recipe_id' => '处方id',
            'recipe_template_id' => '处方模版配置id',
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
            'curelist_id' => '关联治疗医嘱配置id(皮试)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            
            'recipeName' => '选择处方医嘱',
            'name' => '处方名称',
            'dosage_form' => '剂型'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipeTemplate()
    {
        return $this->hasOne(RecipeTemplate::className(), ['id' => 'recipe_template_id']);
    }
    
    
    public function validateInteger($attribute, $params) {
        $rows = [];
        $totalNum = [];
        $count = 0;
        if (count($this->recipe_id) > 0) {
            foreach ($this->recipe_id as $key => $v) {
                try {
                    $list = Json::decode($v);
                } catch (Exception $e) {
                    $this->addError($attribute, '参数错误');
                }
                if ($this->deleted[$key] != 1) {
                    if (!preg_match("/^\s*[+-]?\d+\s*$/", $list['id'])) {
                        $this->addError($attribute, '参数错误');
                    } else if ($this->dose[$key] < 0 || $this->dose[$key] > 1000) {
                        $this->addError($attribute, '剂量必须在0~1000范围内');
                    } else if ($this->dose_unit[$key] == '' || $this->dose_unit[$key] == 0) {
                        $this->addError($attribute, '剂量不能为空');
                    } else if ($this->day[$key] < 0 || $this->day[$key] > 100) {
                        $this->addError($attribute, '天数必须在0~100范围内');
                    } else if ($this->num[$key] <= 0 || $this->num[$key] > 100) {
                        $this->addError($attribute, '数量必须在1~100范围内');
                    } else if (!preg_match("/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/", $this->dose[$key]) || !isset($this->dose[$key])) {
                        $this->addError($attribute, '剂量必须为一个数字');
                    } else if (strlen(explode('.', $this->dose[$key])[1]) > 7) {
                        $this->addError($attribute, '剂量最多可输入7位小数');
                    } else if (!preg_match("/^\s*[+-]?\d+\s*$/", $this->day[$key]) || !isset($this->day[$key])) {
                        $this->addError($attribute, '天数必须为一个数字');
                    } else if (!preg_match("/^\s*[+-]?\d+\s*$/", $this->num[$key]) || !isset($this->num[$key])) {
                        $this->addError($attribute, '数量必须为一个整数');
                    } else if ($this->used[$key] == 0) {
                        $this->addError($attribute, '用法不能为空');
                    } else if ($this->frequency[$key] == 0) {
                        $this->addError($attribute, '用药频次不能为空');
                    } else if($this->scenario != 'makeup' && $this->skin_test_status[$key] === '0' ){
                        $this->addError($attribute, '皮试不能为空');
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
}
