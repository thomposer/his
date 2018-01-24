<?php

namespace app\modules\outpatient\models;

use Yii;
use yii\helpers\Json;
use yii\db\Query;
use app\modules\charge\models\ChargeInfo;
use Exception;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%check_record}}".
 *
 * @property string $id
 * @property integer $checkName
 * @property string $record_id 就诊记录ID
 * @property string $spot_id 诊所ID
 * @property string $check_id
 * @property string $name 名称
 * @property string $unit 单位
 * @property decimal $price 零售价
 * @property integer $report_time 报告时间
 * @property integer $report_user_id 报告人
 * @property integer $check_in_time  检查中状态变更时间
 * @property integer $check_finish_time 检查完成状态变更时间
 * @property string $description 描述
 * @property string $result 检查结果
 * @property string $tag_id 标签id
 * @property string $create_time
 * @property string $update_time
 */
class CheckRecord extends \app\common\base\BaseActiveRecord
{

    public $check_id;
    public $checkName;
    public $deleted;
    public $billingTime;
    public $pain_score;
    public $fall_score;

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%check_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['record_id', 'spot_id'], 'required'],
            [['billingTime'], 'required', 'on' => 'makeup'],
            [['record_id', 'spot_id', 'create_time', 'update_time', 'report_time', 'report_user_id', 'check_in_time', 'check_finish_time', 'tag_id'], 'integer'],
            ['check_id', 'validateCheckId'],
            [['name'], 'string', 'max' => 64],
            ['unit', 'string', 'max' => 16],
//            [['description', 'result'], 'string', 'max' => 255],
            [['description', 'result'], 'safe'],
            [['price'], 'number'],
            [['price', 'report_time', 'report_user_id', 'check_in_time', 'check_finish_time', 'tag_id', 'package_record_id'], 'default', 'value' => '0'],
            [['checkName', 'deleted'], 'safe']
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['makeup'] = ['record_id', 'check_id', 'spot_id', 'name', 'price', 'unit', 'description', 'deleted', 'result', 'billingTime'];
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
            'check_id' => '影像学检查',
            'checkName' => '新增影像学检查',
            'name' => '影像学检查名称',
            'price' => '零售价',
            'unit' => '单位',
            'deleted' => '删除ID',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'report_time' => '报告时间',
            'report_user_id' => '报告人',
            'description' => '描述',
            'result' => '结论',
            'billingTime' => '开单时间',
            'check_in_time' => '检查中状态变更时间',
            'check_finish_time' => '检查完成状态变更时间',
            'tag_id' => '标签id',
        ];
    }

    public function validateCheckId($attribute, $params) {
        if (count($this->check_id) > 0) {
            $alreadyChargeId = array();
            foreach ($this->check_id as $key => $v) {
                try {
                    $list = Json::decode($v);
                } catch (Exception $e) {
                    $this->addError($attribute, '参数错误');
                }
                if (!preg_match("/^\s*[+-]?\d+\s*$/", $list['id'])) {
                    $this->addError($attribute, '参数错误');
                }
                if (isset($list['isNewRecord']) && 0 == $list['isNewRecord'] && 1 == $this->deleted[$key]) {
                    $alreadyChargeId[] = $list['id'];
                }
            }

            if ($this->scenario != 'makeup') {
                $query = new Query();
                $query->from(['a' => CheckRecord::tableName()]);
                $query->select(['b.id']);
                $query->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.outpatient_id');
                $query->where(['a.spot_id' => $this->spotId, 'b.status' => [1, 2], 'a.id' => $alreadyChargeId, 'b.type' => 2]);
                $result = $query->all();
                if ($result) {
                    $this->addError($attribute, '存在已经收费或退费的项目');
                }
            }
        }
    }

    /**
     * 
     * @param type $id 流水ID
     * @return type 获取影像学检查列表
     */
    public static function getCheckRecordDataProvider($id) {
        return self::find()->select(['id', 'name', 'unit', 'price', 'status', 'description', 'result'])->where(['record_id' => $id, 'spot_id' => self::$staticSpotId])->asArray()->all();
    }

    /* 获取影像学检查记录 */

    public static function getCheckRecord($id) {
        $checkRecord = CheckRecord::find()->select(['name', 'record_id'])->where(['record_id' => $id])->asArray()->all();
        $data = [];
        if (!empty($id) && is_array($id)) {
            foreach ($id as $val) {
                foreach ($checkRecord as $v) {
                    if ($val == $v['record_id']) {
                        $data[$v['record_id']][] = $v;
                        $data[$v['record_id']]['name'] .= $v['name'] . ',';
                    }
                }
            }
        }
        return $data;
    }
    // 得到检查的状态
    public static $getStatus = [
        1 => '已完成',
        2 => '检查中',
        3 => '待检查',
    ];
    /**
     *
     * @param 执行状态 $status
      * @param type 0-医生门诊，1-护士工作台
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
            }
            return $html;
        }
    }

}
