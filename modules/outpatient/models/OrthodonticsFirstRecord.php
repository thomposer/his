<?php

namespace app\modules\outpatient\models;

use app\common\base\BaseActiveRecord;
use app\modules\outpatient\models\AllergyOutpatient;
use app\modules\outpatient\models\FirstCheck;
use app\modules\patient\models\Patient;
use app\modules\spot\models\Spot;
use app\modules\spot_set\models\SpotConfig;
use app\modules\triage\models\TriageInfo;
use yii\db\Query;
use yii\helpers\Html;
use app\modules\patient\models\PatientRecord;

/**
 * This is the model class for table "{{%orthodontics_first_record}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $record_id
 * @property string $chiefcomplaint
 * @property string $motivation
 * @property string $historypresent
 * @property string $all_past_history
 * @property string $pastdraghistory
 * @property string $retention
 * @property string $early_loss
 * @property integer $bad_habits
 * @property string $bad_habits_abnormal
 * @property string $bad_habits_abnormal_other
 * @property string $traumahistory
 * @property integer $feed
 * @property string $immediate
 * @property integer $oral_function
 * @property string $oral_function_abnormal
 * @property integer $mandibular_movement
 * @property string $mandibular_movement_abnormal
 * @property integer $mouth_open
 * @property string $mouth_open_abnormal
 * @property integer $left_temporomandibular_joint
 * @property string $left_temporomandibular_joint_abnormal
 * @property string $left_temporomandibular_joint_abnormal_other
 * @property integer $right_temporomandibular_joint
 * @property string $right_temporomandibular_joint_abnormal
 * @property string $right_temporomandibular_joint_abnormal_other
 * @property integer $create_time
 * @property integer $update_time
 */
class OrthodonticsFirstRecord extends BaseActiveRecord
{

