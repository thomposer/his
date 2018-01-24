<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%child_examination_assessment}}".
 * 儿童体检-发育评估
 * @property integer $id
 * @property integer $spot_id
 * @property integer $record_id
 * @property integer $communicate
 * @property integer $coarse_action
 * @property integer $fine_action
 * @property integer $solve_problem
 * @property integer $personal_society
 * @property string $score
 * @property string $evaluation_result
 * @property string $other_evaluation_type
 * @property string $other_evaluation_result
 * @property string $evaluation_type_result
 * @property integer $summary
 * @property string $summary_remark
 * @property integer $create_time
 * @property integer $update_time
 */
class ChildExaminationAssessment extends \app\common\base\BaseActiveRecord
{

    public $assessmentAge; //测评年龄

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%child_examination_assessment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'record_id'], 'required'],
            [['spot_id', 'record_id', 'communicate', 'coarse_action', 'fine_action', 'solve_problem', 'personal_society','evaluation_result', 'summary', 'create_time', 'update_time'], 'integer'],
//            [['score'], 'string','max' => 16],
            [['score'],'number','max'=>1000,'min'=>0],
            [['evaluation_type_result','evaluation_diagnosis','evaluation_guidance','other_evaluation_result'],'string','max' => 1000],
            [['other_evaluation_type', 'summary_remark'], 'string', 'max' => 64],
            [['communicate', 'coarse_action', 'fine_action', 'solve_problem', 'personal_society', 'summary','evaluation_result'], 'default','value' => 0],
            [['score','other_evaluation_type','other_evaluation_result','summary_remark'], 'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '自增id',
            'spot_id' => '诊所id',
            'record_id' => '就诊流水id',
            'communicate' => '1.沟通',
            'coarse_action' => '2.动作粗大',
            'fine_action' => '3.精细动作',
            'solve_problem' => '4.解决问题',
            'personal_society' => '5.个人-社会',
            'score' => '儿童得分',
            'evaluation_result' => '评估结果',
            'other_evaluation_type' => '评估方式',
            'other_evaluation_result' => '评估结果',
            'evaluation_type_result' => '评估方式及结果',
            'evaluation_diagnosis' => '初步诊断',
            'evaluation_guidance' => '指导意见',
            'summary' => '总结',
            'summary_remark' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'assessmentAge' => '测评年龄'
        ];
    }

    /**
     *
     * @var type 沟通
     */
    public static $getCommunicate = [
//        0 => '未查',
        1 => '高于界值',
        2 => '接近界值',
        3 => '低于界值'
    ];

    /**
     *
     * @var type 动作粗大
     */
    public static $getCoarseAction = [
//        0 => '未查',
        1 => '高于界值',
        2 => '接近界值',
        3 => '低于界值'
    ];

    /**
     *
     * @var type 精细动作
     */
    public static $getFineAction = [
//        0 => '未查',
        1 => '高于界值',
        2 => '接近界值',
        3 => '低于界值'
    ];

    /**
     *
     * @var type 解决问题
     */
    public static $getAolveProblem = [
//        0 => '未查',
        1 => '高于界值',
        2 => '接近界值',
        3 => '低于界值'
    ];

    /**
     * 个人-社会
     */
    public static $getPersonalSociety = [
//        0 => '未查',
        1 => '高于界值',
        2 => '接近界值',
        3 => '低于界值'
    ];
    /**
     * 评估结果
     * @var array
     */
    public static $getEvaluationResult = [
//        0 => '未查',
        1 => '高于界值',
        2 => '接近界值',
        3 => '低于界值'
    ];
    /**
     * 发育评估总结
     * @var array
     */
    public static $getSummary = [
//        0 => '未查',
        1 => '正常',
        2 => '随访',
        3 => '转诊'
    ];
    

}
