<?php

namespace app\modules\spot_set\models;

use Yii;
use yii\db\Query;
use app\modules\spot_set\models\CheckListClinic;
use app\modules\spot\models\CheckList;
/**
 * This is the model class for table "{{%outpatient_package_check}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $outpatient_package_id
 * @property integer $check_id
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property ChecklistClinic $check
 * @property OutpatientPackageTemplate $outpatientPackage
 */
class OutpatientPackageCheck extends \app\common\base\BaseActiveRecord
{
    public $checkName;
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
        return '{{%outpatient_package_check}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id'], 'required'],
            [['spot_id', 'outpatient_package_id', 'create_time', 'update_time'], 'integer'],
            [['check_id'],'validateCheckId'],
            [['deleted','checkName'],'safe'],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'spot_id' => '诊所id',
            'outpatient_package_id' => '医嘱模板套餐公共信息表id',
            'check_id' => '诊所的检查医嘱列表id',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'checkName' => '影像学检查',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheck()
    {
        return $this->hasOne(ChecklistClinic::className(), ['id' => 'check_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutpatientPackage()
    {
        return $this->hasOne(OutpatientPackageTemplate::className(), ['id' => 'outpatient_package_id']);
    }
    public function validateCheckId($attribute, $params) {
        if (count($this->check_id) > 0) {
            foreach ($this->check_id as $key => $v) {
                 
                if (!preg_match("/^\s*[+-]?\d+\s*$/",$v)) {
                    $this->addError($attribute, '参数错误');
                }
            }
        }
    }
    /*
     * 获取检查医嘱模板详情
     * @param $idArr 套餐id或id数组
     */
    static public function getCheckList($idArr) {
        $checkListQuery = new Query();
        $checkList = $checkListQuery->from(['a' => self::tableName()])
                ->leftJoin(['b' => CheckListClinic::tableName()], '{{a}}.check_id = {{b}}.id')
                ->leftJoin(['c' => CheckList::tableName()], '{{b}}.check_id = {{c}}.id')
                ->select(['a.check_id','a.outpatient_package_id', 'c.name', 'c.unit', 'b.price', 'c.tag_id'])
                ->where(['a.spot_id' => self::$staticSpotId, 'a.outpatient_package_id' => $idArr, 'b.status' => 1])
                ->all();
        return $checkList;
    }
    
    /**
     *
     * @param integer $templateId 模板套餐id
     * @param Object $model 保存信息
     * @param integer $isNewRecord 1-新增，2-编辑,默认为1
     * @return boolean 保存新增／编辑的检查医嘱信息
     */
    public static function saveInfo($templateId,$model,$isNewRecord = 1){
        $rows = [];
        if ($isNewRecord == 2){
            self::deleteAll(['outpatient_package_id' => $templateId,'spot_id' => self::$staticSpotId]);
        }
        if(count($model->check_id)){
            foreach ($model->check_id as $key => $v) {
                $rows[] = [
                    self::$staticSpotId,
                    $v,
                    $templateId,
                    time(),
                    time()
                ];
            }
            if (count($rows) > 0) {
                Yii::$app->db->createCommand()->batchInsert(self::tableName(), [
                    'spot_id', 'check_id', 'outpatient_package_id', 'create_time', 'update_time'
                ], $rows)->execute();
            }
        }
        return true;
    }
}
