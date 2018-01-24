<?php

namespace app\modules\charge\models;

use Yii;
use yii\base\Object;
use yii\db\Query;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\outpatient\models\InspectRecord;
use app\modules\outpatient\models\CheckRecord;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\MaterialRecord;
use app\modules\stock\models\MaterialStockInfo;
use app\modules\stock\models\MaterialStock;
use app\modules\spot_set\models\Material;
use app\specialModules\recharge\models\CardFlow;
use app\modules\outpatient\models\ConsumablesRecord;
use app\modules\stock\models\ConsumablesStock;
use app\modules\stock\models\ConsumablesStockInfo;

/**
 * This is the model class for table "{{%charge_info}}".
 *
 * @property string $id
 * @property string $charge_record_id
 * @property integer $spot_id
 * @property integer $record_id
 * @property integer $type
 * @property string $outpatient_id
 * @property string $name
 * @property string $unit
 * @property string $unit_price
 * @property string $num
 * @property string $doctor_id
 * @property decimal $discount 优惠折扣
 * @property decimal $discount_price 优惠金额
 * @property decimal $discount_end_price 折后金额
 * @property string $discount_reason 优惠原因
 * @property decimal $card_discount_price 会员卡优惠金额
 * @property integer $status
 * @property string $reason
 * @property string $create_time
 * @property string $update_time
 * @property string $pay_type
 * @property string $totalPks
 * @property ChargeRecord $chargeRecord
 * @property string $remark 备注
 * @property integer $origin 来源(1-医生门诊，2-收费)
 */
class ChargeInfo extends \app\common\base\BaseActiveRecord
{

