<?php

namespace app\modules\outpatient\models;

use Yii;
use app\modules\inspect\models\Inspect;
use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use app\modules\check\models\Check;

/*
 * 医生门诊的报告
 */

class Report extends \app\common\base\BaseActiveRecord
{

    /**
     * 
     * @param 流水ID $record_id
     * @param $type 0,1,2根据类型取不同项目
     * @return 报告的数据 
     */
    public static function reportData($record_id, $reportType, $status = 1, $type = 0) {
        /* 检验项目 */
//        实验室检查项目
        if ($reportType == 1) {
            $inspectList = Inspect::getInspectListByRecord($record_id, $status, 2);//此处不需要区分诊所
            /* 关联项目 */
            // $inspectList = [];
            if ($inspectList) {
                foreach ($inspectList as $key => $val) {
                    $inspectList[$key]['inspectUnionDataProvider'] = self::findInspectUnionDataProvider($val['id']);
                }
            }
            return $inspectList;
        }
//        影像学检查项目
        if ($reportType == 2) {
            $checkList = Check::getCheckListByRecord($record_id, $status);
            return $checkList;
        }

        if ($reportType == 3) {
            $inspectList = Inspect::getInspectListByRecord($record_id, $status, 2);//此处不需要区分诊所
            if ($inspectList) {
                foreach ($inspectList as $key => $val) {
                    $inspectList[$key]['inspectUnionDataProvider'] = self::findInspectUnionDataProvider($val['id']);
                }
            } else {
                $inspectList = [];
            }
            $checkList = Check::getCheckListByRecord($record_id, $status);
            if (empty($checkList)) {
                $checkList = [];
            }
            $inspectCheckList = array_merge($inspectList, $checkList);
            return $inspectCheckList;
        }
    }

    /**
     * @return 检查是否有报告
     * @param 就诊流水id $record_id
     * @param $type 1表示去掉诊所ID条件
     */
    public static function checkReport($record_id, $type = 0, $reportType) {
        //reportType ==3 为医生门诊报告查看是否有报告
        if ($reportType == 3) {
            $inspectCount = Inspect::getInspectNum($record_id, 1, $type);
            $checkCount = Check::getCheckNum($record_id, 1, $type);
            if ($inspectCount != 0 || $checkCount != 0) {
                return true;
            }
            return false;
        }
        if ($reportType == 1) {
            $inspectCount = Inspect::getInspectNum($record_id, 1, $type);
            if ($inspectCount != 0) {
                return true;
            }
            return false;
        }
        if ($reportType == 2) {
            $checkCount = Check::getCheckNum($record_id, 1, $type);
            if ($checkCount != 0) {
                return true;
            }
            return false;
        }
    }

    /**
     * @return 检查是否有报告 array
     * @param 就诊流水id $record_id
     * @param $type 1表示去掉诊所ID条件
     */
    public static function checkReportData($record_id, $type = 0, $reportType) {
        //reportType ==3 为医生门诊报告查看是否有报告
//        if ($reportType == 3) {
//            $inspectCount = Inspect::getInspectNumData($record_id, 1, $type);
//            $checkCount = Check::getCheckNumData($record_id, 1, $type);
//            if ($inspectCount != 0 || $checkCount != 0) {
//                return true;
//            }
//            return false;
//        }
        if ($reportType == 1) {
            $inspectCount = Inspect::getInspectNumData($record_id, 1, $type);
            return $inspectCount;
        }
        if ($reportType == 2) {
            $checkCount = Check::getCheckNumData($record_id, 1, $type);
            return $checkCount;
        }
    }

    /**
     * @param $id
     * @return ActiveDataProvider 查实验室关联项目
     */
    protected static function findInspectUnionDataProvider($id) {
        $query = new ActiveQuery(InspectRecordUnion::className());
        $query->from(InspectRecordUnion::tableName());
        $query->select(['id', 'name', 'unit', 'reference', 'result', 'inspect_record_id', 'result_identification']);
        $query->where(['inspect_record_id' => $id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);

        return $dataProvider;
    }

}
