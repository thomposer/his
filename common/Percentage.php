<?php

/*
 * time: 2017-2-21 18:01:02.
 * author : yu.li.
 * 标准值 百分率算法类
 */

namespace app\common;

use Yii;
use app\modules\growth\models\ZscoreBmi;
use app\modules\growth\models\ZscoreHeadCircumference;
use app\modules\growth\models\ZscoreHeight;
use app\modules\growth\models\ZscoreWeight;
use app\modules\growth\models\ZpscoreComparison;

class Percentage
{

    public static $lt;
    public static $mt;
    public static $st;

    /**
     * 
     * @param type $data 输入数据（身高/体重/头围/BMI）
     * @param type $sex 性别（1/男 2/女）
     * @param type $type Z值的类型（1/身高 2/体重 3/头围 4/BMI）
     * @param type $before 出生日期
     * @param type $after 测量日期
     * @return type 获取Z值
     */
    public static function getZScore($data, $sex, $type, $before, $after = 1) {
        if (!self::$lt) {
            self::setProperty($data, $sex, $type, $before, $after);
        }
        if (!self::$lt) {
            return '';
        }
        $zind = (pow($data / self::$mt, self::$lt) - 1) / (self::$st * self::$lt);
        if ($zind >= -3 && $zind <= 3) {
            $zpointer = $zind;
        }
        if ($zind > 3) {// 获取正值z
            $zpointer = self::getZpointPositive($data);
        }
        if ($zind < -3) {// 获取负值z
            $zpointer = self::getZpointNegative($data);
        }
        return sprintf('%.2f', $zpointer);
    }

    /**
     * 
     * @param type $data 输入数据（身高/体重/头围/BMI）
     * @param type $sex 性别（1/男 2/女）
     * @param type $type 百分率的类型（1/身高 2/体重 3/头围 4/BMI）
     * @param type $before 出生日期
     * @param type $after 测量日期
     * @return type 获取百分率
     */
    public static function getPercentage($data, $sex, $type, $before, $after = 1) {
        self::setProperty($data, $sex, $type, $before, $after);
        if (!self::$lt) {
            return '';
        }
        $zscore = self::getZScore($data, $sex, $type, $before, $after);
        $percentage = '';
        if ($zscore >= -4.09 && $zscore <= 4.09) {//在此范围内才有p值
            $pscore = self::getPscore($zscore);
            if ($zscore >= 0) {//正值
                $percentage = (0.5 + $pscore) * 100;
            } else {
                $percentage = (0.5 - $pscore) * 100;
            }
        }
        return $percentage;
    }

    /**
     * 
     * @param type $zscore
     * @return type 根据z值获取百分率 
     */
    protected static function getPscore($zscore) {
        $score = abs($zscore);
        $res = ZpscoreComparison::find()->where(['zscore' => $score])->asArray()->one();
        return !empty($res) ? $res['pscore'] : 0;
    }

    public static function setProperty($data, $sex, $type, $before, $after = 1) {
        $grouthCurce = self::getGrouthCurve($data, $sex, $type, $before, $after);
        self::$lt = $grouthCurce['lt'];
        self::$mt = $grouthCurce['mt'];
        self::$st = $grouthCurce['st'];
    }

    private static function getZpointPositive($data) {
        $sd3pos = self::$mt * pow((1 + self::$lt * self::$st * 3), 1 / self::$lt);
        $sd23pos = self::$mt * pow(1 + self::$lt * self::$st * 3, 1 / self::$lt) - self::$mt * pow(1 + self::$lt * self::$st * 2, 1 / self::$lt);
        $intermediate = ($data - $sd3pos) / $sd23pos;
        return 3 + $intermediate;
    }

    private static function getZpointNegative($data) {
        $sd3neg = self::$mt * pow(1 + self::$lt * self::$st * -3, 1 / self::$lt);
        $sd23neg = self::$mt * pow(1 + self::$lt * self::$st * -2, 1 / self::$lt) - self::$mt * pow(1 + self::$lt * self::$st * -3, 1 / self::$lt);
        $intermediate = ($data - $sd3neg) / $sd23neg;
        return -3 + $intermediate;
    }

