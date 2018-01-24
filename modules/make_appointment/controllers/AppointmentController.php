<?php

namespace app\modules\make_appointment\controllers;

use app\modules\spot\models\SpotConfig;
use app\modules\spot_set\models\UserAppointmentConfig;
use Yii;
use app\modules\make_appointment\models\Appointment;
use app\modules\make_appointment\models\search\AppointmentSearch;
use app\common\base\BaseController;
use yii\base\NotSupportedException;
use yii\helpers\Url;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use yii\db\Query;
use app\modules\spot_set\models\OnceDepartment;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use app\modules\make_appointment\models\AppointmentConfig;
use yii\base\Object;
use app\modules\user\models\UserSpot;
use yii\bootstrap\Html;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use app\modules\make_appointment\models\DoctorTimeConfig;
use app\modules\spot\models\Spot;
use yii\db\Exception;
use app\modules\make_appointment\models\AppointmentTimeConfig;
use app\common\Common;
use app\modules\spot_set\models\SpotType;
use app\specialModules\recharge\models\CardRecharge;
use app\modules\make_appointment\models\AppointmentTimeTemplate;
use app\modules\make_appointment\models\AppointmentTimeAndServer;
use app\modules\spot_set\models\Schedule;
use app\modules\schedule\models\Scheduling;
use app\modules\spot_set\models\SecondDepartmentUnion;

/**
 * AppointmentController implements the CRUD actions for Appointment model.
 */
