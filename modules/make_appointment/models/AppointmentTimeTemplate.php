<?php

namespace app\modules\make_appointment\models;

use Yii;

/**
 * This is the model class for table "{{%appointment_time_template}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property string $name
 * @property integer $create_time
 * @property integer $update_time
 * @property string $appointment_times
 */
class AppointmentTimeTemplate extends \app\common\base\BaseActiveRecord
{


    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%appointment_time_template}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'name'], 'required'],
            [['spot_id', 'create_time', 'update_time'], 'integer'],
            [['name'], 'string', 'max' => 15],
            [['name'], 'verificationName'],
            [['appointment_times'], 'verificationTimes'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '序号',
            'spot_id' => '诊所id',
            'name' => '模板名称',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'appointment_times' => '预约时间',
        ];
    }

    public function verificationTimes($attribute){

        if(!empty($this->appointment_times)){

            $data = explode(',',$this->appointment_times);
            if(is_array($data)){
                foreach ($data as $k => $v) {
                    $time = explode('-', $v);
                    $startTime = $time[0];
                    $endTime = $time[1];
    
                    if (empty($startTime)) {
                        $this->addError('appointment_times', '预约开始时间不能为空');
                        return;
                    }
    
                    if (empty($endTime)) {
                        $this->addError('appointment_times', '预约结束时间不能为空');
                        return;
                    }
    
                    $tempDate = '2017-12-12';
                    $startDate = strtotime($tempDate . ' ' . $startTime);
                    $endDate = strtotime($tempDate . ' ' . $endTime);
                    if(!$startDate || !$endDate){
                        $this->addError('appointment_times','参数错误');
                    }
                    if ($startDate >= $endDate) {
                        $this->addError('appointment_times', '预约结束时间必须大于开始时间');
                        return;
                    }
    
                    foreach ($data as $k1 => $v1) {
                        if ($k1 != $k) {
                            $time1 = explode('-', $v1);
                            $startTime1 = $time1[0];
                            $endTime1 = $time1[1];
                            $startDate1 = strtotime($tempDate . ' ' . $startTime1);
                            $endDate1 = strtotime($tempDate . ' ' . $endTime1);
                            if (($startDate1 <= $startDate && $startDate < $endDate1) || ($startDate1 < $endDate && $endDate <= $endDate1)) {
                                $this->addError('appointment_times', '预约时间段不允许重叠');
                                return;
                            }
                        }
                    }
                }
            }else{
                $this->addError('appointment_times','参数错误');
            }
        }else{
            $this->addError('appointment_times', '请选择预约时间段');
        }
    }

    public function verificationName($attribute){
        if ($this->isNewRecord){
            $hasRecord = self::getList(['id'],['name' => trim($this->name)]);
            if($hasRecord){
                $this->addError('item_name','该模板名称已存在');
            }
        }else{
            $oldName = $this->getOldAttribute('name');
            if ($oldName != $this->name) {
                $hasRecord = self::getList(['id'],['name' => trim($this->name)]);
                if ($hasRecord) {
                    $this->addError('price', '该模板名称已存在');
                }
            }
            
        }
    }
    /**
     * 
     * @param string | array  $fields 查询字段
     * @param string | array  $where 查询条件
     * @return \yii\db\ActiveRecord|NULL 
     * @desc 返回当前诊所的时间模版配置信息
     */
    public static function getList($fields = '*',$where = null){
        return AppointmentTimeTemplate::find()->select($fields)->where(['spot_id' => self::$staticSpotId])->andFilterWhere($where)->asArray()->limit(1)->one();
    }

}
