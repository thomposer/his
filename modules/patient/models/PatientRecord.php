<?php

namespace app\modules\patient\models;

use app\modules\make_appointment\models\Appointment;
use Yii;
use app\common\base\BaseActiveRecord;
use app\modules\spot_set\models\SpotType;
use yii\db\Query;
use app\modules\spot_set\models\UserAppointmentConfig;

/**
 * This is the model class for table "{{%patient_record}}".
 *
 * @property string $id
 * @property integer $spot_id
 * @property string $patient_id
 * @property integer $type
 * @property integer $status
 * @property decimal $price 诊疗费用
 * @property decimal $total_price 总费用
 * @property decimal $completed_price 已收费用
 * @property integer $type_time 预约类型时间
 * @property integer $child_check_status 儿童体检是否修改过记录(0-没修改，1-有修改)
 * @property integer $charge_type 收费种类(1-正常就诊收费，2-物资管理收费)
 * @property integer $delete_status 隐藏状态(1-正常，2-隐藏)
 * @property integer $is_package 是否开了医嘱模板套餐（1-是，2-否）
 * @property string $create_time
 * @property string $update_time
 */
class PatientRecord extends BaseActiveRecord
{

    public $doctor_id;
    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%patient_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['patient_id', 'spot_id', 'status'], 'required'],
            [['price'], 'required', 'on' => 'end', 'message' => '诊金金额不能为空'],
            [['price', 'completed_price', 'check_price', 'cure_price', 'inspect_price', 'recipe_price', 'income', 'change','record_price'], 'number'],
            [['case_id', 'fee_remarks', 'type_description', 'recipe_number'], 'string'],
            [['fee_remarks', 'type_description'], 'default', 'value' => ''],
            [['fee_remarks'], 'string', 'max' => 25, 'message' => '原因少于25个字'],
            [['completed_price', 'check_price', 'cure_price', 'inspect_price', 'recipe_price', 'income', 'change','record_price'], 'default', 'value' => '0.00'],
            [['patient_id', 'spot_id', 'status', 'type', 'create_time', 'update_time', 'end_time', 'makeup', 'type_time', 'child_check_status', 'charge_type', 'delete_status', 'is_package'], 'integer'],
            [['child_check_status', 'type_time'], 'default', 'value' => 0],
            [['charge_type', 'delete_status'], 'default', 'value' => 1],
            [['is_package'], 'default', 'value' => 2],
                /* [['type'],'validateType'] */
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'patient_id' => '患者ID',
            'type' => '预约服务',
            'type_description' => '预约服务', //这里以后应该会改成预约类型
            'status' => '就诊状态',
            'price' => '本次诊金费用为：',
            'check_price' => '影像学检查总费用',
            'cure_price' => '治疗总费用',
            'inspect_price' => '实验室检查总费用',
            'recipe_price' => '处方总费用',
            'completed_price' => '已收费用',
            'income' => '实收费用',
            'change' => '找零',
            'type_time' => '预约类型时间',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'child_check_status' => '门诊-儿童体检修改状态',
            'fee_remarks' => '诊金备注',
            'charge_type' => '收费种类',
            'delete_status' => '隐藏状态(1-正常，2-隐藏)'
        ];
    }

    /**
     * 
     * @var 就诊状态
     */
    public static $getStatus = [
        1 => '已预约',
        2 => '已报到',
        3 => '已分诊',
        4 => '接诊中',
        5 => '接诊结束',
//        6 => '已失约',
        7 => '已取消预约',
        8 => '已失约',
        9 => '报到后关闭'
//         8 => '已收费'
    ];
    public static $setStatus = [
        1 => 1, //已预约
        2 => 2, //已登记
        3 => 3, //已分诊
        4 => 4, //接诊中
        5 => 5, //接诊结束
        6 => 6, //失约
        7 => 7, //已取消
        8 => 8  //已收费
    ];

    /**
     *
     * @var 就诊类型
     */
    public static $getType = [
        1 => '初诊',
        2 => '复诊',
        3 => '小儿推拿',
        4 => '儿童保健'
    ];

    /**
     * @return 返回就诊类型
     * @property 临时方案
     */
    public static function getType() {
        //临时方案
        $spotType = SpotType::getSpotType();
        $type = self::$getType;
        unset($type[4]);
        if (!empty($spotType)) {
            $type[4] = $spotType[0]['name'];
        }
        return $type;
    }

    /**
     * @return   根据患者ID获取是否有补录数据
     */
    public static function getMakeup($patientId) {
        return self::find()->select(['patient_id', 'id'])->where(['patient_id' => $patientId, 'makeup' => 2])->asArray()->indexBy('patient_id')->all();
    }

    /**
     * @param $recordId 流水ID
     * @return  就诊流水信息
     */
    public static function getPatientRecord($recordId) {
        return self::find()->select(['patient_id', 'makeup', 'end_time', 'status', 'type'])->where(['id' => $recordId])->asArray()->one();
    }

    public function beforeSave($insert) {
        if ($insert) {
            //新建患者   则将唯一的打印患者号写入
            $this->case_id = $this->generateCaseId();
            $this->recipe_number = self::generateRecipeNumber();
            if($this->doctor_id && $this->type){
                //查询医生加服务类型关联的诊金
                $medicalFee = UserAppointmentConfig::getMedicalFee($this->doctor_id,$this->type);
                $this->price = $medicalFee['price'];
                $this->record_price = $medicalFee['price'];
            }
        }
        
        return parent::beforeSave($insert);
    }

    public function generateCaseId() {
        $sn = substr(time(), 2) . substr(microtime(), 2, 5) . sprintf('%03d', rand(0, 999));
        return $sn;
    }

    /**
     *
     * @param 接诊类型 $attribute
     * @param unknown $params
     */
    public function validateType($attribute, $params) {
        if (!$this->hasErrors()) {
            if ($this->isNewRecord) {
                $hasRecord = SpotType::getSpotType(['id' => $this->$attribute, 'status' => 1]);
                if (!$hasRecord) {
                    $this->addError($attribute, '接诊类型不存在');
                }
            } else {
                $oldDescription = $this->getOldAttribute($attribute);
                if ($oldDescription != $this->$attribute) {
                    $hasRecord = SpotType::getSpotType(['id' => $this->$attribute, 'status' => 1]);
                    if (!$hasRecord) {
                        $this->addError($attribute, '接诊类型不存在');
                    }
                }
            }
        }
    }

    /**
     * @param integer $patientId 患者ID
     * @param integer $recordId 是否过滤自身就诊记录，默认为null，不过滤
     * @return 获取是否为第一次就诊记录
     */
    public static function getFirstRecord($patientId, $recordId = null) {
        $query = self::find()->select(['patient_id', 'id'])->where(['patient_id' => $patientId]);
        if (!$recordId) {
            $record = $query->andWhere(['status' => 5])->asArray()->one();
            return ($record && $record != null) ? 2 : 1;
        } else {

            $count = $query->andWhere('id != :recordId', [':recordId' => $recordId])->count();
            return $count >= 1 ? 2 : 1;
        }
    }

    /**
     * @param $recordId 就诊流水id
     * @param $spotId 诊所id
     * @return array|bool 获取就诊流水状态，预约时间
     */
    public static function getAppointmentStatus($recordId) {
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['a.status', 'b.time']);
        $query->leftJoin(['b' => Appointment::tableName()], '{{a}}.id = {{b}}.record_id');
        $query->where(['a.id' => $recordId, 'b.spot_id' => self::$staticSpotId]);
        $data = $query->one();
        return $data;
    }

    /**
     * 
     * @param int/array $patientId 患者ID
     * @return 根据患者ID获取相应的  就诊总数 
     */
    public static function getRecordNum($patientId) {
        $query = new Query();
        $data = $query->from(['a' => PatientRecord::tableName()])
                        ->select(['num' => 'COUNT(1)', 'b.id'])
                        ->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id')
                        ->where(['b.id' => $patientId, 'a.status' => 5])->groupBy(['b.id'])->indexBy('id')->all();
        return $data;
    }

    /**
     * @desc 生成处方编号
     * @return String
     */
    public static function generateRecipeNumber() {
        $maxPatientNumber = self::find()->select(['recipe_number'])->where(['spot_id' => self::$staticSpotId])->orderBy(['recipe_number' => SORT_DESC])->asArray()->one();
        Yii::info('maxPatientNumber: ' . json_encode($maxPatientNumber));
        $sn = sprintf('%07d', ($maxPatientNumber['recipe_number'] ? (1 + $maxPatientNumber['recipe_number']) : 1));
        return $sn;
    }

}
