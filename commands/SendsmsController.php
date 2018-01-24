<?php

namespace app\commands;

use yii;
use yii\db\Query;
use yii\console\Controller;
use app\common\Common;
use yii\log\FileTarget;
use app\modules\behavior\models\SmsRecord;
use app\modules\patient\models\PatientRecord;
use app\modules\patient\models\Patient;
use yii\db\ActiveQuery;
use app\modules\charge\models\ChargeRecordLog;
use app\modules\spot_set\models\SpotConfig;

class SendsmsController extends Controller
{

    /**
     * @desc 定时发送短信表内的短信
     */
    public function actionIndex() {
        $this->log('SendSMSController run');
        $step = 60 * 3;
        $end = strtotime('now');
        $begin = $end - $step;
        $sendRecords = SmsRecord::find()
                        ->select(['id', 'iphone', 'content', 'send_time'])
                        ->Where(['status' => 0])
                        ->andWhere(['between', 'send_time', $begin, $end])
                        ->orderBy(['send_time' => SORT_ASC])->asArray()->all();

        $this->log('SendSMSController count:' . count($sendRecords));
        $success = [];
        $fail = [];
        foreach ($sendRecords as $sms) {
            if (Common::sendSms($sms['iphone'], $sms['content'], 0)) {
                $success[] = $sms['id'];
            } else {
                $fail[] = $sms['id'];
            }
        }

        if (count($success) > 0) {
            SmsRecord::updateAll(['status' => 1, 'update_time' => strtotime('now')], ['id' => $success]);
        }

        if (count($fail) > 0) {
            SmsRecord::updateAll(['status' => 2, 'update_time' => strtotime('now')], ['id' => $fail]);
        }
    }

    public function log($info) {
        $time = microtime(true);
        $log = new FileTarget();
        $log->logFile = Yii::$app->getRuntimePath() . '/logs/commands.log';
        $log->messages[] = [$info, 1, 'application', $time];
        $log->export();
    }

    /**
     * @desc 问诊结束首次收费后发送短信
     * @param integer $record_id 问诊记录id
     * @param integer $spot_id 诊所id
     */
    public static function trySendChargeSMS($record_id, $spot_id) {

        $query = new Query();
        $query->from(['a' => PatientRecord::tableName()]);
        $query->select(['a.patient_id', 'a.status', 'b.iphone']);
        $query->leftJoin(['b' => Patient::tableName()], '{{a}}.patient_id = {{b}}.id');
        $query->where(['a.id' => $record_id, 'a.spot_id' => $spot_id]);
        $info = $query->one();

        //问诊是否结束
        if ($info['status'] != 5) {
            return;
        }

        $chargeCount = ChargeRecordLog::find()->where(['record_id' => $record_id])->count();
        //是否是第一次收费
        if ($chargeCount != 1) {
            return;
        }

        $recordCount = PatientRecord::find()->where(['patient_id' => $info['patient_id'], 'spot_id' => $spot_id, 'status' => 5])->count();
        if ($recordCount > 1) {
            //老用户
            $marketingSms = SpotConfig::getMarketingSms();
            $url = isset($marketingSms[$spot_id]) ? $marketingSms[$spot_id] : '';
            $content = "【妈咪知道】感谢您光临门诊！您的反馈有助于我们为您提供更完善的服务，诚邀您打开链接" . $url . " 参与满意度调查，谢谢！回T退订";
            if ((YII_DEBUG && $spot_id == 75) || (!YII_DEBUG && $spot_id == 62)) {
                $sendTime = 0; //上海诊所  需要即时发送
            } else {
                $sendTime = strtotime('now') + 60 * 60 * 2;
            }
            Common::sendSms($info['iphone'], $content, $sendTime);
        } else {
            //需求调整，新用户不放短信
            return;
            //新用户
//            $content = "【妈咪知道】感谢您光临门诊！您的反馈有助于我们为您提供更完善的服务，诚邀您打开链接https://sojump.com/jq/15203865.aspx 参与满意度调查，提交问卷有机会获得妈咪知道问医优惠券，谢谢！";
//            Common::sendSms($info['iphone'], $content,0);
        }
    }

}
