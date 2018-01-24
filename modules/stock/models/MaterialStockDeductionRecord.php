<?php

namespace app\modules\stock\models;

use Yii;
use yii\db\Query;
use app\modules\spot_set\models\Material;
use app\modules\stock\models\MaterialStock;
use app\modules\stock\models\MaterialStockInfo;
use app\modules\outpatient\models\MaterialRecord;
use app\modules\charge\models\ChargeInfo;
use yii\db\Expression;
use yii\db\Exception;

/**
 * This is the model class for table "{{%material_stock_deduction_record}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $record_id
 * @property string $outpatient_id
 * @property string $material_id
 * @property string $stock_info_id
 * @property string $num
 * @property integer $status
 * @property integer $type
 * @property string $create_time
 * @property string $update_time
 */
class MaterialStockDeductionRecord extends \app\common\base\BaseActiveRecord
{
    public static $materialRecord = 1; //门诊
    public static $chargeInfo = 2; //新增收费
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%material_stock_deduction_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'record_id', 'outpatient_id', 'material_id', 'stock_info_id'], 'required'],
            [['spot_id', 'record_id', 'outpatient_id', 'material_id', 'stock_info_id', 'num', 'status', 'type', 'create_time', 'update_time'], 'integer'],
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
            'outpatient_id' => '其他类门诊id',
            'material_id' => '其他类管理表id',
            'stock_info_id' => '其他类库存管理表id',
            'num' => '扣减数量',
            'status' => '状态(1-正常,2-无效)',
            'type' => '类型(1-门诊，2-新增收费)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
    /*
     * 扣减库存
     * @param $recordId 流水id
     * @param $type 1-门诊 2-收费
     */
    public static function updateStockInfo($recordId,$type = 1) {
        if (1 == $type) {
            $data = self::getOutpatientData($recordId);
        } else {
            $data = self::getChargeData($recordId);
        }
        $outpatientData = $data['outpatientData'];
        $chargeInfo = $data['chargeInfo'];

        $result['errorCode'] = 0;
        $db = Yii::$app->db;
        $dbTrans = $db->beginTransaction();
        try {
            $ret = self::addTotal($recordId, $chargeInfo, $type);
            if(!$ret){
                $result['errorCode'] = 1002;
                $result['message'] = '保存失败';
                Yii::info('库存回退失败', 'material-stock-deduction-record-update-stock-info');
                $dbTrans->rollBack();
                return $result;
            }
            
            if(empty($outpatientData)){//没有记录
                $dbTrans->commit();
                return $result;
            }
            
            $materialId = array_unique(array_column($outpatientData, 'material_id'));
            $materialStockInfo = self::getStockInfo($materialId);

            foreach ($outpatientData as $value) {
                $removeNum = $value['num'];
                if(!isset($materialStockInfo[$value['material_id']])){
                    $result['errorCode'] = 1002;
                    $result['message'] = '库存不足';
                    Yii::info('库存不足', 'material-stock-deduction-record-update-stock-info');
                    $dbTrans->rollBack();
                    return $result;
                }
                foreach ($materialStockInfo[$value['material_id']] as &$v) {
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
                        $value['material_id'],
                        $v['id'],
                        $deductionNum,
                        $type,
                        time(),
                        time(),
                    ];
                }
                if ($removeNum != 0) {
                    $result['errorCode'] = 1002;
                    $result['message'] = '库存不足';
                    Yii::info('库存不足', 'material-stock-deduction-record-update-stock-info');
                    $dbTrans->rollBack();
                    return $result;
                }
            }
            
            $ret = self::deductTotal($updateRow, $recordRow);
            if(!$ret){
                $result['errorCode'] = 1002;
                $result['message'] = '保存失败';
                Yii::info('库存扣减失败', 'material-stock-deduction-record-update-stock-info');
                $dbTrans->rollBack();
                return $result;
            }
            $dbTrans->commit();
            return $result;
        } catch (Exception $e) {
            $result['errorCode'] = 1002;
            $result['message'] = '保存失败';
            Yii::info($e->getMessage(), 'material-stock-deduction-record-update-stock-info');
            $dbTrans->rollBack();
            return $result;
        }
    }
    
    
    
    /*
     * 获取其他类库存信息
     */
    private static function getStockInfo($materialId) {
        $query = new Query();
        $query->from(['a' => MaterialStock::tableName()]);
        $query->select(['b.id', 'b.material_id', 'b.num']);
        $query->leftJoin(['b' => MaterialStockInfo::tableName()], '{{a}}.id = {{b}}.material_stock_id');
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.status' => 1]);
        $query->andWhere('b.expire_time >= :expire_time', [':expire_time' => strtotime(date('Y-m-d'))]);
        $query->andFilterWhere(['b.material_id' => $materialId]);
        $query->orderBy(['b.expire_time' => SORT_ASC]);
        $data = $query->all();

        $materialStockInfo = [];
        foreach ($data as $value) {
            $materialStockInfo[$value['material_id']][] = $value;
        }
        return $materialStockInfo;
    }

    /*
     * 获取其他类门诊记录信息
     */
    private static function getOutpatientData($recordId){
        $query = new Query();
        $query->from(['a' => MaterialRecord::tableName()]);
        $query->select(['outpatient_id' => 'a.id', 'a.material_id', 'a.num']);
        $query->where(['a.record_id' => $recordId, 'a.spot_id' => self::$staticSpotId, 'attribute' => 2]);
        $query->indexBy('outpatient_id');
        $outpatientData = $query->all();
        
        $chargeInfo = ChargeInfo::find()->select(['outpatient_id'])->where(['record_id' => $recordId, 'spot_id' => self::$staticSpotId, 'status' => [1, 2], 'type' => ChargeInfo::$materialType, 'origin' => 1])->asArray()->all();
        foreach ($chargeInfo as $value) {
            unset($outpatientData[$value['outpatient_id']]);//过滤已收费
        }
        return ['chargeInfo' => $chargeInfo, 'outpatientData' => $outpatientData];
    }
    
    
    /*
     * 获取其他类收费记录信息
     */
    private static function getChargeData($recordId){
        $query = new Query();
        $query->from(['a' => ChargeInfo::tableName()]);
        $query->leftJoin(['b' => Material::tableName()], '{{a}}.outpatient_id = {{b}}.id');
        $query->select(['material_id' => 'a.outpatient_id', 'outpatient_id' => 'a.id', 'a.num', 'a.status']);
        $query->where(['a.record_id' => $recordId, 'a.spot_id' => self::$staticSpotId, 'a.type' => ChargeInfo::$materialType, 'a.origin' => 2, 'b.attribute' => 2]);
        $data = $query->all();
        
        $outpatientData = [];
        $chargeInfo = [];
        foreach ($data as $value) {
            if(0 == $value['status']){
                $outpatientData[] = $value;
            }else{
                $chargeInfo[] = $value;
            }
        }
        return ['chargeInfo' => $chargeInfo, 'outpatientData' => $outpatientData];
    }
    
    /*
     * 将扣减的库存返回
     */
    private static function addTotal($recordId, $chargeInfo, $type = 1) {
        $db = Yii::$app->db;
        $outpatientIdList = array_column($chargeInfo, 'outpatient_id');
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['num' => 'SUM(a.num)', 'a.stock_info_id']);
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.status' => 1, 'a.record_id' => $recordId, 'type' => $type]);
        $query->andWhere(['NOT IN', 'a.outpatient_id', $outpatientIdList]); //过滤已收费
        $query->groupBy('a.stock_info_id');
        $result = $query->all();

        //更新库存信息
        foreach ($result as $value) {
            $db->createCommand()->update(MaterialStockInfo::tableName(), ['num' => new Expression('num + ' . $value['num']), 'update_time' => time()], ['id' => $value['stock_info_id'], 'spot_id' => self::$staticSpotId])->execute();
        }
        if($outpatientIdList){
            //将旧记录置为无效
            $db->createCommand()->update(
                            self::tableName(), ['status' => 2, 'update_time' => time()], 'outpatient_id NOT IN(:outpatient_id) AND record_id = :record_id AND spot_id = :spot_id AND type = :type AND status = :status', [':outpatient_id' => $outpatientIdList, ':record_id' => $recordId, ':spot_id' => self::$staticSpotId, ':type' => $type, ':status' => 1])
                    ->execute();
        }else{
            //将旧记录置为无效
            $db->createCommand()->update(
                            self::tableName(), ['status' => 2, 'update_time' => time()],['record_id' => $recordId, 'spot_id' => self::$staticSpotId, 'type' => $type, 'status' => 1])
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
            $db->createCommand()->update(MaterialStockInfo::tableName(), 
                ['num' => new Expression('num - ' . $deductionNum), 'update_time' => time()], ['id' => $key, 'spot_id' => self::$staticSpotId])->execute();
        }
        //插入扣减记录
        $db->createCommand()->batchInsert(self::tableName(), ['spot_id', 'record_id', 'outpatient_id', 'material_id', 'stock_info_id', 'num', 'type', 'create_time', 'update_time'], $recordRow)->execute();
        return true;
    }
    
    
    /*
     * 根据material_record的id或者charge_info的id获取已经扣减的库存
     */
    public static function getDeductTotal($recordId, $outpatientIdList, $type = 1) {
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['num' => 'SUM(a.num)', 'a.material_id']);
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.status' => 1, 'a.record_id' => $recordId, 'type' => $type]);
        $query->andWhere([ 'a.outpatient_id' => $outpatientIdList]);
        $query->groupBy('a.material_id');
        $data = $query->all();
        return array_column($data, 'num', 'material_id');
    }
    

}
