<?php

namespace app\modules\outpatient\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%dental_first_template}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $user_id
 * @property integer $type
 * @property string $name
 * @property string $chiefcomplaint
 * @property string $historypresent
 * @property string $pasthistory
 * @property string $oral_check
 * @property string $auxiliary_check
 * @property string $diagnosis
 * @property string $cure_plan
 * @property string $cure
 * @property string $advice
 * @property string $remark
 * @property integer $create_time
 * @property integer $update_time
 */
class DentalFirstTemplate extends \app\common\base\BaseActiveRecord
{
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
        return '{{%dental_first_template}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id','name'], 'required'],
            [['spot_id', 'create_time', 'update_time','type','user_id'], 'integer'],
            [['chiefcomplaint', 'historypresent', 'pasthistory', 'oral_check', 'auxiliary_check', 'diagnosis', 'cure_plan', 'cure', 'advice', 'remark'], 'string','max' => 1000],
            [['chiefcomplaint', 'historypresent', 'pasthistory', 'oral_check', 'auxiliary_check', 'diagnosis', 'cure_plan', 'cure', 'advice', 'remark'], 'default','value' => ''],
            [['name'], 'string', 'max' => 64],
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
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'name' => '模板名称',
            'chiefcomplaint' => '主诉',
            'historypresent' => '现病史',
            'pasthistory' => '既往病史',
            'oral_check' => '口腔检查',
            'auxiliary_check' => '辅助检查',
            'diagnosis' => '诊断',
            'cure_plan' => '治疗方案',
            'cure' => '治疗',
            'advice' => '医嘱',
            'remark' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'username' => '创建人',
            'type' => '类型'
        ];
    }
    
    public static $getType = [
        1 => '通用',
        2 => '个人'
    ];
    
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
        $query->orFilterWhere(['spot_id' => $this->spotId,'type' => 1,'name' => trim($this->name)]);
        $query->orFilterWhere(['spot_id' => $this->spotId,'user_id' => $this->userInfo->id,'name' => trim($this->name)]);
        $hasRecord = $query->count(1);
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * 获取口腔初诊模版id列表
     */
    public static function templateList() {
        $userId = Yii::$app->user->identity->id;
        $list = self::find()->select(['id', 'name', 'user_id', 'type'])->where(['spot_id' => self::$staticSpotId, 'user_id' => $userId])->orderBy('id desc')->asArray()->all();
        $i = 0;
        //type 1通用 2个人
        $data = [];
        if (!empty($list)) {
            foreach ($list as &$val) {
                if ($val['type'] == 1) {
                    $val['group'] = '通用模板';
                    $data[] = $val;
                } else{
                    $val['group'] = '我的模板';
                    if ($i == 0) {
                        array_unshift($data, $val);
                    } else {
                        $data[] = $val;
                    }
                    $i++;
                }
            }
        }
        return $data;
    }
    
    /**
     * 
     * @param type $model
     * @param 病例ID $case_id
     * @return 根据病例模板Id设置   当前病例信息
     */
    public static function setModel($model, $case_id, &$dentalHistoryRelation) {
        $dentalHistoryRelation = [];
        $caseInfo = self::find()->where(['id' => $case_id, 'spot_id' => self::$staticSpotId])->asArray()->one();
        if (!$caseInfo) {
            return $model;
        }
        $model->chiefcomplaint = $caseInfo['chiefcomplaint'];
        $model->historypresent = $caseInfo['historypresent'];
        $model->pasthistory = $caseInfo['pasthistory'];
        $model->advice = $caseInfo['advice'];
        $model->remarks = $caseInfo['remark'];
        $dentalHistoryRelation[1][] = ['position' => '', 'content' => $caseInfo['oral_check']];
        $dentalHistoryRelation[2][] = ['position' => '', 'content' => $caseInfo['auxiliary_check']];
        $dentalHistoryRelation[3][] = ['position' => '', 'content' => $caseInfo['diagnosis']];
        $dentalHistoryRelation[4][] = ['position' => '', 'content' => $caseInfo['cure_plan']];
        $dentalHistoryRelation[5][] = ['position' => '', 'content' => $caseInfo['cure']];
        return $model;
    }
    
    public function beforeSave($insert){
        if ($this->isNewRecord) {
            $this->user_id = $this->userInfo->id;
        }
        return parent::beforeSave($insert);
    }
}
