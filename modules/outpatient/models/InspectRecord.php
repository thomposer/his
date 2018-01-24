<?php

namespace app\modules\outpatient\models;

use Yii;
use yii\helpers\Json;
use yii\db\Query;
use app\modules\charge\models\ChargeInfo;
use app\modules\outpatient\models\InspectRecordUnion;
use Exception;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%inspect_record}}".
 *
 * @property string $id
 * @property string $record_id
 * @property string $spot_id
 * @property string $name  检验医嘱名称
 * @property string $unit 单位
 * @property integer $price 零售价
 * @property integer $report_time 报告时间
 * @property integer $report_user_id 报告人
 * @property integer $inspect_in_time 检查中状态变更时间
 * @property integer $inspect_finish_time 检查完成状态变更时间
 * @property string $tag_id 标签id
 * @property string $inspect_type 检验类型
 * @property integer $deliver 标本是否外送检验
 * @property integer $specimen_type 标本种类
 * @property integer $cuvette 试管盖颜色
 * @property integer $inspect_id gzh_inspect实验室检查配置表的id
 * @property string $inspect_english_name  检验医嘱英文名称
 * @property string $remark 检验医嘱备注
 * @property string $description 检验医嘱说
 * @property integer $notice_status 通知医生状态(1-已通知，2-未通知)	
 * @property integer $notice_user_id 通知医生--操作人	
 * @property integer $notice_time 点击通知医生-时间	
 * @property integer $handle_status 医生处理状态(1-已处理，2-未处理)	
 * @property integer $handle_time 医生点击处理的时间
 * @property string $create_time
 * @property string $update_time
 */
class InspectRecord extends \app\common\base\BaseActiveRecord
{

