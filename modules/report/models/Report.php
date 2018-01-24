<?php

namespace app\modules\report\models;

use app\modules\make_appointment\models\Appointment;
use Yii;
use app\common\base\BaseActiveRecord;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\spot_set\models\SpotType;
use app\modules\spot\models\OrganizationType;
use app\modules\user\models\User;
use app\modules\patient\models\PatientRecord;

/**
 * This is the model class for table "{{%report}}".
 *
 * @property string $id
 * @property string $patient_id
 * @property string $record_id
 * @property integer $spot_id
 * @property string $create_time
 * @property string $update_time
 * @property string $username 患者名称
 * @property integer $status 就诊状态
 * @property integer $once_department_id 一级科室id
 * @property integer $second_department_id 二级科室id
 * @property integer $doctor_id 医生id
 * @property integer $type 就诊服务id
 * @property string $type_description 就诊服务名称
 * @property string $doctorName 医生名称
 */
class Report extends BaseActiveRecord
{

    public $username;
    public $status;
    public $remarks;
    public $sex;
    public $birthday;
    public $iphone;
    public $patient_number;
    public $doctorName; //医生名称
    public $firstRecord;

    public static $orthodonticsFirst = 6;//正畸初诊
    public static $orthodonticsReturnvisit = 7;//正畸复诊
                
    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%report}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'second_department_id', 'doctor_id', 'type'], 'required'],
            [['patient_id', 'spot_id', 'record_id', 'type', 'create_time', 'update_time', 'once_department_id', 'second_department_id', 'doctor_id', 'type', 'is_vip', 'record_type'], 'integer'],
            [['type_description'], 'string', 'max' => 255],
            [['type_description'], 'default', 'value' => ''],
            [['is_vip'], 'default', 'value' => 0],
            [['doctorName'], 'safe'],
            ['type','validateType','on'=>'report'],
            ['type','validateType','on'=>'choose-doctor'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'patient_id' => '患者ID',
            'record_id' => '病案号',
            'spot_id' => '诊所id',
            'username' => '患者信息',
            'status' => '就诊状态',
            'remarks' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'iphone' => '手机号',
            'birthday' => '出生日期',
            'once_department_id' => '一级科室',
            'second_department_id' => '科室',
            'doctor_id' => '接诊医生',
            'type' => '服务类型',
            'type_description' => '服务类型',
            'is_vip' => '会员标识'
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['reportUmp'] = ['patient_id', 'spot_id', 'record_id'];
        $parent['choose-doctor'] = ['second_department_id', 'type','record_type'];
        return $parent;
    }

    /**
     * 
     * @param type $recordId 流水ID
     * @return 根据流水ID获取  报到信息
     */
    public static function reportRecord($recordId) {
        $data = self::find()->select(['id', 'patient_id', 'create_time', 'doctor_id'])->where(['record_id' => $recordId, 'spot_id' => self::$staticSpotId])->asArray()->one();
        return $data;
    }

    public function beforeSave($insert) {
        if ($this->scenario != 'reportUmp') {
            $departmentInfo = SecondDepartment::getDepartmentFields($this->second_department_id, ['parent_id']);
            $this->once_department_id = $departmentInfo['parent_id'];
        }
        return parent::beforeSave($insert);
    }

    /**
     * 
     * @param type $typeId 服务类型ID(gzh_spot_type表的主键ID)
     * @return 根据服务类型ID 获取病例类型
     */
    public static function getRecordType($typeId) {
        $data = (new \yii\db\Query())
                ->from(['a' => SpotType::tableName()])
                ->select(['b.record_type'])
                ->leftJoin(['b' => OrganizationType::tableName()], '{{a}}.organization_type_id={{b}}.id')
                ->where(['a.id' => $typeId])
                ->one();
       if(empty($data) ||  $data['record_type'] == 0){
           return 1;
       }
       return $data['record_type'];
    }
    
    /**
     * @desc 获取当前诊所该就诊记录id的报到字段信息
     * @param integer $recordId 就诊流水id
     * @param array|string $fields 字段属性
     */
    public static function getFieldsList($recordId,$fields = '*'){
        
        return self::find()->select($fields)->where(['spot_id' => self::$staticSpotId,'record_id' => $recordId])->asArray()->one();
    }
    
    
    /**
     * 
     * @param type $recordId 流水ID
     * @return 根据流水ID获取医生姓名
     */
    public static function getDoctorName($recordId) {
        $data = (new \yii\db\Query())
                ->from(['a' => PatientRecord::tableName()])
                ->leftJoin(['b' => self::tableName()], '{{b}}.record_id={{a}}.id')
                ->leftJoin(['c' => User::tableName()], '{{c}}.id={{b}}.doctor_id')
                ->select(['c.username', 'a.charge_type'])
                ->where(['a.id' => $recordId, 'a.spot_id' => self::$staticSpotId])
                ->one();
        return $data;
    }

    /**
     *
     * @param 验证预约服务类型 $attribute
     * @param unknown $params
     */
    public function validateType($attribute){
        if (!$this->hasErrors()) {
            if ($this->isNewRecord) {
                $appointmentInfo = Appointment::getAppointmentUserInfo($this->record_id);
                if (!empty($appointmentInfo) && !$this->id) {
                    if($appointmentInfo['type'] != $this->type || $appointmentInfo['doctor_id'] != $this->doctor_id){
                        $result = Appointment::getDoctorType($this->doctor_id,$this->type);
                        if(!$result){
                            $this->addError($attribute, '预约服务类型不可用');
                        }
                    }
                }else{
                    $result = Appointment::getDoctorType($this->doctor_id,$this->type);
                    if(!$result){
                        $this->addError($attribute, '预约服务类型不可用');
                    }
                }
            } else {
                if ($this->getOldAttribute('type') != $this->type || $this->getOldAttribute('doctor_id') != $this->doctor_id) {
                    $result = Appointment::getDoctorType($this->doctor_id,$this->type);
                    if(!$result){
                        $this->addError($attribute, '预约服务类型不可用');
                    }
                }
            }
        }
    }
}
