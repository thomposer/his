<?php

namespace app\modules\spot_set\models;

use Yii;
use yii\db\Query;
use app\modules\spot\models\MedicalFee;

/**
 * This is the model class for table "{{%medical_fee_clinic}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $fee_id
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 *
 * @property MedicalFee $fee
 */
class MedicalFeeClinic extends \app\common\base\BaseActiveRecord
{
    public $price;
    public $remarks;
    public $note;
    
    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%medical_fee_clinic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'fee_id', 'status', 'create_time', 'update_time'], 'required'],
            [['spot_id', 'fee_id', 'status', 'create_time', 'update_time'], 'integer'],
//            [['fee_id'], 'exist', 'skipOnError' => true, 'targetClass' => MedicalFee::className(), 'targetAttribute' => ['fee_id' => 'id']],
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
            'fee_id' => '机构下诊金配置ID',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'price' => '诊疗金额',
            'remarks' => '诊金说明',
            'note' => '诊金备注',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFee()
    {
        return $this->hasOne(MedicalFee::className(), ['id' => 'fee_id']);
    }
    
    //获取诊所下诊金信息列表
    public static  function getFeeInfoList($fields = ['fee_id'],$where = '1 != 0'){
        return self::find()->select($fields)->where($where)->andWhere(['spot_id' => self::$staticSpotId])->asArray()->all();
    }
    /**
     * @desc 返回当前诊所下所有诊金配置的字段信息
     * @param string $fields 查询字段
     * @param array $where 查询条件
     */
    public static function getList($fields = '*',$where = ['a.status' => 1]){
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select($fields);
        $query->leftJoin(['b' => MedicalFee::tableName()],'{{a}}.fee_id = {{b}}.id');
        $query->where(['a.spot_id' => self::$staticSpotId]);
        $query->andFilterWhere($where);
        return $query->all();
    }
    
}