class AppointmentController extends BaseController
{

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'save-config' => ['post'],
                    'delete' => ['post'],
                    'copy-config' => ['post']
                ],
            ],
        ];
    }

    public function actionIndex() {
        $appointmentTimeList = Appointment::getAppointmentTimeList();
        return $this->render('index', $appointmentTimeList);
    }

    public function actionAppointmentDetail() {
        $appointmentTimeList = Appointment::getAppointmentTimeList();

        return $this->render('appointment-detail', $appointmentTimeList);
    }

    /**
     * Lists all Appointment models.
     * @return mixed
     */
    public function actionList() {
        $searchModel = new AppointmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
//        $query = new Query();
//        $query->from(['a' => SecondDepartment::tableName()]);
//        $query->leftJoin(['b' => SecondDepartmentUnion::tableName()], '{{a}}.id = {{b}}.second_department_id');
//        $query->select(['a.id', 'a.name']);
//        $query->where(['a.spot_id' => $this->parentSpotId, 'b.spot_id' => $this->spotId, 'a.status' => 1]);
//        $secondDepartmentInfo = $query->all();

        $query = new query();
        $query->from(['a' => User::tableName()]);
        $query->leftJoin(['b' => UserSpot::tableName()], '{{a}}.id = {{b}}.user_id');
        $query->select(['a.id', 'a.username']);
        $query->where(['a.status' => 1, 'a.occupation' => 2, 'b.spot_id' => $this->spotId]);
        $doctorInfo = $query->all();

        $appointment_type = Appointment::checkAppointmentType();

        $queryUser = new query();
        $queryUser->from(['a' => User::tableName()]);
        $queryUser->leftJoin(['b' => UserSpot::tableName()], '{{a}}.id = {{b}}.user_id');
        $queryUser->select(['a.id', 'a.username']);
        $queryUser->where(['a.status' => 1, 'b.spot_id' => $this->spotId]);
        $getAppointmentOperator = $queryUser->all();
        $cardInfo = CardRecharge::getCardInfoByQueryNurse($dataProvider->query);
        //服务类型
        $spotTypeList = SpotType::getSpotType('status=1');

        return $this->render('list', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'secondDepartmentInfo' => $secondDepartmentInfo,
                    'doctorInfo' => $doctorInfo,
                    'appointment_type' => $appointment_type,
                    'getAppointmentOperator' => $getAppointmentOperator,
                    'cardInfo' => $cardInfo,
                    'spotTypeList' => $spotTypeList
        ]);
    }

    /**
     * Lists all Appointment models.
     * @return mixed
     */
    public function actionDetail() {
        /* 获取开放预约的医生 */
        $query = new query();
        $query->from(['u' => User::tableName()]);
        $query->leftJoin(['us' => UserSpot::tableName()], '{{u}}.id={{us}}.user_id');
        $query->select([ 'doctor_id' => 'u.id', 'doctor_name' => 'u.username', 'us.status']);
        $query->where(['us.spot_id' => $this->spotId, 'u.occupation' => 2, 'u.status' => 1, 'us.status' => 1]);
        $query->groupBy('u.id');
        $doctor = $query->all();

        $spotConfig = SpotConfig::find()->select(['begin_time', 'end_time', 'reservation_during'])->where(['spot_id' => $this->spotId])->asArray()->all();

        $timeLine = array();
        $date = date('Y-m-d', time());
        $begin = strtotime($date . ' ' . $spotConfig[0]['begin_time']);
        $end = strtotime($date . ' ' . $spotConfig[0]['end_time']);

        if (empty($spotConfig)) {
            $time = array();
        } else {
            $timeLine[] = $spotConfig[0]['begin_time'];
            for ($i = 1; $i > 0; $i++) {
                if (($begin + 60 * 30) < $end) {
                    $begin = $begin + 60 * 30;

                    $nextTime = date('H:i', $begin);
                    $timeLine[] = $nextTime;
                } else {
                    break;
                }
            }

            $time = json_encode($timeLine);
        }
        $appointment_type = Appointment::checkAppointmentType();
        if (in_array(1, $appointment_type)) {
            return $this->render('detail', [
                        'doctorInfo' => $doctor,
                        'timeLine' => $time,
                        'appointment_type' => $appointment_type
            ]);
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * 科室预约设置界面
     */
    public function actionRoomConfig() {
        $appointment_type = Appointment::checkAppointmentType();
        $spotAppointmentTime = SpotConfig::getConfig();
        $timeConfig = [
            'begin_time' => 0,
            'end_time' => 0
        ];
        if ($spotAppointmentTime && $spotAppointmentTime['begin_time']) {
            $timeConfig = [
                'begin_time' => $spotAppointmentTime['begin_time'],
                'end_time' => $spotAppointmentTime['end_time']
            ];
        }
        if (in_array(2, $appointment_type)) {
            return $this->render('room-config', [
                        'appointment_type' => $appointment_type,
                        'timeConfig' => $timeConfig,
            ]);
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * 医生预约设置界面
     */
    public function actionDoctorConfig() {

        if (Yii::$app->request->isAjax) {
            if ($param = Yii::$app->request->post()) {
                if (!empty($param['remarkArr']) || !empty($param['notRemarkArr'])) {
                    $userSpotModel = new UserSpot();

                    if (!empty($param['remarkArr'])) {
                        foreach ($param['remarkArr'] as $k => $v) {
                            $userSpotModel->updateAll(array('status' => 1), 'user_id=' . $param['remarkArr'][$k] . ' and spot_id=' . $this->spotId . ' and parent_spot_id=' . $this->parentSpotId);
                        }
                    }

                    if (!empty($param['notRemarkArr'])) {
                        foreach ($param['notRemarkArr'] as $z => $l) {
                            $userSpotModel->updateAll(array('status' => 2), 'user_id=' . $param['notRemarkArr'][$z] . ' and spot_id=' . $this->spotId . ' and parent_spot_id=' . $this->parentSpotId);
                        }
                    }

                    return Json::encode($this->result);
                } else {
                    $this->result['errorCode'] = 10001;
                    return Json::encode($this->result);
                }
            } else {
                $userSpotModel = new UserSpot();
                $userSpotModel->updateAll(array('status' => 2), 'spot_id=' . $this->spotId . ' and parent_spot_id=' . $this->parentSpotId);
                return Json::encode($this->result);
            }
        }

        //获取所有医生
        $DoctorInfo = User::getDoctorRoomList($this->spotId);

        $appointment_type = Appointment::checkAppointmentType();

        if (in_array(1, $appointment_type)) {
            return $this->render('doctor-config', [
                        'appointment_type' => $appointment_type,
                        'doctorInfo' => $DoctorInfo
            ]);
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * 保存预约设置时间
     */
    public function actionSaveConfig() {
        $data = Yii::$app->request->post('data');
        if ($data) {
            $timeConfig = SpotConfig::getAppointmentTimeConfig();
            $insertList = [];
            $dataList = Json::decode($data);
            $hasRecord = $this->findOnceDepartment($dataList['id']);
            if (!$hasRecord) {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '诊所已切换';
                return Json::encode($this->result);
            }
            $needInsert = false;
            if (isset($dataList['daily_detail']) && !empty($dataList['daily_detail'])) {
                foreach ($dataList['daily_detail'] as $key => $v) {
                    if ($timeConfig['begin_time'] && $v['start_date'] < $timeConfig['begin_time']) {
                        $this->result['errorCode'] = 1015;
                        $this->result['msg'] = '开始时间必须大于诊所设置的开始时间!';
                        return Json::encode($this->result);
                    }
                    if ($timeConfig['end_time'] && $v['end_date'] > $timeConfig['end_time']) {
                        $this->result['errorCode'] = 1016;
                        $this->result['msg'] = '结束时间必须小于诊所设置的结束时间!';
                        return Json::encode($this->result);
                    }
                    $start_date = strtotime($dataList['date'] . ' ' . $v['start_date']);
                    $end_date = strtotime($dataList['date'] . ' ' . $v['end_date']);

                    if ($v['start_date'] != '' && $v['end_date'] != '' && $v['doctor_count'] != '') {
//                        if ($start_date < time()) {
//                            $this->result['errorCode'] = 1011;
//                            $this->result['msg'] = '预约开始时间不能小于当前时间';
//                            return Json::encode($this->result);
//                        } else if ($end_date < time()) {
//                            $this->result['errorCode'] = 1011;
//                            $this->result['msg'] = '预约结束时间不能小于当前时间';
//                            return Json::encode($this->result);
//                        } else
                        if ($start_date >= $end_date) {
                            $this->result['errorCode'] = 1011;
                            $this->result['msg'] = '预约开始时间不能大于结束时间';
                            return Json::encode($this->result);
                        }
                        foreach ($dataList['daily_detail'] as $child => $value) {
                            if ($key != $child) {
                                if (($value['start_date'] <= $v['start_date'] && $v['start_date'] < $value['end_date']) || ($value['start_date'] < $v['end_date'] && $v['end_date'] <= $value['end_date'])) {
                                    $this->result['errorCode'] = 1012;
                                    $this->result['msg'] = '预约时间段不允许重叠';
                                    return Json::encode($this->result);
                                }
                            }
                        }
                        $insertList[] = [$this->spotId, $dataList['id'], $start_date, $end_date, $v['doctor_count'], time(), time()];
                    } else if ($v['start_date'] == '' && $v['end_date'] == '' && $v['doctor_count'] == '') {
                        
                    } else {
                        $this->result['errorCode'] = 1014;
                        $this->result['msg'] = '请将表单填写完整';
                        return Json::encode($this->result);
                    }
                }
                $needInsert = true;
            }
            AppointmentConfig::deleteAll(['department_id' => $dataList['id'], 'spot_id' => $this->spotId, "FROM_UNIXTIME(begin_time, '%Y-%m-%d')" => $dataList['date']]);
            if ($needInsert) {
                Yii::$app->db->createCommand()->batchInsert(AppointmentConfig::tableName(), ['spot_id', 'department_id', 'begin_time', 'end_time', 'doctor_count', 'create_time', 'update_time'], $insertList)->execute();
            }
        }
        $this->result['data'] = $data;
        return Json::encode($this->result);
    }

    /**
     * 医生预约设置界面
     */
    public function actionTimeConfig() {
        $appointment_type = Appointment::checkAppointmentType();
        $spotAppointmentTime = SpotConfig::getConfig();
        $schedule = Scheduling::getScheduleList(['id', 'shift_name'], ['status' => 1]);
        $timeConfig = [
            'begin_time' => 0,
            'end_time' => 0
        ];
        if ($spotAppointmentTime && $spotAppointmentTime['begin_time']) {
            $timeConfig = [
                'begin_time' => $spotAppointmentTime['begin_time'],
                'end_time' => $spotAppointmentTime['end_time']
            ];
        }
        if (in_array(1, $appointment_type)) {
            //诊所设置的开始结束预约时间
            return $this->render('time-config', [
                        'appointment_type' => $appointment_type,
                        'timeConfig' => $timeConfig,
                        'schedule' => $schedule
            ]);
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     *
     * @param type $id
     * @return type
     */
    public function actionSaveTimeConfig() {
        $dataList = Yii::$app->request->post();
        if ($dataList) {
            $timeConfig = SpotConfig::getAppointmentTimeConfig();
            $insertList = [];
            $needInsert = false;
            if (isset($dataList['dailyDetail']) && !empty($dataList['dailyDetail'])) {
                foreach ($dataList['dailyDetail'] as $key => $v) {

                    //服务类型
                    if (empty($v['serve_types'])) {
                        $this->result['errorCode'] = 1017;
                        $this->result['msg'] = '服务类型不能为空!';
                        return Json::encode($this->result);
                    }

                    $startDate = strtotime($dataList['date'] . ' ' . $v['start_date']);
                    $endDate = strtotime($dataList['date'] . ' ' . $v['end_date']);
                    $timeConfigStartDate = strtotime($dataList['date'] . ' ' . $timeConfig['begin_time']);
                    $timeConfigEndDate = strtotime($dataList['date'] . ' ' . $timeConfig['end_time']);

                    if ($timeConfig['begin_time'] && $startDate < $timeConfigStartDate) {
                        $this->result['errorCode'] = 1015;
                        $this->result['msg'] = '开始时间必须大于诊所设置的开始时间!';
                        return Json::encode($this->result);
                    }
                    if ($timeConfig['end_time'] && $endDate > $timeConfigEndDate) {
                        $this->result['errorCode'] = 1016;
                        $this->result['msg'] = '结束时间必须小于诊所设置的结束时间!';
                        return Json::encode($this->result);
                    }
//                     $start_date = strtotime($dataList['date'] . ' ' . $v['start_date']);
//                     $end_date = strtotime($dataList['date'] . ' ' . $v['end_date']);

                    if ($v['start_date'] != '' && $v['end_date'] != '') {
//                        if ($start_date < time()) {
//                            $this->result['errorCode'] = 1011;
//                            $this->result['msg'] = '预约开始时间不能小于当前时间';
//                            return Json::encode($this->result);
//                        } else if ($end_date < time()) {
//                            $this->result['errorCode'] = 1011;
//                            $this->result['msg'] = '预约结束时间不能小于当前时间';
//                            return Json::encode($this->result);
//                        } else
                        if ($startDate >= $endDate) {
                            $this->result['errorCode'] = 1011;
                            $this->result['msg'] = '预约开始时间不能大于结束时间';
                            return Json::encode($this->result);
                        }
                        foreach ($dataList['dailyDetail'] as $child => $value) {
                            if ($key != $child) {
                                if (($value['start_date'] <= $v['start_date'] && $v['start_date'] < $value['end_date']) || ($value['start_date'] < $v['end_date'] && $v['end_date'] <= $value['end_date'])) {
                                    $dataList['dailyDetail'][$key]['repeat'] = 1;
                                    $this->result['errorCode'] = 1012;
                                    $this->result['msg'] = '预约时间段不允许重叠';
                                    return Json::encode($this->result);
                                    break;
                                }
                            }
                        }
                        if (!isset($dataList['dailyDetail'][$key]['repeat'])) {
//                            $insertList[] = [$this->spotId, $dataList['doctorId'], $startDate, $endDate, time(), time()];
                            $insertList[] = [
                                'spot_id' => $this->spotId,
                                'user_id' => $dataList['doctorId'],
                                'serve_types' => $v['serve_types'],
                                'begin_time' => $startDate,
                                'end_time' => $endDate,
                                'create_time' => time(),
                                'update_time' => time()];
                        }
                    } else if ($v['start_date'] == '' && $v['end_date'] == '' && $v['doctor_count'] == '') {
                        
                    } else {
                        $this->result['errorCode'] = 1014;
                        $this->result['msg'] = '请将表单填写完整';
                        return Json::encode($this->result);
                    }
                }
                $needInsert = true;
            }
            AppointmentConfig::deleteAll(['user_id' => $dataList['doctorId'], 'spot_id' => $this->spotId, "FROM_UNIXTIME(begin_time, '%Y-%m-%d')" => date('Y-m-d', strtotime($dataList['date']))]);
            if ($needInsert) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    foreach ($insertList as $insertItem) {
                        $model = new AppointmentConfig();
                        $model->spot_id = $insertItem['spot_id'];
                        $model->user_id = $insertItem['user_id'];
                        $model->begin_time = $insertItem['begin_time'];
                        $model->end_time = $insertItem['end_time'];
                        $model->create_time = $insertItem['create_time'];
                        $model->update_time = $insertItem['update_time'];

                        if ($model->save() && is_array($insertItem['serve_types'])) {
                            foreach ($insertItem['serve_types'] as $v) {
                                $timeAndServer = new AppointmentTimeAndServer();
                                $timeAndServer->time_config_id = $model->id;
                                $timeAndServer->spot_type_id = $v;
                                $timeAndServer->spot_id = $this->spotId;
                                $timeAndServer->create_time = $model->create_time;
                                $timeAndServer->update_time = $model->update_time;
                                $timeAndServer->save();
                            }
                        }
                    }
                    $dbTrans->commit();
                } catch (Exception $e) {
                    Yii::error(json_encode($e->errorInfo, true), 'save-time-config');
                    $dbTrans->rollBack();
                }

//                Yii::$app->db->createCommand()->batchInsert(AppointmentConfig::tableName(), ['spot_id', 'user_id', 'begin_time', 'end_time', 'create_time', 'update_time'], $insertList)->execute();
            }
            $userSchedule = Scheduling::getSchedulList($dataList['date'], $dataList['date']);
            $this->result['schedule'] = isset($userSchedule[$dataList['date']][$dataList['doctorId']])&&$userSchedule[$dataList['date']][$dataList['doctorId']]['schedule_id'] ? $userSchedule[$dataList['date']][$dataList['doctorId']] : '';
//            $this->result['scheduleConf'] = Schedule::getSecheduleConf();
        }
        $this->result['data'] = $dataList;
        $this->result['scheduleConf'] = Schedule::getSecheduleConf();
        $schedulePermison = 2;
        $clear = 2;
        if (isset(Yii::$app->view->params['permList']['role']) || in_array(Yii::getAlias('@scheduleSchedulingAddScheduling'), Yii::$app->view->params['permList'])) {
            $schedulePermison = 1;
        }
        if (!isset($dataList['dailyDetail']) || empty($dataList['dailyDetail'])) {
            $clear = 1;
        }
        $this->result['schedulePermison'] = $schedulePermison;
        $this->result['scheduleConfPermison'] = $schedulePermison;
        $this->result['clear'] = $clear;
        return Json::encode($this->result);
    }

    public function findOnceDepartment($id) {
        $hasRecord = OnceDepartment::find()->select(['id'])->where(['id' => $id, 'spot_id' => $this->spotId])->asArray()->one();
        return $hasRecord;
    }

    /**
     * Displays a single Appointment model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findAppointInfo($id),
        ]);
    }

    /**
     * Creates a new Appointment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Appointment();
        $model->scenario = 'create';
        $model->doctor_id = Yii::$app->request->get('doctor_id');
        $appointmentType = Appointment::checkAppointmentType();
        $doctorInfo = [];
        $hasAppointmentDoctor = false;
        $onlyAppointmentDoctor = false;

        if (in_array(1, $appointmentType) && count($appointmentType) == 1) {
            $onlyAppointmentDoctor = true;
            $model->scenario = 'appointmentOne'; //当预约类型只有医生预约时，医生为必填项
        }
        if (in_array(1, $appointmentType)) {
            $hasAppointmentDoctor = true;
            $time = Yii::$app->request->get('date');
            $model->doctor_id = Yii::$app->request->get('doctor_id'); //预约医生
            $model->type = Yii::$app->request->get('type'); //预约类型
            $model->time = strtotime($time); //预约时间
            $model->appointmentDate = date('Y-m-d', strtotime($time));
            $doctorInfo = $this->getDoctorInfo(); //当可按医生预约时，查询可预约医生列表
        }
        $patientId = Yii::$app->request->get('patientId');
        if ($patientId) {
            $patientInfo = Patient::find()->select(['id', 'username', 'head_img', 'sex', 'iphone', 'birthday', 'patient_source'])->where(['id' => $patientId])->asArray()->one();
            if ($patientInfo) {
                $model->patient_id = $patientInfo['id'];
                $model->username = $patientInfo['username'];
                $model->head_img = $patientInfo['head_img'];
                $model->sex = $patientInfo['sex'];
                $model->iphone = $patientInfo['iphone'];
                $model->birthday = $patientInfo['birthday'] == 0 ? '' : date('Y-m-d', $patientInfo['birthday']);
                $model->hourMin = $patientInfo['birthday'] == 0 ? '' : date('H:i', $patientInfo['birthday']);
                $model->patient_source = $patientInfo['patient_source'];
                $patientModal = Patient::find()->select(['id', 'username', 'head_img', 'sex', 'iphone', 'birthday', 'spot_id', 'create_time'])->where(['id' => $patientId, 'spot_id' => $this->parentSpotId])->one();
            }
        } else {
            $patientModal = new Patient();
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            //创建患者信息
            $firstRecord = PatientRecord::getFirstRecord($patientModal->id);
            $patientModal->scenario = 'appointment';
            $patientModal->username = $model->username;
            $patientModal->head_img = $model->head_img;
            $patientModal->sex = $model->sex;
            $patientModal->iphone = $model->iphone;
            $patientModal->birthday = strtotime($model->birthday . ' ' . $model->hourMin);
            $patientModal->remark = '';
            $patientModal->personalhistory = '';
            $patientModal->genetichistory = '';
            $patientModal->patient_source = $model->patient_source;
            $patientModal->first_record = $firstRecord;
//            $patientModal->appointment_origin = $model->appointment_origin;
            $patientResult = $patientModal->save();
            if ($patientResult) {
                //创建就诊记录
                $spotTypeInfo = $this->findTypeTime($model->type);
                $patientRecordModal = new PatientRecord();
                $patientRecordModal->patient_id = $patientModal->id;
                $patientRecordModal->status = 1;
                $patientRecordModal->type = $model->type;
                $patientRecordModal->type_description = $spotTypeInfo['type'];
                $patientRecordModal->type_time = $spotTypeInfo['time'];
                $patientRecordModal->doctor_id = $model->doctor_id;
                $patientRecordModal->create_time = time();
                
                $recordResult = $patientRecordModal->save();
                if ($recordResult) {
                    $model->patient_id = $patientModal->id;
                    $model->record_id = $patientRecordModal->id;
                    $model->create_time = time();
                    $model->update_time = time();
                    if ($model->save()) {
                        $result = Common::curlPost(Yii::$app->params['hisApiHost'] . Yii::getAlias('@hisCommonSendAppointmentText'), ['id' => $model->id]);
                        Yii::$app->getSession()->setFlash('success', '保存成功');
                        Yii::info($model->id, '预约id');
                        Yii::info(Yii::$app->params['hisApiHost'] . Yii::getAlias('@hisCommonSendAppointmentText'), 'hisapi预约短信地址');
                        Yii::info($result, '预约成功短信');
                        $returnUrl = Yii::$app->request->get('return');
                        if ($returnUrl == 'appointment-detail') {
                            return $this->redirect([$returnUrl]);
                        } else if ($returnUrl == 'list') {
                            return $this->redirect([$returnUrl, 'appointment[type]' => '1']);
                        } else {
                            return $this->redirect(['appointment-detail']);
                        }
                    }
                }
            }
        }
        return $this->render('create', [
                    'model' => $model,
                    'doctorInfo' => $doctorInfo,
                    'appointmentType' => $appointmentType,
                    'hasAppointmentDoctor' => $hasAppointmentDoctor,
                    'onlyAppointmentDoctor' => $onlyAppointmentDoctor
        ]);
    }

    /**
     * @return 获取当前诊所下的所有开放预约的医生列表
     */
    protected function getDoctorInfo() {
        $query = new Query();
        $query->from(['a' => User::tableName()]);
        $query->select(['a.id', 'a.username', 'b.department_id', 'c.name']);
        $query->leftJoin(['b' => UserSpot::tableName()], '{{a}}.id = {{b}}.user_id');
        $query->leftJoin(['c' => SecondDepartment::tableName()], '{{b}}.department_id = {{c}}.id');
        $query->where(['a.occupation' => 2, 'a.status' => 1, 'b.spot_id' => $this->spotId, 'b.status' => 1]);
        $query->indexBy('id');
        return $query->all();
    }

    /**
     * 预约字段更改发送短信设置比较信息
     * @param object $model
     * @return array
     */
    protected function setCompareArray($model) {
        $oldModel = array();
        $oldModel['iphone'] = $model->iphone;
        $oldModel['second_department_id'] = $model->second_department_id;
        $oldModel['doctor_id'] = $model->doctor_id;
        $oldModel['type'] = $model->type;
        $oldModel['time'] = $model->time;
        return $oldModel;
    }

    /**
     * Updates an existing Appointment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findAppointInfo($id, 1);
        $oldModel = $this->setCompareArray($model);
        $model->scenario = 'update';
        $doctorInfo = [];
        $appointmentType = Appointment::checkAppointmentType();
        $hasAppointmentDoctor = false;
        $onlyAppointmentDoctor = false;
        if (in_array(1, $appointmentType) && count($appointmentType) == 1) {
            $onlyAppointmentDoctor = true;
            $model->scenario = 'appointmentOne'; //当预约类型只有医生预约时，医生为必填项
        } else {
            $model->doctor_id = 0; //当按科室预约时，医生去掉
        }
        if (in_array(1, $appointmentType)) {
            $hasAppointmentDoctor = true;
            $doctorInfo = $this->getDoctorInfo(); //当可按医生预约时，查询可预约医生列表
        }
        $model->appointmentDate = date('Y-m-d', $model->time);
        $model->oldType = $oldModel['type'];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $patientModal = Patient::find()->select(['id', 'username', 'head_img', 'sex', 'iphone', 'birthday', 'spot_id', 'update_time'])->where(['id' => $model->patient_id])->one();
            $patientModal->scenario = 'appointment';
            $patientModal->username = $model->username;
            $patientModal->head_img = $model->head_img;
            $patientModal->sex = $model->sex;
            $patientModal->iphone = $model->iphone;
            $patientModal->birthday = strtotime($model->birthday . ' ' . $model->hourMin);
            $patientModal->update_time = time();
            $patientModal->patient_source = $model->patient_source;
            $patientResult = $patientModal->save();
            if ($model->editStatus) {
                $spotTypeInfo = $this->findTypeTime($model->type);
                if ($spotTypeInfo) {//若是没有预约服务类型,则维持原来历史的预约服务类型
                    $rows = ['type' => $model->type, 'type_time' => $spotTypeInfo['time'], 'type_description' => $spotTypeInfo['type']];
                    if($oldModel['type'] != $model->type || $oldModel['doctor_id'] != $model->doctor_id){
                        //查询医生加服务类型关联的诊金
                        $medicalFee = UserAppointmentConfig::getMedicalFee($model->doctor_id,$model->type);
                        $rows['price'] = $medicalFee['price'];
                        $rows['record_price'] = $medicalFee['price'];
                    }
                    Yii::$app->db->createCommand()->update(PatientRecord::tableName(), $rows, ['id' => $model->record_id, 'spot_id' => $this->spotId])->execute();
                }
            }
            if ($patientResult) {
                $model->update_time = time();
                $model->save();
            }

            if ($oldModel['iphone'] != $model->iphone || $oldModel['second_department_id'] != $model->second_department_id || $oldModel['doctor_id'] != $model->doctor_id || $oldModel['type'] != $model->type || $oldModel['time'] != $model->time) {
                $result = Common::curlPost(Yii::$app->params['hisApiHost'] . Yii::getAlias('@hisCommonSendAppointmentText'), ['id' => $model->id]);
                Yii::info($model->id, '预约id');
                Yii::info(Yii::$app->params['hisApiHost'] . Yii::getAlias('@hisCommonSendAppointmentText'), 'hisapi预约短信地址');
                Yii::info($result, '预约成功短信');
            }
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['list', 'appointment[type]' => '1']);
        } else {
            $deleteStatus = false;
            //判断原医生是否被禁用或者删除，若是，则默认回填
            if (!isset($doctorInfo[$model->doctor_id])) {
                $doctorInfo[$model->doctor_id] = [
                    'id' => $model->doctor_id,
                    'username' => $model->doctorName,
                ];
                $deleteStatus = true;
            }
            if ($model->errors) {
                $model->doctorName = User::getUserInfo($model->doctor_id, ['username'])['username'];
            }
            if (!$model->errors) {
                $model->hourMin = $model->birthday != 0 ? date('H:i', $model->birthday) : '';
                $model->birthday = $model->birthday != 0 ? date('Y-m-d', $model->birthday) : '';
            }
            return $this->render('update', [
                        'model' => $model,
                        'doctorInfo' => $doctorInfo,
                        'appointmentType' => $appointmentType,
                        'hasAppointmentDoctor' => $hasAppointmentDoctor,
                        'onlyAppointmentDoctor' => $onlyAppointmentDoctor,
                        'deleteStatus' => $deleteStatus
            ]);
        }
    }

    public function findAppointInfo($id, $status = null) {
        $query = new ActiveQuery(Appointment::className());
        $query->from(['a' => Appointment::tableName()]);
        $query->select(['b.patient_number patientNumber', 'a.appointment_operator', 'a.appointment_creater', 'a.id', 'a.appointment_origin', 'a.record_id', 'a.create_time', 'a.update_time', 'c.type', 'c.status', 'c.type_description', 'b.patient_source', 'a.patient_id', 'a.second_department_id', 'a.time', 'a.doctor_id', 'a.remarks', 'a.illness_description', 'b.username', 'b.sex', 'b.iphone', 'b.head_img', 'b.birthday', 'doctorName' => 'd.username', 'departmentName' => 'e.name', 'appointment_cancel_operator', 'appointment_cancel_reason', 'cancel_online']);
        $query->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id');
        $query->leftJoin(['c' => PatientRecord::tableName()], '{{a}}.record_id = {{c}}.id');
        $query->leftJoin(['d' => User::tableName()], '{{a}}.doctor_id = {{d}}.id');
        $query->leftJoin(['e' => SecondDepartment::tableName()], '{{a}}.second_department_id = {{e}}.id');
        $query->where(['a.id' => $id, 'a.spot_id' => $this->spotId]);
        $query->andFilterWhere(['c.status' => $status]);
        $model = $query->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * Deletes an existing Appointment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {

        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $doctorId = Yii::$app->request->get('doctor_id'); //医生ID
            $entrance = Yii::$app->request->get('entrance');
            $headerType = Yii::$app->request->get('header_type');
            $time = Yii::$app->request->get('time');
            $model = PatientRecord::findOne(['id' => $id, 'spot_id' => $this->spotId]);
            if ($model === null) {
                throw new NotFoundHttpException('你所请求的页面不存在');
            }
            $appointmentModel = Appointment::getAppointment($id);
            if ($appointmentModel === null) {
                throw new NotFoundHttpException('你所请求的页面不存在');
            }
            $appointmentStatus = PatientRecord::getAppointmentStatus($id);
            $isCanCancel = true;
            if ($appointmentStatus['status'] != 1) {
                $isCanCancel = false;
            } else if ($appointmentStatus['status'] == 1 && strtotime(date("Y-m-d", $appointmentStatus['time'])) + 86400 <= strtotime(date('Y-m-d'))) {
                $isCanCancel = false;
            }
            if (!$isCanCancel) {
                return [
                    'forceClose' => true,
                    'forceType' => 2,
                    'forceMessage' => '取消预约失败',
                    'forceReloadPage' => true,
                ];
            }
            $appointmentModel->scenario = 'cancelAppointment';
            if (isset($entrance) && $entrance == 1) {
                $cancelText = Html::a('取消', ['@apiAppointmentMessage', 'doctor_id' => $doctorId, 'header_type' => $headerType, 'time' => $time, 'entrance' => $entrance], ['class' => 'btn btn-cancel btn-form', 'role' => 'modal-remote']);
            } else {
                $cancelText = Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]);
            }
            $appointmentModel->appointment_cancel_operator = $this->userInfo->id;
            if ($appointmentModel->load(Yii::$app->request->post()) && $appointmentModel->save() && $model->load(Yii::$app->request->post()) && $model->save()) {
                return [
                    'forceClose' => true,
                    'forceReloadPage' => true,
                    'forceMessage' => '操作成功'
                ];
            } else {
                return [
                    'title' => "关闭预约",
                    'content' => $this->renderAjax('_cancelAppointment', [
                        'model' => $appointmentModel,
                        'recordModel' => $model,
                        'record_id' => $id
                    ]),
                    'footer' => $cancelText .
                    Html::button('确定', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * Finds the Appointment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Appointment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Appointment::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    public function actionCopyConfig($prevWeekStartDate) {

        if (Yii::$app->request->isAjax) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $begin = strtotime($prevWeekStartDate);
                $db = Yii::$app->db;
                $weekTime = 60 * 60 * 24 * 7;
                $end = $begin + $weekTime;

                AppointmentConfig::deleteAll('begin_time >= :begin And begin_time <= :end AND spot_id = :gender', [':begin' => $end, ':end' => $end + $weekTime, ':gender' => $this->spotId]);
                Yii::$app->response->format = Response::FORMAT_JSON;
                $spotConfig = SpotConfig::getConfig();
                $spotConfigBegin = $spotConfig['begin_time'];
                $spotConfigEnd = $spotConfig['end_time'];
                for ($begin; $begin < $end;) {
                    $model = AppointmentConfig::find()->select(['spot_id', 'department_id', 'doctor_count', 'begin_time', 'end_time', 'create_time', 'update_time'])->where(['spot_id' => $this->spotId, 'user_id' => 0])->andWhere(['between', 'begin_time', $begin, $begin + 60 * 60 * 24 - 1])->asArray()->all();
                    if (!empty($model)) {
                        foreach ($model as $key => $val) {

                            $model[$key]['begin_time'] = $val['begin_time'] + $weekTime;
                            $model[$key]['end_time'] = $val['end_time'] + $weekTime;
                            $model[$key]['create_time'] = time();
                            $model[$key]['update_time'] = time();
                        }

                        $res = $db->createCommand()->batchInsert(AppointmentConfig::tableName(), ['spot_id', 'department_id', 'doctor_count', 'begin_time', 'end_time', 'create_time', 'update_time'], $model)->execute();
                    }
                    $begin = $begin + 60 * 60 * 24;
                }
                $dbTrans->commit();
                return [
                    'forceClose' => true,
                    'forceMessage' => '复制成功',
                    'forceCallback' => 'main.getAppointmentSetInfo()'
                ];
            } catch (Exception $e) {
                $dbTrans->rollBack();
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     *
     * @return 复制医生的可预约时间
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function actionCopyTimeConfig() {
        $prevWeekStartDate = Yii::$app->request->get('prevWeekStartDate');

        $begin = strtotime($prevWeekStartDate);
        $end = $begin + 60 * 60 * 24 * 7;
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            AppointmentConfig::deleteAll('begin_time >= :begin And begin_time <= :end AND spot_id = :gender', [':begin' => $end, ':end' => $end + 60 * 60 * 24 * 7, ':gender' => $this->spotId]);
            $serverTypeCache = [];
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                for ($begin; $begin < $end;) {
                    $model = AppointmentConfig::find()->select(['id', 'spot_id', 'user_id', 'begin_time', 'end_time', 'create_time', 'update_time'])->where(['spot_id' => $this->spotId])->andWhere(['between', 'begin_time', $begin, $begin + 60 * 60 * 24 - 1])->andWhere(['>', 'user_id', 0])->asArray()->all();
                    if (!empty($model)) {
                        foreach ($model as $key => $val) {
                            $cacheKey = $val['spot_id'] . '-' . $val['user_id'];
                            $doctorTypes = $serverTypeCache[$cacheKey];
                            if (empty($doctorTypes)) {
                                //医生可提供的服务
                                $doctorTypes = UserAppointmentConfig::getDoctorServeType($val['user_id'], $val['spot_id']);
                                $serverTypeCache[$cacheKey] = $doctorTypes;
                            }
                            //有效的预约关联服务
                            $copyTimeAndServerItem = $this->getCopyTimeAndServerItem($doctorTypes, $val['id']);
                            if (count($copyTimeAndServerItem) > 0) {
                                //插入预约设置
                                $modelConfig = new AppointmentConfig();
                                $modelConfig->spot_id = $val['spot_id'];
                                $modelConfig->user_id = $val['user_id'];
                                $modelConfig->begin_time = $val['begin_time'] + 60 * 60 * 24 * 7;
                                $modelConfig->end_time = $val['end_time'] + 60 * 60 * 24 * 7;
                                $modelConfig->create_time = time();
                                $modelConfig->update_time = time();
                                if ($modelConfig->save()) {
                                    //插入关联服务
                                    foreach ($copyTimeAndServerItem as $k => $v) {
                                        $timeAndServer = new AppointmentTimeAndServer();
                                        $timeAndServer->time_config_id = $modelConfig->id;
                                        $timeAndServer->spot_type_id = $v['spot_type_id'];
                                        $timeAndServer->spot_id = $val['spot_id'];
                                        $timeAndServer->create_time = time();
                                        $timeAndServer->update_time = time();
                                        $timeAndServer->save();
                                    }
                                }
                            }
                        }
                    }
                    $begin = $begin + 60 * 60 * 24;
                }
                $dbTrans->commit();
            } catch (Exception $e) {
                Yii::error(json_encode($e->errorInfo, true), 'copy-time-config');
                $dbTrans->rollBack();
            }

            return ['forceClose' => true, 'forceMessage' => "复制成功", 'forceCallback' => 'main.getAppointmentSetInfo()'];
        } else {

            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @return 返回预约类型相应时间
     * @param 预约类型 $type
     */
    public function findTypeTime($type) {

        $info = SpotType::find()->select(['type', 'time'])->where(['id' => $type, 'spot_id' => $this->spotId])->asArray()->one();

        return $info;
    }

    public function actionCloseAppointment() {
        $model = new AppointmentTimeConfig();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->isPost) {
                $dataList = Yii::$app->request->post('close_appointment');
                return $this->saveCloseAppointment($dataList);
            } else {
                $closeInfo = AppointmentTimeConfig::find()->asArray()->where(['spot_id' => $this->spotId])->orderBy(['begin_time' => SORT_ASC])->all();
                if (empty($closeInfo)) {
                    $closeInfo[0] = [
                        'begin_time' => 0,
                        'end_time' => 0,
                        'close_reason' => ''
                    ];
                }
                return [
                    'title' => "关闭预约设置",
                    'content' => $this->renderAjax('closeAppointment', [
                        'closeAppointment' => $closeInfo
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn btn-default btn-save btn-form'])
                ];
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    public function saveCloseAppointment($dataList) {
        $insertList = [];
        $needInsert = false;
        $spotAppointmentTime = SpotConfig::find()->asArray()->where(['spot_id' => $this->spotId])->one();
        $spotBeginTime = strtotime(date("Y-m-d ") . $spotAppointmentTime['begin_time']);
        if (isset($dataList['close_info']) && !empty($dataList['close_info'])) {
            foreach ($dataList['close_info'] as $key => $v) {
                $start_date = strtotime($v['close_begin_time']);
                $end_date = strtotime($v['close_end_time']);
                if ($v['close_begin_time'] != '' && $v['close_end_time'] != '' && $v['close_reason'] != '') {
                    if ($start_date >= $end_date) {
                        $this->result['errorCode'] = 1011;
                        $this->result['msg'] = '关闭预约开始时间不能大于结束时间';
                        return $this->result;
                    }
                    if (mb_strlen($v['close_reason']) > 25) {
                        $this->result['errorCode'] = 1016;
                        $this->result['msg'] = '关闭预约原因不能大于25个字';
                        return $this->result;
                    }
//                        if ($start_date < time()) {
//                            $this->result['errorCode'] = 1011;
//                            $this->result['msg'] = '预约开始时间不能小于当前时间';
//                            return Json::encode($this->result);
//                        } else if ($end_date < time()) {
//                            $this->result['errorCode'] = 1011;
//                            $this->result['msg'] = '预约结束时间不能小于当前时间';
//                            return Json::encode($this->result);
//                        }

                    foreach ($dataList['close_info'] as $child => $value) {
                        if ($value['close_begin_time'] != '' && $value['close_end_time'] != '' && $value['close_reason'] != '') {
                            if ($key != $child) {
                                if (($value['close_begin_time'] <= $v['close_begin_time'] && $v['close_begin_time'] < $value['close_end_time']) || ($value['close_begin_time'] < $v['close_end_time'] && $v['close_end_time'] <= $value['close_end_time'])) {
                                    $this->result['errorCode'] = 1012;
                                    $this->result['msg'] = '预约时间段不允许重叠';
                                    return $this->result;
                                }
                            }
                            $closeBeginTime = strtotime(date('Y-m-d ') . date('H:i', strtotime($value['close_begin_time'])));
                            $closeEndTime = strtotime(date('Y-m-d ') . date('H:i', strtotime($value['close_end_time'])));
                            $spotEndTime = strtotime(date("Y-m-d ") . $spotAppointmentTime['end_time']);
                            $spotBeginTime = strtotime(date("Y-m-d ") . $spotAppointmentTime['begin_time']);

                            if ($closeBeginTime < $spotBeginTime) {
                                $this->result['errorCode'] = 1015;
                                $this->result['msg'] = '关闭预约开始时间不能小于诊所预约开始时间';
                                return $this->result;
                            }
                            if ($spotEndTime < $closeEndTime) {
                                $this->result['errorCode'] = 1015;
                                $this->result['msg'] = '关闭预约结束时间不能大于诊所预约结束时间';
                                return $this->result;
                            }
                        } else {
                            $this->result['errorCode'] = 1014;
                            $this->result['msg'] = '请将表单填写完整';
                            return $this->result;
                        }
                    }
                    $insertList[] = [$this->spotId, $start_date, $end_date, $v['close_reason'], time(), time()];
                } else {
                    $this->result['errorCode'] = 1014;
                    $this->result['msg'] = '请将表单填写完整';
                    return $this->result;
                }
            }
            $needInsert = true;
        }
        AppointmentTimeConfig::deleteAll([ 'spot_id' => $this->spotId]);
        if ($needInsert) {
            Yii::$app->db->createCommand()->batchInsert(AppointmentTimeConfig::tableName(), ['spot_id', 'begin_time', 'end_time', 'close_reason', 'create_time', 'update_time'], $insertList)->execute();
        }
        $this->result['errorCode'] = 0;
        $this->result['msg'] = '保存成功';
        $this->result['data'] = $dataList;
        return $this->result;
    }

    /**
     * 预约时间模板管理页面.
     */
    public function actionAppointmentTimeTemplate() {
        $dataProvider = new ActiveDataProvider([
            'query' => AppointmentTimeTemplate::find()->select(['id', 'name', 'appointment_times'])->andWhere(['spot_id' => $this->spotId]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => ['id']
            ]
        ]);

        return $this->render('appointmentTimeTemplate', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 新建预约时间模板.
     */
    public function actionCreateTimeTemplate() {
        $model = new AppointmentTimeTemplate();
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $this->result['errorCode'] = 0;
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->result;
            } else {
                $this->result['errorCode'] = true;

                $errorStr = '';
                $firstError = array_shift($model->errors);
                if (!empty($firstError)) {
                    $errorStr = $firstError;
                }
                $this->result['msg'] = $errorStr;
                return $this->result;
            }
        } else {

            $spotAppointmentTime = SpotConfig::getConfig(['id', 'begin_time', 'end_time']);
            $timeConfig = [
                'begin_time' => 0,
                'end_time' => 0
            ];
            if ($spotAppointmentTime && $spotAppointmentTime['begin_time']) {
                $timeConfig = [
                    'begin_time' => $spotAppointmentTime['begin_time'],
                    'end_time' => $spotAppointmentTime['end_time']
                ];
            }

            return $this->render('createAppointmentTimeTemplate', [
                        'model' => $model,
                        'timeConfig' => $timeConfig,
            ]);
        }
    }

    /**
     * 修改预约时间模板.
     */
    public function actionUpdateTimeTemplate($id) {
        $model = $this->findTimeTemplateModel($id);
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
//             $model->load(Yii::$app->request->post());

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $this->result['errorCode'] = 0;
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->result;
            } else {
                $this->result['errorCode'] = true;
                $errorStr = '';
                $firstError = array_shift($model->errors);
                if (!empty($firstError)) {
                    $errorStr = $firstError;
                }
                $this->result['msg'] = $errorStr;
                return $this->result;
            }
        } else {

            $spotAppointmentTime = SpotConfig::getConfig();
            $timeConfig = [
                'begin_time' => 0,
                'end_time' => 0
            ];
            if ($spotAppointmentTime && $spotAppointmentTime['begin_time']) {
                $timeConfig = [
                    'begin_time' => $spotAppointmentTime['begin_time'],
                    'end_time' => $spotAppointmentTime['end_time']
                ];
            }

            return $this->render('updateAppointmentTimeTemplate', [
                        'model' => $model,
                        'timeConfig' => $timeConfig,
            ]);
        }
    }

    /**
     * 删除预约时间模板.
     */
    public function actionDeleteTimeTemplate($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            $this->findTimeTemplateModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    protected function findTimeTemplateModel($id) {
        if (($model = AppointmentTimeTemplate::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @param Array $serverTypes 医生提供的服务
     * @param int $timeConfigId 预约设置id
     * @return Array result
     * @desc  获取有效的关联服务。
     */
    public function getCopyTimeAndServerItem($serverTypes, $timeConfigId) {

        if (empty($serverTypes) || count($serverTypes) < 1) {
            return [];
        }

        $result = [];
        $timeAndServer = AppointmentTimeAndServer::find()->select(['spot_type_id'])->where(['time_config_id' => $timeConfigId])->asArray()->all();
        foreach ($timeAndServer as $k => $v) {
            if (in_array($v['spot_type_id'], $serverTypes)) {
                $result[] = $timeAndServer[$k];
            }
        }

        return $result;
    }

}
