<?php

namespace app\specialModules\recharge\models;

use Yii;

/**
 * This is the model class for table "{{%membership_package_card_union}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $membership_package_card_id
 * @property integer $patient_id
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property MembershipPackageCard $membershipPackageCard
 */
class MembershipPackageCardUnion extends \app\common\base\BaseActiveRecord
{
    public $iphone;
    public $patientInfo;
    public $username;//患者名称
    public $sex;//患者性别
    public $birthday;//患者出生日期
    
    public function init(){
        
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%membership_package_card_union}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id','iphone'], 'required'],
            [['spot_id', 'membership_package_card_id', 'patient_id', 'create_time', 'update_time'], 'integer'],
            [['membership_package_card_id'], 'exist', 'skipOnError' => true, 'targetClass' => MembershipPackageCard::className(), 'targetAttribute' => ['membership_package_card_id' => 'id']],
            [['patient_id'],'validatePatient','skipOnEmpty' => false],
            [['patientInfo'],'safe'],
            
        ];
    }
    
    public function scenarios(){
        $parent = parent::scenarios();
        $parent['update'] = ['patient_id'];
        $parent['stepTwo'] = ['spot_id','membership_package_card_id','patient_id','create_time','update_time'];
        return $parent;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'membership_package_card_id' => '会员-套餐卡id',
            'patient_id' => '患者id',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'iphone' => '手机号',
            'patientInfo' => '宝宝信息'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembershipPackageCard()
    {
        return $this->hasOne(MembershipPackageCard::className(), ['id' => 'membership_package_card_id']);
    }
    
    
    public function validatePatient($attribute,$params){
        if(!$this->patient_id ){
            $this->addError('iphone','该患者不存在');
        }
    }
}
