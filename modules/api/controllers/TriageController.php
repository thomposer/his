<?php

namespace app\modules\api\controllers;

use app\modules\spot\models\NursingRecordTemplate;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use app\modules\outpatient\models\OutpatientRelation;
use app\modules\triage\models\TriageInfo;

/**
 * NursingRecordController implements the CRUD actions for NursingRecord model.
 */
class TriageController extends CommonController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * get-template-content
     * @param int $template_id 护理记录模板id
     * @return boolean success true为成功，false为失败,默认为true
     * @return int errorCode 错误代码(0-成功,1001-参数错误,默认为0)
     * @return string msg 错误信息
     * @return array data 模板内容
     * @desc 获取记录模板内容
     */
    public function actionGetTemplateContent() {
        $param = Yii::$app->request->post();
        $template_id = $param['template_id'];
        if ($template_id) {
            $content = NursingRecordTemplate::getNursingRecordTemplate(['id'=>$template_id])[0]['content'];
            $ret = [
                'success' => true,
                'errorCode' => 0,
                'msg' => '',
                'data' => ['content'=>$content]
            ];
        } else {
            $ret = [
                'success' => true,
                'errorCode' => 1001,
                'msg' => '参数错误',
                'data' => []
            ];
        }
        exit(Json::encode($ret));
    }
    
    /**
     * data-synchronous
     * @param int $begin_id 导入起始id  默认为1
     * @param int $end_id 导入结束id  默认为分诊表最大值
     * @return boolean success true为成功，false为失败,默认为true
     * @return int errorCode 错误代码(0-成功,1001-参数错误,默认为0)
     * @return string msg 返回信息
     * @desc 同步分诊表字段
     */
    /*public function actionDataSynchronous($begin_id=0,$end_id=0) {
        $errorCode = 0;
        $success = true;
        $begin_id = $begin_id?$begin_id:1; // 起始id  默认为1
        $end_id = $end_id?$end_id:TriageInfo::find()->select(['max_id'=>'MAX(id)'])->asArray()->one()['max_id']; // 结束id  默认为分诊表最大值
        $triageInfos = TriageInfo::find()->select(['spot_id','record_id','chiefcomplaint','historypresent','pasthistory','personalhistory','genetichistory','physical_examination','remark'])->where(['between','id',$begin_id,$end_id])->asArray()->all();
        if(!empty($triageInfos)){
            $triageList = array();
            foreach ($triageInfos as $value) {
                $haveRecord = OutpatientRelation::find()->where(['record_id'=>$value['record_id']])->count(1);
                if(!$haveRecord){
                    array_push($triageList,[$value['spot_id'],$value['record_id'],$value['chiefcomplaint'],$value['historypresent'],$value['pasthistory'],$value['personalhistory'],$value['genetichistory'],$value['physical_examination'],$value['remark'],time(),time()]);
                }else{
                    $record_id = $value['record_id'];
//                     $this->log("commandsCommonDataSynchronous record_id[$record_id]");
                     Yii::info("commandsCommonDataSynchronous record_id[$record_id]");
                }
            }
            $rows = Yii::$app->db->createCommand()->batchInsert(OutpatientRelation::tableName(), ['spot_id','record_id','chiefcomplaint','historypresent','pasthistory','personalhistory','genetichistory','physical_examination','remark','create_time','update_time'], $triageList)->execute();
            $msg = $rows.' row affected by the execution';
        }else{
            $msg = 'triageInfos is empty';
        }
        exit(Json::encode($ret = [
            'success' => $success,
            'errorCode' => $errorCode,
            'msg' => $msg
        ]));
    }*/
}
