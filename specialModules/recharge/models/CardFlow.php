<?php

namespace app\specialModules\recharge\models;

use Yii;
use app\specialModules\recharge\models\CardRecharge;
use Exception;
use yii\db\Query;
use app\common\Common;

/**
 * This is the model class for table "{{%card_flow}}".
 *
 * @property string $f_physical_id
 * @property string $f_record_id
 * @property string $f_update_beg
 * @property string $f_update_end
 * @property string $f_user_id
 * @property string $f_user_name
 * @property integer $f_state
 * @property string $f_property
 * @property integer $f_record_type 交易类型
 * @property integer $f_operate_origin 操作渠道
 * @property string $f_create_time
 * @property string $f_update_time
 * @property integer $f_charge_record_id 收费记录id
 * @property integer $f_charge_record_log_id 收费交易流水id
 * @property integer $f_sale_id 健康顾问id
 * @property integer $f_record_fee_coefficient
 * @property integer $f_consum_donation_coefficient
 * @property integer $f_channel_source 来源渠道
 */
class CardFlow extends \yii\db\ActiveRecord
{

    public $payType;
    public $donationFee; //赠送金额
    public $isDonation = 0; //默认 不赠送
    public $isUpgrade = 2; //是否升级 默认不升级
    public $orderSn;
    public $upgradeCheck;
    public $wechatAuthCode; //微信授权码
    public $alipayAuthCode; //支付宝授权码
    public $scanMode = 2;
    public $isEmpty = 0;
    public $returnDonation = 0;
    public $oldAmount;
    public $oldDonation;
    public $isUpgradeRecord = 2;

    public function init() {
        parent::init();
        $this->f_spot_id = isset($_COOKIE['spotId']) ? $_COOKIE['spotId'] : 0;
    }

