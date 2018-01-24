<?php

namespace app\modules\make_appointment\models;

use Yii;
use app\modules\make_appointment\models\AppointmentTimeAndServer;
use app\modules\spot_set\models\SpotType;

/**
 * This is the model class for table "{{%appointment_config}}".
 *
 * @property string $id
 * @property integer $spot_id
 * @property integer $user_id
 * @property string $department_id
 * @property string $begin_time
 * @property string $end_time
 * @property string $doctor_count
 * @property string $create_time
 * @property string $update_time
 */
class AppointmentConfig extends \app\common\base\BaseActiveRecord
{
    public function init(){
        
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%appointment_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'begin_time', 'end_time'], 'required'],
            [['department_id','spot_id', 'begin_time', 'end_time', 'doctor_count', 'create_time', 'update_time'], 'integer'],
            ['end_time','compare', 'operator'=>'>', 'compareAttribute'=>'begin_time']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'department_id' => '科室ID',
            'begin_time' => '开始时间',
            'end_time' => '结束时间',
            'doctor_count' => '医生数量',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
    /**
     * 
     * @return 获取所有医生的可预约时间
     */
    public static function getTimeList($start_date, $end_date) {
        $query = (new \yii\db\Query());
        $query->from(['d' => AppointmentConfig::tableName()]);
        $query->leftJoin(['ts' => AppointmentTimeAndServer::tableName()], '{{ts}}.time_config_id={{d}}.id');
        $query->leftJoin(['st' => SpotType::tableName()], '{{ts}}.spot_type_id={{st}}.id');
        $query->select(['doctor_id'=>'d.user_id', 'd.begin_time', 'd.end_time', 'schedule_id'=>'d.id', 'typeNameList' => 'group_concat(st.type)','typeIdList' => 'group_concat(st.id)','spotTypeList' => 'group_concat(ts.spot_type_id)']);
        $query->andWhere('d.begin_time >= :begin_time', [':begin_time' => strtotime($start_date)]);
        $query->andWhere('d.end_time <=:end_time', [':end_time' => strtotime($end_date) + 86400]);
        $query->andWhere(['d.spot_id' => self::$staticSpotId]);
        $query->andWhere(['<>','d.user_id',0]);
        $query->groupBy('d.id');
        $list = $query->all();
        $data = [];
        if (!empty($list)) {
            foreach ($list as $val) {
                $time = date('Y-m-d',$val['begin_time']);
                $data[$time][$val['doctor_id']][] = [
                    'shift_name'=>date('H:i',$val['begin_time']).'-'.date('H:i',$val['end_time']),
                    'typeNameList' => $val['typeNameList'] ? explode(',', $val['typeNameList']) : array(),
                    'typeIdList' => $val['typeIdList'] ? explode(',',$val['typeIdList']) : array(),
                    'spotTypeList' => $val['spotTypeList'] ? explode(',',$val['spotTypeList']) : array(),
                    'schedule_time'=> $time,
                    'schedule_id'=>$val['schedule_id'],
                ];
            }
        }
        return $data;
    }
    
}