    public $deleted;
    public $inspectName;
    public $billingTime;
    public $uuid;
    public $backInspectId;
    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%inspect_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['record_id', 'spot_id'], 'required'],
            [['billingTime'], 'required', 'on' => 'makeup'],
            [['backInspectId'],'required','on' => 'inspectBack'],
            [['record_id', 'spot_id', 'create_time', 'update_time', 'report_time', 'report_user_id', 'inspect_in_time', 'inspect_finish_time', 'tag_id', 'deliver','deliver_organization','specimen_type', 'cuvette','notice_status','notice_user_id','notice_time','handle_status','handle_time'], 'integer'],
            ['inspect_id', 'validateInspectId'],
            [['name', 'inspect_type'], 'string', 'max' => 64],
            [['inspect_english_name', 'remark','description'], 'string', 'max' => 255],
            [['specimen_number'], 'string', 'max' => 32],
            ['unit', 'string', 'max' => 16],
            [['price'], 'number'],
            [['price', 'report_time', 'report_user_id', 'inspect_in_time', 'inspect_finish_time', 'tag_id', 'deliver', 'specimen_type', 'cuvette', 'inspect_id','notice_status','notice_user_id','notice_time','handle_status','handle_time', 'package_record_id'], 'default', 'value' => '0'],
            [['inspectName', 'deleted', 'uuid'], 'safe'],
            [['inspect_type', 'specimen_number'], 'default', 'value' => ''],
            [['deliver'], 'default', 'value' => 2],

        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['makeup'] = ['record_id', 'inspect_id', 'spot_id', 'name', 'price', 'unit', 'price', 'deleted', 'remark', 'recipe_id', 'billingTime', 'uuid'];
        $parent['saveHandle'] = ['handle_status','handle_time'];
        $parent['inspectBack'] = ['backInspectId'];
        return $parent;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'record_id' => '流水ID',
            'spot_id' => '诊所ID',
            'name' => '检验医嘱名称',
            'price' => '零售价',
            'unit' => '单位',
            'deleted' => '删除ID列表',
            'inspectName' => '新增实验室检查',
            'inspect_id' => '检验医嘱',
            'backInspectId' => '检验医嘱',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'report_time' => '报告时间',
            'report_user_id' => '报告人',
            'billingTime' => '开单时间',
            'inspect_in_time' => '检查中时间',
            'inspect_finish_time' => '完成时间',
            'tag_id' => '标签id',
            'deliver' => '标本是否外送检验',
            'specimen_type' => '标本种类',
            'cuvette' => '试管盖颜色',
            'inspect_type' => '检验类型',
            'specimen_number' => '标本编码',
            'notice_status' => '通知医生状态(1-已通知，2-未通知)	',
            'notice_user_id' => '通知医生--操作人',
            'notice_time' => '点击通知医生-时间	',
            'handle_status' => '医生处理状态(1-已处理，2-未处理)	',
            'handle_time' => '医生点击处理的时间',
        ];
    }

    public function validateInspectId($attribute, $params) {
        if (count($this->inspect_id) > 0) {
            $alreadyChargeId = array();
            foreach ($this->inspect_id as $key => $v) {
                try {
                    $list = Json::decode($v);
                } catch (Exception $e) {
                    $this->addError($attribute, '参数错误');
                }
                if (!preg_match("/^\s*[+-]?\d+\s*$/", $list['id'])) {
                    $this->addError($attribute, '参数错误');
                }
            }
            if (isset($list['isNewRecord']) && 0 == $list['isNewRecord'] && 1 == $this->deleted[$key]) {
                $alreadyChargeId[] = $list['id'];
            }

            if ($this->scenario != 'makeup') {
                $query = new Query();
                $query->from(['a' => InspectRecord::tableName()]);
                $query->select(['b.id']);
                $query->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.outpatient_id');
                $query->where(['a.spot_id' => $this->spotId, 'b.status' => [1, 2], 'a.id' => $alreadyChargeId, 'b.type' => 1]);
                $result = $query->all();
                if ($result) {
                    $this->addError($attribute, '存在已经收费或退费的项目');
                }
            }
        }
    }

    /**
     * @param id 流水ID
     * @return 获取已经添加的处方医嘱
     */
    public static function findInspectRecordDataProvider($id) {
        $inspectRecord = InspectRecord::find()->select(['id', 'name', 'unit', 'price', 'status'])->where(['record_id' => $id, 'spot_id' => self::$staticSpotId])->asArray()->all();
        if (!empty($inspectRecord)) {
            foreach ($inspectRecord as &$val) {
                $val['name'] = \yii\helpers\Html::encode($val['name']);
                $val['inspectItem'] = InspectRecordUnion::getInspectItem($val['id']);
            }
        }
        return $inspectRecord;
    }

    /*
     * **获取实验室医嘱记录
     */

    public static function getInspectRecord($id,$status = 0) {
        $itemRecord = InspectRecord::find()->select(['name', 'record_id'])->where(['record_id' => $id])->andWhere(['<>','status', $status])->asArray()->all();
        $data = [];
        if (!empty($id) && is_array($id)) {
            foreach ($id as $val) {
                foreach ($itemRecord as $v) {
                    if ($val == $v['record_id']) {
                        $data[$v['record_id']][] = $v;
                        $data[$v['record_id']]['name'] .= $v['name'] . ',';
                    }
                }
            }
        }
        return $data;
    }

    //医嘱状态
    public static $inspectStatus = [
        1 => '已完成',
        2 => '检验中',
        3 => '待检验',
        4 => '已取消',
    ];
    //医嘱状态颜色
    public static $statusColors = [
        1 => '#76A6EF',
        2 => '#95CA20',
        3 => '#FF5000',
        4 => '#97A3B6',
    ];

    /**
     * @param $record_id 流水id
     * @param string $fields 查询字段
     * @param string $status 状态
     * @return array|\yii\db\ActiveRecord[] 获取实验室检查项的项目
     */
    public static function getInspectList($record_id, $fields = '*',$where = []) {
        $inspectList = self::find()->select($fields)->where(['record_id' => $record_id, 'spot_id' => self::$staticSpotId,'package_record_id' => 0])->andFilterWhere($where)->asArray()->all();
        return $inspectList;
    }

    /**
     *
     * @param 执行状态 $status
     *  * @param type 0-医生门诊，1-护士工作台
     * @return multitype:string 返回class数组样式
     */
    public static function getExecuteStatusOptions($status,$type = 0){
        if($status == 1){
            $color = 'blue';
            $title = '已执行';
        }else if($status == 2){
            $color = 'green';
            $title = '执行中';
        }else if($status == 3){
            $color = 'red';
            $title = '未执行';
        }else if($status == 4) {
            $color = 'blue';
            $title = '已取消';
        }
        $options = [
            'class' => 'fa fa-flag margin-right '. $color,
            'title' => $title,
            'aria-label' => $title,
            'data-toggle' => 'tooltip'
        ];
        if(!$type){
            return $options;
        }else{
            $html = Html::tag('i','',$options);
            if($status == 1){
                $html .= '已执行';
            }else if($status == 2){
                $html .= '执行中';
            }else if($status == 3){
                $html .= '未执行';
            }else{
                $html .= '已取消';
            }
            return $html;
        }
    }

    // 得到检查的状态
    public static $getStatus = [
        1 => '已完成',
        2 => '检查中',
        3 => '待检查',
        4 => '已取消',
    ];

}
