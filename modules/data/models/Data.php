<?php

namespace app\modules\data\models;

use app\modules\charge\models\ChargeInfo;
use yii\db\Query;
use app\modules\patient\models\PatientRecord;
use app\modules\make_appointment\models\Appointment;
use app\modules\triage\models\TriageInfo;
use app\modules\charge\models\ChargeRecord;
use app\modules\charge\models\ChargeRecordLog;
use app\specialModules\recharge\models\CardRecharge;
use app\specialModules\recharge\models\CardFlow;
use app\modules\report\models\Report;

class Data extends \yii\db\ActiveRecord
{
    
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @return mixed 预约人数
     * @desc 获取预约人数
     */
    public static function getReservationNumber($spotId, $beginTime, $endTime) {
        $query = new Query();
        $query->from(['a' => PatientRecord::tableName()]);
        $query->select([
            'num' => 'COUNT(1)',
            'fromTime' => "FROM_UNIXTIME(b.time,'%Y-%m-%d')"
        ]);
        $query->leftJoin(['b' => Appointment::tableName()], '{{b}}.record_id = {{a}}.id');
        $query->where(['a.spot_id' => $spotId]);
        $query->andWhere(['!=', 'status', 7]);
        $query->andWhere(['between', 'b.time', $beginTime, $endTime]);
        $query->groupBy(['fromTime']);
        return $query->indexBy('fromTime')->all();
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @return mixed 实到人数
     * @desc 获取实到人数
     */
    public static function getActualNumber($spotId, $beginTime, $endTime) {
        $query = new Query();
        $query->from(['a' => PatientRecord::tableName()]);
        $query->select([
            'num' => 'COUNT(1)',
            'fromTime' => "FROM_UNIXTIME(b.diagnosis_time,'%Y-%m-%d')"
        ]);
        $query->leftJoin(['b' => TriageInfo::tableName()], '{{b}}.record_id = {{a}}.id');
        $query->where(['a.spot_id' => $spotId]);
        $query->andWhere(['between', 'b.diagnosis_time', $beginTime, $endTime]);
        $query->groupBy(['fromTime']);
        return $query->indexBy('fromTime')->all();
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @return mixed 付款人数
     * @desc 获取付款人数
     */
    public static function getPaymentNumber($spotId, $beginTime, $endTime) {
        $data = ChargeRecordLog::find()
                        ->select([
                            'num' => 'COUNT(DISTINCT record_id)',
                            'fromTime' => "FROM_UNIXTIME(create_time,'%Y-%m-%d')"
                        ])
                        ->where(['spot_id' => $spotId, 'type' => [1]])->andWhere(['between', 'create_time', $beginTime, $endTime])
                        ->groupBy(['fromTime'])
                        ->asArray()->indexBy('fromTime')->all();
        return $data;
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @return mixed 退费金额
     * @desc 获取退费金额
     */
    public static function getReturnPrice($spotId, $beginTime, $endTime) {
        $data = ChargeRecord::find()
                        ->select([
                            'num' => 'SUM(price)',
                            'fromTime' => "FROM_UNIXTIME(create_time,'%Y-%m-%d')"
                        ])
                        ->where(['spot_id' => $spotId, 'status' => [3, 4]])->andWhere(['between', 'create_time', $beginTime, $endTime])
                        ->groupBy(['fromTime'])
                        ->asArray()->indexBy('fromTime')->all();
        return $data;
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @return mixed 应收金额
     * @desc 获取应收金额
     */
    public static function getReceivablePrice($spotId, $beginTime, $endTime) {
        $data = ChargeRecord::find()
                ->select([
                    'num' => 'SUM(price)',
                    'fromTime' => "FROM_UNIXTIME(create_time,'%Y-%m-%d')"
                ])
                ->where(['spot_id' => $spotId, 'status' => [1, 3, 4]])
                ->andWhere(['between', 'create_time', $beginTime, $endTime])
                ->groupBy(['fromTime'])
                ->asArray()
                ->indexBy('fromTime')
                ->all();
        return $data;
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @return mixed 优惠金额
     * @desc 获取优惠金额
     */
    public static function getFavourablePrice($spotId, $beginTime, $endTime) {
        $data = ChargeInfo::find()
                ->select([
                    'num' => 'SUM(card_discount_price + discount_price)',
                    'fromTime' => "FROM_UNIXTIME(create_time,'%Y-%m-%d')"
                ])
                ->where(['spot_id' => $spotId, 'type' => 1])->andWhere(['between', 'create_time', $beginTime, $endTime])
                ->asArray()
                ->indexBy('fromTime')
                ->all();
        return $data;
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @return mixed 退费金额
     * @desc 获取退费金额
     */
    public static function getReturnPrice2($spotId, $beginTime, $endTime) {
        return ChargeRecord::find()->select(['time' => 'create_time', 'price'])->where(['spot_id' => $spotId, 'status' => [3, 4]])->andWhere(['between', 'create_time', $beginTime, $endTime])->asArray()->all();
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @return mixed 应收金额
     * @desc 获取应收金额
     */
    public static function getReceivablePrice2($spotId, $beginTime, $endTime) {
        return ChargeRecord::find()->select(['time' => 'create_time', 'price'])->where(['spot_id' => $spotId, 'status' => [1, 3, 4]])->andWhere(['between', 'create_time', $beginTime, $endTime])->asArray()->all();
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @return mixed 优惠金额
     * @desc 获取优惠金额
     */
    public static function getFavourablePrice2($spotId, $beginTime, $endTime) {
        return ChargeInfo::find()->select(['time' => 'create_time', 'price' => '(card_discount_price + discount_price)'])->where(['spot_id' => $spotId, 'type' => 1])->andWhere(['between', 'create_time', $beginTime, $endTime])->asArray()->all();
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @return mixed 诊金
     * @desc 获取诊金
     */
    /* public static function getFee($spotId,$beginTime,$endTime,$type) {
      $query = new Query();
      $query->select(['time'=>'a.create_time','price' => '(b.num * b.unit_price - b.discount_price)']);
      $query->from(['a' => ChargeRecord::tableName()]);
      $query->leftJoin(['b' => ChargeInfo::tableName()],'{{a}}.id = {{b}}.charge_record_id');
      $query->where(['b.spot_id' => $spotId]);
      $query->andWhere(['b.type' => $type,'a.status'=>[1]]);
      $query->andWhere(['between', 'a.create_time', $beginTime, $endTime]);
      return $query->all();
      //return ChargeInfo::find()->select(['time'=>'create_time','price' => '(num * unit_price - discount_price)'])->where(['spot_id' => $spotId,'type' => $type,'status'=>[1,2]])->andWhere(['between', 'create_time', $beginTime, $endTime])->asArray()->all();
      } */

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @param $is_today 是否为今天
     * @return mixed 诊金
     * @desc 获取诊金
     */
    public static function getFee($spotId, $beginTime, $endTime, $typeKey = 'diagnosis_price') {
        $actual = 0;
        $actualQuery = ChargeRecordLog::find()
                ->select([
                    'num' => 'SUM(' . $typeKey . ')',
                    'fromTime' => "FROM_UNIXTIME(create_time,'%Y-%m-%d')"
                ])
                ->where(['spot_id' => $spotId, 'type' => 1])->andWhere(['between', 'create_time', $beginTime, $endTime]) //各个医嘱收费之和
                ->groupBy(['fromTime'])
                ->asArray()
                ->indexBy('fromTime')
                ->all();
        $returnQuery = ChargeRecordLog::find()
                ->select([
                    'num' => 'SUM(' . $typeKey . ')',
                    'fromTime' => "FROM_UNIXTIME(create_time,'%Y-%m-%d')"
                ])
                ->where(['spot_id' => $spotId, 'type' => 2])->andWhere(['between', 'create_time', $beginTime, $endTime]) //总收费之和
                ->groupBy(['fromTime'])
                ->asArray()
                ->indexBy('fromTime')
                ->all();
        $actual = self::formatPrice($actualQuery, $returnQuery);
//        $actualArray = self::getNumAndDeciaml($actual);
        return $actual;
    }

    public static function getFee2($spotId, $beginTime, $endTime, $type, $isToday) {
        $actual = 0;
        $recordIds = array();

        $query = new Query();
        $query->select(['a.record_id', 'price' => '(b.num * b.unit_price - b.discount_price- b.card_discount_price)']);
        $query->from(['a' => ChargeRecord::tableName()]);
        $query->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.charge_record_id');
        $query->where(['b.type' => $type, 'b.spot_id' => $spotId]);
        $query->andWhere(['between', 'a.create_time', $beginTime, $endTime]);

        if (!$isToday) {
            $actual1 = $query->andWhere(['a.status' => [1, 4]])->all();
            foreach ($actual1 as $value) {
                $actual += $value['price'];
                array_push($recordIds, $value['record_id']);
            }

            $queryPrice = new Query();
            $queryPrice->select(['sum' => 'SUM((b.num * b.unit_price - b.discount_price - b.card_discount_price))']);
            $queryPrice->from(['a' => ChargeRecord::tableName()]);
            $queryPrice->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.charge_record_id');
            $queryPrice->where(['b.spot_id' => $spotId]);
            $queryPrice->andWhere(['b.type' => $type, 'a.status' => 3, 'a.record_id' => $recordIds]);
            $queryPrice->andWhere(['>', 'a.create_time', $endTime]);
            $actual2 = $queryPrice->one()['sum'];

            if ($actual2) {
                $actual = $actual2 + $actual;
            }
        } else {
            $actual1 = $query->andWhere(['a.status' => 1])->all();
            foreach ($actual1 as $value) {
                $actual += $value['price'];
            }
        }
        $actualArray = self::getNumAndDeciaml($actual);
        return $actualArray['num'] . '.' . $actualArray['decimal'];
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间(Y-m-d H:i:s)
     * @param $endTime 结束时间
     * @return mixed 会员卡销量
     * @desc 获取会员卡销量
     */
    public static function getVipCardSum($spotId, $beginTime, $endTime) {
        $data = CardRecharge::find()
                ->select([
                    'num' => 'COUNT(1)',
                    'fromTime' => "DATE_FORMAT(f_create_time,'%Y-%m-%d')",
                    'f_create_time'
                ])
                ->where(['f_spot_id' => $spotId])
                ->andWhere(['between', 'f_create_time', $beginTime, $endTime])
                ->groupBy(['fromTime'])
                ->asArray()
                ->indexBy('fromTime')
                ->all();
        return $data;
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间(Y-m-d H:i:s)
     * @param $endTime 结束时间
     * @return mixed 会员卡充值金额
     * @desc 获取会员卡充值金额
     */
    public static function getVipCardPrice($spotId, $beginTime, $endTime) {
        $data = CardFlow::find()
                ->select([
                    'num' => 'SUM(f_record_fee)',
                    'fromTime' => "DATE_FORMAT(f_create_time,'%Y-%m-%d')"
                ])
                ->where(['f_record_type' => [1], 'f_spot_id' => $spotId])
                ->andWhere(['between', 'f_create_time', $beginTime, $endTime])
                ->groupBy(['fromTime'])
                ->asArray()
                ->indexBy('fromTime')
                ->all();
        return $data;
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间(Y-m-d H:i:s)
     * @param $endTime 结束时间
     * @return mixed 会员卡充值金额
     * @desc 获取会员卡充值金额
     */
    public static function getVipCardPriceGive($spotId, $beginTime, $endTime) {
        $data = CardFlow::find()
                ->select([
                    'num' => 'SUM(f_record_fee)',
                    'fromTime' => "DATE_FORMAT(f_create_time,'%Y-%m-%d')"
                ])
                ->where(['f_record_type' => [5], 'f_spot_id' => $spotId])
                ->andWhere(['between', 'f_create_time', $beginTime, $endTime])
                ->groupBy(['fromTime'])
                ->asArray()
                ->indexBy('fromTime')
                ->all();
        return $data;
    }
    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间(Y-m-d H:i:s)
     * @param $endTime 结束时间
     * @return mixed 会员卡提现金额
     * @desc 获取会员卡提现金额
     */
    public static function getVipCardPriceCash($spotId, $beginTime, $endTime){
        $data = CardFlow::find()
            ->select([
                'num' => 'SUM(f_record_fee)',
                'fromTime' => "DATE_FORMAT(f_create_time,'%Y-%m-%d')"
            ])
            ->where(['f_record_type' => [3], 'f_spot_id' => $spotId])
            ->andWhere(['between', 'f_create_time', $beginTime, $endTime])
            ->groupBy(['fromTime'])
            ->asArray()
            ->indexBy('fromTime')
            ->all();
        return $data;
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间 时间戳
     * @param $endTime 结束时间 时间戳
     * @return mixed 门诊量
     * @desc 获取门诊量
     */
    public static function getReportNum($spotId, $beginTime, $endTime){
        $data = Report::find()
            ->select([
                'num' => 'COUNT(1)',
                'fromTime' => "FROM_UNIXTIME(create_time,'%Y-%m-%d')"
            ])
            ->where(['spot_id' => $spotId])
            ->andWhere(['between', 'create_time', $beginTime, $endTime])
            ->groupBy(['fromTime'])
            ->asArray()
            ->indexBy('fromTime')
            ->all();
        return $data;
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime
     * @param $endTime
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getReportVipNum($spotId, $beginTime, $endTime){
        $data = Report::find()
            ->select([
                'num' => 'COUNT(1)',
                'fromTime' => "FROM_UNIXTIME(create_time,'%Y-%m-%d')"
            ])
            ->where(['spot_id' => $spotId,'is_vip' => 1])
            ->andWhere(['between', 'create_time', $beginTime, $endTime])
            ->groupBy(['fromTime'])
            ->asArray()
            ->indexBy('fromTime')
            ->all();
        return $data;
    }

    public static function getVipCardNew($spotId, $beginTime, $endTime){
        $data = CardRecharge::find()
            ->select([
                'num' => 'COUNT(1)',
                'fromTime' => "DATE_FORMAT(f_create_time,'%Y-%m-%d')"
            ])
            ->where(['f_spot_id' => $spotId])
            ->andWhere(['between', 'f_create_time', $beginTime, $endTime])
            ->groupBy(['fromTime'])
            ->asArray()
            ->indexBy('fromTime')
            ->all();
        return $data;
    }

    /**
     * @param $spotId 诊所id
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @param $is_today 是否为今天
     * @return mixed 实收金额
     * @desc 获取实收金额
     */
    public static function getActualPrice($spotId, $beginTime, $endTime) {
        $actual = 0;
        $actualQuery = ChargeRecordLog::find()
                ->select([
                    'num' => 'SUM(price)',
                    'fromTime' => "FROM_UNIXTIME(create_time,'%Y-%m-%d')"
                ])
                ->where(['spot_id' => $spotId, 'type' => 1])->andWhere(['between', 'create_time', $beginTime, $endTime]) //总收费之和
                ->groupBy(['fromTime'])
                ->asArray()
                ->indexBy('fromTime')
                ->all();
        $returnQuery = ChargeRecordLog::find()
                ->select([
                    'num' => 'SUM(price)',
                    'fromTime' => "FROM_UNIXTIME(create_time,'%Y-%m-%d')"
                ])
                ->where(['spot_id' => $spotId, 'type' => 2])->andWhere(['between', 'create_time', $beginTime, $endTime]) //总退费之和
                ->groupBy(['fromTime'])
                ->asArray()
                ->indexBy('fromTime')
                ->all();
        $actual = self::formatPrice($actualQuery, $returnQuery);
//        $actualArray = self::getNumAndDeciaml($actual);
        return $actual;
    }

    public static function getActualPrice2($spotId, $beginTime, $endTime, $isToday) {
        $actual = 0;
        $recordIds = array();
        $actualQuery = ChargeRecord::find()->select(['price', 'record_id'])->where(['spot_id' => $spotId])->andWhere(['between', 'create_time', $beginTime, $endTime]);
        if (!$isToday) {
            $actual1 = $actualQuery->andWhere(['status' => [1, 4]])->asArray()->all();
            foreach ($actual1 as $value) {
                $actual += $value['price'];
                array_push($recordIds, $value['record_id']);
            }
            $actual2 = ChargeRecord::find()->select(['sum' => 'SUM(price)'])->where(['spot_id' => $spotId, 'status' => 3, 'record_id' => $recordIds])->andWhere(['>', 'create_time', $endTime])->asArray()->one()['sum'];
            if ($actual2) {
                $actual = $actual2 + $actual;
            }
        } else {
            $actual1 = $actualQuery->andWhere(['status' => 1])->asArray()->all();
            foreach ($actual1 as $value) {
                $actual += $value['price'];
            }
        }
        $actualArray = self::getNumAndDeciaml($actual);
        return $actualArray['num'] . '.' . $actualArray['decimal'];
    }

    /**
     * @param $dividend 被除数
     * @param $divisor 除数
     * @param $is_percent 是否转换为百分数
     * @return array
     * @desc  根据除法分割数字的整数部分和小数部分
     */
    public static function getNumAndDeciamlByDivide($dividend, $divisor, $is_percent = true) {
        if (!($dividend > 0) || !($divisor > 0)) {
            $result = 0;
        } else {
            if ($is_percent) {
                $result = round($dividend / $divisor, 4) * 100;
            } else {
                $result = round($dividend / $divisor, 2);
            }
        }
        $numArray = $result ? Data::getNumAndDeciaml($result) : Data::getNumAndDeciaml('0.00');
        $num = $numArray['num'] . '.' . $numArray['decimal'];
        return $num;
    }

    /**
     * @param $number
     * @return array
     * @desc 分割数字的整数部分和小数部分,没有小数默认为00,不够两位小数默认补0
     */
    public static function getNumAndDeciaml($number) {
        $numArray = explode('.', $number);
        $result['num'] = $numArray[0];
        if (0 == strlen($numArray[1])) {
            $result['decimal'] = '00';
        } else if (1 == strlen($numArray[1])) {
            $result['decimal'] = $numArray[1] . '0';
        } else {
            $result['decimal'] = $numArray[1];
        }
        return $result;
    }

    public static function formatPrice($origin, $target) {
        foreach ($origin as $key => &$val) {
            if (isset($target[$key])) {
                $numArray = self::getNumAndDeciaml(($val['num'] - $target[$key]['num']));
                $val['num'] = $numArray['num'] . '.' . $numArray['decimal'];
            }
        }
        return $origin;
    }

}
