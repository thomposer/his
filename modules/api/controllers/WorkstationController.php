<?php

namespace app\modules\api\controllers;

use Yii;
use app\modules\make_appointment\models\Appointment;
use yii\filters\VerbFilter;
use app\modules\schedule\models\Scheduling;
use app\modules\user\models\User;
use app\modules\spot_set\models\Schedule;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\db\Query;
use app\modules\outpatient\models\InspectRecord;
use app\modules\outpatient\models\CheckRecord;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\spot\models\RecipeList;
use yii\data\ArrayDataProvider;
use yii\web\Response;
use app\modules\report\models\Report;
use app\modules\patient\models\Patient;

class WorkstationController extends CommonController
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

    public function actionIndex() {
        $start_date = Yii::$app->request->post('start_date');
        $end_date = Yii::$app->request->post('end_date');
        $name = trim(Yii::$app->request->post('name'));
        $department_id = trim(Yii::$app->request->post('department_id'));
        if ((!$start_date || !$end_date) || !strtotime($start_date) || !strtotime($end_date)) {
            $this->result['errorCode'] = 1001;
            return Json::encode($this->result);
        }
        $model = new Scheduling();
        $week_data = $model->getWeek($start_date, $end_date);
        $spot_id = $this->spotId; //诊所id
        $worker_list = User::getWorkerRoomList($name, $department_id, $spot_id);
        $schedul_list = Appointment::getAppointmentStation($start_date, $end_date);
        $department_list = User::getDepartmentInfo();
        $schedule_list = [];
        foreach ($week_data as $v1) {
            $scheduls = [];
            foreach ($worker_list as $v2) {
                $schedul_merge = [
                    'schedule_time' => '',
                    'appointment_num' => '',
                ];
                if (isset($schedul_list[$v1])) {
                    $sl = $schedul_list[$v1];
                    if (isset($sl[$v2['doctor_id']]) && ($sd = $sl[$v2['doctor_id']])) {
                        $schedul_merge = [
                            'schedule_time' => $v1,
                            'appointment_num' => $sd['appointment_num'],
                        ];
                    }
                }
                $scheduls[] = array_merge($schedul_merge, $v2);
            }
            $schedule_list[] = [
                'date' => $v1,
                'scheduls' => $scheduls
            ];
        }
        $this->result['data'] = $schedule_list;
        $this->result['department_list'] = $department_list;
        return Json::encode($this->result, JSON_ERROR_NONE);
    }

    /*
     * 获取班次列表
     */

    public function actionAppointmentConf() {
        $schedule = Schedule::find()->select(['id', 'shift_name'])->where(['status' => 1, 'spot_id' => $this->spotId])->asArray()->all();
        $this->result['data'] = $schedule;
        return json_encode($this->result, JSON_ERROR_NONE);
    }

    public function actionGetOrdersData($id) {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $pjax = Yii::$app->request->get('_pjax');

            $query = new Query();
            $inspectList = $query->from(['t' => InspectRecord::tableName()])
                    ->select(['t.name', 't.status', 't.create_time', 't.id', 't.record_id'])
                    ->where(['t.spot_id' => $this->spotId, 't.record_id' => $id])
                    ->all();
            array_walk($inspectList, array($this, 'formatRecord'), 1);
            $checkList = $query->from(['t' => CheckRecord::tableName()])
                    ->select(['t.name', 't.status', 't.create_time', 't.id','t.record_id'])
                    ->where(['t.spot_id' => $this->spotId, 't.record_id' => $id])
                    ->all();
            array_walk($checkList, array($this, 'formatRecord'), 2);
            $cureList = $query->from(['t' => CureRecord::tableName()])
                    ->select(['t.name', 't.status', 't.create_time','t.id', 't.record_id'])
                    ->where(['t.spot_id' => $this->spotId, 't.record_id' => $id])
                    ->all();
            array_walk($cureList, array($this, 'formatRecord'), 3);
            $recipeList = $query->from(['t' => RecipeRecord::tableName()])
                    ->select(['t.name', 't.status', 't.drug_type' , 't.create_time','t.id', 't.record_id'])
                    ->where(['t.spot_id' => $this->spotId, 't.record_id' => $id])
                    ->all();
            array_walk($recipeList, array($this, 'formatRecord'), 4);
            $data = array_merge($checkList, $inspectList, $cureList, $recipeList);
            if (!Yii::$app->request->isPjax) {
                $param = time();
            } else {
                $param = substr($pjax, 15);
            }

            $query = new Query();
            $reportInfo = $query->from(['r' => Report::tableName()])
                    ->leftJoin(['p' => Patient::tableName()], '{{p}}.id = {{r}}.patient_id')
                    ->leftJoin(['u' => User::tableName()], '{{u}}.id = {{r}}.doctor_id')
                    ->select(['p.username as userName', 'u.username as doctorName'])
                    ->where(['r.spot_id' => $this->spotId, 'r.record_id' => $id])
                    ->one();
            $provider = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => [
                    'pageSize' => 10,
                ],
                'sort' => [
                    'defaultOrder' => [
                        'create_time' => SORT_DESC
                    ],
                    'attributes' => ['create_time'],
                ],
            ]);
            return [
                'title' => Html::encode($reportInfo['doctorName']) . '已开医嘱<span style="color:#99A3B2;font-size:16px">(' . Html::encode($reportInfo['userName']) . ')</span>',
                'content' => $this->renderAjax('@app/modules/nurse/views/index/orders', [
                    'param' => $param,
                    'dataProvider' => $provider,
                ]),
            ];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    protected function formatRecord(&$value, $key, $type) {
        $value['type'] = $type;
    }

    /*
     * 二维数组的数组元素插入相同元素
     */

    protected function addClumn($array, $field) {
        foreach ($array as &$value) {
            $value = array_merge($value, $field);
        }
        return $array;
    }

}
