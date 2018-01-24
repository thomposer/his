<?php

namespace app\modules\make_appointment\models;

use app\modules\spot\models\OrganizationType;
use Yii;
use app\modules\patient\models\Patient;
use yii\base\Object;
use app\modules\spot_set\models\OnceDepartment;
use app\modules\spot_set\models\SecondDepartment;
use app\common\base\BaseActiveRecord;
use app\modules\patient\models\PatientRecord;
use app\modules\spot\models\Spot;
use app\modules\spot\models\SpotConfig;
use yii\db\Query;
use app\modules\spot_set\models\SpotType;
use yii\helpers\Json;
use app\modules\report\models\search\AppointmentSearch;
use app\modules\user\models\UserSpot;
use app\modules\user\models\User;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use app\modules\spot_set\models\UserAppointmentConfig;
use app\modules\spot_set\models\SecondDepartmentUnion;
/**
 * This is the model class for table "{{%appointment}}".
 *
 * @property string $id
 * @property integer $spot_id
 * @property string $patient_id
 * @property string $record_id
 * @property integer $type
 * @property string $type_description
 * @property integer $once_department_id
 * @property integer $second_department_id
 * @property string $time
 * @property string $first_time
 * @property string $doctor_id
 * @property string $remarks
 * @property string $create_time
 * @property string $update_time
 *
 * @property string $username //患者姓名
 * @property string $head_img //或者头像
 * @property integer $iphone //手机号
 * @property integer $birthday //出生日期
 * @property integer $editStatus //预约时间是否被更改
 * @property integer $sex //性别
 * @property integer $hasAppointmentOperator 是否记录被修改过，0-未修改，1-已修改
 * @property integer $oldType 原预约服务id
 */
class Appointment extends BaseActiveRecord
{

    public $username;
    public $iphone;
    public $birthday;
    public $hourMin;
    public $sex;
    public $head_img;
    public $status;
    public $departmentName;
    public $type;//预约类型id
    public $type_description;//预约服务类型名称
    public $doctorName; //预约医生
    public $departmentDoctorName; //预约信息
    public $appointmentDate; //预约日期
    public $editStatus; //预约时间是否被更改
    public $patient_source; //患者来源
    public $appointment_begin_time;//预约开始时间
    public $appointment_end_time;//预约结束时间
    public $age;//年龄
    public $hasAppointmentOperator;//是否有被修改的状态
    public $patientNumber;//病历号
    public $firstRecord;//是否第一次就诊
    public $oldType;//原预约服务
    public $appointmentOperator;//预约操作人
    public $spot_name;//预约诊所
//    public $appointment_origin;
//     public $appointmentType;//预约类型
//     public $appointmentTime;//预约时间
//     public $appointmentDepartment;//预约科室
//     public $appointmentId;

    public function init() {
        parent::init();
        $this->editStatus = true;
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%appointment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'birthday', 'sex', 'iphone', 'type', 'time', 'spot_id', 'appointment_origin', 'patient_source','appointmentDate'], 'required'],
            [['patient_id', 'spot_id', 'record_id', 'type', 'once_department_id', 'second_department_id', 'doctor_id', 'create_time', 'update_time', 'sex', 'status', 'appointment_origin','appointment_operator','appointment_creater','appointment_cancel_operator','cancel_online','time', 'first_time'], 'integer'],
            [['username', 'iphone', 'birthday'], 'trim'],
            [['doctor_id','illness_description'], 'required', 'on' => 'appointmentOne'],
            [['illness_description'], 'required', 'on' => 'create'],
            [['illness_description'], 'required', 'on' => 'update'],
            ['username', 'validateUsername', 'on' => 'appointmentOne'],
            [['remarks','illness_description', 'head_img', 'departmentName'], 'string'],
            [['doctor_id','hasAppointmentOperator','appointment_operator','appointment_creater','appointment_cancel_operator','cancel_online'], 'default', 'value' => 0],
            ['patient_source', 'default', 'value' => 0],
            [['record_id'], 'unique'],
            [['iphone'], 'match', 'pattern' => '/^\d{11}$/'],
            ['birthday', 'date', 'max' => date('Y-m-d'), 'min' => '1970-01-01'],
            ['username', 'string', 'max' => 64],
            ['illness_description', 'string', 'max' => 500],
            ['illness_description', 'default', 'value' => ''],
            ['appointment_cancel_reason','string','max'=>30],
            ['appointment_cancel_reason', 'default', 'value' => ''],
            ['username', 'validateUsername', 'on' => 'create'],
            ['username', 'validateUsername', 'on' => 'update'],
            ['hourMin', 'validateHourMin'],
            ['time', 'validateTime', 'on' => 'create'],
            ['time', 'validateTime', 'on' => 'appointmentOne'],
            ['doctor_id', 'validateDoctorId', 'on' => 'appointmentOne'],
            [['type'],'validateType','on' => 'appointmentOne'],
            [['appointmentDate', 'editStatus','age','hasAppointmentOperator','type_description'], 'safe']
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['createByDoctor'] = ['type','second_department_id'];
        $parent['cancelAppointment'] = ['appointment_cancel_reason','appointment_cancel_operator'];

        return $parent;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'patient_id' => '患者ID',
            'record_id' => '就诊流水ID',
            'type' => '预约服务',
            'type_description' => '预约服务',
            'second_department_id' => '预约科室',
            'departmentName' => '预约科室',
            'appointmentDate' => '预约日期',
            'time' => '预约时间',
            'doctor_id' => '预约医生',
            'remarks' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'username' => '姓名',
            'userName' => '患者姓名', // 详情页显示为患者姓名
            'iphone' => '手机号',
            'birthday' => '出生日期',
            'hourMin' => '出生时间',
            'sex' => '性别',
            'head_img' => '头像',
            'status' => '状态',
            'doctorName' => '预约医生',
            'appointment_origin' => '预约渠道',
            'patient_source' => '患者来源',
            'appointment_begin_time'=>'预约开始时间',
            'appointment_end_time'=>'预约结束时间',
            'departmentDoctorName' => '预约信息',
            'age' => '年龄',
            'illness_description'=>'病情自述',
            'appointment_operator'=>'预约操作人',
            'appointmentOperator'=>'预约操作人',
            'hasAppointmentOperator' => '是否被修改过记录',
            'patientNumber' => '病历号',
            'appointment_cancel_operator'=>'关闭预约操作人',
            'cancel_online'=>'是否线上取消预约',
            'appointment_cancel_reason'=>'关闭原因',
