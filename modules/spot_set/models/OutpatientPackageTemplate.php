<?php

namespace app\modules\spot_set\models;

use Yii;
use app\modules\spot_set\models\OutpatientPackageCheck;
use app\modules\spot_set\models\OutpatientPackageInspect;
use app\modules\spot_set\models\OutpatientPackageCure;
use app\modules\spot_set\models\OutpatientPackageRecipe;
use yii\db\Query;
use yii\helpers\Html;
use app\modules\spot_set\models\MedicalFeeClinic;
use app\modules\spot\models\MedicalFee;
use app\modules\spot\models\RecipeList;

/**
 * This is the model class for table "{{%outpatient_package_template}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property string $name
 * @property integer $type
 * @property string $price
 * @property integer $medical_fee_clinic_id
 * @property integer $user_id
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property OutpatientPackageCheck[] $outpatientPackageChecks
 * @property OutpatientPackageCure[] $outpatientPackageCures
 * @property OutpatientPackageInspect[] $outpatientPackageInspects
 * @property OutpatientPackageRecipe[] $outpatientPackageRecipes
 */
class OutpatientPackageTemplate extends \app\common\base\BaseActiveRecord
{

    public $userName;
    
    public function init(){
        
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%outpatient_package_template}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id','medical_fee_price','price','name','type'], 'required'],
            [['spot_id', 'type', 'user_id', 'create_time', 'update_time'], 'integer'],
            [['price'], 'number','min' => 0,'max' => 100000],
            [['medical_fee_price'], 'number','min' => 0,'max' => 100000],
            [['name'], 'string', 'max' => 64],
            [['name'],'validateName'],
            [['price','medical_fee_price'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
        ];
    }
    public static  $getType=[
        1=>'套餐',
        2=>'模板',
];



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'name' => '模板名称',
            'type' => '模板类型',
            'price' => '价格',
            'medical_fee_price' => '诊金',
            'user_id' => '创建人',
            'userName'=>'创建人',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutpatientPackageChecks()
    {
        return $this->hasMany(OutpatientPackageCheck::className(), ['outpatient_package_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutpatientPackageCures()
    {
        return $this->hasMany(OutpatientPackageCure::className(), ['outpatient_package_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutpatientPackageInspects()
    {
        return $this->hasMany(OutpatientPackageInspect::className(), ['outpatient_package_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutpatientPackageRecipes()
    {
        return $this->hasMany(OutpatientPackageRecipe::className(), ['outpatient_package_id' => 'id']);
    }
    
    /*
     * 获取套餐列表
     */
    static public function getPackageList($fields = ['id','name'], $where = '1 != 0') {
        return self::find()->select($fields)->where(['spot_id' => self::$staticSpotId])->andWhere($where)->asArray()->indexBy('id')->all();
    }
    
    
     /*
     * 获取套餐列表
     */
    static public function getPackageInfo($where = '1 != 0') {
        $query = new Query();
        return $query->from(['a' => self::tableName()])
//                ->leftJoin(['b' => MedicalFeeClinic::tableName()], '{{a}}.medical_fee_clinic_id={{b}}.id')
//                ->leftJoin(['c' => MedicalFee::tableName()], '{{a}}.medical_fee_id={{c}}.id')
                ->select(['a.id', 'a.medical_fee_price', 'packageName' => 'a.name', 'packagePrice' => 'a.price'])
                ->where(['a.spot_id' => self::$staticSpotId])
                ->andFilterWhere($where)
                ->all();
    }
    public function validateName($attribute) {
        $spotId = $this->spotId;
        if ($this->isNewRecord) {
            $hasRecord = self::find()->select(['id'])->where(['spot_id' => $spotId, $attribute => $this->$attribute])->asArray()->limit(1)->one();
            if ($hasRecord) {
                $this->addError($attribute, '该模板名称已存在');
            }
        } else {
            $oldPrice = $this->getOldAttribute('name');
            if ($oldPrice != $this->name) {
                $hasRecord = $this->checkDuplicate('name', $this->name);
                if ($hasRecord) {
                    $this->addError('name', '该模板名称已存在');
                }
            }
        }
    }
    
    protected function checkDuplicate($attribute, $params) {
        $hasRecord = self::find()->select(['id'])->where(['spot_id' => $this->spotId, $attribute => $params])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }
    /*
     * 获取套餐详情（四大医嘱列表）
     * @param $idArr
     */
    static public function getPackageDetail($idArr) {
        $data = [];
        
        $feeList = self::getPackageInfo(['a.id' => $idArr]);
        foreach ($feeList as $value) {
            $data[$value['id']]['feeRemarks'] = $value['remarks'] ? Html::encode($value['remarks']) : '';
        }
        
        $checkList = OutpatientPackageCheck::getCheckList($idArr);
        foreach ($checkList as $value) {
            $data[$value['outpatient_package_id']]['checkList'][] = Html::encode($value['name']);
        }
        
        $inspectList = OutpatientPackageInspect::getInspectList($idArr);
        foreach ($inspectList as $value) {
            $data[$value['outpatient_package_id']]['inspectList'][] = Html::encode($value['name']);
        }
        
        $cureList = OutpatientPackageCure::getCureList($idArr);
        foreach ($cureList as $value) {
            $data[$value['outpatient_package_id']]['cureList'][] = Html::encode("{$value['name']}{$value['time']}".($value['unit'] ? "{$value['unit']}" : ''));
        }
        
        $recipeList = OutpatientPackageRecipe::getrecipeList($idArr);
        foreach ($recipeList as $value) {
            $unit = RecipeList::$getUnit[$value['unit']];
            $data[$value['outpatient_package_id']]['recipeList'][] = Html::encode("{$value['name']}（{$value['specification']}）{$value['num']}{$unit}");
        }
        return $data;
    }

    public function beforeSave($insert){
        
        if($this->isNewRecord){
            $this->user_id = $this->userInfo->id;
        }
        
        
       return parent::beforeSave($insert);
    }
}
