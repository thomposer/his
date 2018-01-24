<?php

namespace app\modules\spot_set\models;

use Yii;
use yii\db\Query;
use app\modules\spot_set\models\ClinicCure;
use app\modules\spot\models\CureList;

/**
 * This is the model class for table "{{%outpatient_package_cure}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $outpatient_package_id
 * @property integer $cure_id
 * @property integer $time
 * @property string $description
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property CurelistClinic $cure
 * @property OutpatientPackageTemplate $outpatientPackage
 */
class OutpatientPackageCure extends \app\common\base\BaseActiveRecord
{
    public $cureName;
    public $name;
    public $unit;
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
        return '{{%outpatient_package_cure}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id'], 'required'],
            [['spot_id', 'outpatient_package_id', 'create_time', 'update_time'], 'integer'],
            [['cure_id','time','description'], 'validateCureId','on' => 'create'],
            [['cure_id'],'integer','on' => 'normal'],
            [['time'],'integer','min' => 1,'max' => 100,'on' => 'normal'],
            [['cureName','deleted'],'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'name' => '项目名称',
            'unit' => '单位',
            'outpatient_package_id' => '医嘱模板套餐公共信息表id',
            'cure_id' => '诊所的治疗医嘱列表id',
            'time' => '次数',
            'description' => '说明',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'cureName' => '新增治疗',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCure()
    {
        return $this->hasOne(ClinicCure::className(), ['id' => 'cure_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutpatientPackage()
    {
        return $this->hasOne(OutpatientPackageTemplate::className(), ['id' => 'outpatient_package_id']);
    }
    public function validateCureId($attribute, $params) {
        if (count($this->cure_id) > 0) {
            foreach ($this->cure_id as $key => $v) {
                if(!preg_match("/^\s*[+-]?\d+\s*$/", $this->cure_id[$key])){
                    $this->addError('cure_id', '参数错误');
                }
                if(!preg_match("/^\s*[+-]?\d+\s*$/", $this->time[$key])){
                    $this->addError('cure_id', '次数必须为整数');
                }
                if($this->time[$key] < 1 || $this->time[$key] > 100){
                    $this->addError('cure_id', '次数必须在1~100范围内');
                }
            }
        }
    }
    /*
     * 获取治疗医嘱模板详情
     * @param $idArr 套餐id或id数组
     */
    static public function getCureList($idArr) {
        $cureListQuery = new Query();
        $cureList = $cureListQuery->from(['a' => self::tableName()])
                ->leftJoin(['b' => ClinicCure::tableName()], '{{a}}.cure_id = {{b}}.id')
                ->leftJoin(['c' => CureList::tableName()], '{{b}}.cure_id = {{c}}.id')
                ->select(['a.id', 'a.outpatient_package_id', 'c.name', 'c.unit', 'b.price', 'a.time', 'a.description', 'c.tag_id', 'curelistId' => 'c.id', 'c.type'])
                ->where(['a.spot_id' => self::$staticSpotId, 'a.outpatient_package_id' => $idArr, 'b.status' => 1])
                ->indexBy('id')
                ->all();
        return $cureList;
    }
    
    /**
     *
     * @param integer $templateId 模板套餐id
     * @param Object $model 保存信息
     * @param integer $isNewRecord 1-新增，2-编辑,默认为1
     * @return boolean 保存新增／编辑的治疗医嘱信息
     */
    public static function saveInfo($templateId,$model,$isNewRecord = 1){
        $rows = [];
        Yii::info($isNewRecord,'isNewRecord');
        if ($isNewRecord == 2){
            self::deleteAll(['outpatient_package_id' => $templateId,'spot_id' => self::$staticSpotId]);
        }
        if(count($model->cure_id)){
            foreach ($model->cure_id as $key => $v) {
                $rows[] = [
                    self::$staticSpotId,
                    $v,
                    $templateId,
                    $model->time[$key],
                    $model->description[$key],
                    time(),
                    time()
                ];
            }
            if (count($rows) > 0) {
                Yii::$app->db->createCommand()->batchInsert(self::tableName(), [
                    'spot_id', 'cure_id', 'outpatient_package_id','time','description', 'create_time', 'update_time'
                ], $rows)->execute();
            }
        }
        return true;
    }
}