    public static $inspectType = 1; //实验室检查
    public static $checkType = 2; // 影像学检查
    public static $cureType = 3;  // 治疗
    public static $recipeType = 4; // 处方
    public static $priceType = 5; // 诊疗费
    public static $reportType = 6; //报告
    public static $materialType = 7; //物资管理
    public static $consumablesType = 8; //医疗耗材
    public static $packgeType = 9; //医嘱套餐
    public $total_price; //总额
    public $allPrice; //原价总额
    public $username; //患者名称
    public $fee_remarks; //诊金备注
    public $pks; //主键数组
    public $totalPks; //收费详情所有主键数组
    public $pay_type; //支付方式
    public $discount_type; //优惠方式
//     public $discount_price;//优惠金额
//     public $discount_reason;//优惠原因
    public $income; //实收费用
    public $change; //找零

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%charge_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['charge_record_id', 'type', 'outpatient_id', 'spot_id', 'record_id'], 'required'],
            [['charge_record_id', 'spot_id', 'record_id', 'type', 'outpatient_id', 'num', 'doctor_id', 'status', 'create_time', 'update_time', 'discount_type', 'reason','origin'], 'integer'],
            [['unit_price', 'discount', 'discount_price', 'card_discount_price'], 'number'],
            [['name'], 'string', 'max' => 64],
            [['unit'], 'string', 'max' => 16],
            ['reason_description', 'string', 'max' => 50],
            [['discount_reason'], 'string', 'max' => 10],
            [['remark'], 'string', 'max' => 255],
            [['reason_description', 'discount_reason', 'remark'], 'default', 'value' => ''],
            [['reason', 'total_price'], 'required', 'on' => 'update'],
            [['totalPks', 'pks'], 'validatePks', 'on' => 'update'],
            [['charge_record_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChargeRecord::className(), 'targetAttribute' => ['charge_record_id' => 'id']],
            [['fee_remarks', 'discount_reason'], 'safe'],
            ['reason', 'default', 'value' => 0],
            [['origin'],'default','value' => 1]
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['update'] = ['pks', 'totalPks', 'total_price', 'reason', 'reason_description', 'allPrice'];
        $parent['outpatientEnd'] = ['unit_price'];
        return $parent;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'charge_record_id' => '收费记录ID',
            'spot_id' => '诊所ID',
            'record_id' => '流水ID',
            'type' => '收费项类型(1-实验室检查,2-影像学检查,3-治疗,4-处方)',
            'outpatient_id' => '门诊ID',
            'name' => '收费项(名称)',
            'unit' => '单位',
            'unit_price' => '单价(元)',
            'total_price' => '金额(元)',
            'num' => '数量',
            'doctor_id' => '接诊医生',
            'discount' => '折扣（%）',
            'discount_price' => '单项优惠金额(元)',
            'card_discount_price' => '会员优惠金额(元)',
            'discount_end_price' => '折后金额(元)',
            'discount_reason' => '优惠原因',
            'status' => '状态(0-未收费,1-已收费,2-已退费)',
            'refund_total_price' => '退费金额',
            'reason' => '退费原因',
            'reason_description' => '退费说明',
            'remark' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    public static $getRefundChargeReason = [
        1 => '医生误开',
        2 => '系统错误',
        3 => '患者/家属要求',
        4 => '护士无法执行',
        5 => '检验无法执行',
        6 => '影像无法执行',
        7 => '药房无法执行',
        8 => '医生无法执行',
        9 => '其他',
    ];

    public function validatePks($attribute, $params) {
        if (!$this->hasErrors()) {
            if (!$this->pks) {
                $this->addError('total_price', '收费项目不能为空');
            }
            $pks = explode(',', $this->pks);
            $rows = self::getChargeListInfo($pks, $this->record_id);
            $chargeRecordIdList = array_unique(array_column($rows, 'charge_record_id'));
            $flowListCount = CardFlow::getCardChargeRecord($chargeRecordIdList);
            if (empty($rows)) {
                $this->addError('total_price', '该收费项目不存在');
            } else {
                foreach ($rows as $v) {
                    $price = $v['unit_price'] * intval($v['num']) - $v['discount_price'] - $v['card_discount_price'];
                    $income = $v['income'];
                    $type = $v['type'];
                    if($price == 0 && $v['type'] != 8){
                        $this->addError('total_price', '0元项不能退费');
                    }
                    if( ($v['type'] == 5 && !array_key_exists($v['charge_record_id'], $flowListCount)) || $v['type'] == 7){
                        $this->addError('total_price', '服务卡收费项不可退费，如需操作请至【会员卡】手动登记退还');
                    }
                   
                }
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChargeRecord() {
        return $this->hasOne(ChargeRecord::className(), ['id' => 'charge_record_id']);
    }

    /*
     * 获取是否 已经收费
     */

    public static function getChargeRecordNum($record_id) {
        return ChargeInfo::find()->where(['record_id' => $record_id])->count();
    }

    /**
     *
     * @param 收费项类型(1-实验室检查,2-影像学检查,3-治疗,4-处方) $type
     * @param 就诊流水ID $record_id
     * @property 获取新增未收费记录列表
     */
    public static function getChargeInfo($type, $record_id) {
        $chargeList = [];
        $chargeList = ChargeInfo::find()->select(['outpatient_id'])->where(['record_id' => $record_id, 'type' => $type])->indexBy('outpatient_id')->asArray()->all();
        $outpatientList = [];
        $query = new Query();
        $andWhere = [];
        if ($type == ChargeInfo::$inspectType) {
            $query->from(InspectRecord::tableName());
            $query->select(['id', 'name', 'unit', 'price']);
        } else if ($type == ChargeInfo::$checkType) {
            $query->from(CheckRecord::tableName());
            $query->select(['id', 'name', 'unit', 'price']);
        } else if ($type == ChargeInfo::$cureType) {
            $query->from(CureRecord::tableName());
            $query->select(['id', 'name', 'unit', 'price', 'time']);
        } else if ($type == ChargeInfo::$recipeType) {
            $query->from(RecipeRecord::tableName());
            $query->select(['id', 'name', 'unit', 'price', 'num']);
        }
        $outpatientList = $query->where(['record_id' => $record_id, 'spot_id' => self::$staticSpotId])->andWhere($andWhere)->indexBy('id')->all();
        return array_diff_key($outpatientList, $chargeList);
    }

    /**
     * @desc 判断待收费记录id列表是否满足要求，即是收费的数量不能大于对应总库存量
     * @param array $pks 待收费详情记录id
     */
    public static function checkMaterialINum($pks,$spotId = null) {//支付回调时，传入诊所id
        if(!$spotId){
            $spotId = self::$staticSpotId;
        }
        $returnInfo['errorCode'] = 0;
        $returnInfo['msg'] = '';
        $stockRows = [];
        $chargeRows = [];
        $query = new Query();
        $query->from(['a' => ChargeInfo::tableName()]);
        $query->select(['a.id', 'a.num', 'b.material_id', 'stockNum' => 'sum(c.num)']);
        $query->leftJoin(['b' => MaterialRecord::tableName()], '{{a.outpatient_id}} = {{b}}.id');
        $query->leftJoin(['c' => MaterialStockInfo::tableName()], '{{c}}.material_id = {{b}}.material_id');
        $query->leftJoin(['d' => MaterialStock::tableName()], '{{c}}.material_stock_id = {{d}}.id');
        $query->where(['a.id' => $pks, 'a.spot_id' => $spotId, 'a.type' => ChargeInfo::$materialType, 'b.attribute' => 2, 'd.status' => 1,'a.origin' => 1]);
        $query->andWhere('c.expire_time >= :expire_time', [':expire_time' => strtotime(date('Y-m-d'))]);
        $query->groupBy('id');
        $query->indexBy('id');
        $result = $query->all();
        if (!empty($result)) {
            foreach ($result as $v) {
                if (!isset($stockRows[$v['material_id']])) {
                    $stockRows[$v['material_id']] = $v['stockNum'];
                    $returnInfo['stockNum'][$v['material_id']] = $stockRows[$v['material_id']];
                }
                
                if (!isset($chargeRows[$v['material_id']])) {
                    $chargeRows[$v['material_id']] = 0;
                }
                
                $chargeRows[$v['material_id']] += $v['num'];
                $returnInfo['num'][$v['material_id']] = $chargeRows[$v['material_id']];
            }
            foreach ($chargeRows as $key => $v) {
                if ($v > $stockRows[$key]) {
                    $returnInfo['errorCode'] = 1002;
                    $returnInfo['msg'] = '收费失败，库存不足';
                    return $returnInfo;
                }
            }
        }
        $arrayDiffs = array_diff($pks, array_keys($result));
        if(!empty($arrayDiffs)){
            $checkResult =  self::checkMaterialINumSecond($arrayDiffs,$spotId);
            if ($checkResult['errorCode'] == 0 && is_array($checkResult['num'])) {
                !isset($returnInfo['num']) && $returnInfo['num'] = [];
                foreach ($checkResult['num'] as $key => $value) {
                    if (isset($returnInfo['num'][$key])) {
                        $returnInfo['num'][$key] += $checkResult['num'][$key];
                    }else{
                        $returnInfo['num'][$key] = $checkResult['num'][$key];
                    }
                }
                return $returnInfo;
            }else if($checkResult['errorCode'] == 0){
                return $returnInfo;
            }
                return $checkResult;
            
        }else{
            return $returnInfo;
        }
    }

     /**
     * @desc 判断待收费记录id列表是否满足要求，即是收费的数量不能大于对应总库存量
     * @param array $pks 待收费详情记录id
     */
    public static function checkConsumableslINum($pks, $spotId = null) {//回调没有诊所id，需要传入
        if(!$spotId){
            $spotId = self::$staticSpotId;
        }
        $returnInfo['errorCode'] = 0;
        $returnInfo['msg'] = '';
        $stockRows = [];
        $chargeRows = [];
        $query = new Query();
        $query->from(['a' => ChargeInfo::tableName()]);
        $query->select(['a.id', 'a.num', 'b.consumables_id', 'stockNum' => 'sum(c.num)']);
        $query->leftJoin(['b' => ConsumablesRecord::tableName()], '{{a.outpatient_id}} = {{b}}.id');
        $query->leftJoin(['c' => ConsumablesStockInfo::tableName()], '{{c}}.consumables_id = {{b}}.consumables_id');
        $query->leftJoin(['d' => ConsumablesStock::tableName()], '{{c}}.consumables_stock_id = {{d}}.id');
        $query->where(['a.id' => $pks, 'a.spot_id' => $spotId, 'a.type' => ChargeInfo::$consumablesType, 'd.status' => 1,'a.origin' => 1]);
        $query->andWhere('c.expire_time >= :expire_time', [':expire_time' => strtotime(date('Y-m-d'))]);
        $query->groupBy('id');
        $result = $query->all();
        if (!empty($result)) {
            foreach ($result as $v) {
                if (!isset($stockRows[$v['consumables_id']])) {
                    $stockRows[$v['consumables_id']] = $v['stockNum'];
                    $returnInfo['stockNum'][$v['consumables_id']] = $stockRows[$v['consumables_id']];
                }
                if (!isset($chargeRows[$v['consumables_id']])) {
                    $chargeRows[$v['consumables_id']] = 0;
                }
                
                $chargeRows[$v['consumables_id']] += $v['num'];
                $returnInfo['num'][$v['consumables_id']] = $chargeRows[$v['consumables_id']];
            }
            foreach ($chargeRows as $key => $v) {
                if ($v > $stockRows[$key]) {
                    $returnInfo['errorCode'] = 1002;
                    $returnInfo['msg'] = '收费失败，库存不足';
                    return $returnInfo;
                }
            }
        }
        return $returnInfo;
    }

    /**
     * @desc 判断非正常就诊，也就是直接新增收费渠道---待收费记录id列表是否满足要求，即是收费的数量不能大于对应总库存量
     * @param array $pks 待收费详情记录id
     */
    public static function checkMaterialINumSecond($pks,$spotId = null) {//支付回调时，传入诊所id
        if(!$spotId){
            $spotId = self::$staticSpotId;
        }
        $returnInfo['errorCode'] = 0;
        $returnInfo['msg'] = '';
        $stockRows = [];
        $chargeRows = [];
        $query = new Query();
        $query->from(['a' => ChargeInfo::tableName()]);
        $query->select(['a.id', 'a.num', 'material_id' => 'a.outpatient_id', 'stockNum' => 'sum(c.num)']);
//         $query->leftJoin(['e' => Material::tableName()],'{{e}}.id = {{a}}.outpatient_id');
        $query->leftJoin(['c' => MaterialStockInfo::tableName()], '{{c}}.material_id = {{a}}.outpatient_id');
        $query->leftJoin(['e' => Material::tableName()],'{{c}}.material_id = {{e}}.id');
        $query->leftJoin(['d' => MaterialStock::tableName()], '{{c}}.material_stock_id = {{d}}.id');
        $query->where(['a.id' => $pks, 'a.spot_id' => $spotId, 'a.type' => ChargeInfo::$materialType, 'd.status' => 1]);
        $query->andWhere('c.expire_time >= :expire_time', [':expire_time' => strtotime(date('Y-m-d'))]);
        $query->andWhere(['e.attribute' => 2]);
        $query->groupBy('id');
        $query->indexBy('id');
        $result = $query->all();

        if (!empty($result)) {
            foreach ($result as $v) {
                if (!isset($stockRows[$v['material_id']])) {
                    $stockRows[$v['material_id']] = $v['stockNum'];
                    $returnInfo['stockNum'][$v['material_id']] = $stockRows[$v['material_id']];
                }
                
                if (!isset($chargeRows[$v['material_id']])) {
                    $chargeRows[$v['material_id']] = 0;
                }
                
                $chargeRows[$v['material_id']] += $v['num'];
                $returnInfo['num'][$v['material_id']] = $chargeRows[$v['material_id']];
            }
            foreach ($chargeRows as $key => $v) {
                if ($v > $stockRows[$key]) {
                    $returnInfo['errorCode'] = 1002;
                    $returnInfo['msg'] = '收费失败，库存不足';
                    return $returnInfo;
                }
            }
        }
        return $returnInfo;
    }

    public static function recipeStateData($chargeAll) {
        $repId = [];
        $stateData = [];
        if (!empty($chargeAll)) {
            foreach ($chargeAll as $val) {
                if ($val['type'] == self::$recipeType) {//处方类型
                    $repId[] = $val['outpatient_id'];
                }
            }
        }
        \Yii::info('recipeStateData repId ['.var_export($repId,true).']');
        if (!empty($repId)) {
            $stateData = RecipeRecord::find()->select(['status', 'id'])->where(['id' => $repId])->indexBy('id')->asArray()->all();
        }
        return $stateData;
    }
    
    public static function getChargeListInfo($pks,$id){
        $query = new Query();
        $query->from(['a' => ChargeRecord::tableName()]);
        $query->leftJoin(['b' => ChargeInfo::tableName()], '{{b}}.charge_record_id = {{a}}.id');
        $query->select(['b.charge_record_id','a.type', 'a.income', 'b.unit_price', 'b.num', 'b.discount_price', 'b.card_discount_price']);
        $query->where(['b.id' =>$pks , 'b.record_id' => $id, 'b.spot_id' => self::$staticSpotId]);
        return $query->all();
    }

    /**
     * @param $chargeAll
     * @return array|\yii\db\ActiveRecord[]
     * @desc 返回实验室检查状态
     */
    public static function inspectStateData($chargeAll){
        $inspectId = [];
        $stateData = [];
        if (!empty($chargeAll)) {
            foreach ($chargeAll as $val) {
                if ($val['type'] == self::$inspectType) {//实验室检查类型
                    $inspectId[] = $val['outpatient_id'];
                }
            }
        }
        \Yii::info('inspectStateData repId ['.var_export($inspectId,true).']');
        if (!empty($inspectId)) {
            $stateData = InspectRecord::find()->select(['status', 'id'])->where(['id' => $inspectId])->indexBy('id')->asArray()->all();
        }
        return $stateData;
    }
    /**
     * @desc 返回就诊记录对应的收费详情信息
     * @param integer $id 就诊记录ID
     * @param string $fields 查询字段
     * @param array $where 查询条件
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getList($id,$fields = '*',$where = []){
        return ChargeInfo::find()->select($fields)->where(['record_id' => $id, 'spot_id' => self::$staticSpotId])->andFilterWhere($where)->asArray()->all();
    }
    
}
