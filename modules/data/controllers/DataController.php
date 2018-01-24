<?php

namespace app\modules\data\controllers;

use app\modules\data\models\search\DataSearch;
use Yii;
use app\common\base\BaseController;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use app\modules\data\models\Data;

/**
 * ReportFormsController implements the CRUD actions for ReportForms model.
 */
class DataController extends BaseController
{

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

    /**
     * Lists all ReportForms models.
     * @return mixed
     */
    public function actionIndex() {

        $searchModel = new DataSearch();
        $searchModel->load(Yii::$app->request->post());

        $beginEndTime = $this->getBeginEndTime($searchModel);
        $allModels = $beginEndTime['allModels'];
        $beginTime = strtotime($beginEndTime['dateBegin']);
        $endTime = strtotime($beginEndTime['dateEnd'] . ' 24:00:00');
        $reservationNumber = Data::getReservationNumber($this->spotId, $beginTime, $endTime);
        $allModels = self::formatDataByDate($reservationNumber, $allModels, 'reservationNumber'); // 预约人数
        $actualNumber = Data::getActualNumber($this->spotId, $beginTime, $endTime);
        $allModels = self::formatDataByDate($actualNumber, $allModels, 'actualNumber'); // 实到人数
        $paymentNumber = Data::getPaymentNumber($this->spotId, $beginTime, $endTime);
        $allModels = self::formatDataByDate($paymentNumber, $allModels, 'paymentNumber'); // 付款人数
//        $fee = Data::getFee($this->spotId,$beginTime,$endTime,5);
//        $allModels = self::setDataByDate($fee,$allModels,'fee',false);  // 诊金
//        $labFee = Data::getFee($this->spotId,$beginTime,$endTime,1);
//        $allModels = self::setDataByDate($labFee,$allModels,'labFee',false); // 实验室检查费用
//        $iconographyFee = Data::getFee($this->spotId,$beginTime,$endTime,2);
//        $allModels = self::setDataByDate($iconographyFee,$allModels,'iconographyFee',false); // 影像学检查费用
//        $cureFee = Data::getFee($this->spotId,$beginTime,$endTime,3);
//        $allModels = self::setDataByDate($cureFee,$allModels,'cureFee',false); // 治疗费用
//        $recipeFee = Data::getFee($this->spotId,$beginTime,$endTime,4);
//        $allModels = self::setDataByDate($recipeFee,$allModels,'recipeFee',false); // 处方费用

        $vipCardSum = Data::getVipCardSum($this->spotId, date('Y-m-d H:i:s', $beginTime), date('Y-m-d H:i:s', $endTime));
        $allModels = self::formatDataByDate($vipCardSum, $allModels, 'vipCardSum'); // 会员卡销量
        $vipCardPrice = Data::getVipCardPrice($this->spotId, date('Y-m-d H:i:s', $beginTime), date('Y-m-d H:i:s', $endTime));
        $allModels = self::formatDataByDate($vipCardPrice, $allModels, 'vipCardPrice'); // 会员卡充值金额

        $vipCardPriceGive = Data::getVipCardPriceGive($this->spotId, date('Y-m-d H:i:s', $beginTime), date('Y-m-d H:i:s', $endTime));
        $allModels = self::formatDataByDate($vipCardPriceGive, $allModels, 'vipCardPriceGive'); // 会员卡赠送金额

        $receivablePrice = Data::getReceivablePrice2($this->spotId, $beginTime, $endTime);
        $allModels = self::setDataByDate($receivablePrice, $allModels, 'receivablePrice', false); // 就诊应收金额
        $favourablePrice = Data::getFavourablePrice2($this->spotId, $beginTime, $endTime);
        $allModels = self::setDataByDate($favourablePrice, $allModels, 'favourablePrice', false); // 就诊优惠金额

        $returnPrice = Data::getReturnPrice2($this->spotId, $beginTime, $endTime);
        $allModels = self::setDataByDate($returnPrice, $allModels, 'returnPrice', false); // 就诊退费金额

        foreach ($allModels as $key => $val) {
            $begTime = strtotime($allModels[$key]['date']);
            $endTime = strtotime($allModels[$key + 1]['date'] . ' 24:00:00');
            $allModels[$key]['actualPrice'] = Data::getActualPrice2($this->spotId, $begTime, $endTime, date('Y-m-d', strtotime($allModels[$key]['date'])) === date('Y-m-d')); // 就诊实收金额
            $allModels[$key]['perPrice'] = Data::getNumAndDeciamlByDivide($allModels[$key]['actualPrice'], $allModels[$key]['paymentNumber'], false); // 客单价
            $allModels[$key]['consumptionProportion'] = Data::getNumAndDeciamlByDivide($allModels[$key]['actualPrice'], $allModels[$key]['vipCardPrice']); // 消费占比
            $allModels[$key]['marketEfficiency'] = Data::getNumAndDeciamlByDivide($allModels[$key]['paymentNumber'], $allModels[$key]['reservationNumber']); // 市场效率
            $allModels[$key]['marketingEfficiency'] = Data::getNumAndDeciamlByDivide($allModels[$key]['vipCardSum'], $allModels[$key]['paymentNumber']); // 销售效率

            $allModels[$key]['fee'] = Data::getFee2($this->spotId, $begTime, $endTime, 5, date('Y-m-d', strtotime($allModels[$key]['date'])) === date('Y-m-d'));
            $allModels[$key]['labFee'] = Data::getFee2($this->spotId, $begTime, $endTime, 1, date('Y-m-d', strtotime($allModels[$key]['date'])) === date('Y-m-d'));
            $allModels[$key]['iconographyFee'] = Data::getFee2($this->spotId, $begTime, $endTime, 2, date('Y-m-d', strtotime($allModels[$key]['date'])) === date('Y-m-d'));
            $allModels[$key]['cureFee'] = Data::getFee2($this->spotId, $begTime, $endTime, 3, date('Y-m-d', strtotime($allModels[$key]['date'])) === date('Y-m-d'));
            $allModels[$key]['recipeFee'] = Data::getFee2($this->spotId, $begTime, $endTime, 4, date('Y-m-d', strtotime($allModels[$key]['date'])) === date('Y-m-d'));
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $allModels,
            'pagination' => false,
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'dateBegin' => $beginEndTime['dateBegin'],
                    'dateEnd' => $beginEndTime['dateEnd'],
                    'searchModel' => $searchModel
        ]);
    }

