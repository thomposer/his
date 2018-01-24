<?php

namespace app\modules\triage\controllers;

use app\modules\message\models\MessageCenter;
use app\modules\report\controllers\RecordController;
use app\modules\spot_set\models\DoctorRoomUnion;
use Yii;
use app\modules\triage\models\Triage;
use app\modules\triage\models\search\TriageSearch;
use app\modules\triage\models\search\NursingRecordSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\triage\models\TriageInfo;
use app\modules\patient\models\Patient;
use yii\helpers\Json;
use app\modules\spot_set\models\Room;
use app\modules\user\models\UserSpot;
use app\modules\schedule\models\Scheduling;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use yii\web\Response;
use app\modules\outpatient\models\ChildExaminationAssessment;
use app\modules\triage\models\HealthEducation;
use app\modules\report\models\Report;
use app\modules\spot_set\models\UserAppointmentConfig;
use app\modules\spot_set\models\SpotType;
use yii\helpers\Html;
use yii\db\Query;
use app\modules\outpatient\models\AllergyOutpatient;
use app\modules\patient\models\PatientRecord;
use app\modules\outpatient\models\Outpatient;
use app\modules\spot_set\models\SecondDepartmentUnion;
use app\modules\triage\models\ChildAssessment;
use yii\base\Exception;

/**
 * TriageController implements the CRUD actions for Triage model.
 */
class TriageController extends BaseController
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
     * Lists all Triage models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new TriageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        $secondDepartmentInfo = SecondDepartment::getList();
        $doctorInfo = $this->getDoctorInfo();
//        $allergy_list = [
//            'allergy1' => Patient::$allergy1,
//            'allergy2' => Patient::$allergy2,
//            'allergy3' => Patient::$allergy3,
//        ];
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'triageInfoModel' => new TriageInfo(),
                    'dataProvider' => $dataProvider,
                    'secondDepartmentInfo' => $secondDepartmentInfo,
                    'doctorInfo' => $doctorInfo,
