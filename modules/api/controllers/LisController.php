<?php

/**
 * @property LIS同步数据
 */

namespace app\modules\api\controllers;

use app\common\Common;
use app\modules\inspect\models\InspectRecordUnion;
use app\modules\outpatient\models\InspectRecord;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use app\modules\report\models\Report;
use app\modules\spot\models\Inspect;
use app\modules\spot\models\InspectItem;
use app\modules\spot\models\InspectItemUnion;
use app\modules\spot\models\Spot;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use app\modules\behavior\models\JinyuRecord;
use Yii;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use app\modules\outpatient\models\Outpatient;
use Exception;
use SoapFault;
use app\modules\behavior\models\InspectItemExternalUnion;



class LisController extends Controller
{

    public $result;

    public function behaviors() {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'get-doctor-data' => ['post'],
//                    'get-second-department-data' => ['post'],
//                    'get-inspect-data' => ['post'],
                      'get-inspect-record-data' => ['post']
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * get-doctor-data
     * @param int $parentSpotId 机构id
     * @return int errorCode 错误码(0-正常,1001-参数错误,1002-机构代码不存在)
     * @return string parentSpotId 机构id
     * @return array data 机构医生数据
     * @desc 获取机构所有医生数据
     */
    public function actionGetDoctorData() {
        $parentSpotId = Yii::$app->request->post('parentSpotId');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->result['errorCode'] = 0;

        if (!$parentSpotId || !is_numeric($parentSpotId)) {//参数错误
            $this->result['errorCode'] = 1001;
            return $this->result;
        }

        $spotName = Spot::getSpotName($parentSpotId, 'type = 1');
        if (!$spotName) {//机构不存在
            $this->result['errorCode'] = 1002;
            return $this->result;
        }

        $this->result['parentSpotId'] = $parentSpotId;

        $query = new Query();
        $query->from(['a' => User::tableName()]);
        $query->select(['a.id', 'a.username', 'positionTitle' => 'a.position_title', 'a.iphone']);
        $query->where(['a.spot_id' => $parentSpotId, 'a.occupation' => 2, 'a.status' => 1]); //拉取职位为医生的数据
        $data = $query->all();
        foreach ($data as &$value) {
            $value['positionTitle'] = User::$getPositionTitle[$value['positionTitle']];
        }
        $this->result['data'] = $data;
        return $this->result;
    }

    /**
     * get-second-department-data
     * @param int $spotId 诊所id
     * @return int errorCode 错误码(0-正常,1001-参数错误,1002-诊所代码不存在)
     * @return string spotId 诊所id
     * @return array data 诊所二级科室数据
     * @desc 获取诊所所有二级科室数据
     */
    public function actionGetSecondDepartmentData() {
        $spotId = Yii::$app->request->post('spotId');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->result['errorCode'] = 0;

        if (!$spotId || !is_numeric($spotId)) {//参数错误
            $this->result['errorCode'] = 1001;
            return $this->result;
        }

        $spotName = Spot::getSpotName($spotId, 'type = 2');
        if (!$spotName) {//诊所不存在
            $this->result['errorCode'] = 1002;
            return $this->result;
        }

        $this->result['spotId'] = $spotId;

        $query = new Query();
        $query->from(['a' => SecondDepartment::tableName()]);
        $query->select(['a.id', 'a.name']);
        $query->where(['a.spot_id' => $spotId, 'a.status' => 1]); //拉取职位为医生的数据
        $this->result['data'] = $query->all();
        return $this->result;
    }

    /**
     * get-inspect-record-data
     * @param string $patientNumber 患者病历号
     * @param integer $parentSpotId 机构id
     * @param integer $spotId 诊所id
     * @param date $beginTime 创建医嘱开始时间
     * @param date $endTime 创建医嘱结束时间
     * @return integer errorCode 错误代码(0-正常,1001-参数不存在,1002-门诊号不存在，1003-检验项目已完成)
     * @return string msg 错误提示
     * @return integer spotId 诊所id
     * @return integer recordId 就诊流水id
     * @return array data 当前就诊记录所需检查的实验室项目数据
     * @desc LIS系统主动调用该接口获取当前诊所的就诊记录的所需检查的项目
     */
    public function actionGetInspectRecordData() {

        $patientNumber = Yii::$app->request->post('patientNumber');//病历号
        $parentSpotId = Yii::$app->request->post('parentSpotId');//机构id
        $spotId = Yii::$app->request->post('spotId');//诊所id
        
        $beginTime = Yii::$app->request->post('beginTime');//创建医嘱开始时间
        $endTime = Yii::$app->request->post('endTime');//创建医嘱结束时间
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::info('patientNumber:'.$patientNumber.'--parentSpotId:'.$parentSpotId.'--spotId:'.$spotId.'--beginTime:'.$beginTime.'--endTime:'.$endTime);
        
        $this->result['errorCode'] = 0;
        if(!$patientNumber && !$parentSpotId){
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $query = new Query();
        $query->from(['d' => Patient::tableName()]);
        $query->select(['a.id','a.spot_id','d.username','d.sex','d.birthday','d.patient_number', 'inspectRecordId' => 'b.id','b.inspect_id','b.name','b.status','b.price','b.create_time','itemId' =>'c.item_id' ,'itemRecordId' => 'c.id', 'itemRecordName' => 'c.name','c.unit','c.reference','e.doctor_id','doctorName' => 'f.username']);
        $query->leftJoin(['a' => PatientRecord::tableName()],'{{d}}.id = {{a}}.patient_id');
        $query->leftJoin(['e' => Report::tableName()],'{{a}}.id = {{e}}.record_id');
        $query->leftJoin(['f' => User::tableName()],'{{e}}.doctor_id = {{f}}.id');
        $query->leftJoin(['b' => InspectRecord::tableName()],'{{a}}.id = {{b}}.record_id');
        $query->leftJoin(['c' => InspectRecordUnion::tableName()],'{{b}}.id = {{c}}.inspect_record_id');
        $query->where(['d.patient_number' => trim($patientNumber),'d.spot_id' => trim($parentSpotId),'b.status' => [2,3]]);
        $query->andFilterWhere(['a.spot_id' => trim($spotId)]);
        $query->andWhere('b.inspect_id != :inspectId',[':inspectId' => 0]);
        if($beginTime){
            $query->andFilterCompare('b.create_time', strtotime($beginTime),'>=');
        }
        if($endTime){
            $query->andFilterCompare('b.create_time', strtotime($endTime) + 86400,'<=');
        }
        $result = $query->all();
//         $query->from(['a' => InspectRecord::tableName()]);
//         $query->select(['a.id','a.spot_id','a.record_id','a.price', 'itemId' => 'b.id', 'itemRecordName' => 'b.name','b.unit','b.reference']);
//         $query->leftJoin(['b' => InspectRecordUnion::tableName()],'{{a}}.id = {{b}}.inspect_record_id');
//         $query->where(['a.record_id' => $recordId,'a.status' => 2]);
//         $result = $query->all();
        if(empty($result)){
            $this->result['errorCode'] = 1002;
            $this->result['msg'] = '门诊记录不存在';
            return $this->result;
        }
        $rows = [];
        $updateRows = [];
        foreach ($result as $v){
            if($v['status'] == 3){
                $updateRows[] = $v['inspectRecordId'];
            }
            $rows[$v['inspectRecordId']]['recordId'] = $v['id'];
            $rows[$v['inspectRecordId']]['inspectId'] = $v['inspect_id'];
            $rows[$v['inspectRecordId']]['inspectRecordId'] = $v['inspectRecordId'];
            $rows[$v['inspectRecordId']]['inspectRecordName'] = $v['name'];
            $rows[$v['inspectRecordId']]['price'] = $v['price'];
            $rows[$v['inspectRecordId']]['createTime'] = date('Y-m-d H:i',$v['create_time']);
            $rows[$v['inspectRecordId']]['items'][] = [
                'itemId' => $v['itemId'],
                'itemRecordId' => $v['itemRecordId'],
                'itemRecordName' => $v['itemRecordName'],
                'unit' => $v['unit'],
                'reference' => $v['reference']
            ];
        }
        if(!empty($updateRows)){
            //更新状态为检验中
            InspectRecord::updateAll(['status' => 2,'inspect_in_time' => time()],['id' => $updateRows]);
        }
        $this->result['spotId'] = $result[0]['spot_id'];
        $this->result['patientNumber'] = $patientNumber;
        $this->result['patientName'] = $result[0]['username'];
        $this->result['sex'] = Patient::$getSex[$result[0]['sex']];
        $this->result['birthday'] = $result[0]['birthday'];
        $this->result['doctorName'] = $result[0]['doctorName'];
        $this->result['data'] = array_values($rows);

        return $this->result;
    }

    /**
     * @return integer errorCode 错误代码(0-成功,1001-参数错误,1002-实验室检验没找到,1003-检验项目还在检验中，请出结果后再返回)
     * @desc LIS系统检验项目报告全部出来后，请求HIS接口，返回检验结果
     */
    public function actionSaveInspectRecord() {

        $params = Yii::$app->request->post('data');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->result['errorCode'] = 0;
        Yii::info($params,'save-inspect-record');
        if (empty($params)) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $params = json_decode($params,true);
        Yii::info('params:'.$params);
        $spotId = $params['spotId'];
        $data = $params['data'];
        Yii::info('params:'.json_encode($params,true));
        Yii::info('spotId:'.$spotId);
        Yii::info('data:'.json_encode($data,true));
        if(!empty($data)){
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                foreach ($data as $v){
                    if(empty($v)){
                        continue;
                    }
                    
                    $where = ['id' => $v['inspectRecordId'],'spot_id' => $spotId,'record_id' => $v['recordId']];
                    
                    $count = InspectRecord::find()->where($where)->count();
                    if(!$count){
                        $this->result['errorCode'] = 1002;
                        $this->result['msg'] = '实验室检验项目没找到';
                        $dbTrans->rollBack();
                        return $this->result;
                    }
                    InspectRecord::updateAll(['status' => 1,'report_user_id' => $v['items'][0]['reportUserId'],'report_time' => time(),'inspect_finish_time' => time(),'update_time' => time()],['id' => $v['inspectRecordId'],'spot_id' => $spotId,'record_id' => $v['recordId']]);
                   
                    foreach ($v['items'] as $value){
                        if($value['status'] != 1 ){
                            $this->result['errorCode'] = 1003;
                            $this->result['msg'] = '检验项目还在检验中，请出结果后再返回';
                            $dbTrans->rollBack();
                            return $this->result;
                        }
                        InspectRecordUnion::updateAll(['result_identification' => $value['identificationResult'],'result' => $value['result'],'reference' => $value['referenceResult'],'unit' => $value['unitResult']],['item_id' => $value['itemId'],'spot_id' => $spotId,'record_id' => $v['recordId'],'inspect_record_id' => $v['inspectRecordId']]);
                    }
                    //设置 已出报告数量
                    Outpatient::setMadeReport($spotId, $v['recordId'], 1);
                }
               
                $dbTrans->commit();
                return $this->result;
            }
         catch (\yii\db\Exception $e){
             $dbTrans->rollBack();
             Yii::error(json_encode($e->errorInfo,true),'save-inspect-record-error');
             $this->result['errorCode'] = '1004';
             $this->result['msg'] = '系统异常';
             return $this->result;
         }
         
        }
        
    }

    /**
     * get-inspect-data
     * @param int $parentSpotId 机构id
     * @return int errorCode 错误码(0-正常,1001-参数错误,1002-机构代码不存在)
     * @return string parentSpotId 机构id
     * @return array data 检验项目数据
     * @desc 获取机构所有检验项目数据
     */
    public function actionGetInspectData() {
        $parentSpotId = Yii::$app->request->post('parentSpotId');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->result['errorCode'] = 0;

        if (!$parentSpotId || !is_numeric($parentSpotId)) {//参数错误
            $this->result['errorCode'] = 1001;
            return $this->result;
        }

        $spotName = Spot::getSpotName($parentSpotId, 'type = 1');
        if (!$spotName) {//机构不存在
            $this->result['errorCode'] = 1002;
            return $this->result;
        }

        $this->result['parentSpotId'] = $parentSpotId;

        $query = new Query();
        $query->from(['a' => Inspect::tableName()]);
        $query->select(['inspectId' => 'a.id', 'inspectName' => 'a.inspect_name', 'price' => 'a.inspect_price', 'itemId' => 'group_concat(c.id)', 'itemName' => 'group_concat(c.item_name)', 'unit' => 'group_concat(c.unit)', 'reference' => 'group_concat(c.reference)']);
        $query->leftJoin(['b' => InspectItemUnion::tableName()], '{{a}}.id = {{b}}.inspect_id');
        $query->leftJoin(['c' => InspectItem::tableName()], '{{b}}.item_id = {{c}}.id');
        $query->where(['a.spot_id' => $parentSpotId, 'a.status' => 1]);
        $query->groupBy('a.id');
        $data = $query->all();
        foreach ($data as &$value) {
            $value['items'] = [];
            if ($value['itemId']) {
                $tmpId = explode(',', $value['itemId']);
                $tmpName = explode(',', $value['itemName']);
                $tmpUnit = explode(',', $value['unit']);
                $tmpReference = explode(',', $value['reference']);
                foreach ($tmpId as $key => $v) {
                    $value['items'][$key]['itemId'] = $tmpId[$key];
                    $value['items'][$key]['itemName'] = $tmpName[$key];
                    $value['items'][$key]['unit'] = $tmpUnit[$key];
                    $value['items'][$key]['reference'] = $tmpReference[$key];
                }
            }
            unset($value['itemId']);
            unset($value['itemName']);
            unset($value['unit']);
            unset($value['reference']);
        }
        $this->result['data'] = $data;
        return $this->result;
    }
    
    public function actionTestGetLisRequest() {
        $wsdl = 'https://dev.his.easyhin.com/api/webservice-lis/request.html';
        $client = new \SoapClient($wsdl,array('trace' => true));
        try{
            $result = $client->daGetLisRequest('338309536370');
            print_r($result);
        }  catch (SoapFault $e){
            var_dump($e);
            print_r($client->__getLastResponse());
        }        
    }
    
    public function actionTestAffirmRequest() {
        $wsdl = 'https://dev.his.easyhin.com/api/webservice-lis/request.html';
        $client = new \SoapClient($wsdl,array('trace' => true));
        try{
            $result = $client->affirmRequest('338309536370');
            print_r($result);
        }  catch (SoapFault $e){
            var_dump($e);
            print_r($client->__getLastResponse());
        }        
    }
    
    public function actionTestUploadLisRepdata() {
        $data = '<?xml version="1.0" encoding="utf-8"?>
<Report_Result>
  <Report_Info>
    <ext_lab_code>kingmed</ext_lab_code>
    <lis_Barcode>327531783289</lis_Barcode>
    <ext_Barcode>0128591041</ext_Barcode>
    <ext_checkItem/>
    <pat_name>顾静欣</pat_name>
    <pat_age/>
    <pat_height/>
    <pat_wight/>
    <pat_pre_week/>
    <pat_id/>
    <pat_bedNo/>
    <pat_tel/>
    <pat_sex>女</pat_sex>
    <pat_birthday/>
    <pat_ori_name>OP</pat_ori_name>
    <sam_name>微量元素专用管</sam_name>
    <sam_state/>
    <doctor_name>好医生</doctor_name>
    <dept_name/>
    <clinical_diag/>
    <SampleNumber/>
    <blood_time>2017-05-06 08:19:00</blood_time>
    <ext_check_ID/>
    <ext_receive_time>2017-05-06 22:11:24</ext_receive_time>
    <ext_check_time>2017-05-07 14:08:14</ext_check_time>
    <ext_first_audit_time>2017-05-07 14:08:59</ext_first_audit_time>
    <ext_second_audit_time>2017-05-07 15:11:59</ext_second_audit_time>
    <ext_upload_time/>
    <ext_report_suggestion/>
    <ext_report_remark/>
    <ext_checker>林国清</ext_checker>
    <ext_first_audit>林国清</ext_first_audit>
    <ext_second_audit>高永梅</ext_second_audit>
    <ext_intstrmt_name/>
    <ext_lab_name/>
    <ext_report_type/>
    <ext_report_code>GZ0005107928163</ext_report_code>
    <result_info>
      <result_seq>1</result_seq>
      <ext_combine_code>ZH00219</ext_combine_code>
      <ext_combine_name>全血元素七项（Pb,Mn,Ca,Cu,Fe,Zn,Mg）</ext_combine_name>
      <ext_item_code>30033</ext_item_code>
      <ext_item_name>铁(Fe),全血,ICP-MS</ext_item_name>
      <result>111</result>
      <result_unit>mg/L</result_unit>
      <result_flag/>
      <result_reference>测试血常规1</result_reference>
      <result_date>2017-05-07 15:11:59</result_date>
      <result_intstrmt_name>Agilent电感耦合等离子体质谱仪</result_intstrmt_name>
      <result_test_method>ICP-MS</result_test_method>
      <result_suggestion/>
      <result_remark/>
      <lis_combine_code>1286</lis_combine_code>
      <lis_combine_name>全血元素七项（Pb,Mn,Ca,Cu,Fe,Zn,Mg）</lis_combine_name>
      <lis_item_code>1114</lis_item_code>
      <lis_item_name>全血微量元素铁（Fe）</lis_item_name>
      <isreimbu/>
      <reimbudesc/>
      <isdelayed/>
      <delayeddesc/>
    </result_info>
    <result_info>
      <result_seq>2</result_seq>
      <ext_combine_code>ZH00219</ext_combine_code>
      <ext_combine_name>全血元素七项（Pb,Mn,Ca,Cu,Fe,Zn,Mg）</ext_combine_name>
      <ext_item_code>30034</ext_item_code>
      <ext_item_name>钙(Ca),全血,ICP-MS</ext_item_name>
      <result>222</result>
      <result_unit>mg/L</result_unit>
      <result_flag/>
      <result_reference>测试血常规1</result_reference>
      <result_date>2017-05-07 15:12:00</result_date>
      <result_intstrmt_name>Agilent电感耦合等离子体质谱仪</result_intstrmt_name>
      <result_test_method>ICP-MS</result_test_method>
      <result_suggestion/>
      <result_remark/>
      <lis_combine_code>1286</lis_combine_code>
      <lis_combine_name>全血元素七项（Pb,Mn,Ca,Cu,Fe,Zn,Mg）</lis_combine_name>
      <lis_item_code>1115</lis_item_code>
      <lis_item_name>全血微量元素钙（Ca）</lis_item_name>
      <isreimbu/>
      <reimbudesc/>
      <isdelayed/>
      <delayeddesc/>
    </result_info>
    <result_info>
      <result_seq>3</result_seq>
      <ext_combine_code>ZH00219</ext_combine_code>
      <ext_combine_name>全血元素七项（Pb,Mn,Ca,Cu,Fe,Zn,Mg）</ext_combine_name>
      <ext_item_code>30034</ext_item_code>
      <ext_item_name>钙(Ca),全血,ICP-MS</ext_item_name>
      <result>333</result>
      <result_unit>mg/L</result_unit>
      <result_flag/>
      <result_reference>测试血常规1</result_reference>
      <result_date>2017-05-07 15:12:00</result_date>
      <result_intstrmt_name>Agilent电感耦合等离子体质谱仪</result_intstrmt_name>
      <result_test_method>ICP-MS</result_test_method>
      <result_suggestion/>
      <result_remark/>
      <lis_combine_code>1286</lis_combine_code>
      <lis_combine_name>全血元素七项（Pb,Mn,Ca,Cu,Fe,Zn,Mg）</lis_combine_name>
      <lis_item_code>256</lis_item_code>
      <lis_item_name>全血微量元素钙（Ca）</lis_item_name>
      <isreimbu/>
      <reimbudesc/>
      <isdelayed/>
      <delayeddesc/>
    </result_info>
    <result_info>
      <result_seq>4</result_seq>
      <ext_combine_code>ZH00219</ext_combine_code>
      <ext_combine_name>全血元素七项（Pb,Mn,Ca,Cu,Fe,Zn,Mg）</ext_combine_name>
      <ext_item_code>30034</ext_item_code>
      <ext_item_name>钙(Ca),全血,ICP-MS</ext_item_name>
      <result>111</result>
      <result_unit>mg/L</result_unit>
      <result_flag/>
      <result_reference>测试血常规2</result_reference>
      <result_date>2017-05-07 15:12:00</result_date>
      <result_intstrmt_name>Agilent电感耦合等离子体质谱仪</result_intstrmt_name>
      <result_test_method>ICP-MS</result_test_method>
      <result_suggestion/>
      <result_remark/>
      <lis_combine_code>1287</lis_combine_code>
      <lis_combine_name>全血元素七项（Pb,Mn,Ca,Cu,Fe,Zn,Mg）</lis_combine_name>
      <lis_item_code>256</lis_item_code>
      <lis_item_name>全血微量元素钙（Ca）</lis_item_name>
      <isreimbu/>
      <reimbudesc/>
      <isdelayed/>
      <delayeddesc/>
    </result_info>
    <result_info>
      <result_seq>5</result_seq>
      <ext_combine_code>ZH00219</ext_combine_code>
      <ext_combine_name>全血元素七项（Pb,Mn,Ca,Cu,Fe,Zn,Mg）</ext_combine_name>
      <ext_item_code>30034</ext_item_code>
      <ext_item_name>钙(Ca),全血,ICP-MS</ext_item_name>
      <result>222</result>
      <result_unit>mg/L</result_unit>
      <result_flag/>
      <result_reference>测试血常规2</result_reference>
      <result_date>2017-05-07 15:12:00</result_date>
      <result_intstrmt_name>Agilent电感耦合等离子体质谱仪</result_intstrmt_name>
      <result_test_method>ICP-MS</result_test_method>
      <result_suggestion/>
      <result_remark/>
      <lis_combine_code>1287</lis_combine_code>
      <lis_combine_name>全血元素七项（Pb,Mn,Ca,Cu,Fe,Zn,Mg）</lis_combine_name>
      <lis_item_code>258</lis_item_code>
      <lis_item_name>全血微量元素钙（Ca）</lis_item_name>
      <isreimbu/>
      <reimbudesc/>
      <isdelayed/>
      <delayeddesc/>
    </result_info>
    <report_pic>
    </report_pic>
  </Report_Info>
</Report_Result>';
        $wsdl = 'https://dev.his.easyhin.com/api/webservice-lis/request.html';
        $client = new \SoapClient($wsdl,array('trace' => true));
        try{
            $result = $client->uploadLisRepData($data);
            print_r($result);
        }  catch (SoapFault $e){
            var_dump($e);
            print_r($client->__getLastResponse());
        }        
    }

    public function actionGetLisRequest() {
        $lisBarcode = Yii::$app->request->post('lisBarcode');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->result['code'] = 0;
        if (!$lisBarcode) {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $query = new Query();
        $query->from(['d' => Patient::tableName()]);
        $query->select(['a.id', 'a.spot_id', 'd.username', 'd.sex', 'd.birthday', 'd.patient_number', 'inspectRecordId' => 'b.id', 'b.inspect_id', 'b.name', 'b.status', 'b.price', 'b.create_time', 'itemId' => 'c.item_id', 'itemRecordId' => 'c.id', 'itemRecordName' => 'c.name', 'c.unit', 'c.reference', 'e.doctor_id', 'doctorName' => 'f.username']);
        $query->leftJoin(['a' => PatientRecord::tableName()], '{{d}}.id = {{a}}.patient_id');
        $query->leftJoin(['e' => Report::tableName()], '{{a}}.id = {{e}}.record_id');
        $query->leftJoin(['f' => User::tableName()], '{{e}}.doctor_id = {{f}}.id');
        $query->leftJoin(['b' => InspectRecord::tableName()], '{{a}}.id = {{b}}.record_id');
        $query->leftJoin(['c' => InspectRecordUnion::tableName()], '{{b}}.id = {{c}}.inspect_record_id');
        $query->where(['b.specimen_number' => $lisBarcode]);
        $result = $query->all();
        if (empty($result)) {
            $this->result['errorCode'] = 1002;
            $this->result['msg'] = '门诊记录不存在';
            return $this->result;
        }
        $rows = [];
        foreach ($result as $v) {
            $rows[$v['inspectRecordId']]['recordId'] = $v['id'];
            $rows[$v['inspectRecordId']]['inspectId'] = $v['inspect_id'];
            $rows[$v['inspectRecordId']]['inspectRecordId'] = $v['inspectRecordId'];
            $rows[$v['inspectRecordId']]['inspectRecordName'] = $v['name'];
            $rows[$v['inspectRecordId']]['price'] = $v['price'];
            $rows[$v['inspectRecordId']]['createTime'] = date('Y-m-d H:i', $v['create_time']);
            $rows[$v['inspectRecordId']]['items'][] = [
                'itemId' => $v['itemId'],
                'itemRecordId' => $v['itemRecordId'],
                'itemRecordName' => $v['itemRecordName'],
                'unit' => $v['unit'],
                'reference' => $v['reference']
            ];
        }
        $this->result['spotId'] = $result[0]['spot_id'];
        $this->result['patientName'] = $result[0]['username'];
        $this->result['sex'] = Patient::$getSex[$result[0]['sex']];
        $this->result['birthday'] = $result[0]['birthday'];
        $this->result['doctorName'] = $result[0]['doctorName'];
        $this->result['data'] = array_values($rows);
        return $this->result;
    }
    
    /*
     * 获取迪安报告结果，需要定时去获取，只能根据采样时间获取
     */
    public function actionTestGetDaResult() {
        $config = Yii::$app->params['daParamsConfig'];
        $clientId = $config['clientId'];
        $clientGUID = $config['clientGUID'];
        
        $wsdl = 'http://report.dalabs.cn/RasClientDetail.asmx?wsdl';
        $client = new \SoapClient($wsdl,array('trace' => true));
        try{
            $result = $client->GetDetailData(['ClientID' => $clientId, 'ClientGUID' => $clientGUID, 'StartDate' => '2017-06-13', 'EndDate' => '2017-10-13']);
            print_r($result->GetDetailDataResult);
            
//            $result = $client->GetDetailByCode(['ClientId' => '上海义信儿科门诊部有限公司','ClientGUID' => '57DBCA857D444ECAE0530BF0000A29FD','BarCode' => '088398163','type' => 0]);
//            print_r($result->GetDetailByCodeResult);
        }  catch (SoapFault $e){
            var_dump($e);
        }        
    }
    /*
     * 获取迪安报告结果，需要定时去获取，只能根据采样时间获取
     */
    public function actionGetDaResult() {
        $config = Yii::$app->params['daParamsConfig'];
        $spotId = $config['spotId'];
        $clientId = $config['clientId'];
        $clientGUID = $config['clientGUID'];
        
        //获取检验中医嘱的采样时间
        $query = new Query();
        $inspectRecordList = $query->from(['a' => InspectRecord::tableName()])
                ->select(['a.id', 'a.inspect_in_time', 'a.specimen_number'])
                ->where(['a.spot_id' => $spotId, 'a.status' => 2, 'deliver' => 1])//外送项目，执行中，上海诊所
                ->all();       
        if(empty($inspectRecordList)){
            Yii::info('没有执行中的检验医嘱', 'actionGetDaResult');
        }
        Yii::info("GetDetailData inspect_record list".  json_encode($inspectRecordList, true), 'actionGetDaResult');
        
        
        $query = new Query();
        $inspectItemExternalUnion = $query->from(['a' => InspectItemExternalUnion::tableName()])
                ->select(['a.inspect_item_id', 'a.external_id'])
                ->where(['a.company_id' => 0])//获取映射关系表
                ->indexBy('external_id')
                ->all(Yii::$app->get('recordDb'));       
        $wsdl = 'http://report.dalabs.cn/RasClientDetail.asmx?wsdl';
        $client = new \SoapClient($wsdl,array('trace' => true));
        try{
            foreach ($inspectRecordList as $value) {
                $startDate = date('Y-m-d',$value['inspect_in_time']);
                $endDate = date('Y-m-d',$value['inspect_in_time'] + 86400);
                
                Yii::info("GetDetailData Params ClientID[$clientId] ClientGUID[$clientGUID] StartDate[$startDate] EndDate[$endDate] --- inspectRecordId[{$value['id']}]", 'actionGetDaResult');
                $result = $client->GetDetailData(['ClientID' => $clientId, 'ClientGUID' => $clientGUID, 'StartDate' => $startDate, 'EndDate' => $endDate]);//根据采样时间获取检验项目
                Yii::info("GetDetailData Result {$result->GetDetailDataResult} --- inspectRecordId[{$value['id']}]", 'actionGetDaResult');
                $reportData = $this->xml2array('<Document>'.$result->GetDetailDataResult.'</Document>');//添加根标签用于正常解析
                $uploadResult = [];
                if(!empty($reportData['ResultsDataSet']) && !empty($reportData['ResultsDataSet']['Table'])){
                    foreach ($reportData['ResultsDataSet']['Table'] as $v) {
                        if($v['CLINICID'] == $value['specimen_number']){//如果检验项目中条码匹配上，则获取该项
                            $uploadResult[] = $v;
                        }
                    }
                } else {
                    Yii::info("GetDetailData Result ResultsDataSet empty or table empty --- inspectRecordId[{$value['id']}]", 'actionGetDaResult');

                    continue;
                }
                if ($uploadResult) {//结果已出
                    $dbTrans = Yii::$app->db->beginTransaction();
                    try {//try catch 避免影响其他数据
                        $query = new Query();
                        $inspectItemRecordList = $query->from(['a' => InspectRecordUnion::tableName()])
                                ->select(['a.item_id'])
                                ->where(['a.inspect_record_id' => $value['id']])
                                ->indexBy('item_id')
                                ->all();

                        foreach ($uploadResult as $v) {
                            $key = $v['TESTCODE'] . '-' . $v['ANALYTE_ORIGREC'];

                            if (!isset($inspectItemExternalUnion[$key])) {//关联关系不存在
                                Yii::info("GetDetailData union is non-existent da-id[$key]--- inspectRecordId[{$value['id']}]", 'actionGetDaResult');
                                continue;
                            }
                            if (!array_key_exists($inspectItemExternalUnion[$key]['inspect_item_id'], $inspectItemRecordList)) {//本地项目id不存在
                                Yii::info("GetDetailData item_id is non-existent item-id[{$inspectItemExternalUnion[$key]['inspect_item_id']}]--- inspectRecordId[{$value['id']}]", 'actionGetDaResult');
                                continue;
                            }
                            
                            $count = InspectRecordUnion::updateAll(['unit' => $v['UNITS'], 'result' => $v['FINAL'], 'reference' => $v['DISPLOWHIGH'], 'result_identification' => $v['RN10'], 'update_time' => time()], ['inspect_record_id' => $value['id'], 'item_id' => $inspectItemExternalUnion[$key]['inspect_item_id']]); //存在一个条码两个大项，且大项里面有相同的小项，因为使用了inspect_record_id  所以只会更新该大项的数据
                            echo "update inspect_record_union set unit = {$v['UNITS']}, result = {$v['FINAL']}, reference = {$v['DISPLOWHIGH']}, result_identification = {$v['RN10']} where inspect_record_id = {$value['id']},item_id = {$inspectItemExternalUnion[$key]['inspect_item_id']} </br>";
                            if ($count) {//更新成功
                                InspectRecord::updateAll(['status' => 1, 'inspect_finish_time' => time(), 'update_time' => time(), 'report_time' => time()], ['id' => $value['id']]); //更新医嘱状态
                                echo "update inspect_record where id = {$value['id']} </br>";
                            }
                        }
                        $dbTrans->commit();
                    } catch (Exception $e) {
                        $dbTrans->rollBack();
                        Yii::error('save result error' . $e->getMessage(), 'actionGetDaResult');
                    }
                }
            }
//            $result = $client->GetDetailData(['ClientID' => $clientId, 'ClientGUID' => $clientGUID, 'StartDate' => '2017-06-13', 'EndDate' => '2017-10-13']);
//            $reportData = $this->xml2array('<Document>'.$result->GetDetailDataResult.'</Document>');//添加根标签用于正常解析
            
//            $result = $client->GetDetailByCode(['ClientId' => '上海义信儿科门诊部有限公司','ClientGUID' => '57DBCA857D444ECAE0530BF0000A29FD','BarCode' => '088398163','type' => 0]);
//            print_r($result->GetDetailByCodeResult);
        }  catch (SoapFault $e){
            Yii::error($e->getMessage(), 'actionGetDaResult');
        }        
    }
    
    /**
     * XML转换数组 
     * @param $xml 
     * @return array 
     */
    private function xml2array($xml) {
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return json_decode(json_encode($xmlstring), true);
    }

}