//    public function actionIndex2() {
//
//        $searchModel = new DataSearch();
//        $searchModel->load(Yii::$app->request->post());
//
//        $beginEndTime = $this->getBeginEndTime($searchModel);
//        $allModels = $beginEndTime['allModels'];
//        $beginTime = $beginEndTime['dateBegin'];
//        $endTime = $beginEndTime['dateEnd'];
//        $reservationNumber = Data::getReservationNumber($this->spotId, $beginTime, $endTime);
//        $allModels = self::setDataByDate($reservationNumber, $allModels, 'reservationNumber'); // 预约人数
//
//        $actualNumber = Data::getActualNumber($this->spotId, $beginTime, $endTime);
//        $allModels = self::setDataByDate($actualNumber, $allModels, 'actualNumber'); // 实到人数
//
//        $paymentNumber = Data::getPaymentNumber($this->spotId, $beginTime, $endTime);
//        $allModels = self::setPaymentNumberData($paymentNumber, $allModels); // 付款人数
////        $fee = Data::getFee($this->spotId,$beginTime,$endTime,5);
////        $allModels = self::setDataByDate($fee,$allModels,'fee',false);  // 诊金
////        $labFee = Data::getFee($this->spotId,$beginTime,$endTime,1);
////        $allModels = self::setDataByDate($labFee,$allModels,'labFee',false); // 实验室检查费用
////        $iconographyFee = Data::getFee($this->spotId,$beginTime,$endTime,2);
////        $allModels = self::setDataByDate($iconographyFee,$allModels,'iconographyFee',false); // 影像学检查费用
////        $cureFee = Data::getFee($this->spotId,$beginTime,$endTime,3);
////        $allModels = self::setDataByDate($cureFee,$allModels,'cureFee',false); // 治疗费用
////        $recipeFee = Data::getFee($this->spotId,$beginTime,$endTime,4);
////        $allModels = self::setDataByDate($recipeFee,$allModels,'recipeFee',false); // 处方费用
//
//        $vipCardSum = Data::getVipCardSum($this->spotId, $allModels[0]['date'], $allModels[count($allModels) - 1]['date']);
//        $allModels = self::setDataByDate($vipCardSum, $allModels, 'vipCardSum'); // 会员卡销量
//
//        $vipCardPrice = Data::getVipCardPrice($this->spotId, $allModels[0]['date'], $allModels[count($allModels) - 1]['date']);
//        $allModels = self::setDataByDate($vipCardPrice, $allModels, 'vipCardPrice', false); // 会员卡充值金额
//
//        $vipCardPriceGive = Data::getVipCardPriceGive($this->spotId, $allModels[0]['date'], $allModels[count($allModels) - 1]['date']);
//        $allModels = self::setDataByDate($vipCardPriceGive, $allModels, 'vipCardPriceGive', false); // 会员卡赠送金额
//
//        $receivablePrice = Data::getReceivablePrice2($this->spotId, $beginTime, $endTime);
//        $allModels = self::setDataByDate($receivablePrice, $allModels, 'receivablePrice', false); // 就诊应收金额
//
//        $favourablePrice = Data::getFavourablePrice2($this->spotId, $beginTime, $endTime);
//        $allModels = self::setDataByDate($favourablePrice, $allModels, 'favourablePrice', false); // 就诊优惠金额
//
//        $returnPrice = Data::getReturnPrice2($this->spotId, $beginTime, $endTime);
//        $allModels = self::setDataByDate($returnPrice, $allModels, 'returnPrice', false); // 就诊退费金额
//
//        foreach ($allModels as $key => $val) {
//            $allModels[$key]['actualPrice'] = Data::getActualPrice($this->spotId, strtotime($allModels[$key]['date']), strtotime($allModels[$key + 1]['date']), date('Y-m-d', strtotime($allModels[$key]['date'])) === date('Y-m-d')); // 就诊实收金额
//            $allModels[$key]['perPrice'] = Data::getNumAndDeciamlByDivide($allModels[$key]['actualPrice'], $allModels[$key]['paymentNumber'], false); // 客单价
//            $allModels[$key]['consumptionProportion'] = Data::getNumAndDeciamlByDivide($allModels[$key]['actualPrice'], $allModels[$key]['vipCardPrice']); // 消费占比
//            $allModels[$key]['marketEfficiency'] = Data::getNumAndDeciamlByDivide($allModels[$key]['paymentNumber'], $allModels[$key]['reservationNumber']); // 市场效率
//            $allModels[$key]['marketingEfficiency'] = Data::getNumAndDeciamlByDivide($allModels[$key]['vipCardSum'], $allModels[$key]['paymentNumber']); // 销售效率
//
//            $allModels[$key]['fee'] = Data::getFee($this->spotId, strtotime($allModels[$key]['date']), strtotime($allModels[$key + 1]['date']), 5, date('Y-m-d', strtotime($allModels[$key]['date'])) === date('Y-m-d'));
//            $allModels[$key]['labFee'] = Data::getFee($this->spotId, strtotime($allModels[$key]['date']), strtotime($allModels[$key + 1]['date']), 1, date('Y-m-d', strtotime($allModels[$key]['date'])) === date('Y-m-d'));
//            $allModels[$key]['iconographyFee'] = Data::getFee($this->spotId, strtotime($allModels[$key]['date']), strtotime($allModels[$key + 1]['date']), 2, date('Y-m-d', strtotime($allModels[$key]['date'])) === date('Y-m-d'));
//            $allModels[$key]['cureFee'] = Data::getFee($this->spotId, strtotime($allModels[$key]['date']), strtotime($allModels[$key + 1]['date']), 3, date('Y-m-d', strtotime($allModels[$key]['date'])) === date('Y-m-d'));
//            $allModels[$key]['recipeFee'] = Data::getFee($this->spotId, strtotime($allModels[$key]['date']), strtotime($allModels[$key + 1]['date']), 4, date('Y-m-d', strtotime($allModels[$key]['date'])) === date('Y-m-d'));
//        }
//
//        unset($allModels[count($allModels) - 1]);
//
////        array_unshift($allModels,['date'=>'日期','reservationNumber' => '预约人数','actualNumber'=>'接诊人数','paymentNumber'=>'付款人数','fee'=>'诊金','labFee'=>'实验室检查费用','iconographyFee'=>'影像学检查费用','cureFee'=>'治疗费用','recipeFee'=>'处方费用','actualPrice'=>'就诊实收金额','perPrice'=>'客单价','vipCardSum'=>'会员卡销量','vipCardPrice'=>'会员卡充值金额','vipCardPriceGive'=>'会员卡赠送金额','consumptionProportion'=>'消费占比','marketEfficiency'=>'市场效率','marketingEfficiency'=>'销售效率']);
//
//        $dataProvider = new ArrayDataProvider([
//            'allModels' => $allModels,
//            'pagination' => false,
//        ]);
//
//        return $this->render('index', [
//                    'dataProvider' => $dataProvider,
//                    'dateBegin' => $dateBegin,
//                    'dateEnd' => $dateEnd,
//                    'searchModel' => $searchModel
//        ]);
//    }

    protected function formatDataByDate($dataArray, $dateArray, $dataKey) {
        foreach ($dateArray as &$val) {
            if (isset($dataArray[$val['date']])) {
                $val["$dataKey"] = $dataArray[$val['date']]['num'];
            } else {
                $val["$dataKey"] = 0;
            }
        }
        return $dateArray;
    }

    /**
     * @param $dataArray 数据数组
     * @param $dateArray 设置值的数组
     * @param $dataKey 数组的key值
     * @param bool $isCount  是否count
     * @return mixed  设置好值的数组
     * @desc  给数组设置值
     */
    public static function setDataByDate($dataArray, $dateArray, $dataKey, $isCount = true) {
        foreach ($dataArray as $data) {
            foreach ($dateArray as $key => $val) {
                if ($data['time'] < strtotime($dateArray[$key + 1]['date'])) {
                    if ($isCount) {
                        $dateArray[$key]["$dataKey"] += 1;
                    } else {
                        $dateArray[$key]["$dataKey"] += $data['price'];
                        $numArray = Data::getNumAndDeciaml($dateArray[$key]["$dataKey"]);
                        $dateArray[$key]["$dataKey"] = $numArray['num'] . '.' . $numArray['decimal'];
                    }
                    break;
                }
            }
        }
        return $dateArray;
    }

    /**
     * @param $dataArray 数据数组
     * @param $dateArray 设置值的数组
     * @return mixed 设置好值的数组
     * @desc 设置付款人数
     */
    public static function setPaymentNumberData($dataArray, $dateArray) {  //  付款人数
        $recordIdArray = array();
        foreach ($dataArray as $data) {

            foreach ($dateArray as $key => $val) {

                if (empty($recordIdArray[$key]["record_id"])) {
                    $recordIdArray[$key]["record_id"] = array();
                }

                if ($data['time'] < strtotime($dateArray[$key + 1]['date'])) {
                    if (!in_array($data['record_id'], $recordIdArray[$key]["record_id"])) {
                        $dateArray[$key]["paymentNumber"] += 1;
                        array_push($recordIdArray[$key]["record_id"], $data['record_id']);
                    }
                    break;
                }
            }
        }
        return $dateArray;
    }

    /**
     * @param $searchModel
     * @return 返回开始和结束时间
     */
    protected function getBeginEndTime($searchModel){
        $dateBegin = $searchModel->beginTime ? $searchModel->beginTime : date('Y-m-d', strtotime("-1 day"));
        $dateEnd = $searchModel->endTime ? $searchModel->endTime: date('Y-m-d',strtotime("-1 day"));
        $allModels = array();
        $beg = strtotime($dateBegin);
        $end = strtotime($dateEnd." 24:00:00");
        $i = 0;
        while ($beg < $end) {
            $allModels[$i]['date'] = date('Y-m-d', $beg);
            $beg = $beg + 86400;
            $i++;
        }
        $time['dateBegin'] = $dateBegin;
        $time['dateEnd'] = $dateEnd;
        $time['allModels'] = $allModels;
        return $time;
    }

    /**
     * 报表--充值卡
     * @return string
     */
    public function actionRecharge(){
        $searchModel = new DataSearch();
        $searchModel->load(Yii::$app->request->post());
        $time = $this->getBeginEndTime($searchModel);
        $dateBegin = $time['dateBegin'];
        $dateEnd = $time['dateEnd'];
        $allModels = $time['allModels'];
        $beginTime = strtotime($dateBegin);
        $endTime = strtotime($dateEnd . ' 24:00:00');
        //获取报表数据
        $vipCardPrice = Data::getVipCardPrice($this->spotId, date('Y-m-d H:i:s', $beginTime), date('Y-m-d H:i:s', $endTime));
        $allModels = self::formatDataByDate($vipCardPrice, $allModels, 'vipCardPrice', false); // 会员卡充值金额
        $vipCardPriceGive = Data::getVipCardPriceGive($this->spotId, date('Y-m-d H:i:s', $beginTime), date('Y-m-d H:i:s', $endTime));
        $allModels = self::formatDataByDate($vipCardPriceGive, $allModels, 'vipCardPriceGive', false); // 会员卡赠送金额
        $vipCardPriceGive = Data::getVipCardPriceCash($this->spotId, date('Y-m-d H:i:s', $beginTime), date('Y-m-d H:i:s', $endTime));
        $allModels = self::formatDataByDate($vipCardPriceGive, $allModels, 'vipCardPriceCash', false); // 会员卡赠送金额
        $reportNum = Data::getReportNum($this->spotId, $beginTime, $endTime);
        $allModels = self::formatDataByDate($reportNum, $allModels, 'reportNum', false); // 门诊量
        $reportVipNum = Data::getReportVipNum($this->spotId, $beginTime, $endTime);
        $allModels = self::formatDataByDate($reportVipNum, $allModels, 'reportVipNum', false); // 会员门诊量
        $vipCardNew = Data::getVipCardNew($this->spotId, date('Y-m-d H:i:s', $beginTime), date('Y-m-d H:i:s', $endTime));
        $allModels = self::formatDataByDate($vipCardNew, $allModels, 'getVipCardNew', false); // 开卡人次
        $dataProvider = new ArrayDataProvider([
            'allModels' => $allModels,
            'pagination' => false,
        ]);

        return $this->render('recharge', [
            'dataProvider' => $dataProvider,
            'dateBegin' => $dateBegin,
            'dateEnd' => $dateEnd,
            'searchModel' => $searchModel
        ]);
    }


}
