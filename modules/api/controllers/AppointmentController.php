<?php

/**
 * @property ajax请求api控制器
 * @property 预约模块api
 */

namespace app\modules\api\controllers;

use app\modules\spot\models\SpotConfig;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use app\modules\user\models\UserSpot;
use Yii;
use yii\base\Exception;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use app\modules\make_appointment\models\Appointment;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\db\Query;
use app\modules\patient\models\Patient;
use app\modules\make_appointment\models\AppointmentConfig;
use app\modules\spot_set\models\OnceDepartment;
use app\modules\patient\models\PatientRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\modules\schedule\models\Scheduling;
use app\modules\make_appointment\models\DoctorTimeConfig;
use yii\web\NotAcceptableHttpException;
use yii\db\ActiveQuery;
use app\modules\make_appointment\models\AppointmentTimeConfig;
use app\modules\spot_set\models\SpotType;
use app\specialModules\recharge\models\CardRecharge;
use app\modules\spot_set\models\UserAppointmentConfig;
use app\common\Common;
use app\modules\make_appointment\models\AppointmentTimeTemplate;
use app\modules\spot_set\models\SecondDepartmentUnion;

class AppointmentController extends CommonController
{

    public function behaviors() {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
                    'appointment-config' => ['post'],
                    'doctor-info' => ['post'],
                    'doctor-time' => ['post'],
                    'doctor-config' => ['post'],
                    'time-config' => ['post'],
                    'get-appointment-config' => ['post'],
                    'get-appointment-detail' => ['post'],
                    'get-doctor-department' => ['post'],
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    /**
     * index
     * @param int $appointment_type 预约类型
     * @param int $department_id 二级科室id
     * @param int $doctor_id 医生id
     * @param int $start_date 开始时间(如：2017-01-01)
     * @param int $end_date 结束时间(如：2017-01-01)
     * @return boolean success true为成功，false为失败,默认为true
     * @return int errorCode 错误代码(0-成功,1001-参数错误,默认为0)
     * @return array appoint_daily_total 上午和下午对应的预约的总人数
     * @return array appoint_daily_detail 具体的预约时间的人数
     * @desc 预约管理-人数统计接口(获取诊所／医生的预约的人数统计的api)
     */
    public function actionIndex() {
        $type = Yii::$app->request->post('type');
        $doctor_id = Yii::$app->request->post('doctor_id');
        $start_date = Yii::$app->request->post('start_date');
        $end_date = Yii::$app->request->post('end_date');
        return Appointment::getAppointmentDetail($start_date, $end_date, $doctor_id, $type);
    }

    /**
     * get-doctor-appointment
     * @param int $start_date 开始时间(如：2017-01-01)
     * @param int $end_date 结束时间(如：2017-01-01)
     * 
     * @return boolean success true为成功，false为失败,默认为true
     * @return int errorCode 错误代码(0-成功,1001-参数错误,默认为0)
     * @return array appoint_daily_total 上午和下午对应的预约的总人数
     * @return array appoint_daily_detail 具体的预约时间的人数
     * 
     * @desc 医生工作台-获取自己的预约信息
     */
    public function actionGetDoctorAppointment() {

        $doctor_id = $this->userInfo->id;
        $start_date = Yii::$app->request->post('start_date');
        $end_date = Yii::$app->request->post('end_date');
        return Appointment::getAppointmentDetail($start_date, $end_date, $doctor_id);
    }

    /**
     * appointment-config
     * @param int $start_date 开始时间(如：2017-01-01)
     * @param int $end_date 结束时间(如：2017-01-01)
     * @return boolean success true为成功，false为失败
     * @return int errorCode 错误代码(0-成功,1001-参数错误,默认为0)
     * @return array data 对应的一级科室列表以及对应的可预约时间和医生数量 例如 [{name: "内科", id: "14", daily_detail: {2017-03-07: [{time: "10:20-11:10", doctor_count: "2"}]}}] 
     * @desc 获取当前诊所科室预约设置的对应的时间段的数据
     */
    public function actionAppointmentConfig() {
        $row = [];
        $data = [];
        $start_date = Yii::$app->request->post('start_date');
        $end_date = Yii::$app->request->post('end_date');
        $query = new Query();
        $query->from(['a' => AppointmentConfig::tableName()]);
        $query->select(['a.begin_time', 'a.end_time', 'a.department_id', 'a.doctor_count']);
        $query->leftJoin(['b' => OnceDepartment::tableName()], '{{a}}.department_id = {{b}}.id');
        $query->where(['a.spot_id' => $this->spotId]);
        $query->andWhere('a.begin_time >= :begin_time', [':begin_time' => strtotime($start_date)]);
        $query->andWhere('a.end_time <=:end_time', [':end_time' => strtotime($end_date) + 86400]);
        $list = $query->all();
        $onceDepartmentList = OnceDepartment::find()->select(['id', 'name'])->where(['spot_id' => $this->spotId])->asArray()->all();
        foreach ($onceDepartmentList as $value) {
            $row[$value['id']]['name'] = htmlspecialchars($value['name']);
            $row[$value['id']]['id'] = $value['id'];
            $row[$value['id']]['daily_detail'] = [];
        }
        foreach ($list as $key => $v) {
            if (isset($row[$v['department_id']]['daily_detail'])) {
                $row[$v['department_id']]['daily_detail'][date('Y-m-d', $v['begin_time'])][] = [
                    'time' => date('H:i', $v['begin_time']) . '-' . date('H:i', $v['end_time']),
                    'doctor_count' => $v['doctor_count']
                ];
            }
        }
        foreach ($row as $value) {
            $data[] = $value;
        }
        $this->result['data'] = $data;
        return json_encode($this->result, true);
    }

    /**
     * message
     * @param string $time 预约表格上的预约时间段,如:2017-01-01
     * @param int $appointment_type 预约类型
     * @param int $department_id 二级科室id
     * @param int $doctor_id 医生id
     * @param int $entrance 判断预约详情列表字段里备注／病情描述字段的显示(1-显示备注字段，2-显示病情描述字段)
     * @param int $header_type 头部点击(1-当前所属日期内的预约详情,2-当前时间内的预约详情)
     * 
     * @return string title 标题描述
     * @return array content 渲染的视图内容
     * @return string footer 关闭按钮
     * @desc 返回某时间段的预约信息列表
     * @throws NotFoundHttpException
     */
    public function actionMessage($time) {
        if (Yii::$app->request->isAjax) {
            
//            $query = new Query();
//            $query->from(['a' => SecondDepartment::tableName()]);
//            $query->leftJoin(['b' => SecondDepartmentUnion::tableName()], '{{a}}.id = {{b}}.second_department_id');
//            $query->select(['a.id', 'a.name']);
//            $query->where(['a.spot_id' => $this->parentSpotId, 'b.spot_id' => $this->spotId, 'a.status' => 1]);
//            $secondDepartmentInfo = $query->all();
//            
            $query = new Query();
            $query->from(['a' => UserSpot::tableName()]);
            $query->leftJoin(['b' => User::tableName()], '{{a}}.user_id = {{b}}.id');
            $query->select(['id' => 'a.user_id', 'b.username']);
            $query->where(['a.spot_id' => $this->spotId, 'b.status' => 1, 'b.occupation' => 2]);
            $doctorInfo = $query->all();//获取正常医生
            
            $spotTypeList = SpotType::getSpotType('status=1');
            $appointmentType = Yii::$app->request->get('type'); //预约类型
            $doctorId = Yii::$app->request->get('doctor_id'); //医生ID
            $entrance = Yii::$app->request->get('entrance');
            $headerType = Yii::$app->request->get('header_type');
            $time = Yii::$app->request->get('time');
            $pjax = Yii::$app->request->get('_pjax');
            $endDate = Yii::$app->request->get('endDate');
            $username = Yii::$app->request->get('username');
            $iphone = Yii::$app->request->get('iphone');
            $date_formate = Yii::$app->request->get('date_formate');
            $spotConfig = SpotConfig::find()->select(['reservation_during', 'begin_time', 'end_time'])->where(['spot_id' => $this->spotId])->asArray()->one();

            Yii::$app->response->format = Response::FORMAT_JSON;

            $query = (new ActiveQuery(Appointment::className()));
            $query->from(['a' => Appointment::tableName()]);
            $query->select(['a.id','a.record_id', 'b.birthday', 'b.sex', 'a.remarks', 'a.illness_description', 'a.appointment_origin', 'b.iphone', 'c.type', 'c.type_description','c.status', 'firstRecord' => 'b.first_record', 'b.username', 'e.name as departmentName', 'a.time', 'd.username as doctorName','f.username as appointmentOperator']);

            $query->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id');
            $query->leftJoin(['c' => PatientRecord::tableName()], '{{a}}.record_id = {{c}}.id');
            $query->leftJoin(['d' => User::tableName()], '{{a}}.doctor_id = {{d}}.id');
            $query->leftJoin(['e' => SecondDepartment::tableName()], '{{a}}.second_department_id = {{e}}.id');
            $query->leftJoin(['f' => User::tableName()], '{{a}}.appointment_operator = {{f}}.id');
            if ($headerType == 1) {//头部点击
                if($date_formate == 1){
                    $timeSelectedHeader = $time;
                    $beginTime = strtotime($timeSelectedHeader. ' '.$spotConfig['begin_time']);
                    $endTime = strtotime($timeSelectedHeader. ' '.$spotConfig['end_time']);
                    $query->where(['between', 'a.time', $beginTime, $endTime]);
                }else{
                    $timeSelectedHeader = $time;
                    $beginTime = strtotime($timeSelectedHeader . ' 00:00:00');
                    $endTime = strtotime(($endDate ? $endDate : $timeSelectedHeader) . ' 23:59:59');
                    $query->where(['between', 'a.time', $beginTime, $endTime]);
                }
            } else {
                $query->where(['between', 'a.time', strtotime($time), strtotime($time) + $spotConfig['reservation_during'] * 60 - 1]);
            }
            $query->andWhere(['a.spot_id' => $this->spotId]);
//            $query->andWhere('c.status != :statue', [':statue' => 7]);
            $query->andWhere('c.status != 7 and c.status != 8');
            $query->andFilterWhere(['type' => $appointmentType])->andFilterWhere(['doctor_id' => $doctorId]);
            $query->andFilterWhere(['like','b.iphone',trim($iphone)]);
            $query->andFilterWhere(['like','b.username',trim($username)]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                        'time' => SORT_ASC,
                    ],
                    'attributes' => ['id', 'time']
                ],
                'pagination' => [
                    'pageSize' => $this->pageSize
                ],
            ]);
            if (!Yii::$app->request->isPjax) {
                $param = time();
            } else {
                $param = substr($pjax, 20);
            }
            $cardInfo = CardRecharge::getCardInfoByQueryNurse($dataProvider->query);
            return [
                'title' => "预约信息" . ' (' . date('Y-m-d', strtotime($time)) . ($endDate ? ' — '.$endDate : '') .')',
                'content' => $this->renderAjax('@app/modules/make_appointment/views/appointment/message', [
                    'secondDepartmentInfo' => $secondDepartmentInfo, 
                    'doctorInfo'=>$doctorInfo,
                    'spotTypeList' => $spotTypeList,
                    'dataProvider' => $dataProvider,
                    'entrance' => $entrance,
                    'param' => $param,
                    'cardInfo' => $cardInfo,
                    'time' => $time,
                    'doctorId' =>$doctorId,
                    'headerType' =>$headerType,
                ]),
                'footer' => Html::button('关闭', ['class' => 'btn btn-default btn-form close-btn-style', 'data-dismiss' => "modal"])
            ];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * doctor-info
     * @param int $id 二级科室id
     * @return int errorCode 错误代码(0-成功，1001-参数错误)
     * @return string msg 提示信息
     * @return array data 可预约医生信息列表
     * @desc 根据二级科室id来获取可预约医生列表
     */
    public function actionDoctorInfo() {
        $id = intval(Yii::$app->request->post('id'));
        $report = Yii::$app->request->post('report', 0);
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$id) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $query = new Query();
        $query->from(['a' => UserSpot::tableName()]);
        $query->leftJoin(['b' => User::tableName()], '{{a}}.user_id = {{b}}.id');
        $query->select(['id' => 'a.user_id', 'b.username']);
        $query->where(['a.department_id' => $id, 'a.spot_id' => $this->spotId, 'b.status' => 1, 'b.occupation' => 2]);
        if ($report == 0) {
            $query->andWhere(['a.status' => 1]);
        }
        $this->result['data'] = $query->all();
        return $this->result;
    }

    /**
     * doctor-time
     * @param int $id 医生id
     * @param int $type 预约类型id
     * 
     * @return int errorCode 错误代码(0-成功，1001-参数错误)
     * @return string msg 提示信息
     * @return array data 可预约时间列表
     *  
     * @desc 根据预约类型和预约医生，返回相应的可预约时间
     */
    public function actionDoctorTime() {

        $userId = intval(Yii::$app->request->post('doctorId'));
        $type = intval(Yii::$app->request->post('type'));
        $id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$userId || !$type) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        try{
            $rows = Appointment::getDoctorTime($userId, $type, $id);
        }catch (Exception $e){
            Common::monitorReport(Yii::getAlias('@apiAppointmentDoctorTime'),$e->getCode(),$e->getMessage());
        }

        $this->result['data'] = $rows;
        return $this->result;
    }

    /**
     * doctor-config
     * @param string $date 日期,如2017-01-01
     * @return int errorCode 错误代码(0-成功，1001-参数错误)
     * @return string msg 提示信息
     * @return array data 当前诊所的医生的可预约时间段和已预约预约时间段
     * @desc 返回医生预约时间段和已预约预约时间段(预约管理-预约详情调用)
     */
    public function actionDoctorConfig() {

        $date = Yii::$app->request->post('date');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $row = $this->getAppointment($date);

        foreach ($row as $key => &$val) {
            $i = 0;
            $n = 0;
            $rowKey = 0;
            $vCount = count($row[$key]);
            $kArr = [];
            foreach ($val as $k => &$v) {

                $preK = date('H:i', strtotime($date . '' . $k) - 600);
                if ($v['status'] == 1 && !$kArr) {
                    $rowKey = $rowKey != 0 ? $rowKey : $k;
                    $n++;
                } else if ($v['status'] == 1 && $kArr) {
                    if ($kArr[$i - 1] == $preK) {
                        $rowKey = $rowKey != 0 ? $rowKey : $k;
                        $n++;
                    } else {
//                        if($k=='17:00'&&$key==24){
//                            echo $rowKey.'****';
//                            echo $preK.'***********';
//                            echo($kArr[$i-1]);exit;
////                    echo $rowKey.'***'.$n.'***'.$i;exit;
//                        }
                        $val[$rowKey]['rowSpan'] = $n;
                        $rowKey = $k;
                        $n = 1;
                    }
                }
                if (($v['status'] != 1) || ($i == $vCount - 1)) {
                    $rowKey && $val[$rowKey]['rowSpan'] = $n;
                    $rowKey = 0;
                    $n = 0;
                }

                $kArr[] = $k;
                $i++;
            }
        }

        $this->result['data'] = $row;

        return $this->result;
    }

    /**
     * creatby-doctor
     * @param string $date 日期时间,2017-01-01 01:01
     * @param int $doctor_id 医生id
     * 
     * @return string title 标题描述
     * @return array content 渲染的视图内容
     * @return string footer 弹窗按钮
     * @desc 预约管理-预约详情-预约添加
     * @throws 预约管理-预约详情-预约添加
     * 
     */
    public function actionCreatbyDoctor() {
        $model = new Appointment();
        $model->scenario = 'createByDoctor';
        if (Yii::$app->request->isAjax) {
            $date = Yii::$app->request->get('date');
            $doctor_id = Yii::$app->request->get('doctor_id');

            if (!strtotime($date) || !$doctor_id) {
                throw new NotAcceptableHttpException('参数错误');
            }
            $time = strtotime($date);
            $rows = $this->getAppointment(date('Y-m-d', $time));
            $userAppointmentConfigInfo = UserAppointmentConfig::getUserSpotType($doctor_id); //获取医生关联的预约服务
            $hourMin = date('H:i', $time);
            $statusList = []; //医生关联的预约服务的可预约状态集合

            if (!empty($userAppointmentConfigInfo)) {
                foreach ($userAppointmentConfigInfo as $v) {
                    $typeTime = $time + $v['time'] * 60; //计算出预约时长(时间戳)
                    for ($i = $time; $i < $typeTime;) {
                        $begin = date('H:i', $i);
                        if ($rows[intval($doctor_id)][$begin]['status'] != 1) {
                            $statusList[$v['id']] = false;
                            break;
                        }
                        $i = $i + 600;
                    }
                }
            }
            $departmentInfo = UserSpot::getDepartmentInfo($doctor_id);

            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "选择预约信息",
                'content' => $this->renderAjax('@make_appointmentAppointmentCreatbyDoctor', [
                    'model' => $model,
                    'doctor_id' => $doctor_id,
                    'date' => $date,
                    'departmentInfo' => $departmentInfo,
                    'type' => $userAppointmentConfigInfo,
                    'statusList' => $statusList
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                Html::button('确认', ['class' => 'btn btn-default btn-form', 'id' => 'createAppointment'])
            ];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * 
     * @param string $date 预约日期，如 2017-01-01
     * @desc 返回预约医生的可预约时间和已预约时间
     * @return string[]|string[]|number[]|unknown[]
     */
    protected function getAppointment($date) {
        $spotConfig = SpotConfig::find()->select(['begin_time', 'end_time'])->where(['spot_id' => $this->spotId])->asArray()->one();
        $row = [];
        $start_date = strtotime($date . ' 00:00:00');
        $end_date = strtotime($date . ' 23:59:59');
        if (!$date || !$start_date || !$end_date) {
            $this->result['errorCode'] = 1001;
            $this->result['data'] = '';
            $this->result['msg'] = '参数错误';
            return $this->result;
        }

        /* 医生预约设置的时间 */
        $query = new Query();
        $query->from(['a' => AppointmentConfig::tableName()]);
        $query->select(['a.begin_time', 'a.end_time', 'a.user_id']);
        $query->where(['a.spot_id' => $this->spotId]);
        $query->andWhere('a.end_time >= :begin_time', [':begin_time' => strtotime($date)]);
        $query->andWhere('a.end_time <= :end_time', [':end_time' => strtotime($date) + 86400]);
        $query->andWhere('a.end_time >= :time', [':time' => time()]);
        $list = $query->all();

        if (!empty($list)) {

            $timeConfig = AppointmentTimeConfig::getSpotConfig($this->spotId);

            foreach ($list as $v) {

                $time = date('Y-m-d', $v['begin_time']);
                $beginConfigTime = strtotime($time . ' ' . $spotConfig['begin_time']);
                $endConfigTime = strtotime($time . ' ' . $spotConfig['end_time']);
                $beginTime = $v['begin_time'] > $beginConfigTime ? $v['begin_time'] : $beginConfigTime;
                $endTime = $v['end_time'] > $endConfigTime ? $endConfigTime : $v['end_time'];
                for ($t = $beginTime; $t < $endTime;) {
                    if (!empty($timeConfig)) {
                        if ($t >= time()) {
                            foreach ($timeConfig as $config) {
                                if ($t >= $config['begin_time'] && $t < $config['end_time']) {
                                    $row[$v['user_id']][date("H:i", $t)]['status'] = '2';
                                    break;
                                } else {
                                    $row[$v['user_id']][date("H:i", $t)]['status'] = '1';
                                }
                            }
                        }
                    } else if ($t >= time()) {
                        $row[$v['user_id']][date("H:i", $t)]['status'] = '1';
                    }
                    $t += 600;
                }
            }
        }
        /* 已被预约时间段 */
        $secondquery = new Query();
        $secondquery->from(['a' => Appointment::tableName()]);
        $secondquery->select(['a.time', 'a.doctor_id', 'c.type_time']);
        $secondquery->leftJoin(['b' => UserSpot::tableName()], '{{a}}.doctor_id = {{b}}.user_id');
        $secondquery->leftJoin(['c' => PatientRecord::tableName()], '{{a}}.record_id = {{c}}.id');
        $secondquery->where(['a.spot_id' => $this->spotId]);
        $secondquery->andWhere('b.status = :status', [':status' => 1]);
        $secondquery->andWhere('c.status != :statue', [':statue' => 7]);
//        $query->andWhere('c.status != 7 and c.status != 8');
        $secondquery->andWhere('a.time >= :begin_time', [':begin_time' => $start_date]);
        $secondquery->andWhere('a.time <=:end_time', [':end_time' => $end_date]);
        $appointList = $secondquery->all();
        if (!empty($appointList)) {
            foreach ($appointList as $key => $v) {
                $endTime = $v['time'] + $v['type_time'] * 60;
                for ($i = $v['time']; $i < $endTime;) {
                    $row[$v['doctor_id']][date("H:i", $i)]['status'] = 3;
                    $i += 600;
                }
            }
        }
        return $row;
    }

    /**
     * time-config
     * @param string $start_date 开始时间,如:2017-01-01
     * @param string $end_date 结束时间,如:2017-01-01
     * 
     * @return int errorCode 错误代码(0-成功,1001-参数错误)
     * @return array data 所有的医生在某时间段内的预约时间信息列表
     * @desc 医生预约时间设置，获取当前诊所内所有的医生在某时间段内的预约时间信息列表
     */
    public function actionTimeConfig() {
        $start_date = Yii::$app->request->post('start_date');
        $end_date = Yii::$app->request->post('end_date');
//        $start_date = Yii::$app->request->get('start_date');
//        $end_date = Yii::$app->request->get('end_date');
        if (!$start_date || !$end_date) {
            $this->result['errorCode'] = 1001;
            return Json::encode($this->result);
        }
        $model = new Scheduling();
        $doctorTimeConfig = new AppointmentConfig();
        $week_data = $model->getWeek($start_date, $end_date);
        $spot_id = $this->spotId; //诊所id
        Yii::info('this spot id ' . $this->spotId);
        Yii::info('cookie spot id ' . $_COOKIE['spotId']);
        Yii::info('cookie id ' . json_encode($_COOKIE));
        $worder_list = User::getWorkerList(0, 0, $spot_id, 2, true, 0, true); //获取所有医生
        $doctorList = array_column($worder_list, 'doctor_id');
        $doctorList = UserAppointmentConfig::getDoctorServiceType($doctorList);
        $doctorTypeList = array();
        foreach ($doctorList as $docId => $value) {
            $nameArr = explode(',', $value['typeNameList']);
            $idArr = explode(',', $value['typeIdList']);
            foreach ($nameArr as $key => $v) {
                $doctorTypeList[$docId][$key]['typeNameList'] = $nameArr[$key];
                $doctorTypeList[$docId][$key]['typeIdList'] = $idArr[$key];
            }
        }
        $schedule_list = $doctorTimeConfig->getTimeList($start_date, $end_date); //获取所有医生的预约时间
//        $department_list = User::getDepartmentInfo();
        $data = $this->formatSchedule($week_data, $worder_list, $schedule_list,$doctorTypeList);
        $this->result['data'] = $data;
        return Json::encode($this->result, JSON_ERROR_NONE);
    }

    /**
     * 
     * @param int $id 医生id
     * @return 
     * @desc 获取医生拥有的预约服务信息
     */
    public function actionGetAppointmentType() {

        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        if (!$id) {
            $this->result['errorCode'] = 1009; //参数错误
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $typeQuery = new Query();
        $typeQuery->from(['a' => UserAppointmentConfig::tableName()]);
        $typeQuery->select(['id' => 'a.spot_type_id', 'b.type']);
        $typeQuery->leftJoin(['b' => SpotType::tableName()], '{{a}}.spot_type_id = {{b}}.id');
        $typeQuery->where(['a.user_id' => $id, 'a.spot_id' => $this->spotId, 'b.status' => 1]);
        $typeResult = $typeQuery->all();

        $this->result['data'] = $typeResult;

        return $this->result;
    }

    /**
     * 
     * @param type $week_data
     * @param type $worder_list
     * @param type $schedul_list
     * @param $doctorTypeList 医生服务类型列表
     * @return 格式化 医生排班设置的返回数据
     */
    private function formatSchedule($week_data, $worder_list, $schedule_list, $doctorTypeList) {
        $data = [];
        foreach ($week_data as $v1) {
            $scheduls = [];
            foreach ($worder_list as $v2) {
                $schedul_merge = [
                    'schedule_time' => '',
                    'shift_name' => '',
                    'schedule_id' => '',
                    'type' => $doctorTypeList[$v2['doctor_id']] ? $doctorTypeList[$v2['doctor_id']] : '',
                ];
                if (isset($schedule_list[$v1])) {
                    $sl = $schedule_list[$v1];
                    if (isset($sl[$v2['doctor_id']]) && ($sd = $sl[$v2['doctor_id']])) {
                        $shift_arr = [];
                        foreach ($sd as $key => $value) {
                            $shift_arr[$key]['shift_name'] = $value['shift_name'];
                            $newData = count($value['spotTypeList']) ? true : false;
                            $tmpNameList = array();
                            $tmpIdList = array();
                            $tmpDoctorTypeList = $doctorTypeList[$v2['doctor_id']] ? array_column($doctorTypeList[$v2['doctor_id']],'typeIdList') : array();
                            foreach ($value['typeIdList'] as $typeId) {
                                if(in_array($typeId, $tmpDoctorTypeList)){//判断服务类型是否可用
                                    $tmpKey = array_search($typeId, $value['typeIdList']);
                                    $tmpNameList[] = $value['typeNameList'][$tmpKey];
                                    $tmpIdList[] = $value['typeIdList'][$tmpKey];
                                }
                            }
                            $shift_arr[$key]['typeNameList'] = implode('/', $tmpNameList);
                            $shift_arr[$key]['typeIdList'] = $tmpIdList;
                            if(empty($tmpIdList) && $newData){
                                unset($shift_arr[$key]);
                            }
                        }
                        $schedul_merge = [
                            'schedule_time' => $sd[0]['schedule_time'],
                            'shift_name' => array_values($shift_arr),
                            'schedule_id' => $sd[0]['schedule_id'],
                            'type' => $doctorTypeList[$v2['doctor_id']] ? $doctorTypeList[$v2['doctor_id']] : '',
                        ];
                        $scheduls[] = array_merge($schedul_merge, $v2);
                    } else {
                        $scheduls[] = array_merge($schedul_merge, $v2);
                    }
                } else {
                    $scheduls[] = array_merge($schedul_merge, $v2);
                }
            }
            $data[] = [
                'date' => $v1,
                'scheduls' => $scheduls
            ];
        }
        return $data;
    }

    /**
     * 
     * @param array $doctorList 医生列表
     * @param string $startDate 开始时间
     * @param string $endDate 结束时间
     * @param string $type 预约类型
     * @desc 返回医生的已预约人数
     * @return array
     */
    protected function getAppointmentCount($doctorList, $startDate, $endDate, $type = null) {
        $startTime = strtotime($startDate . ' 00:00:00');
        $endTime = strtotime($endDate . ' 23:59:59');

        $list = array();
        for ($i = $startTime; $i <= $endTime; $i += 86400) {
            $dateKey = date('Y-m-d', $i);
            foreach ($doctorList as $val) {//初始化为0
                $list[$dateKey][$val]['usedAppointmentCount'] = 0; //初始化已预约人数
                $list[$dateKey][$val]['canAppointmentCount'] = 0; //初始化可预约人数
            }
        }
        //获取已预约人数
        $query = new Query();
        $query->from(['a' => Appointment::tableName()]);
        $query->select(['a.time', 'a.doctor_id']);
        $query->leftJoin(['c' => PatientRecord::tableName()], '{{a}}.record_id = {{c}}.id');
        $query->where(['in', 'a.doctor_id', $doctorList]);
//        $query->andWhere('c.status != :status', [':status' => 7]);
        $query->andWhere('c.status != 7 and c.status != 8');
        $query->andWhere('a.time >= :begin_time', [':begin_time' => $startTime]);
        $query->andWhere('a.time <=:end_time', [':end_time' => $endTime]);
        $query->andWhere(['a.spot_id' => $this->spotId]);
        $query->orderBy('a.time');
        $data = $query->all();
        foreach ($data as $val) {
            $dateKey = date('Y-m-d', $val['time']);
            $list[$dateKey][$val['doctor_id']]['usedAppointmentCount'] ++;
        }
        //可预约人数 
        $canAppointmentCount = $this->getCanAppointmentCount($doctorList, $startTime, $endTime, $type);
        if (!empty($canAppointmentCount)) {
            foreach ($canAppointmentCount as $key => $value) {
                if (!empty($value)) {
                    foreach ($value as $k => $count) {
                        $list[$k][$key]['canAppointmentCount'] = $count;
                    }
                }
            }
        }
        return $list;
    }

    /**
     *
     * @param array $doctorList 医生列表
     * @param integer $startTime 开始时间
     * @param integer $endTime 结束时间
     * @param string $type 预约类型
     * @desc 返回医生的可预约人数
     * @return array
     */
    protected function getCanAppointmentCount($doctorList, $startTime, $endTime, $type = null) {
        $result = [];
        //获取对应医生的最小预约服务 
        $doctorTimeList = Appointment::getDoctorTimeList($doctorList, $type, $startTime, $endTime);
        foreach ($doctorTimeList as $doctorId => $doctorTimeInfo) {
            foreach ($doctorTimeInfo as $date => $value) {
                $result[$doctorId][$date] = count($value);
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $startDate 开始时间
     * @param string $endDate 结束时间
     * @param string $doctorId
     * @param string $type
     * @desc 返回当前诊所下医生的已预约人数/可预约人数列表
     * @return array
     */
    public function actionGetAppointmentDetail() {
        $startDate = Yii::$app->request->post('startDate');
        $endDate = Yii::$app->request->post('endDate');
        $doctorId = Yii::$app->request->post('doctorId', 0);
        $type = Yii::$app->request->post('type', 0);
//        $secondDepartmentId = Yii::$app->request->post('secondDepartmentId', 0);

        Yii::$app->response->format = Response::FORMAT_JSON;
//        $this->spotId = 75;
        $startTime = strtotime($startDate . ' 00:00:00');
        $endTime = strtotime($endDate . ' 23:59:59');
        
        //获取当前诊所开放预约的医生列表
        $query = new query();
        $query->from(['u' => User::tableName()]);
        $query->leftJoin(['us' => UserSpot::tableName()], '{{u}}.id={{us}}.user_id');
        $query->leftJoin(['uc' => UserAppointmentConfig::tableName()], '{{u}}.id={{uc}}.user_id');
        $query->select([ 'doctorId' => 'u.id', 'doctorName' => 'u.username','appointmentStatus'=>'us.status']);
        $query->where(['us.spot_id' => $this->spotId, 'u.occupation' => 2, 'u.status' => 1]);
        $query->andFilterWhere(['u.id' => $doctorId]);
//        if($secondDepartmentId){
//            $query->andFilterWhere(['us.department_id' => $secondDepartmentId]);
//        }
        if ($type) {
            $query->andWhere(['uc.spot_type_id' => $type]);
            $query->andWhere(['uc.spot_id' => $this->spotId]);
        }
        $query->groupBy('u.id');
        $doctorInfoList = $query->all();

        $doctorList = array();
        $total = array();
        $weekStatusList = array();
        foreach ($doctorInfoList as $value) {
            $doctorList[] = $value['doctorId'];

            $total[$value['doctorId']]['usedAppointmentCount'] = 0;
            $total[$value['doctorId']]['maxAppointmentCount'] = 0;
            $total[$value['doctorId']]['doctorName'] = $value['doctorName'];
            $total[$value['doctorId']]['doctorId'] = $value['doctorId'];
            $weekStatusList[$value['doctorId']]['status'] = 2;//默认当周不显示
        }
        $countList = $this->getAppointmentCount($doctorList, $startDate, $endDate, $type);
        $doctorStatusList = $this->getDoctorStatus($doctorList, $startDate, $endDate);
        $dayList = array();
        $doctorStatus = array();
        for ($i = $startTime,$j=0; $i <= $endTime; $i += 86400,$j++) {
            $dateKey = date('Y-m-d', $i);
            $dayList[$dateKey]['date'] = $dateKey;
            $dayList[$dateKey]['usedAppointmentCount'] = 0;
            $dayList[$dateKey]['maxAppointmentCount'] = 0;
            $dayList[$dateKey]['appointment'] = array();
            foreach ($doctorInfoList as $val) {
                $usedAppointmentCount = $countList[$dateKey][$val['doctorId']]['usedAppointmentCount'];
                $canAppointmentCount = $countList[$dateKey][$val['doctorId']]['canAppointmentCount'];
                $maxAppointmentCount = $val['appointmentStatus'] == 2 ? $usedAppointmentCount:$usedAppointmentCount + $canAppointmentCount;
                $dayList[$dateKey]['appointment'][$val['doctorId']]['usedAppointmentCount'] = $usedAppointmentCount;
                $dayList[$dateKey]['appointment'][$val['doctorId']]['maxAppointmentCount'] = $maxAppointmentCount;

                $dayList[$dateKey]['appointment'][$val['doctorId']]['doctorName'] = $val['doctorName'];
                $dayList[$dateKey]['appointment'][$val['doctorId']]['doctorId'] = $val['doctorId'];
                $dayList[$dateKey]['appointment'][$val['doctorId']]['appointmentStatus'] = $val['appointmentStatus'];
                $dayList[$dateKey]['appointment'][$val['doctorId']]['status'] = $maxAppointmentCount ? 1 : ($doctorStatusList[$dateKey][$val['doctorId']]['status'] ? 1 : 2); //1可预约，2不可约
                //当医生可预约且已有预约人数，赋1
                if($val['appointmentStatus'] == 2 && $usedAppointmentCount ==0){
                    $doctorStatus[$val['doctorId']][$j] =0;
                }else{
                    $doctorStatus[$val['doctorId']][$j]=1;
                }
                $weekStatusList[$val['doctorId']]['status'] = $dayList[$dateKey]['appointment'][$val['doctorId']]['status'] == 1 ? 1 : $weekStatusList[$val['doctorId']]['status'];
                $dayList[$dateKey]['usedAppointmentCount'] += $usedAppointmentCount; //当天预约总人数
                $dayList[$dateKey]['maxAppointmentCount'] += $maxAppointmentCount; //当天最大预约总人数

                $total[$val['doctorId']]['usedAppointmentCount'] += $usedAppointmentCount; //医生已预约总人数
                $total[$val['doctorId']]['maxAppointmentCount'] += $maxAppointmentCount; //医生预约总人数
            }
//            $dayList[$dateKey]['appointment'] = array_values($dayList[$dateKey]['appointment']);
        }

        //过滤出医生可预约且最大预约人数大于1
        foreach($weekStatusList as $key => $val){
            if($val['status'] ==2 ){
                for ($i = $startTime; $i <= $endTime; $i += 86400) {
                    $dateKey = date('Y-m-d', $i);
                    unset($dayList[$dateKey]['appointment'][$key]);
                }
                unset($total[$key]);
            }
        }

        //过滤出医生可预约或医生已有预约记录
        foreach ($doctorStatus as $key => $val) {
            $num = array_sum($val);
            if($num == 0){
                for ($i = $startTime; $i <= $endTime; $i += 86400) {
                    $dateKey = date('Y-m-d', $i);
                    unset($dayList[$dateKey]['appointment'][$key]);
                }
                unset($total[$key]);
            }
        }


        for ($i = $startTime; $i <= $endTime; $i += 86400) {
            $dateKey = date('Y-m-d', $i);
            $dayList[$dateKey]['appointment'] = array_values($dayList[$dateKey]['appointment']);
        }


        $this->result['data'] = array_values($dayList);
        $this->result['total'] = array_values($total);
        $this->result['doctorId'] = $doctorId;
//        $this->result['secondDepartmentId'] = $secondDepartmentId;
        
        $this->result['type'] = $type;
        return $this->result;
    }

    /**
     * 
     * @param string $doctorList
     * @param string $startDate 开始时间
     * @param string $endDate 结束时间
     * @desc 返回当前诊所下医生的可预约状态
     * @return array
     */
    protected function getDoctorStatus($doctorList, $startDate, $endDate) {
        $startTime = strtotime($startDate . ' 00:00:00');
        $endTime = strtotime($endDate . ' 23:59:59');

        $list = array();
        for ($i = $startTime; $i <= $endTime; $i += 86400) {
            $dateKey = date('Y-m-d', $i);
            foreach ($doctorList as $val) {
                $list[$dateKey][$val]['status'] = 0; //初始化医生状态
            }
        }

        $doctorInfoList = AppointmentConfig::find()
                ->select(['user_id as doctorId', 'begin_time as beginTime', 'end_time as endTime'])
                ->where([ 'in', 'user_id', $doctorList])
                ->andWhere(['between', 'end_time', $startTime, $endTime])
                ->andWhere(['spot_id' => $this->spotId])
                ->asArray()
                ->all();

        foreach ($doctorInfoList as $val) {
            $dateKey = date('Y-m-d', $val['beginTime']);
            $list[$dateKey][$val['doctorId']]['status'] = 1;
        }
        return $list;
    }

    /**
     * @desc 选择预约时间模板
     */
    public function actionAppointmentTimeTemplateList($doctor_id,$date)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $type = empty(Yii::$app->request->get('type')) ? 0 : Yii::$app->request->get('type');
            $dataProvider = new ActiveDataProvider([
                'query' => AppointmentTimeTemplate::find()->select(['id','name','appointment_times'])->andWhere(['spot_id' => $this->spotId]),
                'sort' => [
                    'attributes' => ['']
                ],
                'pagination' => false,
            ]);

            $btnID = $type == 1 ? 'time-template-cancel-btn' : 'time-template-cancel-btn-0';

            return [
                'title'=>'使用模板',
                'content' => $this->renderAjax('@popAppointmentTimeTemplateList', [
                    'dataProvider' => $dataProvider,
                    'doctorID' => $doctor_id,
                    'date' => $date,
                ]),

                'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal" ,'id' => $btnID])
            ];
        }else{
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @return int errorCode 错误代码(0-成功,1001-参数错误)
     * @return string msg 错误信息-参数错误
     * @return array rows 指定医生的预约情况
     * @return string date 日期
     * @return string doctorName 医生名称
     * @return int type 服务类型id
     * @return string spotTypeName 服务类型名称
     * @return int departmentId 医生关联的一个科室id
     * @return int doctorId 医生id
     * @return array appointmentTypeList 医生关联的所有预约服务类型
     * @desc 根据访问类型返回当前诊所下指定医生的预约情况列表与医生预约时间（包括已预约和可预约）
     */
    public function actionDoctorTimeList(){

        $isGet = Yii::$app->request->isGet;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if($isGet){
            $params = Yii::$app->request->queryParams;
            if(!$params['doctorId']  || $params['doctorName'] =='' || $params['date'] == ''){
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '参数错误';
                return $this->result;
            }else{
                $date = $params['date'];
                $dateFotmat = date("Y年m月d日",strtotime($params['date']));
                $userId = $params['doctorId'];
                $doctorName = $params['doctorName'];
                $appointmentList = $this->getAppointmentTimeList($userId,$date);
                return [
                    'title' => Html::encode($doctorName) . '<span doctor-id="'.$userId .'" class="notice-title">  (' .$dateFotmat .')</span>' ,
                    'content' => $this->renderAjax('@showMaxAppointmentInfo', [
                        'rows' => $appointmentList['appointmentList'],
                        'date' => $date,
                        'doctorName' => Html::encode($doctorName),
                        'type' => $appointmentList['spotType'],
                        'spotTypeName' => $appointmentList['typeName'],
                        'departmentId' => $appointmentList['departmentId'],
                        'doctorId' => $userId,
                        'appointmentTypeList' => $appointmentList['appointmentTypeList'],
                    ]),
                ];
            }
        }else{
            $userId = intval(Yii::$app->request->post('doctorId'));
            $type = intval(Yii::$app->request->post('type'));
            $spotTypeName = Yii::$app->request->post('spotTypeName');
            $doctorName = Yii::$app->request->post('doctorName');
            $date = Yii::$app->request->post('date');
            if (!$userId || !$type || $doctorName == '' || $date == '') {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = '参数错误';
                return $this->result;
            }
            $appointmentList = $this->getAppointmentTimeList($userId,$date,$type);
            $this->result['data'] = $appointmentList['appointmentList'];
            $this->result['date'] = $date;
            $this->result['doctorName'] = $doctorName;
            $this->result['type'] = $type;
            $this->result['spotTypeName'] = $spotTypeName;
            $this->result['departmentId'] = $appointmentList['departmentId'];
            $this->result['appointmentTypeList'] = $appointmentList['appointmentTypeList'];
            return $this->result;
        }
    }

    /**
     * @param string $doctorId 医生id
     * @param string $date 指定日期
     * @return array appointmentList 指定医生的预约情况
     * @return int type 服务类型id
     * @return string spotTypeName 服务类型名称
     * @return int departmentId 医生关联的一个科室id
     * @return array appointmentTypeList 医生关联的所有预约服务类型
     * @desc 返回医生关联科室，服务类型，预约情况
     */
    public function getAppointmentTimeList($doctorId,$date,$spotType = null){
        //获取默认预约类型和名称
        $userAppointmentTypeInfo = UserAppointmentConfig::getUserSpotType($doctorId);
        $spotType = $spotType?$spotType:$userAppointmentTypeInfo[0]['id'];
        $typeName = $userAppointmentTypeInfo[0]['type'];
        //获取医生的其中一个关联二级科室为默认科室
        $departmentInfo = UserSpot::getDepartmentInfo($doctorId);
        $departmentId = $departmentInfo[0]['department_id'];
        $startTime = strtotime($date);
        $endTime = strtotime($date)+86400;
        $doctorAppointmentStatus = UserSpot::getAppointmentStatus($doctorId)['status'];
        $rows = Appointment::getDoctorTime($doctorId, $spotType,null,$startTime,$endTime,1);
        if(!empty($rows)){
            ksort($rows[$date]);
            $data = array_values($rows[$date]);
        }else{
            $data = $rows;
        }
        //当医生的状态为不开放预约的时候，只返回已已预约的数据
        if($doctorAppointmentStatus == 2 ){
            foreach($data as $key => $val){
                if(!$val['selected']){
                    $dataArr[] = $data[$key];
                }
            }
        }else{
            $dataArr = $data;
        }
        $appointmentList = [
            'appointmentTypeList' => $userAppointmentTypeInfo,
            'departmentId' => $departmentId,
            'appointmentList' => $dataArr,
            'spotType' => $spotType,
            'typeName' => $typeName
        ];
        return $appointmentList;
    }
    
    
    /**
     * get-doctor-department
     * @param int $id 医生id
     * @return errorCode 错误代码(0-成功,1009-参数错误)$this
     * @return msg 提示信息
     * @return data[0].id 二级科室
     * @return data[0].name 二级科室名称
     * @return data[0].parentName 一级科室名称
     * @desc 获取医生拥有的当前诊所的一级，二级科室
     */
    public function actionGetDoctorDepartment() {
    
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $entrance = Yii::$app->request->post('entrance');//获取入口
        if (!$id) {
            $this->result['errorCode'] = 1009; //参数错误
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $query = new Query();
        $query->from(['a' => UserSpot::tableName()]);
        $query->select(['id' => 'a.department_id','b.name', 'parentName' => 'c.name','parentId'=>'c.id']);
        $query->leftJoin(['b' => SecondDepartment::tableName()],'{{a}}.department_id = {{b}}.id');
        $query->leftJoin(['c' => OnceDepartment::tableName()],'{{b}}.parent_id = {{c}}.id');
        $query->leftJoin(['d' => SecondDepartmentUnion::tableName()], '{{b}}.id = {{d}}.second_department_id');
        $query->where(['a.user_id' => $id,'a.spot_id' => $this->spotId,'d.spot_id' => $this->spotId,'b.status' => 1]);
        if($entrance != 1){ //非方便门诊入口需判断当前诊所医生是否开放预约
            $query->andwhere(['a.status' => 1]);
        }
        $departmentInfo = $query->all();
        $rows = [];
        if(!empty($departmentInfo)){
            foreach ($departmentInfo as $v){
                if(!$v['parentName']){
                    continue;
                }
                $rows[$v['parentId']]['name'] = $v['parentName'];
                $rows[$v['parentId']]['children'][] = [
                    'id' => $v['id'],
                    'name' => $v['name']
                ];
                
            }
        }
        $this->result['count'] = count($departmentInfo);
        $this->result['data'] = array_values($rows);
    
        return $this->result;
    }

}
