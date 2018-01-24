<?php
namespace app\modules\api\controllers;
use app\common\Common;
use app\modules\inspect\models\InspectRecordUnion;
use app\modules\outpatient\models\InspectRecord;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use app\modules\spot\models\Inspect;
use app\modules\report\models\Report;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use app\modules\behavior\models\JinyuRecord;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use app\modules\spot\models\Spot;
use yii\web\Response;

class WebserviceLisController extends Controller
{
    
    public $result;
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'request' => 'mongosoft\soapserver\Action',
        ];
    }

    /**
     * @param string $lisBarcode
     * @return string
     * @soap
     */
    public function getLisRequest($lisBarcode)
    {
        $data['Lis_Barcode'] = $lisBarcode;
        $this->result['code'] = 0;
        if (!$lisBarcode) {
            $this->result['code'] = 1001;
            $this->result['message'] = '参数错误';
            return Common::xmlEncode($this->result, false, 'response');
        }
        $query = new Query();
        $query->from(['f' => InspectRecord::tableName()]);
        $query->select(['a.id', 'a.patient_id', 'b.username', 'b.sex', 'b.birthday', 'patient_iphone' => 'b.iphone',
            'dept_name' => 'e.name', 'doctor_name' => 'd.username', 'doctor_iphone' => 'd.iphone', 'f.inspect_in_time','spot_id' => 'g.id', 'g.spot_name']);
        $query->leftJoin(['a' => PatientRecord::tableName()], '{{f}}.record_id = {{a}}.id');
        $query->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id');
        $query->leftJoin(['c' => Report::tableName()], '{{a}}.id = {{c}}.record_id');
        $query->leftJoin(['d' => User::tableName()], '{{c}}.doctor_id = {{d}}.id');
        $query->leftJoin(['e' => SecondDepartment::tableName()], '{{c}}.second_department_id = {{e}}.id');
        $query->leftJoin(['g' => Spot::tableName()],'{{g}}.id = {{f}}.spot_id');
        $query->where(['f.specimen_number' => $lisBarcode]);
        $basicResult = $query->one();
        $data['hospitalcode'] = $basicResult['spot_id'];//诊所id
        $data['hospitalname'] = $basicResult['spot_name'];//诊所名字
        $data['pat_id'] = $basicResult['patient_id']; //病人ID
        $data['pat_name'] = $basicResult['username']; //病人姓名
        $data['pat_bedno'] = ''; //床号TODO
        $data['blood_time'] = date('Y-m-d H:i:s', $basicResult['inspect_in_time']); //采样时间TODO
        $data['pat_sex'] = Patient::$getSex[$basicResult['sex']]; //性别
        $data['pat_birthday'] = date('Y-m-d',$basicResult['birthday']); //出生日期
        $data['pat_age'] = date('Y') - date('Y',$basicResult['birthday']) + (date('m-d') > date('m-d',$basicResult['birthday']) ? 1 : 0); //年龄
        $data['pat_ageunit'] = '岁'; //年龄单位
        $data['pat_tel'] = $basicResult['patient_iphone']; //病人电话
        $data['dept_name'] = $basicResult['dept_name']; //送检科室
        $data['doctor_name'] = $basicResult['doctor_name']; //送检医生
        $data['doctor_tel'] = $basicResult['doctor_iphone']; //医生电话
        $data['clinical_diag'] = ''; //医嘱
        $data['samp_name'] = ''; //标本类型
        $data['pat_from'] = '外送'; //标本来源
        $query = new Query();
        $query->from(['a' => InspectRecord::tableName()]);
        $query->select(['a.id','inspectId' => 'a.inspect_id', 'a.name', 'a.spot_id', 'a.record_id', 'a.price', 'itemId' => 'b.item_id', 'itemRecordName' => 'b.name', 'b.unit', 'b.reference']);
        $query->leftJoin(['b' => InspectRecordUnion::tableName()], '{{a}}.id = {{b}}.inspect_record_id');
        $query->where(['a.specimen_number' => $lisBarcode]);
        $result = $query->all();
        if (empty($result)) {
            $this->result['code'] = 1002;
            $this->result['message'] = '门诊记录不存在';
            return Common::xmlEncode($this->result, false, 'response');
        }
        $rows = [];
        foreach ($result as $v) {
            $rows[$v['id']]['lis_item_code'] = $v['inspectId'];
            $rows[$v['id']]['lis_item_name'] = $v['name'];
            $rows[$v['id']]['SubItems'][] = [
                'lis_subitem_code' => $v['itemId'],
                'lis_subitem_name' => $v['itemRecordName'],
            ];
        }
        $data['LisItems'] = $rows;
        $this->result = $data;
        return Common::xmlEncode($this->result);
    }
    
    
     /**
     * @param string $lisBarcode
     * @return string
     * @soap
     */
    public function affirmRequest($lisBarcode)
    {
        $request = Yii::$app->request;
        JinyuRecord::log($request->userIP, 0, JinyuRecord::affirmRequest, $lisBarcode);
        $this->result['code'] = 0;
        $this->result['message'] = '操作成功';
        return Common::xmlEncode($this->result, false, 'response');
    }
    
    
    /**
     * @param string $resultXML
     * @return string
     * @soap
     */
    public function uploadLisRepData($resultXML)
    {
        $this->result['code'] = 0;
        $this->result['message'] = '保存成功';

        $xml = $resultXML;
        
        $request = Yii::$app->request;
        JinyuRecord::log($request->userIP, 0, JinyuRecord::uploadLisRepData,$xml);//保存信息
        $data = $this->xml2array($xml);
        $lisBarcode = $data['Report_Info']['lis_Barcode'];
        $inspectRecordList = InspectRecord::find()->select(['id','inspect_id'])->where(['specimen_number' => $lisBarcode])->indexBy('inspect_id')->asArray()->all();
        if(!$inspectRecordList){
            $this->result['code'] = 1;
            $this->result['message'] = '实验室检验项目没找到';
            return Common::xmlEncode($this->result, false, 'response');
        }
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            foreach ($data['Report_Info']['result_info'] as $value) {
                if(isset($inspectRecordList[$value['lis_combine_code']])){
                    $inspectRecordId =  $inspectRecordList[$value['lis_combine_code']]['id'];
                    //更新检验状态（根据条形码和实验室项目配置id确定唯一记录）
                    InspectRecord::updateAll(['status' => 1, 'report_time' => time(), 'inspect_finish_time' => time(), 'update_time' => time()], ['id' => $inspectRecordId]);
                    //更新检验项目(根据检验项目配置id和实验室记录id确定唯一记录)
                    InspectRecordUnion::updateAll(['result' => $value['result'], 'reference' => $value['result_reference'], 'unit' => $value['result_unit']], ['item_id' => $value['lis_item_code'],'inspect_record_id' => $inspectRecordId]);
                }
            }
            $dbTrans->commit();
        } catch (\yii\db\Exception $e) {
            $dbTrans->rollBack();
            $this->result['code'] = 1;
            $this->result['message'] = '保存失败';
        }
        return Common::xmlEncode($this->result, false, 'response');
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
    
    /**
     * @param string $lisBarcode
     * @return string
     * @soap
     */
    public function daGetLisRequest($lisBarcode) {
        Yii::info("WebserviceLisController daGetLisRequest params lisBarcode[$lisBarcode]");
        $this->result['code'] = 0;
        if (!$lisBarcode) {
            Yii::info("WebserviceLisController daGetLisRequest params empty");
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return json_encode($this->result,true);
        }
        $query = new Query();
        $query->from(['d' => Patient::tableName()]);
        $query->select(['a.id', 'a.spot_id', 'd.username', 'd.sex', 'd.birthday', 'd.patient_number', 'inspectRecordId' => 'b.id', 'b.inspect_id', 'b.name', 'b.status', 'b.price', 'b.create_time', 'b.inspect_in_time', 'b.specimen_type', 'itemId' => 'c.item_id', 'itemRecordId' => 'c.id', 'itemRecordName' => 'c.name', 'c.unit', 'c.reference', 'e.doctor_id', 'doctorName' => 'f.username']);
        $query->leftJoin(['a' => PatientRecord::tableName()], '{{d}}.id = {{a}}.patient_id');
        $query->leftJoin(['e' => Report::tableName()], '{{a}}.id = {{e}}.record_id');
        $query->leftJoin(['f' => User::tableName()], '{{e}}.doctor_id = {{f}}.id');
        $query->leftJoin(['b' => InspectRecord::tableName()], '{{a}}.id = {{b}}.record_id');
        $query->leftJoin(['c' => InspectRecordUnion::tableName()], '{{b}}.id = {{c}}.inspect_record_id');
        $query->where(['b.specimen_number' => $lisBarcode]);
        $result = $query->all();
        if (empty($result)) {
            Yii::info("WebserviceLisController daGetLisRequest result empty");
            $this->result['errorCode'] = 1002;
            $this->result['msg'] = '门诊记录不存在';
            return json_encode($this->result,true);
        }
        $rows = [];
        foreach ($result as $v) {
            $rows[$v['inspectRecordId']]['recordId'] = $v['id'];
            $rows[$v['inspectRecordId']]['inspectId'] = $v['inspect_id'];
            $rows[$v['inspectRecordId']]['inspectRecordId'] = $v['inspectRecordId'];
            $rows[$v['inspectRecordId']]['inspectRecordName'] = $v['name'];
//            $rows[$v['inspectRecordId']]['price'] = $v['price'];
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
        $this->result['samplingTime'] = $result[0]['inspect_in_time'];
        $this->result['specimenType'] = Inspect::$getSpecimenType[$result[0]['specimen_type']];
        $this->result['data'] = array_values($rows);
        Yii::info("WebserviceLisController daGetLisRequest data " . json_encode($this->result,true));
        return json_encode($this->result,true);
    }
}