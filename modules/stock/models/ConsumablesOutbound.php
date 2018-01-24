<?php

namespace app\modules\stock\models;

use Yii;

/**
 * This is the model class for table "{{%outbound}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $outbound_time
 * @property integer $outbound_type
 * @property integer $leading_department_id
 * @property integer $leading_user_id
 * @property integer $user_id
 * @property integer $apply_user_id
 * @property string $remark
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property OutboundInfo[] $outboundInfos
 */
class ConsumablesOutbound extends \app\common\base\BaseActiveRecord
{
    public $begin_time;//出库开始时间
    public $end_time;//出库结束时间
    public $department_name;//领用科室
    public $username;
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
        return '{{%consumables_outbound}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'outbound_time', 'outbound_type', 'user_id'], 'required'],
            [['spot_id','status', 'outbound_type', 'leading_department_id', 'leading_user_id', 'user_id', 'apply_user_id','create_time', 'update_time'], 'integer'],
            [['remark'], 'string', 'max' => 255],
            [['outbound_time'],'date'],
            [['leading_department_id','leading_user_id','apply_user_id'],'default','value' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '出库单号',
            'spot_id' => '诊所id',
            'outbound_time' => '出库日期',
            'outbound_type' => '出库方式',
            'leading_department_id' => '领用科室',
            'leading_user_id' => '领用人',
            'user_id' => '创建人',
            'apply_user_id' => '审核人',
            'remark' => '备注',
            'status' => '状态',
            'username' => '出库人',
            'department_name' => '领用科室',
            'begin_time' => '出库时间(开始)',
            'end_time' => '出库时间(结束)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    public function scenarios(){
        $parent = parent::scenarios();
        $parent['outboundApply'] = ['status'];
        return $parent;
    }

    public static $getOutboundType = [
        1 => '科室出库',
        2 => '报损出库',
        3 => '退货出库',
        5 => '过期出库',
        4 => '其他出库',
    ];
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutboundInfos()
    {
        return $this->hasMany(ConsumablesOutboundInfo::className(), ['stock_outbound_id' => 'id']);
    }
    public function beforeSave($insert) {
        if($this->scenario != 'outboundApply'){
            $this->outbound_time = strtotime($this->outbound_time);
        }
        return parent::beforeSave($insert);
    }
}
