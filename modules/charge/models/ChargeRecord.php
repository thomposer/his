<?php

namespace app\modules\charge\models;

use Yii;
use app\common\Common;

/**
 * This is the model class for table "{{%charge_record}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $patient_id
 * @property integer $record_id
 * @property integer $parent_id 收费记录ID（退费记录中才有该字段，记录对应的收费记录ID）
 * @property decimal $price 已收费金额
 * @property integer $status 状态(1-已收费,2-未收费)
 * @property integer $type 支付方式
 * @property string $pks //支付的
 * @property string $create_time
 * @property string $update_time
 * @property string $out_trade_no 订单号
 * @property number $cash 
 * @property string $discount_reason 优惠原因
 * @property integer $discount_type 优惠类型(1-无，2-金额扣减，3-折扣)
 * @property number $discount_price 优惠金额
 * @property integer $charge_type 收费种类(1-正常流程收费,2-物资管理收费)
 * @property ChargeInfo[] $chargeInfos
 */
class ChargeRecord extends \app\common\base\BaseActiveRecord
{

    public $pks;
    public $cash;
    public $readonly;
    public $allPrice; //总价格
    public $wechatAuthCode; //微信授权码
    public $alipayAuthCode; //支付宝授权码
    public $scanMode = 2;//默认二维码
    public $payErrors;
    public $cardType; //会员卡类型
    public $originalPrice; //会员卡是否原价支付，不打折扣
    public $firstDiagnosisFree;//首单减免诊金

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%charge_record}}';
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'patient_id', 'record_id'], 'required'],
            [['price', 'cash', 'discount_price', 'allPrice', 'income', 'change', 'wechatAuthCode', 'alipayAuthCode'], 'number'],
            [['price', 'cash', 'discount_price', 'income', 'change'], 'default', 'value' => '0.00'],
            [['discount_type'], 'default', 'value' => 1],
            [['wechatAuthCode'], 'validateAuthCode'],
            [['alipayAuthCode'], 'validateAuthCode'],
            [['scanMode'], 'validateScanMode'],
            [['spot_id', 'patient_id', 'record_id', 'create_time', 'update_time', 'status', 'type', 'discount_type', 'scanMode', 'cardType', 'originalPrice','charge_type','firstDiagnosisFree'], 'integer'],
            [['pks', 'discount_reason'], 'string'],
            [['discount_price'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
            ['cash', 'validateCash', 'on' => 'create'],
            [['out_trade_no', 'discount_reason'], 'default', 'value' => ''],
            [['type'], 'default', 'value' => 0],
            [['parent_id', 'originalPrice'], 'default', 'value' => 0],
            [['charge_type'],'default','value' => 1],
            [['cardType'], 'validateCardType'],
            [['readonly', 'allPrice'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'patient_id' => '患者ID',
            'record_id' => '流水ID',
            'parent_id' => '收费记录ID', //收费记录ID（退费时该字段有效，记录对应的收费记录ID）
            'status' => '状态',
            'price' => '实际应付',
            'type' => '支付方式',
            'cash' => '实收现金',
            'out_trade_no' => '订单号',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'discount_type' => '优惠方式',
            'discount_price' => '优惠金额',
            'discount_reason' => '优惠原因',
            'wechatAuthCode' => '微信授权码',
            'alipayAuthCode' => '支付宝授权码',
            'originalPrice' => '会员卡原价扣费，不打折',
            'charge_type' => '收费种类'
        ];
    }

    /**
     * 
     * @var 支付方式
     */
    public static $getType = [
        1 => '现金',
        2 => '刷卡',
        3 => '微信',
        4 => '支付宝',
        5 => '会员卡',
        6 => '充值卡',
        7 => '服务卡',
        8 => '套餐卡',
        9 => '美团',
    ];

    /**
     * 
     * @var 优惠方式
     */
    public static $getDiscountType = [
        1 => '无',
        2 => '金额扣减',
        3 => '折扣'
    ];
    /**
     * 收费状态
     * @var array
     */
    public static $getStatus = [
        1 => '已收费',
        2 => '已退费'
    ];
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChargeInfos() {
        return $this->hasMany(ChargeInfo::className(), ['charge_record_id' => 'id']);
    }

    /**
     * 
     * @param unknown $attribute
     * @param unknown $params
     * @return 验证cash字段
     */
    public function validateCash($attribute, $params) {
        if (!$this->hasErrors()) {
            if ($this->type == 1) {
                if ($this->cash < $this->price) {
                    $this->addError($attribute, '实收现金金额不能小于实际应付金额。');
                }
            }
            if ($this->discount_type != 1) {
                if ($this->price < 0) {
                    $this->addError('discount_price', '优惠金额不能大于实际应付金额。');
                }

                if ($this->discount_price == '') {
                    $this->addError('discount_price', '优惠金额不能为空。');
                }
                /*  if($this->discount_reason == ''){
                  $this->addError('discount_reason','优惠原因不能为空。');
                  } */
            }
        }
    }

    public function validateAuthCode($attribute) {
        if (strlen($this->$attribute) != 18) {
            $this->addError($attribute, '授权码必须为18为数字。');
        }
    }

    public function validateScanMode($attribute) {
        if ($this->type == 3 && $this->scanMode == 1) {
            if ($this->wechatAuthCode == '') {
                $this->addError('wechatAuthCode', '授权码不能为空。');
            }
        }
        if ($this->type == 4 && $this->scanMode == 1) {
            if ($this->alipayAuthCode == '') {
                $this->addError('alipayAuthCode', '授权码不能为空。');
            }
        }
    }

    public function validateCardType($attribute) {
        if ($this->type == 5 && $this->cardType == null) {
            $this->addError($attribute, '请选择会员卡');
        }
    }

    /**
     * 
     * @param 就诊流水ID $record_id
     * @return 返回当前就诊水流的收费记录表ID
     */
    public static function findChargeRecord($record_id) {
        return ChargeRecord::find()->select(['id'])->where(['record_id' => $record_id])->asArray()->one()['id'];
    }

    /**
     * 
     * @param 总金额 $total
     * @param 优惠类型 $discountType
     * @param 优惠金额／优惠比例 $discountPrice
     * @return 返回对应的实收金额，折扣比例和优惠金额
     */
    public static function getDiscount($total, $discountType, $discountPrice, $chargeTotalDiscount, $cardTotalDiscount) {
        $total = Common::num($total);
        $receiptAmount = $total;
        $secondDiscount = '';
        $oneDiscount = '';
        if ($discountType != 1) {
            if ($discountType == 2) {//若为金额扣减
                $oneDiscount = Common::num($discountPrice); //优惠金额
                $receiptAmount = Common::num($total - $oneDiscount);
            } else if ($discountType == 3) {
                $secondDiscount = $discountPrice . '%';
                $secondDiscounted = Common::num(100 - $discountPrice) . '%';
                $receiptAmount = Common::num($total * ($discountPrice / 100));
                $oneDiscount = Common::num($total - $receiptAmount); //优惠金额
            }
        } else {
            $receiptAmount = Common::num($total - $chargeTotalDiscount - $cardTotalDiscount);
            $oneDiscount = Common::num($chargeTotalDiscount + $cardTotalDiscount); //优惠金额
        }
        $result['secondDiscount'] = $secondDiscount; //折扣比例
        $result['secondDiscounted'] = $secondDiscounted; //已折扣比例
        $result['receiptAmount'] = $receiptAmount; //实收金额
        $result['oneDiscount'] = $oneDiscount; //优惠金额
        return $result;
    }
    
    /**
     * @desc 获取当前诊所对应的收费记录
     * @param string|array $where 查询条件
     * @param string $fields 查询字段
     * @return \yii\db\ActiveRecord[]
     */
    public static function getChargeRecordList($where = null,$fields = '*'){
        return self::find()->select($fields)->where(['spot_id' => self::$staticSpotId])->andFilterWhere($where)->asArray()->all();
    }

}