    /**
     * 
     * @param type $data
     * @param type $type 百分率的类型（1/身高 2/体重 3/头围 4/BMI）
     */
    private static function getGrouthCurve($data, $sex, $type = 1, $before, $after = 1) {
        if ($type == 1) {
            $query = ZscoreHeight::find();
        } elseif ($type == 2) {
            $query = ZscoreWeight::find();
        } elseif ($type == 3) {
            $query = ZscoreHeadCircumference::find();
        } elseif ($type == 4) {
            $query = ZscoreBmi::find();
        }
        $where = [
            'sex' => $sex
        ];
        $age = self::getAge($before, $after);
        if ($age['month']) {//月份
            $where['age_type'] = 2;
            $where['age'] = $age['month'];
        } else {//天数
            $where['age_type'] = 1;
            $where['age'] = $age['day'];
        }
        $growthCurve = $query->select(['lt', 'mt', 'st'])->where($where)->asArray()->one();
        return $growthCurve;
    }

    /*
     * ==============================
     * 此方法由 mantye 提供
     * http://my.oschina.net/u/223350
     * @date 2014-07-22
     * ==============================
     * @description    取得两个时间戳相差的年，月，日，小时
     * @before         较小的时间戳
     * @after          较大的时间戳
     * @return str     返回相差的年，月，日

     * */

    public static function getAge($before, $after = 1) {
        if ($after == 1) {//默认为当前时间
            $after = time();
        }
        if ($before == 0) {
            return '';
        }
        if ($before > $after) {
            $b = getdate($after);
            $a = getdate($before);
            list($before, $after) = [$after, $before];
        } else {
            $b = getdate($before);
            $a = getdate($after);
        }
        $n = array(1 => 31, 2 => 28, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31);
        $y = $m = $d = 0;
        if ($a['mday'] >= $b['mday']) { //天相减为正
            if ($a['mon'] >= $b['mon']) {//月相减为正
                $y = $a['year'] - $b['year'];
                $m = $a['mon'] - $b['mon'];
            } else { //月相减为负，借年
                $y = $a['year'] - $b['year'] - 1;
                $m = $a['mon'] - $b['mon'] + 12;
            }
            $d = $a['mday'] - $b['mday'];
        } else {  //天相减为负，借月
            if ($a['mon'] == 1) { //1月，借年
                $y = $a['year'] - $b['year'] - 1;
                $m = $a['mon'] - $b['mon'] + 12 - 1;
                $d = $a['mday'] - $b['mday'] + $n[12];
            } else {
                if ($a['mon'] == 3) { //3月，判断闰年取得2月天数
                    $d = $a['mday'] - $b['mday'] + ($a['year'] % 4 == 0 ? 29 : 28);
                } else {
                    $d = $a['mday'] - $b['mday'] + $n[$a['mon'] - 1];
                }
                if ($a['mon'] >= $b['mon'] + 1) { //借月后，月相减为正
                    $y = $a['year'] - $b['year'];
                    $m = $a['mon'] - $b['mon'] - 1;
                } else { //借月后，月相减为负，借年
                    $y = $a['year'] - $b['year'] - 1;
                    $m = $a['mon'] - $b['mon'] + 12 - 1;
                }
            }
        }
        $ret = [
            'month' => 0,
            'day' => 0
        ];
        $common = $after - $before;
//        if (($y < 5) || ($y == 5 && ($m == 1 || $m == 0))) {//5岁及5岁零一个月取天数
        if (($y < 5) || ($y == 5 && $m == 0)) {//5岁及5岁零一个月取天数
            $ret['day'] = floor($common / 86400); //天数
        } else {//取的距离当前的月数
            $ret['month'] = $y * 12 + $m;
        }
        return $ret;
    }

}
