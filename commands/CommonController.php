<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\modules\make_appointment\models\Appointment;
use app\modules\outpatient\models\DentalHistory;
use app\modules\outpatient\models\OutpatientRelation;
use app\modules\outpatient\models\RecipeTypeTemplate;
use app\modules\patient\models\PatientRecord;
use app\modules\outpatient\models\ChildExaminationAssessment;
use app\modules\spot\models\MedicalFee;
use app\modules\spot\models\RecipeList;
use app\modules\spot\models\Spot;
use app\modules\spot_set\models\OutpatientPackageTemplate;
use app\modules\spot_set\models\ThirdPlatform;
use yii;
use yii\console\Controller;
use yii\db\Query;
use app\modules\triage\models\TriageInfo;
use app\modules\patient\models\Patient;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\log\FileTarget;
use app\modules\report\models\Report;
use app\modules\make_appointment\models\AppointmentConfig;
use app\modules\make_appointment\models\AppointmentTimeAndServer;
use app\modules\spot_set\models\SpotType;
use app\modules\spot_set\models\UserAppointmentConfig;
use app\modules\user\models\User;
use PHPExcel_Cell;
use yii\db\Command;
use app\modules\spot\models\CheckCode;
use app\modules\outpatient\models\FirstCheck;
use app\modules\charge\models\ChargeRecordLog;
use app\modules\spot\models\CardManage;
use app\modules\card\models\ServiceConfig;
use app\specialModules\recharge\models\CardSpotConfig;
use app\modules\outpatient\models\AllergyOutpatient;
use app\modules\patient\models\PatientAllergy;
use app\modules\outpatient\models\Outpatient;
use app\modules\charge\models\ChargeRecord;
use app\modules\charge\models\FirstOrderFree;
use app\modules\follow\models\Follow;
use app\specialModules\recharge\models\MembershipPackageCardFlow;
use app\specialModules\recharge\models\MembershipPackageCardUnion;
use app\modules\message\models\MessageCenter;
use app\modules\patient\models\PatientFamily;
use app\modules\patient\models\PatientSubmeter;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CommonController extends Controller
{

    /**
     * 获取旧的过敏史数据并将旧的过敏史数据转化为食物过敏和药物过敏
     */
    public function actionIndex() {
        $allergyData = $this->getAllergyData();
        $allergySource = ArrayHelper::map(Patient::$allergy1, 'id', 'name');
        $allergyReaction = ArrayHelper::map(Patient::$allergy2, 'id', 'name');
        $allergyDegree = ArrayHelper::map(Patient::$allergy3, 'id', 'name');
        foreach ($allergyData as $key => $value) {
            $str1 = '';
            $str2 = '';
            $oldAllergy = json_decode($value['allergy'], true);
            if (is_array($oldAllergy) && !empty($oldAllergy)) {
                foreach ($oldAllergy as $k => $v) {
                    if ($v['source'] == '1') { //source为1是食物过敏，2是药物过敏
                        $str1 .= ',';
                        $str1 .= $v['source'] ? $allergySource[$v['source']] : '';
                        $str1 .= $v['reaction'] ? ',' . $allergyReaction[$v['reaction']] : '';
                        $str1 .= $v['degree'] ? ',' . $allergyDegree[$v['degree']] : '';
                        $str1 = trim($str1, ',');
                        $this->setAllergy($value['id'], 1, $str1);
                    } else {
                        $str2 .= ',';
                        $str2 .= $v['source'] ? $allergySource[$v['source']] : '';
                        $str2 .= $v['reaction'] ? ',' . $allergyReaction[$v['reaction']] : '';
                        $str2 .= $v['degree'] ? ',' . $allergyDegree[$v['degree']] : '';
                        $str2 = trim($str2, ',');
                        $this->setAllergy($value['id'], 2, $str2);
                    }
                }
            }
        }
    }

    /**
     * @return array
     * 获取所有过敏史非空的数据
     */
    public function getAllergyData() {
        $query = new Query();
        $query->from(TriageInfo::tableName());
        $query->select(['id', 'allergy']);
        $query->where(['<>', 'allergy', '']);
//        $query->orderBy(['id'=>SORT_DESC]);
//        $query->limit(20);
        $data = $query->all();
        return $data;
    }

    /**
     * @param $id gzh_triage_info的主键id
     * @param $type type为1是食物过敏，2是药物过敏
     * @param $str 将旧的过敏史数据转化为新的过敏史数据
     */
    public function setAllergy($id, $type, $str) {
        if ($type == 1) {
            TriageInfo::updateAll(['food_allergy' => $str], ['id' => $id]);
        } else {
            TriageInfo::updateAll(['meditation_allergy' => $str], ['id' => $id]);
        }
    }

    public function actionSpecification() {
        $recipeListSpecification = RecipeList::find()->select(['id', 'specification'])->limit(20)->asArray()->all();
        print_r($recipeListSpecification);
    }

    /**
     * @param int $begin_id 导入起始id  默认为1
     * @param int $end_id 导入结束id  默认为分诊表最大值
     * @return boolean success true为成功，false为失败,默认为true
     * @return int errorCode 错误代码(0-成功,1001-参数错误,默认为0)
     * @return string msg 返回信息
     * @desc 同步分诊表字段
     */
    public function actionDataSynchronous($begin_id = 0, $end_id = 0) {
        $errorCode = 0;
        $success = true;
        $begin_id = $begin_id ? $begin_id : 1; // 起始id  默认为1
        $end_id = $end_id ? $end_id : TriageInfo::find()->select(['max_id' => 'MAX(id)'])->asArray()->one()['max_id']; // 结束id  默认为分诊表最大值
        $triageInfos = TriageInfo::find()->select(['spot_id', 'record_id', 'chiefcomplaint', 'historypresent', 'pasthistory', 'personalhistory', 'genetichistory', 'physical_examination', 'remark'])->where(['between', 'id', $begin_id, $end_id])->asArray()->all();
        if (!empty($triageInfos)) {
            $triageList = array();
            foreach ($triageInfos as $value) {
                $haveRecord = OutpatientRelation::find()->where(['record_id' => $value['record_id']])->count(1);
                if (!$haveRecord) {
                    array_push($triageList, [$value['spot_id'], $value['record_id'], $value['chiefcomplaint'], $value['historypresent'], $value['pasthistory'], $value['personalhistory'], $value['genetichistory'], $value['physical_examination'], $value['remark'], time(), time()]);
                } else {
                    $record_id = $value['record_id'];
                    $this->log("commandsCommonDataSynchronous record_id[$record_id]");
                }
            }
            $rows = Yii::$app->db->createCommand()->batchInsert(OutpatientRelation::tableName(), ['spot_id', 'record_id', 'chiefcomplaint', 'historypresent', 'pasthistory', 'personalhistory', 'genetichistory', 'physical_examination', 'remark', 'create_time', 'update_time'], $triageList)->execute();
            $msg = $rows . ' row affected by the execution';
        } else {
            $msg = 'triageInfos is empty';
        }
        exit(Json::encode($ret = [
                    'success' => $success,
                    'errorCode' => $errorCode,
                    'msg' => $msg
        ]));
    }

    /**
     * @return boolean success true为成功，false为失败,默认为true
     * @return int errorCode 错误代码(0-成功,1001-参数错误,默认为0)
     * @return string msg 返回信息
     * @desc 同步新用户
     */
    public function actionSynchronousNewPatient() {
        $errorCode = 0;
        $success = true;
        $msg = '';
        $rows = '';
        $patientIdList = PatientRecord::find()->select(['patient_id', 'status' => 'group_concat(status)'])->groupBy('patient_id')->asArray()->all();
        $patientIds = array();
        $firstRecorded = [];

        foreach ($patientIdList as $value) {
            $status = explode(',', $value['status']);
            $this->log('status:' . $value['patient_id'] . '---' . json_encode($status));
            if (in_array(5, $status) && count($status) > 1) {
                $firstRecordEnd[] = $value['patient_id']; //旧用户
            } else {
                $patientIds[] = $value['patient_id']; //新用户
            }
        }
        if (!empty($patientIds)) {
            $rows .= Yii::$app->db->createCommand()->update(Patient::tableName(), ['first_record' => 1], ['id' => $patientIds])->execute();
            $msg .= $rows . ' row affected by the execution';
        } else {
            $msg .= 'patientIds is empty';
        }
        if (!empty($firstRecordEnd)) {
            $rows .= Yii::$app->db->createCommand()->update(Patient::tableName(), ['first_record' => 2], ['id' => $firstRecordEnd])->execute();
            $msg .= $rows . 'firstRecordEnd row affected by the execution';
        } else {
            $msg .= 'firstRecordEnd is empty';
        }
        exit(Json::encode($ret = [
                    'success' => $success,
                    'errorCode' => $errorCode,
                    'msg' => $msg
        ]));
    }

    public function log($info) {
        $time = microtime(true);
        $log = new FileTarget();
        $log->logFile = Yii::$app->getRuntimePath() . '/logs/commands.log';
        $log->messages[] = [$info, 1, 'application', $time];
        $log->export();
    }

    /**
     * @return boolean success true为成功，false为失败,默认为true
     * @return int errorCode 错误代码(0-成功,1001-参数错误,默认为0)
     * @return string msg 返回信息
     * @author JeanneWu
     * @desc 将病历号改成7位数 从0000001开始
     */
    public function actionResetPatientNumber() {

        $parentSpotId = Patient::find()->select(['spot_id'])->indexBy('spot_id')->asArray()->all();

        foreach ($parentSpotId as $value) {
            $patientNumber = Patient::find()->select(['id', 'patient_number'])->where(['spot_id' => $value['spot_id']])->orderBy(['id' => SORT_ASC])->asArray()->all();
            $pNum = 1;
            foreach ($patientNumber as $patient) {
//                var_dump(sprintf('%07d', $pNum));
                Patient::updateAll(['patient_number' => sprintf('%07d', $pNum)], ['id' => $patient['id']]);
//                if($pNum == 3){
//                   break;
//                }
                $pNum++;
            }
        }

        exit(Json::encode($res = [
                    'success' => 'true'
        ]));
    }

    /**
     * @return boolean success true为成功，false为失败,默认为true
     * @return string msg 返回信息
     * @return string rows 查询得到总共的数据量
     * @desc 修复报到表的预约类型，科室，医生数据，如果旧数据是直接报到的，则预约科室为空
     */
    public function actionSetReportData() {
        $success = true;
        $msg = '';
        $query = new Query();
        $query->from(['a' => PatientRecord::tableName()]);
        $query->leftJoin(['b' => Report::tableName()], '{{a}}.id = {{b}}.record_id');
        $query->leftJoin(['c' => TriageInfo::tableName()], '{{a}}.id = {{c}}.record_id');
        $query->leftJoin(['d' => Appointment::tableName()], '{{a}}.id = {{d}}.record_id');
        $query->select(['recordId' => 'a.id', 'a.type', 'a.type_description', 'c.doctor_id', 'd.once_department_id', 'd.second_department_id']);
        //预约服务类型type的值为1-4的为旧数据
        //patient_record表的status值应该是报到后即2-已登记，3 -已分诊, 4-接诊中,5-接诊结束
        $query->where(['a.type' => [1, 2, 3, 4], 'a.status' => [2, 3, 4, 5]]);
        $data = $query->all();
        $rowsAll = 0;
        if (!empty($data)) {
            foreach ($data as $v) {
                $rows = Report::updateAll(['type' => $v['type'], 'type_description' => $v['type_description'], 'once_department_id' => $v['once_department_id'] ? $v['once_department_id'] : 0, 'second_department_id' => $v['second_department_id'] ? $v['second_department_id'] : 0, 'doctor_id' => $v['doctor_id'] ? $v['doctor_id'] : 0], ['record_id' => $v['recordId']]);
                $rowsAll = $rowsAll + $rows;
            }
            $msg .= $rowsAll . "row affected by the execution";
        } else {
            $msg .= 'The data is empty';
        }

        exit(Json::encode($ret = [
                    'success' => $success,
                    'msg' => $msg,
                    'rows' => count($data)
        ]));
    }

    /**
     * @return boolean success true为成功，false为失败,默认为true
     * @return string msg 返回信息
     * @return string rows 查询得到总共的数据量
     * @desc 合并体检发育评估其他结果的评估方式，评估结果到新的字段
     */
    public function actionMergeChildCheckData() {
        $success = true;
        $msg = '';
        $query = new Query();
        $query->from(ChildExaminationAssessment::tableName());
        $query->select(['id', 'evaluation_type_result', 'other_evaluation_type', 'other_evaluation_result']);
        $query->where(['and', ['or', 'LENGTH(evaluation_type_result) = 0', 'ISNULL(evaluation_type_result)'], ['or', 'LENGTH(other_evaluation_type) > 0', 'LENGTH(other_evaluation_result) > 0']]);
        $data = $query->all();
        $rowsAll = 0;
        if (!empty($data)) {
            foreach ($data as $v) {
                $mergeResult = '';
                if (!empty($v['other_evaluation_type'])) {
                    $mergeResult .= $v['other_evaluation_type'];
                }
                if (!empty($v['other_evaluation_result'])) {
                    if (strlen($mergeResult)) {
                        $mergeResult .=";";
                    }
                    $mergeResult .= $v['other_evaluation_result'];
                }

                $rows = ChildExaminationAssessment::updateAll(['evaluation_type_result' => $mergeResult], ['id' => $v['id']]);
                $rowsAll += $rows;
            }

            $msg .= $rowsAll . "row affected by the execution";
        } else {
            $msg .= 'The data is empty';
        }

        exit(Json::encode($res = [
                    'success' => $success,
                    'msg' => $msg,
                    'rows' => count($data)
        ]));
    }

    /**
     * add-serve-type-to-appointment
     * @return boolean success true为成功，false为失败,默认为true
     * @return string msg 返回信息
     * @return string rows 查询得到总共的数据量
     * @desc 给未关联服务类型的预约记录添加服务类型。
     */
    public function actionAddServeTypeToAppointment() {

        //已关联服务类型的预约
        $queryAppointmentTimeAndServer = new Query();
        $queryAppointmentTimeAndServer->select(['time_config_id']);
        $queryAppointmentTimeAndServer->from([AppointmentTimeAndServer::tableName()]);
        $queryAppointmentTimeAndServer->groupBy(['time_config_id']);

        //未设置服务类型的预约
        $query = new Query();
        $query->from([AppointmentConfig::tableName()]);
        $query->select(['id', 'spot_id', 'user_id']);
        $query->where(['not in', 'id', $queryAppointmentTimeAndServer]);
        $query->andWhere('begin_time >= :today', [':today' => strtotime('today')]);
        $query->orderBy(['spot_id' => SORT_ASC, 'user_id' => SORT_ASC]);
        $data = $query->all();
//        echo "sql:". $query->createCommand()->getRawSql().PHP_EOL;
        $findConfigCount = count($data);
        $noserveTypeCount = 0;
        $addSuccessCount = 0;
        $addErrorCount = 0;
        $cache = [];
        foreach ($data as $v) {
            $key = $v['user_id'] . '-' . $v['spot_id'];
            $serveTypes = $cache[$key];
            if (!isset($serveTypes)) {
                $serveTypes = $this->getDoctorServeType($v['user_id'], $v['spot_id']);
                $cache[$key] = $serveTypes;
            }

            if (count($serveTypes) < 1) {
                echo '预约设置id:' . $v['id'] . ' user_id:' . $v['user_id'] . ' spot_id:' . $v['spot_id'] . " 医生无服务，无法添加关联服务 \n";
                $noserveTypeCount++;
            } else {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    foreach ($serveTypes as $sType) {
                        Yii::$app->db->createCommand()
                                ->insert(AppointmentTimeAndServer::tableName(), array(
                                    'time_config_id' => $v['id'],
                                    'spot_type_id' => $sType['id'],
                                    'spot_id' => $v['spot_id'],
                                    'create_time' => strtotime('now'),
                                    'update_time' => strtotime('now')
                                ))->execute();
                    }
                    $dbTrans->commit();
//                    echo '添加 user_id:'.$v['user_id']." spot_id:".$v['spot_id']." 预约设置id:".$v['id']." 服务id:".json_encode($serveTypes).PHP_EOL;
                    $addSuccessCount++;
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    echo 'save error' . json_encode($e->errorInfo, true) . PHP_EOL;
                    $addErrorCount++;
                }
            }
        }

        echo "查询预约设置数:" . $findConfigCount . PHP_EOL;
        echo "无服务类型数:" . $noserveTypeCount . PHP_EOL;
        echo "成功添加数:" . $addSuccessCount . PHP_EOL;
        echo "失败添加数:" . $addErrorCount . PHP_EOL;
        exit();
    }

    /**
     * @param int $spot_id 诊所id
     * @param int $doctor_id 医生id
     * @return Array result
     * @desc 获取医生服务类型
     */
    public function getDoctorServeType($doctor_id, $spot_id) {
        $queryDoctorServeType = new Query();
        $queryDoctorServeType->from(['a' => UserAppointmentConfig::tableName()]);
        $queryDoctorServeType->select(['b.id']);
        $queryDoctorServeType->leftJoin(['b' => SpotType::tableName()], '{{a}}.spot_type_id = {{b}}.id');
        $queryDoctorServeType->where(['b.status' => 1, 'a.spot_id' => $spot_id, 'a.user_id' => $doctor_id]);
        $result = $queryDoctorServeType->all();
        if (count($result) < 1) {
            $queryUser = new Query();
            $doctor = $queryUser->from([User::tableName()])->select(['username'])->where(['id' => $doctor_id])->one();
            echo '查询不到 ' . $doctor['username'] . ' doctor_id:' . $doctor_id . ' spot_id:' . $spot_id . "的服务。\n";
//            echo 'sql:'.$queryDoctorServeType->createCommand()->getRawSql().PHP_EOL;
        }
        return $result;
    }

    public function actionInportCheckCode($spotId) {
        $reader = new \PHPExcel_Reader_Excel2007();
        $excelFile = 'public/file/check_code.xlsx';
        $excel = $reader->load($excelFile);
        $objWorksheet = $excel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow(); // 取得总行数
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); //总列数

        $datas = [];
        for ($row = 1; $row <= $highestRow; $row++) {
            $datas[$row] = array();
            for ($col = 1; $col < $highestColumnIndex; $col++) {
                $datas[$row][$col] = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        $db = Yii::$app->db;
//         $transaction = $db->beginTransaction();
        $rows = [];
//         try {

        foreach ($datas as $key => $value) {

            if ($key == 1) {
                continue;
            }

            $rows[] = [$spotId, $value[1], $value[2], $value[3], $value[4], 1, time(), time()];
        }

        $db->createCommand()->batchInsert(CheckCode::tableName(), ['spot_id', 'major_code', 'add_code', 'name', 'help_code', 'status', 'create_time', 'update_time'], $rows)->execute();
//             $transaction->commit();
        echo count($rows) . '--111';
//         } catch(\Exception $e) {
//             $transaction->rollBack();
//             throw $e;
//         }
    }

    /**
     * move-first-check-data
     * @desc 迁移初步诊断历史数据。
     */
    public function actionMoveFirstCheckData() {

        //已迁移的数据过滤掉
        $queryMovedData = new Query();
        $queryMovedData->from(FirstCheck::tableName());
        $queryMovedData->where(['check_code_id' => 0]);
        $queryMovedData->select(['record_id']);
        $queryMovedData->groupBy(['record_id']);

        $queryTriageInfo = new Query();
        $queryTriageInfo->from(TriageInfo::tableName());
        $queryTriageInfo->select(['first_check', 'spot_id', 'record_id']);
        $queryTriageInfo->where(['>', 'LENGTH(first_check)', 0]);
        $queryTriageInfo->andWhere(['not in', 'record_id', $queryMovedData]);
        $data = $queryTriageInfo->all();

//        echo "sql:". $queryTriageInfo->createCommand()->getRawSql().PHP_EOL;

        $allCount = count($data);
        $handleCount = 0;

        foreach ($data as $v) {
            try {
                Yii::$app->db->createCommand()
                        ->insert(FirstCheck::tableName(), array(
                            'spot_id' => $v['spot_id'],
                            'record_id' => $v['record_id'],
                            'content' => $v['first_check'],
                            'create_time' => strtotime('now'),
                            'update_time' => strtotime('now')
                        ))->execute();

                $handleCount++;
            } catch (yii\db\Exception $e) {
                
            }
        }


        echo "需迁移记录数:" . $allCount . PHP_EOL;
        echo "迁移记录数:" . $handleCount . PHP_EOL;
        exit();
    }

    /**
     * @desc 修复其他收费清单显示其他收费的汇总
     */
    public function actionUpdateChargeLog() {
        $query = new Query();
        $query->from(['a' => ChargeRecordLog::tableName()]);
        $query->select(['a.id', 'a.price', 'a.discount_price', 'b.charge_type']);
        $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id = {{b}}.id');
        $query->where(['b.charge_type' => 2, 'a.material_price' => null]);
        $result = $query->all();
        if (!empty($result)) {
            foreach ($result as $v) {
                ChargeRecordLog::updateAll(['material_price' => $v['price'], 'material_discount_price' => $v['discount_price']], ['id' => $v['id']]);
            }
        }
        echo '修复数据数量为：' . count($result);
        exit();
    }

    /**
     * common/gen-combo-card
     * @desc 生成门诊套餐卡
     */
    public function actionGenComboCard() {

        //cardTypeCode:类型，cardDesc:描述, num:生成数量  
        $card43 = array(
            'cardTypeCode' => 43,
            'cardDesc' => "银盾卡 服务详情：2次全口涂氟、2颗窝沟封闭、1次牙片检查、2次刷牙指导、1次进食习惯指导、1次面型发育评估、无限次口腔检查",
            'num' => 999,
        );

        $card44 = array(
            'cardTypeCode' => 44,
            'cardDesc' => "金盾卡 服务详情：4次全口涂氟、4颗窝沟封闭、1次牙片检查、4次刷牙指导、2次进食习惯指导、1次面型发育评估、无限次口腔检查",
            'num' => 999,
        );

        $card45 = array(
            'cardTypeCode' => 45,
            'cardDesc' => "孕育期牙齿保健套餐服务详情：备孕期口腔健康维护宣教1次、智齿冠周炎风险评估1次、备孕期洁牙1次、孕期口腔健康检查2次、哺乳期（产后半年内）、口腔检查1次、哺乳期（产后半年内）洁牙 1次",
            'num' => 999,
        );

        $card46 = array(
            'cardTypeCode' => 46,
            'cardDesc' => "0-5岁口腔保健套餐服务详情：口腔健康检查、评估、指导无限次；牙片检查无限次；全口涂氟无限次；乳牙补牙无限次；乳牙根管治疗无限次；乳磨牙窝沟封闭无限次；乳牙拔除无限次；牙齿外伤紧急处理无限次",
            'num' => 999,
        );
        $card47 = array(
            'cardTypeCode' => 47,
            'cardDesc' => "妈咪产前心理健康辅导；妈咪泌乳指导，新手妈妈育儿课程；6月龄辅食添加营养指导，12月龄营养喂养评估；专属一对一签约医生专属健康顾问定制个性化健康计划；不限次数远程定签医生咨询（不含小鱼押金）；线上健康问诊基金1000元，保险和疫苗计划；全年口腔防护（涂氟洗牙和窝沟封闭）",
            'num' => 999,
        );

//        $card39 = array(
//            'cardTypeCode' => 39,
//            'cardDesc' => "孕期管理套餐服务详情：妈咪知道线上问诊1000元健康计划、2次孕期营养门诊、1次泌乳门诊",
//            'num' => 50,
//        );
//
//        $card40 = array(
//            'cardTypeCode' => 40,
//            'cardDesc' => "儿童口腔管理套餐（儿童口腔年卡）服务详情：1年内无限次涂氟，赠送口腔检查4次、乳牙拔除2颗",
//            'num' => 100,
//        );
//
//        $card41 = array(
//            'cardTypeCode' => 41,
//            'cardDesc' => "儿童口腔管理套餐（儿童口腔年卡）服务详情：四颗牙齿的窝沟封闭，赠送口腔检查4次、乳牙拔除2颗",
//            'num' => 100,
//        );
//
//        $card42 = array(
//            'cardTypeCode' => 42,
//            'cardDesc' => "儿童口腔管理套餐（儿童口腔年卡）服务详情：四颗补牙（材料富士九），赠送口腔检查4次、乳牙拔除2颗",
//            'num' => 100,
//        );
//        $cardInfo = [$card35, $card36, $card37, $card38, $card39, $card40, $card41, $card42];
        $cardInfo = [$card43, $card44, $card45, $card46, $card47];

        $insertCount = 0;
        $cardDb = Yii::$app->cardCenter;

        $insertRows = array();
        $offset = 0;
        for ($i = 0; $i < count($cardInfo); $i++) {
            $item = $cardInfo[$i];
            echo "cardTypeCode:" . $item["cardTypeCode"] . " num:" . $item["num"] . PHP_EOL;
            $rows = $this->genComboCardSql($item["num"], $offset, $item["cardTypeCode"], $item["cardDesc"]);
            if (count($rows) > 0) {
                $offset += count($rows);
                $insertRows = array_merge($insertRows, $rows);
            }
        }

        if (count($insertRows) < 1) {
            echo "无数据需要插入\n";
            return 0;
        }

        echo "需要插入条数:" . count($insertRows) . PHP_EOL;

        $dbTrans = $cardDb->beginTransaction();
        try {
            $insertCount = $cardDb->createCommand()
                    ->batchInsert(CardManage::tableName(), ['f_card_id', 'f_card_type_code', 'f_identifying_code', 'f_card_desc', 'f_effective_time', 'f_create_time'], $insertRows)
                    ->execute();

            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
            echo 'save error' . json_encode($e->errorInfo, true) . PHP_EOL;
        }

        echo "插入数据条数:" . $insertCount . PHP_EOL;

        exit();
    }

    /**
     * @param int $num 生成数量
     * @param int $offset 起始数
     * @param int $cardTypeCode 卡类型
     * @param string $cardDesc 卡描述
     * @return Array result
     * @desc 生成门诊套餐卡sql。
     */
    public function genComboCardSql($num, $offset, $cardTypeCode, $cardDesc) {

        $part1 = "1076";
        $part2 = strval(date("Y"));
        $part3 = strval(date("md"));

        if (empty($cardDesc)) {
            $cardDesc = "";
        }

        $effectiveTime = strtotime(date("Y-m-d"));
        $numOffset = $offset - 1;
        if (1 == $offset) {
            $numOffset = 0;
        }

        $rows = [];
        $index = 1;
        for ($i = $offset; $i < $num + $numOffset + 1; $i++) {
            $iStr = sprintf("%04d", $i);
            $cardNum = $part1 . $part2 . $part3 . $iStr;
            $code = hash("crc32b", $cardNum, false);

            //'f_card_id','f_card_type_code','f_identifying_code', 'f_card_desc', 'f_effective_time','f_create_time'
            $rows[] = [$cardNum, $cardTypeCode, $code, $cardDesc, $effectiveTime, time()];

            echo "[" . $cardNum . " " . $code . "]";
            if ($index % 4 == 0 || $index == $num) {
                echo PHP_EOL;
            }

            $index++;
        }

        return $rows;
    }

    /**
     * common/gen-service-config
     * @desc 生成门诊套餐卡服务类型
     */
    public function actionGenServiceConfig() {
        $card43 = array(
            'cardTypeCode' => 43,
            'services' => array(
                array(
                    'title' => '全口涂氟',
                    'counts' => 2,
                ),
                array(
                    'title' => '窝沟封闭',
                    'counts' => 2,
                ),
                array(
                    'title' => '牙片检查',
                    'counts' => 1,
                ),
                array(
                    'title' => '口腔检查',
                    'counts' => 999,
                ),
            ),
        );

        $card44 = array(
            'cardTypeCode' => 44,
            'services' => array(
                array(
                    'title' => '全口涂氟',
                    'counts' => 4,
                ),
                array(
                    'title' => '窝沟封闭',
                    'counts' => 4,
                ),
                array(
                    'title' => '牙片检查',
                    'counts' => 1,
                ),
                array(
                    'title' => '口腔检查',
                    'counts' => 999,
                )
            ),
        );

        $card45 = array(
            'cardTypeCode' => 45,
            'services' => array(
                array(
                    'title' => '口腔检查',
                    'counts' => 5
                ),
                array(
                    'title' => '洁牙',
                    'counts' => 2,
                )
            ),
        );

        $card46 = array(
            'cardTypeCode' => 46,
            'services' => array(
                array(
                    'title' => '口腔检查',
                    'counts' => 999,
                ),
                array(
                    'title' => '牙片检查',
                    'counts' => 999,
                ),
                array(
                    'title' => '全口涂氟',
                    'counts' => 999,
                ),
                array(
                    'title' => '乳牙补牙',
                    'counts' => 999,
                ),
                array(
                    'title' => '乳牙根管治疗',
                    'counts' => 999,
                ),
                array(
                    'title' => '乳牙拔除',
                    'counts' => 999,
                ),
                array(
                    'title' => '乳磨牙窝沟封闭',
                    'counts' => 999,
                ),
                array(
                    'title' => '牙齿外伤紧急处理',
                    'counts' => 999,
                ),
            ),
        );
        $card47 = array(
            'cardTypeCode' => 47,
            'services' => array(
                array(
                    'title' => '新生儿一年儿保套餐',
                    'counts' => 999,
                ),
                array(
                    'title' => '全年无限次免收诊金门诊服务（不包治疗检验和费用）',
                    'counts' => 999,
                ),
                array(
                    'title' => '全年口腔防护（涂氟洗牙和窝沟封闭）',
                    'counts' => 999,
                ),
            ),
        );

        $cardInfo = [$card47];//只需要新增丽晟47的卡类型


        $insertRows = [];
        for ($i = 0; $i < count($cardInfo); $i++) {
            $cardItem = $cardInfo[$i];
            $services = $cardItem['services'];
            $cardTypeCode = $cardItem['cardTypeCode'];
            echo "cardTypeCode:" . $cardTypeCode . PHP_EOL;
            for ($j = 0; $j < count($services); $j++) {
                $servicesItem = $services[$j];
                $title = $servicesItem['title'];
                $counts = $servicesItem['counts'];
                echo $title . " " . $counts . PHP_EOL;
                //card_type,service_name,service_desc,service_total,create_time,update_time
                $insertRows[] = [$cardTypeCode, $title, $title, $counts, time(), time()];
            }
        }

        if (count($insertRows) < 1) {
            echo "无数据需要插入\n";
            exit();
        }
        echo "需要插入条数:" . count($insertRows) . PHP_EOL;

        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $insertCount = Yii::$app->db->createCommand()
                    ->batchInsert(ServiceConfig::tableName(), ['card_type', 'service_name', 'service_desc', 'service_total', 'create_time', 'update_time'], $insertRows)
                    ->execute();

            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
            echo 'save error' . json_encode($e->errorInfo, true) . PHP_EOL;
        }

        echo "插入数据条数:" . $insertCount . PHP_EOL;

        exit();
    }

    /**
     * common/gen-card-spot
     * @desc 生成门诊卡适用的诊所
     */
    public function actionGenCardSpot() {
        $cardInfo = [43, 44, 45, 46, 47];
        $spotId = YII_DEBUG ? 75 : 62; //线上诊所ID
        $insertRows = [];
        foreach ($cardInfo as $val) {
            $insertRows[] = [$val, $spotId, time(), time()];
        }
        if (count($insertRows) < 1) {
            echo "无数据需要插入\n";
            exit();
        }
        echo "需要插入条数:" . count($insertRows) . PHP_EOL;

        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $insertCount = Yii::$app->db->createCommand()
                    ->batchInsert(CardSpotConfig::tableName(), ['card_type', 'spot_id', 'create_time', 'update_time'], $insertRows)
                    ->execute();

            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
            echo 'save error' . json_encode($e->errorInfo, true) . PHP_EOL;
        }

        echo "插入数据条数:" . $insertCount . PHP_EOL;

        exit();
    }

    /**
     * allergy-history-move-to-outpatient
     * @desc 食物过敏，药物过敏历史数据迁移至门诊过敏史
     */
    public function actionAllergyHistoryMoveToOutpatient() {

        //（非口腔)药物过敏
        $meditationQuery = new Query();
        $meditationQuery->select(['spot_id', 'record_id', 'allergy_content' => 'meditation_allergy', 'type' => 'cast(1 as signed)',
            'create_time' => 'unix_timestamp(now())', 'update_time' => 'unix_timestamp(now())']);
        $meditationQuery->from([TriageInfo::tableName()]);
        $meditationQuery->where(['>', 'LENGTH(meditation_allergy)', 0]);

        //(非口腔)食物过敏
        $foodQuery = new Query();
        $foodQuery->select(['spot_id', 'record_id', 'allergy_content' => 'food_allergy', 'type' => 'cast(2 as signed)',
            'create_time' => 'unix_timestamp(now())', 'update_time' => 'unix_timestamp(now())']);
        $foodQuery->from([ TriageInfo::tableName()]);
        $foodQuery->where(['>', 'LENGTH(food_allergy)', 0]);

        //(口腔)药物过敏
        $teethMeditationQuery = new Query();
        $teethMeditationQuery->select(['spot_id', 'record_id', 'allergy_content' => 'drugallergy', 'type' => 'cast(1 as signed)',
            'create_time' => 'unix_timestamp(now())', 'update_time' => 'unix_timestamp(now())']);
        $teethMeditationQuery->from([DentalHistory::tableName()]);
        $teethMeditationQuery->where(['>', 'LENGTH(drugallergy)', 0]);

        //(口腔)食物过敏
        $teethFoodQuery = new Query();
        $teethFoodQuery->select(['spot_id', 'record_id', 'allergy_content' => 'foodallergy', 'type' => 'cast(2 as signed)',
            'create_time' => 'unix_timestamp(now())', 'update_time' => 'unix_timestamp(now())']);
        $teethFoodQuery->from([DentalHistory::tableName()]);
        $teethFoodQuery->where(['>', 'LENGTH(foodallergy)', 0]);

        $meditationResult = $meditationQuery->all();
        $foodResult = $foodQuery->all();
        $teethMeditationResult = $teethMeditationQuery->all();
        $teethFoodResult = $teethFoodQuery->all();

        $insertRows = array_merge($meditationResult, $foodResult, $teethMeditationResult, $teethFoodResult);

        echo "需插入:" . count($insertRows) . "\n ";

        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $insertField = ['spot_id', 'record_id', 'allergy_content', 'type', 'create_time', 'update_time'];
            $insertCount = Yii::$app->db->createCommand()
                    ->batchInsert(AllergyOutpatient::tableName(), $insertField, $insertRows)
                    ->execute();

            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
            echo 'save error' . json_encode($e->errorInfo, true) . PHP_EOL;
        }

        echo "插入:" . $insertCount . PHP_EOL;
        exit();
    }

    /**
     * allergy-history-move-to-patient-allergy
     * @desc 食物过敏，药物过敏历史数据迁移至患者基本信息过敏史
     */
    public function actionAllergyHistoryMoveToPatientAllergy() {

        //已经有基本信息过敏史的患者不迁移历史数据，避免重复添加
        $queryPatientAllergy = new Query();
        $queryPatientAllergy->select(['patient_id']);
        $queryPatientAllergy->from([PatientAllergy::tableName()]);
        $queryPatientAllergy->groupBy(['patient_id']);

        $query = new Query();
        $query->select(['a.id', 'spot_id' => 'd.parent_spot', 'a.patient_id', 'b.meditation_allergy', 'b.food_allergy',
            'teeth_meditation_allergy' => 'c.drugallergy', 'teeth_food_allergy' => 'c.foodallergy']);
        $query->from(['a' => PatientRecord::tableName()]);
        $query->leftJoin(['b' => TriageInfo::tableName()], '{{a}}.id = {{b}}.record_id');
        $query->leftJoin(['c' => DentalHistory::tableName()], '{{a}}.id = {{c}}.record_id');
        $query->leftJoin(['d' => Spot::tableName()], '{{a}}.spot_id = {{d}}.id');
        $query->where(['a.status' => 5]); //已结束就诊
        $query->andWhere(['not in', 'a.patient_id', $queryPatientAllergy]);
        $query->andWhere('LENGTH(b.meditation_allergy) > 0 or
                                    LENGTH(b.food_allergy) > 0 or
                                    LENGTH(c.foodallergy) > 0 or
                                    LENGTH(c.drugallergy) > 0');
        $query->orderBy(['a.patient_id' => SORT_DESC, 'a.end_time' => SORT_DESC]); //end_time逆序，导入最新的
        $result = $query->all();

        $insertedUser = []; //缓存已插入的用户，避免同一用户多条问诊单而重复插入
        $insertRows = [];
        foreach ($result as $record) {
            $patient_id = $record['patient_id'];
            $spot_id = $record['spot_id'];
            $meditation_allergy = $record['meditation_allergy'];
            $food_allergy = $record['food_allergy'];
            $teeth_meditation_allergy = $record['teeth_meditation_allergy'];
            $teeth_food_allergy = $record['teeth_food_allergy'];

            if (!in_array($patient_id, $insertedUser)) {
                if (!empty($meditation_allergy)) {
                    $insertRows[] = [$spot_id, $patient_id, 1, $meditation_allergy, time(), time()];
                }

                if (!empty($food_allergy)) {
                    $insertRows[] = [$spot_id, $patient_id, 2, $food_allergy, time(), time()];
                }

                if (!empty($teeth_meditation_allergy)) {
                    $insertRows[] = [$spot_id, $patient_id, 1, $teeth_meditation_allergy, time(), time()];
                }

                if (!empty($teeth_food_allergy)) {
                    $insertRows[] = [$spot_id, $patient_id, 2, $teeth_food_allergy, time(), time()];
                }

                $insertedUser[] = $patient_id;
            }
        }

        if (count($insertRows) < 1) {
            echo "无数据需要插入\n";
            exit();
        }
        echo "需插入:" . count($insertRows) . "\n ";

        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $insertField = ['spot_id', 'patient_id', 'type', 'allergy_content', 'create_time', 'update_time'];
            $insertCount = Yii::$app->db->createCommand()
                    ->batchInsert(PatientAllergy::tableName(), $insertField, $insertRows)
                    ->execute();

            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
            echo 'save error' . json_encode($e->errorInfo, true) . PHP_EOL;
        }

        echo "插入:" . $insertCount . PHP_EOL;
        exit();
    }

    /**
     * server-type-third-plat-form-history-repair
     * 【妈咪知道（第三方）】不可预约未开放的服务类型--历史数据修复脚本
     */
    public function actionServerTypeThirdPlatFormHistoryRepair(){

                $data=[];
                $field=['id','spot_id'];
                $platFormId=2;  //默认预约第三方平台ID    1-妈咪知道APP 2-健康160
                $result=SpotType::find()->select($field)->asArray()->all();

             if (count($result) < 1) {
                echo "no data to insert\n";
                 exit();
                 }
                echo "need to insert:" . count($result) . "\n ";
                foreach($result as $key=>$value){
                    $data[$key]['platform_id']=$platFormId;
                    $data[$key]['spot_id']=$value['spot_id'];
                    $data[$key]['spot_type_id']=$value['id'];
                    $data[$key]['create_time']=time();
                    $data[$key]['update_time']=time();
                }
                $insertField=['platform_id','spot_id','spot_type_id','create_time','update_time'];
                $insertCount=Yii::$app->db->createCommand()->batchInsert(ThirdPlatform::tableName(),$insertField,$data)->execute();
                echo "insert:" . $insertCount . PHP_EOL;
                exit();
    }
    
    /**
     * @desc 修复就诊记录报告数量
     * @param integer $spotId 诊所id
     * @param integer $recordId 就诊记录id
     * @param integer $num 数量
     */
    public function actionRepairReportTime($spotId,$recordId,$num){
        
        Outpatient::setMadeReport($spotId, $recordId, $num);
        echo 'spotid:'.$spotId;
        echo 'recordId:'.$recordId;
        echo 'made--'.Yii::getAlias('@madeReportNum') . $spotId . '_' . $recordId;
        echo Yii::$app->cache->get(Yii::getAlias('@madeReportNum') . $spotId . '_' . $recordId).'---'.Yii::$app->cache->get(Yii::getAlias('@pendingReportNum') . $spotId . '_' . $recordId);;
    }
    /**
     * @desc 合并患者信息--包括就诊记录，卡，家庭成员
     * @param integer $oldPatient 旧患者ID
     * @param integer $newPatient 新患者ID
     */
    public function actionRepairPatientRecord($oldPatient,$newPatient){
        
        $db = Yii::$app->db;
        $db->createCommand()->update(Appointment::tableName(), ['patient_id' => $newPatient],['patient_id' => $oldPatient])->execute();
        $db->createCommand()->update(ChargeRecord::tableName(),['patient_id' => $newPatient],['patient_id' => $oldPatient])->execute();
        $db->createCommand()->update(FirstOrderFree::tableName(),['patient_id' => $newPatient],['patient_id' => $oldPatient])->execute();
        $db->createCommand()->update(Follow::tableName(),['patient_id' => $newPatient],['patient_id' => $oldPatient])->execute();
        $db->createCommand()->update(MembershipPackageCardFlow::tableName(),['patient_id' => $newPatient],['patient_id' => $oldPatient])->execute();
        $db->createCommand()->update(MembershipPackageCardUnion::tableName(),['patient_id' => $newPatient],['patient_id' => $oldPatient])->execute();
        $db->createCommand()->update(MessageCenter::tableName(),['patient_id' => $newPatient],['patient_id' => $oldPatient])->execute();
        $db->createCommand()->update(PatientAllergy::tableName(),['patient_id' => $newPatient],['patient_id' => $oldPatient])->execute();
        $db->createCommand()->update(PatientFamily::tableName(),['patient_id' => $newPatient],['patient_id' => $oldPatient])->execute();
        $db->createCommand()->update(PatientRecord::tableName(),['patient_id' => $newPatient],['patient_id' => $oldPatient])->execute();
        $db->createCommand()->update(PatientSubmeter::tableName(),['patient_id' => $newPatient],['patient_id' => $oldPatient])->execute();
        $db->createCommand()->update(Report::tableName(),['patient_id' => $newPatient],['patient_id' => $oldPatient])->execute();
        
        
    }


    public function actionRepairDentalType(){
        $query = new Query();
        $query->select(['a.record_id','a.type']);
        $query->from(['a' => DentalHistory::tableName()]);
        $query->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id = {{b}}.id');
        $query->where(['b.status' => 5]); //已结束就诊
        $result = $query->all();
        $db = Yii::$app->db;
        $originCount = count($result);
        $count = 0;
        if(!empty($result)){
            foreach($result as $key => $value){
                if($value['type'] == 1){
                    $db->createCommand()->update(Report::tableName(), ['record_type' => 4],['record_id' => $value['record_id']])->execute();
                }else{
                    $db->createCommand()->update(Report::tableName(), ['record_type' => 5],['record_id' => $value['record_id']])->execute();
                }
                $count++;
            }
        }
        echo "repair:" . $count;
        echo "origin:" . $originCount . PHP_EOL;
        exit();
    }

    /**
     * 修复模板套餐的诊金历史数据
     */
    public function actionTemplateFee(){
        $query = new Query();
        $query->select(['a.id','b.price']);
        $query->from(['a' => OutpatientPackageTemplate::tableName()]);
        $query->leftJoin(['b' => MedicalFee::tableName()], '{{a}}.medical_fee_price = {{b}}.id');
        $result = $query->all();
        $db = Yii::$app->db;
        $originCount = count($result);
        $count = 0;
        if(!empty($result)){
            foreach($result as $key => $value){
                $db->createCommand()->update(OutpatientPackageTemplate::tableName(),['medical_fee_price' => $value['price']],['id' => $value['id']])->execute();
                $count++;
            }
        }
        echo "repair:" . $count;
        echo "origin:" . $originCount . PHP_EOL;
        exit();
    }



}
