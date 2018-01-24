<?php

namespace app\modules\stock\models;

use Yii;

/**
 * This is the model class for table "{{%stock_stock}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $inbound_time
 * @property integer $inbound_type
 * @property integer $supplier_id
 * @property integer $user_id
 * @property integer $apply_user_id
 * @property integer $status
 * @property string $remark
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property ConsumablesStockInfo[] $stockStockInfos
 */
class ConsumablesStock extends \app\common\base\BaseActiveRecord
{
    public $username;
    public $supplierName;
    public $begin_time;
    public $end_time;
    
    public function init(){
        parent::init();
        $this->spot_id = $this->spotId;
        $this->user_id = $this->userInfo->id;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%consumables_stock}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'inbound_time', 'inbound_type', 'user_id'], 'required'],
            [['spot_id', 'inbound_type', 'supplier_id', 'user_id','apply_user_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['supplier_id','apply_user_id'], 'default','value' => '0'],
            ['inbound_time','date'],
            [['remark'], 'string', 'max' => 255],
            [['remark'],'default','value' => '']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '入库单号',
            'spot_id' => '诊所ID',
            'inbound_time' => '入库日期',
            'inbound_type' => '入库方式',
            'supplier_id' => '供应商',
            'user_id' => '制单人',
            'apply_user_id' => '审核人',
            'status' => '状态',
            'remark' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'username' => '制单人',
            'supplierName' => '供应商',
            'begin_time' => '入库开始时间',
            'end_time' => '入库结束时间',
        ];
    }
    
    public function scenarios(){
        $parent = parent::scenarios();
        $parent['inboundApply'] = ['status','apply_user_id'];
        return $parent;
    }
    
    /**
     * 
     * @var array 状态
     */
    public static $getStatus = [
        1 => '审核通过',
        2 => '待审核',
        3 => '审核不通过'
    ]; 
    /**
     * 
     * @var array 入库方式
     */
    public static $getInboundType = [
        1 => '采购入库',
        2 => '其他入库'
    ];
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsumablesStockInfos()
    {
        return $this->hasMany(ConsumablesStockInfo::className(), ['stock_stock_id' => 'id']);
    }
    
    public function beforeSave($insert){
        if($this->scenario != 'inboundApply'){
            $this->inbound_time = strtotime($this->inbound_time);
        }
        return parent::beforeSave($insert);
    }
}
