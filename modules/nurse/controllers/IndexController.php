<?php

namespace app\modules\nurse\controllers;

use app\common\Common;
use app\modules\nurse\models\NurseDoctorConfig;
use app\modules\spot_set\models\UserPriceConfig;
use Yii;
use app\modules\nurse\models\Nurse;
use app\common\base\BaseController;
use yii\filters\VerbFilter;
use yii\db\Query;
use app\modules\triage\models\TriageInfo;
use app\modules\user\models\User;
use app\modules\patient\models\PatientRecord;
use app\modules\patient\models\Patient;
use app\modules\nurse\models\search\NurseSearch;
use app\specialModules\recharge\models\CardRecharge;
use yii\web\NotFoundHttpException;
use app\modules\report\models\Report;
use app\modules\outpatient\models\InspectRecord;
use app\modules\outpatient\models\CheckRecord;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\RecipeRecord;
use app\common\base\MultiModel;
use app\modules\patient\models\PatientSubmeter;
use app\modules\patient\models\PatientFamily;
use app\modules\spot_set\models\Room;
use yii\web\Response;
use app\modules\patient\models\PatientAllergy;
use app\modules\spot_set\models\SpotType;
use app\modules\spot_set\models\UserAppointmentConfig;

/**
 * IndexController implements the CRUD actions for Nurse model.
 */
