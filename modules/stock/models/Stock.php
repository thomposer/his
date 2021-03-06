<?php

namespace app\modules\stock\models;

use Yii;

/**
 * This is the model class for table "{{%stock}}".
 * @property model 药房库存公共信息表
 * @property integer $id
 * @property integer $spot_id
 * @property integer $inbound_time
 * @property integer $inbound_type
 * @property integer $supplier_id
 * @property integer $user_id
 * @property integer $status
 * @property string $remark
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property StockInfo[] $stockInfos
 */
class Stock extends \app\common\base\BaseActiveRecord
{
    public $username;
    public $name;
    public $begin_time;
    public $end_time;
    public function init(){
        parent::init();
        $this->spot_id = $this->spotId;
        $this->user_id = Yii::$app->user->identity->id;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%stock}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'inbound_time', 'inbound_type', 'user_id'], 'required'],
            [['spot_id', 'inbound_type', 'supplier_id', 'user_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['supplier_id'], 'default','value' => '0'],
            ['inbound_time','date'],
            [['remark'], 'string', 'max' => 255],
        ];
    }
    public function scenarios(){
        $parent = parent::scenarios();
        $parent['inboundApply'] = ['status'];
        return $parent;
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
            'user_id' => '用户id',
            'status' => '状态',
            'remark' => '备注',
            'username' => '制单人',
            'name' => '供应商',
            'begin_time' => '入库时间(开始)',
            'end_time' => '入库时间(结束)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    public static $getStatus = [
        1 => '已审核',
        2 => '待审核'
    ];
    public static $getInboundType = [
        1 => '采购入库',
        2 => '其他入库'
    ];
    public function beforeSave($insert){
        if($this->scenario != 'inboundApply'){
            $this->inbound_time = strtotime($this->inbound_time);
        }
        return parent::beforeSave($insert);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockInfos()
    {
        return $this->hasMany(StockInfo::className(), ['stock_id' => 'id']);
    }
}
