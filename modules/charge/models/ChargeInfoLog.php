<?php

namespace app\modules\charge\models;

use app\modules\outpatient\models\PackageRecord;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;


/**
 * This is the model class for table "{{%charge_info_log}}".
 *
 * @property string $id
 * @property string $charge_record_log_id
 * @property string $spot_id
 * @property string $record_id
 * @property string $name
 * @property string $unit
 * @property string $unit_price
 * @property string $discount_price
 * @property string $discount_reason
 * @property string $card_discount_price
 * @property string $num
 * @property string $fee_remarks
 * @property integer $is_charge_again
 * @property string $create_time
 * @property string $update_time
 *
 * @property ChargeRecordLog $chargeRecordLog
 */
class ChargeInfoLog extends \app\common\base\BaseActiveRecord
{
    public $type; //收费类型
    public $outpatientType;//医嘱类型
    public $packageRecordId;//医嘱套餐流水id
    public function init()
    {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%charge_info_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['charge_record_log_id', 'spot_id', 'record_id', 'num','is_charge_again', 'create_time', 'update_time','type'], 'integer'],
            [['unit_price', 'discount_price', 'card_discount_price'], 'number'],
            [['name', 'unit'], 'string', 'max' => 255],
            [['discount_reason'], 'string', 'max' => 64],
            [['charge_record_log_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChargeRecordLog::className(), 'targetAttribute' => ['charge_record_log_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'charge_record_log_id' => '交易流水ID',
            'spot_id' => '诊所ID',
            'record_id' => '就诊流水ID',
            'name' => '收费项 名称',
            'unit' => '单位',
            'unit_price' => '单价',
            'discount_price' => '单项优惠金额',
            'discount_reason' => '优惠原因',
            'card_discount_price' => '会员卡优惠金额',
            'num' => '数量',
            'is_charge_again' => '重新收费',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChargeRecordLog()
    {
        return $this->hasOne(ChargeRecordLog::className(), ['id' => 'charge_record_log_id']);
    }


    /**
     *
     * @param 交易流水ID
     * @return 返回当前就诊状态的列表
     */
    public static function chargeInfoLog($logId,$where = '1 != 0') {
        $chargeQuery = new ActiveQuery(self::className());
        $chargeQuery->from(['a' => self::tableName()]);
        $chargeQuery->select(['a.charge_record_log_id', 'a.id','outpatientType'=>'a.type', 'a.record_id', 'a.name','a.is_charge_again', 'a.unit', 'a.unit_price', 'a.num','a.discount_reason','a.discount_price','a.card_discount_price','a.card_discount_price','b.type','packageRecordId' => 'c.id']);
        $chargeQuery->leftJoin(['b' => ChargeRecordLog::tableName()], '{{a}}.charge_record_log_id = {{b}}.id');
        $chargeQuery->leftJoin(['c' => PackageRecord::tableName()], '{{a}}.record_id = {{c}}.record_id');
        $chargeQuery->where(['a.spot_id' => self::$staticSpotId, 'a.charge_record_log_id' => $logId]);
        $chargeQuery->andWhere($where);
        $dataProvider = new ActiveDataProvider([
            'query' => $chargeQuery,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
    }

}
