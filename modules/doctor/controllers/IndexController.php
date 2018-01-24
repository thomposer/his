<?php

namespace app\modules\doctor\controllers;

use app\modules\schedule\models\Scheduling;
use Yii;
use app\modules\doctor\models\Doctor;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\modules\patient\models\Patient;
use yii\helpers\Html;
use app\modules\triage\models\TriageInfo;
use app\modules\user\models\User;
use app\modules\user\models\UserSpot;
use yii\helpers\Url;
use app\modules\report\models\search\AppointmentSearch;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\spot\models\SpotConfig;
use app\modules\make_appointment\models\AppointmentTimeConfig;
use app\modules\spot\models\Spot;
use app\modules\make_appointment\models\Appointment;
use app\modules\spot_set\models\Schedule;

/**
 * IndexController implements the CRUD actions for Doctor model.
 */
class IndexController extends BaseController
{

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Doctor models.
     * @return mixed
     */
    public function actionIndex() {
        if (($param = json_decode(file_get_contents('php://input'), true))) {
            return $this->viewDoctorModal($param);
        }
        $doctor = $this->userInfo;
        $triageInfoModel = new TriageInfo();
        $triage_num = $triageInfoModel->getDoctorDiagnoseNum($doctor->id);     //已分诊未接诊的患者数
        //$triage_num = 11;     //已分诊未接诊的患者数
        $outpatient_num = $triageInfoModel->getDoctorDiagnoseNum($doctor->id, $type = 5); //当天点击接诊且点击了结束就诊的患者数
        //$outpatient_num = 4; //当天点击接诊且点击了结束就诊的患者数
        $schedule = Scheduling::getScheduleList(['id','shift_name'],['status' => 1]);
        $appointmentTimeList = Appointment::getAppointmentTimeList();
        $appointmentTimeList['triage_num'] = $triage_num;
        $appointmentTimeList['outpatient_num'] = $outpatient_num;
        $appointmentTimeList['doctorId'] = $doctor->id;//判断是否从医生工作台查看医生预约信息
        $appointmentTimeList['entrance'] = 2;//入口，区分从预约还是医生工作台打开人数统计的预约情况，1-预约，2-医生工作台
        $appointmentTimeList['schedule'] = $schedule;
        return $this->render('index',$appointmentTimeList);
    }
        /**
         * @return 返回当前诊所的预约类型(1-医生预约,2-科室预约)
         */
        protected function checkAppointmentType() {
            $result = Spot::find()->select(['appointment_type'])->where(['id' => $this->spotId])->asArray()->one();
            return explode(',', $result['appointment_type']);
        }

    protected function viewDoctorModal($param) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $doctorName=  trim($param['doctor_name']);
        if ($doctorName!='') {
            $patient = Patient::find()
                            ->select(['username', 'iphone', 'id', 'sex', 'head_img', 'birthday'])
//                            ->where(['spot_id' => $this->parentSpotId])
                            ->where([
                                'and',
                                ['like', 'username', $doctorName]
                            ])
                            ->orWhere([
                                'and',
                                ['iphone' => $doctorName]
                            ])
                            ->andWhere(['spot_id' => $this->parentSpotId])
                            ->asArray()->all();
            if (!empty($patient)) {
                return [
                    'title' => "请选择患者",
                    'content' => $this->renderAjax('_doctormodal', [
                        'patient' => $patient,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"])
                ];
            } else {
                $this->result['errorCode'] = 10002;
                return $this->result;
            }
        } else {
            $this->result['errorCode'] = 10001;
            return $this->result;
        }
    }

    /**
     * Finds the Doctor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Doctor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Doctor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
