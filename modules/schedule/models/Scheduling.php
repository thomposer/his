<?php

namespace app\modules\schedule\models;

use Yii;
use app\modules\spot_set\models\Schedule;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%scheduling}}".
 * 排班模块表
 * @property string $id
 * @property string $user_id
 * @property string $schedule_id
 * @property string $schedule_time
 * @property string $create_time
 * @property string $update_time
 */
class Scheduling extends \app\common\base\BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public $occupation;

    public static function tableName() {
        return '{{%scheduling}}';
    }

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'schedule_id', 'schedule_time', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'schedule_id' => 'Schedule ID',
            'schedule_time' => 'Schedele Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /*
     * 增加排班 
     */

    public function addScheduling($date, $worker_id, $schedule_id) {
        $model = $this->findSchedulingModel(['spot_id' => $this->spotId, 'user_id' => $worker_id, 'schedule_time' => strtotime(date("Y-m-d", strtotime($date)))]);
        $model->user_id = $worker_id;
        $model->schedule_id = $schedule_id;
        $model->schedule_time = strtotime($date);
        return $model->save();
    }

    /*
     * 获取每个周的所有时间
     */

    public function getWeek($start, $end) {
        $start = strtotime($start);
        $end = strtotime($end);
        $day = $start;
        $week_data = [];
        while ($day <= $end) {
            $week_data[] = date("Y-m-d", $day);
            $day+=86400;
        }
        return $week_data;
    }

    /*
     * 获取排版的列表
     */

    public static function getSchedulList($start_date, $end_date) {

        $query = (new \yii\db\Query());
        $query->from(['sl' => Scheduling::tableName()]);
        $query->leftJoin(['s' => Schedule::tableName()], '{{sl}}.schedule_id={{s}}.id');
        $query->select(['schedule_time' => "FROM_UNIXTIME(sl.schedule_time, '%Y-%m-%d')", 'sl.user_id', 's.shift_name', 'sl.schedule_id',]);
        $query->where(['between', 'sl.schedule_time', strtotime($start_date), strtotime($end_date) + 86400]);
        $query->andWhere(['sl.spot_id' => self::$staticSpotId]);
        $list = $query->all();
        $data = [];
        if (!empty($list)) {
            foreach ($list as $val) {
                $data[$val['schedule_time']][$val['user_id']] = $val;
            }
        }
        return $data;
    }

    protected function findSchedulingModel($where) {

        if (($model = Scheduling::findOne($where)) !== null) {
            return $model;
        } else {
            return new Scheduling();
        }
    }

    /**
     * @desc 返回当前诊所排版的信息
     * @param string | array $fields 查询字段
     * @param string | array $where 查询条件
     * @return \yii\db\ActiveRecord[]
     */
    public static function getScheduleList($fields = '*', $where = '1 != 0') {
        $schedule = Schedule::find()->select($fields)->where(['spot_id' => self::$staticSpotId])->andFilterWhere($where)->asArray()->all();
        return $schedule;
    }


}
