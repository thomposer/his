<?php

namespace app\modules\spot_set\models;

use Yii;
use app\common\base\BaseActiveRecord;

/**
 * This is the model class for table "{{%room}}".
 *
 * @property string $id
 * @property string $clinic_name
 * @property integer $floor
 * @property integer $clinic_type
 * @property integer $status
 * @property string $spot_id
 * @property integer $treatment_time
 * @property integer $clean_status 待诊室状态
 * @property string $create_time
 * @property integer $update_time
 * @property integer $record_id
 */
class Room extends BaseActiveRecord
{

    public $waite_time;

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%room}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['floor', 'record_id', 'clinic_type', 'status', 'spot_id', 'create_time', 'update_time', 'clean_status', 'treatment_time'], 'integer'],
            [['clinic_name', 'floor', 'status', 'clinic_type'], 'required'],
            [['clinic_name'], 'string', 'max' => 30],
            [['clinic_name'], 'trim'],
            [['clinic_name'], 'validateClinicName'],
            [['floor'], 'integer', 'max' => 255, 'min' => -5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '诊室ID',
            'clinic_name' => '诊室名称',
            'floor' => '所在楼层',
            'clinic_type' => '诊室类型',
            'status' => '状态',
            'spot_id' => '诊所ID',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'treatment_time' => '接诊完成时间',
            'waite_time' => '等待时间',
            'clean_status' => '当前状态',
            'record_id' => '就诊流水id'
        ];
    }

    /*
     * 状态
     */

    public static $getStatus = [
        1 => '正常',
        2 => '停用',
//         3 => '已删除'
    ];

    /*
     * 诊室类型
     */
    public static $getClinicType = [
        1 => '普通诊室',
        2 => '儿科诊室',
    ];
    /*
     * 整理状态
     */
    public static $getCleanStatus = [
        1 => '正常',
        2 => '待整理',
    ];

    /*
     * 计算两个时间戳相隔时分秒
     */

    public static function timediff($begin_time, $end_time = 0) {
        if ($end_time == 0) {
            $end_time = time();
        }
        if ($begin_time < $end_time) {
            $starttime = $begin_time;
            $endtime = $end_time;
        } else {
            $starttime = $end_time;
            $endtime = $begin_time;
        }
        //计算天数
        $timediff = $endtime - $starttime;
        $days = intval($timediff / 86400);
        //计算小时数
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);
        //计算分钟数
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        //计算秒数
        $secs = $remain % 60;
        $res = array("d" => $days, "h" => $hours, "i" => $mins, "s" => $secs);
        $ret = "";
        $res['d'] && $ret.= $res['d'] . '天';
        $res['h'] && $ret.=$res['h'] . '小时';
        $res['i'] && $ret.=$res['i'] . '分钟';
        $res['s'] && $ret.=$res['s'] . '秒';
        return $ret;
    }

    public function validateClinicName($attribute) {
        $spotId = $this->spotId;
        if ($this->isNewRecord) {
            $hasRecord = Room::find()->select(['id'])->where(['spot_id' => $this->spotId, $attribute => $this->$attribute, 'status' => [1, 2]])->asArray()->limit(1)->one();
            if ($hasRecord) {
                $this->addError($attribute, '该诊室名称已存在');
            }
        } else {
            $oldClicnic = $this->getOldAttribute('clinic_name');
            if ($oldClicnic != $this->clinic_name) {
                $hasRecord = $this->checkDuplicate('clinic_name', $this->clinic_name);
                if ($hasRecord) {
                    $this->addError('clinic_name', '该诊室名称已存在');
                }
            }
        }
    }

    protected function checkDuplicate($attribute, $params) {

        $hasRecord = Room::find()->select(['id'])->where(['spot_id' => $this->spotId, $attribute => $params])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $status 诊室的状态数组  默认为全部状态
     * @return array 符合状态的所有诊室
     * 获取所有诊室列表
     */
    public static function getRoomList($status = [1,2,3]) {
        return self::find()->select(['id as room_id', 'clinic_name as room_name'])->where(['spot_id' => self::$staticSpotId,'status' => $status])->asArray()->all();
    }

}
