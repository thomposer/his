<?php

namespace app\modules\patient\models;

use app\common\Percentage;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\DentalHistory;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\spot\models\RecipeList;
use app\modules\spot\models\Spot;
use app\modules\triage\models\TriageInfoRelation;
use Yii;
use app\modules\report\models\Report;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\modules\triage\models\TriageInfo;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\base\Object;
use app\modules\make_appointment\models\Appointment;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use yii\helpers\ArrayHelper;
use app\modules\outpatient\models\CheckRecord;
use app\modules\outpatient\models\InspectRecord;
use app\modules\outpatient\models\MedicalFile;
use app\modules\outpatient\models\ChildExaminationAssessment;
use app\modules\outpatient\models\ChildExaminationBasic;
use app\modules\outpatient\models\ChildExaminationCheck;
use app\modules\outpatient\models\ChildExaminationGrowth;
use app\modules\triage\models\HealthEducation;
use app\modules\triage\models\NursingRecord;
use app\modules\outpatient\models\OutpatientRelation;
use app\modules\patient\models\PatientFamily;
use app\modules\outpatient\models\FirstCheck;
use app\modules\outpatient\models\ChildExaminationInfo;
use app\modules\triage\models\ChildAssessment;
use app\modules\outpatient\models\OrthodonticsReturnvisitRecord;
use app\modules\outpatient\models\OrthodonticsFirstRecord;
use app\modules\outpatient\models\OrthodonticsFirstRecordExamination;
use app\modules\outpatient\models\OrthodonticsFirstRecordFeatures;
use app\modules\outpatient\models\OrthodonticsFirstRecordModelCheck;
use app\modules\outpatient\models\OrthodonticsFirstRecordTeethCheck;

/**
 * This is the model class for table "{{%patient}}".
 * 患者用户表
 * @property integer $id
 * @property string $username 患者名称
 * @property integer $sex 性别
 * @property string $birthday 出生日期
 * @property string $nation 民族
 * @property integer $marriage 婚否
 * @property string $occupation 职位
 * @property string $province 省份
 * @property string $city 城市
 * @property string $area 区
 * @property string $detail_address 地址
 * @property string $address 省/市/区
 * @property string $head_img 头像
 * @property string $iphone 手机号码
 * @property string $email 邮箱
 * @property decimal $heightcm 身高
 * @property decimal $weightkg 体重
 * @property enum $bloodtype 血型
 * @property integer $temperature_type 体温类型(1 -'口温',2 - '耳温',3- '额温', 4 -'腋温', 5 -'肛温')
 * @property integer $temperature 体温
 * @property integer $breathing 呼吸
 * @property integer $pulse 脉搏
 * @property integer $shrinkpressure 收缩压
 * @property integer $diastolic_pressure 舒张压
 * @property integer $oxygen_saturation 氧饱和度
 * @property integer $pain_score 疼痛评分
 * @property integer $fall_score 疼痛评分
 * @property integer $treatment_type 就诊方式
 * @property integer $treatment 就诊方式备注
 * @property string $personalhistory 个人史
 * @property string $genetichistory 家族史
 * @property integer $first_record 是否第一次就诊 1是/2不是
 * @property integer $type 接诊类型
 * @property integer $family_relation 成员关系
 * @property string $family_name 成员姓名
 * @property integer $family_birthday 成员生日
 * @property integer $family_sex 成员性别
 * @property string $family_iphone 成员手机号
 * @property string $remark 备注
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $spot_id 机构ID
 * @property integer $diagnosis_time 接诊时间
 * @property integer $record_id 就诊流水ID
 * @property integer $status 就诊流水状态
 * @property string $wechat_num 微信号
 * @property integer $patient_source 患者来源
 */
class Patient extends \app\common\base\BaseActiveRecord
{

    public $address;
    public $type;
    public $age;
    public $birthTime; //出生日期
    public $family_relation;
    public $family_name;
    public $family_birthday;
    public $family_sex;
    public $family_iphone;
    public $family_card;
    public $record_id;
    public $status;
    public $patient_info; //用户基本信息，例如 隔壁老王的儿子（男 2岁3个月21天）
    public $search_start_date; //开始搜索日期
    public $search_end_date; //结束搜索日期
    public $hourMin;
    public $checkName;
    public $recordId;
    public $itemName;
    public $name_phone; //患者姓名或手机号
    public $bmi;
    public $record_count;
    public $end_time;
    public $patientAllergy;
    public $hasAllergy = 1;