    public $hasAllergy;

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%orthodontics_first_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'record_id', 'chiefcomplaint', 'motivation', 'historypresent', 'traumahistory', 'immediate', 'bad_habits', 'feed', 'oral_function', 'mandibular_movement', 'mouth_open', 'left_temporomandibular_joint', 'right_temporomandibular_joint', 'hasAllergy'], 'required'],
            [['spot_id', 'record_id', 'bad_habits', 'oral_function', 'mandibular_movement', 'mouth_open', 'left_temporomandibular_joint', 'right_temporomandibular_joint', 'create_time', 'update_time'], 'integer'],
            [['chiefcomplaint', 'motivation', 'historypresent', 'all_past_history', 'pastdraghistory', 'traumahistory', 'immediate'], 'string', 'max' => '1000'],
            [['retention', 'early_loss'], 'string', 'max' => 125],
            [['mandibular_movement_abnormal', 'mouth_open_abnormal', 'left_temporomandibular_joint_abnormal_other', 'right_temporomandibular_joint_abnormal_other'], 'string', 'max' => 30],
            [['bad_habits_abnormal_other'], 'string', 'max' => 10],
            [['chiefcomplaint', 'motivation', 'historypresent', 'all_past_history', 'pastdraghistory', 'traumahistory', 'immediate', 'bad_habits_abnormal', 'retention', 'early_loss', 'bad_habits_abnormal_other', 'oral_function_abnormal', 'mandibular_movement_abnormal', 'mouth_open_abnormal', 'left_temporomandibular_joint_abnormal', 'left_temporomandibular_joint_abnormal_other', 'right_temporomandibular_joint_abnormal', 'right_temporomandibular_joint_abnormal_other'], 'default', 'value' => ''],
            [['hasAllergy'], 'default', 'value' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'record_id' => '就诊记录ID',
            'chiefcomplaint' => '主诉',
            'motivation' => '动机',
            'historypresent' => '现病史',
            'all_past_history' => '全身既往史',
            'pastdraghistory' => '过去用药史',
            'retention' => '滞留',
            'early_loss' => '早失',
            'bad_habits' => '不良习惯',
            'bad_habits_abnormal' => '有不良习惯',
            'bad_habits_abnormal_other' => '其他不良习惯',
            'traumahistory' => '外伤史',
            'feed' => '喂养方式',
            'immediate' => '直系三代亲属',
            'oral_function' => '口腔功能',
            'oral_function_abnormal' => '口腔功能有异常',
            'mandibular_movement' => '下颌运动',
            'mandibular_movement_abnormal' => '异常内容',
            'mouth_open' => '张口度',
            'mouth_open_abnormal' => '异常内容',
            'left_temporomandibular_joint' => '左颞下颌关节',
            'left_temporomandibular_joint_abnormal' => '左颞下颌关节异常',
            'left_temporomandibular_joint_abnormal_other' => '其他异常内容',
            'right_temporomandibular_joint' => '右颞下颌关节',
            'right_temporomandibular_joint_abnormal' => '右颞下颌关节异常',
            'right_temporomandibular_joint_abnormal_other' => '其他异常内容',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'hasAllergy' => '过敏史'
        ];
    }

    /**
     * @var array 右颞下颌关节异常(1-弹响,2-疼痛,3-其他)
     */
    public static $getRightTemporomandibularJointAbnormal = [
        1 => '弹响',
        2 => '疼痛',
        3 => '其他'
    ];

    /**
     * @var array 右颞下颌关节（1-正常，2-异常）
     * 
     */
    public static $getRightTemporomandibularJoint = [
        1 => '正常',
        2 => '异常'
    ];

    /**
     * 
     * @var array 左颞下颌关节异常(1-弹响,2-疼痛,3-其他)
     */
    public static $getLeftTemporomandibularJointAbnormal = [
        1 => '弹响',
        2 => '疼痛',
        3 => '其他'
    ];

    /**
     * 
     * @var array 左颞下颌关节（1-正常，2-异常）
     */
    public static $getLeftTemporomandibularJoint = [
        1 => '正常',
        2 => '异常'
    ];

    /**
     * 
     * @var array 张口度(1-正常，2-异常)
     */
    public static $getMouthOpen = [
        1 => '正常',
        2 => '异常'
    ];

    /**
     * 
     * @var array 下颌运动（1-正常，2-异常）
     */
    public static $getMandibularMovement = [

        1 => '正常',
        2 => '异常',
    ];

    /**
     * 
     * @var array 口腔功能有异常
     */
    public static $getOralFunctionAbnormal = [
        1 => '口呼吸',
        2 => '偏侧咀嚼（左）',
        3 => '偏侧咀嚼（右）',
        4 => '不良吞咽',
        5 => '发音不清'
    ];

    /**
     *
     * @var array 不良习惯(1-无，2-有)
     */
    public static $getBadHabits = [
        1 => '无',
        2 => '有'
    ];

    /**
     * 
     * @var array 有不良习惯--异常
     */
    public static $getBadHabitsAbnormal = [
        1 => '吮指',
        2 => '咬唇',
        3 => '咬物',
        4 => '吐舌',
        5 => '吸颊',
        6 => '口呼吸',
        7 => '不良吞咽',
        8 => '其他'
    ];

    /**
     * 
     * @var array 喂养方式
     */
    public static $getFeed = [
        1 => '母乳',
        2 => '人工',
        3 => '混合'
    ];

    /**
     * 
     * @var array 口腔功能
     */
    public static $getOralFunction = [
        1 => '无异常',
        2 => '有异常'
    ];

    /**
     * @desc 根据就诊ID获取正畸初诊数据
     * @param type $recordId 就诊流水ID
     * @return Array
     */
    public static function orthodonticsFirstData($recordId) {
        $soptInfo = Spot::getSpot();
        $firstCheck = FirstCheck::getFirstCheckInfo($recordId);
        $allergy = AllergyOutpatient::getAllergyByRecord($recordId);
        $allergy = isset($allergy) ? $allergy[$recordId] : [];
        if (!empty($allergy)) {
            foreach ($allergy as $key => $value) {
                $allergy[$key] = Html::encode($value);
            }
        }
        $result['baseInfo'] = self::getAllOrthodonticsFirstRecord($recordId);
        $userInfo = Patient::getUserInfo($recordId);
        $userInfo['temperature'] = $userInfo['temperature'] ? $userInfo['temperature'] . '℃ - ' . TriageInfo::$temperature_type[$userInfo['temperature_type']] : '';
        $userInfo['weightkg'] = $userInfo['weightkg'] ? $userInfo['weightkg'] : '';
        $userInfo['end_time'] = ($userInfo['end_time'] != 0 ? (date('Y-m-d H时', $userInfo['end_time']) . ((int) date('i', $userInfo['end_time'])) . '分') : '');
        $userInfo['diagnosis_date'] = $userInfo['diagnosis_time']; //接诊日期
        $userInfo['diagnosis_time'] = date('Y-m-d H时', $userInfo['diagnosis_time_timestamp']) . ((int) date('i', $userInfo['diagnosis_time_timestamp'])) . '分'; //接诊时间
        $result['userInfo'] = $userInfo;
        $result['spotInfo'] = Spot::getSpot();
        $result['spotConfig'] = SpotConfig::getConfig(['logo_img', 'pub_tel', 'spot_name', 'logo_shape']);
        $result['firstCheck'] = Html::encode($firstCheck);
        $result['allergy'] = $allergy;
        return $result;
    }

    /**
     * @desc 获取所有正畸初诊关联数据
     * @param type $recordId
     * @return  Array
     */
    public static function getAllOrthodonticsFirstRecord($recordId) {
        $fields = [
            //口腔正畸初诊病历
            't1.chiefcomplaint', 't1.motivation', 't1.historypresent', 't1.all_past_history', 't1.pastdraghistory', 'recordRetention' => 't1.retention', 't1.early_loss', 't1.bad_habits', 't1.bad_habits_abnormal', 't1.bad_habits_abnormal_other',
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
            't5.dental_caries', 't5.reverse', 't5.impacted', 't5.ectopic', 't5.defect', 't5.retention', 't5.repair_body', 't5.other', 't5.other_remark', 't5.orthodontic_target', 't5.cure', 't5.special_risk',
        ];
        $data = (new Query())->from(['t' => PatientRecord::tableName()])
                ->select($fields)
                ->leftJoin(['t1' => self::tableName()], '{{t}}.id = {{t1}}.record_id')
                ->leftJoin(['t2' => OrthodonticsFirstRecordExamination::tableName()], '{{t1}}.record_id = {{t2}}.record_id')
                ->leftJoin(['t3' => OrthodonticsFirstRecordFeatures::tableName()], '{{t1}}.record_id = {{t3}}.record_id')
                ->leftJoin(['t4' => OrthodonticsFirstRecordModelCheck::tableName()], '{{t1}}.record_id = {{t4}}.record_id')
                ->leftJoin(['t5' => OrthodonticsFirstRecordTeethCheck::tableName()], '{{t1}}.record_id = {{t5}}.record_id')
                ->where(['t.id' => $recordId, 't.spot_id' => self::$staticSpotId])
                ->one();
        // t1
        $data['bad_habits'] = self::formatOutHtml($data['bad_habits'], $data['bad_habits_abnormal'], $data['bad_habits_abnormal_other'], self::$getBadHabits, self::$getBadHabitsAbnormal, 8);
        $data['feed'] = self::formatNormal($data['feed'], self::$getFeed); //self::getCheckBoxTextValue($data['feed'], self::$getFeed);
        $data['oral_function'] = self::formatOutHtml($data['oral_function'], $data['oral_function_abnormal'], '', self::$getOralFunction, self::$getOralFunctionAbnormal, 7);
        $data['left_temporomandibular_joint'] = self::formatOutHtml($data['left_temporomandibular_joint'], $data['left_temporomandibular_joint_abnormal'], $data['left_temporomandibular_joint_abnormal_other'], self::$getLeftTemporomandibularJoint, self::$getLeftTemporomandibularJointAbnormal, 3);
        $data['right_temporomandibular_joint'] = self::formatOutHtml($data['right_temporomandibular_joint'], $data['right_temporomandibular_joint_abnormal'], $data['right_temporomandibular_joint_abnormal_other'], self::$getRightTemporomandibularJoint, self::$getRightTemporomandibularJointAbnormal, 3);
        $data['mandibular_movement'] = self::formatOutHtml($data['mandibular_movement'], $data['mandibular_movement_abnormal'], '', self::$getMandibularMovement, [], 999);
        $data['mouth_open'] = self::formatOutHtml($data['mouth_open'], $data['mouth_open_abnormal'], '', self::$getMouthOpen, [], 999);
        //t2
        $data['dentition'] = self::formatNormal($data['dentition'], OrthodonticsFirstRecordExamination::$getDentition);
        $data['arch_form'] = self::formatNormal($data['arch_form'], OrthodonticsFirstRecordExamination::$getArchForm); //OrthodonticsFirstRecordExamination::$getArchForm[$data['arch_form']];
        $data['arch_coordination'] = self::formatNormal($data['arch_coordination'], OrthodonticsFirstRecordExamination::$getArchCoordination); //OrthodonticsFirstRecordExamination::$getArchCoordination[$data['arch_coordination']];
        $data['overbite_anterior_teeth'] = self::formatOutHtml($data['overbite_anterior_teeth'], $data['overbite_anterior_teeth_abnormal'], $data['overbite_anterior_teeth_other'], OrthodonticsFirstRecordExamination::$getOverbiteAnteriorTeeth, OrthodonticsFirstRecordExamination::$getOverbiteAnteriorTeethAbnormal, 3);
        $data['overbite_posterior_teeth'] = self::formatOutHtml($data['overbite_posterior_teeth'], $data['overbite_posterior_teeth_abnormal'], $data['overbite_posterior_teeth_other'], OrthodonticsFirstRecordExamination::$getOverbitePosteriorTeeth, OrthodonticsFirstRecordExamination::$getOverbitePosteriorTeethAbnormal, 3);
        $data['cover_anterior_teeth'] = self::formatOutHtml($data['cover_anterior_teeth'], $data['cover_anterior_teeth_abnormal'], '', OrthodonticsFirstRecordExamination::$getCoverAnteriorTeeth, OrthodonticsFirstRecordExamination::$getCoverAnteriorTeethAbnormal, 4);
        $data['cover_posterior_teeth'] = self::formatOutHtml($data['cover_posterior_teeth'], $data['cover_posterior_teeth_abnormal'], '', OrthodonticsFirstRecordExamination::$getCoverPosteriorTeeth, OrthodonticsFirstRecordExamination::$getCoverPosteriorTeethAbnormal, 4);
        $data['left_canine'] = OrthodonticsFirstRecordExamination::$getLeftCanine[$data['left_canine']];
        $data['right_canine'] = OrthodonticsFirstRecordExamination::$getRightCanine[$data['right_canine']];
        $data['left_molar'] = OrthodonticsFirstRecordExamination::$getLeftMolar[$data['left_molar']];
        $data['right_molar'] = OrthodonticsFirstRecordExamination::$getRightMolar[$data['right_molar']];
        $data['midline_teeth'] = OrthodonticsFirstRecordExamination::$getMidlineTeeth[$data['midline_teeth']];
        $data['midline'] = OrthodonticsFirstRecordExamination::$getMidline[$data['midline']];

        //t3
        $data['second_features'] = OrthodonticsFirstRecordFeatures::$getSecondFeatures[$data['second_features']];
        $data['frontal_type'] = OrthodonticsFirstRecordFeatures::$getFrontalType[$data['frontal_type']];
        $data['symmetry'] = OrthodonticsFirstRecordFeatures::$getSymmetry[$data['symmetry']];
        $data['abit'] = OrthodonticsFirstRecordFeatures::$getAbit[$data['abit']];
        $data['face'] = OrthodonticsFirstRecordFeatures::$getFace[$data['face']];
        $data['smile'] = self::formatOutHtml(2, $data['smile'], $data['smile_other'], [], OrthodonticsFirstRecordFeatures::$getSmile, 3); //OrthodonticsFirstRecordFeatures::$getSmile[$data['smile']];
        $data['upper_lip'] = OrthodonticsFirstRecordFeatures::$getUpperLip[$data['upper_lip']];
        $data['lower_lip'] = OrthodonticsFirstRecordFeatures::$getLowerLip[$data['lower_lip']];
        $data['side'] = OrthodonticsFirstRecordFeatures::$getSide[$data['side']];
        $data['nasolabial_angle'] = OrthodonticsFirstRecordFeatures::$getNasolabialAngle[$data['nasolabial_angle']];
        $data['chin_lip'] = OrthodonticsFirstRecordFeatures::$getChinLip[$data['chin_lip']];
        $data['mandibular_angle'] = OrthodonticsFirstRecordFeatures::$getMandibularAngle[$data['mandibular_angle']];
        $data['upper_lip_position'] = OrthodonticsFirstRecordFeatures::$getUpperLipPosition[$data['upper_lip_position']];
        $data['lower_lip_position'] = OrthodonticsFirstRecordFeatures::$getLowerLipPosition[$data['lower_lip_position']];
        $data['chin_position'] = OrthodonticsFirstRecordFeatures::$getChinPosition[$data['chin_position']];

        //t4
        $data['transversal_curve'] = OrthodonticsFirstRecordModelCheck::$getTransversalCurve[$data['transversal_curve']];
        return $data;
    }

    /**
     * 
     * @param string $data 字段的数据,是以逗号隔开的字符串 
     * @param array $textArray 该字段对应的文本方法数组 例如 self::$getFeed
     * @return string 返回该字段对应的中文描述，以逗号隔开
     */
    public static function getCheckBoxTextValue($data, $textArray) {
        $rows = '';
        if ($data) {
            $value = explode(',', $data);
            if (!empty($value)) {
                foreach ($value as $v) {
                    $rows .= $textArray[$v] . ',';
                }
                $rows = trim(',', $rows);
            }
        }
        return $rows;
    }

    /**
     * 
     * @desc 格式化输出异常状态的数据
     * @param type $normal 正常值
     * @param type $abnormal 异常值
     * @param type $other 其他值
     * @param type $map 正常值map
     * @param type $abnormalMap 异常值map
     * @param type $otherVal 异常选了其他
     * @return String
     */
    public static function formatOutHtml($normal, $abnormal, $other, $map, $abnormalMap, $otherVal = 0) {
        $res = '';
        if ($normal) {
            $abnormalFlag = false;
            if (strpos($map[$normal], '异常') !== false) {
                $abnormalFlag = true;
            }
            if ($normal == 1) {//正常
                $res = $map[$normal];
            } else if (!$abnormalMap) {
                $res = $abnormal;
                if ($abnormalFlag) {
                    $res = $res ? $map[$normal] . '(' . $res . ')' : $map[$normal];
                }
            } else {
                $abnormalArr = explode(',', $abnormal);
                if (!empty($abnormalArr)) {
                    $abnormalStr = [];
                    foreach ($abnormalArr as $val) {
                        if ($val != $otherVal) {
                            $abnormalStr[] = $abnormalMap[$val];
                        } else {
                            $abnormalStr[] = $other;
                        }
                    }
                    $res = implode(',', $abnormalStr);
                }
                if ($abnormalFlag) {
                    $res = $res ? $map[$normal] . '(' . $res . ')' : $map[$normal];
                }
            }
        }
        return Html::encode($res);
    }

    /**
     * @desc 格式化输出 无其他checkbox
     * @param type $data
     * @param type $map
     * @return type
     */
    public static function formatNormal($data, $map) {
        $res = [];
        $dataArr = explode(',', $data);
        if (!empty($dataArr)) {
            foreach ($dataArr as $val) {
                $res[] = isset($map[$val]) ? $map[$val] : '';
            }
        }
        return $res ? implode(',', $res) : '';
    }

    /**
     * @desc 格式化输出 无其他checkbox
     * @param type $data
     * @param type $map
     * @return type
     */
    public static function formatNormalConnect($attr, $info, $map) {
        $res = '';
        if ($attr) {
            $res.=$map[$attr];
            if ($info) {
                $res.='(' . $info . 'mm)';
            }
        }
        return $res;
    }

    public function beforeSave($insert) {

        if (is_array($this->bad_habits_abnormal)) {
            $this->bad_habits_abnormal = implode(',', $this->bad_habits_abnormal);
        }
        if (is_array($this->feed)) {
            $this->feed = implode(',', $this->feed);
        }
        if (is_array($this->oral_function_abnormal)) {
            $this->oral_function_abnormal = implode(',', $this->oral_function_abnormal);
        }
        if (is_array($this->left_temporomandibular_joint_abnormal)) {
            $this->left_temporomandibular_joint_abnormal = implode(',', $this->left_temporomandibular_joint_abnormal);
        }
        if (is_array($this->right_temporomandibular_joint_abnormal)) {
            $this->right_temporomandibular_joint_abnormal = implode(',', $this->right_temporomandibular_joint_abnormal);
        }
        return parent::beforeSave($insert);
    }

}
