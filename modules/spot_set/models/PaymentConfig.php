<?php

namespace app\modules\spot_set\models;

use Yii;
use yii\db\Query;
use app\modules\charge\models\Order;
use app\specialModules\recharge\models\Order as RechargeOrder;
use app\specialModules\recharge\models\CardOrder;

/**
 * This is the model class for table "{{%payment_config}}".
 *
 * @property string $id
 * @property integer $spot_id
 * @property string $appid
 * @property string $mchid
 * @property string $payment_key
 * @property integer $type
 * @property integer $create_time
 * @property integer $update_time
 */
class PaymentConfig extends \app\common\base\BaseActiveRecord
{

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%payment_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'type', 'create_time', 'update_time'], 'integer'],
            [['appid'], 'string', 'max' => 50],
            [['appid', 'mchid', 'payment_key'], 'required'],
            [['payment_key'], 'string', 'max' => 255],
            ['mchid', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'appid' => '绑定支付的APPID',
            'mchid' => '商户号',
            'payment_key' => '商户支付密钥',
            'type' => '支付类型[1 微信支付 2支付宝]',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * 
     * @param 订单号 $out_trade_no
     * @param 支付类型(1-微信，2-支付宝) $type
     * @return 根据订单号以及支付类型来获取诊所支付配置信息
     */
    public static function getConfig($out_trade_no, $type) {
        $query = new Query();
        $query->from(['a' => Order::tableName()]);
        $query->select(['a.out_trade_no', 'b.appid', 'b.mchid', 'b.payment_key', 'b.spot_id']);
        $query->leftJoin(['b' => self::tableName()], '{{a}}.spot_id = {{b}}.spot_id');
        $query->where(['a.out_trade_no' => $out_trade_no, 'b.type' => $type]);
        return $query->one();
    }

    /**
     * 
     * @param 订单号 $out_trade_no
     * @param 支付类型(1-微信，2-支付宝) $type
     * @return 根据订单号以及支付类型来获取诊所支付配置信息
     */
    public static function getConfigByRecharge($out_trade_no, $type) {
        $spotInfo = RechargeOrder::find()->select(['out_trade_no', 'spot_id'])->where(['out_trade_no' => $out_trade_no])->asArray()->one();
        $spotId = $spotInfo ? $spotInfo['spot_id'] : 0;
        $payConfig = self::find()->select(['appid', 'mchid', 'payment_key', 'spot_id'])->where(['spot_id' => $spotId, 'type' => $type])->asArray()->one();
        return $payConfig;
    }

    /**
     * 
     * @param 订单号 $out_trade_no
     * @param 支付类型(1-微信，2-支付宝) $type
     * @return 根据订单号以及支付类型来获取诊所支付配置信息
     */
    public static function getConfigByPackage($out_trade_no, $type) {
        $spotInfo = CardOrder::find()->select(['out_trade_no', 'spot_id'])->where(['out_trade_no' => $out_trade_no])->asArray()->one();
        $spotId = $spotInfo ? $spotInfo['spot_id'] : 0;
        $payConfig = self::find()->select(['appid', 'mchid', 'payment_key', 'spot_id'])->where(['spot_id' => $spotId, 'type' => $type])->asArray()->one();
        return $payConfig;
    }

    /**
     * 
     * @param 支付类型(1-微信，2-支付宝) $type
     * @return 返回相应类型的支付配置(默认返回微信以及支付宝的配置)
     */
    public static function getPaymentConfigList($type = NULL) {
        return self::find()->select(['appid', 'mchid', 'payment_key', 'type'])->where(['spot_id' => self::$staticSpotId])->andFilterWhere(['type' => $type])->indexBy('type')->asArray()->all();
    }

}
