<?php

namespace app\specialModules\recharge\models;

use Yii;
use yii\db\Exception;
use app\modules\patient\models\Patient;
use app\modules\user\models\User;
use yii\db\Query;

/**
 * This is the model class for table "{{%membership_package_card_flow}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $membership_package_card_id
 * @property integer $user_id
 * @property string  $flow_item
 * @property integer $patient_id
 * @property integer $transaction_type
 * @property string  $price
 * @property string  $username
 * @property integer $pay_type
 * @property integer $operate_origin
 * @property string  $remark
 * @property integer $charge_record_id
 * @property integer $charge_record_log_id
 * @property integer $create_time
 * @property integer $update_time
 */
class MembershipPackageCardFlow extends \app\common\base\BaseActiveRecord
{

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%membership_package_card_flow}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'membership_package_card_id', 'user_id', 'patient_id', 'transaction_type', 'pay_type', 'operate_origin', 'charge_record_id', 'charge_record_log_id', 'create_time', 'update_time'], 'integer'],
            [['price'], 'number'],
            [['flow_item', 'remark'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 32],
            [['flow_item', 'remark', 'transaction_type'], 'required', 'on' => 'record'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID(来源渠道)',
            'membership_package_card_id' => '用户套餐卡ID',
            'user_id' => '操作用户ID',
            'flow_item' => '交易项',
            'patient_id' => '交易用户',
            'transaction_type' => '交易类型',
            'price' => '流水金额（元）',
            'username' => '操作用户名',
            'pay_type' => '支付方式',
            'operate_origin' => '操作渠道',
            'remark' => '备注',
            'charge_record_id' => '收费记录id',
            'charge_record_log_id' => '收费交易流水id',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * 
     * @var array 交易类型
     */
    public static $getTransactionType = [
        1 => '消费',
        2 => '购买',
        3 => '消费退还',
    ];

    /**
     * 
     * @var array 支付方式
     */
    public static $getPayType = [
        1 => '现金',
        2 => '刷卡',
        3 => '微信',
        4 => '支付宝',
        5 => '会员卡',
        6 => '美团',
    ];

    /**
     * 
     * @var array 操作渠道
     */
    public static $getOperateOrigin = [
        1 => '门诊收费',
        2 => '手动登记',
        3 => '新增收费',
        4 => '套餐卡购买'
    ];

    /*
     * 
     */
    public static $getEditRecordType = [
        1 => '消费（扣减次数）',
        3 => '消费退还（增加次数）',
    ];
    /**
     *
     * @param integer $spotId 诊所id
     * @param integer $patientId 患者id
     * @param integer $flowItem 交易项
     * @param integer $transactionType 交易类型(1消费／2购买／3消费退还)
     * @param integer $payType 支付方式(1-现金,2-刷卡,3-微信支付,4-支付宝支付,5-会员卡)
     * @param integer $operateOrigin 操作渠道(1门诊收费/2手动登记/3新增收费/4套餐卡购买)
     * @param integer $userId 操作用户ID
     * @param array   $params 使用套餐卡信息
     * @param integer $chargeRecordId 收费记录id
     * @param integer $chargeRecordLogId 收费交易流水id
     * @param decimal $price 购买时价格
     * @param string  $remark 备注
     * @desc 新增交易流水项
     */
    public static function addFlow($spotId, $patientId, $flowItem, $transactionType, $payType, $operateOrigin, $userId, $params, $chargeRecordId = 0, $chargeRecordLogId = 0, $price = 0, $remark = '') {
        $db = Yii::$app->db;
        $dbTrans = $db->beginTransaction();
        try {
            if (!empty($params)) {
                $userInfo = User::getUserInfo($userId, ['username']);
                foreach ($params as $value) {
                    $rows = [
                        'membership_package_card_id' => $value['membership_package_card_id'],
                        'spot_id' => $spotId,
                        'flow_item' => $flowItem,
                        'transaction_type' => $transactionType,
                        'price' => $price,
                        'user_id' => $userId,
                        'patient_id' => $patientId,
                        'username' => $userInfo['username'],
                        'pay_type' => $payType,
                        'operate_origin' => $operateOrigin,
                        'remark' => $remark,
                        'charge_record_id' => $chargeRecordId,
                        'charge_record_log_id' => $chargeRecordLogId,
                        'create_time' => time(),
                        'update_time' => time()
                    ];
                    //插入会员-套餐卡流水基本信息
                    $db->createCommand()->insert(self::tableName(), $rows)->execute();
                    $flowId = $db->lastInsertID;
                    $rowServices = [];
                    foreach ($value['service'] as $v) {//插入流水关联的服务使用次数信息
                        $rowServices[] = [$spotId, $flowId, $v['package_card_service_id'], $v['time'], time(), time()];
                    }
                    $db->createCommand()->batchInsert(MembershipPackageCardFlowService::tableName(), ['spot_id', 'flow_id', 'package_card_service_id', 'time', 'create_time', 'update_time'], $rowServices)->execute();
                    if (!empty($flowId)) {
                        //发送短信提醒
                        MembershipPackageCard::sendMessage($value['membership_package_card_id'], $flowId, $patientId,$spotId);
                    }
                }
                $dbTrans->commit();
                return true;
            }
            $dbTrans->rollBack();
            return false;
        } catch (Exception $e) {
            $dbTrans->rollBack();
            Yii::error($e->errorInfo, 'MembershipPackageCardFlow--addFlow');
        }
        return false;
    }
    /**
     * 
     * @param integer $chargeRecordId 收费记录id
     */
    public static function refundFlow($chargeRecordId){
        
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['a.patient_id','a.flow_item','a.operate_origin','a.membership_package_card_id','a.transaction_type','b.package_card_service_id','b.time']);
        $query->leftJoin(['b' => MembershipPackageCardFlowService::tableName()],'{{a}}.id = {{b}}.flow_id');
        $query->where(['a.charge_record_id' => $chargeRecordId,'a.spot_id' => self::$staticSpotId]);
        $result = $query->all();
        if(!empty($result)){
            $db = Yii::$app->db;
            $dbTrans = $db->beginTransaction();
            try {
                $params = [];
                foreach ($result as  $v){
                    
                    $sql = 'update '.MembershipPackageCardService::tableName().' set remain_time = remain_time + :remain_time where membership_package_card_id = :membership_package_card_id and package_card_service_id = :package_card_service_id and spot_id = :spot_id ';
                    $db->createCommand($sql,[':remain_time' => $v['time'],':membership_package_card_id' => $v['membership_package_card_id'],':package_card_service_id' => $v['package_card_service_id'],':spot_id' => self::$staticSpotId])->execute();
                    $params[$v['membership_package_card_id']]['membership_package_card_id'] = $v['membership_package_card_id'];
                    $params[$v['membership_package_card_id']]['service'][] = [
                        'package_card_service_id' => $v['package_card_service_id'],
                        'time' => $v['time']
                    ];
                }
               $flowResult = self::addFlow(self::$staticSpotId, $result[0]['patient_id'], $result[0]['flow_item'],3, 0, $result[0]['operate_origin'], Yii::$app->user->identity->id, $params);
               $dbTrans->commit();
               return true;
            }catch (Exception $e){
                $dbTrans->rollBack();
                Yii::error($e->errorInfo,'MembershipPackageCardFlow--refundFlow');
                return false;
            }
        }
        return true;
    }
}
