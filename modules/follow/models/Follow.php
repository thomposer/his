<?php

namespace app\modules\follow\models;

use Yii;
use yii\db\Query;
use app\modules\patient\models\Patient;

/**
 * This is the model class for table "{{%follow}}".
 * @property string $id
 * @property string $record_id
 * @property string $patient_id
 * @property string $spot_id
 * @property string $complete_time
 * @property integer $execute_role
 * @property string $plan_creator
 * @property string $content
 * @property string $follow_executor
 * @property integer $follow_method
 * @property integer $follow_result
 * @property string $follow_remark
 * @property integer $follow_state
 * @property string $cancel_user
 * @property string $cancel_reason
 * @property string $create_time
 * @property string $update_time
 */
class Follow extends \app\common\base\BaseActiveRecord
{

    public $patientNumber; //病历号
    public $planCreatorName; //随访创建人
    public $cancelUserName; //取消操作人
    public $followExecutorName; //随访执行人
    public $followPlanExecutorName; //随访计划执行人
    public $sex;
    public $birthday;
    public $username;
    public $spot_name;
    public $iphone; //患者手机号
    public $follow_begin_time; //截止开始时间
    public $follow_end_time; //截止结束时间
    public $diagnosis_begin_time; //接诊开始时间
    public $diagnosis_end_time;//接诊结束时间
    public $diagnosis_time;//接诊时间

    public function init() {
        parent ::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%follow}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['record_id', 'patient_id', 'spot_id', 'execute_role', 'plan_creator', 'follow_executor', 'follow_plan_executor', 'follow_method', 'follow_result', 'follow_state', 'cancel_user'], 'integer'],
            [['content', 'complete_time', 'execute_role'], 'required', 'on' => 'createFollow'],
            [['follow_method', 'follow_state', 'follow_remark'], 'required', 'on' => 'executeFollow'],
            [['cancel_reason'], 'required', 'on' => 'cancelFollow'],
            [['content', 'follow_remark', "spot_name"], 'string', 'max' => 3000],
            [['cancel_reason'], 'string', 'max' => 300],
            [['follow_plan_executor'], 'default', 'value' => 0],
            ['complete_time', 'date', 'min' => date('Y-m-d')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'record_id' => '就诊流水ID',
            'patient_id' => '患者信息',
            'spot_id' => '诊所ID',
            'complete_time' => '截止时间',
            'execute_role' => '计划执行角色',
            'plan_creator' => '创建人',
            'planCreatorName' => '创建人',
            'diagnosis_time' => '接诊时间',
            'diagnosis_begin_time' => '接诊开始时间',
            'diagnosis_end_time' => '接诊结束时间',
            'cancelUserName' => '取消随访人员',
            'followExecutorName' => '执行人',
            'followPlanExecutorName' => '计划执行人',
            'content' => '随访内容',
            'follow_executor' => '执行人',
            'follow_plan_executor' => '计划执行人',
            'follow_method' => '随访方式',
            'follow_result' => '随访结果',
            'follow_remark' => '详情',
            'follow_state' => '随访结果',
            'cancel_user' => '取消操作人',
            'cancel_reason' => '取消原因',
            'create_time' => '创建时间',
            'cancel_time' => '取消随访时间',
            'update_time' => 'Update Time',
            'patientNumber' => '病历号',
            'username' => '患者姓名',
            'iphone' => '患者手机号',
            'follow_begin_time' => '截止开始时间',
            'follow_end_time' => '截止结束时间',
            'spot_name' => '诊所',
            'follow_state' => '状态'
        ];
    }

    public static $getExecuteRole = [
        2 => '医生',
        3 => '护士',
        10 => '健康顾问',
    ];
    //1/待随访 2/成功 3/失败 4/取消',
    public static $getFollowState = [
        1 => '待随访',
        2 => '已随访',
        4 => '已取消',
    ];
    public static $getFollowMethod = [
        1 => '电话',
        2 => '微信',
        3 => '短信',
        4 => '面对面',
        5 => '医信医生',
        6 => '其他'
    ];
    public static $getFollowResult = [
        2 => '随访成功',
        3 => '随访失败'
    ];

    public function scenarios() {
        $parent = parent ::scenarios();
        $parent['createFollow'] = ['complete_time', 'execute_role', 'content', 'follow_plan_executor'];
        $parent['executeFollow'] = ['follow_method', 'follow_state', 'follow_remark'];
        $parent['cancelFollow'] = ['cancel_reason'];

        return $parent;
    }

    public function beforeSave($insert) {
        if ($insert) {
            $this->plan_creator = Yii ::$app->user->identity->id;
        }
        if ($this->scenario == 'executeFollow') {
            $this->follow_executor = Yii ::$app->user->identity->id;
        }
        if ($this->scenario == 'cancelFollow') {
            $this->cancel_user = Yii ::$app->user->identity->id;
            $this->cancel_time = time();
        }
        $this->complete_time = strstr($this->complete_time, '-') ? strtotime($this->complete_time) : $this->complete_time;

        return parent ::beforeSave($insert);
    }

    /**
     * @property 获取医生门诊附件
     * @param record_id
     * @return array 获取医生门诊附件
     */
    public static function findFollowFile($id) {
        $query = new Query();
        $query->from(['m' => FollowFile ::tableName()]);
        $query->select(['file_id' => 'group_concat(m.id)', 'file_url' => 'group_concat(m.file_url)', 'file_name' => 'group_concat(m.file_name)', 'size' => 'group_concat(m.size)']);
        $query->where(['m.follow_id' => $id, 'm.spot_id' => self ::$staticSpotId]);
        $data = $query->one();

        return $data;
    }

    /**
     * @param type $recordId 就诊ID
     * @return 根据就诊ID获取随访信息
     */
    public static function getFollowByRecord($recordId) {
        $data = self ::find()->select(['id', 'record_id', 'complete_time', 'follow_state'])->where(['record_id' => $recordId, 'spot_id' => self ::$staticSpotId])->indexBy('record_id')->asArray()->all();

        return $data;
    }

    /**
     * @param type $recordId 就诊ID
     * @return 根据就诊ID获取随访记录
     */
    public static function recordCount($recordId) {
        $data = self ::find()->select(['id'])->where(['record_id' => $recordId])->one();

        return $data;
    }

    public static function patientInfo($id) {
        $data = (new Query())->from(['a' => self::tableName()])
                        ->select(['a.patient_id', 'b.username', 'b.iphone', 'b.mommyknows_account'])
                        ->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id={{b}}.id')
                        ->where(['a.id' => $id])->one();
        return $data;
    }

}
