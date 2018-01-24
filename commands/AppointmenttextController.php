<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii;
use yii\console\Controller;
use app\modules\make_appointment\models\Appointment;
use yii\db\Query;
use app\modules\patient\models\PatientRecord;
use app\common\Common;
use yii\log\FileTarget;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppointmenttextController extends Controller
{

    /**
     * 发送预约到时短信提醒
     * @param string $message the message to be echoed.
     */
    public function actionIndex() {
        $this->log('lalala');
        $appointmentData = $this->getAppointmentInfo();
        if (!empty($appointmentData)) {
            foreach ($appointmentData as $key => $value) {
                $nowTime = intval($value['time']) - time();
                $this->log('时间：' . $nowTime);
                $send = false;
                if (4 * 60 * 60 - 600 < $nowTime && $nowTime <= 4 * 60 * 60) {
                    $send = true;
                } else if (24 * 60 * 60 - 600 < $nowTime && $nowTime <= 24 * 60 * 60) {
                    $send = true;
                }
                if ($send) {
                    $result = Common::curlPost(Yii::$app->params['hisApiHost'] . Yii::getAlias('@hisCommonSendAppointmentTimer'), ['id' => $value['id']]);
                    $this->log('定时预约result:' . $result);
                }
                $this->log('定时预约id:' . $value['id']);
                $this->log('定时预约url:' . Yii::$app->params['hisApiHost'] . Yii::getAlias('@hisCommonSendAppointmentTimer'));
            }
        }
    }

    public function getAppointmentInfo() {
        $query = new Query();
        $query->from(['a' => Appointment::tableName()]);
        $query->select(['a.id', 'd.status', 'a.time']);
        $query->leftJoin(['d' => PatientRecord::tableName()], '{{a}}.record_id = {{d}}.id');
        $query->where(['>', 'a.time', time()]);
        $query->andWhere(['<', 'a.time', strtotime('+1 day')]);
        $query->andWhere(['d.status' => '1']);
        $data = $query->all();
        return $data;
    }

    public function log($info) {
        $time = microtime(true);
        $log = new FileTarget();
        $log->logFile = Yii::$app->getRuntimePath() . '/logs/commands.log';
        $log->messages[] = [$info, 1, 'application', $time];
        $log->export();
    }

}