//             'appointmentId' => '预约id',
            'spot_name'=>'预约诊所',
        ];
    }

    public static $getType = [
        1 => '初诊',
        2 => '复诊',
        3 => '小儿推拿'
    ];
    public static $getAppointmentOrigin = [
        3 => '线上-妈咪知道',
        5 => '线上-微信问医版',
        7 => '线上-医信儿科微信公众号',
        9 => '线上-好大夫在线',
        10 => '线上-就医160',
        1080 => '呼吸天使（公众号）',
        2 => '客服电话',
        1 => '到店',
        6 => '直接联系内部人员',
        4 => '其他',
    ];

    /**
     *
     * @param 开始时间 $start_date
     * @param 结束时间 $end_date
     * @return 返回所有医生预约信息api
     */
    public static function getAppointmentStation($start_date, $end_date) {
        $query = (new \yii\db\Query());
        $spot_id = self::$staticSpotId;
        $query->from(['a' => Appointment::tableName()]);
        $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id = {{b}}.id');
        $query->select(['a.doctor_id', 'a.time']);
        $query->where(['between', 'a.time', strtotime($start_date), strtotime($end_date) + 86400]);
        $query->andWhere(['a.spot_id' => $spot_id]);
//        $query->andWhere('b.status != :status', [':status' => 7]);
        $query->andWhere('b.status != 7 and b.status != 8');
        $list = $query->all();
        $data = [];
        if (!empty($list)) {
            foreach ($list as $val) {
                $time = date('Y-m-d', $val['time']);
                $data[$time][$val['doctor_id']]['appointment_num'] ++;
            }
        }
        return $data;
    }

    public function validateUsername($attribute, $params) {

        if (!$this->hasErrors()) {
            $hasRecord = Patient::find()->select(['id'])->where(['username' => $this->username, 'iphone' => $this->iphone, 'spot_id' => $this->parentSpotId])->asArray()->one();
            if (!$this->patient_id && $hasRecord) {
                $this->addError($attribute, '已存在姓名和手机号一致的患者,请重新输入');
            } else if ($hasRecord && $hasRecord['id'] != $this->patient_id) {
                $this->addError($attribute, '已存在姓名和手机号一致的患者,请重新输入');
            }
        }
    }

    public function validateHourMin($attribute, $params) {
        if (!$this->hasErrors()) {
            $birthday = $this->birthday . ' ' . $this->hourMin;
            if (strtotime($birthday) > time()) {
                $this->addError($attribute, '出生时间的值必须不大于当前时间');
            }
        }
    }

    public function validateTime($attribute, $params) {
        if (!$this->hasErrors()) {
//              $cacheKey = Yii::getAlias('@doctorTime').$this->doctor_id;
//              $list = Yii::$app->cache->get($cacheKey);
            $type = Spot::find()->select(['appointment_type'])->where(['id' => $this->spotId])->asArray()->one();
            $type = explode(',', $type['appointment_type']);
            if (!in_array(1, $type) && $this->doctor_id != null) {
                $this->addError($attribute, '非法操作');
            }
            $result = '';
            $rows = [];
            //医生预约
            if (in_array(1, $type)) {
                $id = $this->isNewRecord?0:$this->id;
                $rows = self::getDoctorTime($this->doctor_id, $this->type,$id);
                $query = new Query();
                $query->from(['a' => Appointment::tableName()]);
                $query->select(['a.id']);
                $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id = {{b}}.id');
                $query->where(['a.spot_id' => $this->spotId, 'a.time' => $this->time, 'a.doctor_id' => $this->doctor_id]);
//                $query->andWhere('b.status != :status', [':status' => 7]);
                $query->andWhere('b.status != 7 and b.status != 8');
                $result = $query->one();
            } else {
                $rows = self::getDepartmentTime($this->second_department_id, $this->type);
            }
            if ($this->isNewRecord) {
                if ($this->time <= time()) {
                    $this->addError($attribute, '预约时间必须大于当前时间');
                } else if ($result && in_array(1, $type)) {
                    $this->addError($attribute, '预约时间已被占用');
                } else if (!isset($rows[date('Y-m-d', $this->time)][$this->time]) || $rows[date('Y-m-d', $this->time)][$this->time]['selected'] != true) {
                    $this->addError($attribute, '预约时间不可选');
                }
            } else {
                $oldTime = $this->getOldAttribute('time');
                if ($oldTime != $this->time) {
                    if ($result && in_array(1, $type)) {
                        $this->addError($attribute, '预约时间已被占用');
                    } else if (!isset($rows[date('Y-m-d', $this->time)][$this->time]) || $rows[date('Y-m-d', $this->time)][$this->time]['selected'] != true) {
                        $this->addError($attribute, '预约时间不可选');
                    }
                }
            }
        }
    }
    /**
     *
     * @param 验证预约服务类型 $attribute
     * @param unknown $params
     */
    public function validateType($attribute){
        if (!$this->hasErrors()) {
            if ($this->isNewRecord) {
                $result = self::getDoctorType($this->doctor_id,$this->type);
                if(!$result){
                    $this->addError($attribute, '预约服务类型不可用');
                }
            } else {
                if ($this->oldType != $this->$attribute || $this->getOldAttribute('doctor_id') != $this->doctor_id) {
                    $result = self::getDoctorType($this->doctor_id,$this->type);
                    if(!$result){
                        $this->addError($attribute, '预约服务类型不可用');
                    }
                }
            }
        }
    }

    /**
     * @param $doctorId 医生id
     * @param $type 预约服务类型
     * @return array|bool
     */
    public static function getDoctorType($doctorId,$type){
        $query = new Query();
        $query->from(['a' => UserAppointmentConfig::tableName()]);
        $query->select(['a.id']);
        $query->leftJoin(['b' => SpotType::tableName()], '{{a}}.spot_type_id = {{b}}.id');
        $query->leftJoin(['c' => OrganizationType::tableName()], '{{b}}.organization_type_id = {{c}}.id');
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.user_id' => $doctorId,'a.spot_type_id' => $type , 'b.status' => 1,'c.status' => 1]);
        $result = $query->one();
        return $result;
    }

    /**
     *
     * @param 验证预约科室类型 $attribute
     * @param unknown $params
     */
    public function validateDescription($attribute, $params){
        if (!$this->hasErrors()) {
            if ($this->isNewRecord) {
                $hasRecord = SecondDepartment::find()->select(['id'])->where(['id' => $this->$attribute,'spot_id' => $this->spotId])->asArray()->one();
                if (!$hasRecord) {
                    $this->addError($attribute, '预约科室不存在');
                }
            } else {
                $oldDescription = $this->getOldAttribute($attribute);
                if ($oldDescription != $this->$attribute) {
                    $hasRecord = SecondDepartment::find()->select(['id'])->where(['id' => $this->$attribute,'spot_id' => $this->spotId])->asArray()->one();
                    if (!$hasRecord) {
                        $this->addError($attribute,'预约科室不存在');
                    }
                }
            }
        }
    }

    public function validateDoctorId($attribute, $params) {
        if (!$this->hasErrors()) {
                $result = (new Query())
                        ->from(["a" => UserSpot::tableName()])
                        ->leftJoin(["b" => SecondDepartment::tableName()], 'a.department_id = b.id')
                        ->leftJoin(["c" => SecondDepartmentUnion::tableName()], 'b.id = c.second_department_id')
                        ->where(["a.user_id" => $this->$attribute, "a.spot_id" => $this->spotId, "b.status" => "1", "c.spot_id" => $this->spotId])
                        ->count();
                if ($result == 0) {
                    $this->addError($attribute,'该医生没有关联科室，不能进行预约');
                }
        }
    }

    public function beforeSave($insert) {
        //默认添加随机一个二级科室和一级科室
        if($this->doctor_id != $this->getOldAttribute('doctor_id')){
            $query = new Query();
            $query->from(['a' => UserSpot::tableName()]);
            $query->select(['a.department_id','b.parent_id']);
            $query->leftJoin(['b' => SecondDepartment::tableName()],'{{a}}.department_id = {{b}}.id');
            $query->leftJoin(['c' => SecondDepartmentUnion::tableName()], '{{b}}.id = {{c}}.second_department_id');
            $query->where(['a.user_id' => $this->doctor_id,'a.spot_id' => $this->spotId,'a.status' => 1,'b.status' => 1, 'c.spot_id' => $this->spot_id]);
            $departmentId = $query->one();
            $this->once_department_id = $departmentId['parent_id']?$departmentId['parent_id']:0;
            $this->second_department_id = $departmentId['department_id']?$departmentId['department_id']:0;
        }

        if($this->isNewRecord){
            $this->appointment_creater = $this->userInfo->id;
            $this->first_time = $this->time;
            $this->appointment_operator = $this->userInfo->id;
        }else{
            if(1 == $this->hasAppointmentOperator) {
                $this->appointment_operator = $this->userInfo->id;
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     *
     * @param 医生id $userId
     * @param 就诊类型 $type
     * @param integer $id 就诊流水id
     * @param integer $startTime 限制预约开始时间
     * @param integer $endTime 限制预约结束时间
     * @param integer $scenarios 场景(0-只返回可预约时间，1-返回可预约和已预约时间)
     * @return 返回医生预约时间
     */
    public static function getDoctorTime($userId, $type,$id = null,$startTime = null,$endTime = null,$scenarios = 0) {

        $spotConfig = SpotConfig::find()->select(['begin_time', 'end_time'])->where(['spot_id' => self::$staticSpotId])->asArray()->one();

        $periodTime = SpotType::find()->select(['time'])->where(['id' => $type,'spot_id' => self::$staticSpotId,'status' => 1])->asArray()->one();

        $periodTime = $periodTime ? $periodTime['time'] / 10 : 0;
        $spotConfigBeginTime = $spotConfig ? $spotConfig['begin_time'] : '00:00';
        $spotConfigEndTime = $spotConfig ? $spotConfig['end_time'] : '23:50';
        //获取医生预约设置时间段列表
        //$list = AppointmentConfig::find()->select(['begin_time', 'end_time'])->where(['spot_id' => self::$staticSpotId, 'user_id' => $userId])->andWhere('end_time > :end_time', [':end_time' => time()])->andFilterWhere(['between','end_time',$startTime,$endTime])->orderBy(['begin_time' => SORT_ASC])->asArray()->all();
        $appointmentQuery = new Query();
        $appointmentQuery->from(['a' => AppointmentConfig::tableName()]);
        $appointmentQuery->select(['a.begin_time','a.end_time','b.spot_type_id']);
        $appointmentQuery->leftJoin(['b' => AppointmentTimeAndServer::tableName()],'{{a}}.id = {{b}}.time_config_id');
        $appointmentQuery->where(['a.spot_id' => self::$staticSpotId,'a.user_id' => $userId,'b.spot_type_id' => $type]);
        $appointmentQuery->andWhere('a.end_time > :end_time', [':end_time' => time()])->andFilterWhere(['between','a.end_time',$startTime,$endTime]);
        $appointmentQuery->orderBy(['a.begin_time' => SORT_ASC]);
        $list = $appointmentQuery->all();

        $rows = [];
        //已被预约的时间列表
        $query = new Query();
        $query->from(['a' => Appointment::tableName()]);
        $query->select(['a.time', 'b.type_time']);
        $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id = {{b}}.id');
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.doctor_id' => $userId]);
        //             $query->andWhere('a.time >= :time',[':time' => time() - 2*60*60 - 1]);
        $query->andWhere('b.status != 7 and b.status != 8');
//        $query->andWhere('b.status != :statue', [':statue' => 7]);
        $query->andFilterWhere(['!=','a.id',$id]);
        $query->andFilterWhere(['between','a.time',$startTime,$endTime]);
        $appointmentRecordList = $query->all();
        if ($list) {

            //获取当前诊所设置的关闭预约的时间list
            $closeConfigList = AppointmentTimeConfig::getSpotConfig(self::$staticSpotId);
            foreach ($list as $key => $v) {
                $beginTime = date('H:i', $v['begin_time']) > $spotConfigBeginTime ? $v['begin_time'] : strtotime(date('Y-m-d', $v['begin_time']) . ' ' . $spotConfigBeginTime);
                $endTime = date('H:i', $v['end_time']) > $spotConfigEndTime ? strtotime(date('Y-m-d', $v['end_time']) . ' ' . $spotConfigEndTime) : $v['end_time'];
                for ($i = $beginTime; $i < $endTime;) {
                    if (time() <= $i) { // 若当前时间小于预约时间，则保留，否则将被过滤
                        if (!empty($closeConfigList)) {
                            foreach ($closeConfigList as $close) {
                                if ($i < $close['begin_time'] || $i >= $close['end_time']) {
                                    $rows[date('Y-m-d', $i)][$i] = [
                                        'value' => $i,
                                        'selected' => true,
                                        'name' => date('H:i', $i)
                                    ];
                                } else {
                                    unset($rows[date('Y-m-d', $i)][$i]);
                                    break;
                                }
                            }
                        } else {
                            $rows[date('Y-m-d', $i)][$i] = [
                                'value' => $i,
                                'selected' => true,
                                'name' => date('H:i', $i)
                            ];
                        }
                    }
                    $i = $i + 600;
                }
            }

        }

        if (!empty($appointmentRecordList)) {
            foreach ($appointmentRecordList as $k => $value) {
                $appBeginTime = $value['time'];
                $period = $value['type_time'];
                for ($i = 1; $i <= ($period / 10); $i++) {
                    $hasAppointmentTime[$appBeginTime] = date('Y-m-d H:i', $appBeginTime);
                    $appBeginTime = $appBeginTime + 600; //10分钟为一间隔
                }
            }
        }
        //过滤去掉已经被预约的时间段
        if (count($hasAppointmentTime)) {
            foreach ($hasAppointmentTime as $k => $value) {
                if(isset($rows[date('Y-m-d', $k)][$k])){
                    unset($rows[date('Y-m-d', $k)][$k]);
                }
            }
        }
            /* 根据用户选择的预约类型来筛选合适的时间段给用户 */
            foreach ($rows as $k => $res) {
                foreach ($res as $r => $v) {
                    for ($e = 0; $e < ($periodTime); $e++) {
                        $time = $r + (intval($e) * 600);

                        if (!isset($rows[date('Y-m-d', $time)][$time])) {
                            unset($rows[$k][$r]);
                            continue;
//                             $rows[$k][$r]['selected'] = false;
                        }
                    }
                    if(isset($rows[$k][$r])){//若存在，证明该时间可选，则根据预约服务时长来过滤没必要显示的时间
                        for ($i = $r + 600 ; $i < $r + $periodTime * 600;$i++){
                                unset($rows[$k][$i]);
                        }
                    }
                    $r = $r + $periodTime * 600;

                }
                if(empty($res) || $periodTime == 0){//若没有服务类型，则直接去除时间
                    unset($rows[$k]);
                }
            }
            if($scenarios == 1){
                if (!empty($appointmentRecordList)) {
                    foreach ($appointmentRecordList as $k => $value) {
                        $appBeginTime = $value['time'];
                        $rows[date('Y-m-d', $appBeginTime)][$appBeginTime] = [
                            'value' => $appBeginTime,
                            'selected' => false,
                            'name' => date('H:i', $appBeginTime)
                        ];
                    }
                }
            }
        return $rows;
    }


     /**
     *
     * @param 医生id $doctorList
     * @param 就诊类型 $type
     * @param integer $id 就诊流水id
     * @param integer $startTime 限制预约开始时间
     * @param integer $endTime 限制预约结束时间
     * @return 返回医生预约时间
     */
    public static function getDoctorTimeList($doctorList, $type, $startTime = null, $endTime = null) {

        $spotConfig = SpotConfig::find()->select(['begin_time', 'end_time'])->where(['spot_id' => self::$staticSpotId])->asArray()->one();

        $spotConfigBeginTime = $spotConfig ? $spotConfig['begin_time'] : '00:00';
        $spotConfigEndTime = $spotConfig ? $spotConfig['end_time'] : '23:50';

        //获取医生预约设置时间段列表
        $listQuery = new Query();
        $listQuery->from(['ac' => AppointmentConfig::tableName()]);
        $listQuery->leftJoin(['ts' => AppointmentTimeAndServer::tableName()], '{{ts}}.time_config_id={{ac}}.id');
        $listQuery->leftJoin(['st' => SpotType::tableName()], '{{ts}}.spot_type_id={{st}}.id');
        $listQuery->select(['doctorId' => 'ac.user_id', 'beginTime' => 'ac.begin_time', 'endTime' => 'ac.end_time', 'timeList' => 'group_concat(st.time)', 'typeList' => 'group_concat(st.id)']);
        $listQuery->where(['ac.spot_id' => self::$staticSpotId, 'ac.user_id' => $doctorList,'st.status' => 1]);
        $listQuery->andWhere('ac.end_time > :end_time', [':end_time' => time()]);
        $listQuery->andFilterWhere(['st.id' => $type]);
        $listQuery->andFilterWhere(['between','ac.end_time',$startTime,$endTime]);
        $listQuery->orderBy(['ac.begin_time' => SORT_ASC]);
        $listQuery->groupBy('ac.id');
        $list = $listQuery->all();

        //获取医生当前关联的可预约的服务类型
        $doctorTypeData = UserAppointmentConfig::getDoctorServiceType($doctorList);
        $doctorTypeList = array();
        foreach ($doctorTypeData as $docId => $value) {
            $tmpTypeArr = explode(',', $value['typeIdList']);
            $doctorTypeList[$docId] = $tmpTypeArr;
        }



        //根据医生当前关联的可预约服务类型选择时间段下的最小可约时间
        foreach ($list as $key => $value) {
            $tmpTimeArr = explode(',', $value['timeList']);
            $tmpTypeArr = explode(',', $value['typeList']);
            foreach ($tmpTypeArr as $k => $v) {
                if(!isset($doctorTypeList[$value['doctorId']])){
                    $tmpTimeArr = array();
                    break;
                }
                if( array_search($v, $doctorTypeList[$value['doctorId']]) === FALSE ){
                    unset($tmpTimeArr[$k]);
                }
            }
            $list[$key]['timeLength'] = count($tmpTimeArr) ? min($tmpTimeArr) : 0;//
        }
        $rows = [];
        if ($list) {
            //获取当前诊所设置的关闭预约的时间list
            $closeTimeList = AppointmentTimeConfig::getSpotConfig(self::$staticSpotId);

            //初始化关闭预约时间列表
            $closeRows = array();
            foreach ($closeTimeList as $v) {
                for($i = $v['begin_time']; $i < $v['end_time']; $i += 600){
                    $closeRows[$i] = true;
                }
            }

            foreach ($list as $v) {
                if($v['timeLength'] == 0){
                    continue;
                }
                $beginTime = date('H:i', $v['beginTime']) > $spotConfigBeginTime ? $v['beginTime'] : strtotime(date('Y-m-d', $v['beginTime']) . ' ' . $spotConfigBeginTime);
                $endTime = date('H:i', $v['endTime']) > $spotConfigEndTime ? strtotime(date('Y-m-d', $v['endTime']) . ' ' . $spotConfigEndTime) : $v['endTime'];
                for ($i = $beginTime; $i < $endTime; $i += 600) {
                    if (time() <= $i && !isset($closeRows[$i])) { // 若当前时间小于预约时间且不在关闭预约时间段，则保留，否则将被过滤
                        $rows[$v['doctorId']][date('Y-m-d', $i)][$i] = [
                            'value' => $i,
                            'selected' => true,
                            'name' => date('H:i', $i),
                            'timeLength' => $v['timeLength'],
                        ];
                    }
                }
            }

            //已被预约的时间列表
            $query = new Query();
            $query->from(['a' => Appointment::tableName()]);
            $query->select(['a.time', 'b.type_time','doctorId' => 'a.doctor_id']);
            $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id = {{b}}.id');
            $query->where(['a.spot_id' => self::$staticSpotId, 'a.doctor_id' => $doctorList]);
            $query->andWhere('b.status != 7 and b.status != 8');
            $query->andFilterWhere(['between','a.time',$startTime,$endTime]);
            $appointmentRecordList = $query->all();

            $hasAppointmentTime = array();
            //初始化已预约时间列表  过滤去掉已经被预约的时间段
            if (!empty($appointmentRecordList)) {
                foreach ($appointmentRecordList as $value) {
                    $hasAppointmentBeginTime = $value['time'];
                    $period = $value['type_time'];
                    for ($i = 1; $i <= ($period / 10); $i++,$hasAppointmentBeginTime += 600) {
                        unset($rows[$value['doctorId']][date('Y-m-d', $hasAppointmentBeginTime)][$hasAppointmentBeginTime]);
                    }
                }
            }

            //过滤去掉已经被预约的时间段
//            if (count($hasAppointmentTime)) {
//                foreach ($hasAppointmentTime as $doctorId => $doctorTimeInfo) {
//                    foreach ($doctorTimeInfo as $time => $v) {
//                        if(isset($rows[$doctorId][date('Y-m-d', $time)][$time])){
//                            unset($rows[$doctorId][date('Y-m-d', $time)][$time]);
//                        }
//                    }
//                }
//            }

            foreach ($rows as $doctorId => $doctorTimeInfo) {
                foreach ($doctorTimeInfo as $date => $dateTimeInfo) {
                    foreach ($dateTimeInfo as $key => $value) {
                        if(!isset($rows[$doctorId][$date][$key])){
                            continue;
                        }
                        $periodTime = $value['timeLength'] * 60;//服务时长
                        for($i = $key + 600; $i < $key + $periodTime; $i += 600){
                            if(isset($dateTimeInfo[$i]) && $dateTimeInfo[$i]['timeLength'] == $value['timeLength']){
                                unset($rows[$doctorId][$date][$i]);
                            }else{
                                break;
                            }
                        }
                        if($i != $key + $periodTime){
                            unset($rows[$doctorId][$date][$key]);
                        }
                    }
                }
            }
        }
        return $rows;
    }

    public static function getDepartmentTime($id, $type) {
        $rows = [];
        $hasAppointmentTime = array();
        //获取一级科室组的所有预约配置
        $query = (new Query());
        $query->from(['a' => SecondDepartment::tableName()]);
        $query->select(['a.parent_id', 'b.begin_time', 'b.end_time', 'b.doctor_count']);
        $query->leftJoin(['b' => AppointmentConfig::tableName()], '{{a}}.parent_id = {{b}}.department_id');
        $query->where(['a.id' => $id]);
        $query->andWhere('b.end_time >= :time', [':time' => time()]);
        $query->orderBy(['begin_time' => SORT_ASC]);
        $list = $query->all();

        /* 获取预约空余时间间隔列表begin */
        if ($list) {
            //获取预约类型的时间间隔值
            $spotConfigInfo = SpotConfig::find()->select(['begin_time', 'end_time', 'first_visit', 'return_visit', 'massage'])->where(['spot_id' => self::$staticSpotId])->asArray()->one();
            if ($type == 1) {//初诊间隔
                $period = $spotConfigInfo ? $spotConfigInfo['first_visit'] : 30;
            } else if ($type == 2) {//复诊间隔
                $period = $spotConfigInfo ? $spotConfigInfo['return_visit'] : 10;
            } else if($type == 3) {
                $period = $spotConfigInfo ? $spotConfigInfo['massage'] : 10;
            }else if($type == 4){
                $type = SpotType::getSpotType();
                if(!empty($type)){
                    $period = $type[0]['time'];
                }
            }
            $spotConfigBeginTime = $spotConfigInfo ? $spotConfigInfo['begin_time'] : '00:00';
            $spotConfigEndTime = $spotConfigInfo ? $spotConfigInfo['end_time'] : '23:50';

            foreach ($list as $v) {
                $appointQuery = new Query();
                $appointQuery->from(['a' => Appointment::tableName()]);
                $appointQuery->select(['b.type_time', 'a.time', 'b.status']);
                $appointQuery->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id = {{b}}.id');
                $appointQuery->where(['a.once_department_id' => $v['parent_id'], 'a.doctor_id' => 0]);
                $appointQuery->andWhere(['between', 'a.time', $v['begin_time'], $v['end_time']]);
                $appointQuery->andWhere('b.status != 7 and b.status != 8');
                $appointmentList = $appointQuery->all();
                if ($appointmentList) {
                    foreach ($appointmentList as $key => $t) {

                        $periodHistory = $t['type_time'];

                        $val = $t['time'];
                        for ($i = 1; $i <= ($periodHistory / 10); $i++) {
                            $hasAppointmentTime[$val][$key] = date('Y-m-d H:i', $val);
                            $val = $val + 600; //10分钟为一间隔
                        }
                    }
                }
                for ($k = 0; $k < $v['doctor_count']; $k++) {
                    $delete = array();
                    $beginTime = date('H:i', $v['begin_time']) > $spotConfigBeginTime ? $v['begin_time'] : strtotime(date('Y-m-d', $v['begin_time']) . ' ' . $spotConfigBeginTime);
                    $endTime = date('H:i', $v['end_time']) > $spotConfigEndTime ? strtotime(date('Y-m-d', $v['end_time']) . ' ' . $spotConfigEndTime) : $v['end_time'];
                    for ($i = $beginTime; $i < $endTime;) {
                        if (time() <= $i) { // 若当前时间小于预约时间，则保留，否则将被过滤
                            $rows[$i][] = date('Y-m-d H:i', $i);
                        }

                        $i = $i + 600;
                    }
                }
            }
        }

        //过滤去掉已经被预约的时间段
        if (count($hasAppointmentTime)) {
            foreach ($hasAppointmentTime as $k => $value) {
                $count = count($value);
                for ($i = 0; $i < $count; $i++) {
                    unset($rows[$k][$i]);
                }
            }
        }
        $appList = [];
        //整合可预约的时间段
        if (count($rows)) {
            //获取当前诊所设置的关闭预约的时间list
            $closeConfigList = AppointmentTimeConfig::getSpotConfig(self::$staticSpotId);
            foreach ($rows as $key => $value) {
                if (!empty($value)) {
                    if (!empty($closeConfigList)) {
                        foreach ($closeConfigList as $close) {
                            if ($key < $close['begin_time'] || $key >= $close['end_time']) {
                                $appList[date('Y-m-d', $key)][$key] = [
                                    'value' => $key,
                                    'name' => date('H:i', $key),
                                    'selected' => true,
                                ];
                            } else {
                                unset($appList[date('Y-m-d', $key)][$key]);
                                break;
                            }
                        }
                    } else {
                        $appList[date('Y-m-d', $key)][$key] = [
                            'value' => $key,
                            'name' => date('H:i', $key),
                            'selected' => true,
                        ];
                    }
                }
            }
        }

        /* end */
        /* 根据用户选择的预约类型来筛选合适的时间段给用户 */
        $endPeriod = $period / 10;
        foreach ($appList as $k => $res) {
            foreach ($res as $r => $v) {
                for ($e = 0; $e < $endPeriod; $e++) {
                    $time = $r + (intval($e) * 600);
                    if (!isset($appList[$k][$time])) {
                        $appList[$k][$r]['selected'] = false;
                    }
                }
            }
        }
        if (!empty($appList)) {
            $max = max(max($appList));
            $lastTime = $max['value'] + 600;
            $appList[date('Y-m-d', $lastTime)][$lastTime] = [
                'value' => $lastTime,
                'name' => date('H:i', $lastTime),
                'selected' => false,
            ];
        }
        return $appList;
    }


    /**
     * @param patientId 患者Id
     * @return 根据患者ID获取 其预约时间
     */
    public static function getTimeList($patientId = 0) {
        $query = new Query();
        $timeList = $query->from(['a' => self::tableName()])
                ->select(['a.record_id', 'time' => 'FROM_UNIXTIME(a.time, "%Y-%m-%d %H:%i:%s")'])
                ->leftJoin(['pr' => PatientRecord::tableName()], '{{a}}.record_id={{pr}}.id')
                ->where(['a.spot_id' => self::$staticSpotId, 'a.patient_id' => $patientId, 'pr.status' => 1])
                ->orderBy('time', SORT_ASC)
                ->all();
        return $timeList;
    }

    /**
     * @param 流水ID
     * @return 根据流水ID获取预约信息
     */
    public static function getAppointment($recordId) {
        if (($model = Appointment::findOne(['record_id' => $recordId,'spot_id'=>self::$staticSpotId])) !== null) {
            return $model;
        }
        return null;
    }
    /**
     *
     * @param string $start_date 开始时间
     * @param string $end_date 结束时间
     * @param int $doctorId 医生id
     * @param int $appointment_type 预约类型
     * @param int $department_id 二级科室id
     * @return string 返回预约-人数统计／医生工作台的 预约人数和详情
     */
    public static function getAppointmentDetail($start_date,$end_date,$doctorId = NULL,$type = null){
        $result['appoint_daily_total'] = '';
        $result['appoint_daily_detail'] = '';
        $where = array();
        if (!$start_date || !$end_date) {//开始时间与结束必填
            $result['errorCode'] = 1001;
            return Json::encode($result);
        }
        if ($type) {
            $where['type'] = $type;
        }
        if ($doctorId) {
            $where['doctor_id'] = $doctorId;
        }
        $query = (new Query());
        $query->from(['a' => Appointment::tableName()]);
        $query->select(['a.id', 'a.time']);
        $query->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id');
        $query->leftJoin(['c' => PatientRecord::tableName()], '{{a}}.record_id = {{c}}.id');
        $query->where(['between', 'a.time', strtotime($start_date), strtotime($end_date) + 86400]);
        $query->andWhere(['a.spot_id' => self::$staticSpotId]);
//        $query->andWhere('c.status != :status', [':status' => PatientRecord::$setStatus[7]]);
        $query->andWhere('c.status != 7 and c.status != 8');
        $query->andFilterWhere($where);
        $list = $query->all();
        $spotConfig = SpotConfig::find()->select(['begin_time', 'end_time', 'reservation_during'])->where(['spot_id' => self::$staticSpotId])->asArray()->one();
        $detail = '';
        if ($list) {
            foreach ($list as $v) {
                $key = date('Y-m-d', $v['time']);
                $spotConfigBeginTime = strtotime($key.' '.$spotConfig['begin_time']);
                $spotConfigEndTime = strtotime($key.' '.$spotConfig['end_time']);

                if ($v['time'] <= strtotime($key.' 13:00') && $spotConfigBeginTime <= $v['time'] && $v['time'] <=  $spotConfigEndTime ) {
                    $result['appoint_daily_total'][$key]['amCount']++;
                } else if($v['time'] > strtotime($key.' 13:00') && $spotConfigEndTime >= $v['time'] && $spotConfigBeginTime <= $v['time']){
                    $result['appoint_daily_total'][$key]['pmCount']++;
                }
                $time = $v['time'];
                if(!empty($spotConfig)) {
                    $configBegin = strtotime($key.' '.$spotConfig['begin_time']);
                    $configEnd = strtotime($key.' '.$spotConfig['end_time']);
                    $periodTime = $spotConfig['reservation_during'] * 60;
                    for ($i = $configBegin;$i < $configEnd;){
                        $endTime = $i + $periodTime;
                        if($time >= $i && $time < $endTime){
                            $detail[date('Y-m-d H:i',$i)]++;
                            //$this->result['appoint_daily_detail'][$dateTime]++;
                        }
                        $i = $endTime;
                    }
                }
            }

        }
        $result['appoint_daily_detail'] = $detail;
        return json_encode($result, true);
    }



    public static function getAppointmentTimeList(){
        $searchModel = new AppointmentSearch();
        $query = new Query();
        $query->from(['a' => SecondDepartment::tableName()]);
        $query->leftJoin(['b' => SecondDepartmentUnion::tableName()], '{{a}}.id = {{b}}.second_department_id');
        $query->select(['a.id', 'a.name']);
        $query->where(['a.spot_id' => self::$staticParentSpotId, 'b.spot_id' => self::$staticSpotId, 'a.status' => 1]);
        $secondDepartmentInfo = $query->all();

        $userIdArray = UserSpot::find()->select(['user_id'])->where(['spot_id' => self::$staticSpotId]);
        $doctorInfo = User::find()->select(['id', 'username', 'occupation', 'spot_id'])->where(['id' => $userIdArray, 'occupation' => 2,'status' => 1])->asArray()->all();
        $spotConfig = SpotConfig::find()->select(['begin_time', 'end_time', 'reservation_during'])->where(['spot_id' => self::$staticSpotId])->asArray()->all();
        $appointment_type = self::checkAppointmentType();

        $timeLine = array();
        $date = date('Y-m-d', time());
        $begin = strtotime($date . ' ' . $spotConfig[0]['begin_time']);
//         $beginSpilt = strtotime($date . ' ' . $spotConfig[0]['begin_time']);
        $end = strtotime($date . ' ' . $spotConfig[0]['end_time']);

        if (empty($spotConfig)) {
            $time = array();
        } else {
            $timeLine[] = $spotConfig[0]['begin_time'];
//             $nextTimeSpilt[] = $spotConfig[0]['begin_time'];

            if(!$spotConfig[0]['reservation_during']){
                $spotConfig[0]['reservation_during'] = 30;
            }
            for ($i = 1; $i > 0; $i++) {
                if (($begin + 60 * $spotConfig[0]['reservation_during']) < $end) {
                    $begin = $begin + 60 * $spotConfig[0]['reservation_during'];
                    $nextTime = date('H:i', $begin);
                    $timeLine[] = $nextTime;

                } else {
//                     $timeLine[] = date('H:i',$end);//若最后区间大于结束开放时间，那么默认最后区间为结束时间
                    break;
                }
            }
            $time = json_encode($timeLine);
        }

        $timeLineSpilt = [];
        $spiltLength = $spotConfig[0]['reservation_during']/10;
        foreach($timeLine as $key => $value){
            $timeLineSpilt[$value][] = $value;
            for($i = 1;$i<$spiltLength;$i++){
                $timeLineSpilt[$value][] = date("H:i",strtotime($value)+$i*10*60);
            }

        }
        $timeLineSpilt = json_encode($timeLineSpilt);
        $closeAppointment = Url::to(['@closeAppointment']);
        $saveCloseAppointment = Url::to(['@saveCloseAppointment']);
        $closeAppointmentTime = AppointmentTimeConfig::find()->asArray()->where(['spot_id'=>self::$staticSpotId])->all();
        $closeTimeLine = [];
        foreach($closeAppointmentTime as $key => $value){
            $keyDate = date("Y-m-d",$value['begin_time']);
            $closeTimeLine[$keyDate][] = date('Y-m-d H:i', $value['begin_time']);
            $beginDate = $value['begin_time'];
            $endDate = $value['end_time'];
            for ($i = 1; $i > 0; $i++) {
                if (($beginDate + 60 * 10) <= $endDate-$spotConfig[0]['reservation_during']) {
                    $beginDate = $beginDate + 60 * 10;
                    $nextTime = date('Y-m-d H:i', $beginDate);
                    $nextDate = date("Y-m-d",$beginDate);
                    $closeTimeLine[$nextDate][] = $nextTime;
                } else {
                    break;
                }
            }

        }
        $spot_type = SpotType::getSpotType();
        $appointmentTimeList =  [
            'searchModel' => $searchModel,
            'secondDepartmentInfo' => $secondDepartmentInfo,
            'doctorInfo' => $doctorInfo,
            'timeLine' => $time,
            'saveCloseAppointment'=>$saveCloseAppointment,
            'appointment_type' => $appointment_type,
            'closeAppointmentTime'=>$closeAppointmentTime,
            'closeTimeLine'=>$closeTimeLine,
            'timeLineSpilt'=>$timeLineSpilt,
            'spiltLength'=>$spiltLength,
            'spot_type'=>$spot_type,
            'entrance'=>'1',//入口，区分从预约还是医生工作台打开人数统计的预约情况，1-预约，2-医生工作台
            'doctorId'=>  Yii::$app->user->identity->id,
            ];
        return $appointmentTimeList;
    }

    /**
     * @return 返回当前诊所的预约类型(1-医生预约,2-科室预约)
     */
    public static function checkAppointmentType() {
        $result = Spot::find()->select(['appointment_type'])->where(['id' => self::$staticSpotId])->asArray()->one();
        return explode(',', $result['appointment_type']);
    }
    /**
     *
     * @param integer $id 就诊流水id
     * @param string|array $fields 想获取的字段信息,默认全部字段
     */
    public static function getAppointmentInfo($id,$fields = '*'){
        return self::find()->select($fields)->where(['record_id' => $id,'spot_id' => self::$staticSpotId])->asArray()->one();
    }

    /**
     *
     * @param integer $id 就诊流水id
     * @return 返回对应就诊流水的预约信息(预约服务，医生，科室)
     */
    public static function getAppointmentUserInfo($id){
        $query = new Query();
        $query->from(['a' => PatientRecord::tableName()]);
        $query->select(['a.type','a.type_description','b.doctor_id','b.second_department_id','c.username']);
        $query->leftJoin(['b' => self::tableName()],'{{a}}.id = {{b}}.record_id');
        $query->leftJoin(['c' => User::tableName()],'{{b}}.doctor_id = {{c}}.id');
        $query->where(['a.id' => $id,'a.spot_id' => self::$staticSpotId]);
        return $query->one();
    }

}