    public function behaviors() {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%card_flow}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('cardCenter');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['f_record_fee', 'f_spot_id'], 'required'],
            [['f_record_id', 'f_user_id', 'f_record_type', 'f_state', 'f_pay_type', 'f_operate_origin', 'f_spot_id', 'isUpgrade', 'isUpgradeRecord', 'upgradeCheck', 'scanMode', 'isEmpty', 'returnDonation', 'f_charge_record_id', 'f_charge_record_log_id', 'f_sale_id', 'f_refund', 'f_channel_source', 'f_record_fee_coefficient', 'f_consum_donation_coefficient'], 'integer'],
            ['f_record_fee', 'number', 'max' => 999999.99, 'min' => 0],
            ['donationFee', 'number', 'max' => 999999.99, 'min' => 0],
            ['f_income', 'number', 'max' => 999999.99, 'min' => 0],
            [['f_record_fee', 'donationFee'], 'trim'],
            ['f_record_fee', 'match', 'pattern' => '/^(\d*\.?[0-9]{0,2})$/', 'message' => '金额只能精确到分'],
            ['donationFee', 'match', 'pattern' => '/^(\d*\.?[0-9]{0,2})$/', 'message' => '金额只能精确到分'],
            ['f_income', 'match', 'pattern' => '/^(\d*\.?[0-9]{0,2})$/', 'message' => '金额只能精确到分'],
            ['f_change', 'match', 'pattern' => '/^(\d*\.?[0-9]{0,2})$/', 'message' => '金额只能精确到分'],
            ['f_record_fee', 'validateRecordFee'],
            ['donationFee', 'validateDonationFee'],
            [['isUpgradeRecord'], 'validateIsUpgrade', 'on' => 'record'],
            [['f_create_time', 'f_update_time', 'isDonation'], 'safe'],
            [['f_user_name'], 'string', 'max' => 32],
            ['f_flow_item', 'string', 'max' => 15],
            ['f_remark', 'string', 'max' => 70],
            [['f_remark', 'f_flow_item', 'orderSn'], 'string'],
            [['f_income'], 'default', 'value' => '0.00'],
            [['f_consum_donation'], 'default', 'value' => '0.00'],
            ['f_income', 'validateCash', 'on' => 'recharge'],
            [['f_income', 'f_consum_donation', 'f_card_fee_beg', 'f_card_fee_end', 'wechatAuthCode', 'alipayAuthCode', 'oldAmount', 'oldDonation'], 'number'],
            [['f_pay_type', 'f_operate_origin', 'f_charge_record_id', 'f_charge_record_log_id'], 'default', 'value' => 0],
            [['wechatAuthCode'], 'validateAuthCode'],
            [['alipayAuthCode'], 'validateAuthCode'],
            [['scanMode'], 'validateScanMode'],
            [['f_channel_source'], 'default', 'value' => 1],
            [['f_refund'], 'default', 'value' => 2]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'f_physical_id' => '序号',
            'f_record_id' => '操作数据ID',
            'f_record_type' => '交易类型',
            'f_record_fee' => '交易金额(元)',
            'f_user_id' => '后台操作用户ID',
            'f_user_name' => '操作人',
            'f_state' => '状态',
            'f_pay_type' => '支付方式',
            'f_operate_origin' => '操作渠道',
            'f_create_time' => '时间',
            'f_card_fee_beg' => '交易前卡内余额',
            'f_card_fee_end' => '卡内余额(元)',
            'f_remark' => '备注',
            'f_flow_item' => '交易项',
            'donationFee' => '赠送金额',
            'isDonation' => '赠送金额',
            'isEmpty' => '清空赠送账户',
            'returnDonation' => '退到赠送账户',
            'f_income' => '实收现金(元)',
            'f_change' => '找零',
            'isUpgrade' => '升级',
            'wechatAuthCode' => '授权码',
            'alipayAuthCode' => '授权码',
            'f_update_time' => 'F Update Time',
            'f_charge_record_id' => '收费记录id',
            'f_charge_record_log_id' => '收费交易流水id',
            'f_sale_id' => '健康顾问',
            'f_spot_id' => '来源渠道',
            'f_refund' => '退款状态(1-已退款,2-未退款)',
            'f_channel_source' => '来源渠道'
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['record'] = ['f_record_id', 'f_flow_item', 'f_record_type', 'f_record_fee', 'f_pay_type', 'f_remark', 'donationFee', 'isDonation', 'isUpgradeRecord', 'upgradeCheck', 'oldAmount', 'oldDonation', 'isEmpty', 'returnDonation'];
        $scenarios['recharge'] = ['f_record_id', 'f_record_type', 'f_record_fee', 'f_pay_type', 'f_remark', 'f_income', 'f_change', 'donationFee', 'isDonation', 'isUpgrade', 'orderSn', 'upgradeCheck', 'wechatAuthCode', 'alipayAuthCode', 'scanMode'];
        return $scenarios;
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $userModel = Yii::$app->user->identity;
            if (!$this->f_user_name) {
                $this->f_user_name = !empty($userModel) ? $userModel->username : '';
            }
            if (!$this->f_user_id) {
                $this->f_user_id = !empty($userModel) ? $userModel->id : 0;
            }
            $this->f_record_fee = strval($this->f_record_fee);
            $this->f_sale_id = CardRecharge::getSaleByRecordId($this->f_record_id);

            $recordType = $this->f_record_type;
            if ($recordType == 1 || $recordType == 4 || $recordType == 5) {
                $this->f_record_fee_coefficient = 1;
                $this->f_consum_donation_coefficient = 1;
            } else if ($recordType == 2 || $recordType == 3 || $recordType == 6) {
                $this->f_record_fee_coefficient = -1;
                $this->f_consum_donation_coefficient = -1;
            }
        }
        return parent::beforeSave($insert);
    }

    public function validateIsUpgrade($attribute, $params) {
//        if (!$this->hasErrors()) {
//        var_dump($this->oldAmount != $this->f_record_fee);exit;
        if ($this->f_record_type == 1 && empty($this->errors['f_record_fee']) && ( $this->oldAmount != $this->f_record_fee || !$this->oldAmount || ($this->isDonation && ($this->oldDonation != $this->donationFee)))) {
            $upgradeCardInfo = CardRecharge::upgradeCard($this->f_record_id, false, $this->f_record_fee + $this->donationFee);
            $totalAmount = $upgradeCardInfo ? $upgradeCardInfo['totalAmount'] : '';
            $upgradeCard = $upgradeCardInfo ? $upgradeCardInfo['f_category_name'] : '';
            \Yii::info('validateIsUpgrade:' . json_encode($upgradeCardInfo));
//        if ($totalAmount && $this->is_upgrade == 1) {//可自动升级
            if (($totalAmount)) {//可自动升级
                $this->addError($attribute, ['is_upgrade', $totalAmount, $upgradeCard]);
            }
        }

//        }
    }

    public static $getEditRecordType = [
        1 => '充值',
        2 => '消费',
        3 => '提现',
        4 => '消费退还',
    ];
    public static $getRecordType = [
        1 => '充值',
        2 => '消费',
        3 => '提现',
        4 => '消费退还',
        5 => '赠送',
        6 => '清空',
    ];
    public static $getPayType = [
        1 => '现金',
        2 => '刷卡',
        3 => '微信',
        4 => '支付宝',
        5 => '美团'
    ];
    public static $getOperateOrigin = [
        1 => '门诊收费',
        2 => '手动登记',
        3 => '充值',
        4 => '新增收费',
        5 => '套餐卡购买'
    ];

    //当点充值按钮时，判断是否首次充值
    public static function getOneFlow($recordId, $recordType = 1) {
        $record = self::find()->where(['f_record_id' => $recordId, 'f_record_type' => $recordType])->asArray()->all();
        return $record;
    }

    /**
     *
     * @param type $recordId
     * @param type $recordType
     * @return type 获取是否首次赠送 true 是 false 否
     */
    public static function getOnePresent($recordId) {
        $record = self::getOneFlow($recordId, 1);
        if (count($record) == 1) {//只有一条充值记录的时候
            $present = self::find()->select(['f_physical_id', 'f_record_id'])->where(['f_record_id' => $recordId, 'f_record_type' => 5])->asArray()->all();
            return empty($present) ? true : false;
        }
        return false;
    }

    public static function findModel($id) {
        $record = self::findOne(['f_record_id' => $id]);
        if (is_null($record) || empty($record)) {
            return new self;
        } else {
            return $record;
        }
    }

    public static function getExcelCell() {
        $arr = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $data = [];
        foreach ($arr as $v1) {
            foreach ($arr as $v2) {
                $data[] = $v1 . $v2;
            }
        }
        $res = array_merge($arr, $data);
        return $res;
    }

    /**
     *
     * @param type $recordId 记录ID
     * @return 获取流水信息
     */
    public static function getFlow($recordId) {
        $record = self::find()->where(['f_record_id' => $recordId])->asArray()->all();
        return $record;
    }

    public function validateRecordFee($attribute, $params) {

        if (!$this->hasErrors()) {
            if ($this->f_record_type == 2) {//2是消费
//                $feeEnd = CardFlow::find()->select('f_card_fee_end')->where(['f_record_id' => $this->f_record_id])->orderBy(['f_physical_id' => SORT_DESC])->asArray()->one();
                $feeEnd = CardRecharge::find()->select(['f_card_fee', 'f_donation_fee'])->where(['f_physical_id' => $this->f_record_id])->asArray()->one();
                if (($feeEnd['f_card_fee'] + $feeEnd['f_donation_fee']) < $this->f_record_fee) {
                    $this->addError($attribute, "消费金额不能大于卡内余额");
                }
            }
            if ($this->f_record_type == 1 || ($this->f_record_type == 4 && $this->returnDonation == 0)) {//1是充值
                $feeEnd = CardRecharge::find()->select('f_card_fee')->where(['f_physical_id' => $this->f_record_id])->asArray()->one();
                if ($feeEnd['f_card_fee'] + $this->f_record_fee > 999999.99) {
                    $this->addError($attribute, "卡内金额过大");
                }
            }

            if ($this->f_record_type == 4 && $this->returnDonation) {//4是消费退还
                $feeEnd = CardRecharge::find()->select('f_donation_fee')->where(['f_physical_id' => $this->f_record_id])->asArray()->one();
                if ($feeEnd['f_donation_fee'] + $this->f_record_fee > 999999.99) {
                    $this->addError($attribute, "卡内金额过大");
                }
            }
        }
    }

    public function validateDonationFee($attribute, $params) {

        if (!$this->hasErrors()) {
            if ($this->f_record_type == 1) {//1是充值
                $feeEnd = CardRecharge::find()->select('f_donation_fee')->where(['f_physical_id' => $this->f_record_id])->asArray()->one();
                if ($feeEnd['f_donation_fee'] + $this->donationFee > 999999.99) {
                    $this->addError($attribute, "卡内金额过大");
                }
            }
        }
    }

    /**
     *
     * @param unknown $attribute
     * @param unknown $params
     * @return 验证cash字段
     */
    public function validateCash($attribute, $params) {
        if (!$this->hasErrors()) {
            if ($this->f_pay_type == 1) {
                if ($this->f_income < $this->f_record_fee) {
                    $this->addError($attribute, '实收现金金额不能小于实际应付金额。');
                }
            }
        }
    }

    public function validateAuthCode($attribute) {
        if (strlen($this->$attribute) != 18) {
            $this->addError($attribute, '授权码必须为18为数字。');
        }
    }

    public function validateScanMode($attribute) {
        if ($this->f_pay_type == 3 && $this->scanMode == 1) {
            if ($this->wechatAuthCode == '') {
                $this->addError('wechatAuthCode', '授权码不能为空。');
            }
        }
        if ($this->f_pay_type == 4 && $this->scanMode == 1) {
            if ($this->alipayAuthCode == '') {
                $this->addError('alipayAuthCode', '授权码不能为空。');
            }
        }
    }

    /**
     * @return 根据各种情况来增加流水
     */
    public static function addFlow($flowModel, $recordId) {
        $dbTrans = $flowModel->getDb()->beginTransaction();
        try {
            /**
             * 充值自己的流水
             */
            $recordModel = CardRecharge::findModel($recordId);
            $flowModel->f_card_fee_beg = $recordModel->f_card_fee + $recordModel->f_donation_fee; //交易前金额
            $fee = ($flowModel->f_record_type == 1 || $flowModel->f_record_type == 4) ? $flowModel->f_record_fee : -$flowModel->f_record_fee;
            $endFee = $recordModel->f_card_fee + $fee;
            if ($endFee < 0) {//卡内余额不足
                //先判断卡总额是否够减
                if (($endFee + $recordModel->f_donation_fee) < 0) {//不够
                    throw new Exception('卡总额不够减');
                } else {
                    $recordModel->f_card_fee = '0';
                    $recordModel->f_donation_fee = strval($endFee + $recordModel->f_donation_fee);
                }
            } else {
                $recordModel->f_card_fee = strval($recordModel->f_card_fee + $fee);
            }
            $flowModel->f_card_fee_end = strval($flowModel->f_card_fee_beg + $fee); //交易后金额
            //判断是否  首次充值
            $oneFlow = CardFlow::getOneFlow($recordId, 1);

            if (empty($oneFlow) && $flowModel->f_record_type == 1) {
                //首次充值
                $flowModel->f_remark = '首次充值;' . $flowModel->f_remark;
            }

            $flowModel->f_operate_origin = 3;
            $retOne = $flowModel->save(); //充值流水
            if ($retOne === false) {
                throw new Exception('CardFlow save failed');
            }
            Yii::info(json_encode($flowModel->errors) . '********' . 'flowModel-save 充值流水');
            /**
             * 充值赠送流水
             */
            if ($flowModel->isDonation == 1 && $flowModel->donationFee > 0) {//有赠送数据
                $newModel = new CardFlow();
                $newModel->f_record_id = $recordId;
                $newModel->f_record_type = 5;
                $newModel->f_record_fee = $flowModel->donationFee;
                $newModel->f_card_fee_beg = $flowModel->f_card_fee_end;
                $newModel->f_user_id = $flowModel->f_user_id;
                $newModel->f_user_name = $flowModel->f_user_name;
                $newModel->f_card_fee_end = strval($flowModel->f_card_fee_end + $flowModel->donationFee);
                $newModel->f_operate_origin = 3;
                $newModel->f_spot_id = $flowModel->f_spot_id;
                //判断是否  首次赠送
                $onePresentFlow = self::getOnePresent($recordId);
                if ($onePresentFlow) {
                    //首次充值
                    $newModel->f_remark = '首次充值赠送;' . $newModel->f_remark;
                }
                $retTwo = $newModel->save(); //赠送流水
                if ($retTwo === false) {
                    throw new Exception('donationFee save failed');
                }
                Yii::info(json_encode($newModel->errors) . '********' . 'newModel-save 赠送流水');
                $recordModel->f_donation_fee = strval($recordModel->f_donation_fee + $flowModel->donationFee);
            }
            /**
             * 更新充值卡的基本余额  和 赠送余额
             */
            if ($flowModel->isUpgrade == 1) {//点击了自动升级
                if ($flowModel->f_record_type == 1 || $flowModel->f_record_type == 5) {//充值或赠送时判断  是否达到升级卡种
                    try {
                        $res = CardRecharge::upgradeCard($recordId, true);
                    } catch (Exception $e) {
                        Yii::info('CardRecharge upgradeCard failed ' . $e->getMessage());
                        throw new Exception('history save failed');
                    }
                }
            }
            $retThree = $recordModel->save(false);
            if ($retThree === false) {
                throw new Exception('retThree save failed');
            }
            Yii::info(json_encode($recordModel->errors) . '********' . 'recordModel-save 更新充值卡余额');
            //充值的时候    发送短信
            CardRecharge::sendMessage($recordId, $flowModel->f_record_type, $flowModel->f_record_fee, $flowModel->donationFee, '', $flowModel->f_spot_id);
            $dbTrans->commit();
            return true;
        } catch (Exception $e) {
            $dbTrans->rollBack();
            Yii::info('addCardFlow Failed ' . $e->getMessage());
            return false;
        }
    }

    /**
     * @desc 新增充值卡-消费退还交易流水
     * @param integer $oldChargeRecordId 旧收费记录id 需要查找会员卡优惠金额信息，然后原路退回
     * @param integer $newChargeRecordId 新退费记录id
     * @param integer $spotId 诊所id
     * @param object  $userInfo 当前用户信息
     * @param float   $refundMoney 退款金额
     * @param integer $chargeType 1-正常流程，2-物资管理
     * @return 返回充值卡交易流水id
     */
    public static function addRefundFlow($oldChargeRecordId, $newChargeRecordId, $userInfo, $parentSpotId, $spotId, $refundMoney, $chargeType = 1) {
        $cardFlowModel = new static();
        $dbTrans = $cardFlowModel->getDb()->beginTransaction();
        try {
            $query = new Query();
            $query->from(['a' => CardFlow::tableName()]);
            $query->select(['a.f_record_id', 'a.f_record_fee', 'a.f_consum_donation', 'b.f_card_fee', 'b.f_donation_fee']);
            $query->leftJoin(['b' => CardRecharge::tableName()], '{{a}}.f_record_id = {{b}}.f_physical_id');
            $query->where(['a.f_charge_record_id' => $oldChargeRecordId, 'a.f_spot_id' => $spotId]);
            $oldCardFlowInfo = $query->one($cardFlowModel->getDb());
            //         $oldCardFlowInfo = self::find()->select()->where(['f_charge_record_id' => $oldChargeRecordId,'f_spot_id' => $spotId])->asArray()->one();
            $cardFeeBeg = $oldCardFlowInfo['f_card_fee'] + $oldCardFlowInfo['f_donation_fee']; //原充值卡余额
            //新增消费退还流水
            $orderFee = Common::num($oldCardFlowInfo['f_record_fee'] + $oldCardFlowInfo['f_consum_donation']);
            $cardFlowModel->f_record_id = $oldCardFlowInfo['f_record_id'];
            $cardFlowModel->f_user_id = $userInfo->id;
            $cardFlowModel->f_record_type = 4; //消费退还
            $cardFlowModel->f_pay_type = 0;
            $cardFlowModel->f_operate_origin = $chargeType == 1 ? 1 : 4;
            $cardFlowModel->f_card_fee_beg = $cardFeeBeg;
            $cardFlowModel->f_user_name = $userInfo->username;
            $cardFlowModel->f_spot_id = $spotId;
            $cardFlowModel->f_update_time = date('Y-m-d H:i', time());
            $cardFlowModel->f_charge_record_id = $newChargeRecordId;
            if ($refundMoney == $orderFee) {//金额相等，整单退费，退到对应账户
                $cardFlowModel->f_record_fee = $oldCardFlowInfo['f_record_fee'];
                $cardFlowModel->f_card_fee_end = $cardFeeBeg + $oldCardFlowInfo['f_record_fee'] + $oldCardFlowInfo['f_consum_donation'];
                $cardFlowModel->f_consum_donation = $oldCardFlowInfo['f_consum_donation'];
            } else {//单项退费，根据使用账户退到基本账户或者赠送账户
                $cardFlowModel->f_card_fee_end = ($cardFeeBeg + $refundMoney);
                if ($oldCardFlowInfo['f_consum_donation'] > 0) {//如果使用了赠送金额，则全部退还到赠送账号
                    $cardFlowModel->f_record_fee = 0;
                    $cardFlowModel->f_consum_donation = $refundMoney;
                } else {
                    $cardFlowModel->f_record_fee = $refundMoney;
                    $cardFlowModel->f_consum_donation = 0;
                }
            }

            $cardFlowResult = $cardFlowModel->save();
            if (!$cardFlowResult) {
                $dbTrans->rollBack();
                Yii::error('验证失败cardFlowModel:' . json_encode($cardFlowModel->errors, true));
                return false;
            }
            //退款至对应的赠送金额和基本金额
            $cardRechargeModel = CardRecharge::findOne(['f_physical_id' => $cardFlowModel->f_record_id, 'f_parent_spot_id' => $parentSpotId]);
            if ($refundMoney == $orderFee) {//金额相等，整单退费，退到对应账户
                $cardRechargeModel->f_card_fee = $cardRechargeModel->f_card_fee + $oldCardFlowInfo['f_record_fee'];
                $cardRechargeModel->f_donation_fee = $cardRechargeModel->f_donation_fee + $oldCardFlowInfo['f_consum_donation'];
            } else {//单项退费，根据使用账户退到基本账户或者赠送账户
                if ($oldCardFlowInfo['f_consum_donation'] > 0) {//如果使用了赠送金额，则全部退还到赠送账号
                    $cardRechargeModel->f_donation_fee = $cardRechargeModel->f_donation_fee + $refundMoney;
                } else {
                    $cardRechargeModel->f_card_fee = $cardRechargeModel->f_card_fee + $refundMoney;
                }
            }
            $cardRechargeResult = $cardRechargeModel->save();
            if (!$cardRechargeResult) {
                $dbTrans->rollBack();
                Yii::error('验证失败cardRechargeModel:' . json_encode($cardRechargeModel->errors, true));
                return false;
            }
            $dbTrans->commit();
            //消费退还成功  发送短信
            CardRecharge::sendMessage($oldCardFlowInfo['f_record_id'], 4, $refundMoney);
            return $cardFlowModel->f_physical_id;
        } catch (Exception $e) {
            $dbTrans->rollBack();
            Yii::info('addRefundFlow Failed ' . $e->getMessage());
            return false;
        }
    }

    public static function getCardChargeRecord($chargeRecordIdList) {
        $spotId = isset($_COOKIE['spotId']) ? $_COOKIE['spotId'] : 0;
        return self::find()->select(['chargeRecordId' => 'f_charge_record_id', 'count' => 'count(f_physical_id)'])->where(['f_charge_record_id' => $chargeRecordIdList, 'f_spot_id' => $spotId])->groupBy('f_charge_record_id')->asArray()->indexBy('chargeRecordId')->all();
    }

}
