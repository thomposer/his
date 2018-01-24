<?php

namespace app\modules\charge\models;

use app\modules\outpatient\models\PackageRecord;
use app\modules\patient\models\Patient;
use Yii;
use yii\db\Query;
use app\modules\user\models\User;
use app\modules\triage\models\TriageInfo;
use app\modules\report\models\Report;
use app\specialModules\recharge\models\CardFlow;
use app\modules\patient\models\PatientRecord;
use app\commands\SendsmsController;
use app\common\Common;

/**
 * This is the model class for table "{{%charge_record_log}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $record_id
 * @property string $username
 * @property string $sex
 * @property string $age
 * @property string $head_img
 * @property string $iphone
 * @property string $doctor_name
 * @property string $type_description
 * @property integer $type
 * @property integer $pay_type
 * @property string $price
 * @property string $order_price
 * @property string $discount_price
 * @property string $refund_price
 * @property string $income
 * @property string $change
 * @property string $check_price
 * @property string $cure_price
 * @property string $inspect_price
 * @property string $recipe_price
 * @property string $material_price
 * @property string $material_discount_price
 * @property string $consumables_price
 * @property string $consumables_discount_price
 * @property string $diagnosis_price
 * @property string $diagnosis_time
 * @property string $package_price
 * @property string $package_discount_price
 * @property integer $refund_reason
 * @property string $refund_reason_description
 * @property string $create_time
 * @property string $update_time
 * @property string $out_trade_no
 *
 * @property ChargeInfoLog[] $chargeInfoLogs
 */
class ChargeRecordLog extends \app\common\base\BaseActiveRecord
{