    public function init() {
        parent::init();
        $this->spot_id = $this->parentSpotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%patient}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [

            [['username', 'sex', 'spot_id', 'iphone'], 'required'],
            [['sex', 'birthday', 'marriage', 'spot_id', 'sex', 'create_time', 'update_time', 'type', 'record_id', 'diagnosis_time', 'patient_source', 'fall_score', 'first_record', 'end_time'], 'integer'],
            [['username', 'iphone', 'email', 'card', 'birthday'], 'trim'],
            [['head_img', 'worker', 'wechat_num', 'patient_number'], 'string'],
            ['blood_type_supplement', 'default', 'value' => ''],
            [['username', 'address', 'detail_address', 'worker', 'bloodtype', 'treatment', 'mommyknows_account'], 'string', 'max' => 64],
            [['nation', 'province', 'city', 'area'], 'string', 'max' => 32],
            [['nation', 'province', 'city', 'area', 'treatment', 'mommyknows_account'], 'default', 'value' => ''],
            ['email', 'email'],
            [['birthTime'], 'date', 'max' => date('Y-m-d'), 'min' => '1970-01-01'],
            /* ['card', 'match', 'pattern' => '/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/'], */
            ['iphone', 'match', 'pattern' => '/^\d{11}$/'],
            [['remark', 'genetichistory', 'personalhistory'], 'string'],
            [['remark', 'genetichistory', 'personalhistory'], 'default', 'value' => ''],
            [['birthTime'], 'required', 'on' => 'report'],
            [['patient_source'], 'required', 'on' => 'report'],
            [['birthTime', 'patient_source'], 'required', 'on' => 'createMaterial'],
            [['family_relation', 'family_name', 'family_sex', 'family_birthday', 'family_iphone', 'family_card'], 'validateFamily', 'on' => 'report'],
            ['username', 'validateUsername'],
//            [['temperature_type'], 'integer', 'max' => 5, 'min' => 1],
            [['temperature'], 'number', 'max' => 45, 'min' => 30],
            [['fall_score'], 'number', 'max' => 20, 'min' => 6],
            [['breathing'], 'integer', 'max' => 100, 'min' => 1],
            ['head_circumference', 'match', 'pattern' => '/^([0-9]\d*(.[0-9]){0,1})$/', 'message' => '头围只能精确到小数点后一位'],
            [['pulse'], 'integer', 'max' => 300, 'min' => 0],
            [['shrinkpressure'], 'integer', 'max' => 300, 'message' => '标签不合法，必须为汉字、字母或者数字！'], //收缩压
            [['shrinkpressure'], 'integer', 'min' => 50], //收缩压
            [['diastolic_pressure'], 'integer', 'max' => 150, 'min' => 30], //舒张压
            [['oxygen_saturation'], 'integer', 'max' => 300, 'min' => 0], //氧饱和度
            [['pain_score'], 'integer', 'max' => 10, 'min' => 0], //疼痛评分
            ['heightcm', 'match', 'pattern' => '/^([0-9]\d*(.[0-9]){0,1})$/', 'message' => '身高只能精确到小数点后一位'],
            [['weightkg'], 'number', 'min' => 0],
            [['weightkg'], 'number'],
            [['hourMin'], 'default', 'value' => '00:00'],
            [['birthTime'], 'date', 'on' => 'baseInformation'],
            [['birthTime'], 'required', 'on' => 'baseInformation'],
            [['birthTime'], 'date', 'on' => 'createMaterial'],
            [['birthTime'], 'required', 'on' => 'createPatient'],
            [['type', 'marriage', 'occupation', 'family_relation', 'family_sex', 'patient_source'], 'default', 'value' => 0],
            ['hourMin', 'validateHourMin'],
            [['treatment_type', 'temperature_type'], 'default', 'value' => '0'],
            [['patient_number'], 'default', 'value' => '0000000'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '病案号',
            'username' => '患者姓名',
            'sex' => '性别',
            'birthday' => '出生日期',
            'birthTime' => '出生日期',
            'age' => '年龄',
            'nation' => '民族',
            'marriage' => '婚姻状况',
            'occupation' => '职业',
            'province' => '省份',
            'address' => '住址',
            'city' => '城市',
            'area' => '区县',
            'detail_address' => '详细地址',
            'head_img' => '头像',
            'iphone' => '手机号',
            'wechat_num' => '微信号',
            'email' => '邮箱',
            'card' => '身份证号',
            'type' => '接诊类型',
            'worker' => '工作单位',
            'remark' => '备注',
            'heightcm' => '身高',
            'weightkg' => '体重',
            'bloodtype' => '血型',
            'blood_type_supplement' => '血型补充',
            'temperature_type' => '体温类型',
            'temperature' => '体温',
            'breathing' => '呼吸',
            'head_circumference' => '头围',
            'pulse' => '脉搏',
            'shrinkpressure' => '收缩压',
            'diastolic_pressure' => '舒张压',
            'oxygen_saturation' => '氧饱和度',
            'pain_score' => '疼痛评分',
            'fall_score' => '跌倒评分（HDFS 6-20）',
            'treatment_type' => '就诊方式',
            'treatment' => '',
            'personalhistory' => '个人史',
            'genetichistory' => '家族史',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'family_relation' => '成员关系',
            'family_name' => '姓名',
            'family_sex' => '性别',
            'family_birthday' => '出生日期',
            'family_iphone' => '手机号码',
            'family_card' => '身份证',
            'status' => '状态',
            'diagnosis_time' => '接诊时间',
            'search_start_date' => '开始时间',
            'search_end_date' => '结束时间',
            'patient_source' => '患者来源',
            'patient_info' => '患者信息',
            'hourMin' => '出生时间',
            'name_phone' => '患者姓名或手机号',
            'checkName' => '实验室检查',
            'recordId' => '流水id',
            'itemName' => '实验室检查',
            'patient_number' => '病历号',
            'first_record' => '是否第一次就诊',
            'record_count' => '就诊记录',
            'mommyknows_account' => '妈咪知道账号',
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['appointment'] = ['username', 'head_img', 'sex', 'iphone', 'birthday', 'spot_id', 'patient_source'];
        $parent['basic_case'] = ['has_allergy_type', 'allergySource', 'allergyReaction', 'allergyDegree', 'personalhistory', 'genetichistory'];
        $parent['createPatient'] = ['spot_id', 'head_img', 'username', 'birthTime', 'hourMin', 'sex', 'iphone', 'email', 'card', 'marriage', 'nation', 'occupation', 'worker', 'wechat_num', 'patient_source', 'address', 'detail_address', 'remark', 'patient_number'];
        $parent['createMaterial'] = ['username', 'head_img', 'sex', 'iphone', 'birthday', 'birthTime', 'hourMin', 'spot_id', 'patient_source', 'patient_number'];

        return $parent;
    }

    /**
     * @var患者来源
     */
    public static $getPatientSource = [
        7 => '妈咪知道APP',
        2 => '妈咪知道微信/微博',
        8 => '妈咪知道用户群/活动',
        11 => '好大夫在线',
        12 => '就医160',
        3 => '其他网络媒体',
        1 => '门诊患者推荐',
        9 => '诊所医生介绍',
        10 => '电话销售',
        5 => '地面推广',
        6 => '其他',
    ];

//    /**
//     * @var患者来源
//     */
//    public static $getPatientSource = [
//
//        1 => '朋友推荐',
//        2 => '微信',
//        3 => '媒体报道',
//        4 => '微博',
//        5 => '附近居民',
//        6 => '其他'
//    ];

    /**
     *
     * @var 性别
     */
    public static $getSex = [
        1 => '男',
        2 => '女',
        3 => '不详',
        4 => '其他'
    ];

    /**
     * @property 婚姻状况(1-未婚,2-已婚)
     * @var unknown
     */
    public static $getMarriage = [
        1 => '未婚',
        2 => '已婚',
        3 => '离异',
        4 => '分居',
        5 => '丧偶',
        6 => '保密'
    ];
    public static $patient_type = [
        1 => '初诊',
        2 => '复诊',
        3 => '小儿推拿'
    ];
    public static $temperature_type = [
        1 => '口温',
        2 => '耳温',
        3 => '额温',
        4 => '腋温',
        5 => '肛温',
    ];
    public static $treatment_type = [
        1 => '步行',
        2 => '抱入',
        3 => '扶持',
        4 => '轮椅',
        5 => '其他',
    ];

    /**
     *
     * @var 职业
     */
    public static $getOccupation = [
        1 => '医护人员',
        2 => '农民',
        3 => '商人',
        4 => '军人',
        5 => '教师',
        6 => '学生',
        7 => '公务员',
        8 => '公司职员',
        9 => '工人',
        10 => '媒体',
        11 => '无',
        12 => '其他'
    ];
    public static $getFamilyRelation = [
        1 => '父亲',
        2 => '母亲',
        3 => '配偶',
        4 => '儿子',
        5 => '女儿',
        6 => '（外）祖父/母',
        7 => '（外）孙子/女',
        8 => '兄弟',
        9 => '姐妹',
        10 => '朋友',
        11 => '其他',
    ];

    /**
     *
     * @var 民族
     */
    public static $getNation = [
        1 => '汉族', 2 => '蒙古族', 3 => '回族', 4 => '藏族', 5 => '维吾尔族',
        6 => '苗族', 7 => '彝族', 8 => '壮族', 9 => '布依族', 10 => '朝鲜族',
        11 => '满族', 12 => '侗族', 13 => '瑶族', 14 => '白族', 15 => '土家族',
        16 => '哈尼族', 17 => '哈萨克族', 18 => '傣族', 19 => '黎族', 20 => '傈僳族',
        21 => '佤族', 22 => '畲族', 23 => '高山族', 24 => '拉祜族', 25 => '水族',
        26 => '东乡族', 27 => '纳西族', 28 => '景颇族', 29 => '柯尔克孜族', 30 => '土族',
        31 => '达斡尔族', 32 => '仫佬族', 33 => '羌族', 34 => '布朗族', 35 => '撒拉族',
        36 => '毛南族', 37 => '仡佬族', 38 => '锡伯族', 39 => '阿昌族', 40 => '普米族',
        41 => '塔吉克族', 42 => '怒族', 43 => '乌孜别克族', 44 => '俄罗斯族', 45 => '鄂温克族',
        46 => '德昂族', 47 => '保安族', 48 => '裕固族', 49 => '京族', 50 => '塔塔尔族',
        51 => '独龙族', 52 => '鄂伦春族', 53 => '赫哲族', 54 => '门巴族', 55 => '珞巴族',
        56 => '基诺族',
    ];

    /*
     * ==============================
     * 此方法由 mantye 提供
     * http://my.oschina.net/u/223350
     * @date 2014-07-22
     * ==============================
     * @description    取得两个时间戳相差的年龄
     * @before         较小的时间戳
     * @after          较大的时间戳
     * @return str     返回相差年龄y岁m月d天
     * 0-28天 精确到小时 例如0天12小时
     * 29天-12月 精确到天 例如：2月15天
     * 1岁-6岁 精确到月 例如：2岁8个月
     * 6岁以上 精确到年 例如：8岁
     * */

    public static function dateDiffage($before, $after = 1) {
        if ($after == 1) {//默认为当前时间
            $after = time();
        }
        if ($before == 0) {
            return '';
        }
        if ($before > $after) {
            $b = getdate($after);
            $a = getdate($before);
        } else {
            $b = getdate($before);
            $a = getdate($after);
        }
        $n = array(1 => 31, 2 => 28, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31);
        $y = $m = $d = 0;
        if ($a['mday'] >= $b['mday']) { //天相减为正
            if ($a['mon'] >= $b['mon']) {//月相减为正
                $y = $a['year'] - $b['year'];
                $m = $a['mon'] - $b['mon'];
            } else { //月相减为负，借年
                $y = $a['year'] - $b['year'] - 1;
                $m = $a['mon'] - $b['mon'] + 12;
            }
            $d = $a['mday'] - $b['mday'];
        } else {  //天相减为负，借月
            if ($a['mon'] == 1) { //1月，借年
                $y = $a['year'] - $b['year'] - 1;
                $m = $a['mon'] - $b['mon'] + 12 - 1;
                $d = $a['mday'] - $b['mday'] + $n[12];
            } else {
                if ($a['mon'] == 3) { //3月，判断闰年取得2月天数
                    $d = $a['mday'] - $b['mday'] + ($a['year'] % 4 == 0 ? 32 : 31);
                } else {
                    $d = $a['mday'] - $b['mday'] + $n[$a['mon'] - 1];
                }
                if ($a['mon'] >= $b['mon'] + 1) { //借月后，月相减为正
                    $y = $a['year'] - $b['year'];
                    $m = $a['mon'] - $b['mon'] - 1;
                } else { //借月后，月相减为负，借年
                    $y = $a['year'] - $b['year'] - 1;
                    $m = $a['mon'] - $b['mon'] + 12 - 1;
                }
            }
        }
        $ret = "";
        if ($y == 0 && $m == 0 && 0 <= $d && $d <= 28) {
            if ($a['hours'] >= $b['hours']) {
                $h = abs($a['hours'] - $b['hours']);
            } else {
                $h = abs($a['hours'] + 24 - $b['hours']);
                $d = $d - 1;
            }
            $ret = $d . '天' . $h . '小时';
        } elseif ($y == 0 && $m <= 12) {
            $ret = $m . '个月' . $d . '天';
        } elseif ($y >= 1 && $y <= 6) {
            $ret = $y . '岁' . $m . '个月';
        } elseif ($y > 6) {
            $ret = $y . '岁';
        }
        return $ret;
    }

    /*
     * ==============================
     * 此方法由 mantye 提供
     * http://my.oschina.net/u/223350
     * @date 2014-07-22
     * ==============================
     * @description    取得两个时间戳相差的年，月，日，小时
     * @before         较小的时间戳
     * @after          较大的时间戳
     * @return str     返回相差的年，月，日

     * */

    public static function dateDiffageTime($before, $after = 1) {
        if ($after == 1) {//默认为当前时间
            $after = time();
        }
        if ($before == 0) {
            return '';
        }
        if ($before > $after) {
            $b = getdate($after);
            $a = getdate($before);
        } else {
            $b = getdate($before);
            $a = getdate($after);
        }
        $n = array(1 => 31, 2 => 28, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31);
        $y = $m = $d = 0;
        if ($a['mday'] >= $b['mday']) { //天相减为正
            if ($a['mon'] >= $b['mon']) {//月相减为正
                $y = $a['year'] - $b['year'];
                $m = $a['mon'] - $b['mon'];
            } else { //月相减为负，借年
                $y = $a['year'] - $b['year'] - 1;
                $m = $a['mon'] - $b['mon'] + 12;
            }
            $d = $a['mday'] - $b['mday'];
        } else {  //天相减为负，借月
            if ($a['mon'] == 1) { //1月，借年
                $y = $a['year'] - $b['year'] - 1;
                $m = $a['mon'] - $b['mon'] + 12 - 1;
                $d = $a['mday'] - $b['mday'] + $n[12];
            } else {
                if ($a['mon'] == 3) { //3月，判断闰年取得2月天数
                    $d = $a['mday'] - $b['mday'] + ($a['year'] % 4 == 0 ? 29 : 28);
                } else {
                    $d = $a['mday'] - $b['mday'] + $n[$a['mon'] - 1];
                }
                if ($a['mon'] >= $b['mon'] + 1) { //借月后，月相减为正
                    $y = $a['year'] - $b['year'];
                    $m = $a['mon'] - $b['mon'] - 1;
                } else { //借月后，月相减为负，借年
                    $y = $a['year'] - $b['year'] - 1;
                    $m = $a['mon'] - $b['mon'] + 12 - 1;
                }
            }
        }

        $ret = [
            'year' => 0,
            'month' => 0,
            'day' => 0,
            'hour' => 0
        ];
        if ($y == 0 && $m == 0 && 0 <= $d && $d <= 28) {
            if ($a['hours'] >= $b['hours']) {
                $h = abs($a['hours'] - $b['hours']);
            } else {
                $h = abs($a['hours'] + 24 - $b['hours']);
                $d = $d - 1;
            }
            $ret['day'] = $d;
            $ret['hour'] = $h;
//             $ret = $d . '天' . $h . '小时';
        } elseif ($y == 0 && $m <= 12) {
//             $ret = $m . '个月' . $d . '天';
            $ret['month'] = $m;
            $ret['day'] = $d;
        } elseif ($y >= 1 && $y <= 6) {
            $ret['year'] = $y;
            $ret['month'] = $m;
//             $ret = $y . '岁' . $m . '个月';
        } elseif ($y > 6) {
//             $ret = $y . '岁';
            $ret['year'] = $y;
        }
        return $ret;
    }

    public function validateUsername($attribute, $params) {

        if (!$this->hasErrors()) {
            if ($this->isNewRecord) {
                $hasRecord = $this->checkUnique($attribute, $this->username);
                if ($hasRecord) {
                    $this->addError($attribute, '已存在姓名和手机号一致的患者,请重新输入');
                    return;
                }
//                $hasReportRecord = Report::find()->select(['id'])->where(['patient_id' => $hasRecord['id']])->andWhere(['between', 'create_time', strtotime(date('Y-m-d', time())), strtotime(date('Y-m-d', time() + 86400))])->asArray()->one();
//                if ($hasReportRecord) {
//                    $this->addError($attribute, '该患者今天已经登记了');
//                    return;
//                }
            } else {
                $hasRecord = $this->checkUnique($attribute, $this->username);
                if ($hasRecord && $hasRecord['id'] != $this->oldAttributes['id']) {
                    $this->addError($attribute, '已存在姓名和手机号一致的患者,请重新输入');
                    return;
                }
            }
        }
    }

    public function validateFamily($attribute, $params) {
        if (!$this->hasErrors()) {
            if (!$this->family_relation) {
                foreach ($this->family_relation as $key => $v) {
                    if ($v != '' && $v != null) {
                        if ($this->family_birthday[$key] == null || $this->family_iphone == null || $this->family_name == null || $this->family_sex == null) {
                            $this->addError('family_birthday', '请填写完成家庭成员必填项。');
                        } else if (strtotime($this->family_birthday[$key]) > time()) {
                            $this->addError('family_birthday', '出生日期不能大于' . date('Y-m-d') . '。');
                        } else if (!preg_match('/^\d{11}$/', $this->family_iphone[$key])) {
                            $this->addError('family_iphone', '家庭成员手机号码是无效的。');
                        } else if (!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/', $this->family_card[$key])) {
                            $this->addError('family_card', '家庭成员身份证号是无效的。');
                        }
                    }
                }
            }
        }
    }

    private function checkUnique($attribute, $params) {
        $hasRecord = Patient::find()->select(['id'])->where([$attribute => $params, 'iphone' => $this->iphone, 'spot_id' => self::$staticParentSpotId])->asArray()->one();
        return $hasRecord;
    }

    public function beforeSave($insert) {
        if ($this->scenario == 'report' || $this->scenario == 'createPatient' || $this->scenario == 'baseInformation' || $this->scenario == 'createMaterial') {
            $this->birthday = strtotime($this->birthTime . ' ' . $this->hourMin);
            if ($this->address) {
                $address = explode('/', $this->address);
                $this->province = $address[0] ? $address[0] : '';
                $this->city = $address[1] ? $address[1] : '';
                $this->area = $address[2] ? $address[2] : '';
            }
        }
        if (($insert && $this->scenario != 'appointment') || $this->scenario == 'report') { //新建患者   则将唯一的打印患者号写入
            $this->patient_number = $this->patient_number != '0000000' ? $this->patient_number : self::generatePatientNumber();
        }
        return parent::beforeSave($insert);
    }

    public static function generatePatientNumber() {
        $maxPatientNumber = Patient::find()->select(['patient_number'])->where(['spot_id' => self::$staticParentSpotId])->orderBy(['patient_number' => SORT_DESC])->asArray()->one();
        Yii::info('maxPatientNumber: ' . json_encode($maxPatientNumber));
        $sn = sprintf('%07d', ($maxPatientNumber['patient_number'] + 1));
        return $sn;
    }

    /**
     *
     * @param int $record_id 就诊记录ID
     * @return array
     *
     */

    /**
     *
     * @param int $record_id 就诊记录ID
     * @return array
     *
     */
    public static function findTriageInfo($record_id) {
        $query = new Query();
        $query->from(['b' => PatientRecord::tableName()]);
        $query->select([
            'c.patient_number', 'b.patient_id', 'b.case_id', 'b.child_check_status', 'b.create_time', 'd.type', 'd.type_description','b.recipe_number',
            'a.diagnosis_time', 'c.id', 'a.heightcm', 'a.weightkg', 'a.temperature_type', 'a.temperature', 'a.breathing',
            'a.pulse', 'a.shrinkpressure', 'a.diastolic_pressure', 'a.pain_score', 'a.fall_score', 'a.treatment_type',
            'a.treatment', 'b.id as recordId', 'b.patient_id', 'b.case_id', 'c.patient_number', 'c.username', 'c.head_img', 'c.first_record',
            'c.sex', 'c.birthday', 'a.heightcm', 'c.iphone', 'a.head_circumference', 'a.bloodtype', 'a.blood_type_supplement',
            'a.oxygen_saturation', 'reportTime' => 'd.create_time', 'b.end_time'
        ]);
        $query->leftJoin(['a' => TriageInfo::tableName()], '{{b}}.id = {{a}}.record_id');
        $query->leftJoin(['c' => Patient::tableName()], '{{b}}.patient_id = {{c}}.id');
        $query->leftJoin(['d' => Report::tableName()], '{{b}}.id = {{d}}.record_id');
        $query->where(['b.id' => $record_id, 'b.spot_id' => self::$staticSpotId]);
        $tringInfo = $query->one();

        $tringInfo['birth'] = $tringInfo['birthday'] ? date("Y-m-d", $tringInfo['birthday']) : ''; //格式化出生日期
        $tringInfo['birthtime'] = $tringInfo['birthday']; //没转换的出生日期
        $tringInfo['birthday'] = self::dateDiffage($tringInfo['birthday']);
        $tringInfo['bmi'] = self::getBmi($tringInfo['heightcm'], $tringInfo['weightkg']);
        $tringInfo['temperature_type'] = TriageInfo::$temperature_type[$tringInfo['temperature_type']];

        if ($tringInfo['diagnosis_time'] > 0) {
            $tringInfo['diagnosis_time_format'] = date("Y-m-d H:i", $tringInfo['diagnosis_time']);
        } else {
            $tringInfo['diagnosis_time_format'] = "";
        }
        if ($tringInfo['end_time'] > 0) {
            $tringInfo['end_time_format'] = date("Y-m-d H:i", $tringInfo['end_time']);
        } else {
            $tringInfo['end_time_format'] = "";
        }

        $bloodTypeSupplement = $tringInfo['blood_type_supplement'] ? explode(',', $tringInfo['blood_type_supplement']) : '';
        if (!empty($bloodTypeSupplement)) {
            $bloodTypeSupplementStr = '';
            foreach ($bloodTypeSupplement as $key => $value) {
                $bloodTypeSupplementStr .= TriageInfo::$bloodTypeSupplement[$value] . '，';
            }
            $bloodTypeSupplement = rtrim($bloodTypeSupplementStr, '，');
        }
        if (5 == $tringInfo['treatment_type']) {
            $tringInfo['treatment'] = $tringInfo['treatment'];
        } else if (0 == $tringInfo['treatment_type']) {
            $tringInfo['treatment'] = null;
        } else {
            $tringInfo['treatment'] = TriageInfo::$treatment_type[$tringInfo['treatment_type']];
        }
        $tringInfo['bloodTypeSupplementStr'] = $bloodTypeSupplement;
        $tringInfo['bloodtype'] = TriageInfo::$bloodtype[$tringInfo['bloodtype']];
        $tringInfo['pain_score'] = ChildAssessment::getLastScore($record_id, 1);
        $tringInfo['fall_score'] = ChildAssessment::getLastScore($record_id, 2);
        return $tringInfo;
    }

    /**
     * @param $id 患者id
     * @return array|\yii\db\ActiveRecord[] 获取用户就诊记录信息
     * @throws NotFoundHttpException
     *
     */
    public static function findTriageRecord($id, $makeup = 1) {
        if (is_null($id) || !is_numeric($id)) {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
        /* 这里是获取就诊信息的方法 */
        $query = new Query();
        $query->from(['c' => PatientRecord::tableName()]);
        $query->select([
            'a.spot_id', 'a.temperature_type', 'a.meditation_allergy', 'a.food_allergy', 'a.temperature', 'a.breathing',
            'a.pulse', 'a.shrinkpressure', 'a.diastolic_pressure', 'a.oxygen_saturation', 'a.pain_score', 'a.fall_score', 'triage_remark' => 'a.remark',
            'a.treatment_type', 'a.treatment', 'a.diagnosis_time', 'c.type', 'e.spot_name',
            'd.username', 'd.id', 'a.incidence_date', 'o.chiefcomplaint', 'o.historypresent', 'o.pasthistory',
            'o.personalhistory', 'o.genetichistory', 'o.physical_examination', 'o.remark', 'a.examination_check',
            'f.pastdraghistory', 'f.followup',
            'recordId' => 'c.id', 'a.cure_idea', 'c.case_id', 'c.makeup', 'a.heightcm', 'a.weightkg', 'a.bloodtype',
            'a.blood_type_supplement', 'a.head_circumference', 'a.doctor_id',
            'file_id' => 'group_concat(m.id)', 'file_url' => 'group_concat(m.file_url)',
            'file_name' => 'group_concat(m.file_name)', 'size' => 'group_concat(m.size)',
            'd.birthday', 'd.sex', 'q.appearance', 'q.appearance_remark', 'q.skin', 'q.skin_remark', 'q.headFace', 'q.headFace_remark',
            'q.eye', 'q.eye_remark', 'q.ear', 'q.ear_remark', 'q.nose', 'q.nose_remark',
            'q.throat', 'q.throat_remark', 'q.tooth', 'q.tooth_remark', 'q.chest', 'q.chest_remark', 'q.bellows', 'q.bellows_remark',
            'q.cardiovascular', 'q.cardiovascular_remark', 'q.genitals', 'q.genitals_remark', 'q.back', 'q.back_remark',
            'q.limb', 'q.limb_remark', 'q.nerve', 'q.nerve_remark', 'q.belly', 'q.belly_remark',
            'n.bregmatic', 'n.jaundice',
            'p.result as growthResult', 'p.remark as growthRemark',
            'g.communicate', 'g.coarse_action', 'g.fine_action', 'g.solve_problem',
            'g.personal_society', 'g.score', 'g.evaluation_result', 'g.other_evaluation_type',
            'g.other_evaluation_result', 'g.summary', 'g.summary_remark', 'g.evaluation_diagnosis',
            'g.evaluation_guidance', 'g.evaluation_type_result',
            'second_department_name' => 's.name',
            'doctor_name' => 't.username',
            'reportTime' => 'r.create_time', 'r.type_description', 'r.record_type',
            'dental_type' => 'i.type', 'dental_chiefcomplaint' => 'i.chiefcomplaint',
            'dental_historypresent' => 'i.historypresent', 'dental_pasthistory' => 'i.pasthistory',
            'i.advice', 'i.remarks', 'i.returnvisit',
            'j.sleep', 'j.shit', 'j.pee', 'j.visula_check', 'j.hearing_check', 'j.feeding_patterns', 'j.feeding_num', 'j.substitutes', 'j.dietary_supplement',
            'j.food_types', 'j.inspect_content',
            //正畸复诊
            'orthReturnvisit'=>'k.returnvisit', 'k.check', 'orthTreatment'=>'k.treatment',
            //口腔正畸初诊病历
            'orthChiefcomplaint'=>'t1.chiefcomplaint', 't1.motivation', 'orthHistorypresent'=>'t1.historypresent', 't1.all_past_history', 'orthPastdraghistory'=>'t1.pastdraghistory', 'recordRetention' => 't1.retention', 't1.early_loss', 't1.bad_habits', 't1.bad_habits_abnormal', 't1.bad_habits_abnormal_other',
            't1.traumahistory', 't1.feed', 't1.immediate', 't1.oral_function', 't1.oral_function_abnormal', 't1.mandibular_movement', 't1.mandibular_movement_abnormal', 't1.mouth_open', 't1.mouth_open_abnormal', 't1.left_temporomandibular_joint',
            't1.left_temporomandibular_joint_abnormal', 't1.left_temporomandibular_joint_abnormal_other', 't1.right_temporomandibular_joint', 't1.right_temporomandibular_joint_abnormal', 't1.right_temporomandibular_joint_abnormal_other',
            //口腔正畸初诊病历关联口腔组织检查
            't2.hygiene', 't2.periodontal', 't2.ulcer', 't2.gums', 't2.tonsil', 't2.frenum', 't2.soft_palate', 't2.lip', 't2.tongue', 't2.dentition', 't2.arch_form', 't2.arch_coordination', 't2.overbite_anterior_teeth',
            't2.overbite_anterior_teeth_abnormal', 't2.overbite_anterior_teeth_other', 't2.overbite_posterior_teeth', 't2.overbite_posterior_teeth_abnormal', 't2.overbite_posterior_teeth_other', 't2.cover_anterior_teeth', 't2.cover_anterior_teeth_abnormal', 't2.cover_posterior_teeth',
            't2.cover_posterior_teeth_abnormal', 't2.left_canine', 't2.right_canine', 't2.left_molar', 't2.right_molar', 't2.midline_teeth', 't2.midline_teeth_value', 't2.midline', 't2.cover_posterior_teeth', 't2.midline_value',
            //口腔正畸初诊病历关联全身状态与颜貌信息表
            't3.dental_age', 't3.bone_age', 't3.second_features', 't3.frontal_type', 't3.symmetry', 't3.abit', 't3.face', 't3.smile', 't3.smile_other', 't3.upper_lip', 't3.lower_lip',
            't3.side', 't3.nasolabial_angle', 't3.chin_lip', 't3.mandibular_angle', 't3.upper_lip_position', 't3.lower_lip_position', 't3.chin_position',
            //口腔正畸初诊病历关联模型检查t
            't4.crowded_maxillary', 't4.crowded_mandible', 't4.canine_maxillary', 't4.canine_mandible', 't4.molar_maxillary', 't4.molar_mandible', 't4.spee_curve', 't4.transversal_curve', 't4.bolton_nterior_teeth', 't4.bolton_all_teeth', 't4.examination',
            //口腔正畸初诊病历关联牙齿检查
            't5.dental_caries', 't5.reverse', 't5.impacted', 't5.ectopic', 't5.defect', 't5.retention', 't5.repair_body', 't5.other', 't5.other_remark', 't5.orthodontic_target', 't5.cure', 't5.special_risk'
        ]);
        $query->leftJoin(['a' => TriageInfo::tableName()], '{{c}}.id={{a}}.record_id');
        $query->leftJoin(['r' => Report::tableName()], '{{c}}.id = {{r}}.record_id');
        $query->leftJoin(['d' => Patient::tableName()], '{{d}}.id={{c}}.patient_id');
        $query->leftJoin(['e' => Spot::tableName()], '{{e}}.id={{c}}.spot_id');
        $query->leftJoin(['m' => MedicalFile::tableName()], '{{c}}.id = {{m}}.record_id');
        $query->leftJoin(['f' => TriageInfoRelation::tableName()], '{{c}}.id = {{f}}.record_id');
        $query->leftJoin(['o' => OutpatientRelation::tableName()], '{{c}}.id = {{o}}.record_id');
        $query->leftJoin(['g' => ChildExaminationAssessment::tableName()], '{{c}}.id={{g}}.record_id');
        $query->leftJoin(['p' => ChildExaminationGrowth::tableName()], '{{c}}.id={{p}}.record_id');
        $query->leftJoin(['q' => ChildExaminationCheck::tableName()], '{{c}}.id={{q}}.record_id');
        $query->leftJoin(['n' => ChildExaminationBasic::tableName()], '{{c}}.id={{n}}.record_id');
        $query->leftJoin(['h' => Appointment::tableName()], '{{c}}.id = {{h}}.record_id');
        $query->leftJoin(['s' => SecondDepartment::tableName()], '{{r}}.second_department_id = {{s}}.id');
        $query->leftJoin(['t' => User::tableName()], '{{r}}.doctor_id = {{t}}.id');
        $query->leftJoin(['i' => DentalHistory::tableName()], '{{c}}.id = {{i}}.record_id');
        $query->leftJoin(['j' => ChildExaminationInfo::tableName()], '{{c}}.id = {{j}}.record_id');
        $query->leftJoin(['k' => OrthodonticsReturnvisitRecord::tableName()], '{{c}}.id = {{k}}.record_id');
        $query->leftJoin(['t1' => OrthodonticsFirstRecord::tableName()], '{{c}}.id = {{t1}}.record_id');
        $query->leftJoin(['t2' => OrthodonticsFirstRecordExamination::tableName()], '{{c}}.id = {{t2}}.record_id');
        $query->leftJoin(['t3' => OrthodonticsFirstRecordFeatures::tableName()], '{{c}}.id = {{t3}}.record_id');
        $query->leftJoin(['t4' => OrthodonticsFirstRecordModelCheck::tableName()], '{{c}}.id = {{t4}}.record_id');
        $query->leftJoin(['t5' => OrthodonticsFirstRecordTeethCheck::tableName()], '{{c}}.id = {{t5}}.record_id');
        $query->where(['c.patient_id' => $id, 'c.status' => 5]);

        if ($makeup == 2) {
            $query->andWhere(['c.makeup' => $makeup]);
        }
        $query->orderBy(['a.diagnosis_time' => SORT_DESC]);
        $query->groupBy('c.id');

        $result = $query->all();

        return $result;
    }

    /**
     * @return 返回患者基本信息
     * @param 就诊流水id $record_id
     */
    public static function getPatientName($record_id) {

        $query = new Query();
        $query->from(['a' => PatientRecord::tableName()]);
        $query->select(['b.username']);
        $query->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id');
        $query->where(['a.id' => $record_id, 'a.spot_id' => self::$staticSpotId]);
        return $query->one();
    }

    /**
     * 
     */
    public static function patientInfo($fields = '*', $where) {
        return self::find()->select($fields)->where(['spot_id' => self::$staticParentSpotId])->andFilterWhere($where)->asArray()->one();
    }

    /**
     * @return 返回患者库患者的预约信息
     * @param 患者id $id
     */
    public static function getPatientAppointment($id, $pageSize = 10) {
        if (is_null($id) || !is_numeric($id)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        /* 这里是获取患者预约信息的方法 */
        $query = new ActiveQuery(Appointment::className());
        $allergy_list = [
            'allergy1' => Patient::$allergy1,
            'allergy2' => Patient::$allergy2,
            'allergy3' => Patient::$allergy3,
        ];
        $query->from(['a' => Appointment::tableName()]);
        $query->select([
            'appointmentId' => 'a.id', 'appointmentType' => 'c.type', 'appointmentDepartment' => 'b.name', 'doctorName' => 'g.username', 'appointmentTime' => 'a.time',
        ]);
        $query->leftJoin(['c' => PatientRecord::tableName()], '{{a}}.record_id={{c}}.id');
        $query->leftJoin(['d' => Patient::tableName()], '{{d}}.id={{c}}.patient_id');
        $query->leftJoin(['b' => SecondDepartment::tableName()], '{{b}}.id={{a}}.second_department_id');
        $query->leftJoin(['g' => User::tableName()], '{{g}}.id={{a}}.doctor_id');
        $query->where(['a.patient_id' => $id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['a.id' => SORT_DESC],
                'attributes' => ['a.id']
            ]
        ]);
        return $dataProvider;
    }

    /**
     * @param $allergyData
     * @return 返回患者库患者的过敏信息
     */
//     public static function getAllergyStr($allergyData) {
//         $allergyStr = '';
//         if ($allergyData && $allergyData['allergy']) {
//             $allergyArr = [];
//             $allergy = json_decode($allergyData['allergy'], true);
//             $allergySource = ArrayHelper::map(Patient::$allergy1, 'id', 'name');
//             $allergyReaction = ArrayHelper::map(Patient::$allergy2, 'id', 'name');
//             $allergyDegree = ArrayHelper::map(Patient::$allergy3, 'id', 'name');
//             foreach ($allergy as $v) {
//                 $str = '';
//                 $str.=$v['source'] ? $allergySource[$v['source']] : '';
//                 $str.=$v['reaction'] ? ',表现为:' . $allergyReaction[$v['reaction']] : '';
//                 $str.=$v['degree'] ? ',' . $allergyDegree[$v['degree']] : '';
//                 $allergyArr[] = $str;
//             }
//             $allergyStr = implode(' / ', $allergyArr);
//         }
//         return $allergyStr;
//     }
//     public function validatePhone($attribute, $params) {
//         if (count($this->family_iphone) > 0) {
//             foreach ($this->family_iphone as $key => $v) {
//                 if (!preg_match("/^\d{11}$/", $v)) {
//                     $this->addError('family_iphone', '手机号码是无效的。');
//                 }
//             }
//         } else {
//             $this->addError($attribute, '手机号码不能为空。');
//         }
//     }


    public function validateHourMin($attribute, $params) {
        if (!$this->hasErrors()) {
            $birthday = $this->birthTime . ' ' . $this->hourMin;
            if (strtotime($birthday) > time()) {
                $this->addError($attribute, '出生时间的值必须不大于当前时间');
            }
        }
    }

//    /**
//     * @param $id
//     * @return array|\yii\db\ActiveRecord[]
//     *  这里是获取患者关键体征信息的方法
//     */
//    public static function getPatientSignInfo($id){
//        $query = new ActiveQuery(TriageInfo::className());
//        $query->from(['a' => TriageInfo::tableName()]);
//        $query->select([
//           'a.temperature_type','a.temperature','a.breathing','a.pulse','a.shrinkpressure','a.diastolic_pressure','a.oxygen_saturation','a.pain_score'
//        ]);
//        $query->leftJoin(['c' => PatientRecord::tableName()], '{{a}}.record_id={{c}}.id');
//        $query->leftJoin(['d' => Patient::tableName()], '{{d}}.id={{c}}.patient_id');
//        $query->where(['d.id'=>$id,'c.status'=>5]);
//        $query->orderBy(['d.id' => SORT_DESC,]);
//        $patientSignInfo = $query->asArray()->all();
//        return $patientSignInfo;
//    }

    /**
     *
     * @param 就诊流水ID $id
     * @return 返回患者基本信息
     */
    public static function getUserInfo($id) {
        $query = new Query();
        $query->from(['a' => PatientRecord::tableName()]);
        $query->select(['a.patient_id', 'b.username', 'b.patient_number', 'b.iphone', 'b.sex', 'b.head_img', 'b.birthday', 'a.update_time', 'a.makeup', 'd.type_description', 'c.diagnosis_time', 'a.case_id', 'department_name' => 'e.name', 'a.end_time', 'c.temperature_type', 'c.temperature', 'c.weightkg','c.heightcm']);
        $query->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id');
        $query->leftJoin(['d' => Report::tableName()], '{{a}}.id = {{d}}.record_id');
        $query->leftJoin(['c' => TriageInfo::tableName()], '{{a}}.id = {{c}}.record_id');
        $query->leftJoin(['e' => SecondDepartment::tableName()], '{{d}}.second_department_id = {{e}}.id');
        $query->where(['a.id' => $id, 'a.spot_id' => self::$staticSpotId]);
        $result = $query->one();

        $result['birth'] = $result['birthday'] ? date("Y-m-d", $result['birthday']) : ''; //格式化出生日期
        $result['birthtime'] = $result['birthday']; //没转换的出生日期
        $result['birthday'] = self::dateDiffage($result['birthday']);
        $result['diagnosis_time_timestamp'] = $result['diagnosis_time'];
        $result['diagnosis_time'] = date('Y-m-d', $result['diagnosis_time']); //病历打印发病日期
        $result['receive_sex'] = Patient::$getSex[$result['sex']];
        $result['receive_type'] = $result['type_description'];
        return $result;
    }

    /**
     *
     * @param 患者疼痛评分 $pain_score
     * @param 患者跌倒评分 $fall_score
     * @return 返回患者评分警告字符串
     */
    public static function getUserScore($pain_score, $fall_score) {
        $i = '';
        if ($pain_score >= 4 || $fall_score >= 12) { // 高于警告值时显示
            $pain_score >= 4 ? $showValue = '<div style=\'margin-bottom: 8px\'>疼痛高危：<span class=\'text-red-mine\'>' . $pain_score . '</span></div>' : $showValue = '';
            $fall_score >= 12 ? $showValue .= '<div style=\'margin-bottom: 8px\'>跌倒高危：<span class=\'text-red-mine\'>' . $fall_score . '</span></div>' : $showValue .= '';
            $i = '<i class="fa fa-warning text-red-mine ml-9" data-toggle="tooltip" data-html="true" data-placement="bottom" data-original-title="' . $showValue . '"></i>';
        }
        return $i;
    }

    /**
     *
     * @param 该患者所属会员卡列表 $cardInfos
     * @return 返回会员卡html字符串
     */
    public static function getUserVipInfo($cardInfos, $count = 5, $type = 2) {
        if (!empty($cardInfos)) {
            $showValue = '';
            $num = 0;
            $placement = ($type == 1) ? 'top' : 'bottom';
            foreach ($cardInfos as $card) {
                $name = $card['name'];
                if ($count == $num) {  // 超过限定卡数打点
                    $showValue .= '<div style=\'margin-top: -5px;margin-bottom:12px\'>......</div>';
                    break;
                } else {
                    $showValue .= '<div style=\'margin-bottom: 8px;word-break:break-all\'>' . Html::encode(Html::encode($name)) . '【余额: <span style=\'color:#76A6EF\'>' . $card['total_fee'] . '</span> 元】</div>';
                }
                $num++;
            }
            $i = '<span class="vip-span"><i class="fa fa-vimeo text-yellow-mine ml-9" data-toggle="tooltip" data-html="true" data-placement="' . $placement . '" data-original-title="' . $showValue . '"></i></span>';
        } else {
            $i = '';
        }
        return $i;
    }

    /**
     *
     * @param type $firstRecord 是否为新记录
     * @return type text
     */
    public static function getFirstRecord($firstRecord) {
        $iconUrl = Yii::$app->request->baseUrl . '/public/img/common/icon_new.png';
        $text = $firstRecord == 1 ? '<img class="icon_new_record" src="' . $iconUrl . '">' : '';
        return $text;
    }

    /**
     * @param $height 患者身高
     * @param $weightkg 患者体重
     * @return int|null|string 返回bmi
     */
    public static function getBmi($height, $weightkg) {
        if ($height && $height != 0) {
            $bmi = sprintf('%.2f', round($weightkg / ($height / 100 * $height / 100), 2));
        } else {
            $bmi = $height == null ? null : 0;
        }
        return $bmi;
    }

    /**
     * 
     * @param object $model 患者Model
     * @param object $type  类型 1/老用户 2/新用户
     * @return 获取相似患者列表 
     */
    public static function similarPatient($model, $type = 1) {
        $query = Patient::find()->select(['id', 'patient_number', 'username', 'sex', 'birthday', 'iphone']);
        $birthdayBegin = strtotime($model->birthTime);
        $birthdayEnd = strtotime($model->birthTime) + 86400 - 1;
        if ($type == 1) {//老
            $query->where('spot_id=:spot_id AND patient_number!=0000000 ', [':spot_id' => self::$staticParentSpotId])
                    ->andWhere('sex=:sex AND birthday BETWEEN :birthdayBegin AND :birthdayEnd AND (username=:username OR iphone=:iphone) ', [':sex' => $model->sex, ':birthdayBegin' => $birthdayBegin, ':birthdayEnd' => $birthdayEnd, ':username' => $model->username, ':iphone' => $model->iphone])
                    ->andFilterWhere(['!=', 'patient_number', $model->patient_number]);
        } else {//新
            $query->where('spot_id=:spot_id AND patient_number!=0000000 ', [':spot_id' => self::$staticParentSpotId])
                    ->andWhere('(username=:username) OR (iphone=:iphone AND birthday BETWEEN :birthdayBegin AND :birthdayEnd AND sex=:sex AND username!=:username)', [':username' => $model->username, ':iphone' => $model->iphone, ':birthdayBegin' => $birthdayBegin, ':birthdayEnd' => $birthdayEnd, ':sex' => $model->sex]);
        }
        $list = $query->asArray()->all();
        return $list;
    }

    /**
     *
     * @param object $phone 患者手机
     * @return 获取患者家庭成员信息
     */
    public static function findFamilyInfo($phone) {
        $query = new Query();
        $query->from(['a' => Patient::tableName()]);
        $query->leftJoin(['b' => PatientFamily::tableName()], '{{a}}.id = {{b}}.patient_id');
        $query->select(['a.id', 'a.username', 'a.sex', 'a.birthday', 'a.iphone', 'b.name', 'b.card']);
        $query->where([ 'a.spot_id' => self::$staticParentSpotId]);
        $query->andWhere(['like', 'a.iphone', $phone]);
        $query->orderBy(['a.id' => SORT_DESC]);
        $result = $query->all();

        foreach ($result as $k => $v) {
            $result[$k]['birthday'] = self::dateDiffage($v['birthday']);
            $result[$k]['sex'] = Patient::$getSex[$v['sex']];
        }

        return $result;
    }
    /**
     * 
     * @param integer $id 患者ID
     * @param string|array $fields 查询字段
     * @param string|array $where 查询条件
     * @return \yii\db\ActiveRecord|array|NULL 返回该患者对应的基本信息
     */
    public static function getPatientInfo($id,$fields = '*',$where = []){
        return self::find()->select($fields)->where(['id' => $id])->andFilterWhere($where)->asArray()->one();
    }

}
