<?php

namespace app\modules\outpatient\models;

use Yii;
use app\modules\spot\models\CaseTemplate;
use app\modules\spot\models\ChildCareTemplate;
use yii\db\Query;
use app\modules\outpatient\models\InspectRecord;
use app\modules\outpatient\models\CheckRecord;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\RecipeRecord;

/**
 * This is the model class for table "{{%patient_record}}".
 *
 * @property string $id
 * @property string $patient_id
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class Outpatient extends \app\modules\patient\models\PatientRecord
{

    public $username;
    public $phone;
    public $reg_time;
    public $record_status;
    public $patients_type;
    public $chose_room;
    public $user_sex;
    public $appointment_id; //预约记录的id
    public $report_id; //报到记录的id
    public $birthday;
    public $diagnosis_time;
    public $triage_time;
    public $pain_score;
    public $fall_score;
    public $first_record;
    public $recordStatus;
    public $second_department;
    public $patient_number;

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'username' => '患者信息',
            'phone' => '手机号',
            'reg_time' => '预约时间',
            'status' => '状态',
            'type_description' => '服务类型',
            'chose_room' => '诊室',
            'diagnosis_time' => '接诊时间',
            'triage_time' => '分诊时间',
            'second_department' => '科室',
        ];
    }

    public static $getStatus = [
        1 => '已预约',
        2 => '已报到',
        3 => '待接诊',
        4 => '接诊中',
        5 => '接诊结束',
        6 => '已失约',
        7 => '已取消预约'
    ];
    public static $getSelectStatus = [
        1 => '已预约',
        2 => '已报到',
        3 => '待接诊',
        4 => '接诊中',
        5 => '接诊结束',
    ];
    public static $getPatientsType = [
//        0 => '未知',
        1 => '初诊',
        2 => '复诊',
        3 => '小儿推拿'
    ];

    public static function templateList() {
        $list = CaseTemplate::find()->select(['id', 'name', 'user_id', 'type'])->where(['spot_id' => self::$staticParentSpotId])->asArray()->all();
        $userId = Yii::$app->user->identity->id;
        $i = 0;
        //type 1通用 2个人
        $data = [];
        if (!empty($list)) {
            foreach ($list as &$val) {
                if ($val['type'] == 1) {
                    $val['group'] = '通用模板';
                    $data[] = $val;
                } elseif (($val['user_id'] == $userId) && ($val['type'] == 2)) {
                    $val['group'] = '我的模板';
                    if ($i == 0) {
                        array_unshift($data, $val);
                    } else {
                        $data[] = $val;
                    }
                    $i++;
                }
            }
        }
        return $data;
    }

    /**
     * 
     * @return string 儿保模板列表
     */
    public static function childTemplateList() {
        $list = ChildCareTemplate::find()->select(['id', 'name', 'content', 'operating_id', 'type'])->where(['spot_id' => self::$staticParentSpotId])->asArray()->all();
        $userId = Yii::$app->user->identity->id;
        $i = 0;
        //type 1通用 2个人
        $data = [];
        if (!empty($list)) {
            foreach ($list as &$val) {
                if ($val['type'] == 1) {
                    $val['group'] = '通用模板';
                    $data[] = $val;
                } elseif (($val['operating_id'] == $userId) && ($val['type'] == 2)) {
                    $val['group'] = '我的模板';
                    if ($i == 0) {
                        array_unshift($data, $val);
                    } else {
                        $data[] = $val;
                    }
                    $i++;
                }
            }
        }
        return $data;
    }

    /**
     * 
     * @param type $model
     * @param 病例ID $case_id
     * @return 根据病例模板Id设置   当前病例信息
     */
    public static function setModel($model, $case_id, $relationModel = null, $outpatientModel = null) {
        $caseInfo = CaseTemplate::find()->where(['id' => $case_id, 'spot_id' => self::$staticParentSpotId])->asArray()->one();
        if (!$caseInfo) {
            return $model;
        }
        $model->cure_idea = $caseInfo['cure_idea'];
        $model->template = $case_id;
        if (!is_null($relationModel)) {
            $relationModel->pastdraghistory = $caseInfo['pastdraghistory'];
            $relationModel->followup = $caseInfo['followup'];
        }
        if (!is_null($caseInfo)) {
            $outpatientModel->chiefcomplaint = $caseInfo['chiefcomplaint']; //主诉
            $outpatientModel->historypresent = $caseInfo['historypresent'];
            $outpatientModel->pasthistory = $caseInfo['pasthistory'];
            $outpatientModel->personalhistory = $caseInfo['personalhistory'];
            $outpatientModel->genetichistory = $caseInfo['genetichistory'];
            $outpatientModel->physical_examination = $caseInfo['physical_examination'];
        }
        return $model;
    }

    /*
     * @desc 获取四大医嘱信息
     * @param $id record_id
     * @param $type (1-四大医嘱  2-治疗)
     * @return array
     */

    public static function getOrdersStatus($id, $type = 1) {
        $query = new Query();
        if (1 == $type) {
            $inspectCount = $query->from(['a' => InspectRecord::tableName()])->select(['a.id'])->where(['a.record_id' => $id, 'a.spot_id' => self::$staticSpotId])->count(1);
            if ($inspectCount) {
                return true;
            }

            $checkCount = $query->from(['a' => CheckRecord::tableName()])->select(['a.id'])->where(['a.record_id' => $id, 'a.spot_id' => self::$staticSpotId])->count(1);
            if ($checkCount) {
                return true;
            }

            $cureCount = $query->from(['a' => CureRecord::tableName()])->select(['a.id'])->where(['a.record_id' => $id, 'a.spot_id' => self::$staticSpotId])->count(1);
            if ($cureCount) {
                return true;
            }

            $recipeCount = $query->from(['a' => RecipeRecord::tableName()])->select(['a.id'])->where(['a.record_id' => $id, 'a.spot_id' => self::$staticSpotId])->count(1);
            if ($recipeCount) {
                return true;
            }
        } else if (2 == $type) {
            $recipeCount = $query->from(['a' => RecipeRecord::tableName()])->select(['a.id'])->where(['a.record_id' => $id, 'a.spot_id' => self::$staticSpotId])->count(1);
            if ($recipeCount) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $record_id 就诊流水id
     * @param $packageRecordId 医嘱套餐流水id
     * @return array 医嘱套餐模板包含的记录
     */
    public static function getPackageRecord($packageRecordId, $record_id) {
        $priceRecord = PackageRecord::find()->select(['name', 'remarks'])->where(['record_id' => $record_id, 'id' => $packageRecordId])->asArray()->one();
        $inspectRecord = InspectRecord::find()->select(['name'])->where(['record_id' => $record_id, 'package_record_id' => $packageRecordId])->asArray()->all();
        $checkRecord = CheckRecord::find()->select(['name'])->where(['record_id' => $record_id, 'package_record_id' => $packageRecordId])->asArray()->all();
        $cureRecord = CureRecord::find()->select(['name', 'time', 'unit'])->where(['record_id' => $record_id, 'package_record_id' => $packageRecordId])->asArray()->all();
        $recipeRecord = RecipeRecord::find()->select(['name', 'num', 'specification', 'unit'])->where(['record_id' => $record_id, 'package_record_id' => $packageRecordId])->asArray()->all();
        $packageRecord = [
            'price' => $priceRecord,
            'inspect' => $inspectRecord,
            'check' => $checkRecord,
            'cure' => $cureRecord,
            'recipe' => $recipeRecord,
        ];
        return $packageRecord;
    }

    /**
     * 
     * @param type $spotId
     * @param type $recordId
     * @param type $num
     * @param type $type 1增加 2减少
     * @return 增加/减少  待出报告数
     */
    public static function setPendingReport($spotId, $recordId, $num, $type = 1) {
        $store = Yii::$app->cache->get(Yii::getAlias('@pendingReportNum') . $spotId . '_' . $recordId);
        if ($type == 1) {
            $store = $store ? $store + $num : $num;
        } else {
            $num = ($store - $num) >= 0 ? $store - $num : 0;
            $store = $num;
            if($store == 0){
                Yii::$app->cache->set(Yii::getAlias('@allReportTime') . $spotId . '_' . $recordId, time());
            }
        }
        Yii::$app->cache->set(Yii::getAlias('@pendingReportNum') . $spotId . '_' . $recordId, $store);
    }

    /**
     * 
     * @param type $spotId
     * @param type $recordId
     * @param type $num
     * @return 增加同时 减少待出报告数  已出报告数
     */
    public static function setMadeReport($spotId, $recordId, $num) {
        $store = Yii::$app->cache->get(Yii::getAlias('@madeReportNum') . $spotId . '_' . $recordId);
        $store = $store ? $store + $num : $num;
        Yii::$app->cache->set(Yii::getAlias('@madeReportNum') . $spotId . '_' . $recordId, $store);
        
        //减少待出报告数量 
        self::setPendingReport($spotId, $recordId, $num, 2);
    }

}
