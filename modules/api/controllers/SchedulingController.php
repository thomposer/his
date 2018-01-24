<?php

namespace app\modules\api\controllers;

use Yii;
//use yii\bootstrap\Html;
use yii\helpers\Html;
use yii\filters\AccessControl;
use app\modules\make_appointment\models\Appointment;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\helpers;
use app\modules\schedule\models\Scheduling;
use app\modules\user\models\UserSpot;
use app\modules\user\models\User;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\spot_set\models\Schedule;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class SchedulingController extends CommonController
{

    public function behaviors() {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
//                    'add-scheduling' => ['post']
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    /**
     * index
     * @param string $start_date 开始时间 
     * @param string $end_date 结束时间
     * @param string $name 医生姓名
     * @param int $department_id 科室ID
     * 
     * @return int errorCode 错误代码(0-查询成功,1001-参数错误)
     * @return string data 排班信息
     * @return string department_list 科室信息
     * @desc 获取医生的排班信息
     */
    public function actionIndex() {
        $start_date = Yii::$app->request->post('start_date');
        $end_date = Yii::$app->request->post('end_date');
        $name = trim(Yii::$app->request->post('name'));
        $department_id = trim(Yii::$app->request->post('department_id'));
        $sort_type = Yii::$app->request->post('sort_type');
        $occupation = Yii::$app->request->post('occupation');
        if (!$start_date || !$end_date) {
            $this->result['errorCode'] = 1001;
            return Json::encode($this->result);
        }
        $model = new Scheduling();
        $week_data = $model->getWeek($start_date, $end_date);
        $spot_id = $this->spotId; //诊所id
        $worder_list = User::getWorkerList($name, $department_id, $spot_id,$occupation,false,$sort_type);
        $schedul_list = Scheduling::getSchedulList($start_date, $end_date);
        $department_list = User::getDepartmentInfo();
        $schedule_list = $this->formatSchedule($week_data, $worder_list, $schedul_list);
        $this->result['data'] = $schedule_list;
        $this->result['department_list'] = $department_list;
        return Json::encode($this->result, JSON_ERROR_NONE);
    }

    /**
     * doctor-schedule
     * @param string $start_date 开始时间 
     * @param string $end_date 结束时间
     * 
     * @return int errorCode 错误代码(0-查询成功,1001-参数错误)
     * @return string data 排班信息
     * @desc 获取所有医生的排班信息
     */
    public function actionDoctorSchedule() {
        $start_date = Yii::$app->request->post('start_date');
        $end_date = Yii::$app->request->post('end_date');
        if (!$start_date || !$end_date) {
            $this->result['errorCode'] = 1001;
            return Json::encode($this->result);
        }
        $model = new Scheduling();
        $week_data = $model->getWeek($start_date, $end_date);
        $worder_list = User::getWorker(Yii::$app->user->identity->id);
        $schedul_list = Scheduling::getSchedulList($start_date, $end_date);
        $schedule_list = $this->formatSchedule($week_data, $worder_list, $schedul_list);
        $this->result['data'] = $schedule_list;
        return Json::encode($this->result, JSON_ERROR_NONE);
    }

    private function formatSchedule($week_data, $worder_list, $schedul_list) {
        $schedule_list = [];
        foreach ($week_data as $v1) {
            $scheduls = [];
            foreach ($worder_list as $v2) {
                $schedul_merge = [
                    'schedule_time' => '',
                    'shift_name' => '',
                    'schedule_id' => ''
                ];
                if (isset($schedul_list[$v1])) {
                    $sl = $schedul_list[$v1];
                    if (isset($sl[$v2['doctor_id']]) && ($sd = $sl[$v2['doctor_id']])) {
                        $schedul_merge = [
                            'schedule_time' => $sd['schedule_time'],
                            'shift_name' => Html::encode($sd['shift_name']),
                            'schedule_id' => $sd['schedule_id']
                        ];
                        $scheduls[] = array_merge($schedul_merge, $v2);
                    } else {
                        $scheduls[] = array_merge($schedul_merge, $v2);
                    }
                } else {
                    $scheduls[] = array_merge($schedul_merge, $v2);
                }
            }
            $schedule_list[] = [
                'date' => $v1,
                'scheduls' => $scheduls
            ];
        }
        return $schedule_list;
    }

    /**
     * doctor-schedule
     * 
     * @return int errorCode 错误代码(0-查询成功)
     * @return string data 班次信息
     * @desc 获取班次列表
     */
    public function actionScheduleConf() {
        $schedule = Schedule::find()->select(['id', 'shift_name'])->where(['status' => 1, 'spot_id' => $this->spotId])->asArray()->all();
        foreach ($schedule as &$val) {
            $val['shift_name'] = Html::encode($val['shift_name']);
        }
        $this->result['data'] = $schedule;
        return Json::encode($this->result, JSON_ERROR_NONE);
    }

}