//                    'allergy_list' => $allergy_list
        ]);
    }

    /*
     * 渲染modal
     */

    public function actionModal($id) {
        $request = Yii::$app->request;

        if ($request->isAjax) {
            $userData=$this->getpatientinfo($id);

            $user_sex=Patient::$getSex[$userData['sex']];
            $dateDiffage=Patient::dateDiffage($userData['birthday'],time());
            $userName = Html::encode($userData['username']);
            $text = "<h2 class='triage-modal-imformation modal-title'>完善患者信息 - ".$userName."</h2>
                    <span class='triage-modal-info modal-title'>".'(' . $user_sex . ' ' . $dateDiffage . ')</span>';
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($request->get('recordId')) {
                $ret = [
                    'title' => $text,
                    'content' => $this->renderAjax('_modal', $this->getTriageModal($id)),
                ];
            } else {
                $ret = [
                    'title' => $text,
                    'content' => $this->renderAjax('_modal', $this->getTriageModal($id, false)),
                ];
            }

            return $ret;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * 选择医生列表
     */

    public function actionDoctor() {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $param = Yii::$app->request->get();
            $doctor_id = $param['doctor_id'];
            $record_id = $param['record_id'];
            $appointment_doctor = $param['appointment_doctor'];
            $query = new \yii\db\Query;
//        $doctor = $query->from(['a' => User::tableName()])
//                        ->select(['a.id', 'a.username', 'a.head_img', 'department_id'])
//                        ->leftJoin(['b' => UserSpot::tableName()], '{{a}}.id={{b}}.user_id')
//                        ->where(['b.spot_id' => $this->spotId])->limit(5)->all(); //搜索当前诊所
            //获取今天有排版的医生
            $query->from(['s' => Scheduling::tableName()])
                    ->select(['u.id', 'u.username', 'u.head_img', 'name' => 'replace(group_concat(distinct sd.name),",","，")'])
                    ->leftJoin(['u' => User::tableName()], '{{s}}.user_id={{u}}.id')
                    ->leftJoin(['us' => UserSpot::tableName()], '{{u}}.id={{us}}.user_id')
                    ->leftJoin(['sd' => SecondDepartment::tableName()], '{{sd}}.id={{us}}.department_id')
                    ->where([
                        's.spot_id' => $this->spotId, 'us.spot_id' => $this->spotId, 'FROM_UNIXTIME(s.schedule_time, \'%Y-%m-%d\')' => date('Y-m-d'), 'u.occupation' => 2, 'u.status' => 1
                    ])->andWhere(['>', 's.schedule_id', 0]);
            if ($appointment_doctor) {
                $query->orWhere(['u.id' => $appointment_doctor, 'us.spot_id' => $this->spotId]);
            }
            $doctor = $query->groupBy('u.id')->all(); //搜索当前诊所
            if ($doctor) {
                $doctorId = array_column($doctor, 'id');
                $departmentList = User::getDepartmentByDoctorId($doctorId);
                foreach ($doctor as &$val) {
                    $val['name'] = str_replace(',', '，', $departmentList[$val['id']]['department_name']);
                }
            }
            $queryBuilder = new \yii\db\Query();
            $triageInfoModel = new TriageInfo();
            $doctorServiceType = UserAppointmentConfig::getDoctorServiceType();
            foreach ($doctor as &$d) {
                $d['to_diagnose'] = $triageInfoModel->getDoctorDiagnoseNum($d['id'], 1);  //待接诊人数
                $d['diagnosed'] = $triageInfoModel->getDoctorDiagnoseNum($d['id'], 2); //已接诊人数
                $d['appointmentTypeName'] = isset($doctorServiceType[$d['id']]) ? $doctorServiceType[$d['id']]['appointmentTypeName'] : '';
            }
//            var_dump($doctor);
            $ret = [
                'title' => "选择医生",
                'content' => $this->renderAjax('_doctormodal', [
                    'doctor' => $doctor,
                    'doctor_id' => $doctor_id,
                    'record_id' => $record_id,
                    'appointment_doctor' => $appointment_doctor
                ])
            ];
//        return Json::encode($ret);
            return $ret;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     * 选择医生
     */

    public function actionChosedoctor() {

        $request = Yii::$app->request;

        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($request->isAjax) {
            $doctorId = $request->get('doctorId');
            $recordId = $request->get('recordId');
            //根据医生id获取医生的科室

            $query = new Query();
            $query->from(['a' => UserSpot::tableName()]);
            $query->select(['id' => 'a.department_id', 'b.name']);
            $query->rightJoin(['b' => SecondDepartment::tableName()], '{{a}}.department_id = {{b}}.id');
            $query->leftJoin(['c' => SecondDepartmentUnion::tableName()], '{{b}}.id = {{c}}.second_department_id');
            $query->where(['a.spot_id' => $this->spotId, 'a.user_id' => $doctorId, 'b.status' => 1, 'c.spot_id' => $this->spotId]);
            $departmentData = $query->all();


            //根据医生id获取医生的服务类型
            $query = new Query();
            $query->from(['a' => UserAppointmentConfig::tableName()]);
            $query->select(['id' => 'a.spot_type_id', 'b.type']);
            $query->leftJoin(['b' => SpotType::tableName()], '{{a}}.spot_type_id = {{b}}.id');
            $query->where(['a.spot_id' => $this->spotId, 'a.user_id' => $doctorId, 'b.status' => 1]);
            $typeData = $query->all();

            $reportModel = Report::findOne(['record_id' => $recordId, 'spot_id' => $this->spotId]);
            $doctorInfo = User::getUserInfo($doctorId, ['username']);

            //设置分诊的场景只验证科室和服务类型
            $reportModel->scenario = 'choose-doctor';

            if ($reportModel->doctor_id != $doctorId) {//若选择的医生为自身的其他医生，则默认不选中
                $reportModel->type = 0;
                $reportModel->second_department_id = 0;
            }
            if (count($departmentData) == 1) {//若只有一个科室，则默认选中
                $reportModel->second_department_id = $departmentData[0]['id'];
            }
            if (count($typeData) == 1) {//若只有一个服务类型，则默认选中
                $reportModel->type = $typeData[0]['id'];
            }
            if ($reportModel->load(Yii::$app->request->post()) && $reportModel->validate()) {

                $patientRecordModel = PatientRecord::findOne(['id' => $recordId, 'spot_id' => $this->spotId]);
                if($patientRecordModel->status == 4){
                    return [
                        'forceMessage' => '医生已接诊',
                        'forceType' => 2,
                        'forceClose' => true,
                        'forceReloadPage' => true,
                    ];
                }
                if($reportModel->getOldAttribute('doctor_id') != $reportModel->doctor_id || $reportModel->getOldAttribute('type') != $reportModel->type){
                    //查询医生加服务类型关联的诊金
                    $medicalFee = UserAppointmentConfig::getMedicalFee($doctorId,$reportModel->type);
                    $patientRecordModel->price = $medicalFee['price'];
                    $patientRecordModel->record_price = $medicalFee['price'];
                }
                $patientRecordModel->save();


                $typeInfo = SpotType::getTypeFields($reportModel->type, ['type']);
                $reportModel->type_description = $typeInfo['type'];
                $reportModel->doctor_id = $doctorId;
                $reportModel->record_type = Report::getRecordType($reportModel->type);
                if ($reportModel->save()) {
                    return $this->setDoctorRoom($recordId, $doctorId);
                } else {
                    return [
                        'forceReload' => "#crud-datatable-pjax",
                        'forceMessage' => '操作失败',
                        'forceType' => 2,
                        'forceClose' => true,
                    ];
                }
            } else {
                return [
                    'title' => "选择服务类型及科室<span class = 'title-brackets' >（" . Html::encode($doctorInfo['username']) . "）</span>",
                    'content' => $this->renderAjax('/triage/_chooseDepartmentType', [
                        'reportModel' => $reportModel,
                        'departmentData' => $departmentData,
                        'typeData' => $typeData
                    ]),
                    //点取消返回医生卡片列表
                    'footer' => Html::a('取消', ['triage/doctor', 'record_id' => $recordId, 'doctor_id' => $reportModel->doctor_id, 'appointment_doctor' => $reportModel->doctor_id], ['class' => 'btn btn-cancel btn-form', 'role' => 'modal-remote']) .
                    Html::button('确认', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    //分诊后记录分诊时间并推送消息
    public function setDoctorRoom($record_id, $doctor_id) {
        $model = $this->findInfoModel($record_id);
        $model->doctor_id = $doctor_id;
        if ($model->room_id) {
            //如果选择了医生和诊室就更改分诊的时间
            $model->triage_time = time();
        }
        if ($model->save()) {
            if ($model->room_id) {
                $triageModel = $this->findModel($record_id);
                $triageModel->status = 3;
                $triageModel->save();
                //待分诊消息推送存取
                MessageCenter::saveMessageCenter($model->doctor_id, $this->findModel($record_id)->patient_id, Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@outpatientOutpatientIndex')]), '', '待接诊', $model->room_id, $record_id);
            }
            return [
//                'forceReload' => "#crud-datatable-pjax",
                'forceClose' => true,
                'forceReloadPage' => true,
                'forceMessage' => '操作成功'
            ];
        } else {
            return [
                'forceClose' => true,
                'forceMessage' => '操作失败',
                'forceReload' => '#crud-datatable-pjax',
            ];
        }
    }

    /*
     * 选择诊室
     */

    public function actionChoseroom() {
        $param = Yii::$app->request->post();
        $room_id = $param['room_id'];
        $record_id = $param['record_id'];
        $model = $this->findInfoModel($record_id);
        $model->room_id = $room_id;

        $patientRecordModel = PatientRecord::findOne(['id' =>$record_id,'spot_id' =>$this->spotId]);
        if(in_array($patientRecordModel->status,[4,5])){
            $ret = [
                'success' => true,
                'errorCode' => 1001,
                'msg' => '医生已接诊' ,
                'data' => []
            ];
           exit(Json::encode($ret));
        }
        if ($model->doctor_id) {
            //如果选择了医生和诊室就更改分诊的时间 
            $model->triage_time = time();
        }
        $changeStatus = false;
        $oldRoom = $model->getOldAttribute('room_id');
        if ($oldRoom && $oldRoom != $room_id) {
            $changeStatus = true;
        }
        if ($model->save()) {
            if ($model->doctor_id) {
                //修改就诊流水记录状态
                $triageModel = $this->findModel($record_id);
                $triageModel->status = 3;
                $triageModel->save();
                //待分诊消息推送存取
                MessageCenter::saveMessageCenter($model->doctor_id, $this->findModel($record_id)->patient_id, Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@outpatientOutpatientIndex')]), '', '待接诊', $model->room_id, $record_id);
            }
            //将诊室的状态更改为分诊中
            $roomModel = Room::findOne(['id' => $param['room_id'], 'spot_id' => $this->spotId]);
            $roomModel->clean_status = 3;
            $roomModel->record_id = $record_id;
            $roomModel->save();
            if ($changeStatus) {//更换了诊室  修改之前的诊室为正常状态
                $roomChangeModel = Room::findOne(['id' => $oldRoom, 'spot_id' => $this->spotId]);
                $roomChangeModel->clean_status = 1;
                $roomChangeModel->record_id = 0;
                $roomChangeModel->save();
            }


            $ret = [
                'success' => true,
                'errorCode' => 0,
                'msg' => '',
                'data' => []
            ];
        } else {
            Yii::info('分诊诊室：' . json_encode($model->errors));
            $ret = [
                'success' => true,
                'errorCode' => 1001,
                'msg' => '操作失败' . $model->errors,
                'data' => []
            ];
        }
        exit(Json::encode($ret));
    }

    /*
     * 诊室列表
     */

    public function actionRoom() {
        $roomArr = [];
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $param = Yii::$app->request->get();
            $room_id = $param['room_id'];
            $record_id = $param['record_id'];
            $doctor_id = $param['doctor_id'];

            $room = Room::find()->select(['id', 'clinic_name'])->where(['spot_id' => $this->spotId, 'status' => 1])
                    ->orWhere(['id' => [$room_id]])->asArray()
                    ->all();

            $frequentRoom = DoctorRoomUnion::find()->select(['room_id'])->where(['spot_id' => $this->spotId, 'doctor_id' => $doctor_id])->indexBy('room_id')->asArray()->all();
            $doctorName = User::find()->select('username')->where(['id' => $doctor_id])->asArray()->one();

            foreach ($room as $fKey => $fValue) {
                if (isset($frequentRoom[$fValue['id']])) {
                    $room[$fKey]['frequent'] = 1;
                }
            }
            if (!$doctor_id) {
                $title = '选择诊室';
            } else {
                $title = "选择诊室<span class='doc-name'>(" . Html::encode($doctorName['username']) . ")</span>";
            }

            $ret = [
                'title' => $title,
                'content' => $this->renderAjax('_roommodal', [
                    'room' => $room,
                    'room_id' => $room_id,
                    'frequentRoom' => $frequentRoom,
                    'record_id' => $record_id,
                ])
            ];

            return $ret;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Displays a single Triage model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Triage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Triage();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /*
     * 完善信息
     */

    public function actionInfo() {
        $param = Yii::$app->request->post('TriageInfo');
        $model = $this->findInfoModel($param['record_id']);
        if ($param['modal_tab'] == 3) {
            $model->scenario = 'saveType';
        }
        $ret = [
            'success' => true,
            'errorCode' => 0,
            'msg' => '',
            'data' => []
        ];
        if ($param['modal_tab'] == 2) {//保存到   儿童发育评估表中
            $assessmentRet = $this->childExaminationAssessment();
            return $assessmentRet;
        } elseif ($param['modal_tab'] == 4) {
            $allergyRet = $this->saveAllergyOutpatient();
            return $allergyRet;
        } elseif ($param['modal_tab'] == 5) {
            $healthEduRet = $this->healthEducation();
            return $healthEduRet;
        } else {
            if ($model->load(Yii::$app->request->post())) {
                if (is_array($model->blood_type_supplement)) {
                    $model->blood_type_supplement = implode(',', $model->blood_type_supplement);
                }
                if (empty($model->bloodtype) || $model->bloodtype == '0') {
                    $model->blood_type_supplement = '';
                }
                if ($model->save()) {
                    return Json::encode($ret);
                } else {
                    $errors = $model->errors;
                    $ret['msg'] = '';
//                if (isset($errors['allergy'])) {
//                    $ret['msg'] = $errors['allergy'][0];
//                }
                    $ret['errorCode'] = 1001;
                    return Json::encode($ret);
                }
            } else {
                $ret['errorCode'] = 1001;
                $ret['msg'] = '操作失败' . $model->errors;
                return Json::encode($ret);
            }
        }
    }

    /**
     *
     * @return type 保存发育评估
     */
    protected function childExaminationAssessment() {
        $id = Yii::$app->request->post('ChildExaminationAssessment')['record_id'];
        $childAssessment = Yii::$app->request->post('ChildAssessment');
        $score = $childAssessment['score'];
        $assesmentTime = $childAssessment['assesment_time'];
        $remark = $childAssessment['remark'];
        $fallScore = $childAssessment['fallScore'];
        $fallTime = $childAssessment['fallTime'];
        $fallRemark = $childAssessment['fallRemark'];
        $model = $this->findAssessmentModel($id);
        $ret = [
            'success' => true,
            'errorCode' => 0,
            'msg' => '',
            'data' => []
        ];
        if ($model->load(Yii::$app->request->post())) {
            $dbTrans = Yii::$app->db->beginTransaction();
            $db = Yii::$app->db;
            try {
                if ($model->save()) {
                    //先删掉历史数据 
                    ChildAssessment::deleteAll(['record_id' => $id, 'spot_id' => $this->spotId]);
                    //保存 疼痛/跌倒 评分
                    $row = [];
                    $maxPainScore = '';
                    $maxFallScore = '';
                    if (!empty($score)) {
                        foreach ($score as $k => $v) {
                            if ($v && !preg_match('/^\d{1,2}$/', $v)) {
                                $error = ['attr' => 'child-assessment-score', 'key' => $k, 'msg' => '疼痛评分（0-10）必须是整数。'];
                                throw new Exception(json_encode($error));
                                return;
                            }
                            if ($v && $v < 0) {
                                $error = ['attr' => 'child-assessment-score', 'key' => $k, 'msg' => '疼痛评分（0-10）的值必须不小于0。'];
                                throw new Exception(json_encode($error));
                                return;
                            }
                            if ($v && $v > 10) {
                                $error = ['attr' => 'child-assessment-score', 'key' => $k, 'msg' => '疼痛评分（0-10）的值必须不大于10。'];
                                throw new Exception(json_encode($error));
                                return;
                            }
                            if ($assesmentTime[$k] && (isset($assesmentTime[$k - 1]) && strtotime($assesmentTime[$k]) < strtotime($assesmentTime[$k - 1]))) {
                                $error = ['attr' => 'child-assesment-time', 'key' => $k, 'msg' => '不得早于上一次的评估时间'];
                                throw new Exception(json_encode($error));
                                return;
                            }
                            if ($remark[$k] && mb_strlen($remark[$k], 'UTF-8') > 30) {
                                $error = ['attr' => 'child-assessment-remark', 'key' => $k, 'msg' => '备注只能包含至多30个字符。'];
                                throw new Exception(json_encode($error));
                                return;
                            }
                            if ($v!='' || $assesmentTime[$k] || $remark[$k]) {
                                $maxPainScore = $v;
                                $row[] = [$this->spotId, $id, $v, $assesmentTime[$k] ? strtotime($assesmentTime[$k]) : 0, $remark[$k], 1, time(), time()];
                            }
                        }
                    }
                    if (!empty($fallScore)) {
                        foreach ($fallScore as $k => $v) {
                            if ($v && !preg_match('/^\d{1,2}$/', $v)) {
                                $error = ['attr' => 'child-fall-score', 'key' => $k, 'msg' => '跌倒评分（HDFS 6-20）必须是整数。'];
                                throw new Exception(json_encode($error));
                                return;
                            }
                            if ($v && $v < 6) {
                                $error = ['attr' => 'cchild-fall-score', 'key' => $k, 'msg' => '跌倒评分（HDFS 6-20）的值必须不小于6。'];
                                throw new Exception(json_encode($error));
                                return;
                            }
                            if ($v && $v > 20) {
                                $error = ['attr' => 'child-fall-score', 'key' => $k, 'msg' => '跌倒评分（HDFS 6-20）的值必须不大于20。'];
                                throw new Exception(json_encode($error));
                                return;
                            }
                            if ($assesmentTime[$k] && (isset($assesmentTime[$k - 1]) && strtotime($assesmentTime[$k]) < strtotime($assesmentTime[$k - 1]))) {
                                $error = ['attr' => 'child-fall-time', 'key' => $k, 'msg' => '不得早于上一次的评估时间'];
                                throw new Exception(json_encode($error));
                                return;
                            }
                            if ($remark[$k] && mb_strlen($remark[$k], 'UTF-8') > 30) {
                                $error = ['attr' => 'child-fall-remark', 'key' => $k, 'msg' => '备注只能包含至多30个字符。'];
                                throw new Exception(json_encode($error));
                                return;
                            }
                            if ($v || $fallTime[$k] || $fallRemark[$k]) {
                                $maxFallScore = $v;
                                $row[] = [$this->spotId, $id, $v, $fallTime[$k] ? strtotime($fallTime[$k]) : 0, $fallRemark[$k], 2, time(), time()];
                            }
                        }
                    }
                    //更新疼痛跌倒评分最大值
                    TriageInfo::updateAll(['pain_score' => $maxPainScore,'fall_score' => $maxFallScore],['record_id' => $id,'spot_id' => $this->spotId]);
                    if (!empty($row)) {
                        $res = $db->createCommand()->batchInsert(ChildAssessment::tableName(), ['spot_id', 'record_id', 'score', 'assesment_time', 'remark', 'type', 'create_time', 'update_time'], $row)->execute();
                    }
                    $dbTrans->commit();
                    return Json::encode($ret);
                } else {
                    $dbTrans->rollBack();
                    $errors = $model->errors;
                    $ret['msg'] = $errors['score'] ? $errors['score'][0] : $errors;
                    $ret['errorCode'] = 1001;
                    return Json::encode($ret);
                }
            } catch (Exception $ex) {
                $dbTrans->rollBack();
                $ret['errorCode'] = 1001;
                $ret['msg'] = json_decode($ex->getMessage(), true);
                return Json::encode($ret);
            }
        } else {
            $ret['errorCode'] = 1001;
            $ret['msg'] = '操作失败' . $model->errors;
            return Json::encode($ret);
        }
    }

    /**
     * @return type 保存健康教育
     */
    protected function healthEducation() {
        $healthEducation = Yii::$app->request->post('HealthEducation');
        $id = $healthEducation['record_id'];
        $dbTrans = Yii::$app->db->beginTransaction();
        $db = Yii::$app->db;
        $db->createCommand()->delete(HealthEducation::tableName(), ['record_id' => $id, 'spot_id' => $this->spotId])->execute();
        $model = new HealthEducation();
        $ret = [
            'success' => true,
            'errorCode' => 0,
            'msg' => '',
            'data' => []
        ];
        try {
            foreach ($healthEducation['education_content'] as $key => $val) {
                //至少有一个字段不为空
                if ($healthEducation['education_content'][$key] != '' || !empty($healthEducation['education_object'][$key]) || !empty($healthEducation['education_method'][$key]) || !empty($healthEducation['accept_barrier'][$key]) || !empty($healthEducation['accept_ability'][$key])) {
                    $rows[] = [
                        'record_id' => $id,
                        'spot_id' => $this->spotId,
                        'education_content' => $healthEducation['education_content'][$key],
                        'education_object' => $healthEducation['education_object'][$key],
                        'education_method' => $healthEducation['education_method'][$key],
                        'accept_barrier' => $healthEducation['accept_barrier'][$key],
                        'accept_ability' => $healthEducation['accept_ability'][$key],
                        'create_time' => time()
                    ];
                } else {
                    continue;
                }
            }
            //批量插入新增收费记录
            $res = $db->createCommand()->batchInsert(HealthEducation::tableName(), ['record_id', 'spot_id', 'education_content', 'education_object', 'education_method', 'accept_barrier', 'accept_ability', 'create_time'], $rows)->execute();
            $dbTrans->commit();
            return Json::encode($ret);
        } catch (Exception $e) {
            $dbTrans->rollBack();
            $ret['errorCode'] = 1001;
            $ret['msg'] = '操作失败';
            return Json::encode($ret);
        }
    }

    /**
     * @return type 保存过敏信息
     */
    protected function saveAllergyOutpatient() {
        $allergyOutpatient = Yii::$app->request->post('allergyOutpatient');
        $id = $allergyOutpatient['record_id'];

        $ret = [
            'success' => true,
            'errorCode' => 0,
            'msg' => '',
            'data' => []
        ];
        $patientRecord = PatientRecord::getPatientRecord($id);
        if (in_array($patientRecord['status'], [4, 5])) {
            $ret['msg'] = '不可编辑！';
            $ret['errorCode'] = 1002;
            return Json::encode($ret);
        }

        $dbTrans = Yii::$app->db->beginTransaction();
        $db = Yii::$app->db;
        $db->createCommand()->delete(AllergyOutpatient::tableName(), ['record_id' => $id, 'spot_id' => $this->spotId])->execute();
        if ($allergyOutpatient['haveAllergyOutpatient'] == 1) {
            $dbTrans->commit();
            return Json::encode($ret);
        }
        try {
            if (empty($allergyOutpatient['allergy_degree'])) {
                $dbTrans->commit();
                return Json::encode($ret);
            }
            $allergyOutpatient['allergy_degree'] = array_values($allergyOutpatient['allergy_degree']);
            foreach ($allergyOutpatient['type'] as $key => $val) {
                if ($allergyOutpatient['type'][$key] != '' && $allergyOutpatient['allergy_content'][$key] != '') {
                    $rows[] = [
                        'record_id' => $id,
                        'spot_id' => $this->spotId,
                        'type' => $allergyOutpatient['type'][$key] ? $allergyOutpatient['type'][$key] : 0,
                        'allergy_content' => $allergyOutpatient['allergy_content'][$key],
                        'allergy_degree' => $allergyOutpatient['allergy_degree'][$key] ? $allergyOutpatient['allergy_degree'][$key] : 0,
                        'create_time' => time()
                    ];
                } else {
                    if ($allergyOutpatient['type'][$key] == '') {
                        $ret['errorCode'] = 1009;
                        $ret['msg'] = '过敏类型不能为空';
                    } else if ($allergyOutpatient['allergy_content'][$key] == '') {
                        $ret['errorCode'] = 1009;
                        $ret['msg'] = '名称不能为空';
                    }
                    $dbTrans->rollBack();
                    return Json::encode($ret);
                }
            }
            //批量插入记录
            $res = $db->createCommand()->batchInsert(AllergyOutpatient::tableName(), ['record_id', 'spot_id', 'type', 'allergy_content', 'allergy_degree', 'create_time'], $rows)->execute();
            $dbTrans->commit();
            return Json::encode($ret);
        } catch (Exception $e) {
            $dbTrans->rollBack();
            $ret['errorCode'] = 1001;
            $ret['msg'] = '操作失败';
            return Json::encode($ret);
        }
    }

    /**
     * Updates an existing Triage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Triage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('success', '删除成功');
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Triage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Triage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Triage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findInfoModel($id) {
        if (($model = TriageInfo::findOne(['spot_id' => $this->spotId, 'record_id' => $id])) !== null) {
            return $model;
        } else {
            $model = new TriageInfo();
            $model->record_id = $id;
            return $model;
        }
    }

    protected function findAssessmentModel($id) {
        if (($model = ChildExaminationAssessment::findOne(['spot_id' => $this->spotId, 'record_id' => $id])) !== null) {
            return $model;
        } else {
            $model = new ChildExaminationAssessment();
            $model->record_id = $id;
            return $model;
        }
    }

    protected function findAllergyOutpatient($id) {
        $model = AllergyOutpatient::find()->where(['spot_id' => $this->spotId, 'record_id' => $id])->all();
        if ($model !== null && !empty($model)) {
            $haveStatus = 1;
            return ['haveStatus' => $haveStatus, 'model' => $model];
        } else {
            $haveStatus = 0;
            $model = new AllergyOutpatient();
            $model->record_id = $id;
            return ['haveStatus' => $haveStatus, 'model' => [$model]];
        }
    }

    /**
     *
     * @param type $id 获取健康教育
     */
    protected function findHealthEducationModel($id) {
        $model = HealthEducation::find()->where(['spot_id' => $this->spotId, 'record_id' => $id])->all();
        if ($model !== null && !empty($model)) {
            return $model;
        } else {
            $model = new HealthEducation();
            $model->record_id = $id;
            //当model为空时 默认选中
            $model->education_object = 2;
            $model->education_method = 1;
            $model->accept_barrier = 1;
            $model->accept_ability = 1;
            return [$model];
        }
    }

    /**
     * @param  int $id 就诊流水id
     * @param  boolean $isJump 是否需要跳转
     * @return array 返回分诊-完善信息model
     * @author
     */
    protected function getTriageModal($id, $isJump = true) {

        $model = $this->findInfoModel($id);
        if (Outpatient::getOrdersStatus($id,2)) {//查询治疗医嘱是否为空
            $model->scenario = 'updateModalInfo';
        }
        $model->record_id = $id;
        $model->blood_type_supplement = $model->blood_type_supplement ? explode(',', $model->blood_type_supplement) : '';
        $assessmentModel = $this->findAssessmentModel($id);
        $healthEduModel = $this->findHealthEducationModel($id);
        $patientInfo = Patient::getUserInfo($id);
        $reportRecord = Report::reportRecord($id);
        $patientRecord = PatientRecord::getPatientRecord($id);
        $assessmentModel->assessmentAge = Patient::dateDiffage($patientInfo['birthtime'], $reportRecord['create_time']);
        Yii::info('birthday:' . $patientInfo['birthday'] . ' create_time:' . $reportRecord['create_time']);
        Yii::info('assessmentAge: ' . $assessmentModel->assessmentAge);
        //护理记录弹框
        $nursingSearchModel = new NursingRecordSearch();
        $nursingDataProvider = $nursingSearchModel->search(Yii::$app->request->queryParams, $this->pageSize, $id);

        //过敏史
        $allergyOutpatientData = $this->findAllergyOutpatient($id);
        $allergyOtherInfo = [
            'haveStatus' => $allergyOutpatientData['haveStatus'],
            'patientRecordStatus' => $patientRecord['status'],
        ];
        $assesment = ChildAssessment::getScore($id);
        $painScore = $assesment[1] ? $assesment[1] : [['score', 'assesment_time', 'remark']];
        $fallScore = $assesment[2] ? $assesment[2] : [['fallScore', 'fallTime', 'fallRemark']];
        return [
            'model' => $model,
            'assessmentModel' => $assessmentModel,
            'healthEduModel' => $healthEduModel,
            'nursingDataProvider' => $nursingDataProvider,
            'allergyOutpatientModel' => $allergyOutpatientData['model'],
            'allergyOtherInfo' => $allergyOtherInfo,
            'recordId' => $isJump ? $id : '',
            'childModel' => ChildAssessment::findModel($id),
            'painScore' => $painScore,
            'fallScore' => $fallScore
        ];
    }

    /**
     * @return 获取医生列表
     */
    protected function getDoctorInfo() {
        $query = new Query();
        $query->from(['a' => User::tableName()]);
        $query->select(['a.id', 'a.username']);
        $query->leftJoin(['b' => UserSpot::tableName()], '{{a}}.id = {{b}}.user_id');
//         $query->leftJoin(['c' => SecondDepartment::tableName()], '{{b}}.department_id = {{c}}.id');
        $query->where(['a.occupation' => 2, 'a.status' => 1, 'b.spot_id' => $this->spotId]);
        $query->indexBy('id');
        return $query->all();
    }


    /**
     * @param $id流水ID
     */
    protected function getpatientinfo($id){
        $query=new Query();
        $query->from(['a'=>Patient::tableName()]);
        $query->select(['a.id','a.username','a.birthday','a.sex']);
        $query->leftJoin(['b'=>PatientRecord::tableName()],'{{a}}.id={{b}}.patient_id');
        $query->where(['b.id'=>$id,'b.spot_id'=>$this->spotId]);
        return $query->one();

    }

}