class IndexController extends BaseController
{
    public function behaviors()
    {
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
     * 新的护士工作台
     */
    public function actionIndex(){
        $request = Yii::$app->request;
        if($request->isPost){
            return $this->nurseDoctorConfig();
        }
        $searchModel = new NurseSearch();
        $params = Yii::$app->request->queryParams;
        $userId = $this->userInfo->id;
        $nurseDoctorConfigModel  = new NurseDoctorConfig();
        //获取当前诊所正常状态的医生
        $allDoctor = User::getDoctorList();
        //获取当前护士关注的医生id
        $doctorList = NurseDoctorConfig::getDoctorList($userId);
        $doctorList = !empty($doctorList)?$doctorList:$allDoctor;
        $doctorIdArr = array_column($doctorList,'doctor_id');
        $dateFormat = substr($params["date"],0,10);//将 (yyyy-mm-dd 周*) 格式改成(yyyy-mm-dd)
        $date = isset($params['date'])?$dateFormat:date("Y-m-d");
        $params['date']=$date;
        //默认选中医生id
        $doctorSelectedDefault[] = $doctorIdArr[0];
        $doctorSelectedId = !empty($params['doctor_id'])?explode(',',$params['doctor_id']):$doctorSelectedDefault;
        $nurseDoctorConfigModel->doctor_id = $doctorIdArr;
        //当前诊所当前时间的护士关注的所有排班医生
        $scheduleDoctor = NurseDoctorConfig::getschedulingDoctor($date);
        $scheduleDoctorList = array_column($scheduleDoctor,'user_id');
        foreach($doctorList as $key => $value){
            if(in_array($value['doctor_id'],$scheduleDoctorList)){
                $doctorList[$key]['isSchedule'] = true;
            }else{
                $doctorList[$key]['isSchedule'] = false;
            }

            if(in_array($value['doctor_id'],$doctorSelectedId)){
                $doctorList[$key]['selected'] = true;
            }else{
                $doctorList[$key]['selected'] = false;
            }
        }
        //获取预约未到店数据
        $appointmentDataProvider = $searchModel->appointmentSearch($params,$doctorSelectedId);
        //获取已到店数据
        $reportDataProvider = $searchModel->reportSearch($params,$doctorSelectedId);
        //获取预约未到店的会员信息
        $appointmentCardInfo = CardRecharge::getCardInfoByQueryNurse($appointmentDataProvider->query);
        //获取已到店的会员信息
        $reportCardInfo = CardRecharge::getCardInfoByQueryNurse($reportDataProvider->query);
        //获取预约未到店人数和已到店人数
        $appointmentNum = $appointmentDataProvider->query->count();
        $reportNum = $reportDataProvider->query->count();
        //获取是否已经开医嘱
        $patientOrders = $this->getPatientOrders($date,$doctorSelectedId);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'appointmentDataProvider' => $appointmentDataProvider,
            'reportDataProvider' => $reportDataProvider,
            'appointmentNum' => $appointmentNum,
            'reportNum' => $reportNum,
            'appointmentCardInfo' => $appointmentCardInfo,
            'reportCardInfo' => $reportCardInfo,
            'allDoctor' => $allDoctor,
            'nurseDoctorConfigModel' => $nurseDoctorConfigModel,
            'doctorId' => $doctorSelectedId,
            'docFocusList' => $doctorList,
            'date' =>$date,
            'patientOrders' => $patientOrders,
            'doctorIdArr' => $doctorIdArr
        ]);


    }
    
    public function actionCreateRecord($id = null){
        
        $request = Yii::$app->request;
        
        $model = $this->findPatientModel($id);
        $model->scenario = 'report';
        $mutilModel = new MultiModel([
            'models' => [
                'patient' => $model, //患者信息
                'patientSubmeter' => $this->findSubmeterModel($id), //儿童持有信息
                'report' => new Report(), //报到 就诊服务
                'triageInfo' => new TriageInfo()
            ]
        ]);
        $mutilModel->getModel('report')->type = 0;
        $mutilModel->getModel('triageInfo')->scenario = 'create-record';
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            if($mutilModel->load($request->post()) && $mutilModel->validate()){
                if($model->isNewRecord){
                    $this->result['errorCode'] = 1001;
                    $this->result['msg'] = '请选择就诊用户';
                    return $this->result;
                }
                if($model->patient_number == '0000000'){
                    $this->result['errorCode'] = 1001;
                    $this->result['msg'] = '该用户在诊所没有就诊记录';
                    return $this->result;
                }
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $model->save();
                    $mutilModel->getModel('patientSubmeter')->save();
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
                    
                    /* 新增流水记录 */
                    $patientRecord = new PatientRecord();
                    $patientRecord->patient_id = $model->id;
                    $patientRecord->status = PatientRecord::$setStatus[3];

//                    //查询医生方便门诊的诊金
                    $medicalFee = UserPriceConfig::getMedicalFee(['user_id' =>$mutilModel->getModel('report')->doctor_id,'type' => 1]);
                    $patientRecord->price = $medicalFee['price'];
                    $patientRecord->record_price = $medicalFee['price'];

                    $patientRecordResult = $patientRecord->save();
                    //同步患者基本过敏史到就诊记录过敏史
                    PatientAllergy::syncOutpatientAllergy($patientRecord->patient_id, $patientRecord->id);
                    if ($patientRecordResult) {
                        //根据用户报到手机号判断用户是否有会员卡
                        $isVip = CardRecharge::find()->select('f_physical_id')->where(['f_phone' => $mutilModel->getModel('patient')->iphone, 'f_spot_id' => $this->spotId])->asArray()->one();
                        /* 保存登记记录 */
                        $reportModel = $mutilModel->getModel('report');
                        if (!empty($isVip)) {
                            $reportModel->is_vip = 1;
                        }
                        $reportModel->patient_id = $model->id;
                        $reportModel->record_id = $patientRecord->id;
                        $reportModel->type = 0;
                        $reportModel->type_description = '方便门诊';
                        $reportModel->record_type = 1; //方便门诊默认为非专科
                        $reportModel->save();
                    }
                    $triageInfoModel = $mutilModel->getModel('triageInfo');
                    $triageInfoModel->record_id = $patientRecord->id;
                    $triageInfoModel->doctor_id = $reportModel->doctor_id;
                    $triageInfoModel->triage_time = time();
                    $triageInfoModel->save();
                    $dbTrans->commit();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $this->dataReport();
                    return $this->result;
                    
                    
                }catch (\yii\db\Exception $e){
                    $dbTrans->rollBack();
                    Yii::error($e->errorInfo,'nurse-create-record');
                }
                
                
                
            }else{
                $this->result['errorCode'] = 1002;
                $this->result['msg'] = $mutilModel->errors['patient'][0][0];
                return $this->result;
            }
            
        }else{
            if ($id) {
                $model->address = $model->province . '/' . $model->city . '/' . $model->area;
                $model->birthTime = $model->birthday ? date('Y-m-d', $model->birthday) : '';
                $model->hourMin = $model->birthday ? date('H:i', $model->birthday) : '';
                $familyInfo = PatientFamily::find()->select(['relation', 'name', 'sex', 'birthday', 'iphone', 'card'])->where(['patient_id' => $model->id])->asArray()->all();
            }
            if (empty($familyInfo)) {
                $familyInfo[] = [];
            }
            $doctorInfo = User::getDoctorList();
            $room = Room::getRoomList(1);
            return $this->render('create-record',[
                'model' => $mutilModel,
                'familyInfo' => $familyInfo,
                'doctorInfo' => $doctorInfo,
                'room' => $room
            ]);
        }
        
    }

    protected  function dataReport(){

        $moduleId = Yii::$app->controller->module->id;
        $controllerId = Yii::$app->controller->id;
        $requestUrl = '/' . $moduleId . '/' . $controllerId . '/' . Yii::$app->controller->action->id;
        $request=Yii::$app->request;
        //数据上报
        $reportData = [
            'url' => $request->getHostInfo() . $request->getUrl(),
            'eventType' => 1, //1为普通URL 2为普通点击事件
            'ip' => $request->userIP,
            'module' => $moduleId,
            'action' => $requestUrl,
            'name' => '',//eventType==1时可为空,eventType==2时需要给出点击事件的数据统计用途(如：增加家庭成员点击数据上报)
        ];
        Common::dataReport($this->userInfo->id, $this->spotId, $reportData);

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
                throw new NotFoundHttpException('你所请求的页面不存在');
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
     * @return 返回对应的医嘱执行的执行状态和名称
     * @param 就诊流水id数组 $recordArray
     * @param 门诊－医嘱执行的model名称 $modelName
     */
    protected function getList($recordArray,$modelName) {
        $rows = [];
        if(empty($recordArray)){
            return $rows;
        }
        $query = new Query();
        $query->from($modelName);
        $query->select(['name','status','record_id']);
        $query->where(['record_id' => $recordArray]);
        $result = $query->all();
        foreach ($result as $v){
            $rows[$v['record_id']][] = [
                'name' => $v['name'],
                'status' => $v['status']
            ];
        }
        return $rows;
    }
    /**
     * @return 返回对应的诊室关联的医生和患者姓名
     * @param  就诊流水id数组 $recordArray
     */
    protected function getDoctorName($recordArray) {
        $rows = [];
        if(empty($recordArray)){
            return $rows;
        }
        $query = new Query();
        $query->from(['a' => TriageInfo::tableName()]);
        $query->select(['a.record_id','doctorName'=>'b.username','c.patient_id','patientName'=>'d.username']);
        $query->leftJoin(['b' => User::tableName()],'{{a}}.doctor_id = {{b}}.id');
        $query->leftJoin(['c' => PatientRecord::tableName()],'{{a}}.record_id = {{c}}.id');
        $query->leftJoin(['d' => Patient::tableName()],'{{c}}.patient_id = {{d}}.id');
        $query->where(['a.spot_id' => $this->spotId,'a.record_id' => $recordArray]);
        $result = $query->all();
        foreach ($result as $v){
            $rows[$v['record_id']] = [
                'doctorName' => $v['doctorName'],
                'patientName' => $v['patientName']
            ];
        }
        return $rows;
    }

    /**
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @desc 我关注的医生后台
     */
    public function nurseDoctorConfig(){
        $userId = $this->userInfo->id;
        $nurseDoctorConfigModel = new NurseDoctorConfig();
        if($nurseDoctorConfigModel->load(Yii::$app->request->post()) && $nurseDoctorConfigModel->validate()){
            if(!empty($nurseDoctorConfigModel->doctor_id)){
                foreach($nurseDoctorConfigModel->doctor_id as $value){
                    $rows[] = [$userId,$value,$this->spotId,time(),time()];
                }
            }
            NurseDoctorConfig::deleteAll(['user_id' => $userId,'spot_id' => $this->spotId]);
            //批量增加护士关注的医生
            Yii::$app->db->createCommand()->batchInsert(NurseDoctorConfig::tableName(),['user_id','doctor_id','spot_id','create_time','update_time'], $rows)->execute();
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['index']); 
        }else{
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }


/**
     * @return 返回是否已经开医嘱
     */
    private function getPatientOrders($date,$doctorId) {
        $query = new Query();
        $query->from(['a' => Report::tableName()]);
        $query->select([
                'record_id'=>'b.id'
        ]);
        $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id={{b}}.id');

        $query->andFilterWhere([
            'a.spot_id' => $this->spotId,
            'a.doctor_id' => $doctorId,
        ]);
        $nurseDate = $date ? $date : date('Y-m-d');
        $query->andFilterWhere(['between', 'a.create_time', strtotime($nurseDate), strtotime($nurseDate) + 86400]);
        $data = $query->all();
        $recordArray = [];
        $result = [];
        foreach ($data as $key => $value) {
            $result[$value['record_id']] = 0;//初始化为未开医嘱
            $recordArray[] = $value['record_id'];
        }
        
        $query = new Query();
        $inspectList = $query->from(['t' => InspectRecord::tableName()])
                ->select(['t.record_id'])
                ->where(['t.spot_id' => $this->spotId, 't.record_id' => $recordArray])
                ->indexBy('record_id')
                ->all();
        $this->arrayMerge($inspectList,$result);
        
        $checkList = $query->from(['t' => CheckRecord::tableName()])
                ->select(['t.record_id'])
                ->where(['t.spot_id' => $this->spotId, 't.record_id' => $recordArray])
                ->indexBy('record_id')
                ->all();
        $this->arrayMerge($checkList,$result);
        
        $cureList = $query->from(['t' => CureRecord::tableName()])
                ->select(['t.record_id'])
                ->where(['t.spot_id' => $this->spotId, 't.record_id' => $recordArray])
                ->indexBy('record_id')
                ->all();
        $this->arrayMerge($cureList,$result);
        
        $recipeList = $query->from(['t' => RecipeRecord::tableName()])
                ->select(['t.record_id'])
                ->where(['t.spot_id' => $this->spotId, 't.record_id' => $recordArray])
                ->all();
        $this->arrayMerge($recipeList,$result);
        return $result;
    }
    
    private function arrayMerge($array,&$result) {
        foreach ($array as $key => $value) {
            $result[$key] = 1;
        }
        return ;
    }
}
