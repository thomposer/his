<?php

namespace app\modules\api\controllers;

use app\modules\charge\models\ChargeRecord;
use app\modules\make_appointment\models\Appointment;
use app\modules\patient\models\PatientRecord;
use app\modules\triage\models\TriageInfo;
use app\specialModules\recharge\models\CardFlow;
use app\specialModules\recharge\models\CardRecharge;
use Yii;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use app\modules\data\models\Data;

class DataController extends CommonController
{
    public function behaviors()
    {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-chart-data' => ['post'],
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    /**
     * get-chart-data
     * @param string $date 接诊日期
     * @return array 返回报表卡片信息
     * @desc 返回报表卡片信息
     */
    public function actionGetChartData() {
        $date = Yii::$app->request->post('date');
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(!$date){
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $result = array();
        $begin = date('Y-m-d H:i:s',strtotime($date));
        $end = date('Y-m-d H:i:s',strtotime("$date +1 day"));
        $beginTime = strtotime($begin);
        $endTime =  strtotime($end);



        $query = new Query();
        $query->from(['a' => PatientRecord::tableName()]);
        $query->select(['count'=>'COUNT(1)']);
        $query->leftJoin(['b' => Appointment::tableName()],'{{b}}.record_id = {{a}}.id');
        $query->where(['a.spot_id' => $this->spotId]);
        $query->andWhere(['!=','status',7]);
        $query->andWhere(['between', 'b.time', $beginTime, $endTime]);
        $reservationNumber = $query->one()['count'];

        $query = new Query();
        $query->from(['a' => PatientRecord::tableName()]);
        $query->select(['count'=>'COUNT(1)']);
        $query->leftJoin(['b' => TriageInfo::tableName()],'{{b}}.record_id = {{a}}.id');
        $query->where(['a.spot_id' => $this->spotId]);
        $query->andWhere(['between', 'b.diagnosis_time', $beginTime, $endTime]);
        $actualNumber = $query->one()['count'];

        $paymentNumber = ChargeRecord::find()->select(['count'=>'COUNT(DISTINCT(record_id))'])->where(['spot_id' => $this->spotId,'status' => [1,4]])->andWhere(['between', 'create_time', $beginTime, $endTime])->asArray()->one()['count'];

        $result['reservationNumber'] = $reservationNumber; // 预约人数
        $result['actualNumber'] = $actualNumber; // 实到人数
        $result['paymentNumber'] = $paymentNumber; // 付款人数



        $vipCardSum = CardRecharge::find()->select(['count'=>'COUNT(1)'])->where(['f_spot_id' => $this->spotId])->andWhere(['between', 'f_create_time', $begin, $end])->asArray()->one(CardRecharge::getDb())['count'];

        $vipCard = CardFlow::find()->select(['sum'=>'SUM(f_record_fee)'])->where(['f_record_type' => [1],'f_spot_id' => $this->spotId])->andWhere(['between', 'f_create_time', $begin, $end])->asArray()->one(CardFlow::getDb())['sum'];
        $vipCardPrice = $vipCard?Data::getNumAndDeciaml($vipCard):Data::getNumAndDeciaml('0.00');

        $result['vipCardSum'] = $vipCardSum; // 会员卡销量
        $result['vipCardPrice']['num'] = $vipCardPrice['num']; // 会员卡充值金额
        $result['vipCardPrice']['decimal'] = $vipCardPrice['decimal']; // 会员卡充值金额



        $actual = 0;
        $recordIds = array();
        $actualQuery = ChargeRecord::find()->select(['price','record_id'])->where(['spot_id' => $this->spotId])->andWhere(['between', 'create_time', $beginTime, $endTime]);
        if($date != date('Y-m-d')){
            $actual1 = $actualQuery->andWhere(['status' => [1,4]])->asArray()->all();
            foreach ($actual1 as $value) {
                $actual += $value['price'];
                array_push($recordIds,$value['record_id']);
            }
            $actual2 = ChargeRecord::find()->select(['sum'=>'SUM(price)'])->where(['spot_id' => $this->spotId,'status'=>3,'record_id'=>$recordIds])->andWhere(['>', 'create_time', $endTime])->asArray()->one()['sum'];
            if($actual2){
                $actual = $actual2+$actual;
            }
        }else{
            $actual1 = $actualQuery->andWhere(['status' => 1])->asArray()->all();
            foreach ($actual1 as $value) {
                $actual += $value['price'];
            }
        }
        $actualPrice = $actual?Data::getNumAndDeciaml($actual):Data::getNumAndDeciaml('0.00');

        $perPrice = self::getNumAndDeciamlByDivide($actual,$paymentNumber,false);

        $result['actualPrice']['num'] = $actualPrice['num']; // 实收金额
        $result['actualPrice']['decimal'] = $actualPrice['decimal']; // 实收金额
        $result['perPrice']['num'] = $perPrice['num']; // 客单价
        $result['perPrice']['decimal'] = $perPrice['decimal']; // 客单价



        $consumptionProportion = self::getNumAndDeciamlByDivide($actual,$vipCard);
        $marketEfficiency = self::getNumAndDeciamlByDivide($paymentNumber,$reservationNumber);
        $marketingEfficiency = self::getNumAndDeciamlByDivide($vipCardSum,$paymentNumber);

        $result['consumptionProportion']['num'] = $consumptionProportion['num']; // 消费占比
        $result['consumptionProportion']['decimal'] = $consumptionProportion['decimal']; // 消费占比
        $result['marketEfficiency']['num'] = $marketEfficiency['num']; // 市场效率
        $result['marketEfficiency']['decimal'] = $marketEfficiency['decimal']; // 市场效率
        $result['marketingEfficiency']['num'] = $marketingEfficiency['num']; // 销售效率
        $result['marketingEfficiency']['decimal'] = $marketingEfficiency['decimal']; // 销售效率

        $this->result['errorCode'] = 0;
        $this->result['msg'] = 'sucess';
        $this->result['data'] = $result;
        return $this->result;
    }

    /**
     * @param $dividend 被除数
     * @param $divisor 除数
     * @param $is_percent 是否转换为百分数
     * @return array
     * @desc  根据除法分割数字的整数部分和小数部分
     */
    public static function getNumAndDeciamlByDivide($dividend,$divisor,$is_percent = true){
        if(!$dividend || !$divisor){
            $result = 0;
        }else {
            if($is_percent) {
                $result = round($dividend/$divisor,4)*100;
            }else {
                $result = round($dividend/$divisor,2);
            }

        }
        return $result?Data::getNumAndDeciaml($result):Data::getNumAndDeciaml('0.00');
    }

}
