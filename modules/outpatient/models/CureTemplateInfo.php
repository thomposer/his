<?php

namespace app\modules\outpatient\models;

use Yii;
use app\modules\spot_set\models\ClinicCure;
use app\modules\spot\models\CureList;
use yii\db\Query;

/**
 * This is the model class for table "{{%cure_template_info}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property integer $clinic_cure_id
 * @property string $cure_template_id
 * @property string $time
 * @property string $description
 * @property string $create_time
 * @property string $update_time
 *
 * @property CureTemplate $cureTemplate
 * @property CurelistClinic $clinicCure
 */
class CureTemplateInfo extends \app\common\base\BaseActiveRecord
{
    public $cureName;
    
    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cure_template_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'cure_template_id'], 'integer'],
            ['clinic_cure_id', 'validateCureId'],
            [['time','description'], 'safe'],
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
            'clinic_cure_id' => '诊所治疗医嘱id',
            'cure_template_id' => '治疗模版配置id',
            'time' => '次数',
            'description' => '说明',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'cureName' => '项目名称',
            'unit' => '单位',
        ];
    }
    
    /*
     * 验证字段
     */
    public function validateCureId($attribute, $params) {
        if (count($this->clinic_cure_id) > 0) {
            foreach ($this->clinic_cure_id as $key => $v) {
                if(!preg_match("/^\s*[+-]?\d+\s*$/", $this->clinic_cure_id[$key])){
                    $this->addError($attribute, '参数错误');
                }
                if(!preg_match("/^\s*[+-]?\d+\s*$/", $this->time[$key])){
                    $this->addError($attribute, '次数必须为整数');
                }
                if($this->time[$key] < 1 || $this->time[$key] > 100){
                    $this->addError($attribute, '次数必须在1~100范围内');
                }
            }
        }
    }
    
    
   /**
     * 
     * @param type $model CureTemplateModel
     * @param type $cureTemplateInfoModel
    *  @param int $type(1-新增 2-保存)
     * @return 插入cureTemplateInfoModel的关联信息
     */
   public static function saveInfo($model, $cureTemplateInfoModel,$type = 1) {
        $result = $model->getModel('cureTemplate')->save();
        if ($result) {
            $rows = [];
            foreach ($cureTemplateInfoModel->clinic_cure_id as $key => $v) {
                $rows[] = [
                    self::$staticSpotId,
                    $cureTemplateInfoModel->clinic_cure_id[$key],
                    $model->getModel('cureTemplate')->id,
                    $cureTemplateInfoModel->time[$key],
                    $cureTemplateInfoModel->description[$key],
                    time(),
                    time()
                ];
            }
            if(2 == $type){
                Yii::$app->db->createCommand()->delete(CureTemplateInfo::tableName(),['spot_id' => self::$staticSpotId, 'cure_template_id' => $model->getModel('cureTemplate')->id])->execute();
            }
            Yii::$app->db->createCommand()->batchInsert(CureTemplateInfo::tableName(), [
                'spot_id', 'clinic_cure_id', 'cure_template_id', 'time', 'description', 'create_time', 'update_time'
                    ], $rows)->execute();
        }
    }
    
    
    /*
     * 获取治疗医嘱模板详情
     */
    public static function findCureTemplateInfoDataProvider($id) {
        $data = (new Query())->from(['a' => CureTemplateInfo::tableName()])
                        ->select(['a.id', 'name' => 'c.name', 'unit' => 'c.unit', 'a.clinic_cure_id', 'a.cure_template_id', 'a.time', 'a.description'])
                        ->leftJoin(['b' => ClinicCure::tableName()], '{{a}}.clinic_cure_id={{b}}.id')
                        ->leftJoin(['c' => CureList::tableName()], '{{b}}.cure_id={{c}}.id')
                        ->where(['a.cure_template_id' => $id, 'a.spot_id' => self::$staticSpotId])
                        ->orderBy(['a.id' => SORT_ASC])->all();
        return $data;
    }

}
