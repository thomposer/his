<?php

namespace app\specialModules\recharge\models;

use app\modules\spot\models\CardRechargeCategory;
use app\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use app\modules\spot\models\CardDiscount;
use app\modules\spot\models\Spot;
use yii\helpers\Html;
use app\common\Common;
use yii\db\Query;
use app\modules\spot_set\models\CardDiscountClinic;

/**
 * This is the model class for table "{{%card_recharge}}".
 *
 * @property integer $f_physical_id
 * @property string $f_card_id
 * @property string $f_user_name
 * @property string $f_id_info
 * @property string $f_baby_name
 * @property string $f_phone
 * @property integer $f_card_fee
 * @property integer $f_pay_fee
 * @property integer $f_order_status
 * @property integer $f_pay_type
 * @property integer $f_state
 * @property integer $f_property
 * @property integer $f_message_subscribe 订阅短信(1-订阅,2-取消)
 * @property string $f_create_time
 * @property string $f_update_time
 * @property integer $f_sale_id
 */
class CardRecharge extends \yii\db\ActiveRecord
{

    public $category_name;
    public static $staticSpotId; //静态变量，当前诊所id
    public static $staticParentSpotId; //静态变量，当前机构id

    public function init() {
        parent::init();
        $this->f_spot_id = isset($_COOKIE['spotId']) ? $_COOKIE['spotId'] : 0;
        $this->f_parent_spot_id = isset($_COOKIE['parentSpotId']) ? $_COOKIE['parentSpotId'] : 0;
        self::$staticSpotId = $this->f_spot_id;
        self::$staticParentSpotId = $this->f_parent_spot_id;
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
        return '{{%card_recharge}}';
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
            [['f_user_name', 'f_phone'], 'required'],
            [['f_category_id',], 'required', 'on' => 'create'],
            [['f_order_status', 'f_pay_type', 'f_state', 'f_property', 'f_spot_id', 'f_parent_spot_id', 'f_category_id', 'f_message_subscribe', 'f_sale_id'], 'integer'],
            [['f_pay_fee'], 'match', 'pattern' => '/^(\d*\.?[0-9]{0,2})$/', 'message' => '金额只能精确到分'],
            [['f_donation_fee'], 'match', 'pattern' => '/^(\d*\.?[0-9]{0,2})$/', 'message' => '金额只能精确到分'],
            ['f_pay_fee', 'number', 'max' => 999999.99, 'min' => 0],
            ['f_donation_fee', 'number', 'max' => 999999.99, 'min' => 0],
            [['f_card_fee'], 'default', 'value' => '0.00'],
//             [['f_card_fee'], 'string'],
//             [['f_donation_fee'], 'string'],
            [['f_create_time', 'f_update_time'], 'safe'],
            [['f_card_id'], 'string', 'max' => 64],
            [['f_user_name', 'f_phone'], 'string', 'max' => 16],
            [['f_id_info', 'f_baby_name'], 'string', 'max' => 32],
            [['f_give_info'], 'string', 'max' => 255],
            [['f_phone'], 'trim'],
            [['f_message_subscribe'], 'default', 'value' => 1],
            [['f_sale_id'], 'default', 'value' => 0],
            [['f_buy_time'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm', 'max' => '2038-01-01 00:00', 'min' => '1970-01-01 00:00', 'on' => 'create'],
            [['f_buy_time'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm', 'max' => '2038-01-01 00:00', 'min' => '1970-01-01 00:00', 'on' => 'update'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'f_physical_id' => 'ID',
            'f_card_id' => '充值卡ID(预留)',
            'f_user_name' => '客户姓名',
            'f_id_info' => '身份证号',
            'f_baby_name' => '宝宝信息',
            'f_phone' => '手机号',
            'f_card_fee' => '余额(元)',
            'f_pay_fee' => '首次充值金额(元)',
            'f_order_status' => '对应订单id支付状态',
            'f_pay_type' => '支付类型',
            'f_is_logout' => '状态',
            'f_state' => '状态',
            'f_property' => '预留',
            'f_give_info' => '赠送信息',
            'f_donation_fee' => '赠送金额(元)',
            'f_buy_time' => '购卡时间',
            'f_category_id' => '所属卡种',
            'f_sale_id' => '健康顾问',
            'f_create_time' => '支付时间',
            'f_update_time' => '更新时间',
            'category_name' => '卡种',
            'f_message_subscribe' => '订阅',
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['f_card_id', 'f_user_name', 'f_phone', 'f_id_info', 'f_baby_name', 'f_give_info', 'f_category_id', 'f_buy_time', 'f_sale_id'];
        $scenarios['update'] = ['f_card_id', 'f_user_name', 'f_phone', 'f_id_info', 'f_baby_name', 'f_give_info', 'f_buy_time', 'f_sale_id'];
        $scenarios['record'] = ['f_card_fee'];
        return $scenarios;
    }

    public function beforeSave($insert) {
        //判断当前表中是否相应更新时间字段
        if ($insert) {
            $this->f_spot_id = isset($_COOKIE['spotId']) ? $_COOKIE['spotId'] : 0;
            $this->f_parent_spot_id = isset($_COOKIE['parentSpotId']) ? $_COOKIE['parentSpotId'] : 0;
            $this->f_buy_time = (!$this->f_buy_time) ? 0 : strtotime($this->f_buy_time);
        } else {
            $this->f_buy_time = strstr($this->f_buy_time, '-') ? strtotime($this->f_buy_time) : $this->f_buy_time;
        }

//        $this->f_card_fee = strval($this->f_card_fee);
//        $this->f_donation_fee = strval($this->f_donation_fee);
//        isset($this->f_update_time) && $this->f_update_time = time();
        return parent::beforeSave($insert);
    }

    public static $getPayType = [
        '1' => '微信支付',
        '2' => '支付宝',
        '3' => '刷卡',
        '4' => '现金'
    ];
    public static $getOrderStatus = [
        '0' => '未支付',
        '1' => '支付中',
        '2' => '支付失败',
        '3' => '支付成功'
    ];
    public static $getIsLogout = [
        '0' => '正常',
        '1' => '已注销',
    ];

    public static function getData() {
        $data = self::find()->where(['f_parent_spot_id' => $_COOKIE['parentSpotId']])->asArray()->all();
        return $data;
    }

    public static function getExportData() {
        $query = new \yii\db\Query();
        $data = $query->from(['a' => self::tableName()])
                        ->select(['a.f_physical_id', 'a.f_user_name', 'a.f_id_info', 'a.f_phone', 'a.f_baby_name', 'a.f_card_fee', 'a.f_donation_fee', 'category_name' => 'b.f_category_name', "a.f_sale_id"])
                        ->leftJoin(['b' => CardRechargeCategory::tableName()], '{{a}}.f_category_id = {{b}}.f_physical_id')
                        ->where(['f_parent_spot_id' => $_COOKIE['parentSpotId']])->all(self::getDb());
        return $data;
    }

    public static function getSaleNameById($saleId) {
        $data = [];
        if ($saleId) {
            $data = User::find()->select(['username'])->where(["id" => $saleId])->asArray()->one();
        }
        return !empty($data) ? $data["username"] : '';
    }

    /**
     * 
     * @param type $id 卡ID
     * @param $iphoneArray 用户电话数组
     * @return 获取卡相关信息（包括卡种）
     */
    public static function getCardInfo($id = null, $iphoneArray = null) {
        $query = new \yii\db\Query();
        $query->from(['t1' => self::tableName()])
                ->select([
                    'total_fee' => '(t1.f_donation_fee+t1.f_card_fee)', 't1.f_phone', 't1.f_card_id', 't1.f_physical_id', 't1.f_donation_fee', 't1.f_card_fee', 't1.f_is_logout', 't2.f_category_name', 't2.f_category_desc', 't2.f_medical_fee_discount', 't1.f_category_id',
                    't2.f_inspect_discount', 't2.f_check_discount', 't2.f_cure_discount', 't2.f_recipe_discount'
                ])
                ->leftJoin(['t2' => CardRechargeCategory::tableName()], '{{t1}}.f_category_id={{t2}}.f_physical_id');
        if ($id != null) {
            $parentSpotId = isset($_COOKIE['parentSpotId']) ? $_COOKIE['parentSpotId'] : 0;
            $data = $query->where(['t1.f_physical_id' => $id, 't1.f_parent_spot_id' => $parentSpotId])->one(self::getDb());
            return $data;
        } else if ($iphoneArray != null) {
            $rows = [];
            $rechargeCategoryId = [];
            $parentSpotId = isset($_COOKIE['parentSpotId']) ? $_COOKIE['parentSpotId'] : 0;
            $data = $query->where(['t1.f_phone' => $iphoneArray, 't1.f_is_logout' => 0, 't1.f_parent_spot_id' => $parentSpotId])->orderBy(['total_fee' => SORT_DESC])->all(self::getDb());

            foreach ($data as $value) {
//                $tagInfo = CardDiscount::getCardTagDiscount($value['f_category_id']);
                $tagInfo =  CardDiscountClinic::cardDiscountListClinic($value['f_category_id']);
                $rows[$value['f_phone']][] = [
                    'id' => $value['f_category_id'],
                    'name' => $value['f_category_name'],
                    'total_fee' => $value['total_fee'],
                    'card_id' => $value['f_physical_id'],
                    'f_medical_fee_discount' => $value['f_medical_fee_discount'],
                    'tagInfo' => $tagInfo
                ];
            }
            return $rows;
        } else {
            return [];
        }
    }

    /**
     * @param $oldQuery query对象
     * @return 卡相关信息（卡名和卡余额）
     * @desc 获取卡相关信息（卡名和卡余额）
     */
    public static function getCardInfoByQuery($oldQuery = null) {
        if (null == $oldQuery) {
            return [];
        }
        $newQuery = clone $oldQuery; // 为了不影响原来的query对象
        $appointments = $newQuery->indexBy('iphone')->asArray()->all();
        $iphoneArray = array_keys($appointments);
        return self::getCardInfo(null, $iphoneArray);
    }

    /**
     * @param $oldQuery query对象
     * @return 卡相关信息（卡名和卡余额）
     * @desc 获取卡相关信息（卡名和卡余额）
     */
    public static function getCardInfoByQueryNurse($oldQuery = null) {
        if (null == $oldQuery) {
            return [];
        }
        $newQuery = clone $oldQuery; // 为了不影响原来的query对象
        $appointments = $newQuery->indexBy('iphone')->asArray()->all();
        $iphoneArray = array_keys($appointments);
        return self::getPhoneCardCategoryNurse($iphoneArray);
    }

    /**
     * 
     * @param type $id 卡ID
     * @return type 获取卡的基本信息
     */
    public static function getCardById($id) {
        $parentSpotid = isset($_COOKIE['parentSpotId']) ? $_COOKIE['parentSpotId'] : 0;
        return self::find()->select(['f_card_id', 'f_user_name', 'f_id_info', 'f_phone', 'f_card_fee', 'f_pay_fee', 'f_category_id', 'f_donation_fee', 'f_message_subscribe', 'f_is_logout'])->where(['f_physical_id' => $id, 'f_parent_spot_id' => $parentSpotid])->asArray()->one();
    }

    public static function findModel($id) {
        $parentSpotid = isset($_COOKIE['parentSpotId']) ? $_COOKIE['parentSpotId'] : 0;
//        $model = self::findOne(['f_physical_id' => $id, 'f_parent_spot_id' => $parentSpotid]);
        $query = self::find()->where(['f_physical_id' => $id]);
        if ($parentSpotid) {//微信支付  回调的时候  有可能获取不到机构ID
            $query->andWhere(['f_parent_spot_id' => $parentSpotid]);
        }
        $model = $query->one();
        if ($model && $model != null) {
            return $model;
        } else {
            return null;
        }
    }

    /**
     * 
     * @param type $recordId 卡ID
     * @param type $update 是否自动升级 true/是 false/否
     * @param object $flowModel 订单流水详情记录对象信息
     */
    public static function upgradeCard($recordId, $update = false, $amount = 0) {
        $rechargeCategory = CardRechargeCategory::find()->select(['f_upgrade_time', 'f_physical_id'])->where(['f_state' => 2, 'f_auto_upgrade' => 1])->asArray()->one();
        $upgradeTime = $rechargeCategory ? $rechargeCategory['f_upgrade_time'] : 365;
        $total = CardFlow::find()->where(['f_record_id' => $recordId, 'f_record_type' => [1, 5]])->andWhere(['>', 'f_create_time', (time() - $upgradeTime * 24 * 60 * 60)])->sum('f_record_fee');
        $total = $total ? $total : 0;
        if ($update) {
            $totalAmount = $total;
        } else {
            $totalAmount = $total + $amount;
        }
        $cardModel = self::findModel($recordId);
        $now = time();
        $selfCardLevel = self::getCardLevel($recordId);
        \Yii::info(' upgradeCard:selfCardLevel 【' . json_encode($selfCardLevel) . '】');
        if ($selfCardLevel && ($selfCardLevel['f_level'] == 0 || $selfCardLevel['f_level'] == 5)) {
//            $sql = 'SELECT f_physical_id,f_upgrade_amount,f_category_name,f_upgrade_amount FROM {{%card_recharge_category}} WHERE f_physical_id<>' . $cardModel->f_category_id .
//                    ' AND f_auto_upgrade=1 AND f_state=2 AND f_parent_id<>0 AND f_upgrade_amount<=' . ($totalAmount) .
//                    ' AND f_create_time <' . $now .
//                    ' AND f_spot_id= ' . $cardModel->f_parent_spot_id .
//                    ' AND (f_create_time+f_upgrade_time* 24 * 60 * 60)>=' . $now . ' ORDER BY f_upgrade_amount DESC';
            $cardCategory = [];
        } else {
            $sql = 'SELECT t1.f_physical_id,t1.f_upgrade_amount,t1.f_category_name FROM {{%card_recharge_category}} t1' .
                    ' LEFT JOIN {{%card_recharge_category}} t2 ON t1.f_parent_id=t2.f_physical_id' .
                    ' WHERE t1.f_physical_id<>' . $cardModel->f_category_id .
                    ' AND t1.f_auto_upgrade=1 AND t1.f_state=2 AND t2.f_state=2 AND t1.f_parent_id<>0 AND t1.f_upgrade_amount<=' . ($totalAmount) .
                    ' AND t1.f_create_time <' . $now .
                    ' AND t1.f_spot_id= ' . $cardModel->f_parent_spot_id .
                    ' AND t2.f_level>' . $selfCardLevel['f_level'] .
                    ' AND (t1.f_create_time+t1.f_upgrade_time* 24 * 60 * 60)>=' . $now . ' ORDER BY t2.f_level DESC,t1.f_upgrade_amount DESC';
            \Yii::info(' upgradeCard:sql ' . $sql);
            $cardCategory = self::getDb()->createCommand($sql)->queryOne();
        }

        if (!empty($cardCategory)) {//可以升级
            if ($update) {
                $f_beg_category = $cardModel->f_category_id;
                //修改CardRecharge的所属卡种
                $cardModel->f_category_id = $cardCategory['f_physical_id'];
                $cardModel->f_card_fee = strval($cardModel->f_card_fee);
                $cardModel->f_donation_fee = strval($cardModel->f_donation_fee);
                $retOne = $cardModel->save();
                if ($retOne === false) {
                    \Yii::info('history save failed retOne ' . json_encode($cardModel->errors));
                    throw new Exception('history save failed retOne ' . json_encode($cardModel->errors));
                }
                //在卡种变更记录中增加一条流水
                $history = new CategoryHistory();
                $history->f_record_id = $recordId;
                $history->f_beg_category = $f_beg_category;
                $history->f_end_category = $cardCategory['f_physical_id'];
                $history->f_user_name = '系统';
                $history->f_change_reason = $upgradeTime . '天内,充值额满' . $cardCategory['f_upgrade_amount'] . '元，升级';
                $history->f_user_id = 0;
                $retTwo = $history->save();
                if ($retTwo === false) {
                    \Yii::info('history save failed retTwo ' . json_encode($history->errors));
                    throw new Exception('history save failed retTwo ' . json_encode($history->errors));
                }
            } else {
                $cardCategory['totalAmount'] = $totalAmount;
                return $cardCategory;
            }
        } else {
            return [];
        }
    }

    /**
     * 
     * @param type $recordId 卡ID
     * @return  根据卡id获取卡组等级 以及卡组  卡种信息
     */
    public static function getCardLevel($recordId) {
        $query = new \yii\db\Query();
        $data = $query->from(['t1' => self::tableName()])
                ->select(['t3.f_level', 'categoryName' => 't2.f_category_name', 'groupName' => 't3.f_category_name', 't1.f_card_fee', 't1.f_donation_fee', 't1.f_phone', 't1.f_message_subscribe'])
                ->leftJoin(['t2' => CardRechargeCategory::tableName()], "{{t1}}.f_category_id={{t2}}.f_physical_id")
                ->leftJoin(['t3' => CardRechargeCategory::tableName()], "{{t2}}.f_parent_id={{t3}}.f_physical_id")
                ->where(['t1.f_physical_id' => $recordId])
                ->one(self::getDb());
        return $data;
    }

    /**
     * @param  int  recordId 卡的ID
     * @param  int  type 场景分类 1/充值 2/消费 3/提现 4/消费退还
     * @param  float $recordFee 当前交易的金额
     * @param  float $donationFee 当前赠送的金额
     * @param  string $flowItem 交易项
     * @return  充值卡公用发送短信
     */
    public static function sendMessage($recordId, $type = 1, $recordFee, $donationFee = 0, $flowItem = '', $spotId = 0) {
        $cardInfo = self::getCardLevel($recordId);
        $balance = Common::num($cardInfo['f_card_fee'] + $cardInfo['f_donation_fee']);
        if ($cardInfo['f_message_subscribe'] == 2) {//用户取消了订阅
            return;
        }
        $spotInfo = self::getSpotInfo($spotId);
        \Yii::info('sendMessage spotInfo :[' . json_encode($spotInfo) . ']');
        $parentSpotName = urlencode($spotInfo['parentSpotName']);
        $flowItem = urlencode($flowItem);
        $groupName = urlencode($cardInfo['groupName']);
        switch ($type) {
            case 1://充值
                //是否为首次充值
                $firstFlow = CardFlow::getOneFlow($recordId);
                if (count($firstFlow) == 1) {//首次充值
                    $template = "【妈咪知道】欢迎选择{$parentSpotName}！您已新开1张会员卡“{$groupName}”，首次充值{$recordFee}元，";
                    $template .=(!empty($donationFee) && $donationFee != '0.00') ? "赠送{$donationFee}元，" : '';
                    $template .= "目前账户余额{$balance}元。如有疑问，请联系{$spotInfo['telephone']}。";
                } else {
                    $template = "【妈咪知道】您的会员卡本次充值{$recordFee}元，";
                    $template.=(!empty($donationFee) && $donationFee != '0.00') ? "赠送{$donationFee}元，" : '';
                    $template .= "目前账户余额{$balance}元。如有疑问，请联系{$spotInfo['telephone']}。";
                }
                break;
            case 2:// 2/消费
                $template = "【妈咪知道】您的会员卡本次";
                $template.=!empty($flowItem) ? "“{$flowItem}”" : '';
                $template.= "消费{$recordFee}元，目前账户余额{$balance}元。如有疑问，请联系{$spotInfo['telephone']}。";
                break;
            case 3://3/提现
                $template = "【妈咪知道】您的会员卡本次提现{$recordFee}元，目前账户余额{$balance}元。如有疑问，请联系{$spotInfo['telephone']}。";
                break;
            case 4://4/消费退还
                $template = "【妈咪知道】您的会员卡本次";
                $template.=!empty($flowItem) ? "“{$flowItem}”" : '';
                $template .= "消费取消，退还{$recordFee}元，目前账户余额{$balance}元。如有疑问，请联系{$spotInfo['telephone']}。";
                break;
            default :
                $template = '';
                break;
        }
        //发送短信
        Yii::info("cardSendMessage [{$recordId}] [{$type}] [{$recordFee}] [{$donationFee}] [{$flowItem}] [{$cardInfo['f_phone']}] message: [{$template}]");
        if (!empty($template) && $cardInfo['f_phone']) {
            Common::mobileSend($cardInfo['f_phone'], $template);
        }
        return true;
    }

    public static function getSpotInfo($spotId) {
        $spotId = $spotId ? $spotId : $_COOKIE['spotId'];
        Yii::info('cardrecharge send message spot :【' . $spotId . '】');
        Yii::info('cardrecharge send message wechatSpotId :【' . $_COOKIE['wechatSpotId'] . '】');
        $cacheSuffix = $spotId . Yii::$app->user->identity->id;
        $spot['parentSpotName'] = Yii::$app->cache->get(Yii::getAlias('@parentSpotName') . $cacheSuffix) ? Yii::$app->cache->get(Yii::getAlias('@parentSpotName') . $cacheSuffix) : '';
        $spot['spotName'] = Yii::$app->cache->get(Yii::getAlias('@spotName') . $cacheSuffix) ? Yii::$app->cache->get(Yii::getAlias('@spotName') . $cacheSuffix) : '';
        $spot['parentSpotCode'] = Yii::$app->cache->get(Yii::getAlias('@parentSpotCode') . $cacheSuffix) ? Yii::$app->cache->get(Yii::getAlias('@parentSpotCode') . $cacheSuffix) : '';
        $spot['telephone'] = Spot::getSpot($spotId)['telephone'];
        return $spot;
    }

    /**
     * 
     * @param type $phone 手机号
     * @return type  根据手机号获取已有的会员卡信息
     */
    public static function getPhoneCardCategory($phone) {
        $query = new Query();
        $query->from(['a' => CardRecharge::tableName()]);
        $query->select(['a.f_physical_id', 'a.f_user_name', 'a.f_phone', 'f_buy_time' => 'a.f_buy_time', 'b.f_category_name', 'total_fee' => '(a.f_donation_fee+a.f_card_fee)',]);
        $query->leftJoin(['b' => CardRechargeCategory::tableName()], "{{a}}.f_category_id = {{b}}.f_physical_id");
        $parentSpotId = isset($_COOKIE['parentSpotId']) ? $_COOKIE['parentSpotId'] : 0;
        $query->where(['a.f_phone' => $phone, 'a.f_is_logout' => 0, 'a.f_parent_spot_id' => $parentSpotId]);
        $query->orderBy(['f_buy_time' => SORT_DESC]);
        $data = $query->all(self::getDb());
        foreach ($data as &$val) {
            $val['f_buy_time'] = $val['f_buy_time'] ? date('Y-m-d H:i', $val['f_buy_time']) : '暂无购卡时间';
        }
        return $data;
    }

    /**
     * 
     * @param type $phone 手机号
     * @return type  根据手机号获取已有的会员卡信息
     */
    public static function getPhoneCardCategoryNurse($phone) {
        $query = new Query();
        $query->from(['a' => CardRecharge::tableName()]);
        $query->select(['a.f_physical_id', 'a.f_user_name', 'a.f_phone', 'f_buy_time' => 'a.f_buy_time', 'b.f_category_name', 'total_fee' => '(a.f_donation_fee+a.f_card_fee)',]);
        $query->leftJoin(['b' => CardRechargeCategory::tableName()], "{{a}}.f_category_id = {{b}}.f_physical_id");
        $parentSpotId = isset($_COOKIE['parentSpotId']) ? $_COOKIE['parentSpotId'] : 0;
        $query->where(['a.f_phone' => $phone, 'a.f_is_logout' => 0, 'a.f_parent_spot_id' => $parentSpotId]);
        $data = $query->all(self::getDb());
        $rows = [];
        foreach ($data as $value) {
            $rows[$value['f_phone']][] = [
                'id' => $value['f_category_id'],
                'name' => $value['f_category_name'],
                'total_fee' => $value['total_fee'],
            ];
        }
        return $rows;
    }

    /*
     * @return  获取当前机构的所有健康顾问
     */

    public static function getSales() {
        $data = User::find()->select(['id', 'username'])->where("occupation = 10 and status = 1")->andWhere(['spot_id' => $_COOKIE['parentSpotId']])->asArray()->all();
        $sales = [];
        foreach ($data as $sale) {
            $sales[$sale['id']] = $sale['username'];
        }
        return $sales;
    }

    /**
     * 
     * @param type $recordId 卡ID
     * @return 根据卡ID获取其健康顾问 
     */
    public static function getSaleByRecordId($recordId) {
        return self::find()->select(['f_sale_id'])->where(["f_physical_id" => $recordId])->asArray()->one()['f_sale_id'];
    }

}
