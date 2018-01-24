<?php

namespace app\modules\outpatient\models;

use Yii;
use app\common\base\BaseActiveRecord;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Json;
use app\modules\charge\models\ChargeInfo;
use yii\data\ActiveDataProvider;
use Exception;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%cure_record}}".
 *
 * @property string $id
 * @property string $name //名称
 * @property integer $cureName //新增治疗
 * @property string $unit //单位
 * @property string $record_id
 * @property string $spot_id
 * @property decimal $price
 * @property integer $status
 * @property string $remark //备注
 * @property integer $time
 * @property string $description
 * @property integer $report_time 报告时间
 * @property integer $report_user_id 报告人
 * @property integer $cure_in_time 治疗中状态变更时间
 * @property integer $cure_finish_time 治疗完成状态变更时间 
 * @property string $tag_id 标签id
 * @property string $create_time
 * @property string $update_time
 */
class CureRecord extends BaseActiveRecord
{

    public $cure_id;
    public $cureName;
    public $deleted;
    public $charge_status; //收费状态
    public $billingTime;
    public $totalPrice; // 金额（总额）
    public $pain_score; //疼痛评分
    public $fall_score; //跌倒评分

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%cure_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['record_id', 'spot_id'], 'required'],
            [['billingTime'], 'required', 'on' => 'makeup'],
            [['record_id', 'report_time', 'report_user_id', 'spot_id', 'cure_in_time', 'cure_finish_time', 'create_time', 'update_time', 'tag_id', 'type'], 'integer'],
            ['time', 'validateTime'],
            [['name'], 'string', 'max' => 64],
            [['cure_result'], 'string', 'max' => 10],
            ['unit', 'string', 'max' => 16],
            [['price'], 'number'],
            [['price', 'report_time', 'report_user_id', 'cure_in_time', 'cure_finish_time', 'tag_id', 'type', 'package_record_id'], 'default', 'value' => '0'],
            [['description', 'cure_id', 'deleted', 'package_record_id'], 'safe'],
            [['cure_result'], 'default', 'value' => '']
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['makeup'] = ['record_id', 'cure_id', 'spot_id', 'name', 'price', 'unit', 'price', 'time', 'description', 'deleted', 'remark', 'billingTime'];
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
            'cure_id' => '治疗医嘱',
            'time' => '次数',
            'price' => '零售价（元）',
            'status' => '状态',
            'description' => '说明',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'name' => '项目名称',
            'unit' => '单位',
            'remark' => '备注',
            'cure_result' => '执行结果',
            'cureName' => '新增治疗',
            'report_time' => '报告时间',
            'report_user_id' => '报告人',
            'billingTime' => '开单时间',
            'totalPrice' => '金额（元）',
            'cure_in_time' => '治疗中状态变更时间',
            'cure_finish_time' => '治疗完成状态变更时间',
            'tag_id' => '标签id',
            'type' => '医嘱类型',
        ];
    }

    ///^\s*[+-]?\d+\s*$/
    public function validateTime($attribute, $params) {
        if (count($this->time) > 0) {
            $alreadyChargeId = array();
            $isDeleteId = array();
            foreach ($this->time as $key => $v) {
                try {
                    $list = Json::decode($this->cure_id[$key]);
                } catch (Exception $e) {
                    $this->addError($attribute, '参数错误');
                }
                if (!$this->deleted[$key]) {
                    if (!preg_match("/^\s*[+-]?\d+\s*$/", $v)) {
                        $this->addError($attribute, '次数必须是一个数字');
                    } else if ($v <= 0 || $v > 100) {
                        $this->addError($attribute, '次数必须在1~100范围内');
                    } else if (!isset($this->cure_id[$key])) {
                        $this->addError($attribute, '参数错误');
                    }
                } else {
                    $isDeleteId[] = $list['id'];
                }

                if (isset($list['isNewRecord']) && 0 == $list['isNewRecord']) {//删除项 修改项
                    $alreadyChargeId[] = $list['id'];
                }
            }
            if ($this->scenario != 'makeup') {
                $query = new Query();
                $query->from(['a' => CureRecord::tableName()]);
                $query->select(['b.id']);
                $query->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.outpatient_id');
                $query->where(['a.spot_id' => $this->spotId, 'b.status' => [1, 2], 'a.id' => $alreadyChargeId, 'b.type' => 3]);
                $result = $query->all();
                if ($result) {
                    $this->addError($attribute, '存在已经收费或退费的项目');
                }

                $query = new Query();
                $query->from(['a' => CureRecord::tableName()]);
                $query->select(['a.id']);
                $query->where(['a.spot_id' => $this->spotId, 'a.type' => 1, 'a.id' => $isDeleteId]);
                $result = $query->all();
                if ($result) {
                    $this->addError($attribute, '治疗不可删除');
                }
            }
        } else {
            $this->addError($attribute, '次数不能为空');
        }
    }

    public static function findCureRecordDataProvider($id) {
        $query = new ActiveQuery(CureRecord::className());
        $query->from(['a' => CureRecord::tableName()]);
        $query->select(['a.id', 'a.name', 'a.unit', 'a.price', 'a.time', 'a.description', 'a.status', 'a.remark']);
        $query->where(['a.record_id' => $id, 'a.spot_id' => self::$staticSpotId]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
    }

    public static function getCureRecord($id) {
        $cureRecord = CureRecord::find()->select(['name', 'remark', 'description', 'type', 'cure_result', 'record_id'])->where(['record_id' => $id])->asArray()->all();
        $data = [];
        if (!empty($id) && is_array($id)) {
            foreach ($id as $val) {
                foreach ($cureRecord as $v) {
                    if ($val == $v['record_id']) {
                        $data[$v['record_id']][] = $v;
                        $data[$v['record_id']]['name'] .= $v['name'] . ',';
                    }
                }
            }
        }
        return $data;
    }

    public static $getCureResult = [
        1 => '阴性',
        2 => '阳性'
    ];
    // 得到治疗的状态
    public static $getStatus = [
        1 => '已完成',
        2 => '治疗中',
        3 => '待治疗'
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
        }else if($status == 3) {
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
