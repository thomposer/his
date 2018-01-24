<?php

namespace app\commands;

use app\modules\inspect\models\InspectRecordUnion;
use app\modules\outpatient\models\InspectRecord;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use Exception;
use SoapFault;
use app\modules\behavior\models\InspectItemExternalUnion;
use yii\log\FileTarget;

/**
 * @author abelhe
 */
class LisController extends Controller {
    
    public function init() {
        $this->enableCsrfValidation = false;
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
        if (empty($inspectRecordList)) {
            $this->log('没有执行中的检验医嘱');
        }
        $this->log("GetDetailData inspect_record list" . json_encode($inspectRecordList, true));


        $query = new Query();
        $inspectItemExternalUnion = $query->from(['a' => InspectItemExternalUnion::tableName()])
                ->select(['a.inspect_item_id', 'a.external_id'])
                ->where(['a.company_id' => 0])//获取映射关系表
                ->indexBy('external_id')
                ->all(Yii::$app->get('recordDb'));
        $wsdl = 'http://report.dalabs.cn/RasClientDetail.asmx?wsdl';
        $client = new \SoapClient($wsdl, array('trace' => true));
        try {
            foreach ($inspectRecordList as $value) {
                $startDate = date('Y-m-d', $value['inspect_in_time']);
                $endDate = date('Y-m-d', $value['inspect_in_time'] + 86400);

                $this->log("GetDetailData Params ClientID[$clientId] ClientGUID[$clientGUID] StartDate[$startDate] EndDate[$endDate] --- inspectRecordId[{$value['id']}]");
                $result = $client->GetDetailData(['ClientID' => $clientId, 'ClientGUID' => $clientGUID, 'StartDate' => $startDate, 'EndDate' => $endDate]); //根据采样时间获取检验项目
                $this->log("GetDetailData Result {$result->GetDetailDataResult} --- inspectRecordId[{$value['id']}]");
                $reportData = $this->xml2array('<Document>' . $result->GetDetailDataResult . '</Document>'); //添加根标签用于正常解析
                $uploadResult = [];
                if (!empty($reportData['ResultsDataSet']) && !empty($reportData['ResultsDataSet']['Table'])) {
                    foreach ($reportData['ResultsDataSet']['Table'] as $v) {
                        if ($v['CLINICID'] == $value['specimen_number']) {//如果检验项目中条码匹配上，则获取该项
                            $uploadResult[] = $v;
                        }
                    }
                } else {
                    $this->log("GetDetailData Result ResultsDataSet empty or table empty --- inspectRecordId[{$value['id']}]");
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
                                $this->log("GetDetailData union is non-existent da-id[$key]--- inspectRecordId[{$value['id']}]");
                                continue;
                            }
                            if (!array_key_exists($inspectItemExternalUnion[$key]['inspect_item_id'], $inspectItemRecordList)) {//本地项目id不存在
                                $this->log("GetDetailData item_id is non-existent item-id[{$inspectItemExternalUnion[$key]['inspect_item_id']}]--- inspectRecordId[{$value['id']}]");
                                continue;
                            }

                            $count = InspectRecordUnion::updateAll(['unit' => $v['UNITS'], 'result' => $v['FINAL'], 'reference' => $v['DISPLOWHIGH'], 'result_identification' => $v['RN10'], 'update_time' => time()], ['inspect_record_id' => $value['id'], 'item_id' => $inspectItemExternalUnion[$key]['inspect_item_id']]); //存在一个条码两个大项，且大项里面有相同的小项，因为使用了inspect_record_id  所以只会更新该大项的数据
                            echo "update inspect_record_union set unit = {$v['UNITS']}, result = {$v['FINAL']}, reference = {$v['DISPLOWHIGH']}, result_identification = {$v['RN10']} where inspect_record_id = {$value['id']},item_id = {$inspectItemExternalUnion[$key]['inspect_item_id']} </br>";
                            $this->log("update inspect_record_union set unit = {$v['UNITS']}, result = {$v['FINAL']}, reference = {$v['DISPLOWHIGH']}, result_identification = {$v['RN10']} where inspect_record_id = {$value['id']},item_id = {$inspectItemExternalUnion[$key]['inspect_item_id']}");
                            if ($count) {//更新成功
                                InspectRecord::updateAll(['status' => 1, 'inspect_finish_time' => time(), 'update_time' => time(), 'report_time' => time()], ['id' => $value['id']]); //更新医嘱状态
                                $this->log("update inspect_record where id = {$value['id']}");
                                echo "update inspect_record where id = {$value['id']} </br>";
                            }
                        }
                        $dbTrans->commit();
                    } catch (Exception $e) {
                        $dbTrans->rollBack();
                        $this->log('save result error' . json_encode($e->getMessage()));
                    }
                }
            }
        } catch (SoapFault $e) {
            $this->log('save result error' . json_encode($e->getMessage()));
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
    
    
    public function log($info) {
        $time = microtime(true);
        $log = new FileTarget();
        $log->logFile = Yii::$app->getRuntimePath() . '/logs/' . date('Y-m-d') . '_commands.log';
        $log->messages[] = [$info, 1, 'actionGetDaResult', $time];
        $log->export();
    }

}
