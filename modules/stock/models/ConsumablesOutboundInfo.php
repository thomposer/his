<?php

namespace app\modules\stock\models;

use Yii;
use app\modules\stock\models\ConsumablesOutbound;

/**
 * This is the model class for table "{{%outbound_info}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $consumables_outbound_id
 * @property integer $consumables_stock_info_id
 * @property integer $num
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property Outbound $outbound
 * @property StockInfo $stockInfo
 * @property string $name 名称
 * @property string $unit 单位
 * @property string $specification 规格
 * @property string $manufactor 生产厂商
 * @property decimal $price 零售价
 * @property decimal $default_price 成本价
 * @property string $batch_number 批号
 * @property date $expire_time 有效期
 * @property integer $inbound_num 库存数量
 */
class ConsumablesOutboundInfo extends \app\common\base\BaseActiveRecord
{

    public $name;
    public $specification;
    public $manufactor;
    public $unit;
    public $price;
    public $default_price;
    public $expire_time;
    public $consumablesName;
    public $deleted; //删除数组
    public $outboundInfoId; //id组合
    public $consumables_id; //非药品id
    public $product_number;
    public $inbound_num;
    public $total_num;
    public $department_name;
    public $outbound_time;
    public $userName;
    public $leadingUser;
    public $status;

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%consumables_outbound_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id'], 'required'],
            [['spot_id', 'create_time', 'update_time'], 'integer'],
            [['consumables_outbound_id'], 'exist', 'skipOnError' => true, 'targetClass' => ConsumablesOutbound::className(), 'targetAttribute' => ['consumables_outbound_id' => 'id']],
            [['num', 'consumables_stock_info_id'], 'validateNum'],
            [['consumablesName', 'deleted', 'consumables_id', 'outboundInfoId'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '自增id',
            'spot_id' => '诊所id',
            'consumables_outbound_id' => '出库单号',
            'consumables_stock_info_id' => '物资库存信息详情表id',
            'num' => '出库数量',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'name' => '名称',
            'specification' => '规格',
            'unit' => '包装单位',
            'manufactor' => '生产厂商',
            'inbound_num' => '库存数量',
            'price' => '零售价',
            'default_price' => '成本价',
            'expire_time' => '有效期',
            'product_number' => '商品编号',
            'total_num' => '数量',
            'outbound_time' => '出库日期',
            'department_name' => '领用科室',
            'leadingUser' => '领用人',
            'userName' => '出库人',
            'status' => '状态',
        ];
    }

    public function validateNum($attribute, $params) {
        if (!$this->hasErrors()) {
            $sum = 0;
            if (count($this->num) > 0) {
                foreach ($this->deleted as $key => $v) {
                    if ($v == 0) {
                        $sum++;
                        if ($this->num[$key] <= 0) {
                            $this->addError($attribute, '出库数量必须大于0');
                        } else if (!preg_match("/^\s*[+-]?\d+\s*$/", $this->num[$key])) {
                            $this->addError($attribute, '出库数量必须是一个整数');
                        }
                        $num = ConsumablesStockInfo::find()->select(['num'])->where(['id' => $this->consumables_stock_info_id[$key], 'spot_id' => $this->spotId])->asArray()->one()['num'];
                        if ($this->num[$key] > $num) {
                            $this->addError($attribute, '出库数量不能大于库存数量');
                        }
                    }
                }
                if ($sum == 0) {
                    $this->addError($attribute, '请选择出库医疗耗材');
                }
            } else {
                $this->addError($attribute, '请选择出库医疗耗材');
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutbound() {
        return $this->hasOne(ConsumablesOutbound::className(), ['id' => 'consumables_outbound_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockInfo() {
        return $this->hasOne(ConsumablesStockInfo::className(), ['id' => 'consumables_stock_info_id']);
    }

}
