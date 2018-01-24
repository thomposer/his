<?php

namespace app\modules\report\controllers;

use app\common\base\MultiModel;
use app\modules\patient\models\PatientSubmeter;
use app\modules\spot_set\models\SpotType;
use Yii;
use app\modules\patient\models\Patient;
use app\modules\report\models\search\ReportSearch;
use app\common\base\BaseController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Object;
use app\modules\patient\models\PatientFamily;
use app\modules\report\models\Report;
use app\modules\patient\models\PatientRecord;
use yii\db\Query;
use yii\helpers\Url;
use app\modules\report\models\search\AppointmentSearch;
use app\modules\make_appointment\models\Appointment;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\web\Response;
use app\modules\triage\models\TriageInfo;
use app\specialModules\recharge\models\CardRecharge;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use yii\helpers\Html;
use app\modules\patient\models\PatientAllergy;
use app\modules\spot_set\models\UserAppointmentConfig;
/**
 * RecordController implements the CRUD actions for Patient model.
 */
class RecordController extends BaseController
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
     * Lists all Patient models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        $cardInfo = CardRecharge::getCardInfoByQueryNurse($dataProvider->query);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'cardInfo' => $cardInfo,
        ]);
    }

    /**
     * Lists all Appointment models.
     * @return mixed
     */
    public function actionAppointment() {
        $searchModel = new AppointmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        $cardInfo = CardRecharge::getCardInfoByQueryNurse($dataProvider->query);

        return $this->render('appointment', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'cardInfo' => $cardInfo,
        ]);
    }

    /**
     * Displays a single Patient model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $patientRecordModel = PatientRecord::find()->select(['patient_id', 'type_description'])->where(['id' => $id, 'spot_id' => $this->spotId])->one();
        $patient_id = $patientRecordModel['patient_id'];
        $mutilModel = new MultiModel([
            'models' => [
                'patient' => $this->findModel($id, 2),
                'patientSubmeter' => $this->findSubmeterModel($patient_id),
                'patientRecordModel' => $patientRecordModel,
                'report' => $this->findReportModel($id)
            ]
        ]);
        $birthday = Patient::dateDiffage($mutilModel->getModel('patient')->birthday, time());
        return $this->render('view', [
                    'model' => $mutilModel,
        ]);
    }

    /**
     * Creates a new Patient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null) {
        $model = $this->findPatientModel($id);
        $model->scenario = 'report';
        $mutilModel = new MultiModel([
            'models' => [
                'patient' => $model, //患者信息
                'patientSubmeter' => $this->findSubmeterModel($id), //儿童持有信息
                'report' => new Report() //报到 就诊服务
            ]
        ]);
        $mutilModel->getModel('report')->scenario = 'report';
        if (($postParam = Yii::$app->request->post())) {
            if (isset($postParam['postParam'])) {
                $postParam = json_decode($postParam['postParam'], true);
                $id = $postParam['Patient']['id'];
                $id && $mutilModel->setModel('patient', $this->findPatientModel($postParam['Patient']['id']));
                $id && $mutilModel->setModel('patientSubmeter', $this->findSubmeterModel($postParam['Patient']['id']));
                $mutilModel->load($postParam);
                $model = $mutilModel->getModel('patient');
                return $this->saveReport($model, $mutilModel, 2, $id);
            } else {
                if ($mutilModel->load($postParam) && $mutilModel->validate()) {
                    //提示 是否有老用户
                    $similarUser = $this->checkOldUser($mutilModel);
                    if ($similarUser['patientType'] == 2 && !empty($similarUser['similarUser'])) {
                        $postParam['Patient']['id'] = $id;
                        $this->result['similarUser'] = $similarUser;
                        $this->result['postParam'] = $postParam;
                        return $this->result;
                    } else {
                        return $this->saveReport($model, $mutilModel, 1, $id);
                    }
                } else {
                    $errors = $mutilModel->errors;
                    $patientErrors = isset($errors['patient']) ? $mutilModel->getModel('patient')->errors : '';
                    $fimilyErrors = isset($errors['patientSubmeter']) ? $mutilModel->getModel('patientSubmeter')->errors : '';
                    $reportErrors = isset($errors['report']) ? $mutilModel->getModel('report')->errors : '';
                    Yii::$app->response->format = Response::FORMAT_JSON;
//                    $html=$this->viewReportPage($model, $mutilModel);
                    $this->result['patientError'] = $patientErrors;
                    $this->result['fimilyErrors'] = $fimilyErrors;
                    $this->result['reportErrors'] = $reportErrors;
                    $this->result['errCode'] = 1001;
                    return $this->result;
                }
            }
        } else {
            return $this->viewReportPage($model, $mutilModel, $id);
        }
    }

    public function actionConfirmReport() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $postParam = Yii::$app->request->post();
        $ret = [
            'title' => "",
            'content' => $this->renderAjax('_confirmReport', [
                'postParam' => json_encode($postParam['postParam']),
                'similarUser' => $postParam['similarUser'],
                'actionUrl' => $postParam['actionUrl']
            ]),
            'footer' =>
            Html::a('查看相似用户', ['patient-list', 'similarUser' => json_encode($postParam['similarUser']), 'actionUrl' => $postParam['actionUrl']], ['class' => 'btn btn-cancel btn-form', 'role' => 'modal-remote']) .
            Html::button('确认保存', ['class' => 'btn btn-default btn-form card-check-btn', 'type' => "submit"])
        ];
        return $ret;
    }

    public function actionPatientList() {
        $similarUserJson = Yii::$app->request->get('similarUser');
        $actionUrl = Yii::$app->request->get('actionUrl');
        $similarUser = json_decode($similarUserJson, true);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $recordNum = PatientRecord::getRecordNum(array_column($similarUser['similarUser'], 'id'));
        $ret = [
            'title' => "请选择相似用户进行报到",
            'content' => $this->renderAjax('_patientList', [
                'similarUser' => $similarUser['similarUser'],
                'actionUrl' => $actionUrl,
                'recordNum' => $recordNum,
            ]),
            'footer' =>
            Html::button('确认报到', ['class' => 'btn btn-default btn-form patient-report', 'disabled' => 'disabled'])
        ];
        return $ret;
    }

    /**
     * 
     * @param type $model patientModel
     * @param type $mutilModel Model数组 
     * @return type 渲染 新增报到的页面
     */
    protected function viewReportPage($model, $mutilModel, $id) {
        if (!$model->errors && $id) {
            $model->address = $model->province . '/' . $model->city . '/' . $model->area;
            $model->birthTime = $model->birthday ? date('Y-m-d', $model->birthday) : '';
            $model->hourMin = $model->birthday ? date('H:i', $model->birthday) : '';
//                 if (($record = $this->checkHasRecord($id)) != null) {
//                     $model->type = $record['type'];
//                 }
            $familyInfo = PatientFamily::find()->select(['relation', 'name', 'sex', 'birthday', 'iphone', 'card'])->where(['patient_id' => $model->id])->asArray()->all();
        } else {
            if (isset($model->family_relation)) {
                foreach ($model->family_relation as $k => $v) {
                    $familyInfo[] = [
                        'name' => $model->family_name[$k],
                        'iphone' => $model->family_iphone[$k],
                        'relation' => $model->family_relation[$k],
                        'sex' => $model->family_sex[$k],
                        'birthday' => strtotime($model->family_birthday[$k]),
                        'card' => strtotime($model->family_card[$k]),
                    ];
                }
            }
        }
        if (empty($familyInfo)) {
            $familyInfo[] = [];
        }
        $doctorInfo = User::getDoctorList();
        return $this->render('create', [
                    'model' => $mutilModel,
                    'familyInfo' => $familyInfo,
                    'doctorInfo' => $doctorInfo,
        ]);
    }

    /**
     * 
     * @param type $model patientModel
     * @param type $mutilModel Model数组 
     * @return type 渲染 新增报到的页面
     */
    protected function viewUpdateReportPage($model, $mutilModel, $id, $reportModel) {
        if (!$model->errors && $id) {
            $model->address = $model->province ? $model->province . '/' . $model->city . '/' . $model->area : '';
            $model->birthTime = date('Y-m-d', $model->birthday);
            $model->hourMin = date('H:i', $model->birthday);

            $familyInfo = PatientFamily::find()->select(['relation', 'name', 'sex', 'birthday', 'iphone', 'card'])->where(['patient_id' => $model->id])->asArray()->all();
        } else {
            if (isset($model->family_relation)) {
                foreach ($model->family_relation as $k => $v) {
                    $familyInfo[] = [
                        'name' => $model->family_name[$k],
                        'iphone' => $model->family_iphone[$k],
                        'relation' => $model->family_relation[$k],
                        'sex' => $model->family_sex[$k],
                        'birthday' => strtotime($model->family_birthday[$k]),
                        'card' => strtotime($model->family_card[$k]),
                    ];
                }
            }
        }
        if (empty($familyInfo)) {
            $familyInfo[] = [];
        }
        $doctorInfo = User::getDoctorList();
        $deleteStatus = false;
        //判断原医生是否被禁用或者删除，若是，则默认回填
        if (!isset($doctorInfo[$reportModel->doctor_id])) {
            $doctorInfo[$reportModel->doctor_id] = [
                'doctor_id' => $reportModel->doctor_id,
                'doctorName' => $reportModel->doctorName,
            ];
            $deleteStatus = true;
        }
        $secondDepartmentName = SecondDepartment::getDepartmentFields($reportModel->second_department_id, ['name'])['name'];
        return $this->render('update', [
                    'model' => $mutilModel,
                    'familyInfo' => $familyInfo,
                    'deleteStatus' => $deleteStatus,
                    'doctorInfo' => $doctorInfo,
                    'secondDepartmentName' => $secondDepartmentName
        ]);
    }

    /**
     * Updates an existing Patient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $patientId = Yii::$app->request->get('patientId');
        $entrance = Yii::$app->request->post('entrance');
        if ($patientId) {
            $model = $this->findSimilarPatientModel($id, $patientId);
        } else {
            $model = $this->findModel($id);
        }
        $model->scenario = 'report';
        $patientId = $model->id;
        $mutilModel = new MultiModel([
            'models' => [
                'patient' => $model,
                'patientSubmeter' => $this->findSubmeterModel($patientId),
                'report' => $this->findReportModel($id)
            ]
        ]);
        $appointmentInfo = Appointment::getAppointmentUserInfo($id);
        $reportModel = $mutilModel->getModel('report');
        $reportModel->scenario = 'report';
        if (!empty($appointmentInfo) && !$reportModel->id) {//若是从预约待报到进来的，则默认回填对应信息
            $reportModel->doctor_id = $appointmentInfo['doctor_id'];
            $reportModel->second_department_id = $appointmentInfo['second_department_id'];
            $reportModel->type = $model->type;
            $reportModel->doctorName = $appointmentInfo['username'];
            $reportModel->type_description = $appointmentInfo['type_description'];
        }
        
        if (($postParam = Yii::$app->request->post())) {
            if (isset($postParam['postParam'])) {
                $postParam = json_decode($postParam['postParam'], true);
                $id = $postParam['Patient']['id'];
                if ($id) {
                    $model = $this->findModel($postParam['Patient']['id']);
                    $model->scenario = 'report';
                    $mutilModel->setModel('patient', $model);
                    $mutilModel->setModel('patientSubmeter', $this->findSubmeterModel($patientId));
                }
                if ($postParam['entrance'] == 2) {
                    $mutilModel->getModel('patient')->birthTime = date("Y-m-d", $mutilModel->getModel('patient')->birthday);
                    $mutilModel->getModel('patient')->hourMin = date("H:i", $mutilModel->getModel('patient')->birthday);
                    return $this->saveUpdateReport($model, $mutilModel, $reportModel, 2, $id, $patientId, $postParam['entrance'],$appointmentInfo);
                } else {
                    $mutilModel->load($postParam);
                    $model = $mutilModel->getModel('patient');
                    return $this->saveUpdateReport($model, $mutilModel, $reportModel, 2, $id, $patientId,1,$appointmentInfo);
                }
            } else {
                //从护士工作台进入，不用再load数据
                if ($entrance == 2) {
                    $mutilModel->getModel('patient')->birthTime = date("Y-m-d", $mutilModel->getModel('patient')->birthday);
                    $mutilModel->getModel('patient')->hourMin = date("H:i", $mutilModel->getModel('patient')->birthday);
                    $similarUser = $this->checkOldUser($mutilModel);
                    if ($similarUser['patientType'] == 2 && !empty($similarUser['similarUser'])) {
                        $postParam['Patient']['id'] = $id;
                        $this->result['similarUser'] = $similarUser;
                        $this->result['postParam'] = $postParam;
                        return $this->result;
                    } else {
                        return $this->saveUpdateReport($model, $mutilModel, $reportModel, 1, $id, $patientId, $entrance,$appointmentInfo);
                    }
                } else {
                    //此处是为了防止表单数据有变动，护士工作台直接报到不会修改表单，所以可不用再load数据
                    if ($mutilModel->load($postParam) && $mutilModel->validate()) {
                        //提示 是否有老用户
                        $similarUser = $this->checkOldUser($mutilModel);
                        if ($similarUser['patientType'] == 2 && !empty($similarUser['similarUser'])) {
                            $postParam['Patient']['id'] = $id;
                            $this->result['similarUser'] = $similarUser;
                            $this->result['postParam'] = $postParam;
                            return $this->result;
                        } else {
                            return $this->saveUpdateReport($model, $mutilModel, $reportModel, 1, $id, $patientId,1,$appointmentInfo);
                        }
                    } else {
                        $errors = $mutilModel->errors;
                        $patientErrors = isset($errors['patient']) ? $mutilModel->getModel('patient')->errors : '';
                        $fimilyErrors = isset($errors['patientSubmeter']) ? $mutilModel->getModel('patientSubmeter')->errors : '';
                        $reportErrors = isset($errors['report']) ? $mutilModel->getModel('report')->errors : '';
                        Yii::$app->response->format = Response::FORMAT_JSON;
//                    $html=$this->viewReportPage($model, $mutilModel);
                        $this->result['patientError'] = $patientErrors;
                        $this->result['fimilyErrors'] = $fimilyErrors;
                        $this->result['reportErrors'] = $reportErrors;
                        $this->result['errCode'] = 1001;
                        return $this->result;
                    }
                }
            }
        } else {
            return $this->viewUpdateReportPage($model, $mutilModel, $id, $reportModel);
        }
    }

    public function checkHasRecord($id) {
        $query = (new Query());
        $query->from(['a' => Report::tableName()]);
        $query->select(['a.id', 'a.record_id', 'b.status', 'b.type']);
        $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id = {{b}}.id');
        $query->where(['a.patient_id' => $id, 'a.spot_id' => $this->spotId]);
        $query->andWhere(['between', 'a.create_time', strtotime(date('Y-m-d', time())), strtotime(date('Y-m-d', time() + 86400))]);
        $query->andWhere(['in', 'b.status', [2, 3, 4]]);
        $hasRecord = $query->one();
//若今日内已经登记，则提示已登记
        return $hasRecord;
    }

    /**
     * @return 获取患者基本信息
     * @param 患者id $id
     */
    protected function findPatientModel($id = null) {
        if ($id) {
            $model = Patient::findOne(['id' => $id, 'spot_id' => $this->parentSpotId]);
            if ($model !== null) {
                return $model;
            } else {
                return (new Patient());
            }
        } else {
            return (new Patient());
        }
    }

    /**
     * @param 患者id
     * @return 获取儿童特有信息
     */
    protected function findSubmeterModel($id = null) {
        $model = PatientSubmeter::findOne(['patient_id' => $id, 'spot_id' => $this->parentSpotId]);
        if ($model !== null) {
            return $model;
        } else {
            $model = new PatientSubmeter();
            $model->patient_id = $id;
            $model->spot_id = $this->parentSpotId;
            return $model;
        }
    }

    /**
     * @return 获取就诊流水详情纪录
     * @param integer $id  就诊流水id
     */
    public function findPatientRecordModel($id) {

        $patientRecordModel = PatientRecord::find()->where(['id' => $id, 'spot_id' => $this->spotId])->one();
        if ($patientRecordModel !== null) {
            return $patientRecordModel;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @param integer $id 就诊流水id
     * @return 返回报到记录详情
     */
    public function findReportModel($id) {

        $reportQuery = new ActiveQuery(Report::className());
        $reportQuery->from(['a' => Report::tableName()]);
        $reportQuery->select(['a.*', 'doctorName' => 'b.username']);
        $reportQuery->leftJoin(['b' => User::tableName()], '{{a}}.doctor_id = {{b}}.id');
        $reportQuery->where(['a.record_id' => $id, 'a.spot_id' => $this->spotId]);
        $reportModel = $reportQuery->one();
        if ($reportModel !== null) {
            return $reportModel;
        } else {
            $patientInfo = Patient::getUserInfo($id);
            $reportModel = new Report();
            $reportModel->record_id = $id;
            $reportModel->patient_id = $patientInfo['patient_id'];
            return $reportModel;
        }
    }

    /**
     * Deletes an existing Patient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {

        $request = Yii::$app->request;
        if ($request->isAjax) {

            /*
             *   Process for ajax request
             */
            $db = Yii::$app->db;
            $result = $db->createCommand()->delete(PatientRecord::tableName(), ['id' => $id, 'spot_id' => $this->spotId])->execute();
            if ($result) {
                $db->createCommand()->delete(Report::tableName(), ['record_id' => $id])->execute();
                $db->createCommand()->delete(Appointment::tableName(), ['record_id' => $id])->execute();
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * @param $id 流水id
     * @return 报到后关闭
     */
    public function actionClose($id) {

        $request = Yii::$app->request;
        if ($request->isAjax) {

           $patientRecordModel = $this->findPatientRecordModel($id);
            $patientRecordModel->status = 9;
            $patientRecordModel->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /**
     * 
     * @param type $model 
     * @return 检测是否存在老用户  并提示
     */
    protected function checkOldUser($mutilModel) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $patientModel = $mutilModel->getModel('patient');
        if ($patientModel->patient_number && $patientModel->patient_number != '000000') {//老用户
            $patientType = 1;
            $similarUser = Patient::similarPatient($patientModel, $patientType);
        } else {//新用户
            $patientType = 2;
            $similarUser = Patient::similarPatient($patientModel, $patientType);
        }
        return ['patientType' => $patientType, 'similarUser' => $similarUser];
    }

    /**
     * 
     * @param type $model
     * @param type $mutilModel
     * @param type $type 1/默认是表单提交  2/弹窗提交
     * @param array $appointmentInfo 就诊预约记录
     * @return type  更新时 保存表单信息
     */
    protected function saveUpdateReport($model, $mutilModel, $reportModel, $type = 1, $id, $patientId, $entrance = 1,$appointmentInfo = null) {
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $oldPatientId = 0;
            
            $typeInfo = SpotType::getTypeFields($mutilModel->getModel('report')->type, ['type']);
            if ($typeInfo['type'] != '') {
                $mutilModel->getModel('report')->type_description = $typeInfo['type'];
            }
            //根据用户报到手机号判断用户是否有会员卡
            $isVip = CardRecharge::find()->select('f_physical_id')->where(['f_phone' => $mutilModel->getModel('patient')->iphone, 'f_spot_id' => $this->spotId])->asArray()->one();
            if (!empty($isVip) && !$reportModel->id) {//若是从预约待报到进来的,判断是否是会员
                $reportModel->is_vip = 1;
            }
            if ($patientId) {// 如果选择了相似用户  需要修改记录
                $oldPatientId = $mutilModel->getModel('report')->patient_id;
                $mutilModel->getModel('report')->patient_id = $patientId;
            }
//            if ($mutilModel->getModel('patient')->patient_number = '0000000') {//如果是已经存在的用户   但是病历号默认时  则更新病历号
//                $mutilModel->getModel('patient')->patient_number = Patient::generatePatientNumber();
//            }
            $mutilModel->getModel('report')->record_type = Report::getRecordType($mutilModel->getModel('report')->type); //修改病例类型
            if(!$mutilModel->getModel('report')->isNewRecord && ($reportModel->getOldAttribute('doctor_id') != $reportModel->doctor_id || $reportModel->getOldAttribute('type') != $reportModel->type)){
                //查询医生加服务类型关联的诊金
                $medicalFee = UserAppointmentConfig::getMedicalFee($mutilModel->getModel('report')->doctor_id,$mutilModel->getModel('report')->type);
                Yii::$app->db->createCommand()->update(PatientRecord::tableName(), ['price' => $medicalFee['price'],'record_price' => $medicalFee['price']],['id' => $reportModel->record_id])->execute();
            }
            $mutilModel->save();
            Yii::info('saveUpdateReport error1 ' . json_encode($mutilModel->getModel('patient')->errors));
            Yii::info('saveUpdateReport error2 ' . json_encode($mutilModel->getModel('report')->errors));
            /* 更新患者家庭关系 */
            Yii::$app->db->createCommand()->delete(PatientFamily::tableName(), ['patient_id' => $model->id])->execute();
            if (!empty($model->family_relation) && !empty($model->family_relation[0])) {
                foreach ($model->family_relation as $key => $v) {
                    $familyModel = new PatientFamily();
                    $familyModel->relation = $v;
                    $familyModel->name = $model->family_name[$key];
                    $familyModel->patient_id = $model->id;
                    $familyModel->iphone = $model->family_iphone[$key];
                    $familyModel->sex = $model->family_sex[$key];
                    $familyModel->birthday = $model->family_birthday[$key];
                    $familyModel->card = $model->family_card[$key];
                    $result = $familyModel->save();
                    Yii::info('familyModel save error ' . json_encode($familyModel->errors));
                }
            }
            if ($reportModel->doctor_id) {
                $triageInfoModel = TriageInfo::findModel($id);
                $triageInfoModel->record_id = $id;
                $triageInfoModel->doctor_id = $reportModel->doctor_id;
                $triageInfoModel->spot_id = $this->spotId;
                $triageInfoModel->save();
            }
            //若今日内已经登记，则提示已登记
//            if ($this->checkHasRecord($model->id)) {
//                //更新就诊记录流水表的接诊类型
//                Yii::$app->db->createCommand()->update(PatientRecord::tableName(), ['type' => $model->type], ['id' => $id])->execute();
//                if ($model->status == 1) {
//                    $msg = '该患者已经登记';
//                    $url = $entrance == 2 ? Yii::$app->request->referrer : Url::to(['@reportRecordAppointment']);
////                    $url = Url::to(['@reportRecordAppointment']);
//                } else {
//                    $url = $entrance == 2 ? Yii::$app->request->referrer : Url::to(['index']);
//                    $msg = '保存成功';
//                }
//                $dbTrans->commit();
//                if ($type == 1) {
//                    Yii::$app->getSession()->setFlash('success', $msg);
//                    return $this->redirect($url);
//                } else {
//                    //弹窗返回json
//                    Yii::$app->response->format = Response::FORMAT_JSON;
//                    return [
//                        'forceClose' => true,
//                        'forceMessage' => $msg,
//                        'forceRedirect' => $url
//                    ];
//                }
////                return $this->redirect($url);
//            }
            /* 若是从预约入口进来的登记，则修改就诊记录流水状态 */
            if ($model->status == 1) {
                $patientRecordModel = $this->findPatientRecordModel($id);
                $patientRecordModel->status = PatientRecord::$setStatus[2];
                if ($patientId) {// 如果选择了相似用户  需要修改记录) {//如果   选择了相似用户
                    //将就诊流水更改为  新的用户
                    $patientRecordModel->patient_id = $patientId;
                }
                if($appointmentInfo['doctor_id'] != $reportModel->doctor_id || $patientRecordModel->type != $reportModel->type){
                    //查询医生加服务类型关联的诊金
                    $medicalFee = UserAppointmentConfig::getMedicalFee($mutilModel->getModel('report')->doctor_id,$mutilModel->getModel('report')->type);
                    $patientRecordModel->price = $medicalFee['price'];
                    $patientRecordModel->record_price = $medicalFee['price'];
                }
                $patientRecordResult = $patientRecordModel->save();
                //修改预约的patientId
                $appointmentModel = Appointment::getAppointment($id);
                if ($patientId && $appointmentModel) {//如果是预约进来的
                    $appointmentModel->patient_id = $patientId;
                    $appointmentModel->save(false);
                    Patient::deleteAll(['id' => $oldPatientId, 'patient_number' => '0000000']);
                }
            }
            
                                               
            //同步患者基本过敏史到就诊记录过敏史
            PatientAllergy::syncOutpatientAllergy($patientId, $id);
                    
                
            Yii::info($reportModel->doctor_id . ' reportModelDoctorId');
            if ($reportModel->doctor_id) {//判断医生的常用诊室  是否自动分诊
                TriageInfo::autoTriage($reportModel->doctor_id, $id);
            }
            $dbTrans->commit();
            $url = $entrance == 2 ? Yii::$app->request->referrer : Url::to(['@reportRecordAppointment']);
            if ($type == 1) {
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect($url);
            } else {
                //弹窗返回json
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'forceClose' => true,
                    'forceMessage' => '保存成功',
                    'forceRedirect' => $url
                ];
            }
        } catch (Exception $e) {
            Yii::info('saveUpdateReport error ' . $e->getMessage());
            $dbTrans->rollBack();
        }
    }

    /**
     * 
     * @param type $model
     * @param type $mutilModel
     * @param type $type 1/默认是表单提交  2/弹窗提交
     * @return type 
     */
    protected function saveReport($model, $mutilModel, $type = 1, $id) {
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $firstRecord = PatientRecord::getFirstRecord($model->id);
            $model->first_record = $firstRecord;
            if ($model->patient_number == '0000000') {//如果是已经存在的用户   但是病历号默认时  则更新病历号
                $model->patient_number = Patient::generatePatientNumber();
            }
            if ($model->save()) {
                $mutilModel->getModel('patientSubmeter')->patient_id = $model->id;
                $mutilModel->getModel('patientSubmeter')->spot_id = $this->parentSpotId;
                if ($mutilModel->getModel('patientSubmeter')->save()) {
                    /* 保存家庭成员 */
                    if ($model->family_relation) {
                        Yii::$app->db->createCommand()->delete(PatientFamily::tableName(), ['patient_id' => $model->id])->execute();
                        foreach ($model->family_relation as $key => $v) {
                            $familyModel = new PatientFamily();
                            $familyModel->relation = $v;
                            $familyModel->name = $model->family_name[$key];
                            $familyModel->patient_id = $model->id;
                            $familyModel->iphone = $model->family_iphone[$key];
                            $familyModel->sex = $model->family_sex[$key];
                            $familyModel->birthday = $model->family_birthday[$key];
                            $familyModel->card = $model->family_card[$key];
                            $result = $familyModel->save();
                        }
                    }
//                    if ($this->checkHasRecord($id)) {
//                        $url = Url::to(['@reportRecordAppointment']);
//                        $dbTrans->commit();
//                        Yii::$app->getSession()->setFlash('success', '该患者已经登记');
//                        return $this->redirect($url);
//                    }
                    /* 新增流水记录 */
                    $patientRecord = new PatientRecord();
                    $patientRecord->patient_id = $model->id;
                    $patientRecord->status = PatientRecord::$setStatus[2];
                    //查询医生加服务类型关联的诊金
                    $medicalFee = UserAppointmentConfig::getMedicalFee($mutilModel->getModel('report')->doctor_id,$mutilModel->getModel('report')->type);
                    $patientRecord->price = $medicalFee['price'];
                    $patientRecord->record_price = $medicalFee['price'];
                    /* $patientRecord->type = $model->type;
                      $spotTypeInfo = SpotType::getSpotType(['id' => $model->type,'status' => 1])[0];
                      $patientRecord->type_description = $spotTypeInfo['name'];
                      $patientRecord->type_time = $spotTypeInfo['time']; */
                    $patientRecordResult = $patientRecord->save();
                    
                    //同步患者基本过敏史到就诊记录过敏史
                    PatientAllergy::syncOutpatientAllergy($patientRecord->patient_id, $patientRecord->id);
                    
                    if ($patientRecordResult) {
                        //根据用户报到手机号判断用户是否有会员卡
                        $isVip = CardRecharge::find()->select('f_physical_id')->where(['f_phone' => $mutilModel->getModel('patient')->iphone, 'f_spot_id' => $this->spotId])->asArray()->one();
                        /* 保存登记记录 */
                        $reportModel = $mutilModel->getModel('report');
                        $typeInfo = SpotType::getTypeFields($reportModel->type, ['type']);
                        if (!empty($isVip)) {
                            $reportModel->is_vip = 1;
                        }
                        $reportModel->patient_id = $model->id;
                        $reportModel->record_id = $patientRecord->id;
                        $reportModel->type_description = $typeInfo['type'];
                        $reportModel->record_type = Report::getRecordType($reportModel->type);
                        $reportModel->save();
                    }
                    //如果是按医生预约或报到的 将医生ID插入分诊表
//                    $appointment = Appointment::find()->select(['doctor_id'])->where(['record_id' => $id])->asArray()->one();
                    if ($reportModel->doctor_id) {
                        $triageInfoModel = TriageInfo::findModel($patientRecord->id);
                        $triageInfoModel->record_id = $patientRecord->id;
                        $triageInfoModel->doctor_id = $reportModel->doctor_id;
                        $triageInfoModel->spot_id = $this->spotId;
                        $triageInfoModel->save();
                        //判断医生的常用诊室  是否自动分诊
                        TriageInfo::autoTriage($reportModel->doctor_id, $patientRecord->id);
                    }
                    $dbTrans->commit();
                    $url = Url::to(['@reportRecordAppointment']);
                    if ($type == 1) {
                        Yii::$app->getSession()->setFlash('success', '保存成功');
                        return $this->redirect($url);
                    } else {
                        //弹窗返回json
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return [
                            'forceClose' => true,
                            'forceMessage' => '保存成功',
                            'forceRedirect' => $url
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            Yii::info('saveReport failed: ' . $e->getMessage());
            $dbTrans->rollBack();
        }
    }

    /**
     * Finds the Patient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Patient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $actionType = 1) {

        $query = new ActiveQuery(Patient::className());
        $query->from(['a' => PatientRecord::tableName()]);
        $query->select(['a.status', 'a.type', 'a.type_time', 'b.*']);
        $query->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id');
        $query->where(['a.id' => $id, 'a.spot_id' => $this->spotId]);

        if ($actionType == 1) {
            $query->andWhere(['a.status' => [1, 2]]);
        }
        $model = $query->one();
//        var_dump($model);exit();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    protected function findSimilarPatientModel($id, $patientId) {
        $model = Patient::findOne(['id' => $patientId, 'spot_id' => $this->parentSpotId]);
        if ($model !== null) {
            $patientRecord = PatientRecord::find()->select(['status', 'type', 'type_time'])->where(['id' => $id, 'spot_id' => $this->spotId])->one();
            $model->status = $patientRecord->status;
            $model->type = $patientRecord->type;
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