    public function init(){
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%charge_record_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'record_id','type','pay_type','price'],'required'],
            [['spot_id', 'record_id', 'type', 'pay_type', 'diagnosis_time', 'create_time', 'update_time','refund_reason','doctor_id'], 'integer'],
            [['price', 'order_price', 'discount_price', 'refund_price', 'income', 'change', 'check_price','check_discount_price', 'cure_price','cure_discount_price', 'inspect_price','inspect_discount_price', 'recipe_price','recipe_discount_price', 'diagnosis_price','diagnosis_discount_price','material_price','material_discount_price','consumables_discount_price','consumables_price','package_price','package_discount_price'], 'number'],
            [['price', 'order_price', 'discount_price', 'refund_price', 'income', 'change','check_discount_price','cure_discount_price','inspect_discount_price','recipe_discount_price','diagnosis_discount_price','material_discount_price','refund_reason','diagnosis_time','consumables_discount_price','package_discount_price'], 'default','value' => 0],
            [['username', 'sex', 'doctor_name', 'type_description','iphone', 'out_trade_no'], 'string', 'max' => 64],
            [['head_img','fee_remarks','refund_reason_description'],'string','max' => 255],
            [['age'], 'string', 'max' => 100],
            [['username', 'sex', 'doctor_name', 'type_description','iphone','head_img','age','fee_remarks','refund_reason_description'],'default','value' => '']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'record_id' => '流水ID',
            'username' => '姓名',
            'sex' => '性别',
            'age' => '年龄',
            'head_img' => '头像',
            'iphone' => '手机号',
            'doctor_id' => '医生ID',
            'doctor_name' => '接诊医生',
            'type_description' => '服务类型 ',
            'type' => '类别',
            'pay_type' => '支付方式(1-现金,2-刷卡,3-微信支付,4-支付宝支付,5-会员卡)',
            'price' => '流水（元）',
            'order_price' => '应收费用（原价）',
            'discount_price' => '总优惠金额',
            'refund_price' => '退费合计',
            'income' => '实收费用',
            'change' => '找零',
            'check_price' => '影像学检查总费用',
            'check_discount_price' => '影像学检查优惠总金额',
            'cure_price' => '治疗总费用',
            'cure_discount_price' => '治疗优惠总金额',
            'inspect_price' => '实验室检查总费用',
            'inspect_discount_price' => '实验室检查优惠总金额',
            'recipe_price' => '处方总费用',
            'recipe_discount_price' => '处方优惠总金额',
            'diagnosis_price' => '诊疗费用',
            'diagnosis_discount_price' => '诊疗优惠总金额',
            'material_price' => '其他费用',
            'material_discount_price' => '其他优惠总金额',
            'consumables_price' => '医疗耗材费用',
            'consumables_discount_price' => '医疗耗材优惠总金额',
            'package_price' => '医嘱套餐总费用',
            'package_discount_price' => '医嘱套餐优惠总金额',
            'diagnosis_time' => '接诊时间',
            'fee_remarks' => '诊疗费备注',
            'refund_reason' => '退费原因',
            'refund_reason_description' => '退费原因说明',
            'trade_begin_time' => '交易开始时间',
            'trade_end_time' => '交易结束时间',
            'create_time' => '交易时间',
            'update_time' => '更新时间',
            'spot_name' => '诊所名称',
            'case_id' => '门诊号',
        ];
    }
    public static $getType= [
        '1' => '收费',
        '2' => '退费'
    ];
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChargeInfoLogs()
    {
        return $this->hasMany(ChargeInfoLog::className(), ['charge_record_log_id' => 'id']);
    }

    /**
     * @param integer $id 交易流水id
     * @return array|null|\yii\db\ActiveRecord
     * @return 返回交易流水记录详情
     *
     */
    public static function getChargeLogList($id){
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select([
            'a.username','a.sex','a.age','a.head_img','a.iphone','a.doctor_name','a.pay_type','a.type','a.price',
            'a.order_price','a.discount_price','a.refund_price','a.income','a.change',
            'a.check_price','a.check_discount_price','a.cure_price','a.cure_discount_price','a.inspect_price','a.inspect_discount_price','a.recipe_price','a.recipe_discount_price','a.diagnosis_price','a.material_price','a.material_discount_price','a.package_price','a.package_discount_price',
            'a.consumables_price','a.consumables_discount_price',
            'a.diagnosis_discount_price','a.fee_remarks','a.refund_reason','a.refund_reason_description','a.create_time','c.patient_number','c.birthday',
            'packageRecordId' => 'd.id','recordId' =>'b.id'
        ]);
        $query->leftJoin(['b' => PatientRecord::tableName()],'{{a}}.record_id = {{b}}.id');
        $query->leftJoin(['c' => Patient::tableName()],'{{b}}.patient_id = {{c}}.id');
        $query->leftJoin(['d' => PackageRecord::tableName()],'{{d}}.record_id = {{b}}.id');
        $query->where(['a.id' => $id,'a.spot_id' => self::$staticSpotId]);
        $data = $query->one();
        if($data['consumables_price'] != null){
            ($data['material_price'] == null) && $data['material_price'] = 0.00;
            ($data['material_discount_price'] == null) && $data['material_discount_price'] = 0.00;
            $data['material_price'] = Common::num($data['material_price'] + $data['consumables_price']);
            $data['material_discount_price'] = Common::num($data['material_discount_price'] + $data['consumables_discount_price']);
        }
        return $data;
    }
    
    /**
     * 
     * @param integer $spotId 诊所id
     * @param integer $patientId 患者id
     * @param integer $recordId 就诊流水id
     * @param array $params 一些收费参数,例如收费详情id,会员卡优惠参数
     * @param integer $type 类别 1-收费，2-退费
     * @param integer $payType 支付方式(1-现金,2-刷卡,3-微信支付,4-支付宝支付,5-会员卡)
     * @param decimal $income 实收费用
     * @param decimal $change 找零
     * @param number $refundPrice 退费总金额
     * @param integer $cardFlowId 会员卡交易流水记录id
     * @desc 新增交易流水项
     */
    public static function saveChargeRecordLog($spotId,$patientId,$recordId,$params,$type,$payType,$income = 0,$change = 0,$refundPrice = 0,$refundReason = 0,$refundReasonDescription = '',$cardFlowId = 0){
        $body = json_decode($params['body'], true);
        if ($payType == 3 && $type == 1) {//如果为微信支付  则PKS需要从缓存中取
            $pksCacheKey = $body['pks'];
            $pks = Yii::$app->cache->get($pksCacheKey);
        } else {
            $pks = $body['pks'];
        }
        $patientInfo = Patient::find()->select(['username','sex','birthday','head_img','iphone'])->where(['id'=>$patientId])->asArray()->one();
        $chargeInfoList = ChargeInfo::find()->where(['id' => $pks,'spot_id' => $spotId])->asArray()->all();
        $doctorName = User::find()->select(['username'])->where(['id' => $chargeInfoList[0]['doctor_id']])->asArray()->one()['username'];
//         $diagnosisTime = TriageInfo::find()->select(['diagnosis_time'])->where(['record_id' => $recordId,'spot_id' => $spotId])->asArray()->one();
        $query = new Query();
        $query->from(['a' => Report::tableName()]);
        $query->select(['a.type_description','b.diagnosis_time']);
        $query->leftJoin(['b' => TriageInfo::tableName()],'{{a}}.record_id = {{b}}.record_id');
        $query->where(['a.record_id' => $recordId,'a.spot_id' => $spotId]);
        $patientRecordInfo = $query->one();
        $priceRows = [];
        $originalPriceRows = 0;
        $totalDiscountPriceRows = 0;
        $realPriceTotal = 0;
        $feeRemarks = '';//诊疗费收费备注
        $insertRows  = [];//需要插入流水的详情记录数据
        foreach ($chargeInfoList as $v){
           $cardDiscountPrice = 0;
           if (!empty($params['chargeInfoArray'])) {//若是有会员卡优惠金额
               $cardDiscountPrice = $params['chargeInfoArray'][$v['id']];
           }else{
               $cardDiscountPrice = $v['card_discount_price'];
           }
           $originalPrice = $v['unit_price'] * $v['num'];
           $originalPriceRows += $originalPrice;//原价集合
           $totalDiscountPrice = $v['discount_price'] + $cardDiscountPrice;
           $totalDiscountPriceRows += $totalDiscountPrice;//优惠金额总集合
           $priceRows[$v['type']][] = $originalPrice - $totalDiscountPrice;
           $realPriceTotal += $originalPrice - $totalDiscountPrice;//实际应付
           $discountPrice[$v['type']][] = $totalDiscountPrice;
           if($v['type'] == ChargeInfo::$priceType){//若为诊疗费
               $feeRemarks = $v['fee_remarks'];
           }
        }
        $chargeRecordLogModel = new static;
        $chargeRecordLogModel->record_id = $recordId;
        $chargeRecordLogModel->spot_id = $spotId;
        $chargeRecordLogModel->username = $patientInfo['username'];
        $chargeRecordLogModel->sex = $patientInfo['sex'];
        $chargeRecordLogModel->age = Patient::dateDiffage($patientInfo['birthday'],time());
        $chargeRecordLogModel->head_img = $patientInfo['head_img'];
        $chargeRecordLogModel->iphone = $patientInfo['iphone'];
        $chargeRecordLogModel->doctor_id = $chargeInfoList[0]['doctor_id'];
        $chargeRecordLogModel->doctor_name = $doctorName;
        $chargeRecordLogModel->type_description = $patientRecordInfo['type_description'];
        $chargeRecordLogModel->type = $type;
        $chargeRecordLogModel->pay_type = $payType;
        $chargeRecordLogModel->price = $realPriceTotal;//实际应付
        $chargeRecordLogModel->order_price = $originalPriceRows;
        $chargeRecordLogModel->discount_price = $totalDiscountPriceRows;
        if (isset($params["out_trade_no"])) {
            $chargeRecordLogModel -> out_trade_no = $params["out_trade_no"];
        }
        if($type == 2){//若类别为 退费。则记录退费金额
            $chargeRecordLogModel->refund_price = $refundPrice;
            $chargeRecordLogModel->refund_reason_description = $refundReasonDescription;
            $chargeRecordLogModel->refund_reason = $refundReason;
        }
        if ($payType == 3 || $payType == 4) {//若为支付宝，微信支付。
            $chargeRecordLogModel->income = $realPriceTotal;
        } else {
            $chargeRecordLogModel->income = $income;
        }
        $chargeRecordLogModel->change = $change;
        
        $chargeRecordLogModel->check_price = isset($priceRows[ChargeInfo::$checkType])?array_sum($priceRows[ChargeInfo::$checkType]) : '';
        $chargeRecordLogModel->check_discount_price = isset($discountPrice[ChargeInfo::$checkType])?array_sum($discountPrice[ChargeInfo::$checkType]) : 0;
        
        $chargeRecordLogModel->cure_price = isset($priceRows[ChargeInfo::$cureType])?array_sum($priceRows[ChargeInfo::$cureType]) : '';
        $chargeRecordLogModel->cure_discount_price = isset($discountPrice[ChargeInfo::$cureType])?array_sum($discountPrice[ChargeInfo::$cureType]) : 0;
        
        $chargeRecordLogModel->inspect_price = isset($priceRows[ChargeInfo::$inspectType])?array_sum($priceRows[ChargeInfo::$inspectType]) : '';
        $chargeRecordLogModel->inspect_discount_price = isset($discountPrice[ChargeInfo::$inspectType])?array_sum($discountPrice[ChargeInfo::$inspectType]) : 0;
        
        $chargeRecordLogModel->recipe_price = isset($priceRows[ChargeInfo::$recipeType])?array_sum($priceRows[ChargeInfo::$recipeType]) : '';
        $chargeRecordLogModel->recipe_discount_price = isset($discountPrice[ChargeInfo::$recipeType])?array_sum($discountPrice[ChargeInfo::$recipeType]) : 0;
        
        $chargeRecordLogModel->diagnosis_price = isset($priceRows[ChargeInfo::$priceType])?array_sum($priceRows[ChargeInfo::$priceType]) : '';
        $chargeRecordLogModel->diagnosis_discount_price = isset($discountPrice[ChargeInfo::$priceType])?array_sum($discountPrice[ChargeInfo::$priceType]) : 0;
        
        $chargeRecordLogModel->material_price = isset($priceRows[ChargeInfo::$materialType])?array_sum($priceRows[ChargeInfo::$materialType]) : '';
        $chargeRecordLogModel->material_discount_price = isset($discountPrice[ChargeInfo::$materialType])?array_sum($discountPrice[ChargeInfo::$materialType]) : 0;
        
        $chargeRecordLogModel->consumables_price = isset($priceRows[ChargeInfo::$consumablesType])?array_sum($priceRows[ChargeInfo::$consumablesType]) : '';
        $chargeRecordLogModel->consumables_discount_price = isset($discountPrice[ChargeInfo::$consumablesType])?array_sum($discountPrice[ChargeInfo::$consumablesType]) : 0;

        $chargeRecordLogModel->package_price = isset($priceRows[ChargeInfo::$packgeType])?array_sum($priceRows[ChargeInfo::$packgeType]) : '';
        $chargeRecordLogModel->package_discount_price = isset($discountPrice[ChargeInfo::$packgeType])?array_sum($discountPrice[ChargeInfo::$packgeType]) : 0;
        
        $chargeRecordLogModel->diagnosis_time = $patientRecordInfo['diagnosis_time'];
        $chargeRecordLogModel->fee_remarks = $feeRemarks;

        $result = $chargeRecordLogModel->save();
        if($result){
            foreach ($chargeInfoList as $value){
                $insertRows[] = [$chargeRecordLogModel->id,$value['spot_id'],$value['record_id'],$value['type'],$value['outpatient_id'],$value['name'],$value['unit'],$value['unit_price'],$value['discount_price'],$value['discount_reason'],$value['card_discount_price'],$value['num'],$value['is_charge_again'],time(),time()];
            }
            Yii::info('充值卡交易流水id'.$cardFlowId,'info');
            if($cardFlowId != 0){//若为充值卡支付，则更新其收费交易流水id
                $chargeType = PatientRecord::find()->select(['charge_type'])->where(['id' => $recordId,'spot_id' => $spotId])->asArray()->one();
                CardFlow::updateAll(['f_charge_record_log_id' => $chargeRecordLogModel->id,'f_flow_item' => ($chargeType['charge_type'] == 1?$doctorName.'医生门诊':'其他收费')],['f_physical_id' => $cardFlowId,'f_spot_id' => self::$staticSpotId]);
            }
            SendsmsController::trySendChargeSMS($recordId,$spotId);

            Yii::$app->db->createCommand()->batchInsert(ChargeInfoLog::tableName(),['charge_record_log_id','spot_id','record_id','type','outpatient_id','name','unit','unit_price','discount_price','discount_reason','card_discount_price','num','is_charge_again','create_time','update_time'], $insertRows)->execute();
            return $chargeRecordLogModel->id;
        }
        
    }

}
