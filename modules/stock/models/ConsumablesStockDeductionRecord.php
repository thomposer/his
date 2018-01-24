<?php

namespace app\modules\stock\models;

use Yii;
use yii\db\Query;
use app\modules\outpatient\models\ConsumablesRecord;
use app\modules\stock\models\ConsumablesStock;
use app\modules\stock\models\ConsumablesStockInfo;
use app\modules\spot_set\models\ConsumablesClinic;
use app\modules\charge\models\ChargeInfo;
use yii\db\Expression;
use yii\db\Exception;

/**
 * This is the model class for table "{{%consumables_stock_deduction_record}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $record_id
 * @property string $outpatient_id
 * @property string $consumables_id
 * @property string $stock_info_id
 * @property string $num
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class ConsumablesStockDeductionRecord extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%consumables_stock_deduction_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'record_id', 'outpatient_id', 'consumables_id', 'stock_info_id'], 'required'],
            [['spot_id', 'record_id', 'outpatient_id', 'consumables_id', 'stock_info_id', 'num', 'status', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'record_id' => '就诊流水id',
            'outpatient_id' => '医疗耗材门诊id',
            'consumables_id' => '医疗耗材管理表id(机构)',
            'stock_info_id' => '医疗耗材库存管理表id',
            'num' => '扣减数量',
            'status' => '状态(1-正常,2-无效)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
    
    /*
     * 扣减库存
     * @param $recordId 流水id
     */
    public static function updateStockInfo($recordId) {
        $data = self::getOutpatientData($recordId);
        $outpatientData = $data['outpatientData'];
        $chargeInfo = $data['chargeInfo'];
        
        $result['errorCode'] = 0;
        $db = Yii::$app->db;
        $dbTrans = $db->beginTransaction();
        try {
            $ret = self::addTotal($recordId, $chargeInfo);
            if(!$ret){
                $result['errorCode'] = 1002;
                $result['message'] = '保存失败';
                Yii::info('库存回退失败', 'consumables-stock-deduction-record-update-stock-info');
                $dbTrans->rollBack();
                return $result;
            }
            
            if(empty($outpatientData)){//没有记录
                $dbTrans->commit();
                return $result;
            }
            
            $consumableslId = array_unique(array_column($outpatientData, 'consumables_id'));
            $consumablesStockInfo = self::getStockInfo($consumableslId);

            foreach ($outpatientData as $value) {
                $removeNum = $value['num'];
                if(!isset($consumablesStockInfo[$value['consumables_id']])){
                    $result['errorCode'] = 1002;
                    $result['message'] = '库存不足';
                    Yii::info('库存不足', 'consumables-stock-deduction-record-update-stock-info');
                    $dbTrans->rollBack();
                    return $result;
                }
                foreach ($consumablesStockInfo[$value['consumables_id']] as &$v) {
                    if ($removeNum == 0) {
                        continue;
                    } else if ($v['num'] > $removeNum) {
                        $deductionNum = $removeNum;
                    } else {
                        $deductionNum = $v['num'];
                    }

                    $removeNum -= $deductionNum;
                    $v['num'] -= $deductionNum;

                    $updateRow[$v['id']][] = $deductionNum; //数量汇总 更新stcok_info表
                    $recordRow[] = [//插入deduction_record表
                        self::$staticSpotId,
                        $recordId,
                        $value['outpatient_id'],
                        $value['consumables_id'],
                        $v['id'],
                        $deductionNum,
                        time(),
                        time(),
                    ];
                }
                if ($removeNum != 0) {
                    $result['errorCode'] = 1002;
                    $result['message'] = '库存不足';
                    Yii::info('库存不足', 'consumables-stock-deduction-record-update-stock-info');
                    $dbTrans->rollBack();
                    return $result;
                }
            }
            
            $ret = self::deductTotal($updateRow, $recordRow);
            if(!$ret){
                $result['errorCode'] = 1002;
                $result['message'] = '保存失败';
                Yii::info('库存扣减失败', 'consumables-stock-deduction-record-update-stock-info');
                $dbTrans->rollBack();
                return $result;
            }
            $dbTrans->commit();
            return $result;
        } catch (Exception $e) {
            $result['errorCode'] = 1002;
            $result['message'] = '保存失败';
            Yii::info($e->getMessage(), 'consumables-stock-deduction-record-update-stock-info');
            $dbTrans->rollBack();
            return $result;
        }
    }
    
    /*
     * 获取医疗耗材门诊记录信息
     */
    private static function getOutpatientData($recordId){
        $query = new Query();
        $query->from(['a' => ConsumablesRecord::tableName()]);
        $query->select(['outpatient_id' => 'a.id', 'a.consumables_id', 'a.num']);
        $query->where(['a.record_id' => $recordId, 'a.spot_id' => self::$staticSpotId]);
        $query->indexBy('outpatient_id');
        $outpatientData = $query->all();
        
        $chargeInfo = ChargeInfo::find()->select(['outpatient_id'])->where(['record_id' => $recordId, 'spot_id' => self::$staticSpotId, 'status' => [1, 2], 'type' => ChargeInfo::$consumablesType])->asArray()->all();
        foreach ($chargeInfo as $value) {
            unset($outpatientData[$value['outpatient_id']]);//过滤已收费
        }
        return ['chargeInfo' => $chargeInfo, 'outpatientData' => $outpatientData];
    }
    
    /*
     * 获取医疗耗材库存信息
     */
    private static function getStockInfo($consumableslId) {
        $query = new Query();
        $query->from(['a' => ConsumablesStock::tableName()]);
        $query->select(['b.id', 'b.consumables_id', 'b.num']);
        $query->leftJoin(['b' => ConsumablesStockInfo::tableName()], '{{a}}.id = {{b}}.consumables_stock_id');
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.status' => 1]);
        $query->andWhere('b.expire_time >= :expire_time', [':expire_time' => strtotime(date('Y-m-d'))]);
        $query->andFilterWhere(['b.consumables_id' => $consumableslId]);
        $query->orderBy(['b.expire_time' => SORT_ASC]);
        $data = $query->all();

        $consumablesStockInfo = [];
        foreach ($data as $value) {
            $consumablesStockInfo[$value['consumables_id']][] = $value;
        }
        return $consumablesStockInfo;
    }
    
    /*
     * 将扣减的库存返回
     */
    private static function addTotal($recordId, $chargeInfo) {
        $outpatientIdList = array_column($chargeInfo, 'outpatient_id');
        
        $db = Yii::$app->db;
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['num' => 'SUM(a.num)', 'a.stock_info_id']);
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.status' => 1, 'a.record_id' => $recordId]);
        $query->andWhere(['NOT IN', 'a.outpatient_id', $outpatientIdList]);//过滤已收费
        $query->groupBy('a.stock_info_id');
        $result = $query->all();
        
        //更新库存信息
        foreach ($result as $value) {
            $db->createCommand()->update(ConsumablesStockInfo::tableName(), 
                ['num' => new Expression('num + ' . $value['num']), 'update_time' => time()], ['id' => $value['stock_info_id'], 'spot_id' => self::$staticSpotId])->execute();
        }
        
        
        if($outpatientIdList){
            //将旧记录置为无效
            $db->createCommand()->update(
                            self::tableName(), ['status' => 2, 'update_time' => time()], 'outpatient_id NOT IN(:outpatient_id) AND record_id = :record_id AND spot_id = :spot_id AND status = :status', [':outpatient_id' => $outpatientIdList, ':record_id' => $recordId, ':spot_id' => self::$staticSpotId, ':status' => 1])
                    ->execute();
        }else{
            //将旧记录置为无效
            $db->createCommand()->update(
                            self::tableName(), ['status' => 2, 'update_time' => time()],['record_id' => $recordId, 'spot_id' => self::$staticSpotId, 'status' => 1])
                    ->execute();
        }
        return true;
    }
    
    
    /*
     * 扣减库存
     */
    private static function deductTotal($updateRow, $recordRow) {
        $db = Yii::$app->db;
        //更新记录
        foreach ($updateRow as $key => $value) {
            $deductionNum = array_sum($value);
            $db->createCommand()->update(ConsumablesStockInfo::tableName(), 
                ['num' => new Expression('num - ' . $deductionNum), 'update_time' => time()], ['id' => $key, 'spot_id' => self::$staticSpotId])->execute();
        }
        //插入扣减记录
        $db->createCommand()->batchInsert(self::tableName(), ['spot_id', 'record_id', 'outpatient_id', 'consumables_id', 'stock_info_id', 'num', 'create_time', 'update_time'], $recordRow)->execute();
        return true;
    }
    
     
    /*
     * 根据consumables_record的id获取已经扣减的库存
     */
    public static function getDeductTotal($recordId, $outpatientIdList) {
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['num' => 'SUM(a.num)', 'a.consumables_id']);
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.status' => 1, 'a.record_id' => $recordId]);
        $query->andWhere([ 'a.outpatient_id' => $outpatientIdList]);
        $query->groupBy('a.consumables_id');
        $data = $query->all();
        return array_column($data, 'num', 'consumables_id');
    }
}
