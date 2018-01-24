<?php

namespace app\modules\api\controllers;

/*
 * time: 2016-8-30 16:23:20.
 * author : yu.li.
 */

use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use app\modules\charge\models\Wechat;

class CallbackController extends Controller
{

    public $enableCsrfValidation = false;

    public function behaviors() {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'notify' => ['post'],
                    'recharge-notify' => ['post']
//                    'add-scheduling' => ['post']
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    /**
     * notify
     * 
     * @desc 微信支付回调入口
     */
    public function actionNotify() {
        Yii::info('callback success', 'callback');
        $wechat = new Wechat();
        return $wechat->Handle(false);
    }

    /**
     * recharge-notify
     * 
     * @desc 充值卡微信充值-支付回调入口
     */
    public function actionRechargeNotify() {
        Yii::info('recharge-notify callback success', 'callback');
        $wechat = new \app\specialModules\recharge\models\Wechat();
        return $wechat->Handle(false);
    }

    /**
     * package-card-notify
     * 
     * @desc 充值卡微信充值-支付回调入口
     */
    public function actionPackageCardNotify() {
        Yii::info('package-card-notify callback success', 'callback');
        $wechat = new \app\specialModules\recharge\models\PackageWechat();
        return $wechat->Handle(false);
    }

}
