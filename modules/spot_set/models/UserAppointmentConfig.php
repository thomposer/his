<?php

namespace app\modules\spot_set\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%user_appointment_config}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $user_id
 * @property integer $spot_type_id
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $price 
 * @property integer $type 
 * @property SpotType $spotType
 */
class UserAppointmentConfig extends \app\common\base\BaseActiveRecord
{
    
    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user_appointment_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'user_id', 'spot_type_id'], 'required'],
            [['spot_id', 'user_id', 'create_time', 'update_time'], 'integer'],
            [['spot_type_id'], 'validateTypeId'],
            [['price'],'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '自增id',
            'spot_id' => '诊所id',
            'user_id' => '医生id',
            'spot_type_id' => '可提供服务',
            'price' => '诊金',
            'type' => '类型',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'simpleOutpatientPrice' => '诊金'
        ];
    }

    /**
     * 
     * @param int $id 医生id
     * @desc 返回该医生所关联的预约服务列表
     */
    public static function getUserSpotType($id) {
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['b.id', 'b.type', 'b.time']);
        $query->leftJoin(['b' => SpotType::tableName()], '{{a}}.spot_type_id = {{b}}.id');
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.user_id' => $id, 'b.status' => 1]);
        return $query->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpotType() {
        return $this->hasOne(SpotType::className(), ['id' => 'spot_type_id']);
    }

    /**
     * 获取当前诊所  所有医生的预约服务列表
     */
    public static function getDoctorServiceType($doctorList = null) {
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['b.id', 'b.time', 'a.user_id','appointmentTypeName'=>'replace(group_concat(distinct b.type),",","，")','typeNameList'=>'group_concat(b.type)','typeIdList'=>'group_concat(b.id)']);
        $query->leftJoin(['b' => SpotType::tableName()], '{{a}}.spot_type_id = {{b}}.id');
        $query->where(['a.spot_id' => self::$staticSpotId, 'b.status' => 1]);
        if($doctorList){
            $query->andWhere(['a.user_id' => $doctorList]);
        }
        $all = $query->groupBy('user_id')->indexBy('user_id')->all();
        return $all;
    }
    /**
     * @param integer | array $doctorId 医生id | 医生id列表
     * @return 返回医生对应的最小预约服务时间
     */
    public static function getDoctorMinSpotType($doctorId){
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['a.user_id','id' => 'a.spot_type_id','b.time']);
        $query->leftJoin(['b' => SpotType::tableName()],'{{a}}.spot_type_id = {{b}}.id');
        $query->where(['a.user_id' => $doctorId,'a.spot_id' => self::$staticSpotId, 'b.status' => 1]);
        $result = $query->orderBy(['b.time' => SORT_ASC])->all();
        $leastTime = [];
        if(!empty($result)){
            foreach ($result as $value){
                if(!isset($leastTime[$value['user_id']])){
                    $leastTime[$value['user_id']]['time'] = $value['time'];
                    $leastTime[$value['user_id']]['spotTypeId'] = $value['id'];
                    $leastTime[$value['user_id']]['doctorId'] = $value['user_id'];
                }
            }
        }
        return $leastTime;
    }

    /**
     * @param int $spotId 诊所id
     * @param int $doctorId 医生id
     * @return Array result
     * @desc 获取医生服务类型
     */
    public static function getDoctorServeType($doctorId,$spotId){
        $queryDoctorServeType = new Query();
        $queryDoctorServeType->from(['a' => UserAppointmentConfig::tableName()]);
        $queryDoctorServeType->select(['b.id']);
        $queryDoctorServeType->leftJoin(['b' => SpotType::tableName()], '{{a}}.spot_type_id = {{b}}.id');
        $queryDoctorServeType->where(['b.status' => 1, 'a.spot_id' => $spotId,'a.user_id' => $doctorId]);
        $data = $queryDoctorServeType->all();
        $result = [];
        foreach ($data as $v){
            $result[] = $v['id'];
        }
        return $result;
    }
    
    
    /*
     * 验证服务类型及其诊金 
     */
    public function validateTypeId($attribute){
        if($this->spot_type_id){
            foreach ($this->spot_type_id as $typeId) {
                if(!$this->validatePrice($this->price[$typeId],$attribute)){
                    return ;
                }
            }
        }else{
            $this->addError($attribute, '可提供服务不能为空');
            return ;
        }
    }
    
    
    /*
     * 判断诊金金额数据
     */
    private function validatePrice($price,$attribute) {
        if($price == ''){
            $this->addError($attribute, '诊金不能为空');
            return false;
        }
        if(!is_numeric($price)){
            $this->addError($attribute, '诊金金额填写错误');
            return false;
        }
        if($price < 0 || $price > 100000){
            $this->addError($attribute, '诊金金额填写错误');
            return false;
        }
        return true;
    }
    
    
    static public function getTypePriceList($where) {
        return self::find()->select(['spot_type_id', 'price'])->where($where)->asArray()->all();
    }

    /**
     * @param $doctorId 医生id
     * @param $type 服务类型
     * @return array|null|\yii\db\ActiveRecord 返回医生加关联服务配置的诊金
     */
    public static function getMedicalFee($doctorId,$type){
        $result = self::find()->select(['price'])->where(['user_id' => $doctorId,'spot_type_id' => $type,'spot_id' => self::$staticSpotId])->asArray()->one();
        return $result;
    }

}
