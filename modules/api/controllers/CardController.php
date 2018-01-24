<?php

namespace app\modules\api\controllers;

/*
 * time: 2016-8-30 16:23:20.
 * author : yu.li.
 */

use Yii;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use app\modules\wechat\models\Wechat;
use app\modules\spot\models\CardManage;
use app\modules\spot\models\VirtualCardManage;

class CardController extends BaseOutController
{

    public $enableCsrfValidation = false;

    public function behaviors() {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['post'],
//                    'add-scheduling' => ['post']
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    /**
     * card-id-list
     * @param string $card_physical_id 服务卡物理ID
     * 
     * @return string card_info 服务卡的基本信息
     * @desc 根据服务卡id获取服务卡的基本信息
     */
    public function actionCardIdList() {
        $card_physical_id = Yii::$app->request->post('card_physical_id');
        $card_info = '';
        if ($card_physical_id) {
//            $query = new \yii\db\Query();
//            $info = $query->from(['a' => 't_card_manage'])->where(['f_physical_id' => $card_physical_id])->all();
            $info = CardManage::find()->where(['f_physical_id' => $card_physical_id])->asArray()->all();
            if ($info) {
                foreach ($info as $key => $val) {
                    $card_info[$val['f_physical_id']] = $val;
                }
            }
            $card_info = json_encode($card_info);
        }
        return $card_info;
    }

    /**
     * card-sn-list
     * @param string $f_card_id 服务卡卡号
     * 
     * @return string card_info 服务卡的基本信息
     * @desc 根据服务卡卡号获取服务卡的基本信息
     */
    public function actionCardSnList() {
        $f_card_id = Yii::$app->request->post('f_card_id');
        \Yii::info('f_card_id:[' . var_export($f_card_id, true));
        $card_info = '';
        $entityInfo = [];
        $virtualInfo = [];
        if ($f_card_id) {
            foreach ($f_card_id as $card_id) {
                if ($card_id['card_type'] == 1) {
                    $entityCard[] = $card_id['card_id'];
                } else {
                    $virtualCard[] = $card_id['card_id'];
                }
            }
//            $query = new \yii\db\Query();
//            $info = $query->from(['a' => 't_card_manage'])->where(['f_card_id' => $f_card_id])->all();
            if (!empty($entityCard)) { //实体卡
                $entityInfo = CardManage::find()->where(['f_card_id' => $entityCard])->asArray()->all();
            }
            if (!empty($virtualCard)) {
                $virtualInfo = VirtualCardManage::find()->where(['f_card_id' => $virtualCard])->asArray()->all();
            }
            if (empty($entityInfo)) {
                $info = $virtualInfo;
            } elseif (empty($virtualInfo)) {
                $info = $entityInfo;
            } else {
                $info = array_merge($entityInfo, $virtualInfo);
            }
            if (!empty($info)) {
                foreach ($info as $key => $val) {
                    $card_info[$val['f_card_id']] = $val;
                }
                $card_info = json_encode($card_info);
            }
        }
        return $card_info;
    }

    /**
     * activate-card
     * @param string $f_card_id 服务卡卡号
     * @param string $card_type 服务卡类型（1/实体卡 2/虚拟卡）
     * 
     * @return string res 错误代码 0成功 1失败
     * @desc 激活会员卡
     */
    public function actionActivateCard() {
        $f_card_id = Yii::$app->request->post('f_card_id');
        $card_type = Yii::$app->request->post('card_type');
        $res = ['res' => 1];
        if ($f_card_id) {
//            $db = Yii::$app->db;
            $db = Yii::$app->getDb('cardCenter');
            $now = time();
            if ($card_type == 1) {
                $res = CardManage::updateAll(['f_status' => 1, 'f_activate_time' => $now, 'f_invalid_time' => $now + 180 * 24 * 60 * 60], ['f_card_id' => $f_card_id]);
            } else {
                $res = VirtualCardManage::updateAll(['f_status' => 1, 'f_activate_time' => $now, 'f_invalid_time' => $now + 180 * 24 * 60 * 60], ['f_card_id' => $f_card_id]);
            }
            if ($res !== false) {
                $res['res'] = 0;
            }
        }
        return json_encode($res);
    }

    /**
     * card-sn-info
     * @param string $f_card_id 服务卡卡号
     * 
     * @return string card_info 服务卡的基本信息
     * @desc 根据服务卡卡号获取服务卡的基本信息
     */
    public function actionCardSnInfo() {
        $f_card_id = Yii::$app->request->post('f_card_id');
        $cardInfo = CardManage::find()->where(['f_card_id' => $f_card_id])->asArray()->one();
        if (empty($cardInfo)) {
            //再查虚拟卡
            $cardInfo = VirtualCardManage::find()->where(['f_card_id' => $f_card_id])->asArray()->one();
            $cardInfo && $cardInfo['card_type'] = 2;
        } else {
            $cardInfo['card_type'] = 1;
        }
        return json_encode($cardInfo);
    }

}
